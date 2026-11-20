<?php
include __DIR__ . '/../auth/cek_login.php';

$judul = "Home";
include __DIR__ . '/header.php';
?>

<h1>Selamat Datang, <?= htmlspecialchars($_SESSION['nama']); ?></h1>

<p>
    Anda login sebagai
    <strong><?= $_SESSION['level'] == 1 ? 'Owner' : 'Kasir'; ?></strong>.
</p>

<div class="menu-box">
    <h2>Menu</h2>
    <ul>
        <?php if ($_SESSION['level'] == 1) : ?>
            <li><a href="../crud/barang.php">Data Barang</a></li>
            <li><a href="../crud/supplier.php">Data Supplier</a></li>
            <li><a href="../crud/pelanggan.php">Data Pelanggan</a></li>
            <li><a href="../crud/user.php">Data User</a></li>
        <?php endif; ?>

        <li><a href="../transaksi/transaksi.php">Transaksi</a></li>
        <li><a href="../laporan/laporan.php">Laporan</a></li>
    </ul>
</div>

<?php include __DIR__ . '/footer.php'; ?>
