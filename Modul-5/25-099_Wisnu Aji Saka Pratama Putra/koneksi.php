<?php
$conn = mysqli_connect("localhost", "root", "", "Modul5_Database");

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>
