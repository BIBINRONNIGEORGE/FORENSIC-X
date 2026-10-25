<?php
/**
 * FORENSIC-X Append-Only Chain of Custody Record
 */

class ChainOfCustody {
    public static function logEvent(
        int $evidenceId,
        string $eventType,
        string $notes,
        ?string $prevState = null,
        ?string $newState = null
    ): bool {
        try {
            $db = Database::getConnection();
            $user = Auth::user();

            $actorId = $user['id'] ?? null;
            $actorName = $user['full_name'] ?? ($user['username'] ?? 'System Process');
            $ip = Audit::getClientIp();

            $stmt = $db->prepare("
                INSERT INTO evidence_custody_events (
                    evidence_id, event_type, actor_id, actor_name, 
                    previous_state, new_state, notes, ip_address, created_at
                ) VALUES (
                    :evidence_id, :event_type, :actor_id, :actor_name, 
                    :previous_state, :new_state, :notes, :ip_address, CURRENT_TIMESTAMP
                )
            ");

            return $stmt->execute([
                ':evidence_id'     => $evidenceId,
                ':event_type'      => $eventType,
                ':actor_id'        => $actorId,
                ':actor_name'      => $actorName,
                ':previous_state'  => $prevState,
                ':new_state'       => $newState,
                ':notes'           => $notes,
                ':ip_address'      => $ip
            ]);
        } catch (Exception $e) {
            error_log("Failed to log chain of custody event: " . $e->getMessage());
            return false;
        }
    }

    public static function getByEvidenceId(int $evidenceId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT c.*, u.username, u.role
            FROM evidence_custody_events c
            LEFT JOIN users u ON c.actor_id = u.id
            WHERE c.evidence_id = :evidence_id
            ORDER BY c.created_at ASC
        ");
        $stmt->execute([':evidence_id' => $evidenceId]);
        return $stmt->fetchAll();
    }
}
