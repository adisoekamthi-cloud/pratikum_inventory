<?php
$topbar_foto = 'safii.jpg';
$topbar_user_id = $_SESSION['user_id'] ?? null;
$session_foto = $_SESSION['foto'] ?? '';
$user_created_at = $_SESSION['created_at'] ?? '';

if (isset($user_data['created_at'])) {
    $user_created_at = $user_data['created_at'];
}

if (isset($user_data['foto']) && !empty($user_data['foto'])) {
    $topbar_foto = $user_data['foto'];
} elseif (!empty($session_foto)) {
    $topbar_foto = $session_foto;
} elseif ($topbar_user_id && isset($koneksi)) {
    $topbar_user_id = mysqli_real_escape_string($koneksi, (string) $topbar_user_id);
    $topbar_user_query = mysqli_query($koneksi, "SELECT foto, created_at FROM users WHERE id = '$topbar_user_id' LIMIT 1");
    if ($topbar_user_query) {
        $topbar_user = mysqli_fetch_assoc($topbar_user_query);
        if (!empty($topbar_user['foto'])) {
            $topbar_foto = $topbar_user['foto'];
        }
        if (!empty($topbar_user['created_at'])) {
            $user_created_at = $topbar_user['created_at'];
        }
    }
}

$months = [
    'January' => 'Januari',
    'February' => 'Februari',
    'March' => 'Maret',
    'April' => 'April',
    'May' => 'Mei',
    'June' => 'Juni',
    'July' => 'Juli',
    'August' => 'Agustus',
    'September' => 'September',
    'October' => 'Oktober',
    'November' => 'November',
    'December' => 'Desember'
];

$formatted_date = 'Juni 2026';
if (!empty($user_created_at)) {
    $time = strtotime($user_created_at);
    if ($time) {
        $eng_date = date('F Y', $time);
        $formatted_date = strtr($eng_date, $months);
    }
}

$topbar_foto_src = 'assets/img/' . htmlspecialchars($topbar_foto);
?>
<nav class="app-header navbar navbar-expand bg-body">
    <div class="container-fluid">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                    <i class="bi bi-list"></i>
                </a>
            </li>
        </ul>
        <ul class="navbar-nav ms-auto">
            <!-- Theme Toggle -->
            <li class="nav-item me-2">
                <a class="nav-link" href="#" id="theme-toggle" role="button" title="Toggle Theme">
                    <i class="bi bi-sun-fill" id="theme-toggle-icon"></i>
                </a>
            </li>
            
            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <img src="<?= $topbar_foto_src; ?>" class="user-image rounded-circle shadow" alt="User Image" />
                    <span class="d-none d-md-inline"><?= htmlspecialchars($username); ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end border-0 shadow-lg" style="border-radius: 16px; overflow: hidden; min-width: 280px;">
                    <li class="user-header text-bg-primary d-flex flex-column align-items-center justify-content-center" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%) !important; height: 175px; padding: 20px;">
                        <img src="<?= $topbar_foto_src; ?>" class="rounded-circle border border-3 border-white shadow mb-2" style="width: 80px; height: 80px; object-fit: cover;" alt="User Image" />
                        <p class="mb-0 fw-semibold text-white" style="font-size: 1.05rem; letter-spacing: 0.3px;">
                            <?= htmlspecialchars($nama_user); ?>
                        </p>
                        <small class="text-white-50" style="font-size: 0.8rem; margin-top: 2px;"><?= $role; ?> &bull; Member since <?= htmlspecialchars($formatted_date); ?></small>
                    </li>
                    <li class="user-footer d-flex align-items-center justify-content-between bg-white" style="padding: 15px 20px;">
                        <button type="button" class="btn btn-light btn-sm border fw-semibold px-3 py-2 text-secondary" data-bs-toggle="modal" data-bs-target="#profileDialogAdmin" style="border-radius: 8px; background-color: #f8fafc; font-size: 0.85rem;">
                            <i class="bi bi-person me-1"></i> Profile
                        </button>
                        <a href="logout.php" class="btn btn-outline-danger btn-sm fw-semibold px-3 py-2" onclick="return confirmAdminLogout(this.href);" style="border-radius: 8px; font-size: 0.85rem;">
                            <i class="bi bi-box-arrow-right me-1"></i> Sign out
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>

<div class="modal fade" id="profileDialogAdmin" tabindex="-1" aria-labelledby="profileDialogAdminLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 20px;">
            <!-- Premium Header Banner -->
            <div class="bg-gradient bg-primary position-relative d-flex align-items-center justify-content-end p-3" style="height: 110px; border-bottom: none; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
                <button type="button" class="btn-close btn-close-white position-absolute" data-bs-dismiss="modal" aria-label="Close" style="top: 15px; right: 15px;"></button>
            </div>
            
            <!-- Modal Body with Overlapping Avatar -->
            <div class="modal-body text-center pt-0 px-4 pb-4 position-relative">
                <div class="d-flex justify-content-center" style="margin-top: -55px; z-index: 5; position: relative;">
                    <img src="<?= $topbar_foto_src; ?>" class="rounded-circle border border-4 border-white shadow-lg" style="width: 100px; height: 100px; object-fit: cover; background-color: #fff;" alt="Profile">
                </div>
                
                <h4 class="fw-bold mt-3 mb-1 text-dark"><?= htmlspecialchars($nama_user); ?></h4>
                <p class="text-muted mb-4 small"><i class="bi bi-at text-secondary"></i><?= htmlspecialchars($username); ?></p>
                
                <div class="d-grid gap-2 mt-2 text-start">
                    <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3 border-0">
                        <span class="text-muted small fw-medium"><i class="bi bi-shield-lock-fill me-2 text-primary"></i>Role</span>
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1.5 rounded-pill fw-semibold border border-primary border-opacity-25" style="font-size: 0.8rem;">
                            <?= htmlspecialchars($role); ?>
                        </span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3 border-0">
                        <span class="text-muted small fw-medium"><i class="bi bi-check-circle-fill me-2 text-success"></i>Status Akun</span>
                        <span class="badge bg-success bg-opacity-10 text-success px-3 py-1.5 rounded-pill fw-semibold border border-success border-opacity-25" style="font-size: 0.8rem;">
                            Aktif
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer bg-light border-0 d-flex justify-content-between px-4 py-3">
                <button type="button" class="btn btn-secondary px-3 py-2 rounded-3 fw-semibold small" data-bs-dismiss="modal">Tutup</button>
                <a href="profile.php" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold small shadow-sm" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); border: none;">
                    <i class="bi bi-person-fill me-1"></i> Buka Profil
                </a>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmAdminLogout(logoutUrl) {
    Swal.fire({
        icon: "warning",
        title: "Logout sekarang?",
        text: "Sesi Anda akan diakhiri dan Anda perlu login kembali.",
        showCancelButton: true,
        confirmButtonText: "Ya, logout",
        cancelButtonText: "Batal",
        confirmButtonColor: "#dc3545",
        cancelButtonColor: "#6c757d"
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = logoutUrl;
        }
    });

    return false;
}

document.addEventListener("DOMContentLoaded", function () {
    const themeToggle = document.getElementById("theme-toggle");
    const themeIcon = document.getElementById("theme-toggle-icon");
    
    if (themeToggle && themeIcon) {
        function updateToggleUI(theme) {
            if (theme === "dark") {
                themeIcon.classList.remove("bi-sun-fill");
                themeIcon.classList.add("bi-moon-stars-fill");
                themeToggle.setAttribute("title", "Ubah ke Tema Terang");
            } else {
                themeIcon.classList.remove("bi-moon-stars-fill");
                themeIcon.classList.add("bi-sun-fill");
                themeToggle.setAttribute("title", "Ubah ke Tema Gelap");
            }
        }
        
        const currentTheme = document.documentElement.getAttribute("data-bs-theme") || "light";
        updateToggleUI(currentTheme);
        
        themeToggle.addEventListener("click", function (e) {
            e.preventDefault();
            const activeTheme = document.documentElement.getAttribute("data-bs-theme");
            const newTheme = activeTheme === "dark" ? "light" : "dark";
            
            document.documentElement.setAttribute("data-bs-theme", newTheme);
            localStorage.setItem("theme", newTheme);
            updateToggleUI(newTheme);
        });
    }
});
</script>
