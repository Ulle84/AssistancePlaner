<?php

$content = $_POST['content'];

// save to file
$fileName = "../Data/Team/team.txt";
$fh = fopen($fileName, "w");
fwrite($fh,($content));
fclose($fh);

echo "team was saved";

?>