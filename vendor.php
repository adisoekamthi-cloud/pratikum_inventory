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

$nama_user = htmlspecialchars($_SESSION['nama_lengkap']);
$username  = htmlspecialchars($_SESSION['username']);
$role      = ucfirst($_SESSION['role']);

$message = $_SESSION['flash_vendor_message'] ?? '';
$message_type = $_SESSION['flash_vendor_type'] ?? '';
unset($_SESSION['flash_vendor_message'], $_SESSION['flash_vendor_type']);

if (isset($_POST['tambah_vendor'])) {
    $nama_vendor = mysqli_real_escape_string($koneksi, trim($_POST['nama_vendor'] ?? ''));
    $kontak_vendor = mysqli_real_escape_string($koneksi, trim($_POST['kontak_vendor'] ?? ''));
    $alamat_vendor = mysqli_real_escape_string($koneksi, trim($_POST['alamat_vendor'] ?? ''));

    if ($nama_vendor === '' || $kontak_vendor === '' || $alamat_vendor === '') {
        $_SESSION['flash_vendor_message'] = 'Data vendor belum lengkap.';
        $_SESSION['flash_vendor_type'] = 'warning';
    } else {
        $query = mysqli_query(
            $koneksi,
            "INSERT INTO vendor (nama_vendor, kontak_vendor, alamat_vendor)
             VALUES ('$nama_vendor', '$kontak_vendor', '$alamat_vendor')"
        );

        if ($query) {
            $_SESSION['flash_vendor_message'] = 'Vendor berhasil ditambahkan.';
            $_SESSION['flash_vendor_type'] = 'success';
        } else {
            $_SESSION['flash_vendor_message'] = 'Gagal menambahkan vendor.';
            $_SESSION['flash_vendor_type'] = 'error';
        }
    }

    header('Location: vendor.php');
    exit;
}

if (isset($_POST['update_vendor'])) {
    $id_vendor = mysqli_real_escape_string($koneksi, $_POST['id_vendor'] ?? '');
    $nama_vendor = mysqli_real_escape_string($koneksi, trim($_POST['nama_vendor'] ?? ''));
    $kontak_vendor = mysqli_real_escape_string($koneksi, trim($_POST['kontak_vendor'] ?? ''));
    $alamat_vendor = mysqli_real_escape_string($koneksi, trim($_POST['alamat_vendor'] ?? ''));

    if ($id_vendor === '' || $nama_vendor === '' || $kontak_vendor === '' || $alamat_vendor === '') {
        $_SESSION['flash_vendor_message'] = 'Data vendor belum lengkap.';
        $_SESSION['flash_vendor_type'] = 'warning';
    } else {
        $query = mysqli_query(
            $koneksi,
            "UPDATE vendor
             SET nama_vendor = '$nama_vendor',
                 kontak_vendor = '$kontak_vendor',
                 alamat_vendor = '$alamat_vendor'
             WHERE id_vendor = '$id_vendor'"
        );

        if ($query) {
            $_SESSION['flash_vendor_message'] = 'Vendor berhasil diperbarui.';
            $_SESSION['flash_vendor_type'] = 'success';
        } else {
            $_SESSION['flash_vendor_message'] = 'Gagal memperbarui vendor.';
            $_SESSION['flash_vendor_type'] = 'error';
        }
    }

    header('Location: vendor.php');
    exit;
}

if (isset($_GET['hapus'])) {
    $id_hapus = mysqli_real_escape_string($koneksi, $_GET['hapus'] ?? '');

    if ($id_hapus === '') {
        $_SESSION['flash_vendor_message'] = 'ID vendor tidak valid.';
        $_SESSION['flash_vendor_type'] = 'error';
    } else {
        $query = mysqli_query($koneksi, "DELETE FROM vendor WHERE id_vendor = '$id_hapus'");

        if ($query) {
            $_SESSION['flash_vendor_message'] = 'Vendor berhasil dihapus.';
            $_SESSION['flash_vendor_type'] = 'success';
        } else {
            $_SESSION['flash_vendor_message'] = 'Gagal menghapus vendor.';
            $_SESSION['flash_vendor_type'] = 'error';
        }
    }

    header('Location: vendor.php');
    exit;
}

$tampil = mysqli_query($koneksi, "SELECT * FROM vendor ORDER BY id_vendor DESC");

$pageTitle = 'Vendor | Admin';
$activePage = 'vendor';
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
                    <h3 class="mb-0 fw-bold">Manajemen Vendor</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Vendor</li>
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
                            <h5 class="mb-0 fw-bold"><i class="bi bi-table"></i> Daftar Vendor & Supplier</h5>
                            <button type="button" class="btn btn-light btn-sm ms-auto px-3 py-2 fw-semibold shadow-sm animate-btn" data-bs-toggle="modal" data-bs-target="#modalTambahVendor" style="border-radius: 8px; border: none;">
                                <i class="bi bi-plus-lg me-1"></i> Tambah Vendor
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light text-secondary fw-semibold" style="font-size: 0.85rem; border-bottom: 1px solid #f1f5f9;">
                                        <tr>
                                            <th width="80" class="ps-4 text-center">No</th>
                                            <th>Nama Vendor</th>
                                            <th>Kontak</th>
                                            <th>Alamat</th>
                                            <th width="200" class="pe-4 text-end">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (mysqli_num_rows($tampil) > 0) {
                                            $no = 1;
                                            while ($row = mysqli_fetch_assoc($tampil)) {
                                        ?>
                                            <tr>
                                                <td class="ps-4 text-center text-muted fw-semibold"><?= $no++; ?></td>
                                                <td><strong><?= htmlspecialchars($row['nama_vendor']); ?></strong></td>
                                                <td><span class="badge bg-info text-dark"><?= htmlspecialchars($row['kontak_vendor']); ?></span></td>
                                                <td><small class="text-muted"><?= htmlspecialchars($row['alamat_vendor']); ?></small></td>
                                                <td class="pe-4 text-end">
                                                    <div class="d-inline-flex gap-2">
                                                        <button
                                                            type="button"
                                                            class="btn btn-edit-user-custom btn-edit-vendor btn-sm"
                                                            data-id="<?= $row['id_vendor']; ?>"
                                                            data-nama="<?= htmlspecialchars($row['nama_vendor']); ?>"
                                                            data-kontak="<?= htmlspecialchars($row['kontak_vendor']); ?>"
                                                            data-alamat="<?= htmlspecialchars($row['alamat_vendor']); ?>">
                                                            <i class="bi bi-pencil me-1"></i> Edit
                                                        </button>
                                                        <a
                                                            href="vendor.php?hapus=<?= $row['id_vendor']; ?>"
                                                            class="btn btn-hapus-user-custom btn-hapus-vendor btn-sm"
                                                            data-nama="<?= htmlspecialchars($row['nama_vendor']); ?>">
                                                            <i class="bi bi-trash me-1"></i> Hapus
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php
                                            }
                                        } else {
                                            echo '<tr><td colspan="5" class="text-center text-muted py-4">Belum ada vendor terdaftar.</td></tr>';
                                        }
                                        ?>
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

<div class="modal fade" id="modalTambahVendor" tabindex="-1" aria-labelledby="modalTambahVendorLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 12px; overflow: hidden;">
            <form method="POST" action="">
                <div class="modal-header bg-white border-bottom-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-dark" id="modalTambahVendorLabel">
                        <i class="bi bi-building-fill text-primary me-2"></i> Tambah Vendor Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 pb-4 pt-2">
                    <div class="mb-3">
                        <label for="nama_vendor" class="form-label fw-semibold text-secondary" style="font-size: 0.9rem;">Nama Perusahaan / Vendor</label>
                        <input type="text" id="nama_vendor" name="nama_vendor" class="form-control" placeholder="Contoh: PT. Sumber Makmur" required style="border-radius: 8px;">
                    </div>
                    <div class="mb-3">
                        <label for="kontak_vendor" class="form-label fw-semibold text-secondary" style="font-size: 0.9rem;">Nomor Kontak / Telepon</label>
                        <input type="text" id="kontak_vendor" name="kontak_vendor" class="form-control" placeholder="Contoh: 021-xxxxxxxx" required style="border-radius: 8px;">
                    </div>
                    <div class="mb-0">
                        <label for="alamat_vendor" class="form-label fw-semibold text-secondary" style="font-size: 0.9rem;">Alamat Lengkap Kantor</label>
                        <textarea id="alamat_vendor" name="alamat_vendor" class="form-control" rows="3" placeholder="Tulis alamat operasional..." required style="border-radius: 8px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-light fw-semibold" data-bs-dismiss="modal" style="border-radius: 8px; padding: 0.6rem 1.2rem;">Batal</button>
                    <button type="submit" name="tambah_vendor" class="btn btn-primary fw-semibold shadow-sm animate-btn" style="border-radius: 8px; padding: 0.6rem 1.2rem; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); border: none;">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditVendor" tabindex="-1" aria-labelledby="modalEditVendorLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 12px; overflow: hidden;">
            <form method="POST" action="">
                <input type="hidden" id="edit_id_vendor" name="id_vendor">
                <div class="modal-header bg-white border-bottom-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-dark" id="modalEditVendorLabel">
                        <i class="bi bi-pencil-square text-primary me-2"></i> Edit Vendor
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 pb-4 pt-2">
                    <div class="mb-3">
                        <label for="edit_nama_vendor" class="form-label fw-semibold text-secondary" style="font-size: 0.9rem;">Nama Perusahaan / Vendor</label>
                        <input type="text" id="edit_nama_vendor" name="nama_vendor" class="form-control" required style="border-radius: 8px;">
                    </div>
                    <div class="mb-3">
                        <label for="edit_kontak_vendor" class="form-label fw-semibold text-secondary" style="font-size: 0.9rem;">Nomor Kontak / Telepon</label>
                        <input type="text" id="edit_kontak_vendor" name="kontak_vendor" class="form-control" required style="border-radius: 8px;">
                    </div>
                    <div class="mb-0">
                        <label for="edit_alamat_vendor" class="form-label fw-semibold text-secondary" style="font-size: 0.9rem;">Alamat Lengkap Kantor</label>
                        <textarea id="edit_alamat_vendor" name="alamat_vendor" class="form-control" rows="3" required style="border-radius: 8px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-light fw-semibold" data-bs-dismiss="modal" style="border-radius: 8px; padding: 0.6rem 1.2rem;">Batal</button>
                    <button type="submit" name="update_vendor" class="btn btn-primary fw-semibold shadow-sm animate-btn" style="border-radius: 8px; padding: 0.6rem 1.2rem; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); border: none;">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
$additionalScripts = '';
$swal_icon = in_array($message_type, ['success', 'warning', 'error', 'info'], true) ? $message_type : 'info';
$additionalScripts = '
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const editVendorModalElement = document.getElementById("modalEditVendor");
    const editVendorModal = new bootstrap.Modal(editVendorModalElement);

    document.querySelectorAll(".btn-edit-vendor").forEach(function (button) {
        button.addEventListener("click", function () {
            document.getElementById("edit_id_vendor").value = this.dataset.id || "";
            document.getElementById("edit_nama_vendor").value = this.dataset.nama || "";
            document.getElementById("edit_kontak_vendor").value = this.dataset.kontak || "";
            document.getElementById("edit_alamat_vendor").value = this.dataset.alamat || "";
            editVendorModal.show();
        });
    });

    document.querySelectorAll(".btn-hapus-vendor").forEach(function (button) {
        button.addEventListener("click", function (event) {
            event.preventDefault();
            const deleteUrl = this.getAttribute("href");
            const nama = this.dataset.nama || "vendor ini";

            Swal.fire({
                icon: "warning",
                title: "Hapus vendor?",
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

