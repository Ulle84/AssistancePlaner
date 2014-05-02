<!DOCTYPE html>
<meta charset="utf-8">
<html>
<head>
    <title>Assistenz Planer - Monatsplan</title>
    <link rel="stylesheet" type="text/css" href="../CSS/global.css" media="all"/>
    <link rel="stylesheet" type="text/css" href="../CSS/calendar.css" media="all"/>
    <script language="JavaScript" src="../JavaScript/monthPlan.js"></script>
</head>
<body>

<?php
require_once 'MonthPlan.php';

$month = date("n");
$year = date("Y");

if (isset($_GET['year'])) {
    $year = $_GET['year'];
}

if (isset($_GET['month'])) {
    $month = $_GET['month'];
}

$monthInstance = new MonthPlan($year, $month);
$monthInstance->readFromFile("../Data/MonthPlan/" . $year . "-" . $month . ".txt");
$monthInstance->printTable();

echo '<br />';
echo '<input type="button" value="Speichern" onclick="save(' . $year . ', ' . $month . ')"/>';

?>

<br/>

Antwort vom Server: <span id="httpResponse"></span>


</body>
</html>

