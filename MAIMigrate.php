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
    $txtLockDate = $main_result[5];
    $at_table = "African Steel Mills Ltd\$AttendanceMaster Structure";
    $dt_table = "African Steel Mills Ltd\$DayMaster Structure";
    $tu_table = "African Steel Mills Ltd\$tuser";
    $flag_table = "African Steel Mills Ltd\$Flag Title";
    echo "\n\r Record Migration End Date: " . displayDate($txtLockDate);
    if ($main_result[0] == "MSSQL") {
        $server = $main_result[1];
        $db = $main_result[2];
        $mconn = mssql_connection($server, ".[" . $db . "]", $main_result[3], $main_result[4]);
        if ($mconn != "") {
            echo "\n\rConnected to MSSQL [" . $server . ": " . $db . " with Username: " . $main_result[3] . "]: " . $mconn;
            $counter = 0;
            $query = "DELETE FROM [" . $db . "].[dbo].[" . $at_table . "] WHERE [ADate] > " . $txtLockDate . " AND [ADate] < " . insertToday();
            if (!mssql_query($query, $mconn)) {
                echo "MSSQL error: " . mssql_get_last_message() . "\n\rQuery - " . $query;
            }
            $query = "SELECT AttendanceMaster.AttendanceID, AttendanceMaster.EmployeeID, AttendanceMaster.EmpID, AttendanceMaster.group_id, AttendanceMaster.group_min, AttendanceMaster.ADate, AttendanceMaster.Week, AttendanceMaster.EarlyIn, AttendanceMaster.LateIn, AttendanceMaster.Break, AttendanceMaster.LessBreak, AttendanceMaster.MoreBreak, AttendanceMaster.EarlyOut, AttendanceMaster.LateOut, AttendanceMaster.Normal, AttendanceMaster.Grace, AttendanceMaster.Overtime, AttendanceMaster.AOvertime, AttendanceMaster.Day, AttendanceMaster.Flag, AttendanceMaster.p_flag, AttendanceMaster.OT1, AttendanceMaster.OT2 FROM AttendanceMaster WHERE AttendanceMaster.ADate > " . $txtLockDate . " AND AttendanceMaster.ADate < " . insertToday() . " ";
            $result = mysqli_query($conn, $query);
            while ($cur = mysqli_fetch_row($result)) {
                $query = "INSERT INTO [" . $db . "].[dbo].[" . $at_table . "] (AttendanceID, EmployeeID, EmpID, group_id, group_min, ADate, [Week], EarlyIn, LateIn, [Break], LessBreak, MoreBreak, EarlyOut, LateOut, Normal, Grace, Overtime, AOvertime, Day, Flag, p_flag, OT1, OT2, NightFlag, RotateFlag, Remark,\tPHF, EarlyIn_flag, LateInColumn, LateIn_flag, EarlyOut_flag, MoreBreak_flag) VALUES ('" . $cur[0] . "', '" . $cur[1] . "',  '" . $cur[2] . "',  '" . $cur[3] . "',  '" . $cur[4] . "', '" . $cur[5] . "', '" . $cur[6] . "', '" . $cur[7] . "', '" . $cur[8] . "', '" . $cur[9] . "', '" . $cur[10] . "', '" . $cur[11] . "', '" . $cur[12] . "', '" . $cur[13] . "', '" . $cur[14] . "', '" . $cur[15] . "', '" . $cur[16] . "', '" . $cur[17] . "', '" . $cur[18] . "', '" . $cur[19] . "', '" . $cur[20] . "', '" . $cur[21] . "', '" . $cur[22] . "', '0', '0', '0', '0', '0', '0', '0', '0', '0')";
                if (mssql_query($query, $mconn)) {
                    $counter++;
                } else {
                    echo "MSSQL error: " . mssql_get_last_message() . "\n\rQuery - " . $query;
                }
            }
            $query = "DELETE FROM [" . $db . "].[dbo].[" . $dt_table . "] WHERE [TDate] > " . $txtLockDate . " AND [TDate] < " . insertToday();
            if (!mssql_query($query, $mconn)) {
                echo "MSSQL error: " . mssql_get_last_message() . "\n\rQuery - " . $query;
            }
            $query = "SELECT DayMasterID, e_id, TDate, Entry, Start, BreakOut, BreakIn, Close, Exit, p_flag, group_id, Flag, Work FROM DayMaster WHERE DayMaster.TDate > " . $txtLockDate . " AND DayMaster.TDate < " . insertToday();
            $result = mysqli_query($conn, $query);
            while ($cur = mysqli_fetch_row($result)) {
                $query = "INSERT INTO [" . $db . "].[dbo].[" . $dt_table . "] (DayMasterID, e_id, TDate, [Entry], [Start], BreakOut, BreakIn, [Close], [Exit], p_flag, group_id, Flag, Work) VALUES ('" . $cur[0] . "', '" . $cur[1] . "',  '" . $cur[2] . "',  '" . $cur[3] . "',  '" . $cur[4] . "', '" . $cur[5] . "', '" . $cur[6] . "', '" . $cur[7] . "', '" . $cur[8] . "', '" . $cur[9] . "', '" . $cur[10] . "', '" . $cur[11] . "', '" . $cur[12] . "')";
                if (!mssql_query($query, $mconn)) {
                    echo "MSSQL error: " . mssql_get_last_message() . "\n\rQuery - " . $query;
                }
            }
            echo "\n\r Total Attendance Records Migrated: " . $counter;
            $count = 0;
            $query = "DELETE FROM [" . $db . "].[dbo].[" . $tu_table . "] ";
            if (!mssql_query($query, $mconn)) {
                echo "MSSQL error: " . mssql_get_last_message() . "\n\rQuery - " . $query;
            }
            $query = "SELECT id, name, dept, company FROM tuser";
            $result = mysqli_query($conn, $query);
            while ($cur = mysqli_fetch_row($result)) {
                $query = "INSERT INTO [" . $db . "].[dbo].[" . $tu_table . "] (ID, Name, Dept, company) VALUES ('" . $cur[0] . "', '" . $cur[1] . "',  '" . $cur[2] . "', '" . $cur[3] . "')";
                if (mssql_query($query, $mconn)) {
                    $counter++;
                } else {
                    echo "MSSQL error: " . mssql_get_last_message() . "\n\rQuery - " . $query;
                }
            }
            echo "\n\r Total User Records Migrated: " . $count;
            $count = 0;
            $query = "DELETE FROM [" . $db . "].[dbo].[" . $flag_table . "] ";
            if (!mssql_query($query, $mconn)) {
                echo "MSSQL error: " . mssql_get_last_message() . "\n\rQuery - " . $query;
            }
            $query = "SELECT Flag, Title FROM FlagTitle";
            $result = mysqli_query($conn, $query);
            while ($cur = mysqli_fetch_row($result)) {
                $query = "INSERT INTO [" . $db . "].[dbo].[" . $flag_table . "] (Flag, Title) VALUES ('" . $cur[0] . "', '" . $cur[1] . "')";
                if (mssql_query($query, $mconn)) {
                    $counter++;
                } else {
                    echo "MSSQL error: " . mssql_get_last_message() . "\n\rQuery - " . $query;
                }
            }
            echo "\n\r Total Flag Records Migrated: " . $count;
        } else {
            echo "\n\r Could NOT Connect to MSSQL [" . $server . ": " . $db . " with Username: " . $main_result[3] . "]";
        }
        $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('DB Migrate - Attendance Records Inserted:" . $counter . "', " . insertToday() . ", '" . getNow() . "')";
        updateIData($iconn, $query, true);
        mysqli_close($conn);
        mysqli_close($iconn);
    } else {
        print "Process Terminated: Found use of INVALID DB Type";
    }
} else {
    print "Process Terminated: Un Registered Application OR Connection to External Database NOT found: " . $mconn;
}

?>