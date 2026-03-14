<?php


ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "16";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=AssignProject.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$act = $_GET["act"];
if ($act == "") {
    $act = $_POST["act"];
}
$prints = $_GET["prints"];
$message = $_GET["message"];
if ($message == "") {
    $message = "Project Assignment";
}
$lstShift = $_POST["lstShift"];
$lstDepartment = $_POST["lstDepartment"];
$lstDivision = $_POST["lstDivision"];
$lstWeek = $_POST["lstWeek"];
$lstEmployeeIDFrom = $_POST["lstEmployeeIDFrom"];
$lstEmployeeIDTo = $_POST["lstEmployeeIDTo"];
$txtEmployeeCode = $_POST["txtEmployeeCode"];
$txtEmployee = $_POST["txtEmployee"];
$txtSNo = $_POST["txtSNo"];
$txtFrom = $_POST["txtFrom"];
$txtTo = $_POST["txtTo"];
if ($txtFrom == "") {
    if (substr(insertToday(), 6, 2) == "01") {
        if (substr(insertToday(), 4, 2) == "01") {
            $txtFrom = "01/12/" . (substr(insertToday(), 0, 4) - 1);
        } else {
            if (substr(insertToday(), 4, 2) - 1 < 10) {
                $txtFrom = "01/0" . (substr(insertToday(), 4, 2) - 1) . "/" . substr(insertToday(), 0, 4);
            } else {
                $txtFrom = "01/" . (substr(insertToday(), 4, 2) - 1) . "/" . substr(insertToday(), 0, 4);
            }
        }
    } else {
        $txtFrom = "01/" . substr(displayToday(), 3, 7);
    }
}
if ($txtTo == "") {
    $txtTo = displayDate(getLastDay(insertToday(), 1));
}
$lstEmployeeStatus = "";
if (isset($_POST["lstEmployeeStatus"])) {
    $lstEmployeeStatus = $_POST["lstEmployeeStatus"];
} else {
    $lstEmployeeStatus = "ACT";
}
$txtRemark = $_POST["txtRemark"];
$txtPhone = $_POST["txtPhone"];
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
print "<html><title>Project Assignment</title>";
if ($prints != "yes") {
    print "<body>";
} else {
    print "<body onLoad='javascript:window.print()'>";
}
print "<center>";
displayHeader($prints);
print "<center>";
if ($prints != "yes") {
    displayLinks($current_module, $userlevel);
}
print "</center>";
print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b></font></p>";
if ($prints != "yes") {
    print "<table width='800' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
    print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Select ONE or MORE options and click 'Search Record'</b></font></td></tr>";
} else {
    print "<table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
    print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Selected Options</b></font></td></tr>";
}
print "<form name='frm1' method='post' action='AssignProject.php'><input type='hidden' name='act' value='searchRecord'><tr>";
$query = "SELECT id, name from tgroup WHERE id > 1 ORDER BY name";
displayList("lstShift", "Shift: ", $lstShift, $prints, $conn, $query, "", "25%", "75%");
print "</tr>";
displaySearchFields($conn, $prints, $session_variable, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $lstEmployeeName, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
print "<tr>";
$query = "SELECT distinct(Week), Week from AttendanceMaster ORDER BY company";
displayList("lstWeek", "Week: ", $lstWeek, $prints, $conn, $query, "", "25%", "75%");
print "</tr>";
print "<tr>";
displayTextbox("txtFrom", "Date From <font size='1'>(DD/MM/YYYY)</font>: ", $txtFrom, $prints, 12, "25%", "75%");
print "</tr>";
print "<tr>";
displayTextbox("txtTo", "Date To <font size='1'>(DD/MM/YYYY)</font>: ", $txtTo, $prints, 12, "25%", "75%");
print "</tr>";
if ($prints != "yes") {
    print "<tr>";
    displayEmployeeStatus($conn, "lstEmployeeStatus", $lstEmployeeStatus, "", "");
    print "</tr>";
    print "<tr><td>&nbsp;</td><td><input type='button' value='Search Record' onClick='javascript:checkSearch()'></td></tr>";
}
print "</table><br>";
if ($act == "searchRecord") {
    print "<p><font face='Verdana' size='1'><b><u>Daily Basis</u></b></font></p>";
    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, DayMaster.TDate, DayMaster.Entry, DayMaster.Start, DayMaster.BreakOut, DayMaster.BreakIn, DayMaster.Close, DayMaster.Exit, DayMaster.DayMasterID, tuser.idno, tuser.remark, tgroup.id FROM tuser, tgroup, DayMaster WHERE DayMaster.group_id = tgroup.id AND DayMaster.e_id = tuser.id AND DayMaster.DayMasterID NOT IN (SELECT DayMasterID FROM ProjectLog) " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
    if ($lstShift != "") {
        $query = $query . " AND tgroup.id = " . $lstShift;
    }
    $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
    if ($txtFrom != "") {
        $query = $query . " AND DayMaster.TDate >= " . insertDate($txtFrom);
    }
    if ($txtTo != "") {
        $query = $query . " AND DayMaster.TDate <= " . insertDate($txtTo);
    }
    $query = $query . employeeStatusQuery($lstEmployeeStatus);
    $query = $query . " ORDER BY tuser.dept, tuser.name, DayMaster.TDate";
    if ($prints != "yes") {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800'>";
    } else {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%'>";
    }
    print "<tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Shift</font></td> <td><font face='Verdana' size='2'>Date</font></td> <td><font face='Verdana' size='2'>Entry</font></td> <td><font face='Verdana' size='2'>Start</font></td> <td><font face='Verdana' size='2'>BreakOut</font></td> <td><font face='Verdana' size='2'>BreakIn</font></td> <td><font face='Verdana' size='2'>Close</font></td> <td><font face='Verdana' size='2'>Exit</font></td> </tr>";
    $result = mysqli_query($conn, $query);
    for ($count = 0; $cur = mysqli_fetch_row($result); $count++) {
        if ($cur[3] == "") {
            $cur[3] = "&nbsp;";
        }
        if ($cur[13] == "") {
            $cur[13] = "&nbsp;";
        }
        if ($cur[14] == "") {
            $cur[14] = "&nbsp;";
        }
        addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
        displayDate($cur[5]);
        displayVirdiTime($cur[6]);
        displayVirdiTime($cur[7]);
        displayVirdiTime($cur[8]);
        displayVirdiTime($cur[9]);
        displayVirdiTime($cur[10]);
        displayVirdiTime($cur[11]);
        print "<tr><td><a title='ID'><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'><a href='javascript:openWindow(1, " . $cur[12] . ", " . $cur[15] . ")'><font face='Verdana' size='1'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $cur[1] . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $cur[13] . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $cur[2] . "</font></a></td> <td><a title='Div/Desg'><font face='Verdana' size='1'>" . $cur[3] . "</font></a></td> <td><a title='Rmk'><font face='Verdana' size='1'>" . $cur[14] . "</font></a></td> <td><a title='Shift'><font face='Verdana' size='1'>" . $cur[4] . "</font></a></td> <td><a title='Date'><font face='Verdana' size='1'>" . displayDate($cur[5]) . "</font></a></td> <td><a title='Entry'><font face='Verdana' size='1'>" . displayVirdiTime($cur[6]) . "</font></a></td> <td><a title='Start'><font face='Verdana' size='1'>" . displayVirdiTime($cur[7]) . "</font></a></td> <td><a title='Break Out'><font face='Verdana' size='1'>" . displayVirdiTime($cur[8]) . "</font></a></td> <td><a title='Break In'><font face='Verdana' size='1'>" . displayVirdiTime($cur[9]) . "</font></a></td> <td><a title='Close'><font face='Verdana' size='1'>" . displayVirdiTime($cur[10]) . "</font></a></td> <td><a title='Exit'><font face='Verdana' size='1'>" . displayVirdiTime($cur[11]) . "</font></a></td></tr>";
    }
    print "</table><br><p align='center'><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $count . "</b></font><br>";
    print "<p><font face='Verdana' size='1'><b><u>Weekly Basis</u></b></font></p>";
    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, WeekMaster.LogDate, WeekMaster.Start, WeekMaster.Close, WeekMaster.Seconds, WeekMaster.WeekMasterID, tgroup.id, tuser.idno, tuser.remark FROM tuser, tgroup, WeekMaster WHERE WeekMaster.group_id = tgroup.id AND WeekMaster.e_id = tuser.id AND WeekMaster.WeekMasterID NOT IN (SELECT WeekMasterID FROM ProjectLog) " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
    if ($lstShift != "") {
        $query = $query . " AND tgroup.id = " . $lstShift;
    }
    if ($lstDepartment != "") {
        $query = $query . " AND tuser.dept LIKE '" . $lstDepartment . "%'";
    }
    if ($lstDivision != "") {
        $query = $query . " AND tuser.company = '" . $lstDivision . "%'";
    }
    if ($lstEmployeeIDFrom != "") {
        $query = $query . " AND tuser.id >= " . $lstEmployeeIDFrom;
    }
    if ($lstEmployeeIDTo != "") {
        $query = $query . " AND tuser.id <= " . $lstEmployeeIDTo;
    }
    if ($txtEmployeeCode != "") {
        $query = $query . " AND tuser.id = " . $txtEmployeeCode * 1;
    }
    if ($txtEmployee != "") {
        $query = $query . " AND tuser.name like '%" . $txtEmployee . "%'";
    }
    if ($txtSNo != "") {
        $query = $query . " AND tuser.idno like '%" . $txtSNo . "%'";
    }
    if ($lstWeek != "") {
        $query = $query . " AND AttendanceMaster.Week = " . $lstWeek;
    }
    if ($txtFrom != "") {
        $query = $query . " AND WeekMaster.LogDate >= " . insertDate($txtFrom);
    }
    if ($txtTo != "") {
        $query = $query . " AND WeekMaster.LogDate <= " . insertDate($txtTo);
    }
    $query = $query . employeeStatusQuery($lstEmployeeStatus);
    $query = $query . " ORDER BY tuser.dept, tuser.name, WeekMaster.LogDate";
    if ($prints != "yes") {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800'>";
    } else {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%'>";
    }
    print "<tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Division</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Shift</font></td> <td><font face='Verdana' size='2'>Date</font></td> <td><font face='Verdana' size='2'>Start</font></td> <td><font face='Verdana' size='2'>Close</font></td> <td><font face='Verdana' size='2'>Time (Sec)</font></td> <td><font face='Verdana' size='2'>Time (Min)</font></td> <td><font face='Verdana' size='2'>Time (Hrs)</font></td></tr>";
    $result = mysqli_query($conn, $query);
    for ($count = 0; $cur = mysqli_fetch_row($result); $count++) {
        addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
        displayDate($cur[5]);
        displayVirdiTime($cur[6]);
        displayVirdiTime($cur[7]);
        round($cur[11] / 60, 2);
        round($cur[11] / 3600, 2);
        print "<tr><td><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'><a title='ID - Click to Assign Project Time' href='javascript:openWindow(2, " . $cur[9] . ", " . $cur[10] . ")'><font face='Verdana' size='1'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $cur[1] . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $cur[11] . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $cur[2] . "</font></a></td> <td><a title='Div/Desg'><font face='Verdana' size='1'>" . $cur[3] . "</font></a></td> <td><a title='Rmk'><font face='Verdana' size='1'>" . $cur[12] . "</font></a></td> <td><a title='Shift'><font face='Verdana' size='1'>" . $cur[4] . "</font></a></td> <td><a title='Date'><font face='Verdana' size='1'>" . displayDate($cur[5]) . "</font></a></td> <td><a title='Start'><font face='Verdana' size='1'>" . displayVirdiTime($cur[6]) . "</font></a></td> <td><a title='Close'><font face='Verdana' size='1'>" . displayVirdiTime($cur[7]) . "</font></a></td> <td><a title='Time (Sec)'><font face='Verdana' size='1'>" . $cur[11] . "</font></a></td> <td><a title='Time (Min)'><font face='Verdana' size='1'>" . round($cur[11] / 60, 2) . "</font></a></td> <td><a title='Time (Hrs)'><font face='Verdana' size='1'>" . round($cur[11] / 3600, 2) . "</font></a></td> </tr>";
    }
    print "</table><br><p align='center'><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $count . "</b></font>";
    if ($prints != "yes") {
        print "<br><input type='button' value='Print Report' onClick='checkPrint()'>";
    }
    print "</p>";
}
print "</form>";
echo "\r\n<script>\r\nfunction openWindow(a, b, c){\r\n\tif (a == 1){\r\n\t\twin = window.open('AssignProjectChild.php?txtID='+(b*1024)+'&act=dailyAssignment&lstShift='+c, 'winSmall', 'toolbar=no,menubar=no,scrollbars=yes,resize=yes,maximize=no,location=no,height=500,width=600'); \r\n\t\twin.creator = self;\r\n\t}else{\r\n\t\twin = window.open('AssignProjectChild.php?txtID='+(b*1024)+'&act=weeklyAssignment&lstShift='+c, 'winSmall', 'toolbar=no,menubar=no,scrollbars=yes,resize=yes,maximize=no,location=no,height=500,width=600'); \r\n\t\twin.creator = self;\r\n\t}\r\n}\r\n\r\nfunction checkPrint(){\r\n\tvar x = document.frm1;\r\n\tif (check_valid_date(x.txtFrom.value) == false){\r\n\t\talert('Invalid From Date. Date Format should be DD/MM/YYYY');\r\n\t\tx.txtFrom.focus();\r\n\t}else if (check_valid_date(x.txtTo.value) == false){\r\n\t\talert('Invalid From Date. Date Format should be DD/MM/YYYY');\r\n\t\tx.txtTo.focus();\r\n\t}else{\r\n\t\tx.action = 'AssignProject.php?prints=yes';\r\n\t\tx.target = '_blank';\r\n\t\tx.submit();\r\n\t}\r\n}\r\n\r\nfunction checkSearch(){\r\n\tvar x = document.frm1;\r\n\tif (check_valid_date(x.txtFrom.value) == false){\r\n\t\talert('Invalid From Date. Date Format should be DD/MM/YYYY');\r\n\t\tx.txtFrom.focus();\r\n\t}else if (check_valid_date(x.txtTo.value) == false){\r\n\t\talert('Invalid From Date. Date Format should be DD/MM/YYYY');\r\n\t\tx.txtTo.focus();\r\n\t}else{\r\n\t\tx.action = 'AssignProject.php?prints=no';\r\n\t\tx.target = '_self';\r\n\t\tx.submit();\r\n\t}\r\n}\r\n</script>\r\n</center></body></html>";

?>