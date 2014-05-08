<?php
require_once('AssistanceInput.php');

$content = $_POST['content'];
$year = $_POST['year'];
$month = $_POST['month'];
$userName = $_POST['userName'];

$assistanceInput = new AssistanceInput($year, $month);
$assistanceInput->assistanceInput[$userName] = explode(";", $content);
$assistanceInput->saveToFile();

echo "Eingabe wurde gespeichert";

?>