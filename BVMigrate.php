<?php


error_reporting(E_ALL);
date_default_timezone_set("Africa/Algiers");
ob_start("ob_gzhandler");
include "Functions.php";
$conn = openConnection();
if (checkMAC($conn) == false) {
    print "Un Registered Application. Process Terminated.";
    exit;
}
$query = "SELECT EX1, DBType, DBIP, DBName, DBUser, DBPass, MACAddress, LockDate, EmployeeCodeLength FROM OtherSettingMaster";
$result = selectData($conn, $query);
$txtDBIP = $result[2];
$txtDBName = $result[3];
$txtDBUser = $result[4];
$txtDBPass = $result[5];
$txtMAC = $result[6];
$txtLockDate = $result[7];
$txtLength = $result[8];
$this_date = getLastDay(insertToday(), 1);
$data_file = "";
$data_records = "";
$file_count = 0;
$record_count = 0;
for ($i = $txtLockDate; $i <= $this_date; $i++) {
    if (checkdate(substr($i, 4, 2), substr($i, 6, 2), substr($i, 0, 4))) {
        $file_name_ = $i . ".txt";
        $file_name = "C:\\GHBDATA\\" . $file_name_;
        $file_count++;
        unlink($file_name);
        $handle = fopen($file_name, "w");
        $query = "SELECT e_id, Start, Close FROM DayMaster WHERE TDate = '" . $i . "' ORDER BY e_id";
        for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $record_count++) {
            $data_in = "P100001" . $i . $cur[1] . $i . $cur[1] . addZero($cur[0], $txtLength) . "\r\n";
            $data_out = "P200001" . $i . $cur[2] . $i . $cur[2] . addZero($cur[0], $txtLength) . "\r\n";
            fwrite($handle, $data_in);
            fwrite($handle, $data_out);
        }
        fclose($handle);
    }
}
$query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('BVMigrate - Files Inserted: " . $file_count . " - Records Inserted - " . $record_count . "', " . insertToday() . ", '" . getNow() . "')";
updateData($conn, $query, true);

?>