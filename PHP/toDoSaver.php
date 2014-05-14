<?php

$toDo = $_POST['toDo'];
$done = $_POST['done'];

$fileNameToDo = "../Data/ToDoManager/toDos.txt";
$fileToDo = fopen($fileNameToDo, "w");
fwrite($fileToDo, $toDo);
fclose($fileToDo);

$fileNameDone = "../Data/ToDoManager/done.txt";
$fileDone = fopen($fileNameDone, "a");
fwrite($fileDone, $done);
fclose($fileDone);

echo "Änderungen wurden gespeichert.";

?>