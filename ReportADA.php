<?php


ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "27";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!(checkSession($userlevel, $current_module) && $_SESSION[$session_variable . "VirdiLevel"] == "Classic")) {
    header("Location: " . $config["REDIRECT"] . "?url=ReportADA.php&message=Session Expired or Security Policy Violated");
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
    $message = "ADA Report";
}
$lstShift = $_POST["lstShift"];
$lstDepartment = $_POST["lstDepartment"];
$lstDivision = $_POST["lstDivision"];
$lstEmployeeIDFrom = $_POST["lstEmployeeIDFrom"];
$lstEmployeeIDTo = $_POST["lstEmployeeIDTo"];
$txtEmployeeCode = $_POST["txtEmployeeCode"];
if ($txtEmployeeCode == "") {
    $txtEmployeeCode = $_GET["txtEmployeeCode"];
}
$txtEmployee = $_POST["txtEmployee"];
$lstSort = $_POST["lstSort"];
$txtFrom = $_POST["txtFrom"];
if ($txtFrom == "") {
    $txtFrom = $_GET["txtFrom"];
}
$txtTo = $_POST["txtTo"];
if ($txtTo == "") {
    $txtTo = $_GET["txtTo"];
}
if ($txtFrom == "") {
    $txtFrom = displayDate(getLastDay(insertToday(), 1));
}
if ($txtTo == "") {
    $txtTo = displayDate(getLastDay(insertToday(), 1));
}
$txtRemark = $_POST["txtRemark"];
$txtPhone = $_POST["txtPhone"];
$txtSNo = $_POST["txtSNo"];
if ($_POST["ex3"] != "") {
    $ex3 = $_POST["ex3"];
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
$lstLastClockDate = $_POST["lstLastClockDate"];
if ($lstLastClockDate == "") {
    $lstLastClockDate = "No";
}
$lstEmployeeStatus = "";
if (isset($_POST["lstEmployeeStatus"])) {
    $lstEmployeeStatus = $_POST["lstEmployeeStatus"];
} else {
    $lstEmployeeStatus = "ADA";
}
if ($prints != "yes") {
    include 'header.php';
?>
<div class="page-breadcrumb">
                    <div class="row">
                        <div class="col-12 d-flex no-block align-items-center">
                            <h4 class="page-title">ADA Report</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            ADA Report
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

<?php
}
print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='ReportADA.php'><input type='hidden' name='act' value='searchRecord'>";
//print "<html><title>ADA Report</title>";
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
        if ($excel == "yes") {
            header("Content-type: application/x-msdownload");
            header("Content-Disposition: attachment; filename=ReportADA.xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            print "<body>";
        }
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
                displayTextbox("txtFrom", "Date From <font size='1'>(DD/MM/YYYY)</font>: ", $txtFrom, $prints, 12, "", "");
                ?>
            </div>
            <div class="col-2">
                <?php 
                print "<label class='form-label'>Display Last Clock Date:</label><select name='lstLastClockDate' class='form-select select2 shadow-none'><option selected value='" . $lstLastClockDate . "'>" . $lstLastClockDate . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option></select>";
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
            print "</div>";
            print "<div class='row'>";
            print "<div class='col-12'>";
            print "<center><br><input name='btSearch' class='btn btn-primary' type='submit' value='Search Record'><center>";
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
    print '<div class="row"><div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12"><div class="card"><div class="card-body table-responsive">';
    if ($prints != "yes") {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800' class='table table-striped table-bordered dataTable' id='zero_config'>";
    } else {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%' class='table table-striped table-bordered dataTable'>";
    }
    $count = 0;
    $rec_count = 0;
    if ($excel == "yes") {
        print "<tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Current Shift</font></td> <td><font face='Verdana' size='2'>Status</font></td>";
        if ($lstLastClockDate == "Yes") {
            print "<td><font face='Verdana' size='2'>Last Clock</font></td>";
        }
        print "<td><font face='Verdana' size='2'>ADA From</font></td> <td><font face='Verdana' size='2'>ADA To</font></td> <td><font face='Verdana' size='2'>ADA Days</font></td> </tr>";
    }
    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tuser.idno, tuser.remark, tgroup.name, ADALog.DateFrom, IFNULL(ADALog.DateTo, " . insertToday() . "), tuser.PassiveType, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, tgroup, ADALog WHERE tuser.group_id = tgroup.id AND ADALog.e_id = tuser.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
    if ($lstShift != "") {
        $query = $query . " AND tgroup.id = " . $lstShift;
    }
    $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
    if ($txtFrom != "") {
        $query = $query . " AND ADALog.DateFrom >= " . insertDate($txtFrom);
    }
    if ($txtTo != "") {
        $query = $query . " AND (ADALog.DateTo <= " . insertDate($txtTo) . " OR ADALog.DateTo IS NULL) ";
    }
    $query = $query . employeeStatusQuery($lstEmployeeStatus);
    if ($txtDetails != "yes") {
        $query = $query . " ORDER BY tuser.id ";
    }
    $days = 0;
    $data0 = "";
    $data1 = "";
    $data2 = "";
    $data3 = "";
    $data4 = "";
    $data5 = "";
    $data6 = "";
    $data7 = "";
    $data8 = "";
    $data9 = "";
    $result = mysqli_query($conn, $query);
    if (0 < mysqli_num_rows($result)) {
        print "<tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Current Shift</font></td> <td><font face='Verdana' size='2'>Status</font></td>";
        if ($lstLastClockDate == "Yes") {
            print "<td><font face='Verdana' size='2'>Last Clock</font></td>";
        }
        print "<td><font face='Verdana' size='2'>ADA From</font></td><td><font face='Verdana' size='2'>ADA To</font></td><td><font face='Verdana' size='2'>ADA Days</font></td> </tr>";
        while ($cur = mysqli_fetch_row($result)) {
            if ($cur[3] == "") {
                $cur[3] = "&nbsp;";
            }
            if ($cur[4] == "") {
                $cur[4] = "&nbsp;";
            }
            if ($cur[5] == "") {
                $cur[5] = "&nbsp;";
            }
            if ($data0 != $cur[0] && 0 < $count) {
                addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
                print "<tr><td><a title='ID'><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'><font face='Verdana' size='1'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $cur[1] . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $cur[4] . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $cur[2] . "</font></a></td> <td><a title='Div/Desg'><font face='Verdana' size='1'>" . $cur[3] . "</font></a></td> <td><a title='Rmk'><font face='Verdana' size='1'>" . $cur[5] . "</font></a></td> <td><a title='Current Shift'><font face='Verdana' size='1'>" . $cur[6] . "</font></a></td> <td><a title='Current Status'><font face='Verdana' size='1'>" . $cur[9] . "</font></a></td>";
                if ($lstLastClockDate == "Yes") {
                    $sub_query = "SELECT MAX(e_date) FROM tenter WHERE e_id = '" . $cur[0] . "' AND e_date < '" . $cur[7] . "' ";
                    $sub_result = selectData($conn, $sub_query);
                    if ($sub_result[0] == "") {
                        $sub_result[0] = "--";
                    } else {
                        $sub_result[0] = displayDate($sub_result[0]);
                    }
                    print "<td><a title='Last Clock Date'><font face='Verdana' size='1'>" . $sub_result[0] . "</font></a></td>";
                }
                displayDate($cur[7]);
                displayDate($cur[8]);
                addComma(getTotalDays(displayDate($cur[7]), displayDate($cur[8])) - 1);
                print "<td><a title='ADA From'><font face='Verdana' size='1'>" . displayDate($cur[7]) . "</font></a></td> <td><a title='ADA To'><font face='Verdana' size='1'>" . displayDate($cur[8]) . "</font></a></td> <td><a target='_blank' title='ADA Days'><font face='Verdana' size='1'>" . addComma(getTotalDays(displayDate($cur[7]), displayDate($cur[8])) - 1) . "</font></a></td></tr>";
                $days = 0;
                $rec_count++;
            }
            $data0 = $cur[0];
            $data1 = $cur[1];
            $data2 = $cur[2];
            $data3 = $cur[3];
            $data4 = $cur[4];
            $data5 = $cur[5];
            $data6 = $cur[6];
            $data7 = $cur[7];
            $data8 = $cur[8];
            $data9 = $cur[9];
            $count++;
        }
        addZero($data0, $_SESSION[$session_variable . "EmployeeCodeLength"]);
        print "<tr><td><a title='ID'><input type='hidden' name='txh" . $count . "' value='" . $data0 . "'><font face='Verdana' size='1'>" . addZero($data0, $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $data1 . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $data4 . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $data2 . "</font></a></td> <td><a title='Div/Desg'><font face='Verdana' size='1'>" . $data3 . "</font></a></td> <td><a title='Rmk'><font face='Verdana' size='1'>" . $data5 . "</font></a></td> <td><a title='Current Shift'><font face='Verdana' size='1'>" . $data6 . "</font></a></td> <td><a title='Current Status'><font face='Verdana' size='1'>" . $data9 . "</font></a></td>";
        if ($lstLastClockDate == "Yes") {
            $sub_query = "SELECT MAX(e_date) FROM tenter WHERE e_id = '" . $data0 . "' AND e_date < '" . $data7 . "' ";
            $sub_result = selectData($conn, $sub_query);
            if ($sub_result[0] == "") {
                $sub_result[0] = "--";
            } else {
                $sub_result[0] = displayDate($sub_result[0]);
            }
            print "<td><a title='Last Clock Date'><font face='Verdana' size='1'>" . $sub_result[0] . "</font></a></td>";
        }
        displayDate($data7);
        displayDate($data8);
        addComma(getTotalDays(displayDate($data7), displayDate($data8)) - 1);
        print "<td><a title='From'><font face='Verdana' size='1'>" . displayDate($data7) . "</font></a></td> <td><a title='To'><font face='Verdana' size='1'>" . displayDate($data8) . "</font></a></td> <td><a target='_blank' title='Days'><font face='Verdana' size='1'>" . addComma(getTotalDays(displayDate($data7), displayDate($data8)) - 1) . "</font></a></td></tr>";
        $rec_count++;
    }
    print "</table>";
    if ($excel != "yes") {
        print "<br><p align='center'><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $rec_count . "</b></font>";
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