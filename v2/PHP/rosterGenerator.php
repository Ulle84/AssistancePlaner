<?php
session_start();

require_once 'Roster.php';

$workingTimes = $_POST['workingTimes'];
$year = $_POST['year'];
$month = $_POST['month'];

$roster = new Roster($year, $month);
$roster->generateRoster($workingTimes);

?>