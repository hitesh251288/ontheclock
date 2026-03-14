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
    $dateParts = explode('/', $txtFrom);
    if (count($dateParts) == 3) {
        $startDate = $dateParts[2] . '-' . $dateParts[1] . '-' . $dateParts[0];
    } else {
        echo "Invalid date format";
    }
    $dateend = explode('/', $txtTo);
    if (count($dateend) == 3) {
        $endDate = $dateend[2] . '-' . $dateend[1] . '-' . $dateend[0];
    } else {
        echo "Invalid date format";
    }

    // Define your start and end dates
    $startDate = strtotime($startDate);
    $endDate = strtotime($endDate);

    // Initialize a counter for Sundays
    $sundayCount = 0;

    // Iterate through each day
    $currentDate = $startDate;
    while ($currentDate <= $endDate) {
        // Check if the current day is a Sunday (Sunday is represented as 0)
        if (date('w', $currentDate) == 0) {
            $sundayCount++;
        }
        // Move to the next day
        $currentDate = strtotime('+1 day', $currentDate);
    }
    
//    if ($excel != "yes") {
//        print "<p><font face='Verdana' size='1'><b><u>All Data Displayed in Hours</u> <br><br><font size='2'>N = Normal Hours <br>WKD = Week Day Approved Overtime <br>SAT = Saturday / OT1 Approved Overtime <br>SUN = Sunday / OT2 Approved Overtime <br>PR = Purple Flag (Public Holiday) Approved Overtime</font>";
//        print "</b></font></p>";
//    }
    $dateFromArray = "";
    $dateToArray = "";
    $i = 0;
    $txtF = insertDate($txtFrom);
    for ($txtT = insertDate($txtTo); true; $i++) {
        $dateFromArray[$i] = $txtF;
        $y = substr($txtF, 0, 4);
        $m = substr($txtF, 4, 2);
        $d = substr($txtF, 6, 2);
        $d = $d - 1;
        if ($d <= 0) {
            $d = 31;
        }
        if ($d * 1 != 31) {
            $m = $m + 1;
            if (12 <= $m * 1) {
                $m = "01";
                $y = $y + 1;
            } else {
                $m = addZero($m, 2);
            }
        }
        $txtT = $y . $m . $d;
        if (insertDate($txtTo) < $txtT * 1) {
            $txtT = insertDate($txtTo);
            $dateToArray[$i] = $txtT;
            break;
        }
        $dateToArray[$i] = $txtT;
        if ($d * 1 == 31) {
            $m = $m + 1;
            if (12 <= $m * 1) {
                $m = "01";
                $y = $y + 1;
            } else {
                $m = addZero($m, 2);
            }
        }
        $txtF = $y . $m . substr($txtF, 6, 2);
    }
    if ($prints != "yes") {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800'>";
    } else {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%'>";
    }
    if ($lstGroupBy == "Dept") {
        print "<tr><td><font face='Verdana' size='2'>Dept</font></td>";
    } else {
        print "<tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td><td><font face='Verdana' size='2'>Rate</font></td> <td><font face='Verdana' size='2'>Location</font></td> <td><font face='Verdana' size='2'>Bank</font></td> <td><font face='Verdana' size='2'>Account No.</font></td><td><font face='Verdana' size='2'>Account Name</font></td><td><font face='Verdana' size='2'>Next of Kin ACC.</font></td><td><font face='Verdana' size='2'>Bonus</font></td><td><font face='Verdana' size='2'>Working Days</font></td><td><font face='Verdana' size='2'>Present Days</font></td><td><font face='Verdana' size='2'>PH</font></td><td><font face='Verdana' size='2'>PH Amount</font></td><td><font face='Verdana' size='2'>SUN</font></td><td><font face='Verdana' size='2'>Normal Days Payment</font></td><td><font face='Verdana' size='2'>Loan/Ded</font></td><td><font face='Verdana' size='2'>Total Amount</font></td>";
    }
    for ($i = 0; $i < count($dateFromArray); $i++) {
        if ($lstType == "") {
            substr(displayDate($dateFromArray[$i]), 0, 5);
            substr(displayDate($dateToArray[$i]), 0, 5);
//            print "<td colspan='5'><font face='Verdana' size='1'>" . substr(displayDate($dateFromArray[$i]), 0, 5) . " - " . substr(displayDate($dateToArray[$i]), 0, 5) . "</font></td>";
        } else {
            substr(displayDate($dateFromArray[$i]), 0, 5);
            substr(displayDate($dateToArray[$i]), 0, 5);
//            print "<td><font face='Verdana' size='1'>" . substr(displayDate($dateFromArray[$i]), 0, 5) . "<br>" . substr(displayDate($dateToArray[$i]), 0, 5) . "</font></td>";
        }
    }
    if ($lstType == "") {
//        print "<td colspan='5'><font face='Verdana' size='2'>Total</font></td></tr>";
    } else {
//        print "<td><font face='Verdana' size='2'>Total</font></td></tr>";
    }
    if ($lstType == "") {
        if ($lstGroupBy == "Dept") {
            print "<tr><td><font face='Verdana' size='1'>&nbsp;</font></td>";
        } else {
//            print "<tr><td><font face='Verdana' size='1'>&nbsp;</font></td><td><font face='Verdana' size='1'>&nbsp;</font></td><td><font face='Verdana' size='1'>&nbsp;</font></td><td><font face='Verdana' size='1'>&nbsp;</font></td><td><font face='Verdana' size='1'>&nbsp;</font></td><td><font face='Verdana' size='1'>&nbsp;</font></td><td><font face='Verdana' size='1'>&nbsp;</font></td><td><font face='Verdana' size='1'>&nbsp;</font></td>";
        }
        for ($i = 0; $i < count($dateFromArray); $i++) {
//            print "<td><font face='Verdana' size='1'>N</font></td><td><font face='Verdana' size='1'>WKD</font></td><td><font face='Verdana' size='1'>SAT</font></td><td><font face='Verdana' size='1'>SUN</font></td><td><font face='Verdana' size='1' color='Purple'>PR</font></td>";
        }
//        print "<td><font face='Verdana' size='1'><b>N</b></font></td><td><font face='Verdana' size='1'><b>WKD</b></font></td><td><font face='Verdana' size='1'><b>SAT</b></font></td><td><font face='Verdana' size='1'><b>SUN</b></font></td><td><font face='Verdana' size='1' color='Purple'><b>PR</b></font></td>";
    }
    
    $sundays = 0;
    if ($lstGroupBy == "") {
        $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, '', tuser.idno, tuser.remark, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10,COUNT(DISTINCT tenter.e_date) as PresentDays, DATEDIFF('".insertDate($txtTo)."', '".insertDate($txtFrom)."') + 1 AS WorkingDays FROM tuser LEFT JOIN tenter ON tenter.e_id=tuser.id WHERE tuser.id > 0 " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
        $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
        $query = $query . employeeStatusQuery($lstEmployeeStatus);
        if ($lstSort != "") {
            $query = $query . " AND tenter.e_date >='".insertDate($txtFrom)."' AND tenter.e_date <='".insertDate($txtTo)."' group by tuser.id ORDER BY " . $lstSort;
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
        if ($lstGroupBy == "Dept") {
            print "<tr><td><a title='Dept'><font face='Verdana' size='1'>" . $main_cur[0] . "</font></a></td>";
            for ($i = 0; $i < count($dateFromArray); $i++) {
                $query_ = "SELECT SUM(AttendanceMaster.Normal) FROM AttendanceMaster, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND tuser.dept = '" . $main_cur[0] . "' AND ADate >= " . $dateFromArray[$i] . " AND ADate <= " . $dateToArray[$i];
                $result = selectData($conn, $query_);
                displayDate($dateFromArray[$i]);
                displayDate($dateToArray[$i]);
                addComma(round($result[0] / 3600, 2));
//                print "<td><a title='Normal Hours [" . displayDate($dateFromArray[$i]) . " - " . displayDate($dateToArray[$i]) . "]'><font face='Verdana' size='1'>" . addComma(round($result[0] / 3600, 2)) . "</font></a></td>";
                $t1 = $t1 + $result[0];
                $query_ = "SELECT SUM(AttendanceMaster.AOvertime) FROM AttendanceMaster, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND tuser.dept = '" . $main_cur[0] . "' AND AttendanceMaster.Day <> AttendanceMaster.OT1 AND AttendanceMaster.Day <> AttendanceMaster.OT2 AND AttendanceMaster.Flag <> 'Purple' AND AttendanceMaster.ADate >= " . $dateFromArray[$i] . " AND AttendanceMaster.ADate <= " . $dateToArray[$i];
                $result = selectData($conn, $query_);
                displayDate($dateFromArray[$i]);
                displayDate($dateToArray[$i]);
                addComma(round($result[0] / 3600, 2));
//                print "<td><a title='Approved OT on WeekDays [" . displayDate($dateFromArray[$i]) . " - " . displayDate($dateToArray[$i]) . "]'><font face='Verdana' size='1'>" . addComma(round($result[0] / 3600, 2)) . "</font></a></td>";
                $t2 = $t2 + $result[0];
                $query_ = "SELECT SUM(AttendanceMaster.AOvertime) FROM AttendanceMaster, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND tuser.dept = '" . $main_cur[0] . "' AND AttendanceMaster.Day = AttendanceMaster.OT1 AND AttendanceMaster.Flag <> 'Purple' AND AttendanceMaster.ADate >= " . $dateFromArray[$i] . " AND AttendanceMaster.ADate <= " . $dateToArray[$i];
                $result = selectData($conn, $query_);
                displayDate($dateFromArray[$i]);
                displayDate($dateToArray[$i]);
                addComma(round($result[0] / 3600, 2));
//                print "<td><a title='Approved OT on Saturdays [" . displayDate($dateFromArray[$i]) . " - " . displayDate($dateToArray[$i]) . "]'><font face='Verdana' size='1'>" . addComma(round($result[0] / 3600, 2)) . "</font></a></td>";
                $t3 = $t3 + $result[0];
                $query_ = "SELECT SUM(AttendanceMaster.AOvertime) FROM AttendanceMaster, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND tuser.dept = '" . $main_cur[0] . "' AND AttendanceMaster.Flag = 'Purple' AND AttendanceMaster.ADate >= " . $dateFromArray[$i] . " AND AttendanceMaster.ADate <= " . $dateToArray[$i];
                $result = selectData($conn, $query_);
                displayDate($dateFromArray[$i]);
                displayDate($dateToArray[$i]);
                addComma(round($result[0] / 3600, 2));
                print "<td><a title='Approved OT on Purple Flag (Public Holidays) [" . displayDate( [$i]) . " - " . displayDate($dateToArray[$i]) . "]'><font face='Verdana' size='1' color='Purple'>" . addComma(round($result[0] / 3600, 2)) . "</font></a></td>";
                $t5 = $t5 + $result[0];
                $query_ = "SELECT SUM(AttendanceMaster.AOvertime) FROM AttendanceMaster, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND tuser.dept = '" . $main_cur[0] . "' AND AttendanceMaster.Day = AttendanceMaster.OT2 AND AttendanceMaster.Flag <> 'Purple' AND AttendanceMaster.ADate >= " . $dateFromArray[$i] . " AND AttendanceMaster.ADate <= " . $dateToArray[$i];
                $result = selectData($conn, $query_);
                displayDate($dateFromArray[$i]);
                displayDate($dateToArray[$i]);
                addComma(round($result[0] / 3600, 2));
                print "<td><a title='Approved OT on Sundays [" . displayDate($dateFromArray[$i]) . " - " . displayDate($dateToArray[$i]) . "]'><font face='Verdana' size='1'>" . addComma(round($result[0] / 3600, 2)) . "</font></a></td>";
                $t4 = $t4 + $result[0];
                
            }
        } else {
            addZero($main_cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
            print "<tr><td><a title='ID'><input type='hidden' name='txh" . $count . "' value='" . $main_cur[0] . "'><font face='Verdana' size='1'>" . addZero($main_cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $main_cur[1] . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $main_cur[5] . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $main_cur[2] . "</font></a></td> <td><a title='Div/Desg'><font face='Verdana' size='1'>" . $main_cur[3] . "</font></a></td> <td><a title='Rmk'><font face='Verdana' size='1'>" . $main_cur[6] . "</font></a></td><td><a title='Rmk'><font face='Verdana' size='1'>" . $main_cur[7] . "</font></a></td><td><a title='Rmk'><font face='Verdana' size='1'>" . $main_cur[8] . "</font></a></td><td><a title='Rmk'><font face='Verdana' size='1'>" . $main_cur[9] . "</font></a></td><td><a title='Rmk'><font face='Verdana' size='1'>" . $main_cur[10] . "</font></a></td><td><a title='Rmk'><font face='Verdana' size='1'>" . $main_cur[11] . "</font></a></td><td><a title='Rmk'><font face='Verdana' size='1'>" . $main_cur[12] . "</font></a></td><td><a title='Rmk'><font face='Verdana' size='1'>" . $main_cur[13] . "</font></a></td><td><a title='Rmk'><font face='Verdana' size='1'>" . $main_cur[18] . "</font></a></td><td><a title='Rmk'><font face='Verdana' size='1'>".$main_cur[17]."</font></a></td>";
            for ($i = 0; $i < count($dateFromArray); $i++) {
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
                                                    $query_ = "SELECT SUM(AttendanceMaster.Normal) FROM AttendanceMaster WHERE EmployeeID = " . $main_cur[0] . " AND ADate >= " . $dateFromArray[$i] . " AND ADate <= " . $dateToArray[$i];
                                                    $result = selectData($conn, $query_);
                                                    displayDate($dateFromArray[$i]);
                                                    displayDate($dateToArray[$i]);
                                                    round($result[0] / 3600, 2);
//                                                    print "<td><a title='Normal Hours [" . displayDate($dateFromArray[$i]) . " - " . displayDate($dateToArray[$i]) . "]'><font face='Verdana' size='1'>" . round($result[0] / 3600, 2) . "</font></a></td>";
                                                    $t1 = $t1 + $result[0];
                                                    $query_ = "SELECT SUM(AttendanceMaster.AOvertime) FROM AttendanceMaster WHERE EmployeeID = " . $main_cur[0] . " AND Day <> OT1 AND Day <> OT2 AND Flag <> 'Purple' AND ADate >= " . $dateFromArray[$i] . " AND ADate <= " . $dateToArray[$i];
                                                    $result = selectData($conn, $query_);
                                                    displayDate($dateFromArray[$i]);
                                                    displayDate($dateToArray[$i]);
                                                    round($result[0] / 3600, 2);
//                                                    print "<td><a title='Approved OT on WeekDays [" . displayDate($dateFromArray[$i]) . " - " . displayDate($dateToArray[$i]) . "]'><font face='Verdana' size='1'>" . round($result[0] / 3600, 2) . "</font></a></td>";
                                                    $t2 = $t2 + $result[0];
                                                    $query_ = "SELECT SUM(AttendanceMaster.AOvertime) FROM AttendanceMaster WHERE EmployeeID = " . $main_cur[0] . " AND Day = OT1 AND Flag <> 'Purple' AND ADate >= " . $dateFromArray[$i] . " AND ADate <= " . $dateToArray[$i];
                                                    $result = selectData($conn, $query_);
                                                    displayDate($dateFromArray[$i]);
                                                    displayDate($dateToArray[$i]);
                                                    round($result[0] / 3600, 2);
//                                                    print "<td><a title='Approved OT on Saturdays [" . displayDate($dateFromArray[$i]) . " - " . displayDate($dateToArray[$i]) . "]'><font face='Verdana' size='1'>" . round($result[0] / 3600, 2) . "</font></a></td>";
                                                    $t3 = $t3 + $result[0];
                                                    $query_ = "SELECT SUM(AttendanceMaster.AOvertime) FROM AttendanceMaster WHERE EmployeeID = " . $main_cur[0] . " AND Flag = 'Purple' AND ADate >= " . $dateFromArray[$i] . " AND ADate <= " . $dateToArray[$i];
                                                    $result = selectData($conn, $query_);
                                                    displayDate($dateFromArray[$i]);
                                                    displayDate($dateToArray[$i]);
                                                    round($result[0] / 3600, 2);
                                                    print "<td><a title='Approved OT on Purple Flag (Public Holidays) [" . displayDate($dateFromArray[$i]) . " - " . displayDate($dateToArray[$i]) . "]'><font face='Verdana' size='1' color='Purple'>" . round($result[0] / 3600, 2) . "</font></a></td>";
                                                    $t5 = $t5 + $result[0];
                                                    $PHAmount = $main_cur[7]*2*round($result[0] / 3600, 2);
                                                    print "<td><a title='PH Amount'><font face='Verdana' size='1'>".$PHAmount."</font></a></td>";
                                                    $query_ = "SELECT SUM(AttendanceMaster.AOvertime) FROM AttendanceMaster WHERE EmployeeID = " . $main_cur[0] . " AND Day = OT2 AND Flag <> 'Purple' AND ADate >= " . $dateFromArray[$i] . " AND ADate <= " . $dateToArray[$i];
                                                    $result = selectData($conn, $query_);
                                                    displayDate($dateFromArray[$i]);
                                                    displayDate($dateToArray[$i]);
                                                    round($result[0] / 3600, 2);
                                                    print "<td><a title='Sundays'><font face='Verdana' size='1'>" . $sundayCount . "</font></a></td>";
                                                    $t4 = $t4 + $result[0];
                                                    $normalDaysPay = $main_cur[7]*$main_cur[17];
                                                    print "<td><a title='NormalDaysPay'><font face='Verdana' size='1'>".$normalDaysPay."</font></a></td>";
                                                    $totalAmount = $normalDaysPay + $PHAmount + $main_cur[13] - $main_cur[15];
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
            }
            print "<td><a title='Rmk'><font face='Verdana' size='1'>".$main_cur[15]."</font></a></td><td><a title='Rmk'><font face='Verdana' size='1'>".$totalAmount."</font></a></td>";
        }
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