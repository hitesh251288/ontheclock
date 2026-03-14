<?php
ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if ($username == "") {
    header("Location: " . $config["REDIRECT"] . "?url=TaskMaster.php&message=Session Expired or Security Policy Violated");
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
    $message = "Employee Records";
}
$lstShift = $_POST["lstShift"];
$lstDepartment = $_POST["lstDepartment"];
$lstDivision = $_POST["lstDivision"];
$lstEmployeeIDFrom = $_POST["lstEmployeeIDFrom"];
$lstEmployeeIDTo = $_POST["lstEmployeeIDTo"];
$txtEmployeeCode = $_POST["txtEmployeeCode"];
$txtEmployee = $_POST["txtEmployee"];
$lstSort = $_POST["lstSort"];
$txtFrom = $_POST["txtFrom"];
$txtTo = $_POST["txtTo"];
if ($txtFrom == "") {
    $txtFrom = displayToday();
}
if ($txtTo == "") {
    $txtTo = displayToday();
}
$txtRemark = $_POST["txtRemark"];
$txtSNo = $_POST["txtSNo"];
$txtPhone = $_POST["txtPhone"];
$lstEmployeeStatus = "";
if (isset($_POST["lstEmployeeStatus"])) {
    $lstEmployeeStatus = $_POST["lstEmployeeStatus"];
} else {
    $lstEmployeeStatus = "ACT";
}
$lstMissingData = $_POST["lstMissingData"];
$txtOT1 = $_POST["txtOT1"];
$txtOT2 = $_POST["txtOT2"];
$lstStatus = $_POST["lstStatus"];
$lstFingerRegistered = $_POST["lstFingerRegistered"];
$lstCardRegistered = $_POST["lstCardRegistered"];
$txtStartDateFrom = $_POST["txtStartDateFrom"];
$txtStartDateTo = $_POST["txtStartDateTo"];
$txtEndDateFrom = $_POST["txtEndDateFrom"];
$txtEndDateTo = $_POST["txtEndDateTo"];
$lstSelectedDepartment = $_POST["lstSelectedDepartment"];
$lstSelectedDivision = $_POST["lstSelectedDivision"];
$txtSelectedRemark = $_POST["txtSelectedRemark"];
$txtSelectedSNo = $_POST["txtSelectedSNo"];
$txtSelectedPhone = $_POST["txtSelectedPhone"];
$lstSelectedStatus = $_POST["lstSelectedStatus"];
$lstSelectedLevel = $_POST["lstSelectedLevel"];
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
if ($act == "addTask") {
    for ($i = 0; $i < $_POST["txtCount"]; $i++) {
        if ($_POST["chk" . $i] != "") {
            $query = "INSERT INTO TaskMaster (Username, Task, TDate, EmployeeID, Schedule, Status, Importance, Type) VALUES ('" . $username . "', '" . $_POST["txtTask"] . "', '" . insertDate($_POST["txtFrom"]) . "', " . $_POST["txhID" . $i] . ", '" . $_POST["lstSchedule"] . "', 0, '" . $_POST["lstImportance"] . "', '" . $_POST["lstType"] . "') ";
            $text = "Create Task for User - " . $username . ", Task - " . $_POST["txtTask"] . ", Date - " . $_POST["txtFrom"] . ", Employee - " . $_POST["txhID" . $i] . ", Scheduled - " . $_POST["lstSchedule"] . ", Priority - " . $_POST["lstImportance"] . ", Type - " . $_POST["lstType"];
            if (updateIData($iconn, $query, true)) {
                $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                updateIData($iconn, $query, true);
            }
        }
    }
    $act = "searchRecord";
} else {
    if ($act == "deleteTask") {
        $query = "UPDATE TaskMaster SET Status = 2 WHERE TaskID = " . $_GET["id"];
        $text = "Deleted Task of User - " . $username . ", Task ID - " . $_GET["id"];
        if (updateIData($iconn, $query, true)) {
            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
            updateIData($iconn, $query, true);
        }
        $act = "searchRecord";
    } else {
        if ($act == "markComplete") {
            $query = "UPDATE TaskMaster SET Status = 1 WHERE TaskID = " . $_GET["id"];
            $text = "Complete Task Status of User - " . $username . ", Task ID - " . $_GET["id"];
            if (updateIData($iconn, $query, true)) {
                $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                updateIData($iconn, $query, true);
            }
            $act = "searchRecord";
        }
    }
}
if ($prints != "yes") {
    include 'header.php';
?>
<div class="page-breadcrumb">
                    <div class="row">
                        <div class="col-12 d-flex no-block align-items-center">
                            <h4 class="page-title">Task Schedular</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Task Schedular
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

<?php
}
//print "<html><title>Task Schedular</title>";
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
        header("Content-Disposition: attachment; filename=TaskMaster.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        print "<body>";
    }
}
print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='TaskMaster.php'><input type='hidden' name='act' value='searchRecord'>";
if ($excel != "yes") {
?>
<div class="card">
    <div class="card-body">
        <?php 
        print "<p align='center'><font face='Verdana' size='1' color='#FF0000'><b>Assigned Tasks</b></font></p>";
        $query = "SELECT TaskID, Username, Task, TDate, EmployeeID, tuser.name, Schedule, Importance FROM TaskMaster, tuser WHERE TaskMaster.EmployeeID = tuser.id AND Status = 0 AND Username = '" . $username . "' ";
        $result = mysqli_query($conn, $query);
        $count = 0;
        print "<table width='800' cellpadding='1' cellspacing='-1' class='table table-striped table-bordered dataTable'>";
        print "<tr><td><font face='Verdana' size='2'><b>Date</b></font></td> <td><font face='Verdana' size='2'><b>Task</b></font></td> <td><font face='Verdana' size='2'><b>Assigned ID</b></font></td> <td><font face='Verdana' size='2'><b>Name</b></font></td> <td><font face='Verdana' size='2'><b>Scheduled</b></font></td> <td><font face='Verdana' size='2'><b>Priority</b></font></td> <td><font face='Verdana' size='2'><b>&nbsp;</b></font></td> <td><font face='Verdana' size='2'><b>&nbsp;</b></font></td></tr>";
        while ($cur = mysqli_fetch_row($result)) {
            displayDate($cur[3]);
            print "<tr><td><font face='Verdana' size='1'>" . displayDate($cur[3]) . "</font></td> <td><font face='Verdana' size='1'>" . $cur[2] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[4] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[5] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[6] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[7] . "</font></td> <td><a href='#' onClick=javascript:deleteTask('TaskMaster.php?act=deleteTask&id=" . $cur[0] . "')><font face='Verdana' size='1'>Delete Task</font></td> <td><a href='#' onClick=javascript:markComplete('TaskMaster.php?act=markComplete&id=" . $cur[0] . "')><font face='Verdana' size='1'>Mark Complete</font></td></tr>";
        }
        print "</table>";
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
                $query = "SELECT id, name from tgroup ORDER BY name";
                displayList("lstShift", "Current Shift: ", $lstShift, $prints, $conn, $query, "", "", "");
                ?>
            </div>
            <?php 
            displaySearchFields($conn, $prints, $session_variable, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $lstEmployeeName, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
            ?>
        </div>
        <div class="row">
        <?php 
            if ($prints != "yes") {
                print "<div class='col-2'>";
                print "<label class='form-label'>Registered Finger Print:</label><select name='lstFingerRegistered' class='form-control select2 form-select shadow-none'> <option selected value='" . $lstFingerRegistered . "'>" . $lstFingerRegistered . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> <option value='---'>---</option> </select>";
                print "</div>";
                print "<div class='col-2'>";
                displayTextbox("txtOT1", "OT1 Day: ", $txtOT1, $prints, 12, "", "");
                print "</div>";
                print "<div class='col-2'>";
                print "<label class='form-label'>Registered Card:</label><select name='lstCardRegistered' class='form-control select2 form-select shadow-none'> <option selected value='" . $lstCardRegistered . "'>" . $lstCardRegistered . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> <option value='---'>---</option> </select>";
                print "</div>";
                print "<div class='col-2'>";
                displayTextbox("txtOT2", "OT2 Day: ", $txtOT2, $prints, 12, "30%", "25%");
                print "</div>";
                print "<div class='col-2'>";
                displayEmployeeStatus($conn, "lstEmployeeStatus", $lstEmployeeStatus, "25%", "35%");
                print "</div>";
                print "<div class='col-2'>";
                print "<label class='form-label'>User Level:</label><select name='lstStatus' class='form-control select2 form-select shadow-none'> <option selected value='" . $lstStatus . "'>" . $lstStatus . "</option> <option value='1'>1</option><option value='2'>2</option><option value='3'>3</option><option value='4'>4</option><option value='5'>5</option><option value='6'>6</option><option value='7'>7</option><option value='8'>8</option><option value='9'>9</option><option value='10'>10</option></select>";
                print "</div>";
                print "</div>";
                print "<div class='row'>";
                print "<div class='col-2'>";
                displayTextbox("txtStartDateFrom", "Start Date From: ", $txtStartDateFrom, $prints, 12, "", "");
                print "</div>";
                print "<div class='col-2'>";
                displayTextbox("txtEndDateFrom", "End Date From <font size='1'>(DD/MM/YYYY)</font>:", $txtEndDateFrom, $prints, 12, "", "");
                print "</div>";
                print "<div class='col-2'>";
                displayTextbox("txtStartDateTo", "Start Date To <font size='1'><font size='1'>(DD/MM/YYYY)</font></font>: ", $txtStartDateTo, $prints, 12, "", "");
                print "</div>";
                print "<div class='col-2'>";
                displayTextbox("txtEndDateTo", "End Date To <font size='1'><font size='1'>(DD/MM/YYYY)</font></font>: ", $txtEndDateTo, $prints, 12, "", "");
                print "</div>";
                print "<div class='col-2'>";
                print "<label class='form-label'>Missing Data:</label><select name='lstMissingData' class='form-control'> <option selected value='" . $lstMissingData . "'>" . $lstMissingData . "</option> <option value='Missing Name'>Missing Name</option> <option value='Missing Dept'>Missing Dept</option> <option value='Missing Div/Desg'>Missing Div/Desg</option> <option value='Missing " . $_SESSION[$session_variable . "IDColumnName"] . "'>Missing " . $_SESSION[$session_variable . "IDColumnName"] . "</option> <option value='Missing " . $_SESSION[$session_variable . "PhoneColumnName"] . "'>Missing " . $_SESSION[$session_variable . "PhoneColumnName"] . "</option> <option value='Missing Rmk'>Missing Rmk</option> <option value=''>---</option></select>";
                print "</div>";
                print "<div class='col-2'>";
                $array = array(array("tuser.id", "Employee ID"), array("tuser.name, tuser.id", "Employee Name - ID"), array("tuser.PassiveType, tuser.id", "Employee Status - ID"), array("tuser.dept, tuser.id", "Dept - ID"), array("tuser.company, tuser.dept, tuser.id", "Div/Desg - Dept - ID"), array("tuser.company, tuser.dept, tuser.group_id, tuser.id", "Div - Dept - Current Shift - ID"));
                displaySort($array, $lstSort, 6);
                print "</div>";
                print "</div>";
                print "<div class='row'>";
                print "<div class='col-12'>";
                print "<center><br><input name='btSearch' class='btn btn-primary' type='submit' value='Search Record'>";
                if (strpos($userlevel, $current_module . "D") !== false && strpos($userlevel, $current_module . "E") !== false && strpos($userlevel, $current_module . "A") !== false) {
                    print "&nbsp;&nbsp; <input name='btSearch' class='btn btn-primary' type='button' value='Change Employee ID' onClick='javascript:changeID()'>";
                }
                print "</center>";
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
    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tuser.idno, tuser.remark, tuser.phone, tuser.OT1, tuser.OT2, tuser.OldID1, tuser.datelimit, tuser.PassiveType, tuser.flagdatelimit, tuser.UserStatus, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, tgroup WHERE tuser.group_id = tgroup.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
    if ($lstMissingData != "") {
        if ($lstMissingData == "Missing Name") {
            $query = $query . " AND LENGTH(tuser.Name) < 1";
        } else {
            if ($lstMissingData == "Missing Dept") {
                $query = $query . " AND LENGTH(tuser.dept) < 1";
            } else {
                if ($lstMissingData == "Missing Div/Desg") {
                    $query = $query . " AND LENGTH(tuser.company) < 1";
                } else {
                    if ($lstMissingData == "Missing Rmk") {
                        $query = $query . " AND LENGTH(tuser.Remark) < 1";
                    } else {
                        if ($lstMissingData == "Missing " . $_SESSION[$session_variable . "IDColumnName"]) {
                            $query = $query . " AND LENGTH(tuser.IdNo) < 1";
                        } else {
                            if ($lstMissingData == "Missing " . $_SESSION[$session_variable . "PhoneColumnName"]) {
                                $query = $query . " AND LENGTH(tuser.Phone) < 1";
                            }
                        }
                    }
                }
            }
        }
    } else {
        if ($lstShift != "") {
            $query = $query . " AND tgroup.id = " . $lstShift;
        }
        $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
    }
    if ($txtOT1 != "") {
        $query = $query . " AND tuser.OT1 LIKE '" . $txtOT1 . "%'";
    }
    if ($txtOT2 != "") {
        $query = $query . " AND tuser.OT2 LIKE '" . $txtOT2 . "%'";
    }
    if ($lstStatus != "") {
        $query = $query . " AND tuser.UserStatus = " . $lstStatus;
    }
    if ($txtStartDateFrom != "") {
        $query = $query . " AND SUBSTRING(tuser.datelimit, 2, 8) >= '" . insertDate($txtStartDateFrom) . "'";
    }
    if ($txtStartDateTo != "") {
        $query = $query . " AND SUBSTRING(tuser.datelimit, 2, 8) <= '" . insertDate($txtStartDateTo) . "'";
    }
    if ($txtEndDateFrom != "") {
        $query = $query . " AND SUBSTRING(tuser.datelimit, 10, 8) >= '" . insertDate($txtEndDateFrom) . "'";
    }
    if ($txtEndDateTo != "") {
        $query = $query . " AND SUBSTRING(tuser.datelimit, 10, 8) <= '" . insertDate($txtEndDateTo) . "'";
    }
    $query .= employeeStatusQuery($lstEmployeeStatus);
    if ($lstFingerRegistered == "Yes") {
        $query = $query . " AND OCTET_LENGTH(fpdata) IS NOT NULL AND OCTET_LENGTH(fpdata) > 32 ";
    } else {
        if ($lstFingerRegistered == "No") {
            $query = $query . " AND (OCTET_LENGTH(fpdata) IS NULL OR OCTET_LENGTH(fpdata) < 32) ";
        }
    }
    if ($lstCardRegistered == "Yes") {
        $query = $query . " AND LENGTH(cardnum) > 1 ";
    } else {
        if ($lstCardRegistered == "No") {
            $query = $query . " AND (LENGTH(cardnum) < 2 OR cardnum is NULL OR cardnum = 'NULL') ";
        }
    }
    if($lstSort != ""){
        $query = $query . " ORDER BY " . $lstSort;
    }
    print '<div class="row"><div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12"><div class="card"><div class="card-body table-responsive">';
    if ($prints != "yes") {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800' class='table table-striped table-bordered dataTable'> <tr><td><font face='Verdana' size='2'><input type='checkbox' name='chkAll' onClick='javascript:checkAll()'></font></td>";
    } else {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%' class='table table-striped table-bordered dataTable'><tr>";
    }
    print "<td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "PhoneColumnName"] . "</font></td> ";
    for ($i = 0; $i < 10; $i++) {
        if ($_SESSION[$session_variable . "F" . ($i + 1)] != "") {
            print "<td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "F" . ($i + 1)] . "</font></td>";
        }
    }
    print "<td><font face='Verdana' size='2'>Current Shift</font></td> <td><font face='Verdana' size='2'>OT1</font></td> <td><font face='Verdana' size='2'>OT2</font></td> <td><font face='Verdana' size='2'>Old ID</font></td> <td><font face='Verdana' size='2'>Start Date</font></td> <td><font face='Verdana' size='2'>End Date</font></td> <td><font face='Verdana' size='2'>Status</font></td> <td><font face='Verdana' size='2'>Level</font></td>";
    print "</tr>";
    $result = mysqli_query($conn, $query);
    for ($count = 0; $cur = mysqli_fetch_row($result); $count++) {
        if ($cur[3] == "") {
            $cur[3] = "&nbsp;";
        }
        if ($cur[5] == "") {
            $cur[5] = "&nbsp;";
        }
        if ($cur[6] == "") {
            $cur[6] = "&nbsp;";
        }
        print "<tr>";
        if ($prints != "yes") {
            print "<td bgcolor='" . $bgcolor . "'><input type='hidden' name='txhID" . $count . "' value='" . $cur[0] . "'><font face='Verdana' size='2'><input type='checkbox' name='chk" . $count . "' id='chk" . $count . "'></td>";
        }
        if (strpos($userlevel, $current_module . "A") !== false && $prints != "yes") {
            addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
            print "<td><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'><a title='ID' name='" . $cur[0] . "' href='#" . $cur[0] . "' onClick='javascript:openWindow(" . $cur[0] * 419 . ")'><font face='Verdana' color='#000000' size='1'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td>";
        } else {
            addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
            print "<td><a title='ID'><font face='Verdana' color='#000000' size='1'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td>";
        }
        print "<td><a title='Name'><font face='Verdana' size='1'>" . $cur[1] . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $cur[5] . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $cur[2] . "</font></a></td> <td><a title='Div/Desg'><font face='Verdana' size='1'>" . $cur[3] . "</font></a></td> <td><a title='Rmk'><font face='Verdana' size='1'>" . $cur[6] . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "PhoneColumnName"] . "'><font face='Verdana' size='1'>" . $cur[7] . "</font></td> ";
        for ($i = 0; $i < 10; $i++) {
            if ($_SESSION[$session_variable . "F" . ($i + 1)] != "") {
                print "<td><font face='Verdana' size='1'>" . $cur[$i + 15] . "</font></td>";
            }
        }
        print "<td><a title='Current Shift'><font face='Verdana' size='1'>" . $cur[4] . "</font></a></td> <td><a title='OT1'><font face='Verdana' size='1'>" . $cur[8] . "</font></a></td> <td><a title='OT2'><font face='Verdana' size='1'>" . $cur[9] . "</font></a></td> <td><a title='Old ID'><font face='Verdana' size='1'>" . $cur[10] . "</font></a></td> <td><a title='Start Date'><font face='Verdana' size='1'>";
        if (substr($cur[11], 1, 8) == "19770430") {
            displayDate(substr($cur[13], 1, 8));
            print displayDate(substr($cur[13], 1, 8));
        } else {
            displayDate(substr($cur[11], 1, 8));
            print displayDate(substr($cur[11], 1, 8));
        }
        print "</font></a></td> <td><a title='End Date'><font face='Verdana' size='1'>";
        if (substr($cur[11], 9, 8) == "19770430") {
            displayDate(substr($cur[13], 9, 8));
            print displayDate(substr($cur[13], 9, 8));
        } else {
            displayDate(substr($cur[11], 9, 8));
            print displayDate(substr($cur[11], 9, 8));
        }
        print "</font></a></td> <td><a title='Status'><font face='Verdana' size='1'>" . $cur[12] . "</font></a></td> <td><a title='Level'><font face='Verdana' size='1'>" . $cur[14] . "</font></a></td> </tr>";
    }
    print "<input type='hidden' name='txtCount' value='" . $count . "'></table>";
    print "</div></div></div></div>";
    print '<div class="row"><div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12"><div class="card"><div class="card-body">';
    if ($excel != "yes") {
        print "<center><p align='center'><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $count . "</b></font></center>";
    }
    if ($prints != "yes") {
        if (strpos($userlevel, $current_module . "E") !== false) {
            print "<div class='row'>";
            print "<div class='col-2'>";
            displayTextbox("txtTask", "Task:", $txtTask, $prints, 150, "", "");
            print "</div>";
            print "<div class='col-2'>";
            displayTextbox("txtFrom", "Task Date <font size='1'>(DD/MM/YYYY)</font>:", $txtFrom, $prints, 12, "", "");
            print "</div>";
            print "<div class='col-2'>";
            print "<label class='form-label'>Type:</label><select name='lstType' class='form-control select2 form-select shadow-none'><option selected value = 'Email'>Email</option> <option value = 'SMS'>SMS</option></select>";
            print "</div>";
            print "<div class='col-2'>";
            print "<label class='form-label'>Priority:</label><select name='lstImportance' class='form-control select2 form-select shadow-none'><option value = 'H'>H</option> <option selected value = 'M'>M</option> <option value = 'L'>L</option></select>";
            print "</div>";
            print "<div class='col-2'>";
            print "<label class='form-label'>Scheduled:</label><select name='lstSchedule' class='form-control select2 form-select shadow-none'><option selected value = 'Yes'>Yes</option> <option value = 'No'>No</option> </select>";
            print "</div>";
            print "</div>";
            print "<div class='row'>";
            print "<div class='col-12'>";
            print "<center><br><input name='btSubmit' class='btn btn-primary' type='button' value='Add Task' onClick='javascript:addTask()'></center>";
            print "</div>";
            print "</div>";
        }
        print "<div class='row'>";
        print "<div class='col-12'>";
        print "<center><br><br><input type='button' value='Print Report' class='btn btn-primary' onClick='checkPrint(0)'>&nbsp;&nbsp;<input type='button' class='btn btn-primary' value='Excel' onClick='checkPrint(1)'></center>";
        print "</div>";
        print "</div>";
    }
    print "</p>";
}
print "</form>";
echo "\r\n<script>\r\nfunction deleteTask(a){\r\n\tif (confirm('Delete Task')){\r\n\t\twindow.location.href = a;\r\n\t}\t\r\n}\r\n\r\nfunction markComplete(a){\r\n\tif (confirm('Mark Complete')){\r\n\t\twindow.location.href = a;\r\n\t}\t\r\n}\r\n\r\nfunction addTask(){\t\r\n\tx = document.frm1;\t\r\n\tif (check_valid_date(x.txtFrom.value) == false){\r\n\t\talert('Invalid Task Date Format');\r\n\t\tx.txtFrom.focus();\r\n\t}else{\r\n\t\tif (confirm('Add Task')){\r\n\t\t\tx.act.value = 'addTask';\r\n\t\t\tx.submit();\r\n\t\t}\r\n\t}\r\n}\r\n\r\nfunction checkAll(){\t\r\n\tx = document.frm1;\r\n\ty = x.chkAll;\r\n\tz = x.txtCount.value;\t\r\n\tfor (i=0;i<z;i++){\t\t\r\n\t\tif (y.checked == true){\t\t\t\r\n\t\t\tdocument.getElementById(\"chk\"+i).checked = true;\t\t\t\r\n\t\t}else{\r\n\t\t\tdocument.getElementById(\"chk\"+i).checked = false;\r\n\t\t}\r\n\t}\r\n}\r\n\r\nfunction openWindow(a){\r\n\twindow.open(\"EmployeeChild.php?act=viewRecord&txtID=\"+a, \"\",\"height=400,width=600\");\r\n}\r\n\r\nfunction changeID(){\r\n\twindow.open(\"EmployeeChild.php?act=viewchangeIDRecord\", \"\",\"width=600,height=400\");\r\n}\r\n\r\n</script>";
print "</div></div></div></div></div>";
include 'footer.php';
?>