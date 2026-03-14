<?php
ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "30";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
$flagLimitType = $_SESSION[$session_variable . "FlagLimitType"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=EmployeeFlagLimit.php&message=Session Expired or Security Policy Violated");
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
    $message = "Employee Annual Flag Limits";
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
    if ($flagLimitType == "Jan 01") {
        $v = 365;
        $i = 365;
        $b = 365;
        $g = 365;
        $y = 365;
        $o = 365;
        $r = 365;
        $gr = 365;
        $br = 365;
        $pr = 365;
        $mg = 365;
        $tl = 365;
        $aq = 365;
        $sf = 365;
        $am = 365;
        $gl = 365;
        $vm = 365;
        $sl = 365;
        $mr = 365;
        $pk = 365;
        if (is_numeric($_POST["txtVAll"])) {
            $v = $_POST["txtVAll"];
        }
        if (is_numeric($_POST["txtIAll"])) {
            $i = $_POST["txtIAll"];
        }
        if (is_numeric($_POST["txtBAll"])) {
            $b = $_POST["txtBAll"];
        }
        if (is_numeric($_POST["txtGAll"])) {
            $g = $_POST["txtGAll"];
        }
        if (is_numeric($_POST["txtYAll"])) {
            $y = $_POST["txtYAll"];
        }
        if (is_numeric($_POST["txtOAll"])) {
            $o = $_POST["txtOAll"];
        }
        if (is_numeric($_POST["txtRAll"])) {
            $r = $_POST["txtRAll"];
        }
        if (is_numeric($_POST["txtGRAll"])) {
            $gr = $_POST["txtGRAll"];
        }
        if (is_numeric($_POST["txtBRAll"])) {
            $br = $_POST["txtBRAll"];
        }
        if (is_numeric($_POST["txtPRAll"])) {
            $pr = $_POST["txtPRAll"];
        }
        if (is_numeric($_POST["txtMGAll"])) {
            $mg = $_POST["txtMGAll"];
        }
        if (is_numeric($_POST["txtTLAll"])) {
            $tl = $_POST["txtTLAll"];
        }
        if (is_numeric($_POST["txtAQAll"])) {
            $aq = $_POST["txtAQAll"];
        }
        if (is_numeric($_POST["txtSFAll"])) {
            $sf = $_POST["txtSFAll"];
        }
        if (is_numeric($_POST["txtAMAll"])) {
            $am = $_POST["txtAMAll"];
        }
        if (is_numeric($_POST["txtGLAll"])) {
            $gl = $_POST["txtGLAll"];
        }
        if (is_numeric($_POST["txtVMAll"])) {
            $vm = $_POST["txtVMAll"];
        }
        if (is_numeric($_POST["txtSLAll"])) {
            $sl = $_POST["txtSLAll"];
        }
        if (is_numeric($_POST["txtMRAll"])) {
            $mr = $_POST["txtMRAll"];
        }
        if (is_numeric($_POST["txtPKAll"])) {
            $pk = $_POST["txtPKAll"];
        }
        if ($_POST["chkApplyAllDept"] != "") {
            $query = "DELETE FROM GroupFlagLimit WHERE Grp = 'Dept' AND Val = '" . $lstDepartment . "'";
            if (updateIData($iconn, $query, true)) {
                $query = "INSERT INTO GroupFlagLimit (Grp, Val, Violet, Indigo, Blue, Green, Yellow, Orange, Red, Gray, Brown, Purple, Magenta, Teal, Aqua, Safron, Amber, Gold, Vermilion, Silver, Maroon, Pink) VALUES ('Dept', '" . $lstDepartment . "', '" . $v . "', '" . $i . "', '" . $b . "', '" . $g . "', '" . $y . "', '" . $o . "', '" . $r . "', '" . $gr . "', '" . $br . "', '" . $pr . "', '" . $mg . "', '" . $tl . "', '" . $aq . "', '" . $sf . "', '" . $am . "', '" . $gl . "', '" . $vm . "', '" . $sl . "', '" . $mr . "', '" . $pk . "')";
                if (updateIData($iconn, $query, true)) {
                    $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Marked Flag Limits for Department " . $lstDepartment . " [V=" . $v . ", I=" . $i . ", B=" . $b . ", G=" . $g . ", Y=" . $y . ", O=" . $o . ", R=" . $r . ", GR=" . $gr . ", BR=" . $br . ", PR=" . $pr . ", MG=" . $mg . ", TL=" . $tl . ", AQ=" . $aq . ", SF=" . $sf . ", AM=" . $am . ", GL=" . $gl . ", VM=" . $vm . ", SL=" . $sl . ", MR=" . $mr . ", PK=" . $pk . "]')";
                    updateIData($iconn, $query, true);
                }
            }
        }
        if ($_POST["chkApplyAllDiv"] != "") {
            $query = "DELETE FROM GroupFlagLimit WHERE Grp = 'Div' AND Val = '" . $lstDivision . "'";
            if (updateIData($iconn, $query, true)) {
                $query = "INSERT INTO GroupFlagLimit (Grp, Val, Violet, Indigo, Blue, Green, Yellow, Orange, Red, Gray, Brown, Purple, Magenta, Teal, Aqua, Safron, Amber, Gold, Vermilion, Silver, Maroon, Pink) VALUES ('Div', '" . $lstDivision . "', '" . $v . "', '" . $i . "', '" . $b . "', '" . $g . "', '" . $y . "', '" . $o . "', '" . $r . "', '" . $gr . "', '" . $br . "', '" . $pr . "', '" . $mg . "', '" . $tl . "', '" . $aq . "', '" . $sf . "', '" . $am . "', '" . $gl . "', '" . $vm . "', '" . $sl . "', '" . $mr . "', '" . $pk . "')";
                if (updateIData($iconn, $query, true)) {
                    $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Marked Flag Limits for Division " . $lstDivision . " [V=" . $v . ", I=" . $i . ", B=" . $b . ", G=" . $g . ", Y=" . $y . ", O=" . $o . ", R=" . $r . ", GR=" . $gr . ", BR=" . $br . ", PR=" . $pr . ", MG=" . $mg . ", TL=" . $tl . ", AQ=" . $aq . ", SF=" . $sf . ", AM=" . $am . ", GL=" . $gl . ", VM=" . $vm . ", SL=" . $sl . ", MR=" . $mr . ", PK=" . $pk . "]')";
                    updateIData($iconn, $query, true);
                }
            }
        }
        if ($_POST["chkApplyAllRemark"] != "") {
            $query = "DELETE FROM GroupFlagLimit WHERE Grp = 'Remark' AND Val = '" . $txtRemark . "'";
            if (updateIData($iconn, $query, true)) {
                $query = "INSERT INTO GroupFlagLimit (Grp, Val, Violet, Indigo, Blue, Green, Yellow, Orange, Red, Gray, Brown, Purple, Magenta, Teal, Aqua, Safron, Amber, Gold, Vermilion, Silver, Maroon, Pink) VALUES ('Remark', '" . $txtRemark . "', '" . $v . "', '" . $i . "', '" . $b . "', '" . $g . "', '" . $y . "', '" . $o . "', '" . $r . "', '" . $gr . "', '" . $br . "', '" . $pr . "', '" . $mg . "', '" . $tl . "', '" . $aq . "', '" . $sf . "', '" . $am . "', '" . $gl . "', '" . $vm . "', '" . $sl . "', '" . $mr . "', '" . $pk . "')";
                if (updateIData($iconn, $query, true)) {
                    $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Marked Flag Limits for Employees with Remark " . $txtRemark . " [V=" . $v . ", I=" . $i . ", B=" . $b . ", G=" . $g . ", Y=" . $y . ", O=" . $o . ", R=" . $r . ", GR=" . $gr . ", BR=" . $br . ", PR=" . $pr . ", MG=" . $mg . ", TL=" . $tl . ", AQ=" . $aq . ", SF=" . $sf . ", AM=" . $am . ", GL=" . $gl . ", VM=" . $vm . ", SL=" . $sl . ", MR=" . $mr . ", PK=" . $pk . "]')";
                    updateIData($iconn, $query, true);
                }
            }
        }
        if ($_POST["chkApplyAllSNo"] != "") {
            $query = "DELETE FROM GroupFlagLimit WHERE Grp = 'SNo' AND Val = '" . $txtSNo . "'";
            if (updateIData($iconn, $query, true)) {
                $query = "INSERT INTO GroupFlagLimit (Grp, Val, Violet, Indigo, Blue, Green, Yellow, Orange, Red, Gray, Brown, Purple, Magenta, Teal, Aqua, Safron, Amber, Gold, Vermilion, Silver, Maroon, Pink) VALUES ('SNo', '" . $txtSNo . "', '" . $v . "', '" . $i . "', '" . $b . "', '" . $g . "', '" . $y . "', '" . $o . "', '" . $r . "', '" . $gr . "', '" . $br . "', '" . $pr . "', '" . $mg . "', '" . $tl . "', '" . $aq . "', '" . $sf . "', '" . $am . "', '" . $gl . "', '" . $vm . "', '" . $sl . "', '" . $mr . "', '" . $pk . "')";
                if (updateIData($iconn, $query, true)) {
                    $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Marked Flag Limits for Employees with " . $_SESSION[$session_variable . "IDColumnName"] . " " . $txtSNo . " [V=" . $v . ", I=" . $i . ", B=" . $b . ", G=" . $g . ", Y=" . $y . ", O=" . $o . ", R=" . $r . ", GR=" . $gr . ", BR=" . $br . ", PR=" . $pr . ", MG=" . $mg . ", TL=" . $tl . ", AQ=" . $aq . ", SF=" . $sf . ", AM=" . $am . ", GL=" . $gl . ", VM=" . $vm . ", SL=" . $sl . ", MR=" . $mr . ", PK=" . $pk . "]')";
                    updateIData($iconn, $query, true);
                }
            }
        }
        if ($_POST["chkApplyAllPhone"] != "") {
            $query = "DELETE FROM GroupFlagLimit WHERE Grp = 'Phone' AND Val = '" . $txtPhone . "'";
            if (updateIData($iconn, $query, true)) {
                $query = "INSERT INTO GroupFlagLimit (Grp, Val, Violet, Indigo, Blue, Green, Yellow, Orange, Red, Gray, Brown, Purple, Magenta, Teal, Aqua, Safron, Amber, Gold, Vermilion, Silver, Maroon, Pink) VALUES ('Phone', '" . $txtPhone . "', '" . $v . "', '" . $i . "', '" . $b . "', '" . $g . "', '" . $y . "', '" . $o . "', '" . $r . "', '" . $gr . "', '" . $br . "', '" . $pr . "', '" . $mg . "', '" . $tl . "', '" . $aq . "', '" . $sf . "', '" . $am . "', '" . $gl . "', '" . $vm . "', '" . $sl . "', '" . $mr . "', '" . $pk . "')";
                if (updateIData($iconn, $query, true)) {
                    $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Marked Flag Limits for Employees with " . $_SESSION[$session_variable . "PhoneColumnName"] . " " . $txtPhone . " [V=" . $v . ", I=" . $i . ", B=" . $b . ", G=" . $g . ", Y=" . $y . ", O=" . $o . ", R=" . $r . ", GR=" . $gr . ", BR=" . $br . ", PR=" . $pr . ", MG=" . $mg . ", TL=" . $tl . ", AQ=" . $aq . ", SF=" . $sf . ", AM=" . $am . ", GL=" . $gl . ", VM=" . $vm . ", SL=" . $sl . ", MR=" . $mr . ", PK=" . $pk . "]')";
                    updateIData($iconn, $query, true);
                }
            }
        }
        for ($i = 0; $i < $txhCount; $i++) {
            $query = "UPDATE EmployeeFlag SET Violet = '" . $_POST["txtV" . $i] . "', Indigo = '" . $_POST["txtI" . $i] . "', Blue = '" . $_POST["txtB" . $i] . "', Green = '" . $_POST["txtG" . $i] . "', Yellow = '" . $_POST["txtY" . $i] . "', Orange = '" . $_POST["txtO" . $i] . "', Red = '" . $_POST["txtR" . $i] . "', Gray = '" . $_POST["txtGR" . $i] . "', Brown = '" . $_POST["txtBR" . $i] . "', Purple = '" . $_POST["txtPR" . $i] . "', Magenta = " . $_POST["txtMG" . $i] . ", Teal = " . $_POST["txtTL" . $i] . ", Aqua = " . $_POST["txtAQ" . $i] . ", Safron = " . $_POST["txtSF" . $i] . ", Amber = " . $_POST["txtAM" . $i] . ", Gold = " . $_POST["txtGL" . $i] . ", Vermilion = " . $_POST["txtVM" . $i] . ", Silver = " . $_POST["txtSL" . $i] . ", Maroon = " . $_POST["txtMR" . $i] . ", Pink = " . $_POST["txtPK" . $i] . " WHERE EmployeeID = '" . $_POST["txh" . $i] . "'";
            if (updateIData($iconn, $query, true)) {
                $text = "SET Flag Limit for ID: " . $_POST["txh" . $i] . " - Violet = " . $_POST["txtV" . $i] . ", Indigo = " . $_POST["txtI" . $i] . ", Blue = " . $_POST["txtB" . $i] . ", Green = " . $_POST["txtG" . $i] . ", Yellow = " . $_POST["txtY" . $i] . ", Orange = " . $_POST["txtO" . $i] . ", Red = " . $_POST["txtR" . $i] . ", Gray = " . $_POST["txtGR" . $i] . ", Brown = " . $_POST["txtBR" . $i] . ", Purple = " . $_POST["txtPR" . $i] . ", Magenta = " . $_POST["txtMG" . $i] . ", Teal = " . $_POST["txtTL" . $i] . ", Aqua = " . $_POST["txtAQ" . $i] . ", Safron = " . $_POST["txtSF" . $i] . ", Amber = " . $_POST["txtAM" . $i] . ", Gold = " . $_POST["txtGL" . $i] . ", Vermilion = " . $_POST["txtVM" . $i] . ", Silver = " . $_POST["txtSL" . $i] . ", Maroon = " . $_POST["txtMR" . $i] . ", Pink = " . $_POST["txtPK" . $i];
                $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                updateIData($iconn, $query, true);
            }
        }
    } else {
        for ($i = 0; $i < $txhCount; $i++) {
            $query = "DELETE FROM GroupYearFlagLimit WHERE Grp = '" . $_POST["txhGrp" . $i] . "' AND Val = '" . $_POST["txhVal" . $i] . "' AND Years = '" . $_POST["txh" . $i] . "' ";
            if (updateIData($iconn, $query, true)) {
                $query = "INSERT INTO GroupYearFlagLimit (Grp, Val, Years, Violet, Indigo, Blue, Green, Yellow, Orange, Red, Gray, Brown, Purple, Magenta, Teal, Aqua, Safron, Amber, Gold, Vermilion, Silver, Maroon, Pink) VALUES ('" . $_POST["txhGrp" . $i] . "', '" . $_POST["txhVal" . $i] . "', '" . $_POST["txh" . $i] . "', '" . $_POST["txtV" . $i] . "', '" . $_POST["txtI" . $i] . "', '" . $_POST["txtB" . $i] . "', '" . $_POST["txtG" . $i] . "', '" . $_POST["txtY" . $i] . "', '" . $_POST["txtO" . $i] . "', '" . $_POST["txtR" . $i] . "', '" . $_POST["txtGR" . $i] . "', '" . $_POST["txtBR" . $i] . "', '" . $_POST["txtPR" . $i] . "', " . $_POST["txtMG" . $i] . ", " . $_POST["txtTL" . $i] . ", " . $_POST["txtAQ" . $i] . ", " . $_POST["txtSF" . $i] . ", " . $_POST["txtAM" . $i] . ", " . $_POST["txtGL" . $i] . ", " . $_POST["txtVM" . $i] . ", " . $_POST["txtSL" . $i] . ", " . $_POST["txtMR" . $i] . ", " . $_POST["txtPK" . $i] . ")";
                if (updateIData($iconn, $query, true)) {
                    $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Marked Flag Limits for " . $_POST["txhGrp" . $i] . " " . $_POST["txhVal" . $i] . " [Year = " . $_POST["txh" . $i] . ", V=" . $_POST["txtV" . $i] . ", I=" . $_POST["txtI" . $i] . ", B=" . $_POST["txtB" . $i] . ", G=" . $_POST["txtG" . $i] . ", Y=" . $_POST["txtY" . $i] . ", O=" . $_POST["txtO" . $i] . ", R=" . $_POST["txtR" . $i] . ", GR=" . $_POST["txtGR" . $i] . ", BR=" . $_POST["txtBR" . $i] . ", PR=" . $_POST["txtPR" . $i] . ", MG=" . $_POST["txtMG" . $i] . ", TL=" . $_POST["txtTL" . $i] . ", AQ=" . $_POST["txtAQ" . $i] . ", SF=" . $_POST["txtSF" . $i] . ", AM=" . $_POST["txtAM" . $i] . ", GL=" . $_POST["txtGL" . $i] . ", VM=" . $_POST["txtVM" . $i] . ", SL=" . $_POST["txtSL" . $i] . ", MR=" . $_POST["txtMR" . $i] . ", PK=" . $_POST["txtPK" . $i] . "]')";
                    updateIData($iconn, $query, true);
                }
            }
        }
    }
    $act = "searchRecord";
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
if ($prints != "yes") {
    include 'header.php';
?>
<style>
    .form-controls{
        width: auto !important;
        height: calc(1.5em + 0.75rem + 2px);
        border-color: #aaaaaa;
        border-radius: 4px;
        
        display: block;
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        font-weight: 400;
        line-height: 1.5;
        color: #3e5569;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #e9ecef;
        -webkit-appearance: none;
        appearance: none;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
</style>
<div class="page-breadcrumb">
                    <div class="row">
                        <div class="col-12 d-flex no-block align-items-center">
                            <h4 class="page-title">Employee Annual Flag Limits</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Employee Annual Flag Limits
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

<?php
}
print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='EmployeeFlagLimit.php'><input type='hidden' name='act' value='searchRecord'>";
//print "<html><title>Employee Annual Flag Limits</title>";
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
        header("Content-Disposition: attachment; filename=EmployeeFlagLimit.xls");
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
                $array = array(array("tuser.id", "Employee Code"), array("tuser.name, tuser.id", "Employee Name - Code"), array("tuser.dept, tuser.id", "Dept - Code"), array("tuser.company, tuser.dept, tuser.id", "Div/Desg - Dept - Code"), array("tuser.company, tuser.dept, tuser.group_id, tuser.id", "Div - Dept - Current Shift - Code"));
                displaySort($array, $lstSort, 5);
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
    if ($flagLimitType == "Jan 01") {
        $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tuser.idno, tuser.remark, EmployeeFlag.Violet, EmployeeFlag.Indigo, EmployeeFlag.Blue, EmployeeFlag.Green, EmployeeFlag.Yellow, EmployeeFlag.Orange, EmployeeFlag.Red, EmployeeFlag.Brown, EmployeeFlag.Gray, EmployeeFlag.Purple, EmployeeFlag.Magenta, EmployeeFlag.Teal, EmployeeFlag.Aqua, EmployeeFlag.Safron, EmployeeFlag.Amber, EmployeeFlag.Gold, EmployeeFlag.Vermilion, EmployeeFlag.Maroon, EmployeeFlag.Silver, EmployeeFlag.Pink FROM tuser, tgroup, EmployeeFlag WHERE EmployeeFlag.EmployeeID = tuser.id AND tuser.group_id = tgroup.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
        if ($lstShift != "") {
            $query = $query . " AND tgroup.id = " . $lstShift;
        }
        $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
        $query = $query . employeeStatusQuery($lstEmployeeStatus);
        $query = $query . " ORDER BY " . $lstSort;
    } else {
        $query = "SELECT ID, Grp, Val, Years, Violet, Indigo, Blue, Green, Yellow, Orange, Red, Gray, Brown, Purple, Magenta, Teal, Aqua, Safron, Amber, Gold, Vermilion, Silver, Maroon, Pink FROM GroupYearFlagLimit WHERE ID > 0  ";
        if ($lstDepartment != "") {
            $query .= " AND Grp = 'Dept' AND Val = '" . $lstDepartment . "'";
        } else {
            if ($lstDivision != "") {
                $query .= " AND Grp = 'Div' AND Val = '" . $lstDivision . "'";
            } else {
                if ($txtSNo != "") {
                    $query .= " AND Grp = 'SNo' AND Val = '" . $txtSNo . "'";
                } else {
                    if ($txtRemark != "") {
                        $query .= " AND Grp = 'Remark' AND Val = '" . $txtRemark . "'";
                    } else {
                        if ($txtPhone != "") {
                            $query .= " AND Grp = 'Phone' AND Val = '" . $txtPhone . "'";
                        }
                    }
                }
            }
        }
        $query .= " ORDER BY Grp, Val, Years ";
    }
    if ($excel != "yes") {
        print "<p align='center'><font face='Verdana' size='1'><b>" . $_SESSION[$session_variable . "FlagReportText"] . "</b></font></p>";
    }
    print '<div class="row"><div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12"><div class="card"><div class="card-body table-responsive">';
    if ($prints != "yes") {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800' class='table table-striped table-bordered dataTable'>";
    } else {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%' class='table table-striped table-bordered dataTable'>";
    }
    print "<thead><tr>";
    if ($flagLimitType == "Jan 01") {
        print "<td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Current Shift</font></td>";
    } else {
        print "<td><font face='Verdana' size='2'>Type</font></td> <td><font face='Verdana' size='2'>Value</font></td> <td><font face='Verdana' size='2'>Years</font></td> ";
    }
    print "<td><a title='Violet'><font face='Verdana' size='2' color='Violet'>V</font></a></td> <td><a title='Indigo'><font face='Verdana' size='2' color='Indigo'>I</font></a></td> <td><a title='Blue'><font face='Verdana' size='2' color='Blue'>B</font></a></td> <td><a title='Green'><font face='Verdana' size='2' color='Green'>G</font></a></td> <td bgcolor='brown'><a title='Yellow'><font face='Verdana' size='2' color='Yellow'>Y</font></a></td> <td><a title='Orange'><font face='Verdana' size='2' color='Orange'>O</font></a></td> <td><a title='Red'><font face='Verdana' size='2' color='Red'>R</font></a></td> <td><a title='Gray'><font face='Verdana' size='2' color='Gray'>GR</font></a></td> <td><a title='Brown'><font face='Verdana' size='2' color='Brown'>BR</font></a></td> <td><a title='Purple'><font face='Verdana' size='2' color='Purple'>PR</font></a></td> <td><a title='Magenta'><font face='Verdana' size='2' color='Magenta'>MG</font></a></td> <td><a title='Teal'><font face='Verdana' size='2' color='Teal'>TL</font></a></td> <td><a title='Aqua'><font face='Verdana' size='2' color='Aqua'>AQ</font></a></td> <td><a title='Safron'><font face='Verdana' size='2' color='Safron'>SF</font></a></td> <td><a title='Amber'><font face='Verdana' size='2' color='Amber'>AM</font></a></td> <td><a title='Gold'><font face='Verdana' size='2' color='Gold'>GL</font></a></td> <td><a title='Vermilion'><font face='Verdana' size='2' color='Vermilion'>VM</font></a></td> <td><a title='Silver'><font face='Verdana' size='2' color='Silver'>SL</font></a></td> <td><a title='Maroon'><font face='Verdana' size='2' color='Maroon'>MR</font></a></td> <td><a title='Pink'><font face='Verdana' size='2' color='Pink'>PK</font></a></td> </tr>";
    if ($prints != "yes") {
        print "<tr>";
        if ($flagLimitType == "Jan 01") {
            print "<td colspan='7' align='right'>";
        } else {
            print "<td colspan='3' align='right'>";
        }
        print "<font face='Verdana' size='1' color='#000000'><b>Enter the MAX limits in the respective Boxes in this Row and Click on the Text Boxes in below Rows to Copy this Row values <br><br><a href='#1' onClick='javascript:insertAllFlag()'><font face='Verdana' size='1' color='#000000'>Click Here to Copy the Values in the Text Boxes in this Row to all the below Blank/Zero Textboxes</font></a> <br><br><a href='#1' onClick='javascript:resetAllFlag()'><font face='Verdana' size='1' color='#000000'>Reset All Flags to ZERO</font></a></font></td> <td bgcolor='#F0F0F0'><input size='2' name='txtVAll' value='' class=''><br><br><br><br><a href='#1' onClick='javascript:resetFlag(1)' title='Click Here to RESET All Violet Flags to ZERO'><font face='Verdana' size='1' color='#000000'><b>R</b></font></a></td> <td bgcolor='#F0F0F0'><input size='2' name='txtIAll' value='' class=''><br><br><br><br><a href='#1' onClick='javascript:resetFlag(2)' title='Click Here to RESET All Indigo Flags to ZERO'><font face='Verdana' size='1' color='#000000'><b>R</b></font></a></td> <td bgcolor='#F0F0F0'><input size='2' name='txtBAll' value='' class=''><br><br><br><br><a href='#1' onClick='javascript:resetFlag(3)' title='Click Here to RESET All Blue Flags to ZERO'><font face='Verdana' size='1' color='#000000'><b>R</b></font></a></td> <td bgcolor='#F0F0F0'><input size='2' name='txtGAll' value='' class=''><br><br><br><br><a href='#1' onClick='javascript:resetFlag(4)' title='Click Here to RESET All Green Flags to ZERO'><font face='Verdana' size='1' color='#000000'><b>R</b></font></a></td> <td bgcolor='#F0F0F0'><input size='2' name='txtYAll' value='' class=''><br><br><br><br><a href='#1' onClick='javascript:resetFlag(5)' title='Click Here to RESET All Yellow Flags to ZERO'><font face='Verdana' size='1' color='#000000'><b>R</b></font></a></td> <td bgcolor='#F0F0F0'><input size='2' name='txtOAll' value='' class=''><br><br><br><br><a href='#1' onClick='javascript:resetFlag(6)' title='Click Here to RESET All Orange Flags to ZERO'><font face='Verdana' size='1' color='#000000'><b>R</b></font></a></td> <td bgcolor='#F0F0F0'><input size='2' name='txtRAll' value='' class=''><br><br><br><br><a href='#1' onClick='javascript:resetFlag(7)' title='Click Here to RESET All Red Flags to ZERO'><font face='Verdana' size='1' color='#000000'><b>R</b></font></a></td> <td bgcolor='#F0F0F0'><input size='2' name='txtGRAll' value='' class=''><br><br><br><br><a href='#1' onClick='javascript:resetFlag(8)' title='Click Here to RESET All Gray Flags to ZERO'><font face='Verdana' size='1' color='#000000'><b>R</b></font></a></td> <td bgcolor='#F0F0F0'><input size='2' name='txtBRAll' value='' class=''><br><br><br><br><a href='#1' onClick='javascript:resetFlag(9)' title='Click Here to RESET All Brown Flags to ZERO'><font face='Verdana' size='1' color='#000000'><b>R</b></font></a></td> <td bgcolor='#F0F0F0'><input size='2' name='txtPRAll' value='' class=''><br><br><br><br><a href='#1' onClick='javascript:resetFlag(10)' title='Click Here to RESET All Purple Flags to ZERO'><font face='Verdana' size='1' color='#000000'><b>R</b></font></a></td> <td bgcolor='#F0F0F0'><input size='2' name='txtMGAll' value='' class=''><br><br><br><br><a href='#1' onClick='javascript:resetFlag(11)' title='Click Here to RESET All Magenta Flags to ZERO'><font face='Verdana' size='1' color='#000000'><b>R</b></font></a></td> <td bgcolor='#F0F0F0'><input size='2' name='txtTLAll' value='' class=''><br><br><br><br><a href='#1' onClick='javascript:resetFlag(12)' title='Click Here to RESET All Teal Flags to ZERO'><font face='Verdana' size='1' color='#000000'><b>R</b></font></a></td> <td bgcolor='#F0F0F0'><input size='2' name='txtAQAll' value='' class=''><br><br><br><br><a href='#1' onClick='javascript:resetFlag(13)' title='Click Here to RESET All Aqua Flags to ZERO'><font face='Verdana' size='1' color='#000000'><b>R</b></font></a></td> <td bgcolor='#F0F0F0'><input size='2' name='txtSFAll' value='' class=''><br><br><br><br><a href='#1' onClick='javascript:resetFlag(14)' title='Click Here to RESET All Safron Flags to ZERO'><font face='Verdana' size='1' color='#000000'><b>R</b></font></a></td> <td bgcolor='#F0F0F0'><input size='2' name='txtAMAll' value='' class=''><br><br><br><br><a href='#1' onClick='javascript:resetFlag(15)' title='Click Here to RESET All Amber Flags to ZERO'><font face='Verdana' size='1' color='#000000'><b>R</b></font></a></td> <td bgcolor='#F0F0F0'><input size='2' name='txtGLAll' value='' class=''><br><br><br><br><a href='#1' onClick='javascript:resetFlag(16)' title='Click Here to RESET All Gold Flags to ZERO'><font face='Verdana' size='1' color='#000000'><b>R</b></font></a></td> <td bgcolor='#F0F0F0'><input size='2' name='txtVMAll' value='' class=''><br><br><br><br><a href='#1' onClick='javascript:resetFlag(17)' title='Click Here to RESET All Vermilion Flags to ZERO'><font face='Verdana' size='1' color='#000000'><b>R</b></font></a></td> <td bgcolor='#F0F0F0'><input size='2' name='txtSLAll' value='' class=''><br><br><br><br><a href='#1' onClick='javascript:resetFlag(18)' title='Click Here to RESET All Silver Flags to ZERO'><font face='Verdana' size='1' color='#000000'><b>R</b></font></a></td> <td bgcolor='#F0F0F0'><input size='2' name='txtMRAll' value='' class=''><br><br><br><br><a href='#1' onClick='javascript:resetFlag(19)' title='Click Here to RESET All Maroon Flags to ZERO'><font face='Verdana' size='1' color='#000000'><b>R</b></font></a></td> <td bgcolor='#F0F0F0'><input size='2' name='txtPKAll' value='' class=''><br><br><br><br><a href='#1' onClick='javascript:resetFlag(20)' title='Click Here to RESET All Pink Flags to ZERO'><font face='Verdana' size='1' color='#000000'><b>R</b></font></a></td> </tr>";
    }
    print "</thead>";
    $result = mysqli_query($conn, $query);
    for ($count = 0; $cur = mysqli_fetch_row($result); $count++) {
        if ($flagLimitType == "Jan 01") {
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
            print "<tr><td><a title='ID'><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'> <font face='Verdana' color='#000000' size='1'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $cur[1] . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $cur[5] . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $cur[2] . "</font></a></td> <td><a title='Div/Desg'><font face='Verdana' size='1'>" . $cur[3] . "</font></a></td> <td><a title='Rmk'><font face='Verdana' size='1'>" . $cur[6] . "</font></a></td> <td><a title='Current Shift'><font face='Verdana' size='1'>" . $cur[4] . "</font></a></td> ";
            if ($prints == "yes") {
                print "<td><font face='Verdana' size='1'>" . $cur[7] . "</font></td>";
            } else {
                print "<td><input size='2' name='txtV" . $count . "' id='txtV" . $count . "' value='" . $cur[7] . "' onFocus='javascript:insertFlagAll(document.frm1.txtVAll, document.frm1.txtV" . $count . ")' onBlur='javascript:checkFlagTextbox(document.frm1.txtV" . $count . ")' class=''></td>";
            }
            if ($prints == "yes") {
                print "<td><font face='Verdana' size='1'>" . $cur[8] . "</font></td>";
            } else {
                print "<td><input size='2' name='txtI" . $count . "' id='txtI" . $count . "' value='" . $cur[8] . "' onFocus='javascript:insertFlagAll(document.frm1.txtIAll, document.frm1.txtI" . $count . ")' onBlur='javascript:checkFlagTextbox(document.frm1.txtI" . $count . ")' class=''></td>";
            }
            if ($prints == "yes") {
                print "<td><font face='Verdana' size='1'>" . $cur[9] . "</font></td>";
            } else {
                print "<td><input size='2' name='txtB" . $count . "' id='txtB" . $count . "' value='" . $cur[9] . "' onFocus='javascript:insertFlagAll(document.frm1.txtBAll, document.frm1.txtB" . $count . ")' onBlur='javascript:checkFlagTextbox(document.frm1.txtB" . $count . ")' class=''></td>";
            }
            if ($prints == "yes") {
                print "<td><font face='Verdana' size='1'>" . $cur[10] . "</font></td>";
            } else {
                print "<td><input size='2' name='txtG" . $count . "' id='txtG" . $count . "' value='" . $cur[10] . "' onFocus='javascript:insertFlagAll(document.frm1.txtGAll, document.frm1.txtG" . $count . ")' onBlur='javascript:checkFlagTextbox(document.frm1.txtG" . $count . ")' class=''></td>";
            }
            if ($prints == "yes") {
                print "<td><font face='Verdana' size='1'>" . $cur[11] . "</font></td>";
            } else {
                print "<td><input size='2' name='txtY" . $count . "' id='txtY" . $count . "' value='" . $cur[11] . "' onFocus='javascript:insertFlagAll(document.frm1.txtYAll, document.frm1.txtY" . $count . ")' onBlur='javascript:checkFlagTextbox(document.frm1.txtY" . $count . ")' class=''></td>";
            }
            if ($prints == "yes") {
                print "<td><font face='Verdana' size='1'>" . $cur[12] . "</font></td>";
            } else {
                print "<td><input size='2' name='txtO" . $count . "' id='txtO" . $count . "' value='" . $cur[12] . "' onFocus='javascript:insertFlagAll(document.frm1.txtOAll, document.frm1.txtO" . $count . ")' onBlur='javascript:checkFlagTextbox(document.frm1.txtO" . $count . ")' class=''></td>";
            }
            if ($prints == "yes") {
                print "<td><font face='Verdana' size='1'>" . $cur[13] . "</font></td>";
            } else {
                print "<td><input size='2' name='txtR" . $count . "' id='txtR" . $count . "' value='" . $cur[13] . "' onFocus='javascript:insertFlagAll(document.frm1.txtRAll, document.frm1.txtR" . $count . ")' onBlur='javascript:checkFlagTextbox(document.frm1.txtR" . $count . ")' class=''></td>";
            }
            if ($prints == "yes") {
                print "<td><font face='Verdana' size='1'>" . $cur[15] . "</font></td>";
            } else {
                print "<td><input size='2' name='txtGR" . $count . "' id='txtGR" . $count . "' value='" . $cur[15] . "' onFocus='javascript:insertFlagAll(document.frm1.txtGRAll, document.frm1.txtGR" . $count . ")' onBlur='javascript:checkFlagTextbox(document.frm1.txtGR" . $count . ")' class=''></td>";
            }
            if ($prints == "yes") {
                print "<td><font face='Verdana' size='1'>" . $cur[14] . "</font></td>";
            } else {
                print "<td><input size='2' name='txtBR" . $count . "' id='txtBR" . $count . "' value='" . $cur[14] . "' onFocus='javascript:insertFlagAll(document.frm1.txtBRAll, document.frm1.txtBR" . $count . ")' onBlur='javascript:checkFlagTextbox(document.frm1.txtBR" . $count . ")' class=''></td>";
            }
            if ($prints == "yes") {
                print "<td><font face='Verdana' size='1'>" . $cur[16] . "</font></td>";
            } else {
                print "<td><input size='2' name='txtPR" . $count . "' id='txtPR" . $count . "' value='" . $cur[16] . "' onFocus='javascript:insertFlagAll(document.frm1.txtPRAll, document.frm1.txtPR" . $count . ")' onBlur='javascript:checkFlagTextbox(document.frm1.txtPR" . $count . ")' class=''></td>";
            }
            if ($prints == "yes") {
                print "<td><font face='Verdana' size='1'>" . $cur[17] . "</font></td>";
            } else {
                print "<td><input size='2' name='txtMG" . $count . "' id='txtMG" . $count . "' value='" . $cur[17] . "' onFocus='javascript:insertFlagAll(document.frm1.txtMGAll, document.frm1.txtMG" . $count . ")' onBlur='javascript:checkFlagTextbox(document.frm1.txtMG" . $count . ")' class=''></td>";
            }
            if ($prints == "yes") {
                print "<td><font face='Verdana' size='1'>" . $cur[18] . "</font></td>";
            } else {
                print "<td><input size='2' name='txtTL" . $count . "' id='txtTL" . $count . "' value='" . $cur[18] . "' onFocus='javascript:insertFlagAll(document.frm1.txtTLAll, document.frm1.txtTL" . $count . ")' onBlur='javascript:checkFlagTextbox(document.frm1.txtTL" . $count . ")' class=''></td>";
            }
            if ($prints == "yes") {
                print "<td><font face='Verdana' size='1'>" . $cur[19] . "</font></td>";
            } else {
                print "<td><input size='2' name='txtAQ" . $count . "' id='txtAQ" . $count . "' value='" . $cur[19] . "' onFocus='javascript:insertFlagAll(document.frm1.txtAQAll, document.frm1.txtAQ" . $count . ")' onBlur='javascript:checkFlagTextbox(document.frm1.txtAQ" . $count . ")' class=''></td>";
            }
            if ($prints == "yes") {
                print "<td><font face='Verdana' size='1'>" . $cur[20] . "</font></td>";
            } else {
                print "<td><input size='2' name='txtSF" . $count . "' id='txtSF" . $count . "' value='" . $cur[20] . "' onFocus='javascript:insertFlagAll(document.frm1.txtSFAll, document.frm1.txtSF" . $count . ")' onBlur='javascript:checkFlagTextbox(document.frm1.txtSF" . $count . ")' class=''></td>";
            }
            if ($prints == "yes") {
                print "<td><font face='Verdana' size='1'>" . $cur[21] . "</font></td>";
            } else {
                print "<td><input size='2' name='txtAM" . $count . "' id='txtAM" . $count . "' value='" . $cur[21] . "' onFocus='javascript:insertFlagAll(document.frm1.txtAMAll, document.frm1.txtAM" . $count . ")' onBlur='javascript:checkFlagTextbox(document.frm1.txtAM" . $count . ")' class=''></td>";
            }
            if ($prints == "yes") {
                print "<td><font face='Verdana' size='1'>" . $cur[22] . "</font></td>";
            } else {
                print "<td><input size='2' name='txtGL" . $count . "' id='txtGL" . $count . "' value='" . $cur[22] . "' onFocus='javascript:insertFlagAll(document.frm1.txtGLAll, document.frm1.txtGL" . $count . ")' onBlur='javascript:checkFlagTextbox(document.frm1.txtGL" . $count . ")' class=''></td>";
            }
            if ($prints == "yes") {
                print "<td><font face='Verdana' size='1'>" . $cur[23] . "</font></td>";
            } else {
                print "<td><input size='2' id='txtVM" . $count . "' name='txtVM" . $count . "' value='" . $cur[23] . "' onFocus='javascript:insertFlagAll(document.frm1.txtVMAll, document.frm1.txtVM" . $count . ")' onBlur='javascript:checkFlagTextbox(document.frm1.txtVM" . $count . ")' class=''></td>";
            }
            if ($prints == "yes") {
                print "<td><font face='Verdana' size='1'>" . $cur[24] . "</font></td>";
            } else {
                print "<td><input size='2' name='txtSL" . $count . "' id='txtSL" . $count . "' value='" . $cur[24] . "' onFocus='javascript:insertFlagAll(document.frm1.txtSLAll, document.frm1.txtSL" . $count . ")' onBlur='javascript:checkFlagTextbox(document.frm1.txtSL" . $count . ")' class=''></td>";
            }
            if ($prints == "yes") {
                print "<td><font face='Verdana' size='1'>" . $cur[25] . "</font></td>";
            } else {
                print "<td><input size='2' name='txtMR" . $count . "' id='txtMR" . $count . "' value='" . $cur[25] . "' onFocus='javascript:insertFlagAll(document.frm1.txtMRAll, document.frm1.txtMR" . $count . ")' onBlur='javascript:checkFlagTextbox(document.frm1.txtMR" . $count . ")' class=''></td>";
            }
            if ($prints == "yes") {
                print "<td><font face='Verdana' size='1'>" . $cur[26] . "</font></td>";
            } else {
                print "<td><input size='2' name='txtPK" . $count . "' id='txtPK" . $count . "' value='" . $cur[26] . "' onFocus='javascript:insertFlagAll(document.frm1.txtPKAll, document.frm1.txtPK" . $count . ")' onBlur='javascript:checkFlagTextbox(document.frm1.txtPK" . $count . ")' class=''></td>";
            }
            print "</tr>";
        } else {
            print "<tr><td><a title='Type'><input type='hidden' name='txh" . $count . "' value='" . $cur[3] . "'> <input type='hidden' name='txhGrp" . $count . "' value='" . $cur[1] . "'> <input type='hidden' name='txhVal" . $count . "' value='" . $cur[2] . "'> <font face='Verdana' color='#000000' size='1'>" . $cur[1] . "</font></a></td> <td><a title='Value'><font face='Verdana' size='1'>" . $cur[2] . "</font></a></td> <td><a title='Years'><font face='Verdana' size='1'>" . $cur[3] . "</font></a></td> <td><a title='Violet'><input size='2' name='txtV" . $count . "' id='txtV" . $count . "' value='" . $cur[4] . "'></td> <td><a title='Indigo'><input size='2' name='txtI" . $count . "' id='txtI" . $count . "' value='" . $cur[5] . "'></td> <td><a title='Blue'><input size='2' name='txtB" . $count . "' id='txtB" . $count . "' value='" . $cur[6] . "'></td> <td><a title='Green'><input size='2' name='txtG" . $count . "' id='txtG" . $count . "' value='" . $cur[7] . "'></td> <td><a title='Yellow'><input size='2' name='txtY" . $count . "' id='txtY" . $count . "' value='" . $cur[8] . "'></td> <td><a title='Orange'><input size='2' name='txtO" . $count . "' id='txtO" . $count . "' value='" . $cur[9] . "'></td> <td><a title='Red'><input size='2' name='txtR" . $count . "' id='txtR" . $count . "' value='" . $cur[10] . "'></td> <td><a title='Gray'><input size='2' name='txtGR" . $count . "' id='txtGR" . $count . "' value='" . $cur[11] . "'></td> <td><a title='Brown'><input size='2' name='txtBR" . $count . "' id='txtBR" . $count . "' value='" . $cur[12] . "'></td> <td><a title='Purple'><input size='2' name='txtPR" . $count . "' id='txtPR" . $count . "' value='" . $cur[13] . "'></td> <td><a title='Magenta'><input size='2' name='txtMG" . $count . "' id='txtMG" . $count . "' value='" . $cur[14] . "'></td> <td><a title='Teal'><input size='2' name='txtTL" . $count . "' id='txtTL" . $count . "' value='" . $cur[15] . "'></td> <td><a title='Aqua'><input size='2' name='txtAQ" . $count . "' id='txtAQ" . $count . "' value='" . $cur[16] . "'></td> <td><a title='Safron'><input size='2' name='txtSF" . $count . "' id='txtSF" . $count . "' value='" . $cur[17] . "'></td> <td><a title='Amber'><input size='2' name='txtAM" . $count . "' id='txtAM" . $count . "' value='" . $cur[18] . "'></td> <td><a title='Gold'><input size='2' name='txtGL" . $count . "' id='txtGL" . $count . "' value='" . $cur[19] . "'></td> <td><a title='Vermilion'><input size='2' name='txtVM" . $count . "' id='txtVM" . $count . "' value='" . $cur[20] . "'></td> <td><a title='Silver'><input size='2' name='txtSL" . $count . "' id='txtSL" . $count . "' value='" . $cur[21] . "'></td> <td><a title='Maroon'><input size='2' name='txtMR" . $count . "' id='txtMR" . $count . "' value='" . $cur[22] . "'></td> <td><a title='Pink'><input size='2' name='txtPK" . $count . "' id='txtPK" . $count . "' value='" . $cur[23] . "'></td></tr> ";
        }
    }
    if ($flagLimitType == "Employee Start Date" && $count == 0) {
        if ($lstDepartment != "") {
            for ($i = 0; $i < 26; $i++) {
                print "<tr><td><a title='Type'><input type='hidden' name='txh" . $i . "' value='" . ($i + 1) . "'> <input type='hidden' name='txhGrp" . $i . "' value='Dept'> <input type='hidden' name='txhVal" . $i . "' value='" . $lstDepartment . "'> <font face='Verdana' color='#000000' size='1'>Dept</font></a></td> <td><a title='Value'><font face='Verdana' size='1'>" . $lstDepartment . "</font></a></td> <td><a title='Years'><font face='Verdana' size='1'>" . ($i + 1) . "</font></a></td> <td><a title='Violet'><input size='2' name='txtV" . $i . "' id='txtV" . $i . "' value='365'></td> <td><a title='Indigo'><input size='2' name='txtI" . $i . "' id='txtI" . $i . "' value='365'></td> <td><a title='Blue'><input size='2' name='txtB" . $i . "' id='txtB" . $i . "' value='365'></td> <td><a title='Green'><input size='2' name='txtG" . $i . "' id='txtG" . $i . "' value='365'></td> <td><a title='Yellow'><input size='2' name='txtY" . $i . "' id='txtY" . $i . "' value='365'></td> <td><a title='Orange'><input size='2' name='txtO" . $i . "' id='txtO" . $i . "' value='365'></td> <td><a title='Red'><input size='2' name='txtR" . $i . "' id='txtR" . $i . "' value='365'></td> <td><a title='Gray'><input size='2' name='txtGR" . $i . "' id='txtGR" . $i . "' value='365'></td> <td><a title='Brown'><input size='2' name='txtBR" . $i . "' id='txtBR" . $i . "' value='365'></td> <td><a title='Purple'><input size='2' name='txtPR" . $i . "' id='txtPR" . $i . "' value='365'></td> <td><a title='Magenta'><input size='2' name='txtMG" . $i . "' id='txtMG" . $i . "' value='365'></td> <td><a title='Teal'><input size='2' name='txtTL" . $i . "' id='txtTL" . $i . "' value='365'></td> <td><a title='Aqua'><input size='2' name='txtAQ" . $i . "' id='txtAQ" . $i . "' value='365'></td> <td><a title='Safron'><input size='2' name='txtSF" . $i . "' id='txtSF" . $i . "' value='365'></td> <td><a title='Amber'><input size='2' name='txtAM" . $i . "' id='txtAM" . $i . "' value='365'></td> <td><a title='Gold'><input size='2' name='txtGL" . $i . "' id='txtGL" . $i . "' value='365'></td> <td><a title='Vermilion'><input size='2' name='txtVM" . $i . "' id='txtVM" . $i . "' value='365'></td> <td><a title='Silver'><input size='2' name='txtSL" . $i . "' id='txtSL" . $i . "' value='365'></td> <td><a title='Maroon'><input size='2' name='txtMR" . $i . "' id='txtMR" . $i . "' value='365'></td> <td><a title='Pink'><input size='2' name='txtPK" . $i . "' id='txtPK" . $i . "' value='365'></td></tr>";
                $count++;
            }
        }
        if ($lstDivision != "") {
            for ($i = 0; $i < 26; $i++) {
                print "<tr><td><a title='Type'><input type='hidden' name='txh" . $i . "' value='" . ($i + 1) . "'> <input type='hidden' name='txhGrp" . $i . "' value='Div'> <input type='hidden' name='txhVal" . $i . "' value='" . $lstDivision . "'><font face='Verdana' color='#000000' size='1'>Div</font></a></td> <td><a title='Value'><font face='Verdana' size='1'>" . $lstDivision . "</font></a></td> <td><a title='Years'><font face='Verdana' size='1'>" . ($i + 1) . "</font></a></td> <td><a title='Violet'><input size='2' name='txtV" . $i . "' id='txtV" . $i . "' value='365'></td> <td><a title='Indigo'><input size='2' name='txtI" . $i . "' id='txtI" . $i . "' value='365'></td> <td><a title='Blue'><input size='2' name='txtB" . $i . "' id='txtB" . $i . "' value='365'></td> <td><a title='Green'><input size='2' name='txtG" . $i . "' id='txtG" . $i . "' value='365'></td> <td><a title='Yellow'><input size='2' name='txtY" . $i . "' id='txtY" . $i . "' value='365'></td> <td><a title='Orange'><input size='2' name='txtO" . $i . "' id='txtO" . $i . "' value='365'></td> <td><a title='Red'><input size='2' name='txtR" . $i . "' id='txtR" . $i . "' value='365'></td> <td><a title='Gray'><input size='2' name='txtGR" . $i . "' id='txtGR" . $i . "' value='365'></td> <td><a title='Brown'><input size='2' name='txtBR" . $i . "' id='txtBR" . $i . "' value='365'></td> <td><a title='Purple'><input size='2' name='txtPR" . $i . "' id='txtPR" . $i . "' value='365'></td> <td><a title='Magenta'><input size='2' name='txtMG" . $i . "' id='txtMG" . $i . "' value='365'></td> <td><a title='Teal'><input size='2' name='txtTL" . $i . "' id='txtTL" . $i . "' value='365'></td> <td><a title='Aqua'><input size='2' name='txtAQ" . $i . "' id='txtAQ" . $i . "' value='365'></td> <td><a title='Safron'><input size='2' name='txtSF" . $i . "' id='txtSF" . $i . "' value='365'></td> <td><a title='Amber'><input size='2' name='txtAM" . $i . "' id='txtAM" . $i . "' value='365'></td> <td><a title='Gold'><input size='2' name='txtGL" . $i . "' id='txtGL" . $i . "' value='365'></td> <td><a title='Vermilion'><input size='2' name='txtVM" . $i . "' id='txtVM" . $i . "' value='365'></td> <td><a title='Silver'><input size='2' name='txtSL" . $i . "' id='txtSL" . $i . "' value='365'></td> <td><a title='Maroon'><input size='2' name='txtMR" . $i . "' id='txtMR" . $i . "' value='365'></td> <td><a title='Pink'><input size='2' name='txtPK" . $i . "' id='txtPK" . $i . "' value='365'></td></tr>";
                $count++;
            }
        }
        if ($txtSNo != "") {
            for ($i = 0; $i < 26; $i++) {
                print "<tr><td><a title='Type'><input type='hidden' name='txh" . $i . "' value='" . ($i + 1) . "'> <input type='hidden' name='txhGrp" . $i . "' value='SNo'> <input type='hidden' name='txhVal" . $i . "' value='" . $txtSNo . "'><font face='Verdana' color='#000000' size='1'>SNo</font></a></td> <td><a title='Value'><font face='Verdana' size='1'>" . $txtSNo . "</font></a></td> <td><a title='Years'><font face='Verdana' size='1'>" . ($i + 1) . "</font></a></td> <td><a title='Violet'><input size='2' name='txtV" . $i . "' id='txtV" . $i . "' value='365'></td> <td><a title='Indigo'><input size='2' name='txtI" . $i . "' id='txtI" . $i . "' value='365'></td> <td><a title='Blue'><input size='2' name='txtB" . $i . "' id='txtB" . $i . "' value='365'></td> <td><a title='Green'><input size='2' name='txtG" . $i . "' id='txtG" . $i . "' value='365'></td> <td><a title='Yellow'><input size='2' name='txtY" . $i . "' id='txtY" . $i . "' value='365'></td> <td><a title='Orange'><input size='2' name='txtO" . $i . "' id='txtO" . $i . "' value='365'></td> <td><a title='Red'><input size='2' name='txtR" . $i . "' id='txtR" . $i . "' value='365'></td> <td><a title='Gray'><input size='2' name='txtGR" . $i . "' id='txtGR" . $i . "' value='365'></td> <td><a title='Brown'><input size='2' name='txtBR" . $i . "' id='txtBR" . $i . "' value='365'></td> <td><a title='Purple'><input size='2' name='txtPR" . $i . "' id='txtPR" . $i . "' value='365'></td> <td><a title='Magenta'><input size='2' name='txtMG" . $i . "' id='txtMG" . $i . "' value='365'></td> <td><a title='Teal'><input size='2' name='txtTL" . $i . "' id='txtTL" . $i . "' value='365'></td> <td><a title='Aqua'><input size='2' name='txtAQ" . $i . "' id='txtAQ" . $i . "' value='365'></td> <td><a title='Safron'><input size='2' name='txtSF" . $i . "' id='txtSF" . $i . "' value='365'></td> <td><a title='Amber'><input size='2' name='txtAM" . $i . "' id='txtAM" . $i . "' value='365'></td> <td><a title='Gold'><input size='2' name='txtGL" . $i . "' id='txtGL" . $i . "' value='365'></td> <td><a title='Vermilion'><input size='2' name='txtVM" . $i . "' id='txtVM" . $i . "' value='365'></td> <td><a title='Silver'><input size='2' name='txtSL" . $i . "' id='txtSL" . $i . "' value='365'></td> <td><a title='Maroon'><input size='2' name='txtMR" . $i . "' id='txtMR" . $i . "' value='365'></td> <td><a title='Pink'><input size='2' name='txtPK" . $i . "' id='txtPK" . $i . "' value='365'></td></tr>";
                $count++;
            }
        }
        if ($txtRemark != "") {
            for ($i = 0; $i < 26; $i++) {
                print "<tr><td><a title='Type'><input type='hidden' name='txh" . $i . "' value='" . ($i + 1) . "'> <input type='hidden' name='txhGrp" . $i . "' value='Remark'> <input type='hidden' name='txhVal" . $i . "' value='" . $txtRemark . "'><font face='Verdana' color='#000000' size='1'>Remark</font></a></td> <td><a title='Value'><font face='Verdana' size='1'>" . $txtRemark . "</font></a></td> <td><a title='Years'><font face='Verdana' size='1'>" . ($i + 1) . "</font></a></td> <td><a title='Violet'><input size='2' name='txtV" . $i . "' id='txtV" . $i . "' value='365'></td> <td><a title='Indigo'><input size='2' name='txtI" . $i . "' id='txtI" . $i . "' value='365'></td> <td><a title='Blue'><input size='2' name='txtB" . $i . "' id='txtB" . $i . "' value='365'></td> <td><a title='Green'><input size='2' name='txtG" . $i . "' id='txtG" . $i . "' value='365'></td> <td><a title='Yellow'><input size='2' name='txtY" . $i . "' id='txtY" . $i . "' value='365'></td> <td><a title='Orange'><input size='2' name='txtO" . $i . "' id='txtO" . $i . "' value='365'></td> <td><a title='Red'><input size='2' name='txtR" . $i . "' id='txtR" . $i . "' value='365'></td> <td><a title='Gray'><input size='2' name='txtGR" . $i . "' id='txtGR" . $i . "' value='365'></td> <td><a title='Brown'><input size='2' name='txtBR" . $i . "' id='txtBR" . $i . "' value='365'></td> <td><a title='Purple'><input size='2' name='txtPR" . $i . "' id='txtPR" . $i . "' value='365'></td> <td><a title='Magenta'><input size='2' name='txtMG" . $i . "' id='txtMG" . $i . "' value='365'></td> <td><a title='Teal'><input size='2' name='txtTL" . $i . "' id='txtTL" . $i . "' value='365'></td> <td><a title='Aqua'><input size='2' name='txtAQ" . $i . "' id='txtAQ" . $i . "' value='365'></td> <td><a title='Safron'><input size='2' name='txtSF" . $i . "' id='txtSF" . $i . "' value='365'></td> <td><a title='Amber'><input size='2' name='txtAM" . $i . "' id='txtAM" . $i . "' value='365'></td> <td><a title='Gold'><input size='2' name='txtGL" . $i . "' id='txtGL" . $i . "' value='365'></td> <td><a title='Vermilion'><input size='2' name='txtVM" . $i . "' id='txtVM" . $i . "' value='365'></td> <td><a title='Silver'><input size='2' name='txtSL" . $i . "' id='txtSL" . $i . "' value='365'></td> <td><a title='Maroon'><input size='2' name='txtMR" . $i . "' id='txtMR" . $i . "' value='365'></td> <td><a title='Pink'><input size='2' name='txtPK" . $i . "' id='txtPK" . $i . "' value='365'></td></tr>";
                $count++;
            }
        }
        if ($txtPhone != "") {
            for ($i = 0; $i < 26; $i++) {
                print "<tr><td><a title='Type'><input type='hidden' name='txh" . $i . "' value='" . ($i + 1) . "'> <input type='hidden' name='txhGrp" . $i . "' value='Phone'> <input type='hidden' name='txhVal" . $i . "' value='" . $txtPhone . "'><font face='Verdana' color='#000000' size='1'>Phone</font></a></td> <td><a title='Value'><font face='Verdana' size='1'>" . $txtPhone . "</font></a></td> <td><a title='Years'><font face='Verdana' size='1'>" . ($i + 1) . "</font></a></td> <td><a title='Violet'><input size='2' name='txtV" . $i . "' id='txtV" . $i . "' value='365'></td> <td><a title='Indigo'><input size='2' name='txtI" . $i . "' id='txtI" . $i . "' value='365'></td> <td><a title='Blue'><input size='2' name='txtB" . $i . "' id='txtB" . $i . "' value='365'></td> <td><a title='Green'><input size='2' name='txtG" . $i . "' id='txtG" . $i . "' value='365'></td> <td><a title='Yellow'><input size='2' name='txtY" . $i . "' id='txtY" . $i . "' value='365'></td> <td><a title='Orange'><input size='2' name='txtO" . $i . "' id='txtO" . $i . "' value='365'></td> <td><a title='Red'><input size='2' name='txtR" . $i . "' id='txtR" . $i . "' value='365'></td> <td><a title='Gray'><input size='2' name='txtGR" . $i . "' id='txtGR" . $i . "' value='365'></td> <td><a title='Brown'><input size='2' name='txtBR" . $i . "' id='txtBR" . $i . "' value='365'></td> <td><a title='Purple'><input size='2' name='txtPR" . $i . "' id='txtPR" . $i . "' value='365'></td> <td><a title='Magenta'><input size='2' name='txtMG" . $i . "' id='txtMG" . $i . "' value='365'></td> <td><a title='Teal'><input size='2' name='txtTL" . $i . "' id='txtTL" . $i . "' value='365'></td> <td><a title='Aqua'><input size='2' name='txtAQ" . $i . "' id='txtAQ" . $i . "' value='365'></td> <td><a title='Safron'><input size='2' name='txtSF" . $i . "' id='txtSF" . $i . "' value='365'></td> <td><a title='Amber'><input size='2' name='txtAM" . $i . "' id='txtAM" . $i . "' value='365'></td> <td><a title='Gold'><input size='2' name='txtGL" . $i . "' id='txtGL" . $i . "' value='365'></td> <td><a title='Vermilion'><input size='2' name='txtVM" . $i . "' id='txtVM" . $i . "' value='365'></td> <td><a title='Silver'><input size='2' name='txtSL" . $i . "' id='txtSL" . $i . "' value='365'></td> <td><a title='Maroon'><input size='2' name='txtMR" . $i . "' id='txtMR" . $i . "' value='365'></td> <td><a title='Pink'><input size='2' name='txtPK" . $i . "' id='txtPK" . $i . "' value='365'></td></tr>";
                $count++;
            }
        }
    }
    print "</table><input type='hidden' name='txhCount' value='" . $count . "'>";
    if ($excel != "yes") {
        print "<br><p align='center'><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $count . "</b></font></p>";
    }
    if ($prints != "yes" && 0 < $count) {
        if ((strpos($userlevel, $current_module . "A") !== false || strpos($userlevel, $current_module . "E") !== false || strpos($userlevel, $current_module . "D") !== false) && $_SESSION[$session_variable . "LockDate"] < insertDate($txtFrom)) {
            print "<table>";
            if ($flagLimitType == "Jan 01") {
                if ($lstDepartment != "") {
                    print "<tr><td><input type='checkbox' name='chkApplyAllDept'></td><td><font face='Verdana' size='1'>Apply Changes to all Employees in the Selected Department in Future (Picks Data from TOPMOST TextBox Row) (Blank Box = 365)</font></td></tr>";
                }
                if ($lstDivision != "") {
                    print "<tr><td><input type='checkbox' name='chkApplyAllDiv'></td><td><font face='Verdana' size='1'>Apply Changes to all Employees in the Selected Div/Desg in Future (Picks Data from TOPMOST TextBox Row) (Blank Box = 365)</font></td></tr>";
                }
                if ($txtRemark != "") {
                    print "<tr><td><input type='checkbox' name='chkApplyAllRemark'></td><td><font face='Verdana' size='1'>Apply Changes to all Employees with the Selected Remark in Future (Picks Data from TOPMOST TextBox Row) (Blank Box = 365)</font></td></tr>";
                }
                if ($txtSNo != "") {
                    print "<tr><td><input type='checkbox' name='chkApplyAllSNo'></td><td><font face='Verdana' size='1'>Apply Changes to all Employees with the Selected " . $_SESSION[$session_variable . "IDColumnName"] . " in Future (Picks Data from TOPMOST TextBox Row) (Blank Box = 365)</font></td></tr>";
                }
                if ($txtPhone != "") {
                    print "<tr><td><input type='checkbox' name='chkApplyAllPhone'></td><td><font face='Verdana' size='1'>Apply Changes to all Employees with the Selected " . $_SESSION[$session_variable . "PhoneColumnName"] . " in Future (Picks Data from TOPMOST TextBox Row) (Blank Box = 365)</font></td></tr>";
                }
            }
            print "<p align='center'><input type='button' value='Save Changes' class='btn btn-primary' onClick='saveChanges()' name='btSubmit'></p>";
            print "</table>";
        } else {
            print "<br>";
        }
        
        print "<p align='center'><input type='button' value='Print Report' class='btn btn-primary' onClick='checkPrint(0)'>&nbsp;&nbsp;<input type='button' class='btn btn-primary' value='Excel' onClick='checkPrint(1)'></p>";
    }
}
print "</form>";
echo "\r\n<script>\r\nfunction openWindow(a){\r\n\twindow.open(\"EmployeeFlagLimit.php?act=viewRecord&txtID=\"+a, \"\",\"height=400;width=400\");\r\n}\r\n\r\nfunction checkPrint(a){\r\n\tvar x = document.frm1;\r\n\tif (a == 0){\r\n\t\tif (confirm('Go Green - Think Twice before you Print this Document \\nAre you sure want to Print?')){\r\n\t\t\tx.action = 'EmployeeFlagLimit.php?prints=yes';\t\t\t\r\n\t\t}else{\r\n\t\t\treturn;\r\n\t\t}\r\n\t}else{\r\n\t\tx.action = 'EmployeeFlagLimit.php?prints=yes&excel=yes';\t\t\t\r\n\t}\r\n\tx.target = '_blank';\r\n\tx.submit();\r\n}\r\n\r\nfunction checkSearch(){\r\n\tvar x = document.frm1;\t\r\n\tx.action = 'EmployeeFlagLimit.php?prints=no';\r\n\tx.target = '_self';\r\n\tx.btSearch.disabled = true;\r\n\tx.submit();\r\n}\r\n\r\nfunction checkFlagTextbox(x){\r\n\tif (x.value*1 != x.value/1){\r\n\t\talert(\"ONLY Numeric Value ALLOWED\");\r\n\t\tx.focus();\r\n\t}\r\n}\r\n\r\nfunction insertFlagAll(x, y){\r\n\ta = x.value;\r\n\tb = y.value;\r\n\tif (a != \"\" && a*1 == a/1){\r\n\t\ty.value = a;\r\n\t}\r\n}\r\n\r\nfunction insertAllFlag(){\r\n\tx = document.frm1;\r\n\tif (x.txtVAll.value != \"\" && x.txtVAll.value != 0){\r\n\t\tif (confirm(\"Enter Violet = \"+x.txtVAll.value+\" in all the Below Blank/Zero Violet Records?\")){\t\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\r\n\t\t\t\tif (document.getElementById(\"txtV\"+i).value == \"\" || document.getElementById(\"txtV\"+i).value == 0){\r\n\t\t\t\t\tdocument.getElementById(\"txtV\"+i).value = x.txtVAll.value;\r\n\t\t\t\t}\r\n\t\t\t}\r\n\t\t}\r\n\t}\r\n\tif (x.txtIAll.value != \"\" && x.txtIAll.value != 0){\r\n\t\tif (confirm(\"Enter Indigo = \"+x.txtIAll.value+\" in all the Below Blank/Zero Indigo Records?\")){\t\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\r\n\t\t\t\tif (document.getElementById(\"txtI\"+i).value == \"\" || document.getElementById(\"txtI\"+i).value == 0){\r\n\t\t\t\t\tdocument.getElementById(\"txtI\"+i).value = x.txtIAll.value;\r\n\t\t\t\t}\r\n\t\t\t}\r\n\t\t}\r\n\t}\r\n\tif (x.txtBAll.value != \"\" && x.txtBAll.value != 0){\r\n\t\tif (confirm(\"Enter Blue = \"+x.txtBAll.value+\" in all the Below Blank/Zero Blue Records?\")){\t\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\r\n\t\t\t\tif (document.getElementById(\"txtB\"+i).value == \"\" || document.getElementById(\"txtB\"+i).value == 0){\r\n\t\t\t\t\tdocument.getElementById(\"txtB\"+i).value = x.txtBAll.value;\r\n\t\t\t\t}\r\n\t\t\t}\r\n\t\t}\r\n\t}\r\n\tif (x.txtGAll.value != \"\" && x.txtGAll.value != 0){\r\n\t\tif (confirm(\"Enter Green = \"+x.txtGAll.value+\" in all the Below Blank/Zero Green Records?\")){\t\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\r\n\t\t\t\tif (document.getElementById(\"txtG\"+i).value == \"\" || document.getElementById(\"txtG\"+i).value == 0){\r\n\t\t\t\t\tdocument.getElementById(\"txtG\"+i).value = x.txtGAll.value;\r\n\t\t\t\t}\r\n\t\t\t}\r\n\t\t}\r\n\t}\r\n\tif (x.txtYAll.value != \"\" && x.txtYAll.value != 0){\r\n\t\tif (confirm(\"Enter Yellow = \"+x.txtYAll.value+\" in all the Below Blank/Zero Yellow Records?\")){\t\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\r\n\t\t\t\tif (document.getElementById(\"txtY\"+i).value == \"\" || document.getElementById(\"txtY\"+i).value == 0){\r\n\t\t\t\t\tdocument.getElementById(\"txtY\"+i).value = x.txtYAll.value;\r\n\t\t\t\t}\r\n\t\t\t}\r\n\t\t}\r\n\t}\r\n\tif (x.txtOAll.value != \"\" && x.txtOAll.value != 0){\r\n\t\tif (confirm(\"Enter Orange = \"+x.txtOAll.value+\" in all the Below Blank/Zero Orange Records?\")){\t\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\r\n\t\t\t\tif (document.getElementById(\"txtO\"+i).value == \"\" || document.getElementById(\"txtO\"+i).value == 0){\r\n\t\t\t\t\tdocument.getElementById(\"txtO\"+i).value = x.txtOAll.value;\r\n\t\t\t\t}\r\n\t\t\t}\r\n\t\t}\r\n\t}\r\n\tif (x.txtRAll.value != \"\" && x.txtRAll.value != 0){\r\n\t\tif (confirm(\"Enter Red = \"+x.txtRAll.value+\" in all the Below Blank/Zero Red Records?\")){\t\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\r\n\t\t\t\tif (document.getElementById(\"txtR\"+i).value == \"\" || document.getElementById(\"txtR\"+i).value == 0){\r\n\t\t\t\t\tdocument.getElementById(\"txtR\"+i).value = x.txtRAll.value;\r\n\t\t\t\t}\r\n\t\t\t}\r\n\t\t}\r\n\t}\t\r\n\tif (x.txtGRAll.value != \"\" && x.txtGRAll.value != 0){\r\n\t\tif (confirm(\"Enter Gray = \"+x.txtGRAll.value+\" in all the Below Blank/Zero Gray Records?\")){\t\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\r\n\t\t\t\tif (document.getElementById(\"txtGR\"+i).value == \"\" || document.getElementById(\"txtGR\"+i).value == 0){\r\n\t\t\t\t\tdocument.getElementById(\"txtGR\"+i).value = x.txtGRAll.value;\r\n\t\t\t\t}\r\n\t\t\t}\r\n\t\t}\r\n\t}\r\n\tif (x.txtBRAll.value != \"\" && x.txtBRAll.value != 0){\r\n\t\tif (confirm(\"Enter Brown = \"+x.txtBRAll.value+\" in all the Below Blank/Zero Brown Records?\")){\t\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\r\n\t\t\t\tif (document.getElementById(\"txtBR\"+i).value == \"\" || document.getElementById(\"txtBR\"+i).value == 0){\r\n\t\t\t\t\tdocument.getElementById(\"txtBR\"+i).value = x.txtBRAll.value;\r\n\t\t\t\t}\r\n\t\t\t}\r\n\t\t}\r\n\t}\r\n\tif (x.txtPRAll.value != \"\" && x.txtPRAll.value != 0){\r\n\t\tif (confirm(\"Enter Purple = \"+x.txtPRAll.value+\" in all the Below Blank/Zero Purple Records?\")){\t\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\r\n\t\t\t\tif (document.getElementById(\"txtPR\"+i).value == \"\" || document.getElementById(\"txtPR\"+i).value == 0){\r\n\t\t\t\t\tdocument.getElementById(\"txtPR\"+i).value = x.txtPRAll.value;\r\n\t\t\t\t}\r\n\t\t\t}\r\n\t\t}\r\n\t}\r\n\tif (x.txtMGAll.value != \"\" && x.txtMGAll.value != 0){\r\n\t\tif (confirm(\"Enter Magenta = \"+x.txtMGAll.value+\" in all the Below Blank/Zero Magenta Records?\")){\t\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\r\n\t\t\t\tif (document.getElementById(\"txtMG\"+i).value == \"\" || document.getElementById(\"txtMG\"+i).value == 0){\r\n\t\t\t\t\tdocument.getElementById(\"txtMG\"+i).value = x.txtMGAll.value;\r\n\t\t\t\t}\r\n\t\t\t}\r\n\t\t}\r\n\t}\r\n\tif (x.txtTLAll.value != \"\" && x.txtTLAll.value != 0){\r\n\t\tif (confirm(\"Enter Teal = \"+x.txtTLAll.value+\" in all the Below Blank/Zero Teal Records?\")){\t\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\r\n\t\t\t\tif (document.getElementById(\"txtTL\"+i).value == \"\" || document.getElementById(\"txtTL\"+i).value == 0){\r\n\t\t\t\t\tdocument.getElementById(\"txtTL\"+i).value = x.txtTLAll.value;\r\n\t\t\t\t}\r\n\t\t\t}\r\n\t\t}\r\n\t}\r\n\tif (x.txtAQAll.value != \"\" && x.txtAQAll.value != 0){\r\n\t\tif (confirm(\"Enter Aqua = \"+x.txtAQAll.value+\" in all the Below Blank/Zero Aqua Records?\")){\t\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\r\n\t\t\t\tif (document.getElementById(\"txtAQ\"+i).value == \"\" || document.getElementById(\"txtAQ\"+i).value == 0){\r\n\t\t\t\t\tdocument.getElementById(\"txtAQ\"+i).value = x.txtAQAll.value;\r\n\t\t\t\t}\r\n\t\t\t}\r\n\t\t}\r\n\t}\r\n\tif (x.txtSFAll.value != \"\" && x.txtSFAll.value != 0){\r\n\t\tif (confirm(\"Enter Safron = \"+x.txtSFAll.value+\" in all the Below Blank/Zero Safron Records?\")){\t\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\r\n\t\t\t\tif (document.getElementById(\"txtSF\"+i).value == \"\" || document.getElementById(\"txtSF\"+i).value == 0){\r\n\t\t\t\t\tdocument.getElementById(\"txtSF\"+i).value = x.txtSFAll.value;\r\n\t\t\t\t}\r\n\t\t\t}\r\n\t\t}\r\n\t}\r\n\tif (x.txtAMAll.value != \"\" && x.txtAMAll.value != 0){\r\n\t\tif (confirm(\"Enter Amber = \"+x.txtAMAll.value+\" in all the Below Blank/Zero Amber Records?\")){\t\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\r\n\t\t\t\tif (document.getElementById(\"txtAM\"+i).value == \"\" || document.getElementById(\"txtAM\"+i).value == 0){\r\n\t\t\t\t\tdocument.getElementById(\"txtAM\"+i).value = x.txtAMAll.value;\r\n\t\t\t\t}\r\n\t\t\t}\r\n\t\t}\r\n\t}\r\n\tif (x.txtGLAll.value != \"\" && x.txtGLAll.value != 0){\r\n\t\tif (confirm(\"Enter Gold = \"+x.txtGLAll.value+\" in all the Below Blank/Zero Gold Records?\")){\t\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\r\n\t\t\t\tif (document.getElementById(\"txtGL\"+i).value == \"\" || document.getElementById(\"txtGL\"+i).value == 0){\r\n\t\t\t\t\tdocument.getElementById(\"txtGL\"+i).value = x.txtGLAll.value;\r\n\t\t\t\t}\r\n\t\t\t}\r\n\t\t}\r\n\t}\r\n\tif (x.txtVMAll.value != \"\" && x.txtVMAll.value != 0){\r\n\t\tif (confirm(\"Enter Vermilion = \"+x.txtVMAll.value+\" in all the Below Blank/Zero Vermilion Records?\")){\t\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\r\n\t\t\t\tif (document.getElementById(\"txtVM\"+i).value == \"\" || document.getElementById(\"txtVM\"+i).value == 0){\r\n\t\t\t\t\tdocument.getElementById(\"txtVM\"+i).value = x.txtVMAll.value;\r\n\t\t\t\t}\r\n\t\t\t}\r\n\t\t}\r\n\t}\t\r\n\tif (x.txtSLAll.value != \"\" && x.txtSLAll.value != 0){\r\n\t\tif (confirm(\"Enter Silver = \"+x.txtSLAll.value+\" in all the Below Blank/Zero Silver Records?\")){\t\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\r\n\t\t\t\tif (document.getElementById(\"txtSL\"+i).value == \"\" || document.getElementById(\"txtSL\"+i).value == 0){\r\n\t\t\t\t\tdocument.getElementById(\"txtSL\"+i).value = x.txtSLAll.value;\r\n\t\t\t\t}\r\n\t\t\t}\r\n\t\t}\r\n\t}\r\n\tif (x.txtMRAll.value != \"\" && x.txtMRAll.value != 0){\r\n\t\tif (confirm(\"Enter Maroon = \"+x.txtMRAll.value+\" in all the Below Blank/Zero Maroon Records?\")){\t\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\r\n\t\t\t\tif (document.getElementById(\"txtMR\"+i).value == \"\" || document.getElementById(\"txtMR\"+i).value == 0){\r\n\t\t\t\t\tdocument.getElementById(\"txtMR\"+i).value = x.txtMRAll.value;\r\n\t\t\t\t}\r\n\t\t\t}\r\n\t\t}\r\n\t}\r\n\tif (x.txtPKAll.value != \"\" && x.txtPKAll.value != 0){\r\n\t\tif (confirm(\"Enter Pink = \"+x.txtPKAll.value+\" in all the Below Blank/Zero Pink Records?\")){\t\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\r\n\t\t\t\tif (document.getElementById(\"txtPK\"+i).value == \"\" || document.getElementById(\"txtPK\"+i).value == 0){\r\n\t\t\t\t\tdocument.getElementById(\"txtPK\"+i).value = x.txtPKAll.value;\r\n\t\t\t\t}\r\n\t\t\t}\r\n\t\t}\r\n\t}\r\n}\r\n\r\nfunction resetAllFlag(){\r\n\tx = document.frm1;\r\n\tif (confirm(\"Reset all Flags to ZERO\")){\r\n\t\tfor (i=0;i<x.txhCount.value;i++){\t\t\t\t\r\n\t\t\tdocument.getElementById(\"txtV\"+i).value = '0';\r\n\t\t\tdocument.getElementById(\"txtI\"+i).value = '0';\r\n\t\t\tdocument.getElementById(\"txtB\"+i).value = '0';\r\n\t\t\tdocument.getElementById(\"txtG\"+i).value = '0';\r\n\t\t\tdocument.getElementById(\"txtY\"+i).value = '0';\r\n\t\t\tdocument.getElementById(\"txtO\"+i).value = '0';\r\n\t\t\tdocument.getElementById(\"txtR\"+i).value = '0';\r\n\t\t\tdocument.getElementById(\"txtGR\"+i).value = '0';\r\n\t\t\tdocument.getElementById(\"txtBR\"+i).value = '0';\r\n\t\t\tdocument.getElementById(\"txtPR\"+i).value = '0';\r\n\t\t\tdocument.getElementById(\"txtMG\"+i).value = '0';\r\n\t\t\tdocument.getElementById(\"txtTL\"+i).value = '0';\r\n\t\t\tdocument.getElementById(\"txtAQ\"+i).value = '0';\r\n\t\t\tdocument.getElementById(\"txtSF\"+i).value = '0';\r\n\t\t\tdocument.getElementById(\"txtAM\"+i).value = '0';\r\n\t\t\tdocument.getElementById(\"txtGL\"+i).value = '0';\r\n\t\t\tdocument.getElementById(\"txtVM\"+i).value = '0';\r\n\t\t\tdocument.getElementById(\"txtSL\"+i).value = '0';\r\n\t\t\tdocument.getElementById(\"txtMR\"+i).value = '0';\r\n\t\t\tdocument.getElementById(\"txtPK\"+i).value = '0';\r\n\t\t}\r\n\t}\r\n}\r\n\r\nfunction resetFlag(a){\r\n\tx = document.frm1;\r\n\tif (a == 1){\r\n\t\tif (confirm(\"Reset all Violet Flags to ZERO\")){\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\r\n\t\t\t\tdocument.getElementById(\"txtV\"+i).value = '0';\t\t\t\t\t\r\n\t\t\t}\r\n\t\t}\r\n\t}else if (a == 2){\r\n\t\tif (confirm(\"Reset all Indigo Flags to ZERO\")){\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\t\t\t\t\r\n\t\t\t\tdocument.getElementById(\"txtI\"+i).value = '0';\t\t\t\t\t\r\n\t\t\t}\r\n\t\t}\r\n\t}else if (a == 3){\r\n\t\tif (confirm(\"Reset all Blue Flags to ZERO\")){\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\t\t\t\t\r\n\t\t\t\tdocument.getElementById(\"txtB\"+i).value = '0';\t\t\t\t\t\r\n\t\t\t}\r\n\t\t}\r\n\t}else if (a == 4){\r\n\t\tif (confirm(\"Reset all Green Flags to ZERO\")){\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\t\t\t\t\r\n\t\t\t\tdocument.getElementById(\"txtG\"+i).value = '0';\t\t\t\t\t\r\n\t\t\t}\r\n\t\t}\r\n\t}else if (a == 5){\r\n\t\tif (confirm(\"Reset all Yellow Flags to ZERO\")){\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\t\t\t\t\r\n\t\t\t\tdocument.getElementById(\"txtY\"+i).value = '0';\t\t\t\t\t\r\n\t\t\t}\r\n\t\t}\r\n\t}else if (a == 6){\r\n\t\tif (confirm(\"Reset all Orange Flags to ZERO\")){\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\t\t\t\t\r\n\t\t\t\tdocument.getElementById(\"txtO\"+i).value = '0';\t\t\t\t\t\r\n\t\t\t}\r\n\t\t}\r\n\t}else if (a == 7){\r\n\t\tif (confirm(\"Reset all Red Flags to ZERO\")){\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\t\t\t\t\r\n\t\t\t\tdocument.getElementById(\"txtR\"+i).value = '0';\t\t\t\t\t\r\n\t\t\t}\r\n\t\t}\r\n\t}else if (a == 8){\r\n\t\tif (confirm(\"Reset all Gray Flags to ZERO\")){\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\t\t\t\t\r\n\t\t\t\tdocument.getElementById(\"txtGR\"+i).value = '0';\t\t\t\t\t\r\n\t\t\t}\r\n\t\t}\r\n\t}else if (a == 9){\r\n\t\tif (confirm(\"Reset all Brown Flags to ZERO\")){\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\t\t\t\t\r\n\t\t\t\tdocument.getElementById(\"txtBR\"+i).value = '0';\t\t\t\t\t\r\n\t\t\t}\r\n\t\t}\r\n\t}else if (a == 10){\r\n\t\tif (confirm(\"Reset all Purple Flags to ZERO\")){\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\t\t\t\t\r\n\t\t\t\tdocument.getElementById(\"txtPR\"+i).value = '0';\r\n\t\t\t}\r\n\t\t}\r\n\t}else if (a == 11){\r\n\t\tif (confirm(\"Reset all Magenta Flags to ZERO\")){\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\t\t\t\t\r\n\t\t\t\tdocument.getElementById(\"txtMG\"+i).value = '0';\t\t\t\t\t\r\n\t\t\t}\r\n\t\t}\r\n\t}else if (a == 12){\r\n\t\tif (confirm(\"Reset all Teal Flags to ZERO\")){\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\t\t\t\t\r\n\t\t\t\tdocument.getElementById(\"txtTL\"+i).value = '0';\t\t\t\t\t\r\n\t\t\t}\r\n\t\t}\r\n\t}else if (a == 13){\r\n\t\tif (confirm(\"Reset all Aqua Flags to ZERO\")){\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\t\t\t\t\r\n\t\t\t\tdocument.getElementById(\"txtAQ\"+i).value = '0';\t\t\t\t\t\r\n\t\t\t}\r\n\t\t}\r\n\t}else if (a == 14){\r\n\t\tif (confirm(\"Reset all Safron Flags to ZERO\")){\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\t\t\t\t\r\n\t\t\t\tdocument.getElementById(\"txtSF\"+i).value = '0';\t\t\t\t\t\r\n\t\t\t}\r\n\t\t}\r\n\t}else if (a == 15){\r\n\t\tif (confirm(\"Reset all Amber Flags to ZERO\")){\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\t\t\t\t\r\n\t\t\t\tdocument.getElementById(\"txtAM\"+i).value = '0';\t\t\t\t\t\r\n\t\t\t}\r\n\t\t}\r\n\t}else if (a == 16){\r\n\t\tif (confirm(\"Reset all Gold Flags to ZERO\")){\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\t\t\t\t\r\n\t\t\t\tdocument.getElementById(\"txtGL\"+i).value = '0';\t\t\t\t\t\r\n\t\t\t}\r\n\t\t}\r\n\t}else if (a == 17){\r\n\t\tif (confirm(\"Reset all Vermilion Flags to ZERO\")){\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\t\t\t\t\r\n\t\t\t\tdocument.getElementById(\"txtVM\"+i).value = '0';\t\t\t\t\t\r\n\t\t\t}\r\n\t\t}\r\n\t}else if (a == 18){\r\n\t\tif (confirm(\"Reset all Silver Flags to ZERO\")){\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\t\t\t\t\r\n\t\t\t\tdocument.getElementById(\"txtSL\"+i).value = '0';\t\t\t\t\t\r\n\t\t\t}\r\n\t\t}\r\n\t}else if (a == 19){\r\n\t\tif (confirm(\"Reset all Maroon Flags to ZERO\")){\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\t\t\t\t\r\n\t\t\t\tdocument.getElementById(\"txtMR\"+i).value = '0';\t\t\t\t\t\r\n\t\t\t}\r\n\t\t}\r\n\t}else if (a == 20){\r\n\t\tif (confirm(\"Reset all Pink Flags to ZERO\")){\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\t\t\t\t\r\n\t\t\t\tdocument.getElementById(\"txtPK\"+i).value = '0';\r\n\t\t\t}\r\n\t\t}\r\n\t}\r\n}\r\n\r\nfunction saveChanges(){\r\n\tx = document.frm1;\r\n\tif (confirm(\"Save Changes?\")){\r\n\t\tx.act.value = \"saveChanges\";\r\n\t\tx.btSubmit.disabled = true;\r\n\t\tx.submit();\r\n\t}\r\n}\r\n</script>";
print "</div></div></div></div>";
include 'footer.php';
?>