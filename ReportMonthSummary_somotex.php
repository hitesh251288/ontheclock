<?php

ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "21";
set_time_limit(900);
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!(checkSession($userlevel, $current_module) && $_SESSION[$session_variable . "VirdiLevel"] == "Classic")) {
    header("Location: " . $config["REDIRECT"] . "?url=ReportMonthSummary.php&message=Session Expired or Security Policy Violated");
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
    $message = "Month Summary Report<br>Enter Payroll Start Date in 'Date From' Field and Payroll End Date in 'Date To' Field<br>Report Valid ONLY for Shifts with Routine Type = Daily";
}
$lstShift = $_POST["lstShift"];
$lstDepartment = $_POST["lstDepartment"];
$lstDivision = $_POST["lstDivision"];
$lstEmployeeIDFrom = $_POST["lstEmployeeIDFrom"];
$lstEmployeeIDTo = $_POST["lstEmployeeIDTo"];
$txtEmployeeCode = $_POST["txtEmployeeCode"];
$txtEmployee = $_POST["txtEmployee"];
$txtFrom = $_POST["txtFrom"];
$txtTo = $_POST["txtTo"];
$lstSort = $_POST["lstSort"];
if ($txtFrom == "") {
    if (substr(insertToday(), 6, 2) == "01") {
        if (substr(insertToday(), 4, 2) == "01") {
            $txtFrom = "01/12/" . (substr(insertToday(), 0, 4) - 1);
        } else {
            if (substr(insertToday(), 4, 2) - 1 < 10) {
                $txtFrom = "01/0" . (substr(insertToday(), 4, 2) - 1) . "/" . substr(insertToday(), 0, 4);
            } else {
                $txtFrom = "01/" . (substr(insertToday(), 4, 2) - 1) . "/" . substr(insertToday(), 0, 4);
            }
        }
    } else {
        $txtFrom = "01/" . substr(displayToday(), 3, 7);
    }
}
if ($txtTo == "") {
    $txtTo = displayDate(getLastDay(insertToday(), 1));
}
$txtRemark = $_POST["txtRemark"];
$txtPhone = $_POST["txtPhone"];
$txtSNo = $_POST["txtSNo"];
$lstEmployeeStatus = "";
if (isset($_POST["lstEmployeeStatus"])) {
    $lstEmployeeStatus = $_POST["lstEmployeeStatus"];
} else {
    $lstEmployeeStatus = "ACT";
}
$lstType = $_POST["lstType"];
$lstGroupBy = $_POST["lstGroupBy"];
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
print "<html><title>Month Summary Report</title>";
if ($prints != "yes") {
    print "<body>";
} else {
    if ($excel != "yes") {
        print "<body onLoad='javascript:window.print()'>";
    } else {
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=ReportMonthSummary.xls");
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
    displayLinks(18, $userlevel);
}
print "</center>";
if ($excel != "yes") {
    print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b></font></p>";
    if ($prints != "yes") {
        print "<table width='800' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
        print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Select ONE or MORE options and click 'Search Record'</b></font></td></tr>";
    } else {
        print "<table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
        print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Selected Options</b></font></td></tr>";
    }
    print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='ReportMonthSummary.php'><input type='hidden' name='act' value='searchRecord'>";
    print "<tr>";
    displaySearchFields($conn, $prints, $session_variable, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $lstEmployeeName, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
    print "</tr>";
    print "<tr>";
    print "<td width='100%' colspan='2'><table cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%'><tr>";
    displayTextbox("txtFrom", "Date From <font size='1'>(DD/MM/YYYY)</font>: ", $txtFrom, $prints, 12, "25%", "25%");
    print "<td align='right' width='25%'><font face='Verdana' size='2'>Work Type:</font></td><td width='25%'><select name='lstType' class='form-control'><option selected value='" . $lstType . "'>" . $lstType . "</option> <option value='Early In'>Early In</option> <option value='Late In'>Late In</option> <option value='Less Break'>Less Break</option> <option value='More Break'>More Break</option> <option value='Early Out'>Early Out</option> <option value='Late Out'>Late Out</option> <option value='Grace'>Grace</option> <option value='OT'>OT</option> <option value='Approved OT'>Approved OT</option> <option value=''>---</option> </select></td>";
    print "</tr></table></td>";
    print "</tr>";
    print "<tr>";
    print "<td width='100%' colspan='2'><table cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%'><tr>";
    displayTextbox("txtTo", "Date To <font size='1'>(DD/MM/YYYY)</font>: ", $txtTo, $prints, 12, "25%", "25%");
    print "<td align='right' width='25%'><font face='Verdana' size='2'>Group By:</font></td><td width='25%'><select name='lstGroupBy' class='form-control'><option selected value='" . $lstGroupBy . "'>" . $lstGroupBy . "</option> <option value='Dept'>Dept</option> <option value=''>---</option> </select></td>";
    print "</tr></table></td>";
    print "</tr>";
    if ($prints != "yes") {
        print "<tr>";
        displayEmployeeStatus($conn, "lstEmployeeStatus", $lstEmployeeStatus, "", "");
        print "</tr>";
        print "<tr>";
        $array = array(array("tuser.id", "Employee Code"), array("tuser.name, tuser.id", "Employee Name - Code"), array("tuser.dept, tuser.id", "Dept - Code"), array("tuser.company, tuser.dept, tuser.id", "Div/Desg - Dept - Code"), array("tuser.company, tuser.dept, tuser.group_id, tuser.id", "Div - Dept - Current Shift - Code"));
        displaySort($array, $lstSort, 5);
        print "</tr>";
        print "<tr><td width='25%'>&nbsp;</td><td><input name='btSearch' type='submit' value='Search Record'>&nbsp;&nbsp;<input type='button' value='Excel' onClick='checkPrint(1)'></td></tr>";
    }
    print "</table><br>";
}
if ($act == "searchRecord") {
    if ($excel != "yes") {
        print "<p><font face='Verdana' size='1'><b><u>All Data Displayed in Hours</u> <br><br><font size='2'>N = Normal Hours <br>WKD = Week Day Approved Overtime <br>SAT = Saturday / OT1 Approved Overtime <br>SUN = Sunday / OT2 Approved Overtime <br>PR = Purple Flag (Public Holiday) Approved Overtime</font>";
        print "</b></font></p>";
    }
    $dateFromArray = "";
    $dateToArray = "";
    $i = 0;
    $txtF = insertDate($txtFrom);
    $txtT = insertDate($txtTo);
    if ($prints != "yes") {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800'>";
    } else {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%'>";
    }
    if ($lstGroupBy == "Dept") {
        print "<tr><td><font face='Verdana' size='2'>Dept</font></td>";
    } else {
        print "<tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td>";
    }

    if ($lstType == "") {
        if ($lstGroupBy == "Dept") {
            print "<tr><td><font face='Verdana' size='1'>&nbsp;</font></td>";
        }
//        for ($i = 0; $i < count($dateFromArray); $i++) {
        print "<td><font face='Verdana' size='1'>N</font></td><td><font face='Verdana' size='1'>WKD</font></td><td><font face='Verdana' size='1'>SAT</font></td><td><font face='Verdana' size='1'>SUN</font></td><td><font face='Verdana' size='1' color='Purple'>PR</font></td><td><font face='Verdana' size='1'>Sat Rate</font></td><td><font face='Verdana' size='1'>Sun Rate</font></td><td><font face='Verdana' size='1'>PR Rate</font></td><td><font face='Verdana' size='1'>Sat Amount</font></td><td><font face='Verdana' size='1'>Sun Amount</font></td><td><font face='Verdana' size='1'>PR Amount</font></td><td><font face='Verdana' size='1'>Salary Slab</font></td><td><font face='Verdana' size='1'>Sat OT Amount</font></td><td><font face='Verdana' size='1'>Sun OT Amount</font></td><td><font face='Verdana' size='1'>PH OT Amount</font></td><td><font face='Verdana' size='1'>Misc Staff</font></td><td><font face='Verdana' size='1'>Sat Calculation</font></td><td><font face='Verdana' size='1'>Sun Calculation</font></td><td><font face='Verdana' size='1'>PH Calculation</font></td>";
    }
    if ($lstGroupBy == "") {
        $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, '', tuser.idno, tuser.remark, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser WHERE tuser.id > 0 " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
        $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
        $query = $query . employeeStatusQuery($lstEmployeeStatus);
        if ($lstSort != "") {
            $query = $query . " ORDER BY " . $lstSort;
        } else {
            $query = $query . " ORDER BY tuser.id";
        }
        $main_result = mysqli_query($conn, $query);
    } else {
        if ($lstGroupBy == "Dept") {
            $query = "SELECT DISTINCT(dept) FROM tuser WHERE tuser.id > 0 " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
            $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
            $query = $query . employeeStatusQuery($lstEmployeeStatus);
            $main_result = mysqli_query($conn, $query);
        }
    }
    $row_count = 0;
    $total = 0;
    $t1 = 0;
    $t2 = 0;
    $t3 = 0;
    $t4 = 0;
    for ($t5 = 0; $main_cur = mysqli_fetch_row($main_result); $row_count++) {
        addZero($main_cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
        print "<tr><td><a title='ID'><input type='hidden' name='txh" . $count . "' value='" . $main_cur[0] . "'><font face='Verdana' size='1'>" . addZero($main_cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $main_cur[1] . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $main_cur[5] . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $main_cur[2] . "</font></a></td> <td><a title='Div/Desg'><font face='Verdana' size='1'>" . $main_cur[3] . "</font></a></td> <td><a title='Rmk'><font face='Verdana' size='1'>" . $main_cur[6] . "</font></a></td>";
//            for ($i = 0; $i < count($dateFromArray); $i++) {
        $query = "";
        if ($lstType == "Early In") {
            $query = "SELECT SUM(AttendanceMaster.EarlyIn) ";
        } else {
            if ($lstType == "Late In") {
                $query = "SELECT SUM(AttendanceMaster.LateIn) ";
            } else {
                if ($lstType == "Less Break") {
                    $query = "SELECT SUM(AttendanceMaster.LessBreak) ";
                } else {
                    if ($lstType == "More Break") {
                        $query = "SELECT SUM(AttendanceMaster.MoreBreak) ";
                    } else {
                        if ($lstType == "Early Out") {
                            $query = "SELECT SUM(AttendanceMaster.EarlyOut) ";
                        } else {
                            if ($lstType == "Late Out") {
                                $query = "SELECT SUM(AttendanceMaster.LateOut) ";
                            } else {
                                if ($lstType == "Grace") {
                                    $query = "SELECT SUM(AttendanceMaster.Grace) ";
                                } else {
                                    if ($lstType == "OT") {
                                        $query = "SELECT SUM(AttendanceMaster.Overtime) ";
                                    } else {
                                        if ($lstType == "Approved OT") {
                                            $query = "SELECT SUM(AttendanceMaster.AOvertime) ";
                                        } else {
                                            $query_ = "SELECT SUM(AttendanceMaster.Normal) FROM AttendanceMaster WHERE EmployeeID = " . $main_cur[0] . " AND ADate >= " . $txtF . " AND ADate <= " . $txtT;
                                            $result1 = selectData($conn, $query_);
                                            displayDate($dateFromArray[$i]);
                                            displayDate($dateToArray[$i]);
                                            round($result1[0] / 3600, 2);
                                            // [" . displayDate($dateFromArray[$i]) . " - " . displayDate($dateToArray[$i]) . "]
                                            print "<td><a title='Normal Hours'><font face='Verdana' size='1'>" . round($result1[0] / 3600, 2) . "</font></a></td>";
                                            $t1 = $t1 + $result1[0];
                                            $query_ = "SELECT SUM(AttendanceMaster.AOvertime) FROM AttendanceMaster WHERE EmployeeID = " . $main_cur[0] . " AND Day <> OT1 AND Day <> OT2 AND Flag <> 'Purple' AND ADate >= " . $txtF . " AND ADate <= " . $txtT;
                                            $result2 = selectData($conn, $query_);
                                            displayDate($dateFromArray[$i]);
                                            displayDate($dateToArray[$i]);
                                            round($result2[0] / 3600, 2);
                                            print "<td><a title='Approved OT on WeekDays'><font face='Verdana' size='1'>" . round($result2[0] / 3600, 2) . "</font></a></td>";
                                            $t2 = $t2 + $result2[0];
                                            $query_ = "SELECT SUM(AttendanceMaster.AOvertime) FROM AttendanceMaster WHERE EmployeeID = " . $main_cur[0] . " AND Day = OT1 AND Flag <> 'Purple' AND ADate >= " . $txtF . " AND ADate <= " . $txtT;
                                            $result3 = selectData($conn, $query_);
                                            displayDate($dateFromArray[$i]);
                                            displayDate($dateToArray[$i]);
                                            round($result3[0] / 3600, 2);
                                            print "<td><a title='Approved OT on Saturdays'><font face='Verdana' size='1'>" . round($result3[0] / 3600, 2) . "</font></a></td>";
                                            $t3 = $t3 + $result3[0];
                                            $query_ = "SELECT SUM(AttendanceMaster.AOvertime) FROM AttendanceMaster WHERE EmployeeID = " . $main_cur[0] . " AND Day = OT2 AND Flag <> 'Purple' AND ADate >= " . $txtF . " AND ADate <= " . $txtT;
                                            $result4 = selectData($conn, $query_);
                                            displayDate($dateFromArray[$i]);
                                            displayDate($dateToArray[$i]);
                                            round($result4[0] / 3600, 2);
                                            print "<td><a title='Approved OT on Sundays'><font face='Verdana' size='1'>" . round($result4[0] / 3600, 2) . "</font></a></td>";
                                            $t4 = $t4 + $result4[0];
                                            $query_ = "SELECT SUM(AttendanceMaster.AOvertime) FROM AttendanceMaster WHERE EmployeeID = " . $main_cur[0] . " AND Flag = 'Purple' AND ADate >= " . $txtF . " AND ADate <= " . $txtT;
                                            $result5 = selectData($conn, $query_);
                                            displayDate($dateFromArray[$i]);
                                            displayDate($dateToArray[$i]);
                                            round($result5[0] / 3600, 2);
                                            print "<td><a title='Approved OT on Purple Flag (Public Holidays)'><font face='Verdana' size='1' color='Purple'>" . round($result5[0] / 3600, 2) . "</font></a></td>";
                                            $t5 = $t5 + $result5[0];
                                            $query_ = "SELECT F5,F6,F7,F8,F9,F10 FROM tuser WHERE id = " . $main_cur[0] . "";
                                            $resultF5 = selectData($conn, $query_);
                                            print "<td><a title='Sat Rate'><font face='Verdana' size='1'>" . $resultF5[0] . "</font></a></td>";
                                            print "<td><a title='Sun Rate'><font face='Verdana' size='1'>" . $resultF5[1] . "</font></a></td>";
                                            print "<td><a title='PR Rate'><font face='Verdana' size='1'>" . $resultF5[2] . "</font></a></td>";                                       
                                            
                                            if (round($result3[0] / 3600, 2) < 4) {
                                                print "<td><a title='Sat Amount'><font face='Verdana' size='1'>0</font></a></td>";
                                            }
                                            if (round($result3[0] / 3600, 2) >= 4 && round($result3[0] / 3600, 2) <= 10) {
                                                if ($resultF5[0] != '.' && $resultF5[0] != NULL) {
                                                    print "<td><a title='Sat Amount'><font face='Verdana' size='1'>" . $resultF5[0] . "</font></a></td>";
                                                } else {
                                                    print "<td><a title='Sat Amount'><font face='Verdana' size='1'>0</font></a></td>";
                                                }
                                            }
                                            if (round($result3[0] / 3600, 2) > 10) {
                                                if ($resultF5[0] != '.' && $resultF5[0] != NULL) {
                                                    print "<td><a title='Sat Amount'><font face='Verdana' size='1'>4500</font></a></td>";
                                                } else {
                                                    print "<td><a title='Sat Amount'><font face='Verdana' size='1'>0</font></a></td>";
                                                }
                                            }
                                            if (round($result4[0] / 3600, 2) < 4) {
                                                print "<td><a title='Sun Amount'><font face='Verdana' size='1'>0</font></a></td>";
                                            }
                                            if (round($result4[0] / 3600, 2) >= 4 && round($result4[0] / 3600, 2) <= 10) {
                                                if ($resultF5[1] != '.' && $resultF5[1] != NULL) {
                                                    print "<td><a title='Sun Amount'><font face='Verdana' size='1'>" . $resultF5[1] . "</font></a></td>";
                                                } else {
                                                    print "<td><a title='Sun Amount'><font face='Verdana' size='1'>0</font></a></td>";
                                                }
                                            }
                                            if (round($result4[0] / 3600, 2) > 10) {
                                                if ($resultF5[1] != '.' && $resultF5[1] != NULL) {
                                                    print "<td><a title='Sun Amount'><font face='Verdana' size='1'>5000</font></a></td>";
                                                } else {
                                                    print "<td><a title='Sun Amount'><font face='Verdana' size='1'>0</font></a></td>";
                                                }
                                            }
                                            if (round($result5[0] / 3600, 2) < 4) {
                                                print "<td><a title='PR Amount'><font face='Verdana' size='1'>0</font></a></td>";
                                            }
                                            if (round($result5[0] / 3600, 2) >= 4 && round($result5[0] / 3600, 2) <= 10) {
                                                if ($resultF5[2] != '.' && $resultF5[2] != NULL) {
                                                    print "<td><a title='PR Amount'><font face='Verdana' size='1'>" . $resultF5[2] . "</font></a></td>";
                                                } else {
                                                    print "<td><a title='PR Amount'><font face='Verdana' size='1'>0</font></a></td>";
                                                }
                                            }
                                            if (round($result5[0] / 3600, 2) > 10) {
                                                if ($resultF5[2] != '.' && $resultF5[2] != NULL) {
                                                    print "<td><a title='PR Amount'><font face='Verdana' size='1'>5000</font></a></td>";
                                                } else {
                                                    print "<td><a title='PR Amount'><font face='Verdana' size='1'>0</font></a></td>";
                                                }
                                            }
                                            print "<td><a title='Salary Slab'><font face='Verdana' size='1'>" . $resultF5[3] . "</font></a></td>";                                            
                                            if ($resultF5[3] == '.' || $resultF5[3] == NULL) {
                                                print "<td><a title='Sat OT Amount'><font face='Verdana' size='1'>0</font></a></td>";
                                            }
                                                                                       
                                            if ($resultF5[3] > 0 && $resultF5[3] < 183000) {
                                                print "<td><a title='Sat OT Amount'><font face='Verdana' size='1'>0</font></a></td>";
                                            }
                                            if ($resultF5[3] != '.' && $resultF5[3] != NULL) { 
                                                if (round($result3[0] / 3600, 2) < 4) {
                                                    print "<td><a title='Sat OT Amount'><font face='Verdana' size='1'>0</font></a></td>";
                                                }
                                            }
                                            if (round($result3[0] / 3600, 2) >= 4) {
                                                if ($resultF5[3] != '.' && $resultF5[3] != NULL) {                                                    
                                                    if ($resultF5[3] >= 183000 && $resultF5[3] <= 300000) {
                                                        print "<td><a title='Sat OT Amount'><font face='Verdana' size='1'>1500</font></a></td>";
                                                    }
                                                    if ($resultF5[3] > 300000 && $resultF5[3] <= 500000) {
                                                        print "<td><a title='Sat OT Amount'><font face='Verdana' size='1'>2000</font></a></td>";
                                                    }
                                                    if ($resultF5[3] > 500000) {
                                                        print "<td><a title='Sat OT Amount'><font face='Verdana' size='1'>2500</font></a></td>";
                                                    }
                                                }
                                            }
                                            if ($resultF5[3] == '.' || $resultF5[3] == NULL) {
                                                print "<td><a title='Sat OT Amount'><font face='Verdana' size='1'>0</font></a></td>";
                                            }
                                            if ($resultF5[3] > 0 && $resultF5[3] < 183000) {
                                                print "<td><a title='Sat OT Amount'><font face='Verdana' size='1'>0</font></a></td>";
                                            }
                                            if ($resultF5[3] != '.' && $resultF5[3] != NULL) { 
                                                if (round($result4[0] / 3600, 2) < 4) {
                                                    print "<td><a title='Sat OT Amount'><font face='Verdana' size='1'>0</font></a></td>";
                                                }
                                            }
                                            if (round($result4[0] / 3600, 2) >= 4) {
                                                if ($resultF5[3] != '.' && $resultF5[3] != NULL) {
                                                    if ($resultF5[3] >= 183000 && $resultF5[3] <= 300000) {
                                                        print "<td><a title='Sun OT Amount'><font face='Verdana' size='1'>1500</font></a></td>";
                                                    }
                                                    if ($resultF5[3] > 300000 && $resultF5[3] <= 500000) {
                                                        print "<td><a title='Sun OT Amount'><font face='Verdana' size='1'>2000</font></a></td>";
                                                    }
                                                    if ($resultF5[3] > 500000) {
                                                        print "<td><a title='Sun OT Amount'><font face='Verdana' size='1'>2500</font></a></td>";
                                                    }
                                                }
                                            }
                                            if ($resultF5[3] == '.' || $resultF5[3] == NULL) {
                                                print "<td><a title='Sat OT Amount'><font face='Verdana' size='1'>0</font></a></td>";
                                            }
                                            if ($resultF5[3] > 0 && $resultF5[3] < 183000) {
                                                print "<td><a title='Sat OT Amount'><font face='Verdana' size='1'>0</font></a></td>";
                                            }
                                            if ($resultF5[3] != '.' && $resultF5[3] != NULL) { 
                                                if (round($result5[0] / 3600, 2) < 4) {
                                                    print "<td><a title='Sat OT Amount'><font face='Verdana' size='1'>0</font></a></td>";
                                                }
                                            }
                                            if (round($result5[0] / 3600, 2) >= 4) {
                                                if ($resultF5[3] != '.' && $resultF5[3] != NULL) {
                                                    if ($resultF5[3] >= 183000 && $resultF5[3] <= 300000) {
                                                        print "<td><a title='PH OT Amount'><font face='Verdana' size='1'>1500</font></a></td>";
                                                    }
                                                    if ($resultF5[3] > 300000 && $resultF5[3] <= 500000) {
                                                        print "<td><a title='PH OT Amount'><font face='Verdana' size='1'>2000</font></a></td>";
                                                    }
                                                    if ($resultF5[3] > 500000) {
                                                        print "<td><a title='PH OT Amount'><font face='Verdana' size='1'>2500</font></a></td>";
                                                    }
                                                }
                                            }
                                            print "<td><a title='Misc Staff'><font face='Verdana' size='1'>" . $resultF5[5] . "</font></a></td>";
                                            if ($resultF5[5] != '.' && $resultF5[5] != NULL) {
                                                if ($resultF5[5] <= 39999) {
                                                    $satCal = (round($result3[0] / 3600, 2)/176 * 1.5 * $resultF5[4]) + 500;
                                                    print "<td><a title='Sat Cal'><font face='Verdana' size='1'>" . round($satCal,2) . "</font></a></td>";
                                                }else{
                                                    print "<td><a title='Sat Cal'><font face='Verdana' size='1'>0</font></a></td>";
                                                }
                                            }else{
                                                print "<td><a title='Sat Cal'><font face='Verdana' size='1'>0</font></a></td>";
                                            }
                                            if ($resultF5[5] != '.' && $resultF5[5] != NULL) {
                                                if ($resultF5[5] <= 39999) {
                                                    $sunCal = (round($result4[0] / 3600, 2)/176 * 2.0 * $resultF5[4]) + 550;
                                                    print "<td><a title='Sun Cal'><font face='Verdana' size='1'>" . round($sunCal,2) . "</font></a></td>";
                                                }else{
                                                    print "<td><a title='Sun Cal'><font face='Verdana' size='1'>0</font></a></td>";
                                                }
                                            }else{
                                                print "<td><a title='Sun Cal'><font face='Verdana' size='1'>0</font></a></td>";
                                            }
                                            if ($resultF5[5] != '.' && $resultF5[5] != NULL) {
                                                if ($resultF5[5] <= 39999) {
                                                    $phCal = (round($result5[0] / 3600, 2)/176 * 2.0 * $resultF5[4]) + 550 + 150;
                                                    print "<td><a title='PH Cal'><font face='Verdana' size='1'>" . round($phCal,2) . "</font></a></td>";
                                                }else{
                                                    print "<td><a title='PH Cal'><font face='Verdana' size='1'>0</font></a></td>";
                                                }
                                            }else{
                                                print "<td><a title='PH Cal'><font face='Verdana' size='1'>0</font></a></td>";
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
        if ($query != "") {
            $query .= " FROM AttendanceMaster WHERE EmployeeID = " . $main_cur[0] . " AND ADate >= " . $dateFromArray[$i] . " AND ADate <= " . $dateToArray[$i];
            $result = selectData($conn, $query);
            displayDate($dateFromArray[$i]);
            displayDate($dateToArray[$i]);
            round($result[0] / 3600, 2);
            print "<td><a title='" . displayDate($dateFromArray[$i]) . " - " . displayDate($dateToArray[$i]) . "'><font face='Verdana' size='1'>" . round($result[0] / 3600, 2) . "</font></a></td>";
            $total = $total + $result[0];
        }
//            }
//        }
        if ($lstType != "" && $lstGroupBy == "") {
            round($total / 3600, 2);
//            print "<td><a title='Total'><font face='Verdana' size='1'><b>" . round($total / 3600, 2) . "</b></font></a></td></tr>";
        } else {
            round($t1 / 3600, 2);
            round($t2 / 3600, 2);
            round($t3 / 3600, 2);
            round($t4 / 3600, 2);
            round($t5 / 3600, 2);
//            print "<td><a title='Total Normal Hours'><font face='Verdana' size='1'><b>" . round($t1 / 3600, 2) . "</b></font></a></td><td><a title='Total Approved OT on Week Days'><font face='Verdana' size='1'><b>" . round($t2 / 3600, 2) . "</b></font></a></td><td><a title='Total Approved OT on Saturdays'><font face='Verdana' size='1'><b>" . round($t3 / 3600, 2) . "</b></font></a></td><td><a title='Total Approved OT on Sundays'><font face='Verdana' size='1'><b>" . round($t4 / 3600, 2) . "</b></font></a></td><td><a title='Total Approved OT on Purple Flag (Public Holidays)'><font face='Verdana' size='1' color='Purple'><b>" . round($t5 / 3600, 2) . "</b></font></a></td></tr>";
        }
        $total = 0;
        $t1 = 0;
        $t2 = 0;
        $t3 = 0;
        $t4 = 0;
        $t5 = 0;
    }
    print "</table>";
    if ($excel != "yes") {
        print "<br><p align='center'><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $row_count . "</b></font>";
    }
    if ($prints != "yes") {
        print "<br><input type='button' value='Print Report' onClick='checkPrint(0)'>&nbsp;&nbsp;<input type='button' value='Excel' onClick='checkPrint(1)'>";
    }
    print "</p>";
}
print "</form>";
echo "</center></body></html>";
?>