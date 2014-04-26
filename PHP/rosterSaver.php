<?php

$content = $_POST['content'];
$year = $_POST['year'];
$month = $_POST['month'];

echo "roster was saved";

// save to file
$fileName = "../Data/Roster/" . $year . "-" . $month . ".txt";
$fh = fopen($fileName, "w");
fwrite($fh, ($content . "\r\n")); // add newline for next time
fclose($fh);

?>