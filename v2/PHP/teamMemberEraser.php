<?php
session_start();

require_once('Team.php');

$team = new Team();
$team->removeMember($_POST['id']);

echo $_POST['id'] . " wurde gelöscht.";
?>