<?php
ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "25";
set_time_limit(0);
ini_set("memory_limit", "-1");
ini_set("post_max_size", "0");
ini_set("max_execution_time", "0");
ini_set("max_input_time", "-1");
ini_set("max_input_vars", "419419");
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=DeletePreFlaggedRecord.php&message=Session Expired or Security Policy Violated");
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
    $message = "Delete Un-Processed Pre-Flagged Records";
}
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
    $txtFrom = displayDate(getNextDay(insertToday(), 1));
}
if ($txtTo == "") {
    if (substr(insertToday(), 6, 2) < 28) {
        $txtTo = "28/" . substr(displayToday(), 3, 7);
    } else {
        $txtTo = displayDate(getNextDay(insertToday(), 1));
    }
}
$txtSNo = $_POST["txtSNo"];
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
$lstRotateShift = $_POST["lstRotateShift"];
$lstOTType = $_POST["lstOTType"];
$txtOTH = $_POST["txtOTH"];
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

if ($act == "deleteRecord") {
    $count = $_POST["txtCount"];
    for ($i = 0; $i < $count; $i++) {
        if ($_POST["chkDelete" . $i] != "") {
            $query = "DELETE FROM FlagDayRotation WHERE e_date = " . $_POST["txhADate" . $i] . " AND e_id = " . $_POST["txhEID" . $i];
            updateIData($iconn, $query, true);
            $text = "Deleted Pre Flagged Record for Employee ID: " . $_POST["txhEID" . $i] . " Date: " . displaydate($_POST["txhADate" . $i]) . " Flag: " . $_POST["txhFlag" . $i];
            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
            updateIData($iconn, $query, true);
        }
    }
    $message = "Record(s) deleted Successfully";
    $act = "searchRecord";
}
if ($excel != "yes") {
    
}
if ($prints != "yes") {
    include 'header.php';
?>
<div class="page-breadcrumb">
                    <div class="row">
                        <div class="col-12 d-flex no-block align-items-center">
                            <h4 class="page-title">Delete Un-Processed Pre-Flagged Records</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Delete Un-Processed Pre-Flagged Records
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

<?php
}
print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='DeletePreFlaggedRecord.php'><input type='hidden' name='act' value='searchRecord'>";
//print "<html><title>Delete Un-Processed Pre-Flagged Records</title>";
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
        header("Content-Disposition: attachment; filename=DeletePreFlaggedRecord.xls");
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
        //    print "<tr><td width='25%'>&nbsp;</td><td><font face='Verdana' size='1'><b>Select ONE or MORE options and click 'Search Record'</b></font></td></tr>";
        } else {
            if ($excel != "yes") {
//                print "<table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
//                print "<tr><td width='25%'>&nbsp;</td><td><font face='Verdana' size='1'><b>Selected Options</b></font></td></tr>";
            }
        }
        
        ?>
        <div class="row">
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
                if ($lstRotateShift == "0") {
                    $lstRotateShift = "No";
                } else {
                    if ($lstRotateShift == "1") {
                        $lstRotateShift = "Yes";
                    }
                }
                print "<label class='form-label'>Rotate Shift after Flag Day:</label><select name='lstRotateShift' class='select2 form-select shadow-none'> <option selected value='" . $lstRotateShift . "'>" . $lstRotateShift . "</option> <option value='1'>Yes</option> <option value='0'>No</option> <option value=''>---</option> </select>";
                ?>
            </div>
        <?php 
        if ($prints != "yes") {
            print "<div class='col-2'>";
            print "<label class='form-label'>OT Type:</label><select name='lstOTType' class='form-control'> <option selected value='" . $lstOTType . "'>" . $lstOTType . "</option> <option value='OT1'>OT1</option> <option value='OT2'>OT2</option> <option value=''>---</option> </select>";
            print "</div>";
            print "<div class='col-2'>";
            displayColourFlag($conn, $lstColourFlag, "lstColourFlag", true, true);
            print "</div>";
            print "</div>";
            print "<div class='row'>";
            print "<div class='col-2'>";
            displayEmployeeStatus($conn, "lstEmployeeStatus", $lstEmployeeStatus, "25%", "75%");
            print "</div>";
            print "<div class='col-2'>";
            $array = array(array("tuser.id, FlagDayRotation.e_date ", "Employee Code"), array("tuser.name, tuser.id, FlagDayRotation.e_date ", "Employee Name - Code"), array("tuser.dept, tuser.id, FlagDayRotation.e_date ", "Dept - Code"), array("tuser.company, tuser.dept, tuser.id, FlagDayRotation.e_date ", "Div/Desg - Dept - Code"), array("tuser.company, tuser.dept, tuser.group_id, tuser.id, FlagDayRotation.e_date ", "Div - Dept - Shift - Code"), array("FlagDayRotation.Flag, tuser.id", "Flag"));
            displaySort($array, $lstSort, 6);
            print "</div>";
            print "</div>";
            print "<div class='row'>";
            print "<div class='col-12'>";
            print "<center><input name='btSearch' type='submit' value='Search Record' class='btn btn-primary'>";
            print "</div>";
            print "</div>";
        }
        ?>
        <!--</form>-->
    </div>
</div>
<?php
 } 
print "</div></div></div></div>";

if ($act == "searchRecord") {
    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, FlagDayRotation.e_date, FlagDayRotation.Flag, FlagDayRotation.Rotate, tuser.idno, tuser.remark, FlagDayRotation.FlagDayRotationID, FlagDayRotation.OT, FlagDayRotation.OTH  FROM tuser, FlagDayRotation WHERE FlagDayRotation.e_id = tuser.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " AND FlagDayRotation.RecStat = 0 ";
    $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
    if ($txtFrom != "") {
        $query = $query . " AND FlagDayRotation.e_date >= " . insertDate($txtFrom);
    }
    if ($txtTo != "") {
        $query = $query . " AND FlagDayRotation.e_date <= " . insertDate($txtTo);
    }
    if ($lstRotateShift == "No") {
        $lstRotateShift = "0";
    } else {
        if ($lstRotateShift == "Yes") {
            $lstRotateShift = "1";
        }
    }
    if ($lstRotateShift != "") {
        $query = $query . " AND FlagDayRotation.Rotate = '" . $lstRotateShift . "'";
    }
    if ($txtOTH != "") {
        $query = $query . " AND FlagDayRotation.OTH = '" . $txtOTH . "'";
    }
    if ($lstOTType != "") {
        $query = $query . " AND FlagDayRotation.OT = '" . $lstOTType . "'";
    }
    if ($lstColourFlag != "") {
        if ($lstColourFlag == "Black/Proxy") {
            $query = $query . " AND (FlagDayRotation.Flag = 'Black' OR FlagDayRotation.Flag = 'Proxy') ";
        } else {
            if ($lstColourFlag == "All w/o Black/Proxy") {
                $query = $query . " AND FlagDayRotation.Flag NOT LIKE 'Black' AND FlagDayRotation.Flag NOT LIKE 'Proxy'";
            } else {
                if ($lstColourFlag == "All w/o Proxy") {
                    $query = $query . " AND FlagDayRotation.Flag NOT LIKE 'Proxy'";
                } else {
                    $query = $query . " AND FlagDayRotation.Flag = '" . $lstColourFlag . "'";
                }
            }
        }
    } else {
        $query = $query . " AND FlagDayRotation.Flag NOT LIKE 'Delete' ";
    }
    $query = $query . employeeStatusQuery($lstEmployeeStatus);
    $query = $query . " ORDER BY " . $lstSort;
    if ($excel != "yes") {
        print "<p align='center'><font face='Verdana' size='1'><b>" . $_SESSION[$session_variable . "FlagReportText"] . "</b></font></p>";
    }
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
    print "</font></td> <td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Date</font></td> <td><font face='Verdana' size='2'>Flag</font></td> <td><font face='Verdana' size='2'>Rotate</font></td> <td><font face='Verdana' size='2'>OT Type</font></td> <td><font face='Verdana' size='2'>OTH</font></td> </tr></thead>";
    $result = mysqli_query($iconn, $query);
    $count = 0;
    for ($bgcolor = "#FFFFFF"; $cur = mysqli_fetch_row($result); $count++) {
        if ($cur[3] == "") {
            $cur[3] = "&nbsp;";
        }
        if ($cur[5] == "") {
            $cur[5] = "&nbsp;";
        }
        if ($cur[10] == "") {
            $cur[10] = "&nbsp;";
        }
        if ($cur[11] == "") {
            $cur[11] = "&nbsp;";
        }
        if ($cur[5] == "Yellow") {
            $bgcolor = "Brown";
        } else {
            $bgcolor = "White";
        }
        print "<tr>";
        if ($prints != "yes") {
            print "<td bgcolor='" . $bgcolor . "'><input type='checkbox' name='chkDelete" . $count . "' id='chkDelete" . $count . "'> <input type='hidden' name='txhID" . $count . "' value='" . $cur[9] . "'> <input type='hidden' name='txhADate" . $count . "' value='" . $cur[4] . "'> <input type='hidden' name='txhFlag" . $count . "' value='" . $cur[5] . "'> <input type='hidden' name='txhEID" . $count . "' value='" . $cur[0] . "'> </td>";
        } else {
            print "<td bgcolor='" . $bgcolor . "'><font size='1'>&nbsp;</font> <input type='hidden' name='txhID" . $count . "' value='" . $cur[9] . "'></td>";
        }
        addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
        displayDate($cur[4]);
        print "<td bgcolor='" . $bgcolor . "'><a title='ID'><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'><font face='Verdana' size='1' color='" . $cur[5] . "'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Name'><font face='Verdana' size='1' color='" . $cur[5] . "'>" . $cur[1] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1' color='" . $cur[5] . "'>" . $cur[7] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Dept'><font face='Verdana' size='1' color='" . $cur[5] . "'>" . $cur[2] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Div/Desg'><font face='Verdana' size='1' color='" . $cur[5] . "'>" . $cur[3] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Rmk'><font face='Verdana' size='1' color='" . $cur[5] . "'>" . $cur[8] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Date'><font face='Verdana' size='1' color='" . $cur[5] . "'>" . displayDate($cur[4]) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Flag'><font face='Verdana' size='1' color='" . $cur[5] . "'>" . $cur[5] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Shift Rotation'><font face='Verdana' size='1' color='" . $cur[5] . "'>";
        if ($cur[6] == 1) {
            print "Yes";
        } else {
            print "No";
        }
        print "</font></td> <td bgcolor='" . $bgcolor . "'><a title='OT Type'><font face='Verdana' size='1' color='" . $cur[5] . "'>" . $cur[10] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='OT Hours'><font face='Verdana' size='1' color='" . $cur[5] . "'>" . $cur[11] . "</font></a></td></tr>";
    }
    print "</table>";
    if ($excel != "yes" && 0 < $count) {
        print "<p align='center'><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $count . "</b></font><input type='hidden' name='txtCount' value='" . $count . "'>";
        if ($prints != "yes" && strpos($userlevel, $current_module . "D") !== false && $_SESSION[$session_variable . "LockDate"] < insertDate($txtFrom) && $_SESSION[$session_variable . "LockDate"] < insertDate($txtTo)) {
            print "<br><input name='btSubmit' type='button' value='Delete Selected Record(s)' class='btn btn-primary' onClick='checkSubmit()'>";
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
echo "\r\n<script>\r\nfunction checkSubmit(){\t\r\n\tif (confirm('Are you sure you want to DELETE the selected Record(s)')){\r\n\t\tx = document.frm1;\r\n\t\tx.act.value='deleteRecord';\r\n\t\tx.btSubmit.disabled = true;\r\n\t\tx.submit();\r\n\t}\t\r\n}\r\n\r\nfunction checkDelete(x, y, z){\r\n\tif (x.checked == true){\r\n\t\tif (y.value == 0 || y.value == ''){\r\n\t\t\ty.value = z.value;\r\n\t\t}\r\n\t}else{\r\n\t\ty.value = 0;\t\r\n\t}\r\n}\r\n\r\nfunction checkAll(){\t\r\n\tx = document.frm1;\r\n\ty = x.chkDelete;\r\n\tz = x.txtCount.value;\t\r\n\tfor (i=0;i<z;i++){\t\t\r\n\t\tif (y.checked == true){\t\t\t\r\n\t\t\tdocument.getElementById(\"chkDelete\"+i).checked = true;\t\t\t\r\n\t\t}else{\r\n\t\t\tdocument.getElementById(\"chkDelete\"+i).checked = false;\r\n\t\t}\r\n\t}\r\n}\r\n</script>\r\n</center>";

?>