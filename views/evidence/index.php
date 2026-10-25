<?php
/**
 * FORENSIC-X Digital Evidence Vault Inventory View
 */
?>
<!-- STATS CARDS FOR EVIDENCE INTEGRITY -->
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
    <div class="stat-card">
        <div class="stat-header"><span>Total Artifacts</span></div>
        <div class="stat-value"><?= (int)$stats['total'] ?></div>
        <div class="stat-desc">Secured In Vault</div>
    </div>
    <div class="stat-card">
        <div class="stat-header"><span>Verified Integrity</span></div>
        <div class="stat-value" style="color: #10B981;"><?= (int)$stats['verified'] ?></div>
        <div class="stat-desc">SHA-256 Match Confirmed</div>
    </div>
    <div class="stat-card">
        <div class="stat-header"><span>Hash Mismatch</span></div>
        <div class="stat-value" style="color: <?= $stats['mismatch'] > 0 ? '#EF4444' : 'var(--text-muted)' ?>;">
            <?= (int)$stats['mismatch'] ?>
        </div>
        <div class="stat-desc"><?= $stats['mismatch'] > 0 ? 'CRITICAL INTEGRITY ALERT' : 'Zero Alterations Detected' ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-header"><span>Missing on Disk</span></div>
        <div class="stat-value" style="color: <?= $stats['unavailable'] > 0 ? '#F59E0B' : 'var(--text-muted)' ?>;">
            <?= (int)$stats['unavailable'] ?>
        </div>
        <div class="stat-desc">Storage Vault Offline Check</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
            Digital Forensic Evidence Inventory
        </div>
        <?php if (Auth::can('upload_evidence')): ?>
        <a href="/evidence/upload" class="btn btn-primary btn-sm">
            + Secure Evidence Ingestion
        </a>
        <?php endif; ?>
    </div>

    <!-- FILTER BAR -->
    <form method="GET" action="/evidence" class="filter-bar" style="margin: 1rem; border-radius: var(--radius-md);">
        <div style="flex: 1; min-width: 200px;">
            <input type="text" name="search" class="form-control" placeholder="Search by evidence code, name, hash, filename..." value="<?= Security::e($filters['search'] ?? '') ?>">
        </div>
        
        <div>
            <select name="verified_status" class="form-control">
                <option value="">All Integrity Statuses</option>
                <option value="Verified" <?= ($filters['verified_status'] ?? '') === 'Verified' ? 'selected' : '' ?>>Verified</option>
                <option value="Mismatch" <?= ($filters['verified_status'] ?? '') === 'Mismatch' ? 'selected' : '' ?>>Mismatch</option>
                <option value="Unavailable" <?= ($filters['verified_status'] ?? '') === 'Unavailable' ? 'selected' : '' ?>>Unavailable</option>
            </select>
        </div>

        <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
        <a href="/evidence" class="btn btn-secondary btn-sm">Reset</a>
    </form>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Evidence Code</th>
                    <th>Artifact Name</th>
                    <th>Case Association</th>
                    <th>Format / Size</th>
                    <th>SHA-256 Cryptographic Hash</th>
                    <th>Integrity Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($evidence)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                        No evidence items found in the secure vault.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($evidence as $ev): ?>
                    <tr>
                        <td>
                            <a href="/evidence/view?id=<?= (int)$ev['id'] ?>" style="color: var(--cyan-primary); font-family: monospace; font-weight: 700; text-decoration: none;">
                                <?= Security::e($ev['evidence_code']) ?>
                            </a>
                        </td>
                        <td>
                            <div style="font-weight: 600; color: var(--text-primary);"><?= Security::e($ev['name']) ?></div>
                            <div style="font-size: 0.72rem; color: var(--text-muted); font-family: monospace;"><?= Security::e($ev['original_filename']) ?></div>
                        </td>
                        <td>
                            <a href="/incidents/view?id=<?= (int)$ev['incident_id'] ?>" style="color: var(--text-secondary); text-decoration: none; font-size: 0.8rem;">
                                <?= Security::e($ev['incident_code']) ?>
                            </a>
                        </td>
                        <td style="font-size: 0.75rem;">
                            <span class="badge badge-neutral"><?= strtoupper(Security::e($ev['file_extension'])) ?></span>
                            <?= View::formatBytes((int)$ev['file_size']) ?>
                        </td>
                        <td>
                            <span class="code-block" style="font-size: 0.7rem;" title="<?= Security::e($ev['sha256_hash']) ?>">
                                <?= substr(Security::e($ev['sha256_hash']), 0, 14) ?>...<?= substr(Security::e($ev['sha256_hash']), -8) ?>
                            </span>
                        </td>
                        <td>
                            <span id="verify-badge-<?= (int)$ev['id'] ?>">
                                <?= View::statusBadge($ev['last_verified_status']) ?>
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.35rem;">
                                <a href="/evidence/view?id=<?= (int)$ev['id'] ?>" class="btn btn-secondary btn-sm">Custody</a>
                                <button type="button" class="btn btn-secondary btn-sm btn-verify-hash" data-id="<?= (int)$ev['id'] ?>" title="Recompute cryptographic hash and verify integrity">
                                    Verify
                                </button>
                                <a href="/evidence/download?id=<?= (int)$ev['id'] ?>" class="btn btn-secondary btn-sm">
                                    Download
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
