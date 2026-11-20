<?php
// Variabel $judul diisi di halaman sebelum memanggil header ini.
if (!isset($judul)) {
    $judul = "Sistem Penjualan";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($judul); ?> - Sistem Penjualan</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand">
            <a href="../layout/index.php">Sistem Penjualan</a>
        </div>

        <ul class="navbar-menu">
            <li><a href="../layout/index.php">Home</a></li>

            <?php if ($_SESSION['level'] == 1) : ?>
                <li class="dropdown">
                    <a href="#">Data Master</a>
                    <ul class="dropdown-menu">
                        <li><a href="../crud/barang.php">Data Barang</a></li>
                        <li><a href="../crud/supplier.php">Data Supplier</a></li>
                        <li><a href="../crud/pelanggan.php">Data Pelanggan</a></li>
                        <li><a href="../crud/user.php">Data User</a></li>
                    </ul>
                </li>
            <?php endif; ?>

            <li><a href="../transaksi/transaksi.php">Transaksi</a></li>
            <li><a href="../laporan/laporan.php">Laporan</a></li>
        </ul>

        <div class="navbar-user">
            <span>
                <?= htmlspecialchars($_SESSION['nama']); ?>
                (<?= $_SESSION['level'] == 1 ? 'Owner' : 'Kasir'; ?>)
            </span>
            <a href="../auth/logout.php" class="btn-logout">Logout</a>
        </div>
    </nav>

    <main class="container">
