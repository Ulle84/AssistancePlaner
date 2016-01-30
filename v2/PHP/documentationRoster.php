<h1>Dienstplan</h1>
<?php
if (!$_SESSION['isClient']) {
    echo 'Hier kann man den Dienstplan einsehen. <br />';
    echo 'Mit Hilfe der Monats-Navigation kann man sich durch die Monate navigieren.';
}
else {
    include('documentationRosterClient.php');
}
?>