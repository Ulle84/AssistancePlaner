<?php
session_start();

$year = $_POST['year'];
$month = $_POST['month'];

$fileName = "../Data/" . $_SESSION['client'] . "/Roster/" . $year . "-" . $month . ".txt";

if (file_exists($fileName)) {
    unlink($fileName);
}

echo "Dienstplan wurde gelöscht! Bitte Seite neu laden um automatisch einen neuen Dienstplan erstellen zu lassen!";

?>