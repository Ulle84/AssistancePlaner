<?php
session_start();

require_once '../ExternalResources/PHPMailer/PHPMailerAutoload.php';
require_once 'Team.php';
require_once 'Settings.php';
require_once 'functions.php';

$content = $_POST['content'];
$year = $_POST['year'];
$month = $_POST['month'];
$action = $_POST['action'];


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

$settings = new Settings($_SESSION['clientName']);

$sender = $settings->adminName;
if ($settings->adminFirstName != "" && $settings->adminLastName != "") {
    $sender = $settings->adminFirstName . ' ' . $settings->adminLastName;
}

if ($action == "newRoster") {
    $subject = 'Assistenzplaner - Neuer Dienstplan verfügbar';

    $message = 'Liebes Team,<br /><br />';
    $message .= 'der Dienstplan für den Monat <b>' . get_month_description($month) . ' ' . $year . '</b> ist fertiggestellt und kann im ';
    $message .= '<a href="http://' . $hostname . ($path == '/' ? '' : $path) . '/login.php?client=' . $_SESSION['clientName'] . '&redirect=rosterView&year=' . $year . '&month=' . $month .'">';
    $message .= 'Assistenzplaner</a> eingesehen werden.<br /><br />Viele Grüße<br />';
    $message .= $sender;
}

if ($action == "requestDates") {
    $subject = 'Assistenzplaner - Bitte mögliche Termine eintragen';

    $message = 'Liebes Team,<br /><br />';
    $message .= 'bitte tragt bis <b>15. ' . get_month_description($monthReminder) . ' ' . $yearReminder . '</b> Eure möglichen Termine für den ';
    $message .= get_month_description($month) . ' ' . $year . ' im ';
    $message .= '<a href="http://' . $hostname . ($path == '/' ? '' : $path) . '/login.php?client=' . $_SESSION['clientName'] . '&redirect=calendarView">';
    $message .= 'Assistenzplaner</a> ein.<br /><br />Vielen Dank!';
}

if ($action == "notifyProvider") {
    $id = $_POST['uniqueID'];

    $subject = 'Assistenzplaner - Monatsabschluss';

    $message = 'Hallo,<br /><br />';
    $message .= 'der Dienstplan für den Monat <b>' . get_month_description($month) . ' ' . $year . '</b> wurde abgeschlossen und kann im ';
    $message .= '<a href="http://' . $hostname . ($path == '/' ? '' : $path) . '/rosterViewPdf.php?clientName=' . $_SESSION['clientName'] . '&year=' . $year . '&month=' . $month . '&id=' . $id . '">';
    $message .= 'Assistenzplaner</a> eingesehen werden.';
    $message .= '<br /><br />Viele Grüße<br />';
    $message .= $sender;
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

$mail->FromName = $sender . ' via Assistenzplaner';

if ($action == "notifyProvider") {
    $mail->addAddress($settings->mailAddressProvider, $settings->firstNameProvider . ' ' . $settings->lastNameProvider);
}
else {
    foreach ($mailAddresses as $mailAddress => $name) {
        $mail->addAddress($mailAddress, $name);
    }
}

// also notify client
if ($settings->mailAddress != "") {
    $mail->addCC($settings->mailAddress, $sender);
}

// also notify developer
$mail->addBCC('u.belitz@gmx.de', 'Ulrich Belitz');

//$mail->addAddress('ellen@example.com');               // Name is optional
//$mail->addReplyTo('info@example.com', 'Information');
//$mail->addCC('cc@example.com');
//$mail->addBCC('bcc@example.com');

$mail->WordWrap = 50; // Set word wrap to 50 characters

/*if ($action == "notifyProvider") {
    //$pathToPdf = '../Data/' . strtolower($_SESSION['clientName']) . '/' . 'Dienstplan-' . $year . '-' . $month . '.pdf';
    $pathToPdf = 'test.pdf';

    //$roster = new Roster($year, $month);
    //$roster->printPdf($pathToPdf);

    $mail->addAttachment($pathToPdf);

    //TODO delete file afterwards
}*/


//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
$mail->isHTML(true); // Set email format to HTML

$mail->Subject = $subject;
$mail->Body = $message;


//$mail->SMTPDebug = 1;

if (!$mail->send()) {
    echo 'Nachricht konnte nicht gesendet werden. ';
    echo 'Fehlermeldung: ' . $mail->ErrorInfo;
    exit;
}

echo 'Nachricht wurde gesendet.';

?>