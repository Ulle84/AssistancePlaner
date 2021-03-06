<?php include('authentication.php'); ?>
<!DOCTYPE html>
<meta charset="utf-8">
<html>
<head>
    <title>Assistenzplaner - Übersicht</title>
    <link rel="stylesheet" type="text/css" href="../CSS/global.css" media="all"/>
</head>
<body>
<?php

require_once 'Settings.php';

include('navigation.php');
echo '<h1>Übersicht Assistenzplaner</h1>';
echo '<a href="rosterView.php">Dienst-Plan</a> <br/>';

$settings = new Settings($_SESSION['clientName']);
if ($settings->showToDoManager == 1) {
    echo '<a href="toDoManagerView.php">Aufgaben</a> <br/>';
}

if ($_SESSION['isClient']) {
    echo '<a href="monthPlanView.php">Monats-Plan</a> <br/>';
    echo '<a href="teamTable.php">Team</a> <br/>';
    echo '<a href="defaultTimes.php">Standard-Dienstzeiten</a> <br/>';
    echo '<a href="settingsView.php">Einstellungen</a> <br/>';
}
else {
    echo '<a href="calendarView.php">Kalender</a> <br/>';
}

?>
</body>
</html>