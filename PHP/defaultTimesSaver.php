<?php

$content = $_POST['content'];

$fileName = "../Data/Organization/defaultTimes.txt";
$fh = fopen($fileName, "w");
fwrite($fh, ($content));
fclose($fh);

echo "Eingaben wurden gespeichert";

?>