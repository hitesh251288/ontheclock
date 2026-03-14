<?php


ob_start("ob_gzhandler");
include "Functions.php";
$conn = openConnection();
$iconn = openIConnection();
$query = "SELECT RotateShift, RotateShiftNextDay, LockDate FROM OtherSettingMaster";
$result = selectData($conn, $query);
if ($result[0] == "No") {
    $query = "UPDATE tenter, tuser SET tenter.e_group = tuser.group_id WHERE (tenter.e_group = 0 OR tenter.e_group = 1 OR tenter.e_group = 999) AND tenter.e_id = tuser.id AND tenter.p_flag = 0 AND tenter.e_date > '" . $result[2] . "'";
    updateIData($iconn, $query, true);
} else {
    if ($result[0] == "Yes") {
        $query = "SELECT MAX(RDate), RTime FROM shiftrotatelog GROUP BY RTime ORDER BY RTime";
        $result = mysqli_query($conn, $query);
        while ($cur = mysqli_fetch_row($result)) {
            $query = "UPDATE tenter, tuser SET tenter.e_group = tuser.group_id WHERE (tenter.e_group = 0 OR tenter.e_group = 1 OR tenter.e_group = 999) AND tenter.e_id = tuser.id AND tenter.e_date >= '" . $cur[0] . "' AND tenter.e_time > '" . $cur[1] . "00' AND tenter.p_flag = 0 AND tenter.e_date > '" . $result[2] . "' ";
        }
        updateIData($iconn, $query, true);
    }
}
$query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('Shift Synch', " . insertToday() . ", '" . getNow() . "')";
updateIData($iconn, $query, true);

?>