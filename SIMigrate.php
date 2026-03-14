<?php


error_reporting(E_ERROR);
date_default_timezone_set("Africa/Algiers");
ob_start("ob_gzhandler");
include "Functions.php";
$conn = openConnection();
if (checkMAC($conn) == false) {
    print "Un Registered Application. Process Terminated.";
    exit;
}
$query = "SELECT EX1, DBType, DBIP, DBName, DBUser, DBPass, MACAddress, LockDate FROM OtherSettingMaster";
$result = selectData($conn, $query);
$txtDBIP = $result[2];
$txtDBName = $result[3];
$txtDBUser = $result[4];
$txtDBPass = $result[5];
$txtMAC = $result[6];
$txtLockDate = $result[7];
$dd = addZero(substr($txtLockDate, 6, 2) * 1 + 1, 2);
$mm = substr($txtLockDate, 4, 2) * 1;
$yy = substr($txtLockDate, 0, 4) * 1;
if ($mm == 1) {
    $mm = 12;
    $yy = $yy - 1;
} else {
    $mm = addZero(substr($txtLockDate, 4, 2) * 1 - 1, 2);
}
$this_date = $yy . $mm . $dd;
$target = "migrate";
mkdir($target, 448);
mkdir($target . "\\backup", 448);
$files = glob($target . "\\*");
foreach ($files as $file) {
    if (is_file($file)) {
        copy($file, $target . "\\backup\\" . $file);
        unlink($file);
    }
}
$file_name_ = "Leaves.csv";
$file_name = "migrate\\" . $file_name_;
$handle = fopen($file_name, "w");
fwrite($handle, $data);
$query = "SELECT tuser.phone, AttendanceMaster.ADate FROM AttendanceMaster, FlagTitle, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND AttendanceMaster.Flag = FlagTitle.Flag AND FlagTitle.Title = 'ANUL' AND AttendanceMaster.ADate >= '" . $this_date . "' AND AttendanceMaster.ADate <= '" . $txtLockDate . "' ORDER BY AttendanceMaster.EmployeeID";
$result = mysqli_query($conn, $query);
for ($count = 0; $cur = mysqli_fetch_row($result); $count++) {
    $data = $cur[0] . ",ANUL,Annual Leave," . str_replace("/", ".", displayDate($cur[1])) . "," . str_replace("/", ".", displayDate($cur[1])) . "\r\n";
    fwrite($handle, $data);
}
$query = "SELECT tuser.phone, AttendanceMaster.ADate FROM AttendanceMaster, FlagTitle, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND AttendanceMaster.Flag = FlagTitle.Flag AND FlagTitle.Title = 'CASL' AND AttendanceMaster.ADate >= '" . $this_date . "' AND AttendanceMaster.ADate <= '" . $txtLockDate . "' ORDER BY AttendanceMaster.EmployeeID";
$result = mysqli_query($conn, $query);
for ($count = 0; $cur = mysqli_fetch_row($result); $count++) {
    $data = $cur[0] . ",CASL,Casual leave," . str_replace("/", ".", displayDate($cur[1])) . "," . str_replace("/", ".", displayDate($cur[1])) . "\r\n";
    fwrite($handle, $data);
}
$query = "SELECT tuser.phone, AttendanceMaster.ADate FROM AttendanceMaster, FlagTitle, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND AttendanceMaster.Flag = FlagTitle.Flag AND FlagTitle.Title = 'EXAM' AND AttendanceMaster.ADate >= '" . $this_date . "' AND AttendanceMaster.ADate <= '" . $txtLockDate . "' ORDER BY AttendanceMaster.EmployeeID";
$result = mysqli_query($conn, $query);
for ($count = 0; $cur = mysqli_fetch_row($result); $count++) {
    $data = $cur[0] . ",EXAM,Exam Leave," . str_replace("/", ".", displayDate($cur[1])) . "," . str_replace("/", ".", displayDate($cur[1])) . "\r\n";
    fwrite($handle, $data);
}
$query = "SELECT tuser.phone, AttendanceMaster.ADate FROM AttendanceMaster, FlagTitle, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND AttendanceMaster.Flag = FlagTitle.Flag AND FlagTitle.Title = 'LWOP' AND AttendanceMaster.ADate >= '" . $this_date . "' AND AttendanceMaster.ADate <= '" . $txtLockDate . "' ORDER BY AttendanceMaster.EmployeeID";
$result = mysqli_query($conn, $query);
for ($count = 0; $cur = mysqli_fetch_row($result); $count++) {
    $data = $cur[0] . ",LWOP,Leave Without Pay," . str_replace("/", ".", displayDate($cur[1])) . "," . str_replace("/", ".", displayDate($cur[1])) . "\r\n";
    fwrite($handle, $data);
}
$query = "SELECT tuser.phone, AttendanceMaster.ADate FROM AttendanceMaster, FlagTitle, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND AttendanceMaster.Flag = FlagTitle.Flag AND FlagTitle.Title = 'MATL' AND AttendanceMaster.ADate >= '" . $this_date . "' AND AttendanceMaster.ADate <= '" . $txtLockDate . "' ORDER BY AttendanceMaster.EmployeeID";
$result = mysqli_query($conn, $query);
for ($count = 0; $cur = mysqli_fetch_row($result); $count++) {
    $data = $cur[0] . ",MATL,Maternity Leave," . str_replace("/", ".", displayDate($cur[1])) . "," . str_replace("/", ".", displayDate($cur[1])) . "\r\n";
    fwrite($handle, $data);
}
$query = "SELECT tuser.phone, AttendanceMaster.ADate FROM AttendanceMaster, FlagTitle, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND AttendanceMaster.Flag = FlagTitle.Flag AND FlagTitle.Title = 'SLFP' AND AttendanceMaster.ADate >= '" . $this_date . "' AND AttendanceMaster.ADate <= '" . $txtLockDate . "' ORDER BY AttendanceMaster.EmployeeID";
$result = mysqli_query($conn, $query);
for ($count = 0; $cur = mysqli_fetch_row($result); $count++) {
    $data = $cur[0] . ",SLFP,Sick Leave Full Pay," . str_replace("/", ".", displayDate($cur[1])) . "," . str_replace("/", ".", displayDate($cur[1])) . "\r\n";
    fwrite($handle, $data);
}
$query = "SELECT tuser.phone, AttendanceMaster.ADate FROM AttendanceMaster, FlagTitle, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND AttendanceMaster.Flag = FlagTitle.Flag AND FlagTitle.Title = 'SLHP' AND AttendanceMaster.ADate >= '" . $this_date . "' AND AttendanceMaster.ADate <= '" . $txtLockDate . "' ORDER BY AttendanceMaster.EmployeeID";
$result = mysqli_query($conn, $query);
for ($count = 0; $cur = mysqli_fetch_row($result); $count++) {
    $data = $cur[0] . ",SLHP,Sick Leave Half Pay," . str_replace("/", ".", displayDate($cur[1])) . "," . str_replace("/", ".", displayDate($cur[1])) . "\r\n";
    fwrite($handle, $data);
}
$query = "SELECT tuser.phone, AttendanceMaster.ADate FROM AttendanceMaster, FlagTitle, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND AttendanceMaster.Flag = FlagTitle.Flag AND FlagTitle.Title = 'STFP' AND AttendanceMaster.ADate >= '" . $this_date . "' AND AttendanceMaster.ADate <= '" . $txtLockDate . "' ORDER BY AttendanceMaster.EmployeeID";
$result = mysqli_query($conn, $query);
for ($count = 0; $cur = mysqli_fetch_row($result); $count++) {
    $data = $cur[0] . ",STFP,Study Leave with Pay," . str_replace("/", ".", displayDate($cur[1])) . "," . str_replace("/", ".", displayDate($cur[1])) . "\r\n";
    fwrite($handle, $data);
}
$query = "SELECT tuser.phone, AttendanceMaster.ADate FROM AttendanceMaster, FlagTitle, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND AttendanceMaster.Flag = FlagTitle.Flag AND FlagTitle.Title = 'STNP' AND AttendanceMaster.ADate >= '" . $this_date . "' AND AttendanceMaster.ADate <= '" . $txtLockDate . "' ORDER BY AttendanceMaster.EmployeeID";
$result = mysqli_query($conn, $query);
for ($count = 0; $cur = mysqli_fetch_row($result); $count++) {
    $data = $cur[0] . ",STNP,Study Leave without Pay," . str_replace("/", ".", displayDate($cur[1])) . "," . str_replace("/", ".", displayDate($cur[1])) . "\r\n";
    fwrite($handle, $data);
}
if ($count < 2) {
    unlink($file_name);
}
fclose($handle);
$file_name_ = "Overtime.csv";
$file_name = "migrate\\" . $file_name_;
$handle = fopen($file_name, "w");
fwrite($handle, $data);
$query = "SELECT tuser.phone, AttendanceMaster.ADate, AttendanceMaster.AOvertime FROM AttendanceMaster, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND AttendanceMaster.Day <> AttendanceMaster.OT2 AND AttendanceMaster.Flag <> 'Purple' AND AttendanceMaster.AOvertime > 0 AND AttendanceMaster.ADate >= '" . $this_date . "' AND AttendanceMaster.ADate <= '" . $txtLockDate . "' ORDER BY AttendanceMaster.EmployeeID";
$result = mysqli_query($conn, $query);
for ($count = 0; $cur = mysqli_fetch_row($result); $count++) {
    $data = $cur[0] . ",7000,Overtime for Mon - Sat," . str_replace("/", ".", displayDate($cur[1])) . "," . round($cur[2] / 3600, 1) . "\r\n";
    fwrite($handle, $data);
}
$query = "SELECT tuser.phone, AttendanceMaster.ADate, AttendanceMaster.AOvertime FROM AttendanceMaster, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND (AttendanceMaster.Day = AttendanceMaster.OT2 OR AttendanceMaster.Flag = 'Purple') AND AttendanceMaster.AOvertime > 0 AND AttendanceMaster.ADate >= '" . $this_date . "' AND AttendanceMaster.ADate <= '" . $txtLockDate . "' ORDER BY AttendanceMaster.EmployeeID";
$result = mysqli_query($conn, $query);
for ($count = 0; $cur = mysqli_fetch_row($result); $count++) {
    $data = $cur[0] . ",7010,Overtime for Sun - PH," . str_replace("/", ".", displayDate($cur[1])) . "," . round($cur[2] / 3600, 1) . "\r\n";
    fwrite($handle, $data);
}
if ($count < 2) {
    unlink($file_name);
}
fclose($handle);
$query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('SIMigrate', " . insertToday() . ", '" . getNow() . "')";
updateData($conn, $query, true);

?>