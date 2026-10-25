<?php
/**
 * FORENSIC-X Create IOC View
 */
?>
<div style="max-width: 750px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Register Indicator of Compromise (IOC)
            </div>
            <a href="/ioc" class="btn btn-secondary btn-sm">Cancel</a>
        </div>
        <div class="card-body">
            <form method="POST" action="/ioc/create">
                <?= CSRF::input() ?>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="indicator_type">Indicator Type *</label>
                        <select id="indicator_type" name="indicator_type" class="form-control" required>
                            <option value="IPv4">IPv4 Address (e.g. 198.51.100.45)</option>
                            <option value="Domain">Domain Name (e.g. evil-c2.net)</option>
                            <option value="URL">Full URL (e.g. https://evil.org/payload)</option>
                            <option value="FileHash-SHA256">SHA-256 Hash (64 hex characters)</option>
                            <option value="FileHash-MD5">MD5 Hash (32 hex characters)</option>
                            <option value="IPv6">IPv6 Address</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="confidence">Assessment Confidence *</label>
                        <select id="confidence" name="confidence" class="form-control" required>
                            <option value="High" selected>High (Confirmed Malicious)</option>
                            <option value="Medium">Medium (Suspicious / Correlated)</option>
                            <option value="Low">Low (Unverified / Informational)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="value">Indicator Value *</label>
                    <input type="text" id="value" name="value" class="form-control" placeholder="Enter IP, domain, URL, or hash..." required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="incident_id">Associated Incident (Optional)</label>
                        <select id="incident_id" name="incident_id" class="form-control">
                            <option value="">Global / Unattached</option>
                            <?php foreach ($incidents as $inc): ?>
                            <option value="<?= (int)$inc['id'] ?>" <?= (isset($selectedIncidentId) && (int)$selectedIncidentId === (int)$inc['id']) ? 'selected' : '' ?>>
                                [<?= Security::e($inc['incident_code']) ?>] <?= Security::e($inc['title']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="threat_source">Threat Intelligence / Evidence Source</label>
                        <input type="text" id="threat_source" name="threat_source" class="form-control" placeholder="e.g. Auth Log Extract, Reverse Engineering Triage">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">Indicator Description & Behavioral Context</label>
                    <textarea id="description" name="description" class="form-control" rows="3" placeholder="Contextual details regarding why this indicator was flagged and how it interacts with the compromised host..."></textarea>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                    <button type="submit" class="btn btn-primary">Save to IOC Repository</button>
                    <a href="/ioc" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
