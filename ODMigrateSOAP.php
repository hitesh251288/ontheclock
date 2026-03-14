<?php


error_reporting(E_ALL);
date_default_timezone_set("Africa/Algiers");
ob_start("ob_gzhandler");
include "Functions.php";
$conn = openConnection();
$iconn = openIConnection();
$url = "http://sappidev2.dangote-group.com:50000/dir/wsdl?p=sa/8b8c28e432ae34d4959245b8a46c06ca";
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
for ($i = $this_date; $i <= getLastDay(insertToday(), 1); $i++) {
    if (checkdate(substr($i, 4, 2), substr($i, 6, 2), substr($i, 0, 4))) {
        $plant = "1000";
        for ($j = 0; $j < 7; $j++) {
            if ($j == 0) {
                $plant = "1000";
                $query = "SELECT DayMaster.e_id, DayMaster.TDate, DayMaster.Start, DayMaster.Close FROM DayMaster, tgroup WHERE DayMaster.TDate = '" . $i . "' AND DayMaster.group_id = tgroup.id AND tgroup.name LIKE '%HQ%' ORDER BY DayMaster.e_id";
            } else {
                if ($j == 1) {
                    $plant = "1000";
                    $query = "SELECT DayMaster.e_id, DayMaster.TDate, DayMaster.Start, DayMaster.Close FROM DayMaster, tgroup WHERE DayMaster.TDate = '" . $i . "' AND DayMaster.group_id = tgroup.id AND tgroup.name LIKE '%OBJ%' ORDER BY e_id";
                } else {
                    if ($j == 2) {
                        $plant = "1000";
                        $query = "SELECT DayMaster.e_id, DayMaster.TDate, DayMaster.Start, DayMaster.Close FROM DayMaster, tgroup WHERE DayMaster.TDate = '" . $i . "' AND DayMaster.group_id = tgroup.id AND tgroup.name LIKE '%OBT%' ORDER BY e_id";
                    } else {
                        if ($j == 3) {
                            $plant = "1000";
                            $query = "SELECT DayMaster.e_id, DayMaster.TDate, DayMaster.Start, DayMaster.Close FROM DayMaster, tgroup WHERE DayMaster.TDate = '" . $i . "' AND DayMaster.group_id = tgroup.id AND tgroup.name LIKE '%IBT%' ORDER BY e_id";
                        } else {
                            if ($j == 4) {
                                $plant = "1210";
                                $query = "SELECT DayMaster.e_id, DayMaster.TDate, DayMaster.Start, DayMaster.Close FROM DayMaster, tgroup WHERE DayMaster.TDate = '" . $i . "' AND DayMaster.group_id = tgroup.id AND tgroup.name LIKE '%ZMB%' ORDER BY e_id";
                            } else {
                                if ($j == 5) {
                                    $plant = "1230";
                                    $query = "SELECT DayMaster.e_id, DayMaster.TDate, DayMaster.Start, DayMaster.Close FROM DayMaster, tgroup WHERE DayMaster.TDate = '" . $i . "' AND DayMaster.group_id = tgroup.id AND tgroup.name LIKE '%TNZ%' ORDER BY e_id";
                                } else {
                                    if ($j == 6) {
                                        $plant = "1100";
                                        $query = "SELECT DayMaster.e_id, DayMaster.TDate, DayMaster.Start, DayMaster.Close FROM DayMaster, tgroup WHERE DayMaster.TDate = '" . $i . "' AND DayMaster.group_id = tgroup.id AND tgroup.name LIKE '%GHN%' ORDER BY e_id";
                                    }
                                }
                            }
                        }
                    }
                }
            }
            try {
                $client = new SoapClient($url);
                $result = mysqli_query($conn, $query);
                $count = 0;
                for ($params = ""; $cur = mysqli_fetch_row($result); $count++) {
                    $params[$count] = array("Persno" => $cur[0], "Plant" => $plant, "Date" => displayParadoxDate($cur[1]), "Time" => displayVirdiTime($cur[2]), "Event_type" => "clock_in");
                    $count++;
                    $params[$count] = array("Persno" => $cur[0], "Plant" => $plant, "Date" => displayParadoxDate($cur[1]), "Time" => displayVirdiTime($cur[3]), "Event_type" => "clock_in");
                }
                $response = $client->__soapCall("si_time_event_abs_sync", array($params));
                var_dump($response);
            } catch (Exception $e) {
                echo "\n\rException Error - " . $e->getMessage();
            }
        }
    }
}
$query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('ODMigrate SOAP', " . insertToday() . ", '" . getNow() . "')";
updateIData($iconn, $query, true);

?>