<?php
ob_start("ob_gzhandler");
error_reporting(E_ERROR);
set_time_limit(0);
include "Functions.php";
$current_module = "15";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
$lstIgnoreActualOT = $_SESSION[$session_variable . "ApproveOTIgnoreActual"];
$txtMACAddress = $_SESSION[$session_variable . "MACAddress"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=ApproveOvertime.php&message=Session Expired or Security Policy Violated");
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
    if ($lstIgnoreActualOT == "Yes") {
        $message = "Approve Overtime (Details) <br> No ADD Rights on this Module PROHIBITS Approvals ABOVE Pre-Approved (AP2) OT <br> No DELETE Rights on this Module PROHIBITS Approvals ABOVE Approved OT";
    } else {
        $message = "Approve Overtime (Details) <br> No ADD Rights on this Module PROHIBITS Approvals ABOVE Actual OR Pre-Approved (AP2) OT <br> No DELETE Rights on this Module PROHIBITS Approvals ABOVE Actual OR Approved OT";
    }
}
$lstShift = $_POST["lstShift"];
$lstDepartment = $_POST["lstDepartment"];
$lstDivision = $_POST["lstDivision"];
$lstDay = $_POST["lstDay"];
$lstEmployeeIDFrom = $_POST["lstEmployeeIDFrom"];
$lstEmployeeIDTo = $_POST["lstEmployeeIDTo"];
$txtEmployee = $_POST["txtEmployee"];
if ($txtEmployee == "") {
    $txtEmployee = $_GET["txtEmployee"];
}
$txtEmployeeCode = $_POST["txtEmployeeCode"];
if ($txtEmployeeCode == "") {
    $txtEmployeeCode = $_GET["txtEmployeeCode"];
}
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
$lstType = $_POST["lstType"];
if ($lstType == "") {
    $lstType = "";
}
$lstColourFlag = $_POST["lstColourFlag"];
$lstSort = $_POST["lstSort"];
$txtRemark = $_POST["txtRemark"];
$txtPhone = $_POST["txtPhone"];
$txtARemark = $_POST["txtARemark"];
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

if ($act == "editRecord") {
    $count = $_POST["txtCount"];
    $update_flag = false;
    for ($i = 0; $i < $count; $i++) {
        if (is_numeric($_POST["txtAOT" . $i] * 60) && 0 <= $_POST["txtAOT" . $i] && ($_POST["txtAOT" . $i] != $_POST["txhOldAOT" . $i] || $_POST["txtARemark" . $i] != $_POST["txhARemark" . $i])) {
            if (strpos($userlevel, $current_module . "A") !== false) {
                $update_flag = true;
            } else {
                $query = "SELECT OT FROM PreApproveOT WHERE OTDate = '" . $_POST["txhDate" . $i] . "' AND e_id = '" . $_POST["txh" . $i] . "' AND A3 = '1'";
                $result = selectData($conn, $query);
                if ($result[0] != "" && $_POST["txtAOT" . $i] <= $result[0] * 60 && $lstIgnoreActualOT == "Yes") {
                    $update_flag = true;
                } else {
                    if ($result[0] != "" && $_POST["txtAOT" . $i] <= $result[0] * 60 && $lstIgnoreActualOT == "No" && $_POST["txtAOT" . $i] * 1 <= $_POST["txhAOT" . $i] * 1) {
                        $update_flag = true;
                    }
                }
            }
            if ($update_flag) {
                if (getRegister(encryptDecrypt($txtMACAddress), 7) == "25") {
                    $query = "UPDATE AttendanceMaster SET LateInColumn = " . $_POST["txtLateOut" . $i] * 60 . ", Remark = '" . $_POST["txtARemark" . $i] . "' WHERE AttendanceID = " . $_POST["txhID" . $i];
                } else {
                    $query = "UPDATE AttendanceMaster SET AOvertime = " . $_POST["txtAOT" . $i] * 60 . ", Remark = '" . $_POST["txtARemark" . $i] . "' WHERE AttendanceID = " . $_POST["txhID" . $i];
                }
                updateIData($iconn, $query, true);
                $text = "Updated Approve OT for ID: " . $_POST["txh" . $i] . " - " . $_POST["txtAOT" . $i] . " minutes on " . displayDate($_POST["txhDate" . $i]) . ", Set Remark = " . $_POST["txtARemark" . $i];
                $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                updateIData($iconn, $query, true);
            }
        }
    }
    if (strpos($userlevel, $current_module . "A") !== false) {
        $message = "Record(s) saved Successfully";
    } else {
        if ($lstIgnoreActualOT == "Yes") {
            $message = "Record(s) saved Successfully <br>Approvals will NOT be posted with Overtime MORE THAN Pre-Approved (AP2) OT";
        } else {
            $message = "Record(s) saved Successfully <br>Approvals will NOT be posted with Overtime MORE THAN Actual OT/ Pre-Approved (AP2) OT";
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
                            <h4 class="page-title">Approve Overtime (Details)</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Approve Overtime (Details)
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

<?php
}
print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='ApproveOvertime.php'><input type='hidden' name='act' value='searchRecord'> <input type='hidden' name='lstIgnoreActualOT' value='" . $lstIgnoreActualOT . "'>";
//print "<html><title>Approve Overtime (Details)</title>";
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
        header("Content-Disposition: attachment; filename=ApproveOvertime.xls");
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
                }else{
                    print "<center><font face='Verdana' size='1'><b>Selected Options</b></font></center>";
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
                if ($prints != "yes") {
                    print "<div class='mb-3'><label class='form-label'><font face='Verdana' size='2'>Day:</font></label>";
                    print "<select name='lstDay' class='form-select select2 shadow-none'><option selected value='" . $lstDay . "'>" . $lstDay . "</option> <option value='Sunday'>Sunday</option> <option value='Monday'>Monday</option> <option value='Tuesday'>Tuesday</option> <option value='Wednesday'>Wednesday</option> <option value='Thursday'>Thursday</option> <option value='Friday'>Friday</option> <option value='Saturday'>Saturday</option></select>";
                    print "</div>";
                } else {
                    displayTextbox("lstDay", "Day: ", $lstDay, $prints, 12, "", "");
                }
                ?>
            </div>
            <div class="col-2">
                <?php 
                displayTextbox("txtFrom", "Date From <font size='1'>(DD/MM/YYYY)</font>: ", $txtFrom, $prints, 12, "", "");
                ?>
            </div>
            <div class="col-2">
                <?php 
                    displayTextbox("txtTo", "Date To <font size='1'>(DD/MM/YYYY)</font>: ", $txtTo, $prints, 12, "25%", "25%");
                ?>
            </div>
            <div class="col-2">
                <?php 
                    if ($prints != "yes") {
                        print "<div class='mb-3'><label class='form-label'><font face='Verdana' size='2'>Work Type:</font></label>";
                        print "<select name='lstType' class='form-select select2 shadow-none'><option selected value='" . $lstType . "'>" . $lstType . "</option> <option value='Early In'>Early In</option> <option value='Late In'>Late In</option> <option value='Less Break'>Less Break</option> <option value='More Break'>More Break</option> <option value='Early Out'>Early Out</option> <option value='Late Out'>Late Out</option> <option value='Grace'>Grace</option> <option value='OT'>OT</option> <option value='Approved OT'>Approved OT</option> </select>";
                        print "</div>";
                    } else {
                        displayTextbox("lstType", "Work Type: ", $lstType, $prints, 12, "", "");
                    }
                ?>
            </div>
                <?php 
                if ($prints != "yes") {
                    if (strpos($userlevel, $current_module . "D") !== false) {
                        print "<input type='hidden' name='txhDeleteRight' value='1'>";
                    } else {
                        print "<input type='hidden' name='txhDeleteRight' value='0'>";
                    }                    
                    print "<div class='col-2'>";
                    displayTextbox("txtARemark", "Attendance Remark: ", $txtARemark, $prints, 12, "", "");
                    print "</div>";
                    print "<div class='col-2'>";
                    if ($prints != "yes") {
                        displayColourFlag($conn, $lstColourFlag, "lstColourFlag", true, true);
                    } else {
                        displayTextbox("lstColourFlag", "Flag: ", $lstColourFlag, "yes", 1, "", "");
                    }
                    print "</div>";
                    print "</div>";
            //        print "<tr><td colspan='4' width='100%'><img src='img/cBar.gif' width='100%' height='3'></td></tr>";
                    print "<div class='row'>";
                    print "<div class='col-2'>";
                    displayEmployeeStatus($conn, "lstEmployeeStatus", $lstEmployeeStatus, "", "");
                    print "</div>";
                    print "<div class='col-2'>";
                    $array = array(array("tuser.id, AttendanceMaster.ADate", "Employee Code"), array("tuser.name, tuser.id, AttendanceMaster.ADate", "Employee Name - Code"), array("tuser.dept, tuser.id, AttendanceMaster.ADate", "Dept - Code"), array("tuser.company, tuser.dept, tuser.id, AttendanceMaster.ADate", "Div/Desg - Dept - Code"), array("tuser.company, tuser.dept, tuser.group_id, tuser.id, AttendanceMaster.ADate", "Div - Dept - Current Shift - Code"), array("AttendanceMaster.ADate", "Date"), array("AttendanceMaster.Day, tuser.id, AttendanceMaster.ADate", "Day"), array("AttendanceMaster.Week, tuser.id, AttendanceMaster.ADate", "Week"));
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
    print "</div></div></div>";
if ($act == "searchRecord") {
    //AND emprequest.userid=tuser.id 
    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, AttendanceMaster.ADate, AttendanceMaster.Day, AttendanceMaster.Week, AttendanceMaster.EarlyIn, AttendanceMaster.LateIn, AttendanceMaster.Break, AttendanceMaster.LessBreak, AttendanceMaster.MoreBreak, AttendanceMaster.EarlyOut, AttendanceMaster.LateOut, AttendanceMaster.Normal, AttendanceMaster.Grace, AttendanceMaster.Overtime, AttendanceMaster.AOvertime, AttendanceMaster.AttendanceID, tuser.idno, tuser.remark, AttendanceMaster.LateIn_flag, AttendanceMaster.EarlyOut_flag, AttendanceMaster.MoreBreak_flag, AttendanceMaster.Remark, AttendanceMaster.LateInColumn FROM tuser, tgroup, AttendanceMaster  WHERE AttendanceMaster.group_id = tgroup.id AND AttendanceMaster.Flag NOT LIKE 'Delete' AND AttendanceMaster.EmployeeID = tuser.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
    if ($lstShift != "") {
        $query = $query . " AND tgroup.id = " . $lstShift;
    }
    if ($txtARemark != "") {
        $query = $query . " AND AttendanceMaster.Remark LIKE '%" . $txtARemark . "%'";
    }
    $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
    if ($lstDay != "") {
        $query = $query . " AND AttendanceMaster.Day = '" . $lstDay . "'";
    }
    if ($txtFrom != "") {
        $query = $query . " AND AttendanceMaster.ADate >= " . insertDate($txtFrom);
    }
    if ($txtTo != "") {
        $query = $query . " AND AttendanceMaster.ADate <= " . insertDate($txtTo);
    }
    if ($lstType != "") {
        if ($lstType == "Early In") {
            $query = $query . " AND AttendanceMaster.EarlyIn > 0 ";
        } else {
            if ($lstType == "Late In") {
                $query = $query . " AND AttendanceMaster.LateIn > 0 ";
            } else {
                if ($lstType == "Less Break") {
                    $query = $query . " AND AttendanceMaster.LessBreak > 0 ";
                } else {
                    if ($lstType == "More Break") {
                        $query = $query . " AND AttendanceMaster.MoreBreak > 0 ";
                    } else {
                        if ($lstType == "Early Out") {
                            $query = $query . " AND AttendanceMaster.EarlyOut > 0 ";
                        } else {
                            if ($lstType == "Late Out") {
                                $query = $query . " AND AttendanceMaster.LateOut > 0 ";
                            } else {
                                if ($lstType == "Grace") {
                                    $query = $query . " AND AttendanceMaster.Grace > 0 ";
                                } else {
                                    if ($lstType == "OT") {
                                        $query = $query . " AND AttendanceMaster.Overtime > 0 ";
                                    } else {
                                        if ($lstType == "Approved OT") {
                                            $query = $query . " AND AttendanceMaster.AOvertime > 0 ";
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    if ($lstColourFlag != "") {
        $query = $query . " AND AttendanceMaster.Flag = '" . $lstColourFlag . "'";
    }
    $query = $query . employeeStatusQuery($lstEmployeeStatus);
    if ($lstSort != "") {
        $query = $query . " ORDER BY " . $lstSort;
    } else {
        $query = $query . " ORDER BY tuser.id, AttendanceMaster.ADate";
    }
    print '<div class="row"><div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12"><div class="card"><div class="card-body table-responsive">';
    if ($prints != "yes") {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800' class='table table-striped table-bordered dataTable' >";
    } else {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%' class='table table-striped table-bordered dataTable'>";
    }
    print "<thead><tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Shift</font></td> <td><font face='Verdana' size='2'>Date</font></td> <td><font face='Verdana' size='2'>Day</font></td><td><font face='Verdana' size='2'>Week</font></td> <td><font face='Verdana' size='2'>Early In</font></td> <td><font face='Verdana' size='2'>Late In</font></td> <td><font face='Verdana' size='2'>Break</font></td> <td><font face='Verdana' size='2'>Less Break</font></td> <td><font face='Verdana' size='2'>More Break</font></td> <td><font face='Verdana' size='2'>Early Out</font></td> <td><font face='Verdana' size='2'>Late Out</font></td> <td><font face='Verdana' size='2'>Grace</font></td> <td><font face='Verdana' size='2'>Normal</font></td> <td><font face='Verdana' size='2'>OT</font></td> <td><font face='Verdana' size='2'>App OT</font></td>";
    if (getRegister(encryptDecrypt($txtMACAddress), 7) == "25") {
        print "<td><font face='Verdana' size='2'>Late Out</font></td>";
        print "<td><font face='Verdana' size='2'>APP Late Out</font></td>";
    } else {
        if ($prints != "yes") {
            print "<td><font face='Verdana' size='2'><input type='checkbox' name='chkAOT' onClick='approveAll(this)'></font></td>";
        } else {
            print "<td><font face='Verdana' size='2'>&nbsp;</font></td>";
        }
    }
    print "<td><a name='copyRemarkAll' href='#copyRemarkAll' onClick='javascript:copyRemarkAll()' title='Click Here to COPY the Remark from FIRST Row to all the below BLANK Remarks'><font face='Verdana' size='2'>A Remark</font></a></td></tr>";
    print "<tr><td><font face='Verdana' size='2'>&nbsp;</font></td><td><font face='Verdana' size='2'>&nbsp;</font></td><td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>&nbsp;</font></td> <td><font face='Verdana' size='2'>Min</font></td> <td><font face='Verdana' size='2'>Min</font></td> <td><font face='Verdana' size='2'>Min</font></td> <td><font face='Verdana' size='2'>Min</font></td> <td><font face='Verdana' size='2'>Min</font></td> <td><font face='Verdana' size='2'>Min</font></td> <td><font face='Verdana' size='2'>Min</font></td> <td><font face='Verdana' size='2'>Min</font></td> <td><font face='Verdana' size='2'>Hrs</font></td> <td><font face='Verdana' size='2'>Min</font></td> <td><font face='Verdana' size='2'>Min</font></td>";
    if (getRegister(encryptDecrypt($txtMACAddress), 7) == "25") {
        print "<td><font face='Verdana' size='2'>Min</font></td>";
        print "<td><font face='Verdana' size='2'>Min</font></td>";
    } else {
        print "<td><font face='Verdana' size='2'>&nbsp;</font></td>";
    }
    print "<td><a name='resetRemarkAll' href='#resetRemarkAll' onClick='javascript:resetRemarkAll()' title='Click Here to RESET the Remark of all Rows'><font face='Verdana' size='1'>Reset</font></a></td></tr>";
    print "</thead>";
    $result = mysqli_query($conn, $query);
    for ($count = 0; $cur = mysqli_fetch_row($result); $count++) {
        if ($cur[3] == "") {
            $cur[3] = "&nbsp;";
        }
        if ($cur[20] == "") {
            $cur[20] = "&nbsp;";
        }
        if ($cur[21] == "") {
            $cur[21] = "&nbsp;";
        }
        addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
        displayDate($cur[5]);
        round($cur[8] / 60, 2);
        print "<tr><td><a title='ID'><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'><font face='Verdana' size='1'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $cur[1] . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $cur[20] . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $cur[2] . "</font></a></td> <td><a title='Div/Desg'><font face='Verdana' size='1'>" . $cur[3] . "</font></a></td> <td><a title='Rmk'><font face='Verdana' size='1'>" . $cur[21] . "</font></a></td> <td><a title='Shift'><font face='Verdana' size='1'>" . $cur[4] . "</font></a></td> <td><a title='Date'><input type='hidden' name='txhDate" . $count . "' value='" . $cur[5] . "'><font face='Verdana' size='1'>" . displayDate($cur[5]) . "</font></a></td> <td><a title='Day'><font face='Verdana' size='1'>" . $cur[6] . "</font></a></td><td><a title='Week'><font face='Verdana' size='1'>" . $cur[7] . "</font></a></td> <td><a title='Early In (Min)'><font face='Verdana' size='1'>" . round($cur[8] / 60, 2) . "</font></a></td>";
        if ($cur[22] == 0) {
            round($cur[9] / 60, 2);
            print "<td><a title='Late In (Min)'><font face='Verdana' size='1'>" . round($cur[9] / 60, 2) . "</font></a></td>";
        } else {
            round($cur[9] / 60, 2);
            print "<td><a title='Late In (Min)'><font face='Verdana' size='1' color='#FF0000'><strike>" . round($cur[9] / 60, 2) . "</strike></font></a></td>";
        }
        round($cur[10] / 60, 2);
        round($cur[11] / 60, 2);
        print "<td><a title='Break (Min)'><font face='Verdana' size='1'>" . round($cur[10] / 60, 2) . "</font></a></td> <td><a title='Less Break (Min)'><font face='Verdana' size='1'>" . round($cur[11] / 60, 2) . "</font></a></td>";
        if ($cur[24] == 0) {
            round($cur[12] / 60, 2);
            print "<td><a title='More Break (Min)'><font face='Verdana' size='1'>" . round($cur[12] / 60, 2) . "</font></a></td>";
        } else {
            round($cur[12] / 60, 2);
            print "<td><a title='More Break (Min)'><font face='Verdana' size='1' color='#FF0000'><strike>" . round($cur[12] / 60, 2) . "</strike></font></a></td>";
        }
        if ($cur[23] == 0) {
            round($cur[13] / 60, 2);
            print "<td><a title='Early Out (Min)'><font face='Verdana' size='1'>" . round($cur[13] / 60, 2) . "</font></a></td>";
        } else {
            round($cur[13] / 60, 2);
            print "<td><a title='Early Out (Min)'><font face='Verdana' size='1' color='#FF0000'><strike>" . round($cur[13] / 60, 2) . "</strike></font></a></td>";
        }
        round($cur[14] / 60, 2);
        round($cur[16] / 60, 2);
        round($cur[15] / 3600, 2);
        round($cur[17] / 60, 2);
        round($cur[17] / 60, 2);
        print "<td><a title='Late Out (Min)'><font face='Verdana' size='1'>" . round($cur[14] / 60, 2) . "</font></a></td> <td><a title='Grace (Min)'><font face='Verdana' size='1'>" . round($cur[16] / 60, 2) . "</font></a></td> <td><a title='Normal (Hrs)'><font face='Verdana' size='1'>" . round($cur[15] / 3600, 2) . "</font></a></td> <td><a title='OT (Min)'><font face='Verdana' size='1'>" . round($cur[17] / 60, 2) . "</font><input type='hidden' name='txhAOT" . $count . "' id='txhAOT" . $count . "' value='" . round($cur[17] / 60, 2) . "'></td>";
        if ($prints != "yes") {
            if (getRegister(encryptDecrypt($txtMACAddress), 7) == "25") {
                round($cur[18] / 60, 2);
                round($cur[18] / 60, 2);
                print "<td><a title='Approved OT (Min)'><font face='Verdana' size='1'>" . round($cur[18] / 60, 2) . "</font></a><input type='hidden' id='txtAOT" . $count . "' name='txtAOT" . $count . "' value='" . round($cur[18] / 60, 2) . "'></td>";
                $query = "SELECT DayMaster.Start, DayMaster.Close, tgroup.NightFlag FROM DayMaster, tgroup WHERE DayMaster.group_id = tgroup.id AND tgroup.ScheduleID = 7 AND DayMaster.Start > '030000' AND DayMaster.Start < '120000' AND DayMaster.TDate = " . $cur[5] . " AND DayMaster.e_id = " . $cur[0];
                $bhojraj_result = selectData($conn, $query);
                $a = mktime(substr($bhojraj_result[1], 0, 2), substr($bhojraj_result[1], 2, 2), substr($bhojraj_result[1], 4, 2), substr($bhojraj_result[2], 4, 2), substr($bhojraj_result[2], 6, 2), substr($bhojraj_result[2], 0, 4));
                $b = mktime("18", "30", "00", substr($bhojraj_result[2], 4, 2), substr($bhojraj_result[2], 6, 2), substr($bhojraj_result[2], 0, 4));
                $c = $a - $b;
                if (0 < $c) {
                    round($c / 60, 2);
                    print "<td><a title='Late Out (Min)'><font face='Verdana' size='1'>" . round($c / 60, 2) . "</font></a></td>";
                } else {
                    $query = "SELECT DayMaster.Start, DayMaster.Close, tgroup.NightFlag FROM DayMaster, tgroup WHERE DayMaster.group_id = tgroup.id AND tgroup.ScheduleID = 7 AND DayMaster.Start > '150000' && DayMaster.Start < '235959' AND DayMaster.TDate = " . $cur[5] . " AND DayMaster.e_id = " . $cur[0];
                    $bhojraj_result = selectData($conn, $query);
                    $a = mktime(substr($bhojraj_result[1], 0, 2), substr($bhojraj_result[1], 2, 2), substr($bhojraj_result[1], 4, 2), substr($bhojraj_result[2], 4, 2), substr($bhojraj_result[2], 6, 2), substr($bhojraj_result[2], 0, 4));
                    $b = mktime("07", "00", "00", substr($bhojraj_result[2], 4, 2), substr($bhojraj_result[2], 6, 2), substr($bhojraj_result[2], 0, 4));
                    $c = $a - $b;
                    if (0 < $c) {
                        round($c / 60, 2);
                        print "<td><a title='Late Out (Min)'><font face='Verdana' size='1'>" . round($c / 60, 2) . "</font></a></td>";
                    } else {
                        print "<td><a title='Late Out (Min)'><font face='Verdana' size='1'>0</font></a></td>";
                    }
                }
                round($cur[26] / 60, 2);
                print "<td><input size='5' name='txtLateOut" . $count . "' id='txtLateOut" . $count . "' value='" . round($cur[26] / 60, 2) . "' class='form-control-inner'> <input type='hidden' name='txhID" . $count . "' value='" . $cur[19] . "'></td>";
                print "<td><input type='hidden' name='txhARemark" . $count . "' value='" . $cur[25] . "'><input size='12' name='txtARemark" . $count . "' id='txtARemark" . $count . "' value='" . $cur[25] . "' class='form-control-inner'></td>";
            } else {
                round($cur[18] / 60, 2);
                round($cur[18] / 60, 2);
                print "<td><a title='Approved OT (Min)'><input type='hidden' id='txhOldAOT" . $count . "' name='txhOldAOT" . $count . "' value='" . round($cur[18] / 60, 2) . "'><input size='5' name='txtAOT" . $count . "' id='txtAOT" . $count . "' value='" . round($cur[18] / 60, 2) . "' onBlur='javascript:checkOTValue(this, document.frm1.txhAOT" . $count . ", document.frm1.txhOldAOT" . $count . ")' class='form-controls'></td> <td><input type='checkbox' name='chkAOT" . $count . "' id='chkAOT" . $count . "' onClick='javascript:approveOT(document.frm1.chkAOT" . $count . ", document.frm1.txtAOT" . $count . ", document.frm1.txhAOT" . $count . ", document.frm1.txhOldAOT" . $count . ")'> <input type='hidden' name='txhID" . $count . "' value='" . $cur[19] . "'></a></td><td><input type='hidden' name='txhARemark" . $count . "' value='" . $cur[25] . "'><input size='12' name='txtARemark" . $count . "' id='txtARemark" . $count . "' value='" . $cur[25] . "' class='form-controls'></td>";
            }
        } else {
            if (getRegister(encryptDecrypt($txtMACAddress), 7) == "25") {
                round($cur[18] / 60, 2);
                print "<td><font face='Verdana' size='1'>" . round($cur[18] / 60, 2) . "</td>";
                $query = "SELECT DayMaster.Start, DayMaster.Close, tgroup.NightFlag FROM DayMaster, tgroup WHERE DayMaster.group_id = tgroup.id AND tgroup.ScheduleID = 7 AND DayMaster.Start > '030000' AND DayMaster.Start < '120000' AND DayMaster.TDate = " . $cur[5] . " AND DayMaster.e_id = " . $cur[0];
                $bhojraj_result = selectData($conn, $query);
                $a = mktime(substr($bhojraj_result[1], 0, 2), substr($bhojraj_result[1], 2, 2), substr($bhojraj_result[1], 4, 2), substr($bhojraj_result[2], 4, 2), substr($bhojraj_result[2], 6, 2), substr($bhojraj_result[2], 0, 4));
                $b = mktime("18", "30", "00", substr($bhojraj_result[2], 4, 2), substr($bhojraj_result[2], 6, 2), substr($bhojraj_result[2], 0, 4));
                $c = $a - $b;
                if (0 < $c) {
                    round($c / 60, 2);
                    print "<td><a title='Late Out (Min)'><font face='Verdana' size='1'>" . round($c / 60, 2) . "</font></a></td>";
                } else {
                    $query = "SELECT DayMaster.Start, DayMaster.Close, tgroup.NightFlag FROM DayMaster, tgroup WHERE DayMaster.group_id = tgroup.id AND tgroup.ScheduleID = 7 AND DayMaster.Start > '150000' && DayMaster.Start < '235959' AND DayMaster.TDate = " . $cur[5] . " AND DayMaster.e_id = " . $cur[0];
                    $bhojraj_result = selectData($conn, $query);
                    $a = mktime(substr($bhojraj_result[1], 0, 2), substr($bhojraj_result[1], 2, 2), substr($bhojraj_result[1], 4, 2), substr($bhojraj_result[2], 4, 2), substr($bhojraj_result[2], 6, 2), substr($bhojraj_result[2], 0, 4));
                    $b = mktime("07", "00", "00", substr($bhojraj_result[2], 4, 2), substr($bhojraj_result[2], 6, 2), substr($bhojraj_result[2], 0, 4));
                    $c = $a - $b;
                    if (0 < $c) {
                        round($c / 60, 2);
                        print "<td><a title='Late Out (Min)'><font face='Verdana' size='1'>" . round($c / 60, 2) . "</font></a></td>";
                    } else {
                        print "<td><a title='Late Out (Min)'><font face='Verdana' size='1'>0</font></a></td>";
                    }
                }
                round($cur[26] / 60, 2);
                print "<td><font size='1'>" . round($cur[26] / 60, 2) . "</font> <input type='hidden' name='txhID" . $count . "' value='" . $cur[19] . "'></td> <td><font size='1'>" . $cur[25] . "</font></td>";
            } else {
                round($cur[18] / 60, 2);
                print "<td><font face='Verdana' size='1'>" . round($cur[18] / 60, 2) . "</td> <td><font size='1'>&nbsp;</font> <input type='hidden' name='txhID" . $count . "' value='" . $cur[19] . "'></td> <td><font size='1'>" . $cur[25] . "</font></td>";
            }
        }
        print "</tr>";
    }
    print "</table>";
    if ($excel != "yes") {
        print "<p align='center'><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $count . "</b></font><input type='hidden' name='txtCount' value='" . $count . "'>";
        if ($prints != "yes" && strpos($userlevel, $current_module . "E") !== false && $_SESSION[$session_variable . "LockDate"] < insertDate($txtFrom) && $_SESSION[$session_variable . "LockDate"] < insertDate($txtTo)) {
            print "<br><br><input name='btSubmit' type='button' value='Save Changes' onClick='submitRecord()' class='btn btn-primary'>";
        }
        if ($prints != "yes") {
            print "<br><br><input type='button' value='Print Report' onClick='checkPrint(0)' class='btn btn-primary'>&nbsp;&nbsp;<input type='button' value='Excel' onClick='checkPrint(1)' class='btn btn-primary'>";
        }
        print "</p>";
    }
}
print "</form>";
echo "\r\n<script>\r\nfunction submitRecord(){\r\n\tx = document.frm1;\r\n\tx.act.value='editRecord';\r\n\tx.submit();\r\n}\r\n\r\nfunction approveOT(x, y, z, zz){\t\r\n\tif (x.checked == true){\r\n\t\tif (y.value == 0 || y.value == ''){\r\n\t\t\tif (document.frm1.txhDeleteRight.value == 0 && document.frm1.lstIgnoreActualOT.value == \"Yes\"  && z.value*1 > zz.value*1){\r\n\t\t\t\talert('Approved OT value has to be LESS THAN Approved OT value');\r\n\t\t\t\tx.checked = false;\r\n\t\t\t}else if (document.frm1.txhDeleteRight.value == 0 && document.frm1.lstIgnoreActualOT.value == \"No\" && (z.value*1 > y.value*1 || z.value*1 > zz.value*1)){\r\n\t\t\t\talert('Approved OT value has to be LESS THAN OT/ Approved OT value');\r\n\t\t\t\tx.checked = false;\r\n\t\t\t}else{\r\n\t\t\t\ty.value = z.value;\r\n\t\t\t}\r\n\t\t}\r\n\t}else{\r\n\t\ty.value = 0;\t\r\n\t}\r\n}\r\n\r\nfunction checkOTValue(x, y, z){\t\r\n\tif (x.value == '' || x.value*1 != x.value/1 || x.value*1 > 1440){\r\n\t\talert('Please enter a valid Approved OT Value');\r\n\t\tx.focus();\r\n\t\treturn;\r\n\t}else if (document.frm1.txhDeleteRight.value == 0 && document.frm1.lstIgnoreActualOT.value == \"Yes\"  && x.value*1 > z.value*1){\r\n\t\talert('Approved OT value has to be LESS THAN Approved OT value');\r\n\t\tx.focus();\r\n\t\treturn;\r\n\t}else if (document.frm1.txhDeleteRight.value == 0 && document.frm1.lstIgnoreActualOT.value == \"No\"  && (x.value*1 > y.value*1 || x.value*1 > z.value*1)){\r\n\t\talert('Approved OT value has to be LESS THAN OT/ Approved OT value');\r\n\t\tx.focus();\r\n\t\treturn;\r\n\t}\r\n}\r\n\r\nfunction approveAll(y){\t\r\n\tx = document.frm1;\r\n\tz = x.txtCount.value;\r\n\tx.btSearch.disabled = true;\r\n\tx.btSubmit.disabled = true;\r\n\ty.disabled = true;\r\n\tfor (i=0;i<z;i++){\t\t\r\n\t\tif (y.checked == true){\t\t\t\r\n\t\t\tdocument.getElementById(\"chkAOT\"+i).checked = true;\r\n\t\t\tif (document.getElementById(\"txtAOT\"+i).value == 0 || document.getElementById(\"txtAOT\"+i).value == ''){\r\n\t\t\t\tif (document.frm1.txhDeleteRight.value == 0){\r\n\t\t\t\t\t//Do Nothing\r\n\t\t\t\t}else{\r\n\t\t\t\t\tdocument.getElementById(\"txtAOT\"+i).value = document.getElementById(\"txhAOT\"+i).value;\r\n\t\t\t\t}\r\n\t\t\t}\r\n\t\t}else{\r\n\t\t\tdocument.getElementById(\"chkAOT\"+i).checked = false;\r\n\t\t\tdocument.getElementById(\"txtAOT\"+i).value = 0;\t\r\n\t\t}\r\n\t}\r\n\tx.btSearch.disabled = false;\r\n\tx.btSubmit.disabled = false;\r\n\ty.disabled = false;\r\n}\r\n\r\nfunction copyRemarkAll(){\r\n\tif (confirm(\"COPY Attendance Remark from FIRST row to all other BLANK Remark Fields\")){\r\n\t\tvar x = document.frm1;\r\n\t\tvar count = x.txtCount.value;\r\n\t\tif (count > 0){\r\n\t\t\tif (x.txtARemark0.value != \"\"){\r\n\t\t\t\tfor (i=0;i<count;i++){\r\n\t\t\t\t\tif (document.getElementById(\"txtARemark\"+i).value == \"\" || document.getElementById(\"txtARemark\"+i).value == \".\"){\r\n\t\t\t\t\t\tdocument.getElementById(\"txtARemark\"+i).value = x.txtARemark0.value;\r\n\t\t\t\t\t}\r\n\t\t\t\t}\r\n\t\t\t}\r\n\t\t}\r\n\t}\r\n}\r\n\r\nfunction resetRemarkAll(){\r\n\tif (confirm(\"Reset All Attendance Remarks\")){\r\n\t\tvar x = document.frm1;\r\n\t\tvar count = x.txtCount.value;\r\n\t\t//alert(count);\r\n\t\tfor (i=0;i<count;i++){\r\n\t\t\tdocument.getElementById(\"txtARemark\"+i).value = \"\";\r\n\t\t}\r\n\t}\t\r\n}\r\n</script>\r\n</center>";
print "</div></div></div></div></div>";
include 'footer.php';

?>