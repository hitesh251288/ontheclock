<?php
ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "12";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=ReportIOULog.php&message=Session Expired or Security Policy Violated");
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
    $message = "PayMaster IOU Report";
}
$query = "SELECT LockDate, MACAddress, DBType, DBIP, DBName, DBUser, DBPass, EmployeeCodeLength FROM OtherSettingMaster";
$main_result = selectData($conn, $query);
$txtLockDate = $main_result[0];
$lstDBType = $main_result[2];
$txtDBIP = $main_result[3];
$txtDBName = $main_result[4];
$txtDBUser = $main_result[5];
$txtDBPass = $main_result[6];
$txtECodeLength = $main_result[7];
$txtMACAddress = $main_result[1];
$txtDBIP = $txtDBIP . ",1433\\SQLEXPRESS";
$co_code = 1;
$oconn = mssql_connection($txtDBIP, $txtDBName, $txtDBUser, $txtDBPass);
if ($oconn == "") {
    $message = "PayMaster IOU Report<br>Unable to establish Connection to PayMaster";
}
$lstYear = $_POST["lstYear"];
$lstMonth = $_POST["lstMonth"];
$lstDepartment = $_POST["lstDepartment"];
if ($lstMonth == "") {
    $lstMonth = substr(insertToday(), 4, 2);
}
$start = 0;
$PDCode = 0;
if (getRegister($txtMACAddress, 7) == "62") {
    $start = 2015;
    $PDCode = 5;
} else {
    $start = substr(insertToday(), 0, 4);
}
if ($lstYear == "") {
    $lstYear = $start;
}
if ($prints != "yes") {
    include 'header.php';
    ?>
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-12 d-flex no-block align-items-center">
                <h4 class="page-title">PayMaster IOU Report</h4>
                <div class="ms-auto text-end">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">
                                PayMaster IOU Report
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <?php
}
//print "<html><title>PayMaster IOU Report</title>";
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
        header("Content-Disposition: attachment; filename=ReportIOULog.xls");
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
            //                print "<table width='800' cellpadding='1' cellspacing='-1'>";
                    //        print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Select ONE or MORE options and click 'Search Record'</b></font></td></tr>";
                } else {
            //                print "<table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
            //                print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Selected Options</b></font></td></tr>";
                }
                print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='ReportIOULog.php'><input type='hidden' name='act' value='searchRecord'><tr>";
                ?>
            <div class="row">
                <div class="col-2">
                <?php
                print "<label class='form-label'>Select Year</label><select class='form-select select2 shadow-none' name='lstYear'><option selected value = '" . $lstYear . "'>" . $lstYear . "</option>";
                for ($i = $start; $i < $start + 20; $i++) {
                    print "<option value = '" . $i . "'>" . $i . "</option>";
                }
                print "</select>";
                ?>
                </div>
                <div class="col-2">
                    <?php
                    print "<label class='form-label'>Select Month</label><select class='select2 form-select shadow-none' name='lstMonth'><option selected value = '" . $lstMonth . "'>" . $lstMonth . "</option>";
                    for ($i = 1; $i < 13; $i++) {
                        print "<option value = '" . $i . "'>" . $i . "</option>";
                    }
                    print "</select>";
                    ?>
                </div>
                <div class="col-2">
                    <?php
                    $query = "SELECT DepartmentCode, DepartmentName from tblDepartment ORDER BY DepartmentName";
                    print "<label class='form-label'>Department</label>";
                    if ($prints == "yes") {
                        print "<input type='hidden' name='lstDepartment' value='" . $lstDepartment . "'>";
                    } else {
                        print "<select name='lstDepartment' class='select2 form-select shadow-none'>";
                    }
                    if ($prints != "yes") {
                        print "<option value=''>---</option>";
                    }
                    $result = mssql_query($query, $oconn);
                    while ($cur = mssql_fetch_row($result)) {
                        if ($cur[0] == $lstDepartment) {
                            if ($prints == "yes") {
                                print "<font size='2' face='Verdana'><b>&nbsp;&nbsp;" . $cur[1] . "</b></font>";
                            } else {
                                print "<option selected value='" . $cur[0] . "'>" . $cur[1] . "</option>";
                            }
                        } else {
                            if ($prints != "yes") {
                                print "<option value='" . $cur[0] . "'>" . $cur[1] . "</option>";
                            }
                        }
                    }
                    if ($prints == "yes") {
                        print "&nbsp;";
                    } else {
                        print "</select>";
                    }
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                <?php
                if ($prints != "yes") {
                    print "<center><br><input name='btSearch' class='bnt btn-primary' type='submit' value='Search Record'></center>";
                }
                ?>
                </div>
            </div>
            </form>
        </div>
    </div>
    <?php
}
print "</div></div></div></div>";

if ($act == "searchRecord" && stripos($userlevel, $current_module . "R") !== false) {
    $query = "SELECT tblEmployee.EmpNo, tblEmployee.EmpName, tblEmpPayDedTran.Amount, tblDepartment.DepartmentName FROM tblEmployee, tblEmpPayDedTran, tblDepartment WHERE tblEmployee.EmpCode =  tblEmpPayDedTran.EmpCode AND tblDepartment.DepartmentCode = tblEmployee.DepartmentCode AND tblEmpPayDedTran.Amount > 0 AND tblEmpPayDedTran.PaymentDeductionCode = " . $PDCode . " ";
    if ($lstYear != "") {
        $query = $query . " AND tblEmpPayDedTran.YearID = " . ($lstYear - $start + 1);
    }
    if ($lstMonth != "") {
        $query = $query . " AND tblEmpPayDedTran.MonthID = " . $lstMonth;
    }
    if ($lstDepartment != "") {
        $query = $query . " AND tblDepartment.DepartmentCode = " . $lstDepartment;
    }
    $query = $query . " ORDER BY tblDepartment.DepartmentName, tblEmpPayDedTran.EmpCode";
    print '<div class="row"><div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12"><div class="card"><div class="card-body table-responsive">';
    if ($prints != "yes") {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800' class='table table-striped table-bordered dataTable'>";
    } else {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%' class='table table-striped table-bordered dataTable'>";
    }
    print "<tr><td><font face='Verdana' size='2'><b>Department</b></font></td> <td><font face='Verdana' size='2'><b>Employee No</b></font></td> <td><font face='Verdana' size='2'><b>Employee Name</b></font></td> <td><font face='Verdana' size='2'><b>IOU Amount</b></font></td> </tr>";
    $count = 0;
    $bgcolor = "#F0F0F0";
    $date = "";
    $total = 0;
    $result = mssql_query($query, $oconn);
    while ($cur = mssql_fetch_row($result)) {
        if ($date != $cur[0]) {
            if ($bgcolor == "#F0F0F0") {
                $bgcolor = "#FFFFFF";
            } else {
                $bgcolor = "#F0F0F0";
            }
            $date = $cur[0];
        }
        addComma($cur[2], 2);
        print "<tr><td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1'>" . $cur[3] . "</font></td> <td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1'>" . $cur[0] . "</font></td> <td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1'>" . $cur[1] . "</font></td> <td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1'>" . addComma($cur[2], 2) . "</font></td> </tr>";
        $count++;
        $total = $total + $cur[2];
    }
    addComma($total, 2);
    print "<tr><td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1'>&nbsp;</font></td> <td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1'>&nbsp;</font></td> <td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1'>&nbsp;</font></td> <td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1'><b>" . addComma($total, 2) . "</b></font></td> </tr>";
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