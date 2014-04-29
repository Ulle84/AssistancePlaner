<!DOCTYPE html>
<meta charset="utf-8">
<html>
<head>
    <title>Assistenz Planer - Dienstplan-Algorithmus</title>
    <link rel="stylesheet" type="text/css" href="../CSS/global.css" media="all"/>
</head>
<body>

<?php

$month = date("n");
$year = date("Y");

if (isset($_GET['year'])) {
    $year = $_GET['year'];
}

if (isset($_GET['month'])) {
    $month = $_GET['month'];
}

$numberOfDays = date("t", mktime(0, 0, 0, $month, 1, $year));


// check that there is enough availability
// perform algorithm
// print result
// write result to output file
$fileName = "../Data/AssistanceInput/" . $year . "-" . $month . ".txt";

if (!file_exists($fileName)) {
    echo "Keine Eingabe der Assistenten gefunden.";
    exit;
}

$file = fopen($fileName, "r");
$dateSheet = array();
// 0 = busy
// 1 = available
// 2 = standby
// 3 = service

$count = 0;
$names = array();
while (!feof($file)) {
    $name = rtrim(fgets($file));
    $dates = rtrim(fgets($file));

    if ($name != "") {
        echo $count . ' = ';
        echo $name . '<br />';

        $names[$count] = $name;
        $dateSheet[$count] = array();
        $allDates = explode(';', $dates);
        for ($i = 1; $i <= $numberOfDays; $i++) {
            if (in_array($i, $allDates)) {
                $dateSheet[$count][$i] = 1;
            } else {
                $dateSheet[$count][$i] = 0;
            }
        }
        $count++;
    }
}
fclose($file);

// check availability
for ($i = 1; $i <= $numberOfDays; $i++) {
    $sum = 0;
    for ($j = 0; $j < $count; $j++) {
        if ($dateSheet[$j][$i] == 1) {
            $sum += 1;
        }
    }
    if ($sum < 2) {
        echo 'Nicht genug freie Termine bei den Assistenten vorhanden, um einen vollständigen Dienstplan zu erstellen.';
        exit;
    }
}

// give the first best the service and the second best the standby
for ($i = 1; $i <= $numberOfDays; $i++) {
    $serviceTaken = false;
    $standbyTaken = false;
    for ($j = 0; $j < $count; $j++) {
        if ($dateSheet[$j][$i] == 1) {
            if (!$serviceTaken) {
                $dateSheet[$j][$i] = 3;
                $serviceTaken = true;
                continue;
            }

            if (!$standbyTaken) {
                $dateSheet[$j][$i] = 2;
                $standbyTaken = true;
                continue;
            }
        }
    }
}


// Output to page
echo '<table>';
echo '<tr>';
echo '<th>Tag</th>';
for ($i = 0; $i < $count; $i++) {
    echo '<th>' . $names[$i] . '</th>';
}
echo '</tr>';

for ($i = 1; $i <= $numberOfDays; $i++) {
    echo '<tr>';
    echo '<td>' . $i . '</td>';
    for ($j = 0; $j < $count; $j++) {
        echo '<td>' . $dateSheet[$j][$i] . '</td>';
    }
    echo '</tr>';
}

// Output to file
$fileName = "../Data/Roster/" . $year . "-" . $month . ".txt";
$fh = fopen($fileName, "w");
fwrite($fh, date("d.m.Y H:i\n")); //date('Y-m-d H:i:s')

for ($i = 1; $i <= $numberOfDays; $i++) {
    $serviceIndex = 0;
    $standbyIndex = 0;
    for ($j = 0; $j < $count; $j++) {
        if ($dateSheet[$j][$i] == 3) {
            $serviceIndex = $j;
        }
        if ($dateSheet[$j][$i] == 2) {
            $standbyIndex = $j;
        }
    }
    fwrite($fh, $names[$serviceIndex] . ';' . $names[$standbyIndex] . "\n");
}

fclose($fh);

?>

</body>
</html>