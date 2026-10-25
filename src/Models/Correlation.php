<?php
/**
 * FORENSIC-X IOC Correlation Engine
 * Correlates indicators of compromise across ingested text-based forensic evidence
 */

class Correlation {
    public static function runCorrelation(?int $targetIncidentId = null): array {
        $db = Database::getConnection();

        // 1. Fetch all active IOCs
        $iocSql = "SELECT * FROM iocs";
        $iocParams = [];
        if ($targetIncidentId) {
            $iocSql .= " WHERE incident_id = :inc_id OR incident_id IS NULL";
            $iocParams[':inc_id'] = $targetIncidentId;
        }
        $iocStmt = $db->prepare($iocSql);
        $iocStmt->execute($iocParams);
        $iocs = $iocStmt->fetchAll();

        // 2. Fetch supported text-based evidence files
        $evdSql = "
            SELECT * FROM evidence 
            WHERE file_extension IN ('txt', 'log', 'csv', 'json')
        ";
        $evdParams = [];
        if ($targetIncidentId) {
            $evdSql .= " AND incident_id = :inc_id";
            $evdParams[':inc_id'] = $targetIncidentId;
        }
        $evdStmt = $db->prepare($evdSql);
        $evdStmt->execute($evdParams);
        $evidenceItems = $evdStmt->fetchAll();

        $newMatchesCount = 0;
        $processedFiles = 0;

        foreach ($evidenceItems as $evd) {
            $filePath = Evidence::getStoragePath($evd['storage_filename']);
            if (!file_exists($filePath) || filesize($filePath) > 25 * 1024 * 1024) {
                // Skip if missing or extraordinarily large file to preserve memory
                continue;
            }

            $handle = fopen($filePath, 'r');
            if (!$handle) {
                continue;
            }

            $processedFiles++;
            $lineNumber = 0;

            // Stream line by line to keep memory minimal
            while (($line = fgets($handle)) !== false) {
                $lineNumber++;
                $trimmedLine = trim($line);
                if (empty($trimmedLine)) {
                    continue;
                }

                foreach ($iocs as $ioc) {
                    $indicatorVal = $ioc['value'];
                    
                    // Case-insensitive substring match
                    if (stripos($trimmedLine, $indicatorVal) !== false) {
                        // Check if match already recorded
                        $checkStmt = $db->prepare("
                            SELECT id FROM correlation_matches 
                            WHERE ioc_id = :ioc_id AND evidence_id = :evd_id AND line_number = :line_no
                        ");
                        $checkStmt->execute([
                            ':ioc_id'  => $ioc['id'],
                            ':evd_id'  => $evd['id'],
                            ':line_no' => $lineNumber
                        ]);

                        if (!$checkStmt->fetch()) {
                            // Truncate snippet to 300 chars to avoid buffer bloat
                            $snippet = mb_substr($trimmedLine, 0, 300);

                            $insertStmt = $db->prepare("
                                INSERT INTO correlation_matches (
                                    ioc_id, evidence_id, incident_id, matched_value, 
                                    line_number, context_snippet, matched_at
                                ) VALUES (
                                    :ioc_id, :evd_id, :inc_id, :matched_val,
                                    :line_no, :snippet, CURRENT_TIMESTAMP
                                )
                            ");
                            $insertStmt->execute([
                                ':ioc_id'      => $ioc['id'],
                                ':evd_id'      => $evd['id'],
                                ':inc_id'      => $evd['incident_id'],
                                ':matched_val' => $indicatorVal,
                                ':line_no'     => $lineNumber,
                                ':snippet'     => $snippet
                            ]);
                            $newMatchesCount++;
                        }
                    }
                }
            }
            fclose($handle);
        }

        Audit::log('RUN_CORRELATION', 'correlation', (string)($targetIncidentId ?: 'ALL'), 'SUCCESS', "Processed {$processedFiles} evidence files; {$newMatchesCount} new correlation matches discovered.");

        return [
            'success'          => true,
            'files_processed'  => $processedFiles,
            'new_matches'      => $newMatchesCount,
            'total_iocs_used'  => count($iocs)
        ];
    }

    public static function getAllMatches(int $limit = 50, int $offset = 0): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT m.*, 
                   i.value AS ioc_value, 
                   i.indicator_type, 
                   i.confidence,
                   e.name AS evidence_name, 
                   e.evidence_code,
                   inc.incident_code, 
                   inc.title AS incident_title
            FROM correlation_matches m
            INNER JOIN iocs i ON m.ioc_id = i.id
            INNER JOIN evidence e ON m.evidence_id = e.id
            INNER JOIN incidents inc ON m.incident_id = inc.id
            ORDER BY m.matched_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function getMatchesByIncident(int $incidentId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT m.*, 
                   i.value AS ioc_value, 
                   i.indicator_type, 
                   i.confidence,
                   e.name AS evidence_name, 
                   e.evidence_code
            FROM correlation_matches m
            INNER JOIN iocs i ON m.ioc_id = i.id
            INNER JOIN evidence e ON m.evidence_id = e.id
            WHERE m.incident_id = :inc_id
            ORDER BY m.matched_at DESC
        ");
        $stmt->execute([':inc_id' => $incidentId]);
        return $stmt->fetchAll();
    }
}
