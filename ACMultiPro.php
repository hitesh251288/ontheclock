<?php


ob_start("ob_gzhandler");
error_reporting(E_ALL);
date_default_timezone_set("Africa/Algiers");
include "Functions.php";
set_time_limit(0);
$conn = openConnection();
$iconn = openIConnection();
$db = "";
$gid = 11;
$query = "SHOW DATABASES";
$result = mysqli_query($conn, $query);
while ($cur = mysqli_fetch_row($result)) {
    switch (strtoupper($cur[0])) {
        case "ABUJA":
            $db = "abuja";
            $gid = 10;
            break;
        case "BENIN":
            $db = "benin";
            $gid = 12;
            break;
        case "KANO":
            $db = "kano";
            $gid = 13;
            break;
        case "GHANA":
            $db = "ghana";
            $gid = 14;
            break;
        case "BENIN":
            $db = "benin";
            $gid = 15;
            break;
        case "PHC":
            $db = "phc";
            $gid = 21;
            break;
        case "IKEJA":
            $db = "ikeja";
            $gid = 31;
            break;
        case "IBADAN":
            $db = "ibadan";
            $gid = 41;
            break;
    }
}
echo "\n\rSynchronize HQ with Database of: " . $db;
try {
    $hconn = new PDO("mysql:host=192.168.1.3;dbname=" . $db, "fdmsusr", "fdmsamho");
} catch (PDOException $e) {
    try {
        echo "\n\rUnable to connect to VPN IP. Trying LIVE" . $e->getMessage();
        $hconn = new PDO("mysql:host=197.149.65.198;dbname=" . $db, "fdmsusr", "fdmsamho");
    } catch (PDOException $e) {
        echo "\n\r" . $e->getMessage();
        exit;
    }
}
$rconn = new PDO("mysql:host=127.0.0.1;dbname=" . $db, "fdmsusr", "fdmsamho");
echo "\n\rConnected to FP Image Database: " . $db;
if ($db == "phc") {
    $query = "REPLACE INTO " . $db . ".tuser (SELECT * FROM Access.tuser WHERE group_id = " . $gid . " OR group_id = 22)";
} else {
    $query = "REPLACE INTO " . $db . ".tuser (SELECT * FROM Access.tuser WHERE group_id = " . $gid . ")";
}
if (!updateIData($iconn, $query, true)) {
    echo "\n\r" . $query;
}
echo "\n\rRegion-Region Synch - Pushed Data from Access to " . $db;
if ($db == "phc") {
    $query = "INSERT IGNORE INTO " . $db . ".tenter (e_date, e_time, g_id, e_id, e_group) (SELECT e_date, e_time, g_id, e_id, e_group FROM Access.tenter WHERE (e_group = " . $gid . " OR e_group = 22) AND e_date > " . getLastDay(insertToday(), 60) . ")";
} else {
    $query = "INSERT IGNORE INTO " . $db . ".tenter (e_date, e_time, g_id, e_id, e_group) (SELECT e_date, e_time, g_id, e_id, e_group FROM Access.tenter WHERE e_group = " . $gid . " AND e_date > " . getLastDay(insertToday(), 60) . ")";
}
if (updateIData($iconn, $query, true)) {
    echo "\n\rRegion-Region Synch - Pushed Clocking Data from Access to " . $db;
    if ($db == "phc") {
        data_synch($rconn, $hconn, $db, 0, $gid);
        data_synch($rconn, $hconn, $db, 0, 22);
    } else {
        data_synch($rconn, $hconn, $db, 0, $gid);
    }
    echo "\n\rRegion-HQ Synch - Pushed Data from Region." . $db . " to HQ." . $db;
    if ($db == "phc") {
        data_synch($hconn, $rconn, $db, 1, $gid);
        data_synch($hconn, $rconn, $db, 1, 22);
    } else {
        data_synch($hconn, $rconn, $db, 1, $gid);
    }
    echo "\n\rHQ-Region Synch - Pushed Data from HQ." . $db . " to Region." . $db;
    if ($db == "phc") {
        $query = "REPLACE INTO Access.tuser (SELECT * FROM " . $db . ".tuser WHERE (group_id <> " . $gid . " AND group_id <> 22))";
        if (!updateIData($iconn, $query, true)) {
            echo "\n\r" . $query;
        }
    } else {
        $query = "REPLACE INTO Access.tuser (SELECT * FROM " . $db . ".tuser WHERE group_id <> " . $gid . ")";
        if (!updateIData($iconn, $query, true)) {
            echo "\n\r" . $query;
        }
    }
    echo "\n\rRegion-Region Synch - Pushed Data from " . $db . " to Access";
} else {
    echo "\n\r Error Inserting Record: " . $query;
    exit;
}
function data_synch($sel, $ins, $db, $path, $gid)
{
    $query = "";
    if ($path == 0) {
        $query = "SELECT id, name, reg_date, datelimit, idno, company, dept, phone, group_id, pwd, remark, fpdata, fpimage FROM " . $db . ".tuser WHERE group_id = " . $gid;
    } else {
        if ($path == 1) {
            $query = "SELECT id, name, reg_date, datelimit, idno, company, dept, phone, group_id, pwd, remark, fpdata, fpimage FROM " . $db . ".tuser WHERE group_id <> " . $gid;
        }
    }
    try {
        $stmt = $sel->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        $stmt->execute();
        $count = 0;
        while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
            if (is_numeric($row[8])) {
                if ($path == 0 && $row[8] == $gid || $path == 1 && $row[8] != $gid) {
                    $query = "INSERT INTO " . $db . ".tuser (id, name, reg_date, datelimit, idno, company, dept, phone, group_id, pwd, remark, fpdata, fpimage) VALUES (:id, :name, :reg_date, :datelimit, :idno, :company, :dept, :phone, :group_id, :pwd, :remark, :fpdata, :fpimage) ON DUPLICATE KEY UPDATE name = :name, reg_date = :reg_date, datelimit = :datelimit, idno = :idno, company = :company, dept = :dept, phone = :phone, group_id = :group_id, pwd = :pwd, remark = :remark, fpdata = :fpdata, fpimage = :fpimage";
                    $stmt_i = $ins->prepare($query);
                    $stmt_i->bindParam(":id", $row[0], PDO::PARAM_INT);
                    $stmt_i->bindParam(":name", $row[1], PDO::PARAM_STR);
                    $stmt_i->bindParam(":reg_date", $row[2], PDO::PARAM_STR);
                    $stmt_i->bindParam(":datelimit", $row[3], PDO::PARAM_STR);
                    $stmt_i->bindParam(":idno", $row[4], PDO::PARAM_STR);
                    $stmt_i->bindParam(":company", $row[5], PDO::PARAM_STR);
                    $stmt_i->bindParam(":dept", $row[6], PDO::PARAM_STR);
                    $stmt_i->bindParam(":phone", $row[7], PDO::PARAM_STR);
                    $stmt_i->bindParam(":group_id", $row[8], PDO::PARAM_INT);
                    $stmt_i->bindParam(":pwd", $row[9], PDO::PARAM_STR);
                    $stmt_i->bindParam(":remark", $row[10], PDO::PARAM_STR);
                    $stmt_i->bindParam(":fpdata", $row[11], PDO::PARAM_LOB);
                    $stmt_i->bindParam(":fpimage", $row[12], PDO::PARAM_LOB);
                    $stmt_i->execute();
                    $count++;
                }
            } else {
                echo "\n\rNOT NUMERIC";
                exit;
            }
        }
        if ($path == 0) {
            $query = "SELECT e_date, e_time, g_id, e_id, e_group FROM " . $db . ".tenter WHERE e_group = " . $gid . " AND e_date > " . getLastDay(insertToday(), 60);
            try {
                $stmt = $sel->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
                $stmt->execute();
                $count = 0;
                while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
                    if (is_numeric($row[4])) {
                        $query = "INSERT IGNORE INTO " . $db . ".tenter (e_date, e_time, g_id, e_id, e_group) VALUES (:e_date, :e_time, :g_id, :e_id, :e_group)";
                        $stmt_i = $ins->prepare($query);
                        $stmt_i->bindParam(":e_date", $row[0], PDO::PARAM_INT);
                        $stmt_i->bindParam(":e_time", $row[1], PDO::PARAM_STR);
                        $stmt_i->bindParam(":g_id", $row[2], PDO::PARAM_STR);
                        $stmt_i->bindParam(":e_id", $row[3], PDO::PARAM_STR);
                        $stmt_i->bindParam(":e_group", $row[4], PDO::PARAM_STR);
                        $stmt_i->execute();
                        $count++;
                    } else {
                        echo "\n\rNOT NUMERIC";
                        exit;
                    }
                }
                echo "\n\rCount is" . $count;
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }
        $query = "INSERT INTO Access.ProcessLog (PType, PDate, PTime) VALUES (:pdata, :pdate, :ptime)";
        $stmt_i = $ins->prepare($query);
        if ($path == 0) {
            $stmt_i->bindParam(":pdata", $pdata = "Region-HQ Synch - Region." . $db . " --> HQ." . $db, PDO::PARAM_STR);
        } else {
            $stmt_i->bindParam(":pdata", $pdata = "HQ-Region Synch - HQ." . $db . " --> Access." . $db, PDO::PARAM_STR);
        }
        $stmt_i->bindParam(":pdate", $pdate = insertToday(), PDO::PARAM_INT);
        $stmt_i->bindParam(":ptime", $ptime = getNow(), PDO::PARAM_INT);
        $stmt_i->execute();
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}

?>