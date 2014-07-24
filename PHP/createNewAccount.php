<?php session_start(); ?>
<!DOCTYPE html>
<meta charset="utf-8">
<head>
    <title>Assistenz Planer - Neuen Klienten anlegen</title>
    <link rel="stylesheet" type="text/css" href="../CSS/login.css" media="all"/>
    <script language="JavaScript" src="../JavaScript/createNewAccount.js"></script>
</head>
<body>
<?php include('navigation.php'); ?>

<div class="center">
    <table>
        <tr>
            <td>Name des Klienten:</td>
            <td><input onchange="validateClientName(this)" onblur="validateClientName(this)" type="text" name="clientName" id="clientName"/></td>
        </tr>
        <tr>
            <td>Passwort:</td>
            <td><input onchange="validateString(this)" onblur="validateString(this)" type="password" name="password" id="password"/></td>
        </tr>
        <tr>
            <td>Wiederholung Passwort:</td>
            <td><input onchange="validateString(this)" onblur="validateString(this)" onkeydown="if (event.keyCode == 13) createNewAccount()" type="password" name="passwordRepetition" id="passwordRepetition"/></td>
        </tr>
        <tr>
            <td></td>
            <td><input onclick="createNewAccount()" type="submit" value="Klient anlegen"/></td>
        </tr>
    </table>
    <span id="httpResponse"></span>
</div>
</body>
</html>