<?php


ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "21";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=ReportWork.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$act = $_GET["act"];
if ($act == "") {
    $act = $_POST["act"];
}
$prints = $_GET["prints"];
$excel = $_GET["excel"];
$subReport = $_GET["subReport"];
$message = $_GET["message"];
if ($message == "") {
    $message = "Work Report";
}
$lstShift = $_POST["lstShift"];
$lstDepartment = $_GET["lstDepartment"];
if ($lstDepartment == "") {
    $lstDepartment = $_POST["lstDepartment"];
}
$lstDivision = $_GET["lstDivision"];
if ($lstDivision == "") {
    $lstDivision = $_POST["lstDivision"];
}
$lstWeek = $_POST["lstWeek"];
$lstDay = $_POST["lstDay"];
$lstEmployeeID = $_GET["lstEmployeeID"];
$lstEmployeeIDFrom = $_POST["lstEmployeeIDFrom"];
$lstEmployeeIDTo = $_POST["lstEmployeeIDTo"];
$txtEmployeeCode = $_POST["txtEmployeeCode"];
if ($lstEmployeeID != "" && $lstEmployeeIDFrom == "" && $lstEmployeeIDTo == "") {
    $lstEmployeeIDFrom = $lstEmployeeID;
    $lstEmployeeIDTo = $lstEmployeeID;
}
$lstSort = $_POST["lstSort"];
if ($lstSort == "") {
    $lstSort = $_GET["lstSort"];
}
$txtEmployee = $_POST["txtEmployee"];
$lstColourFlag = $_POST["lstColourFlag"];
if ($lstColourFlag == "") {
    $lstColourFlag = $_GET["lstColourFlag"];
}
$txtFrom = $_POST["txtFrom"];
$txtTo = $_POST["txtTo"];
if ($txtFrom == "") {
    $txtFrom = $_GET["txtFrom"];
}
if ($txtTo == "") {
    $txtTo = $_GET["txtTo"];
}
$lstRemark = $_POST["lstRemark"];
if ($lstRemark == "") {
    $lstRemark = "Yes";
}
$txtARemark = $_POST["txtARemark"];
$lstSummary = $_POST["lstSummary"];
if ($lstSummary == "") {
    $lstSummary = $_GET["lstSummary"];
}
if ($lstSummary == "") {
    $lstSummary = "No";
}
$lstIPEL = $_POST["lstIPEL"];
if ($lstIPEL == "") {
    $lstIPEL = $_GET["lstIPEL"];
}
if ($lstIPEL == "") {
    $lstIPEL = "No";
}
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
$txtRemark = $_GET["txtRemark"];
if ($txtRemark == "") {
    $txtRemark = $_POST["txtRemark"];
}
$txtPhone = $_GET["txtPhone"];
if ($txtPhone == "") {
    $txtPhone = $_POST["txtPhone"];
}
$txtSNo = $_GET["txtSNo"];
if ($txtSNo == "") {
    $txtSNo = $_POST["txtSNo"];
}
$lstGroup = $_GET["lstGroup"];
if ($lstGroup == "") {
    $lstGroup = $_POST["lstGroup"];
}
$lstType = $_POST["lstType"];
if ($lstType == "") {
    $lstType = "";
}
$lstEmployeeStatus = "";
if (isset($_POST["lstEmployeeStatus"])) {
    $lstEmployeeStatus = $_POST["lstEmployeeStatus"];
} else {
    if (isset($_GET["lstEmployeeStatus"])) {
        $lstEmployeeStatus = $_GET["lstEmployeeStatus"];
    } else {
        $lstEmployeeStatus = "ACT";
    }
}
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
                            <h4 class="page-title">Work Report</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Work Report
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

<?php
}
print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='ReportWork.php'><input type='hidden' name='act' value='searchRecord'>";
//print "<html><title>Work Report</title>";
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
        if ($subReport != "yes") {
            header("Content-type: application/x-msdownload");
            header("Content-Disposition: attachment; filename=ReportWork.xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            print "<body>";
        }
    }
}

if ($excel != "yes") {
    
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
    //        print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Select ONE or MORE options and click 'Search Record'</b></font></td></tr>";
        } else {
//            print "<table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
//            print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Selected Options</b></font></td></tr>";
        }
        
        ?>
        <div class="row">
            <div class="col-3">
                <?php
                $query = "SELECT id, name from tgroup WHERE id > 1 ORDER BY name";
                displayList("lstShift", "Shift: ", $lstShift, $prints, $conn, $query, "", "25%", "75%");
                ?>
            </div>
            <?php 
                displaySearchFields($conn, $prints, $session_variable, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $lstEmployeeName, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $lstGroup, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
            ?>
        </div>
        <div class="row">
            <div class="col-2">
                <?php 
                $query = "SELECT distinct(Week), Week from AttendanceMaster ORDER BY Week";
                displayList("lstWeek", "Week: ", $lstWeek, $prints, $conn, $query, "", "25%", "25%");
                ?>
            </div>
            <div class="col-2">
                <?php 
                if ($prints != "yes") {
                    print "<label class='form-label'>Day:</label><select name='lstDay'  class='form-select select2 shadow-none'><option selected value='" . $lstDay . "'>" . $lstDay . "</option> <option value='Sunday'>Sunday</option> <option value='Monday'>Monday</option> <option value='Tuesday'>Tuesday</option> <option value='Wednesday'>Wednesday</option> <option value='Thursday'>Thursday</option> <option value='Friday'>Friday</option> <option value='Saturday'>Saturday</option></select>";
                } else {
                    displayTextbox("lstDay", "lstDay: ", $lstDay, $prints, 12, "25%", "75%");
                }
                ?>
            </div>
        <?php 
            if ($prints != "yes") {
                print "<div class='col-2'>";
                displayTextbox("txtFrom", "Date From <font size='1'>(DD/MM/YYYY)</font>: ", $txtFrom, $prints, 12, "25%", "25%");
                print "</div>";
                print "<div class='col-2'>";
                displayTextbox("txtARemark", "Attendance Remark: ", $txtARemark, $prints, 12, "25%", "25%");
                print "</div>";
                print "<div class='col-2'>";
                displayTextbox("txtTo", "Date To <font size='1'>(DD/MM/YYYY)</font>: ", $txtTo, $prints, 12, "25%", "25%");
                print "</div>";
                print "<div class='col-2'>";
                print "<label class='form-label'>Display Attendance Remark:</label><select name='lstRemark' class='form-select select2 shadow-none'><option selected value='" . $lstRemark . "'>" . $lstRemark . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select>";
                print "</div>";
                print "</div>";
                print "<div class='row'>";
                print "<div class='col-2'>";
                displayEmployeeStatus($conn, "lstEmployeeStatus", $lstEmployeeStatus, "25%", "35%");
                print "</div>";
                print "<div class='col-2'>";
                print "<label class='form-label'>Work Type:</label><select name='lstType' class='form-select select2 shadow-none'><option selected value='" . $lstType . "'>" . $lstType . "</option> <option value='Early In'>Early In</option> <option value='Late In'>Late In</option> <option value='Less Break'>Less Break</option> <option value='More Break'>More Break</option> <option value='Early Out'>Early Out</option> <option value='Late Out'>Late Out</option> <option value='Grace'>Grace</option> <option value='Normal = 0'>Normal = 0</option> <option value='Normal > 0'>Normal > 0</option> <option value='OT > 0'>OT > 0</option> <option value='AOT > 0'>AOT > 0</option> <option value=''>---</option></select>";
                print "</div>";
                print "<div class='col-2'>";
                print "<label class='form-label'>Display Summary Only:</label><select name='lstSummary' class='form-control'><option selected value='" . $lstSummary . "'>" . $lstSummary . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select>";
                print "</div>";
                print "<div class='col-2'>";
                displayColourFlag($conn, $lstColourFlag, "lstColourFlag", true, true);
                print "</div>";
                print "<div class='col-2'>";
                $array = array(array("tuser.id, AttendanceMaster.ADate", "Employee Code"), array("tuser.name, tuser.id, AttendanceMaster.ADate", "Employee Name - Code"), array("tuser.dept, tuser.id, AttendanceMaster.ADate", "Dept - Code"), array("tuser.company, tuser.dept, tuser.id, AttendanceMaster.ADate", "Div/Desg - Dept - Code"), array("tuser.company, tuser.dept, tuser.group_id, tuser.id, AttendanceMaster.ADate", "Div - Dept - Current Shift - Code"), array("AttendanceMaster.ADate", "Date"), array("AttendanceMaster.Day, tuser.id, AttendanceMaster.ADate", "Day"), array("AttendanceMaster.Week, tuser.id, AttendanceMaster.ADate", "Week"));
                displaySort($array, $lstSort, 8);
                print "</div>";
                print "</div>";
                print "<div class='row'>";
                print "<div class='col-12'>";
                print "<center><br><input name='btSearch' type='submit' class='btn btn-primary' value='Search Record'></center>";
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
    if ($lstSummary == "No") {
        $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, AttendanceMaster.ADate, AttendanceMaster.Day, AttendanceMaster.Week, AttendanceMaster.EarlyIn, AttendanceMaster.LateIn, AttendanceMaster.Break, AttendanceMaster.LessBreak, AttendanceMaster.MoreBreak, AttendanceMaster.EarlyOut, AttendanceMaster.LateOut, AttendanceMaster.Normal, AttendanceMaster.Grace, AttendanceMaster.Overtime, AttendanceMaster.AOvertime, AttendanceMaster.Flag, tuser.idno, tuser.remark, AttendanceMaster.LateIn_flag, AttendanceMaster.EarlyOut_flag, AttendanceMaster.MoreBreak_flag, AttendanceMaster.Remark, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, tgroup, AttendanceMaster WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.EmployeeID = tuser.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
        if ($lstShift != "") {
            $query = $query . " AND tgroup.id = " . $lstShift;
        }
        if ($txtARemark != "") {
            $query = $query . " AND AttendanceMaster.Remark LIKE '%" . $txtARemark . "%'";
        }
        $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $lstGroup, $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
        if ($lstWeek != "") {
            $query = $query . " AND AttendanceMaster.Week = " . $lstWeek;
        }
        if ($lstDay != "") {
            $query = $query . " AND AttendanceMaster.Day = '" . $lstDay . "'";
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
        if ($lstIPEL == "Yes") {
            $previous_day = getLastDay(insertDate($txtFrom), 1);
            $query = $query . " AND (AttendanceMaster.ADate >= " . insertDate($txtFrom) . " OR (AttendanceMaster.ADate = " . $previous_day . " AND AttendanceMaster.NightFlag = 1)) ";
            $penultimate_day = getLastDay(insertDate($txtTo), 1);
            $query = $query . " AND (AttendanceMaster.ADate <= " . $penultimate_day . " OR (AttendanceMaster.ADate = " . insertDate($txtTo) . " AND AttendanceMaster.NightFlag = 0)) ";
        } else {
            if ($txtFrom != "" && $lstWeek == "") {
                $query = $query . " AND (AttendanceMaster.ADate >= " . insertDate($txtFrom) . " OR AttendanceMaster.ADate = 0) ";
            }
            if ($txtTo != "" && $lstWeek == "") {
                $query = $query . " AND (AttendanceMaster.ADate <= " . insertDate($txtTo) . " OR AttendanceMaster.ADate = 0) ";
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
                                            $query = $query . " AND AttendanceMaster.Normal <= 0 ";
                                        } else {
                                            if ($lstType == "Normal > 0") {
                                                $query = $query . " AND AttendanceMaster.Normal > 0 ";
                                            } else {
                                                if ($lstType == "OT > 0") {
                                                    $query = $query . " AND AttendanceMaster.Overtime > 0 ";
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
        $query = $query . employeeStatusQuery($lstEmployeeStatus);
        if ($lstSort != "") {
            $query = $query . " ORDER BY " . $lstSort;
        } else {
            $query = $query . " ORDER BY tuser.id, AttendanceMaster.ADate";
        }
        if ($excel != "yes") {
            print "<p align='center'><font face='Verdana' size='1'><b>" . $_SESSION[$session_variable . "FlagReportText"] . "</b></font></p>";
        }
        print '<div class="row"><div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12"><div class="card"><div class="card-body table-responsive">';
        if ($prints != "yes") {
            print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800' class='table table-striped table-bordered dataTable' id='zero_config'>";
        } else {
            print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%' class='table table-striped table-bordered dataTable'>";
        }
        print "<thead><tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Shift</font></td> <td><font face='Verdana' size='2'>Date</font></td> <td><font face='Verdana' size='2'>Day</font></td> <td><font face='Verdana' size='2'>Week</font></td> <td><font face='Verdana' size='2'>Early In</font></td> <td><font face='Verdana' size='2'>Late In</font></td> <td><font face='Verdana' size='2'>Break</font></td> <td><font face='Verdana' size='2'>Less Break</font></td> <td><font face='Verdana' size='2'>More Break</font></td> <td><font face='Verdana' size='2'>Early Out</font></td> <td><font face='Verdana' size='2'>Late Out</font></td> <td><font face='Verdana' size='2'>Grace</font></td> <td><font face='Verdana' size='2'>Normal</font></td> <td><font face='Verdana' size='2'>OT</font></td> <td><font face='Verdana' size='2'>App OT</font></td> <td><font face='Verdana' size='2'>Flag</font></td>";
        if ($lstRemark == "Yes") {
            print "<td><font face='Verdana' size='2'>A Rmk</font></td>";
        }
        print "</tr></thead>";
        print "<tr><td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>Min</font></td> <td><font face='Verdana' size='2'>Min</font></td> <td><font face='Verdana' size='2'>Min</font></td> <td><font face='Verdana' size='2'>Min</font></td> <td><font face='Verdana' size='2'>Min</font></td> <td><font face='Verdana' size='2'>Min</font></td> <td><font face='Verdana' size='2'>Min</font></td> <td><font face='Verdana' size='2'>Min</font></td> <td><font face='Verdana' size='2'>Hrs</font></td> <td><font face='Verdana' size='2'>Hrs</font></td> <td><font face='Verdana' size='2'>Hrs</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td>";
        if ($lstRemark == "Yes") {
            print "<td><font face='Verdana' size='2'>&nbsp;</font></td>";
        }
        print "</tr>";
        $result = mysqli_query($conn, $query);
        $count = 0;
        $font = "Black";
        for ($bgcolor = "#FFFFFF"; $cur = mysqli_fetch_row($result); $count++) {
            if ($cur[19] != "") {
                $font = $cur[19];
                if ($font == "Yellow") {
                    $bgcolor = "Brown";
                } else {
                    $bgcolor = "#FFFFFF";
                }
            } else {
                $cur[19] = "&nbsp;";
                $font = "Black";
                $bgcolor = "#FFFFFF";
            }
            if ($cur[3] == "") {
                $cur[3] = "&nbsp;";
            }
            if ($cur[20] == "") {
                $cur[20] = "&nbsp;";
            }
            if ($cur[21] == "") {
                $cur[21] = "&nbsp;";
            }
            addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
            print "<tr><td bgcolor='" . $bgcolor . "'><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'><a title='ID'><font face='Verdana' size='1' color='" . $font . "'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Name'><font face='Verdana' size='1' color='" . $font . "'>" . $cur[1] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1' color='" . $font . "'>" . $cur[20] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Dept'><font face='Verdana' size='1' color='" . $font . "'>" . $cur[2] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Div/Desg'><font face='Verdana' size='1' color='" . $font . "'>" . $cur[3] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Rmk'><font face='Verdana' size='1' color='" . $font . "'>" . $cur[21] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Shift'><font face='Verdana' size='1' color='" . $font . "'>" . $cur[4] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Date'><font face='Verdana' size='1' color='" . $font . "'>";
            if (0 < $cur[5]) {
                displayDate($cur[5]);
                print displayDate($cur[5]);
            } else {
                print "--";
            }
            round($cur[8] / 60, 2);
            print "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Day'><font face='Verdana' size='1' color='" . $font . "'>" . $cur[6] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Week'><font face='Verdana' size='1' color='" . $font . "'>" . $cur[7] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Early In (Min)'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[8] / 60, 2) . "</font></a></td>";
            if ($cur[22] == 0) {
                round($cur[9] / 60, 2);
                print "<td bgcolor='" . $bgcolor . "'><a title='Late In (Min)'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[9] / 60, 2) . "</font></a></td>";
            } else {
                round($cur[9] / 60, 2);
                print "<td bgcolor='" . $bgcolor . "'><a title='Late In (Min)'><font face='Verdana' size='1' color='" . $font . "'><strike>" . round($cur[9] / 60, 2) . "</strike></font></a></td>";
            }
            round($cur[10] / 60, 2);
            round($cur[11] / 60, 2);
            print "<td bgcolor='" . $bgcolor . "'><a title='Break (Min)'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[10] / 60, 2) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Less Break (Min)'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[11] / 60, 2) . "</font></a></td>";
            if ($cur[24] == 0) {
                round($cur[12] / 60, 2);
                print "<td bgcolor='" . $bgcolor . "'><a title='More Break (Min)'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[12] / 60, 2) . "</font></a></td>";
            } else {
                round($cur[12] / 60, 2);
                print "<td bgcolor='" . $bgcolor . "'><a title='More Break (Min)'><font face='Verdana' size='1' color='" . $font . "'><strike>" . round($cur[12] / 60, 2) . "</strike></font></a></td>";
            }
            if ($cur[23] == 0) {
                round($cur[13] / 60, 2);
                print "<td bgcolor='" . $bgcolor . "'><a title='Early Out (Min)'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[13] / 60, 2) . "</font></a></td>";
            } else {
                round($cur[13] / 60, 2);
                print "<td bgcolor='" . $bgcolor . "'><a title='Early Out (Min)'><font face='Verdana' size='1' color='" . $font . "'><strike>" . round($cur[13] / 60, 2) . "</strike></font></a></td>";
            }
            round($cur[14] / 60, 2);
            round($cur[16] / 60, 2);
            round($cur[15] / 3600, 2);
            round($cur[17] / 3600, 2);
            round($cur[18] / 3600, 2);
            print "<td bgcolor='" . $bgcolor . "'><a title='Late Out (Min)'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[14] / 60, 2) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Grace (Min)'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[16] / 60, 2) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Normal (Hrs)'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[15] / 3600, 2) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='OT (Hrs)'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[17] / 3600, 2) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='App OT (Hrs)'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[18] / 3600, 2) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Flag'><font face='Verdana' size='1' color='" . $font . "'>" . $cur[19] . "</font></a></td>";
            if ($lstRemark == "Yes") {
                print "<td bgcolor='" . $bgcolor . "'><a title='Remark'><font face='Verdana' size='1' color='" . $font . "'>" . $cur[25] . "</font></a></td>";
            }
            print "</tr>";
        }
        print "</table>";
    } else {
        $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tuser.idno, tuser.remark, SUM(AttendanceMaster.EarlyIn), SUM(AttendanceMaster.LateIn), SUM(AttendanceMaster.Break), SUM(AttendanceMaster.LessBreak), SUM(AttendanceMaster.MoreBreak), SUM(AttendanceMaster.EarlyOut), SUM(AttendanceMaster.LateOut), SUM(AttendanceMaster.Grace), SUM(AttendanceMaster.Normal), SUM(AttendanceMaster.Overtime), SUM(AttendanceMaster.AOvertime), COUNT(AttendanceMaster.AttendanceID) FROM tuser, AttendanceMaster WHERE AttendanceMaster.EmployeeID = tuser.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
        if ($txtARemark != "") {
            $query = $query . " AND AttendanceMaster.Remark LIKE '%" . $txtARemark . "%'";
        }
        $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $lstGroup, $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
        if ($lstWeek != "") {
            $query = $query . " AND AttendanceMaster.Week = " . $lstWeek;
        }
        if ($lstDay != "") {
            $query = $query . " AND AttendanceMaster.Day = '" . $lstDay . "'";
        }
        if ($lstColourFlag != "") {
            if ($lstColourFlag == "All w/o Black/Proxy") {
                $query = $query . " AND AttendanceMaster.Flag NOT LIKE 'Black' AND AttendanceMaster.Flag NOT LIKE 'Proxy'";
            } else {
                if ($lstColourFlag == "All w/o Proxy") {
                    $query = $query . " AND AttendanceMaster.Flag NOT LIKE 'Proxy'";
                } else {
                    $query = $query . " AND AttendanceMaster.Flag = '" . $lstColourFlag . "'";
                }
            }
        } else {
            $query = $query . " AND AttendanceMaster.Flag NOT LIKE 'Delete' ";
        }
        if ($txtFrom != "" && $lstWeek == "") {
            $query = $query . " AND (AttendanceMaster.ADate >= " . insertDate($txtFrom) . " OR AttendanceMaster.ADate = 0) ";
        }
        if ($txtTo != "" && $lstWeek == "") {
            $query = $query . " AND (AttendanceMaster.ADate <= " . insertDate($txtTo) . " OR AttendanceMaster.ADate = 0) ";
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
                                        if ($lstType == "OT") {
                                            $query = $query . " AND AttendanceMaster.Overtime > 0 ";
                                        } else {
                                            if ($lstType == "Approved OT") {
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
        $query = $query . employeeStatusQuery($lstEmployeeStatus);
        if ($lstSort != "") {
            $query = $query . " GROUP BY tuser.id, tuser.name, tuser.dept, tuser.company, tuser.idno, tuser.remark ORDER BY " . $lstSort;
        } else {
            $query = $query . " ORDER BY tuser.id, AttendanceMaster.ADate";
        }
        if ($excel != "yes") {
            print "<p align='center'><font face='Verdana' size='1'><b>" . $_SESSION[$session_variable . "FlagReportText"] . "</b></font></p>";
        }
        if ($prints != "yes") {
            print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800'>";
        } else {
            print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%'>";
        }
        print "<tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Days</font></td> <td><font face='Verdana' size='2'>Early In</font></td> <td><font face='Verdana' size='2'>Late In</font></td> <td><font face='Verdana' size='2'>Break</font></td> <td><font face='Verdana' size='2'>Less Break</font></td> <td><font face='Verdana' size='2'>More Break</font></td> <td><font face='Verdana' size='2'>Early Out</font></td> <td><font face='Verdana' size='2'>Late Out</font></td> <td><font face='Verdana' size='2'>Grace</font></td> <td><font face='Verdana' size='2'>Normal</font></td> <td><font face='Verdana' size='2'>OT</font></td> <td><font face='Verdana' size='2'>App OT</font></td> </tr>";
        print "<tr><td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>Hrs</font></td> <td><font face='Verdana' size='2'>Hrs</font></td> <td><font face='Verdana' size='2'>Hrs</font></td> <td><font face='Verdana' size='2'>Hrs</font></td> <td><font face='Verdana' size='2'>Hrs</font></td> <td><font face='Verdana' size='2'>Hrs</font></td> <td><font face='Verdana' size='2'>Hrs</font></td> <td><font face='Verdana' size='2'>Hrs</font></td> <td><font face='Verdana' size='2'>Hrs</font></td> <td><font face='Verdana' size='2'>Hrs</font></td> <td><font face='Verdana' size='2'>Hrs</font></td> </tr>";
        $result = mysqli_query($conn, $query);
        $count = 0;
        $font = "Black";
        for ($bgcolor = "#FFFFFF"; $cur = mysqli_fetch_row($result); $count++) {
            if ($cur[19] != "") {
                $font = $cur[19];
                if ($font == "Yellow") {
                    $bgcolor = "Brown";
                } else {
                    $bgcolor = "#FFFFFF";
                }
            } else {
                $cur[19] = "&nbsp;";
                $font = "Black";
                $bgcolor = "#FFFFFF";
            }
            if ($cur[3] == "") {
                $cur[3] = "&nbsp;";
            }
            if ($cur[4] == "") {
                $cur[4] = "&nbsp;";
            }
            if ($cur[5] == "") {
                $cur[5] = "&nbsp;";
            }
            addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
            round($cur[6] / 3600, 2);
            round($cur[7] / 3600, 2);
            round($cur[8] / 3600, 2);
            round($cur[9] / 3600, 2);
            round($cur[10] / 3600, 2);
            round($cur[11] / 3600, 2);
            round($cur[12] / 3600, 2);
            round($cur[13] / 3600, 2);
            round($cur[14] / 3600, 2);
            round($cur[15] / 3600, 2);
            round($cur[16] / 3600, 2);
            print "<tr><td bgcolor='" . $bgcolor . "'><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'><a title='ID'><font face='Verdana' size='1' color='" . $font . "'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Name'><font face='Verdana' size='1' color='" . $font . "'>" . $cur[1] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1' color='" . $font . "'>" . $cur[4] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Dept'><font face='Verdana' size='1' color='" . $font . "'>" . $cur[2] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Div/Desg'><font face='Verdana' size='1' color='" . $font . "'>" . $cur[3] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Rmk'><font face='Verdana' size='1' color='" . $font . "'>" . $cur[5] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Total Days Present'><font face='Verdana' size='1' color='" . $font . "'>" . $cur[17] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Early In (Min)'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[6] / 3600, 2) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Late In (Min)'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[7] / 3600, 2) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Break (Min)'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[8] / 3600, 2) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Less Break (Min)'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[9] / 3600, 2) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='More Break (Min)'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[10] / 3600, 2) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Early Out (Min)'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[11] / 3600, 2) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Late Out (Min)'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[12] / 3600, 2) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Grace (Min)'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[13] / 3600, 2) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Normal (Hrs)'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[14] / 3600, 2) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='OT (Hrs)'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[15] / 3600, 2) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='App OT (Hrs)'><font face='Verdana' size='1' color='" . $font . "'>" . round($cur[16] / 3600, 2) . "</font></a></td> </tr>";
        }
        print "</table>";
    }
    if ($excel != "yes") {
        print "<br><p align='center'><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $count . "</b></font>";
    }
    if ($prints != "yes") {
        print "<br><input type='button' value='Print Report' class='btn btn-primary' onClick='checkPrint(0)'>&nbsp;&nbsp;<input type='button' class='btn btn-primary' value='Excel' onClick='checkPrint(1)'>";
    }
    print "</p>";
}
print "</form>";
echo "</center>";
print "</div></div></div></div></div>";
include 'footer.php';

?>