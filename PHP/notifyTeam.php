<?php

require_once '../ExternalResources/PHPMailer/PHPMailerAutoload.php';
require_once 'Team.php';
require_once 'Settings.php';
require_once 'functions.php';

$content = $_POST['content'];
$year = $_POST['year'];
$month = $_POST['month'];

$hostname = $_SERVER['HTTP_HOST'];
$path = dirname($_SERVER['PHP_SELF']);

$team = new Team();
$mailAddresses = $team->getMailAddresses();

$settings = new Settings();

$message = 'Liebes Team,<br /><br />';
$message .= 'bitte tragt bis <b>22.05.2014</b> Eure möglichen Termine für den '; //TODO Date Monatsende - 10 Tage?
$message .= get_month_description($month) . ' ' . $year . ' im ';
$message .= '<a href="http://' . $hostname . ($path == '/' ? '' : $path) . '/calendarView.php?year=' . $year . '&month=' . $month . '">' ;
$message .= 'Assistenzplaner</a> ein<br /><br />Vielen Dank!';

if ($content != "") {
    $message .= '<br /><br />Hier noch eine Nachricht von ' . $settings->adminName . ': <br /><hr />';
    $message .= $content;
}

//echo $message;
//exit;

$mail = new PHPMailer;

$mail->CharSet = "UTF - 8";
$mail->isSMTP(); // Set mailer to use SMTP
$mail->Host = 'smtp.gmail.com'; // Specify main and backup server
$mail->SMTPAuth = true; // Enable SMTP authentication
$mail->Username = 'AssistenzPlaner'; // SMTP username
$mail->Password = 'l<\O.Oix0cApV<5!j^*VupN4N'; // SMTP password
$mail->SMTPSecure = 'tls'; // Enable encryption, 'ssl' also accepted

/*
 * $mail->IsSMTP();
$mail->Host = "smtp . gmail . com";
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
$mail->Body = $message;
//$mail->AltBody = 'Liebes Team\n\nBitte tragt bis 22.05.2014 Eure möglichen Termien ein\n\nVielen Dank!';

//TODO $mail->AltBody

//$mail->SMTPDebug = 1;

if (!$mail->send()) {
    echo 'Nachricht konnte nicht geschickt werden. ';
    echo 'Fehlermeldung: ' . $mail->ErrorInfo;
    exit;
}

echo 'Nachricht wurde geschickt.';



?>