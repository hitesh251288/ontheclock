<?php
ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
session_start();
$current_module = "27";
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=EmployeeChild.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$iconn = openIConnection();
$uconn = mysqli_connect("127.0.0.1", "unisuser", "unisamho", "UNIS");
print "<link rel=\"stylesheet\" href=\"default.css\" type=\"text/css\" />";
$act = $_GET["act"];
if ($act == "") {
    $act = $_POST["act"];
}
$prints = $_GET["prints"];
$excel = $_GET["excel"];
$message = $_GET["message"];
if ($message == "") {
    $message = "Employee Record";
}
$lstShift = $_POST["lstShift"];
$lstDepartment = $_POST["lstDepartment"];
$lstDivision = $_POST["lstDivision"];
$lstEmployeeIDFrom = $_POST["lstEmployeeIDFrom"];
$lstEmployeeIDTo = $_POST["lstEmployeeIDTo"];
$txtEmployee = $_POST["txtEmployee"];
$txtCardNum = $_POST["txtCardNum"];
$txtID = $_GET["txtID"] / 419;
if ($txtID == "") {
    $txtID = $_POST["txtID"];
}
$txtFrom = $_POST["txtFrom"];
if ($txtFrom == "") {
    $txtFrom = displayToday();
}
$txtRemark = $_POST["txtRemark"];
$txtSNo = $_POST["txtSNo"];
$lstEmployeeStatus = $_POST["lstEmployeeStatus"];
if ($lstEmployeeStatus == "") {
    $lstEmployeeStatus = "Active";
}
$lstMissingData = $_POST["lstMissingData"];
$txtPhone = $_POST["txtPhone"];
$txtStartDate = $_POST["txtStartDate"];
$lstOverWrite = $_POST["lstOverWrite"];
if ($lstOverWrite == "") {
    $lstOverWrite = "No";
}
include 'header.php';
?>
<div class="page-breadcrumb">
        <div class="row">
            <div class="col-12 d-flex no-block align-items-center">
                <h4 class="page-title">Employee Record</h4>
                <div class="ms-auto text-end">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Employee Record
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
<?php
print'<div class="container-fluid">
                <div class="row">
                        <div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12">
                            <div class="card">
                                    <div class="card-body table-responsive">';
//print "<html><title>Employee Record</title><body><center>";
print "<style>input{background-color:'#F0F0F0';font-family:'Verdana';font-size:'10';}</style>";
print "<style>select{background-color:'#FFFFFF';font-family:'Verdana';font-size:'10';}</style>";
if ($act == "viewchangeIDRecord") {
    print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b> <br><br><br><u><b>CAUTION</b></u> <p align='left'><b>1.</b> Please Ensure that you have ALREADY created the NEW Employee ID from ACServer/ UNIS <br><br><b>2A.</b> Please Ensure to DELETE the OLD Employee ID from ACServer/ UNIS AFTER Changing the Code from this Step <br><br><b>OR</b> <br><br><b>2B.</b> Please Ensure to Mark Employee as Passive and DELETE the Fingerprint/ Card Number of Old Employee ID from ACServer/ UNIS <br><br><b>3.</b> Please Ensure NON CLOCKING of Employees on any Terminals during this Process</font></p></p>";
    print "<form name='frm0' method='post' action='EmployeeChild.php' onSubmit='return changeCode()'><input type='hidden' name='act' value='editchangeIDRecord'>";
    print "<table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
    print "<tr>";
    displayTextbox("txtOldID", "Old ID: ", $_GET["txtOldID"], "", 12, "65%", "35%");
    print "</tr>";
    print "<tr>";
    displayTextbox("txtNewID", "New ID: ", $_GET["txtNewID"], "", 12, "65%", "35%");
    print "</tr>";
    print "<tr>";
    displayTextbox("txtFrom", "Date From (DD/MM/YYYY): ", $_GET["txtFrom"], $prints, 12, "65%", "35%");
    print "</tr>";
    print "<tr>";
    print "<td align='right'><font face='Verdana' size='2'><b>Overwrite</b> New ID on EXISTING Old ID Clockings:</font></td><td><select name='lstOverWrite'> <option selected value='" . $lstOverWrite . "'>" . $lstOverWrite . "</option> <option value='No'>No</option> <option value='Yes'>Yes</option> </select></td>";
    print "</tr>";
    if (strpos($userlevel, $current_module . "D") !== false && strpos($userlevel, $current_module . "E") !== false && strpos($userlevel, $current_module . "A") !== false) {
        print "<tr>";
        print "<td>&nbsp;</td><td><input type='submit' value='Save Changes' name='btChangeCode' class='btn btn-primary'></td>";
        print "</tr>";
    }
    print "</table>";
    print "</form>";
} else {
    if ($act == "editchangeIDRecord") {
        $txtOldID = $_POST["txtOldID"];
        $txtNewID = $_POST["txtNewID"];
        $query = "SELECT id FROM tuser WHERE id = " . $txtOldID;
        $result = selectData($conn, $query);
        if ($result[0] == $txtOldID) {
            $result = "";
            $result[0] = "";
            $query = "SELECT id FROM tuser WHERE id = " . $txtNewID;
            $result = selectData($conn, $query);
            if ($result[0] == $txtNewID) {
                $result = "";
                $result[0] = "";
                $overWrite = false;
                if ($lstOverWrite == "No") {
                    $query = "SELECT e_id FROM tenter WHERE e_id = " . $txtNewID;
                    $result = selectData($conn, $query);
                    if ($result[0] == $txtNewID) {
                        $message = "ID CANNOT be changed. Raw Log for New ID " . $txtNewID . " already EXISTS";
                    } else {
                        $overWrite = true;
                    }
                } else {
                    $overWrite = true;
                }
                if ($overWrite) {
                    $query = "SELECT EmployeeCodeLength FROM OtherSettingMaster";
                    $result = selectData($conn, $query);
                    $txtECodeLength = $result[0];
                    $query = "UPDATE IGNORE tenter SET e_id = " . $txtNewID . " WHERE e_id = " . $txtOldID . " AND e_date >= '" . insertDate($txtFrom) . "'";
                    if (updateIData($iconn, $query, true)) {
                        $query = "UPDATE IGNORE DayMaster SET e_id = " . $txtNewID . " WHERE e_id = " . $txtOldID . " AND TDate >= '" . insertDate($txtFrom) . "'";
                        if (updateIData($iconn, $query, true)) {
                            $query = "UPDATE IGNORE WeekMaster SET e_id = " . $txtNewID . " WHERE e_id = " . $txtOldID . " AND LogDate >= '" . insertDate($txtFrom) . "'";
                            if (updateIData($iconn, $query, true)) {
                                $query = "UPDATE IGNORE AttendanceMaster SET EmployeeID = " . $txtNewID . ", EmpID = '" . addZero($txtNewID, $txtECodeLength) . "' WHERE EmployeeID = " . $txtOldID . " AND ADate >= '" . insertDate($txtFrom) . "'";
                                if (updateIData($iconn, $query, true)) {
                                    $query = "UPDATE IGNORE EmployeeFlag SET EmployeeID = " . $txtNewID . " WHERE EmployeeID = " . $txtOldID;
                                    $query = "UPDATE IGNORE FlagDayRotation SET e_id = " . $txtNewID . " WHERE e_id = " . $txtOldID . " AND e_date >= '" . insertDate($txtFrom) . "'";
                                    if (updateIData($iconn, $query, true)) {
                                        $query = "UPDATE IGNORE OTDayRotation SET e_id = " . $txtNewID . " WHERE e_id = " . $txtOldID . " AND e_date >= '" . insertDate($txtFrom) . "'";
                                        if (updateIData($iconn, $query, true)) {
                                            $query = "UPDATE IGNORE OTEmployeeDateExempt SET EmployeeID = " . $txtNewID . " WHERE EmployeeID = " . $txtOldID;
                                            if (updateIData($iconn, $query, true)) {
                                                $query = "UPDATE IGNORE OTEmployeeExempt SET EmployeeID = " . $txtNewID . " WHERE EmployeeID = " . $txtOldID;
                                                if (updateIData($iconn, $query, true)) {
                                                    $query = "UPDATE IGNORE PreApproveOT SET e_id = " . $txtNewID . " WHERE e_id = " . $txtOldID . " AND OTDate >= '" . insertDate($txtFrom) . "'";
                                                    if (updateIData($iconn, $query, true)) {
                                                        $query = "UPDATE IGNORE ProjectLog SET e_id = " . $txtNewID . " WHERE e_id = " . $txtOldID . " AND e_date >= '" . insertDate($txtFrom) . "'";
                                                        if (updateIData($iconn, $query, true)) {
                                                            $query = "UPDATE IGNORE ProxyDelete SET e_id = " . $txtNewID . " WHERE e_id = " . $txtOldID . " AND e_date >= '" . insertDate($txtFrom) . "'";
                                                            if (updateIData($iconn, $query, true)) {
                                                                $query = "UPDATE IGNORE ProxyEmployeeExempt SET EmployeeID = " . $txtNewID . " WHERE EmployeeID = " . $txtOldID;
                                                                if (updateIData($iconn, $query, true)) {
                                                                    $query = "UPDATE IGNORE ShiftRoster SET e_id = " . $txtNewID . " WHERE e_id = " . $txtOldID . " AND e_date >= '" . insertDate($txtFrom) . "'";
                                                                    if (updateIData($iconn, $query, true)) {
                                                                        $query = "UPDATE IGNORE Transact SET Transactquery = REPLACE(Transactquery , 'ID: " . $txtOldID . " ', 'ID: " . $txtNewID . " ') WHERE Transactdate >= '" . insertDate($txtFrom) . "'";
                                                                        if (updateIData($iconn, $query, true)) {
                                                                            $message = "ID Changed Successfully - Old ID: - " . $txtOldID . " New ID: " . $txtNewID;
                                                                            $txtOldID = "";
                                                                            $txtNewID = "";
                                                                        } else {
                                                                            $message = "ID could NOT be changed. Transaction Table Update Error";
                                                                        }
                                                                    } else {
                                                                        $message = "ID could NOT be changed. Shift Roster Table Update Error";
                                                                    }
                                                                } else {
                                                                    $message = "ID could NOT be changed. Proxy Exempt Table Update Error";
                                                                }
                                                            } else {
                                                                $message = "ID could NOT be changed. Proxy Delete Table Update Error";
                                                            }
                                                        } else {
                                                            $message = "ID could NOT be changed. Project Log Table Update Error";
                                                        }
                                                    } else {
                                                        $message = "ID could NOT be changed. Pre Approve OT Table Update Error";
                                                    }
                                                } else {
                                                    $message = "ID could NOT be changed. OT Day Exempt Table Update Error";
                                                }
                                            } else {
                                                $message = "ID could NOT be changed. OT Date Table Update Error";
                                            }
                                        } else {
                                            $message = "ID could NOT be changed. Flag Roster (OT Day Rotation) Table Update Error";
                                        }
                                    } else {
                                        $message = "ID could NOT be changed. Pre Flag (Flag Day Rotation) Table Update Error";
                                    }
                                } else {
                                    $message = "ID could NOT be changed. Attendance Master Table Update Error";
                                }
                            } else {
                                $message = "ID could NOT be changed. Week Master Table Update Error";
                            }
                        } else {
                            $message = "ID could NOT be changed. Day Master Table Update Error";
                        }
                    } else {
                        $message = "ID could NOT be changed. tenter Table Update Error";
                    }
                }
            } else {
                $message = "ID could NOT be changed. New ID " . $txtNewID . " DOES NOT EXIST";
            }
        } else {
            $message = "ID could NOT be changed. Old ID " . $txtOldID . " DOES NOT EXIST";
        }
        header("Location: EmployeeChild.php?act=viewchangeIDRecord&txtOldID=" . $txtOldID . "&txtNewID=" . $txtNewID . "&txtFrom=" . $txtFrom . "&message=" . $message);
    } else {
        if ($act == "viewRecord") {
            print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b></font></p>";
            $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tuser.group_id, tuser.remark, tuser.idno, tuser.datelimit, tuser.cardnum, tuser.Phone, tuser.PassiveType, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser WHERE tuser.id = " . $txtID . " " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
            $result = selectData($conn, $query);
            print "<form name='frm1' method='post' action='EmployeeChild.php'><input type='hidden' name='act' value='editRecord'>";
            print "<table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
            print "<tr>";
            displayTextbox("txtID", "Employee ID: ", $txtID, "yes", 12, "40%", "60%");
            print "</tr>";
            print "<tr>";
            displayTextbox("txtName", "Employee Name: ", $result[1], "", 50, "40%", "60%");
            print "</tr>";
            if (strpos($userlevel, $current_module . "A") !== false && $uconn == "") {
                print "<tr>";
                displayTextbox("txtDepartment", "Change to New Department: ", $result[2], "", 50, "40%", "60%");
                print "</tr>";
            } else {
                print "<input type='hidden' name='txtDepartment' value='" . $result[2] . "'>";
            }
            if (strpos($userlevel, $current_module . "A") !== false && $uconn == "") {
                print "<tr>";
                displayTextbox("txtDivision", "Change to New Division: ", $result[3], "", 50, "40%", "60%");
                print "</tr>";
            } else {
                print "<input type='hidden' name='txtDivision' value='" . $result[3] . "'>";
            }
            print "<tr>";
            displayTextbox("txtRemark", "Remark: ", $result[5], "", 50, "40%", "60%");
            print "</tr>";
            for ($i = 0; $i < 10; $i++) {
                if ($_SESSION[$session_variable . "F" . ($i + 1)] != "") {
                    print "<tr>";
                    displayTextbox("txtF" . ($i + 1), $_SESSION[$session_variable . "F" . ($i + 1)] . ": ", $result[$i + 11], "", 50, "40%", "60%");
                    print "</tr>";
                }
            }
            if (2 < strlen($result[8])) {
                print "<tr>";
                displayTextbox("txtCardNum", "Proximity Card Number: ", $result[8], "", 15, "40%", "60%");
                print "</tr>";
            } else {
                print "<input type='hidden' name='txtCardNum' value='" . $result[8] . "'>";
            }
            print "<tr>";
            displayTextbox("txtIdNo", $_SESSION[$session_variable . "IDColumnName"] . ": ", $result[6], "", 200, "40%", "60%");
            print "</tr>";
            print "<tr>";
            displayTextbox("txtPhone", $_SESSION[$session_variable . "PhoneColumnName"] . ": ", $result[9], "", 200, "40%", "60%");
            print "</tr>";
            print "<tr>";
            $query = "SELECT id, name from tgroup ORDER BY name";
            displayList("lstShift", "Current Shift: ", $result[4], $prints, $conn, $query, "", "40%", "60%");
            print "</tr>";
            print "<tr>";
            displayTextbox("txtOldID", "Old ID: ", $txtOldID, $prints, 12, "40%", "60%");
            print "</tr>";
            print "<tr>";
            $txtStartDate = substr($result[7], 1, 8);
            if ($txtStartDate == "19770430") {
                displayTextbox("txtStartDateDisplay", "Employee Status: ", $result[10], "yes", 12, "40%", "60%");
                displayDate($txtStartDate);
                print "<input type='hidden' name='txtStartDate' value='" . displayDate($txtStartDate) . "'>";
            } else {
                displayTextbox("txtStartDate", "Employment Start Date: ", displayDate($txtStartDate), "yes", 12, "40%", "60%");
            }
            print "</tr>";
            if (strpos($userlevel, $current_module . "E") !== false) {
                print "<tr>";
                print "<td>&nbsp;</td><td><input type='submit' value='Save Changes' class='btn btn-primary'>";
                if (strpos($userlevel, $current_module . "D") !== false) {
                    print "&nbsp;&nbsp;<input type='button' onClick='resetPassword()' value='Reset Password' name='btResetPassword' class='btn btn-primary'></td>";
                }
                print "</tr>";
            }
            print "</table>";
            print "</form>";
        } else {
            if ($act == "editRecord" || $act == "activate" || $act == "deActivate") {
                $query = "SELECT name from tgroup WHERE id = '" . $_POST["lstShift"] . "'";
                $result = selectData($conn, $query);
                $shift_name = $result[0];
                if ($txtOldID == "") {
                    $txtOldID = 0;
                }
                $query = "UPDATE tuser SET Name = '" . replaceString($_POST["txtName"], false) . "', dept = '" . $_POST["txtDepartment"] . "', company = '" . $_POST["txtDivision"] . "', idno = '" . $_POST["txtIdNo"] . "', remark = '" . $_POST["txtRemark"] . "', cardnum = '" . $_POST["txtCardNum"] . "', group_id = '" . $_POST["lstShift"] . "' , Phone = '" . $_POST["txtPhone"] . "', OldID1 = '" . $txtOldID . "', datelimit = CONCAT(SUBSTRING(datelimit, 1, 1), '" . insertDate($txtStartDate) . "', SUBSTRING(datelimit, 10, 8))";
                for ($i = 0; $i < 10; $i++) {
                    if ($_SESSION[$session_variable . "F" . ($i + 1)] != "") {
                        $query .= ", F" . ($i + 1) . " = '" . $_POST["txtF" . ($i + 1)] . "'";
                    }
                }
                $text = "Updated ID: " . $_POST["txtID"] . " - Name = " . $_POST["txtName"] . ", Dept = " . $_POST["txtDepartment"] . ", Division = " . $_POST["txtDivision"] . ", " . $_SESSION[$session_variable . "IDColumnName"] . " = " . $_POST["txtIdNo"] . ", Remarks = " . $_POST["txtRemark"] . ", CardNo = " . $_POST["txtCardNum"] . ", Shift = " . $shift_name . ", Phone = " . $_POST["txtPhone"] . ", OldID1 = " . $txtOldID . ", Start Date = " . $txtStartDate;
                for ($i = 0; $i < 10; $i++) {
                    if ($_SESSION[$session_variable . "F" . ($i + 1)] != "") {
                        $text .= ", F" . ($i + 1) . " = " . $_POST["txtF" . ($i + 1)];
                    }
                }
                if ($act == "activate") {
                    $query = $query . " , datelimit = CONCAT('N', SUBSTRING(datelimit, 2, 16)) ";
                    $text = $text . ", Status = Activated";
                } else {
                    if ($act == "deActivate") {
                        $query = $query . " , datelimit = CONCAT('Y', SUBSTRING(datelimit, 2, 8), '" . getLastDay(insertToday(), 1) . "') ";
                        $text = $text . ", Status = De-Activated";
                    }
                }
                $query = $query . " WHERE id = " . $_POST["txtID"];
                if (updateIData($iconn, $query, true)) {
                    $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                    if (updateIData($iconn, $query, true) && $uconn != "") {
                        $query = "UPDATE tuser SET C_Name = '" . replaceString($_POST["txtName"], false) . "' WHERE L_ID = '" . $_POST["txtID"] . "'";
                        updateIData($uconn, $query, true);
                    }
                }
                print "<html><body onload='javascript:window.close()'></body></html>";
            } else {
                if ($act == "changeCode") {
                    $query = "SELECT id FROM tuser WHERE id = " . $txtNewID;
                    $result = selectData($conn, $query);
                    $data0 = $result[0];
                    if ($data0 != "") {
                        header("Location: EmployeeChild.php?act=viewRecord&txtID=" . $txtID * 419 . "&message=NEW ID " . $txtID . " ALREADY exists in Database");
                    } else {
                        $query = "UPDATE AttendanceMaster SET EmployeeID = " . $txtNewID . ", EmpID = '" . addZero($txtNewID, $_SESSION[$session_variable . "EmployeeCodeLength"]) . "' WHERE EmployeeID = " . $txtID;
                        updateIData($iconn, $query, true);
                        $query = "UPDATE DayMaster SET e_id = " . $txtNewID . " WHERE e_id = " . $txtID;
                        updateIData($iconn, $query, true);
                        $query = "UPDATE FlagDayRotation SET e_id = " . $txtNewID . " WHERE e_id = " . $txtID;
                        updateIData($iconn, $query, true);
                        $query = "UPDATE OTDayRotation SET e_id = " . $txtNewID . " WHERE e_id = " . $txtID;
                        updateIData($iconn, $query, true);
                        $query = "UPDATE OTEmployeeDateExempt SET EmployeeID = " . $txtNewID . " WHERE EmployeeID = " . $txtID;
                        updateIData($iconn, $query, true);
                        $query = "UPDATE OTEmployeeExempt SET EmployeeID = " . $txtNewID . " WHERE EmployeeID = " . $txtID;
                        updateIData($iconn, $query, true);
                        $query = "UPDATE PreApproveOT SET e_id = " . $txtNewID . " WHERE e_id = " . $txtID;
                        updateIData($iconn, $query, true);
                        $query = "UPDATE ProjectLog SET e_id = " . $txtNewID . " WHERE e_id = " . $txtID;
                        updateIData($iconn, $query, true);
                        $query = "UPDATE ProxyDelete SET e_id = " . $txtNewID . " WHERE e_id = " . $txtID;
                        updateIData($iconn, $query, true);
                        $query = "UPDATE tenter SET e_id = " . $txtNewID . " WHERE e_id = " . $txtID;
                        updateIData($iconn, $query, true);
                        $query = "UPDATE WeekMaster SET e_id = " . $txtNewID . " WHERE e_id = " . $txtID;
                        updateIData($iconn, $query, true);
                        $query = "UPDATE tuser SET id = " . $txtNewID . " WHERE id = " . $txtID;
                        updateIData($iconn, $query, true);
                        $text = "Changed Employee ID FROM " . $txtID . " TO " . $txtNewID;
                        $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                        updateIData($iconn, $query, true);
                        header("Location: EmployeeChild.php?act=viewRecord&txtID=" . $txtNewID * 419 . "&message=Employee ID changed Successfully");
                    }
                } else {
                    if ($act == "resetPassword") {
                        $query = "UPDATE tuser SET pwd = 'drowssap' WHERE id = " . $txtID;
                        updateIData($iconn, $query, true);
                        $text = "Reset Password for Employee ID " . $txtID;
                        $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                        updateIData($iconn, $query, true);
                        print "<html><body onload='javascript:window.close()'></body></html>";
                    }
                }
            }
        }
    }
}
print "</div></div></div></div></div>";
echo "\r\n<script>\r\nfunction activateEmployee(){\r\n\tx = document.frm1;\r\n\tif (confirm(\"Save Changes and Activate Employee\")){\r\n\t\tx.act.value = \"activate\";\r\n\t\tx.submit();\r\n\t}\r\n}\r\n\r\nfunction deActivateEmployee(){\r\n\tx = document.frm1;\r\n\tif (confirm(\"Save Changes and De-Activate Employee\")){\r\n\t\tx.act.value = \"deActivate\";\r\n\t\tx.submit();\r\n\t}\r\n}\r\n\r\nfunction openWindow(a){\r\n\twindow.open(\"EmployeeMaster.php?act=viewRecord&txtID=\"+a, \"\",\"height=400;width=400\");\r\n}\r\n\r\nfunction check_valid_date(z){\r\n\t//alert(DD);\r\n\t//alert(MM);\r\n\t//alert(YYYY);\r\n\t//z = DD+\"/\"+MM+\"/\"YYYY;\r\n\tif(z.length != 10 || z.substring(6,10)*1 < 1900 || z.substring(6,10)*1 > 2200){\r\n\t\treturn false;\r\n\t}else{\r\n\t\tif (z.substring(0,2)*1 < 28 && z.substring(3,5)*1 < 13 && z.substring(2,3) == '/'  && z.substring(5,6) == '/'){\r\n\t\t\treturn true;\r\n\t\t}else{\r\n\t\t\tif ((z.substring(3,5)*1 == 4 || z.substring(3,5)*1 == 6 || z.substring(3,5)*1 == 9 || z.substring(3,5)*1 == 11) && z.substring(0,2)*1 < 31){\r\n\t\t\t\treturn true;\r\n\t\t\t}else if (z.substring(3,5)*1 == 2 && z.substring(6,10)*1 % 4 == 0 && z.substring(0,2)*1 < 30){\r\n\t\t\t\treturn true;\r\n\t\t\t}else if (z.substring(3,5)*1 == 2 && z.substring(6,10)*1 % 4 != 0 && z.substring(0,2)*1 < 29){\r\n\t\t\t\treturn true;\r\n\t\t\t}else if ((z.substring(3,5)*1 == 1 || z.substring(3,5)*1 == 3 || z.substring(3,5)*1 == 5 || z.substring(3,5)*1 == 7 || z.substring(3,5)*1 == 8 || z.substring(3,5)*1 == 10 || z.substring(3,5)*1 == 12) && z.substring(0,2)*1 < 32){\r\n\t\t\t\treturn true;\r\n\t\t\t}else{\r\n\t\t\t\treturn false;\r\n\t\t\t}\t\t\r\n\t\t}\r\n\t}\r\n}\r\n\r\nfunction doSaveAs(){\r\n\tvar isReady = true;\r\n\t//alert(\"Here\");\r\n\t//if (document.execCommand){\r\n\t\tif (isReady){\t\t\t\r\n\t\t\tdocument.execCommand(\"SaveAs\");\r\n\t\t}else{\r\n\t\t\talert('Feature available only in Internet Exlorer 4.0 and later.');\r\n\t\t}\r\n\t//}\r\n}\r\n\r\nfunction changeCode(){\r\n\tx = document.frm0;\r\n\tif (x.txtOldID.value.length < 1 || x.txtOldID.value*1 != x.txtOldID.value/1){\r\n\t\talert('Invalid Employee ID - Employee ID Data SHOULD be NUMERIC Digit(s) ONLY');\r\n\t\tx.txtOldID.focus();\r\n\t\treturn false;\r\n\t}else if (x.txtNewID.value.length < 1 || x.txtNewID.value*1 != x.txtNewID.value/1 || x.txtNewID.value == x.txtOldID.value){\r\n\t\talert('Invalid Employee ID - Employee ID Data SHOULD be NUMERIC Digit(s) ONLY');\r\n\t\tx.txtNewID.focus();\r\n\t\treturn false;\r\n\t}else if (check_valid_date(x.txtFrom.value) == false){\r\n\t\talert('Invalid From Date. Date Format should be DD/MM/YYYY');\r\n\t\tx.txtFrom.focus();\r\n\t\treturn false;\r\n\t}else{\r\n\t\tx.btChangeCode.disabled = true;\r\n\t\tx.submit();\r\n\t\treturn true;\r\n\t}\r\n}\r\n\r\nfunction resetPassword(){\r\n\tif (confirm(\"Are you sure you want to Reset Password for this User\")){\r\n\t\tx = document.frm1;\r\n\t\tx.act.value = 'resetPassword';\t\r\n\t\tx.btResetPassword.disabled = true;\r\n\t\tx.submit();\r\n\t}\r\n}\r\n</script>\r\n</center></body></html>";
include 'footer.php';
?>