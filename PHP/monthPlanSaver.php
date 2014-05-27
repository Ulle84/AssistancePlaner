<?php

$content = $_POST['content'];
$year = $_POST['year'];
$month = $_POST['month'];

$fileName = "../Data/MonthPlan/" . $year . "-" . $month . ".txt";

$filePath = substr($fileName, 0, strrpos($fileName, '/'));

if (!file_exists($filePath)) {
    mkdir($filePath, 0777, true);
}

$fh = fopen($fileName, "w");
fwrite($fh, ($content));
fclose($fh);

echo "Monatsplan wurde gespeichert";

?>