<?php
$client = "";
if (isset($_SESSION['client'])) {
    $client = $_SESSION['client'];
}

echo '<div id="navigation">';
if (isset($_SESSION['userName'])) {
    echo 'Klient: ' . $client . '&nbsp;&nbsp;&nbsp;&nbsp;';
    echo 'Angemeldeter Benutzer: <span id="username">' . $_SESSION['userName'] . '</span>&nbsp;&nbsp;&nbsp;&nbsp;';
    echo '&nbsp;&nbsp;&nbsp;<a href="overview.php">Übersicht</a>';
    echo '&nbsp;&nbsp;&nbsp;<a href="changePassword.php">Passwort ändern</a>';
    echo '&nbsp;&nbsp;&nbsp;<a href="documentation.php">Dokumentation</a>';
    echo '&nbsp;&nbsp;&nbsp;<a href="logout.php">Abmelden</a>';
}
else
{
    echo '&nbsp;&nbsp;&nbsp;<a href="login.php">Anmelden</a>';
}
echo '</div>';


?>