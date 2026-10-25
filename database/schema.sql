-- FORENSIC-X Database Schema
-- B.Tech CSE (Cyber Security) Minor Project
-- College of Engineering Kallooppara, Kerala

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS reports;
DROP TABLE IF EXISTS investigation_timeline_events;
DROP TABLE IF EXISTS analysis_findings;
DROP TABLE IF EXISTS correlation_matches;
DROP TABLE IF EXISTS iocs;
DROP TABLE IF EXISTS evidence_custody_events;
DROP TABLE IF EXISTS evidence;
DROP TABLE IF EXISTS incidents;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS system_settings;

SET FOREIGN_KEY_CHECKS = 1;

-- 1. USERS & RBAC TABLE
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'investigator', 'analyst', 'reviewer') NOT NULL DEFAULT 'analyst',
    department VARCHAR(100) DEFAULT 'SOC / Digital Forensics',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    failed_login_attempts INT NOT NULL DEFAULT 0,
    locked_until DATETIME NULL,
    last_login_at DATETIME NULL,
    last_login_ip VARCHAR(45) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user_role (role),
    INDEX idx_user_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. INCIDENTS TABLE
CREATE TABLE incidents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    incident_code VARCHAR(20) NOT NULL UNIQUE,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(60) NOT NULL,
    severity ENUM('Low', 'Medium', 'High', 'Critical') NOT NULL DEFAULT 'Medium',
    status ENUM('Open', 'Investigating', 'Contained', 'Resolved', 'Closed') NOT NULL DEFAULT 'Open',
    assigned_to INT NULL,
    detected_at DATETIME NOT NULL,
    source VARCHAR(100) NOT NULL,
    affected_system VARCHAR(150) NOT NULL,
    tags VARCHAR(255) NULL,
    findings_notes TEXT NULL,
    created_by INT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_inc_severity (severity),
    INDEX idx_inc_status (status),
    INDEX idx_inc_assigned (assigned_to),
    INDEX idx_inc_detected (detected_at),
    CONSTRAINT fk_inc_assigned FOREIGN KEY (assigned_to) REFERENCES users (id) ON DELETE SET NULL,
    CONSTRAINT fk_inc_creator FOREIGN KEY (created_by) REFERENCES users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. EVIDENCE TABLE
CREATE TABLE evidence (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evidence_code VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    description TEXT NULL,
    incident_id INT NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    storage_filename VARCHAR(255) NOT NULL UNIQUE,
    file_size BIGINT NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    file_extension VARCHAR(20) NOT NULL,
    sha256_hash VARCHAR(64) NOT NULL,
    last_verified_at DATETIME NULL,
    last_verified_status ENUM('Unchecked', 'Verified', 'Mismatch', 'Unavailable') NOT NULL DEFAULT 'Verified',
    status ENUM('Acquired', 'In-Analysis', 'Archived', 'Sealed') NOT NULL DEFAULT 'Acquired',
    tags VARCHAR(255) NULL,
    acquisition_notes TEXT NULL,
    uploaded_by INT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_evd_incident (incident_id),
    INDEX idx_evd_sha256 (sha256_hash),
    INDEX idx_evd_status (status),
    CONSTRAINT fk_evd_incident FOREIGN KEY (incident_id) REFERENCES incidents (id) ON DELETE CASCADE,
    CONSTRAINT fk_evd_uploader FOREIGN KEY (uploaded_by) REFERENCES users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. CHAIN OF CUSTODY (Append-only)
CREATE TABLE evidence_custody_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evidence_id INT NOT NULL,
    event_type VARCHAR(50) NOT NULL,
    actor_id INT NULL,
    actor_name VARCHAR(100) NOT NULL,
    previous_state VARCHAR(100) NULL,
    new_state VARCHAR(100) NULL,
    notes TEXT NOT NULL,
    ip_address VARCHAR(45) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_custody_evidence (evidence_id),
    INDEX idx_custody_time (created_at),
    CONSTRAINT fk_custody_evidence FOREIGN KEY (evidence_id) REFERENCES evidence (id) ON DELETE CASCADE,
    CONSTRAINT fk_custody_actor FOREIGN KEY (actor_id) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. INDICATORS OF COMPROMISE (IOCs)
CREATE TABLE iocs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ioc_code VARCHAR(20) NOT NULL UNIQUE,
    indicator_type ENUM('IPv4', 'IPv6', 'Domain', 'URL', 'FileHash-SHA256', 'FileHash-MD5') NOT NULL,
    value VARCHAR(500) NOT NULL,
    description TEXT NULL,
    confidence ENUM('Low', 'Medium', 'High') NOT NULL DEFAULT 'Medium',
    threat_source VARCHAR(150) NULL,
    first_seen DATETIME NULL,
    last_seen DATETIME NULL,
    incident_id INT NULL,
    created_by INT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_ioc_type (indicator_type),
    INDEX idx_ioc_value (value(191)),
    INDEX idx_ioc_incident (incident_id),
    CONSTRAINT fk_ioc_incident FOREIGN KEY (incident_id) REFERENCES incidents (id) ON DELETE SET NULL,
    CONSTRAINT fk_ioc_creator FOREIGN KEY (created_by) REFERENCES users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. CORRELATION MATCHES
CREATE TABLE correlation_matches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ioc_id INT NOT NULL,
    evidence_id INT NOT NULL,
    incident_id INT NOT NULL,
    matched_value VARCHAR(500) NOT NULL,
    line_number INT NULL,
    context_snippet TEXT NOT NULL,
    matched_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_corr_ioc (ioc_id),
    INDEX idx_corr_evidence (evidence_id),
    INDEX idx_corr_incident (incident_id),
    CONSTRAINT fk_corr_ioc FOREIGN KEY (ioc_id) REFERENCES iocs (id) ON DELETE CASCADE,
    CONSTRAINT fk_corr_evidence FOREIGN KEY (evidence_id) REFERENCES evidence (id) ON DELETE CASCADE,
    CONSTRAINT fk_corr_incident FOREIGN KEY (incident_id) REFERENCES incidents (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. ANALYSIS FINDINGS
CREATE TABLE analysis_findings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    finding_code VARCHAR(20) NOT NULL UNIQUE,
    incident_id INT NOT NULL,
    evidence_id INT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    severity ENUM('Low', 'Medium', 'High', 'Critical') NOT NULL DEFAULT 'Medium',
    category VARCHAR(100) NOT NULL,
    supporting_evidence TEXT NULL,
    analysis_notes TEXT NULL,
    recommended_action TEXT NULL,
    created_by INT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_fnd_incident (incident_id),
    INDEX idx_fnd_severity (severity),
    CONSTRAINT fk_fnd_incident FOREIGN KEY (incident_id) REFERENCES incidents (id) ON DELETE CASCADE,
    CONSTRAINT fk_fnd_evidence FOREIGN KEY (evidence_id) REFERENCES evidence (id) ON DELETE SET NULL,
    CONSTRAINT fk_fnd_creator FOREIGN KEY (created_by) REFERENCES users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. INVESTIGATION TIMELINE EVENTS
CREATE TABLE investigation_timeline_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    incident_id INT NOT NULL,
    event_category VARCHAR(50) NOT NULL,
    event_title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    event_time DATETIME NOT NULL,
    is_source_log_timestamp TINYINT(1) NOT NULL DEFAULT 0,
    actor_id INT NULL,
    reference_table VARCHAR(50) NULL,
    reference_id INT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_time_incident (incident_id),
    INDEX idx_time_event_time (event_time),
    CONSTRAINT fk_time_incident FOREIGN KEY (incident_id) REFERENCES incidents (id) ON DELETE CASCADE,
    CONSTRAINT fk_time_actor FOREIGN KEY (actor_id) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. REPORTS
CREATE TABLE reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    report_code VARCHAR(20) NOT NULL UNIQUE,
    incident_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    executive_summary TEXT NOT NULL,
    findings_summary TEXT NOT NULL,
    recommendations TEXT NOT NULL,
    conclusions TEXT NOT NULL,
    generated_by INT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_rpt_incident (incident_id),
    CONSTRAINT fk_rpt_incident FOREIGN KEY (incident_id) REFERENCES incidents (id) ON DELETE CASCADE,
    CONSTRAINT fk_rpt_generator FOREIGN KEY (generated_by) REFERENCES users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. AUDIT LOGS (Security Center)
CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    username VARCHAR(50) NULL,
    action VARCHAR(100) NOT NULL,
    target_type VARCHAR(50) NULL,
    target_id VARCHAR(50) NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent VARCHAR(255) NULL,
    outcome ENUM('SUCCESS', 'FAILURE', 'DENIED') NOT NULL DEFAULT 'SUCCESS',
    details TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_audit_time (created_at),
    INDEX idx_audit_user (user_id),
    INDEX idx_audit_action (action),
    INDEX idx_audit_outcome (outcome)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. SYSTEM SETTINGS
CREATE TABLE system_settings (
    setting_key VARCHAR(50) PRIMARY KEY,
    setting_value TEXT NOT NULL,
    description VARCHAR(255) NULL,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
