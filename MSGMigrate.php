<?php


ob_start("ob_gzhandler");
error_reporting(E_ALL);
date_default_timezone_set("Africa/Algiers");
set_time_limit(0);
include "Functions.php";
$conn = openConnection();
$iconn = openIConnection();
var_dump($argv);
$from_ = insertDate($argv[1]);
$to_ = insertDate($argv[2]);
$staff_ = $argv[3];
$ot_ = $argv[4];
if (is_numeric($from_) == true && is_numeric($to_) == true && $staff_ != "") {
    if (checkMAC($conn) == true) {
        $main_query = "SELECT DBType, DBIP, DBName, DBUser, DBPass, LockDate, EmployeeCodeLength FROM OtherSettingMaster";
        $main_result = selectData($conn, $main_query);
        if ($main_result[0] == "MSSQL") {
            $mconn = mssql_connection($main_result[1], $main_result[2], $main_result[3], $main_result[4]);
            echo "\n\rConnected to MSSQL DB: " . $mconn;
            $counter = 0;
            $ot_counter = 0;
            $period = substr($to_, 0, 4) . "-" . substr($to_, 4, 2) . "" . substr($staff_, 0, 1);
            $operiod = substr($to_, 0, 4) . "-" . substr($to_, 4, 2) . "" . $ot_ . "" . substr($staff_, 0, 1);
            $dayCount = getTotalDays(displayDate($from_), displayDate($to_));
            $actual_saturday = getDayCount($from_, $to_, $dayCount, "Saturday");
            $actual_sunday = getDayCount($from_, $to_, $dayCount, "Sunday");
            $user_query = "SELECT DISTINCT(id), OT1, OT2, phone FROM tuser WHERE Remark = '" . $staff_ . "' ";
            $user_result = mysqli_query($conn, $user_query);
            while ($user_cur = mysqli_fetch_row($user_result)) {
                $emp_code = $user_cur[0];
                $query = "DELETE FROM [COTSYN NIG_ LTD(GARMENTS)\$Monthly Attendance] WHERE [Employee No_] = '" . $emp_code . "' AND Period = '" . $period . "' ";
                mssql_query($query, $mconn);
                $query = "DELETE FROM [COTSYN NIG_ LTD(GARMENTS)\$Monthly Attendance] WHERE [Employee No_] = '" . $emp_code . "' AND Period = '" . $operiod . "' ";
                mssql_query($query, $mconn);
                $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND EmployeeID = " . $emp_code . " AND ADate >= " . $from_ . " AND ADate <= " . $to_ . " AND ((tuser.Remark = 'SNR' AND (Flag = 'Black' OR Flag = 'Proxy' OR Flag = 'Purple' OR Flag = 'Aqua' OR Flag = 'Indigo')) OR (tuser.Remark = 'JNR' AND (Flag = 'Black' OR Flag = 'Proxy' OR Flag = 'Purple' OR Flag = 'Indigo'))) ";
                $result = selectData($conn, $query);
                $no_days = $result[0];
                $proxy_ph = 0;
                $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster WHERE EmployeeID = " . $emp_code . " AND ADate >= " . $from_ . " AND ADate <= " . $to_ . " AND Flag = 'Proxy' AND ADate IN (SELECT OTDate FROM OTDate) ";
                $result = selectData($conn, $query);
                $proxy_ph = $result[0];
                $no_days = $no_days - $proxy_ph;
                if ($no_days < 0) {
                    $no_days = 0;
                }
                $heat = 0;
                $hazard = 0;
                $chemical = 0;
                $dust = 0;
                if (stripos($user_cur[3], "H") !== false) {
                    $heat = $no_days;
                }
                if (stripos($user_cur[3], "Z") !== false) {
                    $hazard = $no_days;
                }
                if (stripos($user_cur[3], "C") !== false) {
                    $chemical = $no_days;
                }
                if (stripos($user_cur[3], "D") !== false) {
                    $dust = $no_days;
                }
                $sub_query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster, tuser WHERE EmployeeID = id AND EmployeeID = " . $emp_code . " AND ADate >= " . $from_ . " AND ADate <= " . $to_ . " AND AttendanceMaster.Day = AttendanceMaster.OT1 AND AOvertime > 0 ";
                $sub_result = selectData($conn, $sub_query);
                $poff_prsnt = $sub_result[0];
                $sub_query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster, tuser WHERE EmployeeID = id AND EmployeeID = " . $emp_code . " AND ADate >= " . $from_ . " AND ADate <= " . $to_ . " AND AttendanceMaster.Day = AttendanceMaster.OT2 AND AOvertime > 0 ";
                $sub_result = selectData($conn, $sub_query);
                $off_prsnt = $sub_result[0];
                $sub_query = "SELECT COUNT(FlagDayRotationID) FROM FlagDayRotation WHERE e_id = " . $emp_code . " AND e_date >= " . $from_ . " AND e_date <= " . $to_ . " AND OT = 'OT1' ";
                $sub_result = selectData($conn, $sub_query);
                $actual_preoff = $sub_result[0];
                $sub_query = "SELECT COUNT(FlagDayRotationID) FROM FlagDayRotation WHERE e_id = " . $emp_code . " AND e_date >= " . $from_ . " AND e_date <= " . $to_ . " AND OT = 'OT2' ";
                $sub_result = selectData($conn, $sub_query);
                $actual_off = $sub_result[0];
                $no_abdays = 0;
                $staggered = 0;
                if (stripos($user_cur[3], "Y") !== false) {
                    $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster WHERE EmployeeID = " . $emp_code . " AND ADate >= " . $from_ . " AND ADate <= " . $to_ . " ";
                    $result = selectData($conn, $query);
                    $totalDays = $result[0];
                    $no_abdays = $dayCount - $totalDays - $proxy_ph;
                    $sub_query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster, FlagDayRotation WHERE AttendanceMaster.EmployeeID = FlagDayRotation.e_id AND AttendanceMaster.EmployeeID = " . $emp_code . " AND AttendanceMaster.ADate = FlagDayRotation.e_date AND AttendanceMaster.ADate >= " . $from_ . " AND AttendanceMaster.ADate <= " . $to_ . " AND AttendanceMaster.group_id = 2 AND AttendanceMaster.Flag = 'Proxy' ";
                    $sub_result = selectData($conn, $sub_query);
                    $no_abdays = $no_abdays - ($actual_preoff + $actual_off - $sub_result[0] - ($poff_prsnt + $off_prsnt));
                    if ($dayCount - $totalDays < $no_abdays) {
                        $no_abdays = $dayCount - $totalDays;
                    }
                    if ($no_abdays < 0) {
                        $no_abdays = 0;
                    }
                    $staggered = 1;
                } else {
                    $no_abdays = getASS($conn, $emp_code, $from_, $to_);
                }
                $heatdys = $no_days;
                $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster WHERE EmployeeID = " . $emp_code . " AND ADate >= " . $from_ . " AND ADate <= " . $to_ . " AND NightFlag = 1 AND Flag <> 'Aqua' ";
                $result = selectData($conn, $query);
                $nightdys = $result[0];
                if (stripos($user_cur[3], "Y") !== false) {
                    $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster WHERE EmployeeID = " . $emp_code . " AND ADate >= " . $from_ . " AND ADate <= " . $to_ . " AND Day = 'Saturday' AND (Flag = 'Black' OR Flag = 'Proxy') ";
                } else {
                    $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster WHERE EmployeeID = " . $emp_code . " AND ADate >= " . $from_ . " AND ADate <= " . $to_ . " AND Day = 'Saturday' AND OT1 = 'Saturday' AND AOvertime > 0 ";
                }
                $result = selectData($conn, $query);
                $no_sat_atnd = $result[0];
                if (stripos($user_cur[3], "Y") !== false) {
                    $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster WHERE EmployeeID = " . $emp_code . " AND ADate >= " . $from_ . " AND ADate <= " . $to_ . " AND Day = 'Sunday' AND (Flag = 'Black' OR Flag = 'Proxy') ";
                } else {
                    $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster WHERE EmployeeID = " . $emp_code . " AND ADate >= " . $from_ . " AND ADate <= " . $to_ . " AND Day = 'Sunday' AND OT2 = 'Sunday' AND AOvertime > 0 ";
                }
                $result = selectData($conn, $query);
                $no_sun_atnd = $result[0];
                $query = "SELECT SUM(AOvertime) FROM AttendanceMaster, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND EmployeeID = " . $emp_code . " AND ADate >= " . $from_ . " AND ADate <= " . $to_ . " AND AttendanceMaster.Day <> AttendanceMaster.OT1 AND AttendanceMaster.Day <> AttendanceMaster.OT2 AND AttendanceMaster.Flag <> 'Purple' AND (tuser.Remark = 'SNR' OR (tuser.Remark = 'JNR' AND AttendanceMaster.Flag <> 'Aqua')) ";
                $result = selectData($conn, $query);
                $wk_dy_ot = $result[0];
                if ($wk_dy_ot == "") {
                    $wk_dy_ot = 0;
                }
                $query = "SELECT SUM(AOvertime) FROM AttendanceMaster WHERE EmployeeID = " . $emp_code . " AND ADate >= " . $from_ . " AND ADate <= " . $to_ . " AND Flag = 'Purple' ";
                $result = selectData($conn, $query);
                $pub_hol_ot = $result[0];
                if ($pub_hol_ot == "") {
                    $pub_hol_ot = 0;
                }
                $query = "SELECT COUNT(AOvertime) FROM AttendanceMaster WHERE EmployeeID = " . $emp_code . " AND ADate >= " . $from_ . " AND ADate <= " . $to_ . " AND Flag = 'Purple' ";
                $result = selectData($conn, $query);
                $no_ph_atnd = $result[0];
                if ($no_ph_atnd == "") {
                    $no_ph_atnd = 0;
                }
                $query = "SELECT COUNT(AOvertime) FROM AttendanceMaster WHERE EmployeeID = " . $emp_code . " AND ADate >= " . $from_ . " AND ADate <= " . $to_ . " AND Flag = 'Orange' ";
                $result = selectData($conn, $query);
                $no_al_atnd = $result[0];
                if ($no_al_atnd == "") {
                    $no_al_atnd = 0;
                }
                $query = "SELECT COUNT(AOvertime) FROM AttendanceMaster WHERE EmployeeID = " . $emp_code . " AND ADate >= " . $from_ . " AND ADate <= " . $to_ . " AND Flag = 'Blue' ";
                $result = selectData($conn, $query);
                $no_cl_atnd = $result[0];
                if ($no_cl_atnd == "") {
                    $no_cl_atnd = 0;
                }
                $query = "SELECT COUNT(AOvertime) FROM AttendanceMaster WHERE EmployeeID = " . $emp_code . " AND ADate >= " . $from_ . " AND ADate <= " . $to_ . " AND Flag = 'Red' ";
                $result = selectData($conn, $query);
                $no_sl_atnd = $result[0];
                if ($no_sl_atnd == "") {
                    $no_sl_atnd = 0;
                }
                $query = "SELECT SUM(AOvertime) FROM AttendanceMaster WHERE EmployeeID = " . $emp_code . " AND ADate >= " . $from_ . " AND ADate <= " . $to_ . " AND Day = OT1 ";
                $result = selectData($conn, $query);
                $sat_ot = $result[0];
                if ($sat_ot == "") {
                    $sat_ot = 0;
                }
                $query = "SELECT SUM(AOvertime) FROM AttendanceMaster WHERE EmployeeID = " . $emp_code . " AND ADate >= " . $from_ . " AND ADate <= " . $to_ . " AND Day = OT2 ";
                $result = selectData($conn, $query);
                $sun_ot = $result[0];
                if ($sun_ot == "") {
                    $sun_ot = 0;
                }
                $query = "INSERT INTO [COTSYN NIG_ LTD(GARMENTS)\$Monthly Attendance] ([Payroll Period], [Employee No_], [Days Worked], [Absent Days], [Night Days], [Normal Overtime], [Annual Leave Days], [Casual Leave Days], [Sick Leave Days], [Hazard Days], [Heat Days], [SAT OT], [SUN OT], [PH OT], [SAT Days Worked], [SUN Days Worked], [PH Days Worked]) VALUES ('" . $period . "', 'GA" . addZero(substr($emp_code, 2, 4), $main_result[6]) . "', " . $no_days . ", " . $no_abdays . ", " . $nightdys . ", " . $wk_dy_ot / 3600 . ", " . $no_al_atnd . ", " . $no_cl_atnd . ", " . $no_sl_atnd . ", " . $hazard . ", " . $heat . ", " . $sat_ot / 3600 . ", " . $sun_ot / 3600 . ", " . $pub_hol_ot / 3600 . ", " . $no_sat_atnd . ", " . $no_sun_atnd . ", " . $no_ph_atnd . ") ";
                if (mssql_query($query, $mconn)) {
                    $counter++;
                }
                $ot_query = "SELECT AOvertime, Day, OT1, OT2, Flag, ADate FROM AttendanceMaster WHERE EmployeeID = " . $emp_code . " AND ADate >= " . $from_ . " AND ADate <= " . $to_ . " AND AOvertime > 0 AND (Day = OT1 OR Day = OT2 OR Flag = 'Purple' OR Flag = 'Magenta') ";
                $ot_result = mysql_query($ot_query, $conn);
                while ($ot_cur = mysqli_fetch_row($ot_result)) {
                    $query = "INSERT INTO [COTSYN NIG_ LTD(GARMENTS)\$Monthly Attendance] ([Payroll Period], [Employee No], [E_D Code], [Overtime Date], Quantity) VALUES ('" . $operiod . "', 'GA" . addZero(substr($emp_code, 2, 4), $main_result[6]) . "', ";
                    if ($ot_cur[1] == $ot_cur[2]) {
                        $query .= " '10820', ";
                    } else {
                        if ($ot_cur[1] == $ot_cur[3]) {
                            $query .= " '10840', ";
                        } else {
                            if ($ot_cur[1] == "Purple") {
                                $query .= " '10860', ";
                            } else {
                                if ($ot_cur[4] == "Magenta") {
                                    $query .= " '10870', ";
                                }
                            }
                        }
                    }
                    $query .= " '" . displayUSDate($ot_cur[5]) . " 12:00:00 AM', " . $ot_cur[0] / 3600 . ")";
                    if (mssql_query($query, $mconn)) {
                        $ot_counter++;
                    }
                }
            }
            echo "\n\r Attendance Total Records Migrated: " . $counter;
            echo "\n\r Attendance Overtime Records Migrated: " . $ot_counter;
            $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('DB Migrate', " . insertToday() . ", '" . getNow() . "')";
            updateIData($iconn, $query, true);
        } else {
            print "Process Terminated: Found use of INVALID DB Type";
        }
    } else {
        print "Process Terminated: Un Registered Application";
    }
} else {
    print "Process Terminated: Invalid Argument Supplied: Command Format = \\php ASMigrate.php FROM TO STAFF_TYPE \n\rDate Formats: DD/MM/YYYY \n\rSTAFF_TYPE: JNR/SNR";
}

?>