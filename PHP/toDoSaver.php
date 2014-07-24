<?php
session_start();

$toDo = $_POST['toDo'];
$done = $_POST['done'];

$fileNameToDo = "../Data/" . $_SESSION['client'] . "/ToDoManager/toDos.txt";

$filePath = substr($fileNameToDo, 0, strrpos($fileNameToDo, '/'));

if (!file_exists($filePath)) {
    mkdir($filePath, 0777, true);
}

$fileToDo = fopen($fileNameToDo, "w");
fwrite($fileToDo, $toDo);
fclose($fileToDo);

$fileNameDone = "../Data/" . $_SESSION['client'] . "/ToDoManager/done.txt";

$filePath = substr($fileNameDone, 0, strrpos($fileNameDone, '/'));

if (!file_exists($filePath)) {
    mkdir($filePath, 0777, true);
}

$fileDone = fopen($fileNameDone, "a");
fwrite($fileDone, $done);
fclose($fileDone);

echo "Änderungen wurden gespeichert.";

?>