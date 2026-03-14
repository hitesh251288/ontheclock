<?php


ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "25";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!(checkSession($userlevel, $current_module) && $_SESSION[$session_variable . "VirdiLevel"] == "Classic")) {
    header("Location: " . $config["REDIRECT"] . "?url=ReportPreFlag.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$act = $_REQUEST["act"] ?? "";
$prints = $_REQUEST["prints"] ?? "";
$excel = $_REQUEST["excel"] ?? "";
$message = $_REQUEST["message"] ?? "Pre Flag Report";

$lstShift = $_REQUEST["lstShift"] ?? "";
$lstDepartment = $_REQUEST["lstDepartment"] ?? "";
$lstDivision = $_REQUEST["lstDivision"] ?? "";
$lstEmployeeIDFrom = $_REQUEST["lstEmployeeIDFrom"] ?? "";
$lstEmployeeIDTo = $_REQUEST["lstEmployeeIDTo"] ?? "";
$txtEmployee = $_REQUEST["txtEmployee"] ?? "";
$txtSNo = $_REQUEST["txtSNo"] ?? "";
$lstSort = $_REQUEST["lstSort"] ?? "tuser.id";
$txtFrom = $_REQUEST["txtFrom"] ?? displayToday();
$txtTo = $_REQUEST["txtTo"] ?? displayToday();
$lstColourFlag = $_REQUEST["lstColourFlag"] ?? "";
$txtPreFlagRemark = $_REQUEST["txtPreFlagRemark"] ?? "";
$lstRotateShift = $_REQUEST["lstRotateShift"] ?? "";
$lstProcessed = $_REQUEST["lstProcessed"] ?? "";
$lstEmployeeStatus = $_REQUEST["lstEmployeeStatus"] ?? "ACT";
$txtEmployeeCode = $_REQUEST["txtEmployeeCode"] ?? "";
$txtRemark = $_REQUEST["txtRemark"] ?? "";
$txtPhone = $_REQUEST["txtPhone"] ?? "";
$txtOTH = $_REQUEST["txtOTH"] ?? "";
$txtF1 = $_REQUEST["txtF1"] ?? "";
$txtF2 = $_REQUEST["txtF2"] ?? "";
$txtF3 = $_REQUEST["txtF3"] ?? "";
$txtF4 = $_REQUEST["txtF4"] ?? "";
$txtF5 = $_REQUEST["txtF5"] ?? "";
$txtF6 = $_REQUEST["txtF6"] ?? "";
$txtF7 = $_REQUEST["txtF7"] ?? "";
$txtF8 = $_REQUEST["txtF8"] ?? "";
$txtF9 = $_REQUEST["txtF9"] ?? "";
$txtF10 = $_REQUEST["txtF10"] ?? "";
$table_name = "Access.FlagDayRotation";
if ($lstDB == "Archive") {
    $table_name = "AccessArchive.FlagDayRotation";
}
if ($prints != "yes") {
    include 'header.php';
?>
<div class="page-breadcrumb">
                    <div class="row">
                        <div class="col-12 d-flex no-block align-items-center">
                            <h4 class="page-title">Pre Flag Report</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Pre Flag Report
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

<?php
}
print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='ReportPreFlag.php'><input type='hidden' name='act' value='searchRecord'>";
//print "<html><title>Pre Flag Report</title>";
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
        header("Content-Disposition: attachment; filename=ReportPreFlag.xls");
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
                $query = "SELECT id, name from tgate ORDER BY name";
                displayList("lstTerminal", "Terminal: ", $lstTerminal, $prints, $conn, $query, "", "", "");
                ?>
            </div>
            <div class="col-2">
                <?php 
                displayTextbox("txtPreFlagRemark", "Pre Flag Remark: ", $txtPreFlagRemark, $prints, 12, "", "");
                ?>
            </div>
            <div class="col-2">
                <?php 
                displayTextbox("txtFrom", "Date From <font size='1'>(DD/MM/YYYY)</font>: ", $txtFrom, $prints, 12, "", "");
                ?>
            </div>
            <div class="col-2">
                <?php 
                displayTextbox("txtOTH", "OT Hours: ", $txtOTH, $prints, 5, "", "");
                ?>
            </div>
            <div class="col-2">
                <?php 
                displayTextbox("txtTo", "Date To <font size='1'>(DD/MM/YYYY)</font>: ", $txtTo, $prints, 12, "", "");
                ?>
            </div>
            <div class="col-2">
                <?php 
                if ($lstProcessed == "0") {
                    $lstProcessed = "No";
                } else {
                    if ($lstProcessed == "1") {
                        $lstProcessed = "Yes";
                    }
                }
                print "<label class='form-label'>Processed:</label><select name='lstProcessed' class='form-select select2 shadow-none'> <option selected value='" . $lstProcessed . "'>" . $lstProcessed . "</option> <option value='1'>Yes</option> <option value='0'>No</option> <option value=''>---</option> </select>";
                ?>
            </div>
            </div>
            
        <?php 
        if ($prints != "yes") {
            print "<div class='row'>";
            print "<div class='col-2'>";
            displayColourFlag($conn, $lstColourFlag, "lstColourFlag", true, true);
            print "</div>";
            print "<div class='col-2'>";
            if ($lstRotateShift == "0") {
                $lstRotateShift = "No";
            } else {
                if ($lstRotateShift == "1") {
                    $lstRotateShift = "Yes";
                }
            }
            print "<label class='form-label'>Rotate Shift after Flag Day:</label><select name='lstRotateShift' class='form-control'> <option selected value='" . $lstRotateShift . "'>" . $lstRotateShift . "</option> <option value='1'>Yes</option> <option value='0'>No</option> <option value=''>---</option> </select>";
            print "</div>";
            print "<div class='col-2'>";
            displayEmployeeStatus($conn, "lstEmployeeStatus", $lstEmployeeStatus, "25%", "25%");
            print "</div>";
            print "<div class='col-2'>";
            $array = array(array("tuser.id, FlagDayRotation.e_date ", "Employee Code"), array("tuser.name, tuser.id, FlagDayRotation.e_date ", "Employee Name - Code"), array("tuser.dept, tuser.id, FlagDayRotation.e_date ", "Dept - Code"), array("tuser.company, tuser.dept, tuser.id, FlagDayRotation.e_date ", "Div/Desg - Dept - Code"), array("tuser.company, tuser.dept, tuser.group_id, tuser.id, FlagDayRotation.e_date ", "Div - Dept - Shift - Code"), array("FlagDayRotation.Flag, tuser.id", "Flag"));
            displaySort($array, $lstSort, 6);
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
    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, " . $table_name . ".Flag, " . $table_name . ".e_date , tgate.name, " . $table_name . ".Remark, " . $table_name . ".Rotate, " . $table_name . ".RecStat, tuser.idno, tuser.remark, " . $table_name . ".OT, " . $table_name . ".OTH, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, tgroup, " . $table_name . ", tgate WHERE tuser.group_id = tgroup.id AND " . $table_name . ".e_id = tuser.id AND " . $table_name . ".g_id = tgate.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
    if ($lstShift != "") {
        $query = $query . " AND tgroup.id = " . $lstShift;
    }
    $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
    if ($txtFrom != "") {
        $query = $query . " AND " . $table_name . ".e_date  >= " . insertDate($txtFrom);
    }
    if ($txtTo != "") {
        $query = $query . " AND " . $table_name . ".e_date  <= " . insertDate($txtTo);
    }
    if ($lstTerminal != "") {
        $query = $query . " AND " . $table_name . ".g_id = '" . $lstTerminal . "'";
    }
    if ($txtPreFlagRemark != "") {
        $query = $query . " AND " . $table_name . ".Remark LIKE '%" . $txtPreFlagRemark . "%'";
    }
    if ($lstRotateShift == "No") {
        $lstRotateShift = "0";
    } else {
        if ($lstRotateShift == "Yes") {
            $lstRotateShift = "1";
        }
    }
    if ($lstRotateShift != "") {
        $query = $query . " AND " . $table_name . ".Rotate = '" . $lstRotateShift . "'";
    }
    if ($lstProcessed == "No") {
        $lstProcessed = "0";
    } else {
        if ($lstProcessed == "Yes") {
            $lstProcessed = "1";
        }
    }
    if ($lstProcessed != "") {
        $query = $query . " AND " . $table_name . ".RecStat = '" . $lstProcessed . "'";
    }
    if ($txtOTH != "") {
        $query = $query . " AND " . $table_name . ".OTH = '" . $txtOTH . "'";
    }
    if ($lstColourFlag != "") {
        if ($lstColourFlag == "Black/Proxy") {
            $query = $query . " AND (" . $table_name . ".Flag = 'Black' OR " . $table_name . ".Flag = 'Proxy') ";
        } else {
            if ($lstColourFlag == "All w/o Black/Proxy") {
                $query = $query . " AND " . $table_name . ".Flag NOT LIKE 'Black' AND " . $table_name . ".Flag NOT LIKE 'Proxy'";
            } else {
                if ($lstColourFlag == "All w/o Proxy") {
                    $query = $query . " AND " . $table_name . ".Flag NOT LIKE 'Proxy'";
                } else {
                    $query = $query . " AND " . $table_name . ".Flag = '" . $lstColourFlag . "'";
                }
            }
        }
    }
    $query = $query . employeeStatusQuery($lstEmployeeStatus);
    if ($lstDB != "Archive") {
        $query = $query . " ORDER BY " . $lstSort;
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
    print "<thead><tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Shift</font></td> <td><font face='Verdana' size='2'>Date</font></td> <td><font face='Verdana' size='2'>Terminal</font></td> <td><font face='Verdana' size='2'>Rotate</font></td>  <td><font face='Verdana' size='2'>Processed</font></td> <td><font face='Verdana' size='2'>Flag</font></td> <td><font face='Verdana' size='2'>OT Type</font></td> <td><font face='Verdana' size='2'>OTH</font></td> <td><font face='Verdana' size='2'>Remarks</font></td> </tr></thead>";
    $result = mysqli_query($conn, $query);
    for ($bgcolor = ""; $cur = mysqli_fetch_row($result); $count++) {
        if ($cur[5] != "") {
            $font = $cur[5];
            if ($font == "Yellow") {
                $bgcolor = "Brown";
            } else {
                $bgcolor = "#FFFFFF";
            }
        } else {
            $cur[5] = "&nbsp;";
            $font = "Black";
            $bgcolor = "#FFFFFF";
        }
        if ($cur[3] == "") {
            $cur[3] = "&nbsp;";
        }
        if ($cur[8] == "") {
            $cur[8] = "&nbsp;";
        }
        if ($cur[12] == "") {
            $cur[12] = "&nbsp;";
        }
        if ($cur[13] == "") {
            $cur[13] = "&nbsp;";
        }
        $flag = $cur[5];
        addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
        displayDate($cur[6]);
        print "<tr><td bgcolor='" . $bgcolor . "'><a title='ID'><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'><font face='Verdana' size='1' color='" . $flag . "'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Name'><font face='Verdana' size='1' color='" . $flag . "'>" . $cur[1] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1' color='" . $flag . "'>" . $cur[11] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Dept'><font face='Verdana' size='1' color='" . $flag . "'>" . $cur[2] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Div/Desg'><font face='Verdana' size='1' color='" . $flag . "'>" . $cur[3] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Rmk'><font face='Verdana' size='1' color='" . $flag . "'>" . $cur[12] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Shift'><font face='Verdana' size='1' color='" . $flag . "'>" . $cur[4] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Date'><font face='Verdana' size='1' color='" . $flag . "'>" . displayDate($cur[6]) . "</font> <td bgcolor='" . $bgcolor . "'><a title='Terminal'><font face='Verdana' size='1' color='" . $flag . "'>" . $cur[7] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Rotate Shift after Flagging Day'><font face='Verdana' size='1' color='" . $flag . "'>";
        if ($cur[9] == 0) {
            print "No";
        } else {
            print "Yes";
        }
        print "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Processed Record'><font face='Verdana' size='1' color='" . $flag . "'>";
        if ($cur[10] == 0) {
            print "No";
        } else {
            print "Yes";
        }
        print "</font></a></td>";
        print "<td bgcolor='" . $bgcolor . "'><a title='Flag'><font face='Verdana' size='1' color='" . $flag . "'>" . $cur[5] . "</font></a></td>";
        print "<td bgcolor='" . $bgcolor . "'><a title='OT Type'><font face='Verdana' size='1' color='" . $flag . "'>" . $cur[13] . "</font></a></td>";
        print "<td bgcolor='" . $bgcolor . "'><a title='OT Hours'><font face='Verdana' size='1' color='" . $flag . "'>" . $cur[14] . "</font></a></td>";
        print "<td bgcolor='" . $bgcolor . "'><a title='Remark'><font face='Verdana' size='1' color='" . $flag . "'>" . $cur[8] . "</font></a></td>";
        print "</tr>";
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