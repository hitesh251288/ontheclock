<?php
ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "17";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=ReportAlterTime.php&message=Session Expired or Security Policy Violated");
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
    $message = "Time Alteration Report";
}
$lstShift = $_POST["lstShift"];
$lstDepartment = $_POST["lstDepartment"];
$lstDivision = $_POST["lstDivision"];
$lstEmployeeIDFrom = $_POST["lstEmployeeIDFrom"];
$lstEmployeeIDTo = $_POST["lstEmployeeIDTo"];
$txtEmployeeCode = $_POST["txtEmployeeCode"];
$txtEmployee = $_POST["txtEmployee"];
$lstUsername = $_POST["lstUsername"];
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
if ($prints != "yes") {
    include 'header.php';
?>
<div class="page-breadcrumb">
                    <div class="row">
                        <div class="col-12 d-flex no-block align-items-center">
                            <h4 class="page-title">Time Alteration Report</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Time Alteration Report
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

<?php
}
print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='ReportAlterTime.php'><input type='hidden' name='act' value='searchRecord'>";
//print "<html><title>Time Alteration Report</title>";
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
        header("Content-Disposition: attachment; filename=ReportAlterTime.xls");
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
                if ($username == "virdi") {
                    $query = "SELECT Username, Username FROM UserMaster ORDER BY Username";
                } else {
                    $query = "SELECT Username, Username FROM UserMaster WHERE Username NOT LIKE 'virdi' ORDER BY Username";
                }
                displayList("lstUsername", "Done by: ", $lstUsername, $prints, $conn, $query, "", "", "");
                ?>
            </div>
            <div class="col-2">
                <?php 
                displayTextbox("txtFrom", "<font size='1'>Transaction Date From <font size='1'>(DD/MM/YYYY)</font>:</font> ", $txtFrom, $prints, 12, "", "");
                ?>
            </div>
            <div class="col-2">
                <?php 
                displayTextbox("txtTo", "<font size='1'>Transaction Date To <font size='1'>(DD/MM/YYYY)</font>:</font> ", $txtTo, $prints, 12, "", "");
                ?>
            </div>
        <?php 
        if ($prints != "yes") {
            print "<div class='col-2'>";
            displayEmployeeStatus($conn, "lstEmployeeStatus", $lstEmployeeStatus, "", "");
            print "</div>";
            print "<div class='col-2'>";
            $array = array(array("tuser.id, AlterLog.TransactDate, AlterLog.LogID", "Employee Code"), array("tuser.name, tuser.id, AlterLog.TransactDate, AlterLog.LogID", "Employee Name - Code"), array("tuser.dept, tuser.id, AlterLog.TransactDate, AlterLog.LogID", "Dept - Code"), array("tuser.company, tuser.dept, tuser.id, AlterLog.TransactDate, AlterLog.LogID", "Div/Desg - Dept - Code"), array("tuser.company, tuser.dept, tuser.group_id, tuser.id, AlterLog.TransactDate, AlterLog.LogID", "Div - Dept - Current Shift - Code"), array("AlterLog.Username, AlterLog.TransactDate, AlterLog.LogID", "System User"));
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
    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tuser.idno, tuser.remark, AlterLog.LogID, AlterLog.Username, AlterLog.ed, AlterLog.DateFrom, AlterLog.DateTo, AlterLog.TimeFrom, AlterLog.TimeTo, AlterLog.GateFrom, AlterLog.GateTo, AlterLog.ShiftFrom, AlterLog.ShiftTo, AlterLog.TransactDate, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, tgroup, AlterLog, tenter WHERE tuser.group_id = tgroup.id AND tenter.e_id = tuser.id AND tenter.ed = AlterLog.ed " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
    if ($lstShift != "") {
        $query = $query . " AND tgroup.id = " . $lstShift;
    }
    $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
    if ($lstUsername != "") {
        $query = $query . " AND AlterLog.Username = '" . $lstUsername . "'";
    } else {
        if ($username != "virdi") {
            $query = $query . " AND AlterLog.Username NOT LIKE 'virdi'";
        }
    }
    if ($txtFrom != "") {
        $query = $query . " AND AlterLog.TransactDate >= " . insertDate($txtFrom);
    }
    if ($txtTo != "") {
        $query = $query . " AND AlterLog.TransactDate <= " . insertDate($txtTo);
    }
    $query = $query . employeeStatusQuery($lstEmployeeStatus);
    $query = $query . " ORDER BY " . $lstSort;
    print '<div class="row"><div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12"><div class="card"><div class="card-body table-responsive">';
    if ($prints != "yes") {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800' class='table table-striped table-bordered dataTable' id='zero_config'>";
    } else {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%' class='table table-striped table-bordered dataTable'>";
    }
    print "<thead><tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Department</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Current Shift</font></td> <td><font face='Verdana' size='2'>Done by</font></td> <td><font face='Verdana' size='2'>Done on</font></td> <td bgcolor='#F0F0F0'><font face='Verdana' size='2'>Date From</font></td> <td bgcolor='#F0F0F0'><font face='Verdana' size='2'>Date To</font></td> <td><font face='Verdana' size='2'>Time From</font></td> <td><font face='Verdana' size='2'>Time To</font></td> <td bgcolor='#F0F0F0'><font face='Verdana' size='2'>Terminal From</font></td> <td bgcolor='#F0F0F0'><font face='Verdana' size='2'>Terminal To</font></td> <td><font face='Verdana' size='2'>Shift From</font></td> <td><font face='Verdana' size='2'>Shift To</font></td></tr></thead>";
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
        if ($cur[14] != "") {
            $query = "SELECT name from tgate where id = " . $cur[14];
            $result1 = selectData($conn, $query);
            $cur[14] = $result1[0];
        } else {
            $cur[14] = "&nbsp;";
        }
        if ($cur[15] != "") {
            $query = "SELECT name from tgate where id = " . $cur[15];
            $result1 = selectData($conn, $query);
            $cur[15] = $result1[0];
        } else {
            $cur[15] = "&nbsp;";
        }
        if ($cur[16] != "") {
            $query = "SELECT name from tgroup where id = " . $cur[16];
            $result1 = selectData($conn, $query);
            $cur[16] = $result1[0];
        } else {
            $cur[16] = "&nbsp;";
        }
        if ($cur[17] != "") {
            $query = "SELECT name from tgroup where id = " . $cur[17];
            $result1 = selectData($conn, $query);
            $cur[17] = $result1[0];
        } else {
            $cur[17] = "&nbsp;";
        }
        addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
        displayDate($cur[18]);
        print "<tr><td><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'><font face='Verdana' size='1'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></td> <td><font face='Verdana' size='1'>" . $cur[1] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[5] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[2] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[3] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[6] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[4] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[8] . "</font></td> <td><font face='Verdana' size='1'>" . displayDate($cur[18]) . "</font></td> <td bgcolor='#F0F0F0'><font face='Verdana' size='1'>";
        if (5 < strlen($cur[10])) {
            displayDate($cur[10]);
            print displayDate($cur[10]);
        } else {
            print "&nbsp;";
        }
        print "</font></td> <td bgcolor='#F0F0F0'><font face='Verdana' size='1'>";
        if (5 < strlen($cur[11])) {
            displayDate($cur[11]);
            print displayDate($cur[11]);
        } else {
            print "&nbsp;";
        }
        print "</font></td> <td><font face='Verdana' size='1'>";
        if (2 < strlen($cur[12])) {
            displayVirdiTime($cur[12]);
            print displayVirdiTime($cur[12]);
        } else {
            print "&nbsp;";
        }
        print "</font></td> <td><font face='Verdana' size='1'>";
        if (2 < strlen($cur[13])) {
            displayVirdiTime($cur[13]);
            print displayVirdiTime($cur[13]);
        } else {
            print "&nbsp;";
        }
        print "</font></td> <td bgcolor='#F0F0F0'><font face='Verdana' size='1'>";
        if ($cur[14] != "No Terminal") {
            print $cur[14];
        } else {
            print "&nbsp;";
        }
        print "</font></td> <td bgcolor='#F0F0F0'><font face='Verdana' size='1'>";
        if ($cur[15] != "No Terminal") {
            print $cur[15];
        } else {
            print "&nbsp;";
        }
        print "</font></td> <td><font face='Verdana' size='1'>";
        if ($cur[16] != "Not Assigned" && $cur[16] != "Assign All") {
            print $cur[16];
        } else {
            print "&nbsp;";
        }
        print "</font></td> <td><font face='Verdana' size='1'>";
        if ($cur[17] != "Not Assigned" && $cur[17] != "Assign All") {
            print $cur[17];
        } else {
            print "&nbsp;";
        }
        print "</font></td> </tr>";
    }
    print "</table>";
    if ($excel != "yes") {
        print "<br><p align='center'><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $count . "</b></font>";
    }
    if ($prints != "yes") {
        print "<br><input type='button' class='btn btn-primary' value='Print Report' onClick='checkPrint(0)'>&nbsp;&nbsp;<input type='button' class='btn btn-primary' value='Excel' onClick='checkPrint(1)'>";
    }
    print "</p>";
}
print "</form>";
echo "</center>";
print "</div></div></div></div></div>";
include 'footer.php';

?>