<?php
/**
 * FORENSIC-X User Management View (Admin Only)
 */
?>
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            SOC Operator & User Access Management
        </div>
        <a href="/users/create" class="btn btn-primary btn-sm">
            + Provision New User
        </a>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Full Name & Username</th>
                    <th>Email Address</th>
                    <th>Role</th>
                    <th>Department</th>
                    <th>Status</th>
                    <th>Last Active</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td style="font-family: monospace; color: var(--text-muted);">#<?= (int)$u['id'] ?></td>
                    <td>
                        <div style="font-weight: 600; color: var(--text-primary);"><?= Security::e($u['full_name']) ?></div>
                        <div style="font-size: 0.72rem; color: var(--cyan-primary); font-family: monospace;"><?= Security::e($u['username']) ?></div>
                    </td>
                    <td style="font-size: 0.8rem;"><?= Security::e($u['email']) ?></td>
                    <td><?= View::roleBadge($u['role']) ?></td>
                    <td style="font-size: 0.78rem; color: var(--text-secondary);"><?= Security::e($u['department']) ?></td>
                    <td>
                        <span class="badge <?= $u['is_active'] ? 'badge-verified' : 'badge-closed' ?>">
                            <?= $u['is_active'] ? 'Active' : 'Deactivated' ?>
                        </span>
                    </td>
                    <td style="font-size: 0.72rem;">
                        <?= $u['last_login_at'] ? date('M d, H:i', strtotime($u['last_login_at'])) : 'Never' ?>
                    </td>
                    <td>
                        <?php if ((int)$u['id'] !== Auth::id()): ?>
                        <form method="POST" action="/users/toggle-status" style="display: inline;">
                            <?= CSRF::input() ?>
                            <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                            <input type="hidden" name="status" value="<?= $u['is_active'] ? 0 : 1 ?>">
                            <button type="submit" class="btn <?= $u['is_active'] ? 'btn-danger' : 'btn-success' ?> btn-sm">
                                <?= $u['is_active'] ? 'Deactivate' : 'Activate' ?>
                            </button>
                        </form>
                        <?php else: ?>
                        <span style="font-size: 0.7rem; color: var(--text-muted);">(Current Self)</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
