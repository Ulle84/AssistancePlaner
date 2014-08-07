<?php
if (!$_SESSION['isClient']) {
    exit;
}
?>

<h1>Team</h1>
In der Team-Übersicht werden die Assistenten verwaltet. <br />

<h2>Buttons</h2>
Im unteren Bereich des Dienstplans sind einige Buttons.
<ul>
    <li>Mit <input type="button" value="Neues Mitglied"/> kann man dem Team ein neues Mitglied hinzufügen.</li>
    <li>Mit <input type="button" value="Team speichern"/> kann man das Team abspeichern. Dies ist nach jeder Änderung
    notwendig!</li>
    <li>Mit <input type="button" value="löschen"/> (in der Spalte Aktionen) kann man ein Team-Mitglied aus dem Team entfernen
        und löschen.</li>
    <li>Mit <input type="button" value="Passwort zurücksetzen"/> (in der Spalte Aktionen) kann man das Passwort eines
    Team-Mitgliedes zurücksetzen. Es wird das Standard-Passwort aus den Einstellungen als neues Passwort festgelegt.</li>
</ul>

<h2>Mitglieder</h2>
Jedes Team-Mitglied hat unterschiedliche Eigenschaften

<h3>Kennung</h3>
Jedes Team-Mitglied braucht eine eindeutige Kennung, um sich beim Assistenzplaner anmelden zu können. Diese Kennung
muss eindeutig sein, sprich sie darf nicht zweimal vorkommen. Weiterhin darf sie nicht leer sein.

<h3>Vorname</h3>
Hier wird der Vorname des Assistenten eingetragen.

<h3>Nachname</h3>
Hier wird der Nachname des Assistenten eingetragen.

<h3>E-Mail Adresse</h3>
Hier wird die E-Mail Adresse des Assistenten eingetragen. Alle Mails vom Assistenzplaner werden an diese E-Mail Adresse
geschickt.

<h3>Telefonnummer</h3>
Hier wird die Telefonnummer des Assistenten eingetragen. Der Assistenzplaner benötigt diese Angabe nicht, jedoch kann
es für den Klienten von Vorteil sein alle personenbezogenen Informationen an einer Stelle zu verwalten.

<h3>Stichwörter</h3>
Hier kann der Klient für einen Assistenten Stichwörter vergeben. Diese Stichwörter werden bei der Dienstplanerstellung
berücksichtigt. Schaut der Klient zum Beispiel gerne mit einem bestimmten Assistenten Fußball, so kann Fußball als Stichwort
eingetragen werden. Ist im Monatsplan für einen bestimmten Tag in den privaten Notizen auch das Stichwort Fußball enthalten
so steigt die Wahrscheinlichkeit, dass der Assistent mit dem Stichwort Fußball auch den Dienst bekommt.

<h3>Stundenkontigent</h3>
Hier wird das Stundenkontingent des Assistenten eingetragen. Das Stundenkontigent spielt bei der Dienstplanerstellung eine
große Rolle.

<h3>Priorisierung</h3>
Hier kann der Klient für die Assistenten einen Priroriserungswert eintragen. Hat z. B. Assistent A den Priorisierungswert 1
und Assistent B den Priorisierungswert 2, so ist (an einem Tag, an dem beide Assistenten verfügbar sind) die Wahrscheinlichkeit
doppelt so hoch, dass Assistent B den Dienst bekommt (natürlich nur, wenn sein Stundenkontigent noch nicht ausgeschöpft ist).

<h3>Bevorzugte Wochentage</h3>
Der Klient kann festlegen, ob er einen Assistenten lieber an einem bestimmten Wochentag haben möchte.
<!-- ToDo: weiter beschreiben -->