<?php

$content = $_POST['content'];

$fileName = "../Data/Team/team.txt";
$fh = fopen($fileName, "w");
fwrite($fh, ($content));
fclose($fh);

echo "Team wurde gespeichert";

?>