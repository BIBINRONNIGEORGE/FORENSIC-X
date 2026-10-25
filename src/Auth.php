<?php
/**
 * FORENSIC-X Role-Based Authentication & Session Guard
 * Implements strict least-privilege RBAC & brute force protection
 */

class Auth {
    public static function init(): void {
        if (session_status() === PHP_SESSION_NONE) {
            // Configure hardened session cookies
            ini_set('session.cookie_httponly', '1');
            ini_set('session.use_only_cookies', '1');
            ini_set('session.cookie_samesite', 'Lax');
            session_start();
        }

        // Check session inactivity timeout
        if (isset($_SESSION['user_id'])) {
            if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_LIFETIME)) {
                self::logout('Session expired due to inactivity.');
                header('Location: /login?timeout=1');
                exit;
            }
            $_SESSION['last_activity'] = time();
        }
    }

    public static function attempt(string $username, string $password): array {
        $db = Database::getConnection();
        
        $stmt = $db->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        if (!$user) {
            Audit::log('LOGIN_FAILED', 'user', $username, 'FAILURE', 'Non-existent username attempt.');
            return ['success' => false, 'error' => 'Invalid username or password.'];
        }

        // Check account status
        if (!$user['is_active']) {
            Audit::log('LOGIN_BLOCKED', 'user', (string)$user['id'], 'DENIED', 'Account is deactivated.');
            return ['success' => false, 'error' => 'This account has been deactivated by an administrator.'];
        }

        // Check lockout status
        if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
            $remaining = ceil((strtotime($user['locked_until']) - time()) / 60);
            Audit::log('LOGIN_LOCKED', 'user', (string)$user['id'], 'DENIED', "Account locked. {$remaining} mins remaining.");
            return [
                'success' => false, 
                'error' => "Account temporarily locked due to repeated failed attempts. Please retry in {$remaining} minute(s)."
            ];
        }

        // Verify password
        if (!password_verify($password, $user['password_hash'])) {
            $newAttempts = $user['failed_login_attempts'] + 1;
            $lockUntil = null;
            if ($newAttempts >= MAX_LOGIN_ATTEMPTS) {
                $lockUntil = date('Y-m-d H:i:s', time() + (LOCKOUT_TIME_MINUTES * 60));
            }

            $updateStmt = $db->prepare("
                UPDATE users 
                SET failed_login_attempts = :attempts, locked_until = :locked_until 
                WHERE id = :id
            ");
            $updateStmt->execute([
                ':attempts'     => $newAttempts,
                ':locked_until' => $lockUntil,
                ':id'           => $user['id']
            ]);

            Audit::log('LOGIN_FAILED', 'user', (string)$user['id'], 'FAILURE', "Incorrect password (Attempt {$newAttempts}/" . MAX_LOGIN_ATTEMPTS . ").");

            if ($lockUntil) {
                return ['success' => false, 'error' => 'Maximum login attempts reached. Account locked for 15 minutes.'];
            }
            return ['success' => false, 'error' => 'Invalid username or password.'];
        }

        // Successful login: regenerate session ID to defeat session fixation
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_data'] = [
            'id'         => $user['id'],
            'username'   => $user['username'],
            'email'      => $user['email'],
            'full_name'  => $user['full_name'],
            'role'       => $user['role'],
            'department' => $user['department'],
        ];
        $_SESSION['last_activity'] = time();

        // Reset failed login attempts and update last login
        $ip = Audit::getClientIp();
        $resetStmt = $db->prepare("
            UPDATE users 
            SET failed_login_attempts = 0, locked_until = NULL, last_login_at = CURRENT_TIMESTAMP, last_login_ip = :ip 
            WHERE id = :id
        ");
        $resetStmt->execute([':ip' => $ip, ':id' => $user['id']]);

        Audit::log('LOGIN_SUCCESS', 'user', (string)$user['id'], 'SUCCESS', 'Authenticated successfully.');

        return ['success' => true];
    }

    public static function check(): bool {
        return isset($_SESSION['user_id']);
    }

    public static function user(): ?array {
        return $_SESSION['user_data'] ?? null;
    }

    public static function id(): ?int {
        return $_SESSION['user_id'] ?? null;
    }

    public static function role(): ?string {
        return $_SESSION['user_data']['role'] ?? null;
    }

    public static function hasRole($roles): bool {
        if (!self::check()) {
            return false;
        }
        $userRole = self::role();
        if (is_array($roles)) {
            return in_array($userRole, $roles, true);
        }
        return $userRole === $roles;
    }

    public static function can(string $action): bool {
        if (!self::check()) {
            return false;
        }
        $role = self::role();

        if ($role === ROLE_ADMIN) {
            return true; // Admin has full access
        }

        switch ($action) {
            case 'manage_users':
            case 'manage_settings':
                return $role === ROLE_ADMIN;

            case 'create_incident':
            case 'edit_incident':
                return in_array($role, [ROLE_ADMIN, ROLE_INVESTIGATOR], true);

            case 'upload_evidence':
            case 'verify_evidence':
                return in_array($role, [ROLE_ADMIN, ROLE_INVESTIGATOR], true);

            case 'manage_iocs':
            case 'run_correlation':
            case 'run_log_analysis':
                return in_array($role, [ROLE_ADMIN, ROLE_INVESTIGATOR, ROLE_ANALYST], true);

            case 'create_finding':
                return in_array($role, [ROLE_ADMIN, ROLE_INVESTIGATOR, ROLE_ANALYST], true);

            case 'generate_report':
                return in_array($role, [ROLE_ADMIN, ROLE_INVESTIGATOR], true);

            case 'view_data':
            case 'view_reports':
            case 'view_evidence':
                return true; // All 4 roles can view authorized records

            default:
                return false;
        }
    }

    public static function requireLogin(): void {
        if (!self::check()) {
            header('Location: /login');
            exit;
        }
    }

    public static function requireRole($roles): void {
        self::requireLogin();
        if (!self::hasRole($roles)) {
            Audit::log('UNAUTHORIZED_ACCESS_ATTEMPT', 'route', $_SERVER['REQUEST_URI'] ?? '', 'DENIED', 'Insufficient role permissions.');
            http_response_code(403);
            require BASE_PATH . '/views/errors/403.php';
            exit;
        }
    }

    public static function requirePermission(string $action): void {
        self::requireLogin();
        if (!self::can($action)) {
            Audit::log('PERMISSION_DENIED', 'action', $action, 'DENIED', "User role '" . self::role() . "' denied permission for '{$action}'.");
            http_response_code(403);
            require BASE_PATH . '/views/errors/403.php';
            exit;
        }
    }

    public static function logout(?string $reason = null): void {
        if (self::check()) {
            Audit::log('LOGOUT', 'user', (string)self::id(), 'SUCCESS', $reason ?: 'User signed out.');
        }
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }
}
