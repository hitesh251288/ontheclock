<?php


ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "30";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=ReportFlagLimit.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$iconn = openIConnection();
$act = $_GET["act"];
if ($act == "") {
    $act = $_POST["act"];
}
$prints = $_GET["prints"];
$excel = $_GET["excel"];
$message = $_GET["message"];
if ($message == "") {
    $message = "Employee Annual Flag Limits Report";
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
if ($txtFrom == "") {
    $txtFrom = displayDate(getNextDay(insertToday(), 1));
}
$txtRemark = $_POST["txtRemark"];
$txtPhone = $_POST["txtPhone"];
$txtSNo = $_POST["txtSNo"];
$txhCount = $_POST["txhCount"];
$lstEmployeeStatus = "";
if (isset($_POST["lstEmployeeStatus"])) {
    $lstEmployeeStatus = $_POST["lstEmployeeStatus"];
} else {
    $lstEmployeeStatus = "ACT";
}
$lstType = $_POST["lstType"];
$lstYear = $_POST["lstYear"];
if ($lstYear == "") {
    $lstYear = substr(insertToday(), 0, 4);
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
                            <h4 class="page-title">Employee Annual Flag Limits Report</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Employee Annual Flag Limits Report
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

<?php
}
print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='ReportFlagLimit.php'><input type='hidden' name='act' value='searchRecord'>";
//print "<html><title>Employee Annual Flag Limits Report</title>";
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
        header("Content-Disposition: attachment; filename=ReportFlagLimit.xls");
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
                $query = "SELECT id, name from tgroup ORDER BY name";
                displayList("lstShift", "Current Shift: ", $lstShift, $prints, $conn, $query, "", "", "");
                ?>
            </div>
            <?php 
            displaySearchFields($conn, $prints, $session_variable, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $lstEmployeeName, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
            ?>
        </div>
        <div class="row">
        <?php 
            if ($prints != "yes") {
                print "<div class='col-2'>";
                print "<label class='form-label'>Year:</label><select name='lstYear' class='form-control'> <option selected value='" . $lstYear . "'>" . $lstYear . "</option>";
                for ($i = 2007; $i <= substr(insertToday(), 0, 4); $i++) {
                    print "<option value='" . $i . "'>" . $i . "</option>";
                }
                print "<option value=''>---</option> </select></td>";
                print "</div>";
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
                print "<center><br><a name='1'><input name='btSearch' class='btn btn-primary' type='submit' value='Search Record'></a></center>";
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
    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tuser.idno, tuser.remark, EmployeeFlag.Violet, EmployeeFlag.Indigo, EmployeeFlag.Blue, EmployeeFlag.Green, EmployeeFlag.Yellow, EmployeeFlag.Orange, EmployeeFlag.Red, EmployeeFlag.Brown, EmployeeFlag.Gray, EmployeeFlag.Purple, tuser.PassiveType, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, tgroup, EmployeeFlag WHERE EmployeeFlag.EmployeeID = tuser.id AND tuser.group_id = tgroup.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
    if ($lstShift != "") {
        $query = $query . " AND tgroup.id = " . $lstShift;
    }
    $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
    $query = $query . employeeStatusQuery($lstEmployeeStatus);
    $query = $query . " ORDER BY " . $lstSort;
    if ($excel != "yes") {
        print "<p align='center'><font face='Verdana' size='1'><b>" . $_SESSION[$session_variable . "FlagReportText"] . "</b></font></p>";
    }
    print '<div class="row"><div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12"><div class="card"><div class="card-body table-responsive">';
    if ($prints != "yes") {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800' class='table table-striped table-bordered dataTable' id='zero_config'>";
    } else {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%' class='table table-striped table-bordered dataTable'>";
    }
    print "<input type='hidden' name='txhFrom' value='" . $txtFrom . "'>";
    print "<thead><tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Current Shift</font></td> <td><a title='Violet - Max Limit'><font face='Verdana' size='2' color='Violet'><b>V</b></font></a></td> <td><a title='Violet - Current Utilization'><font face='Verdana' size='2' color='Violet'>V</font></a></td> <td><img border='0' src='img/logo.gif' height='20' width='1'></td> <td><a title='Indigo - Max Limit'><font face='Verdana' size='2' color='Indigo'><b>I</b></font></a></td> <td><a title='Indigo - Current Utilization'><font face='Verdana' size='2' color='Indigo'>I</font></a></td> <td><img border='0' src='img/logo.gif' height='20' width='1'></td> <td><a title='Blue - Max Limit'><font face='Verdana' size='2' color='Blue'><b>B</b></font></a></td> <td><a title='Blue - Current Utilization'><font face='Verdana' size='2' color='Blue'>B</font></a></td> <td><img border='0' src='img/logo.gif' height='20' width='1'></td> <td><a title='Green - Max Limit'><font face='Verdana' size='2' color='Green'><b>G</b></font></a></td> <td><a title='Green - Current Utilization'><font face='Verdana' size='2' color='Green'>G</font></a></td> <td><img border='0' src='img/logo.gif' height='20' width='1'></td> <td bgcolor='brown'><a title='Yellow - Max Limit'><font face='Verdana' size='2' color='Yellow'><b>Y</b></font></a></td> <td bgcolor='brown'><a title='Yellow - Current Utilization'><font face='Verdana' size='2' color='Yellow'>Y</font></a></td> <td><img border='0' src='img/logo.gif' height='20' width='1'></td> <td><a title='Orange - Max Limit'><font face='Verdana' size='2' color='Orange'><b>O</b></font></a></td> <td><a title='Orange - Current Utilization'><font face='Verdana' size='2' color='Orange'>O</font></a></td> <td><img border='0' src='img/logo.gif' height='20' width='1'></td> <td><a title='Red - Max Limit'><font face='Verdana' size='2' color='Red'><b>R</b></font></a></td> <td><a title='Red - Current Utilization'><font face='Verdana' size='2' color='Red'>R</font></a></td> <td><img border='0' src='img/logo.gif' height='20' width='1'></td> <td><a title='Gray - Max Limit'><font face='Verdana' size='2' color='Gray'><b>GR</b></font></a></td> <td><a title='Gray - Current Utilization'><font face='Verdana' size='2' color='Gray'>GR</font></a></td> <td><img border='0' src='img/logo.gif' height='20' width='1'></td> <td><a title='Brown - Max Limit'><font face='Verdana' size='2' color='Brown'><b>BR</b></font></a></td> <td><a title='Brown - Current Utilization'><font face='Verdana' size='2' color='Brown'>BR</font></a></td> <td><img border='0' src='img/logo.gif' height='20' width='1'></td> <td><a title='PR - Max Limit'><font face='Verdana' size='2' color='Purple'><b>PR</b></font></a></td> <td><a title='PR - Current Utilization'><font face='Verdana' size='2' color='Purple'>PR</font></a></td> </tr></thead>";
    $result = mysqli_query($conn, $query);
    for ($count = 0; $cur = mysqli_fetch_row($result); $count++) {
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
        print "<tr><td><a title='ID'><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'> <font face='Verdana' color='#000000' size='1'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $cur[1] . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $cur[5] . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $cur[2] . "</font></a></td> <td><a title='Div/Desg'><font face='Verdana' size='1'>" . $cur[3] . "</font></a></td> <td><a title='Rmk'><font face='Verdana' size='1'>" . $cur[6] . "</font></a></td> <td><a title='Current Shift'><font face='Verdana' size='1'>" . $cur[4] . "</font></a></td> ";
        print "<td><font face='Verdana' size='1' color='Violet'><b>" . $cur[7] . "</b></font></td>";
        $sub_query = "SELECT COUNT(FlagDayRotationID) FROM FlagDayRotation WHERE e_id = " . $cur[0] . " AND Flag = 'Violet' AND e_date >= " . $lstYear . "0101 AND e_date <= " . $lstYear . "1231 AND RecStat = 0";
        $sub_result = selectData($conn, $sub_query);
        $pre_flag_count = $sub_result[0];
        if ($pre_flag_count == "") {
            $pre_flag_count = 0;
        }
        $sub_query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster WHERE EmployeeID = " . $cur[0] . " AND Flag = 'Violet' AND ADate >= " . $lstYear . "0101 AND ADate <= " . $lstYear . "1231 AND Flag NOT LIKE 'Delete'";
        $sub_result = selectData($conn, $sub_query);
        $post_flag_count = $sub_result[0];
        if ($post_flag_count == "") {
            $post_flag_count = 0;
        }
        if ($prints == "yes" || $pre_flag_count + $post_flag_count == 0) {
            print "<td><font face='Verdana' size='1' color='Violet'>" . ($pre_flag_count + $post_flag_count) . "</font></td>";
        } else {
            displayDate($lstYear . "0101");
            displayDate($lstYear . "1231");
            print "<td><a title='Click to view Hourly Details (ACTIVE EMPLOYEES ONLY)' href='ReportDailyRoster.php?act=searchRecord&prints=yes&excel=yes&subReport=yes&lstAbsent=No&lstImproperClocking=No&lstEmployeeStatus=" . $cur[17] . "&lstEmployeeID=" . $cur[0] . "&txtFrom=" . displayDate($lstYear . "0101") . "&txtTo=" . displayDate($lstYear . "1231") . "&lstColourFlag=Violet' target='_blank'><font face='Verdana' size='1' color='Violet'>" . ($pre_flag_count + $post_flag_count) . "</font></a></td>";
        }
        print "" . "<td><font face='Arial' size='1'>" . $nbsp . ";</font></td>";
        print "<td><font face='Verdana' size='1' color='Indigo'><b>" . $cur[8] . "</b></font></td>";
        $sub_query = "SELECT COUNT(FlagDayRotationID) FROM FlagDayRotation WHERE e_id = " . $cur[0] . " AND Flag = 'Indigo' AND e_date >= " . $lstYear . "0101 AND e_date <= " . $lstYear . "1231 AND RecStat = 0";
        $sub_result = selectData($conn, $sub_query);
        $pre_flag_count = $sub_result[0];
        if ($pre_flag_count == "") {
            $pre_flag_count = 0;
        }
        $sub_query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster WHERE EmployeeID = " . $cur[0] . " AND Flag = 'Indigo' AND ADate >= " . $lstYear . "0101 AND ADate <= " . $lstYear . "1231 AND Flag NOT LIKE 'Delete'";
        $sub_result = selectData($conn, $sub_query);
        $post_flag_count = $sub_result[0];
        if ($post_flag_count == "") {
            $post_flag_count = 0;
        }
        if ($prints == "yes" || $pre_flag_count + $post_flag_count == 0) {
            print "<td><font face='Verdana' size='1' color='Indigo'>" . ($pre_flag_count + $post_flag_count) . "</font></td>";
        } else {
            displayDate($lstYear . "0101");
            displayDate($lstYear . "1231");
            print "<td><a title='Click to view Hourly Details (ACTIVE EMPLOYEES ONLY)' href='ReportDailyRoster.php?act=searchRecord&prints=yes&excel=yes&subReport=yes&lstAbsent=No&lstImproperClocking=No&lstEmployeeStatus=" . $cur[17] . "&lstEmployeeID=" . $cur[0] . "&txtFrom=" . displayDate($lstYear . "0101") . "&txtTo=" . displayDate($lstYear . "1231") . "&lstColourFlag=Indigo' target='_blank'><font face='Verdana' size='1' color='Indigo'>" . ($pre_flag_count + $post_flag_count) . "</font></a></td>";
        }
        print "" . "<td><font face='Arial' size='1'>" . $nbsp . ";</font></td>";
        print "<td><font face='Verdana' size='1' color='Blue'><b>" . $cur[9] . "</b></font></td>";
        $sub_query = "SELECT COUNT(FlagDayRotationID) FROM FlagDayRotation WHERE e_id = " . $cur[0] . " AND Flag = 'Blue' AND e_date >= " . $lstYear . "0101 AND e_date <= " . $lstYear . "1231 AND  RecStat = 0";
        $sub_result = selectData($conn, $sub_query);
        $pre_flag_count = $sub_result[0];
        if ($pre_flag_count == "") {
            $pre_flag_count = 0;
        }
        $sub_query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster WHERE EmployeeID = " . $cur[0] . " AND Flag = 'Blue' AND ADate >= " . $lstYear . "0101 AND ADate <= " . $lstYear . "1231 AND Flag NOT LIKE 'Delete'";
        $sub_result = selectData($conn, $sub_query);
        $post_flag_count = $sub_result[0];
        if ($post_flag_count == "") {
            $post_flag_count = 0;
        }
        if ($prints == "yes" || $pre_flag_count + $post_flag_count == 0) {
            print "<td><font face='Verdana' size='1' color='Blue'>" . ($pre_flag_count + $post_flag_count) . "</font></td>";
        } else {
            displayDate($lstYear . "0101");
            displayDate($lstYear . "1231");
            print "<td><a title='Click to view Hourly Details (ACTIVE EMPLOYEES ONLY)' href='ReportDailyRoster.php?act=searchRecord&prints=yes&excel=yes&subReport=yes&lstAbsent=No&lstImproperClocking=No&lstEmployeeStatus=" . $cur[17] . "&lstEmployeeID=" . $cur[0] . "&txtFrom=" . displayDate($lstYear . "0101") . "&txtTo=" . displayDate($lstYear . "1231") . "&lstColourFlag=Blue' target='_blank'><font face='Verdana' size='1' color='Blue'>" . ($pre_flag_count + $post_flag_count) . "</font></a></td>";
        }
        print "" . "<td><font face='Arial' size='1'>" . $nbsp . ";</font></td>";
        print "<td><font face='Verdana' size='1' color='Green'><b>" . $cur[10] . "</b></font></td>";
        $sub_query = "SELECT COUNT(FlagDayRotationID) FROM FlagDayRotation WHERE e_id = " . $cur[0] . " AND Flag = 'Green' AND e_date >= " . $lstYear . "0101 AND e_date <= " . $lstYear . "1231 AND  RecStat = 0";
        $sub_result = selectData($conn, $sub_query);
        $pre_flag_count = $sub_result[0];
        if ($pre_flag_count == "") {
            $pre_flag_count = 0;
        }
        $sub_query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster WHERE EmployeeID = " . $cur[0] . " AND Flag = 'Green' AND ADate >= " . $lstYear . "0101 AND ADate <= " . $lstYear . "1231 AND Flag NOT LIKE 'Delete'";
        $sub_result = selectData($conn, $sub_query);
        $post_flag_count = $sub_result[0];
        if ($post_flag_count == "") {
            $post_flag_count = 0;
        }
        if ($prints == "yes" || $pre_flag_count + $post_flag_count == 0) {
            print "<td><font face='Verdana' size='1' color='Green'>" . ($pre_flag_count + $post_flag_count) . "</font></td>";
        } else {
            displayDate($lstYear . "0101");
            displayDate($lstYear . "1231");
            print "<td><a title='Click to view Hourly Details (ACTIVE EMPLOYEES ONLY)' href='ReportDailyRoster.php?act=searchRecord&prints=yes&excel=yes&subReport=yes&lstAbsent=No&lstImproperClocking=No&lstEmployeeStatus=" . $cur[17] . "&lstEmployeeID=" . $cur[0] . "&txtFrom=" . displayDate($lstYear . "0101") . "&txtTo=" . displayDate($lstYear . "1231") . "&lstColourFlag=Green' target='_blank'><font face='Verdana' size='1' color='Green'>" . ($pre_flag_count + $post_flag_count) . "</font></a></td>";
        }
        print "" . "<td><font face='Arial' size='1'>" . $nbsp . ";</font></td>";
        print "<td bgcolor='brown'><font face='Verdana' size='1' color='yellow'><b>" . $cur[11] . "</b></font></td>";
        $sub_query = "SELECT COUNT(FlagDayRotationID) FROM FlagDayRotation WHERE e_id = " . $cur[0] . " AND Flag = 'Yellow' AND e_date >= " . $lstYear . "0101 AND e_date <= " . $lstYear . "1231 AND  RecStat = 0";
        $sub_result = selectData($conn, $sub_query);
        $pre_flag_count = $sub_result[0];
        if ($pre_flag_count == "") {
            $pre_flag_count = 0;
        }
        $sub_query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster WHERE EmployeeID = " . $cur[0] . " AND Flag = 'Yellow' AND ADate >= " . $lstYear . "0101 AND ADate <= " . $lstYear . "1231 AND Flag NOT LIKE 'Delete'";
        $sub_result = selectData($conn, $sub_query);
        $post_flag_count = $sub_result[0];
        if ($post_flag_count == "") {
            $post_flag_count = 0;
        }
        if ($prints == "yes" || $pre_flag_count + $post_flag_count == 0) {
            print "<td bgcolor='Brown'><font face='Verdana' size='1' color='Yellow'>" . ($pre_flag_count + $post_flag_count) . "</font></td>";
        } else {
            displayDate($lstYear . "0101");
            displayDate($lstYear . "1231");
            print "<td bgcolor='Brown'><a title='Click to view Hourly Details (ACTIVE EMPLOYEES ONLY)' href='ReportDailyRoster.php?act=searchRecord&prints=yes&excel=yes&subReport=yes&lstAbsent=No&lstImproperClocking=No&lstEmployeeStatus=" . $cur[17] . "&lstEmployeeID=" . $cur[0] . "&txtFrom=" . displayDate($lstYear . "0101") . "&txtTo=" . displayDate($lstYear . "1231") . "&lstColourFlag=Brown' target='_blank'><font face='Verdana' size='1' color='Yellow'>" . ($pre_flag_count + $post_flag_count) . "</font></a></td>";
        }
        print "" . "<td><font face='Arial' size='1'>" . $nbsp . ";</font></td>";
        print "<td><font face='Verdana' size='1' color='Orange'><b>" . $cur[12] . "</b></font></td>";
        $sub_query = "SELECT COUNT(FlagDayRotationID) FROM FlagDayRotation WHERE e_id = " . $cur[0] . " AND Flag = 'Orange' AND e_date >= " . $lstYear . "0101 AND e_date <= " . $lstYear . "1231 AND  RecStat = 0";
        $sub_result = selectData($conn, $sub_query);
        $pre_flag_count = $sub_result[0];
        if ($pre_flag_count == "") {
            $pre_flag_count = 0;
        }
        $sub_query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster WHERE EmployeeID = " . $cur[0] . " AND Flag = 'Orange' AND ADate >= " . $lstYear . "0101 AND ADate <= " . $lstYear . "1231 AND Flag NOT LIKE 'Delete'";
        $sub_result = selectData($conn, $sub_query);
        $post_flag_count = $sub_result[0];
        if ($post_flag_count == "") {
            $post_flag_count = 0;
        }
        if ($prints == "yes" || $pre_flag_count + $post_flag_count == 0) {
            print "<td><font face='Verdana' size='1' color='Orange'>" . ($pre_flag_count + $post_flag_count) . "</font></td>";
        } else {
            displayDate($lstYear . "0101");
            displayDate($lstYear . "1231");
            print "<td><a title='Click to view Hourly Details (ACTIVE EMPLOYEES ONLY)' href='ReportDailyRoster.php?act=searchRecord&prints=yes&excel=yes&subReport=yes&lstAbsent=No&lstImproperClocking=No&lstEmployeeStatus=" . $cur[17] . "&lstEmployeeID=" . $cur[0] . "&txtFrom=" . displayDate($lstYear . "0101") . "&txtTo=" . displayDate($lstYear . "1231") . "&lstColourFlag=Orange' target='_blank'><font face='Verdana' size='1' color='Orange'>" . ($pre_flag_count + $post_flag_count) . "</font></a></td>";
        }
        print "" . "<td><font face='Arial' size='1'>" . $nbsp . ";</font></td>";
        print "<td><font face='Verdana' size='1' color='Red'><b>" . $cur[13] . "</b></font></td>";
        $sub_query = "SELECT COUNT(FlagDayRotationID) FROM FlagDayRotation WHERE e_id = " . $cur[0] . " AND Flag = 'Red' AND e_date >= " . $lstYear . "0101 AND e_date <= " . $lstYear . "1231 AND  RecStat = 0";
        $sub_result = selectData($conn, $sub_query);
        $pre_flag_count = $sub_result[0];
        if ($pre_flag_count == "") {
            $pre_flag_count = 0;
        }
        $sub_query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster WHERE EmployeeID = " . $cur[0] . " AND Flag = 'Red' AND ADate >= " . $lstYear . "0101 AND ADate <= " . $lstYear . "1231 AND Flag NOT LIKE 'Delete'";
        $sub_result = selectData($conn, $sub_query);
        $post_flag_count = $sub_result[0];
        if ($post_flag_count == "") {
            $post_flag_count = 0;
        }
        if ($prints == "yes" || $pre_flag_count + $post_flag_count == 0) {
            print "<td><font face='Verdana' size='1' color='Red'>" . ($pre_flag_count + $post_flag_count) . "</font></td>";
        } else {
            displayDate($lstYear . "0101");
            displayDate($lstYear . "1231");
            print "<td><a title='Click to view Hourly Details (ACTIVE EMPLOYEES ONLY)' href='ReportDailyRoster.php?act=searchRecord&prints=yes&excel=yes&subReport=yes&lstAbsent=No&lstImproperClocking=No&lstEmployeeStatus=" . $cur[17] . "&lstEmployeeID=" . $cur[0] . "&txtFrom=" . displayDate($lstYear . "0101") . "&txtTo=" . displayDate($lstYear . "1231") . "&lstColourFlag=Red' target='_blank'><font face='Verdana' size='1' color='Red'>" . ($pre_flag_count + $post_flag_count) . "</font></a></td>";
        }
        print "" . "<td><font face='Arial' size='1'>" . $nbsp . ";</font></td>";
        print "<td><font face='Verdana' size='1' color='Gray'><b>" . $cur[15] . "</b></font></td>";
        $sub_query = "SELECT COUNT(FlagDayRotationID) FROM FlagDayRotation WHERE e_id = " . $cur[0] . " AND Flag = 'Gray' AND e_date >= " . $lstYear . "0101 AND e_date <= " . $lstYear . "1231 AND  RecStat = 0";
        $sub_result = selectData($conn, $sub_query);
        $pre_flag_count = $sub_result[0];
        if ($pre_flag_count == "") {
            $pre_flag_count = 0;
        }
        $sub_query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster WHERE EmployeeID = " . $cur[0] . " AND Flag = 'Gray' AND ADate >= " . $lstYear . "0101 AND ADate <= " . $lstYear . "1231 AND Flag NOT LIKE 'Delete'";
        $sub_result = selectData($conn, $sub_query);
        $post_flag_count = $sub_result[0];
        if ($post_flag_count == "") {
            $post_flag_count = 0;
        }
        if ($prints == "yes" || $pre_flag_count + $post_flag_count == 0) {
            print "<td><font face='Verdana' size='1' color='Gray'>" . ($pre_flag_count + $post_flag_count) . "</font></td>";
        } else {
            displayDate($lstYear . "0101");
            displayDate($lstYear . "1231");
            print "<td><a title='Click to view Hourly Details (ACTIVE EMPLOYEES ONLY)' href='ReportDailyRoster.php?act=searchRecord&prints=yes&excel=yes&subReport=yes&lstAbsent=No&lstImproperClocking=No&lstEmployeeStatus=" . $cur[17] . "&lstEmployeeID=" . $cur[0] . "&txtFrom=" . displayDate($lstYear . "0101") . "&txtTo=" . displayDate($lstYear . "1231") . "&lstColourFlag=Gray' target='_blank'><font face='Verdana' size='1' color='Gray'>" . ($pre_flag_count + $post_flag_count) . "</font></a></td>";
        }
        print "" . "<td><font face='Arial' size='1'>" . $nbsp . ";</font></td>";
        print "<td><font face='Verdana' size='1' color='Brown'><b>" . $cur[14] . "</b></font></td>";
        $sub_query = "SELECT COUNT(FlagDayRotationID) FROM FlagDayRotation WHERE e_id = " . $cur[0] . " AND Flag = 'Brown' AND e_date >= " . $lstYear . "0101 AND e_date <= " . $lstYear . "1231 AND  RecStat = 0";
        $sub_result = selectData($conn, $sub_query);
        $pre_flag_count = $sub_result[0];
        if ($pre_flag_count == "") {
            $pre_flag_count = 0;
        }
        $sub_query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster WHERE EmployeeID = " . $cur[0] . " AND Flag = 'Brown' AND ADate >= " . $lstYear . "0101 AND ADate <= " . $lstYear . "1231 AND Flag NOT LIKE 'Delete'";
        $sub_result = selectData($conn, $sub_query);
        $post_flag_count = $sub_result[0];
        if ($post_flag_count == "") {
            $post_flag_count = 0;
        }
        if ($prints == "yes" || $pre_flag_count + $post_flag_count == 0) {
            print "<td><font face='Verdana' size='1' color='Brown'>" . ($pre_flag_count + $post_flag_count) . "</font></td>";
        } else {
            displayDate($lstYear . "0101");
            displayDate($lstYear . "1231");
            print "<td><a title='Click to view Hourly Details (ACTIVE EMPLOYEES ONLY)' href='ReportDailyRoster.php?act=searchRecord&prints=yes&excel=yes&subReport=yes&lstAbsent=No&lstImproperClocking=No&lstEmployeeStatus=" . $cur[17] . "&lstEmployeeID=" . $cur[0] . "&txtFrom=" . displayDate($lstYear . "0101") . "&txtTo=" . displayDate($lstYear . "1231") . "&lstColourFlag=Brown' target='_blank'><font face='Verdana' size='1' color='Brown'>" . ($pre_flag_count + $post_flag_count) . "</font></a></td>";
        }
        print "" . "<td><font face='Arial' size='1'>" . $nbsp . ";</font></td>";
        print "<td><font face='Verdana' size='1' color='Purple'><b>" . $cur[16] . "</b></font></td>";
        $sub_query = "SELECT COUNT(FlagDayRotationID) FROM FlagDayRotation WHERE e_id = " . $cur[0] . " AND Flag = 'Purple' AND e_date >= " . $lstYear . "0101 AND e_date <= " . $lstYear . "1231 AND  RecStat = 0";
        $sub_result = selectData($conn, $sub_query);
        $pre_flag_count = $sub_result[0];
        if ($pre_flag_count == "") {
            $pre_flag_count = 0;
        }
        $sub_query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster WHERE EmployeeID = " . $cur[0] . " AND Flag = 'Purple' AND ADate >= " . $lstYear . "0101 AND ADate <= " . $lstYear . "1231 AND Flag NOT LIKE 'Delete'";
        $sub_result = selectData($conn, $sub_query);
        $post_flag_count = $sub_result[0];
        if ($post_flag_count == "") {
            $post_flag_count = 0;
        }
        if ($prints == "yes" || $pre_flag_count + $post_flag_count == 0) {
            print "<td><font face='Verdana' size='1' color='Purple'>" . ($pre_flag_count + $post_flag_count) . "</font></td>";
        } else {
            displayDate($lstYear . "0101");
            displayDate($lstYear . "1231");
            print "<td><a title='Click to view Hourly Details (ACTIVE EMPLOYEES ONLY)' href='ReportDailyRoster.php?act=searchRecord&prints=yes&excel=yes&subReport=yes&lstAbsent=No&lstImproperClocking=No&lstEmployeeStatus=" . $cur[17] . "&lstEmployeeID=" . $cur[0] . "&txtFrom=" . displayDate($lstYear . "0101") . "&txtTo=" . displayDate($lstYear . "1231") . "&lstColourFlag=Purple' target='_blank'><font face='Verdana' size='1' color='Purple'>" . ($pre_flag_count + $post_flag_count) . "</font></a></td>";
        }
        print "</tr>";
    }
    print "<input type='hidden' name='txhCount' value='" . $count . "'></table>";
    if ($excel != "yes") {
        print "<br><p align='center'><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $count . "</b></font>";
    }
    if ($prints != "yes") {
        print "<br> <input type='button' value='Print Report' class='btn btn-primary' onClick='checkPrint(0)'>&nbsp;&nbsp;<input type='button' class='btn btn-primary' value='Excel' onClick='checkPrint(1)'>";
    }
    print "</p>";
    print "</form>";
}
echo "\r\n<script>\r\nfunction openWindow(a){\r\n\twindow.open(\"ReportFlagLimit.php?act=viewRecord&txtID=\"+a, \"\",\"height=400;width=400\");\r\n}\r\n\r\nfunction checkPrint(a){\r\n\tvar x = document.frm1;\r\n\tif (a == 0){\r\n\t\tif (confirm('Go Green - Think Twice before you Print this Document \\nAre you sure want to Print?')){\r\n\t\t\tx.action = 'ReportFlagLimit.php?prints=yes';\t\t\t\r\n\t\t}else{\r\n\t\t\treturn;\r\n\t\t}\r\n\t}else{\r\n\t\tx.action = 'ReportFlagLimit.php?prints=yes&excel=yes';\t\t\t\r\n\t}\r\n\tx.target = '_blank';\r\n\tx.submit();\r\n}\r\n\r\nfunction checkSearch(){\r\n\tvar x = document.frm1;\t\r\n\tx.action = 'ReportFlagLimit.php?prints=no';\r\n\tx.target = '_self';\r\n\tx.btSearch.disabled = true;\r\n\tx.submit();\r\n}\r\n\r\nfunction checkFlagTextbox(x){\r\n\tif (x.value*1 != x.value/1){\r\n\t\talert(\"ONLY Numeric Value ALLOWED\");\r\n\t\tx.focus();\r\n\t}\r\n}\r\n\r\nfunction saveChanges(){\r\n\tx = document.frm1;\r\n\tif (confirm(\"Save Changes?\")){\r\n\t\tx.act.value = \"saveChanges\";\r\n\t\tx.btSubmit.disabled = true;\r\n\t\tx.submit();\r\n\t}\r\n}\r\n</script>\r\n</center>";
print "</div></div></div></div></div>";
include 'footer.php';

?>