<?php

include_once 'Settings.php';

$settings = new Settings($_SESSION['clientName']);

if ($settings->showToDoManager) {
    echo '<h1>Aufgaben-Verwaltung</h1>';

    if ($_SESSION['isClient']) {
        include('documentationToDoManagerClient.php');
    } else {
        include('documentationToDoManagerAssistant.php');
    }
}
?>