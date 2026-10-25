<?php
/**
 * FORENSIC-X Interactive Log Analysis Engine View
 */
?>
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            Interactive Security Log Analysis & Threat Detection
        </div>
        <div style="font-size: 0.72rem; color: var(--cyan-primary);">
            Rule-Based Pattern Triage Engine
        </div>
    </div>
    <div class="card-body">
        <!-- LOG SELECTOR & FILTER FORM -->
        <form method="GET" action="/logs" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end; margin-bottom: 1.5rem;">
            <div style="flex: 1; min-width: 260px;">
                <label class="form-label" for="evidence_id">Select Forensic Log Artifact</label>
                <select id="evidence_id" name="evidence_id" class="form-control" onchange="this.form.submit()">
                    <option value="">-- Choose Log Artifact to Analyze --</option>
                    <optgroup label="Vault Digital Evidence">
                        <?php foreach ($evidenceList as $ev): ?>
                        <option value="<?= (int)$ev['id'] ?>" <?= (isset($selectedEvidenceId) && (int)$selectedEvidenceId === (int)$ev['id']) ? 'selected' : '' ?>>
                            [<?= Security::e($ev['evidence_code']) ?>] <?= Security::e($ev['name']) ?> (<?= Security::e($ev['original_filename']) ?>)
                        </option>
                        <?php endforeach; ?>
                    </optgroup>
                </select>
            </div>

            <div style="min-width: 200px;">
                <label class="form-label" for="rule_filter">Pattern Detection Filter</label>
                <select id="rule_filter" name="rule_filter" class="form-control" onchange="this.form.submit()">
                    <option value="ALL" <?= ($ruleFilter ?? 'ALL') === 'ALL' ? 'selected' : '' ?>>All Supported Lines</option>
                    <option value="FLAGGED" <?= ($ruleFilter ?? '') === 'FLAGGED' ? 'selected' : '' ?>>Only Flagged Anomalies</option>
                    <option value="BRUTE_FORCE" <?= ($ruleFilter ?? '') === 'BRUTE_FORCE' ? 'selected' : '' ?>>Brute Force Failures</option>
                    <option value="WEB_EXPLOIT" <?= ($ruleFilter ?? '') === 'WEB_EXPLOIT' ? 'selected' : '' ?>>Web Exploits (SQLi/Traversal)</option>
                    <option value="PRIVILEGE_ESC" <?= ($ruleFilter ?? '') === 'PRIVILEGE_ESC' ? 'selected' : '' ?>>Privilege Escalations</option>
                    <option value="SUSPICIOUS_CMD" <?= ($ruleFilter ?? '') === 'SUSPICIOUS_CMD' ? 'selected' : '' ?>>Suspicious Command Execution</option>
                    <option value="IOC_DETECTED" <?= ($ruleFilter ?? '') === 'IOC_DETECTED' ? 'selected' : '' ?>>Configured IOC Matches</option>
                </select>
            </div>

            <div>
                <button type="submit" class="btn btn-secondary">Analyze</button>
            </div>
        </form>

        <?php if ($analysisResults): ?>
            <!-- SUMMARY DETECTION METRICS -->
            <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); margin-bottom: 1.5rem;">
                <div class="stat-card" style="padding: 0.85rem;">
                    <div class="stat-header"><span>Total Lines</span></div>
                    <div class="stat-value" style="font-size: 1.4rem;"><?= (int)$analysisResults['total_lines'] ?></div>
                </div>
                <div class="stat-card" style="padding: 0.85rem;">
                    <div class="stat-header"><span>Privilege Esc</span></div>
                    <div class="stat-value" style="font-size: 1.4rem; color: #EF4444;"><?= (int)$analysisResults['counts']['PRIVILEGE_ESC'] ?></div>
                </div>
                <div class="stat-card" style="padding: 0.85rem;">
                    <div class="stat-header"><span>Web Exploits</span></div>
                    <div class="stat-value" style="font-size: 1.4rem; color: #F97316;"><?= (int)$analysisResults['counts']['WEB_EXPLOIT'] ?></div>
                </div>
                <div class="stat-card" style="padding: 0.85rem;">
                    <div class="stat-header"><span>Brute Force</span></div>
                    <div class="stat-value" style="font-size: 1.4rem; color: #F59E0B;"><?= (int)$analysisResults['counts']['BRUTE_FORCE'] ?></div>
                </div>
                <div class="stat-card" style="padding: 0.85rem;">
                    <div class="stat-header"><span>Suspicious Cmds</span></div>
                    <div class="stat-value" style="font-size: 1.4rem; color: var(--cyan-primary);"><?= (int)$analysisResults['counts']['SUSPICIOUS_CMD'] ?></div>
                </div>
                <div class="stat-card" style="padding: 0.85rem;">
                    <div class="stat-header"><span>IOC Hits</span></div>
                    <div class="stat-value" style="font-size: 1.4rem; color: #38BDF8;"><?= (int)$analysisResults['counts']['IOC_DETECTED'] ?></div>
                </div>
            </div>

            <!-- LOG ENTRIES TABLE -->
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">Line #</th>
                            <th style="width: 140px;">Detection Rule</th>
                            <th style="width: 90px;">Severity</th>
                            <th>Raw Log Payload</th>
                            <th>Detection Reason</th>
                            <th style="width: 130px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($analysisResults['events'])): ?>
                        <tr><td colspan="6" style="text-align: center; color: var(--text-muted); padding: 2rem;">No log lines matched the selected filter rule.</td></tr>
                        <?php else: ?>
                            <?php foreach ($analysisResults['events'] as $event): ?>
                            <tr style="<?= $event['flagged'] ? 'background-color: rgba(239, 68, 68, 0.04);' : '' ?>">
                                <td style="font-family: monospace; font-size: 0.75rem; color: var(--text-muted);">
                                    L#<?= (int)$event['line_number'] ?>
                                </td>
                                <td>
                                    <?php if ($event['flagged']): ?>
                                    <span class="badge <?= $event['rule'] === 'PRIVILEGE_ESC' ? 'badge-critical' : ($event['rule'] === 'WEB_EXPLOIT' ? 'badge-high' : 'badge-medium') ?>">
                                        <?= Security::e($event['rule']) ?>
                                    </span>
                                    <?php else: ?>
                                    <span class="badge badge-neutral">NORMAL</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($event['flagged']): ?>
                                    <?= View::severityBadge($event['severity']) ?>
                                    <?php else: ?>
                                    <span style="color: var(--text-muted); font-size: 0.72rem;">Info</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="code-block" style="font-size: 0.75rem; max-width: 500px; white-space: pre-wrap; word-break: break-all;">
                                        <?= Security::e($event['raw_text']) ?>
                                    </div>
                                </td>
                                <td style="font-size: 0.78rem; color: var(--text-secondary); max-width: 250px;">
                                    <?= Security::e($event['reason'] ?: 'Routine system telemetry.') ?>
                                </td>
                                <td>
                                    <?php if ($event['flagged'] && Auth::can('create_finding')): ?>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="openFindingModal(<?= (int)$selectedEvidence['incident_id'] ?>, <?= (int)$selectedEvidence['id'] ?>, '<?= Security::e(addslashes($event['rule'])) ?>', '<?= Security::e(addslashes($event['severity'])) ?>', '<?= Security::e(addslashes($event['reason'])) ?>', '<?= Security::e(addslashes($event['raw_text'])) ?>')">
                                        Flag as Finding
                                    </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        <?php else: ?>
            <div style="text-align: center; padding: 3rem 1rem; color: var(--text-muted);">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom: 0.75rem;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                <div style="font-size: 1rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.3rem;">No Log File Selected</div>
                <div>Select an evidence log from the dropdown above to parse security events and run automated rule-based detection.</div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- MODAL: FLAG AS CASE FINDING -->
<div id="findingModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 1000; align-items: center; justify-content: center; padding: 1.5rem;">
    <div class="card" style="width: 100%; max-width: 600px; margin: 0; box-shadow: 0 10px 40px rgba(0,0,0,0.8);">
        <div class="card-header">
            <div class="card-title">Promote Log Anomaly to Case Finding</div>
            <button type="button" onclick="closeFindingModal()" class="btn btn-secondary btn-sm">✕</button>
        </div>
        <div class="card-body">
            <form method="POST" action="/findings/create">
                <?= CSRF::input() ?>
                <input type="hidden" id="modal_incident_id" name="incident_id" value="">
                <input type="hidden" id="modal_evidence_id" name="evidence_id" value="">

                <div class="form-group">
                    <label class="form-label" for="modal_title">Finding Title *</label>
                    <input type="text" id="modal_title" name="title" class="form-control" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="modal_severity">Severity *</label>
                        <select id="modal_severity" name="severity" class="form-control" required>
                            <option value="Critical">Critical</option>
                            <option value="High">High</option>
                            <option value="Medium">Medium</option>
                            <option value="Low">Low</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="modal_category">Attack Category *</label>
                        <input type="text" id="modal_category" name="category" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="modal_description">Analysis & Assessment Notes *</label>
                    <textarea id="modal_description" name="description" class="form-control" rows="3" required></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="modal_supporting_evidence">Supporting Log Snippet</label>
                    <textarea id="modal_supporting_evidence" name="supporting_evidence" class="form-control" rows="2" readonly style="font-family: monospace; font-size: 0.75rem; color: #38BDF8;"></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="modal_recommended_action">Recommended Defensive Action</label>
                    <input type="text" id="modal_recommended_action" name="recommended_action" class="form-control" placeholder="e.g. Block source IP at border firewall, reset service credentials">
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                    <button type="submit" class="btn btn-primary">Save Finding to Incident Case</button>
                    <button type="button" onclick="closeFindingModal()" class="btn btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openFindingModal(incId, evdId, rule, sev, reason, snippet) {
    document.getElementById('modal_incident_id').value = incId;
    document.getElementById('modal_evidence_id').value = evdId;
    document.getElementById('modal_title').value = `${rule}: Detected Security Anomaly`;
    document.getElementById('modal_severity').value = sev || 'High';
    document.getElementById('modal_category').value = rule;
    document.getElementById('modal_description').value = reason;
    document.getElementById('modal_supporting_evidence').value = snippet;
    document.getElementById('findingModal').style.display = 'flex';
}

function closeFindingModal() {
    document.getElementById('findingModal').style.display = 'none';
}
</script>
