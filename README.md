# FORENSIC-X — Cybersecurity Incident Investigation & Digital Forensics Platform

**B.Tech Computer Science and Engineering (Cyber Security) Minor Project**  
**College of Engineering Kallooppara, Kerala**

---

## 1. Project Overview & Workflow

**FORENSIC-X** is an enterprise-grade Security Operations Center (SOC) incident investigation and digital forensics platform engineered for rapid incident response, cryptographic evidence preservation, indicator correlation, and defensible reporting.

### Central Investigation Workflow

```
   ┌───────────┐      ┌─────────────┐      ┌───────────┐      ┌─────────────┐      ┌──────────┐
   │  DETECT   │ ───► │ INVESTIGATE │ ───► │  ANALYZE  │ ───► │  CORRELATE  │ ───► │  REPORT  │
   └───────────┘      └─────────────┘      └───────────┘      └─────────────┘      └──────────┘
```

1. **DETECT:** Intake and triage security alerts, classify threat severity, and establish an incident case.
2. **INVESTIGATE:** Ingest digital forensic evidence, compute ingestion SHA-256 hashes, and log append-only Chain of Custody.
3. **ANALYZE:** Perform rule-based anomaly detection on security logs (brute force, web exploits, privilege escalation).
4. **CORRELATE:** Scan across all ingested text evidence to correlate Indicators of Compromise (IPs, domains, hashes, URLs).
5. **REPORT:** Compile structured forensic investigation reports with verified cryptographic evidence inventories, timeline reconstructions, and remediation recommendations.

---

## 2. Technology Stack & Architecture

- **Backend:** PHP 8.2+ (PHP 8.5 active), modular OOP structure with strict typing, PDO database layer, and hardened session management.
- **Database:** MariaDB 11.8 / MySQL 8.0 with InnoDB engine, normalized third normal form (3NF), foreign keys, indexes, and transactions.
- **Frontend:** Responsive HTML5, CSS3 with modern SOC dark visual design, Vanilla JavaScript (Fetch API), Chart.js (locally hosted offline).
- **Web Server:** Apache 2.4 with `mod_rewrite`, `mod_headers`, and virtual host configuration.
- **Storage:** Isolated evidence repository located outside the public web root (`storage/evidence/`).

---

## 3. Role-Based Access Control (RBAC) & Demonstration Accounts

The platform implements strict server-side authorization across four distinct roles:

| Role | Default Username | Default Password | Capabilities & Scope |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin` | `Admin@ForensicX2026!` | Full platform administration, user provisioning, audit inspection, system diagnostics. |
| **Investigator** | `investigator` | `Investigator@2026!` | Incident lifecycle management, evidence ingestion, hash verification, report compilation. |
| **Analyst** | `analyst` | `Analyst@2026!` | Log analysis pattern detection, IOC registration, correlation engine execution, findings promotion. |
| **Reviewer** | `reviewer` | `Reviewer@2026!` | Read-only inspection of cases, evidence metadata, timelines, and finalized reports. |

---

## 4. Key Security & Forensic Engineering Features

### A. Digital Evidence Vault & SHA-256 Integrity Verification
- **Ingestion Hashing:** When an evidence artifact is uploaded, PHP computes its cryptographic SHA-256 digest using `hash_file('sha256', ...)`.
- **Integrity Verification:** Analysts can trigger on-demand re-hashing of vault files. If any byte has been tampered with or corrupted, the platform immediately flags a `Mismatch` alert.
- **Non-Execution Boundary:** Files are stored with random identifiers in `storage/evidence/` (outside web root). Downloads are streamed with `Content-Disposition: attachment` and `X-Content-Type-Options: nosniff`.

### B. Append-Only Chain of Custody
- Every evidence interaction (`RECEIVED`, `UPLOADED`, `HASH_VERIFIED`, `VIEWED`, `DOWNLOADED`, `TRANSFERRED`) is logged in `evidence_custody_events`.
- No modification or deletion endpoints exist, ensuring an immutable custody ledger.

### C. Automated Cross-Evidence IOC Correlation Engine
- Case-insensitive regex correlation scans text evidence (`.log`, `.txt`, `.csv`, `.json`) against all registered IPv4, IPv6, Domain, URL, and SHA-256/MD5 indicators.
- Automatically captures the matched value, source file, line number, and sanitized surrounding context.

### D. Security Log Analysis Module
- Built-in rule parser detecting:
  - **Brute Force:** Automated password spraying and authentication failures.
  - **Web Exploits:** SQL Injection (`UNION SELECT`, `' OR 1=1`) and Directory Traversal (`../`).
  - **Privilege Escalation:** Unauthorized root execution (`sudo: ... COMMAND=/bin/bash`, AppArmor denials).
  - **Suspicious Shell:** Webshell probes, base64 payload execution, curl/wget exfiltration.
- Analysts can promote any flagged entry to an official incident finding with recommended defensive actions.

### E. Web Application Security (OWASP Top 10 Defenses)
- **SQL Injection:** 100% prepared SQL statements with parameter binding via PDO.
- **Cross-Site Scripting (XSS):** Contextual output escaping using `htmlspecialchars($val, ENT_QUOTES, 'UTF-8')`.
- **CSRF Defense:** Anti-CSRF cryptographically secure token validated on every state-changing POST request.
- **Session Hardening:** `session_regenerate_id(true)` upon authentication, `HttpOnly`, `SameSite=Lax`, and 30-minute inactivity timeouts.
- **Brute-Force Rate Limiting:** Account lockout after 5 consecutive failed attempts.

---

## 5. Directory Structure

```
forensic-x/
├── config/
│   ├── app.php              # Global configuration, constants, roles
│   └── database.php         # PDO database connection settings
├── database/
│   ├── schema.sql           # Complete MySQL DDL with foreign keys
│   └── seed.sql             # Rich academic demo dataset
├── public/                  # Apache Web Root (Port 8080)
│   ├── assets/
│   │   ├── css/style.css    # SOC dark theme stylesheet
│   │   ├── js/app.js        # Interactive AJAX & CSRF logic
│   │   ├── js/charts.js     # Chart.js initialization
│   │   └── vendor/          # Offline Chart.js library
│   ├── .htaccess            # URL rewrite rules & security headers
│   └── index.php            # Front controller & router
├── src/
│   ├── Auth.php             # Authentication & RBAC guard
│   ├── Audit.php            # Append-only audit logger
│   ├── CSRF.php             # CSRF token manager
│   ├── Database.php         # Singleton PDO wrapper
│   ├── Helpers/
│   │   ├── Security.php     # Defensive encoding & validation
│   │   └── View.php         # Layout renderer & badge formatters
│   └── Models/
│       ├── Incident.php     # Case file operations
│       ├── Evidence.php     # Vault & hashing operations
│       ├── ChainOfCustody.php
│       ├── IOC.php          # Indicator validation & storage
│       ├── Correlation.php  # Cross-evidence correlation
│       ├── LogAnalysis.php  # Anomaly detection engine
│       ├── Timeline.php     # Unified chronological feed
│       ├── Finding.php      # Threat findings & recommendations
│       ├── Report.php       # Report compilation & printing
│       └── User.php         # Account provisioning
├── storage/                 # Outside Public Web Root
│   ├── evidence/            # Secure evidence vault
│   ├── samples/             # Simulated demonstration logs
│   └── logs/                # System error logs
└── views/                   # Presentation Templates
```

---

## 6. Localhost Deployment & Evaluation Instructions

### A. Prerequisites
- Linux / Ubuntu / XAMPP / LAMP Stack
- PHP 8.2 or later with extensions: `pdo_mysql`, `mbstring`, `json`, `fileinfo`
- MariaDB or MySQL 8.0+

### B. Quick Setup Commands
```bash
# 1. Clone or place project in web root
cd /root/forensic-x

# 2. Database Initialization
mysql -u root -e "CREATE DATABASE IF NOT EXISTS forensic_x CHARACTER SET utf8mb4;"
mysql -u root forensic_x < database/schema.sql
mysql -u root forensic_x < database/seed.sql

# 3. Start Web Server
# Option A: Built-in PHP Development Server
php -S 127.0.0.1:8080 -t public

# Option B: Apache
# Set DocumentRoot to /root/forensic-x/public
```

### C. Accessing the Application
Open your browser and navigate to:
```
http://localhost:8080/
```
Log in using any of the four demonstration accounts listed in Section 3.
