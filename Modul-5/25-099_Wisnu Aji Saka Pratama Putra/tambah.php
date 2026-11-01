<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $telp = mysqli_real_escape_string($conn, $_POST['telp']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);

    mysqli_query($conn, "INSERT INTO supplier (nama, telp, alamat) VALUES ('$nama', '$telp', '$alamat')");
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Supplier</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Tambah Data Supplier</h2>

    <form action="tambah.php" method="POST">
        <div class="form-group">
            <label for="nama">Nama Supplier</label>
            <input type="text" id="nama" name="nama" required>
        </div>
        <div class="form-group">
            <label for="telp">No. Telepon</label>
            <input type="text" id="telp" name="telp" required>
        </div>
        <div class="form-group">
            <label for="alamat">Alamat</label>
            <textarea id="alamat" name="alamat" required></textarea>
        </div>
        <button type="submit" class="btn btn-tambah">Simpan</button>
        <a href="index.php" class="btn btn-kembali">Kembali</a>
    </form>
</body>
</html>
