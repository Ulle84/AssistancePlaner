<?php
session_start();

require_once('Passwords.php');

$userName = $_POST['userName'];
$oldPassword = $_POST['oldPassword'];
$newPassword = $_POST['newPassword'];

$passwords = new Passwords($_SESSION['clientName']);

if (!$passwords->checkUser($userName, $oldPassword)) {
    echo 'Altes Passwort ist nicht korrekt!';
    exit;
}

$passwords->setPassword($userName, $newPassword);

echo 'Neues Passwort wurde gespeichert!';

?>