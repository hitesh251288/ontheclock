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
echo "\r\n<html><head><title>SOP-HOD</title>\r\n\t<link rel=\"shortcut icon\" href=\"favicon.ico\">\r\n</head>\r\n\t";
if ($prints != "yes") {
    print "<body><center><div align='center'>";
    displayHeader($prints);
    print "<center>";
    displayLinks(300, $userlevel);
    print "</center>";
    print "<br>";
    print "<font color='#FF0000' face='Verdana' size='2'><b>SOP-HOD</b></font><br><br>";
    print "<form name='frm1' method='post'><table width='800' bgcolor='#C0C0C0'>";
} else {
    print "<body onLoad='javascript:window.print()'><center><div align='center'>";
    displayHeader($prints);
    print "<font color='#FF0000' face='Verdana' size='2'><b>SOP-HOD</b></font><br><br>";
    print "<form name='frm1' method='post'><table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
}
echo "\t\r\n\t\t<tr>\r\n\t\t\t<td vAlign='top' bgcolor='#FFFFFF'>\r\n\t\t\t\t<font face='Verdana' size='2' color='#000000'>\r\n\t\t\t\t\t<i><b>Daily Routines</b></i>\r\n\t\t\t\t</font>\r\n\t\t\t\t<br>\r\n\t\t\t\t<font face='Verdana' size='1' color='#000000'>\r\n\t\t\t\t\t<li>Receive/ Check Daily Attendance Report\r\n\t\t\t\t\t<li>Receive/ Check Late In Report\r\n\t\t\t\t\t<li>Receive/ Check Daily Absence Report\r\n\t\t\t\t\t<li>Approve Overtime for Previous Day\r\n\t\t\t\t\t<li>Pre Approve Overtime for Next Day\r\n\t\t\t\t\t<li>Ensure/ Instruct to HR about Shift Assignment for Employees changing Shifts\r\n\t\t\t\t</font>\r\n\t\t\t</td>\t\t\t\r\n\t\t</tr>\r\n\t</table></form>\r\n\t";
if ($prints != "yes") {
    print "<br><input type='button' value='Print' onClick='checkPrint(0)'>";
}
echo "\t<script>\r\n\t\tfunction checkPrint(a){\r\n\t\t\tif (confirm('Go Green - Think Twice before you Print this Document \\nAre you sure want to Print?')){\r\n\t\t\t\tvar x = document.frm1;\r\n\t\t\t\tx.action = 'SOP-HOD.php?prints=yes';\t\t\t\r\n\t\t\t\tx.target = '_blank';\r\n\t\t\t\tx.submit();\t\t\t\r\n\t\t\t}else{\r\n\t\t\t\treturn;\r\n\t\t\t}\r\n\t\t}\r\n\t</script>\r\n</div></center></body></html>";

?>