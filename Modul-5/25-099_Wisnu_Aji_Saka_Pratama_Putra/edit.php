<?php
include 'koneksi.php';

$id = (int) ($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) $_POST['id'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $telp = mysqli_real_escape_string($conn, $_POST['telp']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);

    mysqli_query($conn, "UPDATE supplier SET nama = '$nama', telp = '$telp', alamat = '$alamat' WHERE id = $id");
    header("Location: index.php");
    exit;
}

$query = mysqli_query($conn, "SELECT * FROM supplier WHERE id = $id");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Supplier</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h2>Edit Data Supplier</h2>

    <form action="edit.php" method="POST">
        <input type="hidden" name="id" value="<?= $data['id']; ?>">

        <div class="form-group">
            <label for="nama">Nama Supplier</label>
            <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($data['nama']); ?>" required>
        </div>
        <div class="form-group">
            <label for="telp">No. Telepon</label>
            <input type="text" id="telp" name="telp" value="<?= htmlspecialchars($data['telp']); ?>" required>
        </div>
        <div class="form-group">
            <label for="alamat">Alamat</label>
            <textarea id="alamat" name="alamat" required><?= htmlspecialchars($data['alamat']); ?></textarea>
        </div>
        <button type="submit" class="btn btn-tambah">Update</button>
        <a href="index.php" class="btn btn-kembali">Kembali</a>
    </form>
</body>

</html>