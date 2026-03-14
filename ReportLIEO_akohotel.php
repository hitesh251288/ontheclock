<?php

ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "18";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
$ex3 = $_SESSION[$session_variable . "Ex3"];
$NightShiftMaxOutTime = $_SESSION[$session_variable . "NightShiftMaxOutTime"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=ReportLIEO.php&message=Session Expired or Security Policy Violated");
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
    $message = "Late In/ Early Out Report <br> (It is recommended that you DO NOT use a long Date Period)";
}
$lstShift = $_POST["lstShift"];
$lstDepartment = $_POST["lstDepartment"];
$lstDivision = $_POST["lstDivision"];
$lstEmployeeIDFrom = $_POST["lstEmployeeIDFrom"];
$lstEmployeeIDTo = $_POST["lstEmployeeIDTo"];
$txtEmployeeCode = $_POST["txtEmployeeCode"];
$txtEmployee = $_POST["txtEmployee"];
$lstSort = $_POST["lstSort"];
$txtFrom = $_POST["txtFrom"];
$txtTo = $_POST["txtTo"];
if ($txtFrom == "") {
    $txtFrom = displayToday();
}
if ($txtTo == "") {
    $txtTo = displayToday();
}
$txtRemark = $_POST["txtRemark"];
$txtPhone = $_POST["txtPhone"];
$txtSNo = $_POST["txtSNo"];
if ($_POST["ex3"] != "") {
    $ex3 = $_POST["ex3"];
}
if (!is_numeric($ex3)) {
    $ex3 = 120;
}
if ($ex3 == 0) {
    $ex3 = 120;
}
$lstEmployeeStatus = "";
if (isset($_POST["lstEmployeeStatus"])) {
    $lstEmployeeStatus = $_POST["lstEmployeeStatus"];
} else {
    $lstEmployeeStatus = "ACT";
}
$lstClockingType = $_POST["lstClockingType"];
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
print "<html><title>Late In/ Early Out Report</title>";
if ($prints != "yes") {
    print "<body>";
} else {
    if ($excel != "yes") {
        print "<body onLoad='javascript:window.print()'>";
    } else {
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=ReportLIEO.xls");
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
    print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='ReportLIEO.php'><input type='hidden' name='act' value='searchRecord'>";
    print "<tr>";
    $query = "SELECT id, name from tgroup WHERE id > 1 ORDER BY name";
    displayList("lstShift", "Shift: ", $lstShift, $prints, $conn, $query, "", "25%", "75%");
    print "</tr>";
    displaySearchFields($conn, $prints, $session_variable, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $lstEmployeeName, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
    print "<tr>";
    print "<td colspan='2'><table width='100%' cellspacing='-1' cellpadding='-1' border='0'><tr>";
    displayTextbox("txtFrom", "Date From <font size='1'>(DD/MM/YYYY)</font>: ", $txtFrom, $prints, 12, "25%", "20%");
    displayTextbox("ex3", "Minute(s) <b>MORE</b> THAN: ", $ex3, $prints, 5, "30%", "25%");
    print "</tr></table></td>";
    print "</tr>";
    print "<tr>";
    print "<td colspan='2'><table width='100%' cellspacing='-1' cellpadding='-1' border='0'><tr>";
    displayTextbox("txtTo", "Date To <font size='1'>(DD/MM/YYYY)</font>: ", $txtTo, $prints, 12, "25%", "20%");
    print "<td width='40%'>&nbsp;</td><td width='25%'>&nbsp;</td>";
    print "</tr></table></td>";
    print "</tr>";
    if ($prints != "yes") {
        displayClockingType($lstClockingType);
        print "<tr>";
        displayEmployeeStatus($conn, "lstEmployeeStatus", $lstEmployeeStatus, "", "");
        print "</tr>";
        print "<tr>";
        $array = array(array("tuser.id, tenter.e_date, tenter.e_time", "Employee Code"), array("tuser.name, tuser.id, tenter.e_date, tenter.e_time", "Employee Name - Code"), array("tuser.dept, tuser.id, tenter.e_date, tenter.e_time", "Dept - Code"), array("tuser.company, tuser.dept, tuser.id, tenter.e_date, tenter.e_time", "Div/Desg - Dept - Code"), array("tuser.company, tuser.dept, tenter.e_group, tuser.id, tenter.e_date, tenter.e_time", "Div - Dept - Shift - Code"));
        displaySort($array, $lstSort, 5);
        print "</tr>";
        print "<tr><td>&nbsp;</td><td><input name='btSearch' type='submit' value='Search Record'>&nbsp;&nbsp;<input type='button' value='Excel' onClick='checkPrint(1)'>&nbsp;&nbsp;<input type='button' value='CSV' onClick='checkPrint(3)'></td></tr></td></tr>";
    }
    print "</table><br>";
}
if ($act == "searchRecord") {
    $count = 0;
    if ($prints != "yes") {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800'>";
    } else {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%'>";
    }
    print "<tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Shift</font></td> <td><font face='Verdana' size='2'>Date</font></td> <td><font face='Verdana' size='2'>Start</font></td> <td><font face='Verdana' size='2'>Grace</font></td> <td><font face='Verdana' size='2'><b>Arrived</b></font></td> <td><font face='Verdana' size='2'><b>Late <font size=1>(Min)</font></b></font></td> <td><font face='Verdana' size='2'>Terminal</font></td></tr>";
    for ($date_count = insertDate($txtFrom); $date_count <= insertDate($txtTo); $date_count++) {
        if (checkdate(substr($date_count, 4, 2), substr($date_count, 6, 2), substr($date_count, 0, 4))) {
            $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tenter.e_date, tenter.e_time, tgate.name, tuser.idno, tuser.remark, tgroup.GraceTo, tgroup.Start, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, tgroup, tenter, tgate WHERE tenter.e_group = tgroup.id AND tenter.e_id = tuser.id AND tgroup.GraceTo >= '0000' AND tenter.g_id = tgate.id  AND tgate.exit = 0 " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
            if ($lstShift != "") {
                $query = $query . " AND tgroup.id = " . $lstShift;
            }
            $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
            if ($date_count != "") {
                $query = $query . " AND tenter.e_date = '" . $date_count . "'";
            }
            $query = queryClockingType($query, $lstClockingType);
            $query = $query . employeeStatusQuery($lstEmployeeStatus);
            $query = $query . " ORDER BY " . $lstSort;
            $last_id = "";
            $last_date = "";
            $result = mysqli_query($conn, $query);

            if (0 < mysqli_num_rows($result)) {
                if ($excel != "yes") {
//                    print "<tr><td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> </tr>";
//                    print "<tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Shift</font></td> <td><font face='Verdana' size='2'>Date</font></td> <td><font face='Verdana' size='2'>Start</font></td> <td><font face='Verdana' size='2'>Grace</font></td> <td><font face='Verdana' size='2'><b>Arrived</b></font></td> <td><font face='Verdana' size='2'><b>Late <font size=1>(Min)</font></b></font></td> <td><font face='Verdana' size='2'>Terminal</font></td></tr>";
                }
                while ($cur = mysqli_fetch_row($result)) {
                    if (!($cur[0] == $last_id && $cur[5] == $last_date)) {
                        if (getLateTime($date_count, $cur[10], $ex3) < $cur[6] && 0 < getLateMin($date_count, $cur[10], $cur[6])) {
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
                            displayVirdiTime($cur[11] . "00");
                            displayVirdiTime($cur[10] . "00");
                            displayVirdiTime($cur[6]);
                            if ($cur[4] != "OFF") {
                                print "<tr><td><a title='ID'><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'><font face='Verdana' size='1'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $cur[1] . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $cur[8] . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $cur[2] . "</font></a></td> <td><a title='Div/Desg'><font face='Verdana' size='1'>" . $cur[3] . "</font></a></td> <td><a title='Rmk'><font face='Verdana' size='1'>" . $cur[9] . "</font></a></td> <td><a title='Shift'><font face='Verdana' size='1'>" . $cur[4] . "</font></a></td> <td><a title='Date'><font face='Verdana' size='1'>" . displayDate($cur[5]) . "</font></a></td> <td><a title='Start'><font face='Verdana' size='1'>" . displayVirdiTime($cur[11] . "00") . "</font></a></td> <td><a title='Grace'><font face='Verdana' size='1'>" . displayVirdiTime($cur[10] . "00") . "</font></a></td> <td><a title='Arrived'><font face='Verdana' size='1'><b>" . displayVirdiTime($cur[6]) . "</b></font></a></td> <td><a title='Late Minutes'><font face='Verdana' size='1'><b>";
                                if ($lstLateness == "From Start Time") {
                                    getLateMin($date_count, $cur[11], $cur[6]);
                                    print getLateMin($date_count, $cur[11], $cur[6]);
                                } else {
                                    getLateMin($date_count, $cur[10], $cur[6]);
                                    print getLateMin($date_count, $cur[10], $cur[6]);
                                }
                                print "</b></font></a></td> <td><a title='Terminal'><font face='Verdana' size='1'>" . $cur[7] . "</font></a></td></tr>";
                            }
                            $count++;
                        }
                        $last_id = $cur[0];
                        $last_date = $cur[5];
                    }
                }
            }
            if ($_SESSION[$session_variable . "ExitTerminal"] == "Yes") {
                $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tenter.e_date, tenter.e_time, tgate.name, tuser.idno, tuser.remark, tgroup.Close, tgroup.NightFlag, tgroup.GraceTo FROM tuser, tgroup, tenter, tgate WHERE tenter.e_group = tgroup.id AND tenter.e_id = tuser.id AND tenter.g_id = tgate.id AND tgroup.Close >= '0000' AND tuser.id > 0 AND tgate.exit = 0 " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . "  AND tuser.id IN (SELECT DISTINCT(tuser.id) FROM tuser, tenter, tgate WHERE tenter.e_id = tuser.id AND tenter.g_id = tgate.id AND tgate.exit = 1) AND ((tenter.e_time < '240000' AND tgroup.NightFlag =0) OR (tenter.e_time < '" . $NightShiftMaxOutTime . "00' AND tgroup.NightFlag = 1)) ";
            } else {
                $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tenter.e_date, tenter.e_time, tgate.name, tuser.idno, tuser.remark, tgroup.Close, tgroup.NightFlag, tgroup.GraceTo FROM tuser, tgroup, tenter, tgate WHERE tenter.e_group = tgroup.id AND tenter.e_id = tuser.id AND tgroup.Close >= '0000' AND tuser.id > 0 AND tenter.g_id = tgate.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " AND ((tenter.e_time < '240000' AND tgroup.NightFlag =0) OR (tenter.e_time < '" . $NightShiftMaxOutTime . "00' AND tgroup.NightFlag = 1)) ";
            }
            if ($lstShift != "") {
                $query = $query . " AND tgroup.id = " . $lstShift;
            }
            $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
            if ($date_count != "") {
                $query = $query . " AND tenter.e_date = '" . $date_count . "'";
            }
            $query = queryClockingType($query, $lstClockingType);
            $query = $query . employeeStatusQuery($lstEmployeeStatus);
            $query = $query . " ORDER BY " . $lstSort;
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
            $data12 = "";
            $ecount = 0;
            $result = mysqli_query($conn, $query);
            if (0 < mysqli_num_rows($result)) {
//                print "<tr><td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> </tr>";
//                print "<tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Shift</font></td> <td><font face='Verdana' size='2'>Close</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='2'>Date</font></td> <td><font face='Verdana' size='2'><b>Depart</b></font></td> <td><font face='Verdana' size='2'><b>Early <font size=1>(Min)</font></b></font></td> <td><font face='Verdana' size='2'>Terminal</font></td></tr>";
                while ($cur = mysqli_fetch_row($result)) {
                    if ($cur[0] == $last_id && $cur[5] == $last_date) {
                        $ecount++;
                    } else {
                        if ($data6 < getEarlyTime($date_count, $data10, $ex3) && strlen($data5) == 8 && 0 < $ecount) {
                            if ($data3 == "") {
                                $data3 = "&nbsp;";
                            }
                            if ($data8 == "") {
                                $data8 = "&nbsp;";
                            }
                            if ($data9 == "") {
                                $data9 = "&nbsp;";
                            }
                            addZero($data0, $_SESSION[$session_variable . "EmployeeCodeLength"]);
                            displayVirdiTime($data10 . "00");
                            displayDate($data5);
                            displayVirdiTime($data6);
                            getEarlyMin($date_count, $data10, $data6);
                            if (isset($data4) != "OFF") {
                                print "<tr><td><a title='ID'><input type='hidden' name='txh" . $count . "' value='" . $data0 . "'><font face='Verdana' size='1'>" . addZero($data0, $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $data1 . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $data8 . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $data2 . "</font></a></td> <td><a title='Div/Desg'><font face='Verdana' size='1'>" . $data3 . "</font></a></td> <td><a title='Rmk'><font face='Verdana' size='1'>" . $data9 . "</font></a></td> <td><a title='Shift'><font face='Verdana' size='1'>" . $data4 . "</font></a></td><td><a title='Date'><font face='Verdana' size='1'>" . displayDate($data5) . "</font></a></td> <td><a title='Start'><font face='Verdana' size='1'>" . displayVirdiTime($data10 . "00") . "</font></a></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><a title='Depart'><font face='Verdana' size='1'><b>" . displayVirdiTime($data6) . "</b></font></a></td> <td><a title='Early Minutes'><font face='Verdana' size='1'><b>" . getEarlyMin($date_count, $data10, $data6) . "</b></font></a></td> <td><a title='Terminal'><font face='Verdana' size='1'>" . $data7 . "</font></a></td></tr>";
                                $count++;
                            }
                            
                        }
                        $last_id = $cur[0];
                        $last_date = $cur[5];
                        $ecount = 0;
                    }
//                    echo "<pre>";print_R($cur);
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
                    $data12 = $cur[12];
                }
            }
            if (0 < mysqli_num_rows($result) && $data6 < getEarlyTime($date_count, $data10, $ex3) && 0 < $ecount) {
                if ($data3 == "") {
                    $data3 = "&nbsp;";
                }
                if ($data8 == "") {
                    $data8 = "&nbsp;";
                }
                if ($data9 == "") {
                    $data9 = "&nbsp;";
                }
                addZero($data0, $_SESSION[$session_variable . "EmployeeCodeLength"]);
                displayVirdiTime($data10 . "00");
                displayDate($data5);
                displayVirdiTime($data6);
                getEarlyMin($date_count, $data10, $data6);
                if (isset($data4) != "OFF") {
                    print "<tr><td><a title='ID'><input type='hidden' name='txh" . $count . "' value='" . $data0 . "'><font face='Verdana' size='1'>" . addZero($data0, $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $data1 . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $data8 . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $data2 . "</font></a></td> <td><a title='Div/Desg'><font face='Verdana' size='1'>" . $data3 . "</font></a></td> <td><a title='Rmk'><font face='Verdana' size='1'>" . $data9 . "</font></a></td> <td><a title='Shift'><font face='Verdana' size='1'>" . $data4 . "</font></a></td><td><a title='Date'><font face='Verdana' size='1'>" . displayDate($data5) . "</font></a></td> <td><a title='Grace'><font face='Verdana' size='1'>" . displayVirdiTime($data10 . "00") . "</font></a></td> <td><font face='Verdana' size='1'>&nbsp;</font></td>  <td><a title='Depart'><font face='Verdana' size='1'><b>" . displayVirdiTime($data6) . "</b></font></a></td> <td><a title='Early Minutes'><font face='Verdana' size='1'><b>" . getEarlyMin($date_count, $data10, $data6) . "</b></font></a></td> <td><a title='Terminal'><font face='Verdana' size='1'>" . $data7 . "</font></a></td></tr>";
                    $count++;
                }
                
            }
        }
    }
    print "</table>";
    if ($excel != "yes") {
        print "<br><p align='center'><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $count . "</b></font>";
    }
    if ($prints != "yes") {
        print "<br><input type='button' value='Print Report' onClick='checkPrint(0)'>&nbsp;&nbsp;<input type='button' value='Excel' onClick='checkPrint(1)'>";
    }
    print "</p>";
}
print "</form>";
echo "</center></body></html>";
?>