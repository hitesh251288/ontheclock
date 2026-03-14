<?php


ob_start("ob_gzhandler");
date_default_timezone_set("Africa/Algiers");
include "Functions.php";
$conn = openConnection();
$iconn = openIConnection();
$unis_conn = "";
$unis_conn = mysqli_connect("172.20.184.14", "root", "namaste", "UNIS");
if ($unis_conn == "") {
    $unis_conn = mysqli_connect("192.168.6.71", "root", "root", "UNIS");
}
$max_ed = 0;
$myDB = false;
if (checkMAC($conn) == false) {
    print "Un Registered Application. Process Terminated.";
    exit;
}
$query = "SELECT MACAddress, LockDate FROM OtherSettingMaster";
$result = selectData($conn, $query);
$txtMACAddress = $result[0];
$txtLockDate = $result[1];
if (getRegister($txtMACAddress, 7) == "84") {
    $query = "UPDATE tuser SET name = UPPER(name), dept = UPPER(dept), company = UPPER(company), remark = UPPER(remark), idno = UPPER(idno), phone = UPPER(phone)";
    updateIData($iconn, $query, true);
}
$query = "SELECT L_ID, C_Name, C_RegDate, L_OptDateLimit, C_DateLimit FROM tUser ORDER BY L_ID";
if ($unis_conn != "") {
    $result = mysqli_query($unis_conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
        if ($cur[4] == "") {
            $cur[4] = "2017010120170101";
        }
        if ($cur[3] == "") {
            $cur[3] = "0";
        }
        $reg_date = substr($cur[4], 0, 8);
        $exp_date = substr($cur[4], 8, 8);
        $query = "SELECT id, name FROM tuser WHERE id = '" . $cur[0] . "'";
        $sub_result = selectData($conn, $query);
        if ($sub_result[0] == $cur[0]) {
            if ($exp_date < insertToday() && $cur[3] == 1) {
                if ($sub_result[1] == "") {
                    $query = "UPDATE tuser SET name = '" . replaceString($cur[1], true) . "', reg_date = '" . substr($cur[2], 0, 10) . "', datelimit = 'Y" . $reg_date . "" . $exp_date . "' WHERE id = '" . $cur[0] . "'";
                } else {
                    $query = "UPDATE tuser SET reg_date = '" . substr($cur[2], 0, 10) . "', datelimit = 'Y" . $reg_date . "" . $exp_date . "' WHERE id = '" . $cur[0] . "'";
                }
            } else {
                if ($sub_result[1] == "") {
                    $query = "UPDATE tuser SET name = '" . replaceString($cur[1], true) . "', reg_date = '" . substr($cur[2], 0, 10) . "', datelimit = 'N" . $reg_date . "" . $exp_date . "' WHERE id = '" . $cur[0] . "'";
                } else {
                    $query = "UPDATE tuser SET reg_date = '" . substr($cur[2], 0, 10) . "', datelimit = 'N" . $reg_date . "" . $exp_date . "' WHERE id = '" . $cur[0] . "'";
                }
            }
        } else {
            $query = "INSERT INTO tuser (id, name, datelimit, reg_date, PassiveType) VALUES ('" . $cur[0] . "', '" . replaceString($cur[1], false) . "', 'N" . $reg_date . "" . $exp_date . "', '" . substr($cur[2], 0, 10) . "', 'ACT')";
        }
        if (updateIData($iconn, $query, true)) {
            $query = "INSERT INTO EmployeeFlag (EmployeeID) VALUES ('" . $cur[0] . "')";
            updateIData($iconn, $query, true);
        }
    }
}
$query = "SELECT C_Date, C_Time, L_TID, L_UID FROM tEnter WHERE L_UID > 0 AND C_Date > " . $txtLockDate;
$result = mysqli_query($unis_conn, $query);
while ($cur = mysqli_fetch_row($result)) {
    $query = "INSERT IGNORE INTO tenter (e_date, e_time, g_id, e_id, e_group) VALUES ('" . $cur[0] . "', '" . $cur[1] . "', '" . $cur[2] . "', '" . $cur[3] . "', '419')";
    if (!updateIData($iconn, $query, true)) {
    }
}
$query = "UPDATE tenter, tuser SET tenter.e_group = tuser.group_id WHERE tenter.e_group = '419' AND tenter.e_id = tuser.id AND tuser.group_id > 1";
updateIData($iconn, $query, true);
if ($myDB) {
    $query = "SELECT L_ID, C_Name FROM tTerminal WHERE C_Name NOT LIKE '' ORDER BY L_ID";
    $result = mysqli_query($unis_conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
        $query = "INSERT INTO tgate (id, name) VALUES ('" . $cur[0] . "', '" . replaceString($cur[1], false) . "')";
        if (!updateIData($iconn, $query, true)) {
            $query = "UPDATE tgate SET name = '" . replaceString($cur[1], false) . "' WHERE id = '" . $cur[0] . "'";
            updateIData($iconn, $query, true);
        }
    }
} else {
    $query = "SELECT L_ID, C_Name FROM tTerminal WHERE C_Name NOT LIKE '' ORDER BY L_ID";
    $result = odbc_exec($unis_conn, $query);
    while (odbc_fetch_into($result, $cur)) {
        $query = "INSERT INTO tgate (id, name) VALUES ('" . $cur[0] . "', '" . replaceString($cur[1], false) . "')";
        if (!updateIData($iconn, $query, true)) {
            $query = "UPDATE tgate SET name = '" . replaceString($cur[1], false) . "' WHERE id = '" . $cur[0] . "'";
            updateIData($iconn, $query, true);
        }
    }
}
$query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('UNIS Synch', " . insertToday() . ", '" . getNow() . "')";
updateIData($iconn, $query, true);

?>