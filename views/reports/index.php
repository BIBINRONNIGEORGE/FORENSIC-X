<?php
/**
 * FORENSIC-X Reports Inventory View
 */
?>
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
            Forensic Investigation Reports
        </div>
        <?php if (Auth::can('generate_report')): ?>
        <a href="/reports/generate" class="btn btn-primary btn-sm">
            + Generate Forensic Report
        </a>
        <?php endif; ?>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Report ID</th>
                    <th>Report Title</th>
                    <th>Associated Case</th>
                    <th>Severity</th>
                    <th>Prepared By</th>
                    <th>Generated Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reports)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                        No formal forensic reports generated yet. Click "+ Generate Forensic Report" to compile a case report.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($reports as $rpt): ?>
                    <tr>
                        <td>
                            <a href="/reports/view?id=<?= (int)$rpt['id'] ?>" style="color: var(--cyan-primary); font-family: monospace; font-weight: 700; text-decoration: none;">
                                <?= Security::e($rpt['report_code']) ?>
                            </a>
                        </td>
                        <td style="font-weight: 600; color: var(--text-primary);">
                            <?= Security::e($rpt['title']) ?>
                        </td>
                        <td>
                            <a href="/incidents/view?id=<?= (int)$rpt['incident_id'] ?>" style="color: var(--text-secondary); text-decoration: none; font-size: 0.8rem;">
                                <?= Security::e($rpt['incident_code']) ?>
                            </a>
                        </td>
                        <td><?= View::severityBadge($rpt['incident_severity']) ?></td>
                        <td><?= Security::e($rpt['generator_name'] ?: $rpt['generator_username']) ?></td>
                        <td style="font-size: 0.75rem;"><?= date('Y-m-d H:i', strtotime($rpt['created_at'])) ?></td>
                        <td>
                            <a href="/reports/view?id=<?= (int)$rpt['id'] ?>" class="btn btn-secondary btn-sm">
                                View / Print Report
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
