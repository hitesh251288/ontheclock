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
if (is_numeric($from_) == true && is_numeric($to_) == true && $staff_ != "") {
    if (checkMAC($conn) == true) {
        $main_query = "SELECT DBType, DBIP, DBName, DBUser, DBPass, LockDate, EmployeeCodeLength FROM OtherSettingMaster";
        $main_result = selectData($conn, $main_query);
        if ($main_result[0] == "ODBC") {
            $aconn = odbc_connection("", $main_result[2], "", "");
            echo "\n\rConnected to Access DB: " . $aconn;
            $counter = 0;
            $period = substr($to_, 4, 2) . "-" . substr($to_, 0, 4);
            $dayCount = getTotalDays(displayDate($from_), displayDate($to_));
            $actual_saturday = getDayCount($from_, $to_, $dayCount, "Saturday");
            $actual_sunday = getDayCount($from_, $to_, $dayCount, "Sunday");
            $user_query = "SELECT DISTINCT(id), OT1, OT2, phone FROM tuser WHERE Remark = '" . $staff_ . "' ";
            $user_result = mysqli_query($conn, $user_query);
            while ($user_cur = mysqli_fetch_row($user_result)) {
                $emp_code = $user_cur[0];
                $query = "DELETE FROM trs_attendance WHERE EMP_Code = '" . $emp_code . "' AND Period = '" . $period . "' ";
                odbc_exec($aconn, $query);
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
                $query = "INSERT INTO trs_attendance (EMP_Code, Period, NO_DAYS, NO_ABDAYS, HEATDYS, NIGHTDYS, WK_DY_OT, PUB_HOL_OT, HAZARD, WK_INCENT, ChemicalDys, NO_SAT_ATND, NO_SUN_ATND, POFF_PRSNT, OFF_PRSNT, SAT_OT, SUN_OT, Actual_Saturday, Actual_Sunday, Actual_Preoff, Actual_Off, Staggered) VALUES ('" . addZero($emp_code, $main_result[6]) . "', '" . $period . "', " . $no_days . ", " . $no_abdays . ", " . $heat . ", " . $nightdys . ", " . $wk_dy_ot / 3600 . ", " . $pub_hol_ot / 3600 . ", " . $hazard . ", 0, " . $chemical . ", " . $no_sat_atnd . ", " . $no_sun_atnd . ", " . $poff_prsnt . ", " . $off_prsnt . ", " . $sat_ot / 3600 . ", " . $sun_ot / 3600 . ", " . $actual_saturday . ", " . $actual_sunday . ", " . $actual_preoff . ", " . $actual_off . ", " . $staggered . ") ";
                if (odbc_exec($aconn, $query)) {
                    $counter++;
                }
            }
            echo "\n\r Total Records Migrated: " . $counter;
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