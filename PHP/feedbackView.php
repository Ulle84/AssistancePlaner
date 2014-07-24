<?php include('authentication.php'); ?>
<!DOCTYPE html>
<meta charset="utf-8">
<html>
<head>
    <title>Assistenz Planer - Feedback</title>

    <link rel="stylesheet" type="text/css" href="../CSS/global.css" media="all"/>
    <script language="JavaScript" src="../JavaScript/feedback.js"></script>
</head>
<body>
<?php include('navigation.php'); ?>

<h1>Feedback</h1>

<textarea onchange="validateString(this)" onblur="validateString(this)" id="feedback" cols="100" rows="10"></textarea> <br/>
<input type="button" value="Feedback abschicken" onclick="sendFeedback(this)"/>

<br/>

Antwort vom Server: <span id="httpResponse"></span>


</body>
</html>