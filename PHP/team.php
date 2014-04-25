<!DOCTYPE html>
<meta charset="utf-8">
<html>
<head>
    <title>Assistenz Planer - Team</title>
    <link rel="stylesheet" type="text/css" href="../CSS/global.css" media="all"/>
    <script language="JavaScript" src="../JavaScript/team.js"></script>
</head>
<body>

<table id="team">
    <tr>
        <th>Vorname</th>
        <th>Nachname</th>
        <th>E-Mail-Adresse</th>
        <th>Telefonnummer</th>
        <th>Stundenkontingent</th>
        <th>Priorisierung</th>
        <!-- ToDo ComboBox ?-->
        <th>Bevorzugte Tage</th>
        <th>Löschen</th>
    </tr>

    <?php

    require 'functions.php';

    $fileName = "../Data/Team/team.txt";
    if (file_exists($fileName)) {
        $teamExists = true;
        $file = fopen($fileName, "r");

        $numberOfTeamMembers = (int)rtrim(fgets($file));

        $weekdays = get_weekdays();

        for ($i = 0; $i < $numberOfTeamMembers; $i++) {
            echo '<tr>';
            for ($j = 0; $j < 6; $j++) {
                echo '<td class="left" onclick="edit(this)">' . rtrim(fgets($file)) . '</td>';
            }
            $preferredWeekdays = explode(";", rtrim(fgets($file)));

            echo '<td>';
            for ($j = 0; $j < 7; $j++) {
                echo '<input type="checkbox" value="' . $weekdays[$j] . '"';
                if (in_array($weekdays[$j], $preferredWeekdays)) {
                    echo ' checked="true"';
                }

                echo '/><span>' . $weekdays[$j] . ' </span>';
            }
            echo '</td>';
            echo '<td class="left"><div class="button" onclick="removeMember(this)">Löschen</div></td>';
            echo '</tr>';
        }
    }
    ?>

</table>

<div class="button" onclick="newMember()">Neues Mitglied</div>
<div class="button" onclick="saveTable()">Tabelle speichern</div>

Answer of Server:
<div id="httpResponse"></div>
</body>
</html>

<?php



?>