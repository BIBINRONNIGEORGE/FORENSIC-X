<?php
/**
 * FORENSIC-X Formal Forensic Investigation Report Document
 * Ready for Browser Print / Save as PDF
 */
?>
<div class="no-print" style="margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;">
    <a href="/reports" class="btn btn-secondary btn-sm">← Back to Reports</a>
    <button onclick="window.print()" class="btn btn-primary btn-sm">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
        Print / Save as PDF
    </button>
</div>

<div class="card" style="padding: 2.5rem; background-color: var(--bg-card); border: 1px solid var(--border-color); max-width: 950px; margin: 0 auto; box-shadow: 0 10px 40px rgba(0,0,0,0.5);">
    <!-- ACADEMIC & INSTITUTIONAL HEADER -->
    <div style="border-bottom: 2px solid var(--cyan-primary); padding-bottom: 1.5rem; margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
            <div>
                <div style="font-size: 0.8rem; font-weight: 700; color: var(--cyan-primary); text-transform: uppercase; letter-spacing: 0.1em;">
                    College of Engineering Kallooppara, Kerala
                </div>
                <div style="font-size: 0.72rem; color: var(--text-muted); margin-bottom: 0.5rem;">
                    Department of Computer Science & Engineering (Cyber Security)
                </div>
                <h1 style="font-size: 1.6rem; font-weight: 800; color: var(--text-primary); letter-spacing: -0.01em;">
                    <?= APP_NAME ?> — Forensic Investigation Dossier
                </h1>
                <div style="font-size: 0.85rem; color: var(--text-secondary);">
                    <?= Security::e($report['title']) ?>
                </div>
            </div>
            <div style="text-align: right; font-family: monospace; font-size: 0.85rem;">
                <div style="font-weight: 700; color: var(--cyan-primary); font-size: 1rem;">
                    <?= Security::e($report['report_code']) ?>
                </div>
                <div style="color: var(--text-muted);">Case: <?= Security::e($report['incident_code']) ?></div>
                <div style="color: var(--text-muted); font-size: 0.75rem;">Date: <?= date('Y-m-d H:i:s', strtotime($report['created_at'])) ?></div>
                <div><?= View::severityBadge($report['incident_severity']) ?></div>
            </div>
        </div>
    </div>

    <!-- METADATA GRID -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; padding: 1rem; background-color: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.82rem; margin-bottom: 2rem;">
        <div><strong>Incident Category:</strong><br><span style="color: var(--text-secondary);"><?= Security::e($report['incident_category']) ?></span></div>
        <div><strong>Status at Report:</strong><br><?= View::statusBadge($report['incident_status']) ?></div>
        <div><strong>Target Asset:</strong><br><span style="color: var(--cyan-primary); font-family: monospace;"><?= Security::e($report['affected_system']) ?></span></div>
        <div><strong>Lead Specialist:</strong><br><span style="color: var(--text-secondary);"><?= Security::e($report['assigned_investigator'] ?: 'Unassigned') ?></span></div>
        <div><strong>Report Preparer:</strong><br><span style="color: var(--text-secondary);"><?= Security::e($report['generator_name']) ?></span></div>
    </div>

    <!-- 1. EXECUTIVE SUMMARY -->
    <div style="margin-bottom: 2rem;">
        <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--cyan-primary); text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid var(--border-color); padding-bottom: 0.4rem; margin-bottom: 0.75rem;">
            1. Executive Summary & Attack Overview
        </h3>
        <p style="white-space: pre-line; color: var(--text-primary); line-height: 1.6; font-size: 0.9rem;">
            <?= Security::e($report['executive_summary']) ?>
        </p>
    </div>

    <!-- 2. TECHNICAL FINDINGS -->
    <div style="margin-bottom: 2rem;">
        <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--cyan-primary); text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid var(--border-color); padding-bottom: 0.4rem; margin-bottom: 0.75rem;">
            2. Detailed Forensic Findings & Vector Analysis
        </h3>
        <p style="white-space: pre-line; color: var(--text-primary); line-height: 1.6; font-size: 0.9rem; margin-bottom: 1rem;">
            <?= Security::e($report['findings_summary']) ?>
        </p>
        
        <?php if (!empty($compiledData['findings'])): ?>
        <table class="data-table" style="margin-top: 0.5rem;">
            <thead>
                <tr>
                    <th>Finding Code</th>
                    <th>Category</th>
                    <th>Severity</th>
                    <th>Finding Summary</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($compiledData['findings'] as $fnd): ?>
                <tr>
                    <td style="font-family: monospace; color: var(--cyan-primary); font-weight: 700;"><?= Security::e($fnd['finding_code']) ?></td>
                    <td><?= Security::e($fnd['category']) ?></td>
                    <td><?= View::severityBadge($fnd['severity']) ?></td>
                    <td>
                        <strong style="color: var(--text-primary);"><?= Security::e($fnd['title']) ?></strong>
                        <div style="font-size: 0.75rem; color: var(--text-secondary);"><?= Security::e($fnd['description']) ?></div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <!-- 3. DIGITAL EVIDENCE INVENTORY & SHA-256 HASH VERIFICATION -->
    <div style="margin-bottom: 2rem;">
        <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--cyan-primary); text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid var(--border-color); padding-bottom: 0.4rem; margin-bottom: 0.75rem;">
            3. Digital Evidence Inventory & Cryptographic Integrity (SHA-256)
        </h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Evidence ID</th>
                    <th>Original Filename</th>
                    <th>Size</th>
                    <th>Cryptographic SHA-256 Hash</th>
                    <th>Integrity Result</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($compiledData['evidence'])): ?>
                <tr><td colspan="5" style="text-align: center; color: var(--text-muted);">No evidence files recorded for this case.</td></tr>
                <?php else: ?>
                    <?php foreach ($compiledData['evidence'] as $ev): ?>
                    <tr>
                        <td style="font-family: monospace; color: var(--cyan-primary); font-weight: 700;"><?= Security::e($ev['evidence_code']) ?></td>
                        <td><?= Security::e($ev['original_filename']) ?></td>
                        <td><?= View::formatBytes((int)$ev['file_size']) ?></td>
                        <td><span class="code-block" style="font-size: 0.68rem;"><?= Security::e($ev['sha256_hash']) ?></span></td>
                        <td><?= View::statusBadge($ev['last_verified_status']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- 4. CORRELATED INDICATORS OF COMPROMISE -->
    <div style="margin-bottom: 2rem;">
        <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--cyan-primary); text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid var(--border-color); padding-bottom: 0.4rem; margin-bottom: 0.75rem;">
            4. Correlated Indicators of Compromise (IOCs)
        </h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Indicator Value</th>
                    <th>Confidence</th>
                    <th>Source Threat Telemetry</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($compiledData['iocs'])): ?>
                <tr><td colspan="4" style="text-align: center; color: var(--text-muted);">No indicators associated with this case.</td></tr>
                <?php else: ?>
                    <?php foreach ($compiledData['iocs'] as $ioc): ?>
                    <tr>
                        <td><span class="badge badge-neutral"><?= Security::e($ioc['indicator_type']) ?></span></td>
                        <td><span class="code-block"><?= Security::e($ioc['value']) ?></span></td>
                        <td><?= View::severityBadge($ioc['confidence']) ?></td>
                        <td style="font-size: 0.75rem;"><?= Security::e($ioc['threat_source'] ?: '-') ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- 5. CONCLUSIONS & RECOMMENDATIONS -->
    <div style="margin-bottom: 2.5rem;">
        <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--cyan-primary); text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid var(--border-color); padding-bottom: 0.4rem; margin-bottom: 0.75rem;">
            5. Forensic Conclusions & Remediation Roadmap
        </h3>
        
        <div style="margin-bottom: 1.25rem;">
            <strong style="color: var(--text-primary); font-size: 0.95rem;">Investigator Conclusions:</strong>
            <p style="white-space: pre-line; color: var(--text-secondary); line-height: 1.6; font-size: 0.88rem; margin-top: 0.3rem;">
                <?= Security::e($report['conclusions']) ?>
            </p>
        </div>

        <div>
            <strong style="color: var(--text-primary); font-size: 0.95rem;">Recommended Defensive Actions:</strong>
            <p style="white-space: pre-line; color: var(--text-secondary); line-height: 1.6; font-size: 0.88rem; margin-top: 0.3rem;">
                <?= Security::e($report['recommendations']) ?>
            </p>
        </div>
    </div>

    <!-- 6. OFFICIAL VERIFICATION & SIGN-OFF BLOCK -->
    <div style="border-top: 1px solid var(--border-color); padding-top: 2rem; margin-top: 3rem;">
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 2rem; text-align: center; font-size: 0.8rem;">
            <div>
                <div style="height: 45px; border-bottom: 1px dashed var(--border-highlight); margin-bottom: 0.5rem;"></div>
                <div style="font-weight: 700; color: var(--text-primary);"><?= Security::e($report['generator_name']) ?></div>
                <div style="color: var(--text-muted); font-size: 0.72rem;">Lead Forensic Specialist</div>
            </div>
            <div>
                <div style="height: 45px; border-bottom: 1px dashed var(--border-highlight); margin-bottom: 0.5rem;"></div>
                <div style="font-weight: 700; color: var(--text-primary);">SOC Operations Director</div>
                <div style="color: var(--text-muted); font-size: 0.72rem;">Security Operations Center</div>
            </div>
            <div>
                <div style="height: 45px; border-bottom: 1px dashed var(--border-highlight); margin-bottom: 0.5rem;"></div>
                <div style="font-weight: 700; color: var(--text-primary);">Faculty Evaluator</div>
                <div style="color: var(--text-muted); font-size: 0.72rem;">College of Engineering Kallooppara</div>
            </div>
        </div>
    </div>
</div>
