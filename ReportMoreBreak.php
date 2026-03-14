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
    header("Location: " . $config["REDIRECT"] . "?url=ReportMoreBreak.php&message=Session Expired or Security Policy Violated");
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
    $message = "More Break Report [Based on Processed Records]<br> (It is recommended that you DO NOT use a long Date Period)";
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
    $txtFrom = displayDate(getLastDay(insertToday(), 1));
}
if ($txtTo == "") {
    $txtTo = displayDate(getLastDay(insertToday(), 1));
}
$txtRemark = $_POST["txtRemark"];
$txtPhone = $_POST["txtPhone"];
$txtSNo = $_POST["txtSNo"];
if ($_POST["ex3"] != "") {
    $ex3 = $_POST["ex3"];
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
$lstEmployeeStatus = "";
if (isset($_POST["lstEmployeeStatus"])) {
    $lstEmployeeStatus = $_POST["lstEmployeeStatus"];
} else {
    $lstEmployeeStatus = "ACT";
}
if ($prints != "yes") {
    include 'header.php';
?>
<div class="page-breadcrumb">
                    <div class="row">
                        <div class="col-12 d-flex no-block align-items-center">
                            <h4 class="page-title">More Break Report [Based on Processed Records]</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            More Break Report [Based on Processed Records]
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

<?php
}
print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='ReportMoreBreak.php'><input type='hidden' name='act' value='searchRecord'>";
//print "<html><title>More Break Report [Based on Processed Records]</title>";
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
        header("Content-Disposition: attachment; filename=ReportMoreBreak.xls");
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
                $query = "SELECT id, name from tgroup WHERE id > 1 ORDER BY name";
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
    print '<div class="row"><div class="col-md-12"><div class="card"><div class="card-body table-responsive">';
    if ($prints != "yes") {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800' class='table table-striped table-bordered dataTable' id='zero_config'>";
    } else {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%' class='table table-striped table-bordered dataTable'>";
    }
    $count = 0;
    if ($excel == "yes") {
        print "<tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Shift</font></td> <td><font face='Verdana' size='2'>Date</font></td> <td><font face='Verdana' size='2'>Break Out</font></td> <td><font face='Verdana' size='2'>Break In</font></td> <td align='center' colspan='2'><font face='Verdana' size='2'>Break</font></td> <td align='center' colspan='2'><font face='Verdana' size='2'>More Break</font></td> </tr>";
        print "<tr><td colspan='10'><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='1'>Min</font></td> <td><font face='Verdana' size='1'>Hrs</font></td> <td><font face='Verdana' size='1'>Min</font></td> <td><font face='Verdana' size='1'>Hrs</font></td> </tr>";
    }
    for ($date_count = insertDate($txtFrom); $date_count <= insertDate($txtTo); $date_count++) {
        if (checkdate(substr($date_count, 4, 2), substr($date_count, 6, 2), substr($date_count, 0, 4))) {
            $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tuser.idno, tuser.remark, tgroup.name, AttendanceMaster.ADate, DayMaster.BreakOut, DayMaster.BreakIn,\tAttendanceMaster.Break, AttendanceMaster.MoreBreak, AttendanceMaster.Flag, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, tgroup, AttendanceMaster, DayMaster WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.EmployeeID = tuser.id AND AttendanceMaster.EmployeeID= DayMaster.e_id AND AttendanceMaster.ADate = DayMaster.TDate AND  AttendanceMaster.MoreBreak > 0 " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
            if ($lstShift != "") {
                $query = $query . " AND tgroup.id = " . $lstShift;
            }
            $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
            if ($date_count != "") {
                $query = $query . " AND AttendanceMaster.ADate = '" . $date_count . "'";
            }
            $query = $query . employeeStatusQuery($lstEmployeeStatus);
            $query = $query . " ORDER BY " . $lstSort;
            $t_break = 0;
            $data0 = "";
            $result = mysqli_query($conn, $query);
            if (0 < mysqli_num_rows($result)) {
                if ($excel != "yes") {
                    print "<tr><td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td></tr>";
                    print "<thead><tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Shift</font></td> <td><font face='Verdana' size='2'>Date</font></td> <td><font face='Verdana' size='2'>Break Out</font></td> <td><font face='Verdana' size='2'>Break In</font></td> <td align='center' colspan='2'><font face='Verdana' size='2'>Break</font></td> <td align='center' colspan='2'><font face='Verdana' size='2'>More Break</font></td> </tr>";
                    print "<tr><td colspan='10'><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='1'>Min</font></td> <td><font face='Verdana' size='1'>Hrs</font></td> <td><font face='Verdana' size='1'>Min</font></td> <td><font face='Verdana' size='1'>Hrs</font></td> </tr></thead>";
                }
                while ($cur = mysqli_fetch_row($result)) {
                    if ($data0 != $cur[0] && 0 < $count) {
                        round($t_break / 60, 2);
                        round($t_break / 3600, 2);
                        print "<tr><td colspan='12'><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'><b>" . round($t_break / 60, 2) . "</b></font></td> <td><font face='Verdana' size='1'><b>" . round($t_break / 3600, 2) . "</b></font></td> </tr>";
                        $t_break = 0;
                    }
                    $data0 = $cur[0];
                    if ($cur[3] == "") {
                        $cur[3] = "&nbsp;";
                    }
                    if ($cur[4] == "") {
                        $cur[4] = "&nbsp;";
                    }
                    if ($cur[5] == "") {
                        $cur[5] = "&nbsp;";
                    }
                    addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
                    displayDate($cur[7]);
                    print "<tr><td><a title='ID'><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'><font face='Verdana' size='1'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1' color='" . $cur[12] . "'>" . $cur[1] . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1' color='" . $cur[12] . "'>" . $cur[4] . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1' color='" . $cur[12] . "'>" . $cur[2] . "</font></a></td> <td><a title='Div/Desg'><font face='Verdana' size='1' color='" . $cur[12] . "'>" . $cur[3] . "</font></a></td> <td><a title='Rmk'><font face='Verdana' size='1' color='" . $cur[12] . "'>" . $cur[5] . "</font></a></td> <td><a title='Shift'><font face='Verdana' size='1' color='" . $cur[12] . "'>" . $cur[6] . "</font></a></td> <td><a title='Date'><font face='Verdana' size='1' color='" . $cur[12] . "'>" . displayDate($cur[7]) . "</font></a></td>";
                    if ($prints != "yes") {
                        displayVirdiTime($cur[8]);
                        print "<td><a title='Break Out'><a title='Click to view Work Details for this Period' href='ReportDailyClocking.php?act=searchRecord&prints=yes&excel=yes&lstEmployeeID=" . $cur[0] . "&txtFrom=" . $txtFrom . "&txtTo=" . $txtTo . "' target='_blank'><font color='" . $cur[12] . "' face='Verdana' size='1'>" . displayVirdiTime($cur[8]) . "</font></a></td>";
                    } else {
                        displayVirdiTime($cur[8]);
                        print "<td><a title='Break Out'><font color='" . $cur[12] . "' face='Verdana' size='1'>" . displayVirdiTime($cur[8]) . "</font></a></td>";
                    }
                    displayVirdiTime($cur[9]);
                    round($cur[10] / 60, 2);
                    round($cur[10] / 3600, 2);
                    print "<td><a title='Break In'><font face='Verdana' size='1' color='" . $cur[12] . "'>" . displayVirdiTime($cur[9]) . "</font></a></td> <td><a title='Break'><font face='Verdana' size='1' color='" . $cur[12] . "'>" . round($cur[10] / 60, 2) . "</font></a></td> <td><a title='Break'><font face='Verdana' size='1' color='" . $cur[12] . "'>" . round($cur[10] / 3600, 2) . "</font></a></td>";
                    if ($prints != "yes") {
                        round($cur[11] / 60, 2);
                        print "<td><a title='More Break'><a title='Click to view Clocking Details for this Period' href='ReportWork.php?act=searchRecord&prints=yes&excel=yes&lstEmployeeID=" . $cur[0] . "&txtFrom=" . $txtFrom . "&txtTo=" . $txtTo . "' target='_blank'><font face='Verdana' size='1' color='" . $cur[12] . "'>" . round($cur[11] / 60, 2) . "</font></a></td>";
                    } else {
                        round($cur[11] / 60, 2);
                        print "<td><a title='More Break'><font face='Verdana' size='1' color='" . $cur[12] . "'>" . round($cur[11] / 60, 2) . "</font></a></td>";
                    }
                    round($cur[11] / 3600, 2);
                    print "<td><a title='More Break'><font face='Verdana' size='1' color='" . $cur[12] . "'>" . round($cur[11] / 3600, 2) . "</font></a></td> </tr>";
                    $count++;
                    $t_break = $tbreak + $cur[11];
                }
            }
            if (0 < $count) {
                round($t_break / 60, 2);
                round($t_break / 3600, 2);
                print "<tr><td colspan='12'><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'><b>" . round($t_break / 60, 2) . "</b></font></td> <td><font face='Verdana' size='1'><b>" . round($t_break / 3600, 2) . "</b></font></td> </tr>";
            }
        }
    }
    print "</table>";
    if ($excel != "yes") {
        print "<br><p align='center'><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $count . "</b></font>";
    }
    if ($prints != "yes") {
        print "<br><input type='button' value='Print Report' class='btn btn-primary' onClick='checkPrint(0)'>&nbsp;&nbsp;<input type='button' value='Excel' class='btn btn-primary' onClick='checkPrint(1)'>";
    }
    print "</p>";
}
print "</form>";
print "</div></div></div></div></div>";
echo "</center>";
include 'footer.php';

?>