<?php


ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "16";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=AssignProjectChild.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$iconn = openIConnection();
$act = $_GET["act"];
if ($act == "") {
    $act = $_POST["act"];
}
$txtID = $_GET["txtID"];
if ($txtID == "") {
    $txtID = $_POST["txtID"];
}
$prints = $_GET["prints"];
$message = $_GET["message"];
if ($message == "") {
    $message = "Project Assignment";
}
$lstShift = $_POST["lstShift"];
$lstDepartment = $_POST["lstDepartment"];
$lstDivision = $_POST["lstDivision"];
if ($lstShift == "") {
    $lstShift = $_GET["lstShift"];
}
if ($lstDepartment == "") {
    $lstDepartment = $_GET["lstDepartment"];
}
if ($lstDivision == "") {
    $lstDivision = $_GET["lstDivision"];
}
$txtCounter = $_POST["txtCounter"];
$lstShiftType = $_POST["lstShiftType"];
if ($lstShift != "") {
    $query = "SELECT NightFlag FROM tgroup WHERE id = " . $lstShift;
    $result = selectData($conn, $query);
    $lstShiftType = $result[0];
}
print "<html><title>Project Assignment</title>";
print "<style>input{background-color:'#F0F0F0';font-family:'Verdana';font-size:'10';}</style>";
print "<style>select{background-color:'#FFFFFF';font-family:'Verdana';font-size:'10';}</style>";
if ($act == "dailyAssignment") {
    $txtID = $txtID / 1024;
    $query = "SELECT ProjectLogID FROM ProjectLog WHERE DayMasterID = " . $txtID;
    $result = selectData($conn, $query);
    if (0 < $result[0]) {
        header("Location: ReportProject.php?act=searchRecord&txtFrom=01/01/1970&txtTo=01/01/9970&prints=yes&excel=yes&txtDayMasterID=" . $txtID);
    } else {
        $query = "SELECT e_id, TDate, Start, BreakOut, BreakIn, Close FROM DayMaster WHERE DayMasterID = " . $txtID;
        $result = selectData($conn, $query);
        print "<form name='frm2' method='post' action='AssignProjectChild.php'><input type='hidden' name='act' value='addRecord'> <input type='hidden' name='shift' value='daily'> <input type='hidden' name='txtEID' value='" . $result[0] . "'> <input type='hidden' name='txtDate' value='" . $result[1] . "'> <input type='hidden' name='lstShift' value='" . $lstShift . "'> <input type='hidden' name='lstShiftType' value='" . $lstShiftType . "'> <input type='hidden' name='txtBreakFrom' value='" . $result[3] . "'> <input type='hidden' name='txtBreakTo' value='" . $result[4] . "'> <input type='hidden' name='txtID' value='" . $txtID . "'> <table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
        print "<tr><td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>Start Time</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>Project</font></td> </tr>";
        for ($i = 0; $i < 11; $i++) {
            print "<tr>";
            if ($i != 10) {
                print "<td><font face='Verdana' size='1'>Project " . ($i + 1) . "</font></td>";
            }
            if ($i == 0) {
                displayVirdiTime($result[2]);
                print "<td><font face='Verdana' size='1'>" . displayVirdiTime($result[2]) . "</font><input type='hidden' name='txtStart' value='" . $result[2] . "'></td>";
            } else {
                if ($i == 10) {
                    print "<td><font face='Verdana' size='1'>End Time</font></td>";
                    displayVirdiTime($result[5]);
                    print "<td><font face='Verdana' size='1'>" . displayVirdiTime($result[5]) . "</font><input type='hidden' name='txtProject" . $i . "' value='" . $result[5] . "'></td>";
                    print "<td><font face='Verdana' size='1'>&nbsp;</font></td>";
                    print "<td><font face='Verdana' size='1'>&nbsp;</font></td>";
                } else {
                    print "<td><input size='5' name='txtProject" . $i . "'><font face='Verdana' size='1'>(HHMM)</font></td>";
                }
            }
            if ($i != 10) {
                $query = "SELECT ProjectID, Name FROM ProjectMaster ORDER BY Name";
                displayList("lstProject" . $i, "&nbsp;", "", $prints, $conn, $query, "", "2%", "20%");
            }
            print "</tr>";
        }
        print "</table>";
        if ((strpos($userlevel, $current_module . "E") !== false || strpos($userlevel, $current_module . "A") !== false) && $_SESSION[$session_variable . "LockDate"] < $result[1]) {
            print "<br><input type='button' onClick='javascript:checkSubmit(0)' value='Submit Record'>";
        }
        print "<input type='hidden' name='txtCounter'></form>";
    }
} else {
    if ($act == "weeklyAssignment") {
        $txtID = $txtID / 1024;
        $query = "SELECT ProjectLogID FROM ProjectLog WHERE WeekMasterID = " . $txtID;
        $result = selectData($conn, $query);
        if (0 < $result[0]) {
            header("Location: ReportProject.php?act=searchRecord&txtFrom=01/01/1970&txtTo=01/01/9970&prints=yes&excel=yes&txtWeekMasterID=" . $txtID);
        } else {
            $query = "SELECT e_id, LogDate, Start, Close, WeekNo FROM WeekMaster WHERE WeekMasterID = " . $txtID;
            $result = selectData($conn, $query);
            print "<form name='frm2' method='post' action='AssignProjectChild.php'><input type='hidden' name='act' value='addRecord'> <input type='hidden' name='shift' value='weekly'> <input type='hidden' name='txtEID' value='" . $result[0] . "'> <input type='hidden' name='txtDate' value='" . $result[1] . "'> <input type='hidden' name='txtID' value='" . $txtID . "'> <input type='hidden' name='lstShift' value='" . $lstShift . "'> <input type='hidden' name='lstShiftType' value='" . $lstShiftType . "'> <table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
            print "<tr><td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>Start Time</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>Project</font></td> </tr>";
            for ($i = 0; $i < 11; $i++) {
                print "<tr>";
                if ($i != 10) {
                    print "<td><font face='Verdana' size='1'>Project " . ($i + 1) . "</font></td>";
                }
                if ($i == 0) {
                    displayVirdiTime($result[2]);
                    print "<td><font face='Verdana' size='1'>" . displayVirdiTime($result[2]) . "</font><input type='hidden' name='txtStart' value='" . $result[2] . "'></td>";
                } else {
                    if ($i == 10) {
                        print "<td><font face='Verdana' size='1'>End Time</font></td>";
                        displayVirdiTime($result[3]);
                        print "<td><font face='Verdana' size='1'>" . displayVirdiTime($result[3]) . "</font><input type='hidden' name='txtProject" . $i . "' value='" . $result[5] . "'></td>";
                        print "<td><font face='Verdana' size='1'>&nbsp;</font></td>";
                        print "<td><font face='Verdana' size='1'>&nbsp;</font></td>";
                    } else {
                        print "<td><input size='5' name='txtProject" . $i . "'><font face='Verdana' size='1'>(HHMM)</font></td>";
                    }
                }
                if ($i != 10) {
                    $query = "SELECT ProjectID, Name FROM ProjectMaster ORDER BY Name";
                    displayList("lstProject" . $i, "&nbsp;", "", $prints, $conn, $query, "", "2%", "20%");
                }
                print "</tr>";
            }
            print "</table>";
            print "<br><input type='button' onClick='javascript:checkSubmit(0)' value='Submit Record'>";
            print "</table><br><input type='button' onClick='javascript:checkSubmit(0)' value='Submit Record'><input type='hidden' name='txtCounter'></form>";
        }
    } else {
        if ($act == "addRecord") {
            $shift = $_POST["shift"];
            $e_id = $_POST["txtEID"];
            $tfrom = $_POST["txtStart"];
            $lastProject = $_POST["lstProject0"];
            $id = $_POST["txtID"];
            $txtDate = $_POST["txtDate"];
            $breakFrom = $_POST["txtBreakFrom"];
            $breakTo = $_POST["txtBreakTo"];
            $start = 0;
            $end = 0;
            $breakFlag = false;
            $nightBreak = false;
            $nb = 0;
            if ($lstShiftType == 1 && $shift == "daily") {
                if ($breakFrom * 1 != $breakTo) {
                    if ($breakFrom * 1 < $breakTo) {
                        $nb = mktime(substr($breakTo, 0, 2), substr($breakTo, 2, 2), substr($breakTo, 4, 2), substr($txtDate, 4, 2), substr($txtDate, 6, 2), substr($txtDate, 0, 4)) - mktime(substr($breakFrom, 0, 2), substr($breakFrom, 2, 2), substr($breakFrom, 4, 2), substr($txtDate, 4, 2), substr($txtDate, 6, 2), substr($txtDate, 0, 4));
                    } else {
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
                        $next = mktime(substr($breakFrom, 0, 2), substr($breakFrom, 2, 2), 0, $m, $d, $a["year"]);
                        $nb = mktime(substr($breakTo, 0, 2), substr($breakTo, 2, 2), substr($breakTo, 4, 2), substr($next, 4, 2), substr($next, 6, 2), substr($next, 0, 4)) - mktime(substr($breakFrom, 0, 2), substr($breakFrom, 2, 2), substr($breakFrom, 4, 2), substr($txtDate, 4, 2), substr($txtDate, 6, 2), substr($txtDate, 0, 4));
                    }
                }
            }
            if ($shift == "daily") {
                for ($i = 1; $i < 11; $i++) {
                    if ($_POST["txtProject" . $i] != "") {
                        if (substr($breakFrom, 0, 4) <= $_POST["txtProject" . $i] && $breakFlag == false && $lstShiftType == 0) {
                            $breakFlag = true;
                            $start = mktime(substr($tfrom, 0, 2), substr($tfrom, 2, 2), 1, substr($txtDate, 4, 2), substr($txtDate, 6, 2), substr($txtDate, 0, 4));
                            if ($breakFrom < $tfrom) {
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
                                $end = mktime(substr($breakFrom, 0, 2), substr($breakFrom, 2, 2), 0, $m, $d, $a["year"]);
                            } else {
                                $end = mktime(substr($breakFrom, 0, 2), substr($breakFrom, 2, 2), 0, substr($txtDate, 4, 2), substr($txtDate, 6, 2), substr($txtDate, 0, 4));
                            }
                            $query = "INSERT INTO ProjectLog (DayMasterID, ProjectID, e_id, e_date, tfrom, tto, twork) VALUES (" . $id . ", " . $lastProject . ", " . $e_id . ", " . $txtDate . ", ";
                            if (strlen($tfrom) == 4) {
                                $query = $query . " '" . $tfrom . "01', ";
                            } else {
                                $query = $query . " '" . $tfrom . "', ";
                            }
                            if (strlen($breakFrom) == 4) {
                                $query = $query . " '" . $breakFrom . "00', ";
                            } else {
                                $query = $query . " '" . $breakFrom . "', ";
                            }
                            $query = $query . ($end - $start) . ")";
                            updateIData($iconn, $query, true);
                            $text = "Added Project Log for Employee: " . $e_id . " - Project: " . $lastProject . " - Date: " . displayDate($txtDate) . " - From: " . $tfrom . " - To: " . $breakFrom;
                            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                            updateIData($iconn, $query, true);
                            $start = mktime(substr($breakTo, 0, 2), substr($breakTo, 2, 2), 1, substr($txtDate, 4, 2), substr($txtDate, 6, 2), substr($txtDate, 0, 4));
                            if ($_POST["txtProject" . $i] < $breakTo) {
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
                                $end = mktime(substr($_POST["txtProject" . $i], 0, 2), substr($_POST["txtProject" . $i], 2, 2), 0, $m, $d, $a["year"]);
                            } else {
                                $end = mktime(substr($_POST["txtProject" . $i], 0, 2), substr($_POST["txtProject" . $i], 2, 2), 0, substr($txtDate, 4, 2), substr($txtDate, 6, 2), substr($txtDate, 0, 4));
                            }
                            $query = "INSERT INTO ProjectLog (DayMasterID, ProjectID, e_id, e_date, tfrom, tto, twork) VALUES (" . $id . ", " . $lastProject . ", " . $e_id . ", " . $txtDate . ", ";
                            if (strlen($breakTo) == 4) {
                                $query = $query . " '" . $breakTo . "01', ";
                            } else {
                                $query = $query . " '" . $breakTo . "', ";
                            }
                            if (strlen($_POST["txtProject" . $i]) == 4) {
                                $query = $query . " '" . $_POST["txtProject" . $i] . "00', ";
                            } else {
                                $query = $query . " '" . $_POST["txtProject" . $i] . "', ";
                            }
                            $query = $query . ($end - $start) . ")";
                            updateIData($iconn, $query, true);
                            $text = "Added Project Log for Employee: " . $e_id . " - Project: " . $lastProject . " - Date: " . displayDate($txtDate) . " - From: " . $breakTo . " - To: " . $_POST["txtProject" . $i];
                            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                            updateIData($iconn, $query, true);
                        } else {
                            $start = mktime(substr($tfrom, 0, 2), substr($tfrom, 2, 2), 1, substr($txtDate, 4, 2), substr($txtDate, 6, 2), substr($txtDate, 0, 4));
                            if ($_POST["txtProject" . $i] < $tfrom) {
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
                                $end = mktime(substr($_POST["txtProject" . $i], 0, 2), substr($_POST["txtProject" . $i], 2, 2), 0, $m, $d, $a["year"]);
                            } else {
                                $end = mktime(substr($_POST["txtProject" . $i], 0, 2), substr($_POST["txtProject" . $i], 2, 2), 0, substr($txtDate, 4, 2), substr($txtDate, 6, 2), substr($txtDate, 0, 4));
                            }
                            $query = "INSERT INTO ProjectLog (DayMasterID, ProjectID, e_id, e_date, tfrom, tto, twork) VALUES (" . $id . ", " . $lastProject . ", " . $e_id . ", " . $txtDate . ", ";
                            if (strlen($tfrom) == 4) {
                                $query = $query . " '" . $tfrom . "01', ";
                            } else {
                                $query = $query . " '" . $tfrom . "', ";
                            }
                            if (strlen($_POST["txtProject" . $i]) == 4) {
                                $query = $query . " '" . $_POST["txtProject" . $i] . "00', ";
                            } else {
                                $query = $query . " '" . $_POST["txtProject" . $i] . "', ";
                            }
                            if ($lstShiftType == 1) {
                                if ($nb < $end - $start && $nightBreak == false) {
                                    $query = $query . ($end - $start - $nb) . ")";
                                    $nightBreak = true;
                                } else {
                                    $query = $query . ($end - $start) . ")";
                                }
                            } else {
                                $query = $query . ($end - $start) . ")";
                            }
                            updateIData($iconn, $query, true);
                            $text = "Added Project Log for Employee: " . $e_id . " - Project: " . $lastProject . " - Date: " . displayDate($txtDate) . " - From: " . $tfrom . " - To: " . $_POST["txtProject" . $i];
                            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                            updateIData($iconn, $query, true);
                        }
                        $tfrom = $_POST["txtProject" . $i];
                        $lastProject = $_POST["lstProject" . $i];
                    }
                }
            } else {
                for ($i = 1; $i < 11; $i++) {
                    if ($_POST["txtProject" . $i] != "") {
                        $start = mktime(substr($tfrom, 0, 2), substr($tfrom, 2, 2), 1, substr($txtDate, 4, 2), substr($txtDate, 6, 2), substr($txtDate, 0, 4));
                        if ($_POST["txtProject" . $i] < $tfrom) {
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
                            $end = mktime(substr($_POST["txtProject" . $i], 0, 2), substr($_POST["txtProject" . $i], 2, 2), 0, $m, $d, $a["year"]);
                        } else {
                            $end = mktime(substr($_POST["txtProject" . $i], 0, 2), substr($_POST["txtProject" . $i], 2, 2), 0, substr($txtDate, 4, 2), substr($txtDate, 6, 2), substr($txtDate, 0, 4));
                        }
                        $query = "" . "INSERT INTO ProjectLog (WeekMasterID, ProjectID, e_id, e_date, tfrom, tto, " . $twork . ") VALUES (" . $id . ", " . $lastProject . ", " . $e_id . ", " . $txtDate . ", ";
                        if (strlen($tfrom) == 4) {
                            $query = $query . " '" . $tfrom . "01', ";
                        } else {
                            $query = $query . " '" . $tfrom . "', ";
                        }
                        if (strlen($_POST["txtProject" . $i]) == 4) {
                            $query = $query . " '" . $_POST["txtProject" . $i] . "00', ";
                        } else {
                            $query = $query . " '" . $_POST["txtProject" . $i] . "', ";
                        }
                        $query = $query . ($end - $start) . ")";
                        updateIData($iconn, $query, true);
                        $text = "Added Project Log for Employee: " . $e_id . " - Project: " . $lastProject . " - Date: " . displayDate($txtDate) . " - From: " . $tfrom . " - To: " . $_POST["txtProject" . $i];
                        $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                        updateIData($iconn, $query, true);
                        $tfrom = $_POST["txtProject" . $i];
                        $lastProject = $_POST["lstProject" . $i];
                    }
                }
            }
            print "<body onLoad='window.close()'></body>";
        }
    }
}
print "<script>";
print "function closeWindow(){" . "var loc = 'AlterTime.php?act=searchRecord&lstShift=" . $lstShift . "&lstDepartment=" . $lstDepartment . "&lstDivision=" . $lstDivision . "';" . "if (!opener){" . "win.creator.location = loc;" . "}else{" . "opener.location = loc;" . "}" . "window.close();" . "}" . "</script>";
echo "\r\n<script>\r\nfunction checkSubmit(a){\r\n\tvar count = 0;\r\n\tx = document.frm2;\r\n\tif (a == 1){\r\n\t\tx = document.frm3;\r\n\t}\r\n\t\r\n\tif (x.txtProject1.value != \"\" && checkTime(x, x.txtProject1.value) == false){\r\n\t\talert('Invalid Time or Time falls during Break Period. Time format should ONLY be HHMM (24 HR Clock format)');\r\n\t\tx.txtProject1.focus();\r\n\t}else if (x.txtProject2.value != \"\" && checkTime(x, x.txtProject2.value) == false){\r\n\t\talert('Invalid Time or Time falls during Break Period. Time format should ONLY be HHMM (24 HR Clock format)');\r\n\t\tx.txtProject2.focus();\r\n\t}else if (x.txtProject3.value != \"\" && checkTime(x, x.txtProject3.value) == false){\r\n\t\talert('Invalid Time or Time falls during Break Period. Time format should ONLY be HHMM (24 HR Clock format)');\r\n\t\tx.txtProject3.focus();\r\n\t}else if (x.txtProject4.value != \"\" && checkTime(x, x.txtProject4.value) == false){\r\n\t\talert('Invalid Time or Time falls during Break Period. Time format should ONLY be HHMM (24 HR Clock format)');\r\n\t\tx.txtProject4.focus();\r\n\t}else if (x.txtProject5.value != \"\" && checkTime(x, x.txtProject5.value) == false){\r\n\t\talert('Invalid Time or Time falls during Break Period. Time format should ONLY be HHMM (24 HR Clock format)');\r\n\t\tx.txtProject5.focus();\r\n\t}else if (x.txtProject6.value != \"\" && checkTime(x, x.txtProject6.value) == false){\r\n\t\talert('Invalid Time or Time falls during Break Period. Time format should ONLY be HHMM (24 HR Clock format)');\r\n\t\tx.txtProject6.focus();\r\n\t}else if (x.txtProject7.value != \"\" && checkTime(x, x.txtProject7.value) == false){\r\n\t\talert('Invalid Time or Time falls during Break Period. Time format should ONLY be HHMM (24 HR Clock format)');\r\n\t\tx.txtProject7.focus();\r\n\t}else if (x.txtProject8.value != \"\" && checkTime(x, x.txtProject8.value) == false){\r\n\t\talert('Invalid Time or Time falls during Break Period. Time format should ONLY be HHMM (24 HR Clock format)');\r\n\t\tx.txtProject8.focus();\r\n\t}else if (x.txtProject9.value != \"\" && checkTime(x, x.txtProject9.value) == false){\r\n\t\talert('Invalid Time or Time falls during Break Period. Time format should ONLY be HHMM (24 HR Clock format)');\r\n\t\tx.txtProject9.focus();\r\n\t}else if (x.lstProject0.value == \"\"){\r\n\t\talert('Please select the First Project');\r\n\t\tx.lstProject0.focus();\r\n\t}else if (x.txtProject1.value != \"\" && x.lstProject1.value == \"\"){\r\n\t\talert('Please select the Second Project');\r\n\t\tx.lstProject1.focus();\r\n\t}else if (x.txtProject2.value != \"\" && x.lstProject2.value == \"\"){\r\n\t\talert('Please select the Third Project');\r\n\t\tx.lstProject2.focus();\r\n\t}else if (x.txtProject3.value != \"\" && x.lstProject3.value == \"\"){\r\n\t\talert('Please select the Fourth Project');\r\n\t\tx.lstProject3.focus();\r\n\t}else if (x.txtProject4.value != \"\" && x.lstProject4.value == \"\"){\r\n\t\talert('Please select the Fifth Project');\r\n\t\tx.lstProject4.focus();\r\n\t}else if (x.txtProject5.value != \"\" && x.lstProject5.value == \"\"){\r\n\t\talert('Please select the Sixth Project');\r\n\t\tx.lstProject5.focus();\r\n\t}else if (x.txtProject6.value != \"\" && x.lstProject6.value == \"\"){\r\n\t\talert('Please select the Seventh Project');\r\n\t\tx.lstProject6.focus();\r\n\t}else if (x.txtProject7.value != \"\" && x.lstProject7.value == \"\"){\r\n\t\talert('Please select the Eighth Project');\r\n\t\tx.lstProject7.focus();\r\n\t}else if (x.txtProject8.value != \"\" && x.lstProject8.value == \"\"){\r\n\t\talert('Please select the Ninth Project');\r\n\t\tx.lstProject8.focus();\r\n\t}else if (x.txtProject9.value != \"\" && x.lstProject9.value == \"\"){\r\n\t\talert('Please select the Tenth Project');\r\n\t\tx.lstProject9.focus();\r\n\t}else if (x.lstProject1.value != \"\" && (x.lstProject0.value == x.lstProject1.value)){\r\n\t\talert('CANNOT select Two consecutive SAME Projects');\r\n\t\tx.lstProject1.focus();\r\n\t}else if (x.lstProject2.value != \"\" && (x.lstProject1.value == x.lstProject2.value)){\r\n\t\talert('CANNOT select Two consecutive SAME Projects');\r\n\t\tx.lstProject2.focus();\r\n\t}else if (x.lstProject3.value != \"\" && (x.lstProject2.value == x.lstProject3.value)){\r\n\t\talert('CANNOT select Two consecutive SAME Projects');\r\n\t\tx.lstProject3.focus();\r\n\t}else if (x.lstProject4.value != \"\" && (x.lstProject3.value == x.lstProject4.value)){\r\n\t\talert('CANNOT select Two consecutive SAME Projects');\r\n\t\tx.lstProject4.focus();\r\n\t}else if (x.lstProject5.value != \"\" && (x.lstProject4.value == x.lstProject5.value)){\r\n\t\talert('CANNOT select Two consecutive SAME Projects');\r\n\t\tx.lstProject5.focus();\r\n\t}else if (x.lstProject6.value != \"\" && (x.lstProject5.value == x.lstProject6.value)){\r\n\t\talert('CANNOT select Two consecutive SAME Projects');\r\n\t\tx.lstProject6.focus();\r\n\t}else if (x.lstProject7.value != \"\" && (x.lstProject6.value == x.lstProject7.value)){\r\n\t\talert('CANNOT select Two consecutive SAME Projects');\r\n\t\tx.lstProject7.focus();\r\n\t}else if (x.lstProject8.value != \"\" && (x.lstProject7.value == x.lstProject8.value)){\r\n\t\talert('CANNOT select Two consecutive SAME Projects');\r\n\t\tx.lstProject8.focus();\r\n\t}else if (x.lstProject9.value != \"\" && (x.lstProject8.value == x.lstProject9.value)){\r\n\t\talert('CANNOT select Two consecutive SAME Projects');\r\n\t\tx.lstProject9.focus();\r\n\t}else if (x.txtProject1.value == \"\" && (x.txtProject2.value != \"\" || x.txtProject3.value != \"\" || x.txtProject4.value != \"\" || x.txtProject5.value != \"\" || x.txtProject6.value != \"\" || x.txtProject7.value != \"\" || x.txtProject8.value != \"\" || x.txtProject9.value != \"\")){\r\n\t\talert('Please enter the Projects in CONSECUTIVE order ONLY');\r\n\t\tx.txtProject1.focus();\r\n\t}else if (x.txtProject2.value == \"\" && (x.txtProject3.value != \"\" || x.txtProject4.value != \"\" || x.txtProject5.value != \"\" || x.txtProject6.value != \"\" || x.txtProject7.value != \"\" || x.txtProject8.value != \"\" || x.txtProject9.value != \"\")){\r\n\t\talert('Please enter the Projects in CONSECUTIVE order ONLY');\r\n\t\tx.txtProject2.focus();\r\n\t}else if (x.txtProject3.value == \"\" && (x.txtProject4.value != \"\" || x.txtProject5.value != \"\" || x.txtProject6.value != \"\" || x.txtProject7.value != \"\" || x.txtProject8.value != \"\" || x.txtProject9.value != \"\")){\r\n\t\talert('Please enter the Projects in CONSECUTIVE order ONLY');\r\n\t\tx.txtProject3.focus();\r\n\t}else if (x.txtProject4.value == \"\" && (x.txtProject5.value != \"\" || x.txtProject6.value != \"\" || x.txtProject7.value != \"\" || x.txtProject8.value != \"\" || x.txtProject9.value != \"\")){\r\n\t\talert('Please enter the Projects in CONSECUTIVE order ONLY');\r\n\t\tx.txtProject4.focus();\r\n\t}else if (x.txtProject5.value == \"\" && (x.txtProject6.value != \"\" || x.txtProject7.value != \"\" || x.txtProject8.value != \"\" || x.txtProject9.value != \"\")){\r\n\t\talert('Please enter the Projects in CONSECUTIVE order ONLY');\r\n\t\tx.txtProject5.focus();\r\n\t}else if (x.txtProject6.value == \"\" && (x.txtProject7.value != \"\" || x.txtProject8.value != \"\" || x.txtProject9.value != \"\")){\r\n\t\talert('Please enter the Projects in CONSECUTIVE order ONLY');\r\n\t\tx.txtProject6.focus();\r\n\t}else if (x.txtProject7.value == \"\" && (x.txtProject8.value != \"\" || x.txtProject9.value != \"\")){\r\n\t\talert('Please enter the Projects in CONSECUTIVE order ONLY');\r\n\t\tx.txtProject7.focus();\r\n\t}else if (x.txtProject8.value == \"\" && (x.txtProject9.value != \"\")){\r\n\t\talert('Please enter the Projects in CONSECUTIVE order ONLY');\r\n\t\tx.txtProject8.focus();\r\n\t}else{\r\n\t\tvar lt = x.txtStart.value;\r\n\t\tif (x.txtProject1.value != \"\"){lt = x.txtProject1.value*100; count++;}\r\n\t\tif (x.txtProject2.value != \"\"){lt = x.txtProject2.value*100; count++;}\r\n\t\tif (x.txtProject3.value != \"\"){lt = x.txtProject3.value*100; count++;}\r\n\t\tif (x.txtProject4.value != \"\"){lt = x.txtProject4.value*100; count++;}\r\n\t\tif (x.txtProject5.value != \"\"){lt = x.txtProject5.value*100; count++;}\r\n\t\tif (x.txtProject6.value != \"\"){lt = x.txtProject6.value*100; count++;}\r\n\t\tif (x.txtProject7.value != \"\"){lt = x.txtProject7.value*100; count++;}\r\n\t\tif (x.txtProject8.value != \"\"){lt = x.txtProject8.value*100; count++;}\r\n\t\tif (x.txtProject9.value != \"\"){lt = x.txtProject9.value*100; count++;}\t\t\r\n\t\t\r\n\t\tif (x.lstShiftType == 0){\r\n\t\t\tif (x.txtProject1.value != '' && x.txtProject1.value*100 < x.txtStart.value*1){\r\n\t\t\t\talert(\"Start Time for Project 2 should be MORE THAN the Start Time for Project 1\");\r\n\t\t\t\tx.txtProject1.focus();\r\n\t\t\t}else if (x.txtProject2.value != '' && x.txtProject2.value*100 < x.txtProject1.value*100){\r\n\t\t\t\talert(\"Start Time for Project 3 should be MORE THAN the Start Time for Project 2\");\r\n\t\t\t\tx.txtProject2.focus();\r\n\t\t\t}else if (x.txtProject3.value != '' && x.txtProject3.value*100 < x.txtProject2.value*100){\r\n\t\t\t\talert(\"Start Time for Project 4 should be MORE THAN the Start Time for Project 3\");\r\n\t\t\t\tx.txtProject3.focus();\r\n\t\t\t}else if (x.txtProject4.value != '' && x.txtProject4.value*100 < x.txtProject3.value*100){\r\n\t\t\t\talert(\"Start Time for Project 5 should be MORE THAN the Start Time for Project 4\");\r\n\t\t\t\tx.txtProject4.focus();\r\n\t\t\t}else if (x.txtProject5.value != '' && x.txtProject5.value*100 < x.txtProject4.value*100){\r\n\t\t\t\talert(\"Start Time for Project 6 should be MORE THAN the Start Time for Project 5\");\r\n\t\t\t\tx.txtProject5.focus();\r\n\t\t\t}else if (x.txtProject6.value != '' && x.txtProject6.value*100 < x.txtProject5.value*100){\r\n\t\t\t\talert(\"Start Time for Project 7 should be MORE THAN the Start Time for Project 6\");\r\n\t\t\t\tx.txtProject6.focus();\r\n\t\t\t}else if (x.txtProject7.value != '' && x.txtProject7.value*100 < x.txtProject6.value*100){\r\n\t\t\t\talert(\"Start Time for Project 8 should be MORE THAN the Start Time for Project 7\");\r\n\t\t\t\tx.txtProject7.focus();\r\n\t\t\t}else if (x.txtProject8.value != '' && x.txtProject8.value*100 < x.txtProject7.value*100){\r\n\t\t\t\talert(\"Start Time for Project 9 should be MORE THAN the Start Time for Project 8\");\r\n\t\t\t\tx.txtProject8.focus();\r\n\t\t\t}else if (x.txtProject9.value != '' && x.txtProject9.value*100 < x.txtProject8.value*100){\r\n\t\t\t\talert(\"Start Time for Project 10 should be MORE THAN the Start Time for Project 9\");\r\n\t\t\t\tx.txtProject9.focus();\r\n\t\t\t}else if (x.txtProject10.value < lt){\r\n\t\t\t\talert(\"End Time should be MORE THAN the Start Time for the last Project\");\t\t\t\r\n\t\t\t}else{\r\n\t\t\t\tx.txtCounter.value = count;\r\n\t\t\t\tx.submit();\r\n\t\t\t}\r\n\t\t}else{\r\n\t\t\tx.txtCounter.value = count;\r\n\t\t\tx.submit();\r\n\t\t}\r\n\t}\r\n}\r\n\r\nfunction check_valid_date(z){\r\n\t//alert(DD);\r\n\t//alert(MM);\r\n\t//alert(YYYY);\r\n\t//z = DD+\"/\"+MM+\"/\"YYYY;\r\n\tif(z.length != 10 || z.substring(6,10)*1 < 1900 || z.substring(6,10)*1 > 2200){\r\n\t\treturn false;\r\n\t}else{\r\n\t\tif (z.substring(0,2)*1 < 28 && z.substring(3,5)*1 < 13 && z.substring(2,3) == '/'  && z.substring(5,6) == '/'){\r\n\t\t\treturn true;\r\n\t\t}else{\r\n\t\t\tif ((z.substring(3,5)*1 == 4 || z.substring(3,5)*1 == 6 || z.substring(3,5)*1 == 9 || z.substring(3,5)*1 == 11) && z.substring(0,2)*1 < 31){\r\n\t\t\t\treturn true;\r\n\t\t\t}else if (z.substring(3,5)*1 == 2 && z.substring(6,10)*1 % 4 == 0 && z.substring(0,2)*1 < 30){\r\n\t\t\t\treturn true;\r\n\t\t\t}else if (z.substring(3,5)*1 == 2 && z.substring(6,10)*1 % 4 != 0 && z.substring(0,2)*1 < 29){\r\n\t\t\t\treturn true;\r\n\t\t\t}else if ((z.substring(3,5)*1 == 1 || z.substring(3,5)*1 == 3 || z.substring(3,5)*1 == 5 || z.substring(3,5)*1 == 7 || z.substring(3,5)*1 == 8 || z.substring(3,5)*1 == 10 || z.substring(3,5)*1 == 12) && z.substring(0,2)*1 < 32){\r\n\t\t\t\treturn true;\r\n\t\t\t}else{\r\n\t\t\t\treturn false;\r\n\t\t\t}\t\t\r\n\t\t}\r\n\t}\r\n}\r\n\r\nfunction deleteRecord(x){\r\n\tif (confirm('Delete this Record')){\r\n\t\twindow.location.href='AssignProjectChild.php?act=deleteRecord&lstED='+(x*1024);\r\n\t}\r\n}\r\n\r\nfunction openWindow(a, b){\t\r\n\twin = window.open('AssignProjectChild.php?lstED='+(b*1024)+'&act='+a, 'toolbar=no,menubar=no,scrollbars=yes,resize=yes,maximize=no,location=no,height=250,width=800'); \r\n\twin.creator = self;\r\n}\r\n\r\nfunction checkTime(x, a){\r\n\t//alert(a);\r\n\tif (a.length != 4){\r\n\t\treturn false;\r\n\t}else if (a*1 != a/1){\r\n\t\treturn false;\r\n\t}else if (a.substring(0, 2)*1 > 24){\r\n\t\treturn false;\r\n\t}else if (a.substring(2, 4)*1 > 59){\r\n\t\treturn false;\r\n\t}else if (a*1 > x.txtBreakFrom.value.substring(0, 4)*1 && a*1 < x.txtBreakTo.value.substring(0, 4)*1){\r\n\t\treturn false;\r\n\t}\r\n}\r\n</script>\r\n</center></body></html>";

?>