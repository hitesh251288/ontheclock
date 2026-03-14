<?php


ob_start("ob_gzhandler");
error_reporting(E_ALL);
date_default_timezone_set("Africa/Algiers");
include "Functions.php";
$conn = openConnection();
$iconn = openIConnection();
$query = "SELECT DBIP, DBName, DBUser, DBPass, EmployeeCodeLength FROM OtherSettingMaster";
$main_result = selectData($conn, $query);
$txtDBIP = $main_result[0];
$txtDBName = $main_result[1];
$txtDBUser = $main_result[2];
$txtDBPass = $main_result[3];
$txtECodeLength = $main_result[4];
if (checkMAC($conn)) {
    $aconn = odbc_connection("", "nitgenacdb", "admin", "nac3000");
    if ($aconn != "") {
        $query = "SELECT e_idno FROM tenter";
        $result = selectData($conn, $query);
        $last_ed = $result[0];
        if ($last_ed == "") {
            $last_ed = 0;
        }
        $query = "SELECT MAX(logindex) FROM NGAC_LOG";
        $last_result = odbc_exec($aconn, $query);
        odbc_fetch_into($last_result, $last_cur);
        $max_ed = $last_cur[0];
        $query = "SELECT NGAC_LOG.logtime, NGAC_LOG.userid, NGAC_LOG.nodeid FROM NGAC_LOG WHERE NGAC_LOG.userid NOT LIKE '' AND NGAC_LOG.logindex > " . $last_ed . " AND NGAC_LOG.logindex <= " . $max_ed . " AND Authresult = 0 ORDER BY NGAC_LOG.logindex";
        $result = odbc_exec($aconn, $query);
        while (odbc_fetch_into($result, $cur)) {
            $query = "INSERT INTO tenter (e_date, e_time, g_id, e_id, e_group) VALUES ('" . insertParadoxDate(substr($cur[0], 0, 10)) . "', '" . insertTime(substr($cur[0], 11, 8)) . "', '" . $cur[2] . "', '" . $cur[1] / 1000000000 . "', '419')";
            updateIData($iconn, $query, true);
        }
        $query = "UPDATE tenter, tuser SET tenter.e_group = tuser.group_id WHERE tenter.e_group = '419' AND tenter.e_id = tuser.id AND tuser.group_id > 1";
        if (updateIData($iconn, $query, true)) {
            $query = "UPDATE tenter SET e_idno ='" . $max_ed . "'";
            updateIData($iconn, $query, true);
        }
        $query = "SELECT NGAC_USERINFO.userid, NGAC_USERINFO.username, NGAC_USERINFO.department, NGAC_USERINFO.description, NGAC_GROUP.groupname, NGAC_USERINFO.employeeno, NGAC_USERINFO.expdate, NGAC_USERINFO.regdate, NGAC_USERINFO.fir, NGAC_USERINFO.password FROM NGAC_USERINFO, NGAC_GROUP WHERE NGAC_USERINFO.groupid = NGAC_GROUP.groupid AND NGAC_GROUP.groupid <> 42 ORDER BY NGAC_USERINFO.userid";
        $result = odbc_exec($aconn, $query);
        $reg_date = "";
        $exp_date = "";
        $group_val = "";
        while (odbc_fetch_into($result, $cur)) {
            if (strlen(insertParadoxDate(substr($cur[7], 0, 10))) < 8) {
                $reg_date = "20010101";
            } else {
                $reg_date = insertParadoxDate(substr($cur[7], 0, 10));
            }
            if (strlen(insertParadoxDate(substr($cur[6], 0, 10))) < 8) {
                $exp_date = "99991231";
            } else {
                $exp_date = insertParadoxDate(substr($cur[6], 0, 10));
            }
            if (stripos(replaceString($cur[4], false), "GNL_STAFF") !== false) {
                $group_val = "E - GNL STAFF";
            } else {
                if (stripos(replaceString($cur[4], false), "STAFF") !== false) {
                    $group_val = "A - LNL STAFF";
                } else {
                    if (stripos(replaceString($cur[4], false), "IK_LNL_EXPARTRIATE") !== false) {
                        $group_val = "H - LNL EXPATRIATE";
                    } else {
                        if (stripos(replaceString($cur[4], false), "IK_GNL_EXPARTRIATE") !== false) {
                            $group_val = "H - GNL EXPATRIATE";
                        } else {
                            if (stripos(replaceString($cur[4], false), "IK_GNL_FIXED") !== false) {
                                $group_val = "I - GNL CONTRACT";
                            } else {
                                if (stripos(replaceString($cur[4], false), "FIXED") !== false) {
                                    $group_val = "C - LNL CONTRACT";
                                }
                            }
                        }
                    }
                }
            }
            if (stripos(replaceString($cur[4], false), "CASUAL") !== false && stripos(replaceString($cur[4], false), "LNL") !== false) {
                if (90 < getTotalDays(displayDate($reg_date), displayToday())) {
                    $group_val = "D - LNL OLD CASUAL";
                } else {
                    $group_val = "B - LNL NEW CASUAL";
                }
            }
            if (stripos(replaceString($cur[4], false), "CASUAL") !== false && stripos(replaceString($cur[4], false), "GNL") !== false) {
                if (90 < getTotalDays(displayDate($reg_date), displayToday())) {
                    $group_val = "F - GNL OLD CASUAL";
                } else {
                    $group_val = "G - GNL NEW CASUAL";
                }
            }
            $query = "SELECT id FROM tuser WHERE id = '" . $cur[0] / 1000000000 . "'";
            $sub_result = selectData($conn, $query);
            if ($sub_result[0] == $cur[0] / 1000000000) {
                if ($exp_date < insertToday()) {
                    $query = "UPDATE tuser SET name = '" . replaceString($cur[1], false) . "', dept = '" . replaceString($cur[2], false) . "', company = '";
                    if (stripos(replaceString($group_val, false), "GNL") !== false) {
                        $query .= "GODREJ NIGERIA LIMITED";
                    } else {
                        if (stripos(replaceString($group_val, false), "LNL") !== false) {
                            $query .= "LORNA NIGERIA LIMITED";
                        }
                    }
                    $query .= "', idno = '" . $group_val . "', remark = '" . replaceString($cur[3], false) . "', phone = '" . replaceString($cur[5], false) . "', datelimit = 'Y" . $reg_date . "" . $exp_date . "', ";
                    if ($cur[9] == "0000") {
                        $query = $query . " PassiveType = 'TRM' ";
                    } else {
                        if ($cur[9] == "1111") {
                            $query = $query . " PassiveType = 'RSN' ";
                        } else {
                            if ($cur[9] == "2222") {
                                $query = $query . " PassiveType = 'RTD' ";
                            } else {
                                if ($cur[9] == "3333") {
                                    $query = $query . " PassiveType = 'PRM' ";
                                }
                            }
                        }
                    }
                    $query = $query . " WHERE id = '" . $cur[0] / 1000000000 . "'";
                } else {
                    $query = "UPDATE tuser SET name = '" . replaceString($cur[1], false) . "', dept = '" . replaceString($cur[2], false) . "', company = '";
                    if (stripos(replaceString($group_val, false), "GNL") !== false) {
                        $query .= "GODREJ NIGERIA LIMITED";
                    } else {
                        if (stripos(replaceString($group_val, false), "LNL") !== false) {
                            $query .= "LORNA NIGERIA LIMITED";
                        }
                    }
                    $query .= "', idno = '" . $group_val . "', remark = '" . replaceString($cur[3], false) . "', phone = '" . replaceString($cur[5], false) . "', datelimit = 'N" . $reg_date . "" . $exp_date . "', pwd = '" . replaceString($cur[9], false) . "', PassiveType = 'ACT' ";
                    $query = $query . " WHERE id = '" . $cur[0] / 1000000000 . "'";
                }
                updateIData($iconn, $query, true);
            } else {
                $query = "INSERT INTO tuser (id, name, dept, company, idno, remark, datelimit, reg_date, pwd, phone) VALUES ('" . $cur[0] / 1000000000 . "', '" . replaceString($cur[1], false) . "', '" . replaceString($cur[2], false) . "', '";
                if (stripos(replaceString($group_val, false), "GNL") !== false) {
                    $query .= "GODREJ NIGERIA LIMITED";
                } else {
                    if (stripos(replaceString($group_val, false), "LNL") !== false) {
                        $query .= "LORNA NIGERIA LIMITED";
                    }
                }
                $query .= "', '" . $group_val . "', '" . replaceString($cur[3], false) . "', 'N" . $reg_date . "" . $exp_date . "', '" . insertParadoxDate(substr($cur[7], 0, 10)) . "0100', '" . replaceString($cur[9], false) . "', '" . replaceString($cur[5], false) . "' ";
                $query = $query . ")";
                updateIData($iconn, $query, true);
            }
            $query = "INSERT INTO EmployeeFlag (EmployeeID) VALUES ('" . $cur[0] / 1000000000 . "')";
            updateIData($iconn, $query, true);
        }
        $query = "SELECT id FROM tuser ORDER BY id";
        $result = mysqli_query($conn, $query);
        while ($cur = mysqli_fetch_row($result)) {
            $count = 0;
            $e_id = nitgenCode($cur[0]);
            $query = "SELECT userid FROM NGAC_USERINFO WHERE userid = '" . $e_id . "'";
            for ($sub_result = odbc_exec($aconn, $query); odbc_fetch_into($sub_result, $sub_cur); $count++) {
            }
            if ($count == 0) {
                $query = "UPDATE tuser SET datelimit = 'Y2001010120010101', PassiveType = 'RSN' WHERE id = '" . $cur[0] . "'";
                updateIData($iconn, $query, true);
            }
        }
        $query = "DELETE FROM tgate";
        updateIData($iconn, $query, true);
        $query = "SELECT NGAC_TERMINAL.nodeid, NGAC_TERMINAL.nodename FROM NGAC_TERMINAL WHERE NGAC_TERMINAL.nodename NOT LIKE '' ORDER BY NGAC_TERMINAL.nodeid";
        $result = odbc_exec($aconn, $query);
        while (odbc_fetch_into($result, $cur)) {
            $query = "INSERT INTO tgate (id, name) VALUES ('" . $cur[0] . "', '" . replaceString($cur[1], false) . "')";
            updateIData($iconn, $query, true);
        }
        $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('AA Migrate', " . insertToday() . ", '" . getNow() . "')";
        updateIData($iconn, $query, true);
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