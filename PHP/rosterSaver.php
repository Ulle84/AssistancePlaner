<?php
session_start();

$content = $_POST['content'];
$year = $_POST['year'];
$month = $_POST['month'];

$fileName = "../Data/" . $_SESSION['clientName'] . "/Roster/" . $year . "-" . $month . ".txt";

$filePath = substr($fileName, 0, strrpos($fileName, '/'));

if (!file_exists($filePath)) {
    mkdir($filePath, 0777, true);
}

$fh = fopen($fileName, "w");
fwrite($fh, date("d.m.Y H:i\n")); //date('Y-m-d H:i:s')
fwrite($fh, $content);
fclose($fh);

echo "Dienstplan wurde gespeichert";

?>