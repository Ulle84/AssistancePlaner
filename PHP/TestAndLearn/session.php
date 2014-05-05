<!DOCTYPE html>
<meta charset="utf-8">
<?php
session_start();
if(isset($_SESSION['views']))
    $_SESSION['views']=$_SESSION['views']+1;
else
    $_SESSION['views']=1;
?>
<html>
<head>
    <title>Session Test</title>
</head>
<body>

<?php
//retrieve session data
echo "Pageviews=". $_SESSION['views'];

if ($_SESSION['views'] > 2) {
unset($_SESSION['views']);

}

// end session
//session_destroy();

?>



</body>
</html>