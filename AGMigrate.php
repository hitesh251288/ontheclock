<?php


ob_start("ob_gzhandler");
error_reporting(E_ERROR);
date_default_timezone_set("Africa/Algiers");
include "Functions.php";
$conn = openConnection();
if (checkMAC($conn) == false) {
    print "Un Registered Application. Process Terminated.";
    exit;
}
$aconn = odbc_connection("", "dbTimekeeper", "admin", "chairman");
if ($aconn == "") {
    print "Connection to External Database NOT available. Process Terminated.";
    exit;
}
$query = "SELECT MAX(AttendanceID) FROM AttendanceMaster WHERE PHF = 1";
$result = selectData($conn, $query);
$last_ed = $result[0];
if ($last_ed == "") {
    $last_ed = 0;
}
$query = "SELECT MAX(AttendanceID) FROM AttendanceMaster WHERE PHF = 0";
$result = selectData($conn, $query);
$max_ed = $result[0];
if ($max_ed == "") {
    $max_ed = 0;
}
$insert_flag = true;
$query = "SELECT AttendanceMaster.EmployeeID, AttendanceMaster.group_id, AttendanceMaster.group_min, AttendanceMaster.ADate, AttendanceMaster.EarlyIn, AttendanceMaster.LateIn, AttendanceMaster.EarlyOut, AttendanceMaster.LateOut, AttendanceMaster.Normal, AttendanceMaster.Grace, AttendanceMaster.Overtime, AttendanceMaster.AOvertime, AttendanceMaster.Day, AttendanceMaster.Flag, AttendanceMaster.OT1, AttendanceMaster.OT2, AttendanceMaster.PHF, DayMaster.Start, DayMaster.Close, tgroup.Start, tgroup.Close, tgroup.NightFlag, tuser.dept, tuser.idno, AttendanceMaster.AttendanceID FROM AttendanceMaster, DayMaster, tgroup, tuser WHERE AttendanceMaster.ADate = DayMaster.TDate AND AttendanceMaster.EmployeeID = DayMaster.e_id AND AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.EmployeeID = tuser.id AND AttendanceMaster.AttendanceID > " . $last_ed . " AND AttendanceMaster.AttendanceID <= " . $max_ed . " AND (AttendanceMaster.Normal > 0  OR AttendanceMaster.Overtime > 0) ";
$result = mysqli_query($conn, $query);
while ($cur = mysqli_fetch_row($result)) {
    $query = "INSERT INTO tblInOut (EmpID, DutyDate, ClockedIn, ClockedOut) VALUES ('" . $cur[23] . "', '" . displayDate($cur[3]) . "', '" . displayDate($cur[3]) . " " . displayTime($cur[17]) . "', ";
    if ($cur[21] == 0) {
        $query .= "'" . displayDate($cur[3]) . " " . displayTime($cur[18]) . "')";
    } else {
        $query .= "'" . displayDate(getNextDay($cur[3], 1)) . " " . displayTime($cur[18]) . "')";
    }
    odbc_exec($aconn, $query);
    if (0 < $cur[4]) {
        $cur[17] = $cur[19] . "00";
    } else {
        if (substr($cur[17], 2, 4) * 1 < 1501) {
            $cur[17] = substr($cur[17], 0, 2) . "0000";
        } else {
            if (1500 < substr($cur[17], 2, 4) * 1) {
                if (substr($cur[17], 0, 2) * 1 == 23) {
                    $cur[17] = "235959";
                } else {
                    $cur[17] = substr($cur[17], 0, 2) * 1 + 1;
                    $cur[17] = addZero($cur[17], 2) . "0000";
                }
            }
        }
    }
    if (substr($cur[18], 2, 4) * 1 < 4500) {
        $cur[18] = substr($cur[18], 0, 2) . "0000";
    } else {
        if (4459 < substr($cur[18], 2, 4) * 1) {
            if (substr($cur[18], 0, 2) * 1 == 23) {
                $cur[18] = "235959";
            } else {
                $cur[18] = substr($cur[18], 0, 2) * 1 + 1;
                $cur[18] = addZero($cur[18], 2) . "0000";
            }
        }
    }
    $out = 0;
    $pout = 0;
    $_day = substr($cur[3], 6, 2) . "/" . substr($cur[3], 4, 2) . "/" . substr($cur[3], 0, 4);
    $pin = displayParadoxDate(insertDate($_day));
    $pin = $pin . " " . substr($cur[17], 0, 2) . ":" . substr($cur[17], 2, 2);
    $in = $_day . " " . substr($cur[17], 0, 2) . ":" . substr($cur[17], 2, 2);
    if ($cur[21] == 1) {
        $_day = getNextDay(insertDate($_day), 1);
        $_day = displayDate($_day);
    }
    $pout = displayParadoxDate(insertDate($_day));
    $pout = $pout . " " . substr($cur[18], 0, 2) . ":" . substr($cur[18], 2, 2);
    $out = $_day . " " . substr($cur[18], 0, 2) . ":" . substr($cur[18], 2, 2);
    $seconds = strtotime($pout) - strtotime($pin);
    $normal = 0;
    $ot = 0;
    if (8 * 3600 < $seconds) {
        $normal = 8 * 3600;
        $ot = $seconds - $normal;
    } else {
        $normal = $seconds;
        $ot = 0;
    }
    if ($cur[13] == "Purple") {
        if (8 * 3600 < $seconds) {
            $ot = $ot * 2;
        }
    } else {
        if ($cur[12] == "Sunday") {
            $ot = $seconds;
            $normal = 0;
        } else {
            if ($cur[12] == "Saturday") {
                if (strtoupper($cur[22]) == "SECURITY") {
                    if (8 * 3600 < $seconds) {
                        $ot = $ot * 1;
                    }
                } else {
                    $ot = $seconds * 1;
                    $normal = 0;
                }
            } else {
                if ($cur[12] == "Friday" && strtoupper($cur[22]) != "SECURITY" && 140000 <= $cur[18] * 1 && $cur[18] * 1 <= 190000) {
                    if (0 < $ot) {
                        if (3600 < $ot) {
                            $ot = $ot - 3600;
                        } else {
                            $normal = $normal - (3600 - $ot);
                            $ot = 0;
                        }
                    } else {
                        $normal = $normal - 3600;
                    }
                }
            }
        }
    }
    $query = "INSERT INTO tblClockings (EmpID, DutyDate, ClockedIn, ClockedOut, StndHours, OTHours, ClockingStatus, FixRecord) VALUES ('" . $cur[23] . "', '" . displayDate($cur[3]) . "', '" . $in . "', '" . $out . "', " . round($normal / 3600) . ", " . round($ot / 3600) . ", ";
    $query = $query . " 'Normal' ";
    $query = $query . ", 0)";
    if (odbc_exec($aconn, $query)) {
        $query = "UPDATE AttendanceMaster SET PHF = 1 WHERE AttendanceID = " . $cur[24];
        updateData($conn, $query, true);
    }
}
$query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('AG Migrate', " . insertToday() . ", '" . getNow() . "')";
updateData($conn, $query, true);

?>