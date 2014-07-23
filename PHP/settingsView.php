<?php include('authentication.php'); ?>
<!DOCTYPE html>
<meta charset="utf-8">
<html>
<head>
    <title>Assistenz Planer - Einstellungen</title>
    <link rel="stylesheet" type="text/css" href="../CSS/global.css" media="all"/>
    <script language="JavaScript" src="../JavaScript/settings.js"></script>
</head>
<body>
<?php include('navigation.php'); ?>

<?php

require_once 'Settings.php';

if ($_SESSION['developer'] || $_SESSION['admin']) {
    $team = new Team();
    $team->setTableId("team");
    $team->printTable();


    echo '<br/>';

    echo '<input type="button" value="Neues Mitglied" onclick="newMember()"/>';
    echo '<input type="button" value="Team speichern" onclick="saveTeam(this)"/>';

    echo '<br/>';
    echo 'Antwort des Servers: <span id="httpResponse"></span>';
}
else {
    echo 'Zugang nicht erlaubt!';
}
?>
</body>
</html>

