<?php


ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "20";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=ReportDailyClocking.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$act = $_GET["act"];
if ($act == "") {
    $act = $_POST["act"];
}
$prints = $_GET["prints"];
$excel = $_GET["excel"];
$subReport = $_GET["subReport"];
$message = $_GET["message"];
if ($message == "") {
    $message = "Processed Log (Shifts with Daily Routine)";
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
$txtFrom = $_POST["txtFrom"];
if ($txtFrom == "") {
    $txtFrom = $_GET["txtFrom"];
}
$txtTo = $_POST["txtTo"];
if ($txtTo == "") {
    $txtTo = $_GET["txtTo"];
}
if ($txtFrom == "") {
    $txtFrom = displayDate(getLastDay(insertToday(), 1));
}
if ($txtTo == "") {
    $txtTo = displayDate(getLastDay(insertToday(), 1));
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
                            <h4 class="page-title">Processed Log (Shifts with Daily Routine)</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Processed Log (Shifts with Daily Routine)
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

<?php
}
print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='ReportDailyClocking.php'><input type='hidden' name='act' value='searchRecord'>";
//print "<html><title>Processed Log (Shifts with Daily Routine)</title>";
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
        if ($subReport != "yes") {
            header("Content-type: application/x-msdownload");
            header("Content-Disposition: attachment; filename=ReportDailyClocking.xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            print "<body>";
        }
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
                displayEmployeeStatus($conn, "lstEmployeeStatus", $lstEmployeeStatus, "", "");
                print "</div>";
                print "<div class='col-2'>";
                displayColourFlag($conn, $lstColourFlag, "lstColourFlag", true, true);
                print "</div>";
                print "<div class='col-2'>";
                $array = array(array("tuser.id, DayMaster.TDate", "Employee Code"), array("tuser.name, tuser.id, DayMaster.TDate", "Employee Name - Code"), array("tuser.dept, tuser.id, DayMaster.TDate", "Dept - Code"), array("tuser.company, tuser.dept, tuser.id, DayMaster.TDate", "Div/Desg - Dept - Code"), array("tuser.company, tuser.dept, tuser.group_id, tuser.id, DayMaster.TDate", "Div - Dept - Shift - Code"));
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
    </div>
</div>
<?php
 } 
print "</div></div></div></div>";

if ($act == "searchRecord") {
    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, DayMaster.TDate, DayMaster.Entry, DayMaster.Start, DayMaster.BreakOut, DayMaster.BreakIn, DayMaster.Close, DayMaster.Exit, DayMaster.Flag, tuser.idno, tuser.remark, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, tgroup, DayMaster WHERE DayMaster.group_id = tgroup.id AND DayMaster.e_id = tuser.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
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
    } else {
        $query = $query . " ORDER BY tuser.id, DayMaster.TDate";
    }
    if ($excel != "yes") {
        print "<p align='center'><font face='Verdana' size='1'><b>" . $_SESSION[$session_variable . "FlagReportText"] . "</b></font></p>";
    }
    print '<div class="row"><div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12"><div class="card"><div class="card-body table-responsive">';
    if ($prints != "yes") {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800' class='table table-striped table-bordered dataTable' id='zero_config'>";
    } else {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%' class='table table-striped table-bordered dataTable'>";
    }
    print "<thead><tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Shift</font></td> <td><font face='Verdana' size='2'>Date</font></td> <td><font face='Verdana' size='2'>Entry</font></td> <td><font face='Verdana' size='2'><b>Start</b></font></td> <td><font face='Verdana' size='2'>BreakOut</font></td> <td><font face='Verdana' size='2'>BreakIn</font></td> <td><font face='Verdana' size='2'><b>Close</b></font></td> <td><font face='Verdana' size='2'>Exit</font></td> <td><font face='Verdana' size='2'>Flag</font></td></tr></thead>";
    $result = mysqli_query($conn, $query);
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
        if ($cur[13] == "") {
            $cur[13] = "&nbsp;";
        }
        if ($cur[14] == "") {
            $cur[14] = "&nbsp;";
        }
        addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
        displayDate($cur[5]);
        print "<tr><td bgcolor='" . $bgcolor . "'><a title='ID'><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'><font face='Verdana' size='1' color='" . $font . "'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Name'><font face='Verdana' size='1' color='" . $font . "'>" . $cur[1] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1' color='" . $font . "'>" . $cur[13] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Dept'><font face='Verdana' size='1' color='" . $font . "'>" . $cur[2] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Div/Desg'><font face='Verdana' size='1' color='" . $font . "'>" . $cur[3] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Rmk'><font face='Verdana' size='1' color='" . $font . "'>" . $cur[14] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Shift'><font face='Verdana' size='1' color='" . $font . "'>" . $cur[4] . "</font></a></td> <td bgcolor='" . $bgcolor . "'><a title='Date'><font face='Verdana' size='1' color='" . $font . "'>" . displayDate($cur[5]) . "</font></a></td>";
        if ($cur[6] != $cur[7]) {
            displayVirdiTime($cur[6]);
            print "<td bgcolor='" . $bgcolor . "'><a title='Entry'><font face='Verdana' size='1' color='" . $font . "'>" . displayVirdiTime($cur[6]) . "</font></a></td>";
        } else {
            print "<td bgcolor='" . $bgcolor . "'><a title='Entry'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
        }
        displayVirdiTime($cur[7]);
        print "<td bgcolor='" . $bgcolor . "'><a title='Start'><font face='Verdana' size='1' color='" . $font . "'><b>" . displayVirdiTime($cur[7]) . "</b></font></a></td>";
        if ($cur[8] != $cur[7]) {
            displayVirdiTime($cur[8]);
            print "<td bgcolor='" . $bgcolor . "'><a title='Break Out'><font face='Verdana' size='1' color='" . $font . "'>" . displayVirdiTime($cur[8]) . "</font></a></td>";
            displayVirdiTime($cur[9]);
            print "<td bgcolor='" . $bgcolor . "'><a title='Break In'><font face='Verdana' size='1' color='" . $font . "'>" . displayVirdiTime($cur[9]) . "</font></a></td>";
        } else {
            print "<td bgcolor='" . $bgcolor . "'><a title='Break Out'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
            print "<td bgcolor='" . $bgcolor . "'><a title='Break In'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
        }
        displayVirdiTime($cur[10]);
        print "<td bgcolor='" . $bgcolor . "'><a title='Close'><font face='Verdana' size='1' color='" . $font . "'><b>" . displayVirdiTime($cur[10]) . "</b></font></a></td>";
        if ($cur[11] != $cur[10]) {
            displayVirdiTime($cur[11]);
            print "<td bgcolor='" . $bgcolor . "'><a title='Exit'><font face='Verdana' size='1' color='" . $font . "'>" . displayVirdiTime($cur[11]) . "</font></a></td>";
        } else {
            print "<td bgcolor='" . $bgcolor . "'><a title='Exit'><font face='Verdana' size='1' color='" . $font . "'>&nbsp;</font></a></td>";
        }
        print "<td bgcolor='" . $bgcolor . "'><a title='Flag'><font face='Verdana' size='1' color='" . $font . "'>" . $cur[12] . "</font></a></td></tr>";
    }
    print "</table>";
    if ($excel != "yes") {
        print "<p align='center'><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $count . "</b></font>";
        if ($prints != "yes") {
            print "<br><input type='button' value='Print Report' class='btn btn-primary' onClick='checkPrint(0)'>&nbsp;&nbsp;<input type='button' value='Excel' class='btn btn-primary' onClick='checkPrint(1)'>";
        }
        print "</p>";
    }
}
print "</form>";
print "</div></div></div></div></div>";
include 'footer.php';

?>