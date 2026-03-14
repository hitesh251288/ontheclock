<?php
ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "21";
set_time_limit(0);
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
$ot1f = $_SESSION[$session_variable . "ot1f"];
$ot2f = $_SESSION[$session_variable . "ot2f"];
$otdf = $_SESSION[$session_variable . "otdf"];
$macAddress = $_SESSION[$session_variable . "MACAddress"];
$frt = $_SESSION[$session_variable . "FlagReportText"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=YearlyAttendanceSummary.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$act = $_GET["act"];
if ($act == "") {
    $act = $_POST["act"];
}
$prints = $_GET["prints"];
$excel = $_GET["excel"];
$subReport = $_GET["subReport"];
$message = $_GET["message"];
if ($message == "") {
    $message = "Employee Absence Summary Report";
}
$lstShift = $_POST["lstShift"];
$lstDepartment = $_GET["lstDepartment"];
if ($lstDepartment == "") {
    $lstDepartment = $_POST["lstDepartment"];
}
$lstDivision = $_GET["lstDivision"];
if ($lstDivision == "") {
    $lstDivision = $_POST["lstDivision"];
}
$lstEmployeeIDFrom = $_POST["lstEmployeeIDFrom"];
$lstEmployeeIDTo = $_POST["lstEmployeeIDTo"];
$txtEmployeeCode = $_POST["txtEmployeeCode"];
$txtEmployee = $_POST["txtEmployee"];
$txtFrom = $_GET["txtFrom"];
if ($txtFrom == "") {
    $txtFrom = $_POST["txtFrom"];
}
$txtTo = $_GET["txtTo"];
if ($txtTo == "") {
    $txtTo = $_POST["txtTo"];
}
//if ($txtFrom == "") {
//    if (substr(insertToday(), 6, 2) == "01") {
//        if (substr(insertToday(), 4, 2) == "01") {
//            $txtFrom = "01/12/" . (substr(insertToday(), 0, 4) - 1);
//        } else {
//            if (substr(insertToday(), 4, 2) - 1 < 10) {
//                $txtFrom = "01/0" . (substr(insertToday(), 4, 2) - 1) . "/" . substr(insertToday(), 0, 4);
//            } else {
//                $txtFrom = "01/" . (substr(insertToday(), 4, 2) - 1) . "/" . substr(insertToday(), 0, 4);
//            }
//        }
//    } else {
//        $txtFrom = "01/" . substr(displayToday(), 3, 7);
//    }
//}
//if ($txtTo == "") {
//    $txtTo = displayDate(getLastDay(insertToday(), 1));
//}

if ($txtFrom == "") {
    // Set txtFrom to January 1st of the current year
    $txtFrom = "01/01/" . date("Y");
}

if ($txtTo == "") {
    // Set txtTo to December 31st of the current year
    $txtTo = "31/12/" . date("Y");
}

$txtRemark = $_GET["txtRemark"];
if ($txtRemark == "") {
    $txtRemark = $_POST["txtRemark"];
}
$txtPhone = $_GET["txtPhone"];
if ($txtPhone == "") {
    $txtPhone = $_POST["txtPhone"];
}
$txtSNo = $_GET["txtSNo"];
if ($txtSNo == "") {
    $txtSNo = $_POST["txtSNo"];
}
$lstGroup = $_GET["lstGroup"];
if ($lstGroup == "") {
    $lstGroup = $_POST["lstGroup"];
}
$lstEmployeeStatus = "";
if (isset($_POST["lstEmployeeStatus"])) {
    $lstEmployeeStatus = $_POST["lstEmployeeStatus"];
} else {
    if (isset($_GET["lstEmployeeStatus"])) {
        $lstEmployeeStatus = $_GET["lstEmployeeStatus"];
    } else {
        $lstEmployeeStatus = "ACT";
    }
}
$lstSort = $_POST["lstSort"];
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
$lstReportType = $_POST["lstReportType"];
$txtSatFactor = $_POST["txtSatFactor"];
if ($txtSatFactor == "" || is_numeric($txtSatFactor) == false) {
    $txtSatFactor = $ot1f;
}
$txtSunFactor = $_POST["txtSunFactor"];
if ($txtSunFactor == "" || is_numeric($txtSunFactor) == false) {
    $txtSunFactor = $ot2f;
}
$txtFlagFactor = $_POST["txtFlagFactor"];
if ($txtFlagFactor == "" || is_numeric($txtFlagFactor) == false) {
    $txtFlagFactor = $otdf;
}
$lstDB = $_POST["lstDB"];
if ($lstDB == "") {
    $lstDB = "Live";
}
$tflag = false;
if ($macAddress == "00-18-8B-8C-C9-D2") {
    $tflag = true;
} else {
    if ($macAddress == "00-15-5D-82-6E-0A") {
        $tflag = true;
    }
}
if ($prints != "yes") {
    include 'header.php';
    ?>
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-12 d-flex no-block align-items-center">
                <h4 class="page-title">Employee Absence Summary</h4>
                <div class="ms-auto text-end">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Employee Absence Summary
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <?php
}
//displaySuperHeader($prints, $excel, $csv, $current_module, $userlevel, "Weekly Monthly Summary", true, false);
if ($prints != "yes") {
    print'<div class="container-fluid">
                <div class="row">
                        <div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12">
                            <div class="card">
                                    <div class="card-body table-responsive">';
} else {
    if ($excel != "yes") {
        print "<body onLoad='javascript:window.print()'>";
    } else {
        ob_end_clean();
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=AbsenceSummary.xls");
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
            print "<p align='center'><font face='Verdana' size='1' color='#FF0000'><b>" . $message . "</b></font></p>";
            if ($prints != "yes") {
//        print "<table width='800' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
                print "<center><h4 class='card-title'><b>Select ONE or MORE options and click 'Search Record'</h4></center>";
            } else {
//        print "<table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
//        print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Selected Options</b></font></td></tr>";
            }
            print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='YearlyAttendanceSummary.php'><input type='hidden' name='act' value='searchRecord'><tr>";
            ?>
            <div class="row">
                <div class="col-2"></div>
                <div class="col-4">
            <?php
            $query = "SELECT id, name from tgroup WHERE ShiftTypeID = 1 ORDER BY name";
//            print "<tr>";
//            print "<td colspan='2'><table width='100%' cellspacing='-1' cellpadding='-1' border='0'><tr>";
            $query = "SELECT distinct(dept), dept from tuser " . $_SESSION[$session_variable . "DeptAccessWhereQuery"] . " ORDER BY UPPER(dept)";
            displaylist("lstDepartment", "Department: ", $lstDepartment, $prints, $conn, $query, "", "25%", "45%");
            print "</div>";
            print "<div class='col-4'>";
            displaytextbox("txtDepartment", ": ", $_POST["txtDepartment"], $prints, 25, "5%", "25%");
//            print "</tr></table></td>";
//            print "</tr>";
            print "</div>";
            print "<div class='col-2'></div>";
            print "</div>";
            print "<div class='row'>";
//            print "<td colspan='2'><table width='100%' cellspacing='-1' cellpadding='-1' border='0'><tr>";
            $query = "SELECT distinct(company), company from tuser " . $_SESSION[$session_variable . "DivAccessWhereQuery"] . " ORDER BY UPPER(company)";
            print "<div class='col-2'></div>";
            print "<div class='col-4'>";
            displaylist("lstDivision", "" . $_SESSION[$session_variable . "DivColumnName"] . ": ", $lstDivision, $prints, $conn, $query, "", "25%", "45%");
            print "</div>";
            print "<div class='col-4'>";
            displaytextbox("txtDivision", ": ", $_POST["txtDivision"], $prints, 25, "5%", "25%");
            print "</div>";
            print "<div class='col-2'></div>";
            print "</div>";
            $query = "SELECT id, name from tuser WHERE id > 0 " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ORDER BY tuser.name";
            $md_array = getmdarray($conn, $query);
            $v_eid = array_keys($md_array);
            $v_ename = array_values($md_array);
            sort($v_eid);
//            print "<tr>";
//            print "<td colspan='2'><table width='100%' cellspacing='-1' cellpadding='-1' border='0'><tr>";
            print "<div class='row'>";
            print "<div class='col-2'></div>";
            print "<div class='col-4'>";
            displayarraylist("lstEmployeeName", "Employee Name:", $lstEmployeeName, $prints, $v_ename, $v_ename, "onChange='putEmployeeName(this, document.frm1.txtEmployee)'", "25%", "45%");
            print "</div>";
            print "<div class='col-4'>";
            displaytextbox("txtEmployee", ": ", $txtEmployee, $prints, 25, "5%", "25%");
            print "</div>";
            print "<div class='col-2'></div>";
            print "</div>";
//            print "<tr>";
//            print "<td colspan='2'><table width='100%' cellspacing='-1' cellpadding='-1' border='0'><tr>";
            print "<div class='row'>";
            print "<div class='col-2'></div>";
            print "<div class='col-4'>";
            displayarraylist("lstEmployeeIDFrom", "Employee ID From:", $lstEmployeeIDFrom, $prints, $v_eid, $v_eid, "", "25%", "25%");
            print "</div>";
            print "<div class='col-4'>";
            displaytextbox("txtEmployeeCode", "Employee ID: ", $txtEmployeeCode, $prints, 12, "25%", "25%");
            print "</div>";
            print "<div class='col-2'></div>";
            print "</div>";
            print "<div class='row'>";
            print "<div class='col-2'></div>";
            print "<div class='col-4'>";
            displayarraylist("lstEmployeeIDTo", "Employee ID To:", $lstEmployeeIDTo, $prints, $v_eid, $v_eid, "", "25%", "25%");
            print "</div>";
            print "<div class='col-6'></div>";
            print "</div>";
            print "<div class='row'>";
            print "<div class='col-2'></div>";
            print "<div class='col-4'>";
            displayTextbox("txtFrom", "Date From <font size='1'>(DD/MM/YYYY)</font>: ", $txtFrom, $prints, 12, "25%", "25%");
            print "<td align='right' width='50%'></td>";
//    displayTextbox("txtSatFactor", "Saturday OT Factor: ", $txtSatFactor, $prints, 4, "25%", "25%");
            print "</div>";
            print "<div class='col-4'>";
            displayTextbox("txtTo", "Date To <font size='1'>(DD/MM/YYYY)</font>: ", $txtTo, $prints, 12, "25%", "25%");
            print "</div>";
            print "<div class='col-2'></div>";
            print "</div>";
            print "<div class='row'>";
            print "<div class='col-4'></div>";
            print "<div class='col-4'>";
            if ($prints != "yes") {
                print "<input name='btSearch' type='submit' value='Search Record' class='btn btn-primary'>&nbsp;&nbsp;<input type='button' value='Excel' onClick='checkPrint(1)' class='btn btn-primary'>&nbsp;&nbsp;<input type='button' value='CSV' onClick='checkPrint(3)' class='btn btn-primary'>";
            }
            print "</div>";
            print "<div class='col-4'></div>";
            print "</div>";
            print "</form><br>";
            ?>
        </div>
    </div>
    <?php
}
print '</div></div></div></div>';
if ($act == "searchRecord") {
    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tuser.OT1, tuser.OT2, tuser.idno, tuser.remark 
          FROM tuser, tgroup 
          WHERE tuser.group_id = tgroup.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
    if ($lstShift != "") {
        $query = $query . " AND tgroup.id = " . $lstShift;
    }

    $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $lstGroup, $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
    $query .= employeeStatusQuery($lstEmployeeStatus);
    $result = mysqli_query($conn, $query);
    $count = 0;
    print '<div class="row"><div class="col-md-12"><div class="card"><div class="card-body table-responsive">';
    print "<table width='800' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' class='table table-striped table-bordered dataTable'>";
    print "<tr>";
    print "<th><font face='Verdana' size='1'>ID</font></th>";
    print "<th><font face='Verdana' size='1'>Name</font></th>";
    print "<th><font face='Verdana' size='1'>Dept</font></th>";
    print "<th><font face='Verdana' size='1'>Division</font></th>";
    print "<th><font face='Verdana' size='1'>Gender</font></th>";
    print "<th><font face='Verdana' size='1'>Status</font></th>";
//print "<th><font face='Verdana' size='1'>Total Working Days</font></th>";
    print "<th><font face='Verdana' size='1'>Total Sunday</font></th>";
    print "<th><font face='Verdana' size='1'>Total Present Days</font></th>";
    print "<th><font face='Verdana' size='1'>Total Absent Days</font></th>";
    print "</tr>";

    while ($row = mysqli_fetch_row($result)) {
        $startDate = insertDate($txtFrom);  // Assuming insertDate returns a string like 'YYYYMMDD'
        $endDate = insertDate($txtTo);

        // Convert dates using strtotime
        $start_timestamp = strtotime($startDate);
        $end_timestamp = strtotime($endDate);

        // Calculate the total number of days between the two dates
        $t_days = floor(($end_timestamp - $start_timestamp) / (60 * 60 * 24)) + 1;

        // Calculate the number of Sundays between the two dates
        $sundays = 0;
        for ($i = $start_timestamp; $i <= $end_timestamp; $i += 60 * 60 * 24) {
            // Check if the current day is a Sunday (0 for Sunday in PHP 5.2)
            if (date('w', $i) == 0) {
                $sundays++;
            }
        }

        // Calculate working days excluding Sundays
        $t_working_days = $t_days - $sundays;

        // Output the rows as before
        print "<tr>";
        print "<td><font face='Verdana' size='1'>" . $row[0] . "</font></td>";
        print "<td><font face='Verdana' size='1'>" . $row[1] . "</font></td>";
        print "<td><font face='Verdana' size='1'>" . $row[2] . "</font></td>";
        print "<td><font face='Verdana' size='1'>" . $row[3] . "</font></td>";
        print "<td><font face='Verdana' size='1'>" . $row[7] . "</font></td>";
        print "<td><font face='Verdana' size='1'>" . $row[8] . "</font></td>";
        // Optionally print the working days and Sundays
        // print "<td><font face='Verdana' size='1'>$t_working_days</font></td>";
        print "<td><font face='Verdana' size='1'>$sundays</font></td>";

        // Continue calculating present and absent days
        $presentQuery = "SELECT COUNT(AttendanceID) FROM attendancemaster, tuser 
                     WHERE attendancemaster.EmployeeID = tuser.id 
                     AND EmployeeID = " . intval($row[0]) . " 
                     AND ADate >= '" . insertDate($txtFrom) . "' 
                     AND ADate <= '" . insertDate($txtTo) . "' 
                     AND (Flag = 'Black' OR Flag = 'Proxy' OR Flag = 'Purple' OR Flag = 'Aqua' OR Flag = 'Indigo')";

        $p_result = selectData($conn, $presentQuery);
        $p_days = $p_result[0];

        print "<td><font face='Verdana' size='1'>$p_days</font></td>";
        $a_days = max(0, $t_working_days - $p_days);  // Calculate absent days
        print "<td><font face='Verdana' size='1'>$a_days</font></td>";
        print "</tr>";

        $count++;
    }

    print "</table>";
    print "<p><center><font face='Verdana' size='1'>Total Records Display: $count</font></center></p>";
    print "</div></div></div></div></div>";
}
include 'footer.php';