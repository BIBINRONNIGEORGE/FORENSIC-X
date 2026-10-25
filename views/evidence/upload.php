<?php
/**
 * FORENSIC-X Secure Evidence Upload View
 */
?>
<div style="max-width: 800px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                Secure Evidence Ingestion & Cryptographic Registration
            </div>
            <a href="/evidence" class="btn btn-secondary btn-sm">Cancel</a>
        </div>
        <div class="card-body">
            <div style="padding: 0.85rem 1rem; background-color: rgba(6, 182, 212, 0.08); border: 1px solid rgba(6, 182, 212, 0.3); border-radius: var(--radius-md); font-size: 0.78rem; color: #38BDF8; margin-bottom: 1.5rem;">
                <strong>Security Guard:</strong> Uploaded evidence is stored outside the public document root in an isolated storage repository. The server calculates an immutable SHA-256 cryptographic digest at intake, registers initial Chain of Custody, and strictly blocks executable script execution.
            </div>

            <form method="POST" action="/evidence/upload" enctype="multipart/form-data">
                <?= CSRF::input() ?>

                <div class="form-group">
                    <label class="form-label" for="incident_id">Associated Incident Case *</label>
                    <select id="incident_id" name="incident_id" class="form-control" required>
                        <option value="">Select Associated Incident</option>
                        <?php foreach ($incidents as $inc): ?>
                        <option value="<?= (int)$inc['id'] ?>" <?= (isset($selectedIncidentId) && (int)$selectedIncidentId === (int)$inc['id']) ? 'selected' : '' ?>>
                            [<?= Security::e($inc['incident_code']) ?>] <?= Security::e($inc['title']) ?> (<?= Security::e($inc['severity']) ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="name">Evidence Descriptive Name *</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="e.g. Memory Dump / Auth Log Extract / Staged Malware Script" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="evidence_file">Forensic Artifact File *</label>
                    <input type="file" id="evidence_file" name="evidence_file" class="form-control" required style="padding: 0.45rem;">
                    <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 0.3rem;">
                        Permitted formats: <code>.txt</code>, <code>.log</code>, <code>.csv</code>, <code>.json</code>, <code>.pcap</code>, <code>.pcapng</code>, <code>.pdf</code>. Maximum file size: 50 MB.
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="acquisition_notes">Acquisition & Source Notes *</label>
                    <textarea id="acquisition_notes" name="acquisition_notes" class="form-control" rows="3" placeholder="Document the origin host, acquisition tool/methodology, acquisition date/time, and physical or virtual location..." required></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">Detailed Description & Forensic Relevance</label>
                    <textarea id="description" name="description" class="form-control" rows="2" placeholder="Explain what artifacts or indicators are suspected within this file..."></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="tags">Evidence Tags (comma separated)</label>
                    <input type="text" id="tags" name="tags" class="form-control" placeholder="e.g. disk-image, network-capture, pcap, syslog">
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                    <button type="submit" class="btn btn-primary">Ingest & Calculate SHA-256</button>
                    <a href="/evidence" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
