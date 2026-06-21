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
require_once __DIR__ . '/includes/app_settings.php';

$nama_user = htmlspecialchars($_SESSION['nama_lengkap']);
$username  = htmlspecialchars($_SESSION['username']);
$role      = ucfirst($_SESSION['role']);

$settings = app_settings_load();
[$settingsMessage, $settingsMessageType] = app_settings_handle_submit($settings);

$pageTitle = 'Setting | ' . $settings['app_name'];
$activePage = 'setting';
$assetPrefix = '';
include __DIR__ . '/components/head.php';
include __DIR__ . '/components/topbar.php';
include __DIR__ . '/components/sidebar.php';
include __DIR__ . '/includes/app_settings_content.php';

$additionalScripts = '';
if (!empty($settingsMessage)) {
    $swalIcon = $settingsMessageType === 'success' ? 'success' : 'error';
    $swalTitle = $settingsMessageType === 'success' ? 'Berhasil' : 'Gagal';
    $additionalScripts = '
<script>
    Swal.fire({
        icon: ' . json_encode($swalIcon) . ',
        title: ' . json_encode($swalTitle) . ',
        html: ' . json_encode($settingsMessage) . ',
        confirmButtonText: "OK"
    });
</script>';
}
include __DIR__ . '/components/footer.php';

