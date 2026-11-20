<?php
include __DIR__ . '/../auth/cek_login.php';
include __DIR__ . '/../layout/koneksi.php';

$transaksi_id = (int) ($_GET['transaksi_id'] ?? 0);
$error        = "";

if ($transaksi_id <= 0) {
    header("Location: transaksi.php");
    exit;
}

// ===== HAPUS ITEM =====
if (isset($_GET['hapus_barang'])) {
    $barang_id = (int) $_GET['hapus_barang'];

    if ($barang_id > 0) {
        mysqli_query($conn, "DELETE FROM transaksi_detail
                             WHERE transaksi_id = $transaksi_id
                             AND barang_id = $barang_id");

        mysqli_query($conn, "UPDATE transaksi SET total = (
                                 SELECT COALESCE(SUM(harga * qty), 0)
                                 FROM transaksi_detail
                                 WHERE transaksi_id = $transaksi_id
                             ) WHERE id = $transaksi_id");
    }

    header("Location: detail.php?transaksi_id=$transaksi_id&pesan=" . urlencode("Item berhasil dihapus."));
    exit;
}

// ===== TAMBAH ITEM =====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $barang_id = (int) ($_POST['barang_id'] ?? 0);
    $qty       = (int) ($_POST['qty'] ?? 0);

    if ($barang_id <= 0 || $qty <= 0) {
        $error = "Barang dan qty wajib diisi dengan benar.";
    } else {
        $barang = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM barang WHERE id = $barang_id"));

        if (!$barang) {
            $error = "Barang tidak ditemukan.";
        } else {
            $cek = mysqli_fetch_assoc(mysqli_query($conn, "SELECT barang_id FROM transaksi_detail
                                                           WHERE transaksi_id = $transaksi_id
                                                           AND barang_id = $barang_id"));

            if ($cek) {
                $error = "Barang tersebut sudah ada pada transaksi ini.";
            } else {
                $harga = (int) $barang['harga'];

                mysqli_query($conn, "INSERT INTO transaksi_detail (transaksi_id, barang_id, harga, qty)
                                     VALUES ($transaksi_id, $barang_id, $harga, $qty)");

                mysqli_query($conn, "UPDATE transaksi SET total = (
                                         SELECT COALESCE(SUM(harga * qty), 0)
                                         FROM transaksi_detail
                                         WHERE transaksi_id = $transaksi_id
                                     ) WHERE id = $transaksi_id");

                header("Location: detail.php?transaksi_id=$transaksi_id&pesan=" . urlencode("Item berhasil ditambahkan."));
                exit;
            }
        }
    }
}

// ===== DATA TRANSAKSI =====
$transaksi = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT t.*, p.nama AS nama_pelanggan
    FROM transaksi t
    JOIN pelanggan p ON t.pelanggan_id = p.id
    WHERE t.id = $transaksi_id
"));

if (!$transaksi) {
    header("Location: transaksi.php");
    exit;
}

// ===== DATA DETAIL =====
$details = mysqli_query($conn, "
    SELECT td.*, b.kode_barang, b.nama_barang
    FROM transaksi_detail td
    JOIN barang b ON td.barang_id = b.id
    WHERE td.transaksi_id = $transaksi_id
    ORDER BY b.nama_barang ASC
");

// Barang yang sudah dipakai di transaksi ini
$used = [];
$res_used = mysqli_query($conn, "SELECT barang_id FROM transaksi_detail WHERE transaksi_id = $transaksi_id");
while ($u = mysqli_fetch_assoc($res_used)) {
    $used[] = $u['barang_id'];
}

// Semua barang untuk dropdown
$all_barang = mysqli_query($conn, "SELECT id, kode_barang, nama_barang, harga
                                   FROM barang ORDER BY nama_barang ASC");

$total_barang = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jumlah FROM barang"))['jumlah'];

$judul = "Detail Transaksi";
include __DIR__ . '/../layout/header.php';
?>

<?php if (!empty($_GET['pesan'])) : ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['pesan']); ?></div>
<?php endif; ?>

<?php if ($error !== "") : ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
<?php endif; ?>

<h1>Detail Transaksi #<?= $transaksi_id; ?></h1>

<p>
    <strong>Tanggal:</strong> <?= htmlspecialchars($transaksi['waktu_transaksi']); ?><br>
    <strong>Pelanggan:</strong> <?= htmlspecialchars($transaksi['nama_pelanggan']); ?><br>
    <strong>Keterangan:</strong> <?= htmlspecialchars($transaksi['keterangan']); ?>
</p>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Kode</th>
            <th>Nama Barang</th>
            <th>Harga Satuan</th>
            <th>Qty</th>
            <th>Subtotal</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; while ($row = mysqli_fetch_assoc($details)) : ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= htmlspecialchars($row['kode_barang']); ?></td>
                <td><?= htmlspecialchars($row['nama_barang']); ?></td>
                <td>Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
                <td><?= $row['qty']; ?></td>
                <td>Rp <?= number_format($row['harga'] * $row['qty'], 0, ',', '.'); ?></td>
                <td>
                    <a href="detail.php?transaksi_id=<?= $transaksi_id; ?>&hapus_barang=<?= $row['barang_id']; ?>"
                       class="btn btn-hapus"
                       onclick="return confirm('Hapus item ini?');">Hapus</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="5" class="text-right">Total</th>
            <th colspan="2">Rp <?= number_format($transaksi['total'], 0, ',', '.'); ?></th>
        </tr>
    </tfoot>
</table>

<h2>Tambah Item</h2>

<?php if (count($used) >= $total_barang) : ?>
    <p>Semua barang sudah ditambahkan ke transaksi ini.</p>
<?php else : ?>
    <form method="POST">
        <div class="form-group">
            <label for="barang_id">Barang</label>
            <select id="barang_id" name="barang_id" required>
                <option value="">-- Pilih Barang --</option>
                <?php while ($b = mysqli_fetch_assoc($all_barang)) : ?>
                    <option value="<?= $b['id']; ?>"
                        <?= in_array($b['id'], $used) ? 'disabled' : ''; ?>>
                        <?= htmlspecialchars($b['kode_barang'] . ' - ' . $b['nama_barang']); ?>
                        (Rp <?= number_format($b['harga'], 0, ',', '.'); ?>)
                        <?= in_array($b['id'], $used) ? '[sudah ditambahkan]' : ''; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="qty">Qty</label>
            <input type="number" id="qty" name="qty" min="1" value="1" required>
        </div>

        <button type="submit" class="btn btn-tambah">Tambah</button>
    </form>
<?php endif; ?>

<p>
    <a href="transaksi.php" class="btn btn-kembali">Kembali ke Transaksi</a>
</p>

<?php include __DIR__ . '/../layout/footer.php'; ?>
