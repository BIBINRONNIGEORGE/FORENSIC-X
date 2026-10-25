-- FORENSIC-X SQLite Schema
-- Enables zero-configuration deployment on Render, Railway, Fly.io

PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    email TEXT NOT NULL UNIQUE,
    password_hash TEXT NOT NULL,
    full_name TEXT NOT NULL,
    role TEXT NOT NULL DEFAULT 'analyst',
    department TEXT DEFAULT 'SOC / Digital Forensics',
    is_active INTEGER NOT NULL DEFAULT 1,
    failed_login_attempts INTEGER NOT NULL DEFAULT 0,
    locked_until TEXT NULL,
    last_login_at TEXT NULL,
    last_login_ip TEXT NULL,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS incidents (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    incident_code TEXT NOT NULL UNIQUE,
    title TEXT NOT NULL,
    description TEXT NOT NULL,
    category TEXT NOT NULL,
    severity TEXT NOT NULL DEFAULT 'Medium',
    status TEXT NOT NULL DEFAULT 'Open',
    assigned_to INTEGER NULL,
    detected_at TEXT NOT NULL,
    source TEXT NOT NULL,
    affected_system TEXT NOT NULL,
    tags TEXT NULL,
    findings_notes TEXT NULL,
    created_by INTEGER NOT NULL,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_to) REFERENCES users (id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users (id)
);

CREATE TABLE IF NOT EXISTS evidence (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    evidence_code TEXT NOT NULL UNIQUE,
    name TEXT NOT NULL,
    description TEXT NULL,
    incident_id INTEGER NOT NULL,
    original_filename TEXT NOT NULL,
    storage_filename TEXT NOT NULL UNIQUE,
    file_size INTEGER NOT NULL,
    mime_type TEXT NOT NULL,
    file_extension TEXT NOT NULL,
    sha256_hash TEXT NOT NULL,
    last_verified_at TEXT NULL,
    last_verified_status TEXT NOT NULL DEFAULT 'Verified',
    status TEXT NOT NULL DEFAULT 'Acquired',
    tags TEXT NULL,
    acquisition_notes TEXT NULL,
    uploaded_by INTEGER NOT NULL,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (incident_id) REFERENCES incidents (id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users (id)
);

CREATE TABLE IF NOT EXISTS evidence_custody_events (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    evidence_id INTEGER NOT NULL,
    event_type TEXT NOT NULL,
    actor_id INTEGER NULL,
    actor_name TEXT NOT NULL,
    previous_state TEXT NULL,
    new_state TEXT NULL,
    notes TEXT NOT NULL,
    ip_address TEXT NULL,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (evidence_id) REFERENCES evidence (id) ON DELETE CASCADE,
    FOREIGN KEY (actor_id) REFERENCES users (id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS iocs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    ioc_code TEXT NOT NULL UNIQUE,
    indicator_type TEXT NOT NULL,
    value TEXT NOT NULL,
    description TEXT NULL,
    confidence TEXT NOT NULL DEFAULT 'Medium',
    threat_source TEXT NULL,
    first_seen TEXT NULL,
    last_seen TEXT NULL,
    incident_id INTEGER NULL,
    created_by INTEGER NOT NULL,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (incident_id) REFERENCES incidents (id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users (id)
);

CREATE TABLE IF NOT EXISTS correlation_matches (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    ioc_id INTEGER NOT NULL,
    evidence_id INTEGER NOT NULL,
    incident_id INTEGER NOT NULL,
    matched_value TEXT NOT NULL,
    line_number INTEGER NULL,
    context_snippet TEXT NOT NULL,
    matched_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ioc_id) REFERENCES iocs (id) ON DELETE CASCADE,
    FOREIGN KEY (evidence_id) REFERENCES evidence (id) ON DELETE CASCADE,
    FOREIGN KEY (incident_id) REFERENCES incidents (id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS analysis_findings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    finding_code TEXT NOT NULL UNIQUE,
    incident_id INTEGER NOT NULL,
    evidence_id INTEGER NULL,
    title TEXT NOT NULL,
    description TEXT NOT NULL,
    severity TEXT NOT NULL DEFAULT 'Medium',
    category TEXT NOT NULL,
    supporting_evidence TEXT NULL,
    analysis_notes TEXT NULL,
    recommended_action TEXT NULL,
    created_by INTEGER NOT NULL,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (incident_id) REFERENCES incidents (id) ON DELETE CASCADE,
    FOREIGN KEY (evidence_id) REFERENCES evidence (id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users (id)
);

CREATE TABLE IF NOT EXISTS investigation_timeline_events (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    incident_id INTEGER NOT NULL,
    event_category TEXT NOT NULL,
    event_title TEXT NOT NULL,
    description TEXT NOT NULL,
    event_time TEXT NOT NULL,
    is_source_log_timestamp INTEGER NOT NULL DEFAULT 0,
    actor_id INTEGER NULL,
    reference_table TEXT NULL,
    reference_id INTEGER NULL,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (incident_id) REFERENCES incidents (id) ON DELETE CASCADE,
    FOREIGN KEY (actor_id) REFERENCES users (id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS reports (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    report_code TEXT NOT NULL UNIQUE,
    incident_id INTEGER NOT NULL,
    title TEXT NOT NULL,
    executive_summary TEXT NOT NULL,
    findings_summary TEXT NOT NULL,
    recommendations TEXT NOT NULL,
    conclusions TEXT NOT NULL,
    generated_by INTEGER NOT NULL,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (incident_id) REFERENCES incidents (id) ON DELETE CASCADE,
    FOREIGN KEY (generated_by) REFERENCES users (id)
);

CREATE TABLE IF NOT EXISTS audit_logs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NULL,
    username TEXT NULL,
    action TEXT NOT NULL,
    target_type TEXT NULL,
    target_id TEXT NULL,
    ip_address TEXT NOT NULL,
    user_agent TEXT NULL,
    outcome TEXT NOT NULL DEFAULT 'SUCCESS',
    details TEXT NULL,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS system_settings (
    setting_key TEXT PRIMARY KEY,
    setting_value TEXT NOT NULL,
    description TEXT NULL,
    updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);
