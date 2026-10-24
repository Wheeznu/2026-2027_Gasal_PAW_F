<?php
// 6.1

// 1. array_push() - Menambahkan elemen ke akhir array
$buah = ["Apel", "Pisang"];
array_push($buah, "Jeruk", "Mangga");

echo "1. Hasil array_push():<br>";
print_r($buah);
echo "<br><br>";


// 2. array_merge() - Menggabungkan dua atau lebih array
$array1 = ["Merah", "Hijau"];
$array2 = ["Biru", "Kuning"];
$hasil_merge = array_merge($array1, $array2);

echo "2. Hasil array_merge():<br>";
print_r($hasil_merge);
echo "<br><br>";


// 3. array_values() - Mengambil semua nilai dan mereset index numerik
$asosiatif = [
    "nama" => "Budi",
    "kota" => "Surabaya",
    "profesi" => "Developer"
];
$hasil_values = array_values($asosiatif);

echo "3. Hasil array_values():<br>";
print_r($hasil_values);
echo "<br><br>";


// 4. array_search() - Mencari nilai dan mengembalikan key/index-nya
$angka = [10, 25, 50, 75, 100];
$key = array_search(50, $angka);

echo "4. Hasil array_search() [Mencari angka 50]:<br>";
echo "Ditemukan pada index: " . $key . "<br>";
echo "<br><br>";


// 5. array_filter() - Menyaring elemen array berdasarkan kondisi (callback)
$nomor = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
// Mengambil angka genap saja
$genap = array_filter($nomor, function ($n) {
    return $n % 2 === 0;
});

echo "5. Hasil array_filter() [Angka Genap]:<br>";
print_r($genap);
echo "<br><br>";


// 6. FUNGSI SORTING PADA SEIAP JENIS ARRAY

// A. Indexed Array (Array Terurut Biasa)
$data_angka = [42, 12, 88, 5, 23];

// sort() - Urutkan nilai dari terkecil ke terbesar (Ascending)
$sort_asc = $data_angka;
sort($sort_asc);

// rsort() - Urutkan nilai dari terbesar ke terkecil (Descending)
$sort_desc = $data_angka;
rsort($sort_desc);

echo "6A. Sorting Indexed Array:<br>";
echo "Origin: " . implode(", ", $data_angka) . "<br>";
echo "sort()  (Ascending) : " . implode(", ", $sort_asc) . "<br>";
echo "rsort() (Descending): " . implode(", ", $sort_desc) . "<br>";
echo "<br><br>";


// B. Associative Array (Array Berpasangan Key => Value)
$nilai_siswa = [
    "Budi" => 85,
    "Siti" => 92,
    "Andi" => 78,
    "Dewi" => 88
];

// asort() - Urut berdasarkan VALUE (Ascending)
$asort_val = $nilai_siswa;
asort($asort_val);

// ksort() - Urut berdasarkan KEY (Ascending)
$ksort_key = $nilai_siswa;
ksort($ksort_key);

// arsort() - Urut berdasarkan VALUE (Descending)
$arsort_val = $nilai_siswa;
arsort($arsort_val);

// krsort() - Urut berdasarkan KEY (Descending)
$krsort_key = $nilai_siswa;
krsort($krsort_key);

echo "6B. Sorting Associative Array:<br>";
echo "asort() [Berdasarkan Nilai Ascending]:<br>";
print_r($asort_val);

echo "ksort() [Berdasarkan Nama/Key Ascending]:<br>";
print_r($ksort_key);

echo "arsort() [Berdasarkan Nilai Descending]:<br>";
print_r($arsort_val);

echo "krsort() [Berdasarkan Nama/Key Descending]:<br>";
print_r($krsort_key);
