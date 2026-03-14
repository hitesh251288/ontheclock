<?php


ob_start("ob_gzhandler");
set_time_limit(0);
date_default_timezone_set("Africa/Algiers");
include "SendMail.php";
$conn = openConnection();
$jconn = openIConnection();
$query = "SELECT IDColumnName, MACAddress, SMTPServer, SMTPFrom, SMTPUsername, SMTPPassword, NightShiftMaxOutTime, ExitTerminal, EX3, EmployeeCodeLength FROM OtherSettingMaster";
$main_result = selectData($conn, $query);
$txtMACAddress = encryptDecrypt($main_result[1]);
$weirdTimeDisplay = false;
if (getWeirdClient($txtMACAddress)) {
    $weirdTimeDisplay = true;
}
if (checkMAC($conn) == true && noTASoftware("", $main_result[1]) == false && 2 < strlen($main_result[2]) && 2 < strlen($main_result[3])) {
    $dept_query = "";
    $div_query = "";
    $file_name = "";
    $count = 0;
    $query = "SELECT Username, Usermail, Userstatus FROM UserMaster WHERE LENGTH(Usermail) > 5 AND Userlevel LIKE '%18D%' AND Username NOT LIKE 'virdi'";
    $user_result = mysqli_query($conn, $query);
    while ($user_cur = mysqli_fetch_row($user_result)) {
        $file_name = "mailer\\\\Late-Arrival-Mailer-" . $user_cur[0] . "-" . insertToday() . "" . getNow() . ".xls";
        $dept_div_query = mailerDeptDiv($conn, $iconn, $jconn, $user_cur[0], $user_cur[2]);
        $date_count = insertToday();
        $query = "";
        if ($main_result[7] == "Yes") {
            $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tenter.e_date, tenter.e_time, tgate.name, tuser.idno, tuser.remark, tgroup.GraceTo, tgroup.Start FROM tuser, tgroup, tenter, tgate WHERE tenter.e_group = tgroup.id AND tenter.e_id = tuser.id AND tenter.g_id = tgate.id AND tgroup.GraceTo >= '0000' AND tgate.exit = 0 " . $dept_div_query . " AND tuser.id IN (SELECT DISTINCT(tuser.id) FROM tuser, tenter, tgate WHERE tenter.e_id = tuser.id AND tenter.g_id = tgate.id AND tgate.exit = 1) AND tenter.e_date = '" . insertToday() . "' ORDER BY tuser.id, tenter.e_time";
        } else {
            $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tenter.e_date, tenter.e_time, tgate.name, tuser.idno, tuser.remark, tgroup.GraceTo, tgroup.Start FROM tuser, tgroup, tenter, tgate WHERE tenter.e_group = tgroup.id AND tenter.e_id = tuser.id AND tgroup.GraceTo >= '0000' AND tenter.g_id = tgate.id " . $dept_div_query . " AND tenter.e_date = '" . insertToday() . "' ORDER BY tuser.id, tenter.e_time";
        }
        $last_id = "";
        $last_date = "";
        $counter = 0;
        $result = mysqli_query($conn, $query);
        if (0 < mysqli_num_rows($result)) {
            $handle = fopen($file_name, "w");
            $data = "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%'>";
            fwrite($handle, $data);
            $data = "<tr><td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> </tr>";
            fwrite($handle, $data);
            $data = "<tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $main_result[0] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Shift</font></td> <td><font face='Verdana' size='2'>Start</font></td> <td><font face='Verdana' size='2'>Grace</font></td> <td><font face='Verdana' size='2'>Date</font></td> <td><font face='Verdana' size='2'><b>Arrived</b></font></td> <td><font face='Verdana' size='2'><b>Late <font size=1>(Min)</font></b></font></td> <td><font face='Verdana' size='2'>Terminal</font></td></tr>";
            fwrite($handle, $data);
            while ($cur = mysqli_fetch_row($result)) {
                if (!($cur[0] == $last_id && $cur[5] == $last_date)) {
                    if (getLateTime(insertToday(), $cur[10], $main_result[8]) < $cur[6]) {
                        if ($cur[3] == "") {
                            $cur[3] = "&nbsp;";
                        }
                        if ($cur[8] == "") {
                            $cur[8] = "&nbsp;";
                        }
                        if ($cur[9] == "") {
                            $cur[9] = "&nbsp;";
                        }
                        $data = "<tr><td><font face='Verdana' size='1'>" . addZero($cur[0], $main_result[9]) . "</font></td> <td><font face='Verdana' size='1'>" . $cur[1] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[8] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[2] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[3] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[9] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[4] . "</font></td> <td><font face='Verdana' size='1'>" . displayVirdiTime($cur[11] . "00") . "</font></td> <td><font face='Verdana' size='1'>" . displayVirdiTime($cur[10] . "00") . "</font></td> <td><font face='Verdana' size='1'>" . displayDate($cur[5]) . "</font></td> <td><font face='Verdana' size='1'><b>" . displayVirdiTime($cur[6]) . "</b></font></td> <td><font face='Verdana' size='1'><b>";
                        if ($weirdTimeDisplay) {
                            $data .= getLateMMSS($date_count, $cur[11], $cur[6]);
                        } else {
                            $data .= getLateMin($date_count, $cur[11], $cur[6]);
                        }
                        $data .= "</b></font></td> <td><font face='Verdana' size='1'>" . $cur[7] . "</font></td></tr>";
                        fwrite($handle, $data);
                        $counter++;
                    }
                    $last_id = $cur[0];
                    $last_date = $cur[5];
                }
            }
            $data = "</table>";
            $mail_query = "SELECT MailerText FROM MailerText WHERE MailerType = 'LateArrival'";
            $mail_result = selectData($conn, $mail_query);
            $mail_text = $mail_result[0];
            $u_email = $user_cur[1];
            $u_name = $user_cur[0];
            fwrite($handle, $data);
            fclose($handle);
            if (sendMail("Daily Late Arrival Mailer: Total Record(s) - " . $counter, str_replace("\r", "<br>", $mail_result[0]), $mail_result[0], $file_name, "", "Virdi Admin", $u_email, $u_name, "", "", "", "")) {
                $count++;
            }
            if (getRegister($main_result[1], 7) != "73") {
                unlink($file_name);
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