<?php


ob_start("ob_gzhandler");
error_reporting(E_ERROR);
set_time_limit(0);
ini_set("memory_limit", 0 - 1);
include "Functions.php";
$current_module = "17";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=AlterTimeChild.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$iconn = openIConnection();
$act = $_GET["act"];
if ($act == "") {
    $act = $_POST["act"];
}
$prints = $_GET["prints"];
$message = $_GET["message"];
if ($message == "") {
    $message = "Time Alterations for Improper Clockins";
}
$lstShift = $_POST["lstShift"];
$lstDepartment = $_POST["lstDepartment"];
$lstDivision = $_POST["lstDivision"];
$lstTerminal = $_POST["lstTerminal"];
$lstGate = $_POST["lstGate"];
if ($lstShift == "") {
    $lstShift = $_GET["lstShift"];
}
if ($lstDepartment == "") {
    $lstDepartment = $_GET["lstDepartment"];
}
if ($lstDivision == "") {
    $lstDivision = $_GET["lstDivision"];
}
if ($lstTerminal == "") {
    $lstTerminal = $_GET["lstTerminal"];
}
if ($lstGate == "") {
    $lstTerminal = $_GET["lstGate"];
}
$lstEmployeeIDFrom = $_GET["lstEmployeeIDFrom"];
$lstEmployeeIDTo = $_GET["lstEmployeeIDTo"];
$txtEmployeeCode = $_GET["txtEmployeeCode"];
$txtEmployee = $_GET["txtEmployee"];
$txtFrom = $_GET["txtFrom"];
$txtTo = $_GET["txtTo"];
$txtRemark = $_POST["txtRemark"];
$txtPhone = $_POST["txtPhone"];
$txtSNo = $_POST["txtSNo"];
$lstSort = $_POST["lstSort"];
if ($txtPhone == "") {
    $txtPhone = $_GET["txtPhone"];
}
if ($txtRemark == "") {
    $txtRemark = $_GET["txtRemark"];
}
if ($txtSNo == "") {
    $txtSNo = $_GET["txtSNo"];
}
if ($lstSort == "") {
    $lstSort = $_GET["lstSort"];
}
$lstEmployeeStatus = $_POST["lstEmployeeStatus"];
if ($lstEmployeeStatus == "") {
    $lstEmployeeStatus = $_GET["lstEmployeeStatus"];
}
print "<html><title>Time Alterations for Improper Clockins</title>";
print "<style>input{background-color:'#F0F0F0';font-family:'Verdana';font-size:'10';}</style>";
print "<style>select{background-color:'#FFFFFF';font-family:'Verdana';font-size:'10';}</style>";
if ($act == "0") {
    $ed = $_GET["lstED"] / 1024;
    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tenter.e_date, tenter.e_time, tenter.g_id, tenter.ed, tenter.e_group FROM tuser, tgroup, tenter WHERE tuser.group_id = tgroup.id AND tenter.e_id = tuser.id AND tenter.ed = " . $ed;
    $result = selectData($conn, $query);
    print "<form name='frm2' method='post' action='AlterTimeChild.php?lstShift=" . $lstShift . "&lstDepartment=" . $lstDepartment . "&lstDivision=" . $lstDivision . "&lstEmployeeIDFrom=" . $lstEmployeeIDFrom . "&lstEmployeeIDTo=" . $lstEmployeeIDTo . "&txtEmployee=" . $txtEmployee . "&txtFrom=" . $txtFrom . "&txtTo=" . $txtTo . "&lstTerminal=" . $lstTerminal . "&txtEmployeeCode=" . $txtEmployeeCode . "&lstEmployeeStatus=" . $lstEmployeeStatus . "&txtRemark=" . $txtRemark . "&txtPhone=" . $txtPhone . "&txtSNo=" . $txtSNo . "&lstSort=" . $lstSort . "'><input type='hidden' name='act' value='addRecord'> <input type='hidden' name='lstShift' value='" . $lstShift . "'><input type='hidden' name='lstDepartment' value='" . $lstDepartment . "'><input type='hidden' name='lstDivision' value='" . $lstDivision . "'> <input type='hidden' name='lstED' value='" . $ed . "'> <input type='hidden' name='txtID' value='" . $result[0] . "'> <input type='hidden' name='txtGroup' value='" . $result[9] . "'> <table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
    print "<tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Division</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>Shift</font></td> <td><font face='Verdana' size='2'>Date</font></td> <td><font face='Verdana' size='2'>Time</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>Terminal</font></td> </tr>";
    addZero($result[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
    print "<tr><td><font face='Verdana' size='1'>" . addZero($result[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></td> <td><font face='Verdana' size='1'>" . $result[1] . "</font></td> <td><font face='Verdana' size='1'>" . $result[2] . "</font></td> <td><font face='Verdana' size='1'>" . $result[3] . "</font></td>";
    $query = "SELECT id, name FROM tgroup WHERE id > 1 ORDER BY name";
    displayList("lstShiftTo", "&nbsp;", $result[4], $prints, $conn, $query, "", "2%", "20%");
    displayDate($result[5]);
    substr($result[6], 0, 4);
    print "<td><input name='txtDate' size='12' value='" . displayDate($result[5]) . "'></td> <td><input name='txtTime' size='4' value='" . substr($result[6], 0, 4) . "'></td>";
    $query = "SELECT id, name FROM tgate WHERE name NOT LIKE '' ORDER BY name";
    displayList("lstGate", "&nbsp;", $result[7], $prints, $conn, $query, "", "2%", "20%");
    print "</tr>";
    print "</table><br><input name='btSubmit' type='button' onClick='javascript:checkSubmit(0)' value='Submit Record'></form>";
} else {
    if ($act == "addRecord") {
        $query = "INSERT INTO tenter (e_date, e_time, g_id, e_id, e_group, e_user, e_mode, e_type, e_result, e_etc) VALUES ('" . insertDate($_POST["txtDate"]) . "', '" . $_POST["txtTime"] . "00', " . $_POST["lstGate"] . ", " . $_POST["txtID"] . ", " . $_POST["lstShiftTo"] . ", '0', '3', '3', '0', 'P')";
        if (!updateIData($iconn, $query, true)) {
            $query = "UPDATE tenter SET p_flag = 0 WHERE e_date = '" . insertDate($_POST["txtDate"]) . "' AND e_time = '" . $_POST["txtTime"] . "00' AND g_id = '" . $_POST["lstGate"] . "' AND e_id = '" . $_POST["txtID"] . "' AND e_group = '" . $_POST["lstShiftTo"] . "'";
            updateIData($iconn, $query, true);
        }
        $query = "SELECT ed FROM tenter WHERE e_date = '" . insertDate($_POST["txtDate"]) . "' AND e_time = '" . $_POST["txtTime"] . "00' AND g_id = " . $_POST["lstGate"] . " AND e_id = " . $_POST["txtID"] . " AND e_group = " . $_POST["lstShiftTo"];
        $max = selectData($conn, $query);
        $query = "INSERT INTO AlterLog (Username, ed, DateTo, TimeTo, GateTo, TransactDate, ShiftTo) VALUES ('" . $username . "', " . $max[0] . ", '" . insertDate($_POST["txtDate"]) . "', '" . $_POST["txtTime"] . "00', " . $_POST["lstGate"] . ", " . insertToday() . ", " . $_POST["lstShiftTo"] . ")";
        updateIData($iconn, $query, true);
        print "<body onLoad='javascript:closeWindow()'></body>";
    } else {
        if ($act == "1") {
            $ed = $_GET["lstED"] / 1024;
            $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tenter.e_date, tenter.e_time, tenter.g_id, tenter.ed, tenter.e_group FROM tuser, tgroup, tenter WHERE tenter.e_group = tgroup.id AND tenter.e_id = tuser.id AND tenter.ed = " . $ed;
            $result = selectData($conn, $query);
            print "<form name='frm3' method='post' action='AlterTimeChild.php?lstShift=" . $lstShift . "&lstDepartment=" . $lstDepartment . "&lstDivision=" . $lstDivision . "&lstTerminal=" . $lstTerminal . "&lstEmployeeIDFrom=" . $lstEmployeeIDFrom . "&lstEmployeeIDTo=" . $lstEmployeeIDTo . "&lstEmployee=" . $lstEmployee . "&txtEmployee=" . $txtEmployee . "&txtFrom=" . $txtFrom . "&txtTo=" . $txtTo . "&txtEmployeeCode=" . $txtEmployeeCode . "&lstEmployeeStatus=" . $lstEmployeeStatus . "&txtRemark=" . $txtRemark . "&txtPhone=" . $txtPhone . "&txtSNo=" . $txtSNo . "&lstSort=" . $lstSort . "'><input type='hidden' name='act' value='editRecord'> <input type='hidden' name='lstShiftFrom' value='" . $result[9] . "'><input type='hidden' name='lstDepartment' value='" . $lstDepartment . "'><input type='hidden' name='lstDivision' value='" . $lstDivision . "'> <input type='hidden' name='lstED' value='" . $ed . "'> <input type='hidden' name='txtID' value='" . $result[0] . "'> <input type='hidden' name='txtDateFrom' value='" . $result[5] . "'> <input type='hidden' name='txtTimeFrom' value='" . $result[6] . "'> <input type='hidden' name='txtGateFrom' value='" . $result[7] . "'> <table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
            print "<tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Division</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>Shift</font></td> <td><font face='Verdana' size='2'>Date</font></td> <td><font face='Verdana' size='2'>Time</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>Terminal</font></td> </tr>";
            addZero($result[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
            print "<tr><td><font face='Verdana' size='1'>" . addZero($result[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></td> <td><font face='Verdana' size='1'>" . $result[1] . "</font></td> <td><font face='Verdana' size='1'>" . $result[2] . "</font></td> <td><font face='Verdana' size='1'>" . $result[3] . "</font></td>";
            $query = "SELECT id, name FROM tgroup ORDER BY name";
            displayList("lstShiftTo", "&nbsp;", $result[9], $prints, $conn, $query, "", "2%", "20%");
            displayDate($result[5]);
            substr($result[6], 0, 4);
            print "<td><input name='txtDate' size='12' value='" . displayDate($result[5]) . "'></td> <td><input name='txtTime' size='4' value='" . substr($result[6], 0, 4) . "'></td>";
            $query = "SELECT id, name FROM tgate WHERE name NOT LIKE '' ORDER BY name";
            displayList("lstGate", "&nbsp;", $result[7], $prints, $conn, $query, "", "2%", "20%");
            print "</tr>";
            print "</table><br><input name='btSave' type='button' onClick='javascript:checkSubmit(1)' value='Save Changes'></form>";
        } else {
            if ($act == "editRecord") {
                $ed = $_POST["lstED"];
                $query = "SELECT e_date, e_time, g_id, e_id, e_name, e_idno, e_group, e_user, e_mode, e_type, e_result, e_etc, p_flag, e_uptime, e_upmode FROM tenter WHERE ed = " . $ed;
                $result = selectData($conn, $query);
                if ($_POST["txtTimeFrom"] == $_POST["txtTime"] . "00" && insertDate($_POST["txtDate"]) == $_POST["txtDateFrom"]) {
                    $query = "UPDATE tenter SET g_id = '" . $_POST["lstGate"] . "', e_group = '" . $_POST["lstShiftTo"] . "', e_etc = 'P' WHERE ed = '" . $ed . "'";
                    updateIData($iconn, $query, true);
                    $query = "INSERT INTO AlterLog (Username, ed, DateFrom, TimeFrom, GateFrom, DateTo, TimeTo, GateTo, TransactDate, ShiftFrom, ShiftTo) VALUES ('" . $username . "', " . $ed . ", '" . $_POST["txtDateFrom"] . "', '" . $_POST["txtTimeFrom"] . "', " . $_POST["txtGateFrom"] . ", '" . insertDate($_POST["txtDate"]) . "', '" . $_POST["txtTime"] . "00', " . $_POST["lstGate"] . ", " . insertToday() . ", " . $_POST["lstShiftFrom"] . ", " . $_POST["lstShiftTo"] . ")";
                    updateIData($iconn, $query, true);
                } else {
                    $query = "UPDATE tenter SET p_flag = 1, e_etc = 'D' WHERE ed = " . $ed;
                    updateIData($iconn, $query, true);
                    $query = "INSERT INTO tenter (e_date, e_time, g_id, e_id, e_name, e_idno, e_group, e_user, e_mode, e_type, e_result, e_etc, p_flag, e_uptime, e_upmode) VALUES ('" . insertDate($_POST["txtDate"]) . "', '" . $_POST["txtTime"] . "00', '" . $_POST["lstGate"] . "', '" . $result[3] . "', '" . $result[4] . "', '" . $result[5] . "', '" . $_POST["lstShiftTo"] . "', '" . $result[7] . "', '" . $result[8] . "', '" . $result[9] . "', '" . $result[10] . "', 'P', 0, '" . $result[13] . "', '" . $result[14] . "')";
                    updateIData($iconn, $query, true);
                    $query = "SELECT ed FROM tenter WHERE e_date = '" . insertDate($_POST["txtDate"]) . "' AND e_time = '" . $_POST["txtTime"] . "00' AND e_id = '" . $result[3] . "' AND g_id = '" . $_POST["lstGate"] . "' AND e_etc = 'P' AND p_flag = 0";
                    $sub_result = selectData($conn, $query);
                    if(!isset($sub_result[0])){
                        $sub_result[0] = 0;
                    }
                    $query = "INSERT INTO AlterLog (Username, ed, DateFrom, TimeFrom, GateFrom, DateTo, TimeTo, GateTo, TransactDate, ShiftFrom, ShiftTo) VALUES ('" . $username . "', " . $sub_result[0] . ", '" . $_POST["txtDateFrom"] . "', '" . $_POST["txtTimeFrom"] . "', " . $_POST["txtGateFrom"] . ", '" . insertDate($_POST["txtDate"]) . "', '" . $_POST["txtTime"] . "00', " . $_POST["lstGate"] . ", " . insertToday() . ", " . $_POST["lstShiftFrom"] . ", " . $_POST["lstShiftTo"] . ")";
                    updateIData($iconn, $query, true);
                }
                print "<body onLoad='javascript:closeWindow()'></body>";
            }
        }
    }
}
print "<script>";
print "function closeWindow(){" . "var loc = 'AlterTime.php?act=searchRecord&lstShift=" . $lstShift . "&lstDepartment=" . $lstDepartment . "&lstDivision=" . $lstDivision . "&lstTerminal=" . $lstTerminal . "&lstEmployeeIDFrom=" . $lstEmployeeIDFrom . "&lstEmployeeIDTo=" . $lstEmployeeIDTo . "&lstEmployee=" . $lstEmployee . "&txtEmployee=" . $txtEmployee . "&txtFrom=" . $txtFrom . "&txtTo=" . $txtTo . "&txtEmployeeCode=" . $txtEmployeeCode . "&lstEmployeeStatus=" . $lstEmployeeStatus . "&txtRemark=" . $txtRemark . "&txtPhone=" . $txtPhone . "&txtSNo=" . $txtSNo . "&lstSort=" . $lstSort . "';" . "if (!opener){" . "win.creator.location = loc;" . "}else{" . "opener.location = loc;" . "}" . "window.close();" . "}" . "</script>";
echo "\r\n<script>\r\nfunction checkSubmit(a){\r\n\tx = document.frm2;\r\n\tif (a == 1){\r\n\t\tx = document.frm3;\r\n\t}\r\n\t\r\n\tif (x.lstShiftTo.value == \"\"){\r\n\t\talert('Please select a Shift');\r\n\t\tx.lstShiftTo.focus();\r\n\t}else if (x.lstShiftTo.value < 2){\r\n\t\talert('Please select a Valid Shift');\r\n\t\tx.lstShiftTo.focus();\r\n\t}else if (check_valid_date(x.txtDate.value) == false){\r\n\t\talert('Invalid Date. Date format should ONLY be DD/MM/YYYY');\r\n\t\tx.txtDate.focus();\r\n\t}else if (checkTime(x.txtTime.value) == false){\r\n\t\talert('Invalid Time. Time format should ONLY be HHMM (24 HR Clock format)');\r\n\t\tx.txtTime.focus();\r\n\t}else if (x.lstGate.value == \"\"){\r\n\t\talert('Please select a Terminal');\r\n\t\tx.lstGate.focus();\r\n\t}else{\r\n\t\tif (a == 0){\r\n\t\t\tx.btSubmit.disabled = true;\r\n\t\t}else{\r\n\t\t\tx.btSave.disabled = true;\r\n\t\t}\r\n\t\tx.submit();\r\n\t}\r\n}\r\n\r\nfunction check_valid_date(z){\r\n\t//alert(DD);\r\n\t//alert(MM);\r\n\t//alert(YYYY);\r\n\t//z = DD+\"/\"+MM+\"/\"YYYY;\r\n\tif(z.length != 10 || z.substring(6,10)*1 < 1900 || z.substring(6,10)*1 > 2200){\r\n\t\treturn false;\r\n\t}else{\r\n\t\tif (z.substring(0,2)*1 < 28 && z.substring(3,5)*1 < 13 && z.substring(2,3) == '/'  && z.substring(5,6) == '/'){\r\n\t\t\treturn true;\r\n\t\t}else{\r\n\t\t\tif ((z.substring(3,5)*1 == 4 || z.substring(3,5)*1 == 6 || z.substring(3,5)*1 == 9 || z.substring(3,5)*1 == 11) && z.substring(0,2)*1 < 31){\r\n\t\t\t\treturn true;\r\n\t\t\t}else if (z.substring(3,5)*1 == 2 && z.substring(6,10)*1 % 4 == 0 && z.substring(0,2)*1 < 30){\r\n\t\t\t\treturn true;\r\n\t\t\t}else if (z.substring(3,5)*1 == 2 && z.substring(6,10)*1 % 4 != 0 && z.substring(0,2)*1 < 29){\r\n\t\t\t\treturn true;\r\n\t\t\t}else if ((z.substring(3,5)*1 == 1 || z.substring(3,5)*1 == 3 || z.substring(3,5)*1 == 5 || z.substring(3,5)*1 == 7 || z.substring(3,5)*1 == 8 || z.substring(3,5)*1 == 10 || z.substring(3,5)*1 == 12) && z.substring(0,2)*1 < 32){\r\n\t\t\t\treturn true;\r\n\t\t\t}else{\r\n\t\t\t\treturn false;\r\n\t\t\t}\t\t\r\n\t\t}\r\n\t}\r\n}\r\n\r\nfunction deleteRecord(x){\r\n\tif (confirm('Delete this Record')){\r\n\t\twindow.location.href='AlterTimeChild.php?act=deleteRecord&lstED='+(x*1024);\r\n\t}\r\n}\r\n\r\nfunction checkTime(a){\r\n\t//alert(a);\r\n\tif (a.length != 4){\r\n\t\treturn false;\r\n\t}else if (a*1 != a/1){\r\n\t\treturn false;\r\n\t}else if (a.substring(0, 2)*1 > 24){\r\n\t\treturn false;\r\n\t}else if (a.substring(2, 4)*1 > 59){\r\n\t\treturn false;\r\n\t}\r\n}\r\n</script>\r\n</center></body></html>";

?>