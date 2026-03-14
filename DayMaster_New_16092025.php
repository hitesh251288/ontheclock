<?php
ob_start("ob_gzhandler");
//mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
set_time_limit(0);
ini_set('display_errors', '1');
ini_set('display_startup_errors', 1);
date_default_timezone_set("Africa/Algiers");
include "Functions.php";
function norm_yyyymmdd($d): string {
    $d = (string)($d ?? '');
    $d = preg_replace('/\D/', '', $d);
    if ($d === '') return '00000000';
    // keep first 8 digits, pad if short
    return str_pad(substr($d, 0, 8), 8, '0', STR_PAD_RIGHT);
}

function norm_hhmmss($t): string {
    $t = (string)($t ?? '');
    $t = preg_replace('/\D/', '', $t);
    if ($t === '') return '000000';
    return str_pad(substr($t, 0, 6), 6, '0', STR_PAD_RIGHT);
}

function ts_from_date_time($yyyymmdd, $hhmmss): int {
    $d = norm_yyyymmdd($yyyymmdd);
    $t = norm_hhmmss($hhmmss);
    $Y = (int)substr($d, 0, 4);
    $m = (int)substr($d, 4, 2);
    $D = (int)substr($d, 6, 2);
    $H = (int)substr($t, 0, 2);
    $i = (int)substr($t, 2, 2);
    $s = (int)substr($t, 4, 2);
    return mktime($H, $i, $s, $m, $D, $Y);
}

function cmp_hhmmss($t1, $t2): int {
    $a = norm_hhmmss($t1);
    $b = norm_hhmmss($t2);
    return $a <=> $b;
}

function secs_between($yyyymmdd, $from_his, $to_his): int {
    return ts_from_date_time($yyyymmdd, $to_his) - ts_from_date_time($yyyymmdd, $from_his);
}

function s($v): string { return (string)($v ?? ''); }

if (function_exists('mysqli_report')) {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
}

$second_execution = "";
$conn = openConnection();
$iconn = openIConnection();
$jconn = openIConnection();
$kconn = openIConnection();
//echo checkMAC($conn);die;
//print_R(checkMAC($conn));die;

if (checkMAC($conn) == false) {
    print "Un Registered Application. Process Terminated.";
    exit;
}
$query = "SELECT MinClockinPeriod, TotalDailyClockin, ExitTerminal, Project, FlagLimitType, LessLunchOT, NightShiftMaxOutTime, TotalExitClockin, NoExitException, NoBreakException, RotateShift, RotateShiftNextDay, MACAddress, EarlyInOTDayDate, LockDate, MinOTValue, EmployeeCodeLength, AutoAssignTerminal, AutoApproveOT, MaxOTValue, RoundOffAOT, MoveNS, UseShiftRoster, SRDay, SRScenario, PreApproveOTValue, AutoResetOT12, SanSatOT, Ex1, CAGR FROM OtherSettingMaster";
$result = selectData($conn, $query);
$txtMinClockinPeriod = $result[0];
$txtTotalDailyClockin = 0;
$lstExitTerminal = $result[2];
$lstProject = $result[3];
$lstFlagLimitType = $result[4];
$lstLessLunchOT = $result[5];
$txtNightShiftMaxOutTime = $result[6];
$txtTotalExitClockin = 0;
$lstNoExitException = $result[8];
$lstNoBreakException = $result[9];
$rotateShift = $result[10];
$txtMACAddress = $result[12];
$lstEarlyInOTDayDate = $result[13];
$txtLockDate = $result[14];
$txtMinOTValue = $result[15];
$txtECodeLength = $result[16];
$lstAutoAssignTerminal = $result[17];
$lstAutoApproveOT = $result[18];
$txtMaxOTValue = $result[19];
$lstRoundOffAOT = $result[20];
$lstMoveNS = $result[21];
$lstUseShiftRoster = $result[22];
$lstPreApproveOTValue = $result[25];
$lstAutoResetOT12 = $result[26];
$lstSanSatOT = $result[27];
$lstNoBreakExceptionOT = "";
$txtBackupPath = $result[28];
//$lstCAGR = $result[19];
$lstCAGR = $result[29];
$night_execution = false;

if (noTASoftware("", $txtMACAddress)) {
    $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('Day Master: NOT Executed - Un Registered Application', " . insertToday() . ", '" . getNow() . "')";
    updateIData($iconn, $query, true);
    print "Un Registered Application. Process Terminated.";
    exit;
}

$maxDate = insertToday();
if (getRegister($txtMACAddress, 7) == "144") {
    $unis_conn = mysqli_connect("localhost", "root", "namaste", "unis"); //UNIS
    if ($unis_conn != "") {
        $query = "TRUNCATE TABLE tcommanddown";
        updateIData($unis_conn, $query, true);
    }
}
$unis_conn = mysqli_connect("localhost", "root", "namaste", "unis");
if ($unis_conn != "") {
    $query = "SELECT id, name from Access.tgroup WHERE id NOT IN (SELECT C_Code FROM unis.tworktype WHERE C_Code <> '****')";
    $resultquery = mysqli_query($iconn, $query);
    while ($cur = mysqli_fetch_row($resultquery)) {
        $query = "INSERT INTO tworktype (C_Code, C_Name) VALUES ('" . $cur[0] . "', '" . $cur[1] . "')";
        updateIData($uconn, $query, true);
    }
}
$query = "SELECT MAX(PDate) FROM ProcessLog WHERE PType = 'DB Maintenance'";
$result = selectData($conn, $query);
if (getNextDay($result[0], 30) < $maxDate) {
    exec("php MaintainDB.php");
}
if (!(getRegister($txtMACAddress, 7) == "6" || getRegister($txtMACAddress, 7) == "36" || getRegister($txtMACAddress, 7) == "54" || getRegister($txtMACAddress, 7) == "55" || getRegister($txtMACAddress, 7) == "39" || getRegister($txtMACAddress, 7) == "165")) {
    $query = "SELECT PDate FROM ProcessLog WHERE PType = 'Backup' AND PDate = '" . $maxDate . "' ";
    $result = selectData($conn, $query);
    if ($result[0] != $maxDate) {
        exec("php Backup.php");
    }
}
if ($txtLockDate < getLastDay(insertToday(), 90)) {
    $txtLockDate = getLastDay(insertToday(), 90);
    $query = "UPDATE OtherSettingMaster SET LockDate = '" . $txtLockDate . "'";
    updateIData($iconn, $query, true);
}
migrateMaster($conn, $iconn);
$missed_shift_rotation_flag = false;
if ($rotateShift == "Yes") {
    $query = "SELECT MIN( RotateShiftNextDay ) , RTime FROM ShiftChangeMaster WHERE AE =1 GROUP BY RTime ORDER BY RTime";
    $result = selectData($conn, $query);
    if ($result[0] != "" && ($maxDate == $result[0] && $result[1] <= getNow() || $result[0] < $maxDate)) {
        $missed_shift_rotation_flag = true;
    }
}
$pay_off_employee = $_GET["txtID"];
if ($pay_off_employee != "" && is_numeric($pay_off_employee) == true) {
    $pay_off_employee = $pay_off_employee / 1024;
    print "<body onLoad=javascript:document.getElementById('img1').style.display='none'><div id='img1' align='center'>This Process may take a Long Time <br><br>Please DO NOT Close this Window</b></font><br><img src='img/processing.gif' name='horse' onClick=alert('ExecutingScript')></div>";
    print "<style>input{background-color:'#F0F0F0';font-family:'Verdana';font-size:'10';}</style>";
} else {
    $pay_off_employee = "";
    displayToday();
    getNow();
    print "\nScript Started: " . displayToday() . ", " . getNow() . " HRS";
    flush();
}
$query = "SELECT MAX(PDate) FROM ProcessLog WHERE PType = 'Daily'";
$result = selectData($conn, $query);
$last_process_date = $result[0];
if ($last_process_date == "") {
    $last_process_date = getLastDay(insertToday(), 1);
} else {
    if ($last_process_date == insertToday()) {
        $night_execution = true;
    }
}

$night_execution = true;
if ($last_process_date == insertToday() && $lstNoExitException == "Yes (Overide Single Clockin)") {
    $second_execution = "second_execution";
}
$query = "SELECT FlagDayRotationID FROM FlagDayRotation WHERE FlagDayRotationID NOT IN ( SELECT FlagDayRotationID FROM flagdayrotation , AttendanceMaster WHERE FlagDayRotation.e_id = AttendanceMaster.EmployeeID AND FlagDayRotation.e_date = AttendanceMaster.ADate AND FlagDayRotation.RecStat = 1 ) AND RecStat = 1 AND LENGTH(Flag) > 0 AND Flag IS NOT NULL";
$result = mysqli_query($conn, $query);
while ($cur = mysqli_fetch_row($result)) {
    $query = "UPDATE FlagDayRotation SET RecStat = 0 WHERE FlagDayRotationID = " . $cur[0];
    updateIData($iconn, $query, true);
}
$query = "SELECT id FROM tuser WHERE id > 0 AND id NOT IN (SELECT EmployeeID FROM EmployeeFlag) AND (PassiveType = 'ACT' OR PassiveType = 'FDA')";
$result = mysqli_query($conn, $query);
while ($cur = mysqli_fetch_row($result)) {
    $query = "INSERT INTO EmployeeFlag (EmployeeID) VALUES (" . $cur[0] . ")";
    updateIData($iconn, $query, true);
}
$query = "DELETE FROM UserDiv WHERE UserDiv.Div NOT IN (SELECT DISTINCT(company) FROM tuser)";
updateIData($iconn, $query, true);
$query = "DELETE FROM UserDept WHERE UserDept.Dept NOT IN (SELECT DISTINCT(dept) FROM tuser)";
updateIData($iconn, $query, true);
if ($lstFlagLimitType == "Jan 01") {
    $query = "DELETE FROM GroupFlagLimit WHERE Grp = 'Dept' AND Val NOT IN (SELECT DISTINCT (dept) FROM tuser)";
    if (updateIData($iconn, $query, true)) {
        $query = "DELETE FROM GroupFlagLimit WHERE Grp = 'Div' AND Val NOT IN (SELECT DISTINCT (company) FROM tuser)";
        if (updateIData($iconn, $query, true)) {
            $query = "DELETE FROM GroupFlagLimit WHERE Grp = 'Remark' AND Val NOT IN (SELECT DISTINCT (remark) FROM tuser)";
            if (updateIData($iconn, $query, true)) {
                $query = "DELETE FROM GroupFlagLimit WHERE Grp = 'SNo' AND Val NOT IN (SELECT DISTINCT (idno) FROM tuser)";
                if (updateIData($iconn, $query, true)) {
                    $query = "DELETE FROM GroupFlagLimit WHERE Grp = 'Phone' AND Val NOT IN (SELECT DISTINCT (phone) FROM tuser)";
                    if (updateIData($iconn, $query, true)) {
                        mysqli_commit($iconn);
                    }
                }
            }
        }
    }
} else {
    $query = "DELETE FROM GroupYearFlagLimit WHERE Grp = 'Dept' AND Val NOT IN (SELECT DISTINCT (dept) FROM tuser)";
    if (updateIData($iconn, $query, true)) {
        $query = "DELETE FROM GroupYearFlagLimit WHERE Grp = 'Div' AND Val NOT IN (SELECT DISTINCT (company) FROM tuser)";
        if (updateIData($iconn, $query, true)) {
            $query = "DELETE FROM GroupYearFlagLimit WHERE Grp = 'Remark' AND Val NOT IN (SELECT DISTINCT (remark) FROM tuser)";
            if (updateIData($iconn, $query, true)) {
                $query = "DELETE FROM GroupYearFlagLimit WHERE Grp = 'SNo' AND Val NOT IN (SELECT DISTINCT (idno) FROM tuser)";
                if (updateIData($iconn, $query, true)) {
                    $query = "DELETE FROM GroupYearFlagLimit WHERE Grp = 'Phone' AND Val NOT IN (SELECT DISTINCT (phone) FROM tuser)";
                    if (updateIData($iconn, $query, true)) {
                        mysqli_commit($iconn);
                    }
                }
            }
        }
    }
}
if ($lstFlagLimitType == "Jan 01") {
    $query = "SELECT Grp, Val, Violet, Indigo, Blue, Green, Yellow, Orange, Red, Gray, Brown, Purple, Magenta, Teal, Aqua, Safron, Amber, Gold, Vermilion, Maroon, Silver, Pink FROM GroupFlagLimit";
} else {
    $query = "SELECT Grp, Val, Violet, Indigo, Blue, Green, Yellow, Orange, Red, Gray, Brown, Purple, Magenta, Teal, Aqua, Safron, Amber, Gold, Vermilion, Maroon, Silver, Pink, Years FROM GroupYearFlagLimit ";
}
$result = mysqli_query($conn, $query);
while ($cur = mysqli_fetch_row($result)) {
    if ($cur[0] == "Dept") {
        $sub_query = "SELECT id FROM tuser WHERE dept = '" . $cur[1] . "' AND (PassiveType 'ACT' OR PassiveType = 'FDA')";
    } else {
        if ($cur[0] == "Div") {
            $sub_query = "SELECT id FROM tuser WHERE company = '" . $cur[1] . "' AND (PassiveType = 'ACT' OR PassiveType = 'FDA')";
        } else {
            if ($cur[0] == "Remark") {
                $sub_query = "SELECT id FROM tuser WHERE remark = '" . $cur[1] . "' AND (PassiveType = 'ACT' OR PassiveType = 'FDA')";
            } else {
                if ($cur[0] == "SNo") {
                    $sub_query = "SELECT id FROM tuser WHERE idno = '" . $cur[1] . "' AND (PassiveType = 'ACT' OR PassiveType = 'FDA')";
                } else {
                    if ($cur[0] == "Phone") {
                        $sub_query = "SELECT id FROM tuser WHERE phone = '" . $cur[1] . "' AND (PassiveType = 'ACT' OR PassiveType = 'FDA')";
                    }
                }
            }
        }
    }
    $year_condition = "";
    if ($lstFlagLimitType == "Employee Start Date") {
        $year_condition = " AND DATEDIFF(SUBSTR(SYSDATE(), 1, 10), CONCAT(SUBSTR(datelimit, 2, 4), '-',SUBSTR(datelimit, 6, 2), '-',SUBSTR(datelimit, 8, 2))) <= " . $cur[22] * 365 . " AND DATEDIFF(SUBSTR(SYSDATE(), 1, 10), CONCAT(SUBSTR(datelimit, 2, 4), '-',SUBSTR(datelimit, 6, 2), '-',SUBSTR(datelimit, 8, 2))) > " . ($cur[22] - 1) * 365 . " ";
    }
    $sub_result = mysqli_query($conn, $sub_query);
    while ($sub_cur = mysqli_fetch_row($sub_result)) {
        $query = "UPDATE EmployeeFlag, tuser SET Violet = '" . $cur[2] . "' WHERE EmployeeFlag.EmployeeID = tuser.id AND EmployeeID = '" . $sub_cur[0] . "' AND Violet = 365 " . $year_condition;
        if (updateIData($iconn, $query, true)) {
            $query = "UPDATE EmployeeFlag, tuser SET Indigo = '" . $cur[3] . "' WHERE EmployeeFlag.EmployeeID = tuser.id AND EmployeeID = '" . $sub_cur[0] . "'  AND Indigo = 365 " . $year_condition;
            if (updateIData($iconn, $query, true)) {
                $query = "UPDATE EmployeeFlag, tuser SET Blue = '" . $cur[4] . "' WHERE EmployeeFlag.EmployeeID = tuser.id AND EmployeeID = '" . $sub_cur[0] . "'  AND Blue = 365 " . $year_condition;
                if (updateIData($iconn, $query, true)) {
                    $query = "UPDATE EmployeeFlag, tuser SET Green = '" . $cur[5] . "' WHERE EmployeeFlag.EmployeeID = tuser.id AND EmployeeID = '" . $sub_cur[0] . "' AND Green = 365  " . $year_condition;
                    if (updateIData($iconn, $query, true)) {
                        $query = "UPDATE EmployeeFlag, tuser SET Yellow = '" . $cur[6] . "' WHERE EmployeeFlag.EmployeeID = tuser.id AND EmployeeID = '" . $sub_cur[0] . "' AND Yellow = 365  " . $year_condition;
                        if (updateIData($iconn, $query, true)) {
                            $query = "UPDATE EmployeeFlag, tuser SET Orange = '" . $cur[7] . "' WHERE EmployeeFlag.EmployeeID = tuser.id AND EmployeeID = '" . $sub_cur[0] . "' AND Orange = 365  " . $year_condition;
                            if (updateIData($iconn, $query, true)) {
                                $query = "UPDATE EmployeeFlag, tuser SET Red = '" . $cur[8] . "' WHERE EmployeeFlag.EmployeeID = tuser.id AND EmployeeID = '" . $sub_cur[0] . "' AND Red = 365  " . $year_condition;
                                if (updateIData($iconn, $query, true)) {
                                    $query = "UPDATE EmployeeFlag, tuser SET Gray = '" . $cur[9] . "' WHERE EmployeeFlag.EmployeeID = tuser.id AND EmployeeID = '" . $sub_cur[0] . "' AND Gray = 365  " . $year_condition;
                                    if (updateIData($iconn, $query, true)) {
                                        $query = "UPDATE EmployeeFlag, tuser SET Brown = '" . $cur[10] . "' WHERE EmployeeFlag.EmployeeID = tuser.id AND EmployeeID = '" . $sub_cur[0] . "' AND Brown = 365  " . $year_condition;
                                        if (updateIData($iconn, $query, true)) {
                                            $query = "UPDATE EmployeeFlag, tuser SET Purple = '" . $cur[11] . "' WHERE EmployeeFlag.EmployeeID = tuser.id AND EmployeeID = '" . $sub_cur[0] . "' AND Purple = 365  " . $year_condition;
                                            if (updateIData($iconn, $query, true)) {
                                                $query = "UPDATE EmployeeFlag, tuser SET Magenta = '" . $cur[12] . "' WHERE EmployeeFlag.EmployeeID = tuser.id AND EmployeeID = '" . $sub_cur[0] . "' AND Magenta = 365  " . $year_condition;
                                                if (updateIData($iconn, $query, true)) {
                                                    $query = "UPDATE EmployeeFlag, tuser SET Teal = '" . $cur[13] . "' WHERE EmployeeFlag.EmployeeID = tuser.id AND EmployeeID = '" . $sub_cur[0] . "' AND Teal = 365  " . $year_condition;
                                                    if (updateIData($iconn, $query, true)) {
                                                        $query = "UPDATE EmployeeFlag, tuser SET Aqua = '" . $cur[14] . "' WHERE EmployeeFlag.EmployeeID = tuser.id AND EmployeeID = '" . $sub_cur[0] . "' AND Aqua = 365  " . $year_condition;
                                                        if (updateIData($iconn, $query, true)) {
                                                            $query = "UPDATE EmployeeFlag, tuser SET Safron = '" . $cur[15] . "' WHERE EmployeeFlag.EmployeeID = tuser.id AND EmployeeID = '" . $sub_cur[0] . "' AND Safron = 365  " . $year_condition;
                                                            if (updateIData($iconn, $query, true)) {
                                                                $query = "UPDATE EmployeeFlag, tuser SET Amber = '" . $cur[16] . "' WHERE EmployeeFlag.EmployeeID = tuser.id AND EmployeeID = '" . $sub_cur[0] . "' AND Amber = 365  " . $year_condition;
                                                                if (updateIData($iconn, $query, true)) {
                                                                    $query = "UPDATE EmployeeFlag, tuser SET Gold = '" . $cur[17] . "' WHERE EmployeeFlag.EmployeeID = tuser.id AND EmployeeID = '" . $sub_cur[0] . "' AND Gold = 365  " . $year_condition;
                                                                    if (updateIData($iconn, $query, true)) {
                                                                        $query = "UPDATE EmployeeFlag, tuser SET Vermilion = '" . $cur[18] . "' WHERE EmployeeFlag.EmployeeID = tuser.id AND EmployeeID = '" . $sub_cur[0] . "' AND Vermilion = 365  " . $year_condition;
                                                                        if (updateIData($iconn, $query, true)) {
                                                                            $query = "UPDATE EmployeeFlag, tuser SET Silver = '" . $cur[19] . "' WHERE EmployeeFlag.EmployeeID = tuser.id AND EmployeeID = '" . $sub_cur[0] . "' AND Silver = 365  " . $year_condition;
                                                                            if (updateIData($iconn, $query, true)) {
                                                                                $query = "UPDATE EmployeeFlag, tuser SET Maroon = '" . $cur[20] . "' WHERE EmployeeFlag.EmployeeID = tuser.id AND EmployeeID = '" . $sub_cur[0] . "' AND Maroon = 365  " . $year_condition;
                                                                                if (updateIData($iconn, $query, true)) {
                                                                                    $query = "UPDATE EmployeeFlag, tuser SET Pink = '" . $cur[21] . "' WHERE EmployeeFlag.EmployeeID = tuser.id AND EmployeeID = '" . $sub_cur[0] . "' AND Pink = 365  " . $year_condition;
                                                                                    if (updateIData($iconn, $query, true)) {
                                                                                        mysqli_commit($iconn);
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
$query = "DELETE FROM GroupDiv WHERE GroupDiv.Div NOT IN (SELECT DISTINCT (company) FROM tuser)";
if (updateIData($iconn, $query, true)) {
    $query = "DELETE FROM GroupDept WHERE Dept NOT IN (SELECT DISTINCT (dept) FROM tuser)";
    if (updateIData($iconn, $query, true)) {
        $query = "DELETE FROM GroupRemark WHERE Remark NOT IN (SELECT DISTINCT (remark) FROM tuser)";
        if (updateIData($iconn, $query, true)) {
            $query = "DELETE FROM GroupIdNo WHERE IdNo NOT IN (SELECT DISTINCT (idno) FROM tuser)";
            if (updateIData($iconn, $query, true)) {
                $query = "DELETE FROM GroupPhone WHERE Phone NOT IN (SELECT DISTINCT (phone) FROM tuser)";
                if (updateIData($iconn, $query, true)) {
                    $query = "DELETE FROM DrillTerminal WHERE g_id NOT IN (SELECT g_id FROM tgate)";
//                    if (updateIData($iconn, $query, true)) {
//                        $query = "DELETE FROM DrillDiv WHERE DrillDiv.Div NOT IN (SELECT DISTINCT (company) FROM tuser)";
                    if (updateIData($iconn, $query, true)) {
                        $query = "DELETE FROM DrillDept WHERE Dept NOT IN (SELECT DISTINCT (dept) FROM tuser)";
                        if (updateIData($iconn, $query, true)) {
                            $query = "DELETE FROM DrillRemark WHERE Remark NOT IN (SELECT DISTINCT (remark) FROM tuser)";
                            if (updateIData($iconn, $query, true)) {
                                $query = "DELETE FROM DrillIdNo WHERE IdNo NOT IN (SELECT DISTINCT (idno) FROM tuser)";
                                if (updateIData($iconn, $query, true)) {
                                    $query = "DELETE FROM DrillPhone WHERE Phone NOT IN (SELECT DISTINCT (phone) FROM tuser)";
                                    if (updateIData($iconn, $query, true)) {
                                        mysqli_commit($iconn);
                                    }
                                }
                            }
                        }
//                        }
                    }
                }
            }
        }
    }
}
$insert_flag = true;
if ($lstAutoAssignTerminal == "Yes") {
    $query = "UPDATE tuser SET DEPT = UPPER(Dept)";
    if (updateIData($iconn, $query, true)) {
        $query = "DELETE FROM DeptGate";
        if (updateIData($iconn, $query, true)) {
            $query = "SELECT id from tgate ORDER BY id";
            $result = mysqli_query($conn, $query);
            while ($cur = mysqli_fetch_row($result)) {
                $sub_query = "SELECT DISTINCT(dept) FROM tuser WHERE dept NOT LIKE '' ORDER BY dept";
                $sub_result = mysqli_query($conn, $sub_query);
                while ($sub_cur = mysqli_fetch_row($sub_result)) {
                    $query = "INSERT INTO DeptGate (dept, g_id) VALUES ('" . $sub_cur[0] . "', '" . $cur[0] . "')";
                    if (!updateIData($iconn, $query, true)) {
                        $insert_flag = false;
                    }
                }
            }
            if ($insert_flag) {
                $text = "Auto Assigned ALL Terminals to ALL Departments";
                $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", 'admin', '" . $text . "')";
                updateIData($iconn, $query, true);
            }
        }
    }
}
$insert_flag = true;
$query = "DELETE FROM GroupExempt WHERE Grp = 'Dept' AND Val NOT IN (SELECT DISTINCT (dept) FROM tuser)";
if (updateIData($iconn, $query, true)) {
    $query = "DELETE FROM GroupExempt WHERE Grp = 'Div' AND Val NOT IN (SELECT DISTINCT (company) FROM tuser)";
    if (updateIData($iconn, $query, true)) {
        $query = "DELETE FROM GroupExempt WHERE Grp = 'Remark' AND Val NOT IN (SELECT DISTINCT (remark) FROM tuser)";
        if (updateIData($iconn, $query, true)) {
            $query = "DELETE FROM GroupExempt WHERE Grp = 'SNo' AND Val NOT IN (SELECT DISTINCT (idno) FROM tuser)";
            if (updateIData($iconn, $query, true)) {
                $query = "DELETE FROM GroupExempt WHERE Grp = 'Phone' AND Val NOT IN (SELECT DISTINCT (phone) FROM tuser)";
                if (updateIData($iconn, $query, true)) {
                    $query = "SELECT Module, Grp, Val FROM GroupExempt";
                    $result = mysqli_query($conn, $query);
                    $table = "";
                    while ($cur = mysqli_fetch_row($result)) {
                        if ($cur[0] == "PE") {
                            $table = "ProxyEmployeeExempt";
                        } else {
                            if ($cur[0] == "OE") {
                                $table = "OTEmployeeExempt";
                            } else {
                                if ($cur[0] == "OED") {
                                    $table = "OTEmployeeDateExempt";
                                }
                            }
                        }
                        if ($cur[1] == "Dept") {
                            $sub_query = "SELECT id FROM tuser WHERE dept = '" . $cur[2] . "' AND id NOT IN (SELECT EmployeeID FROM " . $table . ")";
                        } else {
                            if ($cur[1] == "Div") {
                                $sub_query = "SELECT id FROM tuser WHERE company = '" . $cur[2] . "' AND id NOT IN (SELECT EmployeeID FROM " . $table . ")";
                            } else {
                                if ($cur[1] == "Remark") {
                                    $sub_query = "SELECT id FROM tuser WHERE remark = '" . $cur[2] . "' AND id NOT IN (SELECT EmployeeID FROM " . $table . ")";
                                } else {
                                    if ($cur[1] == "SNo") {
                                        $sub_query = "SELECT id FROM tuser WHERE idno = '" . $cur[2] . "' AND id NOT IN (SELECT EmployeeID FROM " . $table . ")";
                                    } else {
                                        if ($cur[1] == "Phone") {
                                            $sub_query = "SELECT id FROM tuser WHERE phone = '" . $cur[2] . "' AND id NOT IN (SELECT EmployeeID FROM " . $table . ")";
                                        }
                                    }
                                }
                            }
                        }
                        $sub_result = mysqli_query($conn, $sub_query);
                        while ($sub_cur = mysqli_fetch_row($sub_result)) {
                            $query = "INSERT INTO " . $table . " (EmployeeID) VALUES ('" . $sub_cur[0] . "')";
                            if (updateIData($iconn, $query, true)) {
                                if ($table == "OTEmployeeExempt") {
                                    $query = "UPDATE tuser SET OT1 = '', OT2 = '' WHERE id = '" . $sub_cur[0] . "' ";
                                    if (!updateIData($jconn, $query, true)) {
                                        $insert_flag = false;
                                    }
                                }
                            } else {
                                $insert_flag = false;
                            }
                        }
                    }
                }
            }
        }
    }
}
$query = "DELETE FROM UserDept WHERE Dept NOT IN (SELECT DISTINCT dept FROM tuser)";
updateIData($iconn, $query, true);
$query = "DELETE FROM UserDiv WHERE `Div` NOT IN (SELECT DISTINCT company FROM tuser)";
updateIData($iconn, $query, true);
if (getRegister($txtMACAddress, 7) == "133" || getRegister($txtMACAddress, 7) == "39" || getRegister($txtMACAddress, 7) == "73") {
    $query = "UPDATE tenter SET tenter.e_mode = '2' WHERE tenter.e_mode = '3' AND tenter.p_flag = 0 AND tenter.e_date > '" . $txtLockDate . "' ";
    updateIData($iconn, $query, true);
} else {
    $query = "UPDATE tenter, tgate SET tenter.e_mode = '1' WHERE tenter.g_id = tgate.id AND tgate.name LIKE '%(IN)%' AND tenter.p_flag = 0 AND tenter.e_date > '" . $txtLockDate . "' ";
    updateIData($iconn, $query, true);
    $query = "UPDATE tenter, tgate SET tenter.e_mode = '2' WHERE tenter.g_id = tgate.id AND tgate.name LIKE '%(OUT)%' AND tenter.p_flag = 0 AND tenter.e_date > '" . $txtLockDate . "' ";
    updateIData($iconn, $query, true);
}
if (getRegister($txtMACAddress, 7) == "130") {
    $query = "UPDATE tenter, tgroup SET tenter.p_flag = '1' WHERE tenter.e_group = tgroup.id AND tgroup.ScheduleID = 7 AND tenter.p_flag = 0 AND ((tenter.e_time >= '120000' AND tenter.e_time <= '150000') OR (tenter.e_time >= '000001' AND tenter.e_time <= '030000')) AND tenter.e_date > '" . $txtLockDate . "' ";
    updateIData($iconn, $query, true);
}
$query = "SELECT id FROM tgroup WHERE DNT = 'Yes' AND NightFlag = 0 AND id > 2";
$result = selectData($conn, $query);
$query = "UPDATE tenter, tgate, tgroup SET tenter.e_group = " . (int) $result[0] . " WHERE tenter.g_id = tgate.id AND tgate.name LIKE '%(D)%' AND tenter.p_flag = 0 AND tgroup.id = tenter.e_group AND tgroup.DNT = 'Yes' AND tenter.e_date > '" . $txtLockDate . "' ";
/* $query = "
  UPDATE tenter
  JOIN tgate ON tenter.g_id = tgate.id
  JOIN tgroup ON tgroup.id = tenter.e_group
  SET tenter.e_group = " . $result[0] . "
  WHERE tgate.name LIKE '%(D)%'
  AND tenter.p_flag = 0
  AND tgroup.DNT = 'Yes'
  AND tenter.e_date > '" . $txtLockDate . "'
  "; */
$newGroupValue = $result[0]; // Replace this with the actual value you want to set  
//echo $query = "UPDATE tenter  
//JOIN tgate ON tenter.g_id = tgate.id  
//JOIN tgroup ON tgroup.id = tenter.e_group  
//SET tenter.e_group = " . (int)$result[0] . "   
//WHERE tgate.name LIKE '%(D)%'  
//AND tenter.p_flag = 0  
//AND tgroup.DNT = 'Yes'  
//AND tenter.e_date > '" . $txtLockDate . "'"; 
updateIData($iconn, $query, true);

// 1. Fetch group ID
$query = "SELECT id FROM tgroup WHERE DNT = 'Yes' AND NightFlag = 1 AND id > 2 LIMIT 1";
$result = selectData($conn, $query);

$egrpValue = 0;
if ($result && isset($result[0])) {
    $egrpValue = (int) $result[0];
}

// 2. Update tenter.e_group
if ($egrpValue > 0) {
    $query = "
        UPDATE tenter
        INNER JOIN tgate ON tenter.g_id = tgate.id
        INNER JOIN tgroup ON tgroup.id = tenter.e_group
        SET tenter.e_group = $egrpValue
        WHERE tgate.name LIKE '%(N)%'
          AND tenter.p_flag = 0
          AND tgroup.DNT = 'Yes'
          AND tenter.e_date > '" . $txtLockDate . "'
    ";
    updateIData($iconn, $query, true);
}

// 3. Update p_flag
$query = "
    UPDATE tenter
    INNER JOIN tgate ON tenter.g_id = tgate.id
    SET tenter.p_flag = 1
    WHERE tgate.Meal = 1
      AND tenter.p_flag = 0
      AND tenter.e_date > '" . $txtLockDate . "'
";
updateIData($iconn, $query, true);

// 4. Process date
$process_date = $last_process_date;

while ($process_date <= insertToday()) {
    $maxDate = $process_date;
    $process_flag = false;
    $pflag = "";

    if ($missed_shift_rotation_flag) {
        $missed_shift_rotation_query = "SELECT DISTINCT(idf) 
                                        FROM ShiftChangeMaster 
                                        WHERE RotateShiftNextDay = " . (int) $maxDate;
        $missed_shift_rotation_result = mysqli_query($conn, $missed_shift_rotation_query);

        if ($missed_shift_rotation_result) {
            while ($missed_shift_rotation_cur = mysqli_fetch_row($missed_shift_rotation_result)) {
                if (is_numeric($missed_shift_rotation_cur[0]) && $missed_shift_rotation_cur[0] > 0) {
                    // Escape argument to prevent shell issues
                    $shiftId = escapeshellarg($missed_shift_rotation_cur[0]);
                    exec("php RotateShift.php " . $shiftId);

                    $query = "INSERT INTO ProcessLog (PType, PDate, PTime) 
                              VALUES ('Auto Shift Rotation', " . (int) $maxDate . ", '" . getNow() . "')";
                    updateIData($iconn, $query, true);
                }
            }
        }
    }

    if ($rotateShift === "Yes") {
        $query = "SELECT DISTINCT(idf), RotateShiftNextDay 
                  FROM ShiftChangeMaster 
                  WHERE AE = 1 AND SRScenario = 'Morning - 2 Shifts (No Day Shift on Rotation Day)'";
        $result = mysqli_query($conn, $query);

        if ($result) {
            while ($cur = mysqli_fetch_row($result)) {
                $query = "UPDATE tenter 
                          SET e_group = (
                              SELECT DISTINCT(tgroup.id) 
                              FROM tgroup, ShiftChangeMaster 
                              WHERE tgroup.id = ShiftChangeMaster.id 
                              AND ShiftChangeMaster.idf = " . (int) $cur[0] . " 
                              AND tgroup.NightFlag = 1
                          )
                          WHERE p_flag = 0 
                          AND e_etc <> 'P' 
                          AND e_group = (
                              SELECT DISTINCT(tgroup.id) 
                              FROM tgroup, ShiftChangeMaster 
                              WHERE tgroup.id = ShiftChangeMaster.id 
                              AND ShiftChangeMaster.idf = " . (int) $cur[0] . " 
                              AND tgroup.NightFlag = 0
                          )
                          AND e_date = '" . mysqli_real_escape_string($conn, getLastDay($cur[1], 7)) . "'";

                updateIData($iconn, $query, true);

                $query = "UPDATE tenter, tuser, ShiftChangeMaster 
                          SET tenter.e_group = tuser.group_id 
                          WHERE tenter.e_id = tuser.id 
                          AND tenter.p_flag = 0 
                          AND (tenter.e_etc <> 'P' OR tenter.e_etc IS NULL) 
                          AND tenter.e_date > '" . mysqli_real_escape_string($conn, getLastDay($cur[1], 7)) . "' 
                          AND tenter.e_date < '" . mysqli_real_escape_string($conn, $cur[1]) . "' 
                          AND tuser.group_id = ShiftChangeMaster.id 
                          AND ShiftChangeMaster.idf = '" . (int) $cur[0] . "'";

                updateIData($iconn, $query, true);
            }
        }
        $query = "SELECT DISTINCT(idf), RotateShiftNextDay, RTime FROM ShiftChangeMaster WHERE AE = 1 AND SRScenario = 'Evening - 2 Shifts' ";
        $result = mysqli_query($conn, $query);
        while ($cur = mysqli_fetch_row($result)) {
            $eid = "";
            $query = "SELECT tenter.ed, tenter.e_id, tenter.e_time FROM tenter, tuser WHERE tenter.e_id = tuser.id AND tenter.p_flag = 0 AND (tenter.e_etc <> 'P' OR tenter.e_etc IS NULL) AND tenter.e_date = '" . getLastDay($cur[1], 7) . "' AND tuser.group_id = (SELECT DISTINCT(tgroup.id) FROM tgroup, ShiftChangeMaster WHERE tgroup.id = ShiftChangeMaster.id AND ShiftChangeMaster.idf = " . $cur[0] . " AND tgroup.NightFlag = 1) ORDER BY tenter.e_id, tenter.e_time DESC ";
            $sub_result = mysqli_query($jconn, $query);
            while ($sub_cur = mysqli_fetch_row($sub_result)) {
                if ($eid != $sub_cur[1]) {
                    $super_sub_query = "SELECT COUNT(ed) FROM tenter, tgate WHERE tenter.e_id = " . $sub_cur[1] . " AND tenter.e_date = '" . getLastDay($cur[1], 7) . "' AND tenter.p_flag = 0 AND tenter.g_id = tgate.id AND tgate.exit = 0 ";
                    $super_sub_result = selectData($conn, $super_sub_query);
                    if (2 < $super_sub_result[0]) {
                        $query = "UPDATE tenter SET e_group = (SELECT DISTINCT(tgroup.id) FROM tgroup, ShiftChangeMaster WHERE tgroup.id = ShiftChangeMaster.id AND ShiftChangeMaster.idf = " . $cur[0] . " AND tgroup.NightFlag = 1) WHERE ed = '" . $sub_cur[0] . "' AND p_flag = 0 AND e_etc <> 'P' AND e_time > '" . $txtNightShiftMaxOutTime . "00' ";
                        updateIData($iconn, $query, true);
                    } else {
                        $query = "UPDATE tenter SET e_group = (SELECT DISTINCT(tgroup.id) FROM tgroup, ShiftChangeMaster WHERE tgroup.id = ShiftChangeMaster.id AND ShiftChangeMaster.idf = " . $cur[0] . " AND tgroup.NightFlag = 0) WHERE ed = '" . $sub_cur[0] . "' AND p_flag = 0 AND e_etc <> 'P' AND e_time > '" . $txtNightShiftMaxOutTime . "00' ";
                        updateIData($iconn, $query, true);
                    }
                    $query = "UPDATE tenter SET e_group = (SELECT DISTINCT(tgroup.id) FROM tgroup, ShiftChangeMaster WHERE tgroup.id = ShiftChangeMaster.id AND ShiftChangeMaster.idf = " . $cur[0] . " AND tgroup.NightFlag = 1) WHERE ((e_time > '" . $cur[2] . "' AND e_date >= " . getLastDay($cur[1], 7) . ") OR (e_date > " . getLastDay($cur[1], 7) . " AND e_date <= " . $cur[1] . ")) AND e_id = '" . $sub_cur[1] . "' AND p_flag = 0 AND e_etc <> 'P' ";
                    updateIData($iconn, $query, true);
                    $query = "UPDATE tenter SET e_group = (SELECT DISTINCT(tgroup.id) FROM tgroup, ShiftChangeMaster WHERE tgroup.id = ShiftChangeMaster.id AND ShiftChangeMaster.idf = " . $cur[0] . " AND tgroup.NightFlag = 0) WHERE e_id = '" . $sub_cur[1] . "' AND p_flag = 0 AND e_etc <> 'P' AND e_date > " . getLastDay($cur[1], 14) . " AND e_date < " . getLastDay($cur[1], 7);
                    updateIData($iconn, $query, true);
                }
                $eid = $sub_cur[1];
            }
            $query = "UPDATE tenter AS A, tuser AS B SET A.e_group = (SELECT DISTINCT(tgroup.id) FROM tgroup, ShiftChangeMaster WHERE tgroup.id = ShiftChangeMaster.id AND ShiftChangeMaster.idf = " . $cur[0] . " AND tgroup.NightFlag = 1) WHERE A.e_id = B.id AND A.p_flag = 0 AND A.e_etc <> 'P' AND B.group_id = (SELECT DISTINCT(tgroup.id) FROM tgroup, ShiftChangeMaster WHERE tgroup.id = ShiftChangeMaster.id AND ShiftChangeMaster.idf = " . $cur[0] . " AND tgroup.NightFlag = 1) AND A.e_date > '" . getLastDay($cur[1], 7) . "' AND A.e_date < " . $cur[1];
            updateIData($iconn, $query, true);
            $query = "UPDATE tenter AS A, tuser AS B SET A.e_group = (SELECT DISTINCT(tgroup.id) FROM tgroup, ShiftChangeMaster WHERE tgroup.id = ShiftChangeMaster.id AND ShiftChangeMaster.idf = " . $cur[0] . " AND tgroup.NightFlag = 0) WHERE A.e_id = B.id AND A.p_flag = 0 AND A.e_etc <> 'P' AND B.group_id = (SELECT DISTINCT(tgroup.id) FROM tgroup, ShiftChangeMaster WHERE tgroup.id = ShiftChangeMaster.id AND ShiftChangeMaster.idf = " . $cur[0] . " AND tgroup.NightFlag = 1) AND A.e_date > '" . getLastDay($cur[1], 14) . "' AND A.e_date < '" . getLastDay($cur[1], 7) . "' ";
            updateIData($iconn, $query, true);
            $query = "UPDATE tenter AS A, tuser AS B SET A.e_group = (SELECT DISTINCT(tgroup.id) FROM tgroup, ShiftChangeMaster WHERE tgroup.id = ShiftChangeMaster.id AND ShiftChangeMaster.idf = " . $cur[0] . " AND tgroup.NightFlag = 0) WHERE A.e_id = B.id AND A.p_flag = 0 AND A.e_etc <> 'P' AND B.group_id = (SELECT DISTINCT(tgroup.id) FROM tgroup, ShiftChangeMaster WHERE tgroup.id = ShiftChangeMaster.id AND ShiftChangeMaster.idf = " . $cur[0] . " AND tgroup.NightFlag = 0) AND A.e_date > '" . getLastDay($cur[1], 7) . "' AND A.e_date < " . $cur[1];
            updateIData($iconn, $query, true);
            $query = "UPDATE tenter AS A, tuser AS B SET A.e_group = (SELECT DISTINCT(tgroup.id) FROM tgroup, ShiftChangeMaster WHERE tgroup.id = ShiftChangeMaster.id AND ShiftChangeMaster.idf = " . $cur[0] . " AND tgroup.NightFlag = 1) WHERE A.e_id = B.id AND A.p_flag = 0 AND A.e_etc <> 'P' AND B.group_id = (SELECT DISTINCT(tgroup.id) FROM tgroup, ShiftChangeMaster WHERE tgroup.id = ShiftChangeMaster.id AND ShiftChangeMaster.idf = " . $cur[0] . " AND tgroup.NightFlag = 0) AND A.e_date > '" . getLastDay($cur[1], 14) . "' AND A.e_date < '" . getLastDay($cur[1], 7) . "' ";
            updateIData($iconn, $query, true);
        }
        $query = "UPDATE tenter, SRA SET tenter.e_group = SRA.e_group WHERE tenter.p_flag = 0 AND (tenter.e_etc <> 'P' OR tenter.e_etc IS NULL) AND tenter.e_id = SRA.e_id AND UNIX_TIMESTAMP(CONCAT(SUBSTR(tenter.e_date, 1, 4), '-', SUBSTR(tenter.e_date, 5, 2), '-', SUBSTR(tenter.e_date, 7, 2), ' ', SUBSTR(tenter.e_time, 1, 2), '-', SUBSTR(tenter.e_time, 3, 2), '-', SUBSTR(tenter.e_time, 5, 2))) >= UNIX_TIMESTAMP(SRA.gFrom) AND UNIX_TIMESTAMP(CONCAT(SUBSTR(tenter.e_date, 1, 4), '-', SUBSTR(tenter.e_date, 5, 2), '-', SUBSTR(tenter.e_date, 7, 2), ' ', SUBSTR(tenter.e_time, 1, 2), '-', SUBSTR(tenter.e_time, 3, 2), '-', SUBSTR(tenter.e_time, 5, 2))) <= UNIX_TIMESTAMP(SRA.gTo) ";
        updateIData($iconn, $query, true);
    } else {
        if ($lstUseShiftRoster != "Yes" && $lstCAGR != "Yes") {
            $query = "UPDATE tenter, tuser SET tenter.e_group = tuser.group_id WHERE tenter.e_id = tuser.id AND tenter.e_date >= '" . $min_date . "' AND tenter.p_flag = 0 AND (tenter.e_etc <> 'P' OR tenter.e_etc IS NULL) AND tenter.e_date <= '" . $max_date . "' AND tenter.e_group <> tuser.group_id ";
            updateIData($iconn, $query, true);
        }
    }
    if ($lstUseShiftRoster == "Yes" || $lstCAGR == "Yes") {
        if ($lstCAGR == "Yes") {
            $query = "SELECT tuser.id, CAGRotation.group_id FROM tuser, CAGRotation WHERE tuser.CAGID = CAGRotation.CAGID AND CAGRotation.e_date = " . $maxDate;
            $result = mysqli_query($conn, $query);
            while ($cur = mysqli_fetch_row($result)) {
                $query = "INSERT INTO ShiftRoster (e_id, e_date, e_group) VALUES ('" . $cur[0] . "', '" . $maxDate . "', '" . $cur[1] . "')";
                updateIData($iconn, $query, true);
            }
        }
        $query = "UPDATE tuser, ShiftRoster SET tuser.group_id = ShiftRoster.e_group WHERE tuser.id = ShiftRoster.e_id AND ShiftRoster.e_date = " . $maxDate;
        updateIData($iconn, $query, true);
        $query = "UPDATE tenter, ShiftRoster, tgroup SET tenter.e_group = ShiftRoster.e_group WHERE tenter.e_id = ShiftRoster.e_id AND tenter.e_date = ShiftRoster.e_date AND tenter.p_flag = 0 AND (tenter.e_etc <> 'P' OR tenter.e_etc IS NULL) AND ShiftRoster.e_group = tgroup.id AND tgroup.NightFlag = 0";
        updateIData($iconn, $query, true);
        $query = "UPDATE tenter, ShiftRoster, tgroup SET tenter.e_group = ShiftRoster.e_group WHERE tenter.e_id = ShiftRoster.e_id AND tenter.e_date = ShiftRoster.e_date AND tenter.p_flag = 0 AND (tenter.e_etc <> 'P' OR tenter.e_etc IS NULL) AND ShiftRoster.e_group = tgroup.id AND tgroup.NightFlag = 1 AND tenter.e_time > '" . $txtNightShiftMaxOutTime . "00'";
        updateIData($iconn, $query, true);
        $query = "SELECT ShiftRoster.e_id, ShiftRoster.e_date, ShiftRoster.e_group FROM ShiftRoster, tgroup WHERE ShiftRoster.e_group = tgroup.id AND tgroup.NightFlag = 1 AND e_date <= '" . $maxDate . "' AND e_date > '" . $txtLockDate . "' ";
        $result = mysqli_query($conn, $query);
        while ($cur = mysqli_fetch_row($result)) {
            $nextDate = getNextDay($cur[1], 1);
            $dayNight = false;
            $query = "SELECT tenter.ed, tenter.e_group, tenter.e_time FROM ShiftRoster, tgroup, tenter WHERE tenter.e_id = ShiftRoster.e_id AND tenter.e_date = ShiftRoster.e_date AND tenter.p_flag = 0 AND (tenter.e_etc <> 'P' OR tenter.e_etc IS NULL) AND ShiftRoster.e_group = tgroup.id AND tgroup.NightFlag = 0 AND ShiftRoster.e_id = '" . $cur[0] . "' AND ShiftRoster.e_date = '" . $nextDate . "' ORDER BY tenter.e_time ";
            $counter = 0;
            $etime = "";
            $sub_result = mysqli_query($jconn, $query);
            while ($sub_cur = mysqli_fetch_row($sub_result)) {
                $dayNight = true;
                if (cmp_hhmmss($sub_cur[2], $txtNightShiftMaxOutTime . "00") < 0 && ($counter == 0 || $etime != "" &&  (ts_from_date_time($nextDate, $sub_cur[2]) - ts_from_date_time($nextDate, $etime)) < (int)$txtMinClockinPeriod)) {
                    $query = "UPDATE tenter SET tenter.e_group = '" . $cur[2] . "' WHERE ed = '" . $sub_cur[0] . "' ";
                    updateIData($iconn, $query, true);
                } else {
                    $query = "UPDATE tenter SET tenter.e_group = '" . $sub_cur[1] . "' WHERE ed = '" . $sub_cur[0] . "' ";
                    updateIData($iconn, $query, true);
                }
                $counter++;
                $etime = $sub_cur[2];
            }
            if ($dayNight == false) {
                $query = "UPDATE tenter SET tenter.e_group = '" . $cur[2] . "' WHERE tenter.e_id = '" . $cur[0] . "' AND tenter.e_date = '" . $nextDate . "' AND tenter.p_flag = 0 AND (tenter.e_etc <> 'P' OR tenter.e_etc IS NULL) AND tenter.e_time < '" . $txtNightShiftMaxOutTime . "00'";
                updateIData($iconn, $query, true);
            }
        }
    }
    $query = "REPLACE INTO tgroup (id, name, reg_date, timelimit, Start, GraceTo, FlexiBreak, Close, ShiftTypeID, ScheduleID, WorkMin) VALUES (2, 'OFF', '" . insertToday() . "" . getNow() . "', '00002359', '1200', '1200', 0, '1600', 1, 5, 480) ";
    if (updateData($conn, $query, true) == false) {
        
    }
    $query = "SELECT FlagDayRotation.e_id, FlagDayRotation.g_id, FlagDayRotation.Flag, 
                 FlagDayRotation.e_date, FlagDayRotation.Rotate, FlagDayRotation.Remark, 
                 FlagDayRotation.OT, tuser.group_id, FlagDayRotation.OTH 
          FROM FlagDayRotation
          INNER JOIN tuser ON FlagDayRotation.e_id = tuser.id
          WHERE (
                   (FlagDayRotation.e_date <= " . intval($maxDate) . " 
                       AND FlagDayRotation.Flag NOT IN (
                           SELECT FlagLink FROM FlagTitle WHERE FlagLink IS NOT NULL
                       )
                   ) 
                   OR 
                   (FlagDayRotation.e_date < " . intval(getLastDay($maxDate, 1)) . " 
                       AND FlagDayRotation.Flag IN (
                           SELECT FlagLink FROM FlagTitle WHERE FlagLink IS NOT NULL
                       )
                   )
                )
            AND FlagDayRotation.RecStat = 0 
            AND FlagDayRotation.e_date > " . intval($txtLockDate) . " 
          ORDER BY FlagDayRotation.e_id, FlagDayRotation.e_date";

    $result = mysqli_query($conn, $query);

    while ($cur = mysqli_fetch_row($result)) {
        $recStat = false;
        $insert_shift = 2;

        // safer check for empty/null flag
        if (empty($cur[2])) {
            $recStat = false;
        } else {
            if (insertAttendance(
                            $conn, $iconn, intval($cur[0]), displayDate($cur[3]), displayDate($cur[3]), $insert_shift, $cur[2], $cur[1]
                    )) {
                // Remark update if present
                if (!empty($cur[5]) && $cur[5] !== ".") {
                    $query = "UPDATE AttendanceMaster 
                          SET Remark = '" . mysqli_real_escape_string($iconn, $cur[5]) . "' 
                          WHERE EmployeeID = '" . intval($cur[0]) . "' 
                          AND ADate = '" . intval($cur[3]) . "' 
                          AND Flag = '" . mysqli_real_escape_string($iconn, $cur[2]) . "'";
                    updateIData($iconn, $query, true);
                }

                // Overtime update (cast to float for PHP 8.2 safety)
                $aOvertime = floatval($cur[8]) * 3600;
                $query = "UPDATE AttendanceMaster 
                      SET AOvertime = '" . $aOvertime . "' 
                      WHERE EmployeeID = '" . intval($cur[0]) . "' 
                      AND ADate = '" . intval($cur[3]) . "' 
                      AND Flag = '" . mysqli_real_escape_string($iconn, $cur[2]) . "'";
                updateIData($iconn, $query, true);

                // Rotate shift
                if (intval($cur[4]) === 1 && intval($maxDate) === intval($cur[3])) {
                    rotateShift($conn, $iconn, intval($cur[0]), $cur[7]);
                }

                $recStat = true;
            }
        }

        if ($recStat) {
            $query = "UPDATE FlagDayRotation 
                  SET RecStat = 1 
                  WHERE e_id = " . intval($cur[0]) . " 
                  AND e_date = " . intval($cur[3]);
            updateIData($iconn, $query, true);
        }
    }
    $query = "SELECT DrillMasterID, DrillDate, DrillTimeFrom, DrillTimeTo 
          FROM DrillMaster 
          WHERE DrillDate < '" . $maxDate . "' 
          AND DrillDate >= '" . $last_process_date . "'";
    $drill_result = mysqli_query($conn, $query);

    if ($drill_result && mysqli_num_rows($drill_result) > 0) {
        while ($drill_cur = mysqli_fetch_row($drill_result)) {
            if (!$drill_cur)
                continue; // safety check

            $lstDrill = (int) $drill_cur[0];
            $main_query = "UPDATE tenter, tuser 
                       SET p_flag = 1 
                       WHERE tenter.e_id = tuser.id 
                       AND tenter.e_date = '" . $drill_cur[1] . "' 
                       AND tenter.e_time >= '" . $drill_cur[2] . "' 
                       AND tenter.e_time <= '" . $drill_cur[3] . "'";

            // DrillTerminal
            $query = "SELECT g_id FROM DrillTerminal WHERE DrillMasterID = " . $lstDrill;
            $result = mysqli_query($jconn, $query);
            if ($result) {
                while ($cur = mysqli_fetch_row($result)) {
                    if ($cur && isset($cur[0])) {
                        $main_query .= " AND tenter.g_id = " . (int) $cur[0];
                    }
                }
            }

            // DrillDept
            $query = "SELECT Dept FROM DrillDept WHERE DrillMasterID = " . $lstDrill;
            $result = mysqli_query($jconn, $query);
            if ($result) {
                while ($cur = mysqli_fetch_row($result)) {
                    if ($cur && isset($cur[0])) {
                        $main_query .= " AND tuser.dept = '" . mysqli_real_escape_string($jconn, $cur[0]) . "'";
                    }
                }
            }

            // DrillRemark
            $query = "SELECT Remark FROM DrillRemark WHERE DrillMasterID = " . $lstDrill;
            $result = mysqli_query($jconn, $query);
            if ($result) {
                while ($cur = mysqli_fetch_row($result)) {
                    if ($cur && isset($cur[0])) {
                        $main_query .= " AND tuser.Remark = '" . mysqli_real_escape_string($jconn, $cur[0]) . "'";
                    }
                }
            }

            // DrillPhone
            $query = "SELECT Phone FROM DrillPhone WHERE DrillMasterID = " . $lstDrill;
            $result = mysqli_query($jconn, $query);
            if ($result) {
                while ($cur = mysqli_fetch_row($result)) {
                    if ($cur && isset($cur[0])) {
                        $main_query .= " AND tuser.Phone = '" . mysqli_real_escape_string($jconn, $cur[0]) . "'";
                    }
                }
            }

            // DrillIdNo
            $query = "SELECT IdNo FROM DrillIdNo WHERE DrillMasterID = " . $lstDrill;
            $result = mysqli_query($jconn, $query);
            if ($result) {
                while ($cur = mysqli_fetch_row($result)) {
                    if ($cur && isset($cur[0])) {
                        $main_query .= " AND tuser.idno = '" . mysqli_real_escape_string($jconn, $cur[0]) . "'";
                    }
                }
            }

            updateData($iconn, $main_query, true);
        }
    }

    if (empty($pay_off_employee)) {
        displayDate($maxDate);
        displayToday();
        getNow();
        echo "\nShift Calculations Started for " . displayDate($maxDate) . ": " . displayToday() . ", " . getNow() . " HRS";
        flush();
    } else {
        $maxDate = getNextDay(insertToday(), 1);
    }

    if ($second_execution == "second_execution") {
        $lstExitTerminal = "No";
    }

    if ($lstExitTerminal == "No") {
        $txtTotalExitClockin = 0;
    } else {
        $txtTotalExitClockin = 2;
    }
    
    $deptClocking = $txtTotalDailyClockin;
    $exitClocking = $txtTotalExitClockin;

    $query = "SELECT id FROM tgroup WHERE NightFlag = 0 AND ShiftTypeID = 1 AND ScheduleID = 1 AND id > 1";
    $result = mysqli_query($conn, $query);
    while ($sdcur = mysqli_fetch_row($result)) {
        if ($pay_off_employee == "") {
            $query = "SELECT distinct(e_id) FROM tenter WHERE e_group = " . $sdcur[0] . " AND p_flag = 0 AND tenter.e_date < '" . $maxDate . "' ORDER BY e_id ";
        } else {
            $query = "SELECT distinct(e_id) FROM tenter WHERE e_group = " . $sdcur[0] . " AND p_flag = 0 AND tenter.e_id = '" . $pay_off_employee . "' ORDER BY e_id ";
        }
        $result1 = mysqli_query($jconn, $query);
        while ($sdcur1 = mysqli_fetch_row($result1)) {
            if ($second_execution === "second_execution") {
                $query = "SELECT tenter.e_date, tenter.e_time, tenter.g_id, 0, tenter.ed, tenter.e_etc FROM tenter, tgate WHERE tenter.g_id = tgate.id AND tenter.e_id = " . $sdcur1[0] . " AND tenter.e_date < '" . $maxDate . "' AND tenter.p_flag = 0 AND tenter.e_group = " . $sdcur[0] . " ORDER BY tenter.e_date, tenter.e_time";
            } else {
                $query = "SELECT tenter.e_date, tenter.e_time, tenter.g_id, tgate.exit, tenter.ed, tenter.e_etc FROM tenter, tgate WHERE tenter.g_id = tgate.id AND tenter.e_id = " . $sdcur1[0] . " AND tenter.e_date < '" . $maxDate . "' AND tenter.p_flag = 0 AND tenter.e_group = " . $sdcur[0] . "  ORDER BY tenter.e_date, tenter.e_time";
            }
            $date = "";
            $time = "";
            $gate = "";
            $a0 = array();
            $a1 = array();
            $a2 = array();
            $a3 = array();
            $a4 = array();
            $a5 = array();
            $dayCount = 0;
            $exitCount = 0;
            $deptCount = 0;
            $result2 = mysqli_query($kconn, $query);
            while ($cur = mysqli_fetch_row($result2)) {
                if ($time == "") {
                    $date = $cur[0];
//                    $time = mktime((int)substr($cur[1], 0, 2), (int)substr($cur[1], 2, 2), (int)substr($cur[1], 4, 2), (int)substr($cur[0], 4, 2), (int)substr($cur[0], 6, 2), (int)substr($cur[0], 0, 4));
                    $time = ts_from_date_time($cur[0], $cur[1]);
                    $gate = $cur[2];
                    $a0[$dayCount] = $cur[0];
                    $a1[$dayCount] = $cur[1];
                    $a2[$dayCount] = $cur[2];
                    $a3[$dayCount] = $cur[3];
                    $a4[$dayCount] = $cur[4];
                    if ($cur[5] == "P") {
                        $a5[$dayCount] = 1;
                    } else {
                        $a5[$dayCount] = 0;
                    }
                    $dayCount++;
                    if ($cur[3] == 1) {
                        $exitCount++;
                    } else {
                        $deptCount++;
                    }
                } else {
//                    $this_time = mktime((int)substr($cur[1], 0, 2), (int)substr($cur[1], 2, 2), (int)substr($cur[1], 4, 2), (int)substr($cur[0], 4, 2), (int)substr($cur[0], 6, 2), (int)substr($cur[0], 0, 4));
                    $this_time = ts_from_date_time($cur[0], $cur[1]);
                    if ($cur[0] == $date) {
                        if ($this_time * 1 - $time * 1 < $txtMinClockinPeriod) {
                            $query = "UPDATE tenter SET p_flag = 1 WHERE ed = " . $cur[4];
                            updateIData($iconn, $query, true);
                        } else {
                            $a0[$dayCount] = $cur[0];
                            $a1[$dayCount] = $cur[1];
                            $a2[$dayCount] = $cur[2];
                            $a3[$dayCount] = $cur[3];
                            $a4[$dayCount] = $cur[4];
                            if ($cur[5] == "P") {
                                $a5[$dayCount] = 1;
                            } else {
                                $a5[$dayCount] = 0;
                            }
                            $dayCount++;
                            if ($cur[3] == 1) {
                                $exitCount++;
                            } else {
                                $deptCount++;
                            }
                            $time = $this_time;
                        }
                    } else {
                        if ($dayCount % 2 == 0) {
                            $pflag = "Black";
                            if ($a5[0] == 1) {
                                $pflag = "Proxy";
                            }
                            if ($exitClocking == 0 || $exitCount == 0) {
                                $this_work = 0;
                                $this_i = 0;
                                while ($this_i < count($a1) - 1) {
//                                    $this_work = $this_work + mktime((int)substr($a1[$this_i + 1], 0, 2), (int)substr($a1[$this_i + 1], 2, 2), (int)substr($a1[$this_i + 1], 4, 2), (int)substr($a0[$this_i + 1], 4, 2), (int)substr($a0[$this_i + 1], 6, 2), (int)substr($a0[$this_i + 1], 0, 4)) - mktime((int)substr($a1[$this_i], 0, 2), (int)substr($a1[$this_i], 2, 2), (int)substr($a1[$this_i], 4, 2), (int)substr($a0[$this_i], 4, 2), (int)substr($a0[$this_i], 6, 2), (int)substr($a0[$this_i], 0, 4));
                                    $this_work += ts_from_date_time($a0[$this_i + 1], $a1[$this_i + 1]) -  ts_from_date_time($a0[$this_i],     $a1[$this_i]);
                                    $this_i = $this_i + 2;
                                }
                                if ($a1[0] != "" && $a1[$dayCount - 1] != "") {
                                    $query = "INSERT IGNORE INTO DayMaster (e_id, TDate, Entry, Start, BreakOut, BreakIn, Close, `Exit`, group_id, Flag, Work) VALUES (" . $sdcur1[0] . ", " . $date . ", '" . $a1[0] . "', '" . $a1[0] . "', '" . $a1[0] . "', '" . $a1[0] . "', '" . $a1[$dayCount - 1] . "', '" . $a1[$dayCount - 1] . "', " . $sdcur[0] . ", '" . $pflag . "', " . $this_work . ")";
                                    $process_flag = true;
                                }
                            } else {
                                $this_work = 0;
                                $this_i = 1;
                                while ($this_i < count($a1) - 2) {
//                                    $this_work = $this_work + mktime((int)substr($a1[$this_i + 1], 0, 2), (int)substr($a1[$this_i + 1], 2, 2), (int)substr($a1[$this_i + 1], 4, 2), (int)substr($a0[$this_i + 1], 4, 2), (int)substr($a0[$this_i + 1], 6, 2), (int)substr($a0[$this_i + 1], 0, 4)) - mktime((int)substr($a1[$this_i], 0, 2), (int)substr($a1[$this_i], 2, 2), (int)substr($a1[$this_i], 4, 2), (int)substr($a0[$this_i], 4, 2), (int)substr($a0[$this_i], 6, 2), (int)substr($a0[$this_i], 0, 4));
                                    $this_work += ts_from_date_time($a0[$this_i + 1], $a1[$this_i + 1]) -  ts_from_date_time($a0[$this_i],     $a1[$this_i]);
                                    $this_i = $this_i + 2;
                                }
                                if ($a1[1] != "" && $a1[$dayCount - 2] != "") {
                                    $query = "INSERT IGNORE INTO DayMaster (e_id, TDate, Entry, Start, BreakOut, BreakIn, Close, `Exit`, group_id, Flag, Work) VALUES (" . $sdcur1[0] . ", " . $date . ", '" . $a1[0] . "', '" . $a1[1] . "', '" . $a1[1] . "', '" . $a1[1] . "', '" . $a1[$dayCount - 2] . "', '" . $a1[$dayCount - 1] . "', " . $sdcur[0] . ", '" . $pflag . "', " . $this_work . ")";
                                    $process_flag = true;
                                }
                            }
                            if (updateIData($iconn, $query, true)) {
                                for ($k = 0; $k < $exitCount + $deptCount; $k++) {
                                    $query = "UPDATE tenter SET p_flag = 1 WHERE ed = " . $a4[$k];
                                    updateIData($iconn, $query, true);
                                }
                            } else {
                                mysqli_rollback($iconn);
                            }
                        }
                        $a0 = array();
                        $a1 = array();
                        $a2 = array();
                        $a3 = array();
                        $a4 = array();
                        $a5 = array();
                        $dayCount = 0;
                        $exitCount = 0;
                        $deptCount = 0;
                        $date = $cur[0];
                        $time = $this_time;
                        $gate = $cur[2];
                        $a0[$dayCount] = $cur[0];
                        $a1[$dayCount] = $cur[1];
                        $a2[$dayCount] = $cur[2];
                        $a3[$dayCount] = $cur[3];
                        $a4[$dayCount] = $cur[4];
                        if ($cur[5] == "P") {
                            $a5[$dayCount] = 1;
                        } else {
                            $a5[$dayCount] = 0;
                        }
                        $dayCount++;
                        if ($cur[3] == 1) {
                            $exitCount++;
                        } else {
                            $deptCount++;
                        }
                    }
                }
            }
            if ($dayCount % 2 == 0) {
                $pflag = "Black";
                if ($a5[0] == 1) {
                    $pflag = "Proxy";
                }
                if ($exitClocking == 0 || $exitCount == 0) {
                    $this_work = 0;
                    $this_i = 0;
                    while ($this_i < count($a1) - 1) {
//                        $this_work = $this_work + mktime((int)substr($a1[$this_i + 1], 0, 2), (int)substr($a1[$this_i + 1], 2, 2), (int)substr($a1[$this_i + 1], 4, 2), (int)substr($a0[$this_i + 1], 4, 2), (int)substr($a0[$this_i + 1], 6, 2), (int)substr($a0[$this_i + 1], 0, 4)) - mktime((int)substr($a1[$this_i], 0, 2), (int)substr($a1[$this_i], 2, 2), (int)substr($a1[$this_i], 4, 2), (int)substr($a0[$this_i], 4, 2), (int)substr($a0[$this_i], 6, 2), (int)substr($a0[$this_i], 0, 4));
                        $this_work += ts_from_date_time($a0[$this_i + 1], $a1[$this_i + 1]) -  ts_from_date_time($a0[$this_i],     $a1[$this_i]);
                        $this_i = $this_i + 2;
                    }
                    if ($a1[0] != "" && $a1[$dayCount - 1] != "") {
                        $query = "INSERT IGNORE INTO DayMaster (e_id, TDate, Entry, Start, BreakOut, BreakIn, Close, `Exit`, group_id, Flag, Work) VALUES (" . $sdcur1[0] . ", " . $date . ", '" . $a1[0] . "', '" . $a1[0] . "', '" . $a1[0] . "', '" . $a1[0] . "', '" . $a1[$dayCount - 1] . "', '" . $a1[$dayCount - 1] . "', " . $sdcur[0] . ", '" . $pflag . "', " . $this_work . ")";
                        $process_flag = true;
                    }
                } else {
                    $this_work = 0;
                    $this_i = 1;
                    while ($this_i < count($a1) - 2) {
//                        $this_work = $this_work + mktime((int)substr($a1[$this_i + 1], 0, 2), (int)substr($a1[$this_i + 1], 2, 2), (int)substr($a1[$this_i + 1], 4, 2), (int)substr($a0[$this_i + 1], 4, 2), (int)substr($a0[$this_i + 1], 6, 2), (int)substr($a0[$this_i + 1], 0, 4)) - mktime((int)substr($a1[$this_i], 0, 2), (int)substr($a1[$this_i], 2, 2), (int)substr($a1[$this_i], 4, 2), (int)substr($a0[$this_i], 4, 2), (int)substr($a0[$this_i], 6, 2), (int)substr($a0[$this_i], 0, 4));
                        $this_work += ts_from_date_time($a0[$this_i + 1], $a1[$this_i + 1]) -  ts_from_date_time($a0[$this_i],     $a1[$this_i]);
                        $this_i = $this_i + 2;
                    }
                    if ($a1[1] != "" && $a1[$dayCount - 2] != "") {
                        $query = "INSERT IGNORE INTO DayMaster (e_id, TDate, Entry, Start, BreakOut, BreakIn, Close, `Exit`, group_id, Flag, Work) VALUES (" . $sdcur1[0] . ", " . $date . ", '" . $a1[0] . "', '" . $a1[1] . "', '" . $a1[1] . "', '" . $a1[1] . "', '" . $a1[$dayCount - 2] . "', '" . $a1[$dayCount - 1] . "', " . $sdcur[0] . ", '" . $pflag . "', " . $this_work . ")";
                        $process_flag = true;
                    }
                }
                if (updateIData($iconn, $query, true)) {
                    for ($k = 0; $k < $exitCount + $deptCount; $k++) {
                        $query = "UPDATE tenter SET p_flag = 1 WHERE ed = " . $a4[$k];
                        updateIData($iconn, $query, true);
                    }
                } else {
                    mysqli_rollback($iconn);
                }
            }
        }
    }

    $query = "SELECT id FROM tgroup WHERE NightFlag = 0 AND ShiftTypeID = 1 AND ScheduleID > 1 AND ScheduleID <> 7 AND id > 1";
    $result = mysqli_query($conn, $query);
    while ($sdcur = mysqli_fetch_row($result)) {
        $query = "SELECT NoBreakException, NoBreakExceptionOT FROM tgroup WHERE id = " . $sdcur[0];
        $g_result = selectData($jconn, $query);
        $lstNoBreakException = $g_result[0];
        $lstNoBreakExceptionOT = $g_result[1];
        $query = "SELECT ScheduleID FROM tgroup WHERE id = " . $sdcur[0];
        $s_result = selectData($jconn, $query);
        if ($s_result[0] == 2 || $s_result[0] == 3) {
            $deptClocking = 4;
        } else {
            $deptClocking = 0;
        }
        if ($pay_off_employee == "") {
            $query = "SELECT distinct(e_id) FROM tenter WHERE e_group = " . $sdcur[0] . " AND p_flag = 0 AND tenter.e_date < '" . $maxDate . "' ORDER BY e_id";
        } else {
            $query = "SELECT distinct(e_id) FROM tenter WHERE e_group = " . $sdcur[0] . " AND p_flag = 0 AND tenter.e_id = '" . $pay_off_employee . "' ORDER BY e_id";
        }
        $result1 = mysqli_query($jconn, $query);
        while ($sdcur1 = mysqli_fetch_row($result1)) {
            if ($second_execution === "second_execution") {
                $query = "SELECT tenter.e_date, tenter.e_time, tenter.g_id, 0, tenter.ed, tenter.e_etc FROM tenter, tgate WHERE tenter.g_id = tgate.id AND tenter.e_id = " . $sdcur1[0] . " AND tenter.e_date < '" . $maxDate . "' AND tenter.p_flag = 0 AND tenter.e_group = " . $sdcur[0] . " ORDER BY tenter.e_date, tenter.e_time";
            } else {
                $query = "SELECT tenter.e_date, tenter.e_time, tenter.g_id, tgate.exit, tenter.ed, tenter.e_etc FROM tenter, tgate WHERE tenter.g_id = tgate.id AND tenter.e_id = " . $sdcur1[0] . " AND tenter.e_date < '" . $maxDate . "' AND tenter.p_flag = 0 AND tenter.e_group = " . $sdcur[0] . " ORDER BY tenter.e_date, tenter.e_time";
//                echo "\n";
            }
            $date = "";
            $time = "";
            $gate = "";
            $a0 = array();
            $a1 = array();
            $a2 = array();
            $a3 = array();
            $a4 = array();
            $a5 = array();
            $dayCount = 0;
            $exitCount = 0;
            $deptCount = 0;
            $result2 = mysqli_query($kconn, $query);
            while ($cur = mysqli_fetch_row($result2)) {
                if ($time == "") {
                    $date = $cur[0];
                    $hour = (int) substr($cur[1], 0, 2);
                    $minute = (int) substr($cur[1], 2, 2);
                    $second = (int) substr($cur[1] . "00", 4, 2); // adds "00" if missing

                    $month = (int) substr($cur[0], 4, 2);
                    $day = (int) substr($cur[0], 6, 2);
                    $year = (int) substr($cur[0], 0, 4);

//                    $time = mktime($hour, $minute, $second, $month, $day, $year);
                    $time = ts_from_date_time($cur[0], $cur[1]);
//                    $time = mktime(substr($cur[1], 0, 2), substr($cur[1], 2, 2), substr($cur[1], 4, 2), substr($cur[0], 4, 2), substr($cur[0], 6, 2), substr($cur[0], 0, 4));
                    $gate = $cur[2];
                    $a0[$dayCount] = $cur[0];
                    $a1[$dayCount] = $cur[1];
                    $a2[$dayCount] = $cur[2];
                    $a3[$dayCount] = $cur[3];
                    $a4[$dayCount] = $cur[4];
                    if ($cur[5] == "P") {
                        $a5[$dayCount] = 1;
                    } else {
                        $a5[$dayCount] = 0;
                    }
                    $dayCount++;
                    if ($cur[3] == 1) {
                        $exitCount++;
                    } else {
                        $deptCount++;
                    }
                } else {
                    $hour = (int) substr($cur[1], 0, 2);
                    $minute = (int) substr($cur[1], 2, 2);
                    $second = (int) substr($cur[1], 4, 2) ?: 0; // default to 0 if empty
                    $month = (int) substr($cur[0], 4, 2);
                    $day = (int) substr($cur[0], 6, 2);
                    $year = (int) substr($cur[0], 0, 4);

                    $this_time = mktime($hour, $minute, $second, $month, $day, $year);
//                    $this_time = mktime(substr($cur[1], 0, 2), substr($cur[1], 2, 2), substr($cur[1], 4, 2), substr($cur[0], 4, 2), substr($cur[0], 6, 2), substr($cur[0], 0, 4));
                    if ($cur[0] == $date) {
//                        if ($this_time * 1 - $time * 1 < $txtMinClockinPeriod) {
                        if (($this_time - $time) < (int)$txtMinClockinPeriod) {
                            $query = "UPDATE tenter SET p_flag = 1 WHERE ed = " . $cur[4];
                            updateIData($iconn, $query, true);
                        } else {
                            $a0[$dayCount] = $cur[0];
                            $a1[$dayCount] = $cur[1];
                            $a2[$dayCount] = $cur[2];
                            $a3[$dayCount] = $cur[3];
                            $a4[$dayCount] = $cur[4];
                            if ($cur[5] == "P") {
                                $a5[$dayCount] = 1;
                            } else {
                                $a5[$dayCount] = 0;
                            }
                            $dayCount++;
                            if ($cur[3] == 1) {
                                $exitCount++;
                            } else {
                                $deptCount++;
                            }
                            $time = $this_time;
                        }
                    } else {
                        $noBreakFlag = false;
                        if ($lstNoBreakExceptionOT == "Yes" && ($s_result[0] == 3 || $s_result[0] == 2)) {
                            $this_day = getDay(displayDate($date));
                            if ($this_day == "Sunday" || $this_day == "Saturday") {
                                $noBreakFlag = true;
                            } else {
                                $noBreakQuery = "SELECT OTDate FROM OTDate WHERE OTDate = " . $date;
                                $noBreakResult = selectData($conn, $noBreakQuery);
                                if ($noBreakResult[0] == $date) {
                                    $noBreakFlag = true;
                                }
                            }
                        }
                        if (($exitCount == $exitClocking || $exitClocking == 0 || $exitCount == 0 && $lstNoExitException == "Yes") && ($deptCount == $deptClocking || $deptCount == 2 && $lstNoBreakException == "Yes" || $deptCount == 2 && $noBreakFlag == true || ($s_result[0] == 5 || $s_result[0] == 6) && 1 < $deptCount)) {
                            $pflag = "Black";
                            if ($a5[0] == 1) {
                                $pflag = "Proxy";
                            }
                            if ($exitClocking == 0 || $exitCount == 0) {
                                if ($deptCount == 2 || $s_result[0] == 5 || $s_result[0] == 6) {
                                    if ($a1[0] != "" && $a1[$dayCount - 1] != "") {
                                        $query = "INSERT IGNORE INTO DayMaster (e_id, TDate, Entry, Start, BreakOut, BreakIn, Close, `Exit`, group_id, Flag) VALUES (" . $sdcur1[0] . ", " . $date . ", '" . $a1[0] . "', '" . $a1[0] . "', '" . $a1[0] . "', '" . $a1[0] . "', '" . $a1[$dayCount - 1] . "', '" . $a1[$dayCount - 1] . "', " . $sdcur[0] . ", '" . $pflag . "')";
                                        $process_flag = true;
                                    }
                                } else {
                                    if ($a1[0] != "" && $a1[3] != "") {
                                        $query = "INSERT IGNORE INTO DayMaster (e_id, TDate, Entry, Start, BreakOut, BreakIn, Close, `Exit`, group_id, Flag) VALUES (" . $sdcur1[0] . ", " . $date . ", '" . $a1[0] . "', '" . $a1[0] . "', '" . $a1[1] . "', '" . $a1[2] . "', '" . $a1[3] . "', '" . $a1[3] . "', " . $sdcur[0] . ", '" . $pflag . "')";
                                        $process_flag = true;
                                    }
                                }
                            } else {
                                if ($deptCount == 2 || $s_result[0] == 5 || $s_result[0] == 6) {
                                    if ($a1[1] != "" && $a1[$dayCount - 1] != "") {
                                        $query = "INSERT IGNORE INTO DayMaster (e_id, TDate, Entry, Start, BreakOut, BreakIn, Close, `Exit`, group_id, Flag) VALUES (" . $sdcur1[0] . ", " . $date . ", '" . $a1[0] . "', '" . $a1[1] . "', '" . $a1[1] . "', '" . $a1[1] . "', '" . $a1[$dayCount - 2] . "', '" . $a1[$dayCount - 1] . "', " . $sdcur[0] . ", '" . $pflag . "')";
                                        $process_flag = true;
                                    }
                                } else {
                                    if ($a1[1] != "" && $a1[4] != "") {
                                        $query = "INSERT IGNORE INTO DayMaster (e_id, TDate, Entry, Start, BreakOut, BreakIn, Close, `Exit`, group_id, Flag) VALUES (" . $sdcur1[0] . ", " . $date . ", '" . $a1[0] . "', '" . $a1[1] . "', '" . $a1[2] . "', '" . $a1[3] . "', '" . $a1[4] . "', '" . $a1[5] . "', " . $sdcur[0] . ", '" . $pflag . "')";
                                        $process_flag = true;
                                    }
                                }
                            }
                            if (updateIData($iconn, $query, true)) {
                                for ($k = 0; $k < $exitCount + $deptCount; $k++) {
                                    $query = "UPDATE tenter SET p_flag = 1 WHERE ed = " . $a4[$k];
                                    updateIData($iconn, $query, true);
                                }
                            } else {
                                mysqli_rollback($iconn);
                            }
                        }
                        $a0 = array();
                        $a1 = array();
                        $a2 = array();
                        $a3 = array();
                        $a4 = array();
                        $a5 = array();
                        $dayCount = 0;
                        $exitCount = 0;
                        $deptCount = 0;
                        $date = $cur[0];
                        $time = $this_time;
                        $gate = $cur[2];
                        $a0[$dayCount] = $cur[0];
                        $a1[$dayCount] = $cur[1];
                        $a2[$dayCount] = $cur[2];
                        $a3[$dayCount] = $cur[3];
                        $a4[$dayCount] = $cur[4];
                        if ($cur[5] == "P") {
                            $a5[$dayCount] = 1;
                        } else {
                            $a5[$dayCount] = 0;
                        }
                        $dayCount++;
                        if ($cur[3] == 1) {
                            $exitCount++;
                        } else {
                            $deptCount++;
                        }
                    }
                }
            }
            $noBreakFlag = false;
            if ($lstNoBreakExceptionOT == "Yes" && ($s_result[0] == 3 || $s_result[0] == 2)) {
                $this_day = getDay(displayDate($date));
                if ($this_day == "Sunday" || $this_day == "Saturday") {
                    $noBreakFlag = true;
                } else {
                    $noBreakQuery = "SELECT OTDate FROM OTDate WHERE OTDate = " . $date;
                    $noBreakResult = selectData($conn, $noBreakQuery);
                    if ($noBreakResult[0] == $date) {
                        $noBreakFlag = true;
                    }
                }
            }
            if (($exitCount == $exitClocking || $exitClocking == 0 || $exitCount == 0 && $lstNoExitException == "Yes") && ($deptCount == $deptClocking || $deptCount == 2 && $lstNoBreakException == "Yes" || $deptCount == 2 && $noBreakFlag == true || ($s_result[0] == 5 || $s_result[0] == 6) && 1 < $deptCount)) {
                $pflag = "Black";
                if ($a5[0] == 1) {
                    $pflag = "Proxy";
                }
                if ($exitClocking == 0 || $exitCount == 0) {
                    if ($deptCount == 2 || $s_result[0] == 5 || $s_result[0] == 6) {
                        if ($a1[0] != "" && $a1[$dayCount - 1] != "") {
                            $query = "INSERT IGNORE INTO DayMaster (e_id, TDate, Entry, Start, BreakOut, BreakIn, Close, `Exit`, group_id, Flag) VALUES (" . $sdcur1[0] . ", " . $date . ", '" . $a1[0] . "', '" . $a1[0] . "', '" . $a1[0] . "', '" . $a1[0] . "', '" . $a1[$dayCount - 1] . "', '" . $a1[$dayCount - 1] . "', " . $sdcur[0] . ", '" . $pflag . "')";
                            $process_flag = true;
                        }
                    } else {
                        if ($a1[0] != "" && $a1[3] != "") {
                            $query = "INSERT IGNORE INTO DayMaster (e_id, TDate, Entry, Start, BreakOut, BreakIn, Close, `Exit`, group_id, Flag) VALUES (" . $sdcur1[0] . ", " . $date . ", '" . $a1[0] . "', '" . $a1[0] . "', '" . $a1[1] . "', '" . $a1[2] . "', '" . $a1[3] . "', '" . $a1[3] . "', " . $sdcur[0] . ", '" . $pflag . "')";
                            $process_flag = true;
                        }
                    }
                } else {
                    if ($deptCount == 2 || $s_result[0] == 5 || $s_result[0] == 6) {
                        if ($a1[1] != "" && $a1[$dayCount - 1] != "") {
                            $query = "INSERT IGNORE INTO DayMaster (e_id, TDate, Entry, Start, BreakOut, BreakIn, Close, `Exit`, group_id, Flag) VALUES (" . $sdcur1[0] . ", " . $date . ", '" . $a1[0] . "', '" . $a1[1] . "', '" . $a1[1] . "', '" . $a1[1] . "', '" . $a1[$dayCount - 2] . "', '" . $a1[$dayCount - 1] . "', " . $sdcur[0] . ", '" . $pflag . "')";
                            $process_flag = true;
                        }
                    } else {
                        if ($a1[1] != "" && $a1[4] != "") {
                            $query = "INSERT IGNORE INTO DayMaster (e_id, TDate, Entry, Start, BreakOut, BreakIn, Close, `Exit`, group_id, Flag) VALUES (" . $sdcur1[0] . ", " . $date . ", '" . $a1[0] . "', '" . $a1[1] . "', '" . $a1[2] . "', '" . $a1[3] . "', '" . $a1[4] . "', '" . $a1[5] . "', " . $sdcur[0] . ", '" . $pflag . "')";
                            $process_flag = true;
                        }
                    }
                }
                if (updateIData($iconn, $query, true)) {
                    for ($k = 0; $k < $exitCount + $deptCount; $k++) {
                        $query = "UPDATE tenter SET p_flag = 1 WHERE ed = " . $a4[$k];
                        updateIData($iconn, $query, true);
                    }
                } else {
                    mysqli_rollback($iconn);
                }
            }
        }
    }
    $query = "SELECT id FROM tgroup WHERE ShiftTypeID = 1 AND ScheduleID = 7 AND id > 1";
    $result = mysqli_query($conn, $query);
    while ($sdcur = mysqli_fetch_row($result)) {
        $deptClocking = 0;
        if ($pay_off_employee == "") {
            $query = "SELECT distinct(e_id) FROM tenter WHERE e_group = " . $sdcur[0] . " AND p_flag = 0 AND tenter.e_date <= '" . $maxDate . "' ORDER BY e_id";
        } else {
            $query = "SELECT distinct(e_id) FROM tenter WHERE e_group = " . $sdcur[0] . " AND p_flag = 0 AND tenter.e_id = '" . $pay_off_employee . "' ORDER BY e_id";
        }
        $result1 = mysqli_query($jconn, $query);
        while ($sdcur1 = mysqli_fetch_row($result1)) {
            if ($second_execution === "second_execution") {
                $query = "SELECT tenter.e_date, tenter.e_time, tenter.g_id, 0, tenter.ed, tenter.e_etc, tenter.e_mode FROM tenter, tgate WHERE tenter.g_id = tgate.id AND tenter.e_id = " . $sdcur1[0] . " AND tenter.e_date <= '" . $maxDate . "' AND tenter.p_flag = 0 AND tenter.e_group = " . $sdcur[0] . " ORDER BY tenter.e_date, tenter.e_time";
            } else {
                $query = "SELECT tenter.e_date, tenter.e_time, tenter.g_id, tgate.exit, tenter.ed, tenter.e_etc, tenter.e_mode FROM tenter, tgate WHERE tenter.g_id = tgate.id AND tenter.e_id = " . $sdcur1[0] . " AND tenter.e_date <= '" . $maxDate . "' AND tenter.p_flag = 0 AND tenter.e_group = " . $sdcur[0] . " ORDER BY tenter.e_date, tenter.e_time";
            }
            $date = "";
            $time = "";
            $gate = "";
            $a0 = array();
            $a1 = array();
            $a2 = array();
            $a3 = array();
            $a4 = array();
            $a5 = array();
            $a6 = array();
            $cut_off = $cur[0] . "235959";
            $dayCount = 0;
            $exitCount = 0;
            $deptCount = 0;
            $result2 = mysqli_query($kconn, $query);
            while ($cur = mysqli_fetch_row($result2)) {
                if ($time == "" && $cur[6] == 1) {
                    $date = $cur[0];
//                    $time = mktime((int)substr($cur[1], 0, 2), (int)substr($cur[1], 2, 2), (int)substr($cur[1], 4, 2), (int)substr($cur[0], 4, 2), (int)substr($cur[0], 6, 2), (int)substr($cur[0], 0, 4));
                    $time = ts_from_date_time($cur[0], $cur[1]);
                    if ($txtNightShiftMaxOutTime . "00" < $cur[1]) {
                        $cut_off = getNextDay($cur[0], 1) . $txtNightShiftMaxOutTime . "00";
                    } else {
                        $cut_off = $cur[0] . "235959";
                    }
                    $a0[$dayCount] = $cur[0];
                    $a1[$dayCount] = $cur[1];
                    $a2[$dayCount] = $cur[2];
                    $a3[$dayCount] = $cur[3];
                    $a4[$dayCount] = $cur[4];
                    if ($cur[5] == "P") {
                        $a5[$dayCount] = 1;
                    } else {
                        $a5[$dayCount] = 0;
                    }
                    $a6[$dayCount] = $cur[6];
                    $dayCount++;
                    if ($cur[3] == 1) {
                        $exitCount++;
                    } else {
                        $deptCount++;
                    }
                } else {
                    $this_time = mktime((int)substr($cur[1], 0, 2), (int)substr($cur[1], 2, 2), (int)substr($cur[1], 4, 2), (int)substr($cur[0], 4, 2), (int)substr($cur[0], 6, 2), (int)substr($cur[0], 0, 4));
                    if ($cur[0] . $cur[1] < $cut_off) {
                        $a0[$dayCount] = $cur[0];
                        $a1[$dayCount] = $cur[1];
                        $a2[$dayCount] = $cur[2];
                        $a3[$dayCount] = $cur[3];
                        $a4[$dayCount] = $cur[4];
                        if ($cur[5] == "P") {
                            $a5[$dayCount] = 1;
                        } else {
                            $a5[$dayCount] = 0;
                        }
                        $a6[$dayCount] = $cur[6];
                        $dayCount++;
                        if ($cur[3] == 1) {
                            $exitCount++;
                        } else {
                            $deptCount++;
                        }
                    } else {
                        if ($cur[6] == 1) {
                            $last_time = mktime((int)substr($a1[$dayCount - 1], 0, 2), (int)substr($a1[$dayCount - 1], 2, 2), (int)substr($a1[$dayCount - 1], 4, 2), (int)substr($a0[$dayCount - 1], 4, 2), (int)substr($a0[$dayCount - 1], 6, 2), (int)substr($a0[$dayCount - 1], 0, 4));
                            if (($exitCount == $exitClocking || $exitClocking == 0) && 1 < $deptCount && $a6[$dayCount - 1] == 2 && $last_time - $time < 86500) {
                                $pflag = "Black";
                                if ($a5[0] == 1) {
                                    $pflag = "Proxy";
                                }
                                if ($exitClocking == 0 || $exitCount == 0) {
                                    $query = "INSERT IGNORE INTO DayMaster (e_id, TDate, Entry, Start, BreakOut, BreakIn, Close, `Exit`, group_id, Flag) VALUES (" . $sdcur1[0] . ", " . $date . ", '" . $a1[0] . "', '" . $a1[0] . "', '" . $a1[0] . "', '" . $a1[0] . "', '" . $a1[$dayCount - 1] . "', '" . $a1[$dayCount - 1] . "', " . $sdcur[0] . ", '" . $pflag . "')";
                                    $process_flag = true;
                                } else {
                                    $query = "INSERT IGNORE INTO DayMaster (e_id, TDate, Entry, Start, BreakOut, BreakIn, Close, `Exit`, group_id, Flag) VALUES (" . $sdcur1[0] . ", " . $date . ", '" . $a1[0] . "', '" . $a1[1] . "', '" . $a1[1] . "', '" . $a1[1] . "', '" . $a1[$dayCount - 2] . "', '" . $a1[$dayCount - 1] . "', " . $sdcur[0] . ", '" . $pflag . "')";
                                    $process_flag = true;
                                }
                                if (updateIData($iconn, $query, true)) {
                                    for ($k = 0; $k < $exitCount + $deptCount; $k++) {
                                        $query = "UPDATE tenter SET p_flag = 1 WHERE ed = " . $a4[$k];
                                        updateIData($iconn, $query, true);
                                    }
                                } else {
                                    mysqli_rollback($iconn);
                                }
                            }
                            $a0 = array();
                            $a1 = array();
                            $a2 = array();
                            $a3 = array();
                            $a4 = array();
                            $a5 = array();
                            $a6 = array();
                            $dayCount = 0;
                            $exitCount = 0;
                            $deptCount = 0;
                            $date = $cur[0];
                            $time = $this_time;
                            $a0[$dayCount] = $cur[0];
                            $a1[$dayCount] = $cur[1];
                            $a2[$dayCount] = $cur[2];
                            $a3[$dayCount] = $cur[3];
                            $a4[$dayCount] = $cur[4];
                            $a5[$dayCount] = $cur[5];
                            $a6[$dayCount] = $cur[6];
                            $dayCount++;
                            if ($cur[3] == 1) {
                                $exitCount++;
                            } else {
                                $deptCount++;
                            }
                        } else {
                            $a0[$dayCount] = $cur[0];
                            $a1[$dayCount] = $cur[1];
                            $a2[$dayCount] = $cur[2];
                            $a3[$dayCount] = $cur[3];
                            $a4[$dayCount] = $cur[4];
                            $a5[$dayCount] = $cur[5];
                            $a6[$dayCount] = $cur[6];
                            $dayCount++;
                            if ($cur[3] == 1) {
                                $exitCount++;
                            } else {
                                $deptCount++;
                            }
                        }
                    }
                }
            }
            $last_time = mktime((int)substr($a1[$dayCount - 1], 0, 2), (int)substr($a1[$dayCount - 1], 2, 2), (int)substr($a1[$dayCount - 1], 4, 2), (int)substr($a0[$dayCount - 1], 4, 2), (int)substr($a0[$dayCount - 1], 6, 2), (int)substr($a0[$dayCount - 1], 0, 4));
            if (($exitCount == $exitClocking || $exitClocking == 0) && 1 < $deptCount && $a6[$dayCount - 1] == 2 && $last_time - $time < 86500) {
                $pflag = "Black";
                if ($a5[0] == "P") {
                    $pflag = "Proxy";
                }
                if ($exitClocking == 0 || $exitCount == 0) {
                    $query = "INSERT IGNORE INTO DayMaster (e_id, TDate, Entry, Start, BreakOut, BreakIn, Close, `Exit`, group_id, Flag) VALUES (" . $sdcur1[0] . ", " . $date . ", '" . $a1[0] . "', '" . $a1[0] . "', '" . $a1[0] . "', '" . $a1[0] . "', '" . $a1[$dayCount - 1] . "', '" . $a1[$dayCount - 1] . "', " . $sdcur[0] . ", '" . $pflag . "')";
                    $process_flag = true;
                } else {
                    $query = "INSERT IGNORE INTO DayMaster (e_id, TDate, Entry, Start, BreakOut, BreakIn, Close, `Exit`, group_id, Flag) VALUES (" . $sdcur1[0] . ", " . $date . ", '" . $a1[0] . "', '" . $a1[1] . "', '" . $a1[1] . "', '" . $a1[1] . "', '" . $a1[$dayCount - 2] . "', '" . $a1[$dayCount - 1] . "', " . $sdcur[0] . ", '" . $pflag . "')";
                    $process_flag = true;
                }
                if (updateIData($iconn, $query, true)) {
                    for ($k = 0; $k < $exitCount + $deptCount; $k++) {
                        $query = "UPDATE tenter SET p_flag = 1 WHERE ed = " . $a4[$k];
                        updateIData($iconn, $query, true);
                    }
                } else {
                    mysqli_rollback($iconn);
                }
            }
        }
    }
    $maxDate = $process_date;
    
    if ($night_execution) { 
        $query = "SELECT id FROM tgroup WHERE NightFlag = 1 AND ShiftTypeID = 1 AND ScheduleID = 1 AND id > 1";
        $result = mysqli_query($conn, $query);
        while ($sdcur = mysqli_fetch_row($result)) {
            $shift_query = "SELECT MoveNS FROM tgroup WHERE id = " . $sdcur[0];
            $shift_result = selectData($conn, $shift_query);
            $lstMoveNS = $shift_result[0];
            $query = "SELECT distinct(e_id) FROM tenter WHERE e_group = " . $sdcur[0] . " AND p_flag = 0 AND tenter.e_date <= '" . $maxDate . "' ORDER BY e_id ";
            $result1 = mysqli_query($jconn, $query);
            while ($sdcur1 = mysqli_fetch_row($result1)) {
                if ($second_execution === "second_execution") {
                    $query = "SELECT tenter.e_date, tenter.e_time, tenter.g_id, 0, tenter.ed, tenter.e_etc FROM tenter, tgate WHERE tenter.g_id = tgate.id AND tenter.e_id = " . $sdcur1[0] . " AND tenter.e_date <= '" . $maxDate . "' AND tenter.p_flag = 0 AND tenter.e_group = " . $sdcur[0] . " ORDER BY tenter.e_date, tenter.e_time";
                } else {
                    $query = "SELECT tenter.e_date, tenter.e_time, tenter.g_id, tgate.exit, tenter.ed, tenter.e_etc FROM tenter, tgate WHERE tenter.g_id = tgate.id AND tenter.e_id = " . $sdcur1[0] . " AND tenter.e_date <= '" . $maxDate . "' AND tenter.p_flag = 0 AND tenter.e_group = " . $sdcur[0] . "  ORDER BY tenter.e_date, tenter.e_time";
                }
                $date = "";
                $next_date = "";
                $start_date = "";
                $next_time = 0;
                $time = "";
                $gate = "";
                $a0 = array();
                $a1 = array();
                $a2 = array();
                $a3 = array();
                $a4 = array();
                $a5 = array();
                $dayCount = 0;
                $exitCount = 0;
                $deptCount = 0;
                $firstcount = 0 - 1;
                $result2 = mysqli_query($kconn, $query);
                while ($cur = mysqli_fetch_row($result2)) {
                    if ($firstcount == 0 - 1 && $txtNightShiftMaxOutTime . "00" < $cur[1] && $date < $cur[0]) {
                        $firstcount = 0;
                        $next_time = mktime((int)substr($txtNightShiftMaxOutTime, 0, 2), (int)substr($txtNightShiftMaxOutTime, 2, 2), "00", (int)substr($cur[0], 4, 2), (int)substr($cur[0], 6, 2), (int)substr($cur[0], 0, 4)) + 86400;
//                        $time = mktime((int)substr($cur[1], 0, 2), (int)substr($cur[1], 2, 2), (int)substr($cur[1], 4, 2), (int)substr($cur[0], 4, 2), (int)substr($cur[0], 6, 2), (int)substr($cur[0], 0, 4));
                        $time = ts_from_date_time($cur[0], $cur[1]);
                        $gate = $cur[2];
                        $a0[$dayCount] = $cur[0];
                        $a1[$dayCount] = $cur[1];
                        $a2[$dayCount] = $cur[2];
                        $a3[$dayCount] = $cur[3];
                        $a4[$dayCount] = $cur[4];
                        if ($cur[5] == "P") {
                            $a5[$dayCount] = 1;
                        } else {
                            $a5[$dayCount] = 0;
                        }
                        $dayCount++;
                        if ($cur[3] == 1) {
                            $exitCount++;
                        } else {
                            $deptCount++;
                        }
                    } else {
                        if ($time != "" && $firstcount == 0) {
                            if ($this_time < $next_time) {
                                if ($this_time * 1 - $time * 1 < $txtMinClockinPeriod) {
                                    $query = "UPDATE tenter SET p_flag = 1 WHERE ed = " . $cur[4];
                                    updateIData($iconn, $query, true);
                                } else {
                                    $a0[$dayCount] = $cur[0];
                                    $a1[$dayCount] = $cur[1];
                                    $a2[$dayCount] = $cur[2];
                                    $a3[$dayCount] = $cur[3];
                                    $a4[$dayCount] = $cur[4];
                                    if ($cur[5] == "P") {
                                        $a5[$dayCount] = 1;
                                    } else {
                                        $a5[$dayCount] = 0;
                                    }
                                    $dayCount++;
                                    if ($cur[3] == 1) {
                                        $exitCount++;
                                    } else {
                                        $deptCount++;
                                    }
                                    $time = $this_time;
                                }
                            } else {
                                if ($dayCount % 2 == 0) {
                                    $date = $a0[0];
                                    if ($lstMoveNS == "Yes") {
                                        $next_date = getNextDay($date, 1);
                                        $date = $next_date;
                                    }
                                    $pflag = "Black";
                                    if ($a5[0] == 1) {
                                        $pflag = "Proxy";
                                    }
                                    if ($exitClocking == 0 || $exitCount == 0) {
                                        $this_work = 0;
                                        $this_i = 0;
                                        while ($this_i < count($a1) - 1) {
//                                            $this_work = $this_work + mktime((int)substr($a1[$this_i + 1], 0, 2), (int)substr($a1[$this_i + 1], 2, 2), (int)substr($a1[$this_i + 1], 4, 2), (int)substr($a0[$this_i + 1], 4, 2), (int)substr($a0[$this_i + 1], 6, 2), (int)substr($a0[$this_i + 1], 0, 4)) - mktime((int)substr($a1[$this_i], 0, 2), (int)substr($a1[$this_i], 2, 2), (int)substr($a1[$this_i], 4, 2), (int)substr($a0[$this_i], 4, 2), (int)substr($a0[$this_i], 6, 2), (int)substr($a0[$this_i], 0, 4));
                                            $this_work += ts_from_date_time($a0[$this_i + 1], $a1[$this_i + 1]) -  ts_from_date_time($a0[$this_i],     $a1[$this_i]);
                                            $this_i = $this_i + 2;
                                        }
                                        if ($a1[0] != "" && $a1[$dayCount - 1] != "") {
                                            $query = "INSERT IGNORE INTO DayMaster (e_id, TDate, Entry, Start, BreakOut, BreakIn, Close, `Exit`, group_id, Flag, Work) VALUES (" . $sdcur1[0] . ", " . $date . ", '" . $a1[0] . "', '" . $a1[0] . "', '" . $a1[0] . "', '" . $a1[0] . "', '" . $a1[$dayCount - 1] . "', '" . $a1[$dayCount - 1] . "', " . $sdcur[0] . ", '" . $pflag . "', " . $this_work . ")";
                                            $process_flag = true;
                                        }
                                    } else {
                                        $this_work = 0;
                                        $this_i = 1;
                                        while ($this_i < count($a1) - 2) {
//                                            $this_work = $this_work + mktime((int)substr($a1[$this_i + 1], 0, 2), (int)substr($a1[$this_i + 1], 2, 2), (int)substr($a1[$this_i + 1], 4, 2), (int)substr($a0[$this_i + 1], 4, 2), (int)substr($a0[$this_i + 1], 6, 2), (int)substr($a0[$this_i + 1], 0, 4)) - mktime((int)substr($a1[$this_i], 0, 2), (int)substr($a1[$this_i], 2, 2), (int)substr($a1[$this_i], 4, 2), (int)substr($a0[$this_i], 4, 2), (int)substr($a0[$this_i], 6, 2), (int)substr($a0[$this_i], 0, 4));
                                            $this_work += ts_from_date_time($a0[$this_i + 1], $a1[$this_i + 1]) -  ts_from_date_time($a0[$this_i],     $a1[$this_i]);
                                            $this_i = $this_i + 2;
                                        }
                                        if ($a1[1] != "" && $a1[$dayCount - 2] != "") {
                                            $query = "INSERT IGNORE INTO DayMaster (e_id, TDate, Entry, Start, BreakOut, BreakIn, Close, `Exit`, group_id, Flag, Work) VALUES (" . $sdcur1[0] . ", " . $date . ", '" . $a1[0] . "', '" . $a1[1] . "', '" . $a1[1] . "', '" . $a1[1] . "', '" . $a1[$dayCount - 2] . "', '" . $a1[$dayCount - 1] . "', " . $sdcur[0] . ", '" . $pflag . "', " . $this_work . ")";
                                            $process_flag = true;
                                        }
                                    }
                                    if (updateIData($iconn, $query, true)) {
                                        for ($k = 0; $k < $exitCount + $deptCount; $k++) {
                                            $query = "UPDATE tenter SET p_flag = 1 WHERE ed = " . $a4[$k];
                                            updateIData($iconn, $query, true);
                                        }
                                    } else {
                                        mysqli_rollback($iconn);
                                    }
                                }
                                $a0 = array();
                                $a1 = array();
                                $a2 = array();
                                $a3 = array();
                                $a4 = array();
                                $a5 = array();
                                $dayCount = 0;
                                $exitCount = 0;
                                $deptCount = 0;
                                if ($txtNightShiftMaxOutTime . "00" < $cur[1] && $date < $cur[0]) {
                                    $next_time = mktime((int)substr($txtNightShiftMaxOutTime, 0, 2), (int)substr($txtNightShiftMaxOutTime, 2, 2), "00", (int)substr($date, 4, 2), (int)substr($date, 6, 2), (int)substr($date, 0, 4)) + 86400;
                                    $time = $this_time;
                                    $gate = $cur[2];
                                    $a0[$dayCount] = $date;
                                    $a1[$dayCount] = $cur[1];
                                    $a2[$dayCount] = $cur[2];
                                    $a3[$dayCount] = $cur[3];
                                    $a4[$dayCount] = $cur[4];
                                    if ($cur[5] == "P") {
                                        $a5[$dayCount] = 1;
                                    } else {
                                        $a5[$dayCount] = 0;
                                    }
                                    $dayCount++;
                                    if ($cur[3] == 1) {
                                        $exitCount++;
                                    } else {
                                        $deptCount++;
                                    }
                                } else {
                                    $firstcount = 0 - 1;
                                }
                            }
                        }
                    }
                }
                if ($dayCount % 2 == 0) {
                    $date = $a0[0];
                    if ($lstMoveNS == "Yes") {
                        $next_date = getNextDay($date, 1);
                        $date = $next_date;
                    }
                    $pflag = "Black";
                    if ($a5[0] == 1) {
                        $pflag = "Proxy";
                    }
                    if ($exitClocking == 0 || $exitCount == 0) {
                        $this_work = 0;
                        $this_i = 0;
                        while ($this_i < count($a1) - 1) {
//                            $this_work = $this_work + mktime((int)substr($a1[$this_i + 1], 0, 2), (int)substr($a1[$this_i + 1], 2, 2), (int)substr($a1[$this_i + 1], 4, 2), (int)substr($a0[$this_i + 1], 4, 2), (int)substr($a0[$this_i + 1], 6, 2), (int)substr($a0[$this_i + 1], 0, 4)) - mktime((int)substr($a1[$this_i], 0, 2), (int)substr($a1[$this_i], 2, 2), (int)substr($a1[$this_i], 4, 2), (int)substr($a0[$this_i], 4, 2), (int)substr($a0[$this_i], 6, 2), (int)substr($a0[$this_i], 0, 4));
                            $this_work += ts_from_date_time($a0[$this_i + 1], $a1[$this_i + 1]) -  ts_from_date_time($a0[$this_i],     $a1[$this_i]);
                            $this_i = $this_i + 2;
                        }
                        if ($a1[0] != "" && $a1[$dayCount - 1] != "") {
                            $query = "INSERT IGNORE INTO DayMaster (e_id, TDate, Entry, Start, BreakOut, BreakIn, Close, `Exit`, group_id, Flag, Work) VALUES (" . $sdcur1[0] . ", " . $date . ", '" . $a1[0] . "', '" . $a1[0] . "', '" . $a1[0] . "', '" . $a1[0] . "', '" . $a1[$dayCount - 1] . "', '" . $a1[$dayCount - 1] . "', " . $sdcur[0] . ", '" . $pflag . "', " . $this_work . ")";
                            $process_flag = true;
                        }
                    } else {
                        $this_work = 0;
                        $this_i = 1;
                        while ($this_i < count($a1) - 2) {
//                            $this_work = $this_work + mktime((int)substr($a1[$this_i + 1], 0, 2), (int)substr($a1[$this_i + 1], 2, 2), (int)substr($a1[$this_i + 1], 4, 2), (int)substr($a0[$this_i + 1], 4, 2), (int)substr($a0[$this_i + 1], 6, 2), (int)substr($a0[$this_i + 1], 0, 4)) - mktime((int)substr($a1[$this_i], 0, 2), (int)substr($a1[$this_i], 2, 2), (int)substr($a1[$this_i], 4, 2), (int)substr($a0[$this_i], 4, 2), (int)substr($a0[$this_i], 6, 2), (int)substr($a0[$this_i], 0, 4));
                            $this_work += ts_from_date_time($a0[$this_i + 1], $a1[$this_i + 1]) -  ts_from_date_time($a0[$this_i],     $a1[$this_i]);
                            $this_i = $this_i + 2;
                        }
                        if ($a1[1] != "" && $a1[$dayCount - 2] != "") {
                            $query = "INSERT IGNORE INTO DayMaster (e_id, TDate, Entry, Start, BreakOut, BreakIn, Close, `Exit`, group_id, Flag, Work) VALUES (" . $sdcur1[0] . ", " . $date . ", '" . $a1[0] . "', '" . $a1[1] . "', '" . $a1[1] . "', '" . $a1[1] . "', '" . $a1[$dayCount - 2] . "', '" . $a1[$dayCount - 1] . "', " . $sdcur[0] . ", '" . $pflag . "', " . $this_work . ")";
                            $process_flag = true;
                        }
                    }
                    if (updateIData($iconn, $query, true)) {
                        for ($k = 0; $k < $exitCount + $deptCount; $k++) {
                            $query = "UPDATE tenter SET p_flag = 1 WHERE ed = " . $a4[$k];
                            updateIData($iconn, $query, true);
                        }
                    } else {
                        mysqli_rollback($iconn);
                    }
                }
            }
        }
        $query = "SELECT id FROM tgroup WHERE NightFlag = 1 AND ShiftTypeID = 1 AND ScheduleID > 1 AND ScheduleID <> 7 AND id > 1";
        $result = mysqli_query($conn, $query);
        while ($sdcur = mysqli_fetch_row($result)) {
            $query = "SELECT NoBreakException, MoveNS, NoBreakExceptionOT FROM tgroup WHERE id = " . $sdcur[0];
            $g_result = selectData($jconn, $query);
            $lstNoBreakException = $g_result[0];
            $lstMoveNS = $g_result[1];
            $lstNoBreakExceptionOT = $g_result[2];
            $query = "SELECT ScheduleID FROM tgroup WHERE id = " . $sdcur[0];
            $s_result = selectData($jconn, $query);
            if ($s_result[0] == 2 || $s_result[0] == 3) {
                $deptClocking = 4;
            } else {
                $deptClocking = 0;
            }
            $query = "SELECT distinct(e_id) FROM tenter WHERE e_group = " . $sdcur[0] . " AND p_flag = 0 AND tenter.e_date <= '" . $maxDate . "' ORDER BY e_id";
            $result1 = mysqli_query($jconn, $query);
            while ($sdcur1 = mysqli_fetch_row($result1)) {
                if ($second_execution === "second_execution") {
                    $query = "SELECT tenter.e_date, tenter.e_time, tenter.g_id, 0, tenter.ed, tenter.e_etc FROM tenter, tgate WHERE tenter.g_id = tgate.id AND tenter.e_id = " . $sdcur1[0] . " AND tenter.e_date <= '" . $maxDate . "' AND tenter.p_flag = 0 AND tenter.e_group = " . $sdcur[0] . " ORDER BY tenter.e_date, tenter.e_time";
                } else {
                    $query = "SELECT tenter.e_date, tenter.e_time, tenter.g_id, tgate.exit, tenter.ed, tenter.e_etc FROM tenter, tgate WHERE tenter.g_id = tgate.id AND tenter.e_id = " . $sdcur1[0] . " AND tenter.e_date <= '" . $maxDate . "' AND tenter.p_flag = 0 AND tenter.e_group = " . $sdcur[0] . "  ORDER BY tenter.e_date, tenter.e_time";
                }
                $date = "";
                $next_date = "";
                $start_date = "";
                $next_time = 0;
                $time = "";
                $gate = "";
                $a0 = array();
                $a1 = array();
                $a2 = array();
                $a3 = array();
                $a4 = array();
                $a5 = array();
                $dayCount = 0;
                $exitCount = 0;
                $deptCount = 0;
                $firstcount = 0 - 1;
                $result2 = mysqli_query($kconn, $query);
                while ($cur = mysqli_fetch_row($result2)) {
                    if ($firstcount == 0 - 1 && $txtNightShiftMaxOutTime . "00" < $cur[1] && $date < $cur[0]) {
                        $firstcount = 0;
                        $next_time = mktime((int)substr($txtNightShiftMaxOutTime, 0, 2), (int)substr($txtNightShiftMaxOutTime, 2, 2), "00", (int)substr($cur[0], 4, 2), (int)substr($cur[0], 6, 2), (int)substr($cur[0], 0, 4)) + 86400;
//                        $time = mktime((int)substr($cur[1], 0, 2), (int)substr($cur[1], 2, 2), (int)substr($cur[1], 4, 2), (int)substr($cur[0], 4, 2), (int)substr($cur[0], 6, 2), (int)substr($cur[0], 0, 4));
                        $time = ts_from_date_time($cur[0], $cur[1]);
                        $gate = $cur[2];
                        $a0[$dayCount] = $cur[0];
                        $a1[$dayCount] = $cur[1];
                        $a2[$dayCount] = $cur[2];
                        $a3[$dayCount] = $cur[3];
                        $a4[$dayCount] = $cur[4];
                        if ($cur[5] == "P") {
                            $a5[$dayCount] = 1;
                        } else {
                            $a5[$dayCount] = 0;
                        }
                        $dayCount++;
                        if ($cur[3] == 1) {
                            $exitCount++;
                        } else {
                            $deptCount++;
                        }
                    } else { 
                        if ($time != "" && $firstcount == 0) {
                            $this_time = mktime(
                                    (int) substr($cur[1], 0, 2), // Hour
                                    (int) substr($cur[1], 2, 2), // Minute
                                    (int) substr($cur[1] . '00', 4, 2), // Second (defaults to 00 if missing)
                                    (int) substr($cur[0], 4, 2), // Month
                                    (int) substr($cur[0], 6, 2), // Day
                                    (int) substr($cur[0], 0, 4)                 // Year
                            );
//                            $time = ts_from_date_time($cur[0], $cur[1]);
//                            $this_time = mktime((int)substr($cur[1], 0, 2), (int)substr($cur[1], 2, 2), (int)substr($cur[1], 4, 2), (int)substr($cur[0], 4, 2), (int)substr($cur[0], 6, 2), (int)substr($cur[0], 0, 4));
                            if ($this_time < $next_time) { 
                                if ($this_time * 1 - $time * 1 < $txtMinClockinPeriod) { 
                                    $query = "UPDATE tenter SET p_flag = 1 WHERE ed = " . $cur[4];
                                    updateIData($iconn, $query, true);
                                } else {
                                    $a0[$dayCount] = $cur[0];
                                    $a1[$dayCount] = $cur[1];
                                    $a2[$dayCount] = $cur[2];
                                    $a3[$dayCount] = $cur[3];
                                    $a4[$dayCount] = $cur[4];
                                    if ($cur[5] == "P") {
                                        $a5[$dayCount] = 1;
                                    } else {
                                        $a5[$dayCount] = 0;
                                    }
                                    $dayCount++;
                                    if ($cur[3] == 1) {
                                        $exitCount++;
                                    } else {
                                        $deptCount++;
                                    }
                                    $time = $this_time;
                                }
                            } else {
                                $date = $a0[0];
                                if ($lstMoveNS == "Yes") {
                                    $next_date = getNextDay($date, 1);
                                    $date = $next_date;
                                }
                                $noBreakFlag = false;
                                if ($lstNoBreakExceptionOT == "Yes" && ($s_result[0] == 3 || $s_result[0] == 2)) {
                                    $this_day = getDay(displayDate($date));
                                    if ($this_day == "Sunday" || $this_day == "Saturday") {
                                        $noBreakFlag = true;
                                    } else {
                                        $noBreakQuery = "SELECT OTDate FROM OTDate WHERE OTDate = " . $date;
                                        $noBreakResult = selectData($conn, $noBreakQuery);
                                        if ($noBreakResult[0] == $date) {
                                            $noBreakFlag = true;
                                        }
                                    }
                                }
                                if (($exitCount == $exitClocking || $exitClocking == 0 || $exitCount == 0 && $lstNoExitException == "Yes") && ($deptCount == $deptClocking || $deptCount == 2 && $lstNoBreakException == "Yes" || $deptCount == 2 && $noBreakFlag == true || ($s_result[0] == 5 || $s_result[0] == 6) && 1 < $deptCount)) {
                                    $pflag = "Black";
                                    if ($a5[0] == 1) {
                                        $pflag = "Proxy";
                                    }
                                    if ($exitClocking == 0 || $exitCount == 0) {
                                        if ($deptCount == 2 || $s_result[0] == 5 || $s_result[0] == 6) {
                                            if ($a1[0] != "" && $a1[$dayCount - 1] != "") {
                                                $query = "INSERT IGNORE INTO DayMaster (e_id, TDate, Entry, Start, BreakOut, BreakIn, Close, `Exit`, group_id, Flag) VALUES (" . $sdcur1[0] . ", " . $date . ", '" . $a1[0] . "', '" . $a1[0] . "', '" . $a1[0] . "', '" . $a1[0] . "', '" . $a1[$dayCount - 1] . "', '" . $a1[$dayCount - 1] . "', " . $sdcur[0] . ", '" . $pflag . "')";
                                                $process_flag = true;
                                            }
                                        } else {
                                            if ($a1[0] != "" && $a1[3] != "") {
                                                $query = "INSERT IGNORE INTO DayMaster (e_id, TDate, Entry, Start, BreakOut, BreakIn, Close, `Exit`, group_id, Flag) VALUES (" . $sdcur1[0] . ", " . $date . ", '" . $a1[0] . "', '" . $a1[0] . "', '" . $a1[1] . "', '" . $a1[2] . "', '" . $a1[3] . "', '" . $a1[3] . "', " . $sdcur[0] . ", '" . $pflag . "')";
                                                $process_flag = true;
                                            }
                                        }
                                    } else {
                                        if ($deptCount == 2 || $s_result[0] == 5 || $s_result[0] == 6) {
                                            if ($a1[1] != "" && $a1[$dayCount - 2] != "") {
                                                $query = "INSERT IGNORE INTO DayMaster (e_id, TDate, Entry, Start, BreakOut, BreakIn, Close, `Exit`, group_id, Flag) VALUES (" . $sdcur1[0] . ", " . $date . ", '" . $a1[0] . "', '" . $a1[1] . "', '" . $a1[1] . "', '" . $a1[1] . "', '" . $a1[$dayCount - 2] . "', '" . $a1[$dayCount - 1] . "', " . $sdcur[0] . ", '" . $pflag . "')";
                                                $process_flag = true;
                                            }
                                        } else {
                                            if ($a1[1] != "" && $a1[4] != "") {
                                                $query = "INSERT IGNORE INTO DayMaster (e_id, TDate, Entry, Start, BreakOut, BreakIn, Close, `Exit`, group_id, Flag) VALUES (" . $sdcur1[0] . ", " . $date . ", '" . $a1[0] . "', '" . $a1[1] . "', '" . $a1[2] . "', '" . $a1[3] . "', '" . $a1[4] . "', '" . $a1[5] . "', " . $sdcur[0] . ", '" . $pflag . "')";
                                                $process_flag = true;
                                            }
                                        }
                                    }
                                    if (updateIData($iconn, $query, true)) {
                                        for ($k = 0; $k < $exitCount + $deptCount; $k++) {
                                            $query = "UPDATE tenter SET p_flag = 1 WHERE ed = " . $a4[$k];
                                            updateIData($iconn, $query, true);
                                        }
                                    } else {
                                        mysqli_rollback($iconn);
                                        echo "\n2455: Unable to INSERT: " . $query;
                                        exit;
                                    }
                                }
                                $a0 = array();
                                $a1 = array();
                                $a2 = array();
                                $a3 = array();
                                $a4 = array();
                                $a5 = array();
                                $dayCount = 0;
                                $exitCount = 0;
                                $deptCount = 0;
                                if ($txtNightShiftMaxOutTime . "00" < $cur[1] && $date < $cur[0]) {
                                    $next_time = mktime((int)substr($txtNightShiftMaxOutTime, 0, 2), (int)substr($txtNightShiftMaxOutTime, 2, 2), "00", (int)substr($cur[0], 4, 2), (int)substr($cur[0], 6, 2), (int)substr($cur[0], 0, 4)) + 86400;
                                    $time = $this_time;
                                    $gate = $cur[2];
                                    $a0[$dayCount] = $cur[0];
                                    $a1[$dayCount] = $cur[1];
                                    $a2[$dayCount] = $cur[2];
                                    $a3[$dayCount] = $cur[3];
                                    $a4[$dayCount] = $cur[4];
                                    if ($cur[5] == "P") {
                                        $a5[$dayCount] = 1;
                                    } else {
                                        $a5[$dayCount] = 0;
                                    }
                                    $dayCount++;
                                    if ($cur[3] == 1) {
                                        $exitCount++;
                                    } else {
                                        $deptCount++;
                                    }
                                } else {
                                    $firstcount = 0 - 1;
                                }
                            }
                        }
                    }
                }
                $date = $a0[0];
                if ($lstMoveNS == "Yes") {
                    $next_date = getNextDay($date, 1);
                    $date = $next_date;
                }
                $noBreakFlag = false;
                if ($lstNoBreakExceptionOT == "Yes" && ($s_result[0] == 3 || $s_result[0] == 2)) {
                    $this_day = getDay(displayDate($date));
                    if ($this_day == "Sunday" || $this_day == "Saturday") {
                        $noBreakFlag = true;
                    } else {
                        $noBreakQuery = "SELECT OTDate FROM OTDate WHERE OTDate = '" . $date . "'";
                        $noBreakResult = selectData($conn, $noBreakQuery);
                        if ($noBreakResult[0] == $date) {
                            $noBreakFlag = true;
                        }
                    }
                }
                if (($exitCount == $exitClocking || $exitClocking == 0 || $exitCount == 0 && $lstNoExitException == "Yes") && ($deptCount == $deptClocking || $deptCount == 2 && $lstNoBreakException == "Yes" || $deptCount == 2 && $noBreakFlag == true || ($s_result[0] == 5 || $s_result[0] == 6) && 1 < $deptCount)) {
                    $pflag = "Black";
                    if ($a5[0] == 1) {
                        $pflag = "Proxy";
                    }
                    if ($exitClocking == 0 || $exitCount == 0) {
                        if ($deptCount == 2 || $s_result[0] == 5 || $s_result[0] == 6) {
                            if ($a1[0] != "" && $a1[$dayCount - 1] != "") {
                                $query = "INSERT IGNORE INTO DayMaster (e_id, TDate, Entry, Start, BreakOut, BreakIn, Close, `Exit`, group_id, Flag) VALUES (" . $sdcur1[0] . ", " . $date . ", '" . $a1[0] . "', '" . $a1[0] . "', '" . $a1[0] . "', '" . $a1[0] . "', '" . $a1[$dayCount - 1] . "', '" . $a1[$dayCount - 1] . "', " . $sdcur[0] . ", '" . $pflag . "')";
                                $process_flag = true;
                            }
                        } else {
                            if ($a1[0] != "" && $a1[3] != "") {
                                $query = "INSERT IGNORE INTO DayMaster (e_id, TDate, Entry, Start, BreakOut, BreakIn, Close, `Exit`, group_id, Flag) VALUES (" . $sdcur1[0] . ", " . $date . ", '" . $a1[0] . "', '" . $a1[0] . "', '" . $a1[1] . "', '" . $a1[2] . "', '" . $a1[3] . "', '" . $a1[3] . "', " . $sdcur[0] . ", '" . $pflag . "')";
                                $process_flag = true;
                            }
                        }
                    } else {
                        if ($deptCount == 2 || $s_result[0] == 5 || $s_result[0] == 6) {
                            if ($a1[1] != "" && $a1[$dayCount - 2] != "") {
                                $query = "INSERT IGNORE INTO DayMaster (e_id, TDate, Entry, Start, BreakOut, BreakIn, Close, `Exit`, group_id, Flag) VALUES (" . $sdcur1[0] . ", " . $date . ", '" . $a1[0] . "', '" . $a1[1] . "', '" . $a1[1] . "', '" . $a1[1] . "', '" . $a1[$dayCount - 2] . "', '" . $a1[$dayCount - 1] . "', " . $sdcur[0] . ", '" . $pflag . "')";
                                $process_flag = true;
                            }
                        } else {
                            if ($a1[1] != "" && $a1[4] != "") {
                                $query = "INSERT IGNORE INTO DayMaster (e_id, TDate, Entry, Start, BreakOut, BreakIn, Close, `Exit`, group_id, Flag) VALUES (" . $sdcur1[0] . ", " . $date . ", '" . $a1[0] . "', '" . $a1[1] . "', '" . $a1[2] . "', '" . $a1[3] . "', '" . $a1[4] . "', '" . $a1[5] . "', " . $sdcur[0] . ", '" . $pflag . "')";
                                $process_flag = true;
                            }
                        }
                    }
                    if (updateIData($iconn, $query, true)) {
                        for ($k = 0; $k < $exitCount + $deptCount; $k++) {
                            $query = "UPDATE tenter SET p_flag = 1 WHERE ed = " . $a4[$k];
                            updateIData($iconn, $query, true);
                        }
                    } else {
                        mysqli_rollback($iconn);
                        echo "\n2605: Unable to INSERT: " . $query;
                        exit;
                    }
                }
            }
        }
    }
    mysqli_close($conn);
    mysqli_close($iconn);
    mysqli_close($jconn);
    mysqli_close($kconn);
    $conn = openConnection();
    $iconn = openIConnection();
    $jconn = openIConnection();
    $kconn = openIConnection();
    $query = "SELECT EX4, PLFlag, OTDateBalNHrs FROM OtherSettingMaster";
    $o_result = selectData($conn, $query);
    if ($o_result[1] != "Black") {
        $query = "UPDATE DayMaster SET Flag = '" . $o_result[1] . "' WHERE p_flag = 0 AND group_id <> 2 AND DayMaster.TDate IN (SELECT OTDate FROM OTDate) AND (Flag = 'Black' OR Flag = 'Proxy')";
        updateIData($iconn, $query, true);
    }
    if ($pay_off_employee == "") {
        displayDate($maxDate);
        displayToday();
        getNow();
        print "\nOvertime Calculations Started for " . displayDate($maxDate) . ": " . displayToday() . ", " . getNow() . " HRS";
        flush();
    }
    $query = "SELECT id, name, Start, GraceTo, FlexiBreak, BreakFrom, BreakTo, Close, NightFlag, ShiftTypeID, ScheduleID, WorkMin, MinWorkForBreak, MinOTWorkForBreak, RotateFlag, MinOT1Work, EarlyInOT, LessLunchOT, EarlyInOTDayDate, MoveNS FROM tgroup WHERE tgroup.ShiftTypeID = 1 AND id > 1";
    $result = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
        $MinWorkForBreak = $cur[12] * 60;
        $MinOTWorkForBreak = $cur[13] * 60;
        $lstEarlyInOT = $cur[16];
        $lstLessLunchOT = $cur[17];
        $lstEarlyInOTDayDate = $cur[18];
        $lstMoveNS = $cur[19];
        $query = "SELECT DISTINCT(e_id) FROM DayMaster WHERE p_flag = 0 AND group_id = " . $cur[0];
        $result1 = mysqli_query($jconn, $query);
        while ($sdcur1 = mysqli_fetch_row($result1)) {
            $day = "";
            $ot_flag = false;
            $ot_date_flag = false;
            $OT1 = "";
            $OT2 = "";
            $tuser_ot_1_2_query = "SELECT e_id FROM FlagDayRotation WHERE e_id = '" . $sdcur1[0] . "' AND RecStat = 0 AND LENGTH(OT) > 1 AND e_date > " . $txtLockDate;
            $tuser_ot_1_2_result = selectData($conn, $tuser_ot_1_2_query);
            if (!is_numeric($tuser_ot_1_2_result[0])) {
                $tuser_ot_1_2_query = "SELECT OT1, OT2 FROM tuser WHERE id = '" . $sdcur1[0] . "' ";
                $tuser_ot_1_2_result = selectData($conn, $tuser_ot_1_2_query);
                $OT1 = $tuser_ot_1_2_result[0];
                $OT2 = $tuser_ot_1_2_result[1];
                if (!isset($OT1) || $OT1 === "") {
                    $OT1 = "Saturday"; // Assign "Saturday" only if $OT1 is not set or is an empty string  
                }

                if (!isset($OT2) || $OT2 === "") {
                    $OT2 = "Sunday"; // Assign "Sunday" only if $OT2 is not set or is an empty string  
                }
            }
            $ot_exempt_query = "SELECT OTEmployeeExemptID FROM OTEmployeeExempt WHERE EmployeeID = " . $sdcur1[0];
            $ot_exempt_result = selectData($conn, $ot_exempt_query);
            if ($ot_exempt_result[0] != "" && 0 < $ot_exempt_result[0]) {
                $ot_flag = false;
            } else {
                $ot_flag = true;
            }
            $ot_exempt_date_query = "SELECT OTEmployeeDateExemptID FROM OTEmployeeDateExempt WHERE EmployeeID = " . $sdcur1[0];
            $ot_exempt_date_result = selectData($conn, $ot_exempt_date_query);
            if ($ot_exempt_date_result[0] != "" && 0 < $ot_exempt_date_result[0]) {
                $ot_date_flag = false;
            } else {
                $ot_date_flag = true;
            }
            $query = "SELECT DayMaster.DayMasterID, DayMaster.TDate, DayMaster.Entry, DayMaster.Start, DayMaster.BreakOut, DayMaster.BreakIn, DayMaster.Close, `Exit`, DayMaster.group_id, DayMaster.Flag, DayMaster.Work FROM DayMaster WHERE DayMaster.p_flag = 0 AND DayMaster.e_id = " . $sdcur1[0] . " AND DayMaster.group_id = " . $cur[0] . " ORDER BY DayMaster.DayMasterID";
            $result2 = mysqli_query($kconn, $query);
            while ($cur2 = mysqli_fetch_row($result2)) {
                $tuser_ot_1_2_query = "SELECT OT FROM FlagDayRotation WHERE e_id = '" . $sdcur1[0] . "' AND e_date = '" . $cur2[1] . "'";
                $tuser_ot_1_2_result = selectData($conn, $tuser_ot_1_2_query);
                if ($tuser_ot_1_2_result[0] == "OT1") {
                    $OT1 = getDay(displayDate($cur2[1]));
                } else {
                    if ($tuser_ot_1_2_result[0] == "OT2") {
                        $OT2 = getDay(displayDate($cur2[1]));
                    }
                }
                if ($OT1 == "") {
                    $OT1 = "Saturday";
                }
                if ($OT2 == "") {
                    $OT2 = "Sunday";
                }
                $hour = (int) substr($cur2[3], 0, 2);
                $minute = (int) substr($cur2[3], 2, 2);
                $second = (int) substr($cur2[3], 4, 2); // Will be 0 if empty

                $month = (int) substr($cur2[1], 4, 2);
                $day = (int) substr($cur2[1], 6, 2);
                $year = (int) substr($cur2[1], 0, 4);

                $start = mktime($hour, $minute, $second, $month, $day, $year);
//                $start = mktime(substr($cur2[3], 0, 2), substr($cur2[3], 2, 2), substr($cur2[3], 4, 2), substr($cur2[1], 4, 2), substr($cur2[1], 6, 2), substr($cur2[1], 0, 4));
                $temp_array = getDate($start);
                $day = $temp_array["weekday"];
                $bout = 0;
                $bin = 0;
                $close = 0;
                $normal = 0;
                $ot = 0;
                $ibout = 0;
                $ibin = 0;
                $iclose = 0;
                $istart = mktime((int)substr($cur[2], 0, 2), (int)substr($cur[2], 2, 2), 0, (int)substr($cur2[1], 4, 2), (int)substr($cur2[1], 6, 2), (int)substr($cur2[1], 0, 4));
                $igrace = mktime((int)substr($cur[3], 0, 2), (int)substr($cur[3], 2, 2), 0, (int)substr($cur2[1], 4, 2), (int)substr($cur2[1], 6, 2), (int)substr($cur2[1], 0, 4));
                if ($cur2[4] < $cur2[3]) {
                    $next = strtotime((int)substr($cur2[1], 6, 2) . "-" . (int)substr($cur2[1], 4, 2) . "-" . (int)substr($cur2[1], 0, 4) . " + 1 day");
                    $a = getDate($next);
                    $m = $a["mon"];
                    if ($m < 10) {
                        $m = "0" . $m;
                    }
                    $d = $a["mday"];
                    if ($d < 10) {
                        $d = "0" . $d;
                    }
                    $bout = mktime(
                            (int) substr($cur2[4], 0, 2), // hour
                            (int) substr($cur2[4], 2, 2), // minute
                            (int) (substr($cur2[4], 4, 2) !== '' ? substr($cur2[4], 4, 2) : 0), // seconds (default 0)
                            (int) substr($cur2[1], 4, 2), // month
                            (int) substr($cur2[1], 6, 2), // day
                            (int) substr($cur2[1], 0, 4)                          // year
                    );
//                    $bout = mktime(substr($cur2[4], 0, 2), substr($cur2[4], 2, 2), substr($cur2[4], 4, 2), $m, $d, $a["year"]);
                    if (strlen($cur[5]) == 4) {
                        $ibout = mktime((int)substr($cur[5], 0, 2), (int)substr($cur[5], 2, 2), 0, $m, $d, $a["year"]);
                    }
                } else {
                    $bout = mktime((int)substr($cur2[4], 0, 2), (int)substr($cur2[4], 2, 2), (int)substr($cur2[4], 4, 2), (int)substr($cur2[1], 4, 2), (int)substr($cur2[1], 6, 2), (int)substr($cur2[1], 0, 4));
                    if (strlen($cur[5]) == 4) {
                        $ibout = mktime((int)substr($cur[5], 0, 2), (int)substr($cur[5], 2, 2), 0, (int)substr($cur2[1], 4, 2), (int)substr($cur2[1], 6, 2), (int)substr($cur2[1], 0, 4));
                    }
                }
                if ($cur2[5] < $cur2[3]) {
                    $next = strtotime((int)substr($cur2[1], 6, 2) . "-" . (int)substr($cur2[1], 4, 2) . "-" . (int)substr($cur2[1], 0, 4) . " + 1 day");
                    $a = getDate($next);
                    $m = $a["mon"];
                    if ($m < 10) {
                        $m = "0" . $m;
                    }
                    $d = $a["mday"];
                    if ($d < 10) {
                        $d = "0" . $d;
                    }
                    $bin = mktime((int)substr($cur2[5], 0, 2), (int)substr($cur2[5], 2, 2), (int)substr($cur2[5], 4, 2), $m, $d, $a["year"]);
                    if (strlen($cur[6]) == 4) {
                        $ibin = mktime((int)substr($cur[6], 0, 2), (int)substr($cur[6], 2, 2), 0, $m, $d, $a["year"]);
                    }
                } else {
                    $hour = (int) substr($cur2[5], 0, 2);
                    $minute = (int) substr($cur2[5], 2, 2);
                    $second = (int) substr($cur2[5], 4, 2); // If empty, becomes 0 automatically

                    $month = (int) substr($cur2[1], 4, 2);
                    $day = (int) substr($cur2[1], 6, 2);
                    $year = (int) substr($cur2[1], 0, 4);

                    $bin = mktime($hour, $minute, $second, $month, $day, $year);
//                    $bin = mktime(substr($cur2[5], 0, 2), substr($cur2[5], 2, 2), substr($cur2[5], 4, 2), substr($cur2[1], 4, 2), substr($cur2[1], 6, 2), substr($cur2[1], 0, 4));
                    if (strlen($cur[6]) == 4) {
                        $ibin = mktime((int)substr($cur[6], 0, 2), (int)substr($cur[6], 2, 2), 0, (int)substr($cur2[1], 4, 2), (int)substr($cur2[1], 6, 2), (int)substr($cur2[1], 0, 4));
                    }
                }
                if ($cur2[6] < $cur2[3]) {
                    $next = strtotime((int)substr($cur2[1], 6, 2) . "-" . (int)substr($cur2[1], 4, 2) . "-" . (int)substr($cur2[1], 0, 4) . " + 1 day");
                    $a = getDate($next);
                    $m = $a["mon"];
                    if ($m < 10) {
                        $m = "0" . $m;
                    }
                    $d = $a["mday"];
                    if ($d < 10) {
                        $d = "0" . $d;
                    }
                    $hour = (int) substr($cur2[6], 0, 2);
                    $minute = (int) substr($cur2[6], 2, 2);
                    $second = (int) substr($cur2[6], 4, 2); // empty becomes 0 automatically

                    $close = mktime($hour, $minute, $second, $m, $d, (int) $a["year"]);
//                    $close = mktime(substr($cur2[6], 0, 2), substr($cur2[6], 2, 2), substr($cur2[6], 4, 2), $m, $d, $a["year"]);
                    if (strlen($cur[7]) == 4) {
                        $iclose = mktime((int)substr($cur[7], 0, 2), (int)substr($cur[7], 2, 2), 0, $m, $d, $a["year"]);
                    }
                } else {
                    if ($cur[8] == 0) {
                        $close = mktime((int)substr($cur2[6], 0, 2), (int)substr($cur2[6], 2, 2), (int)substr($cur2[6], 4, 2), (int)substr($cur2[1], 4, 2), (int)substr($cur2[1], 6, 2), (int)substr($cur2[1], 0, 4));
                        if (strlen($cur[7]) == 4) {
                            $iclose = mktime((int)substr($cur[7], 0, 2), (int)substr($cur[7], 2, 2), 0, (int)substr($cur2[1], 4, 2), (int)substr($cur2[1], 6, 2), (int)substr($cur2[1], 0, 4));
                        }
                    } else {
                        $close = mktime((int)substr($cur2[6], 0, 2), (int)substr($cur2[6], 2, 2), (int)substr($cur2[6], 4, 2), (int)substr($cur2[1], 4, 2), (int)substr($cur2[1], 6, 2), (int)substr($cur2[1], 0, 4));
                        $next = strtotime(substr($cur2[1], 6, 2) . "-" . substr($cur2[1], 4, 2) . "-" . substr($cur2[1], 0, 4) . " + 1 day");
                        $a = getDate($next);
                        $m = $a["mon"];
                        if ($m < 10) {
                            $m = "0" . $m;
                        }
                        $d = $a["mday"];
                        if ($d < 10) {
                            $d = "0" . $d;
                        }
                        if (strlen($cur[7]) == 4) {
                            $iclose = mktime((int)substr($cur[7], 0, 2), (int)substr($cur[7], 2, 2), 0, $m, $d, $a["year"]);
                        }
                    }
                }
                if ($cur[10] == 1 || $cur[10] == 2 || $cur[10] == 5) {
                    $normal = $cur[11] * 60;
                    $ot = 0;
                    $query = "INSERT INTO AttendanceMaster (AttendanceMaster.EmployeeID, AttendanceMaster.EmpID, AttendanceMaster.group_id, AttendanceMaster.group_min, AttendanceMaster.ADate, AttendanceMaster.EarlyIn, AttendanceMaster.Grace, AttendanceMaster.LateIn, AttendanceMaster.Break, AttendanceMaster.LessBreak, AttendanceMaster.MoreBreak, AttendanceMaster.EarlyOut, AttendanceMaster.LateOut, AttendanceMaster.Normal, AttendanceMaster.Overtime, AttendanceMaster.Day, AttendanceMaster.Flag, AttendanceMaster.OT1, AttendanceMaster.OT2, AttendanceMaster.NightFlag, AttendanceMaster.RotateFlag) VALUES (" . $sdcur1[0] . ", '" . addZero($sdcur1[0], $txtECodeLength) . "', " . $cur2[8] . ", " . $cur[11] . ", " . $cur2[1] . ", ";
                    if ($start <= $istart) {
                        $query = $query . ($istart - $start) . ", 0, 0, ";
                        if ($lstEarlyInOT == "Yes") {
                            $ot = $istart - $start;
                        }
                    } else {
                        if ($start <= $igrace) {
                            $query = $query . " 0, " . ($start - $istart) . ", 0, ";
                        } else {
                            $query = $query . " 0, 0, " . ($start - $istart) . ", ";
                            $normal = $normal - ($start - $istart);
                        }
                    }
                    if ($cur[10] == 2) {
                        if ($bin - $bout <= $cur[4] * 60) {
                            $query = $query . ($bin - $bout) . ", " . ($cur[4] * 60 - ($bin - $bout)) . ", 0, ";
                            if ($lstLessLunchOT == "Yes") {
                                $ot = $ot + $cur[4] * 60 - ($bin - $bout);
                            }
                        } else {
                            $query = $query . ($bin - $bout) . ", 0, " . ($bin - $bout - $cur[4] * 60) . ", ";
                            $normal = $normal - ($bin - $bout - $cur[4] * 60);
                        }
                    } else {
                        if ($cur[10] == 1) {
                            if ($cur[11] * 60 <= $cur2[10] && $cur2[10] <= $cur[11] * 60 + $cur[4] * 60) {
                                $query = $query . ($cur[11] * 60 + $cur[4] * 60 - $cur2[10]) . ", " . ($cur[4] * 60 - ($cur2[10] - $cur[11] * 60)) . ", 0, ";
                                if ($lstLessLunchOT == "Yes") {
                                    $ot = $ot + ($cur[11] * 60 + $cur[4] * 60) - $cur2[10];
                                }
                            } else {
                                if ($cur[11] * 60 + $cur[4] * 60 <= $cur2[10]) {
                                    $query = $query . "0, " . $cur[4] * 60 . ", 0, ";
                                    if ($lstLessLunchOT == "Yes") {
                                        $ot = $ot + ($cur[11] * 60 + $cur[4] * 60) - $cur2[10];
                                    }
                                } else {
                                    if ($cur2[10] < $cur[11] * 60) {
                                        $query = $query . ($cur2[10] - $cur[11] * 60) . ", 0, " . ($cur2[10] - $cur[11] * 60 - $cur[4] * 60) . ", ";
                                    }
                                }
                            }
                        } else {
                            if ($cur[10] == 5) {
                                $query = $query . $cur[4] * 60 . ", 0, 0, ";
                            }
                        }
                    }
                    if ($close <= $iclose) {
                        $query = $query . ($iclose - $close) . ", 0, ";
                        $normal = $normal - ($iclose - $close);
                    } else {
                        $query = $query . " 0, " . ($close - $iclose) . ", ";
                        $ot = $ot + $close - $iclose;
                    }
                    if ($normal < $cur[11] * 60) {
                        $diff = $cur[11] * 60 - $normal;
                        if ($ot != 0) {
                            if ($diff <= $ot) {
                                $normal = $normal + $diff;
                                $ot = $ot - $diff;
                            } else {
                                $normal = $normal + $ot;
                                $ot = 0;
                            }
                        }
                    }
                    $ot_query = "SELECT OT FROM OTDay WHERE Day = '" . $day . "'";
                    $ot_result = selectData($conn, $ot_query);
                    if (($ot_flag == true || $ot_flag == 1) && $ot_result[0] == 1 && ($day == $OT1 || $day == $OT2)) {
                        $this_ot = $normal + $ot;
                        if ($lstEarlyInOTDayDate == "Yes" && $start < $istart && $lstEarlyInOT != "Yes") {
                            $this_ot = $this_ot + $istart - $start;
                        }
                        if ($day == $OT1 && $this_ot < $cur[15] * 60) {
                            $normal = $this_ot;
                            $this_ot = 0;
                        } else {
                            $normal = 0;
                        }
                        if ($cur[13] * 60 < $this_ot) {
                            $query = $query . $normal . ", " . $this_ot . ", '" . $day . "', '" . $cur2[9] . "', '" . $OT1 . "', '" . $OT2 . "', " . $cur[8] . ", " . $cur[14] . ")";
                        } else {
                            $query = $query . $normal . ", " . ($this_ot + $cur[4] * 60) . ", '" . $day . "', '" . $cur2[9] . "', '" . $OT1 . "', '" . $OT2 . "', " . $cur[8] . ", " . $cur[14] . ")";
                        }
                    } else {
                        if (($ot_flag == false || $ot_flag == 0) && ($OT1 != "" && $OT1 == $day || $OT2 != "" && $OT2 == $day)) {
                            $this_ot = $normal + $ot;
                            if ($lstEarlyInOTDayDate == "Yes" && $start < $istart && $lstEarlyInOT != "Yes") {
                                $this_ot = $this_ot + $istart - $start;
                            }
                            if ($day == $OT1 && $this_ot < $cur[15] * 60) {
                                $normal = $this_ot;
                                $this_ot = 0;
                            } else {
                                $normal = 0;
                            }
                            if ($cur[13] * 60 < $this_ot) {
                                $query = $query . $normal . ", " . $this_ot . ", '" . $day . "', '" . $cur2[9] . "', '" . $OT1 . "', '" . $OT2 . "', " . $cur[8] . ", " . $cur[14] . ")";
                            } else {
                                $query = $query . $normal . ", " . ($this_ot + $cur[4] * 60) . ", '" . $day . "', '" . $cur2[9] . "', '" . $OT1 . "', '" . $OT2 . "', " . $cur[8] . ", " . $cur[14] . ")";
                            }
                        } else {
                            $ot_query = "SELECT OTDateID FROM OTDate WHERE OTDate = " . $cur2[1];
                            $ot_result = selectData($conn, $ot_query);
                            if ($ot_result[0] != "" && $ot_date_flag == true && $cur2[8] != 2) {
                                $this_ot = $normal + $ot;
                                if ($lstEarlyInOTDayDate == "Yes" && $start < $istart && $lstEarlyInOT != "Yes") {
                                    $this_ot = $this_ot + $istart - $start;
                                }
                                if ($cur[13] * 60 < $this_ot) {
                                    $query = $query . "0, " . $this_ot . ", '" . $day . "', '" . $cur2[9] . "', '" . $OT1 . "', '" . $OT2 . "', " . $cur[8] . ", " . $cur[14] . ")";
                                } else {
                                    $query = $query . "0, " . ($this_ot + $cur[4] * 60) . ", '" . $day . "', '" . $cur2[9] . "', '" . $OT1 . "', '" . $OT2 . "', " . $cur[8] . ", " . $cur[14] . ")";
                                }
                            } else {
                                $query = $query . $normal . ", " . $ot . ", '" . $day . "', '" . $cur2[9] . "', '" . $OT1 . "', '" . $OT2 . "', " . $cur[8] . ", " . $cur[14] . ")";
                            }
                        }
                    }
                    if (updateIData($iconn, $query, true)) {
                        $query = "UPDATE DayMaster SET p_flag = 1 WHERE DayMasterID = " . $cur2[0];
                        updateIData($iconn, $query, true);
                    }
                } else {
                    if ($cur[10] == 3) {
                        $normal = $cur[11] * 60;
                        $ot = 0;
                        $query = "INSERT INTO AttendanceMaster (AttendanceMaster.EmployeeID, AttendanceMaster.EmpID, AttendanceMaster.group_id, AttendanceMaster.group_min, AttendanceMaster.ADate, AttendanceMaster.EarlyIn, AttendanceMaster.Grace, AttendanceMaster.LateIn, AttendanceMaster.Break, AttendanceMaster.LessBreak, AttendanceMaster.MoreBreak, AttendanceMaster.EarlyOut, AttendanceMaster.LateOut, AttendanceMaster.Normal, AttendanceMaster.Overtime, AttendanceMaster.Day, AttendanceMaster.Flag, AttendanceMaster.OT1, AttendanceMaster.OT2, AttendanceMaster.NightFlag, AttendanceMaster.RotateFlag) VALUES (" . $sdcur1[0] . ", '" . addZero($sdcur1[0], $txtECodeLength) . "', " . $cur2[8] . ", " . $cur[11] . ", " . $cur2[1] . ", ";
                        if ($start <= $istart) {
                            $query = $query . ($istart - $start) . ", 0, 0, ";
                            if ($lstEarlyInOT == "Yes") {
                                $ot = $istart - $start;
                            }
                        } else {
                            if ($start <= $igrace) {
                                $query = $query . " 0, " . ($start - $istart) . ", 0, ";
                            } else {
                                $query = $query . " 0, 0, " . ($start - $istart) . ", ";
                                $normal = $normal - ($start - $istart);
                            }
                        }
                        if ($bin - $bout <= $ibin - $ibout) {
                            $query = $query . ($bin - $bout) . ", " . ($ibin - $ibout - ($bin - $bout)) . ", 0, ";
                            if ($lstLessLunchOT == "Yes") {
                                $ot = $ot + $ibin - $ibout - ($bin - $bout);
                            }
                        } else {
                            $query = $query . ($bin - $bout) . ", 0, " . ($bin - $bout - ($ibin - $ibout)) . ", ";
                            $normal = $normal - ($bin - $bout - ($ibin - $ibout));
                        }
                        if ($close <= $iclose) {
                            $query = $query . ($iclose - $close) . ", 0, ";
                            $normal = $normal - ($iclose - $close);
                        } else {
                            $query = $query . " 0, " . ($close - $iclose) . ", ";
                            $ot = $ot + $close - $iclose;
                        }
                        if ($normal < $cur[11] * 60) {
                            $diff = $cur[11] * 60 - $normal;
                            if ($ot != 0) {
                                if ($diff <= $ot) {
                                    $normal = $normal + $diff;
                                    $ot = $ot - $diff;
                                } else {
                                    $normal = $normal + $ot;
                                    $ot = 0;
                                }
                            }
                        }
                        $ot_query = "SELECT OT FROM OTDay WHERE Day = '" . $day . "'";
                        $ot_result = selectData($conn, $ot_query);
                        if (($ot_flag == true || $ot_flag == 1) && $ot_result[0] == 1 && ($day == $OT1 || $day == $OT2)) {
                            $this_ot = $normal + $ot;
                            if ($lstEarlyInOTDayDate == "Yes" && $start < $istart && $lstEarlyInOT != "Yes") {
                                $this_ot = $this_ot + $istart - $start;
                            }
                            if ($day == $OT1 && $this_ot < $cur[15] * 60) {
                                $normal = $this_ot;
                                $this_ot = 0;
                            } else {
                                $normal = 0;
                            }
                            if ($cur[13] * 60 < $this_ot) {
                                $query = $query . $normal . ", " . $this_ot . ", '" . $day . "', '" . $cur2[9] . "', '" . $OT1 . "', '" . $OT2 . "', " . $cur[8] . ", " . $cur[14] . ")";
                            } else {
                                $query = $query . $normal . ", " . ($this_ot + $ibin - $ibout) . ", '" . $day . "', '" . $cur2[9] . "', '" . $OT1 . "', '" . $OT2 . "', " . $cur[8] . ", " . $cur[14] . ")";
                            }
                        } else {
                            if ($ot_flag == false && ($OT1 != "" && $OT1 == $day || $OT2 != "" && $OT2 == $day)) {
                                $this_ot = $normal + $ot;
                                if ($lstEarlyInOTDayDate == "Yes" && $start < $istart && $lstEarlyInOT != "Yes") {
                                    $this_ot = $this_ot + $istart - $start;
                                }
                                if ($day == $OT1 && $this_ot < $cur[15] * 60) {
                                    $normal = $this_ot;
                                    $this_ot = 0;
                                } else {
                                    $normal = 0;
                                }
                                if ($cur[13] * 60 < $this_ot) {
                                    $query = $query . $normal . ", " . $this_ot . ", '" . $day . "', '" . $cur2[9] . "', '" . $OT1 . "', '" . $OT2 . "', " . $cur[8] . ", " . $cur[14] . ")";
                                } else {
                                    $query = $query . $normal . ", " . ($this_ot + $ibin - $ibout) . ", '" . $day . "', '" . $cur2[9] . "', '" . $OT1 . "', '" . $OT2 . "', " . $cur[8] . ", " . $cur[14] . ")";
                                }
                            } else {
                                $ot_query = "SELECT OTDateID FROM OTDate WHERE OTDate = " . $cur2[1];
                                $ot_result = selectData($conn, $ot_query);
                                if ($ot_result[0] != "" && $ot_date_flag == true && $cur2[8] != 2) {
                                    $this_ot = $normal + $ot;
                                    if ($lstEarlyInOTDayDate == "Yes" && $start < $istart && $lstEarlyInOT != "Yes") {
                                        $this_ot = $this_ot + $istart - $start;
                                    }
                                    if ($cur[13] * 60 < $this_ot) {
                                        $query = $query . "0, " . $this_ot . ", '" . $day . "', '" . $cur2[9] . "', '" . $OT1 . "', '" . $OT2 . "', " . $cur[8] . ", " . $cur[14] . ")";
                                    } else {
                                        $query = $query . "0, " . ($this_ot + $ibin - $ibout) . ", '" . $day . "', '" . $cur2[9] . "', '" . $OT1 . "', '" . $OT2 . "', " . $cur[8] . ", " . $cur[14] . ")";
                                    }
                                } else {
                                    $query = $query . $normal . ", " . $ot . ", '" . $day . "', '" . $cur2[9] . "', '" . $OT1 . "', '" . $OT2 . "', " . $cur[8] . ", " . $cur[14] . ")";
                                }
                            }
                        }
                        if (updateIData($iconn, $query, true)) {
                            $query = "UPDATE DayMaster SET p_flag = 1 WHERE DayMasterID = " . $cur2[0];
                            updateIData($iconn, $query, true);
                        }
                    } else {
                        if ($cur[10] == 6 || $cur[10] == 7) {
                            $normal = $cur[11] * 60;
                            $break = $cur[4] * 60;
                            if ($cur[10] == 7) {
                                if ($txtNightShiftMaxOutTime . "00" < $cur2[3] && $date < $cur[0]) {
                                    $cur[8] = 1;
                                } else {
                                    $cur[8] = 0;
                                }
                            }
                            $ot = 0;
                            $query = "INSERT INTO AttendanceMaster (AttendanceMaster.EmployeeID, AttendanceMaster.EmpID, AttendanceMaster.group_id, AttendanceMaster.group_min, AttendanceMaster.ADate, AttendanceMaster.EarlyIn, AttendanceMaster.Grace, AttendanceMaster.LateIn, AttendanceMaster.Break, AttendanceMaster.LessBreak, AttendanceMaster.MoreBreak, AttendanceMaster.EarlyOut, AttendanceMaster.LateOut, AttendanceMaster.Normal, AttendanceMaster.Overtime, AttendanceMaster.Day, AttendanceMaster.Flag, AttendanceMaster.OT1, AttendanceMaster.OT2, AttendanceMaster.NightFlag, AttendanceMaster.RotateFlag) VALUES (" . $sdcur1[0] . ", '" . addZero($sdcur1[0], $txtECodeLength) . "', " . $cur2[8] . ", " . $cur[11] . ", " . $cur2[1] . ", 0, 0, 0, 0, 0, 0, 0, 0, ";
                            if ($normal + $break < $close - $start) {
                                $ot = $close - $start - ($normal + $break);
                            } else {
                                $normal = $close - $start - $break;
                            }
                            $ot_query = "SELECT OT FROM OTDay WHERE Day = '" . $day . "'";
                            $ot_result = selectData($conn, $ot_query);
                            if (($ot_flag == true || $ot_flag == 1) && $ot_result[0] == 1 && ($day == $OT1 || $day == $OT2)) {
                                $this_ot = $normal + $ot;
                                if ($day == $OT1 && $this_ot < $cur[15] * 60) {
                                    $normal = $this_ot;
                                    $this_ot = 0;
                                } else {
                                    $normal = 0;
                                }
                                if ($cur[13] * 60 < $this_ot) {
                                    $query = $query . $normal . ", " . $this_ot . ", '" . $day . "', '" . $cur2[9] . "', '" . $OT1 . "', '" . $OT2 . "', " . $cur[8] . ", " . $cur[14] . ")";
                                } else {
                                    $query = $query . $normal . ", " . ($this_ot + $cur[4] * 60) . ", '" . $day . "', '" . $cur2[9] . "', '" . $OT1 . "', '" . $OT2 . "', " . $cur[8] . ", " . $cur[14] . ")";
                                }
                            } else {
                                if ($ot_flag == false && ($OT1 != "" && $OT1 == $day || $OT2 != "" && $OT2 == $day)) {
                                    $this_ot = $normal + $ot;
                                    if ($day == $OT1 && $this_ot < $cur[15] * 60) {
                                        $normal = $this_ot;
                                        $this_ot = 0;
                                    } else {
                                        $normal = 0;
                                    }
                                    if ($cur[13] * 60 < $this_ot) {
                                        $query = $query . $normal . ", " . $this_ot . ", '" . $day . "', '" . $cur2[9] . "', '" . $OT1 . "', '" . $OT2 . "', " . $cur[8] . ", " . $cur[14] . ")";
                                    } else {
                                        $query = $query . $normal . ", " . ($this_ot + $cur[4] * 60) . ", '" . $day . "', '" . $cur2[9] . "', '" . $OT1 . "', '" . $OT2 . "', " . $cur[8] . ", " . $cur[14] . ")";
                                    }
                                } else {
                                    $ot_query = "SELECT OTDateID FROM OTDate WHERE OTDate = " . $cur2[1];
                                    $ot_result = selectData($conn, $ot_query);
                                    if ($ot_result[0] != "" && $ot_date_flag == true && $cur2[8] != 2) {
                                        $this_ot = $normal + $ot;
                                        if ($cur[13] * 60 < $this_ot) {
                                            $query = $query . "0, " . $this_ot . ", '" . $day . "', '" . $cur2[9] . "', '" . $OT1 . "', '" . $OT2 . "', " . $cur[8] . ", " . $cur[14] . ")";
                                        } else {
                                            $query = $query . "0, " . ($this_ot + $cur[4] * 60) . ", '" . $day . "', '" . $cur2[9] . "', '" . $OT1 . "', '" . $OT2 . "', " . $cur[8] . ", " . $cur[14] . ")";
                                        }
                                    } else {
                                        $query = $query . $normal . ", " . $ot . ", '" . $day . "', '" . $cur2[9] . "', '" . $OT1 . "', '" . $OT2 . "', " . $cur[8] . ", " . $cur[14] . ")";
                                    }
                                }
                            }
                            if ($cur[10] == 7) {
                                
                            }
                            if (updateIData($iconn, $query, true)) {
                                $query = "UPDATE DayMaster SET p_flag = 1 WHERE DayMasterID = " . $cur2[0];
                                updateIData($iconn, $query, true);
                            }
                        } else {
                            if ($cur[10] != 4) {
                                
                            }
                        }
                    }
                }
            }
        }
    }
    if ($o_result[0] == 1) {
        $proxy_flag = "Proxy";
        $query = "SELECT Flag FROM FlagTitle WHERE FlagLink = 'Purple'";
        $result = selectData($conn, $query);
        if ($result[0] == "Pink") {
            $proxy_flag = "Pink";
        }
        $query = "SELECT OTDate FROM OTDate WHERE OTDate < " . $maxDate . " AND OTDate >= " . $txtLockDate;
        $result = mysqli_query($conn, $query);
        while ($cur = mysqli_fetch_row($result)) {
            $query = "SELECT tuser.id, tuser.group_id, tuser.dept, tgroup.NightFlag FROM tuser, tgroup WHERE tuser.group_id = tgroup.id AND tuser.id NOT IN (SELECT DISTINCT(tenter.e_id) FROM tenter, tgate, tgroup WHERE tenter.g_id = tgate.id AND tenter.e_group = tgroup.id AND tgate.exit = 0 AND ((tenter.e_time > '" . $txtNightShiftMaxOutTime . "00' AND tgroup.NightFlag = 1 AND tenter.e_date = '" . $cur[0] . "' AND tgroup.MoveNS = 'No') OR (tenter.e_time > '" . $txtNightShiftMaxOutTime . "00' AND tgroup.NightFlag = 1 AND tenter.e_date = '" . getLastDay($cur[0], 1) . "' AND tgroup.MoveNS = 'Yes') OR (tenter.e_time > '000000' AND tgroup.NightFlag = 0 AND tenter.e_date = '" . $cur[0] . "'))) AND tuser.id NOT IN (SELECT EmployeeID FROM ProxyEmployeeExempt) AND SUBSTRING(tuser.datelimit, 10, 8) <= '" . $cur[0] . "' ";
            $counter = 0;
            $this_result = mysqli_query($conn, $query);
            while ($this_cur = mysqli_fetch_row($this_result)) {
                $counter++;
                $sub_query = "SELECT g_id FROM DeptGate WHERE dept = '" . $this_cur[2] . "' ORDER BY g_id";
                $sub_result = selectData($conn, $sub_query);
                insertAttendance($conn, $iconn, $this_cur[0], displayDate($cur[0]), displayDate($cur[0]), 2, $proxy_flag, $sub_result[0]);
            }
            $query = "UPDATE AttendanceMaster, DayMaster SET AttendanceMaster.Flag = '" . $proxy_flag . "', DayMaster.Flag = '" . $proxy_flag . "', AttendanceMaster.Remark = CONCAT(AttendanceMaster.Remark, ', ', 'Changed PreFlag to Proxy') WHERE AttendanceMaster.ADate = DayMaster.TDate AND AttendanceMaster.EmployeeID = DayMaster.e_id AND AttendanceMaster.Flag <> '" . $proxy_flag . "' AND AttendanceMaster.Flag <> 'Black' AND AttendanceMaster.Flag <> 'Proxy' AND AttendanceMaster.Overtime = 0 AND AttendanceMaster.Normal = AttendanceMaster.group_min*60 AND AttendanceMaster.ADate = " . $cur[0];
            if (updateIData($iconn, $query, true)) {
                $query = "DELETE FROM FlagDayRotation WHERE Flag <> '" . $proxy_flag . "' AND e_date = " . $cur[0] . " AND e_id IN (SELECT EmployeeID FROM AttendanceMaster WHERE Remark LIKE '%Changed PreFlag to " . $proxy_flag . "%' AND Flag = '" . $proxy_flag . "')";
                updateIData($jconn, $query, true);
            }
        }
    }
    if ($o_result[2] == 1) {
        $query = "SELECT OTDate FROM OTDate WHERE OTDate < " . $maxDate . " AND OTDate >= " . $txtLockDate;
        $result = mysqli_query($conn, $query);
        while ($cur = mysqli_fetch_row($result)) {
            $query = "UPDATE AttendanceMaster SET AttendanceMaster.Normal = ((AttendanceMaster.group_min*60) - AttendanceMaster.Overtime) WHERE AttendanceMaster.ADate = " . $cur[0] . " AND AttendanceMaster.Normal = 0 AND (AttendanceMaster.group_min*60) > AttendanceMaster.Overtime AND AttendanceMaster.group_id <> 2 ";
            updateIData($iconn, $query, true);
        }
    } else {
        if ($o_result[2] == 2) {
            $query = "SELECT OTDate FROM OTDate WHERE OTDate < " . $maxDate . " AND OTDate >= " . $txtLockDate;
            $result = mysqli_query($conn, $query);
            while ($cur = mysqli_fetch_row($result)) {
                $query = "UPDATE AttendanceMaster SET AttendanceMaster.Normal = AttendanceMaster.group_min*60 WHERE AttendanceMaster.ADate = " . $cur[0] . " AND AttendanceMaster.Normal = 0 AND AttendanceMaster.Overtime > 0 AND AttendanceMaster.group_id <> 2 ";
                updateIData($iconn, $query, true);
            }
        }
    }
    $query = "SELECT tgroup.id, tgroup.NSOTCO, AttendanceMaster.EmployeeID, AttendanceMaster.Normal, AttendanceMaster.Overtime, DayMaster.Start, DayMaster.Close, AttendanceMaster.ADate, DayMaster.DayMasterID, AttendanceMaster.AttendanceID, tuser.OT1, tuser.OT2 FROM tgroup, AttendanceMaster, DayMaster, tuser WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.EmployeeID = DayMaster.e_id AND AttendanceMaster.ADate = DayMaster.TDate AND AttendanceMaster.EmployeeID = tuser.id AND ((AttendanceMaster.Overtime > (tgroup.NSOTCO*60) AND AttendanceMaster.Day <> AttendanceMaster.OT1 AND AttendanceMaster.Day <> AttendanceMaster.OT2) OR (AttendanceMaster.Overtime > ((tgroup.WorkMin + tgroup.NSOTCO)*60) AND (AttendanceMaster.Day = AttendanceMaster.OT1 OR AttendanceMaster.Day = AttendanceMaster.OT2))) AND DayMaster.Close > tgroup.Close AND tgroup.NSOTCO > 0 AND tgroup.NightFlag = 1 AND tgroup.id IN (SELECT id FROM ShiftChangeMaster) AND AttendanceMaster.ADate >  " . $txtLockDate;
    $result = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
        $query = "SELECT ShiftChangeMaster.id, tgroup.Start, tgroup.WorkMin FROM ShiftChangeMaster, tgroup WHERE ShiftChangeMaster.id = tgroup.id AND ShiftChangeMaster.idf = (SELECT idf FROM ShiftChangeMaster WHERE id = '" . $cur[0] . "') ";
        $sub_result = mysqli_query($jconn, $query);
        $last_id = "";
        $last_start = "";
        $last_min = "";
        while ($sub_cur = mysqli_fetch_row($sub_result)) {
            if ($sub_cur[0] == $cur[0] && $last_id != "" && $last_id != $sub_cur[0]) {
                break;
            }
            $last_id = $sub_cur[0];
            $last_start = $sub_cur[1];
            $last_min = $sub_cur[2];
        }
        if ($last_id != "") {
            $nextDate = getNextDay($cur[7], 1);
            $nextDay = getDay(displayDate($nextDate));
            if ($nextDate < $maxDate) {
                $carryover = mktime((int)substr($cur[6], 0, 2), (int)substr($cur[6], 2, 2), (int)substr($cur[6], 4, 2), (int)substr($nextDate, 4, 2), (int)substr($nextDate, 6, 2), (int)substr($nextDate, 0, 4)) - mktime((int)substr($last_start, 0, 2), (int)substr($last_start, 2, 2), 0, (int)substr($nextDate, 4, 2), (int)substr($nextDate, 6, 2), (int)substr($nextDate, 0, 4));
                $query = "UPDATE AttendanceMaster SET Overtime = (Overtime - " . $carryover . ") WHERE AttendanceID = " . $cur[9];
                if (updateIData($iconn, $query, true)) {
                    $query = "UPDATE DayMaster SET DayMaster.Close = '" . $last_start . "00' WHERE DayMasterID = " . $cur[8];
                    if (updateIData($iconn, $query, true)) {
                        $query = "SELECT ed, g_id FROM tenter WHERE e_id = '" . $cur[2] . "' AND e_date = '" . $nextDate . "' AND e_time = '" . $cur[6] . "' ";
                        $super_sub_result = selectData($conn, $query);
                        $query = "INSERT INTO tenter (e_date, e_time, g_id, e_id, e_group, e_user, e_mode, e_type, e_result, e_etc, p_flag) VALUES ('" . $nextDate . "', '" . $last_start . "00', " . $super_sub_result[1] . ", " . $cur[2] . ", " . $cur[0] . ", '0', '3', '3', '0', 'P', '1')";
                        if (updateIData($iconn, $query, true)) {
                            $query = "INSERT INTO tenter (e_date, e_time, g_id, e_id, e_group, e_user, e_mode, e_type, e_result, e_etc, p_flag) VALUES ('" . $nextDate . "', '" . $last_start . "01', " . $super_sub_result[1] . ", " . $cur[2] . ", " . $last_id . ", '0', '3', '3', '0', 'P', '1')";
                            if (updateIData($iconn, $query, true)) {
                                $query = "INSERT INTO DayMaster (e_id, TDate, Entry, Start, BreakOut, BreakIn, Close, `Exit`, group_id, Flag, p_flag) VALUES (" . $cur[2] . ", " . $nextDate . ", '" . $last_start . "01', '" . $last_start . "01', '" . $last_start . "01', '" . $last_start . "01', '" . $cur[6] . "', '" . $cur[6] . "', " . $last_id . ", 'Proxy', 1)";
                                if (updateIData($iconn, $query, true)) {
                                    if ($nextDay == $cur[10] || $nextDay == $cur[11]) {
                                        $query = "INSERT INTO AttendanceMaster (EmployeeID, EmpID, group_id, group_min, ADate, Normal, Overtime, Day, Flag, p_flag) VALUES (" . $cur[2] . ", '" . addZero($cur[2], $txtECodeLength) . "', " . $last_id . ", " . $last_min . ", " . $nextDate . ", 0, " . $carryover . ", '" . $nextDay . "', 'Proxy', '1')";
                                    } else {
                                        $query = "INSERT INTO AttendanceMaster (EmployeeID, EmpID, group_id, group_min, ADate, Normal, Overtime, Day, Flag, p_flag) VALUES (" . $cur[2] . ", '" . addZero($cur[2], $txtECodeLength) . "', " . $last_id . ", " . $last_min . ", " . $nextDate . ", " . $carryover . ", 0, '" . $nextDay . "', 'Proxy', '1')";
                                    }
                                    if (updateIData($iconn, $query, true)) {
                                        mysqli_commit($iconn);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    if ($lstAutoResetOT12 == "Yes") {
        $query = "SELECT e_id FROM FlagDayRotation WHERE e_date = " . $maxDate;
        $result = mysqli_query($conn, $query);
        while ($cur = mysqli_fetch_row($result)) {
            $query = "SELECT MIN(e_date) FROM FlagDayRotation WHERE LENGTH(OT) > 0 AND e_date > " . $maxDate . " AND e_id = " . $cur[0];
            $sub_result = selectData($conn, $query);
            if (getNextDay($maxDate, 9) < $sub_result[0] || $sub_result[0] == "") {
                $query = "UPDATE tuser SET OT1 = 'Saturday', OT2 = 'Sunday' WHERE e_id = " . $cur[0];
                updateIData($iconn, $query, true);
            }
        }
    }
    $query = "SELECT FlagDayRotation.e_id, FlagDayRotation.e_date, FlagDayRotation.OT, 0, FlagDayRotation.Rotate, FlagDayRotation.Flag, tuser.group_id, FlagDayRotation.RecStat FROM FlagDayRotation, tuser WHERE FlagDayRotation.e_date = " . $maxDate . " AND FlagDayRotation.e_id = tuser.id ORDER BY FlagDayRotation.e_id, FlagDayRotation.e_date ";
    $result = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
        $recStat = false;
        if ($cur[2] == "OT1") {
            $query = "UPDATE tuser SET OT1 = '" . getDay(displayDate($cur[1])) . "' WHERE id = " . $cur[0];
            if (updateIData($iconn, $query, true)) {
                $recStat = true;
            }
        } else {
            if ($cur[2] == "OT2") {
                $query = "UPDATE tuser SET OT2 = '" . getDay(displayDate($cur[1])) . "' WHERE id = " . $cur[0];
                if (updateIData($iconn, $query, true)) {
                    $recStat = true;
                }
            }
        }
        if ($cur[4] == 1 && $cur[7] == 0) {
            rotateShift($conn, $iconn, $cur[0], $cur[6]);
            $recStat = true;
        }
        if ($recStat) {
            $query = "UPDATE FlagDayRotation SET RecStat = 1 WHERE e_id = " . $cur[0] . " AND e_date = " . $cur[1];
            updateIData($iconn, $query, true);
        }
    }
    if ($process_flag) {
        $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('Daily', " . $maxDate . ", '" . getNow() . "')";
        updateIData($iconn, $query, true);
    }
    $process_date = getNextDay($process_date, 1);
}
$query = "SELECT COUNT(*) FROM FlagTitle WHERE FLagLink IS NOT NULL AND LENGTH(FLagLink) > 1";
$result = selectData($conn, $query);
if (0 < $result[0]) {
    $query = "UPDATE AttendanceMaster, FlagTitle, FlagDayRotation, DayMaster SET AttendanceMaster.Flag = FlagTitle.Flag, DayMaster.Flag = FlagTitle.Flag, FlagDayRotation.RecStat = 1 WHERE AttendanceMaster.ADate = FlagDayRotation.e_date AND AttendanceMaster.EmployeeID = FlagDayRotation.e_id AND DayMaster.TDate = FlagDayRotation.e_date AND DayMaster.e_id = FlagDayRotation.e_id AND FlagDayRotation.Flag = FlagTitle.FlagLink AND FlagDayRotation.RecStat = 0 AND LENGTH(FlagTitle.FlagLink) > 1 AND (AttendanceMaster.Flag = 'Black' OR AttendanceMaster.Flag = 'Proxy') AND ADate > " . $txtLockDate;
    updateIData($iconn, $query, true);
}
if ($lstSanSatOT == "Yes") {
    $query = "SELECT OT FROM OTDay WHERE Day = 'Saturday' ";
    $result = selectData($conn, $query);
    if ($result[0] == 0) {
        $query = "UPDATE AttendanceMaster SET AttendanceMaster.Overtime = (AttendanceMaster.Overtime + AttendanceMaster.Normal), AttendanceMaster.Normal = 0 WHERE AttendanceMaster.ADate IN (SELECT OTDate FROM SanitationDate) AND ADate > " . $txtLockDate;
        updateIData($iconn, $query, true);
    }
}
$query = "SELECT COUNT(*) FROM tgroup WHERE EarlyInOTDayDate = 'SAN'";
$result = selectData($conn, $query);
if (0 < $result[0]) {
    $query = "UPDATE AttendanceMaster, tgroup SET AttendanceMaster.EarlyIn_flag = 1, AttendanceMaster.Overtime = (AttendanceMaster.Overtime + AttendanceMaster.EarlyIn) WHERE AttendanceMaster.EarlyIn_flag = 0 AND AttendanceMaster.group_id = tgroup.id AND tgroup.EarlyInOTDayDate = 'SAN' AND AttendanceMaster.Adate IN (SELECT OTDate FROM SanitationDate) AND ADate > " . $txtLockDate;
    updateIData($jconn, $query, true);
}
$query = "SELECT COUNT(*) FROM tgroup WHERE MinWorkForBreak > 0";
$result = selectData($conn, $query);
if (0 < $result[0]) {
    $query = "UPDATE AttendanceMaster, tgroup SET Normal = (Normal + Break + LessBreak), LessBreak = 0, MoreBreak = 0, Break = 0, p_flag = 1 WHERE AttendanceMaster.group_id = tgroup.id AND Overtime = 0 AND AOvertime = 0 AND (AttendanceMaster.Break > 0 OR AttendanceMaster.LessBreak > 0) AND AttendanceMaster.Normal < (tgroup.MinWorkForBreak*60) AND AttendanceMaster.p_flag = 0 AND ADate > " . $txtLockDate;
    updateIData($kconn, $query, true);
}
$query = "SELECT COUNT(*) FROM tgroup WHERE MinOTWorkForBreak > 0";
$result = selectData($conn, $query);
if (0 < $result[0]) {
    $query = "UPDATE AttendanceMaster, tgroup SET LessBreak = 0, MoreBreak = 0, Break = 0, p_flag = 1 WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.Normal = 0 AND AttendanceMaster.Overtime > 0 AND (AttendanceMaster.Break > 0 OR AttendanceMaster.LessBreak > 0) AND (AttendanceMaster.Overtime*1 - AttendanceMaster.Break*1 - AttendanceMaster.LessBreak*1) < (tgroup.MinOTWorkForBreak*60) AND AttendanceMaster.p_flag = 0 AND AttendanceMaster.ADate > " . $txtLockDate;
    updateIData($iconn, $query, true);
}
$query = "SELECT COUNT(*) FROM SanitationDate WHERE OTDate > " . $txtLockDate;
$result = selectData($conn, $query);
if (0 < $result[0]) {
    $query = "UPDATE AttendanceMaster, tgroup SET AttendanceMaster.Remark = CONCAT(AttendanceMaster.Remark, ' Exempt Late IN'), AttendanceMaster.Overtime = (AttendanceMaster.Overtime + AttendanceMaster.LateIn), AttendanceMaster.LateIn_flag = 1 WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.LateIn_flag = 0 AND AttendanceMaster.ADate IN (SELECT OTDate from SanitationDate) AND tgroup.ExemptLI = 'SAN' AND AttendanceMaster.ADate > " . $txtLockDate;
    updateIData($jconn, $query, true);
}
$query = "SELECT COUNT(*) FROM tgroup WHERE ExemptLI LIKE '%OT1%' OR ExemptLI LIKE '%ALL%'";
$result = selectData($conn, $query);
if (0 < $result[0]) {
    $query = "UPDATE AttendanceMaster, tgroup SET AttendanceMaster.Remark = CONCAT(AttendanceMaster.Remark, ' Exempt Late IN'), AttendanceMaster.Overtime = (AttendanceMaster.Overtime + AttendanceMaster.LateIn), AttendanceMaster.LateIn_flag = 1 WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.LateIn_flag = 0 AND AttendanceMaster.Day = AttendanceMaster.OT1 AND (tgroup.ExemptLI LIKE '%OT1%' OR tgroup.ExemptLI LIKE '%ALL%')  AND AttendanceMaster.ADate > " . $txtLockDate;
    updateIData($kconn, $query, true);
}
$query = "SELECT COUNT(*) FROM tgroup WHERE ExemptLI LIKE '%OT2%' OR ExemptLI LIKE '%ALL%'";
$result = selectData($conn, $query);
if (0 < $result[0]) {
    $query = "UPDATE AttendanceMaster, tgroup SET AttendanceMaster.Remark = CONCAT(AttendanceMaster.Remark, ' Exempt Late IN'), AttendanceMaster.Overtime = (AttendanceMaster.Overtime + AttendanceMaster.LateIn), AttendanceMaster.LateIn_flag = 1 WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.LateIn_flag = 0 AND AttendanceMaster.Day = AttendanceMaster.OT2 AND (tgroup.ExemptLI LIKE '%OT2%' OR tgroup.ExemptLI LIKE '%ALL%')  AND AttendanceMaster.ADate > " . $txtLockDate;
    updateIData($iconn, $query, true);
}
$query = "SELECT COUNT(*) FROM tgroup WHERE ExemptLI LIKE '%OTD%' OR ExemptLI LIKE '%ALL%'";
$result = selectData($conn, $query);
if (0 < $result[0]) {
    $query = "UPDATE AttendanceMaster, tgroup SET AttendanceMaster.Remark = CONCAT(AttendanceMaster.Remark, ' Exempt Late IN'), AttendanceMaster.Overtime = (AttendanceMaster.Overtime + AttendanceMaster.LateIn), AttendanceMaster.LateIn_flag = 1 WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.LateIn_flag = 0 AND AttendanceMaster.Day = AttendanceMaster.OT2 AND (tgroup.ExemptLI LIKE '%OTD%' OR tgroup.ExemptLI LIKE '%ALL%')  AND AttendanceMaster.ADate > " . $txtLockDate;
    updateIData($jconn, $query, true);
}
$query = "SELECT COUNT(*) FROM tgroup WHERE ExemptLI LIKE '%ALL%'";
$result = selectData($conn, $query);
if (0 < $result[0]) {
    $query = "UPDATE AttendanceMaster, tgroup SET AttendanceMaster.Remark = CONCAT(AttendanceMaster.Remark, ' Exempt Late IN'), AttendanceMaster.Overtime = (AttendanceMaster.Overtime + AttendanceMaster.LateIn), AttendanceMaster.LateIn_flag = 1 WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.LateIn_flag = 0 AND tgroup.ExemptLI = 'ALL'  AND AttendanceMaster.ADate > " . $txtLockDate;
    updateIData($kconn, $query, true);
}
$query = "SELECT COUNT(*) FROM tgroup WHERE ExemptOT LIKE '%OT1%' OR ExemptOT LIKE '%ALL%'";
$result = selectData($conn, $query);
if (0 < $result[0]) {
    $query = "UPDATE AttendanceMaster, tgroup SET AttendanceMaster.Normal = AttendanceMaster.Overtime, AttendanceMaster.Overtime = 0 WHERE (AttendanceMaster.Remark = '' OR AttendanceMaster.Remark IS NULL) AND AttendanceMaster.Normal = 0 AND AttendanceMaster.AOvertime = 0 AND AttendanceMaster.Overtime > 0 AND AttendanceMaster.OT1 = AttendanceMaster.Day AND (tgroup.ExemptOT LIKE '%OT1%' OR tgroup.ExemptOT LIKE '%ALL%') AND AttendanceMaster.Overtime <= AttendanceMaster.group_min*60 AND AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.ADate > " . $txtLockDate;
    updateIData($iconn, $query, true);
    $query = "UPDATE AttendanceMaster, tgroup SET AttendanceMaster.Normal = AttendanceMaster.group_min*60, AttendanceMaster.Overtime = (AttendanceMaster.Overtime - AttendanceMaster.group_min*60) WHERE (AttendanceMaster.Remark = '' OR AttendanceMaster.Remark IS NULL) AND AttendanceMaster.Normal = 0 AND AttendanceMaster.AOvertime = 0 AND AttendanceMaster.Overtime > 0 AND AttendanceMaster.OT1 = AttendanceMaster.Day AND (tgroup.ExemptOT LIKE '%OT1%' OR tgroup.ExemptOT LIKE '%ALL%') AND AttendanceMaster.Overtime > AttendanceMaster.group_min*60 AND AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.ADate > " . $txtLockDate;
    updateIData($jconn, $query, true);
}
$query = "SELECT COUNT(*) FROM tgroup WHERE ExemptOT LIKE '%OT2%' OR ExemptOT LIKE '%ALL%'";
$result = selectData($conn, $query);
if (0 < $result[0]) {
    $query = "UPDATE AttendanceMaster, tgroup SET AttendanceMaster.Normal = AttendanceMaster.Overtime, AttendanceMaster.Overtime = 0 WHERE (AttendanceMaster.Remark = '' OR AttendanceMaster.Remark IS NULL) AND AttendanceMaster.Normal = 0 AND AttendanceMaster.AOvertime = 0 AND AttendanceMaster.Overtime > 0 AND AttendanceMaster.OT2 = AttendanceMaster.Day AND (tgroup.ExemptOT LIKE '%OT2%' OR tgroup.ExemptOT LIKE '%ALL%') AND AttendanceMaster.Overtime <= AttendanceMaster.group_min*60 AND AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.ADate > " . $txtLockDate;
    updateIData($kconn, $query, true);
    $query = "UPDATE AttendanceMaster, tgroup SET AttendanceMaster.Normal = AttendanceMaster.group_min*60, AttendanceMaster.Overtime = (AttendanceMaster.Overtime - AttendanceMaster.group_min*60) WHERE (AttendanceMaster.Remark = '' OR AttendanceMaster.Remark IS NULL) AND AttendanceMaster.Normal = 0 AND AttendanceMaster.AOvertime = 0 AND AttendanceMaster.Overtime > 0 AND AttendanceMaster.OT2 = AttendanceMaster.Day AND (tgroup.ExemptOT LIKE '%OT2%' OR tgroup.ExemptOT LIKE '%ALL%') AND AttendanceMaster.Overtime > AttendanceMaster.group_min*60 AND AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.ADate > " . $txtLockDate;
    updateIData($iconn, $query, true);
}
$query = "SELECT COUNT(*) FROM tgroup WHERE ExemptOT LIKE '%OTD%' OR ExemptOT LIKE '%ALL%'";
$result = selectData($conn, $query);
if (0 < $result[0]) {
    $query = "UPDATE AttendanceMaster, tgroup SET AttendanceMaster.Normal = AttendanceMaster.Overtime, AttendanceMaster.Overtime = 0 WHERE (AttendanceMaster.Remark = '' OR AttendanceMaster.Remark IS NULL) AND AttendanceMaster.Normal = 0 AND AttendanceMaster.AOvertime = 0 AND AttendanceMaster.Overtime > 0 AND AttendanceMaster.ADate IN (SELECT OTDate FROM OTDate) AND (tgroup.ExemptOT LIKE '%OTD%' OR tgroup.ExemptOT LIKE '%ALL%') AND AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.Overtime <= AttendanceMaster.group_min*60 AND AttendanceMaster.ADate > " . $txtLockDate;
    updateIData($jconn, $query, true);
    $query = "UPDATE AttendanceMaster, tgroup SET AttendanceMaster.Normal = AttendanceMaster.group_min*60, AttendanceMaster.Overtime = (AttendanceMaster.Overtime - AttendanceMaster.group_min*60) WHERE (AttendanceMaster.Remark = '' OR AttendanceMaster.Remark IS NULL) AND AttendanceMaster.Normal = 0 AND AttendanceMaster.AOvertime = 0 AND AttendanceMaster.Overtime > 0 AND AttendanceMaster.ADate IN (SELECT OTDate FROM OTDate) AND (tgroup.ExemptOT LIKE '%OTD%' OR tgroup.ExemptOT LIKE '%ALL%') AND AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.Overtime > AttendanceMaster.group_min*60 AND AttendanceMaster.ADate > " . $txtLockDate;
    updateIData($kconn, $query, true);
}
$query = "SELECT COUNT(*) FROM tgroup WHERE tgroup.ProxyOT = 'No'";
$result = selectData($conn, $query);
if (0 < $result[0]) {
    $query = "UPDATE AttendanceMaster, tgroup SET AttendanceMaster.AOvertime = 0, AttendanceMaster.Remark = 'No Proxy OT' WHERE AttendanceMaster.Overtime = AttendanceMaster.AOvertime AND AttendanceMaster.group_id = tgroup.id AND tgroup.ProxyOT = 'No' AND AttendanceMaster.Remark <> 'No Proxy OT' AND ADate > " . $txtLockDate;
    updateIData($iconn, $query, true);
}
$query = "SELECT COUNT(*) FROM FlagDayRotation WHERE OT = 'OT' AND e_date > " . $txtLockDate;
$result = selectData($conn, $query);
if (0 < $result[0]) {
    $query = "UPDATE AttendanceMaster, FlagDayRotation SET AttendanceMaster.Overtime = (AttendanceMaster.Normal + AttendanceMaster.Overtime), AttendanceMaster.Normal = 0, AttendanceMaster.Remark = 'Flagged OT Day', FlagDayRotation.RecStat = 1 WHERE AttendanceMaster.EmployeeID = FlagDayRotation.e_id AND AttendanceMaster.ADate = FlagDayRotation.e_date AND AttendanceMaster.ADate > " . $txtLockDate . " AND FlagDayRotation.RecStat = 0 AND FlagDayRotation.OT = 'OT' AND AttendanceMaster.Remark <> 'Flagged OT Day' AND AttendanceMaster.Normal > 0";
    updateIData($jconn, $query, true);
}
$query = "SELECT AttendanceMaster.AttendanceID, AttendanceMaster.Overtime, AttendanceMaster.AOvertime, PreApproveOT.OT, PreApproveOT.A3 FROM AttendanceMaster, PreApproveOT WHERE AttendanceMaster.AOvertime = 0 AND PreApproveOT.A3 = 1 AND AttendanceMaster.EmployeeID = PreApproveOT.e_id AND AttendanceMaster.ADate = PreApproveOT.OTDate";
$result = mysqli_query($conn, $query);
while ($cur = mysqli_fetch_row($result)) {
    if ($lstPreApproveOTValue == "Lower Value") {
        if ($cur[3] * 3600 < $cur[1]) {
            $query = "UPDATE AttendanceMaster SET AOvertime = " . $cur[3] * 3600 . " WHERE AttendanceID = " . $cur[0];
        } else {
            $query = "UPDATE AttendanceMaster SET AOvertime = " . $cur[1] . " WHERE AttendanceID = " . $cur[0];
        }
    } else {
        $query = "UPDATE AttendanceMaster SET AOvertime = " . $cur[3] * 3600 . " WHERE AttendanceID = " . $cur[0];
    }
    updateIData($kconn, $query, true);
}
$query = "SELECT OTDate FROM OTDate WHERE OTDate > " . $txtLockDate . " ORDER BY OTDate";
$result = mysqli_query($conn, $query);
while ($cur = mysqli_fetch_row($result)) {
    $query = "UPDATE AttendanceMaster SET PHF = '1' WHERE ADate = '" . $cur[0] . "' AND PHF = '0'";
    updateIData($iconn, $query, true);
}
$query = "SELECT AttendanceID, ADate, FlagDayRotation.OT FROM AttendanceMaster, FlagDayRotation WHERE AttendanceMaster.ADate = FlagDayRotation.e_date AND AttendanceMaster.EmployeeID = FlagDayRotation.e_id AND (FlagDayRotation.OT = 'OT1' OR FlagDayRotation.OT = 'OT2') AND AttendanceMaster.Normal > 0 AND AttendanceMaster.ADate > " . $txtLockDate;
$result = mysqli_query($conn, $query);
while ($cur = mysqli_fetch_row($result)) {
    if ($cur[2] == "OT1") {
        $query = "UPDATE AttendanceMaster SET OT1 = '" . getDay(displayDate($cur[1])) . "', Overtime = (Normal + Overtime), Normal = 0, AOvertime = 0 WHERE AttendanceID = " . $cur[0];
    } else {
        $query = "UPDATE AttendanceMaster SET OT2 = '" . getDay(displayDate($cur[1])) . "', Overtime = (Normal + Overtime), Normal = 0, AOvertime = 0 WHERE AttendanceID = " . $cur[0];
    }
    updateIData($jconn, $query, true);
}
if ($lstAutoApproveOT == "Yes") {
    $query = "UPDATE AttendanceMaster SET AOvertime = Overtime WHERE AOvertime = 0 AND (AttendanceMaster.Remark = '' OR AttendanceMaster.Remark IS NULL) AND ADate > " . $txtLockDate;
    updateIData($kconn, $query, true);
}
$query = "UPDATE AttendanceMaster, tgroup SET AttendanceMaster.AOvertime = 0, AttendanceMaster.Remark = 'Below Min OT' WHERE AttendanceMaster.AOvertime > 0 AND (AttendanceMaster.Remark = '' OR AttendanceMaster.Remark IS NULL) AND AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.AOvertime < (tgroup.MinOTValue*60) AND AttendanceMaster.ADate > " . $txtLockDate;
updateIData($iconn, $query, true);
if ($lstAutoApproveOT == "Yes") {
    $query = "UPDATE AttendanceMaster, tgroup SET AttendanceMaster.AOvertime = (tgroup.MaxOTValue*60), AttendanceMaster.Remark = 'AutoMaxOT' WHERE (AttendanceMaster.Remark = '' OR AttendanceMaster.Remark IS NULL) AND AttendanceMaster.Overtime = AttendanceMaster.AOvertime AND AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.AOvertime > (tgroup.MaxOTValue*60) AND AttendanceMaster.Normal = AttendanceMaster.group_min*60 AND AttendanceMaster.Day <> AttendanceMaster.OT1 AND AttendanceMaster.Day <> AttendanceMaster.OT2 AND AttendanceMaster.Flag <> 'Purple' AND AttendanceMaster.ADate > " . $txtLockDate;
    updateIData($jconn, $query, true);
}
if ($lstAutoApproveOT == "Yes") {
    $query = "UPDATE AttendanceMaster, tgroup SET AttendanceMaster.AOvertime = (tgroup.MaxOTValueOT1*60), AttendanceMaster.Remark = 'AutoMaxOT' WHERE (AttendanceMaster.Remark = '' OR AttendanceMaster.Remark IS NULL) AND AttendanceMaster.Overtime = AttendanceMaster.AOvertime AND AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.AOvertime > (tgroup.MaxOTValueOT1*60) AND AttendanceMaster.Day = AttendanceMaster.OT1 AND AttendanceMaster.Flag <> 'Purple' AND AttendanceMaster.ADate > " . $txtLockDate;
    updateIData($kconn, $query, true);
}
if ($lstAutoApproveOT == "Yes") {
    $query = "UPDATE AttendanceMaster, tgroup SET AttendanceMaster.AOvertime = (tgroup.MaxOTValueOT2*60), AttendanceMaster.Remark = 'AutoMaxOT' WHERE (AttendanceMaster.Remark = '' OR AttendanceMaster.Remark IS NULL) AND AttendanceMaster.Overtime = AttendanceMaster.AOvertime AND AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.AOvertime > (tgroup.MaxOTValueOT2*60) AND AttendanceMaster.Day = AttendanceMaster.OT2 AND AttendanceMaster.Flag <> 'Purple' AND AttendanceMaster.ADate > " . $txtLockDate;
    updateIData($iconn, $query, true);
}
if ($lstAutoApproveOT == "Yes") {
    $query = "UPDATE AttendanceMaster, tgroup SET AttendanceMaster.AOvertime = (tgroup.MaxOTValueOT2*60), AttendanceMaster.Remark = 'AutoMaxOT' WHERE (AttendanceMaster.Remark = '' OR AttendanceMaster.Remark IS NULL) AND AttendanceMaster.Overtime = AttendanceMaster.AOvertime AND AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.AOvertime > (tgroup.MaxOTValueOT2*60) AND AttendanceMaster.Flag = 'Purple' AND AttendanceMaster.ADate > " . $txtLockDate;
    updateIData($jconn, $query, true);
}
if (getRegister($txtMACAddress, 7) == "-1" || getRegister($txtMACAddress, 7) == "5" || getRegister($txtMACAddress, 7) == "35" || getRegister($txtMACAddress, 7) == "74") {
    if (getRegister($txtMACAddress, 7) == "74" || getRegister($txtMACAddress, 7) == "35") {
        $query = "UPDATE AttendanceMaster SET AOvertime = (AOvertime + 7200) WHERE AOvertime > 0 AND ( Day = OT1 OR Day = OT2 OR Flag = 'Purple' ) AND AOvertime = Overtime  AND AttendanceMaster.ADate > " . $txtLockDate;
        updateIData($kconn, $query, true);
    }
    if (getRegister($txtMACAddress, 7) != "74") {
        $query = "SELECT AttendanceID, AOvertime FROM AttendanceMaster WHERE AOvertime > 0 AND ADate > " . $txtLockDate;
        $result = mysqli_query($conn, $query);
        while ($cur = mysqli_fetch_row($result)) {
            $aot = $cur[1];
            if ($aot % 3600 < 1800 && getRegister($txtMACAddress, 7) != "5") {
                $aot = floor($aot / 60 / 30) / 2 * 3600;
            } else {
                if (1800 <= $aot % 3600) {
                    $aot = ceil($aot / 60 / 60) / 1 * 3600;
                }
            }
            $query = "UPDATE AttendanceMaster SET AOvertime = " . $aot . " WHERE AttendanceID = " . $cur[0];
            updateIData($iconn, $query, true);
        }
    }
} else {
    if ($lstRoundOffAOT == "15" || $lstRoundOffAOT == "30" || $lstRoundOffAOT == "60") {
        $query = "SELECT AttendanceID, AOvertime FROM AttendanceMaster WHERE AOvertime > 0 AND ADate > " . $txtLockDate;
        $result = mysqli_query($conn, $query);
        while ($cur = mysqli_fetch_row($result)) {
            $aot = $cur[1];
            if ($lstRoundOffAOT == "15") {
                $aot = floor($aot / 60 / 15) / 4 * 3600;
            } else {
                if ($lstRoundOffAOT == "30") {
                    $aot = floor($aot / 60 / 30) / 2 * 3600;
                } else {
                    if ($lstRoundOffAOT == "60") {
                        $aot = floor($aot / 60 / 60) / 1 * 3600;
                    }
                }
            }
            $query = "UPDATE AttendanceMaster SET AOvertime = " . $aot . " WHERE AttendanceID = " . $cur[0];
            updateIData($jconn, $query, true);
        }
    }
}
if (getRegister($txtMACAddress, 7) == "36" || getRegister($txtMACAddress, 7) == "6") {
    $query = "UPDATE AttendanceMaster SET AOvertime = (210*60) WHERE AOvertime > (180*60) AND AOvertime < (210*60) AND Day <> OT1 AND Day <> OT2 AND Flag <> 'Purple' AND AttendanceMaster.ADate > " . $txtLockDate;
    if (updateIData($iconn, $query, true)) {
        $query = "UPDATE AttendanceMaster, tuser SET AttendanceMaster.AOvertime = 0 WHERE AttendanceMaster.EmployeeID = tuser.id AND tuser.phone = 'CNS' AND AttendanceMaster.AOvertime > 0 AND AttendanceMaster.Day <> AttendanceMaster.OT1 AND AttendanceMaster.Day <> AttendanceMaster.OT2 AND AttendanceMaster.Flag <> 'Purple' AND AttendanceMaster.ADate > " . $txtLockDate;
        updateIData($kconn, $query, true);
    }
}
if ($lstAutoApproveOT == "Yes") {
    $query = "UPDATE AttendanceMaster, tgroup SET AttendanceMaster.Normal = AttendanceMaster.group_min*60, AttendanceMaster.AOvertime = ((AttendanceMaster.Overtime * tgroup.OT1RF) - AttendanceMaster.group_min*60), AttendanceMaster.Remark = CONCAT(AttendanceMaster.Remark, ' OT1RF') WHERE (AttendanceMaster.Remark NOT LIKE '%OT1RF%' OR AttendanceMaster.Remark IS NULL) AND AttendanceMaster.group_id = tgroup.id AND tgroup.OT1RF > 0 AND AttendanceMaster.Normal = 0 AND AttendanceMaster.Day = AttendanceMaster.OT1 AND (AttendanceMaster.Overtime * tgroup.OT1RF) > AttendanceMaster.group_min*60 AND AttendanceMaster.ADate > " . $txtLockDate;
//    $query = "UPDATE AttendanceMaster, tgroup SET AttendanceMaster.Normal = AttendanceMaster.group_min*60, AttendanceMaster.AOvertime = 1234, AttendanceMaster.Remark = CONCAT(AttendanceMaster.Remark, ' OT1RF') WHERE (AttendanceMaster.Remark NOT LIKE '%OT1RF%' OR AttendanceMaster.Remark IS NULL) AND AttendanceMaster.group_id = tgroup.id AND tgroup.OT1RF > 0 AND AttendanceMaster.Normal = 0 AND AttendanceMaster.Day = AttendanceMaster.OT1 AND (AttendanceMaster.Overtime * tgroup.OT1RF) > AttendanceMaster.group_min*60 AND AttendanceMaster.ADate > " . $txtLockDate;
    updateIData($iconn, $query, true);
    $query = "UPDATE AttendanceMaster, tgroup SET AttendanceMaster.Normal = AttendanceMaster.Overtime, AttendanceMaster.AOvertime = 0, AttendanceMaster.Remark = CONCAT(AttendanceMaster.Remark, ' OT1RF') WHERE (AttendanceMaster.Remark NOT LIKE '%OT1RF%' OR AttendanceMaster.Remark IS NULL) AND AttendanceMaster.group_id = tgroup.id AND tgroup.OT1RF > 0 AND AttendanceMaster.Normal = 0 AND AttendanceMaster.Day = AttendanceMaster.OT1 AND (AttendanceMaster.Overtime * tgroup.OT1RF) <= AttendanceMaster.group_min*60 AND AttendanceMaster.ADate > " . $txtLockDate;
//    $query = "UPDATE AttendanceMaster, tgroup SET AttendanceMaster.Normal = AttendanceMaster.Overtime, AttendanceMaster.AOvertime = 134, AttendanceMaster.Remark = CONCAT(AttendanceMaster.Remark, ' OT1RF') WHERE (AttendanceMaster.Remark NOT LIKE '%OT1RF%' OR AttendanceMaster.Remark IS NULL) AND AttendanceMaster.group_id = tgroup.id AND tgroup.OT1RF > 0 AND AttendanceMaster.Normal = 0 AND AttendanceMaster.Day = AttendanceMaster.OT1 AND (AttendanceMaster.Overtime * tgroup.OT1RF) <= AttendanceMaster.group_min*60 AND AttendanceMaster.ADate > " . $txtLockDate;
    updateIData($jconn, $query, true);
}
$query = "SELECT Violet, Indigo, Blue, Green, Yellow, Orange, Red, Gray, Brown, Purple, Magenta, Teal, Aqua, Safron, Amber, Gold, Vermilion, Silver, Maroon, Pink FROM TLSFlag";
$result = mysqli_query($conn, $query);
while ($cur = mysqli_fetch_row($result)) {
    for ($i = 0; $i < 12; $i++) {
        if ($cur[$i] == "No") {
            $query = "UPDATE AttendanceMaster SET Normal = 0 WHERE group_min*60 = Normal AND Flag = '" . mysqli_fetch_field_direct($result, $i)->name . "' AND ADate > " . $txtLockDate;
            updateIData($kconn, $query, true);
        } else {
            $query = "UPDATE AttendanceMaster SET Normal = group_min*60 WHERE (Normal = 0 OR Normal = 0.01 OR Normal = 1) AND Flag = '" . mysqli_fetch_field_direct($result, $i)->name . "' AND ADate > " . $txtLockDate;
            updateIData($kconn, $query, true);
        }
    }
}
$query = "UPDATE AttendanceMaster, tgroup SET AttendanceMaster.Remark = CONCAT(AttendanceMaster.Remark, ' Exempt Late IN - OT/N'), AttendanceMaster.Overtime = (AttendanceMaster.Overtime + AttendanceMaster.LateIn), AttendanceMaster.Normal = (AttendanceMaster.Normal - AttendanceMaster.LateIn), AttendanceMaster.LateIn_flag = 1 WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.LateIn_flag = 0 AND AttendanceMaster.Overtime > 0 AND AttendanceMaster.group_min*60 = AttendanceMaster.Normal AND tgroup.ExemptLI LIKE '%OT/N%' AND AttendanceMaster.ADate > " . $txtLockDate;
updateIData($iconn, $query, true);
$query = "UPDATE AttendanceMaster SET Normal = Overtime, Overtime = 0, AOvertime = 0 WHERE AttendanceMaster.group_id = 2 AND Flag = 'Proxy' AND Overtime = AOvertime AND Overtime > 0 AND ADate > " . $txtLockDate . " AND ADate IN (SELECT OTDate FROM OTDate) ";
updateIData($jconn, $query, true);
$query = "UPDATE tgroup, DayMaster, AttendanceMaster SET AttendanceMaster.NightFlag = 1 WHERE AttendanceMaster.NightFlag = 0 AND tgroup.id = DayMaster.group_id AND DayMaster.e_id = AttendanceMaster.EmployeeID AND DayMaster.TDate = AttendanceMaster.ADate AND DayMaster.group_id = AttendanceMaster.group_id AND DayMaster.Start > '120000' AND DayMaster.Close < '120000' AND tgroup.ScheduleID = 7 AND AttendanceMaster.ADate > " . $txtLockDate;
updateIData($iconn, $query, true);
if (encryptDecrypt($txtMACAddress) == "00-25-11-8F-2D-7F") {
    $patch_count = 0;
    $query = "SELECT AttendanceID, EmployeeID, ADate, group_min, Overtime, AOvertime FROM AttendanceMaster, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND tuser.phone = 'Y' AND (AttendanceMaster.Day = AttendanceMaster.OT1 OR AttendanceMaster.Day = AttendanceMaster.OT2) AND AOvertime > 0 AND AttendanceMaster.Normal = 0 AND AttendanceMaster.ADate > " . $txtLockDate;
    $result = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
        $sub_query = "SELECT FlagDayRotationID FROM FlagDayRotation WHERE e_id = " . $cur[1] . " AND e_date = " . $cur[2];
        $sub_result = selectData($conn, $sub_query);
        if (!is_numeric($sub_result[0])) {
            if ($cur[4] <= $cur[3] * 60) {
                $query = "UPDATE AttendanceMaster SET AttendanceMaster.Normal = AttendanceMaster.Overtime, Overtime = 0, AOvertime = 0, OT1 = '', OT2 = '' WHERE AttendanceID = " . $cur[0];
            } else {
                $query = "UPDATE AttendanceMaster SET AttendanceMaster.Normal = " . $cur[3] * 60 . ", AttendanceMaster.Overtime = " . ($cur[4] - $cur[3] * 60) . ", AOvertime = Overtime, OT1 = '', OT2 = '' WHERE AttendanceID = " . $cur[0];
            }
            if (updateIData($kconn, $query, true)) {
                $patch_count++;
            }
        }
    }
    $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('Daily Patch: " . $patch_count . "', " . $maxDate . ", '" . getNow() . "')";
    updateIData($iconn, $query, true);
} else {
    $query = "UPDATE AttendanceMaster SET OT1 = 'Saturday' WHERE LENGTH(OT1) = 0 ";
    if (updateIData($jconn, $query, true)) {
        $query = "UPDATE AttendanceMaster SET OT2 = 'Sunday' WHERE LENGTH(OT2) = 0 ";
        updateIData($jconn, $query, true);
    }
}
if (getRegister($txtMACAddress, 7) == "5") {
    $query = "UPDATE AttendanceMaster SET AOvertime = 14400 WHERE Flag = 'Magenta' AND Overtime < 14400 AND AttendanceMaster.ADate > " . $txtLockDate;
    updateIData($kconn, $query, true);
}
if (getRegister($txtMACAddress, 7) == "25") {
    $query = "UPDATE AttendanceMaster SET Normal = 41400 WHERE Flag = 'Orange' AND Normal = 28800 AND AttendanceMaster.ADate > " . $txtLockDate;
    updateIData($iconn, $query, true);
}
if (getRegister($txtMACAddress, 7) == "33") {
    $query = "SELECT tuser.id, tuser.name, tuser.idno, tuser.dept, tuser.company, tuser.remark, tuser.phone FROM tuser";
    $result = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
        $emp_code = "EMP";
        switch ($cur[6]) {
            case "Expat":
                $emp_code .= "01";
            case "Staff":
                $emp_code .= "02";
            case "Unconfirmed Staff":
                $emp_code .= "03";
            case "Casual":
                $emp_code .= "04";
            case "Contract":
                $emp_code .= "05";
        }
        switch ($cur[4]) {
            case "Geepee":
                $emp_code .= "GP";
            case "Alumax":
                $emp_code .= "AL";
            case "Lavleen":
                $emp_code .= "LV";
            case "Chitra":
                $emp_code .= "CH";
            case "Europlast":
                $emp_code .= "EU";
            case "Lambardy":
                $emp_code .= "LM";
            case "Lucky-1":
                $emp_code .= "LK";
            case "Lucky-2":
                $emp_code .= "LK";
            case "Lloyds":
                $emp_code .= "LD";
        }
        $emp_code .= addZero($cur[0], $txtECodeLength);
        $query = "UPDATE tuser SET F10 = '" . $emp_code . "' WHERE id = '" . $cur[0] . "'";
        updateIData($jconn, $query, true);
    }
}
if (getRegister($txtMACAddress, 7) == "25" || getRegister($txtMACAddress, 7) == "86" || getRegister($txtMACAddress, 7) == "44" || getRegister($txtMACAddress, 7) == "43") {
    $query = "SELECT DayMaster.Start, DayMaster.Close, DayMaster.TDate, DayMaster.e_id, tgroup.NightFlag, tgroup.Start, tgroup.Close, tgroup.BreakFrom, tgroup.BreakTo FROM DayMaster, tgroup WHERE DayMaster.group_id = tgroup.id AND tgroup.ScheduleID = 7 AND DayMaster.Start > '030000' AND DayMaster.Start < '120000' AND DayMaster.TDate >= " . $txtLockDate;
    $result = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
        $a = mktime((int)substr($cur[0], 0, 2), (int)substr($cur[0], 2, 2), (int)substr($cur[0], 4, 2), (int)substr($cur[2], 4, 2), (int)substr($cur[2], 6, 2), (int)substr($cur[2], 0, 4));
        $b = mktime((int)substr($cur[5], 0, 2), (int)substr($cur[5], 2, 2), "00", (int)substr($cur[2], 4, 2), (int)substr($cur[2], 6, 2), (int)substr($cur[2], 0, 4));
        $c = $a - $b;
        $d = mktime((int)substr($cur[1], 0, 2), (int)substr($cur[1], 2, 2), (int)substr($cur[1], 4, 2), (int)substr($cur[2], 4, 2), (int)substr($cur[2], 6, 2), (int)substr($cur[2], 0, 4));
        $e = mktime((int)substr($cur[6], 0, 2), (int)substr($cur[6], 2, 2), "00", (int)substr($cur[2], 4, 2), (int)substr($cur[2], 6, 2), (int)substr($cur[2], 0, 4));
        $f = $d - $e;
        $sub_query = "SELECT AttendanceMaster.Normal, AttendanceMaster.Overtime, AttendanceMaster.AOvertime, AttendanceMaster.Day, AttendanceMaster.OT1, AttendanceMaster.OT2, AttendanceMaster.Flag FROM AttendanceMaster WHERE EmployeeID = '" . $cur[3] . "' AND AttendanceMaster.ADate = '" . $cur[2] . "' AND EarlyIn_flag <> 2 ";
        $sub_result = selectData($conn, $sub_query);
        $aot = $sub_result[2];
        $oaot = $aot;
        $alter = false;
        if ($aot != "" && 0 < $aot) {
            if ($c < 0) {
                $c = $c * (0 - 1);
                if ($c < $aot) {
                    $aot = $aot - $c;
                    $alter = true;
                } else {
                    $aot = 0;
                    $alter = true;
                }
            }
            if (0 < $f) {
                if ($f < $aot) {
                    $aot = $aot - $f;
                    $alter = true;
                } else {
                    $aot = 0;
                    $alter = true;
                }
            }
            if ($sub_result[3] == $sub_result[4] || $sub_result[3] == $sub_result[5] || $sub_result[6] == "Purple") {
                if (41400 < $aot) {
                    $aot = 41400;
                    $alter = true;
                }
            } else {
                if (12600 < $aot) {
                    $aot = 12600;
                    $alter = true;
                }
            }
            if ($alter) {
                $sub_query = "UPDATE AttendanceMaster SET AOvertime = " . $aot . ", Remark = CONCAT('" . round($oaot / 3600, 2) . ", ', IFNULL(Remark, ''), ', AUTO-CORRECT-IN-OUT'), EarlyIn_flag = 2 WHERE EmployeeID = '" . $cur[3] . "' AND AttendanceMaster.ADate = '" . $cur[2] . "' ";
                updateIData($kconn, $sub_query, true);
            }
        }
    }
    $query = "SELECT DayMaster.Start, DayMaster.Close, DayMaster.TDate, DayMaster.e_id, tgroup.NightFlag, tgroup.Start, tgroup.Close, tgroup.BreakFrom, tgroup.BreakTo FROM DayMaster, tgroup WHERE DayMaster.group_id = tgroup.id AND tgroup.ScheduleID = 7 AND DayMaster.Start > '150000' && DayMaster.Start < '235959' AND DayMaster.TDate >= " . $txtLockDate;
    $result = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
        $a = mktime((int)substr($cur[0], 0, 2), (int)substr($cur[0], 2, 2), (int)substr($cur[0], 4, 2), (int)substr($cur[2], 4, 2), (int)substr($cur[2], 6, 2), (int)substr($cur[2], 0, 4));
        $b = mktime((int)substr($cur[7], 0, 2), (int)substr($cur[7], 2, 2), "00", (int)substr($cur[2], 4, 2), (int)substr($cur[2], 6, 2), (int)substr($cur[2], 0, 4));
        $c = $a - $b;
        $d = mktime((int)substr($cur[1], 0, 2), (int)substr($cur[1], 2, 2), (int)substr($cur[1], 4, 2), (int)substr($cur[2], 4, 2), (int)substr($cur[2], 6, 2), (int)substr($cur[2], 0, 4));
        $e = mktime((int)substr($cur[8], 0, 2), (int)substr($cur[8], 2, 2), "00", (int)substr($cur[2], 4, 2), (int)substr($cur[2], 6, 2), (int)substr($cur[2], 0, 4));
        $f = $d - $e;
        $sub_query = "SELECT AttendanceMaster.Normal, AttendanceMaster.Overtime, AttendanceMaster.AOvertime, AttendanceMaster.Day, AttendanceMaster.OT1, AttendanceMaster.OT2, AttendanceMaster.Flag FROM AttendanceMaster WHERE EmployeeID = '" . $cur[3] . "' AND AttendanceMaster.ADate = '" . $cur[2] . "' AND EarlyIn_flag <> 2 ";
        $sub_result = selectData($conn, $sub_query);
        $aot = $sub_result[2];
        $oaot = $aot;
        $alter = false;
        if ($aot != "" && 0 < $aot) {
            if ($c < 0) {
                $c = $c * (0 - 1);
                if ($c < $aot) {
                    $aot = $aot - $c;
                    $alter = true;
                } else {
                    $aot = 0;
                    $alter = true;
                }
            }
            if (0 < $f) {
                if ($f < $aot) {
                    $aot = $aot - $f;
                    $alter = true;
                } else {
                    $aot = 0;
                    $alter = true;
                }
            }
            if ($sub_result[3] == $sub_result[4] || $sub_result[3] == $sub_result[5] || $sub_result[6] == "Purple") {
                if (45000 < $aot) {
                    $aot = 45000;
                    $alter = true;
                }
            } else {
                if (16200 < $aot) {
                    $aot = 16200;
                    $alter = true;
                }
            }
            if ($alter) {
                $sub_query = "UPDATE AttendanceMaster SET AOvertime = " . $aot . ", Remark = CONCAT('" . round($oaot / 3600, 2) . "', IFNULL(Remark, ''), ', AUTO-CORRECT-IN-OUT'), EarlyIn_flag = 2 WHERE EmployeeID = '" . $cur[3] . "' AND AttendanceMaster.ADate = '" . $cur[2] . "' ";
                updateIData($kconn, $sub_query, true);
            }
        }
    }
}
if (getRegister($txtMACAddress, 7) == "85") {
    $query = "SELECT EmployeeID, ADate, AttendanceID FROM AttendanceMaster WHERE (LateIN > 900 OR EarlyOut > 900) AND ADate >= " . $txtLockDate;
    $result = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
        $query = "DELETE FROM DayMaster WHERE TDate = '" . $cur[1] . "' AND e_id = '" . $cur[0] . "'";
        if (updateIData($iconn, $query, true)) {
            $query = "DELETE FROM AttendanceMaster WHERE AttendanceID = '" . $cur[3] . "' ";
            updateIData($jconn, $query, true);
        }
    }
}
if (getRegister($txtMACAddress, 7) == "145") {
    $query = "UPDATE AttendanceMaster SET AOvertime = (AOvertime - LateIn) WHERE AOvertime >= 10800 AND Day <> OT1 AND Day <> OT2 AND Flag <> 'Purple' AND ADate >= " . $txtLockDate;
    updateIData($iconn, $query, true);
    $query = "UPDATE AttendanceMaster SET AOvertime = (AOvertime - LateIn) WHERE AOvertime >= 39600 AND (Day = OT1 OR Day = OT2 OR Flag = 'Purple') AND ADate >= " . $txtLockDate;
    updateIData($iconn, $query, true);
}
if (getRegister($txtMACAddress, 7) == "113") {
    $query = "UPDATE AttendanceMaster SET AOvertime = (AOvertime - LateOut), LateInColumn = 1 WHERE group_id = 12 AND LateInColumn = 0 AND LateOut > 0 AND ADate >= " . $txtLockDate;
    updateIData($iconn, $query, true);
}
if (getRegister($txtMACAddress, 7) == "165" || getRegister($txtMACAddress, 7) == "90" || getRegister($txtMACAddress, 7) == "177" || getRegister($txtMACAddress, 7) == "130") {
    $uconn = mysqli_connect("localhost", "root", "namaste", "unis");
    if ($uconn != "") {
        ada($conn, $iconn, $uconn);
    }
}
if (getRegister($txtMACAddress, 7) == "6" || getRegister($txtMACAddress, 7) == "209") {
//    echo $query = "SELECT FlagDayRotation.e_id, FlagDayRotation.g_id, FlagDayRotation.Flag, FlagDayRotation.e_date, FlagDayRotation.Rotate, FlagDayRotation.Remark, FlagDayRotation.OT, tuser.group_id, FlagDayRotation.OTH FROM FlagDayRotation, tuser WHERE FlagDayRotation.e_id = tuser.id AND ((FlagDayRotation.e_date <= " . $maxDate . " AND FlagDayRotation.Flag NOT IN (SELECT FlagLink FROM FlagTitle WHERE FlagLink IS NOT NULL)) OR (FlagDayRotation.e_date < " . getLastDay($maxDate, 1) . " AND FlagDayRotation.Flag IN (SELECT FlagLink FROM FlagTitle WHERE FlagLink IS NOT NULL))) AND FlagDayRotation.RecStat = 0 AND FlagDayRotation.e_date > " . $txtLockDate . " ORDER BY FlagDayRotation.e_id, FlagDayRotation.e_date";
//    echo $query = "SELECT a.*,t.start,t.close,t.name,t.EmpClose,d.Entry,d.Exit from attendancemaster a INNER JOIN tgroup t ON t.id=a.group_id INNER JOIN d.e_id=a.EmpId where a.ADate > '" . $txtLockDate . "'"; // AND a.EmpID='400988'
    $query = "SELECT a.*,t.start,t.close,t.name,t.EmpClose,d.Entry,d.Exit from attendancemaster a RIGHT JOIN tgroup t ON t.id=a.group_id RIGHT JOIN DayMaster d ON a.EmpID=d.e_id AND d.TDate=a.ADate where a.ADate > '" . $txtLockDate . "'";
//        echo "<br>";
    $Result = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_array($Result)) {
//        echo "<pre>";
//        print_R($cur);
        if (round($cur[14] / 3600, 2) >= 8 || $cur[18] == 'Saturday') {
            if ($cur['EmpClose'] != NULL || $cur['EmpClose'] != '0') {
                // $cur[6] = 'Closetime'
                // $cur[19] = 'Overtime'
                // $cur[17] = 'Normal Hours'
                if (strtotime($cur['Exit']) > strtotime($cur['EmpClose'] . '00')) {
//                                        echo "<pre>";
//                                        print_R($cur);
//                                        echo strtotime($otcuttRow['EmpClose'].'00');
//                                    echo "Hey".$otCutoff =  strtotime($otcuttRow['EmpClose'].'00') - strtotime(displayVirdiTime($cur[3]))."<br>";
                    $otCutoff = strtotime(displayVirdiTime($cur['Exit'])) - strtotime(displayVirdiTime($otcuttRow['EmpClose'] . '00'));
//                    $aotcutofftime = $cur[17] - $otCutoff;
                    $aotcutofftimecal = round($cur[17] / 3600, 2) - strtotime(round($otCutoff / 3600, 2));
//                    $aotcutofftime = round($cur[19] / 3600, 2) - round($otCutoff / 3600, 2);
//                                    echo "Hey"."<br>";
                    $aotcutofftime = $aotcutofftimecal * 3600;
                } else {
//                    $aotcutofftime = round($cur[19] / 3600, 2);
//                    $aotcutofftime = round($cur[16] / 3600, 2);
                    $aotcutofftime = $cur[17];
//                                    echo "Here"."<br>";
                }
            } else {
//                $aotcutofftime = round($cur[19] / 3600, 2);
//                $aotcutofftime = round($cur[16] / 3600, 2);
                $aotcutofftime = $cur[17];
            }
        }
//        echo $cur[17];
//        echo  "<br>";
        $query = "UPDATE AttendanceMaster SET AOvertime = '" . $aotcutofftime . "' WHERE EmpID = '$cur[2]' AND group_id='$cur[3]' AND ADate ='$cur[5]' AND ADate > '" . $txtLockDate . "'";
        updateIData($iconn, $query, true);
//        echo "<br>";
    }
//    $query = "UPDATE tenter SET tenter.e_mode = '2' WHERE tenter.e_mode = '3' AND tenter.p_flag = 0 AND tenter.e_date > '" . $txtLockDate . "' ";
//    updateIData($iconn, $query, true);
    $otcuttQuery = "SELECT id,name,EmpClose from tgroup where name='$cur[0]'";
    $otcuttResult = mysqli_query($conn, $otcuttQuery);
    $otcuttRow = mysqli_fetch_assoc($otcuttResult);
}
//mysqli_close($conn);
//mysqli_close($iconn);
//mysqli_close($jconn);
//mysqli_close($kconn);
//mysqli_close($uconn);
displayToday();
getNow();
print "\n\nScript Ended: " . displayToday() . ", " . getNow() . " HRS";
flush();
?>