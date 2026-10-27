<?php
/**
 * FORENSIC-X Digital Evidence Vault View
 * Implemented directly from Figma Source of Truth (ROORaWXDtD6bCWfH7NbtGO — Frame 5:315)
 */

$selectedEvd = !empty($evidence) ? $evidence[0] : null;
$totalCount = (int)($stats['total'] ?? 0);
$verifiedCount = (int)($stats['verified'] ?? 0);
$mismatchCount = (int)($stats['mismatch'] ?? 0);
$unavailCount = (int)($stats['unavailable'] ?? 0);
?>

<!-- 1. PAGE HEADING -->
<div class="page-heading-row">
    <div class="page-heading-copy">
        <h1 class="page-title">Evidence Vault</h1>
        <div class="page-subtitle">Secure evidence storage, hash verification, and traceable handling.</div>
    </div>
    <div class="page-actions">
        <a href="/reports" class="btn btn-secondary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Export inventory
        </a>
        <?php if (Auth::can('upload_evidence')): ?>
        <a href="/evidence/upload" class="btn btn-primary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#06111c" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Upload evidence
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- 2. EVIDENCE METRICS (4 Cards) -->
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-header">
            <div class="kpi-icon-well">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
            </div>
            <div class="kpi-label">Evidence items</div>
        </div>
        <div class="kpi-value"><?= sprintf('%02d', $totalCount) ?></div>
        <div class="kpi-footnote">Preserved in vault</div>
    </div>

    <div class="kpi-card">
        <div class="kpi-header">
            <div class="kpi-icon-well" style="background-color: #114137;">
                <svg viewBox="0 0 24 24" fill="none" stroke="var(--green-accent)" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div class="kpi-label">Integrity verified</div>
        </div>
        <div class="kpi-value" style="color: var(--green-accent);"><?= sprintf('%02d', $verifiedCount) ?></div>
        <div class="kpi-footnote">SHA-256 match confirmed</div>
    </div>

    <div class="kpi-card">
        <div class="kpi-header">
            <div class="kpi-icon-well" style="background-color: #451a29;">
                <svg viewBox="0 0 24 24" fill="none" stroke="var(--red-accent)" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>
            <div class="kpi-label">Needs review</div>
        </div>
        <div class="kpi-value" style="color: <?= $mismatchCount > 0 ? 'var(--red-accent)' : 'var(--text-muted)' ?>;"><?= sprintf('%02d', $mismatchCount) ?></div>
        <div class="kpi-footnote <?= $mismatchCount > 0 ? 'danger' : '' ?>">
            <?= $mismatchCount > 0 ? 'Hash alteration alert' : 'Zero alterations detected' ?>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-header">
            <div class="kpi-icon-well" style="background-color: #0e3553;">
                <svg viewBox="0 0 24 24" fill="none" stroke="var(--cyan-primary)" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            </div>
            <div class="kpi-label">Storage security</div>
        </div>
        <div class="kpi-value" style="font-size: 1.35rem; margin-top: 0.85rem;">Isolated</div>
        <div class="kpi-footnote">Outside web root</div>
    </div>
</div>

<!-- 3. EVIDENCE UPLOAD PANEL (Figma Evidence Upload Panel) -->
<div class="evidence-upload-panel">
    <div class="evidence-upload-icon-well">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
    </div>
    <div class="evidence-upload-info">
        <div class="evidence-upload-title">Add evidence to an investigation</div>
        <div class="evidence-upload-desc">Drop a forensic artifact here or browse securely. Files are automatically cryptographically hashed (SHA-256) and sealed in isolated storage.</div>
        <div style="font-size: 0.68rem; color: var(--cyan-primary); margin-top: 4px; font-weight: 500;">
            Accepted types: TXT &bull; LOG &bull; CSV &bull; JSON &bull; PCAP/PCAPNG &bull; PDF &bull; MEMORY DUMPS
        </div>
    </div>
    <?php if (Auth::can('upload_evidence')): ?>
    <a href="/evidence/upload" class="btn btn-secondary" style="flex-shrink: 0;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
        Browse files &rarr;
    </a>
    <?php endif; ?>
</div>

<!-- 4. EVIDENCE FILTER BAR -->
<form method="GET" action="/evidence" class="filter-bar">
    <div class="filter-search-box">
        <svg class="filter-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" name="search" class="form-control" placeholder="Search evidence name, hash, or ID..." value="<?= Security::e($filters['search'] ?? '') ?>">
    </div>

    <div style="min-width: 170px;">
        <select name="verified_status" class="form-control" onchange="this.form.submit()">
            <option value="">Integrity: All</option>
            <option value="Verified" <?= ($filters['verified_status'] ?? '') === 'Verified' ? 'selected' : '' ?>>Verified</option>
            <option value="Mismatch" <?= ($filters['verified_status'] ?? '') === 'Mismatch' ? 'selected' : '' ?>>Mismatch</option>
            <option value="Unavailable" <?= ($filters['verified_status'] ?? '') === 'Unavailable' ? 'selected' : '' ?>>Unavailable</option>
        </select>
    </div>

    <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
    <a href="/evidence" class="btn btn-secondary btn-sm">Reset</a>
</form>

<!-- 5. EVIDENCE MAIN ROW (Master-Detail 2 Columns) -->
<div class="fx-master-detail">
    <!-- Left: Evidence Inventory Card -->
    <div class="card fx-master-list">
        <div class="card-header">
            <div class="card-header-left">
                <div class="card-title">Evidence Inventory</div>
                <div class="card-subtitle"><?= count($evidence) ?> forensic items registered</div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="data-table" id="evidenceTable">
                <thead>
                    <tr>
                        <th>ARTIFACT</th>
                        <th>CASE</th>
                        <th>SIZE</th>
                        <th>INTEGRITY</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($evidence)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                            No evidence files match the filter criteria.
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($evidence as $idx => $evd): ?>
                        <tr class="evidence-row <?= $idx === 0 ? 'selected' : '' ?>"
                            data-id="<?= (int)$evd['id'] ?>"
                            data-code="<?= Security::e($evd['evidence_code']) ?>"
                            data-name="<?= Security::e($evd['name']) ?>"
                            data-file="<?= Security::e($evd['original_filename']) ?>"
                            data-case="<?= Security::e($evd['incident_code']) ?>"
                            data-casetitle="<?= Security::e($evd['incident_title']) ?>"
                            data-size="<?= View::formatBytes((int)$evd['file_size']) ?>"
                            data-type="<?= Security::e($evd['file_extension'] ?? ($evd['mime_type'] ?? 'Binary')) ?>"
                            data-hash="<?= Security::e($evd['sha256_hash']) ?>"
                            data-status="<?= Security::e($evd['last_verified_status'] ?: 'Pending') ?>"
                            data-date="<?= date('M d, Y • H:i', strtotime($evd['created_at'])) ?>"
                            data-uploader="<?= Security::e($evd['uploader_name'] ?: 'System Intake') ?>"
                            style="cursor: pointer;">
                            <td>
                                <div style="display: flex; flex-direction: column;">
                                    <span style="font-weight: 600; color: var(--text-primary); font-size: 0.83rem;"><?= Security::e($evd['name']) ?></span>
                                    <span style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 0.7rem; color: var(--text-secondary);"><?= Security::e($evd['evidence_code']) ?> &bull; <?= Security::e($evd['original_filename']) ?></span>
                                </div>
                            </td>
                            <td>
                                <a href="/incidents/view?id=<?= (int)$evd['incident_id'] ?>" style="font-family: ui-monospace, monospace; color: var(--cyan-primary); text-decoration: none; font-weight: 600; font-size: 0.75rem;">
                                    <?= Security::e($evd['incident_code']) ?>
                                </a>
                            </td>
                            <td style="font-size: 0.75rem; color: var(--text-secondary);"><?= View::formatBytes((int)$evd['file_size']) ?></td>
                            <td>
                                <span id="verify-badge-<?= (int)$evd['id'] ?>" class="badge badge-<?= strtolower($evd['last_verified_status'] ?: 'open') ?>">
                                    <?= Security::e($evd['last_verified_status'] ?: 'Pending') ?>
                                </span>
                            </td>
                            <td>
                                <a href="/evidence/view?id=<?= (int)$evd['id'] ?>" class="btn btn-secondary btn-sm" title="View Artifact Details">Inspect</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Right: Selected Evidence Details (Figma Selected Evidence Details) -->
    <div class="fx-detail-panel" id="evidenceDetailPanel">
        <?php if ($selectedEvd): ?>
        <div class="detail-panel-header">
            <div>
                <div class="detail-meta-label">SELECTED EVIDENCE</div>
                <div id="detailEvdCode" style="font-size: 1.15rem; font-weight: 700; color: var(--cyan-primary); font-family: ui-monospace, SFMono-Regular, monospace; margin-top: 2px;">
                    <?= Security::e($selectedEvd['evidence_code']) ?>
                </div>
            </div>
            <div id="detailEvdBadgeContainer">
                <span class="badge badge-<?= strtolower($selectedEvd['last_verified_status'] ?: 'open') ?>">
                    <?= Security::e($selectedEvd['last_verified_status'] ?: 'Pending') ?>
                </span>
            </div>
        </div>

        <div>
            <h2 id="detailEvdName" class="detail-panel-title"><?= Security::e($selectedEvd['name']) ?></h2>
            <div id="detailEvdFile" style="font-size: 0.75rem; color: var(--text-secondary); font-family: ui-monospace, monospace; margin-top: 3px;">
                <?= Security::e($selectedEvd['original_filename']) ?>
            </div>
        </div>

        <!-- Ingestion SHA-256 Hash Card -->
        <div>
            <div class="detail-meta-label" style="margin-bottom: 0.35rem;">INGESTION SHA-256 HASH</div>
            <div class="hash-preview-box">
                <span id="detailEvdHash" style="overflow: hidden; text-overflow: ellipsis;"><?= Security::e($selectedEvd['sha256_hash']) ?></span>
                <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('detailEvdHash').innerText); alert('SHA-256 copied to clipboard!');" style="background: none; border: none; color: var(--cyan-primary); cursor: pointer; padding: 2px;" title="Copy SHA-256">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                </button>
            </div>
        </div>

        <!-- Metadata Grid -->
        <div class="detail-meta-list" style="background-color: var(--bg-surface-alt); padding: 0.85rem 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle);">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div class="detail-meta-item">
                    <span class="detail-meta-label">CASE CODE</span>
                    <span id="detailEvdCase" class="detail-meta-value" style="color: var(--cyan-primary); font-family: ui-monospace, monospace; font-weight: 600;"><?= Security::e($selectedEvd['incident_code']) ?></span>
                </div>
                <div class="detail-meta-item">
                    <span class="detail-meta-label">FILE SIZE</span>
                    <span id="detailEvdSize" class="detail-meta-value"><?= View::formatBytes((int)$selectedEvd['file_size']) ?></span>
                </div>
                <div class="detail-meta-item">
                    <span class="detail-meta-label">INGESTED ON</span>
                    <span id="detailEvdDate" class="detail-meta-value" style="font-size: 0.72rem;"><?= date('M d, Y • H:i', strtotime($selectedEvd['created_at'])) ?></span>
                </div>
                <div class="detail-meta-item">
                    <span class="detail-meta-label">HANDLER</span>
                    <span id="detailEvdUploader" class="detail-meta-value" style="font-size: 0.75rem;"><?= Security::e($selectedEvd['uploader_name'] ?: 'SOC System') ?></span>
                </div>
            </div>
        </div>

        <!-- Recent Chain of Custody Preview -->
        <div>
            <div class="detail-meta-label" style="margin-bottom: 0.45rem;">RECENT CHAIN OF CUSTODY</div>
            <div style="display: flex; flex-direction: column; gap: 0.45rem;">
                <?php if (!empty($firstCustody)): ?>
                    <?php foreach (array_slice($firstCustody, 0, 3) as $cst): ?>
                    <div style="background-color: var(--bg-surface-alt); border: 1px solid var(--border-subtle); border-radius: var(--radius-sm); padding: 0.45rem 0.65rem; display: flex; align-items: center; justify-content: space-between; font-size: 0.72rem;">
                        <span style="font-weight: 600; color: var(--text-primary);"><?= Security::e($cst['action']) ?></span>
                        <span style="color: var(--text-muted); font-family: ui-monospace, monospace;"><?= date('M d, H:i', strtotime($cst['action_timestamp'])) ?></span>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="font-size: 0.72rem; color: var(--text-muted); padding: 0.25rem 0;">No prior transfers recorded. Initial intake logged.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Actions -->
        <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 0.35rem;">
            <button type="button" id="btnVerifyDetail" data-id="<?= (int)$selectedEvd['id'] ?>" class="btn btn-primary btn-verify-hash" style="width: 100%;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#06111c" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
                Verify cryptographic hash
            </button>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                <a id="detailDownloadLink" href="/evidence/download?id=<?= (int)$selectedEvd['id'] ?>" class="btn btn-secondary btn-sm" style="width: 100%;">
                    Download
                </a>
                <a id="detailViewLink" href="/evidence/view?id=<?= (int)$selectedEvd['id'] ?>" class="btn btn-secondary btn-sm" style="width: 100%;">
                    Full ledger
                </a>
            </div>
        </div>
        <?php else: ?>
        <div style="text-align: center; color: var(--text-muted); padding: 2rem;">No evidence artifact selected.</div>
        <?php endif; ?>
    </div>
</div>

<!-- Interactive Selection Script -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const rows = document.querySelectorAll('.evidence-row');
    rows.forEach(row => {
        row.addEventListener('click', (e) => {
            if (e.target.tagName.toLowerCase() === 'a' || e.target.tagName.toLowerCase() === 'button') return;

            rows.forEach(r => r.classList.remove('selected'));
            row.classList.add('selected');

            const id = row.getAttribute('data-id');
            const code = row.getAttribute('data-code');
            const name = row.getAttribute('data-name');
            const file = row.getAttribute('data-file');
            const caseCode = row.getAttribute('data-case');
            const size = row.getAttribute('data-size');
            const hash = row.getAttribute('data-hash');
            const status = row.getAttribute('data-status');
            const date = row.getAttribute('data-date');
            const uploader = row.getAttribute('data-uploader');

            document.getElementById('detailEvdCode').textContent = code;
            document.getElementById('detailEvdName').textContent = name;
            document.getElementById('detailEvdFile').textContent = file;
            document.getElementById('detailEvdCase').textContent = caseCode;
            document.getElementById('detailEvdSize').textContent = size;
            document.getElementById('detailEvdHash').textContent = hash;
            document.getElementById('detailEvdDate').textContent = date;
            document.getElementById('detailEvdUploader').textContent = uploader;

            document.getElementById('btnVerifyDetail').setAttribute('data-id', id);
            document.getElementById('detailDownloadLink').href = `/evidence/download?id=${id}`;
            document.getElementById('detailViewLink').href = `/evidence/view?id=${id}`;

            const badgeCont = document.getElementById('detailEvdBadgeContainer');
            const statusClass = status.toLowerCase() === 'verified' ? 'badge-verified' : (status.toLowerCase() === 'mismatch' ? 'badge-mismatch' : 'badge-open');
            badgeCont.innerHTML = `<span class="badge ${statusClass}">${status}</span>`;
        });
    });
});
</script>
