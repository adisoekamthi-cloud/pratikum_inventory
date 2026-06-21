    <footer class="app-footer py-3 bg-body border-top" style="font-size: 0.82rem; border-top: 1px solid var(--bs-border-color) !important;">
        <?php
        require_once dirname(__DIR__) . '/includes/app_settings.php';
        $appSettings = app_settings_load();
        ?>
        <div class="container-fluid text-center text-muted">
            <span>Copyright &copy; <?= date('Y'); ?></span>
            <a href="#" class="text-decoration-none fw-semibold text-primary"><?= htmlspecialchars($appSettings['app_name']); ?></a>.
            <span>All rights reserved.</span>
        </div>
    </footer>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="./js/adminlte.js"></script>
    <?= $additionalScripts ?? ''; ?>
</body>
</html>
