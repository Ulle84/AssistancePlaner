<?php include('authentication.php'); ?>

<!DOCTYPE html>
<meta charset="utf-8">
<html>
<head>
    <title>Assistenz Planer - Arbeitszeiten</title>
    <link rel="stylesheet" type="text/css" href="../CSS/global.css" media="all"/>
    <script language="JavaScript" src="../JavaScript/defaultTimes.js"></script>
</head>
<body>
<?php include('userInformation.php'); ?>

<?php
require_once 'WorkingTimes.php';

$workingTimes = new WorkingTimes();
$workingTimes->printTable();

?>

<br/>

<input type="button" value="Speichern" onclick="save()"/>

<br/>

Antwort vom Server: <span id="httpResponse"></span>


</body>
</html>

