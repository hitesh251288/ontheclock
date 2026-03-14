<?php
ob_start("ob_gzhandler");
set_time_limit(0);
error_reporting(E_ALL);
include "Functions.php";
$current_module = "24";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=Proxy.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$iconn = openIConnection();
$jconn = openIConnection();
$kconn = openIConnection();
$lconn = openIConnection();
$mconn = openIConnection();
$act = $_GET["act"];
if ($act == "") {
    $act = $_POST["act"];
}
$prints = $_GET["prints"];
$excel = $_GET["excel"];
$message = $_GET["message"];
if ($message == "") {
    $message = "Mark Proxy Attendance <br>Setting a SHIFT PROXY OVERWRITES existing Attendance/ Clocking. <br>If you are trying to do a SHIFT PROXY for an Employee who has already clocked IN, the System will ADD a PROXY and DELETE the existing Clocking Record<br>PROXY CANNOT be set for selected Shift for Employees with 4 Clockings per Day and Allow NO BREAK Exception set as NO";
}
$lstShift = $_POST["lstShift"];
$lstProxyShift = $_POST["lstProxyShift"];
$lstDepartment = $_POST["lstDepartment"];
$lstDeptTerminal = $_POST["lstDeptTerminal"];
$lstExitTerminal = $_POST["lstExitTerminal"];
$lstDivision = $_POST["lstDivision"];
$lstTerminal = $_POST["lstTerminal"];
$lstEmployeeIDFrom = $_POST["lstEmployeeIDFrom"];
$lstEmployeeIDTo = $_POST["lstEmployeeIDTo"];
$txtEmployee = $_POST["txtEmployee"];
$txtEmployeeCode = $_POST["txtEmployeeCode"];
$lstSort = $_POST["lstSort"];
$txtFrom = $_POST["txtFrom"];
$txtTime = $_POST["txtTime"];
if ($txtFrom == "") {
    $txtFrom = displayToday();
}
$txtSearchDate = $_POST["txtSearchDate"];
$nextDate = insertDate($txtFrom);
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
$lstEmployeeStatus = "";
if (isset($_POST["lstEmployeeStatus"])) {
    $lstEmployeeStatus = $_POST["lstEmployeeStatus"];
} else {
    $lstEmployeeStatus = "ACT";
}
$txtRemark = $_POST["txtRemark"];
$txtPhone = $_POST["txtPhone"];
if ($act == "saveRecord") {
    $count = $_POST["txtCounter"];
    if (0 < $count) {
        if ($txtTime != "") {
            for ($i = 0; $i < $count; $i++) {
                if ($_POST["chk" . $i] != "") {
                    $query = "SELECT group_id FROM tuser WHERE id = " . $_POST["txhID" . $i];
                    $result = selectData($conn, $query);
                    $cur_shift = $result[0];
                    if ($lstDeptTerminal != "") {
                        $query = "INSERT INTO tenter (e_date, e_time, g_id, e_id, e_group, e_user, e_mode, e_type, e_result, e_etc) VALUES ('" . $nextDate . "', '" . $txtTime . "00', " . $lstDeptTerminal . ", " . $_POST["txhID" . $i] . ", " . $cur_shift . ", '0', '3', '3', '0', 'P')";
                        if (updateIData($iconn, $query, true)) {
                            $text = "Added Proxy for ID: " . $_POST["txhID" . $i] . " - Date: " . displayDate($nextDate) . " - Time: " . displayVirdiTime($txtTime . "00") . " - Shift: " . $cur_shift;
                            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                            updateIData($jconn, $query, true);
                        }
                    } else {
                        $query = "INSERT INTO tenter (e_date, e_time, g_id, e_id, e_group, e_user, e_mode, e_type, e_result, e_etc) VALUES ('" . $nextDate . "', '" . $txtTime . "00', " . $lstExitTerminal . ", " . $_POST["txhID" . $i] . ", " . $cur_shift . ", '0', '3', '3', '0', 'P')";
                        if (updateIData($iconn, $query, true)) {
                            $text = "Added Proxy for ID: " . $_POST["txhID" . $i] . " - Date: " . displayDate($nextDate) . " - Time: " . displayVirdiTime($txtTime . "00") . " - Shift: " . $cur_shift;
                            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                            updateIData($jconn, $query, true);
                        }
                    }
                }
            }
        } else {
            for ($i = 0; $i < $count; $i++) {
                if ($_POST["chk" . $i] != "") {
                    for ($j = 0; $j < $_POST["txhEnterCount"]; $j++) {
                        $query = "DELETE FROM tenter WHERE ed = " . $_POST["txhEnter" . $j] . " AND e_id = " . $_POST["txhID" . $i];
                        updateIData($iconn, $query, true);
                        if ($_POST["txhEmployeeID" . $j] == $_POST["txhID" . $i]) {
                            $query = "INSERT INTO ProxyDelete (e_id, e_date, e_time, group_id, g_id, ed) VALUES (" . $_POST["txhID" . $i] . ", " . $_POST["txhDate" . $j] . ", '" . $_POST["txhTime" . $j] . "', " . $_POST["txhShift" . $j] . ", " . $_POST["txhTerminal" . $j] . ", " . $_POST["txhEnter" . $j] . ")";
                            updateIData($jconn, $query, true);
                        }
                    }
                    for ($j = 0; $j < $_POST["txhDayCount"]; $j++) {
                        $query = "DELETE FROM DayMaster WHERE DayMasterID = " . $_POST["txhDay" . $j] . " AND e_id = " . $_POST["txhID" . $i];
                        updateIData($kconn, $query, true);
                    }
                    for ($j = 0; $j < $_POST["txhWeekCount"]; $j++) {
                        $query = "DELETE FROM WeekMaster WHERE WeekMasterID = " . $_POST["txhWeek" . $j] . " AND e_id = " . $_POST["txhID" . $i];
                        updateIData($lconn, $query, true);
                    }
                    for ($j = 0; $j < $_POST["txhAttendanceCount"]; $j++) {
                        $query = "DELETE FROM AttendanceMaster WHERE AttendanceID = " . $_POST["txhAttendance" . $j] . " AND EmployeeID = " . $_POST["txhID" . $i];
                        updateIData($mconn, $query, true);
                    }
                }
            }
            $query = "SELECT TotalDailyClockin, TotalExitClockin, NoBreakException FROM OtherSettingMaster";
            $result = selectData($conn, $query);
            $txtTotalDailyClockin = $result[0];
            $txtTotalExitClockin = $result[1];
            $dc = $txtTotalDailyClockin;
            $ec = $txtTotalExitClockin;
            $noBreak = $result[2];
            $query = "SELECT Start, FlexiBreak, BreakFrom, BreakTo, Close, NightFlag, WorkMin FROM tgroup WHERE id = " . $lstProxyShift;
            $result = selectData($conn, $query);
            $start = $result[0];
            $fBreak = $result[1];
            $bFrom = $result[2];
            $bTo = $result[3];
            $close = $result[4];
            $night = $result[5];
            $work = $result[6];
            $nextDay = "";
            $bFromDay = "";
            $bToDay = "";
            if ($night == 1) {
                $next = strtotime(substr($nextDate, 6, 2) . "-" . substr($nextDate, 4, 2) . "-" . substr($nextDate, 0, 4) . " + 1 day");
                $a = getDate($next);
                $m = $a["mon"];
                if ($m < 10) {
                    $m = "0" . $m;
                }
                $d = $a["mday"];
                if ($d < 10) {
                    $d = "0" . $d;
                }
                $nextDay = $a["year"] . $m . $d;
            }
            for ($i = 0; $i < $count; $i++) {
                if ($_POST["chk" . $i] != "") {
                    if ($noBreak == "No" && $dc == 4) {
                        $message = "PROXY could NOT be set for SOME Employees with 4 Clockings per Day and Allow NO BREAK Exception as NO<br>";
                    } else {
                        if ($ec == 2 && $lstExitTerminal != "") {
                            if ($night == 1) {
                                $query = "INSERT INTO tenter (e_date, e_time, g_id, e_id, e_group, e_user, e_mode, e_type, e_result, e_etc) VALUES ('" . $nextDate . "', '120500', " . $lstExitTerminal . ", " . $_POST["txhID" . $i] . ", " . $lstProxyShift . ", '0', '3', '3', '0', 'P')";
                                if (updateIData($iconn, $query, true)) {
                                    $text = "Added Proxy for ID: " . $_POST["txhID" . $i] . " - Date: " . displayDate($nextDate) . " - Time: 12:05:00 - Shift: " . $lstProxyShift;
                                    $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                                    if (updateIData($jconn, $query, true)) {
                                        $query = "INSERT INTO tenter (e_date, e_time, g_id, e_id, e_group, e_user, e_mode, e_type, e_result, e_etc) VALUES ('" . $nextDay . "', '115500', " . $lstExitTerminal . ", " . $_POST["txhID" . $i] . ", " . $lstProxyShift . ", '0', '3', '3', '0', 'P')";
                                        if (updateIData($kconn, $query, true)) {
                                            $text = "Added Proxy for ID: " . $_POST["txhID" . $i] . " - Date: " . displayDate($nextDate) . " - Time: 11:55:00 - Shift: " . $lstProxyShift;
                                            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                                            updateIData($lconn, $query, true);
                                        }
                                    }
                                }
                            } else {
                                $query = "INSERT INTO tenter (e_date, e_time, g_id, e_id, e_group, e_user, e_mode, e_type, e_result, e_etc) VALUES ('" . $nextDate . "', '000500', " . $lstExitTerminal . ", " . $_POST["txhID" . $i] . ", " . $lstProxyShift . ", '0', '3', '3', '0', 'P')";
                                if (updateIData($iconn, $query, true)) {
                                    $text = "Added Proxy for ID: " . $_POST["txhID" . $i] . " - Date: " . displayDate($nextDate) . " - Time: 00:05:00 - Shift: " . $lstProxyShift;
                                    $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                                    if (updateIData($jconn, $query, true)) {
                                        $query = "INSERT INTO tenter (e_date, e_time, g_id, e_id, e_group, e_user, e_mode, e_type, e_result, e_etc) VALUES ('" . $nextDate . "', '235500', " . $lstExitTerminal . ", " . $_POST["txhID" . $i] . ", " . $lstProxyShift . ", '0', '3', '3', '0', 'P')";
                                        if (updateIData($kconn, $query, true)) {
                                            $text = "Added Proxy for ID: " . $_POST["txhID" . $i] . " - Date: " . displayDate($nextDate) . " - Time: 23:55:00 - Shift: " . $lstProxyShift;
                                            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                                            updateIData($lconn, $query, true);
                                        }
                                    }
                                }
                            }
                        }
                        $query = "INSERT INTO tenter (e_date, e_time, g_id, e_id, e_group, e_user, e_mode, e_type, e_result, e_etc) VALUES ('" . $nextDate . "', '" . $start . "00', " . $lstDeptTerminal . ", " . $_POST["txhID" . $i] . ", " . $lstProxyShift . ", '0', '3', '3', '0', 'P')";
                        if (updateIData($iconn, $query, true)) {
                            $text = "Added Proxy for ID: " . $_POST["txhID" . $i] . " - Date: " . displayDate($nextDate) . " - Time: " . displayVirdiTime($start . "00") . " - Shift: " . $lstProxyShift;
                            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                            updateIData($jconn, $query, true);
                        }
                        if ($night == 1) {
                            $query = "INSERT INTO tenter (e_date, e_time, g_id, e_id, e_group, e_user, e_mode, e_type, e_result, e_etc) VALUES ('" . $nextDay . "', '" . $close . "00', " . $lstDeptTerminal . ", " . $_POST["txhID" . $i] . ", " . $lstProxyShift . ", '0', '3', '3', '0', 'P')";
                            if (updateIData($iconn, $query, true)) {
                                $text = "Added Proxy for ID: " . $_POST["txhID" . $i] . " - Date: " . displayDate($nextDay) . " - Time: " . displayVirdiTime($close . "00") . " - Shift: " . $lstProxyShift;
                                $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                                updateIData($jconn, $query, true);
                            }
                        } else {
                            $query = "INSERT INTO tenter (e_date, e_time, g_id, e_id, e_group, e_user, e_mode, e_type, e_result, e_etc) VALUES ('" . $nextDate . "', '" . $close . "00', " . $lstDeptTerminal . ", " . $_POST["txhID" . $i] . ", " . $lstProxyShift . ", '0', '3', '3', '0', 'P')";
                            if (updateIData($iconn, $query, true)) {
                                $text = "Added Proxy for ID: " . $_POST["txhID" . $i] . " - Date: " . displayDate($nextDate) . " - Time: " . displayVirdiTime($close . "00") . " - Shift: " . $lstProxyShift;
                                $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                                updateIData($jconn, $query, true);
                            }
                        }
                    }
                }
            }
        }
        $message = $message . "PROXY set successfully for the selected Employees";
    }
    $act = "searchRecord";
}
if ($prints != "yes") {
    include 'header.php';
    ?>
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-12 d-flex no-block align-items-center">
                <h4 class="page-title">Mark Proxy Attendance</h4>
                <div class="ms-auto text-end">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Mark Proxy Attendance
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <?php
}
//print "<html><title>Mark Proxy Attendance</title>";
if ($prints != "yes") {
//    print "<body>";
    print'<div class="container-fluid">
                <div class="row">
                        <div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12">
                            <div class="card">
                                    <div class="card-body table-responsive">';
} else {
    if ($excel != "yes") {
        print "<body onLoad='javascript:window.print()'>";
    } else {
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=Proxy.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        print "<body>";
    }
}
print "<form name='frm1' method='post' onSubmit='return checkProxySearch()' action='Proxy.php'><input type='hidden' name='act' value='searchRecord'>";
if ($excel != "yes") {
?>
<div class="card">
    <div class="card-body">
        <?php
            print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b></font></p>";
            if ($prints != "yes") {
                print "<center><h4 class='card-title'>Select ONE or MORE options and click Search Record</h4></center>";
            } else {
                print "<center><font face='Verdana' size='1'><b>Selected Options</b></font></center>";
            }
            
            ?>
            <div class="row">
                <div class="col-3">
                    <?php
                    $query = "SELECT id, name from tgroup WHERE id > 1 ORDER BY name";
                    displayList("lstShift", "Current Shift: ", $lstShift, $prints, $conn, $query, "", "", "");
                    ?>
                </div>
                <?php
                displaySearchFields($conn, $prints, $session_variable, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $lstEmployeeName, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
                ?>
            </div>
            <div class="row">
                <div class="col-2">
                    <?php
                    displayTextbox("txtFrom", "Date <font size='1'>(DD/MM/YYYY)</font>: ", $txtFrom, $prints, 12, "25%", "75%");
                    ?>
                </div>
                <?php
                if ($prints != "yes") {
                    print "<div class='col-2'>";
                    displayEmployeeStatus($conn, "lstEmployeeStatus", $lstEmployeeStatus, "", "");
                    print "</div>";
                    print "<div class='col-2'>";
                    $array = array(array("tuser.id", "Employee Code"), array("tuser.name, tuser.id", "Employee Name - Code"), array("tuser.dept, tuser.id", "Dept - Code"), array("tuser.company, tuser.dept, tuser.id", "Div/Desg - Dept - Code"), array("tuser.company, tuser.dept, tuser.group_id, tuser.id", "Div - Dept - Current Shift - Code"));
                    displaySort($array, $lstSort, 5);
                    print "</div>";
                    print "</div>";
                    print "<div class='row'>";
                    print "<div class='col-12'>";
                    print "<center><input type='hidden' name='txtSearchDate'><input name='btSearch' type='submit' value='Search Record' class='btn btn-primary'></center>";
                    print "</div>";
                    print "</div>";
                }
                ?>
        </div>
    </div>
<?php } 
print "</div></div></div></div>";

if ($act == "searchRecord") {
    $cutoff = $_SESSION[$session_variable . "NightShiftMaxOutTime"] . "00";
    print '<div class="row"><div class="col-md-12"><div class="card"><div class="card-body table-responsive">';
    if ($prints != "yes") {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800' class='table table-striped table-bordered dataTable' id='zero_config'>";
    } else {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%' class='table table-striped table-bordered dataTable'>";
    }
    print "<tr><td colspan='11' align='center'><font face='Verdana' size='1' color='#FF0000'><b>Existing DAY SHIFTS Raw Log(s) from " . $txtFrom . " to " . $txtTo . " to be DELETED</b></font></td></tr>";
    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tenter.e_date, tenter.e_time, tgate.name, tuser.idno, tuser.remark, tenter.e_etc, tenter.ed, tenter.g_id, tenter.e_group FROM tuser, tgroup, tenter, tgate WHERE tenter.e_group = tgroup.id AND tenter.e_id = tuser.id AND tenter.g_id = tgate.id AND (tgroup.NightFlag = FALSE OR  tgroup.NightFlag = 0) " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
    if ($lstShift != "") {
        $query = $query . " AND tgroup.id = " . $lstShift;
    }
    $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
    if ($txtFrom != "") {
        $query = $query . " AND tenter.e_date = '" . insertDate($txtFrom) . "'";
    }
    $query = $query . employeeStatusQuery($lstEmployeeStatus);
    $query = $query . " ORDER BY tuser.id,  tenter.e_date, tenter.e_time";
    $result = mysqli_query($conn, $query);
//    echo "<pre>";print_R($result);
    if (mysqli_num_rows($result) > 0) {
        print "<tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Shift</font></td> <td><font face='Verdana' size='2'>Date</font></td> <td><font face='Verdana' size='2'>Time</font></td> <td><font face='Verdana' size='2'>Terminal</font></td> <td><font face='Verdana' size='2'>Proxy</font></td> </tr>";
        for ($delCount = 0; $cur = mysqli_fetch_row($result); $delCount++) {
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
            print "<tr><td><a title='ID'> <input type='hidden' name='txhEmployeeID" . $delCount . "' value='" . $cur[0] . "'> <input type='hidden' name='txhEnter" . $delCount . "' value='" . $cur[11] . "'> <input type='hidden' name='txhTerminal" . $delCount . "' value='" . $cur[12] . "'> <input type='hidden' name='txhShift" . $delCount . "' value='" . $cur[13] . "'> <input type='hidden' name='txhDate" . $delCount . "' value='" . $cur[5] . "'> <input type='hidden' name='txhTime" . $delCount . "' value='" . $cur[6] . "'> <font face='Verdana' size='1'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $cur[1] . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $cur[8] . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $cur[2] . "</font></a></td> <td><a title='Div/Desg'><font face='Verdana' size='1'>" . $cur[3] . "</font></a></td> <td><a title='Rmk'><font face='Verdana' size='1'>" . $cur[9] . "</font></a></td> <td><a title='Shift'><font face='Verdana' size='1'>" . $cur[4] . "</font></a></td> <td><a title='Date'><font face='Verdana' size='1'>" . displayDate($cur[5]) . "</font></a></td> <td><a title='Time'><font face='Verdana' size='1'>" . displayVirdiTime($cur[6]) . "</font></a></td> <td><a title='Terminal'><font face='Verdana' size='1'>" . $cur[7] . "</font></a></td>";
            if ($cur[10] == "P") {
                print "<td><a title='Proxy'><font face='Verdana' size='1'>Yes</font></a>";
            } else {
                print "<td><a title='Proxy'><font face='Verdana' size='1'>No</font></a>";
            }
            print "</tr>";
        }
    }
    print "<tr><td colspan='11' align='center'><font face='Verdana' size='1' color='#FF0000'><b>Existing NIGHT SHIFTS Raw Log(s) from " . $txtFrom . " to " . $txtTo . " to be DELETED</b></font></td></tr>";
    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tenter.e_date, tenter.e_time, tgate.name, tuser.idno, tuser.remark, tenter.e_etc, tenter.ed, tenter.g_id, tenter.e_group FROM tuser, tgroup, tenter, tgate WHERE tenter.e_group = tgroup.id AND tenter.e_id = tuser.id AND tenter.g_id = tgate.id AND tgroup.NightFlag = 1 " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
    if ($lstShift != "") {
        $query = $query . " AND tgroup.id = " . $lstShift;
    }
    if ($lstDepartment != "") {
        $query = $query . " AND tuser.dept LIKE '" . $lstDepartment . "%'";
    }
    if ($lstDivision != "") {
        $query = $query . " AND tuser.company = '" . $lstDivision . "%'";
    }
    if ($lstEmployeeIDFrom != "") {
        $query = $query . " AND tuser.id >= " . $lstEmployeeIDFrom;
    }
    if ($lstEmployeeIDTo != "") {
        $query = $query . " AND tuser.id <= " . $lstEmployeeIDTo;
    }
    if ($txtEmployeeCode != "") {
        $query = $query . " AND tuser.id = " . $txtEmployeeCode * 1;
    }
    if ($txtEmployee != "") {
        $query = $query . " AND tuser.name like '%" . $txtEmployee . "%'";
    }
    if ($txtFrom != "") {
        $query = $query . " AND tenter.e_date >= '" . insertDate($txtFrom) . "' AND tenter.e_date <= '" . getNextDay(insertDate($txtFrom), 1) . "'";
    }
    $query = $query . employeeStatusQuery($lstEmployeeStatus);
    $query = $query . " ORDER BY tuser.id,  tenter.e_date, tenter.e_time";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        print "<tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Shift</font></td> <td><font face='Verdana' size='2'>Date</font></td> <td><font face='Verdana' size='2'>Time</font></td> <td><font face='Verdana' size='2'>Terminal</font></td> <td><font face='Verdana' size='2'>Proxy</font></td> </tr>";
        $nd_flag = false;
        $id = "";
        $day = "";
        while ($cur = mysqli_fetch_row($result)) {
            if ($cur[3] == "") {
                $cur[3] = "&nbsp;";
            }
            if ($cur[8] == "") {
                $cur[8] = "&nbsp;";
            }
            if ($cur[9] == "") {
                $cur[9] = "&nbsp;";
            }
            if ($cur[0] != $id) {
                $nd_flag = false;
            } else {
                if ($cur[6] <= $cutoff) {
                    $nd_flag = true;
                }
            }
            if ($nd_flag == false && $id != $cur[0] && $cutoff <= $cur[6] || $nd_flag == true && $id == $cur[0] && $cur[6] <= $cutoff) {
                addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
                displayDate($cur[5]);
                displayVirdiTime($cur[6]);
                print "<tr><td><a title='ID'><input type='hidden' name='txhEmployeeID" . $delCount . "' value='" . $cur[0] . "'> <input type='hidden' name='txhEnter" . $delCount . "' value='" . $cur[11] . "'> <input type='hidden' name='txhTerminal" . $delCount . "' value='" . $cur[12] . "'> <input type='hidden' name='txhShift" . $delCount . "' value='" . $cur[13] . "'> <input type='hidden' name='txhDate" . $delCount . "' value='" . $cur[5] . "'> <input type='hidden' name='txhTime" . $delCount . "' value='" . $cur[6] . "'> <font face='Verdana' size='1'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $cur[1] . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $cur[8] . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $cur[2] . "</font></a></td> <td><a title='Div/Desg'><font face='Verdana' size='1'>" . $cur[3] . "</font></a></td> <td><a title='Rmk'><font face='Verdana' size='1'>" . $cur[9] . "</font></a></td> <td><a title='Shift'><font face='Verdana' size='1'>" . $cur[4] . "</font></a></td> <td><a title='Date'><font face='Verdana' size='1'>" . displayDate($cur[5]) . "</font></a></td> <td><a title='Time'><font face='Verdana' size='1'>" . displayVirdiTime($cur[6]) . "</font></a></td> <td><a title='Terminal'><font face='Verdana' size='1'>" . $cur[7] . "</font></a></td>";
                if ($cur[10] == "P") {
                    print "<td><a title='Proxy'><font face='Verdana' size='1'>Yes</font></a>";
                } else {
                    print "<td><a title='Proxy'><font face='Verdana' size='1'>No</font></a>";
                }
                print "</tr>";
                $delCount++;
                $id = $cur[0];
            }
        }
    }
    print "<input type='hidden' name='txhEnterCount' value='" . $delCount . "'>";
    print "</table>";
    if ($prints != "yes") {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800' class='table table-striped table-bordered dataTable'>";
    } else {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%' class='table table-striped table-bordered dataTable'>";
    }
    print "<tr><td colspan='9' align='center'><font face='Verdana' size='1' color='#FF0000'><b>Existing (Daily Basis) Processed Log(s) from " . $txtFrom . " to " . $txtTo . " to be DELETED</b></font></td></tr>";
    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, DayMaster.TDate, tuser.idno, tuser.remark, DayMaster.Flag, DayMaster.DayMasterID FROM tuser, tgroup, DayMaster WHERE DayMaster.Flag NOT LIKE 'Delete' AND DayMaster.group_id = tgroup.id AND DayMaster.e_id = tuser.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
    if ($lstShift != "") {
        $query = $query . " AND tgroup.id = " . $lstShift;
    }
    if ($lstDepartment != "") {
        $query = $query . " AND tuser.dept LIKE '" . $lstDepartment . "%'";
    }
    if ($lstDivision != "") {
        $query = $query . " AND tuser.company = '" . $lstDivision . "%'";
    }
    if ($lstEmployeeIDFrom != "") {
        $query = $query . " AND tuser.id >= " . $lstEmployeeIDFrom;
    }
    if ($lstEmployeeIDTo != "") {
        $query = $query . " AND tuser.id <= " . $lstEmployeeIDTo;
    }
    if ($txtEmployeeCode != "") {
        $query = $query . " AND tuser.id = " . $txtEmployeeCode * 1;
    }
    if ($txtEmployee != "") {
        $query = $query . " AND tuser.name like '%" . $txtEmployee . "%'";
    }
    if ($txtFrom != "") {
        $query = $query . " AND DayMaster.TDate = " . insertDate($txtFrom);
    }
    $query = $query . employeeStatusQuery($lstEmployeeStatus);
    $query = $query . " ORDER BY tuser.id,  DayMaster.TDate ";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        print "<tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Shift</font></td> <td><font face='Verdana' size='2'>Date</font></td> <td><font face='Verdana' size='2'>Flag</font></td> </tr>";

        for ($delCount = 0; $cur = mysqli_fetch_row($result); $delCount++) {
            if ($cur[3] == "") {
                $cur[3] = "&nbsp;";
            }
            if ($cur[6] == "") {
                $cur[6] = "&nbsp;";
            }
            if ($cur[7] == "") {
                $cur[7] = "&nbsp;";
            }
            addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
            displayDate($cur[5]);
            print "<tr><td><a title='ID'><input type='hidden' name='txhDay" . $delCount . "' value='" . $cur[9] . "'><font face='Verdana' size='1'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $cur[1] . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $cur[6] . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $cur[2] . "</font></a></td> <td><a title='Div/Desg'><font face='Verdana' size='1'>" . $cur[3] . "</font></a></td> <td><a title='Rmk'><font face='Verdana' size='1'>" . $cur[7] . "</font></a></td> <td><a title='Shift'><font face='Verdana' size='1'>" . $cur[4] . "</font></a></td> <td><a title='Date'><font face='Verdana' size='1'>" . displayDate($cur[5]) . "</font></a></td> <td><a title='Flag'><font face='Verdana' size='1'>" . $cur[8] . "</font></a></td> </tr>";
        }
    }
    print "<input type='hidden' name='txhDayCount' value='" . $delCount . "'>";
    print "<tr><td colspan='9' align='center'><font face='Verdana' size='1' color='#FF0000'><b>Existing (Weekly Basis) Processed Log(s) from " . $txtFrom . " to " . $txtTo . " to be DELETED</b></font></td></tr>";
    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, WeekMaster.LogDate, tuser.idno, tuser.remark, WeekMaster.Flag, WeekMaster.WeekMasterID FROM tuser, tgroup, WeekMaster WHERE WeekMaster.Flag NOT LIKE 'Delete' AND WeekMaster.group_id = tgroup.id AND WeekMaster.e_id = tuser.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
    if ($lstShift != "") {
        $query = $query . " AND tgroup.id = " . $lstShift;
    }
    if ($lstDepartment != "") {
        $query = $query . " AND tuser.dept LIKE '" . $lstDepartment . "%'";
    }
    if ($lstDivision != "") {
        $query = $query . " AND tuser.company = '" . $lstDivision . "%'";
    }
    if ($lstEmployeeIDFrom != "") {
        $query = $query . " AND tuser.id >= " . $lstEmployeeIDFrom;
    }
    if ($lstEmployeeIDTo != "") {
        $query = $query . " AND tuser.id <= " . $lstEmployeeIDTo;
    }
    if ($txtEmployeeCode != "") {
        $query = $query . " AND tuser.id = " . $txtEmployeeCode * 1;
    }
    if ($txtEmployee != "") {
        $query = $query . " AND tuser.name like '%" . $txtEmployee . "%'";
    }
    if ($txtFrom != "") {
        $query = $query . " AND WeekMaster.LogDate = " . insertDate($txtFrom);
    }
    $query = $query . employeeStatusQuery($lstEmployeeStatus);
    $query = $query . " ORDER BY tuser.id, WeekMaster.LogDate ";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        print "<tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Shift</font></td> <td><font face='Verdana' size='2'>Date</font></td> <td><font face='Verdana' size='2'>Flag</font></td> </tr>";

        for ($delCount = 0; $cur = mysqli_fetch_row($result); $delCount++) {
            if ($cur[3] == "") {
                $cur[3] = "&nbsp;";
            }
            if ($cur[6] == "") {
                $cur[6] = "&nbsp;";
            }
            if ($cur[7] == "") {
                $cur[7] = "&nbsp;";
            }
            addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
            displayDate($cur[5]);
            print "<tr><td><a title='ID'><input type='hidden' name='txhWeek" . $delCount . "' value='" . $cur[9] . "'><font face='Verdana' size='1'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $cur[1] . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $cur[6] . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $cur[2] . "</font></a></td> <td><a title='Div/Desg'><font face='Verdana' size='1'>" . $cur[3] . "</font></a></td> <td><a title='Rmk'><font face='Verdana' size='1'>" . $cur[7] . "</font></a></td> <td><a title='Shift'><font face='Verdana' size='1'>" . $cur[4] . "</font></a></td> <td><a title='Date'><font face='Verdana' size='1'>" . displayDate($cur[5]) . "</font></a></td> <td><a title='Flag'><font face='Verdana' size='1'>" . $cur[8] . "</font></a></td></tr>";
        }
    }
    print "<input type='hidden' name='txhWeekCount' value='" . $delCount . "'>";
    print "<tr><td colspan='9' align='center'><font face='Verdana' size='1' color='#FF0000'><b>Existing Overtime Calculation(s) from " . $txtFrom . " to " . $txtTo . " to be DELETED</b></font></td></tr>";
    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, AttendanceMaster.ADate, tuser.idno, tuser.remark, AttendanceMaster.Flag, AttendanceMaster.AttendanceID FROM tuser, tgroup, AttendanceMaster WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.Flag NOT LIKE 'Delete' AND AttendanceMaster.EmployeeID = tuser.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
    if ($lstShift != "") {
        $query = $query . " AND tgroup.id = " . $lstShift;
    }
    if ($lstDepartment != "") {
        $query = $query . " AND tuser.dept LIKE '" . $lstDepartment . "%'";
    }
    if ($lstDivision != "") {
        $query = $query . " AND tuser.company = '" . $lstDivision . "%'";
    }
    if ($lstEmployeeIDFrom != "") {
        $query = $query . " AND tuser.id >= " . $lstEmployeeIDFrom;
    }
    if ($lstEmployeeIDTo != "") {
        $query = $query . " AND tuser.id <= " . $lstEmployeeIDTo;
    }
    if ($txtEmployeeCode != "") {
        $query = $query . " AND tuser.id = " . $txtEmployeeCode * 1;
    }
    if ($txtEmployee != "") {
        $query = $query . " AND tuser.name like '%" . $txtEmployee . "%'";
    }
    if ($txtFrom != "") {
        $query = $query . " AND AttendanceMaster.ADate = " . insertDate($txtFrom);
    }
    $query = $query . employeeStatusQuery($lstEmployeeStatus);
    $query = $query . " ORDER BY tuser.id, AttendanceMaster.ADate ";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        print "<tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Shift</font></td> <td><font face='Verdana' size='2'>Date</font></td> <td><font face='Verdana' size='2'>Flag</font></td> </tr>";

        for ($delCount = 0; $cur = mysqli_fetch_row($result); $delCount++) {
            if ($cur[3] == "") {
                $cur[3] = "&nbsp;";
            }
            if ($cur[6] == "") {
                $cur[6] = "&nbsp;";
            }
            if ($cur[7] == "") {
                $cur[7] = "&nbsp;";
            }
            addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
            displayDate($cur[5]);
            print "<tr><td><a title='ID'><input type='hidden' name='txhAttendance" . $delCount . "' value='" . $cur[9] . "'><font face='Verdana' size='1'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $cur[1] . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $cur[6] . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $cur[2] . "</font></a></td> <td><a title='Div/Desg'><font face='Verdana' size='1'>" . $cur[3] . "</font></a></td> <td><a title='Rmk'><font face='Verdana' size='1'>" . $cur[7] . "</font></a></td> <td><a title='Shift'><font face='Verdana' size='1'>" . $cur[4] . "</font></a></td> <td><a title='Date'><font face='Verdana' size='1'>" . displayDate($cur[5]) . "</font></a></td> <td><a title='Flag'><font face='Verdana' size='1'>" . $cur[8] . "</font></a></td> </tr>";
        }
    }
    print "<input type='hidden' name='txhAttendanceCount' value='" . $delCount . "'>";
    print "</table>";
    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tgroup.Start, tgroup.Close, tuser.OT1, tuser.OT2, tgroup.id, tgroup.NightFlag, tuser.idno, tuser.remark FROM tuser, tgroup WHERE tuser.group_id = tgroup.id AND tgroup.ShiftTypeID = 1 " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
    if ($lstShift != "") {
        $query = $query . " AND tgroup.id = " . $lstShift;
    }
    if ($lstDepartment != "") {
        $query = $query . " AND tuser.dept LIKE '" . $lstDepartment . "%'";
    }
    if ($lstDivision != "") {
        $query = $query . " AND tuser.company = '" . $lstDivision . "%'";
    }
    if ($lstEmployeeIDFrom != "") {
        $query = $query . " AND tuser.id >= " . $lstEmployeeIDFrom;
    }
    if ($lstEmployeeIDTo != "") {
        $query = $query . " AND tuser.id <= " . $lstEmployeeIDTo;
    }
    if ($txtEmployeeCode != "") {
        $query = $query . " AND tuser.id = " . $txtEmployeeCode * 1;
    }
    if ($txtEmployee != "") {
        $query = $query . " AND tuser.name like '%" . $txtEmployee . "%'";
    }
    $query = $query . employeeStatusQuery($lstEmployeeStatus);
    $query = $query . " ORDER BY " . $lstSort;
    print "<p align='center'><font face='Verdana' size='1' color='#FF0000'><b>Employee List to mark PROXY for " . $txtFrom . "</b></font>";
    if ($prints != "yes") {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800' class='table table-striped table-bordered dataTable'>";
    } else {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%' class='table table-striped table-bordered dataTable'>";
    }
    print "<tr><td><input type='checkbox' name='chk' onClick='javascript:checkCheckBox()'></input></td><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Department</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Shift</font></td> </tr>";
    $result = mysqli_query($conn, $query);
    for ($count = 0; $cur = mysqli_fetch_row($result); $count++) {
        if ($cur[3] == "") {
            $cur[3] = "&nbsp;";
        }
        if ($cur[11] == "") {
            $cur[11] = "&nbsp;";
        }
        if ($cur[12] == "") {
            $cur[12] = "&nbsp;";
        }
        addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
        print "<tr><td><input type='checkbox' name='chk" . $count . "' id='chk" . $count . "'><td><a title='ID'><input type='hidden' name='txhID" . $count . "' value='" . $cur[0] . "'> <input type='hidden' name='txhStart" . $count . "' value='" . $cur[5] . "'> <input type='hidden' name='txhEnd" . $count . "' value='" . $cur[6] . "'> <input type='hidden' name='txhDeptClocking" . $count . "' value='" . $cur[7] . "'> <input type='hidden' name='txhExitClocking" . $count . "' value='" . $cur[8] . "'> <input type='hidden' name='txhGroup" . $count . "' value='" . $cur[9] . "'> <input type='hidden' name='txhNightFlag" . $count . "' value='" . $cur[10] . "'> <font face='Verdana' size='1'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $cur[1] . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $cur[11] . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $cur[2] . "</font></a></td> <td><a title='Div/Desg'><font face='Verdana' size='1'>" . $cur[3] . "</font></a></td> <td><a title='Rmk'><font face='Verdana' size='1'>" . $cur[12] . "</font></a></td> <td><a title='Shift'><font face='Verdana' size='1'>" . $cur[4] . "</font></a></td> </tr>";
    }
    print "</table>";
    print "</div></div></div></div>";
    print '<div class="row"><div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12"><div class="card"><div class="card-body">';
    if ($excel != "yes") {
        print "<p align='center'><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $count . "</b></font>";
        if ($prints != "yes" && 0 < $count && strpos($userlevel, $current_module . "E") !== false && $_SESSION[$session_variable . "LockDate"] < insertDate($txtFrom)) {
            print "<div class='row'>";
            print "<div class='col-3'>";
            $query = "SELECT id, name from tgate WHERE tgate.exit = 0 ORDER BY name";
            displayList("lstDeptTerminal", "Select Dept Terminal: ", $lstDeptTerminal, $prints, $conn, $query, "", "", "");
            print "</div>";
            print "<div class='col-3'>";
            $query = "SELECT id, name from tgate WHERE tgate.exit = 1 ORDER BY name";
            displayList("lstExitTerminal", "Select Exit Terminal: ", $lstExitTerminal, $prints, $conn, $query, "", "", "");
            print "</div>";
            print "<div class='col-3'>";
            $query = "SELECT id, name from tgroup WHERE id > 1 AND LENGTH(Start) = 4 ORDER BY name";
            displayList("lstProxyShift", "Select Shift on Proxy Date: ", $lstProxyShift, $prints, $conn, $query, "", "", "");
            print "</div>";
            print "<div class='col-3'>";
            print "<label class='form-label'>Time (HHMM):</label><input name='txtTime' size='5' value='" . $txtTime . "'  class='form-control'> &nbsp;<font face='Verdana' size='1'>(Leave this Field BLANK if you wish to Proxy the <b>START</b> and <b>CLOSE</b> clockings for the selected Shift)</font>";
            print "</div>";
            print "<div class='row'>";
            print "<div class='col-12'>";
            print "<center><br><input name='btSubmit' type='button' value='Mark Proxy for Selected Employee(s)' onClick='javascript:checkSubmit()' class='btn btn-primary'></center>";
            print "</div>";
            print "</div>";
        }
        print "</p>";
    }
}
print "<input type='hidden' name='txtCounter' value='" . $count . "'></form>";
print "</div></div></div></div></div>";
include 'footer.php';
echo "\r\n<script>\r\nfunction checkSubmit(){\r\n\tx = document.frm1;\r\n\tif (x.txtTime.value == '' && x.lstDeptTerminal.value == ''){\r\n\t\talert('Please select a Department Terminal');\r\n\t\tx.lstDeptTerminal.focus();\r\n\t}else if (x.txtTime.value != '' && (x.lstDeptTerminal.value == '' && x.lstExitTerminal.value == '')){\r\n\t\talert('Please either select a Department or an Exit Terminal');\r\n\t\tx.lstDeptTerminal.focus();\r\n\t}else if (x.lstProxyShift.value == '' && x.txtTime.value == ''){\r\n\t\talert('Please select the Assigned Shift on Proxy Date OR enter a vaid Proxy Time');\r\n\t\tx.lstProxyShift.focus();\r\n\t}else if (x.lstProxyShift.value != '' && x.txtTime.value != ''){\r\n\t\talert('Please select the Assigned Shift on Proxy Date OR enter a valid Proxy Time. Both Fields CANNOT be entered at the same Time.');\r\n\t\tx.lstProxyShift.focus();\r\n\t}else if (x.lstProxyShift.value == '' && check_valid_time(x.txtTime.value + \"00\") == false){\r\n\t\talert('Please enter a valid Proxy Time');\r\n\t\tx.txtTime.focus();\r\n\t}else{\r\n\t\tif (confirm(\"Mark Proxy Attendance for the Selected Employees for the Selected Date\")){\r\n\t\t\tif (confirm('NO Exit Terminal Entries would be Posted as Exit Terminal is NOT selected')){\r\n\t\t\t\t//if (x.lstProxyShift.value != '' && x.lstExitTerminal.value == ''){\r\n\t\t\t\t\tx.act.value='saveRecord';\r\n\t\t\t\t\tx.btSubmit.disabled = true;\r\n\t\t\t\t\tx.submit();\r\n\t\t\t\t//}\r\n\t\t\t}\r\n\t\t}\r\n\t}\r\n}\r\n\r\nfunction checkCheckBox(){\r\n\tx = document.frm1;\r\n\tif (x.chk.checked){\r\n\t\tfor (i=0;i<x.txtCounter.value;i++){\r\n\t\t\tdocument.getElementById('chk'+i).checked = true;\r\n\t\t}\r\n\t}else{\r\n\t\tfor (i=0;i<x.txtCounter.value;i++){\r\n\t\t\tdocument.getElementById('chk'+i).checked = false;\r\n\t\t}\r\n\t}\r\n}\r\n\r\nfunction checkPrint(a){\r\n\tvar x = document.frm1;\r\n\tif (check_valid_date(x.txtFrom.value) == false){\r\n\t\talert('Invalid Date. Date Format should be DD/MM/YYYY');\r\n\t\tx.txtFrom.focus();\r\n\t//}else if (check_valid_date(x.txtTo.value) == false){\r\n\t\t//alert('Invalid To Date. Date Format should be DD/MM/YYYY');\r\n\t\t//x.txtTo.focus();\r\n\t}else{\r\n\t\tif (a == 0){\r\n\t\t\tif (confirm('Go Green - Think Twice before you Print this Document \\nAre you sure want to Print?')){\r\n\t\t\t\tx.action = 'Proxy.php?prints=yes';\r\n\t\t\t}else{\r\n\t\t\t\treturn;\r\n\t\t\t}\r\n\t\t}else{\r\n\t\t\tx.action = 'Proxy.php?prints=yes&excel=yes';\t\t\t\r\n\t\t}\t\t\r\n\t\tx.target = '_blank';\r\n\t\tx.submit();\r\n\t}\r\n}\r\n\r\nfunction checkProxySearch(a){\r\n\tvar x = document.frm1;\r\n\tif (x.txtFrom.value != '' && check_valid_date(x.txtFrom.value) == false){\r\n\t\talert('Invalid From Date. Date Format should be DD/MM/YYYY');\r\n\t\tx.txtFrom.focus();\r\n\t\treturn false;\r\n\t//}else if (x.txtTo.value != '' && check_valid_date(x.txtTo.value) == false){\r\n\t\t//alert('Invalid To Date. Date Format should be DD/MM/YYYY');\r\n\t\t//x.txtTo.focus();\r\n\t}else{\r\n\t\tx.txtSearchDate.value = a;\r\n\t\tx.btSearch.disabled = true;\r\n\t\treturn true;\r\n\t}\r\n}\r\n\r\nfunction checkDay(){\r\n\tvar x = document.frm1;\r\n\tif (x.txtFrom.value != '' && check_valid_date(x.txtFrom.value) == false){\r\n\t\talert('Invalid From Date. Date Format should be DD/MM/YYYY');\r\n\t\tx.txtFrom.focus();\r\n\t}else if (x.txtTo.value != '' && check_valid_date(x.txtTo.value) == false){\r\n\t\talert('Invalid To Date. Date Format should be DD/MM/YYYY');\r\n\t\tx.txtTo.focus();\r\n\t}else{\r\n\t\tvar date = new Date(x.txtFrom.value);\r\n\t\talert(date);\r\n\t\t//x.submit();\r\n\t}\r\n}\r\n</script>\r\n";
?>