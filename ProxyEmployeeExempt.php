<?php


ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "28";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!(checkSession($userlevel, $current_module) && $_SESSION[$session_variable . "VirdiLevel"] == "Classic")) {
    header("Location: " . $config["REDIRECT"] . "?url=ProxyEmployeeExempt.php&message=Session Expired or Security Policy Violated");
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
    $message = "OT Dates Proxy Exempted Employees <br>Exempted Employee(s) [Proxy will NOT be marked if Employees are ABSENT on OT Dates] <br>Un-Exempted Employee(s) [Proxy will be marked if Employees are ABSENT on OT Dates]";
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
$txtPhone = $_POST["txtPhone"];
$txtSNo = $_POST["txtSNo"];
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
$lstExempted = $_POST["lstExempted"];
if ($act == "exempt" || $act == "unExempt") {
    if ($act == "exempt") {
        if ($_POST["chkApplyAllDept"] != "") {
            $query = "INSERT INTO GroupExempt (Module, Grp, Val) VALUES ('PE', 'Dept', '" . $lstDepartment . "')";
            updateIData($iconn, $query, true);
            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Exempted Department " . $lstDepartment . " from being marked Proxy on OT DATES')";
            updateIData($iconn, $query, true);
        }
        if ($_POST["chkApplyAllDiv"] != "") {
            $query = "INSERT INTO GroupExempt (Module, Grp, Val) VALUES ('PE', 'Div', '" . $lstDivision . "')";
            updateIData($iconn, $query, true);
            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Exempted Div/Desg " . $lstDivision . " from being marked Proxy on OT DATES')";
            updateIData($iconn, $query, true);
        }
        if ($_POST["chkApplyAllRemark"] != "") {
            $query = "INSERT INTO GroupExempt (Module, Grp, Val) VALUES ('PE', 'Remark', '" . $txtRemark . "')";
            updateIData($iconn, $query, true);
            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Exempted ALL Employees with Remark " . $txtRemark . " from being marked Proxy on OT DATES')";
            updateIData($iconn, $query, true);
        }
        if ($_POST["chkApplyAllSNo"] != "") {
            $query = "INSERT INTO GroupExempt (Module, Grp, Val) VALUES ('PE', 'SNo', '" . $txtSNo . "')";
            updateIData($iconn, $query, true);
            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Exempted ALL Employees with " . $_SESSION[$session_variable . "IDColumnName"] . " " . $txtSNo . " from being marked Proxy on OT DATES')";
            updateIData($iconn, $query, true);
        }
        if ($_POST["chkApplyAllPhone"] != "") {
            $query = "INSERT INTO GroupExempt (Module, Grp, Val) VALUES ('PE', 'Phone', '" . $txtPhone . "')";
            updateIData($iconn, $query, true);
            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Exempted ALL Employees with " . $_SESSION[$session_variable . "PhoneColumnName"] . " " . $txtPhone . " from being marked Proxy on OT DATES')";
            updateIData($iconn, $query, true);
        }
    } else {
        if ($_POST["chkApplyAllDept"] != "") {
            $query = "DELETE FROM GroupExempt WHERE Module = 'PE' AND Grp = 'Dept' AND Val = '" . $lstDepartment . "'";
            updateIData($iconn, $query, true);
            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Un Exempted Department " . $lstDepartment . " from being marked Proxy on OT DATES')";
            updateIData($iconn, $query, true);
        }
        if ($_POST["chkApplyAllDiv"] != "") {
            $query = "DELETE FROM GroupExempt WHERE Module = 'PE' AND Grp = 'Div' AND Val = '" . $lstDivision . "'";
            updateIData($iconn, $query, true);
            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Un Exempted Div/Desg " . $lstDivision . " from being marked Proxy on OT DATES')";
            updateIData($iconn, $query, true);
        }
        if ($_POST["chkApplyAllRemark"] != "") {
            $query = "DELETE FROM GroupExempt WHERE Module = 'PE' AND Grp = 'Remark' AND Val = '" . $txtRemark . "'";
            updateIData($iconn, $query, true);
            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Un Exempted ALL Employees with Remark " . $txtRemark . " from being marked Proxy on OT DATES')";
            updateIData($iconn, $query, true);
        }
        if ($_POST["chkApplyAllSNo"] != "") {
            $query = "DELETE FROM GroupExempt WHERE Module = 'PE' AND Grp = 'SNo' AND Val = '" . $txtSNo . "'";
            updateIData($iconn, $query, true);
            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', ' Un Exempted ALL Employees with " . $_SESSION[$session_variable . "IDColumnName"] . " " . $txtSNo . " from being marked Proxy on OT DATES')";
            updateIData($iconn, $query, true);
        }
        if ($_POST["chkApplyAllPhone"] != "") {
            $query = "DELETE FROM GroupExempt WHERE Module = 'PE' AND Grp = 'Phone' AND Val = '" . $txtPhone . "'";
            updateIData($iconn, $query, true);
            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Un Exempted ALL Employees with " . $_SESSION[$session_variable . "PhoneColumnName"] . " " . $txtPhone . " from being marked Proxy on OT DATES')";
            updateIData($iconn, $query, true);
        }
    }
    for ($i = 0; $i < $_POST["txtCount"]; $i++) {
        if ($_POST["chk" . $i] != "") {
            if ($act == "exempt") {
                $query = "INSERT INTO ProxyEmployeeExempt (EmployeeID) VALUES ('" . $_POST["txhID" . $i] . "')";
                updateIData($iconn, $query, true);
                $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Exempted Employee ID: " . $_POST["txhID" . $i] . " from being marked Proxy on OT DATES')";
                updateIData($iconn, $query, true);
            } else {
                if ($act == "unExempt") {
                    $query = "DELETE FROM ProxyEmployeeExempt WHERE EmployeeID = '" . $_POST["txhID" . $i] . "'";
                    updateIData($iconn, $query, true);
                    $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Un Exempted Employee ID: " . $_POST["txhID" . $i] . " from being marked Proxy on OT DATES')";
                    updateIData($iconn, $query, true);
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
                            <h4 class="page-title">Employees Exempted from Proxy on OT Dates</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Employees Exempted from Proxy on OT Dates
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

<?php
}
print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='ProxyEmployeeExempt.php'><input type='hidden' name='act' value='searchRecord'>";
//print "<html><title>Employees Exempted from Proxy on OT Dates</title>";
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
        header("Content-Disposition: attachment; filename=ProxyEmployeeExempt.xls");
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
                displayEmployeeStatus($conn, "lstEmployeeStatus", $lstEmployeeStatus, "", "");
                print "</div>";
                print "<div class='col-2'>";
                print "<label class='form-label'>Proxy Exempted (Proxy NOT to be Marked):</label><select name='lstExempted' class='form-select select2 shadow-none'> <option selected value='" . $lstExempted . "'>" . $lstExempted . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> <option value=''>---</option></select>";
                print "</div>";
                print "<div class='col-2'>";
                $array = array(array("tuser.id", "Employee Code"), array("tuser.name, tuser.id", "Employee Name - Code"), array("tuser.dept, tuser.id", "Dept - Code"), array("tuser.company, tuser.dept, tuser.id", "Div/Desg - Dept - Code"), array("tuser.company, tuser.dept, tuser.group_id, tuser.id", "Div - Dept - Current Shift - Code"));
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
    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tuser.idno, tuser.remark FROM tuser, tgroup WHERE tuser.group_id = tgroup.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
    if ($lstShift != "") {
        $query = $query . " AND tgroup.id = " . $lstShift;
    }
    $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
    $query = $query . employeeStatusQuery($lstEmployeeStatus);
    if ($lstExempted == "Yes") {
        $query = $query . " AND tuser.id IN (SELECT EmployeeID FROM ProxyEmployeeExempt) ";
    } else {
        if ($lstExempted == "No") {
            $query = $query . " AND tuser.id NOT IN (SELECT EmployeeID FROM ProxyEmployeeExempt) ";
        }
    }
    $query = $query . " ORDER BY " . $lstSort;
    print '<div class="row"><div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12"><div class="card"><div class="card-body table-responsive">';
    if ($prints != "yes") {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800' class='table table-striped table-bordered dataTable'> <thead><tr><td><font face='Verdana' size='2'><input type='checkbox' name='chkAll' onClick='javascript:checkAll()'></font></td>";
    } else {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%' class='table table-striped table-bordered dataTable'><tr>";
    }
    print "<td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Current Shift</font></td></tr></thead>";
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
            addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
            print "<td><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'><a title='ID'><font face='Verdana' color='#000000' size='1'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td>";
        } else {
            addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
            print "<td><a title='ID'><font face='Verdana' color='#000000' size='1'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td>";
        }
        print "<td><a title='Name'><font face='Verdana' size='1'>" . $cur[1] . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $cur[5] . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $cur[2] . "</font></a></td> <td><a title='Div/Desg'><font face='Verdana' size='1'>" . $cur[3] . "</font></a></td> <td><a title='Rmk'><font face='Verdana' size='1'>" . $cur[6] . "</font> <td><a title='Current Shift'><font face='Verdana' size='1'>" . $cur[4] . "</font> </tr>";
    }
    print "<input type='hidden' name='txtCount' value='" . $count . "'></table>";
    if ($excel != "yes") {
        print "<p align='center'><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $count . "</b></font><br>";
    }
    if ($prints != "yes" && 0 < $count) {
        if (strpos($userlevel, $current_module . "E") !== false) {
            print "<table>";
            if ($lstDepartment != "") {
                print "<tr><td><input type='checkbox' name='chkApplyAllDept'></td><td><font face='Verdana' size='1'>Apply Changes to all Employees in the Selected Department in Future (For New Employees)</font></td></tr>";
            }
            if ($lstDivision != "") {
                print "<tr><td><input type='checkbox' name='chkApplyAllDiv'></td><td><font face='Verdana' size='1'>Apply Changes to all Employees in the Selected Div/Desg in Future (For New Employees)</font></td></tr>";
            }
            if ($txtRemark != "") {
                print "<tr><td><input type='checkbox' name='chkApplyAllRemark'></td><td><font face='Verdana' size='1'>Apply Changes to all Employees with the Selected Remark in Future (For New Employees)</font></td></tr>";
            }
            if ($txtSNo != "") {
                print "<tr><td><input type='checkbox' name='chkApplyAllSNo'></td><td><font face='Verdana' size='1'>Apply Changes to all Employees with the Selected " . $_SESSION[$session_variable . "IDColumnName"] . " in Future (For New Employees)</font></td></tr>";
            }
            if ($txtPhone != "") {
                print "<tr><td><input type='checkbox' name='chkApplyAllPhone'></td><td><font face='Verdana' size='1'>Apply Changes to all Employees with the Selected " . $_SESSION[$session_variable . "PhoneColumnName"] . " in Future (For New Employees)</font></td></tr>";
            }
            print "<p align='center'><input name='btExempt' type='button' class='btn btn-primary' value='Exempt Proxy on OT Dates' onClick='javascript:exempt(0)'>&nbsp;&nbsp;&nbsp;";
            print "<input name='btUnExempt' type='button' class='btn btn-primary' value='Un-Exempt Proxy on OT Dates' onClick='javascript:unExempt()'></p>";
            print "</table>";
        }
        print "<p align='center'><br><br><input type='button' value='Print Report' class='btn btn-primary' onClick='checkPrint(0)'>&nbsp;&nbsp;<input type='button' class='btn btn-primary' value='Excel' onClick='checkPrint(1)'></p>";
    }
    print "</p>";
}
print "</form>";
echo "\r\n<script>\r\nfunction saveChanges(){\t\r\n\tx = document.frm1;\t\t\r\n\tif (x.lstSelectedDepartment.value == '' && x.lstSelectedDivision.value == '' && x.txtSelectedSNo.value == '' && x.txtSelectedRemark.value == ''){\r\n\t\talert('Please select/ enter at least one Data to update the Employees');\r\n\t}else{\r\n\t\tif (confirm('Save Changes')){\r\n\t\t\tx.act.value = 'saveChanges';\r\n\t\t\tx.submit();\r\n\t\t}\r\n\t}\t\r\n}\r\n\r\nfunction exempt(a){\t\r\n\tx = document.frm1;\t\r\n\tif (confirm('Exempt Selected Employee(s) from Proxy on OT Dates [Selected Employees will NOT be marked PROXY on OT DATES]')){\r\n\t\tx.act.value = 'exempt';\r\n\t\tx.btExempt.disabled = true;\r\n\t\tx.submit();\r\n\t}\r\n}\r\n\r\nfunction unExempt(){\t\r\n\tx = document.frm1;\t\r\n\tif (confirm('Un-Exempt Selected Employee(s) from Proxy on OT Dates [Selected Employees will be marked PROXY on OT DATES]')){\r\n\t\tx.act.value = 'unExempt';\r\n\t\tx.btUnExempt.disabled = true;\r\n\t\tx.submit();\r\n\t}\r\n}\r\n\r\nfunction checkAll(){\t\r\n\tx = document.frm1;\r\n\ty = x.chkAll;\r\n\tz = x.txtCount.value;\t\r\n\tfor (i=0;i<z;i++){\t\t\r\n\t\tif (y.checked == true){\t\t\t\r\n\t\t\tdocument.getElementById(\"chk\"+i).checked = true;\t\t\t\r\n\t\t}else{\r\n\t\t\tdocument.getElementById(\"chk\"+i).checked = false;\r\n\t\t}\r\n\t}\r\n}\r\n\r\nfunction openWindow(a){\r\n\twindow.open(\"EmployeeChild.php?act=viewRecord&txtID=\"+a, \"\",\"height=400;width=400\");\r\n}\r\n</script>\r\n</center>";
print "</div></div></div></div></div>";
include 'footer.php';

?>