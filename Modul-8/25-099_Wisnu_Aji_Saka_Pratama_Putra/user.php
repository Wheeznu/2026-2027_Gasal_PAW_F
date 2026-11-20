<?php
$wajib_owner = true;
include 'cek_login.php';
include 'koneksi.php';

$aksi  = $_GET['aksi'] ?? 'list';
$id    = (int) ($_GET['id'] ?? 0);
$error = "";

// ===== HAPUS DATA =====
if ($aksi === 'hapus' && $id > 0) {
    if ($id == $_SESSION['id_user']) {
        header("Location: user.php?error=" . urlencode("Anda tidak bisa menghapus akun yang sedang digunakan."));
    } else {
        mysqli_query($conn, "DELETE FROM user WHERE id_user = $id");
        header("Location: user.php?pesan=" . urlencode("Data user berhasil dihapus."));
    }
    exit;
}

// ===== SIMPAN TAMBAH =====
if ($aksi === 'tambah' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, trim($_POST['username'] ?? ''));
    $password = trim($_POST['password'] ?? '');
    $nama     = mysqli_real_escape_string($conn, trim($_POST['nama'] ?? ''));
    $alamat   = mysqli_real_escape_string($conn, trim($_POST['alamat'] ?? ''));
    $hp       = mysqli_real_escape_string($conn, trim($_POST['hp'] ?? ''));
    $level    = (int) ($_POST['level'] ?? 0);

    if ($username === "" || $password === "" || $nama === "" || !in_array($level, [1, 2])) {
        $error = "Username, password, nama, dan level wajib diisi dengan benar.";
    } else {
        $cek = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_user FROM user
                                                       WHERE username = '$username'"));

        if ($cek) {
            $error = "Username sudah digunakan.";
        } else {
            $password_md5 = md5($password);
            mysqli_query($conn, "INSERT INTO user (username, password, nama, alamat, hp, level)
                                 VALUES ('$username', '$password_md5', '$nama', '$alamat', '$hp', $level)");
            header("Location: user.php?pesan=" . urlencode("Data user berhasil ditambahkan."));
            exit;
        }
    }
}

// ===== SIMPAN EDIT =====
if ($aksi === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id       = (int) ($_POST['id'] ?? 0);
    $username = mysqli_real_escape_string($conn, trim($_POST['username'] ?? ''));
    $password = trim($_POST['password'] ?? '');
    $nama     = mysqli_real_escape_string($conn, trim($_POST['nama'] ?? ''));
    $alamat   = mysqli_real_escape_string($conn, trim($_POST['alamat'] ?? ''));
    $hp       = mysqli_real_escape_string($conn, trim($_POST['hp'] ?? ''));
    $level    = (int) ($_POST['level'] ?? 0);

    if ($username === "" || $nama === "" || !in_array($level, [1, 2])) {
        $error = "Username, nama, dan level wajib diisi dengan benar.";
    } else {
        $cek = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_user FROM user
                                                       WHERE username = '$username'
                                                       AND id_user != $id"));

        if ($cek) {
            $error = "Username sudah digunakan.";
        } else {
            $sql_password = "";
            if ($password !== "") {
                $password_md5 = md5($password);
                $sql_password = ", password = '$password_md5'";
            }

            mysqli_query($conn, "UPDATE user SET
                                 username = '$username',
                                 nama = '$nama',
                                 alamat = '$alamat',
                                 hp = '$hp',
                                 level = $level
                                 $sql_password
                                 WHERE id_user = $id");

            // Kalau yang diedit adalah akun sendiri, perbarui session.
            if ($id == $_SESSION['id_user']) {
                $_SESSION['nama']  = $nama;
                $_SESSION['level'] = $level;
            }

            header("Location: user.php?pesan=" . urlencode("Data user berhasil diubah."));
            exit;
        }
    }
}

// ===== DATA UNTUK FORM EDIT =====
$data_edit = null;
if ($aksi === 'edit' && $id > 0) {
    $data_edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM user WHERE id_user = $id"));

    if (!$data_edit) {
        header("Location: user.php");
        exit;
    }
}

// ===== DATA LIST =====
$result = mysqli_query($conn, "SELECT * FROM user ORDER BY id_user ASC");

$judul = "Data User";
include 'layout.php';
tampil_header($judul);
?>

<?php if (!empty($_GET['pesan'])) : ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['pesan']); ?></div>
<?php endif; ?>

<?php if (!empty($_GET['error'])) : ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']); ?></div>
<?php endif; ?>

<?php if ($aksi === 'tambah') : ?>
    <h1 class="h3 mb-3">Tambah Data User</h1>

    <?php if ($error !== "") : ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="user.php?aksi=tambah">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username"
                               value="<?= htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    <div class="col-md-6">
                        <label for="nama" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="nama" name="nama"
                               value="<?= htmlspecialchars($_POST['nama'] ?? ''); ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label for="hp" class="form-label">No. HP</label>
                        <input type="text" class="form-control" id="hp" name="hp"
                               value="<?= htmlspecialchars($_POST['hp'] ?? ''); ?>">
                    </div>

                    <div class="col-12">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="2"><?= htmlspecialchars($_POST['alamat'] ?? ''); ?></textarea>
                    </div>

                    <div class="col-md-4">
                        <label for="level" class="form-label">Level</label>
                        <select class="form-select" id="level" name="level" required>
                            <option value="">-- Pilih Level --</option>
                            <option value="1" <?= (($_POST['level'] ?? '') == 1) ? 'selected' : ''; ?>>1 - Owner</option>
                            <option value="2" <?= (($_POST['level'] ?? '') == 2) ? 'selected' : ''; ?>>2 - Kasir</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success">Simpan</button>
                    <a href="user.php" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>

<?php elseif ($aksi === 'edit' && $data_edit) : ?>
    <h1 class="h3 mb-3">Edit Data User</h1>

    <?php if ($error !== "") : ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="user.php?aksi=edit">
                <input type="hidden" name="id" value="<?= $data_edit['id_user']; ?>">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username"
                               value="<?= htmlspecialchars($_POST['username'] ?? $data_edit['username']); ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label for="password" class="form-label">Password Baru</label>
                        <input type="password" class="form-control" id="password" name="password">
                        <div class="form-text">Kosongkan jika tidak ingin mengubah password.</div>
                    </div>

                    <div class="col-md-6">
                        <label for="nama" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="nama" name="nama"
                               value="<?= htmlspecialchars($_POST['nama'] ?? $data_edit['nama']); ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label for="hp" class="form-label">No. HP</label>
                        <input type="text" class="form-control" id="hp" name="hp"
                               value="<?= htmlspecialchars($_POST['hp'] ?? $data_edit['hp']); ?>">
                    </div>

                    <div class="col-12">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="2"><?= htmlspecialchars($_POST['alamat'] ?? $data_edit['alamat']); ?></textarea>
                    </div>

                    <div class="col-md-4">
                        <label for="level" class="form-label">Level</label>
                        <?php $level_terpilih = $_POST['level'] ?? $data_edit['level']; ?>
                        <select class="form-select" id="level" name="level" required>
                            <option value="">-- Pilih Level --</option>
                            <option value="1" <?= $level_terpilih == 1 ? 'selected' : ''; ?>>1 - Owner</option>
                            <option value="2" <?= $level_terpilih == 2 ? 'selected' : ''; ?>>2 - Kasir</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success">Update</button>
                    <a href="user.php" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>

<?php else : ?>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Data User</h1>
        <a href="user.php?aksi=tambah" class="btn btn-primary">+ Tambah User</a>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Username</th>
                        <th>Nama</th>
                        <th>Alamat</th>
                        <th>No. HP</th>
                        <th>Level</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($result)) : ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($row['username']); ?></td>
                            <td><?= htmlspecialchars($row['nama']); ?></td>
                            <td><?= htmlspecialchars($row['alamat']); ?></td>
                            <td><?= htmlspecialchars($row['hp']); ?></td>
                            <td>
                                <span class="badge <?= $row['level'] == 1 ? 'text-bg-primary' : 'text-bg-secondary'; ?>">
                                    <?= $row['level'] == 1 ? 'Owner' : 'Kasir'; ?>
                                </span>
                            </td>
                            <td class="text-nowrap">
                                <a href="user.php?aksi=edit&id=<?= $row['id_user']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="user.php?aksi=hapus&id=<?= $row['id_user']; ?>" class="btn btn-sm btn-danger"
                                   onclick="return confirm('Hapus user ini?');">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php tampil_footer(); ?>
