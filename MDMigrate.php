<?php


ob_start("ob_gzhandler");
error_reporting(E_ALL);
date_default_timezone_set("Africa/Algiers");
set_time_limit(0);
include "Functions.php";
$conn = openConnection();
$iconn = openIConnection();
if (checkMAC($conn) == true) {
    $query = "SELECT LockDate, MACAddress, DBType, DBIP, DBName, DBUser, DBPass, EmployeeCodeLength FROM OtherSettingMaster";
    $main_result = selectData($conn, $query);
    $txtLockDate = $main_result[0];
    $txtMACAddress = $main_result[1];
    $lstDBType = $main_result[2];
    $txtDBIP = $main_result[3];
    $txtDBName = $main_result[4];
    $txtDBUser = $main_result[5];
    $txtDBPass = $main_result[6];
    $txtECodeLength = $main_result[7];
    $txtDBIP = $txtDBIP . ",1433\\SQLEXPRESS";
    $oconn = mssql_connection($txtDBIP, $txtDBName, $txtDBUser, $txtDBPass);
    if ($oconn != "") {
        echo "\n\rConnected to MSSQL: " . $oconn;
        $counter = 0;
        $query = "TRUNCATE TABLE Dabur_Daily_Attendance_Nigeria";
        if (mssql_query($query, $oconn)) {
            $query = "SELECT e_id, TDate, Start, Close FROM DayMaster WHERE TDate > " . getLastDay($txtLockDate, 45) . " AND TDate < " . $txtLockDate;
            $result = mysqli_query($iconn, $query);
            while ($cur = mysqli_fetch_row($result)) {
                $query = "INSERT INTO Dabur_Daily_Attendance_Nigeria (EMP_CODE, OFFICE_DATE, TIME_IN, TIME_OUT, LOCATION) VALUES ('" . $cur[0] . "', '" . displayParadoxDate($cur[1]) . " 00:00:00', '" . displayParadoxDate($cur[1]) . " " . displayVirdiTime($cur[2]) . ".000', '" . displayParadoxDate($cur[1]) . " " . displayVirdiTime($cur[3]) . ".000', 'NIG')";
                if (mssql_query($query, $oconn)) {
                    $counter++;
                } else {
                    echo "\n\rEcho in Query: " . $query;
                }
            }
        }
        $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('Dabur Migration: Records Inserted - " . $counter . "', " . insertToday() . ", '" . getNow() . "')";
        updateIData($iconn, $query, true);
        mssql_close($oconn);
        return 1;
    }
    echo "\n\rUnable to connect to MSSQL Access Log Database";
}

?>