<?php
/**
 * FORENSIC-X Provision User View (Admin Only)
 */
?>
<div style="max-width: 700px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                Provision New Operator Account
            </div>
            <a href="/users" class="btn btn-secondary btn-sm">Cancel</a>
        </div>
        <div class="card-body">
            <form method="POST" action="/users/create">
                <?= CSRF::input() ?>

                <div class="form-group">
                    <label class="form-label" for="full_name">Full Name & Title *</label>
                    <input type="text" id="full_name" name="full_name" class="form-control" placeholder="e.g. Dr. K. Radhakrishnan (Forensic Analyst)" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="username">Username *</label>
                        <input type="text" id="username" name="username" class="form-control" placeholder="e.g. radhakrishnan" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Official Email Address *</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="user@forensic-x.local" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="role">RBAC Security Role *</label>
                        <select id="role" name="role" class="form-control" required>
                            <option value="investigator">Investigator (Manage incidents, evidence, reports)</option>
                            <option value="analyst" selected>Analyst (Log analysis, IOC correlation, findings)</option>
                            <option value="reviewer">Reviewer (Read-only review of evidence & reports)</option>
                            <option value="admin">Admin (Full administrative & user control)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="department">Department / Unit</label>
                        <input type="text" id="department" name="department" class="form-control" value="Digital Forensics & Incident Response">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Initial Password (min 8 chars) *</label>
                    <input type="password" id="password" name="password" class="form-control" minlength="8" required>
                    <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 0.3rem;">
                        Password will be hashed using PHP <code>password_hash()</code> with <code>PASSWORD_DEFAULT</code>.
                    </div>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                    <button type="submit" class="btn btn-primary">Provision Account</button>
                    <a href="/users" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
