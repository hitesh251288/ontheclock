<?php


ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "25";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!(checkSession($userlevel, $current_module) && $_SESSION[$session_variable . "VirdiLevel"] == "Classic")) {
    header("Location: " . $config["REDIRECT"] . "?url=ReportPreFlag.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$act = $_GET["act"];
if ($act == "") {
    $act = $_POST["act"];
}
$prints = $_GET["prints"];
$excel = $_GET["excel"];
$message = $_GET["message"];
if ($message == "") {
    $message = "Pre Flag Report";
}
$lstShift = $_POST["lstShift"];
$lstDepartment = $_POST["lstDepartment"];
$lstDivision = $_POST["lstDivision"];
$lstEmployeeIDFrom = $_POST["lstEmployeeIDFrom"];
$lstEmployeeIDTo = $_POST["lstEmployeeIDTo"];
$txtEmployee = $_POST["txtEmployee"];
$txtSNo = $_POST["txtSNo"];
$lstSort = $_POST["lstSort"];
$txtFrom = $_POST["txtFrom"];
$txtTo = $_POST["txtTo"];
if ($txtFrom == "") {
    $txtFrom = $_GET["txtFrom"];
}
if ($txtTo == "") {
    $txtTo = $_GET["txtTo"];
}
if ($txtFrom == "") {
    $txtFrom = displayToday();
}
if ($txtTo == "") {
    $txtTo = displayToday();
}
$lstColourFlag = $_POST["lstColourFlag"];
$txtPreFlagRemark = $_POST["txtPreFlagRemark"];
$lstRotateShift = $_POST["lstRotateShift"];
$lstProcessed = $_POST["lstProcessed"];
$lstEmployeeStatus = "";
if (isset($_POST["lstEmployeeStatus"])) {
    $lstEmployeeStatus = $_POST["lstEmployeeStatus"];
} else {
    $lstEmployeeStatus = "ACT";
}
$txtEmployeeCode = $_POST["txtEmployeeCode"];
$txtRemark = $_POST["txtRemark"];
$txtPhone = $_POST["txtPhone"];
$txtOTH = $_POST["txtOTH"];
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
$table_name = "Access.FlagDayRotation";
if ($lstDB == "Archive") {
    $table_name = "AccessArchive.FlagDayRotation";
}
print "<html><title>Pre Flag Report</title>";
if ($prints != "yes") {
    print "<body>";
} else {
    if ($excel != "yes") {
        print "<body onLoad='javascript:window.print()'>";
    } else {
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=ReportPreFlag.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        print "<body>";
    }
}
print "<center>";
if ($excel != "yes") {
    displayHeader($prints, true, false);
}
print "<center>";
if ($prints != "yes") {
    displayLinks(18, $userlevel);
}
print "</center>";
if ($excel != "yes") {
    print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b></font></p>";
    if ($prints != "yes") {
        print "<table width='800' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
        print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Select ONE or MORE options and click 'Search Record'</b></font></td></tr>";
    } else {
        print "<table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
        print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Selected Options</b></font></td></tr>";
    }
    print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='ReportPreFlag.php'><input type='hidden' name='act' value='searchRecord'><tr>";
    $query = "SELECT id, name from tgroup WHERE id > 1 ORDER BY name";
    displayList("lstShift", "Current Shift: ", $lstShift, $prints, $conn, $query, "", "25%", "75%");
    print "</tr>";
    displaySearchFields($conn, $prints, $session_variable, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $lstEmployeeName, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
//    print "<tr>";
//    print "<td colspan='2'><table width='100%' cellspacing='-1' cellpadding='-1' border='0'><tr>";
    $query = "SELECT id, name from tgate ORDER BY name";
//    displayList("lstTerminal", "Terminal: ", $lstTerminal, $prints, $conn, $query, "", "25%", "30%");
//    print "<td align='right' width='20%'></td><td width='25%'></td>";
//    displayTextbox("txtPreFlagRemark", "Pre Flag Remark: ", $txtPreFlagRemark, $prints, 12, "20%", "25%");
//    print "<td align='right' width='20%'></td><td width='25%'></td>";
//    print "</tr></table></td>";
//    print "</tr>";
    print "<tr>";
    print "<td colspan='2'><table width='100%' cellspacing='-1' cellpadding='-1' border='0'><tr>";
    displayTextbox("txtFrom", "Date From <font size='1'>(DD/MM/YYYY)</font>: ", $txtFrom, $prints, 12, "25%", "30%");
//    displayTextbox("txtOTH", "OT Hours: ", $txtOTH, $prints, 5, "20%", "25%");
    print "<td align='right' width='20%'></td><td width='25%'></td>";
    print "</tr></table></td>";
    print "</tr>";
    print "<tr>";
    print "<td colspan='2'><table width='100%' cellspacing='-1' cellpadding='-1' border='0'><tr>";
    displayTextbox("txtTo", "Date To <font size='1'>(DD/MM/YYYY)</font>: ", $txtTo, $prints, 12, "25%", "30%");
    if ($lstProcessed == "0") {
        $lstProcessed = "No";
    } else {
        if ($lstProcessed == "1") {
            $lstProcessed = "Yes";
        }
    }
    print "<td align='right' width='20%'></td><td width='25%'></td>";
    print "</tr></table></td>";
    print "</tr>";
    if ($prints != "yes") {
        print "<tr>";
        print "<td colspan='2'><table width='100%' border='0'>";
        print "<tr>";
        displayColourFlag($conn, $lstColourFlag, "lstColourFlag", true, true);
        if ($lstRotateShift == "0") {
            $lstRotateShift = "No";
        } else {
            if ($lstRotateShift == "1") {
                $lstRotateShift = "Yes";
            }
        }
        //print "<td align='right' width='25%'><font face='Verdana' size='2'>Rotate Shift after Flag Day:</font></td><td width='25%'><select name='lstRotateShift' class='form-control'> <option selected value='" . $lstRotateShift . "'>" . $lstRotateShift . "</option> <option value='1'>Yes</option> <option value='0'>No</option> <option value=''>---</option> </select></td>";
        print "</tr>";
        print "<tr>";
        displayEmployeeStatus($conn, "lstEmployeeStatus", $lstEmployeeStatus, "25%", "25%");
        print "<td width='25%'>&nbsp;</td><td width='25%'>&nbsp;</td>";
        print "</tr>";
        print "<tr>";
        $array = array(array("tuser.id, FlagDayRotation.e_date ", "Employee Code"), array("tuser.name, tuser.id, FlagDayRotation.e_date ", "Employee Name - Code"), array("tuser.dept, tuser.id, FlagDayRotation.e_date ", "Dept - Code"), array("tuser.company, tuser.dept, tuser.id, FlagDayRotation.e_date ", "Div/Desg - Dept - Code"), array("tuser.company, tuser.dept, tuser.group_id, tuser.id, FlagDayRotation.e_date ", "Div - Dept - Shift - Code"), array("FlagDayRotation.Flag, tuser.id", "Flag"));
        displaySort($array, $lstSort, 6);
        print "<td width='25%'>&nbsp;</td><td width='25%'>&nbsp;</td>";
        print "</tr>";
        print "</tr></table></td>";
        print "</tr>";
        print "<tr><td>&nbsp;</td><td><input name='btSearch' type='submit' value='Search Record'>&nbsp;&nbsp;<input type='button' value='Excel' onClick='checkPrint(1)'>&nbsp;&nbsp;<input type='button' value='CSV' onClick='checkPrint(3)'></td></tr></td></tr>";
    }
    print "</table><br>";
}
if ($act == "searchRecord") {
    $count = 0;
    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, " . $table_name . ".Flag, " . $table_name . ".e_date , "
            . "tgate.name, " . $table_name . ".Remark, " . $table_name . ".Rotate, " . $table_name . ".RecStat, tuser.idno, "
            . "tuser.remark, " . $table_name . ".OT, " . $table_name . ".OTH, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, "
            . "tuser.F7, tuser.F8, tuser.F9, tuser.F10, count(*) as flagCount, employeeflag.Violet, employeeflag.Green, employeeflag.Indigo, "
            . "employeeflag.Blue, employeeflag.Yellow, employeeflag.Orange, employeeflag.Red, employeeflag.Gray, employeeflag.Brown, "
            . "employeeflag.Purple, employeeflag.Magenta, employeeflag.Teal, employeeflag.Aqua, employeeflag.Safron, employeeflag.Amber, "
            . "employeeflag.Gold, employeeflag.Vermilion, employeeflag.Silver, employeeflag.Maroon, employeeflag.Pink FROM tuser, tgroup, " . $table_name . ", "
            . "tgate, employeeflag WHERE tuser.group_id = tgroup.id AND " . $table_name . ".e_id = tuser.id AND employeeflag.EmployeeID=tuser.id "
            . "AND " . $table_name . ".g_id = tgate.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
    if ($lstShift != "") {
        $query = $query . " AND tgroup.id = " . $lstShift;
    }
    $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
    if ($txtFrom != "") {
        $query = $query . " AND " . $table_name . ".e_date  >= " . insertDate($txtFrom);
    }
    if ($txtTo != "") {
        $query = $query . " AND " . $table_name . ".e_date  <= " . insertDate($txtTo);
    }
    if ($lstTerminal != "") {
        $query = $query . " AND " . $table_name . ".g_id = '" . $lstTerminal . "'";
    }
    if ($txtPreFlagRemark != "") {
        $query = $query . " AND " . $table_name . ".Remark LIKE '%" . $txtPreFlagRemark . "%'";
    }
    if ($lstRotateShift == "No") {
        $lstRotateShift = "0";
    } else {
        if ($lstRotateShift == "Yes") {
            $lstRotateShift = "1";
        }
    }
    if ($lstRotateShift != "") {
        $query = $query . " AND " . $table_name . ".Rotate = '" . $lstRotateShift . "'";
    }
    if ($lstProcessed == "No") {
        $lstProcessed = "0";
    } else {
        if ($lstProcessed == "Yes") {
            $lstProcessed = "1";
        }
    }
    if ($lstProcessed != "") {
        $query = $query . " AND " . $table_name . ".RecStat = '" . $lstProcessed . "'";
    }
    if ($txtOTH != "") {
        $query = $query . " AND " . $table_name . ".OTH = '" . $txtOTH . "'";
    }
    if ($lstColourFlag != "") {
        if ($lstColourFlag == "Black/Proxy") {
            $query = $query . " AND (" . $table_name . ".Flag = 'Black' OR " . $table_name . ".Flag = 'Proxy') ";
        } else {
            if ($lstColourFlag == "All w/o Black/Proxy") {
                $query = $query . " AND " . $table_name . ".Flag NOT LIKE 'Black' AND " . $table_name . ".Flag NOT LIKE 'Proxy'";
            } else {
                if ($lstColourFlag == "All w/o Proxy") {
                    $query = $query . " AND " . $table_name . ".Flag NOT LIKE 'Proxy'";
                } else {
                    $query = $query . " AND " . $table_name . ".Flag = '" . $lstColourFlag . "'";
                }
            }
        }
    }
    $query = $query . employeeStatusQuery($lstEmployeeStatus). "group by tuser.id";
    
    if ($lstDB != "Archive") {
        $query = $query . " ORDER BY " . $lstSort;
    }
    if ($excel != "yes") {
        print "<p align='center'><font face='Verdana' size='1'><b>" . $_SESSION[$session_variable . "FlagReportText"] . "</b></font></p>";
    }
    if ($prints != "yes") {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800'>";
    } else {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%'>";
    }
    print "<tr><td><font face='Verdana' size='2'>Employee ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Flag Limit</font></td> <td><font face='Verdana' size='2'>Flag Count</font></td> <td><font face='Verdana' size='2'>Flag Balance</font></td> <td><font face='Verdana' size='2'>Flag</font></td></tr>";
    $result = mysqli_query($conn, $query);
    for ($bgcolor = ""; $cur = mysqli_fetch_array($result); $count++) {
        if ($cur[5] != "") {
            $font = $cur[5];
            if ($font == "Yellow") {
                $bgcolor = "Brown";
            } else {
                $bgcolor = "#FFFFFF";
            }
        } else {
            $cur[5] = "&nbsp;";
            $font = "Black";
            $bgcolor = "#FFFFFF";
        }
        if ($cur[3] == "") {
            $cur[3] = "&nbsp;";
        }
        if ($cur[8] == "") {
            $cur[8] = "&nbsp;";
        }
        if ($cur[12] == "") {
            $cur[12] = "&nbsp;";
        }
        if ($cur[13] == "") {
            $cur[13] = "&nbsp;";
        }
        $flag = $cur[5];
        addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
        displayDate($cur[6]);
        print "<tr><td bgcolor='" . $bgcolor . "'><a title='ID'><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'><font face='Verdana' size='1' color='" . $flag . "'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Name'><font face='Verdana' size='1' color='" . $flag . "'>" . $cur[1] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1' color='" . $flag . "'>" . $cur[11] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Dept'><font face='Verdana' size='1' color='" . $flag . "'>" . $cur[2] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Div/Desg'><font face='Verdana' size='1' color='" . $flag . "'>" . $cur[3] . "</font></a></td> ";
//        if ($cur[9] == 0) {
//            print "No";
//        } else {
//            print "Yes";
//        }
//        print "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Processed Record'><font face='Verdana' size='1' color='" . $flag . "'>";
//        if ($cur[10] == 0) {
//            print "No";
//        } else {
//            print "Yes";
//        }
//        print "</font></a></td>";
        

        if($cur[5] == 'Violet'){
            $flagLimit = $cur['Violet'];
        }
        if($cur[5] == 'Yellow'){
            $flagLimit = $cur['Yellow'];
        }
        if($cur[5] == 'Green'){
            $flagLimit = $cur['Green'];
        }
        if($cur[5] == 'Indigo'){
            $flagLimit = $cur['Indigo'];
        }
        if($cur[5] == 'Blue'){
            $flagLimit = $cur['Blue'];
        }
        if($cur[5] == 'Orange'){
            $flagLimit = $cur['Orange'];
        }
        if($cur[5] == 'Red'){
            $flagLimit = $cur['Red'];
        }
        if($cur[5] == 'Gray'){
            $flagLimit = $cur['Gray'];
        }
        if($cur[5] == 'Brown'){
            $flagLimit = $cur['Brown'];
        }
        if($cur[5] == 'Purple'){
            $flagLimit = $cur['Purple'];
        }
        if($cur[5] == 'Magenta'){
            $flagLimit = $cur['Magenta'];
        }
        if($cur[5] == 'Teal'){
            $flagLimit = $cur['Teal'];
        }
        if($cur[5] == 'Aqua'){
            $flagLimit = $cur['Aqua'];
        }
        if($cur[5] == 'Safron'){
            $flagLimit = $cur['Safron'];
        }
        if($cur[5] == 'Amber'){
            $flagLimit = $cur['Amber'];
        }
        if($cur[5] == 'Gold'){
            $flagLimit = $cur['Gold'];
        }
        if($cur[5] == 'Vermilion'){
            $flagLimit = $cur['Vermilion'];
        }
        if($cur[5] == 'Silver'){
            $flagLimit = $cur['Silver'];
        }
        if($cur[5] == 'Maroon'){
            $flagLimit = $cur['Maroon'];
        }
        if($cur[5] == 'Pink'){
            $flagLimit = $cur['Pink'];
        }
        $flagBalance = $flagLimit - $cur[25];
        print "<td bgcolor='" . $bgcolor . "'><a title='Flag Limit'><font face='Verdana' size='1' color='" . $flag . "'>" . $flagLimit . "</font></a></td>";
        print "<td bgcolor='" . $bgcolor . "'><a title='Flag Count'><font face='Verdana' size='1' color='" . $flag . "'>" . $cur[25] . "</font></a></td>";
        print "<td bgcolor='" . $bgcolor . "'><a title='Flag Balance'><font face='Verdana' size='1' color='" . $flag . "'>" . $flagBalance . "</font></a></td>";
        print "<td bgcolor='" . $bgcolor . "'><a title='Flag'><font face='Verdana' size='1' color='" . $flag . "'>" . $cur[5] . "</font></a></td>";
        print "</tr>";
    }
    print "</table>";
    if ($excel != "yes") {
        print "<br><p align='center'><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $count . "</b></font>";
    }
    if ($prints != "yes") {
        print "<br><input type='button' value='Print Report' onClick='checkPrint(0)'>&nbsp;&nbsp;<input type='button' value='Excel' onClick='checkPrint(1)'>";
    }
    print "</p>";
}
print "</form>";
echo "</center></body></html>";

?>