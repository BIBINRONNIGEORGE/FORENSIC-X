<?php
/**
 * FORENSIC-X User Management & Role Model
 */

class User {
    public static function getAll(): array {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT id, username, email, full_name, role, department, is_active, 
                   last_login_at, last_login_ip, created_at
            FROM users
            ORDER BY id ASC
        ");
        return $stmt->fetchAll();
    }

    public static function getById(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function getInvestigators(): array {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT id, full_name, username, role, department 
            FROM users 
            WHERE is_active = 1 AND role IN ('admin', 'investigator', 'analyst')
            ORDER BY full_name ASC
        ");
        return $stmt->fetchAll();
    }

    public static function create(array $data): array {
        $db = Database::getConnection();

        // Check unique username and email
        $check = $db->prepare("SELECT id FROM users WHERE username = :u OR email = :e LIMIT 1");
        $check->execute([':u' => $data['username'], ':e' => $data['email']]);
        if ($check->fetch()) {
            return ['success' => false, 'error' => 'A user with that username or email address already exists.'];
        }

        if (strlen($data['password']) < 8) {
            return ['success' => false, 'error' => 'Password must be at least 8 characters long.'];
        }

        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);

        $stmt = $db->prepare("
            INSERT INTO users (username, email, password_hash, full_name, role, department, is_active, created_at)
            VALUES (:username, :email, :hash, :name, :role, :dept, 1, CURRENT_TIMESTAMP)
        ");

        $stmt->execute([
            ':username' => $data['username'],
            ':email'    => $data['email'],
            ':hash'     => $passwordHash,
            ':name'     => $data['full_name'],
            ':role'     => $data['role'],
            ':dept'     => $data['department'] ?? 'Digital Forensics Unit',
        ]);

        $userId = (int)$db->lastInsertId();

        Audit::log('CREATE_USER', 'user', (string)$userId, 'SUCCESS', "Created new {$data['role']} account: {$data['username']}");

        return ['success' => true, 'user_id' => $userId];
    }

    public static function changePassword(int $userId, string $oldPassword, string $newPassword): array {
        $db = Database::getConnection();
        $user = self::getById($userId);
        if (!$user) {
            return ['success' => false, 'error' => 'User not found.'];
        }

        if (!password_verify($oldPassword, $user['password_hash'])) {
            return ['success' => false, 'error' => 'Current password is incorrect.'];
        }

        if (strlen($newPassword) < 8) {
            return ['success' => false, 'error' => 'New password must be at least 8 characters.'];
        }

        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE users SET password_hash = :hash WHERE id = :id");
        $stmt->execute([':hash' => $newHash, ':id' => $userId]);

        Audit::log('CHANGE_PASSWORD', 'user', (string)$userId, 'SUCCESS', 'Password updated successfully.');

        return ['success' => true];
    }

    public static function toggleActive(int $userId, int $newStatus): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE users SET is_active = :status WHERE id = :id");
        $result = $stmt->execute([':status' => $newStatus, ':id' => $userId]);

        Audit::log('TOGGLE_USER_STATUS', 'user', (string)$userId, 'SUCCESS', "User active status set to {$newStatus}.");

        return $result;
    }
}
