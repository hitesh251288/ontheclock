<?php 
ob_start("ob_gzhandler");
set_time_limit(0);
ini_set('display_errors', '1');
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set("memory_limit", "-1");
ini_set("post_max_size", "0");
ini_set("max_execution_time", "0");
ini_set("max_input_time", "-1");
ini_set("max_input_vars", "100000");
include "Functions.php";
//die("Loaded");
$current_module = "34";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
$flagLimitType = $_SESSION[$session_variable . "FlagLimitType"];
$lstUserType = $_SESSION[$session_variable . "lstUserType"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=FlagApplication.php&message=Session Expired or Security Policy Violated");
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
    $message = "Flag Application (Applicable ONLY for Shifts with Routine Type = Daily) <br>EDIT Rights on this Module grants Permission for AP2 <br>DELETE Rights on this Module grants Permission for AP3";
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
$txtTo = $_POST["txtTo"];
if ($txtTo == "") {
    if (substr(insertToday(), 6, 2) < 28) {
        $txtTo = "28/" . substr(displayToday(), 3, 7);
    } else {
        $txtTo = displayDate(getNextDay(insertToday(), 1));
    }
}
$txtRemark = $_POST["txtRemark"];
$txtPhone = $_POST["txtPhone"];
$txtSNo = $_POST["txtSNo"];
$txhCount = $_POST["txhCount"];
$lstExcludeOT = $_POST["lstExcludeOT"];
if ($lstExcludeOT == "") {
    $lstExcludeOT = "OT1/OT2";
}
$lstDeptTerminal = $_POST["lstDeptTerminal"];
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
$lstSetShift = "";
$lstEmployeeStatus = "";
if (isset($_POST["lstEmployeeStatus"])) {
    $lstEmployeeStatus = $_POST["lstEmployeeStatus"];
} else {
    $lstEmployeeStatus = "ACT";
}
$lstType = $_POST["lstType"];
if ($act == "saveRecord") { 
    for ($i = 0; $i < $txhCount; $i++) {
        if ($_POST["txhExist" . $i] == 0) {
            $text = "Updated Flag Application for ID: " . $_POST["txh" . $i];
            $query = "";
            if ($_POST["chkA1" . $i] != "" && is_numeric($_POST["txtDateFrom" . $i]) == false && is_numeric($_POST["txtDateTo" . $i]) == false) {
                $query = "INSERT IGNORE INTO FlagApplication (DateFrom, DateTo, e_id, Flag, A1, A2, A3, Remark) VALUES (" . insertDate($_POST["txtDateFrom" . $i]) . ", " . insertDate($_POST["txtDateTo" . $i]) . ", " . $_POST["txh" . $i] . ", '" . $_POST["lstFlag" . $i] . "', ";
                if ($_POST["chkA1" . $i] != "") {
                    $query .= "1, ";
                    $text .= " - Flag: " . $_POST["lstFlag" . $i];
                } else {
                    $query .= "0, ";
                }
                if ($_POST["chkA2" . $i] != "") {
                    $query .= "1, ";
                } else {
                    $query .= "0, ";
                }
                if ($_POST["chkA3" . $i] != "") {
                    $query .= "1, ";
                } else {
                    $query .= "0, ";
                }
                $query .= " '" . $_POST["txtRemark" . $i] . "')";
                $query .= "ON DUPLICATE KEY UPDATE 
                        Flag = VALUES(Flag),
                        A1 = VALUES(A1),
                        A2 = VALUES(A2),
                        A3 = VALUES(A3),
                        Remark = VALUES(Remark)";
                mysqli_autocommit($iconn, true);
                if (updateIData($iconn, $query, true) == false) {
                    $query = "UPDATE FlagApplication SET DateFrom = " . insertDate($_POST["txtDateFrom" . $i]) . ", DateTo = " . insertDate($_POST["txtDateTo" . $i]) . ", Remark = '" . $_POST["txtRemark" . $i] . "', Flag = '" . $_POST["lstFlag" . $i] . "', ";
                    $text .= " - Updated Flag Application for ID: " . $_POST["txh" . $i] . ", Flag = " . $_POST["lstFlag" . $i] . ", From = " . $_POST["txtDateFrom" . $i] . ", To = " . $_POST["txtDateTo" . $i];
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
                    $query = $query . " WHERE ID = " . $_POST["txhID" . $i];
                    mysqli_autocommit($iconn, true);
                    updateIData($iconn, $query, true);
                }
                $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                mysqli_autocommit($iconn, true);
                updateIData($iconn, $query, true);
                if ($_POST["chkA3" . $i] != "") {
                    if ($lstSetShift == "") {
                        $query = "SELECT id from tgroup WHERE NightFlag = 0 AND name = 'OFF' ";
                        $result = selectData($conn, $query);
                        $lstSetShift = $result[0];
                        if ($lstSetShift == "") {
                            $query = "SELECT id from tgroup WHERE NightFlag = 0 AND id > 1 ";
                            $result = selectData($conn, $query);
                            $lstSetShift = $result[0];
                        }
                    }
                    $ii = insertDate($_POST["txtDateFrom" . $i]);
                    while ($ii <= insertDate($_POST["txtDateTo" . $i])) {
                        $ot_flag = false;
                        if ($lstExcludeOT != "NONE") {
                            if ($lstExcludeOT == "OT1") {
                                $query = "SELECT OT FROM OTDay WHERE OTDay.Day = '" . getDay(displayDate($ii)) . "' AND OTDay.Day = 'Saturday' ";
                            } else {
                                if ($lstExcludeOT == "OT2") {
                                    $query = "SELECT OT FROM OTDay WHERE OTDay.Day = '" . getDay(displayDate($ii)) . "' AND OTDay.Day = 'Sunday' ";
                                } else {
                                    if ($lstExcludeOT == "OT1/OT2") {
                                        $query = "SELECT OT FROM OTDay WHERE OTDay.Day = '" . getDay(displayDate($ii)) . "'";
                                    }
                                }
                            }
                            $result = selectData($conn, $query);
                            if ($result[0] == 0) {
                                $query = "SELECT OTDate FROM OTDate WHERE OTDate = " . $ii;
                                $result = selectData($conn, $query);
                                if (is_numeric($result[0])) {
                                    $ot_flag = true;
                                }
                            } else {
                                $ot_flag = true;
                            }
                        }
                        if ($ot_flag == false) {
                            $insert_flag = false;
                            if ($_POST["lstFlag" . $i] == "Proxy") {
                                $insert_flag = true;
                            } else {
                                $query = "SELECT " . $_POST["lstFlag" . $i] . " FROM EmployeeFlag WHERE EmployeeID = " . $_POST["txh" . $i];
                                $result = selectData($conn, $query);
                                $max_flag_limit = $result[0];
                                if ($max_flag_limit == "") {
                                    $query = "INSERT INTO EmployeeFlag (EmployeeID) VALUES (" . $_POST["txh" . $i] . ")";
                                    updateData($conn, $query, true);
                                    $max_flag_limit = 365;
                                }
                                if ($flagLimitType == "Jan 01") {
                                    $query = "SELECT COUNT(FlagDayRotationID) FROM FlagDayRotation WHERE e_id = " . $_POST["txh" . $i] . " AND Flag = '" . $_POST["lstFlag" . $i] . "' AND e_date >= " . substr(insertToday(), 0, 4) . "0101 AND e_date <= " . substr(insertToday(), 0, 4) . "1231 AND RecStat = 0";
                                } else {
                                    $query = "SELECT COUNT(FlagDayRotationID) FROM FlagDayRotation, tuser WHERE tuser.id = FlagDayRotation.e_id AND FlagDayRotation.e_id = " . $_POST["txh" . $i] . " AND FlagDayRotation.Flag = '" . $_POST["lstFlag" . $i] . "' AND FlagDayRotation.e_date >= CONCAT(" . substr(insertToday(), 0, 4) . ", SUBSTR(tuser.datelimit, 6, 4)) AND FlagDayRotation.e_date < CONCAT(" . (substr(insertToday(), 0, 4) * 1 + 1) . ", SUBSTR(tuser.datelimit, 6, 4)) AND FlagDayRotation.RecStat = 0";
                                }
                                $result = selectData($conn, $query);
                                $pre_flag_count = $result[0];
                                if ($pre_flag_count == "") {
                                    $pre_flag_count = 0;
                                }
                                if ($flagLimitType == "Jan 01") {
                                    $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster WHERE EmployeeID = " . $_POST["txh" . $i] . " AND Flag = '" . $_POST["lstFlag" . $i] . "' AND ADate >= " . substr(insertToday(), 0, 4) . "0101 AND ADate <= " . substr(insertToday(), 0, 4) . "1231 ";
                                } else {
                                    $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND AttendanceMaster.EmployeeID = " . $_POST["txh" . $i] . " AND AttendanceMaster.Flag = '" . $_POST["lstFlag" . $i] . "' AND AttendanceMaster.ADate >= CONCAT(" . substr(insertToday(), 0, 4) . ", SUBSTR(tuser.datelimit, 6, 4)) AND AttendanceMaster.ADate < CONCAT(" . (substr(insertToday(), 0, 4) * 1 + 1) . ", SUBSTR(tuser.datelimit, 6, 4)) ";
                                }
                                $result = selectData($conn, $query);
                                $post_flag_count = $result[0];
                                if ($post_flag_count == "") {
                                    $post_flag_count = 0;
                                }
                                if ($pre_flag_count + $post_flag_count < $max_flag_limit) {
                                    $insert_flag = true;
                                }
                            }
                            if ($insert_flag) {
                                $query = "SELECT AttendanceID FROM AttendanceMaster WHERE EmployeeID = " . $_POST["txh" . $i] . " AND ADate = " . $ii;
                                $result = selectData($conn, $query);
                                if (is_numeric($result[0]) == false) {
                                    $query = "";
                                    $query = "INSERT INTO FlagDayRotation (e_id, e_date, g_id, Flag, Remark, group_id) VALUES (" . $_POST["txh" . $i] . ", " . $ii . ", " . $lstDeptTerminal . ", '" . $_POST["lstFlag" . $i] . "', '" . $_POST["txtRemark" . $i] . "', '" . $lstSetShift . "')";
                                    mysqli_autocommit($iconn, true);
                                    if (updateIData($iconn, $query, true) == false) {
                                        $query = "UPDATE FlagDayRotation SET Flag = '" . $_POST["lstFlag" . $i] . "', Remark = '" . $_POST["txtRemark" . $i] . "', group_id = '" . $lstSetShift . "' WHERE e_id = '" . $_POST["txh" . $i] . "' AND e_date = '" . $ii . "' AND RecStat = 0 ";
                                        mysqli_autocommit($iconn, true);
                                        if (updateIData($iconn, $query, true) == false) {
                                            $insert_flag = false;
                                        }
                                    }
                                    if ($insert_flag) {
                                        $text = "Pre Flagged ID: " . $_POST["txh" . $i] . " for Date: " . displayDate($ii) . " with Flag: " . $_POST["lstFlag" . $i] . ", Shift ID: " . $lstSetShift;
                                        $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                                        mysqli_autocommit($iconn, true);
                                        updateIData($iconn, $query, true);
                                    }
                                }
                            }
                        }
                        $ii = getNextDay($ii, 1);
                    }
                }
            }
        }
    }
    $act = "searchRecord";
} else {
    if ($act == "deleteRecord") {
        $id = (int)($_REQUEST["txtID"] / 1024 / 1024);
        $query = "DELETE FROM FlagApplication WHERE ID = ".$id;
        mysqli_autocommit($conn, true);
        updateData($conn, $query, true);
        $act = "searchRecord";
    }
}
if ($prints != "yes") {
    include 'header.php';
?>
<style>
.form-control, .form-select{
    padding: 0.375rem 0.75rem;
    border-color: #aaaaaa;
    border-radius: 4px;
    box-sizing: border-box;
    width: auto !important;
}    
/* Ensure the main selection box has enough height and padding */
.select2-container--default .select2-selection--single {
    height: 40px !important;
    padding: 6px 12px !important;
    font-size: 14px;
    display: flex;
    align-items: center;
    border: 1px solid #aaa;
    border-radius: 4px;
    box-sizing: border-box;
}

/* Ensure selected text displays properly */
.select2-selection__rendered {
    line-height: 1.5 !important;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
    width: 100%;
    padding-right: 16px; /* for clear button space */
}

/* Fix arrow button alignment */
.select2-selection__arrow {
    height: 100% !important;
    top: 0 !important;
    right: 6px;
}
</style>
<div class="page-breadcrumb">
                    <div class="row">
                        <div class="col-12 d-flex no-block align-items-center">
                            <h4 class="page-title">Flag Application</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Flag Application
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

<?php
}
print "<form name='frm1' method='post' onSubmit='return checkFlagApplicationSearch()' action='FlagApplication.php'><input type='hidden' name='act' value='searchRecord'> <input type='hidden' name='txtTo' value='31/12/2020'>";
//print "<html><title>Flag Application</title>";
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
        header("Content-Disposition: attachment; filename=FlagApplication.xls");
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
                displayTextbox("txtFrom", "Date From <font size='1'>(DD/MM/YYYY)</font>: ", $txtFrom, $prints, 12, "", "");
                ?>
            </div>
            <div class="col-2">
                <?php 
                displayTextbox("txtTo", "Date To <font size='1'>(DD/MM/YYYY)</font>: ", $txtTo, $prints, 12, "", "");
                ?>
            </div>
        <?php 
        if ($prints != "yes") {
        print "<div class='col-2'>";
        print "<label>Record Type:</label><select name='lstType' class='form-control'><option selected value='" . $lstType . "'>" . $lstType . "</option> <option value='Applied'>Applied</option> <option value='AP1 = Yes'>AP1 = Yes</option> <option value='AP2 = Yes'>AP2 = Yes</option> <option value='---'>---</option> </select>";
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
        print "<input type='hidden' name='txhFlagLimitType' value='" . $flagLimitType . "'>";
        print "<center><input name='btSearch' type='submit' value='Search Record' class='btn btn-primary'></a></center>";
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
    if ($lstType == "Applied") {
        $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tuser.idno, tuser.remark FROM tuser, tgroup, FlagApplication WHERE tuser.group_id = tgroup.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " AND tuser.id = FlagApplication.e_id AND FlagApplication.Flag > 0 AND FlagApplication.FlagDate = " . insertDate($txtFrom) . " ";
    } else {
        if ($lstType == "AP1 = Yes") {
            $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tuser.idno, tuser.remark FROM tuser, tgroup, FlagApplication WHERE tuser.group_id = tgroup.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " AND tuser.id = FlagApplication.e_id AND FlagApplication.A2 > 0 AND FlagApplication.FlagDate = " . insertDate($txtFrom) . " ";
        } else {
            if ($lstType == "AP2 = Yes") {
                $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tuser.idno, tuser.remark FROM tuser, tgroup, FlagApplication WHERE tuser.group_id = tgroup.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " AND tuser.id = FlagApplication.e_id AND FlagApplication.A3 > 0 AND FlagApplication.FlagDate = " . insertDate($txtFrom) . " ";
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
    print '<div class="row"><div class="col-md-12"><div class="card"><div class="card-body table-responsive">';
    if ($prints != "yes") {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800' class='table table-striped table-bordered dataTable' >";
    } else {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%' class='table table-striped table-bordered dataTable'>";
    }
    print "<input type='hidden' name='txhFrom' value='" . $txtFrom . "'>";
    print "<thead>";
    print "<tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>From</font></td> <td><font face='Verdana' size='2'>To</font></td> <td><a title='Grant ADD Permission for this Module to the User to Activate this Column'><font face='Verdana' size='2'>Flag</font></a></td> <td><a title='Grant ADD Permission for this Module to the User to Activate this Column'><font face='Verdana' size='2'>Notes</font></a></td> <td><a title='Grant ADD Permission for this Module to the User to Activate this Column'><font face='Verdana' size='2'>AP1</font></a></td> <td><a title='Grant EDIT Permission for this Module to the User to Activate this Column'><font face='Verdana' size='2'>AP2</font></a></td> <td><a title='Grant DELETE Permission for this Module to the User to Activate this Column'><font face='Verdana' size='2'>AP3</font></a></td> <td><a title='Grant DELETE Permission for this Module to the User to Activate this Column'><font face='Verdana' size='2'>&nbsp;</font></a></td> </tr>";
    print"</thead>";
    $count = 0;
    $data0 = "";
    $data1 = "";
    $data2 = "";
    $data3 = "";
    $data5 = "";
    $data6 = "";
    $result = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
        if ($cur[3] == "") {
            $cur[3] = "&nbsp;";
        }
        if ($cur[5] == "") {
            $cur[5] = "&nbsp;";
        }
        if ($cur[6] == "") {
            $cur[6] = "&nbsp;";
        }
        if ($data0 != $cur[0] && $data0 != "") {
            addZero($data0, $_SESSION[$session_variable . "EmployeeCodeLength"]);
            print "<tr><td><a title='ID'><input type='hidden' name='txh" . $count . "' value='" . $data0 . "'> <font face='Verdana' color='#000000' size='1'>" . addZero($data0, $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $data1 . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $data5 . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $data2 . "</font></a></td> <td><a title='Div/Desg'><font face='Verdana' size='1'>" . $data3 . "</font></a></td> <td><a title='Rmk'><font face='Verdana' size='1'>" . $data6 . "</font></a></td> ";
            print "<td><font face='Verdana' size='1'><input size='12' name='txtDateFrom" . $count . "' value='" . $txtFrom . "' class='form-control'></td>";
            print "<td><font face='Verdana' size='1'><input size='12' name='txtDateTo" . $count . "' value='" . $txtTo . "' class='form-control'></td>";
            print "<td>";
            displayColourFlag($conn, "", "lstFlag" . $count, false, false);
            print "</td>";
            print "<td><font face='Verdana' size='1'><input size='12' name='txtRemark" . $count . "' class='form-control'></td>";
            print "<td><input type='checkbox' name='chkA1" . $count . "' onClick='checkFlag(this, document.frm1.lstFlag" . $count . ")'></td>";
            print "<td><input type='checkbox' name='chkA2" . $count . "'></td>";
            print "<td><input type='checkbox' name='chkA3" . $count . "'></td>";
            print "<td>&nbsp;</td>";
            print "</tr>";
            print "<input type='hidden' name='txhExist" . $count . "' value='0'>";
            $count++;
        }
        $query = "SELECT Flag, A1, A2, A3, Remark, ID, DateFrom, DateTo FROM FlagApplication WHERE e_id = " . $cur[0];
        for ($result1 = mysqli_query($jconn, $query); $cur1 = mysqli_fetch_row($result1); $count++) {
            if ($cur1[5] != "") {
                addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
                print "<tr><td><a title='ID'><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'> <font face='Verdana' color='#000000' size='1'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $cur[1] . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $cur[5] . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $cur[2] . "</font></a></td> <td><a title='Div/Desg'><font face='Verdana' size='1'>" . $cur[3] . "</font></a></td> <td><a title='Rmk'><font face='Verdana' size='1'>" . $cur[6] . "</font></a></td> ";
                if ($prints != "yes" && (strpos($userlevel, $current_module . "A") !== false && $cur1[1] == 0 || strpos($userlevel, $current_module . "E") !== false && $cur1[2] == 0 || strpos($userlevel, $current_module . "D") !== false && $cur1[3] == 0)) {
                    displayDate($cur1[6]); 
                    print "<td><font face='Verdana' size='1'><input name='txtDateFrom" . $count . "' value='" . displayDate($cur1[6]) . "' size='12' onBlur='checkFromDate(this)' class='form-control'></td>";
                    displayDate($cur1[7]);
                    print "<td><font face='Verdana' size='1'><input name='txtDateTo" . $count . "' value='" . displayDate($cur1[7]) . "' size='12' onBlur='checkToDate(this)' class='form-control'></td>";
                    print "<td>";
                    displayColourFlag($conn, $cur1[0], "lstFlag" . $count, false, false);
                    print "</td>";
                    print "<td><font face='Verdana' size='1'><input name='txtRemark" . $count . "' value='" . $cur1[4] . "' size='12' class='form-control'></td>";
                    if ($cur1[1] == 1) {
                        print "<td><input type='checkbox' name='chkA1" . $count . "' checked></td>";
                    } else {
                        print "<td><input type='checkbox' name='chkA1" . $count . "' onClick='checkFlag(this, document.frm1.lstFlag" . $count . ")'></td>";
                    }
                } else { 
                    displayDate($cur1[6]);
                    print "<td><font face='Verdana' size='1'>" . displayDate($cur1[6]) . "</font><input type='hidden' name='txtDateFrom" . $count . "' value='" . $cur1[6] . "'></td>";
                    displayDate($cur1[7]);
                    print "<td><font face='Verdana' size='1'>" . displayDate($cur1[7]) . "</font><input type='hidden' name='txtDateTo" . $count . "' value='" . $cur1[7] . "'></td>";
                    if(isset($cur1[0])){
                        $color = $cur1[0];
                    }
                    print "<td bgcolor='$color'><font face='Verdana' size='1'>" . $cur1[0] . "</font><input type='hidden' name='lstFlag" . $count . "' value='" . $cur1[0] . "'></td>";
                    if ($cur1[4] == "") {
                        $cur1[4] = "&nbsp;";
                    }
                    print "<td><font face='Verdana' size='1'>" . $cur1[4] . "</font><input type='hidden' name='txtRemark" . $count . "' value='" . $cur1[4] . "'></td>";
                    if ($cur1[1] == 1) {
                        print "<td><input type='hidden' name='chkA1" . $count . "' value='1'><font face='Verdana' size='1'>Yes</font></td>";
                    } else {
                        print "<td><input type='hidden' name='chkA1" . $count . "' value=''><font face='Verdana' size='1'>No</font></td>";
                    }
                }
                if ($prints != "yes" && $cur1[3] == 0 && (strpos($userlevel, $current_module . "E") !== false && $cur1[3] == 0 || strpos($userlevel, $current_module . "D") !== false)) {
                    if ($cur1[2] == 1) {
                        print "<td><input type='checkbox' name='chkA2" . $count . "' checked></td>";
                    } else {
                        print "<td><input type='checkbox' name='chkA2" . $count . "'></td>";
                    }
                } else {
                    if ($cur1[2] == 1) {
                        print "<td><input type='hidden' name='chkA2" . $count . "' value='1'><font face='Verdana' size='1'>Yes</font></td>";
                    } else {
                        print "<td><input type='hidden' name='chkA2" . $count . "' value=''><font face='Verdana' size='1'>No</font></td>";
                    }
                }
                if ($prints != "yes" && $cur1[3] == 0 && strpos($userlevel, $current_module . "D") !== false) {
                    if ($cur1[3] == 1) {
                        print "<td><input type='checkbox' name='chkA3" . $count . "' checked></td>";
                        print "<td>&nbsp;</td>";
                    } else {
                        print "<td><input type='checkbox' name='chkA3" . $count . "'></td>";
                        print "<td><input type='button' value='X' onClick=deleteRecord(" . $cur1[5] . ") class='btn btn-primary'></td>";
                    }
                } else {
                    if ($cur1[3] == 1) {
                        print "<td><input type='hidden' name='chkA3" . $count . "' value='1'><font face='Verdana' size='1'>Yes</font></td>";
                    } else {
                        print "<td><input type='hidden' name='chkA3" . $count . "' value=''><font face='Verdana' size='1'>No</font></td>";
                    }
                    print "<td>&nbsp;</td>";
                }
                print "<input type='hidden' name='txhExist" . $count . "' value='" . $count . "'><input type='hidden' name='txhID" . $count . "' value='" . $cur1[5] . "'>";
            }
            print "</tr>";
        }
        $data0 = $cur[0];
        $data1 = $cur[1];
        $data2 = $cur[2];
        $data3 = $cur[3];
        $data5 = $cur[5];
        $data6 = $cur[6];
    }
    if (0 < $data0) {
//        echo "Here";
        addZero($data0, $_SESSION[$session_variable . "EmployeeCodeLength"]);
        print "<tr><td><a title='ID'><input type='hidden' name='txh" . $count . "' value='" . $data0 . "'> <font face='Verdana' color='#000000' size='1'>" . addZero($data0, $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $data1 . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $data5 . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $data2 . "</font></a></td> <td><a title='Div/Desg'><font face='Verdana' size='1'>" . $data3 . "</font></a></td> <td><a title='Rmk'><font face='Verdana' size='1'>" . $data6 . "</font></a></td> ";
        print "<td><font face='Verdana' size='1'><input size='12' name='txtDateFrom" . $count . "' value='" . $txtFrom . "' onBlur='checkFromDate(this)' class='form-control'></td>";
        print "<td><font face='Verdana' size='1'><input size='12' name='txtDateTo" . $count . "' value='" . $txtTo . "' onBlur='checkToDate(this)' class='form-control'></td>";
        print "<td>";
        displayColourFlag($conn, "", "lstFlag" . $count, false, false);
        print "</td>";
        print "<td><font face='Verdana' size='1'><input size='12' name='txtRemark" . $count . "' class='form-control'></td>";
        if (strpos($userlevel, $current_module . "A") !== false) {
            print "<td><input type='checkbox' name='chkA1" . $count . "' onClick='checkFlag(this, document.frm1.lstFlag" . $count . ")'></td>";
        } else {
            print "<td>&nbsp;</td>";
        }
        if (strpos($userlevel, $current_module . "E") !== false) {
            print "<td><input type='checkbox' name='chkA2" . $count . "'></td>";
        } else {
            print "<td>&nbsp;</td>";
        }
        if (strpos($userlevel, $current_module . "D") !== false) {
            print "<td><input type='checkbox' name='chkA3" . $count . "'></td>";
        } else {
            print "<td>&nbsp;</td>";
        }
        print "<td>&nbsp;</td>";
        print "<input type='hidden' name='txhExist" . $count . "' value='0'>";
        print "</tr>";
        $count++;
    }
    print "</table><input type='hidden' name='txhCount' value='" . $count . "'>";
    if ($excel != "yes") {
        print "<br><p align='center'><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $count . "</b></font>";
    }
    
    if ($prints != "yes") {
        if ((strpos($userlevel, $current_module . "A") !== false || strpos($userlevel, $current_module . "E") !== false || strpos($userlevel, $current_module . "D") !== false) && $_SESSION[$session_variable . "LockDate"] < insertDate($txtFrom)) {
            print "<div class='row'>";
//            print "<table border='0'><tr>";
            print "<div class='col-2'></div>";
            print "<div class='col-2'></div>";
            print "<div class='col-2'>";
            print "<div class='mb-3'>";
            if ($lstUserType == "User") {
                print "<label class='form-label'>Exclude OT Days: </label><select name='lstExcludeOT'  class='form-control'> <option value='" . $lstExcludeOT . "' selected>" . $lstExcludeOT . "</option><option value='OT1'>OT1</option><option value='OT2'>OT2</option><option value='OT1/OT2'>OT1/OT2</option><option value='NONE'>NONE</option></select>";
            } else {
                print "<td align='right'><font face='Verdana' size='2'>Exclude OT Days: </font></td> <td align='left'><input type='hidden' name='lstExcludeOT' value='OT1/OT2'><font face='Verdana' size='2'>OT1/OT2</font></td>";
            }
            print "</div>";
            print "</div>";
            print "<div class='col-2'>";
            $query = "SELECT id, name from tgate WHERE tgate.exit = 0 ORDER BY name";
            if ($lstUserType == "User") {
                displayList("lstDeptTerminal", "Dept Terminal: ", $lstDeptTerminal, $prints, $conn, $query, "", "", "");
            } else {
                $result = selectData($conn, $query);
                print "<td align='right'><font face='Verdana' size='2'>Dept Terminal: </font></td> <td align='left'><input type='hidden' name='lstDeptTerminal' value='" . $result[0] . "'><font face='Verdana' size='2'>" . $result[1] . "</font></td>";
            }
            print "</div>";
            print "</div>";
            print "<div class='row'>";
            print "<div class='mb-3'>";
            print "<center><input name='btSubmit' type='button' value='Save Changes' onClick='saveChanges()' class='btn btn-primary'><input type='hidden' name='act' value='saveRecord'></center>";
            print "</div>";
            print "</div>";
        } else {
            print "<td>&nbsp;</td><td>";
        }
        print "<div class='row'>";
        print "<div class='col-12'>";
        print "<div class='mb-3'>";
        print "<center><input type='button' value='Print Report' onClick='checkFlagApplicationPrint(0)' class='btn btn-primary'>&nbsp;&nbsp;<input type='button' value='Excel' onClick='checkFlagApplicationPrint(1)' class='btn btn-primary'></center>";
        print "</div>";
        print "</div>";
        print "</div>";
        print "</table>";
    }
    print "</p>";
    print "</form>";
}
print "</div></div></div></div></div>";
include 'footer.php';
//print "\r\n<script>\r\nfunction openWindow(a){\r\n\twindow.open(\"FlagApplication.php?act=viewRecord&txtID=\"+a, \"\",\"height=400;width=400\");\r\n}\r\n\r\nfunction checkFlagApplicationPrint(a){\r\n\tvar x = document.frm1;\r\n\tif (check_valid_date(x.txtFrom.value) == false){\r\n\t\talert('Invalid From Date. Date Format should be DD/MM/YYYY');\r\n\t\tx.txtFrom.focus();\r\n\t\treturn false;\r\n\t}else{\r\n\t\tif (a == 0){\r\n\t\t\tif (confirm('Go Green - Think Twice before you Print this Document \\nAre you sure want to Print?')){\r\n\t\t\t\tx.action = 'FlagApplication.php?prints=yes';\r\n\t\t\t\tx.target = '_blank';\r\n\t\t\t\tx.submit();\r\n\t\t\t\treturn true;\r\n\t\t\t}else{\r\n\t\t\t\treturn false;\r\n\t\t\t}\r\n\t\t}else{\r\n\t\t\tx.action = 'FlagApplication.php?prints=yes&excel=yes';\t\r\n\t\t\tx.target = '_blank';\r\n\t\t\tx.submit();\r\n\t\t\treturn true;\r\n\t\t}\t\t\r\n\t}\r\n}\r\n\r\nfunction checkFlagApplicationSearch(){\r\n\tvar x = document.frm1;\r\n\tif (check_valid_date(x.txtFrom.value) == false){\r\n\t\talert('Invalid From Date. Date Format should be DD/MM/YYYY');\r\n\t\tx.txtFrom.focus();\r\n\t\treturn false;\r\n\t}else{\r\n\t\tx.action = 'FlagApplication.php?prints=no';\r\n\t\tx.target = '_self';\r\n\t\tx.btSearch.disabled = true;\r\n\t\treturn true;\r\n\t}\r\n}\r\n\r\nfunction checkAssignTextbox(x){\r\n\tif (x.value*1 != x.value/1){\r\n\t\talert(\"ONLY Numeric Value ALLOWED as OT\");\r\n\t\tx.focus();\r\n\t}\r\n}\r\n\r\nfunction insertOTAll(x, w){\r\n\ty = document.frm1.txtOTAll.value;\t\r\n\tz = document.frm1.txtRemarkAll.value;\t\r\n\tif (y != \"\" && y*1 == y/1){\r\n\t\tif (x.value == \"\" || x.value == 0){\r\n\t\t\tx.value = y;\r\n\t\t\tw.value = z;\r\n\t\t}\r\n\t}\r\n}\r\n\r\nfunction insertAllOT(){\r\n\tx = document.frm1;\r\n\tif (x.txtOTAll.value != \"\" && x.txtOTAll.value != 0){\r\n\t\tif (confirm(\"Enter OT = \"+x.txtOTAll.value+\" in all the Below Blank OT Records?\")){\t\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\r\n\t\t\t\tif (document.getElementById(\"txtOT\"+i).value == \"\" || document.getElementById(\"txtOT\"+i).value == 0){\r\n\t\t\t\t\tdocument.getElementById(\"txtOT\"+i).value = x.txtOTAll.value;\r\n\t\t\t\t\tdocument.getElementById(\"txtRemark\"+i).value = x.txtRemarkAll.value;\r\n\t\t\t\t}\r\n\t\t\t}\r\n\t\t}\r\n\t}else{\r\n\t\talert(\"Please enter the OT value to be assigned to all Records\");\r\n\t\tx.txtOTAll.focus();\r\n\t}\r\n}\r\n\r\nfunction checkA2All(){\r\n\tx = document.frm1;\r\n\tif (x.chkA2All.checked == true){\r\n\t\tif (confirm(\"Approve All OT\")){\t\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\r\n\t\t\t\tif (document.getElementById(\"txtOT\"+i)){\r\n\t\t\t\t\tif (document.getElementById(\"txtOT\"+i).value != \"\" && document.getElementById(\"txtOT\"+i).value != 0){\r\n\t\t\t\t\t\tdocument.getElementById(\"chkA2\"+i).checked = true;\t\r\n\t\t\t\t\t}\r\n\t\t\t\t}\r\n\t\t\t}\r\n\t\t}else{\r\n\t\t\tx.chkA2All.checked = false;\r\n\t\t}\r\n\t}else{\r\n\t\tif (confirm(\"De-Approve All OT\")){\t\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\r\n\t\t\t\tdocument.getElementById(\"chkA2\"+i).checked = false;\t\t\t\t\t\r\n\t\t\t}\r\n\t\t}else{\r\n\t\t\tx.chkA2All.checked = true;\r\n\t\t}\r\n\t}\r\n}\r\n\r\nfunction checkA3All(){\r\n\tx = document.frm1;\r\n\tif (x.chkA3All.checked == true){\r\n\t\tif (confirm(\"Authorize All OT\")){\t\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\t\t\t\t\r\n\t\t\t\tif (document.getElementById(\"txtOT\"+i).value != \"\" && document.getElementById(\"txtOT\"+i).value != 0 && (document.getElementById(\"chkA2\"+i).checked == true || document.getElementById(\"chkA2\"+i).value == 1)){\r\n\t\t\t\t\tdocument.getElementById(\"chkA3\"+i).checked = true;\t\t\t\t\t\t\r\n\t\t\t\t}\r\n\t\t\t}\r\n\t\t}else{\r\n\t\t\tx.chkA3All.checked = false;\r\n\t\t}\r\n\t}else{\r\n\t\tif (confirm(\"De-Authorize All OT\")){\t\r\n\t\t\tfor (i=0;i<x.txhCount.value;i++){\r\n\t\t\t\tdocument.getElementById(\"chkA3\"+i).checked = false;\t\t\t\t\t\r\n\t\t\t}\r\n\t\t}else{\r\n\t\t\tx.chkA3All.checked = true;\r\n\t\t}\r\n\t}\r\n}\r\n\r\nfunction saveChanges(){\r\n\tx = document.frm1;\r\n\tif (x.lstDeptTerminal.value == \"\"){\r\n\t\talert(\"Please select a Department Terminal\");\r\n\t\tx.lstDeptTerminal.focus();\r\n\t}else{\r\n\t\tif (confirm(\"Save Changes?\")){\r\n\t\t\tx.act.value = \"saveChanges\";\r\n\t\t\tx.btSubmit.disabled = true;\r\n\t\t\tx.submit();\r\n\t\t}\r\n\t}\r\n}\r\n\r\nfunction checkFlag(x, y){\r\n\tif (x.checked == true){\r\n\t\tif (y.value == \"\"){\r\n\t\t\talert('Please select a Flag');\r\n\t\t\tx.checked = false;\r\n\t\t\ty.focus();\r\n\t\t}\r\n\t}\r\n}\r\n\r\nfunction deleteRecord(a){\r\n\tx = document.frm1;\r\n\tif (confirm('Delete this Record')){\r\n\t\tx.act.value = \"deleteRecord\";\t\t\r\n\t\tx.action = 'FlagApplication.php?txtID='+(a*1024*1024);\r\n\t\tx.submit();\r\n\t}\r\n}\r\n\r\nfunction checkFromDate(x){\r\n\tvar d = new Date().getFullYear();\r\n\tif (check_valid_date(x.value) == false){\r\n\t\talert('Invalid From Date. Date Format should be DD/MM/YYYY');\r\n\t\tx.focus();\r\n\t\treturn false;\r\n\t}else if (x.value.substring(6, 10) != d){\r\n\t\talert('Invalid From Year. Only Current Year Allowed');\r\n\t\tx.focus();\r\n\t\treturn false;\r\n\t}else{\r\n\t\treturn true;\r\n\t}\r\n}\r\n\r\nfunction checkToDate(x){\r\n\tz = document.frm1;\r\n\tvar d = new Date().getFullYear();\r\n\tif (check_valid_date(x.value) == false){\r\n\t\talert('Invalid From Date. Date Format should be DD/MM/YYYY');\r\n\t\tx.focus();\r\n\t\treturn false;\r\n\t}else if (z.txhFlagLimitType.value == 'Jan 01' && x.value.substring(6, 10) != d){\r\n\t\talert('Invalid To Year. Only Current Year Allowed');\r\n\t\tx.focus();\r\n\t\treturn false;\r\n\t}else if (z.txhFlagLimitType.value == 'Employee Start Date' && (x.value.substring(6, 10) < d || x.value.substring(6, 10) > (d+1)) ){\r\n\t\talert('Invalid To Year. Only Current and Next Year Allowed');\r\n\t\tx.focus();\r\n\t\treturn false;\r\n\t}else{\r\n\t\treturn true;\r\n\t}\r\n}\r\n</script>";
?>
<script>
    
function openWindow(a){
	window.open("FlagApplication.php?act=viewRecord&txtID="+a, "","height=400;width=400");
}

function checkFlagApplicationPrint(a){
	var x = document.frm1;
	if (check_valid_date(x.txtFrom.value) == false){
		alert('Invalid From Date. Date Format should be DD/MM/YYYY');
		x.txtFrom.focus();
		return false;
	}else{
		if (a == 0){
			if (confirm('Go Green - Think Twice before you Print this Document \nAre you sure want to Print?')){
				x.action = 'FlagApplication.php?prints=yes';
				x.target = '_blank';
				x.submit();
				return true;
			}else{
				return false;
			}
		}else{
			x.action = 'FlagApplication.php?prints=yes&excel=yes';	
			x.target = '_blank';
			x.submit();
			return true;
		}		
	}
}

function checkFlagApplicationSearch(){
	var x = document.frm1;
	if (check_valid_date(x.txtFrom.value) == false){
		alert('Invalid From Date. Date Format should be DD/MM/YYYY');
		x.txtFrom.focus();
		return false;
	}else{
		x.action = 'FlagApplication.php?prints=no';
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

function insertAllOT(){
	x = document.frm1;
	if (x.txtOTAll.value != "" && x.txtOTAll.value != 0){
		if (confirm("Enter OT = "+x.txtOTAll.value+" in all the Below Blank OT Records?")){	
			for (i=0;i<x.txhCount.value;i++){
				if (document.getElementById("txtOT"+i).value == "" || document.getElementById("txtOT"+i).value == 0){
					document.getElementById("txtOT"+i).value = x.txtOTAll.value;
					document.getElementById("txtRemark"+i).value = x.txtRemarkAll.value;
				}
			}
		}
	}else{
		alert("Please enter the OT value to be assigned to all Records");
		x.txtOTAll.focus();
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
	if (x.lstDeptTerminal.value == ""){
		alert("Please select a Department Terminal");
		x.lstDeptTerminal.focus();
	}else{
		if (confirm("Save Changes?")){ 
			x.act.value = "saveChanges";
			x.btSubmit.disabled = true;
			x.submit();
		}
	}
}

function checkFlag(x, y){
	if (x.checked == true){
		if (y.value == ""){
			alert('Please select a Flag');
			x.checked = false;
			y.focus();
		}
	}
}

function deleteRecord(a){
	if (confirm('Delete this Record')){
                let x = document.frm1;
		x.act.value = "deleteRecord";		
		x.action = 'FlagApplication.php?txtID='+(a*1024*1024);
		x.submit();
	}
}

function checkFromDate(x){
	var d = new Date().getFullYear();
	if (check_valid_date(x.value) == false){
		alert('Invalid From Date. Date Format should be DD/MM/YYYY');
		x.focus();
		return false;
	}else if (x.value.substring(6, 10) != d){
		alert('Invalid From Year. Only Current Year Allowed');
		x.focus();
		return false;
	}else{
		return true;
	}
}

function checkToDate(x){
	z = document.frm1;
	var d = new Date().getFullYear();
	if (check_valid_date(x.value) == false){
		alert('Invalid From Date. Date Format should be DD/MM/YYYY');
		x.focus();
		return false;
	}else if (z.txhFlagLimitType.value == 'Jan 01' && x.value.substring(6, 10) != d){
		alert('Invalid To Year. Only Current Year Allowed');
		x.focus();
		return false;
	}else if (z.txhFlagLimitType.value == 'Employee Start Date' && (x.value.substring(6, 10) < d || x.value.substring(6, 10) > (d+1)) ){
		alert('Invalid To Year. Only Current and Next Year Allowed');
		x.focus();
		return false;
	}else{
		return true;
	}
}

</script>