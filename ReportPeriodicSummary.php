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
$userrdsselection = $_SESSION[$session_variable . "userrdsselection"];
$userrdsfont = $_SESSION[$session_variable . "userrdsfont"];
$userrdscw = $_SESSION[$session_variable . "userrdscw"];
$userrdsheaderbreak = $_SESSION[$session_variable . "userrdsheaderbreak"];
$txtMACAddress = $_SESSION[$session_variable . "MACAddress"];
$frt = $_SESSION[$session_variable . "FlagReportText"];
if (!(checkSession($userlevel, $current_module) && $_SESSION[$session_variable . "VirdiLevel"] == "Classic")) {
    header("Location: " . $config["REDIRECT"] . "?url=ReportPeriodicSummary.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$act = $_GET["act"];
if ($act == "") {
    $act = $_POST["act"];
}
$lstSort = $_POST["lstSort"];
$prints = $_GET["prints"];
$excel = $_GET["excel"];
$subReport = $_GET["subReport"];
$message = $_GET["message"];
if ($message == "") {
    $message = "Day Summary Report<br>Report Valid ONLY for Shifts with Routine Type = Daily";
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
$lstType = $_POST["lstType"];
if ($lstType == "") {
    $lstType = "";
}
$lstCaptionO = $_POST["lstCaptionO"];
if ($lstCaptionO == "") {
    if (strpos($userrdsselection, "--O") !== false) {
        $lstCaptionO = "Yes";
    } else {
        $lstCaptionO = "No";
    }
}
$lstCaptionLIEO = $_POST["lstCaptionLIEO"];
if ($lstCaptionLIEO == "") {
    if (strpos($userrdsselection, "--LIEO") !== false) {
        $lstCaptionLIEO = "Yes";
    } else {
        $lstCaptionLIEO = "No";
    }
}
$lstCaptionN = $_POST["lstCaptionN"];
if ($lstCaptionN == "") {
    if (strpos($userrdsselection, "--N") !== false) {
        $lstCaptionN = "Yes";
    } else {
        $lstCaptionN = "No";
    }
}
$lstCaptionA = $_POST["lstCaptionA"];
if ($lstCaptionA == "") {
    if (strpos($userrdsselection, "--A") !== false) {
        $lstCaptionA = "Yes";
    } else {
        $lstCaptionA = "No";
    }
}
$lstCaptionP = $_POST["lstCaptionP"];
if ($lstCaptionP == "") {
    if (strpos($userrdsselection, "--P") !== false) {
        $lstCaptionP = "Yes";
    } else {
        $lstCaptionP = "No";
    }
}
$lstCaptionPreFlag = $_POST["lstCaptionPreFlag"];
if ($lstCaptionPreFlag == "") {
    if (strpos($userrdsselection, "--PreFlag") !== false) {
        $lstCaptionPreFlag = "Yes";
    } else {
        $lstCaptionPreFlag = "No";
    }
}
$lstCaptionIDNo = $_POST["lstCaptionIDNo"];
if ($lstCaptionIDNo == "") {
    if (strpos($userrdsselection, "--IDNo") !== false) {
        $lstCaptionIDNo = "Yes";
    } else {
        $lstCaptionIDNo = "No";
    }
}
$lstCaptionDept = $_POST["lstCaptionDept"];
if ($lstCaptionDept == "") {
    if (strpos($userrdsselection, "--Dept") !== false) {
        $lstCaptionDept = "Yes";
    } else {
        $lstCaptionDept = "No";
    }
}
$lstCaptionDiv = $_POST["lstCaptionDiv"];
if ($lstCaptionDiv == "") {
    if (strpos($userrdsselection, "--Div") !== false) {
        $lstCaptionDiv = "Yes";
    } else {
        $lstCaptionDiv = "No";
    }
}
$lstCaptionRemark = $_POST["lstCaptionRemark"];
if ($lstCaptionRemark == "") {
    if (strpos($userrdsselection, "--Remark") !== false) {
        $lstCaptionRemark = "Yes";
    } else {
        $lstCaptionRemark = "No";
    }
}
$lstCaptionTotal = $_POST["lstCaptionTotal"];
if ($lstCaptionTotal == "") {
    if (strpos($userrdsselection, "--Total") !== false) {
        $lstCaptionTotal = "Yes";
    } else {
        $lstCaptionTotal = "No";
    }
}
$lstCaptionShift = $_POST["lstCaptionShift"];
if ($lstCaptionShift == "") {
    if (strpos($userrdsselection, "--Shift") !== false) {
        $lstCaptionShift = "Yes";
    } else {
        $lstCaptionShift = "No";
    }
}
$txtFontSize = $_POST["txtFontSize"];
if ($txtFontSize == "") {
    $txtFontSize = $userrdsfont;
}
if ($txtFontSize == "") {
    $txtFontSize = "1";
}
$txtColumnWidth = $_POST["txtColumnWidth"];
if ($txtColumnWidth == "") {
    $txtColumnWidth = $userrdscw;
}
if ($txtColumnWidth == "") {
    $txtColumnWidth = "15%";
}
$txtHeaderBreak = $_POST["txtHeaderBreak"];
if ($txtHeaderBreak == "") {
    $txtHeaderBreak = $userrdsheaderbreak;
}
if ($txtHeaderBreak == "") {
    $txtHeaderBreak = "25";
}
$first_page_break = $txtHeaderBreak;
if ($lstType != "ACard") {
    $first_page_break = round($txtHeaderBreak / 3, 0);
}
$lstGroupBy = $_POST["lstGroupBy"];
$colcount = 0;
$userrdsselection = "";
$query = "SELECT RASSelection FROM UserMaster WHERE Username = '" . $username . "'";
$ras_result = selectData($conn, $query);
$ras = $ras_result[0];
if ($prints != "yes") {
    include 'header.php';
?>
<div class="page-breadcrumb">
                    <div class="row">
                        <div class="col-12 d-flex no-block align-items-center">
                            <h4 class="page-title">Day Summary</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Day Summary
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
        if ($subReport != "yes") {
            header("Content-type: application/x-msdownload");
            header("Content-Disposition: attachment; filename=vTime_DaySummaryReport.xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            print "<body>";
        }
    }
}
print "<center>";
$displayHead = false;
if ($excel != "yes") {
    if ($prints != "yes") {
//        displayHeader($prints, true, false);
        $displayHead = true;
    } else {
        if ($lstType != "ACard") {
//            displayHeader($prints, true, false);
            $displayHead = true;
        }
    }
}
print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='ReportPeriodicSummary.php'><input type='hidden' name='act' value='searchRecord'>";
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
//            print "<table width='100%' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' border='1' bordercolor='#C0C0C0'>";
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
                displayTextbox("txtFontSize", "Data Font Size:", $txtFontSize, $prints, 5, "", "");
                ?>
            </div>
            <div class="col-2">
                <?php 
                displayTextbox("txtTo", "Date To <font size='1'>(DD/MM/YYYY)</font>: ", $txtTo, $prints, 12, "", "");
                ?>
            </div>
            <div class="col-2">
                <?php 
                displayTextbox("txtColumnWidth", "<b>Days</b> Column Width:", $txtColumnWidth, $prints, 5, "", "");
                ?>
            </div>
        <?php 
            if ($prints != "yes") {
                print "<div class='col-2'>";
                print "<label class='form-label'>Record(s) Type:</label><select name='lstType' class='form-select select2 shadow-none'><option selected value='" . $lstType . "'>" . $lstType . "</option> <option value='Early In'>Early In</option> <option value='Late In'>Late In</option> <option value='Less Break'>Less Break</option> <option value='More Break'>More Break</option> <option value='Early Out'>Early Out</option> <option value='Late Out'>Late Out</option> <option value='Grace'>Grace</option> <option value='OT'>OT</option> <option value='Approved OT'>Approved OT</option> <option value='Night Shift'>Night Shift</option> <option value='Day Shift'>Day Shift</option> <option value='Flags'>Flags</option> <option value='ACard'>ACard</option> <option value=''>---</option> </select>";
                print "</div>";
                print "<div class='col-2'>";
                print "<label class='form-label'>Displayed Rows on Each Page:</label><input name='txtHeaderBreak' value='" . $txtHeaderBreak . "' size='5' class='form-select select2 shadow-none'>";
                print "</div>";
                print "</div>";
                print "<div class='row'>";
                print "<div class='col-2'>";
                print "<label class='form-label'>Display Present Caption [P]:</label><select name='lstCaptionP' class='form-select select2 shadow-none'><option selected value='" . $lstCaptionP . "'>" . $lstCaptionP . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select>";
                print "</div>";
                print "<div class='col-2'>";
                print "<label class='form-label'>Display Pre Flags:</label><select name='lstCaptionPreFlag' class='form-select select2 shadow-none'><option selected value='" . $lstCaptionPreFlag . "'>" . $lstCaptionPreFlag . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select>";
                print "</div>";
                print "<div class='col-2'>";
                print "<label class='form-label'>Display Absence Caption [A]:</label><select name='lstCaptionA' class='form-select select2 shadow-none'><option selected value='" . $lstCaptionA . "'>" . $lstCaptionA . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select>";
                print "</div>";
                print "<div class='col-2'>";
                print "<label class='form-label'>Display " . $_SESSION[$session_variable . "IDColumnName"] . " Column:</label><select name='lstCaptionIDNo' class='form-select select2 shadow-none'><option selected value='" . $lstCaptionIDNo . "'>" . $lstCaptionIDNo . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select>";
                print "</div>";
                print "<div class='col-2'>";
                print "<label class='form-label'>Display Overtime [O]:</label><select name='lstCaptionO' class='form-select select2 shadow-none'><option selected value='" . $lstCaptionO . "'>" . $lstCaptionO . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select>";
                print "</div>";
                print "<div class='col-2'>";
                print "<label class='form-label'>Display Department Column:</label><select name='lstCaptionDept' class='form-select select2 shadow-none'><option selected value='" . $lstCaptionDept . "'>" . $lstCaptionDept . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select>";
                print "</div>";
                print "</div>";
                print "<div class='row'>";
                print "<div class='col-2'>";
                print "<label class='form-label'>Display LI/ EO:</label><select name='lstCaptionLIEO' class='form-select select2 shadow-none'><option selected value='" . $lstCaptionLIEO . "'>" . $lstCaptionLIEO . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select>";
                print "</div>";
                print "<div class='col-2'>";
                print "<label class='form-label'>Display Div/Desg Column:</label><select name='lstCaptionDiv' class='form-select select2 shadow-none'><option selected value='" . $lstCaptionDiv . "'>" . $lstCaptionDiv . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select>";
                print "</div>";
                print "<div class='col-2'>";
                print "<label class='form-label'>Display Normal Time [N]:</label><select name='lstCaptionN' class='form-select select2 shadow-none'><option selected value='" . $lstCaptionN . "'>" . $lstCaptionN . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select>";
                print "</div>";
                print "<div class='col-2'>";
                print "<label class='form-label'>Display Remark Column:</label><select name='lstCaptionRemark' class='form-select select2 shadow-none'><option selected value='" . $lstCaptionRemark . "'>" . $lstCaptionRemark . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select>";
                print "</div>";
                print "<div class='col-2'>";
                print "<label class='form-label'>Group By:</label><select name='lstGroupBy' class='form-select select2 shadow-none'><option selected value='" . $lstGroupBy . "'>" . $lstGroupBy . "</option> <option value='Shift'>Shift</option> <option value='Dept'>Dept</option> <option value='Div/Desg'>Div/Desg</option> <option value='Remark'>Remark</option> <option value='" . $_SESSION[$session_variable . "IDColumnName"] . "'>" . $_SESSION[$session_variable . "IDColumnName"] . "</option> <option value='" . $_SESSION[$session_variable . "PhoneColumnName"] . "'>" . $_SESSION[$session_variable . "PhoneColumnName"] . "</option> <option value=''>---</option> </select>";
                print "</div>";
                print "<div class='col-2'>";
                print "<label class='form-label'>Display Shift Column:</label><select name='lstCaptionShift' class='form-select select2 shadow-none'><option selected value='" . $lstCaptionShift . "'>" . $lstCaptionShift . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select>";
                print "</div>";
                print "</div>";
                print "<div class='row'>";
                print "<div class='col-2'>";
                displayEmployeeStatus($conn, "lstEmployeeStatus", $lstEmployeeStatus, "", "");
                print "</div>";
                print "<div class='col-2'>";
                print "<label class='form-label'>Display Total Column:</label><select name='lstCaptionTotal' class='form-select select2 shadow-none'><option selected value='" . $lstCaptionTotal . "'>" . $lstCaptionTotal . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select>";
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
    print '<div class="row"><div class="col-md-2"></div><div class="col-md-10">';
    if ($displayHead) {
        print "<center><p><font face='Verdana' size='1'><b><font size='2'>WKD = Week Day ; PXY = Proxy ; FLG = Flag Day ; SAT = Saturday / OT1 ; SUN = Sunday / OT2 ; TLD = Total Days <br>NS = Night Shift</font> <br>O = Overtime Days ; U = Undertime Days ; N = Normal Days ; P = Present Days ; T = Total Days <br>A = Absent Days ; A/S = Absent excluding Sundays / OT2 ; A/SS = Absent Excluding Saturdays and Sundays / OT1 and OT2";
        if ($prints != "yes") {
            print "<br><br>Click on the Day Record to get the Clocking Details for the selected Period";
        }
        print "</b></font></p></center>";
    }
    print '</div></div>';
    $query = "";
    if ($lstGroupBy == "") {
        if ($lstType == "Early In") {
            $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, AttendanceMaster.group_min, AttendanceMaster.Normal, AttendanceMaster.Grace, AttendanceMaster.Overtime, AttendanceMaster.ADate, tuser.idno, tuser.remark, AttendanceMaster.Flag, AttendanceMaster.Day, AttendanceMaster.NightFlag, AttendanceMaster.OT1, AttendanceMaster.OT2, AttendanceMaster.LateIn, AttendanceMaster.EarlyOut, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, tgroup, AttendanceMaster WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.EmployeeID = tuser.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " AND AttendanceMaster.EarlyIn > 0 ";
        } else {
            if ($lstType == "Late In") {
                $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, AttendanceMaster.group_min, AttendanceMaster.Normal, AttendanceMaster.Grace, AttendanceMaster.Overtime, AttendanceMaster.ADate, tuser.idno, tuser.remark, AttendanceMaster.Flag, AttendanceMaster.Day, AttendanceMaster.NightFlag, AttendanceMaster.OT1, AttendanceMaster.OT2, AttendanceMaster.LateIn, AttendanceMaster.EarlyOut, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, tgroup, AttendanceMaster WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.EmployeeID = tuser.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " AND AttendanceMaster.LateIn > 0 ";
            } else {
                if ($lstType == "Less Break") {
                    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, AttendanceMaster.group_min, AttendanceMaster.Normal, AttendanceMaster.Grace, AttendanceMaster.Overtime, AttendanceMaster.ADate, tuser.idno, tuser.remark, AttendanceMaster.Flag, AttendanceMaster.Day, AttendanceMaster.NightFlag, AttendanceMaster.OT1, AttendanceMaster.OT2, AttendanceMaster.LateIn, AttendanceMaster.EarlyOut, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, tgroup, AttendanceMaster WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.EmployeeID = tuser.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " AND AttendanceMaster.LessBreak > 0 ";
                } else {
                    if ($lstType == "More Break") {
                        $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, AttendanceMaster.group_min, AttendanceMaster.Normal, AttendanceMaster.Grace, AttendanceMaster.Overtime, AttendanceMaster.ADate, tuser.idno, tuser.remark, AttendanceMaster.Flag, AttendanceMaster.Day, AttendanceMaster.NightFlag, AttendanceMaster.OT1, AttendanceMaster.OT2, AttendanceMaster.LateIn, AttendanceMaster.EarlyOut, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, tgroup, AttendanceMaster WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.EmployeeID = tuser.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " AND AttendanceMaster.MoreBreak > 0 ";
                    } else {
                        if ($lstType == "Early Out") {
                            $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, AttendanceMaster.group_min, AttendanceMaster.Normal, AttendanceMaster.Grace, AttendanceMaster.Overtime, AttendanceMaster.ADate, tuser.idno, tuser.remark, AttendanceMaster.Flag, AttendanceMaster.Day, AttendanceMaster.NightFlag, AttendanceMaster.OT1, AttendanceMaster.OT2, AttendanceMaster.LateIn, AttendanceMaster.EarlyOut, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, tgroup, AttendanceMaster WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.EmployeeID = tuser.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " AND AttendanceMaster.EarlyOut > 0 ";
                        } else {
                            if ($lstType == "Late Out") {
                                $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, AttendanceMaster.group_min, AttendanceMaster.Normal, AttendanceMaster.Grace, AttendanceMaster.Overtime, AttendanceMaster.ADate, tuser.idno, tuser.remark, AttendanceMaster.Flag, AttendanceMaster.Day, AttendanceMaster.NightFlag, AttendanceMaster.OT1, AttendanceMaster.OT2, AttendanceMaster.LateIn, AttendanceMaster.EarlyOut, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, tgroup, AttendanceMaster WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.EmployeeID = tuser.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " AND AttendanceMaster.LateOut > 0 ";
                            } else {
                                if ($lstType == "Grace") {
                                    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, AttendanceMaster.group_min, AttendanceMaster.Normal, AttendanceMaster.Grace, AttendanceMaster.Overtime, AttendanceMaster.ADate, tuser.idno, tuser.remark, AttendanceMaster.Flag, AttendanceMaster.Day, AttendanceMaster.NightFlag, AttendanceMaster.OT1, AttendanceMaster.OT2, AttendanceMaster.LateIn, AttendanceMaster.EarlyOut, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, tgroup, AttendanceMaster WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.EmployeeID = tuser.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " AND AttendanceMaster.Grace > 0 ";
                                } else {
                                    if ($lstType == "OT") {
                                        $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, AttendanceMaster.group_min, AttendanceMaster.Normal, AttendanceMaster.Grace, AttendanceMaster.Overtime, AttendanceMaster.ADate, tuser.idno, tuser.remark, AttendanceMaster.Flag, AttendanceMaster.Day, AttendanceMaster.NightFlag, AttendanceMaster.OT1, AttendanceMaster.OT2, AttendanceMaster.LateIn, AttendanceMaster.EarlyOut, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, tgroup, AttendanceMaster WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.EmployeeID = tuser.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " AND AttendanceMaster.Overtime > 0 ";
                                    } else {
                                        if ($lstType == "Approved OT") {
                                            $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, AttendanceMaster.group_min, AttendanceMaster.Normal, AttendanceMaster.Grace, AttendanceMaster.Overtime, AttendanceMaster.ADate, tuser.idno, tuser.remark, AttendanceMaster.Flag, AttendanceMaster.Day, AttendanceMaster.NightFlag, AttendanceMaster.OT1, AttendanceMaster.OT2, AttendanceMaster.LateIn, AttendanceMaster.EarlyOut, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, tgroup, AttendanceMaster WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.EmployeeID = tuser.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " AND AttendanceMaster.AOvertime > 0 ";
                                        } else {
                                            if ($lstType == "Night Shift") {
                                                $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, AttendanceMaster.group_min, AttendanceMaster.Normal, AttendanceMaster.Grace, AttendanceMaster.Overtime, AttendanceMaster.ADate, tuser.idno, tuser.remark, AttendanceMaster.Flag, AttendanceMaster.Day, AttendanceMaster.NightFlag, AttendanceMaster.OT1, AttendanceMaster.OT2, AttendanceMaster.LateIn, AttendanceMaster.EarlyOut, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, tgroup, AttendanceMaster WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.EmployeeID = tuser.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " AND AttendanceMaster.NightFlag = 1 ";
                                            } else {
                                                if ($lstType == "Day Shift") {
                                                    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, AttendanceMaster.group_min, AttendanceMaster.Normal, AttendanceMaster.Grace, AttendanceMaster.Overtime, AttendanceMaster.ADate, tuser.idno, tuser.remark, AttendanceMaster.Flag, AttendanceMaster.Day, AttendanceMaster.NightFlag, AttendanceMaster.OT1, AttendanceMaster.OT2, AttendanceMaster.LateIn, AttendanceMaster.EarlyOut, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, tgroup, AttendanceMaster WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.EmployeeID = tuser.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " AND AttendanceMaster.NightFlag = 0 ";
                                                } else {
                                                    if ($lstType == "Flags") {
                                                        $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, AttendanceMaster.group_min, AttendanceMaster.Normal, AttendanceMaster.Grace, AttendanceMaster.Overtime, AttendanceMaster.ADate, tuser.idno, tuser.remark, AttendanceMaster.Flag, AttendanceMaster.Day, AttendanceMaster.NightFlag, AttendanceMaster.OT1, AttendanceMaster.OT2, AttendanceMaster.LateIn, AttendanceMaster.EarlyOut, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, tgroup, AttendanceMaster WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.EmployeeID = tuser.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " AND AttendanceMaster.Flag <> 'Black' AND AttendanceMaster.Flag <> 'Proxy' ";
                                                    } else {
                                                        $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, AttendanceMaster.group_min, AttendanceMaster.Normal, AttendanceMaster.Grace, AttendanceMaster.Overtime, AttendanceMaster.ADate, tuser.idno, tuser.remark, AttendanceMaster.Flag, AttendanceMaster.Day, AttendanceMaster.NightFlag, AttendanceMaster.OT1, AttendanceMaster.OT2, AttendanceMaster.LateIn, AttendanceMaster.EarlyOut, DayMaster.Start, DayMaster.Close, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, tgroup, AttendanceMaster, DayMaster WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.EmployeeID = tuser.id AND AttendanceMaster.ADate = DayMaster.TDate AND AttendanceMaster.EmployeeID = DayMaster.e_id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
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
            }
        }
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
        $query = $query . " ORDER BY " . $lstSort;
    } else {
        if ($lstGroupBy == $_SESSION[$session_variable . "IDColumnName"]) {
            $query = "SELECT DISTINCT(idno) FROM tuser WHERE LENGTH(idno) > 0 ORDER BY idno";
        } else {
            if ($lstGroupBy == "Dept") {
                $query = "SELECT DISTINCT(dept) FROM tuser WHERE LENGTH(dept) > 0 ORDER BY dept";
            } else {
                if ($lstGroupBy == "Div/Desg") {
                    $query = "SELECT DISTINCT(company) FROM tuser WHERE LENGTH(company) > 0 ORDER BY company";
                } else {
                    if ($lstGroupBy == "Rmk") {
                        $query = "SELECT DISTINCT(remark) FROM tuser WHERE LENGTH(remark) > 0 ORDER BY remark";
                    } else {
                        if ($lstGroupBy == "Shift") {
                            $query = "SELECT id, name FROM tgroup WHERE id > 1 ORDER BY name ";
                        }
                    }
                }
            }
        }
    }
    $dayCount = getTotalDays($txtFrom, $txtTo);
    print '<div class="row"><div class="col-md-12"><div class="card"><div class="card-body table-responsive">';
    print "<table border=1 cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%' class='table table-striped table-bordered dataTable' id='zero_config'><thead><tr>";
    if ($lstGroupBy == "") {
        print "<td><font face='Verdana' size='" . $txtFontSize . "'>ID</font></td> <td><font face='Verdana' size='" . $txtFontSize . "'>Name</font></td>";
    }
    if ($lstCaptionPreFlag == "Yes") {
        $userrdsselection = $userrdsselection . "--PreFlag";
    }
    if ($lstCaptionIDNo == "Yes" || $lstGroupBy == $_SESSION[$session_variable . "IDColumnName"]) {
        print "<td><font face='Verdana' size='" . $txtFontSize . "'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td>";
        $userrdsselection = $userrdsselection . "--IDNo";
    } else {
        $colcount++;
    }
    if ($lstCaptionDept == "Yes" || $lstGroupBy == "Dept") {
        print "<td><font face='Verdana' size='" . $txtFontSize . "'>Dept</font></td>";
        $userrdsselection = $userrdsselection . "--Dept";
    } else {
        $colcount++;
    }
    if ($lstCaptionDiv == "Yes" || $lstGroupBy == "Div/Desg") {
        print "<td><font face='Verdana' size='" . $txtFontSize . "'>Div/Desg</font></td>";
        $userrdsselection = $userrdsselection . "--Div";
    } else {
        $colcount++;
    }
    if ($lstCaptionRemark == "Yes" || $lstGroupBy == "Rmk") {
        print "<td><font face='Verdana' size='" . $txtFontSize . "'>Rmk</font></td>";
        $userrdsselection = $userrdsselection . "--Remark";
    } else {
        $colcount++;
    }
    if ($lstCaptionShift == "Yes" || $lstGroupBy == "Shift") {
        print "<td><font face='Verdana' size='" . $txtFontSize . "'>Shift</font></td>";
        $userrdsselection = $userrdsselection . "--Shift";
    } else {
        $colcount++;
    }
    for ($i = 0; $i < $dayCount; $i++) {
        $next = strtotime(substr($txtFrom, 0, 2) . "-" . substr($txtFrom, 3, 2) . "-" . substr($txtFrom, 6, 4) . " + " . $i . " day");
        $a = getDate($next);
        print "<td width='" . $txtColumnWidth . "'><font face='Verdana' size='" . $txtFontSize . "'>" . $a["mday"] . "</font></td>";
    }
    if ($lstCaptionTotal == "Yes") {
        print "<td align='center'><font face='Verdana' size='" . $txtFontSize . "'><b>TD</b></font></td> <td align='center'><font face='Verdana' size='" . $txtFontSize . "'><b>WKD</b></font></td> <td align='center'><font face='Verdana' size='" . $txtFontSize . "'><b>PXY</b></font></td> <td align='center'><font face='Verdana' size='" . $txtFontSize . "'><b>FLG</b></font></td> <td align='center'><font face='Verdana' size='" . $txtFontSize . "'><b>SAT</b></font></td> <td align='center'><font face='Verdana' size='" . $txtFontSize . "'><b>SUN</b></font></td>";
        if (strpos($ras, "-V-") !== false) {
            print "<td><font face='Verdana' size='2' color='Violet'><b>V</b></font></td>";
        }
        if (strpos($ras, "-I-") !== false) {
            print "<td><font face='Verdana' size='2' color='Indigo'><b>I</b></font></td>";
        }
        if (strpos($ras, "-B-") !== false) {
            print "<td><font face='Verdana' size='2' color='Blue'><b>B</b></font></td>";
        }
        if (strpos($ras, "-G-") !== false) {
            print "<td><font face='Verdana' size='2' color='Green'><b>G</b></font></td>";
        }
        if (strpos($ras, "-Y-") !== false) {
            print "<td bgcolor='Brown'><font face='Verdana' size='2' color='Yellow'><b>Y</b></font></td>";
        }
        if (strpos($ras, "-O-") !== false) {
            print "<td><font face='Verdana' size='2' color='Orange'><b>O</b></font></td>";
        }
        if (strpos($ras, "-R-") !== false) {
            print "<td><font face='Verdana' size='2' color='Red'><b>R</b></font></td>";
        }
        if (strpos($ras, "-GR-") !== false) {
            print "<td><font face='Verdana' size='2' color='Gray'><b>GR</b></font></td>";
        }
        if (strpos($ras, "-BR-") !== false) {
            print "<td><font face='Verdana' size='2' color='Brown'><b>BR</b></font></td>";
        }
        if (strpos($ras, "-PR-") !== false) {
            print "<td><font face='Verdana' size='2' color='Purple'><b>PR</b></font></td>";
        }
        if (strpos($ras, "-MG-") !== false) {
            print "<td><font face='Verdana' size='2' color='Magenta'><b>MG</b></font></td>";
        }
        if (strpos($ras, "-TL-") !== false) {
            print "<td><font face='Verdana' size='2' color='Teal'><b>TL</b></font></td>";
        }
        if (strpos($ras, "-AQ-") !== false) {
            print "<td><font face='Verdana' size='2' color='Aqua'><b>AQ</b></font></td>";
        }
        if (strpos($ras, "-SF-") !== false) {
            print "<td><font face='Verdana' size='2' color='Safron'><b>SF</b></font></td>";
        }
        if (strpos($ras, "-AM-") !== false) {
            print "<td><font face='Verdana' size='2' color='Amber'><b>AM</b></font></td>";
        }
        if (strpos($ras, "-GL-") !== false) {
            print "<td><font face='Verdana' size='2' color='Golden'><b>GL</b></font></td>";
        }
        if (strpos($ras, "-VM-") !== false) {
            print "<td><font face='Verdana' size='2' color='Vermilion'><b>VM</b></font></td>";
        }
        if (strpos($ras, "-SL-") !== false) {
            print "<td><font face='Verdana' size='2' color='Silver'><b>SL</b></font></td>";
        }
        if (strpos($ras, "-MR-") !== false) {
            print "<td><font face='Verdana' size='2' color='Maroon'><b>MR</b></font></td>";
        }
        if (strpos($ras, "-PK-") !== false) {
            print "<td><font face='Verdana' size='2' color='Pink'><b>PK</b></font></td>";
        }
        if ($lstType == "" && $lstGroupBy == "") {
            print "<td colspan='4' align='center' bgcolor='#F0F0F0'><font face='Verdana' size='" . $txtFontSize . "'><b>TLD</b></font></td>";
        } else {
            print "<td align='center' bgcolor='#F0F0F0'><font face='Verdana' size='" . $txtFontSize . "'><b>TLD</b></font></td>";
        }
        print "<td align='center'><font face='Verdana' size='" . $txtFontSize . "'><b>NS</b></font></td>";
        $userrdsselection = $userrdsselection . "--Total";
    }
    print "</tr></thead>";
    print "<tr>";
    if ($lstGroupBy == "") {
        if ($lstType == "ACard") {
            substr($txtFrom, 3, 10);
            print "<td><font face='Verdana' size='" . $txtFontSize . "'><b>" . substr($txtFrom, 3, 10) . "</b></font></td>";
            print "<td><font face='Verdana' size='" . $txtFontSize . "'><b>" . $lstDepartment . "</b></font></td>";
            for ($i = 0; $i < 5 - $colcount; $i++) {
                print "<td><font face='Verdana' size='" . $txtFontSize . "'>&nbsp;</font></td>";
            }
        } else {
            for ($i = 0; $i < 7 - $colcount; $i++) {
                print "<td><font face='Verdana' size='" . $txtFontSize . "'>&nbsp;</font></td>";
            }
        }
    } else {
        if ($lstType == "ACard") {
            substr(displayDate($txtFrom), 3, 10);
            print "<td><font face='Verdana' size='" . $txtFontSize . "'><b>" . substr(displayDate($txtFrom), 3, 10) . "</b></font></td>";
            print "<td><font face='Verdana' size='" . $txtFontSize . "'><b>" . $lstDepartment . "</b></font></td>";
            for ($i = 0; $i < 3 - $colcount; $i++) {
                print "<td><font face='Verdana' size='" . $txtFontSize . "'>&nbsp;</font></td>";
            }
        } else {
            for ($i = 0; $i < 5 - $colcount; $i++) {
                print "<td><font face='Verdana' size='" . $txtFontSize . "'>&nbsp;</font></td>";
            }
        }
    }
    for ($i = 0; $i < $dayCount; $i++) {
        $next = strtotime(substr($txtFrom, 0, 2) . "-" . substr($txtFrom, 3, 2) . "-" . substr($txtFrom, 6, 4) . " + " . $i . " day");
        $a = getDate($next);
        substr($a["weekday"], 0, 1);
        print "<td><a title='" . $a["weekday"] . "'><font face='Verdana' size='" . $txtFontSize . "'>" . substr($a["weekday"], 0, 1) . "</font></a></td>";
    }
    if ($lstCaptionTotal == "Yes") {
        print "<td><font face='Verdana' size='1'><b>&nbsp;</b></font></td> <td><font face='Verdana' size='1'><b>&nbsp;</b></font></td> <td><font face='Verdana' size='1'><b>&nbsp;</b></font></td> <td><font face='Verdana' size='1'><b>&nbsp;</b></font></td> <td><font face='Verdana' size='1'><b>&nbsp;</b></font></td> <td><font face='Verdana' size='1'><b>&nbsp;</b></font></td>";
        if (strpos($ras, "-V-") !== false) {
            print "<td><font face='Verdana' size='2' color='Violet'>&nbsp;</font></td>";
        }
        if (strpos($ras, "-I-") !== false) {
            print "<td><font face='Verdana' size='2' color='Indigo'>&nbsp;</font></td>";
        }
        if (strpos($ras, "-B-") !== false) {
            print "<td><font face='Verdana' size='2' color='Blue'>&nbsp;</font></td>";
        }
        if (strpos($ras, "-G-") !== false) {
            print "<td><font face='Verdana' size='2' color='Green'>&nbsp;</font></td>";
        }
        if (strpos($ras, "-Y-") !== false) {
            print "<td bgcolor='Brown'><font face='Verdana' size='2' color='Yellow'>&nbsp;</font></td>";
        }
        if (strpos($ras, "-O-") !== false) {
            print "<td><font face='Verdana' size='2' color='Orange'>&nbsp;</font></td>";
        }
        if (strpos($ras, "-R-") !== false) {
            print "<td><font face='Verdana' size='2' color='Red'>&nbsp;</font></td>";
        }
        if (strpos($ras, "-GR-") !== false) {
            print "<td><font face='Verdana' size='2' color='Gray'>&nbsp;</font></td>";
        }
        if (strpos($ras, "-BR-") !== false) {
            print "<td><font face='Verdana' size='2' color='Brown'>&nbsp;</font></td>";
        }
        if (strpos($ras, "-PR-") !== false) {
            print "<td><font face='Verdana' size='2' color='Purple'>&nbsp;</font></td>";
        }
        if (strpos($ras, "-MG-") !== false) {
            print "<td><font face='Verdana' size='2' color='Magenta'>&nbsp;</font></td>";
        }
        if (strpos($ras, "-TL-") !== false) {
            print "<td><font face='Verdana' size='2' color='Teal'>&nbsp;</font></td>";
        }
        if (strpos($ras, "-AQ-") !== false) {
            print "<td><font face='Verdana' size='2' color='Aqua'>&nbsp;</font></td>";
        }
        if (strpos($ras, "-SF-") !== false) {
            print "<td><font face='Verdana' size='2' color='Safron'>&nbsp;</font></td>";
        }
        if (strpos($ras, "-AM-") !== false) {
            print "<td><font face='Verdana' size='2' color='Amber'>&nbsp;</font></td>";
        }
        if (strpos($ras, "-GL-") !== false) {
            print "<td><font face='Verdana' size='2' color='Golden'>&nbsp;</font></td>";
        }
        if (strpos($ras, "-VM-") !== false) {
            print "<td><font face='Verdana' size='2' color='Vermilion'>&nbsp;</font></td>";
        }
        if (strpos($ras, "-SL-") !== false) {
            print "<td><font face='Verdana' size='2' color='Silver'>&nbsp;</font></td>";
        }
        if (strpos($ras, "-MR-") !== false) {
            print "<td><font face='Verdana' size='2' color='Maroon'>&nbsp;</font></td>";
        }
        if (strpos($ras, "-PK-") !== false) {
            print "<td><font face='Verdana' size='2' color='Pink'>&nbsp;</font></td>";
        }
        if ($lstType == "" && $lstGroupBy == "") {
            print "<td bgcolor='#F0F0F0'><font face='Verdana' size='1'><b>P</b></font></td> <td bgcolor='#F0F0F0'><font face='Verdana' size='1'><b>A</b></font></td> <td bgcolor='#F0F0F0'><font face='Verdana' size='1'><b>A/S</b></font></td> <td bgcolor='#F0F0F0'><font face='Verdana' size='1'><b>A/SS</b></font></td>";
        } else {
            print "<td bgcolor='#F0F0F0'><font face='Verdana' size='1'><b>&nbsp;</b></font></td>";
        }
        print "<td><font face='Verdana' size='1'><b>&nbsp;</b></font></td>";
    }
    print "</tr>";
    $row_count = 0;
    $count = 0;
    $subc = 0;
    $eid = "";
    $wkdn = 0;
    $wkdo = 0;
    $pxyn = 0;
    $pxyo = 0;
    $flgn = 0;
    $flgo = 0;
    $satn = 0;
    $sato = 0;
    $sunn = 0;
    $suno = 0;
    $nsn = 0;
    $nso = 0;
    $nfn = 0;
    $nfo = 0;
    $tn = 0;
    $to = 0;
    $satabn = 0;
    $satabo = 0;
    $sunabn = 0;
    $sunabo = 0;
    $txtDate = insertDate($txtFrom);
    $txtLastDate = insertDate($txtFrom);
    $data0 = "";
    $data9 = "";
    $caption = "";
    $satCount = 0;
    $sunCount = 0;
    $result = mysqli_query($conn, $query);
    if ($lstGroupBy == "") {
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
                        if ($lstCaptionPreFlag == "Yes") {
                            $next = strtotime(substr($txtFrom, 0, 2) . "-" . substr($txtFrom, 3, 2) . "-" . substr($txtFrom, 6, 4) . " + " . $i . " day");
                            $a = getDate($next);
                            $this_date = $a["year"] . "" . addZero($a["mon"], 2) . "" . addZero($a["mday"], 2);
                            $caption = preFlagTitle($conn, $data0, $this_date);
                        } else {
                            $caption = "";
                        }
                        if ($lstCaptionA == "Yes" && $caption == "") {
                            $caption = "A";
                        } else {
                            if ($caption == "") {
                                $caption = "&nbsp;";
                            }
                        }
                        if ($lstType != "ACard") {
                            print "<td><a title='" . ($i + 1) . "'><font face='Verdana' size='" . $txtFontSize . "'>" . $caption . "</font></a></td>";
                        } else {
                            print "<td align='center'><a title='" . ($i + 1) . "'><font face='Verdana' size='" . $txtFontSize . "'><br><b>___</b><br><br><br></font></a></td>";
                        }
                    }
                    if ($lstCaptionTotal == "Yes") {
                        print "<td><a title='Total Days'><font face='Verdana' size='" . $txtFontSize . "'><b>" . $dayCount . "</b></font></a></td> <td><a title='Week Days'><font face='Verdana' size='" . $txtFontSize . "'><b>" . ($wkdn + $wkdo) . "</b></font></a></td> <td><a title='Proxy Days'><font face='Verdana' size='" . $txtFontSize . "'><b>" . ($pxyn + $pxyo) . "</b></font></a></td> <td><a title='Flag Days'><font face='Verdana' size='" . $txtFontSize . "'><b>" . ($flgn + $flgo) . "</b></font></a></td> <td><a title='Saturdays'><font face='Verdana' size='" . $txtFontSize . "'><b>" . ($satn + $sato) . "</b></font></a></td> <td><a title='Sundays'><font face='Verdana' size='" . $txtFontSize . "'><b>" . ($sunn + $suno) . "</b></font></a></td>";
                        if (strpos($ras, "-V-") !== false) {
                            flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Violet", $txtColumnWidth, $txtFontSize, $v_group, $eid);
                        }
                        if (strpos($ras, "-I-") !== false) {
                            flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Indigo", $txtColumnWidth, $txtFontSize, $v_group, $eid);
                        }
                        if (strpos($ras, "-B-") !== false) {
                            flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Blue", $txtColumnWidth, $txtFontSize, $v_group, $eid);
                        }
                        if (strpos($ras, "-G-") !== false) {
                            flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Green", $txtColumnWidth, $txtFontSize, $v_group, $eid);
                        }
                        if (strpos($ras, "-Y-") !== false) {
                            flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Yellow", $txtColumnWidth, $txtFontSize, $v_group, $eid);
                        }
                        if (strpos($ras, "-O-") !== false) {
                            flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Orange", $txtColumnWidth, $txtFontSize, $v_group, $eid);
                        }
                        if (strpos($ras, "-R-") !== false) {
                            flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Red", $txtColumnWidth, $txtFontSize, $v_group, $eid);
                        }
                        if (strpos($ras, "-GR-") !== false) {
                            flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Gray", $txtColumnWidth, $txtFontSize, $v_group, $eid);
                        }
                        if (strpos($ras, "-BR-") !== false) {
                            flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Brown", $txtColumnWidth, $txtFontSize, $v_group, $eid);
                        }
                        if (strpos($ras, "-PR-") !== false) {
                            flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Purple", $txtColumnWidth, $txtFontSize, $v_group, $eid);
                        }
                        if (strpos($ras, "-MG-") !== false) {
                            flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Magenta", $txtColumnWidth, $txtFontSize, $v_group, $eid);
                        }
                        if (strpos($ras, "-TL-") !== false) {
                            flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Teal", $txtColumnWidth, $txtFontSize, $v_group, $eid);
                        }
                        if (strpos($ras, "-AQ-") !== false) {
                            flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Aqua", $txtColumnWidth, $txtFontSize, $v_group, $eid);
                        }
                        if (strpos($ras, "-SF-") !== false) {
                            flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Safron", $txtColumnWidth, $txtFontSize, $v_group, $eid);
                        }
                        if (strpos($ras, "-AM-") !== false) {
                            flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Amber", $txtColumnWidth, $txtFontSize, $v_group, $eid);
                        }
                        if (strpos($ras, "-GL-") !== false) {
                            flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Gold", $txtColumnWidth, $txtFontSize, $v_group, $eid);
                        }
                        if (strpos($ras, "-VM-") !== false) {
                            flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Vermilon", $txtColumnWidth, $txtFontSize, $v_group, $eid);
                        }
                        if (strpos($ras, "-SL-") !== false) {
                            flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Silver", $txtColumnWidth, $txtFontSize, $v_group, $eid);
                        }
                        if (strpos($ras, "-MR-") !== false) {
                            flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Maroon", $txtColumnWidth, $txtFontSize, $v_group, $eid);
                        }
                        if (strpos($ras, "-PK-") !== false) {
                            flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Pink", $txtColumnWidth, $txtFontSize, $v_group, $eid);
                        }
                        if ($lstType == "") {
                            print "<td bgcolor='#F0F0F0'><a title ='Total with Normal + OT Days'><font face='Verdana' size='" . $txtFontSize . "'><b>" . ($wkdn + $pxyn + $flgn + $satn + $sunn + $wkdo + $pxyo + $flgo + $sato + $suno) . "</b></font></a></td> <td bgcolor='#F0F0F0'><a title='Total Days Absent'><font face='Verdana' size='" . $txtFontSize . "'><b>" . ($dayCount - ($wkdn + $pxyn + $flgn + $satn + $sunn + $wkdo + $pxyo + $flgo + $sato + $suno)) . "</b></font></a></td> <td bgcolor='#F0F0F0'><a title='Total Days Absent EXCLUDING Sundays'><font face='Verdana' size='" . $txtFontSize . "'><b>" . ($dayCount + $satabo + $satabn - $sunCount - ($wkdn + $pxyn + $flgn + $satn + $wkdo + $pxyo + $flgo + $sato)) . "</b></font></a></td> <td bgcolor='#F0F0F0'><a title='Total Days Absent EXCLUDING Saturdays and Sundays'><font face='Verdana' size='" . $txtFontSize . "'><b>" . ($dayCount + $sunabo + $sunabn + $satabo + $satabn - ($satCount + $sunCount) - ($wkdn + $pxyn + $flgn + $wkdo + $pxyo + $flgo)) . "</b></font></a></td>";
                        } else {
                            print "<td bgcolor='#F0F0F0'><a title ='Total with Normal + OT Days'><font face='Verdana' size='" . $txtFontSize . "'><b>" . ($wkdn + $pxyn + $flgn + $satn + $sunn + $wkdo + $pxyo + $flgo + $sato + $suno) . "</b></font></a></td>";
                        }
                        print "<td><a title='Night Shifts with Normal + OT Days'><font face='Verdana' size='" . $txtFontSize . "'><b>" . ($nsn + $nso) . "</b></font></a></td>";
                    }
                    print "</tr>";
                    $row_count++;
                }
                if ($lstCaptionTotal == "Yes") {
                    $ot_query = "SELECT OT1, OT2 FROM AttendanceMaster WHERE EmployeeID = " . $cur[0] . " AND ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo);
                    $ot_result = selectData($conn, $ot_query);
                    $satCount = getDayCount(insertDate($txtFrom), insertDate($txtTo), $dayCount, $ot_result[0]);
                    $sunCount = getDayCount(insertDate($txtFrom), insertDate($txtTo), $dayCount, $ot_result[1]);
                }
                if (($row_count % $txtHeaderBreak == 0 || $row_count == $first_page_break) && $prints == "yes" && $excel != "yes" && $row_count != 0) {
                    print "</table><div style='page-break-before: always;'></div><table width='100%' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' border='1' bordercolor='#C0C0C0'>";
                    print "<tr style='page-break-before:always;'><td><font face='Verdana' size='" . $txtFontSize . "'>ID</font></td> <td><font face='Verdana' size='" . $txtFontSize . "'>Name</font></td>";
                    if ($lstCaptionIDNo == "Yes") {
                        print "<td><font face='Verdana' size='" . $txtFontSize . "'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td>";
                    }
                    if ($lstCaptionDept == "Yes") {
                        print "<td><font face='Verdana' size='" . $txtFontSize . "'>Dept</font></td>";
                    }
                    if ($lstCaptionDiv == "Yes") {
                        print "<td><font face='Verdana' size='" . $txtFontSize . "'>Div/Desg</font></td>";
                    }
                    if ($lstCaptionRemark == "Yes") {
                        print "<td><font face='Verdana' size='" . $txtFontSize . "'>Rmk</font></td>";
                    }
                    if ($lstCaptionShift == "Yes") {
                        print "<td><font face='Verdana' size='" . $txtFontSize . "'>Shift</font></td>";
                    }
                    for ($i = 0; $i < $dayCount; $i++) {
                        $next = strtotime(substr($txtFrom, 0, 2) . "-" . substr($txtFrom, 3, 2) . "-" . substr($txtFrom, 6, 4) . " + " . $i . " day");
                        $a = getDate($next);
                        print "<td width='" . $txtColumnWidth . "'><font face='Verdana' size='" . $txtFontSize . "'>" . $a["mday"] . "</font></td>";
                    }
                    if ($lstCaptionTotal == "Yes") {
                        print "<td align='center'><font face='Verdana' size='" . $txtFontSize . "'><b>TD</b></font></td> <td align='center'><font face='Verdana' size='" . $txtFontSize . "'><b>WKD</b></font></td> <td align='center'><font face='Verdana' size='" . $txtFontSize . "'><b>PXY</b></font></td> <td align='center'><font face='Verdana' size='" . $txtFontSize . "'><b>FLG</b></font></td> <td align='center'><font face='Verdana' size='" . $txtFontSize . "'><b>SAT</b></font></td> <td align='center'><font face='Verdana' size='" . $txtFontSize . "'><b>SUN</b></font></td>";
                        if ($lstType == "") {
                            print "<td colspan='4' align='center' bgcolor='#F0F0F0'><font face='Verdana' size='" . $txtFontSize . "'><b>TLD</b></font></td>";
                        } else {
                            print "<td align='center' bgcolor='#F0F0F0'><font face='Verdana' size='" . $txtFontSize . "'><b>TLD</b></font></td>";
                        }
                        print "<td align='center'><font face='Verdana' size='" . $txtFontSize . "'><b>NS</b></font></td> <td align='center'><font face='Verdana' size='" . $txtFontSize . "'><b>NF</b></font></td> <td align='center' bgcolor='#F0F0F0'><font face='Verdana' size='" . $txtFontSize . "'><b>TND</b></font></td>";
                        $userrdsselection = $userrdsselection . "--Total";
                    }
                    print "</tr>";
                    print "<tr>";
                    if ($lstType == "ACard") {
                        substr($txtFrom, 3, 10);
                        print "<td><font face='Verdana' size='" . $txtFontSize . "'><b>" . substr($txtFrom, 3, 10) . "</b></font></td>";
                        print "<td><font face='Verdana' size='" . $txtFontSize . "'><b>" . $lstDepartment . "</b></font></td>";
                        for ($i = 0; $i < 5 - $colcount; $i++) {
                            print "<td><font face='Verdana' size='" . $txtFontSize . "'>&nbsp;</font></td>";
                        }
                    } else {
                        for ($i = 0; $i < 7 - $colcount; $i++) {
                            print "<td><font face='Verdana' size='" . $txtFontSize . "'>&nbsp;</font></td>";
                        }
                    }
                    for ($i = 0; $i < $dayCount; $i++) {
                        $next = strtotime(substr($txtFrom, 0, 2) . "-" . substr($txtFrom, 3, 2) . "-" . substr($txtFrom, 6, 4) . " + " . $i . " day");
                        $a = getDate($next);
                        substr($a["weekday"], 0, 1);
                        print "<td><a title='" . $a["weekday"] . "'><font face='Verdana' size='" . $txtFontSize . "'>" . substr($a["weekday"], 0, 1) . "</font></a></td>";
                    }
                    if ($lstCaptionTotal == "Yes") {
                        print "<td><font face='Verdana' size='1'><b>&nbsp;</b></font></td> <td><font face='Verdana' size='1'><b>&nbsp;</b></font></td> <td><font face='Verdana' size='1'><b>&nbsp;</b></font></td> <td><font face='Verdana' size='1'><b>&nbsp;</b></font></td> <td><font face='Verdana' size='1'><b>&nbsp;</b></font></td> <td><font face='Verdana' size='1'><b>&nbsp;</b></font></td>";
                        if (strpos($ras, "-V-") !== false) {
                            print "<td><font face='Verdana' size='2' color='Violet'>&nbsp;</font></td>";
                        }
                        if (strpos($ras, "-I-") !== false) {
                            print "<td><font face='Verdana' size='2' color='Indigo'>&nbsp;</font></td>";
                        }
                        if (strpos($ras, "-B-") !== false) {
                            print "<td><font face='Verdana' size='2' color='Blue'>&nbsp;</font></td>";
                        }
                        if (strpos($ras, "-G-") !== false) {
                            print "<td><font face='Verdana' size='2' color='Green'>&nbsp;</font></td>";
                        }
                        if (strpos($ras, "-Y-") !== false) {
                            print "<td bgcolor='Brown'><font face='Verdana' size='2' color='Yellow'>&nbsp;</font></td>";
                        }
                        if (strpos($ras, "-O-") !== false) {
                            print "<td><font face='Verdana' size='2' color='Orange'>&nbsp;</font></td>";
                        }
                        if (strpos($ras, "-R-") !== false) {
                            print "<td><font face='Verdana' size='2' color='Red'>&nbsp;</font></td>";
                        }
                        if (strpos($ras, "-GR-") !== false) {
                            print "<td><font face='Verdana' size='2' color='Gray'>&nbsp;</font></td>";
                        }
                        if (strpos($ras, "-BR-") !== false) {
                            print "<td><font face='Verdana' size='2' color='Brown'>&nbsp;</font></td>";
                        }
                        if (strpos($ras, "-PR-") !== false) {
                            print "<td><font face='Verdana' size='2' color='Purple'>&nbsp;</font></td>";
                        }
                        if (strpos($ras, "-MG-") !== false) {
                            print "<td><font face='Verdana' size='2' color='Magenta'>&nbsp;</font></td>";
                        }
                        if (strpos($ras, "-TL-") !== false) {
                            print "<td><font face='Verdana' size='2' color='Teal'>&nbsp;</font></td>";
                        }
                        if (strpos($ras, "-AQ-") !== false) {
                            print "<td><font face='Verdana' size='2' color='Aqua'>&nbsp;</font></td>";
                        }
                        if (strpos($ras, "-SF-") !== false) {
                            print "<td><font face='Verdana' size='2' color='Safron'>&nbsp;</font></td>";
                        }
                        if (strpos($ras, "-AM-") !== false) {
                            print "<td><font face='Verdana' size='2' color='Amber'>&nbsp;</font></td>";
                        }
                        if (strpos($ras, "-GL-") !== false) {
                            print "<td><font face='Verdana' size='2' color='Golden'>&nbsp;</font></td>";
                        }
                        if (strpos($ras, "-VM-") !== false) {
                            print "<td><font face='Verdana' size='2' color='Vermilion'>&nbsp;</font></td>";
                        }
                        if (strpos($ras, "-SL-") !== false) {
                            print "<td><font face='Verdana' size='2' color='Silver'>&nbsp;</font></td>";
                        }
                        if (strpos($ras, "-MR-") !== false) {
                            print "<td><font face='Verdana' size='2' color='Maroon'>&nbsp;</font></td>";
                        }
                        if (strpos($ras, "-PK-") !== false) {
                            print "<td><font face='Verdana' size='2' color='Pink'>&nbsp;</font></td>";
                        }
                        if ($lstType == "") {
                            print "<td bgcolor='#F0F0F0'><font face='Verdana' size='1'><b>P</b></font></td> <td bgcolor='#F0F0F0'><font face='Verdana' size='1'><b>A</b></font></td> <td bgcolor='#F0F0F0'><font face='Verdana' size='1'><b>A/S</b></font></td> <td bgcolor='#F0F0F0'><font face='Verdana' size='1'><b>A/SS</b></font></td>";
                        } else {
                            print "<td bgcolor='#F0F0F0'><font face='Verdana' size='1'><b>&nbsp;</b></font></td>";
                        }
                        print "<td><font face='Verdana' size='1'><b>&nbsp;</b></font></td> <td><font face='Verdana' size='1'><b>&nbsp;</b></font></td> <td bgcolor='#F0F0F0'><font face='Verdana' size='1'><b>&nbsp;</b></font></td>";
                    }
                    print "</tr>";
                }
                print "<tr>";
                if ($prints != "yes") {
                    addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
                    print "<td><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'><a title='ID: " . $cur[0] . " - Click to view Clocking Details for the selected Period (ACTIVE EMPLOYEES ONLY)' href='ReportDailyClocking.php?act=searchRecord&prints=yes&excel=yes&subReport=yes&lstEmployeeID=" . $cur[0] . "&txtFrom=" . $txtFrom . "&txtTo=" . $txtTo . "' target='_blank'><font face='Verdana' size='" . $txtFontSize . "' color='#000000'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td>";
                } else {
                    addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
                    print "<td><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'><font face='Verdana' size='" . $txtFontSize . "' color='#000000'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></td>";
                }
                print "<td><a title='Name'><font face='Verdana' size='" . $txtFontSize . "'>" . $cur[1] . "</font></a></td>";
                if ($lstCaptionIDNo == "Yes") {
                    print "<td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='" . $txtFontSize . "'>" . $cur[10] . "</font></a></td>";
                }
                if ($lstCaptionDept == "Yes") {
                    print "<td><a title='Dept'><font face='Verdana' size='" . $txtFontSize . "'>" . $cur[2] . "</font></a></td>";
                }
                if ($lstCaptionDiv == "Yes") {
                    print "<td><a title='Div/Desg'><font face='Verdana' size='" . $txtFontSize . "'>" . $cur[3] . "</font></a></td>";
                }
                if ($lstCaptionRemark == "Yes") {
                    print "<td><a title='Rmk'><font face='Verdana' size='" . $txtFontSize . "'>" . $cur[11] . "</font></a></td>";
                }
                if ($lstCaptionShift == "Yes") {
                    print "<td><a title='Current Shift'><font face='Verdana' size='" . $txtFontSize . "'>" . $cur[4] . "</font></a></td>";
                }
                $eid = $cur[0];
                $subc = 0;
                $wkdn = 0;
                $wkdo = 0;
                $pxyn = 0;
                $pxyo = 0;
                $flgn = 0;
                $flgo = 0;
                $satn = 0;
                $sato = 0;
                $sunn = 0;
                $suno = 0;
                $nsn = 0;
                $nso = 0;
                $nfn = 0;
                $nfo = 0;
                $tn = 0;
                $to = 0;
                $satabn = 0;
                $satabo = 0;
                $sunabn = 0;
                $sunabo = 0;
                $txtDate = insertDate($txtFrom);
                $txtLastDate = insertDate($txtFrom);
            }
            while (true) {
                $subc++;
                if ($cur[9] == $txtDate || $cur[9] == $txtLastDate) {
                    if ($lstCaptionTotal == "Yes") {
                        if ($cur[12] != "Black" && $cur[12] != "Proxy") {
                            if (0 < $cur[8]) {
                                $flgo++;
                                if ($cur[13] == $cur[15]) {
                                    $satabo++;
                                }
                                if ($cur[13] == $cur[16]) {
                                    $sunabo++;
                                }
                            } else {
                                $flgn++;
                                if ($cur[13] == $cur[15]) {
                                    $satabn++;
                                }
                                if ($cur[13] == $cur[16]) {
                                    $sunabn++;
                                }
                            }
                        } else {
                            if ($cur[13] == $cur[15]) {
                                if (0 < $cur[8]) {
                                    $sato++;
                                } else {
                                    $satn++;
                                }
                            } else {
                                if ($cur[13] == $cur[16]) {
                                    if (0 < $cur[8]) {
                                        $suno++;
                                    } else {
                                        $sunn++;
                                    }
                                } else {
                                    if ($cur[12] == "Proxy") {
                                        if (0 < $cur[8]) {
                                            $pxyo++;
                                        } else {
                                            $pxyn++;
                                        }
                                    } else {
                                        if ($cur[12] == "Black") {
                                            if (0 < $cur[8]) {
                                                $wkdo++;
                                            } else {
                                                $wkdn++;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        if ($cur[14] == 1) {
                            if (0 < $cur[8]) {
                                $nso++;
                            } else {
                                $nsn++;
                            }
                        }
                    }
                    if ($lstType != "ACard") {
                        print "<td>";
                        if ($lstCaptionO == "No" && $lstCaptionP == "No" && $lstCaptionN == "No" && $lstCaptionLIEO == "No") {
                            print "<font face='Verdana' color='#000000' size='" . $txtFontSize . "'>";
                        } else {
                            if ($excel != "yes") {
                                displayDate($cur[9]);
                                print "<a title='" . displayDate($cur[9]) . ": Click to view Hourly Details for the selected Period (ACTIVE EMPLOYEES ONLY)' href='ReportWork.php?act=searchRecord&prints=yes&excel=yes&subReport=yes&lstEmployeeID=" . $cur[0] . "&txtFrom=" . $txtFrom . "&txtTo=" . $txtTo . "' target='_blank'><font face='Verdana' color='#000000' size='" . $txtFontSize . "'>";
                            } else {
                                print "<font face='Verdana' color='#000000' size='" . $txtFontSize . "'>";
                            }
                        }
                        $caption = postFlagTitle($conn, $cur[12]);
                        if ($caption == "") {
                            if ($cur[13] == $cur[15] && getRegister($txtMACAddress, 7) != "99") {
                                print "OT1";
                            } else {
                                if ($cur[13] == $cur[15] && getRegister($txtMACAddress, 7) == "99") {
                                    print "P";
                                } else {
                                    if ($cur[13] == $cur[16]) {
                                        print "OT2";
                                    } else {
                                        if (0 < $cur[17] && $lstCaptionLIEO == "Yes") {
                                            print "LI";
                                        } else {
                                            if (0 < $cur[18] && $lstCaptionLIEO == "Yes") {
                                                print "EO";
                                            } else {
                                                if (0 < $cur[8]) {
                                                    if ($lstCaptionO == "Yes") {
                                                        print "O";
                                                    } else {
                                                        if ($lstCaptionP == "Yes") {
                                                            print "P";
                                                        } else {
                                                            displayTime($cur[19]);
                                                            displayTime($cur[20]);
                                                            round(($cur[6] * 1 + $cur[8] * 1) / 3600, 2);
                                                            print "I=" . displayTime($cur[19]) . " <br>O=" . displayTime($cur[20]) . " <br>W=" . round(($cur[6] * 1 + $cur[8] * 1) / 3600, 2);
                                                        }
                                                    }
                                                } else {
                                                    if ($cur[6] == $cur[5] * 60) {
                                                        if ($lstCaptionN == "Yes") {
                                                            print "N";
                                                        } else {
                                                            if ($lstCaptionP == "Yes") {
                                                                print "P";
                                                            } else {
                                                                print "&nbsp;";
                                                            }
                                                        }
                                                    } else {
                                                        if ($lstCaptionP == "Yes") {
                                                            print "P";
                                                        } else {
                                                            print "&nbsp;";
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            print $caption;
                        }
                        if ($lstCaptionPreFlag == "No" && $lstCaptionO == "No" && $lstCaptionP == "No" && $lstCaptionN == "No" && $lstCaptionLIEO == "No") {
                            print "</font>";
                        } else {
                            print "</font></a>";
                        }
                    } else {
                        print "<td align='center'>";
                        print "<font face='Verdana' color='#000000' size='" . $txtFontSize . "'><br><b>___</b><br><br><br></font>";
                    }
                    print "</td>";
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
                if ($lstCaptionPreFlag == "Yes") {
                    $caption = preFlagTitle($conn, $cur[0], $txtDate);
                } else {
                    $caption = "";
                }
                if ($lstCaptionA == "Yes" && $caption == "") {
                    $caption = "A";
                } else {
                    if ($caption == "") {
                        $query_absent = "SELECT e_time FROM tenter WHERE e_date = '" . $cur[9] . "' AND e_id = '" . $cur[0] . "' AND p_flag = 0";
                        $result_absent = selectData($conn, $query_absent);
                        if ($result_absent == "") {
                            $caption = "&nbsp;";
                        } else {
                            $caption = "I/O= " . displayTime($result_absent[0]);
                        }
                    }
                }
                if ($lstType != "ACard") {
                    displayDate($txtDate);
                    print "<td><a title='" . displayDate($txtDate) . "'><font face='Verdana' size='" . $txtFontSize . "'>" . $caption . "</font></a></td>";
                } else {
                    displayDate($txtDate);
                    print "<td align='center'><a title='" . displayDate($txtDate) . "'><font face='Verdana' size='" . $txtFontSize . "'><br><b>___</b><br><br><br></font></a></td>";
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
            $data0 = $cur[0];
        }
    } else {
        $v_group = "";
        while ($cur = mysqli_fetch_row($result)) {
            $row_count++;
            print "<tr>";
            if ($lstCaptionIDNo == "Yes") {
                if ($lstGroupBy == $_SESSION[$session_variable . "IDColumnName"]) {
                    print "<td><font face='Verdana' size='" . $txtFontSize . "'>" . $cur[0] . "</font></td>";
                } else {
                    print "<td><font face='Verdana' size='" . $txtFontSize . "'>&nbsp;</font></td>";
                }
            }
            if ($lstCaptionDept == "Yes") {
                if ($lstGroupBy == "Dept") {
                    print "<td><font face='Verdana' size='" . $txtFontSize . "'>" . $cur[0] . "</font></td>";
                } else {
                    print "<td><font face='Verdana' size='" . $txtFontSize . "'>&nbsp;</font></td>";
                }
            }
            if ($lstCaptionDiv == "Yes") {
                if ($lstGroupBy == "Div/Desg") {
                    print "<td><font face='Verdana' size='" . $txtFontSize . "'>" . $cur[0] . "</font></td>";
                } else {
                    print "<td><font face='Verdana' size='" . $txtFontSize . "'>&nbsp;</font></td>";
                }
            }
            if ($lstCaptionRemark == "Yes") {
                if ($lstGroupBy == "Rmk") {
                    print "<td><font face='Verdana' size='" . $txtFontSize . "'>" . $cur[0] . "</font></td>";
                } else {
                    print "<td><font face='Verdana' size='" . $txtFontSize . "'>&nbsp;</font></td>";
                }
            }
            if ($lstCaptionShift == "Yes") {
                if ($lstGroupBy == "Shift") {
                    print "<td><font face='Verdana' size='" . $txtFontSize . "'>" . $cur[1] . "</font></td>";
                } else {
                    print "<td><font face='Verdana' size='" . $txtFontSize . "'>&nbsp;</font></td>";
                }
            }
            for ($i = 0; $i < $dayCount; $i++) {
                $next = strtotime(substr($txtFrom, 0, 2) . "-" . substr($txtFrom, 3, 2) . "-" . substr($txtFrom, 6, 4) . " + " . $i . " day");
                $a = getDate($next);
                $this_date = $a["year"] . "" . addZero($a["mon"], 2) . "" . addZero($a["mday"], 2);
                if ($lstGroupBy == $_SESSION[$session_variable . "IDColumnName"]) {
                    $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster, tuser WHERE ADate = " . $this_date . " AND EmployeeID = id AND idno = '" . $cur[0] . "' " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " " . employeeStatusQuery($lstEmployeeStatus) . " ";
                } else {
                    if ($lstGroupBy == "Dept") {
                        $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster, tuser WHERE ADate = " . $this_date . " AND EmployeeID = id AND dept = '" . $cur[0] . "' " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " " . employeeStatusQuery($lstEmployeeStatus) . " ";
                    } else {
                        if ($lstGroupBy == "Div/Desg") {
                            $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster, tuser WHERE ADate = " . $this_date . " AND EmployeeID = id AND company = '" . $cur[0] . "' " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " " . employeeStatusQuery($lstEmployeeStatus) . " ";
                        } else {
                            if ($lstGroupBy == "Rmk") {
                                $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster, tuser WHERE ADate = " . $this_date . " AND EmployeeID = id AND tuser.remark = '" . $cur[0] . "' " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " " . employeeStatusQuery($lstEmployeeStatus) . " ";
                            } else {
                                if ($lstGroupBy == "Shift") {
                                    $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster, tuser WHERE ADate = " . $this_date . " AND EmployeeID = id AND AttendanceMaster.group_id = '" . $cur[0] . "' " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " " . employeeStatusQuery($lstEmployeeStatus) . " ";
                                }
                            }
                        }
                    }
                }
                $sub_result = selectData($conn, $query);
                print "<td width='" . $txtColumnWidth . "'><font face='Verdana' size='" . $txtFontSize . "'>" . $sub_result[0] . "</font></td>";
                $v_group[$i] = $v_group[$i] * 1 + $sub_result[0] * 1;
            }
            print "<td width='" . $txtColumnWidth . "'><font face='Verdana' size='" . $txtFontSize . "'>" . $dayCount . "</font></td>";
            if ($lstGroupBy == $_SESSION[$session_variable . "IDColumnName"]) {
                $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster, tuser WHERE ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND EmployeeID = id AND idno = '" . $cur[0] . "' AND Day <> AttendanceMaster.OT1 AND Day <> AttendanceMaster.OT2 AND (Flag = 'Black' OR Flag = 'Proxy') ";
            } else {
                if ($lstGroupBy == "Dept") {
                    $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster, tuser WHERE ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND EmployeeID = id AND Day <> AttendanceMaster.OT1 AND Day <> AttendanceMaster.OT2 AND (Flag = 'Black' OR Flag = 'Proxy') AND dept = '" . $cur[0] . "'";
                } else {
                    if ($lstGroupBy == "Div/Desg") {
                        $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster, tuser WHERE ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND EmployeeID = id AND Day <> AttendanceMaster.OT1 AND Day <> AttendanceMaster.OT2 AND (Flag = 'Black' OR Flag = 'Proxy') AND company = '" . $cur[0] . "'";
                    } else {
                        if ($lstGroupBy == "Rmk") {
                            $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster, tuser WHERE ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND EmployeeID = id AND Day <> AttendanceMaster.OT1 AND Day <> AttendanceMaster.OT2 AND (Flag = 'Black' OR Flag = 'Proxy') AND tuser.remark = '" . $cur[0] . "'";
                        } else {
                            if ($lstGroupBy == "Shift") {
                                $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster WHERE ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND Day <> AttendanceMaster.OT1 AND Day <> AttendanceMaster.OT2 AND (Flag = 'Black' OR Flag = 'Proxy') AND group_id = '" . $cur[0] . "'";
                            }
                        }
                    }
                }
            }
            $sub_result = selectData($conn, $query);
            print "<td width='" . $txtColumnWidth . "'><font face='Verdana' size='" . $txtFontSize . "'>" . $sub_result[0] . "</font></td>";
            $v_group[$dayCount] = $v_group[$dayCount] * 1 + $sub_result[0] * 1;
            if ($lstGroupBy == $_SESSION[$session_variable . "IDColumnName"]) {
                $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster, tuser WHERE ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND EmployeeID = id AND idno = '" . $cur[0] . "' AND Day <> AttendanceMaster.OT1 AND Day <> AttendanceMaster.OT2 AND  Flag = 'Proxy' ";
            } else {
                if ($lstGroupBy == "Dept") {
                    $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster, tuser WHERE ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND EmployeeID = id AND Day <> AttendanceMaster.OT1 AND Day <> AttendanceMaster.OT2 AND  Flag = 'Proxy' AND dept = '" . $cur[0] . "'";
                } else {
                    if ($lstGroupBy == "Div/Desg") {
                        $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster, tuser WHERE ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND EmployeeID = id AND Day <> AttendanceMaster.OT1 AND Day <> AttendanceMaster.OT2 AND  Flag = 'Proxy' AND company = '" . $cur[0] . "'";
                    } else {
                        if ($lstGroupBy == "Rmk") {
                            $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster, tuser WHERE ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND EmployeeID = id AND Day <> AttendanceMaster.OT1 AND Day <> AttendanceMaster.OT2 AND  Flag = 'Proxy' AND tuser.remark = '" . $cur[0] . "'";
                        } else {
                            if ($lstGroupBy == "Shift") {
                                $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster WHERE ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND Day <> AttendanceMaster.OT1 AND Day <> AttendanceMaster.OT2 AND  Flag = 'Proxy' AND group_id = '" . $cur[0] . "'";
                            }
                        }
                    }
                }
            }
            $sub_result = selectData($conn, $query);
            print "<td width='" . $txtColumnWidth . "'><font face='Verdana' size='" . $txtFontSize . "'>" . $sub_result[0] . "</font></td>";
            $v_group[$dayCount + 1] = $v_group[$dayCount + 1] * 1 + $sub_result[0] * 1;
            if ($lstGroupBy == $_SESSION[$session_variable . "IDColumnName"]) {
                $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster, tuser WHERE ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND EmployeeID = id AND idno = '" . $cur[0] . "' AND Flag <> 'Proxy' AND Flag <> 'Black' ";
            } else {
                if ($lstGroupBy == "Dept") {
                    $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster, tuser WHERE ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND EmployeeID = id AND Flag <> 'Proxy' AND Flag <> 'Black' AND dept = '" . $cur[0] . "'";
                } else {
                    if ($lstGroupBy == "Div/Desg") {
                        $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster, tuser WHERE ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND EmployeeID = id AND Flag <> 'Proxy' AND Flag <> 'Black' AND company = '" . $cur[0] . "'";
                    } else {
                        if ($lstGroupBy == "Rmk") {
                            $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster, tuser WHERE ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND EmployeeID = id AND Flag <> 'Proxy' AND Flag <> 'Black' AND tuser.remark = '" . $cur[0] . "'";
                        } else {
                            if ($lstGroupBy == "Shift") {
                                $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster WHERE ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND Flag <> 'Proxy' AND Flag <> 'Black' AND group_id = '" . $cur[0] . "'";
                            }
                        }
                    }
                }
            }
            $sub_result = selectData($conn, $query);
            print "<td width='" . $txtColumnWidth . "'><font face='Verdana' size='" . $txtFontSize . "'>" . $sub_result[0] . "</font></td>";
            $v_group[$dayCount + 2] = $v_group[$dayCount + 2] * 1 + $sub_result[0] * 1;
            if ($lstGroupBy == $_SESSION[$session_variable . "IDColumnName"]) {
                $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster, tuser WHERE ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND EmployeeID = id AND idno = '" . $cur[0] . "' AND Day = AttendanceMaster.OT1 ";
            } else {
                if ($lstGroupBy == "Dept") {
                    $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster, tuser WHERE ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND EmployeeID = id AND Day = AttendanceMaster.OT1 AND dept = '" . $cur[0] . "'";
                } else {
                    if ($lstGroupBy == "Div/Desg") {
                        $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster, tuser WHERE ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND EmployeeID = id AND Day = AttendanceMaster.OT1 AND company = '" . $cur[0] . "'";
                    } else {
                        if ($lstGroupBy == "Rmk") {
                            $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster, tuser WHERE ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND EmployeeID = id AND Day = AttendanceMaster.OT1 AND tuser.remark = '" . $cur[0] . "'";
                        } else {
                            if ($lstGroupBy == "Shift") {
                                $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster WHERE ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND Day = AttendanceMaster.OT1 AND group_id = '" . $cur[0] . "'";
                            }
                        }
                    }
                }
            }
            $sub_result = selectData($conn, $query);
            print "<td width='" . $txtColumnWidth . "'><font face='Verdana' size='" . $txtFontSize . "'>" . $sub_result[0] . "</font></td>";
            $v_group[$dayCount + 3] = $v_group[$dayCount + 3] * 1 + $sub_result[0] * 1;
            if ($lstGroupBy == $_SESSION[$session_variable . "IDColumnName"]) {
                $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster, tuser WHERE ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND EmployeeID = id AND idno = '" . $cur[0] . "' AND Day = AttendanceMaster.OT2 ";
            } else {
                if ($lstGroupBy == "Dept") {
                    $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster, tuser WHERE ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND EmployeeID = id AND Day = AttendanceMaster.OT2 AND dept = '" . $cur[0] . "'";
                } else {
                    if ($lstGroupBy == "Div/Desg") {
                        $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster, tuser WHERE ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND EmployeeID = id AND Day = AttendanceMaster.OT2 AND company = '" . $cur[0] . "'";
                    } else {
                        if ($lstGroupBy == "Rmk") {
                            $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster, tuser WHERE ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND EmployeeID = id AND Day = AttendanceMaster.OT2 AND tuser.remark = '" . $cur[0] . "'";
                        } else {
                            if ($lstGroupBy == "Shift") {
                                $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster WHERE ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND Day = AttendanceMaster.OT2 AND group_id = '" . $cur[0] . "'";
                            }
                        }
                    }
                }
            }
            $sub_result = selectData($conn, $query);
            print "<td width='" . $txtColumnWidth . "'><font face='Verdana' size='" . $txtFontSize . "'>" . $sub_result[0] . "</font></td>";
            $v_group[$dayCount + 4] = $v_group[$dayCount + 4] * 1 + $sub_result[0] * 1;
            if (strpos($ras, "-V-") !== false) {
                flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Violet", $txtColumnWidth, $txtFontSize, $v_group, $cur[0]);
            }
            if (strpos($ras, "-I-") !== false) {
                flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Indigo", $txtColumnWidth, $txtFontSize, $v_group, $cur[0]);
            }
            if (strpos($ras, "-B-") !== false) {
                flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Blue", $txtColumnWidth, $txtFontSize, $v_group, $cur[0]);
            }
            if (strpos($ras, "-G-") !== false) {
                flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Green", $txtColumnWidth, $txtFontSize, $v_group, $cur[0]);
            }
            if (strpos($ras, "-Y-") !== false) {
                flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Yellow", $txtColumnWidth, $txtFontSize, $v_group, $cur[0]);
            }
            if (strpos($ras, "-O-") !== false) {
                flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Orange", $txtColumnWidth, $txtFontSize, $v_group, $cur[0]);
            }
            if (strpos($ras, "-R-") !== false) {
                flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Red", $txtColumnWidth, $txtFontSize, $v_group, $cur[0]);
            }
            if (strpos($ras, "-GR-") !== false) {
                flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Gray", $txtColumnWidth, $txtFontSize, $v_group, $cur[0]);
            }
            if (strpos($ras, "-BR-") !== false) {
                flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Brown", $txtColumnWidth, $txtFontSize, $v_group, $cur[0]);
            }
            if (strpos($ras, "-PR-") !== false) {
                flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Purple", $txtColumnWidth, $txtFontSize, $v_group, $cur[0]);
            }
            if (strpos($ras, "-MG-") !== false) {
                flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Magenta", $txtColumnWidth, $txtFontSize, $v_group, $cur[0]);
            }
            if (strpos($ras, "-TL-") !== false) {
                flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Teal", $txtColumnWidth, $txtFontSize, $v_group, $cur[0]);
            }
            if (strpos($ras, "-AQ-") !== false) {
                flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Aqua", $txtColumnWidth, $txtFontSize, $v_group, $cur[0]);
            }
            if (strpos($ras, "-SF-") !== false) {
                flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Safron", $txtColumnWidth, $txtFontSize, $v_group, $cur[0]);
            }
            if (strpos($ras, "-AM-") !== false) {
                flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Amber", $txtColumnWidth, $txtFontSize, $v_group, $cur[0]);
            }
            if (strpos($ras, "-GL-") !== false) {
                flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Gold", $txtColumnWidth, $txtFontSize, $v_group, $cur[0]);
            }
            if (strpos($ras, "-VM-") !== false) {
                flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Vermilon", $txtColumnWidth, $txtFontSize, $v_group, $cur[0]);
            }
            if (strpos($ras, "-SL-") !== false) {
                flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Silver", $txtColumnWidth, $txtFontSize, $v_group, $cur[0]);
            }
            if (strpos($ras, "-MR-") !== false) {
                flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Maroon", $txtColumnWidth, $txtFontSize, $v_group, $cur[0]);
            }
            if (strpos($ras, "-PK-") !== false) {
                flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Pink", $txtColumnWidth, $txtFontSize, $v_group, $cur[0]);
            }
            if ($lstGroupBy == $_SESSION[$session_variable . "IDColumnName"]) {
                $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster, tuser WHERE ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND EmployeeID = id AND idno = '" . $cur[0] . "' ";
            } else {
                if ($lstGroupBy == "Dept") {
                    $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster, tuser WHERE ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND EmployeeID = id AND dept = '" . $cur[0] . "'";
                } else {
                    if ($lstGroupBy == "Div/Desg") {
                        $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster, tuser WHERE ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND EmployeeID = id AND company = '" . $cur[0] . "'";
                    } else {
                        if ($lstGroupBy == "Rmk") {
                            $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster, tuser WHERE ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND EmployeeID = id AND tuser.remark = '" . $cur[0] . "'";
                        } else {
                            if ($lstGroupBy == "Shift") {
                                $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster WHERE ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND group_id = '" . $cur[0] . "'";
                            }
                        }
                    }
                }
            }
            $sub_result = selectData($conn, $query);
            print "<td width='" . $txtColumnWidth . "' bgcolor='#F0F0F0'><font face='Verdana' size='" . $txtFontSize . "'>" . $sub_result[0] . "</font></td>";
            $v_group[$dayCount + 5] = $v_group[$dayCount + 5] * 1 + $sub_result[0] * 1;
            if ($lstGroupBy == $_SESSION[$session_variable . "IDColumnName"]) {
                $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster, tuser WHERE ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND EmployeeID = id AND NightFlag = 1 AND idno = '" . $cur[0] . "' ";
            } else {
                if ($lstGroupBy == "Dept") {
                    $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster, tuser WHERE ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND EmployeeID = id AND NightFlag = 1 AND dept = '" . $cur[0] . "'";
                } else {
                    if ($lstGroupBy == "Div/Desg") {
                        $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster, tuser WHERE ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND EmployeeID = id AND NightFlag = 1 AND company = '" . $cur[0] . "'";
                    } else {
                        if ($lstGroupBy == "Rmk") {
                            $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster, tuser WHERE ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND EmployeeID = id AND NightFlag = 1 AND tuser.remark = '" . $cur[0] . "'";
                        } else {
                            if ($lstGroupBy == "Shift") {
                                $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster WHERE ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND NightFlag = 1 AND group_id = '" . $cur[0] . "'";
                            }
                        }
                    }
                }
            }
            $sub_result = selectData($conn, $query);
            print "<td width='" . $txtColumnWidth . "'><font face='Verdana' size='" . $txtFontSize . "'>" . $sub_result[0] . "</font></td>";
            $nsn = $sub_result[0];
            $nso = 0;
            $v_group[$dayCount + 6] = $v_group[$dayCount + 6] * 1 + $sub_result[0] * 1;
            print "</tr>";
        }
        print "<tr>";
        if ($lstCaptionIDNo == "Yes") {
            print "<td><font face='Verdana' size='" . $txtFontSize . "'>&nbsp;</font></td>";
        }
        if ($lstCaptionDept == "Yes") {
            print "<td><font face='Verdana' size='" . $txtFontSize . "'>&nbsp;</font></td>";
        }
        if ($lstCaptionDiv == "Yes") {
            print "<td><font face='Verdana' size='" . $txtFontSize . "'>&nbsp;</font></td>";
        }
        if ($lstCaptionRemark == "Yes") {
            print "<td><font face='Verdana' size='" . $txtFontSize . "'>&nbsp;</font></td>";
        }
        if ($lstCaptionShift == "Yes") {
            print "<td><font face='Verdana' size='" . $txtFontSize . "'>&nbsp;</font></td>";
        }
        for ($i = 0; $i < count($v_group, $cur[0]); $i++) {
            if ($i == count($v_group) - 2) {
                print "<td width='" . $txtColumnWidth . "' bgcolor='#F0F0F0'><font face='Verdana' size='" . $txtFontSize . "'><b>" . $v_group[$i] . "</b></font></td>";
            } else {
                print "<td width='" . $txtColumnWidth . "'><font face='Verdana' size='" . $txtFontSize . "'><b>" . $v_group[$i] . "</b></font></td>";
            }
        }
        print "</tr>";
    }
    if ($lstGroupBy == "") {
        if (0 < $count) {
            for ($i = $subc; $i < $dayCount; $i++) {
                if ($lstCaptionPreFlag == "Yes") {
                    $next = strtotime(substr($txtFrom, 0, 2) . "-" . substr($txtFrom, 3, 2) . "-" . substr($txtFrom, 6, 4) . " + " . $i . " day");
                    $a = getDate($next);
                    $this_date = $a["year"] . "" . addZero($a["mon"], 2) . "" . addZero($a["mday"], 2);
                    $caption = preFlagTitle($conn, $data0, $this_date);
                } else {
                    $caption = "";
                }
                if ($lstCaptionA == "Yes" && $caption == "") {
                    $caption = "A";
                } else {
                    if ($caption == "") {
                        $caption = "&nbsp;";
                    }
                }
                if ($lstType != "ACard") {
                    print "<td><a title='" . ($i + 1) . "'><font face='Verdana' size='" . $txtFontSize . "'>" . $caption . "</font></a></td>";
                } else {
                    print "<td align='center'><a title='" . ($i + 1) . "'><font face='Verdana' size='" . $txtFontSize . "'><br><b>___</b><br><br><br></font></a></td>";
                }
            }
        }
        if (0 < $count && $lstCaptionTotal == "Yes") {
            print "<td><a title='Total Days'><font face='Verdana' size='" . $txtFontSize . "'><b>" . $dayCount . "</b></font></a></td> <td><a title='Week Days with Normal + OT Hours'><font face='Verdana' size='" . $txtFontSize . "'><b>" . ($wkdn + $wkdo) . "</b></font></a></td> <td><a title='Proxy Days with Normal + OT Hours'><font face='Verdana' size='" . $txtFontSize . "'><b>" . ($pxyn + $pxyo) . "</b></font></a></td> <td><a title='Flag Days with Normal + OT Hours'><font face='Verdana' size='" . $txtFontSize . "'><b>" . ($flgn + $flgo) . "</b></font></a></td> <td><a title='Saturdays with Normal + OT Hours'><font face='Verdana' size='" . $txtFontSize . "'><b>" . ($satn + $sato) . "</b></font></a></td> <td><a title='Sunday with Normal + OT Hours'><font face='Verdana' size='" . $txtFontSize . "'><b>" . ($sunn + $suno) . "</b></font></a></td>";
            if (strpos($ras, "-V-") !== false) {
                flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Violet", $txtColumnWidth, $txtFontSize, $v_group, $eid);
            }
            if (strpos($ras, "-I-") !== false) {
                flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Indigo", $txtColumnWidth, $txtFontSize, $v_group, $eid);
            }
            if (strpos($ras, "-B-") !== false) {
                flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Blue", $txtColumnWidth, $txtFontSize, $v_group, $eid);
            }
            if (strpos($ras, "-G-") !== false) {
                flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Green", $txtColumnWidth, $txtFontSize, $v_group, $eid);
            }
            if (strpos($ras, "-Y-") !== false) {
                flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Yellow", $txtColumnWidth, $txtFontSize, $v_group, $eid);
            }
            if (strpos($ras, "-O-") !== false) {
                flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Orange", $txtColumnWidth, $txtFontSize, $v_group, $eid);
            }
            if (strpos($ras, "-R-") !== false) {
                flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Red", $txtColumnWidth, $txtFontSize, $v_group, $eid);
            }
            if (strpos($ras, "-GR-") !== false) {
                flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Gray", $txtColumnWidth, $txtFontSize, $v_group, $eid);
            }
            if (strpos($ras, "-BR-") !== false) {
                flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Brown", $txtColumnWidth, $txtFontSize, $v_group, $eid);
            }
            if (strpos($ras, "-PR-") !== false) {
                flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Purple", $txtColumnWidth, $txtFontSize, $v_group, $eid);
            }
            if (strpos($ras, "-MG-") !== false) {
                flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Magenta", $txtColumnWidth, $txtFontSize, $v_group, $eid);
            }
            if (strpos($ras, "-TL-") !== false) {
                flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Teal", $txtColumnWidth, $txtFontSize, $v_group, $eid);
            }
            if (strpos($ras, "-AQ-") !== false) {
                flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Aqua", $txtColumnWidth, $txtFontSize, $v_group, $eid);
            }
            if (strpos($ras, "-SF-") !== false) {
                flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Safron", $txtColumnWidth, $txtFontSize, $v_group, $eid);
            }
            if (strpos($ras, "-AM-") !== false) {
                flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Amber", $txtColumnWidth, $txtFontSize, $v_group, $eid);
            }
            if (strpos($ras, "-GL-") !== false) {
                flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Gold", $txtColumnWidth, $txtFontSize, $v_group, $eid);
            }
            if (strpos($ras, "-VM-") !== false) {
                flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Vermilon", $txtColumnWidth, $txtFontSize, $v_group, $eid);
            }
            if (strpos($ras, "-SL-") !== false) {
                flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Silver", $txtColumnWidth, $txtFontSize, $v_group, $eid);
            }
            if (strpos($ras, "-MR-") !== false) {
                flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Maroon", $txtColumnWidth, $txtFontSize, $v_group, $eid);
            }
            if (strpos($ras, "-PK-") !== false) {
                flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $cur[0], "Pink", $txtColumnWidth, $txtFontSize, $v_group, $eid);
            }
            if ($lstType == "") {
                print "<td bgcolor='#F0F0F0'><a title ='Total with Normal + OT Days'><font face='Verdana' size='" . $txtFontSize . "'><b>" . ($wkdn + $pxyn + $flgn + $satn + $sunn + $wkdo + $pxyo + $flgo + $sato + $suno) . "</b></font></a></td> <td bgcolor='#F0F0F0'><a title='Total Days Absent'><font face='Verdana' size='" . $txtFontSize . "'><b>" . ($dayCount - ($wkdn + $pxyn + $flgn + $satn + $sunn + $wkdo + $pxyo + $flgo + $sato + $suno)) . "</b></font></a></td> <td bgcolor='#F0F0F0'><a title='Total Days Absent EXCLUDING Sundays'><font face='Verdana' size='" . $txtFontSize . "'><b>" . ($dayCount + $satabo + $satabn - $sunCount - ($wkdn + $pxyn + $flgn + $satn + $wkdo + $pxyo + $flgo + $sato)) . "</b></font></a></td> <td bgcolor='#F0F0F0'><a title='Total Days Absent EXCLUDING Saturdays and Sundays'><font face='Verdana' size='" . $txtFontSize . "'><b>" . ($dayCount + $sunabo + $sunabn + $satabo + $satabn - ($satCount + $sunCount) - ($wkdn + $pxyn + $flgn + $wkdo + $pxyo + $flgo)) . "</b></font></a></td>";
            } else {
                print "<td bgcolor='#F0F0F0'><a title ='Total with Normal + OT Days'><font face='Verdana' size='" . $txtFontSize . "'><b>" . ($wkdn + $pxyn + $flgn + $satn + $sunn + $wkdo + $pxyo + $flgo + $sato + $suno) . "</b></font></a></td>";
            }
            print "<td><a title='Night Shifts with Normal + OT Days'><font face='Verdana' size='" . $txtFontSize . "'><b>" . ($nsn + $nso) . "</b></font></a></td>";
        }
        if (0 < $count) {
            $row_count++;
        }
    }
    if ($lstType == "" && $lstGroupBy == "") {
        $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tuser.idno, tuser.remark FROM tuser, tgroup WHERE tuser.group_id = tgroup.id AND tuser.id NOT IN (SELECT EmployeeID FROM AttendanceMaster WHERE ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . ") " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
        if ($lstShift != "") {
            $query = $query . " AND tgroup.id = " . $lstShift;
        }
        $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
        $query = $query . employeeStatusQuery($lstEmployeeStatus);
        $lstSort = str_replace(", AttendanceMaster.ADate", "", $lstSort);
        $query = $query . " ORDER BY " . $lstSort;
        for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $row_count++) {
            if (($row_count % $txtHeaderBreak == 0 || $row_count == $first_page_break) && $prints == "yes" && $excel != "yes" && $row_count != 0) {
                print "</table><div style='page-break-before: always;'></div><table width='100%' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' border='1' bordercolor='#C0C0C0'>";
                print "<tr style='page-break-before:always;'><td><font face='Verdana' size='" . $txtFontSize . "'>ID</font></td> <td><font face='Verdana' size='" . $txtFontSize . "'>Name</font></td>";
                if ($lstCaptionIDNo == "Yes") {
                    print "<td><font face='Verdana' size='" . $txtFontSize . "'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td>";
                }
                if ($lstCaptionDept == "Yes") {
                    print "<td><font face='Verdana' size='" . $txtFontSize . "'>Dept</font></td>";
                }
                if ($lstCaptionDiv == "Yes") {
                    print "<td><font face='Verdana' size='" . $txtFontSize . "'>Div/Desg</font></td>";
                }
                if ($lstCaptionRemark == "Yes") {
                    print "<td><font face='Verdana' size='" . $txtFontSize . "'>Rmk</font></td>";
                }
                if ($lstCaptionShift == "Yes") {
                    print "<td><font face='Verdana' size='" . $txtFontSize . "'>Shift</font></td>";
                }
                for ($i = 0; $i < $dayCount; $i++) {
                    $next = strtotime(substr($txtFrom, 0, 2) . "-" . substr($txtFrom, 3, 2) . "-" . substr($txtFrom, 6, 4) . " + " . $i . " day");
                    $a = getDate($next);
                    print "<td width='" . $txtColumnWidth . "'><font face='Verdana' size='" . $txtFontSize . "'>" . $a["mday"] . "</font></td>";
                }
                if ($lstCaptionTotal == "Yes") {
                    print "<td align='center'><font face='Verdana' size='" . $txtFontSize . "'><b>WKD</b></font></td> <td align='center'><font face='Verdana' size='" . $txtFontSize . "'><b>PXY</b></font></td> <td align='center'><font face='Verdana' size='" . $txtFontSize . "'><b>FLG</b></font></td> <td align='center'><font face='Verdana' size='" . $txtFontSize . "'><b>SAT</b></font></td> <td align='center'><font face='Verdana' size='" . $txtFontSize . "'><b>SUN</b></font></td>";
                    if ($lstType == "") {
                        print "<td colspan='4' align='center' bgcolor='#F0F0F0'><font face='Verdana' size='" . $txtFontSize . "'><b>TLD</b></font></td>";
                    }
                    print "<td align='center'><font face='Verdana' size='" . $txtFontSize . "'><b>NS</b></font></td>";
                    $userrdsselection = $userrdsselection . "--Total";
                }
                print "</tr>";
                print "<tr>";
                if ($lstType == "ACard") {
                    substr($txtFrom, 3, 10);
                    print "<td><font face='Verdana' size='" . $txtFontSize . "'><b>" . substr($txtFrom, 3, 10) . "</b></font></td>";
                    print "<td><font face='Verdana' size='" . $txtFontSize . "'><b>" . $lstDepartment . "</b></font></td>";
                    for ($i = 0; $i < 5 - $colcount; $i++) {
                        print "<td><font face='Verdana' size='" . $txtFontSize . "'>&nbsp;</font></td>";
                    }
                } else {
                    for ($i = 0; $i < 7 - $colcount; $i++) {
                        print "<td><font face='Verdana' size='" . $txtFontSize . "'>&nbsp;</font></td>";
                    }
                }
                for ($i = 0; $i < $dayCount; $i++) {
                    $next = strtotime(substr($txtFrom, 0, 2) . "-" . substr($txtFrom, 3, 2) . "-" . substr($txtFrom, 6, 4) . " + " . $i . " day");
                    $a = getDate($next);
                    substr($a["weekday"], 0, 1);
                    print "<td><a title='" . $a["weekday"] . "'><font face='Verdana' size='" . $txtFontSize . "'>" . substr($a["weekday"], 0, 1) . "</font></a></td>";
                }
                if ($lstCaptionTotal == "Yes") {
                    print "<td><font face='Verdana' size='1'><b>&nbsp;</b></font></td> <td><font face='Verdana' size='1'><b>&nbsp;</b></font></td> <td><font face='Verdana' size='1'><b>&nbsp;</b></font></td> <td><font face='Verdana' size='1'><b>&nbsp;</b></font></td> <td><font face='Verdana' size='1'><b>&nbsp;</b></font></td>";
                    if ($lstType == "") {
                        print "<td bgcolor='#F0F0F0'><font face='Verdana' size='1'><b>P</b></font></td> <td bgcolor='#F0F0F0'><font face='Verdana' size='1'><b>A</b></font></td> <td bgcolor='#F0F0F0'><font face='Verdana' size='1'><b>A/S</b></font></td> <td bgcolor='#F0F0F0'><font face='Verdana' size='1'><b>A/SS</b></font></td>";
                    }
                    print "<td><font face='Verdana' size='1'><b>&nbsp;</b></font></td>";
                }
                print "</tr>";
            }
            if ($cur[5] == "") {
                $cur[5] = "&nbsp;";
            }
            if ($cur[6] == "") {
                $cur[6] = "&nbsp;";
            }
            addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
            print "<tr><td><a title='ID: " . $cur[0] . "'><font face='Verdana' size='" . $txtFontSize . "' color='#000000'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='" . $txtFontSize . "'>" . $cur[1] . "</font></a></td>";
            if ($lstCaptionIDNo == "Yes") {
                print "<td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='" . $txtFontSize . "'>" . $cur[5] . "</font></a></td>";
            }
            if ($lstCaptionDept == "Yes") {
                print "<td><a title='Dept'><font face='Verdana' size='" . $txtFontSize . "'>" . $cur[2] . "</font></a></td>";
            }
            if ($lstCaptionDiv == "Yes") {
                print "<td><a title='Div/Desg'><font face='Verdana' size='" . $txtFontSize . "'>" . $cur[3] . "</font></a></td>";
            }
            if ($lstCaptionRemark == "Yes") {
                print "<td><a title='Rmk'><font face='Verdana' size='" . $txtFontSize . "'>" . $cur[6] . "</font></a></td>";
            }
            if ($lstCaptionShift == "Yes") {
                print "<td><a title='Current Shift'><font face='Verdana' size='" . $txtFontSize . "'>" . $cur[4] . "</font></a></td>";
            }
            for ($i = 0; $i < $dayCount; $i++) {
                if ($lstCaptionPreFlag == "Yes") {
                    $next = strtotime(substr($txtFrom, 0, 2) . "-" . substr($txtFrom, 3, 2) . "-" . substr($txtFrom, 6, 4) . " + " . $i . " day");
                    $a = getDate($next);
                    $this_date = $a["year"] . "" . addZero($a["mon"], 2) . "" . addZero($a["mday"], 2);
                    $caption = preFlagTitle($conn, $cur[0], $this_date);
                } else {
                    $caption = "";
                }
                if ($lstCaptionA == "Yes" && $caption == "") {
                    $caption = "A";
                } else {
                    if ($caption == "") {
                        $caption = "&nbsp;";
                    }
                }
                if ($lstType != "ACard") {
                    print "<td><a title='" . ($i + 1) . "'><font face='Verdana' size='" . $txtFontSize . "'>" . $caption . "</font></a></td>";
                } else {
                    print "<td align='center'><a title='" . ($i + 1) . "'><font face='Verdana' size='" . $txtFontSize . "'><br><b>___</b><br><br><br></font></a></td>";
                }
            }
            if ($lstCaptionTotal == "Yes") {
                print "<td><a title='Total Days'><font face='Verdana' size='" . $txtFontSize . "'><b>" . $dayCount . "</b></font></a></td> <td><a title='Week Days with Normal Days'><font face='Verdana' size='" . $txtFontSize . "'><b>0</b></font></a></td> <td><a title='Proxy Days with Normal Days'><font face='Verdana' size='" . $txtFontSize . "'><b>0</b></font></a></td> <td><a title='Flag Days with Normal Days'><font face='Verdana' size='" . $txtFontSize . "'><b>0</b></font></a></td> <td><a title='Saturdays with Normal Days'><font face='Verdana' size='" . $txtFontSize . "'><b>0</b></font></a></td> <td><a title='Sunday with Normal Days'><font face='Verdana' size='" . $txtFontSize . "'><b>0</b></font></a></td>";
                if (strpos($ras, "-V-") !== false) {
                    print "<td><font face='Verdana' size='" . $txtFontSize . "'><b>0</b></font></td>";
                }
                if (strpos($ras, "-I-") !== false) {
                    print "<td><font face='Verdana' size='" . $txtFontSize . "'><b>0</b></font></td>";
                }
                if (strpos($ras, "-B-") !== false) {
                    print "<td><font face='Verdana' size='" . $txtFontSize . "'><b>0</b></font></td>";
                }
                if (strpos($ras, "-G-") !== false) {
                    print "<td><font face='Verdana' size='" . $txtFontSize . "'><b>0</b></font></td>";
                }
                if (strpos($ras, "-Y-") !== false) {
                    print "<td><font face='Verdana' size='" . $txtFontSize . "'><b>0</b></font></td>";
                }
                if (strpos($ras, "-O-") !== false) {
                    print "<td><font face='Verdana' size='" . $txtFontSize . "'><b>0</b></font></td>";
                }
                if (strpos($ras, "-R-") !== false) {
                    print "<td><font face='Verdana' size='" . $txtFontSize . "'><b>0</b></font></td>";
                }
                if (strpos($ras, "-GR-") !== false) {
                    print "<td><font face='Verdana' size='" . $txtFontSize . "'><b>0</b></font></td>";
                }
                if (strpos($ras, "-BR-") !== false) {
                    print "<td><font face='Verdana' size='" . $txtFontSize . "'><b>0</b></font></td>";
                }
                if (strpos($ras, "-PR-") !== false) {
                    print "<td><font face='Verdana' size='" . $txtFontSize . "'><b>0</b></font></td>";
                }
                if (strpos($ras, "-MG-") !== false) {
                    print "<td><font face='Verdana' size='" . $txtFontSize . "'><b>0</b></font></td>";
                }
                if (strpos($ras, "-TL-") !== false) {
                    print "<td><font face='Verdana' size='" . $txtFontSize . "'><b>0</b></font></td>";
                }
                if (strpos($ras, "-AQ-") !== false) {
                    print "<td><font face='Verdana' size='" . $txtFontSize . "'><b>0</b></font></td>";
                }
                if (strpos($ras, "-SF-") !== false) {
                    print "<td><font face='Verdana' size='" . $txtFontSize . "'><b>0</b></font></td>";
                }
                if (strpos($ras, "-AM-") !== false) {
                    print "<td><font face='Verdana' size='" . $txtFontSize . "'><b>0</b></font></td>";
                }
                if (strpos($ras, "-GL-") !== false) {
                    print "<td><font face='Verdana' size='" . $txtFontSize . "'><b>0</b></font></td>";
                }
                if (strpos($ras, "-VM-") !== false) {
                    print "<td><font face='Verdana' size='" . $txtFontSize . "'><b>0</b></font></td>";
                }
                if (strpos($ras, "-SL-") !== false) {
                    print "<td><font face='Verdana' size='" . $txtFontSize . "'><b>0</b></font></td>";
                }
                if (strpos($ras, "-MR-") !== false) {
                    print "<td><font face='Verdana' size='" . $txtFontSize . "'><b>0</b></font></td>";
                }
                if (strpos($ras, "-PK-") !== false) {
                    print "<td><font face='Verdana' size='" . $txtFontSize . "'><b>0</b></font></td>";
                }
                print "<td bgcolor='#F0F0F0'><a title ='Total with Normal + OT Days'><a title='Total Days Present'><font face='Verdana' size='" . $txtFontSize . "'><b>0</b></font></a></td> <td bgcolor='#F0F0F0'><a title='Total Days Absent'><font face='Verdana' size='" . $txtFontSize . "'><b>" . $dayCount . "</b></font></a></td> <td bgcolor='#F0F0F0'><a title='Total Days Absent EXCLUDING Sundays'><font face='Verdana' size='" . $txtFontSize . "'><b>" . ($dayCount - $sunCount) . "</b></font></a></td> <td bgcolor='#F0F0F0'><a title='Total Days Absent EXCLUDING Sundays and Saturdays'><font face='Verdana' size='" . $txtFontSize . "'><b>" . ($dayCount - $sunCount - $satCount) . "</b></font></a></td> <td><a title='Night Shifts with Normal Days'><font face='Verdana' size='" . $txtFontSize . "'><b>0</b></font></a></td> </tr>";
            }
        }
    }
    print "</table>";
    print "</div></div></div></div>";
    if ($excel != "yes") {
        print "<br><p align='center'><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $row_count . "</b> <br>Total Day(s) Displayed: <b>" . $dayCount . "</b></font>";
    }
    if ($prints != "yes") {
        print "<br><input type='button' value='Print Report' class='btn btn-primary' onClick='checkPrint(0)'>&nbsp;&nbsp;<input type='button' class='btn btn-primary' value='Excel' onClick='checkPrint(1)'>";
        if ($lstCaptionO == "Yes") {
            $userrdsselection = $userrdsselection . "--O";
        }
        if ($lstCaptionLIEO == "Yes") {
            $userrdsselection = $userrdsselection . "--LIEO";
        }
        if ($lstCaptionN == "Yes") {
            $userrdsselection = $userrdsselection . "--N";
        }
        if ($lstCaptionA == "Yes") {
            $userrdsselection = $userrdsselection . "--A";
        }
        if ($lstCaptionP == "Yes") {
            $userrdsselection = $userrdsselection . "--P";
        }
        if ($lstCaptionPreFlag == "Yes") {
            $userrdsselection = $userrdsselection . "--PreFlag";
        }
        if ($lstCaptionIDNo == "Yes") {
            $userrdsselection = $userrdsselection . "--IDNo";
        }
        if ($lstCaptionDept == "Yes") {
            $userrdsselection = $userrdsselection . "--Dept";
        }
        if ($lstCaptionDiv == "Yes") {
            $userrdsselection = $userrdsselection . "--Div";
        }
        if ($lstCaptionRemark == "Yes") {
            $userrdsselection = $userrdsselection . "--Remark";
        }
        if ($lstCaptionTotal == "Yes") {
            $userrdsselection = $userrdsselection . "--Total";
        }
        if ($lstCaptionShift == "Yes") {
            $userrdsselection = $userrdsselection . "--Shift";
        }
        $query = "UPDATE UserMaster SET RDSFont = '" . $txtFontSize . "', RDSCW = '" . $txtColumnWidth . "', RDSSelection = '" . $userrdsselection . "' WHERE Username = '" . $username . "'";
        updateData($conn, $query, true);
    }
    print "</p>";
}
print "</form>";

include 'footer.php';
//echo "</center></body></html>";
function preFlagTitle($conn, $id, $date)
{
    $query = "SELECT FlagTitle.Title, FlagDayRotation.OT FROM FlagTitle, FlagDayRotation WHERE FlagTitle.Flag = FlagDayRotation.Flag AND FlagDayRotation.e_id = '" . $id . "' AND FlagDayRotation.e_date = '" . $date . "'";
    $result = selectData($conn, $query);
    if (0 < strlen($result[0])) {
        return $result[0];
    }
    return $result[1];
}
function postFlagTitle($conn, $flag)
{
    $query = "SELECT FlagTitle.Title FROM FlagTitle WHERE FlagTitle.Flag = '" . $flag . "'";
    $result = selectData($conn, $query);
    return $result[0];
}
function flagTotal($conn, $lstGroupBy, $session_variable, $txtFrom, $txtTo, $val, $flag, $txtColumnWidth, $txtFontSize, $v_group, $id)
{
    if ($lstGroupBy == $_SESSION[$session_variable . "IDColumnName"]) {
        $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster, tuser WHERE ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND EmployeeID = id AND idno = '" . $val . "' AND AttendanceMaster.Flag = '" . $flag . "' ";
    } else {
        if ($lstGroupBy == "Dept") {
            $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster, tuser WHERE ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND EmployeeID = id AND dept = '" . $val . "' AND AttendanceMaster.Flag = '" . $flag . "' ";
        } else {
            if ($lstGroupBy == "Div/Desg") {
                $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster, tuser WHERE ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND EmployeeID = id AND company = '" . $val . "' AND AttendanceMaster.Flag = '" . $flag . "' ";
            } else {
                if ($lstGroupBy == "Rmk") {
                    $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster, tuser WHERE ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND EmployeeID = id AND tuser.remark = '" . $val . "' AND AttendanceMaster.Flag = '" . $flag . "' ";
                } else {
                    if ($lstGroupBy == "Shift") {
                        $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster WHERE ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND group_id = '" . $val . "' AND AttendanceMaster.Flag = '" . $flag . "' ";
                    } else {
                        if ($lstGroupBy == "") {
                            $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster WHERE ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND AttendanceMaster.Flag = '" . $flag . "' AND EmployeeID = " . $id;
                        }
                    }
                }
            }
        }
    }
    $sub_result = selectData($conn, $query);
    print "<td width='" . $txtColumnWidth . "'";
    if ($flag == "Yellow") {
        print " bgcolor='Brown' ";
    }
    print "><font face='Verdana' size='" . $txtFontSize . "' color='" . $flag . "'><b>" . $sub_result[0] . "</b></font></td>";
    $v_group[$dayCount + 5] = $v_group[$dayCount + 5] * 1 + $sub_result[0] * 1;
}

?>