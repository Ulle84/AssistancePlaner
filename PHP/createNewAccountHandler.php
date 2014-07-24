<?php
session_start();

require_once('Passwords.php');

$clientName = $_POST['clientName'];
$password = $_POST['password'];

$fileName = "../Data/" . $clientName . "/Team/passwords.txt";

$filePath = substr($fileName, 0, strrpos($fileName, '/'));

if (file_exists($fileName)) {
    echo 'Der Klient ' . $clientName . ' wurde bereits angelegt.';
}
else {
    mkdir($filePath, 0777, true);

    $passwords = new Passwords($clientName);
    $passwords->addUser("", $password);

    echo 'Der Klient ' . $clientName . ' wurde gerade neu angelegt. <br /> Bitte <a href="login.php?client=' . $clientName . '">anmelden!';
}
?>