<?php


ob_start("ob_gzhandler");
error_reporting(E_ALL);
set_time_limit(0);
include "Functions.php";
$conn = openConnection();
$iconn = openIConnection();
$jconn = openIConnection();
$kconn = openIConnection();
$lconn = openIConnection();
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
if (checkMAC($conn) == false) {
    print "Un Registered Application. Process Terminated.";
    exit;
}
if (noTASoftware("", $txtMACAddress)) {
    print "Un Registered Application. Process Terminated.";
    exit;
}
$query = "UNLOCK Tables";
if (!updateIData($iconn, $query, true)) {
    echo "Error in Query: " . $query;
}
$main_count = 1;
for ($iii = 0; $iii < $main_count; $iii++) {
    $oconn = mssql_connection($txtDBIP, $txtDBName, $txtDBUser, $txtDBPass);
    echo "\n\rConnected to MSSQL: " . $oconn;
    $query = "SELECT TableName, Overwrite, EID, EName, IDNo, Dept, Division, Remark, Shift, Phone, Status, ActiveValue, PassiveValue, DataCOMPayroll, UpdateDate, UpdateSalary, Project, CostCentre FROM PayrollMap";
    $main_result = selectData($conn, $query);
    if ($oconn != "" && $main_result[1] != "" && checkMAC($conn) == true && $main_result[1] != "No Synchronization" && $main_result[1] == "Payroll DB") {
        $last_cur[0] = "";
        $migrate_query = "SELECT Col, Val, DateFrom, DateTo, MonthNo FROM MigrateMaster WHERE LENGTH(DateFrom) = 8 AND LENGTH(DateTo) = 8 ORDER BY Col, Val ";
        $migrate_result = mysqli_query($jconn, $migrate_query);
        while ($migrate_cur = mysqli_fetch_row($migrate_result)) {
            if ($migrate_cur[2] != "" && $migrate_cur[3] != "") {
                $last_cur[0] = $migrate_cur[2];
                $txtLockDate = $migrate_cur[3];
                $where = " FROM AttendanceMaster WHERE ADate >= " . $last_cur[0] . " AND ADate <= " . $txtLockDate;
                $WKSUN_DAYS = getTotalDays(displayDate($last_cur[0]), displayDate($txtLockDate));
                $SAT_DAYS = getDayCount($last_cur[0], $txtLockDate, $WKSUN_DAYS, "Saturday");
                $SUN_DAYS = getDayCount($last_cur[0], $txtLockDate, $WKSUN_DAYS, "Sunday");
                $WKSAT_DAYS = $WKSUN_DAYS - $SUN_DAYS;
                $WK_DAYS = $WKSAT_DAYS - $SAT_DAYS;
                $regcal = 0;
                $cadre = "";
                if ($main_result[16] == "Dept [TA]") {
                    $cadre = "dept";
                } else {
                    if ($main_result[16] == "Div/Desg [TA]") {
                        $cadre = "company";
                    } else {
                        if ($main_result[16] == "Social No [TA]") {
                            $cadre = "idno";
                        } else {
                            if ($main_result[16] == "Phone [TA]") {
                                $cadre = "phone";
                            } else {
                                if ($main_result[16] == "Remark [TA]") {
                                    $cadre = "remark";
                                }
                            }
                        }
                    }
                }
                $dd = addZero(substr($txtLockDate, 6, 2) * 1 + 1, 2);
                $mm = addZero(substr($txtLockDate, 4, 2) * 1 - 1, 2);
                $yyyy = substr($txtLockDate, 0, 4);
                if (substr($txtLockDate, 4, 2) == "01") {
                    $mm = "12";
                }
                if (substr($txtLockDate, 6, 2) == "31") {
                    $dd = "01";
                }
                $mm = $mm * 1 + 1;
                if (12 < $mm) {
                    $mm = 1;
                }
                if (0 < $migrate_cur[4]) {
                    $mm = $migrate_cur[4];
                }
                $p_day = 0;
                $a_day = 0;
                $a_sat_day = 0;
                $normal = 0;
                $ot_1 = 0;
                $ot_2 = 0;
                $ot_3 = 0;
                $ot_4 = 0;
                $ot1 = 0;
                $ot2 = 0;
                $ot3 = 0;
                $ot4 = 0;
                $v = 0;
                $i = 0;
                $b = 0;
                $g = 0;
                $y = 0;
                $o = 0;
                $r = 0;
                $gr = 0;
                $br = 0;
                $pr = 0;
                $mg = 0;
                $tl = 0;
                $aq = 0;
                $sf = 0;
                $ab = 0;
                $gl = 0;
                $vm = 0;
                $sl = 0;
                $mr = 0;
                $pk = 0;
                $night = 0;
                $rotate = 0;
                $late = 0;
                $early = 0;
                $grace = 0;
                $mcount = 0;
                $wk_bonus_count = 0;
                $off_count_8_12 = 0;
                $off_count_12 = 0;
                $ph_count_8_12 = 0;
                $ph_count_12 = 0;
                $off_aot = 0;
                $query = "SELECT id, phone, F1 FROM tuser WHERE " . $migrate_cur[0] . " = '" . $migrate_cur[1] . "' ORDER BY id";
                $result = mysqli_query($kconn, $query);
                while ($cur = mysqli_fetch_row($result)) {
                    $a_day = getASS($lconn, $cur[0], displayDate($last_cur[0]), displayDate($txtLockDate));
                    $a_sat_day = getAS($lconn, $cur[0], displayDate($last_cur[0]), displayDate($txtLockDate));
                    $a_sun_day = getA($lconn, $cur[0], displayDate($last_cur[0]), displayDate($txtLockDate));
                    $mcount = getCountSum($lconn, $where . " AND (Normal >= 28800 AND Day<>OT1 AND Day<>OT2) ", "COUNT", "Normal", $cur[0]);
                    $wk_bonus_count = getCountSum($lconn, $where . " AND (Normal >= 28800 AND Day=OT1) ", "COUNT", "Normal", $cur[0]);
                    $normal = getCountSum($lconn, $where, "SUM", "Normal", $cur[0]);
                    $ot1 = getCountSum($lconn, $where . " AND Day<>OT1 AND Day<>OT2 AND Flag<>'Purple' ", "SUM", "AOvertime", $cur[0]);
                    $ot2 = getCountSum($lconn, $where . " AND Day=OT1 AND Flag<>'Purple' ", "SUM", "AOvertime", $cur[0]);
                    $ot3 = getCountSum($lconn, $where . " AND Day=OT2 AND (Flag='Black' OR Flag='Proxy') ", "SUM", "AOvertime", $cur[0]);
                    $ot4 = getCountSum($lconn, $where . " AND Flag='Purple' ", "SUM", "AOvertime", $cur[0]);
                    $ot_1 = getCountSum($lconn, $where . " AND Day<>OT1 AND Day<>OT2 AND Flag<>'Purple' ", "COUNT", "AttendanceID", $cur[0]);
                    $ot_2 = getCountSum($lconn, $where . " AND Day=OT1 AND (Flag='Black' OR Flag='Proxy') ", "COUNT", "AttendanceID", $cur[0]);
                    $ot_3 = getCountSum($lconn, $where . " AND Day=OT2 AND (Flag='Black' OR Flag='Proxy') ", "COUNT", "AttendanceID", $cur[0]);
                    $ot_4 = getCountSum($lconn, $where . " AND Flag='Purple' ", "COUNT", "AttendanceID", $cur[0]);
                    $v = getCountSum($lconn, $where . " AND Flag='Violet' ", "COUNT", "AttendanceID", $cur[0]);
                    $i = getCountSum($lconn, $where . " AND Flag='Indigo' ", "COUNT", "AttendanceID", $cur[0]);
                    $b = getCountSum($lconn, $where . " AND Flag='Blue' ", "COUNT", "AttendanceID", $cur[0]);
                    $g = getCountSum($lconn, $where . " AND Flag='Green' ", "COUNT", "AttendanceID", $cur[0]);
                    $y = getCountSum($lconn, $where . " AND Flag='Yellow' ", "COUNT", "AttendanceID", $cur[0]);
                    $o = getCountSum($lconn, $where . " AND Flag='Orange' ", "COUNT", "AttendanceID", $cur[0]);
                    $r = getCountSum($lconn, $where . " AND Flag='Red' ", "COUNT", "AttendanceID", $cur[0]);
                    $gr = getCountSum($lconn, $where . " AND Flag='Gray' ", "COUNT", "AttendanceID", $cur[0]);
                    $br = getCountSum($lconn, $where . " AND Flag='Brown' ", "COUNT", "AttendanceID", $cur[0]);
                    $pr = getCountSum($lconn, $where . " AND Flag='Purple' ", "COUNT", "AttendanceID", $cur[0]);
                    $mg = getCountSum($lconn, $where . " AND Flag='Magenta' ", "COUNT", "AttendanceID", $cur[0]);
                    $tl = getCountSum($lconn, $where . " AND Flag='Teal' ", "COUNT", "AttendanceID", $cur[0]);
                    $aq = getCountSum($lconn, $where . " AND Flag='Aqua' ", "COUNT", "AttendanceID", $cur[0]);
                    $sf = getCountSum($lconn, $where . " AND Flag='Safron' ", "COUNT", "AttendanceID", $cur[0]);
                    $ab = getCountSum($lconn, $where . " AND Flag='Amber' ", "COUNT", "AttendanceID", $cur[0]);
                    $gl = getCountSum($lconn, $where . " AND Flag='Gold' ", "COUNT", "AttendanceID", $cur[0]);
                    $vm = getCountSum($lconn, $where . " AND Flag='Vermilion' ", "COUNT", "AttendanceID", $cur[0]);
                    $sl = getCountSum($lconn, $where . " AND Flag='Silver' ", "COUNT", "AttendanceID", $cur[0]);
                    $mr = getCountSum($lconn, $where . " AND Flag='Maroon' ", "COUNT", "AttendanceID", $cur[0]);
                    $pk = getCountSum($lconn, $where . " AND Flag='Pink' ", "COUNT", "AttendanceID", $cur[0]);
                    $night = getCountSum($lconn, $where . " AND NightFlag='1' ", "COUNT", "AttendanceID", $cur[0]);
                    $rotate = getCountSum($lconn, $where . " AND RotateFlag='1' ", "COUNT", "AttendanceID", $cur[0]);
                    $late = getCountSum($lconn, $where . " AND LateIn>'0' AND LateIn_flag = 0 ", "COUNT", "AttendanceID", $cur[0]);
                    $early = getCountSum($lconn, $where . " AND EarlyIn>'0' ", "COUNT", "AttendanceID", $cur[0]);
                    $grace = getCountSum($lconn, $where . " AND Grace>'0' ", "COUNT", "AttendanceID", $cur[0]);
                    $late_sum = getCountSum($lconn, $where . " AND LateIn>'0' AND LateIn_flag = 0 ", "SUM", "LateIn", $cur[0]);
                    $early_sum = getCountSum($lconn, $where . " AND EarlyIn>'0' ", "SUM", "EarlyIn", $cur[0]);
                    $query__ = "DELETE FROM payroll_integration WHERE DateFrom = '" . displayParadoxDate($last_cur[0]) . " 00:00:00' AND DateTo = '" . displayParadoxDate($txtLockDate) . " 00:00:00' AND EmpId = '" . addZero($cur[1], $txtECodeLength) . "'";
                    if (!mssql_query($query__, $oconn)) {
                        echo "\n\r" . $query__;
                    }
                    if ($normal == 0) {
                        $a_day = $WK_DAYS;
                    }
                    $payroll_query = "INSERT INTO payroll_integration (EmpId, DateFrom, DateTo, Category, Absent, OT1, OT2, OT3, OT4, Leave01, Leave02, Leave03, Leave04, Leave05, Leave06, Leave07, Leave08, Leave09, Leave10, Leave11, Leave12, Leave13, Leave14, Leave15, Leave16, Leave17, Leave18, Leave19, Leave20, PresentDays, NoOfWeekDays, NoOfSAT, NoOfSUN, NoOfPH) VALUES ('" . addZero($cur[1], $txtECodeLength) . "', '" . displayParadoxDate($last_cur[0]) . " 00:00:00', '" . displayParadoxDate($txtLockDate) . " 00:00:00', '" . $cur[2] . "', " . $a_day . ", " . $ot1 . ", " . $ot2 . ", " . $ot3 . ", " . $ot4 . ", " . $v . ", " . $i . ", " . $b . ", " . $g . ", " . $y . ", " . $o . ", " . $r . ", " . $gr . ", " . $br . ", " . $pr . ", " . $mg . ", " . $tl . ", " . $aq . ", " . $sf . ", " . $ab . ", " . $gl . ", " . $vm . ", " . $sl . ", " . $mr . ", " . $pk . ", " . ($WKSUN_DAYS - $a_day) . ", " . $WK_DAYS . ", " . $ot_2 . ", " . $ot_3 . ", " . $ot_4 . ") ";
                    if (!mssql_query($payroll_query, $oconn)) {
                        echo "\n\r" . $payroll_query;
                    }
                }
            }
            $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('Payroll Synch Attendance - Col: " . $migrate_cur[0] . ", Val: " . $migrate_cur[1] . ", From: " . displayDate($migrate_cur[2]) . ", To: " . displayDate($migrate_cur[3]) . ", Mon: " . $migrate_cur[4] . ", ', " . insertToday() . ", '" . getNow() . "')";
            updateIData($iconn, $query, true);
        }
    }
}
function getCountSum($conn, $where, $cs, $f1, $id)
{
    $query = "SELECT " . $cs . "(" . $f1 . ") " . $where . " AND AttendanceMaster.EmployeeID = " . $id;
    $result = selectData($conn, $query);
    if ($result[0] == "") {
        $result[0] = 0;
    }
    return $result[0];
}

?>