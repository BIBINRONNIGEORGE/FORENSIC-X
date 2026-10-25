<?php
/**
 * FORENSIC-X User Profile View
 */
$user = Auth::user();
?>
<div style="max-width: 800px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Operator Profile
            </div>
            <?= View::roleBadge($user['role']) ?>
        </div>
        <div class="card-body">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                <div>
                    <label class="form-label">Full Name</label>
                    <div style="font-size: 1.1rem; font-weight: 600; color: var(--text-primary);"><?= Security::e($user['full_name']) ?></div>
                </div>
                <div>
                    <label class="form-label">Username</label>
                    <div style="font-size: 1.1rem; color: var(--cyan-primary); font-family: monospace;"><?= Security::e($user['username']) ?></div>
                </div>
                <div>
                    <label class="form-label">Email Address</label>
                    <div style="color: var(--text-secondary);"><?= Security::e($user['email']) ?></div>
                </div>
                <div>
                    <label class="form-label">Assigned Department</label>
                    <div style="color: var(--text-secondary);"><?= Security::e($user['department']) ?></div>
                </div>
            </div>

            <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 1.5rem 0;">

            <div style="display: flex; gap: 1rem;">
                <a href="/change-password" class="btn btn-secondary">
                    Change Password
                </a>
                <a href="/logout" class="btn btn-danger">
                    Terminate Session
                </a>
            </div>
        </div>
    </div>
</div>
