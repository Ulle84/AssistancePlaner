<h1>Kalender</h1>
Mit Hilfe des <a href="calendarView.php">Kalenders</a> können die Assistenten ihre Termine eintragen.

<img id="imgCalendar" title="Kalender" class="highResolution" src="../Images/Screenshots/CalendarImplementationWithTimes.png" />

<h2>Mögliche Termine eintragen</h2>
Im <a href="calendarView.php">Kalender</a> können die Assistenten für einen Monat ihre Termine eintragen. <br/>
Im Kalender sind die Dienstzeiten eingetragen, so wie sie vom Klienten hingterlegt wurde. Steht hier z. B. 13:00 - 08:00,
dann geht der Dienst von 13:00 Uhr des Tages bis um 08:00 Uhr des Folgetages.

<?php include("documentationCalendarColor.php") ?>

Durch einen Klick auf ein Datum kann der Zustand geändert werden. Standardmäßig ist ein Termin rot (kein Dienst möglich)
markiert. Durch einen Klick wird der Termin grün (Dienst möglich) markiert. Durch einen zweiten Klick wird das Datum als
gelb (Dienst bedingt möglich) markiert. Durch einen dritten Klick wird das Datum wieder als grün (Dienst möglich)
markiert. <br />

Mit dem Button <input type="button" value="Alle Daten markieren"/> kann man auf einen Rutsch alle Daten des Monats grün
(Dienst möglich) markieren.

<h2>Nachricht an Klienten</h2>
Hier kann eine Nachricht an den Klienten eingeben werden, die bei der Dienstplanerstellung angezeigt werden.

<h2>Speichern</h2>
Wichtig ist am Ende die Eingaben zu speichern. Dafür gibt es unten den Button <input type="button" value="Speichern"/>.
Bei "Antwort vom Server" sollte nach kurzer Zeit die Meldung "Eingabe wurde gespeichert" zu lesen sein. Zu einem späteren
Zeitpunkt kann man auf die Seite zurückgehen und die Eingaben verändern.