<?php


ob_start("ob_gzhandler");
error_reporting(E_ALL);
include "Functions.php";
$current_module = "18";
session_start();
set_time_limit(0);
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=ReportOddLog.php&message=Session Expired or Security Policy Violated");
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
    $message = "Odd Log Report <br> (It is recommended that you DO NOT use a long Date Period)";
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
$txtRemark = $_POST["txtRemark"];
$txtPhone = $_POST["txtPhone"];
$txtSNo = $_POST["txtSNo"];
$lstEmployeeStatus = "";
if (isset($_POST["lstEmployeeStatus"])) {
    $lstEmployeeStatus = $_POST["lstEmployeeStatus"];
} else {
    $lstEmployeeStatus = "ACT";
}
$lstCount = $_POST["lstCount"];
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
if ($prints != "yes") {
    include 'header.php';
?>
<div class="page-breadcrumb">
                    <div class="row">
                        <div class="col-12 d-flex no-block align-items-center">
                            <h4 class="page-title">Odd Log Report</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Odd Log Report
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

<?php
}
print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='ReportOddLog.php'><input type='hidden' name='act' value='searchRecord'>";
//print "<html><title>Odd Log Report</title>";
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
        header("Content-Disposition: attachment; filename=ReportOddLog.xls");
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
                displayClockingType($lstClockingType);
                print "</div>";
                print "<div class='col-2'>";
                print "<label class='form-label'>Clocking Count:</label><select name='lstCount' class='form-select select2 shadow-none'> <option selected value='" . $lstCount . "'>" . $lstCount . "</option> <option value='1'>1</option> <option value='3'>3</option> <option value='5'>5</option> <option value='7'>7</option> <option value='9'>9</option><option value='---'>---</option> </select>";
                print "</div>";
                print "<div class='col-2'>";
                displayEmployeeStatus($conn, "lstEmployeeStatus", $lstEmployeeStatus, "", "");
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
    $query = "SELECT NightShiftMaxOutTime FROM OtherSettingMaster";
    $result = selectData($conn, $query);
    $cutoff = $result[0];
    $count = 0;
    print '<div class="row"><div class="col-md-12"><div class="card"><div class="card-body table-responsive">';
    if ($prints != "yes") {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800' class='table table-striped table-bordered dataTable' id='zero_config'>";
    } else {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%' class='table table-striped table-bordered dataTable'>";
    }
    if ($excel == "yes") {
        print "<tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Shift</font></td> <td><font face='Verdana' size='2'>Date</font></td> </tr>";
    }
    if ($excel != "yes") {
        print "<thead><tr><td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td><td><font face='Verdana' size='1'>&nbsp;</font></td><td><font face='Verdana' size='1'>&nbsp;</font></td> </tr>";
        print "<tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Shift</font></td> <td><font face='Verdana' size='2'>Date</font></td><td><font face='Verdana' size='2'>Time</font></td><td><font face='Verdana' size='2'>Terminal</font></td> </tr></thead>";
    }
    for ($date_count = insertDate($txtFrom); $date_count <= insertDate($txtTo); $date_count++) {
        if (checkdate(substr($date_count, 4, 2), substr($date_count, 6, 2), substr($date_count, 0, 4))) {
            $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tenter.e_date, tuser.idno, tuser.remark, count(tenter.e_date), tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10, tenter.e_time, tgate.name FROM tuser, tgroup, tenter, tgate WHERE tenter.e_group = tgroup.id AND tenter.e_id = tuser.id AND tenter.g_id = tgate.id AND tgate.exit = 0 AND (((tenter.e_time >= '" . $cutoff . "00' AND tgroup.NightFlag = 1 AND tenter.e_date = '" . $date_count . "') OR (tenter.e_time < '" . $cutoff . "00' AND tgroup.NightFlag = 1 AND tenter.e_date = '" . ($date_count + 1) . "')) OR (tenter.e_time > '000000' AND tgroup.NightFlag = 0 AND tenter.e_date = '" . $date_count . "')) " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
            if ($lstShift != "") {
                $query = $query . " AND tgroup.id = " . $lstShift;
            }
            $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
            $query = queryClockingType($query, $lstClockingType);
            $query = $query . employeeStatusQuery($lstEmployeeStatus);
            $query = $query . " GROUP BY tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tuser.idno, tuser.remark ORDER BY " . $lstSort . " ";
            $last_id = "";
            $last_date = "";
            $sub_count = 0;
            $result = mysqli_query($conn, $query);
            
            if (0 <= mysqli_num_rows($result)) {
//                if ($excel != "yes") {
//                    print "<tr><td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td><td><font face='Verdana' size='1'>&nbsp;</font></td><td><font face='Verdana' size='1'>&nbsp;</font></td> </tr>";
//                    print "<tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Shift</font></td> <td><font face='Verdana' size='2'>Date</font></td><td><font face='Verdana' size='2'>Time</font></td><td><font face='Verdana' size='2'>Terminal</font></td> </tr>";
//                }
                while ($cur = mysqli_fetch_row($result)) {
                    if ($lstCount != "") {
                        if ($cur[8] == $lstCount) {
                            if ($cur[3] == "") {
                                $cur[3] = "&nbsp;";
                            }
                            if ($cur[7] == "") {
                                $cur[7] = "&nbsp;";
                            }
                            print "<tr>";
                            if ($prints != "yes") {
                                displayDate($cur[5]);
                                displayDate($cur[5]);
                                addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
                                print "<td><a title='ID' target='_blank' href='ReportClockingLog.php?act=searchRecord&txtEmployeeCode=" . $cur[0] . "&txtFrom=" . displayDate($cur[5]) . "&txtTo=" . displayDate($cur[5]) . "&prints=yes&excel=yes'><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'><font face='Verdana' size='1'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td>";
                            } else {
                                addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
                                print "<td><a title='ID'><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'><font face='Verdana' size='1'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td>";
                            }
//                            echo "<pre>";print_R($cur);
                            displayDate($cur[5]); 
                            print "<td><a title='Name'><font face='Verdana' size='1'>" . $cur[1] . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $cur[6] . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1' color='#000000'>" . $cur[2] . "</font></a></td> <td><a title='Div/Desg'><font face='Verdana' size='1'>" . $cur[3] . "</font></a></td> <td><a title='Rmk'><font face='Verdana' size='1'>" . $cur[7] . "</font></a></td> <td><a title='Shift'><font face='Verdana' size='1'>" . $cur[4] . "</font></a></td> <td><a title='Date'><font face='Verdana' size='1'>" . displayDate($cur[5]) . "</font></a></td><td><a title='Time'><font face='Verdana' size='1'>" . displayVirdiTime($cur[19]) . "</font></a></td><td><a title='Terminal'><font face='Verdana' size='1'>" . $cur[20] . "</font></a></td> </tr>";
                            $count++;
                        }
                    } else {
                        if ($cur[8] % 2 != 0) {
                            if ($cur[3] == "") {
                                $cur[3] = "&nbsp;";
                            }
                            if ($cur[7] == "") {
                                $cur[7] = "&nbsp;";
                            }
                            print "<tr>";
                            if ($prints != "yes") {
                                displayDate($cur[5]);
                                displayDate($cur[5]);
                                addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
                                print "<td><a title='ID' target='_blank' href='ReportClockingLog.php?act=searchRecord&txtEmployeeCode=" . $cur[0] . "&txtFrom=" . displayDate($cur[5]) . "&txtTo=" . displayDate($cur[5]) . "&prints=yes&excel=yes'><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'><font face='Verdana' size='1' color='#000000'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td>";
                            } else {
                                addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
                                print "<td><a title='ID'><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'><font face='Verdana' size='1'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td>";
                            }
                            displayDate($cur[5]);
                            print "<td><a title='Name'><font face='Verdana' size='1'>" . $cur[1] . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $cur[6] . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $cur[2] . "</font></a></td> <td><a title='Div/Desg'><font face='Verdana' size='1'>" . $cur[3] . "</font></a></td> <td><a title='Rmk'><font face='Verdana' size='1'>" . $cur[7] . "</font></a></td> <td><a title='Shift'><font face='Verdana' size='1'>" . $cur[4] . "</font></a></td> <td><a title='Date'><font face='Verdana' size='1'>" . displayDate($cur[5]) . "</font></a></td><td><a title='Time'><font face='Verdana' size='1'>" . displayVirdiTime($cur[19]) . "</font></a></td><td><a title='Terminal'><font face='Verdana' size='1'>" . $cur[20] . "</font></a></td> </tr>";
                            $count++;
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
print "</div></div></div></div></div>";
echo "</center>";
include 'footer.php';

?>