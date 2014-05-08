<?php

require_once('Passwords.php');
require_once('Settings.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    session_start();

    $username = $_POST['username'];
    $password = $_POST['passwort'];

    $hostname = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['PHP_SELF']);

    $passwords = new Passwords();
    if ($passwords->checkUser($username, $password)) {
        $_SESSION['loggedIn'] = true;
        $_SESSION['userName'] = $username;
        //TODO master und develop mode

        if ($_SERVER['SERVER_PROTOCOL'] == 'HTTP/1.1') {
            if (php_sapi_name() == 'cgi') {
                header('Status: 303 See Other');
            } else {
                header('HTTP/1.1 303 See Other');
            }
        }

        $settings = new Settings();
        $pageName = "";

        if ($password == $settings->standardPassword)
        {
            $pageName = "changePassword.php";
        }
        else {
            $pageName = "index.php";
        }

        header('Location: http://' . $hostname . ($path == '/' ? '' : $path) . '/' . $pageName);
        exit;

    }
    else {
        echo '<div id="loginResponse">Falsches Passwort eingegeben!</div>';
    }
}
?>
<!DOCTYPE html>
<meta charset="utf-8">
<head>
    <title>Assistenz Planer - Login</title>
    <link rel="stylesheet" type="text/css" href="../CSS/login.css" media="all"/>
</head>
<body>
<div class="center">
    <form action="login.php" method="post">
        <table>
            <!--
            <tr>
                <td colspan="2">Bitte loggen Sie sich ein!</td>
            </tr>
            -->
            <tr>
                <td>Username:</td>
                <td><input type="text" name="username"/></td>
            </tr>
            <tr>
                <td>Passwort:</td>
                <td><input type="password" name="passwort"/></td>
            </tr>
            <tr>
                <td></td>
                <td><input type="submit" value="Anmelden"/></td>
            </tr>
        </table>
    </form>
</div>
</body>
</html>