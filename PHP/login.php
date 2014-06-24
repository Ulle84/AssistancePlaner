<!DOCTYPE html>
<meta charset="utf-8">
<head>
    <title>Assistenz Planer - Login</title>
    <link rel="stylesheet" type="text/css" href="../CSS/login.css" media="all"/>
    <script language="JavaScript" src="../JavaScript/login.js"></script>
</head>
<body>
<?php include('navigation.php'); ?>

<?php
$redirect = 'overview.php';
if (isset($_GET['redirect'])) {
    $redirect = $_GET['redirect'] . '.php';
}
echo '<div id="redirect" class="hidden">' . $redirect . '</div>';
?>

<div class="center">
    <table>
        <tr>
            <td>Klient:</td>
            <td>

                <?php
                echo '<input type="text" name="client" id="client"';
                if (isset($_GET['client'])) {
                    echo ' value = "' . $_GET['client'] . '"';
                }
                echo '/>';
                ?>

            </td>
        </tr>
        <tr>
            <td>Assistent:</td>
            <td>
                <?php
                echo '<input type="text" name="assistant" id="assistant"';
                if (isset($_GET['assistant'])) {
                    echo ' value = "' . $_GET['assistant'] . '"';
                }
                echo '/>';
                ?>
        </tr>
        <tr>
            <td>Passwort:</td>
            <td><input type="password" name="passwort" id="password" onkeydown="if (event.keyCode == 13) login()"/></td>
        </tr>
        <tr>
            <td></td>
            <td><input type="button" value="Anmelden" onclick = "login()" /></td>
        </tr>
    </table>
    <br/>
    <span id="httpResponse"></span>
</div>
</body>
</html>