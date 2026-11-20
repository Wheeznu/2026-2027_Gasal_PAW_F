<?php
include 'cek_login.php';
include 'koneksi.php';

$tanggal_mulai   = trim($_GET['tanggal_mulai'] ?? '');
$tanggal_selesai = trim($_GET['tanggal_selesai'] ?? '');

if ($tanggal_mulai !== "" && $tanggal_selesai !== "") {
    $mulai   = mysqli_real_escape_string($conn, $tanggal_mulai);
    $selesai = mysqli_real_escape_string($conn, $tanggal_selesai);

    $where    = "WHERE waktu_transaksi BETWEEN '$mulai' AND '$selesai'";
    $filename = "laporan_penjualan_{$mulai}_sd_{$selesai}.xls";
} else {
    $where    = "";
    $filename = "laporan_penjualan.xls";
}

$result = mysqli_query($conn, "SELECT waktu_transaksi, SUM(total) AS total_pendapatan
                               FROM transaksi
                               $where
                               GROUP BY waktu_transaksi
                               ORDER BY waktu_transaksi ASC");

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Export Laporan Penjualan</title>
</head>
<body>
    <h3>Rekapitulasi Penjualan Harian</h3>

    <table border="1">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal Transaksi</th>
                <th>Total Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; while ($row = mysqli_fetch_assoc($result)) : ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= htmlspecialchars($row['waktu_transaksi']); ?></td>
                    <td>Rp <?= number_format($row['total_pendapatan'], 0, ',', '.'); ?></td>
                </tr>
            <?php endwhile; ?>

            <?php if ($no === 1) : ?>
                <tr>
                    <td colspan="3">Tidak ada data pada rentang tanggal ini.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
