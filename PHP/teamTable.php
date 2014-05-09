<?php include('authentication.php'); ?>
<!DOCTYPE html>
<meta charset="utf-8">
<html>
<head>
    <title>Assistenz Planer - Team</title>
    <link rel="stylesheet" type="text/css" href="../CSS/global.css" media="all"/>
    <link rel="stylesheet" type="text/css" href="../CSS/userInformation.css" media="all"/>
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
}
?>

<br/>

<input type="button" value="Neues Mitglied" onclick="newMember()"/>
<input type="button" value="Tabelle speichern" onclick="saveTable()"/>

<br/>
Answer of Server: <span id="httpResponse"></span>

</body>
</html>

