<?php
/**
 * FORENSIC-X Application Configuration
 */

define('APP_NAME', 'FORENSIC-X');
define('APP_SUBTITLE', 'Cybersecurity Incident Investigation & Digital Forensics Platform');
define('APP_INSTITUTION', 'College of Engineering Kallooppara, Kerala');
define('APP_VERSION', '1.0.0');

// Base Paths
define('BASE_PATH', dirname(__DIR__));
define('STORAGE_PATH', BASE_PATH . '/storage');
define('EVIDENCE_STORAGE_PATH', STORAGE_PATH . '/evidence');
define('LOGS_PATH', STORAGE_PATH . '/logs');
define('SAMPLES_PATH', STORAGE_PATH . '/samples');

// Workflow Stages
define('WORKFLOW_STAGES', ['DETECT', 'INVESTIGATE', 'ANALYZE', 'CORRELATE', 'REPORT']);

// Role Definitions & Hierarchy
define('ROLE_ADMIN', 'admin');
define('ROLE_INVESTIGATOR', 'investigator');
define('ROLE_ANALYST', 'analyst');
define('ROLE_REVIEWER', 'reviewer');

// Upload limits
define('ALLOWED_EXTENSIONS', ['txt', 'log', 'csv', 'json', 'pcap', 'pcapng', 'pdf']);
define('MAX_UPLOAD_SIZE', 50 * 1024 * 1024); // 50MB

// Security Defaults
define('SESSION_LIFETIME', 1800); // 30 minutes
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME_MINUTES', 15);
