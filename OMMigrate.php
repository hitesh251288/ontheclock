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
        $oquery = "DELETE FROM ATTENDANCE_UPLOAD_Virdi";
        $res = ociParse($oconn, $oquery);
        if (ociExecute($res)) {
            ociCommit($oconn);
        }
        $oquery = "";
        $migrate_query = "SELECT Col, Val, DateFrom, DateTo, MonthNo FROM MigrateMaster WHERE LENGTH(DateFrom) = 8 AND LENGTH(DateTo) = 8 ORDER BY Col, Val ";
        $counter = 0;
        $migrate_result = mysqli_query($conn, $migrate_query);
        while ($migrate_cur = mysqli_fetch_row($migrate_result)) {
            if ($migrate_cur[2] != "" && $migrate_cur[3] != "") {
                $start = $migrate_cur[2];
                $end = $migrate_cur[3];
                $query = "SELECT id FROM tuser WHERE " . $migrate_cur[0] . " = '" . $migrate_cur[1] . "' AND PassiveType = 'ACT' ";
                $result = mysqli_query($iconn, $query);
                while ($cur = mysqli_fetch_row($result)) {
                    $counter++;
                    $a1 = 0;
                    $a2 = 0;
                    $a3 = 0;
                    $a4 = 0;
                    $a5 = 0;
                    $a6 = 0;
                    $a7 = 0;
                    $a8 = 0;
                    $a9 = 0;
                    $a10 = 0;
                    $a11 = 0;
                    $sub_query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster WHERE ADate >= '" . $start . "' AND ADate <= '" . $end . "' AND (Day <> OT2) AND EmployeeID = '" . $cur[0] . "'";
                    $sub_result = selectData($kconn, $sub_query);
                    $a1 = $sub_result[0];
                    $a2 = getAS($kconn, $cur[0], displayDate($start), displayDate($end));
                    $a4 = getTotalDays(displayDate($start), displayDate($end));
                    $a3 = getDayCount($start, $end, $a4, "Sunday");
                    $sub_query = "SELECT SUM(LateIn) FROM AttendanceMaster WHERE ADate >= '" . $start . "' AND ADate <= '" . $end . "' AND EmployeeID = '" . $cur[0] . "'";
                    $sub_result = selectData($kconn, $sub_query);
                    $a5 = round($sub_result[0] / 60, 2);
                    $sub_query = "SELECT SUM(AOvertime) FROM AttendanceMaster WHERE ADate >= '" . $start . "' AND ADate <= '" . $end . "' AND EmployeeID = '" . $cur[0] . "' AND Day <> OT1 AND Day <> OT2 AND Flag <> 'Purple' ";
                    $sub_result = selectData($kconn, $sub_query);
                    $a7 = round($sub_result[0] / 3600, 2);
                    $sub_query = "SELECT SUM(AOvertime) FROM AttendanceMaster WHERE ADate >= '" . $start . "' AND ADate <= '" . $end . "' AND EmployeeID = '" . $cur[0] . "' AND Day = OT1 AND Flag <> 'Purple' ";
                    $sub_result = selectData($kconn, $sub_query);
                    $a8 = round($sub_result[0] / 3600, 2);
                    $sub_query = "SELECT SUM(AOvertime) FROM AttendanceMaster WHERE ADate >= '" . $start . "' AND ADate <= '" . $end . "' AND EmployeeID = '" . $cur[0] . "' AND Day = OT2 AND Flag <> 'Purple' ";
                    $sub_result = selectData($kconn, $sub_query);
                    $a9 = round($sub_result[0] * 2 / 3600, 2);
                    $sub_query = "SELECT SUM(AOvertime) FROM AttendanceMaster WHERE ADate >= '" . $start . "' AND ADate <= '" . $end . "' AND EmployeeID = '" . $cur[0] . "' AND Flag = 'Purple' ";
                    $sub_result = selectData($kconn, $sub_query);
                    $a10 = round($sub_result[0] * 2 / 3600, 2);
                    $a11 = $a7 + $a8 + $a9 + $a10;
                    $a12 = "L";
                    if (substr($cur[0], 0, 1) == "1") {
                        $a12 = "C";
                    }
                    $oquery = "INSERT INTO ATTENDANCE_UPLOAD_Virdi (VC_EMP_CARD_NO, NU_PRESENT_DAYS, NU_ABSENT_DAYS, NU_HOLIDAYS, NU_TOTAL, NU_ABSENT_HRS, EXTRA_HRS_ANS, EXTRA_HRS_NH, EXTRA_HRS_WOFF, EXTRA_HRS_PH, NU_TOTAL_OT, CAT_TYPE) VALUES ('" . $cur[0] . "', '" . $a1 . "', '" . $a2 . "', '" . $a3 . "', '" . $a4 . "', '" . $a5 . "', '" . $a7 . "', '" . $a8 . "', '" . $a9 . "', '" . $a10 . "', '" . $a11 . "', '" . $a12 . "')";
                    $res = ociParse($oconn, $oquery);
                    if (ociExecute($res)) {
                        ociCommit($oconn);
                        $counter++;
                    } else {
                        echo "\n\rError in Query: " . $oquery;
                    }
                }
            }
        }
        $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('Mikano Migration: Attenddance Record INSERTS-" . $counter . "', " . insertToday() . ", '" . getNow() . "')";
        updateIData($kconn, $query, true);
        return 1;
    }
    echo "\n\rUnable to connect to Oracle Database";
}

?>