<?php


error_reporting(E_ALL);
date_default_timezone_set("Africa/Algiers");
include "SendMail.php";
set_time_limit(0);
$conn = openConnection();
$iconn = openIConnection();
$jconn = openIConnection();
$kconn = openIConnection();
$uconn = mysqli_connect("127.0.0.1", "unisuser", "unisamho", "UNIS");
if (checkMAC($conn) == true) {
    $query = "SELECT DBIP, DBName, DBUser, DBPass, EmployeeCodeLength, LockDate, CAGR FROM OtherSettingMaster";
    $main_result = selectData($conn, $query);
    $txtDBIP = $main_result[0];
    $txtDBName = $main_result[1];
    $txtDBUser = $main_result[2];
    $txtDBPass = $main_result[3];
    $txtECodeLength = $main_result[4];
    $txtLockDate = $main_result[5];
    $txtCAGR = $main_result[6];
    $array_id = "";
    $array_name = "";
    $array_dept = "";
    $array_count = 0;
    if ($txtCAGR == "Yes") {
        if (substr(displayToday(), 0, 2) == "01") {
            $count = 0;
            $query = "SELECT id FROM tuser, CAG WHERE tuser.CAGID = CAG.CAGID AND CAG.CAGType = 'Day' AND tuser.PassiveType = 'AGR' ";
            $result = mysqli_query($conn, $query);
            while ($cur = mysqli_fetch_row($result)) {
                $query = "UPDATE tuser SET L_OptDateLimit = 0 WHERE L_ID = '" . $cur[0] . "' AND L_OptDateLimit = 1";
                if (updateIData($uconn, $query, true)) {
                    $query = "UPDATE tuser SET datelimit = CONCAT('N', SUBSTRING(datelimit, 2, 16)), PassiveType = 'ACT' WHERE id = " . $cur[0];
                    if (updateIData($iconn, $query, true)) {
                        $count++;
                    }
                }
            }
            if (0 < $count) {
                $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Re-Activated Employees with Access Group Restriction Type = Day')";
                updateIData($iconn, $query, true);
            }
        } else {
            $count = 0;
            $start = substr(insertToday(), 0, 6) . "01";
            $query = "SELECT tuser.id, tuser.name, CAG.Days, tuser.dept FROM tuser, CAG WHERE tuser.CAGID = CAG.CAGID AND CAG.CAGType = 'Day' AND tuser.PassiveType = 'ACT' AND CAG.Days > 0 ORDER BY tuser.dept";
            $result = mysqli_query($conn, $query);
            while ($cur = mysqli_fetch_row($result)) {
                $query = "SELECT COUNT(DISTINCT(e_date)) FROM tenter WHERE e_id = '" . $cur[0] . "' AND e_date >= '" . $start . "'";
                $sub_result = selectData($iconn, $query);
                if ($cur[2] <= $sub_result[0]) {
                    $query = "UPDATE tuser SET L_OptDateLimit = 1 WHERE L_ID = '" . $cur[0] . "' AND L_OptDateLimit = 0 ";
                    if (updateIData($uconn, $query, true)) {
                        $query = "UPDATE tuser SET datelimit = CONCAT('Y', SUBSTRING(datelimit, 2, 16)), PassiveType = 'AGR' WHERE id = " . $cur[0];
                        if (updateIData($jconn, $query, true)) {
                            $array_id[$array_count] = $cur[0];
                            $array_name[$array_count] = $cur[1];
                            $array_dept[$array_count] = $cur[3];
                            $array_count++;
                            $count++;
                        }
                    }
                }
            }
            if (0 < $count) {
                $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'De-Activated Employees with Access Group Restriction Type = Day')";
                updateIData($iconn, $query, true);
            }
        }
        $count = 0;
        $query = "SELECT tuser.id, tuser.name, CAG.DateFrom, CAG.DateTo, tuser.dept FROM tuser, CAG WHERE tuser.CAGID = CAG.CAGID AND CAG.CAGType = 'Date' AND CAG.DateFrom > 0 AND CAG.DateTo > 0 ORDER BY tuser.dept";
        $result = mysqli_query($conn, $query);
        while ($cur = mysqli_fetch_row($result)) {
            $start = substr(insertToday(), 0, 6) . addZero($cur[2], 2);
            $end = substr(insertToday(), 0, 6) . addZero($cur[3], 2);
            if (insertToday() <= $end && $start <= insertToday()) {
                $query = "UPDATE tuser SET L_OptDateLimit = 0 WHERE L_ID = '" . $cur[0] . "' AND L_OptDateLimit = 1";
                if (updateIData($uconn, $query, true)) {
                    $query = "UPDATE tuser SET datelimit = CONCAT('N', SUBSTRING(datelimit, 2, 16)), PassiveType = 'ACT' WHERE id = " . $cur[0] . " AND PassiveType = 'AGR'";
                    if (updateIData($iconn, $query, true)) {
                        $count++;
                    }
                }
            } else {
                $query = "UPDATE tuser SET L_OptDateLimit = 1 WHERE L_ID = '" . $cur[0] . "' AND L_OptDateLimit = 0";
                if (updateIData($uconn, $query, true)) {
                    $query = "UPDATE tuser SET datelimit = CONCAT('Y', SUBSTRING(datelimit, 2, 16)), PassiveType = 'AGR' WHERE id = " . $cur[0] . " AND PassiveType = 'ACT'";
                    if (updateIData($iconn, $query, true)) {
                        $array_id[$array_count] = $cur[0];
                        $array_name[$array_count] = $cur[1];
                        $array_dept[$array_count] = $cur[4];
                        $array_count++;
                        $count++;
                    }
                }
            }
            if (0 < $count) {
                $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'De/Re-Activated Employees with Access Group Restriction Type = Date')";
                updateIData($iconn, $query, true);
            }
        }
        $count = 0;
        $mail_count = 0;
        $this_dept = "";
        $mail_text = "Dear User <br><br> Please find below, the list of Employees De-Activated TODAY<br> <table>";
        for ($i = 0; $i < count($array_id); $i++) {
            if (0 < $count && $this_dept != $array_dept[$i]) {
                $mail_text .= "</table> <br><br>List of Employees whose Activation Period is more than 75%<br> <table>";
                $start = substr(insertToday(), 0, 6) . "01";
                $query = "SELECT tuser.id, tuser.name, CAG.Days, tuser.dept FROM tuser, CAG WHERE tuser.CAGID = CAG.CAGID AND CAG.CAGType = 'Day' AND tuser.PassiveType = 'ACT' AND CAG.Days > 0 ORDER BY tuser.dept";
                $result = mysqli_query($conn, $query);
                while ($cur = mysqli_fetch_row($result)) {
                    $query = "SELECT COUNT(DISTINCT(e_date)) FROM tenter WHERE e_id = '" . $cur[0] . "' AND e_date >= '" . $start . "'";
                    $sub_result = selectData($iconn, $query);
                    if ($cur[2] * 75 / 100 <= $sub_result[0]) {
                        $mail_text .= "<tr><td>" . addZero($cur[0], $txtECodeLength) . "</td><td>" . $cur[1] . "</td><td>" . $cur[3] . "</td></tr>";
                    }
                }
                $mail_text .= "</table> <br><br> Best Regards<br>Virdi Admin <br><br>System Generated Mail. DO NOT REPLY.";
                $query = "SELECT UserMaster.Username, UserMaster.Usermail FROM UserMaster, UserDept WHERE UserMaster.Username = UserDept.Username AND UserDept.Dept = '" . $this_dept . "' AND UserMaster.Userlevel LIKE '%35R%'";
                $result = mysqli_query($jconn, $query);
                while ($cur = mysqli_fetch_row($result)) {
                    if (sendMail("Employee De-Activation Report: Total Record(s) - " . $count, str_replace("\r", "<br>", $mail_text), $mail_text, "", "", "Virdi Admin", $cur[1], $cur[0], "", "", "", "")) {
                        $mail_count++;
                    }
                }
                if (0 < $mail_count) {
                    $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('Sent De-Activation Report Mailer to " . $mail_count . " Users', " . insertToday() . ", '" . getNow() . "')";
                    updateIData($kconn, $query, true);
                } else {
                    echo "\n\rUnable to Send Mail";
                }
                $mail_text = "Dear User <br><br> Please find below, the list of Employees De-Activated TODAY<br> <table>";
            }
            $mail_text .= "<tr><td>" . addZero($array_id[$i], $txtECodeLength) . "</td><td>" . $array_name[$i] . "</td><td>" . $array_dept[$i] . "</td></tr>";
            $count++;
            $this_dept = $array_dept[$i];
        }
        if (0 < $count) {
            $mail_text .= "</table> <br><br>List of Employees whose Activation Period is more than 75%<br> <table>";
            $start = substr(insertToday(), 0, 6) . "01";
            $query = "SELECT tuser.id, tuser.name, CAG.Days, tuser.dept FROM tuser, CAG WHERE tuser.CAGID = CAG.CAGID AND CAG.CAGType = 'Day' AND tuser.PassiveType = 'ACT' AND CAG.Days > 0 ORDER BY tuser.dept";
            $result = mysqli_query($conn, $query);
            while ($cur = mysqli_fetch_row($result)) {
                $query = "SELECT COUNT(DISTINCT(e_date)) FROM tenter WHERE e_id = '" . $cur[0] . "' AND e_date >= '" . $start . "'";
                $sub_result = selectData($iconn, $query);
                if ($cur[2] * 75 / 100 <= $sub_result[0]) {
                    $mail_text .= "<tr><td>" . addZero($cur[0], $txtECodeLength) . "</td><td>" . $cur[1] . "</td><td>" . $cur[3] . "</td></tr>";
                }
            }
            $mail_text .= "</table> <br><br> Best Regards<br>Virdi Admin <br><br>System Generated Mail. DO NOT REPLY.";
            $query = "SELECT UserMaster.Username, UserMaster.Usermail FROM UserMaster, UserDept WHERE UserMaster.Username = UserDept.Username AND UserDept.Dept = '" . $this_dept . "' AND UserMaster.Userlevel LIKE '%35R%'";
            $result = mysqli_query($jconn, $query);
            while ($cur = mysqli_fetch_row($result)) {
                if (sendMail("Employee De-Activation Report: Total Record(s) - " . $count, str_replace("\r", "<br>", $mail_text), $mail_text, "", "", "Virdi Admin", $cur[1], $cur[0], "", "", "", "")) {
                    $mail_count++;
                }
            }
            if (0 < $mail_count) {
                $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('Sent De-Activation Report Mailer to " . $mail_count . " Users', " . insertToday() . ", '" . getNow() . "')";
                updateIData($kconn, $query, true);
            } else {
                echo "\n\rUnable to Send Mail";
            }
        }
    }
    $oconn = oracle_connection($txtDBIP, $txtDBName, $txtDBUser, $txtDBPass);
    echo "Connected to Oracle:" . $oconn;
    $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('Access Group Restriction Script', " . insertToday() . ", '" . getNow() . "')";
    updateIData($iconn, $query, true);
} else {
    print "\n\rUn-Registered Application. Process Terminated.";
}

?>