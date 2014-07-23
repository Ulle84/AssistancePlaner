<?php include('authentication.php'); ?>
<!DOCTYPE html>
<meta charset="utf-8">
<html>
<head>
    <title>Assistenz Planer - Übersicht</title>
    <link rel="stylesheet" type="text/css" href="../CSS/global.css" media="all"/>
</head>
<body>
<?php
include('navigation.php');
echo '<h1>Anwendung Assistenz Planer</h1>';
echo '<a href="rosterView.php">Dienst-Plan</a> <br/>';

//TODO Settings showToDoManager
//echo '<a href="toDoManagerView.php">Aufgaben</a> <br/>';


if ($_SESSION['admin']) {
    echo '<a href="monthPlanView.php">Monats-Plan</a> <br/>';
    echo '<a href="teamTable.php">Team</a> <br/>';
    echo '<a href="defaultTimes.php">Standard Dienst-Zeiten</a> <br/>';
}
else {
    echo '<a href="calendarView.php">Kalender</a> <br/>';
}

?>
</body>
</html>