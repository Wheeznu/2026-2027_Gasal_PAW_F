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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm d-print-none">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="index.php">Sistem Penjualan</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarUtama"
                    aria-controls="navbarUtama" aria-expanded="false" aria-label="Buka menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarUtama">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>

                    <?php if ($_SESSION['level'] == 1) : ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button"
                               data-bs-toggle="dropdown" aria-expanded="false">
                                Data Master
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="barang.php">Data Barang</a></li>
                                <li><a class="dropdown-item" href="supplier.php">Data Supplier</a></li>
                                <li><a class="dropdown-item" href="pelanggan.php">Data Pelanggan</a></li>
                                <li><a class="dropdown-item" href="user.php">Data User</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item"><a class="nav-link" href="transaksi.php">Transaksi</a></li>
                    <li class="nav-item"><a class="nav-link" href="laporan.php">Laporan</a></li>
                </ul>

                <div class="d-flex align-items-center gap-3">
                    <span class="navbar-text text-white">
                        <?= htmlspecialchars($_SESSION['nama']); ?>
                        (<?= $_SESSION['level'] == 1 ? 'Owner' : 'Kasir'; ?>)
                    </span>
                    <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="container my-4">
