<?php
// Halaman yang memakai file ini hanya bisa dibuka setelah login.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

// Halaman boleh menandai $wajib_owner = true sebelum include file ini
// agar hanya user level 1 (owner) yang bisa mengaksesnya.
if (!empty($wajib_owner) && $_SESSION['level'] != 1) {
    header("Location: index.php");
    exit;
}
?>
