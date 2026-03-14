<?php
ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "15";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
$lstIgnoreActualOT = $_SESSION[$session_variable . "ApproveOTIgnoreActual"];
$txtMACAddress = $_SESSION[$session_variable . "MACAddress"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=RequestApproval.php&message=Session Expired or Security Policy Violated");
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
    if ($lstIgnoreActualOT == "Yes") {
        $message = "Approve Overtime (Details) <br> No ADD Rights on this Module PROHIBITS Approvals ABOVE Pre-Approved (AP2) OT <br> No DELETE Rights on this Module PROHIBITS Approvals ABOVE Approved OT";
    } else {
        $message = "Approve Overtime (Details) <br> No ADD Rights on this Module PROHIBITS Approvals ABOVE Actual OR Pre-Approved (AP2) OT <br> No DELETE Rights on this Module PROHIBITS Approvals ABOVE Actual OR Approved OT";
    }
}
$lstShift = $_POST["lstShift"];
$lstDepartment = $_POST["lstDepartment"];
$lstDivision = $_POST["lstDivision"];
$lstDay = $_POST["lstDay"];
$lstEmployeeIDFrom = $_POST["lstEmployeeIDFrom"];
$lstEmployeeIDTo = $_POST["lstEmployeeIDTo"];
$txtEmployee = $_POST["txtEmployee"];
if ($txtEmployee == "") {
    $txtEmployee = $_GET["txtEmployee"];
}
$txtEmployeeCode = $_POST["txtEmployeeCode"];
if ($txtEmployeeCode == "") {
    $txtEmployeeCode = $_GET["txtEmployeeCode"];
}
$txtFrom = $_POST["txtFrom"];
if ($txtFrom == "") {
    $txtFrom = $_GET["txtFrom"];
}
$txtTo = $_POST["txtTo"];
if ($txtTo == "") {
    $txtTo = $_GET["txtTo"];
}
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
$txtSNo = $_POST["txtSNo"];
$lstEmployeeStatus = "";
if (isset($_POST["lstEmployeeStatus"])) {
    $lstEmployeeStatus = $_POST["lstEmployeeStatus"];
} else {
    $lstEmployeeStatus = "ACT";
}
$lstType = $_POST["lstType"];
if ($lstType == "") {
    $lstType = "";
}
$lstColourFlag = $_POST["lstColourFlag"];
$lstSort = $_POST["lstSort"];
$txtRemark = $_POST["txtRemark"];
$txtPhone = $_POST["txtPhone"];
$txtARemark = $_POST["txtARemark"];
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
print "<html><title>Approve Overtime (Details)</title>";
if ($prints != "yes") {
    print "<body>";
} else {
    if ($excel != "yes") {
        print "<body onLoad='javascript:window.print()'>";
    } else {
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=RequestApproval.xls");
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
if ($act == "editRecord") {
    $count = $_POST["txtCount"];
    $update_flag = false;
    for ($i = 0; $i < $count; $i++) {
        if (is_numeric($_POST["txtAOT" . $i] * 60) && 0 <= $_POST["txtAOT" . $i] && ($_POST["txtAOT" . $i] != $_POST["txhOldAOT" . $i] || $_POST["txtARemark" . $i] != $_POST["txhARemark" . $i])) {
            if (strpos($userlevel, $current_module . "A") !== false) {
                $update_flag = true;
            } else {
                $query = "SELECT OT FROM PreApproveOT WHERE OTDate = '" . $_POST["txhDate" . $i] . "' AND e_id = '" . $_POST["txh" . $i] . "' AND A3 = '1'";
                $result = selectData($conn, $query);
                if ($result[0] != "" && $_POST["txtAOT" . $i] <= $result[0] * 60 && $lstIgnoreActualOT == "Yes") {
                    $update_flag = true;
                } else {
                    if ($result[0] != "" && $_POST["txtAOT" . $i] <= $result[0] * 60 && $lstIgnoreActualOT == "No" && $_POST["txtAOT" . $i] * 1 <= $_POST["txhAOT" . $i] * 1) {
                        $update_flag = true;
                    }
                }
            }
            if ($update_flag) {
                if (getRegister(encryptDecrypt($txtMACAddress), 7) == "25") {
                    $query = "UPDATE AttendanceMaster SET LateInColumn = " . $_POST["txtLateOut" . $i] * 60 . ", Remark = '" . $_POST["txtARemark" . $i] . "' WHERE AttendanceID = " . $_POST["txhID" . $i];
                } else {
                    $query = "UPDATE AttendanceMaster SET AOvertime = " . $_POST["txtAOT" . $i] * 60 . ", Remark = '" . $_POST["txtARemark" . $i] . "' WHERE AttendanceID = " . $_POST["txhID" . $i];
                }
                updateIData($iconn, $query, true);
                $text = "Updated Approve OT for ID: " . $_POST["txh" . $i] . " - " . $_POST["txtAOT" . $i] . " minutes on " . displayDate($_POST["txhDate" . $i]) . ", Set Remark = " . $_POST["txtARemark" . $i];
                $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                updateIData($iconn, $query, true);
            }
        }
    }
    if (strpos($userlevel, $current_module . "A") !== false) {
        $message = "Record(s) saved Successfully";
    } else {
        if ($lstIgnoreActualOT == "Yes") {
            $message = "Record(s) saved Successfully <br>Approvals will NOT be posted with Overtime MORE THAN Pre-Approved (AP2) OT";
        } else {
            $message = "Record(s) saved Successfully <br>Approvals will NOT be posted with Overtime MORE THAN Actual OT/ Pre-Approved (AP2) OT";
        }
    }
    $act = "searchRecord";
}
if ($excel != "yes") {
    print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b></font></p>";
}
if ($prints != "yes") {
    print "<table width='800' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
    print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Select ONE or MORE options and click 'Search Record'</b></font></td></tr>";
} else {
    if ($excel != "yes") {
        print "<table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
        print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Selected Options</b></font></td></tr>";
    }
}
print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='ApproveOvertime.php'><input type='hidden' name='act' value='searchRecord'> <input type='hidden' name='lstIgnoreActualOT' value='" . $lstIgnoreActualOT . "'> <tr>";
if ($excel != "yes") {
    print "<tr>";
    $query = "SELECT id, name from tgroup WHERE id > 1 ORDER BY name";
    displayList("lstShift", "Shift: ", $lstShift, $prints, $conn, $query, "", "25%", "75%");
    print "</tr>";
    displaySearchFields($conn, $prints, $session_variable, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $lstEmployeeName, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
    print "<tr>";
    print "<td colspan='2'><table width='100%' cellspacing='-1' cellpadding='-1' border='0'><tr>";
    displayTextbox("txtFrom", "Date From <font size='1'>(DD/MM/YYYY)</font>: ", $txtFrom, $prints, 12, "25%", "25%");
    if ($prints != "yes") {
        print "<td align='right' width='25%'><font face='Verdana' size='2'>Day:</font></td><td width='25%'><select name='lstDay' class='form-control'><option selected value='" . $lstDay . "'>" . $lstDay . "</option> <option value='Sunday'>Sunday</option> <option value='Monday'>Monday</option> <option value='Tuesday'>Tuesday</option> <option value='Wednesday'>Wednesday</option> <option value='Thursday'>Thursday</option> <option value='Friday'>Friday</option> <option value='Saturday'>Saturday</option></select></td>";
    } else {
        displayTextbox("lstDay", "Day: ", $lstDay, $prints, 12, "25%", "25%");
    }
    print "</tr></table></td>";
    print "</tr>";
    print "<tr>";
    print "<td colspan='2'><table width='100%' cellspacing='-1' cellpadding='-1' border='0'><tr>";
    displayTextbox("txtTo", "Date To <font size='1'>(DD/MM/YYYY)</font>: ", $txtTo, $prints, 12, "25%", "25%");
    if ($prints != "yes") {
        print "<td align='right' width='25%'><font face='Verdana' size='2'>Work Type:</font></td><td width='25%'><select name='lstType' class='form-control'><option selected value='" . $lstType . "'>" . $lstType . "</option> <option value='Early In'>Early In</option> <option value='Late In'>Late In</option> <option value='Less Break'>Less Break</option> <option value='More Break'>More Break</option> <option value='Early Out'>Early Out</option> <option value='Late Out'>Late Out</option> <option value='Grace'>Grace</option> <option value='OT'>OT</option> <option value='Approved OT'>Approved OT</option> </select></td>";
    } else {
        displayTextbox("lstType", "Work Type: ", $lstType, $prints, 12, "25%", "25%");
    }
    print "</tr></table></td>";
    print "</tr>";
    if ($prints != "yes") {
        if (strpos($userlevel, $current_module . "D") !== false) {
            print "<input type='hidden' name='txhDeleteRight' value='1'>";
        } else {
            print "<input type='hidden' name='txhDeleteRight' value='0'>";
        }
        print "<tr>";
        displayTextbox("txtARemark", "Attendance Remark: ", $txtARemark, $prints, 12, "25%", "25%");
        print "</tr>";
        print "<tr>";
        if ($prints != "yes") {
            displayColourFlag($conn, $lstColourFlag, "lstColourFlag", true, true);
        } else {
            displayTextbox("lstColourFlag", "Flag: ", $lstColourFlag, "yes", 1, "25%", "25%");
        }
        print "</tr>";
        print "<tr>";
        displayEmployeeStatus($conn, "lstEmployeeStatus", $lstEmployeeStatus, "25%", "25%");
        print "</tr>";
        print "<tr>";
        $array = array(array("tuser.id, AttendanceMaster.ADate", "Employee Code"), array("tuser.name, tuser.id, AttendanceMaster.ADate", "Employee Name - Code"), array("tuser.dept, tuser.id, AttendanceMaster.ADate", "Dept - Code"), array("tuser.company, tuser.dept, tuser.id, AttendanceMaster.ADate", "Div/Desg - Dept - Code"), array("tuser.company, tuser.dept, tuser.group_id, tuser.id, AttendanceMaster.ADate", "Div - Dept - Current Shift - Code"), array("AttendanceMaster.ADate", "Date"), array("AttendanceMaster.Day, tuser.id, AttendanceMaster.ADate", "Day"), array("AttendanceMaster.Week, tuser.id, AttendanceMaster.ADate", "Week"));
        displaySort($array, $lstSort, 8);
        print "</tr>";
        print "<tr><td>&nbsp;</td><td><input name='btSearch' type='submit' value='Search Record' class='btn btn-primary'>&nbsp;&nbsp;<input type='button' value='Excel' onClick='checkPrint(1)' class='btn btn-primary'>&nbsp;&nbsp;<input type='button' value='CSV' onClick='checkPrint(3)' class='btn btn-primary'></td></tr></td></tr>";
    }
    print "</table><br>";
}
if ($act == "searchRecord") {
    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company,e.rdate,e.latecome,e.earlyleave,e.absentday,e.ap1,e.ap2 from tuser LEFT JOIN emprequest e ON e.empid=tuser.id where empid!=''";
    $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
    $result = mysqli_query($conn, $query);
    print "<form method='post' action='RequestApproval.php'>";
    print "<table width='800' border='1' cellpadding='1' bordercolor='#C0C0C0'>";
    print "<tr><th><font face='Verdana' size='2'>ID</font></th><th><font face='Verdana' size='2'>Name</font></th><th><font face='Verdana' size='2'>Dept</font></th><th><font face='Verdana' size='2'>Div</font></th><th><font face='Verdana' size='2'>Date</font></th><th><font face='Verdana' size='2'>Reason for late come</font></th><th><font face='Verdana' size='2'>Reason for early leave</font></th><th><font face='Verdana' size='2'>Reason for absent</font></th><th><font face='Verdana' size='2'>AP1</font></th><th><font face='Verdana' size='2'>AP2</font></th></tr>";

    while ($row = mysqli_fetch_array($result)) {
        ?>
        <tr>
            <td><font face='Verdana' size='2'><?php echo $row[0]; ?></font></td>
            <td><font face='Verdana' size='2'><?php echo $row[1]; ?></font></td>
            <td><font face='Verdana' size='2'><?php echo $row[2]; ?></font></td>
            <td><font face='Verdana' size='2'><?php echo $row[3]; ?></font></td>
            <td><font face='Verdana' size='2'><?php echo isset($row[4]) && !empty($row[4]) ? date('d/m/Y', strtotime($row[4])) : ''; ?></font></td>
            <td><font face='Verdana' size='2'><?php echo $row[5]; ?></font></td>
            <td><font face='Verdana' size='2'><?php echo $row[6]; ?></font></td>
            <td><font face='Verdana' size='2'><?php echo $row[7]; ?></font></td>
            <td><font face='Verdana' size='2'><?php if(isset($row[8]) && !empty($row[8])){ echo $row[8]; }else{?><input type='checkbox' name='chkAOT1[]' value="ap1<?php echo ',' . $row[0] . ',' . $row[4]; ?>"/><?php } ?><input type="hidden" name="ap1id[]" value="<?php echo $row[0]; ?>"/><input type="hidden" name="ap1username" value="<?php echo $username; ?>"/><input type="hidden" name="rdate[]" value="<?php echo $row[4]; ?>"/></font></td>
            <td><font face='Verdana' size='2'><?php if(isset($row[9]) && !empty($row[9])){ echo $row[9]; }else{?><input type='checkbox' name='chkAOT2[]' value="ap2<?php echo ',' . $row[0] . ',' . $row[4]; ?>"/><?php } ?><input type="hidden" name="ap2id" value="<?php echo $row[0]; ?>"/><input type="hidden" name="ap2username" value="<?php echo $username; ?>"/></font></td>
        </tr>
        <?php
    }
    print "</table>";
    print "<br><input name='btSubmit' type='submit' value='Save Changes' class='btn btn-primary'>";
    print "</form>";
}
if (isset($_POST['btSubmit'])) {
    if (isset($_POST['ap1username']) && !empty($_POST['ap1username'])) {
        $ap1username = $_POST['ap1username'];
    }
    if (isset($_POST['ap2username']) && !empty($_POST['ap2username'])) {
        $ap2username = $_POST['ap2username'];
    }
    if (!empty($_POST['chkAOT1']) || !empty($_POST['chkAOT2'])) {
        foreach ($_POST['chkAOT1'] as $value) {
            $ap1data = explode(',', $value);
            $updateap1 = "UPDATE emprequest SET ap1='$ap1username' where empid='$ap1data[1]' AND rdate='$ap1data[2]'";
            updateIData($iconn, $updateap1, true);
        }
        foreach ($_POST['chkAOT2'] as $val) {
            $ap2data = explode(',', $val);
            $updateap2 = "UPDATE emprequest SET ap2='$ap2username' where empid='$ap2data[1]' AND rdate='$ap2data[2]'";
            updateIData($iconn, $updateap2, true);
        }
    }
}
echo "\r\n<script>\r\nfunction submitRecord(){\r\n\tx = document.frm1;\r\n\tx.act.value='editRecord';\r\n\tx.submit();\r\n}\r\n\r\nfunction approveOT(x, y, z, zz){\t\r\n\tif (x.checked == true){\r\n\t\tif (y.value == 0 || y.value == ''){\r\n\t\t\tif (document.frm1.txhDeleteRight.value == 0 && document.frm1.lstIgnoreActualOT.value == \"Yes\"  && z.value*1 > zz.value*1){\r\n\t\t\t\talert('Approved OT value has to be LESS THAN Approved OT value');\r\n\t\t\t\tx.checked = false;\r\n\t\t\t}else if (document.frm1.txhDeleteRight.value == 0 && document.frm1.lstIgnoreActualOT.value == \"No\" && (z.value*1 > y.value*1 || z.value*1 > zz.value*1)){\r\n\t\t\t\talert('Approved OT value has to be LESS THAN OT/ Approved OT value');\r\n\t\t\t\tx.checked = false;\r\n\t\t\t}else{\r\n\t\t\t\ty.value = z.value;\r\n\t\t\t}\r\n\t\t}\r\n\t}else{\r\n\t\ty.value = 0;\t\r\n\t}\r\n}\r\n\r\nfunction checkOTValue(x, y, z){\t\r\n\tif (x.value == '' || x.value*1 != x.value/1 || x.value*1 > 1440){\r\n\t\talert('Please enter a valid Approved OT Value');\r\n\t\tx.focus();\r\n\t\treturn;\r\n\t}else if (document.frm1.txhDeleteRight.value == 0 && document.frm1.lstIgnoreActualOT.value == \"Yes\"  && x.value*1 > z.value*1){\r\n\t\talert('Approved OT value has to be LESS THAN Approved OT value');\r\n\t\tx.focus();\r\n\t\treturn;\r\n\t}else if (document.frm1.txhDeleteRight.value == 0 && document.frm1.lstIgnoreActualOT.value == \"No\"  && (x.value*1 > y.value*1 || x.value*1 > z.value*1)){\r\n\t\talert('Approved OT value has to be LESS THAN OT/ Approved OT value');\r\n\t\tx.focus();\r\n\t\treturn;\r\n\t}\r\n}\r\n\r\nfunction approveAll(){\t\r\n\tx = document.frm1;\r\n\ty = x.chkAOT;\r\n\tz = x.txtCount.value;\r\n\tx.btSearch.disabled = true;\r\n\tx.btSubmit.disabled = true;\r\n\ty.disabled = true;\r\n\tfor (i=0;i<z;i++){\t\t\r\n\t\tif (y.checked == true){\t\t\t\r\n\t\t\tdocument.getElementById(\"chkAOT\"+i).checked = true;\r\n\t\t\tif (document.getElementById(\"txtAOT\"+i).value == 0 || document.getElementById(\"txtAOT\"+i).value == ''){\r\n\t\t\t\tif (document.frm1.txhDeleteRight.value == 0){\r\n\t\t\t\t\t//Do Nothing\r\n\t\t\t\t}else{\r\n\t\t\t\t\tdocument.getElementById(\"txtAOT\"+i).value = document.getElementById(\"txhAOT\"+i).value;\r\n\t\t\t\t}\r\n\t\t\t}\r\n\t\t}else{\r\n\t\t\tdocument.getElementById(\"chkAOT\"+i).checked = false;\r\n\t\t\tdocument.getElementById(\"txtAOT\"+i).value = 0;\t\r\n\t\t}\r\n\t}\r\n\tx.btSearch.disabled = false;\r\n\tx.btSubmit.disabled = false;\r\n\ty.disabled = false;\r\n}\r\n\r\nfunction copyRemarkAll(){\r\n\tif (confirm(\"COPY Attendance Remark from FIRST row to all other BLANK Remark Fields\")){\r\n\t\tvar x = document.frm1;\r\n\t\tvar count = x.txtCount.value;\r\n\t\tif (count > 0){\r\n\t\t\tif (x.txtARemark0.value != \"\"){\r\n\t\t\t\tfor (i=0;i<count;i++){\r\n\t\t\t\t\tif (document.getElementById(\"txtARemark\"+i).value == \"\" || document.getElementById(\"txtARemark\"+i).value == \".\"){\r\n\t\t\t\t\t\tdocument.getElementById(\"txtARemark\"+i).value = x.txtARemark0.value;\r\n\t\t\t\t\t}\r\n\t\t\t\t}\r\n\t\t\t}\r\n\t\t}\r\n\t}\r\n}\r\n\r\nfunction resetRemarkAll(){\r\n\tif (confirm(\"Reset All Attendance Remarks\")){\r\n\t\tvar x = document.frm1;\r\n\t\tvar count = x.txtCount.value;\r\n\t\t//alert(count);\r\n\t\tfor (i=0;i<count;i++){\r\n\t\t\tdocument.getElementById(\"txtARemark\"+i).value = \"\";\r\n\t\t}\r\n\t}\t\r\n}\r\n</script>\r\n</center></body></html>";
?>