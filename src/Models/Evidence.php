<?php
/**
 * FORENSIC-X Digital Evidence Vault Model
 */

class Evidence {
    public static function getAll(array $filters = [], int $limit = 50, int $offset = 0): array {
        $db = Database::getConnection();
        $sql = "
            SELECT e.*, 
                   i.incident_code, 
                   i.title AS incident_title,
                   u.full_name AS uploader_name,
                   u.username AS uploader_username
            FROM evidence e
            INNER JOIN incidents i ON e.incident_id = i.id
            LEFT JOIN users u ON e.uploaded_by = u.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['incident_id'])) {
            $sql .= " AND e.incident_id = :incident_id";
            $params[':incident_id'] = $filters['incident_id'];
        }
        if (!empty($filters['status'])) {
            $sql .= " AND e.status = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['verified_status'])) {
            $sql .= " AND e.last_verified_status = :verified_status";
            $params[':verified_status'] = $filters['verified_status'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (e.name LIKE :search OR e.evidence_code LIKE :search OR e.original_filename LIKE :search OR e.sha256_hash LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $sql .= " ORDER BY e.created_at DESC LIMIT :limit OFFSET :offset";

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
        $sql = "SELECT COUNT(*) FROM evidence e WHERE 1=1";
        $params = [];

        if (!empty($filters['incident_id'])) {
            $sql .= " AND e.incident_id = :incident_id";
            $params[':incident_id'] = $filters['incident_id'];
        }
        if (!empty($filters['status'])) {
            $sql .= " AND e.status = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['verified_status'])) {
            $sql .= " AND e.last_verified_status = :verified_status";
            $params[':verified_status'] = $filters['verified_status'];
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public static function getById(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT e.*, 
                   i.incident_code, 
                   i.title AS incident_title,
                   i.severity AS incident_severity,
                   u.full_name AS uploader_name,
                   u.username AS uploader_username,
                   u.department AS uploader_department
            FROM evidence e
            INNER JOIN incidents i ON e.incident_id = i.id
            LEFT JOIN users u ON e.uploaded_by = u.id
            WHERE e.id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function getByIncident(int $incidentId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT e.*, u.full_name AS uploader_name
            FROM evidence e
            LEFT JOIN users u ON e.uploaded_by = u.id
            WHERE e.incident_id = :incident_id
            ORDER BY e.created_at ASC
        ");
        $stmt->execute([':incident_id' => $incidentId]);
        return $stmt->fetchAll();
    }

    public static function getStoragePath(string $storageFilename): string {
        return EVIDENCE_STORAGE_PATH . '/' . basename($storageFilename);
    }

    public static function create(array $data, array $file): array {
        $validation = Security::validateUpload($file);
        if (!$validation['valid']) {
            return ['success' => false, 'error' => $validation['error']];
        }

        $targetPath = EVIDENCE_STORAGE_PATH . '/' . $validation['storage_name'];

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            return ['success' => false, 'error' => 'Failed to store evidence file securely in vault.'];
        }

        // Calculate cryptographic SHA-256 hash at ingestion
        $sha256 = hash_file('sha256', $targetPath);
        if (!$sha256) {
            @unlink($targetPath);
            return ['success' => false, 'error' => 'Failed to generate cryptographic hash for ingested evidence.'];
        }

        $db = Database::getConnection();

        // Unique evidence code FX-EVD-000001
        $stmt = $db->query("SELECT MAX(id) FROM evidence");
        $nextId = ((int)$stmt->fetchColumn()) + 1;
        $evidenceCode = sprintf("FX-EVD-%06d", $nextId);

        $insertStmt = $db->prepare("
            INSERT INTO evidence (
                evidence_code, name, description, incident_id, original_filename,
                storage_filename, file_size, mime_type, file_extension, sha256_hash,
                last_verified_at, last_verified_status, status, tags, acquisition_notes,
                uploaded_by, created_at
            ) VALUES (
                :code, :name, :description, :incident_id, :original_filename,
                :storage_filename, :file_size, :mime_type, :file_extension, :sha256_hash,
                CURRENT_TIMESTAMP, 'Verified', 'In-Analysis', :tags, :acquisition_notes,
                :uploaded_by, CURRENT_TIMESTAMP
            )
        ");

        $insertStmt->execute([
            ':code'              => $evidenceCode,
            ':name'              => $data['name'],
            ':description'       => $data['description'] ?? null,
            ':incident_id'       => (int)$data['incident_id'],
            ':original_filename' => $validation['orig_name'],
            ':storage_filename'  => $validation['storage_name'],
            ':file_size'         => $validation['size'],
            ':mime_type'         => $validation['mime'],
            ':file_extension'    => $validation['extension'],
            ':sha256_hash'       => $sha256,
            ':tags'              => $data['tags'] ?? null,
            ':acquisition_notes' => $data['acquisition_notes'] ?? null,
            ':uploaded_by'       => Auth::id() ?: 1,
        ]);

        $evidenceId = (int)$db->lastInsertId();

        // 1. Chain of Custody: Log Acquisition & Intake
        ChainOfCustody::logEvent(
            $evidenceId,
            'RECEIVED',
            "Evidence item acquired. Source notes: " . ($data['acquisition_notes'] ?? 'Direct forensic ingestion.')
        );
        ChainOfCustody::logEvent(
            $evidenceId,
            'UPLOADED',
            "Uploaded into FORENSIC-X vault. SHA-256 hash computed at intake: {$sha256}.",
            'Acquired',
            'In-Analysis'
        );

        // 2. Audit Trail
        Audit::log('UPLOAD_EVIDENCE', 'evidence', $evidenceCode, 'SUCCESS', "Uploaded '{$validation['orig_name']}' (SHA-256: {$sha256})");

        // 3. Incident Timeline
        Timeline::addEvent(
            (int)$data['incident_id'],
            'EVIDENCE_INTAKE',
            "Evidence Ingested: {$data['name']}",
            "File '{$validation['orig_name']}' ingested into vault. Cryptographic SHA-256: {$sha256}.",
            date('Y-m-d H:i:s'),
            0,
            'evidence',
            $evidenceId
        );

        return ['success' => true, 'evidence_id' => $evidenceId, 'code' => $evidenceCode];
    }

    public static function verifyHash(int $id): array {
        $evidence = self::getById($id);
        if (!$evidence) {
            return ['status' => 'Unavailable', 'message' => 'Evidence record not found.'];
        }

        $filePath = self::getStoragePath($evidence['storage_filename']);
        if (!file_exists($filePath)) {
            $status = 'Unavailable';
            $msg = 'Evidence file does not exist on disk in the storage vault.';
            $currHash = null;
        } else {
            $currHash = hash_file('sha256', $filePath);
            if (hash_equals($evidence['sha256_hash'], $currHash)) {
                $status = 'Verified';
                $msg = 'Cryptographic integrity verified. Current file hash matches original ingestion hash exactly.';
            } else {
                $status = 'Mismatch';
                $msg = 'CRITICAL INTEGRITY WARNING: File hash does not match original ingestion hash! Possible tampering or bit rot.';
            }
        }

        // Update database verification status
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE evidence SET last_verified_at = CURRENT_TIMESTAMP, last_verified_status = :status WHERE id = :id");
        $stmt->execute([':status' => $status, ':id' => $id]);

        // Append to Chain of Custody
        ChainOfCustody::logEvent(
            $id,
            'HASH_VERIFIED',
            "Integrity verification performed. Status: {$status}. Ingestion SHA-256: {$evidence['sha256_hash']}. Current SHA-256: " . ($currHash ?: 'N/A') . "."
        );

        // Audit Log
        Audit::log(
            'VERIFY_EVIDENCE_HASH',
            'evidence',
            $evidence['evidence_code'],
            $status === 'Verified' ? 'SUCCESS' : 'FAILURE',
            "Verification result: {$status}. File: {$evidence['original_filename']}"
        );

        // Timeline entry
        Timeline::addEvent(
            $evidence['incident_id'],
            'INTEGRITY_CHECK',
            "Integrity Verified: {$evidence['evidence_code']}",
            "Integrity check status: {$status}. Result: {$msg}",
            date('Y-m-d H:i:s'),
            0,
            'evidence',
            $id
        );

        return [
            'status'         => $status,
            'message'        => $msg,
            'ingestion_hash' => $evidence['sha256_hash'],
            'current_hash'   => $currHash,
            'verified_at'    => date('Y-m-d H:i:s')
        ];
    }

    public static function getStats(): array {
        $db = Database::getConnection();
        $total = (int)$db->query("SELECT COUNT(*) FROM evidence")->fetchColumn();
        $verified = (int)$db->query("SELECT COUNT(*) FROM evidence WHERE last_verified_status = 'Verified'")->fetchColumn();
        $mismatch = (int)$db->query("SELECT COUNT(*) FROM evidence WHERE last_verified_status = 'Mismatch'")->fetchColumn();
        $unavailable = (int)$db->query("SELECT COUNT(*) FROM evidence WHERE last_verified_status = 'Unavailable'")->fetchColumn();

        return [
            'total'       => $total,
            'verified'    => $verified,
            'mismatch'    => $mismatch,
            'unavailable' => $unavailable,
        ];
    }
}
