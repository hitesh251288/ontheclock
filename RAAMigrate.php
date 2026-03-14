<?php


ob_start("ob_gzhandler");
error_reporting(E_ALL);
date_default_timezone_set("Africa/Algiers");
include "Functions.php";
$conn = openConnection();
$iconn = openIConnection();
$query = "SELECT DBIP, DBName, DBUser, DBPass, EmployeeCodeLength FROM OtherSettingMaster";
$main_result = selectData($conn, $query);
$txtDBIP = $main_result[0];
$txtDBName = $main_result[1];
$txtDBUser = $main_result[2];
$txtDBPass = $main_result[3];
$txtECodeLength = $main_result[4];
if (checkMAC($conn)) {
    $aconn = odbc_connection("", "nitgenacdb", "admin", "nac3000");
    $cconn = odbc_connection("", "nitgenacdb", "admin", "nac3000");
    if ($aconn != "") {
        $query = "SELECT id, name, dept FROM Access.tuser WHERE PassiveType = 'ACT'";
        $result = mysqli_query($conn, $query);
        while ($cur = mysqli_fetch_row($result)) {
            $query = "INSERT INTO NGAC_USERINFO (userid, username, department) VALUES ('" . nitgenCode($cur[0]) . "', '" . $cur[1] . "', '" . $cur[2] . "')";
            odbc_exec($cconn, $query);
        }
    } else {
        print "Connection to External Database NOT available. Process Terminated.";
        exit;
    }
} else {
    print "Un Registered Application. Process Terminated.";
    exit;
}
function nitgenCode($data)
{
    $data = $data . "000000000";
    $data = addZero($data, 15);
    return $data;
}

?>