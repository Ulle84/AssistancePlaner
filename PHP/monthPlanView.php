<?php include('authentication.php'); ?>
<!DOCTYPE html>
<meta charset="utf-8">
<html>
<head>
    <title>Assistenzplaner - Monatsplan</title>
    <link rel="stylesheet" type="text/css" href="../CSS/global.css" media="all"/>
    <link rel="stylesheet" type="text/css" href="../CSS/calendar.css" media="all"/>
    <script language="JavaScript" src="../JavaScript/monthPlan.js"></script>
</head>
<body>
<?php include('navigation.php'); ?>

<?php
require_once 'MonthPlan.php';
require_once 'MonthNavigation.php';

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

if ($_SESSION['isClient']) {

    $navigation = new MonthNavigation(basename($_SERVER['PHP_SELF']), $year, $month);

    $monthInstance = new MonthPlan($year, $month);
    $monthInstance->printTable();
    $monthInstance->printNotesInputForAdmin();

    echo '<input type="button" value="Speichern" onclick="save(this, ' . $year . ', ' . $month . ')"/>';
    echo '<input type="button" value="Team benachrichtigen" onclick="notifyTeam(' . $year . ', ' . $month . ')"/>';

    echo '<br/>';

    echo 'Antwort vom Server: <span id="httpResponse"></span>';
} else {
    echo 'Zugang nicht erlaubt!';
}
?>


</body>
</html>

