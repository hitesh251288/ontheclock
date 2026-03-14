<?php
ob_start("ob_gzhandler");
error_reporting(E_ERROR);
set_time_limit(0);
include "Functions.php";
$current_module = "26";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=PreApproveOvertime.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$iconn = openIConnection();
$jconn = openIConnection();
$act = $_GET["act"];
if ($act == "") {
    $act = $_POST["act"];
}
$prints = $_GET["prints"];
$excel = $_GET["excel"];
$message = $_GET["message"];
if ($message == "") {
    $message = "Pre-Approve Overtime (Applicable ONLY for Shifts with Routine Type = Daily) <br>EDIT Rights on this Module grants Permission for AP1 <br>DELETE Rights on this Module grants Permission for AP2";
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
if ($txtFrom == "") {
    $txtFrom = displayDate(getNextDay(insertToday(), 1));
}
$txtRemark = $_POST["txtRemark"];
$txtPhone = $_POST["txtPhone"];
$txtSNo = $_POST["txtSNo"];
$txhCount = $_POST["txhCount"];
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
$lstType = $_POST["lstType"];
if ($act == "saveChanges") {
    for ($i = 0; $i < $txhCount; $i++) {
        $text = "Updated Pre Approve OT for ID: " . $_POST["txh" . $i];
        $query = "";
        if ($_POST["txtOT" . $i] != "") {
            if ($_POST["txhExist" . $i] == "") {
                $query = "INSERT INTO PreApproveOT (OTDate, e_id, OT, A1, A2, A3, Remark) VALUES (" . insertDate($_POST["txhFrom"]) . ", " . $_POST["txh" . $i] . ", " . $_POST["txtOT" . $i] . ", ";
                if ($_POST["txtOT" . $i] != "" && 0 < $_POST["txtOT" . $i]) {
                    $query = $query . "1, ";
                    $text = $text . " - Set Hours to: " . $_POST["txtOT" . $i];
                } else {
                    $query = $query . "0, ";
                    $text = $text . " - Set Hours to: 0";
                }
                if ($_POST["chkA2" . $i] != "") {
                    $query = $query . "1, ";
                } else {
                    $query = $query . "0, ";
                }
                if ($_POST["chkA3" . $i] != "") {
                    $query = $query . "1, ";
                } else {
                    $query = $query . "0, ";
                }
                $query = $query . " '" . $_POST["txtRemark" . $i] . "')";
            } else {
                $query = "UPDATE PreApproveOT SET OT = " . $_POST["txtOT" . $i] . ", Remark = '" . $_POST["txtRemark" . $i] . "', ";
                $text = $text . " - Set Hours to: " . $_POST["txtOT" . $i];
                if ($_POST["chkA2" . $i] != "") {
                    $query = $query . " A2 = 1, ";
                    $text = $text . " - Approved";
                } else {
                    $query = $query . " A2 = 0, ";
                    $text = $text . " - UnApproved";
                }
                if ($_POST["chkA3" . $i] != "") {
                    $query = $query . " A3 = 1 ";
                    $text = $text . " - Authorized";
                } else {
                    $query = $query . " A3 = 0 ";
                    $text = $text . " - UnAuthorized";
                }
                $query = $query . " WHERE PreApproveOTID = " . $_POST["txhExist" . $i];
            }
            if (updateIData($iconn, $query, true)) {
                $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                updateIData($jconn, $query, true);
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
                <h4 class="page-title">Pre-Approve Overtime</h4>
                <div class="ms-auto text-end">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Pre-Approve Overtime
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <?php
}
//print "<html><title>Pre-Approve Overtime</title>";
print "<form name='frm1' id='frm1' method='post' onSubmit='return checkPreApproveOTSearch()' action='PreApproveOvertime.php'><input type='hidden' name='act' value='searchRecord'> <input type='hidden' name='txtTo' value='31/12/2020'>";
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
        header("Content-Disposition: attachment; filename=PreApproveOvertime.xls");
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
    } else {
        print "<center><font face='Verdana' size='1'><b>Selected Options</b></font></center>";
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
                <div class="col-2">
    <?php
    displayTextbox("txtFrom", "Date <font size='1'>(DD/MM/YYYY)</font>: ", $txtFrom, $prints, 12, "", "");
    ?>
                </div>
                    <?php
                    if ($prints != "yes") {
                        print "<div class='col-2'>";
                        print "<font face='Verdana' size='2'>Record Type:</font><select name='lstType' class='form-control'> <option selected value='" . $lstType . "'>" . $lstType . "</option> <option value='OT > 0'>OT > 0</option> <option value='AP1 = Yes'>AP1 = Yes</option> <option value='AP2 = Yes'>AP2 = Yes</option> <option value='---'>---</option> </select>";
                        print "</div>";
                        print "<div class='col-2'>";
                        displayEmployeeStatus($conn, "lstEmployeeStatus", $lstEmployeeStatus, "", "");
                        print "</div>";
                        print "<div class='col-2'>";
                        $array = array(array("tuser.id", "Employee Code"), array("tuser.name, tuser.id", "Employee Name - Code"), array("tuser.dept, tuser.id", "Dept - Code"), array("tuser.company, tuser.dept, tuser.id", "Div/Desg - Dept - Code"), array("tuser.company, tuser.dept, tuser.group_id, tuser.id", "Div - Dept - Current Shift - Code"));
                        displaySort($array, $lstSort, 5);
                        print "</div>";
                        print "</div>";
                        print "<div class='row'>";
                        print "<div class='col-12'>";
                        print "<center><a name='1'><input name='btSearch' type='submit' value='Search Record' class='btn btn-primary'></a></center>";
                        print "</div>";
                        print "</div>";
                    }
                    ?>
        </div>
    </div>
<?php
} 
print "</div></div></div>";
?>
<?php
if ($act == "searchRecord") {
    if ($lstType == "OT > 0") {
        $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tuser.idno, tuser.remark FROM tuser, tgroup, PreApproveOT WHERE tuser.group_id = tgroup.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " AND tuser.id = PreApproveOT.e_id AND PreApproveOT.OT > 0 AND PreApproveOT.OTDate = " . insertDate($txtFrom) . " ";
    } else {
        if ($lstType == "AP1 = Yes") {
            $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tuser.idno, tuser.remark FROM tuser, tgroup, PreApproveOT WHERE tuser.group_id = tgroup.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " AND tuser.id = PreApproveOT.e_id AND PreApproveOT.A2 > 0 AND PreApproveOT.OTDate = " . insertDate($txtFrom) . " ";
        } else {
            if ($lstType == "AP2 = Yes") {
                $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tuser.idno, tuser.remark FROM tuser, tgroup, PreApproveOT WHERE tuser.group_id = tgroup.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " AND tuser.id = PreApproveOT.e_id AND PreApproveOT.A3 > 0 AND PreApproveOT.OTDate = " . insertDate($txtFrom) . " ";
            } else {
                $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tuser.idno, tuser.remark FROM tuser, tgroup WHERE tuser.group_id = tgroup.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
            }
        }
    }
    if ($lstShift != "") {
        $query = $query . " AND tgroup.id = " . $lstShift;
    }
    $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
    $query = $query . employeeStatusQuery($lstEmployeeStatus);
    $query = $query . " ORDER BY " . $lstSort;
    print '<div class="row"><div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12"><div class="card"><div class="card-body table-responsive">';
    if ($prints != "yes") {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800' class='table table-striped table-bordered dataTable'>";
    } else {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%' class='table table-striped table-bordered dataTable'>";
    }
    print "<input type='hidden' name='txhFrom' value='" . $txtFrom . "'>"; //Grant EDIT Permission for this Module to the User to Activate this Column  //Grant DELETE Permission for this Module to the User to Activate this Column//Grant ADD Permission for this Module to the User to Activate this Column
    print "<thead><tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Current Shift</font></td> <td><font face='Verdana' size='2'>Date</font></td> <td><a title='Overtime Hours'><font face='Verdana' size='2'>OT (Hrs)</font></a></td> <td><a title='Grant ADD Permission for this Module to the User to Activate this Column'><font face='Verdana' size='2'>Notes</font></a></td> <td><a title='Approve 1'><font face='Verdana' size='2'>AP1</font></a></td> <td><a title='Approve 2'><font face='Verdana' size='2'>AP2</font></a></td> </tr>";
    if ($prints != "yes") {
        print "<tr><td colspan='8' align='right'><font face='Verdana' size='1'><b>Enter the OT Hours and Notes in the respective Boxes in this Row and Click on the Blank OT Text Boxes in below Rows to Copy the value of the Boxes in this Row<br><br><a href='#1' onClick='javascript:insertAllOT()'>Click Here to Copy the Values in the Text Boxes in this Row to all the below Blank Textboxes</a></font></td> <td bgcolor='#F0F0F0'><input size='2' name='txtOTAll' value='' onBlur='javascript:checkAssignTextbox(document.frm1.txtOTAll)' class='form-control'></td> <td bgcolor='#F0F0F0'><input name='txtRemarkAll' value='' class='form-control'></td> <td bgcolor='#F0F0F0'><input type='checkbox' name='chkA2All' onClick='javascript:checkA2All()'></td> <td bgcolor='#F0F0F0'><input type='checkbox' name='chkA3All' onClick='javascript:checkA3All()'></td> </tr>";
    }
    print "</thead>";
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
        addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
        print "<tr><td><a title='ID'><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'> <font face='Verdana' color='#000000' size='1'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $cur[1] . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $cur[5] . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $cur[2] . "</font></a></td> <td><a title='Div/Desg'><font face='Verdana' size='1'>" . $cur[3] . "</font></a></td> <td><a title='Rmk'><font face='Verdana' size='1'>" . $cur[6] . "</font></a></td> <td><a title='Current Shift'><font face='Verdana' size='1'>" . $cur[4] . "</font></a></td> <td><a title='Date'><font face='Verdana' size='1'>" . $txtFrom . "</font></a></td>";
        $query = "SELECT OT, A1, A2, A3, Remark, PreApproveOTID FROM PreApproveOT WHERE OTDate = " . insertDate($txtFrom) . " AND e_id = " . $cur[0];
        $result1 = selectData($conn, $query);
        if ($prints != "yes" && (strpos($userlevel, $current_module . "A") !== false && $result1[2] == 0 || strpos($userlevel, $current_module . "E") !== false && $result1[3] == 0 || strpos($userlevel, $current_module . "D") !== false)) {
            print "<td><font face='Verdana' size='1'><input size='2' id='txtOT" . $count . "' name='txtOT" . $count . "' value='" . $result1[0] . "' onFocus='javascript:insertOTAll(document.frm1.txtOT" . $count . ", document.frm1.txtRemark" . $count . ")' onBlur='javascript:checkAssignTextbox(document.frm1.txtOT" . $count . ")' class='form-control'></td>";
            print "<td><font face='Verdana' size='1'><input id='txtRemark" . $count . "' name='txtRemark" . $count . "' value='" . $result1[4] . "' class='form-control'></td>";
        } else {
            if ($result1[0] == "") {
                $result1[0] = "&nbsp;";
            }
            if ($result1[4] == "") {
                $result1[4] = "&nbsp;";
            }
            print "<td><font face='Verdana' size='1'>" . $result1[0] . "</font><input type='hidden' id='txtOT" . $count . "' name='txtOT" . $count . "' value='" . $result1[0] . "'></td>";
            print "<td><font face='Verdana' size='1'>" . $result1[4] . "</font><input type='hidden' name='txtRemark" . $count . "' value='" . $result1[4] . "'></td>";
        }
        print "<input type='hidden' name='txhExist" . $count . "' value='" . $result1[5] . "'>";
        if ($prints != "yes" && (strpos($userlevel, $current_module . "E") !== false && $result1[3] == 0 || strpos($userlevel, $current_module . "D") !== false)) {
            if ($result1[2] == 1) {
                print "<td><input type='checkbox' id='chkA2" . $count . "' name='chkA2" . $count . "' checked></td>";
            } else {
                print "<td><input type='checkbox' id='chkA2" . $count . "' name='chkA2" . $count . "'></td>";
            }
        } else {
            if ($result1[2] == 1) {
                print "<td><input type='hidden' id='chkA2" . $count . "' name='chkA2" . $count . "' value='1'><font face='Verdana' size='1'>Yes</font></td>";
            } else {
                print "<td><input type='hidden' id='chkA2" . $count . "' name='chkA2" . $count . "' value=''><font face='Verdana' size='1'>No</font></td>";
            }
        }
        if ($prints != "yes" && strpos($userlevel, $current_module . "D") !== false) {
            if ($result1[3] == 1) {
                print "<td><input type='checkbox' id='chkA3" . $count . "' name='chkA3" . $count . "' checked></td>";
            } else {
                print "<td><input type='checkbox' id='chkA3" . $count . "' name='chkA3" . $count . "'></td>";
            }
        } else {
            if ($result1[3] == 1) {
                print "<td><input type='hidden' id='chkA3" . $count . "' name='chkA3" . $count . "' value='1'><font face='Verdana' size='1'>Yes</font></td>";
            } else {
                print "<td><input type='hidden' id='chkA3" . $count . "' name='chkA3" . $count . "' value=''><font face='Verdana' size='1'>No</font></td>";
            }
        }
        print "</tr>";
    }
    print "</table><input type='hidden' name='txhCount' value='" . $count . "'>";
    
    if ($excel != "yes") {
        print "<br><p align='center'><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $count . "</b></font>";
    }
    if ($prints != "yes") {
        if ((strpos($userlevel, $current_module . "A") !== false || strpos($userlevel, $current_module . "E") !== false || strpos($userlevel, $current_module . "D") !== false) && $_SESSION[$session_variable . "LockDate"] < insertDate($txtFrom)) {
            print "<br><br><input name='btSubmit' type='button' value='Save Changes' onClick='saveChanges()' class='btn btn-primary'>&nbsp;&nbsp;";
        } else {
            print "<br>";
        }
        print "<input type='button' value='Print Report' onClick='checkPreApproveOTPrint(0)' class='btn btn-primary'>&nbsp;&nbsp;<input type='button' value='Excel' onClick='checkPreApproveOTPrint(1)' class='btn btn-primary'>";
    }
    print "</p>";
    
    print "</div></div></div></div>";
}
print "</form>";
include 'footer.php';
//echo "\r\n<script>\r\nfunction openWindow(a){\r\n\twindow.open(\"PreApproveOvertime.php?act=viewRecord&txtID=\"+a, \"\",\"height=400;width=400\");\r\n}\r\n\r\nfunction checkPreApproveOTPrint(a){\r\n\tvar x = document.frm1;\r\n\tif (check_valid_date(x.txtFrom.value) == false){\r\n\t\talert('Invalid From Date. Date Format should be DD/MM/YYYY');\r\n\t\tx.txtFrom.focus();\r\n\t\treturn false;\r\n\t}else{\r\n\t\tif (a == 0){\r\n\t\t\tif (confirm('Go Green - Think Twice before you Print this Document \\nAre you sure want to Print?')){\r\n\t\t\t\tx.action = 'PreApproveOvertime.php?prints=yes';\r\n\t\t\t\tx.target = '_blank';\r\n\t\t\t\tx.submit();\r\n\t\t\t\treturn true;\r\n\t\t\t}else{\r\n\t\t\t\treturn false;\r\n\t\t\t}\r\n\t\t}else{\r\n\t\t\tx.action = 'PreApproveOvertime.php?prints=yes&excel=yes';\t\r\n\t\t\tx.target = '_blank';\r\n\t\t\tx.submit();\r\n\t\t\treturn true;\r\n\t\t}\t\t\r\n\t}\r\n}\r\n\r\nfunction checkPreApproveOTSearch(){\r\n\tvar x = document.frm1;\r\n\tif (check_valid_date(x.txtFrom.value) == false){\r\n\t\talert('Invalid From Date. Date Format should be DD/MM/YYYY');\r\n\t\tx.txtFrom.focus();\r\n\t\treturn false;\r\n\t}else{\r\n\t\tx.action = 'PreApproveOvertime.php?prints=no';\r\n\t\tx.target = '_self';\r\n\t\tx.btSearch.disabled = true;\r\n\t\treturn true;\r\n\t}\r\n}\r\n\r\nfunction checkAssignTextbox(x){\r\n\tif (x.value*1 != x.value/1){\r\n\t\talert(\"ONLY Numeric Value ALLOWED as OT\");\r\n\t\tx.focus();\r\n\t}\r\n}\r\n\r\nfunction insertOTAll(x, w){\r\n\ty = document.frm1.txtOTAll.value;\t\r\n\tz = document.frm1.txtRemarkAll.value;\t\r\n\tif (y != \"\" && y*1 == y/1){\r\n\t\tif (x.value == \"\" || x.value == 0){\r\n\t\t\tx.value = y;\r\n\t\t\tw.value = z;\r\n\t\t}\r\n\t}\r\n}\r\n\r\nfunction insertAllOT(){\r\n\tx = document.frm1;\r\n\tif (x.txtOTAll.value != \"\" && x.txtOTAll.value != 0){\r\n\t\tif (confirm(\"Enter OT = \"+x.txtOTAll.value+\" in all the Below Blank OT Records?\")){\t\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\r\n\t\t\t\tif (document.getElementById(\"txtOT\"+i).value == \"\" || document.getElementById(\"txtOT\"+i).value == 0){\r\n\t\t\t\t\tdocument.getElementById(\"txtOT\"+i).value = x.txtOTAll.value;\r\n\t\t\t\t\tdocument.getElementById(\"txtRemark\"+i).value = x.txtRemarkAll.value;\r\n\t\t\t\t}\r\n\t\t\t}\r\n\t\t}\r\n\t}else{\r\n\t\talert(\"Please enter the OT value to be assigned to all Records\");\r\n\t\tx.txtOTAll.focus();\r\n\t}\r\n}\r\n\r\nfunction checkA2All(){\r\n\tx = document.frm1;\r\n\tif (x.chkA2All.checked == true){\r\n\t\tif (confirm(\"Approve All OT\")){\t\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\r\n\t\t\t\tif (document.getElementById(\"txtOT\"+i)){\r\n\t\t\t\t\tif (document.getElementById(\"txtOT\"+i).value != \"\" && document.getElementById(\"txtOT\"+i).value != 0){\r\n\t\t\t\t\t\tdocument.getElementById(\"chkA2\"+i).checked = true;\t\r\n\t\t\t\t\t}\r\n\t\t\t\t}\r\n\t\t\t}\r\n\t\t}else{\r\n\t\t\tx.chkA2All.checked = false;\r\n\t\t}\r\n\t}else{\r\n\t\tif (confirm(\"De-Approve All OT\")){\t\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\r\n\t\t\t\tdocument.getElementById(\"chkA2\"+i).checked = false;\t\t\t\t\t\r\n\t\t\t}\r\n\t\t}else{\r\n\t\t\tx.chkA2All.checked = true;\r\n\t\t}\r\n\t}\r\n}\r\n\r\nfunction checkA3All(){\r\n\tx = document.frm1;\r\n\tif (x.chkA3All.checked == true){\r\n\t\tif (confirm(\"Authorize All OT\")){\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\r\n\t\t\t\tif (document.getElementById(\"txtOT\"+i).value != \"\" && document.getElementById(\"txtOT\"+i).value != 0 && (document.getElementById(\"chkA2\"+i).checked == true || document.getElementById(\"chkA2\"+i).value == 1)){\r\n\t\t\t\t\tdocument.getElementById(\"chkA3\"+i).checked = true;\t\t\t\t\t\t\r\n\t\t\t\t}\r\n\t\t\t}\r\n\t\t}else{\r\n\t\t\tx.chkA3All.checked = false;\r\n\t\t}\r\n\t}else{\r\n\t\tif (confirm(\"De-Authorize All OT\")){\t\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\r\n\t\t\t\tdocument.getElementById(\"chkA3\"+i).checked = false;\t\t\t\t\t\r\n\t\t\t}\r\n\t\t}else{\r\n\t\t\tx.chkA3All.checked = true;\r\n\t\t}\r\n\t}\r\n}\r\n\r\nfunction saveChanges(){\r\n\tx = document.frm1;\r\n\tif (confirm(\"Save Changes?\")){\r\n\t\tx.act.value = \"saveChanges\";\r\n\t\tx.btSubmit.disabled = true;\r\n\t\tx.submit();\r\n\t}\r\n}\r\n</script>\r\n</center></body></html>";
?>
<script>
function openWindow(a){
	window.open("PreApproveOvertime.php?act=viewRecord&txtID="+a, "","height=400;width=400");
}

function checkPreApproveOTPrint(a){
	var x = document.frm1;
	if (check_valid_date(x.txtFrom.value) == false){
		alert('Invalid From Date. Date Format should be DD/MM/YYYY');
		x.txtFrom.focus();
		return false;
	}else{
		if (a == 0){
			if (confirm('Go Green - Think Twice before you Print this Document \nAre you sure want to Print?')){
				x.action = 'PreApproveOvertime.php?prints=yes';
				x.target = '_blank';
				x.submit();
				return true;
			}else{
				return false;
			}
		}else{
			x.action = 'PreApproveOvertime.php?prints=yes&excel=yes';	
			x.target = '_blank';
			x.submit();
			return true;
		}		
	}
}

function checkPreApproveOTSearch(){
	var x = document.frm1;
	if (check_valid_date(x.txtFrom.value) == false){
		alert('Invalid From Date. Date Format should be DD/MM/YYYY');
		x.txtFrom.focus();
		return false;
	}else{
		x.action = 'PreApproveOvertime.php?prints=no';
		x.target = '_self';
		x.btSearch.disabled = true;
		return true;
	}
}

function checkAssignTextbox(x){
	if (x.value*1 != x.value/1){
		alert("ONLY Numeric Value ALLOWED as OT");
		x.focus();
	}
}

function insertOTAll(x, w){
	y = document.frm1.txtOTAll.value;	
	z = document.frm1.txtRemarkAll.value;	
	if (y != "" && y*1 == y/1){
		if (x.value == "" || x.value == 0){
			x.value = y;
			w.value = z;
		}
	}
}

//function insertAllOT(){
//	x = document.frm1;
//	if (x.txtOTAll.value != "" && x.txtOTAll.value != 0){
//		if (confirm("Enter OT = "+x.txtOTAll.value+" in all the Below Blank OT Records?")){	
//			for (i=0;i<x.txhCount.value;i++){
//				if (document.getElementById("txtOT"+i).value == "" || document.getElementById("txtOT"+i).value == 0){
//					document.getElementById("txtOT"+i).value = x.txtOTAll.value;
//					document.getElementById("txtRemark"+i).value = x.txtRemarkAll.value;
//				}
//			}
//		}
//	}else{
//		alert("Please enter the OT value to be assigned to all Records");
//		x.txtOTAll.focus();
//	}
//}

function insertAllOT() {
    let form = document.forms['frm1'];
    
    // Check if the main "All" fields are set and have valid values
    let txtOTAll = form.txtOTAll;
    let txtRemarkAll = form.txtRemarkAll;
    let txhCount = form.txhCount ? form.txhCount.value : 0;
    
    if (!txtOTAll || txtOTAll.value === "" || txtOTAll.value === "0") {
        alert("Please enter the OT value to be assigned to all Records");
        txtOTAll.focus();
        return;
    }

    // Confirm before filling all blanks
    if (confirm("Enter OT = " + txtOTAll.value + " in all the Below Blank OT Records?")) {
        for (let i = 0; i < txhCount; i++) {
            let otField = document.getElementById("txtOT" + i);
            let remarkField = document.getElementById("txtRemark" + i);

            // Only update fields if they exist and are blank/zero
            if (otField && (otField.value === "" || otField.value === "0")) {
                otField.value = txtOTAll.value;
            }
            if (remarkField && (remarkField.value === "")) {
                remarkField.value = txtRemarkAll.value;
            }
        }
    }
}

function checkA2All(){
	x = document.frm1;
	if (x.chkA2All.checked == true){
		if (confirm("Approve All OT")){	
			for (i=0;i<x.txhCount.value;i++){
				if (document.getElementById("txtOT"+i)){
					if (document.getElementById("txtOT"+i).value != "" && document.getElementById("txtOT"+i).value != 0){
						document.getElementById("chkA2"+i).checked = true;	
					}
				}
			}
		}else{
			x.chkA2All.checked = false;
		}
	}else{
		if (confirm("De-Approve All OT")){	
			for (i=0;i<x.txhCount.value;i++){
				document.getElementById("chkA2"+i).checked = false;					
			}
		}else{
			x.chkA2All.checked = true;
		}
	}
}

function checkA3All(){
	x = document.frm1;
	if (x.chkA3All.checked == true){
		if (confirm("Authorize All OT")){
			for (i=0;i<x.txhCount.value;i++){
				if (document.getElementById("txtOT"+i).value != "" && document.getElementById("txtOT"+i).value != 0 && (document.getElementById("chkA2"+i).checked == true || document.getElementById("chkA2"+i).value == 1)){
					document.getElementById("chkA3"+i).checked = true;						
				}
			}
		}else{
			x.chkA3All.checked = false;
		}
	}else{
		if (confirm("De-Authorize All OT")){	
			for (i=0;i<x.txhCount.value;i++){
				document.getElementById("chkA3"+i).checked = false;					
			}
		}else{
			x.chkA3All.checked = true;
		}
	}
}

function saveChanges(){
	x = document.frm1;
	if (confirm("Save Changes?")){
		x.act.value = "saveChanges";
		x.btSubmit.disabled = true;
		x.submit();
	}
}

//function saveChanges(){ 
//    const form = document.forms['frm1']; // Reference the form directly by name
//
//    // Check if form and required elements are present
//    if (form && form.act && form.btSubmit) {
//        if (confirm("Save Changes?")) {
//            // Set the action value
//            form.act.value = "saveChanges";
//
//            // Disable the submit button for this function's scope only
//            form.btSubmit.disabled = true;
//
//            // Submit the form directly
//            form.submit();
//            
//            // Re-enable submit button in case DataTables redraw needs it
//            form.btSubmit.disabled = false;
//        }
//    } else {
//        console.error("Form or form elements not found.");
//    }
//}

</script>