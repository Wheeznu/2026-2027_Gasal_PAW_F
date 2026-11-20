<?php
include __DIR__ . '/../auth/cek_owner.php';
include __DIR__ . '/../layout/koneksi.php';

$aksi  = $_GET['aksi'] ?? 'list';
$id    = (int) ($_GET['id'] ?? 0);
$error = "";

// ===== HAPUS DATA =====
if ($aksi === 'hapus' && $id > 0) {
    $cek = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jumlah
                                                   FROM transaksi_detail
                                                   WHERE barang_id = $id"));

    if ($cek['jumlah'] > 0) {
        header("Location: barang.php?error=" . urlencode("Barang tidak bisa dihapus karena sudah dipakai pada transaksi."));
    } else {
        mysqli_query($conn, "DELETE FROM barang WHERE id = $id");
        header("Location: barang.php?pesan=" . urlencode("Data barang berhasil dihapus."));
    }
    exit;
}

// ===== SIMPAN TAMBAH =====
if ($aksi === 'tambah' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode        = mysqli_real_escape_string($conn, trim($_POST['kode_barang'] ?? ''));
    $nama        = mysqli_real_escape_string($conn, trim($_POST['nama_barang'] ?? ''));
    $harga       = (int) ($_POST['harga'] ?? 0);
    $stok        = (int) ($_POST['stok'] ?? 0);
    $supplier_id = (int) ($_POST['supplier_id'] ?? 0);

    if ($kode === "" || $nama === "" || $harga < 0 || $stok < 0 || $supplier_id <= 0) {
        $error = "Semua data wajib diisi dengan benar.";
    } else {
        $cek = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM barang WHERE kode_barang = '$kode'"));

        if ($cek) {
            $error = "Kode barang sudah digunakan.";
        } else {
            mysqli_query($conn, "INSERT INTO barang (kode_barang, nama_barang, harga, stok, supplier_id)
                                 VALUES ('$kode', '$nama', $harga, $stok, $supplier_id)");
            header("Location: barang.php?pesan=" . urlencode("Data barang berhasil ditambahkan."));
            exit;
        }
    }
}

// ===== SIMPAN EDIT =====
if ($aksi === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id          = (int) ($_POST['id'] ?? 0);
    $kode        = mysqli_real_escape_string($conn, trim($_POST['kode_barang'] ?? ''));
    $nama        = mysqli_real_escape_string($conn, trim($_POST['nama_barang'] ?? ''));
    $harga       = (int) ($_POST['harga'] ?? 0);
    $stok        = (int) ($_POST['stok'] ?? 0);
    $supplier_id = (int) ($_POST['supplier_id'] ?? 0);

    if ($kode === "" || $nama === "" || $harga < 0 || $stok < 0 || $supplier_id <= 0) {
        $error = "Semua data wajib diisi dengan benar.";
    } else {
        $cek = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM barang
                                                       WHERE kode_barang = '$kode'
                                                       AND id != $id"));

        if ($cek) {
            $error = "Kode barang sudah digunakan.";
        } else {
            mysqli_query($conn, "UPDATE barang SET
                                 kode_barang = '$kode',
                                 nama_barang = '$nama',
                                 harga = $harga,
                                 stok = $stok,
                                 supplier_id = $supplier_id
                                 WHERE id = $id");
            header("Location: barang.php?pesan=" . urlencode("Data barang berhasil diubah."));
            exit;
        }
    }
}

// ===== DATA UNTUK FORM EDIT =====
$data_edit = null;
if ($aksi === 'edit' && $id > 0) {
    $data_edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM barang WHERE id = $id"));

    if (!$data_edit) {
        header("Location: barang.php");
        exit;
    }
}

// ===== DATA LIST =====
$result = mysqli_query($conn, "SELECT b.*, s.nama AS nama_supplier
                               FROM barang b
                               JOIN supplier s ON b.supplier_id = s.id
                               ORDER BY b.id ASC");
$suppliers = mysqli_query($conn, "SELECT id, nama FROM supplier ORDER BY nama ASC");

$judul = "Data Barang";
include __DIR__ . '/../layout/header.php';
?>

<?php if (!empty($_GET['pesan'])) : ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['pesan']); ?></div>
<?php endif; ?>

<?php if (!empty($_GET['error'])) : ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']); ?></div>
<?php endif; ?>

<?php if ($aksi === 'tambah') : ?>
    <h1>Tambah Data Barang</h1>

    <?php if ($error !== "") : ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="barang.php?aksi=tambah">
        <div class="form-group">
            <label for="kode_barang">Kode Barang</label>
            <input type="text" id="kode_barang" name="kode_barang"
                   value="<?= htmlspecialchars($_POST['kode_barang'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="nama_barang">Nama Barang</label>
            <input type="text" id="nama_barang" name="nama_barang"
                   value="<?= htmlspecialchars($_POST['nama_barang'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="harga">Harga</label>
            <input type="number" id="harga" name="harga" min="0"
                   value="<?= htmlspecialchars($_POST['harga'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="stok">Stok</label>
            <input type="number" id="stok" name="stok" min="0"
                   value="<?= htmlspecialchars($_POST['stok'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="supplier_id">Supplier</label>
            <select id="supplier_id" name="supplier_id" required>
                <option value="">-- Pilih Supplier --</option>
                <?php while ($s = mysqli_fetch_assoc($suppliers)) : ?>
                    <option value="<?= $s['id']; ?>"
                        <?= (isset($_POST['supplier_id']) && $_POST['supplier_id'] == $s['id']) ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($s['nama']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-tambah">Simpan</button>
        <a href="barang.php" class="btn btn-kembali">Kembali</a>
    </form>

<?php elseif ($aksi === 'edit' && $data_edit) : ?>
    <h1>Edit Data Barang</h1>

    <?php if ($error !== "") : ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="barang.php?aksi=edit">
        <input type="hidden" name="id" value="<?= $data_edit['id']; ?>">

        <div class="form-group">
            <label for="kode_barang">Kode Barang</label>
            <input type="text" id="kode_barang" name="kode_barang"
                   value="<?= htmlspecialchars($_POST['kode_barang'] ?? $data_edit['kode_barang']); ?>" required>
        </div>

        <div class="form-group">
            <label for="nama_barang">Nama Barang</label>
            <input type="text" id="nama_barang" name="nama_barang"
                   value="<?= htmlspecialchars($_POST['nama_barang'] ?? $data_edit['nama_barang']); ?>" required>
        </div>

        <div class="form-group">
            <label for="harga">Harga</label>
            <input type="number" id="harga" name="harga" min="0"
                   value="<?= htmlspecialchars($_POST['harga'] ?? $data_edit['harga']); ?>" required>
        </div>

        <div class="form-group">
            <label for="stok">Stok</label>
            <input type="number" id="stok" name="stok" min="0"
                   value="<?= htmlspecialchars($_POST['stok'] ?? $data_edit['stok']); ?>" required>
        </div>

        <div class="form-group">
            <label for="supplier_id">Supplier</label>
            <select id="supplier_id" name="supplier_id" required>
                <option value="">-- Pilih Supplier --</option>
                <?php
                $supplier_terpilih = $_POST['supplier_id'] ?? $data_edit['supplier_id'];
                while ($s = mysqli_fetch_assoc($suppliers)) :
                ?>
                    <option value="<?= $s['id']; ?>"
                        <?= $supplier_terpilih == $s['id'] ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($s['nama']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-tambah">Update</button>
        <a href="barang.php" class="btn btn-kembali">Kembali</a>
    </form>

<?php else : ?>
    <h1>Data Barang</h1>

    <a href="barang.php?aksi=tambah" class="btn btn-tambah">+ Tambah Barang</a>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
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
                        <a href="barang.php?aksi=hapus&id=<?= $row['id']; ?>" class="btn btn-hapus"
                           onclick="return confirm('Hapus barang ini?');">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include __DIR__ . '/../layout/footer.php'; ?>
