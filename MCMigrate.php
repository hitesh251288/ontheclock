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
    echo "\n\r Record Migration End Date: " . displayDate($txtLockDate);
    if ($main_result[0] == "MSSQL") {
        $array = "";
        $array[0] = "";
        $array[1] = "CHI Limited\$Employee Read Only";
        $array[2] = "CHI Limited\$Attendance Register1";
        $array[3] = "CHI Limited\$Shift Register";
        $array[4] = "CHI";
        $array[5] = "192.168.1.77";
        $array[6] = "";
        $count = 0;
        $user_code = "";
        $user_table = "";
        $at_table = "";
        $shift_table = "";
        $db = "";
        $server = "";
        $counter = 0;
        $u_counter = 0;
        $i_counter = 0;
        $s_i_counter = 0;
        $s_u_counter = 0;
        for ($i = 0; $i < count($array); $i++) {
            if ($count == 0) {
                $user_code = $array[$i];
            } else {
                if ($count == 1) {
                    $user_table = $array[$i];
                } else {
                    if ($count == 2) {
                        $at_table = $array[$i];
                    } else {
                        if ($count == 3) {
                            $shift_table = $array[$i];
                        } else {
                            if ($count == 4) {
                                $db = $array[$i];
                            } else {
                                if ($count == 5) {
                                    $server = $array[$i];
                                } else {
                                    $count = 0 - 1;
                                    $mconn = mssql_connection("192.168.1.77", "[Demol Database NAV 2009R2]", "datacom", "12345");
                                    if ($mconn != "") {
                                        echo "\n\rConnected to MSSQL [" . $server . ": " . $db . " with Username: " . $main_result[3] . "]: " . $mconn;
                                        $counter = 0;
                                        $query = "DELETE FROM [" . $at_table . "] WHERE [Attend_ Date] > " . displayParadoxDate(getLastDay(insertToday(), 60)) . " ";
                                        if (mssql_query($query, $mconn)) {
                                            $query = "SELECT AttendanceMaster.AttendanceID, AttendanceMaster.EmployeeID, tuser.idno, AttendanceMaster.group_id, AttendanceMaster.ADate, AttendanceMaster.Overtime, AttendanceMaster.Day, AttendanceMaster.OT1, AttendanceMaster.OT2, DayMaster.Start, DayMaster.Close, tuser.phone, AttendanceMaster.Flag, AttendanceMaster.Normal FROM AttendanceMaster, DayMaster, tuser WHERE AttendanceMaster.ADate = DayMaster.TDate AND AttendanceMaster.EmployeeID = DayMaster.e_id AND AttendanceMaster.EmployeeID = tuser.id AND (AttendanceMaster.Flag = 'Black' OR AttendanceMaster.Flag = 'Proxy' OR AttendanceMaster.Flag = 'Purple') AND AttendanceMaster.ADate > " . getLastDay(insertToday(), 60);
                                            $result = mysqli_query($conn, $query);
                                            while ($cur = mysqli_fetch_row($result)) {
                                                $query = "INSERT INTO [" . $db . "].[dbo].[" . $at_table . "] ([Employee No_], [Attend_ Date], [Shift Code], [Time In], [Time Out], [Overtime Min_], [Hol_ Or Sunday Overtime Min_], [OT Minutes], [Company Name], Blocked, [Employee Role], [First Name], [Last Name], [Section], [Work Location], [Attendance Period], [Terminal], [Total Minutes]) VALUES ( '" . $cur[1] . "', '" . displayParadoxDate($cur[4]) . "', ";
                                                $query .= " '" . $cur[3] . "', ";
                                                $query .= " '" . displayParadoxDate($cur[4]) . " " . displayVirdiTime($cur[9]) . "', '" . displayParadoxDate($cur[4]) . " " . displayVirdiTime($cur[10]) . "',";
                                                if ($cur[6] == $cur[7]) {
                                                    $query .= " '0', '0', '" . $cur[5] / 60 . "', ";
                                                } else {
                                                    if ($cur[6] == $cur[8]) {
                                                        $query .= " '" . $cur[5] / 60 . "', '0', '0', ";
                                                    } else {
                                                        if ($cur[12] == "Purple") {
                                                            $query .= " '0', '" . $cur[5] / 60 . "', '0', ";
                                                        } else {
                                                            $query .= " '0', '0', '" . $cur[5] / 60 . "', ";
                                                        }
                                                    }
                                                }
                                                $query .= " '_', '" . $user_code . "', '_', '_', '_', '_', '_', '_', '_', '" . ($cur[5] + $cur[13]) / 60 . "') ";
                                                if (mssql_query($query, $mconn)) {
                                                    $counter++;
                                                } else {
                                                    exit("MSSQL error: " . mssql_get_last_message() . "\n\rQuery - " . $query);
                                                }
                                            }
                                            echo "\n\r Total Attendance Records Migrated: " . $counter;
                                        } else {
                                            exit("MSSQL error: " . mssql_get_last_message() . "\n\rQuery - " . $query);
                                        }
                                    } else {
                                        echo "\n\r Could NOT Connect to MSSQL [" . $server . ": " . $db . " with Username: " . $main_result[3] . "]";
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $count++;
        }
        $query = "UPDATE tuser SET dept = '.' WHERE dept IS NULL OR dept = '' ";
        updateIData($iconn, $query, true);
        $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('DB Migrate - User Records Updated:" . $u_counter . ", User Records Inserted:" . $i_counter . ", Shift Records Updated:" . $s_u_counter . ", Shift Records Inserted:" . $s_i_counter . ", Attendance Records Inserted:" . $counter . "', " . insertToday() . ", '" . getNow() . "')";
        updateIData($iconn, $query, true);
    } else {
        print "Process Terminated: Found use of INVALID DB Type";
    }
} else {
    print "Process Terminated: Un Registered Application OR Connection to External Database NOT found: " . $mconn;
}

?>