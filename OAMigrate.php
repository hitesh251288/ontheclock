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
if (checkMAC($conn) == true && $oconn != "") {
    print "Connected to Oracle Database: " . $oconn;
    $query = "UPDATE tuser SET phone = CONCAT('AG', RIGHT(id, 4)) WHERE id like '10%' ";
    updateIData($iconn, $query, true);
    $query = "UPDATE tuser SET phone = CONCAT('AT', RIGHT(id, 4)) WHERE id like '20%' ";
    updateIData($iconn, $query, true);
    $query = "SELECT OW_EMP_CODE, OW_EMP_NAME, OW_EMP_JOIN_DT, OW_EMP_JOB_LONG_DESC, OW_EMP_DIVN_CODE, OW_EMP_DEPT_CODE, OW_EMP_LOCN_CODE, OW_EMP_STATUS, OW_EMP_FRZ_FLAG FROM OW_EMP_MAST";
    $result = oci_parse($oconn, $query);
    oci_execute($result);
    while ($cur = oci_fetch_array($result, OCI_BOTH)) {
        $id = $cur[0];
        if (substr($id, 0, 2) == "AG") {
            $id = "10" . substr($id, 2, strlen($id));
        } else {
            if (substr($id, 0, 2) == "AT") {
                $id = "20" . substr($id, 2, strlen($id));
            }
        }
        if ($id * 1 == $id / 1) {
            $location = $cur[6];
            if ($location == "LS-LK-01") {
                $location = 1;
            } else {
                if ($location == "LS-HO-01") {
                    $location = "0002";
                } else {
                    if ($location == "AJ-CP-01") {
                        $location = "0003";
                    } else {
                        if ($location == "AJ-WU-01") {
                            $location = "0004";
                        } else {
                            if ($location == "AJ-ZW-01") {
                                $location = "0005";
                            } else {
                                if ($location == "LS-AO-01") {
                                    $location = "0006";
                                } else {
                                    if ($location == "LS-CW-01") {
                                        $location = "0007";
                                    } else {
                                        if ($location == "PH-AR-01") {
                                            $location = "0008";
                                        } else {
                                            if ($location == "PH-ZW-01") {
                                                $location = "0009";
                                            } else {
                                                if ($location == "PH-PM-01") {
                                                    $location = "10";
                                                } else {
                                                    if ($location == "LS-EC-01") {
                                                        $location = "13";
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
            }
            $query = "SELECT L_ID FROM UNIS.tuser WHERE L_ID = " . $id;
            $result = selectData($conn, $query);
            if (0 < $result[0]) {
                $query = "UPDATE UNIS.tuser SET C_Name = '" . $cur[1] . "' WHERE L_ID = '" . $id . "' ";
                if (updateIData($iconn, $query, true)) {
                    $query = "UPDATE UNIS.temploye SET C_Office = '" . $location . "' WHERE L_UID = '" . $id . "' ";
                    if (updateIData($iconn, $query, true)) {
                        $query = "UPDATE Access.tuser SET name = '" . $cur[1] . "', company = '" . $cur[4] . "', dept = '" . $cur[5] . "', phone = '" . $cur[0] . "' WHERE id = '" . $id . "' ";
                        updateIData($iconn, $query, true);
                    }
                }
            } else {
                $query = "INSERT INTO UNIS.tuser (L_ID, C_Name) VALUES ('" . $cur[1] . "', '" . $id . "') ";
                if (updateIData($iconn, $query, true)) {
                    $query = "UPDATE UNIS.temploye SET C_Office = '" . $location . "' WHERE L_UID = '" . $id . "' ";
                    if (updateIData($iconn, $query, true)) {
                        $query = "UPDATE Access.tuser SET name = '" . $cur[1] . "', company = '" . $cur[4] . "', dept = '" . $cur[5] . "', phone = '" . $cur[0] . "' WHERE id = '" . $id . "' ";
                        updateIData($iconn, $query, true);
                    }
                }
            }
        }
    }
    $last_cur[0] = "";
    $dd = addZero(substr($txtLockDate, 6, 2) * 1 + 1, 2);
    $mm = addZero(substr($txtLockDate, 4, 2) * 1 - 1, 2);
    $yyyy = substr($txtLockDate, 0, 4);
    if (substr($txtLockDate, 4, 2) == "01") {
        $yyyy = substr($txtLockDate, 0, 4) * 1 - 1;
        $mm = "12";
    }
    if (substr($txtLockDate, 6, 2) == "31") {
        $dd = "01";
        $mm = substr($txtLockDate, 4, 2);
    }
    $last_cur[0] = $yyyy . $mm . $dd;
    $query = "DELETE FROM OW_ABSENTEEISM WHERE OW_ATND_DATE = To_Date('" . displayDate($txtLockDate) . "', 'DD/MM/YYYY')";
    $res = ociParse($oconn, $query);
    ociExecute($res);
    $query = "DELETE FROM OW_OVERTIME WHERE OW_OTD_DT = To_Date('" . displayDate($txtLockDate) . "', 'DD/MM/YYYY')";
    $res = ociParse($oconn, $query);
    ociExecute($res);
    ociCommit($oconn);
    $counter = 0;
    $query = "SELECT id, company, dept, phone FROM Access.tuser WHERE id > 0 AND phone is NOT NULL AND LENGTH(phone) = 6";
    $main__result = mysqli_query($conn, $query);
    while ($main_cur = mysqli_fetch_row($main__result)) {
        $query = "SELECT COUNT(AttendanceMaster.AttendanceID) FROM Access.AttendanceMaster WHERE AttendanceMaster.ADate <= " . $txtLockDate . " AND ADate >= " . $last_cur[0] . " AND AttendanceMaster.EmployeeID = " . $main_cur[0];
        $result = selectData($conn, $query);
        if (0 < $result[0]) {
            $absent_days = getASS($conn, $main_cur[0], displayDate($last_cur[0]), displayDate($txtLockDate));
            $query = "INSERT INTO OW_ABSENTEEISM (OW_ATND_DATE , OW_ATND_DIVN_CODE , OW_ATND_DEPT_CODE , OW_ATND_EMP_CODE , OW_ATND_ABSENT_DAYS, OW_ATND_FLAG, OW_ATND_COMP_CODE) VALUES (To_Date('" . displayDate($txtLockDate) . "', 'DD/MM/YYYY'), '" . $main_cur[1] . "', '" . $main_cur[2] . "', '" . $main_cur[3] . "', " . $absent_days . ", 'N', '001') ";
            $res = ociParse($oconn, $query);
            ociExecute($res);
            $query = "SELECT SUM(AttendanceMaster.AOvertime) FROM Access.AttendanceMaster WHERE Day <> OT1 AND Day <> OT2 AND Flag <> 'Purple' AND AttendanceMaster.ADate <= " . $txtLockDate . " AND ADate >= " . $last_cur[0] . " AND AttendanceMaster.EmployeeID = " . $main_cur[0];
            $result = selectData($conn, $query);
            $ot1 = round($result[0] / 3600);
            $query = "SELECT SUM(AttendanceMaster.AOvertime) FROM Access.AttendanceMaster WHERE Day = OT1 AND AttendanceMaster.ADate <= " . $txtLockDate . " AND ADate >= " . $last_cur[0] . " AND AttendanceMaster.EmployeeID = " . $main_cur[0];
            $result = selectData($conn, $query);
            $ot2 = round($result[0] / 3600);
            $query = "SELECT SUM(AttendanceMaster.AOvertime) FROM Access.AttendanceMaster WHERE Day = OT2 AND AttendanceMaster.ADate <= " . $txtLockDate . " AND ADate >= " . $last_cur[0] . " AND AttendanceMaster.EmployeeID = " . $main_cur[0];
            $result = selectData($conn, $query);
            $ot3 = round($result[0] / 3600);
            $query = "SELECT SUM(AttendanceMaster.AOvertime) FROM Access.AttendanceMaster WHERE Flag = 'Purple' AND AttendanceMaster.ADate <= " . $txtLockDate . " AND ADate >= " . $last_cur[0] . " AND AttendanceMaster.EmployeeID = " . $main_cur[0];
            $result = selectData($conn, $query);
            $ot4 = round($result[0] / 3600);
            $query = "INSERT INTO OW_OVERTIME (OW_OTD_DT, OW_OTD_DIVN_CODE, OW_OTD_DEPT_CODE, OW_OTD_EMP_CODE, OW_OTD_OT_CODE, OW_OTD_TOTAL_OT_HRS, OW_OTD_FLAG, OW_OTD_COMP_CODE) VALUES (To_Date('" . displayDate($txtLockDate) . "', 'DD/MM/YYYY'), '" . $main_cur[1] . "', '" . $main_cur[2] . "', '" . $main_cur[3] . "', 'OT1', " . $ot1 . ", 'N', '001') ";
            $res = ociParse($oconn, $query);
            ociExecute($res);
            $query = "INSERT INTO OW_OVERTIME (OW_OTD_DT, OW_OTD_DIVN_CODE, OW_OTD_DEPT_CODE, OW_OTD_EMP_CODE, OW_OTD_OT_CODE, OW_OTD_TOTAL_OT_HRS, OW_OTD_FLAG, OW_OTD_COMP_CODE) VALUES (To_Date('" . displayDate($txtLockDate) . "', 'DD/MM/YYYY'), '" . $main_cur[1] . "', '" . $main_cur[2] . "', '" . $main_cur[3] . "', 'OT2', " . $ot2 . ", 'N', '001') ";
            $res = ociParse($oconn, $query);
            ociExecute($res);
            $query = "INSERT INTO OW_OVERTIME (OW_OTD_DT, OW_OTD_DIVN_CODE, OW_OTD_DEPT_CODE, OW_OTD_EMP_CODE, OW_OTD_OT_CODE, OW_OTD_TOTAL_OT_HRS, OW_OTD_FLAG, OW_OTD_COMP_CODE) VALUES (To_Date('" . displayDate($txtLockDate) . "', 'DD/MM/YYYY'), '" . $main_cur[1] . "', '" . $main_cur[2] . "', '" . $main_cur[3] . "', 'OT3', " . ($ot3 + $ot4) . ", 'N', '001') ";
            $res = ociParse($oconn, $query);
            ociExecute($res);
            $counter++;
        }
    }
    $query = "DELETE FROM OW_ABSENTEEISM WHERE OW_ATND_DIVN_CODE = '.' OR OW_ATND_DIVN_CODE IS NULL OR OW_ATND_DIVN_CODE = '' OR OW_ATND_DEPT_CODE = '.' OR OW_ATND_DEPT_CODE = '' OR OW_ATND_DEPT_CODE IS NULL OR OW_ATND_EMP_CODE = '' OR OW_ATND_EMP_CODE IS NULL ";
    $res = ociParse($oconn, $query);
    ociExecute($res);
    $query = "DELETE FROM OW_OVERTIME WHERE OW_OTD_DIVN_CODE = '.' OR OW_OTD_DIVN_CODE IS NULL OR OW_OTD_DIVN_CODE = '' OR OW_OTD_DEPT_CODE = '.' OR OW_OTD_DEPT_CODE = '' OR OW_OTD_DEPT_CODE IS NULL OR OW_OTD_EMP_CODE = '' OR OW_OTD_EMP_CODE IS NULL ";
    $res = ociParse($oconn, $query);
    ociExecute($res);
    if (ociCommit($oconn)) {
        $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('DB Migrate - From: " . displayDate($last_cur[0]) . " - To: " . displayDate($txtLockDate) . " - Records: " . $counter . "', " . insertToday() . ", '" . getNow() . "')";
        updateData($conn, $query, true);
        return 1;
    }
} else {
    print "Process Terminated: Un Registered Application OR Connection to Oracle Database NOT found: " . $oconn;
}

?>