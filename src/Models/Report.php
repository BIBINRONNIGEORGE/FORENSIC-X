<?php
/**
 * FORENSIC-X Investigation Report Generation Model
 */

class Report {
    public static function getAll(): array {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT r.*, 
                   i.incident_code, 
                   i.title AS incident_title,
                   i.severity AS incident_severity,
                   u.full_name AS generator_name,
                   u.username AS generator_username
            FROM reports r
            INNER JOIN incidents i ON r.incident_id = i.id
            LEFT JOIN users u ON r.generated_by = u.id
            ORDER BY r.created_at DESC
        ");
        return $stmt->fetchAll();
    }

    public static function getById(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT r.*, 
                   i.incident_code, 
                   i.title AS incident_title,
                   i.severity AS incident_severity,
                   i.status AS incident_status,
                   i.category AS incident_category,
                   i.detected_at,
                   i.affected_system,
                   u.full_name AS generator_name,
                   u.department AS generator_department,
                   inv.full_name AS assigned_investigator
            FROM reports r
            INNER JOIN incidents i ON r.incident_id = i.id
            LEFT JOIN users u ON r.generated_by = u.id
            LEFT JOIN users inv ON i.assigned_to = inv.id
            WHERE r.id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function compileDataForIncident(int $incidentId): array {
        $incident = Incident::getById($incidentId);
        $evidence = Evidence::getByIncident($incidentId);
        
        $custodyByEvidence = [];
        foreach ($evidence as $evd) {
            $custodyByEvidence[$evd['id']] = ChainOfCustody::getByEvidenceId($evd['id']);
        }

        $iocs = IOC::getAll(['incident_id' => $incidentId]);
        $correlationMatches = Correlation::getMatchesByIncident($incidentId);
        $timeline = Timeline::getIncidentTimeline($incidentId);
        $findings = Finding::getByIncident($incidentId);

        return [
            'incident'            => $incident,
            'evidence'            => $evidence,
            'custody'             => $custodyByEvidence,
            'iocs'                => $iocs,
            'correlation_matches' => $correlationMatches,
            'timeline'            => $timeline,
            'findings'            => $findings,
        ];
    }

    public static function create(array $data): array {
        $db = Database::getConnection();

        // Generate next report code FX-RPT-000001
        $stmt = $db->query("SELECT MAX(id) FROM reports");
        $nextId = ((int)$stmt->fetchColumn()) + 1;
        $reportCode = sprintf("FX-RPT-%06d", $nextId);

        $insertStmt = $db->prepare("
            INSERT INTO reports (
                report_code, incident_id, title, executive_summary, 
                findings_summary, recommendations, conclusions, generated_by, created_at
            ) VALUES (
                :code, :incident_id, :title, :executive_summary, 
                :findings_summary, :recommendations, :conclusions, :generated_by, NOW()
            )
        ");

        $insertStmt->execute([
            ':code'               => $reportCode,
            ':incident_id'        => (int)$data['incident_id'],
            ':title'              => $data['title'],
            ':executive_summary'  => $data['executive_summary'],
            ':findings_summary'   => $data['findings_summary'],
            ':recommendations'    => $data['recommendations'],
            ':conclusions'        => $data['conclusions'],
            ':generated_by'       => Auth::id() ?: 1,
        ]);

        $reportId = (int)$db->lastInsertId();

        Audit::log('GENERATE_REPORT', 'report', $reportCode, 'SUCCESS', "Generated formal forensic report: {$data['title']}");

        Timeline::addEvent(
            (int)$data['incident_id'],
            'REPORT_GENERATION',
            "Forensic Report Generated: {$reportCode}",
            "Formal investigation report compiled and finalized. Ready for academic / SOC sign-off.",
            date('Y-m-d H:i:s'),
            0,
            'reports',
            $reportId
        );

        return ['success' => true, 'report_id' => $reportId, 'code' => $reportCode];
    }
}
