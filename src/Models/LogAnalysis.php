<?php
/**
 * FORENSIC-X Log Analysis Engine
 * Detects security anomaly patterns, brute force attacks, web exploits, and privilege escalations
 */

class LogAnalysis {
    public static function analyzeFile(string $filePath, ?string $ruleFilter = null): array {
        if (!file_exists($filePath)) {
            return ['success' => false, 'error' => 'Selected log file does not exist on disk.'];
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return ['success' => false, 'error' => 'Unable to read log file contents.'];
        }

        // Fetch IOCs for IOC correlation rule
        $db = Database::getConnection();
        $iocs = $db->query("SELECT indicator_type, value FROM iocs")->fetchAll();

        $events = [];
        $ruleCounts = [
            'BRUTE_FORCE'       => 0,
            'WEB_EXPLOIT'       => 0,
            'SUSPICIOUS_CMD'    => 0,
            'PRIVILEGE_ESC'     => 0,
            'IOC_DETECTED'      => 0,
            'NORMAL'            => 0
        ];

        foreach ($lines as $idx => $line) {
            $lineNum = $idx + 1;
            $trimmed = trim($line);
            if (empty($trimmed)) continue;

            $matchedRule = null;
            $severity = 'Low';
            $reason = null;

            // 1. Check Privilege Escalation
            if (preg_match('/(USER=root|COMMAND=\/bin\/|apparmor="DENIED"|pam_unix.*session opened for user root)/i', $trimmed)) {
                $matchedRule = 'PRIVILEGE_ESC';
                $severity = 'Critical';
                $reason = 'Unauthorized privilege elevation or restricted root command invocation detected.';
            }
            // 2. Check Web Exploits (SQLi, Directory Traversal, XSS)
            elseif (preg_match('/(UNION\s+SELECT|OR\s+1=1|\.\.\/|\/etc\/passwd|<script|sqlmap)/i', $trimmed)) {
                $matchedRule = 'WEB_EXPLOIT';
                $severity = 'High';
                $reason = 'Web application vulnerability probe identified (SQL Injection or Path Traversal).';
            }
            // 3. Check Brute Force / Authentication Failures
            elseif (preg_match('/(Failed password|authentication failure|invalid user|401\s+[0-9]+|login failed)/i', $trimmed)) {
                $matchedRule = 'BRUTE_FORCE';
                $severity = 'Medium';
                $reason = 'Repeated authentication failure or credential guessing attempt.';
            }
            // 4. Check Suspicious Command Execution / Shell Activity
            elseif (preg_match('/(wget|curl.*Exfiltrat|webshell\.php|powershell|Invoke-WebRequest|whoami|uname -a)/i', $trimmed)) {
                $matchedRule = 'SUSPICIOUS_CMD';
                $severity = 'High';
                $reason = 'Potential webshell interaction or staging / exfiltration command executed.';
            }
            // 5. Check Configured IOCs
            else {
                foreach ($iocs as $ioc) {
                    if (stripos($trimmed, $ioc['value']) !== false) {
                        $matchedRule = 'IOC_DETECTED';
                        $severity = 'High';
                        $reason = "Known Indicator of Compromise ({$ioc['indicator_type']}: {$ioc['value']}) present in log entry.";
                        break;
                    }
                }
            }

            if ($matchedRule) {
                $ruleCounts[$matchedRule]++;
            } else {
                $ruleCounts['NORMAL']++;
                $matchedRule = 'NORMAL';
            }

            if (!$ruleFilter || $ruleFilter === 'ALL' || $ruleFilter === $matchedRule) {
                $events[] = [
                    'line_number'  => $lineNum,
                    'raw_text'     => $trimmed,
                    'rule'         => $matchedRule,
                    'severity'     => $severity,
                    'reason'       => $reason,
                    'flagged'      => ($matchedRule !== 'NORMAL')
                ];
            }
        }

        return [
            'success'     => true,
            'total_lines' => count($lines),
            'counts'      => $ruleCounts,
            'events'      => array_slice($events, 0, 500) // Cap display to first 500 lines for performance
        ];
    }
}
