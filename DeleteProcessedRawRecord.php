<?php
ob_start("ob_gzhandler");
error_reporting(E_ERROR);
set_time_limit(0);
ini_set('display_errors', 1);
ini_set("memory_limit", "-1");
ini_set("post_max_size", "0");
ini_set("max_execution_time", "0");
ini_set("max_input_time", "-1");
ini_set("max_input_vars", "100000");
include "Functions.php";
session_start();
$current_module = "22";
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=DeleteProcessedRawRecord.php&message=Session Expired or Security Policy Violated");
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
    $message = "Delete Processed Raw Log";
}
$lstShift = $_POST["lstShift"];
$lstDepartment = $_POST["lstDepartment"];
$lstDivision = $_POST["lstDivision"];
$lstWeek = $_POST["lstWeek"];
$lstEmployeeIDFrom = $_POST["lstEmployeeIDFrom"];
$lstEmployeeIDTo = $_POST["lstEmployeeIDTo"];
$lstEmployee = $_POST["lstEmployee"];
if ($lstEmployee == "") {
    $lstEmployee = $_GET["lstEmployee"];
}
$txtEmployee = $_POST["txtEmployee"];
$txtEmployeeCode = $_POST["txtEmployeeCode"];
$txtFrom = $_POST["txtFrom"];
if ($txtFrom == "") {
    $txtFrom = $_GET["txtFrom"];
}
$txtTo = $_POST["txtTo"];
if ($txtTo == "") {
    $txtTo = $_GET["txtTo"];
}
if ($txtFrom == "") {
    if (substr(insertToday(), 6, 2) == "01") {
        if (substr(insertToday(), 4, 2) == "01") {
            $txtFrom = "01/12/" . (substr(insertToday(), 0, 4) - 1);
        } else {
            if (substr(insertToday(), 4, 2) - 1 < 10) {
                $txtFrom = "01/0" . (substr(insertToday(), 4, 2) - 1) . "/" . substr(insertToday(), 0, 4);
            } else {
                $txtFrom = "01/" . (substr(insertToday(), 4, 2) - 1) . "/" . substr(insertToday(), 0, 4);
            }
        }
    } else {
        $txtFrom = "01/" . substr(displayToday(), 3, 7);
    }
}
if ($txtTo == "") {
    $txtTo = displayDate(getLastDay(insertToday(), 1));
}
$txtSNo = $_POST["txtSNo"];
$lstEmployeeStatus = "";
if (isset($_POST["lstEmployeeStatus"])) {
    $lstEmployeeStatus = $_POST["lstEmployeeStatus"];
} else {
    $lstEmployeeStatus = "ACT";
}
$lstClockingType = $_POST["lstClockingType"];
$txtRemark = $_POST["txtRemark"];
$txtPhone = $_POST["txtPhone"];
$lstSort = $_POST["lstSort"];
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


if ($act == "deleteRecord") {
    $query = "SELECT NightShiftMaxOutTime FROM OtherSettingMaster";
    $global_result = selectData($conn, $query);
    $count = $_POST["txtCount"];
    for ($i = 0; $i < $count; $i++) {
        if ($_POST["chkDelete" . $i] != "") {
            if ($_POST["txhMoveNS" . $i] == "Yes" && $_POST["txhNightFlag" . $i] == "1") {
                if (($global_result[0] . "00") * 1 <= $_POST["txhATime" . $i]) {
                    $query = "SELECT AttendanceID FROM AttendanceMaster WHERE ADate = " . getNextDay($_POST["txhADate" . $i], 1) . " AND EmployeeID = " . $_POST["txhEID" . $i] . " AND group_id = '" . $_POST["txhShift" . $i] . "'";
                } else {
                    $query = "SELECT AttendanceID FROM AttendanceMaster WHERE ADate = " . $_POST["txhADate" . $i] . " AND EmployeeID = " . $_POST["txhEID" . $i] . " AND group_id = '" . $_POST["txhShift" . $i] . "'";
                }
            } else {
                $query = "SELECT AttendanceID FROM AttendanceMaster WHERE ADate = " . $_POST["txhADate" . $i] . " AND EmployeeID = " . $_POST["txhEID" . $i] . " AND group_id = '" . $_POST["txhShift" . $i] . "'";
            }
            $result = selectData($conn, $query);
            if ($result == "" && $result[0] == "") {
                $query = "UPDATE tenter SET p_flag = 0 WHERE ed = '" . $_POST["txhID" . $i] . "' AND p_flag = 1";
                updateIData($iconn, $query, true);
            }
        }
    }
    $message = "Record(s) deleted Successfully";
    $act = "searchRecord";
}

if ($prints != "yes") {
    include 'header.php';
    ?>
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-12 d-flex no-block align-items-center">
                <h4 class="page-title">Delete Processed Raw Log</h4>
                <div class="ms-auto text-end">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Delete Processed Raw Log
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <?php
}
print "<form name='frm1' id='frm1' method='post' onSubmit='return checkSearch()' action='DeleteProcessedRawRecord.php'><input type='hidden' name='act' value='searchRecord'>";
//print "<html><title>Delete Processed Raw Log</title>";
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
        header("Content-Disposition: attachment; filename=DeleteProcessedRawRecord.xls");
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
                //    print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Select ONE or MORE options and click 'Search Record'</b></font></td></tr>";
            } else {
                if ($excel != "yes") {
//                print "<table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
//                print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Selected Options</b></font></td></tr>";
                }
            }
            ?>
            <div class="row">
                <div class="col-3">
    <?php
    $query = "SELECT id, name from tgroup WHERE id > 1 ORDER BY name";
    displayList("lstShift", "Shift: ", $lstShift, $prints, $conn, $query, "", "", "");
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
                        displayClockingType($lstClockingType);
                        print "</div>";
                        print "<div class='col-2'>";
                        displayEmployeeStatus($conn, "lstEmployeeStatus", $lstEmployeeStatus, "", "");
                        print "</div>";
                        print "<div class='col-2'>";
                        $array = array(array("tuser.id, tenter.e_date, tenter.e_time", "Employee Code"), array("tuser.name, tuser.id, tenter.e_date, tenter.e_time", "Employee Name - Code"), array("tuser.dept, tuser.id, tenter.e_date, tenter.e_time", "Dept - Code"), array("tuser.company, tuser.dept, tuser.id, tenter.e_date, tenter.e_time", "Div/Desg - Dept - Code"), array("tuser.company, tuser.dept, tenter.e_group, tuser.id, tenter.e_date, tenter.e_time", "Div - Dept - Shift - Code"));
                        displaySort($array, $lstSort, 8);
                        print "</div>";
                        print "</div>";
                        print "<div class='row'>";
                        print "<div class='col-12'>";
                        print "<center><input name='btSearch' type='submit' value='Search Record' class='btn btn-primary'></center>";
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
    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tenter.e_date, tenter.e_time, tenter.e_etc, tenter.p_flag, tenter.ed, tenter.e_group, tuser.idno, tuser.remark, tgroup.NightFlag, tgroup.MoveNS FROM tuser, tgroup, tenter WHERE tenter.e_group = tgroup.id AND tenter.e_id = tuser.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
    if ($lstShift != "") {
        $query = $query . " AND tgroup.id = " . $lstShift;
    }
    $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
    if ($txtFrom != "") {
        $query = $query . " AND tenter.e_date >= " . insertDate($txtFrom);
    }
    if ($txtTo != "") {
        $query = $query . " AND tenter.e_date <= " . insertDate($txtTo);
    }
    $query = queryClockingType($query, $lstClockingType);
    $query = $query . employeeStatusQuery($lstEmployeeStatus);
    $query = $query . " AND p_flag = '1' ORDER BY " . $lstSort;
    print '<div class="row"><div class="col-md-12"><div class="card"><div class="card-body table-responsive">';
    if ($prints != "yes") {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800' class='table table-striped table-bordered dataTable'>";
    } else {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%' class='table table-striped table-bordered dataTable'>";
    }
    print "<thead><tr><td><font face='Verdana' size='2'>";
    if ($prints != "yes") {
        print "<input type='checkbox' name='chkDelete' onClick='javascript:checkAll()'>";
    } else {
        print "&nbsp;";
    }
    print "</font></td> <td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Shift</font></td> <td><font face='Verdana' size='2'>Date</font></td> <td><font face='Verdana' size='2'>Time</font></td> <td><font face='Verdana' size='2'>Proxy</font></td> </tr></thead>";
    $result = mysqli_query($iconn, $query);
    for ($count = 0; $cur = mysqli_fetch_row($result); $count++) {
        if ($cur[11] == "") {
            $cur[11] = "&nbsp;";
        }
        if ($cur[12] == "") {
            $cur[12] = "&nbsp;";
        }
        print "<tr>";
        if ($prints != "yes") {
            print "<input type='hidden' name='txhID" . $count . "' value='" . $cur[9] . "'> <input type='hidden' name='txhADate" . $count . "' value='" . $cur[5] . "'> <input type='hidden' name='txhATime" . $count . "' value='" . $cur[6] . "'> <input type='hidden' name='txhEID" . $count . "' value='" . $cur[0] . "'> <input type='hidden' name='txhShift" . $count . "' value='" . $cur[10] . "'> <input type='hidden' name='txhNightFlag" . $count . "' value='" . $cur[13] . "'> <input type='hidden' name='txhMoveNS" . $count . "' value='" . $cur[14] . "'><td><input type='checkbox' name='chkDelete" . $count . "' id='chkDelete" . $count . "'> </td>";
        } else {
            print "<input type='hidden' name='txhID" . $count . "' value='" . $cur[9] . "'><td><font size='1'>&nbsp;</font> </td>";
        }
        addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
        displayDate($cur[5]);
        displayVirdiTime($cur[6]);
        print "<input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'><td><a title='ID'><font face='Verdana' size='1'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $cur[1] . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $cur[11] . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $cur[2] . "</font></a></td> <td><a title='Div/Desg'><font face='Verdana' size='1'>" . $cur[3] . "</font></a></td> <td><a title='Rmk'><font face='Verdana' size='1'>" . $cur[12] . "</font></a></td> <td><a title='Shift'><font face='Verdana' size='1'>" . $cur[4] . "</font></a></td> <td><a title='Date'><font face='Verdana' size='1'>" . displayDate($cur[5]) . "</font></a></td> <td><a title='Time'><font face='Verdana' size='1'>" . displayVirdiTime($cur[6]) . "</font></a></td>";
        if ($cur[7] == "P") {
            print "<td><a title='Proxy'><font face='Verdana' size='1'>Yes</font></td>";
        } else {
            print "<td><a title='Proxy'><font face='Verdana' size='1'>No</font></td>";
        }
        print "</tr>";
//        print "<input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'>";
    }
    print "</table>";
    if ($excel != "yes") {
        print "<p align='center'><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $count . "</b></font><input type='hidden' name='txtCount' value='" . $count . "'>";
        if ($prints != "yes" && strpos($userlevel, $current_module . "D") !== false && $_SESSION[$session_variable . "LockDate"] < insertDate($txtFrom) && $_SESSION[$session_variable . "LockDate"] < insertDate($txtTo)) {
            print "<br><input name='btSubmit' type='button' value='Delete Selected Record(s)' onClick='checkSubmit()' class='btn btn-primary'>";
        }
        if ($prints != "yes") {
            print "<br><br><input type='button' value='Print Report' onClick='checkPrint(0)' class='btn btn-primary'>&nbsp;&nbsp;<input type='button' value='Excel' onClick='checkPrint(1)' class='btn btn-primary'>";
        }
        print "</p>";
    }
    print "</div></div></div></div>";
}
print "</div>";
print "</form>";
print "<script>\r\nfunction checkSubmit(){\t\r\n\tif (confirm('Are you sure you want to DELETE the selected Record(s)')){\r\n\t\tx = document.frm1;\r\n\t\tx.target = '_self';\r\n\t\tx.act.value='deleteRecord';\r\n\t\tx.btSubmit.disabled = true;\r\n\t\tx.submit();\r\n\t}\t\r\n}\r\n\r\nfunction checkDelete(x, y, z){\r\n\tif (x.checked == true){\r\n\t\tif (y.value == 0 || y.value == ''){\r\n\t\t\ty.value = z.value;\r\n\t\t}\r\n\t}else{\r\n\t\ty.value = 0;\t\r\n\t}\r\n}\r\n\r\nfunction checkAll(){\t\r\n\tx = document.frm1;\r\n\ty = x.chkDelete;\r\n\tz = x.txtCount.value;\t\r\n\tfor (i=0;i<z;i++){\t\t\r\n\t\tif (y.checked == true){\t\t\t\r\n\t\t\tdocument.getElementById(\"chkDelete\"+i).checked = true;\t\t\t\r\n\t\t}else{\r\n\t\t\tdocument.getElementById(\"chkDelete\"+i).checked = false;\r\n\t\t}\r\n\t}\r\n}\r\n</script>";
include 'footer.php';
?>
<script>
    $(document).ready(function () {
        var table = $('#zero_config').DataTable();

        // Use event delegation for the "Select All" checkbox
        $('#chkAll').on('click', function () {
            var checked = this.checked;
            // Select all checkboxes in the table (visible and non-visible rows)
            $('input[type="checkbox"]', table.rows().nodes()).prop('checked', checked);
        });
    });
</script>