<?php


ob_start("ob_gzhandler");
error_reporting(E_ALL);
include "Functions.php";
$conn = odbc_connect("UNIS", "unisusr", "unisamho");
$co_code = 1;
$txtDBIP = "127.0.0.1,1433";
$oconn = mssql_connection($txtDBIP, "CO1", "sa", "bitplus@123");
if ($oconn != "") {
    $query = "SELECT C_Date, C_Time, L_TID, L_UID, L_Mode FROM tEnter";
    $result = odbc_exec($conn, $query);
    while (odbc_fetch_into($result, $cur)) {
        $ta_query = "INSERT INTO tbl_TAData (NodeId, ICardNo, ShiftDate, CoCode, EmpCode, InOutTime, InOut, CreatedBy, CreatedON, ModifiedBy, ModifiedON) VALUES ('" . $cur[2] . "', '" . addZero($cur[3], 10) . "', '" . displayParadoxDate($cur[0]) . " " . displayVirdiTime($cur[1]) . ":000', 1, '0', '" . displayTime($cur[1]) . "', ";
        if ($cur[4] == 1) {
            $ta_query .= " 'I', ";
        } else {
            $ta_query .= " 'O', ";
        }
        $ta_query .= " 1, '" . displayParadoxDate($cur[0]) . " " . displayVirdiTime($cur[1]) . ":000', 1, '" . displayParadoxDate($cur[0]) . " " . displayVirdiTime($cur[1]) . ":000') ";
        mssql_query($ta_query, $oconn);
    }
    $ta_query = "UPDATE tbl_TAData SET EmpCode = t1.EmpCode FROM tblEmployee t1 INNER JOIN tbl_TAData AS t2 ON t2.ICardNo = t1.ICardNo";
    mssql_query($ta_query, $oconn);
}

?>