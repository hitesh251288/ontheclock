<?php


ob_start("ob_gzhandler");
error_reporting(E_ALL);
set_time_limit(0);
ini_set("memory_limit", 0 - 1);
include "Functions.php";
$current_module = "19";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$username = $_SESSION[$session_variable . "username"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$userstatus = $_SESSION[$session_variable . "userstatus"];
$txtMACAddress = $_SESSION[$session_variable . "MACAddress"];
if (!(checkSession($userlevel, $current_module) && $_SESSION[$session_variable . "VirdiLevel"] == "Classic")) {
    header("Location: " . $config["REDIRECT"] . "?url=MigrateMaster.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$iconn = openIConnection();
$jconn = openIConnection();
if ($prints != "yes") {
    include 'header.php';
?>
<div class="page-breadcrumb">
                    <div class="row">
                        <div class="col-12 d-flex no-block align-items-center">
                            <h4 class="page-title">Migration Settings</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Migration Settings
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

<?php
}
//print "<html><title>Migration Settings</title><body><center>";
//displayHeader($prints, false, false);
print "<center>";
print'<div class="container-fluid">
                <div class="row">
                        <div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12">
                            <div class="card">
                                    <div class="card-body table-responsive">';
//displayLinks($current_module, $userlevel);
//print "</center>";
$act = $_GET["act"];
if ($act == "") {
    $act = $_POST["act"];
}
$message = $_GET["message"];
if ($message == "") {
    $message = "Employee Migration Settings - RUN DayMaster to ADD New Records";
}
$txhCounter = $_POST["txhCounter"];
if ($act == "editRecord") {
    for ($i = 0; $i < $txhCounter; $i++) {
        if ($_POST["txtFrom" . $i] != "" && $_POST["txtFrom" . $i] != "0") {
            $_POST["txtFrom" . $i] = insertDate($_POST["txtFrom" . $i]);
        } else {
            $_POST["txtFrom" . $i] = "0";
        }
        if ($_POST["txtTo" . $i] != "" && $_POST["txtFrom" . $i] != "0") {
            $_POST["txtTo" . $i] = insertDate($_POST["txtTo" . $i]);
        } else {
            $_POST["txtTo" . $i] = "0";
        }
        $query = "UPDATE MigrateMaster SET DateFrom = '" . $_POST["txtFrom" . $i] . "', DateTo = '" . $_POST["txtTo" . $i] . "', MonthNo = '" . $_POST["txtMonthNo" . $i] . "' WHERE Col = '" . $_POST["txhCol" . $i] . "'  AND Val = '" . $_POST["txhVal" . $i] . "' ";
        updateIData($iconn, $query, true);
    }
    $text = "Updated Migrate Master Dates";
    $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
    updateIData($jconn, $query, true);
    $message = "Record(s) Updated";
    header("Location: MigrateMaster.php?message=" . $message);
} else {
    if ($act == "deleteRecord") {
        $query = "SELECT id FROM tuser WHERE " . $_POST["txhCol"] . " = '" . $_POST["txhVal"] . "'";
        while ($cur = mysqli_fetch_row($result)) {
            $query__ = "DELETE FROM tblEmpWorkHours_VIRDI WHERE FROMDATE = '" . displayParadoxDate(insertDate($_POST["txhFrom"])) . " 00:00:00' AND TODATE = '" . displayParadoxDate(insertDate($_POST["txhTo"])) . " 00:00:00' AND EMPPAYROLLNO = '" . addZero($cur[0], $txtECodeLength) . "'";
            if (!mssql_query($query__, $oconn)) {
                echo "\n\r" . $query__;
                exit;
            }
        }
        $message = "Record(s) Deleted";
        header("Location: MigrateMaster.php?message=" . $message);
    }
}
if ($prints != "yes") {
    print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b></font></p>";
    print "<table width='800' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' class='table table-striped table-bordered dataTable'>";
    print "<form name='frm1' method='post' action='MigrateMaster.php?act=editRecord'>";
    print "<tr><td><font face='Verdana' size='2'><b>Column</b></font></td><td><font face='Verdana' size='2'><b>Value</b></font></td><td><font face='Verdana' size='2'><b>From</b></font></td><td><font face='Verdana' size='2'><b>To</b></font></td><td><font face='Verdana' size='2'><b>Month No</b></font></td><td><font face='Verdana' size='2'><b>&nbsp;</b></font></td></tr>";
    $query = "SELECT CostCentre FROM PayrollMap";
    $pay_result = selectData($conn, $query);
    if ($pay_result[0] == "") {
        $query = "SELECT Col, Val, DateFrom, DateTo, MonthNo FROM MigrateMaster WHERE DateFrom <> 0 AND DateFrom <> '' ORDER BY Col, Val ";
    } else {
        if ($pay_result[0] == "Dept [TA]" || $pay_result[0] == "dept") {
            $query = "SELECT Col, Val, DateFrom, DateTo, MonthNo FROM MigrateMaster WHERE Col = 'dept' ORDER BY DateFrom DESC ";
        } else {
            if ($pay_result[0] == "Div/Desg [TA]" || $pay_result[0] == "Company") {
                $query = "SELECT Col, Val, DateFrom, DateTo, MonthNo FROM MigrateMaster WHERE Col = 'company' ORDER BY DateFrom DESC ";
            } else {
                if ($pay_result[0] == "Social No [TA]" || $pay_result[0] == "idno") {
                    $query = "SELECT Col, Val, DateFrom, DateTo, MonthNo FROM MigrateMaster WHERE Col = 'idno' ORDER BY DateFrom DESC ";
                } else {
                    if ($pay_result[0] == "Remark [TA]" || $pay_result[0] == "Remark") {
                        $query = "SELECT Col, Val, DateFrom, DateTo, MonthNo FROM MigrateMaster WHERE Col = 'remark' ORDER BY DateFrom DESC ";
                    } else {
                        if ($pay_result[0] == "Phone [TA]" || $pay_result[0] == "Phone") {
                            $query = "SELECT Col, Val, DateFrom, DateTo, MonthNo FROM MigrateMaster WHERE Col = 'phone' ORDER BY DateFrom DESC ";
                        }
                    }
                }
            }
        }
    }
    $result = mysqli_query($iconn, $query);
    for ($i = 0; $cur = mysqli_fetch_row($result); $i++) {
        if (strlen($cur[2]) == 8) {
            $cur[2] = displayDate($cur[2]);
        }
        if (strlen($cur[3]) == 8) {
            $cur[3] = displayDate($cur[3]);
        }
        print "<tr><td><font face='Verdana' size='1'>" . $cur[0] . "</font><input type='hidden' name='txhCol" . $i . "' value='" . $cur[0] . "'></td><td><font face='Verdana' size='1'>" . $cur[1] . "</font><input type='hidden' name='txhVal" . $i . "' value='" . $cur[1] . "'></td><td><input name='txtFrom" . $i . "' id='txtFrom" . $i . "' value='" . $cur[2] . "' size='12' class='form-control'></td><td><input name='txtTo" . $i . "' id='txtTo" . $i . "' value='" . $cur[3] . "' size='12' class='form-control'></td><td><input name='txtMonthNo" . $i . "' id='txtMonthNo" . $i . "' value='" . $cur[4] . "' size='12' class='form-control'></td><td><a href='MigrateMaster.php?act=deleteRecord&txhCol=" . $cur[0] . "&txhVal=" . $cur[1] . "&txtFrom=" . $cur[2] . "&txtTo=" . $cur[3] . "'><font face='Verdana' size='2'><b>Delete</b></font></a></td></tr>";
    }
    if ($pay_result[0] == "") {
        $query = "SELECT Col, Val, DateFrom, DateTo, MonthNo FROM MigrateMaster WHERE DateFrom = '' OR DateFrom = '0' OR DateFrom IS NULL ORDER BY Col, Val ";
        for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $i++) {
            if (strlen($cur[2]) == 8) {
                $cur[2] = displayDate($cur[2]);
            }
            if (strlen($cur[3]) == 8) {
                $cur[3] = displayDate($cur[3]);
            }
            print "<tr><td><font face='Verdana' size='1'>" . $cur[0] . "</font><input type='hidden' name='txhCol" . $i . "' value='" . $cur[0] . "'></td><td><font face='Verdana' size='1'>" . $cur[1] . "</font><input type='hidden' name='txhVal" . $i . "' value='" . $cur[1] . "'></td><td><input name='txtFrom" . $i . "' id='txtFrom" . $i . "' value='" . $cur[2] . "' size='12' class='form-control'></td><td><input name='txtTo" . $i . "' id='txtTo" . $i . "' value='" . $cur[3] . "' size='12' class='form-control'></td><td><input name='txtMonthNo" . $i . "' id='txtMonthNo" . $i . "' value='" . $cur[4] . "' size='12' class='form-control'></td><td><a href='MigrateMaster.php?act=deleteRecord&txhCol=" . $cur[0] . "&txhVal=" . $cur[1] . "&txtFrom=" . $cur[2] . "&txtTo=" . $cur[3] . "'><font face='Verdana' size='2'><b>Delete</b></font></a></td></tr>";
        }
    }
    print "</table>";
    if (stripos($userlevel, $current_module . "E") !== false) {
        print "<br><p align='center'><input name='btSave' class='btn btn-primary' type='button' value='Save Changes' onClick='javascript:checkSubmit(" . $i . ")'></p>";
    }
    print "<input type='hidden' value='" . $i . "' name='txhCounter'></form>";
}
print "</div></div></div></div></div>";
echo "<script>\r\nfunction checkSubmit(a){\r\n\tx = document.frm1;\t\r\n\tvar date_flag = true;\r\n\tfor (i=0;i<a;i++){\r\n\t\tif (document.getElementById(\"txtFrom\"+i).value != \"\" && document.getElementById(\"txtFrom\"+i).value != \"0\" && check_valid_date(document.getElementById(\"txtFrom\"+i).value) == false){\r\n\t\t\tdate_flag = false;\r\n\t\t\talert('Invalid From Date. Date Format should be DD/MM/YYYY');\r\n\t\t\tdocument.getElementById(\"txtFrom\"+i).focus();\r\n\t\t}else if (document.getElementById(\"txtTo\"+i).value != \"\" && document.getElementById(\"txtTo\"+i).value != \"0\" && check_valid_date(document.getElementById(\"txtTo\"+i).value) == false){\r\n\t\t\tdate_flag = false;\r\n\t\t\talert('Invalid To Date. Date Format should be DD/MM/YYYY');\r\n\t\t\tdocument.getElementById(\"txtFrom\"+i).focus();\r\n\t\t}\r\n\t}\r\n\tif (date_flag){\r\n\t\tif (confirm('Save Changes')){\r\n\t\t\tx.btSave.disabled = true;\r\n\t\t\tx.submit();\r\n\t\t}\r\n\t}\r\n}\r\n</script>\r\n</center>";
include 'footer.php';
?>