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

$message = $_SESSION['flash_status_message'] ?? '';
$message_type = $_SESSION['flash_status_type'] ?? '';
unset($_SESSION['flash_status_message'], $_SESSION['flash_status_type']);

if (isset($_POST['simpan_status'])) {
    $status = mysqli_real_escape_string($koneksi, trim($_POST['status'] ?? ''));

    if ($status === '') {
        $_SESSION['flash_status_message'] = 'Nama status wajib diisi.';
        $_SESSION['flash_status_type'] = 'warning';
    } else {
        $insert_status = mysqli_query($koneksi, "INSERT INTO status_barang (nama_status) VALUES ('$status')");

        if ($insert_status) {
            $_SESSION['flash_status_message'] = 'Status berhasil ditambahkan.';
            $_SESSION['flash_status_type'] = 'success';
        } else {
            $_SESSION['flash_status_message'] = 'Gagal menambahkan status.';
            $_SESSION['flash_status_type'] = 'error';
        }
    }

    header('Location: status.php');
    exit;
}

if (isset($_POST['update_status'])) {
    $id_status = mysqli_real_escape_string($koneksi, trim($_POST['id_status'] ?? ''));
    $status = mysqli_real_escape_string($koneksi, trim($_POST['status'] ?? ''));

    if ($id_status === '' || $status === '') {
        $_SESSION['flash_status_message'] = 'Data status tidak lengkap.';
        $_SESSION['flash_status_type'] = 'warning';
    } else {
        $update_status = mysqli_query($koneksi, "UPDATE status_barang SET nama_status = '$status' WHERE id = '$id_status'");

        if ($update_status) {
            $_SESSION['flash_status_message'] = 'Status berhasil diperbarui.';
            $_SESSION['flash_status_type'] = 'success';
        } else {
            $_SESSION['flash_status_message'] = 'Gagal memperbarui status.';
            $_SESSION['flash_status_type'] = 'error';
        }
    }

    header('Location: status.php');
    exit;
}

$data = mysqli_query($koneksi, "SELECT * FROM status_barang");

$pageTitle = 'Status Barang | Admin';
$activePage = 'status';
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
                    <h3 class="mb-0 fw-bold">Data Status Barang</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Status Barang</li>
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
                            <h5 class="mb-0 fw-bold"><i class="bi bi-table"></i> Tabel Status Barang</h5>
                            <button type="button" class="btn btn-light btn-sm ms-auto px-3 py-2 fw-semibold shadow-sm animate-btn" data-bs-toggle="modal" data-bs-target="#modalTambahStatus" style="border-radius: 8px; border: none;">
                                <i class="bi bi-plus-lg me-1"></i> Tambah Status
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light text-secondary fw-semibold" style="font-size: 0.85rem; border-bottom: 1px solid #f1f5f9;">
                                        <tr>
                                            <th width="80" class="ps-4 text-center">No</th>
                                            <th>Nama Status</th>
                                            <th width="200" class="pe-4 text-end">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        while ($row = mysqli_fetch_assoc($data)) {
                                        ?>
                                            <tr>
                                                <td class="ps-4 text-center text-muted fw-semibold"><?= $no++; ?></td>
                                                <td><strong><?= htmlspecialchars($row['nama_status']); ?></strong></td>
                                                <td class="pe-4 text-end">
                                                    <div class="d-inline-flex gap-2">
                                                        <button
                                                            type="button"
                                                            class="btn btn-edit-user-custom btn-edit-status btn-sm"
                                                            data-id="<?= $row['id']; ?>"
                                                            data-status-name="<?= htmlspecialchars($row['nama_status']); ?>">
                                                            <i class="bi bi-pencil me-1"></i> Edit
                                                        </button>
                                                        <a
                                                            href="hapus_status.php?id=<?= $row['id']; ?>"
                                                            class="btn btn-hapus-user-custom btn-hapus-status btn-sm"
                                                            data-status-name="<?= htmlspecialchars($row['nama_status']); ?>">
                                                            <i class="bi bi-trash me-1"></i> Hapus
                                                        </a>
                                                    </div>
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
    </div>
</main>

<div class="modal fade" id="modalTambahStatus" tabindex="-1" aria-labelledby="modalTambahStatusLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 12px; overflow: hidden;">
            <form method="POST" action="">
                <div class="modal-header bg-white border-bottom-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-dark" id="modalTambahStatusLabel">
                        <i class="bi bi-tag-fill text-primary me-2"></i> Tambah Status Barang
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 pb-4 pt-2">
                    <div class="mb-0">
                        <label for="status" class="form-label fw-semibold text-secondary" style="font-size: 0.9rem;">Nama Status</label>
                        <input type="text" id="status" name="status" class="form-control" placeholder="Masukkan nama status" required style="border-radius: 8px;">
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-light fw-semibold" data-bs-dismiss="modal" style="border-radius: 8px; padding: 0.6rem 1.2rem;">Batal</button>
                    <button type="submit" name="simpan_status" class="btn btn-primary fw-semibold shadow-sm animate-btn" style="border-radius: 8px; padding: 0.6rem 1.2rem; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); border: none;">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditStatus" tabindex="-1" aria-labelledby="modalEditStatusLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 12px; overflow: hidden;">
            <form method="POST" action="">
                <input type="hidden" id="edit_id_status" name="id_status">
                <div class="modal-header bg-white border-bottom-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-dark" id="modalEditStatusLabel">
                        <i class="bi bi-pencil-square text-primary me-2"></i> Edit Status Barang
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 pb-4 pt-2">
                    <div class="mb-0">
                        <label for="edit_status" class="form-label fw-semibold text-secondary" style="font-size: 0.9rem;">Nama Status</label>
                        <input type="text" id="edit_status" name="status" class="form-control" placeholder="Masukkan nama status" required style="border-radius: 8px;">
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-light fw-semibold" data-bs-dismiss="modal" style="border-radius: 8px; padding: 0.6rem 1.2rem;">Batal</button>
                    <button type="submit" name="update_status" class="btn btn-primary fw-semibold shadow-sm animate-btn" style="border-radius: 8px; padding: 0.6rem 1.2rem; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); border: none;">Update</button>
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
    const editStatusModalElement = document.getElementById("modalEditStatus");
    const editStatusModal = new bootstrap.Modal(editStatusModalElement);
    const editStatusInput = document.getElementById("edit_status");
    const editStatusIdInput = document.getElementById("edit_id_status");

    document.querySelectorAll(".btn-edit-status").forEach(function (button) {
        button.addEventListener("click", function () {
            editStatusIdInput.value = this.dataset.id || "";
            editStatusInput.value = this.dataset.statusName || "";
            editStatusModal.show();
        });
    });

    document.querySelectorAll(".btn-hapus-status").forEach(function (button) {
        button.addEventListener("click", function (event) {
            event.preventDefault();
            const deleteUrl = this.getAttribute("href");
            const statusName = this.dataset.statusName || "status ini";

            Swal.fire({
                icon: "warning",
                title: "Hapus status?",
                text: "Data " + statusName + " akan dihapus permanen.",
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

