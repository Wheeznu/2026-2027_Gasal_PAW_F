<?php
// 3.1
$height = array("Andy" => "176", "Barry" => "165", "Charlie" => "170");

// Tambah lima data baru
$height["David"] = "180";
$height["Ethan"] = "172";
$height["Frank"] = "168";
$height["George"] = "175";
$height["Harry"] = "182";


echo 'height = ';
var_dump($height);
echo "<br>Nilai dengan indeks terakhir: " . $height["Harry"] . "<br><br>";

// Hapus satu data tertentu (Barry)
unset($height["Barry"]);

echo 'height = ';
var_dump($height);
echo "<br>Nilai dengan indeks terakhir setelah dihapus: " . $height["Harry"] . "<br><br>";

// 3.2
$weight = array("Andy" => "70", "Barry" => "65", "Charlie" => "75");


echo 'weight = ';
var_dump($weight);
echo "<br>Data kedua: " . array_values($weight)[1] . "<br>";
