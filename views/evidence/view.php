<?php
/**
 * FORENSIC-X Evidence Details & Chain of Custody View
 */
?>
<div class="card" style="margin-bottom: 1.5rem; border-left: 4px solid var(--cyan-primary);">
    <div class="card-body">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
            <div>
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                    <span style="font-size: 1.25rem; font-weight: 800; font-family: monospace; color: var(--cyan-primary);">
                        <?= Security::e($evidence['evidence_code']) ?>
                    </span>
                    <span id="verify-badge-<?= (int)$evidence['id'] ?>">
                        <?= View::statusBadge($evidence['last_verified_status']) ?>
                    </span>
                    <?= View::statusBadge($evidence['status']) ?>
                    <span class="badge badge-neutral"><?= strtoupper(Security::e($evidence['file_extension'])) ?></span>
                </div>
                <h2 style="font-size: 1.35rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <?= Security::e($evidence['name']) ?>
                </h2>
                <div style="font-size: 0.8rem; color: var(--text-muted);">
                    Associated Case: 
                    <a href="/incidents/view?id=<?= (int)$evidence['incident_id'] ?>" style="color: var(--cyan-primary); text-decoration: none; font-weight: 600;">
                        [<?= Security::e($evidence['incident_code']) ?>] <?= Security::e($evidence['incident_title']) ?>
                    </a>
                </div>
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <button type="button" class="btn btn-primary btn-sm btn-verify-hash" data-id="<?= (int)$evidence['id'] ?>">
                    Verify Cryptographic Hash
                </button>
                <a href="/evidence/download?id=<?= (int)$evidence['id'] ?>" class="btn btn-secondary btn-sm">
                    Download Secured Artifact
                </a>
            </div>
        </div>
    </div>
</div>

<!-- CRYPTOGRAPHIC INTEGRITY VERIFICATION CARD -->
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--cyan-primary)" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            Cryptographic Integrity Status (SHA-256)
        </div>
        <div style="font-size: 0.75rem; color: var(--text-muted);">
            Last Verified: <?= $evidence['last_verified_at'] ? date('M d, Y H:i:s', strtotime($evidence['last_verified_at'])) : 'Never' ?>
        </div>
    </div>
    <div class="card-body">
        <div style="margin-bottom: 1rem;">
            <div style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: var(--text-muted); margin-bottom: 0.3rem;">
                Recorded Intake Hash (SHA-256):
            </div>
            <div class="code-block" style="font-size: 0.88rem; padding: 0.5rem 0.75rem;">
                <?= Security::e($evidence['sha256_hash']) ?>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; font-size: 0.8rem; border-top: 1px solid var(--border-color); padding-top: 1rem;">
            <div>Original Filename: <strong style="color: var(--text-primary);"><?= Security::e($evidence['original_filename']) ?></strong></div>
            <div>Storage Vault Filename: <span style="font-family: monospace; color: var(--text-secondary);"><?= Security::e($evidence['storage_filename']) ?></span></div>
            <div>File Size: <strong style="color: var(--text-primary);"><?= View::formatBytes((int)$evidence['file_size']) ?> (<?= number_format((int)$evidence['file_size']) ?> bytes)</strong></div>
            <div>MIME Type: <span style="color: var(--text-secondary);"><?= Security::e($evidence['mime_type']) ?></span></div>
        </div>

        <?php if (!empty($evidence['acquisition_notes'])): ?>
        <div style="margin-top: 1rem; padding: 0.75rem; background-color: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-sm); font-size: 0.8rem;">
            <strong style="color: var(--cyan-primary);">Acquisition Notes:</strong> <?= Security::e($evidence['acquisition_notes']) ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- DEDICATED CHAIN OF CUSTODY (APPEND-ONLY) -->
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
            Evidence Chain of Custody Record (Append-Only)
        </div>
        <div style="font-size: 0.72rem; color: var(--text-muted);">
            Legally Defensible Custody Ledger
        </div>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Event Type</th>
                    <th>Timestamp</th>
                    <th>Actor / Operator</th>
                    <th>State Transition</th>
                    <th>Custody Notes</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($custodyEvents)): ?>
                <tr><td colspan="6" style="text-align: center; color: var(--text-muted);">No custody events logged yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($custodyEvents as $custody): ?>
                    <tr>
                        <td style="font-weight: 700; color: var(--cyan-primary); font-size: 0.78rem;">
                            <?= Security::e($custody['event_type']) ?>
                        </td>
                        <td style="font-size: 0.75rem;"><?= date('Y-m-d H:i:s', strtotime($custody['created_at'])) ?></td>
                        <td>
                            <div style="font-weight: 600; color: var(--text-primary);"><?= Security::e($custody['actor_name']) ?></div>
                            <div style="font-size: 0.7rem; color: var(--text-muted);"><?= strtoupper(Security::e($custody['role'] ?? 'SYSTEM')) ?></div>
                        </td>
                        <td style="font-size: 0.75rem;">
                            <?php if ($custody['previous_state'] || $custody['new_state']): ?>
                                <?= Security::e($custody['previous_state'] ?: 'None') ?> → <strong style="color: var(--text-primary);"><?= Security::e($custody['new_state']) ?></strong>
                            <?php else: ?>
                                <span style="color: var(--text-muted);">-</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size: 0.8rem; color: var(--text-secondary); max-width: 320px;">
                            <?= Security::e($custody['notes']) ?>
                        </td>
                        <td style="font-family: monospace; font-size: 0.72rem;"><?= Security::e($custody['ip_address']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
