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

if (isset($_POST['simpan'])) {

    $status = $_POST['status'];

    mysqli_query(
        $koneksi,
        "INSERT INTO status_barang(nama_status)
    VALUES('$status')"
    );

    echo "
    <script>
        alert('Status berhasil ditambahkan');
        window.location='status.php';
    </script>
    ";
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Tambah Status</title>

    <link rel="stylesheet" href="AdminLTE-4.0.0-rc7/dist/css/adminlte.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">

    <div class="container mt-5">

        <div class="row justify-content-center">

            <div class="col-md-5">

                <div class="card card-success card-outline">

                    <div class="card-header">
                        <h3 class="card-title">
                            Tambah Status Barang
                        </h3>
                    </div>

                    <form method="POST">

                        <div class="card-body">

                            <div class="mb-3">

                                <label>Nama Status</label>

                                <input type="text" name="status" class="form-control" required>

                            </div>

                        </div>

                        <div class="card-footer">

                            <button type="submit" name="simpan" class="btn btn-success">

                                Simpan

                            </button>

                            <a href="status.php" class="btn btn-secondary">

                                Kembali

                            </a>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</body>

</html>
