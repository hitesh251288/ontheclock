<?php


ob_start("ob_gzhandler");
include "Functions.php";
$current_module = "31";
set_time_limit(900);
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=ShiftRoster.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$iconn = openIConnection();
$txtID = $_GET["txtID"];
$txtDate = $_GET["txtDate"];
$txtShift = $_GET["txtShift"];
$txtOldShift = $_GET["txtOldShift"];
$act = $_GET["act"];
if ($act == "editShift" && $txtID != "" && $txtDate != "" && $txtShift != "" && $txtShift != $txtOldShift) {
    $query = "UPDATE ShiftRoster SET e_group = '" . $txtShift . "' WHERE e_group = '" . $txtOldShift . "' AND e_date = '" . $txtDate . "' AND e_id = '" . $txtID . "'";
    updateIData($iconn, $query, true);
    $text = "Changed Shift Roster For ID: " . $txtID . " for Date: " . displayDate($txtDate) . " from Shift: " . $txtOldShift . " to Shift: " . $txtShift;
    $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
    updateIData($iconn, $query, true);
    $query = "SELECT name from tgroup where id = " . $txtShift;
    $result = selectData($conn, $query);
    print $result[0];
}

?>