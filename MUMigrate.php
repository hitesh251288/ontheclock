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
    $txtDBIP = $txtDBIP . ",1433\\SQLEXPRESS";
    $oconn = mssql_connection($txtDBIP, $txtDBName, $txtDBUser, $txtDBPass);
    if ($oconn != "") {
        echo "\n\rConnected to MSSQL: " . $oconn;
        $icounter = 0;
        $ucounter = 0;
        $query = "SELECT ssTagHolders.TagHolderID, ssTagHolders.TagHolderGUID, ssTagHolders.Title, ssCompanies.Name, ssDepartments.Name, CONVERT(VARCHAR(20), ssTagHolders.AccessValidFrom, 121), CONVERT(VARCHAR(20), ssTagHolders.AccessValidTo, 121), ssTagHolders.Surname, ssTagHolders.FirstName FROM ssTagHolders, ssCompanies, ssDepartments WHERE ssTagHolders.CompanyID = ssCompanies.CompanyID AND ssTagHolders.DepartmentID = ssDepartments.DepartmentID AND ssTagHolders.CompanyID = ssDepartments.CompanyID ORDER BY ssTagHolders.TagHolderID";
        $result = mssql_query($query, $oconn);
        while ($cur = mssql_fetch_row($result)) {
            $query = "SELECT id, Name, idno, company, dept, datelimit FROM tuser WHERE id = '" . $cur[0] . "'";
            $sub_result = selectData($conn, $query);
            if ($sub_result[0] == $cur[0]) {
                if ($sub_result[1] != $cur[7] . " " . $cur[8] || $sub_result[2] != $cur[2] || $sub_result[3] != $cur[3] || $sub_result[4] != $cur[4] || insertParadoxDate($cur[5]) != substr($cur[5], 1, 8) || insertParadoxDate($cur[6]) != substr($cur[5], 9, 8)) {
                    $query = "UPDATE tuser SET name = '" . replaceString($cur[7] . " " . $cur[8], false) . "', idno = '" . replaceString($cur[2], false) . "', company = '" . replaceString($cur[3], false) . "', dept = '" . replaceString($cur[4], false) . "', ";
                    if (insertParadoxDate($cur[6]) < insertToday()) {
                        $query .= " datelimit = 'Y" . insertParadoxDate($cur[5]) . "" . insertParadoxDate($cur[6]) . "'";
                    } else {
                        $query .= " datelimit = 'N" . insertParadoxDate($cur[5]) . "" . insertParadoxDate($cur[6]) . "'";
                    }
                    $query .= " WHERE id = '" . $cur[0] . "'";
                    if (updateIData($iconn, $query, true)) {
                        $ucounter++;
                    } else {
                        echo "\n\r\n\r Error in Query - " . $query;
                        exit;
                    }
                }
            } else {
                $query = "INSERT INTO tuser (id, Name, idno, company, dept, datelimit, F10, reg_date) VALUES ('" . $cur[0] . "', '" . replaceString($cur[7] . " " . $cur[8], false) . "', '" . replaceString($cur[2], false) . "', '" . replaceString($cur[3], false) . "', '" . replaceString($cur[4], false) . "', ";
                if (insertParadoxDate($cur[6]) < insertToday()) {
                    $query .= " 'Y" . insertParadoxDate($cur[5]) . "" . insertParadoxDate($cur[6]) . "'";
                } else {
                    $query .= " 'N" . insertParadoxDate($cur[5]) . "" . insertParadoxDate($cur[6]) . "'";
                }
                $query .= ", '" . $cur[1] . "', '201701010000')";
                if (updateIData($iconn, $query, true)) {
                    $icounter++;
                } else {
                    echo "\n\r\n\r Error in Query - " . $query;
                    exit;
                }
            }
        }
        $query = "SELECT ReaderID, Name FROM ssReaders ORDER BY ReaderID";
        $result = mssql_query($query, $oconn);
        while ($cur = mssql_fetch_row($result)) {
            $query = "SELECT id, name FROM tgate WHERE id = '" . $cur[0] . "'";
            $sub_result = selectData($conn, $query);
            if ($sub_result[0] == $cur[0]) {
                if ($sub_result[1] != $cur[1]) {
                    $query = "UPDATE tgate SET name = '" . replaceString($cur[1], false) . "' WHERE id = '" . $cur[0] . "'";
                    if (!updateIData($iconn, $query, true)) {
                        echo "\n\r\n\r Error in Query - " . $query;
                        exit;
                    }
                }
            } else {
                $query = "INSERT INTO tgate (id, Name, reg_date) VALUES ('" . $cur[0] . "', '" . $cur[1] . "', '" . insertToday() . "0100')";
                if (!updateIData($iconn, $query, true)) {
                    echo "\n\r\n\r Error in Query - " . $query;
                    exit;
                }
            }
        }
        mssql_close($oconn);
    } else {
        echo "\n\rUnable to connect to MSSQL Master Database";
    }
    $txtDBName = "ssAccessLog";
    $oconn = mssql_connection($txtDBIP, $txtDBName, $txtDBUser, $txtDBPass);
    if ($oconn != "") {
        echo "\n\rConnected to Access Logs: " . $oconn;
        $query = "SELECT MAX(ed) FROM tenter";
        $result = selectData($conn, $query);
        $start = $result[0];
        if ($start == "" || $start == 0) {
            $query = "SELECT MIN(LogID) FROM ssAccessLog WHERE DateTime >= '" . displayParadoxDate($txtLockDate) . " 00:00:00' AND DateTime <= '" . displayParadoxDate(insertToday()) . " 23:59:59'";
            $result = mssql_query($query, $oconn);
            $cur = mssql_fetch_row($result);
            $start = $cur[0];
        }
        $query = "SELECT MAX(LogID) FROM ssAccessLog WHERE DateTime >= '" . displayParadoxDate($txtLockDate) . " 00:00:00' AND DateTime <= '" . displayParadoxDate(insertToday()) . " 23:59:59'";
        $result = mssql_query($query, $oconn);
        $cur = mssql_fetch_row($result);
        $end = $cur[0];
        $query = "SELECT LogID, CONVERT(VARCHAR(20), DateTime, 121), TagID, ReaderID, TagHolderGUID FROM ssAccessLog WHERE LogID > '" . $start . "' AND LogID <= '" . $end . "' ORDER BY LogID";
        $counter = 0;
        $result = mssql_query($query, $oconn);
        while ($cur = mssql_fetch_row($result)) {
            $counter++;
            $query = "INSERT IGNORE INTO tenter (e_id, e_date, e_time, g_id, ed, e_group, e_idno) VALUES ('" . $cur[2] . "', '" . insertParadoxDate($cur[1]) . "', '" . insertParadoxTime($cur[1]) . "', '" . $cur[3] . "', '" . $cur[0] . "', '999', '" . $cur[4] . "')";
            if (!updateIData($iconn, $query, true)) {
            }
        }
        $query = "UPDATE tenter, tuser SET tenter.e_id = tuser.id WHERE tenter.e_idno = tuser.F10 AND tenter.e_group = '999' AND tuser.group_id > 1 AND tenter.p_flag = 0 AND e_date > " . $txtLockDate;
        if (updateIData($iconn, $query, true)) {
            $query = "UPDATE tenter, tuser SET tenter.e_group = tuser.group_id WHERE tenter.e_id = tuser.id AND tenter.e_group = '999' AND tuser.group_id > 1 AND tenter.p_flag = 0 AND e_date > " . $txtLockDate;
            if (updateIData($jconn, $query, true)) {
                $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('UNICEM Migration: Master INSERTS-" . $icounter . ", Master UPDATES-" . $ucounter . ", Clocking Logs INSERTS-" . $counter . " ', " . insertToday() . ", '" . getNow() . "')";
                updateIData($kconn, $query, true);
            }
        }
        mssql_close($oconn);
        return 1;
    }
    echo "\n\rUnable to connect to MSSQL Access Log Database";
}

?>