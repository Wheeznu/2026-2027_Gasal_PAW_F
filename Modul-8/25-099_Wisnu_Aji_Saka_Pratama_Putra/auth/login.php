<?php
session_start();
include __DIR__ . '/../layout/koneksi.php';

// Kalau sudah login, user tidak boleh membuka halaman login lagi.
if (isset($_SESSION['id_user'])) {
    header("Location: ../layout/index.php");
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

            header("Location: ../layout/index.php");
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
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="login-page">
        <div class="login-box">
            <h1>Sistem Penjualan</h1>
            <h2>Silakan Login</h2>

            <?php if ($error !== "") : ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username"
                           value="<?= htmlspecialchars($username); ?>" required autofocus>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
