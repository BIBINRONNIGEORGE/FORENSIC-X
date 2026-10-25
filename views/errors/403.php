<?php
/**
 * FORENSIC-X 403 Forbidden Error View
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>403 Access Denied — <?= APP_NAME ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body style="display: flex; align-items: center; justify-content: center; min-height: 100vh;">
    <div style="text-align: center; max-width: 500px; padding: 2.5rem; background-color: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg);">
        <div style="width: 50px; height: 50px; margin: 0 auto 1.25rem; background: rgba(239, 68, 68, 0.15); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #EF4444;">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        </div>
        <h1 style="font-size: 1.8rem; font-weight: 800; color: #EF4444; margin-bottom: 0.5rem;">403 Forbidden</h1>
        <div style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 1.5rem;">
            Security Policy Violation: You do not have the required role permissions or your request failed CSRF validation. This security event has been recorded in the audit log.
        </div>
        <a href="/" class="btn btn-primary">Return to Dashboard</a>
    </div>
</body>
</html>
