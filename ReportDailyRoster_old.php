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
$txtMACAddress = $_SESSION[$session_variable . "MACAddress"];
$weirdTimeDisplay = false;
if (getWeirdClient($txtMACAddress)) {
    $weirdTimeDisplay = true;
}
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=ReportDailyRoster.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$act = $_GET["act"];
if ($act == "") {
    $act = $_POST["act"];
}
$prints = $_GET["prints"];
$timecard = $_GET["timecard"];
$excel = $_GET["excel"];
$csv = $_GET["csv"];
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
    $lstRemark = "No";
}
$lstAbsent = $_POST["lstAbsent"];
if ($lstAbsent == "") {
    $lstAbsent = $_GET["lstAbsent"];
}
if ($lstAbsent == "") {
    $lstAbsent = "Yes";
}
$lstTerminal = $_POST["lstTerminal"];
if ($lstTerminal == "") {
    $lstTerminal = $_GET["lstTerminal"];
}
if ($lstTerminal == "") {
    $lstTerminal = "No";
}
$lstDayNight = $_POST["lstDayNight"];
if ($lstDayNight == "") {
    $lstDayNight = $_GET["lstDayNight"];
}
if ($lstDayNight == "") {
    $lstDayNight = "No";
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
    $lstEmployeeSeparator = "Yes";
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
$txtF1 = $_POST["txtF1"];
$txtF2 = $_POST["txtF2"];
$txtF3 = $_POST["txtF3"];
$txtF4 = $_POST["txtF4"];
$txtF5 = $_POST["txtF5"];
$txtF6 = $_POST["txtF6"];
$txtF7 = $_POST["txtF7"];
$txtF8 = $_POST["txtF8"];
$txtF9 = $_POST["txtF9"];
$txtF10 = $_POST["txtF10"];
$lstDB = $_POST["lstDB"];
if ($lstDB == "") {
    $lstDB = $_GET["lstDB"];
}
if ($lstDB == "") {
    $lstDB = "Live";
}
if ($subReport != "yes") {
    displaySuperHeader($prints, $excel, $csv, $current_module, $userlevel, "Daily Roster Report", true, false);
}
if ($csv != "yes") {
    print "<style>@media print {h2 { page-break-before: always;}} </style>";
}
if ($excel != "yes" && $csv != "yes") {
    print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b></font></p>";
}
if ($prints != "yes") {
    print "<table width='800' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
    print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Select ONE or MORE options and click 'Search Record'</b></font></td></tr>";
} else {
    if ($excel != "yes" && $timecard != "yes" && $subReport != "yes") {
        print "<table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
        print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Selected Options</b></font></td></tr>";
    }
}
if ($excel != "yes" && $timecard != "yes" && $subReport != "yes") {
    print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='ReportDailyRoster.php'><input type='hidden' name='act' value='searchRecord'><tr>";
    $query = "SELECT id, name from tgroup WHERE id > 1 AND ShiftTypeID = 1 ORDER BY name";
    displayList("lstShift", "Current Shift: ", $lstShift, $prints, $conn, $query, "", "25%", "75%");
    print "</tr>";
    displaySearchFields($conn, $prints, $session_variable, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $lstEmployeeName, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
    print "<tr>";
    print "<td colspan='2'><table width='100%' cellspacing='-1' cellpadding='-1' border='0'><tr>";
    displayTextbox("txtFrom", "Date From <font size='1'>(DD/MM/YYYY)</font>: ", $txtFrom, $prints, 12, "25%", "25%");
    if ($prints != "yes") {
        displayColourFlag($conn, $lstColourFlag, "lstColourFlag", true, true);
    } else {
        displayTextbox("lstColourFlag", "Flag: ", $lstColourFlag, $prints, 12, "25%", "25%");
    }
    print "</tr>";
    print "<tr><td colspan='4' width='100%'><img src='img/cBar.gif' width='100%' height='3'></td></tr>";
    print "<tr>";
    displayTextbox("txtTo", "Date To <font size='1'>(DD/MM/YYYY)</font>: ", $txtTo, $prints, 12, "25%", "25%");
    if ($prints != "yes") {
        print "<td align='right' width='25%'><font face='Verdana' size='2'>Work Type:</font></td><td width='25%'><select name='lstType' class='form-control'><option selected value='" . $lstType . "'>" . $lstType . "</option> <option value='Early In'>Early In</option> <option value='Late In'>Late In</option> <option value='Less Break'>Less Break</option> <option value='More Break'>More Break</option> <option value='Early Out'>Early Out</option> <option value='Late Out'>Late Out</option> <option value='Grace'>Grace</option> <option value='OT'>OT</option> <option value='Approved OT'>Approved OT</option> <option value=''>---</option></select></td>";
    } else {
        displayTextbox("lstType", "Work Type: ", $lstType, $prints, 12, "25%", "25%");
    }
    print "</tr></table></td>";
    print "</tr>";
    if ($prints != "yes") {
        print "<tr>";
        print "<td colspan='2'><table width='100%' cellspacing='-1' cellpadding='-1' border='0'><tr>";
        print "<td align='right' width='25%'><font face='Verdana' size='2'>Print Remark Column:</font></td><td width='15%'><select name='lstRemark' class='form-control'><option selected value='" . $lstRemark . "'>" . $lstRemark . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select></td>";
        print "<td align='right' width='35%'><font face='Verdana' size='2'>Display Shift of Absent Employees:</font></td><td width='25%'><select name='lstAbsentShift' class='form-control'> <option selected value='" . $lstAbsentShift . "'>" . $lstAbsentShift . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select></td>";
        print "</tr></table></td>";
        print "</tr>";
        print "<tr>";
        print "<td colspan='2'><table width='100%' cellspacing='-1' cellpadding='-1' border='0'><tr>";
        print "<td align='right' width='25%'><font face='Verdana' size='2'>Display Improper Clocking:</font></td><td width='15%'><select name='lstImproperClocking' class='form-control'><option selected value='" . $lstImproperClocking . "'>" . $lstImproperClocking . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select></td>";
        print "<td align='right' width='35%'><font face='Verdana' size='2'>Display Employee Separator Row:</font></td><td width='25%'><select name='lstEmployeeSeparator' class='form-control'><option selected value='" . $lstEmployeeSeparator . "'>" . $lstEmployeeSeparator . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select></td>";
        print "</tr></table></td>";
        print "</tr>";
        print "<tr>";
        print "<td colspan='2'><table width='100%' cellspacing='-1' cellpadding='-1' border='0'><tr>";
        displayEmployeeStatus($conn, "lstEmployeeStatus", $lstEmployeeStatus, "25%", "35%");
        print "<td align='right' width='15%'><font face='Verdana' size='1'>Display Absent Days:</font></td><td width='25%'><select name='lstAbsent' class='form-control'><option selected value='" . $lstAbsent . "'>" . $lstAbsent . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select></td>";
        print "</tr></table></td>";
        print "</tr>";
        print "<tr>";
        print "<td colspan='2'><table width='100%' cellspacing='-1' cellpadding='-1' border='0'><tr>";
        print "<td align='right' width='25%'><font face='Verdana' size='1'>Display Clocking Terminal:</font></td><td width='25%'><select name='lstTerminal' class='form-control'><option selected value='" . $lstTerminal . "'>" . $lstTerminal . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select></td>";
        print "<td align='right' width='25%'><font face='Verdana' size='1'>Display Day/Night Shift:</font></td><td width='25%'><select name='lstDayNight' class='form-control'><option selected value='" . $lstDayNight . "'>" . $lstDayNight . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select></td>";
        print "</tr></table></td>";
        print "</tr>";
        print "<tr><td colspan='2'><table width='100%' cellspacing='-1' cellpadding='-1' border='0'><tr>";
        print "<td align='right' width='25%'><font face='Verdana' size='2'>DB:</font></td><td width='25%'><select name='lstDB' class='form-control'><option selected value='" . $lstDB . "'>" . $lstDB . "</option> <option value='Live'>Live</option> <option value='Archive'>Archive</option> </select></td>";
        print "<td align='right' width='25%'><font face='Verdana' size='2'>&nbsp;</font></td><td width='25%'>&nbsp;</td>";
        print "</tr></table></td></tr>";
        print "<tr>";
        $array = array(array("tuser.id", "Employee Code"), array("tuser.name, tuser.id", "Employee Name - Code"), array("tuser.dept, tuser.id", "Dept - Code"), array("tuser.company, tuser.dept, tuser.id", "Div/Desg - Dept - Code"), array("tuser.company, tuser.dept, tuser.group_id, tuser.id", "Div - Dept - Current Shift - Code"));
        displaySort($array, $lstSort, 5);
        print "</tr>";
        print "<tr><td>&nbsp;</td><td><input name='btSearch' type='submit' value='Search Record'>&nbsp;&nbsp;<input type='button' value='Print Time Card' onClick='checkPrint(2)'>&nbsp;&nbsp;<input type='button' value='Excel' onClick='checkPrint(1)'>&nbsp;&nbsp;<input type='button' value='CSV' onClick='checkPrint(3)'></td></tr>";
    }
    print "</table><br>";
}
if ($act == "searchRecord") {
    if ($lstColourFlag != "" || $lstType != "") {
        $lstAbsent = "No";
        $lstImproperClocking = "No";
    }
    $query = "SELECT RosterColumns FROM OtherSettingMaster";
    $result = selectData($conn, $query);
    $txtRosterColumns = $result[0];
    if ($excel != "yes" && $timecard != "yes" && $subReport != "yes") {
        print "<p align='center'><font face='Verdana' size='1'><b>" . $_SESSION[$session_variable . "FlagReportText"] . "</b></font></p>";
    }
    if ($prints != "yes") {
        print "<table border='1' cellpadding='0' bordercolor='#C0C0C0' cellspacing='-1' width='800'>";
    } else {
        if ($csv != "yes") {
            print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%'>";
        }
    }
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
    $pa_flag = true;
    $force_count = 0;
    $column_count_1 = 0;
    $column_count_2 = 0;
    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tuser.idno, tuser.remark, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, tgroup WHERE tuser.id > 0 AND tuser.group_id = tgroup.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
    if ($lstShift != "") {
        $query = $query . " AND tgroup.id = " . $lstShift;
    }
    $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
    $query = $query . employeeStatusQuery($lstEmployeeStatus);
    if ($lstSort != "") {
        $query = $query . " ORDER BY " . $lstSort;
    } else {
        $query = $query . " ORDER BY tuser.id";
    }
    $main_result = mysqli_query($conn, $query);
    while ($main_cur = mysqli_fetch_row($main_result)) {
        $sub_count = 0;
        $force_count++;
        if ($lstEmployeeSeparator == "Yes" && ($txtFrom != $txtTo || $force_count == 1) || $lstEmployeeSeparator == "No" && $force_count == 1) {
            if (1 < $force_count) {
                if ($timecard == "yes") {
                    print "<tr>";
                    if (strpos($txtRosterColumns, "chkShift") !== false) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Shift'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
                    }
                    if (strpos($txtRosterColumns, "chkOT1") !== false) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Special OT Day 1'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
                    }
                    if (strpos($txtRosterColumns, "chkOT2") !== false) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Special OT Day 2'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
                    }
                    print "<td bgcolor='" . $bgcolor . "'><a title='Date'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Day'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
                    if (strpos($txtRosterColumns, "chkFlag") !== false) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Flag'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
                    }
                    if (strpos($txtRosterColumns, "chkEntry") !== false) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Entry'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
                    }
                    if (strpos($txtRosterColumns, "chkStart") !== false) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Start'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
                    }
                    if (strpos($txtRosterColumns, "chkBreakOut") !== false) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Break Out'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
                    }
                    if (strpos($txtRosterColumns, "chkBreakIn") !== false) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Break In'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
                    }
                    if (strpos($txtRosterColumns, "chkClose") !== false) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Close'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
                    }
                    if (strpos($txtRosterColumns, "chkExit") !== false) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Exit'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
                    }
                    if (strpos($txtRosterColumns, "chkEarlyIn") !== false) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Early In'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></td>";
                    }
                    if (strpos($txtRosterColumns, "chkLateIn") !== false) {
                        if ($weirdTimeDisplay) {
                            getWeirdTime($tc_li);
                            print "<td bgcolor='" . $bgcolor . "'><a title='Late In'><font face='Verdana' size='1' color='" . $font . "'><b>" . getWeirdTime($tc_li) . "</b></font></td>";
                        } else {
                            round($tc_li / 60, 2);
                            print "<td bgcolor='" . $bgcolor . "'><a title='Late In'><font face='Verdana' size='1' color='" . $font . "'><b>" . round($tc_li / 60, 2) . "</b></font></td>";
                        }
                    }
                    if (strpos($txtRosterColumns, "chkLessBreak") !== false) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Less Break'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></td>";
                    }
                    if (strpos($txtRosterColumns, "chkMoreBreak") !== false) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='More Break'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></td>";
                    }
                    if (strpos($txtRosterColumns, "chkEarlyOut") !== false) {
                        if ($weirdTimeDisplay) {
                            getWeirdTime($tc_eo);
                            print "<td bgcolor='" . $bgcolor . "'><a title='Early Out'><font face='Verdana' size='1' color='" . $font . "'><b>" . getWeirdTime($tc_eo) . "</b></font></td>";
                        } else {
                            round($tc_eo / 60, 2);
                            print "<td bgcolor='" . $bgcolor . "'><a title='Early Out'><font face='Verdana' size='1' color='" . $font . "'><b>" . round($tc_eo / 60, 2) . "</b></font></td>";
                        }
                    }
                    if (strpos($txtRosterColumns, "chkLateOut") !== false) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Late Out'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></td>";
                    }
                    if (strpos($txtRosterColumns, "chkGrace") !== false) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Grace'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></td>";
                    }
                    if (strpos($txtRosterColumns, "chkNormal") !== false) {
                        round($tc_n / 3600, 2);
                        print "<td bgcolor='" . $bgcolor . "'><a title='Normal'><font face='Verdana' size='1' color='" . $font . "'><b>" . round($tc_n / 3600, 2) . "</b></font></td>";
                    }
                    if (strpos($txtRosterColumns, "chkOT") !== false) {
                        if ($weirdTimeDisplay) {
                            getWeirdTime($tc_ot);
                            print "<td bgcolor='" . $bgcolor . "'><a title='OT'><font face='Verdana' size='1' color='" . $font . "'><b>" . getWeirdTime($tc_ot) . "</b></font></td>";
                        } else {
                            round($tc_ot / 3600, 2);
                            print "<td bgcolor='" . $bgcolor . "'><a title='OT'><font face='Verdana' size='1' color='" . $font . "'><b>" . round($tc_ot / 3600, 2) . "</b></font></td>";
                        }
                    }
                    if (strpos($txtRosterColumns, "chkAppOT") !== false) {
                        if ($weirdTimeDisplay) {
                            if (getRegister($txtMACAddress, 7) == "25") {
                                print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'><b>&nbsp;</b></font></td>";
                                print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'><b>&nbsp;</b></font></td>";
                                getWeirdTime($tc_aot);
                                print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'><b>" . getWeirdTime($tc_aot) . "</b></font></td>";
                            } else {
                                getWeirdTime($tc_aot);
                                print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'><b>" . getWeirdTime($tc_aot) . "</b></font></td>";
                            }
                        } else {
                            if (getRegister($txtMACAddress, 7) == "25") {
                                print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'><b>&nbsp;</b></font></td>";
                                print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'><b>&nbsp;</b></font></td>";
                                round($tc_aot / 3600, 2);
                                print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'><b>" . round($tc_aot / 3600, 2) . "</b></font></td>";
                            } else {
                                round($tc_aot / 3600, 2);
                                print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'><b>" . round($tc_aot / 3600, 2) . "</b></font></td>";
                            }
                        }
                    }
                    if (strpos($txtRosterColumns, "chkTH") !== false) {
                        if ($weirdTimeDisplay) {
                            getWeirdTime($tc_t);
                            print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'><b>" . getWeirdTime($tc_t) . "</b></font></td>";
                        } else {
                            round($tc_t / 3600, 2);
                            print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'><b>" . round($tc_t / 3600, 2) . "</b></font></td>";
                        }
                    }
                    if ($txtMACAddress == "40-A8-F0-23-F0-AD") {
                        print "<td><font face='Verdana' size='1'>P</font></td>";
                    }
                    if ($lstRemark != "" && $prints == "yes") {
                        print "<td width=50><font face='Verdana' size='2'>&nbsp;</font></td>";
                    }
                    print "</tr>";
                    print "</table>";
                    print "<h2></h2>";
                    print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%'>";
                } else {
                    if ($csv != "yes") {
                        print "<tr><td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td>";
                        if (insertToday() < 20150331) {
                            print "<td><font face='Verdana' size='2'>&nbsp;</font></td>";
                        }
                        for ($j = 0; $j < $column_count_1; $j++) {
                            print "<td><font face='Verdana' size='1'>&nbsp;</font></td>";
                        }
                        $this_day = getDate(strtotime(substr($i, 6, 2) . "-" . substr($i, 4, 2) . "-" . substr($i, 0, 4)));
                        print "<td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td>";
                        for ($j = 0; $j < $column_count_2; $j++) {
                            print "<td><font face='Verdana' size='1'>&nbsp;</font></td>";
                        }
                        print "</tr>";
                    } else {
                        print ";;";
                        if (insertToday() < 20150331) {
                            print ";";
                        }
                        for ($j = 0; $j < $column_count_1; $j++) {
                            print ";";
                        }
                        $this_day = getDate(strtotime(substr($i, 6, 2) . "-" . substr($i, 4, 2) . "-" . substr($i, 0, 4)));
                        print ";;";
                        for ($j = 0; $j < $column_count_2; $j++) {
                            print ";";
                        }
                        print "\n";
                    }
                }
            }
            if ($timecard == "yes") {
                print "<tr><td colspan='20'><font face='Verdana' size='2'>";
                print "<b>ID:</b> " . $main_cur[0];
                print "<br><br><b>Name:</b> " . $main_cur[1];
                print "<br><br><b>Dept:</b> " . $main_cur[2];
                print "<br><br><b>Div/ Desgn:</b> " . $main_cur[3];
                print "<br><br><b>" . $_SESSION[$session_variable . "IDColumnName"] . ":</b> " . $main_cur[5];
                print "<br><br><b>Remark:</b> " . $main_cur[6];
                print "<br><br><b>From:</b> " . $txtFrom . " - <b>To:</b>" . $txtTo;
                print "</font></td></tr>";
            }
            $column_count_1 = 0;
            $column_count_2 = 0;
            if ($csv != "yes") {
                print "<tr>";
            }
            if ($timecard != "yes") {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td>";
                } else {
                    print "ID;Name;";
                }
            }
            if (insertToday() < 20150331 && $timecard != "yes") {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>TRML</font></td>";
                } else {
                    print "TRML;";
                }
            }
            if (strpos($txtRosterColumns, "chkIDColumn") !== false && $timecard != "yes") {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td>";
                } else {
                    print $_SESSION[$session_variable . "IDColumnName"] . ";";
                }
                $column_count_1++;
            }
            if (strpos($txtRosterColumns, "chkDept") !== false && $timecard != "yes") {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>Dept</font></td>";
                } else {
                    print "Dept;";
                }
                $column_count_1++;
            }
            if (strpos($txtRosterColumns, "chkDiv") !== false && $timecard != "yes") {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "DivColumnName"] . "</font></td>";
                } else {
                    print $_SESSION[$session_variable . "DivColumnName"] . ";";
                }
                $column_count_1++;
            }
            if (strpos($txtRosterColumns, "chkRmk") !== false && $timecard != "yes") {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "RemarkColumnName"] . "</font></td>";
                } else {
                    print $_SESSION[$session_variable . "RemarkColumnName"] . ";";
                }
                $column_count_1++;
            }
            if (strpos($txtRosterColumns, "chkF1") !== false && $timecard != "yes") {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "F1"] . "</font></td>";
                } else {
                    print $_SESSION[$session_variable . "F1"] . ";";
                }
                $column_count_1++;
            }
            if (strpos($txtRosterColumns, "chkF2") !== false && $timecard != "yes") {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "F2"] . "</font></td>";
                } else {
                    print $_SESSION[$session_variable . "F2"] . ";";
                }
                $column_count_1++;
            }
            if (strpos($txtRosterColumns, "chkF3") !== false && $timecard != "yes") {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "F3"] . "</font></td>";
                } else {
                    print $_SESSION[$session_variable . "F3"] . ";";
                }
                $column_count_1++;
            }
            if (strpos($txtRosterColumns, "chkF4") !== false && $timecard != "yes") {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "F4"] . "</font></td>";
                } else {
                    print $_SESSION[$session_variable . "F4"] . ";";
                }
                $column_count_1++;
            }
            if (strpos($txtRosterColumns, "chkF5") !== false && $timecard != "yes") {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "F5"] . "</font></td>";
                } else {
                    print $_SESSION[$session_variable . "F5"] . ";";
                }
                $column_count_1++;
            }
            if (strpos($txtRosterColumns, "chkShift") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>Shift</font></td>";
                } else {
                    print "Shift;";
                }
                $column_count_1++;
            }
            if (strpos($txtRosterColumns, "chkOT1") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>OT 1</font></td>";
                } else {
                    print "OT 1;";
                }
                $column_count_1++;
            }
            if (strpos($txtRosterColumns, "chkOT2") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>OT 2</font></td>";
                } else {
                    print "OT 2;";
                }
                $column_count_1++;
            }
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='2'>Date</font></td> <td><font face='Verdana' size='2'>Day</font></td>";
            } else {
                print "Date;Day;";
            }
            if (strpos($txtRosterColumns, "chkFlag") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>Flag</font></td>";
                } else {
                    print "Flag;";
                }
                $column_count_2++;
            }
            if (strpos($txtRosterColumns, "chkEntry") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>Entry</font></td>";
                } else {
                    print "Entry;";
                }
                $column_count_2++;
            }
            if ($lstTerminal == "Yes") {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>Terminal</font></a></td>";
                } else {
                    print "Terminal;";
                }
                $column_count_2++;
            }
            if (strpos($txtRosterColumns, "chkStart") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'><b>Start</b></font></td>";
                } else {
                    print "Start;";
                }
                $column_count_2++;
            }
            if (strpos($txtRosterColumns, "chkBreakOut") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>BreakOut</font></td>";
                } else {
                    print "BreakOut;";
                }
                $column_count_2++;
            }
            if (strpos($txtRosterColumns, "chkBreakIn") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>BreakIn</font></td>";
                } else {
                    print "BreakIn;";
                }
                $column_count_2++;
            }
            if (strpos($txtRosterColumns, "chkClose") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'><b>Close</b></font></td>";
                } else {
                    print "Close;";
                }
                $column_count_2++;
            }
            if ($lstTerminal == "Yes") {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>Terminal</font></a></td>";
                } else {
                    print "Terminal;";
                }
                $column_count_2++;
            }
            if (strpos($txtRosterColumns, "chkExit") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>Exit</font></td>";
                } else {
                    print "Exit;";
                }
                $column_count_2++;
            }
            if (strpos($txtRosterColumns, "chkEarlyIn") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>Early In <br>(Min)</font></td>";
                } else {
                    print "Early In;";
                }
                $column_count_2++;
            }
            if (strpos($txtRosterColumns, "chkLateIn") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>Late In <br>(Min)</font></td>";
                } else {
                    print "Late In;";
                }
                $column_count_2++;
            }
            if (strpos($txtRosterColumns, "chkLessBreak") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>Less Break <br>(Min)</font></td>";
                } else {
                    print "Less Break;";
                }
                $column_count_2++;
            }
            if (strpos($txtRosterColumns, "chkMoreBreak") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>More Break <br>(Min)</font></td>";
                } else {
                    print "More Break;";
                }
                $column_count_2++;
            }
            if (strpos($txtRosterColumns, "chkEarlyOut") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>Early Out <br>(Min)</font></td>";
                } else {
                    print "Early Out;";
                }
                $column_count_2++;
            }
            if (strpos($txtRosterColumns, "chkLateOut") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>Late Out <br>(Min)</font></td>";
                } else {
                    print "Late Out;";
                }
                $column_count_2++;
            }
            if (strpos($txtRosterColumns, "chkGrace") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>Grace <br>(Min)</font></td>";
                } else {
                    print "Grace;";
                }
                $column_count_2++;
            }
            if (strpos($txtRosterColumns, "chkNormal") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>Normal <br>(Hrs)</font></td>";
                } else {
                    print "Normal;";
                }
                $column_count_2++;
            }
            if (strpos($txtRosterColumns, "chkOT") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>OT <br>(Hrs)</font></td>";
                } else {
                    print "OT Hrs;";
                }
                $column_count_2++;
            }
            if (strpos($txtRosterColumns, "chkAppOT") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>App OT <br>(Hrs)</font></td>";
                    if (getRegister($txtMACAddress, 7) == "25") {
                        print "<td><font face='Verdana' size='2'>App Late In<br>(Min)</font></td>";
                        print "<td><font face='Verdana' size='2'>Total App OT<br>(Hrs)</font></td>";
                    }
                } else {
                    print "App OT Hrs;";
                    if (getRegister($txtMACAddress, 7) == "25") {
                        print "App Late In (Min);";
                        print "Total App OT (Hrs);";
                    }
                }
                $column_count_2++;
                if (getRegister($txtMACAddress, 7) == "25") {
                    $column_count_2++;
                    $column_count_2++;
                }
            }
            if (strpos($txtRosterColumns, "chkTH") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>Total <br>(Hrs)</font></td>";
                } else {
                    print "Total Hrs;";
                }
                $column_count_2++;
            }
            if ($lstRemark != "" && $prints == "yes") {
                if ($csv != "yes") {
                    print "<td width=50><font face='Verdana' size='2'>Rmk</font></td>";
                } else {
                    print "Rmk;";
                }
                $column_count_2++;
            }
            if ($txtMACAddress == "40-A8-F0-23-F0-AD") {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>P/A</font></td>";
                } else {
                    print "P/A;";
                }
                $column_count_2++;
            }
            if ($csv != "yes") {
                print "</tr>";
            } else {
                print "\n";
            }
        }
        $table_name = "Access.DayMaster";
        $table_name_ = "Access.AttendanceMaster";
        $_table_name = "Access.tenter";
        if ($lstDB == "Archive") {
            $table_name = "AccessArchive.archive_dm";
            $table_name_ = "AccessArchive.archive_am";
            $_table_name = "AccessArchive.archive_tenter";
        }
        $query = "SELECT tgroup.name, " . $table_name . ".TDate, " . $table_name . ".Entry, " . $table_name . ".Start, " . $table_name . ".BreakOut, " . $table_name . ".BreakIn, " . $table_name . ".Close, " . $table_name . ".Exit, " . $table_name . ".Flag, " . $table_name_ . ".Day, " . $table_name_ . ".EarlyIn, " . $table_name_ . ".LateIn, " . $table_name_ . ".Break, " . $table_name_ . ".LessBreak, " . $table_name_ . ".MoreBreak, " . $table_name_ . ".EarlyOut, " . $table_name_ . ".LateOut, " . $table_name_ . ".Normal, " . $table_name_ . ".Grace, " . $table_name_ . ".Overtime, " . $table_name_ . ".AOvertime, " . $table_name_ . ".OT1, " . $table_name_ . ".OT2, " . $table_name_ . ".LateIn_flag, " . $table_name_ . ".EarlyOut_flag, " . $table_name_ . ".MoreBreak_flag, " . $table_name_ . ".LateInColumn FROM tgroup, " . $table_name . ", " . $table_name_ . " WHERE " . $table_name . ".e_id > 0 AND " . $table_name . ".group_id = tgroup.id AND " . $table_name . ".e_id = " . $table_name_ . ".EmployeeID AND " . $table_name . ".TDate = " . $table_name_ . ".ADate AND " . $table_name . ".e_id = " . $main_cur[0] . " AND " . $table_name . ".TDate >= " . insertDate($txtFrom) . " AND " . $table_name . ".TDate <= " . insertDate($txtTo);
        if ($lstColourFlag != "") {
            if ($lstColourFlag == "Black/Proxy") {
                $query = $query . " AND (" . $table_name_ . ".Flag = 'Black' OR " . $table_name_ . ".Flag = 'Proxy') ";
            } else {
                if ($lstColourFlag == "All w/o Black/Proxy") {
                    $query = $query . " AND " . $table_name_ . ".Flag NOT LIKE 'Black' AND " . $table_name_ . ".Flag NOT LIKE 'Proxy'";
                } else {
                    if ($lstColourFlag == "All w/o Proxy") {
                        $query = $query . " AND " . $table_name_ . ".Flag NOT LIKE 'Proxy'";
                    } else {
                        $query = $query . " AND " . $table_name_ . ".Flag = '" . $lstColourFlag . "'";
                    }
                }
            }
        }
        if ($lstType != "") {
            if ($lstType == "Early In") {
                $query = $query . " AND " . $table_name_ . ".EarlyIn > 0 ";
            } else {
                if ($lstType == "Late In") {
                    $query = $query . " AND " . $table_name_ . ".LateIn > 0 ";
                } else {
                    if ($lstType == "Less Break") {
                        $query = $query . " AND " . $table_name_ . ".LessBreak > 0 ";
                    } else {
                        if ($lstType == "More Break") {
                            $query = $query . " AND " . $table_name_ . ".MoreBreak > 0 ";
                        } else {
                            if ($lstType == "Early Out") {
                                $query = $query . " AND " . $table_name_ . ".EarlyOut > 0 ";
                            } else {
                                if ($lstType == "Late Out") {
                                    $query = $query . " AND " . $table_name_ . ".LateOut > 0 ";
                                } else {
                                    if ($lstType == "Grace") {
                                        $query = $query . " AND " . $table_name_ . ".Grace > 0 ";
                                    } else {
                                        if ($lstType == "OT") {
                                            $query = $query . " AND " . $table_name_ . ".Overtime > 0 ";
                                        } else {
                                            if ($lstType == "Approved OT") {
                                                $query = $query . " AND " . $table_name_ . ".AOvertime > 0 ";
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
        $query = $query . " ORDER BY " . $table_name . ".TDate";
        $nextDate = insertDate($txtFrom);
        $result = mysqli_query($conn, $query);
        $tc_li = 0;
        $tc_eo = 0;
        $tc_n = 0;
        $tc_ot = 0;
        $tc_aot = 0;
        $tc_t = 0;
        while ($cur = mysqli_fetch_row($result)) {
            $sub_count++;
            if ($nextDate < $cur[1]) {
                $shift = " ";
                if ($lstAbsentShift == "Yes") {
                    $shift = $cur[0];
                }
                if ($lstDayNight == "Yes") {
                    $shift = "Absent";
                }
                $count = displayAlterTime($conn, $nextDate, $cur[1] - 1, $main_cur[0], $main_cur[1], $main_cur[2], $main_cur[3], $main_cur[5], $main_cur[6], $main_cur[7], $main_cur[8], $main_cur[9], $main_cur[10], $main_cur[11], $column_count_1, $column_count_2, $txtRosterColumns, $prints, $count, $lstImproperClocking, $lstAbsent, $shift, $txtMACAddress, $timecard, $lstTerminal, $lstDB, $csv);
            }
            if ($cur[8] != "" && strpos($txtRosterColumns, "chkFlag") !== false) {
                $font = $cur[8];
                if ($font == "Yellow") {
                    $bgcolor = "Brown";
                } else {
                    $bgcolor = "#FFFFFF";
                }
            } else {
                $cur[8] = " ";
                $font = "Black";
                $bgcolor = "#FFFFFF";
            }
            if ($main_cur[3] == "") {
                $main_cur[3] = " ";
            }
            if ($main_cur[5] == "") {
                $main_cur[5] = " ";
            }
            if ($main_cur[6] == "") {
                $main_cur[6] = " ";
            }
            if ($main_cur[7] == "") {
                $main_cur[7] = " ";
            }
            if ($main_cur[8] == "") {
                $main_cur[8] = " ";
            }
            if ($main_cur[9] == "") {
                $main_cur[9] = " ";
            }
            if ($main_cur[10] == "") {
                $main_cur[10] = " ";
            }
            if ($main_cur[11] == "") {
                $main_cur[11] = " ";
            }
            if ($csv != "yes") {
                print "<tr>";
            }
            if ($timecard != "yes") {
                if ($csv != "yes") {
                    addZero($main_cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
                    print "<td bgcolor='" . $bgcolor . "'><a title='ID'><font face='Verdana' size='1' color='" . $font . "'>" . addZero($main_cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Name'><font face='Verdana' size='1' color='" . $font . "'>" . $main_cur[1] . "</font></a></td>";
                } else {
                    addZero($main_cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
                    print addZero($main_cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . ";" . $main_cur[1] . ";";
                }
            }
            if (insertToday() < 20150331 && $timecard != "yes") {
                $terminal_query = "SELECT g_id, tgate.name FROM " . $_table_name . ", tgate WHERE " . $_table_name . ".e_id = '" . $main_cur[0] . "' AND " . $_table_name . ".e_date = '" . $cur[1] . "' AND " . $_table_name . ".g_id = tgate.id ";
                $terminal_result = selectData($conn, $terminal_query);
                if ($csv != "yes") {
                    print "<td bgcolor='" . $bgcolor . "'><a title='TRML'><font face='Verdana' size='1' color='" . $font . "'>" . $terminal_result[1] . "</font></a></td>";
                } else {
                    print $terminal_result[1] . ";";
                }
            }
            if (strpos($txtRosterColumns, "chkIDColumn") !== false && $timecard != "yes") {
                if ($csv != "yes") {
                    print "<td bgcolor='" . $bgcolor . "'><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1' color='" . $font . "'>" . $main_cur[5] . "</font></a></td>";
                } else {
                    print $main_cur[5] . ";";
                }
            }
            if (strpos($txtRosterColumns, "chkDept") !== false && $timecard != "yes") {
                if ($csv != "yes") {
                    print "<td bgcolor='" . $bgcolor . "'><a title='Dept'><font face='Verdana' size='1' color='" . $font . "'>" . $main_cur[2] . "</font></a></td>";
                } else {
                    print $main_cur[2] . ";";
                }
            }
            if (strpos($txtRosterColumns, "chkDiv") !== false && $timecard != "yes") {
                if ($csv != "yes") {
                    print "<td bgcolor='" . $bgcolor . "'><a title='Div/Desg'><font face='Verdana' size='1' color='" . $font . "'>" . $main_cur[3] . "</font></a></td>";
                } else {
                    print $main_cur[3] . ";";
                }
            }
            if (strpos($txtRosterColumns, "chkRmk") !== false && $timecard != "yes") {
                if ($csv != "yes") {
                    print "<td bgcolor='" . $bgcolor . "'><a title='Rmk'><font face='Verdana' size='1' color='" . $font . "'>" . $main_cur[6] . "</font></a></td>";
                } else {
                    print $main_cur[6] . ";";
                }
            }
            if (strpos($txtRosterColumns, "chkF1") !== false && $timecard != "yes") {
                if ($csv != "yes") {
                    print "<td bgcolor='" . $bgcolor . "'><a title='F1'><font face='Verdana' size='1' color='" . $font . "'>" . $main_cur[7] . "</font></a></td>";
                } else {
                    print $main_cur[7] . ";";
                }
            }
            if (strpos($txtRosterColumns, "chkF2") !== false && $timecard != "yes") {
                if ($csv != "yes") {
                    print "<td bgcolor='" . $bgcolor . "'><a title='F2'><font face='Verdana' size='1' color='" . $font . "'>" . $main_cur[8] . "</font></a></td>";
                } else {
                    print $main_cur[8] . ";";
                }
            }
            if (strpos($txtRosterColumns, "chkF3") !== false && $timecard != "yes") {
                if ($csv != "yes") {
                    print "<td bgcolor='" . $bgcolor . "'><a title='F3'><font face='Verdana' size='1' color='" . $font . "'>" . $main_cur[9] . "</font></a></td>";
                } else {
                    print $main_cur[9] . ";";
                }
            }
            if (strpos($txtRosterColumns, "chkF4") !== false && $timecard != "yes") {
                if ($csv != "yes") {
                    print "<td bgcolor='" . $bgcolor . "'><a title='F4'><font face='Verdana' size='1' color='" . $font . "'>" . $main_cur[10] . "</font></a></td>";
                } else {
                    print $main_cur[10] . ";";
                }
            }
            if (strpos($txtRosterColumns, "chkF5") !== false && $timecard != "yes") {
                if ($csv != "yes") {
                    print "<td bgcolor='" . $bgcolor . "'><a title='F5'><font face='Verdana' size='1' color='" . $font . "'>" . $main_cur[11] . "</font></a></td>";
                } else {
                    print $main_cur[11] . ";";
                }
            }
            if (strpos($txtRosterColumns, "chkShift") !== false) {
                if ($lstDayNight == "Yes") {
                    if (170000 < $cur[3] && $cur[6] < 170000) {
                        if ($csv != "yes") {
                            print "<td bgcolor='" . $bgcolor . "'><a title='Shift'><font face='Verdana' size='1' color='" . $font . "'>Night</font></a></td>";
                        } else {
                            print "Night;";
                        }
                    } else {
                        if ($csv != "yes") {
                            print "<td bgcolor='" . $bgcolor . "'><a title='Shift'><font face='Verdana' size='1' color='" . $font . "'>Day</font></a></td>";
                        } else {
                            print "Day;";
                        }
                    }
                } else {
                    if ($csv != "yes") {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Shift'><font face='Verdana' size='1' color='" . $font . "'>" . $cur[0] . "</font></a></td>";
                    } else {
                        print $cur[0] . ";";
                    }
                }
            }
            if (strpos($txtRosterColumns, "chkOT1") !== false) {
                if ($csv != "yes") {
                    print "<td bgcolor='" . $bgcolor . "'><a title='Special OT Day 1'><font face='Verdana' size='1' color='" . $font . "'>" . $cur[21] . "</font></a></td>";
                } else {
                    print $cur[21] . ";";
                }
            }
            if (strpos($txtRosterColumns, "chkOT2") !== false) {
                if ($csv != "yes") {
                    print "<td bgcolor='" . $bgcolor . "'><a title='Special OT Day 2'><font face='Verdana' size='1' color='" . $font . "'>" . $cur[22] . "</font></a></td>";
                } else {
                    print $cur[22] . ";";
                }
            }
            if ($csv != "yes") {
                displayDate($cur[1]);
                print "<td bgcolor='" . $bgcolor . "'><a title='Date'><font face='Verdana' size='1' color='" . $font . "'>" . displayDate($cur[1]) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Day'><font face='Verdana' size='1' color='" . $font . "'>" . $cur[9] . "</font></a></td>";
            } else {
                displayDate($cur[1]);
                print displayDate($cur[1]) . ";" . $cur[9] . ";";
            }
            if (strpos($txtRosterColumns, "chkFlag") !== false) {
                if (insertToday() < 20150331 && $cur[8] != "Black" && $cur[8] != "Proxy") {
                    $flag_title_query = "SELECT Title FROM FlagTitle WHERE Flag = '" . $cur[8] . "'";
                    $flag_title_result = selectData($conn, $flag_title_query);
                    if ($csv != "yes") {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Flag'><font face='Verdana' size='1' color='" . $font . "'>" . $flag_title_result[0] . "</font></a></td>";
                    } else {
                        print $flag_title_result[0] . ";";
                    }
                } else {
                    if ($csv != "yes") {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Flag'><font face='Verdana' size='1' color='" . $font . "'>" . $cur[8] . "</font></a></td>";
                    } else {
                        print $cur[8] . ";";
                    }
                }
            }
            if (strpos($txtRosterColumns, "chkEntry") !== false) {
                if ($cur[2] != $cur[3]) {
                    if ($csv != "yes") {
                        displayVirdiTime($cur[2]);
                        print "<td bgcolor='" . $bgcolor . "'><a title='Entry'><font face='Verdana' size='1' color='" . $font . "'>" . displayVirdiTime($cur[2]) . "</font></a></td>";
                    } else {
                        displayVirdiTime($cur[2]);
                        print displayVirdiTime($cur[2]) . ";";
                    }
                } else {
                    if ($csv != "yes") {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Entry'><font face='Verdana' size='1' color='" . $font . "'> </font></a></td>";
                    } else {
                        print ";";
                    }
                }
            }
            if ($lstTerminal == "Yes") {
                $terminal_query = "SELECT tgate.name FROM tgate, " . $_table_name . " WHERE tgate.id = " . $_table_name . ".g_id AND " . $_table_name . ".e_id = '" . $main_cur[0] . "' AND " . $_table_name . ".e_date = '" . $cur[1] . "' AND " . $_table_name . ".e_time = '" . $cur[3] . "' ";
                $terminal_result = selectData($conn, $terminal_query);
                if ($csv != "yes") {
                    print "<td bgcolor='" . $bgcolor . "'><a title='Start'><font face='Verdana' size='1' color='" . $font . "'>" . $terminal_result[0] . "</font></a></td>";
                } else {
                    print $terminal_result[0] . ";";
                }
            }
            if (strpos($txtRosterColumns, "chkStart") !== false) {
                if ($csv != "yes") {
                    displayVirdiTime($cur[3]);
                    print "<td bgcolor='" . $bgcolor . "'><a title='Terminal'><font face='Verdana' size='1' color='" . $font . "'><b>" . displayVirdiTime($cur[3]) . "</b></font></a></td>";
                } else {
                    displayVirdiTime($cur[3]);
                    print displayVirdiTime($cur[3]) . ";";
                }
            }
            if (strpos($txtRosterColumns, "chkBreakOut") !== false) {
                if ($cur[3] != $cur[4]) {
                    if ($csv != "yes") {
                        displayVirdiTime($cur[4]);
                        print "<td bgcolor='" . $bgcolor . "'><a title='Break Out'><font face='Verdana' size='1' color='" . $font . "'>" . displayVirdiTime($cur[4]) . "</font></a></td>";
                    } else {
                        displayVirdiTime($cur[4]);
                        print displayVirdiTime($cur[4]) . ";";
                    }
                } else {
                    if ($csv != "yes") {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Break Out'><font face='Verdana' size='1' color='" . $font . "'> </font></a></td>";
                    } else {
                        print ";";
                    }
                }
            }
            if (strpos($txtRosterColumns, "chkBreakIn") !== false) {
                if ($cur[3] != $cur[5]) {
                    if ($csv != "yes") {
                        displayVirdiTime($cur[5]);
                        print "<td bgcolor='" . $bgcolor . "'><a title='Break In'><font face='Verdana' size='1' color='" . $font . "'>" . displayVirdiTime($cur[5]) . "</font></a></td>";
                    } else {
                        displayVirdiTime($cur[5]);
                        print displayVirdiTime($cur[5]) . ";";
                    }
                } else {
                    if ($csv != "yes") {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Break In'><font face='Verdana' size='1' color='" . $font . "'> </font></a></td>";
                    } else {
                        print ";";
                    }
                }
            }
            if (strpos($txtRosterColumns, "chkClose") !== false) {
                if ($csv != "yes") {
                    displayVirdiTime($cur[6]);
                    print "<td bgcolor='" . $bgcolor . "'><a title='Close'><font face='Verdana' size='1' color='" . $font . "'><b>" . displayVirdiTime($cur[6]) . "</b></font></a></td>";
                } else {
                    displayVirdiTime($cur[6]);
                    print displayVirdiTime($cur[6]) . ";";
                }
            }
            if ($lstTerminal == "Yes") {
                $terminal_query = "SELECT tgate.name FROM tgate, " . $_table_name . " WHERE tgate.id = " . $_table_name . ".g_id AND " . $_table_name . ".e_id = '" . $main_cur[0] . "' AND " . $_table_name . ".e_date = '" . $cur[1] . "' AND " . $_table_name . ".e_time = '" . $cur[6] . "' ";
                $terminal_result = selectData($conn, $terminal_query);
                if ($csv != "yes") {
                    print "<td bgcolor='" . $bgcolor . "'><a title='Terminal'><font face='Verdana' size='1' color='" . $font . "'>" . $terminal_result[0] . "</font></a></td>";
                } else {
                    print $terminal_result[0] . ";";
                }
            }
            if (strpos($txtRosterColumns, "chkExit") !== false) {
                if ($cur[6] != $cur[7]) {
                    if ($csv != "yes") {
                        displayVirdiTime($cur[7]);
                        print "<td bgcolor='" . $bgcolor . "'><a title='Exit'><font face='Verdana' size='1' color='" . $font . "'>" . displayVirdiTime($cur[7]) . "</font></a></td>";
                    } else {
                        displayVirdiTime($cur[7]);
                        print displayVirdiTime($cur[7]) . ";";
                    }
                } else {
                    if ($csv != "yes") {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Exit'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
                    } else {
                        print ";";
                    }
                }
            }
            if (strpos($txtRosterColumns, "chkEarlyIn") !== false) {
                if ($csv != "yes") {
                    round($cur[10] / 60, 2);
                    print "<td bgcolor='" . $bgcolor . "'><a title='Early In'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[10] / 60, 2) . "</font></td>";
                } else {
                    round($cur[10] / 60, 2);
                    print round($cur[10] / 60, 2) . ";";
                }
            }
            if (strpos($txtRosterColumns, "chkLateIn") !== false) {
                if ($cur[23] == 0) {
                    if ($weirdTimeDisplay) {
                        if ($csv != "yes") {
                            getWeirdTime($cur[11]);
                            print "<td bgcolor='" . $bgcolor . "'><a title='Late In'><font face='Verdana' size='1' color='" . $font . "'>" . getWeirdTime($cur[11]) . "</font></td>";
                        } else {
                            getWeirdTime($cur[11]);
                            print getWeirdTime($cur[11]) . ";";
                        }
                    } else {
                        if ($csv != "yes") {
                            round($cur[11] / 60, 2);
                            print "<td bgcolor='" . $bgcolor . "'><a title='Late In'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[11] / 60, 2) . "</font></td>";
                        } else {
                            print "0;";
                        }
                    }
                } else {
                    if ($weirdTimeDisplay) {
                        if ($csv != "yes") {
                            getWeirdTime($cur[11]);
                            print "<td bgcolor='" . $bgcolor . "'><a title='Late In'><font face='Verdana' size='1' color='" . $font . "'><strike>" . getWeirdTime($cur[11]) . "</strike></font></td>";
                        } else {
                            getWeirdTime($cur[11]);
                            print getWeirdTime($cur[11]) . ";";
                        }
                    } else {
                        if ($csv != "yes") {
                            round($cur[11] / 60, 2);
                            print "<td bgcolor='" . $bgcolor . "'><a title='Late In'><font face='Verdana' size='1' color='" . $font . "'><strike>" . round($cur[11] / 60, 2) . "</strike></font></td>";
                        } else {
                            print "0;";
                        }
                    }
                }
                $tc_li = $tc_li + $cur[11];
            }
            if (strpos($txtRosterColumns, "chkLessBreak") !== false) {
                if ($csv != "yes") {
                    round($cur[13] / 60, 2);
                    print "<td bgcolor='" . $bgcolor . "'><a title='Less Break'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[13] / 60, 2) . "</font></td>";
                } else {
                    round($cur[13] / 60, 2);
                    print round($cur[13] / 60, 2) . ";";
                }
            }
            if (strpos($txtRosterColumns, "chkMoreBreak") !== false) {
                if ($cur[25] == 0) {
                    if ($csv != "yes") {
                        round($cur[14] / 60, 2);
                        print "<td bgcolor='" . $bgcolor . "'><a title='More Break'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[14] / 60, 2) . "</font></td>";
                    } else {
                        round($cur[14] / 60, 2);
                        print round($cur[14] / 60, 2) . ";";
                    }
                } else {
                    if ($csv != "yes") {
                        round($cur[14] / 60, 2);
                        print "<td bgcolor='" . $bgcolor . "'><a title='More Break'><font face='Verdana' size='1' color='" . $font . "'><strike>" . round($cur[14] / 60, 2) . "</strike></font></td>";
                    } else {
                        print "0;";
                    }
                }
            }
            if (strpos($txtRosterColumns, "chkEarlyOut") !== false) {
                if ($cur[24] == 0) {
                    if ($weirdTimeDisplay) {
                        if ($csv != "yes") {
                            getWeirdTime($cur[15]);
                            print "<td bgcolor='" . $bgcolor . "'><a title='Early Out'><font face='Verdana' size='1' color='" . $font . "'>" . getWeirdTime($cur[15]) . "</font></td>";
                        } else {
                            getWeirdTime($cur[15]);
                            print getWeirdTime($cur[15]) . ";";
                        }
                    } else {
                        if ($csv != "yes") {
                            round($cur[15] / 60, 2);
                            print "<td bgcolor='" . $bgcolor . "'><a title='Early Out'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[15] / 60, 2) . "</font></td>";
                        } else {
                            round($cur[15] / 60, 2);
                            print round($cur[15] / 60, 2) . ";";
                        }
                    }
                } else {
                    if ($weirdTimeDisplay) {
                        if ($csv != "yes") {
                            getWeirdTime($cur[15]);
                            print "<td bgcolor='" . $bgcolor . "'><a title='Early Out'><font face='Verdana' size='1' color='" . $font . "'><strike>" . getWeirdTime($cur[15]) . "</strike></font></td>";
                        } else {
                            print "0;";
                        }
                    } else {
                        if ($csv != "yes") {
                            round($cur[15] / 60, 2);
                            print "<td bgcolor='" . $bgcolor . "'><a title='Early Out'><font face='Verdana' size='1' color='" . $font . "'><strike>" . round($cur[15] / 60, 2) . "</strike></font></td>";
                        } else {
                            print "0;";
                        }
                    }
                }
                $tc_eo = $tc_eo + $cur[15];
            }
            if (strpos($txtRosterColumns, "chkLateOut") !== false) {
                if ($weirdTimeDisplay) {
                    if ($csv != "yes") {
                        getWeirdTime($cur[16]);
                        print "<td bgcolor='" . $bgcolor . "'><a title='Late Out'><font face='Verdana' size='1' color='" . $font . "'>" . getWeirdTime($cur[16]) . "</font></td>";
                    } else {
                        getWeirdTime($cur[16]);
                        print getWeirdTime($cur[16]) . "\n";
                    }
                } else {
                    if ($csv != "yes") {
                        round($cur[16] / 60, 2);
                        print "<td bgcolor='" . $bgcolor . "'><a title='Late Out'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[16] / 60, 2) . "</font></td>";
                    } else {
                        round($cur[16] / 60, 2);
                        print round($cur[16] / 60, 2) . ";";
                    }
                }
            }
            if (strpos($txtRosterColumns, "chkGrace") !== false) {
                if ($csv != "yes") {
                    round($cur[18] / 60, 2);
                    print "<td bgcolor='" . $bgcolor . "'><a title='Grace'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[18] / 60, 2) . "</font></td>";
                } else {
                    round($cur[18] / 60, 2);
                    print round($cur[18] / 60, 2) . ";";
                }
            }
            if (strpos($txtRosterColumns, "chkNormal") !== false) {
                if ($csv != "yes") {
                    round($cur[17] / 3600, 2);
                    print "<td bgcolor='" . $bgcolor . "'><a title='Normal'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[17] / 3600, 2) . "</font></td>";
                } else {
                    round($cur[17] / 3600, 2);
                    print round($cur[17] / 3600, 2) . ";";
                }
                $tc_n = $tc_n + $cur[17];
            }
            if (strpos($txtRosterColumns, "chkOT") !== false) {
                if ($weirdTimeDisplay) {
                    if ($csv != "yes") {
                        getWeirdTime($cur[19]);
                        print "<td bgcolor='" . $bgcolor . "'><a title='OT'><font face='Verdana' size='1' color='" . $font . "'>" . getWeirdTime($cur[19]) . "</font></td>";
                    } else {
                        getWeirdTime($cur[19]);
                        print getWeirdTime($cur[19]) . ";";
                    }
                } else {
                    if ($csv != "yes") {
                        round($cur[19] / 3600, 2);
                        print "<td bgcolor='" . $bgcolor . "'><a title='OT'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[19] / 3600, 2) . "</font></td>";
                    } else {
                        round($cur[19] / 3600, 2);
                        print round($cur[19] / 3600, 2) . ";";
                    }
                }
                $tc_ot = $tc_ot + $cur[19];
            }
            if (strpos($txtRosterColumns, "chkAppOT") !== false) {
                if ($weirdTimeDisplay) {
                    if ($csv != "yes") {
                        getWeirdTime($cur[20]);
                        print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'>" . getWeirdTime($cur[20]) . "</font></td>";
                        if (getRegister($txtMACAddress, 7) == "25") {
                            getWeirdTime($cur[26]);
                            print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'>" . getWeirdTime($cur[26]) . "</font></td>";
                            getWeirdTime($cur[20] + $cur[26]);
                            print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'>" . getWeirdTime($cur[20] + $cur[26]) . "</font></td>";
                        }
                    } else {
                        getWeirdTime($cur[20]);
                        print getWeirdTime($cur[20]) . ";";
                        if (getRegister($txtMACAddress, 7) == "25") {
                            getWeirdTime($cur[26]);
                            print getWeirdTime($cur[26]) . ";";
                            getWeirdTime($cur[20] + $cur[26]);
                            print getWeirdTime($cur[20] + $cur[26]) . ";";
                        }
                    }
                } else {
                    if ($csv != "yes") {
                        round($cur[20] / 3600, 2);
                        print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[20] / 3600, 2) . "</font></td>";
                        if (getRegister($txtMACAddress, 7) == "25") {
                            round($cur[26] / 60, 2);
                            print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[26] / 60, 2) . "</font></td>";
                            round(($cur[20] + $cur[26]) / 3600, 2);
                            print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'>" . round(($cur[20] + $cur[26]) / 3600, 2) . "</font></td>";
                        }
                    } else {
                        round($cur[20] / 3600, 2);
                        print round($cur[20] / 3600, 2) . ";";
                        if (getRegister($txtMACAddress, 7) == "25") {
                            round($cur[26] / 3600, 2);
                            print round($cur[26] / 3600, 2) . ";";
                            round(($cur[20] + $cur[26]) / 3600, 2);
                            print round(($cur[20] + $cur[26]) / 3600, 2) . ";";
                        }
                    }
                }
                if (getRegister($txtMACAddress, 7) == "25") {
                    $tc_aot = $tc_aot + $cur[20] + $cur[26];
                } else {
                    $tc_aot = $tc_aot + $cur[20];
                }
            }
            if (strpos($txtRosterColumns, "chkTH") !== false) {
                if ($weirdTimeDisplay) {
                    if ($csv != "yes") {
                        getWeirdTime($cur[20] + $cur[17]);
                        print "<td bgcolor='" . $bgcolor . "'><a title='Total Hrs'><font face='Verdana' size='1' color='" . $font . "'>" . getWeirdTime($cur[20] + $cur[17]) . "</font></td>";
                    } else {
                        getWeirdTime($cur[20] + $cur[17]);
                        print getWeirdTime($cur[20] + $cur[17]) . ";";
                    }
                } else {
                    if ($csv != "yes") {
                        round($cur[17] / 3600 + $cur[20] / 3600, 2);
                        print "<td bgcolor='" . $bgcolor . "'><a title='Total Hrs'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[17] / 3600 + $cur[20] / 3600, 2) . "</font></td>";
                    } else {
                        round($cur[17] / 3600 + $cur[20] / 3600, 2);
                        print round($cur[17] / 3600 + $cur[20] / 3600, 2) . ";";
                    }
                }
                $tc_t = $tc_t + $cur[20] + $cur[17];
            }
            if ($txtMACAddress == "40-A8-F0-23-F0-AD") {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='1'>P</font></td>";
                } else {
                    print "P;";
                }
            }
            if ($lstRemark != "" && $prints == "yes") {
                if ($csv != "yes") {
                    print "<td width=50><font face='Verdana' size='2'>&nbsp;</font></td>";
                } else {
                    print ";";
                }
            }
            if ($csv != "yes") {
                print "</tr>";
            } else {
                print "\n";
            }
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
        $shift = " ";
        if ($lstAbsentShift == "Yes") {
            $shift = $last_shift;
        }
        if ($lstDayNight == "Yes") {
            $shift = "Absent";
        }
        if ($sub_count == 0) {
            $count = displayAlterTime($conn, insertDate($txtFrom), insertDate($txtTo), $main_cur[0], $main_cur[1], $main_cur[2], $main_cur[3], $main_cur[5], $main_cur[6], $main_cur[7], $main_cur[8], $main_cur[9], $main_cur[10], $main_cur[11], $column_count_1, $column_count_2, $txtRosterColumns, $prints, $count, $lstImproperClocking, $lstAbsent, $shift, $txtMACAddress, $timecard, $lstTerminal, $lstDB, $csv);
        } else {
            if ($last_date < insertDate($txtTo)) {
                $count = displayAlterTime($conn, $nextDate, insertDate($txtTo), $main_cur[0], $main_cur[1], $main_cur[2], $main_cur[3], $main_cur[5], $main_cur[6], $main_cur[7], $main_cur[8], $main_cur[9], $main_cur[10], $main_cur[11], $column_count_1, $column_count_2, $txtRosterColumns, $prints, $count, $lstImproperClocking, $lstAbsent, $shift, $txtMACAddress, $timecard, $lstTerminal, $lstDB, $csv);
            }
        }
    }
    if ($timecard == "yes") {
        print "<tr>";
        if (strpos($txtRosterColumns, "chkShift") !== false) {
            print "<td bgcolor='" . $bgcolor . "'><a title='Shift'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
        }
        if (strpos($txtRosterColumns, "chkOT1") !== false) {
            print "<td bgcolor='" . $bgcolor . "'><a title='Special OT Day 1'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
        }
        if (strpos($txtRosterColumns, "chkOT2") !== false) {
            print "<td bgcolor='" . $bgcolor . "'><a title='Special OT Day 2'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
        }
        print "<td bgcolor='" . $bgcolor . "'><a title='Date'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Day'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
        if (strpos($txtRosterColumns, "chkFlag") !== false) {
            print "<td bgcolor='" . $bgcolor . "'><a title='Flag'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
        }
        if (strpos($txtRosterColumns, "chkEntry") !== false) {
            print "<td bgcolor='" . $bgcolor . "'><a title='Entry'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
        }
        if (strpos($txtRosterColumns, "chkStart") !== false) {
            print "<td bgcolor='" . $bgcolor . "'><a title='Start'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
        }
        if (strpos($txtRosterColumns, "chkBreakOut") !== false) {
            print "<td bgcolor='" . $bgcolor . "'><a title='Break Out'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
        }
        if (strpos($txtRosterColumns, "chkBreakIn") !== false) {
            print "<td bgcolor='" . $bgcolor . "'><a title='Break In'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
        }
        if (strpos($txtRosterColumns, "chkClose") !== false) {
            print "<td bgcolor='" . $bgcolor . "'><a title='Close'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
        }
        if (strpos($txtRosterColumns, "chkExit") !== false) {
            print "<td bgcolor='" . $bgcolor . "'><a title='Exit'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
        }
        if (strpos($txtRosterColumns, "chkEarlyIn") !== false) {
            print "<td bgcolor='" . $bgcolor . "'><a title='Early In'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></td>";
        }
        if (strpos($txtRosterColumns, "chkLateIn") !== false) {
            if ($weirdTimeDisplay) {
                getWeirdTime($tc_li);
                print "<td bgcolor='" . $bgcolor . "'><a title='Late In'><font face='Verdana' size='1' color='" . $font . "'><b>" . getWeirdTime($tc_li) . "</b></font></td>";
            } else {
                round($tc_li / 60, 2);
                print "<td bgcolor='" . $bgcolor . "'><a title='Late In'><font face='Verdana' size='1' color='" . $font . "'><b>" . round($tc_li / 60, 2) . "</b></font></td>";
            }
        }
        if (strpos($txtRosterColumns, "chkLessBreak") !== false) {
            print "<td bgcolor='" . $bgcolor . "'><a title='Less Break'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></td>";
        }
        if (strpos($txtRosterColumns, "chkMoreBreak") !== false) {
            print "<td bgcolor='" . $bgcolor . "'><a title='More Break'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></td>";
        }
        if (strpos($txtRosterColumns, "chkEarlyOut") !== false) {
            if ($weirdTimeDisplay) {
                getWeirdTime($tc_eo);
                print "<td bgcolor='" . $bgcolor . "'><a title='Early Out'><font face='Verdana' size='1' color='" . $font . "'><b>" . getWeirdTime($tc_eo) . "</b></font></td>";
            } else {
                round($tc_eo / 60, 2);
                print "<td bgcolor='" . $bgcolor . "'><a title='Early Out'><font face='Verdana' size='1' color='" . $font . "'><b>" . round($tc_eo / 60, 2) . "</b></font></td>";
            }
        }
        if (strpos($txtRosterColumns, "chkLateOut") !== false) {
            print "<td bgcolor='" . $bgcolor . "'><a title='Late Out'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></td>";
        }
        if (strpos($txtRosterColumns, "chkGrace") !== false) {
            print "<td bgcolor='" . $bgcolor . "'><a title='Grace'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></td>";
        }
        if (strpos($txtRosterColumns, "chkNormal") !== false) {
            round($tc_n / 3600, 2);
            print "<td bgcolor='" . $bgcolor . "'><a title='Normal'><font face='Verdana' size='1' color='" . $font . "'><b>" . round($tc_n / 3600, 2) . "</b></font></td>";
        }
        if (strpos($txtRosterColumns, "chkOT") !== false) {
            if ($weirdTimeDisplay) {
                getWeirdTime($tc_ot);
                print "<td bgcolor='" . $bgcolor . "'><a title='OT'><font face='Verdana' size='1' color='" . $font . "'><b>" . getWeirdTime($tc_ot) . "</b></font></td>";
            } else {
                round($tc_ot / 3600, 2);
                print "<td bgcolor='" . $bgcolor . "'><a title='OT'><font face='Verdana' size='1' color='" . $font . "'><b>" . round($tc_ot / 3600, 2) . "</b></font></td>";
            }
        }
        if (strpos($txtRosterColumns, "chkAppOT") !== false) {
            if ($weirdTimeDisplay) {
                if (getRegister($txtMACAddress, 7) == "25") {
                    print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'><b>&nbsp;</b></font></td>";
                    print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'><b>&nbsp;</b></font></td>";
                    getWeirdTime($tc_aot);
                    print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'><b>" . getWeirdTime($tc_aot) . "</b></font></td>";
                } else {
                    getWeirdTime($tc_aot);
                    print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'><b>" . getWeirdTime($tc_aot) . "</b></font></td>";
                }
            } else {
                if (getRegister($txtMACAddress, 7) == "25") {
                    print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'><b>&nbsp;</b></font></td>";
                    print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'><b>&nbsp;</b></font></td>";
                    round($tc_aot / 3600, 2);
                    print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'><b>" . round($tc_aot / 3600, 2) . "</b></font></td>";
                } else {
                    round($tc_aot / 3600, 2);
                    print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'><b>" . round($tc_aot / 3600, 2) . "</b></font></td>";
                }
            }
        }
        if (strpos($txtRosterColumns, "chkTH") !== false) {
            if ($weirdTimeDisplay) {
                getWeirdTime($tc_t);
                print "<td bgcolor='" . $bgcolor . "'><a title='Total Hrs'><font face='Verdana' size='1' color='" . $font . "'><b>" . getWeirdTime($tc_t) . "</b></font></td>";
            } else {
                round($tc_t / 3600, 2);
                print "<td bgcolor='" . $bgcolor . "'><a title='Total Hrs'><font face='Verdana' size='1' color='" . $font . "'><b>" . round($tc_t / 3600, 2) . "</b></font></td>";
            }
        }
        if ($txtMACAddress == "40-A8-F0-23-F0-AD") {
            print "<td><font face='Verdana' size='1'>P</font></td>";
        }
        if ($lstRemark != "" && $prints == "yes") {
            print "<td width=50><font face='Verdana' size='2'>&nbsp;</font></td>";
        }
        print "</tr>";
    }
    if ($csv != "yes") {
        print "</table>";
    }
    if ($excel != "yes") {
        print "<p align='center'><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $count . "</b></font>";
        if ($prints != "yes") {
            print "<br><input type='button' value='Print Report' onClick='checkPrint(0)'>&nbsp;&nbsp;<input type='button' value='Print Time Card' onClick='checkPrint(2)'>&nbsp;&nbsp;<input type='button' value='Excel' onClick='checkPrint(1)'>&nbsp;&nbsp;<input type='button' value='CSV' onClick='checkPrint(3)'>";
        }
        print "</p>";
    }
}
if ($csv != "yes") {
    print "</form>";
}
if ($csv != "yes") {
    print "</center></body></html>";
}
function displayAlterTime($conn, $from, $to, $id, $name, $dept, $div, $idno, $rmk, $f1, $f2, $f3, $f4, $f5, $column_count_1, $column_count_2, $txtRosterColumns, $prints, $count, $lstImproperClocking, $lstAbsent, $shift, $txtMACAddress, $timecard, $lstTerminal, $lstDB, $csv)
{
    global $session_variable;
    $table_name = "Access.tenter";
    if ($lstDB == "Archive") {
        $table_name = "AccessArchive.archive_tenter";
    }
    if ($id != "") {
        for ($i = $from; $i <= $to; $i++) {
            if (checkdate(substr($i, 4, 2), substr($i, 6, 2), substr($i, 0, 4))) {
                $start = "";
                $close = "";
                $sgate = "";
                $cgate = "";
                if ($lstImproperClocking == "Yes") {
                    $alter_query = "SELECT " . $table_name . ".e_time, tgroup.Start, tgroup.Close, tgroup.NightFlag, tgroup.WorkMin, tgroup.name, tgate.name FROM " . $table_name . ", tuser, tgate, tgroup WHERE " . $table_name . ".e_id = tuser.id AND " . $table_name . ".g_id = tgate.id AND " . $table_name . ".e_group = tgroup.id AND tgroup.ShiftTypeID = 1 AND tuser.id = " . $id . " AND tgate.exit = 0 AND " . $table_name . ".p_flag = 0 AND " . $table_name . ".e_date = " . $i;
                    $alter_result = selectData($conn, $alter_query);
                    if ($alter_result[0] != "") {
                        $shift = $alter_result[5];
                        if ($alter_result[3] == 0) {
                            $halfTime = getLateTime($i, $alter_result[1], $alter_result[4] / 2);
                            if ($halfTime < $alter_result[0]) {
                                $close = $alter_result[0];
                                $cgate = $alter_result[6];
                            } else {
                                $start = $alter_result[0];
                                $sgate = $alter_result[6];
                            }
                        } else {
                            if ($nightShiftMaxOutTime < $alter_result[0]) {
                                $start = $alter_result[0];
                                $sgate = $alter_result[6];
                            } else {
                                $close = $alter_result[0];
                                $cgate = $alter_result[6];
                            }
                        }
                    }
                }
                if ($start == "") {
                    $start = " ";
                } else {
                    if ($prints == "yes") {
                        $start = displayVirdiTime($start);
                    } else {
                        $start = "<a href='AlterTime.php?act=searchRecord&txtEmployeeCode=" . $id . "&txtFrom=" . displayDate($i) . "&txtTo=" . displayDate($i) . "' title='Click here to view the Improper Clockins' target='_blank'><font face='Verdana' color='#000000'>" . displayVirdiTime($start) . "</font></a>";
                    }
                }
                if ($close == "") {
                    $close = " ";
                } else {
                    if ($prints == "yes") {
                        $close = displayVirdiTime($close);
                    } else {
                        $close = "<a href='AlterTime.php?act=searchRecord&txtEmployeeCode=" . $id . "&txtFrom=" . displayDate($i) . "&txtTo=" . displayDate($i) . "' title='Click here to view the Improper Clockins' target='_blank'><font face='Verdana' color='#000000'>" . displayVirdiTime($close) . "</font></a>";
                    }
                }
                if (!($lstAbsent == "No" && $start == " " && $close == " ")) {
                    if ($timecard != "yes") {
                        if ($csv != "yes") {
                            addZero($id, $_SESSION[$session_variable . "EmployeeCodeLength"]);
                            print "<tr><td><a title='ID'><font face='Verdana' size='1'>" . addZero($id, $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $name . "</font></a></td>";
                        } else {
                            addZero($id, $_SESSION[$session_variable . "EmployeeCodeLength"]);
                            print addZero($id, $_SESSION[$session_variable . "EmployeeCodeLength"]) . ";" . $name . ";";
                        }
                    }
                    if (insertToday() < 20150331 && $timecard != "yes") {
                        if ($csv != "yes") {
                            print "<td><a title='TRML'><font face='Verdana' size='1'> </font></a></td>";
                        } else {
                            print ";";
                        }
                    }
                    $column_count_1_minus = 0;
                    if (strpos($txtRosterColumns, "chkIDColumn") !== false && $timecard != "yes") {
                        if ($csv != "yes") {
                            print "<td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $idno . "</font></a></td>";
                        } else {
                            print $idno . ";";
                        }
                        $column_count_1_minus++;
                    }
                    if (strpos($txtRosterColumns, "chkDept") !== false && $timecard != "yes") {
                        if ($csv != "yes") {
                            print "<td><a title='Dept'><font face='Verdana' size='1'>" . $dept . "</font></a></td>";
                        } else {
                            print $dept . ";";
                        }
                        $column_count_1_minus++;
                    }
                    if (strpos($txtRosterColumns, "chkDiv") !== false && $timecard != "yes") {
                        if ($csv != "yes") {
                            print "<td><a title='Div/Desg'><font face='Verdana' size='1'>" . $div . "</font></a></td>";
                        } else {
                            print $div . ";";
                        }
                        $column_count_1_minus++;
                    }
                    if (strpos($txtRosterColumns, "chkRmk") !== false && $timecard != "yes") {
                        if ($csv != "yes") {
                            print "<td><a title='Rmk'><font face='Verdana' size='1'>" . $rmk . "</font></a></td>";
                        } else {
                            print $rmk . ";";
                        }
                        $column_count_1_minus++;
                    }
                    if (strpos($txtRosterColumns, "chkF1") !== false && $timecard != "yes") {
                        if ($csv != "yes") {
                            print "<td><a title='F1'><font face='Verdana' size='1'>" . $f1 . "</font></a></td>";
                        } else {
                            print $f1 . ";";
                        }
                        $column_count_1_minus++;
                    }
                    if (strpos($txtRosterColumns, "chkF2") !== false && $timecard != "yes") {
                        if ($csv != "yes") {
                            print "<td><a title='F2'><font face='Verdana' size='1'>" . $f2 . "</font></a></td>";
                        } else {
                            print $f2 . ";";
                        }
                        $column_count_1_minus++;
                    }
                    if (strpos($txtRosterColumns, "chkF3") !== false && $timecard != "yes") {
                        if ($csv != "yes") {
                            print "<td><a title='F3'><font face='Verdana' size='1'>" . $f3 . "</font></a></td>";
                        } else {
                            print $f3 . ";";
                        }
                        $column_count_1_minus++;
                    }
                    if (strpos($txtRosterColumns, "chkF4") !== false && $timecard != "yes") {
                        if ($csv != "yes") {
                            print "<td><a title='F4'><font face='Verdana' size='1'>" . $f4 . "</font></a></td>";
                        } else {
                            print $f4 . ";";
                        }
                        $column_count_1_minus++;
                    }
                    if (strpos($txtRosterColumns, "chkF5") !== false && $timecard != "yes") {
                        if ($csv != "yes") {
                            print "<td><a title='F5'><font face='Verdana' size='1'>" . $f5 . "</font></a></td>";
                        } else {
                            print $f5 . ";";
                        }
                        $column_count_1_minus++;
                    }
                    if (strpos($txtRosterColumns, "chkShift") !== false) {
                        if ($csv != "yes") {
                            print "<td><a title='Shift'><font face='Verdana' size='1'>" . $shift . "</font></a></td>";
                        } else {
                            print $shift . ";";
                        }
                        $column_count_1_minus++;
                    }
                    for ($j = 0; $j < $column_count_1 - $column_count_1_minus; $j++) {
                        if ($csv != "yes") {
                            print "<td><font face='Verdana' size='1'> </font></td>";
                        } else {
                            print ";";
                        }
                    }
                    $this_day = getDate(strtotime(substr($i, 6, 2) . "-" . substr($i, 4, 2) . "-" . substr($i, 0, 4)));
                    if ($csv != "yes") {
                        displayDate($i);
                        print "<td><a title='Date'><font face='Verdana' size='1'>" . displayDate($i) . "</font></a></td> <td><a title='Day'><font face='Verdana' size='1'>" . $this_day["weekday"] . "</font></a></td>";
                    } else {
                        displayDate($i);
                        print displayDate($i) . ";" . $this_day["weekday"] . ";";
                    }
                    $column_count_2_minus = 0;
                    if (strpos($txtRosterColumns, "chkFlag") !== false) {
                        if ($csv != "yes") {
                            print "<td><font face='Verdana' size='2'> </font></td>";
                        } else {
                            print ";";
                        }
                        $column_count_2_minus++;
                    }
                    if (strpos($txtRosterColumns, "chkEntry") !== false) {
                        if ($csv != "yes") {
                            print "<td><font face='Verdana' size='2'> </font></td>";
                        } else {
                            print ";";
                        }
                        $column_count_2_minus++;
                    }
                    if (strpos($txtRosterColumns, "chkStart") !== false) {
                        if ($lstTerminal == "Yes") {
                            if ($csv != "yes") {
                                print "<td><font face='Verdana' size='1'>" . $sgate . "</font></td>";
                            } else {
                                print $sgate . ";";
                            }
                        }
                        if ($csv != "yes") {
                            print "<td><font face='Verdana' size='1'><b>" . $start . "</b></font></td>";
                        } else {
                            print $start . ";";
                        }
                        $column_count_2_minus++;
                    }
                    if (strpos($txtRosterColumns, "chkBreakOut") !== false) {
                        if ($csv != "yes") {
                            print "<td><font face='Verdana' size='2'> </font></td>";
                        } else {
                            print ";";
                        }
                        $column_count_2_minus++;
                    }
                    if (strpos($txtRosterColumns, "chkBreakIn") !== false) {
                        if ($csv != "yes") {
                            print "<td><font face='Verdana' size='2'> </font></td>";
                        } else {
                            print ";";
                        }
                        $column_count_2_minus++;
                    }
                    if (strpos($txtRosterColumns, "chkClose") !== false) {
                        if ($csv != "yes") {
                            print "<td><font face='Verdana' size='1'><b>" . $close . "</b></font></td>";
                        } else {
                            print $close . ";";
                        }
                        if ($lstTerminal == "Yes") {
                            if ($csv != "yes") {
                                print "<td><font face='Verdana' size='1'><b>" . $cgate . "</b></font></td>";
                            } else {
                                print $cgate . ";";
                            }
                        }
                        $column_count_2_minus++;
                    }
                    if ($txtMACAddress == "40-A8-F0-23-F0-AD") {
                        if ($csv != "yes") {
                            print "<td><font face='Verdana' size='1'>A</font></td>";
                        } else {
                            print "A;";
                        }
                        $column_count_2_minus++;
                    }
                    for ($j = 0; $j <= $column_count_2 - ($column_count_2_minus + 3); $j++) {
                        if ($csv != "yes") {
                            print "<td><font face='Verdana' size='1'> </font></td>";
                        } else {
                            print ";";
                        }
                    }
                    if ($csv != "yes") {
                        print "</tr>";
                    } else {
                        print "\n";
                    }
                    $count++;
                }
            }
        }
    }
    return $count;
}
function displayReportHeader($session_variable, $txtRosterColumns, $lstRemark, $prints, $txtMACAddress, $timecard)
{
    print "<tr>";
    if ($timecard != "yes") {
        print "<td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td>";
    }
    if (insertToday() < 20150331 && $timecard != "yes") {
        print "<td><font face='Verdana' size='2'>TRML</font></td>";
    }
    if (strpos($txtRosterColumns, "chkIDColumn") !== false && $timecard != "yes") {
        print "<td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td>";
        $column_count_1++;
    }
    if (strpos($txtRosterColumns, "chkDept") !== false && $timecard != "yes") {
        print "<td><font face='Verdana' size='2'>Dept</font></td>";
        $column_count_1++;
    }
    if (strpos($txtRosterColumns, "chkDiv") !== false && $timecard != "yes") {
        print "<td><font face='Verdana' size='2'>Div/Desg</font></td>";
        $column_count_1++;
    }
    if (strpos($txtRosterColumns, "chkRmk") !== false && $timecard != "yes") {
        print "<td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "RemarkColumnName"] . "</font></td>";
        $column_count_1++;
    }
    if (strpos($txtRosterColumns, "chkF1") !== false && $timecard != "yes") {
        print "<td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "F1"] . "</font></td>";
        $column_count_1++;
    }
    if (strpos($txtRosterColumns, "chkF2") !== false && $timecard != "yes") {
        print "<td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "F2"] . "</font></td>";
        $column_count_1++;
    }
    if (strpos($txtRosterColumns, "chkF3") !== false && $timecard != "yes") {
        print "<td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "F3"] . "</font></td>";
        $column_count_1++;
    }
    if (strpos($txtRosterColumns, "chkF4") !== false && $timecard != "yes") {
        print "<td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "F4"] . "</font></td>";
        $column_count_1++;
    }
    if (strpos($txtRosterColumns, "chkF5") !== false && $timecard != "yes") {
        print "<td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "F5"] . "</font></td>";
        $column_count_1++;
    }
    if (strpos($txtRosterColumns, "chkShift") !== false) {
        print "<td><font face='Verdana' size='2'>Shift</font></td>";
        $column_count_1++;
    }
    if (strpos($txtRosterColumns, "chkOT1") !== false) {
        print "<td><font face='Verdana' size='2'>OT 1</font></td>";
        $column_count_1++;
    }
    if (strpos($txtRosterColumns, "chkOT2") !== false) {
        print "<td><font face='Verdana' size='2'>OT 2</font></td>";
        $column_count_1++;
    }
    print "<td><font face='Verdana' size='2'>Date</font></td> <td><font face='Verdana' size='2'>Day</font></td>";
    if (strpos($txtRosterColumns, "chkFlag") !== false) {
        print "<td><font face='Verdana' size='2'>Flag</font></td>";
        $column_count_2++;
    }
    if (strpos($txtRosterColumns, "chkEntry") !== false) {
        print "<td><font face='Verdana' size='2'>Entry</font></td>";
        $column_count_2++;
    }
    if (strpos($txtRosterColumns, "chkStart") !== false) {
        print "<td><font face='Verdana' size='2'><b>Start</b></font></td>";
        $column_count_2++;
    }
    if (strpos($txtRosterColumns, "chkBreakOut") !== false) {
        print "<td><font face='Verdana' size='2'>BreakOut</font></td>";
        $column_count_2++;
    }
    if (strpos($txtRosterColumns, "chkBreakIn") !== false) {
        print "<td><font face='Verdana' size='2'>BreakIn</font></td>";
        $column_count_2++;
    }
    if (strpos($txtRosterColumns, "chkClose") !== false) {
        print "<td><font face='Verdana' size='2'><b>Close</b></font></td>";
        $column_count_2++;
    }
    if (strpos($txtRosterColumns, "chkExit") !== false) {
        print "<td><font face='Verdana' size='2'>Exit</font></td>";
        $column_count_2++;
    }
    if (strpos($txtRosterColumns, "chkEarlyIn") !== false) {
        print "<td><font face='Verdana' size='2'>Early In <br>(Min)</font></td>";
        $column_count_2++;
    }
    if (strpos($txtRosterColumns, "chkLateIn") !== false) {
        print "<td><font face='Verdana' size='2'>Late In <br>(Min)</font></td>";
        $column_count_2++;
    }
    if (strpos($txtRosterColumns, "chkLessBreak") !== false) {
        print "<td><font face='Verdana' size='2'>Less Break <br>(Min)</font></td>";
        $column_count_2++;
    }
    if (strpos($txtRosterColumns, "chkMoreBreak") !== false) {
        print "<td><font face='Verdana' size='2'>More Break <br>(Min)</font></td>";
        $column_count_2++;
    }
    if (strpos($txtRosterColumns, "chkEarlyOut") !== false) {
        print "<td><font face='Verdana' size='2'>Early Out <br>(Min)</font></td>";
        $column_count_2++;
    }
    if (strpos($txtRosterColumns, "chkLateOut") !== false) {
        print "<td><font face='Verdana' size='2'>Late Out <br>(Min)</font></td>";
        $column_count_2++;
    }
    if (strpos($txtRosterColumns, "chkGrace") !== false) {
        print "<td><font face='Verdana' size='2'>Grace <br>(Min)</font></td>";
        $column_count_2++;
    }
    if (strpos($txtRosterColumns, "chkNormal") !== false) {
        print "<td><font face='Verdana' size='2'>Normal <br>(Hrs)</font></td>";
        $column_count_2++;
    }
    if (strpos($txtRosterColumns, "chkOT") !== false) {
        print "<td><font face='Verdana' size='2'>OT <br>(Hrs)</font></td>";
        $column_count_2++;
    }
    if (strpos($txtRosterColumns, "chkAppOT") !== false) {
        print "<td><font face='Verdana' size='2'>App OT <br>(Hrs)</font></td>";
        $column_count_2++;
        if (getRegister($txtMACAddress, 7) == "25") {
            print "<td><font face='Verdana' size='2'>App Late In<br>(Min)</font></td>";
            print "<td><font face='Verdana' size='2'>Total App OT <br>(Hrs)</font></td>";
            $column_count_2++;
        }
    }
    if (strpos($txtRosterColumns, "chkTH") !== false) {
        print "<td><font face='Verdana' size='2'>Total <br>(Hrs)</font></td>";
        $column_count_2++;
    }
    if ($lstRemark != "" && $prints == "yes") {
        print "<td width=50><font face='Verdana' size='2'>Remarks</font></td>";
        $column_count_2++;
    }
    if ($txtMACAddress == "40-A8-F0-23-F0-AD") {
        print "<td><font face='Verdana' size='2'>P/A</font></td>";
        $column_count_2++;
    }
    print "</tr>";
}

?>