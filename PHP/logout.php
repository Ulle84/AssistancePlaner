<?php
session_start();
$client = $_SESSION['clientName'];
session_destroy();

$hostname = $_SERVER['HTTP_HOST'];
$path = dirname($_SERVER['PHP_SELF']);

header('Location: http://' . $hostname . ($path == '/' ? '' : $path) . '/login.php?client=' . $client);
?>