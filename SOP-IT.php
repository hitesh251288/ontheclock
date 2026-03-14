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
echo "\r\n<html><head><title>SOP-IT</title>\r\n\t<link rel=\"shortcut icon\" href=\"favicon.ico\">\r\n</head>\r\n\t";
if ($prints != "yes") {
    print "<body><center><div align='center'>";
    displayHeader($prints);
    print "<center>";
    displayLinks(300, $userlevel);
    print "</center>";
    print "<br>";
    print "<font color='#FF0000' face='Verdana' size='2'><b>SOP-IT</b></font><br><br>";
    print "<form name='frm1' method='post'><table width='800' bgcolor='#C0C0C0'>";
} else {
    print "<body onLoad='javascript:window.print()'><center><div align='center'>";
    displayHeader($prints);
    print "<font color='#FF0000' face='Verdana' size='2'><b>SOP-IT</b></font><br><br>";
    print "<form name='frm1' method='post'><table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
}
echo "\t\r\n\t\t<tr>\r\n\t\t\t<td vAlign='top' bgcolor='#FFFFFF'>\r\n\t\t\t\t<font face='Verdana' size='2' color='#000000'>\r\n\t\t\t\t\t<i><b>Daily Routines</b></i>\r\n\t\t\t\t</font>\r\n\t\t\t\t<br>\r\n\t\t\t\t<font face='Verdana' size='1' color='#000000'>\r\n\t\t\t\t\t<li>Ensure Check List Completion\r\n\t\t\t\t\t<li>Ensure Proper Daily Processing of Data\r\n\t\t\t\t\t<li>Ensure Daily Backups\r\n\t\t\t\t\t<li>Ensure Timely Shift Rotations\r\n\t\t\t\t\t<li>Re Register and Upload Faulty Fingerprints\r\n\t\t\t\t\t<li>Ensure Terminal Connectivity and Regular upload of Raw Data to Server\t\t\t\t\t\r\n\t\t\t\t</font>\r\n\t\t\t</td>\r\n\t\t\t<td vAlign='top' bgcolor='#FFFFFF'>\r\n\t\t\t\t<font face='Verdana' size='2' color='#000000'>\r\n\t\t\t\t\t<i><b>Other Tasks</b></i>\r\n\t\t\t\t</font>\r\n\t\t\t\t<br>\r\n\t\t\t\t<font face='Verdana' size='1' color='#000000'>\r\n\t\t\t\t\t<li>Ensure Marking of Public Holidays prior to Processing\r\n\t\t\t\t\t<li>Mark Suspended/ Resigned Employees and Passive\r\n\t\t\t\t\t<li>For Data Restoration: Paste the last backup to the <b>restore</b> folder and execute the <b>Restore.bat</b> batch file\r\n\t\t\t\t\t<li>For Version Update: Download and Unzip the File <b>virdi.zip</b> - Paste the unzipped files to the Folder: <b>C:\\Program Files\\Apache Software Foundation\\Apache2.2\\htdocs\\virdi\\</b> - Run the File <b>VersionUpdate.bat</b>\r\n\t\t\t\t</font>\r\n\t\t\t</td>\r\n\t\t\t<td vAlign='top' bgcolor='#FFFFFF'>\r\n\t\t\t\t<font face='Verdana' size='2' color='#000000'>\r\n\t\t\t\t\t<i><b>Documents</b></i>\r\n\t\t\t\t</font>\r\n\t\t\t\t<br>\r\n\t\t\t\t<font face='Verdana' size='1' color='#000000'>\r\n\t\t\t\t\t<li><a href='docs/System-Topology.jpg' target='_blank'>System-Topology</a>\r\n\t\t\t\t\t<li><a href='docs/System-Diagram.jpg' target='_blank'>System-Diagram</a>\r\n\t\t\t\t\t<li><a href='docs/DataCOM-Setup.pdf' target='_blank'>DataCOM Server Setup</a>\r\n\t\t\t\t\t<li><a href='docs/DataCOM-Client-Setup.pdf' target='_blank'>DataCOM Client Setup</a>\r\n\t\t\t\t\t<li><a href='docs/FingerPlacement.gif' target='_blank'>Finger Placement</a>\r\n\t\t\t\t\t<li><a href='docs/External-Connectors.gif' target='_blank'>Unit External Connectors</a>\r\n\t\t\t\t\t<li><a href='docs/Terminal-Block.gif' target='_blank'>Door Terminal Connection Diagram</a>\r\n\t\t\t\t\t<li><a href='docs/Door-Phone.gif' target='_blank'>Door Phone Connection Diagram</a>\r\n\t\t\t\t\t<li><a href='docs/EM-Lock.gif' target='_blank'>Electro Magnetic Lock Connection Diagram</a>\r\n\t\t\t\t</font>\r\n\t\t\t</td>\r\n\t\t</tr>\r\n\t</table></form>\r\n\t";
if ($prints != "yes") {
    print "<br><input type='button' value='Print' onClick='checkPrint(0)'>";
}
echo "\t<script>\r\n\t\tfunction checkPrint(a){\r\n\t\t\tif (confirm('Go Green - Think Twice before you Print this Document \\nAre you sure want to Print?')){\r\n\t\t\t\tvar x = document.frm1;\r\n\t\t\t\tx.action = 'SOP-IT.php?prints=yes';\t\t\t\r\n\t\t\t\tx.target = '_blank';\r\n\t\t\t\tx.submit();\t\t\t\r\n\t\t\t}else{\r\n\t\t\t\treturn;\r\n\t\t\t}\r\n\t\t}\r\n\t</script>\r\n</div></center></body></html>";

?>