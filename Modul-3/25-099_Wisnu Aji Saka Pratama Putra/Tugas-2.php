<?php
// 2.1
$fruits = ["Avocado", "Blueberry", "Cherry"];
$fruits_baru = ["Durian", "Apple", "Orange", "Strawberry", "Watermelon"];
$fruits = array_merge($fruits, $fruits_baru);

// for ($i = 1; $i <= 5; $i++) {
//     array_push($fruits, "Buah Tambahan " . $i);
// }


echo "Panjang array saat ini: " . count($fruits) . "<br><br>";

for ($x = 0; $x < count($fruits); $x++) {
    echo $fruits[$x];
    echo "<br>";
}
// Penjelasan 2.1: skrip for baris #5-#8 tidak perlu diubah strukturnya, cukup pastikan batasnya memakai count($fruits) terbaru (8, bukan 3). Karena count() dihitung ulang setelah 5 data ditambah, seluruh data tampil.

echo "<br>";

// 2.2

$vegies = ["Carrot", "Broccoli", "Spinach"];

for ($x = 0; $x < count($vegies); $x++) {
    echo $vegies[$x];
    echo "<br>";
}
// Penjelasan 2.2: cukup memodifikasi skrip yang sudah ada (ganti $fruits menjadi $vegies dan count-nya). Alasannya strukturnya sama-sama array terindeks, jadi logika for tidak perlu dibuat baru dari awal.
