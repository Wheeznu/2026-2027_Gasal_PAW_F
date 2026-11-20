<?php
include 'cek_login.php';

$judul = "Home";
include 'layout.php';
tampil_header($judul);
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h1 class="h3 mb-2">Selamat Datang, <?= htmlspecialchars($_SESSION['nama']); ?></h1>
                <p class="text-muted mb-0">
                    Anda login sebagai
                    <span class="badge text-bg-primary"><?= $_SESSION['level'] == 1 ? 'Owner' : 'Kasir'; ?></span>
                </p>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white fw-semibold">Menu</div>
            <div class="list-group list-group-flush">
                <?php if ($_SESSION['level'] == 1) : ?>
                    <a href="barang.php" class="list-group-item list-group-item-action">Data Barang</a>
                    <a href="supplier.php" class="list-group-item list-group-item-action">Data Supplier</a>
                    <a href="pelanggan.php" class="list-group-item list-group-item-action">Data Pelanggan</a>
                    <a href="user.php" class="list-group-item list-group-item-action">Data User</a>
                <?php endif; ?>

                <a href="transaksi.php" class="list-group-item list-group-item-action">Transaksi</a>
                <a href="laporan.php" class="list-group-item list-group-item-action">Laporan</a>
            </div>
        </div>
    </div>
</div>

<?php tampil_footer(); ?>
