<?php

ini_set('memory_limit', '5120M');
ob_start("ob_gzhandler");
error_reporting(0);
set_time_limit(0);
include "Functions.php";

//var_dump($argv);
$csv = $argv[1];
$file_name = "PayMaster-Attendance-" . insertToday() . "" . getNow() . ".csv";
$conn = openConnection();
$iconn = openIConnection();
$jconn = openIConnection();
$kconn = openIConnection();
$lconn = openIConnection();

$query = "SELECT LockDate, MACAddress, DBType, DBIP, DBName, DBUser, DBPass FROM OtherSettingMaster";
$main_result = selectData($conn, $query);
$txtLockDate = $main_result[0];
$lstDBType = $main_result[2];
$txtDBIP = $main_result[3];
$txtDBName = $main_result[4];
$txtDBUser = $main_result[5];
$txtDBPass = $main_result[6];
$txtMACAddress = $main_result[1];

$serverName = "DESKTOP-I1EBN6N\SQLEXPRESS"; //serverName\instanceName
$username = "sa";
$password = "bit@123";
$database = "Attendance";
// Connect to MSSQL
$oconn = mssql_connection($serverName, $database, $username, $password);
echo "\n\rConnected to MSSQL: " . $oconn;

$query = "SELECT e_id,e_date,e_time,ed FROM tenter";
$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_row($result)) {
    $lastID = $row[3];
    $empID = $row[0];
    $date = $row[1];
    $time = $row[2];
    $logtimestamp = date("Y-m-d H:i:s", strtotime($date . $time));
    $tenterData[] = array(
        'staffid' => $empID,
        'logtimestamp' => $logtimestamp,
        'ed' => $row[3]  
    );
}
//echo "<pre>";print_R($tenterData);
$mysqlLastId = $lastID;


$sqlQuery = "SELECT * from dbo.attendance";
$sqlResult = mssql_query($sqlQuery, $oconn);
if (mssql_num_rows($sqlResult) > 0) { 
    while ($sqlRow = mssql_fetch_array($sqlResult)) {
        $lastid = $sqlRow;
        $attendanceData[] = $sqlRow;
    }
   $lastInsertID = $lastid[1];

    if ($lastInsertID < $mysqlLastId) {
        $aQuery = "SELECT e_id,e_date,e_time,ed FROM tenter where ed > $lastInsertID";
        $aResult = mysqli_query($conn, $aQuery);
        while ($aRow = mysqli_fetch_row($aResult)) {
            $empID = $aRow[0];
            $date = $aRow[1];
            $time = $aRow[2];
            $ed = $aRow[3];
            $logtimestamp = date("Y-m-d H:i:s", strtotime($date . $time));
            $sql = "INSERT INTO dbo.attendance(ed, Staffid, logtimestamp) VALUES ($ed, '$empID', '$logtimestamp')";
            $sqlResult = mssql_query($sql, $oconn);
        }
        if ($sqlResult) {
            echo "<br>";
            echo "<h3> Data Inserted Successfully</h3>";
        } else {
            echo "<br>";
            echo "<h3> Data Not Inserted</h3>";
        }
    }
} else {
    for ($i = 0; $i < count($tenterData); $i++) {
        $sql = "INSERT INTO dbo.attendance(ed, Staffid, logtimestamp) VALUES (".$tenterData[$i]['ed'].", '".$tenterData[$i]['staffid']."', '".$tenterData[$i]['logtimestamp']."')";
        $sqlResult = mssql_query($sql, $oconn);
    }
    if ($sqlResult) {
            echo "<br>";
            echo "<h3> Data Inserted Successfully</h3>";
        } else {
            echo "<br>";
            echo "<h3> Data Not Inserted</h3>";
        }
}