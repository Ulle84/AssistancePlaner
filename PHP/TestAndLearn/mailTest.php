<?php

require '../ExternalResources/PHPMailer/PHPMailerAutoload.php';

$mail = new PHPMailer;

$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'smtp.gmail.com';  // Specify main and backup server
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = 'AssistenzPlaner';                            // SMTP username
$mail->Password = 'l<\O.Oix0cApV<5!j^*VupN4N';                           // SMTP password
$mail->SMTPSecure = 'tls';                            // Enable encryption, 'ssl' also accepted

$mail->From = 'AssistenzPlaner@gmail.com';
$mail->FromName = 'Assistenz Planer';
$mail->addAddress('u.belitz@gmx.de', 'Ulrich Belitz');  // Add a recipient
//$mail->addAddress('ellen@example.com');               // Name is optional
//$mail->addReplyTo('info@example.com', 'Information');
//$mail->addCC('cc@example.com');
//$mail->addBCC('bcc@example.com');

$mail->WordWrap = 80;                                 // Set word wrap to 50 characters
//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = 'Bitte mögliche Termine eintragen.';
$mail->Body    = 'Liebes Team\n\nBitte tragt bis <b>22.05.2014</b> Eure möglichen Termien ein\n\nVielen Dank!';
$mail->AltBody = 'Liebes Team\n\nBitte tragt bis 22.05.2014 Eure möglichen Termien ein\n\nVielen Dank!';

if(!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
    exit;
}

echo 'Message has been sent';





/* not working!!!

// the message
$msg = "First line of text\nSecond line of text";

// use wordwrap() if lines are longer than 70 characters
$msg = wordwrap($msg, 70);

$headers = "From: u.belitz@iCloud.com";

// send email
$returnValue = mail("u.belitz@gmx.de", "My subject", $msg, $headers);

echo $returnValue;

*/

?>