<!DOCTYPE html>
<meta charset="utf-8">
<html>
<head>
    <title>Assistenz Planer - Dienstplan</title>
    <link rel="stylesheet" type="text/css" href="../CSS/calendar.css" media="all"/>
    <link rel="stylesheet" type="text/css" href="../CSS/global.css" media="all"/>
    <script language="JavaScript" src="../JavaScript/roster.js"></script>
</head>
<body onload="init()">

<?php

require 'functions.php';

$month = date("n");
$year = date("Y");

if (isset($_GET['year'])) {
    $year = $_GET['year'];
}

if (isset($_GET['month'])) {
    $month = $_GET['month'];
}

$numberOfDays = date("t", mktime(0, 0, 0, $month, 1, $year));

$fileName = "../Data/AssistanceInput/" . $year . "-" . $month . ".txt";

if (!file_exists($fileName)) {
    echo "no assistance input found";
    exit;
}

$file = fopen($fileName, "r");
$lineCounter = 0;
$dateSheet = array();
while (!feof($file)) {
    $name = fgets($file);
    $name = rtrim($name);

    $dates = fgets($file);

    if ($name != "") {
        $dateSheet[$name] = $dates;
    }
}
fclose($file);

$dataExists = false;
$fileName = "../Data/MonthPlan/" . $year . "-" . $month . ".txt";
$times = array();
$notesPublic = array();
$notesPrivate = array();
if (file_exists($fileName)) {
    $dataExists = true;
    $file = fopen($fileName, "r");

    for ($i = 1; $i <= $numberOfDays; $i++) {
        $times[$i] = rtrim(fgets($file));
        $notesPublic[$i] = rtrim(fgets($file));
        $notesPrivate[$i] = rtrim(fgets($file));
    }
    fclose($file);
} else {
    echo "no month plan found";
    exit;
}

echo generate_header($month, $year);

$rosterExists = false;
$servicePerson = array();
$standbyPerson = array();
$fileName = "../Data/Roster/" . $year . "-" . $month . ".txt";
if (file_exists($fileName)) {
    $rosterExists = true;
    $file = fopen($fileName, "r");

    echo '<div>Letzte Änderung des Dienstplans: ' . rtrim(fgets($file)) . '</div>';

    for ($i = 1; $i <= $numberOfDays; $i++) {
        $line = rtrim(fgets($file));
        $lineContent = explode(";", $line);
        $servicePerson[$i] = $lineContent[0];
        $standbyPerson[$i] = $lineContent[1];
    }

}

echo '<table id="rosterTable">';

echo '<tr class="rosterData">';
echo "<th>Datum</th>";
echo "<th>Zeit</th>";
echo '<th class="hidden">Dienst</th>';
echo '<th class="hidden">Bereitschaft</th>';

foreach ($dateSheet as $x => $x_value) {
    echo "<th>" . $x . "</th>";
}

echo '<th>Bemerkungen (öffentlich)</th>';

$showPrivateNotes = true; //TODO abhängig von Person
echo '<th';
if (!$showPrivateNotes) {
    echo ' class="hidden"';
}
echo '>Bemerkungen (privat)</th>';

$service = array();
$standby = array();

for ($i = 1; $i <= $numberOfDays; $i++) {
    $startTime = substr($times[$i], 0, 2);
    $endTime = substr($times[$i], 8, 2);

    $service[$i] = 24 - $startTime + $endTime - 6; // 6 Stunden in der Nacht werden abgezogen

    if ($service[$i] < 14) {
        $standby[$i] = 0.5;
    } else {
        $standby[$i] = 1.0;
    }
}


echo "</tr>";

for ($i = 1; $i <= $numberOfDays; $i++) {
    echo '<tr class="rosterData">';

    echo '<td class="date">';

    //echo get_weekday_description(date("N", mktime(0, 0, 0, $month, $i, $year)));

    //echo ", " . date("d.m.", mktime(0, 0, 0, $month, $i, $year)) . "</td>";

    echo get_short_date($year, $month, $i) . '</td>';

    echo '<td>' . $times[$i] . '</td>';

    echo '<td class="hidden">' . $service[$i] . '</td>';

    echo '<td class="hidden">' . $standby[$i] . '</td>';


    foreach ($dateSheet as $x => $x_value) {
        $allDates = explode(';', $x_value);

        $className = "";
        $cellTextContent = "";
        if (in_array($i, $allDates)) {
            $className = "good";
            if ($rosterExists) {
                if ($x == $standbyPerson[$i]) {
                    $className = "standby";
                    $cellTextContent = "Bereitschaft";
                }

                if ($x == $servicePerson[$i]) {
                    $className = "service";
                    $cellTextContent = "Dienst";
                }
            }
        } else {
            $className = "bad";
        }

        echo '<td onclick="entryClicked(this)" class="' . $className . '">' . $cellTextContent . '</td>';
    }

    echo '<td class="left">' . $notesPublic[$i] . '</td>';


    echo '<td class=';
    if (!$showPrivateNotes) {
        echo '"hidden"';
    } else {
        echo '"left"';
    }
    echo '>' . $notesPrivate[$i] . '</td>';

    echo "</tr>";

    if (date("N", mktime(0, 0, 0, $month, $i, $year)) == 7) {

        echo "<tr>";
        echo '<th>Datum</th>';
        echo '<th>Zeit</th>';
        echo '<th class="hidden">Dienst</th>';
        echo '<th class="hidden">Bereitschaft</th>';

        foreach ($dateSheet as $x => $x_value) {
            echo '<th>' . $x . "</th>";
        }

        echo '<th>Bemerkungen (öffentlich)</th>';

        echo '<th';
        if (!$showPrivateNotes) {
            echo ' class="hidden"';
        }
        echo '>Bemerkungen (privat)</th>';


        echo "</tr>";
    }
}

echo "</table>";

//TODO refaktoring - es ist doof, dass die ganze Zeit noch das File offen ist.
if (file_exists($fileName)) {
    fclose($file);
}

echo '<h1>Stundenübersicht</h1>';

$fileName = "../Data/Team/team.txt";
$teamHours = array();
if (file_exists($fileName)) {
    $teamExists = true;
    $file = fopen($fileName, "r");

    $numberOfTeamMembers = (int)rtrim(fgets($file));


    for ($i = 0; $i < $numberOfTeamMembers; $i++) {
        $firstName = rtrim(fgets($file));
        for ($j = 0; $j < 3; $j++) {
            fgets($file);
        }
        $teamHours[$firstName] = (int)rtrim(fgets($file));
        for ($j = 0; $j < 2; $j++) {
            fgets($file);
        }
    }
    fclose($file);
}


echo '<table id="hourTable">';

echo '<tr>';
echo '<th>Person</th>';
echo '<th>Stunden</th>';
echo '<th>Benötigte Stunden</th>';
echo '<th>Differenz in Stunden</th>';
echo '<th>Differenz in Prozent</th>';
echo '</tr>';

foreach ($dateSheet as $x => $x_value) {
    echo '<tr>';
    echo '<td class="left">' . $x . "</td>";
    echo '<td class="right" id="hours' . $x . '"></td>';
    echo '<td class="right">';
    if (array_key_exists($x, $teamHours)) {
        echo $teamHours[$x];
    } else {
        echo '0';
    }
    echo '</td>'; //TODO check that $x is in teamHours
    echo '<td class="right"></td>';
    echo '<td class="right"></td>';
    echo '</tr>';
}


echo '</table>';

echo '<div id="year" class="hidden">' . $year . '</div>';
echo '<div id="month" class="hidden">' . $month . '</div>';

?>

<br/>

<input type="button" value="Dienstplan prüfen" onclick="checkRoster()"/>
<input type="button" value="Dienstplan speichern" onclick="save()"/>

<br/>

Antwort vom Server: <span id="httpResponse"></span>

</body>
</html>