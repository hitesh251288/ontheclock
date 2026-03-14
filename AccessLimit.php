<?php


error_reporting(E_ALL);
ob_start("ob_gzhandler");
date_default_timezone_set("Africa/Algiers");
include "Functions.php";
$conn = openConnection();
$query = "SELECT LockDate, MACAddress, UseShiftRoster, NightShiftMaxOutTime, SRDay FROM OtherSettingMaster";
$main_result = selectData($conn, $query);
$txtLockDate = $main_result[0];
$txtMACAddress = $main_result[1];
$lstUseShiftRoster = $main_result[2];
$txtNightShiftMaxOutTime = $main_result[3];
$txtSRDay = $main_result[4];
if (checkMAC($conn) == false) {
    print "Un Registered Application. Process Terminated.";
    exit;
}
$iconn = openIConnection();
$flag = 1;
$darray = getDate();
$day = $darray["weekday"];
$query = "Select Day FROM OTDay WHERE OT = 1 AND Day = '" . $day . "'";
$result = selectData($conn, $query);
if ($day == $result[0]) {
    $flag = 0;
} else {
    $query = "SELECT OTDate FROM OTDate WHERE OTDate = '" . insertToday() . "'";
    $result = selectData($conn, $query);
    if ($result[0] == insertToday()) {
        $flag = 0;
    } else {
        $query = "SELECT SRDay FROM ShiftChangeMaster WHERE SRDay = '" . $day . "' ";
        $result = selectData($conn, $query);
        if ($result[0] == $day) {
            $flag = 2;
        }
    }
}
if ($flag < 2) {
    $query = "SELECT tgroup.id, tgroup.Start, tgroup.Close, tgroup.AccessRestrict, tgroup.RelaxRestrict, tgroup.StartHour, tgroup.CloseHour, tgroup.ASAbsent, tgroup.NightFlag FROM tgroup WHERE id > 1 ORDER BY id";
    $result = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
        $sanitation = false;
        if ($cur[4] == "SAN") {
            $sub_query = "SELECT OTDate from SanitationDate WHERE OTDate = '" . insertToday() . "'";
            $sub_result = selectData($conn, $query);
            if (is_numeric($sub_result)) {
                $sanitation = true;
            }
        }
        if ($flag == 1 && $cur[3] == "Yes" && $sanitation == false) {
            $from = $cur[5];
            $to = $cur[6];
            $r_time = addZero($from, 4) . "" . addZero($to, 4);
            $query = "UPDATE tgroup SET timelimit = '" . $r_time . "' WHERE id = '" . $cur[0] . "'";
            updateIData($iconn, $query, true);
        } else {
            if ($flag == 0 && $cur[3] == "Yes" && $cur[4] == "Yes" || $cur[3] == "No" || $sanitation == true) {
                $query = "UPDATE tgroup SET timelimit = '00002359' WHERE id = '" . $cur[0] . "'";
                updateIData($iconn, $query, true);
            }
        }
        if (0 < $cur[7] && $cur[7] < 365) {
            $query = "SELECT id, OT1, OT2 FROM tuser WHERE group_id = '" . $cur[0] . "' AND PassiveType = 'ACT' ";
            $user_result = mysqli_query($conn, $query);
            while ($user_cur = mysqli_fetch_row($user_result)) {
                $count = 1;
                $break_count = 0;
                $date_query = "";
                $_date = 0;
                while (true) {
                    $_date = getLastDay(insertToday(), $count);
                    $query = "SELECT OTDate FROM OTDate WHERE OTDate = '" . $_date . "'";
                    $ot_result = selectData($conn, $query);
                    if ($ot_result[0] != $_date) {
                        if (getRegister($txtMACAddress, 7) == "-1") {
                            if (getDay(displayDate($_date)) != $user_cur[2]) {
                                $break_count++;
                                $date_query .= " OR tenter.e_date = '" . $_date . "' ";
                            }
                        } else {
                            if (!(getDay(displayDate($_date)) == $user_cur[1] || getDay(displayDate($_date)) == $user_cur[2])) {
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
                $sub_result = selectData($conn, $query);
                $query = "SELECT TransactID FROM Transact WHERE TransactDate IN (" . insertToday() . ", " . getLastDay(insertToday(), 1) . ") AND Transactquery LIKE '%Activated User ID%' AND Transactquery LIKE '%" . $user_cur[0] . "%' ";
                $sub_result_ = selectData($conn, $query);
                if ($sub_result[0] == "0" && $sub_result_[0] == "") {
                    $query = "UPDATE tuser SET tuser.PassiveType = 'ADA', tuser.PassiveRemark = 'Unauthorized Absence DeActivation', tuser.flagdatelimit = tuser.datelimit, tuser.datelimit = 'Y1977043019770430' WHERE tuser.id = " . $user_cur[0];
                    if (updateIData($iconn, $query, true)) {
                        $query = "INSERT INTO ADALog (e_id, DateFrom) VALUES ('" . $user_cur[0] . "', '" . insertToday() . "')";
                        updateIData($iconn, $query, true);
                    }
                }
            }
        }
    }
} else {
    $query = "UPDATE tgroup, ShiftChangeMaster SET tgroup.timelimit = '00002359' WHERE tgroup.id = ShiftChangeMaster.id AND ShiftChangeMaster.SRDay = '" . $day . "' ";
    updateIData($iconn, $query, true);
}
$query = "UPDATE tuser SET tuser.datelimit = CONCAT('N', SUBSTRING(tuser.flagdatelimit, 2, 8), SUBSTRING(tuser.flagdatelimit, 10, 8)), tuser.PassiveType = 'ACT' WHERE tuser.PassiveType = 'FDA' ";
updateIData($iconn, $query, true);
$query = "SELECT * FROM AccessFlag";
$result = selectData($conn, $query);
if (is_array($result) && count($result) > 1) {
for ($i = 1; $i < count($result); $i++) {
    if ($result[$i] == "No") {
        $flag = "Violet";
        if ($i == 2) {
            $flag = "Indigo";
        } else {
            if ($i == 3) {
                $flag = "Blue";
            } else {
                if ($i == 4) {
                    $flag = "Green";
                } else {
                    if ($i == 5) {
                        $flag = "Yellow";
                    } else {
                        if ($i == 6) {
                            $flag = "Orange";
                        } else {
                            if ($i == 7) {
                                $flag = "Red";
                            } else {
                                if ($i == 8) {
                                    $flag = "Gray";
                                } else {
                                    if ($i == 9) {
                                        $flag = "Brown";
                                    } else {
                                        if ($i == 10) {
                                            $flag = "Purple";
                                        } else {
                                            if ($i == 13) {
                                                $flag = "Magenta";
                                            } else {
                                                if ($i == 14) {
                                                    $flag = "Teal";
                                                } else {
                                                    if ($i == 15) {
                                                        $flag = "Aqua";
                                                    } else {
                                                        if ($i == 16) {
                                                            $flag = "Safron";
                                                        } else {
                                                            if ($i == 17) {
                                                                $flag = "Amber";
                                                            } else {
                                                                if ($i == 18) {
                                                                    $flag = "Gold";
                                                                } else {
                                                                    if ($i == 19) {
                                                                        $flag = "Vermilion";
                                                                    } else {
                                                                        if ($i == 20) {
                                                                            $flag = "Silver";
                                                                        } else {
                                                                            if ($i == 21) {
                                                                                $flag = "Maroon";
                                                                            } else {
                                                                                if ($i == 22) {
                                                                                    $flag = "Pink";
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
        $query = "UPDATE tuser, FlagDayRotation, tgroup SET tuser.flagdatelimit = tuser.datelimit, tuser.datelimit = 'Y1977043019770430', tuser.PassiveType = 'FDA' WHERE tuser.id = FlagDayRotation.e_id AND FlagDayRotation.e_date = '" . insertToday() . "' AND FlagDayRotation.Flag = '" . $flag . "' AND tuser.group_id = tgroup.id AND tgroup.NightFlag = 0 AND tuser.PassiveType = 'ACT' ";
        updateIData($iconn, $query, true);
        if ($txtNightShiftMaxOutTime < getNow()) {
            $query = "UPDATE tuser, FlagDayRotation, tgroup SET tuser.flagdatelimit = tuser.datelimit, tuser.datelimit = 'Y1977043019770430', tuser.PassiveType = 'FDA' WHERE tuser.id = FlagDayRotation.e_id AND FlagDayRotation.e_date = '" . insertToday() . "' AND FlagDayRotation.Flag = '" . $flag . "' AND tuser.group_id = tgroup.id AND tgroup.NightFlag = 1 AND tuser.PassiveType = 'ACT' ";
            updateIData($iconn, $query, true);
        }
    }
}
} else {
    // No data found OR query failed
    // You can show a message or just skip
}
if ($lstUseShiftRoster == "Yes") {
    $query = "UPDATE tuser, ShiftRoster SET tuser.group_id = ShiftRoster.e_group WHERE tuser.id = ShiftRoster.e_id AND ShiftRoster.e_date = " . insertToday();
    updateIData($iconn, $query, true);
    $query = "UPDATE tenter, ShiftRoster, tgroup SET tenter.e_group = ShiftRoster.e_group WHERE tenter.e_id = ShiftRoster.e_id AND tenter.e_date = ShiftRoster.e_date AND tenter.p_flag = 0 AND ShiftRoster.e_group = tgroup.id AND tgroup.NightFlag = 0";
    updateIData($iconn, $query, true);
    $query = "UPDATE tenter, ShiftRoster, tgroup SET tenter.e_group = ShiftRoster.e_group WHERE tenter.e_id = ShiftRoster.e_id AND tenter.e_date = ShiftRoster.e_date AND tenter.p_flag = 0 AND ShiftRoster.e_group = tgroup.id AND tgroup.NightFlag = 1 AND tenter.e_time > '" . $txtNightShiftMaxOutTime . "00'";
    updateIData($iconn, $query, true);
    $query = "SELECT ShiftRoster.e_id, ShiftRoster.e_date, ShiftRoster.e_group FROM ShiftRoster, tgroup WHERE ShiftRoster.e_group = tgroup.id AND tgroup.NightFlag = 1 AND e_date <= '" . insertToday() . "'";
    $result = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
        $nextDate = getNextDay($cur[1], 1);
        $dayNight = false;
        $query = "SELECT tenter.e_time FROM ShiftRoster, tgroup, tenter WHERE tenter.e_id = ShiftRoster.e_id AND tenter.e_date = ShiftRoster.e_date AND tenter.p_flag = 0 AND ShiftRoster.e_group = tgroup.id AND tgroup.NightFlag = 0 AND ShiftRoster.e_id = '" . $cur[0] . "' AND ShiftRoster.e_date = '" . $nextDate . "'";
        $sub_result = mysqli_query($conn, $query);
        if ($sub_cur = mysqli_fetch_row($sub_result)) {
            $dayNight = true;
            $query = "UPDATE tenter SET tenter.e_group = '" . $cur[2] . "' WHERE tenter.e_id = '" . $cur[0] . "' AND tenter.e_date = '" . $nextDate . "' AND tenter.p_flag = 0 AND tenter.e_time < '120000' ";
            updateIData($iconn, $query, true);
            break;
        }
        if ($dayNight == false) {
            $query = "UPDATE tenter SET tenter.e_group = '" . $cur[2] . "' WHERE tenter.e_id = '" . $cur[0] . "' AND tenter.e_date = '" . $nextDate . "' AND tenter.p_flag = 0 AND tenter.e_time < '" . $txtNightShiftMaxOutTime . "00'";
            updateIData($iconn, $query, true);
        }
    }
}
echo "\n\rScript Executed Successfully. Stopping ACServer";
exec("net stop ACServer");
echo "\n\rStarting ACServer in 20 Seconds";
sleep(20);
exec("net start ACServer");
$query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('Access Limit', " . insertToday() . ", '" . getNow() . "')";
updateIData($iconn, $query, true);

?>