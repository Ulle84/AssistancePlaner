<?php

$content = $_POST['content'];
$year = $_POST['year'];
$month = $_POST['month'];
$name = $_POST['name'];

$fileName = "../Data/AssistanceInput/" . $year . "-" . $month . ".txt";
$fh = fopen($fileName, "a");
fwrite($fh, ($name . "\r\n"));
fwrite($fh, ($content . "\r\n"));
fclose($fh);

echo "Eingabe wurde gespeichert";

?>