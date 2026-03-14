<?php


ob_start("ob_gzhandler");
error_reporting(E_ALL);
set_time_limit(0);
include "Functions.php";
var_dump($argv);
$csv = $argv[1];
$file_name = "PayMaster-Attendance-" . insertToday() . "" . getNow() . ".csv";
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
$co_code = 1;
$pay_table[0] = "tblEmployee";
$pay_table[1] = "tblDepartment";
$pay_table[2] = "tblBranch";
$pay_table[3] = "tblDesignation";
$pay_table[4] = "tblCategory";
$pay_table[5] = "tblMasterOne";
$pay_table[6] = "tblPayDedGroup";
$pay_id[0] = "EmpNo";
$pay_id[1] = "DepartmentCode";
$pay_id[2] = "BranchCode";
$pay_id[3] = "DesignationCode";
$pay_id[4] = "CategoryCode";
$pay_id[5] = "MasterCode";
$pay_id[6] = "PayDedGroupCode";
$pay_name[0] = "EmpName";
$pay_name[1] = "DepartmentName";
$pay_name[2] = "BranchName";
$pay_name[3] = "DesignationName";
$pay_name[4] = "CategoryName";
$pay_name[5] = "MasterName";
$pay_name[6] = "PayDedGroupName";
$virdi[0] = "";
$virdi[1] = "";
$virdi[2] = "";
$virdi[3] = "Name";
$virdi[4] = "idno";
$virdi[5] = "dept";
$virdi[6] = "company";
$virdi[7] = "Remark";
$virdi[8] = "";
$virdi[9] = "Phone";

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
    }
//    $oconn = mssql_connection($txtDBIP, $txtDBName, $txtDBUser, $txtDBPass);
    $oconn = mssql_connection('DESKTOP-I1EBN6N\SQLEXPRESS', 'PayMaster', 'sa', 'bit123');
    echo "\n\rConnected to MSSQL: " . $oconn;
    $query = "ALTER TABLE tblEmpWorkHours_VIRDI ADD COLUMN LVOFF18_COUNT8 INT NULL, ADD COLUMN LVOFF18_COUNT12 INT NULL, ADD COLUMN LV10_COUNT8 INT NULL, ADD COLUMN LV10_COUNT12 INT NULL, ADD COLUMN LV18_SECONDS INT NULL";
    if (!mssql_query($query, $oconn)) {
        $query = "ALTER TABLE Employee_WorkHour_VIRDI ADD COLUMN LVOFF18_COUNT8 INT NULL, ADD COLUMN LVOFF18_COUNT12 INT NULL, ADD COLUMN LV10_COUNT8 INT NULL, ADD COLUMN LV10_COUNT12 INT NULL, ADD COLUMN LV18_SECONDS INT NULL";
        if (!mssql_query($query, $oconn)) {
            echo "\n\r Web Payroll Warning: Unable to create OFFDAY Columns";
        }
    }
    if (getRegister($txtMACAddress, 7) == "36") {
        $co_code = 2;
    } else {
        if (getRegister($txtMACAddress, 7) == "60") {
            $co_code = 6;
        } else {
            if (getRegister($txtMACAddress, 7) == "58") {
                $co_code = 7;
            } else {
                if (getRegister($txtMACAddress, 7) == "59") {
                    $co_code = 8;
                } else {
                    if (getRegister($txtMACAddress, 7) == "52") {
                        $co_code = 2;
                    } else {
                        if (getRegister($txtMACAddress, 7) == "51") {
                            $co_code = 3;
                        } else {
                            if (getRegister($txtMACAddress, 7) == "53") {
                                $co_code = 4;
                            } else {
                                if (getRegister($txtMACAddress, 7) == "82") {
                                    $co_code = 10;
                                } else {
                                    if (getRegister($txtMACAddress, 7) == "103") {
                                        $co_code = 9;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    $query = "SELECT TableName, Overwrite, EID, EName, IDNo, Dept, Division, Remark, Shift, Phone, Status, ActiveValue, PassiveValue, DataCOMPayroll, UpdateDate, UpdateSalary, Project, CostCentre FROM PayrollMap";
    $main_result = selectData($conn, $query);
    if (($oconn != "" || $csv == "csv") && $main_result[1] != "" && checkMAC($conn) == true && $main_result[1] != "No Synchronization" && $main_result[1] == "Payroll DB") {
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
                if ($migrate_cur[0] == $cadre && (stripos(strtolower($migrate_cur[1]), "cas") !== false || strtolower($migrate_cur[1]) == "sto" || strtolower($migrate_cur[1]) == "caf" || getRegister($txtMACAddress, 7) == "66" && strtolower($migrate_cur[1]) == "Contractor")) {
                    $regcal = 1;
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
                $ali = 0;
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
                
                if (getRegister($txtMACAddress, 7) == "86") {
                    echo $query = "SELECT id, phone, idno FROM tuser WHERE " . $migrate_cur[0] . " = '" . $migrate_cur[1] . "' AND company = '" . $txtDBName . "' ORDER BY id";
                } else {
                    if (getRegister($txtMACAddress, 7) == "82" || getRegister($txtMACAddress, 7) == "103" || getRegister($txtMACAddress, 7) == "106" || getRegister($txtMACAddress, 7) == "107" || getRegister($txtMACAddress, 7) == "126" || getRegister($txtMACAddress, 7) == "57" || getRegister($txtMACAddress, 7) == "58" || getRegister($txtMACAddress, 7) == "59" || getRegister($txtMACAddress, 7) == "60" || getRegister($txtMACAddress, 7) == "117" || getRegister($txtMACAddress, 7) == "150") {
                        echo $query = "SELECT id, phone, idno FROM tuser WHERE " . $migrate_cur[0] . " = '" . $migrate_cur[1] . "' AND idno <> '.' AND idno <> '' ORDER BY id";
                    }
                    if (getRegister($txtMACAddress, 7) == "14") {
                        echo $query = "SELECT idno, phone, idno FROM tuser WHERE " . $migrate_cur[0] . " = '" . $migrate_cur[1] . "' ORDER BY id";
                    } else {
                        echo $query = "SELECT id, phone, idno, company FROM tuser WHERE " . $migrate_cur[0] . " = '" . $migrate_cur[1] . "' ORDER BY id";
                    }
                }
                if ($csv == "csv") {
                    $handle = fopen($file_name, "w");
                }
                $result = mysqli_query($kconn, $query);
                while ($cur = mysqli_fetch_row($result)) { 
                    if (getRegister($txtMACAddress, 7) == "39") {
                        if ($cur[2] == "A - LNL STAFF" || $cur[2] == "C - LNL CONTRACT" || $cur[2] == "E - GNL STAFF" || $cur[2] == "H - EXPATRIATE") {
                            $regcal = 0;
                        } else {
                            $regcal = 1;
                        }
                    }
                    $a_day = getASS($lconn, $cur[0], displayDate($last_cur[0]), displayDate($txtLockDate));
                    $a_sat_day = getAS($lconn, $cur[0], displayDate($last_cur[0]), displayDate($txtLockDate));
                    $a_sun_day = getA($lconn, $cur[0], displayDate($last_cur[0]), displayDate($txtLockDate));
                    $mcount = getCountSum($lconn, $where . " AND (Normal >= 28800 AND Day<>OT1 AND Day<>OT2) ", "COUNT", "Normal", $cur[0]);
                    $wk_bonus_count = getCountSum($lconn, $where . " AND (Normal >= 28800 AND Day=OT1) ", "COUNT", "Normal", $cur[0]);
                    $normal = getCountSum($lconn, $where, "SUM", "Normal", $cur[0]);
                    if (getRegister($txtMACAddress, 7) == "25") {
                        $ot1 = getCountSum($lconn, $where . " AND Day<>OT1 AND Day<>OT2 AND Flag<>'Purple' ", "SUM", "(AOvertime + LateInColumn)", $cur[0]);
                    } else {
                        $ot1 = getCountSum($lconn, $where . " AND Day<>OT1 AND Day<>OT2 AND Flag<>'Purple' ", "SUM", "AOvertime", $cur[0]);
                    }
                    if (getRegister($txtMACAddress, 7) == "25") {
                        $ot2 = getCountSum($lconn, $where . " AND Day=OT1 AND Flag<>'Purple' ", "SUM", "(AOvertime + LateInColumn)", $cur[0]);
                    } else {
                        $ot2 = getCountSum($lconn, $where . " AND Day=OT1 AND Flag<>'Purple' ", "SUM", "AOvertime", $cur[0]);
                    }
                    if (getRegister($txtMACAddress, 7) == "5") {
                        $ot3 = getCountSum($lconn, $where . " AND (Day=OT2 OR Flag='Magenta') AND Flag<>'Purple' ", "SUM", "AOvertime", $cur[0]);
                    } else {
                        if (getRegister($txtMACAddress, 7) == "25") {
                            $ot3 = getCountSum($lconn, $where . " AND Day=OT2 AND (Flag='Black' OR Flag='Proxy') ", "SUM", "(AOvertime + LateInColumn)", $cur[0]);
                        } else {
                            $ot3 = getCountSum($lconn, $where . " AND Day=OT2 AND (Flag='Black' OR Flag='Proxy') ", "SUM", "AOvertime", $cur[0]);
                        }
                    }
                    if (getRegister($txtMACAddress, 7) == "25") {
                        $ot4 = getCountSum($lconn, $where . " AND Flag='Purple' ", "SUM", "(AOvertime + LateInColumn)", $cur[0]);
                    } else {
                        $ot4 = getCountSum($lconn, $where . " AND Flag='Purple' ", "SUM", "AOvertime", $cur[0]);
                    }
                    if (getRegister($txtMACAddress, 7) == "57" || getRegister($txtMACAddress, 7) == "58" || getRegister($txtMACAddress, 7) == "59" || getRegister($txtMACAddress, 7) == "60" || getRegister($txtMACAddress, 7) == "82") {
                        $ot_1 = getCountSum($lconn, $where . " AND Day<>OT1 AND Day<>OT2 AND (Flag='Black' OR Flag='Proxy') ", "COUNT", "AttendanceID", $cur[0]);
                    } else {
                        if (getRegister($txtMACAddress, 7) == "66") {
                            $ot_1 = getCountSum($lconn, $where . " AND Day<>OT1 AND Day<>OT2 AND (Flag='Black' OR Flag='Proxy' OR Flag='Blue') ", "COUNT", "AttendanceID", $cur[0]);
                        } else {
                            $ot_1 = getCountSum($lconn, $where . " AND Day<>OT1 AND Day<>OT2 AND Flag<>'Purple' ", "COUNT", "AttendanceID", $cur[0]);
                        }
                    }
                    if (getRegister($txtMACAddress, 7) == "39") {
                        $ot_2 = getCountSum($lconn, $where . " AND Day=OT1 AND (Flag='Black' OR Flag='Proxy') AND Overtime > 14400 ", "COUNT", "AttendanceID", $cur[0]);
                        $ot_3 = getCountSum($lconn, $where . " AND Day=OT2 AND (Flag='Black' OR Flag='Proxy') AND Overtime > 14400 ", "COUNT", "AttendanceID", $cur[0]);
                        $ot_4 = getCountSum($lconn, $where . " AND Flag='Purple' AND Overtime > 14400 ", "COUNT", "AttendanceID", $cur[0]);
                    } else {
                        $ot_2 = getCountSum($lconn, $where . " AND Day=OT1 AND (Flag='Black' OR Flag='Proxy') ", "COUNT", "AttendanceID", $cur[0]);
                        $ot_3 = getCountSum($lconn, $where . " AND Day=OT2 AND (Flag='Black' OR Flag='Proxy') ", "COUNT", "AttendanceID", $cur[0]);
                        $ot_4 = getCountSum($lconn, $where . " AND Flag='Purple' ", "COUNT", "AttendanceID", $cur[0]);
                    }
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
                    $late60 = getCountSum($lconn, $where . " AND LateIn>'3600' AND LateIn_flag = 0 ", "COUNT", "AttendanceID", $cur[0]);
                    $early = getCountSum($lconn, $where . " AND EarlyIn>'0' ", "COUNT", "AttendanceID", $cur[0]);
                    $grace = getCountSum($lconn, $where . " AND Grace>'0' ", "COUNT", "AttendanceID", $cur[0]);
                    $late_sum = getCountSum($lconn, $where . " AND LateIn>'0' AND LateIn_flag = 0 ", "SUM", "LateIn", $cur[0]);
                    $early_sum = getCountSum($lconn, $where . " AND EarlyIn>'0' ", "SUM", "EarlyIn", $cur[0]);
                    if (getRegister($txtMACAddress, 7) == "16") {
                        $off_count_8_12 = getCountSum($lconn, $where . " AND (Flag='Silver' OR Flag='Teal') AND ((AOvertime) >= 28800 AND (AOvertime) < 43200) ", "COUNT", "AttendanceID", $cur[0]);
                        $off_count_12 = getCountSum($lconn, $where . " AND (Flag='Silver' OR Flag='Teal') AND (AOvertime) >= 43200 ", "COUNT", "AttendanceID", $cur[0]);
                        $ph_count_8_12 = getCountSum($lconn, $where . " AND Flag='Purple' AND ((AOvertime) >= 28800 AND (AOvertime) < 43200) ", "COUNT", "AttendanceID", $cur[0]);
                        $ph_count_12 = getCountSum($lconn, $where . " AND Flag='Purple' AND (AOvertime) >= 43200 ", "COUNT", "AttendanceID", $cur[0]);
                        $off_aot = getCountSum($lconn, $where . " AND (Flag='Silver' OR Flag='Teal') ", "SUM", "(AOvertime)", $cur[0]);
                    }
                    if (getRegister($txtMACAddress, 7) == "57" || getRegister($txtMACAddress, 7) == "58" || getRegister($txtMACAddress, 7) == "59" || getRegister($txtMACAddress, 7) == "60") {
                        $wk_bonus_count = 0;
                        $start = "";
                        $end = "";
                        if (substr($txtLockDate, 6, 2) * 1 < 18) {
                            $start = substr($txtLockDate, 0, 6) . "01";
                            $end = substr($txtLockDate, 0, 6) . "15";
                        } else {
                            $start = substr($txtLockDate, 0, 6) . "16";
                            $end = substr($txtLockDate, 0, 6) . "31";
                        }
                        for ($j = 0; $j < 2; $j++) {
                            if ($j == 0) {
                                $wk_day_count = getCountSum($lconn, " FROM AttendanceMaster WHERE Flag <> 'Orange' AND ADate >= " . $start . " AND ADate <= " . getNextDay($start, 7), "COUNT", "AttendanceID", $cur[0]);
                                if (5 < $wk_day_count) {
                                    $wk_bonus_count++;
                                }
                            } else {
                                if (substr($end, 6, 2) == "31") {
                                    $wk_day_count = getCountSum($lconn, " FROM AttendanceMaster WHERE Flag <> 'Orange' AND ADate >= " . getLastDay($end, 7) . " AND ADate <= " . $end, "COUNT", "AttendanceID", $cur[0]);
                                } else {
                                    $wk_day_count = getCountSum($lconn, " FROM AttendanceMaster WHERE Flag <> 'Orange' AND ADate >= " . getLastDay($end, 6) . " AND ADate <= " . $end, "COUNT", "AttendanceID", $cur[0]);
                                }
                                if (5 < $wk_day_count) {
                                    $wk_bonus_count++;
                                }
                            }
                        }
                    }
                    if (getRegister($txtMACAddress, 7) == "39") {
                        $late_sum = getCountSum($lconn, $where . " AND AttendanceMaster.Normal<(AttendanceMaster.group_min*60) AND Day<>OT1 AND Day<>OT2 AND Flag<>'Purple' AND AttendanceMaster.Normal>0 ", "SUM", "((AttendanceMaster.group_min*60)-AttendanceMaster.Normal)", $cur[0]);
                        $early_sum = 0;
                    }
                    if (getRegister($txtMACAddress, 7) == "82" || getRegister($txtMACAddress, 7) == "103" || getRegister($txtMACAddress, 7) == "106" || getRegister($txtMACAddress, 7) == "107" || getRegister($txtMACAddress, 7) == "126" || getRegister($txtMACAddress, 7) == "57" || getRegister($txtMACAddress, 7) == "58" || getRegister($txtMACAddress, 7) == "59" || getRegister($txtMACAddress, 7) == "60" || getRegister($txtMACAddress, 7) == "117" || getRegister($txtMACAddress, 7) == "150" || getRegister($txtMACAddress, 7) == "3") {
                        $query__ = "DELETE FROM tblEmpWorkHours_VIRDI WHERE FROMDATE = '" . displayParadoxDate($last_cur[0]) . " 00:00:00' AND TODATE = '" . displayParadoxDate($txtLockDate) . " 00:00:00' AND EMPPAYROLLNO = '" . $cur[2] . "'";
                    } else {
                        $query__ = "DELETE FROM tblEmpWorkHours_VIRDI WHERE FROMDATE = '" . displayParadoxDate($last_cur[0]) . " 00:00:00' AND TODATE = '" . displayParadoxDate($txtLockDate) . " 00:00:00' AND EMPPAYROLLNO = '" . addZero($cur[0], $txtECodeLength) . "'";
                    }
                    if (!mssql_query($query__, $oconn)) {
                        if (getRegister($txtMACAddress, 7) == "82" || getRegister($txtMACAddress, 7) == "103" || getRegister($txtMACAddress, 7) == "106" || getRegister($txtMACAddress, 7) == "107" || getRegister($txtMACAddress, 7) == "126" || getRegister($txtMACAddress, 7) == "57" || getRegister($txtMACAddress, 7) == "58" || getRegister($txtMACAddress, 7) == "59" || getRegister($txtMACAddress, 7) == "60" || getRegister($txtMACAddress, 7) == "117" || getRegister($txtMACAddress, 7) == "150" || getRegister($txtMACAddress, 7) == "3") {
                            $query__ = "DELETE FROM Employee_WorkHour_VIRDI WHERE FROMDATE = '" . displayParadoxDate($last_cur[0]) . " 00:00:00' AND TODATE = '" . displayParadoxDate($txtLockDate) . " 00:00:00' AND Emp_Payroll_No = '" . $cur[2] . "'";
                        } else {
                            $query__ = "DELETE FROM Employee_WorkHour_VIRDI WHERE FROMDATE = '" . displayParadoxDate($last_cur[0]) . " 00:00:00' AND TODATE = '" . displayParadoxDate($txtLockDate) . " 00:00:00' AND Emp_Payroll_No = '" . addZero($cur[0], $txtECodeLength) . "'";
                        }
                        if (!mssql_query($query__, $oconn)) {
                            echo "\n\r" . $query__;
                        }
                    }
                    if ($normal == 0) {
                        $a_day = $WK_DAYS;
                    }
                    if (getRegister($txtMACAddress, 7) == "82" || getRegister($txtMACAddress, 7) == "103" || getRegister($txtMACAddress, 7) == "106" || getRegister($txtMACAddress, 7) == "107" || getRegister($txtMACAddress, 7) == "126" || getRegister($txtMACAddress, 7) == "57" || getRegister($txtMACAddress, 7) == "58" || getRegister($txtMACAddress, 7) == "59" || getRegister($txtMACAddress, 7) == "60" || getRegister($txtMACAddress, 7) == "117" || getRegister($txtMACAddress, 7) == "150" || getRegister($txtMACAddress, 7) == "3") {
                        $rgquery = "SELECT EmploymentType FROM tblEmployee WHERE EmpNo = '" . $cur[2] . "'";
                    } else {
                        $rgquery = "SELECT EmploymentType FROM tblEmployee WHERE EmpNo = '" . addZero($cur[0], $txtECodeLength) . "' OR EmpNo = '" . $cur[0] . "'";
                    }
                    if ($rgresult = mssql_query($rgquery, $oconn)) {
                        while ($rgcur = mssql_fetch_row($rgresult)) {
                            $regcal = $rgcur[0];
                        }
                    } else {
                        if (getRegister($txtMACAddress, 7) == "82" || getRegister($txtMACAddress, 7) == "103" || getRegister($txtMACAddress, 7) == "106" || getRegister($txtMACAddress, 7) == "107" || getRegister($txtMACAddress, 7) == "126" || getRegister($txtMACAddress, 7) == "57" || getRegister($txtMACAddress, 7) == "58" || getRegister($txtMACAddress, 7) == "59" || getRegister($txtMACAddress, 7) == "60" || getRegister($txtMACAddress, 7) == "117" || getRegister($txtMACAddress, 7) == "150" || getRegister($txtMACAddress, 7) == "3") {
                            $rgquery = "SELECT Employment_Type FROM tblEmployee WHERE Emp_Payroll_No = '" . $cur[2] . "'";
                        } else {
                            $rgquery = "SELECT Employment_Type FROM tblEmployee WHERE Emp_Payroll_No = '" . addZero($cur[0], $txtECodeLength) . "' OR Emp_Payroll_No = '" . $cur[0] . "'";
                        }
                        if ($rgresult = mssql_query($rgquery, $oconn)) {
                            while ($rgcur = mssql_fetch_row($rgresult)) {
                                $regcal = $rgcur[0];
                            }
                        } else {
                            echo "\n\r" . $rgquery;
                        }
                    }
                    if ($regcal == "") {
                        $regcal = 0;
                    }
                    if (getRegister($txtMACAddress, 7) == "82" || getRegister($txtMACAddress, 7) == "103" || getRegister($txtMACAddress, 7) == "106" || getRegister($txtMACAddress, 7) == "107" || getRegister($txtMACAddress, 7) == "126" || getRegister($txtMACAddress, 7) == "57" || getRegister($txtMACAddress, 7) == "58" || getRegister($txtMACAddress, 7) == "59" || getRegister($txtMACAddress, 7) == "60" || getRegister($txtMACAddress, 7) == "117" || getRegister($txtMACAddress, 7) == "150" || getRegister($txtMACAddress, 7) == "3") {
                        if ($csv == "csv") {
                            $data = $cur[2] . ";.;" . displayParadoxDate($last_cur[0]) . " 00:00:00;" . displayParadoxDate($txtLockDate) . " 00:00:00;" . $mm . ";" . $yyyy . ";" . $regcal . ";" . $normal . ";" . $ot_1 . ";" . ($late_sum + $early_sum) . ";" . $a_day . ";" . $ot1 . ";" . $ot2 . ";" . $ot3 . ";" . $ot4 . ";" . $v . ";" . $i . ";" . $b . ";" . $g . ";" . $y . ";" . $o . ";" . $r . ";" . $gr . ";" . $br . ";" . $pr . ";" . $mg . ";" . $tl . ";" . $aq . ";" . $sf . ";" . $ab . ";" . $gl . ";" . $vm . ";" . $sl . ";" . $mr . ";" . $pk . ";" . $night . ";" . $rotate . ";" . $late . ";" . $early . ";" . $grace . ";" . $ot_2 . ";" . $ot_3 . ";" . $ot_4 . ";" . round($normal / 3600, 2) . ";" . round($ot1 / 3600, 2) . ";" . round($ot2 / 3600, 2) . ";" . round($ot3 / 3600, 2) . ";" . round($ot4 / 3600, 2) . ";" . $WK_DAYS . ";" . $WKSAT_DAYS . ";" . $WKSUN_DAYS . ";" . $a_sat_day . ";" . $late_sum . ";" . $wk_bonus_count . ";" . $mcount . "\n";
                            fwrite($handle, $data);
                        } else {
                            $payroll_query = "INSERT INTO tblEmpWorkHours_VIRDI (EMPPAYROLLNO, EMPNAME, FROMDATE, TODATE, MONTHNO, YEARNO, ISREGULARCASUAL, NORMAL_WORKING_SECONDS, NORMAL_WORKING_DAYS, ABSENT_SECONDS, ABSENT_DAYS, OT1_SECONDS, OT2_SECONDS, OT3_SECONDS, OT4_SECONDS, LV1_COUNT, LV2_COUNT, LV3_COUNT, LV4_COUNT, LV5_COUNT, LV6_COUNT, LV7_COUNT, LV8_COUNT, LV9_COUNT, LV10_COUNT, LV11_COUNT, LV12_COUNT, LV13_COUNT, LV14_COUNT, LV15_COUNT, LV16_COUNT, LV17_COUNT, LV18_COUNT, LV19_COUNT, LV20_COUNT, NIGHT_COUNT, ROTATE_COUNT, LATE_COUNT, EARLY_COUNT, TOTAL_GRACE, OT2_DAYS, OT3_DAYS, OT4_DAYS, NORMAL_WORKING_HOURS, OT1_HOURS, OT2_HOURS, OT3_HOURS, OT4_HOURS, WK_DAYS, WKSAT_DAYS, WKSUN_DAYS, ABSENT_SAT_DAYS, LATE_SECONDS, WK_BONUS_COUNT, EXTRA_HOURS) VALUES ('" . $cur[2] . "', '.', '" . displayParadoxDate($last_cur[0]) . " 00:00:00', '" . displayParadoxDate($txtLockDate) . " 00:00:00', " . $mm . ", " . $yyyy . ", " . $regcal . ", " . $normal . ", " . $ot_1 . ", " . ($late_sum + $early_sum) . ", " . $a_day . ", " . $ot1 . ", " . $ot2 . ", " . $ot3 . ", " . $ot4 . ", " . $v . ", " . $i . ", " . $b . ", " . $g . ", " . $y . ", " . $o . ", " . $r . ", " . $gr . ", " . $br . ", " . $pr . ", " . $mg . ", " . $tl . ", " . $aq . ", " . $sf . ", " . $ab . ", " . $gl . ", " . $vm . ", " . $sl . ", " . $mr . ", " . $pk . ", " . $night . ", " . $rotate . ", " . $late . ", " . $early . ", " . $grace . ", " . $ot_2 . ", " . $ot_3 . ", " . $ot_4 . ", " . round($normal / 3600, 2) . ", " . round($ot1 / 3600, 2) . ", " . round($ot2 / 3600, 2) . ", " . round($ot3 / 3600, 2) . ", " . round($ot4 / 3600, 2) . ", " . $WK_DAYS . ", " . $WKSAT_DAYS . ", " . $WKSUN_DAYS . ", " . $a_sat_day . ", " . $late_sum . ", " . $wk_bonus_count . ", " . $mcount . ") ";
                        }
                    } else {
                        if (getRegister($txtMACAddress, 7) == "16") {
                            $payroll_query = "INSERT INTO tblEmpWorkHours_VIRDI (EMPPAYROLLNO, EMPNAME, FROMDATE, TODATE, MONTHNO, YEARNO, ISREGULARCASUAL, NORMAL_WORKING_SECONDS, NORMAL_WORKING_DAYS, ABSENT_SECONDS, ABSENT_DAYS, OT1_SECONDS, OT2_SECONDS, OT3_SECONDS, OT4_SECONDS, LV1_COUNT, LV2_COUNT, LV3_COUNT, LV4_COUNT, LV5_COUNT, LV6_COUNT, LV7_COUNT, LV8_COUNT, LV9_COUNT, LV10_COUNT, LV11_COUNT, LV12_COUNT, LV13_COUNT, LV14_COUNT, LV15_COUNT, LV16_COUNT, LV17_COUNT, LV18_COUNT, LV19_COUNT, LV20_COUNT, NIGHT_COUNT, ROTATE_COUNT, LATE_COUNT, EARLY_COUNT, TOTAL_GRACE, OT2_DAYS, OT3_DAYS, OT4_DAYS, NORMAL_WORKING_HOURS, OT1_HOURS, OT2_HOURS, OT3_HOURS, OT4_HOURS, WK_DAYS, WKSAT_DAYS, WKSUN_DAYS, ABSENT_SAT_DAYS, LATE_SECONDS, WK_BONUS_COUNT, EXTRA_HOURS, LVOFF18_COUNT8, LVOFF18_COUNT12, LV10_COUNT8, LV10_COUNT12, LV18_SECONDS) VALUES ('" . addZero($cur[0], $txtECodeLength) . "', '.', '" . displayParadoxDate($last_cur[0]) . " 00:00:00', '" . displayParadoxDate($txtLockDate) . " 00:00:00', " . $mm . ", " . $yyyy . ", " . $regcal . ", " . $normal . ", " . $ot_1 . ", " . ($late_sum + $early_sum) . ", " . $a_day . ", " . $ot1 . ", " . $ot2 . ", " . $ot3 . ", " . $ot4 . ", " . $v . ", " . $i . ", " . $b . ", " . $g . ", " . $y . ", " . $o . ", " . $r . ", " . $gr . ", " . $br . ", " . $pr . ", " . $mg . ", " . $tl . ", " . $aq . ", " . $sf . ", " . $ab . ", " . $gl . ", " . $vm . ", " . $sl . ", " . $mr . ", " . $pk . ", " . $night . ", " . $rotate . ", " . $late . ", " . $early . ", " . $grace . ", " . $ot_2 . ", " . $ot_3 . ", " . $ot_4 . ", " . round($normal / 3600, 2) . ", " . round($ot1 / 3600, 2) . ", " . round($ot2 / 3600, 2) . ", " . round($ot3 / 3600, 2) . ", " . round($ot4 / 3600, 2) . ", " . $WK_DAYS . ", " . $WKSAT_DAYS . ", " . $WKSUN_DAYS . ", " . $a_sat_day . ", " . $late_sum . ", " . $wk_bonus_count . ", " . $mcount . ", " . $off_count_8_12 . ", " . $off_count_12 . ", " . $ph_count_8_12 . ", " . $ph_count_12 . ", " . $off_aot . ") ";
                        } else {
                            $payroll_query = "INSERT INTO tblEmpWorkHours_VIRDI (EMPPAYROLLNO, EMPNAME, FROMDATE, TODATE, MONTHNO, YEARNO, ISREGULARCASUAL, NORMAL_WORKING_SECONDS, NORMAL_WORKING_DAYS, ABSENT_SECONDS, ABSENT_DAYS, OT1_SECONDS, OT2_SECONDS, OT3_SECONDS, OT4_SECONDS, LV1_COUNT, LV2_COUNT, LV3_COUNT, LV4_COUNT, LV5_COUNT, LV6_COUNT, LV7_COUNT, LV8_COUNT, LV9_COUNT, LV10_COUNT, LV11_COUNT, LV12_COUNT, LV13_COUNT, LV14_COUNT, LV15_COUNT, LV16_COUNT, LV17_COUNT, LV18_COUNT, LV19_COUNT, LV20_COUNT, NIGHT_COUNT, ROTATE_COUNT, LATE_COUNT, EARLY_COUNT, TOTAL_GRACE, OT2_DAYS, OT3_DAYS, OT4_DAYS, NORMAL_WORKING_HOURS, OT1_HOURS, OT2_HOURS, OT3_HOURS, OT4_HOURS, WK_DAYS, WKSAT_DAYS, WKSUN_DAYS, ABSENT_SAT_DAYS, LATE_SECONDS, WK_BONUS_COUNT, EXTRA_HOURS) VALUES ('" . addZero($cur[0], $txtECodeLength) . "', '.', '" . displayParadoxDate($last_cur[0]) . " 00:00:00', '" . displayParadoxDate($txtLockDate) . " 00:00:00', " . $mm . ", " . $yyyy . ", " . $regcal . ", " . $normal . ", " . $ot_1 . ", " . ($late_sum + $early_sum) . ", " . $a_day . ", " . $ot1 . ", " . $ot2 . ", " . $ot3 . ", " . $ot4 . ", " . $v . ", " . $i . ", " . $b . ", " . $g . ", " . $y . ", " . $o . ", " . $r . ", " . $gr . ", " . $br . ", " . $pr . ", " . $mg . ", " . $tl . ", " . $aq . ", " . $sf . ", " . $ab . ", " . $gl . ", " . $vm . ", " . $sl . ", " . $mr . ", " . $pk . ", " . $night . ", " . $rotate . ", " . $late . ", " . $early . ", " . $grace . ", " . $ot_2 . ", " . $ot_3 . ", " . $ot_4 . ", " . round($normal / 3600, 2) . ", " . round($ot1 / 3600, 2) . ", " . round($ot2 / 3600, 2) . ", " . round($ot3 / 3600, 2) . ", " . round($ot4 / 3600, 2) . ", " . $WK_DAYS . ", " . $WKSAT_DAYS . ", " . $WKSUN_DAYS . ", " . $a_sat_day . ", " . $late_sum . ", " . $wk_bonus_count . ", " . $mcount . ") ";
                        }
                    }
                    if (!mssql_query($payroll_query, $oconn)) {
                        if ($csv != "csv") {
                            if (getRegister($txtMACAddress, 7) == "82" || getRegister($txtMACAddress, 7) == "103" || getRegister($txtMACAddress, 7) == "106" || getRegister($txtMACAddress, 7) == "107" || getRegister($txtMACAddress, 7) == "126" || getRegister($txtMACAddress, 7) == "57" || getRegister($txtMACAddress, 7) == "58" || getRegister($txtMACAddress, 7) == "59" || getRegister($txtMACAddress, 7) == "60" || getRegister($txtMACAddress, 7) == "117" || getRegister($txtMACAddress, 7) == "150" || getRegister($txtMACAddress, 7) == "3") {
                                $payroll_query = "INSERT INTO Employee_WorkHour_VIRDI (TenantId, Cmp_Id, Emp_Id, Emp_Payroll_No, Emp_Name, FROMDATE, TODATE, MONTHNO, YEARNO, Employment_Type, NORMAL_WORKING_SECONDS, NORMAL_WORKING_DAYS, ABSENT_SECONDS, ABSENT_DAYS, OT1_SECONDS, OT2_SECONDS, OT3_SECONDS, OT4_SECONDS, LV1_COUNT, LV2_COUNT, LV3_COUNT, LV4_COUNT, LV5_COUNT, LV6_COUNT, LV7_COUNT, LV8_COUNT, LV9_COUNT, LV10_COUNT, LV11_COUNT, LV12_COUNT, LV13_COUNT, LV14_COUNT, LV15_COUNT, LV16_COUNT, LV17_COUNT, LV18_COUNT, LV19_COUNT, LV20_COUNT, NIGHT_COUNT, ROTATE_COUNT, LATE_COUNT, EARLY_COUNT, TOTAL_GRACE, OT2_DAYS, OT3_DAYS, OT4_DAYS, NORMAL_WORKING_HOURS, OT1_HOURS, OT2_HOURS, OT3_HOURS, OT4_HOURS, WK_DAYS, WKSAT_DAYS, WKSUN_DAYS, ABSENT_SAT_DAYS, LATE_SECONDS, WK_BONUS_COUNT, EXTRA_HOURS, ABSENT_SUN_DAYS) VALUES ('" . $co_code . "', '" . $co_code . "', '" . addZero($cur[2], $txtECodeLength) . "', '" . addZero($cur[2], $txtECodeLength) . "', '.', '" . displayParadoxDate($last_cur[0]) . " 00:00:00', '" . displayParadoxDate($txtLockDate) . " 00:00:00', " . $mm . ", " . $yyyy . ", " . $regcal . ", " . $normal . ", " . $ot_1 . ", " . ($late_sum + $early_sum) . ", " . $a_day . ", " . $ot1 . ", " . $ot2 . ", " . $ot3 . ", " . $ot4 . ", " . $v . ", " . $i . ", " . $b . ", " . $g . ", " . $y . ", " . $o . ", " . $r . ", " . $gr . ", " . $br . ", " . $pr . ", " . $mg . ", " . $tl . ", " . $aq . ", " . $sf . ", " . $ab . ", " . $gl . ", " . $vm . ", " . $sl . ", " . $mr . ", " . $pk . ", " . $night . ", " . $rotate . ", " . $late . ", " . $early . ", " . $grace . ", " . $ot_2 . ", " . $ot_3 . ", " . $ot_4 . ", " . round($normal / 3600, 2) . ", " . round($ot1 / 3600, 2) . ", " . round($ot2 / 3600, 2) . ", " . round($ot3 / 3600, 2) . ", " . round($ot4 / 3600, 2) . ", " . $WK_DAYS . ", " . $WKSAT_DAYS . ", " . $WKSUN_DAYS . ", " . $a_sat_day . ", " . $late_sum . ", " . $wk_bonus_count . ", " . $mcount . ", " . $a_sun_day . ") ";
                            } else {
                                if (getRegister($txtMACAddress, 7) == "16") {
                                    $payroll_query = "INSERT INTO Employee_WorkHour_VIRDI (TenantId, Cmp_Id, Emp_Id, Emp_Payroll_No, Emp_Name, FROMDATE, TODATE, MONTHNO, YEARNO, Employment_Type, NORMAL_WORKING_SECONDS, NORMAL_WORKING_DAYS, ABSENT_SECONDS, ABSENT_DAYS, OT1_SECONDS, OT2_SECONDS, OT3_SECONDS, OT4_SECONDS, LV1_COUNT, LV2_COUNT, LV3_COUNT, LV4_COUNT, LV5_COUNT, LV6_COUNT, LV7_COUNT, LV8_COUNT, LV9_COUNT, LV10_COUNT, LV11_COUNT, LV12_COUNT, LV13_COUNT, LV14_COUNT, LV15_COUNT, LV16_COUNT, LV17_COUNT, LV18_COUNT, LV19_COUNT, LV20_COUNT, NIGHT_COUNT, ROTATE_COUNT, LATE_COUNT, EARLY_COUNT, TOTAL_GRACE, OT2_DAYS, OT3_DAYS, OT4_DAYS, NORMAL_WORKING_HOURS, OT1_HOURS, OT2_HOURS, OT3_HOURS, OT4_HOURS, WK_DAYS, WKSAT_DAYS, WKSUN_DAYS, ABSENT_SAT_DAYS, LATE_SECONDS, WK_BONUS_COUNT, EXTRA_HOURS, ABSENT_SUN_DAYS, LVOFF18_COUNT8, LVOFF18_COUNT12, LV10_COUNT8, LV10_COUNT12, LV18_SECONDS) VALUES ('" . $co_code . "', '" . $co_code . "', '" . addZero($cur[0], $txtECodeLength) . "', '" . addZero($cur[0], $txtECodeLength) . "', '.', '" . displayParadoxDate($last_cur[0]) . " 00:00:00', '" . displayParadoxDate($txtLockDate) . " 00:00:00', " . $mm . ", " . $yyyy . ", " . $regcal . ", " . $normal . ", " . $ot_1 . ", " . ($late_sum + $early_sum) . ", " . $a_day . ", " . $ot1 . ", " . $ot2 . ", " . $ot3 . ", " . $ot4 . ", " . $v . ", " . $i . ", " . $b . ", " . $g . ", " . $y . ", " . $o . ", " . $r . ", " . $gr . ", " . $br . ", " . $pr . ", " . $mg . ", " . $tl . ", " . $aq . ", " . $sf . ", " . $ab . ", " . $gl . ", " . $vm . ", " . $sl . ", " . $mr . ", " . $pk . ", " . $night . ", " . $rotate . ", " . $late . ", " . $early . ", " . $grace . ", " . $ot_2 . ", " . $ot_3 . ", " . $ot_4 . ", " . round($normal / 3600, 2) . ", " . round($ot1 / 3600, 2) . ", " . round($ot2 / 3600, 2) . ", " . round($ot3 / 3600, 2) . ", " . round($ot4 / 3600, 2) . ", " . $WK_DAYS . ", " . $WKSAT_DAYS . ", " . $WKSUN_DAYS . ", " . $a_sat_day . ", " . $late_sum . ", " . $wk_bonus_count . ", " . $mcount . ", " . $a_sun_day . ", " . $off_count_8_12 . ", " . $off_count_12 . ", " . $ph_count_8_12 . ", " . $ph_count_12 . ", " . $off_aot . ") ";
                                } else {
                                    if (getRegister($txtMACAddress, 7) == "168") {
                                        $payroll_query = "INSERT INTO Employee_WorkHour_VIRDI (TenantId, Cmp_Id, Emp_Id, Emp_Payroll_No, Emp_Name, FROMDATE, TODATE, MONTHNO, YEARNO, Employment_Type, NORMAL_WORKING_SECONDS, NORMAL_WORKING_DAYS, ABSENT_SECONDS, ABSENT_DAYS, OT1_SECONDS, OT2_SECONDS, OT3_SECONDS, OT4_SECONDS, LV1_COUNT, LV2_COUNT, LV3_COUNT, LV4_COUNT, LV5_COUNT, LV6_COUNT, LV7_COUNT, LV8_COUNT, LV9_COUNT, LV10_COUNT, LV11_COUNT, LV12_COUNT, LV13_COUNT, LV14_COUNT, LV15_COUNT, LV16_COUNT, LV17_COUNT, LV18_COUNT, LV19_COUNT, LV20_COUNT, NIGHT_COUNT, ROTATE_COUNT, LATE_COUNT, EARLY_COUNT, TOTAL_GRACE, OT2_DAYS, OT3_DAYS, OT4_DAYS, NORMAL_WORKING_HOURS, OT1_HOURS, OT2_HOURS, OT3_HOURS, OT4_HOURS, WK_DAYS, WKSAT_DAYS, WKSUN_DAYS, ABSENT_SAT_DAYS, LATE_SECONDS, WK_BONUS_COUNT, EXTRA_HOURS, ABSENT_SUN_DAYS, Late60_absent) VALUES ('" . $co_code . "', '" . $co_code . "', '" . addZero($cur[0], $txtECodeLength) . "', '" . addZero($cur[0], $txtECodeLength) . "', '.', '" . displayParadoxDate($last_cur[0]) . " 00:00:00', '" . displayParadoxDate($txtLockDate) . " 00:00:00', " . $mm . ", " . $yyyy . ", " . $regcal . ", " . $normal . ", " . $ot_1 . ", " . ($late_sum + $early_sum) . ", " . $a_day . ", " . $ot1 . ", " . $ot2 . ", " . $ot3 . ", " . $ot4 . ", " . $v . ", " . $i . ", " . $b . ", " . $g . ", " . $y . ", " . $o . ", " . $r . ", " . $gr . ", " . $br . ", " . $pr . ", " . $mg . ", " . $tl . ", " . $aq . ", " . $sf . ", " . $ab . ", " . $gl . ", " . $vm . ", " . $sl . ", " . $mr . ", " . $pk . ", " . $night . ", " . $rotate . ", " . $late . ", " . $early . ", " . $grace . ", " . $ot_2 . ", " . $ot_3 . ", " . $ot_4 . ", " . round($normal / 3600, 2) . ", " . round($ot1 / 3600, 2) . ", " . round($ot2 / 3600, 2) . ", " . round($ot3 / 3600, 2) . ", " . round($ot4 / 3600, 2) . ", " . $WK_DAYS . ", " . $WKSAT_DAYS . ", " . $WKSUN_DAYS . ", " . $a_sat_day . ", " . $late_sum . ", " . $wk_bonus_count . ", " . $mcount . ", " . $a_sun_day . ", " . $late60 . ") ";
                                    } else { 
                                        if($cur[3] == 'SOMOTEX'){
                                            $codeemp = 1;
                                        }
                                        if($cur[3] == 'SONNEX'){
                                            $codeemp = 2;
                                        }
                                        if($cur[3] == 'MONTANA'){
                                            $codeemp = 3;
                                        }
                                        if($cur[3] == 'HERMES'){
                                            $codeemp = 4;
                                        }
                                        if($cur[3] == 'VINNIG'){
                                            $codeemp = 5;
                                        }
                                       echo  $payroll_query = "INSERT INTO Employee_WorkHour_VIRDI (TenantId, Cmp_Id, Emp_Id, Emp_Payroll_No, Emp_Name, FROMDATE, TODATE, MONTHNO, YEARNO, Employment_Type, NORMAL_WORKING_SECONDS, NORMAL_WORKING_DAYS, ABSENT_SECONDS, ABSENT_DAYS, OT1_SECONDS, OT2_SECONDS, OT3_SECONDS, OT4_SECONDS, LV1_COUNT, LV2_COUNT, LV3_COUNT, LV4_COUNT, LV5_COUNT, LV6_COUNT, LV7_COUNT, LV8_COUNT, LV9_COUNT, LV10_COUNT, LV11_COUNT, LV12_COUNT, LV13_COUNT, LV14_COUNT, LV15_COUNT, LV16_COUNT, LV17_COUNT, LV18_COUNT, LV19_COUNT, LV20_COUNT, NIGHT_COUNT, ROTATE_COUNT, LATE_COUNT, EARLY_COUNT, TOTAL_GRACE, OT2_DAYS, OT3_DAYS, OT4_DAYS, NORMAL_WORKING_HOURS, OT1_HOURS, OT2_HOURS, OT3_HOURS, OT4_HOURS, WK_DAYS, WKSAT_DAYS, WKSUN_DAYS, ABSENT_SAT_DAYS, LATE_SECONDS, WK_BONUS_COUNT, EXTRA_HOURS, ABSENT_SUN_DAYS) VALUES ('" . $codeemp . "', '" . $codeemp . "', '" . addZero($cur[0], $txtECodeLength) . "', '" . addZero($cur[0], $txtECodeLength) . "', '.', '" . displayParadoxDate($last_cur[0]) . " 00:00:00', '" . displayParadoxDate($txtLockDate) . " 00:00:00', " . $mm . ", " . $yyyy . ", " . $regcal . ", " . $normal . ", " . $ot_1 . ", " . ($late_sum + $early_sum) . ", " . $a_day . ", " . $ot1 . ", " . $ot2 . ", " . $ot3 . ", " . $ot4 . ", " . $v . ", " . $i . ", " . $b . ", " . $g . ", " . $y . ", " . $o . ", " . $r . ", " . $gr . ", " . $br . ", " . $pr . ", " . $mg . ", " . $tl . ", " . $aq . ", " . $sf . ", " . $ab . ", " . $gl . ", " . $vm . ", " . $sl . ", " . $mr . ", " . $pk . ", " . $night . ", " . $rotate . ", " . $late . ", " . $early . ", " . $grace . ", " . $ot_2 . ", " . $ot_3 . ", " . $ot_4 . ", " . round($normal / 3600, 2) . ", " . round($ot1 / 3600, 2) . ", " . round($ot2 / 3600, 2) . ", " . round($ot3 / 3600, 2) . ", " . round($ot4 / 3600, 2) . ", " . $WK_DAYS . ", " . $WKSAT_DAYS . ", " . $WKSUN_DAYS . ", " . $a_sat_day . ", " . $late_sum . ", " . $wk_bonus_count . ", " . $mcount . ", " . $a_sun_day . ") ";
                                        echo "<pre>";print_R($cur);die;
                                    }
                                }
                            }
                            if (!mssql_query($payroll_query, $oconn)) {
                                echo "\n\r" . $payroll_query;
                            }
                        }
                    }
                    if (getRegister($txtMACAddress, 7) == "39") {
                        $extra_hours = getCountSum($lconn, $where . " AND Overtime>'0' AND group_id = 9 AND Day<>OT1 AND Day<>OT2 AND Flag<>'Purple'", "COUNT", "AttendanceID", $cur[0]);
                        $query = "UPDATE tblEmpWorkHours_VIRDI SET EXTRA_HOURS = " . $extra_hours * 3600 . " WHERE EMPPAYROLLNO = '" . addZero($cur[0], $txtECodeLength) . "' AND MONTHNO = " . $mm . " AND YEARNO = " . $yyyy;
                        if (!mssql_query($query, $oconn)) {
                            $query = "UPDATE Employee_WorkHour_VIRDI SET EXTRA_HOURS = " . $extra_hours * 3600 . " WHERE EMP_PAYROLL_NO = '" . addZero($cur[0], $txtECodeLength) . "' AND MONTHNO = " . $mm . " AND YEARNO = " . $yyyy;
                            if (!mssql_query($query, $oconn)) {
                                if ($csv != "csv") {
                                    echo "\n\r" . $query;
                                }
                            }
                        }
                    }
                }
            }
            $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('Payroll Synch Attendance - Col: " . $migrate_cur[0] . ", Val: " . $migrate_cur[1] . ", From: " . displayDate($migrate_cur[2]) . ", To: " . displayDate($migrate_cur[3]) . ", Mon: " . $migrate_cur[4] . ", ', " . insertToday() . ", '" . getNow() . "')";
            updateIData($iconn, $query, true);
        }
        $payroll_query = "UPDATE tblempworkhours_virdi SET YearId = t1.YearId, MonthId = t1.MonthId, PeriodId = t1.PeriodId FROM tblmonth t1 INNER JOIN tblempworkhours_virdi AS t2 ON t2.FROMDATE = t1.FROMDate";
        if (!mssql_query($payroll_query, $oconn)) {
            $payroll_query = "UPDATE Employee_WorkHour_VIRDI SET Year_Id = t1.Year_Id, Month_Id = t1.Month_Id, Period_Id = t1.Period_Id FROM tblmonth t1 INNER JOIN Employee_WorkHour_VIRDI AS t2 ON t2.FROMDATE = t1.From_Date";
            $payroll_query = "UPDATE Employee_WorkHour_VIRDI SET Year_Id = t1.Year_Id, Month_Id = t1.Month_Id FROM tblmonth t1 INNER JOIN Employee_WorkHour_VIRDI AS t2 ON t2.FROMDATE = t1.From_Date";
            if (!mssql_query($payroll_query, $oconn)) {
                echo "\n\r" . $payroll_query;
            }
        }
        if ($csv == "csv") {
            fclose($handle);
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