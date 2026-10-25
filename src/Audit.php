<?php
/**
 * FORENSIC-X Centralized Security & Audit Logger
 * Append-only trail for SOC compliance & forensics
 */

class Audit {
    public static function log(
        string $action,
        ?string $targetType = null,
        ?string $targetId = null,
        string $outcome = 'SUCCESS',
        ?string $details = null
    ): void {
        try {
            $db = Database::getConnection();
            $user = Auth::user();
            
            $userId = $user['id'] ?? null;
            $username = $user['username'] ?? 'anonymous';
            $ipAddress = self::getClientIp();
            $userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 255);

            $stmt = $db->prepare("
                INSERT INTO audit_logs (user_id, username, action, target_type, target_id, ip_address, user_agent, outcome, details, created_at)
                VALUES (:user_id, :username, :action, :target_type, :target_id, :ip_address, :user_agent, :outcome, :details, CURRENT_TIMESTAMP)
            ");

            $stmt->execute([
                ':user_id'     => $userId,
                ':username'    => $username,
                ':action'      => $action,
                ':target_type' => $targetType,
                ':target_id'   => $targetId,
                ':ip_address'  => $ipAddress,
                ':user_agent'  => $userAgent,
                ':outcome'     => $outcome,
                ':details'     => $details
            ]);
        } catch (Exception $e) {
            // Write to system error log without interrupting application flow
            error_log("Failed to write audit log: " . $e->getMessage());
        }
    }

    public static function getClientIp(): string {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '127.0.0.1';
    }
}
