<?php


ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "31";
set_time_limit(900);
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
$lstShift = $_POST["lstShift"];
$lstDepartment = $_POST["lstDepartment"];
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
$lstSetShift = $_POST["lstSetShift"];
$lstEmployeeStatus = $_POST["lstEmployeeStatus"];
if ($lstEmployeeStatus == "") {
    $lstEmployeeStatus = "Active";
}
$lstSort = $_POST["lstSort"];
$txtRemarks = $_POST["txtRemarks"];
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
}
print "<html><title>Shift Roster</title>";
if ($prints != "yes") {
    print "<body>";
} else {
    if ($excel != "yes") {
        print "<body onLoad='javascript:window.print()'>";
    } else {
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=ReportEmployee.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        print "<body>";
    }
}
print "<center>";
if ($excel != "yes") {
    displayHeader();
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
    print "<form name='frm1' method='post' action='ShiftRoster.php'><input type='hidden' name='act' value='saveRecord'><tr>";
    $query = "SELECT id, name from tgroup WHERE ShiftTypeID = 1 ORDER BY name";
    displayList("lstShift", "Current Shift: ", $lstShift, $prints, $conn, $query, "", "25%", "75%");
    print "</tr>";
    displaySearchFields($conn, $prints, $session_variable, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $lstEmployeeName, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"]);
    print "<tr>";
    displayTextbox("txtFrom", "Date From (DD/MM/YYYY): ", $txtFrom, $prints, 12, "25%", "75%");
    print "</tr>";
    print "<tr>";
    displayTextbox("txtTo", "Date To (DD/MM/YYYY): ", $txtTo, $prints, 12, "25%", "75%");
    print "</tr>";
    if ($prints != "yes") {
        print "<tr>";
        displayEmployeeStatus($conn, "lstEmployeeStatus", $lstEmployeeStatus, "", "");
        print "</tr>";
        print "<tr>";
        $array = array(array("tuser.id", "Employee Code"), array("tuser.name, tuser.id", "Employee Name - Code"), array("tuser.dept, tuser.id", "Dept - Code"), array("tuser.company, tuser.dept, tuser.id", "Div/Desg - Dept - Code"), array("tuser.company, tuser.dept, tuser.group_id, tuser.id", "Div - Dept - Current Shift - Code"));
        displaySort($array, $lstSort, 5);
        print "</tr>";
        print "<tr><td>&nbsp;</td><td><input name='btSearch' type='button' value='Search Record' onClick='javascript:checkSearch()'>&nbsp;&nbsp;<input type='button' value='Excel' onClick='checkPrint(1)'></td></tr>";
    }
    print "</table><br>";
}
if ($act == "searchRecord") {
    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, 0, 0, 0, 0, 0, tuser.idno, tuser.remark, '', tuser.group_id, tuser.OT1 FROM tuser, tgroup WHERE tuser.group_id = tgroup.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
    if ($lstShift != "") {
        $query = $query . " AND tgroup.id = " . $lstShift;
    }
    $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn);
    $query = $query . employeeStatusQuery($lstEmployeeStatus);
    $fromTime = mktime(0, 0, 0, substr(insertDate($txtFrom), 4, 2), substr(insertDate($txtFrom), 6, 2), substr(insertDate($txtFrom), 0, 4));
    $toTime = mktime(0, 0, 0, substr(insertDate($txtTo), 4, 2), substr(insertDate($txtTo), 6, 2), substr(insertDate($txtTo), 0, 4));
    $dayCount = ($toTime - $fromTime) / 86400;
    $dayCount++;
    $query = $query . " ORDER BY " . $lstSort;
    print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%'>";
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
            print "<tr><td><a title='ID'><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'><font face='Verdana' size='1'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $cur[1] . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $cur[10] . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $cur[2] . "</font></a></td> <td><a title='Div/Desg'><font face='Verdana' size='1'>" . $cur[3] . "</font></a></td> <td><a title='Rmk'><font face='Verdana' size='1'>" . $cur[11] . "</font></a></td> <td><a title='Current Shift'><font face='Verdana' size='1'>" . $cur[4] . "</font></a></td> <td bgcolor='#F0F0F0'>";
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
            $query = "SELECT tgroup.name FROM tgroup, ShiftRoster WHERE ShiftRoster.e_group = tgroup.id AND ShiftRoster.e_id = " . $cur[0] . " AND ShiftRoster.e_date = " . $txtDate;
            $shift_result = selectData($conn, $query);
            if ($sub_cur[9] == $txtDate || $shift_result[0] != "") {
                print "<td>";
                if ($prints == "yes") {
                    print "<font face='Verdana' size='1'>&nbsp;</font>";
                } else {
                    if ($sub_cur[12] == "" && $shift_result[0] == "") {
                        displayDate($txtDate);
                        print "<td><a title='" . displayDate($txtDate) . "'><input type='checkbox' name='chk" . $tot . "' value='" . $txtDate . "' id='chk-" . $tot . "'> <input type='hidden' name='txhID" . $tot . "' value='" . $cur[0] . "'> <input type='hidden' name='txhDeptClocking" . $tot . "' value='" . $cur[14] . "'></a></td>";
                    } else {
                        if ($sub_cur[12] == "") {
                            print "<font face='Verdana' size='1'>" . $shift_result[0] . "</font>";
                        } else {
                            print "<font face='Verdana' size='1'>" . $sub_cur[12] . "</font>";
                        }
                    }
                }
                print "</td>";
            } else {
                if ($prints != "yes") {
                    displayDate($txtDate);
                    print "<td><a title='" . displayDate($txtDate) . "'><input type='checkbox' name='chk" . $tot . "' value='" . $txtDate . "' id='chk-" . $tot . "'> <input type='hidden' name='txhID" . $tot . "' value='" . $cur[0] . "'> <input type='hidden' name='txhDeptClocking" . $tot . "' value='" . $cur[14] . "'></a></td>";
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
        print "<table>";
        print "<tr>";
        $query = "SELECT id, name from tgroup WHERE id > 1 ORDER BY name";
        displayList("lstSetShift", "Select Shift: ", $lstSetShift, $prints, $conn, $query, "", "25%", "75%");
        print "</tr>";
        print "<tr>";
        print "<td>&nbsp;</td><td><input name='btSubmit' type='button' value='Save Changes' onClick='javascript:checkSubmit()'></td>";
        print "</tr>";
        print "</table>";
    }
    if ($excel != "yes") {
        print "<br><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $row_count . "</b></font>";
    }
    if ($prints != "yes") {
        print "<br><input type='button' value='Print' onClick='checkPrint(0)'>&nbsp;&nbsp;<input type='button' value='Excel' onClick='checkPrint(1)'>";
    }
    print "</p>";
}
print "<input type='hidden' name='txtTot' value='" . $tot . "'> <input type='hidden' name='txtRowCount' value='" . $row_count . "'></form>";
echo "\r\n<script>\r\nfunction checkSubmit(){\r\n\tx = document.frm1;\r\n\tif (x.lstSetShift.value == ''){\r\n\t\talert('Please select the Shift to be set for the selected Day(s)');\r\n\t\tx.lstSetFlag.focus();\r\n\t}else{\r\n\t\tx.act.value='saveRecord';\r\n\t\tx.btSubmit.disabled = true;\r\n\t\tx.submit();\t\t\r\n\t}\r\n}\r\n\r\nfunction checkPrint(a){\r\n\tvar x = document.frm1;\r\n\tif (check_valid_date(x.txtFrom.value) == false){\r\n\t\talert('Invalid From Date. Date Format should be DD/MM/YYYY');\r\n\t\tx.txtFrom.focus();\r\n\t\t/*\r\n\t}else if (check_valid_date(x.txtTo.value) == false){\r\n\t\talert('Invalid From Date. Date Format should be DD/MM/YYYY');\r\n\t\tx.txtTo.focus();\r\n\t\t*/\r\n\t}else{\r\n\t\tif (a == 0){\r\n\t\t\tx.action = 'ShiftRoster.php?prints=yes';\r\n\t\t}else{\r\n\t\t\tx.action = 'ShiftRoster.php?prints=yes&excel=yes';\t\t\t\r\n\t\t}\t\t\r\n\t\tx.act.value = 'searchRecord';\r\n\t\tx.target = '_blank';\r\n\t\tx.submit();\r\n\t}\r\n}\r\n\r\nfunction checkSearch(){\r\n\tvar x = document.frm1;\r\n\tif (check_valid_date(x.txtFrom.value) == false){\r\n\t\talert('Invalid From Date. Date Format should be DD/MM/YYYY');\r\n\t\tx.txtFrom.focus();\r\n\t\t/*\r\n\t}else if (check_valid_date(x.txtTo.value) == false){\r\n\t\talert('Invalid From Date. Date Format should be DD/MM/YYYY');\r\n\t\tx.txtTo.focus();\r\n\t\t*/\r\n\t}else{\r\n\t\tx.act.value = 'searchRecord';\r\n\t\tx.action = 'ShiftRoster.php?prints=no';\r\n\t\tx.target = '_self';\r\n\t\tx.btSearch.disabled = true;\r\n\t\tx.submit();\r\n\t}\r\n}\r\n\r\nfunction checkAllEmployee(b, c, x){\r\n\tif (x.checked){\r\n\t\tfor (i=b;i<(b+c);i++){\r\n\t\t\tif (document.getElementById('chk-'+i)){\r\n\t\t\t\tdocument.getElementById('chk-'+i).checked = true;\r\n\t\t\t}\r\n\t\t}\r\n\t}else{\r\n\t\tfor (i=b;i<(b+c);i++){\r\n\t\t\tif (document.getElementById('chk-'+i)){\r\n\t\t\t\tdocument.getElementById('chk-'+i).checked = false;\r\n\t\t\t}\r\n\t\t}\r\n\t}\r\n}\r\n\r\nfunction checkAllDate(b, c, x){\r\n\ta = document.frm1.txtTot.value;\t\r\n\tif (x.checked){\r\n\t\tfor (i=b;i<a;i=(i+c+1)){\r\n\t\t\tif (document.getElementById('chk-'+(i+2))){\r\n\t\t\t\tdocument.getElementById('chk-'+(i+2)).checked = true;\r\n\t\t\t}\r\n\t\t}\r\n\t}else{\r\n\t\tfor (i=b;i<a;i=(i+c+1)){\r\n\t\t\tif (document.getElementById('chk-'+(i+2))){\r\n\t\t\t\tdocument.getElementById('chk-'+(i+2)).checked = false;\r\n\t\t\t}\r\n\t\t}\r\n\t}\r\n}\r\n</script>\r\n</center></body></html>";

?>