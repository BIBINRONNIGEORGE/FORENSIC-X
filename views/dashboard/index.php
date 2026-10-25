<?php
/**
 * FORENSIC-X SOC Operations Dashboard
 */
?>
<!-- STATS ROW 1: INCIDENTS & THREATS -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <span>Total Incidents</span>
            <div class="stat-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--cyan-primary)" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/></svg>
            </div>
        </div>
        <div class="stat-value"><?= (int)$incidentStats['total'] ?></div>
        <div class="stat-desc"><?= (int)$incidentStats['open'] ?> Active / In Triage</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span>Critical & High</span>
            <div class="stat-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#EF4444" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
        </div>
        <div class="stat-value" style="color: #EF4444;"><?= (int)$incidentStats['critical_high'] ?></div>
        <div class="stat-desc">Priority Investigation Required</div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span>Evidence Items</span>
            <div class="stat-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg>
            </div>
        </div>
        <div class="stat-value"><?= (int)$evidenceStats['total'] ?></div>
        <div class="stat-desc">
            <span style="color: #10B981; font-weight: 600;"><?= (int)$evidenceStats['verified'] ?> Verified</span> | 
            <span style="color: <?= $evidenceStats['mismatch'] > 0 ? '#EF4444' : 'var(--text-muted)' ?>; font-weight: 600;"><?= (int)$evidenceStats['mismatch'] ?> Mismatched</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <span>IOC Repository</span>
            <div class="stat-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--blue-accent)" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/></svg>
            </div>
        </div>
        <div class="stat-value"><?= (int)$iocStats['total'] ?></div>
        <div class="stat-desc"><span style="color: var(--cyan-primary); font-weight: 600;"><?= (int)$iocStats['matches'] ?></span> Evidence Matches Found</div>
    </div>
</div>

<!-- CHARTS ROW -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 1.5rem; margin-bottom: 1.75rem;">
    <!-- Severity Distribution Chart -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 2a10 10 0 0 1 10 10"/></svg>
                Incident Severity Distribution
            </div>
        </div>
        <div class="card-body" style="height: 240px; position: relative;">
            <?php if ($incidentStats['total'] > 0): ?>
            <canvas id="severityChart"></canvas>
            <?php else: ?>
            <div style="display: flex; height: 100%; align-items: center; justify-content: center; color: var(--text-muted);">
                No incident data available yet.
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Incident Status Breakdown Chart -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                Incident Lifecycle Breakdown
            </div>
        </div>
        <div class="card-body" style="height: 240px; position: relative;">
            <?php if ($incidentStats['total'] > 0): ?>
            <canvas id="statusChart"></canvas>
            <?php else: ?>
            <div style="display: flex; height: 100%; align-items: center; justify-content: center; color: var(--text-muted);">
                No incident lifecycle data recorded.
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- RECENT INCIDENTS TABLE -->
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/></svg>
            Active Forensic Incidents
        </div>
        <a href="/incidents" class="btn btn-secondary btn-sm">View All Incidents</a>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Severity</th>
                    <th>Status</th>
                    <th>Investigator</th>
                    <th>Detected</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentIncidents)): ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                        No incidents recorded. Click "+ New Incident" to initiate your first investigation.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($recentIncidents as $inc): ?>
                    <tr>
                        <td>
                            <a href="/incidents/view?id=<?= (int)$inc['id'] ?>" style="color: var(--cyan-primary); font-family: monospace; font-weight: 600; text-decoration: none;">
                                <?= Security::e($inc['incident_code']) ?>
                            </a>
                        </td>
                        <td style="font-weight: 600; color: var(--text-primary); max-width: 250px;">
                            <?= Security::e($inc['title']) ?>
                        </td>
                        <td><?= Security::e($inc['category']) ?></td>
                        <td><?= View::severityBadge($inc['severity']) ?></td>
                        <td><?= View::statusBadge($inc['status']) ?></td>
                        <td><?= Security::e($inc['assigned_name'] ?: 'Unassigned') ?></td>
                        <td style="font-size: 0.75rem;"><?= date('M d, H:i', strtotime($inc['detected_at'])) ?></td>
                        <td>
                            <a href="/incidents/view?id=<?= (int)$inc['id'] ?>" class="btn btn-secondary btn-sm">Inspect</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- TWO-COLUMN RECENT ACTIVITY & AUDIT TRAIL -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem;">
    <!-- Recent Timeline Events -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Recent Investigation Activity
            </div>
            <a href="/timeline" class="btn btn-secondary btn-sm">Full Timeline</a>
        </div>
        <div class="card-body">
            <?php if (empty($recentTimeline)): ?>
            <div style="color: var(--text-muted); text-align: center; padding: 1.5rem;">No recent activities recorded.</div>
            <?php else: ?>
            <div class="timeline" style="padding-left: 1.5rem;">
                <?php foreach ($recentTimeline as $ev): ?>
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-time"><?= date('M d, Y H:i:s', strtotime($ev['event_time'])) ?></div>
                    <div class="timeline-title"><?= Security::e($ev['event_title']) ?></div>
                    <div class="timeline-desc"><?= Security::e($ev['description']) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Security Audit Trail -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Recent Security Audit Events
            </div>
            <a href="/audit" class="btn btn-secondary btn-sm">All Audits</a>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>User</th>
                        <th>Outcome</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentAudits)): ?>
                    <tr><td colspan="4" style="text-align: center; color: var(--text-muted);">No audit events recorded.</td></tr>
                    <?php else: ?>
                        <?php foreach ($recentAudits as $log): ?>
                        <tr>
                            <td style="font-weight: 600; color: var(--text-primary); font-size: 0.78rem;">
                                <?= Security::e($log['action']) ?>
                            </td>
                            <td><?= Security::e($log['username']) ?></td>
                            <td>
                                <span class="badge <?= $log['outcome'] === 'SUCCESS' ? 'badge-verified' : ($log['outcome'] === 'DENIED' ? 'badge-critical' : 'badge-mismatch') ?>">
                                    <?= Security::e($log['outcome']) ?>
                                </span>
                            </td>
                            <td style="font-size: 0.72rem;"><?= date('H:i:s', strtotime($log['created_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
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
