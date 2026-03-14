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
echo "\r\n<html><head><title>Category Help</title>\r\n\t<link rel=\"shortcut icon\" href=\"favicon.ico\">\r\n</head>\r\n\t";
if ($prints != "yes") {
    print "<body><center><div align='center'>";
    displayHeader($prints);
    print "<center>";
    displayLinks(300, $userlevel);
    print "</center>";
    print "<br>";
    print "<font color='#FF0000' face='Verdana' size='2'><b>Category Help</b></font><br><br>";
    print "<form name='frm1' method='post'><table width='800' bgcolor='#C0C0C0'>";
} else {
    print "<body onLoad='javascript:window.print()'><center><div align='center'>";
    displayHeader($prints);
    print "<font color='#FF0000' face='Verdana' size='2'><b>Category Help</b></font><br><br>";
    print "<form name='frm1' method='post'><table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
}
echo "\t\r\n\t\t<tr>\r\n\t\t\t<td vAlign='top' bgcolor='#FFFFFF'>\r\n\t\t\t\t<font face='Verdana' size='2' color='#000000'>\r\n\t\t\t\t\t<i><b>System Users</b></i>\r\n\t\t\t\t</font>\r\n\t\t\t\t<b><font face='Verdana' size='1' color='#000000'>\r\n\t\t\t\t\t<br><br>Change Password\r\n\t\t\t\t\t";
help(1, "Password.php");
echo "\t\t\t\t\t<br><br>User Management\r\n\t\t\t\t\t";
help(14, "UserManagement.php");
echo "\t\t\t\t\t<br><br>Assign User Access to each Dept\r\n\t\t\t\t\t";
help(15, "AssignUserDept.php");
echo "\t\t\t\t\t<br><br>Assign User Access to each Div\r\n\t\t\t\t\t";
help(55, "AssignUserDiv.php");
echo "\t\t\t\t\t<br><br>R: User Transactions\r\n\t\t\t\t\t";
help(51, "ReportUserTransact.php");
echo "\t\t\t\t\t<br><br>R: User Information\r\n\t\t\t\t\t";
help(52, "ReportUserInfo.php");
echo "\t\t\t\t\t<br><br>R: User Rights\r\n\t\t\t\t\t";
help(53, "ReportUserRight.php");
echo "\t\t\t\t\t<br><br>R: User - Department Access Rights\r\n\t\t\t\t\t";
help(54, "ReportUserDept.php");
echo "\t\r\n\t\t\t\t\t<br><br>R: User - Div Access Rights\r\n\t\t\t\t\t";
help(56, "ReportUserDiv.php");
echo "\t\r\n\t\t\t\t\t<br><br>R: Time Alterations (Incorrect Clockings)\r\n\t\t\t\t\t";
help(50, "ReportAlterTime.php");
echo "\t\t\t\t</font></b>\r\n\t\t\t</td>\r\n\t\t\t<td vAlign='top' bgcolor='#FFFFFF'>\r\n\t\t\t\t<font face='Verdana' size='2' color='#000000'>\r\n\t\t\t\t\t<i><b>Employees</b></i>\r\n\t\t\t\t</font>\r\n\t\t\t\t<b><font face='Verdana' size='1' color='#000000'>\r\n\t\t\t\t\t<br><br>Employees\r\n\t\t\t\t\t";
help(16, "EmployeeMaster.php");
echo "\t\t\t\t\t<br><br>Projects\r\n\t\t\t\t\t";
help(19, "ProjectMaster.php");
echo "\t\t\t\t\t<br><br>Assign Project\r\n\t\t\t\t\t";
help(7, "AssignProject.php");
echo "\t\t\t\t\t<br><br>Assign Shifts\r\n\t\t\t\t\t";
help(22, "AssignShift.php");
echo "\t\t\t\t\t<br><br>Proxy Clocking\r\n\t\t\t\t\t";
help(8, "Proxy.php");
echo "\t\t\t\t\t<br><br>Alter Time\r\n\t\t\t\t\t";
help(10, "AlterTime.php");
echo "\t\t\t\t\t<br><br>OT Days/Dates Exception\r\n\t\t\t\t\t";
help(24, "OTDayDateException.php");
echo "\t\t\t\t\t<br><br>Special OT Days for Exempted Employees\r\n\t\t\t\t\t";
help(57, "OTEmployeeExemptOTDay.php");
echo "\t\t\t\t\t<br><br>R: Employees\r\n\t\t\t\t\t";
help(33, "ReportEmployee.php");
echo "\t\t\t\t\t<br><br>R: Projects\r\n\t\t\t\t\t";
help(31, "ReportProject.php");
echo "\t\t\t\t\t<br><br>R: Time Alterations (Incorrect Clockings)\r\n\t\t\t\t\t";
help(50, "ReportAlterTime.php");
echo "\t\t\t\t</font></b>\r\n\t\t\t</td>\r\n\t\t\t<td vAlign='top' bgcolor='#FFFFFF'>\r\n\t\t\t\t<font face='Verdana' size='2' color='#000000'>\r\n\t\t\t\t\t<i><b>Shifts</b></i>\r\n\t\t\t\t</font>\r\n\t\t\t\t<b><font face='Verdana' size='1' color='#000000'>\r\n\t\t\t\t\t<br><br>Shifts Definitions\r\n\t\t\t\t\t";
help(17, "ShiftMaster.php");
echo "\t\t\t\t\t<br><br>Shift Rotation\r\n\t\t\t\t\t";
help(18, "ShiftRotation.php");
echo "\t\t\t\t\t<br><br>Assign Shifts\r\n\t\t\t\t\t";
help(22, "AssignShift.php");
echo "\t\t\t\t\t<br><br>R: Shift Rotation Log\r\n\t\t\t\t\t";
help(34, "ReportShiftRotation.php");
echo "\t\t\t\t</font></b>\r\n\t\t\t</td>\r\n\t\t\t<td vAlign='top' bgcolor='#FFFFFF'>\r\n\t\t\t\t<font face='Verdana' size='2' color='#000000'>\r\n\t\t\t\t\t<i><b>Terminals</b></i>\r\n\t\t\t\t</font>\r\n\t\t\t\t<b><font face='Verdana' size='1' color='#000000'>\r\n\t\t\t\t\t<br><br>Exit Terminals Definitions\r\n\t\t\t\t\t";
help(20, "ExitTerminal.php");
echo "\t\t\t\t\t<br><br>Assign Terminals for each Dept\r\n\t\t\t\t\t";
help(21, "AssignTerminal.php");
echo "\t\t\t\t\t\r\n\t\t\t\t\t<br><br>R: Exit Terminal Error\r\n\t\t\t\t\t";
help(32, "ReportExitTerminalError.php");
echo "\t\t\t\t</font></b>\r\n\t\t\t</td>\r\n\t\t\t<td vAlign='top' bgcolor='#FFFFFF'>\r\n\t\t\t\t<font face='Verdana' size='2' color='#000000'>\r\n\t\t\t\t\t<i><b>OT/ Flags</b></i>\r\n\t\t\t\t</font>\r\n\t\t\t\t<b><font face='Verdana' size='1' color='#000000'>\r\n\t\t\t\t\t<br><br>Pre - Approve Over Time\r\n\t\t\t\t\t";
help(4, "PreApproveOvertime.php");
echo "\t\t\t\t\t<br><br>Approve Over Time (Details)\r\n\t\t\t\t\t";
help(5, "ApproveOvertime.php");
echo "\t\t\t\t\t<br><br>Approve Over Time (Summary)\r\n\t\t\t\t\t";
help(6, "ApproveOvertimeSummary.php");
echo "\t\t\t\t\t\r\n\t\t\t\t\t<br><br>Flag Day(s) (Pre)\r\n\t\t\t\t\t";
help(9, "OffDay.php");
echo "\t\t\t\t\t<br><br>Flag Day(s) (Post)\r\n\t\t\t\t\t";
help(11, "FlagDay.php");
echo "\t\t\t\t\t<br><br>Mark OT Days/ Dates\r\n\t\t\t\t\t";
help(23, "OTDayDate.php");
echo "\t\t\t\t\t<br><br>OT Days/Dates Exception\r\n\t\t\t\t\t";
help(24, "OTDayDateException.php");
echo "\t\t\t\t\t<br><br>Special OT Days for Exempted Employees\r\n\t\t\t\t\t";
help(57, "OTEmployeeExemptOTDay.php");
echo "\t\t\t\t\t<br><br>Mark Flag Colours\r\n\t\t\t\t\t";
help(25, "OtherSetting.php");
echo "\t\t\t\t</font></b>\r\n\t\t\t</td>\t\t\t\r\n\t\t</tr>\r\n\t</table></form>\r\n\t";
if ($prints != "yes") {
    print "<br><input type='button' value='Print' onClick='checkPrint(0)'>";
}
echo "\t<script>\r\n\t\tfunction checkPrint(a){\r\n\t\t\tif (confirm('Go Green - Think Twice before you Print this Document \\nAre you sure want to Print?')){\r\n\t\t\t\tvar x = document.frm1;\r\n\t\t\t\tx.action = 'CategoryHelp.php?prints=yes';\t\t\t\r\n\t\t\t\tx.target = '_blank';\r\n\t\t\t\tx.submit();\t\t\t\r\n\t\t\t}\r\n\t\t}\r\n\t</script>\r\n</div></center></body></html>";

?>