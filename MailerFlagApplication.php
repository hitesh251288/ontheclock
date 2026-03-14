<?php


ob_start("ob_gzhandler");
set_time_limit(0);
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
    $query = "SELECT Username, Usermail, Userlevel, Userstatus FROM UserMaster WHERE LENGTH(Usermail) > 5 AND Userlevel LIKE '%18D%' AND Userlevel LIKE '%34V%' AND Username NOT LIKE 'virdi'";
    $user_result = mysqli_query($conn, $query);
    while ($user_cur = mysqli_fetch_row($user_result)) {
        $file_name = "mailer\\\\Flag-Application-Mailer-" . $user_cur[0] . "-" . insertToday() . "" . getNow() . ".xls";
        $dept_div_query = mailerDeptDiv($conn, $iconn, $jconn, $user_cur[0], $user_cur[2]);
        $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tuser.idno, tuser.remark, FlagApplication.DateFrom, FlagApplication.DateTo, FlagApplication.Flag, FlagApplication.Remark, FlagApplication.A1, FlagApplication.A2, FlagApplication.A3 FROM tuser, FlagApplication WHERE tuser.id = FlagApplication.e_id " . $dept_div_query . " ORDER BY tuser.id, FlagApplication.DateFrom";
        $counter = 0;
        $result = mysqli_query($conn, $query);
        $approval = "";
        if (0 < mysqli_num_rows($result)) {
            $handle = fopen($file_name, "w");
            $data = "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%'>";
            fwrite($handle, $data);
            $data = "<tr><td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> </tr>";
            fwrite($handle, $data);
            $data = "<tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $main_result[0] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Date From</font></td> <td><font face='Verdana' size='2'>Date To</font></td> <td><font face='Verdana' size='2'>Flag</font></td> <td><font face='Verdana' size='2'><b>Remark</b></font></td> <td><font face='Verdana' size='2'><b>Approved</b></font></td> <td><font face='Verdana' size='2'>Authorized</font></td></tr>";
            fwrite($handle, $data);
            while ($cur = mysqli_fetch_row($result)) {
                if ((strpos($user_cur[2], "34E") == true || strpos($user_cur[2], "34D") == true) && $cur[11] == 0 && $cur[12] == 0 || strpos($user_cur[2], "34D") == true && $cur[11] == 1 && $cur[12] == 0) {
                    if ($cur[11] == 0 && $cur[12] == 0) {
                        $approval = "First";
                    } else {
                        $approval = "Final";
                    }
                    if ($cur[3] == "") {
                        $cur[3] = "&nbsp;";
                    }
                    if ($cur[4] == "") {
                        $cur[4] = "&nbsp;";
                    }
                    if ($cur[5] == "") {
                        $cur[5] = "&nbsp;";
                    }
                    $data = "<tr><td><font face='Verdana' size='1'>" . addZero($cur[0], $main_result[9]) . "</font></td> <td><font face='Verdana' size='1'>" . $cur[1] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[4] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[2] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[3] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[5] . "</font></td> <td><font face='Verdana' size='1'>" . displayDate($cur[6]) . "</font></td> <td><font face='Verdana' size='1'>" . displayDate($cur[7]) . "</font></td> <td><font face='Verdana' size='1'>" . $cur[8] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[9] . "</font></td> <td><font face='Verdana' size='1'>";
                    if ($cur[11] == 0) {
                        $data .= "No";
                    } else {
                        $data .= "Yes";
                    }
                    $data .= "</font></td> <td><font face='Verdana' size='1'>";
                    if ($cur[12] == 0) {
                        $data .= "No";
                    } else {
                        $data .= "Yes";
                    }
                    $data .= "</font></td></tr>";
                    fwrite($handle, $data);
                    $counter++;
                }
            }
            $data = "</table>";
            if (0 < $counter) {
                $mail_query = "SELECT MailerText FROM MailerText WHERE MailerType = 'FlagApplication'";
                $mail_result = selectData($conn, $mail_query);
                $mail_text = $mail_result[0];
                $u_email = $user_cur[1];
                $u_name = $user_cur[0];
                fwrite($handle, $data);
                fclose($handle);
                if (sendMail("Pending Flag (Leaves) " . $approval . " Approval Mailer: Total Record(s) - " . $counter, str_replace("\r", "<br>", $mail_text), $mail_text, "", "Virdi Admin", $u_email, $u_name, "", "", "", "")) {
                    $count++;
                }
                if (getRegister($main_result[1], 7) != "73") {
                    unlink($file_name);
                }
            }
        }
    }
    if (0 < $count) {
        $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('Sent Daily Late Arrival Mailer to " . $count . " Users', " . insertToday() . ", '" . getNow() . "')";
        updateIData($jconn, $query, true);
    }
}
print "Late Arrival Mailer Process Executed Successfully";

?>