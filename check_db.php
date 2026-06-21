<?php
include 'koneksi.php';

// Check if foto column exists
$result = mysqli_query($koneksi, "SHOW COLUMNS FROM users LIKE 'foto'");
$foto_exists = mysqli_num_rows($result) > 0;

if (!$foto_exists) {
    echo "Kolom 'foto' belum ada. Menambahkan kolom...\n";
    $alter_query = "ALTER TABLE users ADD COLUMN foto VARCHAR(255) DEFAULT 'safii.jpg' AFTER email";
    if (mysqli_query($koneksi, $alter_query)) {
        echo "✓ Kolom 'foto' berhasil ditambahkan!\n";
    } else {
        echo "✗ Gagal menambahkan kolom: " . mysqli_error($koneksi) . "\n";
    }
} else {
    echo "✓ Kolom 'foto' sudah ada!\n";
}

// Display all columns
echo "\nStruktur tabel users:\n";
$desc = mysqli_query($koneksi, "DESC users");
while ($row = mysqli_fetch_assoc($desc)) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
?>
