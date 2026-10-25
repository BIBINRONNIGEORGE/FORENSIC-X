/**
 * FORENSIC-X Interactive Application Client Logic
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Evidence Hash Verification AJAX
    document.querySelectorAll('.btn-verify-hash').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            const evidenceId = btn.getAttribute('data-id');
            const originalText = btn.innerHTML;
            btn.innerHTML = 'Verifying...';
            btn.disabled = true;

            try {
                const response = await fetch(`/evidence/verify?id=${evidenceId}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                });
                const result = await response.json();
                
                if (result.success) {
                    const badge = document.querySelector(`#verify-badge-${evidenceId}`);
                    if (badge) {
                        badge.className = `badge badge-${result.status.toLowerCase()}`;
                        badge.textContent = result.status;
                    }
                    alert(`Verification Complete: ${result.message}\nCurrent Hash: ${result.current_hash}`);
                    window.location.reload();
                } else {
                    alert(`Verification Error: ${result.message || 'Operation failed.'}`);
                }
            } catch (err) {
                console.error(err);
                alert('An error occurred during verification.');
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        });
    });

    // 2. Correlation Runner AJAX
    const runCorrBtn = document.querySelector('#btn-run-correlation');
    if (runCorrBtn) {
        runCorrBtn.addEventListener('click', async () => {
            runCorrBtn.disabled = true;
            runCorrBtn.innerHTML = 'Scanning Vault Evidence...';

            try {
                const res = await fetch('/ioc/correlate/run', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                });
                const data = await res.json();
                if (data.success) {
                    alert(`Correlation complete! Processed ${data.files_processed} files. Found ${data.new_matches} new match(es).`);
                    window.location.reload();
                } else {
                    alert('Correlation scan failed.');
                }
            } catch (err) {
                alert('Error running correlation engine.');
            } finally {
                runCorrBtn.disabled = false;
                runCorrBtn.innerHTML = 'Run IOC Correlation Engine';
            }
        });
    }
});
