<?php
ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "12";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=ShiftMaster.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$iconn = openIConnection();
//$uconn = mysqli_connect("127.0.0.1", "unisuser", "unisamho", "UNIS");
$uconn = mysqli_connect("127.0.0.1", "root", "namaste", "UNIS");
if ($prints != "yes") {
    include 'header.php';
?>
<div class="page-breadcrumb">
                    <div class="row">
                        <div class="col-12 d-flex no-block align-items-center">
                            <h4 class="page-title">Shift Settings</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Shift Settings
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

<?php
}
//print "<html><title>Shift Settings</title><body onLoad='javascript:checkSchedule()'><center>";
//displayHeader($prints, false, false);
//print "<center>";
//displayLinks($current_module, $userlevel);
//print "</center>";
$act = $_POST["act"];
if ($act == "") {
    $act = $_GET["act"];
}
$message = $_GET["message"];
if ($message == "") {
    $message = "Shift Settings";
}
$lstShift = $_POST["lstShift"];
if ($lstShift == "") {
    $lstShift = $_GET["lstShift"];
}
$txtID = $_POST["txtID"];
if ($txtID == "") {
    $txtID = $_GET["txtID"];
}
$txtName = $_POST["txtName"];
$txtMinWorkForBreak = $_POST["txtMinWorkForBreak"];
if ($txtMinWorkForBreak == "") {
    $txtMinWorkForBreak = "0";
}
$txtMinOTWorkForBreak = $_POST["txtMinOTWorkForBreak"];
if ($txtMinOTWorkForBreak == "") {
    $txtMinOTWorkForBreak = "0";
}
$txtStart = $_POST["txtStart"];
$txtGraceTo = $_POST["txtGraceTo"];
$txtFlexiBreak = $_POST["txtFlexiBreak"];
$txtBreakFrom = $_POST["txtBreakFrom"];
$txtBreakTo = $_POST["txtBreakTo"];
$txtLunchFrom = $_POST["txtLunchFrom"];
$txtLunchTo = $_POST["txtLunchTo"];
$txtSnacksFrom = $_POST["txtSnacksFrom"];
$txtSnacksTo = $_POST["txtSnacksTo"];
$txtClose = $_POST["txtClose"];
$lstNightFlag = $_POST["lstNightFlag"];
$txtWorkMin = $_POST["txtWorkMin"];
$lstSchedule = $_POST["lstSchedule"];
if ($lstSchedule == "") {
    $lstSchedule = "5";
}
$lstShiftType = $_POST["lstShiftType"];
if ($lstShiftType == "") {
    $lstShiftType = "1";
}
$lstMoveNS = $_POST["lstMoveNS"];
if ($lstMoveNS == "") {
    $lstMoveNS = "No";
}
$lstDNT = $_POST["lstDNT"];
if ($lstDNT == "") {
    $lstDNT = "No";
}
if ($act == "deleteRecord") {
    $txtID = $txtID / 1024;
    $query = "SELECT group_id FROM tuser WHERE group_id = " . $txtID;
    $result = selectData($conn, $query);
    if ($cur[0] != "") {
        $message = "Record cannot be Deleted as it is associated with one or more Employees";
    } else {
        $query = "SELECT e_group FROM tenter WHERE e_group = " . $txtID;
        $result = selectData($conn, $query);
        if ($cur[0] != "") {
            $message = "Record cannot be Deleted as it is associated with one or more Clockin";
        } else {
            $query = "DELETE FROM tgroup WHERE id = " . $txtID;
            updateIData($iconn, $query, true);
            $query = "DELETE FROM tworktype WHERE C_Code = " . $txtID;
            updateIData($uconn, $query, true);
            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Deleted Shift ID: " . $txtID . "')";
            updateIData($iconn, $query, true);
            $message = "Record Deleted";
        }
    }
    header("Location: " . $PHP_SELF . "?message=" . $message);
} else {
    if ($act == "editRecord") {
        if ($lstNightFlag == "True") {
            $lstNightFlag = "1";
        } else {
            $lstNightFlag = "0";
        }
        $query = "UPDATE tgroup SET Name = '" . replaceString($txtName, true) . "', ShiftTypeID = " . $lstShiftType . ", NightFlag = " . $lstNightFlag . ", ScheduleID = " . $lstSchedule . ", WorkMin = '" . $txtWorkMin . "', MoveNS = '" . $lstMoveNS . "', DNT = '" . $lstDNT . "'";
        $text = "Updated Shift ID: " . $txtID . " SET Name = " . replaceString($txtName, true) . ", NightFlag = " . $lstNightFlag . ", WorkMinutes = " . $txtWorkMin . ", MoveNS = " . $lstMoveNS . ", DNT = " . $lstDNT;
        if ($lstSchedule == 4) {
            $query = $query . ", Start = '', GraceTo = '', Close = '', FlexiBreak = 0, BreakFrom = '', BreakTo = '' ";
            $text = $text . ", FlexiBreak = 0";
        } else {
            if ($lstSchedule == 1 || $lstSchedule == 2 || $lstSchedule == 5) {
                $query = $query . ", Start = '" . $txtStart . "', GraceTo = '" . $txtGraceTo . "', Close = '" . $txtClose . "', FlexiBreak = '" . $txtFlexiBreak . "', BreakFrom = '', BreakTo = '' ";
                $text = $text . ", Start = " . $txtStart . ", GraceTo = " . $txtGraceTo . ", Close = " . $txtClose . ", FlexiBreak = " . $txtFlexiBreak;
            } else {
                if ($lstSchedule == 3) {
                    $query = $query . ", Start = '" . $txtStart . "', GraceTo = '" . $txtGraceTo . "', Close = '" . $txtClose . "', FlexiBreak = 0, BreakFrom = '" . $txtLunchFrom . "', BreakTo = '" . $txtLunchTo . "' ";
                    $text = $text . ", Start = " . $txtStart . ", GraceTo = " . $txtGraceTo . ", Close = " . $txtClose . ", FlexiBreak = 0, BreakFrom = " . $txtLunchFrom . ", BreakTo = " . $txtLunchTo;
                } else {
                    if ($lstSchedule == 6) {
                        $query = $query . ", Start = '', GraceTo = '', Close = '', FlexiBreak = '" . $txtFlexiBreak . "', BreakFrom = '', BreakTo = '' ";
                        $text = $text . ", FlexiBreak = " . $txtFlexiBreak;
                    }
                }
            }
        }
        $query = $query . " WHERE id = " . $txtID;
        if (updateIData($iconn, $query, true)) {
            $query = "UPDATE tworktype SET C_Name = '" . replaceString($txtName, true) . "' WHERE C_Code = " . $txtID;
            updateIData($uconn, $query, true);
            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
            if (updateIData($iconn, $query, true)) {
                header("Location: ShiftMaster.php?lstShift=" . $txtID . "&message=Shift updated");
            } else {
                header("Location: ShiftMaster.php?lstShift=" . $txtID . "&message=Shift COULD NOT be updated");
            }
        } else {
            header("Location: ShiftMaster.php?lstShift=" . $txtID . "&message=Shift COULD NOT be updated");
        }
    } else {
        if ($act == "addRecord") {
            $query = "SELECT TCount, MACAddress FROM OtherSettingMaster";
            $result = selectData($conn, $query);
            $tcount = substr(encryptDecrypt($result[0]), 18, strlen(encryptDecrypt($result[0])) - 18);
            if ($lstNightFlag == "True") {
                $lstNightFlag = "1";
            } else {
                $lstNightFlag = "0";
            }
            $max = getMax($conn, "tgroup", "id");
            if ($max < 2) {
                $max = 10;
            }
            $text = "";
            if ($lstSchedule == 4) {
                $txtFlexiBreakValue = is_numeric($txtFlexiBreak) && $txtFlexiBreak !== '' ? $txtFlexiBreak : 'NULL';
                $query = "INSERT INTO tgroup (id, Name, reg_date, timelimit, MinWorkForBreak, Start, GraceTo, FlexiBreak, BreakFrom, BreakTo, Close, NightFlag, ShiftTypeID, ScheduleID, WorkMin, MoveNS, DNT) VALUES ('" . $max . "', '" . replaceString($txtName, true) . "', " . insertToday() . getNow() . ", '00002359', '0', '', '', '0', '', '', '', '" . $lstNightFlag . "', '" . $lstShiftType . "', '" . $lstSchedule . "', '" . $txtWorkMin . "', '" . $lstMoveNS . "', '" . $lstDNT . "')";
                $text = "Created Shift (id, Name, reg_date, timelimit, MinWorkForBreak, Start, GraceTo, FlexiBreak, BreakFrom, BreakTo, Close, NightFlag, ShiftTypeID, ScheduleID, WorkMin, MoveNS) VALUES (" . $max . ", " . replaceString($txtName, true) . ", " . insertToday() . getNow() . ", 00002359, 0, , , 0, , , , " . $lstNightFlag . ", " . $lstShiftType . ", " . $lstSchedule . ", " . $txtWorkMin . ", " . $lstMoveNS . ", " . $lstDNT . ")";
            } else {
                if ($lstSchedule == 1 || $lstSchedule == 2 || $lstSchedule == 5) {
                    $txtFlexiBreakValue = is_numeric($txtFlexiBreak) && $txtFlexiBreak !== '' ? $txtFlexiBreak : 'NULL';
                    $query = "INSERT INTO tgroup (id, Name, reg_date, timelimit, MinWorkForBreak, Start, GraceTo, FlexiBreak, BreakFrom, BreakTo, Close, NightFlag, ShiftTypeID, ScheduleID, WorkMin, MoveNS, DNT) VALUES ('" . $max . "', '" . replaceString($txtName, true) . "', " . insertToday() . getNow() . ", '00002359', '" . $txtMinWorkForBreak . "', '" . $txtStart . "', '" . $txtGraceTo . "', '" . $txtFlexiBreakValue . "', '', '', '" . $txtClose . "', '" . $lstNightFlag . "', '" . $lstShiftType . "', '" . $lstSchedule . "', '" . $txtWorkMin . "', '" . $lstMoveNS . "', '" . $lstDNT . "')";
                    $text = "Created Shift (id, Name, reg_date, timelimit, MinWorkForBreak, Start, GraceTo, FlexiBreak, BreakFrom, BreakTo, Close, NightFlag, ShiftTypeID, ScheduleID, WorkMin, MoveNS) VALUES (" . $max . ", " . replaceString($txtName, true) . ", " . insertToday() . getNow() . ", 00002359, " . $txtMinWorkForBreak . ", " . $txtStart . ", " . $txtGraceTo . ", " . $txtFlexiBreak . ", , , " . $txtClose . ", " . $lstNightFlag . ", " . $lstShiftType . ", " . $lstSchedule . ", " . $txtWorkMin . ", " . $lstMoveNS . ", " . $lstDNT . ")";
                } else {
                    if ($lstSchedule == 3) {
                        $txtFlexiBreakValue = is_numeric($txtFlexiBreak) && $txtFlexiBreak !== '' ? $txtFlexiBreak : 'NULL';
                        $query = "INSERT INTO tgroup (id, Name, reg_date, timelimit, MinWorkForBreak, Start, GraceTo, FlexiBreak, BreakFrom, BreakTo, Close, NightFlag, ShiftTypeID, ScheduleID, WorkMin, MoveNS, DNT) VALUES ('" . $max . "', '" . replaceString($txtName, true) . "', " . insertToday() . getNow() . ", '00002359', '" . $txtMinWorkForBreak . "', '" . $txtStart . "', '" . $txtGraceTo . "', '0', '" . $txtLunchFrom . "', '" . $txtLunchTo . "', '" . $txtClose . "', '" . $lstNightFlag . "', '" . $lstShiftType . "', '" . $lstSchedule . "', '" . $txtWorkMin . "', '" . $lstMoveNS . "', '" . $lstDNT . "')";
                        $text = "Created Shift (id, Name, reg_date, timelimit, MinWorkForBreak, Start, GraceTo, FlexiBreak, BreakFrom, BreakTo, Close, NightFlag, ShiftTypeID, ScheduleID, WorkMin, MoveNS) VALUES (" . $max . ", " . replaceString($txtName, true) . ", " . insertToday() . getNow() . ", 00002359, " . $txtMinWorkForBreak . ", " . $txtStart . ", " . $txtGraceTo . ", 0, " . $txtLunchFrom . ", " . $txtLunchTo . ", " . $txtClose . ", " . $lstNightFlag . ", " . $lstShiftType . ", " . $lstSchedule . ", " . $txtWorkMin . ", " . $lstMoveNS . ", " . $lstDNT . ")";
                    } else {
                        if ($lstSchedule == 6) {
                            $txtFlexiBreakValue = is_numeric($txtFlexiBreak) && $txtFlexiBreak !== '' ? $txtFlexiBreak : 'NULL';
                            $query = "INSERT INTO tgroup (id, Name, reg_date, timelimit, MinWorkForBreak, Start, GraceTo, FlexiBreak, BreakFrom, BreakTo, Close, NightFlag, ShiftTypeID, ScheduleID, WorkMin, MoveNS, DNT) VALUES ('" . $max . "', '" . replaceString($txtName, true) . "', " . insertToday() . getNow() . ", '00002359', '" . $txtMinWorkForBreak . "', '', '', '" . $txtFlexiBreakValue . "', '', '', '', '" . $lstNightFlag . "', '" . $lstShiftType . "', '" . $lstSchedule . "', '" . $txtWorkMin . "', '" . $lstMoveNS . "', '" . $lstDNT . "')";
                            $text = "Created Shift (id, Name, reg_date, timelimit, MinWorkForBreak, Start, GraceTo, FlexiBreak, BreakFrom, BreakTo, Close, NightFlag, ShiftTypeID, ScheduleID, WorkMin, MoveNS, DNT) VALUES (" . $max . ", " . replaceString($txtName, true) . ", " . insertToday() . getNow() . ", 00002359, " . $txtMinWorkForBreak . ", , , " . $txtFlexiBreak . ", , , , " . $lstNightFlag . ", " . $lstShiftType . ", " . $lstSchedule . ", " . $txtWorkMin . ", " . $lstMoveNS . ", " . $lstDNT . ")";
                        } else {
                            if ($lstSchedule == 7) {
                                $txtFlexiBreakValue = is_numeric($txtFlexiBreak) && $txtFlexiBreak !== '' ? $txtFlexiBreak : 'NULL';
                                $query = "INSERT INTO tgroup (id, Name, reg_date, timelimit, MinWorkForBreak, Start, GraceTo, FlexiBreak, BreakFrom, BreakTo, Close, NightFlag, ShiftTypeID, ScheduleID, WorkMin, MoveNS, DNT) VALUES ('" . $max . "', '" . replaceString($txtName, true) . "', " . insertToday() . getNow() . ", '00002359', '" . $txtMinWorkForBreak . "', '" . $txtStart . "', '', '" . $txtFlexiBreakValue . "', '', '', '" . $txtClose . "', '" . $lstNightFlag . "', '" . $lstShiftType . "', '" . $lstSchedule . "', '" . $txtWorkMin . "', '" . $lstMoveNS . "', 'No')";
                                $text = "Created Shift (id, Name, reg_date, timelimit, MinWorkForBreak, Start, GraceTo, FlexiBreak, BreakFrom, BreakTo, Close, NightFlag, ShiftTypeID, ScheduleID, WorkMin, MoveNS, DNT) VALUES (" . $max . ", " . replaceString($txtName, true) . ", " . insertToday() . getNow() . ", 00002359, " . $txtMinWorkForBreak . ", " . $txtStart . ", , " . $txtFlexiBreak . ", " . $txtLunchFrom . ", " . $txtLunchTo . "," . $txtClose . " , " . $lstNightFlag . ", Daily, " . $lstSchedule . ", " . $txtWorkMin . ", " . $lstMoveNS . ", No)";
                            }
                        }
                    }
                }
            }
            if (updateIData($iconn, $query, true)) {
                $query = "INSERT INTO tworktype (C_Code, C_Name) VALUES ('" . $max . "', '" . replaceString($txtName, true) . "')";
                updateIData($uconn, $query, true);
                $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                updateIData($iconn, $query, true);
                header("Location: ShiftMaster.php?lstShift=" . $max . "&message=Shift added");
            } else {
                $query;
                exit;
            }
        } else {
            if ($act == "changeScheduleType") {
                $query = "UPDATE tgroup SET ScheduleID = 5 WHERE id = " . $txtID;
                updateIData($iconn, $query, true);
                $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Changed Schedule Type to Fixed Start-End Multi In-Out for Shift ID " . $txtID . "')";
                updateIData($iconn, $query, true);
                header("Location: ShiftMaster.php?lstShift=" . $txtID . "&message=Shift updated");
            } else {
                if ($act == "changeMinWorkForBreak") {
                    header("Location: ShiftMaster.php?lstShift=" . $txtID . "&message=Shift updated");
                }
            }
        }
    }
}
print '<center>';
print'<div class="container-fluid">
                <div class="row">
                        <div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12">
                            <div class="card">
                                    <div class="card-body table-responsive">';


echo "<script>\r\nfunction deleteRecord(){\r\n\tif (confirm('Delete this Record')){\r\n\t\twindow.location.href='ShiftMaster.php?act=deleteRecord&txtID='+(document.frm2.txtID.value*1024);\r\n\t}\r\n}\r\n\r\nfunction checkSchedule(){\r\n\tx = document.frm2;\r\n\ta = x.lstSchedule.value;\r\n\r\n\tif (a == 1 || a == 2 || a == 5){\r\n\t\t//Fixed Start-End, Flexi Multi Break\r\n\t\tx.txtStart.disabled = false;\r\n\t\tx.txtGraceTo.disabled = false;\r\n\t\tx.txtClose.disabled = false;\r\n\t\tx.txtFlexiBreak.disabled = false;\r\n\t\t\r\n\t\tx.txtLunchFrom.disabled = true;\r\n\t\tx.txtLunchTo.disabled = true;\r\n\t}else if (a == 3){\r\n\t\t//Fixed Start-End, Fixed Break\r\n\t\tx.txtFlexiBreak.disabled = true;\r\n\r\n\t\tx.txtStart.disabled = false;\r\n\t\tx.txtGraceTo.disabled = false;\r\n\t\tx.txtClose.disabled = false;\r\n\t\tx.txtLunchFrom.disabled = false;\r\n\t\tx.txtLunchTo.disabled = false;\t\t\r\n\t}else if (a == 4 || a == 6 || a == 7){\r\n\t\t//Flexi Start-End, No Break\r\n\t\tif (a == 4){\r\n\t\t\tx.txtFlexiBreak.disabled = true;\r\n\t\t}else{\r\n\t\t\tx.txtFlexiBreak.disabled = false;\r\n\t\t}\r\n\t\tif (a == 7){\r\n\t\t\tx.lstNightFlag.value = 'False';\r\n\t\t\tx.lstMoveNS.value = 'No';\r\n\t\t\tx.txtStart.disabled = false;\r\n\t\t\tx.txtClose.disabled = false;\r\n\t\t\tx.txtLunchFrom.disabled = false;\r\n\t\t\tx.txtLunchTo.disabled = false;\r\n\t\t}\r\n\t\tif (a != 7){\r\n\t\t\tx.txtStart.disabled = true;\r\n\t\t\tx.txtClose.disabled = true;\r\n\t\t\tx.txtLunchFrom.disabled = true;\r\n\t\t\tx.txtLunchTo.disabled = true;\r\n\t\t}\t\r\n\t\tx.txtGraceTo.disabled = true;\t\t\r\n\t}\r\n}\r\n\r\nfunction checkSubmit(c){\r\n\tx = document.frm2;\r\n\ta = x.lstSchedule.value;\r\n\tb = x.lstShiftType.value;\r\n\t\r\n\tif (x.txtName.value == ''){\r\n\t\talert('Please enter the Shift Name');\r\n\t\tx.txtName.focus();\r\n\t}else if (a == ''){\r\n\t\talert('Please select the Schedule Type');\r\n\t\tx.lstSchedule.focus();\r\n\t}else if (b == ''){\r\n\t\talert('Please select the Routine Type');\r\n\t\tx.lstShiftType.focus();\t\t\r\n\t}else if (x.lstNightFlag.value == 'False' && x.lstMoveNS.value == 'Yes'){\r\n\t\talert('Move Night Shift to Next Day CANNOT be YES for Day Shift');\r\n\t\tx.lstMoveNS.focus();\t\r\n\t}else if (a == 7 && (x.lstNightFlag.value == 'True' || x.lstMoveNS.value == 'Yes')){\r\n\t\talert('Night Shift NOT ALLOWED');\r\n\t\tx.lstNightFlag.focus();\t\t\r\n\t}else{\r\n\t\tif (c == 0){\r\n\t\t\tx.act.value = 'addRecord';\r\n\t\t}else{\r\n\t\t\tx.act.value = 'editRecord';\r\n\t\t}\r\n\t\tx.submit();\r\n\t}\r\n}\r\n\r\nfunction changeScheduleType(){\r\n\tx = document.frm2;\r\n\tx.act.value = 'changeScheduleType';\r\n\tx.submit();\r\n}\r\n\r\nfunction changeMinWorkForBreak(){\r\n\tx = document.frm2;\r\n\tif (x.txtMinWorkForBreak.value*1 != x.txtMinWorkForBreak.value/1 || x.txtMinOTWorkForBreak.value*1 != x.txtMinOTWorkForBreak.value/1 || x.txtMinOT1Work.value*1 != x.txtMinOT1Work.value/1){\r\n\t\talert('Please enter valid: \\r\\nMinimum Minutes to be Worked before deduction of Break Time from Normal Working Min \\r\\nMinimum Minutes to be Worked before deduction of Break Time from Normal Working Min on OT Day/Date \\r\\nNormal Minutes to be Worked before paying OT on OT1 Day');\r\n\t\tx.txtMinWorkForBreak.focus();\r\n\t}else if (x.lstNightFlag.value == 'False' && x.lstMoveNS.value == 'Yes'){\r\n\t\talert('Move Night Shift to Next Day CANNOT be YES for Day Shift');\r\n\t\tx.lstMoveNS.focus();\r\n\t}else if (x.txtASAbsent.value == '' || x.txtASAbsent.value*1 != x.txtASAbsent.value/1 || x.txtASAbsent.value*1 > 365 || x.txtASAbsent.value*1 < 0 ){\r\n\t\talert('Please enter valid Data for \\r\\nNumber of Days of Absence after which Employee should be Automatically Suspended');\r\n\t\tx.txtASAbsent.focus();\r\n\t}else{\r\n\t\tx.act.value = 'changeMinWorkForBreak';\r\n\t\tx.submit();\r\n\t}\r\n}\r\n\r\nfunction checkTime(a){\r\n\t//alert(a);\r\n\tif (a.length != 4){\r\n\t\treturn false;\r\n\t}else if (a*1 != a/1){\r\n\t\treturn false;\r\n\t}else if (a.substring(0, 2)*1 > 24){\r\n\t\treturn false;\r\n\t}else if (a.substring(2, 4)*1 > 59){\r\n\t\treturn false;\r\n\t}\r\n}\r\n\r\n</script>\r\n";
?>
<div class="card">
    <div class="card-body">
        <?php 
        print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b></font></p>";
        ?>
        <div class="row">
            <div class="col-5"></div>
            <div class="col-2">
                <?php 
                print "<label class='form-label'>Select a record from list to edit/delete</label>";
                print "<form name='frm1' method='post' action='ShiftMaster.php'><tr>";
                $query = "SELECT id, Name FROM tgroup WHERE id > 1 ORDER BY Name";
                $prints = "no";
                displayList("lstShift", "Shift Name: ", $lstShift, $prints, $conn, $query, "onChange=javascript:window.location.href='ShiftMaster.php?lstShift='+document.frm1.lstShift.value", "40%", "60%");
                print "</form>";
                ?>
            </div>
        </div>
        <hr style="height: 10px;">
            <?php 
                print "<tr><td bgcolor='#FFFFFF' colspan='2'><img height='2' width='100%' src='img/orange-bar.gif'/></td></tr>";
                if ($lstShift != "") {
                    print "<label class='form-label'>Edit a record</label>";
                } else {
                    print "<label class='form-label'>Add a new record</label>";
                }
                if ($lstShift != "") {
                    $query = "SELECT id, MinWorkForBreak, Name, Start, GraceTo, FlexiBreak, BreakFrom, BreakTo, '', '', '', '', Close, NightFlag, WorkMin, ScheduleID, ShiftTypeID, MinOTWorkForBreak, MinOT1Work, AccessRestrict, RelaxRestrict, StartHour, CloseHour, EarlyInOT, LessLunchOT, NoBreakException, EarlyInOTDayDate, MinOTValue, MaxOTValue, ASLate, ASAbsent, MaxOTValueOT1, MaxOTValueOT2, MoveNS, DNT FROM tgroup WHERE id = " . $lstShift;
                    $result = selectData($conn, $query);
                    $txtID = $result[0];
                    $txtMinWorkForBreak = $result[1];
                    $txtName = $result[2];
                    $txtStart = $result[3];
                    $txtGraceTo = $result[4];
                    $txtFlexiBreak = $result[5];
                    $txtLunchFrom = $result[6];
                    $txtLunchTo = $result[7];
                    $txtSnacksFrom = $result[10];
                    $txtSnacksTo = $result[11];
                    $txtClose = $result[12];
                    $lstNightFlag = $result[13];
                    $txtWorkMin = $result[14];
                    $lstSchedule = $result[15];
                    $lstShiftType = $result[16];
                    $txtMinOTWorkForBreak = $result[17];
                    $txtMinOT1Work = $result[18];
                    $lstAccessRestrict = $result[19];
                    $lstRelaxRestrict = $result[20];
                    $txtStartHour = $result[21];
                    $txtCloseHour = $result[22];
                    $lstEarlyInOT = $result[23];
                    $lstLessLunchOT = $result[24];
                    $lstNoBreakException = $result[25];
                    $lstEarlyInOTDayDate = $result[26];
                    $txtMinOTValue = $result[27];
                    $txtMaxOTValue = $result[28];
                    $txtASLate = $result[29];
                    $txtASAbsent = $result[30];
                    $txtMaxOTValueOT1 = $result[31];
                    $txtMaxOTValueOT2 = $result[32];
                    $lstMoveNS = $result[33];
                    $lstDNT = $result[34];
                }
                
                print "<form name='frm2' method='post' action='ShiftMaster.php'><input type='hidden' name='act'> <input type='hidden' name='txtID' value='" . $txtID . "'> ";
            ?>
        <div class="row">
            <div class="col-3">
                <?php 
                displayTextbox("txtName", "Shift Name: ", $txtName, $prints, "30", "40%", "60%");
                ?>
            </div>
            <div class="col-3">
                <?php 
                $query = "SELECT ScheduleID, Name FROM ScheduleMaster ORDER BY Name";
                displayList("lstSchedule", "Schedule Type:", $lstSchedule, $prints, $conn, $query, "onChange = 'javascript:checkSchedule()'", "", "");
                ?>
            </div>
            <div class="col-3">
                <?php 
                $query = "SELECT ShiftTypeID, Name FROM ShiftTypeMaster ORDER BY Name";
                displayList("lstShiftType", "Routine Type:", $lstShiftType, $prints, $conn, $query, "", "", "");
                ?>
            </div>
            <div class="col-3">
                <?php 
                print "<label class='form-label'>Night Shift:</label><select size='1' name='lstNightFlag' class='form-select select2 shadow-none'>";
                if ($lstNightFlag == 1) {
                    print "<option value='True' selected>True</option> <option value='False'>False</option>";
                } else {
                    print "<option value='False' selected>False</option> <option value='True'>True</option>";
                }
                print "</select>";
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-2">
                <?php 
                print "<label class='form-label'>Move Night Shift to Next Day:</label><select size='1' name='lstMoveNS' class='form-select select2 shadow-none'>";
                print "<option value='" . $lstMoveNS . "' selected>" . $lstMoveNS . "</option> <option value='No'>No</option> <option value='Yes'>Yes</option>";
                print "</select>";
                ?>
            </div>
            <div class="col-2">
                <?php 
                print "<label class='form-label'>Use for Day/Night Terminal:</label><select size='1' name='lstDNT' class='form-select select2 shadow-none'>";
                print "<option value='" . $lstDNT . "' selected>" . $lstDNT . "</option> <option value='No'>No</option> <option value='Yes'>Yes</option>";
                print "</select>";
                ?>
            </div>
            <div class="col-3">
                <?php 
                displayTextbox("txtWorkMin", "Total Working Min (Exclude Break Time): ", $txtWorkMin, $prints, "", "", "");
                ?>
            </div>
            <div class="col-2">
                <?php 
                displayTextbox("txtStart", "Start Time (HHMM): ", $txtStart, $prints, "", "", "");
                ?>
            </div>
            <div class="col-3">
                <?php 
                displayTextbox("txtFlexiBreak", "Total Flexible Break Time (Min): ", $txtFlexiBreak, $prints, "", "", "");
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-3">
                <?php 
                displayTextbox("txtGraceTo", "Grace Time (HHMM): ", $txtGraceTo, $prints, "", "", "");
                ?>
            </div>
            <div class="col-3">
                <?php 
                displayTextbox("txtLunchFrom", "Break From/ Night Start (HHMM): ", $txtLunchFrom, $prints, "", "", "");
                ?>
            </div>
            <div class="col-3">
                <?php 
                displayTextbox("txtClose", "Close Time (HHMM): ", $txtClose, $prints, "", "", "");
                ?>
            </div>
            <div class="col-3">
                <?php 
                displayTextbox("txtLunchTo", "Break To/ Night Close (HHMM): ", $txtLunchTo, $prints, "", "", "");
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <?php 
                if (stripos($userlevel, $current_module . "A") !== false && $lstShift == "") {
                    $query = "SELECT TCount, MACAddress FROM OtherSettingMaster";
                    $result = selectData($conn, $query);
                    $tcount = substr(encryptDecrypt($result[0]), 18, strlen(encryptDecrypt($result[0])) - 18);
                    print "<input type='button' value='Submit Record' class='btn btn-primary' onClick='checkSubmit(0)'>";
                } else {
                    if ($lstShift != "") {
                        $query = "SELECT group_id FROM tuser WHERE group_id = " . $txtID;
                        $result = selectData($conn, $query);
                        if ($result[0] == "" && 2 < $txtID) {
                            if (stripos($userlevel, $current_module . "D") !== false) {
                                print "<input type='button' value='Save Changes' onClick='checkSubmit(1)' class='btn btn-primary'>";
                            } else {
                                print "&nbsp;";
                            }
                        } else {
                            print "<font face='Verdana' size='1' color='#FF0000'><b>Time Alteration Prohibited</b></font>";
                            if ($lstSchedule == 1 || $lstSchedule == 2) {
                                print "<br><input type='button' class='btn btn-primary' value='Change Schedule to Fixed Start-End Multi In-Out' onClick='changeScheduleType()'>";
                            }
                        }
                    }
                }
                ?>
            </div>
        </div>
    <?php print "</form>"; ?>
    </div>
</div>
<?php
print "</center></div></div></div></div></div>";
include 'footer.php';

?>