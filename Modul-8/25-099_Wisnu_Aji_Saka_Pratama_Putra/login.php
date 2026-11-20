<?php
session_start();
include 'koneksi.php';

// ===== LOGOUT =====
// Logout ditangani di halaman login supaya tidak perlu file terpisah.
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php?pesan=logout");
    exit;
}

// Kalau sudah login, user tidak boleh membuka halaman login lagi.
if (isset($_SESSION['id_user'])) {
    header("Location: index.php");
    exit;
}

$error    = "";
$username = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === "" || $password === "") {
        $error = "Username dan password wajib diisi.";
    } else {
        $username_db = mysqli_real_escape_string($conn, $username);
        $password_db = md5($password);

        $query = mysqli_query($conn, "SELECT * FROM user
                                      WHERE username = '$username_db'
                                      AND password = '$password_db'");
        $user = mysqli_fetch_assoc($query);

        if ($user) {
            session_regenerate_id(true);

            $_SESSION['id_user']  = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama']     = $user['nama'];
            $_SESSION['level']    = $user['level'];

            header("Location: index.php");
            exit;
        } else {
            $error = "Username atau password salah.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Penjualan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="min-vh-100 d-flex align-items-center justify-content-center p-3">
        <div class="card shadow-sm w-100" style="max-width: 380px;">
            <div class="card-body p-4">
                <h1 class="h4 text-center mb-1">Sistem Penjualan</h1>
                <p class="text-center text-muted mb-4">Silakan Login</p>

                <?php if ($error !== "") : ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="POST" action="login.php">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username"
                               value="<?= htmlspecialchars($username); ?>" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>

                <?php if (($_GET['pesan'] ?? '') === 'logout') : ?>
                    <div class="alert alert-success mt-3 mb-0">Anda berhasil logout.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
