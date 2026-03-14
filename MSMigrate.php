<?php


ob_start("ob_gzhandler");
error_reporting(E_ALL);
date_default_timezone_set("Africa/Algiers");
include "Functions.php";
$conn = openConnection();
$iconn = openIConnection();
$query = "SELECT DBIP, DBName, DBUser, DBPass, EmployeeCodeLength, MACAddress FROM OtherSettingMaster";
$main_result = selectData($conn, $query);
$txtDBIP = $main_result[0];
$txtDBName = $main_result[1];
$txtDBUser = $main_result[2];
$txtDBPass = $main_result[3];
$txtECodeLength = $main_result[4];
$txtMAC = encryptDecrypt($main_result[5]);
if (checkMAC($conn)) {
    $aconn = mssql_connection("127.0.0.1", "NitgenAccessManager", "sa", "stallion");
    if ($aconn != "") {
        $query = "SELECT e_idno FROM tenter";
        $result = selectData($conn, $query);
        $last_ed = $result[0];
        if ($last_ed == "") {
            $last_ed = 0;
        }
        $query = "SELECT MAX(IndexKey) FROM NGAC_AUTHLOG";
        $last_result = mssql_query($query, $aconn);
        $last_cur = mssql_fetch_row($last_result);
        $max_ed = $last_cur[0];
        $query = "SELECT CONVERT(VARCHAR(19), NGAC_AUTHLOG.TransactionTime, 120), NGAC_AUTHLOG.UserID, NGAC_AUTHLOG.TerminalID FROM NGAC_AUTHLOG WHERE NGAC_AUTHLOG.UserID <> '' AND NGAC_AUTHLOG.IndexKey > " . $last_ed . " AND NGAC_AUTHLOG.IndexKey <= " . $max_ed . " AND Authresult = 0 ORDER BY NGAC_AUTHLOG.IndexKey";
        $result = mssql_query($query, $aconn);
        while ($cur = mssql_fetch_row($result)) {
            $query = "INSERT INTO tenter (e_date, e_time, g_id, e_id, e_group) VALUES ('" . insertParadoxDate(substr($cur[0], 0, 10)) . "', '" . insertTime(substr($cur[0], 11, 8)) . "', '" . $cur[2] . "', '" . $cur[1] / 1 . "', '419')";
            updateIData($iconn, $query, true);
        }
        $query = "UPDATE tenter, tuser SET tenter.e_group = tuser.group_id WHERE tenter.e_group = '419' AND tenter.e_id = tuser.id AND tuser.group_id > 1";
        if (updateIData($iconn, $query, true)) {
            $query = "UPDATE tenter SET e_idno ='" . $max_ed . "'";
            updateIData($iconn, $query, true);
        }
        $query = "SELECT NGAC_USERINFO.ID, NGAC_USERINFO.Name FROM NGAC_USERINFO ";
        $result = mssql_query($query, $aconn);
        while ($cur = mssql_fetch_row($result)) {
            $query = "INSERT INTO tuser (id, name, dept, company, idno, remark, datelimit, reg_date, pwd, phone, group_id) VALUES ('" . $cur[0] / 1 . "', '" . replaceString($cur[1], false) . "', '.', '.', '.', '.', 'N2001010120010101', '" . insertToday() . getNow() . "', '.', '.', 0)";
            updateIData($iconn, $query, true);
        }
        $query = "SELECT NGAC_TERMINAL.ID, NGAC_TERMINAL.Name FROM NGAC_TERMINAL WHERE NGAC_TERMINAL.Name <> '' ORDER BY NGAC_TERMINAL.ID";
        $result = mssql_query($query, $aconn);
        while ($cur = mssql_fetch_row($result)) {
            $query = "INSERT INTO tgate (id, name) VALUES ('" . $cur[0] . "', '" . replaceString($cur[1], false) . "')";
            updateIData($iconn, $query, true);
        }
        $a_1 = "";
        $a_2 = "";
        $a_1[0] = "0002";
        $a_1[1] = "0010";
        $a_1[2] = "0017";
        $a_1[3] = "0028";
        $a_1[4] = "0031";
        $a_1[5] = "0032";
        $a_1[6] = "0033";
        $a_1[7] = "0035";
        $a_1[8] = "0036";
        $a_1[9] = "0037";
        $a_1[10] = "0038";
        $a_1[11] = "0039";
        $a_1[12] = "0040";
        $a_1[13] = "0041";
        $a_1[14] = "0029";
        $a_1[15] = "0099";
        $a_1[16] = "0100";
        $a_1[17] = "0101";
        $a_1[18] = "0102";
        $a_1[19] = "0043";
        $a_1[20] = "0042";
        $a_1[21] = "0044";
        $a_1[22] = "0045";
        $a_1[23] = "0047";
        $a_2[0] = "ORILE-HYUNDAI";
        $a_2[1] = "ONWARD";
        $a_2[2] = "KOFO(HYUNDAI%";
        $a_2[3] = "BENIN-FISH";
        $a_2[4] = "ADL";
        $a_2[5] = "ISOLO";
        $a_2[6] = "PHC-COMMO%";
        $a_2[7] = "VON-NISSAN";
        $a_2[8] = "PHC-THP";
        $a_2[9] = "ABUJA(AUTO%";
        $a_2[10] = "KOFO(VW%";
        $a_2[11] = "KOFO-NISSAN";
        $a_2[12] = "IJESHA-AUTO";
        $a_2[13] = "CALABAR-AUTO";
        $a_2[14] = "POPULAR-FISH";
        $a_2[15] = "Auto-ALL";
        $a_2[16] = "ALL";
        $a_2[17] = "COMM-ALL";
        $a_2[18] = "FISH-ALL";
        $a_2[19] = "VON-COMM";
        $a_2[20] = "Mile2-THP";
        $a_2[21] = "VON-Auto/Transport";
        $a_2[22] = "VON-TransportWS";
        $a_2[23] = "PHC-VW";
        $i = 0;
        while ($i < count($a_1)) {
            $query = "UPDATE UNIS.tuser, Access.tuser SET UNIS.tuser.C_AccessGroup = '" . $a_1[$i] . "' WHERE Access.tuser.dept LIKE '" . $a_2[$i] . "' AND UNIS.tuser.L_ID = Access.tuser.ID ";
            if (updateIData($iconn, $query, true)) {
                $i++;
            } else {
                echo "Error: " . $query;
                exit;
            }
        }
        $query = "UPDATE UNIS.tuser, Access.tuser SET UNIS.tuser.C_AccessGroup = '0001' WHERE UNIS.tuser.C_AccessGroup = '****' ";
        if (updateIData($iconn, $query, true)) {
            $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('MS Migrate', " . insertToday() . ", '" . getNow() . "')";
            if (updateIData($iconn, $query, true)) {
                return 1;
            }
            echo "Error: " . $query;
            exit;
        }
        echo "Error: " . $query;
        exit;
    }
    print "Connection to External Database NOT available. Process Terminated.";
    exit;
}
print "Un Registered Application. Process Terminated.";
exit;
function nitgenCode($data)
{
    $data = $data . "000000000";
    $data = addZero($data, 15);
    return $data;
}

?>