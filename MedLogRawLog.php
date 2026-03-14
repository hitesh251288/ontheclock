<?php

//ini_set('memory_limit', '5120M'); 
ob_start("ob_gzhandler");
error_reporting(0);
set_time_limit(0);
include "Functions.php";
$csv = $argv[1];
$file_name = "PayMaster-Attendance-" . insertToday() . "" . getNow() . ".csv";
$conn = openConnection();
$iconn = openIConnection();
$jconn = openIConnection();
$kconn = openIConnection();
$lconn = openIConnection();

$serverName = "medlog.database.windows.net,1433"; //serverName\instanceName
$connectionInfo = array("Database" => "medlog_att", "UID" => "sazure@medlog.database.windows.net", "PWD" => "gSf_W4St1");
$connectSQL = sqlsrv_connect($serverName, $connectionInfo);

if ($connectSQL) {
    echo "Connection established.<br />";
} else {
    echo "Connection could not be established.<br />";
    echo "<pre>";
    die(print_r(sqlsrv_errors(), true));
}
$query = "SELECT LockDate, MACAddress, DBType, DBIP, DBName, DBUser, DBPass FROM OtherSettingMaster";
$main_result = selectData($conn, $query);
$txtLockDate = $main_result[0];
$lstDBType = $main_result[2];
$txtDBIP = $main_result[3];
$txtDBName = $main_result[4];
$txtDBUser = $main_result[5];
$txtDBPass = $main_result[6];
$txtMACAddress = $main_result[1];

$dayMasterQuery = "SELECT tenter.ed, tenter.e_id, tenter.e_date, tenter.e_time, tenter.g_id, tuser.id, tuser.name, tuser.idno, tuser.f1, tuser.dept, tuser.company, tuser.remark, tuser.group_id, tgroup.name, tgate.name from tenter JOIN tuser ON tenter.e_id = tuser.id JOIN tgroup ON tuser.group_id = tgroup.id JOIN tgate ON tenter.g_id = tgate.id order by tenter.ed DESC LIMIT 5000";
//$dayMasterQuery = "SELECT tenter.ed, tenter.e_id, tenter.e_date, tenter.e_time, tenter.g_id, tuser.id, tuser.name, tuser.idno, tuser.f1, tuser.dept, tuser.company, tuser.remark, tuser.group_id, tgroup.name, tgate.name from tenter JOIN tuser ON tenter.e_id = tuser.id JOIN tgroup ON tuser.group_id = tgroup.id JOIN tgate ON tenter.g_id = tgate.id WHERE tenter.e_id=250129";
$i = 0;
$dayMasterResult = mysqli_query($conn, $dayMasterQuery);
while ($dayMasterRes = mysqli_fetch_array($dayMasterResult)) {
//     echo "<pre>";print_R($dayMasterRes);    
    $fetchQuery = "SELECT * from dbo.medlog_rawlog_test WHERE e_id=$dayMasterRes[1] AND Name='$dayMasterRes[6]' AND Tdate='$dayMasterRes[2]' AND Time='$dayMasterRes[3]'";
//    echo "<br>";
    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $stmt = sqlsrv_query($connectSQL, $fetchQuery, $params, $options);
    $row_count = sqlsrv_num_rows($stmt);
    if ($row_count == 0) {
        $sql = "INSERT INTO dbo.medlog_rawlog_test ( e_id, Genesis_id, Name, Emp_code, Dept, Div_Desg, Rmk, Tshift, Tdate, Time, Terminal) VALUES ( $dayMasterRes[1], '$dayMasterRes[8]', '$dayMasterRes[6]', '$dayMasterRes[7]', '$dayMasterRes[9]', '$dayMasterRes[10]', '$dayMasterRes[11]', '$dayMasterRes[13]', '$dayMasterRes[2]', '$dayMasterRes[3]', '$dayMasterRes[14]')";
//    echo "<br>";
        $stmt = sqlsrv_query($connectSQL, $sql);
    }
}
if ($stmt === false) {
    echo "<br>";
    echo "<h3>Opps! An Error occured from our end.</h3>";
    die(print_r(sqlsrv_errors(), true));
} else {
    echo "<br>";
    echo "<h3> Data Migrated Successfully</h3>";
}