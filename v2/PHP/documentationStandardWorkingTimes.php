<?php
if (!$_SESSION['isClient']) {
    exit;
}
?>

<h1>Standard Arbeitszeiten</h1>
Hier können pro Wochentag die Standard-Arbeitszeiten festgelegt werden. <br />
Die Standard-Arbeitszeiten können für einen bestimmten Tag im Monatsplan überschrieben werden. <br />
Nach einer Änderung der Zeiten muss man einmal auf den Button <input type="button" value="Speichern"/> drücken!
