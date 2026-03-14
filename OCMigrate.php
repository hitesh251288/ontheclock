<?php


error_reporting(E_ERROR);
ob_start("ob_gzhandler");
date_default_timezone_set("Africa/Algiers");
include "Functions.php";
set_time_limit(0);
$conn = openConnection();
$iconn = openIConnection();
$query = "SELECT LockDate, MACAddress, DBType, DBIP, DBName, DBUser, DBPass FROM OtherSettingMaster";
$main_result = selectData($conn, $query);
$txtLockDate = $main_result[0];
$txtMACAdress = $main_result[1];
$lstDBType = $main_result[2];
$txtDBIP = $main_result[3];
$txtDBName = $main_result[4];
$txtDBUser = $main_result[5];
$txtDBPass = $main_result[6];
$oconn = "";
$oconn = oracle_connection($txtDBIP, $txtDBName, $txtDBUser, $txtDBPass);
if (checkMAC($conn) == true && $oconn != "") {
    print "Connected to Oracle Database: " . $oconn;
    $query = "SELECT VC_EMP_ID, VC_EMP_NAME FROM PERSDET ORDER BY VC_EMP_ID";
    $result = odbc_exec($unis_conn, $query);
    while (odbc_fetch_into($result, $cur)) {
        $query = "UPDATE tuser SET phone = '" . $cur[0] . "' WHERE remark = '" . $cur[1] . "' ";
        updateIData($iconn, $query, true);
    }
    $query = "SELECT VC_STRUCT_CODE, VC_DESCRIPTION FROM MST_STRUCT ORDER BY VC_EMP_ID";
    $result = odbc_exec($unis_conn, $query);
    while (odbc_fetch_into($result, $cur)) {
        $query = "UPDATE tuser SET company = '" . $cur[0] . "' WHERE dept = '" . $cur[1] . "' ";
        updateIData($iconn, $query, true);
    }
    $casual_count_insert = 0;
    $casual_count_update = 0;
    $casual_count_no = 0;
    $query = "SELECT tuser.id, tuser.name, tuser.idno, tuser.datelimit, flagdatelimit, remark, dept, phone, company FROM tuser";
    $result = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
        $startdate = substr($cur[3], 1, 8);
        if ($startdate == "19770430") {
            $startdate = substr($cur[4], 1, 8);
        }
        $query = "INSERT INTO CASUAL (VC_EMP_ID, VC_EMP_NAME, VC_SEX, DT_JOIN, VC_LINE_OPERATOR, VC_DEPARTMENT) VALUES ('" . $cur[0] . "', '" . $cur[1] . "', '" . $cur[2] . "', To_Date('" . displayDate($startdate) . "', 'DD/MM/YYYY'), '" . $cur[7] . "', '" . $cur[8] . "' ) ";
        $res = ociParse($oconn, $query);
        if (ociExecute($res)) {
            $casual_count_insert++;
        } else {
            $query = "UPDATE CASUAL SET VC_EMP_NAME = '" . $cur[1] . "', VC_SEX = '" . $cur[2] . "', DT_JOIN = To_Date('" . displayDate($startdate) . "', 'DD/MM/YYYY'), VC_LINE_OPERATOR = '" . $cur[7] . "', VC_DEPARTMENT = '" . $cur[8] . "' WHERE VC_EMP_ID = '" . $cur[0] . "' ";
            $res = ociParse($oconn, $query);
            if (ociExecute($res)) {
                $casual_count_update++;
            } else {
                $casual_count_no++;
            }
        }
    }
    $query = "SELECT MAX(AttendanceID) FROM TEMP_ATTEN_CASUAL";
    $result = odbc_exec($unis_conn, $query);
    odbc_fetch_into($result, $cur);
    if ($cur[0] == "" || $cur == "") {
        $cur[0] = 0;
    }
    $att_count_yes = 0;
    $att_count_no = 0;
    $query = "SELECT AttendanceID, EmployeeID, ADate, AOvertime, NightFlag, Day, OT1, OT2, Flag, (Normal+AOvertime) FROM AttendanceMaster ";
    $result = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
        $shift = "D";
        if ($cur[4] == 1) {
            $shift = "N";
        }
        $query = "INSERT INTO TEMP_ATTEN_CASUAL (AttendanceID, VC_EMP_CODE, DT_ATTEN, NU_PR_AP_FLAG, NU_OT, VC_SHIFT, VC_Field1, VC_Field2) VALUES ('" . $cur[0] . "', '" . $cur[1] . "', To_Date('" . displayDate($cur[2]) . "', 'DD/MM/YYYY'), '1', ROUND(" . $cur[3] . "/3600), '" . $shift . "', ";
        if ($cur[8] == "Purple") {
            $query .= " '3', ";
        } else {
            if ($cur[5] == $cur[6]) {
                $query .= " '1', ";
            } else {
                if ($cur[5] == $cur[7]) {
                    $query .= " '2', ";
                } else {
                    $query .= " '0', ";
                }
            }
        }
        if (14400 <= $cur[9]) {
            $query .= " '1' ";
        } else {
            $query .= " '0' ";
        }
        $query .= ")";
        $res = ociParse($oconn, $query);
        if (ociExecute($res)) {
            $att_count_yes++;
        } else {
            $att_count_no++;
        }
    }
    if (ociCommit($oconn)) {
        $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('DB Migrate - Employees Inserted:" . $casual_count_insert . " - Employees Updated:" . $casual_count_update . " - Employees Update Error:" . $casual_count_no . " - Attendance Inserted:" . $att_count_yes . " - Attendance Insert Error:" . $att_count_no . "', " . insertToday() . ", '" . getNow() . "')";
        updateIData($iconn, $query, true);
        return 1;
    }
} else {
    print "Process Terminated. UnRegistered Application OR Connection to Oracle Database NOT found: " . $oconn;
}

?>