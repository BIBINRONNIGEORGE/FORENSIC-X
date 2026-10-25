<?php
/**
 * FORENSIC-X Authentication Layout
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? Security::e($pageTitle) . ' — ' : '' ?><?= APP_NAME ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at 50% 20%, #0F172A 0%, #070B14 100%);
            padding: 1.5rem;
        }
        .auth-card {
            width: 100%;
            max-width: 440px;
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 2.5rem 2rem;
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
            background: linear-gradient(90deg, var(--blue-accent), var(--cyan-primary), var(--blue-dark));
        }
        .auth-brand {
            text-align: center;
            margin-bottom: 2rem;
        }
        .auth-logo-icon {
            width: 48px;
            height: 48px;
            margin: 0 auto 1rem;
            background: linear-gradient(135deg, var(--cyan-primary), var(--blue-dark));
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 20px var(--cyan-glow);
        }
        .auth-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: 0.05em;
        }
        .auth-subtitle {
            font-size: 0.8rem;
            color: var(--cyan-primary);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-top: 0.25rem;
        }
        .academic-badge {
            margin-top: 0.75rem;
            font-size: 0.72rem;
            color: var(--text-muted);
            border-top: 1px solid var(--border-color);
            padding-top: 0.75rem;
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="auth-brand">
            <div class="auth-logo-icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    <path d="M12 8v4"/>
                    <path d="M12 16h.01"/>
                </svg>
            </div>
            <h1 class="auth-title"><?= APP_NAME ?></h1>
            <div class="auth-subtitle">Cybersecurity Incident Investigation & Digital Forensics</div>
            <div class="academic-badge">
                College of Engineering Kallooppara, Kerala<br>
                B.Tech CSE (Cyber Security) Minor Project
            </div>
        </div>

        <?php if ($flash): ?>
        <div class="alert alert-<?= Security::e($flash['type']) ?>">
            <div><?= Security::e($flash['message']) ?></div>
        </div>
        <?php endif; ?>

        <?= $content ?>
    </div>
</body>
</html>
