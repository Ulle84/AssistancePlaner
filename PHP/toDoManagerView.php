<?php include('authentication.php'); ?>
<!DOCTYPE html>
<meta charset="utf-8">
<html>
<head>
    <title>Assistenz Planer - Aufgaben</title>
    <link rel="stylesheet" type="text/css" href="../CSS/global.css" media="all"/>
    <link rel="stylesheet" type="text/css" href="../CSS/userInformation.css" media="all"/>
    <link rel="stylesheet" type="text/css" href="../CSS/toDo.css" media="all"/>
    <script language="JavaScript" src="../JavaScript/toDoManager.js"></script>
    <script language="JavaScript" src="../JavaScript/DateExtended.js"></script>
</head>
<body onload="init()">
<?php include('userInformation.php'); ?>

<input type="button" onclick="test1()" value="test1" />
<input type="button" onclick="test2()" value="test2" />


<?php

require_once('ToDoManager.php');

$toDoManager = new ToDoManager();
$toDoManager->printToDoTable();
$toDoManager->printToDoSections();

?>

</body>
</html>