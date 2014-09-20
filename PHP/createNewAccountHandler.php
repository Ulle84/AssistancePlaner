<?php
session_start();

require_once('Passwords.php');

$clientName = $_POST['clientName'];
$password = $_POST['password'];
$assistant = "";

$fileName = "../Data/" . $clientName . "/Team/passwords.txt";

$filePath = substr($fileName, 0, strrpos($fileName, '/'));

if (file_exists($fileName)) {
    echo 'Der Klient ' . $clientName . ' wurde bereits angelegt.';
} else {
    mkdir($filePath, 0777, true);

    $passwords = new Passwords($clientName);
    $passwords->addUser("", $password);

    $_SESSION['isLoggedIn'] = true;
    $_SESSION['assistantName'] = $assistant;
    $_SESSION['clientName'] = $clientName;

    if ($assistant == "") {
        $_SESSION['isClient'] = true;
    } else {
        $_SESSION['isClient'] = false;
    }

    echo "OK";

    //echo 'Der Klient ' . $clientName . ' wurde gerade neu angelegt. <br /> Bitte <a href="login.php?client=' . $clientName . '">anmelden!';
}
?>