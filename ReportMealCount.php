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
    header("Location: " . $config["REDIRECT"] . "?url=ReportMealCount.php&message=Session Expired or Security Policy Violated");
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
    $message = "Raw Log Report";
}
$lstShift = $_POST["lstShift"];
$lstDepartment = $_POST["lstDepartment"];
$lstDivision = $_POST["lstDivision"];
$lstTerminal = $_POST["lstTerminal"];
$lstEmployeeIDFrom = $_POST["lstEmployeeIDFrom"];
$lstEmployeeIDTo = $_POST["lstEmployeeIDTo"];
$txtEmployeeCode = $_POST["txtEmployeeCode"];
if ($txtEmployeeCode == "") {
    $txtEmployeeCode = $_GET["txtEmployeeCode"];
}
$lstSort = $_POST["lstSort"];
$txtEmployee = $_POST["txtEmployee"];
$txtFrom = $_POST["txtFrom"];
$txtTo = $_POST["txtTo"];
if ($txtFrom == "") {
    $txtFrom = $_GET["txtFrom"];
}
if ($txtTo == "") {
    $txtTo = $_GET["txtTo"];
}
if ($txtFrom == "") {
    $txtFrom = displayToday();
}
if ($txtTo == "") {
    $txtTo = displayToday();
}
$txtTimeFrom = $_POST["txtTimeFrom"];
if ($txtTimeFrom == "") {
    $txtTimeFrom = "000000";
}
$txtMACAddress = $_SESSION[$session_variable . "MACAddress"];
$txtTimeTo = $_POST["txtTimeTo"];
if ($txtMACAddress == "D0-67-E5-E9-86-6A") {
    if ($txtTimeTo == "") {
        $txtTimeTo = getNow() . "00";
    }
} else {
    if ($txtTimeTo == "") {
        $txtTimeTo = "235959";
    }
}
$txtRemark = $_POST["txtRemark"];
$txtPhone = $_POST["txtPhone"];
$txtSNo = $_POST["txtSNo"];
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
$lstEmployeeStatus = "";
if (isset($_POST["lstEmployeeStatus"])) {
    $lstEmployeeStatus = $_POST["lstEmployeeStatus"];
} else {
    $lstEmployeeStatus = "ACT";
}
$lstRecordType = $_POST["lstRecordType"];
$lstDB = $_POST["lstDB"];
if ($lstDB == "") {
    $lstDB = "Live";
}
if ($prints != "yes") {
    include 'header.php';
?>
<div class="page-breadcrumb">
                    <div class="row">
                        <div class="col-12 d-flex no-block align-items-center">
                            <h4 class="page-title">Meal Count Report</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Meal Count Report
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

<?php
}
//print "<html><title>Meal Count Report</title>";
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
        header("Content-Disposition: attachment; filename=ReportRawLog.xls");
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
        print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='ReportMealCount.php'><input type='hidden' name='act' value='searchRecord'>";
        ?>
        <div class="row">
            <div class="col-3">
                <?php
                $query = "SELECT id, name from tgroup ORDER BY name";
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
                $query = "SELECT id, name from tgate WHERE Meal = 1 ORDER BY name";
                displayList("lstTerminal", "Meal Terminal: ", $lstTerminal, $prints, $conn, $query, "", "", "");
                ?>
            </div>
            <div class="col-2">
                <?php 
                displayTextbox("txtFrom", "Date From <font size='1'>(DD/MM/YYYY)</font>: ", $txtFrom, $prints, 12, "", "");
                ?>
            </div>
            <div class="col-2">
                <?php 
                displayTextbox("txtTimeFrom", "Time From (HHMMSS): ", $txtTimeFrom, $prints, 7, "", "");
                ?>
            </div>
            <div class="col-2">
                <?php 
                displayTextbox("txtTo", "Date To <font size='1'>(DD/MM/YYYY)</font>: ", $txtTo, $prints, 12, "", "");
                ?>
            </div>
            <div class="col-2">
                <?php 
                displayTextbox("txtTimeTo", "Time To (HHMMSS): ", $txtTimeTo, $prints, 7, "", "");
                ?>
            </div>
        <?php 
            if ($prints != "yes") {
                print "<div class='col-2'>";
                print "<label class='form-label'>DB:</label><select name='lstDB' class='form-select select2 shadow-none'><option selected value='" . $lstDB . "'>" . $lstDB . "</option> <option value='Live'>Live</option> <option value='Archive'>Archive</option> </select>";
                print "</div>";
                print "</div>";
                print "<div class='row'>";
                print "<div class='col-2'>";
                print "<label class='form-label'>Record Type:</label><select name='lstRecordType' class='form-select select2 shadow-none'><option selected value=''>All</option> <option value='1'>Processed</option> <option value='0'>Un Processed</option> </select></td>";
                print "</div>";
                print "<div class='col-2'>";
                displayClockingType($lstClockingType);
                print "</div>";
                print "<div class='col-2'>";
                displayEmployeeStatus($conn, "lstEmployeeStatus", $lstEmployeeStatus, "", "");
                print "</div>";
                print "</div>";
                print "<div class='row'>";
                print "<div class='col-12'>";
                print "<center><br><input type='hidden' name='lstSort' value='tgate.name'></td><td><input name='btSearch' type='submit' class='btn btn-primary' value='Search Record'></center>";
                print "</div>";
                print "</div>";
            }
        ?>
        </form>
    </div>
</div>
<?php
 } 
print "</div></div></div></div>";
if ($act == "searchRecord") {
    $table_name = "Access.tenter";
    if ($lstDB == "Archive") {
        $table_name = "AccessArchive.archive_tenter";
    }
    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, " . $table_name . ".e_date, " . $table_name . ".e_time, tgate.name, tuser.idno, tuser.remark, " . $table_name . ".e_etc, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, tgroup, " . $table_name . ", tgate WHERE " . $table_name . ".e_group = tgroup.id AND " . $table_name . ".e_id = tuser.id AND " . $table_name . ".g_id = tgate.id AND tgate.Meal = 1 " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
    if ($lstShift != "") {
        $query = $query . " AND tgroup.id = " . $lstShift;
    }
    if ($lstTerminal != "") {
        $query = $query . " AND " . $table_name . ".g_id = " . $lstTerminal;
    }
    $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
    if ($txtFrom != "") {
        $query = $query . " AND " . $table_name . ".e_date >= '" . insertDate($txtFrom) . "'";
    }
    if ($txtTo != "") {
        $query = $query . " AND " . $table_name . ".e_date <= '" . insertDate($txtTo) . "'";
    }
    if ($txtTimeFrom != "") {
        $query = $query . " AND " . $table_name . ".e_time >= '" . $txtTimeFrom . "'";
    }
    if ($txtTimeTo != "") {
        $query = $query . " AND " . $table_name . ".e_time <= '" . $txtTimeTo . "'";
    }
    $query = queryClockingType($query, $lstClockingType);
    if ($lstRecordType != "") {
        $query = $query . " AND " . $table_name . ".p_flag = '" . $lstRecordType . "'";
    }
    $query = $query . employeeStatusQuery($lstEmployeeStatus);
    if ($lstDB != "Archive" && $lstSort != "") {
        $query = $query . " ORDER BY " . $lstSort;
    }
    print '<div class="row"><div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12"><div class="card"><div class="card-body table-responsive">';
    if ($prints != "yes") {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800' class='table table-striped table-bordered dataTable'>";
    } else {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%' class='table table-striped table-bordered dataTable'>";
    }
    print "<tr><td><font face='Verdana' size='2'>Terminal</font></td> <td><font face='Verdana' size='2'>Count</font></td></tr>";
    $result = mysqli_query($conn, $query);
    $count = 0;
    $tgate = "";
    while ($cur = mysqli_fetch_row($result)) {
        if ($tgate != $cur[7] && 0 < $count) {
            print "<tr>";
            print "<td><a title='Terminal'><font face='Verdana' size='1'>" . $tgate . "</font></a></td>";
            print "<td><a title='Count'><font face='Verdana' size='1'>" . $count . "</font></a></td>";
            print "</tr>";
            $count = 0;
        }
        $count++;
        $tgate = $cur[7];
    }
    if (0 < $count) {
        print "<tr>";
        print "<td><a title='Terminal'><font face='Verdana' size='1'>" . $tgate . "</font></a></td>";
        print "<td><a title='Count'><font face='Verdana' size='1'>" . $count . "</font></a></td>";
        print "</tr>";
    }
    print "</table>";
    if ($prints != "yes") {
        print "<center><br><input type='button' value='Print Report' class='btn btn-primary' onClick='checkPrint(0)'>&nbsp;&nbsp;<input type='button' class='btn btn-primary' value='Excel' onClick='checkPrint(1)'></center>";
    }
    print "</p>";
}
print "</form>";
print "</div></div></div></div></div>";
include 'footer.php';

?>