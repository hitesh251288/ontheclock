<?php


ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "18";
$conn = openConnection();
$iconn = openIConnection();
$act = $_GET["act"];
if ($act == "") {
    $act = $_POST["act"];
}
$g_id = $_GET["id"];
$message = $_GET["message"];
if ($message == "") {
    $message = "Employee Record Display";
}
print "<html><title>Employee Record Display</title><body onLoad=javascript:window.location.href='ReportEmployeeDisplay.php?act=searchRecord&id=" . $g_id . "'>";
print "<center>";
if ($excel != "yes") {
    displayHeader($prints, false, false);
}
print "<center>";
if ($prints != "yes") {
}
print "</center>";
if ($excel != "yes") {
    print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b></font></p>";
}
$query = "";
if ($act == "searchRecord") {
    sleep(3);
    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tuser.idno, tuser.remark, tuser.phone, tuser.OT1, tuser.OT2, tuser.OldID1, tuser.datelimit, tuser.PassiveType, tuser.flagdatelimit, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10, tuser.group_id, tenter.e_time FROM tuser, tgroup, tenter WHERE tuser.group_id = tgroup.id AND tuser.id = tenter.e_id AND tenter.ed = (SELECT MAX(ed) from tenter WHERE tenter.g_id = " . $g_id . " AND tenter.e_date = " . insertToday() . ")";
    $counter = 0;
    for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $counter++) {
        if ($cur[3] == "") {
            $cur[3] = "&nbsp;";
        }
        if ($cur[5] == "") {
            $cur[5] = "&nbsp;";
        }
        if ($cur[6] == "") {
            $cur[6] = "&nbsp;";
        }
        if ($prints != "yes") {
            print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800'>";
        } else {
            print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%'>";
        }
        displayToday();
        displayTime($cur[25]);
        addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
        print "<tr><td><img src='img/usr/" . $cur[0] . ".jpg'></td><td><font face='Verdana' size='1'>" . displayToday() . "<br>" . displayTime($cur[25]) . "</font></td><td><font face='Verdana' size='2'>ID: <b>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</b>" . "<br><br>Name: <b>" . $cur[1] . "</b>" . "<br><br>" . $_SESSION[$session_variable . "IDColumnName"] . ": <b>" . $cur[5] . "</b>" . "<br><br>Dept: <b>" . $cur[2] . "</b>" . "<br><br>Div/Desg: <b>" . $cur[3] . "</b>" . "<br><br>Rmk: <b>" . $cur[6] . "</b>" . "<br><br>Current Shift: <b>" . $cur[4] . "</b>" . "<br><br>" . $_SESSION[$session_variable . "PhoneColumnName"] . ": <b>" . $cur[7] . "</b>";
        if (substr($cur[11], 1, 8) == "19770430") {
            $startdate = displayDate(substr($cur[13], 1, 8));
        } else {
            $startdate = displayDate(substr($cur[11], 1, 8));
        }
        print "<br><br>Start Date: <b>" . $startdate . "</b>";
        if (substr($cur[11], 9, 8) == "19770430") {
            $enddate = displayDate(substr($cur[13], 9, 8));
        } else {
            $enddate = displayDate(substr($cur[11], 9, 8));
        }
        print "<br><br>End Date: <b>" . $enddate . "</b>" . "<br><br>Status: <b>" . $cur[12] . "</b>" . "</font></td></tr></table>";
    }
    if ($counter == 0) {
        print "<p><font face='Verdana' size='15' color='#000000'><b>EMPLOYEE <font color='#FF0000'>" . $txtEmployeeCode . "</font> NOT FOUND</b></font></p>";
    }
}
echo "\r\n<script>\r\nfunction openWindow(a){\r\n\twindow.open(\"ReportEmployeeDisplay.php?act=viewRecord&txtID=\"+a, \"\",\"height=400;width=400\");\r\n}\r\n\r\nfunction checkPrint(a){\r\n\tvar x = document.frm1;\r\n\tif (a == 0){\r\n\t\tif (confirm('Go Green - Think Twice before you Print this Document \\nAre you sure want to Print?')){\r\n\t\t\tx.action = 'ReportEmployeeDisplay.php?prints=yes';\t\t\t\r\n\t\t}else{\r\n\t\t\treturn;\r\n\t\t}\r\n\t}else{\r\n\t\tx.action = 'ReportEmployeeDisplay.php?prints=yes&excel=yes';\t\t\t\r\n\t}\r\n\tx.target = '_blank';\r\n\tx.submit();\r\n}\r\n\r\nfunction checkSearch(){\r\n\tvar x = document.frm1;\r\n\tx.action = 'ReportEmployeeDisplay.php?prints=no';\r\n\tx.target = '_self';\r\n\tx.btSearch.disabled = true;\r\n\tx.submit();\r\n}\r\n</script>\r\n</center></body></html>";

?>