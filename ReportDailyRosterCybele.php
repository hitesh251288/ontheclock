<?php


ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "21";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
$nightShiftMaxOutTime = $_SESSION[$session_variable . "NightShiftMaxOutTime"] . "00";
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=ReportDailyRostercybele.php&message=Session Expired or Security Policy Violated");
}
$dayNow = "";
$conn = openConnection();
$iconn = openIConnection();
$act = $_GET["act"];
if ($act == "") {
    $act = $_POST["act"];
}
$prints = $_GET["prints"];
$excel = $_GET["excel"];
$subReport = $_GET["subReport"];
$message = $_GET["message"];
if ($message == "") {
    $message = "Daily Roster (Daily Routine) <br>(Not Recommended for long Date Range)";
}
$lstShift = $_POST["lstShift"];
$lstDepartment = $_POST["lstDepartment"];
$lstDivision = $_POST["lstDivision"];
$lstEmployeeID = $_GET["lstEmployeeID"];
$lstEmployeeIDFrom = $_POST["lstEmployeeIDFrom"];
$lstEmployeeIDTo = $_POST["lstEmployeeIDTo"];
if ($lstEmployeeID != "" && $lstEmployeeIDFrom == "" && $lstEmployeeIDTo == "") {
    $lstEmployeeIDFrom = $lstEmployeeID;
    $lstEmployeeIDTo = $lstEmployeeID;
}
$txtEmployeeCode = $_POST["txtEmployeeCode"];
$lstSort = $_POST["lstSort"];
$txtEmployee = $_POST["txtEmployee"];
$lstColourFlag = $_POST["lstColourFlag"];
if ($lstColourFlag == "") {
    $lstColourFlag = $_GET["lstColourFlag"];
}
$txtFrom = $_POST["txtFrom"];
if ($txtFrom == "") {
    $txtFrom = $_GET["txtFrom"];
}
$txtTo = $_POST["txtTo"];
if ($txtTo == "") {
    $txtTo = $_GET["txtTo"];
}
if ($txtFrom == "") {
    $txtFrom = displayDate(getLastDay(insertToday(), 1));
}
if ($txtTo == "") {
    $txtTo = displayDate(getLastDay(insertToday(), 1));
}
$txtRemark = $_POST["txtRemark"];
$txtPhone = $_POST["txtPhone"];
$lstRemark = $_POST["lstRemark"];
if ($lstRemark == "") {
    $lstRemark = "Yes";
}
$lstAbsent = $_POST["lstAbsent"];
if ($lstAbsent == "") {
    $lstAbsent = $_GET["lstAbsent"];
}
if ($lstAbsent == "") {
    $lstAbsent = "Yes";
}
$lstImproperClocking = $_POST["lstImproperClocking"];
if ($lstImproperClocking == "") {
    $lstImproperClocking = $_GET["lstImproperClocking"];
}
if ($lstImproperClocking == "") {
    $lstImproperClocking = "Yes";
}
$lstEmployeeSeparator = $_POST["lstEmployeeSeparator"];
if ($lstEmployeeSeparator == "") {
    $lstEmployeeSeparator = "No";
}
$lstAbsentShift = $_POST["lstAbsentShift"];
if ($lstAbsentShift == "") {
    $lstAbsentShift = "No";
}
$txtSNo = $_POST["txtSNo"];
$lstEmployeeStatus = $_GET["lstEmployeeStatus"];
if ($lstEmployeeStatus == "") {
    if (isset($_POST["lstEmployeeStatus"])) {
        $lstEmployeeStatus = $_POST["lstEmployeeStatus"];
    } else {
        $lstEmployeeStatus = "ACT";
    }
}
$lstIPEL = $_POST["lstIPEL"];
if ($lstIPEL == "") {
    $lstIPEL = "No";
}
$lstType = $_POST["lstType"];
$lstColourFlag = $_POST["lstColourFlag"];
if ($lstColourFlag == "") {
    $lstColourFlag = $_GET["lstColourFlag"];
}
print "<html><title>Daily Roster (Daily Routine)</title>";
if ($prints != "yes") {
    print "<body>";
} else {
    if ($excel != "yes") {
        print "<body onLoad='javascript:window.print()'>";
    } else {
        if ($subReport != "yes") {
            header("Content-type: application/x-msdownload");
            header("Content-Disposition: attachment; filename=ReportDailyRostercybele.xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            print "<body>";
        }
    }
}
print "<center>";
if ($excel != "yes") {
    displayHeader($prints, true, false);
}
if ($prints != "yes") {
    displayLinks(18, $userlevel);
}
if ($excel != "yes") {
    print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b></font></p>";
}
if ($prints != "yes") {
    print "<table width='800' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
    print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Select ONE or MORE options and click 'Search Record'</b></font></td></tr>";
} else {
    if ($excel != "yes") {
        print "<table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
        print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Selected Options</b></font></td></tr>";
    }
}
if ($excel != "yes") {
    print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='ReportDailyRostercybele.php'><input type='hidden' name='act' value='searchRecord'><tr>";
    $query = "SELECT id, name from tgroup WHERE id > 1 AND ShiftTypeID = 1 ORDER BY name";
    displayList("lstShift", "Current Shift: ", $lstShift, $prints, $conn, $query, "", "25%", "75%");
    print "</tr>";
    displaySearchFields($conn, $prints, $session_variable, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $lstEmployeeName, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"]);
    print "<tr>";
    print "<td colspan='2'><table width='100%' cellspacing='-1' cellpadding='-1' border='0'><tr>";
    displayTextbox("txtFrom", "Date From <font size='1'>(DD/MM/YYYY)</font>: ", $txtFrom, $prints, 12, "25%", "25%");
    displayColourFlag($conn, $lstColourFlag, "lstColourFlag", true, true);
    print "</tr>";
    print "<tr><td colspan='4' width='100%'><img src='img/cBar.gif' width='100%' height='3'></td></tr>";
    print "<tr>";
    displayTextbox("txtTo", "Date To <font size='1'>(DD/MM/YYYY)</font>: ", $txtTo, $prints, 12, "25%", "25%");
    print "<td align='right' width='25%'><font face='Verdana' size='2'>Work Type:</font></td><td width='25%'><select name='lstType'><option selected value='" . $lstType . "'>" . $lstType . "</option> <option value='Early In'>Early In</option> <option value='Late In'>Late In</option> <option value='Less Break'>Less Break</option> <option value='More Break'>More Break</option> <option value='Early Out'>Early Out</option> <option value='Late Out'>Late Out</option> <option value='Grace'>Grace</option> <option value='OT'>OT</option> <option value='Approved OT'>Approved OT</option> <option value=''>---</option></select></td>";
    print "</tr></table></td>";
    print "</tr>";
    if ($prints != "yes") {
        print "<tr>";
        print "<td colspan='2'><table width='100%' cellspacing='-1' cellpadding='-1' border='0'><tr>";
        print "<td align='right' width='25%'><font face='Verdana' size='2'>Print Remark Column:</font></td><td width='15%'><select name='lstRemark'><option selected value='" . $lstRemark . "'>" . $lstRemark . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select></td>";
        print "<td align='right' width='35%'><font face='Verdana' size='2'>Display Shift of Absent Employees:</font></td><td width='25%'><select name='lstAbsentShift'> <option selected value='" . $lstAbsentShift . "'>" . $lstAbsentShift . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select></td>";
        print "</tr></table></td>";
        print "</tr>";
        print "<tr>";
        print "<td colspan='2'><table width='100%' cellspacing='-1' cellpadding='-1' border='0'><tr>";
        print "<td align='right' width='25%'><font face='Verdana' size='2'>Display Improper Clocking:</font></td><td width='10%'><select name='lstImproperClocking'><option selected value='" . $lstImproperClocking . "'>" . $lstImproperClocking . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select></td>";
        print "<td align='right' width='40%'><font face='Verdana' size='2'>Display Employee Separator Row:</font></td><td width='25%'><select name='lstEmployeeSeparator'><option selected value='" . $lstEmployeeSeparator . "'>" . $lstEmployeeSeparator . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select></td>";
        print "</tr></table></td>";
        print "</tr>";
        print "<tr>";
        print "<td colspan='2'><table width='100%' cellspacing='-1' cellpadding='-1' border='0'><tr>";
        displayEmployeeStatus($conn, "lstEmployeeStatus", $lstEmployeeStatus, "25%", "35%");
        print "<td align='right' width='15%'><font face='Verdana' size='1'>Display Absent Days:</font></td><td width='25%'><select name='lstAbsent'><option selected value='" . $lstAbsent . "'>" . $lstAbsent . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select></td>";
        print "</tr></table></td>";
        print "</tr>";
        print "</tr>";
        print "<tr>";
        $array = array(array("tuser.id", "Employee Code"), array("tuser.name, tuser.id", "Employee Name - Code"), array("tuser.dept, tuser.id", "Dept - Code"), array("tuser.company, tuser.dept, tuser.id", "Div/Desg - Dept - Code"), array("tuser.company, tuser.dept, tuser.group_id, tuser.id", "Div - Dept - Current Shift - Code"), array("attendancemaster.adate", "Date Sort"));
        displaySort($array, $lstSort, 5);
        print "</tr>";
        print "<tr><td>&nbsp;</td><td><input name='btSearch' type='submit' value='Search Record'>&nbsp;&nbsp;<input type='button' value='Excel' onClick='checkPrint(1)'></td></tr>";
    }
    print "</table><br>";
}
if ($act == "searchRecord") {
    if ($lstColourFlag != "" || $lstType != "") {
        $lstAbsent = "No";
        $lstImproperClocking = "No";
    }
    $query = "SELECT RosterColumns FROM OtherSettingMaster1";
    $result = selectData($conn, $query);
    $txtRosterColumns = $result[0];
    if ($excel != "yes") {
        print "<p align='center'><font face='Verdana' size='1'><b>" . $_SESSION[$session_variable . "FlagReportText"] . "</b></font></p>";
    }
    $column_count_1 = 0;
    $column_count_2 = 0;
    if ($prints != "yes") {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800'>";
    } else {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%'>";
    }
    print "<tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>ERP ID</font></td>";
    if (strpos($txtRosterColumns, "chkIDColumn") !== false) {
        print "<td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td>";
        $column_count_1++;
    }
    print "<td><font face='Verdana' size='2'>Date</font></td>";
    $column_count_1++;
    print "<td><font face='Verdana' size='2'>Normal OT HOURS</font></td>";
    $column_count_1++;
    print "<td><font face='Verdana' size='2'>Sat OT </font></td>";
    $column_count_1++;
    print "<td><font face='Verdana' size='2'>Sun OT </font></td>";
    $column_count_1++;
    print "<td><font face='Verdana' size='2'>PH OT </font></td>";
    $column_count_1++;
    print "<td><font face='Verdana' size='2'>Night Prov</font></td>";
    $column_count_1++;
    print "<td><font face='Verdana' size='2'>Present days</font></td>";
    $count = 0;
    $sub_count = 0;
    $last_id = "";
    $last_name = "";
    $last_dept = "";
    $last_div = "";
    $last_idno = "";
    $last_rmk = "";
    $last_date = "";
    $font = "Black";
    $bgcolor = "#FFFFFF";
    $nextDate = insertDate($txtFrom);
    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tuser.idno, tuser.remark FROM tuser, tgroup WHERE tuser.id > 0 AND tuser.group_id = tgroup.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
    if ($lstShift != "") {
        $query = $query . " AND tgroup.id = " . $lstShift;
    }
    $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn);
    $query = $query . employeeStatusQuery($lstEmployeeStatus);
    if ($lstf != "") {
        $query = $query . " ORDER BY " . $lstSort;
    } else {
        $query = $query . " ORDER BY tuser.id";
    }
    $main_result = mysqli_query($conn, $query);
    while ($main_cur = mysqli_fetch_row($main_result)) {
        $sub_count = 0;
        if ($txtFrom != $txtTo && $lstEmployeeSeparator == "Yes") {
            print "<tr><td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td>";
            for ($j = 0; $j < $column_count_1; $j++) {
                print "<td><font face='Verdana' size='1'>&nbsp;</font></td>";
            }
            $this_day = getDate(strtotime(substr($i, 6, 2) . "-" . substr($i, 4, 2) . "-" . substr($i, 0, 4)));
            print "<td><font face='Verdana' size='1'>&nbsp;</font></td> ";
            for ($j = 0; $j < $column_count_2; $j++) {
                print "<td><font face='Verdana' size='1'>&nbsp;</font></td>";
            }
            print "</tr>";
        }
        $query = "SELECT tgroup.name, DayMaster.TDate, DayMaster.Entry, DayMaster.Start, DayMaster.BreakOut, DayMaster.BreakIn, DayMaster.Close, DayMaster.Exit, DayMaster.Flag, AttendanceMaster.Day, AttendanceMaster.EarlyIn, AttendanceMaster.LateIn, AttendanceMaster.Break, AttendanceMaster.LessBreak, AttendanceMaster.MoreBreak, AttendanceMaster.EarlyOut, AttendanceMaster.LateOut, AttendanceMaster.Normal, AttendanceMaster.Grace, AttendanceMaster.Overtime, AttendanceMaster.AOvertime, AttendanceMaster.OT1, AttendanceMaster.OT2, AttendanceMaster.LateIn_flag, AttendanceMaster.EarlyOut_flag, AttendanceMaster.MoreBreak_flag, Attendancemaster.nightflag, attendancemaster.flag FROM tgroup, DayMaster, AttendanceMaster WHERE DayMaster.e_id > 0 AND DayMaster.group_id = tgroup.id AND DayMaster.e_id = AttendanceMaster.EmployeeID AND DayMaster.TDate = AttendanceMaster.ADate AND DayMaster.e_id = " . $main_cur[0] . " AND DayMaster.TDate >= " . insertDate($txtFrom) . " AND DayMaster.TDate <= " . insertDate($txtTo);
        if ($lstColourFlag != "") {
            if ($lstColourFlag == "All w/o Black/Proxy") {
                $query = $query . " AND AttendanceMaster.Flag NOT LIKE 'Black' AND AttendanceMaster.Flag NOT LIKE 'Proxy'";
            } else {
                if ($lstColourFlag == "All w/o Proxy") {
                    $query = $query . " AND AttendanceMaster.Flag NOT LIKE 'Proxy'";
                } else {
                    $query = $query . " AND AttendanceMaster.Flag = '" . $lstColourFlag . "'";
                }
            }
        }
        if ($lstType != "") {
            if ($lstType == "Early In") {
                $query = $query . " AND AttendanceMaster.EarlyIn > 0 ";
            } else {
                if ($lstType == "Late In") {
                    $query = $query . " AND AttendanceMaster.LateIn > 0 ";
                } else {
                    if ($lstType == "Less Break") {
                        $query = $query . " AND AttendanceMaster.LessBreak > 0 ";
                    } else {
                        if ($lstType == "More Break") {
                            $query = $query . " AND AttendanceMaster.MoreBreak > 0 ";
                        } else {
                            if ($lstType == "Early Out") {
                                $query = $query . " AND AttendanceMaster.EarlyOut > 0 ";
                            } else {
                                if ($lstType == "Late Out") {
                                    $query = $query . " AND AttendanceMaster.LateOut > 0 ";
                                } else {
                                    if ($lstType == "Grace") {
                                        $query = $query . " AND AttendanceMaster.Grace > 0 ";
                                    } else {
                                        if ($lstType == "OT") {
                                            $query = $query . " AND AttendanceMaster.Overtime > 0 ";
                                        } else {
                                            if ($lstType == "Approved OT") {
                                                $query = $query . " AND AttendanceMaster.AOvertime > 0 ";
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $query = $query . " ORDER BY DayMaster.TDate";
        $nextDate = insertDate($txtFrom);
        $dayNow = getDay(displayDate($nextDate));
        $result = mysqli_query($iconn, $query);
        while ($cur = mysqli_fetch_row($result)) {
            $sub_count++;
            if ($nextDate < $cur[1]) {
                $shift = "&nbsp;";
                if ($lstAbsentShift == "Yes") {
                    $shift = $cur[0];
                }
                $count = displayAlterTime($conn, $nextDate, $cur[1] - 1, $main_cur[0], $main_cur[1], $main_cur[2], $main_cur[3], $main_cur[5], $main_cur[6], $column_count_1, $column_count_2, $txtRosterColumns, $prints, $count, $lstImproperClocking, $lstAbsent, $shift);
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
            addZero($main_cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
            print "<tr><td bgcolor='" . $bgcolor . "'><a title='ID'><font face='Verdana' size='1' color='" . $font . "'>" . addZero($main_cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='ERP ID'><font face='Verdana' size='1' color='" . $font . "'>" . $main_cur[6] . "</font></a></td>";
            displayDate($cur[1]);
            print "<td bgcolor='" . $bgcolor . "'><a title='Date'><font face='Verdana' size='1' color='" . $font . "'>" . displayDate($cur[1]) . "</font></a></td>";
            if ($cur[9] == "Saturday" || $cur[9] == "Sunday" || $cur[27] == "Purple") {
                print "<td bgcolor='" . $bgcolor . "'><a title='Normal OT'><font face='Verdana' size='1' color='" . $font . "'>0</font></a></td>";
            } else {
                $aot = $cur[20];
                if ($aot % 3600 < 1800) {
                    $aot = floor($aot / 60 / 30) / 2 * 3600;
                } else {
                    if (1800 <= $aot % 3600) {
                        $aot = ceil($aot / 60 / 60) / 1 * 3600;
                    }
                }
                round($aot / 3600);
                print "<td bgcolor='" . $bgcolor . "'><a title='Normal OT'><font face='Verdana' size='1' color='" . $font . "'>" . round($aot / 3600) . "</font></a></td>";
            }
            if ($cur[9] == "Saturday") {
                $aot = $cur[20];
                if ($aot % 3600 < 1800) {
                    $aot = floor($aot / 60 / 30) / 2 * 3600;
                } else {
                    if (1800 <= $aot % 3600) {
                        $aot = ceil($aot / 60 / 60) / 1 * 3600;
                    }
                }
                round($aot / 3600);
                print "<td bgcolor='" . $bgcolor . "'><a title='Sat OT'><font face='Verdana' size='1' color='" . $font . "'>" . round($aot / 3600) . "</font></a></td>";
            } else {
                print "<td bgcolor='" . $bgcolor . "'><a title='Sat OT'><font face='Verdana' size='1' color='" . $font . "'>0</font></a></td>";
            }
            if ($cur[9] == "Sunday") {
                $aot = $cur[20];
                if ($aot % 3600 < 1800) {
                    $aot = floor($aot / 60 / 30) / 2 * 3600;
                } else {
                    if (1800 <= $aot % 3600) {
                        $aot = ceil($aot / 60 / 60) / 1 * 3600;
                    }
                }
                round($aot / 3600);
                print "<td bgcolor='" . $bgcolor . "'><a title='Sun OT'><font face='Verdana' size='1' color='" . $font . "'>" . round($aot / 3600) . "</font></a></td>";
            } else {
                print "<td bgcolor='" . $bgcolor . "'><a title='Sun OT'><font face='Verdana' size='1' color='" . $font . "'>0</font></a></td>";
            }
            if ($cur[27] == "Purple") {
                $aot = $cur[20];
                if ($aot % 3600 < 1800) {
                    $aot = floor($aot / 60 / 30) / 2 * 3600;
                } else {
                    if (1800 <= $aot % 3600) {
                        $aot = ceil($aot / 60 / 60) / 1 * 3600;
                    }
                }
                round($aot / 3600);
                print "<td bgcolor='" . $bgcolor . "'><a title='PH OT'><font face='Verdana' size='1' color='" . $font . "'>" . round($aot / 3600) . "</font></a></td>";
            } else {
                print "<td bgcolor='" . $bgcolor . "'><a title='PH OT'><font face='Verdana' size='1' color='" . $font . "'>0</font></a></td>";
            }
            if ($cur[26] == 0) {
                print "<td bgcolor='" . $bgcolor . "'><a title='Night Shift'><font face='Verdana' size='1' color='" . $font . "'>0</font></a></td>";
            } else {
                print "<td bgcolor='" . $bgcolor . "'><a title='Night Shift'><font face='Verdana' size='1' color='" . $font . "'>8</font></a></td>";
            }
            if ($cur[27] == "Brown") {
                print "<td bgcolor='" . $bgcolor . "'><a title='Absent'><font face='Verdana' size='1' color='" . $font . "'>A</font></a></td>";
            } else {
                print "<td bgcolor='" . $bgcolor . "'><a title='Present'><font face='Verdana' size='1' color='" . $font . "'>P</font></a></td>";
            }
            print "</tr>";
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
            $count = displayAlterTime($conn, insertDate($txtFrom), insertDate($txtTo), $main_cur[0], $main_cur[1], $main_cur[2], $main_cur[3], $main_cur[5], $main_cur[6], $column_count_1, $column_count_2, $txtRosterColumns, $prints, $count, $lstImproperClocking, $lstAbsent, $shift);
        } else {
            if ($last_date < insertDate($txtTo)) {
                $count = displayAlterTime($conn, $nextDate, insertDate($txtTo), $main_cur[0], $main_cur[1], $main_cur[2], $main_cur[3], $main_cur[5], $main_cur[6], $column_count_1, $column_count_2, $txtRosterColumns, $prints, $count, $lstImproperClocking, $lstAbsent, $shift);
            }
        }
    }
    print "</table>";
    if ($excel != "yes") {
        print "<p align='center'><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $count . "</b></font>";
        if ($prints != "yes") {
            print "<br><input type='button' value='Print Report' onClick='checkPrint(0)'>&nbsp;&nbsp;<input type='button' value='Excel' onClick='checkPrint(1)'>";
        }
        print "</p>";
    }
}
print "</form>";
echo "</center></body></html>";
function displayAlterTime($conn, $from, $to, $id, $name, $dept, $div, $idno, $rmk, $column_count_1, $column_count_2, $txtRosterColumns, $prints, $count, $lstImproperClocking, $lstAbsent, $shift)
{
    global $session_variable;
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
                        $start = "<a href='AlterTime.php?act=searchRecord&txtEmployeeCode=" . $id . "&txtFrom=" . displayDate($i) . "&txtTo=" . displayDate($i) . "' title='Click here to view the Improper Clockins' target='_blank'><font face='Verdana' color='#000000'>" . displayVirdiTime($start) . "</font></a>";
                    }
                }
                if ($close == "") {
                    $close = "&nbsp;";
                } else {
                    if ($prints == "yes") {
                        $close = displayVirdiTime($close);
                    } else {
                        $close = "<a href='AlterTime.php?act=searchRecord&txtEmployeeCode=" . $id . "&txtFrom=" . displayDate($i) . "&txtTo=" . displayDate($i) . "' title='Click here to view the Improper Clockins' target='_blank'><font face='Verdana' color='#000000'>" . displayVirdiTime($close) . "</font></a>";
                    }
                }
                if (!($lstAbsent == "No" && $start == "&nbsp;" && $close == "&nbsp;")) {
                    addZero($id, $_SESSION[$session_variable . "EmployeeCodeLength"]);
                    print "<tr><td><a title='ID'><font face='Verdana' size='1'>" . addZero($id, $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='ERP ID'><font face='Verdana' size='1'>" . $rmk . "</font></a></td>";
                    $column_count_1_minus = 0;
                    $this_day = getDate(strtotime(substr($i, 6, 2) . "-" . substr($i, 4, 2) . "-" . substr($i, 0, 4)));
                    displayDate($i);
                    print "<td><a title='Dept'><font face='Verdana' size='1'>" . displayDate($i) . "</font></a></td>";
                    $column_count_1_minus++;
                    print "<td><a title=''><font face='Verdana' size='1'>0</font></a></td><td><a title=''><font face='Verdana' size='1'>0</font></a></td><td><a title=''><font face='Verdana' size='1'>0</font></a></td><td><a title=''><font face='Verdana' size='1'>0</font></a></td><td><a title=''><font face='Verdana' size='1'>0</font></a></td> ";
                    if (getDay(displayDate($i)) == "Saturday" || getDay(displayDate($i)) == "Sunday") {
                        print "<td><a title='Absent'><font face='Verdana' size='1'>P</font></a></td> ";
                    } else {
                        print "<td><a title='Absent'><font face='Verdana' size='1'>A</font></a></td> ";
                    }
                    print "</tr>";
                    $count++;
                }
            }
        }
    }
    return $count;
}

?>