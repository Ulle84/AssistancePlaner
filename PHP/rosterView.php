<?php include('authentication.php'); ?>
<!DOCTYPE html>
<meta charset="utf-8">
<html>
<head>
    <title>Assistenz Planer - Dienstplan</title>
    <link rel="stylesheet" type="text/css" href="../CSS/calendar.css" media="all"/>
    <link rel="stylesheet" type="text/css" href="../CSS/global.css" media="all"/>
    <script language="JavaScript" src="../JavaScript/DateExtended.js"></script>
    <script language="JavaScript" src="../JavaScript/roster.js"></script>
</head>
<body onload="init()">
<?php include('navigation.php'); ?>

<?php

require_once 'functions.php';
require_once 'AssistanceInput.php';
require_once 'Roster.php';
require_once 'MonthNavigation.php';

$month = date("n");
$year = date("Y");

// switch to next month
//TODO switch only if not assistant
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

$navigation = new MonthNavigation(basename($_SERVER['PHP_SELF']), $year, $month);

$roster = new Roster($year, $month);

if ($_SESSION['developer'] || $_SESSION['admin']) {
    $roster->printNotesFromAssistants();
    $roster->printTableAdmin();
    $roster->printHourTable();

    echo '<br/>';
    echo '<input type="button" value="Dienstplan prüfen" onclick="checkRoster(1)"/>';
    echo '<input type="button" value="Dienstplan speichern" onclick="save(this, ' . $year . ', ' . $month . ')"/>';
    echo '<input type="button" value="Dienstplan als PDF anzeigen" onclick="createPdf(this, ' . $year . ', ' . $month . ')"/>';

    echo '<br/>';

    echo 'Antwort vom Server: <span id="httpResponse"></span>';
}
else {
    $roster->printTablesAssistant();
}

?>

</body>
</html>