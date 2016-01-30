<h2>Dienstplan</h2>
Hier kann man den Dienstplan erstellen und anpassen.
Oben sieht man die letzte Änderung des Dienstplanes. Dieses Datum wird jedes Mal erneuert, wenn man auf den
Button <input type="button" value="Dienstplan speichern"/> klickt. <br />

In nachfolgender Tabelle kann man den Dienstplan ansehen und verändern. Wenn genügend freie Termine der Assistenten
eingetragen wurden, so wird automatisch ein Dienstplanvorschlag erstellt. Dieser kann durch Klick in die Tabellenzellen
angepasst werden. <br />

<?php include("documentationCalendarColor.php") ?>

<h2>Stundenübersicht</h2>
In der Stundenübersicht kann man sehen, welcher Assistent wieviele Stunden (Dienst- und Bereitschaftsstunden addiert) in
diesem Monat arbeiten müsste. Aus den Team-Einstellungen werden die benötigten Stunden angezeigt. Weiterhin wird die Differenz
berechnet und angezeigt.

<h2>Buttons</h2>
Im unteren Bereich des Dienstplans sind einige Buttons.
<ul>
    <li>Mit <input type="button" value="Dienstplan prüfen"/> kann man überprüfen, dass an jedem Tag genau ein Assitent
    Dienst und genau ein Assitent Bereitschaft hat.</li>
    <li>Mit <input type="button" value="Dienstplan speichern"/> kann man den Dienstplan abspeichern.</li>
    <li>Mit <input type="button" value="Dienstplan löschen"/> kann man den Dienstplan löschen.</li>
    <li>Mit <input type="button" value="Dienstplan als PDF anzeigen"/> kann man sich den Dienstplan als PDF anzeigen
    lassen um ihn zu speichern oder zu drucken.</li>
</ul>

