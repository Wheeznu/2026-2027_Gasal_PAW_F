<?php
// 4.1
$height = array("Andy" => "176", "Barry" => "165", "Charlie" => "170");

$height["David"] = "180";
$height["Ethan"] = "172";
$height["Frank"] = "168";
$height["George"] = "175";
$height["Harry"] = "182";

echo 'height = ';
var_dump($height);
echo "<br>";

foreach ($height as $name => $val) {
    echo "$name is $val cm tall.<br>";
}

echo "<br>";

// 4.2
$weight = array("Andy" => "70", "Barry" => "65", "Charlie" => "75");

echo 'weight = ';
var_dump($weight);
echo "<br>";

$keys = array_keys($weight);
for ($i = 0; $i < count($keys); $i++) {
    $name = $keys[$i];
    echo "$name is " . $weight[$name] . " kg.<br>";
}
