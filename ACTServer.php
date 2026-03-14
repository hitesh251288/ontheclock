pay<?php


ob_start("ob_gzhandler");
error_reporting(E_ALL);
date_default_timezone_set("Africa/Algiers");
include "Functions.php";
set_time_limit(0);
$conn = openConnection();
if (checkMAC($conn) == false) {
    print "Un Registered Application. Process Terminated.";
    exit;
}
$iconn = openIConnection();
$query = "SELECT LockDate, MACAddress, DBType, DBIP, DBName, DBUser, DBPass FROM OtherSettingMaster";
$main_result = selectData($conn, $query);
$txtMACAddress = $main_result[1];
$db = "";
if (getRegister($txtMACAddress, 7) == "65") {
    $db[0] = "abuja";
    $db[1] = "asaba";
    $db[2] = "esc";
    $db[3] = "ibadan";
    $db[4] = "ikoyi";
    $db[5] = "jos";
    $db[6] = "kaduna";
    $db[7] = "kano";
    $db[8] = "pg";
    $db[9] = "phc";
}
for ($i = 0; $i < count($db); $i++) {
    $query = "INSERT IGNORE INTO " . $db[$i] . ".tuser (SELECT * FROM Access.tuser WHERE group_id <> 10)";
    if (updateIData($iconn, $query, true)) {
        $query = "REPLACE INTO " . $db[$i] . ".tuser (SELECT * FROM Access.tuser WHERE group_id = 10)";
        if (updateIData($iconn, $query, true)) {
            $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('ACServer Synch - HQ --> " . $db[$i] . "', " . insertToday() . ", '" . getNow() . "')";
            if (!updateIData($iconn, $query, true)) {
                echo "\n\r" . $query;
            }
        } else {
            echo "\n\r" . $query;
        }
    } else {
        echo "\n\r" . $query;
    }
}
for ($i = 0; $i < count($db); $i++) {
    $query = "INSERT IGNORE INTO Access.tuser (SELECT * FROM " . $db[$i] . ".tuser WHERE group_id = 10)";
    if (updateIData($iconn, $query, true)) {
        $query = "REPLACE INTO Access.tuser (SELECT * FROM " . $db[$i] . ".tuser WHERE group_id <> 10)";
        if (updateIData($iconn, $query, true)) {
            $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('ACServer Synch - " . $db[$i] . " --> HQ', " . insertToday() . ", '" . getNow() . "')";
            if (!updateIData($iconn, $query, true)) {
                echo "\n\r" . $query;
            }
        } else {
            echo "\n\r" . $query;
        }
    } else {
        echo "\n\r" . $query;
    }
}
for ($i = 0; $i < count($db); $i++) {
    $query = "INSERT IGNORE INTO Access.tenter (e_date, e_time, g_id, e_id, e_group, e_etc, p_flag, e_uptime, e_upmode) (SELECT e_date, e_time, g_id, e_id, e_group, e_etc, p_flag, e_uptime, e_upmode FROM " . $db[$i] . ".tuser WHERE group_id = 10)";
    if (updateIData($iconn, $query, true)) {
        $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('ACServer Clocking Data Synch - " . $db[$i] . " --> HQ', " . insertToday() . ", '" . getNow() . "')";
        if (!updateIData($iconn, $query, true)) {
            echo "\n\r" . $query;
        }
    } else {
        echo "\n\r" . $query;
    }
}

?>