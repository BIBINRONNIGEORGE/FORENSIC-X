<?php
/**
 * FORENSIC-X Generate Report View
 */
?>
<div style="max-width: 850px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                Compile Structured Forensic Investigation Report
            </div>
            <a href="/reports" class="btn btn-secondary btn-sm">Cancel</a>
        </div>
        <div class="card-body">
            <div style="padding: 0.85rem 1rem; background-color: rgba(6, 182, 212, 0.08); border: 1px solid rgba(6, 182, 212, 0.3); border-radius: var(--radius-md); font-size: 0.78rem; color: #38BDF8; margin-bottom: 1.5rem;">
                <strong>Automated Compilation Notice:</strong> Upon generation, FORENSIC-X will aggregate all verified digital evidence, cryptographic SHA-256 hashes, full chain-of-custody ledgers, correlated IOCs, and chronologically recorded timeline events associated with this incident into an authoritative, printable forensic report.
            </div>

            <form method="POST" action="/reports/generate">
                <?= CSRF::input() ?>

                <div class="form-group">
                    <label class="form-label" for="incident_id">Select Case Incident *</label>
                    <select id="incident_id" name="incident_id" class="form-control" required>
                        <option value="">-- Choose Incident Case --</option>
                        <?php foreach ($incidents as $inc): ?>
                        <option value="<?= (int)$inc['id'] ?>" <?= (isset($selectedIncidentId) && (int)$selectedIncidentId === (int)$inc['id']) ? 'selected' : '' ?>>
                            [<?= Security::e($inc['incident_code']) ?>] <?= Security::e($inc['title']) ?> (<?= Security::e($inc['severity']) ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="title">Report Title *</label>
                    <input type="text" id="title" name="title" class="form-control" placeholder="e.g. Comprehensive Forensic Assessment & Incident Triage Report: SEC-SRV-01" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="executive_summary">Executive Summary & Attack Narrative *</label>
                    <textarea id="executive_summary" name="executive_summary" class="form-control" rows="4" placeholder="High-level narrative outlining initial detection, affected enterprise scope, attacker intrusion vectors, and current containment status..." required></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="findings_summary">Key Findings & Threat Vectors *</label>
                    <textarea id="findings_summary" name="findings_summary" class="form-control" rows="3" placeholder="Summary of confirmed technical findings, compromised accounts, privilege escalation paths, or persistence mechanisms..." required></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="conclusions">Forensic Conclusions *</label>
                    <textarea id="conclusions" name="conclusions" class="form-control" rows="3" placeholder="Conclusions established by the forensic team regarding attacker dwell time, attribution hypothesis, and data impact..." required></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="recommendations">Remediation & Defensive Recommendations *</label>
                    <textarea id="recommendations" name="recommendations" class="form-control" rows="3" placeholder="Specific hardening measures, firewall blocks, credential rotations, policy changes, and monitoring improvements..." required></textarea>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                    <button type="submit" class="btn btn-primary">Generate Finalized Report</button>
                    <a href="/reports" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
