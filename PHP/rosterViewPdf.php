<?php

include('authentication.php');

require_once 'Roster.php';

$month = date("n");
$year = date("Y");

// switch to next month
$month++;
if ($month == 13) {
    $month = 1;
    $year++;
}

if (isset($_GET['year'])) {
    $year = $_GET['year'];
}

if (isset($_GET['month'])) {
    $month = $_GET['month'];
}

$roster = new Roster($year, $month, 5);
$roster->printPdf();

?>