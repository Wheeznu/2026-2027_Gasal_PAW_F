<?php
// ===== transaksi.php — Wisnu Aji Saka Pratama Putra (25-099) =====
// Kelola data transaksi: list, tambah, edit, hapus
include 'koneksi.php';

$aksi = $_GET['aksi'] ?? 'list';
$id   = (int) ($_GET['id'] ?? 0);

// --- HAPUS ---
if ($aksi === 'hapus' && $id > 0) {
    // detail rows cascade-delete via FK
    mysqli_query($conn, "DELETE FROM transaksi WHERE id = $id");
    header("Location: transaksi.php");
    exit;
}

// --- SIMPAN TAMBAH ---
if ($aksi === 'tambah' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $waktu     = mysqli_real_escape_string($conn, $_POST['waktu_transaksi']);
    $ket       = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $pelanggan = mysqli_real_escape_string($conn, $_POST['pelanggan_id']);
    mysqli_query($conn, "INSERT INTO transaksi (waktu_transaksi, keterangan, total, pelanggan_id)
                         VALUES ('$waktu', '$ket', 0, '$pelanggan')");
    header("Location: transaksi.php");
    exit;
}

// --- SIMPAN EDIT ---
if ($aksi === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id        = (int) $_POST['id'];
    $waktu     = mysqli_real_escape_string($conn, $_POST['waktu_transaksi']);
    $ket       = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $pelanggan = mysqli_real_escape_string($conn, $_POST['pelanggan_id']);
    mysqli_query($conn, "UPDATE transaksi SET waktu_transaksi='$waktu', keterangan='$ket', pelanggan_id='$pelanggan' WHERE id=$id");
    header("Location: transaksi.php");
    exit;
}

// --- Ambil data untuk form edit ---
$data_edit = null;
if ($aksi === 'edit' && $id > 0) {
    $data_edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM transaksi WHERE id=$id"));
    if (!$data_edit) { header("Location: transaksi.php"); exit; }
}

// --- Data list transaksi ---
$result         = mysqli_query($conn, "SELECT t.*, p.nama AS nama_pelanggan FROM transaksi t
                                       JOIN pelanggan p ON t.pelanggan_id = p.id ORDER BY t.id ASC");
$pelanggan_list = mysqli_query($conn, "SELECT id, nama FROM pelanggan ORDER BY nama ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Transaksi</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <a href="barang.php">Barang</a>
        <a href="transaksi.php">Transaksi</a>
    </nav>

<?php if ($aksi === 'tambah') : ?>
    <!-- ===== FORM TAMBAH TRANSAKSI ===== -->
    <h2>Tambah Transaksi</h2>
    <form method="POST" action="transaksi.php?aksi=tambah">
        <div class="form-group">
            <label>Tanggal</label>
            <input type="date" name="waktu_transaksi" required>
        </div>
        <div class="form-group">
            <label>Pelanggan</label>
            <select name="pelanggan_id" required>
                <option value="">-- Pilih Pelanggan --</option>
                <?php while ($p = mysqli_fetch_assoc($pelanggan_list)) : ?>
                <option value="<?= $p['id']; ?>"><?= htmlspecialchars($p['nama']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Keterangan</label>
            <textarea name="keterangan"></textarea>
        </div>
        <button type="submit" class="btn btn-tambah">Simpan</button>
        <a href="transaksi.php" class="btn btn-kembali">Kembali</a>
    </form>

<?php elseif ($aksi === 'edit' && $data_edit) : ?>
    <!-- ===== FORM EDIT TRANSAKSI ===== -->
    <h2>Edit Transaksi</h2>
    <form method="POST" action="transaksi.php?aksi=edit">
        <input type="hidden" name="id" value="<?= $data_edit['id']; ?>">
        <div class="form-group">
            <label>Tanggal</label>
            <input type="date" name="waktu_transaksi" value="<?= $data_edit['waktu_transaksi']; ?>" required>
        </div>
        <div class="form-group">
            <label>Pelanggan</label>
            <select name="pelanggan_id" required>
                <option value="">-- Pilih Pelanggan --</option>
                <?php while ($p = mysqli_fetch_assoc($pelanggan_list)) : ?>
                <option value="<?= $p['id']; ?>" <?= $p['id'] == $data_edit['pelanggan_id'] ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($p['nama']); ?>
                </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Keterangan</label>
            <textarea name="keterangan"><?= htmlspecialchars($data_edit['keterangan']); ?></textarea>
        </div>
        <button type="submit" class="btn btn-tambah">Update</button>
        <a href="transaksi.php" class="btn btn-kembali">Kembali</a>
    </form>

<?php else : ?>
    <!-- ===== LIST TRANSAKSI ===== -->
    <h2>Data Transaksi</h2>
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
                <td><?= $row['waktu_transaksi']; ?></td>
                <td><?= htmlspecialchars($row['nama_pelanggan']); ?></td>
                <td><?= htmlspecialchars($row['keterangan']); ?></td>
                <td>Rp <?= number_format($row['total'], 0, ',', '.'); ?></td>
                <td>
                    <a href="detail.php?transaksi_id=<?= $row['id']; ?>" class="btn btn-detail">Detail</a>
                    <a href="transaksi.php?aksi=edit&id=<?= $row['id']; ?>" class="btn btn-edit">Edit</a>
                    <a href="transaksi.php?aksi=hapus&id=<?= $row['id']; ?>" class="btn btn-hapus" onclick="return confirm('Hapus transaksi ini?');">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php endif; ?>
</body>
</html>
