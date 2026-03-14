<?php
ob_start("ob_gzhandler");
error_reporting(E_ERROR);
set_time_limit(0);
include "Functions.php";
$current_module = "22";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=DeleteProcessedRecord.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$iconn = openIConnection();
$act = $_GET["act"];
if ($act == "") {
    $act = $_POST["act"];
}
$prints = $_GET["prints"];
$excel = $_GET["excel"];
$message = $_GET["message"];
if ($message == "") {
    $message = "Delete Processed Record";
}
$lstShift = $_POST["lstShift"];
$lstDepartment = $_POST["lstDepartment"];
$lstDivision = $_POST["lstDivision"];
$lstWeek = $_POST["lstWeek"];
$lstDay = $_POST["lstDay"];
$lstEmployeeIDFrom = $_POST["lstEmployeeIDFrom"];
$lstEmployeeIDTo = $_POST["lstEmployeeIDTo"];
$lstEmployee = $_POST["lstEmployee"];
if ($lstEmployee == "") {
    $lstEmployee = $_GET["lstEmployee"];
}
$txtEmployee = $_POST["txtEmployee"];
$txtEmployeeCode = $_POST["txtEmployeeCode"];
$txtFrom = $_POST["txtFrom"];
if ($txtFrom == "") {
    $txtFrom = $_GET["txtFrom"];
}
$txtTo = $_POST["txtTo"];
if ($txtTo == "") {
    $txtTo = $_GET["txtTo"];
}
if ($txtFrom == "") {
    if (substr(insertToday(), 6, 2) == "01") {
        if (substr(insertToday(), 4, 2) == "01") {
            $txtFrom = "01/12/" . (substr(insertToday(), 0, 4) - 1);
        } else {
            if (substr(insertToday(), 4, 2) - 1 < 10) {
                $txtFrom = "01/0" . (substr(insertToday(), 4, 2) - 1) . "/" . substr(insertToday(), 0, 4);
            } else {
                $txtFrom = "01/" . (substr(insertToday(), 4, 2) - 1) . "/" . substr(insertToday(), 0, 4);
            }
        }
    } else {
        $txtFrom = "01/" . substr(displayToday(), 3, 7);
    }
}
if ($txtTo == "") {
    $txtTo = displayDate(getLastDay(insertToday(), 1));
}
$txtSNo = $_POST["txtSNo"];
$lstType = $_POST["lstType"];
$lstEmployeeStatus = "";
if (isset($_POST["lstEmployeeStatus"])) {
    $lstEmployeeStatus = $_POST["lstEmployeeStatus"];
} else {
    $lstEmployeeStatus = "ACT";
}
$lstColourFlag = $_POST["lstColourFlag"];
if ($lstColourFlag == "") {
    $lstColourFlag = $_GET["lstColourFlag"];
}
$txtRemark = $_POST["txtRemark"];
$txtPhone = $_POST["txtPhone"];
$lstSort = $_POST["lstSort"];
$txtF1 = $_POST["txtF1"];
$txtF2 = $_POST["txtF2"];
$txtF3 = $_POST["txtF3"];
$txtF4 = $_POST["txtF4"];
$txtF5 = $_POST["txtF5"];
$txtF6 = $_POST["txtF6"];
$txtF7 = $_POST["txtF7"];
$txtF8 = $_POST["txtF8"];
$txtF9 = $_POST["txtF9"];
$txtF10 = $_POST["txtF10"];
if ($prints != "yes") {
    include 'header.php';
?>
<div class="page-breadcrumb">
                    <div class="row">
                        <div class="col-12 d-flex no-block align-items-center">
                            <h4 class="page-title">Delete Processed Record</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Delete Processed Record
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

<?php
}
//print "<html><title>Delete Processed Record</title>";

print "<center>";
if ($excel != "yes") {
//    displayHeader($prints, true, false);
}
print "<center>";
if ($prints != "yes") {
//    displayLinks($current_module, $userlevel);
}
print "</center>";
if ($act == "deleteRecord") {
    $query = "SELECT NightShiftMaxOutTime FROM OtherSettingMaster";
    $result = selectData($conn, $query);
    $txtMaxOut = $result[0];
    $count = $_POST["txtCount"];
    for ($i = 0; $i < $count; $i++) {
        if ($_POST["chkDelete" . $i] != "") {
            $this_query = "SELECT DayMaster.Start, DayMaster.Close FROM DayMaster, tgroup WHERE DayMaster.group_id = tgroup.id AND tgroup.ScheduleID = 7 AND DayMaster.TDate = '" . $_POST["txhADate" . $i] . "' AND DayMaster.e_id = " . $_POST["txhEID" . $i];
            $this_result = selectData($conn, $this_query);
            $query = "DELETE FROM AttendanceMaster WHERE ADate = " . $_POST["txhADate" . $i] . " AND EmployeeID = " . $_POST["txhEID" . $i];
            if (updateIData($iconn, $query, true)) {
                $query = "DELETE FROM DayMaster WHERE TDate = " . $_POST["txhADate" . $i] . " AND e_id = " . $_POST["txhEID" . $i];
                if (updateIData($iconn, $query, true)) {
                    $query = "DELETE FROM WeekMaster WHERE WeekNo = " . $_POST["txhWeek" . $i] . " AND e_id = " . $_POST["txhEID" . $i];
                    if (updateIData($iconn, $query, true)) {
                        $date = $_POST["txhADate" . $i];
                        if ($_POST["txhShift" . $i] == "1") {
                            if ($_POST["txhMoveNS" . $i] == "Yes") {
                                $last_date = getLastDay($date, 1);
                                $query = "UPDATE tenter, tgroup SET tenter.p_flag = 0 WHERE ((tenter.e_date = '" . $date . "' AND tenter.e_time < '" . $txtMaxOut . "00') OR (tenter.e_date = '" . $last_date . "' AND tenter.e_time > '" . $txtMaxOut . "00')) AND tenter.e_group = tgroup.id AND tgroup.NightFlag = 1 AND tenter.e_id = " . $_POST["txhEID" . $i];
                            } else {
                                $next_date = getNextDay($date, 1);
                                $query = "UPDATE tenter, tgroup SET tenter.p_flag = 0 WHERE ((tenter.e_date = '" . $date . "' AND tenter.e_time > '" . $txtMaxOut . "00') OR (tenter.e_date = '" . $next_date . "' AND tenter.e_time < '" . $txtMaxOut . "00')) AND tenter.e_group = tgroup.id AND tgroup.NightFlag = 1 AND tgroup.ScheduleID <> 7 AND tenter.e_id = " . $_POST["txhEID" . $i];
                            }
                        } else {
                            $query = "UPDATE tenter SET tenter.p_flag = 0 WHERE (e_date = '" . $date . "' AND e_time = '" . $this_result[0] . "' AND e_mode = 1) OR ((e_date = '" . $date . "' OR e_date = '" . getNextDay($date, 1) . "') AND e_time = '" . $this_result[1] . "' AND e_mode = 2) AND tenter.e_id = " . $_POST["txhEID" . $i];
                            updateIData($iconn, $query, true);
                            $query = "UPDATE tenter, tgroup SET tenter.p_flag = 0 WHERE tenter.e_date = '" . $date . "' AND tenter.e_group = tgroup.id AND tgroup.NightFlag = 0 AND tenter.e_id = " . $_POST["txhEID" . $i];
                        }
                        if (updateIData($iconn, $query, true)) {
                            $query = "UPDATE FlagDayRotation SET RecStat = 0 WHERE e_date = " . $_POST["txhADate" . $i] . " AND e_id = " . $_POST["txhEID" . $i];
                            if (updateIData($iconn, $query, true)) {
                                $text = "Deleted Processed Record for Employee ID: " . $_POST["txhEID" . $i] . " Dated " . displaydate($_POST["txhADate" . $i]) . " Week " . $_POST["txhWeek" . $i];
                                $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                                updateIData($iconn, $query, true);
                            }
                        }
                    }
                }
            }
        }
    }
    $message = "Record(s) deleted Successfully";
    $act = "searchRecord";
}
print "<form name='frm1' id='frm1' method='post' onSubmit='return checkSearch()' action='DeleteProcessedRecord.php'><input type='hidden' name='act' value='searchRecord'>";
if ($prints != "yes") {
//    print "<body>";
    print'<div class="container-fluid">
                <div class="row">
                        <div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12">
                            <div class="card">
                                    <div class="card-body table-responsive">';
} else {
    if ($excel != "yes") {
        print "<body onLoad='javascript:window.print()'>";
    } else {
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=DeleteProcessedRecord.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        print "<body>";
    }
}

if ($excel != "yes") {
?>
<div class="card">
    <div class="card-body">
        <?php 
        print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b></font></p>";
        if ($prints != "yes") {
            print "<h4 class='card-title'>Select ONE or MORE options and click Search Record</h4>";
//            print "<table width='800' cellpadding='1' cellspacing='-1'>";
        //    print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Select ONE or MORE options and click 'Search Record'</b></font></td></tr>";
        } else {
            if ($excel != "yes") {
//                print "<table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
//                print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Selected Options</b></font></td></tr>";
            }
        }
//        print "<form name='frm1' id='frm1' method='post' onSubmit='return checkSearch()' action='DeleteProcessedRecord.php'><input type='hidden' name='act' value='searchRecord'>";
        ?>
        <div class="row">
            <div class="col-3">
                <?php
                $query = "SELECT id, name from tgroup WHERE id > 1 ORDER BY name";
                displayList("lstShift", "Shift: ", $lstShift, $prints, $conn, $query, "", "", "");
                ?>
            </div>
            <?php 
            displaySearchFields($conn, $prints, $session_variable, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $lstEmployeeName, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
            ?>
        </div>
        <div class="row">
            <div class="col-2">
                <?php 
                $query = "SELECT distinct(Week), Week from AttendanceMaster ORDER BY Week";
                displayList("lstWeek", "Week: ", $lstWeek, $prints, $conn, $query, "", "", "");
                ?>
            </div>
            <div class="col-2">
                <?php 
                if ($prints != "yes") {
                    print "<label class='form-label'>Day:</label><select name='lstDay' class='form-select select2 shadow-none'><option selected value='" . $lstDay . "'>" . $lstDay . "</option> <option value='Sunday'>Sunday</option> <option value='Monday'>Monday</option> <option value='Tuesday'>Tuesday</option> <option value='Wednesday'>Wednesday</option> <option value='Thursday'>Thursday</option> <option value='Friday'>Friday</option> <option value='Saturday'>Saturday</option></select>";
                } else {
                    displayTextbox("lstDay", "Day: ", $lstDay, $prints, 12, "", "");
                }
                ?>
            </div>
            <div class="col-2">
                <?php 
                displayTextbox("txtFrom", "Date From <font size='1'>(DD/MM/YYYY)</font>: ", $txtFrom, $prints, 12, "", "");
                ?>
            </div>
            <div class="col-2">
                <?php 
                displayTextbox("txtTo", "Date To <font size='1'>(DD/MM/YYYY)</font>: ", $txtTo, $prints, 12, "", "");
                ?>
            </div>
            <div class="col-2">
                <?php 
                print "<label class='form-label'>Work Type:</label><select name='lstType' class='form-select select2 shadow-none'><option selected value='" . $lstType . "'>" . $lstType . "</option> <option value='Early In'>Early In</option> <option value='Late In'>Late In</option> <option value='Less Break'>Less Break</option> <option value='More Break'>More Break</option> <option value='Early Out'>Early Out</option> <option value='Late Out'>Late Out</option> <option value='Grace'>Grace</option> <option value='Normal = 0'>Normal = 0</option> <option value='Normal > 0'>Normal > 0</option> <option value='OT > 0'>OT > 0</option> <option value='AOT = 0'>AOT = 0</option> <option value='AOT > 0'>AOT > 0</option> <option value=''>---</option></select>";
                ?>
            </div>
        <?php 
       if ($prints != "yes") {
            print "<div class='col-2'>";
            displayColourFlag($conn, $lstColourFlag, "lstColourFlag", true, true);
            print "</div>";
            print "</div>";
            print "<div class='row'>";
            print "<div class='col-2'>";
            displayEmployeeStatus($conn, "lstEmployeeStatus", $lstEmployeeStatus, "", "");
            print "</div>";
            print "<div class='col-2'>";
            $array = array(array("tuser.id, AttendanceMaster.ADate", "Employee Code"), array("tuser.name, tuser.id, AttendanceMaster.ADate", "Employee Name - Code"), array("tuser.dept, tuser.id, AttendanceMaster.ADate", "Dept - Code"), array("tuser.company, tuser.dept, tuser.id, AttendanceMaster.ADate", "Div/Desg - Dept - Code"), array("tuser.company, tuser.dept, tuser.group_id, tuser.id, AttendanceMaster.ADate", "Div - Dept - Current Shift - Code"), array("AttendanceMaster.ADate", "Date"), array("AttendanceMaster.Day, tuser.id, AttendanceMaster.ADate", "Day"), array("AttendanceMaster.Week, tuser.id, AttendanceMaster.ADate", "Week"));
            displaySort($array, $lstSort, 8);
            print "</div>";
            print "</div>";
            print "<div class='row'>";
            print "<div class='col-12'>";
            print "<center><input name='btSearch' type='submit' value='Search Record' class='btn btn-primary'></center>";
            print "</div>";
            print "</div>";
        }
        ?>
        <!--</form>-->
        
    </div>
</div>
<?php
 } 
print "</div></div></div>";

if ($act == "searchRecord") {
    if ($excel != "yes") {
        print "<p align='center'><font face='Verdana' size='1'><b>" . $_SESSION[$session_variable . "FlagReportText"] . "</b></font></p>";
    }
    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, AttendanceMaster.ADate, AttendanceMaster.Day, AttendanceMaster.Week, AttendanceMaster.EarlyIn, AttendanceMaster.LateIn, AttendanceMaster.Break, AttendanceMaster.LessBreak, AttendanceMaster.MoreBreak, AttendanceMaster.EarlyOut, AttendanceMaster.LateOut, AttendanceMaster.Normal, AttendanceMaster.Grace, AttendanceMaster.Overtime, AttendanceMaster.AOvertime, AttendanceMaster.AttendanceID, tuser.idno, tuser.remark, AttendanceMaster.Flag, AttendanceMaster.NightFlag, tgroup.MoveNS FROM tuser, tgroup, AttendanceMaster WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.EmployeeID = tuser.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
    if ($lstShift != "") {
        $query = $query . " AND tgroup.id = " . $lstShift;
    }
    $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
    if ($lstWeek != "") {
        $query = $query . " AND AttendanceMaster.Week = " . $lstWeek;
    }
    if ($lstDay != "") {
        $query = $query . " AND AttendanceMaster.Day = '" . $lstDay . "'";
    }
    if ($txtFrom != "") {
        $query = $query . " AND AttendanceMaster.ADate >= " . insertDate($txtFrom);
    }
    if ($txtTo != "") {
        $query = $query . " AND AttendanceMaster.ADate <= " . insertDate($txtTo);
    }
    if ($lstColourFlag != "") {
        if ($lstColourFlag == "Black/Proxy") {
            $query = $query . " AND (AttendanceMaster.Flag = 'Black' OR AttendanceMaster.Flag = 'Proxy') ";
        } else {
            if ($lstColourFlag == "All w/o Black/Proxy") {
                $query = $query . " AND AttendanceMaster.Flag <> 'Black' AND AttendanceMaster.Flag <> 'Proxy'";
            } else {
                if ($lstColourFlag == "All w/o Proxy") {
                    $query = $query . " AND AttendanceMaster.Flag <> 'Proxy'";
                } else {
                    $query = $query . " AND AttendanceMaster.Flag = '" . $lstColourFlag . "'";
                }
            }
        }
    }
    if ($lstType != "") {
        if ($lstType == "Early In") {
            $query = $query . " AND AttendanceMaster.EarlyIn > 0 ";
        } else {
            if ($lstType == "Late In") {
                $query = $query . " AND AttendanceMaster.LateIn > 0 ";
            } else {
                if ($lstType == "Less Break") {
                    $query = $query . " AND AttendanceMaster.LessBreak > 0 ";
                } else {
                    if ($lstType == "More Break") {
                        $query = $query . " AND AttendanceMaster.MoreBreak > 0 ";
                    } else {
                        if ($lstType == "Early Out") {
                            $query = $query . " AND AttendanceMaster.EarlyOut > 0 ";
                        } else {
                            if ($lstType == "Late Out") {
                                $query = $query . " AND AttendanceMaster.LateOut > 0 ";
                            } else {
                                if ($lstType == "Grace") {
                                    $query = $query . " AND AttendanceMaster.Grace > 0 ";
                                } else {
                                    if ($lstType == "Normal = 0") {
                                        $query = $query . " AND AttendanceMaster.Normal <= 2 ";
                                    } else {
                                        if ($lstType == "Normal > 0") {
                                            $query = $query . " AND AttendanceMaster.Normal > 0 ";
                                        } else {
                                            if ($lstType == "OT > 0") {
                                                $query = $query . " AND AttendanceMaster.Overtime > 0 ";
                                            } else {
                                                if ($lstType == "AOT = 0") {
                                                    $query = $query . " AND AttendanceMaster.AOvertime = 0 ";
                                                } else {
                                                    if ($lstType == "AOT > 0") {
                                                        $query = $query . " AND AttendanceMaster.AOvertime > 0 ";
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
    $query = $query . employeeStatusQuery($lstEmployeeStatus);
    $query = $query . " ORDER BY " . $lstSort;
    print '<div class="row"><div class="col-md-12"><div class="card"><div class="card-body table-responsive">';
    if ($prints != "yes") {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800' class='table table-striped table-bordered dataTable'>";
    } else {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%' class='table table-striped table-bordered dataTable'>";
    }
    print "<thead><tr><td><font face='Verdana' size='2'>";
    if ($prints != "yes") {
        print "<input type='checkbox' name='chkDelete' onClick='javascript:checkAll()'>";
    } else {
        print "&nbsp;";
    }
    print "</font></td> <td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Shift</font></td> <td><font face='Verdana' size='2'>Date</font></td> <td><font face='Verdana' size='2'>Day</font></td> <td><font face='Verdana' size='2'>Week</font></td> <td><font face='Verdana' size='2'>Early In</font></td> <td><font face='Verdana' size='2'>Late In</font></td> <td><font face='Verdana' size='2'>Break</font></td> <td><font face='Verdana' size='2'>Less Break</font></td> <td><font face='Verdana' size='2'>More Break</font></td> <td><font face='Verdana' size='2'>Early Out</font></td> <td><font face='Verdana' size='2'>Late Out</font></td> <td><font face='Verdana' size='2'>Normal</font></td> <td><font face='Verdana' size='2'>Grace</font></td> <td><font face='Verdana' size='2'>OT</font></td> <td><font face='Verdana' size='2'>App OT</font></td></tr>";
    print "<tr><td><font face='Verdana' size='2'>&nbsp;</font></td><td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>Min</font></td> <td><font face='Verdana' size='2'>Min</font></td> <td><font face='Verdana' size='2'>Min</font></td> <td><font face='Verdana' size='2'>Min</font></td> <td><font face='Verdana' size='2'>Min</font></td> <td><font face='Verdana' size='2'>Min</font></td> <td><font face='Verdana' size='2'>Min</font></td> <td><font face='Verdana' size='2'>Hrs</font></td> <td><font face='Verdana' size='2'>Min</font></td> <td><font face='Verdana' size='2'>Hrs</font></td> <td><font face='Verdana' size='2'>Hrs</font></td></tr></thead>";
    $result = mysqli_query($iconn, $query);
    $count = 0;
    for ($bgcolor = "#FFFFFF"; $cur = mysqli_fetch_row($result); $count++) {
        if ($cur[3] == "") {
            $cur[3] = "&nbsp;";
        }
        if ($cur[20] == "") {
            $cur[20] = "&nbsp;";
        }
        if ($cur[21] == "") {
            $cur[21] = "&nbsp;";
        }
        if ($cur[22] == "Yellow") {
            $bgcolor = "Brown";
        } else {
            $bgcolor = "White";
        }
        print "<tr>";
        if ($prints != "yes") {
            print "<td bgcolor='" . $bgcolor . "'><input type='checkbox' name='chkDelete" . $count . "' id='chkDelete" . $count . "'> <input type='hidden' name='txhID" . $count . "' value='" . $cur[19] . "'> <input type='hidden' name='txhADate" . $count . "' value='" . $cur[5] . "'> <input type='hidden' name='txhEID" . $count . "' value='" . $cur[0] . "'> <input type='hidden' name='txhWeek" . $count . "' value='" . $cur[7] . "'> <input type='hidden' name='txhShift" . $count . "' value='" . $cur[23] . "'> <input type='hidden' name='txhMoveNS" . $count . "' value='" . $cur[24] . "'></td>";
        } else {
            print "<td bgcolor='" . $bgcolor . "'><font size='1'>&nbsp;</font> <input type='hidden' name='txhID" . $count . "' value='" . $cur[19] . "'></td>";
        }
        addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
        displayDate($cur[5]);
        round($cur[8] / 60, 2);
        round($cur[9] / 60, 2);
        round($cur[10] / 60, 2);
        round($cur[11] / 60, 2);
        round($cur[12] / 60, 2);
        round($cur[13] / 60, 2);
        round($cur[14] / 60, 2);
        round($cur[15] / 3600, 2);
        round($cur[16] / 60, 2);
        round($cur[17] / 3600, 2);
        round($cur[17] / 3600, 2);
        round($cur[18] / 3600, 2);
        print "<td bgcolor='" . $bgcolor . "'><a title='ID'><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'><font face='Verdana' size='1' color='" . $cur[22] . "'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Name'><font face='Verdana' size='1' color='" . $cur[22] . "'>" . $cur[1] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1' color='" . $cur[22] . "'>" . $cur[20] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Dept'><font face='Verdana' size='1' color='" . $cur[22] . "'>" . $cur[2] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Div/Desg'><font face='Verdana' size='1' color='" . $cur[22] . "'>" . $cur[3] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Rmk'><font face='Verdana' size='1' color='" . $cur[22] . "'>" . $cur[21] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Shift'><font face='Verdana' size='1' color='" . $cur[22] . "'>" . $cur[4] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Date'><font face='Verdana' size='1' color='" . $cur[22] . "'>" . displayDate($cur[5]) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Day'><font face='Verdana' size='1' color='" . $cur[22] . "'>" . $cur[6] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Week'><font face='Verdana' size='1' color='" . $cur[22] . "'>" . $cur[7] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Early In (Min)'><font face='Verdana' size='1' color='" . $cur[22] . "'>" . round($cur[8] / 60, 2) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Late In (Min)'><font face='Verdana' size='1' color='" . $cur[22] . "'>" . round($cur[9] / 60, 2) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Break (Min)'><font face='Verdana' size='1' color='" . $cur[22] . "'>" . round($cur[10] / 60, 2) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Less Break (Min)'><font face='Verdana' size='1' color='" . $cur[22] . "'>" . round($cur[11] / 60, 2) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='More Break (Min)'><font face='Verdana' size='1' color='" . $cur[22] . "'>" . round($cur[12] / 60, 2) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Early Out (Min)'><font face='Verdana' size='1' color='" . $cur[22] . "'>" . round($cur[13] / 60, 2) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Late Out (Min)'><font face='Verdana' size='1' color='" . $cur[22] . "'>" . round($cur[14] / 60, 2) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Normal (Hrs)'><font face='Verdana' size='1' color='" . $cur[22] . "'>" . round($cur[15] / 3600, 2) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Grace (Min)'><font face='Verdana' size='1' color='" . $cur[22] . "'>" . round($cur[16] / 60, 2) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='OT (Hrs)'><font face='Verdana' size='1' color='" . $cur[22] . "'>" . round($cur[17] / 3600, 2) . "</font><input type='hidden' name='txhAOT" . $count . "' id='txhAOT" . $count . "' value='" . round($cur[17] / 3600, 2) . "'></td> <td bgcolor='" . $bgcolor . "'><a title='Approved OT (Hrs)'><font face='Verdana' size='1' color='" . $cur[22] . "'>" . round($cur[18] / 3600, 2) . "</td> </tr>";
    }
    print "</table>";
    print "</div></div></div></div>";
    if ($excel != "yes") {
        print "<p align='center'><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $count . "</b></font><input type='hidden' name='txtCount' value='" . $count . "'>";
        if ($prints != "yes" && strpos($userlevel, $current_module . "D") !== false && $_SESSION[$session_variable . "LockDate"] < insertDate($txtFrom) && $_SESSION[$session_variable . "LockDate"] < insertDate($txtTo)) {
            print "<br><input name='btSubmit' type='button' value='Delete Selected Record(s)' onClick='checkSubmit()' class='btn btn-primary'>";
        }
        if ($prints != "yes") {
            print "<br><br><input type='button' value='Print Report' onClick='checkPrint(0)' class='btn btn-primary'>&nbsp;&nbsp;<input type='button' value='Excel' onClick='checkPrint(1)' class='btn btn-primary'>";
        }
        print "</p>";
    }
}
print "</form>";
print "</div>";
include 'footer.php';
echo "\r\n<script>\r\nfunction checkSubmit(){\t\r\n\tif (confirm('Are you sure you want to DELETE the selected Record(s)')){\r\n\t\tx = document.frm1;\r\n\t\tx.target = '_self';\r\n\t\tx.act.value='deleteRecord';\r\n\t\tx.btSubmit.disabled = true;\r\n\t\tx.submit();\r\n\t}\t\r\n}\r\n\r\nfunction checkDelete(x, y, z){\r\n\tif (x.checked == true){\r\n\t\tif (y.value == 0 || y.value == ''){\r\n\t\t\ty.value = z.value;\r\n\t\t}\r\n\t}else{\r\n\t\ty.value = 0;\t\r\n\t}\r\n}\r\n\r\nfunction checkAll(){\t\r\n\tx = document.frm1;\r\n\ty = x.chkDelete;\r\n\tz = x.txtCount.value;\t\r\n\tfor (i=0;i<z;i++){\t\t\r\n\t\tif (y.checked == true){\t\t\t\r\n\t\t\tdocument.getElementById(\"chkDelete\"+i).checked = true;\t\t\t\r\n\t\t}else{\r\n\t\t\tdocument.getElementById(\"chkDelete\"+i).checked = false;\r\n\t\t}\r\n\t}\r\n}\r\n</script>";

?>