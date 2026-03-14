<?php


ob_start("ob_gzhandler");
set_time_limit(0);
date_default_timezone_set("Africa/Algiers");
include "SendMail.php";
$conn = openConnection();
$query = "SELECT IDColumnName, MACAddress, SMTPServer, SMTPFrom, SMTPUsername, SMTPPassword, NightShiftMaxOutTime, EmployeeCodeLength FROM OtherSettingMaster";
$main_result = selectData($conn, $query);
if (checkMAC($conn) == true && noTASoftware("", $main_result[1]) == false && 2 < strlen($main_result[2]) && 2 < strlen($main_result[3])) {
    $dept_query = "";
    $div_query = "";
    $file_name = "";
    $count = 0;
    $query = "SELECT Username, Usermail FROM UserMaster WHERE LENGTH(Usermail) > 5 AND Userlevel LIKE '%18D%' AND Username NOT LIKE 'virdi'";
    $user_result = mysql_query($query, $conn);
    while ($user_cur = mysql_fetch_row($user_result)) {
        $file_name = "mailer\\Attendance-Mailer-" . $user_cur[0] . "-" . insertToday() . "" . getNow() . ".xls";
        $query = "SELECT Dept FROM UserDept WHERE Username = '" . trim($user_cur[0]) . "'";
        $result = selectDataCol($conn, $query);
        $query = "";
        if ($result != "") {
            for ($i = 0; $i < count($result); $i++) {
                if ($result[$i] != "") {
                    if ($i == 0) {
                        if (count($result) == 1) {
                            $query = " AND (tuser.dept = '" . $result[$i] . "') ";
                        } else {
                            $query = " AND (tuser.dept = '" . $result[$i] . "' ";
                        }
                    } else {
                        if ($i == count($result) - 1) {
                            $query = $query . " OR tuser.dept = '" . $result[$i] . "') ";
                        } else {
                            $query = $query . " OR tuser.dept = '" . $result[$i] . "' ";
                        }
                    }
                }
            }
        }
        $dept_query = $query;
        $query = "SELECT UserDiv.Div FROM UserDiv WHERE Username = '" . trim($user_cur[0]) . "'";
        $result = selectDataCol($conn, $query);
        $query = "";
        if ($result != "") {
            for ($i = 0; $i < count($result); $i++) {
                if ($result[$i] != "") {
                    if ($i == 0) {
                        if (count($result) == 1) {
                            $query = " AND (tuser.company = '" . $result[$i] . "') ";
                        } else {
                            $query = " AND (tuser.company = '" . $result[$i] . "' ";
                        }
                    } else {
                        if ($i == count($result) - 1) {
                            $query = $query . " OR tuser.company = '" . $result[$i] . "') ";
                        } else {
                            $query = $query . " OR tuser.company = '" . $result[$i] . "' ";
                        }
                    }
                }
            }
        }
        $div_query = $query;
        $query = "\t\tSELECT q1.empid,ifnull(q2.adate,\r\n\t\t\t\t\t\t\t\t\t\t\t\tif(q1.days='MONDAY',date_format(date_add(date_format(date_format(curdate() - INTERVAL DAYOFWEEK(curdate())+5 DAY,'%Y%m%d'),'%Y-%m-%d'),interval 0 day),'%Y%m%d')\r\n\t\t\t\t\t\t\t\t\t\t\t\t,if(q1.days='TUESDAY',date_format(date_add(date_format(date_format(curdate() - INTERVAL DAYOFWEEK(curdate())+5 DAY,'%Y%m%d'),'%Y-%m-%d'),interval 1 day),'%Y%m%d')\r\n\t\t\t\t\t\t\t\t\t\t\t\t,if(q1.days='WEDNESDAY',date_format(date_add(date_format(date_format(curdate() - INTERVAL DAYOFWEEK(curdate())+5 DAY,'%Y%m%d'),'%Y-%m-%d'),interval 2 day),'%Y%m%d')\r\n\t\t\t\t\t\t\t\t\t\t\t\t,if(q1.days='THURSDAY',date_format(date_add(date_format(date_format(curdate() - INTERVAL DAYOFWEEK(curdate())+5 DAY,'%Y%m%d'),'%Y-%m-%d'),interval 3 day),'%Y%m%d')\r\n\t\t\t\t\t\t\t\t\t\t\t\t,if(q1.days='FRIDAY',date_format(date_add(date_format(date_format(curdate() - INTERVAL DAYOFWEEK(curdate())+5 DAY,'%Y%m%d'),'%Y-%m-%d'),interval 4 day),'%Y%m%d')\r\n\t\t\t\t\t\t\t\t\t\t\t\t,if(q1.days='SATURDAY',date_format(date_add(date_format(date_format(curdate() - INTERVAL DAYOFWEEK(curdate())+5 DAY,'%Y%m%d'),'%Y-%m-%d'),interval 5 day),'%Y%m%d')\r\n\t\t\t\t\t\t\t\t\t\t\t\t,if(q1.days='SUNDAY',date_format(date_add(date_format(date_format(curdate() - INTERVAL DAYOFWEEK(curdate())+5 DAY,'%Y%m%d'),'%Y-%m-%d'),interval 6 day),'%Y%m%d')\r\n\t\t\t\t\t\t\t\t\t\t\t\t,null\r\n\t\t\t\t\t\t\t\t\t\t\t\t)))))))\r\n\t\t\t\t\t\t\t\t\t\t\t\t)\r\n\t\t\t\t\t\t\t,q1.days,q2.NightFlag,q2.aovertime\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t,q2.day1\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t,q2.day2\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t,q2.phone\r\n\t\t\t\t\t\t\t\t\t\t\t\t\r\n\t\t\t\t\t\t\tfrom (\r\n\t\t\t\t\t\t\tSELECT DISTINCT(EMPID) as empid,'MONDAY' as days,1 dayindex\r\n\t\t\t\t\t\t\tfrom attendancemaster,tuser\r\n\t\t\t\t\t\t\twhere tuser.passivetype='act' and tuser.id=attendancemaster.employeeid\r\n\t\t\t\t\t\t\tand adate >= date_format(curdate() - INTERVAL DAYOFWEEK(curdate())+5 DAY,'%Y%m%d')\r\n\t\t\t\t\t\t\tAND adate <= date_format(curdate() - INTERVAL DAYOFWEEK(curdate())-1 DAY,'%Y%m%d')\r\n\t\t\t\t\t\t\tunion\r\n\t\t\t\t\t\t\tSELECT DISTINCT(EMPID) as empid,'TUESDAY' as days,2\r\n\t\t\t\t\t\t\tfrom attendancemaster,tuser\r\n\t\t\t\t\t\t\twhere tuser.passivetype='act' and  tuser.id=attendancemaster.employeeid\r\n\t\t\t\t\t\t\tand adate >= date_format(curdate() - INTERVAL DAYOFWEEK(curdate())+5 DAY,'%Y%m%d')\r\n\t\t\t\t\t\t\tAND adate <= date_format(curdate() - INTERVAL DAYOFWEEK(curdate())-1 DAY,'%Y%m%d')\r\n\t\t\t\t\t\t\tUNION\r\n\t\t\t\t\t\t\tSELECT DISTINCT(EMPID) as empid,'WEDNESDAY' as days,3\r\n\t\t\t\t\t\t\tfrom attendancemaster,tuser\r\n\t\t\t\t\t\t\twhere tuser.passivetype='act' and tuser.id=attendancemaster.employeeid\r\n\t\t\t\t\t\t\tand adate >= date_format(curdate() - INTERVAL DAYOFWEEK(curdate())+5 DAY,'%Y%m%d')\r\n\t\t\t\t\t\t\tAND adate <= date_format(curdate() - INTERVAL DAYOFWEEK(curdate())-1 DAY,'%Y%m%d')\r\n\t\t\t\t\t\t\tunion\r\n\t\t\t\t\t\t\tSELECT DISTINCT(EMPID) as empid,'THURSDAY' as days,4\r\n\t\t\t\t\t\t\tfrom attendancemaster,tuser\r\n\t\t\t\t\t\t\twhere tuser.passivetype='act' and tuser.id=attendancemaster.employeeid\r\n\t\t\t\t\t\t\tand adate >= date_format(curdate() - INTERVAL DAYOFWEEK(curdate())+5 DAY,'%Y%m%d')\r\n\t\t\t\t\t\t\tAND adate <= date_format(curdate() - INTERVAL DAYOFWEEK(curdate())-1 DAY,'%Y%m%d')\r\n\t\t\t\t\t\t\tUNION\r\n\t\t\t\t\t\t\tSELECT DISTINCT(EMPID) as empid,'FRIDAY' as days,5\r\n\t\t\t\t\t\t\tfrom attendancemaster,tuser\r\n\t\t\t\t\t\t\twhere tuser.passivetype='act' and tuser.id=attendancemaster.employeeid\r\n\t\t\t\t\t\t\tand adate >= date_format(curdate() - INTERVAL DAYOFWEEK(curdate())+5 DAY,'%Y%m%d')\r\n\t\t\t\t\t\t\tAND adate <= date_format(curdate() - INTERVAL DAYOFWEEK(curdate())-1 DAY,'%Y%m%d')\r\n\t\t\t\t\t\t\tunion\r\n\t\t\t\t\t\t\tSELECT DISTINCT(EMPID) as empid,'SATURDAY' as days,6\r\n\t\t\t\t\t\t\tfrom attendancemaster,tuser\r\n\t\t\t\t\t\t\twhere tuser.passivetype='act' and tuser.id=attendancemaster.employeeid\r\n\t\t\t\t\t\t\tand adate >= date_format(curdate() - INTERVAL DAYOFWEEK(curdate())+5 DAY,'%Y%m%d')\r\n\t\t\t\t\t\t\tAND adate <= date_format(curdate() - INTERVAL DAYOFWEEK(curdate())-1 DAY,'%Y%m%d')\r\n\t\t\t\t\t\t\tunion\r\n\t\t\t\t\t\t\tSELECT DISTINCT(EMPID) as empid,'SUNDAY' as days,7\r\n\t\t\t\t\t\t\tfrom attendancemaster,tuser\r\n\t\t\t\t\t\t\twhere tuser.passivetype='act' and tuser.id=attendancemaster.employeeid\r\n\t\t\t\t\t\t\tand adate >= date_format(curdate() - INTERVAL DAYOFWEEK(curdate())+5 DAY,'%Y%m%d')\r\n\t\t\t\t\t\t\tAND adate <= date_format(curdate() - INTERVAL DAYOFWEEK(curdate())-1 DAY,'%Y%m%d')\r\n\t\t\t\t\t\t\torder by empid,dayindex\r\n\t\t\t\t\t\t\t) q1\r\n\t\t\t\t\t\t\tleft join\r\n\t\t\t\t\t\t\t(SELECT EmpID,adate,date_format(adate,'%W') as days\r\n\t\t\t\t\t\t\t,NightFlag,aovertime,phone\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t,date_format(curdate() - INTERVAL DAYOFWEEK(curdate())+5 DAY,'%Y%m%d') day1\r\n\t\t\t\t\t\t\t\t\t\t\t\t\t,date_format(curdate() - INTERVAL DAYOFWEEK(curdate())-1 DAY,'%Y%m%d') day2\r\n\t\t\t\t\t\t\tfrom attendancemaster,tuser\r\n\t\t\t\t\t\t\twhere tuser.passivetype='act' and tuser.id=attendancemaster.employeeid\r\n\t\t\t\t\t\t\tand adate >= date_format(curdate() - INTERVAL DAYOFWEEK(curdate())+5 DAY,'%Y%m%d')\r\n\t\t\t\t\t\t\tAND adate <= date_format(curdate() - INTERVAL DAYOFWEEK(curdate())-1 DAY,'%Y%m%d')\r\n\t\t\t\t\t\t\t) q2 on q1.empid =q2.empid and q1.days = q2.days\r\n\t\t\t\t\t\t\torder by q1.empid,q1.dayindex ";
        $counter = 0;
        $last_id = "";
        $last_date = "";
        $result = mysql_query($query, $conn);
        if (0 < mysql_num_rows($result)) {
            $handle = fopen($file_name, "w");
            $data = "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%'>";
            fwrite($handle, $data);
            $data = "<tr><td><font face='Verdana' size='1'>&nbsp;</font></td> \r\n\t\t\t\t<td><font face='Verdana' size='1'>&nbsp;</font></td> \r\n\t\t\t\t<td><font face='Verdana' size='1'>&nbsp;</font></td> \r\n\t\t\t\t<td><font face='Verdana' size='1'>&nbsp;</font></td> \r\n\t\t\t\t<td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> \r\n\t\t\t\t<td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> \r\n\t\t\t\t</tr>";
            fwrite($handle, $data);
            $data = "<tr>\r\n\t\t\t\t<td><font face='Verdana' size='2'>EMP ID</font></td> \r\n\t\t\t\t<td><font face='Verdana' size='2'>DATE</font></td> \r\n\t\t\t\t<td><font face='Verdana' size='2'>ERP ID</font></td> \r\n\t\t\t\t<td><font face='Verdana' size='2'>Normal OT HOURS</font></td> \r\n\t\t\t\t<td><font face='Verdana' size='2'>Sun/Pub OT </font></td> \r\n\t\t\t\t<td><font face='Verdana' size='2'>Night Prov</font></td> \r\n\t\t\t\t<td><font face='Verdana' size='2'>Present days</font></td> \r\n\t\t\t\t<td><font face='Verdana' size='2'>DAY</font></td> \r\n\t\t\t\t</tr>";
            fwrite($handle, $data);
            while ($cur = mysql_fetch_row($result)) {
                $flagquery = "select empid,aovertime from attendancemaster,tuser where empid=" . $cur[0] . " and attendancemaster.empid=tuser.id and adate=" . $cur[1] . " and tuser.passivetype='act' and adate in (select otdate from otdate)";
                $result0 = mysql_query($flagquery, $conn);
                $cur0 = mysql_fetch_row($result0);
                $NightFlag = $cur[3];
                $day = $cur[2];
                if (strtoupper($day) == "SUNDAY" || $cur0[0] != "") {
                    $normalot = 0;
                    $SunPubOT = $cur0[1] / 3600;
                } else {
                    $normalot = number_format($cur[4], 2);
                    $SunPubOT = 0;
                }
                $erpidquery = "Select phone from tuser where id=" . $cur[0] . "";
                $result1 = mysql_query($erpidquery, $conn);
                $cur1 = mysql_fetch_row($result1);
                $normalotquery = "SELECT empid,day,aovertime from attendancemaster where adate =" . $cur[1] . "  and empid=" . $cur[0] . "";
                $result2 = mysql_query($normalotquery, $conn);
                $cur2 = mysql_fetch_row($result2);
                if ($cur[3] == "") {
                    $cur[3] = "&nbsp;";
                }
                $data = "<tr><td><font face='Verdana' size='1'> " . addZero($cur[0], $main_result[7]) . "</font></td> \r\n\t\t\t\t\t\t<td><font face='Verdana' size='1'>  " . displayDate($cur[1]) . "</font></td> <td>";
                if ($cur[7] == "") {
                    $data = $data . "<font face='Verdana' size='1'>" . $cur1[0] . "</font></td>";
                } else {
                    $data = $data . "<font face='Verdana' size='1'>" . $cur[7] . "</font></td>";
                }
                if ($normalot == "") {
                    $normalot = 0;
                }
                $normalot = $normalot / 3600;
                $normalot = number_format($normalot, 2);
                $data = $data . "<td><font face='Verdana' size='1'>" . $normalot . "</font></td>\r\n\t\t\t\t\t\r\n\r\n\t\t\t\t\t\t\r\n\t\t\t\t\t\t<td><font face='Verdana' size='1'>" . $SunPubOT . "</font></td> ";
                if ($NightFlag == 1) {
                    $data = $data . "<td><font face='Verdana' size='1'>8</font></td> ";
                } else {
                    $data = $data . "<td><font face='Verdana' size='1'>0</font></td> ";
                }
                if ($cur2[0] == "") {
                    $data = $data . "<td><font face='Verdana' size='1'>A</font></td>";
                } else {
                    $data = $data . "<td><font face='Verdana' size='1'>P</font></td>";
                }
                $data = $data . "<td><font face='Verdana' size='1'>" . $cur[2] . "</font></td> \r\n\t\t\t\t\t\t \r\n\t\t\t\t\t\t</tr>";
                fwrite($handle, $data);
                $last_id = $cur[0];
                $last_date = $cur[5];
                $counter++;
            }
            $data = "</table>";
            $mail_query = "SELECT MailerText FROM MailerText WHERE MailerType = 'Attendance'";
            $mail_result = selectData($conn, $mail_query);
            fwrite($handle, $data);
            fclose($handle);
            if (sendMail("Daily Attendance Mailer: Total Record(s) - " . $counter, str_replace("\r", "<br>", $mail_result[0]), $mail_result[0], $file_name, $main_result[3], "Virdi Admin", $user_cur[1], $user_cur[0], "", "", "", "")) {
                $count++;
            }
        }
    }
    if (0 < $count) {
        $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('Sent Daily Attendance Mailer to " . $count . " Users', " . insertToday() . ", '" . getNow() . "')";
        updateData($conn, $query, true);
    }
}
print "Attendance Mailer Process Executed Successfully";

?>