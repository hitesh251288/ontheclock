<?php


error_reporting(E_ALL);
ob_start("ob_gzhandler");
date_default_timezone_set("Africa/Algiers");
include "Functions.php";
set_time_limit(0);
$conn = openConnection();
$query = "SELECT LockDate, MACAddress, DBType, DBIP, DBName, DBUser, DBPass FROM OtherSettingMaster";
$main_result = selectData($conn, $query);
$txtLockDate = $main_result[0];
$txtMACAdress = $main_result[1];
$lstDBType = $main_result[2];
$txtDBIP = $main_result[3];
$txtDBName = $main_result[4];
$txtDBUser = $main_result[5];
$txtDBPass = $main_result[6];
$oconn = "";
$oconn = oracle_connection("192.168.0.9", "ORCL", "pay_igm", "payoff");
echo "35: " . $oconn;
exit;
function getIndomieEmployeeCode($txtMACAdress, $id, $staffType)
{
    $emp_id = "";
    $staffType = trim(strtoupper($staffType));
    if ($txtMACAdress == "F4-CE-46-03-62-A1") {
        $e_prefix = "A";
        switch (substr($id, 0, 2)) {
            case "11":
                $e_prefix = "B";
                break;
            case "12":
                $e_prefix = "C";
                break;
            case "13":
                $e_prefix = "D";
                break;
            case "14":
                $e_prefix = "E";
                break;
            case "15":
                $e_prefix = "F";
                break;
            case "16":
                $e_prefix = "G";
                break;
            case "17":
                $e_prefix = "H";
                break;
            case "18":
                $e_prefix = "I";
                break;
            case "19":
                $e_prefix = "J";
                break;
            case "20":
                $e_prefix = "K";
                break;
            case "21":
                $e_prefix = "L";
                break;
            case "22":
                $e_prefix = "M";
                break;
            case "23":
                $e_prefix = "N";
                break;
            case "24":
                $e_prefix = "O";
                break;
            case "25":
                $e_prefix = "P";
                break;
            case "26":
                $e_prefix = "Q";
                break;
            case "27":
                $e_prefix = "R";
                break;
            case "28":
                $e_prefix = "S";
                break;
            case "29":
                $e_prefix = "T";
                break;
            case "30":
                $e_prefix = "U";
                break;
            case "31":
                $e_prefix = "V";
                break;
            case "32":
                $e_prefix = "W";
                break;
            case "33":
                $e_prefix = "X";
                break;
            case "34":
                $e_prefix = "Y";
                break;
            case "35":
                $e_prefix = "Z";
                break;
        }
        $emp_id = $e_prefix . "" . substr($id, 2, 4);
    } else {
        if ($staffType == "CON" || $staffType == "APPRENTICE" || $staffType == "CONT" || $staffType == "IT" || $staffType == "NYSC") {
            $emp_id = $id;
        } else {
            $emp_id = "E" . $id;
        }
    }
    return $emp_id;
}

?>