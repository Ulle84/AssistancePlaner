<?php
session_start();

require_once('Team.php');
require_once('TeamMember.php');

$teamMember = new TeamMember();
$teamMember->loginName = $_POST['id'];
$teamMember->firstName = $_POST['firstName'];
$teamMember->lastName = $_POST['lastName'];
$teamMember->eMailAddress = $_POST['eMailAddress'];
$teamMember->phoneNumber = $_POST['phoneNumber'];
$teamMember->keyWords = explode(" ", $_POST['keyWords']);
$teamMember->hoursPerMonth = $_POST['hoursPerMonth'];
$teamMember->priority = $_POST['priority'];
$teamMember->preferredWeekdays = explode(";", $_POST['preferredWeekdays']);

$team = new Team();
$team->saveMember($_POST['oldId'], $teamMember);

echo "OK";
?>