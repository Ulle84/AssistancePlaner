<!DOCTYPE html>
<meta charset="utf-8">
<html>
<head>
    <title>Assistenz Planer - Kalender</title>

    <link rel="stylesheet" type="text/css" href="../CSS/global.css" media="all"/>
    <link rel="stylesheet" type="text/css" href="../CSS/calendar.css" media="all"/>
    <script language="JavaScript" src="../JavaScript/calendar.js"></script>
</head>
<body>

<?php

require 'functions.php';

// get month and year out of url
// for example calendar.php?year=2011&month=12

$month = date("n");
$year = date("Y");

if (isset($_GET['year'])) {
    $year = $_GET['year'];
}

if (isset($_GET['month'])) {
    $month = $_GET['month'];
}

$weekday = date("N", mktime(0, 0, 0, $month, 1, $year));
$numberOfDays = date("t", mktime(0, 0, 0, $month, 1, $year));

echo generate_header($month, $year);

$dataExists = false;
$publicNotesExists = false;
$fileName = "../Data/MonthPlan/" . $year . "-" . $month . ".txt";
$times = array();
$notesPublic = array();
$notesPrivate = array();
if (file_exists($fileName)) {
    $dataExists = true;
    $file = fopen($fileName, "r");

    for ($i = 1; $i <= $numberOfDays; $i++) {
        $times[$i] = fgets($file);
        $notesPublic[$i] = rtrim(fgets($file));
        if ($notesPublic[$i] != "") {
            $publicNotesExists = true;
        }
        $notesPrivate[$i] = rtrim(fgets($file));
    }
    fclose($file);
}

if ($publicNotesExists) {
    echo '<h2>Bemerkungen</h2>';
    echo '<table>';

    echo "<tr>";
    echo "<th>Datum</th>";
    echo "<th>Bemerkung</th>";
    echo "</tr>";


    for ($i = 1; $i <= $numberOfDays; $i++) {
        if ($notesPublic[$i] != "") {
            echo "<tr>";
            echo '<td class="date">' . get_short_date($year, $month, $i) . '</td>';
            echo '<td class="left">' . $notesPublic[$i] . '</td>';
            echo '</tr>';
        }
    }

    echo '</table>';
    echo '<br />';
}

echo '<h2>Eingabe der Daten</h2>';

echo '<table>';

echo "<tr>";
echo "<th>Mo</th>";
echo "<th>Di</th>";
echo "<th>Mi</th>";
echo "<th>Do</th>";
echo "<th>Fr</th>";
echo "<th>Sa</th>";
echo "<th>So</th>";
echo "</tr>";

echo "<tr>";

$cellCounter = 0;

// print empty cells
for ($i = 1; $i < $weekday; $i++) {
    echo "<td></td>";
    $cellCounter++;
}

// print days
for ($i = 1; $i <= $numberOfDays; $i++) {
    echo '<td class="bad" onclick="dateClicked(this)"><b>' . $i . '</b>';
    if ($dataExists) {
        echo '<br />' . $times[$i];
    }
    echo "</td>";
    $cellCounter++;

    if ($cellCounter % 7 == 0 && $i != $numberOfDays) {
        echo "</tr><tr>";
    }
}

// print empty cells
while ($cellCounter % 7 != 0) {
    echo "<td></td>";
    $cellCounter++;
}


echo "</tr>";
echo "</table>";

echo '<br />Name: <input id="name" type="text" />';

echo '<div id="year" class="hidden">' . $year . '</div>';
echo '<div id="month" class="hidden">' . $month . '</div>';



?>

<br />

<input type="button" value="Alle Daten markieren" onclick="markAllDates()" />
<input type="button" value="Speichern" onclick="save()" />

<br />

Antwort vom Server: <span id="httpResponse"></span>


</body>
</html>