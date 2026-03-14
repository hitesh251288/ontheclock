<?php
set_time_limit(0);
include "lsConfig.php";
$db_type = $config["DB_TYPE"];
$db_ip = $config["DB_IP"];
$db_name = $config["DB_NAME"];
$db_user = $config["DB_USER"];
$db_pass = $config["DB_PASS"];
$USER_PASS_NEW_USER = $config["USER_PASS_NEW_USER"];
$session_variable = $config["SESSION_VARIABLE"];
//define("API_URL", "http://license.bitplus.in/LicenseService_Other");
define("API_URL", "http://89.107.58.217:8010");
$conn = null;
$iconn = null;
$jconn = null;
$kconn = null;
function openConnection() {
    global $db_type;
    global $db_ip;
    global $db_name;
    global $db_user;
    global $db_pass;
    if ($db_type == "1") {
        $conn = odbc_connect(decryptString($db_name), decryptString($db_user), decryptString($db_pass));
    }
    if ($db_type == "2") {
        
    }
    if ($db_type == "3") {
        $conn = openIConnection();
    }
    if ($db_type == "4") {
        $conn = oci_connect($db_user, $db_pass, $db_ip);
    }
    return $conn;
}

function openIConnection() { 
    global $db_type;
    global $db_ip;
    global $db_name;
    global $db_user;
    global $db_pass;
    $iconn = mysqli_connect(decryptString($db_ip), decryptString($db_user), decryptString($db_pass), decryptString($db_name)); //access_lfz//access_geeta
    if (!$iconn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    mysqli_autocommit($iconn, false);
    return $iconn;
}

function selectData($conn, $query) {
    global $db_type;
    if ($db_type == "1") {
        $result = odbc_exec($conn, $query);
        odbc_fetch_into($result, $cur);
    } else {
        if ($db_type != "2") {
            if ($db_type == "3") {
                $result = mysqli_query($conn, $query);
                $cur = mysqli_fetch_row($result);
            } else {
                if ($db_type == "4") {
                    $result = oci_parse($conn, $query);
                    oci_execute($result);
                    $cur = oci_fetch_array($result, OCI_BOTH);
                }
            }
        }
    }
    return $cur;
}

function selectDataCol($conn, $query) {
    global $db_type;
    $array[0] = "";
    $i = 0;
    if ($db_type == "1") {
        for ($result = odbc_exec($conn, $query); odbc_fetch_into($result, $cur); $i++) {
            $array[$i] = $cur[0];
        }
    } else {
        if ($db_type != "2") {
            if ($db_type == "3") {
                echo "\n" . $query;
                $result = mysqli_query($conn, $query) or exit("\nError: " . mysqli_error($conn));
                while ($cur = mysqli_fetch_row($result)) {
                    $array[$i] = $cur[0];
                    echo "\n" . $array[$i];
                    $i++;
                }
            } else {
                if ($db_type == "4") {
                    $result = oci_parse($conn, $query);
                    oci_execute($result);
                    while ($cur = oci_fetch_array($result, OCI_BOTH)) {
                        $array[$i] = $cur[0];
                        $i++;
                    }
                }
            }
        }
    }
    return $array;
}

function updateData($conn, $query, $commit) {
    global $db_type;
    if ($db_type == "1") {
        if (odbc_exec($conn, $query)) {
            return true;
        }
        return false;
    }
    if ($db_type == "2") {
        return NULL;
    }
    if ($db_type == "3") {
        if (mysqli_query($conn, $query)) {
            return true;
        }
        return false;
    }
    if ($db_type == "4") {
        $result = oci_parse($conn, $query);
        if (oci_execute($result)) {
            if ($commit) {
                oci_commit($conn);
            }
            return true;
        }
        return false;
    }
}

function updateIData($iconn, $query, $commit) {
    mysqli_autocommit($iconn, false);
    if (mysqli_query($iconn, $query)) {
        if ($commit) {
            mysqli_commit($iconn);
        }
        return true;
    }
    return false;
}

function updateDataTransact($conn, $query, $commit, $username) {
    global $db_type;
    global $session_variable;
    $transact_query = "";
    if (255 < strlen($query)) {
        $transact_query = substr($query, 0, 255);
    } else {
        $transact_query = $query;
    }
    $transact_query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . replaceString($transact_query, false) . "')";
    if ($db_type == "1") {
        if (odbc_exec($conn, $query)) {
            odbc_exec($conn, $transact_query);
            return true;
        }
        return false;
    }
    if ($db_type == "2") {
        
    }
    if ($db_type == "3") {
        if (mysqli_query($conn, $query)) {
            mysqli_query($conn, $transact_query);
            return true;
        }
        return false;
    }
    if ($db_type == "4") {
        $result = oci_parse($conn, $query);
        if (oci_execute($result)) {
            $result = oci_parse($conn, $transact_query);
            oci_execute($result);
            if ($commit) {
                oci_commit($conn);
            }
            return true;
        }
        return false;
    }
}

function login($conn, $username, $password, $lstUserType) {  
    global $session_variable;
    
    if (filesize("img/virdi.gif") != 1859) {
        $text = "Script Modification Error while Login from IP: " . $_SERVER["REMOTE_ADDR"] . " - Device Name: " . gethostbyaddr($_SERVER["REMOTE_ADDR"]);
        $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . trim($username) . "', '" . $text . "')";
        updatedata($conn, $query, true);
        return 1;
    }
    $query = "SELECT COUNT(*) FROM tgate";
    $result = selectdata($conn, $query);
    $tcount = $result[0];
    
    $resultData = mysqli_query($conn,"SHOW COLUMNS FROM `OtherSettingMaster` WHERE Field IN('CompanyDetail3','CompanyDetail4')");
    $exists = (mysqli_num_rows($resultData));
    
    if($exists) {
        $query = "SELECT ExitTerminal, Project, CompanyName, CompanyDetail1, CompanyDetail2, NightShiftMaxOutTime, IDColumnName, RosterColumns, RotateShift, Ex3, Ex2, LockDate, MACAddress, EmployeeCodeLength, PhoneColumnName, TCount, ApproveOTIgnoreActual, FlagLimitType, UseShiftRoster, DivColumnName, RemarkColumnName,CompanyDetail4,CompanyDetail3 FROM OtherSettingMaster";
    }else{ 
        $query = "SELECT ExitTerminal, Project, CompanyName, CompanyDetail1, CompanyDetail2, NightShiftMaxOutTime, IDColumnName, RosterColumns, RotateShift, Ex3, Ex2, LockDate, MACAddress, EmployeeCodeLength, PhoneColumnName, TCount, ApproveOTIgnoreActual, FlagLimitType, UseShiftRoster, DivColumnName, RemarkColumnName FROM OtherSettingMaster";
    }
    
//    $query = "SELECT ExitTerminal, Project, CompanyName, CompanyDetail1, CompanyDetail2, NightShiftMaxOutTime, IDColumnName, RosterColumns, RotateShift, Ex3, Ex2, LockDate, MACAddress, EmployeeCodeLength, PhoneColumnName, TCount, ApproveOTIgnoreActual, FlagLimitType, UseShiftRoster, DivColumnName, RemarkColumnName FROM OtherSettingMaster";
    $global_result = selectdata($conn, $query);
    $this_mac = $global_result[12];
    
    /*Start*/
    $surrendered = base64_decode(encryptDecrypt($global_result[22]));
    $global_result_decode = base64_decode($global_result[21]);
    $global_result_decrypt = encryptDecrypt($global_result_decode);
    $global_result_21 = json_decode($global_result_decrypt);
    $expiryDate = $global_result_21->EndDate;
    $this_mac_license = encryptDecrypt($global_result_21->MacProcessorId);
    
    $ipaddress = getHostByName(php_uname('n'));
    $computerName = getenv('COMPUTERNAME');
    date_default_timezone_set("Asia/Kolkata");

    $currentdata = date('d-M-Y h:i:s', time());
    $productname = 'VTIME';
    $projectKey = 'VTIME';
    $license = array("LicenseHistory"=>json_encode("$productname;$ipaddress;$computerName;$global_result_21->CompanyName;$global_result[10];$currentdata;$projectKey"));
    
    $json_url = API_URL."/License/LicenseAccessHistory";

    $json_string = base64_encode(str_replace('\"','',json_encode($license)));
    
    $encodeUrlData = json_encode(array("LicenseDetails"=>$json_string));
    
    $ch = curl_init($json_url);

    $options = array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array('Content-type: application/json'),
        CURLOPT_POSTFIELDS => $encodeUrlData
    );

    curl_setopt_array($ch, $options);

    $Sendresult = curl_exec($ch);

//    $MAC = exec('getmac');
//    $mac = strtok($MAC, ' ');
//    $cui = shell_exec("echo | {$_SERVER["SystemRoot"]}\System32\wbem\wmic.exe path win32_processor get processorid");
//
//    //$macid = str_replace("-", "", substr($mac[3], 0, 17));
//    $macid = str_replace("-", "", $mac);
//    $processorids = str_replace("ProcessorId", "", $cui);
//    $processorid = str_replace("\r\n", "", substr($processorids, 0, 25));
//    $macprocessor = $macid . "--" . $processorid;
//    $macprocessorid = str_replace(" ", "", $macprocessor);
    /* END */
    
    $query = "SELECT F1, F2, F3, F4, F5, F6, F7, F8, F9, F10 FROM OtherSettingMaster";
    $global_result_F = selectdata($conn, $query);
    
    if (getRegister($this_mac, 7) == "21" && trim($username) != "virdi" && trim($username) != "admin") { 
        echo $query = "SELECT IFNULL(MAX(Transactdate), 0)  FROM Transact WHERE TransactQuery LIKE '%Changed Password%' AND Username = '" . trim($username) . "'";
        $result = selectdata($conn, $query);
        if (90 < getTotalDays(displayDate($result[0]), displayToday())) {
            $text = "Password Period Limit Expired while Login from IP: " . $_SERVER["REMOTE_ADDR"] . " - Device Name: " . gethostbyaddr($_SERVER["REMOTE_ADDR"]);
            return 5;
        }
    }
    
    if (getRegister($this_mac, 2) < insertToday() && getRegister($this_mac, 3) == 1) {
        $text = "Service Expired while Login from IP: " . $_SERVER["REMOTE_ADDR"] . " - Device Name: " . gethostbyaddr($_SERVER["REMOTE_ADDR"]);
        return 4;
    }
    
    /* 2021 */
    if ($surrendered == 4) {
        $text = "Service Surrendered while Login from IP: " . $_SERVER["REMOTE_ADDR"] . " - Device Name: " . gethostbyaddr($_SERVER["REMOTE_ADDR"]);
        return 6;
    }
    
    if ($global_result[2] != $global_result_21->CompanyName) {
        $text = "Service Expired while Login from IP: " . $_SERVER["REMOTE_ADDR"] . " - Device Name: " . gethostbyaddr($_SERVER["REMOTE_ADDR"]);
        return 7;
    }

    /* End */
//    echo getRegister($this_mac, 1);die;
//    if ($tcount <= getRegister($this_mac, 1)) {
    if ($tcount <= getRegister($this_mac,1)) { 
        if(date("Y-m-d") > $expiryDate){
            $text = "Service Expired";
            return 4;
        }
        $this_password = "";
        if ($lstUserType == "User") {
            $query = "SELECT Userpass, Userlevel, Usermail, Userstatus, RDSSelection, RDSFont, RDSCW, RDSHeaderBreak, RHSSelection, OT1F, OT2F, OTDF, UserType FROM UserMaster WHERE Username = '" . trim($username) . "'";
            $result = selectdata($conn, $query);
            $this_password = decryptString($result[0]);
        } else {
            $query = "SELECT pwd, '18V18R20V20R34V34A', '', '', '', '', '', '', '', '', '', '', '' FROM tuser WHERE id = '" . trim($username) . "'";
//            echo $query = "SELECT id,pwd, name FROM tuser WHERE name = '" . trim($username) . "'";
            $result = selectdata($conn, $query);
            $this_password = strrev($result[0]);
//            echo "<pre>";print_R($this_password);//die;
        }
        if ($this_password == trim($password)) {
            session_start();
            $_SESSION[$session_variable . "lstUserType"] = $lstUserType;
            $_SESSION[$session_variable . "username"] = $username;
            $_SESSION[$session_variable . "userlevel"] = $result[1];
            $_SESSION[$session_variable . "usermail"] = $result[2];
            $_SESSION[$session_variable . "userstatus"] = $result[3];
            if ($lstUserType == "User") {
                $_SESSION[$session_variable . "userrdsselection"] = $result[4];
                $_SESSION[$session_variable . "userrdsfont"] = $result[5];
                $_SESSION[$session_variable . "userrdscw"] = $result[6];
                $_SESSION[$session_variable . "userrdsheaderbreak"] = $result[7];
                $_SESSION[$session_variable . "userrhsselection"] = $result[8];
                $_SESSION[$session_variable . "ot1f"] = $result[9];
                $_SESSION[$session_variable . "ot2f"] = $result[10];
                $_SESSION[$session_variable . "otdf"] = $result[11];
                $userQuery = "select F8 from tuser where name='$username'";
                $designUser = selectdata($conn, $userQuery);
                $_SESSION[$session_variable . "design"] = $designUser[0];
                if ($result[12] == "") {
                    $result[12] = "Yes";
                }
                $_SESSION[$session_variable . "usertype"] = $result[12];
            }
            $query = "UPDATE UserMaster SET LastLogin = " . insertToday() . " WHERE Username = '" . trim($username) . "'";
            updatedata($conn, $query, true);
            $text = "Logged In from IP: " . $_SERVER["REMOTE_ADDR"] . " - Device Name: " . gethostbyaddr($_SERVER["REMOTE_ADDR"]);
            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . trim($username) . "', '" . $text . "')";
            updatedata($conn, $query, true);
            $_SESSION[$session_variable . "ExitTerminal"] = $global_result[0];
            $_SESSION[$session_variable . "Project"] = $global_result[1];
//            $_SESSION[$session_variable . "CompanyName"] = getRegister($this_mac, 4);
//            $_SESSION[$session_variable . "CompanyDetail1"] = getRegister($this_mac, 5);
//            $_SESSION[$session_variable . "CompanyDetail2"] = getRegister($this_mac, 6);
            $_SESSION[$session_variable . "CompanyName"] = getRegister($this_mac, 4);
            $_SESSION[$session_variable . "CompanyDetail1"] = getRegister($this_mac, 5);
            $_SESSION[$session_variable . "CompanyDetail2"] = getRegister($this_mac, 6);
            $_SESSION[$session_variable . "NightShiftMaxOutTime"] = $global_result[5];
            $_SESSION[$session_variable . "IDColumnName"] = $global_result[6];
            $_SESSION[$session_variable . "RotateShift"] = $global_result[8];
            $_SESSION[$session_variable . "Ex3"] = $global_result[9];
            $_SESSION[$session_variable . "Ex2"] = $global_result[10];
            $_SESSION[$session_variable . "MACAddress"] = encryptDecrypt($this_mac);
            $_SESSION[$session_variable . "MACProcessorId"] = getRegister($this_mac, 0);
            $_SESSION[$session_variable . "EmployeeCodeLength"] = $global_result[13];
            $_SESSION[$session_variable . "PhoneColumnName"] = $global_result[14];
            $_SESSION[$session_variable . "ApproveOTIgnoreActual"] = $global_result[16];
            $_SESSION[$session_variable . "FlagLimitType"] = $global_result[17];
            $_SESSION[$session_variable . "F1"] = $global_result_F[0];
            $_SESSION[$session_variable . "F2"] = $global_result_F[1];
            $_SESSION[$session_variable . "F3"] = $global_result_F[2];
            $_SESSION[$session_variable . "F4"] = $global_result_F[3];
            $_SESSION[$session_variable . "F5"] = $global_result_F[4];
            $_SESSION[$session_variable . "F6"] = $global_result_F[5];
            $_SESSION[$session_variable . "F7"] = $global_result_F[6];
            $_SESSION[$session_variable . "F8"] = $global_result_F[7];
            $_SESSION[$session_variable . "F9"] = $global_result_F[8];
            $_SESSION[$session_variable . "F10"] = $global_result_F[9];
            $_SESSION[$session_variable . "ClientID"] = getRegister($this_mac, 7);
            if (trim(strtolower($username)) == "meal" || $global_result[0] == "Canteen") {
                $_SESSION[$session_variable . "VirdiLevel"] = "Meal";
            } else {
//                if (insertToday() < getRegister($this_mac, 2)) {
                if (insertToday() < getRegister($this_mac, 2)) {
                    $_SESSION[$session_variable . "VirdiLevel"] = getVirdiLevel($this_mac);
                } else {
                    $_SESSION[$session_variable . "VirdiLevel"] = "Basic";
                }
            }
            $_SESSION[$session_variable . "UseShiftRoster"] = $global_result[18];
            $_SESSION[$session_variable . "DivColumnName"] = $global_result[19];
            $_SESSION[$session_variable . "RemarkColumnName"] = $global_result[20];
            if ($lstUserType == "User") {
                $user_level_query = " AND (tuser.UserStatus > " . $result[3] . " OR (tuser.UserStatus = " . $result[3] . " AND tuser.id = '" . $username . "')) ";
                $user_level_where_query = " WHERE (tuser.UserStatus > " . $result[3] . " OR (tuser.UserStatus = " . $result[3] . " AND tuser.id = '" . $username . "')) ";
                $query = "SELECT UserDivLockDate.Date FROM UserDivLockDate, UserDiv WHERE UserDivLockDate.Div = UserDiv.Div AND UserDiv.Username = '" . trim($username) . "'";
                $result = selectdata($conn, $query);
                if ($result != "" && $result[0] != "") {
                    $_SESSION[$session_variable . "LockDate"] = $result[0];
                } else {
                    $_SESSION[$session_variable . "LockDate"] = $global_result[11];
                }
                if ($_SESSION[$session_variable . "VirdiLevel"] == "Classic") {
                    $frt = "";
                    $query = "SELECT Flag, Title FROM FlagTitle";
                    $result = mysqli_query($conn, $query);
                    while ($cur = mysqli_fetch_row($result)) {
                        if (1 < strlen($cur[1])) {
                            $frt = $frt . "<font color='" . $cur[0] . "'>" . $cur[0] . " = " . $cur[1] . "</font><br>";
                        }
                    }
                    $_SESSION[$session_variable . "FlagReportText"] = $frt;
                }
                $dept_query = "SELECT Dept FROM UserDept WHERE Username = '" . trim($username) . "'";
                $result = mysqli_query($conn, $dept_query);
                $whereQuery = "";
                $query = "";
                for ($i = 0; $sdcur = mysqli_fetch_row($result); $i++) {
                    if ($sdcur[0] != "") {
                        if ($i == 0) {
                            $whereQuery = " WHERE (tuser.dept = '" . $sdcur[0] . "' ";
                            $query = " AND (tuser.dept = '" . $sdcur[0] . "' ";
                        } else {
                            $whereQuery = $whereQuery . " OR tuser.dept = '" . $sdcur[0] . "' ";
                            $query = $query . " OR tuser.dept = '" . $sdcur[0] . "' ";
                        }
                    }
                }
                if (5 < strlen($query)) {
                    $query = $query . " ) ";
                    $whereQuery = $whereQuery . " ) ";
                    $_SESSION[$session_variable . "DeptAccessQuery"] = $query . $user_level_query;
                    $_SESSION[$session_variable . "DeptAccessWhereQuery"] = $whereQuery . $user_level_query;
                } else {
                    $_SESSION[$session_variable . "DeptAccessQuery"] = $user_level_query;
                    $_SESSION[$session_variable . "DeptAccessWhereQuery"] = $user_level_where_query;
                }
                $div_query = "SELECT UserDiv.Div FROM UserDiv WHERE Username = '" . trim($username) . "'";
                $whereQuery = "";
                $query = "";
                $i = 0;
                for ($result = mysqli_query($conn, $div_query); $sdcur = mysqli_fetch_row($result); $i++) {
                    if ($sdcur[0] != "") {
                        if ($i == 0) {
                            $whereQuery = " WHERE (tuser.company = '" . $sdcur[0] . "' ";
                            $query = " AND (tuser.company = '" . $sdcur[0] . "' ";
                        } else {
                            $whereQuery = $whereQuery . " OR tuser.company = '" . $sdcur[0] . "' ";
                            $query = $query . " OR tuser.company = '" . $sdcur[0] . "' ";
                        }
                    }
                }
                if (5 < strlen($query)) {
                    $query = $query . " ) ";
                    $whereQuery = $whereQuery . " ) ";
                    $_SESSION[$session_variable . "DivAccessQuery"] = $query;
                    $_SESSION[$session_variable . "DivAccessWhereQuery"] = $whereQuery;
                }
                return 0;
            }
            $user_level_query = " AND tuser.id = '" . $username . "' ";
            $_SESSION[$session_variable . "DeptAccessQuery"] = $user_level_query;
            return 0;
        }
        $text = "Invalid Login Attempt from IP: " . $_SERVER["REMOTE_ADDR"] . " - Device Name: " . gethostbyaddr($_SERVER["REMOTE_ADDR"]);
        $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . trim($username) . "', '" . $text . "')";
        updatedata($conn, $query, true);
        return 3;
    }
    
    $text = "Invalid Terminal Error while Login from IP: " . $_SERVER["REMOTE_ADDR"] . " - Device Name: " . gethostbyaddr($_SERVER["REMOTE_ADDR"]);
    $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . trim($username) . "', '" . $text . "')";
    updatedata($conn, $query, true);
    return 2;
}

function loginCustom($conn, $table, $column_username, $column_password, $username, $password) {
    global $session_variable;
    $query = "SELECT " . $column_password . " FROM " . $table . " WHERE " . $column_username . " = '" . $username . "'";
    $result = selectdata($conn, $query);
    if ($result[0] == trim($password)) {
        session_start();
        $_SESSION[$session_variable . "username"] = $username;
        return true;
    }
    return false;
}

function changePassword($conn, $username, $password, $new_password, $lstUserType) {
    if (login($conn, $username, $password, $lstUserType) == 0) {
        if ($lstUserType == "User") {
            $query = "UPDATE UserMaster SET Userpass = '" . encryptString($new_password, $conn) . "' WHERE Username = '" . $username . "'";
        } else {
            $query = "UPDATE tuser SET pwd = '" . encryptString($new_password, $conn) . "' WHERE id = '" . $username . "'";
        }
        if (updateidata($conn, $query, true)) {
            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Changed Password')";
            updateidata($conn, $query, true);
            return true;
        }
        return false;
    }
    return false;
}

function changePasswordCustom($conn, $table, $column_username, $column_password, $username, $password, $new_password) {
    if (logincustom($conn, $table, $column_username, $column_password, $username, $password)) {
        $query = "UPDATE " . $table . " SET " . $column_password . " = '" . replaceString($new_password, false) . "' WHERE " . $column_username . " = '" . $username . "'";
        updatedata($conn, $query, true);
        return true;
    }
    return false;
}

function addUser($conn, $iconn, $username) {
    global $USER_PASS_NEW_USER;
    if (checkDuplicate($conn, "UserMaster", "Username", $username)) {
        $query = "INSERT INTO UserMaster (Username, Userpass, Userstatus) VALUES ('" . $username . "', '" . encryptString($USER_PASS_NEW_USER) . "', 5)";
        if (updateidata($iconn, $query, true)) {
            $text = "Added User: " . $username;
            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $_SESSION[$session_variable . "username"] . "', '" . $text . "')";
            updateidata($iconn, $query, true);
            return true;
        }
        return false;
    }
    return false;
}

function addUserCustom($conn, $table, $column_username, $column_password, $username) {
    if (checkDuplicate($conn, $table, $column_username, $username)) {
        return false;
    }
    $query = "INSERT INTO " . $table . " VALUES (" . $column_username . ", " . $column_password . ") ('" . $username . "', '" . $username . "')";
    if (updatedata($conn, $query, true)) {
        return true;
    }
    return false;
}

function getMax($conn, $table, $column) {
    $query = "SELECT MAX(" . $column . ") FROM " . $table;
    $result = selectdata($conn, $query);
    if ($result[0] == "") {
        $result[0] = 0;
    }
    return $result[0] + 1;
}

function checkDuplicate($conn, $table, $column, $data) {
    $query = "SELECT " . $column . " FROM " . $table . " WHERE " . $column . " = '" . $data . "'";
    $result = selectdata($conn, $query);
    if ($result[0] == "") {
        return true;
    }
    return false;
}

function encryptString($data) {
    $data = convert_uuencode($data);
    $data = strrev($data);
    return $data;
}

function decryptString($data) {
    $data = strrev($data);
    $data = convert_uudecode($data);
    return $data;
}

function replaceString($data, $caps) {
    if ($caps) {
        return preg_replace("/'/", "", strtoupper(trim($data)));
    }
    return preg_replace("/'/", "", trim($data));
}

function addComma($data) {
    return number_format($data, 2, ".", ",");
}

function checkSession($userlevel, $module) {
    global $session_variable;
    if (strpos($userlevel, $module . "V") !== false) {
        if ($_SESSION[$session_variable . "VirdiLevel"] == "Basic" && (strpos($module, "13") !== false || strpos($module, "16") !== false || strpos($module, "23") !== false || strpos($module, "25") !== false || strpos($module, "26") !== false || strpos($module, "29") !== false || strpos($module, "30") !== false || strpos($module, "31") !== false || strpos($module, "32") !== false || strpos($module, "33") !== false || strpos($module, "34") !== false)) {
            session_destroy();
            return false;
        }
        return true;
    }
    session_destroy();
    return false;
}

function displayLoginInfo($prints, $date, $time) {
    global $session_variable;
    date_default_timezone_set("Africa/Algiers");
    $target_url = $_SERVER["PHP_SELF"];
    print "<style>input{background-color:'#F0F0F0';font-family:'Verdana';font-size:'10';}</style>";
    print "<style>select{background-color:'#FFFFFF';font-family:'Verdana';font-size:'10';}</style>";
    print "<link rel=\"shortcut icon\" href=\"img/favicon-16x16.png\" type=\"text/css\" />";
    print "<link rel=\"stylesheet\" href=\"default.css\" type=\"text/css\" />";
//    print "<link rel=\"stylesheet\" href=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css\">";
//    print "<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js\"></script>";
//    print "<script src=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js\"></script>";
    print "<script>" . "function putRemarkValue(x,y){\ty.value = x.value;}" . "function putEmployeeName(x,y){\ty.value = x.value;}" . "function check_valid_date(z){" . "if(z.length != 10 || z.substring(6,10)*1 < 1900 || z.substring(6,10)*1 > 2200){" . "return false;" . "}else{" . "if (z.substring(0,2)*1 < 28 && z.substring(3,5)*1 < 13 && z.substring(2,3) == '/'  && z.substring(5,6) == '/'){" . "return true;" . "}else{" . "if ((z.substring(3,5)*1 == 4 || z.substring(3,5)*1 == 6 || z.substring(3,5)*1 == 9 || z.substring(3,5)*1 == 11) && z.substring(0,2)*1 < 31){" . "return true;" . "}else if (z.substring(3,5)*1 == 2 && z.substring(6,10)*1 % 4 == 0 && z.substring(0,2)*1 < 30){" . "return true;" . "}else if (z.substring(3,5)*1 == 2 && z.substring(6,10)*1 % 4 != 0 && z.substring(0,2)*1 < 29){" . "return true;" . "}else if ((z.substring(3,5)*1 == 1 || z.substring(3,5)*1 == 3 || z.substring(3,5)*1 == 5 || z.substring(3,5)*1 == 7 || z.substring(3,5)*1 == 8 || z.substring(3,5)*1 == 10 || z.substring(3,5)*1 == 12) && z.substring(0,2)*1 < 32){" . "return true;" . "}else{" . "return false;" . "}" . "}" . "}" . "}" . "function check_valid_time(z){" . "if (z*1 != z/1 || z.length != 6 || z.substring(0,2)*1 > 23 || z.substring(2,4)*1 > 59 || z.substring(4,6)*1 > 59){" . "return false;" . "}else{" . "return true;" . "}" . "}" . "function doSaveAs(){" . "var isReady = true;" . "if (isReady){" . "document.execCommand('SaveAs');" . "}else{" . "alert('Feature available only in Internet Exlorer 4.0 and later.');" . "}" . "}";
    print "function checkPrint(a){" . "var x = document.frm1;";
    if ($date == true && $time == false) {
        print "if (check_valid_date(x.txtFrom.value) == false){" . "alert('Invalid From Date. Date Format should be DD/MM/YYYY');" . "x.txtFrom.focus();" . "}else if (check_valid_date(x.txtTo.value) == false){" . "alert('Invalid To Date. Date Format should be DD/MM/YYYY');" . "x.txtTo.focus();" . "}else{";
    } else {
        if ($date == true && $time == true) {
            print "if (check_valid_date(x.txtFrom.value) == false){" . "alert('Invalid From Date. Date Format should be DD/MM/YYYY');" . "x.txtFrom.focus();" . "}else if (check_valid_date(x.txtTo.value) == false){" . "alert('Invalid To Date. Date Format should be DD/MM/YYYY');" . "x.txtTo.focus();" . "}else if (check_valid_time(x.txtTimeFrom.value) == false){" . "alert('Invalid Time Format. Time Format should be HHMMSS');" . "x.txtTimeFrom.focus();" . "}else if (check_valid_time(x.txtTimeTo.value) == false){" . "alert('Invalid Time Format. Time Format should be HHMMSS');" . "x.txtTimeTo.focus();" . "}else{";
        }
    }
    print "if (a == 0){" . "if (confirm('Go Green - Think Twice before you Print this Document. Are you sure want to Print?')){" . "x.action = '" . $target_url . "?prints=yes';" . "x.target = '_blank';" . "x.submit();" . "return true;" . "}else{" . "return false;" . "}" . "}else if (a == 2){" . "if (confirm('Go Green - Think Twice before you Print this Document. Are you sure want to Print?')){" . "x.action = '" . $target_url . "?prints=yes&timecard=yes';" . "x.target = '_blank';" . "x.submit();" . "return true;" . "}else{" . "return false;" . "}" . "}else if (a == 3){" . "x.action = '" . $target_url . "?prints=yes&excel=yes&csv=yes';" . "x.target = '_blank';" . "x.submit();" . "return true;" . "}else{" . "x.action = '" . $target_url . "?prints=yes&excel=yes';" . "x.target = '_blank';" . "x.submit();" . "return true;" . "}";
    if ($date == true || $time == true) {
        print "}";
    }
    print "}";
    print "function checkSearch(){" . "var x = document.frm1;";
    if ($date == true && $time == false) {
        print "if (check_valid_date(x.txtFrom.value) == false){" . "alert('Invalid From Date. Date Format should be DD/MM/YYYY');" . "x.txtFrom.focus();" . "return false;" . "}else if (check_valid_date(x.txtTo.value) == false){" . "alert('Invalid To Date. Date Format should be DD/MM/YYYY');" . "x.txtTo.focus();" . "return false;" . "}else{";
    } else {
        if ($date == true && $time == true) {
            print "if (check_valid_date(x.txtFrom.value) == false){" . "alert('Invalid From Date. Date Format should be DD/MM/YYYY');" . "x.txtFrom.focus();" . "return false;" . "}else if (check_valid_date(x.txtTo.value) == false){" . "alert('Invalid To Date. Date Format should be DD/MM/YYYY');" . "x.txtTo.focus();" . "return false;" . "}else if (check_valid_time(x.txtTimeFrom.value) == false){" . "alert('Invalid Time Format. Time Format should be HHMMSS');" . "x.txtTimeFrom.focus();" . "return false;" . "}else if (check_valid_time(x.txtTimeTo.value) == false){" . "alert('Invalid Time Format. Time Format should be HHMMSS');" . "x.txtTimeTo.focus();" . "return false;" . "}else{";
        }
    }
    print "x.action = '" . $target_url . "?prints=no';" . "x.target = '_self';" . "x.btSearch.disabled = true;" . "return true;";
    if ($date == true || $time == true) {
        print "}";
    }
    print "}";
    print "</script>";
    if ($prints == "yes") {
        print "<table width='100%' cellspacing='0' cellpadding='0'>";
        print "<tr> <td align='left' bgcolor='#FAFAFA' vAlign='top'><font face='Verdana' size='2' color='Brown'><b>" . $_SESSION[$session_variable . "CompanyName"] . "</font> <br><font face='Verdana' size='1' color='#339900'>Go Green - Think Twice before you Make Copies</font></b></td> <td align='right' vAlign='top'><font face='Verdana' size='1' color='Black'><b>Virdi<i><font color='Gray' face='Times New Roman' size='2'>&nbsp;&nbsp;&nbsp;&nbsp;</font></i></b>Access Control, Time and Attendance Application <br><font color='#000000'><b><u>Version: " . $_SESSION[$session_variable . "Ex2"] . "</u></font></b></td></tr>";
        print "</table></div></center>";
    } else {
        $conn = openConnection();
        $query = "SELECT MACAddress, CompanyDetail4 FROM OtherSettingMaster";
        $global_result = selectdata($conn, $query);
        $this_mac = $global_result[0];
        $global_result_decode = base64_decode($global_result[1]);
        $global_result_decrypt = encryptDecrypt($global_result_decode);
        $global_result_21 = json_decode($global_result_decrypt);
        $this_mac_license = encryptDecrypt($global_result_21->MacProcessorId);
        
        print "<table cellspacing='0' cellpadding='0' border='0'>";
        if ($_SESSION[$session_variable . "Ex2"] == "") {
            if ($prints == "") { //<img src='img/virdi.gif'> 
//                print "<div><img src='img/logo.PNG' height='28%' width='70%' class='loginImg'></div><br><br>";
                if ($_SESSION[$session_variable . "username"] == "admin" || $_SESSION[$session_variable . "username"] == "virdi") {
//                    echo "<div><font face='Verdana' color='#000000' size='1'><a href='#' style='text-decoration:none' onClick=\"window.open('VU.php', 'Version Update', 'height=300;width=300;resize=no;menubar=no;addressbar=no')\">Register/ Update</a></font></div>";
                } else {
                    print "<td vAlign='bottom' align='right'><font face='Verdana' color='#000000' size='1'>&nbsp;</font></td></tr>";
                } 
                
                print "<div class='text-center' style='padding-top:40px;'><img src='img/logo.PNG' height='28%' width='70%' loading='lazy' class='loginImg' style='margin-bottom:0px !important;'><br><span style='color:#1b6f9e';>Powered By</span> <br><b style='color:#1b6f9e';>Endeavour Solution Nigeria Limited</b></div><br><br>";
                print "<div class='right-align'><font face='Verdana' size='5' color='Brown'><b>" . getRegister($this_mac,4) . "</b></font>"
                        . "<font face='Verdana' size='2' color='Brown' align='right'><br>" . getRegister($this_mac,6) . "</font></div>";
                print "<tr> <td vAlign='bottom' align='left'>";
                print "<div class='right-align'><b><font face='Verdana' size='1' color='#339900'>Go Green - Think Twice before you Print this Document</font></b></div>";
                if (getRegister($this_mac, 8) == 1) {
                    print "<div class='right-align'><font face='Verdana' size='1' color='#000000'>Enterprise Ver Expires: " . displayDate(getRegister($this_mac, 2)) . "</font></div>";
                }
                if (getRegister($this_mac, 8) == 2) {
                    print "<div class='right-align'><font face='Verdana' size='1' color='#000000'>Profesional Ver Expires: " . displayDate(getRegister($this_mac, 2)) . "</font></div>";
                }
                if (getRegister($this_mac, 8) == 3) {
                    print "<div class='right-align'><font face='Verdana' size='1' color='#000000'>Lite Ver Expires: " . displayDate(getRegister($this_mac, 2)) . "</font></div>";
                }
            } else {  //<img src='img/" . $prints . "'> 
//                print "<div><img src='img/logo.PNG' height='28%' width='70%' class='loginImg'></div><br><br>";
                print "<div class='text-center' style='padding-top:40px;'><img src='img/logo.PNG' height='28%' loading='lazy' width='70%' class='loginImg' style='margin-bottom:0px !important;'><br><span style='color:#1b6f9e';>Powered By</span> <br><b style='color:#1b6f9e';>Endeavour Solution Nigeria Limited</b></div><br><br>";
                print "<div class='right-align'><font face='Verdana' size='5' color='Brown'><b>" . getRegister($this_mac, 4) . "</b></font>"
                        . "<font face='Verdana' size='2' color='Brown' align='right'><br>" . getRegister($this_mac,6) . "</font></div>";
                print "<tr> <td vAlign='bottom' align='left'>";
                print "<div class='right-align'><b><font face='Verdana' size='1' color='#339900'>Go Green - Think Twice before you Print this Document</font></b></div>";
//                echo getRegister($this_mac, 2);
                if (getRegister($this_mac, 8) == 1) {
                    print "<div class='right-align'><font face='Verdana' size='1' color='#000000'>Enterprise Ver Expires: " . displayDate(getRegister($this_mac, 2)) . "</font></div>";
                }
                if (getRegister($this_mac, 8) == 2) {
                    print "<div class='right-align'><font face='Verdana' size='1' color='#000000'>Profesional Ver Expires: " . displayDate(getRegister($this_mac, 2)) . "</font></div>";
                }
                if (getRegister($this_mac, 8) == 3) {
                    print "<div class='right-align'><font face='Verdana' size='1' color='#000000'>Lite Ver Expires: " . displayDate(getRegister($this_mac, 2)) . "</font></div>";
                }

                if ($_SESSION[$session_variable . "username"] == "admin" || $_SESSION[$session_variable . "username"] == "virdi") {
//                    echo "<div><font face='Verdana' color='#000000' size='1'><a href='#' style='text-decoration:none' onClick=\"window.open('VU.php', 'Version Update', 'height=300;width=300;resize=no;menubar=no;addressbar=no')\">Register/ Update</a></font></div>";
                } else {
                    print "<td vAlign='bottom'><font face='Verdana' color='#000000' size='1'>&nbsp;</font></td></tr>";
                }
            }
        } else { 
            if (getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) == 1) {
                $version = "<div class='right-align'><font face='Verdana' size='1' color='#FF0000'><b>Enterprise Ver Expires: " . displayDate(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 2)) . "</b></font></div><div class='right-align'><font face='Verdana' size='1'><b>Your Portal Support $global_result_21->NoOfUser Users</b></font></div><br><br>";
            }
            if (getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) == 2) {
                $version = "<div class='right-align'><font face='Verdana' size='1' color='#FF0000'><b>Profesional Ver Expires: " . displayDate(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 2)) . "</b></font></div><div class='right-align'><font face='Verdana' size='1'><b>Your Portal Support $global_result_21->NoOfUser Users</b></font></div><br><br>";
            }
            if (getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) == 3) {
                $version = "<div class='right-align'><font face='Verdana' size='1' color='#FF0000'><b>Lite Ver Expires: " . displayDate(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 2)) . "</b></font></div><div class='right-align'><font face='Verdana' size='1'><b>Your Portal Support $global_result_21->NoOfUser Users</b></font></div><br><br>";
            }
            if (getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) == 1) {
                $version1 = "<div class='right-align'><font face='Verdana' size='1' color='#000000'>Enterprise Ver Expires: " . displayDate(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 2)) . "</font></div><div class='right-align'><font face='Verdana' size='1'><b>Your Portal Support $global_result_21->NoOfUser Users</b></font></div><br><br>";
            }
            if (getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) == 2) {
                $version1 = "<div class='right-align'><font face='Verdana' size='1' color='#000000'>Profession Ver Expires: " . displayDate(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 2)) . "</font></div><div class='right-align'><font face='Verdana' size='1'><b>Your Portal Support $global_result_21->NoOfUser Users</b></font></div><br><br>";
            }
            if (getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) == 3) {
                $version1 = "<div class='right-align'><font face='Verdana' size='1' color='#000000'>Lite Ver Expires: " . displayDate(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 2)) . "</font></div><div class='right-align'><font face='Verdana' size='1'><b>Your Portal Support $global_result_21->NoOfUser Users</b></font></div><br><br>";
            }
            print "<div class='text-center'><img src='img/logo.PNG' height='28%' width='70%' loading='lazy' class='loginImg' style='margin-bottom:0px !important;'><br><span style='color:#1b6f9e';>Powered By</span> <br><b style='color:#1b6f9e';>Endeavour Solution Nigeria Limited</b></div><br><br>";
            print "<div class='right-align'><font face='Verdana' size='5' color='Brown'><b>" . $_SESSION[$session_variable . "CompanyName"] . "</b></font>"
                    . "<font face='Verdana' size='2' color='Brown' align='right'><br>" . $_SESSION[$session_variable . "CompanyDetail1"] . ", " . $_SESSION[$session_variable . "CompanyDetail2"] . "</font></div>";
            print "<tr> <td vAlign='bottom' align='left'>";
            displayToday();
            print "<div class='right-align' style='padding-top:20px;'><b><font face='Verdana' size='1' color='#339900'>Go Green - Think Twice before you Print this Document</font></b>"
                    . "<br><font face='Verdana' size='1' color='#000000'>User: " . $_SESSION[$session_variable . "username"] . " [" . displayToday() . "]  </font></div>";
            if (getTotalDays(displayToday(), displayDate(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 2))) < 10) {
                displayDate(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 2));
                print $version;
            } else {
                if (getTotalDays(displayToday(), displayDate(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 2))) < 30) {
                    displayDate(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 2));
                    print $version;
                } else {
                    displayDate(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 2));
                    print $version1;
                }
            }
            print "<div class='flex-container'>";
            if ($_SESSION[$session_variable . "VirdiLevel"] == "Classic") {
                print "<div><a href='SiteMap.php' target='_blank' style='text-decoration:none'><font face='Verdana' color='#000000' size='1'>[Help]</a></font>&nbsp;<a href='VersionInfo.php' target='_blank' style='text-decoration:none'><font size='1' color='#000000' face='Verdana'>[" . $_SESSION[$session_variable . "Ex2"] . "]</font></a></div>";
            } else {
                print "<div><font face='Verdana' color='#000000' size='1'><a href='VersionInfo.php' target='_blank' style='text-decoration:none'>[" . $_SESSION[$session_variable . "VirdiLevel"] . "]</a></font></div>";
            }
            if ($_SESSION[$session_variable . "username"] == "admin" || $_SESSION[$session_variable . "username"] == "virdi") {
//                print "<div><font face='Verdana' color='#000000' size='1'><a href='#' class='right-align' style='text-decoration:none' onClick=\"window.open('VU.php', 'Version Update', 'height=300;width=300;resize=no;menubar=no;addressbar=no')\">Register/ Update</a></font>";
            } else {
                print "<font face='Verdana' color='#000000' size='1'>&nbsp;</font>";
            }
            print "</div>";
            print "</td>";
        }
        print "</table>";
    }
}

function displayHeader($prints, $date, $time) {
    global $session_variable;
    date_default_timezone_set("Africa/Algiers");
    $target_url = $_SERVER["PHP_SELF"];
    print "<style>input{background-color:'#F0F0F0';font-family:'Verdana';font-size:'10';}</style>";
    print "<style>select{background-color:'#FFFFFF';font-family:'Verdana';font-size:'10';}</style>";
    print "<link rel=\"shortcut icon\" href=\"img/favicon-16x16.png\" type=\"text/css\" />";
    print "<link rel=\"stylesheet\" href=\"default.css\" type=\"text/css\" />";
//    print "<link rel=\"stylesheet\" href=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css\">";
//    print "<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js\"></script>";
//    print "<script src=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js\"></script>";
    print "<script>" . "function putRemarkValue(x,y){\ty.value = x.value;}" . "function putEmployeeName(x,y){\ty.value = x.value;}" . "function check_valid_date(z){" . "if(z.length != 10 || z.substring(6,10)*1 < 1900 || z.substring(6,10)*1 > 2200){" . "return false;" . "}else{" . "if (z.substring(0,2)*1 < 28 && z.substring(3,5)*1 < 13 && z.substring(2,3) == '/'  && z.substring(5,6) == '/'){" . "return true;" . "}else{" . "if ((z.substring(3,5)*1 == 4 || z.substring(3,5)*1 == 6 || z.substring(3,5)*1 == 9 || z.substring(3,5)*1 == 11) && z.substring(0,2)*1 < 31){" . "return true;" . "}else if (z.substring(3,5)*1 == 2 && z.substring(6,10)*1 % 4 == 0 && z.substring(0,2)*1 < 30){" . "return true;" . "}else if (z.substring(3,5)*1 == 2 && z.substring(6,10)*1 % 4 != 0 && z.substring(0,2)*1 < 29){" . "return true;" . "}else if ((z.substring(3,5)*1 == 1 || z.substring(3,5)*1 == 3 || z.substring(3,5)*1 == 5 || z.substring(3,5)*1 == 7 || z.substring(3,5)*1 == 8 || z.substring(3,5)*1 == 10 || z.substring(3,5)*1 == 12) && z.substring(0,2)*1 < 32){" . "return true;" . "}else{" . "return false;" . "}" . "}" . "}" . "}" . "function check_valid_time(z){" . "if (z*1 != z/1 || z.length != 6 || z.substring(0,2)*1 > 23 || z.substring(2,4)*1 > 59 || z.substring(4,6)*1 > 59){" . "return false;" . "}else{" . "return true;" . "}" . "}" . "function doSaveAs(){" . "var isReady = true;" . "if (isReady){" . "document.execCommand('SaveAs');" . "}else{" . "alert('Feature available only in Internet Exlorer 4.0 and later.');" . "}" . "}";
    print "function checkPrint(a){" . "var x = document.frm1;";
    if ($date == true && $time == false) {
        print "if (check_valid_date(x.txtFrom.value) == false){" . "alert('Invalid From Date. Date Format should be DD/MM/YYYY');" . "x.txtFrom.focus();" . "}else if (check_valid_date(x.txtTo.value) == false){" . "alert('Invalid To Date. Date Format should be DD/MM/YYYY');" . "x.txtTo.focus();" . "}else{";
    } else {
        if ($date == true && $time == true) {
            print "if (check_valid_date(x.txtFrom.value) == false){" . "alert('Invalid From Date. Date Format should be DD/MM/YYYY');" . "x.txtFrom.focus();" . "}else if (check_valid_date(x.txtTo.value) == false){" . "alert('Invalid To Date. Date Format should be DD/MM/YYYY');" . "x.txtTo.focus();" . "}else if (check_valid_time(x.txtTimeFrom.value) == false){" . "alert('Invalid Time Format. Time Format should be HHMMSS');" . "x.txtTimeFrom.focus();" . "}else if (check_valid_time(x.txtTimeTo.value) == false){" . "alert('Invalid Time Format. Time Format should be HHMMSS');" . "x.txtTimeTo.focus();" . "}else{";
        }
    }
    print "if (a == 0){" . "if (confirm('Go Green - Think Twice before you Print this Document. Are you sure want to Print?')){" . "x.action = '" . $target_url . "?prints=yes';" . "x.target = '_blank';" . "x.submit();" . "return true;" . "}else{" . "return false;" . "}" . "}else if (a == 2){" . "if (confirm('Go Green - Think Twice before you Print this Document. Are you sure want to Print?')){" . "x.action = '" . $target_url . "?prints=yes&timecard=yes';" . "x.target = '_blank';" . "x.submit();" . "return true;" . "}else{" . "return false;" . "}" . "}else if (a == 3){" . "x.action = '" . $target_url . "?prints=yes&excel=yes&csv=yes';" . "x.target = '_blank';" . "x.submit();" . "return true;" . "}else{" . "x.action = '" . $target_url . "?prints=yes&excel=yes';" . "x.target = '_blank';" . "x.submit();" . "return true;" . "}";
    if ($date == true || $time == true) {
        print "}";
    }
    print "}";
    print "function checkSearch(){" . "var x = document.frm1;";
    if ($date == true && $time == false) {
        print "if (check_valid_date(x.txtFrom.value) == false){" . "alert('Invalid From Date. Date Format should be DD/MM/YYYY');" . "x.txtFrom.focus();" . "return false;" . "}else if (check_valid_date(x.txtTo.value) == false){" . "alert('Invalid To Date. Date Format should be DD/MM/YYYY');" . "x.txtTo.focus();" . "return false;" . "}else{";
    } else {
        if ($date == true && $time == true) {
            print "if (check_valid_date(x.txtFrom.value) == false){" . "alert('Invalid From Date. Date Format should be DD/MM/YYYY');" . "x.txtFrom.focus();" . "return false;" . "}else if (check_valid_date(x.txtTo.value) == false){" . "alert('Invalid To Date. Date Format should be DD/MM/YYYY');" . "x.txtTo.focus();" . "return false;" . "}else if (check_valid_time(x.txtTimeFrom.value) == false){" . "alert('Invalid Time Format. Time Format should be HHMMSS');" . "x.txtTimeFrom.focus();" . "return false;" . "}else if (check_valid_time(x.txtTimeTo.value) == false){" . "alert('Invalid Time Format. Time Format should be HHMMSS');" . "x.txtTimeTo.focus();" . "return false;" . "}else{";
        }
    }
    print "x.action = '" . $target_url . "?prints=no';" . "x.target = '_self';" . "x.btSearch.disabled = true;" . "return true;";
    if ($date == true || $time == true) {
        print "}";
    }
    print "}";
    print "</script>";
    if ($prints == "yes") {
        print "<table width='100%' cellspacing='0' cellpadding='0'>";
        print "<tr> <td align='left' bgcolor='#FAFAFA' vAlign='top'><font face='Verdana' size='2' color='Brown'><b>" . $_SESSION[$session_variable . "CompanyName"] . "</font> <br><font face='Verdana' size='1' color='#339900'>Go Green - Think Twice before you Make Copies</font></b></td> <td align='right' vAlign='top'><font face='Verdana' size='1' color='Black'><b>Virdi<i><font color='Gray' face='Times New Roman' size='2'>&nbsp;&nbsp;&nbsp;&nbsp;</font></i></b>Access Control, Time and Attendance Application <br><font color='#000000'><b><u>Version: " . $_SESSION[$session_variable . "Ex2"] . "</u></font></b></td></tr>";
        print "</table></div></center>";
    } else {
        print "<table width='600' cellspacing='0' cellpadding='0' border='0'>";
        if ($_SESSION[$session_variable . "Ex2"] == "") {
            if ($prints == "") { //<img src='img/virdi.gif'>
//                print "<tr><td vAlign='top' align='left'><img src='img/logo.PNG' height='28%' class='loginImg'>&nbsp;&nbsp;<font size='5'><b><i></i></b></font></td> <td vAlign='bottom'><font face='Verdana' color='#000000' size='1'>&nbsp;</font></td>";
                if ($_SESSION[$session_variable . "username"] == "admin" || $_SESSION[$session_variable . "username"] == "virdi") {
//                    echo "\t\t\t\t\t\t\t\t<td vAlign='bottom' align='right'><font face='Verdana' color='#000000' size='1'><a href='#' style='text-decoration:none' onClick=\"window.open('VU.php', 'Version Update', 'height=300;width=300;resize=no;menubar=no;addressbar=no')\">Register/ Update</a></font></td></tr>\r\n\t\t\t\t\t\t\t";
                } else {
                    print "<td vAlign='bottom' align='right'><font face='Verdana' color='#000000' size='1'>&nbsp;</font></td></tr>";
                }
            } else { //<img src='img/" . $prints . "'>
//                print "<tr><td vAlign='bottom' align='right' colspan = '3'><img src='img/logo.PNG' height='28%' class='loginImg'></td> <td vAlign='bottom'><font face='Verdana' color='#000000' size='1'>&nbsp;</font></td>";
                if ($_SESSION[$session_variable . "username"] == "admin" || $_SESSION[$session_variable . "username"] == "virdi") {
//                    echo "\t\t\t\t\t\t\t\t<td vAlign='bottom'><font face='Verdana' color='#000000' size='1'><a href='#' style='text-decoration:none' onClick=\"window.open('VU.php', 'Version Update', 'height=300;width=300;resize=no;menubar=no;addressbar=no')\">Register/ Update</a></font></td></tr>\r\n\t\t\t\t\t\t\t";
                } else {
                    print "<td vAlign='bottom'><font face='Verdana' color='#000000' size='1'>&nbsp;</font></td></tr>";
                }
            }
        } else { //<img src='img/virdi.gif'> bgcolor='#FAFAFA' background='img/cBar.gif'
//            print "<tr><td vAlign='top' align='left'><img src='img/logo.PNG' height='28%' class='loginImg'></td> ";
            
//            print "<td colspan='2' vAlign='bottom'><font size='5'><b><i></i></b></font></td> <td vAlign='bottom' align='right' bgcolor=''  style='background-repeat:no-repeat'><font face='Verdana' size='5' color='Brown'><b>" . $_SESSION[$session_variable . "CompanyName"] . "</b></font> <font face='Verdana' size='2' color='Brown'><br>" . $_SESSION[$session_variable . "CompanyDetail1"] . ", " . $_SESSION[$session_variable . "CompanyDetail2"] . "</font></b></td> </tr>";
            print "<td colspan='2' vAlign='bottom' align='left'  style='background-repeat:no-repeat'><font size='5' color='#4169E1'><b>" . $_SESSION[$session_variable . "CompanyName"] . "</b></font> <font face='Verdana' size='2' color='#4169e1'><br>" . $_SESSION[$session_variable . "CompanyDetail1"] . ", " . $_SESSION[$session_variable . "CompanyDetail2"] . "</font></b></td> </tr>";
            print "<tr> <td vAlign='bottom' align='left'>";

            displayToday();
//            print "<td vAlign='bottom' align='right'><b><font face='Verdana' size='1' color='#339900'>Go Green - Think Twice before you Print this Document</font></b> <br><font face='Verdana' size='1' color='#000000'>User: " . $_SESSION[$session_variable . "username"] . " [" . displayToday() . "]  </font>";
            print "<td vAlign='bottom' align='left'><b><font face='Verdana' size='1' color='#339900'>Go Green - Think Twice before you Print this Document</font></b>";
//            if (getTotalDays(displayToday(), displayDate(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 2))) < 10) {
//                displayDate(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 2));
//                print $version;
//            } else {
//                if (getTotalDays(displayToday(), displayDate(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 2))) < 30) {
//                    displayDate(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 2));
//                    print $version;
//                } else {
//                    displayDate(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 2));
//                    print $version1;
//                }
//            }
//            if ($_SESSION[$session_variable . "VirdiLevel"] == "Classic") {
//                print "<a href='SiteMap.php' target='_blank' style='text-decoration:none'><font face='Verdana' color='#000000' size='1'>&nbsp;&nbsp[Help]</a></font>&nbsp;<a href='VersionInfo.php' target='_blank' style='text-decoration:none'><font size='1' color='#000000' face='Verdana'>[" . $_SESSION[$session_variable . "Ex2"] . "]</font></a>";
//            } else {
//                print "<font face='Verdana' color='#000000' size='1'><a href='VersionInfo.php' target='_blank' style='text-decoration:none'>[" . $_SESSION[$session_variable . "VirdiLevel"] . "]</a></font>";
//            }
//            if ($_SESSION[$session_variable . "username"] == "admin" || $_SESSION[$session_variable . "username"] == "virdi") {
//                echo "\t\t\t\t\t\t\t</td><td vAlign='bottom'><font face='Verdana' color='#000000' size='1'><a href='#' style='text-decoration:none' onClick=\"window.open('VU.php', 'Version Update', 'height=300;width=300;resize=no;menubar=no;addressbar=no')\">Register/ Update</a></font></td>\r\n\t\t\t\t\t\t";
//            } else {
//                print "</td><td vAlign='bottom'><font face='Verdana' color='#000000' size='1'>&nbsp;</font></td>";
//            }
            print "</td></tr>";
        }
        print "</table>";
    }
}
function helpVerInfo(){
    global $session_variable;
    date_default_timezone_set("Africa/Algiers");
    if ($_SESSION[$session_variable . "VirdiLevel"] == "Classic") {
        print "<li><a class='dropdown-item' href='SiteMap.php' target='_blank' style='text-decoration:none'><i class='fa fa-question-circle me-1 ms-1'></i> [Help]</a><a class='dropdown-item' href='VersionInfo.php' target='_blank' style='text-decoration:none'><i class='fa fa-history me-1 ms-1'></i> [" . $_SESSION[$session_variable . "Ex2"] . "]</a></li>";
    } else {
        print "<li><font face='Verdana' color='#000000' size='1'><a href='VersionInfo.php' target='_blank' style='text-decoration:none'>[" . $_SESSION[$session_variable . "VirdiLevel"] . "]</a></font></li>";
    }
    if ($_SESSION[$session_variable . "username"] == "admin" || $_SESSION[$session_variable . "username"] == "virdi") {
//        echo "<li><a class='dropdown-item' href='#' style='text-decoration:none' onClick=\"window.open('VU.php', 'Version Update', 'height=300;width=300;resize=no;menubar=no;addressbar=no')\"><i class='fa fa-registered me-1 ms-1'></i> Register/ Update</a></li>";
    } else {
        print "<font face='Verdana' color='#000000' size='1'>&nbsp;</font>";
    }
}
function aboutInfo($prints, $date, $time) {
    global $session_variable;
    date_default_timezone_set("Africa/Algiers");
    $target_url = $_SERVER["PHP_SELF"];
    print"<li class='sidebar-item' style='padding:0px;text-align:center;'>";
    print "<font face='Verdana' size='2' color='#000000'>User: " . $_SESSION[$session_variable . "username"] . " [" . displayToday() . "]  </font>";
    if (getTotalDays(displayToday(), displayDate(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 2))) < 10) {
        displayDate(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 2));
        print $version;
    } else {
        if (getTotalDays(displayToday(), displayDate(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 2))) < 30) {
            displayDate(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 2));
            print $version;
        } else {
            displayDate(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 2));
            print $version1;
        }
    }
    print "</li>";
    
    if (getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) == 1) {
        $version = "<font face='Verdana' size='1' color='#FF0000'><b>Enterprise Ver Expires: " . displayDate(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 2)) . "</b></font>";
    }
    if (getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) == 2) {
        $version = "<font face='Verdana' size='1' color='#FF0000'><b>Professional Ver Expires: " . displayDate(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 2)) . "</b></font>";
    }
    if (getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) == 3) {
        $version = "<font face='Verdana' size='1' color='#FF0000'><b>Lite Ver Expires: " . displayDate(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 2)) . "</b></font>";
    }
    if (getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) == 1) {
        $version1 = "<font face='Verdana' size='1' color='#000000'><b>Enterprise Ver Expires: " . displayDate(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 2)) . "</b></font>";
    }
    if (getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) == 2) {
        $version1 = "<font face='Verdana' size='1' color='#000000'><b>Professional Ver Expires: " . displayDate(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 2)) . "</b></font>";
    }
    if (getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) == 3) {
        $version1 = "<font face='Verdana' size='1' color='#000000'><b>Lite Ver Expires: " . displayDate(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 2)) . "</b></font>";
    }
    print "<li class='sidebar-item' style='padding:0px;text-align:center;'>";
    if (getTotalDays(displayToday(), displayDate(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 2))) < 10) {
        displayDate(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 2));
        print $version;
    } else {
        if (getTotalDays(displayToday(), displayDate(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 2))) < 30) {
            displayDate(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 2));
            print $version;
        } else {
            displayDate(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 2));
            print $version1;
        }
    }
    print "</li>";
    $conn = openConnection();
    $vrQuery = "Select Ex2 from othersettingmaster";
    $vrResult = selectdata($conn, $vrQuery);
    print "<li class='sidebar-item' style='padding:0px;text-align:center;'><a href='VersionInfo.php' target='_blank' style='background-color: transparent; color: inherit; text-decoration: none; box-shadow: none;'><span style='font-family: Verdana; font-size: 10px; font-weight: bold;'>[".$vrResult[0]."]</span></a></li>";
}

function displayLinks($x, $userlevel) {
    global $session_variable;
    $userlevel = $_SESSION[$session_variable . "userlevel"];
    $username = $_SESSION[$session_variable . "username"];
    $link = "";
    $url = $_SERVER["REQUEST_URI"];
    print "<style type='text/css'> .ds_box { background-color: #FFF; border: 1px solid #000; position: absolute; z-index: 32767; } .ds_tbl { background-color: #FFF; } .ds_head { background-color: #333; color: #FFF; font-family: Arial, Helvetica, sans-serif; font-size: 13px; font-weight: bold; text-align: center; letter-spacing: 2px; } .ds_subhead { background-color: #CCC; color: #000; font-size: 12px; font-weight: bold; text-align: center; font-family: Arial, Helvetica, sans-serif; width: 32px; } .ds_cell { background-color: #EEE; color: #000; font-size: 13px; text-align: center; font-family: Arial, Helvetica, sans-serif; padding: 5px; cursor: pointer; } .ds_cell:hover { background-color: #F3F3F3; } </style>";
    print "<table class='ds_box' cellpadding='0' cellspacing='0' id='ds_conclass' style='display: none;'> <tr><td id='ds_calclass'> </td></tr> </table>";
    print "<script type='text/javascript'> var ds_i_date = new Date(); ds_c_month = ds_i_date.getMonth() + 1; ds_c_year = ds_i_date.getFullYear(); function ds_getel(id) { return document.getElementById(id); }  function ds_getleft(el) { var tmp = el.offsetLeft; el = el.offsetParent; while(el) { tmp += el.offsetLeft; el = el.offsetParent; } return tmp; } function ds_gettop(el) { var tmp = el.offsetTop; el = el.offsetParent; while(el) {tmp += el.offsetTop; el = el.offsetParent; } return tmp; } var ds_oe = ds_getel('ds_calclass'); var ds_ce = ds_getel('ds_conclass'); var ds_ob = ''; function ds_ob_clean() { ds_ob = ''; } function ds_ob_flush() {ds_oe.innerHTML = ds_ob; ds_ob_clean(); } function ds_echo(t) { ds_ob += t; } var ds_element;  var ds_monthnames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];  var ds_daynames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']; function ds_template_main_above(t) { return '<table cellpadding=3 cellspacing=1 class=ds_tbl> <tr> <td class=ds_head style=cursor: pointer onclick=ds_py();>&lt;&lt;</td> <td class=ds_head style=cursor: pointer onclick=ds_pm();>&lt;</td> <td class=ds_head style=cursor: pointer onclick=ds_hi(); colspan=3>[Close]</td> <td class=ds_head style=cursor: pointer onclick=ds_nm();>&gt;</td> <td class=ds_head style=cursor: pointer onclick=ds_ny();>&gt;&gt;</td> </tr> <tr> <td colspan=7 class=ds_head>' + t + '</td> </tr> <tr>'; } function ds_template_day_row(t) { return '<td class=ds_subhead>' + t + '</td>'; } function ds_template_new_week() { return '</tr><tr>'; } function ds_template_blank_cell(colspan) { return '<td colspan=' + colspan + '></td>' } function ds_template_day(d, m, y) { return '<td class=ds_cell onclick=ds_onclick(' + d + ',' + m + ',' + y + ')>' + d + '</td>' } function ds_template_main_below() { return '</tr> </table>'; }  function ds_draw_calendar(m, y) { ds_ob_clean(); ds_echo (ds_template_main_above(ds_monthnames[m - 1] + ' ' + y)); for (i = 0; i < 7; i ++) { ds_echo (ds_template_day_row(ds_daynames[i])); } var ds_dc_date = new Date(); ds_dc_date.setMonth(m - 1); ds_dc_date.setFullYear(y); ds_dc_date.setDate(1); if (m == 1 || m == 3 || m == 5 || m == 7 || m == 8 || m == 10 || m == 12) { days = 31; } else if (m == 4 || m == 6 || m == 9 || m == 11) { days = 30; } else { days = (y % 4 == 0) ? 29 : 28; } var first_day = ds_dc_date.getDay(); var first_loop = 1; ds_echo (ds_template_new_week()); if (first_day != 0) { ds_echo (ds_template_blank_cell(first_day)); } var j = first_day; for (i = 0; i < days; i ++) { if (j == 0 && !first_loop) { ds_echo (ds_template_new_week()); } ds_echo (ds_template_day(i + 1, m, y)); first_loop = 0; j ++; j %= 7; } ds_echo (ds_template_main_below()); ds_ob_flush(); ds_ce.scrollIntoView(); } function ds_sh(t) { ds_element = t; var ds_sh_date = new Date(); ds_c_month = ds_sh_date.getMonth() + 1; ds_c_year = ds_sh_date.getFullYear(); ds_draw_calendar(ds_c_month, ds_c_year); ds_ce.style.display = ''; the_left = ds_getleft(t); the_top = ds_gettop(t) + t.offsetHeight; ds_ce.style.left = the_left + 'px'; ds_ce.style.top = the_top + 'px'; ds_ce.scrollIntoView(); } function ds_hi() { ds_ce.style.display = 'none'; } function ds_nm() { ds_c_month ++; if (ds_c_month > 12) { ds_c_month = 1;  ds_c_year++; } ds_draw_calendar(ds_c_month, ds_c_year); } function ds_pm() { ds_c_month = ds_c_month - 1;  if (ds_c_month < 1) { ds_c_month = 12;  ds_c_year = ds_c_year - 1;  } ds_draw_calendar(ds_c_month, ds_c_year); } function ds_ny() { ds_c_year++; ds_draw_calendar(ds_c_month, ds_c_year); } function ds_py() { ds_c_year = ds_c_year - 1;  ds_draw_calendar(ds_c_month, ds_c_year); } function ds_format_date(d, m, y) { m2 = '00' + m; m2 = m2.substr(m2.length - 2); d2 = '00' + d; d2 = d2.substr(d2.length - 2); return d2 + '/' + m2 + '/' + y; } function ds_onclick(d, m, y) { ds_hi(); if (typeof(ds_element.value) != 'undefined') { ds_element.value = ds_format_date(d, m, y); } else if (typeof(ds_element.innerHTML) != 'undefined') { ds_element.innerHTML = ds_format_date(d, m, y); } else { alert (ds_format_date(d, m, y)); } } </script>";
    print "<table width='800' border='0' cellspacing='0' cellpadding='0'><tr><td>";
    if (insertToday() < getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 2)) {
        include "Zinks.php";
    } else {
        if ($x != 300) {
            print "<tr>";
            if ($_SESSION[$session_variable . "VirdiLevel"] != "Meal" && (strpos($userlevel, "15V") !== false || strpos($userlevel, "16V") !== false || strpos($userlevel, "17V") !== false || strpos($userlevel, "22V") !== false || strpos($userlevel, "23V") !== false || strpos($userlevel, "24V") !== false || strpos($userlevel, "25V") !== false || strpos($userlevel, "26V") !== false || strpos($userlevel, "29V") !== false || strpos($userlevel, "31V") !== false || strpos($userlevel, "32V") !== false || strpos($userlevel, "33V") !== false || strpos($userlevel, "34V") !== false)) {
                if (strpos($userlevel, "15V") !== false) {
                    $link = "AlterTime.php";
                } else {
                    if (strpos($userlevel, "16V") !== false) {
                        $link = "AssignProject.php";
                    } else {
                        if (strpos($userlevel, "17V") !== false) {
                            $link = "AlterTime.php";
                        } else {
                            if (strpos($userlevel, "22V") !== false) {
                                $link = "DeleteProcessedRecord.php";
                            } else {
                                if (strpos($userlevel, "23V") !== false) {
                                    $link = "FlagDay.php";
                                } else {
                                    if (strpos($userlevel, "24V") !== false) {
                                        $link = "Proxy.php";
                                    } else {
                                        if (strpos($userlevel, "25V") !== false) {
                                            $link = "OffDay.php";
                                        } else {
                                            if (strpos($userlevel, "26V") !== false) {
                                                $link = "PreApproveOvertime.php";
                                            } else {
                                                if (strpos($userlevel, "29V") !== false) {
                                                    $link = "ExemptLateInEarlyOutMoreBreak.php";
                                                } else {
                                                    if (strpos($userlevel, "31V") !== false) {
                                                        $link = "ShiftRoster.php";
                                                    } else {
                                                        if (strpos($userlevel, "32V") !== false) {
                                                            $link = "FlagRoster.php";
                                                        } else {
                                                            if (strpos($userlevel, "33V") !== false) {
                                                                $link = "DrillMaster.php";
                                                            } else {
                                                                if (strpos($userlevel, "34V") !== false) {
                                                                    $link = "FlagApplication.php";
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
                if ($x == 15 || $x == 16 && strpos($url, "AssignProject.php") !== false || $x == 17 && strpos($url, "AlterTime.php") !== false || $x == 23 || $x == 24 && strpos($url, "Proxy.php") !== false || $x == 25 && (strpos($url, "OffDay.php") !== false || strpos($url, "DeletePreFlaggedRecord.php") !== false) || $x == 26 && strpos($url, "PreApproveOvertime.php") !== false || $x == 29 || $x == 31 || $x == 32 || $x == 22 || $x == 33 || $x == 34) {
                    print "<td width='90' align='center' background='img/over.gif'><a style='text-decoration:none' href='" . $link . "'><font face='Verdana' size='1' color='#FFFFFF'><b>Attendance</b></font></a></td>";
                } else {
                    print "<td width='90' align='center' background='img/under.gif'><a style='text-decoration:none' href='" . $link . "'><font face='Verdana' size='1' color='#000000'><b>Attendance</b></font></a></td>";
                }
            } else {
                print "<td width='90' align='center' background='img/under.gif'><font face='Verdana' size='1' color='#000000'><b>Attendance</b></font></td>";
            }
            print "<td width='5' align='center'>&nbsp;</td>";
            if (strpos($userlevel, "11V") !== false || strpos($userlevel, "12V") !== false || strpos($userlevel, "13V") !== false || strpos($userlevel, "14V") !== false || strpos($userlevel, "16V") !== false || strpos($userlevel, "19V") !== false || strpos($userlevel, "27V") !== false || strpos($userlevel, "28V") !== false || strpos($userlevel, "30V") !== false || strpos($userlevel, "37V") !== false) {
                if (strpos($userlevel, "11V") !== false) {
                    $link = "UserManagement.php";
                } else {
                    if (strpos($userlevel, "12V") !== false) {
                        $link = "ShiftMaster.php";
                    } else {
                        if (strpos($userlevel, "13V") !== false) {
                            $link = "TerminalMaster.php";
                        } else {
                            if (strpos($userlevel, "14V") !== false) {
                                $link = "AssignShift.php";
                            } else {
                                if (strpos($userlevel, "16V") !== false) {
                                    $link = "ProjectMaster.php";
                                } else {
                                    if (strpos($userlevel, "19V") !== false) {
                                        $link = "OtherSetting.php";
                                    } else {
                                        if (strpos($userlevel, "27V") !== false) {
                                            $link = "EmployeeMaster.php";
                                        } else {
                                            if (strpos($userlevel, "28V") !== false) {
                                                $link = "OTDayDate.php";
                                            } else {
                                                if (strpos($userlevel, "30V") !== false) {
                                                    $link = "EmployeeFlagLimit.php";
                                                } else {
                                                    if (strpos($userlevel, "37V") !== false) {
                                                        $link = "WagesCalculationMaster.php";
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
                if ($x == 11 && (strpos($url, "UserManagement.php") !== false || strpos($url, "AssignUserDept.php") !== false || strpos($url, "AssignUserDiv.php") !== false) || $x == 12 && (strpos($url, "ShiftMaster.php") !== false || strpos($url, "ShiftRotation.php") !== false || strpos($url, "ShiftSummaryMaster.php") !== false) || $x == 13 || $x == 14 || $x == 16 && strpos($url, "ProjectMaster.php") !== false || $x == 19 && (strpos($url, "OtherSetting.php") !== false || strpos($url, "MigrateMaster.php") !== false || strpos($url, "Archive.php") !== false) || $x == 27 && strpos($url, "EmployeeMaster.php") !== false || $x == 28 || $x == 30 && strpos($url, "EmployeeFlagLimit.php") !== false || $x == 35 && strpos($url, "WagesCalculationMaster.php") !== false) {
                    print "<td width='90' align='center' background='img/over.gif'><a style='text-decoration:none' href='" . $link . "'><font face='Verdana' size='1' color='#FFFFFF'><b>Settings</b></font></a></td>";
                } else {
                    print "<td width='90' align='center' background='img/under.gif'><a style='text-decoration:none' href='" . $link . "'><font face='Verdana' size='1' color='#000000'><b>Settings</b></font></a></td>";
                }
            } else {
                print "<td width='90' align='center' background='img/under.gif'><font face='Verdana' size='1' color='#000000'><b>Settings</b></font></td>";
            }
            print "<td width='5' align='center'>&nbsp;</td>";
            if (strpos($userlevel, "12R") !== false || strpos($userlevel, "18V") !== false || strpos($userlevel, "19R") !== false || strpos($userlevel, "20V") !== false || strpos($userlevel, "21V") !== false || strpos($userlevel, "11R") !== false || strpos($userlevel, "16R") !== false || strpos($userlevel, "17R") !== false || strpos($userlevel, "24R") !== false || strpos($userlevel, "30R") !== false || strpos($userlevel, "26R") !== false || strpos($userlevel, "27R") !== false) {
                if (strpos($userlevel, "18V") !== false) {
                    $link = "ReportAttendance.php";
                } else {
                    if (strpos($userlevel, "20V") !== false) {
                        $link = "ReportClockingLog.php";
                    } else {
                        if (strpos($userlevel, "21V") !== false) {
                            $link = "ReportDailyRoster.php";
                        } else {
                            if (strpos($userlevel, "12R") !== false) {
                                $link = "ReportShiftRotation.php";
                            } else {
                                if (strpos($userlevel, "19R") !== false) {
                                    $link = "ReportProcessLog.php";
                                } else {
                                    if (strpos($userlevel, "11R") !== false) {
                                        $link = "ReportUserInfo.php";
                                    } else {
                                        if (strpos($userlevel, "16R") !== false) {
                                            $link = "ReportProject.php";
                                        } else {
                                            if (strpos($userlevel, "17R") !== false) {
                                                $link = "ReportAlterTime.php";
                                            } else {
                                                if (strpos($userlevel, "24R") !== false) {
                                                    $link = "ReportAlterTime.php";
                                                } else {
                                                    if (strpos($userlevel, "30R") !== false) {
                                                        $link = "ReportFlagLimit.php";
                                                    } else {
                                                        if (strpos($userlevel, "26R") !== false) {
                                                            $link = "ReportPreApproval.php";
                                                        } else {
                                                            if (strpos($userlevel, "27R") !== false) {
                                                                $link = "ReportEmployee.php";
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
                if ($x == 12 && strpos($url, "ReportShiftRotation.php") !== false || $x == 18 || $x == 19 && strpos($url, "ReportProcessLog.php") !== false || $x == 20 || $x == 21 || $x == 11 && (strpos($url, "ReportUserInfo.php") !== false || strpos($url, "ReportUserTransact.php") !== false || strpos($url, "ReportUserRight.php") !== false || strpos($url, "ReportUserDept.php") !== false || strpos($url, "ReportUserDiv.php") !== false) || $x == 16 && strpos($url, "ReportProject.php") !== false || $x == 17 && strpos($url, "ReportAlterTime.php") !== false || $x == 24 && strpos($url, "ReportAlterTime.php") !== false || $x == 30 && strpos($url, "ReportFlagLimit.php") !== false || $x == 26 && strpos($url, "ReportPreApproval.php") !== false || $x == 27 && (strpos($url, "ReportEmployee.php") !== false || strpos($url, "ReportADA.php") !== false)) {
                    print "<td width='90' align='center' background='img/over.gif'><a style='text-decoration:none' href='" . $link . "'><font face='Verdana' size='1' color='#FFFFFF'><b>Reports</b></font></a></td>";
                } else {
                    print "<td width='90' align='center' background='img/under.gif'><a style='text-decoration:none' href='" . $link . "'><font face='Verdana' size='1' color='#000000'><b>Reports</b></font></a></td>";
                }
            } else {
                print "<td width='90' align='center' background='img/under.gif'><font face='Verdana' size='1' color='#000000'><b>Reports</b></font></td>";
            }
            print "<td width='5' align='center'>&nbsp;</td>";
            if ($_SESSION[$session_variable . "VirdiLevel"] == "Meal") {
                print "<td width='90' background='img/under.gif' align='center'><font face='Verdana' size='1' color='#000000'><b>Dashboard</b></font></td>";
            } else {
                if ($x == 100) {
                    print "<td width='90' background='img/over.gif' align='center'><a style='text-decoration:none' href='Welcome.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Dashboard</b></font></a></td>";
                } else {
                    print "<td width='90' background='img/under.gif' align='center'><a style='text-decoration:none' href='Welcome.php'><font face='Verdana' size='1' color='#000000'><b>Dashboard</b></font></a></td>";
                }
            }
            print "<td width='5' align='center'>&nbsp;</td>";
            if ($x == 200) {
                print "<td width='90' background='img/over.gif' align='center'><a style='text-decoration:none' href='Password.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Password</b></font></a></td>";
            } else {
                print "<td width='90' background='img/under.gif' align='center'><a style='text-decoration:none' href='Password.php'><font face='Verdana' size='1' color='#000000'><b>Password</b></font></a></td>";
            }
            print "<td width='5' align='center'>&nbsp;</td>";
            print "<td width='90' background='img/under.gif' align='center'><a style='text-decoration:none' href='Login.php?act=signout'><font face='Verdana' size='1' color='#000000'><b>Logout</b></font></a></td>";
            print "<td width='235' align='right'><font face='Verdana' size='1' color='#0E6ECE'><b>&nbsp;</b></font>&nbsp;&nbsp;</td>";
            print "</tr>";
        }
        print "<tr>";
        displayLowerLinks($username, $userlevel, $x, $y, $module, $link, $heading);
        print "</tr>";
    }
    print "</td></tr></table>";
}

function displayLowerLinks($username, $userlevel, $x, $module, $link, $heading) {
    $url = $_SERVER["REQUEST_URI"];
    global $session_variable;
    $virdiLevel = $_SESSION[$session_variable . "VirdiLevel"];
    if ($x == 100 || $x == 200) {
        print "<tr><td colspan='6' bgcolor='#0E6ECE' background='img/revo-left.gif' align='center' height='20' style='background-repeat:no-repeat'><font face='Verdana' size='1' color='#FFFFFF'><b>&nbsp;</b></font></td>";
        print "<td colspan='6' bgcolor='#0E6ECE' background='img/revo-right.gif' align='right' height='20' style='background-repeat:no-repeat' style='background-position:right'><font face='Verdana' size='1' color='#FFFFFF'><b>&nbsp;</b></font></td></tr>";
    } else {
        if ($x == 300) {
            print "<tr><td colspan='12' bgcolor='#0E6ECE' align='right' height='20'><font face='Verdana' size='1' color='#FFFFFF'><b>";
            print "<a style='text-decoration:none' href='Checklist.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Checklist</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
            print "<a style='text-decoration:none' href='SiteMap.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Site Map</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
            print "<a style='text-decoration:none' href='CategoryHelp.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Category Help</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
            print "<a style='text-decoration:none' href='SOP-TimeKeepers.php'><font face='Verdana' size='1' color='#FFFFFF'><b>SOP-Time Keepers</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
            print "<a style='text-decoration:none' href='SOP-HOD.php'><font face='Verdana' size='1' color='#FFFFFF'><b>SOP-HOD</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
            print "<a style='text-decoration:none' href='SOP-HR.php'><font face='Verdana' size='1' color='#FFFFFF'><b>SOP-HR</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
            print "<a style='text-decoration:none' href='SOP-IT.php'><font face='Verdana' size='1' color='#FFFFFF'><b>SOP-IT</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
            print "</b></font></td></tr>";
        } else {
            if ($x == 12 && strpos($url, "ReportShiftRotation.php") !== false || $x == 18 || $x == 19 && strpos($url, "ReportProcessLog.php") !== false || $x == 20 || $x == 21 || $x == 11 && (strpos($url, "ReportUserInfo.php") !== false || strpos($url, "ReportUserTransact.php") !== false || strpos($url, "ReportUserRight.php") !== false || strpos($url, "ReportUserDept.php") !== false || strpos($url, "ReportUserDiv.php") !== false) || $x == 16 && strpos($url, "ReportProject.php") !== false || $x == 17 && strpos($url, "ReportAlterTime.php") !== false || $x == 24 && strpos($url, "ReportAlterTime.php") !== false || $x == 30 && strpos($url, "ReportFlagLimit.php") !== false || $x == 26 && strpos($url, "ReportPreApproval.php") !== false || $x == 27 && (strpos($url, "ReportEmployee.php") !== false || strpos($url, "ReportADA.php") !== false)) {
                if (strpos($userlevel, "18R") !== false || strpos($userlevel, "27R") !== false || strpos($userlevel, "12R") !== false || strpos($userlevel, "19R") !== false) {
                    print "<tr><td colspan='12' bgcolor='#0E6ECE' align='right' height='20'><font face='Verdana' size='1' color='#FFFFFF'><b>";
                    if (strpos($userlevel, "18R") !== false) {
                        print "<a style='text-decoration:none' href='ReportAttendance.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Attendance </b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                        if ($virdiLevel != "Meal") {
                            print "<a style='text-decoration:none' href='ReportLateArrival.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Late In</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                            print "<a style='text-decoration:none' href='ReportMoreBreak.php'><font face='Verdana' size='1' color='#FFFFFF'><b>More Break</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                            print "<a style='text-decoration:none' href='ReportEarlyExit.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Early Out</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                            print "<a style='text-decoration:none' href='ReportLIEO.php'><font face='Verdana' size='1' color='#FFFFFF'><b>LI/EO</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                            print "<a style='text-decoration:none' href='ReportAbsence.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Absence</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                            print "<a style='text-decoration:none' href='ReportAbsenceCount.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Abs Count</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                        }
                        if ($_SESSION[$session_variable . "ExitTerminal"] == "Yes" && $virdiLevel == "Classic") {
                            print "<a style='text-decoration:none' href='ReportExitTerminalError.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Exit Error</b></font></a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                        }
                    }
                    if (strpos($userlevel, "27R") !== false) {
                        print "<a style='text-decoration:none' href='ReportEmployee.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Employees</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                    }
                    if (strpos($userlevel, "12R") !== false && $_SESSION[$session_variable . "RotateShift"] == "Yes" && $virdiLevel == "Classic") {
                        print "<a style='text-decoration:none' href='ReportShiftRotation.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Rotation</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                    }
                    if (strpos($userlevel, "19R") !== false && $virdiLevel != "Meal") {
                        print "<a style='text-decoration:none' href='ReportProcessLog.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Process</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                    }
                    print "</b></font></td></tr>";
                }
                if (strpos($userlevel, "20R") !== false && $virdiLevel != "Meal") {
                    print "<tr><td colspan='12'><img src='img/1.gif' loading='lazy' height='1' width='100%'></td></tr>";
                    print "<tr><td colspan='12' bgcolor='#0E6ECE' align='right' height='20'><font face='Verdana' size='1' color='#FFFFFF'><b>";
                    print "<a style='text-decoration:none' href='ReportOddLog.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Odd Log</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                    print "<a style='text-decoration:none' href='ReportClockingLog.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Raw Log</b></font></a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                    print "<a style='text-decoration:none' href='ReportDailyClocking.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Processed Log (Daily Routine)</b></font></a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                    print "<a style='text-decoration:none' href='ReportWeeklyClocking.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Processed Log (Weekly Routine)</b></font></a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                    if (strpos($userlevel, "25R") !== false && $virdiLevel == "Classic") {
                        print "<a style='text-decoration:none' href='ReportPreFlag.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Pre Flag</b></font></a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                    }
                    print "</b></font></td></tr>";
                }
                if (strpos($userlevel, "21R") !== false && $virdiLevel != "Meal") {
                    print "<tr><td colspan='12'><img src='img/1.gif' height='1' width='100%' loading='lazy'></td></tr>";
                    print "<tr><td colspan='12' bgcolor='#0E6ECE' align='right' height='20'><font face='Verdana' size='1' color='#FFFFFF'><b>";
                    print "<a style='text-decoration:none' href='ReportWork.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Work Report</b></font></a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                    print "<a style='text-decoration:none' href='ReportDailyRoster.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Roster</b></font></a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                    if ($virdiLevel == "Classic") {
                        print "<a style='text-decoration:none' href='ReportPeriodicSummary.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Day Summary</b></font></a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                        print "<a style='text-decoration:none' href='ReportMonthlyHours.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Hour Summary</b></font></a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                        print "<a style='text-decoration:none' href='ReportMonthSummary.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Month Summary</b></font></a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                        print "<a style='text-decoration:none' href='ReportShiftSnapShot.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Shift Summary</b></font></a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                        print "<a style='text-decoration:none' href='ReportGroupSummary.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Group Summary</b></font></a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                    }
                    print "<a style='text-decoration:none' href='ReportAttendanceSnapShot.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Snapshot</b></font></a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                    print "</b></font></td></tr>";
                }
                if ($virdiLevel != "Meal" && (strpos($userlevel, "11R") !== false || strpos($userlevel, "17R") !== false || strpos($userlevel, "30R") !== false || strpos($userlevel, "16R") !== false && $_SESSION[$session_variable . "Project"] == "Yes" || strpos($userlevel, "26R") !== false || strpos($userlevel, "24R") !== false || strpos($userlevel, "27R") !== false)) {
                    print "<tr><td colspan='12'><img src='img/1.gif' height='1' width='100%' loading='lazy'></td></tr>";
                    print "<tr><td colspan='12' bgcolor='#0E6ECE' align='right' height='20'><font face='Verdana' size='1' color='#FFFFFF'><b>";
                    if (strpos($userlevel, "16R") !== false && $_SESSION[$session_variable . "Project"] == "Yes" && $virdiLevel == "Classic") {
                        print "<a style='text-decoration:none' href='ReportProject.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Projects</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                    }
                    if (strpos($userlevel, "26R") !== false && $virdiLevel == "Classic") {
                        print "<a style='text-decoration:none' href='ReportPreApproval.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Pre Approval</b></font></a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                    }
                    if (strpos($userlevel, "17R") !== false || strpos($userlevel, "24R") !== false) {
                        print "<a style='text-decoration:none' href='ReportAlterTime.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Time Alteration</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                    }
                    if (strpos($userlevel, "30R") !== false && $virdiLevel == "Classic") {
                        print "<a style='text-decoration:none' href='ReportFlagLimit.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Flag Limit</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                    }
                    if (strpos($userlevel, "27R") !== false && $virdiLevel == "Classic") {
                        print "<a style='text-decoration:none' href='ReportADA.php'><font face='Verdana' size='1' color='#FFFFFF'><b>ADA</b></font></a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                    }
                    if (strpos($userlevel, "11R") !== false) {
                        print "<a style='text-decoration:none' href='ReportUserTransact.php'><font face='Verdana' size='1' color='#FFFFFF'><b>User Transaction</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                        print "<a style='text-decoration:none' href='ReportUserInfo.php'><font face='Verdana' size='1' color='#FFFFFF'><b>User Info</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                        print "<a style='text-decoration:none' href='ReportUserRight.php'><font face='Verdana' size='1' color='#FFFFFF'><b>User Rights</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                        print "<a style='text-decoration:none' href='ReportUserDept.php'><font face='Verdana' size='1' color='#FFFFFF'><b>User Depts</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                        print "<a style='text-decoration:none' href='ReportUserDiv.php'><font face='Verdana' size='1' color='#FFFFFF'><b>User Div</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                    }
                    print "</td></tr>";
                    return NULL;
                }
            } else {
                if ($x == 15 || $x == 16 && strpos($url, "AssignProject.php") !== false || $x == 17 && strpos($url, "AlterTime.php") !== false || $x == 23 || $x == 24 && strpos($url, "Proxy.php") !== false || $x == 25 && (strpos($url, "OffDay.php") !== false || strpos($url, "DeletePreFlaggedRecord.php") !== false) || $x == 26 && strpos($url, "PreApproveOvertime.php") !== false || $x == 34 && strpos($url, "FlagApplication.php") !== false || $x == 29 || $x == 31 || $x == 32 || $x == 22 || $x == 33 || $x == 34) {
                    print "<tr><td bgcolor='#0E6ECE' align='center' height='20'><font face='Verdana' size='1' color='#FFFFFF'><b>&nbsp;</b></font></td>";
                    print "<td colspan='11' bgcolor='#0E6ECE' align='right' height='20'><font face='Verdana' size='1' color='#FFFFFF'><b>";
                    if (strpos($userlevel, "26V") !== false && $virdiLevel == "Classic") {
                        print "<a style='text-decoration:none' href='PreApproveOvertime.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Pre-Approve Overtime</b></font></a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                    }
                    if (strpos($userlevel, "15V") !== false) {
                        print "<a style='text-decoration:none' href='ApproveOvertime.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Approve Overtime (Details)</b></font></a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                        print "<a style='text-decoration:none' href='ApproveOvertimeSummary.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Approve Overtime (Summary)</b></font></a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                    }
                    if (strpos($userlevel, "16V") !== false && $_SESSION[$session_variable . "Project"] == "Yes" && $virdiLevel == "Classic") {
                        print "<a style='text-decoration:none' href='AssignProject.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Assign Project</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                    }
                    if (strpos($userlevel, "33V") !== false && $virdiLevel == "Classic") {
                        print "<a style='text-decoration:none' href='DrillMaster.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Drill</b></font></a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                    }
                    print "</b></font></td></tr>";
                    print "<tr><td colspan='12'><img src='img/1.gif' height='1' width='100%' loading='lazy'></td></tr>";
                    print "<td colspan='12' bgcolor='#0E6ECE' align='right' height='20'><font face='Verdana' size='1' color='#FFFFFF'><b>";
                    if (strpos($userlevel, "24V") !== false) {
                        print "<a style='text-decoration:none' href='Proxy.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Proxy</b></font></a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                    }
                    if (strpos($userlevel, "29V") !== false && $virdiLevel == "Classic") {
                        print "<a style='text-decoration:none' href='ExemptLateInEarlyOutMoreBreak.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Exempt LateIn/ EarlyOut/ MoreBreak</b></font></a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                    }
                    if (strpos($userlevel, "34V") !== false && $virdiLevel == "Classic") {
                        print "<a style='text-decoration:none' href='FlagApplication.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Flag Application</b></font></a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                    }
                    if (strpos($userlevel, "25V") !== false && $virdiLevel == "Classic") {
                        print "<a style='text-decoration:none' href='OffDay.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Pre Flag</b></font></a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                    }
                    if (strpos($userlevel, "23V") !== false && $virdiLevel == "Classic") {
                        print "<a style='text-decoration:none' href='FlagDay.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Post Flag</b></font></a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                    }
                    if (strpos($userlevel, "32V") !== false && $virdiLevel == "Classic") {
                        print "<a style='text-decoration:none' href='FlagRoster.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Flag Roster</b></font></a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                    }
                    if (strpos($userlevel, "31V") !== false && $_SESSION[$session_variable . "UseShiftRoster"] == "Yes" && $virdiLevel == "Classic") {
                        print "<a style='text-decoration:none' href='ShiftRoster.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Shift Roster</b></font></a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                    }
                    print "</b></font></td></tr>";
                    print "<tr><td colspan='12'><img src='img/1.gif' height='1' width='100%' loading='lazy'></td></tr>";
                    print "<td colspan='12' bgcolor='#0E6ECE' align='right' height='20'><font face='Verdana' size='1' color='#FFFFFF'><b>";
                    if (strpos($userlevel, "17V") !== false) {
                        print "<a style='text-decoration:none' href='AlterTime.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Alter Time</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                    }
                    if (strpos($userlevel, "22V") !== false) {
                        print "<a style='text-decoration:none' href='DeleteProcessedRecord.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Delete Processed Logs</b></font></a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                        print "<a style='text-decoration:none' href='DeleteProcessedRawRecord.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Delete Processed Raw Logs</b></font></a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                    }
                    if (strpos($userlevel, "25D") !== false && $virdiLevel == "Classic") {
                        print "<a style='text-decoration:none' href='DeletePreFlaggedRecord.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Delete Pre-Flagged Un-Processed Logs</b></font></a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                    }
                    print "</b></font></td></tr>";
                    return NULL;
                }
                if ($x == 11 && (strpos($url, "UserManagement.php") !== false || strpos($url, "AssignUserDept.php") !== false || strpos($url, "AssignUserDiv.php") !== false) || $x == 12 && (strpos($url, "ShiftMaster.php") !== false || strpos($url, "ShiftRotation.php") !== false || strpos($url, "ShiftSummaryMaster.php") !== false) || $x == 13 || $x == 14 || $x == 16 && strpos($url, "ProjectMaster.php") !== false || $x == 19 && (strpos($url, "OtherSetting.php") !== false || strpos($url, "MigrateMaster.php") !== false || strpos($url, "Archive.php") !== false) || $x == 27 && strpos($url, "EmployeeMaster.php") !== false || $x == 28 || $x == 30 && strpos($url, "EmployeeFlagLimit.php") !== false || $x == 35 && strpos($url, "WagesCalculationMaster.php") !== false) {
                    if (strpos($userlevel, "11V") !== false) {
                        print "<tr><td colspan='12' bgcolor='#0E6ECE' align='right' height='20'><font face='Verdana' size='1' color='#FFFFFF'><b>";
                        print "<a style='text-decoration:none' href='UserManagement.php'><font face='Verdana' size='1' color='#FFFFFF'><b>User</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                        if ($virdiLevel != "Meal") {
                            print "<a style='text-decoration:none' href='AssignUserDept.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Assign User Dept Access</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                            print "<a style='text-decoration:none' href='AssignUserDiv.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Assign User Div Access</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                        }
                        print "</td></tr>";
                        print "<tr><td colspan='12'><img src='img/1.gif' height='1' width='100%' loading='lazy'></td></tr>";
                    }
                    if ($virdiLevel != "Meal" && (strpos($userlevel, "28V") !== false || strpos($userlevel, "16A") !== false && $_SESSION[$session_variable . "Project"] == "Yes" || strpos($userlevel, "13V") !== false)) {
                        print "<tr><td colspan='12' bgcolor='#0E6ECE' align='right' height='20'><font face='Verdana' size='1' color='#FFFFFF'><b>";
                        if (strpos($userlevel, "28V") !== false) {
                            print "<a style='text-decoration:none' href='OTDayDate.php'><font face='Verdana' size='1' color='#FFFFFF'><b>OT Days/Dates</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                            if ($virdiLevel == "Classic") {
                                print "<a style='text-decoration:none' href='ProxyEmployeeExempt.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Proxy Exemption</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                                print "<a style='text-decoration:none' href='OTEmployeeExempt.php'><font face='Verdana' size='1' color='#FFFFFF'><b>OT Exemption</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                                print "<a style='text-decoration:none' href='OTEmployeeExemptOTDay.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Special OT Days for Exempted Employees</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                            }
                        }
                        if (strpos($userlevel, "16A") !== false && $_SESSION[$session_variable . "Project"] == "Yes" && $virdiLevel == "Classic") {
                            print "<a style='text-decoration:none' href='ProjectMaster.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Projects</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                        }
                        if (strpos($userlevel, "13V") !== false && $virdiLevel == "Classic") {
                            print "<a style='text-decoration:none' href='GroupMaster.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Report Groups</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                        }
                        print "</b></font></td></tr>";
                        print "<tr><td colspan='12'><img src='img/1.gif' height='1' width='100%' loading='lazy'></td></tr>";
                    }
                    if (strpos($userlevel, "27V") !== false || strpos($userlevel, "30V") !== false || strpos($userlevel, "12V") !== false || strpos($userlevel, "14V") !== false || strpos($userlevel, "13V") !== false || strpos($userlevel, "19V") !== false || strpos($userlevel, "37V") !== false) {
                        print "<tr><td colspan='12' bgcolor='#0E6ECE' align='right' height='20'><font face='Verdana' size='1' color='#FFFFFF'><b>";
                        if (strpos($userlevel, "27V") !== false) {
                            print "<a style='text-decoration:none' href='EmployeeMaster.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Employee</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                        }
                        if (strpos($userlevel, "30V") !== false && $virdiLevel == "Classic") {
                            print "<a style='text-decoration:none' href='EmployeeFlagLimit.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Flag Limit</b></font></a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                        }
                        if (strpos($userlevel, "12V") !== false && $virdiLevel != "Meal") {
                            print "<a style='text-decoration:none' href='ShiftMaster.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Shift</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                            if ($virdiLevel == "Classic") {
                                print "<a style='text-decoration:none' href='ShiftSummaryMaster.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Shift S</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                            }
                            if ($_SESSION[$session_variable . "RotateShift"] == "Yes" && $virdiLevel == "Classic") {
                                print "<a style='text-decoration:none' href='ShiftRotation.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Shift Rotation</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                            }
                        }
                        if (strpos($userlevel, "14V") !== false && $virdiLevel != "Meal") {
                            print "<a style='text-decoration:none' href='AssignShift.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Assign Shift</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                        }
                        if (strpos($userlevel, "13V") !== false && $virdiLevel == "Classic") {
                            print "<a style='text-decoration:none' href='AssignTerminal.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Assign Trml</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                            if ($_SESSION[$session_variable . "ExitTerminal"] == "Yes") {
                                print "<a style='text-decoration:none' href='ExitTerminal.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Exit Trml</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                            }
                            print "<a style='text-decoration:none' href='MealTerminal.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Meal Trml</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                        }
                        if (strpos($userlevel, "19V") !== false) {
                            print "<a style='text-decoration:none' href='Archive.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Archive</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                            print "<a style='text-decoration:none' href='MigrateMaster.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Migration</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                            print "<a style='text-decoration:none' href='OtherSetting.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Global</b></font><a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                        }
                        if (strpos($userlevel, "37V") !== false) {
                            print "<a style='text-decoration:none' href='WagesCalculationMaster.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Wages Calculation Master</b></font></a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                            print "<a style='text-decoration:none' href='WagesCalculation.php'><font face='Verdana' size='1' color='#FFFFFF'><b>Wages Calculation</b></font></a>&nbsp;&nbsp;|&nbsp;&nbsp;";
                        }
                        print "</b></font></td></tr>";
                    }
                }
            }
        }
    }
}

function displaylinksChild($userlevel, $x, $y, $module, $link, $heading) {
    if (strpos($userlevel, $module) !== false) {
        if ($x == $y) {
            print "<td width='90' align='center' background='img/over.gif'><a style='text-decoration:none' href='" . $link . "'><font face='Verdana' size='1' color='#FFFFFF'><b>" . $heading . "</b></font></a></td>";
        } else {
            print "<td width='90' align='center' background='img/under.gif'><a style='text-decoration:none' href='" . $link . "'><font face='Verdana' size='1' color='#000000'><b>" . $heading . "</b></font></a></td>";
        }
    } else {
        print "<td width='90' align='center' background='img/under.gif'><font face='Verdana' size='1' color='#000000'><b>" . $heading . "</b></font></td>";
    }
    print "<td width='5' align='center'>&nbsp;</td>";
}

function displayFooter($x) {
    if ($x == 9333) {
        return NULL;
    }
    if ($x == 5845) {
        return NULL;
    }
    if ($x == 7762) {
        return NULL;
    }
    if ($x == 7465) {
        print "<table width='800'><tr><td width='50%'><font size='5'><i><b></b></i></font> <font face='Verdana' size='1'>Access Control and Attendance Solution <br>Distributed by <b>Eggle Search International</b> <br>Plot 15, Ola Adejola Crescent, Ojokoro, Ijaiye, Lagos <br>Tel: 0812 894 6340 <br>Email: info@egglesearch-ng.com</font></td><td align='right' width='50%'><img src='img/logo.gif' loading='lazy'></td></tr></table>";
    } else {
        if ($x == 8837) {
            print "<table width='800'><tr><td width='50%'><font size='5'><i><b></b></i></font> <font face='Verdana' size='1'>Access Control and Attendance Solution <br>Distributed by <b>KeamTeck Nig Ltd</b> <br>Plot 8, Oyelola Anifiowodshe Street, Ikeja, Lagos<br>Tel: +234 803 492 6634 <br>Email: Keamtecknigltd@gmail.com</font></td><td align='right' width='50%'><img src='img/logo.gif' loading='lazy'></td></tr></table>";
        }
    }
}

function displayTextbox($field, $name, $value, $prints, $maxlength, $w1, $w2) {
    global $session_variable;

    // Set max length and validation for field
    $maxlen = "1024";
    if ($maxlength == "") {
        $maxlength = "30";
    } else {
        if ($maxlength <= 30) {
            $maxlen = $maxlength;
        } else {
            $maxlength = 30;
        }
    }

    // Check if the field is read-only or printable
    if ($prints == "yes") {
        echo "<div class='mb-3'>
                <label class='form-label'><font face='Verdana' size='2'>" . $name . "</font></label>
                <input type='hidden' name='" . $field . "' value='" . $value . "'>
                <div><font face='Verdana' size='2'><b>&nbsp;&nbsp;" . $value . "</b></font></div>
              </div>";
    } else {
        echo "<div class='mb-3'>
                <label class='form-label'><font face='Verdana' size='2'>" . $name . "</font></label>
                <input type='text' name='" . $field . "' size='" . $maxlength . "' maxlength='" . $maxlen . "' value='" . $value . "' class='form-control'";

        // Add date picker functionality for specific fields
        if (($field == "txtFrom" || $field == "txtTo") && $_SESSION[$session_variable . "usertype"] == "Yes") {
            echo " onclick='ds_sh(this);'";
        }

        echo "></div>";
    }
}

/* For Gloabl Setting */

function globaldisplayTextbox($field, $name, $value, $prints, $maxlength, $w1, $w2) {
    global $session_variable;

    // Set max length and validation for field
    $maxlen = "1024";
    if ($maxlength == "") {
        $maxlength = "30";
    } else {
        if ($maxlength <= 30) {
            $maxlen = $maxlength;
        } else {
            $maxlength = 30;
        }
    }

    // Check if the field is read-only or printable
    if ($prints == "yes") {
        echo "<div class='mb-3'>
                <label class='form-label'><font face='Verdana' size='2'>" . $name . "</font></label>
                <input type='hidden' name='" . $field . "' value='" . $value . "'>
                <div><font face='Verdana' size='2'><b>&nbsp;&nbsp;" . $value . "</b></font></div>
              </div>";
    } else {
        echo "<div class='mb-3'>
                <label class='form-label'><font face='Verdana' size='2'>" . $name . "</font></label>
                <input type='text' name='" . $field . "' size='" . $maxlength . "' maxlength='" . $maxlen . "' value='" . $value . "' class='form-control'";

        // Add date picker functionality for specific fields
        if (($field == "txtFrom" || $field == "txtTo") && $_SESSION[$session_variable . "usertype"] == "Yes") {
            echo " onclick='ds_sh(this);'";
        }

        echo "></div>";
    }
}

function displayTextboxs($field, $name, $value, $prints, $maxlength, $w1, $w2, $condition) {
    global $session_variable;
    if ($w1 == "") {
        $w1 = "50%";
    }
    if ($w2 == "") {
        $w2 = "50%";
    }
    if ($maxlength == "") {
        $maxlength = "30";
    }
    if ($prints == "yes") {
        print "<td align='right' width='" . $w1 . "'><input type='hidden' name='" . $field . "' value='" . $value . "'><font face='Verdana' size='2'>" . $name . "</font></td><td width='" . $w2 . "'><font face='Verdana' size='2'><b>&nbsp;&nbsp;" . $value . "</b></font></td>";
    } else {
        print "<td align='right' width='" . $w1 . "'><font face='Verdana' size='2'>" . $name . "</font></td><td width='" . $w2 . "'><input " . $condition . " size='" . $maxlength . "' name='" . $field . "'";
        if (($field == "txtFrom" || $field == "txtTo") && $_SESSION[$session_variable . "usertype"] == "Yes") {
            print " onclick='ds_sh(this);' ";
        }
        print " maxlength='" . $maxlength . "' value='" . $value . "' class='form-control'></td>";
    }
}

function displayTextarea($field, $name, $value, $prints, $rows, $cols, $w1, $w2) {
    if ($w1 == "") {
        $w1 = "50%";
    }
    if ($w2 == "") {
        $w2 = "50%";
    }
    if ($rows == "") {
        $rows = "5";
    }
    if ($cols == "") {
        $cols = "30";
    }
    if ($prints == "yes") {
        print "<td align='right' width='" . $w1 . "'><input type='hidden' name='" . $field . "' value='" . $value . "'><font face='Verdana' size='2'>" . $name . "</font></td><td width='" . $w2 . "%'><font size='2' face='Verdana'><b>&nbsp;&nbsp;" . $value . "</b></font></td>";
    } else {
        print "<td align='right' width='" . $w1 . "'><font size='2' face='Verdana'>" . $name . "</font></td><td width='" . $w2 . "%'><textarea name='" . $field . "' rows='" . $rows . "' cols='" . $cols . "' class='form-control'>" . $value . "</textarea></td>";
    }
}

//function displayList($field, $name, $value, $prints, $conn, $query, $onChange, $w1, $w2)
//{
//    global $db_type;
//    if ($w1 == "") {
//        $w1 = "50%";
//    }
//    if ($w2 == "") {
//        $w2 = "50%";
//    }
//    print "<td align='right' width='" . $w1 . "'><font size='2' face='Verdana'>" . $name . "</font></td><td width='" . $w2 . "'>";
////    print "<td align='left'><font size='2' face='Verdana'>" . $name . "</font></td><td>";
//    if ($prints == "yes") {
//        print "<input type='hidden' name='" . $field . "' value='" . $value . "' class='form-control'>";
//    } else {
//        if ($onChange == "") {
//            print "<select name='" . $field . "' class='select2 form-select shadow-none'>";
//        } else {
//            print "<select name='" . $field . "' " . $onChange . " class='select2 form-select shadow-none'>";
//        }
//    }
//    if ($prints != "yes") {
//        print "<option value=''>---</option>";
//    }
//    if ($db_type == "1") {
//        $result = odbc_exec($conn, $query);
//        while (odbc_fetch_into($result, $cur)) {
//            if ($cur[0] == $value) {
//                if ($prints == "yes") {
//                    print "<font size='2' face='Verdana'><b>&nbsp;&nbsp;" . $cur[1] . "</b></font>";
//                } else {
//                    print "<option selected value='" . $cur[0] . "'>" . $cur[1] . "</option>";
//                }
//            } else {
//                if ($prints != "yes") {
//                    print "<option value='" . $cur[0] . "'>" . $cur[1] . "</option>";
//                }
//            }
//        }
//    } else {
//        if ($db_type != "2") {
//            if ($db_type == "3") {
//                $result = mysqli_query($conn, $query);
//                while ($cur = mysqli_fetch_row($result)) {
//                    if ($cur[0] == $value) {
//                        if ($prints == "yes") {
//                            print "<font size='2' face='Verdana'><b>&nbsp;&nbsp;" . $cur[1] . "</b></font>";
//                        } else {
//                            print "<option selected value='" . $cur[0] . "'>" . $cur[1] . "</option>";
//                        }
//                    } else {
//                        if ($prints != "yes") {
//                            print "<option value='" . $cur[0] . "'>" . $cur[1] . "</option>";
//                        }
//                    }
//                }
//            } else {
//                if ($db_type == "4") {
//                    $result = oci_parse($conn, $query);
//                    oci_execute($result);
//                    while ($cur = oci_fetch_array($result, OCI_BOTH)) {
//                        if ($cur[0] == $value) {
//                            if ($prints == "yes") {
//                                print "<font size='2' face='Verdana'><b>&nbsp;&nbsp;" . $cur[1] . "</b></font>";
//                            } else {
//                                print "<option selected value='" . $cur[0] . "'>" . $cur[1] . "</option>";
//                            }
//                        } else {
//                            if ($prints != "yes") {
//                                print "<option value='" . $cur[0] . "'>" . $cur[1] . "</option>";
//                            }
//                        }
//                    }
//                }
//            }
//        }
//    }
//    if ($prints == "yes") {
//        print "&nbsp;";
//    } else {
//        print "</select>";
//    }
//    print "</td>";
//}
function displayList($field, $name, $value, $prints, $conn, $query, $onChange, $w1, $w2) {
    global $db_type;

    // Default widths if not provided
    if (empty($w1)) {
        $w1 = "50%";
    }
    if (empty($w2)) {
        $w2 = "50%";
    }

    // Start of the form group (Bootstrap form structure)
    print "<div class='mb-3'>";

    // Label for the field
    print "<label class='form-label'>" . $name . "</label>";

    // Input/Select field column
//    print "<div class='col-sm-8' style='width: $w2;'>";
    // If prints == 'yes', display the value as plain text and a hidden input
    if ($prints == "yes") {
        print "<input type='hidden' name='" . htmlspecialchars($field) . "' value='" . htmlspecialchars($value) . "' class='form-control'>";
        print "<span class='form-control-plaintext' style='font-size: 0.875rem;'><strong>" . htmlspecialchars($value) . "</strong></span>";
    } else {
        // Otherwise, display a select field
        $selectAttributes = "name='" . htmlspecialchars($field) . "' class='form-select select2 shadow-none' id='$field'";
//        $selectAttributes = "name='" . htmlspecialchars($field) . "' class='form-select select2 shadow-none' id='lstSetShift'";

        /*if (!empty($onChange)) {
            $selectAttributes .= " " . htmlspecialchars($onChange);
        }*/
        if (!empty($onChange)) {
            $selectAttributes .= " " . $onChange; // Do NOT htmlspecialchars() here
        }
        print "<select $selectAttributes>";
        print "<option value=''>---</option>";

        // Handle different database types and execute the query accordingly
        if ($db_type == "1") {
            $result = odbc_exec($conn, $query);
            while (odbc_fetch_into($result, $cur)) {
                $selected = ($cur[0] == $value) ? "selected" : "";
                print "<option value='" . htmlspecialchars($cur[0]) . "' $selected>" . htmlspecialchars($cur[1]) . "</option>";
            }
        } elseif ($db_type == "3") {
            $result = mysqli_query($conn, $query);
            while ($cur = mysqli_fetch_row($result)) {
                $selected = ($cur[0] == $value) ? "selected" : "";
                print "<option value='" . htmlspecialchars($cur[0]) . "' $selected>" . htmlspecialchars($cur[1]) . "</option>";
            }
        } elseif ($db_type == "4") {
            $result = oci_parse($conn, $query);
            oci_execute($result);
            while ($cur = oci_fetch_array($result, OCI_BOTH)) {
                $selected = ($cur[0] == $value) ? "selected" : "";
                print "<option value='" . htmlspecialchars($cur[0]) . "' $selected>" . htmlspecialchars($cur[1]) . "</option>";
            }
        }

        print "</select>";
    }

    // Close the input/field div and the form group div
    print "</div>";
}

function displayArrayList($field, $name, $value, $prints, $v1, $v2, $onChange, $w1, $w2) {
    global $db_type;
    if ($w1 == "") {
        $w1 = "50%";
    }
    if ($w2 == "") {
        $w2 = "50%";
    }
//    print "<td align='right' width='" . $w1 . "'><font size='2' face='Verdana'>" . $name . "</font></td><td width='" . $w2 . "'>";
    print "<label class='form-label' >" . $name . "</label>";
    if ($prints == "yes") {
        print "<input type='hidden' name='" . $field . "' value='" . $value . "' class='form-control'>";
    } else {
        if ($onChange == "") {
            print "<select name='" . $field . "' class='select2 form-select shadow-none'>";
        } else {
            print "<select name='" . $field . "' " . $onChange . " class='select2 form-select shadow-none'>";
        }
    }
    if ($prints != "yes") {
        print "<option value=''>---</option>";
    }
    for ($i = 0; $i < count($v1); $i++) {
        if ($v1[$i] == $value) {
            if ($prints == "yes") {
                print "<font size='2' face='Verdana'><b>&nbsp;&nbsp;" . $v2[$i] . "</b></font>";
            } else {
                print "<option selected value='" . $v1[$i] . "'>" . $v2[$i] . "</option>";
            }
        } else {
            if ($prints != "yes") {
                print "<option value='" . $v1[$i] . "'>" . $v2[$i] . "</option>";
            }
        }
    }
    if ($prints == "yes") {
        print "&nbsp;";
    } else {
        print "</select>";
    }
//    print "</td>";
}

function displayUser($conn, $iconn) {
    global $USER_PASS_NEW_USER;
    $message = $_POST["message"];
    if ($message == "") {
        $message = $_GET["message"];
    }
    $act = $_POST["act"];
    if ($act == "") {
        $act = $_GET["act"];
    }
    $username = "";
    $password = "";
    $userstatus = "";
    $userlevel = "";
    $query = "";
    $modules = getModules();
    global $session_variable;
    $sessionlevel = $_SESSION[$session_variable . "userlevel"];
    $sessionstatus = $_SESSION[$session_variable . "userstatus"];
    $sessionuser = $_SESSION[$session_variable . "username"];
    if ($message == "") {
        $message = $modules[0];
    }
    if ($act != "") {
        if ($act == "bad") {
            $username = $_POST["txtBadUsername"];
        } else {
            if ($act == "add") {
                $username = $_POST["txtUsername"];
                if (adduser($conn, $iconn, $username)) {
                    header("location: UserManagement.php?message=Username: <u>" . $username . "</u> Password: <u>" . $USER_PASS_NEW_USER . "</u> added to the Database");
                } else {
                    header("location: UserManagement.php?message=Username: <u>" . $username . "</u> COULD NOT be added to the Database. Please make sure that Username DOES NOT exist.");
                }
            } else {
                if ($act == "view") {
                    $username = $_POST["lstUsername"];
                    if ($username == "") {
                        $username = $_GET["lstUsername"];
                    }
                    $query = "SELECT Userlevel, Userstatus FROM UserMaster WHERE Username = '" . $username . "'";
                    $result = selectdata($conn, $query);
                    $userlevel = $result[0];
                    $userstatus = $result[1];
                } else {
                    if ($act == "edit") {
                        $username = $_POST["txtUsername"];
                        $password = $_POST["txtPassword"];
                        $usermail = $_POST["txtEmail"];
                        $userstatus = $_POST["lstLevel"];
                        $lstUserStatus = $_POST["lstUserStatus"];
                        $userlevel = getLevel(count($modules) + 10);
                        $text = "Updated User: " . $username;
                        $query = "UPDATE UserMaster SET Usermail = '" . replacestring(trim($usermail), false) . "', Userstatus = '" . $lstUserStatus . "', Userlevel = '" . $userlevel . "' ";
                        if (trim($password) != "") {
                            $query = $query . ", Userpass ='" . encryptstring(trim($password)) . "' ";
                            $text .= " and Reset Password ";
                        }
                        $query = $query . " WHERE Username = '" . $username . "'";
                        updateidata($iconn, $query, 0);
                        $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $_SESSION[$session_variable . "username"] . "', '" . $text . "')";
                        updateidata($iconn, $query, true);
                        if (trim($password) != "") {
                            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Changed Password')";
                            updateidata($iconn, $query, true);
                        }
                        header("location: UserManagement.php?message=Username <u>" . $username . "</u> Successfully Updated");
                    } else {
                        if ($act == "delete") {
                            $username = $_POST["txtUsername"];
                            $query = "DELETE FROM UserMaster WHERE Username = '" . $username . "'";
                            updateidata($iconn, $query, true);
                            $text = "Deleted User: " . $username;
                            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $_SESSION[$session_variable . "username"] . "', '" . $text . "')";
                            updateidata($iconn, $query, true);
                            header("location: UserManagement.php?message=User <u>" . $username . "</u> Successfully Deleted");
                        } else {
                            if ($act == "copyRights") {
                                $username = $_GET["txtUsername"];
                                $copyUser = $_GET["lstCopyUser"];
                                $copyRight = "";
                                $query = "SELECT Userlevel FROM UserMaster WHERE Username = '" . $copyUser . "'";
                                $result = selectdata($conn, $query);
                                $copyRight = $result[0];
                                $query = "UPDATE UserMaster SET UserLevel = '" . $copyRight . "' WHERE Username = '" . $username . "'";
                                updateidata($iconn, $query, 0);
                                $text = "Copied User Right from User: " . $copyUser . " to User: " . $username;
                                $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $_SESSION[$session_variable . "username"] . "', '" . $text . "')";
                                updateidata($iconn, $query, true);
                                header("location: UserManagement.php?message=Copied User Rights/ Level from " . $copyUser . " to " . $username);
                            }
                        }
                    }
                }
            }
        }
    }
    print'<div class="container-fluid">
                <div class="row">
                        <div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12">
                            <div class="card">
                                    <div class="card-body table-responsive">';
    print "<html><head><title>" . $modules[0] . "</title></head> <body>";
    print "<script language='javascript'>";
    print "function addUser(){";
    print "if (document.frmUser.txtUsername.value==''){alert('Please Enter the Username'); document.frmUser.txtUsername.focus();}";
    print "else{document.frmUser.act.value='add'; document.frmUser.btAddUser.disabled = true; document.frmUser.submit();}";
    print "}";
    print "function editUser(){";
    print "document.frmUser.act.value='edit'; document.frmUser.btUpdateUser.disabled = false; document.frmUser.submit();";
    print "}";
    print "function deleteUser(){";
    print "if ((confirm('Delete this User and the related Transaction History'))){document.frmUser.act.value='delete'; document.frmUser.btUpdateDelete.disabled = false; document.frmUser.submit();}";
    print "}";
    print "function searchUser(){";
    print "if (document.frmUser.lstUsername.value=='NONE') {alert('Please Select an User');return;} else {document.frmUser.act.value='view';document.frmUser.submit()}";
    print "}";
    print "</script>";
    print "<div align='center'><center>";
    print "<form name='frmUser' method='post' action='UserManagement.php'> <table border='0' width='100%' cellspacing='1'><table width='800'>";
    print "<input type='hidden' name='act'>";
    print "<tr><td align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b> <br><br>[<b>DELETE</b> Rights on <b>General Reports</b> enables sending of Emails to the provided Email Address] <br>[<b>DELETE</b> Rights on <b>User Management</b> enables Employee Migration] <br>[<b>Viewing</b> Rights on <b>User Management</b> AND <b>Editing</b> Rights on <b>Employee Settings</b> enables Rights on User Department/ Division Access] <br>[<b>REPORT</b> Rights on <b>Contract Access Group</b> enables sending of Emails to the provided Email Address AS a HOD of the respective Department assigned] </b></font></td></tr>";
    if ($act == "view") {
        print "<tr><td width='100%' align='center' bgcolor='#F0F0F0'><font face='Verdana' size='1'><b>Edit/Delete User</b></font></td></tr>";
        displayRights($userlevel, $modules);
        print "<div class='row'>";
        print "<div class='col-2'>";
        $query = "SELECT Usermail, UserStatus FROM UserMaster WHERE Username = '" . $username . "'";
        $array = selectdata($conn, $query);
//        print "<tr><td>&nbsp;</td></tr>";
//        print "<label class='form-label'><b>Username:&nbsp;&nbsp;" . $username . "</b><input type='hidden' name='txtUsername' value='" . $username . "'> <font face='Verdana' size='1'><b>Username:&nbsp;&nbsp;" . $username . "</b></font></td></tr>";
        print "<label class='form-label'><b>Username:&nbsp;&nbsp;" . $username . "</b></label><input type='hidden' name='txtUsername' value='" . $username . "'>";
        print "</div>";
        print "<div class='col-2'>";
        print "<label class='form-label'><b>Reset Password:&nbsp;&nbsp;</b></label><input name='txtPassword' class='form-control'>[Type to Reset, else leave Blank]";
        print "</div>";
        print "<div class='col-3'>";
        print "<label class='form-label'><b>Email:</b></label><input name='txtEmail' value='" . $array[0] . "' maxlength='255' class='form-control'>";
        print "</div>";
        print "<div class='col-1'>";
        print "<label class='form-label'><b>Level:</b></label><select name='lstUserStatus' class='form-control-inner select2 form-select shadow-none'><option value='" . $array[1] . "' selected>" . $array[1] . "</option><option value='1'>1</option><option value='2'>2</option><option value='3'>3</option><option value='4'>4</option><option value='5'>5</option><option value='6'>6</option><option value='7'>7</option><option value='8'>8</option><option value='9'>9</option><option value='10'>10</option></select>";
        print "</div>";
        print "</div>";
        print "<div class='row'>";
        print "<div class='col-4'></div>";
        if (strpos($sessionlevel, "11E") !== false) {
            print "<div class='col-2'>";
            print "<input name='btUpdateUser' type='button' class='btn btn-primary' value='Update " . $username . "' onClick='javascript:editUser()'>";
            print "</div>";
        }
        if (strpos($sessionlevel, "11D") !== false && $username != $sessionuser) {
            print "<div class='col-2'>";
            print "<input name='btUpdateDelete' class='btn btn-primary' type='button' value='Delete " . $username . "' onClick='javascript:deleteUser()'>";
            print "</div>";
        }
        if (strpos($sessionlevel, "11A") !== false) {
            print "<div class='col-1'>";
            print "<input type='button' class='btn btn-primary' value='Add new User' onClick=javascript:window.location.href='UserManagement.php'>";
            print "</div>";
        }

        print "</div>";
        if (strpos($sessionlevel, "11E") !== false) {
            print "<div class='row'>";
            print "<div class='col-5'></div>";
            print "<div class='col-2'>";
            print "<br><label class='form-label'>Copy all Rights for " . $username . " from </label>";
            print "<select size='1' name='lstCopyUser' class='form-control-inner select2 form-select shadow-none'>";
            $query = "SELECT Username FROM UserMaster WHERE Username NOT LIKE '" . $username . "' ORDER BY Username DESC";
            $result2 = mysqli_query($conn, $query);
            while ($array = mysqli_fetch_row($result2)) {
                print "<option selected value='" . $array[0] . "'>" . $array[0] . "</option>";
            }
            print "</select>";
            print "</div>";
            print "</div>";
            print "<div class='row'>";
            print "<div class='col-12'>";
            print "<center><br><input type='button' class='btn btn-primary' value='Click to Copy' onClick=javascript:this.disabled=true;window.location.href='UserManagement.php?act=copyRights&lstCopyUser='+document.frmUser.lstCopyUser.value+'&txtUsername='+document.frmUser.txtUsername.value></center>";
            print "</div>";
            print "</div>";
        }
    } else {
        if (($act == "" || $act == "bad") && strpos($sessionlevel, "11A") !== false && noTASoftware($conn, "") == false) {
            print "<div class='row'>";
            print "<div class='col-5'></div>";
            print "<div class='col-2'>";
            print "<label class='form-label'><b>Add User:</b></label>";
            print "<br>Username:<input name='txtUsername' value='" . $username . "' class='form-control'>";
            print "</div>";
            print "</div>";
            print "<div class='row'>";
            print "<div class='col-5'></div>";
            print "<div class='col-2'>";
            print "<br><input name='btAddUser' type='button' class='btn btn-primary' value='Add User' onClick='javascript:addUser()'>";
            print "</div>";
            print "</div>";
        }
    }
    print "<div class='row'>";
    print "<div class='col-5'></div>";
    print "<div class='col-2'>";
    print "<br><label class='form-label'>Select User:</label><select size='1' name='lstUsername' onChange='javascript:searchUser()' class='form-control-inner select2 form-select shadow-none'> <option selected value= 'NONE'>---</option>";
    print "</div>";
    print "</div>";
    if ($_SESSION[$session_variable . "username"] == "virdi") {
        $query = "SELECT Username FROM UserMaster ORDER BY Username";
    } else {
        $query = "SELECT Username FROM UserMaster WHERE Username NOT LIKE 'virdi' ORDER BY Username";
    }
    $result2 = mysqli_query($conn, $query);
    $selectedUser = isset($_POST['lstUsername']) ? $_POST['lstUsername'] : '';
    while ($array = mysqli_fetch_row($result2)) {
        $userSel = $array[0];
        echo $userSelected = ($selectedUser == $userSel) ? 'selected' : '';
        print "<option value='" . $array[0] . "' $userSelected>" . $array[0] . "</option>";
    }
    print "</select>&nbsp;&nbsp;</td></tr>";
    print "<tr> <td width='100%'>&nbsp;</td></tr>";
    print "</form></table>";
    print "</div></div></div></div>";
    print "</center></div>";
    print "</div></div></div></div></div>";
}

function displayRights($userLevel, $modules) {
    $count = count($modules) + 10;
    print "<script language='javascript'>";
    print "function allAll(){ x = document.frmUser; if (x.chk00.checked){ x.chk0V.checked=true; x.chk0A.checked=true; x.chk0E.checked=true;  x.chk0D.checked=true; x.chk0R.checked=true;";
    for ($i = 11; $i <= $count; $i++) {
        print "x.chk" . $i . "V.checked=true;";
        print "x.chk" . $i . "A.checked=true;";
        print "x.chk" . $i . "E.checked=true;";
        print "x.chk" . $i . "D.checked=true;";
        print "x.chk" . $i . "R.checked=true;";
    }
    print " } else{ x.chk0V.checked=false; x.chk0A.checked=false; x.chk0E.checked=false; x.chk0D.checked=false; x.chk0R.checked=false;";
    for ($i = 11; $i <= $count; $i++) {
        print "x.chk" . $i . "V.checked=false;";
        print "x.chk" . $i . "A.checked=false;";
        print "x.chk" . $i . "E.checked=false;";
        print "x.chk" . $i . "D.checked=false;";
        print "x.chk" . $i . "R.checked=false;";
    }
    print " } }";
    print "function viewAll(){ x = document.frmUser; if (x.chk0V.checked){";
    for ($i = 11; $i <= $count; $i++) {
        print "x.chk" . $i . "V.checked=true;";
    }
    print " } else{ ";
    for ($i = 11; $i <= $count; $i++) {
        print "x.chk" . $i . "V.checked=false;";
    }
    print " } }";
    print "function addAll(){ x = document.frmUser; if (x.chk0A.checked){";
    for ($i = 11; $i <= $count; $i++) {
        print "x.chk" . $i . "A.checked=true;";
    }
    print " } else{ ";
    for ($i = 11; $i <= $count; $i++) {
        print "x.chk" . $i . "A.checked=false;";
    }
    print " } }";
    print "function editAll(){ x = document.frmUser; if (x.chk0E.checked){";
    for ($i = 11; $i <= $count; $i++) {
        print "x.chk" . $i . "E.checked=true;";
    }
    print " } else{ ";
    for ($i = 11; $i <= $count; $i++) {
        print "x.chk" . $i . "E.checked=false;";
    }
    print " } }";
    print "function deleteAll(){ x = document.frmUser; if (x.chk0D.checked){";
    for ($i = 11; $i <= $count; $i++) {
        print "x.chk" . $i . "D.checked=true;";
    }
    print " } else{ ";
    for ($i = 11; $i <= $count; $i++) {
        print "x.chk" . $i . "D.checked=false;";
    }
    print " } }";
    print "function reportAll(){ x = document.frmUser; if (x.chk0R.checked){";
    for ($i = 11; $i <= $count; $i++) {
        print "x.chk" . $i . "R.checked=true;";
    }
    print " } else{ ";
    for ($i = 11; $i <= $count; $i++) {
        print "x.chk" . $i . "R.checked=false;";
    }
    print " } }";
    for ($i = 11; $i <= $count; $i++) {
        print "function all" . $i . "(){ x = document.frmUser; if (x.chk" . $i . "0.checked){" . "x.chk" . $i . "V.checked=true;" . "x.chk" . $i . "A.checked=true;" . "x.chk" . $i . "E.checked=true;" . "x.chk" . $i . "D.checked=true;" . "x.chk" . $i . "R.checked=true;" . "}else{" . "x.chk" . $i . "V.checked=false;" . "x.chk" . $i . "A.checked=false;" . "x.chk" . $i . "E.checked=false;" . "x.chk" . $i . "D.checked=false;" . "x.chk" . $i . "R.checked=false;" . " } }";
        print "function view" . $i . "(){ x = document.frmUser; if (x.chk" . $i . "V.checked){ }" . "else{ x.chk00.checked=false; x.chk0V.checked=false; x.chk" . $i . "0.checked=false;} }";
        print "function add" . $i . "(){ x = document.frmUser; if (x.chk" . $i . "A.checked){ }" . "else{ x.chk00.checked=false; x.chk0A.checked=false; x.chk" . $i . "0.checked=false;} }";
        print "function edit" . $i . "(){ x = document.frmUser; if (x.chk" . $i . "E.checked){ }" . "else{ x.chk00.checked=false; x.chk0E.checked=false; x.chk" . $i . "0.checked=false;} }";
        print "function delete" . $i . "(){ x = document.frmUser; if (x.chk" . $i . "D.checked){ }" . "else{ x.chk00.checked=false; x.chk0D.checked=false; x.chk" . $i . "0.checked=false;} }";
        print "function report" . $i . "(){ x = document.frmUser; if (x.chk" . $i . "R.checked){ }" . "else{ x.chk00.checked=false; x.chk0R.checked=false; x.chk" . $i . "0.checked=false;} }";
    }
    print "</script>";
    print "<tr> <td width='100%'> <table width='100%'>";
    print "<tr> <td width='40%' bgcolor='#F0F0F0' align='right'><font face='Verdana' size='2' ><b>Grant all Rights</b></font></td>";
    print "<td width='10%' bgcolor='#F0F0F0' align='right'><font face='Verdana' size='2' > <b> All</b></font> <input type='checkbox' name='chk00' onClick='javascript:allAll()'></td>";
    print "<td width='10%' bgcolor='#F0F0F0' align='right'><font face='Verdana' size='2' ><b>View </b></font><input type='checkbox' name='chk0V' onClick='javascript:viewAll()'></td>";
    print "<td width='10%' bgcolor='#F0F0F0' align='right'><font face='Verdana' size='2' ><b>Add </b></font><input type='checkbox' name='chk0A' onClick='javascript:addAll()'></td>";
    print "<td width='10%' bgcolor='#F0F0F0' align='right'><font face='Verdana' size='2' ><b>Edit </b></font><input type='checkbox' name='chk0E' onClick='javascript:editAll()'></td>";
    print "<td width='10%' bgcolor='#F0F0F0' align='right'><font face='Verdana' size='2' ><b>Delete </b></font><input type='checkbox' name='chk0D' onClick='javascript:deleteAll()'></td>";
    print "<td width='10%' bgcolor='#F0F0F0' align='right'><font face='Verdana' size='2' ><b>Reports </b></font><input type='checkbox' name='chk0R' onClick='javascript:reportAll()'></td>";
    print "</tr>";
    for ($i = 11; $i <= $count; $i++) {
        print "<tr>";
        print "<td width='40%' bgcolor='#F0F0F0' align='right'><font face='Verdana' size='1' ><b>" . $modules[$i - 11] . "</b></font></td> <td width='10%'  bgcolor='#F0F0F0' align='right'><font face='Verdana' size='1' ><b>All </b></font><input type='checkbox' name='chk" . $i . "0' onClick='javascript:all" . $i . "()'></td>";
        $checkbox = "";
        $checkbox = "<td width='10%' bgcolor='#F0F0F0' align='right'><font face='Verdana' size='1' ><b>View </b></font><input type='checkbox' name='chk" . $i . "V' ";
        if (strpos($userLevel, $i . "V") !== false) {
            $checkbox = $checkbox . " checked ";
        }
        $checkbox = $checkbox . " onClick='javascript:view" . $i . "()'></td>";
        print $checkbox;
        $checkbox = "<td width='10%' bgcolor='#F0F0F0' align='right'><font face='Verdana' size='1' ><b>Add </b></font><input type='checkbox' name='chk" . $i . "A' ";
        if (strpos($userLevel, $i . "A") !== false) {
            $checkbox = $checkbox . " checked ";
        }
        $checkbox = $checkbox . " onClick='javascript:add" . $i . "()'></td>";
        print $checkbox;
        $checkbox = "<td width='10%' bgcolor='#F0F0F0' align='right'><font face='Verdana' size='1' ><b>Edit </b></font><input type='checkbox' name='chk" . $i . "E' ";
        if (strpos($userLevel, $i . "E") !== false) {
            $checkbox = $checkbox . " checked ";
        }
        $checkbox = $checkbox . " onClick='javascript:edit" . $i . "()'></td>";
        print $checkbox;
        $checkbox = "<td width='10%' bgcolor='#F0F0F0' align='right'><font face='Verdana' size='1' ><b>Delete </b></font><input type='checkbox' name='chk" . $i . "D' ";
        if (strpos($userLevel, $i . "D") !== false) {
            $checkbox = $checkbox . " checked ";
        }
        $checkbox = $checkbox . " onClick='javascript:delete" . $i . "()'></td>";
        print $checkbox;
        $checkbox = "<td width='10%' bgcolor='#F0F0F0' align='right'><font face='Verdana' size='1' ><b>Reports </b></font><input type='checkbox' name='chk" . $i . "R' ";
        if (strpos($userLevel, $i . "R") !== false) {
            $checkbox = $checkbox . " checked ";
        }
        $checkbox = $checkbox . " onClick='javascript:report" . $i . "()'></td>";
        print $checkbox;
        print "</tr>";
    }
    print "</table></td></tr>";
}

function displayAllRights($userLevel, $modules) {
    $count = count($modules) + 10;
    print "<tr> <td width='100%'> <table width='100%' bgcolor='#FFFFFF'>";
    for ($i = 11; $i <= $count; $i++) {
        print "<tr>";
        print "<td width='50%' bgcolor='#F0F0F0' align='right'><font face='Verdana' size='1' ><b>" . $modules[$i - 11] . "</b></font></td>";
        $checkbox = "";
        $checkbox = "<td width='10%' bgcolor='#F0F0F0' align='right'><font face='Verdana' size='1' ><b>View </b></font><input type='checkbox' name='chk" . $i . "V' ";
        if (strpos($userLevel, $i . "V") !== false) {
            $checkbox = $checkbox . " checked ";
        }
        $checkbox = $checkbox . "></td>";
        print $checkbox;
        $checkbox = "<td width='10%' bgcolor='#F0F0F0' align='right'><font face='Verdana' size='1' ><b>Add </b></font><input type='checkbox' name='chk" . $i . "A' ";
        if (strpos($userLevel, $i . "A") !== false) {
            $checkbox = $checkbox . " checked ";
        }
        $checkbox = $checkbox . "></td>";
        print $checkbox;
        $checkbox = "<td width='10%' bgcolor='#F0F0F0' align='right'><font face='Verdana' size='1' ><b>Edit </b></font><input type='checkbox' name='chk" . $i . "E' ";
        if (strpos($userLevel, $i . "E") !== false) {
            $checkbox = $checkbox . " checked ";
        }
        $checkbox = $checkbox . "></td>";
        print $checkbox;
        $checkbox = "<td width='10%' bgcolor='#F0F0F0' align='right'><font face='Verdana' size='1' ><b>Delete </b></font><input type='checkbox' name='chk" . $i . "D' ";
        if (strpos($userLevel, $i . "D") !== false) {
            $checkbox = $checkbox . " checked ";
        }
        $checkbox = $checkbox . "></td>";
        print $checkbox;
        $checkbox = "<td width='10%' bgcolor='#F0F0F0' align='right'><font face='Verdana' size='1' ><b>Reports </b></font><input type='checkbox' name='chk" . $i . "R' ";
        if (strpos($userLevel, $i . "R") !== false) {
            $checkbox = $checkbox . " checked ";
        }
        $checkbox = $checkbox . "></td>";
        print $checkbox;
        print "</tr>";
    }
    print "</table></td></tr>";
}

function getLevel($count) {
    $level = "";
    $chk = "";
    for ($i = 11; $i <= $count; $i++) {
        for ($j = 0; $j < 5; $j++) {
            $val = "";
            if ($j == 0) {
                $val = "V";
            } else {
                if ($j == 1) {
                    $val = "A";
                } else {
                    if ($j == 2) {
                        $val = "E";
                    } else {
                        if ($j == 3) {
                            $val = "D";
                        } else {
                            if ($j == 4) {
                                $val = "R";
                            }
                        }
                    }
                }
            }
            $chk = "chk" . $i . $val;
            $data = $_POST[$chk];
            if ($data != "") {
                $level = $level . $i . $val;
            }
        }
    }
    return $level;
}

function links() {
    
}

function insertToday() {
    date_default_timezone_set("America/Los_Angeles");
//    return strftime("%Y") . strftime("%m") . strftime("%d");
    return date("Ymd");
}

function displayToday() {
    date_default_timezone_set("Africa/Algiers");
//    return strftime("%d") . "/" . strftime("%m") . "/" . strftime("%Y");
    return date("d/m/Y");
}

function insertDate($data) {
    return substr($data, 6, 4) . substr($data, 3, 2) . substr($data, 0, 2);
}

function insertParadoxDate($data) {
    return substr($data, 0, 4) . substr($data, 5, 2) . substr($data, 8, 2);
}

function insertParadoxxDate($data) {
    return substr($data, 6, 4) . substr($data, 3, 2) . substr($data, 0, 2);
}

function insertMSSQLDate($data) {
    $m = substr($data, 0, 3);
    switch ($m) {
        case "Jan":
            $m = "01";
        case "Feb":
            $m = "02";
        case "Mar":
            $m = "03";
        case "Apr":
            $m = "04";
        case "May":
            $m = "05";
        case "Jun":
            $m = "06";
        case "Jul":
            $m = "07";
        case "Aug":
            $m = "08";
        case "Sep":
            $m = "09";
        case "Oct":
            $m = "10";
        case "Nov":
            $m = "11";
        case "Dec":
            $m = "12";
    }
    $d = trim(substr($data, 3, 3));
    $y = substr($data, 6, 4);
    return $y . $m . $d;
}

function insertMONDate($data) {
    $m = substr($data, 4, 2);
    switch ($m) {
        case "01":
            $m = "Jan";
            break;
        case "02":
            $m = "Feb";
            break;
        case "03":
            $m = "Mar";
            break;
        case "04":
            $m = "Apr";
            break;
        case "05":
            $m = "May";
            break;
        case "06":
            $m = "Jun";
            break;
        case "07":
            $m = "Jul";
            break;
        case "08":
            $m = "Aug";
            break;
        case "09":
            $m = "Sep";
            break;
        case "10":
            $m = "Oct";
            break;
        case "11":
            $m = "Nov";
            break;
        case "12":
            $m = "Dec";
            break;
    }
    $d = trim(substr($data, 6, 8));
    $y = substr($data, 2, 2);
    return $d . "-" . $m . "-" . $y;
}

function displayParadoxDate($data) {
    return substr($data, 0, 4) . "-" . substr($data, 4, 2) . "-" . substr($data, 6, 2);
}

function displayParadoxxDate($data) {
    return substr($data, 6, 2) . "-" . substr($data, 4, 2) . "-" . substr($data, 0, 4);
}

function insertParadoxTime($data) {
    return substr($data, 11, 2) . substr($data, 14, 2) . substr($data, 17, 2);
}

function insertTime($data) {
    return substr($data, 0, 2) . substr($data, 3, 2) . substr($data, 6, 2);
}

function displayDate($data) {
    return substr($data, 6, 2) . "/" . substr($data, 4, 2) . "/" . substr($data, 0, 4);
}

function displayDotDate($data) {
    return substr($data, 6, 2) . "." . substr($data, 4, 2) . "." . substr($data, 0, 4);
}

function displayUSDate($data) {
    return substr($data, 4, 2) . "/" . substr($data, 6, 2) . "/" . substr($data, 0, 4);
}

function getNow() {
    date_default_timezone_set("Africa/Algiers");
//    return strftime("%H") . strftime("%M");
    return date("Hi");
}

function displayTime($data) {
    if (strlen($data) == 3) {
        return "0" . substr($data, 0, 1) . ":" . substr($data, 1, 2);
    }
    return substr($data, 0, 2) . ":" . substr($data, 2, 2);
}

function displayVirdiTime($data) {
    return substr($data, 0, 2) . ":" . substr($data, 2, 2) . ":" . substr($data, 4, 2);
}

function displayUSToday() {
    date_default_timezone_set("Africa/Algiers");
    return strftime("%m") . "/" . strftime("%d") . "/" . strftime("%Y");
}

function calDate($data, $days) {
    
}

function addZero($data, $length) {
    $l = strlen($data);
    if ($l < $length) {
        for ($i = $l; $i < $length; $i++) {
            $data = "0" . $data;
        }
    }
    return $data;
}

function displayColourFlag($conn, $data, $select, $search, $td) {
    global $session_variable;
    if ($td) {
        print "<label class='form-label'>Flag:</label>";
    }
    print "<select name='" . $select . "' class='form-control-inner select2 form-select shadow-none'> <option value='' selected>---</option>";
    if ($search) {
        if ($_SESSION[$session_variable . "VirdiLevel"] == "Classic") {
            $query = "SELECT Flag, Title FROM FlagTitle ORDER BY FlagTitleID";
        } else {
            $query = "SELECT Flag, Title FROM FlagTitle WHERE Flag = 'Black' OR Flag = 'Proxy' OR Flag = 'Purple' ORDER BY FlagTitleID";
        }
    } else {
        if ($_SESSION[$session_variable . "VirdiLevel"] == "Classic") {
            $query = "SELECT Flag, Title FROM FlagTitle WHERE Flag <> 'Black' AND (FlagLink IS NULL OR LENGTH(FlagLink) = 0) ORDER BY FlagTitleID";
        } else {
            $query = "SELECT Flag, Title FROM FlagTitle WHERE Flag = 'Proxy' OR Flag = 'Purple' ORDER BY FlagTitleID";
        }
    }
    $result = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
        if ($data == $cur[0]) {
            if (trim($cur[1]) == "") {
                print "<option selected value='" . $cur[0] . "'>" . $cur[0] . "</option>";
            } else {
                print "<option selected value='" . $cur[0] . "'>" . $cur[0] . " - [" . $cur[1] . "]</option>";
            }
        } else {
            if (trim($cur[1]) == "") {
                print "<option value='" . $cur[0] . "'>" . $cur[0] . "</option>";
            } else {
                print "<option value='" . $cur[0] . "'>" . $cur[0] . " - [" . $cur[1] . "]</option>";
            }
        }
    }
    if ($search) {
        if ($data == "Black") {
            print "<option selected value='Black'>Black</option>";
        } else {
            print "<option value='Black'>Black</option>";
        }
    }
    if ($data == "Proxy") {
        print "<option selected value='Proxy'>Proxy</option>";
    } else {
        print "<option value='Proxy'>Proxy</option>";
    }
    if ($search) {
        if ($data == "Black/Proxy") {
            print "<option selected value='Black/Proxy'>Black/Proxy</option>";
        } else {
            print "<option value='Black/Proxy'>Black/Proxy</option>";
        }
    }
    if ($search) {
        if ($data == "All w/o Black/Proxy") {
            print "<option selected value='All w/o Black/Proxy'>All w/o Black/Proxy</option>";
        } else {
            print "<option value='All w/o Black/Proxy'>All w/o Black/Proxy</option>";
        }
        if ($data == "All w/o Proxy") {
            print "<option selected value='All w/o Proxy'>All w/o Proxy</option>";
        } else {
            print "<option value='All w/o Proxy'>All w/o Proxy</option>";
        }
    }
    print "</select>";
    if ($td) {
        print "</td>";
    }
}

function insertAttendance($conn, $iconn, $id, $txtFrom, $txtTo, $group, $flag, $lstDeptTerminal) {
    $fromDate = insertdate($txtFrom); // yyyymmdd
    $toDate   = insertdate($txtTo);   // yyyymmdd
    $nextDate = $fromDate;
    $loopGuard = 0;
    if ($lstDeptTerminal === NULL || $lstDeptTerminal === '') {
        $lstDeptTerminal = 0; // or a valid default terminal ID
    }
    //$nextDate = insertdate($txtFrom);
    $query = "SELECT TotalDailyClockin, TotalExitClockin, NoBreakException, NoExitException, ExitTerminal, EmployeeCodeLength FROM OtherSettingMaster";
    $result = selectdata($conn, $query);
    $txtTotalDailyClockin = $result[0];
    $txtTotalExitClockin = $result[1];
    $dc = $txtTotalDailyClockin;
    $ec = $txtTotalExitClockin;
    $noBreak = $result[2];
    $noExit = $result[3];
    $exitTerminal = $result[4];
    $txtECodeLength = $result[5];
    $query = "SELECT Start, FlexiBreak, BreakFrom, BreakTo, Close, NightFlag, WorkMin, MoveNS FROM tgroup WHERE ShiftTypeID = 1 AND id = " . $group;
    $result = selectdata($conn, $query);
    $start = $result[0];
    if ($start == "") {
        $start = "0100";
    }
    $fBreak = $result[1];
    $bFrom = $result[2];
    $bTo = $result[3];
    $close = $result[4];
    if ($close == "") {
        $close = getLateTime(insertdate($txtFrom), $start, $result[6]);
        $close = substr($close, 0, 4);
    }
    $night = $result[5];
    $work = $result[6];
    $nextDay = "";
    $bFromDay = "";
    $bToDay = "";
    $lstMoveNS = $result[7];
    $return_flag = false;
    while (true) {
        if (++$loopGuard > 92) {
            break;
        }
        if (!is_numeric($nextDate) || strlen($nextDate) != 8) {
            break;
        }
        if ((int)$nextDate > (int)$toDate) {
            break;
        }
//        if (insertdate($txtTo) < $nextDate) {
//            break;
//        }
        $temp_start = mktime(
            (int) substr($start, 0, 2),
            (int) substr($start, 2, 2),
            0,
            (int) substr($nextDate, 4, 2),
            (int) substr($nextDate, 6, 2),
            (int) substr($nextDate, 0, 4)
        );
//        $temp_start = mktime(substr($start, 0, 2), substr($start, 2, 2), "00", substr($nextDate, 4, 2), substr($nextDate, 6, 2), substr($nextDate, 0, 4));
        $temp_array = getDate($temp_start);
        $day = $temp_array["weekday"];
        if ($night == 1) {
            $next = strtotime(substr($nextDate, 6, 2) . "-" . substr($nextDate, 4, 2) . "-" . substr($nextDate, 0, 4) . " + 1 day");
            $a = getDate($next);
            $m = $a["mon"];
            if ($m < 10) {
                $m = "0" . $m;
            }
            $d = $a["mday"];
            if ($d < 10) {
                $d = "0" . $d;
            }
            $nextDay = $a["year"] . $m . $d;
        }
        if ($noBreak == "No") {
            $message = "Attendance CANNOT be set for Employees with 4 Clockings per Day and Allow NO BREAK Exception as NO<br>";
        } else {
            if ($exitTerminal == "Yes" && $noExit == "No") {
                $message = "Attendance CANNOT be set for Employees with Exit Clocking and ALLOW NON CLOCKING AT EXIT TERMINAL option as NO<br>";
            } else {
                if ($night == 1 && $lstMoveNS == "Yes") {
                    $nextDate = getLastDay($nextDate, 1);
                    $nextDay = getLastDay($nextDay, 1);
                }
                $query_flag = false;
                $query = "INSERT IGNORE INTO tenter (e_date, e_time, g_id, e_id, e_group, e_user, e_mode, e_type, e_result, e_etc, p_flag) VALUES ('" . $nextDate . "', '" . $start . "00', " . $lstDeptTerminal . ", " . $id . ", " . $group . ", '0', '3', '3', '0', 'P', '1')";
                if (updateidata($iconn, $query, true)) {
                    $query_flag = true;
                } else {
                    $query = "UPDATE tenter SET p_flag = 1 WHERE e_date = '" . $nextDate . "' AND e_time = '" . $start . "00' AND e_group = " . $group . " AND e_id = " . $id;
                    if (updateidata($iconn, $query, true)) {
                        $query_flag = true;
                    }
                }
                if ($query_flag) {
                    if ($night == 1) {
                        $query = "INSERT IGNORE INTO tenter (e_date, e_time, g_id, e_id, e_group, e_user, e_mode, e_type, e_result, e_etc, p_flag) VALUES ('" . $nextDay . "', '" . $close . "00', " . $lstDeptTerminal . ", " . $id . ", " . $group . ", '0', '3', '3', '0', 'P', '1')";
                    } else {
                        $query = "INSERT IGNORE INTO tenter (e_date, e_time, g_id, e_id, e_group, e_user, e_mode, e_type, e_result, e_etc, p_flag) VALUES ('" . $nextDate . "', '" . $close . "00', " . $lstDeptTerminal . ", " . $id . ", " . $group . ", '0', '3', '3', '0', 'P', '1')";
                    }
                    if (updateidata($iconn, $query, true) == false) {
                        if ($night == 1) {
                            $query = "UPDATE tenter SET p_flag = 1 WHERE e_date = '" . $nextDay . "' AND e_time = '" . $close . "00' AND e_group = " . $group . " AND g_id = " . $lstDeptTerminal . " AND e_id = " . $id;
                        } else {
                            $query = "UPDATE tenter SET p_flag = 1 WHERE e_date = '" . $nextDate . "' AND e_time = '" . $close . "00' AND e_group = " . $group . " AND g_id = " . $lstDeptTerminal . " AND e_id = " . $id;
                        }
                        if (updateidata($iconn, $query, true)) {
                            $query_flag = true;
                        } else {
                            $query_flag = false;
                        }
                    }
                    if ($query_flag) {
                        $message = "Attendance set successfully for the selected Employees";
                        if ($night == 1 && $lstMoveNS == "Yes") {
                            $nextDate = getNextDay($nextDate, 1);
                            $nextDay = getNextDay($nextDay, 1);
                        }
                        $query = "INSERT IGNORE INTO DayMaster (e_id, TDate, Entry, Start, BreakOut, BreakIn, Close, DayMaster.Exit, group_id, Flag, p_flag) VALUES (" . $id . ", " . $nextDate . ", '" . $start . "00', '" . $start . "00', '" . $start . "00', '" . $start . "00', '" . $close . "00', '" . $close . "00', " . $group . ", '" . $flag . "', 1)";
                        if (updateidata($iconn, $query, true)) {
                            $query = "INSERT IGNORE INTO AttendanceMaster (EmployeeID, EmpID, group_id, group_min, ADate, Normal, Overtime, Day, Flag, p_flag) VALUES (" . $id . ", '" . addzero($id, $txtECodeLength) . "', " . $group . ", " . $work . ", " . $nextDate . ", " . $work * 60 . ", 0, '" . $day . "', '" . $flag . "', '1')";
                            if (updateidata($iconn, $query, true)) {
                                $return_flag = true;
                            }
                        }
                    }
                }
            }
        }
        $next = strtotime(substr($nextDate, 6, 2) . "-" . substr($nextDate, 4, 2) . "-" . substr($nextDate, 0, 4) . " + 1 day");
        $a = getDate($next);
        $m = $a["mon"];
        if ($m < 10) {
            $m = "0" . $m;
        }
        $d = $a["mday"];
        if ($d < 10) {
            $d = "0" . $d;
        }
        $nextDate = $a["year"] . $m . $d;
    }
    return $return_flag;
}

function getNextDay($d, $count) {
    $next = strtotime(substr($d, 0, 4) . "-" . substr($d, 4, 2) . "-" . substr($d, 6, 2) . " + " . $count . " Day");
    $a = getDate($next);
    return $a["year"] . addzero($a["mon"], 2) . addzero($a["mday"], 2);
}

function getLastDay($d, $count) {
    $next = strtotime(substr($d, 0, 4) . "-" . substr($d, 4, 2) . "-" . substr($d, 6, 2) . " - " . $count . " Day");
    $a = getDate($next);
    return $a["year"] . addzero($a["mon"], 2) . addzero($a["mday"], 2);
}

function getNextTime($d, $t, $count) {
    $next = strtotime(substr($d, 0, 4) . "-" . substr($d, 4, 2) . "-" . substr($d, 6, 2) . " " . substr($t, 0, 2) . ":" . substr($t, 2, 2) . ":" . substr($t, 4, 2) . " + " . $count . " hours");
    $a = getDate($next);
    return $a["year"] . "-" . addzero($a["mon"], 2) . "-" . addzero($a["mday"], 2) . " " . addzero($a["hours"], 2) . ":" . addzero($a["minutes"], 2) . ":" . addzero($a["seconds"], 2);
}

function getLastTime($d, $t, $count) {
    $next = strtotime(substr($d, 0, 4) . "-" . substr($d, 4, 2) . "-" . substr($d, 6, 2) . " " . substr($t, 0, 2) . ":" . substr($t, 2, 2) . ":" . substr($t, 4, 2) . " - " . $count . " hours");
    $a = getDate($next);
    return $a["year"] . "-" . addzero($a["mon"], 2) . "-" . addzero($a["mday"], 2) . " " . addzero($a["hours"], 2) . ":" . addzero($a["minutes"], 2) . ":" . addzero($a["seconds"], 2);
}

function rotateShift($conn, $iconn, $e_id, $grp_id) {
    $query = "SELECT ShiftChangeID FROM ShiftChangeMaster WHERE id = " . $grp_id . " ORDER BY ShiftChangeID";
    $result1 = selectdata($conn, $query);
    $query = "SELECT id FROM ShiftChangeMaster WHERE ShiftChangeID > " . $result1[0] . " ORDER BY ShiftChangeID";
    $result2 = selectdata($conn, $query);
    $query = "UPDATE tuser SET group_id = " . $result2[0] . " WHERE id = " . $e_id;
    if (updateidata($iconn, $query, true)) {
        
    }
    return $result2[0];
}

function getDayCount($from, $to, $dayCount, $day) {
    $count = 0;
    if ($to != "") {
        $to = strtotime(substr($to, 6, 2) . "-" . substr($to, 4, 2) . "-" . substr($to, 0, 4));
    }
    for ($i = 0; $i < $dayCount; $i++) {
        $this_date = strtotime(substr($from, 6, 2) . "-" . substr($from, 4, 2) . "-" . substr($from, 0, 4) . " + " . $i . " day");
        if ($to != "" && $to < $this_date) {
            $i = $dayCount;
        } else {
            $this_array = getDate($this_date);
            $this_day = $this_array["weekday"];
            if ($this_day == $day) {
                $count++;
            }
        }
    }
    return $count;
}

//function getTotalDays($txtFrom, $txtTo)
//{
//    $fromTime = mktime(0, 0, 0, substr(insertdate($txtFrom), 4, 2), substr(insertdate($txtFrom), 6, 2), substr(insertdate($txtFrom), 0, 4));
//    $toTime = mktime(0, 0, 0, substr(insertdate($txtTo), 4, 2), substr(insertdate($txtTo), 6, 2), substr(insertdate($txtTo), 0, 4));
//    $dayCount = round(($toTime - $fromTime) / 86400, 0);
//    $dayCount++;
//    return $dayCount;
//}
function getTotalDays($txtFrom, $txtTo) {
    $fromTime = mktime(0, 0, 0, (int) substr(insertdate($txtFrom), 4, 2), (int) substr(insertdate($txtFrom), 6, 2), (int) substr(insertdate($txtFrom), 0, 4));
    $toTime = mktime(0, 0, 0, (int) substr(insertdate($txtTo), 4, 2), (int) substr(insertdate($txtTo), 6, 2), (int) substr(insertdate($txtTo), 0, 4));

    $dayCount = round(($toTime - $fromTime) / 86400, 0);
    $dayCount++;

    return $dayCount;
}

function getDay($date) {
    $date = strtotime(substr($date, 0, 2) . "-" . substr($date, 3, 2) . "-" . substr($date, 6, 4));
    $a = getDate($date);
    return $a["weekday"];
}

function getMAC() {
    exec("getmac", $mac);
    return $mac;
}

function checkMAC($conn) {
    $retn = false;
    global $db_expiry;
    $query = "SELECT MACAddress, TCount FROM OtherSettingMaster";
    $result = selectdata($conn, $query);
    $data = encryptDecrypt($result[0]);
    $dtc = getRegister($result[0], 1);
    $query = "SELECT COUNT(*) FROM tgate WHERE tgate.id > 0";
    $result = selectdata($conn, $query);
    $ptc = $result[0];
//    print_R($ptc);die;
    if ($ptc <= $dtc) { 
        $mac = getmac();
        for ($i = 0; $i < count($mac); $i++) {
            if (substr($mac[$i], 0, 17) == $data) {
                $retn = true;
                break;
            }
            if (substr($mac[$i], 0, 17) == "7C-7A-91-49-DE-A0" || substr($mac[$i], 0, 17) == "00-1D-72-0B-D7-F4" || substr($mac[$i], 0, 17) == "18-3D-A2-00-79-F8" || substr($mac[$i], 0, 17) == "9C-AD-97-15-D2-F5" || substr($mac[$i], 0, 17) == "D0-AB-D5-38-45-46" || substr($mac[$i], 0, 17) == "DE-MO-VE-RS-IO-NN" && inserttoday() < $db_expiry) {
                $retn = true;
                break;
            }
            if (substr($mac[$i], 0, 17) == "00-21-85-98-22-D1" && $data == "00-24-21-AC-24-58") {
                $retn = true;
                break;
            }
            if (substr($mac[$i], 0, 17) == "00-1E-8C-33-D8-0E" && $data == "00-23-24-26-4A-02") {
                $retn = true;
                break;
            }
        }
    }
    return $retn;
}

function help($a, $b) {
    $a = $a * 1024;
//    print "<table width='100%'><tr><td bgcolor='#F0F0F0'><a href='Help.php?i=" . $a . "' target='_blank'><font face='Verdana' size='1'>View</font></a></td> <td bgcolor='#F0F0F0' align='right'><font face='Verdana' size='1'><a href='" . $b . "' target='_blank'>Go</font></a></td></tr></table>";
    print "<br><br><a href='Help.php?i=" . $a . "' target='_blank'><font face='Verdana' size='3'><i class='fa fa-eye'></i></font></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font face='Verdana' size='3'><a href='" . $b . "' target='_blank'><i class='fa fa-arrow-circle-right'></i></font></a>";
}

function getEmployeeCode($conn, $query) {
    $result2 = mysqli_query($conn, $query);
    $array = mysqli_fetch_row($result2);
    return $array;
}

function getEmployeeName($conn, $query) {
    $result2 = mysqli_query($conn, $query);
    $array = mysqli_fetch_row($result2);
    return $array;
}

//function getMDArray($conn, $query)
//{
//    $key = "";
//    $val = "";
//    $i = 0;
//    for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $i++) { 
//        $key[$i] = $cur[0];
//        $val[$i] = $cur[1];
//    }
//    return array_combine($key, $val);
//}
function getMDArray($conn, $query) {
    $result = mysqli_query($conn, $query);
    $array = [];

    while ($cur = mysqli_fetch_row($result)) {
        $array[$cur[0]] = $cur[1];
    }

    return $array;
}

function displaySearchFields($conn, $prints, $session_variable, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $lstEmployeeName, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $lstGroup, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10) {
    $deptQuery = $_SESSION[$session_variable . "DeptAccessQuery"] ?? '';
    $divQuery  = $_SESSION[$session_variable . "DivAccessQuery"] ?? '';
    $query = "SELECT id, name from tuser WHERE id > 0 $deptQuery $divQuery ORDER BY tuser.name";
    $md_array = getmdarray($conn, $query);
    $v_eid = array_keys($md_array);
    $v_ename = array_values($md_array);
    sort($v_eid);
    $divWhereQuery = $_SESSION[$session_variable . "DivAccessWhereQuery"] ?? '';
    $deptWhereQuery = $_SESSION[$session_variable . "DeptAccessWhereQuery"] ?? '';
    $divQuery = $_SESSION[$session_variable . "DivAccessQuery"] ?? '';
    if (!($prints == "yes" && $lstDivision == "")) {
        $query = "SELECT distinct(company), company from tuser $divWhereQuery ORDER BY UPPER(company)";
        print "<div class='col-3'>";
        displaylist("lstDivision", "" . $_SESSION[$session_variable . "DivColumnName"] . ": ", $lstDivision, $prints, $conn, $query, "", "25%", "45%");
        print "</div>";
    }
    if (!($prints == "yes" && $lstDepartment == "")) {
        $query = "SELECT distinct(dept), dept from tuser " . $_SESSION[$session_variable . "DeptAccessWhereQuery"] . " ORDER BY UPPER(dept)";
        print "<div class='col-3'>";
        displaylist("lstDepartment", "Department: ", $lstDepartment, $prints, $conn, $query, "", "25%", "45%");
        print "</div>";
    }
    if (!($prints == "yes" && $txtRemark == "")) {
        $query = "SELECT distinct(remark), remark from tuser $deptWhereQuery $divQuery ORDER BY UPPER(remark)";
        print "<div class='col-3'>";
//        displaylist(
//            "lstRemark",
//            ($_SESSION[$session_variable . "RemarkColumnName"] ?? '') . ": ",
//            $_POST["lstRemark"] ?? '',
//            $prints,
//            $conn,
//            $query,
//            "onChange='putRemarkValue(this, document.frm1.txtRemark)'",
//            "25%",
//            "45%"
//        );
        displaylist("lstRemark", "" . $_SESSION[$session_variable . "RemarkColumnName"] . ": ", $_POST["lstRemark"], $prints, $conn, $query, "onChange='putRemarkValue(this, document.frm1.txtRemark)'", "25%", "45%");
        displaytextbox("txtRemark", ": ", $txtRemark, $prints, 25, "5%", "25%");
        print "</div>";
    }
    print "</div>";
    print "<div class='row'>";
    if (!($prints == "yes" && $lstEmployeeName == "" && $txtEmployee == "")) {
        print "<div class='col-3'>";
        displayarraylist("lstEmployeeName", "Employee Name:", $lstEmployeeName, $prints, $v_ename, $v_ename, "onChange='putEmployeeName(this, document.frm1.txtEmployee)'", "25%", "45%");
        displaytextbox("txtEmployee", ": ", $txtEmployee, $prints, 25, "5%", "25%");
        print "</div>";
    }
    if (!($prints == "yes" && $lstEmployeeIDFrom == "" && $txtEmployeeCode == "")) {
        print "<div class='col-2'>";
        displayarraylist("lstEmployeeIDFrom", "Employee ID From:", $lstEmployeeIDFrom, $prints, $v_eid, $v_eid, "", "25%", "25%");
        print "</div>";
        print "<div class='col-2'>";
        displaytextbox("txtEmployeeCode", "Employee ID: ", $txtEmployeeCode, $prints, 12, "25%", "25%");
        print "</div>";
    }
    if (!($prints == "yes" && $lstEmployeeIDTo == "" && $txtSNo == "")) {
        print "<div class='col-2'>";
        displayarraylist("lstEmployeeIDTo", "Employee ID To:", $lstEmployeeIDTo, $prints, $v_eid, $v_eid, "", "25%", "25%");
        $query = "SELECT DISTINCT(idno) FROM tuser";
        $result = mysqli_query($conn, $query);
        $idno = "";
        while ($cur = mysqli_fetch_row($result)) {
            $idno = $cur["idno"] ?? '';
        }
        print "</div>";
//        displaytextbox("txtSNo", "" . $_SESSION[$session_variable . "IDColumnName"] . ": ", htmlspecialchars($txtSNo), $prints, 12, "25%", "25%");
//        print "</tr></table></td>";
//        print "</tr>";
    }
    if (!($prints == "yes" && $lstGroup == "" && $txtPhone == "")) {
        print "<div class='col-3'>";
        if ($_SESSION[$session_variable . "VirdiLevel"] == "Classic") {
            $query = "SELECT GroupID, Name from GroupMaster ORDER BY UPPER(GroupMaster.Name)";
            displaylist("lstGroup", "Report Group:", $lstGroup, $prints, $conn, $query, "", "25%", "25%");
        } else {
            displaytextbox("lstGroup", "Report Group:", "", "yes", 12, "25%", "25%");
        }
        print "</div>";
        print "</div>";
        print "<div class='row'>";
        print "<div class='col-2'>";
        /*********Phone Column Dropdown 22-11-2024*********/
        $query = "SELECT distinct(phone), phone from tuser";
        displaylist("txtPhone", "" . $_SESSION[$session_variable . "PhoneColumnName"] . ": ", $txtPhone, $prints, $conn, $query, "", "25%", "25%");
        /*****************************/
//        displaytextbox("txtPhone", "" . $_SESSION[$session_variable . "PhoneColumnName"] . ": ", $txtPhone, $prints, 12, "25%", "25%");
        print "</div>";
//        print "</tr></table></td>";
//        print "</tr>";
    }


    if (!($txtF1 === "" && $txtF2 == "" && $_SESSION[$session_variable . "F1"] == "" && $_SESSION[$session_variable . "F2"] == "")) {
        print "<div class='col-2'>";
        displaytextbox("txtF1", "" . $_SESSION[$session_variable . "F1"] . ": ", $txtF1, $prints, 12, "25%", "25%");
        print "</div>";
        print "<div class='col-2'>";
        displaytextbox("txtF2", "" . $_SESSION[$session_variable . "F2"] . ": ", $txtF2, $prints, 12, "25%", "25%");
        print "</div>";
//        print "</tr></table></td>";
//        print "</tr>";
    }
    if (!($txtF3 == "" && $txtF4 == "" && $_SESSION[$session_variable . "F3"] == "" && $_SESSION[$session_variable . "F4"] == "")) {
        print "<div class='col-2'>";
        displaytextbox("txtF3", "" . $_SESSION[$session_variable . "F3"] . ": ", $txtF3, $prints, 12, "25%", "25%");
        print "</div>";
        print "<div class='col-2'>";
        displaytextbox("txtF4", "" . $_SESSION[$session_variable . "F4"] . ": ", $txtF4, $prints, 12, "25%", "25%");
        print "</div>";
//        print "</tr></table></td>";
//        print "</tr>";
    }
    if (!($txtF5 == "" && $txtF6 == "" && $_SESSION[$session_variable . "F5"] == "" && $_SESSION[$session_variable . "F6"] == "")) {
        print "<div class='col-2'>";
        displaytextbox("txtF5", "" . $_SESSION[$session_variable . "F5"] . ": ", $txtF5, $prints, 12, "25%", "25%");
        print "</div>";
        print "</div>";
        print "<div class='row'>";
        print "<div class='col-2'>";
        displaytextbox("txtF6", "" . $_SESSION[$session_variable . "F6"] . ": ", $txtF6, $prints, 12, "25%", "25%");
        print "</div>";
//        print "</tr></table></td>";
//        print "</tr>";
    }
    if (!($txtF7 == "" && $txtF8 == "" && $_SESSION[$session_variable . "F7"] == "" && $_SESSION[$session_variable . "F8"] == "")) {
        print "<div class='col-2'>";
        displaytextbox("txtF7", "" . $_SESSION[$session_variable . "F7"] . ": ", $txtF7, $prints, 12, "25%", "25%");
        print "</div>";
        print "<div class='col-2'>";
        displaytextbox("txtF8", "" . $_SESSION[$session_variable . "F8"] . ": ", $txtF8, $prints, 12, "25%", "25%");
        print "</div>";
//        print "</tr></table></td>";
//        print "</tr>";
    }

    if ($txtF9 == "" && $txtF10 == "" && $_SESSION[$session_variable . "F9"] == "" && $_SESSION[$session_variable . "F10"] == "") {
        return NULL;
    }
//    print "<tr>";
//    print "<td colspan='2'><table width='100%' cellspacing='-1' cellpadding='-1' border='0'><tr>";
    print "<div class='col-2'>";
    displaytextbox("txtF9", "" . $_SESSION[$session_variable . "F9"] . ": ", $txtF9, $prints, 12, "25%", "25%");
    print "</div>";
    print "<div class='col-2'>";
    displaytextbox("txtF10", "" . $_SESSION[$session_variable . "F10"] . ": ", $txtF10, $prints, 12, "25%", "25%");
    print "</div>";
    print "</div>";
//    print "</tr></table></td>";
//    print "</tr>";
}

function displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $lstGroup, $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10) {
//    $txtDepartment = $_POST["txtDepartment"];
    $txtDepartment = $_POST["txtDepartment"] ?? '';
    if ($txtDepartment != "") {
        $query = $query . " AND tuser.dept LIKE '" . $txtDepartment . "%'";
    }
    if ($lstDepartment != "") {
        $query = $query . " AND tuser.dept = '" . $lstDepartment . "'";
    }
//    $txtDivision = $_POST["txtDivision"];
    $txtDivision = $_POST["txtDivision"] ?? '';
    if ($txtDivision != "") {
        $query = $query . " AND tuser.company LIKE '" . $txtDivision . "%'";
    }
    if ($lstDivision != "") {
        $query = $query . " AND tuser.company = '" . $lstDivision . "'";
    }
    if ($lstEmployeeIDFrom != "") {
        $query = $query . " AND tuser.id >= " . $lstEmployeeIDFrom;
    }
    if ($lstEmployeeIDTo != "") {
        $query = $query . " AND tuser.id <= " . $lstEmployeeIDTo;
    }
    if ($txtEmployeeCode != "") {
        $query = $query . " AND tuser.id = " . $txtEmployeeCode * 1;
    }
    if ($txtEmployee != "") {
        $query = $query . " AND tuser.name like '%" . $txtEmployee . "%'";
    }
    if ($txtSNo != "") {
        $query = $query . " AND tuser.idno like '%" . $txtSNo . "%'";
    }
    if ($txtPhone != "") {
        $query = $query . " AND tuser.Phone like '" . $txtPhone . "%'";
    }
    if ($txtRemark != "") {
        $query = $query . " AND tuser.remark like '%" . $txtRemark . "%'";
    }
    if ($lstGroup != "") {
        $query = $query . " AND ( " . group_query($conn, "", $lstGroup) . " ) ";
    }
    if ($txtF1 != "") {
        $query = $query . " AND tuser.F1 like '%" . $txtF1 . "%'";
    }
    if ($txtF2 != "") {
        $query = $query . " AND tuser.F2 like '%" . $txtF2 . "%'";
    }
    if ($txtF3 != "") {
        $query = $query . " AND tuser.F3 like '%" . $txtF3 . "%'";
    }
    if ($txtF4 != "") {
        $query = $query . " AND tuser.F4 like '%" . $txtF4 . "%'";
    }
    if ($txtF5 != "") {
        $query = $query . " AND tuser.F5 like '%" . $txtF5 . "%'";
    }
    if ($txtF6 != "") {
        $query = $query . " AND tuser.F6 like '%" . $txtF6 . "%'";
    }
    if ($txtF7 != "") {
        $query = $query . " AND tuser.F7 like '%" . $txtF7 . "%'";
    }
    if ($txtF8 != "") {
        $query = $query . " AND tuser.F8 like '%" . $txtF8 . "%'";
    }
    if ($txtF9 != "") {
        $query = $query . " AND tuser.F9 like '%" . $txtF9 . "%'";
    }
    if ($txtF10 != "") {
        $query = $query . " AND tuser.F10 like '%" . $txtF10 . "%'";
    }
    return $query;
}

function getLateDate($d, $g, $l) {
    $next = strtotime(substr($d, 0, 4) . "-" . substr($d, 4, 2) . "-" . substr($d, 6, 2) . " " . substr($g, 0, 2) . ":" . substr($g, 2, 2) . ":00 + " . $l . " minutes");
    $a = getDate($next);
    $y = $a["year"];
    $m = $a["mon"];
    if ($m < 10) {
        $m = "0" . $m;
    }
    $d = $a["mday"];
    if ($d < 10) {
        $d = "0" . $d;
    }
    return $y . $m . $d;
}

function getLateTime($d, $g, $l) {
    if ($g == "") {
        $g = "1200";
    }
    $next = strtotime(substr($d, 0, 4) . "-" . substr($d, 4, 2) . "-" . substr($d, 6, 2) . " " . substr($g, 0, 2) . ":" . substr($g, 2, 2) . ":00 + " . $l . " minutes");
    $a = getDate($next);
    $h = $a["hours"];
    if ($h < 10) {
        $h = "0" . $h;
    }
    $m = $a["minutes"];
    if ($m < 10) {
        $m = "0" . $m;
    }
    $s = $a["seconds"];
    if ($s < 10) {
        $s = "0" . $s;
    }
    return $h . $m . $s;
}

function getLateMin($d, $g, $in) {
    return round((strtotime(substr($d, 0, 4) . "-" . substr($d, 4, 2) . "-" . substr($d, 6, 2) . " " . substr($in, 0, 2) . ":" . substr($in, 2, 2) . ":" . substr($in, 4, 2)) - strtotime(substr($d, 0, 4) . "-" . substr($d, 4, 2) . "-" . substr($d, 6, 2) . " " . substr($g, 0, 2) . ":" . substr($g, 2, 2) . ":00")) / 60, 2);
}

function getLateMMSS($d, $g, $in) {
    $ss = strtotime(substr($d, 0, 4) . "-" . substr($d, 4, 2) . "-" . substr($d, 6, 2) . " " . substr($in, 0, 2) . ":" . substr($in, 2, 2) . ":" . substr($in, 4, 2)) - strtotime(substr($d, 0, 4) . "-" . substr($d, 4, 2) . "-" . substr($d, 6, 2) . " " . substr($g, 0, 2) . ":" . substr($g, 2, 2) . ":00");
    $s = $ss % 60;
    $m = floor($ss % 3600 / 60);
    $h = floor($ss % 86400 / 3600);
    $d = floor($ss % 2592000 / 86400);
    $M = floor($ss / 2592000);
    return addzero($h, 2) . ":" . addzero($m, 2) . ":" . addzero($s, 2);
}

function getWeirdClient($txtMACAddress) {
    $txtMACAddress = encryptDecrypt($txtMACAddress);
    if (getRegister($txtMACAddress, 7) == "34" || getRegister($txtMACAddress, 7) == "91" || getRegister($txtMACAddress, 7) == "63" || getRegister($txtMACAddress, 7) == "27" || getRegister($txtMACAddress, 7) == "85" || getRegister($txtMACAddress, 7) == "84" || getRegister($txtMACAddress, 7) == "98" || getRegister($txtMACAddress, 7) == "113" || getRegister($txtMACAddress, 7) == "126") {
        return true;
    }
    return false;
}

function getWeirdTime($ss) {
    $s = $ss % 60;
    $m = floor($ss % 3600 / 60);
    $h = floor($ss % 86400 / 3600);
    $d = floor($ss % 2592000 / 86400);
    $M = floor($ss / 2592000);
    return addzero($h, 2) . ":" . addzero($m, 2) . ":" . addzero($s, 2);
}

function getEarlyTime($d, $g, $l) {
    $next = strtotime(substr($d, 0, 4) . "-" . substr($d, 4, 2) . "-" . substr($d, 6, 2) . " " . substr($g, 0, 2) . ":" . substr($g, 2, 2) . ":00 - " . $l . " minutes");
    $a = getDate($next);
    $h = $a["hours"];
    if ($h < 10) {
        $h = "0" . $h;
    }
    $m = $a["minutes"];
    if ($m < 10) {
        $m = "0" . $m;
    }
    $s = $a["seconds"];
    if ($s < 10) {
        $s = "0" . $s;
    }
    return $h . $m . $s;
}

function getEarlyMin($d, $c, $out) {
    return round((strtotime(substr($d, 0, 4) . "-" . substr($d, 4, 2) . "-" . substr($d, 6, 2) . " " . substr($out, 0, 2) . ":" . substr($out, 2, 2) . ":" . substr($out, 2, 2)) - strtotime(substr($d, 0, 4) . "-" . substr($d, 4, 2) . "-" . substr($d, 6, 2) . " " . substr($c, 0, 2) . ":" . substr($c, 2, 2) . ":00")) / 60, 2) * (0 - 1);
}

function displayClockingType($lstClockingType) {
    print "<label class='form-label'>Clocking Type:</label><select name='lstClockingType' class='select2 form-select shadow-none'>";
    if ($lstClockingType == "") {
        print "<option selected value=''>All</option>";
    } else {
        print "<option value=''>All</option>";
    }
    if ($lstClockingType == "P") {
        print "<option selected value='P'>Proxy</option>";
    } else {
        print "<option value='P'>Proxy</option>";
    }
    if ($lstClockingType == "B") {
        print "<option selected value='B'>No Proxy</option>";
    } else {
        print "<option value='B'>No Proxy</option>";
    }
    if ($lstClockingType == "0") {
        print "<option selected value='0'>Breakfast</option>";
    } else {
        print "<option value='0'>Breakfast</option>";
    }
    if ($lstClockingType == "1") {
        print "<option selected value='1'>Lunch</option>";
    } else {
        print "<option value='1'>Lunch</option>";
    }
    if ($lstClockingType == "2") {
        print "<option selected value='2'>Dinner</option>";
    } else {
        print "<option value='2'>Dinner</option>";
    }
    print "</select>";
}

function queryClockingType($query, $lstClockingType) {
    if ($lstClockingType == "P") {
        $query = $query . " AND tenter.e_etc = 'P'";
    } else {
        if ($lstClockingType == "B") {
            $query = $query . " AND (tenter.e_etc NOT LIKE 'P' OR tenter.e_etc IS NULL)";
        } else {
            if ($lstClockingType != "") {
                $query = $query . " AND (tenter.e_etc = '" . $lstClockingType . "')";
            }
        }
    }
    return $query;
}

function editShiftChangeMaster($conn, $iconn, $counter, $ae, $idf, $username, $lstSRDay, $lstSRScenario, $txtRotateShiftNextDay, $txtSRTime) {
    if ($txtRotateShiftNextDay == "") {
        $txtRotateShiftNextDay = 0;
    }
    $validate = true;
    $message = "";
    if ($_POST["lstShift0"] != $_POST["lstShift" . ($counter - 1)]) {
        $message = "First and Last Shifts should be the same";
    } else {
        for ($i = 0; $i < $counter - 1; $i++) {
            for ($j = $i + 1; $j < $counter - 1; $j++) {
                if ($_POST["lstShift" . $i] == $_POST["lstShift" . $j]) {
                    $message = "Duplicate Shifts selected in the Rotation Series";
                    $validate = false;
                }
            }
        }
        if ($validate) {
            $query = "DELETE FROM ShiftChangeMaster WHERE idf = " . $idf;
            updateidata($iconn, $query, true);
            $text = "";
            for ($i = 0; $i < $counter; $i++) {
                $query = "INSERT INTO ShiftChangeMaster (id, idf, AE, SRDay, SRScenario, RotateShiftNextDay, RTime) VALUES (" . $_POST["lstShift" . $i] . ", " . $idf . ", " . $ae . ", '" . $lstSRDay . "', '" . $lstSRScenario . "', '" . insertdate($txtRotateShiftNextDay) . "', '" . $txtSRTime . "')";
                updateidata($iconn, $query, true);
                $query = "UPDATE tgroup SET RotateFlag = " . $ae . " WHERE id = " . $_POST["lstShift" . $i];
                updateidata($iconn, $query, true);
                $text .= " - " . $_POST["lstShift" . $i];
            }
            if ($_POST["lstShift0"] != $_POST["lstShift" . ($counter - 1)]) {
                $query = "INSERT INTO ShiftChangeMaster (id, idf, AE, SRDay, SRScenario, RotateShiftNextDay, RTime) VALUES (" . $_POST["lstShift0"] . ", " . $idf . ", " . $ae . ", '" . $lstSRDay . "', '" . $lstSRScenario . "', '" . insertdate($txtRotateShiftNextDay) . "', '" . $txtSRTime . "')";
                updateidata($iconn, $query, true);
            }
            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . inserttoday() . ", " . getnow() . ", '" . $username . "', 'Updated Shift Rotation Set " . $idf . ": Rotation Cycle" . $text . ", AutoExecute: " . $ae . ", Day: " . $lstSRDay . ", Scenario: " . $lstSRScenario . ", Next Rotation Date: " . $txtRotateShiftNextDay . ", Rotation Time: " . $txtSRTime . "')";
            updateidata($iconn, $query, true);
            $message = "Shift Rotation Updated";
        }
    }
    return $message;
}

function addShiftChangeMaster($conn, $iconn, $ae, $idf, $username, $lstSRDay, $lstSRScenario, $txtRotateShiftNextDay, $txtSRTime) {
    $message = "";
    if ($txtRotateShiftNextDay == "") {
        $txtRotateShiftNextDay = 0;
    }
    if ($_POST["lstShift"] != "") {
        $query = "SELECT id, ShiftChangeID FROM ShiftChangeMaster WHERE ShiftChangeID = (SELECT MAX(ShiftChangeID) FROM ShiftChangeMaster WHERE idf = " . $idf . ")";
        $result = selectdata($conn, $query);
        $id = $result[0];
        $max = $result[1];
        if ($max == "") {
            $query = "INSERT INTO ShiftChangeMaster (id, idf, AE, SRDay, SRScenario, RotateShiftNextDay, RTime) VALUES (" . $_POST["lstShift"] . ", " . $idf . ", " . $ae . ", '" . $lstSRDay . "', '" . $lstSRScenario . "', '" . insertdate($txtRotateShiftNextDay) . "', '" . $txtSRTime . "')";
            updateidata($iconn, $query, true);
            $query = "INSERT INTO ShiftChangeMaster (id, idf, AE, SRDay, SRScenario, RotateShiftNextDay, RTime) VALUES (" . $_POST["lstShift"] . ", " . $idf . ", " . $ae . ", '" . $lstSRDay . "', '" . $lstSRScenario . "', '" . insertdate($txtRotateShiftNextDay) . "', '" . $txtSRTime . "')";
            updateidata($iconn, $query, true);
        } else {
            $query = "UPDATE ShiftChangeMaster SET id = " . $_POST["lstShift"] . ", AE = " . $ae . " WHERE ShiftChangeID = " . $max;
            updateidata($iconn, $query, true);
            $query = "INSERT INTO ShiftChangeMaster (id, idf, AE, SRDay, SRScenario, RotateShiftNextDay, RTime) VALUES (" . $id . ", " . $idf . ", " . $ae . ", '" . $lstSRDay . "', '" . $lstSRScenario . "', '" . insertdate($txtRotateShiftNextDay) . "', '" . $txtSRTime . "')";
            updateidata($iconn, $query, true);
        }
        $query = "UPDATE tgroup SET RotateFlag = " . $ae . " WHERE id = " . $_POST["lstShift"];
        updateidata($iconn, $query, true);
        $message = "Shift added for Rotation";
        $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . inserttoday() . ", " . getnow() . ", '" . $username . "', 'Added Shift ID: " . $_POST["lstShift"] . " to Shift Rotation')";
        updateidata($iconn, $query, true);
    } else {
        $message = "No Shift selected to be added for Rotation";
    }
    return $message;
}

function deleteShiftChangeMaster($conn, $iconn, $idf, $username) {
    $message = "";
    if ($_POST["lstShift"] != "") {
        $query = "SELECT id FROM ShiftChangeMaster WHERE idf = " . $idf . " ORDER BY ShiftChangeID";
        $result = selectdata($conn, $query);
        if ($result[0] == $_POST["lstShift"]) {
            $message = "CANNOT remove the FIRST Shift in Rotation Cycle";
        } else {
            $query = "DELETE FROM ShiftChangeMaster WHERE idf = " . $idf . " AND id = " . $_POST["lstShift"];
            updateidata($iconn, $query, true);
            $message = "Shift removed from Rotation";
            $query = "UPDATE tgroup SET RotateFlag = 0 WHERE id = " . $_POST["lstShift"];
            updateidata($iconn, $query, true);
            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . inserttoday() . ", " . getnow() . ", '" . $username . "', 'Deleted Shift ID: " . $_POST["lstShift"] . " from Shift Rotation')";
            updateidata($iconn, $query, true);
        }
    } else {
        $message = "No Shift selected to be deleted from Rotation";
    }
    return $message;
}

function encryptDecrypt($str) {
    $str = str_replace("-+-+-+", "'", $str);
    $str = str_replace("@@@@@", "�", $str);
    $ky = "DOANKHENBARAHAATH";
    $ky = str_replace(chr(32), "", $ky);
    if (strlen($ky) < 8) {
        exit("key error");
    }
    $kl = strlen($ky) < 32 ? strlen($ky) : 32;
    $k = array();
    for ($i = 0; $i < $kl; $i++) {
        $k[$i] = ord($ky[$i]) & 31;
    }
    $j = 0;
    for ($i = 0; $i < strlen($str); $i++) {
        $e = ord($str[$i]);
        $str[$i] = $e & 224 ? chr($e ^ $k[$j]) : chr($e);
        $j++;
        $j = $j == $kl ? 0 : $j;
    }
    $str = str_replace("'", "-+-+-+", $str);
    $str = str_replace("�", "@@@@@", $str);
    return $str;
}

function encrypt_Decrypt($str) {
    $str = str_replace("-+-+-+", "'", $str);
    $str = str_replace("@@@@@", "�", $str);
    $ky = "ILoveToEncryptData";
    $ky = str_replace(chr(32), "", $ky);
    if (strlen($ky) < 8) {
        exit("key error");
    }
    $kl = strlen($ky) < 32 ? strlen($ky) : 32;
    $k = array();
    for ($i = 0; $i < $kl; $i++) {
        $k[$i] = ord($ky[$i]) & 31;
    }
    $j = 0;
    for ($i = 0; $i < strlen($str); $i++) {
        $e = ord($str[$i]);
        $str[$i] = $e & 224 ? chr($e ^ $k[$j]) : chr($e);
        $j++;
        $j = $j == $kl ? 0 : $j;
    }
    $str = str_replace("'", "-+-+-+", $str);
    $str = str_replace("�", "@@@@@", $str);
    return $str;
}

function setTCount($conn) {
    $query = "SELECT MACAddress FROM OtherSettingMaster";
    $result = selectdata($conn, $query);
    $mac = encryptdecrypt($result[0]);
    $company_name = "";
    $count = "-1";
    if ($mac == "F8-D1-11-1B-4C-67") {
        $company_name = " , CompanyName = 'Healthline_Limited', CompanyDetail1 = '1A, Odunayo Adeyemi Close', CompanyDetail2 = 'Magodo GRA, Shangisha, Lagos' ";
        $count = "1";
    } else {
        if ($mac == "00-04-75-E3-EA-21") {
            $company_name = " , CompanyName = 'Ontime_Suppliers', CompanyDetail1 = 'Ilupeju', CompanyDetail2 = 'Lagos' ";
            $count = "1";
        } else {
            if ($mac == "78-45-C4-10-1F-A0") {
                $company_name = " , CompanyName = 'Equator_Foods_Ghana Ltd', CompanyDetail1 = '2&4, Baatsona, Spintex', CompanyDetail2 = 'Accra, Ghana' ";
                $count = "6";
            } else {
                if ($mac == "AC-16-2D-8C-2F-58") {
                    $company_name = " , CompanyName = 'Pardee_Foods_2', CompanyDetail1 = 'Lynson Chemical Avenue, Igbala Village, Off KM 38', CompanyDetail2 = 'Sango Ota, Ogun' ";
                    $count = "7";
                } else {
                    if ($mac == "00-15-5D-32-CC-10") {
                        $company_name = " , CompanyName = 'Artee Industries Ltd', CompanyDetail1 = '47, Adeola Odeku Street', CompanyDetail2 = 'VI, Lagos' ";
                        $count = "12";
                    } else {
                        if ($mac == "00-1D-92-0D-60-41") {
                            $company_name = " , CompanyName = 'Stallion Group of Companies', CompanyDetail1 = '270A, Ajose Adeogun Street, VI', CompanyDetail2 = 'Lagos' ";
                            $count = "419";
                        }
                    }
                }
            }
        }
    }
    $count = 175561;
    $count = strrev($mac) . "-" . $count;
    $count = encryptdecrypt($count);
    $query = "UPDATE OtherSettingMaster SET TCount = '" . $count . "' " . $company_name;
    updatedata($conn, $query, true);
    clearRecordMess($conn);
}

function displaySort($array, $default, $count) {
    if ($default == "") {
        $default = $array[0][0];
    }
//    print "<td align='right' width='25%'><font face='Verdana' size='2'>Sort By:</font></td><td><select name='lstSort' class='form-control-inner select2 form-select shadow-none'>";
    print "<label class='form-label'>Sort By:</label>";
    print "<select name='lstSort' class='form-control-inner select2 form-select shadow-none'>";
    for ($i = 0; $i < $count; $i++) {
        if ($default == $array[$i][0]) {
            print "<option selected value='" . $array[$i][0] . "'>" . $array[$i][1] . "</option>";
        } else {
            print "<option value='" . $array[$i][0] . "'>" . $array[$i][1] . "</option>";
        }
    }
    print "</select>";
//    print "</td>";
}

function oracle_connection($txtDBIP, $txtDBName, $txtDBUser, $txtDBPass) {
    $db = "(DESCRIPTION =  (ADDRESS = (PROTOCOL = TCP) (HOST = " . $txtDBIP . ") (PORT = 1521)) (CONNECT_DATA = (SERVER = DEDICATED) (SERVICE_NAME = " . $txtDBName . ") ) )";
    if ($c = oci_connect($txtDBUser, $txtDBPass, $db)) {
        return $c;
    }
    $db = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = " . $txtDBIP . ")(PORT = 1521)))(CONNECT_DATA=(SID=" . $txtDBName . ")))";
    if ($c = oci_connect($txtDBUser, $txtDBPass, $db)) {
        return $c;
    }
    $db = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (COMMUNITY = tcp.world)(PROTOCOL = TCP)(HOST = " . $txtDBIP . ")(PORT = 1521)))(CONNECT_DATA=(SID=" . $txtDBName . ")))";
    if ($c = oci_connect($txtDBUser, $txtDBPass, $db)) {
        return $c;
    }
    $err = oci_error();
    echo "\n\r" . $err;
    return "";
}

function odbc_connection($txtDBIP, $txtDBName, $txtDBUser, $txtDBPass) {
    $c = odbc_connect($txtDBName, $txtDBUser, $txtDBPass);
    return $c;
}

function mysql_connection($txtDBIP, $txtDBName, $txtDBUser, $txtDBPass) {
    $c = mysql_connect($txtDBIP, $txtDBUser, $txtDBPass);
    if ($txtDBName != "") {
        mysql_select_db($txtDBName, $c);
    }
    return $c;
}

function mysqli_connection($txtDBIP, $txtDBName, $txtDBUser, $txtDBPass) {
    $c = mysqli_connect($txtDBIP, $txtDBUser, $txtDBPass, $txtDBName);
    mysqli_autocommit($c, false);
    return $c;
}

function mssql_connection($txtDBIP, $txtDBName, $txtDBUser, $txtDBPass) {
    $c = mssql_connect($txtDBIP, $txtDBUser, $txtDBPass);
    if ($txtDBName != "") {
        mssql_select_db($txtDBName, $c);
    }
    return $c;
}

function clearRecordMess($conn) {
    $query = "SELECT TDate, e_id, DayMasterID FROM DayMaster WHERE Start < '000001' OR Close < '000001'";
    $result = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
        $query = "DELETE FROM AttendanceMaster WHERE ADate = '" . $cur[0] . "' AND EmployeeID = '" . $cur[1] . "'";
        updatedata($conn, $query, true);
        $query = "UPDATE tenter SET p_flag = '0' WHERE e_date = '" . $cur[0] . "' AND e_id = '" . $cur[1] . "'";
        updatedata($conn, $query, true);
        $query = "DELETE FROM DayMaster WHERE DayMasterID = '" . $cur[2] . "'";
        updatedata($conn, $query, true);
    }
    $query = "SELECT TDate, e_id, DayMasterID FROM DayMaster WHERE DayMasterID NOT IN (SELECT DayMaster.DayMasterID FROM DayMaster, AttendanceMaster WHERE DayMaster.Tdate = AttendanceMaster.ADate AND DayMaster.e_id = AttendanceMaster.EmployeeID)";
    $result = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
        $query = "UPDATE tenter SET p_flag = '0' WHERE e_date = '" . $cur[0] . "' AND e_id = '" . $cur[1] . "'";
        updatedata($conn, $query, true);
        $query = "DELETE FROM DayMaster WHERE DayMasterID = '" . $cur[2] . "'";
        updatedata($conn, $query, true);
    }
    $query = "SELECT ADate, EmployeeID, AttendanceID FROM AttendanceMaster WHERE AttendanceID NOT IN (SELECT AttendanceMaster.AttendanceID FROM DayMaster, AttendanceMaster WHERE DayMaster.Tdate = AttendanceMaster.ADate AND DayMaster.e_id = AttendanceMaster.EmployeeID)";
    $result = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
        $query = "UPDATE tenter SET p_flag = '0' WHERE e_date = '" . $cur[0] . "' AND e_id = '" . $cur[1] . "'";
        updatedata($conn, $query, true);
        $query = "DELETE FROM AttendanceMaster WHERE AttendanceID = '" . $cur[2] . "'";
        updatedata($conn, $query, true);
    }
    $query = "SELECT EmployeeCodeLength FROM OtherSettingMaster";
    $result = selectdata($conn, $query);
    $e_length = $result[0];
    $query = "SELECT AttendanceID, EmpID FROM AttendanceMaster WHERE LENGTH(EmpID) NOT LIKE '" . $e_length . "'";
    $result = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
        $query = "UPDATE AttendanceMaster SET EmpID = '" . addzero($cur[1], $e_length) . "' WHERE AttendanceID = '" . $cur[0] . "'";
        updatedata($conn, $query, true);
    }
}

function noTASoftware($conn, $mac) {
    if ($conn != "") {
        $query = "SELECT MACAddress FROM OtherSettingMaster";
        $result = selectdata($conn, $query);
        $mac = $result[0];
    }
    if (getRegister($mac, 7) == "8") {
        return true;
    }
    if (getRegister($mac, 7) == "37") {
        return true;
    }
    return false;
}

function getVirdiLevel($mac) {
    if (getRegister($mac, 7) == "1" || getRegister($mac, 7) == "2" || getRegister($mac, 7) == "65" || getRegister($mac, 7) == "122" || getRegister($mac, 7) == "142" || getRegister($mac, 7) == "143" || getRegister($mac, 7) == "173") {
        return "Basic";
    }
    return "Classic";
}

function getACServer($mac) {
    if (getRegister($mac, 7) == "3") {
        return true;
    }
    return false;
}

function getUNIS($mac) {
    if (getRegister($mac, 7) == "") {
        return true;
    }
    return false;
}

function group_query($conn, $group_query, $group_id) {
    $sub_query = "SELECT GroupDiv.Div FROM GroupDiv WHERE GroupID = " . $group_id;
    $sub_result = mysqli_query($conn, $sub_query);
    while ($sub_cur = mysqli_fetch_row($sub_result)) {
        $group_query = $group_query . " tuser.company = '" . $sub_cur[0] . "' OR ";
    }
    $sub_query = "SELECT Dept FROM GroupDept WHERE GroupID = " . $group_id;
    $sub_result = mysqli_query($conn, $sub_query);
    while ($sub_cur = mysqli_fetch_row($sub_result)) {
        $group_query = $group_query . " tuser.dept = '" . $sub_cur[0] . "' OR ";
    }
    $sub_query = "SELECT Remark FROM GroupRemark WHERE GroupID = " . $group_id;
    $sub_result = mysqli_query($conn, $sub_query);
    while ($sub_cur = mysqli_fetch_row($sub_result)) {
        $group_query = $group_query . " tuser.Remark = '" . $sub_cur[0] . "' OR ";
    }
    $sub_query = "SELECT Phone FROM GroupPhone WHERE GroupID = " . $group_id;
    $sub_result = mysqli_query($conn, $sub_query);
    while ($sub_cur = mysqli_fetch_row($sub_result)) {
        $group_query = $group_query . " tuser.Phone = '" . $sub_cur[0] . "' OR ";
    }
    $sub_query = "SELECT IdNo FROM GroupIdNo WHERE GroupID = " . $group_id;
    $sub_result = mysqli_query($conn, $sub_query);
    while ($sub_cur = mysqli_fetch_row($sub_result)) {
        $group_query = $group_query . " tuser.IdNo = '" . $sub_cur[0] . "' OR ";
    }
    $sub_query = "SELECT Shift FROM GroupShift WHERE GroupID = " . $group_id;
    $sub_result = mysqli_query($conn, $sub_query);
    while ($sub_cur = mysqli_fetch_row($sub_result)) {
        $group_query = $group_query . " tuser.group_id = '" . $sub_cur[0] . "' OR ";
    }
    $group_query = substr($group_query, 0, strlen($group_query) - 3);
    return $group_query;
}

function displayOTDay($conn, $title, $select, $data) {
    print "<label class='form-label'>" . $title . ":</label><select name='" . $select . "' id='" . $select . "' class='select2 form-select shadow-none'> <option value='' selected>---</option>";
    print "<option selected value='" . $data . "'>" . $data . "</option>";
    print "<option value='Monday'>Monday</option>";
    print "<option value='Tuesday'>Tuesday</option>";
    print "<option value='Wednesday'>Wednesday</option>";
    print "<option value='Thursday'>Thursday</option>";
    print "<option value='Friday'>Friday</option>";
    print "<option value='Saturday'>Saturday</option>";
    print "<option value='Sunday'>Sunday</option>";
    print "</select></td>";
}

function displayEmployeeStatus($conn, $select, $data, $w1, $w2) {
//    print "<td align='right' width='" . $w1 . "'><font face='Verdana' size='2'>Employee Status: </font></td> <td width='" . $w2 . "'>";
    print "<label class='form-label'>Employee Status:</label>";
    print "<select name='" . $select . "' class='select2 form-select shadow-none'> <option value='' selected>---</option>";
    print "<option selected value='" . $data . "'>";
    switch ($data) {
        case "ACT":
            print "ACT - Active";
            break;
        case "ADA":
            print "ADA - Unauthorized Absence DeActivation";
            break;
        case "FDA":
            print "FDA - Flagged DeActivation";
            break;
        case "ACT + FDA":
            print "ACT + FDA";
            break;
        case "ACT + ADA + FDA":
            print "ACT + ADA + FDA";
            break;
        case "PSV":
            print "PSV - Passive";
            break;
        case "EXM":
            print "EXM - Exempted";
            break;
        case "PRM":
            print "PRM - Promoted";
            break;
        case "RSN":
            print "RSN - Resigned";
            break;
        case "RTD":
            print "RTD - Retired";
            break;
        case "TRM":
            print "TRM - Terminated";
            break;
        case "XER":
            print "XER - Transferred";
            break;
        case "DSD":
            print "DSD - Deceased";
            break;
        case "SUS":
            print "SUS - Suspended";
            break;
        case "ABC":
            print "ABC - Abscortion";
            break;
        case "RUD":
            print "RUD - Redundant";
            break;
        case "AGR":
            print "AGR - AG Restriction";
            break;
        case "":
            print "---";
            break;
    }
    print "</option>";
    print "<option value='ACT'>ACT - Active</option>";
    print "<option value='ADA'>ADA - Absence DeActivation</option>";
    print "<option value='FDA'>FDA - Flagged DeActivation</option>";
    print "<option value='ACT + FDA'>ACT + FDA</option>";
    print "<option value='ACT + ADA + FDA'>ACT + ADA + FDA</option>";
    print "<option value='PSV'>PSV - Passive [Not Active]</option>";
    print "<option value='ABC'>ABC - Abscortion</option>";
    print "<option value='EXM'>EXM - Exempted</option>";
    print "<option value='PRM'>PRM - Promoted</option>";
    print "<option value='RSN'>RSN - Resigned</option>";
    print "<option value='RTD'>RTD - Retired</option>";
    print "<option value='RUD'>RUD - Redundant</option>";
    print "<option value='SUS'>SUS - Suspended</option>";
    print "<option value='TRM'>TRM - Terminated</option>";
    print "<option value='XER'>XER - Transferred</option>";
    print "<option value='DSD'>DSD - Deceased</option>";
    print "</select></td>";
}

function employeeStatusQuery($lstEmployeeStatus) {
    if ($lstEmployeeStatus == "PSV") {
        return " AND tuser.PassiveType NOT LIKE 'ACT' ";
    }
    if ($lstEmployeeStatus == "ACT + FDA") {
        return " AND (tuser.PassiveType LIKE 'ACT' OR tuser.PassiveType LIKE 'FDA')";
    }
    if ($lstEmployeeStatus == "ACT + ADA + FDA") {
        return " AND (tuser.PassiveType LIKE 'ACT' OR tuser.PassiveType LIKE 'ADA' OR tuser.PassiveType LIKE 'FDA')";
    }
    if ($lstEmployeeStatus != "") {
        return " AND tuser.PassiveType = '" . $lstEmployeeStatus . "' ";
    }
    return "";
}

function getASS($conn, $id, $txtFrom, $txtTo) {
    $query = "SELECT * FROM TLSFlag";
    $colour_result = selectdata($conn, $query);
    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, AttendanceMaster.group_min, AttendanceMaster.Normal, AttendanceMaster.Grace, AttendanceMaster.Overtime, AttendanceMaster.ADate, tuser.idno, tuser.remark, AttendanceMaster.Flag, AttendanceMaster.Day, tgroup.NightFlag, AttendanceMaster.AOvertime, AttendanceMaster.OT1, AttendanceMaster.OT2, tuser.PassiveType FROM tuser, tgroup, AttendanceMaster WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.Flag <> 'Delete' AND AttendanceMaster.EmployeeID = tuser.id AND tuser.id = '" . $id . "'";
    if ($txtFrom != "") {
        $query .= " AND (AttendanceMaster.ADate >= " . insertdate($txtFrom) . " ) ";
    }
    if ($txtTo != "") {
        $query .= " AND (AttendanceMaster.ADate <= " . insertdate($txtTo) . " ) ";
    }
    $query .= " ORDER BY tuser.id, AttendanceMaster.ADate";
    $dayCount = gettotaldays($txtFrom, $txtTo);
    $row_count = 0;
    $count = 0;
    $subc = 0;
    $eid = "";
    $wkdn = 0;
    $wkdo = 0;
    $pxyn = 0;
    $pxyo = 0;
    $flgn = 0;
    $flgo = 0;
    $satn = 0;
    $sato = 0;
    $sunn = 0;
    $suno = 0;
    $nsn = 0;
    $nso = 0;
    $nfn = 0;
    $nfo = 0;
    $tld = 0;
    $violet = 0;
    $indigo = 0;
    $blue = 0;
    $green = 0;
    $yellow = 0;
    $orange = 0;
    $red = 0;
    $gray = 0;
    $brown = 0;
    $purple = 0;
    $black = 0;
    $h_wkdn = 0;
    $h_wkdo = 0;
    $h_wkdao = 0;
    $h_pxyn = 0;
    $h_pxyo = 0;
    $h_pxyao = 0;
    $h_flgn = 0;
    $h_flgo = 0;
    $h_flgao = 0;
    $h_satn = 0;
    $h_sato = 0;
    $h_satao = 0;
    $h_sunn = 0;
    $h_suno = 0;
    $h_sunao = 0;
    $h_nsn = 0;
    $h_nso = 0;
    $h_nsao = 0;
    $h_nfn = 0;
    $h_nfo = 0;
    $h_nfao = 0;
    $h_satabn = 0;
    $h_satabo = 0;
    $h_sunabn = 0;
    $h_sunabo = 0;
    $txtDate = insertdate($txtFrom);
    $txtLastDate = $txtDate;
    $data9 = "";
    $sunCount = 0;
    $satCount = 0;
    $result = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
        if ($eid != $cur[0]) {
            $ot_query = "SELECT OT1, OT2 FROM AttendanceMaster WHERE EmployeeID = " . $cur[0] . " AND ADate >= " . insertdate($txtFrom) . " AND ADate <= " . insertdate($txtTo);
            $ot_result = selectdata($conn, $ot_query);
            if ($ot_result[0] == "") {
                $ot_result[0] = "Saturday";
            }
            if ($ot_result[1] == "") {
                $ot_result[1] = "Sunday";
            }
            $satCount = getdaycount(insertdate($txtFrom), insertdate($txtTo), $dayCount, $ot_result[0]);
            $sunCount = getdaycount(insertdate($txtFrom), insertdate($txtTo), $dayCount, $ot_result[1]);
            $eid = $cur[0];
            $subc = 0;
            $wkdn = 0;
            $wkdo = 0;
            $pxyn = 0;
            $pxyo = 0;
            $flgn = 0;
            $flgo = 0;
            $satn = 0;
            $sato = 0;
            $sunn = 0;
            $suno = 0;
            $nsn = 0;
            $nso = 0;
            $nfn = 0;
            $nfo = 0;
            $tld = 0;
            $violet = 0;
            $indigo = 0;
            $blue = 0;
            $green = 0;
            $yellow = 0;
            $orange = 0;
            $red = 0;
            $gray = 0;
            $brown = 0;
            $purple = 0;
            $black = 0;
            $h_wkdn = 0;
            $h_wkdo = 0;
            $h_wkdao = 0;
            $h_pxyn = 0;
            $h_pxyo = 0;
            $h_pxyao = 0;
            $h_flgn = 0;
            $h_flgo = 0;
            $h_flgao = 0;
            $h_satn = 0;
            $h_sato = 0;
            $h_satao = 0;
            $h_sunn = 0;
            $h_suno = 0;
            $h_sunao = 0;
            $h_nsn = 0;
            $h_nso = 0;
            $h_nsao = 0;
            $h_nfn = 0;
            $h_nfo = 0;
            $h_nfao = 0;
            $h_satabn = 0;
            $h_satabo = 0;
            $h_sunabn = 0;
            $h_sunabo = 0;
            $wkda = 0;
            $wkdsata = 0;
            if ($cur[14] == 1) {
                $txtDate = getlastday(insertdate($txtFrom), 1);
                $txtLastDate = $txtDate;
            } else {
                $txtDate = insertdate($txtFrom);
                $txtLastDate = $txtDate;
            }
        }
        while (true) {
            $subc++;
            if ($cur[9] == $txtDate || $cur[9] == $txtLastDate) {
                if ($cur[12] != "Black" && $cur[12] != "Proxy") {
                    if (0 < $cur[8]) {
                        $flgo++;
                        if ($cur[13] == $cur[16]) {
                            $h_satabo++;
                        }
                        if ($cur[13] == $cur[17]) {
                            $h_sunabo++;
                        }
                    } else {
                        $flgn++;
                        if ($cur[13] == $cur[16]) {
                            $h_satabn++;
                        }
                        if ($cur[13] == $cur[17]) {
                            $h_sunabn++;
                        }
                    }
                    if ($cur[14] == 1) {
                        if (0 < $cur[8]) {
                            $nfo++;
                        } else {
                            $nfn++;
                        }
                        $h_nfn = $h_nfn + $cur[6];
                        $h_nfo = $h_nfo + $cur[8];
                        $h_nfao = $h_nfao + $cur[15];
                    }
                    $h_flgn = $h_flgn + $cur[6];
                    $h_flgo = $h_flgo + $cur[8];
                    $h_flgao = $h_flgao + $cur[15];
                    if ($cur[12] == "Violet") {
                        $violet++;
                        if ($colour_result[1] == "No") {
                            $tld++;
                        }
                    } else {
                        if ($cur[12] == "Indigo") {
                            $indigo++;
                            if ($colour_result[2] == "No") {
                                $tld++;
                            }
                        } else {
                            if ($cur[12] == "Blue") {
                                $blue++;
                                if ($colour_result[3] == "No") {
                                    $tld++;
                                }
                            } else {
                                if ($cur[12] == "Green") {
                                    $green++;
                                    if ($colour_result[4] == "No") {
                                        $tld++;
                                    }
                                } else {
                                    if ($cur[12] == "Yellow") {
                                        $yellow++;
                                        if ($colour_result[5] == "No") {
                                            $tld++;
                                        }
                                    } else {
                                        if ($cur[12] == "Orange") {
                                            $orange++;
                                            if ($colour_result[6] == "No") {
                                                $tld++;
                                            }
                                        } else {
                                            if ($cur[12] == "Red") {
                                                $red++;
                                                if ($colour_result[7] == "No") {
                                                    $tld++;
                                                }
                                            } else {
                                                if ($cur[12] == "Gray") {
                                                    $gray++;
                                                    if ($colour_result[8] == "No") {
                                                        $tld++;
                                                    }
                                                } else {
                                                    if ($cur[12] == "Brown") {
                                                        $brown++;
                                                        if ($colour_result[9] == "No") {
                                                            $tld++;
                                                        }
                                                    } else {
                                                        if ($cur[12] == "Purple") {
                                                            $purple++;
                                                            if ($colour_result[10] == "No") {
                                                                $tld++;
                                                            }
                                                        } else {
                                                            if ($cur[12] == "Black") {
                                                                $black++;
                                                                if ($colour_result[11] == "No") {
                                                                    $tld++;
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
                } else {
                    if ($cur[13] == $cur[16]) {
                        if (0 < $cur[8]) {
                            $sato++;
                        } else {
                            $satn++;
                        }
                        if ($cur[14] == 1) {
                            if (0 < $cur[8]) {
                                $nso++;
                            } else {
                                $nsn++;
                            }
                            $h_nsn = $h_nsn + $cur[6];
                            $h_nso = $h_nso + $cur[8];
                            $h_nsao = $h_nsao + $cur[15];
                        }
                        $h_satn = $h_satn + $cur[6];
                        $h_sato = $h_sato + $cur[8];
                        $h_satao = $h_satao + $cur[15];
                    } else {
                        if ($cur[13] == $cur[17]) {
                            if (0 < $cur[8]) {
                                $suno++;
                            } else {
                                $sunn++;
                            }
                            if ($cur[14] == 1) {
                                if (0 < $cur[8]) {
                                    $nso++;
                                } else {
                                    $nsn++;
                                }
                                $h_nsn = $h_nsn + $cur[6];
                                $h_nso = $h_nso + $cur[8];
                                $h_nsao = $h_nsao + $cur[15];
                            }
                            $h_sunn = $h_sunn + $cur[6];
                            $h_suno = $h_suno + $cur[8];
                            $h_sunao = $h_sunao + $cur[15];
                        } else {
                            if ($cur[12] == "Proxy") {
                                if (0 < $cur[8]) {
                                    $pxyo++;
                                } else {
                                    $pxyn++;
                                }
                                if ($cur[14] == 1) {
                                    if (0 < $cur[8]) {
                                        $nfo++;
                                    } else {
                                        $nfn++;
                                    }
                                    $h_nfn = $h_nfn + $cur[6];
                                    $h_nfo = $h_nfo + $cur[8];
                                    $h_nfao = $h_nfao + $cur[15];
                                }
                                $h_pxyn = $h_pxyn + $cur[6];
                                $h_pxyo = $h_pxyo + $cur[8];
                                $h_pxyao = $h_pxyao + $cur[15];
                                if ($colour_result[12] == "No") {
                                    $tld++;
                                }
                            } else {
                                if ($cur[12] == "Black") {
                                    if (0 < $cur[8]) {
                                        $wkdo++;
                                    } else {
                                        $wkdn++;
                                    }
                                    if ($cur[14] == 1) {
                                        if (0 < $cur[8]) {
                                            $nso++;
                                        } else {
                                            $nsn++;
                                        }
                                        $h_nsn = $h_nsn + $cur[6];
                                        $h_nso = $h_nso + $cur[8];
                                        $h_nsao = $h_nsao + $cur[15];
                                    }
                                    $h_wkdn = $h_wkdn + $cur[6];
                                    $h_wkdo = $h_wkdo + $cur[8];
                                    $h_wkdao = $h_wkdao + $cur[15];
                                    if ($colour_result[11] == "No") {
                                        $tld++;
                                    }
                                }
                            }
                        }
                    }
                }
                $next = strtotime(substr($txtDate, 6, 2) . "-" . substr($txtDate, 4, 2) . "-" . substr($txtDate, 0, 4) . " + 1 day");
                $a = getDate($next);
                $m = $a["mon"];
                if ($m < 10) {
                    $m = "0" . $m;
                }
                $d = $a["mday"];
                if ($d < 10) {
                    $d = "0" . $d;
                }
                $txtDate = $a["year"] . $m . $d;
                break;
            }
            if ($dayCount < $subc) {
                break;
            }
            $next = strtotime(substr($txtDate, 6, 2) . "-" . substr($txtDate, 4, 2) . "-" . substr($txtDate, 0, 4) . " + 1 day");
            $a = getDate($next);
            $m = $a["mon"];
            if ($m < 10) {
                $m = "0" . $m;
            }
            $d = $a["mday"];
            if ($d < 10) {
                $d = "0" . $d;
            }
            $txtDate = $a["year"] . $m . $d;
        }
        $count++;
        $data9 = $cur[9];
    }
    if (0 < $count) {
        $this_record = $dayCount + $h_sunabo + $h_sunabn + $h_satabo + $h_satabn - $sunCount - $satCount - ($wkdn + $pxyn + $flgn + $wkdo + $pxyo + $flgo - $tld);
        if ($this_record < 0) {
            $this_record = 0;
        }
        return $this_record;
    }
    return 0;
}

function getAS($conn, $id, $txtFrom, $txtTo) {
    $ass = getass($conn, $id, $txtFrom, $txtTo);
    $dayCount = gettotaldays($txtFrom, $txtTo);
    $satCount = getdaycount(insertdate($txtFrom), insertdate($txtTo), $dayCount, "Saturday");
    $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster WHERE ADate >= " . insertdate($txtFrom) . " AND ADate <= " . insertdate($txtTo) . " AND Day = 'Saturday' AND EmployeeID = " . $id;
    $result = selectdata($conn, $query);
    return $ass + $satCount - $result[0] * 1;
}

function getTLSFlag($conn) {
    $query = "SELECT * FROM TLSFLag";
    $result = selectdata($conn, $query);
    $str_flag = "'Mazda'";
    if ($result[1] != "Yes") {
        $str_flag .= ", 'Violet'";
    }
    if ($result[2] != "Yes") {
        $str_flag .= ", 'Indigo'";
    }
    if ($result[3] != "Yes") {
        $str_flag .= ", 'Blue'";
    }
    if ($result[4] != "Yes") {
        $str_flag .= ", 'Green'";
    }
    if ($result[5] != "Yes") {
        $str_flag .= ", 'Yellow'";
    }
    if ($result[6] != "Yes") {
        $str_flag .= ", 'Orange'";
    }
    if ($result[7] != "Yes") {
        $str_flag .= ", 'Red'";
    }
    if ($result[8] != "Yes") {
        $str_flag .= ", 'Gray'";
    }
    if ($result[9] != "Yes") {
        $str_flag .= ", 'Brown'";
    }
    if ($result[10] != "Yes") {
        $str_flag .= ", 'Purple'";
    }
    if ($result[11] != "Yes") {
        $str_flag .= ", 'Black'";
    }
    if ($result[12] != "Yes") {
        $str_flag .= ", 'Proxy'";
    }
    if ($result[13] != "Yes") {
        $str_flag .= ", 'Magenta'";
    }
    if ($result[14] != "Yes") {
        $str_flag .= ", 'Teal'";
    }
    if ($result[15] != "Yes") {
        $str_flag .= ", 'Aqua'";
    }
    if ($result[16] != "Yes") {
        $str_flag .= ", 'Safron'";
    }
    if ($result[17] != "Yes") {
        $str_flag .= ", 'Amber'";
    }
    if ($result[18] != "Yes") {
        $str_flag .= ", 'Gold'";
    }
    if ($result[19] != "Yes") {
        $str_flag .= ", 'Vermilion'";
    }
    if ($result[20] != "Yes") {
        $str_flag .= ", 'Silver'";
    }
    if ($result[21] != "Yes") {
        $str_flag .= ", 'Maroon'";
    }
    if ($result[22] != "Yes") {
        $str_flag .= ", 'Pink'";
    }
    return $str_flag;
}

function getP($conn, $id, $txtFrom, $txtTo) {
    $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster WHERE EmployeeID = " . $id . " AND ADate >= " . insertdate($txtFrom) . " AND ADate <= " . insertdate($txtTo) . " AND Flag NOT IN (" . gettlsflag($conn) . ")";
    $result = selectdata($conn, $query);
    return $result[0];
}

function getA($conn, $id, $txtFrom, $txtTo) {
    return gettotaldays($txtFrom, $txtTo) - getp($conn, $id, $txtFrom, $txtTo);
}

function getFlagTitle($txtFlagText, $flag) {
    $return = $flag;
    $flag_array = explode("<br>", $txtFlagText);
    for ($i = 0; $i < count($flag_array); $i++) {
        if (stripos($flag_array[$i], $flag) !== false) {
            $return = substr($flag_array[$i], strpos($flag_array[$i], " = ") + 3, strlen($flag_array[$i]));
            $return = substr($return, 0, strpos($return, "</font>"));
            break;
        }
    }
    return $return;
}

function sra($conn, $jconn, $kconn, $rd, $idf) {
    $flag = true;
    $query = "SELECT DISTINCT(ShiftChangeMaster.id), ShiftChangeMaster.AE, ShiftChangeMaster.SRScenario, tgroup.Start, tgroup.Close, tgroup.NightFlag FROM ShiftChangeMaster, tgroup WHERE ShiftChangeMaster.id = tgroup.id AND AE = 1 AND idf = '" . $idf . "' ";
    $result = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
        $sub_query = "SELECT id FROM tuser WHERE group_id = '" . $cur[0] . "'";
        $sub_result = mysqli_query($jconn, $sub_query);
        while ($sub_cur = mysqli_fetch_row($sub_result)) {
            if ($flag) {
                switch ($cur[2]) {
                    case "Morning - 3 Shifts":
                        if ($cur[5] == 0) {
                            $query = "INSERT INTO SRA (e_id, e_group, gFrom, gTo) VALUES ('" . $sub_cur[0] . "', '" . $cur[0] . "', '" . getlasttime($rd, $cur[3] . "00", 2) . "', '" . getnexttime($rd, $cur[4] . "00", 146) . "') ";
                        } else {
                            $query = "INSERT INTO SRA (e_id, e_group, gFrom, gTo) VALUES ('" . $sub_cur[0] . "', '" . $cur[0] . "', '" . getlasttime($rd, $cur[3] . "00", 2) . "', '" . getnexttime($rd, $cur[4] . "00", 170) . "') ";
                        }
                        break;
                    case "Evening - 3 Shifts":
                        if ($cur[5] == 0) {
                            $query = "INSERT INTO SRA (e_id, e_group, gFrom, gTo) VALUES ('" . $sub_cur[0] . "', '" . $cur[0] . "', '" . getnexttime($rd, $cur[3] . "00", 22) . "', '" . getnexttime($rd, $cur[4] . "00", 170) . "') ";
                        } else {
                            $query = "INSERT INTO SRA (e_id, e_group, gFrom, gTo) VALUES ('" . $sub_cur[0] . "', '" . $cur[0] . "', '" . getlasttime($rd, $cur[3] . "00", 2) . "', '" . getnexttime($rd, $cur[4] . "00", 170) . "') ";
                        }
                        break;
                    case "Morning - 2 Shifts (Day to Day Shift)":
                        $query = "INSERT INTO SRA (e_id, e_group, gFrom, gTo) VALUES ('" . $sub_cur[0] . "', '" . $cur[0] . "', '" . getlasttime($rd, $cur[3] . "00", 2) . "', '" . getnexttime($rd, $cur[4] . "00", 146) . "') ";
                        break;
                }
                if (updateidata($kconn, $query, true)) {
                    $flag = true;
                } else {
                    $flag = false;
                }
            }
        }
    }
    if ($flag) {
        mysqli_commit($kconn);
        return true;
    }
    return false;
}

function migrateMaster($conn, $iconn) {
    $t_user = array();
    $t_user = ["idno", "dept", "company", "phone", "remark"];
    for ($i = 0; $i < count($t_user); $i++) {
        $query = "SELECT DISTINCT(" . $t_user[$i] . ") FROM tuser WHERE LENGTH(" . $t_user[$i] . ") > 0 AND " . $t_user[$i] . " IS NOT NULL";
        $result = mysqli_query($conn, $query);

        while ($cur = mysqli_fetch_row($result)) {
            // Check if the value already exists in MigrateMaster
            $checkQuery = "SELECT COUNT(*) FROM MigrateMaster WHERE Col = '" . $t_user[$i] . "' AND Val = '" . $cur[0] . "'";
            $checkResult = mysqli_query($iconn, $checkQuery);
            $checkRow = mysqli_fetch_row($checkResult);

            // Only insert if the combination does not already exist
            if ($checkRow[0] == 0) {
                $insertQuery = "INSERT INTO MigrateMaster (Col, Val) VALUES ('" . $t_user[$i] . "', '" . $cur[0] . "')";
                updateidata($iconn, $insertQuery, true);
            }
        }

        $deleteQuery = "DELETE FROM MigrateMaster WHERE Col = '" . $t_user[$i] . "' AND Val NOT IN (SELECT DISTINCT(" . $t_user[$i] . ") FROM tuser)";
        updateidata($iconn, $deleteQuery, true);
    }
}

//function migrateMaster($conn, $iconn)
//{
//    $t_user = [];
//    $t_user[0] = "idno";
//    $t_user[1] = "dept";
//    $t_user[2] = "company";
//    $t_user[3] = "phone";
//    $t_user[4] = "remark";
//    for ($i = 0; $i < count($t_user); $i++) {
//        $query = "SELECT DISTINCT(" . $t_user[$i] . ") FROM tuser WHERE LENGTH(" . $t_user[$i] . ") > 0 AND " . $t_user[$i] . " IS NOT NULL";
//        $result = mysqli_query($conn, $query);
//        while ($cur = mysqli_fetch_row($result)) {
//            $query = "INSERT INTO MigrateMaster (Col, Val) VALUES ('" . $t_user[$i] . "', '" . $cur[0] . "')";
//            updateidata($iconn, $query, true);
//        }
//        $query = "DELETE FROM MigrateMaster WHERE Col = '" . $t_user[$i] . "' AND Val NOT IN (SELECT DISTINCT(" . $t_user[$i] . ") FROM tuser) ";
//        updateidata($iconn, $query, true);
//    }
//}
function mailerDeptDiv($conn, $iconn, $jconn, $username, $userstatus) {
    $dept_query = "SELECT Dept FROM UserDept WHERE Username = '" . trim($username) . "'";
    $result = mysqli_query($jconn, $dept_query);
    $query = "";
    for ($i = 0; $sdcur = mysqli_fetch_row($result); $i++) {
        if ($sdcur[0] != "") {
            if ($i == 0) {
                $query = " AND (tuser.dept = '" . $sdcur[0] . "' ";
            } else {
                $query = $query . " OR tuser.dept = '" . $sdcur[0] . "' ";
            }
        }
    }
    if (5 < strlen($query)) {
        $query = $query . " ) ";
        $dept_query = $query . " AND Userstatus > " . $userstatus;
    } else {
        $dept_query = $query . " AND Userstatus > " . $userstatus;
    }
    $div_query = "SELECT UserDiv.Div FROM UserDiv WHERE Username = '" . trim($username) . "'";
    $query = "";
    $i = 0;
    for ($result = mysqli_query($conn, $div_query); $sdcur = mysqli_fetch_row($result); $i++) {
        if ($sdcur[0] != "") {
            if ($i == 0) {
                $query = " AND (tuser.company = '" . $sdcur[0] . "' ";
            } else {
                $query = $query . " OR tuser.company = '" . $sdcur[0] . "' ";
            }
        }
    }
    if (5 < strlen($query)) {
        $query = $query . " ) ";
        $div_query = $query . " AND Userstatus > " . $userstatus;
    } else {
        $div_query = $query . " AND Userstatus > " . $userstatus;
    }
    /******************Employee Access**************************/
    $emp_query = "SELECT userf3.F3 FROM userf3 WHERE Username = '" . trim($username) . "'";
    $result = mysqli_query($jconn, $emp_query);
    $query = "";
    for ($i = 0; $sdcur = mysqli_fetch_row($result); $i++) {
        if ($sdcur[0] != "") {
            if ($i == 0) {
                $query = " AND (tuser.name = '" . $sdcur[0] . "' ";
            } else {
                $query = $query . " OR tuser.name = '" . $sdcur[0] . "' ";
            }
        }
    }
    if (5 < strlen($query)) {
        $query = $query . " ) ";
        $emp_query = $query . " AND Userstatus > " . $userstatus;
    } else {
        $emp_query = $query . " AND Userstatus > " . $userstatus;
    }
    /*******************************************/
    return $dept_query . $div_query . $emp_query;
}

function createTrigger($aconn, $mac, $x) {
    $uconn = mysqli_connect("127.0.0.1", "unisuser", "unisamho", "UNIS");
    if ($x == 0 || $x == 1) {
        $query = "DROP TRIGGER IF EXISTS Access.i_tuser ";
        mysqli_query($aconn, $query);
        $query = "CREATE TRIGGER i_tuser AFTER INSERT ON Access.tuser FOR EACH ROW INSERT INTO Access.EmployeeFlag (EmployeeID) VALUES (New.id) ";
        mysqli_query($aconn, $query);
        $query = "DROP TRIGGER IF EXISTS Access.d_tuser ";
        mysqli_query($aconn, $query);
        $query = "CREATE TRIGGER d_tuser AFTER DELETE ON Access.tuser FOR EACH ROW DELETE FROM Access.EmployeeFlag WHERE EmployeeID = old.id ";
        mysqli_query($aconn, $query);
    }
    if ($x == 0 || $x == 2) {
        $query = "DROP TRIGGER IF EXISTS UNIS.i_tuser ";
        mysqli_query($uconn, $query);
        $query = "DROP TRIGGER IF EXISTS UNIS.u_tuser ";
        mysqli_query($uconn, $query);
        $query = "DROP TRIGGER IF EXISTS UNIS.u_temploye ";
        mysqli_query($uconn, $query);
        if (getRegister($mac, 7) == "7") {
            $query = "CREATE TRIGGER UNIS.i_tuser AFTER INSERT ON UNIS.tuser FOR EACH ROW INSERT INTO Access.tuser (id, name, reg_date, datelimit, PassiveType, dept, company, phone, remark, F6) VALUES (New.L_ID, New.C_Name, IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), IFNULL( ( CASE WHEN New.L_OptDateLimit = 0 THEN CONCAT('N', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN CONCAT('Y', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) >= CURDATE()*1 THEN CONCAT('N', New.C_DateLimit) ELSE datelimit END ), 'N2001010120200101'), IFNULL( ( CASE WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN 'RSN' ELSE 'ACT' END ), 'ACT'), (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID), New.C_Unique, New.C_UserMessage, (SELECT C_Phone FROM UNIS.temploye WHERE UNIS.temploye.L_UID = New.L_ID)) ";
            mysqli_query($uconn, $query);
            $query = "CREATE TRIGGER UNIS.u_tuser AFTER UPDATE ON UNIS.tuser FOR EACH ROW UPDATE Access.tuser SET name = New.C_Name, reg_date = IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), datelimit = IFNULL( ( CASE WHEN New.L_OptDateLimit = 0 THEN CONCAT('N', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN CONCAT('Y', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) >= CURDATE()*1 THEN CONCAT('N', New.C_DateLimit) ELSE datelimit END ), 'N2001010120200101'), PassiveType = IFNULL( ( CASE WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN 'RSN' ELSE 'ACT' END ), 'ACT'), dept = (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), company = (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID), phone = New.C_Unique, remark = New.C_UserMessage, F6 = (SELECT C_Phone FROM UNIS.temploye WHERE UNIS.temploye.L_UID = New.L_ID) WHERE id = New.L_ID ";
            mysqli_query($uconn, $query);
            $query = "CREATE TRIGGER UNIS.u_temploye AFTER UPDATE ON UNIS.temploye FOR EACH ROW UPDATE Access.tuser SET dept = (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_UID), company = (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_UID), F6 = (SELECT C_Phone FROM UNIS.temploye WHERE UNIS.temploye.L_UID = New.L_UID), group_id = IFNULL((SELECT C_Work FROM UNIS.temploye WHERE UNIS.temploye.L_UID = New.L_UID), 0) WHERE id = New.L_UID ";
            mysqli_query($uconn, $query);
        } else {
            if (getRegister($mac, 7) == "82" || getRegister($mac, 7) == "103" || getRegister($mac, 7) == "106" || getRegister($mac, 7) == "107" || getRegister($mac, 7) == "126" || getRegister($mac, 7) == "57" || getRegister($mac, 7) == "58" || getRegister($mac, 7) == "59" || getRegister($mac, 7) == "60") {
                $query = "CREATE TRIGGER UNIS.i_tuser AFTER INSERT ON UNIS.tuser FOR EACH ROW INSERT INTO Access.tuser (id, name, reg_date, datelimit, PassiveType, dept, company, idno) VALUES (New.L_ID, New.C_Name, IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), IFNULL( ( CASE WHEN New.L_OptDateLimit = 0 THEN CONCAT('N', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN CONCAT('Y', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) >= CURDATE()*1 THEN CONCAT('N', New.C_DateLimit) ELSE datelimit END ), 'N2001010120200101'), IFNULL( ( CASE WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN 'RSN' ELSE 'ACT' END ), 'ACT'), (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID), New.C_Unique) ";
                mysqli_query($uconn, $query);
                $query = "CREATE TRIGGER UNIS.u_tuser AFTER UPDATE ON UNIS.tuser FOR EACH ROW UPDATE Access.tuser SET name = New.C_Name, reg_date = IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), datelimit = IFNULL( ( CASE WHEN New.L_OptDateLimit = 0 THEN CONCAT('N', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN CONCAT('Y', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) >= CURDATE()*1 THEN CONCAT('N', New.C_DateLimit) ELSE datelimit END ), 'N2001010120200101'), PassiveType = IFNULL( ( CASE WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN 'RSN' ELSE 'ACT' END ), 'ACT'), dept = (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), company = (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID), idno = New.C_Unique WHERE id = New.L_ID ";
                mysqli_query($uconn, $query);
                $query = "CREATE TRIGGER UNIS.u_temploye AFTER UPDATE ON UNIS.temploye FOR EACH ROW UPDATE Access.tuser SET dept = (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_UID), company = (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_UID) WHERE id = New.L_UID ";
                mysqli_query($uconn, $query);
            } else {
                if (getRegister($mac, 7) == "133") {
                    $query = "CREATE TRIGGER UNIS.i_tuser AFTER INSERT ON UNIS.tuser FOR EACH ROW INSERT INTO Access.tuser (id, name, reg_date, datelimit, PassiveType, dept, company, phone, remark, F2, F3, F4, F5, idno) VALUES (New.L_ID, New.C_Name, IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), IFNULL( ( CASE WHEN New.L_OptDateLimit = 0 THEN CONCAT('N', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN CONCAT('Y', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) >= CURDATE()*1 THEN CONCAT('N', New.C_DateLimit) ELSE datelimit END ), 'N2001010120200101'), IFNULL( ( CASE WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN 'RSN' ELSE 'ACT' END ), 'ACT'), (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID), New.C_Unique, (SELECT C_Name FROM UNIS.cStaff, UNIS.temploye WHERE UNIS.temploye.C_Staff = UNIS.cStaff.C_Code AND UNIS.temploye.L_UID = New.L_ID), (SELECT C_Address FROM UNIS.temploye WHERE UNIS.temploye.L_UID = New.L_ID), New.C_UserMessage, (SELECT C_Remark FROM UNIS.temploye WHERE UNIS.temploye.L_UID = New.L_ID), (SELECT C_Phone FROM UNIS.temploye WHERE UNIS.temploye.L_UID = New.L_ID), (SELECT C_Address FROM UNIS.temploye WHERE UNIS.temploye.L_UID = New.L_ID)) ";
                    mysqli_query($uconn, $query);
                    $query = "CREATE TRIGGER UNIS.u_tuser AFTER UPDATE ON UNIS.tuser FOR EACH ROW UPDATE Access.tuser SET name = New.C_Name, reg_date = IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), datelimit = IFNULL( ( CASE WHEN New.L_OptDateLimit = 0 THEN CONCAT('N', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN CONCAT('Y', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) >= CURDATE()*1 THEN CONCAT('N', New.C_DateLimit) ELSE datelimit END ), 'N2001010120200101'), PassiveType = IFNULL( ( CASE WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN 'RSN' ELSE 'ACT' END ), 'ACT'), dept = (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), company = (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID), phone = New.C_Unique, remark = (SELECT C_Name FROM UNIS.cStaff, UNIS.temploye WHERE UNIS.temploye.C_Staff = UNIS.cStaff.C_Code AND UNIS.temploye.L_UID = New.L_ID), F2 = (SELECT C_Address FROM UNIS.temploye WHERE UNIS.temploye.L_UID = New.L_ID), F3 = New.C_UserMessage, F4 = (SELECT C_Remark FROM UNIS.temploye WHERE UNIS.temploye.L_UID = New.L_ID), F5 = (SELECT C_Phone FROM UNIS.temploye WHERE UNIS.temploye.L_UID = New.L_ID), idno = (SELECT C_Address FROM UNIS.temploye WHERE UNIS.temploye.L_UID = New.L_ID) WHERE id = New.L_ID ";
                    mysqli_query($uconn, $query);
                    $query = "CREATE TRIGGER UNIS.u_temploye AFTER UPDATE ON UNIS.temploye FOR EACH ROW UPDATE Access.tuser SET dept = (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_UID), company = (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_UID), remark = (SELECT C_Name FROM UNIS.cStaff, UNIS.temploye WHERE UNIS.temploye.C_Staff = UNIS.cStaff.C_Code AND UNIS.temploye.L_UID = New.L_UID), F2 = (SELECT C_Address FROM UNIS.temploye WHERE UNIS.temploye.L_UID = New.L_UID), F4 = (SELECT C_Remark FROM UNIS.temploye WHERE UNIS.temploye.L_UID = New.L_UID), F5 = (SELECT C_Phone FROM UNIS.temploye WHERE UNIS.temploye.L_UID = New.L_UID), idno = (SELECT C_Address FROM UNIS.temploye WHERE UNIS.temploye.L_UID = New.L_UID) WHERE id = New.L_UID ";
                    mysqli_query($uconn, $query);
                } else {
                    if (getRegister($mac, 7) == "137") {
                        $query = "CREATE TRIGGER UNIS.i_tuser AFTER INSERT ON UNIS.tuser FOR EACH ROW INSERT INTO Access.tuser (id, name, reg_date, datelimit, PassiveType, dept, company, idno) VALUES (New.L_ID, New.C_Name, IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), IFNULL( ( CASE WHEN New.L_OptDateLimit = 0 THEN CONCAT('N', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN CONCAT('Y', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) >= CURDATE()*1 THEN CONCAT('N', New.C_DateLimit) ELSE datelimit END ), 'N2001010120200101'), IFNULL( ( CASE WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN 'RSN' ELSE 'ACT' END ), 'ACT'), (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID), New.C_Unique) ";
                        mysqli_query($uconn, $query);
                        $query = "CREATE TRIGGER UNIS.u_tuser AFTER UPDATE ON UNIS.tuser FOR EACH ROW UPDATE Access.tuser SET name = New.C_Name, reg_date = IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), datelimit = IFNULL( ( CASE WHEN New.L_OptDateLimit = 0 THEN CONCAT('N', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN CONCAT('Y', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) >= CURDATE()*1 THEN CONCAT('N', New.C_DateLimit) ELSE datelimit END ), 'N2001010120200101'), PassiveType = IFNULL( ( CASE WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN 'RSN' ELSE 'ACT' END ), 'ACT'), dept = (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), company = (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID), idno = New.C_Unique WHERE id = New.L_ID ";
                        mysqli_query($uconn, $query);
                        $query = "CREATE TRIGGER UNIS.u_temploye AFTER UPDATE ON UNIS.temploye FOR EACH ROW UPDATE Access.tuser SET dept = (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_UID), company = (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_UID) WHERE id = New.L_UID ";
                        mysqli_query($uconn, $query);
                    } else {
                        if (getRegister($mac, 7) == "165") {
                            $query = "CREATE TRIGGER UNIS.i_tuser AFTER INSERT ON UNIS.tuser FOR EACH ROW INSERT INTO Access.tuser (id, name, reg_date, datelimit, PassiveType, dept, company, remark, F1, F2, F3, F4, F5, F6, F7, group_id, idno) VALUES (New.L_ID, New.C_Name, IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), IFNULL( ( CASE WHEN New.L_OptDateLimit = 0 THEN CONCAT('N', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN CONCAT('Y', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) >= CURDATE()*1 THEN CONCAT('N', New.C_DateLimit) ELSE datelimit END ), 'N2001010120200101'), IFNULL( ( CASE WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN 'RSN' ELSE 'ACT' END ), 'ACT'), (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID), (SELECT C_Name FROM UNIS.cStaff, UNIS.temploye WHERE UNIS.temploye.C_Staff = UNIS.cStaff.C_Code AND UNIS.temploye.L_UID = New.L_ID), New.C_Notice, (SELECT C_Address FROM UNIS.temploye WHERE UNIS.temploye.L_UID = New.L_ID), New.C_UserMessage, (SELECT UNIS.tmealtype.C_Name FROM UNIS.tmealtype, UNIS.temploye WHERE UNIS.temploye.C_Meal = tmealtype.C_Code AND UNIS.temploye.L_UID = New.L_ID), (SELECT UNIS.tmoney.C_Name FROM UNIS.tmoney, UNIS.temploye WHERE UNIS.temploye.C_Money = tmoney.C_Code AND UNIS.temploye.L_UID = New.L_ID), (SELECT C_Email FROM UNIS.temploye WHERE UNIS.temploye.L_UID = New.L_ID), (SELECT C_Phone FROM UNIS.temploye WHERE UNIS.temploye.L_UID = New.L_ID), IFNULL((SELECT C_Work FROM UNIS.temploye WHERE UNIS.temploye.L_UID = New.L_ID), 0), (SELECT UNIS.cpassback.C_Name FROM UNIS.cpassback, UNIS.tuser WHERE UNIS.tuser.C_PassbackStatus = cpassback.C_Code AND UNIS.tuser.L_ID = New.L_ID)) ";
                            mysqli_query($uconn, $query);
                            $query = "CREATE TRIGGER UNIS.u_tuser AFTER UPDATE ON UNIS.tuser FOR EACH ROW UPDATE Access.tuser SET name = New.C_Name, reg_date = IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), datelimit = IFNULL( ( CASE WHEN New.L_OptDateLimit = 0 THEN CONCAT('N', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN CONCAT('Y', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) >= CURDATE()*1 THEN CONCAT('N', New.C_DateLimit) ELSE datelimit END ), 'N2001010120200101'), PassiveType = IFNULL( ( CASE WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN 'RSN' ELSE 'ACT' END ), 'ACT'), dept = (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), company = (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID), remark = (SELECT C_Name FROM UNIS.cStaff, UNIS.temploye WHERE UNIS.temploye.C_Staff = UNIS.cStaff.C_Code AND UNIS.temploye.L_UID = New.L_ID), F1= New.C_Notice, F2 = (SELECT C_Address FROM UNIS.temploye WHERE UNIS.temploye.L_UID = New.L_ID), F3 = New.C_UserMessage, F4 = (SELECT UNIS.tmealtype.C_Name FROM UNIS.tmealtype, UNIS.temploye WHERE UNIS.temploye.C_Meal = tmealtype.C_Code AND UNIS.temploye.L_UID = New.L_ID), F7 = (SELECT C_Phone FROM UNIS.temploye WHERE UNIS.temploye.L_UID = New.L_ID), F6 = (SELECT C_Email FROM UNIS.temploye WHERE UNIS.temploye.L_UID = New.L_ID), F5 = (SELECT UNIS.tmoney.C_Name FROM UNIS.tmoney, UNIS.temploye WHERE UNIS.temploye.C_Money = tmoney.C_Code AND UNIS.temploye.L_UID = New.L_ID), idno = (SELECT UNIS.cpassback.C_Name FROM UNIS.cpassback, UNIS.tuser WHERE UNIS.tuser.C_PassbackStatus = cpassback.C_Code AND UNIS.tuser.L_ID = New.L_ID) WHERE id = New.L_ID ";
                            mysqli_query($uconn, $query);
                            $query = "CREATE TRIGGER UNIS.u_temploye AFTER UPDATE ON UNIS.temploye FOR EACH ROW UPDATE Access.tuser SET dept = (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_UID), company = (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_UID), remark = (SELECT C_Name FROM UNIS.cStaff, UNIS.temploye WHERE UNIS.temploye.C_Staff = UNIS.cStaff.C_Code AND UNIS.temploye.L_UID = New.L_UID), F1= New.C_Notice, F2 = (SELECT C_Address FROM UNIS.temploye WHERE UNIS.temploye.L_UID = New.L_UID), F4 = (SELECT UNIS.tmealtype.C_Name FROM UNIS.tmealtype, UNIS.temploye WHERE UNIS.temploye.C_Meal = tmealtype.C_Code AND UNIS.temploye.L_UID = New.L_UID), F7 = (SELECT C_Phone FROM UNIS.temploye WHERE UNIS.temploye.L_UID = New.L_UID), F6 = (SELECT C_Email FROM UNIS.temploye WHERE UNIS.temploye.L_UID = New.L_UID), F5 = (SELECT UNIS.tmoney.C_Name FROM UNIS.tmoney, UNIS.temploye WHERE UNIS.temploye.C_Money = tmoney.C_Code AND UNIS.temploye.L_UID = New.L_UID), group_id = IFNULL((SELECT C_Work FROM UNIS.temploye WHERE UNIS.temploye.L_UID = New.L_UID), 0) WHERE id = New.L_UID ";
                            mysqli_query($uconn, $query);
                        } else {
                            if (getRegister($mac, 7) == "177") {
                                $query = "CREATE TRIGGER UNIS.i_tuser AFTER INSERT ON UNIS.tuser FOR EACH ROW INSERT INTO Access.tuser (id, name, reg_date, datelimit, PassiveType, dept, company, phone, idno, remark) VALUES (New.L_ID, New.C_Name, IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), IFNULL( ( CASE WHEN New.L_OptDateLimit = 0 THEN CONCAT('N', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN CONCAT('Y', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) >= CURDATE()*1 THEN CONCAT('N', New.C_DateLimit) ELSE datelimit END ), 'N2001010120200101'), IFNULL( ( CASE WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN 'RSN' ELSE 'ACT' END ), 'ACT'), (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID), (SELECT C_Name FROM UNIS.cStaff, UNIS.temploye WHERE UNIS.temploye.C_Staff = UNIS.cStaff.C_Code AND UNIS.temploye.L_UID = New.L_ID), (SELECT C_Remark FROM UNIS.temploye WHERE UNIS.temploye.L_UID = New.L_ID), (SELECT C_Phone FROM UNIS.temploye WHERE UNIS.temploye.L_UID = New.L_ID)) ";
                                mysqli_query($uconn, $query);
                                $query = "CREATE TRIGGER UNIS.u_tuser AFTER UPDATE ON UNIS.tuser FOR EACH ROW UPDATE Access.tuser SET name = New.C_Name, reg_date = IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), datelimit = IFNULL( ( CASE WHEN New.L_OptDateLimit = 0 THEN CONCAT('N', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN CONCAT('Y', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) >= CURDATE()*1 THEN CONCAT('N', New.C_DateLimit) ELSE datelimit END ), 'N2001010120200101'), PassiveType = IFNULL( ( CASE WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN 'RSN' ELSE 'ACT' END ), 'ACT') WHERE id = New.L_ID ";
                                mysqli_query($uconn, $query);
                                $query = "CREATE TRIGGER UNIS.u_temploye AFTER UPDATE ON UNIS.temploye FOR EACH ROW UPDATE Access.tuser SET dept = (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_UID), company = (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_UID), phone = (SELECT C_Name FROM UNIS.cStaff, UNIS.temploye WHERE UNIS.temploye.C_Staff = UNIS.cStaff.C_Code AND UNIS.temploye.L_UID = New.L_UID), remark = (SELECT C_Phone FROM UNIS.temploye WHERE UNIS.temploye.L_UID = New.L_UID), idno = (SELECT C_Remark FROM UNIS.temploye WHERE UNIS.temploye.L_UID = New.L_UID), group_id = IFNULL((SELECT C_Work FROM UNIS.temploye WHERE UNIS.temploye.L_UID = New.L_UID), 0) WHERE id = New.L_UID ";
                                mysqli_query($uconn, $query);
                            } else {
                                if (getRegister($mac, 7) == "168") {
                                    $query = "CREATE TRIGGER UNIS.i_tuser AFTER INSERT ON UNIS.tuser FOR EACH ROW INSERT INTO Access.tuser (id, name, reg_date, datelimit, PassiveType, dept, company, remark, phone, idno) VALUES (New.L_ID, New.C_Name, IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), IFNULL( ( CASE WHEN New.L_OptDateLimit = 0 THEN CONCAT('N', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN CONCAT('Y', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) >= CURDATE()*1 THEN CONCAT('N', New.C_DateLimit) ELSE datelimit END ), 'N2001010120200101'), IFNULL( ( CASE WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN 'RSN' ELSE 'ACT' END ), 'ACT'), (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID), (SELECT C_Name FROM UNIS.cStaff, UNIS.temploye WHERE UNIS.temploye.C_Staff = UNIS.cStaff.C_Code AND UNIS.temploye.L_UID = New.L_ID), (SELECT C_Phone FROM UNIS.temploye WHERE UNIS.temploye.L_UID = New.L_ID), New.C_Unique) ";
                                    mysqli_query($uconn, $query);
                                    $query = "CREATE TRIGGER UNIS.u_tuser AFTER UPDATE ON UNIS.tuser FOR EACH ROW UPDATE Access.tuser SET name = New.C_Name, reg_date = IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), datelimit = IFNULL( ( CASE WHEN New.L_OptDateLimit = 0 THEN CONCAT('N', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN CONCAT('Y', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) >= CURDATE()*1 THEN CONCAT('N', New.C_DateLimit) ELSE datelimit END ), 'N2001010120200101'), PassiveType = IFNULL( ( CASE WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN 'RSN' ELSE 'ACT' END ), 'ACT'), dept = (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), company = (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID), remark = (SELECT C_Name FROM UNIS.cStaff, UNIS.temploye WHERE UNIS.temploye.C_Staff = UNIS.cStaff.C_Code AND UNIS.temploye.L_UID = New.L_ID), phone = (SELECT C_Phone FROM UNIS.temploye WHERE UNIS.temploye.L_UID = New.L_ID), idno = New.C_Unique WHERE id = New.L_ID ";
                                    mysqli_query($uconn, $query);
                                    $query = "CREATE TRIGGER UNIS.u_temploye AFTER UPDATE ON UNIS.temploye FOR EACH ROW UPDATE Access.tuser SET dept = (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_UID), company = (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_UID), remark = (SELECT C_Name FROM UNIS.cStaff, UNIS.temploye WHERE UNIS.temploye.C_Staff = UNIS.cStaff.C_Code AND UNIS.temploye.L_UID = New.L_UID), phone = (SELECT C_Phone FROM UNIS.temploye WHERE UNIS.temploye.L_UID = New.L_UID) WHERE id = New.L_UID ";
                                    mysqli_query($uconn, $query);
                                } else {
                                    $query = "CREATE TRIGGER UNIS.i_tuser AFTER INSERT ON UNIS.tuser FOR EACH ROW INSERT INTO Access.tuser (id, name, reg_date, datelimit, PassiveType, dept, company) VALUES (New.L_ID, New.C_Name, IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), IFNULL( ( CASE WHEN New.L_OptDateLimit = 0 THEN CONCAT('N', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN CONCAT('Y', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) >= CURDATE()*1 THEN CONCAT('N', New.C_DateLimit) ELSE datelimit END ), 'N2001010120200101'), IFNULL( ( CASE WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN 'RSN' ELSE 'ACT' END ), 'ACT'), (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID)) ";
                                    mysqli_query($uconn, $query);
                                    $query = "CREATE TRIGGER UNIS.u_tuser AFTER UPDATE ON UNIS.tuser FOR EACH ROW UPDATE Access.tuser SET name = New.C_Name, reg_date = IFNULL(SUBSTRING(New.C_RegDate, 1, 12), 200101010101), datelimit = IFNULL( ( CASE WHEN New.L_OptDateLimit = 0 THEN CONCAT('N', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN CONCAT('Y', New.C_DateLimit) WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) >= CURDATE()*1 THEN CONCAT('N', New.C_DateLimit) ELSE datelimit END ), 'N2001010120200101'), PassiveType = IFNULL( ( CASE WHEN New.L_OptDateLimit = 1 && SUBSTRING(New.C_DateLimit, 9, 8) < CURDATE()*1 THEN 'RSN' ELSE 'ACT' END ), 'ACT'), dept = (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_ID), company = (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_ID) WHERE id = New.L_ID ";
                                    mysqli_query($uconn, $query);
                                    if (getRegister($mac, 7) == "181") {
                                        $query = "CREATE TRIGGER UNIS.u_temploye AFTER UPDATE ON UNIS.temploye FOR EACH ROW UPDATE Access.tuser SET dept = (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_UID), company = (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_UID), group_id = IFNULL((SELECT C_Work FROM UNIS.temploye WHERE UNIS.temploye.L_UID = New.L_UID), 0), remark = (SELECT C_Name FROM UNIS.cStaff, UNIS.temploye WHERE UNIS.temploye.C_Staff = UNIS.cStaff.C_Code AND UNIS.temploye.L_UID = New.L_ID) WHERE id = New.L_UID ";
                                    } else {
                                        $query = "CREATE TRIGGER UNIS.u_temploye AFTER UPDATE ON UNIS.temploye FOR EACH ROW UPDATE Access.tuser SET dept = (SELECT C_Name FROM UNIS.cPost, UNIS.temploye WHERE UNIS.temploye.C_Post = UNIS.cPost.C_Code AND UNIS.temploye.L_UID = New.L_UID), company = (SELECT C_Name FROM UNIS.cOffice, UNIS.temploye WHERE UNIS.temploye.C_Office = UNIS.cOffice.C_Code AND UNIS.temploye.L_UID = New.L_UID) WHERE id = New.L_UID ";
                                    }
                                    mysqli_query($uconn, $query);
                                }
                            }
                        }
                    }
                }
            }
        }
        $query = "DROP TRIGGER IF EXISTS UNIS.d_tuser ";
        mysqli_query($uconn, $query);
        $query = "CREATE TRIGGER d_tuser AFTER DELETE ON UNIS.tuser FOR EACH ROW DELETE FROM Access.tuser WHERE id = old.L_ID ";
        mysqli_query($uconn, $query);
    }
    if ($x == 0 || $x == 3) {
        $query = "DROP TRIGGER IF EXISTS UNIS.i_tenter ";
        mysqli_query($uconn, $query);
        $query = "CREATE TRIGGER i_tenter AFTER INSERT ON UNIS.tenter FOR EACH ROW INSERT INTO Access.tenter (e_date, e_time, e_id, g_id, e_group, e_mode, e_etc) VALUES (New.C_Date, New.C_Time, New.L_UID, New.L_TID, (SELECT tuser.group_id FROM Access.tuser WHERE id = NEW.L_UID), New.L_Mode, New.L_Etc) ";
        mysqli_query($uconn, $query);
    }
    if ($x == 0 || $x == 4) {
        $query = "DROP TRIGGER IF EXISTS UNIS.i_tgate ";
        mysqli_query($uconn, $query);
        $query = "CREATE TRIGGER i_tgate AFTER INSERT ON UNIS.tterminal FOR EACH ROW INSERT INTO Access.tgate (id, name, reg_date) VALUES (New.L_ID, New.C_Name, SUBSTRING(New.C_RegDate, 0,8)) ";
        mysqli_query($uconn, $query);
        $query = "DROP TRIGGER IF EXISTS UNIS.u_tgate ";
        mysqli_query($uconn, $query);
        $query = "CREATE TRIGGER u_tgate AFTER UPDATE ON UNIS.tterminal FOR EACH ROW UPDATE Access.tgate SET name = New.C_Name WHERE id = New.L_ID ";
        mysqli_query($uconn, $query);
        $query = "DROP TRIGGER IF EXISTS UNIS.d_tgate ";
        mysqli_query($uconn, $query);
        $query = "CREATE TRIGGER d_tgate AFTER DELETE ON UNIS.tterminal FOR EACH ROW DELETE FROM Access.tgate WHERE id = old.L_ID ";
        mysqli_query($uconn, $query);
    }
}

function deleteTrigger($aconn, $x) {
    $uconn = mysqli_connect("127.0.0.1", "unisuser", "unisamho", "UNIS");
    if ($x == 0 || $x == 1) {
        $query = "DROP TRIGGER IF EXISTS Access.i_tuser ";
        mysqli_query($aconn, $query);
        $query = "DROP TRIGGER IF EXISTS Access.d_tuser ";
        mysqli_query($aconn, $query);
    }
    if ($x == 0 || $x == 2) {
        $query = "DROP TRIGGER IF EXISTS UNIS.i_tuser ";
        mysqli_query($uconn, $query);
        $query = "DROP TRIGGER IF EXISTS UNIS.u_tuser ";
        mysqli_query($uconn, $query);
        $query = "DROP TRIGGER IF EXISTS UNIS.d_tuser ";
        mysqli_query($uconn, $query);
        $query = "DROP TRIGGER IF EXISTS UNIS.i_temploye ";
        mysqli_query($uconn, $query);
        $query = "DROP TRIGGER IF EXISTS UNIS.u_temploye ";
        mysqli_query($uconn, $query);
    }
    if ($x == 0 || $x == 3) {
        $query = "DROP TRIGGER IF EXISTS UNIS.i_tenter ";
        mysqli_query($uconn, $query);
    }
    if ($x == 0 || $x == 4) {
        $query = "DROP TRIGGER IF EXISTS UNIS.i_tgate ";
        mysqli_query($uconn, $query);
        $query = "DROP TRIGGER IF EXISTS UNIS.u_tgate ";
        mysqli_query($uconn, $query);
        $query = "DROP TRIGGER IF EXISTS UNIS.d_tgate ";
        mysqli_query($uconn, $query);
    }
}

function displaySuperHeader($prints, $excel, $csv, $current_module, $userlevel, $page, $date, $time) {
    if ($excel != "yes") {
        print "<html><title>" . $page . "</title>";
    }
    if ($prints != "yes") {
        print "<body>";
    } else {
        if ($csv == "yes") { 
            header("Content-type: application/csv; charset=UTF-8");
            header("Content-Disposition: attachment; filename=" . $page . ".csv");
            header("Pragma: no-cache");
            header("Expires: 0");
        } else {
            if ($excel == "yes") {
                header("Content-type: application/x-msdownload");
                header("Content-Disposition: attachment; filename=" . $page . ".xls");
                header("Pragma: no-cache");
                header("Expires: 0");
            } else {
                if ($prints == "yes") {
                    print "<body onLoad='javascript:window.print()'>";
                    print "<center>";
                }
            }
        }
    }
    if ($excel != "yes") {
        print "<center>";
//        displayheader($prints, $date, $time);
    }
    if ($prints != "yes") {
        print'<div class="container-fluid">
                <div class="row">
                        <div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12">
                            <div class="card">
                                    <div class="card-body table-responsive">';
//        displaylinks($current_module, $userlevel);
    }
}

function ada_deprecate($conn, $iconn, $uconn) {
    $query = "SELECT tgroup.id, tgroup.Start, tgroup.Close, tgroup.AccessRestrict, tgroup.RelaxRestrict, tgroup.StartHour, tgroup.CloseHour, tgroup.ASAbsent, tgroup.NightFlag FROM tgroup WHERE id > 2 AND ASAbsent > 0 AND ASAbsent < 365 ORDER BY id";
    $result = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
        $query = "SELECT id, OT1, OT2 FROM tuser WHERE group_id = '" . $cur[0] . "' AND PassiveType = 'ACT' ";
        $user_result = mysqli_query($iconn, $query);
        while ($user_cur = mysqli_fetch_row($user_result)) {
            $count = $cur[7];
            $break_count = 0;
            $date_query = "";
            $_date = 0;
            while (true) {
                $_date = getlastday(inserttoday(), $count);
                $query = "SELECT OTDate FROM OTDate WHERE OTDate = '" . $_date . "'";
                $ot_result = selectdata($conn, $query);
                if ($ot_result[0] != $_date) {
                    if (getRegister($txtMACAddress, 7) == "-1") {
                        if (getday(displaydate($_date)) != $user_cur[2]) {
                            $break_count++;
                            $date_query .= " OR tenter.e_date = '" . $_date . "' ";
                        }
                    } else {
                        if (!(getday(displaydate($_date)) == $user_cur[1] || getday(displaydate($_date)) == $user_cur[2])) {
                            $break_count++;
                            $date_query .= " OR tenter.e_date = '" . $_date . "' ";
                        }
                    }
                }
                $count++;
                if ($break_count == $cur[7]) {
                    break;
                }
            }
            if ($cur[8] == 0) {
                $query = "SELECT COUNT(tenter.e_id) FROM tenter WHERE tenter.e_id = '" . $user_cur[0] . "' AND (tenter.e_date = 0 " . $date_query . ")";
            } else {
                $query = "SELECT COUNT(tenter.e_id) FROM tenter WHERE tenter.e_id = '" . $user_cur[0] . "' AND tenter.e_time > '" . $txtNightShiftMaxOutTime . "00' AND (tenter.e_date = 0 " . $date_query . ")";
            }
            $sub_result = selectdata($conn, $query);
            $query = "SELECT TransactID FROM Transact WHERE TransactDate IN (" . inserttoday() . ", " . getlastday(inserttoday(), 1) . ") AND Transactquery LIKE '%Activated User ID%' AND Transactquery LIKE '%" . $user_cur[0] . "%' ";
            $sub_result_ = selectdata($conn, $query);
            if ($sub_result[0] == "0" && $sub_result_[0] == "") {
                $query = "UPDATE tuser SET tuser.PassiveType = 'ADA', tuser.PassiveRemark = 'Unauthorized Absence DeActivation', tuser.flagdatelimit = tuser.datelimit, tuser.datelimit = 'Y1977043019770430' WHERE tuser.id = " . $user_cur[0];
                if (updateidata($iconn, $query, true)) {
                    mysqli_query($uconn, "UPDATE UNIS.tuser SET L_OptDateLimit = 1, C_DateLimit = CONCAT(SUBSTRING(C_DateLimit, 1, 8), '" . inserttoday() . "') WHERE L_ID = '" . $user_cur[0] . "'");
                    $query = "INSERT INTO ADALog (e_id, DateFrom) VALUES ('" . $user_cur[0] . "', '" . inserttoday() . "')";
                    updateidata($iconn, $query, true);
                }
            }
        }
    }
}

function ada($conn, $iconn, $uconn) {
    $query = "SELECT tgroup.id, tgroup.Start, tgroup.Close, tgroup.AccessRestrict, tgroup.RelaxRestrict, tgroup.StartHour, tgroup.CloseHour, tgroup.ASAbsent, tgroup.NightFlag FROM tgroup WHERE id > 2 AND ASAbsent > 0 AND ASAbsent < 365 ORDER BY id";
    $result = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
        $ada_count = $cur[7];
        $query = "SELECT id, OT1, OT2 FROM tuser WHERE group_id = '" . $cur[0] . "' AND PassiveType = 'ACT' ";
        $user_result = mysqli_query($iconn, $query);
        while ($user_cur = mysqli_fetch_row($user_result)) {
            $ot1_count = getdaycount(getlastday(inserttoday(), $ada_count), inserttoday(), $ada_count, $user_cur[1]);
            $ot2_count = getdaycount(getlastday(inserttoday(), $ada_count), inserttoday(), $ada_count, $user_cur[2]);
            $ada_count = $ada_count + $ot1_count + $ot2_count;
            $query = "SELECT COUNT(ed) FROM tenter WHERE e_date >= '" . getlastday(inserttoday(), $ada_count) . "' AND e_date <= '" . inserttoday() . "' AND e_id = '" . $user_cur[0] . "'";
            $sub_result = selectdata($conn, $query);
            $query = "SELECT TransactID FROM Transact WHERE TransactDate IN (" . inserttoday() . ", " . getlastday(inserttoday(), 1) . ") AND Transactquery LIKE '%Activated User ID%' AND Transactquery LIKE '%" . $user_cur[0] . "%' ";
            $sub_result_ = selectdata($conn, $query);
            if ($sub_result[0] == "0" && $sub_result_[0] == "") {
                $query = "UPDATE tuser SET tuser.PassiveType = 'ADA', tuser.PassiveRemark = 'Unauthorized Absence DeActivation', tuser.flagdatelimit = tuser.datelimit, tuser.datelimit = 'Y1977043019770430' WHERE tuser.id = " . $user_cur[0];
                if (updateidata($iconn, $query, true)) {
                    mysqli_query($uconn, "UPDATE UNIS.tuser SET L_OptDateLimit = 1, C_DateLimit = CONCAT(SUBSTRING(C_DateLimit, 1, 8), '" . inserttoday() . "') WHERE L_ID = '" . $user_cur[0] . "'");
                    $query = "INSERT INTO ADALog (e_id, DateFrom) VALUES ('" . $user_cur[0] . "', '" . inserttoday() . "')";
                    updateidata($iconn, $query, true);
                }
            }
        }
    }
}

function synchCAG($conn, $iconn) {
    $query = "SELECT CAGR FROM OtherSettingMaster";
    $result = selectdata($conn, $query);
    if ($result[0] == "Yes") {
        $query = "SELECT COUNT(DISTINCT(group_id)), CAGID FROM tuser GROUP BY CAGID ORDER BY CAGID, COUNT(group_id) DESC";
        $result = mysqli_query($iconn, $query);
        while ($cur = mysqli_fetch_row($result)) {
            if (1 < $cur[0]) {
                $count = 0;
                $group_id = 0;
                $query = "SELECT COUNT(group_id), group_id FROM tuser WHERE CAGID = '" . $cur[1] . "'";
                $sub_result = mysqli_query($conn, $query);
                while ($sub_cur = mysqli_fetch_row($result)) {
                    if ($count < $sub_cur[0]) {
                        $count = $sub_cur[0];
                        $group_id = $sub_cur[1];
                    }
                }
            }
        }
    }
}
?>