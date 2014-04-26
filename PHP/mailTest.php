<?php
/**
 * Created by PhpStorm.
 * User: Ulle
 * Date: 25.04.14
 * Time: 20:48
 */

// the message
$msg = "First line of text\nSecond line of text";

// use wordwrap() if lines are longer than 70 characters
$msg = wordwrap($msg, 70);

$headers = "From: u.belitz@iCloud.com";

// send email
$returnValue = mail("u.belitz@gmx.de", "My subject", $msg, $headers);

echo $returnValue;

?>