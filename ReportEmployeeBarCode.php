<?php


ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "18";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
$VirdiLevel = $_SESSION[$session_variable . "VirdiLevel"];
$txtMACAddress = $_SESSION[$session_variable . "MACAddress"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=ReportEmployeeBarCode.php&message=Session Expired or Security Policy Violated");
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
    $message = "Employee Record from Bar Code Search";
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
$txtOT1 = $_POST["txtOT1"];
$txtOT2 = $_POST["txtOT2"];
$txtOldID1 = $_POST["txtOldID1"];
$lstEmployeeStatus = "";
if (isset($_POST["lstEmployeeStatus"])) {
    $lstEmployeeStatus = $_POST["lstEmployeeStatus"];
} else {
    $lstEmployeeStatus = "ACT";
}
$lstFingerRegistered = $_POST["lstFingerRegistered"];
$lstCardRegistered = $_POST["lstCardRegistered"];
$lstMissingData = $_POST["lstMissingData"];
$txtStartDateFrom = $_POST["txtStartDateFrom"];
$txtStartDateTo = $_POST["txtStartDateTo"];
$txtEndDateFrom = $_POST["txtEndDateFrom"];
$txtEndDateTo = $_POST["txtEndDateTo"];
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
if ($act == "viewRecord") {
    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tuser.group_id FROM tuser WHERE tuser.id = " . $_GET["txtID"] . " " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
    $result = selectData($conn, $query);
    print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='ReportEmployeeBarCode.php?act=editRecord'>";
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
                            <h4 class="page-title">Employee Records from Bar Code Search</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Employee Records from Bar Code Search
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

<?php
}
//print "<html><title>Employee Records from Bar Code Search</title>";
if ($prints != "yes") {
    print "<body onLoad='javascript:document.frm1.txtEmployeeCode.focus()'>";
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
        header("Content-Disposition: attachment; filename=ReportEmployeeBarCode.xls");
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
        }
        print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='ReportEmployeeBarCode.php'><input type='hidden' name='act' value='searchRecord'><tr>";
        ?>
        <div class="row">
            <div class="col-5"></div>
            <div class="col-2">
                <?php
                displayTextbox("txtEmployeeCode", "Employee ID: ", "", $prints, 25, "", "");
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <?php 
                print "<center><br><input name='btSearch' type='submit' class='btn btn-primary' value='Search Record'></center>";
                ?>
            </div>
        </form>
        
    </div>
</div>
<?php
 } 
print "</div></div></div></div>";

$query = "";
if ($act == "searchRecord") {
    
    if (!empty($_SESSION[$session_variable . "DeptAccessQuery"])) {  
        $query .= " " . $_SESSION[$session_variable . "DeptAccessQuery"];  
    }  

    if (!empty($_SESSION[$session_variable . "DivAccessQuery"])) {  
        $query .= " " . $_SESSION[$session_variable . "DivAccessQuery"];  
    }
    
    if ($txtMACAddress == "2C-44-FD-84-1C-A8") {
        $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tuser.idno, tuser.remark, tuser.phone, tuser.OT1, tuser.OT2, '', tuser.datelimit, '', '', '', '', '', '', '', '', '', '', '', '', tuser.group_id FROM tuser, tgroup WHERE tuser.group_id = tgroup.id";
    } else {
//        echo $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tuser.idno, tuser.remark, tuser.phone, tuser.OT1, tuser.OT2, tuser.OldID1, tuser.datelimit, tuser.PassiveType, tuser.flagdatelimit, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10, tuser.group_id FROM tuser, tgroup WHERE tuser.group_id = tgroup.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " AND tuser.id = " . $txtEmployeeCode;
        $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tuser.idno, tuser.remark, tuser.phone, tuser.OT1, tuser.OT2, tuser.OldID1, tuser.datelimit, tuser.PassiveType, tuser.flagdatelimit, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10, tuser.group_id FROM tuser, tgroup WHERE tuser.group_id = tgroup.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"];
    }
    
    // Sanitize $txtEmployeeCode to prevent SQL injection  
    if (isset($txtEmployeeCode)) {   
        $query .= " AND tuser.id = '" . $txtEmployeeCode . "'";  
    } 
    $counter = 0;
    for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $counter++) {
        if ($cur[3] == "") {
            $cur[3] = "&nbsp;";
        }
        if ($cur[5] == "") {
            $cur[5] = "&nbsp;";
        }
        if ($cur[6] == "") {
            $cur[6] = "&nbsp;";
        }
        print '<div class="row"><div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12"><div class="card"><div class="card-body table-responsive">';
        if ($prints != "yes") {
            print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800' class='table table-striped table-bordered dataTable'>";
        } else {
            print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%' class='table table-striped table-bordered dataTable'>";
        }
        addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
        print "<tr><td><img src='img/usr/" . $cur[0] . ".jpg'></td><td>&nbsp;</td><td><font face='Verdana' size='2'>ID: <b>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</b>" . "<br><br>Name: <b>" . $cur[1] . "</b>" . "<br><br>" . $_SESSION[$session_variable . "IDColumnName"] . ": <b>" . $cur[5] . "</b>" . "<br><br>Dept: <b>" . $cur[2] . "</b>" . "<br><br>Div/Desg: <b>" . $cur[3] . "</b>" . "<br><br>Rmk: <b>" . $cur[6] . "</b>" . "<br><br>Current Shift: <b>" . $cur[4] . "</b>" . "<br><br>" . $_SESSION[$session_variable . "PhoneColumnName"] . ": <b>" . $cur[7] . "</b>";
        if (substr($cur[11], 1, 8) == "19770430") {
            $startdate = displayDate(substr($cur[13], 1, 8));
        } else {
            $startdate = displayDate(substr($cur[11], 1, 8));
        }
        print "<br><br>Start Date: <b>" . $startdate . "</b>";
        if (substr($cur[11], 9, 8) == "19770430") {
            $enddate = displayDate(substr($cur[13], 9, 8));
        } else {
            $enddate = displayDate(substr($cur[11], 9, 8));
        }
        print "<br><br>End Date: <b>" . $enddate . "</b>" . "<br><br>Status: <b>" . $cur[12] . "</b>" . "</font></td></tr></table>";
        $query = "INSERT INTO tenter (e_date, e_time, g_id, e_id, e_group, e_mode, p_flag) VALUES ('" . insertToday() . "', '" . getNow() . "00', '1', '" . $cur[0] . "', '" . $cur[24] . "', '1', 0)";
        updateIData($iconn, $query, true);
    }
    if ($counter == 0) {
        print "<center><p><font face='Verdana' size='15' color='#000000'><b>EMPLOYEE <font color='#FF0000'>" . $txtEmployeeCode . "</font> NOT FOUND</b></font></p></center>";
    }
}
print "</form>";
echo "\r\n<script>\r\nfunction openWindow(a){\r\n\twindow.open(\"ReportEmployeeBarCode.php?act=viewRecord&txtID=\"+a, \"\",\"height=400;width=400\");\r\n}\r\n\r\nfunction checkPrint(a){\r\n\tvar x = document.frm1;\r\n\tif (a == 0){\r\n\t\tif (confirm('Go Green - Think Twice before you Print this Document \\nAre you sure want to Print?')){\r\n\t\t\tx.action = 'ReportEmployeeBarCode.php?prints=yes';\t\t\t\r\n\t\t}else{\r\n\t\t\treturn;\r\n\t\t}\r\n\t}else{\r\n\t\tx.action = 'ReportEmployeeBarCode.php?prints=yes&excel=yes';\t\t\t\r\n\t}\r\n\tx.target = '_blank';\r\n\tx.submit();\r\n}\r\n\r\nfunction checkSearch(){\r\n\tvar x = document.frm1;\r\n\tx.action = 'ReportEmployeeBarCode.php?prints=no';\r\n\tx.target = '_self';\r\n\tx.btSearch.disabled = true;\r\n\tx.submit();\r\n}\r\n</script>";
print "</div></div></div></div></div>";
include 'footer.php';
?>