<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] !== 'login' || !in_array($_SESSION['role'], ['admin', 'pengguna'])) {
    header("location:login.php");
    exit;
}
include 'koneksi.php';

$nama_user = htmlspecialchars($_SESSION['nama_lengkap']);
$username  = htmlspecialchars($_SESSION['username']);
$role      = ucfirst($_SESSION['role']);

$message = $_SESSION['flash_index_message'] ?? '';
$message_type = $_SESSION['flash_index_type'] ?? '';
unset($_SESSION['flash_index_message'], $_SESSION['flash_index_type']);

$query_status      = mysqli_query($koneksi, "SELECT * FROM status_barang");
$query_penyimpanan = mysqli_query($koneksi, "SELECT * FROM penyimpanan");
$query_vendor      = mysqli_query($koneksi, "SELECT * FROM vendor");

$search             = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';
$filter_status      = isset($_GET['status_id']) ? mysqli_real_escape_string($koneksi, $_GET['status_id']) : '';
$filter_penyimpanan = isset($_GET['penyimpanan_id']) ? mysqli_real_escape_string($koneksi, $_GET['penyimpanan_id']) : '';
$filter_vendor      = isset($_GET['vendor_id']) ? mysqli_real_escape_string($koneksi, $_GET['vendor_id']) : '';

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

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}
$perPage = 10;
$offset = ($page - 1) * $perPage;

$count_query = "SELECT COUNT(*) AS total
                FROM barang
                LEFT JOIN vendor ON barang.vendor_id = vendor.id_vendor
                LEFT JOIN status_barang ON barang.status_id = status_barang.id
                LEFT JOIN penyimpanan ON barang.penyimpanan_id = penyimpanan.id" . $where_sql;
$count_result = mysqli_query($koneksi, $count_query);
$filtered_total = 0;
if ($count_result) {
    $count_row = mysqli_fetch_assoc($count_result);
    $filtered_total = (int) $count_row['total'];
}
$total_pages = max(1, (int) ceil($filtered_total / $perPage));
if ($page > $total_pages) {
    $page = $total_pages;
    $offset = ($page - 1) * $perPage;
}

$query_string = "SELECT barang.*, 
                        barang.id AS id_barang, 
                        status_barang.nama_status, 
                        penyimpanan.nama_penyimpanan, 
                        vendor.nama_vendor 
                 FROM barang 
                 LEFT JOIN vendor ON barang.vendor_id = vendor.id_vendor
                 LEFT JOIN status_barang ON barang.status_id = status_barang.id
                 LEFT JOIN penyimpanan ON barang.penyimpanan_id = penyimpanan.id" . $where_sql . "
                 ORDER BY barang.id DESC
                 LIMIT $perPage OFFSET $offset";
$data = mysqli_query($koneksi, $query_string);

$query_barang = mysqli_query($koneksi, "SELECT COUNT(*) AS total_barang FROM barang");
$data_barang  = mysqli_fetch_assoc($query_barang);
$total_barang = $data_barang['total_barang'];

$query_kritis = mysqli_query($koneksi, "SELECT COUNT(*) AS total_kritis FROM barang WHERE stok <= limit_stok");
$data_kritis  = mysqli_fetch_assoc($query_kritis);
$total_kritis = $data_kritis['total_kritis'];

$bounce_rate_persen = $total_barang > 0 ? round(($total_kritis / $total_barang) * 100) : 0;

$query_users = mysqli_query($koneksi, "SELECT COUNT(*) AS total_users FROM users");
$data_users  = mysqli_fetch_assoc($query_users);
$total_users = $data_users['total_users'];

$query_distribusi = mysqli_query($koneksi, "SELECT COUNT(*) AS total_distribusi FROM distribusi");
$data_distribusi  = mysqli_fetch_assoc($query_distribusi);
$total_distribusi = $data_distribusi['total_distribusi'];

$pageTitle = 'Dashboard | Admin';
$activePage = 'dashboard';
include __DIR__ . '/components/head.php';
include __DIR__ . '/components/topbar.php';
include __DIR__ . '/components/sidebar.php';
?>
<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
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
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="row g-4">
                <?php if ($_SESSION['role'] === 'admin'): ?>
                <div class="col-lg-3 col-6">
                    <div class="small-box text-bg-primary">
                        <div class="inner">
                            <h3><?= $total_barang; ?></h3>
                            <p>Total Produk</p>
                        </div>
                        <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M2.25 2.25a.75.75 0 000 1.5h1.386c.17 0 .318.114.362.278l2.558 9.592a3.752 3.752 0 00-2.806 3.63c0 .414.336.75.75.75h15.75a.75.75 0 000-1.5H5.378A2.25 2.25 0 017.5 15h11.218a.75.75 0 00.674-.421 60.358 60.358 0 002.96-7.228.75.75 0 00-.525-.965A60.864 60.864 0 005.68 4.509l-.232-.867A1.875 1.875 0 003.636 2.25H2.25zM3.75 20.25a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zM16.5 20.25a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0z"></path>
                        </svg>
                        <a href="index.php" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                            Lihat Dashboard <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box text-bg-success">
                        <div class="inner">
                            <h3><?= $bounce_rate_persen; ?><sup class="fs-5">%</sup></h3>
                            <p>Stok Perlu Restok</p>
                        </div>
                        <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M18.375 2.25c-1.035 0-1.875.84-1.875 1.875v15.75c0 1.035.84 1.875 1.875 1.875h.75c1.035 0 1.875-.84 1.875-1.875V4.125c0-1.036-.84-1.875-1.875-1.875h-.75zM9.75 8.625c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-.75a1.875 1.875 0 01-1.875-1.875V8.625zM3 13.125c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v6.75c0 1.035-.84 1.875-1.875 1.875h-.75A1.875 1.875 0 013 19.875v-6.75z"></path>
                        </svg>
                        <a href="stok.php" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                            Lihat Stok <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box text-bg-warning">
                        <div class="inner">
                            <h3><?= $total_users; ?></h3>
                            <p>User Terdaftar</p>
                        </div>
                        <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M6.25 6.375a4.125 4.125 0 118.25 0 4.125 4.125 0 01-8.25 0zM3.25 19.125a7.125 7.125 0 0114.25 0v.003l-.001.119a.75.75 0 01-.363.63 13.067 13.067 0 01-6.761 1.873c-2.472 0-4.786-.684-6.76-1.873a.75.75 0 01-.364-.63l-.001-.122zM19.75 7.5a.75.75 0 00-1.5 0v2.25H16a.75.75 0 000 1.5h2.25v2.25a.75.75 0 001.5 0v-2.25H22a.75.75 0 000-1.5h-2.25V7.5z"></path>
                        </svg>
                        <a href="users.php" class="small-box-footer link-dark link-underline-opacity-0 link-underline-opacity-50-hover">
                            Kelola User <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box text-bg-danger">
                        <div class="inner">
                            <h3><?= $total_distribusi; ?></h3>
                            <p>Log Distribusi</p>
                        </div>
                        <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path clip-rule="evenodd" fill-rule="evenodd" d="M2.25 13.5a8.25 8.25 0 018.25-8.25.75.75 0 01.75.75v6.75H18a.75.75 0 01.75.75 8.25 8.25 0 01-16.5 0z"></path>
                            <path clip-rule="evenodd" fill-rule="evenodd" d="M12.75 3a.75.75 0 01.75-.75 8.25 8.25 0 018.25 8.25.75.75 0 01-.75.75h-7.5a.75.75 0 01-.75-.75V3z"></path>
                        </svg>
                        <a href="distribusi.php" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                            Lihat Riwayat <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                </div>
                <?php else: ?>
                <div class="col-lg-4 col-6">
                    <div class="small-box text-bg-primary">
                        <div class="inner">
                            <h3><?= $total_barang; ?></h3>
                            <p>Total Produk</p>
                        </div>
                        <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M2.25 2.25a.75.75 0 000 1.5h1.386c.17 0 .318.114.362.278l2.558 9.592a3.752 3.752 0 00-2.806 3.63c0 .414.336.75.75.75h15.75a.75.75 0 000-1.5H5.378A2.25 2.25 0 017.5 15h11.218a.75.75 0 00.674-.421 60.358 60.358 0 002.96-7.228.75.75 0 00-.525-.965A60.864 60.864 0 005.68 4.509l-.232-.867A1.875 1.875 0 003.636 2.25H2.25zM3.75 20.25a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zM16.5 20.25a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0z"></path>
                        </svg>
                        <a href="index.php" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                            Lihat Dashboard <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 col-6">
                    <div class="small-box text-bg-success">
                        <div class="inner">
                            <h3><?= $bounce_rate_persen; ?><sup class="fs-5">%</sup></h3>
                            <p>Stok Perlu Restok</p>
                        </div>
                        <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M18.375 2.25c-1.035 0-1.875.84-1.875 1.875v15.75c0 1.035.84 1.875 1.875 1.875h.75c1.035 0 1.875-.84 1.875-1.875V4.125c0-1.036-.84-1.875-1.875-1.875h-.75zM9.75 8.625c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-.75a1.875 1.875 0 01-1.875-1.875V8.625zM3 13.125c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v6.75c0 1.035-.84 1.875-1.875 1.875h-.75A1.875 1.875 0 013 19.875v-6.75z"></path>
                        </svg>
                        <a href="stok.php" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                            Lihat Stok <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 col-12">
                    <div class="small-box text-bg-danger">
                        <div class="inner">
                            <h3><?= $total_distribusi; ?></h3>
                            <p>Log Distribusi</p>
                        </div>
                        <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path clip-rule="evenodd" fill-rule="evenodd" d="M2.25 13.5a8.25 8.25 0 018.25-8.25.75.75 0 01.75.75v6.75H18a.75.75 0 01.75.75 8.25 8.25 0 01-16.5 0z"></path>
                            <path clip-rule="evenodd" fill-rule="evenodd" d="M12.75 3a.75.75 0 01.75-.75 8.25 8.25 0 018.25 8.25.75.75 0 01-.75.75h-7.5a.75.75 0 01-.75-.75V3z"></path>
                        </svg>
                        <a href="distribusi.php" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                            Lihat Riwayat <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <style>
                .btn-edit-user-custom {
                    color: #0d6efd;
                    background: rgba(13, 110, 253, 0.08);
                    border: 1px solid rgba(13, 110, 253, 0.15);
                    border-radius: 8px;
                    font-weight: 600;
                    font-size: 0.8rem;
                    padding: 0.35rem 0.75rem;
                    transition: all 0.2s ease;
                    text-decoration: none;
                }
                .btn-edit-user-custom:hover {
                    color: #ffffff;
                    background: #0d6efd;
                    border-color: #0d6efd;
                    transform: translateY(-1px);
                    box-shadow: 0 4px 6px -1px rgba(13, 110, 253, 0.2);
                }
                .btn-hapus-user-custom {
                    color: #dc3545;
                    background: rgba(220, 53, 69, 0.08);
                    border: 1px solid rgba(220, 53, 69, 0.15);
                    border-radius: 8px;
                    font-weight: 600;
                    font-size: 0.8rem;
                    padding: 0.35rem 0.75rem;
                    transition: all 0.2s ease;
                    text-decoration: none;
                }
                .btn-hapus-user-custom:hover {
                    color: #ffffff;
                    background: #dc3545;
                    border-color: #dc3545;
                    transform: translateY(-1px);
                    box-shadow: 0 4px 6px -1px rgba(220, 53, 69, 0.2);
                }
                .animate-btn {
                    transition: all 0.2s ease;
                }
                .animate-btn:hover {
                    transform: translateY(-1px);
                    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.35) !important;
                }
            </style>

            <div class="card mt-4 shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-box-seam me-2"></i> Master Data Barang</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="" class="row g-3 mb-4 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-secondary mb-1" style="font-size: 0.85rem;">Cari Produk</label>
                            <input type="text" name="search" class="form-control" placeholder="Cari nama barang..." value="<?= htmlspecialchars($search); ?>" style="border-radius: 8px;">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-secondary mb-1" style="font-size: 0.85rem;">Filter Status</label>
                            <select name="status_id" class="form-select" style="border-radius: 8px;">
                                <option value="">-- Semua Status --</option>
                                <?php 
                                mysqli_data_seek($query_status, 0);
                                while ($st = mysqli_fetch_assoc($query_status)) { ?>
                                    <option value="<?= $st['id']; ?>" <?= $filter_status == $st['id'] ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($st['nama_status']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-secondary mb-1" style="font-size: 0.85rem;">Filter Vendor</label>
                            <select name="vendor_id" class="form-select" style="border-radius: 8px;">
                                <option value="">-- Semua Vendor --</option>
                                <?php 
                                mysqli_data_seek($query_vendor, 0);
                                while ($vd = mysqli_fetch_assoc($query_vendor)) { ?>
                                    <option value="<?= $vd['id_vendor']; ?>" <?= $filter_vendor == $vd['id_vendor'] ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($vd['nama_vendor']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-2 d-grid">
                            <button type="submit" class="btn btn-primary fw-semibold animate-btn" style="border-radius: 8px; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); border: none;">
                                <i class="bi bi-funnel me-1"></i> Filter
                            </button>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-secondary fw-semibold" style="font-size: 0.85rem; border-bottom: 1px solid #f1f5f9;">
                                <tr>
                                    <th width="80" class="ps-4 text-center">No</th>
                                    <th>Nama Barang</th>
                                    <th>Vendor</th>
                                    <th>Status</th>
                                    <th>Lokasi Penyimpanan</th>
                                    <th>Harga Barang</th>
                                    <th>Stok</th>
                                    <th>Batas Minimum</th>
                                    <?php if ($_SESSION['role'] === 'admin'): ?>
                                    <th width="200" class="pe-4 text-end">Aksi</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($data && mysqli_num_rows($data) > 0) {
                                    $no = $offset + 1;
                                    while ($row = mysqli_fetch_assoc($data)) {
                                        $stok_kritis = ($row['stok'] <= $row['limit_stok']);
                                ?>
                                        <tr>
                                            <td class="ps-4 text-center text-muted fw-semibold"><?= $no++; ?></td>
                                            <td><strong><?= htmlspecialchars($row['nama_barang']); ?></strong></td>
                                            <td><span class="badge bg-secondary"><?= htmlspecialchars($row['nama_vendor'] ?? 'Belum Set'); ?></span></td>
                                            <td>
                                                <span class="badge bg-info text-dark"><?= htmlspecialchars($row['nama_status'] ?? $row['status_id']); ?></span>
                                            </td>
                                            <td><?= htmlspecialchars($row['nama_penyimpanan'] ?? $row['penyimpanan_id']); ?></td>
                                            <td><strong>Rp <?= number_format($row['harga_barang'], 0, ',', '.'); ?></strong></td>
                                            <td>
                                                <?php if ($stok_kritis) { ?>
                                                    <span class="badge bg-danger">Sisa: <?= $row['stok']; ?></span>
                                                <?php } else { ?>
                                                    <span class="badge bg-success"><?= $row['stok']; ?></span>
                                                <?php } ?>
                                            </td>
                                            <td><span class="text-muted"><?= $row['limit_stok']; ?></span></td>
                                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                            <td class="pe-4 text-end">
                                                <div class="d-inline-flex gap-2">
                                                    <a href="edit_barang.php?id=<?= $row['id']; ?>" class="btn btn-edit-user-custom btn-sm">
                                                        <i class="bi bi-pencil me-1"></i> Edit
                                                    </a>
                                                    <a href="hapus_barang.php?id=<?= $row['id']; ?>" class="btn btn-hapus-user-custom btn-hapus-barang btn-sm" data-nama="<?= htmlspecialchars($row['nama_barang']); ?>">
                                                        <i class="bi bi-trash me-1"></i> Hapus
                                                    </a>
                                                </div>
                                            </td>
                                            <?php endif; ?>
                                        </tr>
                                <?php 
                                    }
                                } else {
                                    $colCount = $_SESSION['role'] === 'admin' ? 9 : 8;
                                    echo '<tr><td colspan="' . $colCount . '" class="text-center text-muted py-4">Tidak ada data barang yang cocok dengan filter.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if ($total_pages > 1): ?>
                        <nav aria-label="Pagination daftar barang" class="mt-3">
                            <ul class="pagination justify-content-center mb-0">
                                <?php for ($i = 1; $i <= $total_pages; $i++) {
                                    $page_params = $_GET;
                                    $page_params['page'] = $i;
                                    $page_url = 'index.php?' . http_build_query($page_params);
                                ?>
                                    <li class="page-item <?= $i === $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="<?= htmlspecialchars($page_url); ?>"><?= $i; ?></a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>
<?php
$additionalScripts = '';
$swal_icon = in_array($message_type, ['success', 'warning', 'error', 'info'], true) ? $message_type : 'info';
$additionalScripts = '
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll(".btn-hapus-barang").forEach(function (button) {
        button.addEventListener("click", function (event) {
            event.preventDefault();
            const deleteUrl = this.getAttribute("href");
            const nama = this.dataset.nama || "barang ini";

            Swal.fire({
                icon: "warning",
                title: "Hapus barang?",
                text: "Data " + nama + " akan dihapus permanen.",
                showCancelButton: true,
                confirmButtonText: "Ya, hapus",
                cancelButtonText: "Batal",
                customClass: {
                    confirmButton: "btn btn-danger fw-semibold px-4 py-2 mx-1 animate-btn",
                    cancelButton: "btn btn-light border fw-semibold px-4 py-2 mx-1",
                    popup: "rounded-4 border-0 shadow"
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = deleteUrl;
                }
            });
        });
    });
</script>';

if ($message) {
    $additionalScripts .= '
<script>
    Swal.fire({
        icon: ' . json_encode($swal_icon) . ',
        title: ' . json_encode(ucfirst($swal_icon)) . ',
        text: ' . json_encode($message) . ',
        confirmButtonText: "OK",
        customClass: {
            confirmButton: "btn btn-primary fw-semibold px-4 py-2 animate-btn",
            popup: "rounded-4 border-0 shadow"
        },
        buttonsStyling: false
    });
</script>';
}
?>
<?php include __DIR__ . '/components/footer.php'; ?>



