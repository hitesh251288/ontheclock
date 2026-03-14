<?php


ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "27";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
$txtMACAddress = $_SESSION[$session_variable . "MACAddress"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=EmployeeMaster.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$iconn = openIConnection();
//$uconn = mysqli_connect("127.0.0.1", "unisuser", "unisamho", "UNIS");
$uconn = mysqli_connect("localhost", "root", "namaste", "unis");
$act = $_GET["act"];
if ($act == "") {
    $act = $_POST["act"];
}
$prints = $_GET["prints"];
$excel = $_GET["excel"];
$csv = $_GET["csv"];
$message = $_GET["message"];
if ($message == "") {
    $message = "Employee Records";
}
$lstShift = $_POST["lstShift"];
$lstDepartment = $_POST["lstDepartment"];
$lstDivision = $_POST["lstDivision"];
$lstEmployeeIDFrom = $_POST["lstEmployeeIDFrom"];
$lstEmployeeIDTo = $_POST["lstEmployeeIDTo"];
$txtEmployeeCode = $_POST["txtEmployeeCode"];
$txtEmployee = $_POST["txtEmployee"];
$lstSort = $_POST["lstSort"];
$txtFrom = $_POST["txtFrom"];
$txtTo = $_POST["txtTo"];
if ($txtFrom == "") {
    $txtFrom = displayToday();
}
if ($txtTo == "") {
    $txtTo = displayToday();
}
$txtRemark = $_POST["txtRemark"];
$txtSNo = $_POST["txtSNo"];
$txtPhone = $_POST["txtPhone"];
$lstEmployeeStatus = "";
if (isset($_POST["lstEmployeeStatus"])) {
    $lstEmployeeStatus = $_POST["lstEmployeeStatus"];
} else {
    $lstEmployeeStatus = "ACT";
}
$lstMissingData = $_POST["lstMissingData"];
$txtOT1 = $_POST["txtOT1"];
$txtOT2 = $_POST["txtOT2"];
$lstStatus = $_POST["lstStatus"];
$lstFingerRegistered = $_POST["lstFingerRegistered"];
$lstCardRegistered = $_POST["lstCardRegistered"];
$txtStartDateFrom = $_POST["txtStartDateFrom"];
$txtStartDateTo = $_POST["txtStartDateTo"];
$txtEndDateFrom = $_POST["txtEndDateFrom"];
$txtEndDateTo = $_POST["txtEndDateTo"];
$lstSelectedDepartment = $_POST["lstSelectedDepartment"];
$lstSelectedDivision = $_POST["lstSelectedDivision"];
$txtSelectedRemark = $_POST["txtSelectedRemark"];
$txtSelectedSNo = $_POST["txtSelectedSNo"];
$txtSelectedPhone = $_POST["txtSelectedPhone"];
$lstSelectedStatus = $_POST["lstSelectedStatus"];
$lstSelectedLevel = $_POST["lstSelectedLevel"];
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
$lstContractAccess = $_POST["lstContractAccess"];
//$txtSelectedEndDate = $_POST["txtSelectedEndDate"];
$lstSelectContractAccess = $_POST["lstSelectContractAccess"];
if ($act == "saveChanges" || $act == "activate" || $act == "deActivate" || $act == "migrate" || $act == "emigrate") {
    for ($i = 0; $i < $_POST["txtCount"]; $i++) {
        if ($_POST["chk" . $i] != "") {
            if ($act == "saveChanges") {
                $query = "UPDATE tuser SET id = " . $_POST["txhID" . $i];
                $text = "Updated ID: " . $_POST["txhID" . $i] . " SET ";
                if ($_POST["lstSelectedDepartment"] != "") {
                    $query = $query . " , dept = '" . $_POST["lstSelectedDepartment"] . "' ";
                    $text = $text . ", Department = " . $_POST["lstSelectedDepartment"];
                    if ($uconn != "") {
                        mysqli_query($uconn, "UPDATE UNIS.temploye SET C_Post = (SELECT C_Code FROM UNIS.cPost WHERE C_Name = '" . $_POST["lstSelectedDepartment"] . "') WHERE L_UID = '" . $_POST["txhID" . $i] . "'");
                    }
                }
                if ($_POST["lstSelectedDivision"] != "") {
                    $query = $query . " , company = '" . $_POST["lstSelectedDivision"] . "' ";
                    $text = $text . ", " . $_SESSION[$session_variable . "DivColumnName"] . " = " . $_POST["lstSelectedDivision"];
                    if ($uconn != "") {
                        mysqli_query($uconn, "UPDATE UNIS.temploye SET C_Office = (SELECT C_Code FROM UNIS.cOffice WHERE C_Name = '" . $_POST["lstSelectedDivision"] . "') WHERE L_UID = '" . $_POST["txhID" . $i] . "'");
                    }
                }
                if ($_POST["txtSelectedSNo"] != "") {
                    $query = $query . " , idno = '" . $_POST["txtSelectedSNo"] . "' ";
                    $text = $text . ", " . $_SESSION[$session_variable . "IDColumnName"] . " = " . $_POST["txtSelectedSNo"];
                }
                if ($_POST["txtSelectedRemark"] != "") {
                    $query = $query . " , remark = '" . $_POST["txtSelectedRemark"] . "' ";
                    $text = $text . ", " . $_SESSION[$session_variable . "RemarkColumnName"] . " = " . $_POST["txtSelectedRemark"];
                }
                if ($_POST["txtSelectedPhone"] != "") {
                    $query = $query . " , phone = '" . $_POST["txtSelectedPhone"] . "' ";
                    $text = $text . ", " . $_SESSION[$session_variable . "PhoneColumnName"] . " = " . $_POST["txtSelectedPhone"];
                }
                for ($j = 0; $j < 10; $j++) {
                    if ($_POST["lstSelectedF" . ($j + 1)] != "") {
                        $query = $query . " , F" . ($j + 1) . " = '" . $_POST["lstSelectedF" . ($j + 1)] . "' ";
                        $text = $text . ", " . $_SESSION[$session_variable . "txtF" . ($j + 1)] . " = " . $_POST["lstSelectedF" . ($j + 1)];
                    }
                }
                if ($_POST["lstSelectedLevel"] != "") {
                    $query = $query . " , UserStatus = '" . $_POST["lstSelectedLevel"] . "' ";
                    $text = $text . ", Level = " . $_POST["lstSelectedLevel"];
                }
                if ($_POST["lstSelectContractAccess"] != "") {
                    $query = $query . " , CAGID = '" . $_POST["lstSelectContractAccess"] . "' ";
                    $text = $text . ", Access Group ID = " . $_POST["lstSelectContractAccess"];
                }
                $query = $query . " WHERE id = " . $_POST["txhID" . $i];
                $text = $text . " WHERE id = " . $_POST["txhID" . $i];
                if (getRegister(encryptDecrypt($txtMACAddress), 7) == "39") {
                    $unis_query = "UPDATE UNIS.tuser SET L_UID = '" . $_POST["txhID" . $i] . "' ";
                    if ($_POST["txtSelectedPhone"] != "") {
                        $unis_query .= " ,C_Unique = '" . $_POST["txtSelectedPhone"] . "'";
                    }
                    if ($_POST["txtSelectedRemark"] != "") {
                        $unis_query .= " ,C_UserMessage = '" . $_POST["txtSelectedRemark"] . "'";
                    }
                    $unis_query .= " WHERE L_UID = '" . $_POST["txhID" . $i] . "' ";
                    mysqli_query($uconn, $unis_query);
                    if ($_POST["txtSelectedF6"] != "") {
                        $unis_query = "UPDATE UNIS.temploye SET C_Phone = '" . $_POST["txtSelectedF6"] . "' WHERE L_UID = '" . $_POST["txhID" . $i] . "' ";
                        mysqli_query($uconn, $unis_query);
                    }
                }
                if (getRegister(encryptDecrypt($txtMACAddress), 7) == "133") {
                    $unis_query = "UPDATE UNIS.tuser, UNIS.temploye SET UNIS.tuser.L_ID = '" . $_POST["txhID" . $i] . "' ";
                    if ($_POST["lstSelectedF2"] != "") {
                        $unis_query .= " ,UNIS.temploye.C_Address = '" . $_POST["lstSelectedF2"] . "' ";
                    }
                    if ($_POST["txtSelectedRemark"] != "") {
                        $unis_query .= " ,UNIS.temploye.C_Staff = (SELECT C_Code FROM cStaff WHERE C_Name = '" . $_POST["txtSelectedRemark"] . "') ";
                    }
                    if ($_POST["lstSelectedF4"] != "") {
                        $unis_query .= " ,UNIS.temploye.C_Remark = '" . $_POST["lstSelectedF4"] . "' ";
                    }
                    if ($_POST["lstSelectedF5"] != "") {
                        $unis_query .= " ,UNIS.temploye.C_Phone = '" . $_POST["lstSelectedF5"] . "' ";
                    }
                    if ($_POST["lstSelectedF3"] != "") {
                        $unis_query .= " ,UNIS.tuser.C_UserMessage = '" . $_POST["lstSelectedF3"] . "' ";
                    }
                    if ($_POST["txtSelectedPhone"] != "") {
                        $unis_query .= " ,UNIS.tuser.C_Unique = '" . $_POST["txtSelectedPhone"] . "'";
                    }
                    $unis_query .= " WHERE UNIS.tuser.L_ID = '" . $_POST["txhID" . $i] . "' AND UNIS.tuser.L_ID = UNIS.temploye.L_UID ";
                    mysqli_query($uconn, $unis_query);
                }
                if (getRegister(encryptDecrypt($txtMACAddress), 7) == "165") {
                    $unis_query = "UPDATE UNIS.tuser, UNIS.temploye SET UNIS.tuser.L_ID = '" . $_POST["txhID" . $i] . "' ";
                    if ($_POST["txtSelectedRemark"] != "") {
                        $unis_query .= " ,UNIS.temploye.C_Staff = (SELECT C_Code FROM cStaff WHERE C_Name = '" . $_POST["txtSelectedRemark"] . "') ";
                    }
                    if ($_POST["lstSelectedF1"] != "") {
                        $unis_query .= " ,UNIS.tuser.C_Notice = '" . $_POST["lstSelectedF1"] . "' ";
                    }
                    if ($_POST["lstSelectedF2"] != "") {
                        $unis_query .= " ,UNIS.temploye.C_Address = '" . $_POST["lstSelectedF2"] . "' ";
                    }
                    if ($_POST["lstSelectedF3"] != "") {
                        $unis_query .= " ,UNIS.tuser.C_UserMessage = '" . $_POST["lstSelectedF3"] . "' ";
                    }
                    if ($_POST["lstSelectedF4"] != "") {
                        $unis_query .= " ,UNIS.temploye.C_Meal = (SELECT UNIS.tmealtype.C_Code FROM UNIS.tmealtype, UNIS.temploye WHERE UNIS.temploye.C_Meal = UNIS.tmealtype.C_Code AND UNIS.tmealtype.C_Name = '" . $_POST["lstSelectedF4"] . "') ";
                    }
                    if ($_POST["lstSelectedF5"] != "") {
                        $unis_query .= " ,UNIS.temploye.C_Money = (SELECT UNIS.tmoney.C_Code FROM UNIS.tmoney, UNIS.temploye WHERE UNIS.temploye.C_Money = UNIS.tmoney.C_Code AND UNIS.tmoney.C_Name = '" . $_POST["lstSelectedF5"] . "') ";
                    }
                    if ($_POST["lstSelectedF6"] != "") {
                        $unis_query .= " ,UNIS.temploye.C_Email = '" . $_POST["lstSelectedF6"] . "' ";
                    }
                    if ($_POST["lstSelectedF7"] != "") {
                        $unis_query .= " ,UNIS.temploye.C_Phone = '" . $_POST["lstSelectedF7"] . "' ";
                    }
                    if ($_POST["txtSelectedSNo"] != "") {
                        $unis_query .= " ,UNIS.tuser.C_passbackstatus = (SELECT UNIS.cpassback.C_Code FROM UNIS.cpassback, UNIS.tuser WHERE UNIS.tuser.C_PassbackStatus = UNIS.cpassback.C_Code AND UNIS.cpassback.C_Name = '" . $_POST["txtSelectedSNo"] . "') ";
                    }
                    $unis_query .= " WHERE UNIS.tuser.L_ID = '" . $_POST["txhID" . $i] . "' AND UNIS.tuser.L_ID = UNIS.temploye.L_UID ";
                    mysqli_query($uconn, $unis_query);
                }
                if (updateIData($iconn, $query, true)) {
                    $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                    updateIData($iconn, $query, true);
                } else {
                    printf("Error message: %s\n", mysqli_error($iconn));
                    printf("Error message: %s\n", mysql_error($iconn));
                }
            } else {
                if ($act == "activate") {
                    if ($uconn != "") {
                        mysqli_query($uconn, "UPDATE UNIS.tuser SET L_OptDateLimit = 0 WHERE L_ID = '" . $_POST["txhID" . $i] . "'");
                    }
                    $query = "UPDATE tuser SET datelimit = CONCAT('N', SUBSTRING(datelimit, 2, 16)), PassiveType = 'ACT', PassiveRemark = '.' WHERE id = " . $_POST["txhID" . $i] . " AND SUBSTRING(datelimit, 2, 16) NOT LIKE '1977043019770430'";
                    if (updateIData($iconn, $query, true)) {
                        $query = "UPDATE tuser SET datelimit = CONCAT('N', SUBSTRING(flagdatelimit, 2, 16)), PassiveType = 'ACT', PassiveRemark = '.' WHERE id = " . $_POST["txhID" . $i] . " AND SUBSTRING(datelimit, 2, 16) = '1977043019770430'";
                        if (updateIData($iconn, $query, true)) {
                            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Activated User ID: " . $_POST["txhID" . $i] . "')";
                            if (updateIData($iconn, $query, true)) {
                                $query = "UPDATE ADALog SET DateTo = '" . insertToday() . "' WHERE DateTo IS NULL AND e_id = '" . $_POST["txhID" . $i] . "' ";
                                updateIData($iconn, $query, true);
                                $message = "Records Activated";
                            }
                        }
                    }
                } else {
                    if ($act == "deActivate") {
//                        echo $query = "UPDATE UNIS.tuser SET L_OptDateLimit = 1, C_DateLimit = CONCAT(SUBSTRING(C_DateLimit, 1, 8), '" . insertDate($_POST["txtSelectedEndDate"]) . "') WHERE L_ID = '" . $_POST["txhID" . $i];die;
                        if ($uconn != "") {
                            mysqli_query($uconn, "UPDATE UNIS.tuser SET L_OptDateLimit = 1, C_DateLimit = CONCAT(SUBSTRING(C_DateLimit, 1, 8), '" . insertDate($_POST["txtSelectedEndDate"]) . "') WHERE L_ID = '" . $_POST["txhID" . $i] . "'");
                        }
                        $query = "UPDATE tuser SET datelimit = CONCAT('Y', SUBSTRING(datelimit, 2, 8), '" . insertDate($_POST["txtSelectedEndDate"]) . "'), PassiveType = '" . $_POST["lstSelectedStatus"] . "' WHERE id = " . $_POST["txhID" . $i];
                        if (updateIData($iconn, $query, true)) {
                            $query = "DELETE FROM FlagDayRotation WHERE e_date > '" . insertDate($_POST["txtSelectedEndDate"]) . "' AND RecStat = 0 AND e_id = " . $_POST["txhID" . $i];
                            if (updateIData($iconn, $query, true)) {
                                $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'De-Activated User ID: " . $_POST["txhID" . $i] . ", End Date: " . insertDate($_POST["txtSelectedEndDate"]) . ", Status: " . $_POST["lstSelectedStatus"] . "')";
                                updateIData($iconn, $query, true);
                                $message = "Records De Activated";
                            }
                        }
                    } else {
                        if ($act == "migrate") {
                            $migrate_count = 0;
                            $query = "SELECT name from tuser WHERE id = " . $_POST["txhID" . $i];
                            $result = selectData($conn, $query);
                            if ($result[0] != "" && $result[0] != $_POST["txhID" . $i]) {
                                $migrate_id = $_POST["txhID" . $i];
                                $migrate_id = $migrate_id * 7;
                                $query = "INSERT INTO PAX (id, N1, N2, N3, N4, N5, N6, N7, N8, N9, N0) VALUES ('" . $migrate_id . "', '" . strrev(substr($result[0], 0, 2)) . "', '" . strrev(substr($result[0], 2, 2)) . "', '" . strrev(substr($result[0], 4, 2)) . "', '" . strrev(substr($result[0], 6, 2)) . "', '" . strrev(substr($result[0], 8, 2)) . "', '" . strrev(substr($result[0], 10, 2)) . "', '" . strrev(substr($result[0], 12, 2)) . "', '" . strrev(substr($result[0], 14, 2)) . "', '" . strrev(substr($result[0], 16, 2)) . "', '" . strrev(substr($result[0], 18, strlen($result[0]))) . "')";
                                if (updateIData($iconn, $query, true)) {
                                    $migrate_count++;
                                } else {
                                    $query = "UPDATE PAX SET N1 = '" . strrev(substr($result[0], 0, 2)) . "', N2 = '" . strrev(substr($result[0], 2, 2)) . "', N3 = '" . strrev(substr($result[0], 4, 2)) . "', N4 = '" . strrev(substr($result[0], 6, 2)) . "', N5 = '" . strrev(substr($result[0], 8, 2)) . "', N6 = '" . strrev(substr($result[0], 10, 2)) . "', N7 = '" . strrev(substr($result[0], 12, 2)) . "', N8 = '" . strrev(substr($result[0], 14, 2)) . "', N9 = '" . strrev(substr($result[0], 16, 2)) . "', N0 = '" . strrev(substr($result[0], 18, strlen($result[0]))) . "' WHERE id = " . $migrate_id;
                                    if (updateIData($iconn, $query, true)) {
                                        $migrate_count++;
                                    }
                                }
                                if (0 < $migrate_count) {
                                    $query = "UPDATE tuser SET name = id WHERE id = " . $_POST["txhID" . $i];
                                    if (updateIData($iconn, $query, true)) {
                                        $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Migrated User ID: " . $_POST["txhID" . $i] . "')";
                                        updateIData($iconn, $query, true);
                                        $message = "Records Migrated";
                                    }
                                }
                            }
                        } else {
                            if ($act == "emigrate") {
                                $migrate_count = 0;
                                $query = "SELECT name from tuser WHERE id = " . $_POST["txhID" . $i];
                                $result = selectData($conn, $query);
                                if ($result[0] != "" && $result[0] == $_POST["txhID" . $i]) {
                                    $migrate_id = $_POST["txhID" . $i];
                                    $migrate_id = $migrate_id * 7;
                                    $pax_name = "";
                                    $pax_query = "SELECT N1, N2, N3, N4, N5, N6, N7, N8, N9, N0 FROM PAX WHERE id = " . $migrate_id;
                                    $pax_result = selectData($conn, $pax_query);
                                    if ($pax_result[0] != "") {
                                        for ($pax_i = 0; $pax_i < 10; $pax_i++) {
                                            $pax_name .= strrev($pax_result[$pax_i]);
                                        }
                                    }
                                    if ($pax_name != "") {
                                        $query = "UPDATE tuser SET name = '" . $pax_name . "' WHERE id = " . $_POST["txhID" . $i];
                                        if (updateIData($iconn, $query, true)) {
                                            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Emigrated User ID: " . $_POST["txhID" . $i] . "')";
                                            updateIData($iconn, $query, true);
                                            $message = "Records Emigrated";
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    $act = "searchRecord";
}
displaySuperHeader($prints, $excel, $csv, $current_module, $userlevel, "Employee Settings", false, false);
if ($excel != "yes") {
    print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b></font></p>";
    if ($prints != "yes") {
        print "<table width='800' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
        print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Select ONE or MORE options and click 'Search Record'</b></font></td></tr>";
    } else {
        print "<table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
        print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Selected Options</b></font></td></tr>";
    }
    print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='EmployeeMaster.php'><input type='hidden' name='act' value='searchRecord'><tr>";
    $query = "SELECT id, name from tgroup ORDER BY name";
    displayList("lstShift", "Current Shift: ", $lstShift, $prints, $conn, $query, "", "25%", "75%");
    print "</tr>";
    displaySearchFields($conn, $prints, $session_variable, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $lstEmployeeName, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
    if ($prints != "yes") {
        print "<tr>";
        print "<td colspan='2'><table width='100%' cellspacing='-1' cellpadding='-1' border='0'><tr>";
        print "<td align='right' width='25%'><font face='Verdana' size='2'>Registered Finger Print:</font></td><td width='25%'><select name='lstFingerRegistered' class='form-control'> <option selected value='" . $lstFingerRegistered . "'>" . $lstFingerRegistered . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> <option value='---'>---</option> </select></td>";
        displayTextbox("txtOT1", "OT1 Day: ", $txtOT1, $prints, 12, "25%", "25%");
        print "</tr></table></td>";
        print "</tr>";
        print "<tr>";
        print "<td colspan='2'><table width='100%' cellspacing='-1' cellpadding='-1' border='0'><tr>";
        print "<td align='right' width='25%'><font face='Verdana' size='2'>Registered Card:</font></td><td width='25%'><select name='lstCardRegistered' class='form-control'> <option selected value='" . $lstCardRegistered . "'>" . $lstCardRegistered . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> <option value='---'>---</option> </select></td>";
        displayTextbox("txtOT2", "OT2 Day: ", $txtOT2, $prints, 12, "25%", "25%");
        print "</tr></table></td>";
        print "</tr>";
        print "<tr>";
        print "<td colspan='2'><table width='100%' cellspacing='-1' cellpadding='-1' border='0'><tr>";
        displayEmployeeStatus($conn, "lstEmployeeStatus", $lstEmployeeStatus, "25%", "25%");
        print "<td align='right' width='25%'><font face='Verdana' size='2'>User Level:</font></td><td width='25%'><select name='lstStatus' class='form-control'> <option selected value='" . $lstStatus . "'>" . $lstStatus . "</option> <option value='1'>1</option><option value='2'>2</option><option value='3'>3</option><option value='4'>4</option><option value='5'>5</option><option value='6'>6</option><option value='7'>7</option><option value='8'>8</option><option value='9'>9</option><option value='10'>10</option></select></td>";
        print "</tr></table></td>";
        print "</tr>";
        print "<tr>";
        print "<td colspan='2'><table width='100%' cellspacing='-1' cellpadding='-1' border='0'><tr>";
        displayTextbox("txtStartDateFrom", "Start Date From: ", $txtStartDateFrom, $prints, 12, "25%", "25%");
        displayTextbox("txtEndDateFrom", "End Date From <font size='1'>(DD/MM/YYYY)</font>:", $txtEndDateFrom, $prints, 12, "25%", "25%");
        print "</tr></table></td>";
        print "</tr>";
        print "<tr>";
        print "<td colspan='2'><table width='100%' cellspacing='-1' cellpadding='-1' border='0'><tr>";
        displayTextbox("txtStartDateTo", "Start Date To <font size='1'><font size='1'>(DD/MM/YYYY)</font></font>: ", $txtStartDateTo, $prints, 12, "25%", "25%");
        displayTextbox("txtEndDateTo", "End Date To <font size='1'><font size='1'>(DD/MM/YYYY)</font></font>: ", $txtEndDateTo, $prints, 12, "25%", "25%");
        print "</tr></table></td>";
        print "</tr>";
        print "<tr>";
        print "<td colspan='2'><table width='100%' cellspacing='-1' cellpadding='-1' border='0'><tr>";
        print "<td align='right' width='25%'><font face='Verdana' size='2'>Missing Data:</font></td><td width='25%'><select name='lstMissingData' class='form-control'> <option selected value='" . $lstMissingData . "'>" . $lstMissingData . "</option> <option value='Missing Name'>Missing Name</option> <option value='Missing Dept'>Missing Dept</option> <option value='Missing " . $_SESSION[$session_variable . "DivColumnName"] . "'>Missing " . $_SESSION[$session_variable . "DivColumnName"] . "</option> <option value='Missing " . $_SESSION[$session_variable . "IDColumnName"] . "'>Missing " . $_SESSION[$session_variable . "IDColumnName"] . "</option> <option value='Missing " . $_SESSION[$session_variable . "PhoneColumnName"] . "'>Missing " . $_SESSION[$session_variable . "PhoneColumnName"] . "</option> <option value='Missing " . $_SESSION[$session_variable . "RemarkColumnName"] . "'>Missing " . $_SESSION[$session_variable . "RemarkColumnName"] . "</option> <option value=''>---</option></select></td>";
        $query = "SELECT CAGID, Name from CAG ORDER BY Name";
        displayList("lstContractAccess", "Access Group: ", $lstContractAccess, $prints, $conn, $query, "", "25%", "25%");
        print "</tr></table></td>";
        print "</tr>";
        print "<tr>";
        print "<td colspan='2'><table width='100%' cellspacing='-1' cellpadding='-1' border='0'><tr>";
        $array = array(array("tuser.id", "Employee ID"), array("tuser.name, tuser.id", "Employee Name - ID"), array("tuser.PassiveType, tuser.id", "Employee Status - ID"), array("tuser.dept, tuser.id", "Dept - ID"), array("tuser.company, tuser.dept, tuser.id", "Div/Desg - Dept - ID"), array("tuser.company, tuser.dept, tuser.group_id, tuser.id", "Div - Dept - Current Shift - ID"));
        displaySort($array, $lstSort, 6);
        print "<td align='right' width='25%'>&nbsp;</td><td width='25%'>&nbsp;</td>";
        print "</tr></table></td>";
        print "</tr>";
        print "<tr><td>&nbsp;</td><td><input name='btSearch' type='submit' value='Search Record'>";
        if (strpos($userlevel, $current_module . "D") !== false && strpos($userlevel, $current_module . "E") !== false && strpos($userlevel, $current_module . "A") !== false) {
            print "&nbsp;&nbsp; <input name='btSearch' type='button' value='Change Employee ID' onClick='javascript:changeID()'>";
        }
        print "</td></tr>";
    }
    print "</table><br>";
}
if ($act == "searchRecord") { //tuser.CAGID = CAG.CAGID AND 
$query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tuser.idno, tuser.remark, tuser.phone, tuser.OT1, tuser.OT2, tuser.OldID1, tuser.datelimit, tuser.PassiveType, tuser.flagdatelimit, tuser.UserStatus, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10, CAG.Name FROM tuser, tgroup, CAG WHERE tuser.group_id = tgroup.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
    if ($lstMissingData != "") {
        if ($lstMissingData == "Missing Name") {
            $query = $query . " AND LENGTH(tuser.Name) < 1";
        } else {
            if ($lstMissingData == "Missing Dept") {
                $query = $query . " AND LENGTH(tuser.dept) < 1";
            } else {
                if ($lstMissingData == "Missing " . $_SESSION[$session_variable . "DivColumnName"]) {
                    $query = $query . " AND LENGTH(tuser.company) < 1";
                } else {
                    if ($lstMissingData == "Missing " . $_SESSION[$session_variable . "RemarkColumnName"]) {
                        $query = $query . " AND LENGTH(tuser.Remark) < 1";
                    } else {
                        if ($lstMissingData == "Missing " . $_SESSION[$session_variable . "IDColumnName"]) {
                            $query = $query . " AND LENGTH(tuser.IdNo) < 1";
                        } else {
                            if ($lstMissingData == "Missing " . $_SESSION[$session_variable . "PhoneColumnName"]) {
                                $query = $query . " AND LENGTH(tuser.Phone) < 1";
                            }
                        }
                    }
                }
            }
        }
    } else {
        if ($lstShift != "") {
            $query = $query . " AND tgroup.id = " . $lstShift;
        }
        $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
    }
    if ($txtOT1 != "") {
        $query = $query . " AND tuser.OT1 LIKE '" . $txtOT1 . "%'";
    }
    if ($txtOT2 != "") {
        $query = $query . " AND tuser.OT2 LIKE '" . $txtOT2 . "%'";
    }
    if ($lstStatus != "") {
        $query = $query . " AND tuser.UserStatus = " . $lstStatus;
    }
    if ($txtStartDateFrom != "") {
        $query = $query . " AND SUBSTRING(tuser.datelimit, 2, 8) >= '" . insertDate($txtStartDateFrom) . "'";
    }
    if ($txtStartDateTo != "") {
        $query = $query . " AND SUBSTRING(tuser.datelimit, 2, 8) <= '" . insertDate($txtStartDateTo) . "'";
    }
    if ($txtEndDateFrom != "") {
        $query = $query . " AND SUBSTRING(tuser.datelimit, 10, 8) >= '" . insertDate($txtEndDateFrom) . "'";
    }
    if ($txtEndDateTo != "") {
        $query = $query . " AND SUBSTRING(tuser.datelimit, 10, 8) <= '" . insertDate($txtEndDateTo) . "'";
    }
    if ($lstContractAccess != "") {
        $query = $query . " AND tuser.CAGID = " . $lstContractAccess;
    }
    $query .= employeeStatusQuery($lstEmployeeStatus);
    if ($lstFingerRegistered == "Yes") {
        $query = $query . " AND OCTET_LENGTH(fpdata) IS NOT NULL AND OCTET_LENGTH(fpdata) > 32 ";
    } else {
        if ($lstFingerRegistered == "No") {
            $query = $query . " AND (OCTET_LENGTH(fpdata) IS NULL OR OCTET_LENGTH(fpdata) < 32) ";
        }
    }
    if ($lstCardRegistered == "Yes") {
        $query = $query . " AND LENGTH(cardnum) > 1 ";
    } else {
        if ($lstCardRegistered == "No") {
            $query = $query . " AND (LENGTH(cardnum) < 2 OR cardnum is NULL OR cardnum = 'NULL') ";
        }
    }
    $query = $query . " ORDER BY " . $lstSort;
    if ($csv != "yes") {
        if ($prints == "yes") {
            print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%'><tr>";
        } else {
            print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800'> <tr><td><font face='Verdana' size='2'><input type='checkbox' name='chkAll' onClick='javascript:checkAll()'></font></td>";
        }
    }
    if ($csv != "yes") {
        print "<td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "DivColumnName"] . "</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "RemarkColumnName"] . "</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "PhoneColumnName"] . "</font></td> ";
        for ($i = 0; $i < 10; $i++) {
            if ($_SESSION[$session_variable . "F" . ($i + 1)] != "") {
                print "<td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "F" . ($i + 1)] . "</font></td>";
            }
        }
        print "<td><font face='Verdana' size='2'>Current Shift</font></td> <td><font face='Verdana' size='2'>OT1</font></td> <td><font face='Verdana' size='2'>OT2</font></td> <td><font face='Verdana' size='2'>Old ID</font></td> <td><font face='Verdana' size='2'>Access Group</font></td> <td><font face='Verdana' size='2'>Start Date</font></td> <td><font face='Verdana' size='2'>End Date</font></td> <td><font face='Verdana' size='2'>Status</font></td> <td><font face='Verdana' size='2'>Level</font></td>";
        print "</tr>";
    } else {
        print "ID;Name;" . $_SESSION[$session_variable . "IDColumnName"] . ";Dept;" . $_SESSION[$session_variable . "DivColumnName"] . ";" . $_SESSION[$session_variable . "RemarkColumnName"] . ";" . $_SESSION[$session_variable . "PhoneColumnName"] . ";";
        for ($i = 0; $i < 10; $i++) {
            if ($_SESSION[$session_variable . "F" . ($i + 1)] != "") {
                print $_SESSION[$session_variable . "F" . ($i + 1)] . ";";
            }
        }
        print "Current Shift;OT1;OT2;Old ID;Access Group;Start Date;End Date;Status;Level";
        print "\n";
    }
    $result = mysqli_query($conn, $query);
    $count = 0;
    while ($cur = mysqli_fetch_row($result)) {
        if ($cur[3] == "") {
            $cur[3] = "";
        }
        if ($cur[5] == "") {
            $cur[5] = "";
        }
        if ($cur[6] == "") {
            $cur[6] = "";
        }
        if ($csv != "yes") {
            print "<tr>";
            if ($prints != "yes" && strpos($userlevel, $current_module . "A") !== false) {
                print "<td bgcolor='" . $bgcolor . "'><input type='hidden' name='txhID" . $count . "' value='" . $cur[0] . "'><font face='Verdana' size='2'><input type='checkbox' name='chk" . $count . "' id='chk" . $count . "'></td>";
                addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
                print "<td><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'><a title='ID' name='" . $cur[0] . "' href='#" . $cur[0] . "' onClick='javascript:openWindow(" . $cur[0] * 419 . ")'><font face='Verdana' color='#000000' size='1'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td>";
            } else {
                addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
                print "<td><a title='ID'><font face='Verdana' color='#000000' size='1'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td>";
            }
            print "<td><a title='Name'><font face='Verdana' size='1'>" . $cur[1] . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $cur[5] . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $cur[2] . "</font></a></td> <td><font face='Verdana' size='1'>" . $cur[3] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[6] . "</font></td> <td><a title='" . $_SESSION[$session_variable . "PhoneColumnName"] . "'><font face='Verdana' size='1'>" . $cur[7] . "</font></td> ";
            for ($i = 0; $i < 10; $i++) {
                if ($_SESSION[$session_variable . "F" . ($i + 1)] != "") {
                    print "<td><font face='Verdana' size='1'>" . $cur[$i + 15] . "</font></td>";
                }
            }
            print "<td><a title='Current Shift'><font face='Verdana' size='1'>" . $cur[4] . "</font></a></td> <td><a title='OT1'><font face='Verdana' size='1'>" . $cur[8] . "</font></a></td> <td><a title='OT2'><font face='Verdana' size='1'>" . $cur[9] . "</font></a></td> <td><a title='Old ID'><font face='Verdana' size='1'>" . $cur[10] . "</font></a></td> <td><a title='Access Group'><font face='Verdana' size='1'>" . $cur[25] . "</font></a></td> <td><a title='Start Date'><font face='Verdana' size='1'>";
            if (substr($cur[11], 1, 8) == "19770430") {
                displayDate(substr($cur[13], 1, 8));
                print displayDate(substr($cur[13], 1, 8));
            } else {
                displayDate(substr($cur[11], 1, 8));
                print displayDate(substr($cur[11], 1, 8));
            }
            print "</font></a></td> <td><a title='End Date'><font face='Verdana' size='1'>";
            if (substr($cur[11], 9, 8) == "19770430") {
                displayDate(substr($cur[13], 9, 8));
                print displayDate(substr($cur[13], 9, 8));
            } else {
                displayDate(substr($cur[11], 9, 8));
                print displayDate(substr($cur[11], 9, 8));
            }
            print "</font></a></td> <td><a title='Status'><font face='Verdana' size='1'>" . $cur[12] . "</font></a></td> <td><a title='Level'><font face='Verdana' size='1'>" . $cur[14] . "</font></a></td> </tr>";
            $count++;
        } else {
            addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
            print addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
            print ";" . $cur[1] . ";" . $cur[5] . ";" . $cur[2] . ";" . $cur[3] . ";" . $cur[6] . ";" . $cur[7] . ";";
            for ($i = 0; $i < 10; $i++) {
                if ($_SESSION[$session_variable . "F" . ($i + 1)] != "") {
                    print $cur[$i + 15] . ";";
                }
            }
            print $cur[4] . ";" . $cur[8] . ";" . $cur[9] . ";" . $cur[10] . ";" . $cur[25] . ";";
            if (substr($cur[11], 1, 8) == "19770430") {
                displayDate(substr($cur[13], 1, 8));
                print displayDate(substr($cur[13], 1, 8));
            } else {
                displayDate(substr($cur[11], 1, 8));
                print displayDate(substr($cur[11], 1, 8));
            }
            print ";";
            if (substr($cur[11], 9, 8) == "19770430") {
                displayDate(substr($cur[13], 9, 8));
                print displayDate(substr($cur[13], 9, 8));
            } else {
                displayDate(substr($cur[11], 9, 8));
                print displayDate(substr($cur[11], 9, 8));
            }
            print ";" . $cur[12] . ";" . $cur[14] . "\n";
            $count++;
        }
    }
    if ($excel != "yes") {
        print "<input type='hidden' name='txtCount' value='" . $count . "'></table>";
        print "<p align='center'><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $count . "</b></font>";
    }
    if ($prints != "yes" && $excel != "yes") {
        if (strpos($userlevel, $current_module . "E") !== false) {
            print "<table>";
            print "<tr>";
            $query = "SELECT DISTINCT(Dept), Dept from tuser ORDER BY Dept";
            displayList("lstSelectedDepartment", "Change the <b>Department</b> of the selected Record(s) to: ", $lstSelectedDepartment, $prints, $conn, $query, "", "50%", "50%");
            print "</tr>";
            print "<tr>";
            $query = "SELECT DISTINCT(company), company from tuser ORDER BY company";
            displayList("lstSelectedDivision", "Change the <b>" . $_SESSION[$session_variable . "DivColumnName"] . "</b> of the selected Record(s) to: ", $lstSelectedDivision, $prints, $conn, $query, "", "50%", "50%");
            print "</tr>";
            print "<tr>";
            $query = "SELECT DISTINCT(idno), idno from tuser ORDER BY idno";
            displayList("txtSelectedSNo", "Change the <b>" . $_SESSION[$session_variable . "IDColumnName"] . "</b> of the selected Record(s) to: ", $txtSelectedSNo, $prints, $conn, $query, "", "50%", "50%");
            print "</tr>";
            print "<tr>";
            $query = "SELECT DISTINCT(Remark), Remark from tuser ORDER BY Remark";
            displayList("txtSelectedRemark", "Change the <b>" . $_SESSION[$session_variable . "RemarkColumnName"] . "</b> of the selected Record(s) to: ", $txtSelectedRemark, $prints, $conn, $query, "", "50%", "50%");
            print "</tr>";
            print "<tr>";
            $query = "SELECT DISTINCT(Phone), Phone from tuser ORDER BY Phone";
            displayList("txtSelectedPhone", "Change the <b>" . $_SESSION[$session_variable . "PhoneColumnName"] . "</b> of the selected Record(s) to: ", $txtSelectedPhone, $prints, $conn, $query, "", "50%", "50%");
            print "</tr>";
            for ($i = 0; $i < 10; $i++) {
                if ($_SESSION[$session_variable . "F" . ($i + 1)] != "") {
                    print "<tr>";
                    $query = "SELECT DISTINCT(F" . ($i + 1) . "), F" . ($i + 1) . " from tuser ORDER BY F" . ($i + 1);
                    displayList("lstSelectedF" . ($i + 1), "Change the <b>" . $_SESSION[$session_variable . "F" . ($i + 1)] . "</b> of the selected Record(s) to: ", $_POST["lstSelectedF" . ($i + 1)], $prints, $conn, $query, "", "50%", "50%");
                    print "</tr>";
                } else {
                    print "<input type='hidden' name='lstSelectedF" . ($i + 1) . "'>";
                }
            }
            print "<tr>";
            print "<td align='right' width='50%'><font face='Verdana' size='2'>Change the <b>Level</b> of the selected Record(s) to:</font></td><td width='50%'><select name='lstSelectedLevel' class='form-control'> <option selected value='" . $lstStatus . "'>" . $lstStatus . "</option>";
            $lstUserStatus = $_SESSION[$session_variable . "userstatus"];
            for ($i = 1; $i < 11; $i++) {
                if ($lstUserStatus < $i) {
                    print "<option value='" . $i . "'>" . $i . "</option>";
                }
            }
            print "</select></td>";
            print "</tr>";
            print "<tr>";
            $query = "SELECT CAGID, Name from CAG ORDER BY Name";
            displayList("lstSelectContractAccess", "Change the <b>Contract Access Group</b> of the selected Record(s) to: ", $lstContractAccess, $prints, $conn, $query, "", "50%", "50%");
            print "</tr>";
            print "<tr><td>&nbsp;</td><td><input name='btSubmit' type='button' value='Save Changes' onClick='javascript:saveChanges()'></td></tr>";
            print "<tr><td>&nbsp;</td><td>&nbsp;</td></tr>";
            print "<tr>";
            displayEmployeeStatus($conn, "lstSelectedStatus", $lstSelectedStatus, "50%", "50%");
            print "</tr>";
            print "<tr>";
            displayTextbox("txtSelectedEndDate", "End Date for Employees to be De-Activated <font size='1'><font size='1'>(DD/MM/YYYY)</font></font>: ", $txtSelectedEndDate, $prints, 15, "50%", "50%");
            print "</tr>";
            print "<tr>";
            if (strpos($userlevel, $current_module . "D") !== false) {
                print "<td align='right'><input type='button' value='De-Activate Selected Record(s)' onClick='javascript:deActivate()'></td>";
            } else {
                print "<td>&nbsp;</td>";
            }
            if (strpos($userlevel, $current_module . "D") !== false) {
                print "<td><input type='button' value='Activate Selected Record(s)' onClick='javascript:activate()'></td>";
            } else {
                print "<td>&nbsp;</td>";
            }
            print "</tr>";
            if (strpos($userlevel, $current_module . "D") !== false && strpos($userlevel, "11D") !== false) {
                print "<tr>";
                print "<td align='right'><input type='button' value='Emigrate Selected Record(s)' onClick='javascript:emigrate()'></td>";
                print "<td><input type='button' value='Migrate Selected Record(s)' onClick='javascript:migrate()'></td>";
                print "</tr>";
            }
            print "</table>";
        }
        print "<p align='center'><input type='button' value='Print Report' onClick='checkPrint(0)'>&nbsp;&nbsp;<input type='button' value='Excel' onClick='checkPrint(1)'>&nbsp;&nbsp;<input type='button' value='CSV' onClick='checkPrint(3)'>";
        print "</p>";
    }
}
if ($csv != "yes") {
    print "</form>";
    echo "<script>\r\nfunction saveChanges(){\t\r\n\tx = document.frm1;\r\n\tif (x.lstSelectedDepartment.value == '' && x.lstSelectedDivision.value == '' && x.txtSelectedSNo.value == '' && x.txtSelectedRemark.value == '' && x.txtSelectedPhone.value == '' && x.lstSelectedLevel.value == '' && x.lstSelectedF1.value == '' && x.lstSelectedF2.value == '' && x.lstSelectedF3.value == '' && x.lstSelectedF4.value == '' && x.lstSelectedF5.value == '' && x.lstSelectedF6.value == '' && x.lstSelectedF7.value == '' && x.lstSelectedF8.value == '' && x.lstSelectedF9.value == '' && x.lstSelectedF10.value == '' && x.lstSelectContractAccess.value == ''){\r\n\t\talert('Please select/ enter at least one Data to update the Employees');\r\n\t\tx.lstSelectedDepartment.focus();\r\n\t}else{\r\n\t\tif (confirm('Save Changes')){\r\n\t\t\tx.act.value = 'saveChanges';\r\n\t\t\tx.btSubmit.disabled = true;\r\n\t\t\tx.submit();\r\n\t\t}\r\n\t}\t\r\n}\r\n\r\nfunction deActivate(){\t\r\n\tx = document.frm1;\t\r\n\tif (x.lstSelectedStatus.value == '' || x.lstSelectedStatus.value == 'ACT' || x.lstSelectedStatus.value == 'PSV' || x.lstSelectedStatus.value == 'ADA' || x.lstSelectedStatus.value == 'FDA'){\r\n\t\talert('Please select a Valid Employee Status: \\rPRM - Promoted \\rRSN - Resigned \\rRTD - Retired \\rTRM - Terminated');\r\n\t\tx.lstSelectedStatus.focus();\r\n\t}else if (check_valid_date(x.txtSelectedEndDate.value) == false){\r\n\t\talert('Invalid End Date Format');\r\n\t\tx.txtSelectedEndDate.focus();\r\n\t}else{\r\n\t\tif (confirm('De-Activate Selected Employee(s)')){\r\n\t\t\tx.act.value = 'deActivate';\r\n\t\t\tx.submit();\r\n\t\t}\r\n\t}\r\n}\r\n\r\nfunction activate(){\t\r\n\tx = document.frm1;\t\r\n\tif (confirm('Activate Selected Employee(s)')){\r\n\t\tx.act.value = 'activate';\r\n\t\tx.submit();\r\n\t}\r\n}\r\n\r\nfunction migrate(){\t\r\n\tx = document.frm1;\t\r\n\tif (confirm('Migrate Selected Employee(s)')){\r\n\t\tx.act.value = 'migrate';\r\n\t\tx.submit();\r\n\t}\r\n}\r\n\r\nfunction emigrate(){\t\r\n\tx = document.frm1;\t\r\n\tif (confirm('Emigrate Selected Employee(s)')){\r\n\t\tx.act.value = 'emigrate';\r\n\t\tx.submit();\r\n\t}\r\n}\r\n\r\nfunction checkAll(){\t\r\n\tx = document.frm1;\r\n\ty = x.chkAll;\r\n\tz = x.txtCount.value;\t\r\n\tfor (i=0;i<z;i++){\t\t\r\n\t\tif (y.checked == true){\t\t\t\r\n\t\t\tdocument.getElementById(\"chk\"+i).checked = true;\t\t\t\r\n\t\t}else{\r\n\t\t\tdocument.getElementById(\"chk\"+i).checked = false;\r\n\t\t}\r\n\t}\r\n}\r\n\r\nfunction openWindow(a){\r\n\twindow.open(\"EmployeeChild.php?act=viewRecord&txtID=\"+a, \"\",\"height=400,width=600\");\r\n}\r\n\r\nfunction changeID(){\r\n\twindow.open(\"EmployeeChild.php?act=viewchangeIDRecord\", \"\",\"width=600,height=400\");\r\n}\r\n\r\n</script>\r\n</center></body></html>\r\n\r\n";
}

?>