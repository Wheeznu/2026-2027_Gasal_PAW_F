<?php
// ===== detail.php — Wisnu Aji Saka Pratama Putra (25-099) =====
// Detail item transaksi: lihat, tambah item, hapus item
include 'koneksi.php';

$transaksi_id = (int) ($_GET['transaksi_id'] ?? 0);
if (!$transaksi_id) { header("Location: transaksi.php"); exit; }

// --- HAPUS ITEM DETAIL ---
if (isset($_GET['hapus_barang'])) {
    $barang_id = (int) $_GET['hapus_barang'];
    if ($barang_id > 0) {
        mysqli_query($conn, "DELETE FROM transaksi_detail WHERE transaksi_id=$transaksi_id AND barang_id=$barang_id");
        // Recalculate total
        mysqli_query($conn, "UPDATE transaksi SET total = (
            SELECT COALESCE(SUM(harga * qty), 0) FROM transaksi_detail WHERE transaksi_id = $transaksi_id
        ) WHERE id = $transaksi_id");
    }
    header("Location: detail.php?transaksi_id=$transaksi_id");
    exit;
}

// --- TAMBAH ITEM DETAIL ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $barang_id = (int) $_POST['barang_id'];
    $qty       = (int) $_POST['qty'];

    // Ambil harga dari tabel barang
    $b     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT harga FROM barang WHERE id = $barang_id"));
    $harga = (int) $b['harga'];

    mysqli_query($conn, "INSERT INTO transaksi_detail (transaksi_id, barang_id, harga, qty)
                         VALUES ($transaksi_id, $barang_id, $harga, $qty)");

    // Recalculate total transaksi
    mysqli_query($conn, "UPDATE transaksi SET total = (
        SELECT COALESCE(SUM(harga * qty), 0) FROM transaksi_detail WHERE transaksi_id = $transaksi_id
    ) WHERE id = $transaksi_id");

    header("Location: detail.php?transaksi_id=$transaksi_id");
    exit;
}

// --- Data master transaksi ---
$transaksi = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT t.*, p.nama AS nama_pelanggan
    FROM transaksi t JOIN pelanggan p ON t.pelanggan_id = p.id
    WHERE t.id = $transaksi_id
"));
if (!$transaksi) { header("Location: transaksi.php"); exit; }

// --- Data detail ---
$details = mysqli_query($conn, "
    SELECT td.*, b.nama_barang, b.kode_barang
    FROM transaksi_detail td
    JOIN barang b ON td.barang_id = b.id
    WHERE td.transaksi_id = $transaksi_id
");

// Barang yang sudah dipakai di transaksi ini
$used     = [];
$res_used = mysqli_query($conn, "SELECT barang_id FROM transaksi_detail WHERE transaksi_id = $transaksi_id");
while ($u = mysqli_fetch_assoc($res_used)) $used[] = $u['barang_id'];

// Semua barang untuk dropdown
$all_barang = mysqli_query($conn, "SELECT id, kode_barang, nama_barang, harga FROM barang ORDER BY nama_barang ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Transaksi #<?= $transaksi_id; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav><a href="barang.php">Barang</a> <a href="transaksi.php">Transaksi</a></nav>

    <h2>Detail Transaksi #<?= $transaksi_id; ?></h2>
    <p>
        <strong>Tanggal:</strong> <?= $transaksi['waktu_transaksi']; ?> &nbsp;
        <strong>Pelanggan:</strong> <?= htmlspecialchars($transaksi['nama_pelanggan']); ?> &nbsp;
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
                <td colspan="5" style="text-align:right"><strong>Total</strong></td>
                <td colspan="2"><strong>Rp <?= number_format($transaksi['total'], 0, ',', '.'); ?></strong></td>
            </tr>
        </tfoot>
    </table>

    <h3 style="margin-top:30px">Tambah Item</h3>
    <?php
    $total_barang = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jml FROM barang"))['jml'];
    if (count($used) >= $total_barang) :
    ?>
        <p>Semua barang sudah ditambahkan ke transaksi ini.</p>
    <?php else : ?>
    <form method="POST">
        <div class="form-group">
            <label>Barang</label>
            <select name="barang_id" required>
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
            <label>Qty</label>
            <input type="number" name="qty" min="1" value="1" required>
        </div>
        <button type="submit" class="btn btn-tambah">Tambah</button>
    </form>
    <?php endif; ?>

    <br>
    <a href="transaksi.php" class="btn btn-kembali">Kembali</a>
</body>
</html>
