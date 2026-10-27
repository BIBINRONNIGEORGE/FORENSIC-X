<?php
/**
 * FORENSIC-X Incident Management View
 * Implemented directly from Figma Source of Truth (ROORaWXDtD6bCWfH7NbtGO — Frame 5:36)
 */

$selectedInc = !empty($incidents) ? $incidents[0] : null;
$openCases = (int)($stats['open'] ?? 0);
$critCases = (int)($stats['by_severity']['Critical'] ?? 0);
$invCases  = (int)($stats['by_status']['Investigating'] ?? 0);
$resCases  = (int)($stats['resolved'] ?? 0);
?>

<!-- 1. PAGE HEADING -->
<div class="page-heading-row">
    <div class="page-heading-copy">
        <h1 class="page-title">Incident Management</h1>
        <div class="page-subtitle">Track cases, assign investigators, and manage incident response.</div>
    </div>
    <div class="page-actions">
        <a href="/reports" class="btn btn-secondary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Export cases
        </a>
        <?php if (Auth::can('create_incident')): ?>
        <a href="/incidents/create" class="btn btn-primary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#06111c" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Create incident
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- 2. SUMMARY METRICS (4 Cards) -->
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-header">
            <div class="kpi-icon-well">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/></svg>
            </div>
            <div class="kpi-label">Open cases</div>
        </div>
        <div class="kpi-value"><?= sprintf('%02d', $openCases) ?></div>
        <div class="kpi-footnote">+2 active today</div>
    </div>

    <div class="kpi-card">
        <div class="kpi-header">
            <div class="kpi-icon-well" style="background-color: #451a29;">
                <svg viewBox="0 0 24 24" fill="none" stroke="var(--red-accent)" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <div class="kpi-label">Critical</div>
        </div>
        <div class="kpi-value" style="color: var(--red-accent);"><?= sprintf('%02d', $critCases) ?></div>
        <div class="kpi-footnote danger">Requires immediate review</div>
    </div>

    <div class="kpi-card">
        <div class="kpi-header">
            <div class="kpi-icon-well" style="background-color: #142e3d;">
                <svg viewBox="0 0 24 24" fill="none" stroke="var(--cyan-primary)" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </div>
            <div class="kpi-label">Investigating</div>
        </div>
        <div class="kpi-value"><?= sprintf('%02d', $invCases) ?></div>
        <div class="kpi-footnote">Active evidence triage</div>
    </div>

    <div class="kpi-card">
        <div class="kpi-header">
            <div class="kpi-icon-well" style="background-color: #114137;">
                <svg viewBox="0 0 24 24" fill="none" stroke="var(--green-accent)" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div class="kpi-label">Resolved cases</div>
        </div>
        <div class="kpi-value"><?= sprintf('%02d', $resCases) ?></div>
        <div class="kpi-footnote">Closure rate steady</div>
    </div>
</div>

<!-- 3. INCIDENT FILTERS -->
<form method="GET" action="/incidents" class="filter-bar">
    <div class="filter-search-box">
        <svg class="filter-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" name="search" class="form-control" placeholder="Search by ID, title, or asset..." value="<?= Security::e($filters['search'] ?? '') ?>">
    </div>

    <div style="min-width: 150px;">
        <select name="severity" class="form-control" onchange="this.form.submit()">
            <option value="">Severity: All</option>
            <option value="Critical" <?= ($filters['severity'] ?? '') === 'Critical' ? 'selected' : '' ?>>Critical</option>
            <option value="High" <?= ($filters['severity'] ?? '') === 'High' ? 'selected' : '' ?>>High</option>
            <option value="Medium" <?= ($filters['severity'] ?? '') === 'Medium' ? 'selected' : '' ?>>Medium</option>
            <option value="Low" <?= ($filters['severity'] ?? '') === 'Low' ? 'selected' : '' ?>>Low</option>
        </select>
    </div>

    <div style="min-width: 150px;">
        <select name="status" class="form-control" onchange="this.form.submit()">
            <option value="">Status: All</option>
            <option value="Open" <?= ($filters['status'] ?? '') === 'Open' ? 'selected' : '' ?>>Open</option>
            <option value="Investigating" <?= ($filters['status'] ?? '') === 'Investigating' ? 'selected' : '' ?>>Investigating</option>
            <option value="Contained" <?= ($filters['status'] ?? '') === 'Contained' ? 'selected' : '' ?>>Contained</option>
            <option value="Resolved" <?= ($filters['status'] ?? '') === 'Resolved' ? 'selected' : '' ?>>Resolved</option>
            <option value="Closed" <?= ($filters['status'] ?? '') === 'Closed' ? 'selected' : '' ?>>Closed</option>
        </select>
    </div>

    <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
    <a href="/incidents" class="btn btn-secondary btn-sm">Reset</a>
</form>

<!-- 4. INCIDENT MAIN ROW (Master-Detail 2-Column Layout) -->
<div class="fx-master-detail">
    <!-- Left: Incidents Table Card -->
    <div class="card fx-master-list">
        <div class="card-header">
            <div class="card-header-left">
                <div class="card-title">Case Inventory</div>
                <div class="card-subtitle"><?= count($incidents) ?> recorded incident files</div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="data-table" id="incidentsTable">
                <thead>
                    <tr>
                        <th>CASE</th>
                        <th>SEVERITY</th>
                        <th>STATUS</th>
                        <th>ASSIGNED</th>
                        <th>ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($incidents)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                            No incident records match the filter criteria.
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($incidents as $idx => $inc): ?>
                        <tr class="incident-row <?= $idx === 0 ? 'selected' : '' ?>" 
                            data-id="<?= (int)$inc['id'] ?>"
                            data-code="<?= Security::e($inc['incident_code']) ?>"
                            data-title="<?= Security::e($inc['title']) ?>"
                            data-desc="<?= Security::e($inc['description'] ?? 'No description logged.') ?>"
                            data-severity="<?= Security::e($inc['severity']) ?>"
                            data-status="<?= Security::e($inc['status']) ?>"
                            data-assigned="<?= Security::e($inc['assigned_name'] ?: 'Unassigned') ?>"
                            data-asset="<?= Security::e($inc['affected_system'] ?: 'General Infrastructure') ?>"
                            data-detected="<?= date('M d, Y • H:i', strtotime($inc['detected_at'])) ?>"
                            data-evidence="<?= (int)($inc['evidence_count'] ?? 0) ?>"
                            data-iocs="<?= (int)($inc['ioc_count'] ?? 0) ?>"
                            data-findings="<?= (int)($inc['findings_count'] ?? 0) ?>"
                            style="cursor: pointer;">
                            <td>
                                <div style="display: flex; flex-direction: column;">
                                    <span style="font-weight: 600; color: var(--text-primary); font-size: 0.83rem;"><?= Security::e($inc['title']) ?></span>
                                    <span style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 0.72rem; color: var(--cyan-primary);"><?= Security::e($inc['incident_code']) ?> &bull; <?= Security::e($inc['affected_system']) ?></span>
                                </div>
                            </td>
                            <td><?= View::severityBadge($inc['severity']) ?></td>
                            <td><?= View::statusBadge($inc['status']) ?></td>
                            <td style="font-size: 0.75rem; color: var(--text-secondary);"><?= Security::e($inc['assigned_name'] ?: 'Unassigned') ?></td>
                            <td>
                                <a href="/incidents/view?id=<?= (int)$inc['id'] ?>" class="btn btn-secondary btn-sm" title="Open Case">View</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Right: Selected Incident Detail (Figma Selected Incident Detail) -->
    <div class="fx-detail-panel" id="incidentDetailPanel">
        <?php if ($selectedInc): ?>
        <div class="detail-panel-header">
            <div>
                <div class="detail-meta-label">SELECTED CASE</div>
                <div id="detailCaseCode" style="font-size: 1.15rem; font-weight: 700; color: var(--cyan-primary); font-family: ui-monospace, SFMono-Regular, monospace; margin-top: 2px;">
                    <?= Security::e($selectedInc['incident_code']) ?>
                </div>
            </div>
            <div id="detailBadgeContainer">
                <?= View::severityBadge($selectedInc['severity']) ?>
            </div>
        </div>

        <div>
            <h2 id="detailTitle" class="detail-panel-title"><?= Security::e($selectedInc['title']) ?></h2>
            <p id="detailDesc" style="font-size: 0.78rem; color: var(--text-secondary); margin-top: 0.4rem; line-height: 1.45;">
                <?= Security::e($selectedInc['description'] ?: 'Repeated authentication anomalies detected on privileged perimeter assets.') ?>
            </p>
        </div>

        <div class="detail-meta-list" style="background-color: var(--bg-surface-alt); padding: 0.85rem 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle);">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div class="detail-meta-item">
                    <span class="detail-meta-label">STATUS</span>
                    <span id="detailStatus" class="detail-meta-value" style="color: var(--cyan-primary); font-weight: 600;"><?= Security::e($selectedInc['status']) ?></span>
                </div>
                <div class="detail-meta-item">
                    <span class="detail-meta-label">ASSIGNED TO</span>
                    <span id="detailAssigned" class="detail-meta-value"><?= Security::e($selectedInc['assigned_name'] ?: 'Unassigned') ?></span>
                </div>
                <div class="detail-meta-item">
                    <span class="detail-meta-label">DETECTED</span>
                    <span id="detailDetected" class="detail-meta-value" style="font-size: 0.75rem;"><?= date('M d, Y • H:i', strtotime($selectedInc['detected_at'])) ?></span>
                </div>
                <div class="detail-meta-item">
                    <span class="detail-meta-label">AFFECTED ASSET</span>
                    <span id="detailAsset" class="detail-meta-value" style="font-family: ui-monospace, monospace; font-size: 0.75rem;"><?= Security::e($selectedInc['affected_system']) ?></span>
                </div>
            </div>
        </div>

        <div>
            <div class="detail-meta-label" style="margin-bottom: 0.5rem;">EVIDENCE & INDICATORS</div>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem; text-align: center;">
                <div style="background-color: var(--bg-surface-alt); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 0.65rem 0.4rem;">
                    <div id="detailEvidenceCount" style="font-size: 1.15rem; font-weight: 700; color: var(--text-primary);"><?= sprintf('%02d', (int)($selectedInc['evidence_count'] ?? 0)) ?></div>
                    <div style="font-size: 0.65rem; color: var(--text-secondary); margin-top: 2px;">Evidence</div>
                </div>
                <div style="background-color: var(--bg-surface-alt); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 0.65rem 0.4rem;">
                    <div id="detailIocCount" style="font-size: 1.15rem; font-weight: 700; color: var(--cyan-primary);"><?= sprintf('%02d', (int)($selectedInc['ioc_count'] ?? 0)) ?></div>
                    <div style="font-size: 0.65rem; color: var(--text-secondary); margin-top: 2px;">IOCs</div>
                </div>
                <div style="background-color: var(--bg-surface-alt); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 0.65rem 0.4rem;">
                    <div id="detailFindingsCount" style="font-size: 1.15rem; font-weight: 700; color: var(--amber-accent);"><?= sprintf('%02d', (int)($selectedInc['findings_count'] ?? 0)) ?></div>
                    <div style="font-size: 0.65rem; color: var(--text-secondary); margin-top: 2px;">Findings</div>
                </div>
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 0.35rem;">
            <a id="detailOpenLink" href="/incidents/view?id=<?= (int)$selectedInc['id'] ?>" class="btn btn-primary" style="width: 100%;">
                Open investigation workspace &rarr;
            </a>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                <a id="detailTimelineLink" href="/timeline?incident_id=<?= (int)$selectedInc['id'] ?>" class="btn btn-secondary btn-sm" style="width: 100%;">
                    Timeline
                </a>
                <a id="detailEditLink" href="/incidents/edit?id=<?= (int)$selectedInc['id'] ?>" class="btn btn-secondary btn-sm" style="width: 100%;">
                    Edit case
                </a>
            </div>
        </div>
        <?php else: ?>
        <div style="text-align: center; color: var(--text-muted); padding: 2rem;">No incident selected.</div>
        <?php endif; ?>
    </div>
</div>

<!-- Interactive Selection Script -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const rows = document.querySelectorAll('.incident-row');
    rows.forEach(row => {
        row.addEventListener('click', (e) => {
            // Prevent if clicked directly on an anchor link
            if (e.target.tagName.toLowerCase() === 'a') return;

            rows.forEach(r => r.classList.remove('selected'));
            row.classList.add('selected');

            const id = row.getAttribute('data-id');
            const code = row.getAttribute('data-code');
            const title = row.getAttribute('data-title');
            const desc = row.getAttribute('data-desc');
            const severity = row.getAttribute('data-severity');
            const status = row.getAttribute('data-status');
            const assigned = row.getAttribute('data-assigned');
            const asset = row.getAttribute('data-asset');
            const detected = row.getAttribute('data-detected');
            const evidence = row.getAttribute('data-evidence');
            const iocs = row.getAttribute('data-iocs');
            const findings = row.getAttribute('data-findings');

            const pad = (n) => String(n).padStart(2, '0');

            document.getElementById('detailCaseCode').textContent = code;
            document.getElementById('detailTitle').textContent = title;
            document.getElementById('detailDesc').textContent = desc;
            document.getElementById('detailStatus').textContent = status;
            document.getElementById('detailAssigned').textContent = assigned;
            document.getElementById('detailDetected').textContent = detected;
            document.getElementById('detailAsset').textContent = asset;
            document.getElementById('detailEvidenceCount').textContent = pad(evidence);
            document.getElementById('detailIocCount').textContent = pad(iocs);
            document.getElementById('detailFindingsCount').textContent = pad(findings);

            document.getElementById('detailOpenLink').href = `/incidents/view?id=${id}`;
            document.getElementById('detailTimelineLink').href = `/timeline?incident_id=${id}`;
            document.getElementById('detailEditLink').href = `/incidents/edit?id=${id}`;

            const badgeSev = document.getElementById('detailBadgeContainer');
            const sevClass = {
                'Critical': 'badge-critical',
                'High': 'badge-high',
                'Medium': 'badge-medium',
                'Low': 'badge-low'
            }[severity] || 'badge-medium';
            badgeSev.innerHTML = `<span class="badge ${sevClass}"><i class="dot"></i>${severity}</span>`;
        });
    });
});
</script>
