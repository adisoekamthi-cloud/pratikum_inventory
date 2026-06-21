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
    $_SESSION['flash_index_message'] = 'ID barang tidak valid.';
    $_SESSION['flash_index_type'] = 'error';
} else {
    $delete = mysqli_query($koneksi, "DELETE FROM barang WHERE id='$id'");
    if ($delete) {
        $_SESSION['flash_index_message'] = 'Barang berhasil dihapus.';
        $_SESSION['flash_index_type'] = 'success';
    } else {
        $_SESSION['flash_index_message'] = 'Gagal menghapus barang.';
        $_SESSION['flash_index_type'] = 'error';
    }
}

header("Location: index.php");
exit;
