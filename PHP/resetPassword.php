<?php
session_start();
require_once('Passwords.php');

$userName = $_POST['userName'];

$passwords = new Passwords($_SESSION['client']);
$passwords->resetPassword($userName);

echo 'Passwort von ' . $userName . ' wurde zurückgesetzt.'

?>