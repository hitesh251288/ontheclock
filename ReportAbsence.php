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
    header("Location: " . $config["REDIRECT"] . "?url=ReportAbsence.php&message=Session Expired or Security Policy Violated");
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
    $message = "Daily Absence Report <br>(This Report may take MORE than Normal time. Please DO NOT Refresh OR Close the Browser) <br> (It is recommended that you DO NOT use a long Date Period)";
}
$lstShift = $_REQUEST["lstShift"] ?? "";
$lstDepartment = $_REQUEST["lstDepartment"] ?? "";
$lstDivision = $_REQUEST["lstDivision"] ?? "";
$txtFrom = $_REQUEST["txtFrom"] ?? displayToday();
$txtTo = $_REQUEST["txtTo"] ?? displayToday();
$txtSearchDate = $_REQUEST["txtSearchDate"] ?? "";
$lstEmployeeID =$_REQUEST["lstEmployeeID"] ?? "";
$lstEmployeeIDFrom = $_REQUEST["lstEmployeeIDFrom"];
$lstEmployeeIDTo = $_REQUEST["lstEmployeeIDTo"];
if ($lstEmployeeID != "" && $lstEmployeeIDFrom == "" && $lstEmployeeIDTo == "") {
    $lstEmployeeIDFrom = $lstEmployeeID;
    $lstEmployeeIDTo = $lstEmployeeID;
}
$txtEmployeeCode = $_REQUEST["txtEmployeeCode"] ?? "";
$lstSort = $_REQUEST["lstSort"] ?? "tuser.id";
$txtEmployee = $_REQUEST["txtEmployee"] ?? "";
$txtRemark = $_REQUEST["txtRemark"] ?? "";
$txtPhone = $_REQUEST["txtPhone"] ?? "";
$txtSNo = $_REQUEST["txtSNo"] ?? "";
$lstEmployeeStatus = "";
if (isset($_POST["lstEmployeeStatus"])) {
    $lstEmployeeStatus = $_POST["lstEmployeeStatus"];
} else {
    $lstEmployeeStatus = "ACT";
}
$lstFlag = $_REQUEST["lstFlag"] ?? "No";
$lstOTAbsent = $_REQUEST["lstOTAbsent"] ?? "No";

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
                            <h4 class="page-title">Daily Absence Report</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Daily Absence Report
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

<?php
}
print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='ReportAbsence.php'><input type='hidden' name='act' value='searchRecord'>";
//print "<html><title>Daily Absence Report</title>";
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
        header("Content-Disposition: attachment; filename=ReportAbsence.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        print "<body>";
    }
}
print "<tr>";
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
                print "<label class='form-label'>Display Flagged Employees as Absent:</label><select name='lstFlag' class='form-select select2 shadow-none'> <option selected value='" . $lstFlag . "'>" . $lstFlag . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select>";
                ?>
            </div>
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
            displayEmployeeStatus($conn, "lstEmployeeStatus", $lstEmployeeStatus, "25%", "75%");
            print "</div>";
            print "<div class='col-2'>";
            $array = array(array("tuser.id", "Employee Code"), array("tuser.name, tuser.id", "Employee Name - Code"), array("tuser.dept, tuser.id", "Dept - Code"), array("tuser.company, tuser.dept, tuser.id", "Div/Desg - Dept - Code"), array("tuser.company, tuser.dept, tuser.group_id, tuser.id", "Div - Dept - Current Shift - Code"));
            displaySort($array, $lstSort, 5);
            print "</div>";
            print "</div>";
            print "<div class='row'>";
            print "<div class='col-12'>";
            print "<center><br><input type='hidden' name='txtSearchDate'><input name='btSearch' class='btn btn-primary' type='submit' value='Search Record'></center>";
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
    for ($date_count = insertDate($txtFrom); $date_count <= insertDate($txtTo); $date_count++) {
        if (checkdate(substr($date_count, 4, 2), substr($date_count, 6, 2), substr($date_count, 0, 4))) {
            $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tuser.idno, tuser.remark, tuser.PassiveType, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, tgroup WHERE SUBSTRING(tuser.datelimit, 2, 8) < '" . $date_count . "0000' AND tuser.group_id = tgroup.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
            if ($lstShift != "") {
                $query = $query . " AND tgroup.id = " . $lstShift;
            }
            $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
            $query = $query . employeeStatusQuery($lstEmployeeStatus);
            if ($lstOTAbsent == "No") { 
                $query = $query . " AND tuser.OT1 NOT LIKE '" . getDay(displayDate($date_count)) . "' AND tuser.OT2 NOT LIKE '" . getDay(displayDate($date_count)) . "' ";
            }
            if ($lstFlag == "No") {
                $query = $query . " AND tuser.id NOT IN (SELECT e_id FROM FlagDayRotation WHERE e_date = " . $date_count . ") ";
            }
                $query = $query . " AND tuser.id NOT IN (SELECT DISTINCT(tenter.e_id) FROM tenter, tgate, tgroup WHERE tenter.g_id = tgate.id AND tenter.e_group = tgroup.id AND tgate.exit = 0 AND ((tenter.e_time > '" . $cutoff . "00' AND tgroup.NightFlag = 1) OR (tenter.e_time > '000000' AND tgroup.NightFlag = 0))";
            if ($date_count != "") {
                $query = $query . " AND tenter.e_date = '" . $date_count . "'";
            }
            $query = $query . ") ";
            if (insertToday() == $date_count && getNow() < $cutoff) {
                $query .= " AND tgroup.NightFlag = 0";
            }
            $query = $query . " ORDER BY " . $lstSort;
            $result = mysqli_query($conn, $query);
            if (0 < mysqli_num_rows($result)) {
                print "<thead><tr><td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> </tr>";
                print "<tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Current Shifts</font></td> <td><font face='Verdana' size='2'>Date</font></td> <td><font face='Verdana' size='2'>Status</font></td> </tr></thead>";
                while ($cur = mysqli_fetch_row($result)) {
                    if ($cur[3] == "") {
                        $cur[3] = "&nbsp;";
                    }
                    if ($cur[5] == "") {
                        $cur[5] = "&nbsp;";
                    }
                    if ($cur[6] == "") {
                        $cur[6] = "&nbsp;";
                    }
                    addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
                    displayDate($date_count);
//                    if($cur[4] != 'OFF'){
                    print "<tr><td><a title='ID'><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'><font face='Verdana' size='1'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $cur[1] . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $cur[5] . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $cur[2] . "</font></a></td> <td><a title='Div/Desg'><font face='Verdana' size='1'>" . $cur[3] . "</font></a></td> <td><a title='Rmk'><font face='Verdana' size='1'>" . $cur[6] . "</font></a></td> <td><a title='Current Shift'><font face='Verdana' size='1'>" . $cur[4] . "</font></a></td> <td><a title='Date'><font face='Verdana' size='1'>" . displayDate($date_count) . "</font></a></td> <td><a title='Status'><font face='Verdana' size='1'>" . $cur[7] . "</font></a></td> </tr>";
//                    }
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
        print "<br><input type='button' value='Print Report' class='btn btn-primary' onClick='checkPrint(0)'>&nbsp;&nbsp;<input type='button' class='btn btn-primary' value='Excel' onClick='checkPrint(1)'>";
    }
    print "</p>";
}
print "</form>";
echo "\r\n<script>\r\nfunction checkDay(){\r\n\tvar x = document.frm1;\r\n\tif (x.txtFrom.value != '' && check_valid_date(x.txtFrom.value) == false){\r\n\t\talert('Invalid From Date. Date Format should be DD/MM/YYYY');\r\n\t\tx.txtFrom.focus();\r\n\t}else{\r\n\t\tvar date = new Date(x.txtFrom.value);\r\n\t\talert(date);\r\n\t\t//x.submit();\r\n\t}\r\n}\r\n</script>";
print "</div></div></div></div></div>";
echo "</center>";
include 'footer.php';

?>