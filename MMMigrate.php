<?php


ob_start("ob_gzhandler");
error_reporting(E_ALL);
date_default_timezone_set("Africa/Algiers");
include "Functions.php";
$conn = openConnection();
$iconn = openIConnection();
$query = "SELECT DBIP, DBName, DBUser, DBPass, EmployeeCodeLength, MACAddress, LockDate FROM OtherSettingMaster";
$main_result = selectData($conn, $query);
$txtDBIP = $main_result[0];
$txtDBName = $main_result[1];
$txtDBUser = $main_result[2];
$txtDBPass = $main_result[3];
$txtECodeLength = $main_result[4];
$txtMAC = encryptDecrypt($main_result[5]);
$txtLockDate = $main_result[6];
$txtTo = $txtLockDate;
$counter = 0;
if (checkMAC($conn)) {
    $aconn = mssql_connection($txtDBIP, $txtDBName, $txtDBUser, $txtDBPass);
    echo "\n\rConnected to MS SQL Database " . $aconn;
    if ($aconn != "") {
        $query = "CREATE TABLE [MOUKA LIMITED LAGOS\$AttendanceMaster] (AttendanceID int default '0', EmployeeID int default '0' , EmpID varchar default NULL , group_id int default '0' , group_min int default '0' , ADate int default '0' , [Week] int default '0' , EarlyIn int default '0' , LateIn int default '0' , [Break] int default '0' , LessBreak int default '0' , MoreBreak int default '0' , EarlyOut int default '0' , LateOut int default '0' , Normal int default '0' , Grace int default '0' , Overtime int default '0' , AOvertime int default '0' , [Day] varchar( 50 ) default NULL , Flag varchar default NULL, p_flag int default '0', LateIn_flag int default '0', EarlyOut_flag int default '0', MoreBreak_flag int default '0', OT1 varchar( 50 ) default '0', OT2 varchar( 50 ) default NULL, NightFlag int default '0', RotateFlag int default '0',  Remark varchar( 50 ) default NULL, PHF int default '0', EarlyIn_flag int default '0')";
        mssql_query($query, $aconn);
        $query = "SELECT MAX(AttendanceID) FROM [MOUKA LIMITED LAGOS\$AttendanceMaster]";
        $result = mssql_query($query, $aconn);
        $cur = mssql_fetch_array($result);
        $txtFromID = $cur[0];
        if ($txtFromID == "NULL" || $txtFromID == "") {
            $txtFromID = 0;
        }
        $query = "SELECT AttendanceMaster.AttendanceID, AttendanceMaster.EmployeeID, tuser.F1, AttendanceMaster.group_id, AttendanceMaster.group_min, AttendanceMaster.ADate, AttendanceMaster.Week, AttendanceMaster.EarlyIn, AttendanceMaster.LateIn, AttendanceMaster.Break, AttendanceMaster.LessBreak, AttendanceMaster.MoreBreak, AttendanceMaster.EarlyOut, AttendanceMaster.LateOut, AttendanceMaster.Normal, AttendanceMaster.Grace, AttendanceMaster.Overtime, AttendanceMaster.AOvertime, AttendanceMaster.Day, AttendanceMaster.Flag, AttendanceMaster.p_flag, AttendanceMaster.LateIn_flag, AttendanceMaster.EarlyOut_flag, AttendanceMaster.MoreBreak_flag, AttendanceMaster.OT1, AttendanceMaster.OT2, AttendanceMaster.NightFlag, AttendanceMaster.RotateFlag, AttendanceMaster.Remark, AttendanceMaster.PHF, AttendanceMaster.EarlyIn_flag FROM AttendanceMaster, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND AttendanceID > " . $txtFromID . " AND ADate <= " . $txtTo;
        echo "\n\r" . $query;
        $result = mysqli_query($conn, $query);
        while ($cur = mysqli_fetch_row($result)) {
            $query = "INSERT INTO [MOUKA LIMITED LAGOS\$AttendanceMaster] (AttendanceID, EmployeeID, EmpID, group_id, group_min, ADate, [Week], EarlyIn, LateIn, [Break], LessBreak, MoreBreak, EarlyOut, LateOut, Normal, Grace, Overtime, AOvertime, [Day], Flag, p_flag, LateIn_flag, EarlyOut_flag, MoreBreak_flag, OT1, OT2, NightFlag, RotateFlag, Remark, PHF, EarlyIn_flag) VALUES ('" . $cur[0] . "', '" . $cur[1] . "', '" . $cur[2] . "', '" . $cur[3] . "', '" . $cur[4] . "', '" . $cur[5] . "', '" . $cur[6] . "', '" . round($cur[7] / 3600, 2) . "', '" . round($cur[8] / 3600, 2) . "', '" . round($cur[9] / 3600, 2) . "', '" . round($cur[10] / 3600, 2) . "', '" . round($cur[11] / 3600, 2) . "', '" . round($cur[12] / 3600, 2) . "', '" . round($cur[13] / 3600, 2) . "', '" . round($cur[14] / 3600, 2) . "', '" . round($cur[15] / 3600, 2) . "', '" . round($cur[16] / 3600, 2) . "', '" . round($cur[17] / 3600, 2) . "', '" . $cur[18] . "', '" . $cur[19] . "', '" . $cur[20] . "', '" . $cur[21] . "', '" . $cur[22] . "', '" . $cur[23] . "', '" . $cur[24] . "', '" . $cur[25] . "', '" . $cur[26] . "', '" . $cur[27] . "', '" . $cur[28] . "', '" . $cur[29] . "', '" . $cur[30] . "') ";
            if (mssql_query($query, $aconn)) {
                $counter++;
            } else {
                exit("MSSQL error: " . mssql_get_last_message() . "\n\rQuery - " . $query);
            }
        }
        if (0 < $counter) {
            $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('DB Migrate - Attendance Records Inserted:" . $counter . "', " . insertToday() . ", '" . getNow() . "')";
            updateIData($iconn, $query, true);
        }
    } else {
        print "Connection to External Database NOT available. Process Terminated.";
        $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('DB Migrate - Connection to External Database NOT available. Process Terminated.', " . insertToday() . ", '" . getNow() . "')";
        updateIData($iconn, $query, true);
        exit;
    }
} else {
    print "Un Registered Application. Process Terminated.";
    $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('DB Migrate - Un Registered Application. Process Terminated.', " . insertToday() . ", '" . getNow() . "')";
    updateIData($iconn, $query, true);
    exit;
}

?>