/* =============================================
   SaveThem — app.js
   ============================================= */

// Auto-dismiss alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.alert-dismissible').forEach(function (alert) {
        setTimeout(function () {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            if (bsAlert) bsAlert.close();
        }, 5000);
    });

    // Confirm delete on any form with data-confirm
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('submit', function (e) {
            if (!confirm(el.dataset.confirm)) e.preventDefault();
        });
    });

    // Add loading spinner to submit buttons on form submit
    document.querySelectorAll('form').forEach(function (form) {
        form.addEventListener('submit', function () {
            const btn = form.querySelector('[type=submit]');
            if (btn && !btn.dataset.noLoad) {
                btn.disabled = true;
                const original = btn.innerHTML;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>' + original;

                // Re-enable after 10s as fallback
                setTimeout(() => {
                    btn.disabled = false;
                    btn.innerHTML = original;
                }, 10000);
            }
        });
    });

    // =============================================
    // Limit duration_days max 365
    // =============================================
    const durationInput = document.getElementById('durationDays');

    if (durationInput) {
        durationInput.addEventListener('input', function () {

            // hapus karakter selain angka
            this.value = this.value.replace(/[^0-9]/g, '');

            let value = parseInt(this.value);

            // max 365
            if (value > 365) {
                this.value = 365;
            }

            // kosong kalau minus / invalid
            if (value < 0 || isNaN(value)) {
                this.value = '';
            }
        });
    }
});

    