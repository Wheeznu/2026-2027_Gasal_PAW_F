<?php
// 1.1
$fruits = array("Avocado", "Blueberry", "Cherry");
array_push($fruits, "Durian", "Elderberry", "Fig", "Grape", "Honeydew");

var_dump($fruits);
echo "<br> Nilai dengan indeks tertinggi: " . $fruits[max(array_keys($fruits))] . "<br>";

echo "<br>";

// 1.2 
unset($fruits[1]);
var_dump($fruits);
echo "<br> Nilai dengan indeks tertinggi: " . $fruits[max(array_keys($fruits))] . "<br>";
