        </div><!-- / container-xxl -->

        <!-- Footer -->
        <footer class="content-footer">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <span>
                    &copy; <?= date('Y') ?> <strong style="color:#64748b;"> NOL DERAJAT COFFEE</strong>  
                    All rights reserved.
                </span>
            </div>
        </footer>
        <!-- / Footer -->
    </div><!-- / content-wrapper -->
</div><!-- / layout-page -->
</div><!-- / layout-container -->
</div><!-- / layout-wrapper -->

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.all.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {

    /* ── Toast Helper ── */
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3500,
        timerProgressBar: true,
        customClass: { popup: 'swal2-toast-custom' },
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    window.showToast = function(icon, message) {
        Toast.fire({ icon, title: message });
    };

    /* ── Flashdata Notifications ── */
    <?php if($this->session->flashdata('success')): ?>
        showToast('success', '<?= addslashes($this->session->flashdata('success')) ?>');
    <?php endif; ?>
    <?php if($this->session->flashdata('error')): ?>
        showToast('error', '<?= addslashes($this->session->flashdata('error')) ?>');
    <?php endif; ?>
    <?php if($this->session->flashdata('info')): ?>
        showToast('info', '<?= addslashes($this->session->flashdata('info')) ?>');
    <?php endif; ?>

    /* ── Realtime Clock ── */
    const clockEl = document.getElementById('topbarClock');
    if (clockEl) {
        const updateClock = () => {
            const now = new Date();
            clockEl.textContent = now.toLocaleTimeString('id-ID', {
                hour: '2-digit', minute: '2-digit', second: '2-digit'
            });
        };
        updateClock();
        setInterval(updateClock, 1000);
    }

    /* ── Mobile Sidebar Drawer ── */
    const layoutMenu     = document.getElementById('layout-menu');
    const layoutOverlay  = document.getElementById('layoutOverlay');
    const mobileToggle   = document.getElementById('mobileMenuToggle');
    const mobileClose    = document.getElementById('mobileMenuClose');

    const openMobileMenu = () => {
        layoutMenu?.classList.add('show');
        layoutOverlay?.classList.add('show');
        document.body.style.overflow = 'hidden';
    };

    const closeMobileMenu = () => {
        layoutMenu?.classList.remove('show');
        layoutOverlay?.classList.remove('show');
        document.body.style.overflow = '';
    };

    mobileToggle?.addEventListener('click', openMobileMenu);
    mobileClose?.addEventListener('click', closeMobileMenu);
    layoutOverlay?.addEventListener('click', closeMobileMenu);

    /* ── Keyboard Shortcuts ── */
    document.addEventListener('keydown', function(e) {
        // F2: Focus POS search
        if (e.key === 'F2') {
            const s = document.getElementById('searchMenu');
            if (s) { e.preventDefault(); s.focus(); s.select(); }
        }
        // F4: Trigger checkout
        if (e.key === 'F4') {
            const btn = document.getElementById('btnCheckout');
            if (btn && !btn.disabled) { e.preventDefault(); btn.click(); }
        }
        // Ctrl+B: Toggle sidebar
        if (e.ctrlKey && e.key.toLowerCase() === 'b') {
            e.preventDefault();
            layoutMenu?.classList.contains('show') ? closeMobileMenu() : openMobileMenu();
        }
        // Escape: Close sidebar
        if (e.key === 'Escape') closeMobileMenu();
    });
});
</script>
</body>
</html>
