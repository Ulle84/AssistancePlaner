<?php
$client = "";
if (isset($_SESSION['client'])) {
    $client = $_SESSION['client'];
}

$username = "";
if (isset($_SESSION['userName'])) {
    $username = $_SESSION['userName'];
}

echo '<div id="userInformation">';
echo 'Klient: ' . $client . '&nbsp;&nbsp;&nbsp;&nbsp;';
echo 'Angemeldeter Benutzer: <span id="username">' . $username . '</span>&nbsp;&nbsp;&nbsp;&nbsp;';
echo '&nbsp;&nbsp;&nbsp;<a href="index.php">Übersicht</a>';
echo '&nbsp;&nbsp;&nbsp;<a href="changePassword.php">Passwort ändern</a>';
echo '&nbsp;&nbsp;&nbsp;<a href="documentation.php">Dokumentation</a>';
echo '&nbsp;&nbsp;&nbsp;<a href="logout.php">Abmelden</a></div>';
?>