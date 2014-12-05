<?php
session_start();

require_once 'Roster.php';

$month = date("n");
$year = date("Y");
$id = $_GET['id'];

$_SESSION['clientName'] = $_GET['clientName'];

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

$roster = new Roster($year, $month);

if ($roster->checkId($id))
{
    $roster->printPdf();
}
else
{
    echo 'Access forbidden!';
}



?>