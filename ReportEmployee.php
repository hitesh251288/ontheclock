<?php
ob_start("ob_gzhandler");
error_reporting(0);
include "Functions.php";
$current_module = "18";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
$VirdiLevel = $_SESSION[$session_variable . "VirdiLevel"];
$txtMACAddress = $_SESSION[$session_variable . "MACAddress"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=ReportEmployee.php&message=Session Expired or Security Policy Violated");
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
    $message = "Employee Records";
}
$lstShift = $_REQUEST["lstShift"] ?? "";
$lstDepartment = $_REQUEST["lstDepartment"] ?? "";
$lstDivision = $_REQUEST["lstDivision"] ?? "";
$lstEmployeeIDFrom = $_REQUEST["lstEmployeeIDFrom"] ?? "";
$lstEmployeeIDTo = $_REQUEST["lstEmployeeIDTo"] ?? "";
$txtEmployeeCode = $_REQUEST["txtEmployeeCode"] ?? "";
$txtEmployee = $_REQUEST["txtEmployee"] ?? "";
$lstSort = $_REQUEST["lstSort"] ?? "tuser.id";  // set default to fix SQL error
$txtFrom = $_REQUEST["txtFrom"] ?? displayToday();
$txtTo = $_REQUEST["txtTo"] ?? displayToday();
$txtRemark = $_REQUEST["txtRemark"] ?? "";
$txtPhone = $_REQUEST["txtPhone"] ?? "";
$txtSNo = $_REQUEST["txtSNo"] ?? "";
$txtOT1 = $_REQUEST["txtOT1"] ?? "";
$txtOT2 = $_REQUEST["txtOT2"] ?? "";
$txtOldID1 = $_REQUEST["txtOldID1"] ?? "";
$lstEmployeeStatus = $_REQUEST["lstEmployeeStatus"] ?? "ACT";
$lstFingerRegistered = $_REQUEST["lstFingerRegistered"] ?? "";
$lstCardRegistered = $_REQUEST["lstCardRegistered"] ?? "";
$lstMissingData = $_REQUEST["lstMissingData"] ?? "";
$txtStartDateFrom = $_REQUEST["txtStartDateFrom"] ?? "";
$txtStartDateTo = $_REQUEST["txtStartDateTo"] ?? "";
$txtEndDateFrom = $_REQUEST["txtEndDateFrom"] ?? "";
$txtEndDateTo = $_REQUEST["txtEndDateTo"] ?? "";
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
if ($act == "viewRecord") {
    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tuser.group_id FROM tuser WHERE tuser.id = " . $_GET["txtID"] . " " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
    $result = selectData($conn, $query);
    print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='ReportEmployee.php?act=editRecord'>";
    print "<table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
    print "<tr>";
    displayTextbox("txtID", "Employee ID: ", $_GET["txtID"], "yes", 12, "25%", "75%");
    print "</tr>";
    print "<tr>";
    displayTextbox("txtName", "Employee Name: ", $cur[1], "", 12, "25%", "75%");
    print "</tr>";
    print "<tr>";
    $query = "SELECT distinct(dept), dept from tuser " . $_SESSION[$session_variable . "DeptAccessWhereQuery"] . " ORDER BY dept";
    displayList("lstDepartment", "Department: ", $cur[2], $prints, $conn, $query, "", "25%", "75%");
    print "</tr>";
    print "<tr>";
    $query = "SELECT distinct(company), company from tuser " . $_SESSION[$session_variable . "DivAccessWhereQuery"] . " ORDER BY company";
    displayList("lstDivision", "Div/Desg: ", $cur[3], $prints, $conn, $query, "", "25%", "75%");
    print "</tr>";
    print "<tr>";
    $query = "SELECT id, name from tgroup ORDER BY name";
    displayList("lstShift", "Current Shift: ", $cur[4], $prints, $conn, $query, "", "25%", "75%");
    print "</tr>";
    if (strpos($userlevel, $current_module) !== false) {
        print "<tr>";
        print "<td>&nbsp;</td><td><input type='button' onClick='javascript:checkEdit()'></td>";
        print "</tr>";
    }
    print "</table>";
    print "</form>";
}
if ($prints != "yes") {
    include 'header.php';
?>
<div class="page-breadcrumb">
                    <div class="row">
                        <div class="col-12 d-flex no-block align-items-center">
                            <h4 class="page-title">Employee Records</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Employee Records
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
				<center><form method="post" action="RealTimeSyncTuser.php">
					<button type="submit" name="push" class="btn btn-primary">Push Data</button>
				</form></center>

<?php
}

print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='ReportEmployee.php'><input type='hidden' name='act' value='searchRecord'>";
//print "<html><title>Employee Records</title>";
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
        header("Content-Disposition: attachment; filename=ReportEmployee.xls");
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
		if (isset($_GET['msg'])) {
			echo "<center><div style='color: green; font-weight: bold;'>" . htmlspecialchars($_GET['msg']) . "</div></center>";
		}
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
                if ($VirdiLevel == "Classic" || $txtMACAddress == "2C-44-FD-84-1C-A8") {
                    print "<label class='form-label'>Registered Finger Print:</label><select name='lstFingerRegistered' class='form-select select2 shadow-none'> <option selected value='" . $lstFingerRegistered . "'>" . $lstFingerRegistered . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> <option value='---'>---</option> </select>";
                } else {
                    displayTextbox("lstFingerRegistered", "Registered Finger Print:", "---", "yes", 12, "", "");
                }
                print "</div>";
                print "<div class='col-2'>";
                displayTextbox("txtOT1", "OT1 Day: ", $txtOT1, $prints, 12, "", "");
                print "</div>";
                print "<div class='col-2'>";
                if ($VirdiLevel == "Classic" || $txtMACAddress == "2C-44-FD-84-1C-A8") {
                    print "<label class='form-label'>Registered Card:</label><select name='lstCardRegistered' class='form-select select2 shadow-none'> <option selected value='" . $lstCardRegistered . "'>" . $lstCardRegistered . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> <option value='---'>---</option> </select>";
                } else {
                    displayTextbox("lstCardRegistered", "Registered Card:", "---", "yes", 12, "", "");
                }
                print "</div>";
                print "<div class='col-2'>";
                displayTextbox("txtOT2", "OT2 Day: ", $txtOT2, $prints, 12, "", "");
                print "</div>";
                print "<div class='col-2'>";
                displayEmployeeStatus($conn, "lstEmployeeStatus", $lstEmployeeStatus, "", "");
                print "</div>";
                print "<div class='col-2'>";
                print "<label class='form-label'>Missing Data:</label><select name='lstMissingData' class='form-select select2 shadow-none'> <option selected value='" . $lstMissingData . "'>" . $lstMissingData . "</option> <option value='Missing Name'>Missing Name</option> <option value='Missing Dept'>Missing Dept</option> <option value='Missing Div/Desg'>Missing Div/Desg</option> <option value='Missing " . $_SESSION[$session_variable . "IDColumnName"] . "'>Missing " . $_SESSION[$session_variable . "IDColumnName"] . "</option> <option value='Missing Rmk'>Missing Rmk</option> <option value=''>---</option></select>";
                print "</div>";
                print "</div>";
                print "<div class='row'>";
                print "<div class='col-2'>";
                displayTextbox("txtStartDateFrom", "Start Date From <font size='1'><font size='1'>(DD/MM/YYYY)</font></font>: ", $txtStartDateFrom, $prints, 12, "", "");
                print "</div>";
                print "<div class='col-2'>";
                displayTextbox("txtEndDateFrom", "End Date From <font size='1'><font size='1'>(DD/MM/YYYY)</font></font>: ", $txtEndDateFrom, $prints, 12, "", "");
                print "</div>";
                print "<div class='col-2'>";
                displayTextbox("txtStartDateTo", "Start Date To <font size='1'><font size='1'>(DD/MM/YYYY)</font></font>: ", $txtStartDateTo, $prints, 12, "", "");
                print "</div>";
                print "<div class='col-2'>";
                displayTextbox("txtEndDateTo", "End Date To <font size='1'><font size='1'>(DD/MM/YYYY)</font></font>: ", $txtEndDateTo, $prints, 12, "", "");
                print "</div>";
                print "<div class='col-2'>";
                $array = array(array("tuser.id", "Employee ID"), array("tuser.name, tuser.id", "Employee Name - ID"), array("tuser.PassiveType, tuser.id", "Employee Status - ID"), array("tuser.dept, tuser.id", "Dept - ID"), array("tuser.company, tuser.dept, tuser.id", "Div/Desg - Dept - ID"), array("tuser.company, tuser.dept, tuser.group_id, tuser.id", "Div - Dept - Current Shift - ID"));
                displaySort($array, $lstSort, 6);
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
$query = "";
if ($act == "searchRecord") {
    if ($txtMACAddress == "2C-44-FD-84-1C-A8") {
        $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tuser.idno, tuser.remark, tuser.phone, tuser.OT1, tuser.OT2, '', tuser.datelimit, '', '', '', '', '', '', '', '', '', '', '', '' FROM tuser, tgroup WHERE tuser.group_id = tgroup.id ";
    } else {
        $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tuser.idno, tuser.remark, tuser.phone, tuser.OT1, tuser.OT2, tuser.OldID1, tuser.datelimit, tuser.PassiveType, tuser.flagdatelimit, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, tgroup WHERE tuser.group_id = tgroup.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
    }
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
    if ($txtMACAddress != "2C-44-FD-84-1C-A8") {
        if ($txtOldID != "") {
            $query = $query . " AND tuser.OldID1 LIKE '" . $txtOldID . "%'";
        }
        $query = $query . employeeStatusQuery($lstEmployeeStatus);
    }
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
    $query = $query . " ORDER BY " . $lstSort;
    print '<div class="row"><div class="col-md-12"><div class="card"><div class="card-body table-responsive">';
    if ($prints != "yes") {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800' class='table table-striped table-bordered dataTable' id='zero_config'>";
    } else {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%' class='table table-striped table-bordered dataTable'>";
    }
    print "<thead><tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Current Shift</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "PhoneColumnName"] . "</font></td> <td><font face='Verdana' size='2'>OT1</font></td> <td><font face='Verdana' size='2'>OT2</font></td> <td><font face='Verdana' size='2'>Old ID</font></td> <td><font face='Verdana' size='2'>Start Date</font></td> <td><font face='Verdana' size='2'>End Date</font></td> <td><font face='Verdana' size='2'>Service Period</font></td> <td><font face='Verdana' size='2'>Status</font></td></tr></thead>";
    $result = mysqli_query($conn, $query);
    $count = 0;
    $startdate = "";
    $enddate = "";
    for ($pos = ""; $cur = mysqli_fetch_row($result); $count++) {
        if ($cur[3] == "") {
            $cur[3] = "&nbsp;";
        }
        if ($cur[5] == "") {
            $cur[5] = "&nbsp;";
        }
        if ($cur[6] == "") {
            $cur[6] = "&nbsp;";
        }
        addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
        print "<tr><td><a title='ID' href='EmployeeDetail.php?id=".$cur[0]."' target='_blank'><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'><font face='Verdana' color='#000000' size='1'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $cur[1] . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $cur[5] . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $cur[2] . "</font></a></td> <td><a title='Div/Desg'><font face='Verdana' size='1'>" . $cur[3] . "</font></a></td> <td><a title='Rmk'><font face='Verdana' size='1'>" . $cur[6] . "</font> <td><a title='Current Shift'><font face='Verdana' size='1'>" . $cur[4] . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "PhoneColumnName"] . "'><font face='Verdana' size='1'>" . $cur[7] . "</font></td> <td><a title='OT1'><font face='Verdana' size='1'>" . $cur[8] . "</font></a></td> <td><a title='OT2'><font face='Verdana' size='1'>" . $cur[9] . "</font></a></td> <td><a title='Old ID'><font face='Verdana' size='1'>" . $cur[10] . "</font></a></td> <td><a title='Start Date'><font face='Verdana' size='1'>";
        if (substr($cur[11], 1, 8) == "19770430") {
            $startdate = displayDate(substr($cur[13], 1, 8));
        } else {
            $startdate = displayDate(substr($cur[11], 1, 8));
        }
        print $startdate . "</font></a></td> <td><a title='End Date'><font face='Verdana' size='1'>";
        if (substr($cur[11], 9, 8) == "19770430") {
            $enddate = displayDate(substr($cur[13], 9, 8));
        } else {
            $enddate = displayDate(substr($cur[11], 9, 8));
        }
        print $enddate . "</font></a></td> <td><a title='Service Period'><font face='Verdana' size='1'>";
        if (insertDate($startdate) == insertDate($enddate) || insertToday() < insertDate($enddate)) {
            $enddate = displayToday();
        }
        $pos = getTotalDays($startdate, $enddate);
        print $pos . " D</font></a></td> <td><a title='Status'><font face='Verdana' size='1'>" . $cur[12] . "</font></a></td> </tr>";
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
print "</div></div></div></div>";
include 'footer.php';
echo "\r\n<script>\r\nfunction openWindow(a){\r\n\twindow.open(\"ReportEmployee.php?act=viewRecord&txtID=\"+a, \"\",\"height=400;width=400\");\r\n}\r\n\r\nfunction checkPrint(a){\r\n\tvar x = document.frm1;\r\n\tif (a == 0){\r\n\t\tif (confirm('Go Green - Think Twice before you Print this Document \\nAre you sure want to Print?')){\r\n\t\t\tx.action = 'ReportEmployee.php?prints=yes';\t\t\t\r\n\t\t}else{\r\n\t\t\treturn;\r\n\t\t}\r\n\t}else{\r\n\t\tx.action = 'ReportEmployee.php?prints=yes&excel=yes';\t\t\t\r\n\t}\r\n\tx.target = '_blank';\r\n\tx.submit();\r\n}\r\n\r\nfunction checkSearch(){\r\n\tvar x = document.frm1;\r\n\tx.action = 'ReportEmployee.php?prints=no';\r\n\tx.target = '_self';\r\n\tx.btSearch.disabled = true;\r\n\tx.submit();\r\n}\r\n</script>\r\n</center></body></html>";

?>
<script>
/*function pushData() {
   const form = document.createElement("form");
   form.method = "post";
   form.action = "reportemployee.php";

   const input = document.createElement("input");
   input.type = "hidden";
   input.name = "push";
   input.value = "1";
   form.appendChild(input);

   document.body.appendChild(form);
   form.submit();
}*/
</script>