<?php


error_reporting(E_ERROR);
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
if ($txtLockDate < getLastDay(insertToday(), 15)) {
    $txtLockDate = getLastDay(insertToday(), 15);
    $query = "UPDATE OtherSettingMaster SET LockDate = '" . $txtLockDate . "'";
    updateIData($iconn, $query, true);
}
$oconn = oracle_connection("10.10.10.5", "iepcl", "truck", "truckwb12");
echo "Connected to Oracle:" . $oconn;
echo "\n\rLock Date: " . displayDate($txtLockDate);
$count = 0;
$query = "SELECT tuser.id, tuser.name, tuser.phone, tuser.dept, tuser.company, tuser.remark, tenter.e_date, tenter.e_time, tenter.e_mode, tenter.g_id, tgate.name FROM Access.tuser, Access.tenter, Access.tgate WHERE tuser.id = tenter.e_id AND tgate.id = tenter.g_id AND tenter.e_date > " . $txtLockDate;
$result = mysqli_query($conn, $query);
while ($cur = mysqli_fetch_row($result)) {
    $query = "INSERT INTO apps.epcl_phc_attendance (LOC_CODE, TR_DATE, ACCESS_TIME, EMPL_CODE, EMP_CODE, EMP_NAME, EMP_DEPT, BRANCH, Contractor, ACC_CODE, TR_NO, REMOTENO) VALUES ('PHC', To_Date('" . $cur[6] . " " . displayVirdiTime($cur[7]) . "', 'YYYYMMDD HH24:MI:SS'), To_Date('" . $cur[6] . " " . displayVirdiTime($cur[7]) . "', 'YYYYMMDD HH24:MI:SS'), '" . $cur[0] . "', '" . $cur[2] . "', '" . replaceString($cur[1], false) . "', '" . replaceString($cur[3], false) . "', '" . replaceString($cur[4], false) . "', '" . replaceString($cur[5], false) . "', ";die;
//    echo $query = "INSERT INTO epcl_phc_attendance (LOC_CODE, TR_DATE, ACCESS_TIME, EMPL_CODE, EMP_CODE, EMP_NAME, EMP_DEPT, BRANCH, Contractor, ACC_CODE, TR_NO, REMOTENO) VALUES ('PHC', To_Date('" . $cur[6] . " " . displayVirdiTime($cur[7]) . "', 'YYYYMMDD HH24:MI:SS'), To_Date('" . $cur[6] . " " . displayVirdiTime($cur[7]) . "', 'YYYYMMDD HH24:MI:SS'), '" . $cur[0] . "', '" . $cur[2] . "', '" . replaceString($cur[1], false) . "', '" . replaceString($cur[3], false) . "', '" . replaceString($cur[4], false) . "', '" . replaceString($cur[5], false) . "', ";
    if (stripos($cur[10], "(IN)") !== false) {
        $query .= "'2', ";
    } else {
        if (stripos($cur[10], "(OUT)") !== false) {
            $query .= "'4', ";
        } else {
            if ($cur[8] == 4) {
                $cur[8] = 3;
            }
            $query .= "'" . $cur[8] . "', ";
        }
    }
    $query .= "'" . $cur[9] . "', '" . replaceString($cur[10], false) . "')";
    $res = ociParse($oconn, $query);
    if (ociExecute($res)) {
        $count++;
    }
}
$query = "UPDATE tuser SET PassiveType = 'RSN', datelimit = CONCAT('Y', SUBSTRING(datelimit, 2, 16)) WHERE PassiveType = 'ACT' AND SUBSTRING(datelimit, 10, 8) <> SUBSTRING(datelimit, 2, 8) AND SUBSTRING(datelimit, 10, 8) <= " . insertToday();
updateIData($iconn, $query, true);
$query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('DB Migrate - Migration Period: " . displayDate($txtLockDate) . " - " . displayToday() . ", Records: " . $count . "', " . insertToday() . ", '" . getNow() . "')";
updateIData($iconn, $query, true);
echo "\n\rRecords Inserted: " . $count;
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