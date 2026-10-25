<?php
/**
 * FORENSIC-X Incident Model
 */

class Incident {
    public static function getAll(array $filters = [], int $limit = 50, int $offset = 0): array {
        $db = Database::getConnection();
        $sql = "
            SELECT i.*, 
                   u.full_name AS assigned_name, 
                   u.username AS assigned_username,
                   c.username AS creator_username,
                   (SELECT COUNT(*) FROM evidence WHERE incident_id = i.id) AS evidence_count,
                   (SELECT COUNT(*) FROM iocs WHERE incident_id = i.id) AS ioc_count,
                   (SELECT COUNT(*) FROM analysis_findings WHERE incident_id = i.id) AS findings_count
            FROM incidents i
            LEFT JOIN users u ON i.assigned_to = u.id
            LEFT JOIN users c ON i.created_by = c.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND i.status = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['severity'])) {
            $sql .= " AND i.severity = :severity";
            $params[':severity'] = $filters['severity'];
        }
        if (!empty($filters['category'])) {
            $sql .= " AND i.category = :category";
            $params[':category'] = $filters['category'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (i.title LIKE :search OR i.incident_code LIKE :search OR i.description LIKE :search OR i.affected_system LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $sql .= " ORDER BY i.created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function getCount(array $filters = []): int {
        $db = Database::getConnection();
        $sql = "SELECT COUNT(*) FROM incidents i WHERE 1=1";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND i.status = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['severity'])) {
            $sql .= " AND i.severity = :severity";
            $params[':severity'] = $filters['severity'];
        }
        if (!empty($filters['category'])) {
            $sql .= " AND i.category = :category";
            $params[':category'] = $filters['category'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (i.title LIKE :search OR i.incident_code LIKE :search OR i.description LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public static function getById(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT i.*, 
                   u.full_name AS assigned_name, 
                   u.username AS assigned_username,
                   u.department AS assigned_department,
                   c.full_name AS creator_name,
                   c.username AS creator_username
            FROM incidents i
            LEFT JOIN users u ON i.assigned_to = u.id
            LEFT JOIN users c ON i.created_by = c.id
            WHERE i.id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function getByCode(string $code): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM incidents WHERE incident_code = :code LIMIT 1");
        $stmt->execute([':code' => $code]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): int {
        $db = Database::getConnection();
        
        // Generate next unique incident code safely
        $stmt = $db->query("SELECT MAX(id) FROM incidents");
        $nextId = ((int)$stmt->fetchColumn()) + 1;
        $incidentCode = sprintf("FX-INC-%06d", $nextId);

        $insertStmt = $db->prepare("
            INSERT INTO incidents (
                incident_code, title, description, category, severity, status, 
                assigned_to, detected_at, source, affected_system, tags, findings_notes, created_by, created_at
            ) VALUES (
                :code, :title, :description, :category, :severity, :status, 
                :assigned_to, :detected_at, :source, :affected_system, :tags, :findings_notes, :created_by, CURRENT_TIMESTAMP
            )
        ");

        $insertStmt->execute([
            ':code'            => $incidentCode,
            ':title'           => $data['title'],
            ':description'     => $data['description'],
            ':category'        => $data['category'],
            ':severity'        => $data['severity'] ?? 'Medium',
            ':status'          => $data['status'] ?? 'Open',
            ':assigned_to'     => !empty($data['assigned_to']) ? (int)$data['assigned_to'] : null,
            ':detected_at'     => $data['detected_at'] ?? date('Y-m-d H:i:s'),
            ':source'          => $data['source'],
            ':affected_system' => $data['affected_system'],
            ':tags'            => $data['tags'] ?? null,
            ':findings_notes'  => $data['findings_notes'] ?? null,
            ':created_by'      => Auth::id() ?: 1,
        ]);

        $incidentId = (int)$db->lastInsertId();

        // Log audit event
        Audit::log('CREATE_INCIDENT', 'incident', $incidentCode, 'SUCCESS', "Created incident: {$data['title']}");

        // Record initial timeline event
        Timeline::addEvent(
            $incidentId,
            'INCIDENT_LIFECYCLE',
            'Incident Created',
            "Incident {$incidentCode} reported. Initial severity: {$data['severity']}. Affected system: {$data['affected_system']}.",
            date('Y-m-d H:i:s'),
            0,
            'incidents',
            $incidentId
        );

        return $incidentId;
    }

    public static function update(int $id, array $data): bool {
        $db = Database::getConnection();
        $current = self::getById($id);
        if (!$current) {
            return false;
        }

        $stmt = $db->prepare("
            UPDATE incidents SET 
                title = :title,
                description = :description,
                category = :category,
                severity = :severity,
                status = :status,
                assigned_to = :assigned_to,
                source = :source,
                affected_system = :affected_system,
                tags = :tags,
                findings_notes = :findings_notes
            WHERE id = :id
        ");

        $result = $stmt->execute([
            ':title'           => $data['title'],
            ':description'     => $data['description'],
            ':category'        => $data['category'],
            ':severity'        => $data['severity'],
            ':status'          => $data['status'],
            ':assigned_to'     => !empty($data['assigned_to']) ? (int)$data['assigned_to'] : null,
            ':source'          => $data['source'],
            ':affected_system' => $data['affected_system'],
            ':tags'            => $data['tags'] ?? null,
            ':findings_notes'  => $data['findings_notes'] ?? null,
            ':id'              => $id
        ]);

        if ($result) {
            Audit::log('UPDATE_INCIDENT', 'incident', $current['incident_code'], 'SUCCESS', "Updated metadata for {$current['incident_code']}.");

            // If status changed, log timeline event
            if ($current['status'] !== $data['status']) {
                Timeline::addEvent(
                    $id,
                    'INCIDENT_LIFECYCLE',
                    'Status Changed',
                    "Status transitioned from '{$current['status']}' to '{$data['status']}'.",
                    date('Y-m-d H:i:s')
                );
            }
            // If severity changed, log timeline event
            if ($current['severity'] !== $data['severity']) {
                Timeline::addEvent(
                    $id,
                    'INCIDENT_LIFECYCLE',
                    'Severity Reclassified',
                    "Severity changed from '{$current['severity']}' to '{$data['severity']}'.",
                    date('Y-m-d H:i:s')
                );
            }
        }

        return $result;
    }

    public static function getStats(): array {
        $db = Database::getConnection();

        $total = (int)$db->query("SELECT COUNT(*) FROM incidents")->fetchColumn();
        $open = (int)$db->query("SELECT COUNT(*) FROM incidents WHERE status IN ('Open', 'Investigating')")->fetchColumn();
        $criticalHigh = (int)$db->query("SELECT COUNT(*) FROM incidents WHERE severity IN ('Critical', 'High')")->fetchColumn();
        $resolvedClosed = (int)$db->query("SELECT COUNT(*) FROM incidents WHERE status IN ('Resolved', 'Closed')")->fetchColumn();

        // Severity breakdown
        $sevStmt = $db->query("SELECT severity, COUNT(*) as count FROM incidents GROUP BY severity");
        $bySeverity = ['Critical' => 0, 'High' => 0, 'Medium' => 0, 'Low' => 0];
        while ($row = $sevStmt->fetch()) {
            $bySeverity[$row['severity']] = (int)$row['count'];
        }

        // Status breakdown
        $statusStmt = $db->query("SELECT status, COUNT(*) as count FROM incidents GROUP BY status");
        $byStatus = ['Open' => 0, 'Investigating' => 0, 'Contained' => 0, 'Resolved' => 0, 'Closed' => 0];
        while ($row = $statusStmt->fetch()) {
            $byStatus[$row['status']] = (int)$row['count'];
        }

        // Category breakdown
        $catStmt = $db->query("SELECT category, COUNT(*) as count FROM incidents GROUP BY category ORDER BY count DESC LIMIT 5");
        $byCategory = $catStmt->fetchAll();

        return [
            'total'          => $total,
            'open'           => $open,
            'critical_high'  => $criticalHigh,
            'resolved'       => $resolvedClosed,
            'by_severity'    => $bySeverity,
            'by_status'      => $byStatus,
            'by_category'    => $byCategory,
        ];
    }
}
