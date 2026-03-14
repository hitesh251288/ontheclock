<?php

ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "32";
set_time_limit(0);
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
$flagLimitType = $_SESSION[$session_variable . "FlagLimitType"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=FlagRoster.php&message=Session Expired or Security Policy Violated");
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
    $message = "Flag Roster (Pre Attendance Pattern) <br>Valid ONLY for Shifts with Routine Type = Daily";
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
    $txtTo = "31/12/" . substr(displayToday(), 6, 4);
}
$txtRemark = $_POST["txtRemark"];
$txtPhone = $_POST["txtPhone"];
$txtSNo = $_POST["txtSNo"];
$lstSetFlag = $_POST["lstSetFlag"];
$lstSetOT = $_POST["lstSetOT"];
$lstDeptTerminal = $_POST["lstDeptTerminal"];
$txtPatternChangeDay = $_POST["txtPatternChangeDay"];
if ($txtPatternChangeDay == "") {
    $txtPatternChangeDay = "8";
}
if (!is_numeric($txtPatternChangeDay)) {
    $txtPatternChangeDay = "8";
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
if ($act == "saveRecord") {
    $tot = $_POST["txtTot"];
    for ($i = 0; $i <= $tot; $i++) {
        if ($_POST["chk" . $i] != "") {
            $total_flag_count = 0;
            $max_flag_limit = 0;
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
                    $total_flag_count = $pre_flag_count + $post_flag_count;
                }
            }
            if ($insert_flag) {
                $query = "";
                $rotate_flag = 0;
                if ($lstRotateShift == "Yes") {
                    $rotate_flag = 1;
                }
                $j = $_POST["chk" . $i];
                while ($j <= insertDate($txtTo)) {
                    $query = "INSERT INTO FlagDayRotation (e_id, e_date, g_id, Flag, Rotate, Remark, OT) VALUES (" . $_POST["txhID" . $i] . ", " . $j . ", " . $lstDeptTerminal . ", '" . $lstSetFlag . "', " . $rotate_flag . ", '" . $txtRemarks . "', '" . $lstSetOT . "')";
                    if (updateIData($iconn, $query, true)) {
                        $text = "Pre Flagged ID: " . $_POST["txhID" . $i] . " for Date: " . displayDate($j) . " with Flag: " . $lstSetFlag . ", Rotate Shift: " . $lstRotateShift . ", OT Type: " . $lstSetOT;
                        $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                        updateIData($jconn, $query, true);
                    }
                    $j = getNextDay($j, $txtPatternChangeDay);
                }
            }
        }
    }
    $message = "Successfully Scheduled the Pattern from the Selected Date(s) for the Selected Employee(s) WITHIN the MAX Annual Flag Limit to be marked with Flag " . $lstSetFlag;
    $act = "searchRecord";
}
if ($prints != "yes") {
    include 'header.php';
?>
<div class="page-breadcrumb">
                    <div class="row">
                        <div class="col-12 d-flex no-block align-items-center">
                            <h4 class="page-title">Flag Roster (Pre Attendance Pattern)</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Flag Roster (Pre Attendance Pattern)
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

<?php
}
print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='FlagRoster.php'><input type='hidden' name='act' value='searchRecord'>";
//print "<html><title>Flag Roster (Pre Attendance Pattern)</title>";
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
        header("Content-Disposition: attachment; filename=FlagRoster.xls");
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
//            print "<table width='800' cellpadding='1' cellspacing='-1'>";
    //        print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Select ONE or MORE options and click 'Search Record'</b></font></td></tr>";
        } else {
//            print "<table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
//            print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Selected Options</b></font></td></tr>";
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
                displayTextbox("txtFrom", "Pattern Start Date: ", $txtFrom, $prints, 12, "", "");
                ?>
            </div>
            <div class="col-2">
                <?php 
                displayTextbox("txtTo", "Pattern End Date: ", $txtTo, $prints, 12, "", "");
                ?>
            </div>
            <div class="col-2">
                <?php 
                displayTextbox("txtPatternChangeDay", "Pattern Change Days: ", $txtPatternChangeDay, $prints, 5, "", "");
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
                print "<center><input name='btSearch' type='submit' value='Search Record' class='btn btn-primary'></center>";
                print "</div>";
                print "</div>";
            }
            ?>
        <!--</form>-->
    </div>
</div>
<?php
}
print "</div></div></div></div>";
if ($act == "searchRecord") {
    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, 0, 0, 0, 0, 0, tuser.idno, tuser.remark, '', tuser.group_id, tuser.OT1 FROM tuser, tgroup WHERE tuser.group_id = tgroup.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
    if ($lstShift != "") {
        $query = $query . " AND tgroup.id = " . $lstShift;
    }
    $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
    $query = $query . employeeStatusQuery($lstEmployeeStatus);
    $dayCount = $txtPatternChangeDay - 1;
    $query = $query . " ORDER BY " . $lstSort;
    if ($excel != "yes") {
        print "<p align='center'><font face='Verdana' size='1'><b>" . $_SESSION[$session_variable . "FlagReportText"] . "</b></font></p>";
    }
    print '<div class="row"><div class="col-md-12"><div class="card"><div class="card-body table-responsive">';
    print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800' class='table table-striped table-bordered dataTable'>";
    print "<thead><tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Current Shift</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> ";
    for ($i = 0; $i < $dayCount; $i++) {
        $next = strtotime(substr($txtFrom, 0, 2) . "-" . substr($txtFrom, 3, 2) . "-" . substr($txtFrom, 6, 4) . " + " . $i . " day");
        $a = getDate($next);
        print "<td><font face='Verdana' size='2'>" . $a["mday"] . "</font></td>";
    }
    print "</tr></thead>";
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
            $sub_result = mysqli_query($conn, $query);
            $sub_cur = mysqli_fetch_row($sub_result);
            $query = "SELECT Flag, Rotate, OT, FlagDayRotationID FROM FlagDayRotation WHERE e_id = " . $cur[0] . " AND e_date = " . $txtDate;
            $flag_result = selectData($conn, $query);
            if ($sub_cur[9] == $txtDate || $flag_result[3] != "") {
                if ($sub_cur[9] == $txtDate) {
                    print "<td bgcolor='" . $sub_cur[12] . "'>";
                } else {
                    if ($flag_result[0] == "" && $flag_result[1] != "") {
                        print "<td bgcolor='Gray'>";
                    } else {
                        print "<td bgcolor='" . $flag_result[0] . "'>";
                    }
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
                        print "<a title='" . displayDate($txtDate) . " - Rotate Shift'><font face='Verdana' size='1'>RS</font></a>";
                    } else {
                        if ($flag_result[2] == "OT1") {
                            displayDate($txtDate);
                            print "<a title='" . displayDate($txtDate) . " - " . $flag_result[2] . "'><font face='Verdana' size='1'>OT1</font></a>";
                        } else {
                            if ($flag_result[2] == "OT2") {
                                displayDate($txtDate);
                                print "<a title='" . displayDate($txtDate) . " - " . $flag_result[3] . "'><font face='Verdana' size='1'>OT2</font></a>";
                            } else {
                                if ($flag_result[2] == "OT") {
                                    displayDate($txtDate);
                                    print "<a title='" . displayDate($txtDate) . " - " . $flag_result[3] . "'><font face='Verdana' size='1'>OT</font></a>";
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
    print "</table><p align='center'>";
    if ($prints != "yes" && 0 < $count && stripos($userlevel, $current_module . "A") !== false && $_SESSION[$session_variable . "LockDate"] < insertDate($txtFrom) && $_SESSION[$session_variable . "LockDate"] < insertDate($txtTo)) {
        print "<div class='row'>";
        print "<div class='col-2'>";
        displayColourFlag($conn, $lstSetFlag, "lstSetFlag", false, true);
        print "</div>";
        print "<div class='col-2'>";
        print "<div class='mb-3'>";
        print "<label class='form-label'>Mark OT1/ OT2/ OT:</label><select name='lstSetOT' class='select2 form-select shadow-none'> <option value='" . $lstSetOT . "' selected>" . $lstSetOT . "</option> <option value='OT1'>OT1</option> <option value='OT2'>OT2</option> <option value='OT'>OT</option> <option value=''>---</option> </select>";
        print "</div>";
        print "</div>";
        print "<div class='col-2'>";
        $query = "SELECT id, name from tgate WHERE tgate.exit = 0 ORDER BY name";
        displayList("lstDeptTerminal", "Dept Terminal: ", $lstDeptTerminal, $prints, $conn, $query, "", "", "");
        print "</div>";
        print "<div class='col-2'>";
        print "<div class='mb-3'>";
        print "<label class='form-label'>Rotate Shift after Flagging:</label> <td><select name='lstRotateShift'  class='select2 form-select shadow-none'> <option value='" . $lstRotateShift . "' selected>" . $lstRotateShift . "</option> <option value='No'>No</option> <option value='Yes'>Yes</option> </select>";
        print "</div>";
        print "</div>";
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
print "</div></div></div></div></div>";
include 'footer.php';
echo "\r\n<script>\r\nfunction checkSubmit(){\r\n\tx = document.frm1;\r\n\tif (x.lstSetFlag.value == '' && (x.lstRotateShift.value == 'No' || x.lstRotateShift.value == '') && x.lstSetOT.value == ''){\r\n\t\talert('Please select the Flag OR SET Rotate Shift Option to YES OR Select OT Day Type for the selected Day(s)');\r\n\t\tx.lstSetFlag.focus();\r\n\t}else if (x.lstSetFlag.value != '' && x.lstSetOT.value != ''){\r\n\t\talert('Please Select Either the Flag OR OT Day Type for the selected Day(s)');\r\n\t\tx.lstSetFlag.focus();\r\n\t}else if (x.lstSetFlag.value == '' && x.lstRotateShift.value == 'Yes' && x.txtRemarks.value != \"\"){\r\n\t\talert('Remark should be BLANK if Flag is NOT Selected');\r\n\t\tx.txtRemarks.focus();\r\n\t}else if (x.lstDeptTerminal.value == ''){\r\n\t\talert('Please select the Department Terminal to be Clocked at');\r\n\t\tx.lstDeptTerminal.focus();\r\n\t}else{\r\n\t\tx.act.value='saveRecord';\r\n\t\tx.btSubmit.disabled = true;\r\n\t\tx.submit();\t\t\r\n\t}\r\n}\r\n\r\nfunction checkAllEmployee(b, c, x){\r\n\tif (x.checked){\r\n\t\tfor (i=b;i<(b+c);i++){\r\n\t\t\tif (document.getElementById('chk-'+i)){\r\n\t\t\t\tdocument.getElementById('chk-'+i).checked = true;\r\n\t\t\t}\r\n\t\t}\r\n\t}else{\r\n\t\tfor (i=b;i<(b+c);i++){\r\n\t\t\tif (document.getElementById('chk-'+i)){\r\n\t\t\t\tdocument.getElementById('chk-'+i).checked = false;\r\n\t\t\t}\r\n\t\t}\r\n\t}\r\n}\r\n\r\nfunction checkAllDate(b, c, x){\r\n\ta = document.frm1.txtTot.value;\t\r\n\tif (x.checked){\r\n\t\tfor (i=b;i<a;i=(i+c+1)){\r\n\t\t\tif (document.getElementById('chk-'+(i+2))){\r\n\t\t\t\tdocument.getElementById('chk-'+(i+2)).checked = true;\r\n\t\t\t}\r\n\t\t}\r\n\t}else{\r\n\t\tfor (i=b;i<a;i=(i+c+1)){\r\n\t\t\tif (document.getElementById('chk-'+(i+2))){\r\n\t\t\t\tdocument.getElementById('chk-'+(i+2)).checked = false;\r\n\t\t\t}\r\n\t\t}\r\n\t}\r\n}\r\n</script>";

?>