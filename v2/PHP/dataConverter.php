<?php
session_start();

$month = date("n");
$year = date("Y");

if (isset($_GET['year'])) {
    $year = $_GET['year'];
}

if (isset($_GET['month'])) {
    $month = $_GET['month'];
}

$daysPerMonth = date("t", mktime(0, 0, 0, $month, 1, $year));

$fileNameRoster = "../Data/" . $_SESSION['clientName'] . "/Roster/" . $year . "-" . $month . ".txt";
$fileNameMonthPlan = "../Data/" . $_SESSION['clientName'] . "/MonthPlan/" . $year . "-" . $month . ".txt";
$fileNameOutput = "../Data/" . $_SESSION['clientName'] . "/Roster/" . $year . "-" . $month . "_new.txt";

$lastChange = "";
$times = array();
$publicNotes = array();
$privateNotes = array();
$servicePerson = array();
$standbyPerson = array();
$notes = array();

if (file_exists($fileNameMonthPlan)) {
    $file = fopen($fileNameMonthPlan, "r");

    for ($i = 1; $i <= $daysPerMonth; $i++) {
        $times[$i] = rtrim(fgets($file));
        $publicNotes[$i] = rtrim(fgets($file));
        $privateNotes[$i] = rtrim(fgets($file));
    }

    while (!feof($file)) {
        $line = rtrim(fgets($file));
        array_push($notes, $line);
    }

    fclose($file);
}

if (file_exists($fileNameRoster)) {
    $file = fopen($fileNameRoster, "r");

    $lastChange = rtrim(fgets($file));

    for ($i = 1; $i <= $daysPerMonth; $i++) {
        $persons = explode(";", rtrim(fgets($file)));;
        $servicePerson[$i] = $persons[0];
        $standbyPerson[$i] = $persons[1];
    }

    fclose($file);
}


$fh = fopen($fileNameOutput, "w");
fwrite($fh, $lastChange . "\n");

for ($i = 1; $i <= $daysPerMonth; $i++) {
    fwrite($fh, $times[$i] . "\n");
    fwrite($fh, $publicNotes[$i] . "\n");
    fwrite($fh, $privateNotes[$i] . "\n");
    fwrite($fh, $servicePerson[$i] . "\n");
    fwrite($fh, $standbyPerson[$i] . "\n");
}

fwrite($fh, implode("\n", $notes));

fclose($fh);

?>