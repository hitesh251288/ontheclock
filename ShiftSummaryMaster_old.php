<?php


ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "12";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!(checkSession($userlevel, $current_module) && $_SESSION[$session_variable . "VirdiLevel"] == "Classic")) {
    header("Location: " . $config["REDIRECT"] . "?url=ShiftSummaryMaster.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$iconn = openIConnection();
print "<html><title>Shift Settings - Summary</title><body><center>";
displayHeader($prints, false, false);
print "<center>";
displayLinks($current_module, $userlevel);
print "</center>";
echo "\r\n<style>\r\n.f1 {color: #000000;font-size: 12px;font-family:\"Verdana\";font-weight:\"bold\";}\r\n.f2 {color: #000000;font-size: 9px;font-family:\"Verdana\";}\r\n.f2b {color: #000000;font-size: 9px;font-family:\"Verdana\";font-weight:\"bold\";}\r\n</style>\r\n\r\n";
$act = $_POST["act"];
if ($act == "") {
    $act = $_GET["act"];
}
$message = $_GET["message"];
if ($message == "") {
    $message = "Shift Settings - Summary";
}
if ($act == "editRecord") {
    $count = $_POST["txhShiftCounter"];
    for ($i = 0; $i < $count; $i++) {
        if ($_POST["chk" . $i] != "") {
            $query = "UPDATE tgroup SET EarlyInOT = '" . $_POST["lstEarlyInOT" . $i] . "', EarlyInOTDayDate = '" . $_POST["lstEarlyInOTDayDate" . $i] . "', LessLunchOT = '" . $_POST["lstLessLunchOT" . $i] . "', NoBreakException = '" . $_POST["lstNoBreakException" . $i] . "', NoBreakExceptionOT = '" . $_POST["lstNoBreakExceptionOT" . $i] . "', AccessRestrict = '" . $_POST["lstAccessRestrict" . $i] . "', RelaxRestrict = '" . $_POST["lstRelaxRestrict" . $i] . "', StartHour = '" . $_POST["txtStartHour" . $i] . "', CloseHour = '" . $_POST["txtCloseHour" . $i] . "', MinWorkForBreak = '" . $_POST["txtMinWorkForBreak" . $i] . "', MinOTWorkForBreak = '" . $_POST["txtMinOTWorkForBreak" . $i] . "', MinOT1Work = '" . $_POST["txtMinOT1Work" . $i] . "', MinOTValue = '" . $_POST["txtMinOTValue" . $i] . "', MaxOTValue = '" . $_POST["txtMaxOTValue" . $i] . "', MaxOTValueOT1 = '" . $_POST["txtMaxOTValueOT1" . $i] . "', MaxOTValueOT2 = '" . $_POST["txtMaxOTValueOT2" . $i] . "', ASAbsent = '" . $_POST["txtASAbsent" . $i] . "', OT1RF = '" . $_POST["txtOT1RF" . $i] . "', NSOTCO = '" . $_POST["txtNSOTCO" . $i] . "', ExemptOT = '" . $_POST["lstExemptOT" . $i] . "', ProxyOT = '" . $_POST["lstProxyOT" . $i] . "', ExemptLI = '" . $_POST["lstExemptLI" . $i] . "' WHERE id = " . $_POST["chk" . $i];
            updateIData($iconn, $query, true);
            $text = "Updated Shift ID: " . $txtID . " SET EarlyInOT = " . $_POST["lstEarlyInOT" . $i] . ", EarlyInOTDayDate = " . $_POST["lstEarlyInOTDayDate" . $i] . ", LessLunchOT = " . $_POST["lstLessLunchOT" . $i] . ", NoBreakException = " . $_POST["lstNoBreakException" . $i] . ", NoBreakExceptionOT = " . $_POST["lstNoBreakExceptionOT" . $i] . ", AccessRestrict = " . $_POST["lstAccessRestrict" . $i] . ", RelaxRestrict = " . $_POST["lstRelaxRestrict" . $i] . ", StartHour = " . $_POST["txtStartHour" . $i] . ", CloseHour = " . $_POST["txtCloseHour" . $i] . ", MinWorkForBreak = " . $_POST["txtMinWorkForBreak" . $i] . ", MinOTWorkForBreak = " . $_POST["txtMinOTWorkForBreak" . $i] . ", MinOT1Work = " . $_POST["txtMinOT1Work" . $i] . ", MinOTValue = " . $_POST["txtMinOTValue" . $i] . ", MaxOTValue = " . $_POST["txtMaxOTValue" . $i] . ", MaxOTValueOT1 = " . $_POST["txtMaxOTValueOT1" . $i] . ", MaxOTValueOT2 = " . $_POST["txtMaxOTValueOT2" . $i] . ", ASAbsent = " . $_POST["txtASAbsent" . $i] . ", OT1RF = " . $_POST["txtOT1RF" . $i] . ", NSOTCO = " . $_POST["txtNSOTCO" . $i] . ", ExemptOT = " . $_POST["lstExemptOT" . $i] . ", ProxyOT = " . $_POST["lstProxyOT" . $i] . ", ExemptLI = " . $_POST["lstExemptLI" . $i];
            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
            updateIData($iconn, $query, true);
        }
    }
    header("Location: ShiftSummaryMaster.php?message=Shift(s) updated");
}
echo "<script>\r\nfunction isNumber(x, y, min, max, len){\r\n\ta = x.value;\r\n\tif (a == \"\" || a*1 != a/1 || a*1 < min*1 || a*1 > max*1 || (len > 0 && a.length != len)){\r\n\t\talert('Invalid Data');\r\n\t\tx.focus();\r\n\t}else{\r\n\t\tcheckEditBox(y);\r\n\t}\r\n}\r\n\r\nfunction checkEditBox(x){\r\n\tdocument.getElementById(\"chk\"+x).checked = true;\r\n}\r\n\r\n</script>\r\n";
print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b></font></p>";
print "<form name='frm' method='post' action='ShiftSummaryMaster.php'><input type='hidden' name='act' value='editRecord'><table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
$query = "SELECT id, tgroup.Name, Start, GraceTo, FlexiBreak, BreakFrom, BreakTo, Close, NightFlag, WorkMin, ScheduleMaster.Name, ShiftTypeMaster.Name, MinOTWorkForBreak, MinWorkForBreak, MinOT1Work, AccessRestrict, RelaxRestrict, StartHour, CloseHour, EarlyInOT, LessLunchOT, NoBreakException, EarlyInOTDayDate, MinOTValue, MaxOTValue, ASLate, ASAbsent, MaxOTValueOT1, MaxOTValueOT2, MoveNS, OT1RF, NSOTCO, ExemptOT1, ExemptOT2, ExemptOTDate, ProxyOT, NoBreakExceptionOT, ExemptLI, ExemptOT FROM tgroup, ScheduleMaster, ShiftTypeMaster WHERE tgroup.id > 2 AND tgroup.ScheduleID = ScheduleMaster.ScheduleID AND ShiftTypeMaster.ShiftTypeID = tgroup.ShiftTypeID ORDER BY id";
$v_shift = "";
$c_shift = 0;
for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $c_shift++) {
    if ($c_shift % 5 == 0) {
        print "<tr><td class='f2'>&nbsp;</td><td class='f2b'>Name</td>";
        print "<td class='f2b'><a title='Shift Details'>Details</a></td> <td class='f2b'><a title='Pay Early in Overtime on Normal Day'>Early OT [N]</a></td> <td class='f2b'><a title='Pay Early in Overtime on OT Day/ Date [OT1, OT2, Public Holiday]'>Early OT [O]</a></td> <td class='f2b'><a title='IF the Total Break Time Actually taken is LESS THAN the Defined Break Minutes, Process the Balance Minutes as OverTime \n\r\n\rIdeal Value: No'>Break OT</a></td> <td class='f2b'><a title='Break Non Clocking\n\r\n\rIF the Shift is Defined to calculate the Break Time, Process Record of Employee who DID NOT Clock for Break AT ALL \n\r\n\rIdeal Value: Yes'>Break NC</a></td> <td class='f2b'><a title='Break Non Clocking\n\r\n\rIF the Shift is Defined to calculate the Break Time, Process Record of Employee who DID NOT Clock for Break AT ALL On Saturday/ Sunday/ Public Holiday\n\r\n\rIdeal Value: Yes'>Break NC [OT]</a></td> <td class='f2b'><a title='APPLY Strict Clocking Restrictions based on the Shift Timings \n\r\n\rIdeal Value: No'>AR</a></td> <td class='f2b'><a title='Relax Clocking Restrictions for OT1/ OT2 Days and Public Holidays \n\r\n\rIdeal Value: Yes'>RR</a></td> <td class='f2b'><a title='Clocking Restriction FROM \n\r\n\rIdeal Value: 0000'>AR<br> FROM</a></td> <td class='f2b'><a title='Clocking Restriction TO\n\r\n\rIdeal Value: 2359'>AR<br> TO</a></td> <td class='f2b'><a title='Minimum Minutes to be Worked BEFORE Deduction of BREAK MINUTES from Working Hours on a Normal Day [Applicable if Break Period is MORE THAN zero] \n\r\n\rIdeal Value: 300'>Break<br>Min N [N]</a></td> <td class='f2b'><a title='Minimum Minutes to be Worked BEFORE Deduction of BREAK MINUTES from Working Hours on a OT1/ OT2 Day OR Public Holiday [Applicable if Break Period is MORE THAN zero] \n\r\n\rIdeal Value: 300'>Break<br>Min N [O]</td> <td class='f2b'><a title='TREAT MINUTES Worked AFTER the Minutes Specified HERE as a NEW SHIFT for a NEW DAY [Will ONLY Work if the Shift is configured for Shift Rotation] \n\r\n\rIdeal Value: 0'><b>NS OT<br>Carry Over<br>Minutes</b></a></td> <td class='f2b'><a title='Minimum Overtime Minutes Cut Off. Any Overtime Worked Less than this value will be RESET to ZERO \n\r\n\rIdeal Value: 0'>Min OT</a></td> <td class='f2b'><a title='OT1 Overtime Reduction Factor \n\r\n\rOvertime will be reduced based on the Following Calculations [Overtime = Overtime*RF - Shift Working Hours, Normal = Shift Working Hours] \n\r\n\rIdeal Value: 0'>OT1 RF</a></td> <td class='f2b'><a title='Minimum Minutes to be Worked on OT1 Day BEFORE Paying OT1 Overtime \n\r\n\rIdeal Value: 0'>Min Work [OT1]</a></td> <td class='f2b'><a title='EXEMPT [DO NOT Pay] Overtime \n\r\n\rIdeal Value: NONE'>Exempt<br>OT</a></td> <td class='f2b'><a title='EXEMPT [Excuse] LATE IN \n\r\n\rIdeal Value: None'>Exempt<br>Late In</a></td> <td class='f2b'><b><a title='Book Overtime on Proxy Days'>Mark OT on Proxy Days</a></b></td> <td class='f2b'><a title='Maximum Overtime Minutes Cut Off on a Normal Day. Maximum Overtime that can be Payed on a Normal Day. Any Overtime Minutes Worked MORE THAN this Value will be RESET to THIS VALUE \n\r\n\rIdeal Value: 1440'>Max OT [N]</a></td> <td class='f2b'><a title='Maximum Overtime Minutes Cut Off on a OT1 Day. Maximum Overtime that can be Payed on a OT1 Day. Any Overtime Minutes Worked MORE THAN this Value will be RESET to THIS VALUE \n\r\n\rIdeal Value: 1440'>Max OT [OT1]</a></td> <td class='f2b'><a title='Maximum Overtime Minutes Cut Off on a OT2 Day/ Public Holday. Maximum Overtime that can be Payed on a OT2 Day/ Public Holiday. Any Overtime Minutes Worked MORE THAN this Value will be RESET to THIS VALUE \n\r\n\rIdeal Value: 1440'>Max OT [OT2]</a></td> <td class='f2b'><a title='Number of Days AFTER which an Employee should be BLOCKED from Clocking IF he was UnAuthorized Absent [Abandonment] \n\r\n\rIdeal Value: 3'>ADA</td> </tr>";
    }
    print "<tr><td><input type='checkbox' name='chk" . $c_shift . "' value='" . $cur[0] . "'></td>";
    print "<td class='f2'><a href='ShiftMaster.php?lstShift=" . $cur[0] . "' target='_blank' title='Schedule Type: " . $cur[10] . " \n\rRoutine Type: " . $cur[11] . "'>" . $cur[1] . "</a></td>";
    print "<td class='f2'>";
    if ($cur[8] == 1) {
        print "Night Shift";
        print "<br>Move NS: <b>" . $cur[29] . "</b><br>";
    }
    print "Work Hrs: <b>" . $cur[9] / 60 . "</b>";
    print "<br>Start: <b>" . $cur[2] . "</b>";
    print "<br>Grace: <b>" . $cur[3] . "</b>";
    print "<br>Break: <b>" . $cur[4] . "</b>";
    print "<br>Close: <b>" . $cur[7] . "</b>";
    print "</td>";
    print "<td class='f2'>";
    print "<select onBlur='javascript:checkEditBox(" . $c_shift . ")' size='1' name='lstEarlyInOT" . $c_shift . "' class='form-control-inner'>";
    print "<option value='" . $cur[19] . "' selected>" . $cur[19] . "</option> <option value='No'>No</option> <option value='Yes'>Yes</option>";
    print "</select>";
    print "</td>";
    print "<td class='f2'>";
    print "<select onBlur='javascript:checkEditBox(" . $c_shift . ")'size='1' name='lstEarlyInOTDayDate" . $c_shift . "' class='form-control-inner'>";
    print "<option value='" . $cur[22] . "' selected>" . $cur[22] . "</option> <option value='No'>No</option> <option value='Yes'>Yes</option> <option value='SAN'>SAN</option>";
    print "</select>";
    print "</td>";
    print "<td class='f2'>";
    print "<select onBlur='javascript:checkEditBox(" . $c_shift . ")'size='1' name='lstLessLunchOT" . $c_shift . "' class='form-control-inner'>";
    print "<option value='" . $cur[20] . "' selected>" . $cur[20] . "</option> <option value='No'>No</option> <option value='Yes'>Yes</option>";
    print "</select>";
    print "</td>";
    print "<td class='f2'>";
    print "<select onBlur='javascript:checkEditBox(" . $c_shift . ")'size='1' name='lstNoBreakException" . $c_shift . "' class='form-control-inner'>";
    print "<option value='" . $cur[21] . "' selected>" . $cur[21] . "</option> <option value='No'>No</option> <option value='Yes'>Yes</option>";
    print "</select>";
    print "</td>";
    print "<td class='f2'>";
    print "<select onBlur='javascript:checkEditBox(" . $c_shift . ")'size='1' name='lstNoBreakExceptionOT" . $c_shift . "' class='form-control-inner'>";
    print "<option value='" . $cur[36] . "' selected>" . $cur[36] . "</option> <option value='No'>No</option> <option value='Yes'>Yes</option>";
    print "</select>";
    print "</td>";
    print "<td class='f2'>";
    print "<select onBlur='javascript:checkEditBox(" . $c_shift . ")'size='1' name='lstAccessRestrict" . $c_shift . "' class='form-control-inner'>";
    print "<option value='" . $cur[15] . "' selected>" . $cur[15] . "</option> <option value='No'>No</option> <option value='Yes'>Yes</option>";
    print "</select>";
    print "</td>";
    print "<td class='f2'>";
    print "<select onBlur='javascript:checkEditBox(" . $c_shift . ")'size='1' name='lstRelaxRestrict" . $c_shift . "' class='form-control-inner'>";
    print "<option value='" . $cur[16] . "' selected>" . $cur[16] . "</option> <option value='No'>No</option> <option value='Yes'>Yes</option> <option value='SAN'>SAN</option>";
    print "</select>";
    print "</td>";
    print "<td class='f2'><input size='4' name='txtStartHour" . $c_shift . "' value='" . $cur[17] . "' onBlur='javascript:isNumber(this, " . $c_shift . ", 0, 2359, 4)' class='form-control-inner'></td>";
    print "<td class='f2'><input size='4' name='txtCloseHour" . $c_shift . "' value='" . $cur[18] . "' onBlur='javascript:isNumber(this, " . $c_shift . ", 0, 2359, 4)' class='form-control-inner'></td>";
    print "<td class='f2'><input size='4' name='txtMinWorkForBreak" . $c_shift . "' value='" . $cur[13] . "' onBlur='javascript:isNumber(this, " . $c_shift . ", 0, 1440, -1)' class='form-control-inner'></td>";
    print "<td class='f2'><input size='4' name='txtMinOTWorkForBreak" . $c_shift . "' value='" . $cur[12] . "' onBlur='javascript:isNumber(this, " . $c_shift . ", 0, 1440, -1)' class='form-control-inner'></td>";
    print "<td class='f2'><input size='4' name='txtNSOTCO" . $c_shift . "' value='" . $cur[31] . "' onBlur='javascript:isNumber(this, " . $c_shift . ", 0, 1440, -1)' class='form-control-inner'></td>";
    print "<td class='f2'><input size='4' name='txtMinOTValue" . $c_shift . "' value='" . $cur[23] . "' onBlur='javascript:isNumber(this, " . $c_shift . ", 0, 1440, -1)' class='form-control-inner'></td>";
    print "<td class='f2'><input size='4' name='txtOT1RF" . $c_shift . "' value='" . $cur[30] . "' onBlur='javascript:isNumber(this, " . $c_shift . ", 0, 10, -1)' class='form-control-inner'></td>";
    print "<td class='f2'><input size='4' name='txtMinOT1Work" . $c_shift . "' value='" . $cur[14] . "' onBlur='javascript:isNumber(this, " . $c_shift . ", 0, 1440, -1)' class='form-control-inner'></td>";
    print "<td class='f2'>";
    print "<select onBlur='javascript:checkEditBox(" . $c_shift . ")'size='1' name='lstExemptOT" . $c_shift . "' class='form-control-inner'>";
    print "<option value='" . $cur[38] . "' selected>" . $cur[38] . "</option><option value='OT1'>OT1</option><option value='OT2'>OT2</option><option value='OTD'>OTD</option><option value='OT1/OT2'>OT1/OT2</option><option value='OT2/OTD'>OT2/OTD</option><option value='ALL OT'>ALL OT</option><option value='NONE'>NONE</option>";
    print "</select>";
    print "</td>";
    print "<td class='f2'>";
    print "<select onBlur='javascript:checkEditBox(" . $c_shift . ")'size='1' name='lstExemptLI" . $c_shift . "' class='form-control-inner'>";
    print "<option value='" . $cur[37] . "' selected>" . $cur[37] . "</option><option value='SAN'>SAN</option><option value='OT1'>OT1</option><option value='OT2'>OT2</option><option value='OTD'>OTD</option><option value='OT1/OT2'>OT1/OT2</option><option value='OT2/OTD'>OT2/OTD</option><option value='ALL OT'>ALL OT</option><option value='ALL'>ALL</option><option value='OT/N'>OT/N</option><option value='NONE'>NONE</option>";
    print "</select>";
    print "</td>";
    print "<td class='f2'>";
    print "<select onBlur='javascript:checkEditBox(" . $c_shift . ")'size='1' name='lstProxyOT" . $c_shift . "' class='form-control-inner'>";
    print "<option value='" . $cur[35] . "' selected>" . $cur[35] . "</option> <option value='No'>No</option> <option value='Yes'>Yes</option>";
    print "</select>";
    print "</td>";
    print "<td class='f2'><input size='4' name='txtMaxOTValue" . $c_shift . "' value='" . $cur[24] . "' onBlur='javascript:isNumber(this, " . $c_shift . ", 0, 1440, -1)' class='form-control-inner'></td>";
    print "<td class='f2'><input size='4' name='txtMaxOTValueOT1" . $c_shift . "' value='" . $cur[27] . "' onBlur='javascript:isNumber(this, " . $c_shift . ", 0, 1440, -1)' class='form-control-inner'></td>";
    print "<td class='f2'><input size='4' name='txtMaxOTValueOT2" . $c_shift . "' value='" . $cur[28] . "' onBlur='javascript:isNumber(this, " . $c_shift . ", 0, 1440, -1)' class='form-control-inner'></td>";
    print "<td class='f2'><input size='1' name='txtASAbsent" . $c_shift . "' value='" . $cur[26] . "' onBlur='javascript:isNumber(this, " . $c_shift . ", 0, 99, -1)' class='form-control-inner'></td>";
    print "</tr>";
}
print "<input type='hidden' name='txhShiftCounter' value='" . $c_shift . "'>";
print "</table><p align='left'>";
if (stripos($userlevel, $current_module . "D") !== false) {
    print "<input type='submit' value='Save Changes'>";
}
print "</p></form>";
echo "\r\n</center></body></html>";

?>