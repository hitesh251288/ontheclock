<?php
ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "38";
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
    header("Location: " . $config["REDIRECT"] . "?url=WagesCalculation.php&message=Session Expired or Security Policy Violated");
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
    $message = "Wages Calculation Report";
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

if ($txtFrom == "") {
    $txtFrom = date('01/m/Y');
}

if ($txtTo == "") {
    $txtTo = date('t/m/Y');
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
                <h4 class="page-title">Wage Calculation Summary</h4>
                <div class="ms-auto text-end">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Wage Calculation Summary
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <?php
}
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
        header("Content-Disposition: attachment; filename=WagesCalculation.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        print "<body>";
    }
}
//displaySuperHeader($prints, $excel, $csv, $current_module, $userlevel, "Wage Calculation Summary", true, false);
if ($excel != "yes") { ?>
    <div class="card">
        <div class="card-body">
<?php 
    print "V.9.0.0.1<p align='center'><font face='Verdana' size='1' color='#FF0000'><b>" . $message . "</b></font></p>";
    if ($prints != "yes") {
        print "<center><h4 class='card-title'>Select ONE or MORE options and click 'Search Record'</h4></center>";
    }
    print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='DailyAttendanceSnapshot.php'><input type='hidden' name='act' value='searchRecord'>";
    $query = "SELECT id, name from tgroup WHERE ShiftTypeID = 1 ORDER BY name";
    print "<div class='row'>";
    print "<div class='col-3'>";
    $query = "SELECT distinct(dept), dept from tuser " . $_SESSION[$session_variable . "DeptAccessWhereQuery"] . " ORDER BY UPPER(dept)";
    displaylist("lstDepartment", "Department: ", $lstDepartment, $prints, $conn, $query, "", "25%", "45%");
    print "</div>";
    print "<div class='col-3'>";
    displaytextbox("txtDepartment", ": ", $_POST["txtDepartment"], $prints, 25, "5%", "25%");
    print "</div>";
    print "<div class='col-3'>";
    $query = "SELECT distinct(company), company from tuser " . $_SESSION[$session_variable . "DivAccessWhereQuery"] . " ORDER BY UPPER(company)";
    displaylist("lstDivision", "" . $_SESSION[$session_variable . "DivColumnName"] . ": ", $lstDivision, $prints, $conn, $query, "", "25%", "45%");
    print "</div>";
    print "<div class='col-3'>";
    displaytextbox("txtDivision", ": ", $_POST["txtDivision"], $prints, 25, "5%", "25%");
    print "</div>";
    print "</div>";
    $query = "SELECT id, name from tuser WHERE id > 0 " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ORDER BY tuser.name";
    $md_array = getmdarray($conn, $query);
    $v_eid = array_keys($md_array);
    $v_ename = array_values($md_array);
    sort($v_eid);
    print "<div class='row'>";
    print "<div class='col-3'>";
    displayarraylist("lstEmployeeName", "Employee Name:", $lstEmployeeName, $prints, $v_ename, $v_ename, "onChange='putEmployeeName(this, document.frm1.txtEmployee)'", "25%", "45%");
    print "</div>";
    print "<div class='col-3'>";
    displaytextbox("txtEmployee", ": ", $txtEmployee, $prints, 25, "5%", "25%");
    print "</div>";
    print "<div class='col-3'>";
    displayarraylist("lstEmployeeIDFrom", "Employee ID From:", $lstEmployeeIDFrom, $prints, $v_eid, $v_eid, "", "25%", "25%");
    print "</div>";
    print "<div class='col-3'>";
    displaytextbox("txtEmployeeCode", "Employee ID: ", $txtEmployeeCode, $prints, 12, "25%", "25%");
    print "</div>";
    print "</div>";
    print "<div class='row'>";
    print "<div class='col-3'>";
    displayarraylist("lstEmployeeIDTo", "Employee ID To:", $lstEmployeeIDTo, $prints, $v_eid, $v_eid, "", "25%", "25%");
    print "</div>";
    print "<div class='col-3'>";
    displayTextbox("txtFrom", "Date From <font size='1'>(DD/MM/YYYY)</font>: ", $txtFrom, $prints, 12, "25%", "25%");
//    displayTextbox("txtSatFactor", "Saturday OT Factor: ", $txtSatFactor, $prints, 4, "25%", "25%");
    print "</div>";
    print "<div class='col-3'>";
    displayTextbox("txtTo", "Date To <font size='1'>(DD/MM/YYYY)</font>: ", $txtTo, $prints, 12, "25%", "25%");
    print "</div>";
    print "</div>";
    print "<div class='row'>";
    print "<div class='col-12'>";
    if ($prints != "yes") {
        print "<center><input name='btSearch' type='submit' value='Search Record' class='btn btn-primary'>&nbsp;&nbsp;<input type='button' value='Excel' onClick='checkPrint(1)' class='btn btn-primary'>&nbsp;&nbsp;<input type='button' value='CSV' onClick='checkPrint(3)' class='btn btn-primary'></center>";
    }
    print "</div></div><br>";
}
print '</div></div></div></div>';
if ($act == "searchRecord") {
    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tuser.OT1, tuser.OT2, tuser.idno, tuser.remark, tuser.F1, tuser.group_id 
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
    print "<th><font face='Verdana' size='1'>Shift</font></th>";
    print "<th><font face='Verdana' size='1'>Status</font></th>";
    print "<th><font face='Verdana' size='1'>Group</font></th>";
    print "<th><font face='Verdana' size='1'>Public Holiday</font></th>";
//print "<th><font face='Verdana' size='1'>Total Working Days</font></th>";
    print "<th><font face='Verdana' size='1'>Monday To Friday</font></th>";
    print "<th><font face='Verdana' size='1'>Saturday</font></th>";
    print "<th><font face='Verdana' size='1'>Sunday</font></th>";
    print "<th><font face='Verdana' size='1'>Total Present Days</font></th>";
    print "<th><font face='Verdana' size='1'>Total Absent Days</font></th>";
    print "<th><font face='Verdana' size='1'>Monday To Friday Wages</font></th>";
    print "<th><font face='Verdana' size='1'>Saturday Wages</font></th>";
    print "<th><font face='Verdana' size='1'>Sunday Wages</font></th>";
    print "<th><font face='Verdana' size='1'>Public Holiday Wages</font></th>";
    print "<th><font face='Verdana' size='1'>Weekly Wages</font></th>";
    print "<th><font face='Verdana' size='1'>Monthly Wages</font></th>";
    print "<th><font face='Verdana' size='1'>Total Wages</font></th>";
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
        print "<td><font face='Verdana' size='1'>" . $row[4] . "</font></td>";
        print "<td><font face='Verdana' size='1'>" . $row[8] . "</font></td>";
        print "<td><font face='Verdana' size='1'>" . $row[9] . "</font></td>";
        $phQuery = "SELECT COUNT(AttendanceID) FROM attendancemaster, tuser 
                     WHERE attendancemaster.EmployeeID = tuser.id 
                     AND EmployeeID = " . intval($row[0]) . " 
                     AND ADate >= '" . insertDate($txtFrom) . "' 
                     AND ADate <= '" . insertDate($txtTo) . "' 
                     AND (Flag = 'Purple')";

        $ph_result = selectData($conn, $phQuery);
        $ph_days = $ph_result[0];
        print "<td><font face='Verdana' size='1'>$ph_days</font></td>";
        // Optionally print the working days and Sundays
        // print "<td><font face='Verdana' size='1'>$t_working_days</font></td>";
        /*         * ************Monday To Friday Work****************** */
        $mfQuery = "SELECT COUNT(AttendanceID) FROM attendancemaster, tuser 
                     WHERE attendancemaster.EmployeeID = tuser.id 
                     AND EmployeeID = " . intval($row[0]) . " 
                     AND ADate >= '" . insertDate($txtFrom) . "' 
                     AND ADate <= '" . insertDate($txtTo) . "' 
                     AND Day IN ('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday')";

        $mf_result = selectData($conn, $mfQuery);
        $mf_days = $mf_result[0];
        print "<td><font face='Verdana' size='1'>$mf_days</font></td>";
        /*         * ************Saturday Work****************** */
        $satQuery = "SELECT COUNT(AttendanceID) FROM attendancemaster, tuser 
                     WHERE attendancemaster.EmployeeID = tuser.id 
                     AND EmployeeID = " . intval($row[0]) . " 
                     AND ADate >= '" . insertDate($txtFrom) . "' 
                     AND ADate <= '" . insertDate($txtTo) . "' 
                     AND Day = 'Saturday'";
        $sat_result = selectData($conn, $satQuery);
        $sat_days = $sat_result[0];
        print "<td><font face='Verdana' size='1'>$sat_days</font></td>";
        /*         * ************Sunday Work****************** */
        $sunQuery = "SELECT COUNT(AttendanceID) FROM attendancemaster, tuser 
                     WHERE attendancemaster.EmployeeID = tuser.id 
                     AND EmployeeID = " . intval($row[0]) . " 
                     AND ADate >= '" . insertDate($txtFrom) . "' 
                     AND ADate <= '" . insertDate($txtTo) . "' 
                     AND Day = 'Sunday'";

        $sun_result = selectData($conn, $sunQuery);
        $sun_days = $sun_result[0];
        print "<td><font face='Verdana' size='1'>$sun_days</font></td>";
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
        /*$monFriWageQuery = "SELECT t.group_id, w.Category, w.Blgroup, w.MonFri  FROM tuser t "
                . "LEFT JOIN wagesmaster w ON w.Blgroup = t.F1 AND w.Category = (SELECT DISTINCT Category from wagesmaster w where w.ShiftId=t.group_id AND t.id=" . intval($row[0]) . ") "
                . "AND STR_TO_DATE('" . insertDate($txtFrom) . "','%Y%m%d') BETWEEN STR_TO_DATE(w.valid_from,'%Y%m%d') AND STR_TO_DATE(IFNULL(w.valid_to,'99991231'),'%Y%m%d') where t.id=" . intval($row[0]) . "";*/
        $monFriWageQuery = "
			SELECT t.group_id, w.Category, w.Blgroup, w.MonFri  
			FROM tuser t
			LEFT JOIN wagesmaster w 
			  ON w.Blgroup = t.F1 
			 AND w.Category IN (
					SELECT DISTINCT Category 
					FROM wagesmaster 
					WHERE ShiftId = t.group_id
				)
			 AND STR_TO_DATE('" . insertDate($txtFrom) . "','%Y%m%d') 
				 BETWEEN STR_TO_DATE(w.valid_from,'%Y%m%d') 
				 AND STR_TO_DATE(IFNULL(w.valid_to,'99991231'),'%Y%m%d')
			WHERE t.id = " . intval($row[0]);
		$monFriWage_result = selectData($conn, $monFriWageQuery);
        $monFriWage_cal = $monFriWage_result[3];
        $monTofriwage = $monFriWage_cal * $mf_days;
        print "<td><font face='Verdana' size='1'>$monTofriwage</font></td>";
        /*$satWageQuery = "SELECT t.group_id, w.Category, w.Blgroup, w.Sat  FROM tuser t "
                . "LEFT JOIN wagesmaster w ON w.Blgroup = t.F1 AND w.Category = (SELECT DISTINCT Category from wagesmaster w where w.ShiftId=t.group_id AND t.id=" . intval($row[0]) . ") "
                . "AND STR_TO_DATE('" . insertDate($txtFrom) . "','%Y%m%d') BETWEEN STR_TO_DATE(w.valid_from,'%Y%m%d') AND STR_TO_DATE(IFNULL(w.valid_to,'99991231'),'%Y%m%d') where t.id=" . intval($row[0]) . "";*/
        $satWageQuery = "
				SELECT t.group_id, w.Category, w.Blgroup, w.Sat  
				FROM tuser t
				LEFT JOIN wagesmaster w 
				  ON w.Blgroup = t.F1
				 AND w.ShiftId = t.group_id
				 AND STR_TO_DATE('" . insertDate($txtFrom) . "','%Y%m%d') 
					 BETWEEN STR_TO_DATE(w.valid_from,'%Y%m%d') 
					 AND STR_TO_DATE(IFNULL(w.valid_to,'99991231'),'%Y%m%d')
				WHERE t.id = " . intval($row[0]);
		$satWage_result = selectData($conn, $satWageQuery);
        $satWage_cal = $satWage_result[3];
        $satWages = $satWage_cal * $sat_days;
        print "<td><font face='Verdana' size='1'>$satWages</font></td>";
        /*$sunWageQuery = "SELECT t.group_id, w.Category, w.Blgroup, w.Sun  FROM tuser t "
                . "LEFT JOIN wagesmaster w ON w.Blgroup = t.F1 AND w.Category = (SELECT DISTINCT Category from wagesmaster w where w.ShiftId=t.group_id AND t.id=" . intval($row[0]) . ") "
                . "AND STR_TO_DATE('" . insertDate($txtFrom) . "','%Y%m%d') BETWEEN STR_TO_DATE(w.valid_from,'%Y%m%d') AND STR_TO_DATE(IFNULL(w.valid_to,'99991231'),'%Y%m%d') where t.id=" . intval($row[0]) . "";*/
        $sunWageQuery = "
				SELECT t.group_id, w.Category, w.Blgroup, w.Sun  
				FROM tuser t
				LEFT JOIN wagesmaster w 
				  ON w.Blgroup = t.F1
				 AND w.ShiftId = t.group_id
				 AND STR_TO_DATE('" . insertDate($txtFrom) . "','%Y%m%d') 
					 BETWEEN STR_TO_DATE(w.valid_from,'%Y%m%d') 
					 AND STR_TO_DATE(IFNULL(w.valid_to,'99991231'),'%Y%m%d')
				WHERE t.id = " . intval($row[0]);
		$sunWage_result = selectData($conn, $sunWageQuery);
        $sunWage_cal = (int)$sunWage_result[3];
        $sunWages = $sunWage_cal * (int)$sun_days;
        print "<td><font face='Verdana' size='1'>$sunWages</font></td>";
        /*$phWageQuery = "SELECT t.group_id, w.Category, w.Blgroup, w.PH  FROM tuser t "
                . "LEFT JOIN wagesmaster w ON w.Blgroup = t.F1 AND w.Category = (SELECT DISTINCT Category from wagesmaster w where w.ShiftId=t.group_id AND t.id=" . intval($row[0]) . ") "
                . "AND STR_TO_DATE('" . insertDate($txtFrom) . "','%Y%m%d') BETWEEN STR_TO_DATE(w.valid_from,'%Y%m%d') AND STR_TO_DATE(IFNULL(w.valid_to,'99991231'),'%Y%m%d') where t.id=" . intval($row[0]) . "";*/
        $phWageQuery = "
				SELECT t.group_id, w.Category, w.Blgroup, w.PH  
				FROM tuser t
				LEFT JOIN wagesmaster w 
				  ON w.Blgroup = t.F1
				 AND w.ShiftId = t.group_id
				 AND STR_TO_DATE('" . insertDate($txtFrom) . "','%Y%m%d') 
					 BETWEEN STR_TO_DATE(w.valid_from,'%Y%m%d') 
					 AND STR_TO_DATE(IFNULL(w.valid_to,'99991231'),'%Y%m%d')
				WHERE t.id = " . intval($row[0]);
		$phWage_result = selectData($conn, $phWageQuery);
        $phWage_cal = $phWage_result[3];
        $phWages = $phWage_cal * $ph_days;
        print "<td><font face='Verdana' size='1'>$phWages</font></td>";
        /*$weeklyWageQuery = "SELECT t.group_id, w.Category, w.Blgroup, w.WK 
                    FROM tuser t 
                    LEFT JOIN wagesmaster w 
                        ON w.Blgroup = t.F1 
                        AND w.Category = (SELECT DISTINCT Category from wagesmaster w where w.ShiftId=t.group_id AND t.id=" . intval($row[0]) . ") "
                    . "AND STR_TO_DATE('" . insertDate($txtFrom) . "','%Y%m%d') BETWEEN STR_TO_DATE(w.valid_from,'%Y%m%d') AND STR_TO_DATE(IFNULL(w.valid_to,'99991231'),'%Y%m%d')
                    WHERE t.id = " . intval($row[0]);*/
		$weeklyWageQuery = "
				SELECT t.group_id, w.Category, w.Blgroup, w.WK  
				FROM tuser t
				LEFT JOIN wagesmaster w 
				  ON w.Blgroup = t.F1
				 AND w.ShiftId = t.group_id
				 AND STR_TO_DATE('" . insertDate($txtFrom) . "','%Y%m%d') 
					 BETWEEN STR_TO_DATE(w.valid_from,'%Y%m%d') 
					 AND STR_TO_DATE(IFNULL(w.valid_to,'99991231'),'%Y%m%d')
				WHERE t.id = " . intval($row[0]);
        $weeklyWage_result = selectData($conn, $weeklyWageQuery);
        $weeklyWageAmount = isset($weeklyWage_result[3]) && $weeklyWage_result[3] !== '' ? $weeklyWage_result[3] : 0;

        $fromDate = date("Ymd", strtotime(insertDate($txtFrom)));
        $toDate = date("Ymd", strtotime(insertDate($txtTo)));

        $start = strtotime(insertDate($txtFrom));
        $end = strtotime(insertDate($txtTo));

        $totalWeeklyAmount = 0;

        // Align to Sunday instead of Monday
        if (date('w', $start) != 0) { // 0 = Sunday
            $start = strtotime('last sunday', $start);
        }

        while ($start <= $end) {
//            $weekStart = strtotime('monday this week', $start);
            $weekStart = $start;
            $weekEnd = strtotime('+6 days', $weekStart);

            $actualWeekStart = max($weekStart, strtotime($fromDate));
            $actualWeekEnd = min($weekEnd, strtotime($toDate));

            $actualWeekStartYMD = date("Ymd", $actualWeekStart);
            $actualWeekEndYMD = date("Ymd", $actualWeekEnd);

            $attendanceQuery = "SELECT COUNT(DISTINCT ADate) 
                        FROM attendancemaster 
                        WHERE EmployeeID = " . intval($row[0]) . " 
                        AND ADate >= '$actualWeekStartYMD' 
                        AND ADate <= '$actualWeekEndYMD' 
                        AND (Flag = 'Black' OR Flag = 'Proxy' OR Flag = 'Purple' OR Flag = 'Aqua' OR Flag = 'Indigo')";

            $weekResult = selectData($conn, $attendanceQuery);
            $presentDays = isset($weekResult[0]) ? intval($weekResult[0]) : 0;

            if ($presentDays >= 6) {
                $totalWeeklyAmount += $weeklyWageAmount;
            }

            $start = strtotime('+7 days', $weekStart);
        }

        echo "<td><font face='Verdana' size='1'>$totalWeeklyAmount</font></td>";
        /************Monthly Wages*****************/
        // Step 1: Fetch employee's Category and Monthly wage from wagesmaster
        /*$monthlyQuery = "SELECT t.group_id, w.Category, w.Blgroup, w.Monthly 
                 FROM tuser t 
                 LEFT JOIN wagesmaster w 
                    ON w.Blgroup = t.F1 
                    AND w.Category = (SELECT DISTINCT Category from wagesmaster w where w.ShiftId=t.group_id AND t.id=" . intval($row[0]) . ") 
                    AND w.Category IS NOT NULL AND STR_TO_DATE('" . insertDate($txtFrom) . "','%Y%m%d') BETWEEN STR_TO_DATE(w.valid_from,'%Y%m%d') AND STR_TO_DATE(IFNULL(w.valid_to,'99991231'),'%Y%m%d')
                 WHERE t.id = " . intval($row[0]);*/
		$monthlyQuery = "
				SELECT t.group_id, w.Category, w.Blgroup, w.Monthly  
				FROM tuser t 
				LEFT JOIN wagesmaster w 
				  ON w.Blgroup = t.F1
				 AND w.ShiftId = t.group_id
				 AND w.Category IS NOT NULL
				 AND STR_TO_DATE('" . insertDate($txtFrom) . "','%Y%m%d') 
					 BETWEEN STR_TO_DATE(w.valid_from,'%Y%m%d') 
					 AND STR_TO_DATE(IFNULL(w.valid_to,'99991231'),'%Y%m%d')
				WHERE t.id = " . intval($row[0]);
        $monthly_result = selectData($conn, $monthlyQuery);

        $category = isset($monthly_result[1]) ? strtolower(trim($monthly_result[1])) : '';
        $monthlyAmount = isset($monthly_result[3]) && $monthly_result[3] !== '' ? $monthly_result[3] : 0;

        $fromDate = date("Ymd", strtotime(insertDate($txtFrom)));
        $toDate = date("Ymd", strtotime(insertDate($txtTo)));

        $totalDays = 0;
        $monToSatDays = 0;
        $current = strtotime(insertDate($txtFrom));
        $end = strtotime(insertDate($txtTo));

        while ($current <= $end) {
            $dayOfWeek = date('w', $current); // 0=Sunday, 1=Monday, ..., 6=Saturday
            $totalDays++;
            if ($dayOfWeek != 0) { // Mon–Sat
                $monToSatDays++;
            }
            $current = strtotime('+1 day', $current);
        }

        $presentQuery = "SELECT COUNT(DISTINCT ADate) 
                 FROM attendancemaster 
                 WHERE EmployeeID = " . intval($row[0]) . " 
                 AND ADate >= '$fromDate' 
                 AND ADate <= '$toDate' 
                 AND (Flag = 'Black' OR Flag = 'Proxy' OR Flag = 'Purple' OR Flag = 'Aqua' OR Flag = 'Indigo')";

        $presentResult = selectData($conn, $presentQuery);
        $presentDays = isset($presentResult[0]) ? intval($presentResult[0]) : 0;

        $monthlyWage = 0;
    $fromMonthStart = date("Ymd", strtotime(date("Y-m-01", strtotime($fromDate))));
$toMonthEnd     = date("Ymd", strtotime(date("Y-m-t", strtotime($toDate))));

if ($fromDate == $fromMonthStart && $toDate == $toMonthEnd) {
        if ($category != '') {
            if ($category == 'cat_12') {
                // Allow 1 leave on any day
                $absentDays = $totalDays - $presentDays;
                if ($absentDays <= 1) {
                    $monthlyWage = $monthlyAmount;
                }
            } elseif ($category == 'cat_8') {
                // Must attend all Mon–Sat
                if ($presentDays >= $monToSatDays) {
                    $monthlyWage = $monthlyAmount;
                }
            }
            // Optionally add more categories with different rules here
        }
} else {
    // Not a full month → force to 0
    $monthlyWage = 0;
}
        echo "<td><font face='Verdana' size='1'>$monthlyWage</font></td>";
        /*****Total Wages Calculation*******/
        $totalWagesCal = $monTofriwage + $satWages + $sunWages + $phWages + $totalWeeklyAmount + $monthlyWage;
        /*********/
        echo "<td><font face='Verdana' size='1'>$totalWagesCal</font></td>";
        print "</tr>";

        $count++;
    }
    print "</table>";
    print "<p><center><font face='Verdana' size='1'>Total Records Display: $count</font></center></p>";
    print "</div></div></div></div>";
}
print "</div>";
include 'footer.php';