<?php


ob_start("ob_gzhandler");
error_reporting(E_ALL);
date_default_timezone_set("Africa/Algiers");
include "Functions.php";
$conn = openConnection();
$iconn = openIConnection();
$jconn = openIConnection();
$query = "SELECT DBIP, DBName, DBUser, DBPass, EmployeeCodeLength, LockDate FROM OtherSettingMaster";
$main_result = selectData($conn, $query);
$txtDBIP = $main_result[0];
$txtDBName = $main_result[1];
$txtDBUser = $main_result[2];
$txtDBPass = $main_result[3];
$txtECodeLength = $main_result[4];
$txtLockDate = $main_result[5];
if (checkMAC($conn)) {
    $aconn = odbc_connection("", "ANVIZ", "admin", "Katco-19701127");
    echo "\n\rConnected " . $aconn;
    if ($aconn != "") {
        $query = "SELECT tblLog.LogTime, tblLog.ID, tblLog.Machine FROM tblLog WHERE tblLog.LogTime > #" . displayUSDate($txtLockDate) . " 00:00:00# ORDER BY tblLog.ID";
        echo "\n\r" . $query;
        $result = odbc_exec($aconn, $query);
        while (odbc_fetch_into($result, $cur)) {
            $query = "INSERT INTO tenter (e_date, e_time, g_id, e_id, e_group) VALUES ('" . insertParadoxDate(substr($cur[0], 0, 10)) . "', '" . insertTime(substr($cur[0], 11, 8)) . "', '" . $cur[2] . "', '" . $cur[1] . "', '419')";
            if (!updateIData($iconn, $query, true)) {
            }
        }
        $query = "UPDATE tenter, tuser SET tenter.e_group = tuser.group_id WHERE tenter.e_group = '419' AND tenter.e_id = tuser.id AND tuser.group_id > 2";
        if (updateIData($iconn, $query, true)) {
        }
        $query = "SELECT ID, Name FROM tblMachine";
        $result = odbc_exec($aconn, $query);
        while (odbc_fetch_into($result, $cur)) {
            $query = "INSERT INTO tgate (id, name) VALUES ('" . $cur[0] . "', '" . replaceString($cur[1], false) . "')";
            updateIData($iconn, $query, true);
        }
        $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('AIMP Migrate', " . insertToday() . ", '" . getNow() . "')";
        updateIData($iconn, $query, true);
    } else {
        print "Connection to External Database NOT available. Process Terminated.";
        exit;
    }
} else {
    print "Un Registered Application. Process Terminated.";
    exit;
}

?>