<?php

ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "31";
set_time_limit(0);
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=ShiftRoster.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$iconn = openIConnection();
$act = $_GET["act"];
if ($act == "") {
    $act = $_POST["act"];
}
$prints = $_GET["prints"];
$excel = $_GET["excel"];
$message = $_GET["message"];
if ($message == "") {
    $message = "Shift Roster";
}
$lstSetFlag = $_POST["lstSetFlag"];
$lstShift = $_POST["lstShift"];
$lstDepartment = $_POST["lstDepartment"];
//echo $lstDeptTerminal = $_POST["lstDeptTerminal"];
$lstDeptTerminal = 2;
$lstDivision = $_POST["lstDivision"];
$lstEmployeeIDFrom = $_POST["lstEmployeeIDFrom"];
$lstEmployeeIDTo = $_POST["lstEmployeeIDTo"];
$txtEmployee = $_POST["txtEmployee"];
$txtEmployeeCode = $_POST["txtEmployeeCode"];
$txtFrom = $_POST["txtFrom"];
$txtTo = $_POST["txtTo"];
if ($txtFrom == "") {
    $txtFrom = displayDate(getNextDay(insertToday(), 1));
}
if ($txtTo == "") {
    if (substr(insertToday(), 6, 2) < 28) {
        $txtTo = "28/" . substr(displayToday(), 3, 7);
    } else {
        $txtTo = displayDate(getNextDay(insertToday(), 1));
    }
}
$txtRemark = $_POST["txtRemark"];
$txtPhone = $_POST["txtPhone"];
$txtSNo = $_POST["txtSNo"];
$lstDisplayFlag = $_POST["lstDisplayFlag"];
if ($lstDisplayFlag == "") {
    $lstDisplayFlag = "Yes";
}
$lstSetShift = $_POST["lstSetShift"];
$lstEmployeeStatus = "";
if (isset($_POST["lstEmployeeStatus"])) {
    $lstEmployeeStatus = $_POST["lstEmployeeStatus"];
} else {
    $lstEmployeeStatus = "ACT";
}
$lstSort = $_POST["lstSort"];
$txtRemarks = $_POST["txtRemarks"];
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
$txtOTH = $_POST["txtOTH"];
$lstSetOT = $_POST["lstSetOT"];
if ($txtOTH == "") {
    $txtOTH = "0";
}
if ($act == 'saveFlag') {
    /*     * ********************Start Pre Flag*************************** */
    if ($lstSetShift == "") {
        $query = "SELECT id from tgroup WHERE NightFlag = 0 AND name = 'OFF' ";
        $result = selectData($conn, $query);
        $lstSetShift = $result[0];
        if ($lstSetShift == "") {
            $query = "SELECT id from tgroup WHERE NightFlag = 0 AND id > 1 ";
            $result = selectData($conn, $query);
            $lstSetShift = $result[0];
        }
    }
    $tot = $_POST["txtTot"];
    $insert_flag = false;
    for ($i = 0; $i <= $tot; $i++) {
        if ($_POST["chk" . $i] != "") {
            $insert_flag = false;
            if ($lstSetFlag == "Black" || $lstSetFlag == "Proxy" || $lstSetFlag == "") {
                $insert_flag = true;
            } else {
                $query = "SELECT " . $lstSetFlag . " FROM EmployeeFlag WHERE EmployeeID = " . $_POST["txhID" . $i];
                $result = selectData($conn, $query);
                $max_flag_limit = $result[0];
                if ($max_flag_limit == "") {
                    $query = "INSERT INTO EmployeeFlag (EmployeeID) VALUES (" . $_POST["txhID" . $i] . ")";
                    updateData($conn, $query, true);
                    $max_flag_limit = 365;
                }
                if ($flagLimitType == "Jan 01") {
                    $query = "SELECT COUNT(FlagDayRotationID) FROM FlagDayRotation WHERE e_id = " . $_POST["txhID" . $i] . " AND Flag = '" . $lstSetFlag . "' AND e_date >= " . substr(insertToday(), 0, 4) . "0101 AND e_date <= " . substr(insertToday(), 0, 4) . "1231 AND RecStat = 0";
                } else {
                    $query = "SELECT COUNT(FlagDayRotationID) FROM FlagDayRotation, tuser WHERE tuser.id = FlagDayRotation.e_id AND FlagDayRotation.e_id = " . $_POST["txhID" . $i] . " AND FlagDayRotation.Flag = '" . $lstSetFlag . "' AND FlagDayRotation.e_date >= CONCAT(" . substr(insertToday(), 0, 4) . ", SUBSTR(tuser.datelimit, 6, 4)) AND FlagDayRotation.e_date < CONCAT(" . (substr(insertToday(), 0, 4) * 1 + 1) . ", SUBSTR(tuser.datelimit, 6, 4)) AND FlagDayRotation.RecStat = 0";
                }
                $result = selectData($conn, $query);
                $pre_flag_count = $result[0];
                if ($pre_flag_count == "") {
                    $pre_flag_count = 0;
                }
                if ($flagLimitType == "Jan 01") {
                    $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster WHERE EmployeeID = " . $_POST["txhID" . $i] . " AND Flag = '" . $lstSetFlag . "' AND ADate >= " . substr(insertToday(), 0, 4) . "0101 AND ADate <= " . substr(insertToday(), 0, 4) . "1231 ";
                } else {
                    $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND AttendanceMaster.EmployeeID = " . $_POST["txhID" . $i] . " AND AttendanceMaster.Flag = '" . $lstSetFlag . "' AND AttendanceMaster.ADate >= CONCAT(" . substr(insertToday(), 0, 4) . ", SUBSTR(tuser.datelimit, 6, 4)) AND AttendanceMaster.ADate < CONCAT(" . (substr(insertToday(), 0, 4) * 1 + 1) . ", SUBSTR(tuser.datelimit, 6, 4)) ";
                }
                $result = selectData($conn, $query);
                $post_flag_count = $result[0];
                if ($post_flag_count == "") {
                    $post_flag_count = 0;
                }
                if ($pre_flag_count + $post_flag_count < $max_flag_limit) {
                    $insert_flag = true;
                }
            }
            if ($insert_flag) {
                $query = "";
                if ($lstRotateShift == "Yes") {
                    $query = "INSERT INTO FlagDayRotation (e_id, e_date, g_id, Flag, Rotate, Remark, OT, group_id, OTH) VALUES (" . $_POST["txhID" . $i] . ", " . $_POST["chk" . $i] . ", " . $lstDeptTerminal . ", '" . $lstSetFlag . "', 1, '" . $txtRemarks . "', '" . $lstSetOT . "', '" . $lstSetShift . "', '" . $txtOTH . "')";
                } else { 
                    $query = "INSERT INTO FlagDayRotation (e_id, e_date, g_id, Flag, Rotate, Remark, OT, group_id, OTH) VALUES (" . $_POST["txhID" . $i] . ", " . $_POST["chk" . $i] . ", " . $lstDeptTerminal . ", '" . $lstSetFlag . "', 0, '" . $txtRemarks . "', '" . $lstSetOT . "', '" . $lstSetShift . "', '" . $txtOTH . "')";
                }
                if (!updateIData($iconn, $query, true)) {
                    if ($lstRotateShift == "Yes") {
                        $query = "UPDATE FlagDayRotation SET Flag = '" . $lstSetFlag . "', Rotate = '1', Remark = '" . $txtRemarks . "', OT = '" . $lstSetOT . "', group_id = '" . $lstSetShift . "', OTH = '" . $txtOTH . "' WHERE e_id = '" . $_POST["txhID" . $i] . "' AND e_date = '" . $_POST["chk" . $i] . "' AND RecStat = 0 ";
                    } else {
                        $query = "UPDATE FlagDayRotation SET Flag = '" . $lstSetFlag . "', Rotate = '0', Remark = '" . $txtRemarks . "', OT = '" . $lstSetOT . "', group_id = '" . $lstSetShift . "', OTH = '" . $txtOTH . "' WHERE e_id = '" . $_POST["txhID" . $i] . "' AND e_date = '" . $_POST["chk" . $i] . "' AND RecStat = 0 ";
                    }
                    if (!updateIData($jconn, $query, true)) {
                        $insert_flag = false;
                    }
                }
                if ($insert_flag) {
                    $text = "Pre Flagged ID: " . $_POST["txhID" . $i] . " for Date: " . displayDate($_POST["chk" . $i]) . " with Flag: " . $lstSetFlag . ", Rotate Shift: " . $lstRotateShift . ", OT Type: " . $lstSetOT . ", Shift ID: " . $lstSetShift . ", OT Hrs: " . $txtOTH;
                    $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                    updateIData($iconn, $query, true);
                }
            }
        }
    }
    $message = "Successfully Scheduled the Selected Date(s) for the Selected Employee(s) to be marked with Shift " . $lstSetShift;
    $act = "searchRecord";
    /*     * ****************END*************************** */
}
if ($act == "saveRecord") {
    $tot = $_POST["txtTot"];
    for ($i = 0; $i <= $tot; $i++) {
        if ($_POST["chk" . $i] != "") {
            $query = "";
            $query = "INSERT INTO ShiftRoster (e_id, e_date, e_group) VALUES (" . $_POST["txhID" . $i] . ", " . $_POST["chk" . $i] . ", " . $lstSetShift . ")";
            updateIData($iconn, $query, true);
            $text = "Set Shift Roster For ID: " . $_POST["txhID" . $i] . " for Date: " . displayDate($_POST["chk" . $i]) . " with Shift: " . $lstSetShift;
            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
            updateIData($iconn, $query, true);
        }
    }
    $message = "Successfully Scheduled the Selected Date(s) for the Selected Employee(s) to be marked with Shift " . $lstSetShift;
    $act = "searchRecord";
} else {
    if ($act == "generateRoster") {
        $query = "SELECT tuser.id, tuser.group_id FROM tuser WHERE tuser.id > 0 " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
        if ($lstShift != "") {
            $query = $query . " AND tgroup.id = " . $lstShift;
        }
        $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
        $query = $query . employeeStatusQuery($lstEmployeeStatus);
    }
}
print "<html><title>Shift Roster</title>";
if ($prints != "yes") {
    print "<body>";
} else {
    if ($excel != "yes") {
        print "<body onLoad='javascript:window.print()'>";
    } else {
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=ShiftRoster.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        print "<body>";
    }
}
print "<center>";
if ($excel != "yes") {
    displayHeader($prints, true, false);
}
print "<center>";
if ($prints != "yes") {
    displayLinks($current_module, $userlevel);
}
print "</center>";
if ($excel != "yes") {
    print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b> </font></p>";
    if ($prints != "yes") {
        print "<table width='800' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
        print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Select ONE or MORE options and click 'Search Record'</b></font></td></tr>";
    } else {
        print "<table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
        print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Selected Options</b></font></td></tr>";
    }
    print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='ShiftRoster.php'><input type='hidden' name='act' value='searchRecord'><tr>";
    $query = "SELECT id, name from tgroup WHERE ShiftTypeID = 1 ORDER BY name";
    displayList("lstShift", "Current Shift: ", $lstShift, $prints, $conn, $query, "", "25%", "75%");
    print "</tr>";
    displaySearchFields($conn, $prints, $session_variable, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $lstEmployeeName, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
    print "<tr>";
    print "<td colspan='2'><table width='100%' cellspacing='0' cellpadding='0' border='0'><tr>";
    displayTextbox("txtFrom", "Date From <font size='1'>(DD/MM/YYYY)</font>: ", $txtFrom, $prints, 12, "25%", "30%");
    if ($prints == "yes") {
        displayTextbox("lstDisplayFlag", "Display Flags: ", $lstDisplayFlag, $prints, 12, "25%", "10%");
    } else {
        print "<td align='right' width='20%'><font face='Verdana' size='2'>Display Flags</font>: </td> <td width='25%'><select name='lstDisplayFlag' class='form-control'><option selected value='" . $lstDisplayFlag . "'>" . $lstDisplayFlag . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select></td>";
    }
    print "</tr></table></td>";
    print "</tr>";
    print "<tr>";
    displayTextbox("txtTo", "Date To <font size='1'>(DD/MM/YYYY)</font>: ", $txtTo, $prints, 12, "25%", "75%");
    print "</tr>";
    if ($prints != "yes") {
        print "<tr>";
        displayEmployeeStatus($conn, "lstEmployeeStatus", $lstEmployeeStatus, "", "");
        print "</tr>";
        print "<tr>";
        $array = array(array("tuser.id", "Employee Code"), array("tuser.name, tuser.id", "Employee Name - Code"), array("tuser.dept, tuser.id", "Dept - Code"), array("tuser.company, tuser.dept, tuser.id", "Div/Desg - Dept - Code"), array("tuser.company, tuser.dept, tuser.group_id, tuser.id", "Div - Dept - Current Shift - Code"));
        displaySort($array, $lstSort, 5);
        print "</tr>";
        print "<tr><td>&nbsp;</td><td><input name='btSearch' type='submit' value='Search Record' class='btn btn-primary'>&nbsp;&nbsp;<input type='button' value='Excel' onClick='checkPrint(1)' class='btn btn-primary'>&nbsp;&nbsp;<input type='button' value='CSV' onClick='checkPrint(3)' class='btn btn-primary'></td></tr></td></tr>";
    }
    print "</table><br>";
}
if ($act == "searchRecord") {
    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, 0, 0, 0, 0, 0, tuser.idno, tuser.remark, '', tuser.group_id, tuser.OT1 FROM tuser, tgroup WHERE tuser.group_id = tgroup.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
    if ($lstShift != "") {
        $query = $query . " AND tgroup.id = " . $lstShift;
    }
    $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
    $query = $query . employeeStatusQuery($lstEmployeeStatus);
    $fromTime = mktime(0, 0, 0, substr(insertDate($txtFrom), 4, 2), substr(insertDate($txtFrom), 6, 2), substr(insertDate($txtFrom), 0, 4));
    $toTime = mktime(0, 0, 0, substr(insertDate($txtTo), 4, 2), substr(insertDate($txtTo), 6, 2), substr(insertDate($txtTo), 0, 4));
    $dayCount = ($toTime - $fromTime) / 86400;
    $dayCount = round($dayCount, 0);
    $dayCount++;
    $query = $query . " ORDER BY " . $lstSort;
    if ($prints != "yes") {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800'>";
    } else {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%'>";
    }
    print "<tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Current Shift</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> ";
    for ($i = 0; $i < $dayCount; $i++) {
        $next = strtotime(substr($txtFrom, 0, 2) . "-" . substr($txtFrom, 3, 2) . "-" . substr($txtFrom, 6, 4) . " + " . $i . " day");
        $a = getDate($next);
        print "<td><font face='Verdana' size='2'>" . $a["mday"] . "</font></td>";
    }
    print "</tr>";
    print "<tr>";
    for ($i = 0; $i < 8; $i++) {
        print "<td><font face='Verdana' size='1'>&nbsp;</font></td>";
    }
    for ($i = 0; $i < $dayCount; $i++) {
        $next = strtotime(substr($txtFrom, 0, 2) . "-" . substr($txtFrom, 3, 2) . "-" . substr($txtFrom, 6, 4) . " + " . $i . " day");
        $a = getDate($next);
        substr($a["weekday"], 0, 1);
        print "<td><a title='" . $a["weekday"] . "'><font face='Verdana' size='2'>" . substr($a["weekday"], 0, 1) . "</font></a></td>";
    }
    print "</tr>";
    print "<tr>";
    for ($i = 0; $i < 7; $i++) {
        print "<td><font face='Verdana' size='1'>&nbsp;</font></td>";
    }
    if ($prints != "yes") {
        print "<td bgcolor='#F0F0F0' align='center'><font face='Verdana' size='2'><b>All</b></font>";
    } else {
        print "<td bgcolor='#F0F0F0' align='center'><font face='Verdana' size='2'><b>&nbsp;</b></font>";
    }
    for ($i = 0; $i < $dayCount; $i++) {
        print "<td bgcolor='#F0F0F0'><font face='Verdana' size='1'>";
        if ($prints != "yes") {
            print "<input type='checkbox' onClick='javascript:checkAllDate(" . $i . ", " . $dayCount . ", this)'>";
        } else {
            print "&nbsp;";
        }
        print "</font></td>";
    }
    print "</tr>";
    $result = mysqli_query($conn, $query);
    $count = 0;
    $tot = 0;
    $subc = 0;
    $eid = "";
    $txtDate = insertDate($txtFrom);
    $txtLastDate = insertDate($txtTo);
    $data9 = "";
    $data12 = "";
    $data14 = "";
    $data0 = "";
    $row_count = 0;
    while ($cur = mysqli_fetch_row($result)) {
        $tot++;
        if ($cur[3] == "") {
            $cur[3] = "&nbsp;";
        }
        if ($cur[10] == "") {
            $cur[10] = "&nbsp;";
        }
        if ($cur[11] == "") {
            $cur[11] = "&nbsp;";
        }
        if ($eid != $cur[0]) {
            if ($count != 0) {
                $this_date = $txtDate;
                $this_day = "";
                for ($i = $subc; $i < $dayCount; $i++) {
                    $tot++;
                    if ($prints != "yes") {
                        displayDate($this_date);
                        print "<td><a title='" . displayDate($this_date) . " (" . $this_day . ")'><input type='checkbox' name='chk" . $tot . "' value='" . $this_date . "' id='chk-" . $tot . "'><input type='hidden' name='txhID" . $tot . "' value='" . $data0 . "'> <input type='hidden' name='txhDeptClocking" . $tot . "' value='" . $data14 . "'></a> </td>";
                        $next = strtotime(substr($this_date, 6, 2) . "-" . substr($this_date, 4, 2) . "-" . substr($this_date, 0, 4) . " + 1 day");
                        $a = getDate($next);
                        $m = $a["mon"];
                        if ($m < 10) {
                            $m = "0" . $m;
                        }
                        $d = $a["mday"];
                        if ($d < 10) {
                            $d = "0" . $d;
                        }
                        $this_date = $a["year"] . $m . $d;
                        $this_day = $a["weekday"];
                    } else {
                        print "<td><font size='1'>&nbsp;</font></td>";
                    }
                }
                print "</tr>";
                $row_count++;
            }
            addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
            print "<tr><td><a title='ID'><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'><font face='Verdana' size='1'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $cur[1] . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $cur[10] . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $cur[2] . "</font></a></td> <td><a title='Div/Desg'><font face='Verdana' size='1'>" . $cur[3] . "</font></a></td> <td><a title='Rmk'><font face='Verdana' size='1'>" . $cur[11] . "</font></a></td> <td><a title='Current Shift'><font face='Verdana' size='1'>";
            $todayDate = date('Ymd');
            $shiftQuery = "select shiftroster.e_id,shiftroster.e_group,tgroup.name from shiftroster,tgroup where e_date='$todayDate' AND shiftroster.e_group=tgroup.id";
            $shiftResult = mysqli_query($conn, $shiftQuery);
            while ($curShift = mysqli_fetch_row($shiftResult)) {
                if ($curShift[0] == $cur[0]) {
                    echo $curShift[2];
                }
            }
            print "</font></a></td> <td bgcolor='#F0F0F0'>";
            if ($prints != "yes") {
                print "<a title='Check All'><input type='checkbox' onClick='javascript:checkAllEmployee(" . ($tot + 1) . ", " . $dayCount . ", this)'></a>";
            } else {
                print "<font face='Verdana' size='1'>&nbsp;</font>";
            }
            print "</td>";
            $eid = $cur[0];
            $subc = 0;
            $oc = 0;
            $uc = 0;
            $nc = 0;
            $ac = 0;
            $txtDate = insertDate($txtFrom);
        }
        for ($i = 0; $i < $dayCount; $i++) {
            $tot++;
            $subc++;
            $count++;
            $query = "SELECT '', '', '', '', '', AttendanceMaster.group_min, AttendanceMaster.Normal, AttendanceMaster.Grace, AttendanceMaster.Overtime, AttendanceMaster.ADate, '', '', tgroup.name, '' FROM AttendanceMaster, tgroup WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.EmployeeID = " . $cur[0] . " AND AttendanceMaster.ADate = " . $txtDate;
            $sub_result = mysqli_query($conn, $query);
            $sub_cur = mysqli_fetch_row($sub_result);
            $query = "SELECT tgroup.name, tgroup.id FROM tgroup, ShiftRoster WHERE ShiftRoster.e_group = tgroup.id AND ShiftRoster.e_id = " . $cur[0] . " AND ShiftRoster.e_date = " . $txtDate;
            $shift_result = selectData($conn, $query);
//            echo $query = "SELECT Flag, Rotate, OT, FlagDayRotationID FROM FlagDayRotation WHERE e_id = " . $cur[0] . " AND e_date = " . $txtDate;
//            echo $query = "SELECT flagdayrotation.Flag, flagdayrotation.Rotate, flagdayrotation.OT, flagdayrotation.FlagDayRotationID,flagtitle.Title FROM flagdayrotation,flagtitle WHERE flagtitle.Flag=flagdayrotation.Flag AND flagdayrotation.e_id = " . $cur[0] . " AND flagdayrotation.e_date = " . $txtDate;
            $query = "SELECT flagdayrotation.Flag, flagdayrotation.Rotate,flagdayrotation.OT,flagdayrotation.FlagDayRotationID,flagtitle.Title,
                    CASE
                      WHEN flagdayrotation.Flag = '' AND flagdayrotation.OT != '' THEN flagdayrotation.OT
                      WHEN flagdayrotation.Flag != '' AND flagdayrotation.OT = '' THEN flagdayrotation.Flag
                      WHEN flagdayrotation.Flag != '' AND flagdayrotation.OT != '' THEN flagdayrotation.OT
                      ELSE '' -- Use this for cases where neither condition is met, adjust as needed
                    END AS DisplayValue
                    FROM flagdayrotation
                    LEFT JOIN flagtitle ON flagtitle.Flag = flagdayrotation.Flag
                    WHERE flagdayrotation.e_id = " . $cur[0] . " AND flagdayrotation.e_date = " . $txtDate;
            $flag_result = selectData($iconn, $query);

            if ($sub_cur[9] == $txtDate || $shift_result[0] != "" || $flag_result[3] != "") { 
                if ($sub_cur[9] == $txtDate) {
                    print "<td '" . $sub_cur[12] . "'>";
                } else { 
                    print "<td><font face='Verdana' size='1'>" . $flag_result[5] ."</font>";
                }
                if ($sub_cur[12] == "" && $shift_result[0] == "" && $flag_result[0] == "" && $flag_result[2] == "" && $flag_result[3] == "") {
                    if ($prints == "yes") {
                        print "<font face='Verdana' size='1'>&nbsp;</font>";
                    } else {
                        displayDate($txtDate);
                        print "<td><a title='" . displayDate($txtDate) . "'><input type='checkbox' name='chk" . $tot . "' value='" . $txtDate . "' id='chk-" . $tot . "'> <input type='hidden' name='txhID" . $tot . "' value='" . $cur[0] . "'> <input type='hidden' name='txhDeptClocking" . $tot . "' value='" . $cur[14] . "'></a></td>";
                    }
                } else {
                    if ($sub_cur[12] == "") {
                        if ($prints == "yes") {
                            print "<font face='Verdana' size='1' color='#000000'>" . $shift_result[0] . "</font>";
                        } else {
                            displayDate($txtDate);
                            print "<a title='" . displayDate($txtDate) . "' onClick='javascript:ajaxFunction(" . $cur[0] . ", " . $txtDate . ", " . $shift_result[1] . ", " . $count . ")'><u><font face='Verdana' size='1' color='#000000'><span id='txtSPAN" . $count . "'>" . $shift_result[0] . "</span></font></u></a>";
                        }
                    } else {
                        print "<font face='Verdana' size='1' color='#000000'>" . $sub_cur[12] . "</font>";
                    }
                }
                print "</td>";
            } else { 
                if ($prints != "yes") {
                    displayDate($txtDate);
                    print "<td><a titles='" . displayDate($txtDate) . "'><input type='checkbox' eid='" . $cur[0] . "' flag='".$lstSetFlag."' gid='".$lstDeptTerminal."' remark='".$txtRemarks."' ot='".$lstSetOT."' group_id='".$lstSetShift."' oth='".$txtOTH."' name='chk" . $tot . "' value='" . $txtDate . "' id='chk-" . $tot . "'> <input type='hidden' class='dynamic-hidden-field' date='".$txtDate."' name='txhID" . $tot . "' value='" . $cur[0] . "'> <input type='hidden' name='txhDeptClocking" . $tot . "' value='" . $cur[14] . "'></a></td>";
                } else {
                    print "<td><font size='1'>&nbsp;</font></td>";
                }
            }
            $next = strtotime(substr($txtDate, 6, 2) . "-" . substr($txtDate, 4, 2) . "-" . substr($txtDate, 0, 4) . " + 1 day");
            $a = getDate($next);
            $m = $a["mon"];
            if ($m < 10) {
                $m = "0" . $m;
            }
            $d = $a["mday"];
            if ($d < 10) {
                $d = "0" . $d;
            }
            $txtDate = $a["year"] . $m . $d;
        }
    }
    $this_date = $txtDate;
    $this_day = "";
    for ($i = $subc; $i < $dayCount; $i++) {
        $tot++;
        if ($prints != "yes") {
            displayDate($this_date);
            print "<td><a title='" . displayDate($this_date) . " (" . $this_day . ")'><input type='checkbox' name='chk" . $tot . "' value='" . $this_date . "' id='chk-" . $tot . "'> <input type='hidden' name='txhID" . $tot . "' value='" . $data0 . "'> <input type='hidden' name='txhDeptClocking" . $tot . "' value='" . $data14 . "'></a> </td>";
            $next = strtotime(substr($this_date, 6, 2) . "-" . substr($this_date, 4, 2) . "-" . substr($this_date, 0, 4) . " + 1 day");
            $a = getDate($next);
            $m = $a["mon"];
            if ($m < 10) {
                $m = "0" . $m;
            }
            $d = $a["mday"];
            if ($d < 10) {
                $d = "0" . $d;
            }
            $this_date = $a["year"] . $m . $d;
            $this_day = $a["weekday"];
        } else {
            print "<td><font size='1'>&nbsp;</font></td>";
        }
    }
    print "</tr>";
    $row_count++;
    print "</table><p align='center'>";
    if ($prints != "yes" && 0 < $count && stripos($userlevel, $current_module . "A") !== false && $_SESSION[$session_variable . "LockDate"] < insertDate($txtFrom) && $_SESSION[$session_variable . "LockDate"] < insertDate($txtTo)) {
        print "<table width='800'>";
        print "<tr>";
        print "<td width='25%'>&nbsp;</td> <td><br><font face='Verdana' size='2'>Select Shift to be added to the checked boxes</font></td>";
        print "</tr>";
        print "<tr>";
        $query = "SELECT id, name from tgroup WHERE id > 1 ORDER BY name";
        displayList("lstSetShift", "", $lstSetShift, $prints, $conn, $query, "", "25%", "75%");
        print "</tr>";
        print "<tr>";
        print "<td>&nbsp;</td><td><input name='btSubmit' type='button' value='Save Changes' onClick='javascript:checkSubmit()' class='btn btn-primary'></td>";
        print "</tr>";
        print "<tr>";
        print "<td align='right'><font face='Verdana' size='2'>Mark Off Day:</font></td> <td><select name='lstSetOT' class='form-control'> <option value='" . $lstSetOT . "' selected>" . $lstSetOT . "</option> <option value='OT1'>OT1</option> <option value='OT2'>OT2</option> <option value='OT'>OT</option> <option value=''>---</option> </select></td>";
        print "</tr>";
//        print "<tr>";
//        print '<td>&nbsp;</td><td><button type="button" id="submitBtn" class="btn btn-primary">Save Changes</button></td>';
//        print "</tr>";
        print "<tr>";
        displayColourFlag($conn, $lstSetFlag, "lstSetFlag", false, true);
        print "</tr>";
        print "<tr>";
        print '<td>&nbsp;</td><td><input name="btnSubmit" type="button" value="Save Changes" onClick="javascript:checkSubmitFlag()" class="btn btn-primary"></td>';
        print "</tr>";
        print "</table>";
    }
    if ($prints != "yes" && 0 < $count && stripos($userlevel, $current_module . "E") !== false && $_SESSION[$session_variable . "LockDate"] < insertDate($txtFrom) && $_SESSION[$session_variable . "LockDate"] < insertDate($txtTo)) {
        print "<table width='800'>";
        print "<tr>";
        print "<td width='25%'>&nbsp;</td> <td><br><font face='Verdana' size='2'>Select Shift to be edited to <br>(Click on the Shifts above to change it to the Below Selected Shift)</font></td>";
        print "</tr>";
        print "<tr>";
        $query = "SELECT id, name from tgroup ORDER BY name";
        displayList("lstEditShift", "", $lstSetEditShift, $prints, $conn, $query, "", "25%", "75%");
        print "</tr>";
        print "</table>";
    }
    if ($excel != "yes") {
        print "<br><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $row_count . "</b></font>";
    }
    if ($prints != "yes") {
        print "<br><input type='button' value='Print' onClick='checkPrint(0)' class='btn btn-primary'>&nbsp;&nbsp;<input type='button' value='Excel' onClick='checkPrint(1)' class='btn btn-primary'>";
    }
    print "</p>";
}
print "<input type='hidden' name='txtTot' value='" . $tot . "'> <input type='hidden' name='txtRowCount' value='" . $row_count . "'></form>";
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    function checkSubmitFlag() { 
        x = document.frm1;
//        if (x.lstSetFlag.value == '') {
//            alert('Please select the Flag');
//            x.lstSetFlag.value = '';
//            x.lstSetFlag.focus();
//        } else {
            x.act.value = 'saveFlag';
            x.btnSubmit.disabled = true;
            x.submit();
//        }
    }
  
    $(document).ready(function() {
    $('#submitBtn').click(function() {

var checkboxValues = [];
var hiddenFieldValues = [];
var seenEIds = {};
x = document.frm1;
if (x.lstSetOT.value == '') {
    alert('Please select the off day flag');
    x.lstSetOT.focus();
}
//$('.dynamic-hidden-field').each(function() {
//    var hiddenFieldValue = $(this).val();
//    if (!seenEIds[hiddenFieldValue]) {
//        seenEIds[hiddenFieldValue] = true; // Mark the e_id value as seen
//        checkboxValues.push({ e_id: hiddenFieldValue });
//    }
//});
$('input[type="checkbox"]:checked').each(function() {
    var checkboxId = $(this).attr('id'); // Get the ID of the checked checkbox
    var checkboxValue = $(this).val(); // Get the value of the checked checkbox
    var e_id = $(this).attr('eid');
    var g_id = $(this).attr('gid');
    var flag = $(this).attr('flag');
    var remark = $(this).attr('remark');
    var ot = $(this).attr('ot');
    var group_id = $(this).attr('group_id');
    var oth = $(this).attr('oth');
        checkboxValues.push({ id: checkboxId, value: checkboxValue, offday:x.lstSetOT.value, e_id:e_id, deptTerminal: g_id, flag:flag, remark: remark, ot:ot, group_id:group_id, oth:oth  });
    });
        $.ajax({
            type: 'POST',
            url: 'offdayAddScript.php',
            data: { checkboxes: checkboxValues }, 
            success: function(response) {
                console.log(response);
                alert('Data saved successfully');
            },
            error: function() {
                alert('Error saving data');
            }
        });
    });
});
    
//    function generateRoster() {
//        if (confirm('Generate Shift Roster for the Selected Period \n\r[Roster will be generated for FUTURE Period ONLY]')) {
//            x.act.value = 'generateRoster';
//            x.btGenerateRoster.disabled = true;
//            x.submit();
//        }
//    }

//    function checkSubmit() {
//        x = document.frm1;
//        if (x.lstSetShift.value == '') {
//            alert('Please select the Shift to be set for the selected Day(s)');
//            x.lstSetFlag.focus();
//        } else {
//            x.act.value = 'saveRecord';
//            x.btSubmit.disabled = true;
//            x.submit();
//        }
//    }

//    function checkAllEmployee(b, c, x) {
//        if (x.checked) {
//            for (i = b; i < (b + c); i++) {
//                if (document.getElementById('chk-' + i)) {
//                    document.getElementById('chk-' + i).checked = true;
//                }
//            }
//        } else {
//            for (i = b; i < (b + c); i++) {
//                if (document.getElementById('chk-' + i)) {
//                    document.getElementById('chk-' + i).checked = false;
//                }
//            }
//        }
//    }

//    function checkAllDate(b, c, x) {
//        a = document.frm1.txtTot.value;
//        if (x.checked) {
//            for (i = b; i < a; i = (i + c + 1)) {
//                if (document.getElementById('chk-' + (i + 2))) {
//                    document.getElementById('chk-' + (i + 2)).checked = true;
//                }
//            }
//        } else {
//            for (i = b; i < a; i = (i + c + 1)) {
//                if (document.getElementById('chk-' + (i + 2))) {
//                    document.getElementById('chk-' + (i + 2)).checked = false;
//                }
//            }
//        }
//    }

//    function ajaxFunction(a, b, c, d) {
//        if (confirm("Edit this Shift")) {
//            x = document.frm1;
//            if (x.lstEditShift.value == '') {
//                alert('Please select a Shift to be edited to');
//                x.lstEditShift.focus();
//                return false;
//            } else {
//                var xmlHttp;
//                try {
//                    // Firefox, Opera 8.0+, Safari
//                    xmlHttp = new XMLHttpRequest();
//                } catch (e) {
//                    // Internet Explorer
//                    try {
//                        xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
//                    } catch (e) {
//                        try {
//                            xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
//                        } catch (e) {
//                            alert("Your browser does not support AJAX!");
//                            return false;
//                        }
//                    }
//                }
//                xmlHttp.onreadystatechange = function () {
//                    if (xmlHttp.readyState == 4) {
//                        x = xmlHttp.responseText;
//                        document.getElementById('txtSPAN' + d).innerHTML = x;
//                    }
//                };
//                xmlHttp.open("GET", "Ajax.php?act=editShift&txtID=" + a + "&txtDate=" + b + "&txtOldShift=" + c + "&txtShift=" + x.lstEditShift.value, true);
//                xmlHttp.send(null);
//            }
//        }
//    }
</script>
<!--</center></body></html>-->
<?php

echo "\r\n<script>\r\n/*\r\nfunction generateRoster(){\r\n\tif (confirm('Generate Shift Roster for the Selected Period \\n\\r[Roster will be generated for FUTURE Period ONLY]')){\r\n\t\tx.act.value='generateRoster';\r\n\t\tx.btGenerateRoster.disabled = true;\r\n\t\tx.submit();\r\n\t}\r\n}\r\n*/\r\nfunction checkSubmit(){\r\n\tx = document.frm1;\r\n\tif (x.lstSetShift.value == ''){\r\n\t\talert('Please select the Shift to be set for the selected Day(s)');\r\n\t\tx.lstSetFlag.focus();\r\n\t}else{\r\n\t\tx.act.value='saveRecord';\r\n\t\tx.btSubmit.disabled = true;\r\n\t\tx.submit();\r\n\t}\r\n}\r\n\r\nfunction checkAllEmployee(b, c, x){\r\n\tif (x.checked){\r\n\t\tfor (i=b;i<(b+c);i++){\r\n\t\t\tif (document.getElementById('chk-'+i)){\r\n\t\t\t\tdocument.getElementById('chk-'+i).checked = true;\r\n\t\t\t}\r\n\t\t}\r\n\t}else{\r\n\t\tfor (i=b;i<(b+c);i++){\r\n\t\t\tif (document.getElementById('chk-'+i)){\r\n\t\t\t\tdocument.getElementById('chk-'+i).checked = false;\r\n\t\t\t}\r\n\t\t}\r\n\t}\r\n}\r\n\r\nfunction checkAllDate(b, c, x){\r\n\ta = document.frm1.txtTot.value;\t\r\n\tif (x.checked){\r\n\t\tfor (i=b;i<a;i=(i+c+1)){\r\n\t\t\tif (document.getElementById('chk-'+(i+2))){\r\n\t\t\t\tdocument.getElementById('chk-'+(i+2)).checked = true;\r\n\t\t\t}\r\n\t\t}\r\n\t}else{\r\n\t\tfor (i=b;i<a;i=(i+c+1)){\r\n\t\t\tif (document.getElementById('chk-'+(i+2))){\r\n\t\t\t\tdocument.getElementById('chk-'+(i+2)).checked = false;\r\n\t\t\t}\r\n\t\t}\r\n\t}\r\n}\r\n\r\nfunction ajaxFunction(a, b, c, d){\r\n\tif (confirm(\"Edit this Shift\")){\r\n\t\tx = document.frm1;\r\n\t\tif (x.lstEditShift.value == ''){\r\n\t\t\talert('Please select a Shift to be edited to');\r\n\t\t\tx.lstEditShift.focus();\r\n\t\t\treturn false;\r\n\t\t}else{\r\n\t\t\tvar xmlHttp;\r\n\t\t\ttry{    \r\n\t\t\t\t// Firefox, Opera 8.0+, Safari    \r\n\t\t\t\txmlHttp=new XMLHttpRequest();    \r\n\t\t\t}catch (e){    \r\n\t\t\t\t// Internet Explorer    \r\n\t\t\t\ttry{      \r\n\t\t\t\t\txmlHttp=new ActiveXObject(\"Msxml2.XMLHTTP\");      \r\n\t\t\t\t}catch (e){      \r\n\t\t\t\t\ttry{        \r\n\t\t\t\t\t\txmlHttp=new ActiveXObject(\"Microsoft.XMLHTTP\");        \r\n\t\t\t\t\t}catch (e){        \r\n\t\t\t\t\t\talert(\"Your browser does not support AJAX!\");        \r\n\t\t\t\t\t\treturn false;        \r\n\t\t\t\t\t}      \r\n\t\t\t\t}    \r\n\t\t\t}\r\n\t\t\t\r\n\t\t\txmlHttp.onreadystatechange=function(){\r\n\t\t\t\tif(xmlHttp.readyState==4){\r\n\t\t\t\t\tx = xmlHttp.responseText;\t\t\t\r\n\t\t\t\t\tdocument.getElementById('txtSPAN'+d).innerHTML = x;\t\t\t\r\n\t\t\t\t}\r\n\t\t\t}\r\n\t\t\txmlHttp.open(\"GET\", \"Ajax.php?act=editShift&txtID=\"+a+\"&txtDate=\"+b+\"&txtOldShift=\"+c+\"&txtShift=\"+x.lstEditShift.value,true);\r\n\t\t\txmlHttp.send(null);\r\n\t\t}\r\n\t}\r\n}\r\n</script>\r\n</center></body></html>";
?>
