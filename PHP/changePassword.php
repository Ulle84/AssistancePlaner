<?php session_start(); ?>
<!DOCTYPE html>
<meta charset="utf-8">
<head>
    <title>Assistenzplaner - Passwort ändern</title>
    <link rel="stylesheet" type="text/css" href="../CSS/login.css" media="all"/>
    <script language="JavaScript" src="../JavaScript/changePassword.js"></script>
</head>
<body>
<?php include('navigation.php'); ?>
<div class="hidden" id="userName"><?php echo $_SESSION['userName']; ?></div>

<div class="center">
    <table>
        <tr>
            <td>Aktuelles Passwort:</td>
            <td><input type="password" name="oldPassword" id="oldPassword"/></td>
        </tr>
        <tr>
            <td>Neues Passwort:</td>
            <td><input onchange="validateString(this)" onblur="validateString(this)" type="password" name="newPassword" id="newPassword"/></td>
        </tr>
        <tr>
            <td>Wiederholung neues Passwort:</td>
            <td><input onchange="validateString(this)" onblur="validateString(this)" onkeydown="if (event.keyCode == 13) changePassword()" type="password" name="newPasswordRepetition" id="newPasswordRepetition"/></td>
        </tr>
        <tr>
            <td></td>
            <td><input onclick="changePassword(this)" type="submit" value="Passwort ändern"/></td>
        </tr>
    </table>
    <span id="httpResponse"></span>
</div>
</body>
</html>