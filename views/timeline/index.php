<?php
/**
 * FORENSIC-X Unified Investigation Timeline View
 * Implemented directly from Figma Source of Truth (ROORaWXDtD6bCWfH7NbtGO — Frame 10:123)
 */

$selectedEv = !empty($events) ? $events[0] : null;
?>

<!-- 1. PAGE HEADING -->
<div class="page-heading-row">
    <div class="page-heading-copy">
        <h1 class="page-title">Investigation Timeline</h1>
        <div class="page-subtitle">Reconstruct case activity from evidence intake through final reporting.</div>
    </div>
    <div class="page-actions">
        <a href="/reports" class="btn btn-secondary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Export timeline
        </a>
        <?php if ($selectedIncident): ?>
        <a href="/incidents/view?id=<?= (int)$selectedIncident['id'] ?>" class="btn btn-primary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#06111c" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            Case workspace
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- 2. TIMELINE FILTERS -->
<form method="GET" action="/timeline" class="filter-bar">
    <div style="flex: 1; min-width: 250px;">
        <select name="incident_id" class="form-control" onchange="this.form.submit()">
            <option value="">All Incident Cases</option>
            <?php foreach ($incidents as $inc): ?>
            <option value="<?= (int)$inc['id'] ?>" <?= ($filters['incident_id'] ?? ($selectedIncident['id'] ?? '')) == $inc['id'] ? 'selected' : '' ?>>
                <?= Security::e($inc['incident_code']) ?> &bull; <?= Security::e($inc['title']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div style="min-width: 200px;">
        <select name="category" class="form-control" onchange="this.form.submit()">
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

<!-- 3. SELECTED CASE BANNER (Figma Selected Case Banner) -->
<?php if ($selectedIncident): ?>
<div class="selected-case-banner">
    <div class="selected-case-info">
        <div class="case-icon-well">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        </div>
        <div>
            <div style="font-size: 1.05rem; font-weight: 700; color: var(--text-primary);">
                <?= Security::e($selectedIncident['title']) ?>
            </div>
            <div style="font-size: 0.72rem; color: var(--text-secondary); margin-top: 2px;">
                <span style="font-family: ui-monospace, monospace; color: var(--cyan-primary); font-weight: 600;"><?= Security::e($selectedIncident['incident_code']) ?></span> &bull; 
                Detected <?= date('M d, Y • H:i', strtotime($selectedIncident['detected_at'])) ?> &bull; 
                Asset: <span style="font-family: ui-monospace, monospace; color: var(--text-primary);"><?= Security::e($selectedIncident['affected_system']) ?></span>
            </div>
        </div>
    </div>
    <div style="display: flex; align-items: center; gap: 0.65rem;">
        <?= View::severityBadge($selectedIncident['severity']) ?>
        <?= View::statusBadge($selectedIncident['status']) ?>
    </div>
</div>
<?php endif; ?>

<!-- 4. TIMELINE MAIN ROW (Master-Detail 2 Columns) -->
<div class="fx-master-detail">
    <!-- Left: Chronological Event Timeline -->
    <div class="card fx-master-list">
        <div class="card-header">
            <div class="card-header-left">
                <div class="card-title">Chronological Event Timeline</div>
                <div class="card-subtitle"><?= count($events) ?> sequential investigation milestones</div>
            </div>
        </div>
        <div class="card-body" style="padding: 1.5rem 1.75rem;">
            <?php if (empty($events)): ?>
            <div style="text-align: center; padding: 3rem; color: var(--text-muted);">
                No timeline milestones logged for the selected filter parameters.
            </div>
            <?php else: ?>
            <div class="timeline-rail" id="timelineRail">
                <?php foreach ($events as $idx => $ev): 
                    $cat = $ev['category'] ?? 'GENERAL';
                    $dotClass = match($cat) {
                        'EVIDENCE_INTAKE' => 'dot-green',
                        'INTEGRITY_CHECK' => 'dot-green',
                        'IOC_CORRELATION' => 'dot-purple',
                        'LOG_FINDING'     => 'dot-amber',
                        'INCIDENT_LIFECYCLE' => 'dot-red',
                        default => ''
                    };
                ?>
                <div class="timeline-event-item <?= $idx === 0 ? 'selected' : '' ?>"
                    data-id="<?= (int)$ev['id'] ?>"
                    data-title="<?= Security::e($ev['event_title']) ?>"
                    data-desc="<?= Security::e($ev['description']) ?>"
                    data-category="<?= Security::e($ev['category']) ?>"
                    data-time="<?= date('M d, Y • H:i:s', strtotime($ev['event_time'])) ?>"
                    data-actor="<?= Security::e($ev['actor_name'] ?? 'Automated Engine') ?>"
                    data-case="<?= Security::e($ev['incident_code'] ?? 'Global') ?>">
                    <div class="timeline-marker-dot <?= $dotClass ?>"></div>
                    <div class="timeline-event-top">
                        <span class="timeline-event-time"><?= date('M d, Y • H:i:s', strtotime($ev['event_time'])) ?></span>
                        <span class="badge badge-medium" style="font-size: 0.62rem;"><?= Security::e($ev['category']) ?></span>
                    </div>
                    <div class="timeline-event-title"><?= Security::e($ev['event_title']) ?></div>
                    <div class="timeline-event-desc"><?= Security::e($ev['description']) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right: Selected Event Details (Figma Selected Event Details) -->
    <div class="fx-detail-panel" id="timelineDetailPanel">
        <?php if ($selectedEv): ?>
        <div class="detail-panel-header">
            <div>
                <div class="detail-meta-label">SELECTED EVENT</div>
                <div id="detailEvCategory" style="font-size: 0.95rem; font-weight: 700; color: var(--cyan-primary); margin-top: 2px;">
                    <?= Security::e($selectedEv['category']) ?>
                </div>
            </div>
            <span class="badge badge-open">VERIFIED LOG</span>
        </div>

        <div>
            <h2 id="detailEvTitle" class="detail-panel-title"><?= Security::e($selectedEv['event_title']) ?></h2>
            <div id="detailEvDesc" style="font-size: 0.78rem; color: var(--text-secondary); margin-top: 0.4rem; line-height: 1.45;">
                <?= Security::e($selectedEv['description']) ?>
            </div>
        </div>

        <!-- Parsed Fields -->
        <div>
            <div class="detail-meta-label" style="margin-bottom: 0.45rem;">PARSED FIELDS</div>
            <div class="detail-meta-list" style="background-color: var(--bg-surface-alt); padding: 0.85rem 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle);">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div class="detail-meta-item">
                        <span class="detail-meta-label">EVENT ID</span>
                        <span id="detailEvId" class="detail-meta-value" style="font-family: ui-monospace, monospace; color: var(--cyan-primary);">EVT-<?= sprintf('%04d', (int)$selectedEv['id']) ?></span>
                    </div>
                    <div class="detail-meta-item">
                        <span class="detail-meta-label">RECORDED AT</span>
                        <span id="detailEvTime" class="detail-meta-value" style="font-size: 0.72rem;"><?= date('H:i:s • M d, Y', strtotime($selectedEv['event_time'])) ?></span>
                    </div>
                    <div class="detail-meta-item">
                        <span class="detail-meta-label">OPERATOR / ACTOR</span>
                        <span id="detailEvActor" class="detail-meta-value"><?= Security::e($selectedEv['actor_name'] ?? 'System Core') ?></span>
                    </div>
                    <div class="detail-meta-item">
                        <span class="detail-meta-label">INCIDENT REF</span>
                        <span id="detailEvCase" class="detail-meta-value" style="font-family: ui-monospace, monospace; color: var(--cyan-primary);"><?= Security::e($selectedEv['incident_code'] ?? 'Global') ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analyst Action -->
        <?php if ($selectedIncident): ?>
        <div style="margin-top: 0.5rem;">
            <a href="/incidents/view?id=<?= (int)$selectedIncident['id'] ?>" class="btn btn-primary" style="width: 100%;">
                Inspect Case File Workspace &rarr;
            </a>
        </div>
        <?php endif; ?>

        <div style="font-size: 0.68rem; color: var(--text-dim); line-height: 1.4; padding-top: 0.5rem; border-top: 1px solid var(--border-dark);">
            Source log timestamps are kept distinct from application-generated event timestamps to maintain evidentiary chain of custody.
        </div>
        <?php else: ?>
        <div style="text-align: center; color: var(--text-muted); padding: 2rem;">No event selected.</div>
        <?php endif; ?>
    </div>
</div>

<!-- Interactive Selection Script -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const items = document.querySelectorAll('.timeline-event-item');
    items.forEach(item => {
        item.addEventListener('click', () => {
            items.forEach(i => i.classList.remove('selected'));
            item.classList.add('selected');

            const id = item.getAttribute('data-id');
            const title = item.getAttribute('data-title');
            const desc = item.getAttribute('data-desc');
            const category = item.getAttribute('data-category');
            const time = item.getAttribute('data-time');
            const actor = item.getAttribute('data-actor');
            const caseCode = item.getAttribute('data-case');

            document.getElementById('detailEvCategory').textContent = category;
            document.getElementById('detailEvTitle').textContent = title;
            document.getElementById('detailEvDesc').textContent = desc;
            document.getElementById('detailEvId').textContent = 'EVT-' + String(id).padStart(4, '0');
            document.getElementById('detailEvTime').textContent = time;
            document.getElementById('detailEvActor').textContent = actor;
            document.getElementById('detailEvCase').textContent = caseCode;
        });
    });
});
</script>
