<?php


ob_start("ob_gzhandler");
error_reporting(E_ALL);
date_default_timezone_set("Africa/Algiers");
set_time_limit(0);
include "Functions.php";
$conn = openConnection();
$iconn = openIConnection();
if (checkMAC($conn) == true) {
    $query = "SELECT DBType, DBIP, DBName, DBUser, DBPass, LockDate FROM OtherSettingMaster";
    $main_result = selectData($conn, $query);
    if ($main_result[0] == "MSSQL") {
        $mconn = mssql_connection("192.168.136.44\\niglagho", "HODatabase", "compusoft", "compusoft");
        echo "\n\rConnected to MSSQL: " . $mconn;
        if (3 < strlen($mconn)) {
            $query = "SELECT MAX(AttendanceID) FROM [FRIGOGLASS  INDUSTRIES LIMITED\$AttendanceMaster]";
            $result = mssql_query($query, $mconn);
            $last_cur = mssql_fetch_row($result);
            if ($last_cur[0] == "") {
                $last_cur[0] = "0";
            }
            echo "\n\r Migration Start Record: " . $last_cur[0];
            $query = "SELECT MAX(AttendanceID) FROM AttendanceMaster";
            $end_result = selectData($conn, $query);
            if ($end_result[0] == "") {
                $end_result[0] = "0";
            }
            echo "\n\r Migration End Record: " . $end_result[0];
            $counter = 0;
            $query = "SELECT AttendanceMaster.AttendanceID, AttendanceMaster.EmployeeID, AttendanceMaster.EmpID, AttendanceMaster.group_id, AttendanceMaster.group_min, AttendanceMaster.ADate, AttendanceMaster.Week, AttendanceMaster.EarlyIn, AttendanceMaster.LateIn, AttendanceMaster.Break, AttendanceMaster.LessBreak, AttendanceMaster.MoreBreak, AttendanceMaster.EarlyOut, AttendanceMaster.LateOut, AttendanceMaster.Normal, AttendanceMaster.Grace, AttendanceMaster.Overtime, AttendanceMaster.AOvertime, AttendanceMaster.Day, AttendanceMaster.Flag, AttendanceMaster.p_flag, 0, 0, 0, AttendanceMaster.OT1, AttendanceMaster.OT2, AttendanceMaster.NightFlag, AttendanceMaster.RotateFlag, AttendanceMaster.Remark, AttendanceMaster.PHF, tuser.name, tuser.dept, tuser.remark FROM AttendanceMaster, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND AttendanceID <= " . $end_result[0] . " AND AttendanceID > " . $last_cur[0];
            $result = mysqli_query($conn, $query);
            while ($cur = mysqli_fetch_row($result)) {
                $query = "INSERT INTO [FRIGOGLASS  INDUSTRIES LIMITED\$AttendanceMaster] (AttendanceID, EmployeeID, EmpID, group_id, group_min, ADate, Week, EarlyIn, LateIn, Breakk, LessBreak, MoreBreak, EarlyOut, LateOut, Normal, Grace, Overtime, AOvertime, Day, Flag, p_flag, LateIn_flag, EarlyOut_flag, MoreBreak_flag, OT1, OT2, NightFlag, RotateFlag, Remark, PHF, EName, EDept, EIDNo) VALUES ( '" . $cur[0] . "', ";
                for ($i = 1; $i <= 31; $i++) {
                    $query .= "'" . $cur[$i] . "', ";
                }
                $query .= "'" . $cur[32] . "') ";
                if (mssql_query($query, $mconn)) {
                    $counter++;
                }
            }
            echo "\n\r Total Records Migrated: " . $counter;
            $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('DB Migrate - Records Migrated: " . $counter . "', " . insertToday() . ", '" . getNow() . "')";
            updateIData($iconn, $query, true);
            mssql_close($mconn);
        } else {
            print "Process Terminated: MSSQL Server NOT found/ Authentication Denied";
        }
    } else {
        print "Process Terminated: Found use of INVALID DB Type";
    }
} else {
    print "Process Terminated: Un Registered Application OR Connection to External Database NOT found: " . $mconn;
}

?>