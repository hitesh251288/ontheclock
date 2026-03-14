<?php


ob_start("ob_gzhandler");
set_time_limit(0);
date_default_timezone_set("Africa/Algiers");
include "SendMail.php";
$conn = openConnection();
$query = "SELECT IDColumnName, MACAddress, SMTPServer, SMTPFrom, SMTPUsername, SMTPPassword, NightShiftMaxOutTime, ExitTerminal, EX3, EmployeeCodeLength, EmployeeEmailField FROM OtherSettingMaster";
$main_result = selectData($conn, $query);
$txtMACAddress = encryptDecrypt($main_result[1]);
$txtEmployeeEmailField = $main_result[10];
$weirdTimeDisplay = false;
if (getWeirdClient($txtMACAddress)) {
    $weirdTimeDisplay = true;
}
if (checkMAC($conn) == true && noTASoftware("", $main_result[1]) == false && 2 < strlen($main_result[2]) && 2 < strlen($main_result[3])) {
    $dept_query = "";
    $div_query = "";
    $file_name = "";
    $counter = 0;
    $count = 0;
    $query = "SELECT DISTINCT(EmployeeID), tuser.Name, tuser." . $main_result[10] . " FROM TaskMaster, tuser WHERE Status = 0 AND Type = 'Email' AND TaskMaster.EmployeeID = tuser.id ";
    $user_result = mysqli_query($conn, $query);
    while ($user_cur = mysqli_fetch_row($user_result)) {
        $query = "SELECT TaskID, Username, Task, TDate, Importance FROM TaskMaster WHERE EmployeeID = '" . trim($user_cur[0]) . "'";
        $result = mysqli_query($conn, $query);
        for ($data = "Dear " . $user_cur[1] . "<br><br>" . "Please make a note of the below Tasks assigned to you<br><br>" . "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>" . "<tr><td><b>Assigned By</b></td> <td><b>Date</b></td> <td><b>Importance</b></td> <td><b>Task</b></td></tr>"; $cur = mysqli_fetch_row($result); $counter++) {
            $data .= "<tr><td>" . $cur[1] . "</td> <td>" . displayDate($cur[3]) . "</td> <td>" . $cur[4] . "</td> <td>" . $cur[2] . "</td></tr>";
        }
        $data .= "</table><br><br><br>This is a System Generated Mail. DO NOT REPLY.";
        if (sendMail("Daily Task Schedular Mailer: Total Record(s) - " . $counter, $data, "", "", $main_result[3], "Virdi Admin", $user_cur[2], $user_cur[1], "", "", "", "")) {
            $count++;
        } else {
            print "Unable to Send Mail";
        }
    }
    if (0 < $count) {
        $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('Sent Daily Task Schedular Mailer to " . $count . " Employees', " . insertToday() . ", '" . getNow() . "')";
        updateData($conn, $query, true);
    }
}
print "Daily Task Schedular Mailer Executed Successfully";

?>