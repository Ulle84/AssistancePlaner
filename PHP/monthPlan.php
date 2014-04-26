<!DOCTYPE html>
<meta charset="utf-8">
<html>
<head>
    <title>Assistenz Planer - Monatsplan</title>

    <link rel="stylesheet" type="text/css" href="../CSS/global.css" media="all"/>
    <link rel="stylesheet" type="text/css" href="../CSS/calendar.css" media="all"/>
    <script language="JavaScript" src="../JavaScript/monthPlan.js"></script>
</head>
<body>

<table>
    <tr>
        <th>Datum</th>
        <th>Dienstbeginn</th>
        <th>Dienstende</th>
        <th>Bemerkungen (öffentlich)</th>
        <th>Bemerkungen (privat)</th>
    </tr>

    <?php

    require 'functions.php';

    $month = date("n");
    $year = date("Y");

    if (isset($_GET['year'])) {
        $year = $_GET['year'];
    }

    if (isset($_GET['month'])) {
        $month = $_GET['month'];
    }

    $numberOfDays = date("t", mktime(0, 0, 0, $month, 1, $year));

    $defaultBegin = array();
    $defaultEnd = array();

    $defaultBegin[1] = "17:00";
    $defaultEnd[1] = "08:00";
    $defaultBegin[2] = "13:00";
    $defaultEnd[2] = "08:00";
    $defaultBegin[3] = "17:00";
    $defaultEnd[3] = "08:00";
    $defaultBegin[4] = "13:00";
    $defaultEnd[4] = "08:00";
    $defaultBegin[5] = "14:00";
    $defaultEnd[5] = "13:00";
    $defaultBegin[6] = "13:00";
    $defaultEnd[6] = "13:00";
    $defaultBegin[7] = "13:00";
    $defaultEnd[7] = "08:00";

    $monthPlanExists = false;
    $fileName = "../Data/MonthPlan/" . $year . "-" . $month . ".txt";
    $startTimes = array();
    $endTimes = array();
    $notesPublic = array();
    $notesPrivate = array();
    if (file_exists($fileName)) {
        $monthPlanExists = true;

        $file = fopen($fileName, "r");
        $dateSheet = array();

        for ($i = 1; $i <= $numberOfDays; $i++) {
            $times = fgets($file);
            $startTimes[$i] = substr($times, 0, 5);
            $stopTimes[$i] = substr($times, 8, 5);
            $notesPublic[$i] = fgets($file);
            $notesPrivate[$i] = fgets($file);
        }
        fclose($file);
    }


    for ($i = 1; $i <= $numberOfDays; $i++) {
        echo '<tr class="data">';

        echo '<td class="date">';

        echo get_short_date($year, $month, $i) . '</td>';

        // start time
        echo '<td><input value="';
        if ($monthPlanExists) {
            echo $startTimes[$i];
        } else {
            echo $defaultBegin[date("N", mktime(0, 0, 0, $month, $i, $year))];
        }
        echo '" type="text" size="5" maxlength="5" /></td>';

        // end time
        echo '<td><input value="';
        if ($monthPlanExists) {
            echo $stopTimes[$i];
        } else {
            echo $defaultEnd[date("N", mktime(0, 0, 0, $month, $i, $year))];
        }
        echo '" type="text" size="5" maxlength="5" /></td>';

        // private notes
        echo '<td><input value="';
        if ($monthPlanExists) {
            echo $notesPublic[$i];
        }
        echo '" type="text" size="30" maxlength="200" /></td>';

        // public notes
        echo '<td><input value="';
        if ($monthPlanExists) {
            echo $notesPrivate[$i];
        }
        echo '" type="text" size="30" maxlength="200" /></td>';


        echo '</tr>';

    }

    echo '</table>';

    echo '<div id="year" class="hidden">' . $year . '</div>';
    echo '<div id="month" class="hidden">' . $month . '</div>';



    ?>

    <div class="button" onclick="saveMonthPlan()">Speichern</div>

    Answer of Server:
    <div id="myDiv"></div>


</body>
</html>

