<?php


ob_start("ob_gzhandler");
error_reporting(E_ALL);
date_default_timezone_set("Africa/Algiers");
include "Functions.php";
$conn = openConnection();
$iconn = openIConnection();
$query = "SELECT DBIP, DBName, DBUser, DBPass, EmployeeCodeLength, LockDate FROM OtherSettingMaster";
$main_result = selectData($conn, $query);
$txtDBIP = $main_result[0];
$txtDBName = $main_result[1];
$txtDBUser = $main_result[2];
$txtDBPass = $main_result[3];
$txtECodeLength = $main_result[4];
$txtLockDate = $main_result[5];
if (checkMAC($conn)) {
    $aconn = odbc_connection("", "virdi", "", "");
    if ($aconn != "") {
        $query = "SELECT CHECKINOUT.CHECKTIME, USERINFO.BadgeNumber, CHECKINOUT.SENSORID FROM CHECKINOUT, USERINFO WHERE CHECKINOUT.USERID = USERINFO.USERID AND CHECKINOUT.CHECKTIME > '" . displayParadoxDate($txtLockDate) . " 00:00:00' ORDER BY CHECKINOUT.CHECKTIME";
        $result = odbc_exec($aconn, $query);
        while (odbc_fetch_into($result, $cur)) {
            $query = "INSERT IGNORE INTO tenter (e_date, e_time, g_id, e_id, e_group) VALUES ('" . insertParadoxDate(substr($cur[0], 0, 10)) . "', '" . insertParadoxTime($cur[0]) . "', '" . $cur[2] . "', " . $cur[1] . ", '419')";
            if (!updateIData($iconn, $query, true)) {
                echo "\n\rError: " . $query;
            }
        }
        $query = "UPDATE tenter, tuser SET tenter.e_group = tuser.group_id WHERE tenter.e_group = '419' AND tenter.e_id = tuser.id AND tuser.group_id > 1";
        if (updateIData($iconn, $query, true)) {
        }
        $query = "SELECT USERINFO.BadgeNumber, USERINFO.Name, USERINFO.Title, USERINFO.Gender, USERINFO.Pager, 20501231, USERINFO.HIREDDAY, USERINFO.SSN, DEPARTMENTS.DEPTNAME, USERINFO.DEFAULTDEPTID, USERINFO.Street FROM USERINFO, DEPARTMENTS WHERE DEPARTMENTS.DEPTID = USERINFO.DEFAULTDEPTID ORDER BY USERINFO.BadgeNumber";
        $result = odbc_exec($aconn, $query);
        $reg_date = "";
        $exp_date = "";
        $group_val = "";
        while (odbc_fetch_into($result, $cur)) {
            if (strlen(insertParadoxDate(substr($cur[6], 0, 10))) < 8) {
                $reg_date = "20010101";
            } else {
                $reg_date = insertParadoxDate(substr($cur[6], 0, 10));
            }
            if (strlen(insertParadoxDate(substr($cur[5], 0, 10))) < 8 && 0 < $cur[9] * 1) {
                $exp_date = "99991231";
            } else {
                $exp_date = insertParadoxDate(substr($cur[6], 0, 10));
            }
            $query = "SELECT id FROM tuser WHERE id = '" . $cur[0] . "'";
            $sub_result = selectData($conn, $query);
            if ($sub_result[0] == $cur[0]) {
                if ($exp_date < insertToday()) {
                    $query = "UPDATE tuser SET name = '" . replaceString($cur[1], false) . "', idno = '" . $cur[3] . "', company = '" . replaceString($cur[2], false) . "', phone = '" . replaceString($cur[7], false) . "', datelimit = 'Y" . $reg_date . "" . $exp_date . "', PassiveType = 'RSN', dept = '" . replaceString($cur[8], false) . "', F3 = '" . replaceString($cur[4], false) . "', F2 = '" . replaceString($cur[10], false) . "' WHERE id = '" . $cur[0] . "'";
                } else {
                    $query = "UPDATE tuser SET name = '" . replaceString($cur[1], false) . "', idno = '" . $cur[3] . "', company = '" . replaceString($cur[2], false) . "', phone = '" . replaceString($cur[7], false) . "', datelimit = 'N" . $reg_date . "" . $exp_date . "', PassiveType = 'ACT', dept = '" . replaceString($cur[8], false) . "', F3 = '" . replaceString($cur[4], false) . "', F2 = '" . replaceString($cur[10], false) . "'  ";
                    if (stripos($cur[2], "CONTRACT") !== false || strlen($cur[0]) == 6) {
                        $query .= ", remark = 'CAS'";
                    } else {
                        $query .= ", remark = 'REG'";
                    }
                    $query .= " WHERE id = '" . $cur[0] . "'";
                }
                if (!updateIData($iconn, $query, true)) {
                    echo "\n\rError: " . $query;
                }
            } else {
                $query = "INSERT INTO tuser (id, name, dept, company, idno, remark, datelimit, reg_date, phone) VALUES ('" . $cur[0] . "', '" . replaceString($cur[1], false) . "', '" . replaceString($cur[8], false) . "', '" . replaceString($cur[2], false) . "', '" . replaceString($cur[3], false) . "'";
                if (stripos($cur[2], "CONTRACT") !== false || strlen($cur[0]) == 6) {
                    $query .= ", 'CAS'";
                } else {
                    $query .= ", 'REG'";
                }
                $query .= ", 'N" . $reg_date . "" . $exp_date . "', '" . insertParadoxDate(substr($cur[7], 0, 10)) . "0100', '" . replaceString($cur[7], false) . "')";
                if (!updateIData($iconn, $query, true)) {
                    echo "\n\rError: " . $query;
                    exit;
                }
            }
            $query = "INSERT IGNORE INTO EmployeeFlag (EmployeeID) VALUES ('" . $cur[0] . "')";
            if (!updateIData($iconn, $query, true)) {
                echo "\n\rError: " . $query;
            }
        }
        $query = "DELETE FROM tgate";
        if (!updateIData($iconn, $query, true)) {
            echo "\n\rError: " . $query;
        }
        $query = "SELECT Machines.MachineNumber, Machines.MachineAlias FROM Machines WHERE Machines.MachineAlias NOT LIKE '' ORDER BY Machines.ID";
        $result = odbc_exec($aconn, $query);
        while (odbc_fetch_into($result, $cur)) {
            $query = "INSERT INTO tgate (id, name) VALUES ('" . $cur[0] . "', '" . replaceString($cur[1], false) . "')";
            if (!updateIData($iconn, $query, true)) {
                echo "\n\rError: " . $query;
            }
        }
        $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('ALG Migrate', " . insertToday() . ", '" . getNow() . "')";
        if (updateIData($iconn, $query, true)) {
            return 1;
        }
    } else {
        print "Connection to External Database NOT available. Process Terminated.";
        exit;
    }
} else {
    print "Un Registered Application. Process Terminated.";
    exit;
}
function nitgenCode($data)
{
    $data = $data . "000000000";
    $data = addZero($data, 15);
    return $data;
}

?>