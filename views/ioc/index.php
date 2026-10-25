<?php
/**
 * FORENSIC-X IOC Management View
 */
?>
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
            Indicators of Compromise (IOC) Repository
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="/ioc/correlate" class="btn btn-secondary btn-sm">
                IOC Correlation Engine
            </a>
            <?php if (Auth::can('manage_iocs')): ?>
            <a href="/ioc/create" class="btn btn-primary btn-sm">
                + Add Indicator
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- FILTER BAR -->
    <form method="GET" action="/ioc" class="filter-bar" style="margin: 1rem; border-radius: var(--radius-md);">
        <div style="flex: 1; min-width: 200px;">
            <input type="text" name="search" class="form-control" placeholder="Search by indicator value, code, description..." value="<?= Security::e($filters['search'] ?? '') ?>">
        </div>

        <div>
            <select name="type" class="form-control">
                <option value="">All Indicator Types</option>
                <option value="IPv4" <?= ($filters['type'] ?? '') === 'IPv4' ? 'selected' : '' ?>>IPv4</option>
                <option value="IPv6" <?= ($filters['type'] ?? '') === 'IPv6' ? 'selected' : '' ?>>IPv6</option>
                <option value="Domain" <?= ($filters['type'] ?? '') === 'Domain' ? 'selected' : '' ?>>Domain</option>
                <option value="URL" <?= ($filters['type'] ?? '') === 'URL' ? 'selected' : '' ?>>URL</option>
                <option value="FileHash-SHA256" <?= ($filters['type'] ?? '') === 'FileHash-SHA256' ? 'selected' : '' ?>>SHA-256 Hash</option>
                <option value="FileHash-MD5" <?= ($filters['type'] ?? '') === 'FileHash-MD5' ? 'selected' : '' ?>>MD5 Hash</option>
            </select>
        </div>

        <div>
            <select name="confidence" class="form-control">
                <option value="">All Confidence Levels</option>
                <option value="High" <?= ($filters['confidence'] ?? '') === 'High' ? 'selected' : '' ?>>High</option>
                <option value="Medium" <?= ($filters['confidence'] ?? '') === 'Medium' ? 'selected' : '' ?>>Medium</option>
                <option value="Low" <?= ($filters['confidence'] ?? '') === 'Low' ? 'selected' : '' ?>>Low</option>
            </select>
        </div>

        <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
        <a href="/ioc" class="btn btn-secondary btn-sm">Reset</a>
    </form>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>IOC Code</th>
                    <th>Type</th>
                    <th>Indicator Value</th>
                    <th>Confidence</th>
                    <th>Threat Source</th>
                    <th>Associated Case</th>
                    <th>Matches</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($iocs)): ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                        No indicators recorded in the local repository.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($iocs as $ioc): ?>
                    <tr>
                        <td style="font-family: monospace; color: var(--cyan-primary); font-weight: 700;">
                            <?= Security::e($ioc['ioc_code']) ?>
                        </td>
                        <td><span class="badge badge-neutral"><?= Security::e($ioc['indicator_type']) ?></span></td>
                        <td>
                            <span class="code-block" style="color: #F8FAFC;"><?= Security::e($ioc['value']) ?></span>
                            <?php if (!empty($ioc['description'])): ?>
                            <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 0.2rem;"><?= Security::e($ioc['description']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td><?= View::severityBadge($ioc['confidence']) ?></td>
                        <td style="font-size: 0.75rem; color: var(--text-secondary);"><?= Security::e($ioc['threat_source'] ?: 'Manual Triage') ?></td>
                        <td>
                            <?php if ($ioc['incident_id']): ?>
                            <a href="/incidents/view?id=<?= (int)$ioc['incident_id'] ?>" style="color: var(--cyan-primary); text-decoration: none; font-size: 0.8rem;">
                                <?= Security::e($ioc['incident_code']) ?>
                            </a>
                            <?php else: ?>
                            <span style="color: var(--text-muted); font-size: 0.8rem;">Global</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge <?= (int)$ioc['match_count'] > 0 ? 'badge-critical' : 'badge-neutral' ?>">
                                <?= (int)$ioc['match_count'] ?> Matches
                            </span>
                        </td>
                        <td style="font-size: 0.75rem;"><?= date('Y-m-d H:i', strtotime($ioc['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
