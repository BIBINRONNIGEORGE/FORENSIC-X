<?php
/**
 * FORENSIC-X Change Password View
 */
?>
<div style="max-width: 500px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">Change Password</div>
        </div>
        <div class="card-body">
            <form method="POST" action="/change-password">
                <?= CSRF::input() ?>

                <div class="form-group">
                    <label class="form-label" for="old_password">Current Password</label>
                    <input type="password" id="old_password" name="old_password" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="new_password">New Password (min 8 chars)</label>
                    <input type="password" id="new_password" name="new_password" class="form-control" minlength="8" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" minlength="8" required>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                    <button type="submit" class="btn btn-primary">Update Password</button>
                    <a href="/profile" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
