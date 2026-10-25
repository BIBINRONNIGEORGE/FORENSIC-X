<?php
/**
 * FORENSIC-X Unified Investigation Timeline View
 */
?>
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            Chronological Investigation Timeline Feed
        </div>
        <div style="font-size: 0.72rem; color: var(--cyan-primary);">
            Multi-Source Correlated Event Sequence
        </div>
    </div>

    <!-- FILTER BAR -->
    <form method="GET" action="/timeline" class="filter-bar" style="margin: 1rem; border-radius: var(--radius-md);">
        <div>
            <select name="incident_id" class="form-control">
                <option value="">All Incident Cases</option>
                <?php foreach ($incidents as $inc): ?>
                <option value="<?= (int)$inc['id'] ?>" <?= ($filters['incident_id'] ?? '') == $inc['id'] ? 'selected' : '' ?>>
                    [<?= Security::e($inc['incident_code']) ?>] <?= Security::e($inc['title']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <select name="category" class="form-control">
                <option value="">All Event Categories</option>
                <option value="INCIDENT_LIFECYCLE" <?= ($filters['category'] ?? '') === 'INCIDENT_LIFECYCLE' ? 'selected' : '' ?>>Incident Lifecycle</option>
                <option value="EVIDENCE_INTAKE" <?= ($filters['category'] ?? '') === 'EVIDENCE_INTAKE' ? 'selected' : '' ?>>Evidence Intake</option>
                <option value="INTEGRITY_CHECK" <?= ($filters['category'] ?? '') === 'INTEGRITY_CHECK' ? 'selected' : '' ?>>Cryptographic Verification</option>
                <option value="IOC_CORRELATION" <?= ($filters['category'] ?? '') === 'IOC_CORRELATION' ? 'selected' : '' ?>>IOC Correlation</option>
                <option value="LOG_FINDING" <?= ($filters['category'] ?? '') === 'LOG_FINDING' ? 'selected' : '' ?>>Analysis Finding</option>
                <option value="REPORT_GENERATION" <?= ($filters['category'] ?? '') === 'REPORT_GENERATION' ? 'selected' : '' ?>>Report Generation</option>
            </select>
        </div>

        <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
        <a href="/timeline" class="btn btn-secondary btn-sm">Reset</a>
    </form>

    <div class="card-body">
        <?php if (empty($events)): ?>
        <div style="text-align: center; padding: 3rem; color: var(--text-muted);">
            No timeline events match the filter parameters.
        </div>
        <?php else: ?>
        <div class="timeline">
            <?php foreach ($events as $ev): ?>
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-time">
                    <?= date('M d, Y H:i:s', strtotime($ev['event_time'])) ?>
                    <a href="/incidents/view?id=<?= (int)$ev['incident_id'] ?>" style="color: var(--cyan-primary); text-decoration: none; margin-left: 0.5rem; font-family: monospace;">
                        [<?= Security::e($ev['incident_code']) ?>]
                    </a>
                    <span class="badge badge-neutral" style="font-size: 0.65rem; margin-left: 0.5rem;">
                        <?= Security::e($ev['event_category']) ?>
                    </span>
                    <?php if ($ev['is_source_log_timestamp']): ?>
                    <span class="badge badge-warning" style="font-size: 0.65rem;">Imported Log Timestamp</span>
                    <?php endif; ?>
                </div>
                <div class="timeline-title"><?= Security::e($ev['event_title']) ?></div>
                <div class="timeline-desc"><?= Security::e($ev['description']) ?></div>
                <?php if (!empty($ev['actor_name'])): ?>
                <div style="font-size: 0.7rem; color: var(--text-muted); margin-top: 0.25rem;">
                    Action by: <span style="color: var(--text-secondary);"><?= Security::e($ev['actor_name']) ?></span>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
