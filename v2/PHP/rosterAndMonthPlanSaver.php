<?php
session_start();

$contentRoster = $_POST['contentRoster'];
$contentMonthPlan = $_POST['contentMonthPlan'];

$year = $_POST['year'];
$month = $_POST['month'];

// save roster
if ($contentRoster != "") {
    $fileName = "../Data/" . strtolower($_SESSION['clientName']) . "/Roster/" . $year . "-" . $month . ".txt";

    $filePath = substr($fileName, 0, strrpos($fileName, '/'));

    if (!file_exists($filePath)) {
        mkdir($filePath, 0777, true);
    }

    $fh = fopen($fileName, "w");
    fwrite($fh, date("d.m.Y H:i\n")); //date('Y-m-d H:i:s')
    fwrite($fh, $contentRoster);
    fclose($fh);
}


// save month plan
$fileNameMonthPlan = "../Data/" . strtolower($_SESSION['clientName']) . "/MonthPlan/" . $year . "-" . $month . ".txt";

$filePathMonthPlan = substr($fileNameMonthPlan, 0, strrpos($fileNameMonthPlan, '/'));

if (!file_exists($filePathMonthPlan)) {
    mkdir($filePathMonthPlan, 0777, true);
}

$fhMonthPlan = fopen($fileNameMonthPlan, "w");
fwrite($fhMonthPlan, date("d.m.Y H:i\n")); //date('Y-m-d H:i:s')
fwrite($fhMonthPlan, ($contentMonthPlan));
fclose($fhMonthPlan);


if ($contentRoster != "") {
    echo "Dienstplan wurde gespeichert";
}

?>