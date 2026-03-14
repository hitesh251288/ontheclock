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
$query = "SELECT EX1, DBType, DBIP, DBName, DBUser, DBPass, MACAddress FROM OtherSettingMaster";
$result = selectData($conn, $query);
$txtDBIP = $result[2];
$txtDBName = $result[3];
$txtDBUser = $result[4];
$txtDBPass = $result[5];
$txtMAC = $result[6];
$this_date = getLastDay(insertToday(), 30);
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
for ($i = $this_date; $i <= getLastDay(insertToday(), 1); $i++) {
    if (checkdate(substr($i, 4, 2), substr($i, 6, 2), substr($i, 0, 4))) {
        $file_name_ = "lagos.txt_" . $i;
        for ($j = 0; $j < 7; $j++) {
            if ($j == 0) {
                $file_name_ = "lagos.txt_" . $i;
                $query = "SELECT DayMaster.e_id, DayMaster.TDate, DayMaster.Start, DayMaster.Close FROM DayMaster, tgroup WHERE DayMaster.TDate = '" . $i . "' AND DayMaster.group_id = tgroup.id AND tgroup.name LIKE '%HQ%' ORDER BY DayMaster.e_id";
            } else {
                if ($j == 1) {
                    $file_name_ = "obajana.txt_" . $i;
                    $query = "SELECT DayMaster.e_id, DayMaster.TDate, DayMaster.Start, DayMaster.Close FROM DayMaster, tgroup WHERE DayMaster.TDate = '" . $i . "' AND DayMaster.group_id = tgroup.id AND tgroup.name LIKE '%OBJ%' ORDER BY e_id";
                } else {
                    if ($j == 2) {
                        $file_name_ = "obt.txt_" . $i;
                        $query = "SELECT DayMaster.e_id, DayMaster.TDate, DayMaster.Start, DayMaster.Close FROM DayMaster, tgroup WHERE DayMaster.TDate = '" . $i . "' AND DayMaster.group_id = tgroup.id AND tgroup.name LIKE '%OBT%' ORDER BY e_id";
                    } else {
                        if ($j == 3) {
                            $file_name_ = "ibt.txt_" . $i;
                            $query = "SELECT DayMaster.e_id, DayMaster.TDate, DayMaster.Start, DayMaster.Close FROM DayMaster, tgroup WHERE DayMaster.TDate = '" . $i . "' AND DayMaster.group_id = tgroup.id AND tgroup.name LIKE '%IBT%' ORDER BY e_id";
                        } else {
                            if ($j == 4) {
                                $file_name_ = "zambia.txt_" . $i;
                                $query = "SELECT DayMaster.e_id, DayMaster.TDate, DayMaster.Start, DayMaster.Close FROM DayMaster, tgroup WHERE DayMaster.TDate = '" . $i . "' AND DayMaster.group_id = tgroup.id AND tgroup.name LIKE '%ZMB%' ORDER BY e_id";
                            } else {
                                if ($j == 5) {
                                    $file_name_ = "tanzania.txt_" . $i;
                                    $query = "SELECT DayMaster.e_id, DayMaster.TDate, DayMaster.Start, DayMaster.Close FROM DayMaster, tgroup WHERE DayMaster.TDate = '" . $i . "' AND DayMaster.group_id = tgroup.id AND tgroup.name LIKE '%TNZ%' ORDER BY e_id";
                                } else {
                                    if ($j == 6) {
                                        $file_name_ = "ghana.txt_" . $i;
                                        $query = "SELECT DayMaster.e_id, DayMaster.TDate, DayMaster.Start, DayMaster.Close FROM DayMaster, tgroup WHERE DayMaster.TDate = '" . $i . "' AND DayMaster.group_id = tgroup.id AND tgroup.name LIKE '%GHN%' ORDER BY e_id";
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $file_name = "migrate\\" . $file_name_;
            $handle = fopen($file_name, "w");
            $result = mysqli_query($conn, $query);
            for ($count = 0; $cur = mysqli_fetch_row($result); $count++) {
                $data = "";
                $trt = "";
                $code = "";
                $data = "#" . addZero($cur[0], 6) . "#" . displayDotDate($cur[1]) . "#" . displayVirdiTime($cur[2]) . "#P10#\r\n";
                fwrite($handle, $data);
                $data = "#" . addZero($cur[0], 6) . "#" . displayDotDate($cur[1]) . "#" . displayVirdiTime($cur[3]) . "#P20#\r\n";
                fwrite($handle, $data);
            }
            if ($count < 2) {
                unlink($file_name);
            }
            fclose($handle);
        }
    }
}
$query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('ODMigrate', " . insertToday() . ", '" . getNow() . "')";
updateData($conn, $query, true);

?>