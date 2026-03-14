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
$txtMACAddress = $_SESSION[$session_variable . "MACAddress"];
$weirdTimeDisplay = false;
if (getWeirdClient($txtMACAddress)) {
    $weirdTimeDisplay = true;
}
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=ReportLateArrival.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$act = $_REQUEST["act"] ?? "";
$prints = $_REQUEST["prints"] ?? "";
$excel = $_REQUEST["excel"] ?? "";
$message = $_REQUEST["message"] ?? "Late In Report <br> (It is recommended that you DO NOT use a long Date Period)";

$lstShift = $_REQUEST["lstShift"] ?? "";
$lstDepartment = $_REQUEST["lstDepartment"] ?? "";
$lstDivision = $_REQUEST["lstDivision"] ?? "";
$lstEmployeeIDFrom = $_REQUEST["lstEmployeeIDFrom"] ?? "";
$lstEmployeeIDTo = $_REQUEST["lstEmployeeIDTo"] ?? "";
$txtEmployeeCode = $_REQUEST["txtEmployeeCode"] ?? "";
$txtEmployee = $_REQUEST["txtEmployee"] ?? "";
$lstSort = $_REQUEST["lstSort"] ?? "tuser.id, tenter.e_date, tenter.e_time"; // Default to a safe column
$txtFrom = $_REQUEST["txtFrom"] ?? displayToday();
$txtTo = $_REQUEST["txtTo"] ?? displayToday();
$txtRemark = $_REQUEST["txtRemark"] ?? "";
$txtPhone = $_REQUEST["txtPhone"] ?? "";
$txtSNo = $_REQUEST["txtSNo"] ?? "";

$ex3 = $_REQUEST["ex3"] ?? 0;
$ex3 = is_numeric($ex3) ? $ex3 : 0;

$ex4 = $_REQUEST["ex4"] ?? "120";
$ex4 = is_numeric($ex4) ? $ex4 : 120;

$lstLateness = $_REQUEST["lstLateness"] ?? "From Start Time";
$lstEmployeeStatus = $_REQUEST["lstEmployeeStatus"] ?? "ACT";
$lstClockingType = $_REQUEST["lstClockingType"] ?? "";

$txtF1 = $_REQUEST["txtF1"] ?? "";
$txtF2 = $_REQUEST["txtF2"] ?? "";
$txtF3 = $_REQUEST["txtF3"] ?? "";
$txtF4 = $_REQUEST["txtF4"] ?? "";
$txtF5 = $_REQUEST["txtF5"] ?? "";
$txtF6 = $_REQUEST["txtF6"] ?? "";
$txtF7 = $_REQUEST["txtF7"] ?? "";
$txtF8 = $_REQUEST["txtF8"] ?? "";
$txtF9 = $_REQUEST["txtF9"] ?? "";
$txtF10 = $_REQUEST["txtF10"] ?? "";

if ($prints != "yes") {
    include 'header.php';
?>
<div class="page-breadcrumb">
                    <div class="row">
                        <div class="col-12 d-flex no-block align-items-center">
                            <h4 class="page-title">Late In Report</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Late In Report
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

<?php
}
print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='ReportLateArrival.php'><input type='hidden' name='act' value='searchRecord'>";
//print "<html><title>Late In Report</title>";
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
        header("Content-Disposition: attachment; filename=ReportLateArrival.xls");
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
                displayList("lstShift", "Shift: ", $lstShift, $prints, $conn, $query, "", "25%", "75%");
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
                displayTextbox("ex3", "Late Minutes after Grace Time <b>MORE</b> THAN: ", $ex3, $prints, 5, "", "");
                ?>
            </div>
            <div class="col-2">
                <?php 
                displayTextbox("txtTo", "Date To <font size='1'>(DD/MM/YYYY)</font>: ", $txtTo, $prints, 12, "", "");
                ?>
            </div>
            <div class="col-2">
                <?php 
                displayTextbox("ex4", "Late Minutes after Grace Time <b>LESS</b> THAN: ", $ex4, $prints, 5, "", "");
                ?>
            </div>
        <?php 
        if ($prints != "yes") {
            print "<div class='col-2'>";
            displayClockingType($lstClockingType);
            print "</div>";
            print "<div class='col-2'>";
            displayEmployeeStatus($conn, "lstEmployeeStatus", $lstEmployeeStatus, "", "");
            print "</div>";
            print "</div>";
            print "<div class='row'>";
            print "<div class='col-2'>";
            print "<label class='form-label'>Lateness:</label><select name='lstLateness' class='form-select select2 shadow-none'><option selected value='" . $lstLateness . "'>" . $lstLateness . "</option><option value='From Start Time'>From Start Time</option><option>From Grace Time</option></select>";
            print "</div>";
            print "<div class='col-2'>";
            $array = array(array("tuser.id, tenter.e_date, tenter.e_time", "Employee Code"), array("tuser.name, tuser.id, tenter.e_date, tenter.e_time", "Employee Name - Code"), array("tuser.dept, tuser.id, tenter.e_date, tenter.e_time", "Dept - Code"), array("tuser.company, tuser.dept, tuser.id, tenter.e_date, tenter.e_time", "Div/Desg - Dept - Code"), array("tuser.company, tuser.dept, tenter.e_group, tuser.id, tenter.e_date, tenter.e_time", "Div - Dept - Shift - Code"));
            displaySort($array, $lstSort, 5);
            print "</div>";
            print "</div>";
            print "<div class='row'>";
            print "<div class='col-12'>";
            print "<center><br><input name='btSearch' type='submit' value='Search Record' class='btn btn-primary'></center>";
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
    $count = 0;
    print '<div class="row"><div class="col-md-12"><div class="card"><div class="card-body table-responsive">';
    if ($prints != "yes") {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800' class='table table-striped table-bordered dataTable' id='zero_config'>";
    } else {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%' class='table table-striped table-bordered dataTable'>";
    }
    if ($excel == "yes") {
        print "<tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Shift</font></td> <td><font face='Verdana' size='2'>Date</font></td> <td><font face='Verdana' size='2'>Start</font></td> <td><font face='Verdana' size='2'>Grace</font></td> <td><font face='Verdana' size='2'><b>Arrived</b></font></td> <td><font face='Verdana' size='2'><b>Late <font size=1>(Min)</font></b></font></td> <td><font face='Verdana' size='2'>Terminal</font></td></tr>";
    }
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
                    print "<thead><tr><td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> </tr>";
                    print "<tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Shift</font></td> <td><font face='Verdana' size='2'>Date</font></td> <td><font face='Verdana' size='2'>Start</font></td> <td><font face='Verdana' size='2'>Grace</font></td> <td><font face='Verdana' size='2'><b>Arrived</b></font></td> <td><font face='Verdana' size='2'><b>Late <font size=1>(Min)</font></b></font></td> <td><font face='Verdana' size='2'>Terminal</font></td></tr></thead>";
                }
                while ($cur = mysqli_fetch_row($result)) {
                    if (!($cur[0] == $last_id && $cur[5] == $last_date)) {
                        if (getLateTime($date_count, $cur[10], $ex3) < $cur[6] && $cur[6] < getLateTime($date_count, $cur[10], $ex4) && 0 < getLateMin($date_count, $cur[10], $cur[6])) {
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
                            print "<tr><td><a title='ID'><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'><font face='Verdana' size='1'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $cur[1] . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $cur[8] . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $cur[2] . "</font></a></td> <td><a title='Div/Desg'><font face='Verdana' size='1'>" . $cur[3] . "</font></a></td> <td><a title='Rmk'><font face='Verdana' size='1'>" . $cur[9] . "</font></a></td> <td><a title='Shift'><font face='Verdana' size='1'>" . $cur[4] . "</font></a></td> <td><a title='Date'><font face='Verdana' size='1'>" . displayDate($cur[5]) . "</font></a></td> <td><a title='Start'><font face='Verdana' size='1'>" . displayVirdiTime($cur[11] . "00") . "</font></a></td> <td><a title='Grace'><font face='Verdana' size='1'>" . displayVirdiTime($cur[10] . "00") . "</font></a></td> <td><a title='Arrived'><font face='Verdana' size='1'><b>" . displayVirdiTime($cur[6]) . "</b></font></a></td> <td><a title='Late Minutes'><font face='Verdana' size='1'><b>";
                            if ($lstLateness == "From Start Time") {
                                if ($weirdTimeDisplay) {
                                    getLateMMSS($date_count, $cur[11], $cur[6]);
                                    print getLateMMSS($date_count, $cur[11], $cur[6]);
                                } else {
                                    getLateMin($date_count, $cur[11], $cur[6]);
                                    print getLateMin($date_count, $cur[11], $cur[6]);
                                }
                            } else {
                                if ($weirdTimeDisplay) {
                                    getLateMMSS($date_count, $cur[10], $cur[6]);
                                    print getLateMMSS($date_count, $cur[10], $cur[6]);
                                } else {
                                    getLateMin($date_count, $cur[10], $cur[6]);
                                    print getLateMin($date_count, $cur[10], $cur[6]);
                                }
                            }
                            print "</b></font></a></td> <td><a title='Terminal'><font face='Verdana' size='1'>" . $cur[7] . "</font></a></td></tr>";
                            $count++;
                        }
                        $last_id = $cur[0];
                        $last_date = $cur[5];
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
print "</div></div></div></div></div>";
echo "</center>";
include 'footer.php';

?>