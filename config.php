<?php


include "Functions.php";
$conn = openConnection();
$query = "SELECT SMTPServer, SMTPUsername, SMTPPassword, SMTPPort FROM OtherSettingMaster";
$main_result = selectData($conn, $query);
$smtp_flag = "disabled";
if (2 < strlen($main_result[1])) {
    $smtp_flag = "enabled";
}
$site["smtp_mode"] = $smtp_flag;
$site["smtp_port"] = "25";
$site["smtp_port"] = $main_result[3];
$site["smtp_host"] = $main_result[0];
$site["smtp_username"] = $main_result[1];
$site["smtp_password"] = $main_result[2];

?>