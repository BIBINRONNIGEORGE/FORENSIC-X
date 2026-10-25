<?php
/**
 * FORENSIC-X Edit Incident View
 */
?>
<div style="max-width: 900px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                Edit Incident Record: <?= Security::e($incident['incident_code']) ?>
            </div>
            <a href="/incidents/view?id=<?= (int)$incident['id'] ?>" class="btn btn-secondary btn-sm">Cancel</a>
        </div>
        <div class="card-body">
            <form method="POST" action="/incidents/edit?id=<?= (int)$incident['id'] ?>">
                <?= CSRF::input() ?>

                <div class="form-group">
                    <label class="form-label" for="title">Incident Title *</label>
                    <input type="text" id="title" name="title" class="form-control" value="<?= Security::e($incident['title']) ?>" required>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="category">Category *</label>
                        <select id="category" name="category" class="form-control" required>
                            <?php foreach (['Malware Infection', 'Ransomware', 'Data Exfiltration', 'Unauthorized Access', 'Phishing', 'Denial of Service', 'Insider Threat', 'Credential Compromise'] as $cat): ?>
                            <option value="<?= $cat ?>" <?= $incident['category'] === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="severity">Severity Classification *</label>
                        <select id="severity" name="severity" class="form-control" required>
                            <option value="Critical" <?= $incident['severity'] === 'Critical' ? 'selected' : '' ?>>Critical</option>
                            <option value="High" <?= $incident['severity'] === 'High' ? 'selected' : '' ?>>High</option>
                            <option value="Medium" <?= $incident['severity'] === 'Medium' ? 'selected' : '' ?>>Medium</option>
                            <option value="Low" <?= $incident['severity'] === 'Low' ? 'selected' : '' ?>>Low</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="status">Investigation Status *</label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="Open" <?= $incident['status'] === 'Open' ? 'selected' : '' ?>>Open</option>
                            <option value="Investigating" <?= $incident['status'] === 'Investigating' ? 'selected' : '' ?>>Investigating</option>
                            <option value="Contained" <?= $incident['status'] === 'Contained' ? 'selected' : '' ?>>Contained</option>
                            <option value="Resolved" <?= $incident['status'] === 'Resolved' ? 'selected' : '' ?>>Resolved</option>
                            <option value="Closed" <?= $incident['status'] === 'Closed' ? 'selected' : '' ?>>Closed</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="assigned_to">Assigned Investigator</label>
                        <select id="assigned_to" name="assigned_to" class="form-control">
                            <option value="">Unassigned</option>
                            <?php foreach ($investigators as $inv): ?>
                            <option value="<?= (int)$inv['id'] ?>" <?= (int)$incident['assigned_to'] === (int)$inv['id'] ? 'selected' : '' ?>>
                                <?= Security::e($inv['full_name']) ?> (<?= Security::e($inv['role']) ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="affected_system">Affected Asset or System *</label>
                        <input type="text" id="affected_system" name="affected_system" class="form-control" value="<?= Security::e($incident['affected_system']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="source">Intake Source *</label>
                        <input type="text" id="source" name="source" class="form-control" value="<?= Security::e($incident['source']) ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="tags">Incident Tags (comma separated)</label>
                    <input type="text" id="tags" name="tags" class="form-control" value="<?= Security::e($incident['tags'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">Detailed Description *</label>
                    <textarea id="description" name="description" class="form-control" rows="4" required><?= Security::e($incident['description']) ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="findings_notes">Triage / Investigation Notes</label>
                    <textarea id="findings_notes" name="findings_notes" class="form-control" rows="3"><?= Security::e($incident['findings_notes'] ?? '') ?></textarea>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="/incidents/view?id=<?= (int)$incident['id'] ?>" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
