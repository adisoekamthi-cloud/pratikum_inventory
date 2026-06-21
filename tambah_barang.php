<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] !== 'login' || !in_array($_SESSION['role'], ['admin', 'pengguna'])) {
    header('Location: login.php');
    exit;
}
include 'koneksi.php';

$nama_user = htmlspecialchars($_SESSION['nama_lengkap']);
$username  = htmlspecialchars($_SESSION['username']);
$role      = ucfirst($_SESSION['role']);

$message = $_SESSION['flash_tambah_barang_message'] ?? '';
$message_type = $_SESSION['flash_tambah_barang_type'] ?? '';
unset($_SESSION['flash_tambah_barang_message'], $_SESSION['flash_tambah_barang_type']);

$query_status      = mysqli_query($koneksi, "SELECT * FROM status_barang");
$query_penyimpanan = mysqli_query($koneksi, "SELECT * FROM penyimpanan");
$query_vendor      = mysqli_query($koneksi, "SELECT * FROM vendor");

if (isset($_POST['submit'])) {
    $nama        = mysqli_real_escape_string($koneksi, trim($_POST['nama'] ?? ''));
    $status_id   = mysqli_real_escape_string($koneksi, $_POST['status_id'] ?? '');
    $penyimp_id  = mysqli_real_escape_string($koneksi, $_POST['penyimpanan_id'] ?? '');
    $harga       = (int) ($_POST['harga'] ?? 0);
    $stok_awal   = (int) ($_POST['stok'] ?? 0);
    $limit_stok  = (int) ($_POST['limit_stok'] ?? 0);
    $vendor_id   = (int) ($_POST['vendor_id'] ?? 0);

    if ($nama === '' || $status_id === '' || $penyimp_id === '' || $harga < 0 || $stok_awal < 0 || $limit_stok < 1) {
        $_SESSION['flash_tambah_barang_message'] = 'Data barang belum lengkap atau tidak valid.';
        $_SESSION['flash_tambah_barang_type'] = 'warning';
    } else {
        $query_insert = "INSERT INTO barang 
                         (nama_barang, status_id, penyimpanan_id, harga_barang, stok, limit_stok, vendor_id) 
                         VALUES 
                         ('$nama', '$status_id', '$penyimp_id', '$harga', '$stok_awal', '$limit_stok', '$vendor_id')";

        if (mysqli_query($koneksi, $query_insert)) {
            $_SESSION['flash_tambah_barang_message'] = 'Master barang baru berhasil ditambahkan.';
            $_SESSION['flash_tambah_barang_type'] = 'success';
        } else {
            $_SESSION['flash_tambah_barang_message'] = 'Gagal menambah data barang.';
            $_SESSION['flash_tambah_barang_type'] = 'error';
        }
    }

    header('Location: tambah_barang.php');
    exit;
}

$pageTitle = 'Tambah Barang | ' . $role;
$activePage = 'tambah';
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
                    <h3 class="mb-0 fw-bold">Tambah Barang</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tambah Barang</li>
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
                            <h5 class="mb-0 fw-bold"><i class="bi bi-box-seam me-2"></i> Form Tambah Master Barang Baru</h5>
                        </div>
                        <div class="card-body p-4">
                            <form method="POST" action="">
                                <div class="mb-4">
                                    <label class="form-label fw-semibold text-secondary" style="font-size: 0.9rem;">Nama Barang</label>
                                    <input type="text" name="nama" class="form-control" placeholder="Contoh: Keyboard Logi" required style="border-radius: 8px;">
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label fw-semibold text-secondary" style="font-size: 0.9rem;">Status Barang</label>
                                        <select name="status_id" class="form-select" required style="border-radius: 8px;">
                                            <option value="">-- Pilih Status --</option>
                                            <?php while ($st = mysqli_fetch_assoc($query_status)) { ?>
                                                <option value="<?= $st['id']; ?>"><?= htmlspecialchars($st['nama_status']); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label fw-semibold text-secondary" style="font-size: 0.9rem;">Lokasi Penyimpanan</label>
                                        <select name="penyimpanan_id" class="form-select" required style="border-radius: 8px;">
                                            <option value="">-- Pilih Gedung/Rak --</option>
                                            <?php while ($pny = mysqli_fetch_assoc($query_penyimpanan)) { ?>
                                                <option value="<?= $pny['id']; ?>"><?= htmlspecialchars($pny['nama_penyimpanan']); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label fw-semibold text-secondary" style="font-size: 0.9rem;">Harga Barang (Rp)</label>
                                        <input type="number" name="harga" class="form-control" placeholder="0" min="0" required style="border-radius: 8px;">
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label fw-semibold text-secondary" style="font-size: 0.9rem;">Vendor / Supplier</label>
                                        <select name="vendor_id" class="form-select" required style="border-radius: 8px;">
                                            <option value="0">Belum Set (Default)</option>
                                            <?php while ($vd = mysqli_fetch_assoc($query_vendor)) { ?>
                                                <option value="<?= $vd['id_vendor']; ?>"><?= htmlspecialchars($vd['nama_vendor']); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label fw-semibold text-secondary" style="font-size: 0.9rem;">Stok Awal</label>
                                        <input type="number" name="stok" class="form-control" value="0" min="0" required style="border-radius: 8px;">
                                        <div class="form-text text-muted" style="font-size: 0.75rem;">Kuantitas awal saat barang didaftarkan.</div>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label fw-semibold text-secondary" style="font-size: 0.9rem;">Limit Stok Minimum</label>
                                        <input type="number" name="limit_stok" class="form-control" value="5" min="1" required style="border-radius: 8px;">
                                        <div class="form-text text-muted" style="font-size: 0.75rem;">Batas peringatan kritis warna merah di dashboard.</div>
                                    </div>
                                </div>
                                <hr class="mt-4 mb-4 text-muted opacity-25">
                                <div class="d-flex justify-content-between">
                                    <a href="index.php" class="btn btn-light border fw-semibold" style="border-radius: 8px; padding: 0.6rem 1.2rem;">
                                        <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
                                    </a>
                                    <button type="submit" name="submit" class="btn btn-primary fw-semibold shadow-sm animate-btn" style="border-radius: 8px; padding: 0.6rem 1.2rem; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); border: none;">
                                        <i class="bi bi-check-circle"></i> Simpan Data Master
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

