<?php


ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$conn = openConnection();
$iconn = openIConnection();
$counter = 0;
$query = "UPDATE Access.AttendanceMaster SET OT1 = 'Saturday' WHERE LENGTH(OT1) < 3";
updateIData($iconn, $query, true);
$query = "UPDATE Access.AttendanceMaster SET OT2 = 'Sunday' WHERE LENGTH(OT2) < 3";
updateIData($iconn, $query, true);
$query = "UPDATE Access.DayMaster SET OT1 = 'Saturday' WHERE LENGTH(OT1) < 3";
updateIData($iconn, $query, true);
$query = "UPDATE Access.DayMaster SET OT2 = 'Sunday' WHERE LENGTH(OT2) < 3";
updateIData($iconn, $query, true);
$query = "ALTER TABLE AccessArchive.archive_am ADD COLUMN LateInColumn INT DEFAULT 0";
updateIData($iconn, $query, true);
$query = "ALTER TABLE AccessArchive.archive_tenter MODIFY COLUMN e_etc VARCHAR(10) NULL";
updateIData($iconn, $query, true);
$query = "ALTER TABLE AccessArchive.archive_tenter MODIFY COLUMN ed INT(20) NOT NULL AUTO_INCREMENT";
updateIData($iconn, $query, true);
$query = "INSERT IGNORE INTO AccessArchive.archive_tenter (SELECT * FROM Access.tenter WHERE e_date > '20010101' AND e_date < '20190101')";
if (updateIData($iconn, $query, true)) {
    $query = "DELETE FROM Access.tenter WHERE e_date > '20010101' AND e_date < '20190101'";
    if (updateIData($iconn, $query, true)) {
        $tr_query = "INSERT INTO Access.Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", 'admin', 'Archived Logs - Raw [01/01/2001 - 31/12/2018]')";
        updateIData($iconn, $tr_query, true);
    }
}
$query = "INSERT IGNORE INTO AccessArchive.archive_am (SELECT * FROM Access.AttendanceMaster WHERE ADate > '20010101' AND ADate < '20190101')";
if (updateIData($iconn, $query, true)) {
    $query = "DELETE FROM Access.AttendanceMaster WHERE ADate > '20010101' AND ADate < '20190101'";
    if (updateIData($iconn, $query, true)) {
        $query = "INSERT IGNORE INTO AccessArchive.archive_dm (SELECT * FROM Access.DayMaster WHERE TDate > '20010101' AND TDate < '20190101')";
        if (updateIData($iconn, $query, true)) {
            $query = "DELETE FROM Access.DayMaster WHERE TDate > '20010101' AND TDate < '20190101'";
            if (updateIData($iconn, $query, true)) {
                $query = "INSERT IGNORE INTO AccessArchive.archive_trans (SELECT * FROM Access.Transact WHERE TransactDate > '20010101' AND TransactDate < '20190101' AND TransactQuery NOT LIKE '%Archived Logs%')";
                if (updateIData($iconn, $query, true)) {
                    $query = "DELETE FROM Access.Transact WHERE TransactDate > '20010101' AND TransactDate < '20190101' AND TransactQuery NOT LIKE '%Archived Logs%'";
                    if (updateIData($iconn, $query, true)) {
                        $query = "INSERT IGNORE INTO AccessArchive.FlagDayRotation (SELECT * FROM Access.FlagDayRotation WHERE e_date > '20010101' AND e_date < '20190101')";
                        if (updateIData($iconn, $query, true)) {
                            $query = "DELETE FROM Access.FlagDayRotation WHERE e_date > '20010101' AND e_date < '20190101')";
                            if (updateIData($iconn, $query, true)) {
                                $query = "INSERT IGNORE INTO AccessArchive.ShiftRoster (SELECT * FROM Access.ShiftRoster WHERE e_date > '20010101' AND e_date < '20190101')";
                                if (updateIData($iconn, $query, true)) {
                                    $query = "DELETE FROM Access.ShiftRoster WHERE e_date > '20010101' AND e_date < '20190101'";
                                    if (updateIData($iconn, $query, true)) {
                                        $tr_query = "INSERT INTO Access.Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", 'admin', 'Archived Logs - Processed [01/01/2001 - 31/12/2018]')";
                                        if (updateIData($iconn, $tr_query, false)) {
                                            $tr_query = "INSERT INTO Access.Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", 'admin', 'Archived Logs - User Transactions [01/01/2001 - 31/12/2018]')";
                                            if (updateIData($iconn, $tr_query, false)) {
                                                $tr_query = "INSERT INTO Access.Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", 'admin', 'Archived Logs - Flag Roster, Pre Flags [01/01/2001 - 31/12/2018]')";
                                                if (updateIData($iconn, $tr_query, false)) {
                                                    $tr_query = "INSERT INTO Access.Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", 'admin', 'Archived Logs - Shift Roster [01/01/2001 - 31/12/2018]')";
                                                    if (updateIData($iconn, $tr_query, true)) {
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
mysqli_close($conn);
mysqli_close($iconn);

?>