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
    header("Location: " . $config["REDIRECT"] . "?url=ReportShiftSnapShot.php&message=Session Expired or Security Policy Violated");
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
    $message = "Shift Snap Shot Report<br>Report Valid ONLY for Shifts with Routine Type = Daily";
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
$lstSort = $_POST["lstSort"];
$lstTotal = $_POST["lstTotal"];
if ($lstTotal == "") {
    $lstTotal = "No";
}
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
if ($prints != "yes") {
    include 'header.php';
?>
<div class="page-breadcrumb">
                    <div class="row">
                        <div class="col-12 d-flex no-block align-items-center">
                            <h4 class="page-title">Shift Snap Shot Report</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Shift Snap Shot Report
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

<?php
}
//print "<html><title>Shift Snap Shot Report</title>";
print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='ReportShiftSnapShot.php'><input type='hidden' name='act' value='searchRecord'>";
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
        header("Content-Disposition: attachment; filename=ReportShiftSnapShot.xls");
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
        print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b></font></p>";
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
                displayList("lstShift", "Shift: ", $lstShift, $prints, $conn, $query, "", "", "");
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
                print "<label class='form-label'>Display Flags:</label><select name='lstFlag' class='form-select select2 shadow-none'> <option selected value='" . $lstFlag . "'>" . $lstFlag . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> <option value='---'>---</option> </select>";
                ?>
            </div>
            <div class="col-2">
                <?php 
                displayTextbox("txtTo", "Date To <font size='1'>(DD/MM/YYYY)</font>: ", $txtTo, $prints, 12, "25%", "25%");
                ?>
            </div>
            <div class="col-2">
                <?php 
                print "<label class='form-label'>Display Totals:</label><select name='lstTotal' class='form-select select2 shadow-none'> <option selected value='" . $lstTotal . "'>" . $lstTotal . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> <option value='---'>---</option> </select>";
                ?>
            </div>
        <?php 
            if ($prints != "yes") {
                print "<div class='col-2'>";
                displayEmployeeStatus($conn, "lstEmployeeStatus", $lstEmployeeStatus, "", "");
                print "</div>";
                print "<div class='col-2'>";
                $array = array(array("tuser.id, AttendanceMaster.ADate", "Employee Code"), array("tuser.name, tuser.id, AttendanceMaster.ADate", "Employee Name - Code"), array("tuser.dept, tuser.id, AttendanceMaster.ADate", "Dept - Code"), array("tuser.company, tuser.dept, tuser.id, AttendanceMaster.ADate", "Div/Desg - Dept - Code"), array("tuser.company, tuser.dept, tuser.group_id, tuser.id, AttendanceMaster.ADate", "Div - Dept - Current Shift - Code"));
                displaySort($array, $lstSort, 5);
                print "</div>";
                print "</div>";
                print "<div class='row'>";
                print "<div class='col-12'>";
                print "<center><br><input name='btSearch' class='btn btn-primary' type='submit' value='Search Record'></center>";
                print "</div>";
                print "</div>";
            }
        ?>
    </div>
</div>
<?php
 } 
print "</div></div></div></div>";

if ($act == "searchRecord") {
    if ($excel != "yes" && $prints != "yes") {
        print "<p align='center'><font face='Verdana' size='1'><b>";
        print "<br><br>Click on the Day Record to get the Clocking Details for the selected Period";
        print "</b></font></p>";
    }
    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, AttendanceMaster.group_min, AttendanceMaster.Normal, AttendanceMaster.Grace, AttendanceMaster.Overtime, AttendanceMaster.ADate, tuser.idno, tuser.remark, AttendanceMaster.Flag, AttendanceMaster.Day, tgroup.NightFlag, tgroup.id, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, tgroup, AttendanceMaster WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.Flag NOT LIKE 'Delete' AND AttendanceMaster.EmployeeID = tuser.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
    if ($lstShift != "") {
        $query = $query . " AND tgroup.id = " . $lstShift;
    }
    $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
    if ($txtFrom != "") {
        $query = $query . " AND AttendanceMaster.ADate >= " . insertDate($txtFrom);
    }
    if ($txtTo != "") {
        $query = $query . " AND AttendanceMaster.ADate <= " . insertDate($txtTo);
    }
    $query = $query . employeeStatusQuery($lstEmployeeStatus);
    $fromTime = mktime(0, 0, 0, substr(insertDate($txtFrom), 4, 2), substr(insertDate($txtFrom), 6, 2), substr(insertDate($txtFrom), 0, 4));
    $toTime = mktime(0, 0, 0, substr(insertDate($txtTo), 4, 2), substr(insertDate($txtTo), 6, 2), substr(insertDate($txtTo), 0, 4));
    $dayCount = ($toTime - $fromTime) / 86400;
    $dayCount++;
    $query = $query . " ORDER BY " . $lstSort;
    print '<div class="row"><div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12"><div class="card"><div class="card-body table-responsive">';
    print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%' class='table table-striped table-bordered dataTable' id='zero_config'>";
    print "<thead><tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> ";
    for ($i = 0; $i < $dayCount; $i++) {
        $next = strtotime(substr($txtFrom, 0, 2) . "-" . substr($txtFrom, 3, 2) . "-" . substr($txtFrom, 6, 4) . " + " . $i . " day");
        $a = getDate($next);
        print "<td><font face='Verdana' size='2'>" . $a["mday"] . "</font></td>";
    }
    $shift_array = [];
    if ($lstTotal == "Yes") {
        print "<td bgcolor='#F0F0F0'><font face='Verdana' size='1'>&nbsp;</font></td>";
        $sub_query = "SELECT tgroup.name from tgroup WHERE id > 1 ORDER BY tgroup.name";
        $result2 = mysqli_query($conn, $sub_query);
        while ($cur = mysqli_fetch_row($result2)) {
            print "<td><font face='Verdana' size='1'>" . $cur[0] . "</font></td>";
            $shift_array[$cur[0]] = 0;
        }
    }
    print "</tr></thead>";
    print "<tr>";
    for ($i = 0; $i < 6; $i++) {
        print "<td><font face='Verdana' size='1'>&nbsp;</font></td>";
    }
    for ($i = 0; $i < $dayCount; $i++) {
        $next = strtotime(substr($txtFrom, 0, 2) . "-" . substr($txtFrom, 3, 2) . "-" . substr($txtFrom, 6, 4) . " + " . $i . " day");
        $a = getDate($next);
        substr($a["weekday"], 0, 1);
        print "<td><a title='" . $a["weekday"] . "'><font face='Verdana' size='1'>" . substr($a["weekday"], 0, 1) . "</font></a></td>";
    }
    if ($lstTotal == "Yes") {
        print "<td bgcolor='#F0F0F0'><font face='Verdana' size='1'>&nbsp;</font></td>";
        for ($i = 0; $i < count($sub_result); $i++) {
            print "<td><font face='Verdana' size='1'>&nbsp;</font></td>";
        }
    }
    print "</tr>";
    $row_count = 0;
    $count = 0;
    $subc = 0;
    $eid = "";
    $txtDate = insertDate($txtFrom);
    $data9 = "";
    $array_flag = "";
    $last_shift = "";
    $last_shift_name = "";
    $result = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
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
                for ($i = $subc; $i < $dayCount; $i++) {
                    if ($lstFlag == "Yes") {
                        $print_flag = true;
                        if ($array_flag[$txtDate] != "") {
                            print "<td><font face='Verdana' color='" . $array_flag[$txtDate] . "' size='1'>" . $array_flag[$txtDate] . "</font></td>";
                            $print_flag = false;
                        } else {
                            if ($array_ot1[$txtDate] != "") {
                                print "<td bgcolor='#F0F0F0'><font face='Verdana' color='#000000' size='1'>OT1</font></td>";
                                $print_flag = false;
                            } else {
                                if ($array_ot2[$txtDate] != "") {
                                    print "<td bgcolor='#F0F0F0'><font face='Verdana' color='#000000' size='1'>OT2</font></td>";
                                    $print_flag = false;
                                }
                            }
                        }
                        if ($array_rotate[$txtDate] != "" && $array_rotate[$txtDate] == 1) {
                            $last_shift = rotateShift($conn, $eid, $last_shift);
                            $super_sub_query = "SELECT name from tgroup where id = " . $last_shift;
                            $super_sub_result = selectData($conn, $super_sub_query);
                            $last_shift_name = $super_sub_result[0];
                        }
                        $super_sub_query = "SELECT ShiftChangeID, AE, SRDay FROM ShiftChangeMaster WHERE id = " . $last_shift;
                        $super_sub_result = selectData($conn, $super_sub_query);
                        if (is_numeric($super_sub_result[0]) && $super_sub_result[1] == 1 && getDay(displayDate($txtDate)) == $super_sub_result[2]) {
                            $last_shift = rotateShift($conn, $cur[0], $last_shift);
                            $super_sub_query = "SELECT name from tgroup where id = " . $last_shift;
                            $super_sub_result = selectData($conn, $super_sub_query);
                            $last_shift_name = $super_sub_result[0];
                        }
                        if ($print_flag) {
                            print "<td><font face='Verdana' size='1'>" . $last_shift_name . "</font></td>";
                        }
                    } else {
                        print "<td><font face='Verdana' size='1'>&nbsp;</font></td>";
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
                if ($lstTotal == "Yes") {
                    print "<td bgcolor='#F0F0F0'><font face='Verdana' size='1'>&nbsp;</font></td>";
                    $shift_array_key = array_keys($shift_array);
                    for ($i = 0; $i < count($shift_array_key); $i++) {
                        print "<td><font face='Verdana' size='2'>" . $shift_array[$shift_array_key[$i]] . "</font></td>";
                        $shift_array[$shift_array_key[$i]] = 0;
                    }
                }
                print "</tr>";
                $row_count++;
            }
            print "<tr>";
            if ($excel != "yes") {
                addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
                print "<td><a title='ID: Click to view Clocking Details for the selected Period (ACTIVE EMPLOYEES ONLY)' href='ReportDailyClocking.php?act=searchRecord&prints=yes&excel=yes&subReport=yes&lstEmployeeID=" . $cur[0] . "&txtFrom=" . $txtFrom . "&txtTo=" . $txtTo . "' target='_blank'><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'><font face='Verdana' size='1' color='#000000'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $cur[1] . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $cur[10] . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $cur[2] . "</font></a></td> <td><a title='Div/Desg'><font face='Verdana' size='1'>" . $cur[3] . "</font></a></td> <td><a title='Rmk'><font face='Verdana' size='1'>" . $cur[11] . "</font></a></td> ";
            } else {
                addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
                print "<td><font face='Verdana' size='1' color='#000000'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></td> <td><font face='Verdana' size='1'>" . $cur[1] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[10] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[2] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[3] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[11] . "</font></td> ";
            }
            $eid = $cur[0];
            $last_shift = $cur[15];
            $last_shift_name = $cur[4];
            $subc = 0;
            $txtDate = insertDate($txtFrom);
            $array_flag = "";
            $array_ot1 = "";
            $array_ot2 = "";
            $array_rotate = "";
            if ($lstFlag == "Yes") {
                $query_flag = "SELECT FlagDayRotation.e_date, FlagDayRotation.Flag, FlagDayRotation.OT1, FlagDayRotation.OT2, FlagDayRotation.Rotate FROM FlagDayRotation WHERE FlagDayRotation.e_date >= " . insertDate($txtFrom) . " AND FlagDayRotation.e_date <= " . insertDate($txtTo) . " AND FlagDayRotation.e_id = " . $cur[0];
                $result_flag = mysqli_query($query_flag, $conn);
                while ($cur_flag = mysqli_fetch_row($result_flag)) {
                    if (1 < strlen($cur_flag[1])) {
                        $array_flag[$cur_flag[0]] = $cur_flag[1];
                        $array_rotate[$cur_flag[0]] = $cur_flag[4];
                    }
                    if (1 < strlen($cur_flag[2])) {
                        $array_ot1[$cur_flag[0]] = $cur_flag[2];
                        $array_rotate[$cur_flag[0]] = $cur_flag[4];
                    }
                    if (1 < strlen($cur_flag[3])) {
                        $array_ot2[$cur_flag[0]] = $cur_flag[3];
                        $array_rotate[$cur_flag[0]] = $cur_flag[4];
                    }
                }
            }
        }
        while (true) {
            $subc++;
            if ($cur[9] == $txtDate) {
                if ($excel != "yes" && $lstFlag == "Yes") {
                    $bgcolor = "#FFFFFF";
                    $data = $cur[4];
                    if ($array_flag[$txtDate] != "") {
                        $data = $array_flag[$txtDate];
                    } else {
                        if ($array_ot1[$txtDate] != "") {
                            $bgcolor = "#F0F0F0";
                            $data = "OT1";
                        } else {
                            if ($array_ot2[$txtDate] != "") {
                                $bgcolor = "#F0F0F0";
                                $data = "OT2";
                            }
                        }
                    }
                    displayDate($cur[9]);
                    print "<td bgcolor='" . $bgcolor . "'><a title='" . displayDate($cur[9]) . ": Click to view Hour Details for the selected Period (ACTIVE EMPLOYEES ONLY)' href='ReportWork.php?act=searchRecord&prints=yes&excel=yes&subReport=yes&lstEmployeeID=" . $cur[0] . "&txtFrom=" . $txtFrom . "&txtTo=" . $txtTo . "' target='_blank'>";
                    if ($data == "Violet" || $data == "Indigo" || $data == "Blue" || $data == "Green" || $data == "Yellow" || $data == "Orange" || $data == "Red" || $data == "Gray" || $data == "Brown" || $data == "Purple") {
                        print "<font face='Verdana' color='" . $data . "' size='1'>" . $data . "</font></a></td>";
                    } else {
                        print "<font face='Verdana' color='Black' size='1'>" . $data . "</font></a></td>";
                    }
                } else {
                    print "<td><font face='Verdana' color='" . $cur[4] . "' size='1'>" . $cur[4] . "</font></td>";
                }
                $shift_array[$cur[4]] = $shift_array[$cur[4]] + 1;
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
                break;
            }
            if ($dayCount < $subc) {
                break;
            }
            if ($lstFlag == "Yes") {
                $print_flag = true;
                if ($array_flag[$txtDate] != "") {
                    print "<td><font face='Verdana' color='" . $array_flag[$txtDate] . "' size='1'>" . $array_flag[$txtDate] . "</font></td>";
                    $print_flag = false;
                } else {
                    if ($array_ot1[$txtDate] != "") {
                        print "<td bgcolor='#F0F0F0'><font face='Verdana' color='#000000' size='1'>OT1</font></td>";
                        $print_flag = false;
                    } else {
                        if ($array_ot2[$txtDate] != "") {
                            print "<td bgcolor='#F0F0F0'><font face='Verdana' color='#000000' size='1'>OT2</font></td>";
                            $print_flag = false;
                        }
                    }
                }
                if ($array_rotate[$txtDate] != "" && $array_rotate[$txtDate] == 1) {
                    $last_shift = rotateShift($conn, $cur[0], $last_shift);
                    $super_sub_query = "SELECT name from tgroup where id = " . $last_shift;
                    $super_sub_result = selectData($conn, $super_sub_query);
                    $last_shift_name = $super_sub_result[0];
                }
                $super_sub_query = "SELECT ShiftChangeID, AE, SRDay FROM ShiftChangeMaster WHERE id = " . $last_shift;
                $super_sub_result = selectData($conn, $super_sub_query);
                if (is_numeric($super_sub_result[0]) && $super_sub_result[1] == 1 && getDay(displayDate($cur[9])) == $super_sub_result[2]) {
                    $last_shift = rotateShift($conn, $cur[0], $last_shift);
                    $super_sub_query = "SELECT name from tgroup where id = " . $last_shift;
                    $super_sub_result = selectData($conn, $super_sub_query);
                    $last_shift_name = $super_sub_result[0];
                }
                if ($print_flag) {
                    print "<td><font face='Verdana' size='1'>" . $last_shift_name . "</font></td>";
                }
            } else {
                print "<td><font face='Verdana' size='1'>&nbsp;</font></td>";
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
        $count++;
        $data9 = $cur[9];
    }
    if (0 < $count) {
        for ($i = $subc; $i < $dayCount; $i++) {
            if ($lstFlag == "Yes") {
                $print_flag = true;
                if ($array_flag[$txtDate] != "") {
                    print "<td><font face='Verdana' color='" . $array_flag[$txtDate] . "' size='1'>" . $array_flag[$txtDate] . "</font></td>";
                    $print_flag = false;
                } else {
                    if ($array_ot1[$txtDate] != "") {
                        print "<td bgcolor='#F0F0F0'><font face='Verdana' color='#000000' size='1'>OT1</font></td>";
                        $print_flag = false;
                    } else {
                        if ($array_ot2[$txtDate] != "") {
                            print "<td bgcolor='#F0F0F0'><font face='Verdana' color='#000000' size='1'>OT2</font></td>";
                            $print_flag = false;
                        }
                    }
                }
                if ($array_rotate[$txtDate] != "" && $array_rotate[$txtDate] == 1) {
                    $last_shift = rotateShift($conn, $eid, $last_shift);
                    $super_sub_query = "SELECT name from tgroup where id = " . $last_shift;
                    $super_sub_result = selectData($conn, $super_sub_query);
                    $last_shift_name = $super_sub_result[0];
                }
                $super_sub_query = "SELECT ShiftChangeID, AE, SRDay FROM ShiftChangeMaster WHERE id = " . $last_shift;
                $super_sub_result = selectData($conn, $super_sub_query);
                if (is_numeric($super_sub_result[0]) && $super_sub_result[1] == 1 && getDay(displayDate($txtDate)) == $super_sub_result[2]) {
                    $last_shift = rotateShift($conn, $eid, $last_shift);
                    $super_sub_query = "SELECT name from tgroup where id = " . $last_shift;
                    $super_sub_result = selectData($conn, $super_sub_query);
                    $last_shift_name = $super_sub_result[0];
                }
                if ($print_flag) {
                    print "<td><font face='Verdana' size='1'>" . $last_shift_name . "</font></td>";
                }
            } else {
                print "<td><font face='Verdana' size='1'>&nbsp;</font></td>";
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
        if ($lstTotal == "Yes") {
            print "<td bgcolor='#F0F0F0'><font face='Verdana' size='2'>&nbsp;</font></td>";
            $shift_array_key = array_keys($shift_array);
            for ($i = 0; $i < count($shift_array_key); $i++) {
                print "<td><font face='Verdana' size='2'>" . $shift_array[$shift_array_key[$i]] . "</font></td>";
                $shift_array[$shift_array_key[$i]] = 0;
            }
        }
    }
    print "</tr>";
    $row_count++;
    print "</table>";
    print "</div></div></div></div>";
    print "<center>";
    if ($excel != "yes") {
        print "<br><p align='center'><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $row_count . "</b></font>";
    }
    if ($prints != "yes") {
        print "<br><input type='button' value='Print Report' class='btn btn-primary' onClick='checkPrint(0)'>&nbsp;&nbsp;<input type='button' class='btn btn-primary' value='Excel' onClick='checkPrint(1)'>";
    }
    print "</p>";
    print "</center>";
}
print "</form>";
print "</div>";
include 'footer.php';

?>