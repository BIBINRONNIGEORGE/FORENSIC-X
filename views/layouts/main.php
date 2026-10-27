<?php
/**
 * FORENSIC-X Main SOC Application Layout
 * Designed to match Figma Source of Truth (ROORaWXDtD6bCWfH7NbtGO)
 */
$user = Auth::user();
$currentRoute = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$flash = $flash ?? View::getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= Security::e(CSRF::getToken()) ?>">
    <title><?= isset($pageTitle) ? Security::e($pageTitle) . ' — ' : '' ?><?= APP_NAME ?> • Digital Forensics</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="/assets/css/style.css">
    
    <!-- Scripts -->
    <script src="/assets/vendor/chart.min.js"></script>
    <script src="/assets/js/charts.js"></script>
    <script src="/assets/js/app.js" defer></script>
</head>
<body>
    <div class="app-container">
        <!-- -----------------------------------------------------------------
             FIGMA LEFT SIDEBAR (248px width, dark navy #081825)
             ----------------------------------------------------------------- -->
        <aside class="sidebar" id="sidebar">
            <!-- Brand Header -->
            <div class="sidebar-brand">
                <a href="/" class="brand-link">
                    <div class="brand-shield-logo">
                        <svg width="28" height="34" viewBox="0 0 28 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14 1L2 5.5V15.5C2 23.5 7.5 30 14 33C20.5 30 26 23.5 26 15.5V5.5L14 1Z" stroke="#21d4f3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M14 8V16M14 20H14.02M9 13.5C9 10.74 11.24 8.5 14 8.5C16.76 8.5 19 10.74 19 13.5V17" stroke="#21d4f3" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <div class="brand-text">
                        <span class="brand-wordmark"><?= APP_NAME ?></span>
                        <span class="brand-subtitle">DIGITAL FORENSICS</span>
                    </div>
                </a>
            </div>

            <!-- Navigation Links -->
            <nav class="sidebar-nav">
                <div class="nav-group-title">WORKSPACE</div>

                <!-- 1. Dashboard -->
                <a href="/" class="nav-item <?= $currentRoute === '/' || $currentRoute === '/dashboard' ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>
                    <span>Dashboard</span>
                </a>

                <!-- 2. Incidents -->
                <a href="/incidents" class="nav-item <?= str_starts_with($currentRoute, '/incidents') ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    <span>Incidents</span>
                </a>

                <!-- 3. Evidence Vault -->
                <a href="/evidence" class="nav-item <?= str_starts_with($currentRoute, '/evidence') ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                    <span>Evidence Vault</span>
                </a>

                <!-- 4. IOC Management -->
                <a href="/ioc" class="nav-item <?= $currentRoute === '/ioc' || str_starts_with($currentRoute, '/ioc/create') ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    <span>IOC Management</span>
                </a>

                <!-- 5. IOC Correlation -->
                <a href="/ioc/correlate" class="nav-item <?= str_starts_with($currentRoute, '/ioc/correlate') ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                    <span>IOC Correlation</span>
                </a>

                <!-- 6. Log Analysis -->
                <a href="/logs" class="nav-item <?= str_starts_with($currentRoute, '/logs') ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    <span>Log Analysis</span>
                </a>

                <!-- 7. Investigation Timeline -->
                <a href="/timeline" class="nav-item <?= str_starts_with($currentRoute, '/timeline') ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span>Investigation Timeline</span>
                </a>

                <!-- 8. Reports -->
                <a href="/reports" class="nav-item <?= str_starts_with($currentRoute, '/reports') ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                    <span>Reports</span>
                </a>

                <!-- 9. Audit Center -->
                <a href="/audit" class="nav-item <?= str_starts_with($currentRoute, '/audit') ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
                    <span>Audit Center</span>
                </a>

                <div class="nav-group-title" style="margin-top: 0.5rem;">ADMINISTRATION</div>

                <?php if (Auth::hasRole(ROLE_ADMIN)): ?>
                <!-- 10. User Management (Admin) -->
                <a href="/users" class="nav-item <?= str_starts_with($currentRoute, '/users') ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    <span>User Management</span>
                </a>
                <?php endif; ?>

                <!-- 11. Settings -->
                <a href="/settings" class="nav-item <?= str_starts_with($currentRoute, '/settings') ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                    <span>Settings</span>
                </a>
            </nav>

            <!-- Bottom User Card -->
            <div class="sidebar-user-footer">
                <div class="sidebar-user-card">
                    <div class="sidebar-user-avatar">
                        <?= strtoupper(substr($user['username'] ?? 'FX', 0, 2)) ?>
                    </div>
                    <div class="sidebar-user-info">
                        <div class="sidebar-user-name"><?= Security::e($user['full_name'] ?? 'Security Team') ?></div>
                        <div class="sidebar-user-role"><?= Security::e($user['role'] ?? 'SOC Workspace') ?></div>
                    </div>
                    <a href="/logout" class="sidebar-logout-btn" title="Sign out of platform">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    </a>
                </div>
            </div>
        </aside>

        <!-- -----------------------------------------------------------------
             MAIN WORKSPACE
             ----------------------------------------------------------------- -->
        <div class="main-wrapper">
            <!-- TOPBAR (Matching Figma Top Navigation) -->
            <header class="topbar">
                <div class="topbar-context">
                    <button class="topbar-mobile-toggle" id="mobileMenuToggle" aria-label="Toggle navigation menu">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                    </button>
                    <span class="topbar-crumb-tag">FORENSIC-X &bull; INVESTIGATION PLATFORM</span>
                    <div class="system-status-indicator">
                        <span class="system-status-dot"></span>
                        <span>SYSTEM ONLINE</span>
                    </div>
                </div>

                <div class="topbar-actions">
                    <!-- Global Search -->
                    <form action="/incidents" method="GET" class="topbar-search-form">
                        <svg class="topbar-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input type="text" name="search" class="topbar-search-input" placeholder="Search incidents, hashes, IOCs..." autocomplete="off">
                    </form>

                    <!-- Notifications Button -->
                    <a href="/audit" class="topbar-icon-btn" title="Security & Audit Notifications">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                    </a>

                    <!-- Profile Control -->
                    <a href="/profile" class="topbar-profile-badge">
                        <div class="topbar-avatar">
                            <?= strtoupper(substr($user['username'] ?? 'U', 0, 1)) ?>
                        </div>
                        <div>
                            <div class="topbar-meta-name"><?= Security::e($user['username'] ?? 'User') ?></div>
                            <div class="topbar-meta-role"><?= strtoupper(Security::e($user['role'] ?? '')) ?></div>
                        </div>
                    </a>
                </div>
            </header>

            <!-- MAIN INNER CONTENT -->
            <main class="content-body">
                <!-- Flash Alert Messages -->
                <?php if ($flash): ?>
                <div class="alert alert-<?= Security::e($flash['type'] === 'error' ? 'danger' : $flash['type']) ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <div><?= Security::e($flash['message']) ?></div>
                </div>
                <?php endif; ?>

                <?= $content ?>

                <!-- Unified Figma Design Footer -->
                <footer class="app-footer">
                    FORENSIC-X &bull; Authorized Investigation Workspace &bull; College of Engineering Kallooppara Minor Project
                </footer>
            </main>
        </div>
    </div>

    <!-- Mobile Drawer Script & Backdrop -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggle = document.getElementById('mobileMenuToggle');
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        
        function toggleSidebar() {
            const isOpen = sidebar.classList.toggle('open');
            if (backdrop) backdrop.classList.toggle('active', isOpen);
        }

        function closeSidebar() {
            sidebar.classList.remove('open');
            if (backdrop) backdrop.classList.remove('active');
        }

        if (toggle && sidebar) {
            toggle.addEventListener('click', toggleSidebar);
            if (backdrop) backdrop.addEventListener('click', closeSidebar);
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && sidebar.classList.contains('open')) {
                    closeSidebar();
                }
            });
        }
    });
    </script>
</body>
</html>
