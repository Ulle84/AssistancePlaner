<?php
session_start();

require_once('Passwords.php');
require_once('Settings.php');

$assistant = $_POST['assistant'];
$client = $_POST['client'];
$password = $_POST['password'];

$passwords = new Passwords($client);
if ($passwords->checkUser($assistant, $password)) {
    $_SESSION['loggedIn'] = true;
    $_SESSION['userName'] = $assistant;
    $_SESSION['client'] = $client;
    $_SESSION['developer'] = false;

    $settings = new Settings();
    if ($assistant == "") {
        $_SESSION['admin'] = true;
    } else {
        $_SESSION['admin'] = false;
    }

    if ($assistant == "SuperUser") {
        $_SESSION['admin'] = true;
        $_SESSION['developer'] = true;
    }

    if ($password == $settings->standardPassword) {
        echo 'changePassword';
    } else {
        echo 'OK';
    }

} else {
    echo 'Falsches Passwort eingegeben!';
}

?>