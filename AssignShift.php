<?php
ob_start("ob_gzhandler");
set_time_limit(0);
error_reporting(E_ALL);
include "Functions.php";
$current_module = "14";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=AssignShift.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$iconn = openIConnection();
$jconn = openIConnection();
//$uconn = mysqli_connect("127.0.0.1", "unisuser", "unisamho", "UNIS");

$act = isset($_POST['act']) ? $_POST['act'] : $_GET['act'];
$prints = $_GET["prints"];
$excel = $_GET["excel"];
$message = $_GET["message"];
if ($message == "") {
    $message = "Assign Shift";
}
$lstShift = $_POST["lstShift"];
$lstAssignShift = $_POST["lstAssignShift"];
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
$txtPhone = $_POST["txtPhone"];
if ($act == "editRecord") {
    $uconn = mysqli_connect("127.0.0.1", "root", "namaste", "UNIS");
    $txtCount = $_POST["txtCount"];
    $shift_name = "";
    if (0 < $txtCount && $lstAssignShift != "") {
        $query = "SELECT name from tgroup WHERE id = '" . $lstAssignShift . "'";
        $result = selectData($conn, $query);
        $shift_name = $result[0];
    }
    $payroll_query = "";
    $oconn = "";
    $query = "SELECT TableName, EID, Shift, Overwrite FROM PayrollMap";
    $main_result = selectData($conn, $query);
    if ($main_result[3] != "No Synchronization") {
        $query = "SELECT LockDate, MACAddress, DBType, DBIP, DBName, DBUser, DBPass FROM OtherSettingMaster";
        $super_main_result = selectData($jconn, $query);
        $txtLockDate = $super_main_result[0];
        $lstDBType = $super_main_result[2];
        $txtDBIP = $super_main_result[3];
        $txtDBName = $super_main_result[4];
        $txtDBUser = $super_main_result[5];
        $txtDBPass = $super_main_result[6];
    }
    for ($i = 0; $i < $txtCount; $i++) {
        if ($_POST["chk" . $i] != "") {
            $query = "UPDATE tuser SET group_id = " . $lstAssignShift . " WHERE id = " . $_POST["chk" . $i];
            if (updateIData($iconn, $query, true)) {
                $query = "UPDATE temploye SET C_Work = " . $lstAssignShift . " WHERE L_UID = " . $_POST["chk" . $i];
                updateIData($uconn, $query, true);
                $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Assigned Shift for Employee ID: " . $_POST["chk" . $i] . " - Shift ID = " . $shift_name . "')";
                updateIData($jconn, $query, true);
            }
        }
    }
    $message = "Records Updated";
    $act = "searchRecord";
}
if ($prints != "yes") {
    include 'header.php';
?>
<div class="page-breadcrumb">
                    <div class="row">
                        <div class="col-12 d-flex no-block align-items-center">
                            <h4 class="page-title">Assign Shift</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Assign Shift
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

<?php
}
print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='AssignShift.php'><input type='hidden' name='act' value='searchRecord'>";
//print "<html><title>Assign Shift</title>";
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
        <!--</form>-->
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
    $query = $query . " ORDER BY " . $lstSort;
    print '<div class="row"><div class="col-md-12"><div class="card"><div class="card-body table-responsive">';
    if ($prints != "yes") {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%' class='table table-striped table-bordered dataTable'>";
    } else {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%' class='table table-striped table-bordered dataTable'>";
    }
    print "<thead><tr><td><font face='Verdana'>";
    if ($prints != "yes") {
        print "<input type='checkbox' onclick='checkAll(this)' name='chkAll' id='chkAll'>";
    } else {
        print "&nbsp;";
    }
    print "</font></td> <td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Current Shift</font></td></tr></thead>";
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
        print "<tr><td><font face='Verdana' size='1'>";
        if ($prints != "yes") {
            print "<input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'><input type='checkbox' name='chk" . $count . "' value='" . $cur[0] . "'>";
        } else {
            print "&nbsp;";
        }
        addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
        print "</font></td> <td><a title='ID'><font face='Verdana' color='#000000' size='1'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $cur[1] . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $cur[5] . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $cur[2] . "</font></a></td> <td><a title='Div/Desg'><font face='Verdana' size='1'>" . $cur[3] . "</font></a></td> <td><a title='Rmk'><font face='Verdana' size='1'>" . $cur[6] . "</font> <td><a title='Current Shift'><font face='Verdana' size='1'>" . $cur[4] . "</font> </tr>";
    }
    print "</table>";
    if ($prints != "yes") {
        print "<div class='row'>";
        print "<div class='col-5'></div>";
        print "<div class='col-2'>";
        $query = "SELECT id, name FROM tgroup ORDER BY name";
        displayList("lstAssignShift", "Assign selected Employees to:", $lstAssignShift, $prints, $conn, $query, "", "40%", "30%");
        print"</div>";
        print"</div>";
        print "<div class='row'>";
        print "<div class='col-12'>";
        if (stripos($userlevel, $current_module . "E") !== false) {
            print "<center><br><input name='btSubmit' class='btn btn-primary' type='button' value='Save Changes' onClick='javascript:checkSubmit()'></center>";
        }
        print "</div>";
        print "</div>";
    }
    if ($excel != "yes") {
        print "<br><p align='center'><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $count . "</b></font> <input type='hidden' name='txtCount' value='" . $count . "'>";
    }
    if ($prints != "yes") {
        print "<br><input type='button' class='btn btn-primary' value='Print Report' onClick='checkPrint(0)'>&nbsp;&nbsp;<input type='button' class='btn btn-primary' value='Excel' onClick='checkPrint(1)'>";
    }
    print "</p>";
}
print "</form>";
print "</div></div></div></div>";

print "<script>";
print "function checkAll(){" . "x = document.frm1;" . "y = document.frm1.chkAll;" . "var count = 0;" . "if (y.checked){";
for ($i = 0; $i < $count; $i++) {
    print "x.chk" . $i . ".checked = true;";
}
print "}else{";
for ($i = 0; $i < $count; $i++) {
    print "x.chk" . $i . ".checked = false;";
}
print "}" . "}";
print "</script>";
echo "\r\n<script>\r\nfunction openWindow(a){\r\n\twindow.open(\"AssignShift.php?act=viewRecord&txtID=\"+a, \"\",\"height=400;width=400\");\r\n}\r\n\r\nfunction checkSubmit(){\r\n\tvar x = document.frm1;\t\r\n\ta = x.lstAssignShift.value;\r\n\t\r\n\tif (a == ''){\r\n\t\talert(\"Please select a Shift to be Assigned\");\r\n\t\tx.lstAssignShift.focus();\r\n\t}else{\r\n\t\tx.act.value = 'editRecord';\r\n\t\tx.btSubmit.disabled = true;\r\n\t\tx.submit();\r\n\t}\r\n}\r\n</script>\r\n</center>";
include 'footer.php';

?>
<script>
//    function checkAll(chkAll) {
//            var x = document.frm1; // form reference
//            var y = x.chkAll; // "Check All" checkbox reference
//            var z = x.txtCount.value; // Total count of checkboxes
//
//            // Loop through the checkboxes based on the count
//            for (var i = 0; i < z; i++) {
//                var checkbox = document.getElementById("chk" + i); // Get each checkbox
//
//                // Check if checkbox exists before accessing 'checked' property
//                if (checkbox) {
//                    checkbox.checked = y.checked; // Set its checked state
//                }
//            }
//        }
//        $(document).ready(function () {
////            var table = $('#zero_config').DataTable();
//
//            // Use event delegation for the "Select All" checkbox
//            $('#chkAll').on('click', function () {
//                var checked = this.checked;
//                // Select all checkboxes in the table (visible and non-visible rows)
//                $('input[type="checkbox"]', table.rows().nodes()).prop('checked', checked);
//            });
//        });
</script>