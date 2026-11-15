<?php
include "connection.php";

$tanggal_mulai = $_GET['tanggal_mulai'] ?? "";
$tanggal_selesai = $_GET['tanggal_selesai'] ?? "";

if ($tanggal_mulai === "" || $tanggal_selesai === "") {
    $result = mysqli_query($conn, "SELECT waktu_transaksi, SUM(total) AS total_pendapatan
        FROM transaksi
        GROUP BY waktu_transaksi
        ORDER BY waktu_transaksi ASC");
    $filename = "laporan_penjualan.xls";
} else {
    $result = mysqli_query($conn, "SELECT waktu_transaksi, SUM(total) AS total_pendapatan
        FROM transaksi
        WHERE waktu_transaksi BETWEEN '$tanggal_mulai' AND '$tanggal_selesai'
        GROUP BY waktu_transaksi
        ORDER BY waktu_transaksi ASC");
    $filename = "laporan_penjualan_{$tanggal_mulai}_sd_{$tanggal_selesai}.xls";
}

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");


while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eksport</title>
</head>

<body>
    <!-- Tabel Rekapitulasi -->
    <h4>Rekapitulasi Penjualan Harian</h4>
    <table border="1">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal Transaksi</th>
                <th>Total Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $i => $row): ?>
                <tr>
                    <td><?php echo $i + 1; ?></td>
                    <td><?php echo htmlspecialchars($row['waktu_transaksi']); ?></td>
                    <td>Rp <?php echo number_format($row['total_pendapatan'], 0, ',', '.'); ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($rows)): ?>
                <tr>
                    <td colspan="3">Tidak ada data pada rentang tanggal ini.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>

</html>