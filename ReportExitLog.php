<?php


ob_start("ob_gzhandler");
set_time_limit(0);
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "20";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=ReportExitLog.php&message=Session Expired or Security Policy Violated");
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
$csv = $_GET["csv"];
$message = $_GET["message"];
if ($message == "") {
    $message = "Exit Log Report";
}
$lstShift = $_POST["lstShift"];
$lstDepartment = $_POST["lstDepartment"];
$lstDivision = $_POST["lstDivision"];
$lstTerminal = $_POST["lstTerminal"];
$lstEmployeeIDFrom = $_POST["lstEmployeeIDFrom"];
$lstEmployeeIDTo = $_POST["lstEmployeeIDTo"];
$txtEmployeeCode = $_POST["txtEmployeeCode"];
if ($txtEmployeeCode == "") {
    $txtEmployeeCode = $_GET["txtEmployeeCode"];
}
$lstSort = $_POST["lstSort"];
$txtEmployee = $_POST["txtEmployee"];
$txtFrom = $_POST["txtFrom"];
$txtTo = $_POST["txtTo"];
if ($txtFrom == "") {
    $txtFrom = $_GET["txtFrom"];
}
if ($txtTo == "") {
    $txtTo = $_GET["txtTo"];
}
if ($txtFrom == "") {
    $txtFrom = displayToday();
}
if ($txtTo == "") {
    $txtTo = displayToday();
}
$txtTimeFrom = $_POST["txtTimeFrom"];
if ($txtTimeFrom == "") {
    $txtTimeFrom = "000000";
}
$txtMACAddress = $_SESSION[$session_variable . "MACAddress"];
$txtTimeTo = $_POST["txtTimeTo"];
if ($txtMACAddress == "D0-67-E5-E9-86-6A") {
    if ($txtTimeTo == "") {
        $txtTimeTo = getNow() . "00";
    }
} else {
    if ($txtTimeTo == "") {
        $txtTimeTo = "235959";
    }
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
if ($prints != "yes") {
    include 'header.php';
?>
<div class="page-breadcrumb">
                    <div class="row">
                        <div class="col-12 d-flex no-block align-items-center">
                            <h4 class="page-title">Report Exit Log</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Report Exit Log
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

<?php
}
print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='ReportExitLog.php'><input type='hidden' name='act' value='searchRecord'>";
if ($prints != "yes") {
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
        header("Content-Disposition: attachment; filename=ExitLog.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        print "<body>";
    }
}
if (isset($_POST["lstEmployeeStatus"])) {
    $lstEmployeeStatus = $_POST["lstEmployeeStatus"];
} else {
    $lstEmployeeStatus = "ACT";
}
$lstDB = $_POST["lstDB"];
if ($lstDB == "") {
    $lstDB = "Live";
}
$lstGS = $_POST["lstGS"];
if ($lstGS == "") {
    $lstDB = $_SESSION[$session_variable . "RemarkColumnName"];
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
            <div class="col-2">
                <?php 
                $query = "SELECT id, name from tgate";
                displayList("lstTerminal", "Terminal: ", $lstTerminal, $prints, $conn, $query, "", "", "");
                ?>
            </div>
            <div class="col-2">
                <?php 
                displayTextbox("txtFrom", "Date From <font size='1'>(DD/MM/YYYY)</font>: ", $txtFrom, $prints, 12, "", "");
                ?>
            </div>
            <div class="col-2">
                <?php 
                displayTextbox("txtTimeFrom", "Time From (HHMMSS): ", $txtTimeFrom, $prints, 7, "", "");
                ?>
            </div>
            <div class="col-2">
                <?php 
                displayTextbox("txtTo", "Date To <font size='1'>(DD/MM/YYYY)</font>: ", $txtTo, $prints, 12, "", "");
                ?>
            </div>
            <div class="col-2">
                <?php 
                displayTextbox("txtTimeTo", "Time To (HHMMSS): ", $txtTimeTo, $prints, 7, "", "");
                ?>
            </div>
            
        <?php 
        if ($prints != "yes") {
            print "<div class='col-2'>";
            print "<label class='form-label'>DB:</label><select name='lstDB' class='form-control'><option selected value='" . $lstDB . "'>" . $lstDB . "</option> <option value='Live'>Live</option> <option value='Archive'>Archive</option> </select>";
            print "</div>";
            print "</div>";
            print "<div class='row'>";
            print "<div class='col-2'>";
            print "<label class='form-label'>Group By:</label><select name='lstGS' class='form-control'><option selected value='" . $lstGS . "'>" . $lstGS . "</option> <option value='Dept'>Dept</option> <option value='" . $_SESSION[$session_variable . "DivColumnName"] . "'>" . $_SESSION[$session_variable . "DivColumnName"] . "</option> <option value='" . $_SESSION[$session_variable . "RemarkColumnName"] . "'>" . $_SESSION[$session_variable . "RemarkColumnName"] . "</option> <option value='" . $_SESSION[$session_variable . "IDColumnName"] . "'>" . $_SESSION[$session_variable . "IDColumnName"] . "</option> <option value='" . $_SESSION[$session_variable . "PhoneColumnName"] . "'>" . $_SESSION[$session_variable . "PhoneColumnName"] . "</option></select>";
            print "</div>";
            print "<div class='col-2'>";
            displayEmployeeStatus($conn, "lstEmployeeStatus", $lstEmployeeStatus, "", "");
            print "</div>";
            print "<div class='row'>";
            print "<div class='col-12'>";
            print "<center><br><input name='btSearch' type='submit' value='Search Record' class='btn btn-primary'></center>";
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
    $table_name = "Access.tenter";
    if ($lstDB == "Archive") {
        $table_name = "AccessArchive.archive_tenter";
    }
    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tuser.idno, tuser.remark, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10, tuser.phone FROM tuser, tgroup WHERE tuser.group_id = tgroup.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
    if ($lstShift != "") {
        $query = $query . " AND tgroup.id = " . $lstShift;
    }
    $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
    $query = $query . employeeStatusQuery($lstEmployeeStatus);
    if ($lstGS == $_SESSION[$session_variable . "IDColumnName"]) {
        $query = $query . " ORDER BY idno";
    } else {
        if ($lstGS == $_SESSION[$session_variable . "DivColumnName"]) {
            $query = $query . " ORDER BY company";
        } else {
            if ($lstGS == $_SESSION[$session_variable . "RemarkColumnName"]) {
                $query = $query . " ORDER BY remark";
            } else {
                if($lstGS){
                    $query = $query . " ORDER BY " . $lstGS;
                }else{
                    $query = $query;
                }
            }
        }
    }
    print '<div class="row"><div class="col-md-12"><div class="card"><div class="card-body table-responsive">';
    if ($csv != "yes") {
        if ($prints == "yes") {
            print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%' class='table table-striped table-bordered dataTable'>";
        } else {
            print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800' class='table table-striped table-bordered dataTable' id='zero_config'>";
        }
    }
    if ($csv != "yes") {
        print "<thead><tr><td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "DivColumnName"] . "</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "RemarkColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Date</font></td>";
        if (getRegister(encryptDecrypt($txtMACAddress), 7) == "39") {
            print "<td><font face='Verdana' size='2'>Reader OUT</font></td><td><font face='Verdana' size='2'>Time OUT</font></td><td><font face='Verdana' size='2'>Time IN</font></td><td><font face='Verdana' size='2'>Reader IN</font></td>";
        } else {
            print "<td><font face='Verdana' size='2'>Reader IN</font></td><td><font face='Verdana' size='2'>Time IN</font></td><td><font face='Verdana' size='2'>Time OUT</font></td><td><font face='Verdana' size='2'>Reader OUT</font></td>";
        }
        print "</tr></thead>";
    } else {
        if (getRegister(encryptDecrypt($txtMACAddress), 7) == "39") {
            print "ID;Name;" . $_SESSION[$session_variable . "IDColumnName"] . ";Dept;" . $_SESSION[$session_variable . "DivColumnName"] . ";" . $_SESSION[$session_variable . "RemarkColumnName"] . ";Date;Reader OUT;Time OUT;Time IN;Reader IN\n";
        } else {
            print "ID;Name;" . $_SESSION[$session_variable . "IDColumnName"] . ";Dept;" . $_SESSION[$session_variable . "DivColumnName"] . ";" . $_SESSION[$session_variable . "RemarkColumnName"] . ";Date;Reader IN;Time IN;Time OUT;Reader OUT\n";
        }
    }
    $last_data = "";
    $gs_name = [];
    $gs_in = [];
    $gs_out = [];
    $gs_count = 0;
    $gs_in_count = 0;
    $gs_out_count = 0;
    $main_count = 0;
    $main_result = mysqli_query($conn, $query);
    while ($main_cur = mysqli_fetch_row($main_result)) {
        if (0 < mysqli_num_rows($main_result)) {
            if ($main_count == 0) {
                $main_count++;
            } else {
                $main_count++;
                if ($lstGS == "Dept" && $last_data != $main_cur[2] || $lstGS == $_SESSION[$session_variable . "DivColumnName"] && $last_data != $main_cur[3] || $lstGS == $_SESSION[$session_variable . "RemarkColumnName"] && $last_data != $main_cur[6] || $lstGS == $_SESSION[$session_variable . "IDColumnName"] && $last_data != $main_cur[5] || $lstGS == $_SESSION[$session_variable . "PhoneColumnName"] && $last_data != $main_cur[17] || mysqli_num_rows($main_result) == $main_count) {
                    $gs_name[$gs_count] = $last_data;
                    $gs_in[$gs_count] = $gs_in_count;
                    $gs_out[$gs_count] = $gs_out_count;
                    $gs_in_count = 0;
                    $gs_out_count = 0;
                    $gs_count++;
                }
            }
            if ($lstGS == "Dept") {
                $last_data = $main_cur[2];
            } else {
                if ($lstGS == $_SESSION[$session_variable . "DivColumnName"]) {
                    $last_data = $main_cur[3];
                } else {
                    if ($lstGS == $_SESSION[$session_variable . "RemarkColumnName"]) {
                        $last_data = $main_cur[6];
                    } else {
                        if ($lstGS == $_SESSION[$session_variable . "IDColumnName"]) {
                            $last_data = $main_cur[5];
                        } else {
                            if ($lstGS == $_SESSION[$session_variable . "PhoneColumnName"]) {
                                $last_data = $main_cur[17];
                            }
                        }
                    }
                }
            }
            if($lstTerminal){
                $query = "SELECT name from tgate WHERE id = " . $lstTerminal;
            }else{
                $query = "SELECT name from tgate";
            }
            $result = selectData($iconn, $query);
            $t_name = $result[0];
            $t_like = "";
            if (stripos($t_name, "01") !== false) {
                $t_like = " AND tgate.name LIKE '%01%' ";
            } else {
                if (stripos($t_name, "02") !== false) {
                    $t_like = " AND tgate.name LIKE '%02%' ";
                } else {
                    if (stripos($t_name, "03") !== false) {
                        $t_like = " AND tgate.name LIKE '%03%' ";
                    } else {
                        if (stripos($t_name, "04") !== false) {
                            $t_like = " AND tgate.name LIKE '%04%' ";
                        } else {
                            if (stripos($t_name, "05") !== false) {
                                $t_like = " AND tgate.name LIKE '%05%' ";
                            } else {
                                if (stripos($t_name, "06") !== false) {
                                    $t_like = " AND tgate.name LIKE '%06%' ";
                                } else {
                                    if (stripos($t_name, "07") !== false) {
                                        $t_like = " AND tgate.name LIKE '%07%' ";
                                    } else {
                                        if (stripos($t_name, "08") !== false) {
                                            $t_like = " AND tgate.name LIKE '%08%' ";
                                        } else {
                                            if (stripos($t_name, "09") !== false) {
                                                $t_like = " AND tgate.name LIKE '%09%' ";
                                            } else {
                                                if (stripos($t_name, "10") !== false) {
                                                    $t_like = " AND tgate.name LIKE '%10%' ";
                                                } else {
                                                    if (stripos($t_name, "11") !== false) {
                                                        $t_like = " AND tgate.name LIKE '%11%' ";
                                                    } else {
                                                        if (stripos($t_name, "12") !== false) {
                                                            $t_like = " AND tgate.name LIKE '%12%' ";
                                                        } else {
                                                            if (stripos($t_name, "13") !== false) {
                                                                $t_like = " AND tgate.name LIKE '%13%' ";
                                                            } else {
                                                                if (stripos($t_name, "14") !== false) {
                                                                    $t_like = " AND tgate.name LIKE '%14%' ";
                                                                } else {
                                                                    if (stripos($t_name, "15") !== false) {
                                                                        $t_like = " AND tgate.name LIKE '%15%' ";
                                                                    } else {
                                                                        if (stripos($t_name, "16") !== false) {
                                                                            $t_like = " AND tgate.name LIKE '%16%' ";
                                                                        } else {
                                                                            if (stripos($t_name, "17") !== false) {
                                                                                $t_like = " AND tgate.name LIKE '%17%' ";
                                                                            } else {
                                                                                if (stripos($t_name, "18") !== false) {
                                                                                    $t_like = " AND tgate.name LIKE '%18%' ";
                                                                                } else {
                                                                                    if (stripos($t_name, "19") !== false) {
                                                                                        $t_like = " AND tgate.name LIKE '%19%' ";
                                                                                    } else {
                                                                                        if (stripos($t_name, "20") !== false) {
                                                                                            $t_like = " AND tgate.name LIKE '%20%' ";
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
            $table_name = "Access.tenter";
            if ($lstDB == "Archive") {
                $table_name = "AccessArchive.archive_tenter";
            }
            $query = "SELECT e_date, e_time, g_id, tgate.name, e_mode FROM " . $table_name . ", tgate WHERE e_id = '" . $main_cur[0] . "' AND " . $table_name . ".g_id = tgate.id " . $t_like;
            if ($txtFrom != "") {
                $query = $query . " AND " . $table_name . ".e_date >= '" . insertDate($txtFrom) . "'";
            }
            if ($txtTo != "") {
                $query = $query . " AND " . $table_name . ".e_date <= '" . insertDate($txtTo) . "'";
            }
            if ($txtTimeFrom != "") {
                $query = $query . " AND " . $table_name . ".e_time >= '" . $txtTimeFrom . "'";
            }
            if ($txtTimeTo != "") {
                $query = $query . " AND " . $table_name . ".e_time <= '" . $txtTimeTo . "'";
            }
            $query .= " ORDER BY e_date, e_id, e_time ";
            $count = 0;
            $in_count = 0;
            $idate = "";
            $ingate = "";
            $intime = "";
            $out_count = 0;
            $odate = "";
            $outgate = "";
            $outtime = "";
            $result = mysqli_query($jconn, $query);
            while ($cur = mysqli_fetch_row($result)) {
                if ($count == 0) {
                    if (stripos($cur[3], "(IN)") !== false || $cur[4] == 1) {
                        $idate = displayDate($cur[0]);
                        $ingate = $cur[3];
                        $intime = displayVirdiTime($cur[1]);
                        $count++;
                        $in_count++;
                        $gs_in_count++;
                    } else {
                        if ((stripos($cur[3], "(OUT)") !== false || $cur[4] == 2) && stripos($cur[3], "(IN)") === false) {
                            $odate = displayDate($cur[0]);
                            $outgate = $cur[3];
                            $outtime = displayVirdiTime($cur[1]);
                            $out_count++;
                        }
                    }
                } else {
                    if ((stripos($cur[3], "(OUT)") !== false || $cur[4] == 2) && stripos($cur[3], "(IN)") === false) {
                        if ($csv != "yes") {
                            addZero($main_cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
                            print "<tr><td><a title='ID'><input type='hidden' name='txh" . $count . "' value='" . $main_cur[0] . "'><font face='Verdana' size='1'>" . addZero($main_cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $main_cur[1] . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $main_cur[5] . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $main_cur[2] . "</font></a></td> <td><font face='Verdana' size='1'>" . $main_cur[3] . "</font></td> <td><font face='Verdana' size='1'>" . $main_cur[6] . "</font></td>";
                            displayVirdiTime($cur[1]);
                            print "<td><a title='Date'><font face='Verdana' size='1'>" . $idate . "</font></a></td> <td><font face='Verdana' size='1'>" . $ingate . "</font></td> <td><font face='Verdana' size='1'>" . $intime . "</font></td> <td><font face='Verdana' size='1'>" . displayVirdiTime($cur[1]) . "</font></td> <td><font face='Verdana' size='1'>" . $cur[3] . "</font></td></tr>";
                        } else {
                            addZero($main_cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
                            displayVirdiTime($cur[1]);
                            print addZero($main_cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . ";" . $main_cur[1] . ";" . $main_cur[5] . ";" . $main_cur[2] . ";" . $main_cur[3] . ";" . $main_cur[6] . ";" . $idate . ";" . $ingate . ";" . $intime . ";" . displayVirdiTime($cur[1]) . ";" . $cur[3] . "\n";
                        }
                        $count = 0;
                        $gs_out_count++;
                    } else {
                        if ((stripos($cur[3], "(IN)") !== false || $cur[4] == 1) && 0 < $in_count && $idate != $cur[0]) {
                            if ($csv != "yes") {
                                addZero($main_cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
                                print "<tr><td><a title='ID'><input type='hidden' name='txh" . $count . "' value='" . $main_cur[0] . "'><font face='Verdana' size='1'>" . addZero($main_cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $main_cur[1] . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $main_cur[5] . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $main_cur[2] . "</font></a></td> <td><font face='Verdana' size='1'>" . $main_cur[3] . "</font></td> <td><font face='Verdana' size='1'>" . $main_cur[6] . "</font></td>";
                                print "<td><a title='Date'><font face='Verdana' size='1'>" . $idate . "</font></a></td> <td><font face='Verdana' size='1'>" . $ingate . "</font></td> <td><font face='Verdana' size='1'>" . $intime . "</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td>";
                            } else {
                                addZero($main_cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
                                print addZero($main_cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . ";" . $main_cur[1] . ";" . $main_cur[5] . ";" . $main_cur[2] . ";" . $main_cur[3] . ";" . $main_cur[6] . ";" . $idate . ";" . $ingate . ";" . $intime . ";;;\n";
                            }
                            $idate = displayDate($cur[0]);
                            $ingate = $cur[3];
                            $intime = displayVirdiTime($cur[1]);
                            $gs_in_count++;
                        }
                    }
                }
            }
            if (0 < $in_count && 0 < $count) {
                if ($csv != "yes") {
                    addZero($main_cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
                    print "<tr><td><a title='ID'><input type='hidden' name='txh" . $count . "' value='" . $main_cur[0] . "'><font face='Verdana' size='1'>" . addZero($main_cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $main_cur[1] . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $main_cur[5] . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $main_cur[2] . "</font></a></td> <td><font face='Verdana' size='1'>" . $main_cur[3] . "</font></td> <td><font face='Verdana' size='1'>" . $main_cur[6] . "</font></td>";
                    print "<td><a title='Date'><font face='Verdana' size='1'>" . $idate . "</font></a></td> <td><font face='Verdana' size='1'>" . $ingate . "</font></td> <td><font face='Verdana' size='1'>" . $intime . "</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td>";
                } else {
                    addZero($main_cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
                    print addZero($main_cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . ";" . $main_cur[1] . ";" . $main_cur[5] . ";" . $main_cur[2] . ";" . $main_cur[3] . ";" . $main_cur[6] . ";" . $idate . ";" . $ingate . ";" . $intime . ";;;\n";
                }
            }
            if (0 < $out_count && $count == 0) {
                if ($csv != "yes") {
                    addZero($main_cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
                    print "<tr><td><a title='ID'><input type='hidden' name='txh" . $count . "' value='" . $main_cur[0] . "'><font face='Verdana' \tsize='1'>" . addZero($main_cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>" . $main_cur[1] . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $main_cur[5] . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $main_cur[2] . "</font></a></td> <td><font face='Verdana' size='1'>" . $main_cur[3] . "</font></td> <td><font face='Verdana' size='1'>" . $main_cur[6] . "</font></td>";
                    print "<td><a title='Date'><font face='Verdana' size='1'>" . $odate . "</font></a></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='1'>" . $outtime . "</font></td> <td><font face='Verdana' size='1'>" . $outgate . "</font></td>";
                } else {
                    addZero($main_cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
                    print addZero($main_cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . ";" . $main_cur[1] . ";" . $main_cur[5] . ";" . $main_cur[2] . ";" . $main_cur[3] . ";" . $main_cur[6] . ";" . $odate . ";;;" . $outtime . ";" . $outgate . "\n";
                }
            }
        }
    }
    if ($csv != "yes") {
        print "</table>";
    }
    if ($csv != "yes") {
        if ($prints != "yes") {
            print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800' class='table table-striped table-bordered dataTable'>";
        } else {
            print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%' class='table table-striped table-bordered dataTable'>";
        }
        print "<tr><td><font face='Verdana' size='2'>Group</font></td>";
        if (getRegister(encryptDecrypt($txtMACAddress), 7) == "39") {
            print "<td><font face='Verdana' size='2'>OUT</font></td> <td><font face='Verdana' size='2'>IN</font></td>";
        } else {
            print "<td><font face='Verdana' size='2'>IN</font></td> <td><font face='Verdana' size='2'>OUT</font></td>";
        }
        print "<td><font face='Verdana' size='2'>Balance</font></td> </tr>";
    } else {
        print "\n\n";
        if (getRegister(encryptDecrypt($txtMACAddress), 7) == "39") {
            print "Group;OUT;IN;Balance\n";
        } else {
            print "Group;IN;OUT;Balance\n";
        }
    }
    $tot_in = 0;
    $tot_out = 0;
    $tot_bal = 0;
    for ($i = 0; $i < $gs_count; $i++) {
        if (!($gs_in[$i] == 0 && $gs_out[$i] == 0)) {
            if ($csv != "yes") {
                print "<tr><td><font face='Verdana' size='1'>" . $gs_name[$i] . "</font></td> <td><font face='Verdana' size='1'>" . $gs_in[$i] . "</font></td> <td><font face='Verdana' size='1'>" . $gs_out[$i] . "</font></td> <td><font face='Verdana' size='1'>" . ($gs_in[$i] - $gs_out[$i]) . "</font></a></td></tr>";
            } else {
                print $gs_name[$i] . ";" . $gs_in[$i] . ";" . $gs_out[$i] . ";" . ($gs_in[$i] - $gs_out[$i]) . "\n";
            }
            $tot_in = $tot_in + $gs_in[$i];
            $tot_out = $tot_out + $gs_out[$i];
        }
    }
    $tot_bal = $tot_in - $tot_out;
    if ($csv != "yes") {
        print "<tr><td><font face='Verdana' size='1'><b>Totals</b></font></a></td> <td><font face='Verdana' size='1'><b>" . $tot_in . "</b></font></td> <td><font face='Verdana' size='1'><b>" . $tot_out . "</b></font></td> <td><font face='Verdana' size='1'><b>" . $tot_bal . "</b></font></a></td></tr>";
        print "</table>";
    } else {
        print "Totals;" . $tot_in . ";" . $tot_out . ";" . $tot_bal . "\n";
    }
    if ($prints != "yes") {
        print "<p align='center'><input type='button' value='Print' onClick='checkPrint(0)' class='btn btn-primary'>&nbsp;&nbsp;<input type='button' value='Excel' onClick='checkPrint(1)' class='btn btn-primary'>&nbsp;&nbsp;<input type='button' value='CSV' onClick='checkPrint(3)' class='btn btn-primary'></p>";
    }
}
if ($csv != "yes") {
    print "</form>";
    print "</div></div></div></div></div>";
    echo "</center>";
    include 'footer.php';
}


?>