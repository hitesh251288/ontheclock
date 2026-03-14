<?php


ob_start("ob_gzhandler");
error_reporting(E_ALL);
include "Functions.php";
$conn = openConnection();
$iconn = openIConnection();
$co_code = 1;
$txtDBIP = "127.0.0.1,1433";
$oconn = mssql_connection($txtDBIP, "COMM", "sa", "123");
if ($oconn != "") {
    $query = "SELECT CONVERT(VARCHAR(25), IdDateTime, 121), NodeCode, EnrollmentNo FROM tblRACData";
    $result = mssql_query($query, $oconn);
    while ($cur = mssql_fetch_row($result)) {
        $ta_query = "INSERT INTO tenter (e_date, e_time, g_id, e_id) VALUES ('" . insertParadoxDate($cur[0]) . "', '" . insertParadoxTime($cur[0]) . "', '" . $cur[1] . "', '" . $cur[2] . "') ";
        updateIData($iconn, $ta_query, true);
    }
    $query = "UPDATE tenter, tuser SET tenter.e_group = tuser.group_id WHERE tenter.e_group < 2";
    updateIData($iconn, $query, true);
}

?>