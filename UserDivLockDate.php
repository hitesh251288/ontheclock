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
    header("Location: " . $config["REDIRECT"] . "?url=UserDivLockDate.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$iconn = openIConnection();
$jconn = openIConnection();
$kconn = openIConnection();
print "<html><title>Division wise Lock Dates</title><body><center>";
$act = $_GET["act"];
if ($act == "") {
    $act = $_POST["act"];
}
$message = $_GET["message"];
if ($message == "") {
    $message = "Division wise Lock Dates. <br>Lock Date on Global Settings will be SET to the LOWEST Date Value below";
}
$txtCount = $_POST["txtCount"];
if ($act == "editRecord") {
    $query = "DELETE FROM UserDivLockDate";
    if (updateIData($iconn, $query, true)) {
        for ($i = 0; $i < $txtCount; $i++) {
            if ($_POST["txh" . $i] != "") {
                $query = "INSERT INTO UserDivLockDate (Date, UserDivLockDate.Div) VALUES ('" . insertDate($_POST["txtDate" . $i]) . "', '" . $_POST["txh" . $i] . "')";
                updateIData($jconn, $query, true);
                $query = "UPDATE OtherSettingMaster SET LockDate = (SELECT MIN(Date) FROM UserDivLockDate)";
                updateIData($kconn, $query, true);
            }
        }
    }
    $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Updated User Division Lock/ Migration Date)";
    updateIData($iconn, $query, true);
    $message = "Record Updated";
    header("Location: UserDivLockDate.php?message=" . $message);
}
echo "<script>\r\nfunction saveChanges(){\r\n\tx = document.frm1;\r\n\tflag = true;\t\r\n\tfor (i=0;i<x.txtCount.value;i++){\r\n\t\tif (check_valid_date(document.getElementById(\"txtDate\"+i).value) == false && document.getElementById(\"txh\"+i).value != ''){\r\n\t\t\talert(\"Invald Date Format. Date Format should be DD/MM/YYYY\");\r\n\t\t\tflag = false;\r\n\t\t\tdocument.getElementById(\"txtDate\"+i).focus();\t\t\t\t\r\n\t\t\tbreak;\r\n\t\t}\r\n\t}\r\n\t\r\n\tif (flag){\r\n\t\tif (confirm('Save Changes')){\r\n\t\t\tx.bt.disabled = true;\r\n\t\t\tx.submit();\r\n\t\t}\r\n\t}\r\n}\r\n</script>\r\n";
print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b></font></p>";
print "<style>input{background-color:'#F0F0F0';font-family:'Verdana';font-size:'10';}</style>";
print "<style>select{background-color:'#FFFFFF';font-family:'Verdana';font-size:'10';}</style>";
print "<script>" . "function check_valid_date(z){" . "if(z.length != 10 || z.substring(6,10)*1 < 1900 || z.substring(6,10)*1 > 2200){" . "return false;" . "}else{" . "if (z.substring(0,2)*1 < 28 && z.substring(3,5)*1 < 13 && z.substring(2,3) == '/'  && z.substring(5,6) == '/'){" . "return true;" . "}else{" . "if ((z.substring(3,5)*1 == 4 || z.substring(3,5)*1 == 6 || z.substring(3,5)*1 == 9 || z.substring(3,5)*1 == 11) && z.substring(0,2)*1 < 31){" . "return true;" . "}else if (z.substring(3,5)*1 == 2 && z.substring(6,10)*1 % 4 == 0 && z.substring(0,2)*1 < 30){" . "return true;" . "}else if (z.substring(3,5)*1 == 2 && z.substring(6,10)*1 % 4 != 0 && z.substring(0,2)*1 < 29){" . "return true;" . "}else if ((z.substring(3,5)*1 == 1 || z.substring(3,5)*1 == 3 || z.substring(3,5)*1 == 5 || z.substring(3,5)*1 == 7 || z.substring(3,5)*1 == 8 || z.substring(3,5)*1 == 10 || z.substring(3,5)*1 == 12) && z.substring(0,2)*1 < 32){" . "return true;" . "}else{" . "return false;" . "}" . "}" . "}" . "}" . "</script>";
print "<table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
print "<form name='frm1' method='post' action='UserDivLockDate.php'><input type='hidden' name='act' value='editRecord'>";
print "<tr>";
print "<td align='right'>&nbsp;</td><td><font face='Verdana' size='2'><b>Divisions</b></font></td>";
print "</tr>";
print "<tr>";
$count = 0;
$query = "SELECT UserDivLockDate.Div, Date FROM UserDivLockDate ORDER BY UserDivLockDate.Div";
for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $count++) {
    print "<tr>";
    displayDate($cur[1]);
    print "<td align='right'><input type='hidden' name='txh" . $count . "' id='txh" . $count . "' value='" . $cur[0] . "'><font face='Verdana' size='1'>" . $cur[0] . "</font></td><td><input name='txtDate" . $count . "' id='txtDate" . $count . "' value='" . displayDate($cur[1]) . "'></td>";
    print "</tr>";
}
$query = "SELECT DISTINCT(company) FROM tuser WHERE company NOT IN (SELECT UserDivLockDate.Div FROM UserDivLockDate) AND company <> '' ORDER BY company";
for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $count++) {
    print "<tr>";
    print "<td align='right'><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'><font face='Verdana' size='1'>" . $cur[0] . "</font></td><td><input name='txtDate" . $count . "' id='txtDate" . $count . "' value=''></td>";
    print "</tr>";
}
if (stripos($userlevel, $current_module . "E") !== false) {
    print "<tr><td>&nbsp;</td><td><input name='bt' type='button' value='Save Changes' onClick='javascript:saveChanges()'>";
}
print "</td></tr>";
print "<input type='hidden' name='txtCount' value='" . $count . "'></form>";
echo "</table>\r\n</center></body></html>";

?>