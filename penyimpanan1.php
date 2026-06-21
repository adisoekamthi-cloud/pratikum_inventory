<?php
session_start();
include 'koneksi.php';

//terupdate
$nama_user = $_SESSION['nama_lengkap'];
$username  = $_SESSION['username'];
$role      = ucfirst($_SESSION['role']);

// 1. Ambil data untuk opsi dropdown filter
$query_status      = mysqli_query($koneksi, "SELECT * FROM status_barang");
$query_penyimpanan = mysqli_query($koneksi, "SELECT * FROM penyimpanan");
$query_vendor      = mysqli_query($koneksi, "SELECT * FROM vendor");

// 2. Tangkap data filter dari URL jika ada (Metode GET)
$search             = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';
$filter_status      = isset($_GET['status_id']) ? mysqli_real_escape_string($koneksi, $_GET['status_id']) : '';
$filter_penyimpanan = isset($_GET['penyimpanan_id']) ? mysqli_real_escape_string($koneksi, $_GET['penyimpanan_id']) : '';
$filter_vendor      = isset($_GET['vendor_id']) ? mysqli_real_escape_string($koneksi, $_GET['vendor_id']) : '';

// 3. Menyusun query filter dinamis
$where_clauses = [];
if (!empty($search)) {
    $where_clauses[] = "barang.nama_barang LIKE '%$search%'";
}
if (!empty($filter_status)) {
    $where_clauses[] = "barang.status_id = '$filter_status'";
}
if (!empty($filter_penyimpanan)) {
    $where_clauses[] = "barang.penyimpanan_id = '$filter_penyimpanan'";
}
if (!empty($filter_vendor)) {
    $where_clauses[] = "barang.vendor_id = '$filter_vendor'";
}

$where_sql = "";
if (count($where_clauses) > 0) {
    $where_sql = " WHERE " . implode(" AND ", $where_clauses);
}

// 4. Query utama dengan penyesuaian kolom vendor.id_vendor dan barang.vendor_id
$query_string = "SELECT barang.*, 
                        barang.id AS id_barang, 
                        barang.status_id AS nama_status, 
                        barang.penyimpanan_id AS nama_penyimpanan, 
                        vendor.nama_vendor 
                 FROM barang 
                 LEFT JOIN vendor ON barang.vendor_id = vendor.id_vendor" . $where_sql;

$data = mysqli_query($koneksi, $query_string);

// Baru
$query_barang = mysqli_query($koneksi, "SELECT COUNT(*) AS total_barang FROM barang");
$data_barang  = mysqli_fetch_assoc($query_barang);
$total_barang = $data_barang['total_barang'];

// 2. Hitung Persentase Barang yang Butuh Restok (Pengganti Bounce Rate)
// Mencari barang yang stoknya sudah menyentuh atau di bawah limit_stok
$query_kritis = mysqli_query($koneksi, "SELECT COUNT(*) AS total_kritis FROM barang WHERE stok <= limit_stok");
$data_kritis  = mysqli_fetch_assoc($query_kritis);
$total_kritis = $data_kritis['total_kritis'];

// Rumus presentase agar aman dari pembagian dengan angka 0 (division by zero)
$bounce_rate_persen = $total_barang > 0 ? round(($total_kritis / $total_barang) * 100) : 0;

// 3. Hitung Total User Registrations
$query_users = mysqli_query($koneksi, "SELECT COUNT(*) AS total_users FROM users");
$data_users  = mysqli_fetch_assoc($query_users);
$total_users = $data_users['total_users'];

// 4. Hitung Total Distribusi Barang (Pengganti Unique Visitors)
$query_distribusi = mysqli_query($koneksi, "SELECT COUNT(*) AS total_distribusi FROM distribusi");
$data_distribusi  = mysqli_fetch_assoc($query_distribusi);
$total_distribusi = $data_distribusi['total_distribusi'];

// 1. PROSES TAMBAH DATA
if (isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_penyimpanan']);
    $lokasi = mysqli_real_escape_string($koneksi, $_POST['lokasi']);
    
    $query = "INSERT INTO penyimpanan (nama_penyimpanan, lokasi) VALUES ('$nama', '$lokasi')";
    if (mysqli_query($koneksi, $query)) {
        header("Location: penyimpanan.php?status=sukses");
    }
}

// 2. PROSES EDIT DATA
if (isset($_POST['ubah'])) {
    $id = $_POST['id'];
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_penyimpanan']);
    $lokasi = mysqli_real_escape_string($koneksi, $_POST['lokasi']);
    
    $query = "UPDATE penyimpanan SET nama_penyimpanan='$nama', lokasi='$lokasi' WHERE id='$id'";
    if (mysqli_query($koneksi, $query)) {
        header("Location: penyimpanan.php?status=sukses");
    }
}

// 3. PROSES HAPUS DATA
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM penyimpanan WHERE id='$id'");
    header("Location: penyimpanan.php");
}

// 4. AMBIL DATA UNTUK TOMBOL EDIT
$edit_data = null;
if (isset($_GET['edit'])) {
    $id_edit = $_GET['edit'];
    $res = mysqli_query($koneksi, "SELECT * FROM penyimpanan WHERE id='$id_edit'");
    $edit_data = mysqli_fetch_assoc($res);
}

// AMBIL SEMUA DATA UNTUK TABEL
$tampil = mysqli_query($koneksi, "SELECT * FROM penyimpanan ORDER BY id DESC");
?>
<!doctype html>
<html lang="en">
<!--begin::Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AdminLTE v4 | Dashboard</title>

    <!--begin::Accessibility Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <meta name="color-scheme" content="light dark" />
    <meta name="theme-color" content="#007bff" media="(prefers-color-scheme: light)" />
    <meta name="theme-color" content="#1a1a1a" media="(prefers-color-scheme: dark)" />
    <!--end::Accessibility Meta Tags-->

    <!--begin::Primary Meta Tags-->
    <meta name="title" content="AdminLTE v4 | Dashboard" />
    <meta name="author" content="ColorlibHQ" />
    <meta name="description"
        content="AdminLTE is a Free Bootstrap 5 Admin Dashboard, 30 example pages using Vanilla JS. Fully accessible with WCAG 2.1 AA compliance." />
    <meta name="keywords"
        content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard, bootstrap 5 dashboard, bootstrap 5 charts, bootstrap 5 calendar, bootstrap 5 datepicker, bootstrap 5 tables, bootstrap 5 datatable, vanilla js datatable, colorlibhq, colorlibhq dashboard, colorlibhq admin dashboard, accessible admin panel, WCAG compliant" />
    <!--end::Primary Meta Tags-->

    <!--begin::Accessibility Features-->
    <!-- Skip links will be dynamically added by accessibility.js -->
    <meta name="supported-color-schemes" content="light dark" />
    <link rel="preload" href="./css/adminlte.css" as="style" />
    <!--end::Accessibility Features-->

    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous" media="print"
        onload="this.media = 'all'" />
    <!--end::Fonts-->

    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
        crossorigin="anonymous" />
    <!--end::Third Party Plugin(OverlayScrollbars)-->

    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
        crossorigin="anonymous" />
    <!--end::Third Party Plugin(Bootstrap Icons)-->

    <!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="./css/adminlte.css" />
    <!--end::Required Plugin(AdminLTE)-->

    <!-- apexcharts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css"
        integrity="sha256-4MX+61mt9NVvvuPjUWdUdyfZfxSB1/Rf9WtqRHgG5S0=" crossorigin="anonymous" />

    <!-- jsvectormap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/css/jsvectormap.min.css"
        integrity="sha256-+uGLJmmTKOqBr+2E6KDYs/NRsHxSkONXFHUL0fy2O/4=" crossorigin="anonymous" />
</head>
<!--end::Head-->
<!--begin::Body-->

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
        <!--begin::Header-->
        <nav class="app-header navbar navbar-expand bg-body">
            <!--begin::Container-->
            <div class="container-fluid">
                <!--begin::Start Navbar Links-->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                            <i class="bi bi-list"></i>
                        </a>
                    </li>
                    <li class="nav-item d-none d-md-block">
                        <a href="#" class="nav-link">Home</a>
                    </li>
                    <li class="nav-item d-none d-md-block">
                        <a href="#" class="nav-link">Contact</a>
                    </li>
                </ul>
                <!--end::Start Navbar Links-->

                <!--begin::End Navbar Links-->
                <ul class="navbar-nav ms-auto">
                    <!--begin::Navbar Search-->
                    <li class="nav-item">
                        <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                            <i class="bi bi-search"></i>
                        </a>
                    </li>
                    <!--end::Navbar Search-->



                    <!--begin::User Menu Dropdown-->
                    <li class="nav-item dropdown user-menu">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img src="assets/img/safii.jpg" class="user-image rounded-circle shadow"
                                alt="User Image" />
                            <span class="d-none d-md-inline">muhamadsafiiwibowo</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                            <!--begin::User Image-->
                            <li class="user-header text-bg-primary">
                                <img src="assets/img/safii.jpg" class="rounded-circle shadow"
                                    alt="User Image" />
                                <p>
                                    muhamadsafiiwibowo - Admin
                                    <small>Member since juni 2026</small>
                                </p>
                            </li>
                            <!--end::User Image-->
                            <!--begin::Menu Body-->
                            <li class="user-body">
                                <!--begin::Row-->
                                <div class="row">
                                    <div class="col-4 text-center">
                                        <a href="#">Followers</a>
                                    </div>
                                    <div class="col-4 text-center">
                                        <a href="#">Sales</a>
                                    </div>
                                    <div class="col-4 text-center">
                                        <a href="#">Friends</a>
                                    </div>
                                </div>
                                <!--end::Row-->
                            </li>
                            <!--end::Menu Body-->
                            <!--begin::Menu Footer-->
                            <li class="user-footer">
                                <a href="#" class="btn btn-outline-secondary">Profile</a>
                                <a href="#" class="btn btn-outline-danger float-end">Sign out</a>
                            </li>
                            <!--end::Menu Footer-->
                        </ul>
                    </li>
                    <!--end::User Menu Dropdown-->
                </ul>
                <!--end::End Navbar Links-->
            </div>
            <!--end::Container-->
        </nav>
        <!--end::Header-->
        <!--begin::Sidebar-->
        <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
            <!--begin::Sidebar Brand-->
            <div class="sidebar-brand">
                <!--begin::Brand Link-->
                <a href="./index.html" class="brand-link">
                    <!--begin::Brand Image-->
                    <img src="./assets/img/AdminLTELogo.png" alt="AdminLTE Logo"
                        class="brand-image opacity-75 shadow" />
                    <!--end::Brand Image-->
                    <!--begin::Brand Text-->
                    <span class="brand-text fw-light">AdminLTE 4</span>
                    <!--end::Brand Text-->
                </a>
                <!--end::Brand Link-->
            </div>
            <!--end::Sidebar Brand-->
            <!--begin::Sidebar Wrapper-->
            <div class="sidebar-wrapper">
                <nav class="mt-2">
                    <!--begin::Sidebar Menu-->
                    <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation"
                        aria-label="Main navigation" data-accordion="false" id="navigation">
                        <li class="nav-item menu-open">
                            <a href="#" class="nav-link active">
                                <i class="nav-icon bi bi-speedometer"></i>
                                <p>
                                    Dashboard
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="Status.php" class="nav-link active">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Status Produk</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="penyimpanan.php" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Penyimpanan Produk</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="stok.php" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Stok</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="distribusi.php" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Distribusi</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="vendor.php" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Vendor</p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="logout.php" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Logout</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!--end::Sidebar Menu-->
                </nav>
            </div>
            <!--end::Sidebar Wrapper-->
        </aside>
        <!--end::Sidebar-->
        <!--begin::App Main-->
        <main class="app-main">
            <!--begin::App Content Header-->
            <div class="app-content-header">
                <!--begin::Container-->
                <div class="container-fluid">
                    <!--begin::Row-->
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Dashboard</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                            </ol>
                        </div>
                    </div>
                    <!--end::Row-->
                </div>
                <!--end::Container-->
            </div>
            <!--end::App Content Header-->
            <!--begin::App Content-->
            <div class="app-content">
                <!--begin::Container-->
                <div class="container-fluid">
                    <!--begin::Row-->
                    <div class="row">
                        <!--begin::Col-->
                        <div class="col-lg-3 col-6">
                            <!--begin::Small Box Widget 1-->
                            <div class="small-box text-bg-primary">
                          
                            </div>
                            <!--end::Small Box Widget 1-->
                        </div>
                        <!--end::Col-->
                       <body class="bg-light p-4">
<div class="container bg-white p-4 rounded shadow-sm">
    <h3 class="mb-4">📦 Manajemen Lokasi Penyimpanan</h3>
    
    <div class="row">
        <!-- FORM INPUT (TAMBAH / EDIT) -->
        <div class="col-md-4 mb-4">
            <div class="card p-3 shadow-sm">
                <h5><?= $edit_data ? 'Edit Lokasi' : 'Tambah Lokasi Baru' ?></h5>
                <form method="POST" action="">
                    <?php if ($edit_data) { ?>
                        <input type="hidden" name="id" value="<?= $edit_data['id']; ?>">
                    <?php } ?>
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Penyimpanan / Gudang</label>
                        <input type="text" name="nama_penyimpanan" class="form-control" required value="<?= $edit_data ? $edit_data['nama_penyimpanan'] : ''; ?>" placeholder="Contoh: Gudang Utama A">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Detail Lokasi (Keterangan)</label>
                        <input type="text" name="lokasi" class="form-control" value="<?= $edit_data ? $edit_data['lokasi'] : ''; ?>" placeholder="Contoh: Blok B Lantai 2">
                    </div>
                    
                    <button type="submit" name="<?= $edit_data ? 'ubah' : 'tambah' ?>" class="btn <?= $edit_data ? 'btn-warning' : 'btn-primary' ?> w-100">
                        <?= $edit_data ? 'Simpan Perubahan' : 'Tambah Data' ?>
                    </button>
                    <?php if ($edit_data) { echo '<a href="penyimpanan.php" class="btn btn-secondary w-100 mt-2">Batal</a>'; } ?>
                </form>
            </div>
        </div>

        <!-- TABEL DATA -->
        <div class="col-md-8">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nama Penyimpanan</th>
                        <th>Keterangan Lokasi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($tampil)) { ?>
                    <tr>
                        <td><?= $row['id']; ?></td>
                        <td><strong><?= htmlspecialchars($row['nama_penyimpanan']); ?></strong></td>
                        <td><?= htmlspecialchars($row['lokasi']); ?></td>
                        <td>
                            <a href="penyimpanan.php?edit=<?= $row['id']; ?>" class="btn btn-sm btn-info text-white">Edit</a>
                            <a href="penyimpanan.php?hapus=<?= $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus lokasi ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


    </div>
    </div>
    </div>
    <!-- /.Start col -->
    </div>
    <!-- /.row (main row) -->
    </div>
    <!--end::Container-->
    </div>
    <!--end::App Content-->
    </main>
    <!--end::App Main-->
    <!--begin::Footer-->
    <footer class="app-footer">
        <!--begin::To the end-->
        <div class="float-end d-none d-sm-inline">Anything you want</div>
        <!--end::To the end-->
        <!--begin::Copyright-->
        <strong>
            Copyright &copy; 2014-2026&nbsp;
            <a href="https://adminlte.io" class="text-decoration-none">AdminLTE.io</a>.
        </strong>
        All rights reserved.
        <!--end::Copyright-->
    </footer>
    <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"
        crossorigin="anonymous"></script>
    <!--end::Third Party Plugin(OverlayScrollbars)-->
    <!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous">
    </script>
    <!--end::Required Plugin(popperjs for Bootstrap 5)-->
    <!--begin::Required Plugin(Bootstrap 5)-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js" crossorigin="anonymous">
    </script>
    <!--end::Required Plugin(Bootstrap 5)-->
    <!--begin::Required Plugin(AdminLTE)-->
    <script src="./js/adminlte.js"></script>
    <!--end::Required Plugin(AdminLTE)-->
    <!--begin::OverlayScrollbars Configure-->
    <script>
        const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
        const Default = {
            scrollbarTheme: 'os-theme-light',
            scrollbarAutoHide: 'leave',
            scrollbarClickScroll: true,
        };
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);

            // Disable OverlayScrollbars on mobile devices to prevent touch interference
            const isMobile = window.innerWidth <= 992;

            if (
                sidebarWrapper &&
                OverlayScrollbarsGlobal?.OverlayScrollbars !== undefined &&
                !isMobile
            ) {
                OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
                    scrollbars: {
                        theme: Default.scrollbarTheme,
                        autoHide: Default.scrollbarAutoHide,
                        clickScroll: Default.scrollbarClickScroll,
                    },
                });
            }
        });
    </script>
    <!--end::OverlayScrollbars Configure-->

    <!-- OPTIONAL SCRIPTS -->

    <!-- sortablejs -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js" crossorigin="anonymous"></script>

    <!-- sortablejs -->
    <script>
        new Sortable(document.querySelector('.connectedSortable'), {
            group: 'shared',
            handle: '.card-header',
        });

        const cardHeaders = document.querySelectorAll('.connectedSortable .card-header');
        cardHeaders.forEach((cardHeader) => {
            cardHeader.style.cursor = 'move';
        });
    </script>

    <!-- apexcharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.min.js"
        integrity="sha256-+vh8GkaU7C9/wbSLIcwq82tQ2wTf44aOHA8HlBMwRI8=" crossorigin="anonymous"></script>

    <!-- ChartJS -->
    <script>
        // NOTICE!! DO NOT USE ANY OF THIS JAVASCRIPT
        // IT'S ALL JUST JUNK FOR DEMO
        // ++++++++++++++++++++++++++++++++++++++++++

        const sales_chart_options = {
            series: [{
                    name: 'Digital Goods',
                    data: [28, 48, 40, 19, 86, 27, 90],
                },
                {
                    name: 'Electronics',
                    data: [65, 59, 80, 81, 56, 55, 40],
                },
            ],
            chart: {
                height: 300,
                type: 'area',
                toolbar: {
                    show: false,
                },
            },
            legend: {
                show: false,
            },
            colors: ['#0d6efd', '#20c997'],
            dataLabels: {
                enabled: false,
            },
            stroke: {
                curve: 'smooth',
            },
            xaxis: {
                type: 'datetime',
                categories: [
                    '2023-01-01',
                    '2023-02-01',
                    '2023-03-01',
                    '2023-04-01',
                    '2023-05-01',
                    '2023-06-01',
                    '2023-07-01',
                ],
            },
            tooltip: {
                x: {
                    format: 'MMMM yyyy',
                },
            },
        };

        const sales_chart = new ApexCharts(
            document.querySelector('#revenue-chart'),
            sales_chart_options,
        );
        sales_chart.render();
    </script>

    <!-- jsvectormap -->
    <script src="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/js/jsvectormap.min.js"
        integrity="sha256-/t1nN2956BT869E6H4V1dnt0X5pAQHPytli+1nTZm2Y=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/maps/world.js"
        integrity="sha256-XPpPaZlU8S/HWf7FZLAncLg2SAkP8ScUTII89x9D3lY=" crossorigin="anonymous"></script>

    <!-- jsvectormap -->
    <script>
        // World map by jsVectorMap
        new jsVectorMap({
            selector: '#world-map',
            map: 'world',
        });

        // Sparkline charts
        const option_sparkline1 = {
            series: [{
                data: [1000, 1200, 920, 927, 931, 1027, 819, 930, 1021],
            }, ],
            chart: {
                type: 'area',
                height: 50,
                sparkline: {
                    enabled: true,
                },
            },
            stroke: {
                curve: 'straight',
            },
            fill: {
                opacity: 0.3,
            },
            yaxis: {
                min: 0,
            },
            colors: ['#DCE6EC'],
        };

        const sparkline1 = new ApexCharts(document.querySelector('#sparkline-1'), option_sparkline1);
        sparkline1.render();

        const option_sparkline2 = {
            series: [{
                data: [515, 519, 520, 522, 652, 810, 370, 627, 319, 630, 921],
            }, ],
            chart: {
                type: 'area',
                height: 50,
                sparkline: {
                    enabled: true,
                },
            },
            stroke: {
                curve: 'straight',
            },
            fill: {
                opacity: 0.3,
            },
            yaxis: {
                min: 0,
            },
            colors: ['#DCE6EC'],
        };

        const sparkline2 = new ApexCharts(document.querySelector('#sparkline-2'), option_sparkline2);
        sparkline2.render();

        const option_sparkline3 = {
            series: [{
                data: [15, 19, 20, 22, 33, 27, 31, 27, 19, 30, 21],
            }, ],
            chart: {
                type: 'area',
                height: 50,
                sparkline: {
                    enabled: true,
                },
            },
            stroke: {
                curve: 'straight',
            },
            fill: {
                opacity: 0.3,
            },
            yaxis: {
                min: 0,
            },
            colors: ['#DCE6EC'],
        };

        const sparkline3 = new ApexCharts(document.querySelector('#sparkline-3'), option_sparkline3);
        sparkline3.render();
    </script>
    <!--end::Script-->
</body>
<!--end::Body-->

</html>
