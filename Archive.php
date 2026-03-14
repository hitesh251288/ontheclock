<?php


ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "19";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$username = $_SESSION[$session_variable . "username"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$userstatus = $_SESSION[$session_variable . "userstatus"];
if ($username == "") {
    header("Location: " . $config["REDIRECT"]);
}
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=Archive.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$iconn = openIConnection();
$act = $_GET["act"];
if ($act == "") {
    $act = $_POST["act"];
}
$message = $_GET["message"];
if ($message == "") {
    $message = "Database Archive/ Retrieve - Date Range should NOT exceed 31 Days";
}
$txtFrom = $_POST["txtFrom"];
if ($txtFrom == "") {
    $txtFrom = $_GET["txtFrom"];
}
if ($txtFrom == "") {
    $txtFrom = "01" . substr(displayDate(getLastDay(insertToday(), 365)), 2, 8);
}
$txtTo = $_POST["txtTo"];
if ($txtTo == "") {
    $txtTo = $_GET["txtTo"];
}
if ($txtTo == "") {
    $txtTo = "01" . substr(displayDate(getLastDay(insertToday(), 335)), 2, 8);
}
$counter = 0;
if ($act == "archiveR" || $act == "archiveP") {
    if (31 < getTotalDays($txtFrom, $txtTo)) {
        header("Location: Archive.php?txtFrom=" . $txtFrom . "&txtTo=" . $txtTo . "&message=Selected Period should NOT be more than 30 Days.");
    } else {
        $commit_flag = true;
        $query = "UPDATE Access.AttendanceMaster SET OT1 = 'Saturday' WHERE LENGTH(OT1) < 3";
        updateIData($iconn, $query, true);
        $query = "UPDATE Access.AttendanceMaster SET OT2 = 'Sunday' WHERE LENGTH(OT2) < 3";
        updateIData($iconn, $query, true);
        $query = "UPDATE Access.DayMaster SET OT1 = 'Saturday' WHERE LENGTH(OT1) < 3";
        updateIData($iconn, $query, true);
        $query = "UPDATE Access.DayMaster SET OT2 = 'Sunday' WHERE LENGTH(OT2) < 3";
        updateIData($iconn, $query, true);
        $query = "INSERT IGNORE INTO AccessArchive.archive_tenter (SELECT * FROM Access.tenter WHERE e_date >= '" . insertDate($txtFrom) . "' AND e_date <= '" . insertDate($txtTo) . "')";
        if (updateIData($iconn, $query, true)) {
            $query = "DELETE FROM Access.tenter WHERE e_date >= '" . insertDate($txtFrom) . "' AND e_date <= '" . insertDate($txtTo) . "'";
            if (updateIData($iconn, $query, true)) {
                $counter++;
            }
        }
        if (0 < $counter) {
            $tr_query = "INSERT INTO Access.Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Archived Logs - Raw [" . addComma($counter) . "] [" . $txtFrom . " - " . $txtTo . "]')";
            updateIData($iconn, $tr_query, true);
        }
        if ($act == "archiveP") {
            $query = "INSERT IGNORE INTO AccessArchive.archive_am (SELECT * FROM Access.AttendanceMaster WHERE ADate >= '" . insertDate($txtFrom) . "' AND ADate <= '" . insertDate($txtTo) . "')";
            if (updateIData($iconn, $query, true)) {
                $query = "DELETE FROM Access.AttendanceMaster WHERE ADate >= '" . insertDate($txtFrom) . "' AND ADate <= '" . insertDate($txtTo) . "'";
                if (updateIData($iconn, $query, true)) {
                    $query = "INSERT IGNORE INTO AccessArchive.archive_dm (SELECT * FROM Access.DayMaster WHERE TDate >= '" . insertDate($txtFrom) . "' AND TDate <= '" . insertDate($txtTo) . "')";
                    if (updateIData($iconn, $query, true)) {
                        $query = "DELETE FROM Access.DayMaster WHERE TDate >= '" . insertDate($txtFrom) . "' AND TDate <= '" . insertDate($txtTo) . "'";
                        if (updateIData($iconn, $query, true)) {
                            $query = "INSERT IGNORE INTO AccessArchive.archive_trans (SELECT * FROM Access.Transact WHERE TransactDate >= '" . insertDate($txtFrom) . "' AND TransactDate <= '" . insertDate($txtTo) . "' AND TransactQuery NOT LIKE '%Archived Logs%')";
                            if (updateIData($iconn, $query, true)) {
                                $query = "DELETE FROM Access.Transact WHERE TransactDate >= '" . insertDate($txtFrom) . "' AND TransactDate <= '" . insertDate($txtTo) . "' AND TransactQuery NOT LIKE '%Archived Logs%'";
                                if (updateIData($iconn, $query, true)) {
                                    $query = "INSERT IGNORE INTO AccessArchive.FlagDayRotation (SELECT * FROM Access.FlagDayRotation WHERE e_date >= '" . insertDate($txtFrom) . "' AND e_date <= '" . insertDate($txtTo) . "')";
                                    if (updateIData($iconn, $query, true)) {
                                        $query = "DELETE FROM Access.FlagDayRotation WHERE e_date >= '" . insertDate($txtFrom) . "' AND e_date <= '" . insertDate($txtTo) . "')";
                                        if (updateIData($iconn, $query, true)) {
                                            $query = "INSERT IGNORE INTO AccessArchive.ShiftRoster (SELECT * FROM Access.ShiftRoster WHERE e_date >= '" . insertDate($txtFrom) . "' AND e_date <= '" . insertDate($txtTo) . "')";
                                            if (updateIData($iconn, $query, true)) {
                                                $query = "DELETE FROM Access.ShiftRoster WHERE e_date >= '" . insertDate($txtFrom) . "' AND e_date <= '" . insertDate($txtTo) . "'";
                                                if (updateIData($iconn, $query, true)) {
                                                    $counter++;
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
            if (1 < $counter) {
                $tr_query = "INSERT INTO Access.Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Archived Logs - Processed [" . $txtFrom . " - " . $txtTo . "]')";
                if (updateIData($iconn, $tr_query, false)) {
                    $tr_query = "INSERT INTO Access.Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Archived Logs - User Transactions [" . $txtFrom . " - " . $txtTo . "]')";
                    if (updateIData($iconn, $tr_query, false)) {
                        $tr_query = "INSERT INTO Access.Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Archived Logs - Flag Roster, Pre Flags [" . $txtFrom . " - " . $txtTo . "]')";
                        if (updateIData($iconn, $tr_query, false)) {
                            $tr_query = "INSERT INTO Access.Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Archived Logs - Shift Roster [" . $txtFrom . " - " . $txtTo . "]')";
                            if (updateIData($iconn, $tr_query, true)) {
                                $counter = 0;
                                header("Location: Archive.php?txtFrom=" . $txtFrom . "&txtTo=" . $txtTo . "&message=Action Completed Successfully.");
                            } else {
                                header("Location: Archive.php?txtFrom=" . $txtFrom . "&txtTo=" . $txtTo . "&message=Action COULD NOT be Completed.");
                            }
                        }
                    }
                }
            } else {
                if ($counter == 0) {
                    header("Location: Archive.php?txtFrom=" . $txtFrom . "&txtTo=" . $txtTo . "&message=Action COULD NOT be Completed.");
                }
            }
        }
    }
} else {
    if ($act == "retrieveR" || $act == "retrieveP") {
        if (31 < getTotalDays($txtFrom, $txtTo)) {
            header("Location: Archive.php?txtFrom=" . $txtFrom . "&txtTo=" . $txtTo . "&message=Selected Period should NOT be more than 30 Days.");
        } else {
            $commit_flag = true;
            $query = "INSERT IGNORE INTO Access.tenter (SELECT * FROM AccessArchive.archive_tenter WHERE e_date >= '" . insertDate($txtFrom) . "' AND e_date <= '" . insertDate($txtTo) . "')";
            if (updateIData($iconn, $query, true)) {
                $counter++;
            }
            if (0 < $counter) {
                $tr_query = "INSERT INTO Access.Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Retrieved Logs - Raw [" . addComma($counter) . "] [" . $txtFrom . " - " . $txtTo . "]')";
                updateIData($iconn, $tr_query, true);
            }
            if ($act == "retrieveP") {
                $query = "INSERT IGNORE INTO Access.AttendanceMaster (SELECT * FROM AccessArchive.archive_am WHERE ADate >= '" . insertDate($txtFrom) . "' AND ADate <= '" . insertDate($txtTo) . "')";
                if (updateIData($iconn, $query, true)) {
                    $query = "INSERT IGNORE INTO Access.DayMaster (SELECT * FROM AccessArchive.archive_dm WHERE TDate >= '" . insertDate($txtFrom) . "' AND TDate <= '" . insertDate($txtTo) . "')";
                    if (updateIData($iconn, $query, true)) {
                        $query = "INSERT IGNORE INTO Access.Transact (SELECT * FROM AccessArchive.archive_trans WHERE TransactDate >= '" . insertDate($txtFrom) . "' AND TransactDate <= '" . insertDate($txtTo) . "' AND TransactQuery NOT LIKE '%Archived Logs%')";
                        if (updateIData($iconn, $query, true)) {
                            $query = "INSERT IGNORE INTO Access.FlagDayRotation (SELECT * FROM AccessArchive.FlagDayRotation WHERE e_date >= '" . insertDate($txtFrom) . "' AND e_date <= '" . insertDate($txtTo) . "')";
                            if (updateIData($iconn, $query, true)) {
                                $query = "INSERT IGNORE INTO Access.ShiftRoster (SELECT * FROM AccessArchive.ShiftRoster WHERE e_date >= '" . insertDate($txtFrom) . "' AND e_date <= '" . insertDate($txtTo) . "')";
                                if (updateIData($iconn, $query, true)) {
                                    $counter++;
                                }
                            }
                        }
                    }
                }
                if (1 < $counter) {
                    $tr_query = "INSERT INTO Access.Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Retrieved Logs - Processed [" . addComma($counter) . "] [" . $txtFrom . " - " . $txtTo . "]')";
                    if (updateIData($iconn, $tr_query, false)) {
                        $tr_query = "INSERT INTO Access.Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Retrieved Logs - User Transactions [" . addComma($counter) . "] [" . $txtFrom . " - " . $txtTo . "]')";
                        if (updateIData($iconn, $tr_query, false)) {
                            $tr_query = "INSERT INTO Access.Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Retrieved Logs - Flag Roster/ Pre Flags [" . addComma($counter) . "] [" . $txtFrom . " - " . $txtTo . "]')";
                            if (updateIData($iconn, $tr_query, false)) {
                                $tr_query = "INSERT INTO Access.Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Retrieved Logs - Shift Roster [" . addComma($counter) . "] [" . $txtFrom . " - " . $txtTo . "]')";
                                if (updateIData($iconn, $tr_query, true)) {
                                    $counter = 0;
                                    header("Location: Archive.php?txtFrom=" . $txtFrom . "&txtTo=" . $txtTo . "&message=Action Completed Successfully.");
                                } else {
                                    header("Location: Archive.php?txtFrom=" . $txtFrom . "&txtTo=" . $txtTo . "&message=Action COULD NOT be Completed.");
                                }
                            }
                        }
                    }
                } else {
                    if ($counter == 0) {
                        header("Location: Archive.php?txtFrom=" . $txtFrom . "&txtTo=" . $txtTo . "&message=Action COULD NOT be Completed.");
                    }
                }
            } else {
                if (0 < $counter) {
                    header("Location: Archive.php?txtFrom=" . $txtFrom . "&txtTo=" . $txtTo . "&message=Action Completed Successfully.");
                }
            }
        }
    } else {
        if ($act == "archiveB" || $act == "archiveD") {
            if (31 < getTotalDays($txtFrom, $txtTo)) {
                header("Location: Archive.php?txtFrom=" . $txtFrom . "&txtTo=" . $txtTo . "&message=Selected Period should NOT be more than 30 Days.");
            } else {
                $success = false;
                $query = "SELECT EX1, MACAddress, CompanyName FROM OtherSettingMaster";
                $result = selectData($conn, $query);
                $file_name = "";
                if (strpos($result[2], " ") !== false) {
                    $file_name = strToLower(substr($result[2], 0, strpos($result[2], " "))) . "-TA-Archive-" . insertToday() . "-" . getNow();
                } else {
                    $file_name = strToLower($result[2]) . "-TA-Archive-" . insertToday() . "-" . getNow();
                }
                echo exec("\"C:\\Program Files\\MySQL\\MySQL Server 5.0\\bin\\mysqldump\" --user=" . decryptString($db_user) . " --password=" . decryptString($db_pass) . " AccessArchive>" . $file_name . ".sql");
                if (filesize($file_name . ".sql") != 0) {
                    rename($file_name . ".sql", $result[0] . "\\" . $file_name . ".sql");
                    $success = true;
                } else {
                    echo exec("\"C:\\Program Files\\MySQL\\MySQL Server 5.1\\bin\\mysqldump\" --user=" . decryptString($db_user) . " --password=" . decryptString($db_pass) . " AccessArchive>" . $file_name . ".sql");
                    if (filesize($file_name . ".sql") != 0) {
                        rename($file_name . ".sql", $result[0] . "\\" . $file_name . ".sql");
                        $success = true;
                    } else {
                        echo exec("\"E:\\Program Files\\MySQL\\MySQL Server 5.1\\bin\\mysqldump\" --user=" . decryptString($db_user) . " --password=" . decryptString($db_pass) . " AccessArchive>" . $file_name . ".sql");
                        if (filesize($file_name . ".sql") != 0) {
                            rename($file_name . ".sql", $result[0] . "\\" . $file_name . ".sql");
                            $success = true;
                        } else {
                            unlink($file_name . ".sql");
                        }
                    }
                }
                if ($success) {
                    $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('Archive Backup', " . insertToday() . ", '" . getNow() . "')";
                    if (updateIData($iconn, $query, true)) {
                        $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Archive Backup')";
                        updateIData($iconn, $query, true);
                    }
                    if ($act == "archiveD") {
                        $query = "DELETE FROM AccessArchive.archive_tenter";
                        if (updateIData($iconn, $query, true)) {
                            $query = "DELETE FROM AccessArchive.archive_am";
                            if (updateIData($iconn, $query, true)) {
                                $query = "DELETE FROM AccessArchive.archive_dm";
                                if (updateIData($iconn, $query, true)) {
                                    $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('Archive Delete', " . insertToday() . ", '" . getNow() . "')";
                                    if (updateIData($iconn, $query, true)) {
                                        $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Archive Delete')";
                                        updateIData($iconn, $query, true);
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
if ($prints != "yes") {
    include 'header.php';
?>
<div class="page-breadcrumb">
                    <div class="row">
                        <div class="col-12 d-flex no-block align-items-center">
                            <h4 class="page-title">Database Archive/ Retrieve</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Database Archive/ Retrieve
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

<?php
}
//echo "\r\n<html><head><title>Database Archive/ Retrieve</title></head>\r\n<script>\r\n\t\r\n</script>\r\n<body><center><div align='center'>\r\n\t";
//displayHeader($prints,'','');
print "<center>";
//displayLinks($current_module, $userlevel);
print'<div class="container-fluid">
                <div class="row">
                        <div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12">
                            <div class="card">
                                    <div class="card-body table-responsive">';
//print "</center>";
print "<table width='800'>";
print "<tr><td width='100%' colspan='2' align='center'><font face='Verdana' size='1' color='#6481BD'><b>" . $message . "</b></font></td></tr>";
print "<tr><td width='100%' colspan='2' align='center'><font face='Verdana' size='5' color='#6481BD'><b>&nbsp;</b></font></td></tr>";
print "<tr>";
print "<td vAlign='top' width='50%' bgcolor='#F0F0F0'><font face='Verdana' size='2'><form name ='frm1' method='post' action='Archive.php'><input type='hidden' name='act'>";
displayDate(getMax($conn, "AccessArchive.archive_tenter", "e_date") - 1);
print "Raw Log - Last Archive Date: <b>" . displayDate(getMax($conn, "AccessArchive.archive_tenter", "e_date") - 1) . "</b>";
displayDate(getMax($conn, "AccessArchive.archive_am", "ADate") - 1);
print "<br>Processed Log - Last Archive Date: <b>" . displayDate(getMax($conn, "AccessArchive.archive_am", "ADate") - 1) . "</b>";
if (strpos($userlevel, $current_module . "D") !== false) {
    print "<br><br><table width='100%'>";
    print "<tr>";
    print "<td width='50%'><input type='button' class='btn btn-primary' name='btArchiveB' value='Backup Archive' onClick='ar(4)'></td>";
    print "<td width='50%'><input type='button' class='btn btn-primary' name='btArchiveD' value=' Delete Archive' onClick='ar(5)'></td>";
    print "</tr>";
    print "</table>";
}
print "</font></td>";
print "<td vAlign='top' width='50%' bgcolor='#E0E0E0'><font face='Verdana' size='2'>";
print "<b>Archive/ Retrieve</b>";
print "<br><table width='100%'>";
print "<tr>";
displayTextbox("txtFrom", "Date From (DD/MM/YYYY): ", $txtFrom, $prints, 12, "50%", "50%");
print "</tr>";
print "<tr>";
displayTextbox("txtTo", "Date To (DD/MM/YYYY): ", $txtTo, $prints, 12, "50%", "50%");
print "</tr>";
print "</table>";
if (strpos($userlevel, $current_module . "D") !== false) {
    print "<br><table width='100%'>";
    print "<tr>";
    print "<td width='50%'><input type='button' name='btArchiveR' class='btn btn-primary' value='Archive Raw Logs' onClick='ar(0)'></td>";
    print "<td width='50%'><input type='button' name='btArchiveP' class='btn btn-primary' value='Archive All Logs' onClick='ar(2)'></td>";
    print "</tr>";
    print "<tr>";
    print "<td width='50%'><input type='button' class='btn btn-primary' name='btRetrieveR' onClick='ar(1)' value='Retrieve Raw Logs'></td>";
    print "<td width='50%'><input type='button' class='btn btn-primary' name='btRetrieveP' onClick='ar(3)' value='Retrieve All Logs'></td>";
    print "</tr>";
    print "</table>";
}
print "</font></td>";
print "</tr>";
print "<tr>";
print "<td vAlign='top' width='50%' bgcolor='#E0E0E0'><font face='Verdana' size='2'>";
print "<b>Archive Log History</b>";
print "<br><table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'><tr><td><font face='Verdana' size='1'><b>Date</b></font></td> <td><font face='Verdana' size='1'><b>Time</b></font></td> <td><font face='Verdana' size='1'><b>By</b></font></td> <td><font face='Verdana' size='1'><b>Remarks</b></font></td></tr>";
if ($username == "virdi") {
    $query = "SELECT TransactDate, TransactTime, Username, Transactquery FROM Transact WHERE Transactquery LIKE '%Archived Logs%'";
} else {
    $query = "SELECT TransactDate, TransactTime, Username, Transactquery FROM Transact WHERE Transactquery LIKE '%Archived Logs%' AND Username NOT LIKE 'virdi'";
}
$result = mysqli_query($conn, $query);
while ($cur = mysqli_fetch_row($result)) {
    displayDate($cur[0]);
    displayTime($cur[1]);
    print "<tr><td><font face='Verdana' size='1'>" . displayDate($cur[0]) . "</font></td> <td><font face='Verdana' size='1'>" . displayTime($cur[1]) . "</font></td> <td><font face='Verdana' size='1'>" . $cur[2] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[3] . "</font></td></tr>";
}
print "</table>";
print "</font></td>";
print "<td vAlign='top' width='50%' bgcolor='#F0F0F0'><font face='Verdana' size='2'>";
print "<b>Retrieve Log History</b>";
print "<br><table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'><tr><td><font face='Verdana' size='1'><b>Date</b></font></td> <td><font face='Verdana' size='1'><b>Time</b></font></td> <td><font face='Verdana' size='1'><b>By</b></font></td> <td><font face='Verdana' size='1'><b>Remarks</b></font></td></tr>";
if ($username == "virdi") {
    $query = "SELECT TransactDate, TransactTime, Username, Transactquery FROM Transact WHERE Transactquery LIKE '%Retrieved Logs%'";
} else {
    $query = "SELECT TransactDate, TransactTime, Username, Transactquery FROM Transact WHERE Transactquery LIKE '%Retrieved Logs%' AND Username NOT LIKE 'virdi'";
}
$result = mysqli_query($conn, $query);
while ($cur = mysqli_fetch_row($result)) {
    displayDate($cur[0]);
    displayTime($cur[1]);
    print "<tr><td><font face='Verdana' size='1'>" . displayDate($cur[0]) . "</font></td> <td><font face='Verdana' size='1'>" . displayTime($cur[1]) . "</font></td> <td><font face='Verdana' size='1'>" . $cur[2] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[3] . "</font></td></tr>";
}
print "</table>";
print "</font></td></form>";
print "</tr>";
print "</table>";
print "</div></div></div></div></div>";
echo "\t\r\n<script>\r\n\tfunction ar(a){\r\n\t\tvar x = document.frm1;\r\n\t\tif (check_valid_date(x.txtFrom.value) == false){\r\n\t\t\talert('Invalid From Date. Date Format should be DD/MM/YYYY');\r\n\t\t\tx.txtFrom.focus();\r\n\t\t}else if (check_valid_date(x.txtTo.value) == false){\r\n\t\t\talert('Invalid To Date. Date Format should be DD/MM/YYYY');\r\n\t\t\tx.txtTo.focus();\r\n\t\t}else{\r\n\t\t\tif (confirm('Archive/ Retrieve/ Backup/ Delete')){\r\n\t\t\t\tif (a == 0){\r\n\t\t\t\t\tx.act.value = 'archiveR';\r\n\t\t\t\t}else if (a == 1){\r\n\t\t\t\t\tx.act.value = 'retrieveR';\r\n\t\t\t\t}else if (a == 2){\r\n\t\t\t\t\tx.act.value = 'archiveP';\r\n\t\t\t\t}else if (a == 3){\r\n\t\t\t\t\tx.act.value = 'retrieveP';\r\n\t\t\t\t}else if (a == 4){\r\n\t\t\t\t\tx.act.value = 'archiveB';\r\n\t\t\t\t}else if (a == 5){\r\n\t\t\t\t\tx.act.value = 'archiveD';\r\n\t\t\t\t}\r\n\t\t\t\tx.btArchiveR.disabled = true;\r\n\t\t\t\tx.btArchiveP.disabled = true;\r\n\t\t\t\tx.btRetrieveR.disabled = true;\r\n\t\t\t\tx.btRetrieveP.disabled = true;\r\n\t\t\t\tx.btArchiveB.disabled = true;\r\n\t\t\t\tx.btArchiveD.disabled = true;\r\n\t\t\t\tx.submit();\r\n\t\t\t}\r\n\t\t}\r\n\t}\r\n</script>\r\n</div></center>";
include 'footer.php';

?>