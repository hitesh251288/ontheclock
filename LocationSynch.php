<?php


error_reporting(E_ALL);
ob_start("ob_gzhandler");
include "Functions.php";
$db_user = $config["DB_USER"];
$db_pass = $config["DB_PASS"];
$conn = openConnection();
if ($conn == null || $conn == "") {
    echo "Client Station";
    unlink("Access.mdb");
    copy("C:\\Program Files\\Acserver\\Access.mdb", "Access.mdb");
} else {
    echo "Server Station";
    unlink("C:\\Program Files\\Acserver\\Access.mdb");
    unlink("C:\\Program Files\\Acserver\\Access.ldb");
    copy("Access.mdb", "C:\\Program Files\\Acserver\\Access.mdb");
    if (checkMAC($conn) == false || noTASoftware($conn, "") == true) {
        print "Un Registered Application. Process Terminated.";
        exit;
    }
    $aconn = odbc_connection("", "locationSynch", $db_user, $db_pass);
    if ($aconn == null || $aconn == "") {
        print "ODBC Connection -locationSynch- to C:\\Program Files\\AcServer\\Access.mdb NOT Found";
        exit;
    }
    $iconn = openIConnection();
    echo "\nCopying User Info";
    $query = "SELECT id, name, reg_date, datelimit, idno, badmin, padmin, dept, company, phone, group_id, cantgate, timegate, validtype, pwd, cancard, cardnum, identify, seculevel, fpdata, fpimage, fpname, face, voice, remark, antipass_state, antipass_lasttime FROM tuser";
    $result = odbc_exec($aconn, $query);
    while (odbc_fetch_into($result, $cur)) {
        $query = "INSERT INTO tuser (id, name, reg_date, datelimit, idno, badmin, padmin, dept, company, phone, group_id, cantgate, timegate, validtype, pwd, cancard, cardnum, identify, seculevel, fpdata, fpimage, fpname, face, voice, remark, antipass_state, antipass_lasttime) VALUES ('" . $cur[0] . "', '" . $cur[1] . "', '" . $cur[2] . "', '" . $cur[3] . "', '" . $cur[4] . "', '" . $cur[5] . "', '" . $cur[6] . "', '" . $cur[7] . "', '" . $cur[8] . "', '" . $cur[9] . "', '" . $cur[10] . "', " . $cur[11] . ", " . $cur[12] . ", '" . $cur[13] . "', '" . $cur[14] . "', '" . $cur[15] . "', '" . $cur[16] . "', '" . $cur[17] . "', '" . $cur[18] . "', " . $cur[19] . ", " . $cur[20] . ", " . $cur[21] . ", " . $cur[22] . ", " . $cur[23] . ", '" . $cur[24] . "', '" . $cur[25] . "', '" . $cur[26] . "')";
        updateIData($iconn, $query, true);
    }
    echo "\nCopying Shift Info";
    $query = "SELECT id, name, reg_date, timelimit, gate_id, remark FROM tgroup";
    $result = odbc_exec($aconn, $query);
    while (odbc_fetch_into($result, $cur)) {
        $query = "INSERT INTO tgroup (id, name, reg_date, timelimit, gate_id, remark) VALUES ('" . $cur[0] . "', '" . $cur[1] . "', '" . $cur[2] . "', '" . $cur[3] . "', " . $cur[4] . ", '" . $cur[5] . "')";
        updateIData($iconn, $query, true);
    }
    echo "\nCopying Terminal Info";
    $query = "SELECT id, name, reg_date, floor, place, block, userctrl, passtime, version, admin, lastup, remark, antipass, antipass_level, antipass_mode FROM tgate";
    $result = odbc_exec($aconn, $query);
    while (odbc_fetch_into($result, $cur)) {
        $query = "INSERT INTO tuser (id, name, reg_date, floor, place, block, userctrl, passtime, version, admin, lastup, remark, antipass, antipass_level, antipass_mode) VALUES ('" . $cur[0] . "', '" . $cur[1] . "', '" . $cur[2] . "', '" . $cur[3] . "', '" . $cur[4] . "', '" . $cur[5] . "', '" . $cur[6] . "', '" . $cur[7] . "', '" . $cur[8] . "', " . $cur[9] . ", '" . $cur[10] . "', " . $cur[11] . ", " . $cur[12] . ", '" . $cur[13] . "', '" . $cur[14] . "')";
        updateIData($iconn, $query, true);
    }
    $query = "SELECT LocationSynchShift FROM OtherSettingMaster";
    $result = selectData($conn, $query);
    $lstLocationSynchShift = $result[0];
    $query = "SELECT MAX(PDate) FROM ProcessLog WHERE PType LIKE '%Location%' AND Ptype LIKE '%Synch%'";
    $result = selectData($conn, $query);
    if ($result == "" || is_numeric($result[0]) == false) {
        $last_date = "20010101";
    } else {
        $last_date = $result[0];
    }
    echo "\nCopying Clocking Log";
    $query = "SELECT e_date, e_time, g_id, e_id, e_group, e_user, e_mode, e_type, e_result, e_etc FROM tenter WHERE e_date >= " . $last_date;
    $result = odbc_exec($aconn, $query);
    while (odbc_fetch_into($result, $cur)) {
        if ($lstLocationSynchShift == "Location") {
            $query = "INSERT INTO tenter (e_date, e_time, g_id, e_id, e_group, e_user, e_mode, e_type, e_result, e_etc) VALUES ('" . $cur[0] . "', '" . $cur[1] . "', '" . $cur[2] . "', '" . $cur[3] . "', '" . $cur[4] . "', '" . $cur[5] . "', '" . $cur[6] . "', '" . $cur[7] . "', '" . $cur[8] . "', '" . $cur[9] . "')";
        } else {
            $query = "INSERT INTO tenter (e_date, e_time, g_id, e_id, e_group, e_user, e_mode, e_type, e_result, e_etc) VALUES ('" . $cur[0] . "', '" . $cur[1] . "', '" . $cur[2] . "', '" . $cur[3] . "', '-1', '" . $cur[5] . "', '" . $cur[6] . "', '" . $cur[7] . "', '" . $cur[8] . "', '" . $cur[9] . "')";
        }
        updateIData($iconn, $query, true);
    }
    if ($lstLocationSynchShift == "Server") {
        $query = "UPDATE tenter, tuser SET tenter.e_group = tuser.group_id WHERE tenter.e_id = tuser.id AND tenter.e_group = -1 AND tenter.e_date >= " . $last_date;
        updateIData($iconn, $query, true);
    }
    $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('Location Synch', " . insertToday() . ", '" . getNow() . "')";
    updateIData($iconn, $query, true);
}

?>