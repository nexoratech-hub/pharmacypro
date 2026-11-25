<?php
// ================================================================
// FILE: includes/super-admin-footer.php
// ================================================================
?>
<footer class="sa-footer">
    <div class="sa-footer-brand">
        <i class="fas fa-crown"></i>
        <span>&copy; <?= date('Y') ?> BismartTech. All rights reserved.</span>
    </div>
    <div class="sa-footer-links">
        <a href="#">Privacy</a>
        <a href="#">Terms</a>
        <a href="#">Support</a>
        <a href="#">v1.0.0</a>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleSidebar() {
        if (window.innerWidth < 992) {
            document.body.classList.toggle('sidebar-open');
        } else {
            document.body.classList.toggle('sidebar-collapsed');
            localStorage.setItem('sa_sidebar_collapsed',
                document.body.classList.contains('sidebar-collapsed') ? '1' : '0');
        }
    }

    (function() {
        if (localStorage.getItem('sa_sidebar_collapsed') === '1' && window.innerWidth >= 992) {
            document.body.classList.add('sidebar-collapsed');
        }
    })();

    document.querySelectorAll('.sa-nav-link').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 992) {
                document.body.classList.remove('sidebar-open');
            }
        });
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 992) {
            document.body.classList.remove('sidebar-open');
        }
    });

    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'b') {
            e.preventDefault();
            toggleSidebar();
        }
    });
</script>