-- FORENSIC-X Seed Data
-- B.Tech CSE (Cyber Security) Minor Project Demonstration Dataset

-- 1. USERS SEED
-- Passwords:
-- admin: Admin@ForensicX2026!
-- investigator: Investigator@2026!
-- analyst: Analyst@2026!
-- reviewer: Reviewer@2026!

INSERT INTO users (id, username, email, password_hash, full_name, role, department, is_active, created_at) VALUES
(1, 'admin', 'admin@forensic-x.local', '$2y$12$4VsKm6BCkLNcRLxE/07hUuo35PsqomPwjOc0/HCcFMsdO/.ezgupO', 'Dr. Arun Kumar (SOC Lead)', 'admin', 'SOC Operations & Administration', 1, '2026-10-25 04:00:00'),
(2, 'investigator', 'investigator@forensic-x.local', '$2y$12$LOAHLpao/JNyj./KZJMr1.R0FIfXqFHPUtBxfK1a/jlLXgC/NG2ry', 'Meera Nair (Lead Forensic Specialist)', 'investigator', 'Digital Forensics & Incident Response', 1, '2026-10-25 04:05:00'),
(3, 'analyst', 'analyst@forensic-x.local', '$2y$12$RyxPvlHstGxUR42x97fDTe1Nh7Gk9rdIMXqdVZnDxvypBHZYY5xYi', 'Rahul Varma (Threat Intelligence Analyst)', 'analyst', 'Threat Hunting & IOC Correlation', 1, '2026-10-25 04:10:00'),
(4, 'reviewer', 'reviewer@forensic-x.local', '$2y$12$b08xiQzyD3qAUp8x1IzFPuXyxaOWk2lvS5Dy2D.nYWB72hZvLk9gW', 'Prof. Jacob Thomas (Faculty Reviewer)', 'reviewer', 'Cyber Security Academic Review Board', 1, '2026-10-25 04:15:00');

-- 2. INCIDENTS SEED
INSERT INTO incidents (id, incident_code, title, description, category, severity, status, assigned_to, detected_at, source, affected_system, tags, findings_notes, created_by, created_at) VALUES
(1, 'FX-INC-000001', 'Ransomware Infiltration & Lateral Movement on Core Financial DB', 'Automated alerts detected unauthorized privileged SSH session on SEC-SRV-01 originating from known threat actor IP 198.51.100.45. Host subsequently attempted lateral connections and beaconing to external C2 infrastructure.', 'Ransomware', 'Critical', 'Investigating', 2, '2026-10-25 04:12:00', 'SIEM Rule Alert & EDR Probe', 'SEC-SRV-01 (Core Financial Cluster)', 'ransomware, lateral-movement, ssh-bruteforce, finance', 'Attacker leveraged brute force against service account followed by sudo privilege escalation. PowerShell staging artifact was deployed.', 1, '2026-10-25 04:20:00'),
(2, 'FX-INC-000002', 'Web Application SQL Injection & Traversal Probing', 'Security sensors flagged an external IP executing repetitive SQL injection payloads and directory traversal probes targeting student portal API endpoints.', 'Unauthorized Access', 'High', 'Contained', 2, '2026-10-25 05:05:00', 'WAF / Reverse Proxy Logs', 'PORTAL-WEB-02', 'sqli, path-traversal, web-attack', 'IP was temporarily isolated by perimeter firewall. Vulnerable endpoint verified and patched.', 1, '2026-10-25 05:15:00'),
(3, 'FX-INC-000003', 'Spear Phishing Campaign Targeting Senior Administration', 'Phishing emails with deceptive financial attachments containing malicious macro links masquerading as bank updates were delivered to 14 administrative accounts.', 'Phishing', 'Medium', 'Resolved', 2, '2026-10-25 02:30:00', 'Mail Gateway Alert', 'EXCHANGE-MAIL-01', 'phishing, credential-harvest, social-engineering', 'All malicious links were blocked at DNS and perimeter level. Impacted users reset their credentials.', 2, '2026-10-25 02:45:00');

-- 3. EVIDENCE SEED
INSERT INTO evidence (id, evidence_code, name, description, incident_id, original_filename, storage_filename, file_size, mime_type, file_extension, sha256_hash, last_verified_at, last_verified_status, status, tags, acquisition_notes, uploaded_by, created_at) VALUES
(1, 'FX-EVD-000001', 'Authentication Syslog Triage Dump', 'Linux authentication logs captured directly from SEC-SRV-01 encompassing timeline of initial breach and unauthorized privilege elevation.', 1, 'auth_audit.log', 'evd_storage_001_auth.log', 1252, 'text/plain', 'log', '4da3d9b877095055333485cb490f1b6ba1dec6e28fb9a2c6aca53f11de0fd5cc', '2026-10-25 04:35:00', 'Verified', 'In-Analysis', 'syslog, authentication, ssh', 'Acquired via live forensic imaging script with root read permissions.', 2, '2026-10-25 04:30:00'),
(2, 'FX-EVD-000002', 'Web Access Log Extract (Apache)', 'Apache HTTP server access log snippet documenting attacking IP 203.0.113.195 conducting SQLi and directory traversal attempts.', 2, 'web_access_sample.log', 'evd_storage_002_web.log', 931, 'text/plain', 'log', '977cdf5244495f25823660c26f7fde55d77a0592e53fa2d9a6ec31ea9f9bac61', '2026-10-25 05:25:00', 'Verified', 'In-Analysis', 'apache, web-server, access-log', 'Extracted from /var/log/apache2/access.log at PORTAL-WEB-02.', 2, '2026-10-25 05:20:00'),
(3, 'FX-EVD-000003', 'Malicious PowerShell Stager Artifact', 'PowerShell beacon script recovered from temporary filesystem folder on infected host containing C2 endpoints and base64 exfiltration functions.', 1, 'malicious_script_payload.ps1.txt', 'evd_storage_003_script.txt', 644, 'text/plain', 'txt', 'b5f34cf2fd19afa9b071bd6d75974d0f373fecf3c94a32b52ea329b0c6db6fb2', '2026-10-25 04:45:00', 'Verified', 'In-Analysis', 'powershell, malware, beacon, c2', 'Recovered from %TEMP% directory during live response triage.', 2, '2026-10-25 04:40:00');

-- 4. CHAIN OF CUSTODY SEED
INSERT INTO evidence_custody_events (evidence_id, event_type, actor_id, actor_name, previous_state, new_state, notes, ip_address, created_at) VALUES
(1, 'RECEIVED', 2, 'Meera Nair (Lead Forensic Specialist)', NULL, 'Acquired', 'Evidence log file acquired from target host SEC-SRV-01 via secure encrypted transfer.', '127.0.0.1', '2026-10-25 04:30:00'),
(1, 'UPLOADED', 2, 'Meera Nair (Lead Forensic Specialist)', 'Acquired', 'In-Analysis', 'Ingested into FORENSIC-X Evidence Vault. Ingestion SHA-256 hash computed and recorded.', '127.0.0.1', '2026-10-25 04:30:05'),
(1, 'HASH_VERIFIED', 2, 'Meera Nair (Lead Forensic Specialist)', 'In-Analysis', 'In-Analysis', 'Cryptographic verification check performed. Current file hash matches original ingestion hash exactly (4da3d9b877...).', '127.0.0.1', '2026-10-25 04:35:00'),
(2, 'RECEIVED', 2, 'Meera Nair (Lead Forensic Specialist)', NULL, 'Acquired', 'Exported from reverse proxy log repository.', '127.0.0.1', '2026-10-25 05:20:00'),
(2, 'UPLOADED', 2, 'Meera Nair (Lead Forensic Specialist)', 'Acquired', 'In-Analysis', 'Uploaded to vault. Calculated SHA-256: 977cdf5244...', '127.0.0.1', '2026-10-25 05:20:04'),
(2, 'HASH_VERIFIED', 3, 'Rahul Varma (Threat Intelligence Analyst)', 'In-Analysis', 'In-Analysis', 'Integrity verified prior to log analysis parsing.', '127.0.0.1', '2026-10-25 05:25:00'),
(3, 'RECEIVED', 2, 'Meera Nair (Lead Forensic Specialist)', NULL, 'Acquired', 'Staged PowerShell script isolated into secure analysis container.', '127.0.0.1', '2026-10-25 04:40:00'),
(3, 'UPLOADED', 2, 'Meera Nair (Lead Forensic Specialist)', 'Acquired', 'In-Analysis', 'Ingested into vault with strict non-execution policy.', '127.0.0.1', '2026-10-25 04:40:08'),
(3, 'HASH_VERIFIED', 2, 'Meera Nair (Lead Forensic Specialist)', 'In-Analysis', 'In-Analysis', 'Routine integrity re-check verified zero bit alterations.', '127.0.0.1', '2026-10-25 04:45:00');

-- 5. IOCs SEED
INSERT INTO iocs (id, ioc_code, indicator_type, value, description, confidence, threat_source, first_seen, last_seen, incident_id, created_by, created_at) VALUES
(1, 'FX-IOC-000001', 'IPv4', '198.51.100.45', 'Attacker external C2 / SSH brute-force staging host IP address', 'High', 'SEC-SRV-01 Auth Logs', '2026-10-25 04:12:01', '2026-10-25 05:11:02', 1, 3, '2026-10-25 04:50:00'),
(2, 'FX-IOC-000002', 'Domain', 'c2-stealth-update.net', 'Command and Control domain discovered inside PowerShell beacon', 'High', 'Malicious Script Payload Analysis', '2026-10-25 04:13:45', '2026-10-25 04:13:45', 1, 3, '2026-10-25 04:52:00'),
(3, 'FX-IOC-000003', 'URL', 'https://data-drop.malicious-domain.org/collect', 'Target exfiltration webhook for staging stolen credentials', 'High', 'PowerShell Stager & Curl Event', '2026-10-25 04:14:10', '2026-10-25 04:14:10', 1, 3, '2026-10-25 04:55:00'),
(4, 'FX-IOC-000004', 'IPv4', '203.0.113.195', 'Source IP address executing automated sqlmap and traversal requests', 'Medium', 'Web Application Firewall Logs', '2026-10-25 05:05:22', '2026-10-25 05:06:01', 2, 3, '2026-10-25 05:30:00'),
(5, 'FX-IOC-000005', 'FileHash-SHA256', 'b5f34cf2fd19afa9b071bd6d75974d0f373fecf3c94a32b52ea329b0c6db6fb2', 'Cryptographic hash of the recovered malicious PowerShell stager', 'High', 'File Ingestion Analysis', '2026-10-25 04:40:00', '2026-10-25 04:40:00', 1, 3, '2026-10-25 04:58:00');

-- 6. CORRELATION MATCHES SEED
INSERT INTO correlation_matches (id, ioc_id, evidence_id, incident_id, matched_value, line_number, context_snippet, matched_at) VALUES
(1, 1, 1, 1, '198.51.100.45', 1, 'Failed password for invalid user admin from 198.51.100.45 port 49152 ssh2', '2026-10-25 05:00:00'),
(2, 1, 1, 1, '198.51.100.45', 6, 'Accepted password for service_account from 198.51.100.45 port 49170 ssh2', '2026-10-25 05:00:00'),
(3, 2, 1, 1, 'c2-stealth-update.net', 10, 'wget[14305]: Connecting to c2-stealth-update.net:443... connected.', '2026-10-25 05:00:00'),
(4, 3, 1, 1, 'https://data-drop.malicious-domain.org/collect', 11, 'curl[14312]: Exfiltrating payload to https://data-drop.malicious-domain.org/collect?node=sec-srv-01', '2026-10-25 05:00:00'),
(5, 4, 2, 2, '203.0.113.195', 3, '203.0.113.195 - - [25/Oct/2026:05:05:22 +0000] "GET /login.php HTTP/1.1" 200 3120', '2026-10-25 05:35:00'),
(6, 1, 3, 1, '198.51.100.45', 3, '$C2_IP = "198.51.100.45"', '2026-10-25 05:01:00'),
(7, 2, 3, 1, 'c2-stealth-update.net', 4, '$C2_Domain = "c2-stealth-update.net"', '2026-10-25 05:01:00'),
(8, 3, 3, 1, 'https://data-drop.malicious-domain.org/collect', 5, '$Callback_URL = "https://data-drop.malicious-domain.org/collect"', '2026-10-25 05:01:00');

-- 7. ANALYSIS FINDINGS SEED
INSERT INTO analysis_findings (id, finding_code, incident_id, evidence_id, title, description, severity, category, supporting_evidence, analysis_notes, recommended_action, created_by, created_at) VALUES
(1, 'FX-FND-000001', 1, 1, 'SSH Brute-Force Password Guessing & Privilege Escalation', 'Attacker performed an automated password spray from 198.51.100.45 resulting in credential compromise of service_account, followed by unauthorized sudo execution.', 'Critical', 'Privilege Escalation', 'auth_audit.log lines 1-8', 'The service account lacked multi-factor authentication and had overly broad sudo permissions without password prompt.', 'Enforce key-based SSH authentication, disable remote login for service accounts, and restrict sudoers entries.', 3, '2026-10-25 05:05:00'),
(2, 'FX-FND-000002', 1, 3, 'C2 Persistence Stager via PowerShell Base64 Encoded Webhook', 'A malicious PowerShell script was placed in the temp directory configured to transmit host discovery data to an external exfiltration endpoint.', 'High', 'Malware Persistence', 'malicious_script_payload.ps1.txt', 'The script attempts to execute periodically to report host configuration to https://data-drop.malicious-domain.org/collect.', 'Block external traffic to domain c2-stealth-update.net and IP 198.51.100.45. Audit temp folders on all domain hosts.', 3, '2026-10-25 05:10:00'),
(3, 'FX-FND-000003', 2, 2, 'Automated SQL Injection & Directory Traversal Exploration', 'Web access logs prove automated reconnaissance using sqlmap against /api/user and file inclusion probes against /view.php.', 'High', 'Web Application Attack', 'web_access_sample.log lines 3-6', 'Attacker targeted parameter id with UNION SELECT and requested ../../../../etc/passwd.', 'Ensure prepared SQL statements are mandatory across all endpoints and sanitize file path inputs.', 3, '2026-10-25 05:35:00');

-- 8. INVESTIGATION TIMELINE EVENTS SEED
INSERT INTO investigation_timeline_events (incident_id, event_category, event_title, description, event_time, is_source_log_timestamp, actor_id, reference_table, reference_id, created_at) VALUES
(1, 'INCIDENT_LIFECYCLE', 'Incident Created', 'Security alert escalated to incident FX-INC-000001 by SOC Operations.', '2026-10-25 04:20:00', 0, 1, 'incidents', 1, '2026-10-25 04:20:00'),
(1, 'EVIDENCE_INTAKE', 'Evidence Ingested: auth_audit.log', 'Authentication log from SEC-SRV-01 added to the Evidence Vault.', '2026-10-25 04:30:00', 0, 2, 'evidence', 1, '2026-10-25 04:30:00'),
(1, 'INTEGRITY_CHECK', 'Cryptographic SHA-256 Verified', 'Ingestion hash computed: 4da3d9b877095055333485cb490f1b6ba1dec6e28fb9a2c6aca53f11de0fd5cc.', '2026-10-25 04:35:00', 0, 2, 'evidence', 1, '2026-10-25 04:35:00'),
(1, 'EVIDENCE_INTAKE', 'Evidence Ingested: malicious_script_payload.ps1.txt', 'Triage stager script extracted from target filesystem.', '2026-10-25 04:40:00', 0, 2, 'evidence', 3, '2026-10-25 04:40:00'),
(1, 'IOC_CORRELATION', 'IOC Correlation Matches Detected', 'Correlation engine found 4 matches linking IOC 198.51.100.45 and C2 domains to the evidence files.', '2026-10-25 05:00:00', 0, 3, 'iocs', 1, '2026-10-25 05:00:00'),
(1, 'LOG_FINDING', 'Finding Logged: SSH Brute-Force & Privilege Escalation', 'Analyst recorded confirmed attack path with Critical severity rating.', '2026-10-25 05:05:00', 0, 3, 'analysis_findings', 1, '2026-10-25 05:05:00'),
(1, 'INCIDENT_LIFECYCLE', 'Status Updated to Investigating', 'Assigned investigator Meera Nair actively correlating artifacts.', '2026-10-25 05:10:00', 0, 2, 'incidents', 1, '2026-10-25 05:10:00'),
(2, 'INCIDENT_LIFECYCLE', 'Incident Created', 'Web Application Attack Incident FX-INC-000002 initiated.', '2026-10-25 05:15:00', 0, 1, 'incidents', 2, '2026-10-25 05:15:00'),
(2, 'EVIDENCE_INTAKE', 'Evidence Ingested: web_access_sample.log', 'Apache access log captured from PORTAL-WEB-02.', '2026-10-25 05:20:00', 0, 2, 'evidence', 2, '2026-10-25 05:20:00'),
(2, 'LOG_FINDING', 'Finding Logged: SQL Injection Probes', 'Confirmed attack signature from 203.0.113.195.', '2026-10-25 05:35:00', 0, 3, 'analysis_findings', 3, '2026-10-25 05:35:00'),
(2, 'INCIDENT_LIFECYCLE', 'Status Updated to Contained', 'Perimeter block active; vulnerable API parameter patched.', '2026-10-25 05:40:00', 0, 2, 'incidents', 2, '2026-10-25 05:40:00');

-- 9. REPORTS SEED
INSERT INTO reports (id, report_code, incident_id, title, executive_summary, findings_summary, recommendations, conclusions, generated_by, created_at) VALUES
(1, 'FX-RPT-000001', 1, 'Forensic Triage & Attack Vector Assessment: SEC-SRV-01', 'On October 25, 2026, the SOC detected unauthorized lateral activity on SEC-SRV-01. Forensic ingestion of authentication logs and forensic artifacts confirmed an external brute-force attack originating from 198.51.100.45. Compromised credentials were used to gain initial access, followed by execution of a PowerShell staging beacon targeting external C2 servers.', '1. Initial Access: Password guessing against service_account via SSH.\n2. Privilege Escalation: Exploitation of unpassworded sudo bash permission.\n3. Defense Evasion & Persistence: Dropped base64 encoded PowerShell beacon in temporary storage.', '1. Enforce Mandatory MFA on all external and internal SSH sessions.\n2. Restrict sudoers rules to specific non-shell binaries.\n3. Blacklist IOCs 198.51.100.45 and c2-stealth-update.net across campus firewalls.\n4. Deploy EDR continuous process monitoring on all Linux core servers.', 'The threat actor succeeded in establishing initial foothold and privilege escalation. Prompt containment and evidence isolation prevented data destruction or encryption. Full remediation and monitoring continue.', 2, '2026-10-25 05:30:00');

-- 10. AUDIT LOGS SEED
INSERT INTO audit_logs (id, user_id, username, action, target_type, target_id, ip_address, user_agent, outcome, details, created_at) VALUES
(1, 1, 'admin', 'LOGIN', 'user', '1', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64)', 'SUCCESS', 'Administrator successfully authenticated.', '2026-10-25 04:00:00'),
(2, 1, 'admin', 'CREATE_INCIDENT', 'incident', 'FX-INC-000001', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64)', 'SUCCESS', 'Created incident FX-INC-000001 with Critical severity.', '2026-10-25 04:20:00'),
(3, 2, 'investigator', 'LOGIN', 'user', '2', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64)', 'SUCCESS', 'Investigator session established.', '2026-10-25 04:25:00'),
(4, 2, 'investigator', 'UPLOAD_EVIDENCE', 'evidence', 'FX-EVD-000001', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64)', 'SUCCESS', 'Uploaded evidence auth_audit.log. SHA-256 computed: 4da3d9b87709...', '2026-10-25 04:30:05'),
(5, 2, 'investigator', 'VERIFY_EVIDENCE_HASH', 'evidence', 'FX-EVD-000001', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64)', 'SUCCESS', 'SHA-256 hash verified. Status: Verified.', '2026-10-25 04:35:00'),
(6, 2, 'investigator', 'UPLOAD_EVIDENCE', 'evidence', 'FX-EVD-000003', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64)', 'SUCCESS', 'Uploaded evidence malicious_script_payload.ps1.txt.', '2026-10-25 04:40:08'),
(7, 3, 'analyst', 'LOGIN', 'user', '3', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64)', 'SUCCESS', 'Analyst session started.', '2026-10-25 04:45:00'),
(8, 3, 'analyst', 'CREATE_IOC', 'ioc', 'FX-IOC-000001', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64)', 'SUCCESS', 'Created IOC 198.51.100.45.', '2026-10-25 04:50:00'),
(9, 3, 'analyst', 'RUN_CORRELATION', 'correlation', 'ALL', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64)', 'SUCCESS', 'Executed IOC correlation engine across all evidence files. Discovered 8 matches.', '2026-10-25 05:00:00'),
(10, 2, 'investigator', 'GENERATE_REPORT', 'report', 'FX-RPT-000001', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64)', 'SUCCESS', 'Generated forensic report FX-RPT-000001 for incident FX-INC-000001.', '2026-10-25 05:30:00');

-- 11. SYSTEM SETTINGS SEED
INSERT INTO system_settings (setting_key, setting_value, description) VALUES
('platform_name', 'FORENSIC-X', 'Platform brand name'),
('platform_subtitle', 'Cybersecurity Incident Investigation & Digital Forensics Platform', 'Subtitle'),
('institution', 'College of Engineering Kallooppara, Kerala', 'Educational Institution Name'),
('project_type', 'B.Tech Computer Science and Engineering (Cyber Security) Minor Project', 'Academic Project Type'),
('version', '1.0.0', 'Platform release version'),
('workflow_stages', 'DETECT → INVESTIGATE → ANALYZE → CORRELATE → REPORT', 'Investigation workflow standard'),
('session_timeout_seconds', '1800', 'Inactivity session timeout (30 minutes)'),
('max_failed_logins', '5', 'Failed login attempts before temporary lockout'),
('allowed_evidence_extensions', 'txt,log,csv,json,pcap,pcapng,pdf', 'Permitted forensic evidence file extensions'),
('max_evidence_size_bytes', '52428800', 'Maximum evidence file upload size (50MB)');
