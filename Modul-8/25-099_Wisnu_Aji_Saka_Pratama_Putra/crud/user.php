<?php
include __DIR__ . '/../auth/cek_owner.php';
include __DIR__ . '/../layout/koneksi.php';

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
include __DIR__ . '/../layout/header.php';
?>

<?php if (!empty($_GET['pesan'])) : ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['pesan']); ?></div>
<?php endif; ?>

<?php if (!empty($_GET['error'])) : ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']); ?></div>
<?php endif; ?>

<?php if ($aksi === 'tambah') : ?>
    <h1>Tambah Data User</h1>

    <?php if ($error !== "") : ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="user.php?aksi=tambah">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username"
                   value="<?= htmlspecialchars($_POST['username'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group">
            <label for="nama">Nama Lengkap</label>
            <input type="text" id="nama" name="nama"
                   value="<?= htmlspecialchars($_POST['nama'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="alamat">Alamat</label>
            <textarea id="alamat" name="alamat"><?= htmlspecialchars($_POST['alamat'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label for="hp">No. HP</label>
            <input type="text" id="hp" name="hp"
                   value="<?= htmlspecialchars($_POST['hp'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="level">Level</label>
            <select id="level" name="level" required>
                <option value="">-- Pilih Level --</option>
                <option value="1" <?= (($_POST['level'] ?? '') == 1) ? 'selected' : ''; ?>>1 - Owner</option>
                <option value="2" <?= (($_POST['level'] ?? '') == 2) ? 'selected' : ''; ?>>2 - Kasir</option>
            </select>
        </div>

        <button type="submit" class="btn btn-tambah">Simpan</button>
        <a href="user.php" class="btn btn-kembali">Kembali</a>
    </form>

<?php elseif ($aksi === 'edit' && $data_edit) : ?>
    <h1>Edit Data User</h1>

    <?php if ($error !== "") : ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="user.php?aksi=edit">
        <input type="hidden" name="id" value="<?= $data_edit['id_user']; ?>">

        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username"
                   value="<?= htmlspecialchars($_POST['username'] ?? $data_edit['username']); ?>" required>
        </div>

        <div class="form-group">
            <label for="password">Password Baru</label>
            <input type="password" id="password" name="password">
            <small>Kosongkan jika tidak ingin mengubah password.</small>
        </div>

        <div class="form-group">
            <label for="nama">Nama Lengkap</label>
            <input type="text" id="nama" name="nama"
                   value="<?= htmlspecialchars($_POST['nama'] ?? $data_edit['nama']); ?>" required>
        </div>

        <div class="form-group">
            <label for="alamat">Alamat</label>
            <textarea id="alamat" name="alamat"><?= htmlspecialchars($_POST['alamat'] ?? $data_edit['alamat']); ?></textarea>
        </div>

        <div class="form-group">
            <label for="hp">No. HP</label>
            <input type="text" id="hp" name="hp"
                   value="<?= htmlspecialchars($_POST['hp'] ?? $data_edit['hp']); ?>">
        </div>

        <div class="form-group">
            <label for="level">Level</label>
            <?php $level_terpilih = $_POST['level'] ?? $data_edit['level']; ?>
            <select id="level" name="level" required>
                <option value="">-- Pilih Level --</option>
                <option value="1" <?= $level_terpilih == 1 ? 'selected' : ''; ?>>1 - Owner</option>
                <option value="2" <?= $level_terpilih == 2 ? 'selected' : ''; ?>>2 - Kasir</option>
            </select>
        </div>

        <button type="submit" class="btn btn-tambah">Update</button>
        <a href="user.php" class="btn btn-kembali">Kembali</a>
    </form>

<?php else : ?>
    <h1>Data User</h1>

    <a href="user.php?aksi=tambah" class="btn btn-tambah">+ Tambah User</a>

    <table>
        <thead>
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
                    <td><?= $row['level'] == 1 ? 'Owner' : 'Kasir'; ?></td>
                    <td>
                        <a href="user.php?aksi=edit&id=<?= $row['id_user']; ?>" class="btn btn-edit">Edit</a>
                        <a href="user.php?aksi=hapus&id=<?= $row['id_user']; ?>" class="btn btn-hapus"
                           onclick="return confirm('Hapus user ini?');">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include __DIR__ . '/../layout/footer.php'; ?>
