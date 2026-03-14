<?php


ob_start("ob_gzhandler");
date_default_timezone_set("Africa/Algiers");
include "SendMail.php";
$conn = openConnection();
$jconn = openIConnection();
$query = "SELECT IDColumnName, MACAddress, SMTPServer, SMTPFrom, SMTPUsername, SMTPPassword, NightShiftMaxOutTime, ExitTerminal, EX3, EmployeeCodeLength FROM OtherSettingMaster";
$main_result = selectData($conn, $query);
if (checkMAC($conn) == true && noTASoftware("", $main_result[1]) == false && 2 < strlen($main_result[2]) && 2 < strlen($main_result[3])) {
    $dept_query = "";
    $div_query = "";
    $file_name = "";
    $count = 0;
    $query = "SELECT Username, Usermail, Userstatus FROM UserMaster WHERE LENGTH(Usermail) > 5 AND Userlevel LIKE '%18D%' AND Username NOT LIKE 'virdi'";
    $user_result = mysqli_query($conn, $query);
    while ($user_cur = mysqli_fetch_row($user_result)) {
        $file_name = "mailer\\\\Odd-Log-Mailer-" . $user_cur[0] . "-" . insertToday() . "" . getNow() . ".xls";
        $dept_div_query = mailerDeptDiv($conn, $iconn, $jconn, $user_cur[0], $user_cur[2]);
        $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tenter.e_date, tuser.idno, tuser.remark, count(tuser.id) FROM tuser, tgroup, tenter, tgate WHERE tenter.e_group = tgroup.id AND tenter.e_id = tuser.id AND tenter.g_id = tgate.id AND tgate.exit = 0 AND ((tenter.e_time > '" . $main_result[6] . "00' AND tgroup.NightFlag = 1) OR (tenter.e_time > '000000' AND tgroup.NightFlag = 0)) " . $dept_div_query . " AND tenter.e_date = '" . insertToday() . "' GROUP BY tuser.name, tuser.dept, tuser.company, tgroup.name, tenter.e_date, tuser.idno, tuser.remark ORDER BY tuser.id, tenter.e_time";
        $result = mysqli_query($conn, $query);
        $counter = 0;
        if (0 < mysqli_num_rows($result)) {
            $handle = fopen($file_name, "w");
            $data = "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%'>";
            fwrite($handle, $data);
            $data = "<tr><td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> </tr>";
            fwrite($handle, $data);
            $data = "<tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $main_result[0] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Shift</font></td> <td><font face='Verdana' size='2'>Date</font></td> </tr>";
            fwrite($handle, $data);
            while ($cur = mysqli_fetch_row($result)) {
                if ($cur[8] % 2 != 0) {
                    if ($cur[3] == "") {
                        $cur[3] = "&nbsp;";
                    }
                    if ($cur[7] == "") {
                        $cur[7] = "&nbsp;";
                    }
                    $data = "<tr><td><font face='Verdana' size='1'>" . addZero($cur[0], $main_result[9]) . "</font></td> <td><font face='Verdana' size='1'>" . $cur[1] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[8] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[2] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[3] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[7] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[4] . "</font></td> <td><font face='Verdana' size='1'>" . displayDate($cur[5]) . "</font></td> </tr>";
                    fwrite($handle, $data);
                    $counter++;
                }
            }
            $data = "</table>";
            $mail_query = "SELECT MailerText FROM MailerText WHERE MailerType = 'OddLog'";
            $mail_result = selectData($conn, $mail_query);
            $mail_text = $mail_result[0];
            $u_email = $user_cur[1];
            $u_name = $user_cur[0];
            fwrite($handle, $data);
            fclose($handle);
            if (sendMail("Daily Odd Logs Mailer: Total Record(s) - " . $counter, str_replace("\r", "<br>", $mail_text), $mail_text, $file_name, "", "Virdi Admin", $u_email, $u_name, "", "", "", "")) {
                $count++;
            }
            if (getRegister($main_result[1], 7) != "73") {
                unlink($file_name);
            }
        }
    }
    if (0 < $count) {
        $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('Sent Odd Log Mailer to " . $count . " Users', " . insertToday() . ", '" . getNow() . "')";
        updateIData($jconn, $query, true);
    }
}
print "Odd Log Mailer Process Executed Successfully";

?>