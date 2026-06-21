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
    $_SESSION['flash_status_message'] = 'ID status tidak valid.';
    $_SESSION['flash_status_type'] = 'error';
    header('Location: status.php');
    exit;
}

$delete_status = mysqli_query(
    $koneksi,
    "DELETE FROM status_barang WHERE id='$id'"
);

if ($delete_status) {
    $_SESSION['flash_status_message'] = 'Status berhasil dihapus.';
    $_SESSION['flash_status_type'] = 'success';
} else {
    $_SESSION['flash_status_message'] = 'Gagal menghapus status.';
    $_SESSION['flash_status_type'] = 'error';
}

header('Location: status.php');
exit;

