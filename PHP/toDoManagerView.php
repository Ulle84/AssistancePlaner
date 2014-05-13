<?php include('authentication.php'); ?>
<!DOCTYPE html>
<meta charset="utf-8">
<html>
<head>
    <title>Assistenz Planer - Aufgaben</title>
    <link rel="stylesheet" type="text/css" href="../CSS/global.css" media="all"/>
    <link rel="stylesheet" type="text/css" href="../CSS/userInformation.css" media="all"/>
    <script language="JavaScript" src="../JavaScript/toDoManager.js"></script>
    <script language="JavaScript" src="../JavaScript/DateExtended.js"></script>
</head>
<body onload="init()">
<?php include('userInformation.php'); ?>

<?php

require_once('ToDoManager.php');

$toDoManager = new ToDoManager();
$toDoManager->printToDoTable();
$toDoManager->printToDoSections();

?>

</body>
</html>