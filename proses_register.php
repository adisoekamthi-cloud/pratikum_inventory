<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit;
}

include 'koneksi.php';

$nama = mysqli_real_escape_string($koneksi, trim($_POST['nama'] ?? ''));
$username = mysqli_real_escape_string($koneksi, trim($_POST['username'] ?? ''));
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

if ($nama === '' || $username === '' || $password === '' || $confirmPassword === '') {
    header('Location: register.php?pesan=gagal&reason=empty');
    exit;
}

if ($password !== $confirmPassword) {
    header('Location: register.php?pesan=gagal&reason=password');
    exit;
}

$checkUsername = mysqli_query($koneksi, "SELECT id FROM users WHERE username = '$username' LIMIT 1");
if ($checkUsername && mysqli_num_rows($checkUsername) > 0) {
    header('Location: register.php?pesan=gagal&reason=username');
    exit;
}

$passwordHash = md5($password);
$query = mysqli_query(
    $koneksi,
    "INSERT INTO users (nama_lengkap, username, password, role)
     VALUES ('$nama', '$username', '$passwordHash', 'pengguna')"
);

if ($query) {
    header('Location: login.php?pesan=register_berhasil');
    exit;
}

header('Location: register.php?pesan=gagal&reason=server');
exit;
