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

$message = $_SESSION['flash_users_message'] ?? '';
$message_type = $_SESSION['flash_users_type'] ?? '';
unset($_SESSION['flash_users_message'], $_SESSION['flash_users_type']);

if (isset($_POST['tambah_user'])) {
    $nama_lengkap = mysqli_real_escape_string($koneksi, trim($_POST['nama_lengkap'] ?? ''));
    $username_baru = mysqli_real_escape_string($koneksi, trim($_POST['username'] ?? ''));
    $password = $_POST['password'] ?? '';
    $role_baru = mysqli_real_escape_string($koneksi, $_POST['role'] ?? 'pengguna');

    if ($nama_lengkap === '' || $username_baru === '' || $password === '' || !in_array($role_baru, ['admin', 'pengguna'], true)) {
        $_SESSION['flash_users_message'] = 'Data user belum lengkap.';
        $_SESSION['flash_users_type'] = 'warning';
    } else {
        $check_user = mysqli_query($koneksi, "SELECT id FROM users WHERE username = '$username_baru' LIMIT 1");
        if ($check_user && mysqli_num_rows($check_user) > 0) {
            $_SESSION['flash_users_message'] = 'Username sudah digunakan.';
            $_SESSION['flash_users_type'] = 'warning';
        } else {
            $password_hash = md5($password);
            $insert_user = mysqli_query(
                $koneksi,
                "INSERT INTO users (username, password, nama_lengkap, role)
                 VALUES ('$username_baru', '$password_hash', '$nama_lengkap', '$role_baru')"
            );

            if ($insert_user) {
                $_SESSION['flash_users_message'] = 'User berhasil ditambahkan.';
                $_SESSION['flash_users_type'] = 'success';
            } else {
                $_SESSION['flash_users_message'] = 'Gagal menambahkan user.';
                $_SESSION['flash_users_type'] = 'error';
            }
        }
    }

    header('Location: users.php');
    exit;
}

if (isset($_POST['update_user'])) {
    $id_user = mysqli_real_escape_string($koneksi, $_POST['id_user'] ?? '');
    $nama_lengkap = mysqli_real_escape_string($koneksi, trim($_POST['nama_lengkap'] ?? ''));
    $username_baru = mysqli_real_escape_string($koneksi, trim($_POST['username'] ?? ''));
    $password = $_POST['password'] ?? '';
    $role_baru = mysqli_real_escape_string($koneksi, $_POST['role'] ?? 'pengguna');
    $current_user_id = (string) ($_SESSION['user_id'] ?? '');

    if ($id_user === '' || $nama_lengkap === '' || $username_baru === '' || !in_array($role_baru, ['admin', 'pengguna'], true)) {
        $_SESSION['flash_users_message'] = 'Data user belum lengkap.';
        $_SESSION['flash_users_type'] = 'warning';
    } else {
        $check_user = mysqli_query($koneksi, "SELECT id FROM users WHERE username = '$username_baru' AND id != '$id_user' LIMIT 1");
        if ($check_user && mysqli_num_rows($check_user) > 0) {
            $_SESSION['flash_users_message'] = 'Username sudah digunakan user lain.';
            $_SESSION['flash_users_type'] = 'warning';
        } elseif ($id_user === $current_user_id && $role_baru !== 'admin') {
            $_SESSION['flash_users_message'] = 'Role akun admin yang sedang aktif tidak boleh diubah menjadi pengguna.';
            $_SESSION['flash_users_type'] = 'warning';
        } else {
            $update_fields = [
                "username = '$username_baru'",
                "nama_lengkap = '$nama_lengkap'",
                "role = '$role_baru'",
            ];

            if ($password !== '') {
                $update_fields[] = "password = '" . md5($password) . "'";
            }

            $update_user = mysqli_query($koneksi, "UPDATE users SET " . implode(', ', $update_fields) . " WHERE id = '$id_user'");

            if ($update_user) {
                if ($id_user === $current_user_id) {
                    $_SESSION['username'] = $username_baru;
                    $_SESSION['nama_lengkap'] = $nama_lengkap;
                    $_SESSION['role'] = $role_baru;
                }

                $_SESSION['flash_users_message'] = 'User berhasil diperbarui.';
                $_SESSION['flash_users_type'] = 'success';
            } else {
                $_SESSION['flash_users_message'] = 'Gagal memperbarui user.';
                $_SESSION['flash_users_type'] = 'error';
            }
        }
    }

    header('Location: users.php');
    exit;
}

if (isset($_POST['hapus_user'])) {
    $id_user = mysqli_real_escape_string($koneksi, $_POST['id_user'] ?? '');
    $current_user_id = (string) ($_SESSION['user_id'] ?? '');

    if ($id_user === '') {
        $_SESSION['flash_users_message'] = 'ID user tidak valid.';
        $_SESSION['flash_users_type'] = 'error';
    } elseif ($id_user === $current_user_id) {
        $_SESSION['flash_users_message'] = 'Akun yang sedang digunakan tidak bisa dihapus.';
        $_SESSION['flash_users_type'] = 'warning';
    } else {
        $delete_user = mysqli_query($koneksi, "DELETE FROM users WHERE id = '$id_user'");

        if ($delete_user) {
            $_SESSION['flash_users_message'] = 'User berhasil dihapus.';
            $_SESSION['flash_users_type'] = 'success';
        } else {
            $_SESSION['flash_users_message'] = 'Gagal menghapus user.';
            $_SESSION['flash_users_type'] = 'error';
        }
    }

    header('Location: users.php');
    exit;
}

$users = mysqli_query($koneksi, "SELECT id, username, nama_lengkap, role FROM users ORDER BY id DESC");

$pageTitle = 'Users | Admin';
$activePage = 'users';
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
        }
        .btn-hapus-user-custom:hover {
            color: #ffffff;
            background: #dc3545;
            border-color: #dc3545;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(220, 53, 69, 0.2);
        }
        .avatar-circle {
            transition: transform 0.2s ease;
        }
        tr:hover .avatar-circle {
            transform: scale(1.08);
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
                    <h3 class="mb-0 fw-bold">Manajemen User</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Users</li>
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
                            <h5 class="mb-0 fw-bold"><i class="bi bi-table"></i> Daftar User</h5>
                            <button type="button" class="btn btn-light btn-sm ms-auto px-3 py-2 fw-semibold shadow-sm animate-btn" data-bs-toggle="modal" data-bs-target="#modalTambahUser" style="border-radius: 8px; border: none;">
                                <i class="bi bi-plus-lg me-1"></i> Tambah User
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light text-secondary fw-semibold" style="font-size: 0.85rem; border-bottom: 1px solid #f1f5f9;">
                                        <tr>
                                            <th width="80" class="ps-4 text-center">No</th>
                                            <th>Nama Lengkap</th>
                                            <th>Username</th>
                                            <th>Role</th>
                                            <th width="200" class="pe-4 text-end">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($users && mysqli_num_rows($users) > 0) {
                                            $no = 1;
                                            while ($user_row = mysqli_fetch_assoc($users)) {
                                        ?>
                                            <tr>
                                                <td class="ps-4 text-center text-muted fw-semibold"><?= $no++; ?></td>
                                                <td><strong><?= htmlspecialchars($user_row['nama_lengkap'] ?? '-'); ?></strong></td>
                                                <td><?= htmlspecialchars($user_row['username']); ?></td>
                                                <td>
                                                    <span class="badge <?= $user_row['role'] === 'admin' ? 'bg-danger' : 'bg-success'; ?>">
                                                        <?= htmlspecialchars(ucfirst($user_row['role'])); ?>
                                                    </span>
                                                </td>
                                                <td class="pe-4 text-end">
                                                    <div class="d-inline-flex gap-2">
                                                        <button
                                                            type="button"
                                                            class="btn btn-edit-user-custom btn-edit-user btn-sm"
                                                            data-id="<?= $user_row['id']; ?>"
                                                            data-nama="<?= htmlspecialchars($user_row['nama_lengkap'] ?? ''); ?>"
                                                            data-username="<?= htmlspecialchars($user_row['username']); ?>"
                                                            data-role="<?= htmlspecialchars($user_row['role']); ?>">
                                                            <i class="bi bi-pencil me-1"></i> Edit
                                                        </button>
                                                        <button
                                                            type="button"
                                                            class="btn btn-hapus-user-custom btn-hapus-user btn-sm"
                                                            data-id="<?= $user_row['id']; ?>"
                                                            data-nama="<?= htmlspecialchars($user_row['username']); ?>">
                                                            <i class="bi bi-trash me-1"></i> Hapus
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php
                                            }
                                        } else {
                                            echo '<tr><td colspan="5" class="text-center text-muted py-4">Belum ada user terdaftar.</td></tr>';
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

<div class="modal fade" id="modalTambahUser" tabindex="-1" aria-labelledby="modalTambahUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 12px; overflow: hidden;">
            <form method="POST" action="">
                <div class="modal-header bg-white border-bottom-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-dark" id="modalTambahUserLabel">
                        <i class="bi bi-person-plus-fill text-primary me-2"></i> Tambah User
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 pb-4 pt-2">
                    <div class="mb-3">
                        <label for="nama_lengkap" class="form-label fw-semibold text-secondary" style="font-size: 0.9rem;">Nama Lengkap</label>
                        <input type="text" id="nama_lengkap" name="nama_lengkap" class="form-control" required style="border-radius: 8px;">
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label fw-semibold text-secondary" style="font-size: 0.9rem;">Username</label>
                        <input type="text" id="username" name="username" class="form-control" required style="border-radius: 8px;">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold text-secondary" style="font-size: 0.9rem;">Password</label>
                        <input type="password" id="password" name="password" class="form-control" required style="border-radius: 8px;">
                    </div>
                    <div class="mb-0">
                        <label for="role" class="form-label fw-semibold text-secondary" style="font-size: 0.9rem;">Role</label>
                        <select id="role" name="role" class="form-select" required style="border-radius: 8px;">
                            <option value="pengguna">Pengguna</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-light fw-semibold" data-bs-dismiss="modal" style="border-radius: 8px; padding: 0.6rem 1.2rem;">Batal</button>
                    <button type="submit" name="tambah_user" class="btn btn-primary fw-semibold shadow-sm animate-btn" style="border-radius: 8px; padding: 0.6rem 1.2rem; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); border: none;">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditUser" tabindex="-1" aria-labelledby="modalEditUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 12px; overflow: hidden;">
            <form method="POST" action="">
                <input type="hidden" id="edit_id_user" name="id_user">
                <div class="modal-header bg-white border-bottom-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-dark" id="modalEditUserLabel">
                        <i class="bi bi-pencil-square text-primary me-2"></i> Edit User
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 pb-4 pt-2">
                    <div class="mb-3">
                        <label for="edit_nama_lengkap" class="form-label fw-semibold text-secondary" style="font-size: 0.9rem;">Nama Lengkap</label>
                        <input type="text" id="edit_nama_lengkap" name="nama_lengkap" class="form-control" required style="border-radius: 8px;">
                    </div>
                    <div class="mb-3">
                        <label for="edit_username" class="form-label fw-semibold text-secondary" style="font-size: 0.9rem;">Username</label>
                        <input type="text" id="edit_username" name="username" class="form-control" required style="border-radius: 8px;">
                    </div>
                    <div class="mb-3">
                        <label for="edit_password" class="form-label fw-semibold text-secondary" style="font-size: 0.9rem;">Password Baru</label>
                        <input type="password" id="edit_password" name="password" class="form-control" style="border-radius: 8px;">
                        <small class="text-muted" style="font-size: 0.75rem;">Kosongkan jika tidak ingin mengubah password.</small>
                    </div>
                    <div class="mb-0">
                        <label for="edit_role" class="form-label fw-semibold text-secondary" style="font-size: 0.9rem;">Role</label>
                        <select id="edit_role" name="role" class="form-select" required style="border-radius: 8px;">
                            <option value="pengguna">Pengguna</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-light fw-semibold" data-bs-dismiss="modal" style="border-radius: 8px; padding: 0.6rem 1.2rem;">Batal</button>
                    <button type="submit" name="update_user" class="btn btn-primary fw-semibold shadow-sm animate-btn" style="border-radius: 8px; padding: 0.6rem 1.2rem; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); border: none;">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<form id="formHapusUser" method="POST" action="" class="d-none">
    <input type="hidden" name="id_user" id="hapus_id_user">
    <input type="hidden" name="hapus_user" value="1">
</form>
<?php
$additionalScripts = '';
$swal_icon = in_array($message_type, ['success', 'warning', 'error', 'info'], true) ? $message_type : 'info';
$additionalScripts = '
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const editUserModalElement = document.getElementById("modalEditUser");
    const editUserModal = new bootstrap.Modal(editUserModalElement);

    document.querySelectorAll(".btn-edit-user").forEach(function (button) {
        button.addEventListener("click", function () {
            document.getElementById("edit_id_user").value = this.dataset.id || "";
            document.getElementById("edit_nama_lengkap").value = this.dataset.nama || "";
            document.getElementById("edit_username").value = this.dataset.username || "";
            document.getElementById("edit_role").value = this.dataset.role || "pengguna";
            document.getElementById("edit_password").value = "";
            editUserModal.show();
        });
    });

    document.querySelectorAll(".btn-hapus-user").forEach(function (button) {
        button.addEventListener("click", function () {
            const userId = this.dataset.id || "";
            const username = this.dataset.nama || "user ini";

            Swal.fire({
                icon: "warning",
                title: "Hapus user?",
                text: "Akun " + username + " akan dihapus permanen.",
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
                    document.getElementById("hapus_id_user").value = userId;
                    document.getElementById("formHapusUser").submit();
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

