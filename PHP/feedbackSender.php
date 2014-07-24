<?php
session_start();

require_once '../ExternalResources/PHPMailer/PHPMailerAutoload.php';

$feedback = $_POST['feedback'];

if ($feedback == "") {
    echo 'Es wurde kein Feedback eingegeben!';
    exit;
}

$mailContent = "Klient: " . $_SESSION['client'] . "<br /> Assistent: " . $_SESSION['userName'] . "<br /><br />" . str_replace("\n", "<br />", $feedback);


$mail = new PHPMailer;

$mail->CharSet = "UTF - 8";
$mail->isSMTP(); // Set mailer to use SMTP
$mail->Host = 'smtp.strato.de'; // Specify main and backup server
$mail->SMTPAuth = true; // Enable SMTP authentication
$mail->Username = 'info@assistenzplaner.de'; // SMTP username
$mail->Password = '8XELhtUfgwFc'; // SMTP password
$mail->SMTPSecure = 'ssl'; // Enable encryption, 'ssl' also accepted
$mail->Port = "465";

$mail->From = 'info@assistenzplaner.de';
$mail->FromName = 'Assistenz Planer';

$mail->addAddress('u.belitz@gmx.de', 'Ulrich Belitz');

//$mail->addAddress('ellen@example.com');               // Name is optional
//$mail->addReplyTo('info@example.com', 'Information');
//$mail->addCC('cc@example.com');
//$mail->addBCC('bcc@example.com');

$mail->WordWrap = 50; // Set word wrap to 50 characters
//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
$mail->isHTML(true); // Set email format to HTML

$mail->Subject = 'Assistenzplaner - Feedback';
$mail->Body = $mailContent;


//$mail->SMTPDebug = 1;

if (!$mail->send()) {
    echo 'Nachricht konnte nicht gesendet werden. ';
    echo 'Fehlermeldung: ' . $mail->ErrorInfo;
    exit;
}

echo 'Nachricht wurde gesendert.';

?>