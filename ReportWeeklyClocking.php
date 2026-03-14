<?php


ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "20";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=ReportWeeklyClocking.php&message=Session Expired or Security Policy Violated");
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
    $message = "Processed Log (Shifts with Weekly Routine)";
}
$lstShift = $_POST["lstShift"];
$lstDepartment = $_POST["lstDepartment"];
$lstDivision = $_POST["lstDivision"];
$lstEmployeeIDFrom = $_POST["lstEmployeeIDFrom"];
$lstEmployeeIDTo = $_POST["lstEmployeeIDTo"];
$txtEmployeeCode = $_POST["txtEmployeeCode"];
$lstEmployeeID = $_GET["lstEmployeeID"];
$lstSort = $_POST["lstSort"];
$txtEmployee = $_POST["txtEmployee"];
$lstWeek = $_POST["lstWeek"];
if ($lstWeek == "") {
    $lstWeek = $_GET["lstWeek"];
}
$txtRemark = $_POST["txtRemark"];
$txtPhone = $_POST["txtPhone"];
$txtSNo = $_POST["txtSNo"];
$lstColourFlag = $_POST["lstColourFlag"];
if ($lstColourFlag == "") {
    $lstColourFlag = $_GET["lstColourFlag"];
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
$lstEmployeeStatus = "";
if (isset($_POST["lstEmployeeStatus"])) {
    $lstEmployeeStatus = $_POST["lstEmployeeStatus"];
} else {
    $lstEmployeeStatus = "ACT";
}
if ($prints != "yes") {
    include 'header.php';
?>
<div class="page-breadcrumb">
                    <div class="row">
                        <div class="col-12 d-flex no-block align-items-center">
                            <h4 class="page-title">Processed Log (Shifts with Weekly Routine)</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Processed Log (Shifts with Weekly Routine)
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

<?php
}
print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='ReportWeeklyClocking.php'><input type='hidden' name='act' value='searchRecord'>";
//print "<html><title>Processed Log (Shifts with Weekly Routine)</title>";
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
        header("Content-Disposition: attachment; filename=ReportWeeklyClocking.xls");
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
    //        print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Select ONE or MORE options and click 'Search Record'</b></font></td></tr>";
        } else {
//            print "<table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
//            print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Selected Options</b></font></td></tr>";
        }
        
        ?>
        <div class="row">
            <div class="col-3">
                <?php
                $query = "SELECT id, name from tgroup WHERE ShiftTypeID = 2 AND id > 1 ORDER BY name";
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
                displayColourFlag($conn, $lstColourFlag, "lstColourFlag", true, true);
                ?>
            </div>
            <div class="col-2">
                <?php 
                $query = "SELECT distinct(WeekNo), WeekNo from WeekMaster ORDER BY WeekNo";
                displayList("lstWeek", "Week: ", $lstWeek, $prints, $conn, $query, "", "", "");
                ?>
            </div>
        <?php 
            if ($prints != "yes") {
                print "<div class='col-2'>";
                displayEmployeeStatus($conn, "lstEmployeeStatus", $lstEmployeeStatus, "", "");
                print "</div>";
                print "<div class='col-2'>";
                $array = array(array("tuser.id, WeekMaster.LogDate", "Employee Code"), array("tuser.name, tuser.id, WeekMaster.LogDate", "Employee Name - Code"), array("tuser.dept, tuser.id, WeekMaster.LogDate", "Dept - Code"), array("tuser.company, tuser.dept, tuser.id, WeekMaster.LogDate", "Div/Desg - Dept - Code"), array("tuser.company, tuser.dept, tuser.group_id, tuser.id, WeekMaster.LogDate", "Div - Dept - Shift - Code"));
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
    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, WeekMaster.LogDate, WeekMaster.Start, WeekMaster.Close, WeekMaster.Seconds, WeekMaster.WeekNo, tuser.idno, tuser.remark, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, tgroup, WeekMaster WHERE WeekMaster.group_id = tgroup.id AND WeekMaster.e_id = tuser.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
    if ($lstShift != "") {
        $query = $query . " AND tgroup.id = " . $lstShift;
    }
    $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
    if ($lstColourFlag != "") {
        if ($lstColourFlag == "Black/Proxy") {
            $query = $query . " AND (WeekMaster.Flag = 'Black' OR WeekMaster.Flag = 'Proxy') ";
        } else {
            if ($lstColourFlag == "All w/o Black/Proxy") {
                $query = $query . " AND WeekMaster.Flag NOT LIKE 'Black' AND WeekMaster.Flag NOT LIKE 'Proxy'";
            } else {
                if ($lstColourFlag == "All w/o Proxy") {
                    $query = $query . " AND WeekMaster.Flag NOT LIKE 'Proxy'";
                } else {
                    $query = $query . " AND WeekMaster.Flag = '" . $lstColourFlag . "'";
                }
            }
        }
    } else {
        $query = $query . " AND WeekMaster.Flag NOT LIKE 'Delete'";
    }
    if ($lstWeek != "") {
        $query = $query . " AND WeekMaster.WeekNo = " . $lstWeek;
    }
    $query = $query . employeeStatusQuery($lstEmployeeStatus);
    $query = $query . " ORDER BY " . $lstSort;
    print '<div class="row"><div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12"><div class="card"><div class="card-body table-responsive">';
    if ($prints != "yes") {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800' class='table table-striped table-bordered dataTable' id='zero_config'>";
    } else {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%' class='table table-striped table-bordered dataTable'>";
    }
    print "<thead><tr><td><font face='Verdana' size='2'>Week</font></td> <td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Shift</font></td> <td><font face='Verdana' size='2'>Date</font></td> <td><font face='Verdana' size='2'>Start</font></td> <td><font face='Verdana' size='2'>Close</font></td> <td><font face='Verdana' size='2'>Time (Min)</font></td> <td><font face='Verdana' size='2'>Time (Hrs)</font></td></tr></thead>";
    $result = mysqli_query($conn, $query);
    for ($count = 0; $cur = mysqli_fetch_row($result); $count++) {
        if ($cur[3] == "") {
            $cur[3] = "&nbsp;";
        }
        if ($cur[10] == "") {
            $cur[10] = "&nbsp;";
        }
        if ($cur[11] == "") {
            $cur[11] = "&nbsp;";
        }
        addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
        displayDate($cur[5]);
        displayVirdiTime($cur[6]);
        displayVirdiTime($cur[7]);
        round($cur[8] / 60, 2);
        round($cur[8] / 3600, 2);
        print "<tr><td><a title='Week'><font face='Verdana' size='1'>" . $cur[9] . "</font></a></td> <td><a title='ID'><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'><font face='Verdana' size='1'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $cur[1] . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $cur[10] . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $cur[2] . "</font></a></td> <td><a title='Div/Desg'><font face='Verdana' size='1'>" . $cur[3] . "</font></a></td> <td><a title='Rmk'><font face='Verdana' size='1'>" . $cur[11] . "</font></a></td> <td><a title='Shift'><font face='Verdana' size='1'>" . $cur[4] . "</font></a></td> <td><a title='Date'><font face='Verdana' size='1'>" . displayDate($cur[5]) . "</font></a></td> <td><a title='Start'><font face='Verdana' size='1'>" . displayVirdiTime($cur[6]) . "</font></a></td> <td><a title='Close'><font face='Verdana' size='1'>" . displayVirdiTime($cur[7]) . "</font></a></td> <td><a title='Time (Min)'><font face='Verdana' size='1'>" . round($cur[8] / 60, 2) . "</font></a></td> <td><a title='Time (Hrs)'><font face='Verdana' size='1'>" . round($cur[8] / 3600, 2) . "</font></a></td> </tr>";
    }
    print "</table>";
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