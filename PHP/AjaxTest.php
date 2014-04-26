<?php

$content = $_POST['content'];

echo $content;

// save to file
$fileName = "../UserInput/AjaxTest.txt";
$fh = fopen($fileName, "a");
fwrite($fh, ($content . "\r\n")); // add newline for next time
fclose($fh);

?>

