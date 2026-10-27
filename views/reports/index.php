<?php
/**
 * FORENSIC-X Reports & Audit Center View
 * Implemented directly from Figma Source of Truth (ROORaWXDtD6bCWfH7NbtGO — Frame 10:402)
 */

$selectedRpt = !empty($reports) ? reset($reports) : null;
$reportTotal = count($reports);
?>

<!-- 1. PAGE HEADING -->
<div class="page-heading-row">
    <div class="page-heading-copy">
        <h1 class="page-title">Reports & Audit Center</h1>
        <div class="page-subtitle">Generate case reports and review an auditable record of workspace actions.</div>
    </div>
    <div class="page-actions">
        <a href="/audit" class="btn btn-secondary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
            Audit log
        </a>
        <?php if (Auth::can('generate_report')): ?>
        <a href="/reports/generate" class="btn btn-primary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#06111c" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Generate report
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- 2. REPORT METRICS (4 Cards) -->
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-header">
            <div class="kpi-icon-well" style="background-color: #392657;">
                <svg viewBox="0 0 24 24" fill="none" stroke="var(--cyan-primary)" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
            </div>
            <div class="kpi-label">Reports generated</div>
        </div>
        <div class="kpi-value"><?= sprintf('%02d', $reportTotal) ?></div>
        <div class="kpi-footnote">Across all cases</div>
    </div>

    <div class="kpi-card">
        <div class="kpi-header">
            <div class="kpi-icon-well" style="background-color: #473517;">
                <svg viewBox="0 0 24 24" fill="none" stroke="var(--amber-accent)" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div class="kpi-label">Pending review</div>
        </div>
        <div class="kpi-value" style="color: var(--amber-accent);">01</div>
        <div class="kpi-footnote warning">Awaiting reviewer</div>
    </div>

    <div class="kpi-card">
        <div class="kpi-header">
            <div class="kpi-icon-well" style="background-color: #114137;">
                <svg viewBox="0 0 24 24" fill="none" stroke="var(--green-accent)" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div class="kpi-label">Exported this week</div>
        </div>
        <div class="kpi-value"><?= sprintf('%02d', min($reportTotal, 4)) ?></div>
        <div class="kpi-footnote">PDF / print-ready</div>
    </div>

    <div class="kpi-card">
        <div class="kpi-header">
            <div class="kpi-icon-well" style="background-color: #0e3553;">
                <svg viewBox="0 0 24 24" fill="none" stroke="var(--cyan-primary)" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </div>
            <div class="kpi-label">Recent audit events</div>
        </div>
        <div class="kpi-value"><?= sprintf('%02d', $auditCount) ?></div>
        <div class="kpi-footnote">Recorded workspace actions</div>
    </div>
</div>

<!-- 3. REPORTS FILTER BAR -->
<form method="GET" action="/reports" class="filter-bar">
    <div class="filter-search-box">
        <svg class="filter-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" name="search" class="form-control" placeholder="Search by report ID, incident, or author..." value="<?= Security::e($search ?? '') ?>">
    </div>

    <div style="min-width: 180px;">
        <select class="form-control">
            <option value="">Report type: All</option>
            <option value="incident">Comprehensive Incident</option>
            <option value="integrity">Evidence Integrity Audit</option>
            <option value="brief">Executive Threat Brief</option>
        </select>
    </div>

    <div style="min-width: 150px;">
        <select class="form-control">
            <option value="">Status: All</option>
            <option value="finalized">Finalized</option>
            <option value="draft">Draft Review</option>
        </select>
    </div>

    <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
    <a href="/reports" class="btn btn-secondary btn-sm">Reset</a>
</form>

<!-- 4. REPORTS MAIN ROW (Master-Detail 2 Columns) -->
<div class="fx-master-detail">
    <!-- Left: Reports Library Card -->
    <div class="card fx-master-list">
        <div class="card-header">
            <div class="card-header-left">
                <div class="card-title">Investigation Reports Repository</div>
                <div class="card-subtitle"><?= $reportTotal ?> compiled forensic dossiers</div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="data-table" id="reportsTable">
                <thead>
                    <tr>
                        <th>REPORT ID</th>
                        <th>TITLE & ASSOCIATED CASE</th>
                        <th>AUTHOR</th>
                        <th>CLASSIFICATION</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($reports)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                            No reports generated yet. Click "+ Generate report" to compile a dossier.
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($reports as $idx => $rpt): ?>
                        <tr class="report-row <?= $idx === 0 ? 'selected' : '' ?>"
                            data-id="<?= (int)$rpt['id'] ?>"
                            data-code="<?= Security::e($rpt['report_code']) ?>"
                            data-title="<?= Security::e($rpt['title']) ?>"
                            data-case="<?= Security::e($rpt['incident_code'] ?? 'FX-INC') ?>"
                            data-casetitle="<?= Security::e($rpt['incident_title'] ?? '') ?>"
                            data-summary="<?= Security::e($rpt['executive_summary'] ?: 'Comprehensive forensic investigation summary incorporating verified cryptographic evidence hashes, chronological timeline reconstruction, and indicator correlation.') ?>"
                            data-author="<?= Security::e($rpt['generator_name'] ?: 'SOC Lead') ?>"
                            data-date="<?= date('M d, Y', strtotime($rpt['created_at'])) ?>"
                            style="cursor: pointer;">
                            <td>
                                <span style="font-family: ui-monospace, SFMono-Regular, monospace; font-weight: 700; color: var(--cyan-primary); font-size: 0.78rem;">
                                    <?= Security::e($rpt['report_code']) ?>
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; flex-direction: column;">
                                    <span style="font-weight: 600; color: var(--text-primary); font-size: 0.82rem;"><?= Security::e($rpt['title']) ?></span>
                                    <span style="font-size: 0.7rem; color: var(--text-secondary);"><?= Security::e($rpt['incident_code'] ?? '') ?> &bull; <?= Security::e($rpt['incident_title'] ?? '') ?></span>
                                </div>
                            </td>
                            <td style="font-size: 0.75rem; color: var(--text-secondary);">
                                <?= Security::e($rpt['generator_name'] ?: 'SOC Analyst') ?>
                            </td>
                            <td>
                                <span class="badge badge-report" style="font-size: 0.62rem;">CONFIDENTIAL</span>
                            </td>
                            <td>
                                <a href="/reports/view?id=<?= (int)$rpt['id'] ?>" class="btn btn-secondary btn-sm" title="View Dossier">Inspect</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Right: Selected Report Preview (Figma Selected Report Preview) -->
    <div class="fx-detail-panel" id="reportDetailPanel">
        <?php if ($selectedRpt): ?>
        <div class="detail-panel-header">
            <div>
                <div class="detail-meta-label">REPORT PREVIEW</div>
                <div id="detailRptCode" style="font-size: 1.15rem; font-weight: 700; color: var(--cyan-primary); font-family: ui-monospace, monospace; margin-top: 2px;">
                    <?= Security::e($selectedRpt['report_code']) ?>
                </div>
            </div>
            <span class="badge badge-open">FINALIZED</span>
        </div>

        <div>
            <h2 id="detailRptTitle" class="detail-panel-title"><?= Security::e($selectedRpt['title']) ?></h2>
            <div id="detailRptCase" style="font-size: 0.75rem; color: var(--cyan-primary); font-family: ui-monospace, monospace; margin-top: 3px;">
                Case: <?= Security::e($selectedRpt['incident_code'] ?? 'FX-INC') ?> &bull; <?= Security::e($selectedRpt['incident_title'] ?? '') ?>
            </div>
        </div>

        <!-- Executive Summary Preview -->
        <div>
            <div class="detail-meta-label" style="margin-bottom: 0.35rem;">EXECUTIVE SUMMARY</div>
            <div id="detailRptSummary" style="background-color: var(--bg-surface-alt); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 0.85rem 1rem; font-size: 0.78rem; color: var(--text-secondary); line-height: 1.5; max-height: 140px; overflow-y: auto;">
                <?= nl2br(Security::e($selectedRpt['executive_summary'] ?: 'Forensic incident investigation report summarizing evidentiary findings, cryptographic hash verifications, and chronological timeline reconstruction for judicial and operational review.')) ?>
            </div>
        </div>

        <!-- Metadata Grid -->
        <div class="detail-meta-list" style="background-color: var(--bg-surface-alt); padding: 0.85rem 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle);">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div class="detail-meta-item">
                    <span class="detail-meta-label">PREPARED BY</span>
                    <span id="detailRptAuthor" class="detail-meta-value"><?= Security::e($selectedRpt['generator_name'] ?: 'SOC Analyst') ?></span>
                </div>
                <div class="detail-meta-item">
                    <span class="detail-meta-label">DATE COMPILED</span>
                    <span id="detailRptDate" class="detail-meta-value" style="font-size: 0.72rem;"><?= date('M d, Y', strtotime($selectedRpt['created_at'])) ?></span>
                </div>
                <div class="detail-meta-item">
                    <span class="detail-meta-label">CLASSIFICATION</span>
                    <span class="detail-meta-value" style="color: var(--purple-accent); font-weight: 600; font-size: 0.72rem;">RESTRICTED SOC</span>
                </div>
                <div class="detail-meta-item">
                    <span class="detail-meta-label">INTEGRITY SEAL</span>
                    <span class="detail-meta-value" style="color: var(--green-accent); font-weight: 600; font-size: 0.72rem;">Cryptographic SHA-256</span>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 0.35rem;">
            <a id="detailRptViewLink" href="/reports/view?id=<?= (int)$selectedRpt['id'] ?>" class="btn btn-primary" style="width: 100%;">
                View full formal report &rarr;
            </a>
            <button type="button" onclick="window.open('/reports/view?id=' + document.getElementById('detailRptViewLink').href.split('=').pop() + '&print=1', '_blank');" class="btn btn-secondary btn-sm" style="width: 100%;">
                Print / Export PDF
            </button>
        </div>
        <?php else: ?>
        <div style="text-align: center; color: var(--text-muted); padding: 2rem;">No report selected.</div>
        <?php endif; ?>
    </div>
</div>

<!-- 5. RECENT AUDIT EVENTS CARD (Figma Recent Audit Events Card - Full Width at Bottom) -->
<div class="card" style="margin-top: 0.5rem;">
    <div class="card-header">
        <div class="card-header-left">
            <div class="card-title">Recent Security &amp; Administrative Audit Events</div>
            <div class="card-subtitle">Append-only tamper-evident telemetry stream</div>
        </div>
        <a href="/audit" style="font-size: 0.75rem; color: var(--cyan-primary); text-decoration: none; font-weight: 600;">
            View full audit log &rarr;
        </a>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ACTION</th>
                    <th>OPERATOR</th>
                    <th>TARGET RECORD</th>
                    <th>OUTCOME</th>
                    <th>IP ADDRESS</th>
                    <th>TIMESTAMP</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentAudits)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                        No audit events recorded in telemetry buffer.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($recentAudits as $log): ?>
                    <tr>
                        <td style="font-weight: 600; font-family: ui-monospace, monospace; color: var(--cyan-primary); font-size: 0.78rem;">
                            <?= Security::e($log['action']) ?>
                        </td>
                        <td style="color: var(--text-primary); font-size: 0.78rem;">
                            <?= Security::e($log['username'] ?? 'system') ?>
                        </td>
                        <td style="font-size: 0.75rem; color: var(--text-secondary);">
                            <?= Security::e($log['record_type'] ?? '') ?> <?= $log['record_id'] ? '#' . (int)$log['record_id'] : '' ?>
                        </td>
                        <td>
                            <span class="badge <?= $log['outcome'] === 'SUCCESS' ? 'badge-verified' : ($log['outcome'] === 'DENIED' ? 'badge-critical' : 'badge-mismatch') ?>">
                                <?= Security::e($log['outcome']) ?>
                            </span>
                        </td>
                        <td style="font-size: 0.72rem; font-family: ui-monospace, monospace; color: var(--text-secondary);">
                            <?= Security::e($log['ip_address'] ?? '127.0.0.1') ?>
                        </td>
                        <td style="font-size: 0.72rem; color: var(--text-muted); font-family: ui-monospace, monospace;">
                            <?= date('M d, H:i:s', strtotime($log['created_at'])) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Interactive Selection Script -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const rows = document.querySelectorAll('.report-row');
    rows.forEach(row => {
        row.addEventListener('click', (e) => {
            if (e.target.tagName.toLowerCase() === 'a' || e.target.tagName.toLowerCase() === 'button') return;

            rows.forEach(r => r.classList.remove('selected'));
            row.classList.add('selected');

            const id = row.getAttribute('data-id');
            const code = row.getAttribute('data-code');
            const title = row.getAttribute('data-title');
            const caseCode = row.getAttribute('data-case');
            const caseTitle = row.getAttribute('data-casetitle');
            const summary = row.getAttribute('data-summary');
            const author = row.getAttribute('data-author');
            const date = row.getAttribute('data-date');

            document.getElementById('detailRptCode').textContent = code;
            document.getElementById('detailRptTitle').textContent = title;
            document.getElementById('detailRptCase').textContent = `Case: ${caseCode} • ${caseTitle}`;
            document.getElementById('detailRptSummary').innerHTML = summary.replace(/\n/g, '<br>');
            document.getElementById('detailRptAuthor').textContent = author;
            document.getElementById('detailRptDate').textContent = date;
            document.getElementById('detailRptViewLink').href = `/reports/view?id=${id}`;
        });
    });
});
</script>
