<?php


error_reporting(E_ALL);
ob_start("ob_gzhandler");
date_default_timezone_set("Africa/Algiers");
include "Functions.php";
set_time_limit(0);
$conn = openConnection();
$iconn = openIConnection();
$query = "SELECT LockDate, MACAddress, DBType, DBIP, DBName, DBUser, DBPass FROM OtherSettingMaster";
$main_result = selectData($conn, $query);
$txtLockDate = $main_result[0];
$txtMACAddress = $main_result[1];
$lstDBType = $main_result[2];
$txtDBIP = $main_result[3];
$txtDBName = $main_result[4];
$txtDBUser = $main_result[5];
$txtDBPass = $main_result[6];
$oconn = "";
$oconn = oracle_connection($txtDBIP, $txtDBName, $txtDBUser, $txtDBPass);
print "Connected to Oracle Database: " . $oconn;
$flag_division = true;
if (getRegister($txtMACAddress, 7) == "63" || getRegister($txtMACAddress, 7) == "4" || getRegister($txtMACAddress, 7) == "14" || getRegister($txtMACAddress, 7) == "18" || getRegister($txtMACAddress, 7) == "19" || getRegister($txtMACAddress, 7) == "115" || getRegister($txtMACAddress, 7) == "49" || getRegister($txtMACAddress, 7) == "89" || getRegister($txtMACAddress, 7) == "109" || getRegister($txtMACAddress, 7) == "21" || getRegister($txtMACAddress, 7) == "151") {
    $flag_division = false;
}
$payid_flag = false;
if (getRegister($txtMACAddress, 7) == "63" || getRegister($txtMACAddress, 7) == "89" || getRegister($txtMACAddress, 7) == "18" || getRegister($txtMACAddress, 7) == "49" || getRegister($txtMACAddress, 7) == "151") {
    $payid_flag = true;
}
if (checkMAC($conn) == true && $oconn != "") {
    if (getRegister($txtMACAddress, 7) == "63" || getRegister($txtMACAddress, 7) == "89") {
        $query = "SELECT EMP_CODE, EMP_END_OF_SERVICE_DT FROM ZEOS_BIO WHERE UPDATED_Y_N = 'N'";
        $result = oci_parse($oconn, $query);
        oci_execute($result);
        while ($cur = oci_fetch_array($result, OCI_BOTH)) {
            $query_ = "UPDATE tuser SET PassiveType = 'RSN', PassiveRemark = 'OIMigrate', datelimit = CONCAT('Y', SUBSTRING(datelimit, 2, 8), " . insertDate($cur[1]) . ") WHERE ID = '" . $cur[0] . "'";
            if (updateIData($iconn, $query_, true)) {
                $_query = "UPDATE ZEOS_BIO SET UPDATED_Y_N = 'Y' WHERE EMP_CODE = '" . $cur[0] . "'";
                $_res = ociParse($oconn, $_query);
                ociExecute($_res);
            }
        }
        ociCommit($oconn);
    }
    if ($flag_division == true && getRegister($txtMACAddress, 7) != "4") {
        $query = "DELETE FROM tuser";
        $res = ociParse($oconn, $query);
        ociExecute($res);
        ociCommit($oconn);
        $query = "SELECT id, company FROM tuser";
        $result = mysqli_query($conn, $query);
        while ($cur = mysqli_fetch_row($result)) {
            $query = "INSERT INTO tuser (id, company) VALUES (" . $cur[0] . ", '" . $cur[1] . "')";
            $res = ociParse($oconn, $query);
            if (!ociExecute($res)) {
                $err = oci_error();
                echo "\n\r" . $err;
                exit;
            }
        }
        ociCommit($oconn);
    }
    $query = "SELECT MAX(AttendanceMaster.ADate) FROM AttendanceMaster ";
    $last_result = oci_parse($oconn, $query);
    oci_execute($last_result);
    $last_cur = oci_fetch_array($last_result, OCI_BOTH);
    if ($last_cur[0] <= 0) {
        $last_cur[0] = 20190101;
    }
    echo "\n\rLast Attendance Date: " . displayDate($last_cur[0]);
    $super_query = "";
    $last_cur[0] = "";
    $super_query = "SELECT Val, DateFrom, DateTo, Col FROM MigrateMaster WHERE LENGTH(DateFrom) = 8 AND LENGTH(DateTo) = 8 ORDER BY Val ";
    $super_result = mysqli_query($conn, $super_query);
    while ($super_cur = mysqli_fetch_row($super_result)) {
        $txtLockDate = $super_cur[2];
        $last_cur[0] = $super_cur[1];
        echo ", Last Record Date: " . $super_cur[0] . " - " . displayDate($last_cur[0]) . " - " . displayDate($txtLockDate);
        if ($flag_division) {
            $query = "SELECT AttendanceMaster.AttendanceID, AttendanceMaster.EmployeeID, AttendanceMaster.EmpID, AttendanceMaster.group_id, AttendanceMaster.group_min, AttendanceMaster.ADate, AttendanceMaster.Week, AttendanceMaster.EarlyIn, AttendanceMaster.LateIn, AttendanceMaster.Break, AttendanceMaster.LessBreak, AttendanceMaster.MoreBreak, AttendanceMaster.EarlyOut, AttendanceMaster.LateOut, AttendanceMaster.Normal, AttendanceMaster.Grace, AttendanceMaster.Overtime, AttendanceMaster.AOvertime, AttendanceMaster.Day, AttendanceMaster.Flag, AttendanceMaster.p_flag, tgroup.name, AttendanceMaster.OT1, AttendanceMaster.OT2, tuser.idno, tuser.phone FROM AttendanceMaster, tgroup, tuser WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.EmployeeID = tuser.id AND AttendanceMaster.ADate <= " . $txtLockDate . " AND ADate >= " . $last_cur[0] . " AND AttendanceMaster.EmployeeID IN (SELECT id FROM tuser where " . $super_cur[3] . " = '" . $super_cur[0] . "') AND tuser.phone <> 'Contract' ";
        } else {
            if (getRegister($txtMACAddress, 7) != "4") {
                $query = "SELECT AttendanceMaster.AttendanceID, AttendanceMaster.EmployeeID, AttendanceMaster.EmpID, AttendanceMaster.group_id, AttendanceMaster.group_min, AttendanceMaster.ADate, AttendanceMaster.Week, AttendanceMaster.EarlyIn, AttendanceMaster.LateIn, AttendanceMaster.Break, AttendanceMaster.LessBreak, AttendanceMaster.MoreBreak, AttendanceMaster.EarlyOut, AttendanceMaster.LateOut, AttendanceMaster.Normal, AttendanceMaster.Grace, AttendanceMaster.Overtime, AttendanceMaster.AOvertime, AttendanceMaster.Day, AttendanceMaster.Flag, AttendanceMaster.p_flag, tgroup.name, AttendanceMaster.OT1, AttendanceMaster.OT2, tuser.idno, tuser.phone FROM AttendanceMaster, tgroup, tuser WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.EmployeeID = tuser.id AND AttendanceMaster.ADate <= " . $txtLockDate . " AND ADate >= " . $last_cur[0] . " AND AttendanceMaster.EmployeeID IN (SELECT id FROM tuser where " . $super_cur[3] . " = '" . $super_cur[0] . "') ";
            } else {
                $query = "SELECT AttendanceMaster.AttendanceID, AttendanceMaster.EmployeeID, AttendanceMaster.EmpID, AttendanceMaster.group_id, AttendanceMaster.group_min, AttendanceMaster.ADate, AttendanceMaster.Week, AttendanceMaster.EarlyIn, AttendanceMaster.LateIn, AttendanceMaster.Break, AttendanceMaster.LessBreak, AttendanceMaster.MoreBreak, AttendanceMaster.EarlyOut, AttendanceMaster.LateOut, AttendanceMaster.Normal, AttendanceMaster.Grace, AttendanceMaster.Overtime, AttendanceMaster.AOvertime, AttendanceMaster.Day, AttendanceMaster.Flag, AttendanceMaster.p_flag, tgroup.name, AttendanceMaster.OT1, AttendanceMaster.OT2, tuser.idno, tuser.phone FROM AttendanceMaster, tgroup, tuser WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.EmployeeID = tuser.id AND AttendanceMaster.ADate <= " . $txtLockDate . " AND ADate >= " . $last_cur[0] . " AND AttendanceMaster.EmployeeID IN (SELECT id FROM tuser where " . $super_cur[3] . " = '" . $super_cur[0] . "') ";
            }
        }
        $result = mysqli_query($conn, $query);
        while ($cur = mysqli_fetch_row($result)) {
            $query = "DELETE FROM AttendanceMaster WHERE AttendanceID = " . $cur[0];
            $res = ociParse($oconn, $query);
            if (!ociExecute($res)) {
                $err = oci_error();
                echo "\n\r Error 209: " . $err;
            }
            $emp_id = "";
            $query = "INSERT INTO AttendanceMaster (AttendanceID, EmployeeID, EmpID, group_id, group_min, ADate, Week, EarlyIn, LateIn, Break, LessBreak, MoreBreak, EarlyOut, LateOut, Normall, Grace, Overtime, AOvertime, Dayy, Flag, p_flag, Shift_Name, OT1, OT2, ODate, ECode, Amounted_Y_N, Meal_Allw_Flag, PHF) VALUES ( '" . $cur[0] . "', ";
            for ($i = 1; $i <= 23; $i++) {
                $query .= "'" . $cur[$i] . "', ";
            }
            if ($payid_flag) {
                $query .= " To_Date('" . displayDate($cur[5]) . "', 'DD/MM/YYYY'), '" . $cur[25] . "', 'N', 'N', '0')";
            } else {
                $query .= " To_Date('" . displayDate($cur[5]) . "', 'DD/MM/YYYY'), '" . getIndomieEmployeeCode(encryptDecrypt($txtMACAddress), $cur[1], $cur[24]) . "', 'N', 'N', '0')";
            }
            $res = ociParse($oconn, $query);
            if (!ociExecute($res)) {
                $err = oci_error();
                echo "\n\r Error 229: " . $err;
                exit;
            }
        }
        if (getRegister($txtMACAddress, 7) != "63" && getRegister($txtMACAddress, 7) != "89" && getRegister($txtMACAddress, 7) != "4") {
            $query = "SELECT tuser.id, tuser.idno, phone from tuser WHERE tuser." . $super_cur[3] . " = '" . $super_cur[0] . "' AND (tuser.PassiveType LIKE 'ACT' OR tuser.PassiveType LIKE 'ADA' OR tuser.PassiveType LIKE 'FDA') AND tuser.phone <> 'Contract' ";
        } else {
            $query = "SELECT tuser.id, tuser.idno, phone from tuser WHERE (tuser.PassiveType LIKE 'ACT' OR tuser.PassiveType LIKE 'ADA' OR tuser.PassiveType LIKE 'FDA') ";
        }
        $result = mysqli_query($conn, $query);
        while ($cur = mysqli_fetch_row($result)) {
            if (getRegister($txtMACAddress, 7) == "63" || getRegister($txtMACAddress, 7) == "89" || getRegister($txtMACAddress, 7) == "115") {
                $query = "DELETE FROM Absenteeism WHERE EMP_ID = '" . $cur[0] . "' AND MON = '" . substr($txtLockDate, 4, 2) . "-" . substr($txtLockDate, 0, 4) . "' ";
            } else {
                if (getRegister($txtMACAddress, 7) == "21" || getRegister($txtMACAddress, 7) == "109") {
                    $query = "DELETE FROM Absenteeism WHERE EMP_ID = '" . getIndomieEmployeeCode(encryptDecrypt($txtMACAddress), $cur[0], $cur[1]) . "' AND FROM_DT = To_Date('" . displayDate($last_cur[0]) . "', 'DD/MM/YYYY') AND TO_DT = To_Date('" . displayDate($txtLockDate) . "', 'DD/MM/YYYY')";
                } else {
                    if ($payid_flag == true) {
                        $query = "DELETE FROM Absenteeism WHERE EMP_ID = '" . $cur[2] . "' AND FROM_DT = To_Date('" . displayDate($last_cur[0]) . "', 'DD/MM/YYYY') AND TO_DT = To_Date('" . displayDate($txtLockDate) . "', 'DD/MM/YYYY')";
                    } else {
                        if (getRegister($txtMACAddress, 7) == "4") {
                            $query = "DELETE FROM Absenteeism WHERE EMP_ID = '" . getIndomieEmployeeCode(encryptDecrypt($txtMACAddress), $cur[0], $cur[1]) . "' AND FROM_DT = To_Date('" . displayDate($last_cur[0]) . "', 'DD/MM/YYYY') AND TO_DT = To_Date('" . displayDate($txtLockDate) . "', 'DD/MM/YYYY')";
                        } else {
                            $query = "DELETE FROM Absenteeism WHERE EMP_ID = '" . getIndomieEmployeeCode(encryptDecrypt($txtMACAddress), $cur[0], $cur[1]) . "' AND FROM_DT = To_Date('" . displayDate(getNextDay($last_cur[0], 1)) . "', 'DD/MM/YYYY') AND TO_DT = To_Date('" . displayDate($txtLockDate) . "', 'DD/MM/YYYY')";
                        }
                    }
                }
            }
            $res = ociParse($oconn, $query);
            if (!ociExecute($res)) {
                $err = oci_error();
                echo "\n\r Error 280: " . $err;
            }
            $absent_days = 0;
            if (getRegister($txtMACAddress, 7) == "63" || getRegister($txtMACAddress, 7) == "89") {
                if ($flag_division) {
                    $absent_days = getAS($conn, $cur[0], displayDate($last_cur[0]), displayDate($txtLockDate));
                } else {
                    $absent_days = getAS($conn, $cur[0], displayDate(getNextDay($last_cur[0], 1)), displayDate($txtLockDate));
                }
            } else {
                if (getRegister($txtMACAddress, 7) == "21" || getRegister($txtMACAddress, 7) == "109") {
                    if ($flag_division) {
                        $absent_days = getA($conn, $cur[0], displayDate($last_cur[0]), displayDate($txtLockDate));
                    } else {
                        $absent_days = getA($conn, $cur[0], displayDate(getNextDay($last_cur[0], 1)), displayDate($txtLockDate));
                    }
                } else {
                    if ($flag_division) {
                        $absent_days = getASS($conn, $cur[0], displayDate($last_cur[0]), displayDate($txtLockDate));
                    } else {
                        if (getRegister($txtMACAddress, 7) == "4") {
                            $absent_days = getASS($conn, getIndomieEmployeeCode(encryptDecrypt($txtMACAddress), $cur[0], $cur[1]), displayDate($last_cur[0]), displayDate($txtLockDate));
                        } else {
                            if (getRegister($txtMACAddress, 7) == "-1") {
                                $absent_days = getASS($conn, $cur[0], displayDate($last_cur[0]), displayDate($txtLockDate));
                            } else {
                                $absent_days = getASS($conn, $cur[0], displayDate(getNextDay($last_cur[0], 1)), displayDate($txtLockDate));
                            }
                        }
                    }
                }
            }
            if (0 < $absent_days) {
                if (getRegister($txtMACAddress, 7) == "115" || getRegister($txtMACAddress, 7) == "89") {
                    $query = "INSERT INTO Absenteeism (EMP_ID, ABSNT_DAYS, FROM_DT, TO_DT, MON) VALUES ('" . getIndomieEmployeeCode(encryptDecrypt($txtMACAddress), $cur[0], $cur[1]) . "', '" . $absent_days . "', To_Date('" . displayDate($last_cur[0]) . "', 'DD/MM/YYYY'), To_Date('" . displayDate($txtLockDate) . "', 'DD/MM/YYYY'), '" . substr($txtLockDate, 4, 2) . "-" . substr($txtLockDate, 0, 4) . "')";
                } else {
                    if (getRegister($txtMACAddress, 7) == "21" || getRegister($txtMACAddress, 7) == "109") {
                        $a_s = getAS($conn, $cur[0], displayDate($last_cur[0]), displayDate($txtLockDate));
                        $a_ss = getASS($conn, $cur[0], displayDate($last_cur[0]), displayDate($txtLockDate));
                        $p_d = getP($conn, $cur[0], displayDate($last_cur[0]), displayDate($txtLockDate));
                        $query = "INSERT INTO Absenteeism (EMP_ID, ABSNT_DAYS, FROM_DT, TO_DT, present, total_abs_days, abs_excl_sunday) VALUES ('" . getIndomieEmployeeCode(encryptDecrypt($txtMACAddress), $cur[0], $cur[1]) . "', '" . $a_ss . "', To_Date('" . displayDate($last_cur[0]) . "', 'DD/MM/YYYY'), To_Date('" . displayDate($txtLockDate) . "', 'DD/MM/YYYY'), '" . $p_d . "', '" . $absent_days . "', '" . $a_s . "')";
                    } else {
                        if ($payid_flag == true) {
                            $query = "INSERT INTO Absenteeism (EMP_ID, ABSNT_DAYS, FROM_DT, TO_DT) VALUES ('" . $cur[0] . "', '" . $absent_days . "', To_Date('" . displayDate($last_cur[0]) . "', 'DD/MM/YYYY'), To_Date('" . displayDate($txtLockDate) . "', 'DD/MM/YYYY'))";
                        } else {
                            $query = "INSERT INTO Absenteeism (EMP_ID, ABSNT_DAYS, FROM_DT, TO_DT) VALUES ('" . getIndomieEmployeeCode(encryptDecrypt($txtMACAddress), $cur[0], $cur[1]) . "', '" . $absent_days . "', To_Date('" . displayDate(getNextDay($last_cur[0], 1)) . "', 'DD/MM/YYYY'), To_Date('" . displayDate($txtLockDate) . "', 'DD/MM/YYYY'))";
                        }
                    }
                }
                $res = ociParse($oconn, $query);
                if (!ociExecute($res)) {
                    $err = oci_error();
                    echo "\n\r Error 353: " . $query;
                    echo "\n\r Error 354: " . $err;
                    exit;
                }
            }
        }
        if (ociCommit($oconn)) {
            $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('DB Migrate - " . $super_cur[0] . " - " . displayDate($last_cur[0]) . " - " . displayDate($txtLockDate) . "', " . insertToday() . ", '" . getNow() . "')";
            updateData($conn, $query, true);
        }
        $query = "SELECT OTDate FROM OTDate ORDER BY OTDate";
        $result = mysqli_query($conn, $query);
        while ($cur = mysqli_fetch_row($result)) {
            $query = "UPDATE AttendanceMaster SET PHF = '1' WHERE ADate = '" . $cur[0] . "' AND PHF = '0'";
            $res = ociParse($oconn, $query);
            ociExecute($res);
        }
        ociCommit($oconn);
    }
} else {
    print "Process Terminated: Un Registered Application OR Connection to Oracle Database NOT found: " . $oconn;
}
function getIndomieEmployeeCode($txtMACAddress, $id, $staffType)
{
    $emp_id = "";
    $staffType = trim(strtoupper($staffType));
    $txtMACAddress = encryptDecrypt($txtMACAddress);
    if (getRegister($txtMACAddress, 7) == "115") {
        $e_prefix = "";
        switch (substr($id, 0, 2)) {
            case "10":
                $e_prefix = "A";
                break;
            case "11":
                $e_prefix = "B";
                break;
            case "12":
                $e_prefix = "C";
                break;
            case "13":
                $e_prefix = "D";
                break;
            case "14":
                $e_prefix = "E";
                break;
            case "15":
                $e_prefix = "F";
                break;
            case "16":
                $e_prefix = "G";
                break;
            case "17":
                $e_prefix = "H";
                break;
            case "18":
                $e_prefix = "I";
                break;
            case "19":
                $e_prefix = "J";
                break;
            case "20":
                $e_prefix = "K";
                break;
            case "21":
                $e_prefix = "L";
                break;
            case "22":
                $e_prefix = "M";
                break;
            case "23":
                $e_prefix = "N";
                break;
            case "24":
                $e_prefix = "O";
                break;
            case "25":
                $e_prefix = "P";
                break;
            case "26":
                $e_prefix = "Q";
                break;
            case "27":
                $e_prefix = "R";
                break;
            case "28":
                $e_prefix = "S";
                break;
            case "29":
                $e_prefix = "T";
                break;
            case "30":
                $e_prefix = "U";
                break;
            case "31":
                $e_prefix = "V";
                break;
            case "32":
                $e_prefix = "W";
                break;
            case "33":
                $e_prefix = "X";
                break;
            case "34":
                $e_prefix = "Y";
                break;
            case "35":
                $e_prefix = "Z";
                break;
        }
        $emp_id = $e_prefix . "" . substr($id, 2, 4);
    } else {
        if (getRegister($txtMACAddress, 7) == "63" || getRegister($txtMACAddress, 7) == "89") {
            $emp_id = $id;
        } else {
            if ($staffType == "CON" || $staffType == "APPRENTICE" || $staffType == "CONT" || $staffType == "IT" || $staffType == "NYSC") {
                $emp_id = $id;
            } else {
                if (getRegister($txtMACAddress, 7) == "109") {
                    $emp_id = "S" . addZero($id, 5);
                } else {
                    $emp_id = "E" . addZero($id, 5);
                }
            }
        }
    }
    return $emp_id;
}

?>