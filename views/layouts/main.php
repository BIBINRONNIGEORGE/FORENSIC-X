<?php
/**
 * FORENSIC-X Main SOC Application Layout
 */
$user = Auth::user();
$currentRoute = $_SERVER['REQUEST_URI'] ?? '/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= Security::e(CSRF::getToken()) ?>">
    <title><?= isset($pageTitle) ? Security::e($pageTitle) . ' — ' : '' ?><?= APP_NAME ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <script src="/assets/vendor/chart.min.js"></script>
    <script src="/assets/js/charts.js"></script>
    <script src="/assets/js/app.js" defer></script>
</head>
<body>
    <div class="app-container">
        <!-- FIXED LEFT SIDEBAR (12 Items) -->
        <aside class="sidebar">
            <div class="sidebar-brand">
                <a href="/" class="brand-logo">
                    <div class="brand-icon">
                        <!-- Shield & Fingerprint SVG -->
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            <path d="M12 8v4"/>
                            <path d="M12 16h.01"/>
                        </svg>
                    </div>
                    <div>
                        <div class="brand-title"><?= APP_NAME ?></div>
                        <span class="brand-subtitle">SOC Forensics</span>
                    </div>
                </a>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section-title">Core Operations</div>
                
                <!-- 1. Dashboard -->
                <a href="/" class="nav-item <?= $currentRoute === '/' || $currentRoute === '/dashboard' ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    Dashboard
                </a>

                <!-- 2. Incidents -->
                <a href="/incidents" class="nav-item <?= str_starts_with($currentRoute, '/incidents') ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    Incidents
                </a>

                <!-- 3. Evidence Vault -->
                <a href="/evidence" class="nav-item <?= str_starts_with($currentRoute, '/evidence') ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                    Evidence Vault
                </a>

                <div class="nav-section-title">Analysis & Correlation</div>

                <!-- 4. IOC Management -->
                <a href="/ioc" class="nav-item <?= $currentRoute === '/ioc' || str_starts_with($currentRoute, '/ioc/create') ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    IOC Management
                </a>

                <!-- 5. IOC Correlation -->
                <a href="/ioc/correlate" class="nav-item <?= str_starts_with($currentRoute, '/ioc/correlate') ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                    IOC Correlation
                </a>

                <!-- 6. Log Analysis -->
                <a href="/logs" class="nav-item <?= str_starts_with($currentRoute, '/logs') ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    Log Analysis
                </a>

                <!-- 7. Investigation Timeline -->
                <a href="/timeline" class="nav-item <?= str_starts_with($currentRoute, '/timeline') ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Timeline
                </a>

                <div class="nav-section-title">Reporting & Governance</div>

                <!-- 8. Reports -->
                <a href="/reports" class="nav-item <?= str_starts_with($currentRoute, '/reports') ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                    Reports
                </a>

                <!-- 9. Security & Audit Logs -->
                <a href="/audit" class="nav-item <?= str_starts_with($currentRoute, '/audit') ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    Security & Audit
                </a>

                <?php if (Auth::hasRole(ROLE_ADMIN)): ?>
                <!-- 10. User Management (Admin Only) -->
                <a href="/users" class="nav-item <?= str_starts_with($currentRoute, '/users') ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    User Management
                </a>
                <?php endif; ?>

                <!-- 11. Settings -->
                <a href="/settings" class="nav-item <?= str_starts_with($currentRoute, '/settings') ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                    Settings
                </a>

                <!-- 12. Logout -->
                <a href="/logout" class="nav-item" style="color: #EF4444;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Logout
                </a>
            </nav>

            <div class="sidebar-footer">
                <div>CE Kallooppara — Minor Project</div>
                <div style="font-size: 0.68rem; margin-top: 0.2rem; color: var(--cyan-primary);">v<?= APP_VERSION ?></div>
            </div>
        </aside>

        <!-- MAIN WORKSPACE -->
        <div class="main-wrapper">
            <!-- TOP NAVBAR -->
            <header class="topbar">
                <div class="topbar-left">
                    <h1 class="page-title"><?= isset($pageTitle) ? Security::e($pageTitle) : 'SOC Command' ?></h1>
                    
                    <!-- Investigation Workflow Indicator -->
                    <div class="workflow-badge">
                        <span>Workflow:</span>
                        <span class="workflow-stage">DETECT</span> → 
                        <span class="workflow-stage">INVESTIGATE</span> → 
                        <span class="workflow-stage">ANALYZE</span> → 
                        <span class="workflow-stage">CORRELATE</span> → 
                        <span class="workflow-stage">REPORT</span>
                    </div>
                </div>

                <div class="topbar-right">
                    <?php if (Auth::can('create_incident')): ?>
                    <a href="/incidents/create" class="btn btn-primary btn-sm">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        New Incident
                    </a>
                    <?php endif; ?>

                    <?php if (Auth::can('upload_evidence')): ?>
                    <a href="/evidence/upload" class="btn btn-secondary btn-sm">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        Upload Evidence
                    </a>
                    <?php endif; ?>

                    <!-- User Profile Dropdown / Link -->
                    <a href="/profile" class="user-profile-badge">
                        <div class="user-avatar">
                            <?= strtoupper(substr($user['username'] ?? 'U', 0, 1)) ?>
                        </div>
                        <div>
                            <div class="user-meta-name"><?= Security::e($user['full_name'] ?? 'User') ?></div>
                            <div class="user-meta-role"><?= strtoupper(Security::e($user['role'] ?? '')) ?></div>
                        </div>
                    </a>
                </div>
            </header>

            <!-- INNER CONTENT BODY -->
            <main class="content-body">
                <!-- Flash Alert Messages -->
                <?php if ($flash): ?>
                <div class="alert alert-<?= Security::e($flash['type']) ?>">
                    <div><?= Security::e($flash['message']) ?></div>
                </div>
                <?php endif; ?>

                <?= $content ?>
            </main>
        </div>
    </div>
</body>
</html>
