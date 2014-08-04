<?php

include_once 'Settings.php';

$settings = new Settings($_SESSION['clientName']);

if ($settings->showToDoManager) {
    echo '<h1>Aufgaben-Verwaltung</h1>';

    if (!$_SESSION['isClient']) {
        echo 'Hier kann man den Dienstplan einsehen. <br />';
        echo 'Mit Hilfe der Monats-Navigation kann man sich durch die Monate navigieren.';
    } else {
        //TODO Docuementation for Client/Admin
    }
}
?>