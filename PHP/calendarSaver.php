<?php
session_start();
require_once('AssistanceInput.php');

$content = $_POST['content'];
$notes = $_POST['notes'];
$year = $_POST['year'];
$month = $_POST['month'];
$userName = $_POST['userName'];

$assistanceInput = new AssistanceInput($year, $month);
$assistanceInput->assistanceInput[$userName] = explode(";", $content);
$assistanceInput->assistanceNotes[$userName] = str_replace("\n", "<br />", $notes);
$assistanceInput->saveToFile();

echo "Eingabe wurde gespeichert";

?>