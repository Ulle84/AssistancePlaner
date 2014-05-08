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

$month = date("n");
$year = date("Y");

if (isset($_GET['year'])) {
    $year = $_GET['year'];
}

if (isset($_GET['month'])) {
    $month = $_GET['month'];
}

$id = 'calendar';
$calendar = new MonthPlan($year, $month);
$calendar->calendarId = $id;
$calendar->printHeader();
$calendar->printPublicNotes();
$calendar->printCalendar();

echo '<br />Name: <input id="name" type="text" /><br />';

echo '<input type="button" value="Alle Daten markieren" onclick="markAllDates()" />';
echo '<input type="button" value="Speichern" onclick="save(' . $year . ', ' . $month . ', \'' . $id . '\')" />';

?>

<br />

Antwort vom Server: <span id="httpResponse"></span>



</body>
</html>