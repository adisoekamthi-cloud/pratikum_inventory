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

$id = $_GET['id'];

$data = mysqli_query(
    $koneksi,
    "SELECT * FROM status_barang
WHERE id='$id'"
);

$row = mysqli_fetch_assoc($data);

if (isset($_POST['update'])) {

    $status = $_POST['status'];

    mysqli_query($koneksi, "
    UPDATE status_barang
    SET nama_status='$status'
    WHERE id='$id'
    ");

    echo "
    <script>
        alert('Status berhasil diupdate');
        window.location='status.php';
    </script>
    ";
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Status</title>

    <link rel="stylesheet" href="AdminLTE-4.0.0-rc7/dist/css/adminlte.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">

    <div class="container mt-5">

        <div class="row justify-content-center">

            <div class="col-md-5">

                <div class="card card-warning card-outline">

                    <div class="card-header">
                        <h3 class="card-title">
                            Edit Status Barang
                        </h3>
                    </div>

                    <form method="POST">

                        <div class="card-body">

                            <div class="mb-3">

                                <label>Nama Status</label>

                                <input type="text" name="status" class="form-control"
                                    value="<?= $row['nama_status']; ?>" required>

                            </div>

                        </div>

                        <div class="card-footer">

                            <button type="submit" name="update" class="btn btn-warning">

                                Update

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
