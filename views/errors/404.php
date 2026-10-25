<?php
/**
 * FORENSIC-X 404 Not Found View
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>404 Not Found — <?= APP_NAME ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body style="display: flex; align-items: center; justify-content: center; min-height: 100vh;">
    <div style="text-align: center; max-width: 500px; padding: 2.5rem; background-color: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg);">
        <h1 style="font-size: 2.5rem; font-weight: 800; color: var(--cyan-primary); margin-bottom: 0.5rem;">404</h1>
        <div style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">Resource Not Found</div>
        <div style="font-size: 0.88rem; color: var(--text-secondary); margin-bottom: 1.5rem;">
            The requested incident case, evidence record, or endpoint does not exist.
        </div>
        <a href="/" class="btn btn-primary">Return to Dashboard</a>
    </div>
</body>
</html>
