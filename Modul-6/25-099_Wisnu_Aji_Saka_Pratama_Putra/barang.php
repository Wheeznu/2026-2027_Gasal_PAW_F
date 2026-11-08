<?php
// ===== barang.php — Wisnu Aji Saka Pratama Putra (25-099) =====
// Kelola data barang: list, tambah, edit, hapus
include 'koneksi.php';

$aksi = $_GET['aksi'] ?? 'list';
$id   = (int) ($_GET['id'] ?? 0);

// --- HAPUS ---
if ($aksi === 'hapus' && $id > 0) {
    $cek = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jml FROM transaksi_detail WHERE barang_id = $id"));
    if ($cek['jml'] > 0) {
        header("Location: barang.php?error=Barang+masih+digunakan+pada+transaksi,+tidak+bisa+dihapus.");
    } else {
        mysqli_query($conn, "DELETE FROM barang WHERE id = $id");
        header("Location: barang.php");
    }
    exit;
}

// --- SIMPAN TAMBAH ---
if ($aksi === 'tambah' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode        = mysqli_real_escape_string($conn, $_POST['kode_barang']);
    $nama        = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $harga       = (int) $_POST['harga'];
    $stok        = (int) $_POST['stok'];
    $supplier_id = (int) $_POST['supplier_id'];
    mysqli_query($conn, "INSERT INTO barang (kode_barang, nama_barang, harga, stok, supplier_id)
                         VALUES ('$kode', '$nama', $harga, $stok, $supplier_id)");
    header("Location: barang.php");
    exit;
}

// --- SIMPAN EDIT ---
if ($aksi === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id          = (int) $_POST['id'];
    $kode        = mysqli_real_escape_string($conn, $_POST['kode_barang']);
    $nama        = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $harga       = (int) $_POST['harga'];
    $stok        = (int) $_POST['stok'];
    $supplier_id = (int) $_POST['supplier_id'];
    mysqli_query($conn, "UPDATE barang SET kode_barang='$kode', nama_barang='$nama',
                         harga=$harga, stok=$stok, supplier_id=$supplier_id WHERE id=$id");
    header("Location: barang.php");
    exit;
}

// --- Ambil data untuk form edit ---
$data_edit = null;
if ($aksi === 'edit' && $id > 0) {
    $data_edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM barang WHERE id = $id"));
    if (!$data_edit) { header("Location: barang.php"); exit; }
}

// --- Data list barang ---
$result    = mysqli_query($conn, "SELECT b.*, s.nama AS nama_supplier FROM barang b
                                  JOIN supplier s ON b.supplier_id = s.id ORDER BY b.id ASC");
$suppliers = mysqli_query($conn, "SELECT id, nama FROM supplier ORDER BY nama ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Barang</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <a href="barang.php">Barang</a>
        <a href="transaksi.php">Transaksi</a>
    </nav>

<?php if ($aksi === 'tambah') : ?>
    <!-- ===== FORM TAMBAH BARANG ===== -->
    <h2>Tambah Data Barang</h2>
    <form action="barang.php?aksi=tambah" method="POST">
        <div class="form-group">
            <label for="kode_barang">Kode Barang</label>
            <input type="text" id="kode_barang" name="kode_barang" required>
        </div>
        <div class="form-group">
            <label for="nama_barang">Nama Barang</label>
            <input type="text" id="nama_barang" name="nama_barang" required>
        </div>
        <div class="form-group">
            <label for="harga">Harga</label>
            <input type="number" id="harga" name="harga" min="0" required>
        </div>
        <div class="form-group">
            <label for="stok">Stok</label>
            <input type="number" id="stok" name="stok" min="0" required>
        </div>
        <div class="form-group">
            <label for="supplier_id">Supplier</label>
            <select id="supplier_id" name="supplier_id" required>
                <option value="">-- Pilih Supplier --</option>
                <?php while ($s = mysqli_fetch_assoc($suppliers)) : ?>
                <option value="<?= $s['id']; ?>"><?= htmlspecialchars($s['nama']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-tambah">Simpan</button>
        <a href="barang.php" class="btn btn-kembali">Kembali</a>
    </form>

<?php elseif ($aksi === 'edit' && $data_edit) : ?>
    <!-- ===== FORM EDIT BARANG ===== -->
    <h2>Edit Data Barang</h2>
    <form action="barang.php?aksi=edit" method="POST">
        <input type="hidden" name="id" value="<?= $data_edit['id']; ?>">
        <div class="form-group">
            <label for="kode_barang">Kode Barang</label>
            <input type="text" id="kode_barang" name="kode_barang" value="<?= htmlspecialchars($data_edit['kode_barang']); ?>" required>
        </div>
        <div class="form-group">
            <label for="nama_barang">Nama Barang</label>
            <input type="text" id="nama_barang" name="nama_barang" value="<?= htmlspecialchars($data_edit['nama_barang']); ?>" required>
        </div>
        <div class="form-group">
            <label for="harga">Harga</label>
            <input type="number" id="harga" name="harga" value="<?= $data_edit['harga']; ?>" min="0" required>
        </div>
        <div class="form-group">
            <label for="stok">Stok</label>
            <input type="number" id="stok" name="stok" value="<?= $data_edit['stok']; ?>" min="0" required>
        </div>
        <div class="form-group">
            <label for="supplier_id">Supplier</label>
            <select id="supplier_id" name="supplier_id" required>
                <option value="">-- Pilih Supplier --</option>
                <?php while ($s = mysqli_fetch_assoc($suppliers)) : ?>
                <option value="<?= $s['id']; ?>" <?= $s['id'] == $data_edit['supplier_id'] ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($s['nama']); ?>
                </option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-tambah">Update</button>
        <a href="barang.php" class="btn btn-kembali">Kembali</a>
    </form>

<?php else : ?>
    <!-- ===== LIST BARANG ===== -->
    <h2>Data Barang</h2>
    <?php if (!empty($_GET['error'])) : ?>
        <div class="alert-danger"><?= htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>
    <a href="barang.php?aksi=tambah" class="btn btn-tambah">+ Tambah Data</a>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Supplier</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; while ($row = mysqli_fetch_assoc($result)) : ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= htmlspecialchars($row['kode_barang']); ?></td>
                <td><?= htmlspecialchars($row['nama_barang']); ?></td>
                <td>Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
                <td><?= $row['stok']; ?></td>
                <td><?= htmlspecialchars($row['nama_supplier']); ?></td>
                <td>
                    <a href="barang.php?aksi=edit&id=<?= $row['id']; ?>" class="btn btn-edit">Edit</a>
                    <a href="barang.php?aksi=hapus&id=<?= $row['id']; ?>" class="btn btn-hapus" onclick="return confirm('Hapus barang ini?');">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php endif; ?>
</body>
</html>
