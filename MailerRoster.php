<?php
ob_start("ob_gzhandler");
error_reporting(E_ALL);
include "SendMail.php";
$dept_query = "";
$div_query = "";
$file_name = "";
$count = 0;
$lstEmployeeSeparator = "Yes";
$lstAbsent = "Yes";
$lstAbsentShift = "Yes";
$lstImproperClocking = "Yes";
$prints = "yes";
$lstRemark = "Yes";
$conn = openConnection();
$jconn = openIConnection();
//var_dump($argv);
$idf_ = $argv[1];
$txtFrom = displayDate(getLastDay(insertToday(), 1));
$txtTo = displayDate(getLastDay(insertToday(), 1));
if ($idf_ == "Week") {
    $txtFrom = displayDate(getLastDay(insertToday(), 8));
} else {
    if ($idf_ == "Month") {
        $txtFrom = displayDate(getLastDay(insertToday(), 32));
    }
}
$query = "SELECT IDColumnName, MACAddress, SMTPServer, SMTPFrom, SMTPUsername, SMTPPassword, NightShiftMaxOutTime, ExitTerminal, EX3, EmployeeCodeLength FROM OtherSettingMaster";
$major_result = selectData($conn, $query);
$nightShiftMaxOutTime = $major_result[6];
$weirdTimeDisplay = false;
if (getWeirdClient(encryptDecrypt($major_result[1]))) {
    $weirdTimeDisplay = true;
}
if (checkMAC($conn) == true && noTASoftware("", $major_result[1]) == false && 2 < strlen($major_result[2]) && 2 < strlen($major_result[3])) {
    $query = "SELECT Username, Usermail, Userstatus FROM UserMaster WHERE LENGTH(Usermail) > 5 AND Userlevel LIKE '%18D%' AND Username NOT LIKE 'virdi'";
    $user_result = mysqli_query($conn, $query);
    while ($user_cur = mysqli_fetch_row($user_result)) {
        $file_name = "mailer\\Roster-Mailer-" . $user_cur[0] . "-" . insertToday() . "" . getNow() . ".xls";
        $dept_div_query = mailerDeptDiv($conn, $iconn, $jconn, $user_cur[0], $user_cur[2]);
        $handle = fopen($file_name, "w");
        $query = "SELECT RosterColumns FROM OtherSettingMaster";
        $result = selectData($conn, $query);
        $txtRosterColumns = $result[0];
        $column_count_1 = 0;
        $column_count_2 = 0;
        if ($prints != "yes") {
            $data = "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800'>";
            fwrite($handle, $data);
        } else {
            $data = "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%'>";
            fwrite($handle, $data);
        }
        $data = "<tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td>";
        fwrite($handle, $data);
        if (insertToday() < 20150231) {
            $data = "<td><font face='Verdana' size='2'>TRML</font></td>";
            fwrite($handle, $data);
        }
        if (strpos($txtRosterColumns, "chkIDColumn") !== false) {
            $data = "<td><font face='Verdana' size='2'>" . $major_result[0] . "</font></td>";
            fwrite($handle, $data);
            $column_count_1++;
        }
        if (strpos($txtRosterColumns, "chkDept") !== false) {
            $data = "<td><font face='Verdana' size='2'>Dept</font></td>";
            fwrite($handle, $data);
            $column_count_1++;
        }
        if (strpos($txtRosterColumns, "chkDiv") !== false) {
            $data = "<td><font face='Verdana' size='2'>Div/Desg</font></td>";
            fwrite($handle, $data);
            $column_count_1++;
        }
        if (strpos($txtRosterColumns, "chkRmk") !== false) {
            $data = "<td><font face='Verdana' size='2'>Rmk</font></td>";
            fwrite($handle, $data);
            $column_count_1++;
        }
        if (strpos($txtRosterColumns, "chkShift") !== false) {
            $data = "<td><font face='Verdana' size='2'>Shift</font></td>";
            fwrite($handle, $data);
            $column_count_1++;
        }
        if (strpos($txtRosterColumns, "chkOT1") !== false) {
            $data = "<td><font face='Verdana' size='2'>OT 1</font></td>";
            fwrite($handle, $data);
            $column_count_1++;
        }
        if (strpos($txtRosterColumns, "chkOT2") !== false) {
            $data = "<td><font face='Verdana' size='2'>OT 2</font></td>";
            fwrite($handle, $data);
            $column_count_1++;
        }
        $data = "<td><font face='Verdana' size='2'>Date</font></td> <td><font face='Verdana' size='2'>Day</font></td>";
        fwrite($handle, $data);
        if (strpos($txtRosterColumns, "chkFlag") !== false) {
            $data = "<td><font face='Verdana' size='2'>Flag</font></td>";
            fwrite($handle, $data);
            $column_count_2++;
        }
        if (strpos($txtRosterColumns, "chkEntry") !== false) {
            $data = "<td><font face='Verdana' size='2'>Entry</font></td>";
            fwrite($handle, $data);
            $column_count_2++;
        }
        if (strpos($txtRosterColumns, "chkStart") !== false) {
            $data = "<td><font face='Verdana' size='2'><b>Start</b></font></td>";
            fwrite($handle, $data);
            $column_count_2++;
        }
        if (strpos($txtRosterColumns, "chkBreakOut") !== false) {
            $data = "<td><font face='Verdana' size='2'>BreakOut</font></td>";
            fwrite($handle, $data);
            $column_count_2++;
        }
        if (strpos($txtRosterColumns, "chkBreakIn") !== false) {
            $data = "<td><font face='Verdana' size='2'>BreakIn</font></td>";
            fwrite($handle, $data);
            $column_count_2++;
        }
        if (strpos($txtRosterColumns, "chkClose") !== false) {
            $data = "<td><font face='Verdana' size='2'><b>Close</b></font></td>";
            fwrite($handle, $data);
            $column_count_2++;
        }
        if (strpos($txtRosterColumns, "chkExit") !== false) {
            $data = "<td><font face='Verdana' size='2'>Exit</font></td>";
            fwrite($handle, $data);
            $column_count_2++;
        }
        if (strpos($txtRosterColumns, "chkEarlyIn") !== false) {
            $data = "<td><font face='Verdana' size='2'>Early In <br>(Min)</font></td>";
            fwrite($handle, $data);
            $column_count_2++;
        }
        if (strpos($txtRosterColumns, "chkLateIn") !== false) {
            $data = "<td><font face='Verdana' size='2'>Late In <br>(Min)</font></td>";
            fwrite($handle, $data);
            $column_count_2++;
        }
        if (strpos($txtRosterColumns, "chkLessBreak") !== false) {
            $data = "<td><font face='Verdana' size='2'>Less Break <br>(Min)</font></td>";
            fwrite($handle, $data);
            $column_count_2++;
        }
        if (strpos($txtRosterColumns, "chkMoreBreak") !== false) {
            $data = "<td><font face='Verdana' size='2'>More Break <br>(Min)</font></td>";
            fwrite($handle, $data);
            $column_count_2++;
        }
        if (strpos($txtRosterColumns, "chkEarlyOut") !== false) {
            $data = "<td><font face='Verdana' size='2'>Early Out <br>(Min)</font></td>";
            fwrite($handle, $data);
            $column_count_2++;
        }
        if (strpos($txtRosterColumns, "chkLateOut") !== false) {
            $data = "<td><font face='Verdana' size='2'>Late Out <br>(Min)</font></td>";
            fwrite($handle, $data);
            $column_count_2++;
        }
        if (strpos($txtRosterColumns, "chkGrace") !== false) {
            $data = "<td><font face='Verdana' size='2'>Grace <br>(Min)</font></td>";
            fwrite($handle, $data);
            $column_count_2++;
        }
        if (strpos($txtRosterColumns, "chkNormal") !== false) {
            $data = "<td><font face='Verdana' size='2'>Normal <br>(Hrs)</font></td>";
            fwrite($handle, $data);
            $column_count_2++;
        }
        if (strpos($txtRosterColumns, "chkOT") !== false) {
            $data = "<td><font face='Verdana' size='2'>OT <br>(Hrs)</font></td>";
            fwrite($handle, $data);
            $column_count_2++;
        }
        if (strpos($txtRosterColumns, "chkAppOT") !== false) {
            $data = "<td><font face='Verdana' size='2'>App OT <br>(Hrs)</font></td>";
            fwrite($handle, $data);
            $column_count_2++;
        }
        if ($lstRemark != "") {
            $data = "<td width=50><font face='Verdana' size='2'>Remarks</font></td>";
            fwrite($handle, $data);
            $column_count_2++;
        }
        $count = 0;
        $counter = 0;
        $sub_count = 0;
        $last_id = "";
        $last_name = "";
        $last_dept = "";
        $last_div = "";
        $last_idno = "";
        $last_rmk = "";
        $last_date = "";
        $last_shift = "";
        $font = "Black";
        $bgcolor = "#FFFFFF";
        $nextDate = insertDate($txtFrom);
        $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tuser.idno, tuser.remark FROM tuser, tgroup WHERE tuser.id > 0 AND tuser.group_id = tgroup.id " . $dept_div_query . " AND tuser.PassiveType = 'ACT' ";
        $main_result = mysqli_query($conn, $query);
        while ($main_cur = mysqli_fetch_row($main_result)) {
            $sub_count = 0;
            if ($txtFrom != $txtTo && $lstEmployeeSeparator == "Yes") {
                $data = "<tr><td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td>";
                fwrite($handle, $data);
                if (insertToday() < 20150231) {
                    $data = "<td><font face='Verdana' size='2'>&nbsp;</font></td>";
                    fwrite($handle, $data);
                }
                for ($j = 0; $j < $column_count_1; $j++) {
                    $data = "<td><font face='Verdana' size='1'>&nbsp;</font></td>";
                    fwrite($handle, $data);
                }
                $this_day = getDate(strtotime(substr($i, 6, 2) . "-" . substr($i, 4, 2) . "-" . substr($i, 0, 4)));
                $data = "<td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td>";
                fwrite($handle, $data);
                for ($j = 0; $j < $column_count_2; $j++) {
                    $data = "<td><font face='Verdana' size='1'>&nbsp;</font></td>";
                    fwrite($handle, $data);
                }
                $data = "</tr>";
            }
            $query = "SELECT tgroup.name, DayMaster.TDate, DayMaster.Entry, DayMaster.Start, DayMaster.BreakOut, DayMaster.BreakIn, DayMaster.Close, DayMaster.Exit, DayMaster.Flag, AttendanceMaster.Day, AttendanceMaster.EarlyIn, AttendanceMaster.LateIn, AttendanceMaster.Break, AttendanceMaster.LessBreak, AttendanceMaster.MoreBreak, AttendanceMaster.EarlyOut, AttendanceMaster.LateOut, AttendanceMaster.Normal, AttendanceMaster.Grace, AttendanceMaster.Overtime, AttendanceMaster.AOvertime, AttendanceMaster.OT1, AttendanceMaster.OT2, AttendanceMaster.LateIn_flag, AttendanceMaster.EarlyOut_flag, AttendanceMaster.MoreBreak_flag FROM tgroup, DayMaster, AttendanceMaster WHERE DayMaster.e_id > 0 AND DayMaster.group_id = tgroup.id AND DayMaster.e_id = AttendanceMaster.EmployeeID AND DayMaster.TDate = AttendanceMaster.ADate AND DayMaster.e_id = " . $main_cur[0] . " AND DayMaster.TDate >= " . insertDate($txtFrom) . " AND DayMaster.TDate <= " . insertDate($txtTo);
            $query = $query . " ORDER BY DayMaster.TDate";
            $nextDate = insertDate($txtFrom);
            $result = mysqli_query($conn, $query);
            while ($cur = mysqli_fetch_row($result)) {
                $sub_count++;
                if ($nextDate < $cur[1]) {
                    $shift = "&nbsp;";
                    if ($lstAbsentShift == "Yes") {
                        $shift = $cur[0];
                    }
                    $count = displayAlterTime($conn, $nextDate, $cur[1] - 1, $main_cur[0], $main_cur[1], $main_cur[2], $main_cur[3], $main_cur[5], $main_cur[6], $column_count_1, $column_count_2, $txtRosterColumns, $prints, $count, $lstImproperClocking, $lstAbsent, $shift, $major_result, $handle, $nightShiftMaxOutTime);
                }
                if ($cur[8] != "" && strpos($txtRosterColumns, "chkFlag") !== false) {
                    $font = $cur[8];
                    if ($font == "Yellow") {
                        $bgcolor = "Brown";
                    } else {
                        $bgcolor = "#FFFFFF";
                    }
                } else {
                    $cur[8] = "&nbsp;";
                    $font = "Black";
                    $bgcolor = "#FFFFFF";
                }
                if ($main_cur[3] == "") {
                    $main_cur[3] = "&nbsp;";
                }
                if ($main_cur[5] == "") {
                    $main_cur[5] = "&nbsp;";
                }
                if ($main_cur[6] == "") {
                    $main_cur[6] = "&nbsp;";
                }
                $data = "<tr><td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1' color='" . $font . "'>" . addZero($main_cur[0], $major_result[9]) . "</font></td> <td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1' color='" . $font . "'>" . $main_cur[1] . "</font></td>";
                fwrite($handle, $data);
                if (insertToday() < 20150231) {
                    $terminal_query = "SELECT g_id, tgate.name FROM tenter, tgate WHERE tenter.e_id = '" . $main_cur[0] . "' AND tenter.e_date = '" . $cur[1] . "' AND tenter.g_id = tgate.id ";
                    $terminal_result = selectData($conn, $terminal_query);
                    $data = "<td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1' color='" . $font . "'>" . $terminal_result[0] . " - " . $terminal_result[1] . "</font></td>";
                    fwrite($handle, $data);
                }
                if (strpos($txtRosterColumns, "chkIDColumn") !== false) {
                    $data = "<td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1' color='" . $font . "'>" . $main_cur[5] . "</font></td>";
                    fwrite($handle, $data);
                }
                if (strpos($txtRosterColumns, "chkDept") !== false) {
                    $data = "<td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1' color='" . $font . "'>" . $main_cur[2] . "</font></td>";
                    fwrite($handle, $data);
                }
                if (strpos($txtRosterColumns, "chkDiv") !== false) {
                    $data = "<td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1' color='" . $font . "'>" . $main_cur[3] . "</font></td>";
                    fwrite($handle, $data);
                }
                if (strpos($txtRosterColumns, "chkRmk") !== false) {
                    $data = "<td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1' color='" . $font . "'>" . $main_cur[6] . "</font></td>";
                    fwrite($handle, $data);
                }
                if (strpos($txtRosterColumns, "chkShift") !== false) {
                    $data = "<td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1' color='" . $font . "'>" . $cur[0] . "</font></td>";
                    fwrite($handle, $data);
                }
                if (strpos($txtRosterColumns, "chkOT1") !== false) {
                    $data = "<td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1' color='" . $font . "'>" . $cur[21] . "</font></td>";
                    fwrite($handle, $data);
                }
                if (strpos($txtRosterColumns, "chkOT2") !== false) {
                    $data = "<td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1' color='" . $font . "'>" . $cur[22] . "</font></td>";
                    fwrite($handle, $data);
                }
                $data = "<td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1' color='" . $font . "'>" . displayDate($cur[1]) . "</font></td> <td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1' color='" . $font . "'>" . $cur[9] . "</font></td>";
                fwrite($handle, $data);
                if (strpos($txtRosterColumns, "chkFlag") !== false) {
                    if (insertToday() < 20150231 && $cur[8] != "Black" && $cur[8] != "Proxy") {
                        $flag_title_query = "SELECT Title FROM FlagTitle WHERE Flag = '" . $cur[8] . "'";
                        $flag_title_result = selectData($conn, $flag_title_query);
                        $data = "<td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1' color='" . $font . "'>" . $flag_title_result[0] . "</font></td>";
                        fwrite($handle, $data);
                    } else {
                        $data = "<td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1' color='" . $font . "'>" . $cur[8] . "</font></td>";
                        fwrite($handle, $data);
                    }
                }
                if (strpos($txtRosterColumns, "chkEntry") !== false) {
                    if ($cur[2] != $cur[3]) {
                        $data = "<td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1' color='" . $font . "'>" . displayVirdiTime($cur[2]) . "</font></td>";
                        fwrite($handle, $data);
                    } else {
                        $data = "<td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></td>";
                        fwrite($handle, $data);
                    }
                }
                if (strpos($txtRosterColumns, "chkStart") !== false) {
                    $data = "<td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1' color='" . $font . "'><b>" . displayVirdiTime($cur[3]) . "</b></font></td>";
                    fwrite($handle, $data);
                }
                if (strpos($txtRosterColumns, "chkBreakOut") !== false) {
                    if ($cur[3] != $cur[4]) {
                        $data = "<td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1' color='" . $font . "'>" . displayVirdiTime($cur[4]) . "</font></td>";
                        fwrite($handle, $data);
                    } else {
                        $data = "<td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></td>";
                        fwrite($handle, $data);
                    }
                }
                if (strpos($txtRosterColumns, "chkBreakIn") !== false) {
                    if ($cur[3] != $cur[5]) {
                        $data = "<td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1' color='" . $font . "'>" . displayVirdiTime($cur[5]) . "</font></td>";
                        fwrite($handle, $data);
                    } else {
                        $data = "<td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></td>";
                        fwrite($handle, $data);
                    }
                }
                if (strpos($txtRosterColumns, "chkClose") !== false) {
                    $data = "<td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1' color='" . $font . "'><b>" . displayVirdiTime($cur[6]) . "</b></font></td>";
                    fwrite($handle, $data);
                }
                if (strpos($txtRosterColumns, "chkExit") !== false) {
                    if ($cur[6] != $cur[7]) {
                        $data = "<td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1' color='" . $font . "'>" . displayVirdiTime($cur[7]) . "</font></td>";
                    } else {
                        $data = "<td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></td>";
                    }
                    fwrite($handle, $data);
                }
                if (strpos($txtRosterColumns, "chkEarlyIn") !== false) {
                    $data = "<td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[10] / 60, 2) . "</font>";
                    fwrite($handle, $data);
                }
                if (strpos($txtRosterColumns, "chkLateIn") !== false) {
                    if ($cur[23] == 0) {
                        if ($weirdTimeDisplay) {
                            $data = "<td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1' color='" . $font . "'>" . getWeirdTime($cur[11]) . "</font>";
                        } else {
                            $data = "<td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[11] / 60, 2) . "</font>";
                        }
                    } else {
                        if ($weirdTimeDisplay) {
                            $data = "<td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1' color='" . $font . "'><strike>" . getWeirdTime($cur[11]) . "</strike></font>";
                        } else {
                            $data = "<td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1' color='" . $font . "'><strike>" . round($cur[11] / 60, 2) . "</strike></font>";
                        }
                    }
                    fwrite($handle, $data);
                }
                if (strpos($txtRosterColumns, "chkLessBreak") !== false) {
                    $data = "<td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[13] / 60, 2) . "</font>";
                    fwrite($handle, $data);
                }
                if (strpos($txtRosterColumns, "chkMoreBreak") !== false) {
                    if ($cur[25] == 0) {
                        $data = "<td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[14] / 60, 2) . "</font>";
                    } else {
                        $data = "<td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1' color='" . $font . "'><strike>" . round($cur[14] / 60, 2) . "</strike></font>";
                    }
                    fwrite($handle, $data);
                }
                if (strpos($txtRosterColumns, "chkEarlyOut") !== false) {
                    if ($cur[24] == 0) {
                        if ($weirdTimeDisplay) {
                            $data = "<td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1' color='" . $font . "'>" . getWeirdTime($cur[15]) . "</font>";
                        } else {
                            $data = "<td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[15] / 60, 2) . "</font>";
                        }
                    } else {
                        if ($weirdTimeDisplay) {
                            $data = "<td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1' color='" . $font . "'><strike>" . getWeirdTime($cur[15]) . "</strike></font>";
                        } else {
                            $data = "<td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1' color='" . $font . "'><strike>" . round($cur[15] / 60, 2) . "</strike></font>";
                        }
                    }
                    fwrite($handle, $data);
                }
                if (strpos($txtRosterColumns, "chkLateOut") !== false) {
                    if ($weirdTimeDisplay) {
                        $data = "<td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1' color='" . $font . "'>" . getWeirdTime($cur[16]) . "</font>";
                    } else {
                        $data = "<td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[16] / 60, 2) . "</font>";
                    }
                    fwrite($handle, $data);
                }
                if (strpos($txtRosterColumns, "chkGrace") !== false) {
                    $data = "<td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[18] / 60, 2) . "</font>";
                    fwrite($handle, $data);
                }
                if (strpos($txtRosterColumns, "chkNormal") !== false) {
                    $data = "<td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[17] / 3600, 2) . "</font>";
                    fwrite($handle, $data);
                }
                if (strpos($txtRosterColumns, "chkOT") !== false) {
                    if ($weirdTimeDisplay) {
                        $data = "<td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1' color='" . $font . "'>" . getWeirdTime($cur[19]) . "</font>";
                    } else {
                        $data = "<td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[19] / 3600, 2) . "</font>";
                    }
                    fwrite($handle, $data);
                }
                if (strpos($txtRosterColumns, "chkAppOT") !== false) {
                    if ($weirdTimeDisplay) {
                        $data = "<td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1' color='" . $font . "'>" . getWeirdTime($cur[20]) . "</font>";
                    } else {
                        $data = "<td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[20] / 3600, 2) . "</font>";
                    }
                    fwrite($handle, $data);
                }
                if ($lstRemark != "") {
                    $data = "<td width=50><font face='Verdana' size='2'>&nbsp;</font></td>";
                    fwrite($handle, $data);
                }
                $data = "</tr>";
                $count++;
                $last_id = $main_cur[0];
                $last_name = $main_cur[1];
                $last_dept = $main_cur[2];
                $last_div = $main_cur[3];
                $last_idno = $main_cur[5];
                $last_rmk = $main_cur[6];
                $last_date = $cur[1];
                $last_shift = $cur[0];
                $nextDate = getNextDay($cur[1], 1);
            }
            $shift = "&nbsp;";
            if ($lstAbsentShift == "Yes") {
                $shift = $last_shift;
            }
            if ($sub_count == 0) {
                $count = displayAlterTime($conn, insertDate($txtFrom), insertDate($txtTo), $main_cur[0], $main_cur[1], $main_cur[2], $main_cur[3], $main_cur[5], $main_cur[6], $column_count_1, $column_count_2, $txtRosterColumns, $prints, $count, $lstImproperClocking, $lstAbsent, $shift, $major_result, $handle, $nightShiftMaxOutTime);
            } else {
                if ($last_date < insertDate($txtTo)) {
                    $count = displayAlterTime($conn, $nextDate, insertDate($txtTo), $main_cur[0], $main_cur[1], $main_cur[2], $main_cur[3], $main_cur[5], $main_cur[6], $column_count_1, $column_count_2, $txtRosterColumns, $prints, $count, $lstImproperClocking, $lstAbsent, $shift, $major_result, $handle, $nightShiftMaxOutTime);
                }
            }
        }
        $data = "</table>";
        fwrite($handle, $data);
        fclose($handle);
        $mail_query = "SELECT MailerText FROM MailerText WHERE MailerType = 'Roster'";
        $mail_result = selectData($conn, $mail_query);
        $mail_text = $mail_result[0];
        $u_email = $user_cur[1];
        $u_name = $user_cur[0];
        if (sendMail("Roster Mailer: Total Record(s) - " . $count, str_replace("\r", "<br>", $mail_text), $mail_text, $file_name, "", "Virdi Admin", $u_email, $u_name, "", "", "", "")) {
            $counter++;
        } else {
            echo "\n\rUnable to Send Mail";
        }
        if (getRegister($main_result[1], 7) != "73") {
            unlink($file_name);
        }
    }
    if (0 < $counter) {
        $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('Sent Roster Mailer to " . $counter . " Users', " . insertToday() . ", '" . getNow() . "')";
        updateIData($jconn, $query, true);
        $data = "Roster Mailer Process Executed Successfully";
    }
}
function displayAlterTime($conn, $from, $to, $id, $name, $dept, $div, $idno, $rmk, $column_count_1, $column_count_2, $txtRosterColumns, $prints, $count, $lstImproperClocking, $lstAbsent, $shift, $major_result, $handle, $nightShiftMaxOutTime)
{
    if ($id != "") {
        for ($i = $from; $i <= $to; $i++) {
            if (checkdate(substr($i, 4, 2), substr($i, 6, 2), substr($i, 0, 4))) {
                $start = "";
                $close = "";
                if ($lstImproperClocking == "Yes") {
                    $alter_query = "SELECT tenter.e_time, tgroup.Start, tgroup.Close, tgroup.NightFlag, tgroup.WorkMin, tgroup.name FROM tenter, tuser, tgate, tgroup WHERE tenter.e_id = tuser.id AND tenter.g_id = tgate.id AND tenter.e_group = tgroup.id AND tgroup.ShiftTypeID = 1 AND tuser.id = " . $id . " AND tgate.exit = 0 AND tenter.p_flag = 0 AND tenter.e_date = " . $i;
                    $alter_result = selectData($conn, $alter_query);
                    if ($alter_result[0] != "") {
                        $shift = $alter_result[5];
                        if ($alter_result[3] == 0) {
                            $halfTime = getLateTime($i, $alter_result[1], $alter_result[4] / 2);
                            if ($halfTime < $alter_result[0]) {
                                $close = $alter_result[0];
                            } else {
                                $start = $alter_result[0];
                            }
                        } else {
                            if ($nightShiftMaxOutTime < $alter_result[0]) {
                                $start = $alter_result[0];
                            } else {
                                $close = $alter_result[0];
                            }
                        }
                    }
                }
                if ($start == "") {
                    $start = "&nbsp;";
                } else {
                    if ($prints == "yes") {
                        $start = displayVirdiTime($start);
                    } else {
                        $start = "<font face='Verdana' color='#000000'>" . displayVirdiTime($start) . "</font>";
                    }
                }
                if ($close == "") {
                    $close = "&nbsp;";
                } else {
                    if ($prints == "yes") {
                        $close = displayVirdiTime($close);
                    } else {
                        $close = "<font face='Verdana' color='#000000'>" . displayVirdiTime($close) . "</font>";
                    }
                }
                if (!($lstAbsent == "No" && $start == "&nbsp;" && $close == "&nbsp;")) {
                    $data = "<tr><td><font face='Verdana' size='1'>" . addZero($id, $major_result[0]) . "</font></td> <td><font face='Verdana' size='1'>" . $name . "</font></td>";
                    fwrite($handle, $data);
                    if (insertToday() < 20150231) {
                        $data = "<td><font face='Verdana' size='1'>&nbsp;</font></td>";
                        fwrite($handle, $data);
                    }
                    $column_count_1_minus = 0;
                    if (strpos($txtRosterColumns, "chkIDColumn") !== false) {
                        $data = "<td><font face='Verdana' size='1'>" . $idno . "</font></td>";
                        fwrite($handle, $data);
                        $column_count_1_minus++;
                    }
                    if (strpos($txtRosterColumns, "chkDept") !== false) {
                        $data = "<td><font face='Verdana' size='1'>" . $dept . "</font></td>";
                        fwrite($handle, $data);
                        $column_count_1_minus++;
                    }
                    if (strpos($txtRosterColumns, "chkDiv") !== false) {
                        $data = "<td><font face='Verdana' size='1'>" . $div . "</font></td>";
                        fwrite($handle, $data);
                        $column_count_1_minus++;
                    }
                    if (strpos($txtRosterColumns, "chkRmk") !== false) {
                        $data = "<td><font face='Verdana' size='1'>" . $rmk . "</font></td>";
                        fwrite($handle, $data);
                        $column_count_1_minus++;
                    }
                    if (strpos($txtRosterColumns, "chkShift") !== false) {
                        $data = "<td><font face='Verdana' size='1'>" . $shift . "</font></td>";
                        fwrite($handle, $data);
                        $column_count_1_minus++;
                    }
                    for ($j = 0; $j < $column_count_1 - $column_count_1_minus; $j++) {
                        $data = "<td><font face='Verdana' size='1'>&nbsp;</font></td>";
                        fwrite($handle, $data);
                    }
                    $this_day = getDate(strtotime(substr($i, 6, 2) . "-" . substr($i, 4, 2) . "-" . substr($i, 0, 4)));
                    $data = "<td><font face='Verdana' size='1'>" . displayDate($i) . "</font></td> <td><font face='Verdana' size='1'>" . $this_day["weekday"] . "</font></td>";
                    fwrite($handle, $data);
                    $column_count_2_minus = 0;
                    if (strpos($txtRosterColumns, "chkFlag") !== false) {
                        $data = "<td><font face='Verdana' size='2'>&nbsp;</font></td>";
                        fwrite($handle, $data);
                        $column_count_2_minus++;
                    }
                    if (strpos($txtRosterColumns, "chkEntry") !== false) {
                        $data = "<td><font face='Verdana' size='2'>&nbsp;</font></td>";
                        fwrite($handle, $data);
                        $column_count_2_minus++;
                    }
                    if (strpos($txtRosterColumns, "chkStart") !== false) {
                        $data = "<td><font face='Verdana' size='1'><b>" . $start . "</b></font></td>";
                        fwrite($handle, $data);
                        $column_count_2_minus++;
                    }
                    if (strpos($txtRosterColumns, "chkBreakOut") !== false) {
                        $data = "<td><font face='Verdana' size='2'>&nbsp;</font></td>";
                        fwrite($handle, $data);
                        $column_count_2_minus++;
                    }
                    if (strpos($txtRosterColumns, "chkBreakIn") !== false) {
                        $data = "<td><font face='Verdana' size='2'>&nbsp;</font></td>";
                        fwrite($handle, $data);
                        $column_count_2_minus++;
                    }
                    if (strpos($txtRosterColumns, "chkClose") !== false) {
                        $data = "<td><font face='Verdana' size='1'><b>" . $close . "</b></font></td>";
                        fwrite($handle, $data);
                        $column_count_2_minus++;
                    }
                    for ($j = 0; $j < $column_count_2 - $column_count_2_minus; $j++) {
                        $data = "<td><font face='Verdana' size='1'>&nbsp;</font></td>";
                        fwrite($handle, $data);
                    }
                    $data = "</tr>";
                    fwrite($handle, $data);
                    $count++;
                }
            }
        }
    }
    return $count;
}

?>