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
include 'layout.php';
tampil_header($judul);
?>

<h1 class="h3 mb-3">Laporan Penjualan</h1>

<?php if ($error !== "") : ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="card shadow-sm mb-4 d-print-none">
    <div class="card-body">
        <form method="GET" action="laporan.php" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai"
                       value="<?= htmlspecialchars($tanggal_mulai); ?>">
            </div>

            <div class="col-md-4">
                <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai"
                       value="<?= htmlspecialchars($tanggal_selesai); ?>">
            </div>

            <div class="col-md-4">
                <button type="submit" class="btn btn-primary">Tampilkan</button>
            </div>
        </form>
    </div>
</div>

<?php if (count($rows) > 0) : ?>
    <div class="mb-3 d-print-none">
        <button type="button" class="btn btn-secondary" onclick="window.print()">Cetak</button>
        <a class="btn btn-success" href="export_excel.php?tanggal_mulai=<?= urlencode($tanggal_mulai); ?>&tanggal_selesai=<?= urlencode($tanggal_selesai); ?>">Export Excel</a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h2 class="h5 mb-3">Tren Pendapatan Harian</h2>
            <canvas id="chartPendapatan"></canvas>
        </div>
    </div>
<?php endif; ?>

<h2 class="h4 mb-3">Rekapitulasi Penjualan Harian</h2>

<div class="card shadow-sm mb-4">
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover align-middle mb-0">
            <thead class="table-light">
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
                        <td colspan="3" class="text-center text-muted">Tidak ada data pada rentang tanggal ini.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<h2 class="h4 mb-3">Ringkasan</h2>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card shadow-sm border-start border-primary border-4">
            <div class="card-body">
                <div class="text-muted small">Jumlah Pelanggan Unik</div>
                <div class="h4 mb-0"><?= $jumlah_pelanggan; ?></div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm border-start border-success border-4">
            <div class="card-body">
                <div class="text-muted small">Total Pendapatan</div>
                <div class="h4 mb-0">Rp <?= number_format($total_pendapatan, 0, ',', '.'); ?></div>
            </div>
        </div>
    </div>
</div>

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

<?php tampil_footer(); ?>
