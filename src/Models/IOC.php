<?php
/**
 * FORENSIC-X Indicators of Compromise (IOC) Model
 */

class IOC {
    public static function validate(string $type, string $value): array {
        $value = trim($value);

        switch ($type) {
            case 'IPv4':
                if (!filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                    return ['valid' => false, 'error' => 'Invalid IPv4 address format (e.g. 198.51.100.45).'];
                }
                break;

            case 'IPv6':
                if (!filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                    return ['valid' => false, 'error' => 'Invalid IPv6 address format.'];
                }
                break;

            case 'Domain':
                // RFC compliant domain pattern
                if (!preg_match('/^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}$/', $value)) {
                    return ['valid' => false, 'error' => 'Invalid domain format (e.g. malicious-domain.com).'];
                }
                break;

            case 'URL':
                if (!filter_var($value, FILTER_VALIDATE_URL)) {
                    return ['valid' => false, 'error' => 'Invalid URL format (e.g. https://domain.com/path).'];
                }
                break;

            case 'FileHash-SHA256':
                if (!preg_match('/^[a-fA-F0-9]{64}$/', $value)) {
                    return ['valid' => false, 'error' => 'Invalid SHA-256 hash (must be 64 hexadecimal characters).'];
                }
                $value = strtolower($value);
                break;

            case 'FileHash-MD5':
                if (!preg_match('/^[a-fA-F0-9]{32}$/', $value)) {
                    return ['valid' => false, 'error' => 'Invalid MD5 hash (must be 32 hexadecimal characters).'];
                }
                $value = strtolower($value);
                break;

            default:
                return ['valid' => false, 'error' => 'Unsupported indicator type.'];
        }

        return ['valid' => true, 'normalized' => $value];
    }

    public static function getAll(array $filters = [], int $limit = 50, int $offset = 0): array {
        $db = Database::getConnection();
        $sql = "
            SELECT i.*, 
                   inc.incident_code, 
                   inc.title AS incident_title,
                   u.username AS creator_username,
                   (SELECT COUNT(*) FROM correlation_matches WHERE ioc_id = i.id) AS match_count
            FROM iocs i
            LEFT JOIN incidents inc ON i.incident_id = inc.id
            LEFT JOIN users u ON i.created_by = u.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['type'])) {
            $sql .= " AND i.indicator_type = :type";
            $params[':type'] = $filters['type'];
        }
        if (!empty($filters['confidence'])) {
            $sql .= " AND i.confidence = :confidence";
            $params[':confidence'] = $filters['confidence'];
        }
        if (!empty($filters['incident_id'])) {
            $sql .= " AND i.incident_id = :incident_id";
            $params[':incident_id'] = $filters['incident_id'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (i.value LIKE :search OR i.ioc_code LIKE :search OR i.description LIKE :search)";
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
        $sql = "SELECT COUNT(*) FROM iocs WHERE 1=1";
        $params = [];

        if (!empty($filters['type'])) {
            $sql .= " AND indicator_type = :type";
            $params[':type'] = $filters['type'];
        }
        if (!empty($filters['confidence'])) {
            $sql .= " AND confidence = :confidence";
            $params[':confidence'] = $filters['confidence'];
        }
        if (!empty($filters['incident_id'])) {
            $sql .= " AND incident_id = :incident_id";
            $params[':incident_id'] = $filters['incident_id'];
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public static function getById(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT i.*, 
                   inc.incident_code, 
                   inc.title AS incident_title,
                   u.username AS creator_username
            FROM iocs i
            LEFT JOIN incidents inc ON i.incident_id = inc.id
            LEFT JOIN users u ON i.created_by = u.id
            WHERE i.id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): array {
        $validation = self::validate($data['indicator_type'], $data['value']);
        if (!$validation['valid']) {
            return ['success' => false, 'error' => $validation['error']];
        }
        $normalizedVal = $validation['normalized'];

        $db = Database::getConnection();

        // Check if IOC value already exists for this incident
        $dupCheck = $db->prepare("SELECT id FROM iocs WHERE indicator_type = :type AND value = :val");
        $dupCheck->execute([':type' => $data['indicator_type'], ':val' => $normalizedVal]);
        if ($dupCheck->fetch()) {
            return ['success' => false, 'error' => 'This indicator already exists in the IOC repository.'];
        }

        // Generate next code FX-IOC-000001
        $stmt = $db->query("SELECT MAX(id) FROM iocs");
        $nextId = ((int)$stmt->fetchColumn()) + 1;
        $iocCode = sprintf("FX-IOC-%06d", $nextId);

        $insertStmt = $db->prepare("
            INSERT INTO iocs (
                ioc_code, indicator_type, value, description, confidence, 
                threat_source, first_seen, last_seen, incident_id, created_by, created_at
            ) VALUES (
                :code, :type, :value, :desc, :confidence, 
                :source, :first_seen, :last_seen, :incident_id, :created_by, NOW()
            )
        ");

        $insertStmt->execute([
            ':code'        => $iocCode,
            ':type'        => $data['indicator_type'],
            ':value'       => $normalizedVal,
            ':desc'        => $data['description'] ?? null,
            ':confidence'  => $data['confidence'] ?? 'Medium',
            ':source'      => $data['threat_source'] ?? null,
            ':first_seen'  => !empty($data['first_seen']) ? $data['first_seen'] : null,
            ':last_seen'   => !empty($data['last_seen']) ? $data['last_seen'] : null,
            ':incident_id' => !empty($data['incident_id']) ? (int)$data['incident_id'] : null,
            ':created_by'  => Auth::id() ?: 1,
        ]);

        $iocId = (int)$db->lastInsertId();

        Audit::log('CREATE_IOC', 'ioc', $iocCode, 'SUCCESS', "Created {$data['indicator_type']} IOC: {$normalizedVal}");

        if (!empty($data['incident_id'])) {
            Timeline::addEvent(
                (int)$data['incident_id'],
                'IOC_CORRELATION',
                "IOC Registered: {$normalizedVal}",
                "Identified {$data['indicator_type']} indicator ({$normalizedVal}) with {$data['confidence']} confidence.",
                date('Y-m-d H:i:s'),
                0,
                'iocs',
                $iocId
            );
        }

        return ['success' => true, 'ioc_id' => $iocId, 'code' => $iocCode];
    }

    public static function getStats(): array {
        $db = Database::getConnection();
        $total = (int)$db->query("SELECT COUNT(*) FROM iocs")->fetchColumn();
        $matches = (int)$db->query("SELECT COUNT(*) FROM correlation_matches")->fetchColumn();

        $byTypeStmt = $db->query("SELECT indicator_type, COUNT(*) as count FROM iocs GROUP BY indicator_type");
        $byType = [];
        while ($row = $byTypeStmt->fetch()) {
            $byType[$row['indicator_type']] = (int)$row['count'];
        }

        return [
            'total'   => $total,
            'matches' => $matches,
            'by_type' => $byType,
        ];
    }
}
