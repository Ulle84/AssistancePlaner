<?php session_start(); ?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="utf-8"/>
    <title>Assistenzplaner</title>
    <link rel="stylesheet" type="text/css" href="../CSS/global.css" media="all" />
    <link rel="stylesheet" type="text/css" href="../CSS/tooltip.css" media="all" />
</head>
<body>
<?php include('navigation.php'); ?>

<div id="main" class="wrapLongText">
    <h1>Willkommen beim Assistenzplaner</h1>
    Der Assistenzplaner ist ein Hilfsmittel für Menschen, die auf Assistenz angewiesen sind. <br/>

    Die Planung umfasst die Erstellung von Dienstplänen und die Verwaltung von Aufgaben.<br />
<!-- Texte für Tooltip siehe Thesis S. 20 -->
    Mit dem Assistenzplaner hat der <span class="tooltip" data-tooltip="Körperlich behinderter Mensch, der im alltäglichen Leben permanent auf Hilfe angewiesen ist">Klient</span> die Möglichkeit, sein <span class="tooltip" data-tooltip="Helfer, der den Klienten pflegt und im Alltag unterstützt">Assistenz</span>-Team zu verwalten.
    Dabei kann der Klient neben den Kontaktdaten auch Priorisierungswerte und Stundenkontingent der Assistenten definieren.<br />

    Der Klient kann mit dem Monatsplan die Dienstzeiten für einen Monat festlegen.
    Seine Assistenten werden per Knopfdruck benachrichtigt und gebeten ihre Termine in einen Kalender einzutragen.
    Ein Algorithmus erstellt automatisch einen Dienstplanvorschlag, der alle Vorlieben des Klienten und die möglichst gleichmäßige Ausschöpfung der Stundenkontingente berücksichtigt.
    Der Klient kann sich mehrere Dienstplanvorschläge erstellen lassen.
    Weiterhin ist es möglich, den Dienstplan manuell anzupassen.<br />

    Eine Aufgaben-Verwaltung rundet den Assistenzplaner ab.<br />


    Bitte <a href="login.php">melden Sie sich an</a> oder <a href="createNewAccount.php">legen Sie einen neuen Klienten-Zugang
        an</a>.


    <!--


    Mit Hilfe des Assistenzplaners ist es unter anderem möglich:
    <ul>
        <li>Assistenz-Teams zu verwalten</li>
        <li>Dienstpläne zu erstellen</li>
        <li>Aufgaben der Assistenten zu verwalten</li>
    </ul>
    -->
</div>
</body>
</html>
