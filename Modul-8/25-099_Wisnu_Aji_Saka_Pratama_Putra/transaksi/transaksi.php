<?php
include __DIR__ . '/../auth/cek_login.php';
include __DIR__ . '/../layout/koneksi.php';

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
include __DIR__ . '/../layout/header.php';
?>

<?php if (!empty($_GET['pesan'])) : ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['pesan']); ?></div>
<?php endif; ?>

<?php if ($aksi === 'tambah') : ?>
    <h1>Tambah Transaksi</h1>

    <?php if ($error !== "") : ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="transaksi.php?aksi=tambah">
        <div class="form-group">
            <label for="waktu_transaksi">Tanggal</label>
            <input type="date" id="waktu_transaksi" name="waktu_transaksi"
                   value="<?= htmlspecialchars($_POST['waktu_transaksi'] ?? date('Y-m-d')); ?>" required>
        </div>

        <div class="form-group">
            <label for="pelanggan_id">Pelanggan</label>
            <select id="pelanggan_id" name="pelanggan_id" required>
                <option value="">-- Pilih Pelanggan --</option>
                <?php while ($p = mysqli_fetch_assoc($pelanggan_list)) : ?>
                    <option value="<?= htmlspecialchars($p['id']); ?>"
                        <?= (isset($_POST['pelanggan_id']) && $_POST['pelanggan_id'] === $p['id']) ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($p['nama']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="keterangan">Keterangan</label>
            <textarea id="keterangan" name="keterangan"><?= htmlspecialchars($_POST['keterangan'] ?? ''); ?></textarea>
        </div>

        <button type="submit" class="btn btn-tambah">Simpan</button>
        <a href="transaksi.php" class="btn btn-kembali">Kembali</a>
    </form>

<?php elseif ($aksi === 'edit' && $data_edit) : ?>
    <h1>Edit Transaksi</h1>

    <?php if ($error !== "") : ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="transaksi.php?aksi=edit">
        <input type="hidden" name="id" value="<?= $data_edit['id']; ?>">

        <div class="form-group">
            <label for="waktu_transaksi">Tanggal</label>
            <input type="date" id="waktu_transaksi" name="waktu_transaksi"
                   value="<?= htmlspecialchars($_POST['waktu_transaksi'] ?? $data_edit['waktu_transaksi']); ?>" required>
        </div>

        <div class="form-group">
            <label for="pelanggan_id">Pelanggan</label>
            <?php $pelanggan_terpilih = $_POST['pelanggan_id'] ?? $data_edit['pelanggan_id']; ?>
            <select id="pelanggan_id" name="pelanggan_id" required>
                <option value="">-- Pilih Pelanggan --</option>
                <?php while ($p = mysqli_fetch_assoc($pelanggan_list)) : ?>
                    <option value="<?= htmlspecialchars($p['id']); ?>"
                        <?= $pelanggan_terpilih === $p['id'] ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($p['nama']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="keterangan">Keterangan</label>
            <textarea id="keterangan" name="keterangan"><?= htmlspecialchars($_POST['keterangan'] ?? $data_edit['keterangan']); ?></textarea>
        </div>

        <button type="submit" class="btn btn-tambah">Update</button>
        <a href="transaksi.php" class="btn btn-kembali">Kembali</a>
    </form>

<?php else : ?>
    <h1>Data Transaksi</h1>

    <a href="transaksi.php?aksi=tambah" class="btn btn-tambah">+ Tambah Transaksi</a>

    <table>
        <thead>
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
                    <td>
                        <a href="detail.php?transaksi_id=<?= $row['id']; ?>" class="btn btn-detail">Detail</a>
                        <a href="transaksi.php?aksi=edit&id=<?= $row['id']; ?>" class="btn btn-edit">Edit</a>
                        <a href="transaksi.php?aksi=hapus&id=<?= $row['id']; ?>" class="btn btn-hapus"
                           onclick="return confirm('Hapus transaksi ini?');">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include __DIR__ . '/../layout/footer.php'; ?>
