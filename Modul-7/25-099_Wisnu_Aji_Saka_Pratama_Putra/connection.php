<?php
$conn = mysqli_connect("localhost", "root", "", "db_penjualan_7");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
