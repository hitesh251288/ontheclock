<?php
ob_start("ob_gzhandler");
error_reporting(E_ALL);
include "Functions.php";
$current_module = "21";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
$nightShiftMaxOutTime = $_SESSION[$session_variable . "NightShiftMaxOutTime"] . "00";
$txtMACAddress = $_SESSION[$session_variable . "MACAddress"];
$weirdTimeDisplay = false;
if (getWeirdClient($txtMACAddress)) {
    $weirdTimeDisplay = true;
}
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=ReportDailyRoster.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$act = $_GET["act"] ?? ($_POST["act"] ?? '');
$prints = $_GET["prints"] ?? '';
$timecard = $_GET["timecard"] ?? '';
$excel = $_GET["excel"] ?? '';
$csv = $_GET["csv"] ?? '';
$subReport = $_GET["subReport"] ?? '';
$message = $_GET["message"] ?? '';

if ($message == "") {
    $message = "Daily Roster (Daily Routine) <br>(Not Recommended for long Date Range)";
}
$lstShift = $_POST["lstShift"] ?? '';
$lstDepartment = $_POST["lstDepartment"] ?? '';
$lstDivision = $_POST["lstDivision"] ?? '';
$lstEmployeeID = $_GET["lstEmployeeID"] ?? '';
$lstEmployeeIDFrom = $_POST["lstEmployeeIDFrom"] ?? '';
$lstEmployeeIDTo = $_POST["lstEmployeeIDTo"] ?? '';

if ($lstEmployeeID != "" && $lstEmployeeIDFrom == "" && $lstEmployeeIDTo == "") {
    $lstEmployeeIDFrom = $lstEmployeeID;
    $lstEmployeeIDTo = $lstEmployeeID;
}

$txtEmployeeCode = $_POST["txtEmployeeCode"] ?? '';
$lstSort = $_POST["lstSort"] ?? '';
$txtEmployee = $_POST["txtEmployee"] ?? '';
$lstColourFlag = $_POST["lstColourFlag"] ?? ($_GET["lstColourFlag"] ?? '');
$txtFrom = $_POST["txtFrom"] ?? ($_GET["txtFrom"] ?? '');
$txtTo = $_POST["txtTo"] ?? ($_GET["txtTo"] ?? '');

if ($txtFrom == "") {
    $txtFrom = displayDate(getLastDay(insertToday(), 1));
}
if ($txtTo == "") {
    $txtTo = displayDate(getLastDay(insertToday(), 1));
}

$txtRemark = $_POST["txtRemark"] ?? '';
$txtPhone = $_POST["txtPhone"] ?? '';
$lstRemark = $_POST["lstRemark"] ?? '';
if ($lstRemark == "") {
    $lstRemark = "No";
}

$lstAbsent = $_POST["lstAbsent"] ?? ($_GET["lstAbsent"] ?? '');
if ($lstAbsent == "") {
    $lstAbsent = "Yes";
}

$lstTerminal = $_POST["lstTerminal"] ?? ($_GET["lstTerminal"] ?? '');
if ($lstTerminal == "") {
    $lstTerminal = "No";
}

$lstDayNight = $_POST["lstDayNight"] ?? ($_GET["lstDayNight"] ?? '');
if ($lstDayNight == "") {
    $lstDayNight = "No";
}

$lstImproperClocking = $_POST["lstImproperClocking"] ?? ($_GET["lstImproperClocking"] ?? '');
if ($lstImproperClocking == "") {
    $lstImproperClocking = "Yes";
}

$lstEmployeeSeparator = $_POST["lstEmployeeSeparator"] ?? 'Yes';
$lstAbsentShift = $_POST["lstAbsentShift"] ?? 'No';
$txtSNo = $_POST["txtSNo"] ?? '';

$lstEmployeeStatus = $_GET["lstEmployeeStatus"] ?? ($_POST["lstEmployeeStatus"] ?? 'ACT');

$lstIPEL = $_POST["lstIPEL"] ?? 'No';
$lstType = $_POST["lstType"] ?? '';
$lstColourFlag = $_POST["lstColourFlag"] ?? ($_GET["lstColourFlag"] ?? '');

$txtF1 = $_POST["txtF1"] ?? '';
$txtF2 = $_POST["txtF2"] ?? '';
$txtF3 = $_POST["txtF3"] ?? '';
$txtF4 = $_POST["txtF4"] ?? '';
$txtF5 = $_POST["txtF5"] ?? '';
$txtF6 = $_POST["txtF6"] ?? '';
$txtF7 = $_POST["txtF7"] ?? '';
$txtF8 = $_POST["txtF8"] ?? '';
$txtF9 = $_POST["txtF9"] ?? '';
$txtF10 = $_POST["txtF10"] ?? '';
$lstDB = $_POST["lstDB"] ?? '';
$lstEmployeeName = $_POST["lstEmployeeName"] ?? ($_GET["lstEmployeeName"] ?? '');
$lstGroup = $_POST["lstGroup"] ?? ($_GET["lstGroup"] ?? '');
if ($prints != "yes") {
    include 'header.php';
?>
<div class="page-breadcrumb">
                    <div class="row">
                        <div class="col-12 d-flex no-block align-items-center">
                            <h4 class="page-title">Daily Roster Report</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Daily Roster Report
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

<?php
}

$lstDB = $_POST["lstDB"] ?? ($_GET["lstDB"] ?? "Live");
if ($subReport != "yes") {
    displaySuperHeader($prints, $excel, $csv, $current_module, $userlevel, "Daily Roster Report", true, false);
}
if ($csv != "yes") {
    print "<style>@media print {h2 { page-break-before: always;}} </style>";
}
print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='ReportDailyRoster.php'><input type='hidden' name='act' value='searchRecord'>";
if ($excel != "yes" && $timecard != "yes" && $subReport != "yes") {
?>
<div class="card">
    <div class="card-body">
        <?php 
        print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b></font></p>";
        if ($prints != "yes") {
            print "<center><h4 class='card-title'>Select ONE or MORE options and click Search Record</h4></center>";
//            print "<table width='800' cellpadding='1' cellspacing='-1'>";
        //    print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Select ONE or MORE options and click 'Search Record'</b></font></td></tr>";
        } else {
            if ($excel != "yes" && $timecard != "yes" && $subReport != "yes") {
//                print "<table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
//                print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Selected Options</b></font></td></tr>";
            }
        }
        
        ?>
        <div class="row">
            <div class="col-3">
                <?php
                $query = "SELECT id, name from tgroup WHERE id > 1 AND ShiftTypeID = 1 ORDER BY name";
                displayList("lstShift", "Current Shift: ", $lstShift, $prints, $conn, $query, "", "25%", "75%");
                ?>
            </div>
            <?php 
            displaySearchFields($conn, $prints, $session_variable, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $lstEmployeeName, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $lstGroup, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
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
                if ($prints != "yes") {
                    displayColourFlag($conn, $lstColourFlag, "lstColourFlag", true, true);
                } else {
                    displayTextbox("lstColourFlag", "Flag: ", $lstColourFlag, $prints, 12, "", "");
                }
                ?>
            </div>
            <div class="col-2">
                <?php 
                displayTextbox("txtTo", "Date To <font size='1'>(DD/MM/YYYY)</font>: ", $txtTo, $prints, 12, "", "");
                ?>
            </div>
            <div class="col-2">
                <?php 
                if ($prints != "yes") {
                    print "<label class='form-label'>Work Type:</label><select name='lstType' class='form-select select2 shadow-none'><option selected value='" . $lstType . "'>" . $lstType . "</option> <option value='Early In'>Early In</option> <option value='Late In'>Late In</option> <option value='Less Break'>Less Break</option> <option value='More Break'>More Break</option> <option value='Early Out'>Early Out</option> <option value='Late Out'>Late Out</option> <option value='Grace'>Grace</option> <option value='OT'>OT</option> <option value='Approved OT'>Approved OT</option> <option value=''>---</option></select>";
                } else {
                    displayTextbox("lstType", "Work Type: ", $lstType, $prints, 12, "", "");
                }
                ?>
            </div>
            
        <?php 
            if ($prints != "yes") {
                print "<div class='col-2'>";
                print "<label class='form-label'>Print Remark Column:</label><select name='lstRemark' class='form-select select2 shadow-none'><option selected value='" . $lstRemark . "'>" . $lstRemark . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select>";
                print "</div>";
                print "<div class='col-2'>";
                print "<label class='form-label'>Display Shift of Absent Employees:</label><select name='lstAbsentShift' class='form-select select2 shadow-none'> <option selected value='" . $lstAbsentShift . "'>" . $lstAbsentShift . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select>";
                print "</div>";
                print "</div>";
                print "<div class='row'>";
                print "<div class='col-2'>";
                print "<label class='form-label'>Display Improper Clocking:</label><select name='lstImproperClocking' class='form-select select2 shadow-none'><option selected value='" . $lstImproperClocking . "'>" . $lstImproperClocking . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select>";
                print "</div>";
                print "<div class='col-2'>";
                print "<label class='form-label'>Display Employee Separator Row:</label><select name='lstEmployeeSeparator' class='form-select select2 shadow-none'><option selected value='" . $lstEmployeeSeparator . "'>" . $lstEmployeeSeparator . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select>";
                print "</div>";
                print "<div class='col-2'>";
                displayEmployeeStatus($conn, "lstEmployeeStatus", $lstEmployeeStatus, "", "");
                print "</div>";
                print "<div class='col-2'>";
                print "<label class='form-label'>Display Absent Days:</label><select name='lstAbsent' class='form-select select2 shadow-none'><option selected value='" . $lstAbsent . "'>" . $lstAbsent . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select>";
                print "</div>";
                print "<div class='col-2'>";
                print "<label class='form-label'>Display Clocking Terminal:</label><select name='lstTerminal' class='form-select select2 shadow-none'><option selected value='" . $lstTerminal . "'>" . $lstTerminal . "</option> <option value='Yes' selected>Yes</option> <option value='No'>No</option> </select></td>";
                print "</div>";
                print "<div class='col-2'>";
                print "<label class='form-label'>Display Day/Night Shift:</label><select name='lstDayNight' class='form-select select2 shadow-none'><option selected value='" . $lstDayNight . "'>" . $lstDayNight . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select>";
                print "</div>";
                print "</div>";
                print "<div class='row'>";
                print "<div class='col-2'>";
                print "<label class='form-label'>DB:</label><select name='lstDB' class='form-select select2 shadow-none'><option selected value='" . $lstDB . "'>" . $lstDB . "</option> <option value='Live'>Live</option> <option value='Archive'>Archive</option> </select>";
                print "</div>";
                print "<div class='col-2'>";
                $array = array(array("tuser.id", "Employee Code"), array("tuser.name, tuser.id", "Employee Name - Code"), array("tuser.dept, tuser.id", "Dept - Code"), array("tuser.company, tuser.dept, tuser.id", "Div/Desg - Dept - Code"), array("tuser.company, tuser.dept, tuser.group_id, tuser.id", "Div - Dept - Current Shift - Code"));
                displaySort($array, $lstSort, 5);
                print "</div>";
                print "</div>";
                print "<div class='row'>";
                print "<div class='col-12'>";
                print "<center><br><input name='btSearch' type='submit' class='btn btn-primary' value='Search Record'>&nbsp;&nbsp;<input type='button' class='btn btn-primary' value='Print Time Card' onClick='checkPrint(2)'></center>";
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
    if ($lstColourFlag != "" || $lstType != "") {
        $lstAbsent = "No";
        $lstImproperClocking = "No";
    }
    $query = "SELECT RosterColumns FROM OtherSettingMaster";
    $result = selectData($conn, $query);
    $txtRosterColumns = $result[0];
    if ($excel != "yes" && $timecard != "yes" && $subReport != "yes") {
        print "<p align='center'><font face='Verdana' size='1'><b>" . $_SESSION[$session_variable . "FlagReportText"] . "</b></font></p>";
    }
    print '<div class="row"><div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12"><div class="card"><div class="card-body table-responsive">';
    if ($prints != "yes") {
        print "<table border='1' cellpadding='0' bordercolor='#C0C0C0' cellspacing='-1' width='800' class='table table-striped table-bordered dataTable'>";
    } else {
        if ($csv != "yes") {
            print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%' class='table table-striped table-bordered dataTable'>";
        }
    }
    $count = 0;
    $sub_count = 0;
    $last_id = "";
    $last_name = "";
    $last_dept = "";
    $last_div = "";
    $last_idno = "";
    $last_rmk = "";
    $last_date = "";
    $font = "Black";
    $bgcolor = "#FFFFFF";
    $nextDate = insertDate($txtFrom);
    $pa_flag = true;
    $force_count = 0;
    $column_count_1 = 0;
    $column_count_2 = 0;
    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tuser.idno, tuser.remark, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, tgroup WHERE tuser.id > 0 AND tuser.group_id = tgroup.id " . ($_SESSION[$session_variable . "DivAccessQuery"] ?? '') . " " . ($_SESSION[$session_variable . "DivAccessQuery"] ?? '') . " ";
//    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tuser.idno, tuser.remark, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10, AttendanceMaster.Week, AttendanceMaster.Break FROM tuser, tgroup, AttendanceMaster WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.Flag NOT LIKE 'Delete' AND AttendanceMaster.EmployeeID = tuser.id AND tuser.id > 0 AND tuser.group_id = tgroup.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
    if ($lstShift != "") {
        $query = $query . " AND tgroup.id = " . $lstShift;
    }
    $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
    $query = $query . employeeStatusQuery($lstEmployeeStatus);
    if ($lstSort != "") {
        $query = $query . " ORDER BY " . $lstSort;
    } else {
        $query = $query . " ORDER BY tuser.id";
    }
    $main_result = mysqli_query($conn, $query);
    /*     * ************* */

    // Fetch the nwsprx value
    // Check if the nwsprx column exists in the othersettingmaster table
    $columnCheckQuery = "SHOW COLUMNS FROM `othersettingmaster` LIKE 'nwsprx'";
    $columnCheckResult = mysqli_query($conn, $columnCheckQuery);
    if (mysqli_num_rows($columnCheckResult) == 0) {
        // Add the nwsprx column if it does not exist
        $alterTableQuery = "ALTER TABLE `othersettingmaster` ADD `nwsprx` INT(1) NOT NULL DEFAULT '0'";
        mysqli_query($conn, $alterTableQuery);
    }

    // Fetch the nwsprx value
    $nwsquery = "SELECT nwsprx FROM othersettingmaster";
    $nwsresult = mysqli_query($conn, $nwsquery);
    $nwsData = mysqli_fetch_array($nwsresult);

    // Check if nwsprx is 1
    if ($nwsData['nwsprx'] == 1) {
        // Create the non_work_sat table if it does not exist
        $nonworksatquery = "CREATE TABLE IF NOT EXISTS `non_work_sat` (
                                `ID` int(11) NOT NULL AUTO_INCREMENT,
                                `OTDate` int(8) NOT NULL DEFAULT '0',
                                `Day` varchar(255) NOT NULL,
                                PRIMARY KEY (`ID`)
                              ) ENGINE=InnoDB DEFAULT CHARSET=latin1";
        $nonworksatResult = mysqli_query($conn, $nonworksatquery);

        // Check if the OTDate column exists in the non_work_sat table
        $columnCheckQuery = "SHOW COLUMNS FROM `non_work_sat` LIKE 'OTDate'";
        $columnCheckResult = mysqli_query($conn, $columnCheckQuery);
        if (mysqli_num_rows($columnCheckResult) == 0) {
            // Add the OTDate column if it does not exist
            $alterTableQuery = "ALTER TABLE `non_work_sat` ADD `OTDate` int(8) NOT NULL DEFAULT '0'";
            mysqli_query($conn, $alterTableQuery);
        }

        // Fetch the OTDate values from the non_work_sat table
        $nwsquery = "SELECT OTDate FROM non_work_sat ORDER BY OTDate";
        $nonworksat = mysqli_query($conn, $nwsquery);
        $nwsDate = [];
        while ($nwsRow = mysqli_fetch_array($nonworksat)) {
            $nwsDate[] = $nwsRow['OTDate'];
        }
    }


    /*$nwsquery = "SELECT nwsprx from othersettingmaster";
    $nwsresult = mysqli_query($conn, $nwsquery);
    $nwsData = mysqli_fetch_array($nwsresult);
    if ($nwsData['nwsprx'] == 1) {
        $nonworksatquery = "CREATE TABLE IF NOT EXISTS `non_work_sat` (
                                `ID` int(11) NOT NULL AUTO_INCREMENT,
                                `OTDate` int(8) NOT NULL DEFAULT '0',
                                `Day` varchar(255) NOT NULL,
                                PRIMARY KEY (`ID`)
                              ) ENGINE=InnoDB DEFAULT CHARSET=latin1";
        $nonworksatResult = mysqli_query($conn, $nonworksatquery);

        $nwsquery = "SELECT OTDate FROM non_work_sat ORDER BY OTDate";
        $nonworksat = mysqli_query($conn, $nwsquery);
        while ($nwsRow = mysqli_fetch_array($nonworksat)) {
            $nwsDate[] = $nwsRow['OTDate'];
        }
    }*/

    
    /*     * ******************** */
    while ($main_cur = mysqli_fetch_row($main_result)) {
        $sub_count = 0;
        $force_count++;
        if ($lstEmployeeSeparator == "Yes" && ($txtFrom != $txtTo || $force_count == 1) || $lstEmployeeSeparator == "No" && $force_count == 1) {
            if (1 < $force_count) {
                if ($timecard == "yes") {
                    print "<tr>";
                    if (strpos($txtRosterColumns, "chkShift") !== false) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Shift'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
                    }
                    if (strpos($txtRosterColumns, "chkOT1") !== false) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Special OT Day 1'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
                    }
                    if (strpos($txtRosterColumns, "chkOT2") !== false) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Special OT Day 2'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
                    }
                    print "<td bgcolor='" . $bgcolor . "'><a title='Date'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Day'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
                    if (strpos($txtRosterColumns, "chkFlag") !== false) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Flag'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
                    }
                    if (strpos($txtRosterColumns, "chkEntry") !== false) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Entry'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
                    }
                    if (strpos($txtRosterColumns, "chkStart") !== false) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Start'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
                    }
                    if (strpos($txtRosterColumns, "chkBreakOut") !== false) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Break Out'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
                    }
                    if (strpos($txtRosterColumns, "chkBreakIn") !== false) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Break In'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
                    }
                    if (strpos($txtRosterColumns, "chkClose") !== false) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Close'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
                    }
                    if (strpos($txtRosterColumns, "chkExit") !== false) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Exit'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
                    }
                    if (strpos($txtRosterColumns, "chkEarlyIn") !== false) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Early In'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></td>";
                    }
                    if (strpos($txtRosterColumns, "chkLateIn") !== false) {
                        if ($weirdTimeDisplay) {
                            getWeirdTime($tc_li);
                            print "<td bgcolor='" . $bgcolor . "'><a title='Late In'><font face='Verdana' size='1' color='" . $font . "'><b>" . getWeirdTime($tc_li) . "</b></font></td>";
                        } else {
                            round($tc_li / 60, 2);
                            print "<td bgcolor='" . $bgcolor . "'><a title='Late In'><font face='Verdana' size='1' color='" . $font . "'><b>" . round($tc_li / 60, 2) . "</b></font></td>";
                        }
                    }
                    if (strpos($txtRosterColumns, "chkLessBreak") !== false) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Less Break'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></td>";
                    }
                    if (strpos($txtRosterColumns, "chkMoreBreak") !== false) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='More Break'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></td>";
                    }
                    if (strpos($txtRosterColumns, "chkEarlyOut") !== false) {
                        if ($weirdTimeDisplay) {
                            getWeirdTime($tc_eo);
                            print "<td bgcolor='" . $bgcolor . "'><a title='Early Out'><font face='Verdana' size='1' color='" . $font . "'><b>" . getWeirdTime($tc_eo) . "</b></font></td>";
                        } else {
                            round($tc_eo / 60, 2);
                            print "<td bgcolor='" . $bgcolor . "'><a title='Early Out'><font face='Verdana' size='1' color='" . $font . "'><b>" . round($tc_eo / 60, 2) . "</b></font></td>";
                        }
                    }
                    if (strpos($txtRosterColumns, "chkLateOut") !== false) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Late Out'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></td>";
                    }
                    if (strpos($txtRosterColumns, "chkGrace") !== false) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Grace'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></td>";
                    }
                    if (strpos($txtRosterColumns, "chkNormal") !== false) {
                        round($tc_n / 3600, 2);
                        print "<td bgcolor='" . $bgcolor . "'><a title='Normal'><font face='Verdana' size='1' color='" . $font . "'><b>" . round($tc_n / 3600, 2) . "</b></font></td>";
                    }
                    if (strpos($txtRosterColumns, "chkOT") !== false) {
                        if ($weirdTimeDisplay) {
                            getWeirdTime($tc_ot);
                            print "<td bgcolor='" . $bgcolor . "'><a title='OT'><font face='Verdana' size='1' color='" . $font . "'><b>" . getWeirdTime($tc_ot) . "</b></font></td>";
                        } else {
                            round($tc_ot / 3600, 2);
                            print "<td bgcolor='" . $bgcolor . "'><a title='OT'><font face='Verdana' size='1' color='" . $font . "'><b>" . round($tc_ot / 3600, 2) . "</b></font></td>";
                        }
                    }
                    if (strpos($txtRosterColumns, "chkAppOT") !== false) {
                        if ($weirdTimeDisplay) {
                            if (getRegister($txtMACAddress, 7) == "25") {
                                print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'><b>&nbsp;</b></font></td>";
                                print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'><b>&nbsp;</b></font></td>";
                                getWeirdTime($tc_aot);
                                print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'><b>" . getWeirdTime($tc_aot) . "</b></font></td>";
                            } else {
                                getWeirdTime($tc_aot);
                                print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'><b>" . getWeirdTime($tc_aot) . "</b></font></td>";
                            }
                        } else {
                            if (getRegister($txtMACAddress, 7) == "25") {
                                print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'><b>&nbsp;</b></font></td>";
                                print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'><b>&nbsp;</b></font></td>";
                                round($tc_aot / 3600, 2);
                                print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'><b>" . round($tc_aot / 3600, 2) . "</b></font></td>";
                            } else {
                                round($tc_aot / 3600, 2);
                                print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'><b>" . round($tc_aot / 3600, 2) . "</b></font></td>";
                            }
                        }
                    }
                    if (strpos($txtRosterColumns, "chkTH") !== false) {
                        if ($weirdTimeDisplay) {
                            getWeirdTime($tc_t);
                            print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'><b>" . getWeirdTime($tc_t) . "</b></font></td>";
                        } else {
                            round($tc_t / 3600, 2);
                            print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'><b>" . round($tc_t / 3600, 2) . "</b></font></td>";
                        }
                    }
                    if ($txtMACAddress == "40-A8-F0-23-F0-AD") {
                        print "<td><font face='Verdana' size='1'>P</font></td>";
                    }
                    if ($lstRemark != "" && $prints == "yes") {
                        print "<td width=50><font face='Verdana' size='2'>&nbsp;</font></td>";
                    }
                    print "</tr>";
                    print "</table>";
                    print "<h2></h2>";
                    print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%'>";
                } else {
                    if ($csv != "yes") {
                        print "<tr><td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td>";
                        if (insertToday() < 20150331) {
                            print "<td><font face='Verdana' size='2'>&nbsp;</font></td>";
                        }
                        for ($j = 0; $j < $column_count_1; $j++) {
                            print "<td><font face='Verdana' size='1'>&nbsp;</font></td>";
                        }
                        $this_day = getDate(strtotime(substr($i, 6, 2) . "-" . substr($i, 4, 2) . "-" . substr($i, 0, 4)));
                        print "<td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td>";
                        for ($j = 0; $j < $column_count_2; $j++) {
                            print "<td><font face='Verdana' size='1'>&nbsp;</font></td>";
                        }
                        print "</tr>";
                    } else {
                        print ";;";
                        if (insertToday() < 20150331) {
                            print ";";
                        }
                        for ($j = 0; $j < $column_count_1; $j++) {
                            print ";";
                        }
                        $this_day = getDate(strtotime(substr($i, 6, 2) . "-" . substr($i, 4, 2) . "-" . substr($i, 0, 4)));
                        print ";;";
                        for ($j = 0; $j < $column_count_2; $j++) {
                            print ";";
                        }
                        print "\n";
                    }
                }
            }
//
            if ($timecard == "yes") {
                print "<tr><td colspan='20'><font face='Verdana' size='2'>";
                print "<b>ID:</b> " . $main_cur[0];
                print "<br><br><b>Name:</b> " . $main_cur[1];
                print "<br><br><b>Dept:</b> " . $main_cur[2];
                print "<br><br><b>Div/ Desgn:</b> " . $main_cur[3];
                print "<br><br><b>" . $_SESSION[$session_variable . "IDColumnName"] . ":</b> " . $main_cur[5];
                print "<br><br><b>Remark:</b> " . $main_cur[6];
                print "<br><br><b>From:</b> " . $txtFrom . " - <b>To:</b>" . $txtTo;
                print "</font></td></tr>";
            }
            $column_count_1 = 0;
            $column_count_2 = 0;
            if ($csv != "yes") {
                print "<thead><tr>";
            }
//            if ($timecard != "yes") {
//                if ($csv != "yes") {
//                    print "<tr><td><font face='Verdana' size='1'><input type='checkbox' name='chkAOT' onClick='javascript:approveAll()'></td>";
//                } else {
//                    print "&nbsp;";
//                }
//            }

            if ($timecard != "yes") {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td>";
                } else {
                    print "ID;Name;";
                }
            }
            if (insertToday() < 20150331 && $timecard != "yes") {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>TRML</font></td>";
                } else {
                    print "TRML;";
                }
            }
            if (strpos($txtRosterColumns, "chkIDColumn") !== false && $timecard != "yes") {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td>";
                } else {
                    print $_SESSION[$session_variable . "IDColumnName"] . ";";
                }
                $column_count_1++;
            }
            if (strpos($txtRosterColumns, "chkDept") !== false && $timecard != "yes") {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>Dept</font></td>";
                } else {
                    print "Dept;";
                }
                $column_count_1++;
            }
            if (strpos($txtRosterColumns, "chkDiv") !== false && $timecard != "yes") {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "DivColumnName"] . "</font></td>";
                } else {
                    print $_SESSION[$session_variable . "DivColumnName"] . ";";
                }
                $column_count_1++;
            }
            if (strpos($txtRosterColumns, "chkRmk") !== false && $timecard != "yes") {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "RemarkColumnName"] . "</font></td>";
                } else {
                    print $_SESSION[$session_variable . "RemarkColumnName"] . ";";
                }
                $column_count_1++;
            }
            if (strpos($txtRosterColumns, "chkF1") !== false && $timecard != "yes") {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "F1"] . "</font></td>";
                } else {
                    print $_SESSION[$session_variable . "F1"] . ";";
                }
                $column_count_1++;
            }
            if (strpos($txtRosterColumns, "chkF2") !== false && $timecard != "yes") {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "F2"] . "</font></td>";
                } else {
                    print $_SESSION[$session_variable . "F2"] . ";";
                }
                $column_count_1++;
            }
            if (strpos($txtRosterColumns, "chkF3") !== false && $timecard != "yes") {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "F3"] . "</font></td>";
                } else {
                    print $_SESSION[$session_variable . "F3"] . ";";
                }
                $column_count_1++;
            }
            if (strpos($txtRosterColumns, "chkF4") !== false && $timecard != "yes") {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "F4"] . "</font></td>";
                } else {
                    print $_SESSION[$session_variable . "F4"] . ";";
                }
                $column_count_1++;
            }
            if (strpos($txtRosterColumns, "chkF5") !== false && $timecard != "yes") {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "F5"] . "</font></td>";
                } else {
                    print $_SESSION[$session_variable . "F5"] . ";";
                }
                $column_count_1++;
            }
            if (strpos($txtRosterColumns, "chkShift") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>Shift</font></td>";
                } else {
                    print "Shift;";
                }
                $column_count_1++;
            }
            if (strpos($txtRosterColumns, "chkOT1") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>OT 1</font></td>";
                } else {
                    print "OT 1;";
                }
                $column_count_1++;
            }
            if (strpos($txtRosterColumns, "chkOT2") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>OT 2</font></td>";
                } else {
                    print "OT 2;";
                }
                $column_count_1++;
            }
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='2'>Date</font></td> <td><font face='Verdana' size='2'>Day</font></td><td><font face='Verdana' size='2'>Week</font></td>";
            } else {
                print "Date;Day;Week;";
            }
            if (strpos($txtRosterColumns, "chkFlag") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>Flag</font></td>";
                } else {
                    print "Flag;";
                }
                $column_count_2++;
            }
            if (strpos($txtRosterColumns, "chkEntry") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>Entry</font></td>";
                } else {
                    print "Entry;";
                }
                $column_count_2++;
            }
            if ($lstTerminal == "Yes") {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>Terminal</font></a></td>";
                } else {
                    print "Terminal;";
                }
                $column_count_2++;
            }
            if (strpos($txtRosterColumns, "chkStart") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'><b>Start</b></font></td>";
                } else {
                    print "Start;";
                }
                $column_count_2++;
            }
            if (strpos($txtRosterColumns, "chkBreakOut") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>BreakOut</font></td>";
                } else {
                    print "BreakOut;";
                }
                $column_count_2++;
            }
            if (strpos($txtRosterColumns, "chkBreakIn") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>BreakIn</font></td>";
                } else {
                    print "BreakIn;";
                }
                $column_count_2++;
            }
            if (strpos($txtRosterColumns, "chkClose") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'><b>Close</b></font></td>";
                } else {
                    print "Close;";
                }
                $column_count_2++;
            }
            if ($lstTerminal == "Yes") {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>Terminal</font></a></td>";
                } else {
                    print "Terminal;";
                }
                $column_count_2++;
            }
            if (strpos($txtRosterColumns, "chkExit") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>Exit</font></td>";
                } else {
                    print "Exit;";
                }
                $column_count_2++;
            }
            if (strpos($txtRosterColumns, "chkEarlyIn") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>Early In <br>(Min)</font></td>";
                } else {
                    print "Early In;";
                }
                $column_count_2++;
            }
            if (strpos($txtRosterColumns, "chkLateIn") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>Late In <br>(Min)</font></td>";
                } else {
                    print "Late In;";
                }
                $column_count_2++;
            }
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='2'> Break <br> (Min)</font></td>";
            } else {
                print "Break;";
            }
            $column_count_2++;
            if (strpos($txtRosterColumns, "chkLessBreak") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>Less Break <br>(Min)</font></td>";
                } else {
                    print "Less Break;";
                }
                $column_count_2++;
            }
            if (strpos($txtRosterColumns, "chkMoreBreak") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>More Break <br>(Min)</font></td>";
                } else {
                    print "More Break;";
                }
                $column_count_2++;
            }
            if (strpos($txtRosterColumns, "chkEarlyOut") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>Early Out <br>(Min)</font></td>";
                } else {
                    print "Early Out;";
                }
                $column_count_2++;
            }
            if (strpos($txtRosterColumns, "chkLateOut") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>Late Out <br>(Min)</font></td>";
                } else {
                    print "Late Out;";
                }
                $column_count_2++;
            }
            if (strpos($txtRosterColumns, "chkGrace") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>Grace <br>(Min)</font></td>";
                } else {
                    print "Grace;";
                }
                $column_count_2++;
            }
            if (strpos($txtRosterColumns, "chkNormal") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>Normal <br>(Hrs)</font></td>";
                } else {
                    print "Normal;";
                }
                $column_count_2++;
            }
            if (strpos($txtRosterColumns, "chkOT") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>OT <br>(Hrs)</font></td>";
                } else {
                    print "OT Hrs;";
                }
                $column_count_2++;
            }
            if (strpos($txtRosterColumns, "chkAppOT") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>App OT <br>(Hrs)</font></td>";
                    if (getRegister($txtMACAddress, 7) == "25") {
                        print "<td><font face='Verdana' size='2'>App Late In<br>(Min)</font></td>";
                        print "<td><font face='Verdana' size='2'>Total App OT<br>(Hrs)</font></td>";
                    }
                } else {
                    print "App OT Hrs;";
                    if (getRegister($txtMACAddress, 7) == "25") {
                        print "App Late In (Min);";
                        print "Total App OT (Hrs);";
                    }
                }
                $column_count_2++;
                if (getRegister($txtMACAddress, 7) == "25") {
                    $column_count_2++;
                    $column_count_2++;
                }
            }
            if (strpos($txtRosterColumns, "chkTH") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>Total <br>(Hrs)</font></td>";
                } else {
                    print "Total Hrs;";
                }
                $column_count_2++;
            }
            if ($lstRemark != "" && $prints == "yes") {
                if ($csv != "yes") {
                    print "<td width=50><font face='Verdana' size='2'>Rmk</font></td>";
                } else {
                    print "Rmk;";
                }
                $column_count_2++;
            }
            if ($txtMACAddress == "40-A8-F0-23-F0-AD") {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2'>P/A</font></td>";
                } else {
                    print "P/A;";
                }
                $column_count_2++;
            }
            if ($csv != "yes") {
                print "</tr></thead>";
            } else {
                print "\n";
            }
        }
//        $table_name = "Access.DayMaster";
//        $table_name_ = "Access.AttendanceMaster";
//        $_table_name = "Access.tenter";
        $table_name = "DayMaster";
        $table_name_ = "AttendanceMaster";
        $_table_name = "tenter";
        if ($lstDB == "Archive") {
            $table_name = "AccessArchive.archive_dm";
            $table_name_ = "AccessArchive.archive_am";
            $_table_name = "AccessArchive.archive_tenter";
        }

        $query = "SELECT tgroup.name, " . $table_name . ".TDate, " . $table_name . ".Entry, " . $table_name . ".Start, " . $table_name . ".BreakOut, " . $table_name . ".BreakIn, " . $table_name . ".Close, " . $table_name . ".Exit, " . $table_name . ".Flag, " . $table_name_ . ".Day, " . $table_name_ . ".EarlyIn, " . $table_name_ . ".LateIn, " . $table_name_ . ".Break, " . $table_name_ . ".LessBreak, " . $table_name_ . ".MoreBreak, " . $table_name_ . ".EarlyOut, " . $table_name_ . ".LateOut, " . $table_name_ . ".Normal, " . $table_name_ . ".Grace, " . $table_name_ . ".Overtime, " . $table_name_ . ".AOvertime, " . $table_name_ . ".OT1, " . $table_name_ . ".OT2, " . $table_name_ . ".LateIn_flag, " . $table_name_ . ".EarlyOut_flag, " . $table_name_ . ".MoreBreak_flag, " . $table_name_ . ".LateInColumn, " . $table_name_ . ".Week, " . $table_name_ . ".Break, " . $table_name_ . ".AttendanceID FROM tgroup, " . $table_name . ", " . $table_name_ . " WHERE " . $table_name . ".e_id > 0 AND " . $table_name . ".group_id = tgroup.id AND " . $table_name . ".e_id = " . $table_name_ . ".EmployeeID AND " . $table_name . ".TDate = " . $table_name_ . ".ADate AND " . $table_name . ".e_id = " . $main_cur[0] . " AND " . $table_name . ".TDate >= " . insertDate($txtFrom) . " AND " . $table_name . ".TDate <= " . insertDate($txtTo);
        if ($lstColourFlag != "") {
            if ($lstColourFlag == "Black/Proxy") {
                $query = $query . " AND (" . $table_name_ . ".Flag = 'Black' OR " . $table_name_ . ".Flag = 'Proxy') ";
            } else {
                if ($lstColourFlag == "All w/o Black/Proxy") {
                    $query = $query . " AND " . $table_name_ . ".Flag NOT LIKE 'Black' AND " . $table_name_ . ".Flag NOT LIKE 'Proxy'";
                } else {
                    if ($lstColourFlag == "All w/o Proxy") {
                        $query = $query . " AND " . $table_name_ . ".Flag NOT LIKE 'Proxy'";
                    } else {
                        $query = $query . " AND " . $table_name_ . ".Flag = '" . $lstColourFlag . "'";
                    }
                }
            }
        }//AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.Flag NOT LIKE 'Delete' AND AttendanceMaster.EmployeeID = tuser.id AND 
        if ($lstType != "") {
            if ($lstType == "Early In") {
                $query = $query . " AND " . $table_name_ . ".EarlyIn > 0 ";
            } else {
                if ($lstType == "Late In") {
                    $query = $query . " AND " . $table_name_ . ".LateIn > 0 ";
                } else {
                    if ($lstType == "Less Break") {
                        $query = $query . " AND " . $table_name_ . ".LessBreak > 0 ";
                    } else {
                        if ($lstType == "More Break") {
                            $query = $query . " AND " . $table_name_ . ".MoreBreak > 0 ";
                        } else {
                            if ($lstType == "Early Out") {
                                $query = $query . " AND " . $table_name_ . ".EarlyOut > 0 ";
                            } else {
                                if ($lstType == "Late Out") {
                                    $query = $query . " AND " . $table_name_ . ".LateOut > 0 ";
                                } else {
                                    if ($lstType == "Grace") {
                                        $query = $query . " AND " . $table_name_ . ".Grace > 0 ";
                                    } else {
                                        if ($lstType == "OT") {
                                            $query = $query . " AND " . $table_name_ . ".Overtime > 0 ";
                                        } else {
                                            if ($lstType == "Approved OT") {
                                                $query = $query . " AND " . $table_name_ . ".AOvertime > 0 ";
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $query = $query . " ORDER BY " . $table_name . ".TDate";
        $nextDate = insertDate($txtFrom);
        $result = mysqli_query($conn, $query);
        $tc_li = 0;
        $tc_eo = 0;
        $tc_n = 0;
        $tc_ot = 0;
        $tc_aot = 0;
        $tc_t = 0;
        $chkcount = 0;
        // echo $result->num_rows;
        // echo "<pre>";print_R($result);die;
        if($result->num_rows > 0){
        while ($cur = mysqli_fetch_array($result)) { //echo "<pre>";print_R($cur);die;
            $sub_count++;
            $chkcount++;
            if ($nextDate < $cur[1]) {
                $shift = " ";
                if ($lstAbsentShift == "Yes") {
                    $shift = $cur[0];
                }
                if ($lstDayNight == "Yes") {
                    $shift = "Absent";
                }
                $count = displayAlterTime($conn, $nextDate, $cur[1] - 1, $main_cur[0], $main_cur[1], $main_cur[2], $main_cur[3], $main_cur[5], $main_cur[6], $main_cur[7], $main_cur[8], $main_cur[9], $main_cur[10], $main_cur[11], $column_count_1, $column_count_2, $txtRosterColumns, $prints, $count, $lstImproperClocking, $lstAbsent, $shift, $txtMACAddress, $timecard, $lstTerminal, $lstDB, $csv, $cur[28]);
            }
            if ($cur[8] != "" && strpos($txtRosterColumns, "chkFlag") !== false) {
                $font = $cur[8];
                if ($font == "Yellow") {
                    $bgcolor = "Brown";
                } else {
                    $bgcolor = "#FFFFFF";
                }
            } else {
                $cur[8] = "";
                $font = "Black";
                $bgcolor = "#FFFFFF";
            }
            if ($main_cur[3] == "") {
                $main_cur[3] = " ";
            }
            if ($main_cur[5] == "") {
                $main_cur[5] = " ";
            }
            if ($main_cur[6] == "") {
                $main_cur[6] = " ";
            }
            if ($main_cur[7] == "") {
                $main_cur[7] = " ";
            }
            if ($main_cur[8] == "") {
                $main_cur[8] = " ";
            }
            if ($main_cur[9] == "") {
                $main_cur[9] = " ";
            }
            if ($main_cur[10] == "") {
                $main_cur[10] = " ";
            }
            if ($main_cur[11] == "") {
                $main_cur[11] = " ";
            }
            if ($csv != "yes") {
                print "<tr>";
            }
//            print " <td><font face='Verdana' size='2'>";
//             if ($timecard != "yes") {
//            if ($csv != "yes") {
//                    print "<input type='checkbox' name='chkAOT' onClick='javascript:approveAll()'>";
//                } else {
//                    print "&nbsp;";
//                }
//             }
//            echo "<pre>";print_R($cur);
//            if ($prints != "yes") {
//                print "<tr><td><input type='checkbox' name='chkAOT" . $chkcount . "' id='chkAOT" . $chkcount . "'> <input type='hidden' name='txhID" . $chkcount . "' value='" . $cur[29] . "'> <input type='hidden' name='txtOT" . $chkcount . "' value='" . $cur[19] . "'> <input type='hidden' name='txtNormal" . $chkcount . "' value='" . $cur[17] . "'> <input type='hidden' name='txtLateIn" . $chkcount . "' value='" . $cur[11] . "'> <input type='hidden' name='txtMoreBreak" . $chkcount . "' value='" . $cur[14] . "'> <input type='hidden' name='txtEarlyOut" . $chkcount . "' value='" . $cur[15] . "'> <input type='hidden' name='txtBreak" . $chkcount . "' value='" . $cur[12] . "'></td> ";
//            } else {
//                print "<tr><td><font size='1'>&nbsp;</font></td>";
//            }

            if ($timecard != "yes") {
                if ($csv != "yes") {
                    if (is_array($nwsDate)) {
                        if ($cur[19] != 0 && in_array($cur[1], $nwsDate)) {
                            addZero($main_cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
                            print "<td bgcolor='" . $bgcolor . "'><a title='ID'><font face='Verdana' size='1' color='pink'>" . addZero($main_cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Name'><font face='Verdana' size='1' color='pink'>" . $main_cur[1] . "</font></a></td>";
                        } else {
                            addZero($main_cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
                            print "<td bgcolor='" . $bgcolor . "'><a title='ID'><font face='Verdana' size='1' color='" . $font . "'>" . addZero($main_cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Name'><font face='Verdana' size='1' color='" . $font . "'>" . $main_cur[1] . "</font></a></td>";
                        }
                    }else{
                        $nwsDate = [];
                    }
                } else {
                    addZero($main_cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
                    print addZero($main_cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . ";" . $main_cur[1] . ";";
                }
            }
            if (insertToday() < 20150331 && $timecard != "yes") {
                $terminal_query = "SELECT g_id, tgate.name FROM " . $_table_name . ", tgate WHERE " . $_table_name . ".e_id = '" . $main_cur[0] . "' AND " . $_table_name . ".e_date = '" . $cur[1] . "' AND " . $_table_name . ".g_id = tgate.id ";
                $terminal_result = selectData($conn, $terminal_query);
                if ($csv != "yes") {
                    print "<td bgcolor='" . $bgcolor . "'><a title='TRML'><font face='Verdana' size='1' color='" . $font . "'>" . $terminal_result[1] . "</font></a></td>";
                } else {
                    print $terminal_result[1] . ";";
                }
            }
            if (strpos($txtRosterColumns, "chkIDColumn") !== false && $timecard != "yes") {
                if ($csv != "yes") {
                    if ($cur[19] != 0 && in_array($cur[1], $nwsDate)) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1' color='pink'>" . $main_cur[5] . "</font></a></td>";
                    } else {
                        print "<td bgcolor='" . $bgcolor . "'><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1' color='" . $font . "'>" . $main_cur[5] . "</font></a></td>";
                    }
                } else {
                    print $main_cur[5] . ";";
                }
            }
            if (strpos($txtRosterColumns, "chkDept") !== false && $timecard != "yes") {
                if ($csv != "yes") {
                    if ($cur[19] != 0 && in_array($cur[1], $nwsDate)) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Dept'><font face='Verdana' size='1' color='pink'>" . $main_cur[2] . "</font></a></td>";
                    } else {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Dept'><font face='Verdana' size='1' color='" . $font . "'>" . $main_cur[2] . "</font></a></td>";
                    }
                } else {
                    print $main_cur[2] . ";";
                }
            }
            if (strpos($txtRosterColumns, "chkDiv") !== false && $timecard != "yes") {
                if ($csv != "yes") {
                    if ($cur[19] != 0 && in_array($cur[1], $nwsDate)) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Div/Desg'><font face='Verdana' size='1' color='pink'>" . $main_cur[3] . "</font></a></td>";
                    } else {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Div/Desg'><font face='Verdana' size='1' color='" . $font . "'>" . $main_cur[3] . "</font></a></td>";
                    }
                } else {
                    print $main_cur[3] . ";";
                }
            }
            if (strpos($txtRosterColumns, "chkRmk") !== false && $timecard != "yes") {
                if ($csv != "yes") {
                    if ($cur[19] != 0 && in_array($cur[1], $nwsDate)) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Rmk'><font face='Verdana' size='1' color='pink'>" . $main_cur[6] . "</font></a></td>";
                    } else {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Rmk'><font face='Verdana' size='1' color='" . $font . "'>" . $main_cur[6] . "</font></a></td>";
                    }
                } else {
                    print $main_cur[6] . ";";
                }
            }
            if (strpos($txtRosterColumns, "chkF1") !== false && $timecard != "yes") {
                if ($csv != "yes") {
                    if ($cur[19] != 0 && in_array($cur[1], $nwsDate)) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='F1'><font face='Verdana' size='1' color='pink'>" . $main_cur[7] . "</font></a></td>";
                    } else {
                        print "<td bgcolor='" . $bgcolor . "'><a title='F1'><font face='Verdana' size='1' color='" . $font . "'>" . $main_cur[7] . "</font></a></td>";
                    }
                } else {
                    print $main_cur[7] . ";";
                }
            }
            if (strpos($txtRosterColumns, "chkF2") !== false && $timecard != "yes") {
                if ($csv != "yes") {
                    if ($cur[19] != 0 && in_array($cur[1], $nwsDate)) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='F2'><font face='Verdana' size='1' color='pink'>" . $main_cur[8] . "</font></a></td>";
                    } else {
                        print "<td bgcolor='" . $bgcolor . "'><a title='F2'><font face='Verdana' size='1' color='" . $font . "'>" . $main_cur[8] . "</font></a></td>";
                    }
                } else {
                    print $main_cur[8] . ";";
                }
            }
            if (strpos($txtRosterColumns, "chkF3") !== false && $timecard != "yes") {
                if ($csv != "yes") {
                    if ($cur[19] != 0 && in_array($cur[1], $nwsDate)) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='F3'><font face='Verdana' size='1' color='pink'>" . $main_cur[9] . "</font></a></td>";
                    } else {
                        print "<td bgcolor='" . $bgcolor . "'><a title='F3'><font face='Verdana' size='1' color='" . $font . "'>" . $main_cur[9] . "</font></a></td>";
                    }
                } else {
                    print $main_cur[9] . ";";
                }
            }
            if (strpos($txtRosterColumns, "chkF4") !== false && $timecard != "yes") {
                if ($csv != "yes") {
                    if ($cur[19] != 0 && in_array($cur[1], $nwsDate)) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='F4'><font face='Verdana' size='1' color='pink'>" . $main_cur[10] . "</font></a></td>";
                    } else {
                        print "<td bgcolor='" . $bgcolor . "'><a title='F4'><font face='Verdana' size='1' color='" . $font . "'>" . $main_cur[10] . "</font></a></td>";
                    }
                } else {
                    print $main_cur[10] . ";";
                }
            }
            if (strpos($txtRosterColumns, "chkF5") !== false && $timecard != "yes") {
                if ($csv != "yes") {
                    if ($cur[19] != 0 && in_array($cur[1], $nwsDate)) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='F5'><font face='Verdana' size='1' color='pink'>" . $main_cur[11] . "</font></a></td>";
                    } else {
                        print "<td bgcolor='" . $bgcolor . "'><a title='F5'><font face='Verdana' size='1' color='" . $font . "'>" . $main_cur[11] . "</font></a></td>";
                    }
                } else {
                    print $main_cur[11] . ";";
                }
            }
            if (strpos($txtRosterColumns, "chkShift") !== false) {
                if ($lstDayNight == "Yes") {
                    if (170000 < $cur[3] && $cur[6] < 170000) {
                        if ($csv != "yes") {
                            if ($cur[19] != 0 && in_array($cur[1], $nwsDate)) {
                                print "<td bgcolor='" . $bgcolor . "'><a title='Shift'><font face='Verdana' size='1' color='pink'>Night</font></a></td>";
                            } else {
                                print "<td bgcolor='" . $bgcolor . "'><a title='Shift'><font face='Verdana' size='1' color='" . $font . "'>Night</font></a></td>";
                            }
                        } else {
                            print "Night;";
                        }
                    } else {
                        if ($csv != "yes") {
                            if ($cur[19] != 0 && in_array($cur[1], $nwsDate)) {
                                print "<td bgcolor='" . $bgcolor . "'><a title='Shift'><font face='Verdana' size='1' color='pink'>Day</font></a></td>";
                            } else {
                                print "<td bgcolor='" . $bgcolor . "'><a title='Shift'><font face='Verdana' size='1' color='" . $font . "'>Day</font></a></td>";
                            }
                        } else {
                            print "Day;";
                        }
                    }
                } else {
                    if ($csv != "yes") {
                        if ($cur[19] != 0 && in_array($cur[1], $nwsDate)) {
                            print "<td bgcolor='" . $bgcolor . "'><a title='Shift'><font face='Verdana' size='1' color='pink'>" . $cur[0] . "</font></a></td>";
                        } else {
                            print "<td bgcolor='" . $bgcolor . "'><a title='Shift'><font face='Verdana' size='1' color='" . $font . "'>" . $cur[0] . "</font></a></td>";
                        }
                    } else {
                        print $cur[0] . ";";
                    }
                }
            }
            if (strpos($txtRosterColumns, "chkOT1") !== false) {
                if ($csv != "yes") {
                    if ($cur[19] != 0 && in_array($cur[1], $nwsDate)) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Special OT Day 1'><font face='Verdana' size='1' color='pink'>" . $cur[21] . "</font></a></td>";
                    } else {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Special OT Day 1'><font face='Verdana' size='1' color='" . $font . "'>" . $cur[21] . "</font></a></td>";
                    }
                } else {
                    print $cur[21] . ";";
                }
            }
            if (strpos($txtRosterColumns, "chkOT2") !== false) {
                if ($csv != "yes") {
                    if ($cur[19] != 0 && in_array($cur[1], $nwsDate)) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Special OT Day 2'><font face='Verdana' size='1' color='pink'>" . $cur[22] . "</font></a></td>";
                    } else {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Special OT Day 2'><font face='Verdana' size='1' color='" . $font . "'>" . $cur[22] . "</font></a></td>";
                    }
                } else {
                    print $cur[22] . ";";
                }
            }
            if ($csv != "yes") {
                displayDate($cur[1]);
                if ($cur[19] != 0 && in_array($cur[1], $nwsDate)) {
                    print "<td bgcolor='" . $bgcolor . "'><a title='Date'><font face='Verdana' size='1' color='pink'>" . displayDate($cur[1]) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Day'><font face='Verdana' size='1' color='pink'>" . $cur[9] . "</font></a></td><td bgcolor='" . $bgcolor . "'><a title='Week'><font face='Verdana' size='1' color='pink'>" . $cur[28] . "</font></a></td>";
                } else {
                    print "<td bgcolor='" . $bgcolor . "'><a title='Date'><font face='Verdana' size='1' color='" . $font . "'>" . displayDate($cur[1]) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Day'><font face='Verdana' size='1' color='" . $font . "'>" . $cur[9] . "</font></a></td><td bgcolor='" . $bgcolor . "'><a title='Week'><font face='Verdana' size='1' color='" . $font . "'>" . $cur[28] . "</font></a></td>";
                }
            } else {
                displayDate($cur[1]);
                print displayDate($cur[1]) . ";" . $cur[9] . ";";
            }
            if (strpos($txtRosterColumns, "chkFlag") !== false) {
                if (insertToday() < 20150331 && $cur[8] != "Black" && $cur[8] != "Proxy") {
                    $flag_title_query = "SELECT Title FROM FlagTitle WHERE Flag = '" . $cur[8] . "'";
                    $flag_title_result = selectData($conn, $flag_title_query);
                    if ($csv != "yes") {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Flag'><font face='Verdana' size='1' color='" . $font . "'>" . $flag_title_result[0] . "</font></a></td>";
                    } else {
                        print $flag_title_result[0] . ";";
                    }
                } else {
                    if ($csv != "yes") {
                        if ($cur[8] == 'Black') {
                            $ncolor = 'Normal';
                        } else {
                            $ncolor = $cur[8];
                        }
                        if ($cur[19] != 0 && in_array($cur[1], $nwsDate)) {
                            print "<td bgcolor='" . $bgcolor . "'><a title='Flag'><font face='Verdana' size='1' color='pink'>" . $ncolor . "</font></a></td>";
                        } else {
                            print "<td bgcolor='" . $bgcolor . "'><a title='Flag'><font face='Verdana' size='1' color='" . $font . "'>" . $ncolor . "</font></a></td>";
                        }
                    } else {
                        print $cur[8] . ";";
                    }
                }
            }
            if (strpos($txtRosterColumns, "chkEntry") !== false) {
                if ($cur[2] != $cur[3]) {
                    if ($csv != "yes") {
                        displayVirdiTime($cur[2]);
                        print "<td bgcolor='" . $bgcolor . "'><a title='Entry'><font face='Verdana' size='1' color='" . $font . "'>" . displayVirdiTime($cur[2]) . "</font></a></td>";
                    } else {
                        displayVirdiTime($cur[2]);
                        print displayVirdiTime($cur[2]) . ";";
                    }
                } else {
                    if ($csv != "yes") {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Entry'><font face='Verdana' size='1' color='" . $font . "'> </font></a></td>";
                    } else {
                        print ";";
                    }
                }
            }
            if ($lstTerminal == "Yes") {
                $terminal_query = "SELECT tgate.name FROM tgate, " . $_table_name . " WHERE tgate.id = " . $_table_name . ".g_id AND " . $_table_name . ".e_id = '" . $main_cur[0] . "' AND " . $_table_name . ".e_date = '" . $cur[1] . "' AND " . $_table_name . ".e_time = '" . $cur[3] . "' ";
                $terminal_result = selectData($conn, $terminal_query);
                if ($csv != "yes") {
                    if ($cur[19] != 0 && in_array($cur[1], $nwsDate)) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Start'><font face='Verdana' size='1' color='pink'>" . $terminal_result[0] . "</font></a></td>";
                    } else {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Start'><font face='Verdana' size='1' color='" . $font . "'>" . $terminal_result[0] . "</font></a></td>";
                    }
                } else {
                    print $terminal_result[0] . ";";
                }
            }
            if (strpos($txtRosterColumns, "chkStart") !== false) {
                if ($csv != "yes") {
                    displayVirdiTime($cur[3]);
                    if ($cur[19] != 0 && in_array($cur[1], $nwsDate)) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Terminal'><font face='Verdana' size='1' color='pink'><b>" . displayVirdiTime($cur[3]) . "</b></font></a></td>";
                    } else {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Terminal'><font face='Verdana' size='1' color='" . $font . "'><b>" . displayVirdiTime($cur[3]) . "</b></font></a></td>";
                    }
                } else {
                    displayVirdiTime($cur[3]);
                    print displayVirdiTime($cur[3]) . ";";
                }
            }
            if (strpos($txtRosterColumns, "chkBreakOut") !== false) {
                if ($cur[3] != $cur[4]) {
                    if ($csv != "yes") {
                        displayVirdiTime($cur[4]);
                        print "<td bgcolor='" . $bgcolor . "'><a title='Break Out'><font face='Verdana' size='1' color='" . $font . "'>" . displayVirdiTime($cur[4]) . "</font></a></td>";
                    } else {
                        displayVirdiTime($cur[4]);
                        print displayVirdiTime($cur[4]) . ";";
                    }
                } else {
                    if ($csv != "yes") {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Break Out'><font face='Verdana' size='1' color='" . $font . "'> </font></a></td>";
                    } else {
                        print ";";
                    }
                }
            }
            if (strpos($txtRosterColumns, "chkBreakIn") !== false) {
                if ($cur[3] != $cur[5]) {
                    if ($csv != "yes") {
                        displayVirdiTime($cur[5]);
                        print "<td bgcolor='" . $bgcolor . "'><a title='Break In'><font face='Verdana' size='1' color='" . $font . "'>" . displayVirdiTime($cur[5]) . "</font></a></td>";
                    } else {
                        displayVirdiTime($cur[5]);
                        print displayVirdiTime($cur[5]) . ";";
                    }
                } else {
                    if ($csv != "yes") {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Break In'><font face='Verdana' size='1' color='" . $font . "'> </font></a></td>";
                    } else {
                        print ";";
                    }
                }
            }
            if (strpos($txtRosterColumns, "chkClose") !== false) {
                if ($csv != "yes") {
                    displayVirdiTime($cur[6]);
                    if ($cur[19] != 0 && in_array($cur[1], $nwsDate)) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Close'><font face='Verdana' size='1' color='pink'><b>" . displayVirdiTime($cur[6]) . "</b></font></a></td>";
                    } else {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Close'><font face='Verdana' size='1' color='" . $font . "'><b>" . displayVirdiTime($cur[6]) . "</b></font></a></td>";
                    }
                } else {
                    displayVirdiTime($cur[6]);
                    print displayVirdiTime($cur[6]) . ";";
                }
            }
            if ($lstTerminal == "Yes") {
                $terminal_query = "SELECT tgate.name FROM tgate, " . $_table_name . " WHERE tgate.id = " . $_table_name . ".g_id AND " . $_table_name . ".e_id = '" . $main_cur[0] . "' AND " . $_table_name . ".e_date = '" . $cur[1] . "' AND " . $_table_name . ".e_time = '" . $cur[6] . "' ";
                $terminal_result = selectData($conn, $terminal_query);
                if ($csv != "yes") {
                    if ($cur[19] != 0 && in_array($cur[1], $nwsDate)) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Terminal'><font face='Verdana' size='1' color='pink'>" . $terminal_result[0] . "</font></a></td>";
                    } else {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Terminal'><font face='Verdana' size='1' color='" . $font . "'>" . $terminal_result[0] . "</font></a></td>";
                    }
                } else {
                    print $terminal_result[0] . ";";
                }
            }
            if (strpos($txtRosterColumns, "chkExit") !== false) {
                if ($cur[6] != $cur[7]) {
                    if ($csv != "yes") {
                        displayVirdiTime($cur[7]);
                        print "<td bgcolor='" . $bgcolor . "'><a title='Exit'><font face='Verdana' size='1' color='" . $font . "'>" . displayVirdiTime($cur[7]) . "</font></a></td>";
                    } else {
                        displayVirdiTime($cur[7]);
                        print displayVirdiTime($cur[7]) . ";";
                    }
                } else {
                    if ($csv != "yes") {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Exit'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
                    } else {
                        print ";";
                    }
                }
            }
            if (strpos($txtRosterColumns, "chkEarlyIn") !== false) {
                if ($csv != "yes") {
                    round($cur[10] / 60, 2);
                    if ($cur[19] != 0 && in_array($cur[1], $nwsDate)) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Early In'><font face='Verdana' size='1' color='pink'>" . round($cur[10] / 60, 2) . "</font></td>";
                    } else {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Early In'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[10] / 60, 2) . "</font></td>";
                    }
                } else {
                    round($cur[10] / 60, 2);
                    print round($cur[10] / 60, 2) . ";";
                }
            }
            if (strpos($txtRosterColumns, "chkLateIn") !== false) {
                if ($cur[23] == 0) {
                    if ($weirdTimeDisplay) {
                        if ($csv != "yes") {
                            getWeirdTime($cur[11]);
                            print "<td bgcolor='" . $bgcolor . "'><a title='Late In'><font face='Verdana' size='1' color='" . $font . "'>" . getWeirdTime($cur[11]) . "</font></td>";
                        } else {
                            getWeirdTime($cur[11]);
                            print getWeirdTime($cur[11]) . ";";
                        }
                    } else {
                        if ($csv != "yes") {
                            round($cur[11] / 60, 2);
                            if ($cur[19] != 0 && in_array($cur[1], $nwsDate)) {
                                print "<td bgcolor='" . $bgcolor . "'><a title='Late In'><font face='Verdana' size='1' color='pink'>" . round($cur[11] / 60, 2) . "</font></td>";
                            } else {
                                print "<td bgcolor='" . $bgcolor . "'><a title='Late In'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[11] / 60, 2) . "</font></td>";
                            }
                        } else {
                            print "0;";
                        }
                    }
                } else {
                    if ($weirdTimeDisplay) {
                        if ($csv != "yes") {
                            getWeirdTime($cur[11]);
                            print "<td bgcolor='" . $bgcolor . "'><a title='Late In'><font face='Verdana' size='1' color='" . $font . "'><strike>" . getWeirdTime($cur[11]) . "</strike></font></td>";
                        } else {
                            getWeirdTime($cur[11]);
                            print getWeirdTime($cur[11]) . ";";
                        }
                    } else {
                        if ($csv != "yes") {
                            round($cur[11] / 60, 2);
                            print "<td bgcolor='" . $bgcolor . "'><a title='Late In'><font face='Verdana' size='1' color='" . $font . "'><strike>" . round($cur[11] / 60, 2) . "</strike></font></td>";
                        } else {
                            print "0;";
                        }
                    }
                }
                $tc_li = $tc_li + $cur[11];
            }
            if ($csv != "yes") {
                round($cur[12] / 60, 2);
                if ($cur[19] != 0 && in_array($cur[1], $nwsDate)) {
                    print "<td bgcolor='" . $bgcolor . "'><a title='Break (Min)'><font face='Verdana' size='1' color='pink'>" . round($cur[12] / 60, 2) . "</font></td>";
                } else {
                    print "<td bgcolor='" . $bgcolor . "'><a title='Break (Min)'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[12] / 60, 2) . "</font></td>";
                }
            } else {
                round($cur[12] / 60, 2);
                print round($cur[12] / 60, 2) . ";";
            }
            if (strpos($txtRosterColumns, "chkLessBreak") !== false) {
                if ($csv != "yes") {
                    round($cur[13] / 60, 2);
                    if ($cur[19] != 0 && in_array($cur[1], $nwsDate)) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Less Break'><font face='Verdana' size='1' color='pink'>" . round($cur[13] / 60, 2) . "</font></td>";
                    } else {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Less Break'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[13] / 60, 2) . "</font></td>";
                    }
                } else {
                    round($cur[13] / 60, 2);
                    print round($cur[13] / 60, 2) . ";";
                }
            }
            if (strpos($txtRosterColumns, "chkMoreBreak") !== false) {
                if ($cur[25] == 0) {
                    if ($csv != "yes") {
                        round($cur[14] / 60, 2);
                        if ($cur[19] != 0 && in_array($cur[1], $nwsDate)) {
                            print "<td bgcolor='" . $bgcolor . "'><a title='More Break'><font face='Verdana' size='1' color='pink'>" . round($cur[14] / 60, 2) . "</font></td>";
                        } else {
                            print "<td bgcolor='" . $bgcolor . "'><a title='More Break'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[14] / 60, 2) . "</font></td>";
                        }
                    } else {
                        round($cur[14] / 60, 2);
                        print round($cur[14] / 60, 2) . ";";
                    }
                } else {
                    if ($csv != "yes") {
                        round($cur[14] / 60, 2);
                        if ($cur[19] != 0 && in_array($cur[1], $nwsDate)) {
                            print "<td bgcolor='" . $bgcolor . "'><a title='More Break'><font face='Verdana' size='1' color='pink'><strike>" . round($cur[14] / 60, 2) . "</strike></font></td>";
                        } else {
                            print "<td bgcolor='" . $bgcolor . "'><a title='More Break'><font face='Verdana' size='1' color='" . $font . "'><strike>" . round($cur[14] / 60, 2) . "</strike></font></td>";
                        }
                    } else {
                        print "0;";
                    }
                }
            }
            if (strpos($txtRosterColumns, "chkEarlyOut") !== false) {
                if ($cur[24] == 0) {
                    if ($weirdTimeDisplay) {
                        if ($csv != "yes") {
                            getWeirdTime($cur[15]);
                            if ($cur[19] != 0 && in_array($cur[1], $nwsDate)) {
                                print "<td bgcolor='" . $bgcolor . "'><a title='Early Out'><font face='Verdana' size='1' color='pink'>" . getWeirdTime($cur[15]) . "</font></td>";
                            } else {
                                print "<td bgcolor='" . $bgcolor . "'><a title='Early Out'><font face='Verdana' size='1' color='" . $font . "'>" . getWeirdTime($cur[15]) . "</font></td>";
                            }
                        } else {
                            getWeirdTime($cur[15]);
                            print getWeirdTime($cur[15]) . ";";
                        }
                    } else {
                        if ($csv != "yes") {
                            round($cur[15] / 60, 2);
                            if ($cur[19] != 0 && in_array($cur[1], $nwsDate)) {
                                print "<td bgcolor='" . $bgcolor . "'><a title='Early Out'><font face='Verdana' size='1' color='pink'>" . round($cur[15] / 60, 2) . "</font></td>";
                            } else {
                                print "<td bgcolor='" . $bgcolor . "'><a title='Early Out'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[15] / 60, 2) . "</font></td>";
                            }
                        } else {
                            round($cur[15] / 60, 2);
                            print round($cur[15] / 60, 2) . ";";
                        }
                    }
                } else {
                    if ($weirdTimeDisplay) {
                        if ($csv != "yes") {
                            getWeirdTime($cur[15]);
                            if ($cur[19] != 0 && in_array($cur[1], $nwsDate)) {
                                print "<td bgcolor='" . $bgcolor . "'><a title='Early Out'><font face='Verdana' size='1' color='pink'><strike>" . getWeirdTime($cur[15]) . "</strike></font></td>";
                            } else {
                                print "<td bgcolor='" . $bgcolor . "'><a title='Early Out'><font face='Verdana' size='1' color='" . $font . "'><strike>" . getWeirdTime($cur[15]) . "</strike></font></td>";
                            }
                        } else {
                            print "0;";
                        }
                    } else {
                        if ($csv != "yes") {
                            round($cur[15] / 60, 2);
                            if ($cur[19] != 0 && in_array($cur[1], $nwsDate)) {
                                print "<td bgcolor='" . $bgcolor . "'><a title='Early Out'><font face='Verdana' size='1' color='pink'><strike>" . round($cur[15] / 60, 2) . "</strike></font></td>";
                            } else {
                                print "<td bgcolor='" . $bgcolor . "'><a title='Early Out'><font face='Verdana' size='1' color='" . $font . "'><strike>" . round($cur[15] / 60, 2) . "</strike></font></td>";
                            }
                        } else {
                            print "0;";
                        }
                    }
                }
                $tc_eo = $tc_eo + $cur[15];
            }
            if (strpos($txtRosterColumns, "chkLateOut") !== false) {
                if ($weirdTimeDisplay) {
                    if ($csv != "yes") {
                        getWeirdTime($cur[16]);
                        if ($cur[19] != 0 && in_array($cur[1], $nwsDate)) {
                            print "<td bgcolor='" . $bgcolor . "'><a title='Late Out'><font face='Verdana' size='1' color='pink'>" . getWeirdTime($cur[16]) . "</font></td>";
                        } else {
                            print "<td bgcolor='" . $bgcolor . "'><a title='Late Out'><font face='Verdana' size='1' color='" . $font . "'>" . getWeirdTime($cur[16]) . "</font></td>";
                        }
                    } else {
                        getWeirdTime($cur[16]);
                        print getWeirdTime($cur[16]) . "\n";
                    }
                } else {
                    if ($csv != "yes") {
                        round($cur[16] / 60, 2);
                        if ($cur[19] != 0 && in_array($cur[1], $nwsDate)) {
                            print "<td bgcolor='" . $bgcolor . "'><a title='Late Out'><font face='Verdana' size='1' color='pink'>" . round($cur[16] / 60, 2) . "</font></td>";
                        } else {
                            print "<td bgcolor='" . $bgcolor . "'><a title='Late Out'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[16] / 60, 2) . "</font></td>";
                        }
                    } else {
                        round($cur[16] / 60, 2);
                        print round($cur[16] / 60, 2) . ";";
                    }
                }
            }
            if (strpos($txtRosterColumns, "chkGrace") !== false) {
                if ($csv != "yes") {
                    round($cur[18] / 60, 2);
                    if ($cur[19] != 0 && in_array($cur[1], $nwsDate)) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Grace'><font face='Verdana' size='1' color='pink'>" . round($cur[18] / 60, 2) . "</font></td>";
                    } else {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Grace'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[18] / 60, 2) . "</font></td>";
                    }
                } else {
                    round($cur[18] / 60, 2);
                    print round($cur[18] / 60, 2) . ";";
                }
            }
            if (strpos($txtRosterColumns, "chkNormal") !== false) {
                if ($csv != "yes") {
                    round($cur[17] / 3600, 2);
                    if ($cur[19] != 0 && in_array($cur[1], $nwsDate)) {
                        print "<td bgcolor='" . $bgcolor . "'><a title='Normal'><font face='Verdana' size='1' color='pink'>" . round($cur[17] / 3600, 2) . "</font></td>";
                    } else { 
                        $tuserQuery = 'SELECT OT1,OT2 FROM tuser';
                        $otData = selectData($conn, $tuserQuery);

                        if(in_array($cur[9], $otData)){
                            $otHours = 0;
                        }else{
                            $otHours = round($cur[17] / 3600, 2);
                        }
                        
                        print "<td bgcolor='" . $bgcolor . "'><a title='Normal'><font face='Verdana' size='1' color='" . $font . "'>" . $otHours . "</font></td>";
                    }
                } else {
                    round($cur[17] / 3600, 2);
                    print round($cur[17] / 3600, 2) . ";";
                }
                $tc_n = $tc_n + $cur[17];
            }
//            echo "<pre>";print_R($cur);
            if (strpos($txtRosterColumns, "chkOT") !== false) {
                if ($weirdTimeDisplay) {
                    if ($csv != "yes") {
                        getWeirdTime($cur[19]);
                        if ($cur[19] != 0 && in_array($cur[1], $nwsDate)) {
                            print "<td bgcolor='" . $bgcolor . "'><a title='OT'><font face='Verdana' size='1' color='pink'>" . getWeirdTime($cur[19]) . "</font></td>";
                        } else {
                            print "<td bgcolor='" . $bgcolor . "'><a title='OT'><font face='Verdana' size='1' color='" . $font . "'>" . getWeirdTime($cur[19]) . "</font></td>";
                        }
                    } else {
                        getWeirdTime($cur[19]);
                        print getWeirdTime($cur[19]) . ";";
                    }
                } else {
                    if ($csv != "yes") {
                        round($cur[19] / 3600, 2);
                        if ($cur[19] != 0 && in_array($cur[1], $nwsDate)) {
                            print "<td bgcolor='" . $bgcolor . "'><a title='OT'><font face='Verdana' size='1' color='pink'>" . round($cur[19] / 3600, 2) . "</font></td>";
                        } else {
                            print "<td bgcolor='" . $bgcolor . "'><a title='OT'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[19] / 3600, 2) . "</font></td>";
                        }
                    } else {
                        round($cur[19] / 3600, 2);
                        print round($cur[19] / 3600, 2) . ";";
                    }
                }
                $tc_ot = $tc_ot + $cur[19];
            }
            if (strpos($txtRosterColumns, "chkAppOT") !== false) {
                if ($weirdTimeDisplay) {
                    if ($csv != "yes") {
                        getWeirdTime($cur[20]);
                        if ($cur[19] != 0 && in_array($cur[1], $nwsDate)) {
                            print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='pink'>" . getWeirdTime($cur[20]) . "</font></td>";
                        } else {
                            print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'>" . getWeirdTime($cur[20]) . "</font></td>";
                        }
                        if (getRegister($txtMACAddress, 7) == "25") {
                            getWeirdTime($cur[26]);
                            print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'>" . getWeirdTime($cur[26]) . "</font></td>";
                            getWeirdTime($cur[20] + $cur[26]);
                            print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'>" . getWeirdTime($cur[20] + $cur[26]) . "</font></td>";
                        }
                    } else {
                        getWeirdTime($cur[20]);
                        print getWeirdTime($cur[20]) . ";";
                        if (getRegister($txtMACAddress, 7) == "25") {
                            getWeirdTime($cur[26]);
                            print getWeirdTime($cur[26]) . ";";
                            getWeirdTime($cur[20] + $cur[26]);
                            print getWeirdTime($cur[20] + $cur[26]) . ";";
                        }
                    }
                } else {
                    if ($csv != "yes") {
                        round($cur[20] / 3600, 2);
                        if ($cur[19] != 0 && in_array($cur[1], $nwsDate)) {
                            print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='pink'>" . round($cur[20] / 3600, 2) . "</font></td>";
                        } else {
                            
                            $otcuttQuery = "SELECT id,name,EmpClose from tgroup where name='$cur[0]'";
                            $otcuttResult = mysqli_query($conn, $otcuttQuery);
                            $otcuttRow = mysqli_fetch_assoc($otcuttResult);
//                            echo "<pre>";
//                            print_R($otcuttRow);
//                            if($otcuttRow['name'] == $cur[0] && $otcuttRow['EmpClose'] != NULL){
//                            echo "<pre>";
//                                        print_R($cur);
                                        //round($cur[17] / 3600, 2)
                            if (round($cur[17] / 3600, 2) >= 8 || $cur[9] == 'Saturday') {
                                if ($otcuttRow['name'] == $cur[0] && $otcuttRow['EmpClose'] != NULL) { 
                                    // $cur[6] = 'Closetime'
                                    // $cur[19] = 'Overtime'
                                    // $cur[17] = 'Normal Hours'
                                    if (strtotime($otcuttRow['EmpClose'] . '00') < strtotime($cur[6])) {
//                                        echo "<pre>";
//                                        print_R($cur);
//                                        echo strtotime($otcuttRow['EmpClose'].'00');
//                                    echo "Hey".$otCutoff =  strtotime($otcuttRow['EmpClose'].'00') - strtotime(displayVirdiTime($cur[3]))."<br>";
                                        $otCutoff = strtotime(displayVirdiTime($cur[6])) - strtotime(displayVirdiTime($otcuttRow['EmpClose'] . '00')) . "<br>";
                                        $aotcutofftime = round($cur[19] / 3600, 2) - round($otCutoff / 3600, 2);
//                                    echo "Hey"."<br>";
                                    } else {
                                        $aotcutofftime = round($cur[19] / 3600, 2);
//                                    echo "Here"."<br>";
                                    }
                                }else{
                                    $aotcutofftime = round($cur[19] / 3600, 2);
                                }
                            } else {
                                $aotcutofftime = 0;
                            }
                            
//                            print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[20] / 3600, 2) . "</font></td>";
                            print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'>" . $aotcutofftime . "</font></td>";
                        }

                        if (getRegister($txtMACAddress, 7) == "25") {
                            round($cur[26] / 60, 2);
                            print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[26] / 60, 2) . "</font></td>";
                            round(($cur[20] + $cur[26]) / 3600, 2);
                            print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'>" . round(($cur[20] + $cur[26]) / 3600, 2) . "</font></td>";
                        }
                    } else {
                        round($cur[20] / 3600, 2);
                        print round($cur[20] / 3600, 2) . ";";
                        if (getRegister($txtMACAddress, 7) == "25") {
                            round($cur[26] / 3600, 2);
                            print round($cur[26] / 3600, 2) . ";";
                            round(($cur[20] + $cur[26]) / 3600, 2);
                            print round(($cur[20] + $cur[26]) / 3600, 2) . ";";
                        }
                    }
                }
                if (getRegister($txtMACAddress, 7) == "25") {
                    $tc_aot = $tc_aot + $cur[20] + $cur[26];
                } else {
                    $tc_aot = $tc_aot + $cur[20];
                }
            }
            if (strpos($txtRosterColumns, "chkTH") !== false) {
                if ($weirdTimeDisplay) {
                    if ($csv != "yes") {
                        getWeirdTime($cur[20] + $cur[17]);
                        if ($cur[19] != 0 && in_array($cur[1], $nwsDate)) {
                            print "<td bgcolor='" . $bgcolor . "'><a title='Total Hrs'><font face='Verdana' size='1' color='pink'>" . getWeirdTime($cur[20] + $cur[17]) . "</font></td>";
                        } else {
                            print "<td bgcolor='" . $bgcolor . "'><a title='Total Hrs'><font face='Verdana' size='1' color='" . $font . "'>" . getWeirdTime($cur[20] + $cur[17]) . "</font></td>";
                        }
                    } else {
                        getWeirdTime($cur[20] + $cur[17]);
                        print getWeirdTime($cur[20] + $cur[17]) . ";";
                    }
                } else {
                    if ($csv != "yes") {
                        round($cur[17] / 3600 + $cur[20] / 3600, 2);
                        if ($cur[19] != 0 && in_array($cur[1], $nwsDate)) {
                            print "<td bgcolor='" . $bgcolor . "'><a title='Total Hrs'><font face='Verdana' size='1' color='pink'>" . round($cur[17] / 3600 + $cur[20] / 3600, 2) . "</font></td>";
                        } else {
                            print "<td bgcolor='" . $bgcolor . "'><a title='Total Hrs'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[17] / 3600 + $cur[20] / 3600, 2) . "</font></td>";
                        }
                    } else {
                        round($cur[17] / 3600 + $cur[20] / 3600, 2);
                        print round($cur[17] / 3600 + $cur[20] / 3600, 2) . ";";
                    }
                }
                $tc_t = $tc_t + $cur[20] + $cur[17];
            }
            if ($txtMACAddress == "40-A8-F0-23-F0-AD") {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='1'>P</font></td>";
                } else {
                    print "P;";
                }
            }
            if ($lstRemark != "" && $prints == "yes") {
                if ($csv != "yes") {
                    print "<td width=50><font face='Verdana' size='2'>&nbsp;</font></td>";
                } else {
                    print ";";
                }
            }
            if ($csv != "yes") {
                print "</tr>";
            } else {
                print "\n";
            }
            $count++;
            $last_id = $main_cur[0];
            $last_name = $main_cur[1];
            $last_dept = $main_cur[2];
            $last_div = $main_cur[3];
            $last_idno = $main_cur[5];
            $last_rmk = $main_cur[6];
            $last_date = $cur[1];
            $last_shift = $cur[0];
            $nextDate = getNextDay($cur[1], 1);
        }
    }
        $shift = " ";
        if ($lstAbsentShift == "Yes") {
            $shift = $last_shift;
        }
        if ($lstDayNight == "Yes") {
            $shift = "Absent";
        }
        if ($sub_count == 0) { 
            $count = displayAlterTime($conn, insertDate($txtFrom), insertDate($txtTo), $main_cur[0], $main_cur[1], $main_cur[2], $main_cur[3], $main_cur[5], $main_cur[6], $main_cur[7], $main_cur[8], $main_cur[9], $main_cur[10], $main_cur[11], $column_count_1, $column_count_2, $txtRosterColumns, $prints, $count, $lstImproperClocking, $lstAbsent, $shift, $txtMACAddress, $timecard, $lstTerminal, $lstDB, $csv, $cur[28] ?? "");
        } else { 
            if ($last_date < insertDate($txtTo)) {
                $count = displayAlterTime($conn, $nextDate, insertDate($txtTo), $main_cur[0], $main_cur[1], $main_cur[2], $main_cur[3], $main_cur[5], $main_cur[6], $main_cur[7], $main_cur[8], $main_cur[9], $main_cur[10], $main_cur[11], $column_count_1, $column_count_2, $txtRosterColumns, $prints, $count, $lstImproperClocking, $lstAbsent, $shift, $txtMACAddress, $timecard, $lstTerminal, $lstDB, $csv, $cur[28] ?? "");
            }
        }
    }
    if ($timecard == "yes") {
        print "<tr>";
        if (strpos($txtRosterColumns, "chkShift") !== false) {
            print "<td bgcolor='" . $bgcolor . "'><a title='Shift'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
        }
        if (strpos($txtRosterColumns, "chkOT1") !== false) {
            print "<td bgcolor='" . $bgcolor . "'><a title='Special OT Day 1'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
        }
        if (strpos($txtRosterColumns, "chkOT2") !== false) {
            print "<td bgcolor='" . $bgcolor . "'><a title='Special OT Day 2'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
        }
        print "<td bgcolor='" . $bgcolor . "'><a title='Date'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Day'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
        if (strpos($txtRosterColumns, "chkFlag") !== false) {
            print "<td bgcolor='" . $bgcolor . "'><a title='Flag'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
        }
        if (strpos($txtRosterColumns, "chkEntry") !== false) {
            print "<td bgcolor='" . $bgcolor . "'><a title='Entry'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
        }
        if (strpos($txtRosterColumns, "chkStart") !== false) {
            print "<td bgcolor='" . $bgcolor . "'><a title='Start'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
        }
        if (strpos($txtRosterColumns, "chkBreakOut") !== false) {
            print "<td bgcolor='" . $bgcolor . "'><a title='Break Out'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
        }
        if (strpos($txtRosterColumns, "chkBreakIn") !== false) {
            print "<td bgcolor='" . $bgcolor . "'><a title='Break In'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
        }
        if (strpos($txtRosterColumns, "chkClose") !== false) {
            print "<td bgcolor='" . $bgcolor . "'><a title='Close'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
        }
        if (strpos($txtRosterColumns, "chkExit") !== false) {
            print "<td bgcolor='" . $bgcolor . "'><a title='Exit'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
        }
        if (strpos($txtRosterColumns, "chkEarlyIn") !== false) {
            print "<td bgcolor='" . $bgcolor . "'><a title='Early In'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></td>";
        }
        if (strpos($txtRosterColumns, "chkLateIn") !== false) {
            if ($weirdTimeDisplay) {
                getWeirdTime($tc_li);
                print "<td bgcolor='" . $bgcolor . "'><a title='Late In'><font face='Verdana' size='1' color='" . $font . "'><b>" . getWeirdTime($tc_li) . "</b></font></td>";
            } else {
                round($tc_li / 60, 2);
                print "<td bgcolor='" . $bgcolor . "'><a title='Late In'><font face='Verdana' size='1' color='" . $font . "'><b>" . round($tc_li / 60, 2) . "</b></font></td>";
            }
        }

        if (strpos($txtRosterColumns, "chkLessBreak") !== false) {
            print "<td bgcolor='" . $bgcolor . "'><a title='Less Break'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></td>";
        }
        if (strpos($txtRosterColumns, "chkMoreBreak") !== false) {
            print "<td bgcolor='" . $bgcolor . "'><a title='More Break'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></td>";
        }
        if (strpos($txtRosterColumns, "chkEarlyOut") !== false) {
            if ($weirdTimeDisplay) {
                getWeirdTime($tc_eo);
                print "<td bgcolor='" . $bgcolor . "'><a title='Early Out'><font face='Verdana' size='1' color='" . $font . "'><b>" . getWeirdTime($tc_eo) . "</b></font></td>";
            } else {
                round($tc_eo / 60, 2);
                print "<td bgcolor='" . $bgcolor . "'><a title='Early Out'><font face='Verdana' size='1' color='" . $font . "'><b>" . round($tc_eo / 60, 2) . "</b></font></td>";
            }
        }
        if (strpos($txtRosterColumns, "chkLateOut") !== false) {
            print "<td bgcolor='" . $bgcolor . "'><a title='Late Out'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></td>";
        }
        if (strpos($txtRosterColumns, "chkGrace") !== false) {
            print "<td bgcolor='" . $bgcolor . "'><a title='Grace'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></td>";
        }
        if (strpos($txtRosterColumns, "chkNormal") !== false) {
            round($tc_n / 3600, 2);
            print "<td bgcolor='" . $bgcolor . "'><a title='Normal'><font face='Verdana' size='1' color='" . $font . "'><b>" . round($tc_n / 3600, 2) . "</b></font></td>";
        }
        if (strpos($txtRosterColumns, "chkOT") !== false) {
            if ($weirdTimeDisplay) {
                getWeirdTime($tc_ot);
                print "<td bgcolor='" . $bgcolor . "'><a title='OT'><font face='Verdana' size='1' color='" . $font . "'><b>" . getWeirdTime($tc_ot) . "</b></font></td>";
            } else {
                round($tc_ot / 3600, 2);
                print "<td bgcolor='" . $bgcolor . "'><a title='OT'><font face='Verdana' size='1' color='" . $font . "'><b>" . round($tc_ot / 3600, 2) . "</b></font></td>";
            }
        }
        if (strpos($txtRosterColumns, "chkAppOT") !== false) {
            if ($weirdTimeDisplay) {
                if (getRegister($txtMACAddress, 7) == "25") {
                    print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'><b>&nbsp;</b></font></td>";
                    print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'><b>&nbsp;</b></font></td>";
                    getWeirdTime($tc_aot);
                    print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'><b>" . getWeirdTime($tc_aot) . "</b></font></td>";
                } else {
                    getWeirdTime($tc_aot);
                    print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'><b>" . getWeirdTime($tc_aot) . "</b></font></td>";
                }
            } else {
                if (getRegister($txtMACAddress, 7) == "25") {
                    print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'><b>&nbsp;</b></font></td>";
                    print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'><b>&nbsp;</b></font></td>";
                    round($tc_aot / 3600, 2);
                    print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'><b>" . round($tc_aot / 3600, 2) . "</b></font></td>";
                } else {
                    round($tc_aot / 3600, 2);
                    print "<td bgcolor='" . $bgcolor . "'><a title='Approved OT'><font face='Verdana' size='1' color='" . $font . "'><b>" . round($tc_aot / 3600, 2) . "</b></font></td>";
                }
            }
        }
        if (strpos($txtRosterColumns, "chkTH") !== false) {
            if ($weirdTimeDisplay) {
                getWeirdTime($tc_t);
                print "<td bgcolor='" . $bgcolor . "'><a title='Total Hrs'><font face='Verdana' size='1' color='" . $font . "'><b>" . getWeirdTime($tc_t) . "</b></font></td>";
            } else {
                round($tc_t / 3600, 2);
                print "<td bgcolor='" . $bgcolor . "'><a title='Total Hrs'><font face='Verdana' size='1' color='" . $font . "'><b>" . round($tc_t / 3600, 2) . "</b></font></td>";
            }
        }
        if ($txtMACAddress == "40-A8-F0-23-F0-AD") {
            print "<td><font face='Verdana' size='1'>P</font></td>";
        }
        if ($lstRemark != "" && $prints == "yes") {
            print "<td width=50><font face='Verdana' size='2'>&nbsp;</font></td>";
        }
        print "</tr>";
    }
    if ($csv != "yes") {
        print "</table>";
    }
    if ($excel != "yes") {
        print "<p align='center'><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $count . "</b></font>";
        if ($prints != "yes") {
            print "<br><input type='button' value='Print Report' class='btn btn-primary' onClick='checkPrint(0)'>&nbsp;&nbsp;<input type='button' value='Print Time Card' class='btn btn-primary' onClick='checkPrint(2)'>&nbsp;&nbsp;<input type='button' class='btn btn-primary' value='Excel' onClick='checkPrint(1)'>&nbsp;&nbsp;<input type='button' class='btn btn-primary' value='CSV' id='export-btn'>";
        }
        print "</p>";
    }
}
if ($csv != "yes") {
    print "</form>";
}
if ($csv != "yes") {
    print "</center></body></html>";
}

function displayAlterTime($conn, $from, $to, $id, $name, $dept, $div, $idno, $rmk, $f1, $f2, $f3, $f4, $f5, $column_count_1, $column_count_2, $txtRosterColumns, $prints, $count, $lstImproperClocking, $lstAbsent, $shift, $txtMACAddress, $timecard, $lstTerminal, $lstDB, $csv, $week) {
    global $session_variable;
//    $table_name = "Access.tenter";
    $table_name = "tenter";
    if ($lstDB == "Archive") {
        $table_name = "AccessArchive.archive_tenter";
    }
    if ($id != "") { 
        for ($i = $from; $i <= $to; $i++) {
            if (checkdate(substr($i, 4, 2), substr($i, 6, 2), substr($i, 0, 4))) { 
                $start = "";
                $close = "";
                $sgate = "";
                $cgate = "";
                if ($lstImproperClocking == "Yes") {  
                    $alter_query = "SELECT " . $table_name . ".e_time, tgroup.Start, tgroup.Close, tgroup.NightFlag, tgroup.WorkMin, tgroup.name, tgate.name, AttendanceMaster.Week, AttendanceMaster.Break FROM " . $table_name . ", tuser, tgate, tgroup, AttendanceMaster WHERE " . $table_name . ".e_id = tuser.id AND " . $table_name . ".g_id = tgate.id AND " . $table_name . ".e_group = tgroup.id AND tgroup.ShiftTypeID = 1 AND tuser.id = " . $id . " AND tgate.exit = 0 AND " . $table_name . ".p_flag = 0 AND " . $table_name . ".e_date = " . $i;
                    $alter_result = selectData($conn, $alter_query);
//                    echo "<pre>";print_R($alter_result);
                    if (isset($alter_result[0]) && $alter_result[0] != "") {

                        $shift = $alter_result[5];
                        if ($alter_result[3] == 0) {
                            $halfTime = getLateTime($i, $alter_result[1], $alter_result[4] / 2);
                            if ($halfTime < $alter_result[0]) {
                                $close = $alter_result[0];
                                $cgate = $alter_result[6];
                            } else { 
                                $start = $alter_result[0];
                                $sgate = $alter_result[6];
                            }
                        } else {
                            if ($nightShiftMaxOutTime < $alter_result[0]) {
                                $start = $alter_result[0];
                                $sgate = $alter_result[6];
                            } else {
                                $close = $alter_result[0];
                                $cgate = $alter_result[6];
                            }
                        }
                    }
                }

                if ($start == "") {
                    $start = " ";
                } else {
                    if ($prints == "yes") {
                        $start = displayVirdiTime($start);
                    } else {
                        $start = "<a href='AlterTime.php?act=searchRecord&txtEmployeeCode=" . $id . "&txtFrom=" . displayDate($i) . "&txtTo=" . displayDate($i) . "' title='Click here to view the Improper Clockins' target='_blank'><font face='Verdana' color='#000000'>" . displayVirdiTime($start) . "</font></a>";
                    }
                }
                if ($close == "") { 
                    $close = " ";
                } else { 
                    if ($prints == "yes") {
                        $close = displayVirdiTime($close);
                    } else {
                        $close = "<a href='AlterTime.php?act=searchRecord&txtEmployeeCode=" . $id . "&txtFrom=" . displayDate($i) . "&txtTo=" . displayDate($i) . "' title='Click here to view the Improper Clockins' target='_blank'><font face='Verdana' color='#000000'>" . displayVirdiTime($close) . "</font></a>";
                    }
                }

                if (!($lstAbsent == "No" && $start == " " && $close == " ")) {
//                    print " <td><font face='Verdana' size='2'>";
//             if ($timecard != "yes") {
//            if ($csv != "yes") {
//                    print "<input type='checkbox' name='chkAOT' onClick='javascript:approveAll()'>";
//                } else {
//                    print "&nbsp;";
//                }
//             }
//                    if ($timecard != "yes") {
//                        if ($csv != "yes") {
//                            print "<tr><td><font face='Verdana' size='1'><input type='checkbox' name='chkAOT' onClick='javascript:approveAll()'></td>";
//                        } else {
//                            print "&nbsp;";
//                        }
//                    }
                    if ($timecard != "yes") {
                        if ($csv != "yes") {
                            addZero($id, $_SESSION[$session_variable . "EmployeeCodeLength"]);
                            print "<td><a title='ID'><font face='Verdana' size='1'>" . addZero($id, $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $name . "</font></a></td>";
                        } else {
                            addZero($id, $_SESSION[$session_variable . "EmployeeCodeLength"]);
                            print addZero($id, $_SESSION[$session_variable . "EmployeeCodeLength"]) . ";" . $name . ";";
                        }
                    }
                    if (insertToday() < 20150331 && $timecard != "yes") {
                        if ($csv != "yes") {
                            print "<td><a title='TRML'><font face='Verdana' size='1'> </font></a></td>";
                        } else {
                            print ";";
                        }
                    }
                    $column_count_1_minus = 0;
                    if (strpos($txtRosterColumns, "chkIDColumn") !== false && $timecard != "yes") {
                        if ($csv != "yes") {
                            print "<td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $idno . "</font></a></td>";
                        } else {
                            print $idno . ";";
                        }
                        $column_count_1_minus++;
                    }
                    if (strpos($txtRosterColumns, "chkDept") !== false && $timecard != "yes") {
                        if ($csv != "yes") {
                            print "<td><a title='Dept'><font face='Verdana' size='1'>" . $dept . "</font></a></td>";
                        } else {
                            print $dept . ";";
                        }
                        $column_count_1_minus++;
                    }
                    if (strpos($txtRosterColumns, "chkDiv") !== false && $timecard != "yes") {
                        if ($csv != "yes") {
                            print "<td><a title='Div/Desg'><font face='Verdana' size='1'>" . $div . "</font></a></td>";
                        } else {
                            print $div . ";";
                        }
                        $column_count_1_minus++;
                    }
                    if (strpos($txtRosterColumns, "chkRmk") !== false && $timecard != "yes") {
                        if ($csv != "yes") {
                            print "<td><a title='Rmk'><font face='Verdana' size='1'>" . $rmk . "</font></a></td>";
                        } else {
                            print $rmk . ";";
                        }
                        $column_count_1_minus++;
                    }
                    if (strpos($txtRosterColumns, "chkF1") !== false && $timecard != "yes") {
                        if ($csv != "yes") {
                            print "<td><a title='F1'><font face='Verdana' size='1'>" . $f1 . "</font></a></td>";
                        } else {
                            print $f1 . ";";
                        }
                        $column_count_1_minus++;
                    }
                    if (strpos($txtRosterColumns, "chkF2") !== false && $timecard != "yes") {
                        if ($csv != "yes") {
                            print "<td><a title='F2'><font face='Verdana' size='1'>" . $f2 . "</font></a></td>";
                        } else {
                            print $f2 . ";";
                        }
                        $column_count_1_minus++;
                    }
                    if (strpos($txtRosterColumns, "chkF3") !== false && $timecard != "yes") {
                        if ($csv != "yes") {
                            print "<td><a title='F3'><font face='Verdana' size='1'>" . $f3 . "</font></a></td>";
                        } else {
                            print $f3 . ";";
                        }
                        $column_count_1_minus++;
                    }
                    if (strpos($txtRosterColumns, "chkF4") !== false && $timecard != "yes") {
                        if ($csv != "yes") {
                            print "<td><a title='F4'><font face='Verdana' size='1'>" . $f4 . "</font></a></td>";
                        } else {
                            print $f4 . ";";
                        }
                        $column_count_1_minus++;
                    }
                    if (strpos($txtRosterColumns, "chkF5") !== false && $timecard != "yes") {
                        if ($csv != "yes") {
                            print "<td><a title='F5'><font face='Verdana' size='1'>" . $f5 . "</font></a></td>";
                        } else {
                            print $f5 . ";";
                        }
                        $column_count_1_minus++;
                    }
                    if (strpos($txtRosterColumns, "chkShift") !== false) {
                        if ($csv != "yes") {
                            print "<td><a title='Shift'><font face='Verdana' size='1'>" . $shift . "</font></a></td>";
                        } else {
                            print $shift . ";";
                        }
                        $column_count_1_minus++;
                    }
                    for ($j = 0; $j < $column_count_1 - $column_count_1_minus; $j++) {
                        if ($csv != "yes") {
                            print "<td><font face='Verdana' size='1'> </font></td>";
                        } else {
                            print ";";
                        }
                    }
                    $this_day = getDate(strtotime(substr($i, 6, 2) . "-" . substr($i, 4, 2) . "-" . substr($i, 0, 4)));
                    if ($csv != "yes") {
                        displayDate($i);
                        print "<td><a title='Date'><font face='Verdana' size='1'>" . displayDate($i) . "</font></a></td> <td><a title='Day'><font face='Verdana' size='1'>" . $this_day["weekday"] . "</font></a><td><a title='Week'><font face='Verdana' size='1'>" . $week . "</font></a></td>";
                    } else {
                        displayDate($i);
                        print displayDate($i) . ";" . $this_day["weekday"] . ";" . $this_day["weekday"] . ";";
                    }
                    $column_count_2_minus = 0;
                    if (strpos($txtRosterColumns, "chkFlag") !== false) {
                        if ($csv != "yes") {
                            print "<td><font face='Verdana' size='1'>Absent</font></td>";
                        } else {
                            print ";";
                        }
                        $column_count_2_minus++;
                    }
                    if (strpos($txtRosterColumns, "chkEntry") !== false) {
                        if ($csv != "yes") {
                            print "<td><font face='Verdana' size='2'> </font></td>";
                        } else {
                            print ";";
                        }
                        $column_count_2_minus++;
                    }
                    if (strpos($txtRosterColumns, "chkStart") !== false) {
                        if ($lstTerminal == "Yes") {
                            if ($csv != "yes") {
                                print "<td><font face='Verdana' size='1'>" . $sgate . "</font></td>";
                            } else {
                                print $sgate . ";";
                            }
                        }
                        if ($csv != "yes") {
                            print "<td><font face='Verdana' size='1'><b>" . $start . "</b></font></td>";
                        } else {
                            print $start . ";";
                        }
                        $column_count_2_minus++;
                    }
                    if (strpos($txtRosterColumns, "chkBreakOut") !== false) {
                        if ($csv != "yes") {
                            print "<td><font face='Verdana' size='2'> </font></td>";
                        } else {
                            print ";";
                        }
                        $column_count_2_minus++;
                    }
                    if (strpos($txtRosterColumns, "chkBreakIn") !== false) {
                        if ($csv != "yes") {
                            print "<td><font face='Verdana' size='2'> </font></td>";
                        } else {
                            print ";";
                        }
                        $column_count_2_minus++;
                    }
                    if (strpos($txtRosterColumns, "chkClose") !== false) {
                        if ($csv != "yes") {
                            print "<td><font face='Verdana' size='1'><b>" . $close . "</b></font></td>";
                        } else {
                            print $close . ";";
                        }
                        if ($lstTerminal == "Yes") {
                            if ($csv != "yes") {
                                print "<td><font face='Verdana' size='1'><b>" . $cgate . "</b></font></td>";
                            } else {
                                print $cgate . ";";
                            }
                        }
                        $column_count_2_minus++;
                    }
                    if ($txtMACAddress == "40-A8-F0-23-F0-AD") {
                        if ($csv != "yes") {
                            print "<td><font face='Verdana' size='1'>A</font></td>";
                        } else {
                            print "A;";
                        }
                        $column_count_2_minus++;
                    }
                    for ($j = 0; $j <= $column_count_2 - ($column_count_2_minus + 3); $j++) {
                        if ($csv != "yes") {
                            print "<td><font face='Verdana' size='1'> </font></td>";
                        } else {
                            print ";";
                        }
                    }
                    if ($csv != "yes") {
                        print "</tr>";
                    } else {
                        print "\n";
                    }
                    $count++;
                }
            }
        }
    }
    return $count;
}

function displayReportHeader($session_variable, $txtRosterColumns, $lstRemark, $prints, $txtMACAddress, $timecard) {
    print "<tr>";
    if ($timecard != "yes") {
        print "<td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td>";
    }
    if (insertToday() < 20150331 && $timecard != "yes") {
        print "<td><font face='Verdana' size='2'>TRML</font></td>";
    }
    if (strpos($txtRosterColumns, "chkIDColumn") !== false && $timecard != "yes") {
        print "<td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td>";
        $column_count_1++;
    }
    if (strpos($txtRosterColumns, "chkDept") !== false && $timecard != "yes") {
        print "<td><font face='Verdana' size='2'>Dept</font></td>";
        $column_count_1++;
    }
    if (strpos($txtRosterColumns, "chkDiv") !== false && $timecard != "yes") {
        print "<td><font face='Verdana' size='2'>Div/Desg</font></td>";
        $column_count_1++;
    }
    if (strpos($txtRosterColumns, "chkRmk") !== false && $timecard != "yes") {
        print "<td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "RemarkColumnName"] . "</font></td>";
        $column_count_1++;
    }
    if (strpos($txtRosterColumns, "chkF1") !== false && $timecard != "yes") {
        print "<td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "F1"] . "</font></td>";
        $column_count_1++;
    }
    if (strpos($txtRosterColumns, "chkF2") !== false && $timecard != "yes") {
        print "<td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "F2"] . "</font></td>";
        $column_count_1++;
    }
    if (strpos($txtRosterColumns, "chkF3") !== false && $timecard != "yes") {
        print "<td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "F3"] . "</font></td>";
        $column_count_1++;
    }
    if (strpos($txtRosterColumns, "chkF4") !== false && $timecard != "yes") {
        print "<td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "F4"] . "</font></td>";
        $column_count_1++;
    }
    if (strpos($txtRosterColumns, "chkF5") !== false && $timecard != "yes") {
        print "<td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "F5"] . "</font></td>";
        $column_count_1++;
    }
    if (strpos($txtRosterColumns, "chkShift") !== false) {
        print "<td><font face='Verdana' size='2'>Shift</font></td>";
        $column_count_1++;
    }
    if (strpos($txtRosterColumns, "chkOT1") !== false) {
        print "<td><font face='Verdana' size='2'>OT 1</font></td>";
        $column_count_1++;
    }
    if (strpos($txtRosterColumns, "chkOT2") !== false) {
        print "<td><font face='Verdana' size='2'>OT 2</font></td>";
        $column_count_1++;
    }
    print "<td><font face='Verdana' size='2'>Date</font></td> <td><font face='Verdana' size='2'>Day</font></td>";
    if (strpos($txtRosterColumns, "chkFlag") !== false) {
        print "<td><font face='Verdana' size='2'>Flag</font></td>";
        $column_count_2++;
    }
    if (strpos($txtRosterColumns, "chkEntry") !== false) {
        print "<td><font face='Verdana' size='2'>Entry</font></td>";
        $column_count_2++;
    }
    if (strpos($txtRosterColumns, "chkStart") !== false) {
        print "<td><font face='Verdana' size='2'><b>Start</b></font></td>";
        $column_count_2++;
    }
    if (strpos($txtRosterColumns, "chkBreakOut") !== false) {
        print "<td><font face='Verdana' size='2'>BreakOut</font></td>";
        $column_count_2++;
    }
    if (strpos($txtRosterColumns, "chkBreakIn") !== false) {
        print "<td><font face='Verdana' size='2'>BreakIn</font></td>";
        $column_count_2++;
    }
    if (strpos($txtRosterColumns, "chkClose") !== false) {
        print "<td><font face='Verdana' size='2'><b>Close</b></font></td>";
        $column_count_2++;
    }
    if (strpos($txtRosterColumns, "chkExit") !== false) {
        print "<td><font face='Verdana' size='2'>Exit</font></td>";
        $column_count_2++;
    }
    if (strpos($txtRosterColumns, "chkEarlyIn") !== false) {
        print "<td><font face='Verdana' size='2'>Early In <br>(Min)</font></td>";
        $column_count_2++;
    }
    if (strpos($txtRosterColumns, "chkLateIn") !== false) {
        print "<td><font face='Verdana' size='2'>Late In <br>(Min)</font></td>";
        $column_count_2++;
    }
    if (strpos($txtRosterColumns, "chkLessBreak") !== false) {
        print "<td><font face='Verdana' size='2'>Less Break <br>(Min)</font></td>";
        $column_count_2++;
    }
    if (strpos($txtRosterColumns, "chkMoreBreak") !== false) {
        print "<td><font face='Verdana' size='2'>More Break <br>(Min)</font></td>";
        $column_count_2++;
    }
    if (strpos($txtRosterColumns, "chkEarlyOut") !== false) {
        print "<td><font face='Verdana' size='2'>Early Out <br>(Min)</font></td>";
        $column_count_2++;
    }
    if (strpos($txtRosterColumns, "chkLateOut") !== false) {
        print "<td><font face='Verdana' size='2'>Late Out <br>(Min)</font></td>";
        $column_count_2++;
    }
    if (strpos($txtRosterColumns, "chkGrace") !== false) {
        print "<td><font face='Verdana' size='2'>Grace <br>(Min)</font></td>";
        $column_count_2++;
    }
    if (strpos($txtRosterColumns, "chkNormal") !== false) {
        print "<td><font face='Verdana' size='2'>Normal <br>(Hrs)</font></td>";
        $column_count_2++;
    }
    if (strpos($txtRosterColumns, "chkOT") !== false) {
        print "<td><font face='Verdana' size='2'>OT <br>(Hrs)</font></td>";
        $column_count_2++;
    }
    if (strpos($txtRosterColumns, "chkAppOT") !== false) {
        print "<td><font face='Verdana' size='2'>App OT <br>(Hrs)</font></td>";
        $column_count_2++;
        if (getRegister($txtMACAddress, 7) == "25") {
            print "<td><font face='Verdana' size='2'>App Late In<br>(Min)</font></td>";
            print "<td><font face='Verdana' size='2'>Total App OT <br>(Hrs)</font></td>";
            $column_count_2++;
        }
    }
    if (strpos($txtRosterColumns, "chkTH") !== false) {
        print "<td><font face='Verdana' size='2'>Total <br>(Hrs)</font></td>";
        $column_count_2++;
    }
    if ($lstRemark != "" && $prints == "yes") {
        print "<td width=50><font face='Verdana' size='2'>Remarks</font></td>";
        $column_count_2++;
    }
    if ($txtMACAddress == "40-A8-F0-23-F0-AD") {
        print "<td><font face='Verdana' size='2'>P/A</font></td>";
        $column_count_2++;
    }
    print "</tr>";
}

echo "\r\n<script>\r\nfunction submitRecord(a){\r\n\tx = document.frm1;\t\r\n\tif (a == 0){\r\n\t\tif (confirm(\"Exempt LATE IN for Selected Employees. This Process CANNOT be Reversed\")){\t\t\t\r\n\t\t\tx.act.value='exemptLateIn';\r\n\t\t\tx.btExemptLateIn.disabled = true;\r\n\t\t\tx.submit();\r\n\t\t}\r\n\t}else if (a == 1){\r\n\t\tif (confirm(\"Exempt EARLY OUT for Selected Employees. This Process CANNOT be Reversed\")){\r\n\t\t\tx.act.value='exemptEarlyOut';\r\n\t\t\tx.btExemptEarlyOut.disabled = true;\r\n\t\t\tx.submit();\r\n\t\t}\r\n\t}else if (a == 2){\r\n\t\tif (confirm(\"Exempt MORE BREAK for Selected Employees. This Process CANNOT be Reversed\")){\r\n\t\t\tx.act.value='exemptMoreBreak';\r\n\t\t\tx.btExemptMoreBreak.disabled = true;\r\n\t\t\tx.submit();\r\n\t\t}\r\n\t}\r\n}\r\n\r\nfunction approveOT(x, y, z){\r\n\tif (x.checked == true){\r\n\t\tif (y.value == 0 || y.value == ''){\r\n\t\t\ty.value = z.value;\r\n\t\t}\r\n\t}else{\r\n\t\ty.value = 0;\t\r\n\t}\r\n}\r\n\r\nfunction checkOTValue(x){\r\n\tif (x.value == '' || x.value*1 != x.value/1 || x.value*1 > 1440){\r\n\t\talert('Please enter a valid Approved OT Value');\r\n\t\tx.focus();\r\n\t\treturn;\r\n\t}\r\n}\r\n\r\nfunction approveAll(){\t\r\n\tx = document.frm1;\r\n\ty = x.chkAOT;\r\n\tz = x.txtCount.value;\t\r\n\tfor (i=0;i<z;i++){\t\t\r\n\t\tif (y.checked == true){\t\t\t\r\n\t\t\tdocument.getElementById(\"chkAOT\"+i).checked = true;\t\t\t\r\n\t\t}else{\r\n\t\t\tdocument.getElementById(\"chkAOT\"+i).checked = false;\t\t\t\r\n\t\t}\r\n\t}\r\n}\r\n\r\nfunction copyRemarkAll(){\r\n\tif (confirm(\"COPY Attendance Remark from FIRST row to all other BLANK Remark Fields\")){\r\n\t\tvar x = document.frm1;\r\n\t\tvar count = x.txtCount.value;\r\n\t\tif (count > 0){\r\n\t\t\tif (x.txtARemark0.value != \"\"){\r\n\t\t\t\tfor (i=0;i<count;i++){\r\n\t\t\t\t\tif (document.getElementById(\"txtARemark\"+i).value == \"\" || document.getElementById(\"txtARemark\"+i).value == \".\"){\r\n\t\t\t\t\t\tdocument.getElementById(\"txtARemark\"+i).value = x.txtARemark0.value;\r\n\t\t\t\t\t}\r\n\t\t\t\t}\r\n\t\t\t}\r\n\t\t}\r\n\t}\r\n}\r\n\r\nfunction resetRemarkAll(){\r\n\tif (confirm(\"Reset All Attendance Remarks\")){\r\n\t\tvar x = document.frm1;\r\n\t\tvar count = x.txtCount.value;\t\r\n\t\tfor (i=0;i<count;i++){\r\n\t\t\tdocument.getElementById(\"txtARemark\"+i).value = \"\";\r\n\t\t}\r\n\t}\t\r\n}\r\n</script>\r\n</center></body></html>";
print "</div></div></div></div></div>";
include 'footer.php';
?>

<script>
//    function approveAll() {
//        x = document.frm1;
//        y = x.chkAOT;
//        z = x.txtCount.value;
//
//        for (i = 0; i < z; i++) {
//            if (y.checked == true) {
//                document.getElementById("chkAOT" + i).checked = true;
//            } else {
//                document.getElementById("chkAOT" + i).checked = false;
//            }
//        }
//    }
        document.getElementById('export-btn').addEventListener('click', function () {  
            const rows = Array.from(document.querySelectorAll('#zero_config tr'));  
            const csvContent = rows.map(row =>   
                Array.from(row.querySelectorAll('th, td')) // Selects all header and data cells  
                     .map(cell => {  
                         // Extract only the text content without any HTML tags and nested elements  
                         return extractText(cell);   
                     })  
                     .map(text => `"${text.replace(/"/g, '""')}"`) // Escape double quotes by repeating them  
                     .join(',') // Join cell values with commas  
            ).join('\n'); // Join rows with new lines  

            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });  
            const link = document.createElement('a');  
            const url = URL.createObjectURL(blob);  
            link.setAttribute('href', url);  
            link.setAttribute('download', 'ReportDailyRoster.csv');  
            document.body.appendChild(link);  
            link.click();  
            document.body.removeChild(link);  
        });  

        // Function to extract only the text content from an element (removes all HTML tags)  
        function extractText(element) {  
            // Use the textContent property and trim it  
            return element.textContent.trim().replace(/\s+/g, ' '); // Replace multiple spaces with a single space  
        }
</script>