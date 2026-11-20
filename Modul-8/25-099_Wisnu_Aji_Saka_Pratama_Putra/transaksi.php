<?php
include 'cek_login.php';
include 'koneksi.php';

$aksi  = $_GET['aksi'] ?? 'list';
$id    = (int) ($_GET['id'] ?? 0);
$error = "";

// ===== HAPUS DATA =====
if ($aksi === 'hapus' && $id > 0) {
    // Detail transaksi ikut terhapus karena foreign key ON DELETE CASCADE.
    mysqli_query($conn, "DELETE FROM transaksi WHERE id = $id");
    header("Location: transaksi.php?pesan=" . urlencode("Data transaksi berhasil dihapus."));
    exit;
}

// ===== SIMPAN TAMBAH =====
if ($aksi === 'tambah' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $waktu     = mysqli_real_escape_string($conn, trim($_POST['waktu_transaksi'] ?? ''));
    $keterangan = mysqli_real_escape_string($conn, trim($_POST['keterangan'] ?? ''));
    $pelanggan = mysqli_real_escape_string($conn, $_POST['pelanggan_id'] ?? '');

    if ($waktu === "" || $pelanggan === "") {
        $error = "Tanggal dan pelanggan wajib diisi.";
    } else {
        mysqli_query($conn, "INSERT INTO transaksi (waktu_transaksi, keterangan, total, pelanggan_id)
                             VALUES ('$waktu', '$keterangan', 0, '$pelanggan')");

        $id_baru = mysqli_insert_id($conn);
        header("Location: detail.php?transaksi_id=$id_baru&pesan=" . urlencode("Transaksi berhasil dibuat. Silakan tambahkan barang."));
        exit;
    }
}

// ===== SIMPAN EDIT =====
if ($aksi === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id         = (int) ($_POST['id'] ?? 0);
    $waktu      = mysqli_real_escape_string($conn, trim($_POST['waktu_transaksi'] ?? ''));
    $keterangan = mysqli_real_escape_string($conn, trim($_POST['keterangan'] ?? ''));
    $pelanggan  = mysqli_real_escape_string($conn, $_POST['pelanggan_id'] ?? '');

    if ($waktu === "" || $pelanggan === "") {
        $error = "Tanggal dan pelanggan wajib diisi.";
    } else {
        mysqli_query($conn, "UPDATE transaksi SET
                             waktu_transaksi = '$waktu',
                             keterangan = '$keterangan',
                             pelanggan_id = '$pelanggan'
                             WHERE id = $id");
        header("Location: transaksi.php?pesan=" . urlencode("Data transaksi berhasil diubah."));
        exit;
    }
}

// ===== DATA UNTUK FORM EDIT =====
$data_edit = null;
if ($aksi === 'edit' && $id > 0) {
    $data_edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM transaksi WHERE id = $id"));

    if (!$data_edit) {
        header("Location: transaksi.php");
        exit;
    }
}

// ===== DATA LIST =====
$result = mysqli_query($conn, "SELECT t.*, p.nama AS nama_pelanggan
                               FROM transaksi t
                               JOIN pelanggan p ON t.pelanggan_id = p.id
                               ORDER BY t.id ASC");
$pelanggan_list = mysqli_query($conn, "SELECT id, nama FROM pelanggan ORDER BY nama ASC");

$judul = "Data Transaksi";
include 'layout.php';
tampil_header($judul);
?>

<?php if (!empty($_GET['pesan'])) : ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['pesan']); ?></div>
<?php endif; ?>

<?php if ($aksi === 'tambah') : ?>
    <h1 class="h3 mb-3">Tambah Transaksi</h1>

    <?php if ($error !== "") : ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="transaksi.php?aksi=tambah">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="waktu_transaksi" class="form-label">Tanggal</label>
                        <input type="date" class="form-control" id="waktu_transaksi" name="waktu_transaksi"
                               value="<?= htmlspecialchars($_POST['waktu_transaksi'] ?? date('Y-m-d')); ?>" required>
                    </div>

                    <div class="col-md-8">
                        <label for="pelanggan_id" class="form-label">Pelanggan</label>
                        <select class="form-select" id="pelanggan_id" name="pelanggan_id" required>
                            <option value="">-- Pilih Pelanggan --</option>
                            <?php while ($p = mysqli_fetch_assoc($pelanggan_list)) : ?>
                                <option value="<?= htmlspecialchars($p['id']); ?>"
                                    <?= (isset($_POST['pelanggan_id']) && $_POST['pelanggan_id'] === $p['id']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($p['nama']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-12">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="2"><?= htmlspecialchars($_POST['keterangan'] ?? ''); ?></textarea>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success">Simpan</button>
                    <a href="transaksi.php" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>

<?php elseif ($aksi === 'edit' && $data_edit) : ?>
    <h1 class="h3 mb-3">Edit Transaksi</h1>

    <?php if ($error !== "") : ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="transaksi.php?aksi=edit">
                <input type="hidden" name="id" value="<?= $data_edit['id']; ?>">

                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="waktu_transaksi" class="form-label">Tanggal</label>
                        <input type="date" class="form-control" id="waktu_transaksi" name="waktu_transaksi"
                               value="<?= htmlspecialchars($_POST['waktu_transaksi'] ?? $data_edit['waktu_transaksi']); ?>" required>
                    </div>

                    <div class="col-md-8">
                        <label for="pelanggan_id" class="form-label">Pelanggan</label>
                        <?php $pelanggan_terpilih = $_POST['pelanggan_id'] ?? $data_edit['pelanggan_id']; ?>
                        <select class="form-select" id="pelanggan_id" name="pelanggan_id" required>
                            <option value="">-- Pilih Pelanggan --</option>
                            <?php while ($p = mysqli_fetch_assoc($pelanggan_list)) : ?>
                                <option value="<?= htmlspecialchars($p['id']); ?>"
                                    <?= $pelanggan_terpilih === $p['id'] ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($p['nama']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-12">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="2"><?= htmlspecialchars($_POST['keterangan'] ?? $data_edit['keterangan']); ?></textarea>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success">Update</button>
                    <a href="transaksi.php" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>

<?php else : ?>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Data Transaksi</h1>
        <a href="transaksi.php?aksi=tambah" class="btn btn-primary">+ Tambah Transaksi</a>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Keterangan</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($result)) : ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($row['waktu_transaksi']); ?></td>
                            <td><?= htmlspecialchars($row['nama_pelanggan']); ?></td>
                            <td><?= htmlspecialchars($row['keterangan']); ?></td>
                            <td>Rp <?= number_format($row['total'], 0, ',', '.'); ?></td>
                            <td class="text-nowrap">
                                <a href="detail.php?transaksi_id=<?= $row['id']; ?>" class="btn btn-sm btn-info">Detail</a>
                                <a href="transaksi.php?aksi=edit&id=<?= $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="transaksi.php?aksi=hapus&id=<?= $row['id']; ?>" class="btn btn-sm btn-danger"
                                   onclick="return confirm('Hapus transaksi ini?');">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php tampil_footer(); ?>
