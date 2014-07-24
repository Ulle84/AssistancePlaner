<?php
$client = "";
if (isset($_SESSION['client'])) {
    $client = $_SESSION['client'];
}

echo '<div id="navigation">';
if (isset($_SESSION['userName'])) {
    echo 'Klient: ' . $client . '&nbsp;&nbsp;&nbsp;&nbsp;';
    if ($_SESSION['userName'] != "") {
        echo 'Assistent: <span id="username">' . $_SESSION['userName'] . '</span>&nbsp;&nbsp;&nbsp;&nbsp;';
    }
    else {
        echo '<span class="hidden" id="username">' . $_SESSION['client'] . '</span>';
    }
    echo '&nbsp;&nbsp;&nbsp;<a href="overview.php">Übersicht</a>';
    echo '&nbsp;&nbsp;&nbsp;<a href="changePassword.php">Passwort ändern</a>';
    echo '&nbsp;&nbsp;&nbsp;<a href="documentation.php">Dokumentation</a>';
    echo '&nbsp;&nbsp;&nbsp;<a href="feedbackView.php">Feedback</a>';
    echo '&nbsp;&nbsp;&nbsp;<a href="impressum.php">Impressum</a>';
    echo '&nbsp;&nbsp;&nbsp;<a href="logout.php">Abmelden</a>';
}
else
{
    echo '<a href="index.php">Willkommen</a>';
    echo '&nbsp;&nbsp;&nbsp;<a href="createNewAccount.php">Neuen Klienten einrichten</a>';
    echo '&nbsp;&nbsp;&nbsp;<a href="impressum.php">Impressum</a>';
    echo '&nbsp;&nbsp;&nbsp;<a href="login.php">Anmelden</a>';
}
echo '</div>';
?>