<?php
ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "23";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
$flagLimitType = $_SESSION[$session_variable . "FlagLimitType"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=FlagDay.php&message=Session Expired or Security Policy Violated");
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
    $message = "Flag Day(s) (Post Attendance) <br>Valid ONLY for Shifts with Routine Type = Daily";
}
$lstShift = $_POST["lstShift"];
$lstDepartment = $_POST["lstDepartment"];
$lstDivision = $_POST["lstDivision"];
$lstEmployeeID = $_GET["lstEmployeeID"];
$lstEmployeeIDFrom = $_POST["lstEmployeeIDFrom"];
$lstEmployeeIDTo = $_POST["lstEmployeeIDTo"];
if ($lstEmployeeID != "" && $lstEmployeeIDFrom == "" && $lstEmployeeIDTo == "") {
    $lstEmployeeIDFrom = $lstEmployeeID;
    $lstEmployeeIDTo = $lstEmployeeID;
}
$txtEmployeeCode = $_POST["txtEmployeeCode"];
$lstSort = $_POST["lstSort"];
$txtEmployee = $_POST["txtEmployee"];
$lstColourFlag = $_POST["lstColourFlag"];
if ($lstColourFlag == "") {
    $lstColourFlag = $_GET["lstColourFlag"];
}
$lstSetFlag = $_POST["lstSetFlag"];
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
$txtRemark = $_POST["txtRemark"];
$txtPhone = $_POST["txtPhone"];
$txtRemarks = $_POST["txtRemarks"];
$lstRemark = $_POST["lstRemark"];
if ($lstRemark == "") {
    $lstRemark = "Yes";
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
if ($act == "saveRecord") {
    $count = $_POST["txtCounter"];
    $insert_flag = false;
    for ($i = 0; $i < $count; $i++) {
        if ($_POST["chk" . $i] != "") {
            $insert_flag = false;
            if ($lstSetFlag == "Black" || $lstSetFlag == "Proxy") {
                $insert_flag = true;
            } else {
                $query = "SELECT " . $lstSetFlag . " FROM EmployeeFlag WHERE EmployeeID = " . $_POST["txh" . $i];
                $result = selectData($conn, $query);
                $max_flag_limit = $result[0];
                $pre_flag_count = 0;
                $post_flag_count = 0;
                if ($max_flag_limit == "") {
                    $query = "INSERT INTO EmployeeFlag (EmployeeID) VALUES (" . $_POST["txh" . $i] . ")";
                    updateData($conn, $query, true);
                    $max_flag_limit = 365;
                }
                if ($flagLimitType == "Jan 01") {
                    $query = "SELECT COUNT(FlagDayRotationID) FROM FlagDayRotation WHERE e_id = " . $_POST["txh" . $i] . " AND Flag = '" . $lstSetFlag . "' AND e_date >= " . substr(insertToday(), 0, 4) . "0101 AND e_date <= " . substr(insertToday(), 0, 4) . "1231 AND RecStat = 0";
                } else {
                    $query = "SELECT COUNT(FlagDayRotationID) FROM FlagDayRotation, tuser WHERE tuser.id = FlagDayRotation.e_id AND FlagDayRotation.e_id = " . $_POST["txh" . $i] . " AND FlagDayRotation.Flag = '" . $lstSetFlag . "' AND FlagDayRotation.e_date >= CONCAT(" . substr(insertToday(), 0, 4) . ", SUBSTR(tuser.datelimit, 6, 4)) AND FlagDayRotation.e_date < CONCAT(" . (substr(insertToday(), 0, 4) * 1 + 1) . ", SUBSTR(tuser.datelimit, 6, 4)) AND FlagDayRotation.RecStat = 0";
                }
                $result = selectData($conn, $query);
                $pre_flag_count = $result[0];
                if ($pre_flag_count == "") {
                    $pre_flag_count = 0;
                }
                if ($flagLimitType == "Jan 01") {
                    $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster WHERE EmployeeID = " . $_POST["txh" . $i] . " AND Flag = '" . $lstSetFlag . "' AND ADate >= " . substr(insertToday(), 0, 4) . "0101 AND ADate <= " . substr(insertToday(), 0, 4) . "1231 ";
                } else {
                    $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND AttendanceMaster.EmployeeID = " . $_POST["txh" . $i] . " AND AttendanceMaster.Flag = '" . $lstSetFlag . "' AND AttendanceMaster.ADate >= CONCAT(" . substr(insertToday(), 0, 4) . ", SUBSTR(tuser.datelimit, 6, 4)) AND AttendanceMaster.ADate < CONCAT(" . (substr(insertToday(), 0, 4) * 1 + 1) . ", SUBSTR(tuser.datelimit, 6, 4)) ";
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
                $query = "UPDATE DayMaster SET Flag = '" . $lstSetFlag . "' WHERE DayMasterID = " . $_POST["txhID" . $i];
                updateIData($iconn, $query, true);
                $query = "UPDATE AttendanceMaster SET Flag = '" . $lstSetFlag . "', Remark = '" . $txtRemarks . "' WHERE ADate = " . $_POST["txhDate" . $i] . " AND EmployeeID = " . $_POST["txh" . $i];
                updateIData($iconn, $query, true);
                $text = "Post Flagged ID: " . $_POST["txh" . $i] . " for Date: " . displayDate($_POST["txhDate" . $i]) . " with Flag: " . $lstSetFlag;
                $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                updateIData($iconn, $query, true);
            }
        }
    }
    $message = "Successfully Flagged Selected Date(s) to " . $lstSetFlag . " for the Selected Employee(s) WITHIN the MAX Annual Flag Limit";
    $act = "searchRecord";
}
if ($prints != "yes") {
    include 'header.php';
?>
<div class="page-breadcrumb">
                    <div class="row">
                        <div class="col-12 d-flex no-block align-items-center">
                            <h4 class="page-title">Flag Day(s) (Post Attendance)</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Flag Day(s) (Post Attendance)
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

<?php
}
print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='FlagDay.php'><input type='hidden' name='act' value='searchRecord'>";
//print "<html><title>Flag Day(s) (Post Attendance)</title>";
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
        header("Content-Disposition: attachment; filename=ReportEmployee.xls");
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
        if ($excel != "yes") {
            print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b></font></p>";
        }
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
                $query = "SELECT id, name from tgroup WHERE id > 1 AND ShiftTypeID = 1 ORDER BY name";
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
                displayColourFlag($conn, $lstColourFlag, "lstColourFlag", true, true);
                ?>
            </div>
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
                print "<label class='form-label'>Display Attendance Remark:</label><select name='lstRemark' class='form-control'><option selected value='" . $lstRemark . "'>" . $lstRemark . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select>";
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
                print "<center><input name='btSearch' type='submit' value='Search Record' class='btn btn-primary'>";
                print "</center>";
                print "</div>";
                print "</div>";
            }
         ?>
        <!--</form>-->
    </div>
</div>
<?php
}
print "</div></div></div>";
if ($act == "searchRecord") {
    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, DayMaster.TDate, DayMaster.Entry, DayMaster.Start, DayMaster.BreakOut, DayMaster.BreakIn, DayMaster.Close, DayMaster.Exit, DayMaster.Flag, DayMaster.DayMasterID, tuser.idno, tuser.remark FROM tuser, tgroup, DayMaster WHERE DayMaster.group_id = tgroup.id AND DayMaster.e_id = tuser.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
    if ($lstShift != "") {
        $query = $query . " AND tgroup.id = " . $lstShift;
    }
    $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
    if ($lstColourFlag != "") {
        if ($lstColourFlag == "Black/Proxy") {
            $query = $query . " AND (DayMaster.Flag = 'Black' OR DayMaster.Flag = 'Proxy') ";
        } else {
            if ($lstColourFlag == "All w/o Black/Proxy") {
                $query = $query . " AND DayMaster.Flag NOT LIKE 'Black' AND DayMaster.Flag NOT LIKE 'Proxy'";
            } else {
                if ($lstColourFlag == "All w/o Proxy") {
                    $query = $query . " AND DayMaster.Flag NOT LIKE 'Proxy'";
                } else {
                    $query = $query . " AND DayMaster.Flag = '" . $lstColourFlag . "'";
                }
            }
        }
    } else {
        $query = $query . " AND DayMaster.Flag NOT LIKE 'Delete'";
    }
    if ($txtFrom != "") {
        $query = $query . " AND DayMaster.TDate >= " . insertDate($txtFrom);
    }
    if ($txtTo != "") {
        $query = $query . " AND DayMaster.TDate <= " . insertDate($txtTo);
    }
    $query = $query . employeeStatusQuery($lstEmployeeStatus);
    if ($lstSort != "") {
        $query = $query . " ORDER BY " . $lstSort;
    }
    if ($excel != "yes") {
        print "<p align='center'><font face='Verdana' size='1'><b>" . $_SESSION[$session_variable . "FlagReportText"] . "</b></font></p>";
    }
    print '<div class="row"><div class="col-md-12"><div class="card"><div class="card-body table-responsive">';
    if ($prints != "yes") {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800' class='table table-striped table-bordered dataTable'>";
    } else {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%' class='table table-striped table-bordered dataTable'>";
    }
    print "<thead><tr><td><input type='checkbox' name='chk' onClick='javascript:checkCheckBox()'></td> <td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Shift</font></td> <td><font face='Verdana' size='2'>Date</font></td> <td><font face='Verdana' size='2'>Entry</font></td> <td><font face='Verdana' size='2'>Start</font></td> <td><font face='Verdana' size='2'>BreakOut</font></td> <td><font face='Verdana' size='2'>BreakIn</font></td> <td><font face='Verdana' size='2'>Close</font></td> <td><font face='Verdana' size='2'>Exit</font></td> <td><font face='Verdana' size='2'>Flag</font></td>";
    if ($lstRemark == "Yes") {
        print "<td><font face='Verdana' size='2'>A Rmk</font></td>";
    }
    print "</tr></thead>";
    $result = mysqli_query($iconn, $query);
    $count = 0;
    $font = "Black";
    for ($bgcolor = "#FFFFFF"; $cur = mysqli_fetch_row($result); $count++) {
        if ($cur[12] != "") {
            $font = $cur[12];
            if ($font == "Yellow") {
                $bgcolor = "Brown";
            } else {
                $bgcolor = "#FFFFFF";
            }
        } else {
            $cur[12] = "&nbsp;";
            $font = "Black";
            $bgcolor = "#FFFFFF";
        }
        if ($cur[3] == "") {
            $cur[3] = "&nbsp;";
        }
        if ($cur[14] == "") {
            $cur[14] = "&nbsp;";
        }
        if ($cur[15] == "") {
            $cur[15] = "&nbsp;";
        }
        addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
        displayDate($cur[5]);
        displayVirdiTime($cur[6]);
        displayVirdiTime($cur[7]);
        displayVirdiTime($cur[8]);
        displayVirdiTime($cur[9]);
        displayVirdiTime($cur[10]);
        displayVirdiTime($cur[11]);
        print "<tr><td bgcolor='" . $bgcolor . "'><input type='checkbox' name='chk" . $count . "' id='chk" . $count . "'><input type='hidden' name='txhID" . $count . "' value='" . $cur[13] . "'> </td> <td bgcolor='" . $bgcolor . "'><a title='ID'><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'> <input type='hidden' name='txhDate" . $count . "' value='" . $cur[5] . "'><font face='Verdana' size='1' color='" . $font . "'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Name'><font face='Verdana' size='1' color='" . $font . "'>" . $cur[1] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1' color='" . $font . "'>" . $cur[14] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Dept'><font face='Verdana' size='1' color='" . $font . "'>" . $cur[2] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Div/Desg'><font face='Verdana' size='1' color='" . $font . "'>" . $cur[3] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Rmk'><font face='Verdana' size='1' color='" . $font . "'>" . $cur[15] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Shift'><font face='Verdana' size='1' color='" . $font . "'>" . $cur[4] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Date'><font face='Verdana' size='1' color='" . $font . "'>" . displayDate($cur[5]) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Entry'><font face='Verdana' size='1' color='" . $font . "'>" . displayVirdiTime($cur[6]) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Start'><font face='Verdana' size='1' color='" . $font . "'>" . displayVirdiTime($cur[7]) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Break Out'><font face='Verdana' size='1' color='" . $font . "'>" . displayVirdiTime($cur[8]) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Break In'><font face='Verdana' size='1' color='" . $font . "'>" . displayVirdiTime($cur[9]) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Close'><font face='Verdana' size='1' color='" . $font . "'>" . displayVirdiTime($cur[10]) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Exit'><font face='Verdana' size='1' color='" . $font . "'>" . displayVirdiTime($cur[11]) . "</font> <td bgcolor='" . $bgcolor . "'><a title='Flag'><font face='Verdana' size='1' color='" . $font . "'>" . $cur[12] . "</font></a></td>";
        if ($lstRemark == "Yes") {
            $sub_query = "SELECT Remark FROM AttendanceMaster WHERE EmployeeID = '" . $cur[0] . "' AND ADate = '" . $cur[5] . "'";
            $sub_result = selectData($conn, $sub_query);
            print "<td bgcolor='" . $bgcolor . "'><a title='Remark'><font face='Verdana' size='1' color='" . $font . "'>" . $sub_result[0] . "</font></a></td>";
        }
        print "</tr>";
    }
    print "</table>";
    if ($excel != "yes") {
        print "<p align='center'><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $count . "</b></font>";
        if ($prints != "yes" && 0 < $count && $_SESSION[$session_variable . "LockDate"] < insertDate($txtFrom) && $_SESSION[$session_variable . "LockDate"] < insertDate($txtTo) && (stripos($userlevel, $current_module . "E") !== false || stripos($userlevel, $current_module . "A") !== false)) {
            print "<div class='row'>";
            print "<div class='col-2'></div>";
            print "<div class='col-2'></div>";
            print "<div class='col-2'>";
            if ($_SESSION[$session_variable . "MACAddress"] == "00-23-24-26-4A-02" && $username == "admin" || $_SESSION[$session_variable . "MACAddress"] == "00-23-AE-94-B1-52" && $username == "fc") {
                displayColourFlag($conn, $lstSetFlag, "lstSetFlag", true, true);
            } else {
                displayColourFlag($conn, $lstSetFlag, "lstSetFlag", false, true);
            }
            print "</div>";
            print "<div class='col-2'>";
            displayTextbox("txtRemarks", "Remarks: ", $txtRemarks, $prints, "", "25%", "75%");
            print "</div>";
            print "<div>";
            print "<div class='row'>";
            print "<div class='col-12'>";
            print "<center><input name='btSubmit' class='btn btn-primary' type='button' value='Flag Selected Record(s)' onClick='javascript:checkSubmit()'></center>";
            print "</div>";
            print "</div>";
        }
        print "</p>";
    }
}
print "<input type='hidden' name='txtCounter' value='" . $count . "'></form>";
print "</div></div></div></div></div>";
include 'footer.php';
echo "\r\n<script>\r\nfunction checkSubmit(){\r\n\tx = document.frm1;\r\n\tx.act.value='saveRecord';\r\n\tx.btSubmit.disabled = true;\r\n\tx.submit();\r\n}\r\n\r\nfunction checkCheckBox(){\r\n\tx = document.frm1;\r\n\tif (x.chk.checked){\r\n\t\tfor (i=0;i<x.txtCounter.value;i++){\r\n\t\t\tdocument.getElementById('chk'+i).checked = true;\r\n\t\t}\r\n\t}else{\r\n\t\tfor (i=0;i<x.txtCounter.value;i++){\r\n\t\t\tdocument.getElementById('chk'+i).checked = false;\r\n\t\t}\r\n\t}\r\n}\r\n</script>\r\n</center></body></html>";

?>