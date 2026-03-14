<?php


ob_start("ob_gzhandler");
error_reporting(E_ALL);
date_default_timezone_set("Africa/Algiers");
set_time_limit(0);
include "Functions.php";
$conn = openConnection();
$iconn = openIConnection();
$jconn = openIConnection();
$kconn = openIConnection();
if (checkMAC($conn) == true) {
    $query = "SELECT LockDate, MACAddress, DBType, DBIP, DBName, DBUser, DBPass, EmployeeCodeLength FROM OtherSettingMaster";
    $main_result = selectData($conn, $query);
    $txtLockDate = $main_result[0];
    $txtMACAddress = $main_result[1];
    $lstDBType = $main_result[2];
    $txtDBIP = $main_result[3];
    $txtDBName = $main_result[4];
    $txtDBUser = $main_result[5];
    $txtDBPass = $main_result[6];
    $txtECodeLength = $main_result[7];
    $oconn = oracle_connection($txtDBIP, $txtDBName, $txtDBUser, $txtDBPass);
    if ($oconn != "") {
        echo "\n\rConnected to Oracle: " . $oconn;
        $oquery = "DELETE FROM ATTEN";
        $res = ociParse($oconn, $oquery);
        if (ociExecute($res)) {
            $oquery = "DELETE FROM TEMP_ATTEN_CASUAL";
            $res = ociParse($oconn, $oquery);
            if (ociExecute($res)) {
                $oquery = "DELETE FROM FEEDING_ALLOWANCE";
                $res = ociParse($oconn, $oquery);
                if (ociExecute($res)) {
                    ociCommit($oconn);
                }
            }
        }
        $oquery = "";
        $migrate_query = "SELECT Col, Val, DateFrom, DateTo, MonthNo FROM MigrateMaster WHERE LENGTH(DateFrom) = 8 AND LENGTH(DateTo) = 8 ORDER BY Col, Val ";
        $counter = 0;
        $migrate_result = mysqli_query($conn, $migrate_query);
        while ($migrate_cur = mysqli_fetch_row($migrate_result)) {
            if ($migrate_cur[2] != "" && $migrate_cur[3] != "") {
                $start = $migrate_cur[2];
                $end = $migrate_cur[3];
                $query = "";
                if (getRegister($txtMACAddress, 7) == "177") {
                    $query = "SELECT id, remark FROM tuser WHERE " . $migrate_cur[0] . " = '" . $migrate_cur[1] . "' AND PassiveType = 'ACT' ";
                } else {
                    $query = "SELECT id FROM tuser WHERE " . $migrate_cur[0] . " = '" . $migrate_cur[1] . "' AND PassiveType = 'ACT' ";
                }
                $result = mysqli_query($iconn, $query);
                while ($cur = mysqli_fetch_row($result)) {
                    $counter++;
                    $now = $start;
                    $a1 = 0;
                    $a2 = 0;
                    $a3 = 0;
                    $a4 = 0;
                    $b1 = 0;
                    $b2 = 0;
                    while ($now <= $end) {
                        $sub_query = "SELECT AOvertime, Day, Flag, OT1, OT2, NightFlag FROM AttendanceMaster WHERE EmployeeID = '" . $cur[0] . "' AND ADate = '" . $now . "'";
                        $sub_result = selectData($jconn, $sub_query);
                        if ($sub_result == "" || $sub_result[0] == "") {
                            $nu_absent = 0;
                            if (getDay(displayDate($now)) == "Saturday" || getDay(displayDate($now)) == "Sunday") {
                                if (getRegister($txtMACAddress, 7) == "177") {
                                    $nu_absent = "A";
                                } else {
                                    $nu_absent = 8;
                                }
                            } else {
                                if (getRegister($txtMACAddress, 7) == "177") {
                                    $nu_absent = "P";
                                }
                            }
                            if (getRegister($txtMACAddress, 7) == "177") {
                                $oquery = "INSERT INTO ATTEN (VC_EMP_ID, DT_ATTEN, NU_ANH_OT, NU_SUN_OT, NU_PH_OT, NU_ABSENT, NU_SAT_OT) VALUES ('" . $cur[1] . "', '" . insertMONDate($now) . "', 0, 0, 0, " . $nu_absent . ", 0)";
                            } else {
                                $oquery = "INSERT INTO ATTEN (VC_EMP_ID, DT_ATTEN, NU_ANH_OT, NU_SUN_OT, NU_PH_OT, NU_ABSENT) VALUES ('" . $cur[0] . "', '" . insertMONDate($now) . "', 0, 0, 0, " . $nu_absent . ")";
                            }
                            $res = ociParse($oconn, $oquery);
                            if (ociExecute($res)) {
                                $oquery = "INSERT INTO TEMP_ATTEN_CASUAL (VC_EMP_CODE, DT_ATTEN, NU_PR_AP_FLAG, NU_NIGHT_FLAG) VALUES ('" . $cur[0] . "', '" . insertMONDate($now) . "', 0, 0)";
                                $res = ociParse($oconn, $oquery);
                                if (!ociExecute($res)) {
                                    $err = oci_error();
                                    echo "\n\r" . $err;
                                    echo "\n\r" . $oquery;
                                }
                            } else {
                                $err = oci_error();
                                echo "\n\r" . $err;
                                echo "\n\r" . $oquery;
                            }
                        } else {
                            $ot1 = 0;
                            $ot2 = 0;
                            $ot3 = 0;
                            $ot4 = 0;
                            $night = $sub_result[5];
                            $nu_absent = 8;
                            if ($sub_result[1] == $sub_result[4] || $sub_result[2] == "Purple") {
                                $ot3 = $sub_result[0];
                                $ot4 = $sub_result[0];
                            } else {
                                if ($sub_result[1] == $sub_result[3]) {
                                    $ot2 = $sub_result[0];
                                } else {
                                    $ot1 = $sub_result[0];
                                }
                            }
                            if (getRegister($txtMACAddress, 7) == "177") {
                                $oquery = "INSERT INTO ATTEN (VC_EMP_ID, DT_ATTEN, NU_ANH_OT, NU_SAT_OT, NU_SUN_OT, NU_PH_OT, NU_ABSENT) VALUES ('" . $cur[1] . "', '" . insertMONDate($now) . "', " . round($ot1 / 3600, 0) . ", " . round($ot2 / 3600, 0) . ", " . round($ot3 / 3600, 0) . ", " . $night . ", 'P')";
                            } else {
                                if ($night == 1) {
                                    $night = 8;
                                }
                                $oquery = "INSERT INTO ATTEN (VC_EMP_ID, DT_ATTEN, NU_ANH_OT, NU_SUN_OT, NU_PH_OT, NU_ABSENT) VALUES ('" . $cur[0] . "', '" . insertMONDate($now) . "', " . round(($ot1 + $ot2) / 3600, 0) . ", " . round($ot3 / 3600, 0) . ", " . $night . ", 8)";
                            }
                            $res = ociParse($oconn, $oquery);
                            if (ociExecute($res)) {
                                if ($sub_result[5] == 1) {
                                    $oquery = "INSERT INTO TEMP_ATTEN_CASUAL (VC_EMP_CODE, DT_ATTEN, NU_PR_AP_FLAG, NU_NIGHT_FLAG) VALUES ('" . $cur[0] . "', '" . insertMONDate($now) . "', 1, 12)";
                                } else {
                                    $oquery = "INSERT INTO TEMP_ATTEN_CASUAL (VC_EMP_CODE, DT_ATTEN, NU_PR_AP_FLAG, NU_NIGHT_FLAG) VALUES ('" . $cur[0] . "', '" . insertMONDate($now) . "', 1, 0)";
                                }
                                $res = ociParse($oconn, $oquery);
                                if (!ociExecute($res)) {
                                    $err = oci_error();
                                    echo "\n\r" . $err;
                                    echo "\n\r" . $oquery;
                                }
                            } else {
                                $err = oci_error();
                                echo "\n\r" . $err;
                                echo "\n\r" . $oquery;
                            }
                        }
                        $now = getNextDay($now, 1);
                        ociCommit($oconn);
                    }
                    if (getRegister($txtMACAddress, 7) == "177") {
                        $sub_query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster WHERE ADate >= '" . $start . "' AND ADate <= '" . $end . "' AND (Day = OT1) AND EmployeeID = '" . $cur[0] . "'";
                    } else {
                        $sub_query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster WHERE ADate >= '" . $start . "' AND ADate <= '" . $end . "' AND (Day = OT1 OR Day = OT2 OR Flag = 'Purple') AND EmployeeID = '" . $cur[0] . "'";
                    }
                    $sub_result = mysqli_query($kconn, $sub_query);
                    while ($sub_cur = mysqli_fetch_row($sub_result)) {
                        if (0 < $sub_cur[0]) {
                            if (getRegister($txtMACAddress, 7) == "177") {
                                $oquery = "INSERT INTO FEEDING_ALLOWANCE (VC_EMP_ID, NU_DAYS) VALUES ('" . $cur[1] . "', '" . $sub_cur[0] . "')";
                            } else {
                                $oquery = "INSERT INTO FEEDING_ALLOWANCE (VC_EMP_ID, NU_DAYS) VALUES ('" . $cur[0] . "', '" . $sub_cur[0] . "')";
                            }
                            $res = ociParse($oconn, $oquery);
                            if (!ociExecute($res)) {
                            }
                        }
                    }
                    ociCommit($oconn);
                }
            }
        }
        $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('IMPCO Migration: Employee Record INSERTS-" . $counter . "', " . insertToday() . ", '" . getNow() . "')";
        updateIData($kconn, $query, true);
        return 1;
    }
    echo "\n\rUnable to connect to MSSQL Access Log Database";
}

?>