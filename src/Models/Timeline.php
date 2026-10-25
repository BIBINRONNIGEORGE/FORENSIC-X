<?php
/**
 * FORENSIC-X Unified Investigation Timeline Model
 */

class Timeline {
    public static function addEvent(
        int $incidentId,
        string $eventCategory,
        string $title,
        string $description,
        string $eventTime,
        int $isSourceLog = 0,
        ?string $refTable = null,
        ?int $refId = null
    ): bool {
        try {
            $db = Database::getConnection();
            $actorId = Auth::id();

            $stmt = $db->prepare("
                INSERT INTO investigation_timeline_events (
                    incident_id, event_category, event_title, description, 
                    event_time, is_source_log_timestamp, actor_id, 
                    reference_table, reference_id, created_at
                ) VALUES (
                    :incident_id, :event_category, :title, :description,
                    :event_time, :is_source_log, :actor_id,
                    :ref_table, :ref_id, CURRENT_TIMESTAMP
                )
            ");

            return $stmt->execute([
                ':incident_id'     => $incidentId,
                ':event_category'  => $eventCategory,
                ':title'           => $title,
                ':description'     => $description,
                ':event_time'      => $eventTime,
                ':is_source_log'   => $isSourceLog,
                ':actor_id'        => $actorId,
                ':ref_table'       => $refTable,
                ':ref_id'          => $refId
            ]);
        } catch (Exception $e) {
            error_log("Failed to add timeline event: " . $e->getMessage());
            return false;
        }
    }

    public static function getIncidentTimeline(int $incidentId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT t.*, u.full_name AS actor_name, u.username AS actor_username
            FROM investigation_timeline_events t
            LEFT JOIN users u ON t.actor_id = u.id
            WHERE t.incident_id = :incident_id
            ORDER BY t.event_time ASC, t.id ASC
        ");
        $stmt->execute([':incident_id' => $incidentId]);
        return $stmt->fetchAll();
    }

    public static function getAll(array $filters = [], int $limit = 50, int $offset = 0): array {
        $db = Database::getConnection();
        $sql = "
            SELECT t.*, 
                   i.incident_code, 
                   i.title AS incident_title,
                   u.full_name AS actor_name, 
                   u.username AS actor_username
            FROM investigation_timeline_events t
            INNER JOIN incidents i ON t.incident_id = i.id
            LEFT JOIN users u ON t.actor_id = u.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['incident_id'])) {
            $sql .= " AND t.incident_id = :incident_id";
            $params[':incident_id'] = $filters['incident_id'];
        }
        if (!empty($filters['category'])) {
            $sql .= " AND t.event_category = :category";
            $params[':category'] = $filters['category'];
        }

        $sql .= " ORDER BY t.event_time DESC LIMIT :limit OFFSET :offset";

        $stmt = $db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
