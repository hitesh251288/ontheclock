<?php
ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$username = $_SESSION[$session_variable . "username"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$userstatus = $_SESSION[$session_variable . "userstatus"];
$prints = $_GET["prints"];
echo "\r\n<html><head><title>SiteMap</title>\r\n\t<link rel=\"shortcut icon\" href=\"favicon.ico\">\r\n</head>\r\n\t";

if ($prints != "yes") {
    include 'header.php';
    ?>
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-12 d-flex no-block align-items-center">
                <h4 class="page-title">Site Map</h4>
                <div class="ms-auto text-end">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">
                                SiteMap
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <?php
print'<div class="container-fluid">';
print "<form name='frm1' method='post'>";

 } else{
     print "<body onLoad='javascript:window.print()'><center><div align='center'>";
 }

//if ($prints != "yes") {
//    print "<body><center><div align='center'>";
//    displayHeader($prints,$false,$false);
//    print "<center>";
//    displayLinks(300, $userlevel);
//    print "</center>";
//    print "<br>";
//    print "<font color='#FF0000' face='Verdana' size='2'><b>Site Map</b></font><br><br>";
//    print "<form name='frm1' method='post'><table width='800' bgcolor='#C0C0C0'>";
//} else {
//    print "<body onLoad='javascript:window.print()'><center><div align='center'>";
//    displayHeader($prints,$false,$false);
//    print "<font color='#FF0000' face='Verdana' size='2'><b>Site Map</b></font><br><br>";
//    print "<form name='frm1' method='post'><table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
//}
print '<div class="row"><div class="col-md-12"><div class="card"><div class="card-body table-responsive">';
//echo "\t\r\n\t\t<tr>\r\n\t\t\t<td vAlign='top' bgcolor='#FFFFFF'>\r\n\t\t\t\t<font face='Verdana' size='2' color='#000000'>\r\n\t\t\t\t\t<i><b>Login</b></i>\r\n\t\t\t\t</font>\r\n\t\t\t\t<br>\r\n\t\t\t\t";
//help(0, "Login.php");
echo "<div class='row'>";
echo "<div class='col-3'>";
echo "<font face='Verdana' size='2' color='#000000'><i><b>Login</b></i></font>";
help(0, "Login.php");
echo "</div>";
echo "<div class='col-3'>";
echo "<font face='Verdana' size='2' color='#000000'><i><b>Password</b></i></font>";
help(1, "Password.php");
echo "</div>";
echo "<div class='col-3'>";
echo "<font face='Verdana' size='2' color='#000000'><i><b>Dashboard</b></i></font>";
help(2, "Welcome.php");
echo "</div>";
echo "<div class='col-3'>";
echo "<font face='Verdana' size='2' color='#000000'><i><b>Logout</b></i></font>";
help(3, "Login.php?act=signout");
echo "</div>";
echo "</div>";
echo "<hr>";
echo "<h3>Attendance</h3>";
echo "<font face='Verdana' size='1' color='#000000'>";
echo "<div class='row'>";
echo "<div class='col-2'>";
echo "Pre - Approve Over Time";
help(4, "PreApproveOvertime.php");
echo "</div>";
echo "<div class='col-2'>";
echo "Approve Over Time (Details)";
help(5, "ApproveOvertime.php");
echo "</div>";
echo "<div class='col-2'>";
echo "Approve Over Time (Summary)";
help(6, "ApproveOvertimeSummary.php");
echo "</div>";
echo "<div class='col-2'>";
echo "Assign Project";
help(7, "AssignProject.php");
echo "</div>";
echo "<div class='col-2'>";
echo "Proxy Clocking";
help(8, "Proxy.php");
echo "</div>";
echo "<div class='col-2'>";
echo "Flag Day(s) (Pre)";
help(9, "OffDay.php");
echo "</div>";
echo "</div>";
echo "</font>";
echo "<br>";
echo "<font face='Verdana' size='1' color='#000000'>";
echo "<div class='row'>";
echo "<div class='col-2'>";
echo "Flag Day(s) (Post)";
help(11, "FlagDay.php");
echo "</div>";
echo "<div class='col-2'>";
echo "Alter Time";
help(10, "AlterTime.php");
echo "</div>";
echo "<div class='col-2'>";
echo "Delete Processed Record";
help(12, "DeleteProcessedRecord.php");
echo "</div>";
echo "<div class='col-2'>";
echo "Delete Pre-Flagged Un-Processed Record";
help(13, "DeletePreFlaggedRecord.php");
echo "</div>";
echo "</div>";
echo "</font>";
echo "<hr>";
echo "<h3>Settings</h3>";
echo "<font face='Verdana' size='1' color='#000000'>";
echo "<div class='row'>";
echo "<div class='col-2'>";
echo "User Management";
help(14, "UserManagement.php");
echo "</div>";
echo "<div class='col-2'>";
echo "Assign User Access to each Dept";
help(15, "AssignUserDept.php");
echo "</div>";
echo "<div class='col-2'>";
echo "Assign User Access to each Div";
help(55, "AssignUserDiv.php");
echo "</div>";
echo "<div class='col-2'>";
echo "Employees";
help(16, "EmployeeMaster.php");
echo "</div>";
echo "<div class='col-2'>";
echo "Shifts Definitions";
help(17, "ShiftMaster.php");
echo "</div>";
echo "<div class='col-2'>";
echo "Shift Rotation";
help(18, "ShiftRotation.php");
echo "</div>";
echo "</div>";
echo "<br>";
echo "<div class='row'>";
echo "<div class='col-2'>";
echo "Projects";
help(19, "ProjectMaster.php");
echo "</div>";
echo "<div class='col-2'>";
echo "Exit Terminals Definitions";
help(20, "ExitTerminal.php");
echo "</div>";
echo "<div class='col-2'>";
echo "Assign Terminals for each Dept";
help(21, "AssignTerminal.php");
echo "</div>";
echo "<div class='col-2'>";
echo "Assign Shifts";
help(22, "AssignShift.php");
echo "</div>";
echo "<div class='col-2'>";
echo "OT Days/Dates Definitions";
help(23, "OTDayDate.php");
echo "</div>";
echo "<div class='col-2'>";
echo "OT Days/Dates Exception";
help(24, "OTDayDateException.php");
echo "</div>";
echo "</div>";
echo "<br>";
echo "<div class='row'>";
echo "<div class='col-2'>";
echo "Special OT Days for Exempted Employees";
help(57, "OTEmployeeExemptOTDay.php");
echo "</div>";
echo "<div class='col-2'>";
echo "Global Settings";
help(25, "OtherSetting.php");
echo "</div>";
echo "</div>";
echo "</font>";
echo "<hr>";
echo "<h3>General Reports</h3>";
echo "<font face='Verdana' size='1' color='#000000'>";
echo "<div class='row'>";
echo "<div class='col-2'>";
echo "Daily Attendance";
help(26, "ReportAttendance.php");
echo "</div>";
echo "<div class='col-2'>";
echo "Late Arrival";
help(27, "ReportLateArrival.php");
echo "</div>";
echo "<div class='col-2'>";
echo "More Break";
help(28, "ReportMoreBreak.php");
echo "</div>";
echo "<div class='col-2'>";
echo "Early Exit";
help(29, "ReportEarlyExit.php");
echo "</div>";
echo "<div class='col-2'>";
echo "Absence";
help(30, "ReportAbsence.php");
echo "</div>";
echo "<div class='col-2'>";
echo "Projects";
help(31, "ReportProject.php");
echo "</div>";
echo "</div>";
echo "<br>";
echo "<div class='row'>";
echo "<div class='col-2'>";
echo "Exit Terminal Error";
help(32, "ReportExitTerminalError.php");
echo "</div>";
echo "<div class='col-2'>";
echo "Employees";
help(33, "ReportEmployee.php");
echo "</div>";
echo "<div class='col-2'>";
echo "Shift Rotation Log";
help(34, "ReportShiftRotation.php");
echo "</div>";
echo "<div class='col-2'>";
echo "Process Log";
help(35, "ReportProcessLog.php");
echo "</div>";
echo "</div>";
echo "</font>";
echo "<hr>";
echo "<h3>Clocking Reports</h3>";
echo "<div class='row'>";
echo "<div class='col-2'>";
echo "<font face='Verdana' size='1' color='#000000'>";
echo "Odd Log";
help(41, "ReportOddLog.php");
echo "</div>";
echo "<div class='col-2'>";
echo "Raw Log";
help(42, "ReportRawLog.php");
echo "</div>";
echo "<div class='col-2'>";
echo "Processed Log (Daily Routine)";
help(43, "ReportDailyClocking.php");
echo "</div>";
echo "<div class='col-2'>";
echo "Processed Log (Weekly Routine)";
help(44, "ReportWeeklyClocking.php");
echo "</div>";
echo "</div>";
echo "</font>";
echo "<hr>";
echo "<h3>HR Reports</h3>";
echo "<font face='Verdana' size='1' color='#000000'>";
echo "<div class='row'>";
echo "<div class='col-2'>";
echo "Work Report";
help(45, "ReportWork.php");
echo "</div>";
echo "<div class='col-2'>";
echo "Day Summary";
help(46, "ReportMonthlyAttendance.php");
echo "</div>";
echo "<div class='col-2'>";
echo "Hours Summary";
help(47, "ReportMonthlyHours.php");
echo "</div>";
echo "<div class='col-2'>";
echo "Shift Snapshot";
help(48, "ReportShiftSnapShot.php");
echo "</div>";
echo "<div class='col-2'>";
echo "Attendance Snapshot";
help(49, "ReportAttendanceSnapShot.php");
echo "</div>";
echo "</div>";
echo "</font>";
echo "<hr>";
echo "<h3>Module Reports</h3>";
echo "<font face='Verdana' size='1' color='#000000'>";
echo "<div class='row'>";
echo "<div class='col-2'>";
echo "Time Alterations (Incorrect Clockings)";
help(50, "ReportAlterTime.php");
echo "</div>";
echo "<div class='col-2'>";
echo "User Transactions";
help(51, "ReportUserTransact.php");
echo "</div>";
echo "<div class='col-2'>";
echo "User Information";
help(52, "ReportUserInfo.php");
echo "</div>";
echo "<div class='col-2'>";
echo "User Rights";
help(53, "ReportUserRight.php");
echo "</div>";
echo "<div class='col-2'>";
echo "User - Department Access Rights";
help(54, "ReportUserDept.php");
echo "</div>";
echo "<div class='col-2'>";
echo "User - Div Access Rights";
help(56, "ReportUserDiv.php");
echo "</div>";
echo "</div>";
echo "</form>";
echo "</form>";
echo "<div class='row'>";
echo "<div class='col-12'>";
if ($prints != "yes") {
    print "<center><br><input type='button' value='Print' class='btn btn-primary' onClick='checkPrint(0)'></center>";
}
echo "</div>";
echo "</div>";
print "</div></div></div></div></div>";
include 'footer.php';
echo "\t<script>\r\n\t\tfunction checkPrint(a){\r\n\t\t\tif (confirm('Go Green - Think Twice before you Print this Document \\nAre you sure want to Print?')){\r\n\t\t\t\tvar x = document.frm1;\r\n\t\t\t\tx.action = 'SiteMap.php?prints=yes';\t\t\t\r\n\t\t\t\tx.target = '_blank';\r\n\t\t\t\tx.submit();\t\t\t\r\n\t\t\t}else{\r\n\t\t\t\treturn;\r\n\t\t\t}\r\n\t\t}\r\n\t</script>\r\n</div></center></body></html>";

?>