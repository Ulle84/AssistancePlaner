<?php

require_once('Team.php');
require_once('Passwords.php');

$content = $_POST['content'];

$team = new Team();
$team->saveToFile($content);

$passwords = new Passwords();
$passwords->checkTeam($team->getLoginNames());

echo "Team wurde gespeichert";

?>