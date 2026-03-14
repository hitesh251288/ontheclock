<?php


ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "25";
set_time_limit(0);
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
$flagLimitType = $_SESSION[$session_variable . "FlagLimitType"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=OffDay.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$iconn = openIConnection();
$jconn = openIConnection();
$act = $_GET["act"];
if ($act == "") {
    $act = $_POST["act"];
}
$prints = $_GET["prints"];
$excel = $_GET["excel"];
$message = $_GET["message"];
if ($message == "") {
    $message = "Flag Day(s) (Pre Attendance)<br>Valid ONLY for Shifts with Routine Type = Daily";
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
$lstSetFlag = $_POST["lstSetFlag"];
$lstSetOT = $_POST["lstSetOT"];
$lstDeptTerminal = $_POST["lstDeptTerminal"];
$lstSetShift = $_POST["lstSetShift"];
if ($lstSetShift == "") {
    $lstSetShift = 2;
}
$lstRotateShift = $_POST["lstRotateShift"];
if ($lstRotateShift == "") {
    $lstRotateShift = "No";
}
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
if ($txtOTH == "") {
    $txtOTH = "0";
}
if ($act == "saveRecord") {
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
    $message = "Successfully Scheduled the Selected Date(s) for the Selected Employee(s) WITHIN the MAX Annual Flag Limit to be marked with Flag " . $lstSetFlag;
    $act = "searchRecord";
}
if ($prints != "yes") {
    include 'header.php';
?>
<div class="page-breadcrumb">
                    <div class="row">
                        <div class="col-12 d-flex no-block align-items-center">
                            <h4 class="page-title">Flag Day(s) (Pre Attendance)</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Flag Day(s) (Pre Attendance)
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

<?php
}
print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='OffDay.php'><input type='hidden' name='act' value='searchRecord'>";
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
        header("Content-Disposition: attachment; filename=OffDay.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        print "<body>";
    }
}

if ($excel != "yes") {
?>
<div class="card">
    <div class="card-body">
        <?php 
            print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b> <br>Pre Flagging Day(s) will NOT function in the below scenarios <br>1. Allow NO BREAK Exception as NO <br>2. Using Exit Terminals and ALLOW NON CLOCKING AT EXIT TERMINAL option as NO</font></p>";
            if ($prints != "yes") {
                print "<center><h4 class='card-title'>Select ONE or MORE options and click Search Record</h4></center>";
//                print "<table width='800' cellpadding='1' cellspacing='-1'>";
        //        print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Select ONE or MORE options and click 'Search Record'</b></font></td></tr>";
            } else {
//                print "<table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
//                print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Selected Options</b></font></td></tr>";
            }
            
        ?>
        <div class="row">
            <div class="col-3">
                <?php
                $query = "SELECT id, name from tgroup WHERE ShiftTypeID = 1 ORDER BY name";
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
                displayTextbox("txtFrom", "Date From <font size='1'>(DD/MM/YYYY)</font>: ", $txtFrom, $prints, 12, "", "");
                ?>
            </div>
            <div class="col-2">
                <?php 
                displayTextbox("txtTo", "Date To <font size='1'>(DD/MM/YYYY)</font>: ", $txtTo, $prints, 12, "", "");
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
            print "<input type='hidden' name='txhFlagLimitType' value='" . $flagLimitType . "'>";
            print "<center><input name='btSearch' type='submit' value='Search Record' class='btn btn-primary'>";
            if (stripos($userlevel, $current_module . "A") !== false) {
                print "&nbsp;&nbsp;<input type='button' value='Upload from XML' onClick='openWindow()' class='btn btn-primary'>";
            }
            print "</center>";
            print "</div>";
            print "</div>";
        }
        ?>
        <!--</form>-->
    </div>
</div>
<?php
}
$records_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;
print "</div></div></div>";
if ($act == "searchRecord") {
    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, 0, 0, 0, 0, 0, tuser.idno, tuser.remark, '', tuser.group_id, tuser.OT1 FROM tuser, tgroup WHERE tuser.group_id = tgroup.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
    if ($lstShift != "") {
        $query = $query . " AND tgroup.id = " . $lstShift;
    }
    $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
    $query = $query . employeeStatusQuery($lstEmployeeStatus);
    $fromTime = mktime(0, 0, 0, substr(insertDate($txtFrom), 4, 2), substr(insertDate($txtFrom), 6, 2), substr(insertDate($txtFrom), 0, 4));
    $toTime = mktime(0, 0, 0, substr(insertDate($txtTo), 4, 2), substr(insertDate($txtTo), 6, 2), substr(insertDate($txtTo), 0, 4));
    $dayCount = round(($toTime - $fromTime) / 86400);
    $dayCount++;
    $query = $query . " ORDER BY " . $lstSort;
//    . " LIMIT " .$offset. ",". $records_per_page
    
    if ($excel != "yes") {
        print "<p align='center'><font face='Verdana' size='1'><b>" . $_SESSION[$session_variable . "FlagReportText"] . "</b></font></p>";
    }
    print '<div class="row"><div class="col-md-12"><div class="card"><div class="card-body table-responsive">';
    print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%' class='table table-striped table-bordered dataTable'>";
    print "<thead><tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Current Shift</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> ";
    for ($i = 0; $i < $dayCount; $i++) {
        $next = strtotime(substr($txtFrom, 0, 2) . "-" . substr($txtFrom, 3, 2) . "-" . substr($txtFrom, 6, 4) . " + " . $i . " day");
        $a = getDate($next);
        print "<td><font face='Verdana' size='2'>" . $a["mday"] . "</font></td>";
    }
    print "</tr>";
    print "</thead>";
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
            $query = "SELECT '', '', '', '', '', AttendanceMaster.group_min, AttendanceMaster.Normal, AttendanceMaster.Grace, AttendanceMaster.Overtime, AttendanceMaster.ADate, '', '', AttendanceMaster.Flag, '' FROM AttendanceMaster WHERE AttendanceMaster.Flag NOT LIKE 'Delete' AND AttendanceMaster.EmployeeID = " . $cur[0] . " AND AttendanceMaster.ADate = " . $txtDate;
            $sub_result = mysqli_query($iconn, $query);
            $sub_cur = mysqli_fetch_row($sub_result);
            $query = "SELECT Flag, Rotate, OT, FlagDayRotationID FROM FlagDayRotation WHERE e_id = " . $cur[0] . " AND e_date = " . $txtDate;
            $flag_result = selectData($iconn, $query);
            if ($sub_cur[9] == $txtDate || $flag_result[3] != "") {
                if ($sub_cur[9] == $txtDate) {
                    print "<td bgcolor='" . $sub_cur[12] . "'>";
                } else {
                    print "<td bgcolor='" . $flag_result[0] . "'>";
                }
                if ($sub_cur[12] == "" && $flag_result[0] == "" && $flag_result[2] == "" && $flag_result[3] == "") {
                    if ($prints == "yes") {
                        print "<font face='Verdana' size='1'>&nbsp;</font>";
                    } else {
                        displayDate($txtDate);
                        print "<a title='" . displayDate($txtDate) . "'><input type='checkbox' name='chk" . $tot . "' value='" . $txtDate . "' id='chk-" . $tot . "'> <input type='hidden' name='txhID" . $tot . "' value='" . $cur[0] . "'> <input type='hidden' name='txhDeptClocking" . $tot . "' value='" . $cur[14] . "'></a>";
                    }
                } else {
                    if ($flag_result[1] == "Yes") {
                        displayDate($txtDate);
                        print "<a title='" . displayDate($txtDate) . " - Rotate Shift'>";
                        if ($sub_cur[9] == $txtDate) {
                            print "<font face='Verdana' size='1' color='#FFFFFF'>RS</font></a>";
                        } else {
                            print "<font face='Verdana' size='1'>RS</font></a>";
                        }
                    } else {
                        if ($flag_result[2] == "OT1") {
                            displayDate($txtDate);
                            print "<a title='" . displayDate($txtDate) . " - " . $flag_result[2] . "'>";
                            if ($sub_cur[9] == $txtDate) {
                                print "<font face='Verdana' size='1' color='#FFFFFF'>OT1</font></a>";
                            } else {
                                print "<font face='Verdana' size='1'>OT1</font></a>";
                            }
                        } else {
                            if ($flag_result[2] == "OT2") {
                                displayDate($txtDate);
                                print "<a title='" . displayDate($txtDate) . " - " . $flag_result[3] . "'>";
                                if ($sub_cur[9] == $txtDate) {
                                    print "<font face='Verdana' size='1' color='#FFFFFF'>OT2</font></a>";
                                } else {
                                    print "<font face='Verdana' size='1'>OT2</font></a>";
                                }
                            } else {
                                if ($flag_result[2] == "OT") {
                                    displayDate($txtDate);
                                    print "<a title='" . displayDate($txtDate) . " - " . $flag_result[3] . "'>";
                                    if ($sub_cur[9] == $txtDate) {
                                        print "<font face='Verdana' size='1' color='#FFFFFF'>OT</font></a>";
                                    } else {
                                        print "<font face='Verdana' size='1'>OT</font></a>";
                                    }
                                } else {
                                    displayDate($txtDate);
                                    print "<a title='" . displayDate($txtDate) . "'><font face='Verdana' size='1'>&nbsp;</font></a>";
                                }
                            }
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
    print "</table>";
    
    $total_pages = ceil($row_count / $records_per_page);
    // 7. Display pagination controls
//echo "<div style='margin-top: 20px;'>";
//for ($i = 1; $i <= $total_pages; $i++) {
//    echo "<a href='OffDay.php?prints=no&page=" . $i . "' style='padding: 8px; text-decoration: none;'>" . $i . "</a> ";
//}
//echo "</div>";
print "<p align='center'>";
    if ($prints != "yes" && 0 < $count && stripos($userlevel, $current_module . "A") !== false && $_SESSION[$session_variable . "LockDate"] < insertDate($txtFrom) && $_SESSION[$session_variable . "LockDate"] < insertDate($txtTo)) {
        print "<div class='row'>";
        print "<div class='col-2'>";
        print "<div class='mb-3'>";
        displayColourFlag($conn, $lstSetFlag, "lstSetFlag", false, true);
        print "</div>";
        print "</div>";
        print "<div class='col-2'>";
        displayTextbox("txtOTH", "OT Hours: ", $txtOTH, $prints, "5", "25%", "75%");
        print "</div>";
        print "<div class='col-2'>";
        print "<label class='form-label'>Mark OT1/ OT2/ OT:</label><select name='lstSetOT' class='select2 form-select shadow-none'> <option value='" . $lstSetOT . "' selected>" . $lstSetOT . "</option> <option value='OT1'>OT1</option> <option value='OT2'>OT2</option> <option value='OT'>OT</option> <option value=''>---</option> </select>";
        print "</div>";
        print "<div class='col-2'>";
        $query = "SELECT id, name from tgate WHERE tgate.exit = 0 ORDER BY name";
        displayList("lstDeptTerminal", "Dept Terminal: ", $lstDeptTerminal, $prints, $conn, $query, "", "25%", "75%");
        print "</div>";
        print "<div class='col-2'>";
        $query = "SELECT id, name from tgroup WHERE id > 1 AND NightFlag = 0 ORDER BY id";
        displayList("lstSetShift", "Select Shift:", $lstSetShift, $prints, $conn, $query, "", "25%", "75%");
        print "</div>";
        print "<div class='col-2'>";
        print "<label class='form-label'>Rotate Shift after Flagging:</label> <td><select name='lstRotateShift' class='select2 form-select shadow-none'><option value='" . $lstRotateShift . "' selected>" . $lstRotateShift . "</option> <option value='No'>No</option> <option value='Yes'>Yes</option> </select>";
        print "</div>";
        print "</div>";
        print "<div class='row'>";
        print "<div class='col-2'>";
        displayTextbox("txtRemarks", "Remarks: ", $txtRemarks, $prints, "", "", "");
        print "</div>";
        print "</div>";
        print "<div class='row'>";
        print "<div class='col-12'>";
        print "<center><input name='btSubmit' type='button' value='Save Changes' onClick='javascript:checkSubmit()' class='btn btn-primary'></center>";
        print "</div>";
        print "</div>";
    }
    print "<div class='row'>";
    print "<div class='col-12'>";
    print "<center>";
    if ($excel != "yes") {
        print "<br><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $row_count . "</b></font>";
    }
    if ($prints != "yes") {
        print "<br><input type='button' value='Print' onClick='checkPrint(0)' class='btn btn-primary'>&nbsp;&nbsp;<input type='button' value='Excel' onClick='checkPrint(1)' class='btn btn-primary'>";
    }
    print "</center>";
    print "</div>";
    print "</div>";
    print "</p>";
}
print "<input type='hidden' name='txtTot' value='" . $tot . "'> <input type='hidden' name='txtRowCount' value='" . $row_count . "'></form>";
//echo "\r\n<script>\r\nfunction checkSubmit(){\r\n\tx = document.frm1;\r\n\tif (x.lstSetFlag.value == '' && (x.lstRotateShift.value == 'No' || x.lstRotateShift.value == '') && x.lstSetOT.value == ''){\r\n\t\talert('Please select the Flag OR SET Rotate Shift Option to YES OR Select OT1/OT2/OT Day Option for the selected Day(s)');\r\n\t\tx.lstSetFlag.focus();\r\n\t}else if (x.lstSetFlag.value != '' && x.lstSetOT.value != ''){\r\n\t\talert('Please Select Either the Flag OR OT1/ OT2/ OT Day Option for the selected Day(s)');\r\n\t\tx.lstSetFlag.focus();\r\n\t}else if (x.lstSetFlag.value == '' && x.lstRotateShift.value == 'Yes' && x.txtRemarks.value != \"\"){\r\n\t\talert('Remark should be BLANK if Flag is NOT Selected');\r\n\t\tx.txtRemarks.focus();\r\n\t}else if (x.txtOTH.value*1 != x.txtOTH.value/1 || x.txtOTH.value*1 > 24){\r\n\t\talert('OT Hours should be a valid NUMERIC Hour Value');\r\n\t\tx.txtOTH.focus();\r\n\t}else if (x.lstSetFlag.value == '' && x.txtOTH.value > 0){\r\n\t\talert('OT Hours should be ZERO if Flag is NOT Selected');\r\n\t\tx.txtOTH.focus();\r\n\t}else if (x.lstDeptTerminal.value == ''){\r\n\t\talert('Please select the Department Terminal to be Clocked at');\r\n\t\tx.lstDeptTerminal.focus();\r\n\t//}else if (x.lstGroup.value == '' && x.lstRotateShift.value == \"No\"){\r\n\t\t//alert('Please select the Shift to be Assigned');\r\n\t\t//x.lstGroup.focus();\r\n\t//}else if (x.lstGroup.value != '' && x.lstRotateShift.value == \"Yes\"){\r\n\t\t//alert('Please DO NOT select ANY shift as Shift Assignment is automated when Rotation Option is selected');\r\n\t\t//x.lstGroup.focus();\r\n\t}else{\r\n\t\tx.act.value='saveRecord';\r\n\t\tx.btSubmit.disabled = true;\r\n\t\tx.submit();\t\t\r\n\t}\r\n}\r\n\r\nfunction checkPrint(a){\r\n\tvar x = document.frm1;\r\n\tif (check_valid_date(x.txtFrom.value) == false){\r\n\t\talert('Invalid From Date. Date Format should be DD/MM/YYYY');\r\n\t\tx.txtFrom.focus();\r\n\t\t/*\r\n\t}else if (check_valid_date(x.txtTo.value) == false){\r\n\t\talert('Invalid From Date. Date Format should be DD/MM/YYYY');\r\n\t\tx.txtTo.focus();\r\n\t\t*/\r\n\t}else{\r\n\t\tif (a == 0){\r\n\t\t\tif (confirm('Go Green - Think Twice before you Print this Document \\nAre you sure want to Print?')){\r\n\t\t\t\tx.action = 'OffDay.php?prints=yes';\r\n\t\t\t}else{\r\n\t\t\t\treturn;\r\n\t\t\t}\r\n\t\t}else{\r\n\t\t\tx.action = 'OffDay.php?prints=yes&excel=yes';\t\t\t\r\n\t\t}\t\t\r\n\t\tx.act.value = 'searchRecord';\r\n\t\tx.target = '_blank';\r\n\t\tx.submit();\r\n\t}\r\n}\r\n\r\nfunction checkSearch(){\r\n\tvar x = document.frm1;\r\n\tvar d = new Date().getFullYear();\r\n\tif (check_valid_date(x.txtFrom.value) == false){\r\n\t\talert('Invalid From Date. Date Format should be DD/MM/YYYY');\r\n\t\tx.txtFrom.focus();\r\n\t\treturn false;\r\n\t}else if (x.txhFlagLimitType.value == 'Jan 01' && x.txtFrom.value.substring(6, 10) != d){\r\n\t\talert('Invalid To Year. Only Current Year Allowed');\r\n\t\tx.txtFrom.focus();\r\n\t\treturn false;\r\n\t}else if (x.txhFlagLimitType.value == 'Employee Start Date' && (x.txtFrom.value.substring(6, 10) < (d-1) || x.txtFrom.value.substring(6, 10) > (d+1)) ){\r\n\t\talert('Invalid To Year. Only Last/ Current/ Next Year Allowed');\r\n\t\tx.txtFrom.focus();\r\n\t\treturn false;\r\n\t}else if (check_valid_date(x.txtTo.value) == false){\r\n\t\talert('Invalid From Date. Date Format should be DD/MM/YYYY');\r\n\t\tx.txtTo.focus();\r\n\t\treturn false;\r\n\t}else if (x.txhFlagLimitType.value == 'Jan 01' && x.txtTo.value.substring(6, 10) != d){\r\n\t\talert('Invalid To Year. Only Current Year Allowed');\r\n\t\tx.txtTo.focus();\r\n\t\treturn false;\r\n\t}else if (x.txhFlagLimitType.value == 'Employee Start Date' && (x.txtTo.value.substring(6, 10) < (d-1) || x.txtTo.value.substring(6, 10) > (d+1)) ){\r\n\t\talert('Invalid To Year. Only Last/ Current/ Next Year Allowed');\r\n\t\tx.txtTo.focus();\r\n\t\treturn false;\r\n\t}else{\r\n\t\tx.act.value = 'searchRecord';\r\n\t\tx.action = 'OffDay.php?prints=no';\r\n\t\tx.target = '_self';\r\n\t\tx.btSearch.disabled = true;\r\n\t\tx.submit();\r\n\t}\r\n}\r\n\r\nfunction checkAllEmployee(b, c, x){\r\n\tif (x.checked){\r\n\t\tfor (i=b;i<(b+c);i++){\r\n\t\t\tif (document.getElementById('chk-'+i)){\r\n\t\t\t\tdocument.getElementById('chk-'+i).checked = true;\r\n\t\t\t}\r\n\t\t}\r\n\t}else{\r\n\t\tfor (i=b;i<(b+c);i++){\r\n\t\t\tif (document.getElementById('chk-'+i)){\r\n\t\t\t\tdocument.getElementById('chk-'+i).checked = false;\r\n\t\t\t}\r\n\t\t}\r\n\t}\r\n}\r\n\r\nfunction checkAllDate(b, c, x){\r\n\ta = document.frm1.txtTot.value;\t\r\n\tif (x.checked){\r\n\t\tfor (i=b;i<a;i=(i+c+1)){\r\n\t\t\tif (document.getElementById('chk-'+(i+2))){\r\n\t\t\t\tdocument.getElementById('chk-'+(i+2)).checked = true;\r\n\t\t\t}\r\n\t\t}\r\n\t}else{\r\n\t\tfor (i=b;i<a;i=(i+c+1)){\r\n\t\t\tif (document.getElementById('chk-'+(i+2))){\r\n\t\t\t\tdocument.getElementById('chk-'+(i+2)).checked = false;\r\n\t\t\t}\r\n\t\t}\r\n\t}\r\n}\r\n\r\nfunction openWindow(){\r\n\twindow.open('UploadPreFlag.php', 'UploadPreFlag', 'width=800;height=600;resize=no;menubar=no;addressbar=no');\r\n}\r\n</script>\r\n</center></body></html>";
print "</div></div></div></div></div>";
include 'footer.php';
?>
<script>
//function checkSubmit() {
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

    function checkAllEmployee(b, c, x) {
        if (x.checked) {
            for (i = b; i < (b + c); i++) {
                if (document.getElementById('chk-' + i)) {
                    document.getElementById('chk-' + i).checked = true;
                }
            }
        } else {
            for (i = b; i < (b + c); i++) {
                if (document.getElementById('chk-' + i)) {
                    document.getElementById('chk-' + i).checked = false;
                }
            }
        }
    }

    function checkAllDate(b, c, x) {
        a = document.frm1.txtTot.value;
        if (x.checked) {
            for (i = b; i < a; i = (i + c + 1)) {
                if (document.getElementById('chk-' + (i + 2))) {
                    document.getElementById('chk-' + (i + 2)).checked = true;
                }
            }
        } else {
            for (i = b; i < a; i = (i + c + 1)) {
                if (document.getElementById('chk-' + (i + 2))) {
                    document.getElementById('chk-' + (i + 2)).checked = false;
                }
            }
        }
    }

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
//// Firefox, Opera 8.0+, Safari
//                    xmlHttp = new XMLHttpRequest();
//                } catch (e) {
//// Internet Explorer
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
//                }
//                xmlHttp.open("GET", "Ajax.php?act=editShift&txtID=" + a + "&txtDate=" + b + "&txtOldShift=" + c + "&txtShift=" + x.lstEditShift.value, true);
//                xmlHttp.send(null);
//            }
//        }
//    }
//
////    function checkSubmit() {
////        x = document.frm1;
////        if (x.lstSetShift.value == '') {
////            alert('Please select the Shift to be set for the selected Day(s)');
////            x.lstSetFlag.focus();
////        } else {
////            x.act.value = 'saveRecord';
////            x.btSubmit.disabled = true;
////            x.submit();
////        }
////    }
    function checkSubmit() { 
        x = document.frm1;
        if (
                x.lstSetFlag.value == '' &&
                (x.lstRotateShift.value == 'No' || x.lstRotateShift.value == '') &&
                x.lstSetOT.value == ''
                ) { 
            alert('Please select the Flag OR SET Rotate Shift Option to YES OR Select OT1/OT2/OT Day Option for the selected Day(s)');
            x.lstSetFlag.focus();
        } else if (x.lstSetFlag.value != '' && x.lstSetOT.value != '') {
            alert('Please Select Either the Flag OR OT1/ OT2/ OT Day Option for the selected Day(s)');
            x.lstSetFlag.focus();
        } else if (
                x.lstSetFlag.value == '' &&
                x.lstRotateShift.value == 'Yes' &&
                x.txtRemarks.value != ""
                ) {
            alert('Remark should be BLANK if Flag is NOT Selected');
            x.txtRemarks.focus();
        } else if (x.txtOTH.value * 1 != x.txtOTH.value / 1 || x.txtOTH.value * 1 > 24) {
            alert('OT Hours should be a valid NUMERIC Hour Value');
            x.txtOTH.focus();
        } else if (x.lstSetFlag.value == '' && x.txtOTH.value > 0) {
            alert('OT Hours should be ZERO if Flag is NOT Selected');
            x.txtOTH.focus();
        } else if (x.lstDeptTerminal.value == '') {
            alert('Please select the Department Terminal to be Clocked at');
            x.lstDeptTerminal.focus();
            //} else if (x.lstGroup.value == '' && x.lstRotateShift.value == "No") {
            //alert('Please select the Shift to be Assigned');
            //x.lstGroup.focus();
            //} else if (x.lstGroup.value != '' && x.lstRotateShift.value == "Yes") {
            //alert('Please DO NOT select ANY shift as Shift Assignment is automated when Rotation Option is selected');
            //x.lstGroup.focus();
        } else { 
            x.act.value = 'saveRecord';
            x.btSubmit.disabled = true;
            x.submit();
        }
    }

//    function checkPrint(a) {
//        var x = document.frm1;
//        if (check_valid_date(x.txtFrom.value) == false) {
//            alert('Invalid From Date. Date Format should be DD/MM/YYYY');
//            x.txtFrom.focus();
//            /*
//             } else if (check_valid_date(x.txtTo.value) == false) {
//             alert('Invalid From Date. Date Format should be DD/MM/YYYY');
//             x.txtTo.focus();
//             */
//        } else {
//            if (a == 0) {
//                if (confirm('Go Green - Think Twice before you Print this Document \nAre you sure want to Print?')) {
//                    x.action = 'OffDay.php?prints=yes';
//                } else {
//                    return;
//                }
//            } else {
//                x.action = 'OffDay.php?prints=yes&excel=yes';
//            }
//            x.act.value = 'searchRecord';
//            x.target = '_blank';
//            x.submit();
//        }
//    }
//
//    function checkSearch() { 
//    var x = document.frm1;
//            var d = new Date().getFullYear();
//            if (check_valid_date(x.txtFrom.value) == false) {
//    alert('Invalid From Date. Date Format should be DD/MM/YYYY');
//            x.txtFrom.focus();
//            return false;
//    } else if (x.txhFlagLimitType.value == 'Jan 01' && x && x.txtFrom.value.substring(6, 10) != d) {
//    alert('Invalid To Year. Only Current Year Allowed');
//            x.txtFrom.focus();
//            return false;
//            } else if (x.txhFlagLimitType.value == 'Employee Start Date' && (x.txtFrom.value.substring(6, 10) < (d - 1) || x.txtFrom.value.substring(6, 10) > (d + 1))) {
//    alert('Invalid To Year. Only Last/ Current/ Next Year Allowed');
//            x.txtFrom.focus();
//            return false;
//            } else if (check_valid_date(x.txtTo.value) == false) {
//    alert('Invalid From Date. Date Format should be DD/MM/YYYY');
//            x.txtTo.focus();
//            return false;
//            } else if (x.txhFlagLimitType.value == 'Jan 01' && x.txtTo.value.substring(6, 10) != d) {
//    alert('Invalid To Year. Only Current Year Allowed');
//            x.txtTo.focus();
//            return false;
//            } else if (x.txhFlagLimitType.value == 'Employee Start Date' && (x.txtTo.value.substring(6, 10) < (d - 1) || x.txtTo.value.substring(6, 10) > (d + 1))) {
//    alert('Invalid To Year. Only Last/ Current/ Next Year Allowed');
//            x.txtTo.focus();
//            return false;
//            } else {
//    x.act.value = 'searchRecord';
//            x.action = 'OffDay.php?prints=no';
//            x.target = '_self';
//            x.btSearch.disabled = true;
//            x.submit();
//            }
//        }
//    function checkAllEmployee(b, c, x) {
//    if (x.checked) {
//    for (i = b; i < (b + c); i++) {
//    if (document.getElementById('chk-' + i)) {
//    document.getElementById('chk-' + i).checked = true;
//            }
//    }
//    } else {
//    for (i = b; i < (b + c); i++) {
//    if (document.getElementById('chk-' + i)) {
//    document.getElementById('chk-' + i).checked = false;
//            }
//    }
//    }
//    }

//    function checkAllDate(b, c, x) {
//    a = document.frm1.txtTot.value;
//            if (x.checked) {
//    for (i = b; i < a; i = (i + c + 1)) {
//    if (document.getElementById('chk-' + (i + 2))) {
//    document.getElementById('chk-' + (i + 2)).checked = true;
//            }
//    }
//    } else {
//    for (i = b; i < a; i = (i + c + 1)) {
//    if (document.getElementById('chk-' + (i + 2))) {
//    document.getElementById('chk-' + (i + 2)).checked = false;
//            }
//    }
//    }
//    }

    function openWindow() {
        window.open('UploadPreFlag.php', 'UploadPreFlag', 'width=800;height=600;resize=no;menubar=no;addressbar=no');
    }    
</script>
