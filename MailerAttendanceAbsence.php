<?php


ob_start("ob_gzhandler");
set_time_limit(0);
date_default_timezone_set("Africa/Algiers");
include "SendMail.php";
$conn = openConnection();
$jconn = openIConnection();
$dept_query = "";
$div_query = "";
$file_name = "";
$count = 0;
$query = "SELECT IDColumnName, MACAddress, SMTPServer, SMTPFrom, SMTPUsername, SMTPPassword, NightShiftMaxOutTime, ExitTerminal, EX3, EmployeeCodeLength FROM OtherSettingMaster";
$main_result = selectData($conn, $query);
if (checkMAC($conn) == true && noTASoftware("", $main_result[1]) == false && 2 < strlen($main_result[2]) && 2 < strlen($main_result[3])) {
    $query = "SELECT Username, Usermail, Userstatus FROM UserMaster WHERE LENGTH(Usermail) > 5 AND Userlevel LIKE '%18D%' AND Username NOT LIKE 'virdi'";
    $user_result = mysqli_query($conn, $query);
    while ($user_cur = mysqli_fetch_row($user_result)) {
        $file_name = "mailer\\Attendance-Absence-Mailer-" . $user_cur[0] . "-" . insertToday() . "" . getNow() . ".xls";
        $handle = fopen($file_name, "w");
        $dept_div_query = mailerDeptDiv($conn, $iconn, $jconn, $user_cur[0], $user_cur[2]);
        $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tenter.e_date, tenter.e_time, tgate.name, tuser.idno, tuser.remark FROM tuser, tgroup, tenter, tgate WHERE tenter.e_group = tgroup.id AND tenter.e_id = tuser.id AND tenter.g_id = tgate.id AND tgate.exit = 0 AND ((tenter.e_time > '" . $main_result[6] . "00' AND tgroup.NightFlag = 1) OR (tenter.e_time > '000000' AND tgroup.NightFlag = 0)) AND tenter.e_date = '" . insertToday() . "' " . $dept_div_query . " ORDER BY tuser.id";
        $counter = 0;
        $last_id = "";
        $last_date = "";
        $result = mysqli_query($conn, $query);
        if (0 < mysqli_num_rows($result)) {
            $data = "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%'>";
            fwrite($handle, $data);
            $data = "<tr><td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td></tr>";
            fwrite($handle, $data);
            $data = "<tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $main_result[0] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Shift</font></td> <td><font face='Verdana' size='2'>Date</font></td> <td><font face='Verdana' size='2'>Time</font></td> <td><font face='Verdana' size='2'>Terminal</font></td></tr>";
            fwrite($handle, $data);
            while ($cur = mysqli_fetch_row($result)) {
                if (!($cur[0] == $last_id && $cur[5] == $last_date)) {
                    if ($cur[3] == "") {
                        $cur[3] = "&nbsp;";
                    }
                    if ($cur[8] == "") {
                        $cur[8] = "&nbsp;";
                    }
                    if ($cur[9] == "") {
                        $cur[9] = "&nbsp;";
                    }
                    $data = "<tr><td><font face='Verdana' size='1'>" . addZero($cur[0], $main_result[9]) . "</font></td> <td><font face='Verdana' size='1'>" . $cur[1] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[8] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[2] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[3] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[9] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[4] . "</font></td> <td><font face='Verdana' size='1'>" . displayDate($cur[5]) . "</font></td> <td><font face='Verdana' size='1'>" . displayVirdiTime($cur[6]) . "</font></td> <td><font face='Verdana' size='1'>" . $cur[7] . "</font></td></tr>";
                    fwrite($handle, $data);
                    $last_id = $cur[0];
                    $last_date = $cur[5];
                    $counter++;
                }
            }
            $data = "</table>";
        }
        $counter_ = 0;
        $darray = getDate();
        $day = $darray["weekday"];
        $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tuser.idno, tuser.remark FROM tuser, tgroup WHERE tuser.reg_date < '" . insertToday() . "0000' AND tuser.group_id = tgroup.id " . $dept_div_query . " ";
        $query = $query . employeeStatusQuery("ACT");
        $query = $query . " AND tuser.OT1 NOT LIKE '" . $day . "' AND tuser.OT2 NOT LIKE '" . $day . "'";
        $query = $query . " AND tuser.id NOT IN (SELECT e_id FROM FlagDayRotation WHERE e_date = " . insertToday() . ") ";
        $query = $query . " AND tuser.id NOT IN (SELECT DISTINCT(tenter.e_id) FROM tenter, tgate, tgroup WHERE tenter.g_id = tgate.id AND tenter.e_group = tgroup.id AND tgate.exit = 0 AND ((tenter.e_time > '" . $main_result[6] . "00' AND tgroup.NightFlag = 1) OR (tenter.e_time > '000000' AND tgroup.NightFlag = 0))";
        $query = $query . " AND tenter.e_date = '" . insertToday() . "'";
        $query = $query . ") ";
        if (getNow() < $main_result[6]) {
            $query .= " AND tgroup.NightFlag = 0";
        }
        $query = $query . " ORDER BY tuser.id";
        $result = mysqli_query($conn, $query);
        if (0 < mysqli_num_rows($result)) {
            $data = "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%'>";
            fwrite($handle, $data);
            $data = "<tr><td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> </tr>";
            fwrite($handle, $data);
            $data = "<tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $main_result[0] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Current Shift</font></td> <td><font face='Verdana' size='2'>Date</font></td> </tr>";
            fwrite($handle, $data);
            while ($cur = mysqli_fetch_row($result)) {
                if ($cur[3] == "") {
                    $cur[3] = "&nbsp;";
                }
                if ($cur[5] == "") {
                    $cur[5] = "&nbsp;";
                }
                if ($cur[6] == "") {
                    $cur[6] = "&nbsp;";
                }
                $data = "<tr><td><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'><font face='Verdana' size='1'>" . addZero($cur[0], $main_result[9]) . "</font></td> <td><font face='Verdana' size='1'>" . $cur[1] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[5] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[2] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[3] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[6] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[4] . "</font></td> <td><font face='Verdana' size='1'>" . displayToday() . "</font></td> </tr>";
                fwrite($handle, $data);
                $counter_++;
            }
            $data = "</table>";
        }
        $mail_query = "SELECT MailerText FROM MailerText WHERE MailerType = 'Attendance'";
        $mail_result = selectData($conn, $mail_query);
        $mail_text = $mail_result[0];
        $u_email = $user_cur[1];
        $u_name = $user_cur[0];
        fwrite($handle, $data);
        fclose($handle);
        if (sendMail("Daily Attendance/ Absence Mailer: Total Record(s) - Attendance(" . $counter . ") - Absence(" . $counter_ . ")", str_replace("\r", "<br>", $mail_text), $mail_text, $file_name, "", "Virdi Admin", $u_email, $u_name, "", "", "", "")) {
            $count++;
        }
        if (getRegister($main_result[1], 7) != "73") {
            unlink($file_name);
        }
    }
    if (0 < $count) {
        $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('Sent Daily Attendance/ Absence Mailer to " . $count . " Users', " . insertToday() . ", '" . getNow() . "')";
        updateIData($jconn, $query, true);
        print "Attendance/ Absence Mailer Process Executed Successfully";
        return 1;
    }
} else {
    print "Un Registered Application OR Mail Server NOT Defined";
}

?>