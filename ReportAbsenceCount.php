<?php


ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "18";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=ReportAbsenceCount.php&message=Session Expired or Security Policy Violated");
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
    $message = "Absence Count Report [Based on Processed Records]<br>(This Report DOES NOT include TODAY or ANY Day after TODAY)";
}
$lstShift = $_POST["lstShift"];
$lstDepartment = $_POST["lstDepartment"];
$lstDivision = $_POST["lstDivision"];
$txtFrom = $_POST["txtFrom"];
$txtTo = $_POST["txtTo"];
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
$txtSearchDate = $_POST["txtSearchDate"];
$lstEmployeeID = $_GET["lstEmployeeID"];
$lstEmployeeIDFrom = $_POST["lstEmployeeIDFrom"];
$lstEmployeeIDTo = $_POST["lstEmployeeIDTo"];
if ($lstEmployeeID != "" && $lstEmployeeIDFrom == "" && $lstEmployeeIDTo == "") {
    $lstEmployeeIDFrom = $lstEmployeeID;
    $lstEmployeeIDTo = $lstEmployeeID;
}
$txtEmployeeCode = $_POST["txtEmployeeCode"];
$lstSort = $_POST["lstSort"];
$txtEmployee = $_POST["txtEmployee"];
$txtRemark = $_POST["txtRemark"];
$txtPhone = $_POST["txtPhone"];
$txtSNo = $_POST["txtSNo"];
$txtAbsence = $_POST["txtAbsence"];
if ($txtAbsence == "") {
    $txtAbsence = 0;
}
$txtAbsenceS = $_POST["txtAbsenceS"];
if ($txtAbsenceS == "") {
    $txtAbsenceS = 0;
}
$txtAbsenceSS = $_POST["txtAbsenceSS"];
if ($txtAbsenceSS == "") {
    $txtAbsenceSS = 0;
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
                            <h4 class="page-title">Absence Count Report [Based on Processed Records]</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Absence Count Report [Based on Processed Records]
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

<?php
}
print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='ReportAbsenceCount.php'><input type='hidden' name='act' value='searchRecord'>";
//print "<html><title>Absence Count Report [Based on Processed Records]</title>";
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
        header("Content-Disposition: attachment; filename=ReportAbsenceCount.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        print "<body>";
    }
}
print "<center>";
if ($excel != "yes") {
//    displayHeader($prints, true, false);
}
print "<center>";
if ($prints != "yes") {
//    displayLinks($current_module, $userlevel);
}
print "</center>";

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
                displayTextbox("txtAbsenceSS", "Absence Excluding OT1 & OT2>=: ", $txtAbsenceSS, $prints, 12, "", "");
                ?>
            </div>
            <div class="col-2">
                <?php 
                displayTextbox("txtTo", "Date To <font size='1'>(DD/MM/YYYY)</font>: ", $txtTo, $prints, 12, "", "");
                ?>
            </div>
            <div class="col-2">
                <?php 
                displayTextbox("txtAbsenceS", "Absence Excluding OT2>=: ", $txtAbsenceS, $prints, 12, "", "");
                ?>
            </div>
        <?php 
        if ($prints != "yes") {
            print "<div class='col-2'>";
            displayEmployeeStatus($conn, "lstEmployeeStatus", $lstEmployeeStatus, "25%", "35%");
            print "</div>";
            print "<div class='col-2'>";
            displayTextbox("txtAbsence", "Absence >=: ", $txtAbsence, $prints, 12, "15%", "25%");
            print "</div>";
            print "</div>";
            print "<div class='row'>";
            print "<div class='col-2'>";
            $array = array(array("tuser.id", "Employee Code"), array("tuser.name, tuser.id", "Employee Name - Code"), array("tuser.dept, tuser.id", "Dept - Code"), array("tuser.company, tuser.dept, tuser.id", "Div/Desg - Dept - Code"), array("tuser.company, tuser.dept, tuser.group_id, tuser.id", "Div - Dept - Current Shift - Code"));
            displaySort($array, $lstSort, 5);
            print "</div>";
            print "</div>";
            print "<div class='row'>";
            print "<div class='col-12'>";
            print "<center><br><input type='hidden' name='txtSearchDate'><input name='btSearch' type='submit' class='btn btn-primary' value='Search Record'></center>";
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
        print "<p align='center'><font face='Verdana' size='1'><b><font size='2'>OT1 = Saturday; OT2 = Sunday <br>A = Total Absent Days ; A/S = Absent Days EXCLUDING Sunday / OT2 ; A/SS = Absent Days EXCLUDING Saturdays and Sundays / OT1 and OT2";
        print "</b></font></p>";
    }
    print '<div class="row"><div class="col-md-12"><div class="card"><div class="card-body table-responsive">';
    if ($prints != "yes") {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800' class='table table-striped table-bordered dataTable' id='zero_config'>";
    } else {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%' class='table table-striped table-bordered dataTable'>";
    }
    $dayCount = getTotalDays($txtFrom, $txtTo);
    $count = 0;
    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tuser.idno, tuser.remark, tuser.OT1, tuser.OT2, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, tgroup WHERE SUBSTRING(tuser.datelimit, 2, 8) < '" . insertDate($txtTo) . "0000' AND tuser.group_id = tgroup.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
    if ($lstShift != "") {
        $query = $query . " AND tgroup.id = " . $lstShift;
    }
    $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
    $query = $query . employeeStatusQuery($lstEmployeeStatus);
    $query = $query . " ORDER BY " . $lstSort;
//    print "<tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Current Shift</font></td> <td><font face='Verdana' size='2'>A</font></td> <td><font face='Verdana' size='2'>A/S</font></td> <td><font face='Verdana' size='2'>A/SS</font></td> </tr>";
    print "<thead><tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td><td><font face='Verdana' size='2'>A</font></td> <td><font face='Verdana' size='2'>A/S</font></td> <td><font face='Verdana' size='2'>A/SS</font></td> </tr></thead>";
    $result = mysqli_query($conn, $query);
    if (0 < mysqli_num_rows($result)) {
        while ($cur = mysqli_fetch_row($result)) {
            $satCount = getDayCount(insertDate($txtFrom), insertDate($txtTo), $dayCount, $cur[7]);
            $sunCount = getDayCount(insertDate($txtFrom), insertDate($txtTo), $dayCount, $cur[8]);
            $sub_query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster WHERE EmployeeID = '" . $cur[0] . "'";
            if ($txtFrom != "") {
                $sub_query = $sub_query . " AND AttendanceMaster.ADate >= '" . insertDate($txtFrom) . "'";
            }
            if ($txtTo != "") {
                $sub_query = $sub_query . " AND AttendanceMaster.ADate <= '" . insertDate($txtTo) . "'";
            }
            $flag_query = "SELECT * FROM TLSFlag";
            $flag_result = mysqli_query($conn,$flag_query);
            while ($flag_cur = mysqli_fetch_row($flag_result)) {
                for ($i = 0; $i <= 22; $i++) {
                    if ($flag_cur[$i] == "No") {
                        $sub_query .= " AND AttendanceMaster.Flag <> '" . mysqli_fetch_field_direct($flag_result, $i)->name . "' ";
                    }
                }
            }
            $sub_result = selectData($conn, $sub_query);
            $total = $sub_result[0];
            $sub_query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster WHERE EmployeeID = '" . $cur[0] . "' AND Day = OT1";
            if ($txtFrom != "") {
                $sub_query = $sub_query . " AND AttendanceMaster.ADate >= '" . insertDate($txtFrom) . "'";
            }
            if ($txtTo != "") {
                $sub_query = $sub_query . " AND AttendanceMaster.ADate <= '" . insertDate($txtTo) . "'";
            }
            $sub_result = selectData($conn, $sub_query);
            $ot1 = $sub_result[0];
            $sub_query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster WHERE EmployeeID = '" . $cur[0] . "' AND Day = OT2";
            if ($txtFrom != "") {
                $sub_query = $sub_query . " AND AttendanceMaster.ADate >= '" . insertDate($txtFrom) . "'";
            }
            if ($txtTo != "") {
                $sub_query = $sub_query . " AND AttendanceMaster.ADate <= '" . insertDate($txtTo) . "'";
            }
            $sub_result = selectData($conn, $sub_query);
            $ot2 = $sub_result[0];
            if ($cur[3] == "") {
                $cur[3] = "&nbsp;";
            }
            if ($cur[5] == "") {
                $cur[5] = "&nbsp;";
            }
            if ($cur[6] == "") {
                $cur[6] = "&nbsp;";
            }
            $flag = false;
            if (is_Numeric($txtAbsence) && $txtAbsence <= $dayCount - $total && is_Numeric($txtAbsenceS) && $txtAbsenceS <= $dayCount - $sunCount - ($total - $ot2) && is_Numeric($txtAbsenceSS) && $txtAbsenceSS <= $dayCount - ($satCount + $sunCount) - ($total - ($ot1 + $ot2))) {
                $flag = true;
            }
            if ($flag) {
                addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
//                print "<tr><td><a title='ID'><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'><font face='Verdana' size='1'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $cur[1] . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $cur[5] . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $cur[2] . "</font></a></td> <td><a title='Div/Desg'><font face='Verdana' size='1'>" . $cur[3] . "</font></a></td> <td><a title='Rmk'><font face='Verdana' size='1'>" . $cur[6] . "</font></a></td> <td><a title='Current Shift'><font face='Verdana' size='1'>" . $cur[4] . "</font></a></td> <td><a title='Absence'><font face='Verdana' size='1'>" . ($dayCount - $total) . "</font></a></td> <td><a title='Absence Excluding OT2'><font face='Verdana' size='1'>" . ($dayCount - $sunCount - ($total - $ot2)) . "</font></a></td> <td><a title='Absence'><font face='Verdana' size='1'>" . ($dayCount - ($satCount + $sunCount) - ($total - ($ot1 + $ot2))) . "</font></a></td> </tr>";
                print "<tr><td><a title='ID'><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'><font face='Verdana' size='1'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $cur[1] . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $cur[5] . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $cur[2] . "</font></a></td> <td><a title='Div/Desg'><font face='Verdana' size='1'>" . $cur[3] . "</font></a></td> <td><a title='Rmk'><font face='Verdana' size='1'>" . $cur[6] . "</font></a></td><td><a title='Absence'><font face='Verdana' size='1'>" . ($dayCount - $total) . "</font></a></td> <td><a title='Absence Excluding OT2'><font face='Verdana' size='1'>" . ($dayCount - $sunCount - ($total - $ot2)) . "</font></a></td> <td><a title='Absence'><font face='Verdana' size='1'>" . ($dayCount - ($satCount + $sunCount) - ($total - ($ot1 + $ot2))) . "</font></a></td> </tr>";
                $count++;
            }
        }
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
echo "\r\n<script>\r\nfunction checkDay(){\r\n\tvar x = document.frm1;\r\n\tif (x.txtFrom.value != '' && check_valid_date(x.txtFrom.value) == false){\r\n\t\talert('Invalid From Date. Date Format should be DD/MM/YYYY');\r\n\t\tx.txtFrom.focus();\r\n\t}else{\r\n\t\tvar date = new Date(x.txtFrom.value);\r\n\t\talert(date);\r\n\t\t//x.submit();\r\n\t}\r\n}\r\n</script>";
print "</div></div></div></div></div>";
echo "</center>";
include 'footer.php';

?>