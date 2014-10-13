<?php include('authentication.php'); ?>
<!DOCTYPE html>
<meta charset="utf-8">
<html>
<head>
    <title>Assistenzplaner - Team</title>
    <link rel="stylesheet" type="text/css" href="../CSS/global.css" media="all"/>
    <script language="JavaScript" src="../JavaScript/team.js"></script>
</head>
<body>
<?php include('navigation.php'); ?>

<?php

require_once 'Team.php';

echo '<div id="main">';
if ($_SESSION['isClient']) {
    $team = new Team();

    $team->printAllTeamMembers();

    echo 'ToDo';


    echo '<br/>';

    echo '<input type="button" value="Neues Mitglied" onclick="newMember()"/>';
    echo '<input type="button" value="Team speichern" onclick="saveTeam(this)"/>';

    echo '<br/>';
    echo 'Antwort des Servers: <span id="httpResponse"></span>';
}
else {
    echo 'Zugang nicht erlaubt!';
}
echo '</div>';
?>

</body>
</html>

