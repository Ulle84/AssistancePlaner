<?php include('authentication.php'); ?>
<!DOCTYPE html>
<meta charset="utf-8">
<html>
<head>
    <title>Assistenzplaner - Team</title>
    <link rel="stylesheet" type="text/css" href="../CSS/global.css" media="all"/>
    <script language="JavaScript" src="../JavaScript/inputValidation.js"></script>
    <script language="JavaScript" src="../JavaScript/teamBuisnessCard.js"></script>
</head>
<body>
<?php include('navigation.php'); ?>

<?php

require_once 'Team.php';

echo '<div id="main">';
if ($_SESSION['isClient']) {
    $team = new Team();

    $team->printAllTeamMembers();

    echo '<br/>';

    echo '<input type="button" value="Neues Mitglied" onclick="newMember()"/>';
}
else {
    echo 'Zugang nicht erlaubt!';
}
echo '</div>';
?>

</body>
</html>

