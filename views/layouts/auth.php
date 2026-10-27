<?php
/**
 * FORENSIC-X Authentication Layout
 * Designed to match Figma Brand Identity (ROORaWXDtD6bCWfH7NbtGO)
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? Security::e($pageTitle) . ' — ' : '' ?><?= APP_NAME ?> • Secure Login</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at 50% 15%, #0d2538 0%, #06111c 80%);
            min-height: 100vh;
            padding: 1.5rem;
        }
        .auth-container {
            width: 100%;
            max-width: 440px;
        }
        .auth-card {
            background-color: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-xl);
            padding: 2.5rem 2.25rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.6);
            position: relative;
            overflow: hidden;
        }
        .auth-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #186a87, var(--cyan-primary), #165066);
        }
        .auth-brand {
            text-align: center;
            margin-bottom: 2rem;
        }
        .auth-shield-icon {
            width: 52px;
            height: 58px;
            margin: 0 auto 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(33, 212, 243, 0.08);
            border: 1px solid rgba(33, 212, 243, 0.25);
            border-radius: var(--radius-lg);
            box-shadow: 0 0 20px rgba(33, 212, 243, 0.15);
        }
        .auth-title {
            font-size: 1.45rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            color: var(--text-primary);
        }
        .auth-subtitle {
            font-size: 0.72rem;
            color: var(--cyan-primary);
            letter-spacing: 0.14em;
            text-transform: uppercase;
            font-weight: 600;
            margin-top: 0.25rem;
        }
        .auth-card .form-group {
            margin-bottom: 1.2rem;
        }
        .auth-card .form-label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 0.45rem;
        }
        .auth-footer-tag {
            text-align: center;
            margin-top: 1.75rem;
            font-size: 0.72rem;
            color: var(--text-muted);
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-brand">
                <div class="auth-shield-icon">
                    <svg width="32" height="38" viewBox="0 0 28 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14 1L2 5.5V15.5C2 23.5 7.5 30 14 33C20.5 30 26 23.5 26 15.5V5.5L14 1Z" stroke="#21d4f3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M14 8V16M14 20H14.02M9 13.5C9 10.74 11.24 8.5 14 8.5C16.76 8.5 19 10.74 19 13.5V17M9 18V19" stroke="#21d4f3" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                </div>
                <h1 class="auth-title"><?= APP_NAME ?></h1>
                <div class="auth-subtitle">DIGITAL FORENSICS &bull; SOC PLATFORM</div>
            </div>

            <!-- Flash Alert Messages -->
            <?php if ($flash): ?>
            <div class="alert alert-<?= Security::e($flash['type'] === 'error' ? 'danger' : $flash['type']) ?>" style="margin-bottom: 1.25rem;">
                <div><?= Security::e($flash['message']) ?></div>
            </div>
            <?php endif; ?>

            <?= $content ?>
        </div>

        <div class="auth-footer-tag">
            FORENSIC-X &bull; College of Engineering Kallooppara &bull; Minor Project
        </div>
    </div>
</body>
</html>
