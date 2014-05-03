<!DOCTYPE html>
<meta charset="utf-8">
<html>
<head>
    <title>Assistenz Planer - Dienstplan-Algorithmus</title>
    <link rel="stylesheet" type="text/css" href="../CSS/global.css" media="all"/>
</head>
<body>

<?php

/*
 * very stupid algorithm
 * checks, that there are two assistants available every day
 * the first available assistant gets the service
 * the second available assistant gets the standby
 */

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
        $names[$count] = $name;
        $dateSheet[$count] = array();
        $allDates = explode(';', $dates);
        for ($i = 1; $i <= $numberOfDays; $i++) {
            if ($allDates[$i - 1] == 1) {
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
$servicePerson = array();
$standbyPerson = array();
for ($i = 1; $i <= $numberOfDays; $i++) {
    $serviceTaken = false;
    $standbyTaken = false;
    for ($j = 0; $j < $count; $j++) {
        if ($dateSheet[$j][$i] == 1) {
            if (!$serviceTaken) {
                $servicePerson[$i] = $j;
                $serviceTaken = true;
                continue;
            }

            if (!$standbyTaken) {
                $standbyPerson[$i] = $j;
                $standbyTaken = true;
                continue;
            }
        }
    }
}


// Output score table to page
echo '<table>';
echo '<tr>';
echo '<th>Tag</th>';
for ($i = 0; $i < $count; $i++) {
    echo '<th>' . $names[$i] . '</th>';
}
echo '<th>Dienst</th>';
echo '<th>Bereitschaft</th>';
echo '</tr>';

for ($i = 1; $i <= $numberOfDays; $i++) {
    echo '<tr>';
    echo '<td>' . $i . '</td>';
    for ($j = 0; $j < $count; $j++) {
        echo '<td>' . $dateSheet[$j][$i] . '</td>';
    }
    echo '<td>' . $names[$servicePerson[$i]] . '</td>';
    echo '<td>' . $names[$standbyPerson[$i]] . '</td>';
    echo '</tr>';
}

// Output to file
$fileName = "../Data/Roster/" . $year . "-" . $month . ".txt";
$fh = fopen($fileName, "w");
fwrite($fh, date("d.m.Y H:i\n")); //date('Y-m-d H:i:s')

for ($i = 1; $i <= $numberOfDays; $i++) {
    fwrite($fh, $names[$servicePerson[$i]] . ';' . $names[$standbyPerson[$i]] . "\n");
}

fclose($fh);

?>

</body>
</html>