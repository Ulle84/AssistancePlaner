<?php

// http://assistenzplaner.de/PHP/alive.php?name=horst

$name = $_GET['name'];

$year = date("Y");
$month = date("n");
$day = date("d");

if (strlen($month) < 2) {
    $month = '0' . $month;
}

if (strlen($day) < 2) {
    $day = '0' . $day;
}

$fileName = "../alive/" . strtolower($name) . "/" . $year . "/" . $year . "-" . $month . "-" . $day . ".txt";

$filePath = substr($fileName, 0, strrpos($fileName, '/'));

if (!file_exists($filePath)) {
    mkdir($filePath, 0777, true);
}

$fh = fopen($fileName, "a");
fwrite($fh, date("d.m.Y H:i:s\n")); //date('Y-m-d H:i:s')
fclose($fh);

echo 'ok ' . $name;

?>