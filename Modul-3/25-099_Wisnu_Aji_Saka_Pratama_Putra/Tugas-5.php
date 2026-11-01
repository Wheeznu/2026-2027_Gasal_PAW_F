<?php
// 5.1
$students = [
    ["Alex", "220401", "0812345678"],
    ["Bianca", "220402", "0812345687"],
    ["Candice", "220403", "0812345665"]
];

echo "data awal: <br>";

for ($i = 0; $i < count($students); $i++) {
    echo " | ";
    for ($j = 0; $j < count($students[$i]); $j++) {
        echo $students[$i][$j] . " | ";
    }
    echo "<br>";
}


echo "<br>";
echo "<br>";

$new_students = [
    ["Daniel", "220404", "0812345611"],
    ["Elena", "220405", "0812345622"],
    ["Fiona", "220406", "0812345633"],
    ["Gabe", "220407", "0812345644"],
    ["Hannah", "220408", "0812345655"]
];

for ($i = 0; $i < count($new_students); $i++) {
    array_push($students, $new_students[$i]);
}

echo "data setelah ditambah 5 data lain: <br>";

for ($i = 0; $i < count($students); $i++) {
    echo " | ";
    for ($j = 0; $j < count($students[$i]); $j++) {
        echo $students[$i][$j] . " | ";
    }
    echo "<br>";
}

echo "<br>";
echo "<br>";

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <table border="1">
        <tr>
            <th>Name</th>
            <th>NIM</th>
            <th>Mobile</th>
        </tr>
        <?php for ($i = 0; $i < count($students); $i++): ?>
            <tr>
                <?php for ($j = 0; $j < count($students[$i]); $j++): ?>
                    <td><?php echo $students[$i][$j]; ?></td>
                <?php endfor; ?>
            </tr>
        <?php endfor; ?>
    </table>
</body>

</html>