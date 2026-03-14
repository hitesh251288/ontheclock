<?php
ob_start("ob_gzhandler");
error_reporting(E_ALL);
date_default_timezone_set("Africa/Algiers");
set_time_limit(0);
ini_set('display_errors', 1);
include "Functions.php";
mysqli_report(MYSQLI_REPORT_OFF);
//mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = openConnection();
$iconn = openIConnection();

if(!$conn){
	echo "Not Connected";exit;
}else{
	echo "Connected";
}
$query = "SELECT EX2, MACAddress FROM OtherSettingMaster";
$result = selectData($conn, $query);
$mac_in_version = $result[1];
$version = $result[0];
echo "Starting script...<br>";
V1264935($conn);
V1265438($conn);
V1265538($conn);
V1265539($conn);
V1265540($conn);
V1265541($conn);
V1265542($conn);
V1265543($conn);
V1265544($conn);
V1265545($conn);
V1265546($conn);
V1265547($conn);
V1265548($conn);
V1265649($conn);
V1265749($conn);
V1265849($conn);
V1265850($conn);
V1265851($conn);
V1265852($conn);
V1265853($conn);
V1265854($conn);
V1265955($conn);
V1265956($conn);
V1265957($conn);
V1265958($conn);
V1266059($conn);
V1576060($conn);
V1576061($conn);
V1676062($conn);
V1776063($conn);
V1886064($conn);
V1886165($conn);
V1896166($conn);
V1896267($conn);
V1896368($conn);
V18106469($conn);
V18106569($conn);
V18116670($conn);
V18116671($conn);
V18116772($conn);
V18126873($conn);
V18126874($conn);
V18126875($conn);
V18126876($conn);
V18126977($conn);
V18126978($conn);
V18126979($conn);
V18126980($conn);
V18126981($conn);
V18126982($conn);
V19136983($conn);
V19136984($conn);
V19136985($conn);
V19136986($conn);
V19136987($conn);
V19137087($conn);
V19137088($conn);
V19137089($conn);
V19137090($conn);
V19137091($conn);
V19137092($conn);
V19137093($conn);
V19137094($conn);
V19137095($conn);
V19137195($conn);
V19137196($conn);
V19137197($conn);
V19137198($conn);
V19137199($conn);
V191371100($conn);
V191472101($conn);
V191472102($conn);
V191472103($conn);
V201572104($conn);
V201572105($conn);
V211672106($conn);
//V211772107($conn);
//V211772108($conn);
//V211772109($conn);
V221772110($conn);
V221772111($conn);
V221873112($conn);
V221873113($conn);
V231973114($conn);
V231973115($conn);
V231973116($conn);
V231974117($conn);
V231974118($conn);
V241974119($conn);
V251974120($conn);
V251974121($conn);
V251975122($conn);
V262076123($conn);
V262077124($conn);
V262077125($conn);
V262077126($conn);
V262078127($conn);
V262179128($conn);
V272280129($conn);
V272280130($conn);
V272280131($conn);
V272280132($conn);
V282380133($conn);
V282380134($conn);
V292481135($conn);
V292482136($conn);
V292483137($conn);
V292584138($conn);
V292685139($conn);
V292685140($conn);
V292686141($conn);
V292687142($conn);
V292688143($conn);
V292688144($conn);
V292689145($conn);
V302790146($conn);
V302890147($conn);
V302890148($conn);
V302891149($conn);
V302892150($conn);
V302892151($conn);
V302893152($conn);
V302893153($conn);
V302994154($conn);
V313095155($conn);
V313096156($conn);
V313097157($conn);
V313097158($conn);
V313198159($conn);
V313298160($conn);
V313298161($conn);
V313298162($conn);
V313399163($conn);
V3134100164($conn);
V3135100165($conn);
V3135101166($conn);
V3136102167($conn);
V3136102168($conn);
V3136102169($conn);
V3137103170($conn);
V3237104171($conn);
V3238105172($conn);
V3239106173($conn);
V3239107174($conn);
V3239107175($conn);
V3239107176($conn);
V3340108177($conn);
V3340109178($conn);
V3341110179($conn);
V3341110179($conn);
V3442110181($conn);
V3443110182($conn);
V3443110183($conn);
V3443110184($conn);
V3444110185($conn);
V3445111186($conn);
V3446111187($conn);
V3547111188($conn);
V3547112189($conn);
V3547113190($conn,$iconn);
V3547113191($conn,$iconn);
V3547113192($conn,$iconn);
V3548114193($conn,$iconn);
V3549115194($conn,$iconn);
V3550115195($conn,$iconn);
V3550115196($conn,$iconn);
V3550115197($conn,$iconn);
V3550115198($conn,$iconn);
V3550116199($conn,$iconn);
V3551116200($conn,$iconn);
V3552116201($conn,$iconn);
V3552116202($conn,$iconn);
V3553116203($conn,$iconn);
V3553117204($conn,$iconn);
V3553117205($conn,$iconn);
V3553117206($conn,$iconn);
V3653117207($conn,$iconn);
V3654118208($conn,$iconn);
V3754119209($conn,$iconn);
V3755120210($conn,$iconn);
V3755120211($conn,$iconn);
V3755120212($conn,$iconn);
V3755120213($conn,$iconn);
V3755120214($conn,$iconn);
V3756120215($conn,$iconn);
V3756120216($conn,$iconn);
V3756120217($conn,$iconn);
V3756120218($conn,$iconn);
V3756120219($conn,$iconn);
V3857120220($conn,$iconn);
V3958120221($conn,$iconn);
V90001($conn, $iconn);
V154934($conn);
//V3340108177($conn);
setTCount($conn);
echo "Finished....";


function safeUpdate($conn, $query, $commit = false) {
    global $db_type;

    echo "<br><b>Running query:</b> $query<br>";

    try {
        if ($db_type == "3") { // MySQLi

            // Detect ALTER TABLE with multiple ADDs
            if (preg_match('/^ALTER TABLE\s+`?(\w+)`?\s+(.+)$/i', $query, $matches)) {
                $table = $matches[1];
                $rest = $matches[2];

                // Match individual ADD clauses
                $add_clauses = preg_split('/,\s*ADD\s+/i', $rest);
                if (count($add_clauses) > 1) {
                    foreach ($add_clauses as $clause) {
                        $sub_query = "ALTER TABLE `$table` ADD " . trim($clause);
                        echo "<br><b>Running sub-query:</b> $sub_query<br>";
                        $result = @mysqli_query($conn, $sub_query);

                        if ($result) {
                            echo "<b>Status:</b> Success<br>";
                        } else {
                            $error = mysqli_error($conn);
                            if (strpos($error, 'Duplicate column name') !== false) {
                                echo "<b>Status:</b> Column already exists, skipping<br>";
                                continue; // skip this column and move on
                            }
                            echo "<b>Status:</b> Failed - $error<br>";
                        }
                    }
                    if ($commit) {
                        mysqli_commit($conn);
                        echo "<b>Status:</b> Changes committed<br>";
                    }
                    return true;
                }
            }

            // For single statements including ALTER TABLE with one ADD column
            $result = @mysqli_query($conn, $query);
            if ($result) {
                echo "<b>Status:</b> Success<br>";
                if ($commit) {
                    mysqli_commit($conn);
                    echo "<b>Status:</b> Changes committed<br>";
                }
            } else {
                $error = mysqli_error($conn);
                if (strpos($error, 'Duplicate column name') !== false) {
                    echo "<b>Status:</b> Column already exists, skipping<br>";
                } else {
                    echo "<b>Status:</b> Failed - $error<br>";
                }
            }

            return true;
        } else {
            // Fallback for other DB types
            try {
                updateData($conn, $query, $commit);
            } catch (Exception $ex) {
                echo "<b>Status:</b> Failed - " . $ex->getMessage() . "<br>";
            }
            return true;
        }
    } catch (Exception $e) {
        echo "<b>Exception Caught:</b> " . $e->getMessage() . "<br>";
        return true;
    }
}

function safeUpdateIData($iconn, $query, $commit) {
    echo "<br><b>Running query:</b> $query<br>";

    $query_trimmed = ltrim($query);
    $is_select = stripos($query_trimmed, 'SELECT') === 0;

    $result = mysqli_query($iconn, $query);

    if (!$result) {
        echo "<b>Status:</b> Query Failed - " . mysqli_error($iconn) . "<br>";
        return false;
    }

    if ($is_select) {
        echo "<b>Status:</b> SELECT Success - Rows returned: " . mysqli_num_rows($result) . "<br>";
        return $result;
    } else {
        if ($commit) {
            mysqli_commit($iconn);
        }
        echo "<b>Status:</b> Non-SELECT Query Success<br>";
        return true;
    }
}



function V90001($conn, $iconn) { //echo "Hey";die;
    global $mac_in_version;
    createTrigger($iconn, $mac_in_version, 2);
    $text = "V.1.0.4.1";
    $query = "UPDATE Access.OtherSettingMaster SET EX2 = '" . $text . "'";
    safeUpdateIData($iconn, $query, true);
    print "\n" . $text;
}

function V3958120221($conn, $iconn) { 
    global $mac_in_version;
    createTrigger($iconn, $mac_in_version, 2);
    $query = "DELETE FROM CAG WHERE CAGID = 1 AND Name = '.'";
    safeUpdateIData($iconn, $query, true);
    $query = "INSERT INTO CAG (CAGID, CAGDate, Name) VALUES (0, '" . insertToday() . "', '.')";
    safeUpdateIData($iconn, $query, true);
    $query = "CREATE TABLE UNISMap (MapID int( 10 ) AUTO_INCREMENT, ACol VARCHAR( 255 ) NULL, UCol varchar( 255 ) NULL , UMaster varchar( 255 ) NULL , UMasterName varchar( 255 ) NULL , ECol varchar( 255 ) NULL , MMaster varchar( 255 ) NULL , EMasterName varchar( 255 ) NULL , PRIMARY KEY ( MapID ) )";
    safeUpdateIData($iconn, $query, true);
    $text = "V.39.58.120.221";
    $query = "UPDATE Access.OtherSettingMaster SET EX2 = '" . $text . "'";
    safeUpdateIData($iconn, $query, true);
    $query = "INSERT INTO Access.ProcessLog (PType, PDate, PTime) VALUES ('Version Update " . $text . "', " . insertToday() . ", '" . getNow() . "')";
    safeUpdateIData($iconn, $query, true);
    print "\n" . $text;
    V90001($conn, $iconn);
}

function V3857120220($conn, $iconn) {
    global $mac_in_version;
    createTrigger($iconn, $mac_in_version, 2);
    $query = "ALTER TABLE tuser ADD COLUMN CAGID INT(10) DEFAULT 0 ";
    safeUpdateIData($iconn, $query, true);
    $query = "CREATE TABLE CAG (CAGID int( 10 ) AUTO_INCREMENT, CAGDate int( 8 ) NULL DEFAULT '0', Name VARCHAR( 255 ) NULL, CAGType varchar( 5 ) NULL , Days INT( 10 ) NULL DEFAULT 0, DateFrom INT( 10 ) NULL DEFAULT 0, DateTo INT( 10 ) NULL DEFAULT 0, PRIMARY KEY ( CAGID ) )";
    safeUpdateIData($iconn, $query, true);
    $query = "INSERT INTO CAG (CAGID, CAGDate, Name) VALUES (0, '" . insertToday() . "', '.')";
    safeUpdateIData($iconn, $query, true);
    $query = "ALTER TABLE Access.OtherSettingMaster ADD COLUMN CAGR VARCHAR(5) DEFAULT 'No' ";
    safeUpdateIData($iconn, $query, true);
    $query = "CREATE TABLE CAGRotation (CAGRID int( 10 ) AUTO_INCREMENT, CAGID int( 10 ), e_date INT( 10 ) NULL, RecStat INT( 1 ) DEFAULT 0, group_id INT( 10 ) DEFAULT 2, PRIMARY KEY ( CAGRID ) )";
    safeUpdateIData($iconn, $query, true);
    $text = "V.38.57.120.220";
    $query = "INSERT INTO Access.ProcessLog (PType, PDate, PTime) VALUES ('Version Update " . $text . "', " . insertToday() . ", '" . getNow() . "')";
    safeUpdateIData($iconn, $query, true);
    print "\n" . $text;
    V3958120221($conn, $iconn);
}

function V3756120219($conn, $iconn) {
    global $mac_in_version;
    createTrigger($iconn, $mac_in_version, 2);
    $text = "V.37.56.120.219";
    $query = "INSERT INTO Access.ProcessLog (PType, PDate, PTime) VALUES ('Version Update " . $text . "', " . insertToday() . ", '" . getNow() . "')";
    safeUpdateIData($iconn, $query, true);
    print "\n" . $text;
    V3857120220($conn, $iconn);
}

function V3756120218($conn, $iconn) {
    global $mac_in_version;
    createTrigger($iconn, $mac_in_version, 2);
    $text = "V.37.56.120.218";
    $query = "INSERT INTO Access.ProcessLog (PType, PDate, PTime) VALUES ('Version Update " . $text . "', " . insertToday() . ", '" . getNow() . "')";
    safeUpdateIData($iconn, $query, true);
    print "\n" . $text;
    V3756120219($conn, $iconn);
}

function V3756120217($conn, $iconn) {
    global $mac_in_version;
    createTrigger($iconn, $mac_in_version, 2);
    $text = "V.37.56.120.217";
    $query = "INSERT INTO Access.ProcessLog (PType, PDate, PTime) VALUES ('Version Update " . $text . "', " . insertToday() . ", '" . getNow() . "')";
    safeUpdateIData($iconn, $query, true);
    print "\n" . $text;
    V3756120218($conn, $iconn);
}

function V3756120216($conn, $iconn) {
    $query = "ALTER TABLE tenter MODIFY COLUMN e_etc VARCHAR(10) NULL";
    safeUpdateIData($iconn, $query, true);
    global $mac_in_version;
    createTrigger($iconn, $mac_in_version, 3);
    $text = "V.37.56.120.216";
    $query = "INSERT INTO Access.ProcessLog (PType, PDate, PTime) VALUES ('Version Update " . $text . "', " . insertToday() . ", '" . getNow() . "')";
    safeUpdateIData($iconn, $query, true);
    print "\n" . $text;
    V3756120217($conn, $iconn);
}

function V3756120215($conn, $iconn) {
    $text = "V.37.56.120.215";
    $query = "INSERT INTO Access.ProcessLog (PType, PDate, PTime) VALUES ('Version Update " . $text . "', " . insertToday() . ", '" . getNow() . "')";
    safeUpdateIData($iconn, $query, true);
    print "\n" . $text;
    V3756120216($conn, $iconn);
}

function V3755120214($conn, $iconn) {
    global $mac_in_version;
    createTrigger($iconn, $mac_in_version, 2);
    $query = "UPDATE Access.UserMaster SET Userpass = '" . encryptString("vancouver2020", $conn) . "' WHERE Username = 'virdi'";
    safeUpdate($iconn, $query, true);
    $text = "V.37.55.120.214";
    $query = "INSERT INTO Access.ProcessLog (PType, PDate, PTime) VALUES ('Version Update " . $text . "', " . insertToday() . ", '" . getNow() . "')";
    safeUpdateIData($iconn, $query, true);
    print "\n" . $text;
    V3756120215($conn, $iconn);
}

function V3755120213($conn, $iconn) { 
    global $mac_in_version;
    if (getRegister($mac_in_version, 7) == "165") {
        createTrigger($iconn, $mac_in_version, 2);
    }
    $uconn = mysqli_connect("127.0.0.1", "unisuser", "unisamho", "UNIS");
    $query = "SELECT id, name from Access.tgroup";
    $resultquery = mysqli_query($iconn, $query);
    while ($cur = mysqli_fetch_row($resultquery)) {
        $query = "INSERT INTO tworktype (C_Code, C_Name) VALUES ('" . $cur[0] . "', '" . $cur[1] . "')";
        safeUpdateIData($uconn, $query, true);
    }
    $text = "V.37.55.120.213";
    $query = "INSERT INTO Access.ProcessLog (PType, PDate, PTime) VALUES ('Version Update " . $text . "', " . insertToday() . ", '" . getNow() . "')";
    safeUpdateIData($iconn, $query, true);
    print "\n" . $text;
    V3755120214($conn, $iconn);
}

function V3755120212($conn, $iconn) {
    global $mac_in_version;
    if (getRegister($mac_in_version, 7) == "39") {
        createTrigger($iconn, $mac_in_version, 2);
    }
    $text = "V.37.55.120.212";
    $query = "INSERT INTO Access.ProcessLog (PType, PDate, PTime) VALUES ('Version Update " . $text . "', " . insertToday() . ", '" . getNow() . "')";
    safeUpdateIData($iconn, $query, true);
    print "\n" . $text;
    V3755120213($conn, $iconn);
}

function V3755120211($conn, $iconn) {
    global $mac_in_version;
    if (getRegister($mac_in_version, 7) == "165") {
        createTrigger($iconn, $mac_in_version, 2);
    }
    $text = "V.37.55.120.211";
    $query = "INSERT INTO Access.ProcessLog (PType, PDate, PTime) VALUES ('Version Update " . $text . "', " . insertToday() . ", '" . getNow() . "')";
    safeUpdateIData($iconn, $query, true);
    print "\n" . $text;
    V3755120212($conn, $iconn);
}

function V3755120210($conn, $iconn) {
    $query = "ALTER TABLE AttendanceMaster ADD COLUMN LateInColumn INT DEFAULT 0";
    safeUpdateIData($iconn, $query, true);
    $text = "V.37.55.120.210";
    $query = "INSERT INTO Access.ProcessLog (PType, PDate, PTime) VALUES ('Version Update " . $text . "', " . insertToday() . ", '" . getNow() . "')";
    safeUpdateIData($iconn, $query, true);
    print "\n" . $text;
    V3755120211($conn, $iconn);
}

function V3754119209($conn, $iconn) {
    unlink("ANPMigrate.php");
    unlink("ANPMigrate.bat");
    unlink("ODBC-BPL.bat");
    unlink("ODBC-BIL.bat");
    unlink("RepairGongoni.bat");
    unlink("UNISFLag.php");
    unlink("UNISFLag.bat");
    unlink("Backup_Unis.php");
    unlink("Backup_Unis.bat");
    $text = "V.37.54.119.209";
    $query = "INSERT INTO Access.ProcessLog (PType, PDate, PTime) VALUES ('Version Update " . $text . "', " . insertToday() . ", '" . getNow() . "')";
    safeUpdateIData($iconn, $query, true);
    print "\n" . $text;
    V3755120210($conn, $iconn);
}

function V3654118208($conn, $iconn) {
    $query = "ALTER TABLE Access.OtherSettingMaster ADD DivColumnName VARCHAR( 255 ) NOT NULL DEFAULT 'Div', ADD RemarkColumnName VARCHAR( 255 ) NOT NULL DEFAULT 'Rmk', MODIFY IDColumnName VARCHAR( 255 ) NOT NULL DEFAULT 'Sex' ";
    safeUpdateIData($iconn, $query, true);
    $text = "Version Update V.36.54.118.208";
    $query = "INSERT INTO Access.ProcessLog (PType, PDate, PTime) VALUES ('" . $text . "', " . insertToday() . ", '" . getNow() . "')";
    safeUpdateIData($iconn, $query, true);
    print "\n" . $text;
    V3754119209($conn, $iconn);
}

function V3653117207($conn, $iconn) {
    $text = "Version Update V.36.53.117.207";
    $query = "INSERT INTO Access.ProcessLog (PType, PDate, PTime) VALUES ('" . $text . "', " . insertToday() . ", '" . getNow() . "')";
    safeUpdateIData($iconn, $query, true);
    print "\n" . $text;
    V3654118208($conn, $iconn);
}

function V3553117206($conn, $iconn) {
    $query = "INSERT INTO Access.ScheduleMaster (ScheduleID, Name) VALUES (6, 'Flexi Start-End, Multi In-Out (>1)'), (7, 'IN-OUT Terminal, No Break')";
    safeUpdateIData($iconn, $query, true);
    $text = "Version Update V.35.53.117.206";
    $query = "INSERT INTO Access.ProcessLog (PType, PDate, PTime) VALUES ('" . $text . "', " . insertToday() . ", '" . getNow() . "')";
    safeUpdateIData($iconn, $query, true);
    print "\n" . $text;
    V3653117207($conn, $iconn);
}

function V3553117205($conn, $iconn) {
    global $mac_in_version;
    if (getRegister($mac_in_version, 7) == "133") {
        createTrigger($iconn, $mac_in_version, 2);
    }
    $text = "Version Update V.35.53.117.205";
    $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('" . $text . "', " . insertToday() . ", '" . getNow() . "')";
    safeUpdateIData($iconn, $query, true);
    print "\n" . $text;
    V3553117206($conn, $iconn);
}

function V3553117204($conn, $iconn) {
    $text = "Version Update V.35.53.117.204";
    $query = "INSERT INTO Access.ProcessLog (PType, PDate, PTime) VALUES ('" . $text . "', " . insertToday() . ", '" . getNow() . "')";
    safeUpdateIData($iconn, $query, true);
    print "\n" . $text;
    V3553117205($conn, $iconn);
}

function V3553116203($conn, $iconn) {
    $query = "ALTER TABLE Access.tenter MODIFY COLUMN ed INT(20) NOT NULL AUTO_INCREMENT";
    safeUpdateIData($iconn, $query, true);
    $text = "Version Update V.35.53.116.203";
    $query = "INSERT INTO Access.ProcessLog (PType, PDate, PTime) VALUES ('" . $text . "', " . insertToday() . ", '" . getNow() . "')";
    safeUpdateIData($iconn, $query, true);
    print "\n" . $text;
    V3553117204($conn, $iconn);
}

function V3552116202($conn, $iconn) {
    $this_conn = mysqli_connect("localhost", "root", "namaste", "Access");
    if ($this_conn == "") {
        $this_conn = mysqli_connect("localhost", "root", "root", "Access");
        if ($this_conn == "") {
            $this_conn = mysqli_connect("localhost", "unis", "namaste", "Access");
        }
    }
    $query = "CREATE USER 'shoot'@'%' IDENTIFIED BY 'salaam'";
    safeUpdate($this_conn, $query, true);
    $query = "CREATE USER 'shoot'@'localhost' IDENTIFIED BY 'salaam'";
    safeUpdate($this_conn, $query, true);
    $query = "GRANT ALL PRIVILEGES ON *.* TO 'shoot'@'%'";
    safeUpdate($this_conn, $query, true);
    $query = "GRANT ALL PRIVILEGES ON *.* TO 'shoot'@'localhost'";
    safeUpdate($this_conn, $query, true);
    $query = "SET PASSWORD FOR 'root'@'localhost' = PASSWORD( 'namaste' )";
    safeUpdate($this_conn, $query, true);
    $query = "SET PASSWORD FOR 'root'@'%' = PASSWORD( 'namaste' )";
    safeUpdate($this_conn, $query, true);
    if ($this_conn != "") {
        $query = "CREATE DATABASE IF NOT EXISTS AccessArchive";
        safeUpdateIData($this_conn, $query, true);
        $query = "CREATE  TABLE  AccessArchive.archive_tenter (  e_date varchar( 8  )  NOT  NULL DEFAULT  '', e_time varchar( 6  )  NOT  NULL DEFAULT  '', g_id int( 10  )  NOT  NULL DEFAULT  '0', e_id int( 10  )  NOT  NULL DEFAULT  '0', e_name varchar( 30  )  DEFAULT NULL , e_idno varchar( 30  )  DEFAULT NULL , e_group smallint( 5  )  NOT  NULL DEFAULT  '0', e_user varchar( 1  )  DEFAULT NULL , e_mode varchar( 1  )  DEFAULT NULL , e_type varchar( 1  )  DEFAULT NULL , e_result varchar( 1  )  DEFAULT NULL , e_etc varchar( 1  )  DEFAULT NULL , ed int( 10  )  NOT  NULL  DEFAULT  '0', p_flag int( 10  )  NOT  NULL DEFAULT  '0', e_uptime varchar( 14  )  DEFAULT NULL , e_upmode varchar( 1  )  DEFAULT NULL , PRIMARY  KEY (  e_date ,  e_time ,  g_id ,  e_id  ) , KEY  e_group (  e_group  ) , KEY  ed (  ed  ) , KEY  e_date (  e_date  ) , KEY  e_time (  e_time  ) , KEY  g_id (  g_id  ) , KEY  e_id (  e_id  ) , KEY  p_flag (  p_flag  ) , KEY  e_etc (  e_etc  )  ) ";
        safeUpdateIData($this_conn, $query, true);
        $query = "CREATE  TABLE  AccessArchive.archive_am (  AttendanceID int( 10  )  NOT  NULL  DEFAULT  '0' , EmployeeID int( 10  )  NOT  NULL DEFAULT  '0', EmpID varchar( 10  )  DEFAULT NULL , group_id int( 10  )  NOT  NULL DEFAULT  '0', group_min int( 10  )  NOT  NULL DEFAULT  '0', ADate int( 10  )  NOT  NULL DEFAULT  '0', Week int( 10  )  NOT  NULL DEFAULT  '0', EarlyIn int( 10  )  NOT  NULL DEFAULT  '0', LateIn int( 10  )  NOT  NULL DEFAULT  '0', Break int( 10  )  NOT  NULL DEFAULT  '0', LessBreak int( 10  )  NOT  NULL DEFAULT  '0', MoreBreak int( 10  )  NOT  NULL DEFAULT  '0', EarlyOut int( 10  )  NOT  NULL DEFAULT  '0', LateOut int( 10  )  NOT  NULL DEFAULT  '0', Normal int( 10  )  NOT  NULL DEFAULT  '0', Grace int( 10  )  NOT  NULL DEFAULT  '0', Overtime int( 10  )  NOT  NULL DEFAULT  '0', AOvertime int( 10  )  NOT  NULL DEFAULT  '0', Day varchar( 50  )  DEFAULT NULL , Flag varchar( 10  )  NOT  NULL DEFAULT  'Black', p_flag int( 11  )  NOT  NULL DEFAULT  '0', LateIn_flag int( 1  )  NOT  NULL DEFAULT  '0', EarlyOut_flag int( 1  )  NOT  NULL DEFAULT  '0', MoreBreak_flag int( 1  )  NOT  NULL DEFAULT  '0', OT1 varchar( 255  )  NOT  NULL DEFAULT  'Saturday', OT2 varchar( 255  )  NOT  NULL DEFAULT  'Sunday', NightFlag int( 11  )  NOT  NULL DEFAULT  '0', RotateFlag int( 11  )  NOT  NULL DEFAULT  '0', Remark varchar( 1024  )  NOT  NULL DEFAULT  '.', PRIMARY  KEY (  AttendanceID  ) , UNIQUE  KEY  AEA (  EmployeeID ,  ADate  ) , KEY  g_id (  group_id  ) , KEY  EmployeeID (  EmployeeID  )  ) ";
        safeUpdateIData($this_conn, $query, true);
        $query = "CREATE  TABLE  AccessArchive.archive_dm (  DayMasterID int( 10  )  NOT  NULL  DEFAULT  '0' , e_id int( 10  )  NOT  NULL DEFAULT  '0', TDate int( 10  )  NOT  NULL DEFAULT  '0', archive_dm.Entry varchar( 6  )  DEFAULT NULL , Start varchar( 6  )  DEFAULT NULL , BreakOut varchar( 6  )  DEFAULT NULL , BreakIn varchar( 6  )  DEFAULT NULL , Close varchar( 6  )  DEFAULT NULL , archive_dm.Exit varchar( 6  )  DEFAULT NULL , p_flag int( 11  )  NOT  NULL DEFAULT  '0', group_id int( 10  )  NOT  NULL DEFAULT  '0', Flag varchar( 10  )  NOT  NULL DEFAULT  'Black', Work int( 11  )  NOT  NULL DEFAULT  '0', PRIMARY  KEY (  DayMasterID  ) , UNIQUE  KEY  DET (  e_id ,  TDate  ) , KEY  p_flag (  p_flag  ) , KEY  e_id (  e_id  )  )";
        safeUpdateIData($this_conn, $query, true);
        $query = "CREATE TABLE AccessArchive.archive_trans (TransactID int( 10 ) NOT NULL AUTO_INCREMENT , Transactdate int( 10 ) NOT NULL DEFAULT '0', Transacttime int( 10 ) NOT NULL DEFAULT '0', Username varchar( 255 ) DEFAULT NULL , Transactquery varchar( 1024 ) NOT NULL , PRIMARY KEY ( TransactID ) , KEY TransactID ( TransactID ) )";
        safeUpdateIData($this_conn, $query, true);
        $query = "CREATE TABLE AccessArchive.FlagDayRotation ( FlagDayRotationID int( 11 ) NULL, e_id int( 11 ) NOT NULL DEFAULT '0', e_date int( 8 ) NOT NULL DEFAULT '0', g_id int( 11 ) NOT NULL DEFAULT '0', Flag varchar( 1024 ) NULL DEFAULT 'Black', Rotate int( 1 ) NOT NULL DEFAULT '0', RecStat int( 1 ) NOT NULL DEFAULT '0', Remark varchar( 1024 ) NULL, OT1 varchar( 10 ) NULL, OT2 varchar( 10 ) NULL, group_id int( 11 ) NOT NULL DEFAULT '0', PRIMARY KEY ( FlagDayRotationID ) , UNIQUE KEY FDRED ( e_id , e_date ) ) ";
        safeUpdateIData($this_conn, $query, true);
        $query = "CREATE TABLE AccessArchive.ShiftRoster ( ShiftRosterID int( 11 ) NULL, e_id int( 11 ) NOT NULL DEFAULT '0', e_date int( 11 ) NOT NULL DEFAULT '0', e_group int( 11 ) NOT NULL DEFAULT '0', PRIMARY KEY ( ShiftRosterID ) , UNIQUE KEY ShiftRoster ( e_id , e_date , e_group ) ) ";
        safeUpdateIData($this_conn, $query, true);
        $query = "GRANT ALL PRIVILEGES ON accessarchive.* TO 'fdmsusr'@'localhost' WITH GRANT OPTION";
        safeUpdateIData($this_conn, $query, true);
        $query = "GRANT ALL PRIVILEGES ON accessarchive.* TO 'fdmsusr'@'%' WITH GRANT OPTION";
        safeUpdateIData($this_conn, $query, true);
        $query = "ALTER TABLE AccessArchive.archive_am MODIFY `Remark` VARCHAR( 1024 ) DEFAULT NULL";
        safeUpdateIData($this_conn, $query, true);
        $query = "ALTER TABLE AccessArchive.archive_trans MODIFY `Transactquery` VARCHAR( 1024 ) DEFAULT NULL";
        safeUpdateIData($this_conn, $query, true);
        $query = "ALTER TABLE AccessArchive.archive_am ADD EarlyIn_flag INT( 1 ) NULL DEFAULT '0' ";
        safeUpdateIData($this_conn, $query, true);
        $query = "ALTER TABLE AccessArchive.FlagDayRotation ADD OTH INT NULL DEFAULT '0'";
        safeUpdateIData($this_conn, $query, true);
        $query = "ALTER TABLE AccessArchive.FlagDayRotation ADD OT VARCHAR(5) DEFAULT NULL";
        safeUpdateIData($this_conn, $query, true);
        $query = "ALTER TABLE AccessArchive.archive_am MODIFY `Remark` VARCHAR( 1024 ) DEFAULT NULL";
        safeUpdateIData($this_conn, $query, true);
        $query = "ALTER TABLE AccessArchive.archive_trans MODIFY `Transactquery` VARCHAR( 1024 ) DEFAULT NULL";
        safeUpdateIData($this_conn, $query, true);
        $query = "ALTER TABLE AccessArchive.FlagDayRotation 
            MODIFY Remark VARCHAR(1024) NULL, 
            MODIFY OT1 VARCHAR(10) NULL, 
            MODIFY OT2 VARCHAR(10) NULL, 
            MODIFY Flag VARCHAR(10) NULL";
        safeUpdateIData($this_conn, $query, true);

        $query = "ALTER TABLE AccessArchive.archive_am 
            MODIFY Flag VARCHAR(10) NULL DEFAULT 'Black', 
            MODIFY OT1 VARCHAR(255) NULL DEFAULT 'Saturday', 
            MODIFY OT2 VARCHAR(255) NULL DEFAULT 'Sunday'";
        safeUpdateIData($this_conn, $query, true);

        $query = "ALTER TABLE AccessArchive.archive_dm 
            MODIFY Flag VARCHAR(10) NULL DEFAULT 'Black'";
        safeUpdateIData($this_conn, $query, true);

        $query = "ALTER TABLE AccessArchive.archive_trans 
            MODIFY Username VARCHAR(255) NULL DEFAULT NULL, 
            MODIFY Transactquery VARCHAR(1024) NULL";
        safeUpdateIData($this_conn, $query, true);

        $query = "ALTER TABLE AccessArchive.archive_am 
            MODIFY Remark VARCHAR(1024) NULL, 
            ADD PHF INT(1) NOT NULL DEFAULT 0";
        safeUpdateIData($this_conn, $query, true);

        $query = "ALTER TABLE Access.AttendanceMaster 
            MODIFY Remark VARCHAR(1024) NULL";
        safeUpdateIData($this_conn, $query, true);
        $query = "UPDATE AccessArchive.archive_am SET AccessArchive.archive_am.PHF = 1 WHERE AccessArchive.archive_am.ADate IN (SELECT Access.OTDate.OTDate FROM Access.OTDate)";
        safeUpdateIData($this_conn, $query, true);
        $query = "ALTER TABLE accessarchive.archive_tenter ADD D_Latitude INT(10) NULL DEFAULT 0";
        safeUpdateIData($this_conn, $query, true);
        $query = "ALTER TABLE accessarchive.archive_tenter ADD D_Longitude INT(10) NULL DEFAULT 0";
        safeUpdateIData($this_conn, $query, true);
        $query = "ALTER TABLE accessarchive.archive_tenter ADD C_MobilePhone INT(10) NULL DEFAULT 0";
        safeUpdateIData($this_conn, $query, true);
        $query = "INSERT IGNORE INTO accessarchive.archive_tenter (e_date, e_time, g_id, e_id, e_name, e_idno, e_group, e_user, e_mode, e_type, e_result, e_etc, ed, p_flag, e_uptime, e_upmode, D_Latitude, D_Longitude, C_MobilePhone) (SELECT e_date, e_time, g_id, e_id, e_name, e_idno, e_group, e_user, e_mode, e_type, e_result, e_etc, ed, p_flag, e_uptime, e_upmode, D_Latitude, D_Longitude, C_MobilePhone from Access.tenter WHERE e_date < 20180101)";
        safeUpdateIData($this_conn, $query, true);
        $query = "DELETE FROM Access.tenter where e_date < 20180101";
        safeUpdateIData($this_conn, $query, true);
        $query = "ALTER TABLE Access.tenter DROP COLUMN ed";
        safeUpdateIData($this_conn, $query, true);
        $query = "ALTER TABLE Access.tenter ADD COLUMN ed INT(10) NULL AUTO_INCREMENT UNIQUE AFTER e_etc";
        safeUpdateIData($this_conn, $query, true);
        $query = "ALTER TABLE Access.tenter MODIFY COLUMN ed INT(10) AUTO_INCREMENT";
        safeUpdateIData($this_conn, $query, true);
        $query = "ALTER TABLE Access.AttendanceMaster DROP COLUMN transferedtoc";
        safeUpdateIData($this_conn, $query, true);
        $query = "ALTER TABLE Access.DayMaster DROP COLUMN transferedtoc";
        safeUpdateIData($this_conn, $query, true);
        $text = "Version Update V.35.52.116.202";
        $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('" . $text . "', " . insertToday() . ", '" . getNow() . "')";
        safeUpdateIData($iconn, $query, true);
        print "\n" . $text;
    }
    V3553116203($conn, $iconn);
}

function V3552116201($conn, $iconn) {
    $text = "Version Update V.35.52.116.201";
    print "\n" . $text;
    V3552116202($conn, $iconn);
}

function V3551116200($conn, $iconn) {
    global $mac_in_version;
    $uconn = mysqli_connect("127.0.0.1", "unisuser", "unisamho", "UNIS");
    if (getRegister($mac_in_version, 7) == "7") {
        $query = "DROP TRIGGER IF EXISTS UNIS.i_tuser ";
        mysqli_query($uconn, $query);
        $query = "CREATE TRIGGER UNIS.i_tuser AFTER INSERT ON UNIS.tuser FOR EACH ROW INSERT INTO Access.tuser (id, name, reg_date, datelimit, PassiveType, dept, company, phone, remark) VALUES (New.L_ID, New.C_Name, IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), IFNULL( ( CASE WHEN New.L_OptDateLimit = 0 THEN CONCAT('N', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN CONCAT('Y', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) >= CURDATE()*1 THEN CONCAT('N', New.C_DateLimit) ELSE datelimit END ), 'N2001010120200101'), IFNULL( ( CASE WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN 'RSN' ELSE 'ACT' END ), 'ACT'), (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID), New.C_Unique, New.C_UserMessage) ";
        mysqli_query($uconn, $query);
        $query = "DROP TRIGGER IF EXISTS UNIS.u_tuser ";
        mysqli_query($uconn, $query);
        $query = "CREATE TRIGGER UNIS.u_tuser AFTER UPDATE ON UNIS.tuser FOR EACH ROW UPDATE Access.tuser SET name = New.C_Name, reg_date = IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), datelimit = IFNULL( ( CASE WHEN New.L_OptDateLimit = 0 THEN CONCAT('N', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN CONCAT('Y', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) >= CURDATE()*1 THEN CONCAT('N', New.C_DateLimit) ELSE datelimit END ), 'N2001010120200101'), PassiveType = IFNULL( ( CASE WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN 'RSN' ELSE 'ACT' END ), 'ACT'), dept = (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), company = (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID), phone = New.C_Unique, remark = New.C_UserMessage WHERE id = New.L_ID ";
        mysqli_query($uconn, $query);
    }
    $query = "UPDATE Access.OtherSettingMaster SET EX2 = 'V.35.51.116.200'";
    safeUpdateIData($iconn, $query, true);
    $text = "Version Update V.35.51.116.200";
    $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('" . $text . "', " . insertToday() . ", '" . getNow() . "')";
    safeUpdateIData($iconn, $query, true);
    print "\n" . $text;
    V3552116201($conn, $iconn);
}

function V3550116199($conn, $iconn) {
    global $mac_in_version;
    $uconn = mysqli_connect("127.0.0.1", "unisuser", "unisamho", "UNIS");
    if (getRegister($mac_in_version, 7) == "7") {
        $query = "DROP TRIGGER IF EXISTS UNIS.i_tuser ";
        mysqli_query($uconn, $query);
        $query = "CREATE TRIGGER UNIS.i_tuser AFTER INSERT ON UNIS.tuser FOR EACH ROW INSERT INTO Access.tuser (id, name, reg_date, datelimit, PassiveType, dept, company, phone, remark) VALUES (New.L_ID, New.C_Name, IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), IFNULL( ( CASE WHEN New.L_OptDateLimit = 0 THEN CONCAT('N', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN CONCAT('Y', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) >= CURDATE()*1 THEN CONCAT('N', New.C_DateLimit) ELSE datelimit END ), 'N2001010120200101'), IFNULL( ( CASE WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN 'RSN' ELSE 'ACT' END ), 'ACT'), (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID), New.C_Unique, (SELECT C_Remark FROM UNIS.temploye WHERE L_UID = New.L_ID)) ";
        mysqli_query($uconn, $query);
        $query = "DROP TRIGGER IF EXISTS UNIS.u_tuser ";
        mysqli_query($uconn, $query);
        $query = "CREATE TRIGGER UNIS.u_tuser AFTER UPDATE ON UNIS.tuser FOR EACH ROW UPDATE Access.tuser SET name = New.C_Name, reg_date = IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), datelimit = IFNULL( ( CASE WHEN New.L_OptDateLimit = 0 THEN CONCAT('N', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN CONCAT('Y', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) >= CURDATE()*1 THEN CONCAT('N', New.C_DateLimit) ELSE datelimit END ), 'N2001010120200101'), PassiveType = IFNULL( ( CASE WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN 'RSN' ELSE 'ACT' END ), 'ACT'), dept = (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), company = (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID), phone = New.C_Unique, remark = (SELECT C_Remark FROM UNIS.temploye WHERE L_UID = New.L_ID) WHERE id = New.L_ID ";
        mysqli_query($uconn, $query);
    }
    $text = "Version Update V.35.50.116.199";
    print "\n" . $text;
    V3551116200($conn, $iconn);
}

function V3550115198($conn, $iconn) {
    global $mac_in_version;
    $query = "ALTER TABLE Access.tuser ADD PassiveType VARCHAR(5) NOT NULL DEFAULT 'ACT'";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE Access.tuser ADD PassiveRemark VARCHAR(255) NOT NULL DEFAULT '.'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE Access.tuser SET PassiveType = 'ACT' WHERE datelimit LIKE 'N%'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE Access.tuser SET PassiveType = 'RSN' WHERE datelimit LIKE 'Y%' AND SUBSTRING(tuser.datelimit, 10, 8) < '" . insertToday() . "' AND SUBSTRING(tuser.datelimit, 10, 8) NOT LIKE '19770430' ";
    safeUpdate($conn, $query, true);
    $query = "UPDATE Access.tuser SET PassiveType = 'FDA' WHERE datelimit LIKE 'Y%' AND SUBSTRING(tuser.datelimit, 10, 8) < '" . insertToday() . "' AND SUBSTRING(tuser.datelimit, 10, 8) = '19770430' ";
    safeUpdate($conn, $query, true);
    $uconn = mysqli_connect("127.0.0.1", "unisuser", "unisamho", "UNIS");
    $query = "SELECT id, datelimit, PassiveType FROM Access.tuser";
    $resultquery = mysqli_query($iconn, $query);
    while ($cur = mysqli_fetch_row($resultquery)) {
        if ($cur[2] == "ACT") {
            $query = "UPDATE UNIS.tuser SET L_OptDateLimit = 1, C_DateLimit = '" . substr($cur[1], 1, 8) . "" . substr($cur[1], 9, 8) . "' WHERE L_ID = " . $cur[0];
        } else {
            if (substr($cur[1], 9, 8) < insertToday()) {
                $query = "UPDATE UNIS.tuser SET L_OptDateLimit = 1, C_DateLimit = '" . substr($cur[1], 1, 8) . "" . substr($cur[1], 9, 8) . "' WHERE L_ID = " . $cur[0];
            } else {
                $query = "UPDATE UNIS.tuser SET L_OptDateLimit = 1, C_DateLimit = '" . substr($cur[1], 1, 8) . "" . getLastDay(insertToday(), 1) . "' WHERE L_ID = " . $cur[0];
            }
        }
        mysqli_query($uconn, $query);
    }
    if (getRegister($mac_in_version, 7) == "82" || getRegister($mac_in_version, 7) == "103" || getRegister($mac_in_version, 7) == "106" || getRegister($mac_in_version, 7) == "107" || getRegister($mac_in_version, 7) == "126" || getRegister($mac_in_version, 7) == "57" || getRegister($mac_in_version, 7) == "58" || getRegister($mac_in_version, 7) == "59" || getRegister($mac_in_version, 7) == "60") {
        $query = "DROP TRIGGER IF EXISTS UNIS.i_tuser ";
        mysqli_query($uconn, $query);
        $query = "CREATE TRIGGER UNIS.i_tuser AFTER INSERT ON UNIS.tuser FOR EACH ROW INSERT INTO Access.tuser (id, name, reg_date, datelimit, PassiveType, dept, company, idno) VALUES (New.L_ID, New.C_Name, IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), IFNULL( ( CASE WHEN New.L_OptDateLimit = 0 THEN CONCAT('N', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN CONCAT('Y', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) >= CURDATE()*1 THEN CONCAT('N', New.C_DateLimit) ELSE datelimit END ), 'N2001010120200101'), IFNULL( ( CASE WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN 'RSN' ELSE 'ACT' END ), 'ACT'), (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID), New.C_Unique) ";
        mysqli_query($uconn, $query);
        $query = "DROP TRIGGER IF EXISTS UNIS.u_tuser ";
        mysqli_query($uconn, $query);
        $query = "CREATE TRIGGER UNIS.u_tuser AFTER UPDATE ON UNIS.tuser FOR EACH ROW UPDATE Access.tuser SET name = New.C_Name, reg_date = IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), datelimit = IFNULL( ( CASE WHEN New.L_OptDateLimit = 0 THEN CONCAT('N', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN CONCAT('Y', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) >= CURDATE()*1 THEN CONCAT('N', New.C_DateLimit) ELSE datelimit END ), 'N2001010120200101'), PassiveType = IFNULL( ( CASE WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN 'RSN' ELSE 'ACT' END ), 'ACT'), dept = (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), company = (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID), idno = New.C_Unique WHERE id = New.L_ID ";
        mysqli_query($uconn, $query);
    } else {
        $query = "DROP TRIGGER IF EXISTS UNIS.i_tuser ";
        mysqli_query($uconn, $query);
        $query = "CREATE TRIGGER UNIS.i_tuser AFTER INSERT ON UNIS.tuser FOR EACH ROW INSERT INTO Access.tuser (id, name, reg_date, datelimit, PassiveType, dept, company) VALUES (New.L_ID, New.C_Name, IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), IFNULL( ( CASE WHEN New.L_OptDateLimit = 0 THEN CONCAT('N', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN CONCAT('Y', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) >= CURDATE()*1 THEN CONCAT('N', New.C_DateLimit) ELSE datelimit END ), 'N2001010120200101'), IFNULL( ( CASE WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN 'RSN' ELSE 'ACT' END ), 'ACT'), (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID)) ";
        mysqli_query($uconn, $query);
        $query = "DROP TRIGGER IF EXISTS UNIS.u_tuser ";
        mysqli_query($uconn, $query);
        $query = "CREATE TRIGGER UNIS.u_tuser AFTER UPDATE ON UNIS.tuser FOR EACH ROW UPDATE Access.tuser SET name = New.C_Name, reg_date = IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), datelimit = IFNULL( ( CASE WHEN New.L_OptDateLimit = 0 THEN CONCAT('N', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN CONCAT('Y', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) >= CURDATE()*1 THEN CONCAT('N', New.C_DateLimit) ELSE datelimit END ), 'N2001010120200101'), PassiveType = IFNULL( ( CASE WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN 'RSN' ELSE 'ACT' END ), 'ACT'), dept = (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), company = (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID) WHERE id = New.L_ID ";
        mysqli_query($uconn, $query);
    }
    unlink("UNISynch.php");
    unlink("UNISynch.bat");
    unlink("OIMigrateMySQL.php");
    unlink("Functions-ica.php");
    unlink("HalogenSequence.php");
    unlink("ACHalogen.php");
    unlink("ACHalogen.bat");
    unlink("UNISUpgrade1.php");
    unlink("UNISUpgrade2.php");
    unlink("UNISTrigger1.php");
    unlink("UNISTrigger2.php");
    unlink("BSMigrate.php");
    unlink("AAMigrate-Stallion.php");
    unlink("TestCoupon.php");
    safeUpdateIData($iconn, $query, true);
    $text = "Version Update V.35.50.115.198";
    print "\n" . $text;
    V3550116199($conn, $iconn);
}

function V3550115197($conn, $iconn) { 
    global $mac_in_version;
    $uconn = mysqli_connect("127.0.0.1", "unisuser", "unisamho", "UNIS");
    if (getRegister($mac_in_version, 7) == "82" || getRegister($mac_in_version, 7) == "103" || getRegister($mac_in_version, 7) == "106" || getRegister($mac_in_version, 7) == "107" || getRegister($mac_in_version, 7) == "126" || getRegister($mac_in_version, 7) == "57" || getRegister($mac_in_version, 7) == "58" || getRegister($mac_in_version, 7) == "59" || getRegister($mac_in_version, 7) == "60") {
        $query = "DROP TRIGGER IF EXISTS UNIS.i_tuser ";
        mysqli_query($uconn, $query);
        $query = "CREATE TRIGGER UNIS.i_tuser AFTER INSERT ON UNIS.tuser FOR EACH ROW INSERT INTO Access.tuser (id, name, reg_date, datelimit, dept, company, idno) VALUES (New.L_ID, New.C_Name, IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), IFNULL( IF(New.L_OptDateLimit = 1, CONCAT('Y', New.C_DateLimit), CONCAT('N', New.C_DateLimit)), 'N2001010120200101'), (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID), New.C_Unique) ";
        mysqli_query($uconn, $query);
        $query = "DROP TRIGGER IF EXISTS UNIS.u_tuser ";
        mysqli_query($uconn, $query);
        $query = "CREATE TRIGGER UNIS.u_tuser AFTER UPDATE ON UNIS.tuser FOR EACH ROW UPDATE Access.tuser SET name = New.C_Name, reg_date = IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), datelimit = IFNULL( IF(New.L_OptDateLimit = 1, CONCAT('Y', New.C_DateLimit), CONCAT('N', New.C_DateLimit)), 'N2001010120200101'), dept = (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), company = (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID), idno = New.C_Unique WHERE id = New.L_ID ";
        mysqli_query($uconn, $query);
    } else {
        $query = "DROP TRIGGER IF EXISTS UNIS.i_tuser ";
        mysqli_query($uconn, $query);
        $query = "CREATE TRIGGER UNIS.i_tuser AFTER INSERT ON UNIS.tuser FOR EACH ROW INSERT INTO Access.tuser (id, name, reg_date, datelimit, dept, company) VALUES (New.L_ID, New.C_Name, IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), IFNULL( IF(New.L_OptDateLimit = 1, CONCAT('Y', New.C_DateLimit), CONCAT('N', New.C_DateLimit)), 'N2001010120200101'), (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID)) ";
        mysqli_query($uconn, $query);
        $query = "DROP TRIGGER IF EXISTS UNIS.u_tuser ";
        mysqli_query($uconn, $query);
        $query = "CREATE TRIGGER UNIS.u_tuser AFTER UPDATE ON UNIS.tuser FOR EACH ROW UPDATE Access.tuser SET name = New.C_Name, reg_date = IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), datelimit = IFNULL( IF(New.L_OptDateLimit = 1, CONCAT('Y', New.C_DateLimit), CONCAT('N', New.C_DateLimit)), 'N2001010120200101'), dept = (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), company = (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID) WHERE id = New.L_ID ";
        mysqli_query($uconn, $query);
    }
    $query = "DROP TRIGGER IF EXISTS UNIS.i_temploye ";
    mysqli_query($uconn, $query);
    $query = "CREATE TRIGGER UNIS.i_temploye AFTER INSERT ON UNIS.temploye FOR EACH ROW UPDATE Access.tuser SET dept = (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_UID), company = (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_UID) WHERE id = New.L_UID ";
    mysqli_query($uconn, $query);
    $query = "DROP TRIGGER IF EXISTS UNIS.u_temploye ";
    mysqli_query($uconn, $query);
    $query = "CREATE TRIGGER UNIS.u_temploye AFTER UPDATE ON UNIS.temploye FOR EACH ROW UPDATE Access.tuser SET dept = (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_UID), company = (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_UID) WHERE id = New.L_UID ";
    mysqli_query($uconn, $query);
    unlink("UNISynch.php");
    unlink("UNISynch.bat");
    unlink("OIMigrateMySQL.php");
    $text = "Version Update V.35.50.115.197";
    print "\n" . $text;
    V3550115198($conn, $iconn);
}

function V3550115196($conn, $iconn) { 
    global $mac_in_version;
    $uconn = mysqli_connect("127.0.0.1", "unisuser", "unisamho", "UNIS");
    if (!(getRegister($mac_in_version, 7) == "82" || getRegister($mac_in_version, 7) == "103" || getRegister($mac_in_version, 7) == "106" || getRegister($mac_in_version, 7) == "107" || getRegister($mac_in_version, 7) == "126" || getRegister($mac_in_version, 7) == "57" || getRegister($mac_in_version, 7) == "58" || getRegister($mac_in_version, 7) == "59" || getRegister($mac_in_version, 7) == "60")) {
        $query = "DROP TRIGGER IF EXISTS UNIS.i_tuser ";
        mysqli_query($uconn, $query);
        $query = "CREATE TRIGGER UNIS.i_tuser AFTER INSERT ON UNIS.tuser FOR EACH ROW INSERT INTO Access.tuser (id, name, reg_date, datelimit, dept, company) VALUES (New.L_ID, New.C_Name, IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), IFNULL( IF(New.L_OptDateLimit = 1, CONCAT('Y', New.C_DateLimit), CONCAT('N', New.C_DateLimit)), 'N2001010120200101'), (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID)) ";
        mysqli_query($uconn, $query);
        $query = "DROP TRIGGER IF EXISTS UNIS.u_tuser ";
        mysqli_query($uconn, $query);
        $query = "CREATE TRIGGER UNIS.u_tuser AFTER UPDATE ON UNIS.tuser FOR EACH ROW UPDATE Access.tuser SET name = New.C_Name, reg_date = IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), datelimit = IFNULL( IF(New.L_OptDateLimit = 1, CONCAT('Y', New.C_DateLimit), CONCAT('N', New.C_DateLimit)), 'N2001010120200101'), dept = (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), company = (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID) WHERE id = New.L_ID ";
        mysqli_query($uconn, $query);
    }
    $text = "Version Update V.35.50.115.196";
    print "\n" . $text;
    V3550115197($conn, $iconn);
}

function V3550115195($conn, $iconn) { 
    global $mac_in_version;
    $uconn = mysqli_connect("127.0.0.1", "unisuser", "unisamho", "UNIS");
    if (getRegister($mac_in_version, 7) == "82" || getRegister($mac_in_version, 7) == "103" || getRegister($mac_in_version, 7) == "106" || getRegister($mac_in_version, 7) == "107" || getRegister($mac_in_version, 7) == "126" || getRegister($mac_in_version, 7) == "57" || getRegister($mac_in_version, 7) == "58" || getRegister($mac_in_version, 7) == "59" || getRegister($mac_in_version, 7) == "60") {
        $query = "DROP TRIGGER IF EXISTS UNIS.i_tuser ";
        mysqli_query($uconn, $query);
        $query = "CREATE TRIGGER UNIS.i_tuser AFTER INSERT ON UNIS.tuser FOR EACH ROW INSERT INTO Access.tuser (id, name, reg_date, datelimit, dept, company, idno) VALUES (New.L_ID, New.C_Name, IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), IFNULL( IF(New.L_OptDateLimit = 1, CONCAT('Y', New.C_DateLimit), CONCAT('N', New.C_DateLimit)), 'N2001010120200101'), (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID), New.C_Unique) ";
        mysqli_query($uconn, $query);
        $query = "DROP TRIGGER IF EXISTS UNIS.u_tuser ";
        mysqli_query($uconn, $query);
        $query = "CREATE TRIGGER UNIS.u_tuser AFTER UPDATE ON UNIS.tuser FOR EACH ROW UPDATE Access.tuser SET name = New.C_Name, reg_date = IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), datelimit = IFNULL( IF(New.L_OptDateLimit = 1, CONCAT('Y', New.C_DateLimit), CONCAT('N', New.C_DateLimit)), 'N2001010120200101'), dept = (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), company = (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID), idno = New.C_Unique WHERE id = New.L_ID ";
        mysqli_query($uconn, $query);
    }
    $text = "Version Update V.35.50.115.195";
    print "\n" . $text;
    V3550115196($conn, $iconn);
}

function V3549115194($conn, $iconn) {
    unlink("PayMaster.php");
    unlink("PayMaster.bat");
    $query = "ALTER TABLE Access.PayrollMap ADD F1 VARCHAR(255) NULL, ADD F2 VARCHAR(255) NULL, ADD F3 VARCHAR(255) NULL, ADD F4 VARCHAR(255) NULL, ADD F5 VARCHAR(255) NULL, ADD F6 VARCHAR(255) NULL, ADD F7 VARCHAR(255) NULL, ADD F8 VARCHAR(255) NULL, ADD F9 VARCHAR(255) NULL";
    safeUpdateIData($iconn, $query, true);
    V3550115195($conn, $iconn);
}

function V3548114193($conn, $iconn) {
    $uconn = mysqli_connect("127.0.0.1", "unisuser", "unisamho", "UNIS");
    $query = "DROP TRIGGER IF EXISTS UNIS.i_tuser ";
    mysqli_query($uconn, $query);
    $query = "CREATE TRIGGER UNIS.i_tuser AFTER INSERT ON UNIS.tuser FOR EACH ROW INSERT INTO Access.tuser (id, name, reg_date, datelimit, dept, company) VALUES (New.L_ID, New.C_Name, IFNULL(SUBSTRING(New.C_RegDate, 0, 8), 20010101), IFNULL(CONCAT('N', New.C_DateLimit), 'N2001010120200101'), (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID)) ";
    mysqli_query($uconn, $query);
    $query = "DROP TRIGGER IF EXISTS UNIS.u_tuser ";
    safeUpdateIData($iconn, $query, true);
    $query = "CREATE TRIGGER UNIS.u_tuser AFTER UPDATE ON UNIS.tuser FOR EACH ROW UPDATE Access.tuser SET name = New.C_Name, reg_date = SUBSTRING(New.C_RegDate, 0,8), dept = (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), company = (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID) WHERE id = New.L_ID ";
    mysqli_query($uconn, $query);
    $query = "ALTER TABLE Access.MigrateMaster ADD MonthNo INT(5) NULL DEFAULT 0";
    safeUpdateIData($iconn, $query, true);
    V3549115194($conn, $iconn);
}

function V3547113192($conn, $iconn) {
    $uconn = mysqli_connect("127.0.0.1", "unisuser", "unisamho", "UNIS");
    $query = "DROP TRIGGER IF EXISTS UNIS.i_tuser ";
    mysqli_query($uconn, $query);
    $query = "CREATE TRIGGER UNIS.i_tuser AFTER INSERT ON UNIS.tuser FOR EACH ROW INSERT INTO Access.tuser (id, name, reg_date, datelimit, dept, company) VALUES (New.L_ID, New.C_Name, IFNULL(SUBSTRING(New.C_RegDate, 0, 8), 20010101), IFNULL(CONCAT('N', New.C_DateLimit), 'N2001010120200101'), (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID)) ";
    mysqli_query($uconn, $query);
    $query = "DROP TRIGGER IF EXISTS UNIS.u_tuser ";
    mysqli_query($uconn, $query);
    $query = "CREATE TRIGGER UNIS.u_tuser AFTER UPDATE ON UNIS.tuser FOR EACH ROW UPDATE Access.tuser SET name = New.C_Name, reg_date = SUBSTRING(New.C_RegDate, 0,8), dept = (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), company = (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID) WHERE id = New.L_ID ";
    mysqli_query($uconn, $query);
    V3548114193($conn, $iconn);
}

function V3547113191($conn, $iconn) {
    $query = "UPDATE Access.tuser SET pwd = '" . strrev("password") . "' WHERE pwd IS NULL OR LENGTH(pwd) < 2";
    safeUpdateIData($iconn, $query, true);
    V3547113192($conn, $iconn);
}

function V3547113190($conn, $iconn) {
    $query = "ALTER TABLE Access.tent ADD  D_Latitude INT(10) NULL DEFAULT 0";
    safeUpdateIData($iconn, $query, true);
    $query = "ALTER TABLE Access.tent ADD D_Longitude INT(10) NULL DEFAULT 0 ";
    safeUpdateIData($iconn, $query, true);
    $query = "ALTER TABLE Access.tent ADD C_MobilePhone VARCHAR(255) NULL ";
    safeUpdateIData($iconn, $query, true);
    $query = "ALTER TABLE Access.tenter ADD  D_Latitude INT(10) NULL DEFAULT 0 ";
    safeUpdateIData($iconn, $query, true);
    $query = "ALTER TABLE Access.tenter ADD D_Longitude INT(10) NULL DEFAULT 0 ";
    safeUpdateIData($iconn, $query, true);
    $query = "ALTER TABLE Access.tenter ADD C_MobilePhone VARCHAR(255) NULL ";
    safeUpdateIData($iconn, $query, true);
    $query = "ALTER TABLE Access.tuser MODIFY COLUMN pwd VARCHAR(255) NULL ";
    safeUpdateIData($iconn, $query, true);
    $query = "UPDATE Access.tuser SET pwd = '" . decryptString("letmein") . "' WHERE pwd IS NULL ";
    safeUpdateIData($iconn, $query, true);
    V3547113191($conn, $iconn);
}

function V3547112189($conn) {
    mysqli_select_db($conn, "UNIS");
    $query = "ALTER TABLE UNIS.cAuthority ADD L_MntMgr INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_MntClient INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_MntTerminal INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_MntAuthLog INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_MntEvent INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_TmnMgr INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_TmnAdd INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_TmnMod INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_TmnDel INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_TmnUpgrade INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_TmnOption INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_TmnAdmin INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_TmnSendFile INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_EmpMgr INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_EmpAdd INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_EmpMod INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_EmpDel INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_EmpSendTerminal INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_EmpTerminalMng INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_EmpRegAdmin INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_VstMgr INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_VstAdd INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_VstMod INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_VstDel INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_BlckMgr INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_BlckChange INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_BlckRelease INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_BlckDel INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_BlckMod INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_AccMgr INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_AccSet INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_MapMgr INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_MapSet INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_TnaMgr INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_TnaSet INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_TnaSpecial INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_TnaWork INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_TnaOutState INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_TnaOutExcRecord INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_TnaSummary INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_TnaSendResult INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_TnaDelData INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_MealMgr INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_MealOutRecord INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_MealDelData INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_MealOutDept INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_MealOutPerson INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_MealSet INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_LogMgr INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_LogOutRecord INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_LogDelRecord INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_SetRegInfo INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_SetMgr INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_SetServer INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_SetPwd INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_SetMail INT NULL DEFAULT 0; ALTER TABLE cAuthority ADD L_SetEtc INT NULL DEFAULT 0; ALTER TABLE cTimezone ADD L_AuthValue INT NULL DEFAULT 0; CREATE TABLE UNIS.iACUInfo (L_TID INT(10) NULL DEFAULT 0, C_PartitionStatus VARCHAR(255) NULL, C_ZoneStatus VARCHAR(255) NULL, C_LockStatus VARCHAR(255) NULL, C_ReaderStatus VARCHAR(255) NULL, C_ReaderVer1 VARCHAR(255) NULL, C_ReaderVer2 VARCHAR(255) NULL, C_ReaderVer3 VARCHAR(255) NULL, C_ReaderVer4 VARCHAR(255) NULL, C_ReaderVer5 VARCHAR(255) NULL, C_ReaderVer6 VARCHAR(255) NULL, C_ReaderVer7 VARCHAR(255) NULL, C_ReaderVer8 VARCHAR(255) NULL, C_ReaderName0 VARCHAR(255) NULL, C_ReaderName1 VARCHAR(255) NULL, C_ReaderName2 VARCHAR(255) NULL, C_ReaderName3 VARCHAR(255) NULL, C_ReaderName4 VARCHAR(255) NULL, C_ReaderName5 VARCHAR(255) NULL, C_ReaderName6 VARCHAR(255) NULL, C_ReaderName7 VARCHAR(255) NULL, C_WiegandName1 VARCHAR(255) NULL, C_WiegandName2 VARCHAR(255) NULL, C_WiegandName3 VARCHAR(255) NULL, C_WiegandName4 VARCHAR(255) NULL, PRIMARY KEY (L_TID) ); CREATE TABLE UNIS.iAdminRestrict (L_UID INT(10) NULL DEFAULT 0, C_AccessGroup VARCHAR(255) NULL); CREATE TABLE UNIS.iDVRInfo (L_DVRID INT(10) NULL DEFAULT 0, C_DVRIP VARCHAR(255) NULL, L_DVRPort INT(10) NULL DEFAULT 0, C_DVRLoginID VARCHAR(255) NULL, C_DVRLoginPW VARCHAR(255) NULL, L_PrevTime INT(10) NULL DEFAULT 0; ALTER TABLE iMapTerminal ADD L_Size INT NULL DEFAULT 0; CREATE TABLE UNIS.iMobileKeyAdmin (C_ServerDNS VARCHAR(255) NULL, C_ClientID VARCHAR(255) NULL, C_Secret VARCHAR(255) NULL, C_EMail VARCHAR(255) NULL, C_Password VARCHAR(255) NULL, C_CountryCode VARCHAR(255) NULL, C_PhoneNo VARCHAR(255) NULL, C_SiteCode VARCHAR(255) NULL, C_MacAddr VARCHAR(255) NULL, L_tzIndex INT(10) NULL DEFAULT 0, L_tzBias INT(10) NULL DEFAULT 0, C_tzKeyName VARCHAR(255) NULL, C_TimeZone VARCHAR(255) NULL, C_Company VARCHAR(255) NULL, C_Country VARCHAR(255) NULL, L_SiteCode INT(10) NULL DEFAULT 0); CREATE TABLE UNIS.iNecessityField (L_Type INT(10) NULL DEFAULT 0, L_Index INT(10) NULL DEFAULT 0); CREATE TABLE UNIS.iUserMobileKey (L_UID INT(10) NULL DEFAULT 0, C_MobilePhone VARCHAR(255) NULL, C_CountryCode VARCHAR(255) NULL, L_KeyType INT(10) NULL DEFAULT 0, C_ImkeyPeriod VARCHAR(255) NULL, L_issuecount INT(10) NULL DEFAULT 0, C_KeyNo VARCHAR(255) NULL, L_NowIssue INT(10) NULL, B_UUID LONGBLOB NULL, PRIMARY KEY (L_UID) ); ALTER TABLE tAuditServer ADD C_Remark VARCHAR(255) NULL; ALTER TABLE tChangedInfo ADD L_ClientID INT(10) NULL DEFAULT 0; CREATE TABLE UNIS.tChangedInfo (C_CreateTime VARCHAR(255) NULL, L_Target INT(10) NULL DEFAULT 0, L_Procedure INT(10) NULL DEFAULT 0, L_TargetID INT(10) NULL DEFAULT 0, C_TargetCode VARCHAR(255) NULL, L_ClientID INT(10) NULL DEFAULT 0, UNIQUE INDEX PK_tChangedInfo (C_CreateTime, L_Target, L_Procedure, L_TargetID) ); ALTER TABLE tConfig ADD L_PanicDuress INT(10) NULL DEFAULT 0; ALTER TABLE tConfig ADD L_DefaultNotAccess INT(10) NULL DEFAULT 0; ALTER TABLE tConfig ADD L_ServerLanguage INT(10) NULL DEFAULT 0; ALTER TABLE tConfig ADD L_LFDLevel INT(10) NULL DEFAULT 0; ALTER TABLE tConfig ADD L_AuthData INT(10) NULL DEFAULT 0; ALTER TABLE tConfig ADD C_WebOpen VARCHAR(255) NULL; ALTER TABLE tConfig ADD C_MobileOpen VARCHAR(255) NULL; ALTER TABLE tConfig ADD L_AdminRestrict INT(10) NULL DEFAULT 0; ALTER TABLE tConfig ADD L_FindUserByFP INT(10) NULL DEFAULT 0; ALTER TABLE tEnter ADD D_Latitude INT(10) NULL DEFAULT 0; ALTER TABLE tEnter ADD D_Longitude INT(10) NULL DEFAULT 0; ALTER TABLE tEnter ADD C_MobilePhone VARCHAR(255) NULL; CREATE TABLE UNIS.tLogonHistory (C_DateTime VARCHAR(255) NULL, C_AccessType VARCHAR(255) NULL, L_UID INT(10) NULL DEFAULT 0, C_IP VARCHAR(255) NULL, C_LogonSuccess VARCHAR(255) NULL ); ALTER TABLE tMailConfig ADD L_DuressFinger INT(10) NULL DEFAULT 0; ALTER TABLE tMailConfig ADD L_ACU INT(10) NULL DEFAULT 0; ALTER TABLE tMailConfig ADD L_NoPermission INT(10) NULL DEFAULT 0; CREATE TABLE UNIS.tPasswdHistory (L_UID INT(10) NULL DEFAULT 0, C_OldRemotePW VARCHAR(255) NULL, C_NewRemotePW VARCHAR(255) NULL, C_Action VARCHAR(255) NULL, L_AdminID INT(10) NULL DEFAULT 0, C_UDateTime VARCHAR(255) NULL); CREATE TABLE UNIS.tPWConfig (L_LengthMin INT(10) NULL DEFAULT 0, L_LengthMax INT(10) NULL DEFAULT 0, L_DayLimit INT(10) NULL DEFAULT 0, C_NotAllowOldPW VARCHAR(255) NULL, L_WrongLimit INT(10) NULL DEFAULT 0, C_NotAllowDupChar VARCHAR(255) NULL, C_FirstChgPW VARCHAR(255) NULL, C_UpperLowerSame VARCHAR(255) NULL, C_NotAllowSameID VARCHAR(255) NULL, C_CreateOpt VARCHAR(255) NULL, C_OptUpper VARCHAR(255) NULL, C_OptLower VARCHAR(255) NULL, C_OptNumeric VARCHAR(255) NULL, C_OptSymbol VARCHAR(255) NULL, L_InitValue INT(10) NULL DEFAULT 0, C_InitPwd VARCHAR(255) NULL); ALTER TABLE tTerminal ADD L_AuthType INT(10) NULL DEFAULT 0; ALTER TABLE tTerminal ADD L_DVRID INT(10) NULL DEFAULT 0; ALTER TABLE tTerminal ADD L_Chnl1 INT(10) NULL DEFAULT 0; ALTER TABLE tTerminal ADD L_Chnl2 INT(10) NULL DEFAULT 0; ALTER TABLE tuser ADD L_FaceIdentify INT(10) NULL DEFAULT 0; ALTER TABLE tuser ADD B_DuressFinger LONGBLOB NULL; ALTER TABLE tUser ADD L_AuthValue INT(10) NULL DEFAULT 0; ALTER TABLE tUser ADD L_RegServer INT(10) NULL DEFAULT 0; ALTER TABLE tUser ADD C_RemotePW VARCHAR(255) NULL; ALTER TABLE tUser ADD L_WrongCount INT(10) NULL DEFAULT 0; ALTER TABLE tUser ADD L_LogonLocked INT(10) NULL DEFAULT 0; ALTER TABLE tUser ADD C_LogonDateTime VARCHAR(255) NULL; ALTER TABLE tUser ADD C_UdatePassword VARCHAR(255) NULL; ALTER TABLE tUser ADD C_MustChgPwd VARCHAR(255) NULL; ALTER TABLE tUser ADD B_DuressFinger LONGBLOB NULL; CREATE TABLE UNIS.wTempWorkResult (C_WorkDate VARCHAR(255) NULL, L_UID INT(10) NULL DEFAULT 0, L_AccessTime INT(10) NULL DEFAULT 0, L_Mode INT(10) NULL, UNIQUE INDEX PK_wTempWorkResult (C_WorkDate, L_UID, L_Mode) ); ALTER TABLE wWorkConfig ADD C_NeisSavePath VARCHAR(255) NULL; ALTER TABLE wWorkConfig ADD L_NeisUsed INT(10) NULL DEFAULT 0;  ";
    $query_array = explode(";", $query);
    for ($i = 0; $i < count($query_array); $i++) {
        mysqli_query($conn, $query_array[$i]);
    }
    V3547113190($conn, $conn);
}

function V3547111188($conn) {
    $query = "ALTER TABLE access.OtherSettingMaster ADD EmployeeEmailField VARCHAR (5) NULL, ADD EmployeeSMSField VARCHAR (5) NULL, ADD SMTPPort VARCHAR (5) NULL, ADD SMTPSSL VARCHAR (5) NULL";
    safeUpdate($conn, $query, true);
    $query = "CREATE TABLE  access.TaskMaster (TaskID INT NULL AUTO_INCREMENT , Username VARCHAR( 255 ) NULL , Task VARCHAR( 1024 ) NULL , TDate INT( 8 ) NULL , EmployeeID INT NULL , Schedule VARCHAR( 3 ) NULL , Status INT NULL , Importance VARCHAR( 1 ) NULL , Type VARCHAR( 5 ) NULL , PRIMARY KEY (  TaskID ) )";
    safeUpdate($conn, $query, true);
    V3547112189($conn);
}

function V3446111187($conn) {
    V3547111188($conn);
}

function V3445111186($conn) {
    $query = " CREATE  TABLE  access.tent (  e_date varchar( 8  )  NOT  NULL DEFAULT  '', e_time varchar( 6  )  NOT  NULL DEFAULT  '', g_id int( 10  )  NOT  NULL DEFAULT  '0', e_id int( 10  )  NOT  NULL DEFAULT  '0', e_name varchar( 30  )  DEFAULT NULL , e_idno varchar( 30  )  DEFAULT NULL , e_group smallint( 5  )  NOT  NULL DEFAULT  '0', e_user varchar( 1  )  DEFAULT NULL , e_mode varchar( 1  )  DEFAULT NULL , e_type varchar( 1  )  DEFAULT NULL , e_result varchar( 1  )  DEFAULT NULL , e_etc varchar( 1  )  DEFAULT NULL , ed int( 10  )  NOT  NULL  AUTO_INCREMENT , p_flag int( 10  )  NOT  NULL DEFAULT  '0', e_uptime varchar( 14  )  DEFAULT NULL , e_upmode varchar( 1  )  DEFAULT NULL , PRIMARY  KEY (  e_date ,  e_time ,  g_id ,  e_id  ) , KEY  e_group (  e_group  ) , KEY  ed (  ed  ) , KEY  e_date (  e_date  ) , KEY  e_time (  e_time  ) , KEY  g_id (  g_id  ) , KEY  e_id (  e_id  ) , KEY  p_flag (  p_flag  ) , KEY  e_etc (  e_etc  )  ) ";
    safeUpdate($conn, $query, true);
    V3446111187($conn);
}

function V3444110185($conn) {
    $query = "ALTER TABLE UNIS.cAuthority ADD L_MntMgr INT(10) NULL, ADD L_MntClient INT(10) NULL, ADD L_MntTerminal INT(10) NULL, ADD L_MntAuthLog INT(10) NULL, ADD L_MntEvent INT(10) NULL, ADD L_TmnMgr INT(10) NULL, ADD L_TmnAdd INT(10) NULL, ADD L_TmnMod INT(10) NULL, ADD L_TmnDel INT(10) NULL, ADD L_TmnUpgrade INT(10) NULL, ADD L_TmnOption INT(10) NULL, ADD L_TmnAdmin INT(10) NULL, ADD L_TmnSendFile INT(10) NULL, ADD L_EmpMgr INT(10) NULL, ADD L_EmpAdd INT(10) NULL, ADD L_EmpMod INT(10) NULL, ADD L_EmpDel INT(10) NULL, ADD L_EmpSendTerminal INT(10) NULL, ADD L_EmpTerminalMng INT(10) NULL, ADD L_EmpRegAdmin INT(10) NULL, ADD L_VstMgr INT(10) NULL, ADD L_VstAdd INT(10) NULL, ADD L_VstMod INT(10) NULL, ADD L_VstDel INT(10) NULL, ADD L_BlckMgr INT(10) NULL, ADD L_BlckChange INT(10) NULL, ADD L_BlckRelease INT(10) NULL, ADD L_BlckDel INT(10) NULL, ADD L_BlckMod INT(10) NULL, ADD L_AccMgr INT(10) NULL, ADD L_AccSet INT(10) NULL, ADD L_MapMgr INT(10) NULL, ADD L_MapSet INT(10) NULL, ADD L_TnaMgr INT(10) NULL, ADD L_TnaSet INT(10) NULL, ADD L_TnaSpecial INT(10) NULL, ADD L_TnaWork INT(10) NULL, ADD L_TnaOutState INT(10) NULL, ADD L_TnaOutExcRecord INT(10) NULL, ADD L_TnaSummary INT(10) NULL, ADD L_TnaSendResult INT(10) NULL, ADD L_TnaDelData INT(10) NULL, ADD L_MealMgr INT(10) NULL, ADD L_MealOutRecord INT(10) NULL, ADD L_MealDelData INT(10) NULL, ADD L_MealOutDept INT(10) NULL, ADD L_MealOutPerson INT(10) NULL, ADD L_MealSet INT(10) NULL, ADD L_LogMgr INT(10) NULL, ADD L_LogOutRecord INT(10) NULL, ADD L_LogDelRecord INT(10) NULL, ADD L_SetRegInfo INT(10) NULL, ADD L_SetMgr INT(10) NULL, ADD L_SetServer INT(10) NULL, ADD L_SetPwd INT(10) NULL, ADD L_SetMail INT(10) NULL, ADD L_SetEtc INT(10) NULL ";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE UNIS.cTimezone ADD L_AuthValue INT(10) NULL";
    safeUpdate($conn, $query, true);
    $query = "CREATE TABLE UNIS.cStaff1 (   C_Code VARCHAR(30) NULL,   C_Name VARCHAR(30) NULL )";
    safeUpdate($conn, $query, true);
    $query = "CREATE TABLE UNIS.cPost1 (   C_Code VARCHAR(30) NULL,   C_Name VARCHAR(30) NULL )";
    safeUpdate($conn, $query, true);
    V3445111186($conn);
}

function V3443110184($conn) {
    $query = "INSERT INTO Access.ScheduleMaster (ScheduleID, Name) VALUES (6, 'Flexi Start-End, Multi In-Out (>1)'), (7, 'IN-OUT Terminal, No Break')";
    safeUpdate($conn, $query, true);
    V3444110185($conn);
}

function V3443110183($conn) {
    $query = "DELETE FROM Access.ScheduleMaster WHERE ScheduleID > 5";
    safeUpdate($conn, $query, true);
    V3443110184($conn);
}

function V3443110182($conn) { 
    $query = "ALTER TABLE access.tuser MODIFY `group_id` INT(20) NULL DEFAULT '0'";
    safeUpdate($conn, $query, true);
    V3443110183($conn);
}

//function V3442110181($conn) {
//    global $mac_in_version;
//    $query = "UPDATE UserMaster SET Userpass = '" . encryptString("sholay", $conn) . "' WHERE Username = 'virdi'";
//    safeUpdate($conn, $query, true);
//    $query = "UPDATE OtherSettingMaster SET EX2 = 'V.34.42.110.181', MACAddress = '" . encryptDecrypt(encrypt_Decrypt($mac_in_version)) . "'";
//    safeUpdate($conn, $query, true);
//    $this_conn = mysql_connection("localhost", "Access", "root", "root");
//    if ($this_conn != "") {
//        $query = "SET PASSWORD FOR 'root'@'localhost' = PASSWORD( 'namaste' )";
//        safeUpdate($this_conn, $query, true);
//        $query = "SET PASSWORD FOR 'root'@'%' = PASSWORD( 'namaste' )";
//        safeUpdate($this_conn, $query, true);
//        $query = "SET PASSWORD FOR 'shoot'@'localhost' = PASSWORD( 'salaam' )";
//        safeUpdate($this_conn, $query, true);
//        $query = "SET PASSWORD FOR 'shoot'@'%' = PASSWORD( 'salaam' )";
//        safeUpdate($this_conn, $query, true);
//    }
//    exec("php Register.php");
//    V3443110182($conn);
//}

//function V3341110180($conn) {
//    $query = "ALTER TABLE tuser ADD F1 VARCHAR (255) NULL, ADD F2 VARCHAR (255) NULL, ADD F3 VARCHAR (255) NULL, ADD F4 VARCHAR (255) NULL, ADD F5 VARCHAR (255) NULL, ADD F6 VARCHAR (255) NULL, ADD F7 VARCHAR (255) NULL, ADD F8 VARCHAR (255) NULL, ADD F9 VARCHAR (255) NULL, ADD F10 VARCHAR (255) NULL";
//    safeUpdate($conn, $query, true);
//    $query = "ALTER TABLE OtherSettingMaster ADD F1 VARCHAR (255) NULL, ADD F2 VARCHAR (255) NULL, ADD F3 VARCHAR (255) NULL, ADD F4 VARCHAR (255) NULL, ADD F5 VARCHAR (255) NULL, ADD F6 VARCHAR (255) NULL, ADD F7 VARCHAR (255) NULL, ADD F8 VARCHAR (255) NULL, ADD F9 VARCHAR (255) NULL, ADD F10 VARCHAR (255) NULL";
//    safeUpdate($conn, $query, true);
//    $query = "ALTER TABLE Access.tenter MODIFY ed ed int( 10 ) NOT NULL AUTO_INCREMENT";
//    safeUpdate($conn, $query, true);
//    V3442110181($conn);
//}

function V3341110179($conn) { 
    $query = "ALTER TABLE Access.tuser ADD F1 VARCHAR (255) NULL, ADD F2 VARCHAR (255) NULL, ADD F3 VARCHAR (255) NULL, ADD F4 VARCHAR (255) NULL, ADD F5 VARCHAR (255) NULL, ADD F6 VARCHAR (255) NULL, ADD F7 VARCHAR (255) NULL, ADD F8 VARCHAR (255) NULL, ADD F9 VARCHAR (255) NULL, ADD F10 VARCHAR (255) NULL";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE Access.OtherSettingMaster ADD F1 VARCHAR (255) NULL, ADD F2 VARCHAR (255) NULL, ADD F3 VARCHAR (255) NULL, ADD F4 VARCHAR (255) NULL, ADD F5 VARCHAR (255) NULL, ADD F6 VARCHAR (255) NULL, ADD F7 VARCHAR (255) NULL, ADD F8 VARCHAR (255) NULL, ADD F9 VARCHAR (255) NULL, ADD F10 VARCHAR (255) NULL";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE Access.tenter MODIFY `ed` int( 10 ) NOT NULL AUTO_INCREMENT";
    safeUpdate($conn, $query, true);
    V3443110182($conn);
}

function V3340109178($conn) {
    V3341110179($conn);
}

function V3340108177($conn) {
    $query = "CREATE TABLE IF NOT EXISTS access.MigrateMaster (
                ID INT NOT NULL AUTO_INCREMENT,
                Col VARCHAR(255) NULL,
                Val VARCHAR(255) NULL,
                DateFrom INT(8) NULL,
                DateTo INT(8) NULL,
                PRIMARY KEY (ID),
                UNIQUE KEY CV (Col, Val)
            )";
    safeUpdate($conn, $query, true);
    $t_user = array();
    $t_user = ["idno", "dept", "company", "phone", "remark"];
    for ($i = 0; $i < count($t_user); $i++) { 
        $query = "SELECT DISTINCT(" . $t_user[$i] . ") FROM access.tuser";
//        $result = safeUpdateIData($iconn, $query, true);
//        $result = safeUpdate($conn, $query, true);
        $result = mysqli_query($conn, $query);
        while ($cur = mysqli_fetch_row($result)) {
            $query = "INSERT INTO access.MigrateMaster (Col, Val) VALUES ('" . $t_user[$i] . "', '" . $cur[0] . "')";
            safeUpdate($conn, $query, true);
        }
    }
    $query = "UPDATE access.UserMaster SET UserMail = 'a@b.com' WHERE UserMail LIKE '%datacom%' OR UserMail LIKE '%compusoft%' ";
    safeUpdate($conn, $query, true);
    V3340109178($conn);
}

function V3239107176($conn) {
    $query = "ALTER TABLE  access.payrollmap MODIFY `DataCOMPayroll` VARCHAR( 255 ) NOT NULL DEFAULT  'No' ";
    safeUpdate($conn, $query, true);
    V3340108177($conn);
}

function V3239107175($conn) {
    $query = "ALTER TABLE AccessArchive.archive_am MODIFY `Remark` VARCHAR( 1024 ) DEFAULT NULL";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE AccessArchive.archive_trans MODIFY `Transactquery` VARCHAR( 1024 ) DEFAULT NULL";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE AccessArchive.FlagDayRotation DROP OTH";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE AccessArchive.FlagDayRotation ADD OTH INT NULL DEFAULT '0'";
    safeUpdate($conn, $query, true);
    V3239107176($conn);
}

function V3239107174($conn) {
    $query = "ALTER TABLE AccessArchive.archive_am ADD EarlyIn_flag INT( 1 ) NULL DEFAULT '0' ";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE AccessArchive.FlagDayRotation ADD OTH INT NULL DEFAULT '0'";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE AccessArchive.FlagDayRotation ADD OT VARCHAR(5) DEFAULT NULL";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE AccessArchive.archive_am MODIFY `Remark` VARCHAR( 1024 ) DEFAULT NULL";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE AccessArchive.archive_trans MODIFY `Transactquery` VARCHAR( 1024 ) DEFAULT NULL";
    safeUpdate($conn, $query, true);
    $query = "UPDATE FlagDayRotation SET RecStat = 0 WHERE LENGTH(Flag) < 3 AND e_date > " . insertToday();
    safeUpdate($conn, $query, true);
    V3239107175($conn);
}

function V3239106173($conn) {
    $query = "ALTER TABLE access.OtherSettingMaster ADD SanSatOT VARCHAR(5) NULL DEFAULT 'No' ";
    safeUpdate($conn, $query, true);
    V3239107174($conn);
}

function V3238105172($conn) {
    $query = "ALTER TABLE access.tgroup ADD DNT VARCHAR(5) NULL DEFAULT 'No' ";
    safeUpdate($conn, $query, true);
    V3239106173($conn);
}

function V3237104171($conn) {
    V3238105172($conn);
}

function V3137103170($conn) {
    $query = "CREATE TABLE access.PAX (  id int( 11  )  NOT  NULL DEFAULT '0', N1 varchar( 10  )  DEFAULT NULL , N2 varchar( 10  )  DEFAULT NULL , N3 varchar( 10  )  DEFAULT NULL, N4 varchar( 10  )  DEFAULT NULL, N5 varchar( 10  )  DEFAULT NULL, N6 varchar( 10  )  DEFAULT NULL, N7 varchar( 10  )  DEFAULT NULL, N8 varchar( 10  )  DEFAULT NULL, N9 varchar( 10  )  DEFAULT NULL, N0 varchar( 255 )  DEFAULT NULL, PRIMARY  KEY (id) ) ";
    safeUpdate($conn, $query, true);
    V3237104171($conn);
}

function V3136102169($conn) {
    $query = "ALTER TABLE access.DayMaster ADD INDEX group_id (group_id)";
    safeUpdate($conn, $query, true);
    V3137103170($conn);
}

function V3136102168($conn) {
    $query = "UPDATE access.tgate SET tgate.Exit = 1 WHERE tgate.Meal = 1";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.TLSFlag SET Black = 'Yes', Proxy = 'Yes' ";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.FlagDayRotation SET group_id = 2 WHERE group_id <> 2 AND RecStat = 0 ";
    safeUpdate($conn, $query, true);
    $query = "INSERT INTO access.ScheduleMaster ( ScheduleID, Name ) VALUES (6, 'Flexi Start-End, Multi In-Out (>1)')";
    safeUpdate($conn, $query, true);
    $query = "INSERT INTO access.ScheduleMaster ( ScheduleID, Name) VALUES (7, 'IN/OUT Terminal, No Break')";
    safeUpdate($conn, $query, true);
    V3136102169($conn);
}

function V3136102167($conn) {
    $query = "CREATE TABLE access.GroupShift (GroupShiftID INT NOT NULL AUTO_INCREMENT , GroupID INT NOT NULL DEFAULT '0', Shift VARCHAR( 255 ) NOT NULL DEFAULT '.', PRIMARY KEY ( GroupShiftID ) )  ";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.OtherSettingMaster ADD ClientLogo VARCHAR( 255 ) NULL ";
    safeUpdate($conn, $query, true);
    V3136102168($conn);
}

function V3135101166($conn) {
    V3136102167($conn);
}

function V3135100165($conn) {
    $query = "CREATE TABLE access.SRA (ID INT NOT NULL AUTO_INCREMENT , e_id INT NOT NULL DEFAULT '0', e_group INT NOT NULL DEFAULT '0', gFrom DATETIME NULL , gTo DATETIME NULL , PRIMARY KEY ( ID ) , UNIQUE  KEY EEGG (e_id,  e_group, gFrom, gTo) ) ";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.UserMaster SET UserStatus = '5' ";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.UserMaster MODIFY `UserStatus` INT NOT NULL DEFAULT '5' ";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.tuser ADD UserStatus INT NOT NULL DEFAULT '10' ";
    safeUpdate($conn, $query, true);
    unlink("RotateShift1.php");
    unlink("RotateShift2.php");
    unlink("RotateShift3.php");
    unlink("RotateShift4.php");
    unlink("RotateShift5.php");
    unlink("RotateShift6.php");
    unlink("RotateShift7.php");
    unlink("RotateShift8.php");
    unlink("RotateShift9.php");
    unlink("RotateShift10.php");
    V3135101166($conn);
}

function V3134100164($conn) {
    $query = "ALTER TABLE access.OtherSettingMaster ADD AutoResetOT12 VARCHAR( 3 ) NULL DEFAULT 'No' ";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.UserMaster SET UserStatus = '5' ";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.UserMaster MODIFY `UserStatus` INT NOT NULL DEFAULT '5' ";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.tuser ADD UserStatus INT NOT NULL DEFAULT '10' ";
    safeUpdate($conn, $query, true);
    V3135100165($conn);
}

function V313399163($conn) { 
    $query = "ALTER TABLE access.FlagDayRotation ADD OTH INT NULL DEFAULT '0'";
    safeUpdate($conn, $query, true);
    $query = "CREATE TABLE access.SanitationDate ( OTDateID int( 11 ) NOT NULL AUTO_INCREMENT , OTDate int( 8 ) NOT NULL DEFAULT '0', PRIMARY KEY ( OTDateID ) )";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.AttendanceMaster ADD EarlyIn_flag INT( 1 ) NULL DEFAULT '0' ";
    safeUpdate($conn, $query, true);
    V3134100164($conn);
}

function V313298162($conn) {
    V313399163($conn);
}

function V313298161($conn) {
    $query = "UPDATE access.OtherSettingMaster SET AutoAssignTerminal = 'Yes', EX2 = 'V.31.32.98.161'";
    safeUpdate($conn, $query, true);
    V313298162($conn);
}

function V313298160($conn) {
    V313298161($conn);
}

function V313198159($conn) {
    $query = "ALTER TABLE access.tgroup ADD ExemptLI VARCHAR(10) NULL DEFAULT 'NONE' ";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.tgroup ADD ExemptOT VARCHAR(10) NULL DEFAULT 'NONE' ";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.tgroup ADD ExemptOT1 VARCHAR(3) NOT NULL DEFAULT 'No'";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE Access.tgroup  
                ADD ExemptOT2 VARCHAR(3) NOT NULL DEFAULT 'No', 
                ADD ExemptOTDate VARCHAR(3) NOT NULL DEFAULT 'No'";
    safeUpdate($conn, $query, true);
    $query = "SELECT id, ExemptOT1, ExemptOT2, ExemptOTDate FROM access.tgroup WHERE id > 2";
//    $result = safeUpdateIData($iconn, $query, true);
    $result = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
        if ($cur[1] == "Yes") {
            if ($cur[2] == "Yes") {
                if ($cur[3] == "Yes") {
                    $query = "UPDATE access.tgroup SET ExemptOT = 'ALL OT' WHERE id = " . $cur[0];
                } else {
                    $query = "UPDATE access.tgroup SET ExemptOT = 'OT1/OT2' WHERE id = " . $cur[0];
                }
            } else {
                $query = "UPDATE access.tgroup SET ExemptOT = 'OT1' WHERE id = " . $cur[0];
            }
        } else {
            if ($cur[2] == "Yes") {
                if ($cur[3] == "Yes") {
                    $query = "UPDATE access.tgroup SET ExemptOT = 'OT2/OTD' WHERE id = " . $cur[0];
                } else {
                    $query = "UPDATE access.tgroup SET ExemptOT = 'OT2' WHERE id = " . $cur[0];
                }
            } else {
                if ($cur[3] == "Yes") {
                    $query = "UPDATE access.tgroup SET ExemptOT = 'OTD' WHERE id = " . $cur[0];
                } else {
                    $query = "UPDATE access.tgroup SET ExemptOT = 'NONE' WHERE id = " . $cur[0];
                }
            }
        }
        safeUpdate($conn, $query, true);
    }
    $query = "ALTER TABLE Access.UserMaster ADD RHSSelection VARCHAR( 1024 ) NULL DEFAULT '--IDNo--Dept--Div--Remark', ADD OT1F DECIMAL(5, 2) NULL DEFAULT '1', ADD OT2F DECIMAL(5, 2) NULL DEFAULT '1', ADD OTDF DECIMAL(5, 2) NULL DEFAULT '1' ";
    safeUpdate($conn, $query, true);
    V313298160($conn);
}

function V313097158($conn) {
    $query = "ALTER TABLE access.OtherSettingMaster MODIFY `NoExitException` VARCHAR(50) NULL DEFAULT 'No' ";
    safeUpdate($conn, $query, true);
    V313198159($conn);
}

function V313097157($conn) {
    $query = "UPDATE access.TLSFlag SET Black = 'Yes', Proxy = 'Yes' ";
    safeUpdate($conn, $query, true);
    V313097158($conn);
}

function V313096156($conn) {
    $query = "UPDATE access.TLSFlag SET Black = 'Yes', Proxy = 'Yes' ";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.FlagDayRotation SET group_id = 2 WHERE group_id <> 2 AND RecStat = 0 ";
    safeUpdate($conn, $query, true);
    $query = "INSERT INTO access.ScheduleMaster (Name) VALUES ('IN/OUT Terminal, No Break')";
    safeUpdate($conn, $query, true);
    V313097157($conn);
}

function V313095155($conn) {
    $query = "ALTER TABLE access.OtherSettingMaster MODIFY `FlagLimitType` VARCHAR(255) DEFAULT NULL ";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.OtherSettingMaster SET FlagLimitType = 'Jan 01' ";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.GroupFlagLimit ADD Magenta INT( 5 ) NULL DEFAULT '365', ADD Teal INT( 5 ) NULL DEFAULT '365', ADD Aqua INT( 5 ) NULL DEFAULT '365', ADD Safron INT( 5 ) NULL DEFAULT '365', ADD Amber INT( 5 ) NULL DEFAULT '365', ADD Gold INT( 5 ) NULL DEFAULT '365', ADD Vermilion INT( 5 ) NULL DEFAULT '365', ADD Silver INT( 5 ) NULL DEFAULT '365', ADD Maroon INT( 5 ) NULL DEFAULT '365', ADD Pink INT( 5 ) NULL DEFAULT '365' ";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.EmployeeFlag ADD Magenta INT( 5 ) NULL DEFAULT '365', ADD Teal INT( 5 ) NULL DEFAULT '365', ADD Aqua INT( 5 ) NULL DEFAULT '365', ADD Safron INT( 5 ) NULL DEFAULT '365', ADD Amber INT( 5 ) NULL DEFAULT '365', ADD Gold INT( 5 ) NULL DEFAULT '365', ADD Vermilion INT( 5 ) NULL DEFAULT '365', ADD Silver INT( 5 ) NULL DEFAULT '365', ADD Maroon INT( 5 ) NULL DEFAULT '365', ADD Pink INT( 5 ) NULL DEFAULT '365' ";
    safeUpdate($conn, $query, true);
    $query = "CREATE TABLE access.GroupYearFlagLimit (ID INT NOT NULL AUTO_INCREMENT, Grp VARCHAR( 255 ) NULL, Val VARCHAR( 255 ) NULL, Years INT( 3 ) NULL DEFAULT '0', Violet INT( 5 ) NULL DEFAULT '365', Indigo INT( 5 ) NULL DEFAULT '365', Blue INT( 5 ) NULL DEFAULT '365', Green INT( 5 ) NULL DEFAULT '365', Yellow INT( 5 ) NULL DEFAULT '365', Orange INT( 5 ) NULL DEFAULT '365', Red INT( 5 ) NULL DEFAULT '365', Gray INT( 5 ) NULL DEFAULT '365', Brown INT( 5 ) NULL DEFAULT '365', Purple INT( 5 ) NULL DEFAULT '365', Magenta INT( 5 ) NULL DEFAULT '365', Teal INT( 5 ) NULL DEFAULT '365', Aqua INT( 5 ) NULL DEFAULT '365', Safron INT( 5 ) NULL DEFAULT '365', Amber INT( 5 ) NULL DEFAULT '365', Gold INT( 5 ) NULL DEFAULT '365', Vermilion INT( 5 ) NULL DEFAULT '365', Silver INT( 5 ) NULL DEFAULT '365', Maroon INT( 5 ) NULL DEFAULT '365', Pink INT( 5 ) NULL DEFAULT '365', PRIMARY KEY ( ID ), UNIQUE  KEY  GV (Grp,  Val, Years)  ) ";
    safeUpdate($conn, $query, true);
    $query = "CREATE TABLE access.FlagApplication (ID int(11) AUTO_INCREMENT, DateFrom int(8) NULL default '0', DateTo int(8) NULL default '0', e_id int(11) NULL default '0', Flag varchar(255) NULL, A1 int(1) NULL default '0' COMMENT 'Add Rights', A2 int(1) NULL default '0' COMMENT 'Edit Rights', A3 int(1) NULL default '0' COMMENT 'Delete Rights', Remark varchar(255) NULL, PRIMARY KEY(ID), UNIQUE KEY EFT ( e_id , DateFrom, DateTo ))";
    safeUpdate($conn, $query, true);
    $query = "INSERT INTO access.MailerText (MailerType , MailerText) VALUES ('FlagApplication', '')";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.TLSFlag SET Black = 'Yes', Proxy = 'Yes' ";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.FlagDayRotation SET group_id = 2 WHERE group_id <> 2 AND RecStat = 0 ";
    safeUpdate($conn, $query, true);
    V313096156($conn);
}

function V302994154($conn) {
    $query = "ALTER TABLE access.FlagTitle ADD FlagLink VARCHAR(255) DEFAULT NULL, MODIFY `Title` VARCHAR(255) DEFAULT NULL";
    safeUpdate($conn, $query, true);
    $query = "INSERT INTO access.FlagTitle (Flag) VALUES ('Magenta'), ('Teal'), ('Aqua'), ('Safron'), ('Amber'), ('Gold'), ('Vermilion'), ('Silver'), ('Maroon'), ('Pink')";
    safeUpdate($conn, $query, true);
    $query = "DELETE FROM access.TLSFLag where TLSFlagID > 1";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.TLSFLag ADD Magenta VARCHAR(5) DEFAULT 'Yes', ADD Teal VARCHAR(5) DEFAULT 'Yes', ADD Aqua VARCHAR(5) DEFAULT 'Yes', ADD Safron VARCHAR(5) DEFAULT 'Yes', ADD Amber VARCHAR(5) DEFAULT 'Yes', ADD Gold VARCHAR(5) DEFAULT 'Yes', ADD Vermilion VARCHAR(5) DEFAULT 'Yes', ADD Silver VARCHAR(5) DEFAULT 'Yes', ADD Maroon VARCHAR(5) DEFAULT 'Yes', ADD Pink VARCHAR(5) DEFAULT 'Yes' ";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.AccessFlag ADD Magenta VARCHAR(5) DEFAULT 'Yes', ADD Teal VARCHAR(5) DEFAULT 'Yes', ADD Aqua VARCHAR(5) DEFAULT 'Yes', ADD Safron VARCHAR(5) DEFAULT 'Yes', ADD Amber VARCHAR(5) DEFAULT 'Yes', ADD Gold VARCHAR(5) DEFAULT 'Yes', ADD Vermilion VARCHAR(5) DEFAULT 'Yes', ADD Silver VARCHAR(5) DEFAULT 'Yes', ADD Maroon VARCHAR(5) DEFAULT 'Yes', ADD Pink VARCHAR(5) DEFAULT 'Yes' ";
    safeUpdate($conn, $query, true);
    V313095155($conn);
}

function V302893153($conn) {
    V302994154($conn);
}

function V302893152($conn) {
    $query = "ALTER TABLE access.ShiftChangeMaster ADD RTime VARCHAR(5) DEFAULT NULL";
    safeUpdate($conn, $query, true);
    $query = "SELECT MAX(RDate) FROM access.ShiftRotateLog";
    $result = selectData($conn, $query);
    $query = "SELECT DISTINCT(ShiftFrom), RTime FROM access.ShiftRotateLog WHERE RDate > " . getLastDay($result[0], 7);
//    $result = safeUpdateIData($iconn, $query, true);
    $result = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
        $query = "UPDATE access.ShiftChangeMaster SET RTime = '" . substr(addZero($cur[1], 4), 0, 2) . "00' WHERE id = " . $cur[0];
        safeUpdate($conn, $query, true);
    }
    $query = "ALTER TABLE access.FlagDayRotation ADD OT VARCHAR(5) DEFAULT NULL";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.FlagDayRotation SET OT = 'OT1' WHERE LENGTH(OT1) > 1";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.FlagDayRotation SET OT = 'OT2' WHERE LENGTH(OT2) > 1";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.FlagDayRotation SET OT1 = '0', OT2 = '0' ";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.FlagDayRotation MODIFY OT1 INT DEFAULT NULL, MODIFY OT2 INT DEFAULT NULL";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.FlagDayRotation SET OT1 = NULL, OT2 = NULL";
    safeUpdate($conn, $query, true);
    exec("php MaintainDB.php");
    V302893153($conn);
}

function V302892151($conn) {
    $query = "SELECT id FROM access.tgroup WHERE name = 'OFF'";
    $result = selectData($conn, $query);
    
    if (!empty($result)) {
        $shift = $result[0]; // Use existing OFF group id
    } else {
        $query = "SELECT MAX(id) FROM access.tgroup WHERE LENGTH(id) = 1";
        $result = selectData($conn, $query);
        $shift = $result[0] * 1 + 1;
        $query = "INSERT INTO access.tgroup (id, name, reg_date, timelimit, Start, GraceTo, FlexiBreak, Close, ShiftTypeID, ScheduleID, WorkMin) VALUES (" . $shift . ", 'OFF', '" . insertToday() . "" . getNow() . "', '00002359', '0800', '0800', 0, '1600', 1, 5, 480) ";
        safeUpdate($conn, $query, true);
    }
    $query = "ALTER TABLE access.FlagDayRotation MODIFY group_id INT(11) DEFAULT " . $shift;
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.FlagDayRotation SET group_id = " . $shift . " WHERE (group_id = 0 OR group_id IS NULL) AND RecStat = 0";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.FlagDayRotation SET group_id = " . $shift . " WHERE RecStat = 0 AND group_id IN (SELECT id FROM tgroup WHERE NightFlag = 1) ";
    safeUpdate($conn, $query, true);
    V302893152($conn);
}

function V302892150($conn) {
    $query = "ALTER TABLE access.tgroup ADD NoBreakExceptionOT VARCHAR( 5 ) NULL DEFAULT 'Yes' ";
    safeUpdate($conn, $query, true);
    V302892151($conn);
}

function V302891149($conn) {
    V302892150($conn);
}

function V302890148($conn) {
    $query = "CREATE TABLE AccessArchive.FlagDayRotation ( FlagDayRotationID int( 11 ) NULL, e_id int( 11 ) NOT NULL DEFAULT '0', e_date int( 8 ) NOT NULL DEFAULT '0', g_id int( 11 ) NOT NULL DEFAULT '0', Flag varchar( 1024 ) NULL DEFAULT 'Black', Rotate int( 1 ) NOT NULL DEFAULT '0', RecStat int( 1 ) NOT NULL DEFAULT '0', Remark varchar( 1024 ) NULL, OT1 varchar( 10 ) NULL, OT2 varchar( 10 ) NULL, group_id int( 11 ) NOT NULL DEFAULT '0', PRIMARY KEY ( FlagDayRotationID ) , UNIQUE KEY FDRED ( e_id , e_date ) ) ";
    safeUpdate($conn, $query, true);
    $query = "CREATE TABLE AccessArchive.ShiftRoster ( ShiftRosterID int( 11 ) NULL, e_id int( 11 ) NOT NULL DEFAULT '0', e_date int( 11 ) NOT NULL DEFAULT '0', e_group int( 11 ) NOT NULL DEFAULT '0', PRIMARY KEY ( ShiftRosterID ) , UNIQUE KEY ShiftRoster ( e_id , e_date , e_group ) ) ";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE AccessArchive.FlagDayRotation 
        MODIFY `Remark` VARCHAR(1024) NULL, 
        MODIFY OT1 VARCHAR(10) NULL, 
        MODIFY OT2 VARCHAR(10) NULL, 
        MODIFY Flag VARCHAR(10) NULL";
    safeUpdate($conn, $query, true);

    $query = "ALTER TABLE archive_am 
        MODIFY Flag VARCHAR(10) NULL DEFAULT 'Black', 
        MODIFY OT1 VARCHAR(255) NULL DEFAULT 'Saturday', 
        MODIFY OT2 VARCHAR(255) NULL DEFAULT 'Sunday'";
    safeUpdate($conn, $query, true);

    $query = "ALTER TABLE archive_dm 
        MODIFY Flag VARCHAR(10) NULL DEFAULT 'Black'";
    safeUpdate($conn, $query, true);

    $query = "ALTER TABLE archive_trans 
        MODIFY Username VARCHAR(255) NULL DEFAULT NULL, 
        MODIFY Transactquery VARCHAR(1024) NULL";
    safeUpdate($conn, $query, true);
    V302891149($conn);
}

function V302890147($conn) {
    $query = "ALTER TABLE AccessArchive.archive_am MODIFY `Remark` VARCHAR( 1024 ) NULL , ADD PHF INT ( 1 ) NOT NULL DEFAULT 0 ";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE Access.AttendanceMaster MODIFY `Remark` VARCHAR( 1024 ) NULL ";
    safeUpdate($conn, $query, true);
    V302890148($conn);
}

function V302790146($conn) {
    $query = "SELECT id from access.tgroup WHERE NightFlag = 0 AND id > 1";
    $result = selectData($conn, $query);
    $query = "UPDATE access.FlagDayRotation SET group_id = '" . $result[0] . "' WHERE RecStat = 0 AND group_id = 0";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.OtherSettingMaster ADD LateDays INT( 2 ) NOT NULL DEFAULT '0' ";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.tuser SET OT1 = '', OT2 = '' WHERE OT1 = 'Saturday' AND OT2 = 'Sunday' AND id IN (SELECT EmployeeID FROM OTEmployeeExempt) ";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.tgroup ADD ProxyOT VARCHAR( 5 ) NULL DEFAULT 'Yes' ";
    safeUpdate($conn, $query, true);
    V302890147($conn);
}

function V292689145($conn) {
    $query = "SELECT id from access.tgroup WHERE NightFlag = 0 AND id > 1";
    $result = selectData($conn, $query);
    $query = "UPDATE access.FlagDayRotation SET group_id = '" . $result[0] . "' WHERE RecStat = 0 AND group_id = 0";
    safeUpdate($conn, $query, true);
    $query = "DELETE FROM access.FlagDayRotation WHERE e_date > '" . insertToday() . "' AND RecStat = 0 AND e_id IN (SELECT id FROM tuser WHERE PassiveType = 'RSN' OR PassiveType = 'PRM' OR PassiveType = 'RTD' OR PassiveType = 'TRM' OR PassiveType = 'DSD') ";
    safeUpdate($conn, $query, true);
    $query = "CREATE TABLE AccessArchive.FlagDayRotation ( FlagDayRotationID int( 11 ) NOT NULL AUTO_INCREMENT , e_id int( 11 ) NOT NULL DEFAULT '0', e_date int( 8 ) NOT NULL DEFAULT '0', g_id int( 11 ) NOT NULL DEFAULT '0', Flag varchar( 1024 ) NOT NULL DEFAULT 'Black', Rotate int( 1 ) NOT NULL DEFAULT '0', RecStat int( 1 ) NOT NULL DEFAULT '0', Remark varchar( 1024 ) NOT NULL DEFAULT '.', OT1 varchar( 10 ) NOT NULL DEFAULT '', OT2 varchar( 10 ) NOT NULL DEFAULT '', group_id int( 11 ) NOT NULL DEFAULT '0', PRIMARY KEY ( FlagDayRotationID ) , UNIQUE KEY FDRED ( e_id , e_date ) ) ";
    safeUpdate($conn, $query, true);
    $query = "CREATE TABLE AccessArchive.ShiftRoster ( ShiftRosterID int( 11 ) NOT NULL AUTO_INCREMENT , e_id int( 11 ) NOT NULL DEFAULT '0', e_date int( 11 ) NOT NULL DEFAULT '0', e_group int( 11 ) NOT NULL DEFAULT '0', PRIMARY KEY ( ShiftRosterID ) , UNIQUE KEY ShiftRoster ( e_id , e_date , e_group ) ) ";
    safeUpdate($conn, $query, true);
    V302790146($conn);
}

function V292688144($conn) {
    $query = "DELETE FROM access.FlagDayRotation WHERE e_date > '" . insertToday() . "' AND RecStat = 0 AND e_id IN (SELECT id FROM tuser WHERE PassiveType = 'RSN' OR PassiveType = 'PRM' OR PassiveType = 'RTD' OR PassiveType = 'TRM' OR PassiveType = 'DSD') ";
    safeUpdate($conn, $query, true);
    V292689145($conn);
}

function V292688143($conn) {
    $query = "ALTER TABLE access.FlagDayRotation ADD group_id INT NOT NULL DEFAULT '0' ";
    safeUpdate($conn, $query, true);
    V292688144($conn);
}

function V292687142($conn) {
    $query = "ALTER TABLE access.AttendanceMaster MODIFY `Remark` VARCHAR( 1024 ) NOT NULL DEFAULT '' ";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.AttendanceMaster SET Remark = '' WHERE Remark = '.' ";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.tgroup ADD ExemptOT1 VARCHAR( 3 ) NOT NULL DEFAULT 'No', ADD ExemptOT2 VARCHAR( 3 ) NOT NULL DEFAULT 'No', ADD ExemptOTDate VARCHAR( 3 ) NOT NULL DEFAULT 'No' ";
    safeUpdate($conn, $query, true);
    V292688143($conn);
}

function V292686141($conn) {
    $query = "UPDATE access.tgroup SET StartHour = 0, CloseHour = 0";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.tgroup MODIFY `StartHour` VARCHAR( 4 ) NOT NULL DEFAULT '0000' ";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.tgroup MODIFY `CloseHour` VARCHAR( 4 ) NOT NULL DEFAULT '2359' ";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.tgroup SET StartHour = '0000' ";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.tgroup SET CloseHour = '2359' ";
    safeUpdate($conn, $query, true);
    $query = "CREATE TABLE access.ADALog ( LogID INT NOT NULL AUTO_INCREMENT , e_id INT NULL , DateFrom INT( 8 ) NULL , DateTo INT( 8 ) NULL , PRIMARY KEY ( LogID ) ) ";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.ADALog ADD UNIQUE EDF ( e_id , DateFrom )  ";
    safeUpdate($conn, $query, true);
    V292687142($conn);
}

function V292685140($conn) {
    $query = "UPDATE access.AttendanceMaster SET Remark = '' WHERE Remark LIKE '%AOT Round OFF%' ";
    safeUpdate($conn, $query, true);
    V292686141($conn);
}

function V292685139($conn) {
    $query = "ALTER TABLE access.tgroup ADD NSOTCO INT( 4 ) NOT NULL DEFAULT 0";
    safeUpdate($conn, $query, true);
    V292685140($conn);
}

function V292584138($conn) {
    $query = "ALTER TABLE access.shiftchangemaster ADD SRDay VARCHAR( 10 ) NOT NULL DEFAULT 'None', ADD SRScenario VARCHAR( 255 ) NOT NULL DEFAULT 'None', ADD RotateShiftNextDay INT( 8 ) NOT NULL DEFAULT '0' ";
    safeUpdate($conn, $query, true);
    $query = " UPDATE access.shiftchangemaster SET RotateShiftNextDay = (SELECT RotateShiftNextDay FROM OtherSettingMaster)";
    safeUpdate($conn, $query, true);
    unlink("ShiftRotation2.php");
    unlink("ShiftRotation3.php");
    unlink("ShiftRotation4.php");
    unlink("ShiftRotation5.php");
    unlink("ShiftRotation6.php");
    unlink("ShiftRotation7.php");
    unlink("ShiftRotation8.php");
    unlink("ShiftRotation9.php");
    unlink("ShiftRotation10.php");
    V292685139($conn);
}

function V292483137($conn) {
    $query = "ALTER TABLE access.tgroup ADD OT1RF DECIMAL(5, 2) NOT NULL DEFAULT '0' ";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.tgroup MODIFY `StartHour` DECIMAL( 5, 2 ) NOT NULL DEFAULT '0', MODIFY `CloseHour` DECIMAL( 5, 2 ) NOT NULL DEFAULT '0' ";
    safeUpdate($conn, $query, true);
    V292584138($conn);
}

function V292482136($conn) {
    $query = "ALTER TABLE access.FlagDayRotation ADD OT1 VARCHAR(10) NOT NULL DEFAULT '', ADD OT2 VARCHAR(10) NOT NULL DEFAULT ''";
    safeUpdate($conn, $query, true);
    V292483137($conn);
}

function V292481135($conn) {
    $query = "ALTER TABLE access.tgroup ADD MoveNS VARCHAR(5) NOT NULL DEFAULT 'No'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.tgroup, access.OtherSettingMaster SET tgroup.MoveNS = OtherSettingMaster.MoveNS WHERE tgroup.NightFlag = 1";
    safeUpdate($conn, $query, true);
    V292482136($conn);
}

function V282380134($conn) {
    $query = "ALTER TABLE AccessArchive.archive_am ADD PHF INT( 1 ) NOT NULL DEFAULT '0' ";
    safeUpdate($conn, $query, true);
    $query = "UPDATE AccessArchive.archive_am SET AccessArchive.archive_am.PHF = 1 WHERE AccessArchive.archive_am.ADate IN (SELECT Access.OTDate.OTDate FROM Access.OTDate)";
    safeUpdate($conn, $query, true);
    V292481135($conn);
}

function V282380133($conn) {
    $query = "ALTER TABLE access.OtherSettingMaster ADD MealCouponPrinterName VARCHAR( 255 ) NOT NULL DEFAULT '.' ";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.OtherSettingMaster ADD MealCouponFont VARCHAR( 5 ) NOT NULL DEFAULT '80' ";
    safeUpdate($conn, $query, true);
    $query = "CREATE TABLE access.MealMaster (MealMasterID INT NOT NULL AUTO_INCREMENT, MealSlot VARCHAR( 255 ) NOT NULL, MealTimeFrom VARCHAR( 6 ) NOT NULL, MealTimeTo VARCHAR( 6 ) NOT NULL, PRIMARY KEY ( MealMasterID ) )";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.tgate ADD Meal INT( 1 ) NOT NULL DEFAULT '0'";
    safeUpdate($conn, $query, true);
    V282380134($conn);
}

function V272280132($conn) {
    V282380133($conn);
}

function V272280131($conn) {
    $query = "ALTER TABLE access.tgroup ADD onetouch VARCHAR( 25 ) NULL DEFAULT NULL, ADD autosync VARCHAR( 1 ) NULL DEFAULT NULL";
    safeUpdate($conn, $query, true);
    V272280132($conn);
}

function V272280130($conn) {
    V272280131($conn);
}

function V272280129($conn) {
    $query = "DROP TABLE archive_am";
    safeUpdate($conn, $query, true);
    $query = "DROP TABLE archive_dm";
    safeUpdate($conn, $query, true);
    $query = "DROP TABLE archive_tenter";
    safeUpdate($conn, $query, true);
    $query = "DROP TABLE attendancemaster_delete";
    safeUpdate($conn, $query, true);
    $query = "DROP TABLE tenter-20090831";
    safeUpdate($conn, $query, true);
    $query = "DROP TABLE tenter_copy";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.OtherSettingMaster ADD PreApproveOTValue VARCHAR( 25 ) NOT NULL DEFAULT 'Lower Value'";
    safeUpdate($conn, $query, true);
    $query = "CREATE TABLE access.DrillMaster (DrillMasterID INT NOT NULL AUTO_INCREMENT ,DrillDate INT( 8 ) NOT NULL ,DrillTimeFrom VARCHAR( 6 ) NOT NULL ,DrillTimeTo VARCHAR( 6 ) NOT NULL ,PRIMARY KEY ( DrillMasterID ) )";
    safeUpdate($conn, $query, true);
    $query = "CREATE TABLE access.DrillTerminal (DrillID INT NOT NULL AUTO_INCREMENT, DrillMasterID INT NOT NULL, g_id INT NOT NULL, PRIMARY KEY ( DrillID ) )";
    safeUpdate($conn, $query, true);
    $query = "CREATE TABLE access.DrillDept (DrillID INT NOT NULL AUTO_INCREMENT, DrillMasterID INT NOT NULL, Dept VARCHAR( 255 ) NOT NULL, PRIMARY KEY ( DrillID ) ) ";
    safeUpdate($conn, $query, true);
    $query = "CREATE TABLE access.DrillDiv (
                DrillID INT NOT NULL AUTO_INCREMENT,
                DrillMasterID INT NOT NULL,
                `Div` VARCHAR(255) NOT NULL,
                PRIMARY KEY (DrillID)
            )";
    safeUpdate($conn, $query, true);
    $query = "CREATE TABLE access.DrillIdNo (DrillID INT NOT NULL AUTO_INCREMENT, DrillMasterID INT NOT NULL, IDNo VARCHAR( 255 ) NOT NULL, PRIMARY KEY ( DrillID ) )";
    safeUpdate($conn, $query, true);
    $query = "CREATE TABLE access.DrillRemark (DrillID INT NOT NULL AUTO_INCREMENT, DrillMasterID INT NOT NULL, Remark VARCHAR( 255 ) NOT NULL, PRIMARY KEY ( DrillID ) )";
    safeUpdate($conn, $query, true);
    $query = "CREATE TABLE access.DrillPhone (DrillID INT NOT NULL AUTO_INCREMENT, DrillMasterID INT NOT NULL, Phone VARCHAR( 255 ) NOT NULL, PRIMARY KEY ( DrillID ) ) ";
    safeUpdate($conn, $query, true);
    V272280130($conn);
}

function V262179128($conn) {
    $query = "CREATE TABLE access.MailerText (MailerTextID INT NOT NULL AUTO_INCREMENT , MailerType VARCHAR( 255 ) NOT NULL , MailerText VARCHAR( 1024 ) NOT NULL , PRIMARY KEY ( MailerTextID ) )";
    safeUpdate($conn, $query, true);
    $query = "INSERT INTO access.MailerText (MailerTextID , MailerType , MailerText) VALUES (NULL , 'Attendance', ''), (NULL , 'Absence', ''), (NULL , 'OddLog', ''), (NULL , 'LateArrival', ''), (NULL , 'EarlyExit', '')";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.tgroup ADD MaxOTValueOT1 INT( 5 ) NOT NULL DEFAULT 1440, ADD MaxOTValueOT2 INT(5) NOT NULL DEFAULT 1440";
    safeUpdate($conn, $query, true);
    $query = "SELECT PLFlag FROM access.OtherSettingMaster";
    $result = selectData($conn, $query);
    if ($result[0] != "Purple") {
        $query = "Select FlagTitle.Title FROM access.FlagTitle WHERE Flag = 'Purple'";
        $result = selectData($conn, $query);
        if ($result[0] == "") {
            $query = "UPDATE access.OtherSettingMaster SET PLFlag = 'Purple'";
            safeUpdate($conn, $query, true);
            $query = "UPDATE access.FlagTitle SET FlagTitle.Title = 'PH' WHERE Flag = 'Purple'";
            safeUpdate($conn, $query, true);
        }
    }
    V272280129($conn);
}

function V262078127($conn) {
    $query = "ALTER TABLE Access.tgroup ADD ASLate INT( 3 ) NOT NULL DEFAULT '0' COMMENT 'Option to Enter the Number of Normal Days of Lateness after which Employee should be Automatically Suspended', ADD ASAbsent INT( 3 ) NOT NULL DEFAULT '0' COMMENT 'Option to Enter the Number of Normal Days of Absence after which Employee should be Automatically Suspended'";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE Access.tuser ADD PassiveType VARCHAR( 5 ) NOT NULL DEFAULT 'ACT', ADD PassiveRemark VARCHAR( 255 ) NOT NULL DEFAULT '.'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE Access.tuser SET PassiveType = 'ACT' WHERE datelimit LIKE 'N%'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE Access.tuser SET PassiveType = 'RSN' WHERE datelimit LIKE 'Y%' AND SUBSTRING(tuser.datelimit, 10, 8) < '" . insertToday() . "' AND SUBSTRING(tuser.datelimit, 10, 8) NOT LIKE '19770430' ";
    safeUpdate($conn, $query, true);
    $query = "UPDATE Access.tuser SET PassiveType = 'FDA' WHERE datelimit LIKE 'Y%' AND SUBSTRING(tuser.datelimit, 10, 8) < '" . insertToday() . "' AND SUBSTRING(tuser.datelimit, 10, 8) = '19770430' ";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE Access.tconfig ADD latetime VARCHAR(4) NULL DEFAULT NULL, ADD lfdlevel VARCHAR(1) NULL DEFAULT NULL ";
    safeUpdate($conn, $query, true);
    V262179128($conn);
}

function V262077126($conn) {
    $query = "ALTER TABLE access.tuser ADD flagdatelimit VARCHAR( 17 ) NOT NULL DEFAULT 'N2001010120010101' COMMENT 'Store Actual AccessLimit while Flagging Employee for NOT clocking Temporarily'";
    safeUpdate($conn, $query, true);
    V262078127($conn);
}

function V262077125($conn) {
    V262077126($conn);
}

function V262077124($conn) {
    $query = "ALTER TABLE access.OtherSettingMaster ADD SRDay VARCHAR( 15 ) NOT NULL DEFAULT 'None', ADD SRScenario VARCHAR( 255 ) NOT NULL DEFAULT 'None'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.OtherSettingMaster SET SRDay = 'None', SRScenario = 'None'";
    $mac = encryptDecrypt($result[1]);
    if ($mac == "00-16-EC-A4-8B-B6" || $mac == "00-1E-90-DB-2C-6F" || $mac == "00-16-EC-9E-E0-D1") {
        $query = "UPDATE access.OtherSettingMaster SET SRDay = 'Sunday', SRScenario = 'Morning - 2 Shifts (No Day Shift on Rotation Day)'";
    } else {
        if ($mac == "00-0F-1F-68-8E-A2" || $mac == "40-61-86-0E-D5-07" || $mac == "40-61-86-0F-28-EB" || $mac == "40-61-86-0F-29-18") {
            $query = "UPDATE access.OtherSettingMaster SET SRDay = 'Sunday', SRScenario = 'Morning - 3 Shifts'";
        } else {
            if ($mac == "00-1C-25-26-FF-6E") {
                $query = "UPDATE access.OtherSettingMaster SET SRDay = 'Sunday', SRScenario = 'Morning - 2 (No Day Shift on Rotation Day) And 3 Shifts'";
            }
        }
    }
    safeUpdate($conn, $query, true);
    V262077125($conn);
}

function V262076123($conn) { 
    $query = "CREATE TABLE access.UserDivLockDate (ID INT NOT NULL AUTO_INCREMENT , UserDivLockDate.Div VARCHAR( 255 ) NOT NULL DEFAULT '.', Date INT(8) NOT NULL DEFAULT '0', PRIMARY KEY ( ID ) ) ";
    safeUpdate($conn, $query, true);
    $query = "CREATE TABLE access.GroupMaster (GroupID INT NOT NULL AUTO_INCREMENT , Name VARCHAR( 255 ) NOT NULL DEFAULT '.', PRIMARY KEY ( GroupID ) ) ";
    safeUpdate($conn, $query, true);
    $query = "CREATE TABLE access.GroupDiv (GroupDivID INT NOT NULL AUTO_INCREMENT , GroupID INT NOT NULL DEFAULT '0', GroupDiv.Div VARCHAR( 255 ) NOT NULL DEFAULT '.', PRIMARY KEY ( GroupDivID ) )  ";
    safeUpdate($conn, $query, true);
    $query = "CREATE TABLE access.GroupDept (GroupDeptID INT NOT NULL AUTO_INCREMENT , GroupID INT NOT NULL DEFAULT '0', Dept VARCHAR( 255 ) NOT NULL DEFAULT '.', PRIMARY KEY ( GroupDeptID ) )  ";
    safeUpdate($conn, $query, true);
    $query = "CREATE TABLE access.GroupRemark (GroupRemarkID INT NOT NULL AUTO_INCREMENT , GroupID INT NOT NULL DEFAULT '0', Remark VARCHAR( 255 ) NOT NULL DEFAULT '.', PRIMARY KEY ( GroupRemarkID ) )  ";
    safeUpdate($conn, $query, true);
    $query = "CREATE TABLE access.GroupPhone (GroupPhoneID INT NOT NULL AUTO_INCREMENT , GroupID INT NOT NULL DEFAULT '0', Phone VARCHAR( 255 ) NOT NULL DEFAULT '.', PRIMARY KEY ( GroupPhoneID ) )  ";
    safeUpdate($conn, $query, true);
    $query = "CREATE TABLE access.GroupIdNo (GroupIdNoID INT NOT NULL AUTO_INCREMENT , GroupID INT NOT NULL DEFAULT '0', IdNo VARCHAR( 255 ) NOT NULL DEFAULT '.', PRIMARY KEY ( GroupIdNoID ) )  ";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.Usermaster ADD RGSSelection VARCHAR( 1024 ) NOT NULL DEFAULT '-RG--DAR--DAPO--BK--PX--V--I--B--G--Y--O--R--GR--BR--PR--FLG--WKD--SAT--SUN--P--A--AF--NS--GC--LI--MB--EO--AO-'";
    safeUpdate($conn, $query, true);
    $query = "CREATE TABLE access.UserDivLockDate (ID INT NOT NULL AUTO_INCREMENT , UserDivLockDate.Div VARCHAR( 255 ) NOT NULL DEFAULT '.', Date INT(8) NOT NULL DEFAULT '0', PRIMARY KEY ( ID ) ) ";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.AttendanceMaster ADD PHF INT( 1 ) NOT NULL DEFAULT '0' ";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.AttendanceMaster SET PHF = 1 WHERE ADate IN (SELECT OTDate FROM access.OTDate)";
    safeUpdate($conn, $query, true);
    V262077124($conn);
}

function V251975122($conn) { 
    $query = "ALTER TABLE access.OtherSettingMaster CHANGE EarlyInOT FlagLimitType VARCHAR(255) DEFAULT NULL";
    safeUpdate($conn, $query, true);
    $query = "SELECT FlagLimitType, LessLunchOT, NoBreakException, EarlyInOTDayDate, MinOTValue, MaxOTValue FROM Access.OtherSettingMaster";
    $main_result = selectData($conn, $query);
    $query = "ALTER TABLE Access.tgroup ADD EarlyInOT VARCHAR( 5 ) NOT NULL DEFAULT '" . $main_result[0] . "', ADD LessLunchOT VARCHAR( 5 ) NOT NULL DEFAULT '" . $main_result[1] . "', ADD NoBreakException VARCHAR( 5 ) NOT NULL DEFAULT '" . $main_result[2] . "', ADD EarlyInOTDayDate VARCHAR( 5 ) NOT NULL DEFAULT '" . $main_result[3] . "', ADD MinOTValue INT( 5 ) NOT NULL DEFAULT '" . $main_result[4] . "', ADD MaxOTValue INT( 5 ) NOT NULL DEFAULT '" . $main_result[5] . "'";
    safeUpdate($conn, $query, true);
    V262076123($conn);
}

function V251974120($conn) { 
    $data1 = "";
    $data2 = "";
    $query = "SELECT FlagDayRotationID, e_id, e_date FROM access.FlagDayRotation ORDER BY e_id, e_date";
    $result = safeUpdateIData($conn, $query, true);
//    $result = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
        if ($data1 == $cur[1] && $data2 == $cur[2]) {
            $query = "DELETE FROM access.FlagDayRotation WHERE FlagDayRotationID = '" . $cur[0] . "'";
            safeUpdate($conn, $query, true);
            $data1 = $cur[1];
            $data2 = $cur[2];
        }
    }
    $query = "ALTER TABLE access.FlagDayRotation ADD UNIQUE FDRED (e_id, e_date ) ";
    safeUpdate($conn, $query, true);
//    mysqli_query($conn, $query);
    V251975122($conn);
}

function V241974119($conn) {
    V251974120($conn);
}

function V231974118($conn) {
    V241974119($conn);
}

function V231974117($conn) {
    global $result;
    $query = "ALTER TABLE access.PayrollMap ADD DataCOMPayroll VARCHAR( 5 ) NOT NULL DEFAULT 'No', ADD UpdateDate VARCHAR( 5 ) NOT NULL DEFAULT 'No', ADD UpdateSalary VARCHAR( 5 ) NOT NULL DEFAULT 'No', ADD Project VARCHAR( 50 ) NOT NULL DEFAULT '', ADD CostCentre VARCHAR( 50 ) NOT NULL DEFAULT ''";
    safeUpdate($conn, $query, true);
    $mac = encryptDecrypt($result[1]);
    $datacom_flag = false;
    if ($mac == "00-14-38-B8-FA-CE") {
        $datacom_flag = true;
    } else {
        if ($mac == "00-04-23-B5-26-2F") {
            $datacom_flag = true;
        } else {
            if ($mac == "00-1C-F0-A7-63-05") {
                $datacom_flag = true;
            } else {
                if ($mac == "00-1E-90-DC-B9-FE") {
                    $datacom_flag = true;
                } else {
                    if ($mac == "00-1C-25-24-FD-78") {
                        $datacom_flag = true;
                    } else {
                        if ($mac == "00-12-3F-47-34-23") {
                            $datacom_flag = true;
                        } else {
                            if ($mac == "00-16-EC-A4-8B-B6") {
                                $datacom_flag = true;
                            } else {
                                if ($mac == "00-0F-1F-68-8E-A2") {
                                    $datacom_flag = true;
                                } else {
                                    if ($mac == "00-23-AE-7B-6F-81") {
                                        $datacom_flag = true;
                                    } else {
                                        if ($mac == "00-22-19-A4-47-1E") {
                                            $datacom_flag = true;
                                        } else {
                                            if ($mac == "00-1C-25-4D-E8-26") {
                                                $datacom_flag = true;
                                            } else {
                                                if ($mac == "00-1C-C4-95-81-E4") {
                                                    $datacom_flag = true;
                                                } else {
                                                    if ($mac == "00-1E-C9-D5-38-9E") {
                                                        $datacom_flag = true;
                                                    } else {
                                                        if ($mac == "00-13-D3-07-92-25") {
                                                            $datacom_flag = true;
                                                        } else {
                                                            if ($mac == "00-19-5B-84-13-3B") {
                                                                $datacom_flag = true;
                                                            } else {
                                                                if ($mac == "00-1C-25-26-FF-6E") {
                                                                    $datacom_flag = true;
                                                                } else {
                                                                    if ($mac == "40-61-86-0E-D5-07") {
                                                                        $datacom_flag = true;
                                                                    } else {
                                                                        if ($mac == "40-61-86-0F-28-EB") {
                                                                            $datacom_flag = true;
                                                                        } else {
                                                                            if ($mac == "40-61-86-0F-29-18") {
                                                                                $datacom_flag = true;
                                                                            } else {
                                                                                if ($mac == "00-18-FE-FE-69-4A") {
                                                                                    $datacom_flag = true;
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    if ($datacom_flag) {
        $query = "UPDATE access.PayrollMap SET DataCOMPayroll = 'Yes', UpdateDate = 'Yes', UpdateSalary = 'Yes'";
        safeUpdate($conn, $query, true);
    }
    V231974118($conn);
}

function V231973116($conn) {
    V231974117($conn);
}

function V231973115($conn) {
    $query = "DELETE FROM access.UserDiv WHERE LENGTH(UserDiv.Div) = 0";
    safeUpdate($conn, $query, true);
    $query = "DELETE FROM access.UserDept WHERE LENGTH(UserDept.Dept) = 0";
    safeUpdate($conn, $query, true);
    V231973116($conn);
}

function V231973114($conn) {
    $query = "CREATE TABLE access.AccessFlag ( TLSFlagID int( 10 ) NOT NULL AUTO_INCREMENT , Violet varchar( 5 ) DEFAULT 'Yes', Indigo varchar( 5 ) DEFAULT 'Yes', Blue varchar( 5 ) DEFAULT 'Yes', Green varchar( 5 ) DEFAULT 'Yes', Yellow varchar( 5 ) DEFAULT 'Yes', Orange varchar( 5 ) DEFAULT 'Yes', Red varchar( 5 ) DEFAULT 'Yes', Gray varchar( 5 ) DEFAULT 'Yes', Brown varchar( 5 ) DEFAULT 'Yes', Purple varchar( 5 ) DEFAULT 'Yes', Black varchar( 5 ) DEFAULT 'Yes', Proxy varchar( 5 ) DEFAULT 'Yes', PRIMARY KEY ( TLSFlagID ) ) ";
    safeUpdate($conn, $query, true);
    $query = "INSERT INTO access.AccessFlag (Violet) VALUES ('Yes')";
    safeUpdate($conn, $query, true);
    unlink("GroupAccessLimit.php");
    unlink("GroupAccessLimit.bat");
    V231973115($conn);
}

function V221873113($conn) {
    V231973114($conn);
}

function V221873112($conn) {
    $query = "CREATE TABLE Access.GroupExempt (ID INT NOT NULL AUTO_INCREMENT , Module VARCHAR( 5 ) NOT NULL DEFAULT '.', Grp VARCHAR( 255 ) NOT NULL DEFAULT '.', Val VARCHAR( 255 ) NOT NULL DEFAULT '.', PRIMARY KEY ( ID ), UNIQUE  KEY  MGV ( Module,  Grp,  Val) )";
    safeUpdate($conn, $query, true);
    $query = "CREATE TABLE Access.GroupFlagLimit (ID INT NOT NULL AUTO_INCREMENT , Grp VARCHAR( 255 ) NOT NULL DEFAULT '.', Val VARCHAR( 255 ) NOT NULL DEFAULT '.', Violet INT( 5 ) NOT NULL DEFAULT '365', Indigo INT( 5 ) NOT NULL DEFAULT '365', Blue INT( 5 ) NOT NULL DEFAULT '365', Green INT( 5 ) NOT NULL DEFAULT '365', Yellow INT( 5 ) NOT NULL DEFAULT '365', Orange INT( 5 ) NOT NULL DEFAULT '365', Red INT( 5 ) NOT NULL DEFAULT '365', Gray INT( 5 ) NOT NULL DEFAULT '365', Brown INT( 5 ) NOT NULL DEFAULT '365', Purple INT( 5 ) NOT NULL DEFAULT '365', PRIMARY KEY ( ID ), UNIQUE  KEY  GV (Grp,  Val) ) ";
    safeUpdate($conn, $query, true);
    V221873113($conn);
}

function V221772111($conn) {
    $query = "ALTER TABLE access.tgroup ADD AccessRestrict VARCHAR( 5 ) NOT NULL DEFAULT 'No', ADD RelaxRestrict VARCHAR( 5 ) NOT NULL DEFAULT 'No', ADD StartHour INT( 2 ) NOT NULL DEFAULT '0', ADD CloseHour INT( 2 ) NOT NULL DEFAULT '0'";
    safeUpdate($conn, $query, true);
    unlink("CheckShiftAccess.php");
    unlink("CheckShiftAccess.bat");
    unlink("i.php");
    unlink("DeptClocking.php");
    unlink("NameRepair.php");
    unlink("Delete.php");
    unlink("P_Card_Shift.php");
    unlink("P_Card_Shift.bat");
    unlink("IDRepair.php");
    unlink("Matori-1-Repair.php");
    unlink("Matori-2-Repair.php");
    unlink("Repair.php");
    unlink("OK.php");
    unlink("AssignInTimeForWeeklyShift.php");
    unlink("Insignia.php");
    unlink("ND.php");
    unlink("E.php");
    unlink("ReportNight.php");
    unlink("ReportDeleteProcessedRecord.php");
    V221873112($conn);
}

function V221772110($conn) {
    $query = "UPDATE access.EmployeeFlag SET Violet = 365 WHERE Violet = 50";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.EmployeeFlag SET Indigo = 365 WHERE Indigo = 50";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.EmployeeFlag SET Blue = 365 WHERE Blue = 50";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.EmployeeFlag SET Green = 365 WHERE Green = 50";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.EmployeeFlag SET Yellow = 365 WHERE Yellow = 50";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.EmployeeFlag SET Orange = 365 WHERE Orange = 50";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.EmployeeFlag SET Red = 365 WHERE Red = 50";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.EmployeeFlag SET Gray = 365 WHERE Gray = 50";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.EmployeeFlag SET Brown = 365 WHERE Brown = 50";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.EmployeeFlag SET Purple = 365 WHERE Purple = 50";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.employeeflag 
        MODIFY Violet INT(5) NOT NULL DEFAULT '365', 
        MODIFY Indigo INT(5) NOT NULL DEFAULT '365', 
        MODIFY Blue INT(5) NOT NULL DEFAULT '365', 
        MODIFY Green INT(5) NOT NULL DEFAULT '365', 
        MODIFY Yellow INT(5) NOT NULL DEFAULT '365', 
        MODIFY Orange INT(5) NOT NULL DEFAULT '365', 
        MODIFY Red INT(5) NOT NULL DEFAULT '365', 
        MODIFY Gray INT(5) NOT NULL DEFAULT '365', 
        MODIFY Brown INT(5) NOT NULL DEFAULT '365', 
        MODIFY Purple INT(5) NOT NULL DEFAULT '365'";
    safeUpdate($conn, $query, true);
//    $this_conn = mysql_connect("localhost", "root", "root");
//    
//    $query = "CREATE USER 'shoot'@'%' IDENTIFIED BY 'wallah'";
//    safeUpdate($this_conn, $query, true);
//    $query = "CREATE USER 'shoot'@'localhost' IDENTIFIED BY 'wallah'";
//    safeUpdate($this_conn, $query, true);
//    $query = "GRANT ALL PRIVILEGES ON *.* TO 'shoot'@'%'";
//    safeUpdate($this_conn, $query, true);
//    $query = "GRANT ALL PRIVILEGES ON *.* TO 'shoot'@'localhost'";
//    safeUpdate($this_conn, $query, true);
//    $query = "DROP TABLE Access.archive_am";
//    safeUpdate($this_conn, $query, true);
//    $query = "DROP TABLE Access.archive_dm";
//    safeUpdate($this_conn, $query, true);
//    $query = "DROP TABLE Access.archive_tenter";
//    safeUpdate($this_conn, $query, true);
    V221772111($conn);
}

//function V211772109($conn) {
//    $this_conn = mysql_connect("localhost", "root", "root");
//    $query = "CREATE DATABASE AccessArchive";
//    safeUpdate($conn, $query, true);
//    $query = "CREATE  TABLE  AccessArchive.archive_tenter (  e_date varchar( 8  )  NOT  NULL DEFAULT  '', e_time varchar( 6  )  NOT  NULL DEFAULT  '', g_id int( 10  )  NOT  NULL DEFAULT  '0', e_id int( 10  )  NOT  NULL DEFAULT  '0', e_name varchar( 30  )  DEFAULT NULL , e_idno varchar( 30  )  DEFAULT NULL , e_group smallint( 5  )  NOT  NULL DEFAULT  '0', e_user varchar( 1  )  DEFAULT NULL , e_mode varchar( 1  )  DEFAULT NULL , e_type varchar( 1  )  DEFAULT NULL , e_result varchar( 1  )  DEFAULT NULL , e_etc varchar( 1  )  DEFAULT NULL , ed int( 10  )  NOT  NULL  DEFAULT  '0', p_flag int( 10  )  NOT  NULL DEFAULT  '0', e_uptime varchar( 14  )  DEFAULT NULL , e_upmode varchar( 1  )  DEFAULT NULL , PRIMARY  KEY (  e_date ,  e_time ,  g_id ,  e_id  ) , KEY  e_group (  e_group  ) , KEY  ed (  ed  ) , KEY  e_date (  e_date  ) , KEY  e_time (  e_time  ) , KEY  g_id (  g_id  ) , KEY  e_id (  e_id  ) , KEY  p_flag (  p_flag  ) , KEY  e_etc (  e_etc  )  ) ";
//    safeUpdate($this_conn, $query, true);
//    $query = "CREATE  TABLE  AccessArchive.archive_am (  AttendanceID int( 10  )  NOT  NULL  DEFAULT  '0' , EmployeeID int( 10  )  NOT  NULL DEFAULT  '0', EmpID varchar( 10  )  DEFAULT NULL , group_id int( 10  )  NOT  NULL DEFAULT  '0', group_min int( 10  )  NOT  NULL DEFAULT  '0', ADate int( 10  )  NOT  NULL DEFAULT  '0', Week int( 10  )  NOT  NULL DEFAULT  '0', EarlyIn int( 10  )  NOT  NULL DEFAULT  '0', LateIn int( 10  )  NOT  NULL DEFAULT  '0', Break int( 10  )  NOT  NULL DEFAULT  '0', LessBreak int( 10  )  NOT  NULL DEFAULT  '0', MoreBreak int( 10  )  NOT  NULL DEFAULT  '0', EarlyOut int( 10  )  NOT  NULL DEFAULT  '0', LateOut int( 10  )  NOT  NULL DEFAULT  '0', Normal int( 10  )  NOT  NULL DEFAULT  '0', Grace int( 10  )  NOT  NULL DEFAULT  '0', Overtime int( 10  )  NOT  NULL DEFAULT  '0', AOvertime int( 10  )  NOT  NULL DEFAULT  '0', Day varchar( 50  )  DEFAULT NULL , Flag varchar( 10  )  NOT  NULL DEFAULT  'Black', p_flag int( 11  )  NOT  NULL DEFAULT  '0', LateIn_flag int( 1  )  NOT  NULL DEFAULT  '0', EarlyOut_flag int( 1  )  NOT  NULL DEFAULT  '0', MoreBreak_flag int( 1  )  NOT  NULL DEFAULT  '0', OT1 varchar( 255  )  NOT  NULL DEFAULT  'Saturday', OT2 varchar( 255  )  NOT  NULL DEFAULT  'Sunday', NightFlag int( 11  )  NOT  NULL DEFAULT  '0', RotateFlag int( 11  )  NOT  NULL DEFAULT  '0', Remark varchar( 1024  )  NOT  NULL DEFAULT  '.', PRIMARY  KEY (  AttendanceID  ) , UNIQUE  KEY  AEA (  EmployeeID ,  ADate  ) , KEY  g_id (  group_id  ) , KEY  EmployeeID (  EmployeeID  )  ) ";
//    safeUpdate($this_conn, $query, true);
//    $query = "CREATE  TABLE  AccessArchive.archive_dm (  DayMasterID int( 10  )  NOT  NULL  DEFAULT  '0' , e_id int( 10  )  NOT  NULL DEFAULT  '0', TDate int( 10  )  NOT  NULL DEFAULT  '0', archive_dm.Entry varchar( 6  )  DEFAULT NULL , Start varchar( 6  )  DEFAULT NULL , BreakOut varchar( 6  )  DEFAULT NULL , BreakIn varchar( 6  )  DEFAULT NULL , Close varchar( 6  )  DEFAULT NULL , archive_dm.Exit varchar( 6  )  DEFAULT NULL , p_flag int( 11  )  NOT  NULL DEFAULT  '0', group_id int( 10  )  NOT  NULL DEFAULT  '0', Flag varchar( 10  )  NOT  NULL DEFAULT  'Black', Work int( 11  )  NOT  NULL DEFAULT  '0', PRIMARY  KEY (  DayMasterID  ) , UNIQUE  KEY  DET (  e_id ,  TDate  ) , KEY  p_flag (  p_flag  ) , KEY  e_id (  e_id  )  )";
//    safeUpdate($this_conn, $query, true);
//    V221772110($conn);
//}
//
//function V211772108($conn) {
//    $this_conn = mysql_connect("localhost", "root", "root");
//    $query = "CREATE DATABASE AccessArchive";
//    safeUpdate($conn, $query, true);
//    $query = "CREATE  TABLE  AccessArchive.archive_tenter (  e_date varchar( 8  )  NOT  NULL DEFAULT  '', e_time varchar( 6  )  NOT  NULL DEFAULT  '', g_id int( 10  )  NOT  NULL DEFAULT  '0', e_id int( 10  )  NOT  NULL DEFAULT  '0', e_name varchar( 30  )  DEFAULT NULL , e_idno varchar( 30  )  DEFAULT NULL , e_group smallint( 5  )  NOT  NULL DEFAULT  '0', e_user varchar( 1  )  DEFAULT NULL , e_mode varchar( 1  )  DEFAULT NULL , e_type varchar( 1  )  DEFAULT NULL , e_result varchar( 1  )  DEFAULT NULL , e_etc varchar( 1  )  DEFAULT NULL , ed int( 10  )  NOT  NULL  DEFAULT  '0', p_flag int( 10  )  NOT  NULL DEFAULT  '0', e_uptime varchar( 14  )  DEFAULT NULL , e_upmode varchar( 1  )  DEFAULT NULL , PRIMARY  KEY (  e_date ,  e_time ,  g_id ,  e_id  ) , KEY  e_group (  e_group  ) , KEY  ed (  ed  ) , KEY  e_date (  e_date  ) , KEY  e_time (  e_time  ) , KEY  g_id (  g_id  ) , KEY  e_id (  e_id  ) , KEY  p_flag (  p_flag  ) , KEY  e_etc (  e_etc  )  ) ";
//    safeUpdate($this_conn, $query, true);
//    $query = "CREATE  TABLE  AccessArchive.archive_am (  AttendanceID int( 10  )  NOT  NULL  DEFAULT  '0' , EmployeeID int( 10  )  NOT  NULL DEFAULT  '0', EmpID varchar( 10  )  DEFAULT NULL , group_id int( 10  )  NOT  NULL DEFAULT  '0', group_min int( 10  )  NOT  NULL DEFAULT  '0', ADate int( 10  )  NOT  NULL DEFAULT  '0', Week int( 10  )  NOT  NULL DEFAULT  '0', EarlyIn int( 10  )  NOT  NULL DEFAULT  '0', LateIn int( 10  )  NOT  NULL DEFAULT  '0', Break int( 10  )  NOT  NULL DEFAULT  '0', LessBreak int( 10  )  NOT  NULL DEFAULT  '0', MoreBreak int( 10  )  NOT  NULL DEFAULT  '0', EarlyOut int( 10  )  NOT  NULL DEFAULT  '0', LateOut int( 10  )  NOT  NULL DEFAULT  '0', Normal int( 10  )  NOT  NULL DEFAULT  '0', Grace int( 10  )  NOT  NULL DEFAULT  '0', Overtime int( 10  )  NOT  NULL DEFAULT  '0', AOvertime int( 10  )  NOT  NULL DEFAULT  '0', Day varchar( 50  )  DEFAULT NULL , Flag varchar( 10  )  NOT  NULL DEFAULT  'Black', p_flag int( 11  )  NOT  NULL DEFAULT  '0', LateIn_flag int( 1  )  NOT  NULL DEFAULT  '0', EarlyOut_flag int( 1  )  NOT  NULL DEFAULT  '0', MoreBreak_flag int( 1  )  NOT  NULL DEFAULT  '0', OT1 varchar( 255  )  NOT  NULL DEFAULT  'Saturday', OT2 varchar( 255  )  NOT  NULL DEFAULT  'Sunday', NightFlag int( 11  )  NOT  NULL DEFAULT  '0', RotateFlag int( 11  )  NOT  NULL DEFAULT  '0', Remark varchar( 1024  )  NOT  NULL DEFAULT  '.', PRIMARY  KEY (  AttendanceID  ) , UNIQUE  KEY  AEA (  EmployeeID ,  ADate  ) , KEY  g_id (  group_id  ) , KEY  EmployeeID (  EmployeeID  )  ) ";
//    safeUpdate($this_conn, $query, true);
//    $query = "CREATE  TABLE  AccessArchive.archive_dm (  DayMasterID int( 10  )  NOT  NULL  DEFAULT  '0' , e_id int( 10  )  NOT  NULL DEFAULT  '0', TDate int( 10  )  NOT  NULL DEFAULT  '0', archive_dm.Entry varchar( 6  )  DEFAULT NULL , Start varchar( 6  )  DEFAULT NULL , BreakOut varchar( 6  )  DEFAULT NULL , BreakIn varchar( 6  )  DEFAULT NULL , Close varchar( 6  )  DEFAULT NULL , archive_dm.Exit varchar( 6  )  DEFAULT NULL , p_flag int( 11  )  NOT  NULL DEFAULT  '0', group_id int( 10  )  NOT  NULL DEFAULT  '0', Flag varchar( 10  )  NOT  NULL DEFAULT  'Black', Work int( 11  )  NOT  NULL DEFAULT  '0', PRIMARY  KEY (  DayMasterID  ) , UNIQUE  KEY  DET (  e_id ,  TDate  ) , KEY  p_flag (  p_flag  ) , KEY  e_id (  e_id  )  )";
//    safeUpdate($this_conn, $query, true);
//    V211772109($conn);
//}

//function V211772107($conn) {
//    $query = "CREATE DATABASE AccessArchive";
//    safeUpdate($conn, $query, true);
//    $query = "CREATE  TABLE  AccessArchive.archive_tenter (  e_date varchar( 8  )  NOT  NULL DEFAULT  '', e_time varchar( 6  )  NOT  NULL DEFAULT  '', g_id int( 10  )  NOT  NULL DEFAULT  '0', e_id int( 10  )  NOT  NULL DEFAULT  '0', e_name varchar( 30  )  DEFAULT NULL , e_idno varchar( 30  )  DEFAULT NULL , e_group smallint( 5  )  NOT  NULL DEFAULT  '0', e_user varchar( 1  )  DEFAULT NULL , e_mode varchar( 1  )  DEFAULT NULL , e_type varchar( 1  )  DEFAULT NULL , e_result varchar( 1  )  DEFAULT NULL , e_etc varchar( 1  )  DEFAULT NULL , ed int( 10  )  NOT  NULL  DEFAULT  '0', p_flag int( 10  )  NOT  NULL DEFAULT  '0', e_uptime varchar( 14  )  DEFAULT NULL , e_upmode varchar( 1  )  DEFAULT NULL , PRIMARY  KEY (  e_date ,  e_time ,  g_id ,  e_id  ) , KEY  e_group (  e_group  ) , KEY  ed (  ed  ) , KEY  e_date (  e_date  ) , KEY  e_time (  e_time  ) , KEY  g_id (  g_id  ) , KEY  e_id (  e_id  ) , KEY  p_flag (  p_flag  ) , KEY  e_etc (  e_etc  )  ) ";
//    safeUpdate($conn, $query, true);
//    $query = "CREATE  TABLE  AccessArchive.archive_am (  AttendanceID int( 10  )  NOT  NULL  DEFAULT  '0' , EmployeeID int( 10  )  NOT  NULL DEFAULT  '0', EmpID varchar( 10  )  DEFAULT NULL , group_id int( 10  )  NOT  NULL DEFAULT  '0', group_min int( 10  )  NOT  NULL DEFAULT  '0', ADate int( 10  )  NOT  NULL DEFAULT  '0', Week int( 10  )  NOT  NULL DEFAULT  '0', EarlyIn int( 10  )  NOT  NULL DEFAULT  '0', LateIn int( 10  )  NOT  NULL DEFAULT  '0', Break int( 10  )  NOT  NULL DEFAULT  '0', LessBreak int( 10  )  NOT  NULL DEFAULT  '0', MoreBreak int( 10  )  NOT  NULL DEFAULT  '0', EarlyOut int( 10  )  NOT  NULL DEFAULT  '0', LateOut int( 10  )  NOT  NULL DEFAULT  '0', Normal int( 10  )  NOT  NULL DEFAULT  '0', Grace int( 10  )  NOT  NULL DEFAULT  '0', Overtime int( 10  )  NOT  NULL DEFAULT  '0', AOvertime int( 10  )  NOT  NULL DEFAULT  '0', Day varchar( 50  )  DEFAULT NULL , Flag varchar( 10  )  NOT  NULL DEFAULT  'Black', p_flag int( 11  )  NOT  NULL DEFAULT  '0', LateIn_flag int( 1  )  NOT  NULL DEFAULT  '0', EarlyOut_flag int( 1  )  NOT  NULL DEFAULT  '0', MoreBreak_flag int( 1  )  NOT  NULL DEFAULT  '0', OT1 varchar( 255  )  NOT  NULL DEFAULT  'Saturday', OT2 varchar( 255  )  NOT  NULL DEFAULT  'Sunday', NightFlag int( 11  )  NOT  NULL DEFAULT  '0', RotateFlag int( 11  )  NOT  NULL DEFAULT  '0', Remark varchar( 1024  )  NOT  NULL DEFAULT  '.', PRIMARY  KEY (  AttendanceID  ) , UNIQUE  KEY  AEA (  EmployeeID ,  ADate  ) , KEY  g_id (  group_id  ) , KEY  EmployeeID (  EmployeeID  )  ) ";
//    safeUpdate($conn, $query, true);
//    mysqli_query($conn, $query);
//    $query = "CREATE  TABLE  AccessArchive.archive_dm (  DayMasterID int( 10  )  NOT  NULL  DEFAULT  '0' , e_id int( 10  )  NOT  NULL DEFAULT  '0', TDate int( 10  )  NOT  NULL DEFAULT  '0', archive_dm.Entry varchar( 6  )  DEFAULT NULL , Start varchar( 6  )  DEFAULT NULL , BreakOut varchar( 6  )  DEFAULT NULL , BreakIn varchar( 6  )  DEFAULT NULL , Close varchar( 6  )  DEFAULT NULL , archive_dm.Exit varchar( 6  )  DEFAULT NULL , p_flag int( 11  )  NOT  NULL DEFAULT  '0', group_id int( 10  )  NOT  NULL DEFAULT  '0', Flag varchar( 10  )  NOT  NULL DEFAULT  'Black', Work int( 11  )  NOT  NULL DEFAULT  '0', PRIMARY  KEY (  DayMasterID  ) , UNIQUE  KEY  DET (  e_id ,  TDate  ) , KEY  p_flag (  p_flag  ) , KEY  e_id (  e_id  )  )";
//    safeUpdate($conn, $query, true);
////    $query = "ALTER TABLE Access.event ADD p_flag SMALLINT NOT NULL DEFAULT '0'";
////    safeUpdate($conn, $query, true);
//    V211772108($conn);
//}

function V211672106($conn) {
    $query = "CREATE TABLE Cardholder (FTItemID INT NOT NULL , FirstName VARCHAR( 255 ) NOT NULL , LastName VARCHAR( 255 ) NOT NULL , Authorised INT NOT NULL DEFAULT '1', PRIMARY KEY ( FTItemID ) ) ";
    safeUpdate($conn, $query, true);
    $query = "CREATE TABLE FTItem (\tID INT NOT NULL ,\tName VARCHAR( 255 ) NOT NULL ,\tDescription VARCHAR( 255 ) NOT NULL DEFAULT '.',\tDivisionID INT NOT NULL ,\tTypeID INT NOT NULL ,\tPRIMARY KEY ( ID ) \t) ";
    safeUpdate($conn, $query, true);
    $query = "\tCREATE TABLE RelatedItems (\tEventID BIGINT NOT NULL DEFAULT '0',\tFTItemID INT NOT NULL DEFAULT '0',\tRelationCode SMALLINT NOT NULL DEFAULT '0'\t)";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE RelatedItems ADD INDEX ( EventID ) ";
    safeUpdate($conn, $query, true);
    $query = "\tALTER TABLE RelatedItems ADD INDEX ( FTItemID ) ";
    safeUpdate($conn, $query, true);
    $query = "\tALTER TABLE RelatedItems ADD UNIQUE (EventID, FTItemID ) ";
    safeUpdate($conn, $query, true);
    $query = "CREATE TABLE Access.Event (\tID BIGINT NOT NULL ,\tOccurrenceDate INT( 8 ) NOT NULL ,\tOccurrenceTime VARCHAR( 6 ) NOT NULL ,\tEventType INT NOT NULL DEFAULT '0',\tDivisionID INT NOT NULL DEFAULT '0',\te_ID INT NOT NULL DEFAULT '0',\tg_ID INT NOT NULL DEFAULT '0',\tPRIMARY KEY ( ID ) \t)";
    safeUpdate($conn, $query, true);
    $query = "CREATE  TABLE  archive_tenter (  e_date varchar( 8  )  NOT  NULL DEFAULT  '', e_time varchar( 6  )  NOT  NULL DEFAULT  '', g_id int( 10  )  NOT  NULL DEFAULT  '0', e_id int( 10  )  NOT  NULL DEFAULT  '0', e_name varchar( 30  )  DEFAULT NULL , e_idno varchar( 30  )  DEFAULT NULL , e_group smallint( 5  )  NOT  NULL DEFAULT  '0', e_user varchar( 1  )  DEFAULT NULL , e_mode varchar( 1  )  DEFAULT NULL , e_type varchar( 1  )  DEFAULT NULL , e_result varchar( 1  )  DEFAULT NULL , e_etc varchar( 1  )  DEFAULT NULL , ed int( 10  )  NOT  NULL  DEFAULT  '0', p_flag int( 10  )  NOT  NULL DEFAULT  '0', e_uptime varchar( 14  )  DEFAULT NULL , e_upmode varchar( 1  )  DEFAULT NULL , PRIMARY  KEY (  e_date ,  e_time ,  g_id ,  e_id  ) , KEY  e_group (  e_group  ) , KEY  ed (  ed  ) , KEY  e_date (  e_date  ) , KEY  e_time (  e_time  ) , KEY  g_id (  g_id  ) , KEY  e_id (  e_id  ) , KEY  p_flag (  p_flag  ) , KEY  e_etc (  e_etc  )  ) ";
    safeUpdate($conn, $query, true);
    $query = "CREATE  TABLE  archive_am (  AttendanceID int( 10  )  NOT  NULL  DEFAULT  '0' , EmployeeID int( 10  )  NOT  NULL DEFAULT  '0', EmpID varchar( 10  )  DEFAULT NULL , group_id int( 10  )  NOT  NULL DEFAULT  '0', group_min int( 10  )  NOT  NULL DEFAULT  '0', ADate int( 10  )  NOT  NULL DEFAULT  '0', Week int( 10  )  NOT  NULL DEFAULT  '0', EarlyIn int( 10  )  NOT  NULL DEFAULT  '0', LateIn int( 10  )  NOT  NULL DEFAULT  '0', Break int( 10  )  NOT  NULL DEFAULT  '0', LessBreak int( 10  )  NOT  NULL DEFAULT  '0', MoreBreak int( 10  )  NOT  NULL DEFAULT  '0', EarlyOut int( 10  )  NOT  NULL DEFAULT  '0', LateOut int( 10  )  NOT  NULL DEFAULT  '0', Normal int( 10  )  NOT  NULL DEFAULT  '0', Grace int( 10  )  NOT  NULL DEFAULT  '0', Overtime int( 10  )  NOT  NULL DEFAULT  '0', AOvertime int( 10  )  NOT  NULL DEFAULT  '0', Day varchar( 50  )  DEFAULT NULL , Flag varchar( 10  )  NOT  NULL DEFAULT  'Black', p_flag int( 11  )  NOT  NULL DEFAULT  '0', LateIn_flag int( 1  )  NOT  NULL DEFAULT  '0', EarlyOut_flag int( 1  )  NOT  NULL DEFAULT  '0', MoreBreak_flag int( 1  )  NOT  NULL DEFAULT  '0', OT1 varchar( 255  )  NOT  NULL DEFAULT  'Saturday', OT2 varchar( 255  )  NOT  NULL DEFAULT  'Sunday', NightFlag int( 11  )  NOT  NULL DEFAULT  '0', RotateFlag int( 11  )  NOT  NULL DEFAULT  '0', Remark varchar( 1024  )  NOT  NULL DEFAULT  '.', PRIMARY  KEY (  AttendanceID  ) , UNIQUE  KEY  AEA (  EmployeeID ,  ADate  ) , KEY  g_id (  group_id  ) , KEY  EmployeeID (  EmployeeID  )  ) ";
    safeUpdate($conn, $query, true);
    $query = "CREATE  TABLE  archive_dm (  DayMasterID int( 10  )  NOT  NULL  DEFAULT  '0' , e_id int( 10  )  NOT  NULL DEFAULT  '0', TDate int( 10  )  NOT  NULL DEFAULT  '0', archive_dm.Entry varchar( 6  )  DEFAULT NULL , Start varchar( 6  )  DEFAULT NULL , BreakOut varchar( 6  )  DEFAULT NULL , BreakIn varchar( 6  )  DEFAULT NULL , Close varchar( 6  )  DEFAULT NULL , archive_dm.Exit varchar( 6  )  DEFAULT NULL , p_flag int( 11  )  NOT  NULL DEFAULT  '0', group_id int( 10  )  NOT  NULL DEFAULT  '0', Flag varchar( 10  )  NOT  NULL DEFAULT  'Black', Work int( 11  )  NOT  NULL DEFAULT  '0', PRIMARY  KEY (  DayMasterID  ) , UNIQUE  KEY  DET (  e_id ,  TDate  ) , KEY  p_flag (  p_flag  ) , KEY  e_id (  e_id  )  )";
    safeUpdate($conn, $query, true);
//    V211772107($conn);
    V221772110($conn);
}

function V201572105($conn) {
    $this_conn = mysql_connect("localhost", "root", "kifak");
    $query = "SET PASSWORD FOR 'root'@'localhost' = PASSWORD( 'root' )";
    mysql_query($query, $this_conn);
    $query = "SET PASSWORD FOR 'root'@'%' = PASSWORD( 'root' )";
    mysql_query($query, $this_conn);
    mysql_close($this_conn);
    V211672106($conn);
}

function V201572104($conn) {
    $query = "CREATE TABLE IF NOT EXISTS access.ShiftRoster (ShiftRosterID INT NOT NULL AUTO_INCREMENT , e_id INT NOT NULL DEFAULT 0, e_date INT NOT NULL DEFAULT 0, e_group INT NOT NULL DEFAULT 0, PRIMARY KEY ( ShiftRosterID), UNIQUE KEY ShiftRoster (e_id, e_date, e_group)) ";
//    safeUpdate($conn, $query, true);
    mysqli_query($conn, $query);
    $query = "ALTER TABLE access.OtherSettingMaster ADD UseShiftRoster VARCHAR(5) NOT NULL DEFAULT 'No'";
//    safeUpdate($conn, $query, true);
    mysqli_query($conn, $query);
//    V201572105($conn);
    V211672106($conn);
}

function V191472103($conn) {
    global $result;
    if (encryptDecrypt($result[1]) == "00-16-EC-9E-E0-D1") {
        $query = "UPDATE access.tgroup SET WorkMin = (WorkMin + FlexiBreak ), FlexiBreak  = 0";
        safeUpdateIData($iconn, $query, true);
    }
    $query = "ALTER TABLE access.OtherSettingMaster ADD MoveNS VARCHAR(5) NOT NULL DEFAULT 'No'";
    safeUpdate($conn, $query, true);
    V201572104($conn);
}

function V191472102($conn) {
    global $result;
    if (encryptDecrypt($result[1]) == "00-1E-C9-F6-FA-E1" || encryptDecrypt($result[1]) == "00-1F-D0-64-7E-FA" || encryptDecrypt($result[1]) == "00-1F-D0-22-3A-EA" || encryptDecrypt($result[1]) == "00-04-23-88-A4-AF" || encryptDecrypt($result[1]) == "00-1E-8C-33-D8-0E" || encryptDecrypt($result[1]) == "00-11-2F-E1-CC-FA") {
        $query = "SET PASSWORD FOR 'root'@'localhost' = PASSWORD( 'root' )";
        safeUpdateIData($iconn, $query, true);
        $query = "SET PASSWORD FOR 'root'@'%' = PASSWORD( 'root' )";
        safeUpdateIData($iconn, $query, true);
    } else {
        if (encryptDecrypt($result[1]) == "00-1E-8C-33-D8-0E") {
            $query = "SET PASSWORD FOR 'root'@'localhost' = PASSWORD( 'kifak' )";
            safeUpdateIData($iconn, $query, true);
            $query = "SET PASSWORD FOR 'root'@'%' = PASSWORD( 'kifak' )";
            safeUpdateIData($iconn, $query, true);
        }
    }
    V191472103($conn);
}

function V191472101($conn) {
    $query = "GRANT ALL PRIVILEGES ON * . * TO 'fdmsusr'@'%'";
    safeUpdate($conn, $query, true);
    $query = "GRANT ALL PRIVILEGES ON * . * TO 'fdmsusr'@'localhost'";
    safeUpdate($conn, $query, true);
    $query = "CREATE USER 'shoot'@'%' IDENTIFIED BY 'wallah'";
    safeUpdate($conn, $query, true);
    $query = "CREATE USER 'shoot'@'%' IDENTIFIED BY 'wallah'";
    safeUpdate($conn, $query, true);
    $query = "GRANT ALL PRIVILEGES ON * . * TO 'shoot'@'%'";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.OtherSettingMaster ADD RoundOffAOT VARCHAR(5) NOT NULL DEFAULT 'None'";
    safeUpdate($conn, $query, true);
    V191472102($conn);
}

function V191371100($conn) {
    V191472101($conn);
}

function V19137199($conn) {
    $query = "ALTER TABLE access.OtherSettingMaster ADD MaxOTValue INT NOT NULL DEFAULT '1440'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.19.13.71.99'";
    safeUpdate($conn, $query, true);
    V191371100($conn);
}

function V19137198($conn) {
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.19.13.71.98'";
    safeUpdate($conn, $query, true);
    V19137199($conn);
}

function V19137197($conn) {
    global $result;
    if (encryptDecrypt($result[1]) == "00-14-2A-00-A0-39") {
        $query = "UPDATE access.tgroup SET ScheduleID = 2 WHERE id > 1";
        safeUpdate($conn, $query, true);
    } else {
        if (encryptDecrypt($result[1]) == "00-22-19-A4-47-1E") {
            $query = "UPDATE access.AttendanceMaster SET Normal = 0, Overtime = 0, AOvertime = 0 WHERE Normal = 28800 AND Overtime = 0 AND (Flag = 'Red' OR Flag = 'Orange' OR Flag = 'Indigo')";
            safeUpdate($conn, $query, true);
        }
    }
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.19.13.71.97'";
    safeUpdate($conn, $query, true);
    V19137198($conn);
}

function V19137196($conn) {
    $query = "ALTER TABLE access.OtherSettingMaster ADD AutoApproveOT VARCHAR(5) NOT NULL DEFAULT 'No'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.19.13.71.96'";
    safeUpdate($conn, $query, true);
    V19137197($conn);
}

function V19137195($conn) {
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.19.13.71.95'";
    safeUpdate($conn, $query, true);
    V19137196($conn);
}

function V19137095($conn) {
    global $result;
    if (encryptDecrypt($result[1]) == "00-1E-C9-3B-20-5C") {
        $query = "UPDATE access.UserMaster SET Userpass = '" . encryptString("admin") . "' WHERE Username = 'admin'";
        safeUpdate($conn, $query, true);
    }
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.19.13.70.95'";
    safeUpdate($conn, $query, true);
    V19137195($conn);
}

function V19137094($conn) {
    $query = "ALTER TABLE access.tenter ADD INDEX e_date (e_date)";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.tenter ADD INDEX e_time (e_time)";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.tenter ADD INDEX g_id  (g_id )";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.tenter ADD INDEX e_id  (e_id)";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.tenter ADD INDEX e_group (e_group)";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.tenter ADD INDEX p_flag (p_flag)";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.tenter ADD INDEX e_etc (e_etc)";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.DayMaster DROP INDEX e_id";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.DayMaster DROP INDEX goup_id";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.DayMaster DROP INDEX LastDayMasterID";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.DayMaster ADD INDEX e_id (e_id)";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.DayMaster ADD INDEX p_flag (p_flag)";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.AttendanceMaster DROP INDEX EmpID";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.AttendanceMaster DROP INDEX EmployeeID";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.AttendanceMaster ADD INDEX EmployeeID (EmployeeID)";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.employeeflag DROP INDEX EmployeeID";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.modulemaster DROP INDEX Name_3";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.modulemaster DROP INDEX Name_2";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.modulemaster DROP INDEX Name";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.modulemaster ADD UNIQUE KEY Name (Name)";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.otday DROP INDEX Day_3";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.otday DROP INDEX Day_2";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.otday DROP INDEX Day";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.otday ADD UNIQUE KEY Day (Day)";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.shifttypemaster DROP INDEX Name_3";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.shifttypemaster DROP INDEX Name_2";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.shifttypemaster DROP INDEX Name";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.shifttypemaster ADD UNIQUE KEY Name (Name)";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.transactiontypemaster DROP INDEX Name_3";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.transactiontypemaster DROP INDEX Name_2";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.transactiontypemaster DROP INDEX Name";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.transactiontypemaster ADD UNIQUE KEY Name (Name)";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.19.13.70.94'";
    safeUpdate($conn, $query, true);
    V19137095($conn);
}

function V19137093($conn) {
    $query = "ALTER TABLE access.UserMaster ADD RDSHeaderBreak VARCHAR( 10 ) NOT NULL DEFAULT '25'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.UserMaster SET RDSHeaderBreak = '25'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.19.13.70.93'";
    safeUpdate($conn, $query, true);
    V19137094($conn);
}

function V19137092($conn) {
    $query = "ALTER TABLE access.OtherSettingMaster ADD AutoAssignTerminal VARCHAR(5) NOT NULL DEFAULT 'Yes'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.19.13.70.92'";
    safeUpdate($conn, $query, true);
    V19137093($conn);
}

function V19137091($conn) {
    $query = "ALTER TABLE access.tuser ADD OTRotateDate INT NOT NULL DEFAULT '99999999'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.tuser SET OTRotateDate = '99999999' WHERE OTRotate = 'No'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.tuser SET OTRotateDate = '" . insertToday() . "' WHERE OTRotate = 'Yes'";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.FlagDayRotation ADD Remark VARCHAR( 1024 ) NOT NULL DEFAULT '.'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.19.13.70.91'";
    safeUpdate($conn, $query, true);
    V19137092($conn);
}

function V19137090($conn) {
    $query = "ALTER TABLE access.EmployeeFlag ADD UNIQUE KEY EFEID (EmployeeID) ";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.19.13.70.90'";
    safeUpdate($conn, $query, true);
    V19137091($conn);
}

function V19137089($conn) {
    $query = "ALTER TABLE access.OtherSettingMaster ADD ApproveOTIgnoreActual VARCHAR( 5 ) NOT NULL DEFAULT 'No'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.19.13.70.89'";
    safeUpdate($conn, $query, true);
    V19137090($conn);
}

function V19137088($conn) {
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.19.13.70.88'";
    safeUpdate($conn, $query, true);
    V19137089($conn);
}

function V19137087($conn) {
    $query = "ALTER TABLE access.tuser ADD OldID1 INT NOT NULL DEFAULT '0'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.19.13.70.87'";
    safeUpdate($conn, $query, true);
    V19137088($conn);
}

function V19136987($conn) {
    $query = "ALTER TABLE access.AttendanceMaster ADD Remark VARCHAR( 1024 ) NOT NULL DEFAULT '.'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.19.13.69.87'";
    safeUpdate($conn, $query, true);
    V19137087($conn);
}

function V19136986($conn) {
    $query = "UPDATE access.tuser SET DeptClocking = '0'";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.tuser CHANGE DeptClocking OldID1 INT NOT NULL DEFAULT '0'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.19.13.69.86'";
    safeUpdate($conn, $query, true);
    V19136987($conn);
}

function V19136985($conn) {
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.19.13.69.85'";
    safeUpdate($conn, $query, true);
    V19136986($conn);
}

function V19136984($conn) {
    $query = "UPDATE access.AttendanceMaster SET OT1 = 'Saturday' WHERE OT1 = ''";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.AttendanceMaster SET OT2 = 'Sunday' WHERE OT2 = ''";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.tgroup ADD MinOT1Work INT NOT NULL DEFAULT '0'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.19.13.69.84'";
    safeUpdate($conn, $query, true);
    V19136985($conn);
}

function V19136983($conn) {
    $query = "ALTER TABLE access.tuser 
        MODIFY name VARCHAR(255) NULL DEFAULT NULL,
        MODIFY idno VARCHAR(255) NULL DEFAULT NULL,
        MODIFY dept VARCHAR(255) NULL DEFAULT NULL,
        MODIFY company VARCHAR(255) NULL DEFAULT NULL,
        MODIFY phone VARCHAR(255) NULL DEFAULT NULL,
        MODIFY remark VARCHAR(255) NULL DEFAULT NULL";
    safeUpdate($conn, $query, true);
    $query = "SELECT MACAddress FROM access.OtherSettingMaster";
    $result = selectData($conn, $query);
    $query = "UPDATE access.OtherSettingMaster SET MACAddress = '" . encryptDecrypt($result[0]) . "', EX2 = 'V.19.13.69.83'";
    safeUpdate($conn, $query, true);
    V19136984($conn);
}

function V18126982($conn) {
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.19.13.69.82'";
    safeUpdate($conn, $query, true);
    V19136983($conn);
}

function V18126981($conn) {
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.19.13.69.81'";
    safeUpdate($conn, $query, true);
    V18126982($conn);
}

function V18126980($conn) {
    $query = "ALTER TABLE access.UserMaster ADD RDSSelection VARCHAR( 1024 ) NOT NULL DEFAULT '--P--Dept--Div--Shift--Total', ADD RDSFont VARCHAR( 10 ) NOT NULL DEFAULT '1', ADD RDSCW VARCHAR( 10 ) NOT NULL DEFAULT '15%'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.UserMaster SET RDSSelection = '--P--Dept--Div--Shift--Total'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.19.13.69.80'";
    safeUpdate($conn, $query, true);
    V18126981($conn);
}

function V18126979($conn) {
    $query = "ALTER TABLE access.PayrollMap ADD Status VARCHAR( 255 ) NOT NULL , ADD ActiveValue VARCHAR( 255 ) NOT NULL, ADD PassiveValue VARCHAR( 255 ) NOT NULL";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.PayrollMap SET Overwrite = 'No Synchronization' WHERE PayrollMap = ''";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.19.13.69.79'";
    safeUpdate($conn, $query, true);
    V18126980($conn);
}

function V18126978($conn) {
    $query = "UPDATE access.tuser SET OT1 = 'Saturday' WHERE OT1 IS NULL";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.tuser SET OT1 = 'Saturday' WHERE OT1 = ''";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.tuser SET OT2 = 'Sunday' WHERE OT2 IS NULL";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.tuser SET OT2 = 'Sunday' WHERE OT2 = ''";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.AttendanceMaster SET OT1 = 'Saturday' WHERE OT1 IS NULL";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.AttendanceMaster SET OT1 = 'Saturday' WHERE OT1 = ''";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.AttendanceMaster SET OT2 = 'Sunday' WHERE OT2 IS NULL";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.AttendanceMaster SET OT2 = 'Sunday' WHERE OT2 = ''";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.19.13.69.78'";
    safeUpdate($conn, $query, true);
    V18126979($conn);
}

function V18126977($conn) {
    $query = "CREATE TABLE access.EmployeeFlag (EmployeeFlagID INT NOT NULL AUTO_INCREMENT , EmployeeID INT NOT NULL , Violet INT NOT NULL DEFAULT '50', Indigo INT NOT NULL DEFAULT '50', Blue INT NOT NULL DEFAULT '50', Green INT NOT NULL DEFAULT '50', Yellow INT NOT NULL DEFAULT '50', Orange INT NOT NULL DEFAULT '50', Red INT NOT NULL DEFAULT '50', Gray INT NOT NULL DEFAULT '50', Brown INT NOT NULL DEFAULT '50', Purple INT NOT NULL DEFAULT '50', PRIMARY KEY ( EmployeeFlagID ) , UNIQUE KEY EFEID ( EmployeeID ) ) ";
    safeUpdate($conn, $query, true);
    $query = "CREATE TABLE access.PayrollMap (PayrollMapID INT NOT NULL AUTO_INCREMENT , TableName VARCHAR( 255 ) NOT NULL, Overwrite VARCHAR( 255 ) NOT NULL DEFAULT 'Payroll DB', EID VARCHAR( 255 ) NOT NULL, EName VARCHAR( 255 ) NOT NULL , IDNo VARCHAR( 255 ) NOT NULL , Dept VARCHAR( 255 ) NOT NULL , Division VARCHAR( 255 ) NOT NULL , Remark VARCHAR( 255 ) NOT NULL , Shift VARCHAR( 255 ) NOT NULL , Phone VARCHAR( 255 ) NOT NULL , PRIMARY KEY ( PayrollMapID ) ) ";
    safeUpdate($conn, $query, true);
    $query = "SELECT id FROM access.tuser WHERE id > 0";
//    $result = safeUpdateIData($iconn, $query, true);
    $result = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
        $query = "INSERT INTO access.EmployeeFlag (EmployeeID) VALUES (" . $cur[0] . ")";
        safeUpdate($conn, $query, true);
    }
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.19.13.69.77'";
    safeUpdate($conn, $query, true);
    V18126978($conn);
}

function V18126876($conn) {
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.18.12.68.76'";
    safeUpdate($conn, $query, true);
    V18126977($conn);
}

function V18126875($conn) {
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.18.12.68.75'";
    safeUpdate($conn, $query, true);
    V18126876($conn);
}

function V18126874($conn) {
    $query = "CREATE TABLE access.ProxyEmployeeExempt (ProxyEmployeeExemptID INT NOT NULL AUTO_INCREMENT, EmployeeID INT NOT NULL DEFAULT '0', PRIMARY KEY ( ProxyEmployeeExemptID ), UNIQUE KEY PEEI (EmployeeID) )";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.18.12.68.74'";
    safeUpdate($conn, $query, true);
    V18126875($conn);
}

function V18126873($conn) {
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.18.12.68.73'";
    safeUpdate($conn, $query, true);
    V18126874($conn);
}

function V18116772($conn) {
    $query = "ALTER TABLE access.OtherSettingMaster ADD TCount VARCHAR( 255 ) NOT NULL";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.AttendanceMaster, tgroup SET AttendanceMaster.NightFlag = tgroup.NightFlag, AttendanceMaster.RotateFlag = tgroup.RotateFlag WHERE AttendanceMaster.group_id = tgroup.id";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.18.11.67.72'";
    safeUpdate($conn, $query, true);
    V18126873($conn);
}

function V18116671($conn) {
    $query = "SELECT EX4, PLFlag, NightShiftMaxOutTime FROM access.OtherSettingMaster";
    $o_result = selectData($conn, $query);
    if ($o_result[0] == 1) {
        $query = "SELECT OTDate FROM access.OTDate WHERE OTDate < " . insertToday();
        $result = safeUpdateIData($iconn, $query, true);
        while ($cur = mysqli_fetch_row($result)) {
            $query = "SELECT tuser.id, tuser.group_id, tuser.dept FROM access.tuser WHERE tuser.id NOT IN (SELECT DISTINCT(tenter.e_id) FROM access.tenter, access.tgate, access.tgroup WHERE tenter.g_id = tgate.id AND tenter.e_group = tgroup.id AND tgate.exit = 0 AND ((tenter.e_time > '" . $o_result[2] . "00' AND tgroup.NightFlag = 1) OR (tenter.e_time > '000000' AND tgroup.NightFlag = 0)) AND tenter.e_date = '" . $cur[0] . "')";
            $this_result = safeUpdateIData($iconn, $query, true);
            while ($this_cur = mysqli_fetch_row($this_result)) {
                $sub_query = "SELECT g_id FROM access.DeptGate WHERE dept = '" . $this_cur[2] . "'";
                $sub_result = selectData($conn, $sub_query);
            }
        }
    }
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.18.11.66.71'";
    safeUpdate($conn, $query, true);
    V18116772($conn);
}

function V18116670($conn) {
    $query = "ALTER TABLE access.tgroup ADD RotateFlag INT NOT NULL DEFAULT '0'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.tgroup SET RotateFlag = 1 WHERE id IN (SELECT id FROM ShiftChangeMaster WHERE AE = 1)";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.AttendanceMaster ADD NightFlag INT NOT NULL DEFAULT '0', ADD RotateFlag INT NOT NULL DEFAULT '0'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.AttendanceMaster, tgroup SET AttendanceMaster.NightFlag = tgroup.NightFlag, AttendanceMaster.RotateFlag = tgroup.RotateFlag WHERE AttendanceMaster.group_id = tgroup.id";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.OtherSettingMaster ADD OTDateBalNHrs INT NOT NULL DEFAULT '0', ADD LocationSynchShift VARCHAR (10) NOT NULL DEFAULT 'Server'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.18.11.66.70'";
    safeUpdate($conn, $query, true);
    V18116671($conn);
}

function V18106569($conn) {
    $query = "SELECT DayMasterID FROM access.DayMaster WHERE DayMasterID NOT IN (SELECT DayMaster.DayMasterID FROM access.DayMaster, access.AttendanceMaster WHERE DayMaster.Tdate = AttendanceMaster.ADate AND DayMaster.e_id = AttendanceMaster.EmployeeID)";
    $result = safeUpdateIData($conn, $query, true);
//    $result = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
        $query = "UPDATE access.DayMaster SET p_flag = '0' WHERE DayMasterID = " . $cur[0];
        safeUpdate($conn, $query, true);
    }
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.18.10.65.69'";
    safeUpdate($conn, $query, true);
    V18116670($conn);
}

function V18106469($conn) {
    $query = "ALTER TABLE access.OtherSettingMaster ADD PhoneColumnName VARCHAR( 255 ) NOT NULL DEFAULT '--'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.18.10.64.69'";
    safeUpdate($conn, $query, true);
    V18106569($conn);
}

function V1896368($conn) {
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.18.9.63.68'";
    safeUpdate($conn, $query, true);
    V18106469($conn);
}

function V1896267($conn) {
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.18.9.62.67'";
    safeUpdate($conn, $query, true);
    V1896368($conn);
}

function V1896166($conn) {
    $query = "ALTER TABLE access.OtherSettingMaster ADD MinOTValue INT NOT NULL DEFAULT '0', ADD DBType VARCHAR( 255 ) NOT NULL DEFAULT 'Oracle', ADD DBIP VARCHAR( 255 ) NOT NULL DEFAULT '127.0.0.1', ADD DBName VARCHAR( 255 ) NOT NULL , ADD DBUser VARCHAR( 255 ) NOT NULL , ADD DBPass VARCHAR( 255 ) NOT NULL, ADD EmployeeCodeLength INT NOT NULL DEFAULT '6' ";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.AttendanceMaster ADD OT1 VARCHAR( 255 ) NOT NULL DEFAULT 'Saturday', ADD OT2 VARCHAR( 255 ) NOT NULL DEFAULT 'Sunday'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.18.9.61.66'";
    safeUpdate($conn, $query, true);
    V1896267($conn);
}

function V1886165($conn) {
    $query = "INSERT INTO access.ScheduleMaster ( Name ) VALUES ('Flexi Start-End, Multi In-Out (>1)')";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.18.8.61.65'";
    safeUpdate($conn, $query, true);
    V1896166($conn);
}

function V1886064($conn) {
    $query = "ALTER TABLE access.AttendanceMaster ADD LateIn_flag INT( 1 ) NOT NULL DEFAULT '0', ADD EarlyOut_flag INT( 1 ) NOT NULL DEFAULT '0', ADD MoreBreak_flag INT( 1 ) NOT NULL DEFAULT '0'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.18.8.60.64'";
    safeUpdate($conn, $query, true);
    V1886165($conn);
}

function V1776063($conn) {
    $query = "ALTER TABLE access.OtherSettingMaster CHANGE `FlagReportText` `RosterColumns` VARCHAR( 1024 ) NOT NULL COMMENT 'Roster Report Column Selection' ";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.tuser SET company = TRIM(company), dept = TRIM(dept)";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.OtherSettingMaster SET RosterColumns = 'chkDeptchkDivchkShiftchkStartchkClosechkLateInchkEarlyOutchkNormalchkAppOTchkFlag', EX2 = 'V.17.7.60.63'";
    safeUpdate($conn, $query, true);
    V1886064($conn);
}

function V1676062($conn) {
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.16.7.60.62'";
    safeUpdate($conn, $query, true);
    V1776063($conn);
}

function V1576061($conn) {
    $query = "SELECT AttendanceID, ADate FROM access.AttendanceMaster WHERE Day = 'Off'";
    $result = safeUpdateIData($conn, $query, true);
//    $result = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
        $this_date = strtotime(substr($cur[1], 6, 2) . "-" . substr($cur[1], 4, 2) . "-" . substr($cur[1], 0, 4));
        $this_array = getDate($this_date);
        $this_day = $this_array["weekday"];
        $query = "UPDATE access.AttendanceMaster SET Day = '" . $this_day . "' WHERE AttendanceID = " . $cur[0];
        safeUpdate($conn, $query, true);
    }
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.15.7.60.61'";
    safeUpdate($conn, $query, true);
    V1676062($conn);
}

function V1576060($conn) {
    $query = "CREATE TABLE access.FlagTitle (FlagTitleID INT NOT NULL AUTO_INCREMENT, Flag VARCHAR( 255 ) NOT NULL , Title VARCHAR( 255 ) NOT NULL , PRIMARY KEY ( FlagTitleID ), UNIQUE KEY FTF (Flag) ) ";
    safeUpdate($conn, $query, true);
    $query = "INSERT IGNORE INTO access.FlagTitle (Flag, Title) VALUES ('Violet', ''), ('Indigo', ''), ('Blue', ''), ('Green', ''), ('Yellow', ''), ('Orange', ''), ('Red', ''), ('Gray', ''), ('Brown', ''), ('Purple', '')";
    safeUpdate($conn, $query, true);
//    $query = "ALTER TABLE access.OtherSettingMaster CHANGE FlagReportText RosterColumns VARCHAR( 1024 ) NOT NULL COMMENT 'Roster Report Column Selection'";
    $query = "ALTER TABLE access.OtherSettingMaster CHANGE FlagReportText RosterColumns VARCHAR(2048) NULL COMMENT 'Roster Report Column Selection'";
    safeUpdate($conn, $query, true);
    $query = "SELECT RosterColumns FROM access.OtherSettingMaster";
    $result = selectData($conn, $query);
    $frt = $result[0];
    $frt = nl2br($frt);
    $v_frt = explode("<br />", $frt);
    $frt = "";
    for ($i = 0; $i < count($v_frt); $i++) {
        if (stripos($v_frt[$i], "Violet") !== false) {
            $query = "UPDATE access.FlagTitle SET Title = '" . $v_frt[$i] . "' WHERE Flag = 'Violet'";
            safeUpdate($conn, $query, true);
        } else {
            if (stripos($v_frt[$i], "Indigo") !== false) {
                $query = "UPDATE access.FlagTitle SET Title = '" . $v_frt[$i] . "' WHERE Flag = 'Indigo'";
                safeUpdate($conn, $query, true);
            } else {
                if (stripos($v_frt[$i], "Blue") !== false) {
                    $query = "UPDATE access.FlagTitle SET Title = '" . $v_frt[$i] . "' WHERE Flag = 'Blue'";
                    safeUpdate($conn, $query, true);
                } else {
                    if (stripos($v_frt[$i], "Green") !== false) {
                        $query = "UPDATE access.FlagTitle SET Title = '" . $v_frt[$i] . "' WHERE Flag = 'Green'";
                        safeUpdate($conn, $query, true);
                    } else {
                        if (stripos($v_frt[$i], "Yellow") !== false) {
                            $query = "UPDATE access.FlagTitle SET Title = '" . $v_frt[$i] . "' WHERE Flag = 'Yellow'";
                            safeUpdate($conn, $query, true);
                        } else {
                            if (stripos($v_frt[$i], "Orange") !== false) {
                                $query = "UPDATE access.FlagTitle SET Title = '" . $v_frt[$i] . "' WHERE Flag = 'Orange'";
                                safeUpdate($conn, $query, true);
                            } else {
                                if (stripos($v_frt[$i], "Red") !== false) {
                                    $query = "UPDATE access.FlagTitle SET Title = '" . $v_frt[$i] . "' WHERE Flag = 'Red'";
                                    safeUpdate($conn, $query, true);
                                } else {
                                    if (stripos($v_frt[$i], "Gray") !== false) {
                                        $query = "UPDATE access.FlagTitle SET Title = '" . $v_frt[$i] . "' WHERE Flag = 'Gray'";
                                        safeUpdate($conn, $query, true);
                                    } else {
                                        if (stripos($v_frt[$i], "Brown") !== false) {
                                            $query = "UPDATE access.FlagTitle SET Title = '" . $v_frt[$i] . "' WHERE Flag = 'Brown'";
                                            safeUpdate($conn, $query, true);
                                        } else {
                                            if (stripos($v_frt[$i], "Purple") !== false) {
                                                $query = "UPDATE access.FlagTitle SET Title = '" . $v_frt[$i] . "' WHERE Flag = 'Purple'";
                                                safeUpdate($conn, $query, true);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    $query = "ALTER TABLE access.othersettingmaster ADD SMTPServer VARCHAR( 255 ) NOT NULL , ADD SMTPFrom VARCHAR( 255 ) NOT NULL , ADD SMTPAuth VARCHAR( 5 ) NOT NULL , ADD SMTPUsername VARCHAR( 255 ) NOT NULL , ADD SMTPPassword VARCHAR( 255 ) NOT NULL";
    safeUpdate($conn, $query, true);
    $query = "CREATE TABLE access.UserDiv (UserDivID INT NOT NULL AUTO_INCREMENT , Username VARCHAR( 255 ) NOT NULL , UserDiv.Div VARCHAR( 255 ) NOT NULL , PRIMARY KEY ( UserDivID ) ) ";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.15.7.60.60'";
    safeUpdate($conn, $query, true);
    V1576061($conn);
}

function V1266059($conn) {
    $query = "UPDATE access.tenter SET e_etc = 'P' WHERE e_etc = '9'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.tenter SET e_etc = '0' WHERE e_time NOT LIKE '%000' AND e_etc = 'P'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.AttendanceMaster SET Flag = 'Black' WHERE (EarlyIn > 0 OR LateIn > 0) AND Flag = 'Proxy'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.DayMaster SET Flag = 'Black' WHERE Start NOT LIKE '%000' AND Close NOT LIKE '%000' AND Flag = 'Proxy'";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.tuser ADD DeptClocking VARCHAR( 255 ) NULL, ADD ExitClocking VARCHAR( 255 ) NULL ";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.tuser ADD OTRotate VARCHAR( 255 ) NOT NULL DEFAULT 'No' ";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.tuser SET DeptClocking = '' WHERE DeptClocking IS NULL";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.tuser SET ExitClocking = '' WHERE ExitClocking IS NULL";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.tuser CHANGE `DeptClocking` `OT1` VARCHAR( 255 ) NOT NULL DEFAULT 'Saturday', CHANGE `ExitClocking` `OT2` VARCHAR( 255 ) NOT NULL DEFAULT 'Sunday'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.tuser SET OT1 = '', OT2 = ''";
    safeUpdate($conn, $query, true);
    $query = "CREATE TABLE access.OTDayRotation ( OTDayRotationID INT NOT NULL AUTO_INCREMENT , e_id INT NOT NULL DEFAULT '0', e_date INT( 8 ) NOT NULL DEFAULT '0', PRIMARY KEY ( OTDayRotationID ) , UNIQUE KEY ODREID (e_id )) ";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.12.6.60.59'";
    safeUpdate($conn, $query, true);
    V1576060($conn);
}

function V1265958($conn) {
    $query = "ALTER TABLE access.OtherSettingMaster ADD EarlyInOTDayDate VARCHAR( 50 ) NOT NULL DEFAULT 'No'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.12.6.59.58'";
    safeUpdate($conn, $query, true);
    V1266059($conn);
}

function V1265957($conn) {
    $query = "ALTER TABLE access.ProcessLog MODIFY `PType` VARCHAR( 255 ) NULL DEFAULT NULL";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.12.6.59.57'";
    safeUpdate($conn, $query, true);
    V1265958($conn);
}

function V1265956($conn) {
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.12.6.59.56'";
    safeUpdate($conn, $query, true);
    V1265957($conn);
}

function V1265955($conn) {
    $query = "CREATE TABLE access.OTEmployeeDateExempt (OTEmployeeDateExemptID INT NOT NULL AUTO_INCREMENT, EmployeeID INT NOT NULL DEFAULT '0', PRIMARY KEY ( OTEmployeeDateExemptID ), UNIQUE KEY OEEDEI (EmployeeID) )";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.12.6.59.55'";
    safeUpdate($conn, $query, true);
    V1265956($conn);
}

function V1265854($conn) {
    $query = "ALTER TABLE access.DayMaster CHANGE `LastDayMasterID` `p_flag` INT NOT NULL DEFAULT '0' ";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.WeekMaster CHANGE `LastWeekMasterID` `p_flag` INT NOT NULL DEFAULT '0' ";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.DayMaster SET p_flag = 1";
    safeUpdate($conn, $query, true);
    $query = "SELECT e_id, TDate, DayMasterID FROM access.DayMaster ORDER BY DayMasterID";
//    $result = safeUpdateIData($iconn, $query, true);
    $result = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
        $qquery = "SELECT EmployeeID FROM access.AttendanceMaster WHERE EmployeeID = " . $cur[0] . " AND ADate = " . $cur[1];
        $result1 = selectData($conn, $qquery);
        if (0 >= $result1[0]) {
            $qqquery = "UPDATE access.DayMaster SET p_flag = 0 WHERE DayMasterID = " . $cur[2];
            safeUpdate($conn, $qqquery, true);
        }
    }
    $query = "ALTER TABLE access.tgroup ADD MinOTWorkForBreak INT NOT NULL DEFAULT '0'";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.AttendanceMaster ADD p_flag INT NOT NULL DEFAULT '0'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.AttendanceMaster SET p_flag = 1";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.12.6.58.54'";
    safeUpdate($conn, $query, true);
    V1265955($conn);
}

function V1265853($conn) {
    $query = "ALTER TABLE access.OtherSettingMaster ADD MACAddress VARCHAR( 255 ) NOT NULL DEFAULT '.'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.12.6.58.53'";
    safeUpdate($conn, $query, true);
    V1265854($conn);
}

function V1265852($conn) {
    $query = "INSERT INTO access.ModuleMaster (Name) VALUES ('Delete Processed Log'), ('Employees'), ('OT Days/Date'), ('Projects')";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.ModuleMaster SET Name = 'Global Settings' WHERE Name = 'Other Settings'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.ModuleMaster SET Name = 'Approve/Pre Approve Overtime' WHERE Name = 'Approve Overtime'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.12.6.58.52'";
    safeUpdate($conn, $query, true);
    V1265853($conn);
}

function V1265851($conn) {
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.12.6.58.51'";
    safeUpdate($conn, $query, true);
    V1265852($conn);
}

function V1265850($conn) {
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.12.6.58.50'";
    safeUpdate($conn, $query, true);
    V1265851($conn);
}

function V1265849($conn) {
    $query = "CREATE TABLE access.OTEmployeeExempt (OTEmployeeExemptID INT NOT NULL AUTO_INCREMENT, EmployeeID INT NOT NULL DEFAULT '0', PRIMARY KEY ( OTEmployeeExemptID ), UNIQUE KEY OEEEI (EmployeeID) )";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.Usermaster MODIFY RASSelection VARCHAR(1024) NOT NULL DEFAULT '-V--I--B--G--Y--O--R--GR--BR--PR--BK--WKD--PXY--FLG--SAT--SUN--TLD--NS--NF--TND--WKH--PXH--FLH--SATH--SUNH--TLH--NSH--NFH--TNH-'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.12.6.58.49'";
    safeUpdate($conn, $query, true);
    V1265850($conn);
}

function V1265749($conn) {
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.12.6.57.49'";
    safeUpdate($conn, $query, true);
    V1265849($conn);
}

function V1265649($conn) {
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.12.6.56.49'";
    safeUpdate($conn, $query, true);
    V1265749($conn);
}

function V1265548($conn) {
    $query = "DELETE FROM Access.AttendanceMaster WHERE Flag = 'Delete'";
    safeUpdate($conn, $query, true);
    $query = "DELETE FROM Access.DayMaster WHERE Flag = 'Delete'";
    safeUpdate($conn, $query, true);
    $query = "DELETE FROM Access.WeekMaster WHERE Flag = 'Delete'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE Access.OtherSettingMaster SET EX2 = 'V.12.6.55.48'";
    safeUpdate($conn, $query, true);
    V1265649($conn);
}

function V1265547($conn) { 
    $data0 = array();
    $data1 = array();
    $data2 = array();
    $query = "SELECT AttendanceMaster.AttendanceID, AttendanceMaster.EmployeeID, AttendanceMaster.ADate FROM access.AttendanceMaster ORDER BY AttendanceMaster.EmployeeID, AttendanceMaster.ADate";
    $result = safeUpdateIData($conn, $query, true);
//    $result = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
        if ($data1 == $cur[1] && $data2 == $cur[2]) {
            $query = "DELETE FROM Access.AttendanceMaster WHERE AttendanceID = " . $cur[0];
            safeUpdate($conn, $query, true);
        } else {
            $data0 = $cur[0];
            $data1 = $cur[1];
            $data2 = $cur[2];
        }
    }
    $data0 = array();
    $data1 = array();
    $data2 = array();
    $query = "SELECT DayMaster.DayMasterID, DayMaster.e_id, DayMaster.TDate FROM access.DayMaster ORDER BY DayMaster.e_id, DayMaster.TDate";
    $result = safeUpdateIData($conn, $query, true);
//    $result = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
        if ($data1 == $cur[1] && $data2 == $cur[2]) {
            $query = "DELETE FROM Access.DayMaster WHERE DayMasterID = " . $cur[0];
            safeUpdate($conn, $query, true);
        } else {
            $data0 = $cur[0];
            $data1 = $cur[1];
            $data2 = $cur[2];
        }
    }
    $query = "ALTER TABLE Access.AttendanceMaster ADD UNIQUE AEA (EmployeeID , ADate)";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE Access.DayMaster ADD UNIQUE DET (e_id , TDate)";
    safeUpdate($conn, $query, true);
    $query = "UPDATE Access.OtherSettingMaster SET EX2 = 'V.12.6.55.47'";
    safeUpdate($conn, $query, true);
    V1265548($conn);
}

function V1265546($conn) { 
    $query = "SELECT FlagDayRotationID FROM access.FlagDayRotation WHERE FlagDayRotationID NOT IN (SELECT FlagDayRotation.FlagDayRotationID FROM access.AttendanceMaster, access.FlagDayRotation WHERE AttendanceMaster.EmployeeID = FlagDayRotation.e_id AND AttendanceMaster.ADate = FlagDayRotation.e_date) AND FlagDayRotation.e_date < " . insertToday() . " ORDER BY e_id";
    $result = safeUpdateIData($conn, $query, true);
//    $result = mysqli_query($conn, $query);
    if(mysqli_num_rows($result) > 0){
        while ($cur = mysqli_fetch_row($result)) {
            $query = "UPDATE access.FlagDayRotation SET RecStat = 0 WHERE FlagDayRotationID = " . $cur[0];
            safeUpdate($conn, $query, true);
        }
    }
    $query = "SELECT DayMasterID FROM access.DayMaster WHERE DayMasterID NOT IN (SELECT DayMaster.DayMasterID FROM access.DayMaster, access.AttendanceMaster WHERE DayMaster.Tdate = AttendanceMaster.ADate AND DayMaster.e_id = AttendanceMaster.EmployeeID)";
    $result = safeUpdateIData($conn, $query, true);
//    $result = mysqli_query($conn, $query);
    if(mysqli_num_rows($result) > 0){
        while ($cur = mysqli_fetch_row($result)) {
            $query = "DELETE FROM access.DayMaster WHERE DayMasterID = " . $cur[0];
            safeUpdate($conn, $query, true);
        }
    }
    $query = "UPDATE access.OtherSettingMaster SET EX2 = 'V.12.6.55.46'";
    safeUpdate($conn, $query, true);
    V1265547($conn);
}

function V1265545($conn) { 
    $query = "SELECT attendancemaster.AttendanceID, attendancemaster.EmployeeID, attendancemaster.ADate, attendancemaster.Flag FROM access.attendancemaster WHERE  attendancemaster.Flag = 'Proxy'";
    $result = safeUpdateIData($conn, $query, true);
//    $result = mysqli_query($conn, $query);
    if(mysqli_num_rows($result) > 0){
        while ($cur = mysqli_fetch_row($result)) {
            $query = "SELECT attendancemaster.Flag FROM access.attendancemaster WHERE attendancemaster.EmployeeID = " . (int)$cur[1] . " AND attendancemaster.ADate = '" . $cur[2] . "' AND attendancemaster.Flag NOT LIKE 'Proxy'";
            $sub_result = selectData($conn, $query);
            if (0 < count($sub_result)) {
                $this_flag = $sub_result[0];
                if ($this_flag == "Violet" || $this_flag == "Indigo" || $this_flag == "Blue" || $this_flag == "Green" || $this_flag == "Yellow" || $this_flag == "Orange" || $this_flag == "Red" || $this_flag == "Purple" || $this_flag == "Brown" || $this_flag == "Gray" || $this_flag == "Black") {
                    $this_query = "DELETE FROM attendancemaster WHERE AttendanceID = " . $cur[0];
                    safeUpdate($conn, $this_query, false);
                    $this_query = "DELETE FROM daymaster WHERE e_id = " . $cur[1] . " AND TDate = " . $cur[2] . " AND Flag = 'Proxy'";
                    safeUpdate($conn, $this_query, true);
                }
            }
        }
    }
    $query = "SELECT attendancemaster.AttendanceID, attendancemaster.EmployeeID, attendancemaster.ADate, attendancemaster.Flag FROM access.attendancemaster, access.flagdayrotation WHERE attendancemaster.EmployeeID = flagdayrotation.e_id AND attendancemaster.ADate = flagdayrotation.e_date AND attendancemaster.Flag = 'Proxy' AND flagdayrotation.RecStat = 0 AND attendancemaster.ADate > " . insertToday();
    $result = safeUpdateIData($conn, $query, true);
//    $result = mysqli_query($conn, $query);
    if(mysqli_num_rows($result) > 0){
        while ($cur = mysqli_fetch_row($result)) {
            $this_query = "DELETE FROM access.attendancemaster WHERE AttendanceID = " . $cur[0];
            safeUpdate($conn, $this_query, true);
        }
    }
    $query = "UPDATE tgroup SET Skey = '0' WHERE Skey IS NULL";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.tgroup CHANGE `Skey` `MinWorkForBreak` INT NOT NULL DEFAULT 0";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.othersettingmaster SET EX2 = 'V.12.6.55.45'";
    safeUpdate($conn, $query, true);
    V1265546($conn);
}

function V1265544($conn) {
    $query = "UPDATE access.othersettingmaster SET EX2 = 'V.12.6.55.44'";
    safeUpdate($conn, $query, true);
    V1265545($conn);
}

function V1265543($conn) {
    $query = "ALTER TABLE access.attendancemaster MODIFY Flag VARCHAR(10) NOT NULL DEFAULT 'Black'";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.daymaster MODIFY Flag VARCHAR(10) NOT NULL DEFAULT 'Black'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.tenter SET e_group = 0 WHERE e_group IS NULL";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE access.tenter MODIFY e_group SMALLINT(5) NOT NULL DEFAULT '0'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE access.othersettingmaster SET EX2 = 'V.12.6.55.43'";
    safeUpdate($conn, $query, true);
    V1265544($conn);
}

function V1265542($conn) {
    $query = "ALTER TABLE Access.tcommand ADD c_result VARCHAR(1) NULL DEFAULT NULL ";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE Access.tconfig ADD savemode VARCHAR(1) NULL DEFAULT NULL ";
    safeUpdate($conn, $query, true);
    $query = "CREATE TABLE Access.TLSFlag (TLSFlagID int( 10 ) NOT NULL AUTO_INCREMENT , Violet varchar( 5 ) default 'Yes' , Indigo varchar( 5 ) default 'Yes' , Blue varchar( 5 ) default 'Yes' , Green varchar( 5 ) default 'Yes' , Yellow varchar( 5 ) default 'Yes' , Orange varchar( 5 ) default 'Yes' , Red varchar( 5 ) default 'Yes' , Gray varchar( 5 ) default 'Yes' , Brown varchar( 5 ) default 'Yes' , Purple varchar( 5 ) default 'Yes' , Black varchar( 5 ) default 'Yes' , Proxy varchar( 5 ) default 'Yes' , PRIMARY KEY ( TLSFlagID ) ) ";
    safeUpdate($conn, $query, true);
    $query = "INSERT INTO Access.TLSFlag (Violet) VALUES ('Yes')";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE Access.alterlog
            MODIFY `ed` INT(10) NOT NULL DEFAULT 0,
            MODIFY `GateFrom` INT(10) NOT NULL DEFAULT 0,
            MODIFY `GateTo` INT(10) NOT NULL DEFAULT 0,
            MODIFY `TransactDate` INT(10) NOT NULL DEFAULT 0,
            MODIFY `ShiftFrom` INT(10) NOT NULL DEFAULT 0,
            MODIFY `ShiftTo` INT(10) NOT NULL DEFAULT 0";
    safeUpdate($conn, $query, true);
    $query = "UPDATE Access.AttendanceMaster SET Week = 0 WHERE Week IS NULL";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE Access.attendancemaster 
        MODIFY EmployeeID INT(10) NOT NULL DEFAULT '0', 
        MODIFY group_id INT(10) NOT NULL DEFAULT '0', 
        MODIFY group_min INT(10) NOT NULL DEFAULT '0', 
        MODIFY ADate INT(10) NOT NULL DEFAULT '0', 
        MODIFY Week INT(10) NOT NULL DEFAULT '0', 
        MODIFY EarlyIn INT(10) NOT NULL DEFAULT '0', 
        MODIFY LateIn INT(10) NOT NULL DEFAULT '0', 
        MODIFY Break INT(10) NOT NULL DEFAULT '0', 
        MODIFY LessBreak INT(10) NOT NULL DEFAULT '0', 
        MODIFY MoreBreak INT(10) NOT NULL DEFAULT '0', 
        MODIFY EarlyOut INT(10) NOT NULL DEFAULT '0', 
        MODIFY LateOut INT(10) NOT NULL DEFAULT '0', 
        MODIFY Normal INT(10) NOT NULL DEFAULT '0', 
        MODIFY Grace INT(10) NOT NULL DEFAULT '0', 
        MODIFY Overtime INT(10) NOT NULL DEFAULT '0', 
        MODIFY AOvertime INT(10) NOT NULL DEFAULT '0'";
    safeUpdate($conn, $query, true);

    $query = "ALTER TABLE Access.daymaster 
        MODIFY e_id INT(10) NOT NULL DEFAULT '0', 
        MODIFY TDate INT(10) NOT NULL DEFAULT '0', 
        MODIFY LastDayMasterID INT(10) NOT NULL DEFAULT '0', 
        MODIFY group_id INT(10) NOT NULL DEFAULT '0', 
        MODIFY Work INT(11) NOT NULL DEFAULT '0'";
    safeUpdate($conn, $query, true);

    $query = "ALTER TABLE Access.deptgate MODIFY g_id INT(10) NOT NULL DEFAULT '0'";
    safeUpdate($conn, $query, true);

    $query = "ALTER TABLE Access.logmaster 
        MODIFY LogDate INT(10) NOT NULL DEFAULT '0', 
        MODIFY ed INT(10) NOT NULL DEFAULT '0'";
    safeUpdate($conn, $query, true);

    $query = "ALTER TABLE Access.processlog MODIFY PDate INT(10) NOT NULL DEFAULT '0'";
    safeUpdate($conn, $query, true);

    $query = "ALTER TABLE Access.othersettingmaster 
        MODIFY MinClockinPeriod INT(10) NOT NULL DEFAULT '0', 
        MODIFY TotalDailyClockin INT(10) NOT NULL DEFAULT '0', 
        MODIFY NightShiftMaxOutTime INT(10) NOT NULL DEFAULT '0', 
        MODIFY TotalExitClockin INT(10) NOT NULL DEFAULT '0', 
        MODIFY RotateShiftNextDay INT(10) NOT NULL DEFAULT '0', 
        MODIFY Ex3 INT(10) NOT NULL DEFAULT '0', 
        MODIFY Ex4 INT(10) NOT NULL DEFAULT '0'";
    safeUpdate($conn, $query, true);

    $query = "ALTER TABLE Access.projectlog 
        MODIFY DayMasterID INT(10) NOT NULL DEFAULT '0', 
        MODIFY WeekMasterID INT(10) NOT NULL DEFAULT '0', 
        MODIFY ProjectID INT(10) NOT NULL DEFAULT '0', 
        MODIFY e_id INT(10) NOT NULL DEFAULT '0', 
        MODIFY e_date INT(10) NOT NULL DEFAULT '0', 
        MODIFY twork INT(10) NOT NULL DEFAULT '0'";
    safeUpdate($conn, $query, true);

    $query = "ALTER TABLE Access.proxydelete 
        MODIFY e_id INT(10) NOT NULL DEFAULT '0', 
        MODIFY e_date INT(10) NOT NULL DEFAULT '0', 
        MODIFY group_id INT(10) NOT NULL DEFAULT '0', 
        MODIFY g_id INT(10) NOT NULL DEFAULT '0', 
        MODIFY ed INT(10) NOT NULL DEFAULT '0'";
    safeUpdate($conn, $query, true);

    $query = "ALTER TABLE Access.shiftchangemaster MODIFY id INT(10) NOT NULL DEFAULT '0'";
    safeUpdate($conn, $query, true);

    $query = "ALTER TABLE Access.shiftrotatelog 
        MODIFY RDate INT(10) NOT NULL DEFAULT '0', 
        MODIFY RTime INT(10) NOT NULL DEFAULT '0', 
        MODIFY ShiftFrom INT(10) NOT NULL DEFAULT '0', 
        MODIFY ShiftTo INT(10) NOT NULL DEFAULT '0'";
    safeUpdate($conn, $query, true);

    $query = "ALTER TABLE Access.tauditlog 
        MODIFY id INT(10) NOT NULL DEFAULT '0', 
        MODIFY rid INT(10) NOT NULL DEFAULT '0'";
    safeUpdate($conn, $query, true);

    $query = "ALTER TABLE Access.tcommand 
        MODIFY c_key INT(10) NOT NULL DEFAULT '0', 
        MODIFY c_gid INT(10) NOT NULL DEFAULT '0', 
        MODIFY c_retry INT(10) NOT NULL DEFAULT '0'";
    safeUpdate($conn, $query, true);

    $query = "ALTER TABLE Access.tconfig 
        MODIFY maxuser INT(10) NOT NULL DEFAULT '0', 
        MODIFY minvid INT(10) NOT NULL DEFAULT '0', 
        MODIFY maxvid INT(10) NOT NULL DEFAULT '0', 
        MODIFY tsockport INT(10) NOT NULL DEFAULT '0', 
        MODIFY csockport INT(10) NOT NULL DEFAULT '0', 
        MODIFY polltime INT(10) NOT NULL DEFAULT '0'";
    safeUpdate($conn, $query, true);

    $query = "ALTER TABLE Access.tenter 
        MODIFY g_id INT(10) NOT NULL DEFAULT '0', 
        MODIFY e_id INT(10) NOT NULL DEFAULT '0', 
        MODIFY e_group SMALLINT(5) NOT NULL DEFAULT '0', 
        MODIFY ed INT(10) NOT NULL AUTO_INCREMENT, 
        MODIFY p_flag INT(10) NOT NULL DEFAULT '0'";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE Access.tgate 
            MODIFY `floor` INT(10) NOT NULL DEFAULT 0, 
            MODIFY `exit` TINYINT(1) NOT NULL DEFAULT 0, 
            MODIFY `antipass` INT(10) NOT NULL DEFAULT 0, 
            MODIFY `antipass_level` INT(10) NOT NULL DEFAULT 0, 
            MODIFY `antipass_mode` INT(10) NOT NULL DEFAULT 0";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE Access.tgatelog MODIFY `id` INT(10) NOT NULL AUTO_INCREMENT";
    safeUpdate($conn, $query, true);
    $query = "UPDATE Access.tgroup SET tgroup.FlexiBreak = '0', ShiftTypeID = '0', ScheduleID = '0', WorkMin = '0' WHERE tgroup.id = '0'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE Access.tgroup SET tgroup.FlexiBreak = '0', ShiftTypeID = '0', ScheduleID = '0', WorkMin = '0' WHERE tgroup.id = '1'";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE Access.tgroup 
        MODIFY FlexiBreak INT(10) NOT NULL DEFAULT '0', 
        MODIFY NightFlag TINYINT(1) NOT NULL DEFAULT '0', 
        MODIFY ShiftTypeID INT(10) NOT NULL DEFAULT '0', 
        MODIFY ScheduleID INT(10) NOT NULL DEFAULT '0', 
        MODIFY WorkMin INT(10) NOT NULL DEFAULT '0'";
    safeUpdate($conn, $query, true);
    $query = "UPDATE Access.tgroup SET tgroup.NightFlag = '1' WHERE tgroup.NightFlag = '-1'";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE Access.transact MODIFY Transactdate INT( 10 ) NOT NULL DEFAULT '0', MODIFY Transacttime INT( 10 ) NOT NULL DEFAULT '0' ";
    safeUpdate($conn, $query, true);
    $query = "UPDATE Access.tuser SET padmin = '0' WHERE padmin IS NULL";
    safeUpdate($conn, $query, true);
    $query = "UPDATE Access.tuser SET group_id = '0' WHERE group_id IS NULL";
    safeUpdate($conn, $query, true);
    $query = "UPDATE Access.tuser SET antipass_state = '0' WHERE antipass_state IS NULL";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE Access.tuser 
        MODIFY padmin INT(10) NOT NULL DEFAULT '0', 
        MODIFY group_id SMALLINT(5) NOT NULL DEFAULT '0', 
        MODIFY antipass_state INT(10) NOT NULL DEFAULT '0'";
    safeUpdate($conn, $query, true);

    $query = "ALTER TABLE Access.tvisited 
        MODIFY id INT(10) NOT NULL DEFAULT '0', 
        MODIFY group_id SMALLINT(5) NOT NULL DEFAULT '0'";
    safeUpdate($conn, $query, true);

    $query = "ALTER TABLE Access.tvisitor 
        MODIFY group_id SMALLINT(5) NOT NULL DEFAULT '0'";
    safeUpdate($conn, $query, true);

    $query = "ALTER TABLE Access.UserMaster 
        MODIFY Lastlogin INT(10) NOT NULL DEFAULT '0'";
    safeUpdate($conn, $query, true);

    $query = "ALTER TABLE Access.WeekMaster 
        MODIFY WeekNo INT(10) NOT NULL DEFAULT '0', 
        MODIFY e_id INT(10) NOT NULL DEFAULT '0', 
        MODIFY LogDate INT(10) NOT NULL DEFAULT '0', 
        MODIFY Seconds INT(10) NOT NULL DEFAULT '0', 
        MODIFY LastWeekMasterID INT(10) NOT NULL DEFAULT '0', 
        MODIFY group_id INT(10) NOT NULL DEFAULT '0', 
        MODIFY Flag VARCHAR(10) NOT NULL DEFAULT 'Black'";
    safeUpdate($conn, $query, true);

    $query = "UPDATE access.othersettingMaster SET EX2 = 'V.12.6.55.42'";
    safeUpdate($conn, $query, true);
    V1265543($conn);
}

function V1265541($conn) {
    $query = "ALTER TABLE Access.OTDay ADD UNIQUE OD (Day)";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE Access.ShiftTypeMaster ADD UNIQUE SN (Name)";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE Access.TransactionTypeMaster ADD UNIQUE TN (Name)";
    safeUpdate($conn, $query, true);
    $query = "UPDATE Access.OtherSettingMaster SET EX2 = 'V.12.6.55.41'";
    safeUpdate($conn, $query, true);
    V1265542($conn);
}

function V1265540($conn) {
    $query = "ALTER TABLE ShiftChangeMaster ADD idf INT NOT NULL DEFAULT 1 COMMENT 'Shift Rotation Group'";
    safeUpdate($conn, $query, true);

    $query = "ALTER TABLE ShiftChangeMaster ADD AE TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Auto Execute Shift Rotation'";
    safeUpdate($conn, $query, true);

    $query = "UPDATE OtherSettingMaster SET EX2 = 'V.12.6.55.40'";
    safeUpdate($conn, $query, true);
    V1265541($conn);
}

function V1265539($conn) {
    $query = "UPDATE OtherSettingMaster SET EX2 = 'V.12.6.55.39'";
    safeUpdate($conn, $query, true);
    V1265540($conn);
}

function V1265538($conn) {
    $query = "UPDATE TransactionTypeMaster SET Name = 'ADD' WHERE TransactionTypeID = 1";
    safeUpdate($conn, $query, true);

    // Assuming LockDate is a YYYYMMDD integer, better use BIGINT
    $query = "ALTER TABLE OtherSettingMaster ADD LockDate BIGINT NOT NULL DEFAULT 20010101";
    safeUpdate($conn, $query, true);

    $query = "UPDATE OtherSettingMaster SET EX2 = 'V.12.6.55.38'";
    safeUpdate($conn, $query, true);
    V1265539($conn);
}

function V1265438($conn) { 
    // Change Transactquery column type in Transact table
    $query = "ALTER TABLE Transact MODIFY `Transactquery` VARCHAR(1024) NOT NULL";
    safeUpdate($conn, $query, true);
    
    // Add unique index MN on Name column in ModuleMaster table
    $query = "ALTER TABLE ModuleMaster ADD UNIQUE MN (Name)";
    safeUpdate($conn, $query, true);
    
    // Insert new module names into ModuleMaster
    $query = "INSERT INTO ModuleMaster (Name) VALUES 
        ('Pre Approve Overtime'), 
        ('Proxy'), 
        ('Pre Flag Days'), 
        ('Post Flag Days')";
    safeUpdate($conn, $query, true);

    // Update Userlevel for user 'virdi' in Usermaster table
    $query = "UPDATE Usermaster SET Userlevel = '11V11A11E11D11R12V12A12E12D12R13V13A13E13D13R14V14A14E14D14R15V15A15E15D15R16V16A16E16D16R17V17A17E17D17R18V18A18E18D18R19V19A19E19D19R20V20A20E20D20R21V21A21E21D21R22V22A22E22D22R23V23A23E23D23R24V24A24E24D24R25V25A25E25D25R26V26A26E26D26R27V27A27E27D27R28V28A28E28D28R' 
        WHERE Username = 'virdi'";
    safeUpdate($conn, $query, true);

    // Add PLFlag column to OtherSettingMaster table with default 'Black'
    $query = "ALTER TABLE OtherSettingMaster ADD PLFlag VARCHAR(255) NOT NULL DEFAULT 'Black'";
    safeUpdate($conn, $query, true);

    // Add RASSelection column to Usermaster table with default value
    $query = "ALTER TABLE Usermaster ADD RASSelection VARCHAR(1024) NOT NULL DEFAULT '-V--I--B--G--Y--O--R--GR--BR--PR--WKD--PXY--FLG--SAT--SUN--TLD--NS--NF--TND--WKH--PXH--FLH--SATH--SUNH--TLH--NSH--NFH--TNH-'";
    safeUpdate($conn, $query, true);

    // Update RASSelection for all users in Usermaster
    $query = "UPDATE Usermaster SET RASSelection = '-V--I--B--G--Y--O--R--GR--BR--PR--BK--WKD--PXY--FLG--SAT--SUN--TLD--NS--NF--TND--WKH--PXH--FLH--SATH--SUNH--TLH--NSH--NFH--TNH-'";
    safeUpdate($conn, $query, true);

    // Update EX2 in OtherSettingMaster table
    $query = "UPDATE OtherSettingMaster SET EX2 = 'V.12.6.54.38'";
    safeUpdate($conn, $query, true);
    // Call the function V1265538 with connection
    V1265538($conn);
}

function V1264935($conn) {
    $query = "CREATE TABLE IF NOT EXISTS OTDay (
		OTDayID INT NOT NULL AUTO_INCREMENT,
		Day VARCHAR(255) NOT NULL,
		OT INT NOT NULL DEFAULT 0,
		PRIMARY KEY (OTDayID),
		UNIQUE KEY Day (Day)
	)";
	safeUpdate($conn, $query, true);
	$query = "INSERT IGNORE INTO OTDay (Day, OT) VALUES 
		('Monday', 0), ('Tuesday', 0), ('Wednesday', 0),
		('Thursday', 0), ('Friday', 0), ('Saturday', 0), ('Sunday', 0)";
	safeUpdate($conn, $query, true);
	$query = "CREATE TABLE IF NOT EXISTS OTDate (
		OTDateID INT NOT NULL AUTO_INCREMENT,
		OTDate INT NOT NULL DEFAULT 0,
		Day VARCHAR(255) NOT NULL,
		PRIMARY KEY (OTDateID)
	)";
	safeUpdate($conn, $query, true);
	$query = "UPDATE OtherSettingMaster SET EX2 = 'V.12.6.49.35'";
	safeUpdate($conn, $query, true);
    V1265438($conn);
}

function V154934($conn) {
    $query = "ALTER TABLE DayMaster ADD Work INT NOT NULL DEFAULT '0'";
    safeUpdate($conn, $query, true);
    $query = "DELETE FROM ScheduleMaster";
    safeUpdate($conn, $query, true);
    $query = "INSERT INTO ScheduleMaster (ScheduleID, Name) VALUES (1, 'Fixed Start-End, Flexi Multi Break (%2)'), (2, 'Fixed Start-End, Flexi Single Break (2/4)'), (3, 'Fixed Start-End, Fixed Break (2/4)'), (4, 'Flexi Start-End, No Break (%2)'), (5, 'Fixed Start-End, Multi In-Out (>1)')";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE tenter ADD e_uptime VARCHAR(14) NULL, ADD e_upmode VARCHAR(1) NULL";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE tgate 
        MODIFY `tgate.exit` INT(1) NOT NULL DEFAULT '0', 
        ADD antipass VARCHAR(255) NULL, 
        ADD antipass_level VARCHAR(255) NULL, 
        ADD antipass_mode VARCHAR(255) NULL";
    safeUpdate($conn, $query, true);
    $query = "UPDATE tgate SET tgate.exit = '1' WHERE tgate.exit = '-1'";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE tuser ADD antipass_state VARCHAR(255) NULL, ADD antipass_lasttime VARCHAR(14) NULL";
    safeUpdate($conn, $query, true);
    $query = "CREATE TABLE FlagDayRotation (FlagDayRotationID int(11) NOT NULL AUTO_INCREMENT, e_id int(11) NOT NULL default '0', e_date int(8) NOT NULL default '0', g_id int(11) NOT NULL default '0', Flag varchar(1024) NOT NULL default 'Black', Rotate int(1) NOT NULL default '0', RecStat int(1) NOT NULL default '0', PRIMARY KEY(FlagDayRotationID))";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE FlagDayRotation ADD g_id INT NOT NULL DEFAULT '0', ADD Flag VARCHAR(255) NOT NULL DEFAULT 'Black', ADD Rotate INT(1) NOT NULL DEFAULT '0'";
    safeUpdate($conn, $query, true);
    $query = "CREATE TABLE PreApproveOT(PreApproveOTID int(11) NOT NULL AUTO_INCREMENT, OTDate int(8) NOT NULL default '0', e_id int(11) NOT NULL default '0', OT int(11) NOT NULL default '0', A1 int(1) NOT NULL default '0' COMMENT 'Add Rights', A2 int(1) NOT NULL default '0' COMMENT 'Edit Rights', A3 int(1) NOT NULL default '0' COMMENT 'Delete Rights', Remark varchar(255) NOT NULL, PRIMARY KEY(PreApproveOTID))";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE OtherSettingMaster MODIFY `FlagReportText` VARCHAR(1024) NOT NULL DEFAULT ''";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE UserMaster ADD PRIMARY KEY (Username)";
    safeUpdate($conn, $query, true);
    $query = "ALTER TABLE UserMaster MODIFY `UserStatus` VARCHAR(1024) NOT NULL DEFAULT ''";
    safeUpdate($conn, $query, true);
    $query = "INSERT INTO UserMaster (Username, Userpass, Usermail, Userlevel, Userstatus, Lastlogin) VALUES ('virdi', 'S]&;U]F8&', 'virdi@datacom.com', '11V11A11E11D11R12V12A12E12D12R13V13A13E13D13R14V14A14E14D14R15V15A15E15D15R16V16A16E16D16R17V17A17E17D17R18V18A18E18D18R19V19A19E19D19R20V20A20E20D20R21V21A21E21D21R22V22A22E22D22R23V23A23E23D23R24V24A24E24D24R25V25A25E25D25R26V26A26E26D26R27V27A27E27D27R', '', 20010101)";
    safeUpdate($conn, $query, true);
    $query = "UPDATE OtherSettingMaster SET EX2 = 'V.15.49.34'";
    safeUpdate($conn, $query, true);
    V1264935($conn);
}

?>