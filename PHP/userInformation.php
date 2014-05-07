<?php
    $username = "";
    if (isset($_SESSION['userName']))
    {
        $username = $_SESSION['userName'];
    }

    echo '<div id="userInformation">Eingeloggter User: ' . $username . ' <a href="changePassword.php">Passwort ändern</a> <a href="logout.php">Logout</a></div>';
?>