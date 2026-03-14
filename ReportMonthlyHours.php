<?php


ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "21";
set_time_limit(900);
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
$userrhsselection = $_SESSION[$session_variable . "userrhsselection"];
$ot1f = $_SESSION[$session_variable . "ot1f"];
$ot2f = $_SESSION[$session_variable . "ot2f"];
$otdf = $_SESSION[$session_variable . "otdf"];
if (!(checkSession($userlevel, $current_module) && $_SESSION[$session_variable . "VirdiLevel"] == "Classic")) {
    header("Location: " . $config["REDIRECT"] . "?url=ReportMonthlyHours.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$act = $_GET["act"];
if ($act == "") {
    $act = $_POST["act"];
}
$prints = $_GET["prints"];
$excel = $_GET["excel"];
$message = $_GET["message"];
if ($message == "") {
    $message = "Hour Summary Report<br>Report Valid ONLY for Shifts with Routine Type = Daily";
}
$lstShift = $_POST["lstShift"];
$lstDepartment = $_POST["lstDepartment"];
$lstDivision = $_POST["lstDivision"];
$lstEmployeeIDFrom = $_POST["lstEmployeeIDFrom"];
$lstEmployeeIDTo = $_POST["lstEmployeeIDTo"];
$txtEmployeeCode = $_POST["txtEmployeeCode"];
$txtEmployee = $_POST["txtEmployee"];
$txtFrom = $_POST["txtFrom"];
$txtTo = $_POST["txtTo"];
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
$lstHourDetails = $_POST["lstHourDetails"];
if ($lstHourDetails == "") {
    $lstHourDetails = "No";
}
$txtSatFactor = $_POST["txtSatFactor"];
if ($txtSatFactor == "" || is_numeric($txtSatFactor) == false) {
    $txtSatFactor = $ot1f;
}
$txtSunFactor = $_POST["txtSunFactor"];
if ($txtSunFactor == "" || is_numeric($txtSunFactor) == false) {
    $txtSunFactor = $ot2f;
}
$txtFlagFactor = $_POST["txtFlagFactor"];
if ($txtFlagFactor == "" || is_numeric($txtFlagFactor) == false) {
    $txtFlagFactor = $otdf;
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
$txtRemark = $_POST["txtRemark"];
$txtPhone = $_POST["txtPhone"];
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
$lstCaptionPreFlag = $_POST["lstCaptionPreFlag"];
if ($lstCaptionPreFlag == "") {
    if (strpos($userrhsselection, "--PreFlag") !== false) {
        $lstCaptionPreFlag = "Yes";
    } else {
        $lstCaptionPreFlag = "No";
    }
}
$lstCaptionIDNo = $_POST["lstCaptionIDNo"];
if ($lstCaptionIDNo == "") {
    if (strpos($userrhsselection, "--IDNo") !== false) {
        $lstCaptionIDNo = "Yes";
    } else {
        $lstCaptionIDNo = "No";
    }
}
$lstCaptionDept = $_POST["lstCaptionDept"];
if ($lstCaptionDept == "") {
    if (strpos($userrhsselection, "--Dept") !== false) {
        $lstCaptionDept = "Yes";
    } else {
        $lstCaptionDept = "No";
    }
}
$lstCaptionDiv = $_POST["lstCaptionDiv"];
if ($lstCaptionDiv == "") {
    if (strpos($userrhsselection, "--Div") !== false) {
        $lstCaptionDiv = "Yes";
    } else {
        $lstCaptionDiv = "No";
    }
}
$lstCaptionRemark = $_POST["lstCaptionRemark"];
if ($lstCaptionRemark == "") {
    if (strpos($userrhsselection, "--Remark") !== false) {
        $lstCaptionRemark = "Yes";
    } else {
        $lstCaptionRemark = "No";
    }
}
$lstCaptionTotal = $_POST["lstCaptionTotal"];
if ($lstCaptionTotal == "") {
    if (strpos($userrhsselection, "--Total") !== false) {
        $lstCaptionTotal = "Yes";
    } else {
        $lstCaptionTotal = "No";
    }
}
$colcount = 0;
$userrhsselection = "";
if ($lstType == "LateIn-EarlyOut") {
    $lstHourDetails = "No";
}
if ($prints != "yes") {
    include 'header.php';
?>
<div class="page-breadcrumb">
                    <div class="row">
                        <div class="col-12 d-flex no-block align-items-center">
                            <h4 class="page-title">Hour Summary Report</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Hour Summary Report
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

<?php
}

print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='ReportMonthlyHours.php'><input type='hidden' name='act' value='searchRecord'>";
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
        header("Content-Disposition: attachment; filename=ReportMonthlyHours.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        print "<body>";
    }
}
print "<center>";
print "<tr>";
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
                $query = "SELECT id, name from tgroup WHERE ShiftTypeID = 1 ORDER BY name";
                displayList("lstShift", "Current Shift: ", $lstShift, $prints, $conn, $query, "", "", "");
                ?>
            </div>
            <?php 
            displaySearchFields($conn, $prints, $session_variable, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $lstEmployeeName, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
            ?>
        </div>
        <div class="row">
            <div class="col-2">
                <?php 
                displayTextbox("txtFrom", "Date From <font size='1'>(DD/MM/YYYY)</font>: ", $txtFrom, $prints, 12, "", "");
                ?>
            </div>
            <div class="col-2">
                <?php 
                print "<label class='form-label'>Display Hour Details:</label><select name='lstHourDetails' class='form-select select2 shadow-none'><option selected value='" . $lstHourDetails . "'>" . $lstHourDetails . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option><option value='IN/OUT'>IN/OUT</option></select>";
                ?>
            </div>
            <div class="col-2">
                <?php 
                displayTextbox("txtTo", "Date To <font size='1'>(DD/MM/YYYY)</font>: ", $txtTo, $prints, 12, "", "");
                ?>
            </div>
            <div class="col-2">
                <?php 
                displayTextbox("txtSatFactor", "Saturday OT Factor: ", $txtSatFactor, $prints, 4, "", "");
                ?>
            </div>
            
        <?php 
            if ($prints != "yes") {
                print "<div class='col-2'>";
                print "<label class='form-label'>Work Type:</label><select name='lstType' class='form-select select2 shadow-none'><option selected value='" . $lstType . "'>" . $lstType . "</option> <option value='LateIn-EarlyOut'>LateIn-EarlyOut</option> <option value='Early In'>Early In</option> <option value='Late In'>Late In</option> <option value='Less Break'>Less Break</option> <option value='More Break'>More Break</option> <option value='Early Out'>Early Out</option> <option value='Late Out'>Late Out</option> <option value='Grace'>Grace</option> <option value='OT'>OT</option> <option value='Approved OT'>Approved OT</option> <option value=''>---</option> </select>";
                print "</div>";
                print "<div class='col-2'>";
                displayTextbox("txtSunFactor", "Sunday OT Factor: ", $txtSunFactor, $prints, 4, "", "");
                print "</div>";
                print "</div>";
                print "<div class='row'>";
                print "<div class='col-2'>";
                print "<label class='form-label'>Display " . $_SESSION[$session_variable . "IDColumnName"] . " Column:</label><select name='lstCaptionIDNo' class='form-select select2 shadow-none'><option selected value='" . $lstCaptionIDNo . "'>" . $lstCaptionIDNo . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select>";
                print "</div>";
                print "<div class='col-2'>";
                displayTextbox("txtFlagFactor", "Flag OT Factor: ", $txtFlagFactor, $prints, 4, "", "");
                print "</div>";
                print "<div class='col-2'>";
                print "<label class='form-label'>Display Remark Column:</label><select name='lstCaptionRemark' class='form-select select2 shadow-none'><option selected value='" . $lstCaptionRemark . "'>" . $lstCaptionRemark . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select>";
                print "</div>";
                print "<div class='col-2'>";
                print "<label class='form-label'>Display Department Column:</label><select name='lstCaptionDept' class='form-select select2 shadow-none'><option selected value='" . $lstCaptionDept . "'>" . $lstCaptionDept . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select>";
                print "</div>";
                print "<div class='col-2'>";
                print "<label class='form-label'>Display Div/Desg Column:</label><select name='lstCaptionDiv' class='form-select select2 shadow-none'><option selected value='" . $lstCaptionDiv . "'>" . $lstCaptionDiv . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select>";
                print "</div>";
                print "<div class='col-2'>";
                print "<label class='form-label'>Display Flag Title:</label><select name='lstCaptionPreFlag' class='form-select select2 shadow-none'><option selected value='" . $lstCaptionPreFlag . "'>" . $lstCaptionPreFlag . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select>";
                print "</div>";
                print "</div>";
                print "<div class='row'>";
                print "<div class='col-2'>";
                displayEmployeeStatus($conn, "lstEmployeeStatus", $lstEmployeeStatus, "25%", "25%");
                print "</div>";
                print "<div class='col-2'>";
                print "<label class='form-label'>Display TLH Column ONLY:</label><select name='lstCaptionTotal' class='form-select select2 shadow-none'><option selected value='" . $lstCaptionTotal . "'>" . $lstCaptionTotal . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select>";
                print "</div>";
                print "<div class='col-2'>";
                $array = array(array("tuser.id, AttendanceMaster.ADate", "Employee Code"), array("tuser.name, tuser.id, AttendanceMaster.ADate", "Employee Name - Code"), array("tuser.dept, tuser.id, AttendanceMaster.ADate", "Dept - Code"), array("tuser.company, tuser.dept, tuser.id, AttendanceMaster.ADate", "Div/Desg - Dept - Code"), array("tuser.company, tuser.dept, tuser.group_id, tuser.id, AttendanceMaster.ADate", "Div - Dept - Current Shift - Code"));
                displaySort($array, $lstSort, 5);
                print "</div>";
                print "</div>";
                print "<div class='row'>";
                print "<div class='col-12'>";
                print "<center><br><input name='btSearch' class='btn btn-primary' type='submit' value='Search Record'></center>";
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
    if ($excel != "yes") {
        print "<p align='center'><font face='Verdana' size='1'><b>" . $_SESSION[$session_variable . "FlagReportText"] . "</b></font></p>";
        print "<p align='center'><font face='Verdana' size='1'><b><u>All Times Displayed are in Hours</u> <br><br><font size='2'>WKD = Week Day ; PXY = Proxy ; FLG = Flag Day ; SAT = Saturday / OT1 ; SUN = Sunday / OT2 ; TLH = Total Hours <br>NS = Night Shift Hours </font> <br>N = Normal Hours; O = Overtime Hours ; AO = Approved Overtime Hours";
        if ($prints != "yes") {
            print "<br><br>Click on the Hour Record to get the Clocking Details for the selected Period";
        }
        print "</b></font></p>";
    }
    $query = "";
    if ($lstType == "LateIn-EarlyOut") {
        $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, AttendanceMaster.group_min, AttendanceMaster.LateIn, AttendanceMaster.Grace, 0, AttendanceMaster.ADate, tuser.idno, tuser.remark, AttendanceMaster.Flag, AttendanceMaster.Day, tgroup.NightFlag, AttendanceMaster.OT1, AttendanceMaster.OT2, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10, AttendanceMaster.EarlyOut FROM tuser, tgroup, AttendanceMaster WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.Flag NOT LIKE 'Delete' AND AttendanceMaster.EmployeeID = tuser.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
    } else {
        if ($lstType == "Early In") {
            $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, AttendanceMaster.group_min, AttendanceMaster.EarlyIn, AttendanceMaster.Grace, 0, AttendanceMaster.ADate, tuser.idno, tuser.remark, AttendanceMaster.Flag, AttendanceMaster.Day, tgroup.NightFlag, AttendanceMaster.OT1, AttendanceMaster.OT2, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, tgroup, AttendanceMaster WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.Flag NOT LIKE 'Delete' AND AttendanceMaster.EmployeeID = tuser.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
        } else {
            if ($lstType == "Late In") {
                $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, AttendanceMaster.group_min, AttendanceMaster.LateIn, AttendanceMaster.Grace, 0, AttendanceMaster.ADate, tuser.idno, tuser.remark, AttendanceMaster.Flag, AttendanceMaster.Day, tgroup.NightFlag, AttendanceMaster.OT1, AttendanceMaster.OT2, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, tgroup, AttendanceMaster WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.Flag NOT LIKE 'Delete' AND AttendanceMaster.EmployeeID = tuser.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
            } else {
                if ($lstType == "Less Break") {
                    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, AttendanceMaster.group_min, AttendanceMaster.LessBreak, AttendanceMaster.Grace, 0, AttendanceMaster.ADate, tuser.idno, tuser.remark, AttendanceMaster.Flag, AttendanceMaster.Day, tgroup.NightFlag, AttendanceMaster.OT1, AttendanceMaster.OT2, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, tgroup, AttendanceMaster WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.Flag NOT LIKE 'Delete' AND AttendanceMaster.EmployeeID = tuser.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
                } else {
                    if ($lstType == "More Break") {
                        $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, AttendanceMaster.group_min, AttendanceMaster.MoreBreak, AttendanceMaster.Grace, 0, AttendanceMaster.ADate, tuser.idno, tuser.remark, AttendanceMaster.Flag, AttendanceMaster.Day, tgroup.NightFlag, AttendanceMaster.OT1, AttendanceMaster.OT2, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, tgroup, AttendanceMaster WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.Flag NOT LIKE 'Delete' AND AttendanceMaster.EmployeeID = tuser.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
                    } else {
                        if ($lstType == "Early Out") {
                            $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, AttendanceMaster.group_min, AttendanceMaster.EarlyOut, AttendanceMaster.Grace, 0, AttendanceMaster.ADate, tuser.idno, tuser.remark, AttendanceMaster.Flag, AttendanceMaster.Day, tgroup.NightFlag, AttendanceMaster.OT1, AttendanceMaster.OT2, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, tgroup, AttendanceMaster WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.Flag NOT LIKE 'Delete' AND AttendanceMaster.EmployeeID = tuser.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
                        } else {
                            if ($lstType == "Late Out") {
                                $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, AttendanceMaster.group_min, AttendanceMaster.LateOut, AttendanceMaster.Grace, 0, AttendanceMaster.ADate, tuser.idno, tuser.remark, AttendanceMaster.Flag, AttendanceMaster.Day, tgroup.NightFlag, AttendanceMaster.OT1, AttendanceMaster.OT2, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, tgroup, AttendanceMaster WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.Flag NOT LIKE 'Delete' AND AttendanceMaster.EmployeeID = tuser.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
                            } else {
                                if ($lstType == "Grace") {
                                    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, AttendanceMaster.group_min, AttendanceMaster.Grace, AttendanceMaster.Grace, 0, AttendanceMaster.ADate, tuser.idno, tuser.remark, AttendanceMaster.Flag, AttendanceMaster.Day, tgroup.NightFlag, AttendanceMaster.OT1, AttendanceMaster.OT2, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, tgroup, AttendanceMaster WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.Flag NOT LIKE 'Delete' AND AttendanceMaster.EmployeeID = tuser.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
                                } else {
                                    if ($lstType == "OT") {
                                        $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, AttendanceMaster.group_min, AttendanceMaster.Overtime, AttendanceMaster.Grace, 0, AttendanceMaster.ADate, tuser.idno, tuser.remark, AttendanceMaster.Flag, AttendanceMaster.Day, tgroup.NightFlag, AttendanceMaster.OT1, AttendanceMaster.OT2, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, tgroup, AttendanceMaster WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.Flag NOT LIKE 'Delete' AND AttendanceMaster.EmployeeID = tuser.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
                                    } else {
                                        if ($lstType == "Approved OT") {
                                            $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, AttendanceMaster.group_min, AttendanceMaster.AOvertime, AttendanceMaster.Grace, 0, AttendanceMaster.ADate, tuser.idno, tuser.remark, AttendanceMaster.Flag, AttendanceMaster.Day, tgroup.NightFlag, AttendanceMaster.OT1, AttendanceMaster.OT2, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, tgroup, AttendanceMaster WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.Flag NOT LIKE 'Delete' AND AttendanceMaster.EmployeeID = tuser.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
                                        } else {
                                            $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, AttendanceMaster.group_min, AttendanceMaster.Normal, AttendanceMaster.Grace, AttendanceMaster.Overtime, AttendanceMaster.ADate, tuser.idno, tuser.remark, AttendanceMaster.Flag, AttendanceMaster.Day, tgroup.NightFlag, AttendanceMaster.OT1, AttendanceMaster.OT2, AttendanceMaster.AOvertime, AttendanceMaster.LateIn, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10, tuser.phone FROM tuser, tgroup, AttendanceMaster WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.Flag NOT LIKE 'Delete' AND AttendanceMaster.EmployeeID = tuser.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
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
    if ($lstShift != "") {
        $query = $query . " AND tgroup.id = " . $lstShift;
    }
    $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
    if ($txtFrom != "") {
        $query = $query . " AND AttendanceMaster.ADate >= " . insertDate($txtFrom);
    }
    if ($txtTo != "") {
        $query = $query . " AND AttendanceMaster.ADate <= " . insertDate($txtTo);
    }
    $query = $query . employeeStatusQuery($lstEmployeeStatus);
    $fromTime = mktime(0, 0, 0, substr(insertDate($txtFrom), 4, 2), substr(insertDate($txtFrom), 6, 2), substr(insertDate($txtFrom), 0, 4));
    $toTime = mktime(0, 0, 0, substr(insertDate($txtTo), 4, 2), substr(insertDate($txtTo), 6, 2), substr(insertDate($txtTo), 0, 4));
    $dayCount = ($toTime - $fromTime) / 86400;
    $dayCount++;
    $query = $query . " ORDER BY " . $lstSort;
    print '<div class="row"><div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12"><div class="card"><div class="card-body table-responsive">';
    print "<table border='-1' cellpadding='0' bordercolor='#C0C0C0' cellspacing='0' width='100%' class='table table-striped table-bordered dataTable' id='zero_config'>";
    print "<thead><tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td>";
    if ($lstCaptionPreFlag == "Yes") {
        $userrhsselection = $userrhsselection . "--PreFlag";
    }
    if ($lstCaptionIDNo == "Yes") {
        print "<td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td>";
        $userrhsselection = $userrhsselection . "--IDNo";
    } else {
        $colcount++;
    }
    if ($lstCaptionDept == "Yes") {
        print "<td><font face='Verdana' size='2'>Dept</font></td>";
        $userrhsselection = $userrhsselection . "--Dept";
    } else {
        $colcount++;
    }
    if ($lstCaptionDiv == "Yes") {
        print "<td><font face='Verdana' size='2'>Div/Desg</font></td>";
        $userrhsselection = $userrhsselection . "--Div";
    } else {
        $colcount++;
    }
    if ($lstCaptionRemark == "Yes") {
        print "<td><font face='Verdana' size='2'>Rmk</font></td>";
        $userrhsselection = $userrhsselection . "--Remark";
    } else {
        $colcount++;
    }
    //    if ($txtPhone != "") {
            print "<td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "PhoneColumnName"] . "</font></td>";
    //        $userrhsselection = $userrhsselection . "--Phone";
    //    } else {
    //        $colcount++;
    //    }
    for ($i = 0; $i < $dayCount; $i++) {
        $next = strtotime(substr($txtFrom, 0, 2) . "-" . substr($txtFrom, 3, 2) . "-" . substr($txtFrom, 6, 4) . " + " . $i . " day");
        $a = getDate($next);
        if ($lstHourDetails == "No" || $lstHourDetails == "IN/OUT") {
            print "<td><font face='Verdana' size='2'>" . $a["mday"] . "</font></td>";
        } else {
            print "<td colspan='3'><font face='Verdana' size='2'>" . $a["mday"] . "</font></td>";
        }
    }
    if ($lstCaptionTotal == "No") {
        print "<td colspan='3' align='center'><font face='Verdana' size='2'><b>WKD</b></font></td> <td colspan='3' align='center'><font face='Verdana' size='2'><b>PXY</b></font></td> <td colspan='3' align='center'><font face='Verdana' size='2'><b>FLG</b></font></td> <td colspan='3' align='center'><font face='Verdana' size='2'><b>SAT</b></font></td> <td colspan='3' align='center'><font face='Verdana' size='2'><b>SUN</b></font></td>";
    } else {
        $userrhsselection = $userrhsselection . "--Total";
    }
    if ($lstType == "LateIn-EarlyOut") {
        print "<td align='center'><font face='Verdana' size='2'><b>LIH</b></font></td> <td align='center'><font face='Verdana' size='2'><b>EOH</b></font></td>";
    }
    print "<td colspan='4' align='center' bgcolor='#F0F0F0'><font face='Verdana' size='2'><b>TLH</b></font></td>";
    if ($lstCaptionTotal == "No") {
        print "<td colspan='3' align='center'><font face='Verdana' size='2'><b>NS</b></font></td>";
    }
    print "</tr>";
    print "<tr>";
    for ($i = 0; $i < 7 - $colcount; $i++) {
        print "<td><font face='Verdana' size='1'>&nbsp;</font></td>";
    }
    for ($i = 0; $i < $dayCount; $i++) {
        $next = strtotime(substr($txtFrom, 0, 2) . "-" . substr($txtFrom, 3, 2) . "-" . substr($txtFrom, 6, 4) . " + " . $i . " day");
        $a = getDate($next);
        if ($lstHourDetails == "No" || $lstHourDetails == "IN/OUT") {
            substr($a["weekday"], 0, 1);
            print "<td><a title='" . $a["weekday"] . "'><font face='Verdana' size='1'>" . substr($a["weekday"], 0, 1) . "</font></a></td>";
        } else {
            substr($a["weekday"], 0, 1);
            print "<td colspan='3'><a title='" . $a["weekday"] . "'><font face='Verdana' size='1'>" . substr($a["weekday"], 0, 1) . "</font></a></td>";
        }
    }
    if ($lstCaptionTotal == "No") {
        for ($i = 0; $i < 5; $i++) {
            print "<td><font face='Verdana' size='1'><b>N</b></font></td> <td><font face='Verdana' size='1'><b>O</b></font></td><td><font face='Verdana' size='1'><b>AO</b></font></td>";
        }
    }
    if ($lstType == "LateIn-EarlyOut") {
        for ($i = 0; $i < 2; $i++) {
            print "<td><font face='Verdana' size='1'>&nbsp;</font></td>";
        }
    }
    print "<td><font face='Verdana' size='1'><b>N</b></font></td> <td><font face='Verdana' size='1'><b>O</b></font></td><td><font face='Verdana' size='1'><b>AO</b></font></td><td><font face='Verdana' size='1'><b>N+AO</b></font></td>";
    if ($lstCaptionTotal == "No") {
        print "<td><font face='Verdana' size='1'><b>N</b></font></td> <td><font face='Verdana' size='1'><b>O</b></font></td><td><font face='Verdana' size='1'><b>AO</b></font></td>";
    }
    print "</tr></thead>";
    if ($lstHourDetails == "Yes") {
        print "<tr>";
        for ($i = 0; $i < 6 - $colcount; $i++) {
            print "<td><font face='Verdana' size='1'>&nbsp;</font></td>";
        }
        for ($i = 0; $i < $dayCount; $i++) {
            print "<td><font face='Verdana' size='1'>N</font></td><td><font face='Verdana' size='1'>O</font></td><td><font face='Verdana' size='1'>AO</font></td>";
        }
        if ($lstCaptionTotal == "No") {
            for ($i = 0; $i < 28; $i++) {
                print "<td><font face='Verdana' size='1'>&nbsp;</font></td>";
            }
        } else {
            for ($i = 0; $i < 4; $i++) {
                print "<td><font face='Verdana' size='1'>&nbsp;</font></td>";
            }
        }
        print "</tr>";
    }
    $row_count = 0;
    $count = 0;
    $subc = 0;
    $eid = "";
    $wkdn = 0;
    $wkdo = 0;
    $wkdao = 0;
    $pxyn = 0;
    $pxyo = 0;
    $pxyao = 0;
    $flgn = 0;
    $flgo = 0;
    $flgao = 0;
    $satn = 0;
    $sato = 0;
    $satao = 0;
    $sunn = 0;
    $suno = 0;
    $sunao = 0;
    $nsn = 0;
    $nso = 0;
    $nsao = 0;
    $nfn = 0;
    $nfo = 0;
    $nfao = 0;
    $lih = 0;
    $lieo_li = 0;
    $lieo_eo = 0;
    $txtDate = insertDate($txtFrom);
    $txtLastDate = insertDate($txtFrom);
    $data9 = "";
    $result = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
        if ($cur[3] == "") {
            $cur[3] = "&nbsp;";
        }
        if ($cur[10] == "") {
            $cur[10] = "&nbsp;";
        }
        if ($cur[11] == "") {
            $cur[11] = "&nbsp;";
        }
        if ($eid != $cur[0]) {
            if ($count != 0) {
                for ($i = $subc; $i < $dayCount; $i++) {
                    print "<td><a title='" . $i . "'><font face='Verdana' size='1'>0</font></a></td>";
                    if ($lstHourDetails == "Yes") {
                        print "<td><a title='" . $i . "'><font face='Verdana' size='1'>0</font></a></td>";
                        print "<td><a title='" . $i . "'><font face='Verdana' size='1'>0</font></a></td>";
                    }
                }
                if ($lstCaptionTotal == "No") {
                    round($wkdn / 3600, 2);
                    round($wkdo / 3600, 2);
                    round($wkdao / 3600, 2);
                    round($pxyn / 3600, 2);
                    round($pxyo / 3600, 2);
                    round($pxyao / 3600, 2);
                    round($flgn / 3600, 2);
                    round($flgo / 3600, 2);
                    round($flgao / 3600, 2);
                    round($satn / 3600, 2);
                    round($sato / 3600, 2);
                    round($satao / 3600, 2);
                    round($sunn / 3600, 2);
                    round($suno / 3600, 2);
                    round($sunao / 3600, 2);
                    print "<td><a title='Week Days Normal Hours'><font face='Verdana' size='1'><b>" . round($wkdn / 3600, 2) . "</b></font></a></td> <td><a title='Week Days OT Hours'><font face='Verdana' size='1'><b>" . round($wkdo / 3600, 2) . "</b></font></a></td> <td><a title='Week Days AOT Hours'><font face='Verdana' size='1'><b>" . round($wkdao / 3600, 2) . "</b></font></a></td> <td><a title='Proxy Days Normal Hours'><font face='Verdana' size='1'><b>" . round($pxyn / 3600, 2) . "</b></font></a></td> <td><a title='Proxy Days OT Hours'><font face='Verdana' size='1'><b>" . round($pxyo / 3600, 2) . "</b></font></a></td> <td><a title='Proxy Days AOT Hours'><font face='Verdana' size='1'><b>" . round($pxyao / 3600, 2) . "</b></font></a></td> <td><a title='Flag Days Normal Hours'><font face='Verdana' size='1'><b>" . round($flgn / 3600, 2) . "</b></font></a></td> <td><a title='Flag Days OT Hours'><font face='Verdana' size='1'><b>" . round($flgo / 3600, 2) . "</b></font></a></td> <td><a title='Flag Days AOT Hours'><font face='Verdana' size='1'><b>" . round($flgao / 3600, 2) . "</b></font></a></td> <td><a title='Saturdays Normal Hours'><font face='Verdana' size='1'><b>" . round($satn / 3600, 2) . "</b></font></a></td> <td><a title='Saturdays OT Hours'><font face='Verdana' size='1'><b>" . round($sato / 3600, 2) . "</b></font></a></td> <td><a title='Saturdays AOT Hours'><font face='Verdana' size='1'><b>" . round($satao / 3600, 2) . "</b></font></a></td> <td><a title='Sundays Normal Hours'><font face='Verdana' size='1'><b>" . round($sunn / 3600, 2) . "</b></font></a></td> <td><a title='Sundays OT Hours'><font face='Verdana' size='1'><b>" . round($suno / 3600, 2) . "</b></font></a></td> <td><a title='Sundays AOT Hours'><font face='Verdana' size='1'><b>" . round($sunao / 3600, 2) . "</b></font></a></td>";
                }
                if ($lstType == "LateIn-EarlyOut") {
                    round($lieo_li / 60, 2);
                    round($lieo_eo / 60, 2);
                    print "<td><a title='Late In Hours'><font face='Verdana' size='1'><b>" . round($lieo_li / 60, 2) . "</b></font></a></td> <td><a title='Early out Hours'><font face='Verdana' size='1'><b>" . round($lieo_eo / 60, 2) . "</b></font></a></td>";
                }
                round(($wkdn + $pxyn + $flgn + $satn + $sunn) / 3600, 2);
                round(($wkdo + $pxyo + $flgo + $sato + $suno) / 3600, 2);
                round(($wkdao + $pxyao + $flgao + $satao + $sunao) / 3600, 2);
                round(($wkdn + $pxyn + $flgn + $satn + $sunn) / 3600, 2);
                round(($wkdao + $pxyao + $flgao + $satao + $sunao) / 3600, 2);
                print "<td bgcolor='#F0F0F0'><a title='Total Normal Hours'><font face='Verdana' size='1'><b>" . round(($wkdn + $pxyn + $flgn + $satn + $sunn) / 3600, 2) . "</b></font></a></td> <td bgcolor='#F0F0F0'><a title='Total OT Hours'><font face='Verdana' size='1'><b>" . round(($wkdo + $pxyo + $flgo + $sato + $suno) / 3600, 2) . "</b></font></a></td> <td bgcolor='#F0F0F0'><a title='Total Approved OT Hours'><font face='Verdana' size='1'><b>" . round(($wkdao + $pxyao + $flgao + $satao + $sunao) / 3600, 2) . "</b></font></a></td> <td bgcolor='#F0F0F0'><a title='Total Hours'><font face='Verdana' size='1'><b>" . (round(($wkdn + $pxyn + $flgn + $satn + $sunn) / 3600, 2) + round(($wkdao + $pxyao + $flgao + $satao + $sunao) / 3600, 2)) . "</b></font></a></td>";
                if ($lstCaptionTotal == "No") {
                    round($nsn / 3600, 2);
                    round($nso / 3600, 2);
                    round($nsao / 3600, 2);
                    print "<td><a title='Night Shifts Normal Hours'><font face='Verdana' size='1'><b>" . round($nsn / 3600, 2) . "</b></font></a></td> <td><a title='Night Shifts OT Hours'><font face='Verdana' size='1'><b>" . round($nso / 3600, 2) . "</b></font></a></td> <td><a title='Night Shifts AOT Hours'><font face='Verdana' size='1'><b>" . round($nsao / 3600, 2) . "</b></font></a></td>";
                }
                print "</tr>";
                $row_count++;
            }
            addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
            print "<tr><td><a title='ID'><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'><a title='Click to view Clocking Details for the selected Period (ACTIVE EMPLOYEES ONLY)' href='ReportDailyClocking.php?act=searchRecord&prints=yes&excel=yes&subReport=yes&lstEmployeeID=" . $cur[0] . "&txtFrom=" . $txtFrom . "&txtTo=" . $txtTo . "' target='_blank'><font face='Verdana' size='1' color='#0000FF'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $cur[1] . "</font></a></td>";
            if ($lstCaptionIDNo == "Yes") {
                print "<td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $cur[10] . "</font></a></td>";
            }
            if ($lstCaptionDept == "Yes") {
                print "<td><a title='Dept'><font face='Verdana' size='1'>" . $cur[2] . "</font></a></td>";
            }
            if ($lstCaptionDiv == "Yes") {
                print "<td><a title='Div/Desg'><font face='Verdana' size='1'>" . $cur[3] . "</font></a></td>";
            }
            if ($lstCaptionRemark == "Yes") {
                print "<td><a title='Rmk'><font face='Verdana' size='1'>" . $cur[11] . "</font></a></td>";
            }
//            if ($txtPhone != "") {
                print "<td><a title='Phone'><font face='Verdana' size='1'>" . $cur[29] . "</font></a></td>";
//            }
            $eid = $cur[0];
            $subc = 0;
            $wkdn = 0;
            $wkdo = 0;
            $wkdao = 0;
            $pxyn = 0;
            $pxyo = 0;
            $pxyao = 0;
            $flgn = 0;
            $flgo = 0;
            $flgao = 0;
            $satn = 0;
            $sato = 0;
            $satao = 0;
            $sunn = 0;
            $suno = 0;
            $sunao = 0;
            $nsn = 0;
            $nso = 0;
            $nsao = 0;
            $nfn = 0;
            $nfo = 0;
            $nfao = 0;
            $lih = 0;
            $lieo_li = 0;
            $lieo_eo = 0;
            $txtDate = insertDate($txtFrom);
            $txtLastDate = insertDate($txtFrom);
        }
        while (true) {
            $subc++;
            if ($cur[9] == $txtDate || $cur[9] == $txtLastDate) {
                if ($cur[12] != "Black" && $cur[12] != "Proxy") {
                    $cur[17] = $cur[17] * $txtFlagFactor;
                    if ($lstType == "LateIn-EarlyOut") {
                        $flgn = $flgn + $cur[6] + $cur[27];
                    } else {
                        $flgn = $flgn + $cur[6];
                    }
                    $flgo = $flgo + $cur[8];
                    $flgao = $flgao + $cur[17];
                    if ($cur[14] == 1) {
                        $nfn = $nfn + $cur[6];
                        $nfo = $nfo + $cur[8];
                        $nfao = $nfao + $cur[17];
                    }
                } else {
                    if ($cur[13] == $cur[15]) {
                        $cur[17] = $cur[17] * $txtSatFactor;
                        if ($lstType == "LateIn-EarlyOut") {
                            $satn = $satn + $cur[6] + $cur[27];
                        } else {
                            $satn = $satn + $cur[6];
                        }
                        $sato = $sato + $cur[8];
                        $satao = $satao + $cur[17];
                        if ($cur[14] == 1) {
                            $nsn = $nsn + $cur[6];
                            $nso = $nso + $cur[8];
                            $nsao = $nsao + $cur[17];
                        }
                    } else {
                        if ($cur[13] == $cur[16]) {
                            $cur[17] = $cur[17] * $txtSunFactor;
                            if ($lstType == "LateIn-EarlyOut") {
                                $sunn = $sunn + $cur[6] + $cur[27];
                            } else {
                                $sunn = $sunn + $cur[6];
                            }
                            $suno = $suno + $cur[8];
                            $sunao = $sunao + $cur[17];
                            if ($cur[14] == 1) {
                                $nsn = $nsn + $cur[6];
                                $nso = $nso + $cur[8];
                                $nsao = $nsao + $cur[17];
                            }
                        } else {
                            if ($cur[12] == "Proxy") {
                                if ($lstType == "LateIn-EarlyOut") {
                                    $pxyn = $pxyn + $cur[6] + $cur[27];
                                } else {
                                    $pxyn = $pxyn + $cur[6];
                                }
                                $pxyo = $pxyo + $cur[8];
                                $pxyao = $pxyao + $cur[17];
                                if ($cur[14] == 1) {
                                    $nfn = $nfn + $cur[6];
                                    $nfo = $nfo + $cur[8];
                                    $nfao = $nfao + $cur[17];
                                }
                            } else {
                                if ($cur[12] == "Black") {
                                    if ($lstType == "LateIn-EarlyOut") {
                                        $wkdn = $wkdn + $cur[6] + $cur[27];
                                    } else {
                                        $wkdn = $wkdn + $cur[6];
                                    }
                                    $wkdo = $wkdo + $cur[8];
                                    $wkdao = $wkdao + $cur[17];
                                    if ($cur[14] == 1) {
                                        $nsn = $nsn + $cur[6];
                                        $nso = $nso + $cur[8];
                                        $nsao = $nsao + $cur[17];
                                    }
                                }
                            }
                        }
                    }
                }
                $lih = $lih + $cur[18] * 1;
                $lieo_li = $lieo_li + $cur[6] * 1;
                $lieo_eo = $lieo_eo + intval($cur[27]) * 1;
                print "<td>";
                if ($excel != "yes") {
                    if ($lstType == "LateIn-EarlyOut") {
                        print "<table border='-1' cellspacing='0' cellpadding='0'><tr>";
                        round($cur[6] / 60, 2);
                        print "<td><font face='Verdana' color='#000000' size='1'>LI: " . round($cur[6] / 60, 2) . "</font></td>";
                        round($cur[27] / 60, 2);
                        print "<td><font face='Verdana' color='#000000' size='1'>EO: " . round($cur[27] / 60, 2) . "</font></td>";
                        print "</tr></table>";
                    } else {
                        displayDate($cur[9]);
                        print "<a title='" . displayDate($cur[9]) . ": Click to view Hourly Details for the selected Period (ACTIVE EMPLOYEES ONLY)' href='ReportWork.php?act=searchRecord&prints=yes&excel=yes&subReport=yes&lstEmployeeID=" . $cur[0] . "&txtFrom=" . $txtFrom . "&txtTo=" . $txtTo . "' target='_blank'><font face='Verdana' color='#0000FF' size='1'>";
                        if ($lstCaptionPreFlag == "Yes" && $cur[12] != "Black" && $cur[12] != "Proxy") {
                            getFlagTitle($_SESSION[$session_variable . "FlagReportText"], $cur[12]);
                            print "<font color='" . $cur[12] . "'>" . getFlagTitle($_SESSION[$session_variable . "FlagReportText"], $cur[12]) . "</font>";
                        } else {
                            if ($lstHourDetails == "No") {
                                round(($cur[6] + $cur[8]) / 3600, 2);
                                print round(($cur[6] + $cur[8]) / 3600, 2);
                            } else {
                                if ($lstHourDetails == "IN/OUT") {
                                    $inout_query = "SELECT Start, Close FROM DayMaster WHERE e_id = " . $cur[0] . " AND TDate = " . $cur[9];
                                    $inout_result = selectData($conn, $inout_query);
                                    displayVirdiTime($inout_result[0]);
                                    displayVirdiTime($inout_result[1]);
                                    print displayVirdiTime($inout_result[0]) . "<br>" . displayVirdiTime($inout_result[1]);
                                } else {
                                    round($cur[6] / 3600, 2);
                                    print round($cur[6] / 3600, 2);
                                }
                            }
                        }
                        print "</font></a>";
                    }
                } else {
                    if ($lstType == "LateIn-EarlyOut") {
                        print "<table border='1' cellspacing='0' cellpadding='0'><tr>";
                        round($cur[6] / 3600, 2);
                        print "<td><font face='Verdana' color='#000000' size='1'>LI: " . round($cur[6] / 3600, 2) . "</font></td>";
                        round($cur[27] / 3600, 2);
                        print "<td><font face='Verdana' color='#000000' size='1'>EO: " . round($cur[27] / 3600, 2) . "</font></td>";
                        print "</tr></table>";
                    } else {
                        if ($lstCaptionPreFlag == "Yes" && $cur[12] != "Black" && $cur[12] != "Proxy") {
                            getFlagTitle($_SESSION[$session_variable . "FlagReportText"], $cur[12]);
                            print "<font color='" . $cur[12] . "'>" . getFlagTitle($_SESSION[$session_variable . "FlagReportText"], $cur[12]) . "</font>";
                        } else {
                            if ($lstHourDetails == "No") {
                                round(($cur[6] + $cur[8]) / 3600, 2);
                                print "<font face='Verdana' color='#000000' size='1'>" . round(($cur[6] + $cur[8]) / 3600, 2) . "</font>";
                            } else {
                                if ($lstHourDetails == "IN/OUT") {
                                    print "<font face='Verdana' color='#000000' size='1'>";
                                    $inout_query = "SELECT Start, Close FROM DayMaster WHERE e_id = " . $cur[0] . " AND TDate = " . $cur[9];
                                    $inout_result = selectData($conn, $inout_query);
                                    displayVirdiTime($inout_result[0]);
                                    displayVirdiTime($inout_result[1]);
                                    print displayVirdiTime($inout_result[0]) . "<br>" . displayVirdiTime($inout_result[1]);
                                    print "</font>";
                                } else {
                                    round($cur[6] / 3600, 2);
                                    print "<font face='Verdana' color='#000000' size='1'>" . round($cur[6] / 3600, 2) . "</font>";
                                }
                            }
                        }
                    }
                }
                print "</td>";
                if ($lstHourDetails == "Yes") {
                    round($cur[8] / 3600, 2);
                    print "<td><font face='Verdana' color='#000000' size='1'>" . round($cur[8] / 3600, 2) . "</font></td>";
                    round($cur[17] / 3600, 2);
                    print "<td><font face='Verdana' color='#000000' size='1'>" . round($cur[17] / 3600, 2) . "</font></td>";
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
            displayDate($cur[9]);
            print "<td><a title='" . displayDate($cur[9]) . "'><font face='Verdana' size='1'>0</font></a></td>";
            if ($lstHourDetails == "Yes") {
                print "<td><a title='" . $i . "'><font face='Verdana' size='1'>0</font></a></td>";
                print "<td><a title='" . $i . "'><font face='Verdana' size='1'>0</font></a></td>";
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
        for ($i = $subc; $i < $dayCount; $i++) {
            print "<td><a title='" . $i . "'><font face='Verdana' size='1'>0</font></a></td>";
            if ($lstHourDetails == "Yes") {
                print "<td><a title='" . $i . "'><font face='Verdana' size='1'>0</font></a></td>";
                print "<td><a title='" . $i . "'><font face='Verdana' size='1'>0</font></a></td>";
            }
        }
        if ($lstCaptionTotal == "No") {
            round($wkdn / 3600, 2);
            round($wkdo / 3600, 2);
            round($wkdao / 3600, 2);
            round($pxyn / 3600, 2);
            round($pxyo / 3600, 2);
            round($pxyao / 3600, 2);
            round($flgn / 3600, 2);
            round($flgo / 3600, 2);
            round($flgao / 3600, 2);
            round($satn / 3600, 2);
            round($sato / 3600, 2);
            round($satao / 3600, 2);
            round($sunn / 3600, 2);
            round($suno / 3600, 2);
            round($sunao / 3600, 2);
            print "<td><a title='Week Days Normal Hours'><font face='Verdana' size='1'><b>" . round($wkdn / 3600, 2) . "</b></font></a></td> <td><a title='Week Days OT Hours'><font face='Verdana' size='1'><b>" . round($wkdo / 3600, 2) . "</b></font></a></td> <td><a title='Week Days AOT Hours'><font face='Verdana' size='1'><b>" . round($wkdao / 3600, 2) . "</b></font></a></td> <td><a title='Proxy Days Normal Hours'><font face='Verdana' size='1'><b>" . round($pxyn / 3600, 2) . "</b></font></a></td> <td><a title='Proxy Days OT Hours'><font face='Verdana' size='1'><b>" . round($pxyo / 3600, 2) . "</b></font></a></td> <td><a title='Proxy Days AOT Hours'><font face='Verdana' size='1'><b>" . round($pxyao / 3600, 2) . "</b></font></a></td> <td><a title='Flag Days Normal Hours'><font face='Verdana' size='1'><b>" . round($flgn / 3600, 2) . "</b></font></a></td> <td><a title='Flag Days OT Hours'><font face='Verdana' size='1'><b>" . round($flgo / 3600, 2) . "</b></font></a></td> <td><a title='Flag Days AOT Hours'><font face='Verdana' size='1'><b>" . round($flgao / 3600, 2) . "</b></font></a></td> <td><a title='Saturdays Normal Hours'><font face='Verdana' size='1'><b>" . round($satn / 3600, 2) . "</b></font></a></td> <td><a title='Saturdays OT Hours'><font face='Verdana' size='1'><b>" . round($sato / 3600, 2) . "</b></font></a></td> <td><a title='Saturdays AOT Hours'><font face='Verdana' size='1'><b>" . round($satao / 3600, 2) . "</b></font></a></td> <td><a title='Sundays Normal Hours'><font face='Verdana' size='1'><b>" . round($sunn / 3600, 2) . "</b></font></a></td> <td><a title='Sundays OT Hours'><font face='Verdana' size='1'><b>" . round($suno / 3600, 2) . "</b></font></a></td> <td><a title='Sundays AOT Hours'><font face='Verdana' size='1'><b>" . round($sunao / 3600, 2) . "</b></font></a></td>";
        }
        if ($lstType == "LateIn-EarlyOut") {
            round($lieo_li / 60, 2);
            round($lieo_eo / 60, 2);
            print "<td><a title='Late In Hours'><font face='Verdana' size='1'><b>" . round($lieo_li / 60, 2) . "</b></font></a></td> <td><a title='Early out Hours'><font face='Verdana' size='1'><b>" . round($lieo_eo / 60, 2) . "</b></font></a></td>";
        }
        round(($wkdn + $pxyn + $flgn + $satn + $sunn) / 3600, 2);
        round(($wkdo + $pxyo + $flgo + $sato + $suno) / 3600, 2);
        round(($wkdao + $pxyao + $flgao + $satao + $sunao) / 3600, 2);
        round(($wkdn + $pxyn + $flgn + $satn + $sunn) / 3600, 2);
        round(($wkdao + $pxyao + $flgao + $satao + $sunao) / 3600, 2);
        print "<td bgcolor='#F0F0F0'><a title='Total Normal Hours'><font face='Verdana' size='1'><b>" . round(($wkdn + $pxyn + $flgn + $satn + $sunn) / 3600, 2) . "</b></font></a></td> <td bgcolor='#F0F0F0'><a title='Total OT Hours'><font face='Verdana' size='1'><b>" . round(($wkdo + $pxyo + $flgo + $sato + $suno) / 3600, 2) . "</b></font></a></td> <td bgcolor='#F0F0F0'><a title='Total Approved OT Hours'><font face='Verdana' size='1'><b>" . round(($wkdao + $pxyao + $flgao + $satao + $sunao) / 3600, 2) . "</b></font></a></td> <td bgcolor='#F0F0F0'><a title='Total Hours'><font face='Verdana' size='1'><b>" . (round(($wkdn + $pxyn + $flgn + $satn + $sunn) / 3600, 2) + round(($wkdao + $pxyao + $flgao + $satao + $sunao) / 3600, 2)) . "</b></font></a></td>";
        if ($lstCaptionTotal == "No") {
            round($nsn / 3600, 2);
            round($nso / 3600, 2);
            round($nsao / 3600, 2);
            print "<td><a title='Night Shifts Normal Hours'><font face='Verdana' size='1'><b>" . round($nsn / 3600, 2) . "</b></font></a></td> <td><a title='Night Shifts OT Hours'><font face='Verdana' size='1'><b>" . round($nso / 3600, 2) . "</b></font></a></td> <td><a title='Night Shifts AOT Hours'><font face='Verdana' size='1'><b>" . round($nsao / 3600, 2) . "</b></font></a></td>";
        }
        print "</tr>";
        $row_count++;
    }
    print "</table>";
    print "</div></div></div></div>";
    print "<center>";
    if ($excel != "yes") {
        print "<br><p align='center'><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $row_count . "</b></font>";
    }
    if ($prints != "yes") {
        print "<br><input type='button' value='Print Report' class='btn btn-primary' onClick='checkPrint(0)'>&nbsp;&nbsp;<input type='button' value='Excel' class='btn btn-primary' onClick='checkPrint(1)'>";
        $query = "UPDATE UserMaster SET RHSSelection = '" . $userrhsselection . "', OT1F = " . $txtSatFactor . ", OT2F = " . $txtSunFactor . ", OTDF = " . $txtFlagFactor . " WHERE Username = '" . $username . "'";
        updateData($conn, $query, true);
    }
    print "</center>";
    print "</p>";
}
print "</form>";
print "</div>";
include 'footer.php';

?>