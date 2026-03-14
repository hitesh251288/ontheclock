<?php
//include "Functions.php";
$conn = mysqli_connect('localhost', 'root', 'namaste', 'access_lftz');
foreach($_POST['checkboxes'] as $flagAllData){
    $queryFlag = "INSERT INTO FlagDayRotation (e_id, e_date, g_id, Flag, Rotate, Remark, OT, group_id, OTH) "
        . "VALUES (" . $flagAllData['e_id'] . ", " . $flagAllData['value'] . ", " . $flagAllData['deptTerminal'] . ", "
        . "'" . $flagAllData['flag'] . "', 0, '" . $flagAllData['remark'] . "', '" . $flagAllData['ot'] . "', '" . 
            $flagAllData['group_id'] . "', '" . $flagAllData['oth'] . "')";
//    $result = updateIData($iconn, $queryFlag, true);
    
$resultData = mysqli_query($conn, $queryFlag);
}       
if(!$resultData) {
    die('Error inserting data: ' . mysqli_error($conn));
}
?>