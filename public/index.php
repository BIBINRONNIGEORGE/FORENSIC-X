<?php
/**
 * FORENSIC-X — Front Controller & Application Router
 * College of Engineering Kallooppara, Kerala
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';

// Class Autoloader
spl_autoload_register(function ($class) {
    $paths = [
        BASE_PATH . '/src/' . $class . '.php',
        BASE_PATH . '/src/Models/' . $class . '.php',
        BASE_PATH . '/src/Helpers/' . $class . '.php',
    ];
    foreach ($paths as $file) {
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Enforce Defense-in-Depth Security Headers
Security::sendSecurityHeaders();

// Initialize Hardened Session & Inactivity Tracker
Auth::init();

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// -----------------------------------------------------------------------------
// AUTHENTICATION ROUTES (Public)
// -----------------------------------------------------------------------------

if ($uri === '/login') {
    if (Auth::check()) {
        header('Location: /');
        exit;
    }

    if ($method === 'POST') {
        CSRF::verifyOrDie();
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        $res = Auth::attempt($username, $password);
        if ($res['success']) {
            View::setFlash('success', 'Authentication successful. Welcome to FORENSIC-X SOC Platform.');
            header('Location: /');
            exit;
        } else {
            View::setFlash('danger', $res['error']);
            header('Location: /login');
            exit;
        }
    }

    View::render('auth/login', [
        'pageTitle' => 'Authenticate Session',
    ], 'auth');
    exit;
}

if ($uri === '/logout') {
    Auth::logout();
    header('Location: /login');
    exit;
}

// All subsequent routes require active authenticated session
Auth::requireLogin();

// -----------------------------------------------------------------------------
// PROFILE & ACCOUNT SETTINGS
// -----------------------------------------------------------------------------

if ($uri === '/profile') {
    View::render('auth/profile', ['pageTitle' => 'Operator Profile']);
    exit;
}

if ($uri === '/change-password') {
    if ($method === 'POST') {
        CSRF::verifyOrDie();
        $old = $_POST['old_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if ($new !== $confirm) {
            View::setFlash('danger', 'New password and confirmation do not match.');
            header('Location: /change-password');
            exit;
        }

        $res = User::changePassword(Auth::id(), $old, $new);
        if ($res['success']) {
            View::setFlash('success', 'Your password has been securely updated.');
            header('Location: /profile');
            exit;
        } else {
            View::setFlash('danger', $res['error']);
            header('Location: /change-password');
            exit;
        }
    }

    View::render('auth/change_password', ['pageTitle' => 'Change Password']);
    exit;
}

// -----------------------------------------------------------------------------
// DASHBOARD
// -----------------------------------------------------------------------------

if ($uri === '/' || $uri === '/dashboard') {
    $incidentStats = Incident::getStats();
    $evidenceStats = Evidence::getStats();
    $iocStats      = IOC::getStats();
    $recentIncidents = Incident::getAll([], 5, 0);
    $recentTimeline  = Timeline::getAll([], 5, 0);
    
    $db = Database::getConnection();
    $recentAudits = $db->query("SELECT * FROM audit_logs ORDER BY created_at DESC LIMIT 6")->fetchAll();

    View::render('dashboard/index', [
        'pageTitle'       => 'SOC Operations Command',
        'incidentStats'   => $incidentStats,
        'evidenceStats'   => $evidenceStats,
        'iocStats'        => $iocStats,
        'recentIncidents' => $recentIncidents,
        'recentTimeline'  => $recentTimeline,
        'recentAudits'    => $recentAudits,
    ]);
    exit;
}

// -----------------------------------------------------------------------------
// INCIDENTS MODULE
// -----------------------------------------------------------------------------

if ($uri === '/incidents') {
    $filters = [
        'status'   => $_GET['status'] ?? null,
        'severity' => $_GET['severity'] ?? null,
        'category' => $_GET['category'] ?? null,
        'search'   => trim($_GET['search'] ?? ''),
    ];
    $incidents = Incident::getAll($filters, 100);
    $stats = Incident::getStats();

    View::render('incidents/index', [
        'pageTitle' => 'Incident Management',
        'incidents' => $incidents,
        'filters'   => $filters,
        'stats'     => $stats,
    ]);
    exit;
}

if ($uri === '/incidents/create') {
    Auth::requirePermission('create_incident');

    if ($method === 'POST') {
        CSRF::verifyOrDie();
        $data = [
            'title'           => trim($_POST['title'] ?? ''),
            'category'        => $_POST['category'] ?? 'Malware Infection',
            'severity'        => $_POST['severity'] ?? 'Medium',
            'status'          => $_POST['status'] ?? 'Open',
            'assigned_to'     => $_POST['assigned_to'] ?? null,
            'affected_system' => trim($_POST['affected_system'] ?? ''),
            'source'          => trim($_POST['source'] ?? ''),
            'detected_at'     => !empty($_POST['detected_at']) ? str_replace('T', ' ', $_POST['detected_at']) . ':00' : date('Y-m-d H:i:s'),
            'tags'            => trim($_POST['tags'] ?? ''),
            'description'     => trim($_POST['description'] ?? ''),
            'findings_notes'  => trim($_POST['findings_notes'] ?? ''),
        ];

        if (empty($data['title']) || empty($data['affected_system']) || empty($data['description'])) {
            View::setFlash('danger', 'Please complete all required fields.');
            header('Location: /incidents/create');
            exit;
        }

        $id = Incident::create($data);
        View::setFlash('success', 'Incident record created successfully.');
        header("Location: /incidents/view?id={$id}");
        exit;
    }

    $investigators = User::getInvestigators();
    View::render('incidents/create', [
        'pageTitle'     => 'Create Incident Case',
        'investigators' => $investigators,
    ]);
    exit;
}

if ($uri === '/incidents/view') {
    $id = (int)($_GET['id'] ?? 0);
    $incident = Incident::getById($id);
    if (!$incident) {
        http_response_code(404);
        View::render('errors/404', [], 'none');
        exit;
    }

    $evidence = Evidence::getByIncident($id);
    $iocs = IOC::getAll(['incident_id' => $id]);
    $findings = Finding::getByIncident($id);
    $timeline = Timeline::getIncidentTimeline($id);

    View::render('incidents/view', [
        'pageTitle' => "Case File: {$incident['incident_code']}",
        'incident'  => $incident,
        'evidence'  => $evidence,
        'iocs'      => $iocs,
        'findings'  => $findings,
        'timeline'  => $timeline,
    ]);
    exit;
}

if ($uri === '/incidents/edit') {
    Auth::requirePermission('edit_incident');
    $id = (int)($_GET['id'] ?? 0);
    $incident = Incident::getById($id);
    if (!$incident) {
        http_response_code(404);
        View::render('errors/404', [], 'none');
        exit;
    }

    if ($method === 'POST') {
        CSRF::verifyOrDie();
        $data = [
            'title'           => trim($_POST['title'] ?? ''),
            'category'        => $_POST['category'] ?? $incident['category'],
            'severity'        => $_POST['severity'] ?? $incident['severity'],
            'status'          => $_POST['status'] ?? $incident['status'],
            'assigned_to'     => $_POST['assigned_to'] ?? null,
            'affected_system' => trim($_POST['affected_system'] ?? ''),
            'source'          => trim($_POST['source'] ?? ''),
            'tags'            => trim($_POST['tags'] ?? ''),
            'description'     => trim($_POST['description'] ?? ''),
            'findings_notes'  => trim($_POST['findings_notes'] ?? ''),
        ];

        Incident::update($id, $data);
        View::setFlash('success', 'Incident updated successfully.');
        header("Location: /incidents/view?id={$id}");
        exit;
    }

    $investigators = User::getInvestigators();
    View::render('incidents/edit', [
        'pageTitle'     => "Edit {$incident['incident_code']}",
        'incident'      => $incident,
        'investigators' => $investigators,
    ]);
    exit;
}

// -----------------------------------------------------------------------------
// EVIDENCE VAULT MODULE
// -----------------------------------------------------------------------------

if ($uri === '/evidence') {
    $filters = [
        'verified_status' => $_GET['verified_status'] ?? null,
        'search'          => trim($_GET['search'] ?? ''),
    ];
    $evidence = Evidence::getAll($filters, 100);
    $stats    = Evidence::getStats();
    $firstCustody = !empty($evidence) ? ChainOfCustody::getByEvidenceId((int)$evidence[0]['id']) : [];

    View::render('evidence/index', [
        'pageTitle'    => 'Evidence Vault',
        'evidence'     => $evidence,
        'stats'        => $stats,
        'filters'      => $filters,
        'firstCustody' => $firstCustody,
    ]);
    exit;
}

if ($uri === '/evidence/upload') {
    Auth::requirePermission('upload_evidence');

    if ($method === 'POST') {
        CSRF::verifyOrDie();
        $incidentId = (int)($_POST['incident_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');

        if (!$incidentId || empty($name) || empty($_FILES['evidence_file'])) {
            View::setFlash('danger', 'Please provide an associated incident, evidence name, and file.');
            header('Location: /evidence/upload');
            exit;
        }

        $res = Evidence::create([
            'incident_id'       => $incidentId,
            'name'              => $name,
            'description'       => trim($_POST['description'] ?? ''),
            'acquisition_notes' => trim($_POST['acquisition_notes'] ?? ''),
            'tags'              => trim($_POST['tags'] ?? ''),
        ], $_FILES['evidence_file']);

        if ($res['success']) {
            View::setFlash('success', "Evidence '{$res['code']}' securely ingested into vault. SHA-256 computed.");
            header("Location: /evidence/view?id={$res['evidence_id']}");
            exit;
        } else {
            View::setFlash('danger', $res['error']);
            header('Location: /evidence/upload');
            exit;
        }
    }

    $incidents = Incident::getAll([], 100);
    View::render('evidence/upload', [
        'pageTitle'          => 'Secure Evidence Ingestion',
        'incidents'          => $incidents,
        'selectedIncidentId' => $_GET['incident_id'] ?? null,
    ]);
    exit;
}

if ($uri === '/evidence/view') {
    $id = (int)($_GET['id'] ?? 0);
    $evidence = Evidence::getById($id);
    if (!$evidence) {
        http_response_code(404);
        View::render('errors/404', [], 'none');
        exit;
    }

    $custodyEvents = ChainOfCustody::getByEvidenceId($id);

    View::render('evidence/view', [
        'pageTitle'     => "Artifact: {$evidence['evidence_code']}",
        'evidence'      => $evidence,
        'custodyEvents' => $custodyEvents,
    ]);
    exit;
}

if ($uri === '/evidence/verify') {
    Auth::requirePermission('verify_evidence');
    $id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
    $result = Evidence::verifyHash($id);

    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(array_merge(['success' => true], $result));
        exit;
    }

    View::setFlash($result['status'] === 'Verified' ? 'success' : 'danger', $result['message']);
    header("Location: /evidence/view?id={$id}");
    exit;
}

if ($uri === '/evidence/download') {
    $id = (int)($_GET['id'] ?? 0);
    $evidence = Evidence::getById($id);
    if (!$evidence) {
        http_response_code(404);
        die("Evidence record not found.");
    }

    $filePath = Evidence::getStoragePath($evidence['storage_filename']);
    if (!file_exists($filePath)) {
        http_response_code(404);
        die("Secured evidence file is missing from vault storage.");
    }

    // Log Chain of Custody & Audit
    ChainOfCustody::logEvent($id, 'DOWNLOADED', 'Evidence file downloaded for forensic inspection.');
    Audit::log('DOWNLOAD_EVIDENCE', 'evidence', $evidence['evidence_code'], 'SUCCESS', "Downloaded file: {$evidence['original_filename']}");

    // Send file stream with strict security headers
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . rawurlencode($evidence['original_filename']) . '"');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filePath));
    readfile($filePath);
    exit;
}

// -----------------------------------------------------------------------------
// IOC MANAGEMENT & CORRELATION
// -----------------------------------------------------------------------------

if ($uri === '/ioc') {
    $filters = [
        'type'       => $_GET['type'] ?? null,
        'confidence' => $_GET['confidence'] ?? null,
        'search'     => trim($_GET['search'] ?? ''),
    ];
    $iocs = IOC::getAll($filters, 100);
    $stats = IOC::getStats();
    $matches = Correlation::getAllMatches(10);
    $db = Database::getConnection();
    $findings = $db->query("SELECT f.*, i.incident_code FROM analysis_findings f LEFT JOIN incidents i ON f.incident_id = i.id ORDER BY f.created_at DESC LIMIT 5")->fetchAll();
    $logsCount = (int)$db->query("SELECT COUNT(*) FROM evidence WHERE mime_type IN ('text/plain', 'application/json', 'text/csv') OR file_extension IN ('log', 'txt', 'json', 'csv') OR original_filename LIKE '%.log'")->fetchColumn();

    View::render('ioc/index', [
        'pageTitle' => 'IOC & Log Analysis',
        'iocs'      => $iocs,
        'stats'     => $stats,
        'matches'   => $matches,
        'findings'  => $findings,
        'logsCount' => max(1, $logsCount),
        'filters'   => $filters,
    ]);
    exit;
}

if ($uri === '/ioc/create') {
    Auth::requirePermission('manage_iocs');

    if ($method === 'POST') {
        CSRF::verifyOrDie();
        $res = IOC::create([
            'indicator_type' => $_POST['indicator_type'] ?? 'IPv4',
            'value'          => $_POST['value'] ?? '',
            'confidence'     => $_POST['confidence'] ?? 'Medium',
            'threat_source'  => trim($_POST['threat_source'] ?? ''),
            'description'    => trim($_POST['description'] ?? ''),
            'incident_id'    => $_POST['incident_id'] ?? null,
        ]);

        if ($res['success']) {
            View::setFlash('success', "Indicator '{$res['code']}' added to IOC repository.");
            header('Location: /ioc');
            exit;
        } else {
            View::setFlash('danger', $res['error']);
            header('Location: /ioc/create');
            exit;
        }
    }

    $incidents = Incident::getAll([], 100);
    View::render('ioc/create', [
        'pageTitle'          => 'Register IOC',
        'incidents'          => $incidents,
        'selectedIncidentId' => $_GET['incident_id'] ?? null,
    ]);
    exit;
}

if ($uri === '/ioc/correlate') {
    $matches = Correlation::getAllMatches(100);
    View::render('ioc/correlate', [
        'pageTitle' => 'IOC Correlation Engine',
        'matches'   => $matches,
    ]);
    exit;
}

if ($uri === '/ioc/correlate/run') {
    Auth::requirePermission('run_correlation');
    CSRF::verifyOrDie();
    $result = Correlation::runCorrelation();

    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }

    View::setFlash('success', "Correlation run completed. Processed {$result['files_processed']} evidence files, identified {$result['new_matches']} new match(es).");
    header('Location: /ioc/correlate');
    exit;
}

// -----------------------------------------------------------------------------
// LOG ANALYSIS & FINDINGS
// -----------------------------------------------------------------------------

if ($uri === '/logs') {
    Auth::requirePermission('run_log_analysis');
    $evidenceList = Evidence::getAll([], 100);
    $selectedEvidenceId = (int)($_GET['evidence_id'] ?? 0);
    $ruleFilter = $_GET['rule_filter'] ?? 'ALL';

    $selectedEvidence = null;
    $analysisResults = null;

    if ($selectedEvidenceId) {
        $selectedEvidence = Evidence::getById($selectedEvidenceId);
        if ($selectedEvidence) {
            $filePath = Evidence::getStoragePath($selectedEvidence['storage_filename']);
            $analysisResults = LogAnalysis::analyzeFile($filePath, $ruleFilter);
            Audit::log('RUN_LOG_ANALYSIS', 'evidence', $selectedEvidence['evidence_code'], 'SUCCESS', "Analyzed log: {$selectedEvidence['original_filename']}");
        }
    }

    View::render('logs/index', [
        'pageTitle'          => 'Log Analysis & Pattern Triage',
        'evidenceList'       => $evidenceList,
        'selectedEvidenceId' => $selectedEvidenceId,
        'selectedEvidence'   => $selectedEvidence,
        'analysisResults'    => $analysisResults,
        'ruleFilter'         => $ruleFilter,
    ]);
    exit;
}

if ($uri === '/findings/create') {
    Auth::requirePermission('create_finding');
    if ($method === 'POST') {
        CSRF::verifyOrDie();
        $res = Finding::create([
            'incident_id'         => $_POST['incident_id'] ?? 0,
            'evidence_id'         => $_POST['evidence_id'] ?? null,
            'title'               => trim($_POST['title'] ?? ''),
            'description'         => trim($_POST['description'] ?? ''),
            'severity'            => $_POST['severity'] ?? 'Medium',
            'category'            => trim($_POST['category'] ?? 'Security Anomaly'),
            'supporting_evidence' => trim($_POST['supporting_evidence'] ?? ''),
            'recommended_action'  => trim($_POST['recommended_action'] ?? ''),
        ]);

        if ($res['success']) {
            View::setFlash('success', "Finding '{$res['code']}' attached to incident case.");
            header("Location: /incidents/view?id=" . (int)$_POST['incident_id']);
            exit;
        } else {
            View::setFlash('danger', 'Failed to create finding record.');
            header('Location: /logs');
            exit;
        }
    }
}

// -----------------------------------------------------------------------------
// TIMELINE
// -----------------------------------------------------------------------------

if ($uri === '/timeline') {
    $filters = [
        'incident_id' => $_GET['incident_id'] ?? null,
        'category'    => $_GET['category'] ?? null,
    ];
    $events = Timeline::getAll($filters, 100);
    $incidents = Incident::getAll([], 100);
    
    $selectedIncident = null;
    if (!empty($filters['incident_id'])) {
        $selectedIncident = Incident::getById((int)$filters['incident_id']);
    } elseif (!empty($incidents)) {
        $selectedIncident = $incidents[0];
    }

    View::render('timeline/index', [
        'pageTitle'        => 'Investigation Timeline',
        'events'           => $events,
        'incidents'        => $incidents,
        'selectedIncident' => $selectedIncident,
        'filters'          => $filters,
    ]);
    exit;
}

// -----------------------------------------------------------------------------
// REPORTS MODULE
// -----------------------------------------------------------------------------

if ($uri === '/reports') {
    $search = trim($_GET['search'] ?? '');
    $reports = Report::getAll();
    if (!empty($search)) {
        $reports = array_filter($reports, function($r) use ($search) {
            return stripos($r['title'], $search) !== false 
                || stripos($r['report_code'], $search) !== false 
                || stripos($r['incident_code'] ?? '', $search) !== false;
        });
    }

    $db = Database::getConnection();
    $recentAudits = $db->query("SELECT * FROM audit_logs ORDER BY created_at DESC LIMIT 6")->fetchAll();
    $auditCount = (int)$db->query("SELECT COUNT(*) FROM audit_logs")->fetchColumn();

    View::render('reports/index', [
        'pageTitle'    => 'Reports & Audit Center',
        'reports'      => $reports,
        'recentAudits' => $recentAudits,
        'auditCount'   => $auditCount,
        'search'       => $search,
    ]);
    exit;
}

if ($uri === '/reports/generate') {
    Auth::requirePermission('generate_report');

    if ($method === 'POST') {
        CSRF::verifyOrDie();
        $incidentId = (int)($_POST['incident_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');

        if (!$incidentId || empty($title)) {
            View::setFlash('danger', 'Please choose an incident case and provide a report title.');
            header('Location: /reports/generate');
            exit;
        }

        $res = Report::create([
            'incident_id'        => $incidentId,
            'title'              => $title,
            'executive_summary'  => trim($_POST['executive_summary'] ?? ''),
            'findings_summary'   => trim($_POST['findings_summary'] ?? ''),
            'recommendations'    => trim($_POST['recommendations'] ?? ''),
            'conclusions'        => trim($_POST['conclusions'] ?? ''),
        ]);

        if ($res['success']) {
            View::setFlash('success', "Report '{$res['code']}' compiled successfully.");
            header("Location: /reports/view?id={$res['report_id']}");
            exit;
        } else {
            View::setFlash('danger', 'Failed to generate report.');
            header('Location: /reports/generate');
            exit;
        }
    }

    $incidents = Incident::getAll([], 100);
    View::render('reports/generate', [
        'pageTitle'          => 'Generate Case Report',
        'incidents'          => $incidents,
        'selectedIncidentId' => $_GET['incident_id'] ?? null,
    ]);
    exit;
}

if ($uri === '/reports/view') {
    $id = (int)($_GET['id'] ?? 0);
    $report = Report::getById($id);
    if (!$report) {
        http_response_code(404);
        View::render('errors/404', [], 'none');
        exit;
    }

    $compiledData = Report::compileDataForIncident((int)$report['incident_id']);

    View::render('reports/view', [
        'pageTitle'     => "Report: {$report['report_code']}",
        'report'        => $report,
        'compiledData'  => $compiledData,
    ]);
    exit;
}

// -----------------------------------------------------------------------------
// AUDIT LOGS
// -----------------------------------------------------------------------------

if ($uri === '/audit') {
    $db = Database::getConnection();
    $sql = "SELECT * FROM audit_logs WHERE 1=1";
    $params = [];

    $filters = [
        'outcome'  => $_GET['outcome'] ?? '',
        'action'   => trim($_GET['action'] ?? ''),
        'username' => trim($_GET['username'] ?? ''),
    ];

    if (!empty($filters['outcome'])) {
        $sql .= " AND outcome = :outcome";
        $params[':outcome'] = $filters['outcome'];
    }
    if (!empty($filters['action'])) {
        $sql .= " AND action LIKE :action";
        $params[':action'] = '%' . $filters['action'] . '%';
    }
    if (!empty($filters['username'])) {
        $sql .= " AND username LIKE :username";
        $params[':username'] = '%' . $filters['username'] . '%';
    }

    $sql .= " ORDER BY created_at DESC LIMIT 100";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $logs = $stmt->fetchAll();

    View::render('audit/index', [
        'pageTitle' => 'Security & Audit Center',
        'logs'      => $logs,
        'filters'   => $filters,
    ]);
    exit;
}

// -----------------------------------------------------------------------------
// USER MANAGEMENT (Admin Only)
// -----------------------------------------------------------------------------

if ($uri === '/users') {
    Auth::requireRole(ROLE_ADMIN);
    $users = User::getAll();
    View::render('users/index', [
        'pageTitle' => 'User Management',
        'users'     => $users,
    ]);
    exit;
}

if ($uri === '/users/create') {
    Auth::requireRole(ROLE_ADMIN);

    if ($method === 'POST') {
        CSRF::verifyOrDie();
        $res = User::create([
            'username'   => trim($_POST['username'] ?? ''),
            'email'      => trim($_POST['email'] ?? ''),
            'full_name'  => trim($_POST['full_name'] ?? ''),
            'role'       => $_POST['role'] ?? 'analyst',
            'department' => trim($_POST['department'] ?? 'Digital Forensics Unit'),
            'password'   => $_POST['password'] ?? '',
        ]);

        if ($res['success']) {
            View::setFlash('success', 'User account successfully provisioned.');
            header('Location: /users');
            exit;
        } else {
            View::setFlash('danger', $res['error']);
            header('Location: /users/create');
            exit;
        }
    }

    View::render('users/create', ['pageTitle' => 'Provision User']);
    exit;
}

if ($uri === '/users/toggle-status') {
    Auth::requireRole(ROLE_ADMIN);
    CSRF::verifyOrDie();
    $userId = (int)($_POST['user_id'] ?? 0);
    $newStatus = (int)($_POST['status'] ?? 0);

    if ($userId !== Auth::id()) {
        User::toggleActive($userId, $newStatus);
        View::setFlash('success', 'User status updated successfully.');
    } else {
        View::setFlash('warning', 'You cannot alter the status of your active session.');
    }
    header('Location: /users');
    exit;
}

// -----------------------------------------------------------------------------
// SETTINGS
// -----------------------------------------------------------------------------

if ($uri === '/settings') {
    View::render('settings/index', ['pageTitle' => 'System Diagnostics & Settings']);
    exit;
}

// 404 Catch-All
http_response_code(404);
View::render('errors/404', [], 'none');
exit;
