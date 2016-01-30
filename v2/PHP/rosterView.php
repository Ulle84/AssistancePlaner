<?php include('authentication.php'); ?>
<!DOCTYPE html>
<meta charset="utf-8">
<html>
<head>
    <title>Assistenzplaner - Dienstplan</title>
    <link rel="stylesheet" type="text/css" href="../CSS/calendar.css" media="all"/>
    <link rel="stylesheet" type="text/css" href="../CSS/global.css" media="all"/>
    <script language="JavaScript" src="../JavaScript/inputValidation.js"></script>
    <script language="JavaScript" src="../JavaScript/DateExtended.js"></script>
    <script language="JavaScript" src="../JavaScript/roster.js"></script>
</head>
<?php

require_once 'functions.php';
require_once 'AssistanceInput.php';
require_once 'Roster.php';
require_once 'MonthNavigation.php';

$month = date("n");
$year = date("Y");
$rosterAlgorithmVersion = 5;

// switch to next month if client
if ($_SESSION['isClient']) {
    $month++;
    if ($month == 13) {
        $month = 1;
        $year++;
    }
}

if (isset($_GET['year'])) {
    $year = $_GET['year'];
}

if (isset($_GET['month'])) {
    $month = $_GET['month'];
}

echo '<body onload="init(' . $year . ', ' . $month . ')">';
include('navigation.php');
echo '<div id="dirtyPositionHack" style="background-color: white; min-width: 1px; min-height: 1px; float: left;"></div>';

echo '<div id="main">';

$navigation = new MonthNavigation(basename($_SERVER['PHP_SELF']), $year, $month);

$roster = new Roster($year, $month);


if ($_SESSION['isClient']) {
    $roster->printTableClient();
    $roster->printHourTable();
    $roster->printButtons();
    $roster->printNotesInputForAdmin();
    $roster->printNotesFromAssistants();
}
else {
    $roster->printTablesAssistant();
}
echo '</div>';

?>

</body>
</html>