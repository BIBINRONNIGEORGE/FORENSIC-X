<?php
/**
 * FORENSIC-X Incidents Inventory View
 */
?>
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/></svg>
            Cybersecurity Incident Case Files
        </div>
        <?php if (Auth::can('create_incident')): ?>
        <a href="/incidents/create" class="btn btn-primary btn-sm">
            + Create New Incident
        </a>
        <?php endif; ?>
    </div>

    <!-- FILTER BAR -->
    <form method="GET" action="/incidents" class="filter-bar" style="margin: 1rem; border-radius: var(--radius-md);">
        <div style="flex: 1; min-width: 200px;">
            <input type="text" name="search" class="form-control" placeholder="Search by ID, title, affected system..." value="<?= Security::e($filters['search'] ?? '') ?>">
        </div>
        
        <div>
            <select name="status" class="form-control">
                <option value="">All Statuses</option>
                <option value="Open" <?= ($filters['status'] ?? '') === 'Open' ? 'selected' : '' ?>>Open</option>
                <option value="Investigating" <?= ($filters['status'] ?? '') === 'Investigating' ? 'selected' : '' ?>>Investigating</option>
                <option value="Contained" <?= ($filters['status'] ?? '') === 'Contained' ? 'selected' : '' ?>>Contained</option>
                <option value="Resolved" <?= ($filters['status'] ?? '') === 'Resolved' ? 'selected' : '' ?>>Resolved</option>
                <option value="Closed" <?= ($filters['status'] ?? '') === 'Closed' ? 'selected' : '' ?>>Closed</option>
            </select>
        </div>

        <div>
            <select name="severity" class="form-control">
                <option value="">All Severities</option>
                <option value="Critical" <?= ($filters['severity'] ?? '') === 'Critical' ? 'selected' : '' ?>>Critical</option>
                <option value="High" <?= ($filters['severity'] ?? '') === 'High' ? 'selected' : '' ?>>High</option>
                <option value="Medium" <?= ($filters['severity'] ?? '') === 'Medium' ? 'selected' : '' ?>>Medium</option>
                <option value="Low" <?= ($filters['severity'] ?? '') === 'Low' ? 'selected' : '' ?>>Low</option>
            </select>
        </div>

        <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
        <a href="/incidents" class="btn btn-secondary btn-sm">Reset</a>
    </form>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Case ID</th>
                    <th>Title & Affected Asset</th>
                    <th>Category</th>
                    <th>Severity</th>
                    <th>Status</th>
                    <th>Artifacts</th>
                    <th>Assigned Lead</th>
                    <th>Reported</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($incidents)): ?>
                <tr>
                    <td colspan="9" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                        No incidents match the selected filter criteria.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($incidents as $inc): ?>
                    <tr>
                        <td>
                            <a href="/incidents/view?id=<?= (int)$inc['id'] ?>" style="color: var(--cyan-primary); font-family: monospace; font-weight: 700; text-decoration: none;">
                                <?= Security::e($inc['incident_code']) ?>
                            </a>
                        </td>
                        <td>
                            <div style="font-weight: 600; color: var(--text-primary);"><?= Security::e($inc['title']) ?></div>
                            <div style="font-size: 0.72rem; color: var(--text-muted); font-family: monospace;">Asset: <?= Security::e($inc['affected_system']) ?></div>
                        </td>
                        <td><?= Security::e($inc['category']) ?></td>
                        <td><?= View::severityBadge($inc['severity']) ?></td>
                        <td><?= View::statusBadge($inc['status']) ?></td>
                        <td style="font-size: 0.75rem;">
                            <span title="Evidence items">📦 <?= (int)$inc['evidence_count'] ?></span> &nbsp;
                            <span title="IOCs">🎯 <?= (int)$inc['ioc_count'] ?></span> &nbsp;
                            <span title="Findings">🚩 <?= (int)$inc['findings_count'] ?></span>
                        </td>
                        <td><?= Security::e($inc['assigned_name'] ?: 'Unassigned') ?></td>
                        <td style="font-size: 0.75rem;"><?= date('Y-m-d H:i', strtotime($inc['detected_at'])) ?></td>
                        <td>
                            <div style="display: flex; gap: 0.35rem;">
                                <a href="/incidents/view?id=<?= (int)$inc['id'] ?>" class="btn btn-secondary btn-sm">View</a>
                                <?php if (Auth::can('edit_incident')): ?>
                                <a href="/incidents/edit?id=<?= (int)$inc['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
