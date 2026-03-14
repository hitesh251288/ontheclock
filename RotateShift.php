<?php


ob_start("ob_gzhandler");
date_default_timezone_set("Africa/Algiers");
error_reporting(E_ALL);
include "Functions.php";
$conn = openIConnection();
$iconn = openIConnection();
$jconn = openIConnection();
$kconn = openIConnection();
$lconn = openIConnection();
$uconn = mysqli_connect("127.0.0.1", "unisuser", "unisamho", "UNIS");
if (noTASoftware($conn, "")) {
    print "Un Registered Application. Process Terminated.";
    exit;
}
var_dump($argv);
$idf_ = $argv[1];
print "\n\rStarting the Shift Rotation Cycle";
if ($idf_ == "ALL" || $idf_ == "") {
    $query = "SELECT DISTINCT(idf) FROM ShiftChangeMaster WHERE AE = 1";
} else {
    $query = "SELECT DISTINCT(idf) FROM ShiftChangeMaster WHERE AE = 1 AND idf = " . $idf_;
}
$insert_flag = true;
$nextDate = insertToday();
$main_result = mysqli_query($conn, $query);
while ($idf_cur = mysqli_fetch_row($main_result)) {
    //$v_id = "";
	$v_id = array();
    $i = 0;
    $query = "SELECT id FROM ShiftChangeMaster WHERE idf = " . $idf_cur[0] . " ORDER BY ShiftChangeID";
	$result = mysqli_query($iconn, $query);
	if ($result && mysqli_num_rows($result) > 0) {
		while ($id_cur = mysqli_fetch_row($result)) {
			$v_id[$i] = $id_cur[0];
			$i++;
		}
	}
    /*for ($result = mysqli_query($iconn, $query); $id_cur = mysqli_fetch_row($result); $i++) {
        $v_id[$i] = $id_cur[0];
    }*/
	if (!empty($v_id) && count($v_id) > 1) {
    for ($j = 0; $j < count($v_id) - 1; $j++) {
    //for ($j = 0; $j < sizeOf($v_id) - 1; $j++) {
        $query = "UPDATE tuser SET group_id = " . $v_id[$j + 1] * 1000 . " WHERE group_id = " . $v_id[$j];
        if (updateIData($jconn, $query, true)) {
            $query = "SELECT DISTINCT(RotateShiftNextDay) FROM ShiftChangeMaster WHERE idf = " . $idf_cur[0] . " ";
            $result = selectData($kconn, $query);
            if (is_numeric($result[0]) == false) {
                $result[0] = insertToday();
            }
            $query = "INSERT INTO ShiftRotateLog (RDate, RTime, ShiftFrom, ShiftTo) VALUES (" . $result[0] . ", " . getNow() . ", " . $v_id[$j] . ", " . $v_id[$j + 1] . ")";
            if (updateIData($lconn, $query, true)) {
                $nextDate = getNextDay($result[0], 7);
            } else {
                $insert_flag = false;
            }
        } else {
            $insert_flag = false;
        }
    }
	}
    if ($insert_flag) {
        $query = "UPDATE tuser SET group_id = (group_id/1000) WHERE group_id > 1000";
        if (updateIData($iconn, $query, true)) {
            $query = "UPDATE temploye SET C_Work = (C_Work/1000) WHERE C_Work > 1000";
            updateIData($uconn, $query, true);
            $query = "UPDATE ShiftChangeMaster SET RotateShiftNextDay = " . $nextDate . " WHERE idf = " . $idf_cur[0];
            if (!updateIData($jconn, $query, true)) {
                $insert_flag = false;
            }
        } else {
            $insert_flag = false;
        }
    }
    if (!($insert_flag && mysqli_commit($iconn) && sra($conn, $jconn, $kconn, getLastDay($nextDate, 7), $idf_cur[0]))) {
    }
}
mysqli_close($conn);
mysqli_close($iconn);
mysqli_close($jconn);
mysqli_close($kconn);
mysqli_close($lconn);
mysqli_close($uconn);
print "\n\rProcess Complete";

?>