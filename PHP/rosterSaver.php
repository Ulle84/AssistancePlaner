<?php
session_start();

$content = $_POST['content'];
$year = $_POST['year'];
$month = $_POST['month'];
$lastChangeTime = $_POST['lastChangeTime'];
$publishedDate = $_POST['publishedDate'];
$closedDate = $_POST['closedDate'];
$uniqueID = $_POST['uniqueID'];

$fileName = "../Data/" . strtolower($_SESSION['clientName']) . "/Roster/" . $year . "-" . $month . ".txt";

$filePath = substr($fileName, 0, strrpos($fileName, '/'));

if (!file_exists($filePath)) {
    mkdir($filePath, 0777, true);
}

$fh = fopen($fileName, "w");
fwrite($fh, $lastChangeTime . "\n");
fwrite($fh, $publishedDate . "\n");
fwrite($fh, $closedDate . "\n");
fwrite($fh, $uniqueID . "\n");
fwrite($fh, $content);
fclose($fh);

echo $lastChangeTime;

?>