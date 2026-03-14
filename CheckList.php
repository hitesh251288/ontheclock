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
echo "\r\n<html><head><title>CheckList</title>\r\n\t<link rel=\"shortcut icon\" href=\"favicon.ico\">\r\n</head>\r\n\t";
if ($prints != "yes") {
    print "<body><center><div align='center'>";
    displayHeader($prints);
    print "<center>";
    displayLinks(300, $userlevel);
    print "</center>";
    print "<br>";
    print "<font color='#FF0000' face='Verdana' size='2'><b>Check List</b></font><br><br>";
    print "<p align='center'><a href='docs/Questionnaire 01.pdf' target='_blank'><font color='#000000' face='Verdana' size='2'><b>Questionnaire 01</b></font></a> &nbsp;&nbsp;<a href='docs/Questionnaire 02.pdf' target='_blank'><font color='#000000' face='Verdana' size='2'><b>Questionnaire 02</b></font></a> &nbsp;&nbsp;<a href='docs/Questionnaire 03.pdf' target='_blank'><font color='#000000' face='Verdana' size='2'><b>Questionnaire 03</b></font></a> &nbsp;&nbsp;<a href='docs/Questionnaire 04.pdf' target='_blank'><font color='#000000' face='Verdana' size='2'><b>Questionnaire 04</b></font></a></p>";
    print "<form name='frm1' method='post'><table width='800' bgcolor='#C0C0C0'>";
} else {
    print "<body onLoad='javascript:window.print()'><center><div align='center'>";
    displayHeader($prints);
    print "<font color='#FF0000' face='Verdana' size='2'><b>Check List</b></font><br><br>";
    print "<p align='center'><a href='docs/Questionnaire 01.pdf' target='_blank'><font color='#000000' face='Verdana' size='2'><b>Questionnaire 01</b></font></a> &nbsp;&nbsp;<a href='docs/Questionnaire 02.pdf' target='_blank'><font color='#000000' face='Verdana' size='2'><b>Questionnaire 02</b></font></a> &nbsp;&nbsp;<a href='docs/Questionnaire 03.pdf' target='_blank'><font color='#000000' face='Verdana' size='2'><b>Questionnaire 03</b></font></a> &nbsp;&nbsp;<a href='docs/Questionnaire 04.pdf' target='_blank'><font color='#000000' face='Verdana' size='2'><b>Questionnaire 04</b></font></a></p>";
    print "<form name='frm1' method='post'><table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
}
echo "\t\r\n\t\t<tr>\r\n\t\t\t<td vAlign='top' bgcolor='#FFFFFF'>\r\n\t\t\t\t<font face='Verdana' size='2' color='#000000'>\r\n\t\t\t\t\t<i><b>Attendance Unit</b></i>\r\n\t\t\t\t</font>\r\n\t\t\t\t<br>\r\n\t\t\t\t<font face='Verdana' size='1' color='#000000'>\r\n\t\t\t\t\t<li>IP Addresses\r\n\t\t\t\t\t<li>Network Mode [SN/NS/NO]\r\n\t\t\t\t\t<li>Set Verify Option to - Display Employee ID/ Name\t\t\t\t\t\r\n\t\t\t\t\t<li>Disable Door Lock Trigger\r\n\t\t\t\t\t<li>Enable Back Light\r\n\t\t\t\t\t<li>Check Voice Prompt\t\r\n\t\t\t\t\t<li>FP Sensor Level\r\n\t\t\t\t\t<li>Employee Code Length\r\n\t\t\t\t\t<li>Create Terminal Admin with an ID and Password\r\n\t\t\t\t</font>\r\n\t\t\t</td>\r\n\t\t\t<td vAlign='top' bgcolor='#FFFFFF'>\r\n\t\t\t\t<font face='Verdana' size='2' color='#000000'>\r\n\t\t\t\t\t<i><b>Remote Access Manager</b></i>\r\n\t\t\t\t</font>\r\n\t\t\t\t<font face='Verdana' size='1' color='#000000'>\r\n\t\t\t\t\t<br>\r\n\t\t\t\t<font face='Verdana' size='1' color='#000000'>\r\n\t\t\t\t\t<li>Set No of Fingerprints to be Registered for each Employee \r\n\t\t\t\t\t<li>Set Employee Code Length\r\n\t\t\t\t\t<li>Set Group Code Length\r\n\t\t\t\t\t<li>Set Terminal Length\r\n\t\t\t\t\t<li>Set Visitor ID Range FROM 9999999 TO 9999999\r\n\t\t\t\t\t<li>Set Automatic Synch of changed User Info to Immediate Synchronization\r\n\t\t\t\t\t<li>Add Terminals with option <b>Allow Access to All Users</b> selected\r\n\t\t\t\t\t<li>Create Groups\r\n\t\t\t\t\t<li>Download users from Terminals and Synchronize\r\n\t\t\t\t\t<li>Create Server Admin with an ID and Password\r\n\t\t\t\t</font>\r\n\t\t\t</td>\r\n\t\t\t<td vAlign='top' bgcolor='#FFFFFF'>\r\n\t\t\t\t<font face='Verdana' size='2' color='#000000'>\r\n\t\t\t\t\t<i><b>Settings</b></i>\r\n\t\t\t\t</font>\r\n\t\t\t\t<font face='Verdana' size='1' color='#000000'>\r\n\t\t\t\t\t<br>\r\n\t\t\t\t<font face='Verdana' size='1' color='#000000'>\r\n\t\t\t\t\t<li>Create System Users and Assign Rights \r\n\t\t\t\t\t<li>Mark OT Days and Dates\r\n\t\t\t\t\t<li>Set the Shift Time and other Properties\r\n\t\t\t\t\t<li>Run the Employee XML to input ALL Data OR do them manually\r\n\t\t\t\t\t<li>Assign Shifts to All Employees\r\n\t\t\t\t\t<li>Assign Terminals to all Departments\r\n\t\t\t\t\t<li>Set the Min Clocking Time for the same Employee\r\n\t\t\t\t\t<li>Set Exit Terminal Option\r\n\t\t\t\t\t<li>Set Shift Rotation Option and Shift Rotation Cycles\r\n\t\t\t\t\t<li>Set Backup Path (Preferably an external USB Drive or a Network Drive)\r\n\t\t\t\t\t<li>Relate the Flags with the Leaves/ Suspension/ Public Holidays\r\n\t\t\t\t</font>\t\t\t\t\r\n\t\t\t</td>\r\n\t\t\t<td vAlign='top' bgcolor='#FFFFFF'>\r\n\t\t\t\t<font face='Verdana' size='2' color='#000000'>\r\n\t\t\t\t\t<i><b>Batch Process</b></i>\r\n\t\t\t\t</font>\r\n\t\t\t\t<font face='Verdana' size='1' color='#000000'>\r\n\t\t\t\t\t<br>\r\n\t\t\t\t<font face='Verdana' size='1' color='#000000'>\r\n\t\t\t\t\t<li>DayMaster\r\n\t\t\t\t\t<li>Backup\r\n\t\t\t\t\t<li>RotateShift\r\n\t\t\t\t\t<li>MailerAttendance\r\n\t\t\t\t\t<li>MailerAbsence\r\n\t\t\t\t\t<li>MailerLateArrival\r\n\t\t\t\t\t<li>MailerEarlyExit\r\n\t\t\t\t\t<li>WeekMaster\r\n\t\t\t\t</font>\t\t\t\t\r\n\t\t\t</td>\t\t\t\r\n\t\t</tr>\r\n\t</table></form>\r\n\t";
if ($prints != "yes") {
    print "<br><input type='button' value='Print' onClick='checkPrint(0)'>";
}
echo "\t<script>\r\n\t\tfunction checkPrint(a){\r\n\t\t\tif (confirm('Go Green - Think Twice before you Print this Document \\nAre you sure want to Print?')){\r\n\t\t\t\tvar x = document.frm1;\r\n\t\t\t\tx.action = 'CheckList.php?prints=yes';\t\t\t\r\n\t\t\t\tx.target = '_blank';\r\n\t\t\t\tx.submit();\t\t\t\r\n\t\t\t}else{\r\n\t\t\t\treturn;\r\n\t\t\t}\r\n\t\t}\r\n\t</script>\r\n</div></center></body></html>";

?>