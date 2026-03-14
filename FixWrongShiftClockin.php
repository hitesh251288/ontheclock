<?php


error_reporting(E_ALL);
ob_start("ob_gzhandler");
date_default_timezone_set("Africa/Algiers");
include "Functions.php";
$conn = openConnection();
$query = "SELECT LockDate, MACAddress, UseShiftRoster, NightShiftMaxOutTime, SRDay FROM OtherSettingMaster";
$main_result = selectData($conn, $query);
$txtLockDate = $main_result[0];
$txtMACAddress = $main_result[1];
if (checkMAC($conn) == false) {
    print "Un Registered Application. Process Terminated.";
    exit;
}
if (noTASoftware("", $txtMACAddress)) {
    print "Un Registered Application. Process Terminated.";
    exit;
}
$query = "UPDATE AttendanceMaster SET Normal = (group_min*60) WHERE Normal = 0 AND Overtime = 0 AND ((EarlyIn > 0 AND EarlyOut > 0) OR (LateIn > 0 AND LateOut > 0)) AND ADate > " . $txtLockDate;
updateData($conn, $query, true);

?>