<?php
// Data master hanya boleh dibuka oleh user level 1 (owner).
include 'cek_login.php';

if ($_SESSION['level'] != 1) {
    header("Location: index.php");
    exit;
}
?>
