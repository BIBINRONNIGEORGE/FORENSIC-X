<?php
/**
 * FORENSIC-X System Settings & Diagnostic Telemetry View
 */
?>
<div style="max-width: 900px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                Platform Settings & Environment Diagnostics
            </div>
            <span class="badge badge-verified">Operational</span>
        </div>
        <div class="card-body">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                <div style="padding: 1rem; background-color: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                    <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); margin-bottom: 0.3rem;">Platform Core</div>
                    <div style="font-weight: 700; color: var(--text-primary); font-size: 1.1rem;"><?= APP_NAME ?> v<?= APP_VERSION ?></div>
                    <div style="font-size: 0.75rem; color: var(--cyan-primary);"><?= APP_SUBTITLE ?></div>
                </div>

                <div style="padding: 1rem; background-color: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                    <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); margin-bottom: 0.3rem;">Academic Institution</div>
                    <div style="font-weight: 700; color: var(--text-primary); font-size: 0.95rem;"><?= APP_INSTITUTION ?></div>
                    <div style="font-size: 0.75rem; color: var(--text-secondary);">B.Tech CSE (Cyber Security) Minor Project</div>
                </div>

                <div style="padding: 1rem; background-color: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                    <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); margin-bottom: 0.3rem;">Workflow Architecture</div>
                    <div style="font-weight: 700; color: var(--cyan-primary); font-size: 0.85rem; font-family: monospace;">
                        DETECT → INVESTIGATE → ANALYZE → CORRELATE → REPORT
                    </div>
                </div>
            </div>

            <h3 style="font-size: 0.95rem; font-weight: 700; color: var(--cyan-primary); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem;">
                Server & Environment Health
            </h3>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Subsystem Component</th>
                        <th>Environment Parameter</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>PHP Runtime</strong></td>
                        <td>PHP <?= phpversion() ?></td>
                        <td><span class="badge badge-verified">Active</span></td>
                    </tr>
                    <tr>
                        <td><strong>Database Engine</strong></td>
                        <td>MariaDB / MySQL PDO (utf8mb4)</td>
                        <td><span class="badge badge-verified">Connected</span></td>
                    </tr>
                    <tr>
                        <td><strong>Web Server</strong></td>
                        <td><?= Security::e($_SERVER['SERVER_SOFTWARE'] ?? 'Apache') ?></td>
                        <td><span class="badge badge-verified">Serving Port 8080</span></td>
                    </tr>
                    <tr>
                        <td><strong>Cryptographic Hash Engine</strong></td>
                        <td>SHA-256 (PHP <code>hash_file</code>)</td>
                        <td><span class="badge badge-verified">Available</span></td>
                    </tr>
                    <tr>
                        <td><strong>Evidence Storage Vault</strong></td>
                        <td><code><?= EVIDENCE_STORAGE_PATH ?></code></td>
                        <td>
                            <?php if (is_writable(EVIDENCE_STORAGE_PATH)): ?>
                            <span class="badge badge-verified">Secure & Writable</span>
                            <?php else: ?>
                            <span class="badge badge-critical">Permission Error</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Session Security</strong></td>
                        <td>HttpOnly, SameSite=Lax, Auto Regeneration</td>
                        <td><span class="badge badge-verified">Hardened</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
