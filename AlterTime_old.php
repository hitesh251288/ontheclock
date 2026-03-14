<?php
ob_start("ob_gzhandler");
set_time_limit(0);
include "Functions.php";
//if (session_status() == PHP_SESSION_NONE) {
    session_start();
//}
$current_module = "17";
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
$nightShiftMaxOutTime = $_SESSION[$session_variable . "NightShiftMaxOutTime"] . "00";
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=AlterTime.php&message=Session Expired or Security Policy Violated");
    exit;
}
/**
 * Normalize a time-like input to a HHMMSS string.
 * Accepts: "19", "1900", "04:00", "040000", "815", "8:15", "231530", etc.
 * Returns: always 6 digits string like "190000", "040000", "231530"
 */
//function normalize_time_hhmmss($t) {
//    $s = preg_replace('/\D/', '', (string)$t); // strip non-digits
//    if ($s === '') return '000000';
//
//    // if more than 6 digits use first 6 (HHMMSS)
//    if (strlen($s) >= 6) return substr($s, 0, 6);
//
//    // pad/transform based on length
//    switch (strlen($s)) {
//        case 5: // e.g. "12345" -> "012345" -> we only want HHMM + SS -> drop last? safer: make "012345" and take first 6
//            $s = '0' . $s;
//            return $s;
//        case 4: // "HHMM" -> "HHMM00"
//            return $s . '00';
//        case 3: // "HMM" or "815" -> "0815" -> "081500"
//            $s = str_pad($s, 4, '0', STR_PAD_LEFT);
//            return $s . '00';
//        case 2: // "HH" -> "HH0000" -> e.g. "19" -> "190000"
//            return $s . '0000';
//        case 1: // "H" -> "0H0000"
//            return '0' . $s . '0000';
//        default:
//            return str_pad($s, 6, '0', STR_PAD_LEFT);
//    }
//}

// --- Added helpers to normalize and compare times to fix night shift handling ---
//function normalizeTimeInt($t) {
//    // Converts times like "23:15:00", "231500", "2315" into integer HHMM (e.g., 2315)
//    $s = preg_replace('/\D/', '', strval($t));
//    if ($s === '') return 0;
//    // ensure at least 4 digits (HHMM). If seconds provided (6 digits), take first 4.
//    if (strlen($s) >= 6) $s = substr($s, 0, 4);
//    if (strlen($s) == 5) $s = substr($s, 0, 4);
//    if (strlen($s) < 4) $s = str_pad($s, 4, '0', STR_PAD_LEFT);
//    return intval($s);
//}

//function compareTime($a, $op, $b) {
//    $ai = normalize_time_hhmmss($a);
//    $bi = normalize_time_hhmmss($b);
//    switch ($op) {
//        case '<': return $ai < $bi;
//        case '<=': return $ai <= $bi;
//        case '>': return $ai > $bi;
//        case '>=': return $ai >= $bi;
//        case '==': return $ai == $bi;
//        case '!=': return $ai != $bi;
//        default: return false;
//    }
//}
// Normalize night shift session values to integers (HHMM)
//if (isset($_SESSION[$session_variable . "NightShiftMaxOutTime"])) {
//    $nightShiftMaxOutTime = normalize_time_hhmmss($_SESSION[$session_variable . "NightShiftMaxOutTime"]);
//} else {
//    $nightShiftMaxOutTime = 0;
//}
$conn = openConnection();
$iconn = openIConnection();
$jconn = openIConnection();
$kconn = openIConnection();
//$act = isset($_POST['act']) ? $_POST['act'] : $_GET['act'];
$act = $_POST['act'] ?? $_GET['act'] ?? '';
$prints = $_GET['prints'] ?? null;
$excel = $_GET['excel'] ?? null;
$message = $_GET['message'] ?? "Time Alterations for Improper Clockins";

//$lstShift = $_POST['lstShift'] ?? $_GET['lstShift'];
$lstShift = $_POST['lstShift'] ?? $_GET['lstShift'] ?? '';
$lstEditShift      = $_POST["lstEditShift"] ?? $_GET["lstEditShift"] ?? '';
$lstDepartment     = $_POST['lstDepartment'] ?? $_GET['lstDepartment'] ?? '';
$lstDivision       = $_POST['lstDivision'] ?? $_GET['lstDivision'] ?? '';
$lstTerminal       = $_POST['lstTerminal'] ?? $_GET['lstTerminal'] ?? '';
$lstEditTerminal   = $_POST["lstEditTerminal"] ?? $_GET["lstEditTerminal"] ?? '';
$txtSNo            = $_POST['txtSNo'] ?? $_GET['txtSNo'] ?? '';
$txtRemark         = $_POST['txtRemark'] ?? $_GET['txtRemark'] ?? '';
$txtPhone          = $_POST["txtPhone"] ?? $_GET["txtPhone"] ?? '';
$lstSort           = $_POST["lstSort"] ?? $_GET["lstSort"] ?? '';
$txtF1             = $_POST["txtF1"] ?? $_GET["txtF1"] ?? '';
$txtF2             = $_POST["txtF2"] ?? $_GET["txtF2"] ?? '';
$txtF3             = $_POST["txtF3"] ?? $_GET["txtF3"] ?? '';
$txtF4             = $_POST["txtF4"] ?? $_GET["txtF4"] ?? '';
$txtF5             = $_POST["txtF5"] ?? $_GET["txtF5"] ?? '';
$txtF6             = $_POST["txtF6"] ?? $_GET["txtF6"] ?? '';
$txtF7             = $_POST["txtF7"] ?? $_GET["txtF7"] ?? '';
$txtF8             = $_POST["txtF8"] ?? $_GET["txtF8"] ?? '';
$txtF9             = $_POST["txtF9"] ?? $_GET["txtF9"] ?? '';
$txtF10            = $_POST["txtF10"] ?? $_GET["txtF10"] ?? '';
$lstEmployeeID     = $_POST['lstEmployeeID'] ?? $_GET['lstEmployeeID'] ?? '';
$lstEmployeeIDFrom = $_POST["lstEmployeeIDFrom"] ?? $_GET["lstEmployeeIDFrom"] ?? '';
$lstEmployeeIDTo   = $_POST["lstEmployeeIDTo"] ?? $_GET["lstEmployeeIDTo"] ?? '';
$lstEmployee       = $_POST["lstEmployee"] ?? $_GET["lstEmployee"] ?? '';
$txtEmployee       = $_POST["txtEmployee"] ?? $_GET["txtEmployee"] ?? '';
$txtEmployeeCode   = $_POST["txtEmployeeCode"] ?? $_GET["txtEmployeeCode"] ?? '';
$txtFrom           = $_POST["txtFrom"] ?? $_GET["txtFrom"] ?? '';
$txtTo             = $_POST["txtTo"] ?? $_GET["txtTo"] ?? '';
$lstEmployeeName = '';
$lstGroup = $_POST['lstGroup'] ?? $_GET['lstGroup'] ?? '';
if ($txtFrom == "") {
    if (substr(insertToday(), 6, 2) == "01") {
        if (substr(insertToday(), 4, 2) == "01") {
            $txtFrom = "01/12/" . (substr(insertToday(), 0, 4) - 1);
        } else {
            if (substr(insertToday(), 4, 2) - 1 < 10) {
                $txtFrom = "01/0" . (substr(insertToday(), 4, 2) - 1) . "/" . substr(insertToday(), 0, 4);
            } else {
                $txtFrom = "01/" . (substr(insertToday(), 4, 2) - 1) . "/" . substr(insertToday(), 0, 4);
            }
        }
    } else {
        $txtFrom = "01/" . substr(displayToday(), 3, 7);
    }
}
if ($txtTo == "") {
    $txtTo = displayDate(getLastDay(insertToday(), 1));
}
$lstEmployeeStatus = $_POST["lstEmployeeStatus"] ?? $_GET["lstEmployeeStatus"] ?? "ACT";

$txtPhone    = $_POST["txtPhone"] ?? $_GET["txtPhone"] ?? '';
$txtRemark   = $_POST["txtRemark"] ?? $_GET["txtRemark"] ?? '';
$txtSNo      = $_POST["txtSNo"] ?? $_GET["txtSNo"] ?? '';
$lstSort     = $_POST["lstSort"] ?? $_GET["lstSort"] ?? '';

if ($act == "deleteRecord") {
    $ed = $_GET["lstED"] / 1024;
    $query = "UPDATE tenter SET p_flag = 1, e_etc = 'D' WHERE ed = " . $ed;
    updateIData($iconn, $query, true);
    $query = "SELECT e_date, e_time, g_id, e_group FROM tenter WHERE ed = " . $ed;
    $result = selectData($conn, $query);
    $query = "INSERT INTO AlterLog (Username, ed, DateFrom, TimeFrom, GateFrom, TransactDate, ShiftFrom) VALUES ('" . $username . "', " . $ed . ", '" . $result[0] . "', '" . $result[1] . "', " . $result[2] . ", " . insertToday() . ", " . $result[3] . ")";
    updateIData($jconn, $query, true);
    header("Location: AlterTime.php?act=searchRecord&lstShift=" . $lstShift . "&lstDepartment=" . $lstDepartment . "&lstDivision=" . $lstDivision . "&lstEmployeeID=" . $lstEmployeeID . "&lstEmployeeIDFrom=" . $lstEmployeeIDFrom . "&lstEmployeeIDTo=" . $lstEmployeeIDTo . "&lstTerminal=" . $lstTerminal . "&txtEmployee=" . $txtEmployee . "&txtFrom=" . $txtFrom . "&txtTo=" . $txtTo . "&txtEmployeeCode=" . $txtEmployeeCode . "&lstEmployeeStatus=" . $lstEmployeeStatus . "&txtRemark=" . $txtRemark . "&txtPhone=" . $txtPhone . "&txtSNo=" . $txtSNo . "&lstSort=" . $lstSort . "&message=Record Deleted");
} else {
    if ($act == "deleteSelected") {
        $count = $_POST["txtCount"];
        for ($i = 0; $i < $count; $i++) {
            if ($_POST["chk" . $i] == "on") {
                $ed = $_POST["txh" . $i];
                $query = "UPDATE tenter SET p_flag = 1, e_etc = 'D' WHERE ed = " . $ed;
                updateIData($iconn, $query, true);
                $query = "SELECT e_date, e_time, g_id, e_group FROM tenter WHERE ed = " . $ed;
                $result = selectData($conn, $query);
                $query = "INSERT INTO AlterLog (Username, ed, DateFrom, TimeFrom, GateFrom, TransactDate, ShiftFrom) VALUES ('" . $username . "', " . $ed . ", '" . $result[0] . "', '" . $result[1] . "', " . $result[2] . ", " . insertToday() . ", " . $result[3] . ")";
                updateIData($jconn, $query, true);
            }
        }
        header("Location: AlterTime.php?act=searchRecord&lstShift=" . $lstShift . "&lstDepartment=" . $lstDepartment . "&lstDivision=" . $lstDivision . "&lstEmployeeID=" . $lstEmployeeID . "&txtEmployee=" . $txtEmployee . "&lstEmployeeIDFrom=" . $lstEmployeeIDFrom . "&lstEmployeeIDTo=" . $lstEmployeeIDTo . "&lstTerminal=" . $lstTerminal . "&txtFrom=" . $txtFrom . "&txtTo=" . $txtTo . "&txtEmployeeCode=" . $txtEmployeeCode . "&lstEmployeeStatus=" . $lstEmployeeStatus . "&txtRemark=" . $txtRemark . "&txtPhone=" . $txtPhone . "&txtSNo=" . $txtSNo . "&lstSort=" . $lstSort . "&message=Record(s) Deleted");
    } else {
        if ($act == "saveChanges") {
            $count = $_POST["txtCount"];
            for ($i = 0; $i < $count; $i++) {
                if (insertDate($_POST["txtDate" . $i]) != $_POST["txhDate" . $i] || $_POST["txtTime" . $i] != $_POST["txhTime" . $i] || $lstEditShift != "" && $_POST["chk" . $i] == "on" || $lstEditTerminal != "" && $_POST["chk" . $i] == "on") {
                    $ed = $_POST["txh" . $i];
                    $query = "SELECT e_date, e_time, g_id, e_id, e_name, e_idno, e_group, e_user, e_mode, e_type, e_result, e_etc, p_flag, e_uptime, e_upmode FROM tenter WHERE ed = " . $ed;
                    $result = selectData($conn, $query);
                    if ($lstEditShift != "" && $_POST["chk" . $i] == "on") {
                        $query = "UPDATE tenter SET e_group = '" . $lstEditShift . "', e_etc = 'P' WHERE ed = " . $ed;
                        if (updateIData($iconn, $query, true)) {
                            $query = "INSERT INTO AlterLog (Username, ed, ShiftFrom, ShiftTo, TransactDate) VALUES ('" . $username . "', " . $_POST["txh" . $i] . ", " . $_POST["txhShift" . $i] . ", " . $lstEditShift . ", " . insertToday() . ")";
                            updateIData($jconn, $query, true);
                        }
                    } else {
                        if ($lstEditTerminal != "" && $_POST["chk" . $i] == "on") {
                            $query = "UPDATE tenter SET g_id = '" . $lstEditTerminal . "', e_etc = 'P' WHERE ed = " . $ed;
                            if (updateIData($iconn, $query, true)) {
                                $query = "INSERT INTO AlterLog (Username, ed, GateFrom, GateTo, TransactDate) VALUES ('" . $username . "', " . $ed . ", " . $_POST["txhTerminal" . $i] . ", " . $lstEditTerminal . ", " . insertToday() . ")";
                                updateIData($jconn, $query, true);
                            }
                        } else {
                            $query = "UPDATE tenter SET p_flag = 1, e_etc = 'D' WHERE ed = " . $ed;
                            if (updateIData($iconn, $query, true)) {
                                //$query = "INSERT INTO tenter (e_date, e_time, g_id, e_id, e_name, e_idno, e_group, e_user, e_mode, e_type, e_result, e_etc, p_flag, e_uptime, e_upmode) VALUES ('" . insertDate($_POST["txtDate" . $i]) . "', '" . $_POST["txtTime" . $i] . "', '" . $result[2] . "', '" . $result[3] . "', '" . $result[4] . "', '" . $result[5] . "', '" . $result[6] . "', '" . $result[7] . "', '" . $result[8] . "', '" . $result[9] . "', '" . $result[10] . "', 'P', 0, '" . $result[13] . "', '" . $result[14] . "')";
                                $e_date   = insertDate($_POST["txtDate".$i]);
//                                $e_time   = $_POST["txtTime".$i] ?? '000000';
                                $g_id     = isset($result[2]) ? (int)$result[2] : 0;
                                $e_id     = isset($result[3]) ? (int)$result[3] : 0;
                                $e_name   = !empty($result[4]) ? $result[4] : '';
                                $e_idno   = !empty($result[5]) ? $result[5] : '';
                                $e_group  = isset($result[6]) ? (int)$result[6] : 0;
                                $e_user   = isset($result[7]) ? (int)$result[7] : 0;
                                $e_mode   = isset($result[8]) ? (int)$result[8] : 0;
                                $e_type   = isset($result[9]) ? (int)$result[9] : 0;
                                $e_result = isset($result[10]) ? (int)$result[10] : 0;
                                $e_etc    = 'P';
                                $p_flag   = 0;
                                $e_uptime = !empty($result[13]) ? $result[13] : '';
                                $e_upmode = !empty($result[14]) ? $result[14] : '';
                                $query = "
                                        INSERT INTO tenter 
                                            (e_date, e_time, g_id, e_id, e_name, e_idno, e_group, e_user, e_mode, e_type, e_result, e_etc, p_flag, e_uptime, e_upmode)
                                        VALUES
                                            ('$e_date', '" . $_POST["txtTime" . $i] . "', $g_id, $e_id, '$e_name', '$e_idno', $e_group, $e_user, $e_mode, $e_type, $e_result, '$e_etc', $p_flag, '$e_uptime', '$e_upmode')
                                        ON DUPLICATE KEY UPDATE
                                            g_id      = VALUES(g_id),
                                            e_name    = VALUES(e_name),
                                            e_idno    = VALUES(e_idno),
                                            e_group   = VALUES(e_group),
                                            e_user    = VALUES(e_user),
                                            e_mode    = VALUES(e_mode),
                                            e_type    = VALUES(e_type),
                                            e_result  = VALUES(e_result),
                                            e_etc     = VALUES(e_etc),
                                            p_flag    = VALUES(p_flag),
                                            e_uptime  = VALUES(e_uptime),
                                            e_upmode  = VALUES(e_upmode)
                                        ";
                                updateIData($jconn, $query, true);
                            }
                            $query = "SELECT ed FROM tenter WHERE e_date = '" . insertDate($_POST["txtDate" . $i]) . "' AND e_time = '" . $_POST["txtTime" . $i] . "' AND e_id = '" . $result[3] . "' AND e_etc = 'P' AND p_flag = 0";
                            $sub_result = selectData($conn, $query);
                            $ed = isset($sub_result[0]) && $sub_result[0] !== "" ? (int)$sub_result[0] : 0;
                            $query = "INSERT INTO AlterLog (Username, ed, DateFrom, TimeFrom, DateTo, TimeTo, TransactDate) VALUES ('" . $username . "', " . $ed . ", '" . $_POST["txhDate" . $i] . "', '" . $_POST["txhTime" . $i] . "', '" . insertDate($_POST["txtDate" . $i]) . "', '" . $_POST["txtTime" . $i] . "', " . insertToday() . ")";
                            updateIData($kconn, $query, true);
                        }
                    }
                }
            }
            header("Location: AlterTime.php?act=searchRecord&lstShift=" . $lstShift . "&lstDepartment=" . $lstDepartment . "&lstDivision=" . $lstDivision . "&lstEmployeeID=" . $lstEmployeeID . "&lstEmployeeIDFrom=" . $lstEmployeeIDFrom . "&lstEmployeeIDTo=" . $lstEmployeeIDTo . "&lstTerminal=" . $lstTerminal . "&txtEmployee=" . $txtEmployee . "&txtFrom=" . $txtFrom . "&txtTo=" . $txtTo . "&txtEmployeeCode=" . $txtEmployeeCode . "&lstEmployeeStatus=" . $lstEmployeeStatus . "&txtRemark=" . $txtRemark . "&txtPhone=" . $txtPhone . "&txtSNo=" . $txtSNo . "&lstSort=" . $lstSort . "&message=Records Updated");
        } else {
            if ($act == "fixSingleClocking") {
                $sub_date = "";
                $sub_id = "";
                $sub_time = "";
                $sub_gate = "";
                $counter = 0;
                $sub_start = "";
                $sub_work_min = 0;
                $sub_night_flag = "";
                $sub_start = "";
                $sub_close = "";
                $sub_group = "";
                $sub_schedule = "";
                $sub_gate_name = "";
                $sub_bfrom = "";
                $sub_bto = "";
                $sub_query = "SELECT tenter.e_date, tenter.e_time, tenter.g_id, tenter.e_id, tgroup.Start, tgroup.Close, tgroup.WorkMin, tgroup.NightFlag, tenter.e_group, tgroup.ScheduleID , tgate.name, tgroup.BreakFrom, tgroup.BreakTo FROM tenter, tuser, tgroup, tgate WHERE tenter.e_id = tuser.id AND tenter.e_group = tgroup.id AND tenter.g_id = tgate.id AND tgate.exit = 0 AND tgroup.ShiftTypeID = 1 AND tgroup.NightFlag = 0 AND tenter.p_flag = 0 AND tenter.e_date >= '" . insertDate($txtFrom) . "' AND tenter.e_date <= '" . insertDate($txtTo) . "' AND tenter.e_date < '" . insertToday() . "'";
                if ($lstShift != "") {
                    $sub_query = $sub_query . " AND tenter.e_group = " . $lstShift;
                }
                $sub_query = displayQueryFields($sub_query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
                if ($lstTerminal != "") {
                    $sub_query = $sub_query . " AND tenter.g_id = " . $lstTerminal;
                }
                $sub_query = $sub_query . employeeStatusQuery($lstEmployeeStatus);
                $sub_query = $sub_query . " ORDER BY tenter.e_id, tenter.e_date, tenter.e_time";
                for ($sub_result = mysqli_query($conn, $sub_query); $sub_cur = mysqli_fetch_row($sub_result); $counter++) {
                    if ($counter == 1 && ($sub_date != $sub_cur[0] || $sub_id != $sub_cur[3])) {
                        $halfTime = getLateTime($sub_date, $sub_start, $sub_work_min / 2);
                        if ($sub_night_flag == 0 && $halfTime <= $sub_time) {
                            if ($sub_schedule == 7 && strpos($sub_gate_name, "(OUT)") != false) {
                                $gate_query = "SELECT id FROM tgate WHERE name LIKE '%(IN)%' ";
                                $gate_result = selectData($conn, $gate_query);
                                if (0 < $gate_result[0]) {
                                    $sub_gate = $gate_result[0];
                                }
                            }
                            $query = "UPDATE tenter SET p_flag = 0, e_etc = 'P' WHERE p_flag = '1' AND e_date = '" . $sub_date . "' AND e_time = '" . $sub_start . "00' AND g_id = " . $sub_gate . " AND e_id =  " . $sub_id;
                            updateIData($iconn, $query, true);
//                            $query = "INSERT INTO tenter (e_date, e_time, g_id, e_id, e_group, e_user, e_mode, e_type, e_result, e_etc) VALUES ('" . $sub_date . "', '" . $sub_start . "00', " . $sub_gate . ", " . $sub_id . ", " . $sub_group . ", '0', '3', '3', '0', 'P')";
                            $query = "
                                    INSERT INTO tenter 
                                        (e_date, e_time, g_id, e_id, e_group, e_user, e_mode, e_type, e_result, e_etc)
                                    VALUES 
                                        ('$enter_date', '" . $enter_time . "', $sub_gate, $sub_id, $sub_group, '0', '3', '3', '0', 'P')
                                    ON DUPLICATE KEY UPDATE
                                        e_group = VALUES(e_group),
                                        e_user  = VALUES(e_user),
                                        e_mode  = VALUES(e_mode),
                                        e_type  = VALUES(e_type),
                                        e_result= VALUES(e_result),
                                        e_etc   = VALUES(e_etc)
                                    ";
                            updateIData($jconn, $query, true);
                            $query = "SELECT ed FROM tenter WHERE e_date = '" . $sub_date . "' AND e_time = '" . $sub_start . "00' AND g_id = " . $sub_gate . " AND e_id = " . $sub_id . " AND e_group = " . $sub_group;
                            $max = selectData($conn, $query);
                            $ed = isset($max[0]) ? (int)$max[0] : 0; 
                            $query = "INSERT INTO AlterLog (Username, ed, DateTo, TimeTo, GateTo, TransactDate, ShiftTo) VALUES ('" . $username . "', " . $ed . ", '" . $sub_date . "', '" . $sub_start . "00', " . $sub_gate . ", " . insertToday() . ", " . $sub_group . ")";
                            updateIData($kconn, $query, true);
//                            echo "DEBUG → sub_start: [" . $sub_start . "] sub_time: [" . $sub_time . "] HHMM: [" . substr($sub_time, 0, 4) . "]<br>";
                            $timeHHMM = substr($sub_time, 0, 4); // e.g. 1800
                            if ($timeHHMM == $sub_close) {
                                // Only OUT punch present → add missing IN
                                $punchType = "IN";
                                $punchTime = $sub_start; // 0900
                            }elseif ($timeHHMM == $sub_start) {
                                // Only IN punch present → add missing OUT
                                $punchType = "OUT";
                                $punchTime = $sub_close; // 1800
                            }
//                            if (abs($timeHHMM - $sub_start) <= 30) {
//                                // punch close to IN time → missing OUT
//                                $punchType = "OUT";
//                                $punchTime = $sub_close;
//                            }elseif (abs($timeHHMM - $sub_close) <= 30) {
//                                // punch close to OUT time → missing IN
//                                $punchType = "IN";
//                                $punchTime = $sub_start;
//                            }
//                            echo "DEBUG → punchType=$punchType punchTime=$punchTime<br>";die;
                            $text = "Fixed Single Clocking → Added punch for EmployeeID: " . $sub_id .
                                    " on Date: " . displayDate($sub_date) .
                                    " at Time: " . $punchTime . "00" .
                                    ", GateID: " . $sub_gate .
                                    ", ShiftID: " . $sub_group;

                            $logQuery = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) 
                                         VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . mysqli_real_escape_string($conn, $text) . "')";
                            updateIData($conn, $logQuery, true);
                            
                        } else {
                            if ($sub_night_flag == 0 && $sub_time < $halfTime) {
                                if ($sub_schedule == 7 && stripos($sub_gate_name, "(IN)") != false) {
                                    $gate_query = "SELECT id FROM tgate WHERE name LIKE '%(OUT)%' ";
                                    $gate_result = selectData($conn, $gate_query);
                                    if (0 < $gate_result[0]) {
                                        $sub_gate = $gate_result[0];
                                    }
                                }
                                $query = "UPDATE tenter SET p_flag = 0, e_etc = 'P' WHERE p_flag = '1' AND e_date = '" . $sub_date . "' AND e_time = '" . $sub_close . "00' AND g_id = " . $sub_gate . " AND e_id =  " . $sub_id;
                                updateIData($iconn, $query, true);
                                $query = "INSERT INTO tenter (e_date, e_time, g_id, e_id, e_group, e_user, e_mode, e_type, e_result, e_etc) VALUES ('" . $sub_date . "', '" . $sub_close . "00', " . $sub_gate . ", " . $sub_id . ", " . $sub_group . ", '0', '3', '3', '0', 'P')";
                                updateIData($jconn, $query, true);
                                $query = "SELECT ed FROM tenter WHERE e_date = '" . $sub_date . "' AND e_time = '" . $sub_close . "00' AND g_id = " . $sub_gate . " AND e_id = " . $sub_id . " AND e_group = " . $sub_group;
                                $max = selectData($conn, $query);
                                $ed = isset($max[0]) && $max[0] !== "" ? (int)$max[0] : 0;
                                $query = "INSERT INTO AlterLog (Username, ed, DateTo, TimeTo, GateTo, TransactDate, ShiftTo) VALUES ('" . $username . "', " . $ed . ", '" . $sub_date . "', '" . $sub_close . "00', " . $sub_gate . ", " . insertToday() . ", " . $sub_group . ")";
                                updateIData($kconn, $query, true);
                                $timeHHMM = substr($sub_time, 0, 4); // e.g. 1800
                                if ($timeHHMM == $sub_close) {
                                    // Only OUT punch present → add missing IN
                                    $punchType = "IN";
                                    $punchTime = $sub_start; // 0900
                                }elseif ($timeHHMM == $sub_start) {
                                    // Only IN punch present → add missing OUT
                                    $punchType = "OUT";
                                    $punchTime = $sub_close; // 1800
                                }
                                $text = "Fixed Single Clocking → Added punch for EmployeeID: " . $sub_id .
                                    " on Date: " . displayDate($sub_date) .
                                    " at Time: " . $punchTime . "00" .
                                    ", GateID: " . $sub_gate .
                                    ", ShiftID: " . $sub_group;

                                $logQuery = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) 
                                             VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . mysqli_real_escape_string($conn, $text) . "')";
                                updateIData($conn, $logQuery, true);
                                
                            }
                        }
                        $counter = 0;
                    } else {
                        if ($sub_date != $sub_cur[0] || $sub_id != $sub_cur[3]) {
                            $counter = 0;
                        }
                    }
                    $sub_date = $sub_cur[0];
                    $sub_time = $sub_cur[1];
                    $sub_gate = $sub_cur[2];
                    $sub_id = $sub_cur[3];
                    $sub_start = $sub_cur[4];
                    $sub_close = $sub_cur[5];
                    $sub_work_min = $sub_cur[6];
                    $sub_night_flag = $sub_cur[7];
                    $sub_group = $sub_cur[8];
                    $sub_schedule = $sub_cur[9];
                    $sub_gate_name = $sub_cur[10];
                    $sub_bfrom = $sub_cur[11];
                    $sub_bto = $sub_cur[12];
                }
                if ($counter == 1) {
                    $halfTime = getLateTime($sub_date, $sub_start, $sub_work_min / 2);
                    if ($sub_night_flag == 0 && $halfTime <= $sub_time || $sub_night_flag == 1 && $sub_time <= $nightShiftMaxOutTime) {
                        if ($sub_night_flag == 1) {
                            $sub_date = getLastDay($sub_date, 1);
                        }
                        if ($sub_schedule == 7 && stripos($sub_gate_name, "(OUT)") != false) {
                            $gate_query = "SELECT id FROM tgate WHERE name LIKE '%(IN)%' ";
                            $gate_result = selectData($conn, $gate_query);
                            if (0 < $gate_result[0]) {
                                $sub_gate = $gate_result[0];
                            }
                        }
                        $query = "UPDATE tenter SET p_flag = 0, e_etc = 'P' WHERE p_flag = '1' AND e_date = '" . $sub_date . "' AND e_time = '" . $sub_start . "00' AND g_id = " . $sub_gate . " AND e_id =  " . $sub_id;
                        updateIData($iconn, $query, true);
                        $query = "INSERT INTO tenter (e_date, e_time, g_id, e_id, e_group, e_user, e_mode, e_type, e_result, e_etc) VALUES ('" . $sub_date . "', '" . $sub_start . "00', " . $sub_gate . ", " . $sub_id . ", " . $sub_group . ", '0', '3', '3', '0', 'P')";
                        updateIData($jconn, $query, true);
                        $query = "SELECT ed FROM tenter WHERE e_date = '" . $sub_date . "' AND e_time = '" . $sub_start . "00' AND g_id = " . $sub_gate . " AND e_id = " . $sub_id . " AND e_group = " . $sub_group;
                        $max = selectData($conn, $query);
                        $query = "INSERT INTO AlterLog (Username, ed, DateTo, TimeTo, GateTo, TransactDate, ShiftTo) VALUES ('" . $username . "', " . $max[0] . ", '" . $sub_date . "', '" . $sub_start . "00', " . $sub_gate . ", " . insertToday() . ", " . $sub_group . ")";
                        updateIData($kconn, $query, true);
                        $timeHHMM = substr($sub_time, 0, 4); // e.g. 1800
                        if ($timeHHMM == $sub_close) {
                            // Only OUT punch present → add missing IN
                            $punchType = "IN";
                            $punchTime = $sub_start; // 0900
                        }elseif ($timeHHMM == $sub_start) {
                            // Only IN punch present → add missing OUT
                            $punchType = "OUT";
                            $punchTime = $sub_close; // 1800
                        }
                        $text = "Fixed Single Clocking → Added punch for EmployeeID: " . $sub_id .
                                    " on Date: " . displayDate($sub_date) .
                                    " at Time: " . $punchTime . "00" .
                                    ", GateID: " . $sub_gate .
                                    ", ShiftID: " . $sub_group;

                        $logQuery = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) 
                                     VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . mysqli_real_escape_string($conn, $text) . "')";
                        updateIData($conn, $logQuery, true);
                        
                    } else {
                        if (($sub_night_flag == 0 && $sub_time < $halfTime || $sub_night_flag == 1 && $nightShiftMaxOutTime < $sub_time) && $sub_date < insertToday()) {
                            if ($sub_night_flag == 1) {
                                $sub_date = getNextDay($sub_date, 1);
                            }
                            if ($sub_schedule == 7 && stripos($sub_gate_name, "(IN)") != false) {
                                $gate_query = "SELECT id FROM tgate WHERE name LIKE '%(OUT)%' ";
                                $gate_result = selectData($conn, $gate_query);
                                if (0 < $gate_result[0]) {
                                    $sub_gate = $gate_result[0];
                                }
                            }
                            $query = "UPDATE tenter SET p_flag = 0, e_etc = 'P' WHERE p_flag = '1' AND e_date = '" . $sub_date . "' AND e_time = '" . $sub_close . "00' AND g_id = " . $sub_gate . " AND e_id =  " . $sub_id;
                            updateIData($iconn, $query, true);
                            $query = "INSERT INTO tenter (e_date, e_time, g_id, e_id, e_group, e_user, e_mode, e_type, e_result, e_etc) VALUES ('" . $sub_date . "', '" . $sub_close . "00', " . $sub_gate . ", " . $sub_id . ", " . $sub_group . ", '0', '3', '3', '0', 'P')";
                            updateIData($jconn, $query, true);
                            $query = "SELECT ed FROM tenter WHERE e_date = '" . $sub_date . "' AND e_time = '" . $sub_close . "00' AND g_id = " . $sub_gate . " AND e_id = " . $sub_id . " AND e_group = " . $sub_group;
                            $max = selectData($conn, $query);
                            $query = "INSERT INTO AlterLog (Username, ed, DateTo, TimeTo, GateTo, TransactDate, ShiftTo) VALUES ('" . $username . "', " . $max[0] . ", '" . $sub_date . "', '" . $sub_close . "00', " . $sub_gate . ", " . insertToday() . ", " . $sub_group . ")";
                            updateIData($kconn, $query, true);
                            $timeHHMM = substr($sub_time, 0, 4); // e.g. 1800
                            if ($timeHHMM == $sub_close) {
                                // Only OUT punch present → add missing IN
                                $punchType = "IN";
                                $punchTime = $sub_start; // 0900
                            }elseif ($timeHHMM == $sub_start) {
                                // Only IN punch present → add missing OUT
                                $punchType = "OUT";
                                $punchTime = $sub_close; // 1800
                            }
                            $text = "Fixed Single Clocking → Added punch for EmployeeID: " . $sub_id .
                                    " on Date: " . displayDate($sub_date) .
                                    " at Time: " . $punchTime . "00" .
                                    ", GateID: " . $sub_gate .
                                    ", ShiftID: " . $sub_group;

                            $logQuery = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) 
                                         VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . mysqli_real_escape_string($conn, $text) . "')";
                            updateIData($conn, $logQuery, true);
                            
                        }
                    }
                }
                $counter = 0;
                $sub_query = "SELECT tenter.e_date, tenter.e_time, tenter.g_id, tenter.e_id, tgroup.Start, tgroup.Close, tgroup.WorkMin, tgroup.NightFlag, tenter.e_group, tgroup.ScheduleID, tgate.name, tgroup.BreakFrom, tgroup.BreakTo FROM tenter, tuser, tgroup, tgate WHERE tenter.e_id = tuser.id AND tenter.e_group = tgroup.id AND tenter.g_id = tgate.id AND tgate.exit = 0 AND tgroup.ShiftTypeID = 1 AND tgroup.NightFlag = 1 AND tenter.p_flag = 0 AND tenter.e_date > '" . insertDate($txtFrom) . "' AND tenter.e_date <= '" . insertDate($txtTo) . "' AND tenter.e_date < '" . insertToday() . "'";
                if ($lstShift != "") {
                    $sub_query = $sub_query . " AND tenter.e_group = " . $lstShift;
                }
                $sub_query = displayQueryFields($sub_query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
                if ($lstTerminal != "") {
                    $sub_query = $sub_query . " AND tenter.g_id = " . $lstTerminal;
                }
                $sub_query = $sub_query . employeeStatusQuery($lstEmployeeStatus);
                $sub_query = $sub_query . " ORDER BY tenter.e_id, tenter.e_date, tenter.e_time";
                $enter_date = 0;
                $enter_time = 0;
                $enter_flag = false;
                for ($sub_result = mysqli_query($conn, $sub_query); $sub_cur = mysqli_fetch_row($sub_result); $counter++) {
                    if ($counter == 1 || $sub_cur[3] != $sub_id || $nightShiftMaxOutTime < $sub_time && getLastDay($sub_cur[0], 1) == $sub_date && $nightShiftMaxOutTime < $sub_cur[1] || $nightShiftMaxOutTime < $sub_time && getLastDay($sub_cur[0], 1) != $sub_date && $sub_date != $sub_cur[0]) {
                        if ($sub_cur[3] != $sub_id) {
                            if ($sub_time <= $nightShiftMaxOutTime) {
                                $enter_date = getLastDay($sub_date, 1);
                                $enter_time = $sub_start;
                                if ($sub_schedule == 7 && stripos($sub_gate_name, "(OUT)") != false) {
                                    $gate_query = "SELECT id FROM tgate WHERE name LIKE '%(IN)%' ";
                                    $gate_result = selectData($iconn, $gate_query);
                                    if (0 < $gate_result[0]) {
                                        $sub_gate = $gate_result[0];
                                    }
                                }
                            } else {
                                $enter_date = getNextDay($sub_date, 1);
                                $enter_time = $sub_close;
                                if ($sub_schedule == 7 && stripos($sub_gate_name, "(IN)") != false) {
                                    $gate_query = "SELECT id FROM tgate WHERE name LIKE '%(OUT)%' ";
                                    $gate_result = selectData($iconn, $gate_query);
                                    if (0 < $gate_result[0]) {
                                        $sub_gate = $gate_result[0];
                                    }
                                }
                            }
                            $enter_flag = true;
                        } else {
                            if ($nightShiftMaxOutTime < $sub_time && getLastDay($sub_cur[0], 1) == $sub_date && $nightShiftMaxOutTime < $sub_cur[1]) {
                                $enter_date = $sub_cur[0];
                                $enter_time = $sub_close;
                                if ($sub_schedule == 7 && stripos($sub_gate_name, "(IN)") != false) {
                                    $gate_query = "SELECT id FROM tgate WHERE name LIKE '%(OUT)%' ";
                                    $gate_result = selectData($iconn, $gate_query);
                                    if (0 < $gate_result[0]) {
                                        $sub_gate = $gate_result[0];
                                    }
                                }
                                $enter_flag = true;
                            } else {
                                if ($nightShiftMaxOutTime < $sub_time && getLastDay($sub_cur[0], 1) != $sub_date && $sub_date != $sub_cur[0]) {
                                    $enter_date = getNextDay($sub_date, 1);
                                    $enter_time = $sub_close;
                                    if ($sub_schedule == 7 && stripos($sub_gate_name, "(IN)") != false) {
                                        $gate_query = "SELECT id FROM tgate WHERE name LIKE '%(OUT)%' ";
                                        $gate_result = selectData($iconn, $gate_query);
                                        if (0 < $gate_result[0]) {
                                            $sub_gate = $gate_result[0];
                                        }
                                    }
                                    $enter_flag = true;
                                } else {
                                    if ($sub_time <= $nightShiftMaxOutTime) {
                                        $enter_date = getLastDay($sub_date, 1);
                                        $enter_time = $sub_start;
                                        if ($sub_schedule == 7 && stripos($sub_gate_name, "(OUT)") != false) {
                                            $gate_query = "SELECT id FROM tgate WHERE name LIKE '%(IN)%' ";
                                            $gate_result = selectData($iconn, $gate_query);
                                            if (0 < $gate_result[0]) {
                                                $sub_gate = $gate_result[0];
                                            }
                                        }
                                        $enter_flag = true;
                                    }
                                }
                            }
                        }
                        if ($enter_flag) {
//                            $query = "UPDATE tenter SET p_flag = 0, e_etc = 'P' WHERE p_flag = '1' AND e_date = '" . $enter_date . "' AND e_time = '" . $enter_time . "00' AND g_id = " . $sub_gate . " AND e_id =  " . $sub_id . " AND e_group = " . $sub_group;
                            $query = "
                                UPDATE tenter
                                SET p_flag = 0, e_etc = 'P'
                                WHERE p_flag = '1'
                                  AND e_date = '" . $enter_date . "'
                                  AND e_time = '" . str_pad($enter_time, 6, '0', STR_PAD_RIGHT) . "'
                                  AND g_id = " . (int)$sub_gate . "
                                  AND e_id = " . (int)$sub_id . "
                                  AND e_group = " . (int)$sub_group . "
                            ";
                            updateIData($iconn, $query, true);
//                            $query = "INSERT INTO tenter (e_date, e_time, g_id, e_id, e_group, e_user, e_mode, e_type, e_result, e_etc) VALUES ('" . $enter_date . "', '" . $enter_time . "00', " . $sub_gate . ", " . $sub_id . ", " . $sub_group . ", '0', '3', '3', '0', 'P')";
                            // Make sure your inputs are valid
                            $enter_date = !empty($enter_date) ? $enter_date : date('Ymd'); // fallback to today
                            $enter_time = !empty($enter_time) ? str_pad($enter_time, 6, '0', STR_PAD_RIGHT) : '000000';

                            $sub_gate   = isset($sub_gate)   ? (int)$sub_gate   : 0;
                            $sub_id     = isset($sub_id)     ? (int)$sub_id     : 0;
                            $sub_group  = isset($sub_group)  ? (int)$sub_group  : 0;

                            $query = "
                                INSERT INTO tenter 
                                    (e_date, e_time, g_id, e_id, e_group, e_user, e_mode, e_type, e_result, e_etc) 
                                VALUES 
                                    ('$enter_date', '$enter_time', $sub_gate, $sub_id, $sub_group, 0, 3, 3, 0, 'P')
                                ON DUPLICATE KEY UPDATE
                                    g_id      = VALUES(g_id),
                                    e_group  = VALUES(e_group),
                                    e_user   = VALUES(e_user),
                                    e_mode   = VALUES(e_mode),
                                    e_type   = VALUES(e_type),
                                    e_result = VALUES(e_result),
                                    e_etc    = VALUES(e_etc)
                            ";
                            updateIData($jconn, $query, true);
                            $query = "SELECT ed FROM tenter WHERE e_date = '" . $enter_date . "' AND e_time = '" . $enter_time . "' AND g_id = " . $sub_gate . " AND e_id = " . $sub_id . " AND e_group = " . $sub_group;
                            $max = selectData($conn, $query);
                            $ed = isset($max[0]) && $max[0] !== "" ? (int)$max[0] : 0;
                            //$query = "INSERT INTO AlterLog (Username, ed, DateTo, TimeTo, GateTo, TransactDate, ShiftTo) VALUES ('" . $username . "', " . $ed . ", '" . $enter_date . "', '" . $enter_time . "00', " . $sub_gate . ", " . insertToday() . ", " . $sub_group . ")";
                            // Fallbacks / sanitization
                            $username     = !empty($username) ? $username : 'system';
                            $ed           = isset($ed) && $ed !== '' ? (int)$ed : 0;
                            $enter_date   = !empty($enter_date) ? $enter_date : date('Ymd');  // default to today
                            $enter_time   = !empty($enter_time) ? str_pad($enter_time, 6, '0', STR_PAD_RIGHT) : '000000';
                            $sub_gate     = isset($sub_gate) ? (int)$sub_gate : 0;
                            $sub_group    = isset($sub_group) ? (int)$sub_group : 0;
                            $transactDate = insertToday(); // your existing function

                            $query = "
                                INSERT INTO AlterLog 
                                    (Username, ed, DateTo, TimeTo, GateTo, TransactDate, ShiftTo)
                                VALUES 
                                    ('$username', $ed, '$enter_date', '$enter_time', $sub_gate, $transactDate, $sub_group)
                            ";
                            updateIData($kconn, $query, true);
                            $timeHHMM = substr($sub_time, 0, 4); // e.g. 1800
                            if ($timeHHMM == $sub_close) {
                                // Only OUT punch present → add missing IN
                                $punchType = "IN";
                                $punchTime = $sub_start; // 0900
                            }elseif ($timeHHMM == $sub_start) {
                                // Only IN punch present → add missing OUT
                                $punchType = "OUT";
                                $punchTime = $sub_close; // 1800
                            }
                            $text = "Fixed Single Clocking → Added punch for EmployeeID: " . $sub_id .
                                    " on Date: " . displayDate($sub_date) .
                                    " at Time: " . $punchTime . "00" .
                                    ", GateID: " . $sub_gate .
                                    ", ShiftID: " . $sub_group;

                            $logQuery = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) 
                                         VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . mysqli_real_escape_string($conn, $text) . "')";
                            updateIData($conn, $logQuery, true);
                            $counter = 0;
                        }
                    }
                    $sub_date = $sub_cur[0];
                    $sub_time = $sub_cur[1];
                    $sub_gate = $sub_cur[2];
                    $sub_id = $sub_cur[3];
                    $sub_start = $sub_cur[4];
                    $sub_close = $sub_cur[5];
                    $sub_work_min = $sub_cur[6];
                    $sub_night_flag = $sub_cur[7];
                    $sub_group = $sub_cur[8];
                    $sub_schedule = $sub_cur[9];
                    $sub_gate_name = $sub_cur[10];
                    $sub_bfrom = $sub_cur[11];
                    $sub_bto = $sub_cur[12];
                }
                if ($counter == 1) {
                    $halfTime = getLateTime($sub_date, $sub_start, $sub_work_min / 2);
                    if ($sub_night_flag == 0 && $halfTime <= $sub_time || $sub_night_flag == 1 && $sub_time <= $nightShiftMaxOutTime) {
                        if ($sub_night_flag == 1) {
                            $sub_date = getLastDay($sub_date, 1);
                        }
                        if ($sub_schedule == 7 && stripos($sub_gate_name, "(OUT)") != false) {
                            $gate_query = "SELECT id FROM tgate WHERE name LIKE '%(IN)%' ";
                            $gate_result = selectData($conn, $gate_query);
                            if (0 < $gate_result[0]) {
                                $sub_gate = $gate_result[0];
                            }
                        }
                        $query = "UPDATE tenter SET p_flag = 0, e_etc = 'P' WHERE p_flag = '1' AND e_date = '" . $sub_date . "' AND e_time = '" . $sub_start . "00' AND g_id = " . $sub_gate . " AND e_id =  " . $sub_id . " AND e_group = " . $sub_group;
                        updateIData($iconn, $query, true);
//                        $query = "INSERT INTO tenter (e_date, e_time, g_id, e_id, e_group, e_user, e_mode, e_type, e_result, e_etc) VALUES ('" . $sub_date . "', '" . $sub_start . "00', " . $sub_gate . ", " . $sub_id . ", " . $sub_group . ", '0', '3', '3', '0', 'P')";
                        $query = "INSERT INTO tenter
                                (e_date, e_time, g_id, e_id, e_group, e_user, e_mode, e_type, e_result, e_etc)
                            VALUES
                                ('$sub_date', '" . $sub_start . "00', $sub_gate, $sub_id, $sub_group, 0, 3, 3, 0, 'P')
                            ON DUPLICATE KEY UPDATE
                                g_id     = VALUES(g_id),
                                e_group  = VALUES(e_group),
                                e_user   = VALUES(e_user),
                                e_mode   = VALUES(e_mode),
                                e_type   = VALUES(e_type),
                                e_result = VALUES(e_result),
                                e_etc    = VALUES(e_etc)";
                        updateIData($jconn, $query, true);
                        $query = "SELECT ed FROM tenter WHERE e_date = '" . $sub_date . "' AND e_time = '" . $sub_start . "00' AND g_id = " . $sub_gate . " AND e_id = " . $sub_id . " AND e_group = " . $sub_group;
                        $max = selectData($conn, $query);
                        $query = "INSERT INTO AlterLog (Username, ed, DateTo, TimeTo, GateTo, TransactDate, ShiftTo) VALUES ('" . $username . "', " . $max[0] . ", '" . $sub_date . "', '" . $sub_start . "00', " . $sub_gate . ", " . insertToday() . ", " . $sub_group . ")";
                        updateIData($kconn, $query, true);
                        $timeHHMM = substr($sub_time, 0, 4); // e.g. 1800
                        if ($timeHHMM == $sub_close) {
                            // Only OUT punch present → add missing IN
                            $punchType = "IN";
                            $punchTime = $sub_start; // 0900
                        }elseif ($timeHHMM == $sub_start) {
                            // Only IN punch present → add missing OUT
                            $punchType = "OUT";
                            $punchTime = $sub_close; // 1800
                        }
                        $text = "Fixed Single Clocking → Added punch for EmployeeID: " . $sub_id .
                                    " on Date: " . displayDate($sub_date) .
                                    " at Time: " . $punchTime . "00" .
                                    ", GateID: " . $sub_gate .
                                    ", ShiftID: " . $sub_group;

                        $logQuery = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) 
                                     VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . mysqli_real_escape_string($conn, $text) . "')";
                        updateIData($conn, $logQuery, true);
                        
                    } else {
                        if (($sub_night_flag == 0 && $sub_time < $halfTime || $sub_night_flag == 1 && $nightShiftMaxOutTime < $sub_time) && $sub_date < insertToday()) {
                            if ($sub_night_flag == 1) {
                                $sub_date = getNextDay($sub_date, 1);
                            }
                            if ($sub_schedule == 7 && stripos($sub_gate_name, "(IN)") != false) {
                                $gate_query = "SELECT id FROM tgate WHERE name LIKE '%(OUT)%' ";
                                $gate_result = selectData($conn, $gate_query);
                                if (0 < $gate_result[0]) {
                                    $sub_gate = $gate_result[0];
                                }
                            }
                            $query = "UPDATE tenter SET p_flag = 0, e_etc = 'P' WHERE p_flag = '1' AND e_date = '" . $sub_date . "' AND e_time = '" . $sub_close . "00' AND g_id = " . $sub_gate . " AND e_id =  " . $sub_id;
                            updateIData($iconn, $query, true);
                            $query = "INSERT INTO tenter (e_date, e_time, g_id, e_id, e_group, e_user, e_mode, e_type, e_result, e_etc) VALUES ('" . $sub_date . "', '" . $sub_close . "00', " . $sub_gate . ", " . $sub_id . ", " . $sub_group . ", '0', '3', '3', '0', 'P')";
                            updateIData($jconn, $query, true);
                            $query = "SELECT ed FROM tenter WHERE e_date = '" . $sub_date . "' AND e_time = '" . $sub_close . "00' AND g_id = " . $sub_gate . " AND e_id = " . $sub_id . " AND e_group = " . $sub_group;
                            $max = selectData($conn, $query);
                            $query = "INSERT INTO AlterLog (Username, ed, DateTo, TimeTo, GateTo, TransactDate, ShiftTo) VALUES ('" . $username . "', " . $max[0] . ", '" . $sub_date . "', '" . $sub_close . "00', " . $sub_gate . ", " . insertToday() . ", " . $sub_group . ")";
                            updateIData($kconn, $query, true);
                            $timeHHMM = substr($sub_time, 0, 4); // e.g. 1800
                            if ($timeHHMM == $sub_close) {
                                // Only OUT punch present → add missing IN
                                $punchType = "IN";
                                $punchTime = $sub_start; // 0900
                            }elseif ($timeHHMM == $sub_start) {
                                // Only IN punch present → add missing OUT
                                $punchType = "OUT";
                                $punchTime = $sub_close; // 1800
                            }
                            $text = "Fixed Single Clocking → Added punch for EmployeeID: " . $sub_id .
                                    " on Date: " . displayDate($sub_date) .
                                    " at Time: " . $punchTime . "00" .
                                    ", GateID: " . $sub_gate .
                                    ", ShiftID: " . $sub_group;

                            $logQuery = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) 
                                         VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . mysqli_real_escape_string($conn, $text) . "')";
                            updateIData($conn, $logQuery, true);
                            
                        }
                    }
                }
                file_put_contents("progress.log", "Step 1 complete\n", FILE_APPEND);
                header("Location: AlterTime.php?act=searchRecord&lstShift=" . $lstShift . "&lstDepartment=" . $lstDepartment . "&lstDivision=" . $lstDivision . "&lstEmployeeID=" . $lstEmployeeID . "&lstEmployeeIDFrom=" . $lstEmployeeIDFrom . "&lstEmployeeIDTo=" . $lstEmployeeIDTo . "&lstTerminal=" . $lstTerminal . "&txtEmployee=" . $txtEmployee . "&txtFrom=" . $txtFrom . "&txtTo=" . $txtTo . "&txtEmployeeCode=" . $txtEmployeeCode . "&lstEmployeeStatus=" . $lstEmployeeStatus . "&txtRemark=" . $txtRemark . "&txtPhone=" . $txtPhone . "&txtSNo=" . $txtSNo . "&lstSort=" . $lstSort . "&message=Fixed SINGLE Clocking(s)");
            }
        }
    }
}
if ($prints != "yes") {
    include 'header.php';
    ?>
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-12 d-flex no-block align-items-center">
                <h4 class="page-title">Alter Logs</h4>
                <div class="ms-auto text-end">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Alter Logs
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <?php
}
print "<form name='frm1' id='frm1' method='post' onSubmit='return checkSearch()' action='AlterTime.php'><input type='hidden' name='act' value='searchRecord'>";
if ($prints != "yes") {
    print'<div class="container-fluid">
                <div class="row">
                        <div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12">
                            <div class="card">
                                    <div class="card-body table-responsive">';
} else {
    if ($excel != "yes") {
        print "<body onLoad='javascript:window.print()'>";
    } else {
        ob_end_clean();
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=AlterTime.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        print "<body>";
    }
}

if ($excel != "yes") {
    ?>
    <div class="card">
        <div class="card-body">
            <?php
            print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b></font></p>";
            if ($prints != "yes") {
                print "<center><h4 class='card-title'>Select ONE or MORE options and click Search Record</h4></center>";
            }
            ?>
            <div class="row">
                <div class="col-3">
                    <?php
                    $query = "SELECT id, name from tgroup ORDER BY name";
                    displayList("lstShift", "Shift: ", $lstShift, $prints, $conn, $query, "", "", "");
                    ?>
                </div>
                <input type="hidden" size="25" name="txtEmployee" maxlength="25" value="" class="form-control">
                <input type="hidden" size="12" name="txtEmployeeCode" maxlength="12" value="" class="form-control">
                <input type="hidden" size="25" name="txtRemark" maxlength="25" value="" class="form-control">
                <input type="hidden" size="25" name="txtSNo" maxlength="25" value="" class="form-control">
                <?php
                displaySearchFields($conn, $prints, $session_variable, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $lstEmployeeName, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $lstGroup, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
                ?>
            </div>
            <div class="row">
                <div class="col-2">
                    <?php
                    $query = "SELECT id, name from tgate ORDER BY name";
                    displayList("lstTerminal", "Terminal: ", $lstTerminal, $prints, $conn, $query, "", "25%", "75%");
                    ?>
                </div>
                <div class="col-2">
                    <?php
                    displayTextbox("txtFrom", "Date From <font size='1'>(DD/MM/YYYY)</font>: ", $txtFrom, $prints, 12, "25%", "75%");
                    ?>
                </div>
                <div class="col-2">
                    <?php
                    displayTextbox("txtTo", "Date To <font size='1'>(DD/MM/YYYY)</font>: ", $txtTo, $prints, 12, "25%", "75%");
                    ?>
                </div>
                <?php
                if ($prints != "yes") {
                    print "<div class='col-2'>";
                    displayEmployeeStatus($conn, "lstEmployeeStatus", $lstEmployeeStatus, "", "");
                    print "</div>";
                    print "<div class='col-2'>";
                    $array = array(array("tuser.id, tenter.e_date, tenter.e_time", "Employee Code"), array("tuser.name, tuser.id, tenter.e_date, tenter.e_time", "Employee Name - Code"), array("tuser.dept, tuser.id, tenter.e_date, tenter.e_time", "Dept - Code"), array("tuser.company, tuser.dept, tuser.id, tenter.e_date, tenter.e_time", "Div/Desg - Dept - Code"), array("tuser.company, tuser.dept, tuser.group_id, tuser.id, tenter.e_date, tenter.e_time", "Div - Dept - Current Shift - Code"));
                    displaySort($array, $lstSort, 5);
                    print "</div>";
                    print "</div>";
                    print "<div class='row'>";
                    print "<div class='col-12'>";
                    print "<center><input name='btSearch' type='submit' value='Search Record' class='btn btn-primary'></center>";
                    print "</div>";
                    print "</div>";
                }
                ?>

            </div>
        </div>
    <?php } ?>      
    <?php
//    print "<center>";
    print '</div></div></div></div>';
    $records_per_page = 2; // Number of records per page
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $offset = ($page - 1) * $records_per_page;
    if ($act == "searchRecord") {
        $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tenter.e_date, tenter.e_time, tgate.name, tenter.ed, tuser.idno, tuser.remark, tenter.e_group, tenter.g_id FROM tuser, tgroup, tenter, tgate WHERE tenter.e_group = tgroup.id AND tenter.e_id = tuser.id AND tenter.g_id = tgate.id AND tenter.p_flag = 0 " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
        if ($lstShift != "") {
            $query = $query . " AND tgroup.id = " . $lstShift;
        }
        $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
        if ($lstTerminal != "") {
            $query = $query . " AND tenter.g_id = " . $lstTerminal;
        }
        if ($txtFrom != "") {
            $query = $query . " AND tenter.e_date >= '" . insertDate($txtFrom) . "'";
        }
        if ($txtTo != "") {
            $query = $query . " AND tenter.e_date <= '" . insertDate($txtTo) . "'";
        }
        $query = $query . employeeStatusQuery($lstEmployeeStatus);
        if ($lstSort != "") {
            $query = $query . " ORDER BY " . $lstSort;
        }
//        $query .= " LIMIT $offset, $records_per_page";

        print '<div class="row"><div class="col-md-12"><div class="card"><div class="card-body table-responsive">';
        if ($prints != "yes") {
            print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%' class='table table-striped table-bordered dataTable'><thead><tr><td><font face='Verdana' size='2'><input type='checkbox' name='chkAll' id='chkAll' onClick='checkAll(this)'></font></td>";
        } else {
            print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%' class='table table-striped table-bordered dataTable'><thead><tr>";
        }
        print "<td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Shift</font></td> <td><font face='Verdana' size='2'>Date</font></td> <td><font face='Verdana' size='2'>Time</font></td> <td><font face='Verdana' size='2'>Terminal</font></td>";
        if ($prints != "yes") {
            print "<td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td>";
        }
        print "</thead>";
        print "</tr>";
        print "</div></div></div></div>";
        $result = mysqli_query($conn, $query);
        $count = 0;
        $bg1 = "#FDFAD7";
        $bg2 = "#E8F2F9";
        $bgcolor = "";
        $ccount = 0;
        $id = "";
        print "<tbody id='table-body'>";
        for ($date = ""; $cur = mysqli_fetch_row($result); $count++) {
            if ($cur[3] == "") {
                $cur[3] = "&nbsp;";
            }
            if ($cur[9] == "") {
                $cur[9] = "&nbsp;";
            }
            if ($cur[10] == "") {
                $cur[10] = "&nbsp;";
            }
            if ($id != $cur[0] || $date != $cur[5]) {
                if ($ccount % 2 == 0) {
                    $bgcolor = $bg1;
                } else {
                    $bgcolor = $bg2;
                }
                $id = $cur[0];
                $date = $cur[5];
                $ccount++;
            }

            print "<tr>";
            if ($prints != "yes") {
                print "<td bgcolor='" . $bgcolor . "'><font face='Verdana' size='2'><input type='checkbox' class='chkBox' name='chk" . $count . "' id='chk" . $count . "'></td>";
            }
            addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
            print "<td bgcolor='" . $bgcolor . "'><a title='ID'><input type='hidden' name='txh" . $count . "' value='" . $cur[8] . "'> <font face='Verdana' size='1'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Name'><font face='Verdana' size='1'>" . $cur[1] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $cur[9] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Dept'><font face='Verdana' size='1'>" . $cur[2] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Div/Desg'><font face='Verdana' size='1'>" . $cur[3] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Rmk'><font face='Verdana' size='1'>" . $cur[10] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Shift'><input type='hidden' name='txhShift" . $count . "' value='" . $cur[11] . "'> <input type='hidden' name='txhTerminal" . $count . "' value='" . $cur[12] . "'> <font face='Verdana' size='1'>" . $cur[4] . "</font></a></td>";
            if ($prints != "yes") {
                displayDate($cur[5]);
                print "<td bgcolor='" . $bgcolor . "'><a title='Date'><input type='hidden' name='txhDate" . $count . "' value='" . $cur[5] . "'> <input name='txtDate" . $count . "' value='" . displayDate($cur[5]) . "' size='11' onBlur='javascript:check_valid_date(document.frm1.txtDate" . $count . ")' class='form-control'></a></td>";
            } else {
                displayDate($cur[5]);
                print "<td bgcolor='" . $bgcolor . "'><a title='Date'><font face='Verdana' size='1'>" . displayDate($cur[5]) . "</font></a></td>";
            }
            if ($prints != "yes") {
                print "<td bgcolor='" . $bgcolor . "'><a title='Time'><input type='hidden' name='txhTime" . $count . "' value='" . $cur[6] . "'> <input name='txtTime" . $count . "' value='" . $cur[6] . "' size='7' onBlur='javascript:check_valid_time(document.frm1.txtTime" . $count . ")' class='form-control'></a></td>";
            } else {
                displayVirdiTime($cur[6]);
                print "<td bgcolor='" . $bgcolor . "'><a title='Time'><font face='Verdana' size='1'>" . displayVirdiTime($cur[6]) . "</font></a></td>";
            }
            print "<td bgcolor='" . $bgcolor . "'><a title='Terminal'><font face='Verdana' size='1'>" . $cur[7] . "</font></a></td>";
            if ($prints != "yes") {
                $curIndex = isset($cur[8]) ? (int) $cur[8] : 0;
                if (strpos($userlevel, $current_module . "E") !== false && $_SESSION[$session_variable . "LockDate"] < insertDate($txtFrom) && $_SESSION[$session_variable . "LockDate"] < insertDate($txtTo)) {
                    print "<td bgcolor='" . $bgcolor . "'><input id='btEdit" . $curIndex . "' type='button' value='Edit' onClick='openWindow(1, " . json_encode($curIndex) . ")' class='btn btn-primary'></td>";
                } else {
                    print "<td bgcolor='" . $bgcolor . "'>&nbsp;</td>";
                }
                if (strpos($userlevel, $current_module . "D") !== false && $_SESSION[$session_variable . "LockDate"] < insertDate($txtFrom) && $_SESSION[$session_variable . "LockDate"] < insertDate($txtTo)) {
                    print "<td bgcolor='" . $bgcolor . "'><input id='btDel" . $cur[8] . "' type='button' value='Del' onClick='javascript:deleteRecord(" . $cur[8] . ")' class='btn btn-primary'></td>";
                } else {
                    print "<td bgcolor='" . $bgcolor . "'>&nbsp;</td>";
                }
                if (strpos($userlevel, $current_module . "A") !== false && $_SESSION[$session_variable . "LockDate"] < insertDate($txtFrom) && $_SESSION[$session_variable . "LockDate"] < insertDate($txtTo)) {
                    print "<td bgcolor='" . $bgcolor . "'><input id='btNew" . $cur[8] . "' type='button' value='New' onClick='javascript:openWindow(0, " . $cur[8] . ")' class='btn btn-primary'></td>";
                } else {
                    print "<td bgcolor='" . $bgcolor . "'>&nbsp;</td>";
                }
            }
            print "</tr>";
        }
        print "</tbody>";
//        $total_pages = ceil($count / $records_per_page);
//        echo '<div class="pagination">';
//        for ($i = 1; $i <= $total_pages; $i++) {
//            echo "<a href='AlterTime.php?page=$i'>$i</a> ";
//        }
//        echo '</div>';
        print "</table>";

        if ($excel != "yes") {
            print "<p align='center'><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $count . "</b></font></p>";
            print "<center><table width='850'>";
            if ($prints != "yes") {
                if (strpos($userlevel, $current_module . "E") !== false && $_SESSION[$session_variable . "LockDate"] < insertDate($txtFrom) && $_SESSION[$session_variable . "LockDate"] < insertDate($txtTo)) {
                    print "<tr>";
                    $query = "SELECT id, name from tgroup ORDER BY name";
                    print "<td>";
                    displayList("lstEditShift", "Change the Shift of the selected Record(s) to: <font size='1'>Leave selection as Blank if Shift Change is NOT required</font>", $lstEditShift, $prints, $conn, $query, "", "", "");
                    print "</td>";
                    print "<td>";
                    $query = "SELECT id, name from tgate ORDER BY name";
                    displayList("lstEditTerminal", "Change the Terminal of the selected Record(s) to: <font size='1'>Leave selection as Blank if Terminal Change is NOT required</font>", $lstEditTerminal, $prints, $conn, $query, "", "50%", "50%");
                    print "</td>";
                    print "</tr>";
                    print "<tr><td><input type='button' value='Save Changes' onClick='javascript:saveChanges()' class='btn btn-primary'></td>";
                }
                if (strpos($userlevel, $current_module . "D") !== false && $_SESSION[$session_variable . "LockDate"] < insertDate($txtFrom) && $_SESSION[$session_variable . "LockDate"] < insertDate($txtTo)) {
                    print "<td><input type='button' value='Delete Selected' onClick='deleteSelected()' class='btn btn-primary'></td>";
                }
                if (strpos($userlevel, $current_module . "A") !== false && $_SESSION[$session_variable . "LockDate"] < insertDate($txtFrom) && $_SESSION[$session_variable . "LockDate"] < insertDate($txtTo) && insertDate($txtFrom) < insertToday() && insertDate($txtTo) < insertToday()) {
                    print "<td><input id='btFSC' type='button' value='Fix SINGLE Clockings' onClick='fixSingleClocking()' class='btn btn-primary'></td></tr>";
                }
                print "<tr><td>&nbsp;</td><td><center><input type='button' value='Print Report' onClick='checkPrint(0)' class='btn btn-primary'>&nbsp;&nbsp;<input type='button' value='Excel' onClick='checkPrint(1)' class='btn btn-primary'></center></td></tr>";
            }
            print "</table></center>";
        }
    }
    print "<input type='hidden' name='txtCount' value='" . $count . "'>";
    print "</div>";
    print "</form>";
    print "\r\n<script>\r\nfunction saveChanges(){\r\n\tvar x = document.frm1;\r\n\tif (x.lstEditShift.value != \"\"){\r\n\t\tif (confirm(\"Selecting a Shift to be assigned will IGNORE any changes in DATE OR TIME for the selected Record(s)\\n\\rDo you want to Continue?\")){\r\n\t\t\tx.action = 'AlterTime.php?prints=yes';\r\n\t\t\tx.act.value = 'saveChanges';\t\r\n\t\t\tx.submit();\r\n\t\t}\r\n\t}else if (x.lstEditTerminal.value != \"\"){\r\n\t\tif (confirm(\"Selecting a Terminal to be assigned will IGNORE any changes in DATE OR TIME for the selected Record(s) \\n\\rDo you want to Continue?\")){\r\n\t\t\tx.action = 'AlterTime.php?prints=yes';\r\n\t\t\tx.act.value = 'saveChanges';\t\r\n\t\t\tx.submit();\r\n\t\t}\r\n\t}else{\r\n\t\tif (confirm('Save Changes')){\r\n\t\t\tx.action = 'AlterTime.php?prints=yes';\r\n\t\t\tx.act.value = 'saveChanges';\t\r\n\t\t\tx.submit();\r\n\t\t}\t\t\r\n\t}\r\n}\r\n\r\nfunction deleteSelected(){\r\n\tif (confirm('Delete Selected Record(s)')){\r\n\t\tvar x = document.frm1;\r\n\t\tx.action = 'AlterTime.php';\r\n\t\tx.act.value = 'deleteSelected';\t\r\n\t\tx.submit();\r\n\t}\r\n}\r\n\r\nfunction fixSingleClocking(){\r\n\tif (confirm('Fix SINGLE Clocking(s) for the Displayed Records')){\r\n\t\tvar x = document.frm1;\r\n\t\tx.action = 'AlterTime.php';\r\n\t\tx.act.value = 'fixSingleClocking';\t\r\n\t\tx.btFSC.disabled = true;\r\n\t\tx.submit();\r\n\t}\r\n}\r\n\r\nfunction openWindow(a, b){\r\n\tx = document.frm1;\r\n\t/*\r\n\tc = 0;\r\n\td = 0;\r\n\te = 0;\r\n\t*/\r\n\tc = '';\r\n\td = '';\r\n\te = '';\r\n\tf = '';\r\n\tg = '';\r\n\th = '';\r\n\ti = '';\r\n\tj = '';\r\n\tk = '';\r\n\tl = '';\r\n\tm = '';\r\n\tn = '';\r\n\to = '';\r\n\r\n\tif (x.lstShift.value != ''){c = x.lstShift.value;}\r\n\tif (x.lstDepartment.value != ''){d = x.lstDepartment.value;}\r\n\tif (x.lstDivision.value != ''){e = x.lstDivision.value;}\r\n\tif (x.lstEmployeeIDFrom.value != ''){f = x.lstEmployeeIDFrom.value;}\r\n\tif (x.lstEmployeeIDTo.value != ''){j = x.lstEmployeeIDTo.value;}\r\n\t//if (x.lstEmployee.value != ''){g = x.lstEmployee.value;}\r\n\tif (x.txtEmployee.value != ''){g = x.txtEmployee.value;}\r\n\tif (x.txtFrom.value != ''){h = x.txtFrom.value;}\r\n\tif (x.txtTo.value != ''){i = x.txtTo.value;}\r\n\tif (x.lstTerminal.value != ''){k = x.lstTerminal.value;}\r\n\tif (x.txtEmployeeCode.value != ''){l = x.txtEmployeeCode.value;}\r\n\tif (x.txtRemark.value != ''){m = x.txtRemark.value;}\r\n\tif (x.txtPhone.value != ''){n = x.txtPhone.value;}\r\n\tif (x.txtSNo.value != ''){o = x.txtSNo.value;}\r\n\tif (a == 1){\r\n\t\tdocument.getElementById(\"btEdit\"+b).disabled = true;\r\n\t}else{\r\n\t\tdocument.getElementById(\"btNew\"+b).disabled = true;\r\n\t}\r\n\tvar strWindowFeatures = \"location=no,height=150,width=800,scrollbars=yes,status=no,toolbar=no,menubar=no,resize=no\";\r\n\tvar URL = \"AlterTimeChild.php?lstED=\"+(b*1024)+\"&act=\"+a+\"&lstShift=\"+c+\"&lstDepartment=\"+d+\"&lstDivision=\"+e+\"&lstEmployeeIDFrom=\"+f+\"&lstEmployeeIDTo=\"+j+\"&txtEmployee=\"+g+\"&txtFrom=\"+h+\"&txtTo=\"+i+\"&lstTerminal=\"+k+\"&txtEmployeeCode=\"+l+\"&txtRemark=\"+m+\"&txtPhone=\"+n+\"&txtSNo=\"+o+\"&lstSort=\"+x.lstSort.value;\r\n\tvar win = window.open(URL, \"_blank\", strWindowFeatures);\r\n\t//win = window.open('AlterTimeChild.php?lstED='+(b*1024)+'&act='+a+'&lstShift='+c+'&lstDepartment='+d+'&lstDivision='+e+'&lstEmployeeIDFrom='+f+'&lstEmployeeIDTo='+j+'&txtEmployee='+g+'&txtFrom='+h+'&txtTo='+i+'&lstTerminal='+k+'&txtEmployeeCode='+l+'&txtRemark='+m+'&txtPhone='+n+'&txtSNo='+o+'&lstSort='+x.document.frm1.lstSort.value, 'winSmall', 'toolbar=no;menubar=no;scrollbars=yes;resize=yes;maximize=no;location=no;height=150;width=800'); \r\n\t//win.creator = self;\r\n}\r\n</script>\r\n\r\n";
    print "<script>";
//    print "function deleteRecord(x){" . "if (confirm('Delete this Record')){" . "document.getElementById('btDel'+x).disabled = true;" . "window.location.href='AlterTime.php?act=deleteRecord&lstShift=" . $lstShift . "&lstDepartment=" . $lstDepartment . "&lstDivision=" . $lstDivision . "&lstTerminal=" . $lstTerminal . "&lstEmployeeID=" . $lstEmployeeID . "&lstEmployeeIDFrom=" . $lstEmployeeIDFrom . "&lstEmployeeIDTo=" . $lstEmployeeIDTo . "&txtEmployee=" . $txtEmployee . "&txtFrom=" . $txtFrom . "&txtTo=" . $txtTo . "&txtEmployeeCode=" . $txtEmployeeCode . "&lstEmployeeStatus=" . $lstEmployeeStatus . "&txtRemark=" . $txtRemark . "&txtPhone=" . $txtPhone . "&txtSNo=" . $txtSNo . "&lstSort=" . $lstSort . "&lstED='+(x*1024);" . "}" . "}";
    print "</script>";
//    print "<script>\r\n\tfunction checkAll(){\t\r\n\t\tx = document.frm1;\r\n\t\ty = x.chkAll;\r\n\t\tz = x.txtCount.value;\t\r\n\t\tfor (i=0;i<z;i++){\t\t\r\n\t\t\tif (y.checked == true){\t\t\t\r\n\t\t\t\tdocument.getElementById(\"chk\"+i).checked = true;\t\t\t\r\n\t\t\t}else{\r\n\t\t\t\tdocument.getElementById(\"chk\"+i).checked = false;\r\n\t\t\t}\r\n\t\t}\r\n\t}\r\n</script>";
    include 'footer.php';
    ?>
    <script>

        document.getElementById("btEdit" + b).disabled = true;
        function saveChanges() {
            var x = document.frm1;
            if (x.lstEditShift.value != "") {
                if (confirm("Selecting a Shift to be assigned will IGNORE any changes in DATE OR TIME for the selected Record(s)\n\rDo you want to Continue?")) {
                    x.action = 'AlterTime.php?prints=yes';
                    x.act.value = 'saveChanges';
                    x.submit();
                }
            } else if (x.lstEditTerminal.value != "") {
                if (confirm("Selecting a Terminal to be assigned will IGNORE any changes in DATE OR TIME for the selected Record(s) \n\rDo you want to Continue?")) {
                    x.action = 'AlterTime.php?prints=yes';
                    x.act.value = 'saveChanges';
                    x.submit();
                }
            } else {
                if (confirm('Save Changes')) {
                    x.action = 'AlterTime.php?prints=yes';
                    x.act.value = 'saveChanges';
                    x.submit();
                }
            }
        }

        function deleteSelected() {
            if (confirm('Delete Selected Record(s)')) {
                var x = document.getElementById('frm1');
                x.action = 'AlterTime.php';
                x.act.value = 'deleteSelected';
                x.submit();
            }
        }

        function fixSingleClocking() {
            if (confirm('Fix SINGLE Clocking(s) for the Displayed Records')) {
                var x = document.frm1;
                x.action = 'AlterTime.php';
                x.act.value = 'fixSingleClocking';
                x.btFSC.disabled = true;
                x.submit();
            }
        }

        function openWindow(a, b) {
            
//            var x = document.forms["frm1"];
//            var txtEmployee = document.querySelector("form[name='frm1'] input[name='txtEmployee']");
            x = document.frm1;
//            var x = document.forms["frm1"];
//            console.log("Form:", x);
//            console.log("txtEmployee:", x ? x.txtEmployee : "Not Found");
            /*
             c = 0;
             d = 0;
             e = 0;
             */
            c = '';
            d = '';
            e = '';
            f = '';
            g = '';
            h = '';
            i = '';
            j = '';
            k = '';
            l = '';
            m = '';
            n = '';
            o = '';

            if (x.lstShift.value != '') {
                c = x.lstShift.value;
            }
            if (x.lstDepartment.value != '') {
                d = x.lstDepartment.value;
            }
            if (x.lstDivision.value != '') {
                e = x.lstDivision.value;
            }
            if (x.lstEmployeeIDFrom.value != '') {
                f = x.lstEmployeeIDFrom.value;
            }
            if (x.lstEmployeeIDTo.value != '') {
                j = x.lstEmployeeIDTo.value;
            }
            //if (x.lstEmployee.value != ''){g = x.lstEmployee.value;}
            if (x.txtEmployee.value != '') {
                g = x.txtEmployee.value;
            }
            
            if (x.txtFrom.value != '') {
                h = x.txtFrom.value;
            }
            if (x.txtTo.value != '') {
                i = x.txtTo.value;
            }
            if (x.lstTerminal.value != '') {
                k = x.lstTerminal.value;
            }
            if (x.txtEmployeeCode.value != '') {
                l = x.txtEmployeeCode.value;
            }
            if (x.txtRemark.value != '') {
                m = x.txtRemark.value;
            }
            if (x.txtPhone.value != '') {
                n = x.txtPhone.value;
            }
            if (x.txtSNo.value != '') {
                o = x.txtSNo.value;
            }
            if (a == 1) {
                document.getElementById("btEdit" + b).disabled = true;
            } else {
                document.getElementById("btNew" + b).disabled = true;
            }
            var strWindowFeatures = "location=no,height=150,width=800,scrollbars=yes,status=no,toolbar=no,menubar=no,resize=no";
            var URL = "AlterTimeChild.php?lstED=" + (b * 1024) + "&act=" + a + "&lstShift=" + c + "&lstDepartment=" + d + "&lstDivision=" + e + "&lstEmployeeIDFrom=" + f + "&lstEmployeeIDTo=" + j + "&txtEmployee=" + g + "&txtFrom=" + h + "&txtTo=" + i + "&lstTerminal=" + k + "&txtEmployeeCode=" + l + "&txtRemark=" + m + "&txtPhone=" + n + "&txtSNo=" + o + "&lstSort=" + x.lstSort.value;
            var win = window.open(URL, "_blank", strWindowFeatures);
            //win = window.open('AlterTimeChild.php?lstED='+(b*1024)+'&act='+a+'&lstShift='+c+'&lstDepartment='+d+'&lstDivision='+e+'&lstEmployeeIDFrom='+f+'&lstEmployeeIDTo='+j+'&txtEmployee='+g+'&txtFrom='+h+'&txtTo='+i+'&lstTerminal='+k+'&txtEmployeeCode='+l+'&txtRemark='+m+'&txtPhone='+n+'&txtSNo='+o+'&lstSort='+x.document.frm1.lstSort.value, 'winSmall', 'toolbar=no;menubar=no;scrollbars=yes;resize=yes;maximize=no;location=no;height=150;width=800'); 
            //win.creator = self;
        }
        function checkAll() {
            var x = document.frm1; // form reference
            var y = x.chkAll; // "Check All" checkbox reference
            var z = x.txtCount.value; // Total count of checkboxes

            // Loop through the checkboxes based on the count
            for (var i = 0; i < z; i++) {
                var checkbox = document.getElementById("chk" + i); // Get each checkbox

                // Check if checkbox exists before accessing 'checked' property
                if (checkbox) {
                    checkbox.checked = y.checked; // Set its checked state
                }
            }
        }
        $(document).ready(function () {
            var table = $('#zero_config').DataTable();

            // Use event delegation for the "Select All" checkbox
            $('#chkAll').on('click', function () {
                var checked = this.checked;
                // Select all checkboxes in the table (visible and non-visible rows)
                $('input[type="checkbox"]', table.rows().nodes()).prop('checked', checked);
            });
        });
// Define the deleteRecord function  
        function deleteRecord(x) {
            if (confirm('Delete this Record')) {
                document.getElementById('btDel' + x).disabled = true;
                window.location.href = 'AlterTime.php?act=deleteRecord&lstShift=<?php echo $lstShift; ?>&lstDepartment=<?php echo $lstDepartment; ?>&lstDivision=<?php echo $lstDivision; ?>&lstTerminal=<?php echo $lstTerminal; ?>&lstEmployeeID=<?php echo $lstEmployeeID; ?>&lstEmployeeIDFrom=<?php echo $lstEmployeeIDFrom; ?>&lstEmployeeIDTo=<?php echo $lstEmployeeIDTo; ?>&txtEmployee=<?php echo $txtEmployee; ?>&txtFrom=<?php echo $txtFrom; ?>&txtTo=<?php echo $txtTo; ?>&txtEmployeeCode=<?php echo $txtEmployeeCode; ?>&lstEmployeeStatus=<?php echo $lstEmployeeStatus; ?>&txtRemark=<?php echo $txtRemark; ?>&txtPhone=<?php echo $txtPhone; ?>&txtSNo=<?php echo $txtSNo; ?>&lstSort=<?php echo $lstSort; ?>&lstED=' + (x * 1024);
            }
        }

    </script>