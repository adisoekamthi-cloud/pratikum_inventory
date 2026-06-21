<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] !== 'login') {
    header("location:login.php");
    exit;
}
if ($_SESSION['role'] !== 'admin') {
    header("location:index.php");
    exit;
}
include 'koneksi.php';

$id = mysqli_real_escape_string($koneksi, $_GET['id'] ?? '');

if ($id === '') {
    $_SESSION['flash_index_message'] = 'ID barang tidak valid.';
    $_SESSION['flash_index_type'] = 'error';
    header('Location: index.php');
    exit;
}

$data = mysqli_query($koneksi, "SELECT * FROM barang WHERE id = '$id' LIMIT 1");
$row = $data ? mysqli_fetch_assoc($data) : null;

if (!$row) {
    $_SESSION['flash_index_message'] = 'Data barang tidak ditemukan.';
    $_SESSION['flash_index_type'] = 'error';
    header('Location: index.php');
    exit;
}

$query_status      = mysqli_query($koneksi, "SELECT * FROM status_barang");
$query_penyimpanan = mysqli_query($koneksi, "SELECT * FROM penyimpanan");
$query_vendor      = mysqli_query($koneksi, "SELECT * FROM vendor");

if (isset($_POST['update'])) {
    $nama           = mysqli_real_escape_string($koneksi, trim($_POST['nama'] ?? ''));
    $status_id      = mysqli_real_escape_string($koneksi, $_POST['status_id'] ?? '');
    $penyimpanan_id = mysqli_real_escape_string($koneksi, $_POST['penyimpanan_id'] ?? '');
    $harga          = (int) ($_POST['harga'] ?? 0);
    $vendor_id      = (int) ($_POST['vendor_id'] ?? 0);

    if ($nama === '' || $status_id === '' || $penyimpanan_id === '' || $harga < 0) {
        $_SESSION['flash_index_message'] = 'Data edit barang tidak lengkap atau tidak valid.';
        $_SESSION['flash_index_type'] = 'warning';
    } else {
        $query_update = "UPDATE barang SET 
                         nama_barang = '$nama', 
                         status_id = '$status_id', 
                         penyimpanan_id = '$penyimpanan_id', 
                         harga_barang = '$harga',
                         vendor_id = '$vendor_id'
                         WHERE id = '$id'";

        if (mysqli_query($koneksi, $query_update)) {
            $_SESSION['flash_index_message'] = 'Data barang berhasil diperbarui.';
            $_SESSION['flash_index_type'] = 'success';
        } else {
            $_SESSION['flash_index_message'] = 'Gagal memperbarui data barang.';
            $_SESSION['flash_index_type'] = 'error';
        }
    }

    header('Location: index.php');
    exit;
}

$nama_user = htmlspecialchars($_SESSION['nama_lengkap']);
$username  = htmlspecialchars($_SESSION['username']);
$role      = ucfirst($_SESSION['role']);

$pageTitle = 'Edit Barang | Admin';
$activePage = 'dashboard';
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
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.35) !important;
        }
    </style>
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0 fw-bold">Edit Barang</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit Barang</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
                        <div class="card-header bg-primary text-white py-3">
                            <h5 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i> Form Edit Master Barang</h5>
                        </div>
                        <div class="card-body p-4">
                            <form method="POST" action="">
                                <div class="mb-4">
                                    <label class="form-label fw-semibold text-secondary" style="font-size: 0.9rem;">Nama Barang</label>
                                    <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($row['nama_barang']); ?>" required style="border-radius: 8px;">
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label fw-semibold text-secondary" style="font-size: 0.9rem;">Status Barang</label>
                                        <select name="status_id" class="form-select" required style="border-radius: 8px;">
                                            <option value="">-- Pilih Status --</option>
                                            <?php while ($st = mysqli_fetch_assoc($query_status)) { ?>
                                                <option value="<?= $st['id']; ?>" <?= $row['status_id'] == $st['id'] ? 'selected' : ''; ?>>
                                                    <?= htmlspecialchars($st['nama_status']); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label fw-semibold text-secondary" style="font-size: 0.9rem;">Lokasi Penyimpanan</label>
                                        <select name="penyimpanan_id" class="form-select" required style="border-radius: 8px;">
                                            <option value="">-- Pilih Gedung/Rak --</option>
                                            <?php while ($pny = mysqli_fetch_assoc($query_penyimpanan)) { ?>
                                                <option value="<?= $pny['id']; ?>" <?= $row['penyimpanan_id'] == $pny['id'] ? 'selected' : ''; ?>>
                                                    <?= htmlspecialchars($pny['nama_penyimpanan']); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label fw-semibold text-secondary" style="font-size: 0.9rem;">Harga Barang (Rp)</label>
                                        <input type="number" name="harga" class="form-control" value="<?= $row['harga_barang']; ?>" min="0" required style="border-radius: 8px;">
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label fw-semibold text-secondary" style="font-size: 0.9rem;">Vendor / Supplier</label>
                                        <select name="vendor_id" class="form-select" required style="border-radius: 8px;">
                                            <option value="0">Belum Set (Default)</option>
                                            <?php while ($vd = mysqli_fetch_assoc($query_vendor)) { ?>
                                                <option value="<?= $vd['id_vendor']; ?>" <?= $row['vendor_id'] == $vd['id_vendor'] ? 'selected' : ''; ?>>
                                                    <?= htmlspecialchars($vd['nama_vendor']); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <hr class="mt-4 mb-4 text-muted opacity-25">
                                <div class="d-flex justify-content-between">
                                    <a href="index.php" class="btn btn-light border fw-semibold" style="border-radius: 8px; padding: 0.6rem 1.2rem;">
                                        <i class="bi bi-arrow-left"></i> Batal
                                    </a>
                                    <button type="submit" name="update" class="btn btn-primary fw-semibold shadow-sm animate-btn" style="border-radius: 8px; padding: 0.6rem 1.2rem; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); border: none;">
                                        <i class="bi bi-check-circle"></i> Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php include __DIR__ . '/components/footer.php'; ?>
