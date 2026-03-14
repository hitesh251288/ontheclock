<?php


ob_start("ob_gzhandler");
error_reporting(E_ALL);
date_default_timezone_set("Africa/Algiers");
include "Functions.php";
$conn = openConnection();
$iconn = openIConnection();
$query = "SELECT DBIP, DBName, DBUser, DBPass, EmployeeCodeLength, MACAddress, LockDate FROM OtherSettingMaster";
$main_result = selectData($conn, $query);
$txtDBIP = $main_result[0];
$txtDBName = $main_result[1];
$txtDBUser = $main_result[2];
$txtDBPass = $main_result[3];
$txtECodeLength = $main_result[4];
$txtMAC = encryptDecrypt($main_result[5]);
$txtLockDate = $main_result[6];
$txtFrom = substr($txtLockDate, 0, 6) . "01";
$txtTo = $txtLockDate;
$counter = 0;
if (checkMAC($conn)) {
    $aconn = mssql_connection("pzngip22", "jtest", "virdi", "virdi123");
    echo "\n\rConnected to MS SQL Database " . $aconn;
    if ($aconn != "") {
        $query = "TRUNCATE TABLE evirdi";
        mssql_query($query, $aconn);
        $query = "TRUNCATE TABLE trans16";
        mssql_query($query, $aconn);
        $query = "SELECT EmployeeID, DISTINCT(tuser.remark) FROM AttendanceMaster, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND LENGTH(tuser.remark) > 3 AND ADate >= " . $txtFrom . " AND ADate <= " . $txtTo;
        $result = mysqli_query($conn, $query);
        while ($cur = mysqli_fetch_row($result)) {
            $query = "INSERT INTO evirdi (approval_code, approval_by, employee_number, trans_date, trans_code, trans_type, time_in, time_out, break_time, analysis0, analysis1, analysis2, analysis3, analysis4, analysis5, analysis6, analysis7, analysis8, analysis9, staff_status, no_of_days, weekdays_overtime, public_overtime, wkend_overtime, overtime, hrs_worked, comment1, last_userid) VALUES ('Y', 'SYS', '" . $cur[1] . "', '" . displayParadoxDate(insertToday()) . " 00:00:00.000', 'S', 'TMSS', '', '', '', '', '', '', '', '', '', '', '', '', '', 'W', ";
            $abs = getASS($conn, $cur[0], displayDate($txtFrom), displayDate($txtTo));
            $sub_query = "SELECT SUM(LateIn), SUM(EarlyOut) FROM AttendanceMaster WHERE Day<>OT1 AND Day<>OT2 AND Flag<>'Purple' AND EmployeeID = " . $cur[0] . " AND ADate >= " . $txtFrom . " AND ADate <= " . $txtTo;
            $sub_result = selectData($conn, $sub_query);
            $abs_ = round($abs * 8 + $sub_result[0] / 3600 + $sub_result[1] / 3600, 2);
            $query .= "'" . $abs_ . "', ";
            $query .= "'0', ";
            $sub_query = "SELECT SUM(AOvertime) FROM AttendanceMaster WHERE (Flag='Purple' OR Day=OT2) AND EmployeeID = " . $cur[0] . " AND ADate >= " . $txtFrom . " AND ADate <= " . $txtTo;
            $sub_result = selectData($conn, $sub_query);
            $query .= "'" . round($sub_result[0] / 3600, 2) . "', ";
            $sub_query = "SELECT SUM(AOvertime) FROM AttendanceMaster WHERE Day<>OT2 AND Flag<>'Purple' AND EmployeeID = " . $cur[0] . " AND ADate >= " . $txtFrom . " AND ADate <= " . $txtTo;
            $sub_result = selectData($conn, $sub_query);
            $query .= "'" . round($sub_result[0] / 3600, 2) . "', ";
            $query .= " 0, '" . $abs_ . "', 'Upload from VIRDI', 'VIRDI')";
            if (mssql_query($query, $aconn)) {
                $counter++;
            } else {
                exit("MSSQL error: " . mssql_get_last_message() . "\n\rQuery - " . $query);
            }
        }
        if (0 < $counter) {
            $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('DB Migrate - User Records Inserted:" . $counter . "', " . insertToday() . ", '" . getNow() . "')";
            updateIData($iconn, $query, true);
        }
    } else {
        print "Connection to External Database NOT available. Process Terminated.";
        exit;
    }
} else {
    print "Un Registered Application. Process Terminated.";
    exit;
}
function nitgenCode($data)
{
    $data = $data . "000000000";
    $data = addZero($data, 15);
    return $data;
}

?>