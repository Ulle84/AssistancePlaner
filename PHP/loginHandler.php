<?php

require_once('Passwords.php');
require_once('Settings.php');

session_start();

$username = $_POST['username'];
$password = $_POST['password'];

$passwords = new Passwords();
if ($passwords->checkUser($username, $password)) {
    $_SESSION['loggedIn'] = true;
    $_SESSION['userName'] = $username;

    if ($username == "developer") {
        $_SESSION['developer'] = true;
    } else {
        $_SESSION['developer'] = false;
    }

    $settings = new Settings();
    if ($username == $settings->adminName) {
        $_SESSION['admin'] = true;
    } else {
        $_SESSION['admin'] = false;
    }

    if ($username == "SuperUser") {
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