<?php


ob_start("ob_gzhandler");
date_default_timezone_set("Africa/Algiers");
set_time_limit(0);
error_reporting(E_ALL);
include "Functions.php";
$conn = openConnection();
$iconn = openIConnection();
displayToday();
getNow();
print "\n\nScript Started: " . displayToday() . ", " . getNow() . " HRS";
flush();
if (checkMAC($conn)) {
    $success = false;
    $query = "SELECT EX1, MACAddress, CompanyName, DBType, DBName, DBUser, DBPass, MealCouponPrinterName FROM OtherSettingMaster";
    $result = selectData($conn, $query);
    $file_name = "";
    $archive_name = "";
    $unis_name = "";
    $comp = str_replace(" ", "-", getRegister($txtMACAddress, 4));
    if ($comp != "") {
        $file_name = "Virdi-" . date("dMy-His") . "-" . $comp;
        $archive_name = "Archive-" . date("dMy-His") . "-" . $comp;
        $unis_name = "UNIS-" . date("dMy-His") . "-" . $comp;
    }
    $mysqlpath = $result[7];
    exec("\"" . $mysqlpath . "\\mysqldump\" --user=" . decryptString($db_user) . " --password=" . decryptString($db_pass) . " " . decryptString($db_name) . ">" . $file_name . ".sql");
    if (filesize($file_name . ".sql") != 0) {
        rename($file_name . ".sql", $result[0] . "\\" . $file_name . ".sql");
        exec("\"" . $mysqlpath . "\\mysqldump\" --user=" . decryptString($db_user) . " --password=" . decryptString($db_pass) . " AccessArchive>" . $archive_name . ".sql");
        rename($archive_name . ".sql", $result[0] . "\\" . $archive_name . ".sql");
        exec("\"" . $mysqlpath . "\\mysqldump\" --user=unisuser --password=unisamho UNIS>" . $unis_name . ".sql");
        rename($unis_name . ".sql", $result[0] . "\\" . $unis_name . ".sql");
        $srcfile = $mysqlpath . "\\mysql.exe";
        $dstfile = "restore\\mysql.exe";
        copy($srcfile, $dstfile);
        $success = true;
    } else {
        unlink($file_name . ".sql");
        $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('MySQL BIN Path NOT Found. Unable to execute Backup.', " . insertToday() . ", '" . getNow() . "')";
        updateIData($iconn, $query, true);
        print "MySQL BIN Path NOT Found. Unable to execute Backup.";
    }
    if ($success) {
        $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('Backup', " . insertToday() . ", '" . getNow() . "')";
        updateIData($iconn, $query, true);
        print "Backup Complete";
    }
} else {
    print "Un Registered Application. Process Terminated.";
}
displayToday();
getNow();
print "\n\nScript Ended: " . displayToday() . ", " . getNow() . " HRS";
flush();

?>