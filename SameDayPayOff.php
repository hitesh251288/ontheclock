<?php


ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "19";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=SameDayPayOff.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$iconn = openIConnection();
$act = $_GET["act"];
if ($act == "") {
    $act = $_POST["act"];
}
$prints = $_GET["prints"];
$excel = $_GET["excel"];
$message = $_GET["message"];
if ($message == "") {
    $message = "Same Day Pay Off";
}
$txtID = $_POST["txtID"];
print "<html><title>Same Day Pay Off</title><body><center>";
print "<style>input{background-color:'#F0F0F0';font-family:'Verdana';font-size:'10';}</style>";
print "<style>select{background-color:'#FFFFFF';font-family:'Verdana';font-size:'10';}</style>";
if ($act == "payOff") {
    header("Location: DayMaster.php?txtID=" . $txtID * 1024 . "");
}
print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b> </font></p>";
print "<form name='frm0' method='post' action='SameDayPayOff.php'>";
print "<table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
if ($txtID == "") {
    print "<tr>";
    displayTextbox("txtID", "Enter Employee ID: ", $txtID, "", 12, "50%", "50%");
    print "</tr>";
} else {
    print "<tr>";
    displayTextbox("txtID", "Enter Employee ID: ", $txtID, "yes", 12, "50%", "50%");
    print "</tr>";
    $query = "SELECT name, company, dept, remark, idno, phone from tuser where id = '" . $txtID . "'";
    $result = selectData($conn, $query);
    print "<tr>";
    displayTextbox("txtName", "Name: ", $result[0], "yes", 12, "50%", "50%");
    print "</tr>";
    print "<tr>";
    displayTextbox("txtDiv", "Div/Desg: ", $result[1], "yes", 12, "50%", "50%");
    print "</tr>";
    print "<tr>";
    displayTextbox("txtDept", "Dept: ", $result[2], "yes", 12, "50%", "50%");
    print "</tr>";
    print "<tr>";
    displayTextbox("txtRmk", "Remark: ", $result[3], "yes", 12, "50%", "50%");
    print "</tr>";
    print "<tr>";
    displayTextbox("txtIdNo", $_SESSION[$session_variable . "IDColumnName"], $result[4], "yes", 12, "50%", "50%");
    print "</tr>";
    print "<tr>";
    displayTextbox("txtPhone", $_SESSION[$session_variable . "PhoneColumnName"], $result[5], "yes", 12, "50%", "50%");
    print "</tr>";
}
print "</tr>";
if (strpos($userlevel, $current_module . "D") !== false && strpos($userlevel, $current_module . "E") !== false && strpos($userlevel, $current_module . "A") !== false) {
    if ($result[0] != "") {
        print "<tr>";
        print "<td>&nbsp;</td><td><input type='submit' value='Pay Off'></td><input type='hidden' name='act' value='payOff'>";
        print "</tr>";
    } else {
        print "<tr>";
        print "<td>&nbsp;</td><td><input type='submit' value='Search'></td>";
        print "</tr>";
    }
    print "<tr>";
    print "<td>&nbsp;</td><td><input type='button' value='Search Other Code' onClick=javascript:window.location.href='SameDayPayOff.php'></td>";
    print "</tr>";
}
print "</table>";
print "</form>";
echo "</center></body></html>";

?>