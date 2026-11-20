<?php
include 'cek_owner.php';
include 'koneksi.php';

// Membuat ID pelanggan otomatis, contoh: PLG006
function buat_id_pelanggan($conn)
{
    $query = mysqli_query($conn, "SELECT id FROM pelanggan ORDER BY id DESC LIMIT 1");
    $data  = mysqli_fetch_assoc($query);

    if ($data) {
        $nomor = (int) substr($data['id'], 3) + 1;
    } else {
        $nomor = 1;
    }

    return "PLG" . str_pad($nomor, 3, "0", STR_PAD_LEFT);
}

$aksi  = $_GET['aksi'] ?? 'list';
$id    = $_GET['id'] ?? '';
$error = "";

// ===== HAPUS DATA =====
if ($aksi === 'hapus' && $id !== '') {
    $id_aman = mysqli_real_escape_string($conn, $id);
    $cek     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jumlah
                                                       FROM transaksi
                                                       WHERE pelanggan_id = '$id_aman'"));

    if ($cek['jumlah'] > 0) {
        header("Location: pelanggan.php?error=" . urlencode("Pelanggan tidak bisa dihapus karena sudah memiliki transaksi."));
    } else {
        mysqli_query($conn, "DELETE FROM pelanggan WHERE id = '$id_aman'");
        header("Location: pelanggan.php?pesan=" . urlencode("Data pelanggan berhasil dihapus."));
    }
    exit;
}

// ===== SIMPAN TAMBAH =====
if ($aksi === 'tambah' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_baru       = buat_id_pelanggan($conn);
    $nama          = mysqli_real_escape_string($conn, trim($_POST['nama'] ?? ''));
    $jenis_kelamin = mysqli_real_escape_string($conn, $_POST['jenis_kelamin'] ?? '');
    $telp          = mysqli_real_escape_string($conn, trim($_POST['telp'] ?? ''));
    $alamat        = mysqli_real_escape_string($conn, trim($_POST['alamat'] ?? ''));

    if ($nama === "" || $telp === "" || $alamat === "" || ($jenis_kelamin !== 'L' && $jenis_kelamin !== 'P')) {
        $error = "Semua data wajib diisi dengan benar.";
    } else {
        mysqli_query($conn, "INSERT INTO pelanggan (id, nama, jenis_kelamin, telp, alamat)
                             VALUES ('$id_baru', '$nama', '$jenis_kelamin', '$telp', '$alamat')");
        header("Location: pelanggan.php?pesan=" . urlencode("Data pelanggan berhasil ditambahkan."));
        exit;
    }
}

// ===== SIMPAN EDIT =====
if ($aksi === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id            = mysqli_real_escape_string($conn, $_POST['id'] ?? '');
    $nama          = mysqli_real_escape_string($conn, trim($_POST['nama'] ?? ''));
    $jenis_kelamin = mysqli_real_escape_string($conn, $_POST['jenis_kelamin'] ?? '');
    $telp          = mysqli_real_escape_string($conn, trim($_POST['telp'] ?? ''));
    $alamat        = mysqli_real_escape_string($conn, trim($_POST['alamat'] ?? ''));

    if ($nama === "" || $telp === "" || $alamat === "" || ($jenis_kelamin !== 'L' && $jenis_kelamin !== 'P')) {
        $error = "Semua data wajib diisi dengan benar.";
    } else {
        mysqli_query($conn, "UPDATE pelanggan SET
                             nama = '$nama',
                             jenis_kelamin = '$jenis_kelamin',
                             telp = '$telp',
                             alamat = '$alamat'
                             WHERE id = '$id'");
        header("Location: pelanggan.php?pesan=" . urlencode("Data pelanggan berhasil diubah."));
        exit;
    }
}

// ===== DATA UNTUK FORM EDIT =====
$data_edit = null;
if ($aksi === 'edit' && $id !== '') {
    $id_aman   = mysqli_real_escape_string($conn, $id);
    $data_edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM pelanggan WHERE id = '$id_aman'"));

    if (!$data_edit) {
        header("Location: pelanggan.php");
        exit;
    }
}

// ===== DATA LIST =====
$result = mysqli_query($conn, "SELECT * FROM pelanggan ORDER BY id ASC");

$judul = "Data Pelanggan";
include 'header.php';
?>

<?php if (!empty($_GET['pesan'])) : ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['pesan']); ?></div>
<?php endif; ?>

<?php if (!empty($_GET['error'])) : ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']); ?></div>
<?php endif; ?>

<?php if ($aksi === 'tambah') : ?>
    <h1 class="h3 mb-3">Tambah Data Pelanggan</h1>

    <?php if ($error !== "") : ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="pelanggan.php?aksi=tambah">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">ID Pelanggan</label>
                        <input type="text" class="form-control" value="<?= buat_id_pelanggan($conn); ?>" readonly>
                    </div>

                    <div class="col-md-8">
                        <label for="nama" class="form-label">Nama Pelanggan</label>
                        <input type="text" class="form-control" id="nama" name="nama"
                               value="<?= htmlspecialchars($_POST['nama'] ?? ''); ?>" required>
                    </div>

                    <div class="col-md-4">
                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                        <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required>
                            <option value="">-- Pilih --</option>
                            <option value="L" <?= (($_POST['jenis_kelamin'] ?? '') === 'L') ? 'selected' : ''; ?>>Laki-laki</option>
                            <option value="P" <?= (($_POST['jenis_kelamin'] ?? '') === 'P') ? 'selected' : ''; ?>>Perempuan</option>
                        </select>
                    </div>

                    <div class="col-md-8">
                        <label for="telp" class="form-label">No. Telepon</label>
                        <input type="text" class="form-control" id="telp" name="telp"
                               value="<?= htmlspecialchars($_POST['telp'] ?? ''); ?>" required>
                    </div>

                    <div class="col-12">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="3" required><?= htmlspecialchars($_POST['alamat'] ?? ''); ?></textarea>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success">Simpan</button>
                    <a href="pelanggan.php" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>

<?php elseif ($aksi === 'edit' && $data_edit) : ?>
    <h1 class="h3 mb-3">Edit Data Pelanggan</h1>

    <?php if ($error !== "") : ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="pelanggan.php?aksi=edit">
                <input type="hidden" name="id" value="<?= htmlspecialchars($data_edit['id']); ?>">

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">ID Pelanggan</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($data_edit['id']); ?>" readonly>
                    </div>

                    <div class="col-md-8">
                        <label for="nama" class="form-label">Nama Pelanggan</label>
                        <input type="text" class="form-control" id="nama" name="nama"
                               value="<?= htmlspecialchars($_POST['nama'] ?? $data_edit['nama']); ?>" required>
                    </div>

                    <div class="col-md-4">
                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                        <?php $jk = $_POST['jenis_kelamin'] ?? $data_edit['jenis_kelamin']; ?>
                        <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required>
                            <option value="">-- Pilih --</option>
                            <option value="L" <?= $jk === 'L' ? 'selected' : ''; ?>>Laki-laki</option>
                            <option value="P" <?= $jk === 'P' ? 'selected' : ''; ?>>Perempuan</option>
                        </select>
                    </div>

                    <div class="col-md-8">
                        <label for="telp" class="form-label">No. Telepon</label>
                        <input type="text" class="form-control" id="telp" name="telp"
                               value="<?= htmlspecialchars($_POST['telp'] ?? $data_edit['telp']); ?>" required>
                    </div>

                    <div class="col-12">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="3" required><?= htmlspecialchars($_POST['alamat'] ?? $data_edit['alamat']); ?></textarea>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success">Update</button>
                    <a href="pelanggan.php" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>

<?php else : ?>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Data Pelanggan</h1>
        <a href="pelanggan.php?aksi=tambah" class="btn btn-primary">+ Tambah Pelanggan</a>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Jenis Kelamin</th>
                        <th>Telepon</th>
                        <th>Alamat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($result)) : ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($row['id']); ?></td>
                            <td><?= htmlspecialchars($row['nama']); ?></td>
                            <td><?= $row['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan'; ?></td>
                            <td><?= htmlspecialchars($row['telp']); ?></td>
                            <td><?= htmlspecialchars($row['alamat']); ?></td>
                            <td class="text-nowrap">
                                <a href="pelanggan.php?aksi=edit&id=<?= urlencode($row['id']); ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="pelanggan.php?aksi=hapus&id=<?= urlencode($row['id']); ?>" class="btn btn-sm btn-danger"
                                   onclick="return confirm('Hapus pelanggan ini?');">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php include 'footer.php'; ?>
