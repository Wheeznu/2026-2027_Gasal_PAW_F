<?php
include 'cek_owner.php';
include 'koneksi.php';

$aksi  = $_GET['aksi'] ?? 'list';
$id    = (int) ($_GET['id'] ?? 0);
$error = "";

// ===== HAPUS DATA =====
if ($aksi === 'hapus' && $id > 0) {
    $cek = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jumlah
                                                   FROM barang
                                                   WHERE supplier_id = $id"));

    if ($cek['jumlah'] > 0) {
        header("Location: supplier.php?error=" . urlencode("Supplier tidak bisa dihapus karena masih dipakai oleh barang."));
    } else {
        mysqli_query($conn, "DELETE FROM supplier WHERE id = $id");
        header("Location: supplier.php?pesan=" . urlencode("Data supplier berhasil dihapus."));
    }
    exit;
}

// ===== SIMPAN TAMBAH =====
if ($aksi === 'tambah' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama   = mysqli_real_escape_string($conn, trim($_POST['nama'] ?? ''));
    $telp   = mysqli_real_escape_string($conn, trim($_POST['telp'] ?? ''));
    $alamat = mysqli_real_escape_string($conn, trim($_POST['alamat'] ?? ''));

    if ($nama === "" || $telp === "" || $alamat === "") {
        $error = "Semua data wajib diisi.";
    } else {
        mysqli_query($conn, "INSERT INTO supplier (nama, telp, alamat)
                             VALUES ('$nama', '$telp', '$alamat')");
        header("Location: supplier.php?pesan=" . urlencode("Data supplier berhasil ditambahkan."));
        exit;
    }
}

// ===== SIMPAN EDIT =====
if ($aksi === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id     = (int) ($_POST['id'] ?? 0);
    $nama   = mysqli_real_escape_string($conn, trim($_POST['nama'] ?? ''));
    $telp   = mysqli_real_escape_string($conn, trim($_POST['telp'] ?? ''));
    $alamat = mysqli_real_escape_string($conn, trim($_POST['alamat'] ?? ''));

    if ($nama === "" || $telp === "" || $alamat === "") {
        $error = "Semua data wajib diisi.";
    } else {
        mysqli_query($conn, "UPDATE supplier SET
                             nama = '$nama',
                             telp = '$telp',
                             alamat = '$alamat'
                             WHERE id = $id");
        header("Location: supplier.php?pesan=" . urlencode("Data supplier berhasil diubah."));
        exit;
    }
}

// ===== DATA UNTUK FORM EDIT =====
$data_edit = null;
if ($aksi === 'edit' && $id > 0) {
    $data_edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM supplier WHERE id = $id"));

    if (!$data_edit) {
        header("Location: supplier.php");
        exit;
    }
}

// ===== DATA LIST =====
$result = mysqli_query($conn, "SELECT * FROM supplier ORDER BY id ASC");

$judul = "Data Supplier";
include 'header.php';
?>

<?php if (!empty($_GET['pesan'])) : ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['pesan']); ?></div>
<?php endif; ?>

<?php if (!empty($_GET['error'])) : ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']); ?></div>
<?php endif; ?>

<?php if ($aksi === 'tambah') : ?>
    <h1 class="h3 mb-3">Tambah Data Supplier</h1>

    <?php if ($error !== "") : ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="supplier.php?aksi=tambah">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="nama" class="form-label">Nama Supplier</label>
                        <input type="text" class="form-control" id="nama" name="nama"
                               value="<?= htmlspecialchars($_POST['nama'] ?? ''); ?>" required>
                    </div>

                    <div class="col-md-6">
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
                    <a href="supplier.php" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>

<?php elseif ($aksi === 'edit' && $data_edit) : ?>
    <h1 class="h3 mb-3">Edit Data Supplier</h1>

    <?php if ($error !== "") : ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="supplier.php?aksi=edit">
                <input type="hidden" name="id" value="<?= $data_edit['id']; ?>">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="nama" class="form-label">Nama Supplier</label>
                        <input type="text" class="form-control" id="nama" name="nama"
                               value="<?= htmlspecialchars($_POST['nama'] ?? $data_edit['nama']); ?>" required>
                    </div>

                    <div class="col-md-6">
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
                    <a href="supplier.php" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>

<?php else : ?>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Data Supplier</h1>
        <a href="supplier.php?aksi=tambah" class="btn btn-primary">+ Tambah Supplier</a>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Supplier</th>
                        <th>Telepon</th>
                        <th>Alamat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($result)) : ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($row['nama']); ?></td>
                            <td><?= htmlspecialchars($row['telp']); ?></td>
                            <td><?= htmlspecialchars($row['alamat']); ?></td>
                            <td class="text-nowrap">
                                <a href="supplier.php?aksi=edit&id=<?= $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="supplier.php?aksi=hapus&id=<?= $row['id']; ?>" class="btn btn-sm btn-danger"
                                   onclick="return confirm('Hapus supplier ini?');">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php include 'footer.php'; ?>
