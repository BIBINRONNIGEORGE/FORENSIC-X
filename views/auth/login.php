<?php
/**
 * FORENSIC-X Login View
 * College of Engineering Kallooppara, Kerala
 */
?>
<form method="POST" action="/login">
    <?= CSRF::input() ?>

    <div class="form-group">
        <label class="form-label" for="username">Username or Operator ID</label>
        <input type="text" id="username" name="username" class="form-control" placeholder="admin" required autofocus>
    </div>

    <div class="form-group">
        <label class="form-label" for="password">Password</label>
        <input type="password" id="password" name="password" class="form-control" placeholder="••••••••••••" required>
    </div>

    <div style="margin-top: 1.5rem;">
        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem; font-size: 0.85rem;">
            Authenticate Session &rarr;
        </button>
    </div>
</form>

<!-- Demonstration Accounts Helper for Evaluation -->
<div style="margin-top: 1.75rem; padding: 0.95rem 1.15rem; background-color: var(--bg-surface-alt); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); font-size: 0.75rem;">
    <div style="font-weight: 700; color: var(--cyan-primary); margin-bottom: 0.45rem; text-transform: uppercase; letter-spacing: 0.08em; display: flex; align-items: center; gap: 0.35rem;">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
        Demonstration Accounts:
    </div>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.35rem 0.75rem; color: var(--text-secondary); font-size: 0.72rem;">
        <div><strong style="color: var(--text-primary);">Admin:</strong> <code>admin</code></div>
        <div><code>Admin@ForensicX2026!</code></div>
        <div><strong style="color: var(--text-primary);">Investigator:</strong> <code>investigator</code></div>
        <div><code>Investigator@2026!</code></div>
        <div><strong style="color: var(--text-primary);">Analyst:</strong> <code>analyst</code></div>
        <div><code>Analyst@2026!</code></div>
    </div>
</div>
