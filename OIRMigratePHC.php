<?php


error_reporting(E_ALL);
date_default_timezone_set("Africa/Algiers");
include "Functions.php";
set_time_limit(0);
$conn = openIConnection();
$iconn = openIConnection();
$query = "";
$oconn = "";
$unis_conn = "";
$result = "";
$cur = "";
$mc = "";
$mac = getMAC();
$mc = "AC-72-89-23-ED-44";
$query = "SELECT DBIP, DBName, DBUser, DBPass, EmployeeCodeLength, LockDate FROM OtherSettingMaster";
$main_result = selectData($conn, $query);
$txtDBIP = $main_result[0];
$txtDBName = $main_result[1];
$txtDBUser = $main_result[2];
$txtDBPass = $main_result[3];
$txtECodeLength = $main_result[4];
$txtLockDate = $main_result[5];
$oconn = oracle_connection("10.10.10.5", "iepcl", "truck", "truckwb12");
echo "Connected to Oracle:" . $oconn;
$query = "SELECT C_Date, C_Time, L_TID, L_UID, L_Mode FROM UNIS.tenter WHERE C_Date > " . $txtLockDate;
$result = mysqli_query($conn, $query);
while ($cur = mysqli_fetch_row($result)) {
    $query = "INSERT INTO epcl_phc_attendance (TR_DATE, EMPL_CODE, ACC_CODE) VALUES ('" . displayUSDate($cur[0]) . " " . displayVirdiTime($cur[1]) . "', '" . $cur[3] . "', '" . $cur[4] . "')";
    $res = ociParse($oconn, $query);
    ociExecute($res);
    ociCommit($oconn);
}
function checkIndoramaMAC()
{
    $retn = false;
    $mac = getMAC();
    for ($i = 0; $i < count($mac); $i++) {
        if (substr($mac[$i], 0, 17) == $data || substr($mac[$i], 0, 17) == "00-1D-72-0B-D7-F4") {
            $retn = true;
            break;
        }
        if (substr($mac[$i], 0, 17) == "00-1B-11-05-21-0A") {
            $retn = true;
            break;
        }
        if (substr($mac[$i], 0, 17) == "00-1B-11-05-2D-1F") {
            $retn = true;
            break;
        }
        if (substr($mac[$i], 0, 17) == "44-37-E6-E9-22-C2") {
            $retn = true;
            break;
        }
    }
    return $retn;
}

?>