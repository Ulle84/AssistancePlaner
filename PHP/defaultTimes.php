<?php include('authentication.php'); ?>

<!DOCTYPE html>
<meta charset="utf-8">
<html>
<head>
    <title>Assistenz Planer - Arbeitszeiten</title>
    <link rel="stylesheet" type="text/css" href="../CSS/global.css" media="all"/>
    <link rel="stylesheet" type="text/css" href="../CSS/userInformation.css" media="all"/>
    <script language="JavaScript" src="../JavaScript/defaultTimes.js"></script>
</head>
<body>
<?php include('userInformation.php'); ?>

<?php
require_once 'WorkingTimes.php';

if ($_SESSION['developer'] || $_SESSION['admin']) {

    $workingTimes = new WorkingTimes();
    $workingTimes->printTable();

    echo '<br/>';
    echo '<input type="button" value="Speichern" onclick="save()"/>';
    echo '<br/>';
    echo 'Antwort vom Server: <span id="httpResponse"></span>';
} else {
    echo 'Zugang nicht erlaubt!';
}
?>


</body>
</html>

