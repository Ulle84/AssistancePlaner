<?php

$content = $_POST['content'];
$year = $_POST['year'];
$month = $_POST['month'];

$fileName = "../Data/MonthPlan/" . $year . "-" . $month . ".txt";
$fh = fopen($fileName, "w");
fwrite($fh, ($content));
fclose($fh);

echo "Monatsplan wurde gespeichert";

?>