<!DOCTYPE html>
<meta charset="utf-8">
<html>
<head>
    <title>Assistenz Planer - Arbeitszeiten</title>
    <link rel="stylesheet" type="text/css" href="../CSS/global.css" media="all"/>
    <script language="JavaScript" src="../JavaScript/defaultTimes.js"></script>
</head>
<body>

<?php
require_once 'WorkingTimes.php';

$workingTimes = new WorkingTimes();
$workingTimes->readFromFile("../Data/Organization/defaultTimes.txt");
$workingTimes->printTable();

?>

<br/>

<input type="button" value="Speichern" onclick="save()"/>

<br/>

Antwort vom Server: <span id="httpResponse"></span>


</body>
</html>

