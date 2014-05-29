<?php include('authentication.php'); ?>
<!DOCTYPE html>
<meta charset="utf-8">
<html>
<head>
    <title>Assistenz Planer - Dokumentation</title>
    <link rel="stylesheet" type="text/css" href="../CSS/global.css" media="all"/>
    <link rel="stylesheet" type="text/css" href="../CSS/documentation.css" media="all"/>
    <script language="JavaScript" src="../JavaScript/documentation.js"></script>
</head>
<body onload="init()">
<?php include('navigation.php'); ?>

<div id="documentation">
    <?php
    include('documentationNavigation.php');
    include('documentationMonthNavigation.php');
    if (!$_SESSION['admin']) {
        include('documentationCalendar.php');
    }
    include('documentationRoster.php');
    ?>
</div>

</body>
</html>