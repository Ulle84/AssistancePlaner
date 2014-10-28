<?php
session_start();

$content = $_POST['content'];
$year = $_POST['year'];
$month = $_POST['month'];

$fileName = "../Data/" . strtolower($_SESSION['clientName']) . "/Roster/" . $year . "-" . $month . ".txt";

$filePath = substr($fileName, 0, strrpos($fileName, '/'));

if (!file_exists($filePath)) {
    mkdir($filePath, 0777, true);
}

$now = date("d.m.Y H:i"); //date('Y-m-d H:i:s')

$fh = fopen($fileName, "w");
fwrite($fh, $now . "\n");
fwrite($fh, $content);
fclose($fh);

echo "Dienstplan wurde gespeichert";

?>