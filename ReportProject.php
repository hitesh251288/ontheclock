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
    header("Location: " . $config["REDIRECT"] . "?url=ReportProject.php&message=Session Expired or Security Policy Violated");
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
    $message = "Project Assignment Report";
}
$lstShift = $_POST["lstShift"];
$lstDepartment = $_POST["lstDepartment"];
$lstDivision = $_POST["lstDivision"];
$lstProject = $_POST["lstProject"];
$lstEmployeeIDFrom = $_POST["lstEmployeeIDFrom"];
$lstEmployeeIDTo = $_POST["lstEmployeeIDTo"];
$txtEmployee = $_POST["txtEmployee"];
$txtSNo = $_POST["txtSNo"];
$lstSort = $_POST["lstSort"];
if ($lstSort == "") {
    $lstSort = "ProjectLog.ProjectLogID";
}
$txtFrom = $_POST["txtFrom"];
$txtTo = $_POST["txtTo"];
if ($txtFrom == "") {
    $txtFrom = $_GET["txtFrom"];
}
if ($txtTo == "") {
    $txtTo = $_GET["txtTo"];
}
if ($txtFrom == "") {
    $txtFrom = displayToday();
}
if ($txtTo == "") {
    $txtTo = displayToday();
}
$txtTimeFrom = $_POST["txtTimeFrom"];
$txtTimeTo = $_POST["txtTimeTo"];
if ($txtTimeFrom == "") {
    $txtTimeFrom = "000000";
}
if ($txtTimeTo == "") {
    $txtTimeTo = "235959";
}
$lstType = $_POST["lstType"];
if ($lstType == "") {
    $lstType = "Detailed";
}
$lstEmployeeStatus = "";
if (isset($_POST["lstEmployeeStatus"])) {
    $lstEmployeeStatus = $_POST["lstEmployeeStatus"];
} else {
    $lstEmployeeStatus = "ACT";
}
$txtEmployeeCode = $_POST["txtEmployeeCode"];
$txtRemark = $_POST["txtRemark"];
$txtPhone = $_POST["txtPhone"];
$txtDayMasterID = $_GET["txtDayMasterID"];
$txtWeekMasterID = $_GET["txtWeekMasterID"];
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
                            <h4 class="page-title">Project Assignment Report</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Project Assignment Report
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

<?php
}
print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='ReportProject.php'><input type='hidden' name='act' value='searchRecord'>";
//print "<html><title>Project Assignment Report</title>";
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
        header("Content-Disposition: attachment; filename=ReportProject.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        print "<body>";
    }
}
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
                $query = "SELECT ProjectID, Name from ProjectMaster ORDER BY Name";
                displayList("lstProject", "Project: ", $lstProject, $prints, $conn, $query, "", "", "");
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
        <?php 
        if ($prints != "yes") {
            print "<div class='col-2'>";
            displayEmployeeStatus($conn, "lstEmployeeStatus", $lstEmployeeStatus, "", "");
            print "</div>";
            print "<div class='col-2'>";
            print "<label class='form-label'>Report Type:</label><select size='1' class='select2 form-select shadow-none' name='lstType'><option value='" . $lstType . "'>" . $lstType . "</option> <option value='Detailed'>Detailed</option> <option value='Summary - Sorted by Project Code'>Summary - Sorted by Project Code</option> <option value='Summary - Sorted by Employees'>Summary - Sorted by Employees</option></select>";
            print "</div>";
            print "<div class='col-2'>";
            print "<label class='form-label'>Sort Detailed Report By:</label><select class='select2 form-select shadow-none' name='lstSort'><option selected value='ProjectLog.e_date'>Date</option> <option value='ProjectMaster.Code, tuser.name, ProjectLog.e_date'>Project Code</option> <option value='ProjectMaster.Name, tuser.name, ProjectLog.e_date'>Project Name</option> <option value='tuser.id, ProjectLog.e_date'>Employee Code</option> <option value='tuser.name, ProjectLog.e_date'>Employee Name</option> <option value='tuser.dept, tuser.name, ProjectLog.e_date'>Department</option> <option value='tuser.company, ProjectLog.e_date'>Division</option></select>";
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
    $count = 0;
    if ($lstType == "Detailed") {
        $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, ProjectMaster.Name, ProjectLog.e_date, ProjectLog.tfrom, ProjectLog.tto, ProjectLog.twork, ProjectMaster.Code, tuser.idno, tuser.remark, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, tgroup, ProjectMaster, ProjectLog WHERE tuser.group_id = tgroup.id AND ProjectLog.e_id = tuser.id AND ProjectLog.ProjectID = ProjectMaster.ProjectID " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
        if ($lstShift != "") {
            $query = $query . " AND tgroup.id = " . $lstShift;
        }
        $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
        if ($txtFrom != "") {
            $query = $query . " AND ProjectLog.e_date >= " . insertDate($txtFrom);
        }
        if ($txtTo != "") {
            $query = $query . " AND ProjectLog.e_date <= " . insertDate($txtTo);
        }
        if ($txtDayMasterID != "") {
            $query = $query . " AND ProjectLog.DayMasterID = " . $txtDayMasterID;
        }
        if ($txtWeekMasterID != "") {
            $query = $query . " AND ProjectLog.WeekMasterID = " . $txtWeekMasterID;
        }
        $query = $query . employeeStatusQuery($lstEmployeeStatus);
        $query = $query . " ORDER BY " . $lstSort;
        print '<div class="row"><div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12"><div class="card"><div class="card-body table-responsive">';
        if ($prints != "yes") {
            print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800' class='table table-striped table-bordered dataTable' id='zero_config'>";
        } else {
            print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%' class='table table-striped table-bordered dataTable'>";
        }
        print "<thead><tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Shift</font></td> <td><font face='Verdana' size='2'>Date</font></td> <td><font face='Verdana' size='2'>Project</font></td> <td><font face='Verdana' size='2'>Time From</font></td> <td><font face='Verdana' size='2'>Time To</font></td> <td><font face='Verdana' size='2'>Time Worked<br>(Min)</font></td> <td><font face='Verdana' size='2'>Time Worked<br>(Hrs)</font></td></tr></thead>";
        for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $count++) {
            if ($cur[3] == "") {
                $cur[3] = "&nbsp;";
            }
            if ($cur[11] == "") {
                $cur[11] = "&nbsp;";
            }
            if ($cur[12] == "") {
                $cur[12] = "&nbsp;";
            }
            addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
            displayDate($cur[6]);
            displayVirdiTime($cur[7]);
            displayVirdiTime($cur[8]);
            round($cur[9] / 60, 2);
            round($cur[9] / 3600, 2);
            print "<tr><td><a title='ID'><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'><font face='Verdana' size='1'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $cur[1] . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $cur[11] . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $cur[2] . "</font></a></td> <td><a title='Div/Desg'><font face='Verdana' size='1'>" . $cur[3] . "</font></a></td> <td><a title='Rmk'><font face='Verdana' size='1'>" . $cur[12] . "</font></a></td> <td><a title='Shift'><font face='Verdana' size='1'>" . $cur[4] . "</font></a></td> <td><a title='Date'><font face='Verdana' size='1'>" . displayDate($cur[6]) . "</font> <td><a title='Project'><font face='Verdana' size='1'>" . $cur[10] . ": <br>" . $cur[5] . "</font></a></td> <td><a title='Time From'><font face='Verdana' size='1'>" . displayVirdiTime($cur[7]) . "</font></a></td> <td><a title='Time To'><font face='Verdana' size='1'>" . displayVirdiTime($cur[8]) . "</font></a></td> <td><a title='Time Worked (Min)'><font face='Verdana' size='1'>" . round($cur[9] / 60, 2) . "</font></a></td> <td><a title='Time Worked (Hrs)'><font face='Verdana' size='1'>" . round($cur[9] / 3600, 2) . "</font></a></td></tr>";
        }
        print "</table>";
    } else {
        $query = "SELECT SUM(ProjectLog.twork), ProjectLog.e_id, tuser.name, tuser.dept, tuser.company, ProjectMaster.Code, ProjectMaster.Name, tuser.idno, tuser.remark, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, ProjectMaster, ProjectLog WHERE ProjectLog.e_id = tuser.id AND ProjectLog.ProjectID = ProjectMaster.ProjectID ";
        if ($lstDepartment != "") {
            $query = $query . " AND tuser.dept LIKE '" . $lstDepartment . "%'";
        }
        if ($lstDivision != "") {
            $query = $query . " AND tuser.company = '" . $lstDivision . "%'";
        }
        if ($lstProject != "") {
            $query = $query . " AND ProjectLog.ProjectID = " . $lstProject;
        }
        if ($lstEmployeeIDFrom != "") {
            $query = $query . " AND tuser.id >= " . $lstEmployeeIDFrom;
        }
        if ($lstEmployeeIDTo != "") {
            $query = $query . " AND tuser.id <= " . $lstEmployeeIDTo;
        }
        if ($txtEmployeeCode != "") {
            $query = $query . " AND tuser.id = " . $txtEmployeeCode * 1;
        }
        if ($txtEmployee != "") {
            $query = $query . " AND tuser.name like '%" . $txtEmployee . "%'";
        }
        if ($txtSNo != "") {
            $query = $query . " AND tuser.idno like '%" . $txtSNo . "%'";
        }
        if ($txtFrom != "") {
            $query = $query . " AND ProjectLog.e_date >= " . insertDate($txtFrom);
        }
        if ($txtTo != "") {
            $query = $query . " AND ProjectLog.e_date <= " . insertDate($txtTo);
        }
        if ($txtDayMasterID != "") {
            $query = $query . " AND ProjectLog.DayMasterID = " . $txtDayMasterID;
        }
        if ($txtWeekMasterID != "") {
            $query = $query . " AND ProjectLog.WeekMasterID = " . $txtWeekMasterID;
        }
        $query = $query . employeeStatusQuery($lstEmployeeStatus);
        $query = $query . " GROUP BY ProjectLog.e_id, tuser.name, tuser.dept, tuser.company, ProjectMaster.Code, ProjectMaster.Name, tuser.idno, tuser.remark ";
        if ($lstType == "Summary - Sorted by Employees") {
            $query = $query . " ORDER BY ProjectLog.e_id, ProjectMaster.Code";
        } else {
            $query = $query . " ORDER BY ProjectMaster.Code, ProjectLog.e_id";
        }
        if ($prints != "yes") {
            print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800'>";
        } else {
            print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%'>";
        }
        print "<tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Project Code</font></td> <td><font face='Verdana' size='2'>Project Name</font></td> <td><font face='Verdana' size='2'>Time Worked<br>(Min)</font></td> <td><font face='Verdana' size='2'>Time Worked<br>(Hrs)</font></td></tr>";
        for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $count++) {
            if ($cur[4] == "") {
                $cur[4] = "&nbsp;";
            }
            if ($cur[7] == "") {
                $cur[7] = "&nbsp;";
            }
            if ($cur[8] == "") {
                $cur[8] = "&nbsp;";
            }
            addZero($cur[1], $_SESSION[$session_variable . "EmployeeCodeLength"]);
            round($cur[0] / 60, 2);
            round($cur[0] / 3600, 2);
            print "<tr><td><a title='ID'><input type='hidden' name='txh" . $count . "' value='" . $cur[1] . "'><font face='Verdana' size='1'>" . addZero($cur[1], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $cur[2] . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $cur[7] . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $cur[3] . "</font></a></td> <td><a title='Div/Desg'><font face='Verdana' size='1'>" . $cur[4] . "</font></a></td> <td><a title='Rmk'><font face='Verdana' size='1'>" . $cur[8] . "</font></a></td> <td><a title='Project Code'><font face='Verdana' size='1'>" . $cur[5] . "</font></a></td> <td><a title='Project Name'><font face='Verdana' size='1'>" . $cur[6] . "</font></a></td> <td><a title='Time Worked (Min)'><font face='Verdana' size='1'>" . round($cur[0] / 60, 2) . "</font></a></td> <td><a title='Time Worked (Hrs)'><font face='Verdana' size='1'>" . round($cur[0] / 3600, 2) . "</font></a></td></tr>";
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
echo "\r\n<script>\r\nfunction checkPrint(a){\r\n\tvar x = document.frm1;\r\n\tif (check_valid_date(x.txtFrom.value) == false){\r\n\t\talert('Invalid From Date. Date Format should be DD/MM/YYYY');\r\n\t\tx.txtFrom.focus();\r\n\t}else if (check_valid_date(x.txtTo.value) == false){\r\n\t\talert('Invalid To Date. Date Format should be DD/MM/YYYY');\r\n\t\tx.txtTo.focus();\r\n\t}else{\r\n\t\tif (a == 0){\r\n\t\t\tif (confirm('Go Green - Think Twice before you Print this Document \\nAre you sure want to Print?')){\r\n\t\t\t\tx.action = 'ReportProject.php?prints=yes';\t\t\t\r\n\t\t\t}else{\r\n\t\t\t\treturn;\r\n\t\t\t}\r\n\t\t}else{\r\n\t\t\tx.action = 'ReportProject.php?prints=yes&excel=yes';\t\t\t\r\n\t\t}\r\n\t\tx.target = '_blank';\r\n\t\tx.submit();\r\n\t}\r\n}\r\n\r\nfunction checkSearch(){\r\n\tvar x = document.frm1;\r\n\tif (check_valid_date(x.txtFrom.value) == false){\r\n\t\talert('Invalid From Date. Date Format should be DD/MM/YYYY');\r\n\t\tx.txtFrom.focus();\r\n\t}else if (check_valid_date(x.txtTo.value) == false){\r\n\t\talert('Invalid To Date. Date Format should be DD/MM/YYYY');\r\n\t\tx.txtTo.focus();\r\n\t}else{\r\n\t\tx.action = 'ReportProject.php?prints=no';\r\n\t\tx.target = '_self';\r\n\t\tx.submit();\r\n\t}\r\n}\r\n</script>\r\n</center>";
print "</div></div></div></div></div>";
include 'footer.php';

?>