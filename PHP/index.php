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
include('userInformation.php');
echo '<h1>Anwendung Assistenz Planer</h1>';
echo '<a href="rosterView.php">Dienst-Plan</a> <br/>';
echo '<a href="toDoManagerView.php">Aufgaben</a> <br/>';

if (!$_SESSION['admin']) {
    echo '<a href="calendarView.php">Kalender</a> <br/>';
}

if ($_SESSION['developer'] || $_SESSION['admin']) {
    echo '<a href="monthPlanView.php">Monats-Plan</a> <br/>';
    echo '<a href="teamTable.php">Team</a> <br/>';
    echo '<a href="defaultTimes.php">Standard Dienst-Zeiten</a> <br/>';
}

if ($_SESSION['developer']) {
    echo '<h1>Dokumentation</h1>';

    echo '<a href="../HTML/diary.html">Tägliche Notizen</a> <br/>';
    echo '<a href="../HTML/ToDo.html">ToDo</a> <br/>';
    echo '<a href="../HTML/userStories.html">User Stories</a> <br/>';
    echo '<a href="../HTML/info.html">Informationen</a> <br/>';
    echo '<a href="../HTML/problems.html">Probleme und Lösungen</a> <br/>';
    echo '<a href="../HTML/algorithm.html">Algorithmus</a> <br/>';


    echo '<h1>Sonstiges</h1>';

    echo '<a href="../PHP/phpInfo.php">PHP Information</a> <br/>';
}

?>
</body>
</html>