<?php
session_start();

require_once('Team.php');
require_once('Passwords.php');

$content = $_POST['content'];

echo "Team-Mitglied wurde gespeichert";

?>