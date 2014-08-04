<?php include('authentication.php'); ?>
<!DOCTYPE html>
<meta charset="utf-8">
<html>
<head>
    <title>Assistenzplaner - Aufgaben</title>
    <link rel="stylesheet" type="text/css" href="../CSS/global.css" media="all"/>
    <link rel="stylesheet" type="text/css" href="../CSS/toDo.css" media="all"/>
    <script language="JavaScript" src="../JavaScript/DateExtended.js"></script>
    <script language="JavaScript" src="../JavaScript/toDoManager.js"></script>
    <script language="JavaScript" src="../JavaScript/datePicker.js"></script>
</head>
<body onload="init()">
<?php include('navigation.php'); ?>

<?php
require_once('ToDoManager.php');

$toDoManager = new ToDoManager();

if ($_SESSION['isAdmin']) {
    $toDoManager->printToDoInput();
}

$toDoManager->printToDoSections();
$toDoManager->printToDoTable();

echo '<br />';
echo '<input type="button" onclick="save(this)" value="Speichern" />';
echo '<br />';
echo 'Antwort vom Server: <span id="httpResponse"></span>';

?>


</body>
</html>