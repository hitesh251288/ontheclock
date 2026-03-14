<?php


ob_start("ob_gzhandler");
error_reporting(E_ALL);
date_default_timezone_set("Africa/Algiers");
include "Functions.php";
$txtDBIP = "154.118.51.247";
$txtDBName = "Access";
$txtDBUser = "fdmsusr";
$txtDBPass = "fdmsamho";
$aconn = odbc_connection("", "nitgenacdb", "", "");
echo "\n\rConnected to Local Access Database: " . $aconn;
if ($aconn != "") {
    $mconn = mysql_connection($txtDBIP, $txtDBName, $txtDBUser, $txtDBPass);
    echo "\n\rConnected to MySQL on Live IP: " . $mconn;
    if ($mconn != "") {
        $query = "SELECT e_idno FROM tenter";
        $result = selectData($mconn, $query);
        $last_ed = $result[0];
        if ($last_ed == "") {
            $last_ed = 0;
        }
        $query = "SELECT MAX(logid) FROM Checkinout";
        $last_result = odbc_exec($aconn, $query);
        odbc_fetch_into($last_result, $last_cur);
        $max_ed = $last_cur[0];
        $query = "SELECT Checkinout.CheckTime, Checkinout.Userid, Checkinout.Sensorid FROM Checkinout WHERE Checkinout.userid <> '' AND Checkinout.logid > " . $last_ed . " AND Checkinout.logid <= " . $max_ed . " ORDER BY Checkinout.logid";
        $result = odbc_exec($aconn, $query);
        while (odbc_fetch_into($result, $cur)) {
            $query = "INSERT INTO tenter (e_date, e_time, g_id, e_id, e_group) VALUES ('" . insertDate(substr($cur[0], 0, 10)) . "', '" . insertTime(substr($cur[0], 11, 8)) . "', '" . $cur[2] . "', '" . $cur[1] . "', '419')";
            updateData($mconn, $query, true);
        }
        $query = "UPDATE tenter, tuser SET tenter.e_group = tuser.group_id WHERE tenter.e_group = '419' AND tenter.e_id = tuser.id AND tuser.group_id > 1";
        if (updateData($mconn, $query, false)) {
            $query = "UPDATE tenter SET e_idno ='" . $max_ed . "'";
            updateData($mconn, $query, true);
        }
        $query = "SELECT Userinfo.Userid, Userinfo.Name, '', '', '', '', 0, Userinfo.EmployDate, '', '' FROM Userinfo ORDER BY Userinfo.userid";
        $result = odbc_exec($aconn, $query);
        $reg_date = "";
        while (odbc_fetch_into($result, $cur)) {
            if (strlen(insertDate(substr($cur[7], 0, 10))) < 8) {
                $reg_date = "20010101";
            } else {
                $reg_date = insertDate(substr($cur[7], 0, 10));
            }
            $query = "SELECT id FROM tuser WHERE id = '" . $cur[0] . "'";
            $sub_result = selectData($mconn, $query);
            if ($sub_result[0] == $cur[0]) {
                $query = "UPDATE tuser SET name = '" . replaceString($cur[1], false) . "', datelimit = 'N" . $reg_date . "" . $reg_date . "', PassiveType = 'ACT' WHERE id = '" . $cur[0] . "'";
                updateData($mconn, $query, true);
            } else {
                $query = "INSERT INTO tuser (id, name, datelimit, reg_date) VALUES ('" . $cur[0] . "', '" . replaceString($cur[1], false) . "', 'N" . $reg_date . "" . $reg_date . "', '" . insertDate(substr($cur[7], 0, 10)) . "0100', '" . replaceString($cur[9], false) . "', '" . replaceString($cur[5], false) . "' ";
                $query = $query . ")";
                updateData($mconn, $query, true);
            }
            $query = "INSERT INTO EmployeeFlag (EmployeeID) VALUES ('" . $cur[0] . "')";
            updateData($mconn, $query, true);
        }
        $query = "SELECT id FROM tuser ORDER BY id";
        $result = mysqli_query($mconn, $query);
        while ($cur = mysqli_fetch_row($result)) {
            $count = 0;
            $e_id = $cur[0];
            $query = "SELECT Userid FROM Userinfo WHERE Userid = '" . $e_id . "'";
            for ($sub_result = odbc_exec($aconn, $query); odbc_fetch_into($sub_result, $sub_cur); $count++) {
            }
            if ($count == 0) {
                $query = "UPDATE tuser SET datelimit = 'Y2001010120010101', PassiveType = 'RSN' WHERE id = '" . $cur[0] . "'";
                updateData($mconn, $query, true);
            }
        }
        $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('AAF Migrate', " . insertToday() . ", '" . getNow() . "')";
        updateData($mconn, $query, true);
    }
} else {
    print "Connection to External Database NOT available. Process Terminated.";
    exit;
}

?>