<?php include('authentication.php'); ?>
<!DOCTYPE html>
<meta charset="utf-8">
<html>
<head>
    <title>Assistenzplaner - Einstellungen</title>
    <link rel="stylesheet" type="text/css" href="../CSS/global.css" media="all"/>
    <script language="JavaScript" src="../JavaScript/settings.js"></script>
    <script language="JavaScript" src="../JavaScript/defaultTimes.js"></script>
</head>
<body>
<?php include('navigation.php'); ?>

<?php

require_once 'Settings.php';
require_once 'WorkingTimes.php';

echo '<div id="main">';
if ($_SESSION['isClient']) {
    $settings = new Settings($_SESSION['clientName']);

    echo '<h1>Allgemein</h1>';

    echo '<table>';

    echo '<tr><th>Beschreibung</th><th>Wert</th></tr>';
    echo '<tr><td class="left">Vorname</td><td><input id="firstName" type="text" size="30" maxlength="50" value="' . $settings->adminFirstName . '"/></td></tr>';
    echo '<tr><td class="left">Nachname</td><td><input id="lastName"  type="text" size="30" maxlength="50" value="' . $settings->adminLastName . '"/></td></tr>';
    echo '<tr><td class="left">E-Mail-  Adresse</td><td><input id="mailAddress"  type="text" size="30" maxlength="50" value="' . $settings->mailAddress . '"/></td></tr>';
    echo '<tr><td class="left">Standard-Passwort für Assistenten</td><td><input id="standardPassword"  type="text" size="30" maxlength="50" value="' . $settings->standardPassword . '"/></td></tr>';

    echo '<tr class="hidden"><td class="left">Aufgaben-Verwaltung nutzen</td><td class="left"><input id="showToDoManager" type="checkbox" value=""';
    if ($settings->showToDoManager == 1) {
        echo ' checked="true"';
    }
    echo '"/></td></tr>';

    echo '</table>';

    echo '<br/>';

    echo '<input type="button" value="Einstellungen speichern" onclick="saveSettings(this)"/>';

    echo '<br/>';
    echo 'Antwort des Servers: <span id="httpResponse"></span>';

    echo '<hr />';

    $workingTimes = new WorkingTimes();
    $workingTimes->printTable();

    echo '<br/>';
    echo '<input type="button" value="Standard-Dienstzeiten Speichern" onclick="saveWorkingTimes(this)"/>';
    echo '<br/>';
    echo 'Antwort des Servers: <span id="httpResponseWorkingTimes"></span>';
} else {
    echo 'Zugang nicht erlaubt!';
}
echo '</div>';
?>
</body>
</html>

