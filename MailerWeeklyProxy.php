<?php


ob_start("ob_gzhandler");
set_time_limit(0);
date_default_timezone_set("Africa/Algiers");
include "SendMail.php";
$conn = openConnection();
$jconn = openIConnection();
$query = "SELECT IDColumnName, MACAddress, SMTPServer, SMTPFrom, SMTPUsername, SMTPPassword, NightShiftMaxOutTime, EmployeeCodeLength FROM OtherSettingMaster";
$main_result = selectData($conn, $query);
if (checkMAC($conn) == true && noTASoftware("", $main_result[1]) == false && 2 < strlen($main_result[2]) && 2 < strlen($main_result[3])) {
    $dept_query = "";
    $div_query = "";
    $file_name = "";
    $count = 0;
    $query = "SELECT Username, Usermail, Userstatus FROM UserMaster WHERE LENGTH(Usermail) > 5 AND Userlevel LIKE '%18D%' AND Username NOT LIKE 'virdi'";
    $user_result = mysqli_query($conn, $query);
    while ($user_cur = mysqli_fetch_row($user_result)) {
        $file_name = "mailer\\WeeklyProxy-Mailer-" . $user_cur[0] . "-" . insertToday() . "" . getNow() . ".xls";
        $dept_div_query = mailerDeptDiv($conn, $iconn, $jconn, $user_cur[0], $user_cur[2]);
        $query = "SELECT COUNT(AttendanceMaster.AttendanceID), tuser.id, tuser.name, tuser.dept, tuser.company, tuser.idno, tuser.remark FROM tuser, AttendanceMaster, tgroup WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.EmployeeID = tuser.id AND AttendanceMaster.ADate >= '" . getLastDay(insertToday(), 7) . "' AND AttendanceMaster.ADate < '" . insertToday() . "' AND AttendanceMaster.Flag = 'Proxy' " . $dept_div_query . " GROUP BY tuser.id, tuser.name, tuser.dept, tuser.company, tuser.idno, tuser.remark ";
        $counter = 0;
        $last_id = "";
        $last_date = "";
        $result = mysqli_query($conn, $query);
        if (0 < mysqli_num_rows($result)) {
            $handle = fopen($file_name, "w");
            $data = "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%'>";
            fwrite($handle, $data);
            fwrite($handle, $data);
            $data = "<tr><td><font face='Verdana' size='2'>From</font></td><td><font face='Verdana' size='2'>To</font></td><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $main_result[0] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td><td><font face='Verdana' size='2'><b>Proxy Count</b></font></td></tr>";
            fwrite($handle, $data);
            while ($cur = mysqli_fetch_row($result)) {
                if ($cur[4] == "") {
                    $cur[4] = "&nbsp;";
                }
                if ($cur[5] == "") {
                    $cur[5] = "&nbsp;";
                }
                if ($cur[6] == "") {
                    $cur[6] = "&nbsp;";
                }
                $data = "<tr><td><font face='Verdana' size='1'>" . displayDate(getLastDay(insertToday(), 7)) . "</font></td><td><font face='Verdana' size='1'>" . displayDate(getLastDay(insertToday(), 1)) . "</font></td><td><font face='Verdana' size='1'>" . addZero($cur[1], $main_result[7]) . "</font></td> <td><font face='Verdana' size='1'>" . $cur[2] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[5] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[3] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[4] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[6] . "</font></td> <td><font face='Verdana' size='1'><b>" . $cur[0] . "</b></font></td></tr>";
                fwrite($handle, $data);
                $last_id = $cur[1];
                $counter++;
            }
            $data = "</table>";
            $mail_query = "SELECT MailerText FROM MailerText WHERE MailerType = 'WeeklyProxy'";
            $mail_result = selectData($conn, $mail_query);
            $mail_text = $mail_result[0];
            $u_email = $user_cur[1];
            $u_name = $user_cur[0];
            fwrite($handle, $data);
            fclose($handle);
            if (sendMail("Weekly Proxy Mailer: Total Record(s) - " . $counter, str_replace("\r", "<br>", $mail_text), $mail_text, $file_name, "", "Virdi Admin", $u_email, $u_name, "", "", "", "")) {
                $count++;
            }
            if (getRegister($main_result[1], 7) != "73") {
                unlink($file_name);
            }
        }
    }
    if (0 < $count) {
        $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('Sent Weekly Proxy Mailer to " . $count . " Users', " . insertToday() . ", '" . getNow() . "')";
        updateIData($jconn, $query, true);
    }
    print "Weekly Proxy Mailer Process Executed Successfully";
} else {
    print "Weekly Proxy Mailer COULD NOT be Executed";
}

?>