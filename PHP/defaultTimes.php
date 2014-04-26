<!DOCTYPE html>
<meta charset="utf-8">
<html>
<head>
    <title>Assistenz Planer - Arbeitszeiten</title>

    <link rel="stylesheet" type="text/css" href="../CSS/global.css" media="all"/>
    <script language="JavaScript" src="../JavaScript/defaultTimes.js"></script>
</head>
<body>

<table id="defaultTimes">
    <tr>
        <th>Wochentag</th>
        <th>Dienstbeginn</th>
        <th>Dienstende</th>
    </tr>

    <?php

    require 'functions.php';

    $defaultBegin = array();
    $defaultEnd = array();

    $fileName = "../Data/Organization/defaultTimes.txt";
    $defaultTimesExists = false;
    if (file_exists($fileName)) {
        $defaultTimesExists = true;

        $file = fopen($fileName, "r");

        for ($i = 1; $i <= 7; $i++) {
            $times = fgets($file);
            $defaultBegin[$i] = substr($times, 0, 5);
            $defaultEnd[$i] = substr($times, 8, 5);
        }
        fclose($file);
    } else {
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
    }

    $weekdays = get_weekdays();
    for ($i = 1; $i <= 7; $i++) {
        echo '<tr>';
        echo '<td>' . $weekdays[$i - 1] . '</td>';
        echo '<td><input value="' . $defaultBegin[$i] . '" type="text" size="5" maxlength="5" /></td>';
        echo '<td><input value="' . $defaultEnd[$i] . '" type="text" size="5" maxlength="5" /></td>';
        echo '</tr>';

    }

    echo '</table>';



    ?>

    <br/>

    <input type="button" value="Speichern" onclick="save()"/>

    <br/>

    Antwort vom Server: <span id="httpResponse"></span>


</body>
</html>

