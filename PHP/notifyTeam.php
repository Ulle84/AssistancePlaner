<?php

require_once '../ExternalResources/PHPMailer/PHPMailerAutoload.php';
require_once 'Team.php';

$content = $_POST['content'];
$year = $_POST['year'];
$month = $_POST['month'];

$team = new Team();
$mailAddresses = $team->getMailAddresses();

$mail = new PHPMailer;

$mail->CharSet = "UTF-8";
$mail->isSMTP(); // Set mailer to use SMTP
$mail->Host = 'smtp.gmail.com'; // Specify main and backup server
$mail->SMTPAuth = true; // Enable SMTP authentication
$mail->Username = 'AssistenzPlaner'; // SMTP username
$mail->Password = 'l<\O.Oix0cApV<5!j^*VupN4N'; // SMTP password
$mail->SMTPSecure = 'tls'; // Enable encryption, 'ssl' also accepted

/*
 * $mail->IsSMTP();
$mail->Host = "smtp.gmail.com";
$mail->SMTPAuth = true;
$mail->SMTPSecure = "ssl";
$mail->Username = "myEmail";
$mail->Password = "myPassword";
$mail->Port = "465";
 */

//TODO username and reply to of administrator-settings?

$mail->From = 'AssistenzPlaner@gmail.com';
$mail->FromName = 'Assistenz Planer';

foreach ($mailAddresses as $mailAddress => $name) {
    $mail->addAddress($mailAddress, $name);
}

//$mail->addAddress('u.belitz@gmx.de', 'Ulrich Belitz');  // Add a recipient
//$mail->addAddress('ellen@example.com');               // Name is optional
//$mail->addReplyTo('info@example.com', 'Information');
//$mail->addCC('cc@example.com');
//$mail->addBCC('bcc@example.com');

$mail->WordWrap = 50; // Set word wrap to 50 characters
//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
$mail->isHTML(true); // Set email format to HTML

$mail->Subject = 'Bitte mögliche Termine eintragen.';
$mail->Body = 'Liebes Team<br /><br />Bitte tragt bis <b>22.05.2014</b> Eure möglichen Termine im <a href="ToDo">Assistenzplaner</a> ein<br /><br />Vielen Dank!';
$mail->AltBody = 'Liebes Team\n\nBitte tragt bis 22.05.2014 Eure möglichen Termien ein\n\nVielen Dank!';
//$mail->SMTPDebug = 1;

if (!$mail->send()) {
    echo 'Nachricht konnte nicht geschickt werden. ';
    echo 'Fehlermeldung: ' . $mail->ErrorInfo;
    exit;
}

echo 'Nachricht wurde geschickt.';



?>