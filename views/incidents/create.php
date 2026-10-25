<?php
/**
 * FORENSIC-X Create Incident View
 */
?>
<div style="max-width: 900px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Initiate New Forensic Incident Case
            </div>
            <a href="/incidents" class="btn btn-secondary btn-sm">Cancel</a>
        </div>
        <div class="card-body">
            <form method="POST" action="/incidents/create">
                <?= CSRF::input() ?>

                <div class="form-group">
                    <label class="form-label" for="title">Incident Title *</label>
                    <input type="text" id="title" name="title" class="form-control" placeholder="e.g. Ransomware Infiltration & Lateral Movement on DB Cluster" required>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="category">Incident Category *</label>
                        <select id="category" name="category" class="form-control" required>
                            <option value="Malware Infection">Malware Infection</option>
                            <option value="Ransomware">Ransomware</option>
                            <option value="Data Exfiltration">Data Exfiltration</option>
                            <option value="Unauthorized Access">Unauthorized Access</option>
                            <option value="Phishing">Phishing</option>
                            <option value="Denial of Service">Denial of Service</option>
                            <option value="Insider Threat">Insider Threat</option>
                            <option value="Credential Compromise">Credential Compromise</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="severity">Initial Severity *</label>
                        <select id="severity" name="severity" class="form-control" required>
                            <option value="Critical">Critical (Immediate containment required)</option>
                            <option value="High" selected>High (Significant threat or data exposure)</option>
                            <option value="Medium">Medium (Moderate operational risk)</option>
                            <option value="Low">Low (Informational / minor hygiene issue)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="status">Initial Status *</label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="Open" selected>Open (New case)</option>
                            <option value="Investigating">Investigating</option>
                            <option value="Contained">Contained</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="assigned_to">Assign Lead Investigator</label>
                        <select id="assigned_to" name="assigned_to" class="form-control">
                            <option value="">Unassigned</option>
                            <?php foreach ($investigators as $inv): ?>
                            <option value="<?= (int)$inv['id'] ?>"><?= Security::e($inv['full_name']) ?> (<?= Security::e($inv['role']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="affected_system">Affected Asset or System *</label>
                        <input type="text" id="affected_system" name="affected_system" class="form-control" placeholder="e.g. SEC-SRV-01, PORTAL-DB, 10.0.4.12" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="source">Detection / Intake Source *</label>
                        <input type="text" id="source" name="source" class="form-control" placeholder="e.g. SIEM Alert, EDR Telemetry, User Escalation" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="detected_at">Detection Timestamp *</label>
                        <input type="datetime-local" id="detected_at" name="detected_at" class="form-control" value="<?= date('Y-m-d\TH:i') ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="tags">Incident Tags (comma separated)</label>
                        <input type="text" id="tags" name="tags" class="form-control" placeholder="e.g. finance, lateral-movement, brute-force">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">Detailed Description & Attack Scope *</label>
                    <textarea id="description" name="description" class="form-control" rows="4" placeholder="Provide complete contextual details of how the security event was identified, initial observed symptoms, and potential perimeter exposure..." required></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="findings_notes">Preliminary Findings / Analyst Notes</label>
                    <textarea id="findings_notes" name="findings_notes" class="form-control" rows="3" placeholder="Initial hypotheses, triage notes, or immediate defensive actions taken..."></textarea>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                    <button type="submit" class="btn btn-primary">Create Incident Record</button>
                    <a href="/incidents" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
