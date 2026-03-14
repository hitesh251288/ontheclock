<?php


ob_start("ob_gzhandler");
error_reporting(E_ALL);
date_default_timezone_set("Africa/Algiers");
include "Functions.php";
set_time_limit(0);
$conn = openConnection();
if (checkMAC($conn) == false) {
    print "Un Registered Application. Process Terminated.";
    exit;
}
$iconn = openIConnection();
$query = "SELECT LockDate, MACAddress, DBType, DBIP, DBName, DBUser, DBPass FROM OtherSettingMaster";
$main_result = selectData($conn, $query);
$txtMACAddress = $main_result[1];
$db = "";
if (getRegister($txtMACAddress, 7) == "65") {
    $mconn = mssql_connection("localhost", "HRM", "sa", "bitplus@123");
    $db[0] = "abuja";
    $db[1] = "asaba";
    $db[2] = "esc";
    $db[3] = "ibadan";
    $db[4] = "ikoyi";
    $db[5] = "jos";
    $db[6] = "kaduna";
    $db[7] = "kano";
    $db[8] = "pg";
    $db[9] = "phc";
} else {
    if (getRegister($txtMACAddress, 7) == "63") {
        $db[0] = "ibadan";
        $db[1] = "kano";
        $db[2] = "phc";
        $db[3] = "benin";
        $db[4] = "abuja";
    }
}
if (getRegister($txtMACAddress, 7) == "65") {
    $query = "SHOW COLUMNS FROM tuser";
    $result = mysqli_query($conn, $query);
    $col_count = 0;
    while ($cur = mysqli_fetch_row($result)) {
        $col_count++;
        if (30 < $col_count) {
            $query = "ALTER TABLE tuser DROP COLUMN " . $cur[0];
            updateIData($iconn, $query, true);
        }
    }
    for ($i = 0; $i < count($db); $i++) {
        $query = "INSERT IGNORE INTO " . $db[$i] . ".tuser (SELECT * FROM Access.tuser WHERE group_id <> 10)";
        if (updateIData($iconn, $query, true)) {
            $query = "REPLACE INTO " . $db[$i] . ".tuser (SELECT * FROM Access.tuser WHERE group_id = 10)";
            if (updateIData($iconn, $query, true)) {
                $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('ACServer Synch - HQ --> " . $db[$i] . "', " . insertToday() . ", '" . getNow() . "')";
                if (!updateIData($iconn, $query, true)) {
                    echo "\n\r" . $query;
                }
            } else {
                echo "\n\r" . $query;
            }
        } else {
            echo "\n\r" . $query;
        }
    }
    for ($i = 0; $i < count($db); $i++) {
        $query = "INSERT IGNORE INTO Access.tuser (SELECT * FROM " . $db[$i] . ".tuser WHERE group_id = 10)";
        if (updateIData($iconn, $query, true)) {
            $query = "REPLACE INTO Access.tuser (SELECT * FROM " . $db[$i] . ".tuser WHERE group_id <> 10)";
            if (updateIData($iconn, $query, true)) {
                $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('ACServer Synch - " . $db[$i] . " --> HQ', " . insertToday() . ", '" . getNow() . "')";
                if (!updateIData($iconn, $query, true)) {
                    echo "\n\r" . $query;
                }
            } else {
                echo "\n\r" . $query;
            }
        } else {
            echo "\n\r" . $query;
        }
    }
    $query = "UPDATE tuser SET name = replace(name,'''','')";
    updateIData($iconn, $query, true);
    $v1 = "";
    $v2 = "";
    $i = 0;
    $query = "SELECT Emp_Payroll_No, Emp_Name, Emp_IsBlackList, Is_Active from tblEmployee";
    for ($result = mssql_query($query, $mconn); $cur = mssql_fetch_row($result); $i++) {
        $query = "UPDATE tuser SET name = '" . $cur[1] . "' WHERE id = '" . $cur[0] . "' AND name <> '" . $cur[1] . "'";
        updateData($conn, $query, true);
        if ($cur[2] == 1 || $cur[3] == 0) {
            $query = "UPDATE tuser SET datelimit = CONCAT('Y', SUBSTRING(datelimit, 2, 8), " . insertToday() . ") WHERE id = '" . $cur[0] . "' AND datelimit LIKE 'N%'";
            updateData($conn, $query, true);
        } else {
            $query = "UPDATE tuser SET datelimit = CONCAT('N', SUBSTRING(datelimit, 2, 16)) WHERE id = '" . $cur[0] . "' AND datelimit LIKE 'Y%'";
            updateData($conn, $query, true);
        }
        $v1[$i] = $cur[0];
        $v2[$i] = $cur[1];
    }
    $not_in = "";
    for ($i = 0; $i < count($v1); $i++) {
        $not_in .= "'" . $v1[$i] . "', ";
    }
    $not_in .= "0";
    $query = "SELECT id, name, OCTET_LENGTH(fpdata) from tuser WHERE id NOT IN (" . $not_in . ")";
    $result = mysqli_query($conn, $query);
    $fp = 2;
    while ($cur = mysqli_fetch_row($result)) {
        $pieces = explode(" ", $cur[1]);
        if (count($pieces) == 2) {
            $pieces[2] = "";
        } else {
            if (count($pieces) == 1) {
                $pieces[1] = "";
                $pieces[2] = "";
            }
        }
        if (32 < $cur[2]) {
            $fp = 1;
        } else {
            $fp = 2;
        }
        $max_query = "SELECT MAX(Emp_Id) FROM tblEmployee";
        $max_result = mssql_query($max_query, $mconn);
        $max_cur = mssql_fetch_row($max_result);
        $max_id = $max_cur[0] * 1 + 1;
        $sub_query = "INSERT INTO tblEmployee (Emp_Id, Emp_Name, Cmp_id, Branch_id, Emp_Payroll_No, Emp_First_Name, Emp_Last_Name, Emp_Title, Emp_Gender, Employment_Type, Category_Id, Emp_WeekOff_Type, Company_Calendar_Id, Emp_Desig_Id, Emp_Location_Id, Emp_Dept_Id, Login_C_Id, Is_Active) VALUES (" . $max_id . ", '" . $cur[1] . "', 1, 1, '" . $cur[0] . "', '" . $pieces[0] . "', '" . $pieces[1] . " " . $pieces[2] . "', 1, 1, 1, " . $fp . ", 1, 1, 1, 1, 1, 1, 1)";
        if (!mssql_query($sub_query, $mconn)) {
            echo mssql_get_last_message();
            exit;
        }
    }
} else {
    if (getRegister($txtMACAddress, 7) == "63") {
        $gid = 11;
        $d_b = "";
        echo "\n\r" . count($db);
        for ($i = 0; $i < count($db); $i++) {
            switch (strtoupper($db[$i])) {
                case "ABUJA":
                    $d_b = "abuja";
                    $gid = 10;
                    break;
                case "BENIN":
                    $d_b = "benin";
                    $gid = 12;
                    break;
                case "KANO":
                    $d_b = "kano";
                    $gid = 13;
                    break;
                case "GHANA":
                    $d_b = "ghana";
                    $gid = 14;
                    break;
                case "BENIN":
                    $d_b = "benin";
                    $gid = 15;
                    break;
                case "PHC":
                    $d_b = "phc";
                    $gid = 21;
                    break;
                case "IKEJA":
                    $d_b = "ikeja";
                    $gid = 31;
                    break;
                case "IBADAN":
                    $d_b = "ibadan";
                    $gid = 41;
                    break;
            }
            if ($db[$i] == "phc") {
                $query = "INSERT IGNORE INTO " . $db[$i] . ".tuser (SELECT * FROM Access.tuser WHERE group_id <> " . $gid . " AND group_id <> 22)";
            } else {
                $query = "INSERT IGNORE INTO " . $db[$i] . ".tuser (SELECT * FROM Access.tuser WHERE group_id <> " . $gid . ")";
            }
            if (updateIData($iconn, $query, true)) {
                if ($db[$i] == "phc") {
                    $query = "REPLACE INTO " . $db[$i] . ".tuser (SELECT * FROM Access.tuser WHERE group_id <> " . $gid . " AND group_id <> 22)";
                } else {
                    $query = "REPLACE INTO " . $db[$i] . ".tuser (SELECT * FROM Access.tuser WHERE group_id <> " . $gid . " )";
                }
                if (updateIData($iconn, $query, true)) {
                    $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('ACServer Synch - HQ --> " . $db[$i] . "', " . insertToday() . ", '" . getNow() . "')";
                    if (!updateIData($iconn, $query, true)) {
                        echo "\n\r" . $query;
                    }
                } else {
                    echo "\n\r" . $query;
                }
            } else {
                echo "\n\r" . $query;
            }
            if ($db[$i] == "phc") {
                $query = "INSERT IGNORE INTO Access.tuser (SELECT * FROM " . $db[$i] . ".tuser WHERE group_id = " . $gid . " OR group_id = 22)";
            } else {
                $query = "INSERT IGNORE INTO Access.tuser (SELECT * FROM " . $db[$i] . ".tuser WHERE group_id = " . $gid . ")";
            }
            if (updateIData($iconn, $query, true)) {
                if ($db[$i] == "phc") {
                    $query = "UPDATE Access.tuser, " . $db[$i] . ".tuser SET Access.tuser.name = " . $db[$i] . ".tuser.name WHERE Access.tuser.id = " . $db[$i] . ".tuser.id AND (Access.tuser.group_id = " . $gid . " OR Access.tuser.group_id = 22)";
                } else {
                    $query = "UPDATE Access.tuser, " . $db[$i] . ".tuser SET Access.tuser.name = " . $db[$i] . ".tuser.name WHERE Access.tuser.id = " . $db[$i] . ".tuser.id AND (Access.tuser.group_id = " . $gid . ")";
                }
                if (updateIData($iconn, $query, true)) {
                    $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('ACServer Synch - " . $db[$i] . " --> HQ', " . insertToday() . ", '" . getNow() . "')";
                    if (!updateIData($iconn, $query, true)) {
                        echo "\n\r" . $query;
                    }
                } else {
                    echo "\n\r" . $query;
                }
            } else {
                echo "\n\r" . $query;
            }
            if ($db[$i] == "phc") {
                $query = "INSERT IGNORE INTO Access.tenter (e_date, e_time, g_id, e_id, e_group) (SELECT e_date, e_time, g_id, e_id, e_group FROM " . $db[$i] . ".tenter WHERE (e_group = " . $gid . " OR e_group = 22) AND e_date > '" . $main_result[0] . "')";
            } else {
                $query = "INSERT IGNORE INTO Access.tenter (e_date, e_time, g_id, e_id, e_group) (SELECT e_date, e_time, g_id, e_id, e_group FROM " . $db[$i] . ".tenter WHERE (e_group = " . $gid . ") AND e_date > '" . $main_result[0] . "')";
            }
            if (updateIData($iconn, $query, true)) {
                $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('ACServer Log Synch - " . $db[$i] . " --> HQ', " . insertToday() . ", '" . getNow() . "')";
                if (!updateIData($iconn, $query, true)) {
                    echo "\n\r" . $query;
                }
            } else {
                echo "\n\r" . $query;
            }
        }
    }
}

?>