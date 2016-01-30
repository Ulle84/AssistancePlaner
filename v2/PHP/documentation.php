<?php include('authentication.php'); ?>
<!DOCTYPE html>
<meta charset="utf-8">
<html>
<head>
    <title>Assistenzplaner - Dokumentation</title>
    <link rel="stylesheet" type="text/css" href="../CSS/global.css" media="all"/>
    <link rel="stylesheet" type="text/css" href="../CSS/documentation.css" media="all"/>
    <script language="JavaScript" src="../JavaScript/documentation.js"></script>
</head>
<body onload="init()">
<?php include('navigation.php'); ?>

<div id="main">
    <div id="documentation">
        <?php

        //TODO Reihenfolge definieren


        include('documentationNavigation.php');
        include('documentationMonthNavigation.php');
        if (!$_SESSION['isClient']) {
            include('documentationCalendar.php');
        }
        include('documentationRoster.php');
        include('documentationToDoManager.php');
        include('documentationMonthPlan.php');
        include('documentationTeam.php');
        include('documentationStandardWorkingTimes.php');
        include('documentationSettings.php');

        ?>
    </div>
</div>

</body>
</html>