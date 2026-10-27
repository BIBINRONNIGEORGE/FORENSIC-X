<?php
/**
 * FORENSIC-X SOC Operations Dashboard
 * Implemented directly from Figma Source of Truth (ROORaWXDtD6bCWfH7NbtGO — Frame 2:16)
 */

$totalIncidents = max(1, (int)$incidentStats['total']);
$sevCritical = (int)($incidentStats['by_severity']['Critical'] ?? 0);
$sevHigh = (int)($incidentStats['by_severity']['High'] ?? 0);
$sevMedium = (int)($incidentStats['by_severity']['Medium'] ?? 0);
$sevLow = (int)($incidentStats['by_severity']['Low'] ?? 0);

$pctCritical = round(($sevCritical / $totalIncidents) * 100);
$pctHigh = round(($sevHigh / $totalIncidents) * 100);
$pctMedium = round(($sevMedium / $totalIncidents) * 100);
$pctLow = round(($sevLow / $totalIncidents) * 100);

$totalEvidence = max(1, (int)$evidenceStats['total']);
$verifiedEvidence = (int)$evidenceStats['verified'];
$integrityPct = round(($verifiedEvidence / $totalEvidence) * 100, 1);
?>

<!-- 1. PAGE HEADING (Figma Page Heading) -->
<div class="page-heading-row">
    <div class="page-heading-copy">
        <h1 class="page-title">Overview</h1>
        <div class="page-subtitle">Monitor incidents, evidence integrity, and investigation activity.</div>
    </div>
    <div class="page-actions">
        <a href="/reports" class="btn btn-secondary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Export overview
        </a>
        <?php if (Auth::can('create_incident')): ?>
        <a href="/incidents/create" class="btn btn-primary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#06111c" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Create incident
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- 2. INVESTIGATION PIPELINE (Figma Investigation Workflow) -->
<div class="workflow-pipeline-card">
    <div class="workflow-pipeline-header">INVESTIGATION PIPELINE</div>
    <div class="workflow-pipeline-steps">
        <a href="/incidents" class="workflow-step active" title="Triage and classify incoming incidents">
            <div class="workflow-step-num">01</div>
            <div class="workflow-step-name">DETECT</div>
        </a>
        <a href="/evidence" class="workflow-step" title="Preserve and ingest digital evidence">
            <div class="workflow-step-num">02</div>
            <div class="workflow-step-name">INVESTIGATE</div>
        </a>
        <a href="/logs" class="workflow-step" title="Run log analysis and rule engines">
            <div class="workflow-step-num">03</div>
            <div class="workflow-step-name">ANALYZE</div>
        </a>
        <a href="/ioc/correlate" class="workflow-step" title="Correlate IOCs against ingested evidence">
            <div class="workflow-step-num">04</div>
            <div class="workflow-step-name">CORRELATE</div>
        </a>
        <a href="/reports" class="workflow-step" title="Generate signed cryptographic reports">
            <div class="workflow-step-num">05</div>
            <div class="workflow-step-name">REPORT</div>
        </a>
    </div>
</div>

<!-- 3. KPI GRID (Figma KPI Grid - 4 Cards) -->
<div class="kpi-grid">
    <!-- Active incidents -->
    <div class="kpi-card">
        <div class="kpi-header">
            <div class="kpi-icon-well">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/></svg>
            </div>
            <div class="kpi-label">Active incidents</div>
        </div>
        <div class="kpi-value"><?= (int)$incidentStats['total'] ?></div>
        <div class="kpi-footnote <?= $sevCritical > 0 ? 'danger' : '' ?>">
            +<?= (int)$incidentStats['critical_high'] ?> need review
        </div>
    </div>

    <!-- Evidence items -->
    <div class="kpi-card">
        <div class="kpi-header">
            <div class="kpi-icon-well" style="background-color: #0e3553;">
                <svg viewBox="0 0 24 24" fill="none" stroke="var(--cyan-primary)" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
            </div>
            <div class="kpi-label">Evidence items</div>
        </div>
        <div class="kpi-value"><?= (int)$evidenceStats['total'] ?></div>
        <div class="kpi-footnote">
            +<?= (int)$evidenceStats['verified'] ?> verified in vault
        </div>
    </div>

    <!-- IOC matches -->
    <div class="kpi-card">
        <div class="kpi-header">
            <div class="kpi-icon-well" style="background-color: #392657;">
                <svg viewBox="0 0 24 24" fill="none" stroke="var(--cyan-primary)" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
            </div>
            <div class="kpi-label">IOC matches</div>
        </div>
        <div class="kpi-value"><?= (int)$iocStats['matches'] ?></div>
        <div class="kpi-footnote">
            Across <?= (int)$incidentStats['open'] ?> active cases
        </div>
    </div>

    <!-- Evidence integrity -->
    <div class="kpi-card">
        <div class="kpi-header">
            <div class="kpi-icon-well" style="background-color: #0f493d;">
                <svg viewBox="0 0 24 24" fill="none" stroke="var(--cyan-primary)" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
            </div>
            <div class="kpi-label">Evidence integrity</div>
        </div>
        <div class="kpi-value"><?= $integrityPct ?>%</div>
        <div class="kpi-footnote <?= $evidenceStats['mismatch'] > 0 ? 'danger' : '' ?>">
            <?= $evidenceStats['mismatch'] > 0 ? (int)$evidenceStats['mismatch'] . ' items need review' : '0 hash alterations' ?>
        </div>
    </div>
</div>

<!-- 4. ANALYTICS ROW (Figma Analytics Row - Trend + Severity Distribution) -->
<div class="fx-two-col-row">
    <!-- Incident Trend Card -->
    <div class="card">
        <div class="card-header">
            <div class="card-header-left">
                <div class="card-title">Incident trend</div>
                <div class="card-subtitle">Reported incidents over the last 7 days</div>
            </div>
            <div style="background-color: var(--bg-surface-alt); border: 1px solid var(--border-subtle); border-radius: var(--radius-sm); padding: 0.25rem 0.65rem; font-size: 0.68rem; color: var(--text-secondary); font-weight: 600;">
                7 days
            </div>
        </div>
        <div class="card-body" style="height: 200px; position: relative;">
            <canvas id="incidentTrendChart"></canvas>
        </div>
    </div>

    <!-- Severity Distribution Card -->
    <div class="card">
        <div class="card-header">
            <div class="card-header-left">
                <div class="card-title">Severity distribution</div>
                <div class="card-subtitle">Current incident classification</div>
            </div>
        </div>
        <div class="card-body" style="display: flex; flex-direction: column; justify-content: space-around; gap: 0.85rem;">
            <!-- Critical -->
            <div>
                <div style="display: flex; justify-content: space-between; font-size: 0.75rem; margin-bottom: 0.35rem;">
                    <span style="color: var(--red-accent); font-weight: 600;">Critical</span>
                    <span style="color: var(--text-secondary);"><?= $sevCritical ?> (<?= $pctCritical ?>%)</span>
                </div>
                <div style="background-color: var(--bg-surface-alt); height: 6px; border-radius: 3px; overflow: hidden;">
                    <div style="background-color: var(--red-accent); height: 100%; width: <?= max(4, $pctCritical) ?>%; border-radius: 3px;"></div>
                </div>
            </div>

            <!-- High -->
            <div>
                <div style="display: flex; justify-content: space-between; font-size: 0.75rem; margin-bottom: 0.35rem;">
                    <span style="color: var(--amber-accent); font-weight: 600;">High</span>
                    <span style="color: var(--text-secondary);"><?= $sevHigh ?> (<?= $pctHigh ?>%)</span>
                </div>
                <div style="background-color: var(--bg-surface-alt); height: 6px; border-radius: 3px; overflow: hidden;">
                    <div style="background-color: var(--amber-accent); height: 100%; width: <?= max(4, $pctHigh) ?>%; border-radius: 3px;"></div>
                </div>
            </div>

            <!-- Medium -->
            <div>
                <div style="display: flex; justify-content: space-between; font-size: 0.75rem; margin-bottom: 0.35rem;">
                    <span style="color: var(--blue-accent); font-weight: 600;">Medium</span>
                    <span style="color: var(--text-secondary);"><?= $sevMedium ?> (<?= $pctMedium ?>%)</span>
                </div>
                <div style="background-color: var(--bg-surface-alt); height: 6px; border-radius: 3px; overflow: hidden;">
                    <div style="background-color: var(--blue-accent); height: 100%; width: <?= max(4, $pctMedium) ?>%; border-radius: 3px;"></div>
                </div>
            </div>

            <!-- Low -->
            <div>
                <div style="display: flex; justify-content: space-between; font-size: 0.75rem; margin-bottom: 0.35rem;">
                    <span style="color: var(--text-secondary); font-weight: 600;">Low</span>
                    <span style="color: var(--text-secondary);"><?= $sevLow ?> (<?= $pctLow ?>%)</span>
                </div>
                <div style="background-color: var(--bg-surface-alt); height: 6px; border-radius: 3px; overflow: hidden;">
                    <div style="background-color: var(--text-muted); height: 100%; width: <?= max(4, $pctLow) ?>%; border-radius: 3px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 5. RECENT ACTIVITY ROW (Figma Recent Incidents + Investigation Activity) -->
<div class="fx-two-col-row">
    <!-- Recent Incidents Table Card -->
    <div class="card">
        <div class="card-header">
            <div class="card-header-left">
                <div class="card-title">Recent incidents</div>
                <div class="card-subtitle">Latest cases undergoing active investigation</div>
            </div>
            <a href="/incidents" style="font-size: 0.75rem; color: var(--cyan-primary); text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                View all &rarr;
            </a>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>INCIDENT</th>
                        <th>SEVERITY</th>
                        <th>STATUS</th>
                        <th>UPDATED</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentIncidents)): ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                            No incident records available.
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($recentIncidents as $inc): ?>
                        <tr>
                            <td>
                                <a href="/incidents/view?id=<?= (int)$inc['id'] ?>" style="text-decoration: none; display: flex; flex-direction: column;">
                                    <span style="color: var(--text-primary); font-weight: 600; font-size: 0.82rem;"><?= Security::e($inc['title']) ?></span>
                                    <span style="font-family: ui-monospace, SFMono-Regular, monospace; font-size: 0.7rem; color: var(--text-secondary);"><?= Security::e($inc['incident_code']) ?></span>
                                </a>
                            </td>
                            <td><?= View::severityBadge($inc['severity']) ?></td>
                            <td><?= View::statusBadge($inc['status']) ?></td>
                            <td style="font-size: 0.72rem; color: var(--text-secondary);">
                                <?= date('M d, H:i', strtotime($inc['detected_at'])) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Investigation Activity Card -->
    <div class="card">
        <div class="card-header">
            <div class="card-header-left">
                <div class="card-title">Investigation activity</div>
                <div class="card-subtitle">Latest updates across your workspace</div>
            </div>
            <a href="/timeline" style="font-size: 0.75rem; color: var(--cyan-primary); text-decoration: none; font-weight: 600;">
                Full feed &rarr;
            </a>
        </div>
        <div class="card-body" style="padding: 1rem 1.25rem;">
            <?php if (empty($recentTimeline)): ?>
            <div style="color: var(--text-muted); text-align: center; padding: 2rem;">No recent activities logged.</div>
            <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 0.95rem;">
                <?php 
                $dotColors = ['#2ed6a1', '#21d4f3', '#f7b84b', '#a277ff'];
                foreach ($recentTimeline as $idx => $ev): 
                    $color = $dotColors[$idx % count($dotColors)];
                ?>
                <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                    <div style="width: 8px; height: 8px; border-radius: 50%; background-color: <?= $color ?>; box-shadow: 0 0 6px <?= $color ?>; margin-top: 5px; flex-shrink: 0;"></div>
                    <div style="flex: 1; min-width: 0;">
                        <div style="font-size: 0.81rem; font-weight: 600; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <?= Security::e($ev['event_title']) ?>
                        </div>
                        <div style="font-size: 0.7rem; color: var(--text-secondary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <?= Security::e($ev['description']) ?>
                        </div>
                    </div>
                    <div style="font-size: 0.68rem; color: var(--text-muted); font-family: ui-monospace, monospace; flex-shrink: 0;">
                        <?= date('H:i', strtotime($ev['event_time'])) ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    initDashboardCharts(
        <?= json_encode($incidentStats['by_severity']) ?>,
        <?= json_encode($incidentStats['by_status']) ?>
    );
});
</script>
