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

$message = $_SESSION['flash_stok_message'] ?? '';
$message_type = $_SESSION['flash_stok_type'] ?? '';
unset($_SESSION['flash_stok_message'], $_SESSION['flash_stok_type']);

// PROSES UPDATE STOK & LIMIT
if (isset($_POST['update_stok'])) {
    if ($_SESSION['role'] !== 'admin') {
        $_SESSION['flash_stok_message'] = 'Akses ditolak: Anda tidak memiliki izin untuk mengubah stok.';
        $_SESSION['flash_stok_type'] = 'error';
        header("Location: stok.php");
        exit;
    }
    $id = mysqli_real_escape_string($koneksi, $_POST['id'] ?? '');
    $stok_baru = (int) ($_POST['stok'] ?? 0);
    $limit_baru = (int) ($_POST['limit_stok'] ?? 0);
    $redirect_page = max(1, (int) ($_POST['current_page'] ?? 1));

    if ($id === '') {
        $_SESSION['flash_stok_message'] = 'Data barang tidak valid.';
        $_SESSION['flash_stok_type'] = 'error';
    } else {
        $query = "UPDATE barang SET stok='$stok_baru', limit_stok='$limit_baru' WHERE id='$id'";
        if (mysqli_query($koneksi, $query)) {
            $_SESSION['flash_stok_message'] = 'Stok barang berhasil diperbarui.';
            $_SESSION['flash_stok_type'] = 'success';
        } else {
            $_SESSION['flash_stok_message'] = 'Gagal memperbarui stok barang.';
            $_SESSION['flash_stok_type'] = 'error';
        }
    }

    header("Location: stok.php?page=" . $redirect_page);
    exit;
}

// PAGINATION VARIABLES
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}
$perPage = 10;
$offset  = ($page - 1) * $perPage;

// HITUNG TOTAL BARANG
$total_query = "SELECT COUNT(*) AS total FROM barang";
$total_result = mysqli_query($koneksi, $total_query);
$total_rows = 0;
if ($total_result) {
    $row = mysqli_fetch_assoc($total_result);
    $total_rows = (int) $row['total'];
}
$total_pages = max(1, ceil($total_rows / $perPage));

// AMBIL DATA BARANG + SINKRONISASI INNER JOIN
$query_barang = "SELECT b.id, b.nama_barang, b.stok, b.limit_stok, p.nama_penyimpanan 
                 FROM barang b 
                 LEFT JOIN penyimpanan p ON b.penyimpanan_id = p.id
                 ORDER BY b.nama_barang ASC
                 LIMIT $perPage OFFSET $offset";
$data_stok = mysqli_query($koneksi, $query_barang);

$pageTitle = 'Stok | Admin';
$activePage = 'stok';
include __DIR__ . '/components/head.php';
include __DIR__ . '/components/topbar.php';
include __DIR__ . '/components/sidebar.php';
?>
<main class="app-main">
    <style>
        .animate-btn {
            transition: all 0.2s ease;
        }
        .animate-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(33, 37, 41, 0.2) !important;
        }
    </style>
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0 fw-bold">Manajemen Stok</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Stok</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
                        <div class="card-header bg-primary text-white py-3">
                            <h5 class="mb-0 fw-bold"><i class="bi bi-graph-up"></i> Kontrol Stok & Batas Minimum Barang</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light text-secondary fw-semibold" style="font-size: 0.85rem; border-bottom: 1px solid #f1f5f9;">
                                        <tr>
                                            <th class="ps-4">Nama Barang</th>
                                            <th>Lokasi Penyimpanan</th>
                                            <th>Stok Saat Ini</th>
                                            <th>Batas Minimum (Limit)</th>
                                            <th>Status Stok</th>
                                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                            <th width="320" class="pe-4 text-end">Aksi Cepat</th>
                                            <?php endif; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = mysqli_fetch_assoc($data_stok)) { 
                                            $kritis = ($row['stok'] <= $row['limit_stok']);
                                        ?>
                                        <tr>
                                            <td class="ps-4"><strong><?= htmlspecialchars($row['nama_barang']); ?></strong></td>
                                            <td><span class="badge bg-secondary"><?= htmlspecialchars($row['nama_penyimpanan'] ?? 'Belum Diatur'); ?></span></td>
                                            <td><strong><?= $row['stok']; ?></strong></td>
                                            <td><?= $row['limit_stok']; ?></td>
                                            <td>
                                                <?php if ($kritis) { ?>
                                                    <span class="badge bg-danger"><i class="bi bi-exclamation-circle"></i> Kritis</span>
                                                <?php } else { ?>
                                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Aman</span>
                                                <?php } ?>
                                            </td>
                                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                            <td class="pe-4 text-end">
                                                <form method="POST" action="" class="d-inline-flex align-items-end gap-2 flex-wrap justify-content-end">
                                                    <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                                    <input type="hidden" name="current_page" value="<?= $page; ?>">
                                                    <div class="text-start">
                                                        <label class="form-label form-label-sm mb-1 d-block small text-muted" style="font-size: 0.72rem; font-weight: 500;">Stok</label>
                                                        <input type="number" name="stok" class="form-control form-control-sm" value="<?= $row['stok']; ?>" title="Ubah Jumlah Stok" style="width: 80px; border-radius: 6px;">
                                                    </div>
                                                    <div class="text-start">
                                                        <label class="form-label form-label-sm mb-1 d-block small text-muted" style="font-size: 0.72rem; font-weight: 500;">Limit Stok</label>
                                                        <input type="number" name="limit_stok" class="form-control form-control-sm" value="<?= $row['limit_stok']; ?>" title="Ubah Limit" style="width: 90px; border-radius: 6px;">
                                                    </div>
                                                    <button type="submit" name="update_stok" class="btn btn-sm btn-dark fw-semibold px-3 animate-btn" style="border-radius: 6px; padding: 0.35rem 0.75rem;">Simpan</button>
                                                </form>
                                            </td>
                                            <?php endif; ?>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <nav aria-label="Pagination">
                        <ul class="pagination justify-content-center mt-3">
                            <?php
                            for ($i = 1; $i <= $total_pages; $i++) {
                                $page_url = 'stok.php?page=' . $i;
                            ?>
                                <li class="page-item <?= $i === $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="<?= $page_url; ?>"><?= $i; ?></a>
                                </li>
                            <?php } ?>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</main>
<?php
$additionalScripts = '';
if ($message) {
    $swal_icon = in_array($message_type, ['success', 'warning', 'error', 'info'], true) ? $message_type : 'info';
    $additionalScripts = '
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

