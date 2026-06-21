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

// GET PROFILE DATA FROM USERS TABLE
$user_id = $_SESSION['user_id'] ?? null;
$session_username = mysqli_real_escape_string($koneksi, $_SESSION['username'] ?? '');

if (!empty($user_id)) {
    $safe_user_id = mysqli_real_escape_string($koneksi, (string) $user_id);
    $query_user = mysqli_query($koneksi, "SELECT * FROM users WHERE id = '$safe_user_id' LIMIT 1");
} else {
    $query_user = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$session_username' LIMIT 1");
}

$user_data = mysqli_fetch_assoc($query_user) ?: [];
if (!empty($user_data['id'])) {
    $user_id = $user_data['id'];
    $_SESSION['user_id'] = $user_id;
    $_SESSION['foto'] = $user_data['foto'] ?? '';
}

// PROSES UPDATE PROFILE
$message = '';
$message_type = '';

if (isset($_POST['update_profile'])) {
    $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    
    $update_query = "UPDATE users SET nama_lengkap = '$nama_lengkap' WHERE id = '$user_id'";
    if (mysqli_query($koneksi, $update_query)) {
        $_SESSION['nama_lengkap'] = $nama_lengkap;
        $nama_user = htmlspecialchars($nama_lengkap);
        $message = 'Profil berhasil diperbarui!';
        $message_type = 'success';
        // Refresh user data
        $query_user = mysqli_query($koneksi, "SELECT * FROM users WHERE id = '$user_id' LIMIT 1");
        $user_data = mysqli_fetch_assoc($query_user);
    } else {
        $message = 'Gagal memperbarui profil!';
        $message_type = 'danger';
    }
}

// PROSES UPLOAD FOTO PROFIL
$photo_message = '';
$photo_message_type = '';

if (isset($_POST['update_photo'])) {
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $foto_tmp = $_FILES['photo']['tmp_name'];
        $foto_name = $_FILES['photo']['name'];
        $foto_size = $_FILES['photo']['size'];
        $foto_type = $_FILES['photo']['type'];
        $old_photo = $user_data['foto'] ?? '';
        
        // Validasi tipe file
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($foto_type, $allowed_types)) {
            $photo_message = 'Tipe file hanya boleh: JPG, PNG, GIF, atau WebP!';
            $photo_message_type = 'warning';
        } else if ($foto_size > 5 * 1024 * 1024) { // Max 5MB
            $photo_message = 'Ukuran file maksimal 5MB!';
            $photo_message_type = 'warning';
        } else {
            // Generate unique filename
            $foto_ext = strtolower(pathinfo($foto_name, PATHINFO_EXTENSION));
            $foto_new_name = 'profile_' . $user_id . '_' . time() . '.' . $foto_ext;
            $foto_path = 'assets/img/' . $foto_new_name;
            
            if (move_uploaded_file($foto_tmp, $foto_path)) {
                // Update database
                $update_photo_query = "UPDATE users SET foto = '$foto_new_name' WHERE id = '$user_id'";
                if (mysqli_query($koneksi, $update_photo_query)) {
                    $_SESSION['foto'] = $foto_new_name;
                    if (!empty($old_photo) && $old_photo !== 'safii.jpg' && file_exists('assets/img/' . $old_photo)) {
                        unlink('assets/img/' . $old_photo);
                    }
                    $photo_message = 'Foto profil berhasil diperbarui!';
                    $photo_message_type = 'success';
                    // Refresh user data
                    $query_user = mysqli_query($koneksi, "SELECT * FROM users WHERE id = '$user_id'");
                    $user_data = mysqli_fetch_assoc($query_user);
                } else {
                    if (file_exists($foto_path)) {
                        unlink($foto_path);
                    }
                    $photo_message = 'Gagal menyimpan foto ke database!';
                    $photo_message_type = 'danger';
                }
            } else {
                $photo_message = 'Gagal mengupload foto!';
                $photo_message_type = 'danger';
            }
        }
    } else {
        $photo_message = 'Pilih foto terlebih dahulu!';
        $photo_message_type = 'warning';
    }
}

// PROSES UPDATE PASSWORD
$pass_message = '';
$pass_message_type = '';

if (isset($_POST['update_password'])) {
    $password_lama = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];
    $password_konfirm = $_POST['password_konfirm'];
    
    // Verify old password
    if (password_verify($password_lama, $user_data['password'])) {
        if ($password_baru === $password_konfirm) {
            if (strlen($password_baru) >= 6) {
                $password_hash = password_hash($password_baru, PASSWORD_BCRYPT);
                $update_pass_query = "UPDATE users SET password = '$password_hash' WHERE id = '$user_id'";
                if (mysqli_query($koneksi, $update_pass_query)) {
                    $pass_message = 'Password berhasil diperbarui!';
                    $pass_message_type = 'success';
                } else {
                    $pass_message = 'Gagal memperbarui password!';
                    $pass_message_type = 'danger';
                }
            } else {
                $pass_message = 'Password minimal 6 karakter!';
                $pass_message_type = 'warning';
            }
        } else {
            $pass_message = 'Password baru dan konfirmasi tidak cocok!';
            $pass_message_type = 'warning';
        }
    } else {
        $pass_message = 'Password lama salah!';
        $pass_message_type = 'danger';
    }
}

$pageTitle = 'Profil | Admin';
$activePage = 'profile';
include __DIR__ . '/components/head.php';
include __DIR__ . '/components/topbar.php';
include __DIR__ . '/components/sidebar.php';
?>
<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0 fw-bold">Profil Saya</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Profil</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <!-- PROFILE CARD -->
                    <div class="card shadow-sm border-0 mb-4 overflow-hidden">
                        <div class="card-header bg-gradient bg-primary position-relative" style="height: 80px; border-bottom: none;">
                            <!-- Decorative background header -->
                        </div>
                        <div class="card-body text-center pt-0 position-relative">
                            <?php 
                            $foto_display = !empty($user_data['foto']) ? 'assets/img/' . htmlspecialchars($user_data['foto']) : 'assets/img/safii.jpg';
                            ?>
                            <div class="d-flex justify-content-center" style="margin-top: -50px;">
                                <img src="<?= $foto_display; ?>" class="rounded-circle border border-4 border-white shadow" style="width: 100px; height: 100px; object-fit: cover; background-color: #fff;" alt="Profile">
                            </div>
                            <h4 class="fw-bold mt-3 mb-1 text-dark text-center w-100 d-block"><?= $nama_user; ?></h4>
                            <div class="mb-3 d-flex justify-content-center">
                                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1.5 rounded-pill fw-semibold border border-primary border-opacity-25" style="font-size: 0.85rem;">
                                    <i class="bi bi-shield-lock-fill me-1"></i><?= $role; ?>
                                </span>
                            </div>
                            
                            <div class="d-grid gap-2 mt-4 text-start">
                                <div class="d-flex align-items-center justify-content-between p-2.5 px-3 bg-light rounded-3 border-0">
                                    <span class="text-muted small fw-medium"><i class="bi bi-person-fill me-2 text-secondary"></i>Username</span>
                                    <span class="fw-bold text-dark small"><?= $username; ?></span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between p-2.5 px-3 bg-light rounded-3 border-0">
                                    <span class="text-muted small fw-medium"><i class="bi bi-calendar3 me-2 text-secondary"></i>Bergabung</span>
                                    <span class="fw-bold text-dark small"><?= htmlspecialchars($formatted_date); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- UPLOAD FOTO PROFIL -->
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white border-bottom-0 pt-3">
                            <h5 class="card-title fw-bold text-dark mb-0"><i class="bi bi-image text-info me-2"></i>Ubah Foto Profil</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label class="form-label text-muted small fw-semibold">Pilih Foto Baru</label>
                                    <div class="input-group">
                                        <input type="file" name="photo" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp" required>
                                    </div>
                                    <div class="form-text text-muted" style="font-size: 0.75rem;">Format: JPG, PNG, GIF, WebP | Max 5MB</div>
                                </div>
                                <button type="submit" name="update_photo" class="btn btn-info text-white w-100 fw-semibold">
                                    <i class="bi bi-upload me-1"></i> Upload Foto
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <!-- UPDATE PROFILE -->
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white border-bottom-0 pt-3">
                            <h5 class="card-title fw-bold text-dark mb-0"><i class="bi bi-person-fill text-primary me-2"></i>Perbarui Profil</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-semibold">Nama Lengkap</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-person-bounding-box"></i></span>
                                            <input type="text" name="nama_lengkap" class="form-control border-start-0 ps-0" value="<?= htmlspecialchars($user_data['nama_lengkap'] ?? ''); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-semibold">Username</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-at"></i></span>
                                            <input type="text" class="form-control border-start-0 ps-0 bg-light" value="<?= $username; ?>" disabled>
                                        </div>
                                        <div class="form-text text-muted" style="font-size: 0.75rem;">Username tidak dapat diubah</div>
                                    </div>
                                </div>
                                <button type="submit" name="update_profile" class="btn btn-primary fw-semibold">
                                    <i class="bi bi-check-circle me-1"></i> Simpan Perubahan
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- CHANGE PASSWORD -->
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white border-bottom-0 pt-3">
                            <h5 class="card-title fw-bold text-dark mb-0"><i class="bi bi-lock-fill text-warning me-2"></i>Ubah Password</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label class="form-label text-muted small fw-semibold">Password Lama</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-key-fill"></i></span>
                                        <input type="password" name="password_lama" class="form-control border-start-0 ps-0" required>
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-semibold">Password Baru</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-shield-lock"></i></span>
                                            <input type="password" name="password_baru" class="form-control border-start-0 ps-0" required>
                                        </div>
                                        <div class="form-text text-muted" style="font-size: 0.75rem;">Minimal 6 karakter</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-semibold">Konfirmasi Password</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-shield-lock-fill"></i></span>
                                            <input type="password" name="password_konfirm" class="form-control border-start-0 ps-0" required>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" name="update_password" class="btn btn-warning text-dark fw-semibold">
                                    <i class="bi bi-shield-fill-check me-1"></i> Ubah Password
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php
$swal_msg = '';
$swal_type = '';

if (!empty($message)) {
    $swal_msg = $message;
    $swal_type = $message_type;
} elseif (!empty($photo_message)) {
    $swal_msg = $photo_message;
    $swal_type = $photo_message_type;
} elseif (!empty($pass_message)) {
    $swal_msg = $pass_message;
    $swal_type = $pass_message_type;
}

$additionalScripts = '';
if ($swal_msg) {
    $swal_icon = $swal_type;
    if ($swal_type === 'danger') {
        $swal_icon = 'error';
    }
    
    $additionalScripts = '
    <script>
        Swal.fire({
            icon: ' . json_encode($swal_icon) . ',
            title: ' . json_encode(ucfirst($swal_icon)) . ',
            text: ' . json_encode($swal_msg) . ',
            confirmButtonText: "OK"
        });
    </script>';
}
?>
<?php include __DIR__ . '/components/footer.php'; ?>

