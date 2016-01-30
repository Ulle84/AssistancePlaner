<?php include('authentication.php'); ?>
<!DOCTYPE html>
<meta charset="utf-8">
<html>
<head>
    <title>Assistenzplaner - Einstellungen</title>
    <link rel="stylesheet" type="text/css" href="../CSS/global.css" media="all"/>
    <script language="JavaScript" src="../JavaScript/settings.js"></script>
</head>
<body>
<?php include('navigation.php'); ?>

<?php

require_once 'Settings.php';
require_once 'WorkingTimes.php';

echo '<div id="main">';
if ($_SESSION['isClient']) {
    $settings = new Settings($_SESSION['clientName']);

    echo '<h1>Klient</h1>';

    echo '<table>';

    echo '<tr><th>Beschreibung</th><th>Wert</th></tr>';
    echo '<tr><td class="left">Vorname</td><td><input onchange="saveSettings()" id="firstName" type="text" size="30" maxlength="50" value="' . $settings->adminFirstName . '"/></td></tr>';
    echo '<tr><td class="left">Nachname</td><td><input onchange="saveSettings()" id="lastName"  type="text" size="30" maxlength="50" value="' . $settings->adminLastName . '"/></td></tr>';
    echo '<tr><td class="left">E-Mail-Adresse</td><td><input onchange="saveSettings()" id="mailAddress"  type="text" size="30" maxlength="50" value="' . $settings->mailAddress . '"/></td></tr>';
    echo '<tr><td class="left">Standard-Passwort für Assistenten</td><td><input onchange="saveSettings()" id="standardPassword"  type="text" size="30" maxlength="50" value="' . $settings->standardPassword . '"/></td></tr>';

    echo '<tr class="hidden"><td class="left">Aufgaben-Verwaltung nutzen</td><td class="left"><input id="showToDoManager" type="checkbox" value=""';
    if ($settings->showToDoManager == 1) {
        echo ' checked="true"';
    }
    echo '"/></td></tr>';

    echo '</table><br /><br />';

    echo '<h1>Ansprechpartner Leistungserbringer</h1>';

    echo '<table>';

    echo '<tr><th>Beschreibung</th><th>Wert</th></tr>';
    echo '<tr><td class="left">Vorname</td><td><input onchange="saveSettings()" id="firstNameProvider" type="text" size="30" maxlength="50" value="' . $settings->firstNameProvider . '"/></td></tr>';
    echo '<tr><td class="left">Nachname</td><td><input onchange="saveSettings()" id="lastNameProvider"  type="text" size="30" maxlength="50" value="' . $settings->lastNameProvider . '"/></td></tr>';
    echo '<tr><td class="left">E-Mail-Adresse</td><td><input onchange="saveSettings()" id="mailAddressProvider"  type="text" size="30" maxlength="50" value="' . $settings->mailAddressProvider . '"/></td></tr>';

    echo '</table>';

    echo '<br /><br />';

    $workingTimes = new WorkingTimes();
    $workingTimes->printTable();
} else {
    echo 'Zugang nicht erlaubt!';
}
echo '</div>';
?>
</body>
</html>

