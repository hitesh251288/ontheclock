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
        $file_name = "mailer\\\\Early-Exit-Mailer-" . $user_cur[0] . "-" . insertToday() . "" . getNow() . ".xls";
        $dept_div_query = mailerDeptDiv($conn, $iconn, $jconn, $user_cur[0], $user_cur[2]);
        $query = "";
        if ($main_result[7] == "Yes") {
            $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tenter.e_date, tenter.e_time, tgate.name, tuser.idno, tuser.remark, tgroup.Close, tgroup.NightFlag FROM tuser, tgroup, tenter, tgate WHERE tenter.e_group = tgroup.id AND tenter.e_id = tuser.id AND tenter.g_id = tgate.id AND tgroup.Close >= '0000' AND tuser.id > 0 AND tgate.exit = 0 " . $dept_div_query . " AND tuser.id IN (SELECT DISTINCT(tuser.id) FROM tuser, tenter, tgate WHERE tenter.e_id = tuser.id AND tenter.g_id = tgate.id AND tgate.exit = 1) AND ((tenter.e_time < '240000' AND tgroup.NightFlag =0) OR (tenter.e_time < '" . $main_result[6] . "00' AND tgroup.NightFlag = 1)) AND tenter.e_date = '" . getLastDay(insertToday(), 1) . "' ORDER BY tuser.id, tenter.e_time";
        } else {
            $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tenter.e_date, tenter.e_time, tgate.name, tuser.idno, tuser.remark, tgroup.Close, tgroup.NightFlag FROM tuser, tgroup, tenter, tgate WHERE tenter.e_group = tgroup.id AND tenter.e_id = tuser.id AND tgroup.Close >= '0000' AND tuser.id > 0 AND tenter.g_id = tgate.id " . $dept_div_query . " AND ((tenter.e_time < '240000' AND tgroup.NightFlag =0) OR (tenter.e_time < '" . $main_result[6] . "00' AND tgroup.NightFlag = 1))  AND tenter.e_date = '" . getLastDay(insertToday(), 1) . "' ORDER BY tuser.id, tenter.e_time";
        }
        $last_id = "";
        $last_date = "";
        $data0 = "";
        $data1 = "";
        $data2 = "";
        $data3 = "";
        $data4 = "";
        $data5 = "";
        $data6 = "";
        $data7 = "";
        $data8 = "";
        $data9 = "";
        $data10 = "";
        $ecount = 0;
        $counter = 0;
        $result = mysqli_query($conn, $query);
        if (0 < mysqli_num_rows($result)) {
            $handle = fopen($file_name, "w");
            $data = "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%'>";
            fwrite($handle, $data);
            $data = "<tr><td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> </tr>";
            fwrite($handle, $data);
            $data = "<tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $main_result[0] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Shift</font></td> <td><font face='Verdana' size='2'>Close</font></td> <td><font face='Verdana' size='2'>Date</font></td> <td><font face='Verdana' size='2'><b>Depart</b></font></td> <td><font face='Verdana' size='2'><b>Early <font size=1>(Min)</font></b></font></td> <td><font face='Verdana' size='2'>Terminal</font></td></tr>";
            fwrite($handle, $data);
            while ($cur = mysqli_fetch_row($result)) {
                if ($cur[0] == $last_id && $cur[5] == $last_date) {
                    $ecount++;
                } else {
                    if ($data6 < getEarlyTime(insertToday(), $data10, $main_result[8]) && strlen($data5) == 8 && 0 < $ecount) {
                        if ($data3 == "") {
                            $data3 = "&nbsp;";
                        }
                        if ($data8 == "") {
                            $data8 = "&nbsp;";
                        }
                        if ($data9 == "") {
                            $data9 = "&nbsp;";
                        }
                        $data = "<tr><td><font face='Verdana' size='1'>" . addZero($data0, $main_result[9]) . "</font></td> <td><font face='Verdana' size='1'>" . $data1 . "</font></td> <td><font face='Verdana' size='1'>" . $data8 . "</font></td> <td><font face='Verdana' size='1'>" . $data2 . "</font></td> <td><font face='Verdana' size='1'>" . $data3 . "</font></td> <td><font face='Verdana' size='1'>" . $data9 . "</font></td> <td><font face='Verdana' size='1'>" . $data4 . "</font></td> <td><font face='Verdana' size='1'>" . displayVirdiTime($data10 . "00") . "</font></td> <td><font face='Verdana' size='1'>" . displayDate($data5) . "</font></td> <td><font face='Verdana' size='1'><b>" . displayVirdiTime($data6) . "</b></font></td> <td><font face='Verdana' size='1'><b>" . getEarlyMin(insertToday(), $data10, $data6) . "</b></font></td> <td><font face='Verdana' size='1'>" . $data7 . "</font></td></tr>";
                        fwrite($handle, $data);
                        $counter++;
                    }
                    $last_id = $cur[0];
                    $last_date = $cur[5];
                    $ecount = 0;
                }
                $data0 = $cur[0];
                $data1 = $cur[1];
                $data2 = $cur[2];
                $data3 = $cur[3];
                $data4 = $cur[4];
                $data5 = $cur[5];
                $data6 = $cur[6];
                $data7 = $cur[7];
                $data8 = $cur[8];
                $data9 = $cur[9];
                $data10 = $cur[10];
            }
            if ($data6 < getEarlyTime(insertToday(), $data10, $main_result[8]) && 0 < $ecount) {
                if ($data3 == "") {
                    $data3 = "&nbsp;";
                }
                if ($data8 == "") {
                    $data8 = "&nbsp;";
                }
                if ($data9 == "") {
                    $data9 = "&nbsp;";
                }
                $data = "<tr><td><font face='Verdana' size='1'>" . addZero($data0, $main_result[9]) . "</font></td> <td><font face='Verdana' size='1'>" . $data1 . "</font></td> <td><font face='Verdana' size='1'>" . $data8 . "</font></td> <td><font face='Verdana' size='1'>" . $data2 . "</font></td> <td><font face='Verdana' size='1'>" . $data3 . "</font></td> <td><font face='Verdana' size='1'>" . $data9 . "</font></td> <td><font face='Verdana' size='1'>" . $data4 . "</font></td> <td><font face='Verdana' size='1'>" . displayVirdiTime($data10 . "00") . "</font></td> <td><font face='Verdana' size='1'>" . displayDate($data5) . "</font></td> <td><font face='Verdana' size='1'><b>" . displayVirdiTime($data6) . "</b></font></td> <td><font face='Verdana' size='1'><b>" . getEarlyMin(insertToday(), $data10, $data6) . "</b></font></td> <td><font face='Verdana' size='1'>" . $data7 . "</font></td></tr>";
                fwrite($handle, $data);
                $counter++;
            }
            $data = "</table>";
            $mail_query = "SELECT MailerText FROM MailerText WHERE MailerType = 'EarlyExit'";
            $mail_result = selectData($conn, $mail_query);
            $mail_text = $mail_result[0];
            $u_email = $user_cur[1];
            $u_name = $user_cur[0];
            fwrite($handle, $data);
            fclose($handle);
            if (sendMail("Daily Early Exit Mailer: Total Record(s) - " . $counter, str_replace("\r", "<br>", $mail_text), $mail_text, $file_name, "", "Virdi Admin", $u_email, $u_name, "", "", "", "")) {
                $count++;
            }
            if (getRegister($main_result[1], 7) != "73") {
                unlink($file_name);
            }
        }
    }
    if (0 < $count) {
        $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('Sent Early Exit Mailer to " . $count . " Users', " . insertToday() . ", '" . getNow() . "')";
        updateIData($jconn, $query, true);
    }
}
print "Early Exit Mailer Process Executed Successfully";

?>