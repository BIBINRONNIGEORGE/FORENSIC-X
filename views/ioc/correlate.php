<?php
/**
 * FORENSIC-X IOC Correlation Engine & Match Matrix View
 */
?>
<div class="card" style="border-left: 4px solid var(--cyan-primary);">
    <div class="card-body" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.25rem;">
                Cross-Evidence IOC Correlation Engine
            </h2>
            <div style="font-size: 0.82rem; color: var(--text-secondary); max-width: 650px;">
                Executes case-insensitive regex parsing across all text-based forensic evidence items in the vault (syslog, auth logs, web access logs, scripts) against the local Indicator of Compromise repository.
            </div>
        </div>
        <form method="POST" action="/ioc/correlate/run">
            <?= CSRF::input() ?>
            <button type="submit" id="btn-run-correlation" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                Execute Correlation Engine
            </button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title">
            Correlated Threat Matches (<?= count($matches) ?> Identified)
        </div>
        <a href="/ioc" class="btn btn-secondary btn-sm">Manage Indicators</a>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Matched IOC</th>
                    <th>Type / Confidence</th>
                    <th>Source Evidence</th>
                    <th>Associated Case</th>
                    <th>Line #</th>
                    <th>Contextual Match Snippet</th>
                    <th>Match Time</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($matches)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                        No correlation matches currently recorded. Click "Execute Correlation Engine" to scan all vault evidence.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($matches as $m): ?>
                    <tr>
                        <td>
                            <span class="code-block" style="color: #F8FAFC; font-weight: 600;"><?= Security::e($m['matched_value']) ?></span>
                        </td>
                        <td>
                            <span class="badge badge-neutral"><?= Security::e($m['indicator_type']) ?></span>
                            <?= View::severityBadge($m['confidence']) ?>
                        </td>
                        <td>
                            <a href="/evidence/view?id=<?= (int)$m['evidence_id'] ?>" style="color: var(--cyan-primary); text-decoration: none; font-weight: 600; font-size: 0.8rem;">
                                <?= Security::e($m['evidence_name']) ?>
                            </a>
                            <div style="font-size: 0.7rem; color: var(--text-muted); font-family: monospace;"><?= Security::e($m['evidence_code']) ?></div>
                        </td>
                        <td>
                            <a href="/incidents/view?id=<?= (int)$m['incident_id'] ?>" style="color: var(--text-secondary); text-decoration: none; font-size: 0.8rem;">
                                <?= Security::e($m['incident_code']) ?>
                            </a>
                        </td>
                        <td style="font-family: monospace; font-size: 0.75rem; color: var(--text-muted);">
                            L#<?= (int)$m['line_number'] ?>
                        </td>
                        <td>
                            <div class="code-block" style="max-width: 450px; overflow-x: auto; white-space: nowrap; color: #38BDF8;">
                                <?= Security::e($m['context_snippet']) ?>
                            </div>
                        </td>
                        <td style="font-size: 0.72rem;"><?= date('M d, H:i:s', strtotime($m['matched_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
