<?php include('authentication.php'); ?>
<!DOCTYPE html>
<meta charset="utf-8">
<html>
<head>
    <title>Assistenz Planer - Kalender</title>

    <link rel="stylesheet" type="text/css" href="../CSS/global.css" media="all"/>
    <link rel="stylesheet" type="text/css" href="../CSS/calendar.css" media="all"/>
    <script language="JavaScript" src="../JavaScript/calendar.js"></script>
</head>
<body>
<?php include('navigation.php'); ?>

<?php

require_once "MonthPlan.php";
require_once "MonthNavigation.php";

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

$navigation = new MonthNavigation(basename($_SERVER['PHP_SELF']), $year, $month);

$id = 'calendar';
$calendar = new MonthPlan($year, $month);
$calendar->calendarId = $id;
$calendar->printHeader();
$calendar->printNotesFromAdmin();
$calendar->printPublicNotes();
$calendar->printCalendar();
echo '<br /><input type="button" value="Alle Daten markieren" onclick="markAllDates()" />';

$calendar->printNotesInputForAssistant();

echo '<input type="button" value="Speichern" onclick="save(this, \'' . $_SESSION['userName'] . '\', ' . $year . ', ' . $month . ', \'' . $id . '\')" />';

?>

<br />

Antwort vom Server: <span id="httpResponse"></span>



</body>
</html>