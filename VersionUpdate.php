<?php
ob_start("ob_gzhandler");
error_reporting(E_ALL);
date_default_timezone_set("Africa/Algiers");
set_time_limit(0);
ini_set('display_errors', 1);
include "Functions.php";
mysqli_report(MYSQLI_REPORT_OFF);
//mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = openConnection();
$iconn = openIConnection();

if(!$conn){
    echo "Not Connected";exit;
}
$query = "SELECT EX2, MACAddress FROM OtherSettingMaster";
$result = selectData($conn, $query);
$mac_in_version = $result[1];
$version = $result[0];

if (!isset($result[0]) || $result[0] == "") {
    V1264935($conn);
} else { 
    $version = $result[0];
    if ($version == "V.1.0.4.1") {
        V1042($conn, $iconn);
        setTCount($conn);
    } elseif ($version == "V.1.0.4.2") { 
        V1005($conn, $iconn);
        setTCount($conn);
    } elseif ($version == "V.1.0.0.5") { 
        V1006($conn, $iconn);
        setTCount($conn);
    } elseif ($version == "V.1.0.0.6") { 
        echo "Version Updated Successfully";
        return 1;
    } elseif (preg_match('/^V\.(35|36|37|38|39)\./', $version)) {
        V1042($conn, $iconn);
        setTCount($conn);
    } else {
        setTCount($conn);
    }
}

function safeUpdate($conn, $query, $commit = false) {
    global $db_type;

    echo "<br><b>Running query:</b> $query<br>";

    try {
        if ($db_type == "3") { // MySQLi

            // Detect ALTER TABLE with multiple ADDs
            if (preg_match('/^ALTER TABLE\s+`?(\w+)`?\s+(.+)$/i', $query, $matches)) {
                $table = $matches[1];
                $rest = $matches[2];

                // Match individual ADD clauses
                $add_clauses = preg_split('/,\s*ADD\s+/i', $rest);
                if (count($add_clauses) > 1) {
                    foreach ($add_clauses as $clause) {
                        $sub_query = "ALTER TABLE `$table` ADD " . trim($clause);
                        echo "<br><b>Running sub-query:</b> $sub_query<br>";
                        $result = @mysqli_query($conn, $sub_query);

                        if ($result) {
                            echo "<b>Status:</b> Success<br>";
                        } else {
                            $error = mysqli_error($conn);
                            if (strpos($error, 'Duplicate column name') !== false) {
                                echo "<b>Status:</b> Column already exists, skipping<br>";
                                continue; // skip this column and move on
                            }
                            echo "<b>Status:</b> Failed - $error<br>";
                        }
                    }
                    if ($commit) {
                        mysqli_commit($conn);
                        echo "<b>Status:</b> Changes committed<br>";
                    }
                    return true;
                }
            }

            // For single statements including ALTER TABLE with one ADD column
            $result = @mysqli_query($conn, $query);
            if ($result) {
                echo "<b>Status:</b> Success<br>";
                if ($commit) {
                    mysqli_commit($conn);
                    echo "<b>Status:</b> Changes committed<br>";
                }
            } else {
                $error = mysqli_error($conn);
                if (strpos($error, 'Duplicate column name') !== false) {
                    echo "<b>Status:</b> Column already exists, skipping<br>";
                } else {
                    echo "<b>Status:</b> Failed - $error<br>";
                }
            }

            return true;
        } else {
            // Fallback for other DB types
            try {
                updateData($conn, $query, $commit);
            } catch (Exception $ex) {
                echo "<b>Status:</b> Failed - " . $ex->getMessage() . "<br>";
            }
            return true;
        }
    } catch (Exception $e) {
        echo "<b>Exception Caught:</b> " . $e->getMessage() . "<br>";
        return true;
    }
}

function safeUpdateIData($iconn, $query, $commit) {
    echo "<br><b>Running query:</b> $query<br>";

    $query_trimmed = ltrim($query);
    $is_select = stripos($query_trimmed, 'SELECT') === 0;

    $result = mysqli_query($iconn, $query);

    if (!$result) {
        echo "<b>Status:</b> Query Failed - " . mysqli_error($iconn) . "<br>";
        return false;
    }

    if ($is_select) {
        echo "<b>Status:</b> SELECT Success - Rows returned: " . mysqli_num_rows($result) . "<br>";
        return $result;
    } else {
        if ($commit) {
            mysqli_commit($iconn);
        }
        echo "<b>Status:</b> Non-SELECT Query Success<br>";
        return true;
    }
}


function V1264935($conn){
	echo "Starting script...<br>";
	$query = "UPDATE Access.tgroup SET tgroup.FlexiBreak = '0', ShiftTypeID = '0', ScheduleID = '0', WorkMin = '0' WHERE tgroup.id = '0'";
	safeUpdate($conn, $query, true);
	$query = "UPDATE Access.tgroup SET tgroup.FlexiBreak = '0', ShiftTypeID = '0', ScheduleID = '0', WorkMin = '0' WHERE tgroup.id = '1'";
	safeUpdate($conn, $query, true);
	$query = "UPDATE Access.tgroup SET tgroup.NightFlag = '1' WHERE tgroup.NightFlag = '-1'";
	safeUpdate($conn, $query, true);

	$query = "UPDATE Access.tuser SET padmin = '0' WHERE padmin IS NULL";
	safeUpdate($conn, $query, true);
	$query = "UPDATE Access.tuser SET group_id = '0' WHERE group_id IS NULL";
	safeUpdate($conn, $query, true);
	$query = "UPDATE Access.tuser SET antipass_state = '0' WHERE antipass_state IS NULL";
	safeUpdate($conn, $query, true);
	V1265543($conn);
}

function V1265543($conn) {
    $query = "UPDATE access.tenter SET e_group = 0 WHERE e_group IS NULL";
    safeUpdate($conn, $query, true);
    V1265545($conn);
}

function V1265545($conn) { 
    $query = "SELECT attendancemaster.AttendanceID, attendancemaster.EmployeeID, attendancemaster.ADate, attendancemaster.Flag FROM access.attendancemaster WHERE  attendancemaster.Flag = 'Proxy'";
    $result = safeUpdateIData($conn, $query, true);
//    $result = mysqli_query($conn, $query);
    if(mysqli_num_rows($result) > 0){
        while ($cur = mysqli_fetch_row($result)) {
            $query = "SELECT attendancemaster.Flag FROM access.attendancemaster WHERE attendancemaster.EmployeeID = " . (int)$cur[1] . " AND attendancemaster.ADate = '" . $cur[2] . "' AND attendancemaster.Flag NOT LIKE 'Proxy'";
            $sub_result = selectData($conn, $query);
            if (0 < count($sub_result)) {
                $this_flag = $sub_result[0];
                if ($this_flag == "Violet" || $this_flag == "Indigo" || $this_flag == "Blue" || $this_flag == "Green" || $this_flag == "Yellow" || $this_flag == "Orange" || $this_flag == "Red" || $this_flag == "Purple" || $this_flag == "Brown" || $this_flag == "Gray" || $this_flag == "Black") {
                    $this_query = "DELETE FROM attendancemaster WHERE AttendanceID = " . $cur[0];
                    safeUpdate($conn, $this_query, false);
                    $this_query = "DELETE FROM daymaster WHERE e_id = " . $cur[1] . " AND TDate = " . $cur[2] . " AND Flag = 'Proxy'";
                    safeUpdate($conn, $this_query, true);
                }
            }
        }
    }
    $query = "SELECT attendancemaster.AttendanceID, attendancemaster.EmployeeID, attendancemaster.ADate, attendancemaster.Flag FROM access.attendancemaster, access.flagdayrotation WHERE attendancemaster.EmployeeID = flagdayrotation.e_id AND attendancemaster.ADate = flagdayrotation.e_date AND attendancemaster.Flag = 'Proxy' AND flagdayrotation.RecStat = 0 AND attendancemaster.ADate > " . insertToday();
    $result = safeUpdateIData($conn, $query, true);
//    $result = mysqli_query($conn, $query);
    if(mysqli_num_rows($result) > 0){
        while ($cur = mysqli_fetch_row($result)) {
            $this_query = "DELETE FROM access.attendancemaster WHERE AttendanceID = " . $cur[0];
            safeUpdate($conn, $this_query, true);
        }
    }
    $query = "UPDATE tgroup SET `MinWorkForBreak` = '0' WHERE MinWorkForBreak IS NULL";
    safeUpdate($conn, $query, true);
    V1265546($conn);
}

function V1265546($conn) { 
    $query = "SELECT FlagDayRotationID FROM access.FlagDayRotation WHERE FlagDayRotationID NOT IN (SELECT FlagDayRotation.FlagDayRotationID FROM access.AttendanceMaster, access.FlagDayRotation WHERE AttendanceMaster.EmployeeID = FlagDayRotation.e_id AND AttendanceMaster.ADate = FlagDayRotation.e_date) AND FlagDayRotation.e_date < " . insertToday() . " ORDER BY e_id";
    $result = safeUpdateIData($conn, $query, true);
//    $result = mysqli_query($conn, $query);
    if(mysqli_num_rows($result) > 0){
        while ($cur = mysqli_fetch_row($result)) {
            $query = "UPDATE access.FlagDayRotation SET RecStat = 0 WHERE FlagDayRotationID = " . $cur[0];
            safeUpdate($conn, $query, true);
        }
    }
    $query = "SELECT DayMasterID FROM access.DayMaster WHERE DayMasterID NOT IN (SELECT DayMaster.DayMasterID FROM access.DayMaster, access.AttendanceMaster WHERE DayMaster.Tdate = AttendanceMaster.ADate AND DayMaster.e_id = AttendanceMaster.EmployeeID)";
    $result = safeUpdateIData($conn, $query, true);
//    $result = mysqli_query($conn, $query);
    if(mysqli_num_rows($result) > 0){
        while ($cur = mysqli_fetch_row($result)) {
            $query = "DELETE FROM access.DayMaster WHERE DayMasterID = " . $cur[0];
            safeUpdate($conn, $query, true);
        }
    }
    V1265547($conn);
}

function V1265547($conn) { 
    $data0 = array();
    $data1 = array();
    $data2 = array();
    $query = "SELECT AttendanceMaster.AttendanceID, AttendanceMaster.EmployeeID, AttendanceMaster.ADate FROM access.AttendanceMaster ORDER BY AttendanceMaster.EmployeeID, AttendanceMaster.ADate";
    $result = safeUpdateIData($conn, $query, true);
//    $result = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
        if ($data1 == $cur[1] && $data2 == $cur[2]) {
            $query = "DELETE FROM Access.AttendanceMaster WHERE AttendanceID = " . $cur[0];
            safeUpdate($conn, $query, true);
        } else {
            $data0 = $cur[0];
            $data1 = $cur[1];
            $data2 = $cur[2];
        }
    }
    $data0 = array();
    $data1 = array();
    $data2 = array();
    $query = "SELECT DayMaster.DayMasterID, DayMaster.e_id, DayMaster.TDate FROM access.DayMaster ORDER BY DayMaster.e_id, DayMaster.TDate";
    $result = safeUpdateIData($conn, $query, true);
//    $result = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
        if ($data1 == $cur[1] && $data2 == $cur[2]) {
            $query = "DELETE FROM Access.DayMaster WHERE DayMasterID = " . $cur[0];
            safeUpdate($conn, $query, true);
        } else {
            $data0 = $cur[0];
            $data1 = $cur[1];
            $data2 = $cur[2];
        }
    }
    
    V1265548($conn);
}

function V1265548($conn) {
    $query = "DELETE FROM Access.AttendanceMaster WHERE Flag = 'Delete'";
    safeUpdate($conn, $query, true);
    $query = "DELETE FROM Access.DayMaster WHERE Flag = 'Delete'";
    safeUpdate($conn, $query, true);
    $query = "DELETE FROM Access.WeekMaster WHERE Flag = 'Delete'";
    safeUpdate($conn, $query, true);
	V1265852($conn);
}

function V1265852($conn) {
    $query = "INSERT INTO access.ModuleMaster (Name) VALUES ('Delete Processed Log'), ('Employees'), ('OT Days/Date'), ('Projects')";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.ModuleMaster SET Name = 'Global Settings' WHERE Name = 'Other Settings'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.ModuleMaster SET Name = 'Approve/Pre Approve Overtime' WHERE Name = 'Approve Overtime'";
    safeUpdate($conn, $query, true);
    V1265854($conn);
}

function V1265854($conn) {
    $query = "UPDATE access.DayMaster SET p_flag = 1";
    safeUpdate($conn, $query, true);
    $query = "SELECT e_id, TDate, DayMasterID FROM access.DayMaster ORDER BY DayMasterID";
//    $result = safeUpdateIData($iconn, $query, true);
    $result = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
        $qquery = "SELECT EmployeeID FROM access.AttendanceMaster WHERE EmployeeID = " . $cur[0] . " AND ADate = " . $cur[1];
        $result1 = selectData($conn, $qquery);
        if (0 >= $result1[0]) {
            $qqquery = "UPDATE access.DayMaster SET p_flag = 0 WHERE DayMasterID = " . $cur[2];
            safeUpdate($conn, $qqquery, true);
        }
    }
    $query = "UPDATE access.AttendanceMaster SET p_flag = 1";
    safeUpdate($conn, $query, true);
    V1266059($conn);
}

function V1266059($conn) {
    $query = "UPDATE access.tenter SET e_etc = 'P' WHERE e_etc = '9'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.tenter SET e_etc = '0' WHERE e_time NOT LIKE '%000' AND e_etc = 'P'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.AttendanceMaster SET Flag = 'Black' WHERE (EarlyIn > 0 OR LateIn > 0) AND Flag = 'Proxy'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.DayMaster SET Flag = 'Black' WHERE Start NOT LIKE '%000' AND Close NOT LIKE '%000' AND Flag = 'Proxy'";
    safeUpdate($conn, $query, true);
    
    $query = "UPDATE access.tuser SET DeptClocking = '' WHERE DeptClocking IS NULL";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.tuser SET ExitClocking = '' WHERE ExitClocking IS NULL";
    safeUpdate($conn, $query, true);
    
    $query = "UPDATE access.tuser SET OT1 = '', OT2 = ''";
    safeUpdate($conn, $query, true);
    
    V1576060($conn);
}

function V1576060($conn) {
    $query = "INSERT IGNORE INTO access.FlagTitle (Flag, Title) VALUES ('Violet', ''), ('Indigo', ''), ('Blue', ''), ('Green', ''), ('Yellow', ''), ('Orange', ''), ('Red', ''), ('Gray', ''), ('Brown', ''), ('Purple', '')";
    safeUpdate($conn, $query, true);

    $query = "SELECT RosterColumns FROM access.OtherSettingMaster";
    $result = selectData($conn, $query);
    $frt = $result[0];
    $frt = nl2br($frt);
    $v_frt = explode("<br />", $frt);
    $frt = "";
    for ($i = 0; $i < count($v_frt); $i++) {
        if (stripos($v_frt[$i], "Violet") !== false) {
            $query = "UPDATE access.FlagTitle SET Title = '" . $v_frt[$i] . "' WHERE Flag = 'Violet'";
            safeUpdate($conn, $query, true);
        } else {
            if (stripos($v_frt[$i], "Indigo") !== false) {
                $query = "UPDATE access.FlagTitle SET Title = '" . $v_frt[$i] . "' WHERE Flag = 'Indigo'";
                safeUpdate($conn, $query, true);
            } else {
                if (stripos($v_frt[$i], "Blue") !== false) {
                    $query = "UPDATE access.FlagTitle SET Title = '" . $v_frt[$i] . "' WHERE Flag = 'Blue'";
                    safeUpdate($conn, $query, true);
                } else {
                    if (stripos($v_frt[$i], "Green") !== false) {
                        $query = "UPDATE access.FlagTitle SET Title = '" . $v_frt[$i] . "' WHERE Flag = 'Green'";
                        safeUpdate($conn, $query, true);
                    } else {
                        if (stripos($v_frt[$i], "Yellow") !== false) {
                            $query = "UPDATE access.FlagTitle SET Title = '" . $v_frt[$i] . "' WHERE Flag = 'Yellow'";
                            safeUpdate($conn, $query, true);
                        } else {
                            if (stripos($v_frt[$i], "Orange") !== false) {
                                $query = "UPDATE access.FlagTitle SET Title = '" . $v_frt[$i] . "' WHERE Flag = 'Orange'";
                                safeUpdate($conn, $query, true);
                            } else {
                                if (stripos($v_frt[$i], "Red") !== false) {
                                    $query = "UPDATE access.FlagTitle SET Title = '" . $v_frt[$i] . "' WHERE Flag = 'Red'";
                                    safeUpdate($conn, $query, true);
                                } else {
                                    if (stripos($v_frt[$i], "Gray") !== false) {
                                        $query = "UPDATE access.FlagTitle SET Title = '" . $v_frt[$i] . "' WHERE Flag = 'Gray'";
                                        safeUpdate($conn, $query, true);
                                    } else {
                                        if (stripos($v_frt[$i], "Brown") !== false) {
                                            $query = "UPDATE access.FlagTitle SET Title = '" . $v_frt[$i] . "' WHERE Flag = 'Brown'";
                                            safeUpdate($conn, $query, true);
                                        } else {
                                            if (stripos($v_frt[$i], "Purple") !== false) {
                                                $query = "UPDATE access.FlagTitle SET Title = '" . $v_frt[$i] . "' WHERE Flag = 'Purple'";
                                                safeUpdate($conn, $query, true);
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
   
    V1576061($conn);
}

function V1576061($conn) {
    $query = "SELECT AttendanceID, ADate FROM access.AttendanceMaster WHERE Day = 'Off'";
    $result = safeUpdateIData($conn, $query, true);
//    $result = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
        $this_date = strtotime(substr($cur[1], 6, 2) . "-" . substr($cur[1], 4, 2) . "-" . substr($cur[1], 0, 4));
        $this_array = getDate($this_date);
        $this_day = $this_array["weekday"];
        $query = "UPDATE access.AttendanceMaster SET Day = '" . $this_day . "' WHERE AttendanceID = " . $cur[0];
        safeUpdate($conn, $query, true);
    }
    V1776063($conn);
}

function V1776063($conn) {
    $query = "UPDATE access.tuser SET company = TRIM(company), dept = TRIM(dept)";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.OtherSettingMaster SET RosterColumns = 'chkDeptchkDivchkShiftchkStartchkClosechkLateInchkEarlyOutchkNormalchkAppOTchkFlag'";
    safeUpdate($conn, $query, true);
    V1886165($conn);
}

function V1886165($conn) {
    $query = "INSERT INTO access.ScheduleMaster ( Name ) VALUES ('Flexi Start-End, Multi In-Out (>1)')";
    safeUpdate($conn, $query, true);
    V18106569($conn);
}

function V18106569($conn) {
    $query = "SELECT DayMasterID FROM access.DayMaster WHERE DayMasterID NOT IN (SELECT DayMaster.DayMasterID FROM access.DayMaster, access.AttendanceMaster WHERE DayMaster.Tdate = AttendanceMaster.ADate AND DayMaster.e_id = AttendanceMaster.EmployeeID)";
    $result = safeUpdateIData($conn, $query, true);
//    $result = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
        $query = "UPDATE access.DayMaster SET p_flag = '0' WHERE DayMasterID = " . $cur[0];
        safeUpdate($conn, $query, true);
    }
    $query = "UPDATE access.tgroup SET RotateFlag = 1 WHERE id IN (SELECT id FROM ShiftChangeMaster WHERE AE = 1)";
    safeUpdate($conn, $query, true);
    
    $query = "UPDATE access.AttendanceMaster, tgroup SET AttendanceMaster.NightFlag = tgroup.NightFlag, AttendanceMaster.RotateFlag = tgroup.RotateFlag WHERE AttendanceMaster.group_id = tgroup.id";
    safeUpdate($conn, $query, true);
    
	V18116671($conn);
}

function V18116671($conn) {
    $query = "SELECT EX4, PLFlag, NightShiftMaxOutTime FROM access.OtherSettingMaster";
    $o_result = selectData($conn, $query);
    if ($o_result[0] == 1) {
        $query = "SELECT OTDate FROM access.OTDate WHERE OTDate < " . insertToday();
        $result = safeUpdateIData($iconn, $query, true);
        while ($cur = mysqli_fetch_row($result)) {
            $query = "SELECT tuser.id, tuser.group_id, tuser.dept FROM access.tuser WHERE tuser.id NOT IN (SELECT DISTINCT(tenter.e_id) FROM access.tenter, access.tgate, access.tgroup WHERE tenter.g_id = tgate.id AND tenter.e_group = tgroup.id AND tgate.exit = 0 AND ((tenter.e_time > '" . $o_result[2] . "00' AND tgroup.NightFlag = 1) OR (tenter.e_time > '000000' AND tgroup.NightFlag = 0)) AND tenter.e_date = '" . $cur[0] . "')";
            $this_result = safeUpdateIData($iconn, $query, true);
            while ($this_cur = mysqli_fetch_row($this_result)) {
                $sub_query = "SELECT g_id FROM access.DeptGate WHERE dept = '" . $this_cur[2] . "'";
                $sub_result = selectData($conn, $sub_query);
            }
        }
    }
    
    V18126977($conn);
}

function V18126977($conn) {
    $query = "SELECT id FROM access.tuser WHERE id > 0";
//    $result = safeUpdateIData($iconn, $query, true);
    $result = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
        $query = "INSERT INTO access.EmployeeFlag (EmployeeID) VALUES (" . $cur[0] . ")";
        safeUpdate($conn, $query, true);
    }
    
    V18126978($conn);
}

function V18126978($conn) {
    $query = "UPDATE access.tuser SET OT1 = 'Saturday' WHERE OT1 IS NULL";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.tuser SET OT1 = 'Saturday' WHERE OT1 = ''";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.tuser SET OT2 = 'Sunday' WHERE OT2 IS NULL";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.tuser SET OT2 = 'Sunday' WHERE OT2 = ''";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.AttendanceMaster SET OT1 = 'Saturday' WHERE OT1 IS NULL";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.AttendanceMaster SET OT1 = 'Saturday' WHERE OT1 = ''";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.AttendanceMaster SET OT2 = 'Sunday' WHERE OT2 IS NULL";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.AttendanceMaster SET OT2 = 'Sunday' WHERE OT2 = ''";
    safeUpdate($conn, $query, true);
	$query = "UPDATE access.PayrollMap SET Overwrite = 'No Synchronization' WHERE PayrollMap = ''";
    safeUpdate($conn, $query, true);
	$query = "UPDATE access.tuser SET OTRotateDate = '99999999' WHERE OTRotate = 'No'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.tuser SET OTRotateDate = '" . insertToday() . "' WHERE OTRotate = 'Yes'";
    safeUpdate($conn, $query, true);
	$query = "UPDATE access.UserMaster SET RDSHeaderBreak = '25'";
    safeUpdate($conn, $query, true);
    V19137197($conn);
}

function V19137197($conn) {
    global $result;
    if (encryptDecrypt($result[1]) == "00-14-2A-00-A0-39") {
        $query = "UPDATE access.tgroup SET ScheduleID = 2 WHERE id > 1";
        safeUpdate($conn, $query, true);
    } else {
        if (encryptDecrypt($result[1]) == "00-22-19-A4-47-1E") {
            $query = "UPDATE access.AttendanceMaster SET Normal = 0, Overtime = 0, AOvertime = 0 WHERE Normal = 28800 AND Overtime = 0 AND (Flag = 'Red' OR Flag = 'Orange' OR Flag = 'Indigo')";
            safeUpdate($conn, $query, true);
        }
    }
	$query = "CREATE TABLE Access.Event (\tID BIGINT NOT NULL ,\tOccurrenceDate INT( 8 ) NOT NULL ,\tOccurrenceTime VARCHAR( 6 ) NOT NULL ,\tEventType INT NOT NULL DEFAULT '0',\tDivisionID INT NOT NULL DEFAULT '0',\te_ID INT NOT NULL DEFAULT '0',\tg_ID INT NOT NULL DEFAULT '0',\tPRIMARY KEY ( ID ) \t)";
    safeUpdate($conn, $query, true);
	$query = "ALTER TABLE Access.event ADD p_flag SMALLINT NOT NULL DEFAULT '0'";
    safeUpdate($conn, $query, true);
    V191472102($conn);
}

function V191472102($conn) {
    global $result;
    if (encryptDecrypt($result[1]) == "00-1E-C9-F6-FA-E1" || encryptDecrypt($result[1]) == "00-1F-D0-64-7E-FA" || encryptDecrypt($result[1]) == "00-1F-D0-22-3A-EA" || encryptDecrypt($result[1]) == "00-04-23-88-A4-AF" || encryptDecrypt($result[1]) == "00-1E-8C-33-D8-0E" || encryptDecrypt($result[1]) == "00-11-2F-E1-CC-FA") {
        $query = "SET PASSWORD FOR 'root'@'localhost' = PASSWORD( 'root' )";
        safeUpdateIData($iconn, $query, true);
        $query = "SET PASSWORD FOR 'root'@'%' = PASSWORD( 'root' )";
        safeUpdateIData($iconn, $query, true);
    } else {
        if (encryptDecrypt($result[1]) == "00-1E-8C-33-D8-0E") {
            $query = "SET PASSWORD FOR 'root'@'localhost' = PASSWORD( 'kifak' )";
            safeUpdateIData($iconn, $query, true);
            $query = "SET PASSWORD FOR 'root'@'%' = PASSWORD( 'kifak' )";
            safeUpdateIData($iconn, $query, true);
        }
    }
	if (encryptDecrypt($result[1]) == "00-16-EC-9E-E0-D1") {
        $query = "UPDATE access.tgroup SET WorkMin = (WorkMin + FlexiBreak ), FlexiBreak  = 0";
        safeUpdateIData($iconn, $query, true);
    }
    V231973115($conn);
}

function V231973115($conn) {
    $query = "DELETE FROM access.UserDiv WHERE LENGTH(UserDiv.Div) = 0";
    safeUpdate($conn, $query, true);
    $query = "DELETE FROM access.UserDept WHERE LENGTH(UserDept.Dept) = 0";
    safeUpdate($conn, $query, true);
    V231974117($conn);
}

function V231974117($conn) {
    global $result;
    $mac = encryptDecrypt($result[1]);
    $datacom_flag = false;
    if ($mac == "00-14-38-B8-FA-CE") {
        $datacom_flag = true;
    } else {
        if ($mac == "00-04-23-B5-26-2F") {
            $datacom_flag = true;
        } else {
            if ($mac == "00-1C-F0-A7-63-05") {
                $datacom_flag = true;
            } else {
                if ($mac == "00-1E-90-DC-B9-FE") {
                    $datacom_flag = true;
                } else {
                    if ($mac == "00-1C-25-24-FD-78") {
                        $datacom_flag = true;
                    } else {
                        if ($mac == "00-12-3F-47-34-23") {
                            $datacom_flag = true;
                        } else {
                            if ($mac == "00-16-EC-A4-8B-B6") {
                                $datacom_flag = true;
                            } else {
                                if ($mac == "00-0F-1F-68-8E-A2") {
                                    $datacom_flag = true;
                                } else {
                                    if ($mac == "00-23-AE-7B-6F-81") {
                                        $datacom_flag = true;
                                    } else {
                                        if ($mac == "00-22-19-A4-47-1E") {
                                            $datacom_flag = true;
                                        } else {
                                            if ($mac == "00-1C-25-4D-E8-26") {
                                                $datacom_flag = true;
                                            } else {
                                                if ($mac == "00-1C-C4-95-81-E4") {
                                                    $datacom_flag = true;
                                                } else {
                                                    if ($mac == "00-1E-C9-D5-38-9E") {
                                                        $datacom_flag = true;
                                                    } else {
                                                        if ($mac == "00-13-D3-07-92-25") {
                                                            $datacom_flag = true;
                                                        } else {
                                                            if ($mac == "00-19-5B-84-13-3B") {
                                                                $datacom_flag = true;
                                                            } else {
                                                                if ($mac == "00-1C-25-26-FF-6E") {
                                                                    $datacom_flag = true;
                                                                } else {
                                                                    if ($mac == "40-61-86-0E-D5-07") {
                                                                        $datacom_flag = true;
                                                                    } else {
                                                                        if ($mac == "40-61-86-0F-28-EB") {
                                                                            $datacom_flag = true;
                                                                        } else {
                                                                            if ($mac == "40-61-86-0F-29-18") {
                                                                                $datacom_flag = true;
                                                                            } else {
                                                                                if ($mac == "00-18-FE-FE-69-4A") {
                                                                                    $datacom_flag = true;
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
    if ($datacom_flag) {
        $query = "UPDATE access.PayrollMap SET DataCOMPayroll = 'Yes', UpdateDate = 'Yes', UpdateSalary = 'Yes'";
        safeUpdate($conn, $query, true);
    }
    V262077124($conn);
}

function V262077124($conn) {
    $query = "UPDATE access.OtherSettingMaster SET SRDay = 'None', SRScenario = 'None'";
    $mac = encryptDecrypt($result[1]);
    if ($mac == "00-16-EC-A4-8B-B6" || $mac == "00-1E-90-DB-2C-6F" || $mac == "00-16-EC-9E-E0-D1") {
        $query = "UPDATE access.OtherSettingMaster SET SRDay = 'Sunday', SRScenario = 'Morning - 2 Shifts (No Day Shift on Rotation Day)'";
    } else {
        if ($mac == "00-0F-1F-68-8E-A2" || $mac == "40-61-86-0E-D5-07" || $mac == "40-61-86-0F-28-EB" || $mac == "40-61-86-0F-29-18") {
            $query = "UPDATE access.OtherSettingMaster SET SRDay = 'Sunday', SRScenario = 'Morning - 3 Shifts'";
        } else {
            if ($mac == "00-1C-25-26-FF-6E") {
                $query = "UPDATE access.OtherSettingMaster SET SRDay = 'Sunday', SRScenario = 'Morning - 2 (No Day Shift on Rotation Day) And 3 Shifts'";
            }
        }
    }
    safeUpdate($conn, $query, true);
	$query = "UPDATE Access.tuser SET PassiveType = 'ACT' WHERE datelimit LIKE 'N%'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE Access.tuser SET PassiveType = 'RSN' WHERE datelimit LIKE 'Y%' AND SUBSTRING(tuser.datelimit, 10, 8) < '" . insertToday() . "' AND SUBSTRING(tuser.datelimit, 10, 8) NOT LIKE '19770430' ";
    safeUpdate($conn, $query, true);
    $query = "UPDATE Access.tuser SET PassiveType = 'FDA' WHERE datelimit LIKE 'Y%' AND SUBSTRING(tuser.datelimit, 10, 8) < '" . insertToday() . "' AND SUBSTRING(tuser.datelimit, 10, 8) = '19770430' ";
    safeUpdate($conn, $query, true);
    V262179128($conn);
}

function V262179128($conn) {
    $query = "INSERT INTO access.MailerText (MailerTextID , MailerType , MailerText) VALUES (NULL , 'Attendance', ''), (NULL , 'Absence', ''), (NULL , 'OddLog', ''), (NULL , 'LateArrival', ''), (NULL , 'EarlyExit', '')";
    safeUpdate($conn, $query, true);
    $query = "SELECT PLFlag FROM access.OtherSettingMaster";
    $result = selectData($conn, $query);
    if ($result[0] != "Purple") {
        $query = "Select FlagTitle.Title FROM access.FlagTitle WHERE Flag = 'Purple'";
        $result = selectData($conn, $query);
        if ($result[0] == "") {
            $query = "UPDATE access.OtherSettingMaster SET PLFlag = 'Purple'";
            safeUpdate($conn, $query, true);
            $query = "UPDATE access.FlagTitle SET FlagTitle.Title = 'PH' WHERE Flag = 'Purple'";
            safeUpdate($conn, $query, true);
        }
    }
    V282380134($conn);
}

function V282380134($conn) {
    $query = "ALTER TABLE AccessArchive.archive_am ADD PHF INT( 1 ) NOT NULL DEFAULT '0' ";
    safeUpdate($conn, $query, true);
    $query = "UPDATE AccessArchive.archive_am SET AccessArchive.archive_am.PHF = 1 WHERE AccessArchive.archive_am.ADate IN (SELECT Access.OTDate.OTDate FROM Access.OTDate)";
    safeUpdate($conn, $query, true);
	$query = "UPDATE access.tgroup, access.OtherSettingMaster SET tgroup.MoveNS = OtherSettingMaster.MoveNS WHERE tgroup.NightFlag = 1";
    safeUpdate($conn, $query, true);
	$query = " UPDATE access.shiftchangemaster SET RotateShiftNextDay = (SELECT RotateShiftNextDay FROM OtherSettingMaster)";
    safeUpdate($conn, $query, true);
    V292685140($conn);
}

function V292685140($conn) {
    $query = "UPDATE access.AttendanceMaster SET Remark = '' WHERE Remark LIKE '%AOT Round OFF%' ";
    safeUpdate($conn, $query, true);
	$query = "UPDATE access.AttendanceMaster SET Remark = '' WHERE Remark = '.' ";
    safeUpdate($conn, $query, true);
    V292688144($conn);
}

function V292688144($conn) {
    $query = "DELETE FROM access.FlagDayRotation WHERE e_date > '" . insertToday() . "' AND RecStat = 0 AND e_id IN (SELECT id FROM tuser WHERE PassiveType = 'RSN' OR PassiveType = 'PRM' OR PassiveType = 'RTD' OR PassiveType = 'TRM' OR PassiveType = 'DSD') ";
    safeUpdate($conn, $query, true);
    V292689145($conn);
}

function V292689145($conn) {
    $query = "SELECT id from access.tgroup WHERE NightFlag = 0 AND id > 1";
    $result = selectData($conn, $query);
    $query = "UPDATE access.FlagDayRotation SET group_id = '" . $result[0] . "' WHERE RecStat = 0 AND group_id = 0";
    safeUpdate($conn, $query, true);
    $query = "DELETE FROM access.FlagDayRotation WHERE e_date > '" . insertToday() . "' AND RecStat = 0 AND e_id IN (SELECT id FROM tuser WHERE PassiveType = 'RSN' OR PassiveType = 'PRM' OR PassiveType = 'RTD' OR PassiveType = 'TRM' OR PassiveType = 'DSD') ";
    safeUpdate($conn, $query, true);
    $query = "CREATE TABLE AccessArchive.FlagDayRotation ( FlagDayRotationID int( 11 ) NOT NULL AUTO_INCREMENT , e_id int( 11 ) NOT NULL DEFAULT '0', e_date int( 8 ) NOT NULL DEFAULT '0', g_id int( 11 ) NOT NULL DEFAULT '0', Flag varchar( 1024 ) NOT NULL DEFAULT 'Black', Rotate int( 1 ) NOT NULL DEFAULT '0', RecStat int( 1 ) NOT NULL DEFAULT '0', Remark varchar( 1024 ) NOT NULL DEFAULT '.', OT1 varchar( 10 ) NOT NULL DEFAULT '', OT2 varchar( 10 ) NOT NULL DEFAULT '', group_id int( 11 ) NOT NULL DEFAULT '0', PRIMARY KEY ( FlagDayRotationID ) , UNIQUE KEY FDRED ( e_id , e_date ) ) ";
    safeUpdate($conn, $query, true);
    $query = "CREATE TABLE AccessArchive.ShiftRoster ( ShiftRosterID int( 11 ) NOT NULL AUTO_INCREMENT , e_id int( 11 ) NOT NULL DEFAULT '0', e_date int( 11 ) NOT NULL DEFAULT '0', e_group int( 11 ) NOT NULL DEFAULT '0', PRIMARY KEY ( ShiftRosterID ) , UNIQUE KEY ShiftRoster ( e_id , e_date , e_group ) ) ";
    safeUpdate($conn, $query, true);
    V302790146($conn);
}

function V302790146($conn) {
    $query = "SELECT id from access.tgroup WHERE NightFlag = 0 AND id > 1";
    $result = selectData($conn, $query);
    $query = "UPDATE access.FlagDayRotation SET group_id = '" . $result[0] . "' WHERE RecStat = 0 AND group_id = 0";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.OtherSettingMaster ADD LateDays INT( 2 ) NOT NULL DEFAULT '0' ";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.tuser SET OT1 = '', OT2 = '' WHERE OT1 = 'Saturday' AND OT2 = 'Sunday' AND id IN (SELECT EmployeeID FROM OTEmployeeExempt) ";
    safeUpdate($conn, $query, true);
    V302892151($conn);
}

function V302892151($conn) {
    $query = "SELECT id FROM access.tgroup WHERE name = 'OFF'";
    $result = selectData($conn, $query);
    
    if (!empty($result)) {
        $shift = $result[0]; // Use existing OFF group id
    } else {
        $query = "SELECT MAX(id) FROM access.tgroup WHERE LENGTH(id) = 1";
        $result = selectData($conn, $query);
        $shift = $result[0] * 1 + 1;
        $query = "INSERT INTO access.tgroup (id, name, reg_date, timelimit, Start, GraceTo, FlexiBreak, Close, ShiftTypeID, ScheduleID, WorkMin) VALUES (" . $shift . ", 'OFF', '" . insertToday() . "" . getNow() . "', '00002359', '0800', '0800', 0, '1600', 1, 5, 480) ";
        safeUpdate($conn, $query, true);
    }
    $query = "UPDATE access.FlagDayRotation SET group_id = " . $shift . " WHERE (group_id = 0 OR group_id IS NULL) AND RecStat = 0";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.FlagDayRotation SET group_id = " . $shift . " WHERE RecStat = 0 AND group_id IN (SELECT id FROM tgroup WHERE NightFlag = 1) ";
    safeUpdate($conn, $query, true);
    V302893152($conn);
}

function V302893152($conn) {
	exec("php Backup.php");
    $query = "SELECT MAX(RDate) FROM access.ShiftRotateLog";
    $result = selectData($conn, $query);
    $query = "SELECT DISTINCT(ShiftFrom), RTime FROM access.ShiftRotateLog WHERE RDate > " . getLastDay($result[0], 7);
//    $result = safeUpdateIData($iconn, $query, true);
    $result = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
        $query = "UPDATE access.ShiftChangeMaster SET RTime = '" . substr(addZero($cur[1], 4), 0, 2) . "00' WHERE id = " . $cur[0];
        safeUpdate($conn, $query, true);
    }
    
    $query = "UPDATE access.FlagDayRotation SET OT = 'OT1' WHERE LENGTH(OT1) > 1";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.FlagDayRotation SET OT = 'OT2' WHERE LENGTH(OT2) > 1";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.FlagDayRotation SET OT1 = '0', OT2 = '0' ";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.FlagDayRotation MODIFY OT1 INT DEFAULT NULL, MODIFY OT2 INT DEFAULT NULL";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.FlagDayRotation SET OT1 = NULL, OT2 = NULL";
    safeUpdate($conn, $query, true);
    exec("php MaintainDB.php");
    V302994154($conn);
}

function V302994154($conn) {
    $query = "INSERT INTO access.FlagTitle (Flag) VALUES ('Magenta'), ('Teal'), ('Aqua'), ('Safron'), ('Amber'), ('Gold'), ('Vermilion'), ('Silver'), ('Maroon'), ('Pink')";
    safeUpdate($conn, $query, true);
    $query = "DELETE FROM access.TLSFLag where TLSFlagID > 1";
    safeUpdate($conn, $query, true);
    V313095155($conn);
}

function V313095155($conn) {
    $query = "UPDATE access.OtherSettingMaster SET FlagLimitType = 'Jan 01' ";
    safeUpdate($conn, $query, true);
    $query = "INSERT INTO access.MailerText (MailerType , MailerText) VALUES ('FlagApplication', '')";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.TLSFlag SET Black = 'Yes', Proxy = 'Yes' ";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.FlagDayRotation SET group_id = 2 WHERE group_id <> 2 AND RecStat = 0 ";
    safeUpdate($conn, $query, true);
    V313096156($conn);
}

function V313096156($conn) {
    $query = "UPDATE access.FlagDayRotation SET group_id = 2 WHERE group_id <> 2 AND RecStat = 0 ";
    safeUpdate($conn, $query, true);
    $query = "INSERT INTO access.ScheduleMaster (Name) VALUES ('IN/OUT Terminal, No Break')";
    safeUpdate($conn, $query, true);
    V313198159($conn);
}

function V313198159($conn) {
    $query = "SELECT id, ExemptOT1, ExemptOT2, ExemptOTDate FROM access.tgroup WHERE id > 2";
//    $result = safeUpdateIData($iconn, $query, true);
    $result = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
        if ($cur[1] == "Yes") {
            if ($cur[2] == "Yes") {
                if ($cur[3] == "Yes") {
                    $query = "UPDATE access.tgroup SET ExemptOT = 'ALL OT' WHERE id = " . $cur[0];
                } else {
                    $query = "UPDATE access.tgroup SET ExemptOT = 'OT1/OT2' WHERE id = " . $cur[0];
                }
            } else {
                $query = "UPDATE access.tgroup SET ExemptOT = 'OT1' WHERE id = " . $cur[0];
            }
        } else {
            if ($cur[2] == "Yes") {
                if ($cur[3] == "Yes") {
                    $query = "UPDATE access.tgroup SET ExemptOT = 'OT2/OTD' WHERE id = " . $cur[0];
                } else {
                    $query = "UPDATE access.tgroup SET ExemptOT = 'OT2' WHERE id = " . $cur[0];
                }
            } else {
                if ($cur[3] == "Yes") {
                    $query = "UPDATE access.tgroup SET ExemptOT = 'OTD' WHERE id = " . $cur[0];
                } else {
                    $query = "UPDATE access.tgroup SET ExemptOT = 'NONE' WHERE id = " . $cur[0];
                }
            }
        }
        safeUpdate($conn, $query, true);
    }
    V3136102168($conn);
}

function V3136102168($conn) {
    $query = "UPDATE access.tgate SET tgate.Exit = 1 WHERE tgate.Meal = 1";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.TLSFlag SET Black = 'Yes', Proxy = 'Yes' ";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.FlagDayRotation SET group_id = 2 WHERE group_id <> 2 AND RecStat = 0 ";
    safeUpdate($conn, $query, true);
    $query = "INSERT INTO access.ScheduleMaster ( ScheduleID, Name ) VALUES (6, 'Flexi Start-End, Multi In-Out (>1)')";
    safeUpdate($conn, $query, true);
    $query = "INSERT INTO access.ScheduleMaster ( ScheduleID, Name) VALUES (7, 'IN/OUT Terminal, No Break')";
    safeUpdate($conn, $query, true);
    V3239107174($conn);
}

function V3239107174($conn) {
    $query = "ALTER TABLE AccessArchive.archive_am ADD EarlyIn_flag INT( 1 ) NULL DEFAULT '0' ";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE AccessArchive.FlagDayRotation ADD OTH INT NULL DEFAULT '0'";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE AccessArchive.FlagDayRotation ADD OT VARCHAR(5) DEFAULT NULL";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE AccessArchive.archive_am MODIFY `Remark` VARCHAR( 1024 ) DEFAULT NULL";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE AccessArchive.archive_trans MODIFY `Transactquery` VARCHAR( 1024 ) DEFAULT NULL";
    safeUpdate($conn, $query, true);
    $query = "UPDATE FlagDayRotation SET RecStat = 0 WHERE LENGTH(Flag) < 3 AND e_date > " . insertToday();
    safeUpdate($conn, $query, true);
    V3340108177($conn);
}

function V3340108177($conn) {
    $t_user = array();
    $t_user = ["idno", "dept", "company", "phone", "remark"];
    for ($i = 0; $i < count($t_user); $i++) { 
        $query = "SELECT DISTINCT(" . $t_user[$i] . ") FROM access.tuser";
//        $result = safeUpdateIData($iconn, $query, true);
//        $result = safeUpdate($conn, $query, true);
        $result = mysqli_query($conn, $query);
        while ($cur = mysqli_fetch_row($result)) {
            $query = "INSERT INTO access.MigrateMaster (Col, Val) VALUES ('" . $t_user[$i] . "', '" . $cur[0] . "')";
            safeUpdate($conn, $query, true);
        }
    }
    $query = "UPDATE access.UserMaster SET UserMail = 'a@b.com' WHERE UserMail LIKE '%datacom%' OR UserMail LIKE '%compusoft%' ";
    safeUpdate($conn, $query, true);
    V3443110184($conn);
}

function V3443110184($conn) {
	$query = "DELETE FROM Access.ScheduleMaster WHERE ScheduleID > 5";
    safeUpdate($conn, $query, true);
    $query = "INSERT INTO Access.ScheduleMaster (ScheduleID, Name) VALUES (6, 'Flexi Start-End, Multi In-Out (>1)'), (7, 'IN-OUT Terminal, No Break')";
    safeUpdate($conn, $query, true);
    V3444110185($conn);
}

function V3444110185($conn) {
    $query = "ALTER TABLE UNIS.cAuthority ADD L_MntMgr INT(10) NULL, ADD L_MntClient INT(10) NULL, ADD L_MntTerminal INT(10) NULL, ADD L_MntAuthLog INT(10) NULL, ADD L_MntEvent INT(10) NULL, ADD L_TmnMgr INT(10) NULL, ADD L_TmnAdd INT(10) NULL, ADD L_TmnMod INT(10) NULL, ADD L_TmnDel INT(10) NULL, ADD L_TmnUpgrade INT(10) NULL, ADD L_TmnOption INT(10) NULL, ADD L_TmnAdmin INT(10) NULL, ADD L_TmnSendFile INT(10) NULL, ADD L_EmpMgr INT(10) NULL, ADD L_EmpAdd INT(10) NULL, ADD L_EmpMod INT(10) NULL, ADD L_EmpDel INT(10) NULL, ADD L_EmpSendTerminal INT(10) NULL, ADD L_EmpTerminalMng INT(10) NULL, ADD L_EmpRegAdmin INT(10) NULL, ADD L_VstMgr INT(10) NULL, ADD L_VstAdd INT(10) NULL, ADD L_VstMod INT(10) NULL, ADD L_VstDel INT(10) NULL, ADD L_BlckMgr INT(10) NULL, ADD L_BlckChange INT(10) NULL, ADD L_BlckRelease INT(10) NULL, ADD L_BlckDel INT(10) NULL, ADD L_BlckMod INT(10) NULL, ADD L_AccMgr INT(10) NULL, ADD L_AccSet INT(10) NULL, ADD L_MapMgr INT(10) NULL, ADD L_MapSet INT(10) NULL, ADD L_TnaMgr INT(10) NULL, ADD L_TnaSet INT(10) NULL, ADD L_TnaSpecial INT(10) NULL, ADD L_TnaWork INT(10) NULL, ADD L_TnaOutState INT(10) NULL, ADD L_TnaOutExcRecord INT(10) NULL, ADD L_TnaSummary INT(10) NULL, ADD L_TnaSendResult INT(10) NULL, ADD L_TnaDelData INT(10) NULL, ADD L_MealMgr INT(10) NULL, ADD L_MealOutRecord INT(10) NULL, ADD L_MealDelData INT(10) NULL, ADD L_MealOutDept INT(10) NULL, ADD L_MealOutPerson INT(10) NULL, ADD L_MealSet INT(10) NULL, ADD L_LogMgr INT(10) NULL, ADD L_LogOutRecord INT(10) NULL, ADD L_LogDelRecord INT(10) NULL, ADD L_SetRegInfo INT(10) NULL, ADD L_SetMgr INT(10) NULL, ADD L_SetServer INT(10) NULL, ADD L_SetPwd INT(10) NULL, ADD L_SetMail INT(10) NULL, ADD L_SetEtc INT(10) NULL ";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE UNIS.cTimezone ADD L_AuthValue INT(10) NULL";
    safeUpdate($conn, $query, true);
    V3547112189($conn);
}

function V3547112189($conn) {
    //mysqli_select_db($conn, "UNIS");
    $query = "ALTER TABLE UNIS.cAuthority ADD L_MntMgr INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_MntClient INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_MntTerminal INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_MntAuthLog INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_MntEvent INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_TmnMgr INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_TmnAdd INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_TmnMod INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_TmnDel INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_TmnUpgrade INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_TmnOption INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_TmnAdmin INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_TmnSendFile INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_EmpMgr INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_EmpAdd INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_EmpMod INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_EmpDel INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_EmpSendTerminal INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_EmpTerminalMng INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_EmpRegAdmin INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_VstMgr INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_VstAdd INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_VstMod INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_VstDel INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_BlckMgr INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_BlckChange INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_BlckRelease INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_BlckDel INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_BlckMod INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_AccMgr INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_AccSet INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_MapMgr INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_MapSet INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_TnaMgr INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_TnaSet INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_TnaSpecial INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_TnaWork INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_TnaOutState INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_TnaOutExcRecord INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_TnaSummary INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_TnaSendResult INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_TnaDelData INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_MealMgr INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_MealOutRecord INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_MealDelData INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_MealOutDept INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_MealOutPerson INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_MealSet INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_LogMgr INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_LogOutRecord INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_LogDelRecord INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_SetRegInfo INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_SetMgr INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_SetServer INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_SetPwd INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_SetMail INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_SetEtc INT NULL DEFAULT 0; ALTER TABLE cTimezone ADD L_AuthValue INT NULL DEFAULT 0; CREATE TABLE UNIS.iACUInfo (L_TID INT(10) NULL DEFAULT 0, C_PartitionStatus VARCHAR(255) NULL, C_ZoneStatus VARCHAR(255) NULL, C_LockStatus VARCHAR(255) NULL, C_ReaderStatus VARCHAR(255) NULL, C_ReaderVer1 VARCHAR(255) NULL, C_ReaderVer2 VARCHAR(255) NULL, C_ReaderVer3 VARCHAR(255) NULL, C_ReaderVer4 VARCHAR(255) NULL, C_ReaderVer5 VARCHAR(255) NULL, C_ReaderVer6 VARCHAR(255) NULL, C_ReaderVer7 VARCHAR(255) NULL, C_ReaderVer8 VARCHAR(255) NULL, C_ReaderName0 VARCHAR(255) NULL, C_ReaderName1 VARCHAR(255) NULL, C_ReaderName2 VARCHAR(255) NULL, C_ReaderName3 VARCHAR(255) NULL, C_ReaderName4 VARCHAR(255) NULL, C_ReaderName5 VARCHAR(255) NULL, C_ReaderName6 VARCHAR(255) NULL, C_ReaderName7 VARCHAR(255) NULL, C_WiegandName1 VARCHAR(255) NULL, C_WiegandName2 VARCHAR(255) NULL, C_WiegandName3 VARCHAR(255) NULL, C_WiegandName4 VARCHAR(255) NULL, PRIMARY KEY (L_TID) ); CREATE TABLE UNIS.iAdminRestrict (L_UID INT(10) NULL DEFAULT 0, C_AccessGroup VARCHAR(255) NULL); CREATE TABLE UNIS.iDVRInfo (L_DVRID INT(10) NULL DEFAULT 0, C_DVRIP VARCHAR(255) NULL, L_DVRPort INT(10) NULL DEFAULT 0, C_DVRLoginID VARCHAR(255) NULL, C_DVRLoginPW VARCHAR(255) NULL, L_PrevTime INT(10) NULL DEFAULT 0; ALTER TABLE iMapTerminal ADD L_Size INT NULL DEFAULT 0; CREATE TABLE UNIS.iMobileKeyAdmin (C_ServerDNS VARCHAR(255) NULL, C_ClientID VARCHAR(255) NULL, C_Secret VARCHAR(255) NULL, C_EMail VARCHAR(255) NULL, C_Password VARCHAR(255) NULL, C_CountryCode VARCHAR(255) NULL, C_PhoneNo VARCHAR(255) NULL, C_SiteCode VARCHAR(255) NULL, C_MacAddr VARCHAR(255) NULL, L_tzIndex INT(10) NULL DEFAULT 0, L_tzBias INT(10) NULL DEFAULT 0, C_tzKeyName VARCHAR(255) NULL, C_TimeZone VARCHAR(255) NULL, C_Company VARCHAR(255) NULL, C_Country VARCHAR(255) NULL, L_SiteCode INT(10) NULL DEFAULT 0); CREATE TABLE UNIS.iNecessityField (L_Type INT(10) NULL DEFAULT 0, L_Index INT(10) NULL DEFAULT 0); CREATE TABLE UNIS.iUserMobileKey (L_UID INT(10) NULL DEFAULT 0, C_MobilePhone VARCHAR(255) NULL, C_CountryCode VARCHAR(255) NULL, L_KeyType INT(10) NULL DEFAULT 0, C_ImkeyPeriod VARCHAR(255) NULL, L_issuecount INT(10) NULL DEFAULT 0, C_KeyNo VARCHAR(255) NULL, L_NowIssue INT(10) NULL, B_UUID LONGBLOB NULL, PRIMARY KEY (L_UID) ); ALTER TABLE tAuditServer ADD C_Remark VARCHAR(255) NULL; ALTER TABLE tChangedInfo ADD L_ClientID INT(10) NULL DEFAULT 0; CREATE TABLE UNIS.tChangedInfo (C_CreateTime VARCHAR(255) NULL, L_Target INT(10) NULL DEFAULT 0, L_Procedure INT(10) NULL DEFAULT 0, L_TargetID INT(10) NULL DEFAULT 0, C_TargetCode VARCHAR(255) NULL, L_ClientID INT(10) NULL DEFAULT 0, UNIQUE INDEX PK_tChangedInfo (C_CreateTime, L_Target, L_Procedure, L_TargetID) ); ALTER TABLE tConfig ADD L_PanicDuress INT(10) NULL DEFAULT 0; ALTER TABLE tConfig ADD L_DefaultNotAccess INT(10) NULL DEFAULT 0; ALTER TABLE tConfig ADD L_ServerLanguage INT(10) NULL DEFAULT 0; ALTER TABLE tConfig ADD L_LFDLevel INT(10) NULL DEFAULT 0; ALTER TABLE tConfig ADD L_AuthData INT(10) NULL DEFAULT 0; ALTER TABLE tConfig ADD C_WebOpen VARCHAR(255) NULL; ALTER TABLE tConfig ADD C_MobileOpen VARCHAR(255) NULL; ALTER TABLE tConfig ADD L_AdminRestrict INT(10) NULL DEFAULT 0; ALTER TABLE tConfig ADD L_FindUserByFP INT(10) NULL DEFAULT 0; ALTER TABLE tEnter ADD D_Latitude INT(10) NULL DEFAULT 0; ALTER TABLE tEnter ADD D_Longitude INT(10) NULL DEFAULT 0; ALTER TABLE tEnter ADD C_MobilePhone VARCHAR(255) NULL; CREATE TABLE UNIS.tLogonHistory (C_DateTime VARCHAR(255) NULL, C_AccessType VARCHAR(255) NULL, L_UID INT(10) NULL DEFAULT 0, C_IP VARCHAR(255) NULL, C_LogonSuccess VARCHAR(255) NULL ); ALTER TABLE tMailConfig ADD L_DuressFinger INT(10) NULL DEFAULT 0; ALTER TABLE tMailConfig ADD L_ACU INT(10) NULL DEFAULT 0; ALTER TABLE tMailConfig ADD L_NoPermission INT(10) NULL DEFAULT 0; CREATE TABLE UNIS.tPasswdHistory (L_UID INT(10) NULL DEFAULT 0, C_OldRemotePW VARCHAR(255) NULL, C_NewRemotePW VARCHAR(255) NULL, C_Action VARCHAR(255) NULL, L_AdminID INT(10) NULL DEFAULT 0, C_UDateTime VARCHAR(255) NULL); CREATE TABLE UNIS.tPWConfig (L_LengthMin INT(10) NULL DEFAULT 0, L_LengthMax INT(10) NULL DEFAULT 0, L_DayLimit INT(10) NULL DEFAULT 0, C_NotAllowOldPW VARCHAR(255) NULL, L_WrongLimit INT(10) NULL DEFAULT 0, C_NotAllowDupChar VARCHAR(255) NULL, C_FirstChgPW VARCHAR(255) NULL, C_UpperLowerSame VARCHAR(255) NULL, C_NotAllowSameID VARCHAR(255) NULL, C_CreateOpt VARCHAR(255) NULL, C_OptUpper VARCHAR(255) NULL, C_OptLower VARCHAR(255) NULL, C_OptNumeric VARCHAR(255) NULL, C_OptSymbol VARCHAR(255) NULL, L_InitValue INT(10) NULL DEFAULT 0, C_InitPwd VARCHAR(255) NULL); ALTER TABLE tTerminal ADD L_AuthType INT(10) NULL DEFAULT 0; ALTER TABLE tTerminal ADD L_DVRID INT(10) NULL DEFAULT 0; ALTER TABLE tTerminal ADD L_Chnl1 INT(10) NULL DEFAULT 0; ALTER TABLE tTerminal ADD L_Chnl2 INT(10) NULL DEFAULT 0; ALTER TABLE tuser ADD L_FaceIdentify INT(10) NULL DEFAULT 0; ALTER TABLE tuser ADD B_DuressFinger LONGBLOB NULL; ALTER TABLE tUser ADD L_AuthValue INT(10) NULL DEFAULT 0; ALTER TABLE tUser ADD L_RegServer INT(10) NULL DEFAULT 0; ALTER TABLE tUser ADD C_RemotePW VARCHAR(255) NULL; ALTER TABLE tUser ADD L_WrongCount INT(10) NULL DEFAULT 0; ALTER TABLE tUser ADD L_LogonLocked INT(10) NULL DEFAULT 0; ALTER TABLE tUser ADD C_LogonDateTime VARCHAR(255) NULL; ALTER TABLE tUser ADD C_UdatePassword VARCHAR(255) NULL; ALTER TABLE tUser ADD C_MustChgPwd VARCHAR(255) NULL; ALTER TABLE tUser ADD B_DuressFinger LONGBLOB NULL; CREATE TABLE UNIS.wTempWorkResult (C_WorkDate VARCHAR(255) NULL, L_UID INT(10) NULL DEFAULT 0, L_AccessTime INT(10) NULL DEFAULT 0, L_Mode INT(10) NULL, UNIQUE INDEX PK_wTempWorkResult (C_WorkDate, L_UID, L_Mode) ); ALTER TABLE wWorkConfig ADD C_NeisSavePath VARCHAR(255) NULL; ALTER TABLE wWorkConfig ADD L_NeisUsed INT(10) NULL DEFAULT 0;  ";
    $query_array = explode(";", $query);
    for ($i = 0; $i < count($query_array); $i++) {
        mysqli_query($conn, $query_array[$i]);
    }
    V3547113192($conn, $conn);
}

function V3547113192($conn, $iconn) {
    $uconn = mysqli_connect("127.0.0.1", "unisuser", "unisamho", "UNIS");
    $query = "DROP TRIGGER IF EXISTS UNIS.i_tuser ";
    mysqli_query($uconn, $query);
    $query = "CREATE TRIGGER UNIS.i_tuser AFTER INSERT ON UNIS.tuser FOR EACH ROW INSERT INTO Access.tuser (id, name, reg_date, datelimit, dept, company) VALUES (New.L_ID, New.C_Name, IFNULL(SUBSTRING(New.C_RegDate, 0, 8), 20010101), IFNULL(CONCAT('N', New.C_DateLimit), 'N2001010120200101'), (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID)) ";
    mysqli_query($uconn, $query);
    $query = "DROP TRIGGER IF EXISTS UNIS.u_tuser ";
    mysqli_query($uconn, $query);
    $query = "CREATE TRIGGER UNIS.u_tuser AFTER UPDATE ON UNIS.tuser FOR EACH ROW UPDATE Access.tuser SET name = New.C_Name, reg_date = SUBSTRING(New.C_RegDate, 0,8), dept = (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), company = (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID) WHERE id = New.L_ID ";
    mysqli_query($uconn, $query);
    V3548114193($conn, $iconn);
}

function V3548114193($conn, $iconn) {
    $uconn = mysqli_connect("127.0.0.1", "unisuser", "unisamho", "UNIS");
    $query = "DROP TRIGGER IF EXISTS UNIS.i_tuser ";
    mysqli_query($uconn, $query);
    $query = "CREATE TRIGGER UNIS.i_tuser AFTER INSERT ON UNIS.tuser FOR EACH ROW INSERT INTO Access.tuser (id, name, reg_date, datelimit, dept, company) VALUES (New.L_ID, New.C_Name, IFNULL(SUBSTRING(New.C_RegDate, 0, 8), 20010101), IFNULL(CONCAT('N', New.C_DateLimit), 'N2001010120200101'), (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID)) ";
    mysqli_query($uconn, $query);
    $query = "DROP TRIGGER IF EXISTS UNIS.u_tuser ";
    safeUpdateIData($iconn, $query, true);
    $query = "CREATE TRIGGER UNIS.u_tuser AFTER UPDATE ON UNIS.tuser FOR EACH ROW UPDATE Access.tuser SET name = New.C_Name, reg_date = SUBSTRING(New.C_RegDate, 0,8), dept = (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), company = (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID) WHERE id = New.L_ID ";
    mysqli_query($uconn, $query);
    V3550115195($conn, $iconn);
}

function V3550115195($conn, $iconn) { 
    global $mac_in_version;
    $uconn = mysqli_connect("127.0.0.1", "unisuser", "unisamho", "UNIS");
    if (getRegister($mac_in_version, 7) == "82" || getRegister($mac_in_version, 7) == "103" || getRegister($mac_in_version, 7) == "106" || getRegister($mac_in_version, 7) == "107" || getRegister($mac_in_version, 7) == "126" || getRegister($mac_in_version, 7) == "57" || getRegister($mac_in_version, 7) == "58" || getRegister($mac_in_version, 7) == "59" || getRegister($mac_in_version, 7) == "60") {
        $query = "DROP TRIGGER IF EXISTS UNIS.i_tuser ";
        mysqli_query($uconn, $query);
        $query = "CREATE TRIGGER UNIS.i_tuser AFTER INSERT ON UNIS.tuser FOR EACH ROW INSERT INTO Access.tuser (id, name, reg_date, datelimit, dept, company, idno) VALUES (New.L_ID, New.C_Name, IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), IFNULL( IF(New.L_OptDateLimit = 1, CONCAT('Y', New.C_DateLimit), CONCAT('N', New.C_DateLimit)), 'N2001010120200101'), (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID), New.C_Unique) ";
        mysqli_query($uconn, $query);
        $query = "DROP TRIGGER IF EXISTS UNIS.u_tuser ";
        mysqli_query($uconn, $query);
        $query = "CREATE TRIGGER UNIS.u_tuser AFTER UPDATE ON UNIS.tuser FOR EACH ROW UPDATE Access.tuser SET name = New.C_Name, reg_date = IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), datelimit = IFNULL( IF(New.L_OptDateLimit = 1, CONCAT('Y', New.C_DateLimit), CONCAT('N', New.C_DateLimit)), 'N2001010120200101'), dept = (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), company = (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID), idno = New.C_Unique WHERE id = New.L_ID ";
        mysqli_query($uconn, $query);
    }
    $text = "Version Update V.35.50.115.195";
    print "\n" . $text;
    V3550115196($conn, $iconn);
}

function V3550115196($conn, $iconn) { 
    global $mac_in_version;
    $uconn = mysqli_connect("127.0.0.1", "unisuser", "unisamho", "UNIS");
    if (!(getRegister($mac_in_version, 7) == "82" || getRegister($mac_in_version, 7) == "103" || getRegister($mac_in_version, 7) == "106" || getRegister($mac_in_version, 7) == "107" || getRegister($mac_in_version, 7) == "126" || getRegister($mac_in_version, 7) == "57" || getRegister($mac_in_version, 7) == "58" || getRegister($mac_in_version, 7) == "59" || getRegister($mac_in_version, 7) == "60")) {
        $query = "DROP TRIGGER IF EXISTS UNIS.i_tuser ";
        mysqli_query($uconn, $query);
        $query = "CREATE TRIGGER UNIS.i_tuser AFTER INSERT ON UNIS.tuser FOR EACH ROW INSERT INTO Access.tuser (id, name, reg_date, datelimit, dept, company) VALUES (New.L_ID, New.C_Name, IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), IFNULL( IF(New.L_OptDateLimit = 1, CONCAT('Y', New.C_DateLimit), CONCAT('N', New.C_DateLimit)), 'N2001010120200101'), (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID)) ";
        mysqli_query($uconn, $query);
        $query = "DROP TRIGGER IF EXISTS UNIS.u_tuser ";
        mysqli_query($uconn, $query);
        $query = "CREATE TRIGGER UNIS.u_tuser AFTER UPDATE ON UNIS.tuser FOR EACH ROW UPDATE Access.tuser SET name = New.C_Name, reg_date = IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), datelimit = IFNULL( IF(New.L_OptDateLimit = 1, CONCAT('Y', New.C_DateLimit), CONCAT('N', New.C_DateLimit)), 'N2001010120200101'), dept = (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), company = (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID) WHERE id = New.L_ID ";
        mysqli_query($uconn, $query);
    }
    $text = "Version Update V.35.50.115.196";
    print "\n" . $text;
    V3550115197($conn, $iconn);
}

function V3550115197($conn, $iconn) { 
    global $mac_in_version;
    $uconn = mysqli_connect("127.0.0.1", "unisuser", "unisamho", "UNIS");
    if (getRegister($mac_in_version, 7) == "82" || getRegister($mac_in_version, 7) == "103" || getRegister($mac_in_version, 7) == "106" || getRegister($mac_in_version, 7) == "107" || getRegister($mac_in_version, 7) == "126" || getRegister($mac_in_version, 7) == "57" || getRegister($mac_in_version, 7) == "58" || getRegister($mac_in_version, 7) == "59" || getRegister($mac_in_version, 7) == "60") {
        $query = "DROP TRIGGER IF EXISTS UNIS.i_tuser ";
        mysqli_query($uconn, $query);
        $query = "CREATE TRIGGER UNIS.i_tuser AFTER INSERT ON UNIS.tuser FOR EACH ROW INSERT INTO Access.tuser (id, name, reg_date, datelimit, dept, company, idno) VALUES (New.L_ID, New.C_Name, IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), IFNULL( IF(New.L_OptDateLimit = 1, CONCAT('Y', New.C_DateLimit), CONCAT('N', New.C_DateLimit)), 'N2001010120200101'), (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID), New.C_Unique) ";
        mysqli_query($uconn, $query);
        $query = "DROP TRIGGER IF EXISTS UNIS.u_tuser ";
        mysqli_query($uconn, $query);
        $query = "CREATE TRIGGER UNIS.u_tuser AFTER UPDATE ON UNIS.tuser FOR EACH ROW UPDATE Access.tuser SET name = New.C_Name, reg_date = IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), datelimit = IFNULL( IF(New.L_OptDateLimit = 1, CONCAT('Y', New.C_DateLimit), CONCAT('N', New.C_DateLimit)), 'N2001010120200101'), dept = (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), company = (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID), idno = New.C_Unique WHERE id = New.L_ID ";
        mysqli_query($uconn, $query);
    } else {
        $query = "DROP TRIGGER IF EXISTS UNIS.i_tuser ";
        mysqli_query($uconn, $query);
        $query = "CREATE TRIGGER UNIS.i_tuser AFTER INSERT ON UNIS.tuser FOR EACH ROW INSERT INTO Access.tuser (id, name, reg_date, datelimit, dept, company) VALUES (New.L_ID, New.C_Name, IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), IFNULL( IF(New.L_OptDateLimit = 1, CONCAT('Y', New.C_DateLimit), CONCAT('N', New.C_DateLimit)), 'N2001010120200101'), (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID)) ";
        mysqli_query($uconn, $query);
        $query = "DROP TRIGGER IF EXISTS UNIS.u_tuser ";
        mysqli_query($uconn, $query);
        $query = "CREATE TRIGGER UNIS.u_tuser AFTER UPDATE ON UNIS.tuser FOR EACH ROW UPDATE Access.tuser SET name = New.C_Name, reg_date = IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), datelimit = IFNULL( IF(New.L_OptDateLimit = 1, CONCAT('Y', New.C_DateLimit), CONCAT('N', New.C_DateLimit)), 'N2001010120200101'), dept = (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), company = (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID) WHERE id = New.L_ID ";
        mysqli_query($uconn, $query);
    }
    $query = "DROP TRIGGER IF EXISTS UNIS.i_temploye ";
    mysqli_query($uconn, $query);
    $query = "CREATE TRIGGER UNIS.i_temploye AFTER INSERT ON UNIS.temploye FOR EACH ROW UPDATE Access.tuser SET dept = (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_UID), company = (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_UID) WHERE id = New.L_UID ";
    mysqli_query($uconn, $query);
    $query = "DROP TRIGGER IF EXISTS UNIS.u_temploye ";
    mysqli_query($uconn, $query);
    $query = "CREATE TRIGGER UNIS.u_temploye AFTER UPDATE ON UNIS.temploye FOR EACH ROW UPDATE Access.tuser SET dept = (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_UID), company = (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_UID) WHERE id = New.L_UID ";
    mysqli_query($uconn, $query);
    unlink("UNISynch.php");
    unlink("UNISynch.bat");
    unlink("OIMigrateMySQL.php");
    $text = "Version Update V.35.50.115.197";
    print "\n" . $text;
    V3550115198($conn, $iconn);
}

function V3550115198($conn, $iconn) {
    global $mac_in_version;
    $uconn = mysqli_connect("127.0.0.1", "unisuser", "unisamho", "UNIS");
    $query = "SELECT id, datelimit, PassiveType FROM Access.tuser";
    $resultquery = mysqli_query($iconn, $query);
    while ($cur = mysqli_fetch_row($resultquery)) {
        if ($cur[2] == "ACT") {
            $query = "UPDATE UNIS.tuser SET L_OptDateLimit = 1, C_DateLimit = '" . substr($cur[1], 1, 8) . "" . substr($cur[1], 9, 8) . "' WHERE L_ID = " . $cur[0];
        } else {
            if (substr($cur[1], 9, 8) < insertToday()) {
                $query = "UPDATE UNIS.tuser SET L_OptDateLimit = 1, C_DateLimit = '" . substr($cur[1], 1, 8) . "" . substr($cur[1], 9, 8) . "' WHERE L_ID = " . $cur[0];
            } else {
                $query = "UPDATE UNIS.tuser SET L_OptDateLimit = 1, C_DateLimit = '" . substr($cur[1], 1, 8) . "" . getLastDay(insertToday(), 1) . "' WHERE L_ID = " . $cur[0];
            }
        }
        mysqli_query($uconn, $query);
    }
    if (getRegister($mac_in_version, 7) == "82" || getRegister($mac_in_version, 7) == "103" || getRegister($mac_in_version, 7) == "106" || getRegister($mac_in_version, 7) == "107" || getRegister($mac_in_version, 7) == "126" || getRegister($mac_in_version, 7) == "57" || getRegister($mac_in_version, 7) == "58" || getRegister($mac_in_version, 7) == "59" || getRegister($mac_in_version, 7) == "60") {
        $query = "DROP TRIGGER IF EXISTS UNIS.i_tuser ";
        mysqli_query($uconn, $query);
        $query = "CREATE TRIGGER UNIS.i_tuser AFTER INSERT ON UNIS.tuser FOR EACH ROW INSERT INTO Access.tuser (id, name, reg_date, datelimit, PassiveType, dept, company, idno) VALUES (New.L_ID, New.C_Name, IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), IFNULL( ( CASE WHEN New.L_OptDateLimit = 0 THEN CONCAT('N', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN CONCAT('Y', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) >= CURDATE()*1 THEN CONCAT('N', New.C_DateLimit) ELSE datelimit END ), 'N2001010120200101'), IFNULL( ( CASE WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN 'RSN' ELSE 'ACT' END ), 'ACT'), (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID), New.C_Unique) ";
        mysqli_query($uconn, $query);
        $query = "DROP TRIGGER IF EXISTS UNIS.u_tuser ";
        mysqli_query($uconn, $query);
        $query = "CREATE TRIGGER UNIS.u_tuser AFTER UPDATE ON UNIS.tuser FOR EACH ROW UPDATE Access.tuser SET name = New.C_Name, reg_date = IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), datelimit = IFNULL( ( CASE WHEN New.L_OptDateLimit = 0 THEN CONCAT('N', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN CONCAT('Y', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) >= CURDATE()*1 THEN CONCAT('N', New.C_DateLimit) ELSE datelimit END ), 'N2001010120200101'), PassiveType = IFNULL( ( CASE WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN 'RSN' ELSE 'ACT' END ), 'ACT'), dept = (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), company = (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID), idno = New.C_Unique WHERE id = New.L_ID ";
        mysqli_query($uconn, $query);
    } else {
        $query = "DROP TRIGGER IF EXISTS UNIS.i_tuser ";
        mysqli_query($uconn, $query);
        $query = "CREATE TRIGGER UNIS.i_tuser AFTER INSERT ON UNIS.tuser FOR EACH ROW INSERT INTO Access.tuser (id, name, reg_date, datelimit, PassiveType, dept, company) VALUES (New.L_ID, New.C_Name, IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), IFNULL( ( CASE WHEN New.L_OptDateLimit = 0 THEN CONCAT('N', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN CONCAT('Y', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) >= CURDATE()*1 THEN CONCAT('N', New.C_DateLimit) ELSE datelimit END ), 'N2001010120200101'), IFNULL( ( CASE WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN 'RSN' ELSE 'ACT' END ), 'ACT'), (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID)) ";
        mysqli_query($uconn, $query);
        $query = "DROP TRIGGER IF EXISTS UNIS.u_tuser ";
        mysqli_query($uconn, $query);
        $query = "CREATE TRIGGER UNIS.u_tuser AFTER UPDATE ON UNIS.tuser FOR EACH ROW UPDATE Access.tuser SET name = New.C_Name, reg_date = IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), datelimit = IFNULL( ( CASE WHEN New.L_OptDateLimit = 0 THEN CONCAT('N', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN CONCAT('Y', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) >= CURDATE()*1 THEN CONCAT('N', New.C_DateLimit) ELSE datelimit END ), 'N2001010120200101'), PassiveType = IFNULL( ( CASE WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN 'RSN' ELSE 'ACT' END ), 'ACT'), dept = (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), company = (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID) WHERE id = New.L_ID ";
        mysqli_query($uconn, $query);
    }
    unlink("UNISynch.php");
    unlink("UNISynch.bat");
    unlink("OIMigrateMySQL.php");
    unlink("Functions-ica.php");
    unlink("HalogenSequence.php");
    unlink("ACHalogen.php");
    unlink("ACHalogen.bat");
    unlink("UNISUpgrade1.php");
    unlink("UNISUpgrade2.php");
    unlink("UNISTrigger1.php");
    unlink("UNISTrigger2.php");
    unlink("BSMigrate.php");
    unlink("AAMigrate-Stallion.php");
    unlink("TestCoupon.php");
    safeUpdateIData($iconn, $query, true);
    $text = "Version Update V.35.50.115.198";
    print "\n" . $text;
    V3550116199($conn, $iconn);
}

function V3550116199($conn, $iconn) {
    global $mac_in_version;
    $uconn = mysqli_connect("127.0.0.1", "unisuser", "unisamho", "UNIS");
    if (getRegister($mac_in_version, 7) == "7") {
        $query = "DROP TRIGGER IF EXISTS UNIS.i_tuser ";
        mysqli_query($uconn, $query);
        $query = "CREATE TRIGGER UNIS.i_tuser AFTER INSERT ON UNIS.tuser FOR EACH ROW INSERT INTO Access.tuser (id, name, reg_date, datelimit, PassiveType, dept, company, phone, remark) VALUES (New.L_ID, New.C_Name, IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), IFNULL( ( CASE WHEN New.L_OptDateLimit = 0 THEN CONCAT('N', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN CONCAT('Y', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) >= CURDATE()*1 THEN CONCAT('N', New.C_DateLimit) ELSE datelimit END ), 'N2001010120200101'), IFNULL( ( CASE WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN 'RSN' ELSE 'ACT' END ), 'ACT'), (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID), New.C_Unique, (SELECT C_Remark FROM UNIS.temploye WHERE L_UID = New.L_ID)) ";
        mysqli_query($uconn, $query);
        $query = "DROP TRIGGER IF EXISTS UNIS.u_tuser ";
        mysqli_query($uconn, $query);
        $query = "CREATE TRIGGER UNIS.u_tuser AFTER UPDATE ON UNIS.tuser FOR EACH ROW UPDATE Access.tuser SET name = New.C_Name, reg_date = IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), datelimit = IFNULL( ( CASE WHEN New.L_OptDateLimit = 0 THEN CONCAT('N', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN CONCAT('Y', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) >= CURDATE()*1 THEN CONCAT('N', New.C_DateLimit) ELSE datelimit END ), 'N2001010120200101'), PassiveType = IFNULL( ( CASE WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN 'RSN' ELSE 'ACT' END ), 'ACT'), dept = (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), company = (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID), phone = New.C_Unique, remark = (SELECT C_Remark FROM UNIS.temploye WHERE L_UID = New.L_ID) WHERE id = New.L_ID ";
        mysqli_query($uconn, $query);
    }
    $text = "Version Update V.35.50.116.199";
    print "\n" . $text;
    V3551116200($conn, $iconn);
}

function V3551116200($conn, $iconn) {
    global $mac_in_version;
    $uconn = mysqli_connect("127.0.0.1", "unisuser", "unisamho", "UNIS");
    if (getRegister($mac_in_version, 7) == "7") {
        $query = "DROP TRIGGER IF EXISTS UNIS.i_tuser ";
        mysqli_query($uconn, $query);
        $query = "CREATE TRIGGER UNIS.i_tuser AFTER INSERT ON UNIS.tuser FOR EACH ROW INSERT INTO Access.tuser (id, name, reg_date, datelimit, PassiveType, dept, company, phone, remark) VALUES (New.L_ID, New.C_Name, IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), IFNULL( ( CASE WHEN New.L_OptDateLimit = 0 THEN CONCAT('N', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN CONCAT('Y', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) >= CURDATE()*1 THEN CONCAT('N', New.C_DateLimit) ELSE datelimit END ), 'N2001010120200101'), IFNULL( ( CASE WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN 'RSN' ELSE 'ACT' END ), 'ACT'), (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID), New.C_Unique, New.C_UserMessage) ";
        mysqli_query($uconn, $query);
        $query = "DROP TRIGGER IF EXISTS UNIS.u_tuser ";
        mysqli_query($uconn, $query);
        $query = "CREATE TRIGGER UNIS.u_tuser AFTER UPDATE ON UNIS.tuser FOR EACH ROW UPDATE Access.tuser SET name = New.C_Name, reg_date = IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), datelimit = IFNULL( ( CASE WHEN New.L_OptDateLimit = 0 THEN CONCAT('N', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN CONCAT('Y', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) >= CURDATE()*1 THEN CONCAT('N', New.C_DateLimit) ELSE datelimit END ), 'N2001010120200101'), PassiveType = IFNULL( ( CASE WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN 'RSN' ELSE 'ACT' END ), 'ACT'), dept = (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), company = (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID), phone = New.C_Unique, remark = New.C_UserMessage WHERE id = New.L_ID ";
        mysqli_query($uconn, $query);
    }
    V3552116202($conn, $iconn);
}

function V3552116202($conn, $iconn) {
    $this_conn = mysqli_connect("localhost", "root", "namaste", "Access");
    if ($this_conn == "") {
        $this_conn = mysqli_connect("localhost", "root", "root", "Access");
        if ($this_conn == "") {
            $this_conn = mysqli_connect("localhost", "unis", "namaste", "Access");
        }
    }
    $query = "CREATE USER 'shoot'@'%' IDENTIFIED BY 'salaam'";
    safeUpdate($this_conn, $query, true);
    $query = "CREATE USER 'shoot'@'localhost' IDENTIFIED BY 'salaam'";
    safeUpdate($this_conn, $query, true);
    $query = "GRANT ALL PRIVILEGES ON *.* TO 'shoot'@'%'";
    safeUpdate($this_conn, $query, true);
    $query = "GRANT ALL PRIVILEGES ON *.* TO 'shoot'@'localhost'";
    safeUpdate($this_conn, $query, true);
    $query = "SET PASSWORD FOR 'root'@'localhost' = PASSWORD( 'namaste' )";
    safeUpdate($this_conn, $query, true);
    $query = "SET PASSWORD FOR 'root'@'%' = PASSWORD( 'namaste' )";
    safeUpdate($this_conn, $query, true);
    if ($this_conn != "") {
        $query = "CREATE DATABASE IF NOT EXISTS AccessArchive";
        safeUpdateIData($this_conn, $query, true);
        $query = "CREATE  TABLE  AccessArchive.archive_tenter (  e_date varchar( 8  )  NOT  NULL DEFAULT  '', e_time varchar( 6  )  NOT  NULL DEFAULT  '', g_id int( 10  )  NOT  NULL DEFAULT  '0', e_id int( 10  )  NOT  NULL DEFAULT  '0', e_name varchar( 30  )  DEFAULT NULL , e_idno varchar( 30  )  DEFAULT NULL , e_group smallint( 5  )  NOT  NULL DEFAULT  '0', e_user varchar( 1  )  DEFAULT NULL , e_mode varchar( 1  )  DEFAULT NULL , e_type varchar( 1  )  DEFAULT NULL , e_result varchar( 1  )  DEFAULT NULL , e_etc varchar( 1  )  DEFAULT NULL , ed int( 10  )  NOT  NULL  DEFAULT  '0', p_flag int( 10  )  NOT  NULL DEFAULT  '0', e_uptime varchar( 14  )  DEFAULT NULL , e_upmode varchar( 1  )  DEFAULT NULL , PRIMARY  KEY (  e_date ,  e_time ,  g_id ,  e_id  ) , KEY  e_group (  e_group  ) , KEY  ed (  ed  ) , KEY  e_date (  e_date  ) , KEY  e_time (  e_time  ) , KEY  g_id (  g_id  ) , KEY  e_id (  e_id  ) , KEY  p_flag (  p_flag  ) , KEY  e_etc (  e_etc  )  ) ";
        safeUpdateIData($this_conn, $query, true);
        $query = "CREATE  TABLE  AccessArchive.archive_am (  AttendanceID int( 10  )  NOT  NULL  DEFAULT  '0' , EmployeeID int( 10  )  NOT  NULL DEFAULT  '0', EmpID varchar( 10  )  DEFAULT NULL , group_id int( 10  )  NOT  NULL DEFAULT  '0', group_min int( 10  )  NOT  NULL DEFAULT  '0', ADate int( 10  )  NOT  NULL DEFAULT  '0', Week int( 10  )  NOT  NULL DEFAULT  '0', EarlyIn int( 10  )  NOT  NULL DEFAULT  '0', LateIn int( 10  )  NOT  NULL DEFAULT  '0', Break int( 10  )  NOT  NULL DEFAULT  '0', LessBreak int( 10  )  NOT  NULL DEFAULT  '0', MoreBreak int( 10  )  NOT  NULL DEFAULT  '0', EarlyOut int( 10  )  NOT  NULL DEFAULT  '0', LateOut int( 10  )  NOT  NULL DEFAULT  '0', Normal int( 10  )  NOT  NULL DEFAULT  '0', Grace int( 10  )  NOT  NULL DEFAULT  '0', Overtime int( 10  )  NOT  NULL DEFAULT  '0', AOvertime int( 10  )  NOT  NULL DEFAULT  '0', Day varchar( 50  )  DEFAULT NULL , Flag varchar( 10  )  NOT  NULL DEFAULT  'Black', p_flag int( 11  )  NOT  NULL DEFAULT  '0', LateIn_flag int( 1  )  NOT  NULL DEFAULT  '0', EarlyOut_flag int( 1  )  NOT  NULL DEFAULT  '0', MoreBreak_flag int( 1  )  NOT  NULL DEFAULT  '0', OT1 varchar( 255  )  NOT  NULL DEFAULT  'Saturday', OT2 varchar( 255  )  NOT  NULL DEFAULT  'Sunday', NightFlag int( 11  )  NOT  NULL DEFAULT  '0', RotateFlag int( 11  )  NOT  NULL DEFAULT  '0', Remark varchar( 1024  )  NOT  NULL DEFAULT  '.', PRIMARY  KEY (  AttendanceID  ) , UNIQUE  KEY  AEA (  EmployeeID ,  ADate  ) , KEY  g_id (  group_id  ) , KEY  EmployeeID (  EmployeeID  )  ) ";
        safeUpdateIData($this_conn, $query, true);
        $query = "CREATE  TABLE  AccessArchive.archive_dm (  DayMasterID int( 10  )  NOT  NULL  DEFAULT  '0' , e_id int( 10  )  NOT  NULL DEFAULT  '0', TDate int( 10  )  NOT  NULL DEFAULT  '0', archive_dm.Entry varchar( 6  )  DEFAULT NULL , Start varchar( 6  )  DEFAULT NULL , BreakOut varchar( 6  )  DEFAULT NULL , BreakIn varchar( 6  )  DEFAULT NULL , Close varchar( 6  )  DEFAULT NULL , archive_dm.Exit varchar( 6  )  DEFAULT NULL , p_flag int( 11  )  NOT  NULL DEFAULT  '0', group_id int( 10  )  NOT  NULL DEFAULT  '0', Flag varchar( 10  )  NOT  NULL DEFAULT  'Black', Work int( 11  )  NOT  NULL DEFAULT  '0', PRIMARY  KEY (  DayMasterID  ) , UNIQUE  KEY  DET (  e_id ,  TDate  ) , KEY  p_flag (  p_flag  ) , KEY  e_id (  e_id  )  )";
        safeUpdateIData($this_conn, $query, true);
        $query = "CREATE TABLE AccessArchive.archive_trans (TransactID int( 10 ) NOT NULL AUTO_INCREMENT , Transactdate int( 10 ) NOT NULL DEFAULT '0', Transacttime int( 10 ) NOT NULL DEFAULT '0', Username varchar( 255 ) DEFAULT NULL , Transactquery varchar( 1024 ) NOT NULL , PRIMARY KEY ( TransactID ) , KEY TransactID ( TransactID ) )";
        safeUpdateIData($this_conn, $query, true);
        $query = "CREATE TABLE AccessArchive.FlagDayRotation ( FlagDayRotationID int( 11 ) NULL, e_id int( 11 ) NOT NULL DEFAULT '0', e_date int( 8 ) NOT NULL DEFAULT '0', g_id int( 11 ) NOT NULL DEFAULT '0', Flag varchar( 1024 ) NULL DEFAULT 'Black', Rotate int( 1 ) NOT NULL DEFAULT '0', RecStat int( 1 ) NOT NULL DEFAULT '0', Remark varchar( 1024 ) NULL, OT1 varchar( 10 ) NULL, OT2 varchar( 10 ) NULL, group_id int( 11 ) NOT NULL DEFAULT '0', PRIMARY KEY ( FlagDayRotationID ) , UNIQUE KEY FDRED ( e_id , e_date ) ) ";
        safeUpdateIData($this_conn, $query, true);
        $query = "CREATE TABLE AccessArchive.ShiftRoster ( ShiftRosterID int( 11 ) NULL, e_id int( 11 ) NOT NULL DEFAULT '0', e_date int( 11 ) NOT NULL DEFAULT '0', e_group int( 11 ) NOT NULL DEFAULT '0', PRIMARY KEY ( ShiftRosterID ) , UNIQUE KEY ShiftRoster ( e_id , e_date , e_group ) ) ";
        safeUpdateIData($this_conn, $query, true);
        $query = "GRANT ALL PRIVILEGES ON accessarchive.* TO 'fdmsusr'@'localhost' WITH GRANT OPTION";
        safeUpdateIData($this_conn, $query, true);
        $query = "GRANT ALL PRIVILEGES ON accessarchive.* TO 'fdmsusr'@'%' WITH GRANT OPTION";
        safeUpdateIData($this_conn, $query, true);
        $query = "ALTER TABLE AccessArchive.archive_am MODIFY `Remark` VARCHAR( 1024 ) DEFAULT NULL";
        safeUpdateIData($this_conn, $query, true);
        $query = "ALTER TABLE AccessArchive.archive_trans MODIFY `Transactquery` VARCHAR( 1024 ) DEFAULT NULL";
        safeUpdateIData($this_conn, $query, true);
        $query = "ALTER TABLE AccessArchive.archive_am ADD EarlyIn_flag INT( 1 ) NULL DEFAULT '0' ";
        safeUpdateIData($this_conn, $query, true);
        $query = "ALTER TABLE AccessArchive.FlagDayRotation ADD OTH INT NULL DEFAULT '0'";
        safeUpdateIData($this_conn, $query, true);
        $query = "ALTER TABLE AccessArchive.FlagDayRotation ADD OT VARCHAR(5) DEFAULT NULL";
        safeUpdateIData($this_conn, $query, true);
        $query = "ALTER TABLE AccessArchive.archive_am MODIFY `Remark` VARCHAR( 1024 ) DEFAULT NULL";
        safeUpdateIData($this_conn, $query, true);
        $query = "ALTER TABLE AccessArchive.archive_trans MODIFY `Transactquery` VARCHAR( 1024 ) DEFAULT NULL";
        safeUpdateIData($this_conn, $query, true);
        $query = "ALTER TABLE AccessArchive.FlagDayRotation 
            MODIFY Remark VARCHAR(1024) NULL, 
            MODIFY OT1 VARCHAR(10) NULL, 
            MODIFY OT2 VARCHAR(10) NULL, 
            MODIFY Flag VARCHAR(10) NULL";
        safeUpdateIData($this_conn, $query, true);

        $query = "ALTER TABLE AccessArchive.archive_am 
            MODIFY Flag VARCHAR(10) NULL DEFAULT 'Black', 
            MODIFY OT1 VARCHAR(255) NULL DEFAULT 'Saturday', 
            MODIFY OT2 VARCHAR(255) NULL DEFAULT 'Sunday'";
        safeUpdateIData($this_conn, $query, true);

        $query = "ALTER TABLE AccessArchive.archive_dm 
            MODIFY Flag VARCHAR(10) NULL DEFAULT 'Black'";
        safeUpdateIData($this_conn, $query, true);

        $query = "ALTER TABLE AccessArchive.archive_trans 
            MODIFY Username VARCHAR(255) NULL DEFAULT NULL, 
            MODIFY Transactquery VARCHAR(1024) NULL";
        safeUpdateIData($this_conn, $query, true);

        $query = "ALTER TABLE AccessArchive.archive_am 
            MODIFY Remark VARCHAR(1024) NULL, 
            ADD PHF INT(1) NOT NULL DEFAULT 0";
        safeUpdateIData($this_conn, $query, true);

        $query = "ALTER TABLE Access.AttendanceMaster 
            MODIFY Remark VARCHAR(1024) NULL";
        safeUpdateIData($this_conn, $query, true);
        $query = "UPDATE AccessArchive.archive_am SET AccessArchive.archive_am.PHF = 1 WHERE AccessArchive.archive_am.ADate IN (SELECT Access.OTDate.OTDate FROM Access.OTDate)";
        safeUpdateIData($this_conn, $query, true);
        $query = "ALTER TABLE accessarchive.archive_tenter ADD D_Latitude INT(10) NULL DEFAULT 0";
        safeUpdateIData($this_conn, $query, true);
        $query = "ALTER TABLE accessarchive.archive_tenter ADD D_Longitude INT(10) NULL DEFAULT 0";
        safeUpdateIData($this_conn, $query, true);
        $query = "ALTER TABLE accessarchive.archive_tenter ADD C_MobilePhone INT(10) NULL DEFAULT 0";
        safeUpdateIData($this_conn, $query, true);
        $query = "INSERT IGNORE INTO accessarchive.archive_tenter (e_date, e_time, g_id, e_id, e_name, e_idno, e_group, e_user, e_mode, e_type, e_result, e_etc, ed, p_flag, e_uptime, e_upmode, D_Latitude, D_Longitude, C_MobilePhone) (SELECT e_date, e_time, g_id, e_id, e_name, e_idno, e_group, e_user, e_mode, e_type, e_result, e_etc, ed, p_flag, e_uptime, e_upmode, D_Latitude, D_Longitude, C_MobilePhone from Access.tenter WHERE e_date < 20180101)";
        safeUpdateIData($this_conn, $query, true);
    }
    V3553117205($conn, $iconn);
}

function V3553117205($conn, $iconn) {
    global $mac_in_version;
    if (getRegister($mac_in_version, 7) == "133") {
        createTrigger($iconn, $mac_in_version, 2);
    }
    $text = "Version Update V.35.53.117.205";
    $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('" . $text . "', " . insertToday() . ", '" . getNow() . "')";
    safeUpdateIData($iconn, $query, true);
    print "\n" . $text;
    V3755120211($conn, $iconn);
}

function V3755120211($conn, $iconn) {
    global $mac_in_version;
    if (getRegister($mac_in_version, 7) == "165" || getRegister($mac_in_version, 7) == "39") {
        createTrigger($iconn, $mac_in_version, 2);
    }
    $text = "V.37.55.120.212";
    $query = "INSERT INTO Access.ProcessLog (PType, PDate, PTime) VALUES ('Version Update " . $text . "', " . insertToday() . ", '" . getNow() . "')";
    safeUpdateIData($iconn, $query, true);
    print "\n" . $text;
    V3755120213($conn, $iconn);
}

function V3755120213($conn, $iconn) { 
    global $mac_in_version;
    if (getRegister($mac_in_version, 7) == "165") {
        createTrigger($iconn, $mac_in_version, 2);
    }
    $uconn = mysqli_connect("127.0.0.1", "unisuser", "unisamho", "UNIS");
    $query = "SELECT id, name from Access.tgroup";
    $resultquery = mysqli_query($iconn, $query);
    while ($cur = mysqli_fetch_row($resultquery)) {
        $query = "INSERT INTO tworktype (C_Code, C_Name) VALUES ('" . $cur[0] . "', '" . $cur[1] . "')";
        safeUpdateIData($uconn, $query, true);
    }
    $text = "V.37.55.120.213";
    $query = "INSERT INTO Access.ProcessLog (PType, PDate, PTime) VALUES ('Version Update " . $text . "', " . insertToday() . ", '" . getNow() . "')";
    safeUpdateIData($iconn, $query, true);
    print "\n" . $text;
    V3958120221($conn, $iconn);
}

function V3958120221($conn, $iconn) { 
    global $mac_in_version;
    createTrigger($iconn, $mac_in_version, 2);
    $query = "DELETE FROM Access.CAG WHERE CAGID = 1 AND Name = '.'";
    safeUpdateIData($iconn, $query, true);
    $query = "INSERT INTO Access.CAG (CAGID, CAGDate, Name) VALUES (0, '" . insertToday() . "', '.')";
    safeUpdateIData($iconn, $query, true);
    $query = "CREATE TABLE Access.UNISMap (MapID int( 10 ) AUTO_INCREMENT, ACol VARCHAR( 255 ) NULL, UCol varchar( 255 ) NULL , UMaster varchar( 255 ) NULL , UMasterName varchar( 255 ) NULL , ECol varchar( 255 ) NULL , MMaster varchar( 255 ) NULL , EMasterName varchar( 255 ) NULL , PRIMARY KEY ( MapID ) )";
    safeUpdateIData($iconn, $query, true);
    $text = "V.39.58.120.221";
    $query = "UPDATE Access.OtherSettingMaster SET EX2 = '" . $text . "'";
    safeUpdateIData($iconn, $query, true);
    $query = "INSERT INTO Access.ProcessLog (PType, PDate, PTime) VALUES ('Version Update " . $text . "', " . insertToday() . ", '" . getNow() . "')";
    safeUpdateIData($iconn, $query, true);
    print "\n" . $text;
    V1041($conn, $iconn);
}

function V1041($conn, $iconn) { //echo "Hey";die;
    global $mac_in_version;
    createTrigger($iconn, $mac_in_version, 2);
    $text = "V.1.0.4.1";
    $query = "UPDATE Access.OtherSettingMaster SET EX2 = '" . $text . "'";
    safeUpdateIData($iconn, $query, true);
    print "\n" . $text;
    setTCount($conn);
    echo "<br>";
    echo "End script...<br>";
    V1042($conn, $iconn);
}

function V1042($conn, $iconn){
    $text = "V.1.0.4.2";
    $query = "UPDATE Access.OtherSettingMaster SET EX2 = '" . $text . "'";
    safeUpdateIData($iconn, $query, true);
    print "\n" . $text;
    echo "<br>";
    echo "End script...<br>";
    V1005($conn, $iconn);
}
function V1005($conn, $iconn){
    $text = "V.1.0.0.5";
    $query = "UPDATE Access.OtherSettingMaster SET EX2 = '" . $text . "'";
    safeUpdateIData($iconn, $query, true);
    print "\n" . $text;
    echo "<br>";
    echo "End script...<br>";
    V1006($conn, $iconn);
}
function V1006($conn, $iconn){
    $text = "V.1.0.0.6";
    $query = "UPDATE Access.OtherSettingMaster SET EX2 = '" . $text . "'";
    safeUpdateIData($iconn, $query, true);
    print "\n" . $text;
    echo "<br>";
    echo "End script...<br>";
}
?>