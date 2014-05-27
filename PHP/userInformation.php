<?php
$username = "";
if (isset($_SESSION['userName'])) {
    $username = $_SESSION['userName'];
}

echo '<div id="userInformation">Eingeloggter Benutzer: <span id="username">' . $username . '</span>&nbsp;&nbsp;&nbsp;&nbsp;';
echo '&nbsp;&nbsp;&nbsp;<a href="index.php">Home</a>';
echo '&nbsp;&nbsp;&nbsp;<a href="changePassword.php">Passwort ändern</a>';
echo '&nbsp;&nbsp;&nbsp;<a href="documentation.php">Dokumentation</a>';
echo '&nbsp;&nbsp;&nbsp;<a href="logout.php">Logout</a></div>';
?>