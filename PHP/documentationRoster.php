<h1>Dienstplan</h1>
<?php
if (!$_SESSION['isAdmin']) {
    echo 'Hier kann man den Dienstplan einsehen. <br />';
    echo 'Mit Hilfe der Monats-Navigation kann man sich durch die Monate navigieren.';
}
else {
    //TODO Docuementation for Client/Admin
}
?>