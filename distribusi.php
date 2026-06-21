<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] !== 'login' || !in_array($_SESSION['role'], ['admin', 'pengguna'])) {
    header("location:login.php");
    exit;
}
include 'koneksi.php';

function adjustBarangStock(mysqli $koneksi, string $idBarang, int $delta): bool
{
    $barang_result = mysqli_query($koneksi, "SELECT stok FROM barang WHERE id = '$idBarang' LIMIT 1");
    $barang = $barang_result ? mysqli_fetch_assoc($barang_result) : null;

    if (!$barang) {
        return false;
    }

    $stok_baru = (int) $barang['stok'] + $delta;
    if ($stok_baru < 0) {
        return false;
    }

    return (bool) mysqli_query($koneksi, "UPDATE barang SET stok = '$stok_baru' WHERE id = '$idBarang'");
}

$nama_user = htmlspecialchars($_SESSION['nama_lengkap']);
$username  = htmlspecialchars($_SESSION['username']);
$role      = ucfirst($_SESSION['role']);

$message = $_SESSION['flash_distribusi_message'] ?? '';
$message_type = $_SESSION['flash_distribusi_type'] ?? '';
unset($_SESSION['flash_distribusi_message'], $_SESSION['flash_distribusi_type']);

if (isset($_POST['tambah_distribusi'])) {
    if ($_SESSION['role'] !== 'admin') {
        $_SESSION['flash_distribusi_message'] = 'Akses ditolak: Anda tidak memiliki izin untuk menambah distribusi.';
        $_SESSION['flash_distribusi_type'] = 'error';
        header('Location: distribusi.php');
        exit;
    }
    $id_barang = mysqli_real_escape_string($koneksi, $_POST['id_barang'] ?? '');
    $jenis = mysqli_real_escape_string($koneksi, $_POST['jenis_distribusi'] ?? '');
    $jumlah = (int) ($_POST['jumlah'] ?? 0);
    $keterangan = mysqli_real_escape_string($koneksi, trim($_POST['keterangan'] ?? ''));
    $tanggal = mysqli_real_escape_string($koneksi, $_POST['tanggal'] ?? '');

    if ($id_barang === '' || $jenis === '' || $jumlah <= 0 || $keterangan === '' || $tanggal === '') {
        $_SESSION['flash_distribusi_message'] = 'Data distribusi belum lengkap.';
        $_SESSION['flash_distribusi_type'] = 'warning';
        header('Location: distribusi.php');
        exit;
    }

    mysqli_begin_transaction($koneksi);

    try {
        $delta = $jenis === 'Masuk' ? $jumlah : -$jumlah;
        if (!adjustBarangStock($koneksi, $id_barang, $delta)) {
            throw new Exception($jenis === 'Keluar'
                ? 'Jumlah distribusi keluar melebihi stok tersedia.'
                : 'Gagal memperbarui stok barang.');
        }

        $query = "INSERT INTO distribusi (id_barang, jenis_distribusi, jumlah, keterangan, tanggal_distribusi)
                  VALUES ('$id_barang', '$jenis', '$jumlah', '$keterangan', '$tanggal')";

        if (!mysqli_query($koneksi, $query)) {
            throw new Exception('Gagal menyimpan distribusi barang.');
        }

        mysqli_commit($koneksi);
        $_SESSION['flash_distribusi_message'] = 'Distribusi barang berhasil disimpan.';
        $_SESSION['flash_distribusi_type'] = 'success';
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        $_SESSION['flash_distribusi_message'] = $e->getMessage();
        $_SESSION['flash_distribusi_type'] = in_array($e->getMessage(), [
            'Jumlah distribusi keluar melebihi stok tersedia.'
        ], true) ? 'warning' : 'error';
    }

    header('Location: distribusi.php');
    exit;
}

if (isset($_POST['update_distribusi'])) {
    if ($_SESSION['role'] !== 'admin') {
        $_SESSION['flash_distribusi_message'] = 'Akses ditolak: Anda tidak memiliki izin untuk mengedit distribusi.';
        $_SESSION['flash_distribusi_type'] = 'error';
        header('Location: distribusi.php');
        exit;
    }
    $id_dist = mysqli_real_escape_string($koneksi, $_POST['id_distribusi'] ?? '');
    $id_barang_baru = mysqli_real_escape_string($koneksi, $_POST['id_barang'] ?? '');
    $jenis_baru = mysqli_real_escape_string($koneksi, $_POST['jenis_distribusi'] ?? '');
    $jumlah_baru = (int) ($_POST['jumlah'] ?? 0);
    $keterangan_baru = mysqli_real_escape_string($koneksi, trim($_POST['keterangan'] ?? ''));
    $tanggal_baru = mysqli_real_escape_string($koneksi, $_POST['tanggal'] ?? '');

    if ($id_dist === '' || $id_barang_baru === '' || $jenis_baru === '' || $jumlah_baru <= 0 || $keterangan_baru === '' || $tanggal_baru === '') {
        $_SESSION['flash_distribusi_message'] = 'Data edit distribusi belum lengkap.';
        $_SESSION['flash_distribusi_type'] = 'warning';
        header('Location: distribusi.php');
        exit;
    }

    $log_query = mysqli_query($koneksi, "SELECT * FROM distribusi WHERE id_distribusi = '$id_dist' LIMIT 1");
    $log_lama = $log_query ? mysqli_fetch_assoc($log_query) : null;

    if (!$log_lama) {
        $_SESSION['flash_distribusi_message'] = 'Data distribusi tidak ditemukan.';
        $_SESSION['flash_distribusi_type'] = 'error';
        header('Location: distribusi.php');
        exit;
    }

    mysqli_begin_transaction($koneksi);

    try {
        $id_barang_lama = mysqli_real_escape_string($koneksi, $log_lama['id_barang']);
        $jumlah_lama = (int) $log_lama['jumlah'];
        $jenis_lama = $log_lama['jenis_distribusi'];

        $rollback_delta = $jenis_lama === 'Masuk' ? -$jumlah_lama : $jumlah_lama;
        if (!adjustBarangStock($koneksi, $id_barang_lama, $rollback_delta)) {
            throw new Exception('Perubahan tidak bisa diproses karena stok saat ini tidak cukup untuk membalik log lama.');
        }

        $update_log_query = mysqli_query(
            $koneksi,
            "UPDATE distribusi
             SET id_barang = '$id_barang_baru',
                 jenis_distribusi = '$jenis_baru',
                 jumlah = '$jumlah_baru',
                 keterangan = '$keterangan_baru',
                 tanggal_distribusi = '$tanggal_baru'
             WHERE id_distribusi = '$id_dist'"
        );

        if (!$update_log_query) {
            throw new Exception('Gagal memperbarui data distribusi.');
        }

        $apply_delta = $jenis_baru === 'Masuk' ? $jumlah_baru : -$jumlah_baru;
        if (!adjustBarangStock($koneksi, $id_barang_baru, $apply_delta)) {
            throw new Exception('Jumlah distribusi keluar melebihi stok tersedia setelah penyesuaian.');
        }

        mysqli_commit($koneksi);
        $_SESSION['flash_distribusi_message'] = 'Distribusi barang berhasil diperbarui.';
        $_SESSION['flash_distribusi_type'] = 'success';
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        $_SESSION['flash_distribusi_message'] = $e->getMessage();
        $_SESSION['flash_distribusi_type'] = 'error';
    }

    header('Location: distribusi.php');
    exit;
}

if (isset($_GET['hapus'])) {
    if ($_SESSION['role'] !== 'admin') {
        $_SESSION['flash_distribusi_message'] = 'Akses ditolak: Anda tidak memiliki izin untuk menghapus riwayat.';
        $_SESSION['flash_distribusi_type'] = 'error';
        header('Location: distribusi.php');
        exit;
    }
    $id_dist = mysqli_real_escape_string($koneksi, $_GET['hapus'] ?? '');

    if ($id_dist === '') {
        $_SESSION['flash_distribusi_message'] = 'ID distribusi tidak valid.';
        $_SESSION['flash_distribusi_type'] = 'error';
        header('Location: distribusi.php');
        exit;
    }

    $log_query = mysqli_query($koneksi, "SELECT * FROM distribusi WHERE id_distribusi = '$id_dist' LIMIT 1");
    $log_data = $log_query ? mysqli_fetch_assoc($log_query) : null;

    if (!$log_data) {
        $_SESSION['flash_distribusi_message'] = 'Data distribusi tidak ditemukan.';
        $_SESSION['flash_distribusi_type'] = 'error';
        header('Location: distribusi.php');
        exit;
    }

    mysqli_begin_transaction($koneksi);

    try {
        $id_barang = mysqli_real_escape_string($koneksi, $log_data['id_barang']);
        $jumlah = (int) $log_data['jumlah'];
        $rollback_delta = $log_data['jenis_distribusi'] === 'Masuk' ? -$jumlah : $jumlah;

        if (!adjustBarangStock($koneksi, $id_barang, $rollback_delta)) {
            throw new Exception('Riwayat ini tidak bisa dihapus karena stok saat ini tidak cukup untuk membalik transaksi.');
        }

        $delete_query = mysqli_query($koneksi, "DELETE FROM distribusi WHERE id_distribusi = '$id_dist'");
        if (!$delete_query) {
            throw new Exception('Gagal menghapus riwayat distribusi.');
        }

        mysqli_commit($koneksi);
        $_SESSION['flash_distribusi_message'] = 'Riwayat distribusi berhasil dihapus.';
        $_SESSION['flash_distribusi_type'] = 'success';
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        $_SESSION['flash_distribusi_message'] = $e->getMessage();
        $_SESSION['flash_distribusi_type'] = 'error';
    }

    header('Location: distribusi.php');
    exit;
}

$list_barang_result = mysqli_query($koneksi, "SELECT id, nama_barang, stok FROM barang ORDER BY nama_barang ASC");
$list_barang = [];
if ($list_barang_result) {
    while ($barang = mysqli_fetch_assoc($list_barang_result)) {
        $list_barang[] = $barang;
    }
}

$query_riwayat = "SELECT d.*, b.nama_barang
                  FROM distribusi d
                  JOIN barang b ON d.id_barang = b.id
                  ORDER BY d.tanggal_distribusi DESC, d.id_distribusi DESC";
$riwayat = mysqli_query($koneksi, $query_riwayat);

$pageTitle = 'Distribusi | Admin';
$activePage = 'distribusi';
include __DIR__ . '/components/head.php';
include __DIR__ . '/components/topbar.php';
include __DIR__ . '/components/sidebar.php';
?>
<main class="app-main">
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
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0 fw-bold">Manajemen Distribusi</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Distribusi</li>
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
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
                            <h5 class="mb-0 fw-bold"><i class="bi bi-table"></i> Riwayat Keluar Masuk Barang</h5>
                            <?php if ($_SESSION['role'] === 'admin'): ?>
                            <button type="button" class="btn btn-light btn-sm ms-auto px-3 py-2 fw-semibold shadow-sm animate-btn" data-bs-toggle="modal" data-bs-target="#modalTambahDistribusi" style="border-radius: 8px; border: none;">
                                <i class="bi bi-plus-lg me-1"></i> Tambah Distribusi
                            </button>
                            <?php endif; ?>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light text-secondary fw-semibold" style="font-size: 0.85rem; border-bottom: 1px solid #f1f5f9;">
                                        <tr>
                                            <th width="80" class="ps-4 text-center">No</th>
                                            <th>Waktu & Tanggal</th>
                                            <th>Nama Barang</th>
                                            <th>Jenis</th>
                                            <th>Jumlah</th>
                                            <th>Keterangan</th>
                                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                            <th width="200" class="pe-4 text-end">Aksi</th>
                                            <?php endif; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        while ($r = mysqli_fetch_assoc($riwayat)) {
                                        ?>
                                            <tr>
                                                <td class="ps-4 text-center text-muted fw-semibold"><?= $no++; ?></td>
                                                <td><?= date('d/m/Y H:i', strtotime($r['tanggal_distribusi'])); ?></td>
                                                <td><strong><?= htmlspecialchars($r['nama_barang']); ?></strong></td>
                                                <td>
                                                    <?php if ($r['jenis_distribusi'] === 'Masuk') { ?>
                                                        <span class="badge bg-success"><i class="bi bi-box-arrow-in-down"></i> Masuk</span>
                                                    <?php } else { ?>
                                                        <span class="badge bg-danger"><i class="bi bi-box-arrow-up"></i> Keluar</span>
                                                    <?php } ?>
                                                </td>
                                                <td><strong><?= $r['jumlah']; ?></strong></td>
                                                <td><?= htmlspecialchars($r['keterangan']); ?></td>
                                                <?php if ($_SESSION['role'] === 'admin'): ?>
                                                <td class="pe-4 text-end">
                                                    <div class="d-inline-flex gap-2">
                                                        <button
                                                            type="button"
                                                            class="btn btn-edit-user-custom btn-edit-distribusi btn-sm"
                                                            data-id="<?= $r['id_distribusi']; ?>"
                                                            data-id-barang="<?= $r['id_barang']; ?>"
                                                            data-jenis="<?= htmlspecialchars($r['jenis_distribusi']); ?>"
                                                            data-jumlah="<?= (int) $r['jumlah']; ?>"
                                                            data-keterangan="<?= htmlspecialchars($r['keterangan']); ?>"
                                                            data-tanggal="<?= date('Y-m-d\TH:i', strtotime($r['tanggal_distribusi'])); ?>">
                                                            <i class="bi bi-pencil me-1"></i> Edit
                                                        </button>
                                                        <a
                                                            href="distribusi.php?hapus=<?= $r['id_distribusi']; ?>"
                                                            class="btn btn-hapus-user-custom btn-hapus-distribusi btn-sm"
                                                            data-nama="<?= htmlspecialchars($r['nama_barang']); ?>"
                                                            data-jenis="<?= htmlspecialchars($r['jenis_distribusi']); ?>"
                                                            data-jumlah="<?= (int) $r['jumlah']; ?>">
                                                            <i class="bi bi-trash me-1"></i> Hapus
                                                        </a>
                                                    </div>
                                                </td>
                                                <?php endif; ?>
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
    </div>
</main>

<?php if ($_SESSION['role'] === 'admin'): ?>
<div class="modal fade" id="modalTambahDistribusi" tabindex="-1" aria-labelledby="modalTambahDistribusiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 12px; overflow: hidden;">
            <form method="POST" action="">
                <div class="modal-header bg-white border-bottom-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-dark" id="modalTambahDistribusiLabel">
                        <i class="bi bi-box-seam text-primary me-2"></i> Input Alur Logistik
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 pb-4 pt-2">
                    <div class="mb-3">
                        <label for="id_barang" class="form-label fw-semibold text-secondary" style="font-size: 0.9rem;">Pilih Barang</label>
                        <select id="id_barang" name="id_barang" class="form-select" required style="border-radius: 8px;">
                            <option value="">-- Pilih Barang --</option>
                            <?php foreach ($list_barang as $b) { ?>
                                <option value="<?= $b['id']; ?>"><?= htmlspecialchars($b['nama_barang']); ?> (Stok: <?= $b['stok']; ?>)</option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="jenis_distribusi" class="form-label fw-semibold text-secondary" style="font-size: 0.9rem;">Jenis Distribusi</label>
                        <select id="jenis_distribusi" name="jenis_distribusi" class="form-select" required style="border-radius: 8px;">
                            <option value="Masuk">Barang Masuk (+)</option>
                            <option value="Keluar">Barang Keluar (-)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="jumlah" class="form-label fw-semibold text-secondary" style="font-size: 0.9rem;">Jumlah</label>
                        <input type="number" id="jumlah" name="jumlah" class="form-control" min="1" required style="border-radius: 8px;">
                    </div>
                    <div class="mb-3">
                        <label for="keterangan" class="form-label fw-semibold text-secondary" style="font-size: 0.9rem;">Keterangan / Tujuan / Asal</label>
                        <input type="text" id="keterangan" name="keterangan" class="form-control" placeholder="Contoh: Kirim ke Cabang B" required style="border-radius: 8px;">
                    </div>
                    <div class="mb-0">
                        <label for="tanggal" class="form-label fw-semibold text-secondary" style="font-size: 0.9rem;">Tanggal Transaksi</label>
                        <input type="datetime-local" id="tanggal" name="tanggal" class="form-control" value="<?= date('Y-m-d\TH:i'); ?>" required style="border-radius: 8px;">
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-light fw-semibold" data-bs-dismiss="modal" style="border-radius: 8px; padding: 0.6rem 1.2rem;">Batal</button>
                    <button type="submit" name="tambah_distribusi" class="btn btn-primary fw-semibold shadow-sm animate-btn" style="border-radius: 8px; padding: 0.6rem 1.2rem; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); border: none;">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditDistribusi" tabindex="-1" aria-labelledby="modalEditDistribusiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 12px; overflow: hidden;">
            <form method="POST" action="">
                <input type="hidden" id="edit_id_distribusi" name="id_distribusi">
                <div class="modal-header bg-white border-bottom-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-dark" id="modalEditDistribusiLabel">
                        <i class="bi bi-pencil-square text-primary me-2"></i> Edit Distribusi
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 pb-4 pt-2">
                    <div class="mb-3">
                        <label for="edit_id_barang" class="form-label fw-semibold text-secondary" style="font-size: 0.9rem;">Pilih Barang</label>
                        <select id="edit_id_barang" name="id_barang" class="form-select" required style="border-radius: 8px;">
                            <option value="">-- Pilih Barang --</option>
                            <?php foreach ($list_barang as $b) { ?>
                                <option value="<?= $b['id']; ?>"><?= htmlspecialchars($b['nama_barang']); ?> (Stok: <?= $b['stok']; ?>)</option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_jenis_distribusi" class="form-label fw-semibold text-secondary" style="font-size: 0.9rem;">Jenis Distribusi</label>
                        <select id="edit_jenis_distribusi" name="jenis_distribusi" class="form-select" required style="border-radius: 8px;">
                            <option value="Masuk">Barang Masuk (+)</option>
                            <option value="Keluar">Barang Keluar (-)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_jumlah" class="form-label fw-semibold text-secondary" style="font-size: 0.9rem;">Jumlah</label>
                        <input type="number" id="edit_jumlah" name="jumlah" class="form-control" min="1" required style="border-radius: 8px;">
                    </div>
                    <div class="mb-3">
                        <label for="edit_keterangan" class="form-label fw-semibold text-secondary" style="font-size: 0.9rem;">Keterangan / Tujuan / Asal</label>
                        <input type="text" id="edit_keterangan" name="keterangan" class="form-control" required style="border-radius: 8px;">
                    </div>
                    <div class="mb-0">
                        <label for="edit_tanggal" class="form-label fw-semibold text-secondary" style="font-size: 0.9rem;">Tanggal Transaksi</label>
                        <input type="datetime-local" id="edit_tanggal" name="tanggal" class="form-control" required style="border-radius: 8px;">
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-light fw-semibold" data-bs-dismiss="modal" style="border-radius: 8px; padding: 0.6rem 1.2rem;">Batal</button>
                    <button type="submit" name="update_distribusi" class="btn btn-primary fw-semibold shadow-sm animate-btn" style="border-radius: 8px; padding: 0.6rem 1.2rem; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); border: none;">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
<?php
$additionalScripts = '';
$swal_icon = in_array($message_type, ['success', 'warning', 'error', 'info'], true) ? $message_type : 'info';

if ($_SESSION['role'] === 'admin') {
    $additionalScripts = '
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const editDistribusiModalElement = document.getElementById("modalEditDistribusi");
        const editDistribusiModal = new bootstrap.Modal(editDistribusiModalElement);

        document.querySelectorAll(".btn-edit-distribusi").forEach(function (button) {
            button.addEventListener("click", function () {
                document.getElementById("edit_id_distribusi").value = this.dataset.id || "";
                document.getElementById("edit_id_barang").value = this.dataset.idBarang || "";
                document.getElementById("edit_jenis_distribusi").value = this.dataset.jenis || "Masuk";
                document.getElementById("edit_jumlah").value = this.dataset.jumlah || "";
                document.getElementById("edit_keterangan").value = this.dataset.keterangan || "";
                document.getElementById("edit_tanggal").value = this.dataset.tanggal || "";
                editDistribusiModal.show();
            });
        });

        document.querySelectorAll(".btn-hapus-distribusi").forEach(function (button) {
            button.addEventListener("click", function (event) {
                event.preventDefault();
                const deleteUrl = this.getAttribute("href");
                const nama = this.dataset.nama || "barang ini";
                const jenis = this.dataset.jenis || "distribusi";
                const jumlah = this.dataset.jumlah || "0";

                Swal.fire({
                    icon: "warning",
                    title: "Hapus riwayat distribusi?",
                    text: "Log " + jenis + " untuk " + nama + " sebanyak " + jumlah + " akan dihapus.",
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
} else {
    if ($message) {
        $additionalScripts = '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
    }
}

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

