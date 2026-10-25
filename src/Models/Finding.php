<?php
/**
 * FORENSIC-X Threat Severity & Analysis Findings Model
 */

class Finding {
    public static function getByIncident(int $incidentId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT f.*, 
                   e.name AS evidence_name, 
                   e.evidence_code,
                   u.full_name AS creator_name,
                   u.username AS creator_username
            FROM analysis_findings f
            LEFT JOIN evidence e ON f.evidence_id = e.id
            LEFT JOIN users u ON f.created_by = u.id
            WHERE f.incident_id = :incident_id
            ORDER BY f.created_at DESC
        ");
        $stmt->execute([':incident_id' => $incidentId]);
        return $stmt->fetchAll();
    }

    public static function create(array $data): array {
        $db = Database::getConnection();

        // Generate next code FX-FND-000001
        $stmt = $db->query("SELECT MAX(id) FROM analysis_findings");
        $nextId = ((int)$stmt->fetchColumn()) + 1;
        $findingCode = sprintf("FX-FND-%06d", $nextId);

        $insertStmt = $db->prepare("
            INSERT INTO analysis_findings (
                finding_code, incident_id, evidence_id, title, description, 
                severity, category, supporting_evidence, analysis_notes, 
                recommended_action, created_by, created_at
            ) VALUES (
                :code, :incident_id, :evidence_id, :title, :description,
                :severity, :category, :supporting_evidence, :analysis_notes,
                :recommended_action, :created_by, CURRENT_TIMESTAMP
            )
        ");

        $insertStmt->execute([
            ':code'                 => $findingCode,
            ':incident_id'          => (int)$data['incident_id'],
            ':evidence_id'          => !empty($data['evidence_id']) ? (int)$data['evidence_id'] : null,
            ':title'                => $data['title'],
            ':description'          => $data['description'],
            ':severity'             => $data['severity'] ?? 'Medium',
            ':category'             => $data['category'],
            ':supporting_evidence'  => $data['supporting_evidence'] ?? null,
            ':analysis_notes'       => $data['analysis_notes'] ?? null,
            ':recommended_action'   => $data['recommended_action'] ?? null,
            ':created_by'           => Auth::id() ?: 1,
        ]);

        $findingId = (int)$db->lastInsertId();

        Audit::log('CREATE_FINDING', 'finding', $findingCode, 'SUCCESS', "Created finding: {$data['title']} ({$data['severity']})");

        Timeline::addEvent(
            (int)$data['incident_id'],
            'LOG_FINDING',
            "Finding Logged: {$data['title']}",
            "Severity: {$data['severity']}. Category: {$data['category']}. Analysis: {$data['description']}",
            date('Y-m-d H:i:s'),
            0,
            'analysis_findings',
            $findingId
        );

        return ['success' => true, 'finding_id' => $findingId, 'code' => $findingCode];
    }
}
