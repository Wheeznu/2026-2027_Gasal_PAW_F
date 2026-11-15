<?php
include "connection.php";

$tanggal_mulai = $_GET['tanggal_mulai'] ?? "";
$tanggal_selesai = $_GET['tanggal_selesai'] ?? "";

$labels = [];
$data = [];
$rows = [];
$jumlah_pelanggan = 0;
$total_pendapatan = 0;

if ($tanggal_mulai !== "" && $tanggal_selesai !== "") {
    $result = mysqli_query($conn, "SELECT waktu_transaksi, SUM(total) AS total_pendapatan
        FROM transaksi
        WHERE waktu_transaksi BETWEEN '$tanggal_mulai' AND '$tanggal_selesai'
        GROUP BY waktu_transaksi
        ORDER BY waktu_transaksi ASC");

    $result2 = mysqli_query($conn, "SELECT COUNT(DISTINCT pelanggan_id) AS jumlah_pelanggan, SUM(total) AS total_pendapatan
        FROM transaksi
        WHERE waktu_transaksi BETWEEN '$tanggal_mulai' AND '$tanggal_selesai'");
} else {
    $result = mysqli_query($conn, "SELECT waktu_transaksi, SUM(total) AS total_pendapatan
        FROM transaksi
        GROUP BY waktu_transaksi
        ORDER BY waktu_transaksi ASC");

    $result2 = mysqli_query($conn, "SELECT COUNT(DISTINCT pelanggan_id) AS jumlah_pelanggan, SUM(total) AS total_pendapatan
        FROM transaksi");
}

while ($row = mysqli_fetch_assoc($result)) {
    $labels[] = $row['waktu_transaksi'];
    $data[] = (int)$row['total_pendapatan'];
    $rows[] = $row;
}

$summary = mysqli_fetch_assoc($result2);
$jumlah_pelanggan = (int)$summary['jumlah_pelanggan'];
$total_pendapatan = (int)$summary['total_pendapatan'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <h3>Laporan Penjualan</h3>

    <!-- Filter Form -->
    <form method="get" class="filter-form no-print">
        <div>
            <label>Tanggal Mulai</label>
            <input type="date" name="tanggal_mulai"
                value="<?php echo htmlspecialchars($tanggal_mulai); ?>">
        </div>
        <div>
            <label>Tanggal Selesai</label>
            <input type="date" name="tanggal_selesai"
                value="<?php echo htmlspecialchars($tanggal_selesai); ?>">
        </div>
        <div>
            <button type="submit">Tampilkan</button>
        </div>
    </form>

    <!-- Bar Chart -->
    <h4>Tren Pendapatan Harian</h4>
    <canvas id="chartPendapatan"></canvas>

    <!-- Tombol Cetak & Export -->
    <div class="actions no-print">
        <button onclick="window.print()">Cetak</button>
        <a href="export_excel.php?tanggal_mulai=<?php echo urlencode($tanggal_mulai); ?>&tanggal_selesai=<?php echo urlencode($tanggal_selesai); ?>"><button>Export</button></a>
    </div>

    <!-- Tabel Rekapitulasi -->
    <h4>Rekapitulasi Penjualan Harian</h4>
    <table>
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

    <!-- Ringkasan -->
    <h4>Ringkasan</h4>
    <table>
        <tr>
            <th>Jumlah Pelanggan Unik</th>
            <td><?php echo $jumlah_pelanggan; ?></td>
        </tr>
        <tr>
            <th>Total Pendapatan Keseluruhan</th>
            <td>Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></td>
        </tr>
    </table>

    <script>
        const ctx = document.getElementById('chartPendapatan').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [{
                    label: 'Total Pendapatan (Rp)',
                    data: <?php echo json_encode($data); ?>,
                    backgroundColor: 'rgba(13, 110, 253, 0.7)',
                    borderColor: 'rgba(13, 110, 253, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    </script>

</body>

</html>