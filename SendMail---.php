<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include "Functions.php";
require 'E:/wamp/www/ontheclock/PHPMailer/src/PHPMailer.php';
require 'E:/wamp/www/ontheclock/PHPMailer/src/SMTP.php';
require 'E:/wamp/www/ontheclock/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendMail($subject, $html, $text, $attach, $fromMail, $fromName, $toMail, $toName, $ccMail, $ccName, $bccMail, $bccName)
{
    if ($html == "") {
        $html = $subject;
    }

    $conn = openConnection();
    $query = "SELECT SMTPServer, SMTPUsername, SMTPPassword, SMTPPort, SMTPSSL FROM OtherSettingMaster";
    $main_result = selectData($conn, $query);

    if (empty($main_result) || count($main_result) < 5) {
        die("Error: SMTP settings not found in database.");
    }

    //$mail = new PHPMailer\PHPMailer\PHPMailer(true);
    $mail = new PHPMailer(true);
    $mail->SMTPDebug = 2;
    $mail->Debugoutput = 'html';

    $mail->isSMTP();
    $mail->Host = $main_result[0] ?? '';
    $mail->SMTPAuth = true;
    $mail->Username = $main_result[1] ?? '';
    $mail->Password = $main_result[2] ?? '';
    $mail->Port = $main_result[3] ?? 587;
    $mail->SMTPSecure = $main_result[4] ?? 'tls';

    $mail->setFrom($mail->Username, "Virdi Admin");
    $mail->addAddress($toMail, $toName);
    $mail->Subject = $subject;
    $mail->Body = $html;
    $mail->AltBody = "To view the message, please use an HTML-compatible email viewer!";

    if (!empty($attach)) {
        $fileName = basename($attach);
        $mail->addAttachment($attach, $fileName);
    }

    if (!empty($ccMail)) {
        $mail->addCC($ccMail, $ccName);
    }

    if (!empty($bccMail)) {
        $mail->addBCC($bccMail, $bccName);
    }

    if (!$mail->send()) {
        echo "Mailer Error: " . $mail->ErrorInfo;
        return false;
    }

    echo "Message sent!";
    return true;
}
