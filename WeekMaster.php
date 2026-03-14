<?php


ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$conn = openConnection();
$jconn = openIConnection();
$process_flag = false;
print "\nWeekly Shift Type Calculations for Last Week. This Utility should be run ONLY on Monday or Tuesday.";
displayToday();
getNow();
print "\nCurrent System Date and Time: " . displayToday() . ", " . getNow() . " HRS";
if (checkMAC($conn) == false) {
    print "Un Registered Application. Process Terminated.";
    exit;
}
$query = "SELECT MinClockinPeriod, TotalDailyClockin, ExitTerminal, Project, EarlyInOT, LessLunchOT, NightShiftMaxOutTime, TotalExitClockin, RotateShift, RotateShiftNextDay, MACAddress, EmployeeCodelength  FROM OtherSettingMaster";
$result = selectData($conn, $query);
$txtMinClockinPeriod = $result[0];
$txtTotalDailyClockin = $result[1];
$lstExitTerminal = $result[2];
$lstProject = $result[3];
$lstEarlyInOT = $result[4];
$lstLessLunchOT = $result[5];
$txtNightShiftMaxOutTime = $result[6];
$txtTotalExitClockin = $result[7];
$rotateShift = $result[8];
$nextDay = $result[9];
$txtMACAddress = $result[10];
$txtECodeLength = $result[11];
if (noTASoftware("", $txtMACAddress)) {
    print "Un Registered Application. Process Terminated.";
    exit;
}
if ($rotateShift == "Yes" && $nextDay < insertToday()) {
    print "Process TERMINATED because Shift Rotation DID NOT Process as PER SCHEDULE";
    exit;
}
if ($lstExitTerminal == "No") {
    $txtTotalExitClockin = 0;
}
$darray = getDate();
$day = $darray[weekday];
$firstDay = 0;
$lastDay = 0;
$lastTime = "235959";
$date = 0;
$month = 0;
$week = Date("W") - 1;
if ($day == "Monday") {
    $a = getDate(strtotime("-7 day"));
    $date = $a[mday];
    $month = $a[mon];
    if ($month < 10) {
        $month = "0" . $month;
    }
    if ($date < 10) {
        $date = "0" . $date;
    }
    $firstDay = $a[year] . $month . $date;
    $a = getDate(strtotime("-1 day"));
    $date = $a[mday];
    $month = $a[mon];
    if ($month < 10) {
        $month = "0" . $month;
    }
    if ($date < 10) {
        $date = "0" . $date;
    }
    $lastDay = $a[year] . $month . $date;
} else {
    if ($day == "Tuesday") {
        $a = getDate(strtotime("-8 day"));
        $date = $a[mday];
        $month = $a[mon];
        if ($month < 10) {
            $month = "0" . $month;
        }
        if ($date < 10) {
            $date = "0" . $date;
        }
        $firstDay = $a[year] . $month . $date;
        $a = getDate(strtotime("-2 day"));
        $date = $a[mday];
        $month = $a[mon];
        if ($month < 10) {
            $month = "0" . $month;
        }
        if ($date < 10) {
            $date = "0" . $date;
        }
        $lastDay = $a[year] . $month . $date;
    } else {
        echo "This Task can ONLY be performed on Monday or Tuesday";
        exit;
    }
}
$nightFlag = false;
$query = "SELECT id, NightFlag, WorkMin FROM tgroup WHERE ShiftTypeID = 2";
$result1 = mysqli_query($conn, $query);
while ($cur1 = mysqli_fetch_row($result1)) {
    if ($cur1[1] == 1) {
        $nightFlag = true;
        if ($day == "Monday") {
            $a = getDate(strtotime("-2 day"));
            $date = $a[mday];
            $month = $a[mon];
            if ($month < 10) {
                $month = "0" . $month;
            }
            if ($date < 10) {
                $date = "0" . $date;
            }
            $lastDay = $a[year] . $month . $date;
        } else {
            if ($day == "Tuesday") {
                $a = getDate(strtotime("-3 day"));
                $date = $a[mday];
                $month = $a[mon];
                if ($month < 10) {
                    $month = "0" . $month;
                }
                if ($date < 10) {
                    $date = "0" . $date;
                }
                $lastDay = $a[year] . $month . $date;
            }
        }
        $lastTime = $txtNightShiftMaxOutTime . "00";
    } else {
        $nightFlag = false;
    }
    $group_id = $cur1[0];
    $work_min = $cur1[2];
    $time = "";
    $query = "SELECT distinct(e_id) FROM tenter WHERE e_group = " . $group_id . " AND p_flag = 0 AND (tenter.e_date <= '" . $lastDay . "' AND tenter.e_time <= '" . $lastTime . "') AND tenter.e_date >= '" . $firstDay . "' ORDER BY e_id";
    $result2 = mysqli_query($conn, $query);
    while ($sdcur = mysqli_fetch_row($result2)) {
        $query = "SELECT tenter.e_date, tenter.e_time, tenter.g_id, tgate.exit, tenter.ed FROM tenter, tgate WHERE tenter.e_id = " . $result2[$j] . " AND tenter.g_id = tgate.id AND (tenter.e_date <= '" . $lastDay . "' AND tenter.e_time <= '" . $lastTime . "') AND tenter.e_date >= '" . $firstDay . "' AND tenter.p_flag = 0 ORDER BY tenter.e_date, tenter.e_time";
        $result3 = mysqli_query($conn, $query);
        while ($cur3 = mysqli_fetch_row($result3)) {
            if ($cur3[3] == 1 || $cur3[3] == true) {
                $query = "UPDATE tenter SET p_flag = 1 WHERE ed = " . $cur3[4];
                updateData($conn, $query, true);
            } else {
                if ($time == "") {
                    $time = mktime(substr($cur3[1], 0, 2), substr($cur3[1], 2, 2), substr($cur3[1], 4, 2), substr($cur3[0], 4, 2), substr($cur3[0], 6, 2), substr($cur3[0], 0, 4));
                } else {
                    $this_time = mktime(substr($cur3[1], 0, 2), substr($cur3[1], 2, 2), substr($cur3[1], 4, 2), substr($cur3[0], 4, 2), substr($cur3[0], 6, 2), substr($cur3[0], 0, 4));
                    if ($this_time * 1 - $time * 1 < $txtMinClockinPeriod) {
                        $query = "UPDATE tenter SET p_flag = 1 WHERE ed = " . $cur3[4];
                        updateData($conn, $query, true);
                    } else {
                        $time = $this_time;
                    }
                }
            }
        }
        $query = "SELECT COUNT(tenter.ed) FROM tenter WHERE tenter.e_id = " . $result2[$j] . " AND (tenter.e_date <= '" . $lastDay . "' AND tenter.e_time <= '" . $lastTime . "') AND tenter.e_date >= '" . $firstDay . "' AND tenter.p_flag = 0";
        $result4 = selectData($conn, $query);
        if (!($result4 != "" && $result4[0] % 2 != 0 && 0 < $result4[0])) {
            $query = "SELECT tenter.e_date, tenter.e_time, tenter.g_id, tgate.exit, tenter.ed FROM tenter, tgate WHERE tenter.e_id = " . $result2[$j] . " AND tenter.g_id = tgate.id AND (tenter.e_date <= '" . $lastDay . "' AND tenter.e_time <= '" . $lastTime . "') AND tenter.e_date >= '" . $firstDay . "' AND tenter.p_flag = 0 ORDER BY tenter.e_date, tenter.e_time";
            $date = "";
            $time = "";
            $normal = 0;
            $ot = 0;
            $in = 0;
            $out = 0;
            $id = "";
            $count = 0;
            $result5 = mysqli_query($conn, $query);
            while ($cur5 = mysqli_fetch_row($result5)) {
                if ($time == "") {
                    $date = $cur5[0];
                    $time = mktime(substr($cur5[1], 0, 2), substr($cur5[1], 2, 2), substr($cur5[1], 4, 2), substr($cur5[0], 4, 2), substr($cur5[0], 6, 2), substr($cur5[0], 0, 4));
                    $in = $cur5[1];
                    $id = $cur5[4];
                    $count++;
                } else {
                    $this_time = mktime(substr($cur5[1], 0, 2), substr($cur5[1], 2, 2), substr($cur5[1], 4, 2), substr($cur5[0], 4, 2), substr($cur5[0], 6, 2), substr($cur5[0], 0, 4));
                    if ($count % 2 != 0) {
                        $query = "INSERT INTO WeekMaster (WeekNo, e_id, LogDate, Start, Close, Seconds, group_id) VALUES (" . $week . ", " . $result2[$j] . ", " . $date . ", '" . $in . "', '" . $cur5[1] . "', " . ($this_time * 1 - $time * 1) . ", " . $group_id . ")";
                        updateData($conn, $query, true);
                        $process_flag = true;
                        $query = "UPDATE tenter SET p_flag = 1 WHERE ed = " . $id;
                        updateData($conn, $query, true);
                        $query = "UPDATE tenter SET p_flag = 1 WHERE ed = " . $cur5[4];
                        updateData($conn, $query, true);
                        $normal = $normal + $this_time - $time;
                        $date = "";
                        $time = "";
                        $in = "";
                        $id = "";
                    } else {
                        $date = $cur5[0];
                        $time = mktime(substr($cur5[1], 0, 2), substr($cur5[1], 2, 2), substr($cur5[1], 4, 2), substr($cur5[0], 4, 2), substr($cur5[0], 6, 2), substr($cur5[0], 0, 4));
                        $in = $cur5[1];
                        $id = $cur5[4];
                    }
                    $count++;
                }
            }
            if ($work_min * 60 <= $normal) {
                $ot = $normal - $work_min * 60;
                $normal = $work_min * 60;
            }
            $query = "INSERT INTO AttendanceMaster (EmployeeID, EmpID, group_id, group_min, Week, Normal, Overtime, ADate) VALUES (" . $result2[$j] . ", '" . addZero($result2[$j], $txtECodeLength) . "', " . $group_id . ", " . $work_min . ", " . $week . ", " . $normal . ", " . $ot . ", 0)";
            updateData($conn, $query, true);
            $normal = 0;
            $ot = 0;
        }
    }
}
if ($process_flag) {
    $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('Weekly', " . insertToday() . ", '" . getNow() . "')";
    updateData($conn, $query, true);
}

?>