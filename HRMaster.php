<?php

ob_start("ob_gzhandler");
error_reporting(E_ALL);
set_time_limit(0);
include "Functions.php";
$conn = openConnection();
$iconn = openIConnection();
$query = "SELECT LockDate, MACAddress, DBType, DBIP, DBName, DBUser, DBPass, EmployeeCodeLength FROM OtherSettingMaster";
$main_result = selectData($conn, $query);
$txtLockDate = $main_result[0];
$lstDBType = $main_result[2];
$txtDBIP = $main_result[3];
$txtDBName = $main_result[4];
$txtDBUser = $main_result[5];
$txtDBPass = $main_result[6];
$txtECodeLength = $main_result[7];
$txtMACAddress = $main_result[1];
$txtDBIP = $txtDBIP . ",1433\\SQLEXPRESS";
$co_code = 1;
if (checkMAC($conn) == false) {
    print "Un Registered Application. Process Terminated.";
    exit;
}
if (noTASoftware("", $txtMACAddress)) {
    print "Un Registered Application. Process Terminated.";
    exit;
}
$main_count = 1;
if (getRegister($txtMACAddress, 7) == "86") {
    $main_count = 3;
}
for ($iii = 0; $iii < $main_count; $iii++) {
    if (getRegister($txtMACAddress, 7) == "86") {
        if ($iii == 0) {
            $txtDBName = "GSM";
            $co_code = 1;
        } else {
            if ($iii == 1) {
                $txtDBName = "GPI";
                $co_code = 2;
            } else {
                if ($iii == 2) {
                    $txtDBName = "UMPI";
                    $co_code = 3;
                }
            }
        }
    } else {
        if (getRegister($txtMACAddress, 7) == "168") {
            $txtDBName = "HRM";
        } else {
            $txtDBName = "HRMS";
        }
    }
    $oconn = mssql_connection($txtDBIP, $txtDBName, $txtDBUser, $txtDBPass);
    if ($oconn == "") {
        $txtDBName = "HRM";
        $oconn = mssql_connection($txtDBIP, $txtDBName, $txtDBUser, $txtDBPass);
    }
    echo "\n\r Connected to MSSQL: " . $oconn;
//    echo $query = "SELECT LR.Leave_U_Id, lm.Leave_Id, e.Emp_Payroll_No, CONVERT(VARCHAR(20), ld.From_Date, 112), CONVERT(VARCHAR(20), ld.To_Date, 112), ld.Leave_Period, lm.Leave_Count_Holiday, lm.Leave_Count_Saturday, lm.Leave_Count_Sunday, lm.Leave_Count_Weekoff FROM [" . $txtDBName . "].[dbo].[tblleave_Request] LR INNER JOIN [" . $txtDBName . "].[dbo].[tblLeave_Request_Detail] ld on ld.Cmp_Id = LR.Cmp_Id and ld.Leave_Request_Id = LR.Leave_Request_Id INNER JOIN [" . $txtDBName . "].[dbo].[tblEmployee] e ON e.Cmp_Id = LR.cmp_Id and e.Emp_Id = LR.Emp_Id INNER JOIN [" . $txtDBName . "].[dbo].[tblLeave] lm on lm.Leave_Id = ld.Leave_Id WHERE LR.Leave_Request_Status  = 1 AND e.Is_Separate = 0 ";
    echo $query = "SELECT LR.LeaveRequest_Code, lm.Leave_Id, e.Emp_Payroll_No, CONVERT(VARCHAR(20), LR.FromDate, 112), CONVERT(VARCHAR(20), LR.ToDate, 112), ld.Leave_Period, lm.LeaveCountHoliday, lm.LeaveCountSaturday, lm.LeaveCountSunday, lm.LeaveCountWeekoff FROM [" . $txtDBName . "].[dbo].[tblLeaveRequest] LR INNER JOIN [" . $txtDBName . "].[dbo].[tblLeaveDateDetail] ld on ld.Cmp_Id = LR.Cmp_Id and ld.Leave_Request_Id = LR.Leave_Request_Id INNER JOIN [" . $txtDBName . "].[dbo].[tblEmployee] e ON e.Cmp_Id = LR.cmp_Id and e.Emp_Id = LR.Emp_Id INNER JOIN [" . $txtDBName . "].[dbo].[tblLeave] lm on lm.Leave_Id = ld.Leave_Id WHERE LR.Leave_Request_Status  = 1 AND e.Is_Separate = 0 ";
    $counter = 0;
    $result = mssql_query($query, $oconn);
    while ($cur = mssql_fetch_row($result)) {
        if ($txtLockDate < $cur[4]) {
            $flag = "";
            switch ($cur[1]) {
                case 1:
                    $flag = "Green";
                    break;
                case 2:
                    if (getRegister($txtMACAddress, 7) == "157") {
                        $flag = "Orange";
                    } else {
                        $flag = "Indigo";
                    }
                    break;
                case 3:
                    if (getRegister($txtMACAddress, 7) == "157") {
                        $flag = "Red";
                    } else {
                        $flag = "Blue";
                    }
                    break;
                case 4:
                    if (getRegister($txtMACAddress, 7) == "157") {
                        $flag = "Yellow";
                    } else {
                        $flag = "Violet";
                    }
                    break;
                case 5:
                    if (getRegister($txtMACAddress, 7) == "157") {
                        $flag = "Brown";
                    } else {
                        $flag = "Gray";
                    }
                    break;
                case 6:
                    if (getRegister($txtMACAddress, 7) == "157") {
                        $flag = "Green";
                    } else {
                        $flag = "Yellow";
                    }
                    break;
                case 7:
                    $flag = "Green";
                    break;
                case 8:
                    if (getRegister($txtMACAddress, 7) == "157") {
                        $flag = "Green";
                    } else {
                        $flag = "Yellow";
                    }
                    break;
                case 9:
                    $flag = "Black";
                    break;
                case 11:
                    $flag = "Violet";
                    break;
                case 12:
                    $flag = "Magenta";
                    break;
                case 13:
                    $flag = "Teal";
                    break;
                case 14:
                    $flag = "Indigo";
                    break;
                case 15:
                    $flag = "Green";
                    break;
                case 16:
                    $flag = "Green";
                    break;
                case 17:
                    $flag = "Green";
                    break;
                case 18:
                    $flag = "Green";
                    break;
                case 19:
                    $flag = "Green";
                    break;
            }
            $ii = $cur[3];
            while ($ii <= $cur[4]) {
                $ot_flag = false;
                $insert_flag = true;
                if ($cur[7] == 0 && getDay(displayDate($ii)) == "Saturday" || $cur[8] == 0 && getDay(displayDate($ii)) == "Sunday") {
                    $ot_flag = true;
                }
                $query = "SELECT OTDate FROM OTDate WHERE OTDate = " . $ii;
                $result_ = selectData($conn, $query);
                if (is_numeric($result_[0])) {
                    $ot_flag = true;
                }
                if ($ot_flag == false) {
                    $query = "SELECT AttendanceID FROM AttendanceMaster WHERE EmployeeID = " . $cur[2] . " AND ADate = " . $ii;
                    $result_ = selectData($conn, $query);
                    if (0 < $result_[0] || $result_[0] == "") {
                        $query = "INSERT INTO FlagDayRotation (e_id, e_date, g_id, Flag, Remark, group_id) VALUES (" . $cur[2] . ", " . $ii . ", 1, '" . $flag . "', 'HRMS', '2')";
                        if (updateIData($iconn, $query, true) == false) {
                            $query = "UPDATE FlagDayRotation SET Flag = '" . $flag . "', Remark = 'HRMS', group_id = '2' WHERE e_id = '" . $cur[2] . "' AND e_date = '" . $ii . "' AND RecStat = 0 ";
                            if (updateIData($iconn, $query, true) == false) {
                                $insert_flag = false;
                            }
                        }
                        if ($insert_flag) {
                            $text = "HRMS Pre Flagged ID: " . $cur[2] . " for Date: " . displayDate($ii) . " with Flag: " . $flag . ", Shift: OFF";
                            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", 'admin', '" . $text . "')";
                            updateIData($iconn, $query, true);
                        }
                    }
                }
                $ii = getNextDay($ii, 1);
            }
        }
    }
    if (getRegister($txtMACAddress, 7) == "157" || getRegister($txtMACAddress, 7) == "44") {
        $count = 0;
        $query = "DELETE FROM tblEmployee_Weekoff_Datewise WHERE CONVERT(VARCHAR(20), EMP_WeekOffDate, 112) >= " . $txtLockDate;
        if (mssql_query($query, $oconn)) {
            $query = "SELECT MAX(Emp_WeekOff_Id) FROM tblEmployee_Weekoff_Datewise";
            $e_result = mssql_query($query, $oconn);
            $e_cur = mssql_fetch_row($e_result);
            $counter = $e_cur[0] * 1 + 1;
            $query = "SELECT e_id, e_date FROM FlagDayRotation WHERE Flag = 'Gray' AND e_date >= " . $txtLockDate;
            $result = mysqli_query($conn, $query);
            while ($cur = mysqli_fetch_row($result)) {
                $query = "INSERT INTO tblEmployee_Weekoff_Datewise (Cmp_Id, Branch_Id, Emp_Id, Emp_WeekOff_Id, Emp_WeekOffDate, Login_C_id, Form_Id) VALUES ('" . $co_code . "', '1', (SELECT Emp_Id FROM tblemployee WHERE Emp_payroll_No = '" . addZero($cur[0], $txtECodeLength) . "'), '" . $counter . "', '" . displayParadoxDate($cur[1]) . " 00:00:00.000', '1', '57')";
                if (mssql_query($query, $oconn)) {
                    $count++;
                    $counter++;
                } else {
                    echo "269: Error in Query: " . $query;
                }
            }
        } else {
            echo "273: Error in Query: " . $query;
        }
        $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('HRM Off Day Synch - Records: " . $count . "', " . insertToday() . ", '" . getNow() . "')";
        updateData($conn, $query, true);
    }
    $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('HRM Synch', " . insertToday() . ", '" . getNow() . "')";
    updateData($conn, $query, true);
}

?>