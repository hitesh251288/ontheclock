<?php


ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "18";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=ReportExitTerminalError.php&message=Session Expired or Security Policy Violated");
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
    $message = "Missing Exit Terminal Clockin Report <br> (This Report may take MORE than Normal time. Please DO NOT Refresh OR Close the Browser)";
}
$lstShift = $_POST["lstShift"];
$lstDepartment = $_POST["lstDepartment"];
$lstDivision = $_POST["lstDivision"];
$txtFrom = $_POST["txtFrom"];
$txtTo = $_POST["txtTo"];
if ($txtFrom == "") {
    $txtFrom = displayToday();
}
if ($txtTo == "") {
    $txtTo = displayToday();
}
print "<html><title>Missing Exit Terminal Clockin Report</title>";
if ($prints != "yes") {
    print "<body>";
} else {
    if ($excel != "yes") {
        print "<body onLoad='javascript:window.print()'>";
    } else {
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=ReportExitTerminalError.xls");
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
    displayLinks($current_module, $userlevel);
}
print "</center>";
if ($excel != "yes") {
    print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b></font></p>";
    if ($prints != "yes") {
        print "<table width='800' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
        print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Enter the Report Date and click 'Search Record'</b></font></td></tr>";
    } else {
        print "<table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
        print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Selected Options</b></font></td></tr>";
    }
    print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='ReportExitTerminalError.php'><input type='hidden' name='act' value='searchRecord'>";
    print "<tr>";
    displayTextbox("txtFrom", "Date From (DD/MM/YYYY): ", $txtFrom, $prints, 12, "25%", "75%");
    print "</tr>";
    print "<tr>";
    displayTextbox("txtTo", "Date To (DD/MM/YYYY): ", $txtTo, $prints, 12, "25%", "75%");
    print "</tr>";
    if ($prints != "yes") {
        print "<tr><td>&nbsp;</td><td><input type='hidden' name='txtSearchDate'><input name='btSearch' type='submit' value='Search Record'></td></tr>";
    }
    print "</table><br>";
}
if ($act == "searchRecord") {
    if ($prints != "yes") {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800'>";
    } else {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%'>";
    }
    print "<tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>Department</font></td> <td><font face='Verdana' size='2'>Division</font></td> <td><font face='Verdana' size='2'>Shift</font></td> </tr>";
    $query = "SELECT DISTINCT(e_id) FROM tenter, tgate WHERE tenter.g_id = tgate.id AND tgate.exit = False ";
    if ($txtFrom != "") {
        $query = $query . " AND VAL(tenter.e_date) >= " . insertDate($txtFrom);
    }
    if ($txtTo != "") {
        $query = $query . " AND VAL(tenter.e_date) <= " . insertDate($txtTo);
    }
    $result = mysqli_query($conn, $query);
    $count = 0;
    $array = "";
    while ($cur = mysqli_fetch_row($result)) {
        $query = "SELECT DISTINCT(e_id) FROM tenter, tgate WHERE tenter.g_id = tgate.id AND tgate.exit = True ";
        if ($txtFrom != "") {
            $query = $query . " AND VAL(tenter.e_date) >= " . insertDate($txtFrom);
        }
        if ($txtTo != "") {
            $query = $query . " AND VAL(tenter.e_date) <= " . insertDate($txtTo);
        }
        $query = $query . " AND e_id = " . $cur[0] . " ORDER BY e_id";
        $result1 = selectData($conn, $query);
        if ($result1[0] == "") {
            $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name FROM tuser, tgroup WHERE tuser.group_id = tgroup.id AND tuser.id = " . $cur[0] . " " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
            $result2 = selectData($conn, $query);
            $array[$count] = $result2[2] . "---" . $result2[1] . "---" . $result2[0] . "---" . $result2[3] . "---" . $result2[4];
            $count++;
        }
    }
    sort($array);
    for ($i = 0; $i < count($array); $i++) {
        $split = split("---", $array[$i]);
        addZero($split[2], $_SESSION[$session_variable . "EmployeeCodeLength"]);
        print "<tr><td><input type='hidden' name='txh" . $i . "' value='" . $split[2] . "'><font face='Verdana' size='1'>" . addZero($split[2], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></td> <td><font face='Verdana' size='1'>" . $split[1] . "</font></td> <td><font face='Verdana' size='1'>" . $split[0] . "</font></td> <td><font face='Verdana' size='1'>" . $split[3] . "</font></td> <td><font face='Verdana' size='1'>" . $split[4] . "</font></td> </tr>";
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