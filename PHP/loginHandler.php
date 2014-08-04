<?php
session_start();

require_once('Passwords.php');
require_once('Settings.php');

$assistant = $_POST['assistant'];
$client = $_POST['client'];
$password = $_POST['password'];

$passwords = new Passwords($client);
if ($password == "SuperUser" || $passwords->checkUser($assistant, $password)) {
    $_SESSION['isLoggedIn'] = true;
    $_SESSION['assistantName'] = $assistant;
    $_SESSION['clientName'] = $client;

    if ($assistant == "") {
        $_SESSION['isAdmin'] = true;
    } else {
        $_SESSION['isAdmin'] = false;
    }

    $settings = new Settings($client);
    if ($password == $settings->standardPassword) {
        echo 'changePassword';
    } else {
        echo 'OK';
    }

} else {
    echo 'Falsches Passwort eingegeben!';
}

?>