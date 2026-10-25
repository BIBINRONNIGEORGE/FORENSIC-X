<?php
/**
 * FORENSIC-X Comprehensive Incident Dossier View
 */
?>
<!-- HEADER DOSSIER BAR -->
<div class="card" style="margin-bottom: 1.5rem; border-left: 4px solid <?= $incident['severity'] === 'Critical' ? '#EF4444' : ($incident['severity'] === 'High' ? '#F97316' : '#06B6D4') ?>;">
    <div class="card-body">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
            <div>
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                    <span style="font-size: 1.25rem; font-weight: 800; font-family: monospace; color: var(--cyan-primary);">
                        <?= Security::e($incident['incident_code']) ?>
                    </span>
                    <?= View::severityBadge($incident['severity']) ?>
                    <?= View::statusBadge($incident['status']) ?>
                    <span class="badge badge-neutral"><?= Security::e($incident['category']) ?></span>
                </div>
                <h2 style="font-size: 1.4rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                    <?= Security::e($incident['title']) ?>
                </h2>
                <div style="display: flex; flex-wrap: wrap; gap: 1.5rem; font-size: 0.8rem; color: var(--text-muted);">
                    <div>Target Asset: <strong style="color: var(--text-secondary);"><?= Security::e($incident['affected_system']) ?></strong></div>
                    <div>Intake Source: <strong style="color: var(--text-secondary);"><?= Security::e($incident['source']) ?></strong></div>
                    <div>Detected: <strong style="color: var(--text-secondary);"><?= date('M d, Y H:i:s', strtotime($incident['detected_at'])) ?></strong></div>
                    <div>Investigator: <strong style="color: var(--cyan-primary);"><?= Security::e($incident['assigned_name'] ?: 'Unassigned') ?></strong></div>
                </div>
            </div>

            <!-- Quick Action Toolbar -->
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                <?php if (Auth::can('edit_incident')): ?>
                <a href="/incidents/edit?id=<?= (int)$incident['id'] ?>" class="btn btn-secondary btn-sm">
                    Edit Case
                </a>
                <?php endif; ?>

                <?php if (Auth::can('upload_evidence')): ?>
                <a href="/evidence/upload?incident_id=<?= (int)$incident['id'] ?>" class="btn btn-secondary btn-sm">
                    + Ingest Evidence
                </a>
                <?php endif; ?>

                <?php if (Auth::can('generate_report')): ?>
                <a href="/reports/generate?incident_id=<?= (int)$incident['id'] ?>" class="btn btn-primary btn-sm">
                    Generate Report
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- SECTION 1: CASE OVERVIEW & DESCRIPTION -->
<div class="card">
    <div class="card-header">
        <div class="card-title">Case Overview & Attack Scope</div>
        <?php if (!empty($incident['tags'])): ?>
        <div style="font-size: 0.75rem; color: var(--text-muted);">
            Tags: <span style="color: var(--cyan-primary);"><?= Security::e($incident['tags']) ?></span>
        </div>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <p style="white-space: pre-line; color: var(--text-primary); line-height: 1.6; margin-bottom: 1.25rem;">
            <?= Security::e($incident['description']) ?>
        </p>

        <?php if (!empty($incident['findings_notes'])): ?>
        <div style="padding: 1rem; background-color: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
            <div style="font-weight: 700; font-size: 0.78rem; text-transform: uppercase; color: var(--cyan-primary); margin-bottom: 0.4rem;">
                Triage Notes & Hypotheses:
            </div>
            <div style="color: var(--text-secondary); font-size: 0.85rem; white-space: pre-line;">
                <?= Security::e($incident['findings_notes']) ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- SECTION 2: DIGITAL EVIDENCE VAULT ARTIFACTS -->
<div class="card">
    <div class="card-header">
        <div class="card-title">
            Digital Evidence Artifacts (<?= count($evidence) ?>)
        </div>
        <?php if (Auth::can('upload_evidence')): ?>
        <a href="/evidence/upload?incident_id=<?= (int)$incident['id'] ?>" class="btn btn-secondary btn-sm">
            + Upload Evidence
        </a>
        <?php endif; ?>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Evidence Code</th>
                    <th>Name & Original File</th>
                    <th>Size</th>
                    <th>SHA-256 Ingestion Hash</th>
                    <th>Status</th>
                    <th>Verification</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($evidence)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                        No digital evidence ingested for this incident yet.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($evidence as $ev): ?>
                    <tr>
                        <td>
                            <a href="/evidence/view?id=<?= (int)$ev['id'] ?>" style="color: var(--cyan-primary); font-family: monospace; font-weight: 700; text-decoration: none;">
                                <?= Security::e($ev['evidence_code']) ?>
                            </a>
                        </td>
                        <td>
                            <div style="font-weight: 600; color: var(--text-primary);"><?= Security::e($ev['name']) ?></div>
                            <div style="font-size: 0.72rem; color: var(--text-muted); font-family: monospace;"><?= Security::e($ev['original_filename']) ?></div>
                        </td>
                        <td style="font-size: 0.75rem;"><?= View::formatBytes((int)$ev['file_size']) ?></td>
                        <td>
                            <span class="code-block" style="font-size: 0.7rem;">
                                <?= substr(Security::e($ev['sha256_hash']), 0, 16) ?>...<?= substr(Security::e($ev['sha256_hash']), -8) ?>
                            </span>
                        </td>
                        <td><?= View::statusBadge($ev['status']) ?></td>
                        <td>
                            <span id="verify-badge-<?= (int)$ev['id'] ?>">
                                <?= View::statusBadge($ev['last_verified_status']) ?>
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.4rem;">
                                <button type="button" class="btn btn-secondary btn-sm btn-verify-hash" data-id="<?= (int)$ev['id'] ?>" title="Recompute cryptographic hash and verify integrity">
                                    Verify
                                </button>
                                <a href="/evidence/download?id=<?= (int)$ev['id'] ?>" class="btn btn-secondary btn-sm" title="Download secured artifact">
                                    Download
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- SECTION 3: ASSOCIATED IOCS & CORRELATION -->
<div class="card">
    <div class="card-header">
        <div class="card-title">
            Associated Indicators of Compromise (<?= count($iocs) ?>)
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="/ioc/correlate" class="btn btn-secondary btn-sm">Scan Correlation</a>
            <?php if (Auth::can('manage_iocs')): ?>
            <a href="/ioc/create?incident_id=<?= (int)$incident['id'] ?>" class="btn btn-secondary btn-sm">+ Add IOC</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>IOC Code</th>
                    <th>Type</th>
                    <th>Indicator Value</th>
                    <th>Confidence</th>
                    <th>Source Notes</th>
                    <th>Discovered</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($iocs)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                        No indicators associated with this case yet.
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
                        </td>
                        <td><?= View::severityBadge($ioc['confidence']) ?></td>
                        <td style="font-size: 0.75rem; color: var(--text-secondary);"><?= Security::e($ioc['threat_source'] ?: '-') ?></td>
                        <td style="font-size: 0.75rem;"><?= date('Y-m-d H:i', strtotime($ioc['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- SECTION 4: ANALYSIS FINDINGS -->
<div class="card">
    <div class="card-header">
        <div class="card-title">
            Analysis Findings (<?= count($findings) ?>)
        </div>
        <?php if (Auth::can('create_finding')): ?>
        <a href="/logs" class="btn btn-secondary btn-sm">Triage In Log Analyzer</a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <?php if (empty($findings)): ?>
        <div style="text-align: center; padding: 2rem; color: var(--text-muted);">
            No formal findings logged yet. Use the Log Analysis module to identify and flag suspicious event patterns.
        </div>
        <?php else: ?>
            <?php foreach ($findings as $fnd): ?>
            <div style="padding: 1.25rem; background-color: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md); margin-bottom: 1rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="font-family: monospace; color: var(--cyan-primary); font-weight: 700;"><?= Security::e($fnd['finding_code']) ?></span>
                        <strong style="font-size: 1rem; color: var(--text-primary);"><?= Security::e($fnd['title']) ?></strong>
                    </div>
                    <?= View::severityBadge($fnd['severity']) ?>
                </div>
                <div style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.75rem;">
                    <?= Security::e($fnd['description']) ?>
                </div>
                <?php if (!empty($fnd['recommended_action'])): ?>
                <div style="font-size: 0.8rem; padding: 0.75rem; background: rgba(6, 182, 212, 0.08); border-left: 3px solid var(--cyan-primary); border-radius: var(--radius-sm);">
                    <strong>Recommended Defensive Action:</strong> <?= Security::e($fnd['recommended_action']) ?>
                </div>
                <?php endif; ?>
                <div style="margin-top: 0.5rem; font-size: 0.72rem; color: var(--text-muted);">
                    Logged by <?= Security::e($fnd['creator_name']) ?> on <?= date('M d, Y H:i', strtotime($fnd['created_at'])) ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- SECTION 5: UNIFIED INVESTIGATION TIMELINE -->
<div class="card">
    <div class="card-header">
        <div class="card-title">
            Case Chronological Timeline (<?= count($timeline) ?> Events)
        </div>
    </div>
    <div class="card-body">
        <?php if (empty($timeline)): ?>
        <div style="text-align: center; padding: 2rem; color: var(--text-muted);">No timeline records logged.</div>
        <?php else: ?>
        <div class="timeline">
            <?php foreach ($timeline as $item): ?>
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-time">
                    <?= date('M d, Y H:i:s', strtotime($item['event_time'])) ?>
                    <?php if ($item['is_source_log_timestamp']): ?>
                    <span class="badge badge-neutral" style="font-size: 0.65rem;">Imported Log Timestamp</span>
                    <?php else: ?>
                    <span class="badge badge-neutral" style="font-size: 0.65rem;">SOC System Event</span>
                    <?php endif; ?>
                </div>
                <div class="timeline-title"><?= Security::e($item['event_title']) ?></div>
                <div class="timeline-desc"><?= Security::e($item['description']) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
