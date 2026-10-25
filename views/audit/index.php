<?php
/**
 * FORENSIC-X Security & Audit Center View
 */
?>
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            Security & Audit Trail (Append-Only)
        </div>
        <div style="font-size: 0.72rem; color: var(--cyan-primary);">
            Tamper-Evident System Telemetry
        </div>
    </div>

    <!-- FILTER BAR -->
    <form method="GET" action="/audit" class="filter-bar" style="margin: 1rem; border-radius: var(--radius-md);">
        <div>
            <select name="outcome" class="form-control">
                <option value="">All Outcomes</option>
                <option value="SUCCESS" <?= ($filters['outcome'] ?? '') === 'SUCCESS' ? 'selected' : '' ?>>SUCCESS</option>
                <option value="FAILURE" <?= ($filters['outcome'] ?? '') === 'FAILURE' ? 'selected' : '' ?>>FAILURE</option>
                <option value="DENIED" <?= ($filters['outcome'] ?? '') === 'DENIED' ? 'selected' : '' ?>>DENIED</option>
            </select>
        </div>

        <div>
            <input type="text" name="action" class="form-control" placeholder="Action filter (e.g. LOGIN, UPLOAD)..." value="<?= Security::e($filters['action'] ?? '') ?>">
        </div>

        <div>
            <input type="text" name="username" class="form-control" placeholder="Operator username..." value="<?= Security::e($filters['username'] ?? '') ?>">
        </div>

        <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
        <a href="/audit" class="btn btn-secondary btn-sm">Reset</a>
    </form>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Log ID</th>
                    <th>Action</th>
                    <th>Operator</th>
                    <th>Target Record</th>
                    <th>Outcome</th>
                    <th>Context Details</th>
                    <th>IP Address</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($logs)): ?>
                <tr><td colspan="8" style="text-align: center; color: var(--text-muted); padding: 2rem;">No audit logs match criteria.</td></tr>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                    <tr>
                        <td style="font-family: monospace; font-size: 0.72rem; color: var(--text-muted);">#<?= (int)$log['id'] ?></td>
                        <td style="font-weight: 700; color: var(--text-primary); font-size: 0.78rem;">
                            <?= Security::e($log['action']) ?>
                        </td>
                        <td>
                            <strong style="color: var(--cyan-primary);"><?= Security::e($log['username'] ?: 'anonymous') ?></strong>
                        </td>
                        <td style="font-family: monospace; font-size: 0.75rem;">
                            <?= Security::e($log['target_type'] ? $log['target_type'] . ':' . $log['target_id'] : '-') ?>
                        </td>
                        <td>
                            <span class="badge <?= $log['outcome'] === 'SUCCESS' ? 'badge-verified' : ($log['outcome'] === 'DENIED' ? 'badge-critical' : 'badge-mismatch') ?>">
                                <?= Security::e($log['outcome']) ?>
                            </span>
                        </td>
                        <td style="font-size: 0.78rem; color: var(--text-secondary); max-width: 320px;">
                            <?= Security::e($log['details']) ?>
                        </td>
                        <td style="font-family: monospace; font-size: 0.72rem;"><?= Security::e($log['ip_address']) ?></td>
                        <td style="font-size: 0.72rem;"><?= date('Y-m-d H:i:s', strtotime($log['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
