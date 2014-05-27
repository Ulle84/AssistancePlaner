<?php

$content = $_POST['content'];

$fileName = "../Data/Organization/defaultTimes.txt";

$filePath = substr($fileName, 0, strrpos($fileName, '/'));

if (!file_exists($filePath)) {
    mkdir($filePath, 0777, true);
}

$fh = fopen($fileName, "w");
fwrite($fh, ($content));
fclose($fh);

echo "Eingaben wurden gespeichert";

?>