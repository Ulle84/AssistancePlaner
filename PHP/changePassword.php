<?php session_start(); ?>
<!DOCTYPE html>
<meta charset="utf-8">
<head>
    <title>Assistenz Planer - Passwort ändern</title>
    <link rel="stylesheet" type="text/css" href="../CSS/login.css" media="all"/>
    <script language="JavaScript" src="../JavaScript/changePassword.js"></script>
</head>
<body>
<div class="center">
    <table>
        <tr>
            <td>Username:</td>
            <td id="userName"><?php echo $_SESSION['userName']; ?></td>
        </tr>
        <tr>
            <td>Aktuelles Passwort:</td>
            <td><input type="password" name="oldPassword" id="oldPassword"/></td>
        </tr>
        <tr>
            <td>Neues Passwort:</td>
            <td><input type="password" name="newPassword" id="newPassword"/></td>
        </tr>
        <tr>
            <td>Wiederholung neues Passwort:</td>
            <td><input type="password" name="newPasswordRepetition" id="newPasswordRepetition"/></td>
        </tr>
        <tr>
            <td></td>
            <td><input onclick="changePassword()" type="submit" value="Passwort ändern"/></td>
        </tr>
    </table>
    Antwort vom Server: <span id="httpResponse"></span>

</div>
</body>
</html>