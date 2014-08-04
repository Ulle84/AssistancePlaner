<?php include('authentication.php'); ?>

<!DOCTYPE html>
<meta charset="utf-8">
<html>
<head>
    <title>Assistenzplaner - Arbeitszeiten</title>
    <link rel="stylesheet" type="text/css" href="../CSS/global.css" media="all"/>
    <script language="JavaScript" src="../JavaScript/defaultTimes.js"></script>
</head>
<body>
<?php include('navigation.php'); ?>

<?php
require_once 'WorkingTimes.php';

if ($_SESSION['isClient']) {

    $workingTimes = new WorkingTimes();
    $workingTimes->printTable();

    echo '<br/>';
    echo '<input type="button" value="Speichern" onclick="save(this)"/>';
    echo '<br/>';
    echo 'Antwort vom Server: <span id="httpResponse"></span>';
} else {
    echo 'Zugang nicht erlaubt!';
}
?>


</body>
</html>

