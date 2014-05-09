<?php include('authentication.php'); ?>
<!DOCTYPE html>
<meta charset="utf-8">
<html>
<head>
    <title>Assistenz Planer - Kalender</title>

    <link rel="stylesheet" type="text/css" href="../CSS/global.css" media="all"/>
    <link rel="stylesheet" type="text/css" href="../CSS/calendar.css" media="all"/>
    <link rel="stylesheet" type="text/css" href="../CSS/userInformation.css" media="all"/>
    <script language="JavaScript" src="../JavaScript/calendar.js"></script>
</head>
<body>
<?php include('userInformation.php'); ?>

<?php

require_once "MonthPlan.php";
require_once "MonthNavigation.php";

$month = date("n");
$year = date("Y");

if (isset($_GET['year'])) {
    $year = $_GET['year'];
}

if (isset($_GET['month'])) {
    $month = $_GET['month'];
}

$navigation = new MonthNavigation(basename($_SERVER['PHP_SELF']), $year, $month);

$id = 'calendar';
$calendar = new MonthPlan($year, $month);
$calendar->calendarId = $id;
$calendar->printHeader();
$calendar->printPublicNotes();
$calendar->printCalendar();

echo '<br /><input type="button" value="Alle Daten markieren" onclick="markAllDates()" />';
echo '<input type="button" value="Speichern" onclick="save(\'' . $_SESSION['userName'] . '\', ' . $year . ', ' . $month . ', \'' . $id . '\')" />';

?>

<br />

Antwort vom Server: <span id="httpResponse"></span>



</body>
</html>