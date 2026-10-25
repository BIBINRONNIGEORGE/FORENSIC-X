<?php
/**
 * FORENSIC-X Login View
 */
?>
<form method="POST" action="/login">
    <?= CSRF::input() ?>

    <div class="form-group">
        <label class="form-label" for="username">Username or Service Account</label>
        <input type="text" id="username" name="username" class="form-control" placeholder="Enter username (e.g. admin)" required autofocus>
    </div>

    <div class="form-group">
        <label class="form-label" for="password">Password</label>
        <input type="password" id="password" name="password" class="form-control" placeholder="••••••••••••" required>
    </div>

    <div style="margin-top: 1.5rem;">
        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem;">
            Authenticate Session
        </button>
    </div>
</form>

<!-- Demonstration Accounts Helper for Academic Evaluation -->
<div style="margin-top: 1.75rem; padding: 1rem; background-color: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.75rem;">
    <div style="font-weight: 700; color: var(--cyan-primary); margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em;">
        Demonstration RBAC Accounts:
    </div>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; color: var(--text-secondary);">
        <div><strong>Admin:</strong> <code>admin</code></div>
        <div><code>Admin@ForensicX2026!</code></div>
        <div><strong>Investigator:</strong> <code>investigator</code></div>
        <div><code>Investigator@2026!</code></div>
        <div><strong>Analyst:</strong> <code>analyst</code></div>
        <div><code>Analyst@2026!</code></div>
        <div><strong>Reviewer:</strong> <code>reviewer</code></div>
        <div><code>Reviewer@2026!</code></div>
    </div>
</div>
