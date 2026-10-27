<?php
/**
 * FORENSIC-X IOC & Log Analysis View
 * Implemented directly from Figma Source of Truth (ROORaWXDtD6bCWfH7NbtGO — Frame 5:594)
 */

$recentMatch = !empty($matches) ? $matches[0] : null;
$totalIocs = (int)($stats['total'] ?? count($iocs));
$totalMatches = (int)($stats['matches'] ?? count($matches));
?>

<!-- 1. PAGE HEADING -->
<div class="page-heading-row">
    <div class="page-heading-copy">
        <h1 class="page-title">IOC & Log Analysis</h1>
        <div class="page-subtitle">Correlate local threat indicators with evidence and inspect suspicious log activity.</div>
    </div>
    <div class="page-actions">
        <a href="/reports" class="btn btn-secondary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Export IOCs
        </a>
        <?php if (Auth::can('manage_iocs')): ?>
        <a href="/ioc/create" class="btn btn-primary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#06111c" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add indicator
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- 2. ANALYSIS METRICS (4 Cards) -->
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-header">
            <div class="kpi-icon-well">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
            </div>
            <div class="kpi-label">Local IOCs</div>
        </div>
        <div class="kpi-value"><?= sprintf('%02d', $totalIocs) ?></div>
        <div class="kpi-footnote">Stored indicators</div>
    </div>

    <div class="kpi-card">
        <div class="kpi-header">
            <div class="kpi-icon-well" style="background-color: #392657;">
                <svg viewBox="0 0 24 24" fill="none" stroke="var(--cyan-primary)" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
            </div>
            <div class="kpi-label">Correlation matches</div>
        </div>
        <div class="kpi-value" style="color: var(--cyan-primary);"><?= sprintf('%02d', $totalMatches) ?></div>
        <div class="kpi-footnote">Across active cases</div>
    </div>

    <div class="kpi-card">
        <div class="kpi-header">
            <div class="kpi-icon-well" style="background-color: #0e3553;">
                <svg viewBox="0 0 24 24" fill="none" stroke="var(--cyan-primary)" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/></svg>
            </div>
            <div class="kpi-label">Logs analyzed</div>
        </div>
        <div class="kpi-value"><?= sprintf('%02d', $logsCount) ?></div>
        <div class="kpi-footnote">Supported files only</div>
    </div>

    <div class="kpi-card">
        <div class="kpi-header">
            <div class="kpi-icon-well" style="background-color: #473517;">
                <svg viewBox="0 0 24 24" fill="none" stroke="var(--amber-accent)" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/></svg>
            </div>
            <div class="kpi-label">Flagged findings</div>
        </div>
        <div class="kpi-value" style="color: var(--amber-accent);"><?= sprintf('%02d', count($findings)) ?></div>
        <div class="kpi-footnote warning">Review recommended</div>
    </div>
</div>

<!-- 3. IOC FILTER BAR -->
<form method="GET" action="/ioc" class="filter-bar">
    <div class="filter-search-box">
        <svg class="filter-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" name="search" class="form-control" placeholder="Search IP, domain, URL, or hash..." value="<?= Security::e($filters['search'] ?? '') ?>">
    </div>

    <div style="min-width: 160px;">
        <select name="type" class="form-control" onchange="this.form.submit()">
            <option value="">Type: All</option>
            <option value="IPv4" <?= ($filters['type'] ?? '') === 'IPv4' ? 'selected' : '' ?>>IPv4 Address</option>
            <option value="IPv6" <?= ($filters['type'] ?? '') === 'IPv6' ? 'selected' : '' ?>>IPv6 Address</option>
            <option value="Domain" <?= ($filters['type'] ?? '') === 'Domain' ? 'selected' : '' ?>>Domain</option>
            <option value="URL" <?= ($filters['type'] ?? '') === 'URL' ? 'selected' : '' ?>>URL</option>
            <option value="FileHash-SHA256" <?= ($filters['type'] ?? '') === 'FileHash-SHA256' ? 'selected' : '' ?>>SHA-256 Hash</option>
            <option value="FileHash-MD5" <?= ($filters['type'] ?? '') === 'FileHash-MD5' ? 'selected' : '' ?>>MD5 Hash</option>
        </select>
    </div>

    <div style="min-width: 160px;">
        <select name="confidence" class="form-control" onchange="this.form.submit()">
            <option value="">Confidence: Any</option>
            <option value="High" <?= ($filters['confidence'] ?? '') === 'High' ? 'selected' : '' ?>>High Confidence</option>
            <option value="Medium" <?= ($filters['confidence'] ?? '') === 'Medium' ? 'selected' : '' ?>>Medium Confidence</option>
            <option value="Low" <?= ($filters['confidence'] ?? '') === 'Low' ? 'selected' : '' ?>>Low Confidence</option>
        </select>
    </div>

    <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
    <a href="/ioc" class="btn btn-secondary btn-sm">Reset</a>
</form>

<!-- 4. IOC ANALYSIS MAIN ROW (Master-Detail 2 Columns) -->
<div class="fx-master-detail">
    <!-- Left: IOC Inventory Card -->
    <div class="card fx-master-list">
        <div class="card-header">
            <div class="card-header-left">
                <div class="card-title">Indicator Repository</div>
                <div class="card-subtitle"><?= count($iocs) ?> active threat signatures</div>
            </div>
            <a href="/ioc/correlate" class="btn btn-secondary btn-sm">Full Correlation View</a>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>INDICATOR VALUE</th>
                        <th>TYPE</th>
                        <th>CONFIDENCE</th>
                        <th>SOURCE / CONTEXT</th>
                        <th>MATCHES</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($iocs)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                            No indicators match the search criteria.
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($iocs as $ioc): ?>
                        <tr>
                            <td>
                                <div style="display: flex; flex-direction: column;">
                                    <span style="font-family: ui-monospace, SFMono-Regular, monospace; font-weight: 700; color: var(--text-primary); font-size: 0.82rem; word-break: break-all;">
                                        <?= Security::e($ioc['value']) ?>
                                    </span>
                                    <span style="font-size: 0.68rem; color: var(--text-secondary);"><?= Security::e($ioc['ioc_code']) ?></span>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-medium">
                                    <?= Security::e($ioc['indicator_type']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge <?= $ioc['confidence'] === 'High' ? 'badge-critical' : ($ioc['confidence'] === 'Medium' ? 'badge-high' : 'badge-low') ?>">
                                    <?= Security::e($ioc['confidence']) ?>
                                </span>
                            </td>
                            <td style="font-size: 0.75rem; color: var(--text-secondary);">
                                <?= Security::e($ioc['threat_source'] ?: 'Manual Triage') ?>
                            </td>
                            <td>
                                <span style="font-weight: 700; color: <?= (int)$ioc['match_count'] > 0 ? 'var(--cyan-primary)' : 'var(--text-muted)' ?>; font-size: 0.85rem;">
                                    <?= (int)$ioc['match_count'] ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Right: IOC Correlation Panel (Figma IOC Correlation Panel) -->
    <div class="fx-detail-panel">
        <div class="detail-panel-header">
            <div>
                <div class="detail-meta-label">CORRELATION ENGINE</div>
                <div style="font-size: 1.15rem; font-weight: 700; color: var(--cyan-primary); margin-top: 2px;">
                    Vault Scanner
                </div>
            </div>
            <span class="badge badge-open">READY</span>
        </div>

        <p style="font-size: 0.78rem; color: var(--text-secondary); line-height: 1.45;">
            Cross-reference all stored indicators (IPs, domains, hashes) against ingested digital evidence and forensic text dumps.
        </p>

        <!-- Correlation Runner Trigger -->
        <button type="button" id="btn-run-correlation" class="btn btn-primary" style="width: 100%;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#06111c" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            Run IOC Correlation Engine
        </button>

        <!-- Recent Match Card (Figma Recent Match) -->
        <div>
            <div class="detail-meta-label" style="margin-bottom: 0.45rem;">RECENT CORRELATION MATCH</div>
            <?php if ($recentMatch): ?>
            <div style="background-color: var(--bg-surface-alt); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 0.85rem 1rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.35rem;">
                    <span style="font-size: 0.68rem; color: var(--cyan-primary); font-family: ui-monospace, monospace; font-weight: 700;"><?= Security::e($recentMatch['ioc_code']) ?></span>
                    <span class="badge badge-critical" style="font-size: 0.62rem;">CONFIRMED</span>
                </div>
                <div style="font-family: ui-monospace, monospace; font-size: 0.8rem; font-weight: 600; color: var(--text-primary); word-break: break-all;">
                    <?= Security::e($recentMatch['ioc_value']) ?>
                </div>
                <div style="font-size: 0.72rem; color: var(--text-secondary); margin-top: 0.35rem;">
                    File: <span style="color: var(--text-primary); font-family: ui-monospace, monospace;"><?= Security::e($recentMatch['original_filename']) ?></span>
                </div>
                <?php if (!empty($recentMatch['matched_context'])): ?>
                <div style="background-color: var(--bg-canvas); border: 1px solid var(--border-dark); border-radius: var(--radius-sm); padding: 0.45rem 0.65rem; font-size: 0.68rem; font-family: ui-monospace, monospace; color: var(--text-secondary); margin-top: 0.5rem; word-break: break-all;">
                    <?= Security::e(substr($recentMatch['matched_context'], 0, 110)) ?>...
                </div>
                <?php endif; ?>
            </div>
            <?php else: ?>
            <div style="background-color: var(--bg-surface-alt); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 1rem; text-align: center; font-size: 0.75rem; color: var(--text-muted);">
                Click "Run IOC Correlation Engine" to initiate an automated vault scan.
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- 5. LOG ANALYSIS ROW (Figma Log Analysis Row - Results + Detection Rules) -->
<div style="display: grid; grid-template-columns: minmax(0, 1.6fr) minmax(340px, 1fr); gap: 1.25rem;">
    <!-- Log Analysis Results Card -->
    <div class="card">
        <div class="card-header">
            <div class="card-header-left">
                <div class="card-title">Rule-Based Log Analysis Findings</div>
                <div class="card-subtitle">Automated anomaly detection across ingested logs</div>
            </div>
            <a href="/logs" class="btn btn-secondary btn-sm">Full Log Inspector</a>
        </div>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>FINDING TITLE</th>
                        <th>SEVERITY</th>
                        <th>CASE</th>
                        <th>DETECTED</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($findings)): ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                            No automated findings flagged yet. Upload forensic logs in Evidence Vault to trigger rules.
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($findings as $f): ?>
                        <tr>
                            <td>
                                <div style="display: flex; flex-direction: column;">
                                    <span style="font-weight: 600; color: var(--text-primary); font-size: 0.81rem;"><?= Security::e($f['title']) ?></span>
                                    <span style="font-size: 0.68rem; color: var(--text-secondary); font-family: ui-monospace, monospace;"><?= Security::e($f['finding_code']) ?> &bull; <?= Security::e($f['category']) ?></span>
                                </div>
                            </td>
                            <td><?= View::severityBadge($f['severity']) ?></td>
                            <td>
                                <span style="font-family: ui-monospace, monospace; color: var(--cyan-primary); font-size: 0.75rem;">
                                    <?= Security::e($f['incident_code'] ?: 'N/A') ?>
                                </span>
                            </td>
                            <td style="font-size: 0.72rem; color: var(--text-secondary);">
                                <?= date('M d, H:i', strtotime($f['created_at'])) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div style="padding: 0.65rem 1.25rem; font-size: 0.68rem; color: var(--text-dim); border-top: 1px solid var(--border-dark);">
            Rule-based results are indicators for analyst review, not confirmed attacks.
        </div>
    </div>

    <!-- Detection Rules Card (Figma Detection Rules Card) -->
    <div class="card">
        <div class="card-header">
            <div class="card-header-left">
                <div class="card-title">Configured detection rules</div>
                <div class="card-subtitle">Transparent patterns evaluated on log ingestion</div>
            </div>
        </div>
        <div class="card-body" style="display: flex; flex-direction: column; gap: 0.75rem;">
            <!-- Rule 1 -->
            <div style="background-color: var(--bg-surface-alt); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 0.75rem 0.85rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2px;">
                    <span style="font-family: ui-monospace, monospace; font-weight: 700; color: var(--cyan-primary); font-size: 0.72rem;">AUTH-003</span>
                    <span class="badge badge-critical" style="font-size: 0.62rem;">HIGH</span>
                </div>
                <div style="font-size: 0.78rem; font-weight: 600; color: var(--text-primary);">Brute Force Authentication Pattern</div>
                <div style="font-size: 0.68rem; color: var(--text-secondary); margin-top: 2px;">Triggers on &gt;5 failed authentications within 60s from single IP.</div>
            </div>

            <!-- Rule 2 -->
            <div style="background-color: var(--bg-surface-alt); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 0.75rem 0.85rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2px;">
                    <span style="font-family: ui-monospace, monospace; font-weight: 700; color: var(--cyan-primary); font-size: 0.72rem;">IOC-001</span>
                    <span class="badge badge-medium" style="font-size: 0.62rem;">MEDIUM</span>
                </div>
                <div style="font-size: 0.78rem; font-weight: 600; color: var(--text-primary);">Exact String &amp; Hash Matching</div>
                <div style="font-size: 0.68rem; color: var(--text-secondary); margin-top: 2px;">Scans raw artifacts against all registered IP and hash indicators.</div>
            </div>

            <!-- Rule 3 -->
            <div style="background-color: var(--bg-surface-alt); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 0.75rem 0.85rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2px;">
                    <span style="font-family: ui-monospace, monospace; font-weight: 700; color: var(--cyan-primary); font-size: 0.72rem;">TIME-002</span>
                    <span class="badge badge-high" style="font-size: 0.62rem;">MEDIUM</span>
                </div>
                <div style="font-size: 0.78rem; font-weight: 600; color: var(--text-primary);">Off-Hours Privileged Access</div>
                <div style="font-size: 0.68rem; color: var(--text-secondary); margin-top: 2px;">Flags administrative logins outside operational SOC window (00:00 - 05:00).</div>
            </div>
        </div>
    </div>
</div>
