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
echo "\r\n<html><head><title>SOP-Timekeepers</title>\r\n\t<link rel=\"shortcut icon\" href=\"favicon.ico\">\r\n</head>\r\n\t";
if ($prints != "yes") {
    print "<body><center><div align='center'>";
    displayHeader($prints);
    print "<center>";
    displayLinks(300, $userlevel);
    print "</center>";
    print "<br>";
    print "<font color='#FF0000' face='Verdana' size='2'><b>SOP-Timekeepers</b></font><br><br>";
    print "<form name='frm1' method='post'><table width='800' bgcolor='#C0C0C0'>";
} else {
    print "<body onLoad='javascript:window.print()'><center><div align='center'>";
    displayHeader($prints);
    print "<font color='#FF0000' face='Verdana' size='2'><b>SOP-Timekeepers</b></font><br><br>";
    print "<form name='frm1' method='post'><table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
}
echo "\t\r\n\t\t<tr>\r\n\t\t\t<td vAlign='top' bgcolor='#FFFFFF'>\r\n\t\t\t\t<font face='Verdana' size='2' color='#000000'>\r\n\t\t\t\t\t<i><b>On Ground Routines</b></i>\r\n\t\t\t\t</font>\r\n\t\t\t\t<br>\r\n\t\t\t\t<font face='Verdana' size='1' color='#000000'>\r\n\t\t\t\t\t<li>Ensure Proper Queuing and Clocking of Employees\r\n\t\t\t\t\t<li>Check Fingerprint Placement for Rejected Employees\r\n\t\t\t\t\t<li>Check the wetness and dryness of the Fingerprint for Rejected Employees\r\n\t\t\t\t\t<li>Ensure to hang a clean cloth near the Terminal for Rejected Employees to wipe their Fingers\r\n\t\t\t\t\t<li>Ensure to clean the Glass Surface of Biometric Unit by a a clean dry white cloth atleast once a week\t\t\t\t\t\r\n\t\t\t\t</font>\r\n\t\t\t</td>\r\n\t\t\t<td vAlign='top' bgcolor='#FFFFFF'>\r\n\t\t\t\t<font face='Verdana' size='2' color='#000000'>\r\n\t\t\t\t\t<i><b>Daily Routines</b></i>\r\n\t\t\t\t</font>\r\n\t\t\t\t<br>\r\n\t\t\t\t<font face='Verdana' size='1' color='#000000'>\r\n\t\t\t\t\t<li>Print Daily Attendance Report and pass to respective HOD\r\n\t\t\t\t\t<li>Print Daily Late In Report and pass to respective HOD\r\n\t\t\t\t\t<li>Print Daily Absence Report and pass to respective HOD\r\n\t\t\t\t\t<li>Clear Time Alteration Logs\r\n\t\t\t\t\t<li>Print Processed Clocking and Work Reports for previous Day and pass to respective HOD\r\n\t\t\t\t\t<li>Assign Projects for previous Day\t\t\t\t\t\r\n\t\t\t\t</font>\r\n\t\t\t</td>\r\n\t\t\t<td vAlign='top' bgcolor='#FFFFFF'>\r\n\t\t\t\t<font face='Verdana' size='2' color='#000000'>\r\n\t\t\t\t\t<i><b>Documents</b></i>\r\n\t\t\t\t</font>\r\n\t\t\t\t<br>\r\n\t\t\t\t<font face='Verdana' size='1' color='#000000'>\r\n\t\t\t\t\t<li><a href='docs/FingerPlacement.gif' target='_blank'>Fingerprint Placement Image</a>\t\t\t\t\t\r\n\t\t\t\t</font>\r\n\t\t\t</td>\r\n\t\t</tr>\r\n\t</table></form>\r\n\t";
if ($prints != "yes") {
    print "<br><input type='button' value='Print' onClick='checkPrint(0)'>";
}
echo "\t<script>\r\n\t\tfunction checkPrint(a){\r\n\t\t\tif (confirm('Go Green - Think Twice before you Print this Document \\nAre you sure want to Print?')){\r\n\t\t\t\tvar x = document.frm1;\r\n\t\t\t\tx.action = 'SOP-Timekeepers.php?prints=yes';\t\t\t\r\n\t\t\t\tx.target = '_blank';\r\n\t\t\t\tx.submit();\t\t\t\r\n\t\t\t}else{\r\n\t\t\t\treturn;\r\n\t\t\t}\r\n\t\t}\r\n\t</script>\r\n</div></center></body></html>";

?>