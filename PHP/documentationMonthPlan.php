<?php
if (!$_SESSION['isClient']) {
    exit;
}
?>

<h1>Monatsplan</h1>
<h2>Monatstabelle</h2>
In der Monatstabelle können der Dienstzeitbeginn und das Dienstzeitende pro Tag festgelegt werden. Das Dienstzeitende ist immer
am Folgetag. Weiterhin können pro Tag private und öffentliche Bemerkungen festgehalten werden. Die privaten Bemerkungen
sieht nur der Klient selbst. Die öffentlichen Bemerkungen sind auch von den Assistenten einzusehen.

<h2>Nachricht an das Team</h2>
Hier kann eine Nachricht an das Team hinterlegt werden. Alles was sich nicht in den öffentlichen Bemerkungen unterbringen
lässt kann hier eingetragen werden.

<h2>Buttons</h2>
Im unteren Bereich des Dienstplans sind einige Buttons.
<ul>
    <li>Mit <input type="button" value="Speichern"/> kann man den Monatsplan abspeichern.</li>
    <li>Mit <input type="button" value="Team benachrichtigen"/> kann man das Team benachrichtigen. Alle Assistenten, die
        in der Team-Übersicht mit einer E-Mail Adresse hinterlegt sind, werden benachrichtigt und gebeten ihre möglichen
        Termine für den Monat einzutragen.</li>
</ul>