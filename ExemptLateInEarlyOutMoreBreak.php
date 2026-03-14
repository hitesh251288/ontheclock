<?php


ob_start("ob_gzhandler");
error_reporting(E_ERROR);
set_time_limit(0);
include "Functions.php";
$current_module = "29";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=ExemptLateInEarlyOutMoreBreak.php&message=Session Expired or Security Policy Violated");
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
    $message = "Exempt LateIn/ EarlyOut/ MoreBreak";
}
$lstShift = $_POST["lstShift"];
$lstDepartment = $_POST["lstDepartment"];
$lstDivision = $_POST["lstDivision"];
$lstWeek = $_POST["lstWeek"];
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
$lstEmployeeStatus = "";
if (isset($_POST["lstEmployeeStatus"])) {
    $lstEmployeeStatus = $_POST["lstEmployeeStatus"];
} else {
    $lstEmployeeStatus = "ACT";
}
$lstType = $_POST["lstType"];
if ($lstType == "") {
    $lstType = "";
}
$lstSort = $_POST["lstSort"];
$txtRemark = $_POST["txtRemark"];
$txtMinutes = $_POST["txtMinutes"];
if ($txtMinutes != "") {
    if (!is_numeric($txtMinutes)) {
        $txtMinutes = 120;
    }
}
$txtPhone = $_POST["txtPhone"];
$txtARemark = $_POST["txtARemark"];
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

if ($act == "exemptLateIn" || $act == "exemptEarlyOut" || $act == "exemptMoreBreak") {
    $count = $_POST["txtCount"];
    for ($i = 0; $i < $count; $i++) {
        if ($act == "exemptLateIn" && 0 < $_POST["txtLateIn" . $i] && $_POST["chkAOT" . $i] != "") {
            $break = 0;
            if ($_POST["txtBreak" . $i] == 0 && (0 < $_POST["txhFlexiBreak" . $i] || $_POST["txhBreakFrom" . $i] != "" && $_POST["txhBreakTo" . $i] != "")) {
                if (0 < $_POST["txhFlexiBreak" . $i]) {
                    $break = $_POST["txhFlexiBreak" . $i] * 60;
                } else {
                    $ibout = mktime(substr($_POST["txhBreakFrom" . $i], 0, 2), substr($_POST["txhBreakFrom" . $i], 2, 2), 0, 1, 1, 2001);
                    if ($_POST["txhNightFlag" . $i] == 0) {
                        $ibin = mktime(substr($_POST["txhBreakTo" . $i], 0, 2), substr($_POST["txhBreakTo" . $i], 2, 2), 0, 1, 1, 2001);
                    } else {
                        $ibin = mktime(substr($_POST["txhBreakTo" . $i], 0, 2), substr($_POST["txhBreakTo" . $i], 2, 2), 0, 1, 2, 2001);
                    }
                    $break = $ibin - $ibout;
                }
            }
            if (0 < $_POST["txtOT" . $i]) {
                $query = "UPDATE AttendanceMaster SET Remark = '" . $_POST["txtARemark" . $i] . "', Overtime = (Overtime + LateIn - " . $break . "), Break = " . $break . ", LateIn_flag = 1 WHERE LateIn_flag = 0 AND AttendanceID = " . $_POST["txhID" . $i];
                updateIData($iconn, $query, true);
            } else {
                if ($_POST["txtLateIn" . $i] <= $_POST["txtGroupSec" . $i] - $_POST["txtNormal" . $i]) {
                    $query = "UPDATE AttendanceMaster SET Remark = '" . $_POST["txtARemark" . $i] . "', Normal = (Normal + LateIn - " . $break . "), Break = " . $break . ", LateIn_flag = 1 WHERE LateIn_flag = 0 AND AttendanceID = " . $_POST["txhID" . $i];
                    updateIData($iconn, $query, true);
                } else {
                    $query = "UPDATE AttendanceMaster SET Remark = '" . $_POST["txtARemark" . $i] . "', Normal = " . $_POST["txtGroupSec" . $i] . ", Break = " . $break . ", Overtime = " . ($_POST["txtLateIn" . $i] - ($_POST["txtGroupSec" . $i] - $_POST["txtNormal" . $i]) - $break) . ", LateIn_flag = 1 WHERE LateIn_flag = 0 AND AttendanceID = " . $_POST["txhID" . $i];
                    updateIData($iconn, $query, true);
                }
            }
            $text = "Exempted LATE IN for ID: " . $_POST["txh" . $i] . " - on " . displayDate($_POST["txhDate" . $i]) . ", Remark = " . $_POST["txtARemark" . $i];
            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
            updateIData($iconn, $query, true);
        } else {
            if ($act == "exemptEarlyOut" && 0 < $_POST["txtEarlyOut" . $i] && $_POST["chkAOT" . $i] != "") {
                $break = 0;
                if ($_POST["txtBreak" . $i] == 0 && (0 < $_POST["txhFlexiBreak" . $i] || $_POST["txhBreakFrom" . $i] != "" && $_POST["txhBreakTo" . $i] != "")) {
                    if (0 < $_POST["txhFlexiBreak" . $i]) {
                        $break = $_POST["txhFlexiBreak" . $i] * 60;
                    } else {
                        $ibout = mktime(substr($_POST["txhBreakFrom" . $i], 0, 2), substr($_POST["txhBreakFrom" . $i], 2, 2), 0, 1, 1, 2001);
                        if ($_POST["txhNightFlag" . $i] == 0) {
                            $ibin = mktime(substr($_POST["txhBreakTo" . $i], 0, 2), substr($_POST["txhBreakTo" . $i], 2, 2), 0, 1, 1, 2001);
                        } else {
                            $ibin = mktime(substr($_POST["txhBreakTo" . $i], 0, 2), substr($_POST["txhBreakTo" . $i], 2, 2), 0, 1, 2, 2001);
                        }
                        $break = $ibin - $ibout;
                    }
                }
                if (0 < $_POST["txtOT" . $i]) {
                    $query = "UPDATE AttendanceMaster SET Remark = '" . $_POST["txtARemark" . $i] . "', Overtime = (Overtime + EarlyOut - " . $break . "), Break = " . $break . ", EarlyOut_flag = 1 WHERE EarlyOut_flag = 0 AND AttendanceID = " . $_POST["txhID" . $i];
                    updateIData($iconn, $query, true);
                } else {
                    if ($_POST["txtEarlyOut" . $i] <= $_POST["txtGroupSec" . $i] - $_POST["txtNormal" . $i]) {
                        $query = "UPDATE AttendanceMaster SET Remark = '" . $_POST["txtARemark" . $i] . "', Normal = (Normal + EarlyOut - " . $break . "), Break = " . $break . ", EarlyOut_flag = 1 WHERE EarlyOut_flag = 0 AND AttendanceID = " . $_POST["txhID" . $i];
                        updateIData($iconn, $query, true);
                    } else {
                        $query = "UPDATE AttendanceMaster SET Remark = '" . $_POST["txtARemark" . $i] . "', Normal = " . $_POST["txtGroupSec" . $i] . ", Break = " . $break . ", Overtime = " . ($_POST["txtEarlyOut" . $i] - ($_POST["txtGroupSec" . $i] - $_POST["txtNormal" . $i]) - $break) . ", EarlyOut_flag = 1 WHERE EarlyOut_flag = 0 AND AttendanceID = " . $_POST["txhID" . $i];
                        updateIData($iconn, $query, true);
                    }
                }
                $text = "Exempted EARLY OUT for ID: " . $_POST["txh" . $i] . " - on " . displayDate($_POST["txhDate" . $i]) . ", Remark = " . $_POST["txtARemark" . $i];
                $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                updateIData($iconn, $query, true);
            } else {
                if ($act == "exemptMoreBreak" && 0 < $_POST["txtMoreBreak" . $i] && $_POST["chkAOT" . $i] != "") {
                    if (0 < $_POST["txtOT" . $i]) {
                        $query = "UPDATE AttendanceMaster SET Remark = '" . $_POST["txtARemark" . $i] . "', Overtime = (Overtime + MoreBreak), MoreBreak_flag = 1 WHERE MoreBreak_flag = 0 AND AttendanceID = " . $_POST["txhID" . $i];
                        updateIData($iconn, $query, true);
                    } else {
                        if ($_POST["txtMoreBreak" . $i] <= $_POST["txtGroupSec" . $i] - $_POST["txtNormal" . $i]) {
                            $query = "UPDATE AttendanceMaster SET Remark = '" . $_POST["txtARemark" . $i] . "', Normal = (Normal + MoreBreak), MoreBreak_flag = 1 WHERE MoreBreak_flag = 0 AND AttendanceID = " . $_POST["txhID" . $i];
                            updateIData($iconn, $query, true);
                        } else {
                            $query = "UPDATE AttendanceMaster SET Remark = '" . $_POST["txtARemark" . $i] . "', Normal = " . $_POST["txtGroupSec" . $i] . ", Overtime = " . ($_POST["txtMoreBreak" . $i] - ($_POST["txtGroupSec" . $i] - $_POST["txtNormal" . $i])) . ", MoreBreak_flag = 1 WHERE MoreBreak_flag = 0 AND AttendanceID = " . $_POST["txhID" . $i];
                            updateIData($iconn, $query, true);
                        }
                    }
                    $text = "Exempted MORE BREAK for ID: " . $_POST["txh" . $i] . " - on " . displayDate($_POST["txhDate" . $i]) . ", Remark = " . $_POST["txtARemark" . $i];
                    $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                    updateIData($iconn, $query, true);
                }
            }
        }
    }
    $message = "Record(s) saved Successfully";
    $act = "searchRecord";
}
if ($prints != "yes") {
    include 'header.php';
?>
<div class="page-breadcrumb">
                    <div class="row">
                        <div class="col-12 d-flex no-block align-items-center">
                            <h4 class="page-title">Exempt LateIn/ EarlyOut/ MoreBreak</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Exempt LateIn/ EarlyOut/ MoreBreak
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

<?php
}
print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='ExemptLateInEarlyOutMoreBreak.php'><input type='hidden' name='act' value='searchRecord'>";
//print "<html><title>Exempt LateIn/ EarlyOut/ MoreBreak</title>";
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
        header("Content-Disposition: attachment; filename=ExemptLateInEarlyOutMoreBreak.xls");
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
            print "<center><h4 class='card-title'>Select ONE or MORE options and click Search Record</h4></center>";
//            print "<table width='800' cellpadding='1' cellspacing='-1'>";
        //    print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Select ONE or MORE options and click 'Search Record'</b></font></td></tr>";
        } else {
            if ($excel != "yes") {
//                print "<table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
//                print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Selected Options</b></font></td></tr>";
            }
        }
        
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
                displayTextbox("txtFrom", "Date From <font size='1'>(DD/MM/YYYY)</font>: ", $txtFrom, $prints, 12, "25%", "25%");
                ?>
            </div>
            <div class="col-2">
                <?php 
                displayTextbox("txtTo", "Date To <font size='1'>(DD/MM/YYYY)</font>: ", $txtTo, $prints, 12, "25%", "25%");
                ?>
            </div>
            <div class="col-2">
                <?php 
                print "<label class='form-label'>Display Record(s) less than:</label><input size='5' name='txtMinutes' value='" . $txtMinutes . "' class='form-select select2 shadow-none'>&nbsp;<font face='Verdana' size='2'>Minutes</font>";
                ?>
            </div>
            <div class="col-2">
                <?php 
                displayTextbox("txtARemark", "Attendance Remark: ", $txtARemark, $prints, 12, "25%", "25%");
                ?>
            </div>
            
        <?php 
       if ($prints != "yes") {
            print "<div class='col-2'>";
            print "<label class='form-label'>Work Type:</label><select name='lstType' class='form-select select2 shadow-none'><option selected value='" . $lstType . "'>" . $lstType . "</option> <option value='Late In'>Late In</option> <option value='More Break'>More Break</option> <option value='Early Out'>Early Out</option> </select>";
            print "</div>";
            print "<div class='col-2'>";
            displayEmployeeStatus($conn, "lstEmployeeStatus", $lstEmployeeStatus, "25%", "75%");
            print "</div>";
            print "</div>";
            print "<div class='row'>";
            print "<div class='col-2'>";
            print "<label class='form-label'>Sort By:</label><select name='lstSort' class='form-select select2 shadow-none'><option selected value='tuser.id, AttendanceMaster.ADate'>Employee Code</option> <option value='tuser.name, tuser.id, AttendanceMaster.ADate'>Employee Name</option> <option value='tuser.dept, tuser.id, AttendanceMaster.ADate'>Department</option> <option value='tuser.company, tuser.id, AttendanceMaster.ADate'>Div/Desg</option> <option value='AttendanceMaster.ADate'>Date</option> <option value='AttendanceMaster.Day, tuser.id, AttendanceMaster.ADate'>Day</option> <option value='AttendanceMaster.Week, tuser.id, AttendanceMaster.ADate'>Week</option> </select>";
            print "</div>";
            print "</div>";
            print "<div class='row'>";
            print "<div class='col-12'>";
            print "<center><input name='btSearch' type='submit' value='Search Record' class='btn btn-primary'></center>";
            print "</div>";
            print "</div>";
        }
        ?>        
    </div>
</div>
<?php
 } 
print "</div></div></div></div>";

if ($act == "searchRecord") {
    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, AttendanceMaster.ADate, AttendanceMaster.Day, AttendanceMaster.Week, AttendanceMaster.EarlyIn, AttendanceMaster.LateIn, AttendanceMaster.Break, AttendanceMaster.LessBreak, AttendanceMaster.MoreBreak, AttendanceMaster.EarlyOut, AttendanceMaster.LateOut, AttendanceMaster.Normal, AttendanceMaster.Grace, AttendanceMaster.Overtime, AttendanceMaster.AOvertime, AttendanceMaster.AttendanceID, tuser.idno, tuser.remark, AttendanceMaster.LateIn_flag, AttendanceMaster.EarlyOut_flag, AttendanceMaster.MoreBreak_flag, tgroup.WorkMin, tgroup.FlexiBreak, tgroup.BreakFrom, tgroup.BreakTo, tgroup.NightFlag, AttendanceMaster.Remark, AttendanceMaster.Flag FROM tuser, tgroup, AttendanceMaster WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.Flag NOT LIKE 'Delete' AND AttendanceMaster.EmployeeID = tuser.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
    if ($lstShift != "") {
        $query = $query . " AND tgroup.id = " . $lstShift;
    }
    if ($txtARemark != "") {
        $query = $query . " AND AttendanceMaster.Remark LIKE '%" . $txtARemark . "%'";
    }
    $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
    if ($lstWeek != "") {
        $query = $query . " AND AttendanceMaster.Week = " . $lstWeek;
    }
    if ($txtFrom != "") {
        $query = $query . " AND AttendanceMaster.ADate >= " . insertDate($txtFrom);
    }
    if ($txtTo != "") {
        $query = $query . " AND AttendanceMaster.ADate <= " . insertDate($txtTo);
    }
    if ($lstType != "") {
        if ($txtMinutes == "") {
            $txtMinutes = 9999999999.0;
        } else {
            $txtMinutes = $txtMinutes * 60;
        }
        if ($lstType == "Late In") {
            $query = $query . " AND AttendanceMaster.LateIn > 0 AND AttendanceMaster.LateIn < " . $txtMinutes;
        } else {
            if ($lstType == "More Break") {
                $query = $query . " AND AttendanceMaster.MoreBreak > 0 AND AttendanceMaster.MoreBreak < " . $txtMinutes;
            } else {
                if ($lstType == "Early Out") {
                    $query = $query . " AND AttendanceMaster.EarlyOut > 0 AND AttendanceMaster.EarlyOut < " . $txtMinutes;
                }
            }
        }
    }
    $query = $query . employeeStatusQuery($lstEmployeeStatus);
    if ($lstSort != "") {
        $query = $query . " ORDER BY " . $lstSort;
    } else {
        $query = $query . " ORDER BY tuser.id, AttendanceMaster.ADate";
    }
    print '<div class="row"><div class="col-md-12"><div class="card"><div class="card-body table-responsive">';
    if ($prints != "yes") {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800' class='table table-striped table-bordered dataTable'>";
    } else {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%' class='table table-striped table-bordered dataTable'>";
    }
    print "<tr> <td><font face='Verdana' size='2'>";
    if ($prints != "yes") {
        print "<input type='checkbox' name='chkAOT' onClick='javascript:approveAll()'>";
    } else {
        print "&nbsp;";
    }
    print "</font></td>";
    print "<td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Shift</font></td> <td><font face='Verdana' size='2'>Date</font></td> <td><font face='Verdana' size='2'>Day</font></td> <td><font face='Verdana' size='2'>Week</font></td> <td><font face='Verdana' size='2'>Early In</font></td> <td><font face='Verdana' size='2'>Late In</font></td> <td><font face='Verdana' size='2'>Break</font></td> <td><font face='Verdana' size='2'>Less Break</font></td> <td><font face='Verdana' size='2'>More Break</font></td> <td><font face='Verdana' size='2'>Early Out</font></td> <td><font face='Verdana' size='2'>Late Out</font></td> <td><font face='Verdana' size='2'>Grace</font></td> <td><font face='Verdana' size='2'>Normal</font></td> <td><font face='Verdana' size='2'>OT</font></td> <td><font face='Verdana' size='2'>App OT</font></td> <td><font face='Verdana' size='2'>Flag</font></td> <td><a name='copyRemarkAll' href='#copyRemarkAll' onClick='javascript:copyRemarkAll()' title='Click Here to COPY the Remark from FIRST Row to all the below BLANK Remarks'><font face='Verdana' size='2'>A Remark</font></a></td> </tr>";
    print "<tr> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>Min</font></td> <td><font face='Verdana' size='2'>Min</font></td> <td><font face='Verdana' size='2'>Min</font></td> <td><font face='Verdana' size='2'>Min</font></td> <td><font face='Verdana' size='2'>Min</font></td> <td><font face='Verdana' size='2'>Min</font></td> <td><font face='Verdana' size='2'>Min</font></td> <td><font face='Verdana' size='2'>Min</font></td> <td><font face='Verdana' size='2'>Hrs</font></td> <td><font face='Verdana' size='2'>Min</font></td> <td><font face='Verdana' size='2'>Min</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><a name='resetRemarkAll' href='#resetRemarkAll' onClick='javascript:resetRemarkAll()' title='Click Here to RESET the Remark of all Rows'><font face='Verdana' size='1'>Reset</font></a></td> </tr>";
    $result = mysqli_query($conn, $query);
    for ($count = 0; $cur = mysqli_fetch_row($result); $count++) {
        if ($cur[3] == "") {
            $cur[3] = "&nbsp;";
        }
        if ($cur[20] == "") {
            $cur[20] = "&nbsp;";
        }
        if ($cur[21] == "") {
            $cur[21] = "&nbsp;";
        }
        if ($prints != "yes") {
            print "<tr><td><input type='checkbox' name='chkAOT" . $count . "' id='chkAOT" . $count . "'> <input type='hidden' name='txhID" . $count . "' value='" . $cur[19] . "'> <input type='hidden' name='txtOT" . $count . "' value='" . $cur[17] . "'> <input type='hidden' name='txtNormal" . $count . "' value='" . $cur[15] . "'> <input type='hidden' name='txtLateIn" . $count . "' value='" . $cur[9] . "'> <input type='hidden' name='txtMoreBreak" . $count . "' value='" . $cur[12] . "'> <input type='hidden' name='txtEarlyOut" . $count . "' value='" . $cur[13] . "'> <input type='hidden' name='txtGroupSec" . $count . "' value='" . $cur[25] * 60 . "'> <input type='hidden' name='txtBreak" . $count . "' value='" . $cur[10] . "'> <input type='hidden' name='txhFlexiBreak" . $count . "' value='" . $cur[26] . "'> <input type='hidden' name='txhBreakFrom" . $count . "' value='" . $cur[27] . "'> <input type='hidden' name='txhBreakTo" . $count . "' value='" . $cur[28] . "'> <input type='hidden' name='txhNightFlag" . $count . "' value='" . $cur[29] . "'></td> ";
        } else {
            print "<tr><td><font size='1'>&nbsp;</font></td>";
        }
        addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
        displayDate($cur[5]);
        round($cur[8] / 60, 2);
        print "<td><a title='ID'><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'> <input type='hidden' name='txhLateIn" . $count . "' value='" . $cur[22] . "'> <input type='hidden' name='txhEarlyOut" . $count . "' value='" . $cur[23] . "'> <input type='hidden' name='txhMoreBreak" . $count . "' value='" . $cur[24] . "'> <font face='Verdana' size='1'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $cur[1] . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $cur[20] . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $cur[2] . "</font></a></td> <td><a title='Div/Desg'><font face='Verdana' size='1'>" . $cur[3] . "</font></a></td> <td><a title='Rmk'><font face='Verdana' size='1'>" . $cur[21] . "</font></a></td> <td><a title='Shift'><font face='Verdana' size='1'>" . $cur[4] . "</font></a></td> <td><a title='Date'><font face='Verdana' size='1'>" . displayDate($cur[5]) . "</font></a></td> <td><a title='Day'><font face='Verdana' size='1'>" . $cur[6] . "</font></a></td> <td><a title='Week'><font face='Verdana' size='1'>" . $cur[7] . "</font></a></td> <td><a title='Early In (Min)'><font face='Verdana' size='1'>" . round($cur[8] / 60, 2) . "</font></a></td>";
        if ($cur[22] == 0) {
            round($cur[9] / 60, 2);
            print "<td><a title='Late In (Min)'><font face='Verdana' size='1'>" . round($cur[9] / 60, 2) . "</font></a></td>";
        } else {
            round($cur[9] / 60, 2);
            print "<td><a title='Late In (Min)'><font face='Verdana' size='1' color='#FF0000'><strike>" . round($cur[9] / 60, 2) . "</strike></font></a></td>";
        }
        round($cur[10] / 60, 2);
        round($cur[11] / 60, 2);
        print "<td><a title='Break (Min)'><font face='Verdana' size='1'>" . round($cur[10] / 60, 2) . "</font></a></td> <td><a title='Less Break (Min)'><font face='Verdana' size='1'>" . round($cur[11] / 60, 2) . "</font></a></td>";
        if ($cur[24] == 0) {
            round($cur[12] / 60, 2);
            print "<td><a title='More Break (Min)'><font face='Verdana' size='1'>" . round($cur[12] / 60, 2) . "</font></a></td>";
        } else {
            round($cur[12] / 60, 2);
            print "<td><a title='More Break (Min)'><font face='Verdana' size='1' color='#FF0000'><strike>" . round($cur[12] / 60, 2) . "</strike></font></a></td>";
        }
        if ($cur[23] == 0) {
            round($cur[13] / 60, 2);
            print "<td><a title='Early Out (Min)'><font face='Verdana' size='1'>" . round($cur[13] / 60, 2) . "</font></a></td>";
        } else {
            round($cur[13] / 60, 2);
            print "<td><a title='Early Out (Min)'><font face='Verdana' size='1' color='#FF0000'><strike>" . round($cur[13] / 60, 2) . "</strike></font></a></td>";
        }
        round($cur[14] / 60, 2);
        round($cur[16] / 60, 2);
        round($cur[15] / 3600, 2);
        round($cur[17] / 60, 2);
        round($cur[17] / 60, 2);
        round($cur[18] / 60, 2);
        print "<td><a title='Late Out (Min)'><font face='Verdana' size='1'>" . round($cur[14] / 60, 2) . "</font></a></td> <td><a title='Grace (Min)'><font face='Verdana' size='1'>" . round($cur[16] / 60, 2) . "</font></a></td> <td><a title='Normal (Hrs)'><font face='Verdana' size='1'>" . round($cur[15] / 3600, 2) . "</font></a></td> <td><a title='OT (Min)'><font face='Verdana' size='1'>" . round($cur[17] / 60, 2) . "</font><input type='hidden' name='txhAOT" . $count . "' id='txhAOT" . $count . "' value='" . round($cur[17] / 60, 2) . "'></td> <td><a title='Approved OT (Min)'><font face='Verdana' size='1'>" . round($cur[18] / 60, 2) . "</font></td> <td><a title='Flag'><font face='Verdana' size='1'>" . $cur[31] . "</font></td>";
        if ($prints == "yes") {
            print "<td><a title='Remark'><font face='Verdana' size='1'>" . $cur[30] . "</font></td>";
        } else {
            print "<td><a title='Remark'><input size='12' name='txtARemark" . $count . "' value='" . $cur[30] . "' class='form-control'></td>";
        }
        print "<input type='hidden' name='txhDate" . $count . "' value='" . $cur[5] . "'></tr>";
    }
    print "</table>";
    if ($excel != "yes") {
        print "<p align='center'><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $count . "</b></font><input type='hidden' name='txtCount' value='" . $count . "'>";
        if ($prints != "yes" && strpos($userlevel, $current_module . "E") !== false && $_SESSION[$session_variable . "LockDate"] < insertDate($txtFrom) && $_SESSION[$session_variable . "LockDate"] < insertDate($txtTo)) {
            print "<br><input name='btExemptLateIn' type='button' value='Exempt Late In' class='btn btn-primary' onClick='submitRecord(0)'> &nbsp;&nbsp;<input name='btExemptEarlyOut' type='button' class='btn btn-primary' value='Exempt Early Out' onClick='submitRecord(1)'> &nbsp;&nbsp;<input name='btExemptMoreBreak' class='btn btn-primary' type='button' value='Exempt More Break' onClick='submitRecord(2)'>";
        }
        if ($prints != "yes") {
            print "<br><br><input type='button' value='Print Report' onClick='checkPrint(0)' class='btn btn-primary'>&nbsp;&nbsp;<input type='button' value='Excel' onClick='checkPrint(1)' class='btn btn-primary'>";
        }
        print "</p>";
    }
}
print "</form>";
print "</div></div></div></div></div>";
include 'footer.php';
echo "\r\n<script>\r\nfunction submitRecord(a){\r\n\tx = document.frm1;\t\r\n\tif (a == 0){\r\n\t\tif (confirm(\"Exempt LATE IN for Selected Employees. This Process CANNOT be Reversed\")){\t\t\t\r\n\t\t\tx.act.value='exemptLateIn';\r\n\t\t\tx.btExemptLateIn.disabled = true;\r\n\t\t\tx.submit();\r\n\t\t}\r\n\t}else if (a == 1){\r\n\t\tif (confirm(\"Exempt EARLY OUT for Selected Employees. This Process CANNOT be Reversed\")){\r\n\t\t\tx.act.value='exemptEarlyOut';\r\n\t\t\tx.btExemptEarlyOut.disabled = true;\r\n\t\t\tx.submit();\r\n\t\t}\r\n\t}else if (a == 2){\r\n\t\tif (confirm(\"Exempt MORE BREAK for Selected Employees. This Process CANNOT be Reversed\")){\r\n\t\t\tx.act.value='exemptMoreBreak';\r\n\t\t\tx.btExemptMoreBreak.disabled = true;\r\n\t\t\tx.submit();\r\n\t\t}\r\n\t}\r\n}\r\n\r\nfunction approveOT(x, y, z){\r\n\tif (x.checked == true){\r\n\t\tif (y.value == 0 || y.value == ''){\r\n\t\t\ty.value = z.value;\r\n\t\t}\r\n\t}else{\r\n\t\ty.value = 0;\t\r\n\t}\r\n}\r\n\r\nfunction checkOTValue(x){\r\n\tif (x.value == '' || x.value*1 != x.value/1 || x.value*1 > 1440){\r\n\t\talert('Please enter a valid Approved OT Value');\r\n\t\tx.focus();\r\n\t\treturn;\r\n\t}\r\n}\r\n\r\nfunction approveAll(){\t\r\n\tx = document.frm1;\r\n\ty = x.chkAOT;\r\n\tz = x.txtCount.value;\t\r\n\tfor (i=0;i<z;i++){\t\t\r\n\t\tif (y.checked == true){\t\t\t\r\n\t\t\tdocument.getElementById(\"chkAOT\"+i).checked = true;\t\t\t\r\n\t\t}else{\r\n\t\t\tdocument.getElementById(\"chkAOT\"+i).checked = false;\t\t\t\r\n\t\t}\r\n\t}\r\n}\r\n\r\nfunction copyRemarkAll(){\r\n\tif (confirm(\"COPY Attendance Remark from FIRST row to all other BLANK Remark Fields\")){\r\n\t\tvar x = document.frm1;\r\n\t\tvar count = x.txtCount.value;\r\n\t\tif (count > 0){\r\n\t\t\tif (x.txtARemark0.value != \"\"){\r\n\t\t\t\tfor (i=0;i<count;i++){\r\n\t\t\t\t\tif (document.getElementById(\"txtARemark\"+i).value == \"\" || document.getElementById(\"txtARemark\"+i).value == \".\"){\r\n\t\t\t\t\t\tdocument.getElementById(\"txtARemark\"+i).value = x.txtARemark0.value;\r\n\t\t\t\t\t}\r\n\t\t\t\t}\r\n\t\t\t}\r\n\t\t}\r\n\t}\r\n}\r\n\r\nfunction resetRemarkAll(){\r\n\tif (confirm(\"Reset All Attendance Remarks\")){\r\n\t\tvar x = document.frm1;\r\n\t\tvar count = x.txtCount.value;\t\r\n\t\tfor (i=0;i<count;i++){\r\n\t\t\tdocument.getElementById(\"txtARemark\"+i).value = \"\";\r\n\t\t}\r\n\t}\t\r\n}\r\n</script>\r\n</center>";

?>