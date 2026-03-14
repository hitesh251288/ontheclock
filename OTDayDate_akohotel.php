<?php


ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "28";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$username = $_SESSION[$session_variable . "username"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$userstatus = $_SESSION[$session_variable . "userstatus"];
$message = "All Working Hours will be considered as OT for the defined Days/ Dates";
$lstColourFlag = "Black";
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=OTDayDate.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$iconn = openIConnection();
$act = $_POST["act"];
if ($act == "editRecord") {
    for ($i = 0; $i < 7; $i++) {
        if ($_POST["chk" . $i] != "") {
            $query = "UPDATE OTDay SET OT = 1 WHERE Day = '" . $_POST["txtDay" . $i] . "'";
            if (updateIData($iconn, $query, true)) {
                $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Added " . $_POST["txtDay" . $i] . " to OT Day')";
                updateIData($iconn, $query, true);
            }
        } else {
            $query = "UPDATE OTDay SET OT = 0 WHERE Day = '" . $_POST["txtDay" . $i] . "'";
            if (updateIData($iconn, $query, true)) {
                $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Deleted " . $_POST["txtDay" . $i] . " from OT Day')";
                updateIData($iconn, $query, true);
            }
        }
    }
    $query = "DELETE FROM OTDate";
    updateIData($iconn, $query, true);
    for ($i = 0; $i < 28; $i++) {
        if ($_POST["txtDate" . $i] != "") {
            $query = "INSERT INTO OTDate (OTDate, Day) VALUES (" . insertDate($_POST["txtDate" . $i]) . ", '" . $_POST["txtHoliday" . $i] . "')";
            if (updateIData($iconn, $query, true)) {
                $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Add " . $_POST["txtDate" . $i] . " to OT Date')";
                updateIData($iconn, $query, true);
            }
        }
    }
    $query = "DELETE FROM SanitationDate";
    updateIData($iconn, $query, true);
    for ($i = 0; $i < 28; $i++) {
        if ($_POST["txtSDate" . $i] != "" && getDay($_POST["txtSDate" . $i]) == "Saturday") {
            $query = "INSERT INTO SanitationDate (OTDate) VALUES (" . insertDate($_POST["txtSDate" . $i]) . ")";
            if (updateIData($iconn, $query, true)) {
                $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Add " . $_POST["txtSDate" . $i] . " to Sanitation Date')";
                updateIData($iconn, $query, true);
            }
        }
    }
    $data1 = 0;
    $data2 = 0;
    if ($_POST["chkMarkProxy"] != "") {
        $data1 = 1;
    }
    $data2 = $_POST["lstOTDateBalNHrs"];
    $query = "UPDATE OtherSettingMaster SET EX4 = " . $data1 . ", OTDateBalNHrs = " . $data2 . ", PLFlag='" . $_POST["lstColourFlag"] . "', SanSatOT = '" . $_POST["lstSanSatOT"] . "' ";
    if (updateIData($iconn, $query, true)) {
        $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'OTDate Flag = " . $_POST["lstColourFlag"] . ", Mark Proxy for Employees ABSENT on OT Dates = " . $data1 . ", Add Balance Normal Hours for Employees PRESENT on OT Dates = " . $data2 . ", Sanitation OT = " . $_POST["lstSanSatOT"] . " ')";
        updateIData($iconn, $query, true);
        $message = "Record edited Successfully";
    }
}
echo "\r\n<html><head><title>OT Days/ Dates</title></head>\r\n<script>\r\n\t\r\n</script>\r\n<body><center><div align='center'>\r\n\t";
displayHeader($prints, false, false);
print "<center>";
displayLinks($current_module, $userlevel);
print "</center>";
print "<form name='frm' method='post' action='OTDayDate.php'> <input type='hidden' name='act' value='editRecord'>";
print "<table width='800' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
print "<tr><td width='100%' colspan='7' align='center'><font face='Verdana' size='1' color='#6481BD'><b>" . $message . "</b></font></td></tr>";
print "<tr><td width='100%' colspan='7' align='center'><br><font face='Verdana' size='2' color='#000000'>Select OT <b>DAYS</b> below</font></td></tr>";
print "<tr>";
$query = "SELECT Day, OT FROM OTDay ORDER BY OTDayID";
$result = mysqli_query($conn, $query);
for ($count = 0; $cur = mysqli_fetch_row($result); $count++) {
    print "<td bgcolor='#CCCCFF'><font face='Verdana' size='1'>";
    if ($cur[1] == 0) {
        print "<input type='checkbox' name='chk" . $count . "'><input type='hidden' name='txtDay" . $count . "' value='" . $cur[0] . "'>&nbsp;&nbsp;";
    } else {
        print "<input type='checkbox' name='chk" . $count . "' checked><input type='hidden' name='txtDay" . $count . "' value='" . $cur[0] . "'>&nbsp;&nbsp;";
    }
    print $cur[0] . "</font></td>";
}
print "</tr>";
print "<tr><td width='100%' colspan='7' align='center' ><br><br><font face='Verdana' size='2' color='#000000'>Enter OT <b>DATES</b> Below</font></td></tr>";
$query = "SELECT OTDate FROM OTDate ORDER BY OTDate";
$count = 0;
$result = array();
for ($result2 = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result2); $count++) {
    $result[$count] = $cur[0];
}
$count = 0;
for ($j = 0; $j < 4; $j++) {
    print "<tr>";
    for ($i = 0; $i < 7; $i++) {
        if ($result[$count] != "") {
            displayDate($result[$count]);
            print "<td bgcolor='#FFFFCC'><input name='txtDate" . $count . "' value='" . displayDate($result[$count]) . "' size='12' onBlur='check_date(this)' class='form-control-inner'><input type='hidden' name='txtHoliday" . $count . "' value=''></td>";
        } else {
            print "<td bgcolor='#FFFFCC'><input name='txtDate" . $count . "' value='' size='12' onBlur='check_date(this)' class='form-control-inner'><input type='hidden' name='txtHoliday" . $count . "' value=''></td>";
        }
        $count++;
    }
    print "</tr>";
}
//$query = "SELECT EX4, PLFlag, OTDateBalNHrs, SanSatOT FROM OtherSettingMaster";
$query = "SELECT o.EX4, o.PLFlag, o.OTDateBalNHrs, o.SanSatOT, f.Flag, f.Title FROM OtherSettingMaster o LEFT JOIN flagtitle f ON f.Flag=o.PLFlag";
$o_result = selectData($conn, $query);
if ($o_result[1] == "") {
    $o_result[1] == "Purple";
}
if ($o_result[2] == "") {
    $o_result[2] == 2;
}
if ($o_result[3] == "") {
    $o_result[3] == "Yes";
}
//$flagQuery="SELECT * from flagtitle where";
print "<tr><td colspan='5'><table width='100%' bgcolor='#FFFFCC' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'> <tr><td width='60%' align='right' bgcolor='#FFFFCC'><font face='Verdana' size='2'>Mark Proxy for <b>ABSENT</b> Employees on OT <b>DATES</b></font></td><td colspan='1' bgcolor='#FFFFCC'>";
if ($o_result[0] == 1) {
    print "<input type='checkbox' value='1' checked name='chkMarkProxy'>";
} else {
    print "<input type='checkbox' name='chkMarkProxy'>";
}
print "</td></tr></table></td> <td colspan='2' rowspan='3' bgcolor='#FFFFCC'><font face='Verdana' size='1'><b>" . $_SESSION[$session_variable . "FlagReportText"] . "</b></font></td></tr>";
print "<tr><td colspan='5'><table width='100%' bgcolor='#FFFFCC' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'> <tr><td width='60%' align='right' bgcolor='#FFFFCC'><font face='Verdana' size='2'><b>NORMAL</b> Hrs Treatment for <b>PRESENT</b> Employees on OT <b>DATES</b></font></td>";
print "<td><select name='lstOTDateBalNHrs' class='form-control'> <option value='" . $o_result[2] . "' selected>";
if ($o_result[2] == 0) {
    print "ZERO Hours";
} else {
    if ($o_result[2] == 1) {
        print "Balance Working Hrs";
    } else {
        if ($o_result[2] == 2) {
            print "Shift Hours";
        }
    }
}
print "<option value='0'>ZERO Hours</option>";
print "<option value='1'>Balance Working Hrs</option>";
print "<option value='2'>Shift Hours</option>";
print "</select></td>";
print "</tr></table></td></tr>";
print "<tr><td colspan='5'><table width='100%' bgcolor='#FFFFCC' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'><tr><td width='60%' align='right'><font size='2' face='Verdana'>Select a Flag to Mark the Attendance for the above ENTERED OT <b>DATES</b></font></td>";
print "<td><select name='lstColourFlag' class='form-control'> <option value='" . $o_result[1] . "' selected>" . $o_result[1] .' - '.$o_result[5]. "</option> <option value='Black'>Black</option>";
if ($o_result[1] != "Purple") {
    print "<option value='Purple'>Purple</option>";
}
print "</select></td>";
print "</tr></table></td></tr>";
print "<tr><td width='100%' colspan='7' align='center' ><br><br><font face='Verdana' size='2' color='#000000'>Enter Sanitation <b>DATES</b> Below <font size='1'><br>[Useful for Paying Early IN Overtime if configured under Shift Settings]</font></font></td></tr>";
$query = "SELECT OTDate FROM SanitationDate ORDER BY OTDate";
$count = 0;
$result = array();
for ($result2 = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result2); $count++) {
    $result[$count] = $cur[0];
}
$count = 0;
for ($j = 0; $j < 4; $j++) {
    print "<tr>";
    for ($i = 0; $i < 7; $i++) {
        if ($result[$count] != "") {
            displayDate($result[$count]);
            print "<td bgcolor='#FFFFCC'><input name='txtSDate" . $count . "' value='" . displayDate($result[$count]) . "' size='12' onBlur='check_date(this)' class='form-control-inner'></td>";
        } else {
            print "<td bgcolor='#FFFFCC'><input name='txtSDate" . $count . "' value='' size='12' onBlur='check_date(this)' class='form-control-inner'></td>";
        }
        $count++;
    }
    print "</tr>";
}
print "<tr><td align='right' colspan='5'><table width='100%' bgcolor='#FFFFCC' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'> <tr><td width='60%' align='right' bgcolor='#FFFFCC'><font size='2' face='Verdana'>Treat Sanitation Date as FULL Overtime</font><font size='1' face='Verdana'><br>(Applicable IF Saturday is NOT treated as a OT Day)</font></td>";
print "<td bgcolor='#FFFFCC' ><select name='lstSanSatOT' class='form-control'> <option value='" . $o_result[3] . "' selected>" . $o_result[3] . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option></select></td></tr></table></td>";
print "<td bgcolor='#FFFFCC' colspan='2' ><font size='1' face='Verdana'>&nbsp;</font></td></tr>";
print "<tr> <td align='right' colspan='3'><font face='Verdana' size='2'>&nbsp;</font></td><td colspan='5'>";
if (strpos($userlevel, $current_module . "E") !== false) {
    print "<input type='submit' value='Save Changes'>";
}
print "</td></tr>";
echo "\t\t\r\n\t\t</form>\t\t\r\n\t</table>\t\r\n\t<script>\r\n\t\tfunction check_date(x){\r\n\t\t\tif (x.value != \"\" && check_valid_date(x.value) == false){\r\n\t\t\t\talert(\"Invalid Date Format. Date Format should be DD/MM/YYYY ONLY\");\r\n\t\t\t\tx.focus();\r\n\t\t\t}\r\n\t\t}\r\n\t</script>\r\n</div></center></body></html>";

?>