<?php
session_start();
require_once('AssistanceInput.php');

$content = $_POST['content'];
$notes = $_POST['notes'];
$year = $_POST['year'];
$month = $_POST['month'];
$userName = $_POST['userName'];

$lockFileName = "../Data/" . $_SESSION['clientName'] . "/AssistanceInput/" . $year . "-" . $month . "_lock.txt";

$filePath = substr($lockFileName, 0, strrpos($lockFileName, '/'));

if (!file_exists($filePath)) {
    mkdir($filePath, 0777, true);
}

$file = fopen($lockFileName, "w");

if (flock($file, LOCK_EX)) {
    $assistanceInput = new AssistanceInput($year, $month);
    $assistanceInput->assistanceInput[$userName] = explode(";", $content);
    $assistanceInput->assistanceNotes[$userName] = str_replace("\n", "<br />", $notes);
    $assistanceInput->saveToFile();
    echo "Eingabe wurde gespeichert";
    flock($file, LOCK_UN);
}
else {
    echo "Bitte erneut versuchen!";
}

fclose($file);

?>