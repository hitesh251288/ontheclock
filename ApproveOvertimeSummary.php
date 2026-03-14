<?php
ob_start("ob_gzhandler");
error_reporting(E_ALL);
set_time_limit(0);
ini_set('display_errors', '1');
include "Functions.php";
$current_module = "15";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
$lstIgnoreActualOT = $_SESSION[$session_variable . "ApproveOTIgnoreActual"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=ApproveOvertimeSummary.php&message=Session Expired or Security Policy Violated");
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
    if ($lstIgnoreActualOT == "Yes") {
        $message = "Approve Overtime (Details) <br> No ADD Rights on this Module PROHIBITS Approvals ABOVE Pre-Approved (AP2) OT <br> No DELETE Rights on this Module PROHIBITS Approvals ABOVE Approved OT";
    } else {
        $message = "Approve Overtime (Details) <br> No ADD Rights on this Module PROHIBITS Approvals ABOVE Actual OR Pre-Approved (AP2) OT <br> No DELETE Rights on this Module PROHIBITS Approvals ABOVE Actual OR Approved OT";
    }
}
$lstShift = $_POST["lstShift"];
$lstDepartment = $_POST["lstDepartment"];
$lstDivision = $_POST["lstDivision"];
$lstDay = $_POST["lstDay"];
$lstEmployeeIDFrom = $_POST["lstEmployeeIDFrom"];
$lstEmployeeIDTo = $_POST["lstEmployeeIDTo"];
$txtEmployee = $_POST["txtEmployee"];
if ($txtEmployee == "") {
    $txtEmployee = $_GET["txtEmployee"];
}
$txtEmployeeCode = $_POST["txtEmployeeCode"];
if ($txtEmployeeCode == "") {
    $txtEmployeeCode = $_GET["txtEmployeeCode"];
}
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
$lstColourFlag = $_POST["lstColourFlag"];
$lstSort = $_POST["lstSort"];
$txtRemark = $_POST["txtRemark"];
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

if ($act == "editRecord") {
    $count = $_POST["txtCount"];
    $update_flag = false;
    for ($i = 0; $i < $count; $i++) {
        if (is_numeric($_POST["txtAOT" . $i] * 60) && 0 <= $_POST["txtAOT" . $i] && $_POST["txtAOT" . $i] != $_POST["txhOldAOT" . $i]) {
            if (strpos($userlevel, $current_module . "A") !== false) {
                $update_flag = true;
            }
            if ($update_flag && $_POST["txtAOT" . $i] != $_POST["txhOldAOT" . $i]) {
                $aot = $_POST["txtAOT" . $i] * 3600;
                $counter = 1;
                $query = "UPDATE AttendanceMaster SET AOvertime = 0 WHERE EmployeeID = '" . $_POST["txh" . $i] . "' AND ADate >= '" . $_POST["txhFrom" . $i] . "' AND ADate <= '" . $_POST["txhTo" . $i] . "' ";
                if (updateIData($iconn, $query, true)) {
                    $query = "SELECT AttendanceID, Overtime FROM AttendanceMaster WHERE EmployeeID = '" . $_POST["txh" . $i] . "' AND ADate >= '" . $_POST["txhFrom" . $i] . "' AND ADate <= '" . $_POST["txhTo" . $i] . "' ";
                    $result = mysqli_query($conn, $query);
                    for ($rows = mysqli_num_rows($result); $cur = mysqli_fetch_row($result); $counter++) {
                        if ($counter == $rows && 0 < $aot) {
                            $query = "UPDATE AttendanceMaster SET AOvertime = " . $aot . ", Remark = '" . $_POST["txtARemark" . $i] . "' WHERE AttendanceID = " . $cur[0];
                            if (updateIData($iconn, $query, true)) {
                                $aot = 0;
                            } else {
                                $update_flag = false;
                            }
                        } else {
                            if ($cur[1] < $aot) {
                                $query = "UPDATE AttendanceMaster SET AOvertime = " . $cur[1] . ", Remark = '" . $_POST["txtARemark" . $i] . "' WHERE AttendanceID = " . $cur[0];
                                if (updateIData($iconn, $query, true)) {
                                    $aot = $aot - $cur[1];
                                } else {
                                    $update_flag = false;
                                }
                            } else {
                                $query = "UPDATE AttendanceMaster SET AOvertime = " . $aot . ", Remark = '" . $_POST["txtARemark" . $i] . "' WHERE AttendanceID = " . $cur[0];
                                if (updateIData($iconn, $query, true)) {
                                    $aot = 0;
                                } else {
                                    $update_flag = false;
                                }
                            }
                        }
                    }
                }
                if ($update_flag) {
                    $text = "Approve Summary OT for ID: " . $_POST["txh" . $i] . " - " . $_POST["txtAOT" . $i] . " Hours From " . displayDate($_POST["txhFrom" . $i]) . " To " . displayDate($_POST["txhTo" . $i]) . ", Set Remark = " . $_POST["txtARemark" . $i];
                    $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                    updateIData($iconn, $query, true);
                    $message = "Record(s) saved Successfully";
                }
            }
        }
    }
    $act = "searchRecord";
}

if ($prints != "yes") {
    include 'header.php';
?>
<div class="page-breadcrumb">
                    <div class="row">
                        <div class="col-12 d-flex no-block align-items-center">
                            <h4 class="page-title">Approve Overtime (Summary)</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Approve Overtime (Summary)
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

<?php
}
print "<form name='frm1' id='frm1' method='post' onSubmit='return checkSearch()' action='ApproveOvertimeSummary.php'><input type='hidden' name='act' value='searchRecord'> <input type='hidden' name='lstIgnoreActualOT' value='" . $lstIgnoreActualOT . "'>";
//print "<html><title>Approve Overtime (Summary)</title>";
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
        header("Content-Disposition: attachment; filename=ApproveOvertimeSummary.xls");
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
        if ($excel != "yes") {
            print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b></font></p>";
        }
        if ($prints != "yes") {
            print "<center><h4 class='card-title'>Select ONE or MORE options and click Search Record</h4></center>";
//            print "<table width='800' cellpadding='1' cellspacing='-1'>";
//            print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Select ONE or MORE options and click 'Search Record'</b></font></td></tr>";
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
                displayTextbox("txtTo", "Date To <font size='1'>(DD/MM/YYYY)</font>: ", $txtTo, $prints, 12, "", "");
                ?>
            </div>
            <div class="col-2">
                <?php 
                displayTextbox("txtARemark", "Attendance Remark: ", $txtARemark, $prints, 12, "", "");
                ?>
            </div>
            <div class="col-2">
                <?php 
                displayEmployeeStatus($conn, "lstEmployeeStatus", $lstEmployeeStatus, "", "");
                ?>
            </div>
        <!--</div>-->
        <?php 
        if ($prints != "yes") {
            
            if (strpos($userlevel, $current_module . "D") !== false) {
                print "<input type='hidden' name='txhDeleteRight' value='1'>";
            } else {
                print "<input type='hidden' name='txhDeleteRight' value='0'>";
            }
            print "<div class='col-2'>";
            $array = array(array("tuser.id, AttendanceMaster.ADate", "Employee Code"), array("tuser.name, tuser.id, AttendanceMaster.ADate", "Employee Name - Code"), array("tuser.dept, tuser.id, AttendanceMaster.ADate", "Dept - Code"), array("tuser.company, tuser.dept, tuser.id, AttendanceMaster.ADate", "Div/Desg - Dept - Code"), array("tuser.company, tuser.dept, tuser.group_id, tuser.id, AttendanceMaster.ADate", "Div - Dept - Current Shift - Code"));
            displaySort($array, $lstSort, 5);
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
print "</div></div></div>";
if ($act == "searchRecord") {
    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, SUM(AttendanceMaster.Overtime), SUM(AttendanceMaster.AOvertime), tuser.idno, tuser.remark FROM tuser, tgroup, AttendanceMaster WHERE tuser.group_id = tgroup.id AND AttendanceMaster.EmployeeID = tuser.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
    if ($lstShift != "") {
        $query = $query . " AND tgroup.id = " . $lstShift;
    }
    if ($txtARemark != "") {
        $query = $query . " AND AttendanceMaster.Remark LIKE '%" . $txtARemark . "%'";
    }
    $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
    if ($txtFrom != "") {
        $query = $query . " AND AttendanceMaster.ADate >= " . insertDate($txtFrom);
    }
    if ($txtTo != "") {
        $query = $query . " AND AttendanceMaster.ADate <= " . insertDate($txtTo);
    }
    $query = $query . employeeStatusQuery($lstEmployeeStatus);
    $query = $query . " GROUP BY tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tuser.idno, tuser.remark ORDER BY " . $lstSort;
    print '<div class="row"><div class="col-md-12"><div class="card"><div class="card-body table-responsive">';
    if ($prints != "yes") {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800' class='table table-striped table-bordered dataTable'>";
    } else {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%' class='table table-striped table-bordered dataTable'>";
    }
    print "<thead><tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Cur Shift</font></td> <td><font face='Verdana' size='2'>OT</font></td> <td><font face='Verdana' size='2'>App OT</font></td> <td><font face='Verdana' size='2'>";
    if ($prints != "yes") {
        print "<input type='checkbox' name='chkAOT' onClick='javascript:approveAll()'>";
    } else {
        print "&nbsp;";
    }
    print "</font></td> <td><a name='copyRemarkAll' href='#copyRemarkAll' onClick='javascript:copyRemarkAll()' title='Click Here to COPY the Remark from FIRST Row to all the below BLANK Remarks'><font face='Verdana' size='2'>A Remark</font></a></td></tr>";
    print "<tr><td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>Hrs</font></td> <td><font face='Verdana' size='2'>Hrs</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><a name='resetRemarkAll' href='#resetRemarkAll' onClick='javascript:resetRemarkAll()' title='Click Here to RESET the Remark of all Rows'><font face='Verdana' size='1'>Reset</font></a></td></tr>";
    print "</thead>";
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
        addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
        round($cur[5] / 3600, 2);
        round($cur[5] / 3600, 2);
        print "<tr><td><a title='ID'><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'><font face='Verdana' size='1'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $cur[1] . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $cur[7] . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $cur[2] . "</font></a></td> <td><a title='Div/Desg'><font face='Verdana' size='1'>" . $cur[3] . "</font></a></td> <td><a title='Rmk'><font face='Verdana' size='1'>" . $cur[8] . "</font></a></td> <td><a title='Shift'><font face='Verdana' size='1'>" . $cur[4] . "</font></a></td> <td><a title='OT (Hrs) - Click to Check Details' href='ApproveOvertime.php?act=searchRecord&txtEmployeeCode=" . $cur[0] . "&txtFrom=" . $txtFrom . "&txtTo=" . $txtTo . "' target='_blank'><font face='Verdana' size='1'>" . round($cur[5] / 3600, 2) . "</font><input type='hidden' name='txhAOT" . $count . "' id='txhAOT" . $count . "' value='" . round($cur[5] / 3600, 2) . "'></td>";
        if ($prints != "yes") {
            round($cur[6] / 3600, 2);
            insertDate($txtFrom);
            insertDate($txtTo);
            round($cur[6] / 3600, 2);
            print "<td><a title='Approved OT (Hrs)'><input type='hidden' id='txhOldAOT" . $count . "' name='txhOldAOT" . $count . "' value='" . round($cur[6] / 3600, 2) . "'> <input type='hidden' name='txhFrom" . $count . "' value='" . insertDate($txtFrom) . "'> <input type='hidden' name='txhTo" . $count . "' value='" . insertDate($txtTo) . "'> <input size='5' name='txtAOT" . $count . "' id='txtAOT" . $count . "' value='" . round($cur[6] / 3600, 2) . "' onBlur='javascript:checkOTValue(this, document.frm1.txhAOT" . $count . ", document.frm1.txhOldAOT" . $count . ")' class='form-control'></td> <td><input type='checkbox' name='chkAOT" . $count . "' id='chkAOT" . $count . "' onClick='javascript:approveOT(document.frm1.chkAOT" . $count . ", document.frm1.txtAOT" . $count . ", document.frm1.txhAOT" . $count . ", document.frm1.txhOldAOT" . $count . ")'> </a></td><td><input size='12' name='txtARemark" . $count . "' value='' class='form-control'></td>";
        } else {
            round($cur[6] / 3600, 2);
            print "<td><font face='Verdana' size='1'>" . round($cur[6] / 3600, 2) . "</td> <td><font size='1'>&nbsp;</font></td> <td><font size='1'>&nbsp;</font></td>";
        }
        print "</tr>";
    }
    print "</table>";
    if ($excel != "yes") {
        print "<p align='center'><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $count . "</b></font><input type='hidden' name='txtCount' value='" . $count . "'>";
        if ($prints != "yes" && strpos($userlevel, $current_module . "E") !== false && $_SESSION[$session_variable . "LockDate"] < insertDate($txtFrom) && $_SESSION[$session_variable . "LockDate"] < insertDate($txtTo)) {
            print "<br><br><input name='btSubmit' type='button' class='btn btn-primary' value='Save Changes' onClick='submitRecord()'>";
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
//echo "\r\n<script>\r\nfunction submitRecord(){\r\n\tx = document.frm1;\r\n\tx.act.value='editRecord';\r\n\tx.submit();\r\n}\r\n\r\nfunction approveOT(x, y, z, zz){\t\r\n\tif (x.checked == true){\r\n\t\tif (y.value == 0 || y.value == ''){\r\n\t\t\tif (document.frm1.txhDeleteRight.value == 0 && document.frm1.lstIgnoreActualOT.value == \"Yes\"  && z.value*1 > zz.value*1){\r\n\t\t\t\talert('Approved OT value has to be LESS THAN Approved OT value');\r\n\t\t\t\tx.checked = false;\r\n\t\t\t}else if (document.frm1.txhDeleteRight.value == 0 && document.frm1.lstIgnoreActualOT.value == \"No\" && (z.value*1 > y.value*1 || z.value*1 > zz.value*1)){\r\n\t\t\t\talert('Approved OT value has to be LESS THAN OT/ Approved OT value');\r\n\t\t\t\tx.checked = false;\r\n\t\t\t}else{\r\n\t\t\t\ty.value = z.value;\r\n\t\t\t}\r\n\t\t}\r\n\t}else{\r\n\t\ty.value = 0;\t\r\n\t}\r\n}\r\n\r\nfunction checkOTValue(x, y, z){\t\r\n\tif (x.value == '' || x.value*1 != x.value/1 || x.value*1 > 1440){\r\n\t\talert('Please enter a valid Approved OT Value');\r\n\t\tx.focus();\r\n\t\treturn;\r\n\t}else if (document.frm1.txhDeleteRight.value == 0 && document.frm1.lstIgnoreActualOT.value == \"Yes\"  && x.value*1 > z.value*1){\r\n\t\talert('Approved OT value has to be LESS THAN Approved OT value');\r\n\t\tx.focus();\r\n\t\treturn;\r\n\t}else if (document.frm1.txhDeleteRight.value == 0 && document.frm1.lstIgnoreActualOT.value == \"No\"  && (x.value*1 > y.value*1 || x.value*1 > z.value*1)){\r\n\t\talert('Approved OT value has to be LESS THAN OT/ Approved OT value');\r\n\t\tx.focus();\r\n\t\treturn;\r\n\t}\r\n}\r\n\r\nfunction approveAll(){\t\r\n\tx = document.frm1;\r\n\ty = x.chkAOT;\r\n\tz = x.txtCount.value;\r\n\tx.btSearch.disabled = true;\r\n\tx.btSubmit.disabled = true;\r\n\ty.disabled = true;\r\n\tfor (i=0;i<z;i++){\t\t\r\n\t\tif (y.checked == true){\t\t\t\r\n\t\t\tdocument.getElementById(\"chkAOT\"+i).checked = true;\r\n\t\t\tif (document.getElementById(\"txtAOT\"+i).value == 0 || document.getElementById(\"txtAOT\"+i).value == ''){\r\n\t\t\t\tif (document.frm1.txhDeleteRight.value == 0){\r\n\t\t\t\t\t//Do Nothing\r\n\t\t\t\t}else{\r\n\t\t\t\t\tdocument.getElementById(\"txtAOT\"+i).value = document.getElementById(\"txhAOT\"+i).value;\r\n\t\t\t\t}\r\n\t\t\t}\r\n\t\t}else{\r\n\t\t\tdocument.getElementById(\"chkAOT\"+i).checked = false;\r\n\t\t\tdocument.getElementById(\"txtAOT\"+i).value = 0;\t\r\n\t\t}\r\n\t}\r\n\tx.btSearch.disabled = false;\r\n\tx.btSubmit.disabled = false;\r\n\ty.disabled = false;\r\n}\r\n\r\nfunction copyRemarkAll(){\r\n\tif (confirm(\"COPY Attendance Remark from FIRST row to all other BLANK Remark Fields\")){\r\n\t\tvar x = document.frm1;\r\n\t\tvar count = x.txtCount.value;\r\n\t\tif (count > 0){\r\n\t\t\tif (x.txtARemark0.value != \"\"){\r\n\t\t\t\tfor (i=0;i<count;i++){\r\n\t\t\t\t\tif (document.getElementById(\"txtARemark\"+i).value == \"\" || document.getElementById(\"txtARemark\"+i).value == \".\"){\r\n\t\t\t\t\t\tdocument.getElementById(\"txtARemark\"+i).value = x.txtARemark0.value;\r\n\t\t\t\t\t}\r\n\t\t\t\t}\r\n\t\t\t}\r\n\t\t}\r\n\t}\r\n}\r\n\r\nfunction resetRemarkAll(){\r\n\tif (confirm(\"Reset All Attendance Remarks\")){\r\n\t\tvar x = document.frm1;\r\n\t\tvar count = x.txtCount.value;\t\r\n\t\tfor (i=0;i<count;i++){\r\n\t\t\tdocument.getElementById(\"txtARemark\"+i).value = \"\";\r\n\t\t}\r\n\t}\t\r\n}\r\n</script>\r\n";

?>
    <script>
     function submitRecord() {
        x = document.frm1;
        x.act.value = 'editRecord';
        x.submit();
    }

    function approveOT(x, y, z, zz) {
        if (x.checked == true) {
            if (y.value == 0 || y.value == '') {
                if (document.frm1.txhDeleteRight.value == 0 && document.frm1.lstIgnoreActualOT.value == "Yes" && z.value * 1 > zz.value * 1) {
                    alert('Approved OT value has to be LESS THAN Approved OT value');
                    x.checked = false;
                } else if (document.frm1.txhDeleteRight.value == 0 && document.frm1.lstIgnoreActualOT.value == "No" && (z.value * 1 > y.value * 1 || z.value * 1 > zz.value * 1)) {
                    alert('Approved OT value has to be LESS THAN OT/ Approved OT value');
                    x.checked = false;
                } else {
                    y.value = z.value;
                }
            }
        } else {
            y.value = 0;
        }
    }

    function checkOTValue(x, y, z) {
        if (x.value == '' || x.value * 1 != x.value / 1 || x.value * 1 > 1440) {
            alert('Please enter a valid Approved OT Value');
            x.focus();
            return;
        } else if (document.frm1.txhDeleteRight.value == 0 && document.frm1.lstIgnoreActualOT.value == "Yes" && x.value * 1 > z.value * 1) {
            alert('Approved OT value has to be LESS THAN Approved OT value');
            x.focus();
            return;
        } else if (document.frm1.txhDeleteRight.value == 0 && document.frm1.lstIgnoreActualOT.value == "No" && (x.value * 1 > y.value * 1 || x.value * 1 > z.value * 1)) {
            alert('Approved OT value has to be LESS THAN OT/ Approved OT value');
            x.focus();
            return;
        }
    }

    function approveAll() {
        x = document.frm1;
        y = x.chkAOT;
        z = x.txtCount.value;
        x.btSearch.disabled = true;
        x.btSubmit.disabled = true;
        y.disabled = true;
        for (i = 0; i < z; i++) {
            if (y.checked == true) {
                document.getElementById("chkAOT" + i).checked = true;
                if (document.getElementById("txtAOT" + i).value == 0 || document.getElementById("txtAOT" + i).value == '') {
                    if (document.frm1.txhDeleteRight.value == 0) {//Do Nothing
                    } else {
                        document.getElementById("txtAOT" + i).value = document.getElementById("txhAOT" + i).value;
                    }
                }
            } else {
                document.getElementById("chkAOT" + i).checked = false;
                document.getElementById("txtAOT" + i).value = 0;
            }
        }
        x.btSearch.disabled = false;
        x.btSubmit.disabled = false;
        y.disabled = false;
    }

    function copyRemarkAll() {
        if (confirm("COPY Attendance Remark from FIRST row to all other BLANK Remark Fields")) {
            var x = document.frm1;
            var count = x.txtCount.value;
            if (count > 0) {
                if (x.txtARemark0.value != "") {
                    for (i = 0; i < count; i++) {
                        if (document.getElementById("txtARemark" + i).value == "" || document.getElementById("txtARemark" + i).value == ".") {
                            document.getElementById("txtARemark" + i).value = x.txtARemark0.value;
                        }
                    }
                }
            }
        }
    }

    function resetRemarkAll() {
        if (confirm("Reset All Attendance Remarks")) {
            var x = document.frm1;
            var count = x.txtCount.value;
            for (i = 0; i < count; i++) {
                document.getElementById("txtARemark" + i).value = "";
            }
        }
    }
        </script>