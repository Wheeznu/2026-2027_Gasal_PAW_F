<?php
include 'cek_login.php';
include 'koneksi.php';

$tanggal_mulai   = trim($_GET['tanggal_mulai'] ?? '');
$tanggal_selesai = trim($_GET['tanggal_selesai'] ?? '');
$error           = "";

if (($tanggal_mulai !== "" && $tanggal_selesai === "") || ($tanggal_mulai === "" && $tanggal_selesai !== "")) {
    $error = "Isi kedua tanggal atau kosongkan keduanya.";
} elseif ($tanggal_mulai !== "" && $tanggal_selesai !== "" && $tanggal_mulai > $tanggal_selesai) {
    $error = "Tanggal mulai tidak boleh lebih besar dari tanggal selesai.";
}

$labels           = [];
$data             = [];
$rows             = [];
$jumlah_pelanggan = 0;
$total_pendapatan = 0;

if ($error === "") {
    $where = "";

    if ($tanggal_mulai !== "" && $tanggal_selesai !== "") {
        $mulai   = mysqli_real_escape_string($conn, $tanggal_mulai);
        $selesai = mysqli_real_escape_string($conn, $tanggal_selesai);
        $where   = "WHERE waktu_transaksi BETWEEN '$mulai' AND '$selesai'";
    }

    $result = mysqli_query($conn, "SELECT waktu_transaksi, SUM(total) AS total_pendapatan
                                   FROM transaksi
                                   $where
                                   GROUP BY waktu_transaksi
                                   ORDER BY waktu_transaksi ASC");

    while ($row = mysqli_fetch_assoc($result)) {
        $labels[] = $row['waktu_transaksi'];
        $data[]   = (int) $row['total_pendapatan'];
        $rows[]   = $row;
    }

    $summary = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT pelanggan_id) AS jumlah_pelanggan,
                                                              SUM(total) AS total_pendapatan
                                                       FROM transaksi
                                                       $where"));

    $jumlah_pelanggan = (int) $summary['jumlah_pelanggan'];
    $total_pendapatan = (int) $summary['total_pendapatan'];
}

$judul = "Laporan Penjualan";
include 'header.php';
?>

<h1>Laporan Penjualan</h1>

<?php if ($error !== "") : ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
<?php endif; ?>

<form method="GET" action="laporan.php" class="filter-form no-print">
    <div class="form-group">
        <label for="tanggal_mulai">Tanggal Mulai</label>
        <input type="date" id="tanggal_mulai" name="tanggal_mulai"
               value="<?= htmlspecialchars($tanggal_mulai); ?>">
    </div>

    <div class="form-group">
        <label for="tanggal_selesai">Tanggal Selesai</label>
        <input type="date" id="tanggal_selesai" name="tanggal_selesai"
               value="<?= htmlspecialchars($tanggal_selesai); ?>">
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-primary">Tampilkan</button>
    </div>
</form>

<?php if (count($rows) > 0) : ?>
    <div class="actions no-print">
        <button type="button" class="btn" onclick="window.print()">Cetak</button>
        <a class="btn" href="export_excel.php?tanggal_mulai=<?= urlencode($tanggal_mulai); ?>&tanggal_selesai=<?= urlencode($tanggal_selesai); ?>">Export Excel</a>
    </div>

    <h2>Tren Pendapatan Harian</h2>
    <canvas id="chartPendapatan"></canvas>
<?php endif; ?>

<h2>Rekapitulasi Penjualan Harian</h2>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal Transaksi</th>
            <th>Total Pendapatan</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($rows) > 0) : ?>
            <?php foreach ($rows as $i => $row) : ?>
                <tr>
                    <td><?= $i + 1; ?></td>
                    <td><?= htmlspecialchars($row['waktu_transaksi']); ?></td>
                    <td>Rp <?= number_format($row['total_pendapatan'], 0, ',', '.'); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="3">Tidak ada data pada rentang tanggal ini.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<h2>Ringkasan</h2>

<table>
    <tr>
        <th>Jumlah Pelanggan Unik</th>
        <td><?= $jumlah_pelanggan; ?></td>
    </tr>
    <tr>
        <th>Total Pendapatan</th>
        <td>Rp <?= number_format($total_pendapatan, 0, ',', '.'); ?></td>
    </tr>
</table>

<?php if (count($rows) > 0) : ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('chartPendapatan').getContext('2d');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($labels); ?>,
                datasets: [{
                    label: 'Total Pendapatan (Rp)',
                    data: <?= json_encode($data); ?>,
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
                            callback: function (value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    </script>
<?php endif; ?>

<?php include 'footer.php'; ?>
