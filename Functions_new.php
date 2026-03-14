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

function openConnection()
{
    global $db_type, $db_ip, $db_name, $db_user, $db_pass;

    $conn = null;
    switch ($db_type) {
        case "1":
            $conn = odbc_connect(decryptString($db_name), decryptString($db_user), decryptString($db_pass));
            break;
        case "2":
            // Add connection logic for db_type 2
            break;
        case "3":
            $conn = openIConnection();
            break;
        case "4":
            $conn = oci_connect($db_user, $db_pass, $db_ip);
            break;
        default:
            throw new Exception("Unsupported DB type");
    }
    return $conn;
}

function openIConnection()
{
    global $db_ip, $db_user, $db_pass;
    $iconn = mysqli_connect(decryptString($db_ip), decryptString($db_user), decryptString($db_pass), "access_geeta");
    mysqli_autocommit($iconn, false);
    return $iconn;
}

function selectData($conn, $query)
{
    global $db_type;

    switch ($db_type) {
        case "1":
            $result = odbc_exec($conn, $query);
            odbc_fetch_into($result, $cur);
            break;
        case "2":
            // Add logic for db_type 2
            break;
        case "3":
            $result = mysqli_query($conn, $query);
            $cur = mysqli_fetch_row($result);
            break;
        case "4":
            $result = oci_parse($conn, $query);
            oci_execute($result);
            $cur = oci_fetch_array($result, OCI_BOTH);
            break;
        default:
            throw new Exception("Unsupported DB type");
    }
    return $cur;
}

function selectDataCol($conn, $query)
{
    global $db_type;
    $array = [];
    $i = 0;

    switch ($db_type) {
        case "1":
            $result = odbc_exec($conn, $query);
            while (odbc_fetch_into($result, $cur)) {
                $array[$i++] = $cur[0];
            }
            break;
        case "2":
            // Add logic for db_type 2
            break;
        case "3":
            $result = mysqli_query($conn, $query) or exit("\nError: " . mysqli_error($conn));
            while ($cur = mysqli_fetch_row($result)) {
                $array[$i++] = $cur[0];
            }
            break;
        case "4":
            $result = oci_parse($conn, $query);
            oci_execute($result);
            while ($cur = oci_fetch_array($result, OCI_BOTH)) {
                $array[$i++] = $cur[0];
            }
            break;
        default:
            throw new Exception("Unsupported DB type");
    }
    return $array;
}

function updateData($conn, $query, $commit)
{
    global $db_type;

    $success = false;
    switch ($db_type) {
        case "1":
            $success = odbc_exec($conn, $query);
            break;
        case "2":
            // Add logic for db_type 2
            break;
        case "3":
            $success = mysqli_query($conn, $query);
            break;
        case "4":
            $result = oci_parse($conn, $query);
            $success = oci_execute($result);
            if ($success && $commit) {
                oci_commit($conn);
            }
            break;
        default:
            throw new Exception("Unsupported DB type");
    }
    return $success;
}

function updateIData($iconn, $query, $commit)
{
    $success = mysqli_query($iconn, $query);
    if ($success && $commit) {
        mysqli_commit($iconn);
    }
    return $success;
}

function updateDataTransact($conn, $query, $commit, $username)
{
    global $db_type;

    $transact_query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . replaceString($transact_query, false) . "')";
    $success = false;

    switch ($db_type) {
        case "1":
            if (odbc_exec($conn, $query)) {
                odbc_exec($conn, $transact_query);
                $success = true;
            }
            break;
        case "2":
            // Add logic for db_type 2
            break;
        case "3":
            if (mysqli_query($conn, $query)) {
                mysqli_query($conn, $transact_query);
                $success = true;
            }
            break;
        case "4":
            $result = oci_parse($conn, $query);
            if (oci_execute($result)) {
                $result = oci_parse($conn, $transact_query);
                oci_execute($result);
                if ($commit) {
                    oci_commit($conn);
                }
                $success = true;
            }
            break;
        default:
            throw new Exception("Unsupported DB type");
    }
    return $success;
}

function login($conn, $username, $password, $lstUserType)
{
    global $session_variable;

    // Check for script modification
    if (filesize("img/virdi.gif") != 1859) {
        $text = "Script Modification Error while Login from IP: " . $_SERVER["REMOTE_ADDR"] . " - Device Name: " . gethostbyaddr($_SERVER["REMOTE_ADDR"]);
        $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . trim($username) . "', '" . $text . "')";
        updatedata($conn, $query, true);
        return 1;
    }

    // Fetch gate count
    $query = "SELECT COUNT(*) FROM tgate";
    $result = selectdata($conn, $query);
    $tcount = $result[0];

    // Fetch global settings
    $query = "SELECT ExitTerminal, Project, CompanyName, CompanyDetail1, CompanyDetail2, NightShiftMaxOutTime, IDColumnName, RosterColumns, RotateShift, Ex3, Ex2, LockDate, MACAddress, EmployeeCodeLength, PhoneColumnName, TCount, ApproveOTIgnoreActual, FlagLimitType, UseShiftRoster, DivColumnName, RemarkColumnName FROM OtherSettingMaster";
    $global_result = selectdata($conn, $query);

    $this_mac = $global_result[12];

    // Fetch global setting flags
    $query = "SELECT F1, F2, F3, F4, F5, F6, F7, F8, F9, F10 FROM OtherSettingMaster";
    $global_result_F = selectdata($conn, $query);

    // Password period check
    if (getRegister($this_mac, 7) == "21" && !in_array(trim($username), ["virdi", "admin"])) {
        $query = "SELECT IFNULL(MAX(Transactdate), 0) FROM Transact WHERE TransactQuery LIKE '%Changed Password%' AND Username = '" . trim($username) . "'";
        $result = selectdata($conn, $query);
        if (90 < getTotalDays(displayDate($result[0]), displayToday())) {
            return 5;
        }
    }

    // Service expiration check
    if (getRegister($this_mac, 2) < insertToday() && getRegister($this_mac, 3) == 1) {
        return 4;
    }

    // Terminal count check
    if ($tcount >= getRegister($this_mac, 1)) {
        if ($lstUserType == "User") {
            $query = "SELECT Userpass, Userlevel, Usermail, Userstatus, RDSSelection, RDSFont, RDSCW, RDSHeaderBreak, RHSSelection, OT1F, OT2F, OTDF, UserType FROM UserMaster WHERE Username = '" . trim($username) . "'";
            $result = selectdata($conn, $query);
            $this_password = decryptString($result[0]);
        } else {
            $query = "SELECT pwd FROM tuser WHERE id = '" . trim($username) . "'";
            $result = selectdata($conn, $query);
            $this_password = strrev($result[0]);
        }

        // Password verification
        if ($this_password === trim($password)) {
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

                $userQuery = "SELECT F8 FROM tuser WHERE name = '" . trim($username) . "'";
                $designUser = selectdata($conn, $userQuery);
                $_SESSION[$session_variable . "design"] = $designUser[0];

                $_SESSION[$session_variable . "usertype"] = $result[12] ?: "Yes";
            }

            // Update last login
            $query = "UPDATE UserMaster SET LastLogin = " . insertToday() . " WHERE Username = '" . trim($username) . "'";
            updatedata($conn, $query, true);

            // Log login activity
            $text = "Logged In from IP: " . $_SERVER["REMOTE_ADDR"] . " - Device Name: " . gethostbyaddr($_SERVER["REMOTE_ADDR"]);
            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . trim($username) . "', '" . $text . "')";
            updatedata($conn, $query, true);

            // Set session variables
            $_SESSION[$session_variable . "ExitTerminal"] = $global_result[0];
            $_SESSION[$session_variable . "Project"] = $global_result[1];
            $_SESSION[$session_variable . "CompanyName"] = getRegister($this_mac, 4);
            $_SESSION[$session_variable . "CompanyDetail1"] = getRegister($this_mac, 5);
            $_SESSION[$session_variable . "CompanyDetail2"] = getRegister($this_mac, 6);
            $_SESSION[$session_variable . "NightShiftMaxOutTime"] = $global_result[5];
            $_SESSION[$session_variable . "IDColumnName"] = $global_result[6];
            $_SESSION[$session_variable . "RotateShift"] = $global_result[8];
            $_SESSION[$session_variable . "Ex3"] = $global_result[9];
            $_SESSION[$session_variable . "Ex2"] = $global_result[10];
            $_SESSION[$session_variable . "MACAddress"] = encryptDecrypt($this_mac);
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

            if (in_array(trim(strtolower($username)), ["meal", "canteen"])) {
                $_SESSION[$session_variable . "VirdiLevel"] = "Meal";
            } else {
                $_SESSION[$session_variable . "VirdiLevel"] = (insertToday() < getRegister($this_mac, 2)) ? getVirdiLevel($this_mac) : "Basic";
            }

            $_SESSION[$session_variable . "UseShiftRoster"] = $global_result[18];
            $_SESSION[$session_variable . "DivColumnName"] = $global_result[19];
            $_SESSION[$session_variable . "RemarkColumnName"] = $global_result[20];

            if ($lstUserType == "User") {
                $user_level_query = " AND (tuser.UserStatus > " . $result[3] . " OR (tuser.UserStatus = " . $result[3] . " AND tuser.id = '" . $username . "')) ";
                $user_level_where_query = " WHERE (tuser.UserStatus > " . $result[3] . " OR (tuser.UserStatus = " . $result[3] . " AND tuser.id = '" . $username . "')) ";
                $query = "SELECT UserDivLockDate.Date FROM UserDivLockDate, UserDiv WHERE UserDivLockDate.Div = UserDiv.Div AND UserDiv.Username = '" . trim($username) . "'";
                $result = selectdata($conn, $query);

                $_SESSION[$session_variable . "LockDate"] = $result ? $result[0] : $global_result[11];

                if ($_SESSION[$session_variable . "VirdiLevel"] == "Classic") {
                    $frt = "";
                    $query = "SELECT Flag, Title FROM FlagTitle";
                    $result = mysqli_query($conn, $query);
                    while ($cur = mysqli_fetch_row($result)) {
                        if (strlen($cur[1]) > 1) {
                            $frt .= "<font color='" . $cur[0] . "'>" . $cur[0] . " = " . $cur[1] . "</font><br>";
                        }
                    }
                    $_SESSION[$session_variable . "FlagReportText"] = $frt;
                }

                $dept_query = "SELECT Dept FROM UserDept WHERE Username = '" . trim($username) . "'";
                $result = mysqli_query($conn, $dept_query);
                $whereQuery = "";
                $query = "";

                for ($i = 0; $sdcur = mysqli_fetch_row($result); $i++) {
                    if ($sdcur[0]) {
                        if ($i == 0) {
                            $whereQuery = " WHERE (tuser.dept = '" . $sdcur[0] . "' ";
                            $query = " AND (tuser.dept = '" . $sdcur[0] . "' ";
                        } else {
                            $whereQuery .= " OR tuser.dept = '" . $sdcur[0] . "' ";
                            $query .= " OR tuser.dept = '" . $sdcur[0] . "' ";
                        }
                    }
                }

                if (strlen($query) > 5) {
                    $query .= " ) ";
                    $whereQuery .= " ) ";
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
                    if ($sdcur[0]) {
                        if ($i == 0) {
                            $whereQuery = " WHERE (tuser.company = '" . $sdcur[0] . "' ";
                            $query = " AND (tuser.company = '" . $sdcur[0] . "' ";
                        } else {
                            $whereQuery .= " OR tuser.company = '" . $sdcur[0] . "' ";
                            $query .= " OR tuser.company = '" . $sdcur[0] . "' ";
                        }
                    }
                }

                if (strlen($query) > 5) {
                    $query .= " ) ";
                    $whereQuery .= " ) ";
                    $_SESSION[$session_variable . "DivAccessQuery"] = $query;
                    $_SESSION[$session_variable . "DivAccessWhereQuery"] = $whereQuery;
                }

                return 0;
            }

            $user_level_query = " AND tuser.id = '" . $username . "' ";
            $_SESSION[$session_variable . "DeptAccessQuery"] = $user_level_query;

            return 0;
        }

        // Invalid login attempt
        $text = "Invalid Login Attempt from IP: " . $_SERVER["REMOTE_ADDR"] . " - Device Name: " . gethostbyaddr($_SERVER["REMOTE_ADDR"]);
        $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . trim($username) . "', '" . $text . "')";
        updatedata($conn, $query, true);

        return 3;
    }

    // Invalid terminal error
    $text = "Invalid Terminal Error while Login from IP: " . $_SERVER["REMOTE_ADDR"] . " - Device Name: " . gethostbyaddr($_SERVER["REMOTE_ADDR"]);
    $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . trim($username) . "', '" . $text . "')";
    updatedata($conn, $query, true);

    return 2;
}

function loginCustom($conn, $table, $column_username, $column_password, $username, $password)
{
    global $session_variable;

    // Escape special characters in the username and password to prevent SQL injection
    $escaped_username = mysqli_real_escape_string($conn, $username);
    $escaped_password = mysqli_real_escape_string($conn, $password);

    // Build the query string
    $query = "SELECT " . $column_password . " FROM " . $table . " WHERE " . $column_username . " = '" . $escaped_username . "'";

    // Execute the query
    $result = selectdata($conn, $query);

    // Check if the result is valid and if the password matches
    if ($result && isset($result[0]) && $result[0] == trim($escaped_password)) {
        // Start the session and set the session variable
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION[$session_variable . "username"] = $escaped_username;
        return true;
    }
    
    return false;
}

function displayHeader($prints, $date, $time)
{
    global $session_variable;
    date_default_timezone_set("Africa/Algiers");
    $target_url = $_SERVER["PHP_SELF"];
    echo "
    <style>
        input {
            background-color: '#F0F0F0';
            font-family: 'Verdana';
            font-size: 10;
        }
        select {
            background-color: '#FFFFFF';
            font-family: 'Verdana';
            font-size: 10;
        }
    </style>
    <link rel='shortcut icon' href='img/favicon.png' type='text/css' />
    <link rel='stylesheet' href='default.css' type='text/css' />
    <script>
        function putRemarkValue(x, y) {
            y.value = x.value;
        }
        function putEmployeeName(x, y) {
            y.value = x.value;
        }
        function check_valid_date(z) {
            if (z.length != 10 || z.substring(6, 10) * 1 < 1900 || z.substring(6, 10) * 1 > 2200) {
                return false;
            } else {
                if (z.substring(0, 2) * 1 < 28 && z.substring(3, 5) * 1 < 13 && z.substring(2, 3) == '/' && z.substring(5, 6) == '/') {
                    return true;
                } else {
                    if ((z.substring(3, 5) * 1 == 4 || z.substring(3, 5) * 1 == 6 || z.substring(3, 5) * 1 == 9 || z.substring(3, 5) * 1 == 11) && z.substring(0, 2) * 1 < 31) {
                        return true;
                    } else if (z.substring(3, 5) * 1 == 2 && z.substring(6, 10) * 1 % 4 == 0 && z.substring(0, 2) * 1 < 30) {
                        return true;
                    } else if (z.substring(3, 5) * 1 == 2 && z.substring(6, 10) * 1 % 4 != 0 && z.substring(0, 2) * 1 < 29) {
                        return true;
                    } else if ((z.substring(3, 5) * 1 == 1 || z.substring(3, 5) * 1 == 3 || z.substring(3, 5) * 1 == 5 || z.substring(3, 5) * 1 == 7 || z.substring(3, 5) * 1 == 8 || z.substring(3, 5) * 1 == 10 || z.substring(3, 5) * 1 == 12) && z.substring(0, 2) * 1 < 32) {
                        return true;
                    } else {
                        return false;
                    }
                }
            }
        }
        function check_valid_time(z) {
            if (z * 1 != z / 1 || z.length != 6 || z.substring(0, 2) * 1 > 23 || z.substring(2, 4) * 1 > 59 || z.substring(4, 6) * 1 > 59) {
                return false;
            } else {
                return true;
            }
        }
        function doSaveAs() {
            var isReady = true;
            if (isReady) {
                document.execCommand('SaveAs');
            } else {
                alert('Feature available only in Internet Explorer 4.0 and later.');
            }
        }
        function checkPrint(a) {
            var x = document.frm1;
    ";

    if ($date && !$time) {
        echo "
            if (check_valid_date(x.txtFrom.value) == false) {
                alert('Invalid From Date. Date Format should be DD/MM/YYYY');
                x.txtFrom.focus();
            } else if (check_valid_date(x.txtTo.value) == false) {
                alert('Invalid To Date. Date Format should be DD/MM/YYYY');
                x.txtTo.focus();
            } else {
        ";
    } elseif ($date && $time) {
        echo "
            if (check_valid_date(x.txtFrom.value) == false) {
                alert('Invalid From Date. Date Format should be DD/MM/YYYY');
                x.txtFrom.focus();
            } else if (check_valid_date(x.txtTo.value) == false) {
                alert('Invalid To Date. Date Format should be DD/MM/YYYY');
                x.txtTo.focus();
            } else if (check_valid_time(x.txtTimeFrom.value) == false) {
                alert('Invalid Time Format. Time Format should be HHMMSS');
                x.txtTimeFrom.focus();
            } else if (check_valid_time(x.txtTimeTo.value) == false) {
                alert('Invalid Time Format. Time Format should be HHMMSS');
                x.txtTimeTo.focus();
            } else {
        ";
    }

    echo "
            if (a == 0) {
                if (confirm('Go Green - Think Twice before you Print this Document. Are you sure want to Print?')) {
                    x.action = '$target_url?prints=yes';
                    x.target = '_blank';
                    x.submit();
                    return true;
                } else {
                    return false;
                }
            } else if (a == 2) {
                if (confirm('Go Green - Think Twice before you Print this Document. Are you sure want to Print?')) {
                    x.action = '$target_url?prints=yes&timecard=yes';
                    x.target = '_blank';
                    x.submit();
                    return true;
                } else {
                    return false;
                }
            } else if (a == 3) {
                x.action = '$target_url?prints=yes&excel=yes&csv=yes';
                x.target = '_blank';
                x.submit();
                return true;
            } else {
                x.action = '$target_url?prints=yes&excel=yes';
                x.target = '_blank';
                x.submit();
                return true;
            }
    ";

    if ($date || $time) {
        echo "}";
    }

    echo "
        }
        function checkSearch() {
            var x = document.frm1;
    ";

    if ($date && !$time) {
        echo "
            if (check_valid_date(x.txtFrom.value) == false) {
                alert('Invalid From Date. Date Format should be DD/MM/YYYY');
                x.txtFrom.focus();
                return false;
            } else if (check_valid_date(x.txtTo.value) == false) {
                alert('Invalid To Date. Date Format should be DD/MM/YYYY');
                x.txtTo.focus();
                return false;
            } else {
        ";
    } elseif ($date && $time) {
        echo "
            if (check_valid_date(x.txtFrom.value) == false) {
                alert('Invalid From Date. Date Format should be DD/MM/YYYY');
                x.txtFrom.focus();
                return false;
            } else if (check_valid_date(x.txtTo.value) == false) {
                alert('Invalid To Date. Date Format should be DD/MM/YYYY');
                x.txtTo.focus();
                return false;
            } else if (check_valid_time(x.txtTimeFrom.value) == false) {
                alert('Invalid Time Format. Time Format should be HHMMSS');
                x.txtTimeFrom.focus();
                return false;
            } else if (check_valid_time(x.txtTimeTo.value) == false) {
                alert('Invalid Time Format. Time Format should be HHMMSS');
                x.txtTimeTo.focus();
                return false;
            } else {
        ";
    }

    echo "
            x.action = '$target_url?prints=no';
            x.target = '_self';
            x.btSearch.disabled = true;
            return true;
    ";

    if ($date || $time) {
        echo "}";
    }

    echo "
        }
    </script>
    ";

    if ($prints == "yes") {
        echo "
        <table width='100%' cellspacing='0' cellpadding='0'>
            <tr>
                <td align='left' bgcolor='#FAFAFA' vAlign='top'>
                    <font face='Verdana' size='2' color='Brown'><b>" . $_SESSION[$session_variable . "CompanyName"] . "</b></font>
                    <br>
                    <font face='Verdana' size='1' color='#339900'>Go Green - Think Twice before you Make Copies</font>
                </td>
                <td align='right' vAlign='top'>
                    <font face='Verdana' size='1' color='Black'><b>Virdi<i><font color='Gray' face='Times New Roman' size='2'>&nbsp;&nbsp;&nbsp;&nbsp;</font></i></b></font>
                    Access Control, Time and Attendance Application
                    <br>
                    <font color='#000000'><b><u>Version: " . $_SESSION[$session_variable . "Ex2"] . "</u></b></font>
                </td>
            </tr>
        </table>
        ";
    } else {
        echo "
        <table width='800' cellspacing='0' cellpadding='0' border='0'>
        ";

        if ($_SESSION[$session_variable . "Report"] != "") {
            echo "
            <tr>
                <td align='left' vAlign='top'>
                    <img src='img/report.png' width='40' height='30' border='0' title='Reports'>
                </td>
            </tr>
            ";
        } else {
            echo "
            <tr>
                <td align='left' vAlign='top'>
                    <img src='img/settings.png' width='40' height='30' border='0' title='Settings'>
                </td>
            </tr>
            ";
        }

        echo "
        </table>
        ";
    }
}

function displayLinks($x, $userlevel)
{
    global $session_variable;
    $userlevel = $_SESSION[$session_variable . "userlevel"];
    $username = $_SESSION[$session_variable . "username"];
    $link = "";
    $url = $_SERVER["REQUEST_URI"];

    print <<<HTML
<link href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css" rel="stylesheet">
<style>
    .ds_box { background-color: #FFF; border: 1px solid #000; position: absolute; z-index: 32767; }
    .ds_tbl { background-color: #FFF; }
    .ds_head { background-color: #333; color: #FFF; font-family: Arial, Helvetica, sans-serif; font-size: 13px; font-weight: bold; text-align: center; letter-spacing: 2px; }
    .ds_subhead { background-color: #CCC; color: #000; font-size: 12px; font-weight: bold; text-align: center; font-family: Arial, Helvetica, sans-serif; width: 32px; }
    .ds_cell { background-color: #EEE; color: #000; font-size: 13px; text-align: center; font-family: Arial, Helvetica, sans-serif; padding: 5px; cursor: pointer; }
    .ds_cell:hover { background-color: #F3F3F3; }
</style>
<script>
    var ds_i_date = new Date();
    ds_c_month = ds_i_date.getMonth() + 1;
    ds_c_year = ds_i_date.getFullYear();
    function ds_getel(id) { return document.getElementById(id); }
    function ds_getleft(el) { var tmp = el.offsetLeft; el = el.offsetParent; while(el) { tmp += el.offsetLeft; el = el.offsetParent; } return tmp; }
    function ds_gettop(el) { var tmp = el.offsetTop; el = el.offsetParent; while(el) {tmp += el.offsetTop; el = el.offsetParent; } return tmp; }
    var ds_oe = ds_getel('ds_calclass');
    var ds_ce = ds_getel('ds_conclass');
    var ds_ob = '';
    function ds_ob_clean() { ds_ob = ''; }
    function ds_ob_flush() {ds_oe.innerHTML = ds_ob; ds_ob_clean(); }
    function ds_echo(t) { ds_ob += t; }
    var ds_element;
    var ds_monthnames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    var ds_daynames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    function ds_template_main_above(t) { return '<table cellpadding=3 cellspacing=1 class=ds_tbl> <tr> <td class=ds_head style=cursor: pointer onclick=ds_py();>&lt;&lt;</td> <td class=ds_head style=cursor: pointer onclick=ds_pm();>&lt;</td> <td class=ds_head style=cursor: pointer onclick=ds_hi(); colspan=3>[Close]</td> <td class=ds_head style=cursor: pointer onclick=ds_nm();>&gt;</td> <td class=ds_head style=cursor: pointer onclick=ds_ny();>&gt;&gt;</td> </tr> <tr> <td colspan=7 class=ds_head>' + t + '</td> </tr> <tr>'; }
    function ds_template_day_row(t) { return '<td class=ds_subhead>' + t + '</td>'; }
    function ds_template_new_week() { return '</tr><tr>'; }
    function ds_template_blank_cell(colspan) { return '<td colspan=' + colspan + '></td>' }
    function ds_template_day(d, m, y) { return '<td class=ds_cell onclick=ds_onclick(' + d + ',' + m + ',' + y + ')>' + d + '</td>' }
    function ds_template_main_below() { return '</tr> </table>'; }
    function ds_draw_calendar(m, y) { ds_ob_clean(); ds_echo (ds_template_main_above(ds_monthnames[m - 1] + ' ' + y)); for (i = 0; i < 7; i ++) { ds_echo (ds_template_day_row(ds_daynames[i])); } var ds_dc_date = new Date(); ds_dc_date.setMonth(m - 1); ds_dc_date.setFullYear(y); ds_dc_date.setDate(1); if (m == 1 || m == 3 || m == 5 || m == 7 || m == 8 || m == 10 || m == 12) { days = 31; } else if (m == 4 || m == 6 || m == 9 || m == 11) { days = 30; } else { days = (y % 4 == 0) ? 29 : 28; } var first_day = ds_dc_date.getDay(); var first_loop = 1; ds_echo (ds_template_new_week()); if (first_day != 0) { ds_echo (ds_template_blank_cell(first_day)); } var j = first_day; for (i = 0; i < days; i ++) { if (j == 0 && !first_loop) { ds_echo (ds_template_new_week()); } ds_echo (ds_template_day(i + 1, m, y)); first_loop = 0; j ++; j %= 7; } ds_echo (ds_template_main_below()); ds_ob_flush(); ds_ce.scrollIntoView(); }
    function ds_sh(t) { ds_element = t; var ds_sh_date = new Date(); ds_c_month = ds_sh_date.getMonth() + 1; ds_c_year = ds_sh_date.getFullYear(); ds_draw_calendar(ds_c_month, ds_c_year); ds_ce.style.display = ''; the_left = ds_getleft(t); the_top = ds_gettop(t) + t.offsetHeight; ds_ce.style.left = the_left + 'px'; ds_ce.style.top = the_top + 'px'; ds_ce.scrollIntoView(); }
    function ds_hi() { ds_ce.style.display = 'none'; }
    function ds_nm() { ds_c_month ++; if (ds_c_month > 12) { ds_c_month = 1; ds_c_year++; } ds_draw_calendar(ds_c_month, ds_c_year); }
    function ds_pm() { ds_c_month = ds_c_month - 1; if (ds_c_month < 1) { ds_c_month = 12; ds_c_year = ds_c_year - 1; } ds_draw_calendar(ds_c_month, ds_c_year); }
    function ds_ny() { ds_c_year++; ds_draw_calendar(ds_c_month, ds_c_year); }
    function ds_py() { ds_c_year = ds_c_year - 1; ds_draw_calendar(ds_c_month, ds_c_year); }
    function ds_format_date(d, m, y) { m2 = '00' + m; m2 = m2.substr(m2.length - 2); d2 = '00' + d; d2 = d2.substr(d2.length - 2); return d2 + '/' + m2 + '/' + y; }
    function ds_onclick(d, m, y) { ds_hi(); if (typeof(ds_element.value) != 'undefined') { ds_element.value = ds_format_date(d, m, y); } else if (typeof(ds_element.innerHTML) != 'undefined') { ds_element.innerHTML = ds_format_date(d, m, y); } else { alert (ds_format_date(d, m, y)); } }
</script>
<table class="ds_box d-none" id="ds_conclass">
    <tr><td id="ds_calclass"></td></tr>
</table>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav">
HTML;

    if (insertToday() < getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 2)) {
        include "Zinks.php";
    } else {
        if ($x != 300) {
            print "<li class='nav-item'>";
            if ($_SESSION[$session_variable . "VirdiLevel"] != "Meal" && (strpos($userlevel, "15V") !== false || strpos($userlevel, "16V") !== false || strpos($userlevel, "17V") !== false || strpos($userlevel, "22V") !== false || strpos($userlevel, "23V") !== false || strpos($userlevel, "24V") !== false || strpos($userlevel, "25V") !== false || strpos($userlevel, "26V") !== false || strpos($userlevel, "29V") !== false || strpos($userlevel, "31V") !== false || strpos($userlevel, "32V") !== false || strpos($userlevel, "33V") !== false || strpos($userlevel, "34V") !== false || strpos($userlevel, "35V") !== false || strpos($userlevel, "37V") !== false || strpos($userlevel, "39V") !== false || strpos($userlevel, "41V") !== false || strpos($userlevel, "44V") !== false || strpos($userlevel, "46V") !== false || strpos($userlevel, "49V") !== false)) {
                $link = ($x != 300) ? ($link . "<a class='nav-link' href='" . (substr($url, strrpos($url, "/") + 1) == "Bindex.php" ? "#" : "Bindex.php") . "'>Bindex</a>") : ($link . "<a class='nav-link active' href='#'>Bindex</a>");
            }
            print $link;
            print "</li>";
        }
    }

    print <<<HTML
            </ul>
        </div>
    </div>
</nav>
HTML;
}