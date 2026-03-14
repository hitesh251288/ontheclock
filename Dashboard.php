<?php 
ob_start("ob_gzhandler");
error_reporting(E_ALL);
date_default_timezone_set("Africa/Algiers");
include "Functions.php";
$current_module = "25";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
$ex3 = $_SESSION[$session_variable . "Ex3"];
if (!is_numeric($ex3)) {
    $ex3 = 0;
}
$ex4 = "";
if ($ex4 == "") {
    $ex4 = "120";
}
if (!is_numeric($ex4)) {
    $ex4 = 120;
}
$lstEmployeeStatus = "ACT";
$lstClockingType = "All";
$count = 0;
$NightShiftMaxOutTime = $_SESSION[$session_variable . "NightShiftMaxOutTime"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=Dashboard.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}
$act = $_GET["act"];

//Total Employee
$querytotal = "SELECT * from tuser WHERE PassiveType='ACT'";
$resulttotal = mysqli_query($conn, $querytotal);
$row_cnt = $resulttotal->num_rows;
$current_date = date("Ymd");
//$current_date = '20250510';
$dt = DateTime::createFromFormat('Ymd', $current_date);

//Present
$query = "SELECT NightShiftMaxOutTime FROM OtherSettingMaster";
$result = selectData($conn, $query);
$cutoff = $result[0];
$querypresent = "SELECT 
                tuser.id, 
                tuser.name, 
                tuser.dept, 
                tuser.company, 
                tgroup.name AS group_name,
                tenter.e_date, 
                MIN(tenter.e_time) AS InTime, 
                MAX(tenter.e_time) AS OutTime, 
                tgate.name AS gate_name, 
                tuser.idno, 
                tuser.remark, 
                tuser.phone 
            FROM 
                tuser, tgroup, tenter, tgate
            WHERE 
                tenter.e_group = tgroup.id
                AND tenter.e_id = tuser.id
                AND tenter.g_id = tgate.id 
                AND tgate.exit = 0 
                AND (
                    (tenter.e_time > '$cutoff" . "00' AND tgroup.NightFlag = 1) 
                    OR 
                    (tenter.e_time > '000000' AND tgroup.NightFlag = 0)
                ) 
                AND tuser.id NOT IN (
                    SELECT e_id FROM FlagDayRotation WHERE e_date = '$current_date'
                ) 
                AND tenter.e_date = '$current_date' 
                AND tuser.PassiveType = '$lstEmployeeStatus' 
                " . $_SESSION[$session_variable . "DeptAccessQuery"] . " 
                " . $_SESSION[$session_variable . "DivAccessQuery"] . " 
            GROUP BY 
                tuser.id, tenter.e_date";

$resultpresent = mysqli_query($conn, $querypresent);
if (!$resultpresent) {
    die("Query failed: " . mysqli_error($conn));
}
$presentrow_cnt = $resultpresent->num_rows;

//Absent
$queryabsent = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, "
        . "tuser.idno, tuser.remark, tuser.PassiveType, tuser.F1, tuser.F2, "
        . "tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, "
        . "tuser.F10 FROM tuser, tgroup WHERE "
        . "(SUBSTRING(tuser.datelimit, 2, 8) < '" . $current_date . "0000') AND "
        . "tuser.group_id = tgroup.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " "
        . "" . $_SESSION[$session_variable . "DivAccessQuery"] . " AND tuser.OT1 NOT "
        . "LIKE '%" . getDay(displayDate($current_date)) . "%' AND tuser.OT2 NOT "
        . "LIKE '%" . getDay(displayDate($current_date)) . "%' AND tuser.id NOT "
        . "IN (SELECT e_id FROM FlagDayRotation WHERE e_date = '" . $current_date . "') "
        . "AND tuser.id NOT IN (SELECT DISTINCT(tenter.e_id) FROM tenter, tgate, "
        . "tgroup WHERE tenter.g_id = tgate.id AND tenter.e_group = tgroup.id AND "
        . "tgate.exit = 0 AND ((tenter.e_time > '" . $cutoff . "00' AND tgroup.NightFlag = 1) "
        . "OR (tenter.e_time > '000000' AND tgroup.NightFlag = 0)) AND tenter.e_date = '" . $current_date . "') "
        . "AND tuser.PassiveType = '" . $lstEmployeeStatus . "'";
$resultabsent = mysqli_query($conn, $queryabsent);
$absentrow_cnt = $resultabsent->num_rows;

//Late In
$querylate = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, "
        . "tenter.e_date, tenter.e_time, tgate.name, tuser.idno, tuser.remark, "
        . "tgroup.GraceTo, tgroup.Start, tuser.F1, tuser.F2, tuser.F3, tuser.F4, "
        . "tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, "
        . "tgroup, tenter, tgate WHERE tenter.e_group = tgroup.id AND tenter.e_id = tuser.id "
        . "AND tgroup.GraceTo >= '0000' AND tenter.g_id = tgate.id  AND "
        . "tgate.exit = 0 " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . "  "
        . "AND tenter.e_date = '" . $current_date . "' AND tuser.PassiveType = '" . $lstEmployeeStatus . "' ORDER BY tuser.id, tenter.e_date, tenter.e_time";

$last_id = "";
$last_date = "";
$countlate = 0;
$resultlate = mysqli_query($conn, $querylate);

while ($cur = mysqli_fetch_row($resultlate)) {
    if (!($cur[0] == $last_id && $cur[5] == $last_date)) {
        $lateTime = getLateTime($current_date, $cur[10], $ex3);
        $lateTimeThreshold = getLateTime($current_date, $cur[10], $ex4);
        $lateMin = getLateMin($current_date, $cur[6], $in);

//        if ($lateTime < $cur[6] && $cur[6] < $lateTimeThreshold && $lateMin > 0) {
        if (getLateTime($current_date, $cur[10], $ex3) < $cur[6] && $cur[6] < getLateTime($current_date, $cur[10], $ex4) && 0 < getLateMin($current_date, $cur[10], $cur[6])) {	
            $cur[3] = ($cur[3] == "") ? "&nbsp;" : htmlspecialchars($cur[3]);
            $cur[8] = ($cur[8] == "") ? "&nbsp;" : htmlspecialchars($cur[8]);
            $cur[9] = ($cur[9] == "") ? "&nbsp;" : htmlspecialchars($cur[9]);
            $countlate++;
        }

        $last_id = $cur[0];
        $last_date = $cur[5];
    }
}

//Early Exit
$queryEarlyexit = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, "
        . "tenter.e_date, tenter.e_time, tgate.name, tuser.idno, tuser.remark, "
        . "tgroup.Close, tgroup.NightFlag, tuser.F1, tuser.F2, tuser.F3, tuser.F4, "
        . "tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, "
        . "tgroup, tenter, tgate WHERE tenter.e_group = tgroup.id AND tenter.e_id = tuser.id "
        . "AND tenter.g_id = tgate.id AND tgroup.Close >= '0000' AND tuser.id > 0 "
        . "AND tgate.exit = 0 " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . "  "
//        . "AND tuser.id IN (SELECT DISTINCT(tuser.id) FROM tuser, tenter, tgate "
//        . "WHERE tenter.e_id = tuser.id AND tenter.g_id = tgate.id AND tgate.exit = 1) "
        . "AND ((tenter.e_time < '240000' AND tgroup.NightFlag =0) OR "
        . "(tenter.e_time < '" . $NightShiftMaxOutTime . "00' AND tgroup.NightFlag = 1)) "
        . "AND tenter.e_date = '" . $current_date . "' AND tuser.PassiveType = '" . $lstEmployeeStatus . "'";

$resultEarlyexit = mysqli_query($conn, $queryEarlyexit);

$ex3 = 0;    // minimum early minutes
$ex4 = 120;

$earlyExitCount = 0;
$last_id = "";
$last_date = "";
$ecount = 0;

while ($row = mysqli_fetch_assoc($resultEarlyexit)) {
    $e_time = $row['e_time'];
    $close = $row['Close'];
    
    if (strlen($close) == 4) {
        $close .= "00";                    // convert '1800' → '180000'
    }
    if (strlen($e_time) != 6 || strlen($close) != 6) continue;

    // Calculate thresholds
    $earlyMax = getEarlyTime($current_date, $close, $ex3); // 10 mins early
    $earlyMin = getEarlyTime($current_date, $close, $ex4); // 120 mins early
//    echo "e_time: $e_time | earlyMin: $earlyMin | earlyMax: $earlyMax | Close: $close<br>";
    
    if (strlen($e_time) == 6 && strlen($close) == 6 && $e_time < $close && $e_time >= $earlyMin && $e_time <= $earlyMax) {
        $earlyExitCount++;
    }
}

//echo "<h3>Early Exit Count on $current_date: <b>$earlyExitCount</b></h3>";
//$row_cnt_earlyexit = $resultEarlyexit->num_rows;
$row_cnt_earlyexit = $earlyExitCount;

//On Leave
$table_name = "FlagDayRotation";
$queryleave = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, "
        . "tgroup.name, " . $table_name . ".Flag, " . $table_name . ".e_date , "
        . "tgate.name, " . $table_name . ".Remark, " . $table_name . ".Rotate, " . $table_name . ".RecStat, "
        . "tuser.idno, tuser.remark, " . $table_name . ".OT, " . $table_name . ".OTH, "
        . "tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, "
        . "tuser.F9, tuser.F10 FROM tuser, tgroup, " . $table_name . ", tgate "
        . "WHERE tuser.group_id = tgroup.id AND " . $table_name . ".e_id = tuser.id "
        . "AND " . $table_name . ".g_id = tgate.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . "  "
        . "AND " . $table_name . ".e_date  >= '" . $current_date . "' AND " . $table_name . ".e_date  <= '" . $current_date . "' AND "
        . "tuser.PassiveType = '" . $lstEmployeeStatus . "'";
$resultleave = mysqli_query($conn, $queryleave);
$row_cnt_leave = $resultleave->num_rows;

//Department Wise Present
$totaldept = [];
$totaldiv = [];
while ($presentrow = mysqli_fetch_row($resultpresent)) {
    $totaldept[] = $presentrow[2];
    $totaldiv[] = $presentrow[3]; // Division Wise Attendance
}

$arrlengths = count($totaldept);
$arrCount = array();
for ($i = 0; $i < $arrlengths - 1; $i++) {
    $key = $totaldept[$i];
    if (@$arrCount[$key] >= 1) {
        $arrCount[$key] ++;
    } else {
        $arrCount[$key] = 1;
    }
}

// Division Wise Attendance
$arrdivlengths = count($totaldiv);
$arrdivCount = array();
for ($i = 0; $i < $arrdivlengths - 1; $i++) {
    $key = $totaldiv[$i];
    if (@$arrdivCount[$key] >= 1) {
        $arrdivCount[$key] ++;
    } else {
        $arrdivCount[$key] = 1;
    }
}
/* * ********************* */
$deptartment = [];
$division = [];
foreach ($arrCount as $key => $val) {
    $deptartment[] = array(
        $key, $val
    );
}
// Division Wise Attendance
foreach ($arrdivCount as $key => $val) {
    $division[] = array(
        $key, $val
    );
}
/* * *********************** */

$pieHeading = array(array('deptname', 'deptvalue'));
$depts = array_merge($pieHeading, $deptartment);
if (!empty($division)) {
    $piedivHeading = array(array('divname', 'divvalue'));
    $divs = array_merge($piedivHeading, $division);
} else {
    $divisions = [];
}
// Division Wise Attendance
//$piedivHeading = array(array('divname', 'divvalue'));
//$divs = array_merge($piedivHeading, $division);
/* * ************** */

$querydept = "SELECT distinct(dept) from tuser " . $_SESSION[$session_variable . "virdiDeptAccessQuery"] . " ORDER BY UPPER(dept)";
$resultdept = mysqli_query($conn, $querydept);
$dept = [];
while ($rowdept = mysqli_fetch_assoc($resultdept)) {
    $dept[] = $rowdept['dept'];
}

// Division Wise Present

/* * *********************** */
function checkinDetail($conn) {
    $empData = [];
    $query = "SELECT e_id,e_time,count(e_id) as totalInTime,e_date FROM tenter WHERE e_date='20210729' AND e_time <= ADDTIME((select MIN(e_time) from tenter where e_date='20210729'),SEC_TO_TIME(10*60)) group by e_id order by e_id asc";
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_array($result)) {
        $empData[] = $row;
    }
    return $empData;
}

$checkin = checkinDetail($conn);

if (isset($_POST['submit']) || !empty($_POST['lstShift']) || !empty($_POST['lstDept']) || !empty($_POST['lstDiv'])) {
    $lstShift = $_POST['lstShift'];
    $lstDept = $_POST['lstDept'];
    $lstDiv = $_POST['lstDiv'];
    $count = 0;
    
    if (!empty($lstShift)) {
        $queryShiftDetail .= " AND tgroup.id = " . $lstShift;
    }
    if (!empty($lstDept)) {
        $queryDeptDetail .= " AND tuser.dept =  '$lstDept' ";
    }
    if (!empty($lstDiv)) {
        $queryDivDetail .= " AND tuser.company =  '$lstDiv' ";
    }
    
    $querytotal = "SELECT tuser.id FROM tuser, tgroup WHERE tuser.group_id = tgroup.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " "
            . "$queryShiftDetail $queryDeptDetail $queryDivDetail AND tuser.PassiveType = '" . $lstEmployeeStatus . "' GROUP BY tuser.id ORDER BY tuser.id";
    $resulttotal = mysqli_query($conn, $querytotal);
    $row_cnt = $resulttotal->num_rows;

    $querypresent = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, "
            . "tenter.e_date, MIN(tenter.e_time) as InTime,MAX(tenter.e_time) as OutTime, "
            . "tgate.name, tuser.idno, tuser.remark, tuser.phone FROM tuser, tgroup, tenter, tgate WHERE tenter.e_group = tgroup.id
                AND tenter.e_id = tuser.id AND tenter.g_id = tgate.id AND tgate.exit = 0 AND ((tenter.e_time > '" . $cutoff . "00' AND tgroup.NightFlag = 1) OR (tenter.e_time > '000000' AND tgroup.NightFlag = 0))  
            " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " "
            . "$queryShiftDetail AND tuser.id NOT IN (SELECT e_id FROM FlagDayRotation WHERE e_date = '" . $current_date . "')
            AND tenter.e_date = '" . $current_date . "' $queryDeptDetail $queryDivDetail AND tuser.PassiveType = '" . $lstEmployeeStatus . "'  "
            . "GROUP BY tuser.id,tenter.e_date ORDER BY tuser.id, tenter.e_date, tenter.e_time";
    $resultpresent = mysqli_query($conn, $querypresent);
    $presentrow_cnt = $resultpresent->num_rows;

    $queryabsent = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tuser.idno, "
            . "tuser.remark, tuser.PassiveType, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, "
            . "tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, tgroup "
            . "WHERE SUBSTRING(tuser.datelimit, 2, 8) < '" . $current_date . "0000' AND tuser.group_id = tgroup.id AND "
            . "(tuser.UserStatus > 5 OR (tuser.UserStatus = 5 AND tuser.id = 'admin')) $queryShiftDetail $queryDeptDetail $queryDivDetail "
            . "AND tuser.PassiveType = '" . $lstEmployeeStatus . "' AND tuser.OT1 NOT LIKE '" . getDay(displayDate($current_date)) . "' AND tuser.OT2 "
            . "NOT LIKE '" . getDay(displayDate($current_date)) . "' AND tuser.id NOT IN (SELECT e_id FROM FlagDayRotation "
            . "WHERE e_date = '" . $current_date . "') AND tuser.id NOT IN (SELECT DISTINCT(tenter.e_id) FROM tenter, tgate, tgroup "
            . "WHERE tenter.g_id = tgate.id AND tenter.e_group = tgroup.id AND tgate.exit = 0 AND "
            . "((tenter.e_time > '" . $cutoff . "00' AND tgroup.NightFlag = 1) OR (tenter.e_time > '000000' AND tgroup.NightFlag = 0)) "
            . "AND tenter.e_date = '" . $current_date . "') group by tuser.id ORDER BY tuser.id";
    $resultabsent = mysqli_query($conn, $queryabsent);
    $absentrow_cnt = $resultabsent->num_rows;

    $table_name = "FlagDayRotation";
    $queryleave = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, "
            . "tgroup.name, " . $table_name . ".Flag, " . $table_name . ".e_date , "
            . "tgate.name, " . $table_name . ".Remark, " . $table_name . ".Rotate, " . $table_name . ".RecStat, "
            . "tuser.idno, tuser.remark, " . $table_name . ".OT, " . $table_name . ".OTH, "
            . "tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, "
            . "tuser.F9, tuser.F10 FROM tuser, tgroup, " . $table_name . ", tgate "
            . "WHERE tuser.group_id = tgroup.id AND " . $table_name . ".e_id = tuser.id $queryShiftDetail $queryDeptDetail $queryDivDetail "
            . "AND " . $table_name . ".g_id = tgate.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . "  "
            . "AND " . $table_name . ".e_date  >= '" . $current_date . "' AND " . $table_name . ".e_date  <= '" . $current_date . "' AND "
            . "tuser.PassiveType = '" . $lstEmployeeStatus . "'";
    $resultleave = mysqli_query($conn, $queryleave);
    $row_cnt_leave = $resultleave->num_rows;

    $querylate = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, "
            . "tenter.e_date, tenter.e_time, tgate.name, tuser.idno, tuser.remark, "
            . "tgroup.GraceTo, tgroup.Start, tuser.F1, tuser.F2, tuser.F3, tuser.F4, "
            . "tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, "
            . "tgroup, tenter, tgate WHERE tenter.e_group = tgroup.id AND tenter.e_id = tuser.id "
            . "AND tgroup.GraceTo <= tenter.e_time AND tenter.g_id = tgate.id  $queryShiftDetail $queryDeptDetail $queryDivDetail AND "
            . "tgate.exit = 0 " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . "  "
            . "AND tenter.e_date = '" . $current_date . "' AND tuser.PassiveType = '" . $lstEmployeeStatus . "' ORDER BY tuser.id, tenter.e_date, tenter.e_time";

    $last_id = "";
    $last_date = "";
    $resultlate = mysqli_query($conn, $querylate);

    while ($cur = mysqli_fetch_row($resultlate)) {
        if (!($cur[0] == $last_id && $cur[5] == $last_date)) {
            if (getLateTime($current_date, $cur[10], $ex3) < $cur[6] && $cur[6] < getLateTime($current_date, $cur[10], $ex4) && 0 < getLateMin($current_date, $cur[10], $cur[6])) {
                if ($cur[3] == "") {
                    $cur[3] = "&nbsp;";
                }
                if ($cur[8] == "") {
                    $cur[8] = "&nbsp;";
                }
                if ($cur[9] == "") {
                    $cur[9] = "&nbsp;";
                }
                $count++;
            } 
            $last_id = $cur[0];
            $last_date = $cur[5];
        }
    }
    $countlate = $count;
    $queryEarlyexit = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, "
            . "tenter.e_date, tenter.e_time, tgate.name, tuser.idno, tuser.remark, "
            . "tgroup.Close, tgroup.NightFlag, tuser.F1, tuser.F2, tuser.F3, tuser.F4, "
            . "tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, "
            . "tgroup, tenter, tgate WHERE tenter.e_group = tgroup.id AND tenter.e_id = tuser.id "
            . "AND tenter.g_id = tgate.id AND tgroup.Close >= '0000' AND tuser.id > 0 "
            . "AND tgate.exit = 0 " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . "  "
            . "AND tuser.id IN (SELECT DISTINCT(tuser.id) FROM tuser, tenter, tgate "
            . "WHERE tenter.e_id = tuser.id AND tenter.g_id = tgate.id AND tgate.exit = 1) "
            . "AND ((tenter.e_time < '240000' AND tgroup.NightFlag =0) OR "
            . "(tenter.e_time < '" . $NightShiftMaxOutTime . "00' AND tgroup.NightFlag = 1)) $queryShiftDetail $queryDeptDetail $queryDivDetail "
            . "AND tenter.e_date = '" . $current_date . "' AND tuser.PassiveType = '" . $lstEmployeeStatus . "'";

    $resultEarlyexit = mysqli_query($conn, $queryEarlyexit);
    $row_cnt_earlyexit = $resultEarlyexit->num_rows;
}

/* * **********Employee Gender Count******************* */
if($_POST['lstDept'] !='' || $_POST['lstDiv'] !=''){
    $where = " where dept= '". $_POST['lstDept'] . "' OR company= '".$_POST['lstDiv']."' ";
}
$genderQuery = "SELECT idno, COUNT(*) as count FROM tuser $where GROUP BY idno";
$genderResult = mysqli_query($conn, $genderQuery);
$genderData = [];
while ($row = mysqli_fetch_array($genderResult)) {
    $genderData[$row['idno']] = $row['count'];
}
$maleCount = isset($genderData['M']) ? $genderData['M'] : 0;
$femaleCount = isset($genderData['F']) ? $genderData['F'] : 0;

/* * *************Employee Category Count********************* */
// Fetch employee category count dynamically
if($_POST['lstDept'] !='' || $_POST['lstDiv'] !=''){
    $where = " AND dept= '". $_POST['lstDept'] . "' OR company= '".$_POST['lstDiv']."' ";
}
$categoryQuery = "SELECT remark, COUNT(*) as count FROM tuser where PassiveType='ACT' $where GROUP BY remark ORDER BY count DESC";
$categoryResult = mysqli_query($conn, $categoryQuery);

$categories = [];
$categoryCounts = [];
$totalEmployees = 0;
while ($row = mysqli_fetch_array($categoryResult)) {
    $categories[] = $row['remark'];
    $categoryCounts[] = $row['count'];
    $totalEmployees += $row['count'];
}

// Calculate cumulative percentages
$cumulativePercentages = [];
$cumulativeSum = 0;

foreach ($categoryCounts as $count) {
    $cumulativeSum += $count;
    $cumulativePercentages[] = ($cumulativeSum / $totalEmployees) * 100;
}
/* * *************Employee Status Count********************* */
// Fetch employee status count dynamically
if($_POST['lstDept'] !='' || $_POST['lstDiv'] !=''){
    $where = " where dept= '". $_POST['lstDept'] . "' OR company= '".$_POST['lstDiv']."' ";
}
$statusQuery = "SELECT PassiveType, COUNT(*) as count FROM tuser $where GROUP BY PassiveType ORDER BY count DESC";
$statusResult = mysqli_query($conn, $statusQuery);

//$statuses = [];
$counts = [];
$statuses = [
    'ACT' => 0,
    'INACTIVE' => 0
];
while ($row = mysqli_fetch_array($statusResult)) {
//    $statuses[] = $row['PassiveType'];
//    $counts[] = $row['count'];
    if ($row['PassiveType'] === 'ACT') {
        // Set the "ACT" count directly
        $statuses['ACT'] = $row['count'];
    } else {
        // Add counts of other types to "INACTIVE"
        $statuses['INACTIVE'] += $row['count'];
    }
}
//echo "<pre>";print_R($statuses);die;
// Calculate cumulative percentages
$cumulativePercentages = [];
$cumulativeSum = 0;

foreach ($categoryCounts as $count) {
    $cumulativeSum += $count;
    $cumulativePercentages[] = ($cumulativeSum / $totalEmployees) * 100;
}

include 'header.php';
?>
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-12 d-flex no-block align-items-center">
            <h4 class="page-title">Dashboard</h4>
            <div class="ms-auto text-end">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Dashboard
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
<!-- ============================================================== -->
<!-- End Bread crumb and right sidebar toggle -->
<!-- ============================================================== -->
<!-- ============================================================== -->
<!-- Container fluid  -->
<!-- ============================================================== -->
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Sales Cards  -->
    <!-- ============================================================== -->
    <?php if (getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) != 3) { 
        if ((strpos($userlevel, $current_module . "A") !== false || strpos($userlevel, $current_module . "E") !== false || strpos($userlevel, $current_module . "D") !== false)) {
        ?>
        <form name="shiftsearch" method="post" action="">
            <div class="row">
                <div class="col-md-6 col-lg-3 col-xlg-3">
                    <div class="card card-hover">
                        <select class="select2 form-select" name="lstShift" >
                            <option value="">Select Shift</option>
                            <?php
                            $queryTgroup = "SELECT id, name from tgroup WHERE id > 1 ORDER BY name";
                            $resultTgroup = mysqli_query($conn, $queryTgroup);
                            $selectedShift = isset($_POST['lstShift']) ? $_POST['lstShift'] : '';
                            while ($allShift = mysqli_fetch_array($resultTgroup)) {
                                $shiftId = $allShift[0];
                                $shiftName = $allShift[1];
                                ?>
                                <option value="<?php echo $shiftId; ?>" <?php echo ($selectedShift == $shiftId) ? 'selected' : ''; ?>><?php echo $shiftName; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 col-xlg-3">
                    <div class="card card-hover">
                        <select class="select2 form-select" name="lstDiv" >
                            <option value="">Select Division</option>
                            <?php
                            $queryDiv = "SELECT distinct(company), company from tuser " . $_SESSION[$session_variable . "DivAccessWhereQuery"] . " ORDER BY UPPER(company)";
//                            displaylist("lstDepartment", "Department: ", $lstDepartment, $prints, $conn, $query, "", "25%", "45%");
                            $resultDiv = mysqli_query($conn, $queryDiv);
                            $selectedDiv = isset($_POST['lstDiv']) ? $_POST['lstDiv'] : '';
                            while ($allDiv = mysqli_fetch_array($resultDiv)) {
                                $divId = $allDiv[0];
                                $divName = $allDiv[1];
                                ?>
                                <option value="<?php echo $divId; ?>" <?php echo ($selectedDiv == $divId) ? 'selected' : ''; ?>><?php echo $divName; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 col-xlg-3">
                    <div class="card card-hover">
                        <select class="select2 form-select" name="lstDept" >
                            <option value="">Select Department</option>
                            <?php
                            $queryDept = "SELECT distinct(dept), dept from tuser " . $_SESSION[$session_variable . "DeptAccessWhereQuery"] . " ORDER BY UPPER(dept)";
//                            displaylist("lstDepartment", "Department: ", $lstDepartment, $prints, $conn, $query, "", "25%", "45%");
                            $resultDept = mysqli_query($conn, $queryDept);
                            $selectedDept = isset($_POST['lstDept']) ? $_POST['lstDept'] : '';
                            while ($allDept = mysqli_fetch_array($resultDept)) {
                                $deptId = $allDept[0];
                                $deptName = $allDept[1];
                                ?>
                                <option value="<?php echo $deptId; ?>" <?php echo ($selectedDept == $deptId) ? 'selected' : ''; ?>><?php echo $deptName; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2 col-lg-2 col-xlg-2">
                    <div class="card card-hover">
                        <input type="submit" name="submit" value="Search" class="btn btn-primary">
                    </div>
                </div>
            </div>
        </form>
    <?php } } ?>
    <div class="row">
        <!-- Column -->
        <div class="col-md-6 col-lg-2 col-xlg-3">
            <div class="card card-hover">
                <a href="ReportEmployee.php?act=searchRecord&prints=no&lstShift=<?php echo $selectedShift; ?>&lstDivision=<?php echo $selectedDiv; ?>&lstDepartment=<?php echo $selectedDept; ?>" target="_blank">
                    <div class="box bg-cyan text-center">
                        <h1 class="font-light text-white">
                            <i class="mdi mdi-view-dashboard"></i>
                        </h1>
                        <h6 class="text-white">Head Count <br><?php echo $row_cnt; ?></h6>
                    </div>
                </a>
            </div>
        </div>
        <!-- Column -->
        <div class="col-md-6 col-lg-2 col-xlg-3">
            <div class="card card-hover">
                <a href="ReportAttendance.php?act=searchRecord&prints=no&txtFrom=<?php echo $dt->format('d/m/Y'); ?>&txtTo=<?php echo $dt->format('d/m/Y'); ?>&lstShift=<?php echo $selectedShift; ?>&lstDivision=<?php echo $selectedDiv; ?>&lstDepartment=<?php echo $selectedDept; ?>" target="_blank">
                    <div class="box bg-success text-center">
                        <h1 class="font-light text-white">
                            <i class="mdi mdi-chart-areaspline"></i>
                        </h1>
                        <h6 class="text-white">Present <br><?php echo $presentrow_cnt; ?></h6>
                    </div>
                </a>
            </div>
        </div>
        <!-- Column -->
        <div class="col-md-6 col-lg-2 col-xlg-3">
            <div class="card card-hover">
                <a href="ReportAbsence.php?act=searchRecord&prints=no&txtFrom=<?php echo $dt->format('d/m/Y'); ?>&txtTo=<?php echo $dt->format('d/m/Y'); ?>&lstShift=<?php echo $selectedShift; ?>&lstDivision=<?php echo $selectedDiv; ?>&lstDepartment=<?php echo $selectedDept; ?>" target="_blank">
                    <div class="box bg-warning text-center">
                        <h1 class="font-light text-white">
                            <i class="mdi mdi-collage"></i>
                        </h1>
                        <h6 class="text-white">Absent <br><?php echo $absentrow_cnt; ?></h6>
                    </div>
                </a>
            </div>
        </div>
        <!-- Column -->
        <div class="col-md-6 col-lg-2 col-xlg-3">
            <div class="card card-hover">
                <a href="ReportLateArrival.php?act=searchRecord&prints=no&txtFrom=<?php echo $dt->format('d/m/Y'); ?>&txtTo=<?php echo $dt->format('d/m/Y'); ?>&lstShift=<?php echo $selectedShift; ?>&lstDivision=<?php echo $selectedDiv; ?>&lstDepartment=<?php echo $selectedDept; ?>" target="_blank">
                    <div class="box bg-danger text-center">
                        <h1 class="font-light text-white">
                            <i class="mdi mdi-border-outside"></i>
                        </h1>
                        <h6 class="text-white">Late In <br><?php echo $countlate; ?></h6>
                    </div>
                </a>
            </div>
        </div>
        <!-- Column -->
        <div class="col-md-6 col-lg-2 col-xlg-3">
            <div class="card card-hover">
                <a href="ReportEarlyExit.php?act=searchRecord&prints=no&txtFrom=<?php echo $dt->format('d/m/Y'); ?>&txtTo=<?php echo $dt->format('d/m/Y'); ?>&lstShift=<?php echo $selectedShift; ?>&lstDivision=<?php echo $selectedDiv; ?>&lstDepartment=<?php echo $selectedDept; ?>" target="_blank">
                    <div class="box bg-info text-center">
                        <h1 class="font-light text-white">
                            <i class="mdi mdi-arrow-all"></i>
                        </h1>
                        <h6 class="text-white">Early Out <br><?php echo $row_cnt_earlyexit; ?></h6>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-md-6 col-lg-2 col-xlg-3">
            <div class="card card-hover">
                <a href="ReportPreFlag.php?act=searchRecord&prints=no&txtFrom=<?php echo $dt->format('d/m/Y'); ?>&txtTo=<?php echo $dt->format('d/m/Y'); ?>&lstShift=<?php echo $selectedShift; ?>&lstDivision=<?php echo $selectedDiv; ?>&lstDepartment=<?php echo $selectedDept; ?>" target="_blank">
                    <div class="box bg-danger text-center">
                        <h1 class="font-light text-white">
                            <i class="mdi mdi-arrow-all"></i>
                        </h1>
                        <h6 class="text-white">On Leave <br><?php echo $row_cnt_leave; ?></h6>
                    </div>
                </a>
            </div>
        </div>
        <!-- Column -->
        <!-- Column -->

        <!-- Column -->
    </div>
    <!-- ============================================================== -->
    <!-- Sales chart -->
    <!-- ============================================================== -->
    <?php if (getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) != 3) { ?>
        <div class="row">
            <div class="col-md-12 col-12">
                <!--<div class="card">-->
                <!--<div class="card-body">-->
                <div class="row">
                    <!-- column -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-md-flex align-items-center">
                                    <div>
                                        <h4 class="card-title">Department Wise Attendance</h4>
                                        <h5 class="card-subtitle">Overview of Latest Month</h5>
                                    </div>
                                </div>
                                <div class="flot-chart" style="height: 300px;">
                                    <div class="flot-chart-content" id="flot-line-chart" style="width:100%;height:100%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
<!--                    <div class="col-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Division Wise Attendance</h5>
                                <div>
                                    <canvas id="piechart" width="400" height="400"></canvas>
                                </div>
                                <button class="btn btn-group" id="prevPage">Previous Page</button>
                                <button class="btn btn-group" id="nextPage">Next Page</button>
                            </div>
                        </div>
                    </div>-->
                    <!-- column -->
                    <!--                        </div>
                                        </div>-->
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Division Wise Attendance</h5>
                        <div>
                            <canvas id="piechart" width="400" height="400"></canvas>
                        </div>
                        <button class="btn btn-group" id="prevPage">Previous Page</button>
                        <button class="btn btn-group" id="nextPage">Next Page</button>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Company Wide Attendance Status</h5>
                        <!--<div class="bars" id="barchart" style="height: 400px"></div>-->
                        <div>
                            <canvas id="barchart"></canvas>
                            <!--<canvas id="barChart" style="height: 400px; width: 100%;"></canvas>-->
                        </div>
                    </div>
                </div>
            </div>
<!--            <div class="col-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Employee Status</h5>
                        <div class="bars" id="barchart" style="height: 400px"></div>
                        <div>
                            <canvas id="horizontalBarChart" width="800" height="400"></canvas>
                            <canvas id="barChart" style="height: 400px; width: 100%;"></canvas>
                        </div>
                    </div>
                </div>
            </div>-->
        </div>
        <!-- ============================================================== -->
        <!-- Sales chart -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Recent comment and chats -->
        <!-- ============================================================== -->

        <!-- ============================================================== -->
        <!-- Recent comment and chats -->
        <!-- ============================================================== -->
        <!--</div>-->
        <div class="row">
            <div class="col-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Employee Status</h5>
                        <!--<div class="bars" id="barchart" style="height: 400px"></div>-->
                        <div>
                            <canvas id="horizontalBarChart" width="800" height="400"></canvas>
                            <!--<canvas id="barChart" style="height: 400px; width: 100%;"></canvas>-->
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Employee Gender Details</h5>
                        <!--<div class="bars" id="barchart" style="height: 400px"></div>-->
                        <div>
                            <canvas id="genderRadarChart"></canvas>
                            <!--<canvas id="barChart" style="height: 400px; width: 100%;"></canvas>-->
                        </div>
                    </div>
                </div>
            </div>
<!--            <div class="col-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Employee Category</h5>
                        <div class="bars" id="barchart" style="height: 400px"></div>
                        <div>
                            <canvas id="paretoChart" width="800" height="400"></canvas>
                           <canvas id="barChart" style="height: 400px; width: 100%;"></canvas>
                        </div>
                    </div>
                </div>
            </div>-->
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Employee Category</h5>
                        <!--<div class="bars" id="barchart" style="height: 400px"></div>-->
                        <div>
                            <canvas id="paretoChart" width="800" height="400"></canvas>
                           <!--<canvas id="barChart" style="height: 400px; width: 100%;"></canvas>-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>   
<!-- ============================================================== -->
<!-- End Container fluid  -->
<!-- ============================================================== -->
<!-- ============================================================== -->
<!-- footer -->
<!-- ============================================================== -->
<?php
/* * ***For Line Chart*** */
$jsonData = json_encode($depts);
$data = json_decode($jsonData, true);
$flotData = array();
$selectedDepartment = isset($_POST['lstDept']) ? $_POST['lstDept'] : '';
for ($i = 1; $i < count($data); $i++) {
//    $flotData[] = array($i - 1, $data[$i][1], $data[$i][0]);
    if (empty($selectedDepartment) || $data[$i][0] === $selectedDepartment) {
        $flotData[] = array($i - 1, $data[$i][1], $data[$i][0]);
    }
}

$selectedDivision = isset($_POST['lstDiv']) ? $_POST['lstDiv'] : '';
$divisionData = [];
if (!is_array($divs)) {
    $divs = []; // Ensure $divs is an array
}
for($h=1;$h<count($divs);$h++){
    if(empty($selectedDivision) || $divs[$h][0] == $selectedDivision){
        $divisionData[] = $divs[$h];
    }
}
//echo "<pre>";print_R($divs);
include 'footer.php';
?>

<script>
    /**********Department Wise Attendance***********/
    $(document).ready(function () {
        var data = <?php echo json_encode($flotData); ?>;

        function plotChart() {
            $.plot("#flot-line-chart", [{
                    data: data,
                    label: "Department",
                    lines: {show: true},
                    points: {show: true}
                }], {
                xaxis: {
                    ticks: data.map(d => [d[0], d[2]]),
                    tickLength: 0
                },
                yaxis: {
                    min: 0,
                    tickDecimals: 0
                },
                grid: {
                    hoverable: true,
                    clickable: true
                }
            });

            var previousPoint = null;
            $("#flot-line-chart").bind("plothover", function (event, pos, item) {
                if (item) {
                    if (previousPoint !== item.dataIndex) {
                        previousPoint = item.dataIndex;

                        $("#tooltip").remove();
                        var x = item.datapoint[0],
                                y = item.datapoint[1];

                        showTooltip(item.pageX, item.pageY, item.series.label + " of " + item.series.xaxis.ticks[item.dataIndex].label + " = " + y);
                    }
                } else {
                    $("#tooltip").remove();
                    previousPoint = null;
                }
            });
        }

        function showTooltip(x, y, contents) {
            $('<div id="tooltip">' + contents + '</div>').css({
                position: 'absolute',
                display: 'none',
                top: y + 5,
                left: x + 5,
                border: '1px solid #fdd',
                padding: '2px',
                'background-color': '#fee',
                opacity: 0.80
            }).appendTo("body").fadeIn(200);
        }

        // Initial plot
        plotChart();

        // Re-plot the chart on window resize
        $(window).resize(function () {
            // You might need a debounce function here to optimize performance
            plotChart();
        });
    });

    /***********Division Wise Attendance***********/
    $(document).ready(function () {
        var jsonData = <?php echo json_encode($divisionData); ?>;
        var currentPage = 1;
        var itemsPerPage = 10;
        var myPieChart;

        function updatePieChart() {
            var ctx = document.getElementById('piechart').getContext('2d');

            var startIndex = (currentPage - 1) * itemsPerPage;
            var dataSubset = jsonData.slice(startIndex, startIndex + itemsPerPage);

            var data = {
                labels: dataSubset.map(item => item[0]),
                datasets: [{
                        data: dataSubset.map(item => item[1]),
                        backgroundColor: ['red', 'blue', 'green', 'yellow', 'orange', 'purple', 'pink', 'brown', 'grey', 'black']
                    }]
            };

            var options = {
                responsive: true,
                maintainAspectRatio: false
            };

            if (myPieChart) {
                myPieChart.destroy();
            }

            myPieChart = new Chart(ctx, {
                type: 'pie',
                data: data,
                options: options
            });
        }

        function previousPage() {
            if (currentPage > 1) {
                currentPage--;
                updatePieChart();
            }
        }

        function nextPage() {
            var maxPage = Math.ceil(jsonData.length / itemsPerPage);
            if (currentPage < maxPage) {
                currentPage++;
                updatePieChart();
            }
        }

        function navigatePage(page) {
            currentPage = page;
            updatePieChart();
        }

        updatePieChart();

        var totalPageCount = Math.ceil(jsonData.length / itemsPerPage);
        for (var i = 1; i <= totalPageCount; i++) {
            $("#pagination").append('<button class="btn btn-primary" data-page="' + i + '">' + i + '</button>');
        }

        $("#pagination").on("click", "button", function () {
            var page = $(this).data("page");
            navigatePage(page);
        });

        $("#prevPage").on("click", previousPage);
        $("#nextPage").on("click", nextPage);
    });


    /*************Company Wide Attendance****************/
    let data = {
        labels: ['Present', 'Absent', 'Late In', 'Early Out', 'On Leave', 'Total Employee'],
        datasets: [{
                label: 'Attendance Data',
                data: [<?php echo $presentrow_cnt; ?>,<?php echo $absentrow_cnt; ?>,<?php echo $count; ?>,<?php echo $row_cnt_earlyexit; ?>,<?php echo $row_cnt_leave; ?>,<?php echo $row_cnt; ?>],
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
    };

    // Configuration options for the bar chart
    let options = {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    };

    // Get the canvas element
    const ctx = document.getElementById('barchart').getContext('2d');

    // Create the bar chart
    const myBarChart = new Chart(ctx, {
        type: 'bar',
        data: data,
        options: options
    });

    /**********Employee Gender***********************/
    // Fetch gender data from PHP
    const maleCount = <?php echo $maleCount; ?>;
    const femaleCount = Number(<?php echo (int)$femaleCount; ?>);

    // Setup chart data for radar chart
    const datagen = {
        labels: ['Male', 'Female', ''], // 3 axes needed
        datasets: [
            {
                label: 'Male',
                data: [maleCount, 0, 0], // only populate "Male" axis
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 3,
                pointBackgroundColor: '#36A2EB',
                pointBorderColor: '#36A2EB'
            },
            {
                label: 'Female',
                data: [0, femaleCount, 0], // only populate "Female" axis
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 3,
                pointBackgroundColor: '#FF6384',
                pointBorderColor: '#FF6384'
            }
        ]
    };

    const config = {
        type: 'radar',
        data: datagen,
        options: {
            responsive: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            return `${context.dataset.label}: ${context.raw}`;
                        }
                    }
                },
                legend: {
                    labels: {
                        color: 'black',
                        usePointStyle: true,
                        font: {
                            size: 14
                        },
                        padding: 20
                    }
                }
            },
            scales: {
                r: {
                    beginAtZero: true,
                    suggestedMax: Math.max(maleCount, femaleCount) + 2, // some breathing room
                    ticks: {
                        stepSize: 1,
                        backdropPadding: 4,
                        maxTicksLimit: 5,
                        color: '#555',
                        font: {
                            size: 12
                        }
                    },
                    pointLabels: {
                        font: {
                            size: 14
                        },
                        padding: 12
                    }
                }
            }
        }
    };

    // Render the radar chart
    const genderRadarChart = new Chart(
            document.getElementById('genderRadarChart'),
            config
            );

    /*************Employee Category Wise********************/
    // PHP arrays passed to JavaScript
    const categories = <?php echo json_encode($categories); ?>;
    const categoryCounts = <?php echo json_encode($categoryCounts); ?>;
    const cumulativePercentages = <?php echo json_encode($cumulativePercentages); ?>;

    // Create the Pareto chart
    const ctxcat = document.getElementById('paretoChart').getContext('2d');
    const paretoChart = new Chart(ctxcat, {
        type: 'bar', // Bar chart for categories
        data: {
            labels: categories, // Dynamic categories from PHP
            datasets: [
                {// Bar dataset (Category Counts)
                    label: 'Employee Count',
                    data: categoryCounts, // Category counts
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                },
                {// Line dataset (Cumulative Percentages)
                    label: 'Cumulative Percentage',
                    data: cumulativePercentages, // Cumulative percentages
                    type: 'line',
                    fill: false,
                    borderColor: 'rgba(255, 99, 132, 1)',
                    tension: 0.1, // Line smoothness
                    yAxisID: 'percentage'
                }
            ]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true, // Primary y-axis for employee counts
                    ticks: {
                        precision: 0  // Ensure whole numbers on the y-axis
                    }
                },
                percentage: {// Secondary y-axis for cumulative percentages
                    type: 'linear',
                    position: 'right',
                    beginAtZero: true,
                    ticks: {
                        callback: function (value) {
                            return value + '%';  // Display percentage sign
                        }
                    }
                }
            }
        }
    });

    /****************Employee Status**********************/
    /// PHP arrays passed to JavaScript
    const dataStatus = <?php echo json_encode($statuses); ?>;

    // Prepare datasets for the horizontal stacked bar chart
    const datastatus = {
        labels: ['Employee Status'], // Single label since we want a horizontal stack
        datasets: [
            {
                label: 'ACT',
                data: [dataStatus.ACT],
                backgroundColor: '#FF6384' // Color for "ACT"
            },
            {
                label: 'INACTIVE',
                data: [dataStatus.INACTIVE],
                backgroundColor: '#36A2EB' // Color for "INACTIVE"
            }
        ]
    };

    // Config for the horizontal stacked bar chart
    const configstatus = {
        type: 'bar',
        data: datastatus,
        options: {
            indexAxis: 'y', // Horizontal bar chart
            scales: {
                x: {
                    stacked: true  // Enable stacking
                },
                y: {
                    stacked: true  // Enable stacking
                }
            },
            responsive: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const label = context.dataset.label || '';
                            const value = context.raw;
                            return `${label}: ${value}`;
                        }
                    }
                }
            }
        }
    };

    // Render the Horizontal Stacked Bar chart
    const horizontalBarChart = new Chart(
            document.getElementById('horizontalBarChart'),
            configstatus
            );
</script>

