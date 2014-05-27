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
<?php include('userInformation.php'); ?>

<div id="documentation">
    <h1>Kalender</h1>
    Mit Hilfe des <a href="calendarView.php">Kalenders</a> können die Assistenten ihre Termine eintragen.

    <h2>Mögliche Termine eintragen</h2>
    Im Kalender können die freien Termine eingetragen werden.
    Durch Klick auf ein Datum wird der Termin ...

    Farblich codiert ist folgendes:
    <table>
        <tr>
            <th>Farbe</th>
            <th>Bedeutung</th>
        </tr>
        <tr>
            <td class="bad">Rot</td>
            <td class="left">An diesem Termin ist kein Dienst möglich.</td>
        </tr>
        <tr>
            <td class="okay">Gelb</td>
            <td class="left">An diesem Termin ist ein Dienst zur Not möglich.</td>
        </tr>
        <tr>
            <td class="good">Grün</td>
            <td class="left">An diesem Termin ist ein Dienst auf jeden Fall möglich.</td>
        </tr>
    </table>

    Mit dem Button "Alle Daten markieren" kann man auf einen Rutsch alle Daten als mögliche Daten markieren.

    <h2>Nachricht an Klienten</h2>
    Hier kann eine Nachricht an den Klienten eingeben werden, die bei der Dienstplanerstellung angezeigt werden.

    <h2>Speichern</h2>
    Wichtig ist am Ende die Eingaben zu speichern. Dafür gibt es unten einen Button.
    Bei "Antwort vom Server" sollte die Meldung "Eingabe wurde gespeichert" zu lesen sein. Zu einem späteren Zeitpunkt
    kann man auf die Seite zurückgehen und die Eingaben verändern.
</div>

</body>
</html>