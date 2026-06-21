<?php
require_once __DIR__ . '/includes/app_settings.php';
$appSettings = app_settings_load();
?>
<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?= htmlspecialchars($appSettings['app_name']); ?> | Register</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="<?= htmlspecialchars(app_settings_asset_url($appSettings, 'favicon')); ?>" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="AdminLTE-4.0.0-rc7/dist/css/adminlte.css" />
    
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Outfit', sans-serif;
            margin: 0;
            overflow-y: auto;
            position: relative;
            padding: 40px 0;
            box-sizing: border-box;
        }

        .bg-decoration {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            z-index: -1;
            overflow: hidden;
            pointer-events: none;
        }

        /* Background decorative glowing circles */
        .glow-circle {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.12;
        }
        .glow-circle-1 {
            width: 300px;
            height: 300px;
            background: #10b981; /* Emerald green */
            top: -50px;
            left: -50px;
        }
        .glow-circle-2 {
            width: 400px;
            height: 400px;
            background: #3b82f6; /* Blue */
            bottom: -100px;
            right: -100px;
        }

        .register-container {
            width: 100%;
            max-width: 460px;
            padding: 20px;
            z-index: 10;
        }

        .register-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: 20px;
            padding: 40px 30px;
            box-shadow: 0 15px 35px rgba(15, 23, 42, 0.08);
            color: #0f172a;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .register-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.12);
        }

        .logo-container {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .logo-wrapper {
            width: 120px;
            height: 120px;
            background: #ffffff;
            border-radius: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 14px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.06);
            border: 1px solid rgba(255, 255, 255, 0.9);
            transition: transform 0.3s ease;
        }

        .logo-wrapper:hover {
            transform: scale(1.05) rotate(3deg);
        }

        .logo-wrapper img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .app-title {
            font-size: 1.7rem;
            font-weight: 700;
            text-align: center;
            margin-top: 10px;
            margin-bottom: 6px;
            letter-spacing: 0.5px;
            color: #0f172a;
        }

        .app-subtitle {
            font-size: 0.9rem;
            color: #64748b;
            text-align: center;
            margin-bottom: 35px;
            font-weight: 400;
        }

        .form-label-custom {
            font-size: 0.85rem;
            font-weight: 500;
            color: #475569;
            margin-bottom: 8px;
            display: block;
        }

        .input-group-custom {
            position: relative;
            margin-bottom: 20px;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            transition: color 0.3s ease;
            font-size: 1.15rem;
            z-index: 10;
        }

        .input-control-custom {
            width: 100%;
            padding: 13px 15px 13px 48px;
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            color: #0f172a;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        .input-control-custom::placeholder {
            color: #94a3b8;
        }

        .input-control-custom:focus {
            background: #ffffff;
            border-color: #3b82f6; /* Blue on focus */
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        .input-control-custom:focus + .input-icon {
            color: #3b82f6;
        }

        .btn-register-custom {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            border: none;
            border-radius: 12px;
            color: #fff;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(29, 78, 216, 0.35);
            margin-top: 15px;
        }

        .btn-register-custom:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(29, 78, 216, 0.5);
            background: linear-gradient(135deg, #60a5fa 0%, #2563eb 100%);
        }

        .btn-register-custom:active {
            transform: translateY(1px);
        }

        .login-link-wrapper {
            text-align: center;
            margin-top: 30px;
            font-size: 0.85rem;
            color: #475569;
        }

        .login-link-wrapper a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .login-link-wrapper a:hover {
            color: #1d4ed8;
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <!-- Fixed Background Decoration Wrapper -->
    <div class="bg-decoration">
        <div class="glow-circle glow-circle-1"></div>
        <div class="glow-circle glow-circle-2"></div>
    </div>

    <div class="register-container">
        <div class="register-card">
            <div class="logo-container">
                <div class="logo-wrapper">
                    <img src="<?= htmlspecialchars(app_settings_asset_url($appSettings, 'company_logo')); ?>" alt="Logo <?= htmlspecialchars($appSettings['app_name']); ?>">
                </div>
            </div>
            
            <h1 class="app-title"><?= htmlspecialchars($appSettings['app_name']); ?></h1>
            <p class="app-subtitle">Buat akun anggota baru Anda</p>
            
            <form action="proses_register.php" method="post">
                <!-- Nama Lengkap -->
                <div class="mb-3">
                    <label class="form-label-custom">Nama Lengkap</label>
                    <div class="input-group-custom">
                        <input type="text" class="input-control-custom" name="nama" placeholder="Masukkan Nama Lengkap" required />
                        <i class="bi bi-person-bounding-box input-icon"></i>
                    </div>
                </div>
                
                <!-- Username -->
                <div class="mb-3">
                    <label class="form-label-custom">Username</label>
                    <div class="input-group-custom">
                        <input type="text" class="input-control-custom" name="username" placeholder="Masukkan Username" required />
                        <i class="bi bi-person-fill input-icon"></i>
                    </div>
                </div>
                
                <!-- Password -->
                <div class="mb-3">
                    <label class="form-label-custom">Password</label>
                    <div class="input-group-custom">
                        <input type="password" class="input-control-custom" name="password" placeholder="Masukkan Password" required />
                        <i class="bi bi-lock-fill input-icon"></i>
                    </div>
                </div>
                
                <!-- Confirm Password -->
                <div class="mb-3">
                    <label class="form-label-custom">Konfirmasi Password</label>
                    <div class="input-group-custom">
                        <input type="password" class="input-control-custom" name="confirm_password" placeholder="Konfirmasi Password" required />
                        <i class="bi bi-shield-lock-fill input-icon"></i>
                    </div>
                </div>
                
                <button type="submit" class="btn-register-custom">Register</button>
                
                <div class="login-link-wrapper">
                    Sudah punya akun? <a href="login.php">Masuk di sini</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?php if (isset($_GET['pesan']) && $_GET['pesan'] === 'gagal'): ?>
        <?php
        $reason = $_GET['reason'] ?? '';
        $registerErrorMessage = 'Registrasi gagal. Silakan coba lagi.';
        if ($reason === 'empty') {
            $registerErrorMessage = 'Semua field wajib diisi.';
        } elseif ($reason === 'password') {
            $registerErrorMessage = 'Password dan konfirmasi password tidak cocok.';
        } elseif ($reason === 'username') {
            $registerErrorMessage = 'Username sudah digunakan. Silakan gunakan username lain.';
        }
        ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            Swal.fire({
                icon: "error",
                title: "Register gagal",
                text: <?= json_encode($registerErrorMessage); ?>,
                confirmButtonText: "Coba Lagi",
                confirmButtonColor: "#3b82f6"
            });
        </script>
    <?php endif; ?>
</body>

</html>
