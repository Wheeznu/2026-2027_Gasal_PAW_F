<?php
include 'cek_login.php';

$judul = "Home";
include 'header.php';
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
            <li><a href="barang.php">Data Barang</a></li>
            <li><a href="supplier.php">Data Supplier</a></li>
            <li><a href="pelanggan.php">Data Pelanggan</a></li>
            <li><a href="user.php">Data User</a></li>
        <?php endif; ?>

        <li><a href="transaksi.php">Transaksi</a></li>
        <li><a href="laporan.php">Laporan</a></li>
    </ul>
</div>

<?php include 'footer.php'; ?>
