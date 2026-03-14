<?php

ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "18";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=ReportAttendance_Aspira.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$act = $_GET["act"];
if ($act == "") {
    $act = $_POST["act"];
}
$prints = $_GET["prints"];
$excel = $_GET["excel"];
$message = $_GET["message"];
if ($message == "") {
    $message = "Daily Attendance Report <br> (It is recommended that you DO NOT use a long Date Period)";
}
$lstShift = $_POST["lstShift"];
$lstDepartment = $_POST["lstDepartment"];
$lstDivision = $_POST["lstDivision"];
$lstEmployeeIDFrom = $_POST["lstEmployeeIDFrom"];
$lstEmployeeIDTo = $_POST["lstEmployeeIDTo"];
$txtEmployee = $_POST["txtEmployee"];
$txtEmployeeCode = $_POST["txtEmployeeCode"];
$lstSort = $_POST["lstSort"];
$txtFrom = $_POST["txtFrom"];
$txtTo = $_POST["txtTo"];
if ($txtFrom == "") {
    $txtFrom = displayToday();
}
if ($txtTo == "") {
    $txtTo = displayToday();
}
$lstTerminal = $_POST["lstTerminal"];
$txtRemark = $_POST["txtRemark"];
$txtPhone = $_POST["txtPhone"];
$txtSNo = $_POST["txtSNo"];
$lstDrill = $_POST["lstDrill"];
$lstEmployeeStatus = "";
if (isset($_POST["lstEmployeeStatus"])) {
    $lstEmployeeStatus = $_POST["lstEmployeeStatus"];
} else {
    $lstEmployeeStatus = "ACT";
}
$lstClockingType = $_POST["lstClockingType"];
$lstFlag = $_POST["lstFlag"];
if ($lstFlag == "") {
    $lstFlag = "No";
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
$lstGroupBy = $_POST["lstGroupBy"];
print "<html><title>Daily Attendance Report</title>";
if ($prints != "yes") {
    print "<body>";
} else {
    if ($excel != "yes") {
        print "<body onLoad='javascript:window.print()'>";
    } else {
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=ReportAttendance.xls");
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
    print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b></font></p>";
}
if ($excel != "yes") {
    if ($prints != "yes") {
        print "<table width='800' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
        print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Select ONE or MORE options and click 'Search Record'</b></font></td></tr>";
    } else {
        print "<table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
        print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Selected Options</b></font></td></tr>";
    }
    print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='ReportAttendance.php'><input type='hidden' name='act' value='searchRecord'>";
    print "<tr>";
    $query = "SELECT id, name from tgroup WHERE id > 1 ORDER BY name";
    displayList("lstShift", "Shift: ", $lstShift, $prints, $conn, $query, "", "25%", "75%");
    print "</tr>";
    displaySearchFields($conn, $prints, $session_variable, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $lstEmployeeName, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
    print "<tr>";
    print "<td colspan='2'><table width='100%' cellspacing='-1' cellpadding='-1' border='0'><tr>";
    displayTextbox("txtFrom", "Date From <font size='1'>(DD/MM/YYYY)</font>: ", $txtFrom, $prints, 12, "25%", "20%");
    $query = "SELECT id, name from tgate ORDER BY name";
    displayList("lstTerminal", "Terminal: ", $lstTerminal, $prints, $conn, $query, "", "30%", "25%");
    print "</tr></table></td>";
    print "</tr>";
    print "</tr>";
    print "<tr>";
    print "<td colspan='2'><table width='100%' cellspacing='-1' cellpadding='-1' border='0'><tr>";
    displayTextbox("txtTo", "Date To <font size='1'>(DD/MM/YYYY)</font>: ", $txtTo, $prints, 12, "25%", "20%");
    print "<td align='right' width='30%'><font face='Verdana' size='2'>Include Flagged Employees:</font></td><td width='25%'>";
    if ($prints != "yes") {
        print "<select name='lstFlag' class='form-control'> <option selected value='" . $lstFlag . "'>" . $lstFlag . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select>";
    } else {
        print "<font face='Verdana' size='2'><b>" . $lstFlag . "</b></font>";
    }
    print "</td>";
    print "</tr></table></td>";
    print "</tr>";
    if ($prints != "yes") {
        print "<tr>";
        print "<td colspan='2'><table width='100%' cellspacing='0' cellpadding='0' border='0'><tr>";
        $query = "SELECT DrillMasterID, CONCAT( DATE_FORMAT( DrillDate, '%d/%m/%Y' ) , ': ', SUBSTR(DrillTimeFrom, 1, 4), ' - ', SUBSTR(DrillTimeTo, 1, 4) ) FROM DrillMaster WHERE DrillDate <= '" . insertToday() . "' ORDER BY DrillDate DESC";
        displayList("lstDrill", "Display Record(s) for Drill: ", $lstDrill, $prints, $conn, $query, "", "25%", "25%");
        print "<td align='right' width='25%'><font face='Verdana' size='2'>(Meal) Group By:</font></td><td width='25%'><select name='lstGroupBy' class='form-control'><option selected value='" . $lstGroupBy . "'>" . $lstGroupBy . "</option> <option value='Shift'>Shift</option> <option value='Dept'>Dept</option> <option value='Div/Desg'>Div/Desg</option> <option value='Remark'>Remark</option> <option value='" . $_SESSION[$session_variable . "IDColumnName"] . "'>" . $_SESSION[$session_variable . "IDColumnName"] . "</option> <option value=''>---</option> </select></td>";
        print "</tr></table></td>";
        print "</tr>";
        displayClockingType($lstClockingType);
        print "<tr>";
        displayEmployeeStatus($conn, "lstEmployeeStatus", $lstEmployeeStatus, "", "");
        print "</tr>";
        print "<tr>";
        $array = array(array("tuser.id, tenter.e_date, tenter.e_time", "Employee Code"), array("tuser.name, tuser.id, tenter.e_date, tenter.e_time", "Employee Name - Code"), array("tuser.dept, tuser.id, tenter.e_date, tenter.e_time", "Dept - Code"), array("tuser.company, tuser.dept, tuser.id, tenter.e_date, tenter.e_time", "Div/Desg - Dept - Code"), array("tuser.company, tuser.dept, tenter.e_group, tuser.id, tenter.e_date, tenter.e_time", "Div - Dept - Shift - Code"));
        displaySort($array, $lstSort, 5);
        print "</tr>";
        print "<tr><td>&nbsp;</td><td><input name='btSearch' type='submit' value='Search Record' class='btn btn-primary'>&nbsp;&nbsp;<input type='button' value='Excel' onClick='checkPrint(1)' class='btn btn-primary'>&nbsp;&nbsp;<input type='button' value='CSV' onClick='checkPrint(3)' class='btn btn-primary'></td></tr></td></tr>";
    }
    print "</table><br>";
}
if ($act == "searchRecord") {
    $query = "SELECT NightShiftMaxOutTime FROM OtherSettingMaster";
    $result = selectData($conn, $query);
    $cutoff = $result[0];
    $count = 0;
    if ($prints != "yes") {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800'>";
    } else {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%'>";
    }
    if ($lstDrill != "") {
        $query = "SELECT DrillDate, DrillTimeFrom, DrillTimeTo FROM DrillMaster WHERE DrillMasterID = " . $lstDrill;
        $result = selectData($conn, $query);
        $main_query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tenter.e_date, tenter.e_time, tgate.name, tuser.idno, tuser.remark, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, tgroup, tenter, tgate WHERE tenter.e_group = tgroup.id AND tenter.e_id = tuser.id AND tenter.g_id = tgate.id AND tenter.e_date = '" . $result[0] . "' AND tenter.e_time >= '" . $result[1] . "' AND tenter.e_time <= '" . $result[2] . "' " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
        $query = "SELECT DrillTerminal.g_id FROM DrillTerminal WHERE DrillTerminal.DrillMasterID = " . $lstDrill;
        $result = mysqli_query($conn, $query);
        while ($cur = mysqli_fetch_row($result)) {
            $main_query = $main_query . " AND tenter.g_id = " . $cur[0];
        }
        $query = "SELECT DrillDiv.Div FROM DrillDiv WHERE DrillDiv.DrillMasterID = " . $lstDrill;
        $result = mysqli_query($conn, $query);
        while ($cur = mysqli_fetch_row($result)) {
            $main_query = $main_query . " AND tuser.company = '" . $cur[0] . "'";
        }
        $query = "SELECT DrillDept.Dept FROM DrillDept WHERE DrillDept.DrillMasterID = " . $lstDrill;
        $result = mysqli_query($conn, $query);
        while ($cur = mysqli_fetch_row($result)) {
            $main_query = $main_query . " AND tuser.dept = '" . $cur[0] . "'";
        }
        $query = "SELECT DrillRemark.Remark FROM DrillRemark WHERE DrillRemark.DrillMasterID = " . $lstDrill;
        $result = mysqli_query($conn, $query);
        while ($cur = mysqli_fetch_row($result)) {
            $main_query = $main_query . " AND tuser.Remark = '" . $cur[0] . "'";
        }
        $query = "SELECT DrillPhone.Phone FROM DrillPhone WHERE DrillPhone.DrillMasterID = " . $lstDrill;
        $result = mysqli_query($conn, $query);
        while ($cur = mysqli_fetch_row($result)) {
            $main_query = $main_query . " AND tuser.Phone = '" . $cur[0] . "'";
        }
        $query = "SELECT DrillIdNo.IdNo FROM DrillIdNo WHERE DrillIdNo.DrillMasterID = " . $lstDrill;
        $result = mysqli_query($conn, $query);
        while ($cur = mysqli_fetch_row($result)) {
            $main_query = $main_query . " AND tuser.idno = '" . $cur[0] . "'";
        }
        $last_id = "";
        $last_date = "";
        $result = mysqli_query($conn, $main_query);
        if (0 < mysqli_num_rows($result)) {
            print "<tr><td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td></tr>";
            print "<tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Shift</font></td> <td><font face='Verdana' size='2'>Date</font></td> <td><font face='Verdana' size='2'>Time</font></td> <td><font face='Verdana' size='2'>Terminal</font></td></tr>";
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
                    addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
                    displayDate($cur[5]);
                    displayVirdiTime($cur[6]);
                    print "<tr><td><a title='ID'><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'><font face='Verdana' size='1'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $cur[1] . "</font></a></td> <td><a title='Normal'><font face='Verdana' size='1'>" . $cur[8] . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $cur[2] . "</font></a></td> <td><a title='Dev/Desg'><font face='Verdana' size='1'>" . $cur[3] . "</font></a></td> <td><a title='Rmk'><font face='Verdana' size='1'>" . $cur[9] . "</font></a></td> <td><a title='Shift'><font face='Verdana' size='1'>" . $cur[4] . "</font></a></td> <td><a title='Date'><font face='Verdana' size='1'>" . displayDate($cur[5]) . "</font></a></td> <td><a title='Time'><font face='Verdana' size='1'>" . displayVirdiTime($cur[6]) . "</font></a></td> <td><a title='Terminal'><font face='Verdana' size='1'>" . $cur[7] . "</font></a></td></tr>";
                    $last_id = $cur[0];
                    $last_date = $cur[5];
                    $count++;
                }
            }
        }
    } else {
        if ($lstGroupBy != "") {
            $row_total = 0;
            $col_total = 0;
            $phone_count = 0;
            $phone_array = "";
            print "<tr><td>&nbsp;</td>";
            $query = "SELECT DISTINCT(Phone) FROM tuser";
            for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $phone_count++) {
                print "<td><font face='Verdana' size='1'><b>" . $cur[0] . "</b></font></td>";
                $phone_array[$phone_count] = $cur[0];
            }
            print "<td><font face='Verdana' size='1'><b>Total</b></font></td></tr>";
            if ($lstGroupBy == "Shift") {
                $query = "SELECT DISTINCT(name), id FROM tgroup";
            } else {
                if ($lstGroupBy == "Dept") {
                    $query = "SELECT DISTINCT(dept) FROM tuser";
                } else {
                    if ($lstGroupBy == "Div/Desg") {
                        $query = "SELECT DISTINCT(company) FROM tuser";
                    } else {
                        if ($lstGroupBy == "Remark") {
                            $query = "SELECT DISTINCT(remark) FROM tuser";
                        } else {
                            if ($lstGroupBy == $_SESSION[$session_variable . "IDColumnName"]) {
                                $query = "SELECT DISTINCT(idno) FROM tuser";
                            }
                        }
                    }
                }
            }
            $result = mysqli_query($conn, $query);
            while ($cur = mysqli_fetch_row($result)) {
                $row_total = 0;
                print "<tr><td><font face='Verdana' size='1'>" . $cur[0] . "</font></td>";
                for ($i = 0; $i < count($phone_array); $i++) {
                    $query = "SELECT COUNT(ed) FROM tenter, tuser WHERE tenter.e_date >= " . insertDate($txtFrom) . " AND tenter.e_date <= " . insertDate($txtTo) . " AND tenter.e_id = tuser.id AND tuser.phone = '" . $phone_array[$i] . "' AND ";
                    if ($lstGroupBy == "Shift") {
                        $query .= " tenter.e_group = " . $cur[1];
                    } else {
                        if ($lstGroupBy == "Dept") {
                            $query .= " tuser.dept = '" . $cur[0] . "'";
                        } else {
                            if ($lstGroupBy == "Div/Desg") {
                                $query .= " tuser.company = '" . $cur[0] . "'";
                            } else {
                                if ($lstGroupBy == "Remark") {
                                    $query .= " tuser.remark = '" . $cur[0] . "'";
                                } else {
                                    if ($lstGroupBy == $_SESSION[$session_variable . "IDColumnName"]) {
                                        $query .= " tuser.idno = '" . $cur[0] . "'";
                                    }
                                }
                            }
                        }
                    }
                    $sub_result = selectData($conn, $query);
                    print "<td><font face='Verdana' size='1'>" . $sub_result[0] . "</font></td>";
                    $row_total = $row_total + $sub_result[0] * $phone_array[$i];
                }
                $col_total = $col_total + $row_total;
                addComma($row_total);
                print "<td><font face='Verdana' size='1'><b>" . addComma($row_total) . "</b></font></td></tr>";
            }
            print "<tr><td>&nbsp;</td>";
            for ($i = 0; $i < count($phone_array); $i++) {
                print "<td>&nbsp;</td>";
            }
            addComma($col_total);
            print "<td><font face='Verdana' size='1' color='red'><b>" . addComma($col_total) . "</b></font></td></tr>";
        } else {
            for ($date_count = insertDate($txtFrom); $date_count <= insertDate($txtTo); $date_count++) {
                if (checkdate(substr($date_count, 4, 2), substr($date_count, 6, 2), substr($date_count, 0, 4))) {
                    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tenter.e_date, tenter.e_time, tgate.name, tuser.idno, tuser.remark, tuser.phone,tuser.F1,tuser.F2,tuser.F3,tuser.F4,tuser.F5,tuser.F6 FROM tuser, tgroup, tenter, tgate WHERE tenter.e_group = tgroup.id AND tenter.e_id = tuser.id AND tenter.g_id = tgate.id AND tgate.exit = 0 AND ((tenter.e_time > '" . $cutoff . "00' AND tgroup.NightFlag = 1) OR (tenter.e_time > '000000' AND tgroup.NightFlag = 0)) " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
                    if ($lstShift != "") {
                        $query = $query . " AND tgroup.id = " . $lstShift;
                    }
                    if ($lstTerminal != "") {
                        $query = $query . " AND tenter.g_id = " . $lstTerminal;
                    }
                    $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
                    if ($lstFlag == "No") {
                        $query = $query . " AND tuser.id NOT IN (SELECT e_id FROM FlagDayRotation WHERE e_date = " . $date_count . ") ";
                    }
                    if ($date_count != "") {
                        $query = $query . " AND tenter.e_date = '" . $date_count . "'";
                    }
                    $query = queryClockingType($query, $lstClockingType);
                    $query = $query . employeeStatusQuery($lstEmployeeStatus);
                    echo $query = $query . " ORDER BY " . $lstSort;
                    $last_id = "";
                    $last_date = "";
                    $result = mysqli_query($conn, $query);
                    if (0 < count(mysqli_num_rows($result))) {
                        print "<tr><td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td></tr>";
                        print "<tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Rate</font></td> <td><font face='Verdana' size='2'>Location</font></td> <td><font face='Verdana' size='2'>Bank</font></td> <td><font face='Verdana' size='2'>Account No.</font></td><td><font face='Verdana' size='2'>Account Name</font></td><td><font face='Verdana' size='2'>Next of Kin ACC.</font></td><td><font face='Verdana' size='2'>Bonus</font></td><td><font face='Verdana' size='2'>Working Days</font></td><td><font face='Verdana' size='2'>Present Days</font></td><td><font face='Verdana' size='2'>PH</font></td><td><font face='Verdana' size='2'>PH Amount</font></td><td><font face='Verdana' size='2'>SUN</font></td><td><font face='Verdana' size='2'>Normal Days Payment</font></td><td><font face='Verdana' size='2'>Loan/Ded</font></td><td><font face='Verdana' size='2'>Total Amount</font></td>";
                        if ($_SESSION[$session_variable . "VirdiLevel"] == "Meal") {
                            print "<td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "PhoneColumnName"] . "</font></td>";
                        }
                        print "</tr>";
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
                                addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
                                displayDate($cur[5]);
                                displayVirdiTime($cur[6]);
                                print "<tr><td><a title='ID'><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'><font face='Verdana' size='1'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $cur[1] . "</font></a></td> <td><a title='Normal'><font face='Verdana' size='1'>" . $cur[8] . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $cur[2] . "</font></a></td> <td><a title='Dev/Desg'><font face='Verdana' size='1'>" . $cur[3] . "</font></a></td> <td><a title='Rmk'><font face='Verdana' size='1'>" . $cur[9] . "</font></a></td> <td><a title='Shift'><font face='Verdana' size='1'>" . $cur[11] . "</font></a></td> <td><a title='Date'><font face='Verdana' size='1'>" . $cur[12] . "</font></a></td> <td><a title='Time'><font face='Verdana' size='1'>" . $cur[13] . "</font></a></td> <td><a title='Terminal'><font face='Verdana' size='1'>" . $cur[14] . "</font></a></td><td><a title='Terminal'><font face='Verdana' size='1'>" . $cur[15] . "</font></a></td><td><a title='Terminal'><font face='Verdana' size='1'>" . $cur[16] . "</font></a></td><td><a title='Terminal'><font face='Verdana' size='1'></font></a></td><td><a title='Terminal'><font face='Verdana' size='1'></font></a></td><td><a title='Terminal'><font face='Verdana' size='1'></font></a></td><td><a title='Terminal'><font face='Verdana' size='1'></font></a></td><td><a title='Terminal'><font face='Verdana' size='1'></font></a></td><td><a title='Terminal'><font face='Verdana' size='1'></font></a></td><td><a title='Terminal'><font face='Verdana' size='1'></font></a></td><td><a title='Terminal'><font face='Verdana' size='1'></font></a></td><td><a title='Terminal'><font face='Verdana' size='1'></font></a></td>";
                                if ($_SESSION[$session_variable . "VirdiLevel"] == "Meal") {
                                    print "<td><font face='Verdana' size='1'>" . $cur[10] . "</font></td>";
                                }
                                print "</tr>";
                                $last_id = $cur[0];
                                $last_date = $cur[5];
                                $count++;
                            }
                        }
                    }
                }
            }
        }
    }
    print "</table>";
    if ($excel != "yes") {
        print "<br><p align='center'><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $count . "</b></font>";
    }
    if ($prints != "yes") {
        print "<br><input type='button' value='Print Report' onClick='checkPrint(0)' class='btn btn-primary'>&nbsp;&nbsp;<input type='button' value='Excel' onClick='checkPrint(1)' class='btn btn-primary'>";
    }
    print "</p>";
}
print "</form>";
echo "</center></body></html>";

?>