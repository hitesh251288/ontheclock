<?php


ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$username = $_SESSION[$session_variable . "username"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$userstatus = $_SESSION[$session_variable . "userstatus"];
$maxOut = $_SESSION[$session_variable . "NightShiftMaxOutTime"];
$VirdiLevel = $_SESSION[$session_variable . "VirdiLevel"];
$date = insertToday();
if ($username == "") {
    header("Location: " . $config["REDIRECT"]);
}
$conn = openConnection();
$act = $_GET["act"];
$message = "This Page will AUTO-REFRESH every 15 Minutes";
echo "\r\n<html><head><title>Dashboard - DataCOM Time and Attendance</title></head>\r\n<script>\r\n\tfunction checkPassword(){\r\n\t\tx = document.frm1;\r\n\t\tif (x.txtPassword.value == '' || x.txtNewPassword.value == '' || x.txtNewPassword.value != x.txtNewPasswordRepeat.value){\r\n\t\t\talert('Passwords cannot be blank. New Password values should be the same.');\r\n\t\t\tx.txtPassword.focus();\r\n\t\t}else{\r\n\t\t\tx.submit();\r\n\t\t}\r\n\t}\r\n</script>\r\n<meta http-equiv=\"refresh\" content=\"900;url=Welcome.php\">\r\n<body><center><div align='center'>\r\n\t";
displayHeader($prints);
print "<center>";
displayLinks(100, $userlevel);
print "</center>";
print "<table width='800'>";
print "<tr><td width='100%' colspan='3' align='center'><font face='Verdana' size='1' color='#6481BD'><b>" . $message . "</b></font></td></tr>";
print "<tr><td width='100%' colspan='3' align='center'><font face='Verdana' size='5' color='#6481BD'><b>&nbsp;</b></font></td></tr>";
print "<tr>";
print "<td vAlign='top' width='30%' bgcolor='#F0F0F0'><font face='Verdana' size='2'>";
print "<u><b>My Profile</b></u>";
$query = "SELECT COUNT(TransactID) FROM Transact WHERE Username = '" . $username . "' AND Transactquery LIKE '%Logged In%'";
$result = selectData($conn, $query);
print "<br><br>Login(s): <b>" . $result[0] . "</b>";
$query = "SELECT COUNT(LogID) FROM AlterLog WHERE Username = '" . $username . "'";
$result = selectData($conn, $query);
addComma($result[0]);
print "<br><br>Time Alteration(s): <b>" . addComma($result[0]) . "</b>";
$query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster WHERE EmployeeID = '" . $username . "'";
$result = selectData($conn, $query);
addComma($result[0]);
print "<br><br>Day(s) Present: <b>" . addComma($result[0]) . "</b>";
if ($VirdiLevel == "Classic") {
    print "<br><br><br><b><u>Pending User-Terminal Synch</u></b><br>";
    $query = "SELECT c_gid, COUNT(*) FROM tcommand GROUP BY c_gid";
    $result = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
        addComma($cur[1]);
        print $cur[0] . ": " . addComma($cur[1]) . "<br>";
    }
}
print "</font></td>";
print "<td vAlign='top' width='35%' bgcolor='#E0E0E0'><font face='Verdana' size='2'>";
print "<u><b>Company Profile</b></u>";
$query = "SELECT COUNT(id) FROM tuser WHERE tuser.PassiveType = 'ACT'";
$result = selectData($conn, $query);
$atotal = $result[0];
print "<br><br>Active Employee(s): <b>" . $atotal . "</b>";
$query = "SELECT COUNT(id) FROM tuser WHERE tuser.PassiveType = 'FDA' OR tuser.PassiveType = 'ADA'";
$result = selectData($conn, $query);
$p1total = $result[0];
print "<br><br>De-Activated <font size='1'>(<a title='Unauthorized Absence DeActivation'>ADA</a>/<a title='Flagged DeActivation'>FDA</a>)</font>: <b>" . $p1total . "</b>";
$query = "SELECT COUNT(id) FROM tuser WHERE tuser.PassiveType = 'PRM' OR tuser.PassiveType = 'RSN' OR tuser.PassiveType = 'RTD' OR tuser.PassiveType = 'TRM'";
$result = selectData($conn, $query);
$p2total = $result[0];
print "<br><br>In-Active <font size='1'>(<a title='Promoted'>PRM</a>/<a title='Resigned'>RSN</a>/<a title='Retired'>RTD</a>/<a title='Terminated'>TRM</a>)</font>: <b>" . $p2total . "</b>";
$query = "SELECT COUNT(id) FROM tuser WHERE tuser.PassiveType NOT LIKE 'ACT'";
$result = selectData($conn, $query);
$ptotal = $result[0];
print "<br><br>Total Passive Employee(s): <b>" . $ptotal . "</b>";
if ($VirdiLevel == "Classic") {
    $query = "SELECT COUNT(id) FROM tuser WHERE LENGTH(cardnum) > 1";
    $result = selectData($conn, $query);
    $card = $result[0];
    print "<br><br>Employee(s) (Cards): <b>" . $card . "</b>";
    print "<br><br>Employee(s) (Finger Prints): <b>" . ($atotal + $ptotal - $card) . "</b>";
}
$query = "SELECT COUNT(id) FROM tuser WHERE group_id < 2";
$result = selectData($conn, $query);
print "<br><br>Employee(s) with Unassigned/ Assign All Shifts: <b>" . $result[0] . "</b>";
$query = "SELECT COUNT(DISTINCT(dept)) FROM tuser WHERE dept NOT LIKE ''";
$result = selectData($conn, $query);
print "<br><br>Department(s): <b>" . $result[0] . "</b>";
$query = "SELECT COUNT(DISTINCT(company)) FROM tuser WHERE company NOT LIKE ''";
$result = selectData($conn, $query);
print "<br><br>Division(s): <b>" . $result[0] . "</b>";
$query = "SELECT COUNT(id) FROM tgroup WHERE id > 1";
$result = selectData($conn, $query);
print "<br><br>Shift(s): <b>" . $result[0] . "</b>";
$query = "SELECT COUNT(Username)-1 FROM Usermaster";
$result = selectData($conn, $query);
print "<br><br>System User(s): <b>" . $result[0] . "</b>";
print "</font></td>";
print "<td vAlign='top' width='35%' bgcolor='#F0F0F0'><font face='Verdana' size='2'>";
print "<u><b>Clocking Profile</b></u>";
$query = "SELECT COUNT(DISTINCT(e_id)) FROM tenter, tgroup, tgate WHERE tenter.e_date = '" . $date . "' AND tenter.e_group = tgroup.id AND tgroup.NightFlag = 0 AND tenter.g_id = tgate.id AND tgate.Exit = 0 AND (tenter.e_etc NOT LIKE 'P' OR tenter.e_etc IS NULL) AND e_id IN (SELECT id from tuser WHERE tuser.datelimit LIKE 'N%' OR (tuser.datelimit LIKE 'Y%' AND SUBSTRING(tuser.datelimit, 10, 8) >= '" . $date . "'))";
$result = selectData($conn, $query);
print "<br><br>Today's Day Shift(s) Attendance: <b>" . $result[0] . "</b>";
$last = strtotime(substr($date, 6, 2) . "-" . substr($date, 4, 2) . "-" . substr($date, 0, 4) . " - 1 day");
$a = getDate($last);
$m = $a["mon"];
if ($m < 10) {
    $m = "0" . $m;
}
$d = $a["mday"];
if ($d < 10) {
    $d = "0" . $d;
}
$lastDay = $a["year"] . $m . $d;
$query = "SELECT COUNT(DISTINCT(e_id)) FROM tenter, tgroup, tgate WHERE tenter.e_date = '" . $lastDay . "' AND e_time > '" . $maxOut . "00' AND tenter.e_group = tgroup.id AND tgroup.NightFlag = 1 AND tenter.g_id = tgate.id AND tgate.Exit = 0 AND (tenter.e_etc NOT LIKE 'P' OR tenter.e_etc IS NULL) AND e_id IN (SELECT id from tuser WHERE tuser.datelimit LIKE 'N%' OR (tuser.datelimit LIKE 'Y%' AND SUBSTRING(tuser.datelimit, 10, 8) >= '" . $date . "'))";
$result = selectData($conn, $query);
print "<br><br>Last Night Shift(s) Attendance: <b>" . $result[0] . "</b>";
$flagged = 0;
if ($VirdiLevel == "Classic") {
    $query = "SELECT COUNT(FlagDayRotationID) FROM FlagDayRotation, tuser WHERE e_date = '" . $date . "' AND FlagDayRotation.e_id = tuser.id AND tuser.PassiveType = 'ACT'";
    $result = selectData($conn, $query);
    print "<br><br>Today's Flagged Attendance: <b>" . $result[0] . "</b>";
}
$absent = 0;
$query = "SELECT count(tuser.id) FROM tuser, tgroup WHERE tuser.reg_date < '" . $date . "0000' AND tuser.group_id = tgroup.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
$query = $query . " AND (tuser.datelimit LIKE 'N%' OR (tuser.datelimit LIKE 'Y%' AND SUBSTRING(tuser.datelimit, 10, 8) >= '" . $date . "'))";
$query = $query . " AND tuser.id NOT IN (SELECT e_id FROM FlagDayRotation WHERE e_date = '" . $date . "' AND e_id IN (SELECT id from tuser WHERE tuser.datelimit LIKE 'N%' OR (tuser.datelimit LIKE 'Y%' AND SUBSTRING(tuser.datelimit, 10, 8) >= '" . $date . "'))) ";
$query = $query . " AND tuser.id NOT IN (SELECT DISTINCT(tenter.e_id) FROM tenter, tgate, tgroup WHERE tenter.g_id = tgate.id AND tenter.e_group = tgroup.id AND tgate.exit = 0 AND ((tenter.e_time > '" . $maxOut . "00' AND tgroup.NightFlag = 1) OR (tenter.e_time > '000000' AND tgroup.NightFlag = 0))";
$query = $query . " AND tenter.e_date = '" . $date . "')";
$result = selectData($conn, $query);
$absent = $result[0];
print "<br><br>Today's Day Shift(s) Absence: <b>" . $absent . "</b>";
$query = "SELECT COUNT(id) FROM tgate WHERE id > 0";
$result = selectData($conn, $query);
print "<br><br>Terminal(s): <b>" . $result[0] . "</b>";
$query = "SELECT COUNT(ed) FROM tenter";
$result = selectData($conn, $query);
addComma($result[0]);
print "<br><br>Raw Log(s): <b>" . addComma($result[0]) . "</b>";
$query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster";
$result = selectData($conn, $query);
addComma($result[0]);
print "<br><br>Processed Record(s): <b>" . addComma($result[0]) . "</b>";
$query = "SELECT COUNT(LogID) FROM AlterLog";
$result = selectData($conn, $query);
addComma($result[0]);
print "<br><br>Time Alteration(s): <b>" . addComma($result[0]) . "</b>";
print "</font></td>";
print "</tr>";
print "</table>";
echo "\t\t\r\n\t\t\r\n\t\r\n</div></center></body></html>";

?>