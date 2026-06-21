<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <?php
    require_once dirname(__DIR__) . '/includes/app_settings.php';
    $appSettings = app_settings_load();
    ?>
    <div class="sidebar-brand">
        <a href="./index.php" class="brand-link">
            <img src="<?= htmlspecialchars(app_settings_asset_url($appSettings, 'sidebar_header_image', '')); ?>" alt="<?= htmlspecialchars($appSettings['app_name']); ?> Logo" class="brand-image opacity-75 shadow" />
            <span class="brand-text fw-light"><?= htmlspecialchars($appSettings['app_name']); ?></span>
        </a>
    </div>
    <?php
    $sidebarPages = ['dashboard', 'status', 'penyimpanan', 'stok', 'distribusi', 'vendor', 'users', 'setting'];
    ?>
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation" aria-label="Main navigation" data-accordion="false" id="navigation">
                <li class="nav-item">
                    <a href="index.php" class="nav-link <?= ($activePage ?? '') === 'dashboard' ? 'active' : ''; ?>">
                        <i class="nav-icon bi bi-speedometer"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <li class="nav-item">
                    <a href="status.php" class="nav-link <?= ($activePage ?? '') === 'status' ? 'active' : ''; ?>">
                        <i class="nav-icon bi bi-tags"></i>
                        <p>Status Produk</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="penyimpanan.php" class="nav-link <?= ($activePage ?? '') === 'penyimpanan' ? 'active' : ''; ?>">
                        <i class="nav-icon bi bi-box-seam"></i>
                        <p>Penyimpanan Produk</p>
                    </a>
                </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a href="stok.php" class="nav-link <?= ($activePage ?? '') === 'stok' ? 'active' : ''; ?>">
                        <i class="nav-icon bi bi-clipboard-data"></i>
                        <p>Stok</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="distribusi.php" class="nav-link <?= ($activePage ?? '') === 'distribusi' ? 'active' : ''; ?>">
                        <i class="nav-icon bi bi-truck"></i>
                        <p>Distribusi</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="tambah_barang.php" class="nav-link <?= ($activePage ?? '') === 'tambah' ? 'active' : ''; ?>">
                        <i class="nav-icon bi bi-plus-square"></i>
                        <p>Tambah Barang</p>
                    </a>
                </li>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <li class="nav-item">
                    <a href="vendor.php" class="nav-link <?= ($activePage ?? '') === 'vendor' ? 'active' : ''; ?>">
                        <i class="nav-icon bi bi-building"></i>
                        <p>Vendor</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="users.php" class="nav-link <?= ($activePage ?? '') === 'users' ? 'active' : ''; ?>">
                        <i class="nav-icon bi bi-people"></i>
                        <p>Users</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="setting.php" class="nav-link <?= ($activePage ?? '') === 'setting' ? 'active' : ''; ?>">
                        <i class="nav-icon bi bi-gear"></i>
                        <p>Setting</p>
                    </a>
                </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a href="logout.php" class="nav-link" onclick="return confirmAdminLogout(this.href);">
                        <i class="nav-icon bi bi-box-arrow-right"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
