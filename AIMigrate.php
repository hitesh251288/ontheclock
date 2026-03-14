<?php


error_reporting(E_ALL);
date_default_timezone_set("Africa/Algiers");
include "Functions.php";
set_time_limit(0);
$unis_conn = odbc_connection("", "UNIS", encryptDecrypt("`X6:M168%"), encryptDecrypt("|bfedygj"));
if ($unis_conn == "") {
    print "Connection to UNIS Database NOT available. Process Terminated.";
    exit;
}
echo "Connected to UNIS Database: " . $unis_conn;
$query = "SELECT COUNT(L_ID) FROM tTerminal";
$result = selectData($unis_conn, $query);
if (1 < $result[0]) {
    print "Invalid Terminal Error. Process Terminated.";
    exit;
}
$mac_flag = false;
$mac = getMAC();
if (getRegister($mac, 7) == "8") {
    $mac_flag = true;
}
if ($mac_flag == false) {
    print "Un Registered Application. Process Terminated.";
    exit;
}
$oconn = odbc_connection("", "UNIS_ORACLE", "", "");
if ($oconn == "") {
    print "Connection to Oracle Database NOT available. Process Terminated.";
    exit;
}
if ($oconn != "") {
    echo "Connected to Oracle Database: " . $oconn;
    $query = "SELECT MAX(tr_date) FROM INTO EPCL_LOS_ATTENDANCE";
    $last_result = oci_parse($oconn, $query);
    oci_execute($last_result);
    echo "\n\r" . $query;
    if ($last_cur[0] == "") {
        $last_cur[0] = "01/01/2001 00:00:01";
    }
    echo "Last Record Time Stamp: " . $last_cur[0];
    $counter = 0;
    $query = "SELECT tEnter.C_Date, tEnter.C_Time, tEnter.L_UID, L_Mode, ed FROM tEnter WHERE tEnter.C_Date >= " . insertDate(substr($last_cur[0]), 0, 10) . " AND tEnter.C_Time > " . insertTime(substr($last_cur[0], 11, 8));
    for ($result = odbc_exec($unis_conn, $query); odbc_fetch_into($result, $cur); $counter++) {
        if ($cur[3] == "1") {
            $cur[3] == "1";
        } else {
            if ($cur[3] == "5") {
                $cur[3] == "3";
            } else {
                $cur[3] == "5";
            }
        }
        $query = "INSERT INTO EPCL_LOS_ATTENDANCE (tr_no, tr_date, empl_code, acc_code) VALUES ('" . $cur[4] . "', To_Date('" . displayDate($cur[0]) . " " . displayVirdiTime($cur[1]) . "', 'DD/MM/YYYY HH:MI:SS'), '" . $cur[2] . "', '" . $cur[3] . "')";
        $res = ociParse($oconn, $query);
        ociExecute($res);
    }
    ociCommit($oconn);
    echo "Migrated Records: " . $counter;
} else {
    print "Process Terminated. Connection to Oracle Database NOT found: " . $oconn;
}

?>