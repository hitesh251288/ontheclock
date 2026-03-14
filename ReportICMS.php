<?php


ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$conn = mssql_connection("127.0.0.1", "EPCMS", "sa", "cms@123");
$act = $_GET["act"];
if ($act == "") {
    $act = $_POST["act"];
}
$prints = $_GET["prints"];
$excel = $_GET["excel"];
$csv = $_GET["csv"];
$message = $_GET["message"];
if ($message == "") {
    $message = "Daily Attendance Report <br> (It is recommended that you DO NOT use a long Date Period)";
}
$txtFrom = $_POST["txtFrom"];
$txtTo = $_POST["txtTo"];
if ($txtFrom == "") {
    $txtFrom = displayToday();
}
if ($txtTo == "") {
    $txtTo = displayToday();
}
$txtEmpNo = $_POST["txtEmpNo"];
$txtName = $_POST["txtName"];
$txtDept = $_POST["txtDept"];
$txtKiosk = $_POST["txtKiosk"];
if ($csv != "yes") {
    print "<html><title>Daily Attendance Report</title>";
}
if ($prints != "yes") {
    print "<body>";
} else {
    if ($csv == "yes") {
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=ReportICMS.csv");
        header("Pragma: no-cache");
        header("Expires: 0");
    } else {
        if ($excel == "yes") {
            header("Content-type: application/x-msdownload");
            header("Content-Disposition: attachment; filename=ReportICMS.xls");
            header("Pragma: no-cache");
            header("Expires: 0");
        } else {
            if ($prints == "yes") {
                print "<body onLoad='javascript:window.print()'>";
                print "<center>";
            }
        }
    }
}
print "<center>";
if ($excel != "yes") {
    displayHeader($prints, true, false);
}
if ($excel != "yes") {
    print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b></font></p>";
}
print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='ReportICMS.php'><input type='hidden' name='act' value='searchRecord'>";
print "<table width='800' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
if ($excel != "yes") {
    print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Select ONE or MORE options and click 'Search Record'</b></font></td></tr>";
}
print "<tr>";
print "<td colspan='2'><table width='100%' cellspacing='-1' cellpadding='-1' border='0'><tr>";
displayTextbox("txtEmpNo", "Employee No:", $txtEmpNo, $prints, 12, "25%", "10%");
displayTextbox("txtName", "Employee Name:", $txtName, $prints, 12, "40%", "25%");
print "</tr></table></td>";
print "</tr>";
print "<tr>";
print "<td colspan='2'><table width='100%' cellspacing='-1' cellpadding='-1' border='0'><tr>";
displayTextbox("txtDept", "Department:", $txtDept, $prints, 12, "25%", "10%");
displayTextbox("txtKiosk", "Location:", $txtKiosk, $prints, 12, "40%", "25%");
print "</tr></table></td>";
print "</tr>";
print "<tr>";
print "<td colspan='2'><table width='100%' cellspacing='-1' cellpadding='-1' border='0'><tr>";
displayTextbox("txtFrom", "Date From <font size='1'>(DD/MM/YYYY)</font>: ", $txtFrom, $prints, 12, "25%", "10%");
displayTextbox("txtTo", "Date To <font size='1'>(DD/MM/YYYY)</font>: ", $txtTo, $prints, 12, "40%", "25%");
print "</tr></table></td>";
print "</tr>";
print "<tr><td>&nbsp;</td><td><input name='btSearch' type='submit' value='Search Record'>";
print "&nbsp;&nbsp;<input type='button' value='Excel' onClick='checkPrint(1)'>";
print "</td></tr>";
print "</table><br>";
print "</form>";
for ($i = 0; $i < 2; $i++) {
    $comp = "Petro Chemicals";
    if ($i == 1) {
        $conn = mssql_connection("127.0.0.1", "IFCMS", "sa", "cms@123");
        $comp = "Fertilizers";
    }
    $count = 0;
    print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800'>";
    print $comp;
    print "<tr><td><font face='Verdana' size='1'><b>No</b></font></td> <td><font face='Verdana' size='1'><b>Name</b></font></td> <td><font face='Verdana' size='1'><b>Date</b></font></td> <td><font face='Verdana' size='1'><b>Time</b></font></td> <td><font face='Verdana' size='1'>IN/Out</font></td> <td><font face='Verdana' size='1'><b>Dept</b></font></td> <td><font face='Verdana' size='1'><b>Location</b></font></td></tr>";
    $query = "SELECT a.EmpNo AS [EMPLOYEE NO], a.EmpName AS [EMPLOYEE NAME], b.IdDate AS [Date], STUFF(b.IdTime,1,11,'') AS [Time], b.InOut, c.DepartmentName AS [DEPARTMENT], d.NodeName AS [CLOCKED AT] FROM tblTAData b LEFT JOIN tblEmployee a on a.ICardNo = b.ICardNo LEFT JOIN tblDepartment c ON c.DepartmentCode = a.DepartmentCode LEFT JOIN tblNodeSetup d ON d.NodeCode = b.NodeCode WHERE b.IdDate >= '" . displayParadoxDate(insertDate($txtFrom)) . " 00:00:00' AND b.IdDate <= '" . displayParadoxDate(insertDate($txtTo)) . " 00:00:00' ";
    if ($txtEmpNo != "") {
        $query .= " AND a.EmpNo LIKE '%" . $txtEmpNo . "%' ";
    }
    if ($txtName != "") {
        $query .= " AND a.EmpName LIKE '%" . $txtName . "%' ";
    }
    if ($txtDept != "") {
        $query .= " AND c.DepartmentName LIKE '%" . $txtDept . "%' ";
    }
    if ($txtKiosk != "") {
        $query .= " AND d.NodeName LIKE '%" . $txtKiosk . "%' ";
    }
    $query .= " ORDER BY d.NodeName, a.EmpNo, C.DepartmentName";
    $result = mssql_query($query, $conn);
    if (0 < mssql_num_rows($result)) {
        while ($cur = mssql_fetch_row($result)) {
            print "<tr><td><font face='Verdana' size='1'>" . $cur[0] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[1] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[2] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[3] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[4] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[5] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[6] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[7] . "</font></td> </tr>";
            $count++;
        }
    }
    print "</table>";
    if ($excel != "yes") {
        print "<br><p align='center'><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $count . "</b></font>";
    }
    print "</p>";
}
print "</center></body></html>";

?>