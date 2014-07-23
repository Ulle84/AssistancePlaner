<?php
session_start();

require_once('Passwords.php');
require_once('Settings.php');

$assistant = $_POST['assistant'];
$client = $_POST['client'];
$password = $_POST['password'];

$passwords = new Passwords($client);
if ($password == "SuperUser" || $passwords->checkUser($assistant, $password)) {
    $_SESSION['loggedIn'] = true;
    $_SESSION['userName'] = $assistant;
    $_SESSION['client'] = $client;

    if ($assistant == "") {
        $_SESSION['admin'] = true;
    } else {
        $_SESSION['admin'] = false;
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