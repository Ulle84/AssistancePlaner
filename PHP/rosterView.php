<?php include('authentication.php'); ?>
<!DOCTYPE html>
<meta charset="utf-8">
<html>
<head>
    <title>Assistenz Planer - Dienstplan</title>
    <link rel="stylesheet" type="text/css" href="../CSS/calendar.css" media="all"/>
    <link rel="stylesheet" type="text/css" href="../CSS/global.css" media="all"/>
    <link rel="stylesheet" type="text/css" href="../CSS/userInformation.css" media="all"/>
    <script language="JavaScript" src="../JavaScript/roster.js"></script>
</head>
<body onload="init()">
<?php include('userInformation.php'); ?>

<?php

require_once 'functions.php';
require_once 'AssistanceInput.php';
require_once 'Roster.php';

$month = date("n");
$year = date("Y");

if (isset($_GET['year'])) {
    $year = $_GET['year'];
}

if (isset($_GET['month'])) {
    $month = $_GET['month'];
}

$roster = new Roster($year, $month);
$roster->printHourTable();
$roster->printTable();

echo '<br/>';

echo '<input type="button" value="Dienstplan prüfen" onclick="checkRoster()"/>';
echo '<input type="button" value="Dienstplan speichern" onclick="save(' . $year . ', ' . $month . ')"/>';

?>

<br/>

Antwort vom Server: <span id="httpResponse"></span>

</body>
</html>