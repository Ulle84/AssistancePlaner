<?php include('authentication.php'); ?>
<!DOCTYPE html>
<meta charset="utf-8">
<html>
<head>
    <title>Assistenz Planer - Team</title>
    <link rel="stylesheet" type="text/css" href="../CSS/global.css" media="all"/>
    <script language="JavaScript" src="../JavaScript/team.js"></script>
</head>
<body>
<?php include('userInformation.php'); ?>

<?php

require_once 'Team.php';

if ($_SESSION['developer'] || $_SESSION['admin']) {
    $team = new Team();
    $team->setTableId("team");
    $team->printTable();


    echo '<br/>';

    echo '<input type="button" value="Neues Mitglied" onclick="newMember()"/>';
    echo '<input type="button" value="Tabelle speichern" onclick="saveTable()"/>';

    echo '<br/>';
    echo 'Answer of Server: <span id="httpResponse"></span>';
}
else {
    echo 'Zugang nicht erlaubt!';
}
?>
</body>
</html>

