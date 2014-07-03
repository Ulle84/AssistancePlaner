<?php
session_start();

require_once '../ExternalResources/PHPMailer/PHPMailerAutoload.php';
require_once 'Team.php';
require_once 'Settings.php';
require_once 'functions.php';

$content = $_POST['content'];
$year = $_POST['year'];
$month = $_POST['month'];

$monthReminder = $month - 1;
$yearReminder = $year;
if ($monthReminder == 0) {
    $monthReminder = 12;
    $yearReminder--;
}

$hostname = $_SERVER['HTTP_HOST'];
$path = dirname($_SERVER['PHP_SELF']);

$team = new Team();
$mailAddresses = $team->getMailAddresses();

$settings = new Settings();

$message = 'Liebes Team,<br /><br />';
$message .= 'bitte tragt bis <b>15. ' . get_month_description($monthReminder) . ' ' . $yearReminder . '</b> Eure möglichen Termine für den ';
$message .= get_month_description($month) . ' ' . $year . ' im ';
$message .= '<a href="http://' . $hostname . ($path == '/' ? '' : $path) . '/calendarView.php?year=' . $year . '&month=' . $month . '">';
$message .= 'Assistenzplaner</a> ein.<br /><br />Vielen Dank!';

if ($content != "") {
    $message .= '<br /><br />Hier noch eine Nachricht von ' . $settings->adminName . ': <br /><hr />';
    $message .= $content;
}

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

foreach ($mailAddresses as $mailAddress => $name) {
    $mail->addAddress($mailAddress, $name);
}

// also notify developer
$mail->addBCC('u.belitz@gmx.de', 'Ulrich Belitz');

//$mail->addAddress('ellen@example.com');               // Name is optional
//$mail->addReplyTo('info@example.com', 'Information');
//$mail->addCC('cc@example.com');
//$mail->addBCC('bcc@example.com');

$mail->WordWrap = 50; // Set word wrap to 50 characters
//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
$mail->isHTML(true); // Set email format to HTML

$mail->Subject = 'Assistenzplaner - Bitte mögliche Termine eintragen';
$mail->Body = $message;


//$mail->SMTPDebug = 1;

if (!$mail->send()) {
    echo 'Nachricht konnte nicht gesendet werden. ';
    echo 'Fehlermeldung: ' . $mail->ErrorInfo;
    exit;
}

echo 'Nachricht wurde gesendert.';

?>