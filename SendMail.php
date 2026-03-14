<?php
error_reporting(E_ALL);
include "Functions.php";
//include "PHPMailerAutoload.php";
require 'E:/wamp/www/ontheclock/PHPMailer/src/PHPMailer.php';
require 'E:/wamp/www/ontheclock/PHPMailer/src/SMTP.php';
require 'E:/wamp/www/ontheclock/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Instantiate PHPMailer correctly

function sendMail($subject, $html, $text, $attach, $fromMail, $fromName, $toMail, $toName, $ccMail, $ccName, $bccMail, $bccName)
{
    if ($html == "") {
        $html = $subject;
    }
    $conn = openConnection();
    $query = "SELECT SMTPServer, SMTPUsername, SMTPPassword, SMTPPort, SMTPSSL FROM OtherSettingMaster";
    $main_result = selectData($conn, $query);
    $mailto = $toName . " <" . $toMail . ">";
    $mail = new PHPMailer(true);
    $mail->IsSMTP();
    $mail->Host = $main_result[0];
    $mail->SMTPAuth = true;
    $mail->Username = $main_result[1];
//    $mail->SetFrom($main_result[1], "Virdi Admin");
    if (!empty($main_result[1])) {
        $mail->setFrom($main_result[1], "Virdi Admin");
    } else {
        echo "Error: From email is empty!";
        return false;
    }
    $mail->Password = $main_result[2];
    $mail->Port = $main_result[3];
    $mail->AddAddress($toMail, $toName);
    $mail->Subject = $subject;
    $mail->Body = $html;
    $mail->AltBody = "To view the message, please use an HTML compatible email viewer!";
    if ($main_result[4] != "None") {
        $mail->SMTPSecure = $main_result[4];
    }
    if ($attach != "") {
        $fileName = substr($attach, strrpos($attach, "\\") + 1, strlen($attach));
        $mail->AddAttachment($attach, $fileName);
    }
    if ($ccMail != "") {
        $mail->AddCC($ccMail, $ccName);
    }
    if ($bccMail != "") {
        $mail->AddBCC($bccMail, $bccName);
    }
    if (!$mail->send()) {
        echo "Mailer Error: " . $mail->ErrorInfo;
        return false;
    }
    echo "Message sent!";
    return true;
}

?>