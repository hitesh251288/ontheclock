<?php
ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "21";
set_time_limit(0);
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
$macAddress = $_SESSION[$session_variable . "MACAddress"];
if (!(checkSession($userlevel, $current_module) && $_SESSION[$session_variable . "VirdiLevel"] == "Classic")) {
    header("Location: " . $config["REDIRECT"] . "?url=ReportGroupSummary.php&message=Session Expired or Security Policy Violated");
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
    $message = "Group Summary Report<br>Report Valid ONLY for Shifts with Routine Type = Daily";
}
$lstDisplayGroup = $_POST["lstDisplayGroup"];
if ($lstDisplayGroup == "") {
    $lstDisplayGroup = "Yes";
}

$lstDisplayDeptGroup = $_POST["lstDisplayDeptGroup"];
if ($lstDisplayDeptGroup == "") {
    $lstDisplayDeptGroup = "No";
}
$lstDisplayDivision = $_POST["lstDisplayDivision"];
if ($lstDisplayDivision == "") {
    $lstDisplayDivision = "No";
}
$lstDisplayDepartment = $_POST["lstDisplayDepartment"];
if ($lstDisplayDepartment == "") {
    $lstDisplayDepartment = "No";
}
$txtFrom = $_POST["txtFrom"];
$txtTo = $_POST["txtTo"];
if ($txtFrom == "") {
    $txtFrom = displayToday();
}
if ($txtTo == "") {
    $txtTo = displayToday();
}
$lstDisplayRemark = $_POST["lstDisplayRemark"];
if ($lstDisplayRemark == "") {
    $lstDisplayRemark = "No";
}
$lstDisplayPhone = $_POST["lstDisplayPhone"];
if ($lstDisplayPhone == "") {
    $lstDisplayPhone = "No";
}
$lstDisplayTerminal = $_POST["lstDisplayTerminal"];
if ($lstDisplayTerminal == "") {
    $lstDisplayTerminal = "No";
}
$lstDisplayIdNo = $_POST["lstDisplayIdNo"];
if ($lstDisplayIdNo == "") {
    $lstDisplayIdNo = "No";
}
$lstEmployeeStatus = $_POST["lstEmployeeStatus"];
if ($lstEmployeeStatus == "") {
    $lstEmployeeStatus = "Active";
}
$tflag = false;
if ($macAddress == "00-18-8B-8C-C9-D2") {
    $tflag = true;
}
$t_ae = 0;
$t_fda = 0;
$t_rg = 0;
$t_prm = 0;
$t_rsn = 0;
$t_rtd = 0;
$t_trm = 0;
$t_bk = 0;
$t_px = 0;
$t_v = 0;
$t_i = 0;
$t_b = 0;
$t_g = 0;
$t_y = 0;
$t_o = 0;
$t_r = 0;
$t_gr = 0;
$t_br = 0;
$t_pr = 0;
$t_mg = 0;
$t_tl = 0;
$t_aq = 0;
$t_sf = 0;
$t_am = 0;
$t_gl = 0;
$t_vm = 0;
$t_sl = 0;
$t_mr = 0;
$t_pk = 0;
$t_flg = 0;
$t_wkd = 0;
$t_sat = 0;
$t_sun = 0;
$t_p = 0;
$t_a = 0;
$t_af = 0;
$t_ns = 0;
$t_gc = 0;
$t_li = 0;
$t_mb = 0;
$t_eo = 0;
$t_ao = 0;
$normal_hour;
$ot1_hour;
$ot2_hour;
$ot3_hour;
$ot4_hour;
if ($prints != "yes") {
    include 'header.php';
    ?>
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-12 d-flex no-block align-items-center">
                <h4 class="page-title">Group Summary Report</h4>
                <div class="ms-auto text-end">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Group Summary Report
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <?php
}
print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='ReportGroupSummary.php'><input type='hidden' name='act' value='searchRecord'>";
//print "<html><title>Group Summary Report</title>";
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
        header("Content-Disposition: attachment; filename=ReportGroupSummary.xls");
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
        //        print "<tr><td width='25%'>&nbsp;</td><td><font face='Verdana' size='1'><b>Select ONE or MORE options and click 'Search Record'</b></font></td></tr>";
    } else {
//            print "<table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
//            print "<tr><td width='25%'>&nbsp;</td><td><font face='Verdana' size='1'><b>Selected Options</b></font></td></tr>";
    }
    ?>
            <div class="row">
                <div class="col-2">
            <?php
            print "<label class='form-label'>Display Groups:</label><select name='lstDisplayGroup' class='select2 form-select shadow-none'><option selected value='" . $lstDisplayGroup . "'>" . $lstDisplayGroup . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select>";
            ?>
                </div>
                <div class="col-2">
                    <?php
                    print "<label class='form-label'>Display Div/Desg:</label><select name='lstDisplayDivision' class='select2 form-select shadow-none'><option selected value='" . $lstDisplayDivision . "'>" . $lstDisplayDivision . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select>";
                    ?>
                </div>
                <div class="col-2">
                    <?php
                    $query = "SELECT GroupID, Name from GroupMaster ORDER BY name";
                    //    displayList("lstDisplayDeptGroup", "List Departments for Group:", $lstDisplayDeptGroup, $prints, $conn, $query, "", "25%", "25%");
                    print "<label class='form-label'>List Departments for Group:</label><select name='lstDisplayDeptGroup' class='select2 form-select shadow-none'><option selected value='" . $lstDisplayDeptGroup . "'>" . $lstDisplayDeptGroup . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select>";
                    ?>
                </div>
                <div class="col-2">
                    <?php
                    print "<label class='form-label'>Display Dept:</label><select name='lstDisplayDepartment' class='select2 form-select shadow-none'><option selected value='" . $lstDisplayDepartment . "'>" . $lstDisplayDepartment . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select>";
                    ?>
                </div>
                <div class="col-2">
                    <?php
                    print "<label class='form-label'>Display Remarks:</label><select name='lstDisplayRemark' class='select2 form-select shadow-none'><option selected value='" . $lstDisplayRemark . "'>" . $lstDisplayRemark . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select>";
                    ?>
                </div>
                <div class="col-2">
                    <?php
                    displayTextbox("txtFrom", "Date From (DD/MM/YYYY): ", $txtFrom, $prints, 12, "25%", "25%");
                    ?>
                </div>
                </div>
                <div class="row">
                    <div class="col-2">
    <?php
    print "<label class='form-label'>Display " . $_SESSION[$session_variable . "IDColumnName"] . ":</label><select name='lstDisplayIdNo' class='select2 form-select shadow-none'><option selected value='" . $lstDisplayIdNo . "'>" . $lstDisplayIdNo . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select>";
    ?>
                    </div>
                    <div class="col-2">
                        <?php
                        displayTextbox("txtTo", "Date To (DD/MM/YYYY): ", $txtTo, $prints, 12, "25%", "25%");
                        ?>
                    </div>
                    <div class="col-2">
                        <?php
                        print "<label class='form-label'>Display " . $_SESSION[$session_variable . "PhoneColumnName"] . ":</label><select name='lstDisplayPhone' class='select2 form-select shadow-none'><option selected value='" . $lstDisplayPhone . "'>" . $lstDisplayPhone . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select>";
                        ?>
                    </div>
                    <div class="col-2">
                        <?php
                        print "<label class='form-label'>Display Terminal:</label><select name='lstDisplayTerminal' class='select2 form-select shadow-none'><option selected value='" . $lstDisplayTerminal . "'>" . $lstDisplayTerminal . "</option> <option value='Yes'>Yes</option> <option value='No'>No</option> </select>";
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
    <?php
    if ($prints != "yes") {
        print "<center><br><input name='btSearch' class='btn btn-primary' type='submit' value='Search Record'></center>";
    }
    ?>
                    </div>
                </div>
            </div>
        </div>
    <?php
}
print "</div></div></div></div>";

if ($act == "searchRecord") {
    if ($excel != "yes") {
        print "<p align='center'><font face='Verdana' size='1'><b>" . $_SESSION[$session_variable . "FlagReportText"] . "</b></font></p>";
        print "<p><font face='Verdana' size='1'><b>AE</b> = Current Active Employees ; <b>FDA</b> = Current Flagged Employees ; <b>RG</b> = No of Employees Registered ; <b>PRM</b> = No of Employees Promoted ; <b>RSN</b> = No of Employees Resigned ; <b>RTD</b> = No of Employees Retired ; <b>TRM</b> = No of Employees Terminated <br> <b>BK</b> = Black Flag ; <b>PX</b> = Proxy Flag <br><b>FLG</b> = Total Flags ; <b>WKD</b> = Week Day ; <b>SAT</b> = Saturday / OT1 ; <b>SUN</b> = Sunday / OT2 <br><b>P</b> = Total Employees Present ; <b>A</b> = Total Absent Employees ; <b>A/SS</b> = Absent Employees (EXCLUDING Saturdays (OT1) and Sundays (OT2)) ; <b>A/F</b> - Total Absent Employees (EXCLUDING Flagged Employees) <br><b>NS</b> = Total Night Shift Attendance <br><b>GC</b> = Total Grace Hours ; <b>LI</b> = Total Late In Hours ; <b>MB</b> =Total More Break Hours ; <b>EO</b> = Total Early Out Hours <br><b>AO</b> = Total Approved Overtime Hours </font></p>";
    }
    $query = "SELECT * FROM TLSFlag";
    $colour_result = selectData($conn, $query);
    $query = "SELECT RGSSelection FROM Usermaster WHERE Username = '" . $username . "'";
    $rgs_selection = selectData($conn, $query);
    $rgs = $rgs_selection[0];
    $dayCount = getTotalDays($txtFrom, $txtTo);
    print '<div class="row"><div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12"><div class="card"><div class="card-body table-responsive">';
    for ($ii = 0; $ii < 8; $ii++) {
        if ($lstDisplayDivision == "Yes" && $ii == 0 || $lstDisplayDepartment == "Yes" && $ii == 1 || $lstDisplayRemark == "Yes" && $ii == 2 || $lstDisplayPhone == "Yes" && $ii == 3 || $lstDisplayIdNo == "Yes" && $ii == 4 || $lstDisplayTerminal == "Yes" && $ii == 5 || $lstDisplayGroup == "Yes" && $ii == 6 || 0 < strlen($lstDisplayDeptGroup) && $ii == 7) {
            $counter = 0;
            if ($excel != "yes") {
                print "<br>";
            }
            print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%' class='table table-striped table-bordered dataTable' id='zero_config'>";
            if ($excel == "yes") {
                
            }
            print "<thead><tr><td><font face='Verdana' size='2'><b>";
            switch ($ii) {
                case 0:
                    print "Division";
                    break;
                case 1:
                    print "Department";
                    break;
                case 2:
                    print "Remark";
                    break;
                case 3:
                    print $_SESSION[$session_variable . "PhoneColumnName"];
                    break;
                case 4:
                    print $_SESSION[$session_variable . "IDColumnName"];
                    break;
                case 5:
                    print "Terminal";
                    break;
                case 6:
                    print "Group";
                    break;
                case 7:
                    print "Groups Displayed with Department Details";
                    break;
            }
            print "</b></font></td>";
            if ($ii != 5) {
                print "<td bgcolor='#F0F0F0'><a title='Current Active Employees'><font face='Verdana' size='2'>";
                if ($tflag) {
                    print "Active Employees";
                } else {
                    print "AE";
                }
                print "</font></a></td>";
                if ($macAddress != "40-A8-F0-23-F0-AD") {
                    print "<td bgcolor='#F0F0F0'><a title='Current Flagged Employees'><font face='Verdana' size='2'>";
                    if ($tflag) {
                        print "Flagged De-Activation";
                    } else {
                        print "FDA";
                    }
                    print "</font></a></td>";
                }
                if (strpos($rgs, "-RG-") !== false) {
                    print "<td><font face='Verdana' size='2'>";
                    if ($tflag) {
                        print "Registered Employees";
                    } else {
                        print "RG";
                    }
                    print "<td><font face='Verdana' size='2'>RG</font></td>";
                }
                if (strpos($rgs, "-PRM-") !== false) {
                    print "<td><font face='Verdana' size='2'>PRM</font></td>";
                }
                if (strpos($rgs, "-RSN-") !== false) {
                    print "<td><font face='Verdana' size='2'>RSN</font></td>";
                }
                if (strpos($rgs, "-RTD-") !== false) {
                    print "<td><font face='Verdana' size='2'>RTD</font></td>";
                }
                if (strpos($rgs, "-TRM-") !== false) {
                    print "<td><font face='Verdana' size='2'>TRM</font></td>";
                }
                if (strpos($rgs, "-P-") !== false && $txtFrom == $txtTo) {
                    print "<td bgcolor='#F0F0F0'><font face='Verdana' size='2'>P</font></td>";
                }
                if (strpos($rgs, "-A-") !== false && $txtFrom == $txtTo) {
                    print "<td><font face='Verdana' size='2'>A</font></td>";
                } else {
                    if (strpos($rgs, "-A-") !== false) {
                        print "<td><font face='Verdana' size='2'>A/SS</font></td>";
                    }
                }
                if (strpos($rgs, "-A/F-") !== false && $txtFrom == $txtTo) {
                    print "<td><font face='Verdana' size='2'>A/F</font></td>";
                }
                if (strpos($rgs, "-BK-") !== false) {
                    print "<td bgcolor='#F0F0F0'><font face='Verdana' size='2'>BK</font></td>";
                }
                if (strpos($rgs, "-PX-") !== false) {
                    print "<td bgcolor='#F0F0F0'><font face='Verdana' size='2'>PX</font></td>";
                }
                if (strpos($rgs, "-V-") !== false) {
                    print "<td><font face='Verdana' size='2' color='Violet'>V</font></td>";
                }
                if (strpos($rgs, "-I-") !== false) {
                    print "<td><font face='Verdana' size='2' color='Indigo'>I</font></td>";
                }
                if (strpos($rgs, "-B-") !== false) {
                    print "<td><font face='Verdana' size='2' color='Blue'>B</font></td>";
                }
                if (strpos($rgs, "-G-") !== false) {
                    print "<td><font face='Verdana' size='2' color='Green'>G</font></td>";
                }
                if (strpos($rgs, "-Y-") !== false) {
                    print "<td bgcolor='Brown'><font face='Verdana' size='2' color='Yellow'>Y</font></td> ";
                }
                if (strpos($rgs, "-O-") !== false) {
                    print "<td><font face='Verdana' size='2' color='Orange'>O</font></td>";
                }
                if (strpos($rgs, "-R-") !== false) {
                    print "<td><font face='Verdana' size='2' color='Red'>R</font></td>";
                }
                if (strpos($rgs, "-GR-") !== false) {
                    print "<td><font face='Verdana' size='2' color='Gray'>GR</font></td>";
                }
                if (strpos($rgs, "-BR-") !== false) {
                    print "<td><font face='Verdana' size='2' color='Brown'>BR</font></td>";
                }
                if (strpos($rgs, "-PR-") !== false) {
                    print "<td><font face='Verdana' size='2' color='Purple'>PR</font></td>";
                }
                if (strpos($rgs, "-MG-") !== false) {
                    print "<td><font face='Verdana' size='2' color='Magenta'><b>MG</b></font></td>";
                }
                if (strpos($rgs, "-TL-") !== false) {
                    print "<td><font face='Verdana' size='2' color='Teal'><b>TL</b></font></td>";
                }
                if (strpos($rgs, "-AQ-") !== false) {
                    print "<td><font face='Verdana' size='2' color='Aqua'><b>AQ</b></font></td>";
                }
                if (strpos($rgs, "-SF-") !== false) {
                    print "<td><font face='Verdana' size='2' color='Safron'><b>SF</b></font></td>";
                }
                if (strpos($rgs, "-AM-") !== false) {
                    print "<td><font face='Verdana' size='2' color='Amber'><b>AM</b></font></td>";
                }
                if (strpos($rgs, "-GL-") !== false) {
                    print "<td><font face='Verdana' size='2' color='Golden'><b>GL</b></font></td>";
                }
                if (strpos($rgs, "-VM-") !== false) {
                    print "<td><font face='Verdana' size='2' color='Vermilion'><b>VM</b></font></td>";
                }
                if (strpos($rgs, "-SL-") !== false) {
                    print "<td><font face='Verdana' size='2' color='Silver'><b>SL</b></font></td>";
                }
                if (strpos($rgs, "-MR-") !== false) {
                    print "<td><font face='Verdana' size='2' color='Maroon'><b>MR</b></font></td>";
                }
                if (strpos($rgs, "-PK-") !== false) {
                    print "<td><font face='Verdana' size='2' color='Pink'><b>PK</b></font></td>";
                }
                if (strpos($rgs, "-FLG-") !== false) {
                    print "<td bgcolor='#F0F0F0'><font face='Verdana' size='2'>FLG</font></td>";
                }
                if (strpos($rgs, "-WKD-") !== false) {
                    print "<td><font face='Verdana' size='2'>WKD</font></td>";
                }
                if (strpos($rgs, "-SAT-") !== false) {
                    print "<td><font face='Verdana' size='2'>SAT</font></td>";
                }
                if (strpos($rgs, "-SUN-") !== false) {
                    print "<td><font face='Verdana' size='2'>SUN</font></td>";
                }
                if (strpos($rgs, "-NS-") !== false) {
                    print "<td bgcolor='#F0F0F0'><font face='Verdana' size='2'>NS</font></td>";
                }
                if (strpos($rgs, "-GC-") !== false) {
                    print "<td><font face='Verdana' size='2'>GC</font></td>";
                }
                if (strpos($rgs, "-LI-") !== false) {
                    print "<td><font face='Verdana' size='2'>LI</font></td>";
                }
                if (strpos($rgs, "-MB-") !== false) {
                    print "<td><font face='Verdana' size='2'>MB</font></td>";
                }
                if (strpos($rgs, "-EO-") !== false) {
                    print "<td><font face='Verdana' size='2'>EO</font></td>";
                }
                if (strpos($rgs, "-AO-") !== false) {
                    print "<td><font face='Verdana' size='2'>AO</font></td>";
                }
            } else {
                print "<td><font face='Verdana' size='2'>P</font></td>";
                print "<td><font face='Verdana' size='2'>N</font></td>";
                print "<td><font face='Verdana' size='2'>WK OT</font></td>";
                print "<td><font face='Verdana' size='2'>SAT OT</font></td>";
                print "<td><font face='Verdana' size='2'>SUN OT</font></td>";
                print "<td><font face='Verdana' size='2'>PH OT</font></td>";
            }
            print "</tr></thead>";
            $query = "";
            switch ($ii) {
                case 0:
                    $query = "SELECT DISTINCT(tuser.company) FROM tuser WHERE LENGTH(tuser.company) > 0  " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ORDER BY tuser.company";
                    break;
                case 1:
                    $query = "SELECT DISTINCT(tuser.dept) FROM tuser WHERE LENGTH(tuser.dept) > 0  " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ORDER BY tuser.dept";
                    break;
                case 2:
                    $query = "SELECT DISTINCT(tuser.remark) FROM tuser WHERE LENGTH(tuser.remark) > 0  " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ORDER BY tuser.remark";
                    break;
                case 3:
                    $query = "SELECT DISTINCT(tuser.phone) FROM tuser WHERE LENGTH(tuser.phone) > 0  " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ORDER BY tuser.phone";
                    break;
                case 4:
                    $query = "SELECT DISTINCT(tuser.idno) FROM tuser WHERE LENGTH(tuser.idno) > 0  " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ORDER BY tuser.idno";
                    break;
                case 5:
                    $query = "SELECT tgate.id, tgate.name FROM tgate ORDER BY tgate.name";
                    break;
                case 6:
                    $query = "SELECT GroupID, Name FROM GroupMaster ORDER BY Name";
                    break;
                case 7:
                    $query = "SELECT DISTINCT(tuser.dept) FROM tuser WHERE LENGTH(tuser.dept) > 0  " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ORDER BY tuser.dept";
                    break;
            }
            $result = mysqli_query($conn, $query);
            while ($cur = mysqli_fetch_row($result)) {
                $group_query = "";
                if ($ii == 6) {
                    $group_query = " ( " . group_query($conn, $group_query, $cur[0]) . " ) ";
                } else {
                    switch ($ii) {
                        case 0:
                            $group_query = " tuser.company = '" . $cur[0] . "' ";
                            break;
                        case 1:
                            $group_query = " tuser.dept = '" . $cur[0] . "' ";
                            break;
                        case 2:
                            $group_query = " tuser.remark = '" . $cur[0] . "' ";
                            break;
                        case 3:
                            $group_query = " tuser.phone = '" . $cur[0] . "' ";
                            break;
                        case 4:
                            $group_query = " tuser.idno = '" . $cur[0] . "' ";
                            break;
                        case 7:
                            $group_query = " ( " . group_query($conn, $group_query, $lstDisplayDeptGroup) . " ) AND tuser.dept = '" . $cur[0] . "' ";
                            break;
                    }
                }
                $no_flag = 0;
                $sub_query = "";
                if ($ii = !5) {
                    $sub_query = "SELECT SUM(Grace)/3600, SUM(LateIN)/3600, SUM(MoreBreak)/3600, SUM(EarlyOut)/3600, SUM(AOvertime)/3600 FROM AttendanceMaster, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND AttendanceMaster.ADate >= " . insertDate($txtFrom) . " AND AttendanceMaster.ADate <= " . insertDate($txtTo) . " AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $sum_cur = mysqli_fetch_row($sub_result);
                    $sub_query = "SELECT COUNT(id) FROM tuser WHERE SUBSTRING(tuser.datelimit, 2, 8) >= '" . insertDate($txtFrom) . "' AND SUBSTRING(tuser.datelimit, 2, 8) <= '" . insertDate($txtTo) . "' AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $reg_cur = mysqli_fetch_row($sub_result);
                    $sub_query = "SELECT COUNT(id) FROM tuser WHERE tuser.datelimit LIKE 'Y%' AND SUBSTRING(tuser.datelimit, 10, 8) >= '" . insertDate($txtFrom) . "' AND SUBSTRING(tuser.datelimit, 10, 8) <= '" . insertDate($txtTo) . "' AND " . $group_query . " AND PassiveType = 'PRM' ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $prm_cur = mysqli_fetch_row($sub_result);
                    $sub_query = "SELECT COUNT(id) FROM tuser WHERE tuser.datelimit LIKE 'Y%' AND SUBSTRING(tuser.datelimit, 10, 8) >= '" . insertDate($txtFrom) . "' AND SUBSTRING(tuser.datelimit, 10, 8) <= '" . insertDate($txtTo) . "' AND " . $group_query . " AND PassiveType = 'RSN' ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $rsn_cur = mysqli_fetch_row($sub_result);
                    $sub_query = "SELECT COUNT(id) FROM tuser WHERE tuser.datelimit LIKE 'Y%' AND SUBSTRING(tuser.datelimit, 10, 8) >= '" . insertDate($txtFrom) . "' AND SUBSTRING(tuser.datelimit, 10, 8) <= '" . insertDate($txtTo) . "' AND " . $group_query . " AND PassiveType = 'RTD' ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $rtd_cur = mysqli_fetch_row($sub_result);
                    $sub_query = "SELECT COUNT(id) FROM tuser WHERE tuser.datelimit LIKE 'Y%' AND SUBSTRING(tuser.datelimit, 10, 8) >= '" . insertDate($txtFrom) . "' AND SUBSTRING(tuser.datelimit, 10, 8) <= '" . insertDate($txtTo) . "' AND " . $group_query . " AND PassiveType = 'TRM' ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $trm_cur = mysqli_fetch_row($sub_result);
                    $abs_cur = 0;
                    $sub_query = "SELECT id FROM tuser WHERE (PassiveType = 'ACT' OR PassiveType = 'ADA' OR PassiveType = 'FDA') AND SUBSTRING(tuser.datelimit, 2, 8) < '" . insertDate($txtFrom) . "' AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    while ($sub_cur = mysqli_fetch_row($sub_result)) {
                        $abs_cur = $abs_cur + getASS($conn, $sub_cur[0], $txtFrom, $txtTo);
                    }
                    $sub_query = "SELECT tuser.id, tuser.OT1, tuser.OT2 FROM tuser WHERE (PassiveType = 'ACT' OR PassiveType = 'ADA' OR PassiveType = 'FDA') AND " . $group_query . " ";
                    $sub_query .= " AND tuser.id NOT IN (SELECT AttendanceMaster.EmployeeID FROM AttendanceMaster WHERE AttendanceMaster.ADate >= " . insertDate($txtFrom) . " AND AttendanceMaster.ADate <= " . insertDate($txtTo) . ") AND SUBSTRING(tuser.datelimit, 2, 8) < '" . insertDate($txtFrom) . "' ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    while ($sub_cur = mysqli_fetch_row($sub_result)) {
                        if ($sub_cur[1] == "") {
                            $sub_cur[1] = "Saturday";
                        }
                        if ($sub_cur[2] == "") {
                            $sub_cur[2] = "Sunday";
                        }
                        $satCount = getDayCount(insertDate($txtFrom), insertDate($txtTo), $dayCount, $sub_cur[1]);
                        $sunCount = getDayCount(insertDate($txtFrom), insertDate($txtTo), $dayCount, $sub_cur[2]);
                        $abs_cur = $abs_cur + $dayCount - $satCount - $sunCount;
                    }
                    $sub_query = "SELECT COUNT(EmployeeID) FROM AttendanceMaster, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND Flag = 'Black' AND AttendanceMaster.ADate >= " . insertDate($txtFrom) . " AND AttendanceMaster.ADate <= " . insertDate($txtTo) . " AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $black_cur = mysqli_fetch_row($sub_result);
                    $sub_query = "SELECT COUNT(FlagDayRotation.e_id) FROM FlagDayRotation, tuser WHERE FlagDayRotation.e_id = tuser.id AND FlagDayRotation.Flag = 'Black' AND FlagDayRotation.e_date >= " . insertDate($txtFrom) . " AND FlagDayRotation.e_date <= " . insertDate($txtTo) . " AND FlagDayRotation.RecStat = 0 AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $black_pre_cur = mysqli_fetch_row($sub_result);
                    $sub_query = "SELECT COUNT(EmployeeID) FROM AttendanceMaster, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND  Flag = 'Proxy' AND AttendanceMaster.ADate >= " . insertDate($txtFrom) . " AND AttendanceMaster.ADate <= " . insertDate($txtTo) . " AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $proxy_cur = mysqli_fetch_row($sub_result);
                    $sub_query = "SELECT COUNT(FlagDayRotation.e_id) FROM FlagDayRotation, tuser WHERE FlagDayRotation.e_id = tuser.id AND FlagDayRotation.Flag = 'Proxy' AND FlagDayRotation.e_date >= " . insertDate($txtFrom) . " AND FlagDayRotation.e_date <= " . insertDate($txtTo) . " AND FlagDayRotation.RecStat = 0 AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $proxy_pre_cur = mysqli_fetch_row($sub_result);
                    $sub_query = "SELECT COUNT(EmployeeID) FROM AttendanceMaster, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND  Flag = 'Violet' AND AttendanceMaster.ADate >= " . insertDate($txtFrom) . " AND AttendanceMaster.ADate <= " . insertDate($txtTo) . " AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $violet_cur = mysqli_fetch_row($sub_result);
                    $sub_query = "SELECT COUNT(FlagDayRotation.e_id) FROM FlagDayRotation, tuser WHERE FlagDayRotation.e_id = tuser.id AND FlagDayRotation.Flag = 'Violet' AND FlagDayRotation.e_date >= " . insertDate($txtFrom) . " AND FlagDayRotation.e_date <= " . insertDate($txtTo) . " AND FlagDayRotation.RecStat = 0 AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $violet_pre_cur = mysqli_fetch_row($sub_result);
                    if ($colour_result[1] == "No") {
                        $no_flag = $no_flag + $violet_cur[0] + $violet_pre_cur[0];
                    }
                    $sub_query = "SELECT COUNT(EmployeeID) FROM AttendanceMaster, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND  Flag = 'Indigo' AND AttendanceMaster.ADate >= " . insertDate($txtFrom) . " AND AttendanceMaster.ADate <= " . insertDate($txtTo) . " AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $indigo_cur = mysqli_fetch_row($sub_result);
                    $sub_query = "SELECT COUNT(FlagDayRotation.e_id) FROM FlagDayRotation, tuser WHERE FlagDayRotation.e_id = tuser.id AND FlagDayRotation.Flag = 'Indigo' AND FlagDayRotation.e_date >= " . insertDate($txtFrom) . " AND FlagDayRotation.e_date <= " . insertDate($txtTo) . " AND FlagDayRotation.RecStat = 0 AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $indigo_pre_cur = mysqli_fetch_row($sub_result);
                    if ($colour_result[2] == "No") {
                        $no_flag = $no_flag + $indigo_cur[0] + $indigo_pre_cur[0];
                    }
                    $sub_query = "SELECT COUNT(EmployeeID) FROM AttendanceMaster, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND  Flag = 'Blue' AND AttendanceMaster.ADate >= " . insertDate($txtFrom) . " AND AttendanceMaster.ADate <= " . insertDate($txtTo) . " AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $blue_cur = mysqli_fetch_row($sub_result);
                    $sub_query = "SELECT COUNT(FlagDayRotation.e_id) FROM FlagDayRotation, tuser WHERE FlagDayRotation.e_id = tuser.id AND FlagDayRotation.Flag = 'Blue' AND FlagDayRotation.e_date >= " . insertDate($txtFrom) . " AND FlagDayRotation.e_date <= " . insertDate($txtTo) . " AND FlagDayRotation.RecStat = 0 AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $blue_pre_cur = mysqli_fetch_row($sub_result);
                    if ($colour_result[3] == "No") {
                        $no_flag = $no_flag + $blue_cur[0] + $blue_pre_cur[0];
                    }
                    $sub_query = "SELECT COUNT(EmployeeID) FROM AttendanceMaster, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND  Flag = 'Green' AND AttendanceMaster.ADate >= " . insertDate($txtFrom) . " AND AttendanceMaster.ADate <= " . insertDate($txtTo) . " AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $green_cur = mysqli_fetch_row($sub_result);
                    $sub_query = "SELECT COUNT(FlagDayRotation.e_id) FROM FlagDayRotation, tuser WHERE FlagDayRotation.e_id = tuser.id AND FlagDayRotation.Flag = 'Green' AND FlagDayRotation.e_date >= " . insertDate($txtFrom) . " AND FlagDayRotation.e_date <= " . insertDate($txtTo) . " AND FlagDayRotation.RecStat = 0 AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $green_pre_cur = mysqli_fetch_row($sub_result);
                    if ($colour_result[3] == "No") {
                        $no_flag = $no_flag + $green_cur[0] + $green_pre_cur[0];
                    }
                    $sub_query = "SELECT COUNT(EmployeeID) FROM AttendanceMaster, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND  Flag = 'Yellow' AND AttendanceMaster.ADate >= " . insertDate($txtFrom) . " AND AttendanceMaster.ADate <= " . insertDate($txtTo) . " AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $yellow_cur = mysqli_fetch_row($sub_result);
                    $sub_query = "SELECT COUNT(FlagDayRotation.e_id) FROM FlagDayRotation, tuser WHERE FlagDayRotation.e_id = tuser.id AND FlagDayRotation.Flag = 'Yellow' AND FlagDayRotation.e_date >= " . insertDate($txtFrom) . " AND FlagDayRotation.e_date <= " . insertDate($txtTo) . " AND FlagDayRotation.RecStat = 0 AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $yellow_pre_cur = mysqli_fetch_row($sub_result);
                    if ($colour_result[3] == "No") {
                        $no_flag = $no_flag + $yellow_cur[0] + $yellow_pre_cur[0];
                    }
                    $sub_query = "SELECT COUNT(EmployeeID) FROM AttendanceMaster, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND  Flag = 'Orange' AND AttendanceMaster.ADate >= " . insertDate($txtFrom) . " AND AttendanceMaster.ADate <= " . insertDate($txtTo) . " AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $orange_cur = mysqli_fetch_row($sub_result);
                    $sub_query = "SELECT COUNT(FlagDayRotation.e_id) FROM FlagDayRotation, tuser WHERE FlagDayRotation.e_id = tuser.id AND FlagDayRotation.Flag = 'Orange' AND FlagDayRotation.e_date >= " . insertDate($txtFrom) . " AND FlagDayRotation.e_date <= " . insertDate($txtTo) . " AND FlagDayRotation.RecStat = 0 AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $orange_pre_cur = mysqli_fetch_row($sub_result);
                    if ($colour_result[3] == "No") {
                        $no_flag = $no_flag + $orange_cur[0] + $orange_pre_cur[0];
                    }
                    $sub_query = "SELECT COUNT(EmployeeID) FROM AttendanceMaster, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND  Flag = 'Red' AND AttendanceMaster.ADate >= " . insertDate($txtFrom) . " AND AttendanceMaster.ADate <= " . insertDate($txtTo) . " AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $red_cur = mysqli_fetch_row($sub_result);
                    $sub_query = "SELECT COUNT(FlagDayRotation.e_id) FROM FlagDayRotation, tuser WHERE FlagDayRotation.e_id = tuser.id AND FlagDayRotation.Flag = 'Red' AND FlagDayRotation.e_date >= " . insertDate($txtFrom) . " AND FlagDayRotation.e_date <= " . insertDate($txtTo) . " AND FlagDayRotation.RecStat = 0 AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $red_pre_cur = mysqli_fetch_row($sub_result);
                    if ($colour_result[3] == "No") {
                        $no_flag = $no_flag + $red_cur[0] + $red_pre_cur[0];
                    }
                    $sub_query = "SELECT COUNT(EmployeeID) FROM AttendanceMaster, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND  Flag = 'Gray' AND AttendanceMaster.ADate >= " . insertDate($txtFrom) . " AND AttendanceMaster.ADate <= " . insertDate($txtTo) . " AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $gray_cur = mysqli_fetch_row($sub_result);
                    $sub_query = "SELECT COUNT(FlagDayRotation.e_id) FROM FlagDayRotation, tuser WHERE FlagDayRotation.e_id = tuser.id AND FlagDayRotation.Flag = 'Gray' AND FlagDayRotation.e_date >= " . insertDate($txtFrom) . " AND FlagDayRotation.e_date <= " . insertDate($txtTo) . " AND FlagDayRotation.RecStat = 0 AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $gray_pre_cur = mysqli_fetch_row($sub_result);
                    if ($colour_result[3] == "No") {
                        $no_flag = $no_flag + $gray_cur[0] + $gray_pre_cur[0];
                    }
                    $sub_query = "SELECT COUNT(EmployeeID) FROM AttendanceMaster, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND  Flag = 'Brown' AND AttendanceMaster.ADate >= " . insertDate($txtFrom) . " AND AttendanceMaster.ADate <= " . insertDate($txtTo) . " AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $brown_cur = mysqli_fetch_row($sub_result);
                    $sub_query = "SELECT COUNT(FlagDayRotation.e_id) FROM FlagDayRotation, tuser WHERE FlagDayRotation.e_id = tuser.id AND FlagDayRotation.Flag = 'Brown' AND FlagDayRotation.e_date >= " . insertDate($txtFrom) . " AND FlagDayRotation.e_date <= " . insertDate($txtTo) . " AND FlagDayRotation.RecStat = 0 AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $brown_pre_cur = mysqli_fetch_row($sub_result);
                    if ($colour_result[3] == "No") {
                        $no_flag = $no_flag + $brown_cur[0] + $brown_pre_cur[0];
                    }
                    $sub_query = "SELECT COUNT(EmployeeID) FROM AttendanceMaster, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND  Flag = 'Purple' AND AttendanceMaster.ADate >= " . insertDate($txtFrom) . " AND AttendanceMaster.ADate <= " . insertDate($txtTo) . " AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $purple_cur = mysqli_fetch_row($sub_result);
                    $sub_query = "SELECT COUNT(FlagDayRotation.e_id) FROM FlagDayRotation, tuser WHERE FlagDayRotation.e_id = tuser.id AND FlagDayRotation.Flag = 'Purple' AND FlagDayRotation.e_date >= " . insertDate($txtFrom) . " AND FlagDayRotation.e_date <= " . insertDate($txtTo) . " AND FlagDayRotation.RecStat = 0 AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $purple_pre_cur = mysqli_fetch_row($sub_result);
                    if ($colour_result[3] == "No") {
                        $no_flag = $no_flag + $purple_cur[0] + $purple_pre_cur[0];
                    }
                    $sub_query = "SELECT COUNT(EmployeeID) FROM AttendanceMaster, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND  Flag = 'Magenta' AND AttendanceMaster.ADate >= " . insertDate($txtFrom) . " AND AttendanceMaster.ADate <= " . insertDate($txtTo) . " AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $magenta_cur = mysqli_fetch_row($sub_result);
                    $sub_query = "SELECT COUNT(FlagDayRotation.e_id) FROM FlagDayRotation, tuser WHERE FlagDayRotation.e_id = tuser.id AND FlagDayRotation.Flag = 'Magenta' AND FlagDayRotation.e_date >= " . insertDate($txtFrom) . " AND FlagDayRotation.e_date <= " . insertDate($txtTo) . " AND FlagDayRotation.RecStat = 0 AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $magenta_pre_cur = mysqli_fetch_row($sub_result);
                    if ($colour_result[1] == "No") {
                        $no_flag = $no_flag + $magenta_cur[0] + $magenta_pre_cur[0];
                    }
                    $sub_query = "SELECT COUNT(EmployeeID) FROM AttendanceMaster, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND  Flag = 'Teal' AND AttendanceMaster.ADate >= " . insertDate($txtFrom) . " AND AttendanceMaster.ADate <= " . insertDate($txtTo) . " AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $teal_cur = mysqli_fetch_row($sub_result);
                    $sub_query = "SELECT COUNT(FlagDayRotation.e_id) FROM FlagDayRotation, tuser WHERE FlagDayRotation.e_id = tuser.id AND FlagDayRotation.Flag = 'Teal' AND FlagDayRotation.e_date >= " . insertDate($txtFrom) . " AND FlagDayRotation.e_date <= " . insertDate($txtTo) . " AND FlagDayRotation.RecStat = 0 AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $teal_pre_cur = mysqli_fetch_row($sub_result);
                    if ($colour_result[2] == "No") {
                        $no_flag = $no_flag + $teal_cur[0] + $teal_pre_cur[0];
                    }
                    $sub_query = "SELECT COUNT(EmployeeID) FROM AttendanceMaster, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND  Flag = 'Aqua' AND AttendanceMaster.ADate >= " . insertDate($txtFrom) . " AND AttendanceMaster.ADate <= " . insertDate($txtTo) . " AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $aqua_cur = mysqli_fetch_row($sub_result);
                    $sub_query = "SELECT COUNT(FlagDayRotation.e_id) FROM FlagDayRotation, tuser WHERE FlagDayRotation.e_id = tuser.id AND FlagDayRotation.Flag = 'Aqua' AND FlagDayRotation.e_date >= " . insertDate($txtFrom) . " AND FlagDayRotation.e_date <= " . insertDate($txtTo) . " AND FlagDayRotation.RecStat = 0 AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $aqua_pre_cur = mysqli_fetch_row($sub_result);
                    if ($colour_result[3] == "No") {
                        $no_flag = $no_flag + $aqua_cur[0] + $aqua_pre_cur[0];
                    }
                    $sub_query = "SELECT COUNT(EmployeeID) FROM AttendanceMaster, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND  Flag = 'Safron' AND AttendanceMaster.ADate >= " . insertDate($txtFrom) . " AND AttendanceMaster.ADate <= " . insertDate($txtTo) . " AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $safron_cur = mysqli_fetch_row($sub_result);
                    $sub_query = "SELECT COUNT(FlagDayRotation.e_id) FROM FlagDayRotation, tuser WHERE FlagDayRotation.e_id = tuser.id AND FlagDayRotation.Flag = 'Safron' AND FlagDayRotation.e_date >= " . insertDate($txtFrom) . " AND FlagDayRotation.e_date <= " . insertDate($txtTo) . " AND FlagDayRotation.RecStat = 0 AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $safron_pre_cur = mysqli_fetch_row($sub_result);
                    if ($colour_result[3] == "No") {
                        $no_flag = $no_flag + $safron_cur[0] + $safron_pre_cur[0];
                    }
                    $sub_query = "SELECT COUNT(EmployeeID) FROM AttendanceMaster, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND  Flag = 'Amber' AND AttendanceMaster.ADate >= " . insertDate($txtFrom) . " AND AttendanceMaster.ADate <= " . insertDate($txtTo) . " AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $amber_cur = mysqli_fetch_row($sub_result);
                    $sub_query = "SELECT COUNT(FlagDayRotation.e_id) FROM FlagDayRotation, tuser WHERE FlagDayRotation.e_id = tuser.id AND FlagDayRotation.Flag = 'Amber' AND FlagDayRotation.e_date >= " . insertDate($txtFrom) . " AND FlagDayRotation.e_date <= " . insertDate($txtTo) . " AND FlagDayRotation.RecStat = 0 AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $amber_pre_cur = mysqli_fetch_row($sub_result);
                    if ($colour_result[3] == "No") {
                        $no_flag = $no_flag + $amber_cur[0] + $amber_pre_cur[0];
                    }
                    $sub_query = "SELECT COUNT(EmployeeID) FROM AttendanceMaster, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND  Flag = 'Gold' AND AttendanceMaster.ADate >= " . insertDate($txtFrom) . " AND AttendanceMaster.ADate <= " . insertDate($txtTo) . " AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $gold_cur = mysqli_fetch_row($sub_result);
                    $sub_query = "SELECT COUNT(FlagDayRotation.e_id) FROM FlagDayRotation, tuser WHERE FlagDayRotation.e_id = tuser.id AND FlagDayRotation.Flag = 'Gold' AND FlagDayRotation.e_date >= " . insertDate($txtFrom) . " AND FlagDayRotation.e_date <= " . insertDate($txtTo) . " AND FlagDayRotation.RecStat = 0 AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $gold_pre_cur = mysqli_fetch_row($sub_result);
                    if ($colour_result[3] == "No") {
                        $no_flag = $no_flag + $gold_cur[0] + $gold_pre_cur[0];
                    }
                    $sub_query = "SELECT COUNT(EmployeeID) FROM AttendanceMaster, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND  Flag = 'Vermilion' AND AttendanceMaster.ADate >= " . insertDate($txtFrom) . " AND AttendanceMaster.ADate <= " . insertDate($txtTo) . " AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $vermilion_cur = mysqli_fetch_row($sub_result);
                    $sub_query = "SELECT COUNT(FlagDayRotation.e_id) FROM FlagDayRotation, tuser WHERE FlagDayRotation.e_id = tuser.id AND FlagDayRotation.Flag = 'Vermilion' AND FlagDayRotation.e_date >= " . insertDate($txtFrom) . " AND FlagDayRotation.e_date <= " . insertDate($txtTo) . " AND FlagDayRotation.RecStat = 0 AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $vermilion_pre_cur = mysqli_fetch_row($sub_result);
                    if ($colour_result[3] == "No") {
                        $no_flag = $no_flag + $vermilion_cur[0] + $vermilion_pre_cur[0];
                    }
                    $sub_query = "SELECT COUNT(EmployeeID) FROM AttendanceMaster, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND  Flag = 'Silver' AND AttendanceMaster.ADate >= " . insertDate($txtFrom) . " AND AttendanceMaster.ADate <= " . insertDate($txtTo) . " AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $silver_cur = mysqli_fetch_row($sub_result);
                    $sub_query = "SELECT COUNT(FlagDayRotation.e_id) FROM FlagDayRotation, tuser WHERE FlagDayRotation.e_id = tuser.id AND FlagDayRotation.Flag = 'Silver' AND FlagDayRotation.e_date >= " . insertDate($txtFrom) . " AND FlagDayRotation.e_date <= " . insertDate($txtTo) . " AND FlagDayRotation.RecStat = 0 AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $silver_pre_cur = mysqli_fetch_row($sub_result);
                    if ($colour_result[3] == "No") {
                        $no_flag = $no_flag + $silver_cur[0] + $silver_pre_cur[0];
                    }
                    $sub_query = "SELECT COUNT(EmployeeID) FROM AttendanceMaster, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND  Flag = 'Maroon' AND AttendanceMaster.ADate >= " . insertDate($txtFrom) . " AND AttendanceMaster.ADate <= " . insertDate($txtTo) . " AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $maroon_cur = mysqli_fetch_row($sub_result);
                    $sub_query = "SELECT COUNT(FlagDayRotation.e_id) FROM FlagDayRotation, tuser WHERE FlagDayRotation.e_id = tuser.id AND FlagDayRotation.Flag = 'Maroon' AND FlagDayRotation.e_date >= " . insertDate($txtFrom) . " AND FlagDayRotation.e_date <= " . insertDate($txtTo) . " AND FlagDayRotation.RecStat = 0 AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $maroon_pre_cur = mysqli_fetch_row($sub_result);
                    if ($colour_result[3] == "No") {
                        $no_flag = $no_flag + $maroon_cur[0] + $maroon_pre_cur[0];
                    }
                    $sub_query = "SELECT COUNT(EmployeeID) FROM AttendanceMaster, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND  Flag = 'Pink' AND AttendanceMaster.ADate >= " . insertDate($txtFrom) . " AND AttendanceMaster.ADate <= " . insertDate($txtTo) . " AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $pink_cur = mysqli_fetch_row($sub_result);
                    $sub_query = "SELECT COUNT(FlagDayRotation.e_id) FROM FlagDayRotation, tuser WHERE FlagDayRotation.e_id = tuser.id AND FlagDayRotation.Flag = 'Pink' AND FlagDayRotation.e_date >= " . insertDate($txtFrom) . " AND FlagDayRotation.e_date <= " . insertDate($txtTo) . " AND FlagDayRotation.RecStat = 0 AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $pink_pre_cur = mysqli_fetch_row($sub_result);
                    if ($colour_result[3] == "No") {
                        $no_flag = $no_flag + $pink_cur[0] + $pink_pre_cur[0];
                    }
                    $no_flag_query = "";
                    $sub_query = "SELECT Violet, Indigo, Blue, Green, Yellow, Orange, Red, Gray, Brown, Purple, Magenta, Teal, Aqua, Safron, Amber, Gold, Vermilion, Silver, Maroon, Pink FROM TLSFlag";
                    $sub_result = selectData($conn, $sub_query);
                    if ($sub_result[0] == "No") {
                        $no_flag_query = $no_flag_query . " AND AttendanceMaster.Flag <> 'Violet' ";
                    }
                    if ($sub_result[1] == "No") {
                        $no_flag_query = $no_flag_query . " AND AttendanceMaster.Flag <> 'Indigo' ";
                    }
                    if ($sub_result[2] == "No") {
                        $no_flag_query = $no_flag_query . " AND AttendanceMaster.Flag <> 'Blue' ";
                    }
                    if ($sub_result[3] == "No") {
                        $no_flag_query = $no_flag_query . " AND AttendanceMaster.Flag <> 'Green' ";
                    }
                    if ($sub_result[4] == "No") {
                        $no_flag_query = $no_flag_query . " AND AttendanceMaster.Flag <> 'Yellow' ";
                    }
                    if ($sub_result[5] == "No") {
                        $no_flag_query = $no_flag_query . " AND AttendanceMaster.Flag <> 'Orange' ";
                    }
                    if ($sub_result[6] == "No") {
                        $no_flag_query = $no_flag_query . " AND AttendanceMaster.Flag <> 'Red' ";
                    }
                    if ($sub_result[7] == "No") {
                        $no_flag_query = $no_flag_query . " AND AttendanceMaster.Flag <> 'Gray' ";
                    }
                    if ($sub_result[8] == "No") {
                        $no_flag_query = $no_flag_query . " AND AttendanceMaster.Flag <> 'Brown' ";
                    }
                    if ($sub_result[9] == "No") {
                        $no_flag_query = $no_flag_query . " AND AttendanceMaster.Flag <> 'Purple' ";
                    }
                    if ($sub_result[10] == "No") {
                        $no_flag_query = $no_flag_query . " AND AttendanceMaster.Flag <> 'Magenta' ";
                    }
                    if ($sub_result[11] == "No") {
                        $no_flag_query = $no_flag_query . " AND AttendanceMaster.Flag <> 'Teal' ";
                    }
                    if ($sub_result[12] == "No") {
                        $no_flag_query = $no_flag_query . " AND AttendanceMaster.Flag <> 'Aqua' ";
                    }
                    if ($sub_result[13] == "No") {
                        $no_flag_query = $no_flag_query . " AND AttendanceMaster.Flag <> 'Safron' ";
                    }
                    if ($sub_result[14] == "No") {
                        $no_flag_query = $no_flag_query . " AND AttendanceMaster.Flag <> 'Amber' ";
                    }
                    if ($sub_result[15] == "No") {
                        $no_flag_query = $no_flag_query . " AND AttendanceMaster.Flag <> 'Gold' ";
                    }
                    if ($sub_result[16] == "No") {
                        $no_flag_query = $no_flag_query . " AND AttendanceMaster.Flag <> 'Vermilion' ";
                    }
                    if ($sub_result[17] == "No") {
                        $no_flag_query = $no_flag_query . " AND AttendanceMaster.Flag <> 'Silver' ";
                    }
                    if ($sub_result[18] == "No") {
                        $no_flag_query = $no_flag_query . " AND AttendanceMaster.Flag <> 'Maroon' ";
                    }
                    if ($sub_result[19] == "No") {
                        $no_flag_query = $no_flag_query . " AND AttendanceMaster.Flag <> 'Pink' ";
                    }
                    $sub_query = "SELECT COUNT(EmployeeID) FROM AttendanceMaster, tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND (AttendanceMaster.Day <> AttendanceMaster.OT1 AND AttendanceMaster.Day <> AttendanceMaster.OT2) AND AttendanceMaster.ADate >= " . insertDate($txtFrom) . " AND AttendanceMaster.ADate <= " . insertDate($txtTo) . " " . $no_flag_query . " AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $week_cur = mysqli_fetch_row($sub_result);
                    $sub_query = "SELECT COUNT(EmployeeID) FROM AttendanceMaster , tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND  AttendanceMaster.Day = AttendanceMaster.OT1 AND AttendanceMaster.ADate >= " . insertDate($txtFrom) . " AND AttendanceMaster.ADate <= " . insertDate($txtTo) . " " . $no_flag_query . " AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $sat_cur = mysqli_fetch_row($sub_result);
                    $sub_query = "SELECT COUNT(EmployeeID) FROM AttendanceMaster , tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND  AttendanceMaster.Day = AttendanceMaster.OT2 AND AttendanceMaster.ADate >= " . insertDate($txtFrom) . " AND AttendanceMaster.ADate <= " . insertDate($txtTo) . " " . $no_flag_query . " AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $sun_cur = mysqli_fetch_row($sub_result);
                    $sub_query = "SELECT COUNT(EmployeeID) FROM AttendanceMaster , tuser WHERE AttendanceMaster.EmployeeID = tuser.id AND  NightFlag = 1 AND AttendanceMaster.ADate >= " . insertDate($txtFrom) . " AND AttendanceMaster.ADate <= " . insertDate($txtTo) . " " . $no_flag_query . " AND " . $group_query . " ";
                    $sub_result = mysqli_query($sub_query, $conn);
                    $night_cur = mysqli_fetch_row($sub_result);
                    $present_cur = 0;
                    $employee_cur = 0;
                    $fda_cur = 0;
                    $preflag_cur = 0;
                    $abs_pre_cur = 0;
                    $sub_query = "SELECT COUNT(id) FROM tuser WHERE PassiveType = 'FDA' AND " . $group_query;
                    $sub_result = mysqli_query($sub_query, $conn);
                    $fda_cur_ = mysqli_fetch_row($sub_result);
                    $fda_cur = $fda_cur_[0];
                    $t_fda = $t_fda + $fda_cur;
                    $sub_query = "SELECT COUNT(id) FROM tuser WHERE tuser.datelimit LIKE 'N%' AND " . $group_query;
                    $sub_result = mysqli_query($sub_query, $conn);
                    $employee_cur_ = mysqli_fetch_row($sub_result);
                    $employee_cur = $employee_cur_[0];
                    $t_ae = $t_ae + $employee_cur;
                    if ($txtTo == $txtFrom) {
                        $sub_query = "SELECT count(distinct(e_id)) FROM tenter, tuser WHERE tenter.e_id = tuser.id AND tenter.p_flag = 1 AND tenter.e_date = '" . insertDate($txtTo) . "' AND " . $group_query . " ";
                        $sub_result = mysqli_query($sub_query, $conn);
                        $present_cur_ = mysqli_fetch_row($sub_result);
                        $present_cur = $present_cur_[0] - $no_flag;
                        $sub_query = "SELECT count(e_id) FROM flagdayrotation, tuser WHERE flagdayrotation.e_id = tuser.id AND " . $group_query . " AND flagdayrotation.e_date = '" . insertDate($txtTo) . "'";
                        $sub_result = mysqli_query($sub_query, $conn);
                        $preflag_cur_ = mysqli_fetch_row($sub_result);
                        $preflag_cur = $preflag_cur_[0];
                        $abs_pre_cur = $employee_cur - $present_cur - $preflag_cur;
                        if ($abs_pre_cur < 0) {
                            $abs_pre_cur = 0;
                        }
                    } else {
                        $present_cur = $week_cur[0];
                        $total_days = getTotalDays($txtFrom, $txtTo);
                        $ot1_array = "";
                        $ot2_array = "";
                        $ot1_count_array = "";
                        $ot2_count_array = "";
                        $ot_days = 0;
                        $i = 0;
                        $sub_query = "SELECT OT1, count( id ) FROM tuser WHERE SUBSTRING(tuser.datelimit, 2, 8) <= '" . insertDate($txtTo) . "' AND " . $group_query . " AND tuser.datelimit LIKE 'N%' GROUP BY OT1";
                        for ($sub_result = mysqli_query($sub_query, $conn); $sub_cur = mysqli_fetch_row($sub_result); $i++) {
                            $ot1_array[$i] = $sub_cur[0];
                            $ot1_count_array[$i] = $sub_cur[1];
                        }
                        $i = 0;
                        $sub_query = "SELECT OT2, count( id ) FROM tuser WHERE SUBSTRING(tuser.datelimit, 2, 8) <= '" . insertDate($txtTo) . "' AND " . $group_query . " AND tuser.datelimit LIKE 'N%' GROUP BY OT2";
                        for ($sub_result = mysqli_query($sub_query, $conn); $sub_cur = mysqli_fetch_row($sub_result); $i++) {
                            $ot2_array[$i] = $sub_cur[0];
                            $ot2_count_array[$i] = $sub_cur[1];
                        }
                        for ($i = 0; $i < count($ot1_array); $i++) {
                            $ot_days = $ot_days + getDayCount(insertDate($txtFrom), insertDate($txtTo), $total_days, $ot1_array[$i]) * $ot1_count_array[$i];
                        }
                        for ($i = 0; $i < count($ot2_array); $i++) {
                            $ot_days = $ot_days + getDayCount(insertDate($txtFrom), insertDate($txtTo), $total_days, $ot2_array[$i]) * $ot2_count_array[$i];
                        }
                    }
                } else {
                    if ($ii == 5) {
                        $sub_query = "SELECT COUNT(DISTINCT(e_id)) FROM tenter WHERE tenter.p_flag = 1 AND tenter.e_date >= '" . insertDate($txtFrom) . "' AND tenter.e_date <= '" . insertDate($txtTo) . "' AND tenter.g_id = " . $cur[0] . " ";
                        $sub_result = mysqli_query($sub_query, $conn);
                        $present_cur_ = mysqli_fetch_row($sub_result);
                        $present_cur = $present_cur_[0] - $no_flag;
                        $sub_query = "SELECT SUM(Normal) FROM AttendanceMaster WHERE AttendanceMaster.ADate >= " . insertDate($txtFrom) . " AND AttendanceMaster.ADate <= " . insertDate($txtTo) . " AND AttendanceMaster.EmployeeID IN (SELECT DISTINCT(e_id) FROM tenter WHERE tenter.p_flag = 1 AND tenter.e_date >= '" . insertDate($txtFrom) . "' AND tenter.e_date <= '" . insertDate($txtTo) . "' AND tenter.g_id = " . $cur[0] . ") ";
                        $sub_result = mysqli_query($sub_query, $conn);
                        $normal_hour = mysqli_fetch_row($sub_result);
                        $sub_query = "SELECT SUM(AOvertime) FROM AttendanceMaster WHERE AttendanceMaster.ADate >= " . insertDate($txtFrom) . " AND AttendanceMaster.ADate <= " . insertDate($txtTo) . " AND Day<>OT1 AND Day<>OT2 AND Flag<>'Purple' AND AttendanceMaster.EmployeeID IN (SELECT DISTINCT(e_id) FROM tenter WHERE tenter.p_flag = 1 AND tenter.e_date >= '" . insertDate($txtFrom) . "' AND tenter.e_date <= '" . insertDate($txtTo) . "' AND tenter.g_id = " . $cur[0] . ") ";
                        $sub_result = mysqli_query($sub_query, $conn);
                        $ot1_hour = mysqli_fetch_row($sub_result);
                        $sub_query = "SELECT SUM(AOvertime) FROM AttendanceMaster WHERE AttendanceMaster.ADate >= " . insertDate($txtFrom) . " AND AttendanceMaster.ADate <= " . insertDate($txtTo) . " AND Day=OT1 AND AttendanceMaster.EmployeeID IN (SELECT DISTINCT(e_id) FROM tenter WHERE tenter.p_flag = 1 AND tenter.e_date >= '" . insertDate($txtFrom) . "' AND tenter.e_date <= '" . insertDate($txtTo) . "' AND tenter.g_id = " . $cur[0] . ") ";
                        $sub_result = mysqli_query($sub_query, $conn);
                        $ot2_hour = mysqli_fetch_row($sub_result);
                        $sub_query = "SELECT SUM(AOvertime) FROM AttendanceMaster WHERE AttendanceMaster.ADate >= " . insertDate($txtFrom) . " AND AttendanceMaster.ADate <= " . insertDate($txtTo) . " AND Day=OT2 AND AttendanceMaster.EmployeeID IN (SELECT DISTINCT(e_id) FROM tenter WHERE tenter.p_flag = 1 AND tenter.e_date >= '" . insertDate($txtFrom) . "' AND tenter.e_date <= '" . insertDate($txtTo) . "' AND tenter.g_id = " . $cur[0] . ") ";
                        $sub_result = mysqli_query($sub_query, $conn);
                        $ot3_hour = mysqli_fetch_row($sub_result);
                        $sub_query = "SELECT SUM(AOvertime) FROM AttendanceMaster WHERE AttendanceMaster.ADate >= " . insertDate($txtFrom) . " AND AttendanceMaster.ADate <= " . insertDate($txtTo) . " AND Flag = 'Purple' AND AttendanceMaster.EmployeeID IN (SELECT DISTINCT(e_id) FROM tenter WHERE tenter.p_flag = 1 AND tenter.e_date >= '" . insertDate($txtFrom) . "' AND tenter.e_date <= '" . insertDate($txtTo) . "' AND tenter.g_id = " . $cur[0] . ") ";
                        $sub_result = mysqli_query($sub_query, $conn);
                        $ot4_hour = mysqli_fetch_row($sub_result);
                    }
                }
                print "<tr><td>";
                if ($ii == 6) {
                    if ($prints != "yes") {
                        print "<font face='Verdana' size='1' color='#000000'>" . $cur[1] . "</font>";
                    } else {
                        print "<font face='Verdana' size='1' color='#000000'>" . $cur[1] . "</font>";
                    }
                } else {
                    if ($prints != "yes") {
                        print "<font face='Verdana' size='1' color='#000000'>" . $cur[0] . "</font>";
                    } else {
                        print "<font face='Verdana' size='1' color='#000000'>" . $cur[0] . "</font>";
                    }
                }
                if ($ii == 5) {
                    print "<td bgcolor='#F0F0F0'><a title='Present'><font face='Verdana' size='1'>" . $present_cur . "</font></a></td>";
                    print "<td bgcolor='#F0F0F0'><a title='Normal Hour'><font face='Verdana' size='1'>" . $normal_hour . "</font></a></td>";
                    print "<td bgcolor='#F0F0F0'><a title='Week OT'><font face='Verdana' size='1'>" . $ot1_hour . "</font></a></td>";
                    print "<td bgcolor='#F0F0F0'><a title='SAT OT'><font face='Verdana' size='1'>" . $ot2_hour . "</font></a></td>";
                    print "<td bgcolor='#F0F0F0'><a title='SUN OT'><font face='Verdana' size='1'>" . $ot3_hour . "</font></a></td>";
                    print "<td bgcolor='#F0F0F0'><a title='PH OT'><font face='Verdana' size='1'>" . $ot4_hour . "</font></a></td>";
                } else {
                    print "<td bgcolor='#F0F0F0'><a title='Current Active Employees'><font face='Verdana' size='1'>" . $employee_cur . "</font></a></td>";
                    if ($macAddress != "40-A8-F0-23-F0-AD") {
                        print "<td bgcolor='#F0F0F0'><a title='Current Flagged Employees'><font face='Verdana' size='1'>" . $fda_cur . "</font></a></td>";
                    }
                    if (strpos($rgs, "-RG-") !== false) {
                        print "<td><a title='No of Employees Registered'><font face='Verdana' size='1'>" . $reg_cur[0] . "</font></a></td>";
                        $t_rg = $t_rg + $reg_cur[0];
                    }
                    if (strpos($rgs, "-PRM-") !== false) {
                        print "<td><a title='No of Employees Promoted'><font face='Verdana' size='1'>" . $prm_cur[0] . "</font></a></td>";
                        $t_prm = $t_prm + $prm_cur[0];
                    }
                    if (strpos($rgs, "-RSN-") !== false) {
                        print "<td><a title='No of Employees Resigned'><font face='Verdana' size='1'>" . $rsn_cur[0] . "</font></a></td>";
                        $t_rsn = $t_rsn + $rsn_cur[0];
                    }
                    if (strpos($rgs, "-RTD-") !== false) {
                        print "<td><a title='No of Employees Registered'><font face='Verdana' size='1'>" . $rtd_cur[0] . "</font></a></td>";
                        $t_rtd = $t_rtd + $rtd_cur[0];
                    }
                    if (strpos($rgs, "-TRM-") !== false) {
                        print "<td><a title='No of Employees Registered'><font face='Verdana' size='1'>" . $trm_cur[0] . "</font></a></td>";
                        $t_trm = $t_trm + $trm_cur[0];
                    }
                    if (strpos($rgs, "-P-") !== false) {
                        print "<td bgcolor='#F0F0F0'><a title='Total Present Employees'><font face='Verdana' size='1'>" . $present_cur . "</font></a></td>";
                        $t_p = $t_p + $present_cur;
                    }
                    if (strpos($rgs, "-A-") !== false) {
                        print "<td><a title='Absent Employees'><font face='Verdana' size='1'>" . $abs_cur . "</font></a></td>";
                        $t_a = $t_a + $abs_cur;
                    }
                    if (strpos($rgs, "-A/F-") !== false && $txtFrom == $txtTo) {
                        print "<td><a title='Absent Employees EXCLUDING Flagged Employees'><font face='Verdana' size='1'>" . $abs_pre_cur . "</font></a></td> ";
                        $t_af = $t_af + $abs_pre_cur;
                    }
                    if (strpos($rgs, "-BK-") !== false) {
                        print "<td bgcolor='#F0F0F0'><a title='Black'><font face='Verdana' size='1'>" . ($black_cur[0] + $black_pre_cur[0]) . "</font></a></td>";
                        $t_bk = $t_bk + $black_cur[0] + $black_pre_cur[0];
                    }
                    if (strpos($rgs, "-PX-") !== false) {
                        print "<td bgcolor='#F0F0F0'><a title='Proxy'><font face='Verdana' size='1'>" . ($proxy_cur[0] + $proxy_pre_cur[0]) . "</font></a></td>";
                        $t_px = $t_px + $proxy_cur[0] + $proxy_pre_cur[0];
                    }
                    if (strpos($rgs, "-V-") !== false) {
                        print "<td><a title='Violet'><font face='Verdana' size='1' color='Violet'>" . ($violet_cur[0] + $violet_pre_cur[0]) . "</font></a></td>";
                        $t_v = $t_v + $violet_cur[0] + $violet_pre_cur[0];
                    }
                    if (strpos($rgs, "-I-") !== false) {
                        print "<td><a title='Indigo'><font face='Verdana' size='1' color='Indigo'>" . ($indigo_cur[0] + $indigo_pre_cur[0]) . "</font></a></td>";
                        $t_i = $t_i + $indigo_cur[0] + $indigo_pre_cur[0];
                    }
                    if (strpos($rgs, "-B-") !== false) {
                        print "<td><a title='Blue'><font face='Verdana' size='1' color='Blue'>" . ($blue_cur[0] + $blue_pre_cur[0]) . "</font></a></td>";
                        $t_b = $t_b + $blue_cur[0] + $blue_pre_cur[0];
                    }
                    if (strpos($rgs, "-G-") !== false) {
                        print "<td><a title='Green'><font face='Verdana' size='1' color='Green'>" . ($green_cur[0] + $green_pre_cur[0]) . "</font></a></td>";
                        $t_g = $t_g + $green_cur[0] + $green_pre_cur[0];
                    }
                    if (strpos($rgs, "-Y-") !== false) {
                        print "<td bgcolor='Brown'><a title='Yellow'><font face='Verdana' size='1' color='Yellow'>" . ($yellow_cur[0] + $yellow_pre_cur[0]) . "</font></a></td>";
                        $t_y = $t_y + $yellow_cur[0] + $yellow_pre_cur[0];
                    }
                    if (strpos($rgs, "-O-") !== false) {
                        print "<td><a title='Orange'><font face='Verdana' size='1' color='Orange'>" . ($orange_cur[0] + $orange_pre_cur[0]) . "</font></a></td>";
                        $t_o = $t_o + $orange_cur[0] + $orange_pre_cur[0];
                    }
                    if (strpos($rgs, "-R-") !== false) {
                        print "<td><a title='Red'><font face='Verdana' size='1' color='Red'>" . ($red_cur[0] + $red_pre_cur[0]) . "</font></a></td>";
                        $t_r = $t_r + $red_cur[0] + $red_pre_cur[0];
                    }
                    if (strpos($rgs, "-GR-") !== false) {
                        print "<td><a title='Gray'><font face='Verdana' size='1' color='Gray'>" . ($gray_cur[0] + $gray_pre_cur[0]) . "</font></a></td>";
                        $t_gr = $t_gr + $gray_cur[0] + $gray_pre_cur[0];
                    }
                    if (strpos($rgs, "-BR-") !== false) {
                        print "<td><a title='Brown'><font face='Verdana' size='1' color='Brown'>" . ($brown_cur[0] + $brown_pre_cur[0]) . "</font></a></td>";
                        $t_br = $t_br + $brown_cur[0] + $brown_pre_cur[0];
                    }
                    if (strpos($rgs, "-PR-") !== false) {
                        print "<td><a title='Purple'><font face='Verdana' size='1' color='Purple'>" . ($purple_cur[0] + $purple_pre_cur[0]) . "</font></a></td>";
                        $t_pr = $t_pr + $purple_cur[0] + $purple_pre_cur[0];
                    }
                    if (strpos($rgs, "-MG-") !== false) {
                        print "<td><a title='Magenta'><font face='Verdana' size='1' color='Magenta'>" . ($magenta_cur[0] + $magenta_pre_cur[0]) . "</font></a></td>";
                        $t_mg = $t_mg + $magenta_cur[0] + $magenta_pre_cur[0];
                    }
                    if (strpos($rgs, "-TL-") !== false) {
                        print "<td><a title='Teal'><font face='Verdana' size='1' color='Teal'>" . ($teal_cur[0] + $teal_pre_cur[0]) . "</font></a></td>";
                        $t_tl = $t_tl + $teal_cur[0] + $teal_pre_cur[0];
                    }
                    if (strpos($rgs, "-AQ-") !== false) {
                        print "<td><a title='Aqua'><font face='Verdana' size='1' color='Aqua'>" . ($aqua_cur[0] + $aqua_pre_cur[0]) . "</font></a></td>";
                        $t_aq = $t_aq + $aqua_cur[0] + $aqua_pre_cur[0];
                    }
                    if (strpos($rgs, "-SF-") !== false) {
                        print "<td><a title='Safron'><font face='Verdana' size='1' color='Safron'>" . ($safron_cur[0] + $safron_pre_cur[0]) . "</font></a></td>";
                        $t_sf = $t_sf + $safron_cur[0] + $safron_pre_cur[0];
                    }
                    if (strpos($rgs, "-AM-") !== false) {
                        print "<td><a title='Amber'><font face='Verdana' size='1' color='Amber'>" . ($amber_cur[0] + $amber_pre_cur[0]) . "</font></a></td>";
                        $t_am = $t_am + $amber_cur[0] + $amber_pre_cur[0];
                    }
                    if (strpos($rgs, "-GL-") !== false) {
                        print "<td><a title='Gold'><font face='Verdana' size='1' color='Gold'>" . ($gold_cur[0] + $gold_pre_cur[0]) . "</font></a></td>";
                        $t_gl = $t_gl + $gold_cur[0] + $gold_pre_cur[0];
                    }
                    if (strpos($rgs, "-VM-") !== false) {
                        print "<td><a title='Vermilion'><font face='Verdana' size='1' color='Vermilion'>" . ($vermilion_cur[0] + $vermilion_pre_cur[0]) . "</font></a></td>";
                        $t_vm = $t_vm + $vermilion_cur[0] + $vermilion_pre_cur[0];
                    }
                    if (strpos($rgs, "-SL-") !== false) {
                        print "<td><a title='Silver'><font face='Verdana' size='1' color='Silver'>" . ($silver_cur[0] + $silver_pre_cur[0]) . "</font></a></td>";
                        $t_sl = $t_sl + $silver_cur[0] + $silver_pre_cur[0];
                    }
                    if (strpos($rgs, "-MR-") !== false) {
                        print "<td><a title='Maroon'><font face='Verdana' size='1' color='Maroon'>" . ($maroon_cur[0] + $maroon_pre_cur[0]) . "</font></a></td>";
                        $t_mr = $t_mr + $maroon_cur[0] + $maroon_pre_cur[0];
                    }
                    if (strpos($rgs, "-PK-") !== false) {
                        print "<td><a title='Pink'><font face='Verdana' size='1' color='Pink'>" . ($pink_cur[0] + $pink_pre_cur[0]) . "</font></a></td>";
                        $t_pk = $t_pk + $pink_cur[0] + $pink_pre_cur[0];
                    }
                    if (strpos($rgs, "-FLG-") !== false) {
                        $this_t_flag = $violet_cur[0] + $indigo_cur[0] + $blue_cur[0] + $green_cur[0] + $yellow_cur[0] + $orange_cur[0] + $red_cur[0] + $gray_cur[0] + $brown_cur[0] + $purple_cur[0] + $magenta_cur[0] + $teal_cur[0] + $aqua_cur[0] + $safron_cur[0] + $amber_cur[0] + $gold_cur[0] + $vermilion_cur[0] + $silver_cur[0] + $maroon_cur[0] + $pink_cur[0] + $violet_pre_cur[0] + $indigo_pre_cur[0] + $blue_pre_cur[0] + $green_pre_cur[0] + $yellow_pre_cur[0] + $orange_pre_cur[0] + $red_pre_cur[0] + $gray_pre_cur[0] + $brown_pre_cur[0] + $purple_pre_cur[0] + $magenta_pre_cur[0] + $teal_pre_cur[0] + $aqua_pre_cur[0] + $safron_pre_cur[0] + $amber_pre_cur[0] + $gold_pre_cur[0] + $vermilion_pre_cur[0] + $silver_pre_cur[0] + $maroon_pre_cur[0] + $pink_pre_cur[0];
                        print "<td bgcolor='#F0F0F0'><a title='Total Flagged Days'><font face='Verdana' size='1'>" . ($this_t_flag - $no_flag) . "</font></a></td>";
                        $t_flg = $t_flg + $this_t_flag;
                    }
                    if (strpos($rgs, "-WKD-") !== false) {
                        print "<td><a title='Total Week Days'><font face='Verdana' size='1'>" . $week_cur[0] . "</font></a></td>";
                        $t_wkd = $t_wkd + $week_cur[0];
                    }
                    if (strpos($rgs, "-SAT-") !== false) {
                        print "<td><a title='Total Saturdays'><font face='Verdana' size='1'>" . $sat_cur[0] . "</font></a></td>";
                        $t_sat = $t_sat + $sat_cur[0];
                    }
                    if (strpos($rgs, "-SUN-") !== false) {
                        print "<td><a title='Total Sundays'><font face='Verdana' size='1'>" . $sun_cur[0] . "</font></a></td>";
                        $t_sun = $t_sun + $sun_cur[0];
                    }
                    if (strpos($rgs, "-NS-") !== false) {
                        print "<td bgcolor='#F0F0F0'><a title='Night Shifts'><font face='Verdana' size='1'>" . $night_cur[0] . "</font></a></td>";
                        $t_ns = $t_ns + $night_cur[0];
                    }
                    if (strpos($rgs, "-GC-") !== false) {
                        addComma($sum_cur[0]);
                        print "<td><a title='Total Grace Hours'><font face='Verdana' size='1'>" . addComma($sum_cur[0]) . "</font></a></td>";
                        $t_gc = $t_gc + $sum_cur[0];
                    }
                    if (strpos($rgs, "-LI-") !== false) {
                        addComma($sum_cur[1]);
                        print "<td><a title='Total Late In Hours'><font face='Verdana' size='1'>" . addComma($sum_cur[1]) . "</font></a></td>";
                        $t_li = $t_li + $sum_cur[1];
                    }
                    if (strpos($rgs, "-MB-") !== false) {
                        addComma($sum_cur[2]);
                        print "<td><a title='Total More Break Hours'><font face='Verdana' size='1'>" . addComma($sum_cur[2]) . "</font></a></td>";
                        $t_mb = $t_mb + $sum_cur[2];
                    }
                    if (strpos($rgs, "-EO-") !== false) {
                        addComma($sum_cur[3]);
                        print "<td><a title='Total Early Out Hours'><font face='Verdana' size='1'>" . addComma($sum_cur[3]) . "</font></a></td>";
                        $t_eo = $t_eo + $sum_cur[3];
                    }
                    if (strpos($rgs, "-AO-") !== false) {
                        print "<td>";
                        if ($ii == 6) {
                            if ($prints != "yes") {
                                addComma($sum_cur[4]);
                                print "<a target='_blank' title='Total Approved Overtime Hours: Click Here to View Employee Details' href='ReportWork.php?act=searchRecord&prints=yes&excel=yes&subReport=yes&lstSummary=yes&lstGroup=" . $cur[1] . "&txtFrom=" . $txtFrom . "&txtTo=" . $txtTo . "&lstEmployeeStatus=&lstSort=tuser.id, AttendanceMaster.ADate'><font face='Verdana' size='1' color='#000000'>" . addComma($sum_cur[4]) . "</font></a>";
                            } else {
                                addComma($sum_cur[4]);
                                print "<font face='Verdana' size='1' color='#000000'>" . addComma($sum_cur[4]) . "</font>";
                            }
                        } else {
                            if ($prints != "yes") {
                                print "<a target='_blank' title='Total Approved Overtime Hours: Click Here to View Employee Details' href='ReportWork.php?act=searchRecord&prints=yes&excel=yes&subReport=yes&lstSummary=yes&";
                                switch ($ii) {
                                    case 0:
                                        print "lstDivision";
                                        break;
                                    case 1:
                                        print "lstDepartment";
                                        break;
                                    case 2:
                                        print "txtRemark";
                                        break;
                                    case 3:
                                        print "txtPhone";
                                        break;
                                    case 4:
                                        print "txtSNo";
                                        break;
                                    case 6:
                                        print "lstDepartment";
                                        break;
                                }
                                addComma($sum_cur[4]);
                                print "=" . $cur[0] . "&txtFrom=" . $txtFrom . "&txtTo=" . $txtTo . "&lstEmployeeStatus=&lstSort=tuser.id, AttendanceMaster.ADate'><font face='Verdana' size='1' color='#000000'>" . addComma($sum_cur[4]) . "</font></a>";
                            } else {
                                addComma($sum_cur[4]);
                                print "<font face='Verdana' size='1' color='#000000'>" . addComma($sum_cur[4]) . "</font>";
                            }
                        }
                        print "</td>";
                        $t_ao = $t_ao + $sum_cur[4];
                    }
                    print "</tr>";
                    $counter++;
                }
                print "<tr>";
                print "<td><font face='Verdana' size='1'><b>Totals</b></font></td>";
                print "<td bgcolor='#F0F0F0'><font face='Verdana' size='1'><b>" . $t_ae . "</b></font></td>";
                if ($macAddress != "40-A8-F0-23-F0-AD") {
                    print "<td bgcolor='#F0F0F0'><font face='Verdana' size='1'><b>" . $t_fda . "</b></font></td>";
                }
                if (strpos($rgs, "-RG-") !== false) {
                    print "<td><a title='No of Employees Registered'><font face='Verdana' size='1'><b>" . $t_rg . "</b></font></a></td>";
                }
                if (strpos($rgs, "-PRM-") !== false) {
                    print "<td><a title='No of Employees Promoted'><font face='Verdana' size='1'><b>" . $t_prm . "</b></font></a></td>";
                }
                if (strpos($rgs, "-RSN-") !== false) {
                    print "<td><a title='No of Employees Resigned'><font face='Verdana' size='1'><b>" . $t_rsn . "</b></font></a></td>";
                }
                if (strpos($rgs, "-RTD-") !== false) {
                    print "<td><a title='No of Employees Retired'><font face='Verdana' size='1'><b>" . $t_rtd . "</b></font></a></td>";
                }
                if (strpos($rgs, "-TRM-") !== false) {
                    print "<td><a title='No of Employees Terminated'><font face='Verdana' size='1'><b>" . $t_trm . "</b></font></a></td>";
                }
                if (strpos($rgs, "-P-") !== false && $txtFrom == $txtTo) {
                    print "<td bgcolor='#F0F0F0'><a title='Total Present Employees'><font face='Verdana' size='1'><b>" . $t_p . "</b></font></a></td>";
                }
                if (strpos($rgs, "-A-") !== false) {
                    print "<td><a title='Absent Employees'><font face='Verdana' size='1'><b>" . $t_a . "</b></font></a></td>";
                }
                if (strpos($rgs, "-A/F-") !== false && $txtFrom == $txtTo) {
                    print "<td><a title='Absent Employees EXCLUDING Flagged Employees'><font face='Verdana' size='1'><b>" . $t_af . "</b></font></a></td>";
                }
                if (strpos($rgs, "-BK-") !== false) {
                    print "<td bgcolor='#F0F0F0'><a title='Black'><font face='Verdana' size='1'><b>" . $t_bk . "</b></font></a></td>";
                }
                if (strpos($rgs, "-PX-") !== false) {
                    print "<td bgcolor='#F0F0F0'><a title='Proxy'><font face='Verdana' size='1'><b>" . $t_px . "</b></font></a></td>";
                }
                if (strpos($rgs, "-V-") !== false) {
                    print "<td><a title='Violet'><font face='Verdana' size='1' color='Violet'><b>" . $t_v . "</b></font></a></td>";
                }
                if (strpos($rgs, "-I-") !== false) {
                    print "<td><a title='Indigo'><font face='Verdana' size='1' color='Indigo'><b>" . $t_i . "</b></font></a></td>";
                }
                if (strpos($rgs, "-B-") !== false) {
                    print "<td><a title='Blue'><font face='Verdana' size='1' color='Blue'><b>" . $t_b . "</b></font></a></td>";
                }
                if (strpos($rgs, "-G-") !== false) {
                    print "<td><a title='Green'><font face='Verdana' size='1' color='Green'><b>" . $t_g . "</b></font></a></td>";
                }
                if (strpos($rgs, "-Y-") !== false) {
                    print "<td bgcolor='Brown'><a title='Yellow'><font face='Verdana' size='1' color='Yellow'><b>" . $t_y . "</b></font></a></td>";
                }
                if (strpos($rgs, "-O-") !== false) {
                    print "<td><a title='Orange'><font face='Verdana' size='1' color='Orange'><b>" . $t_o . "</b></font></a></td>";
                }
                if (strpos($rgs, "-R-") !== false) {
                    print "<td><a title='Red'><font face='Verdana' size='1' color='Red'><b>" . $t_r . "</b></font></a></td>";
                }
                if (strpos($rgs, "-GR-") !== false) {
                    print "<td><a title='Gray'><font face='Verdana' size='1' color='Gray'><b>" . $t_gr . "</b></font></a></td>";
                }
                if (strpos($rgs, "-BR-") !== false) {
                    print "<td><a title='Brown'><font face='Verdana' size='1' color='Brown'><b>" . $t_br . "</b></font></a></td>";
                }
                if (strpos($rgs, "-PR-") !== false) {
                    print "<td><a title='Purple'><font face='Verdana' size='1' color='Purple'><b>" . $t_pr . "</b></font></a></td>";
                }
                if (strpos($rgs, "-MG-") !== false) {
                    print "<td><a title='Magenta'><font face='Verdana' size='1' color='Magenta'><b>" . $t_mg . "</b></font></a></td>";
                }
                if (strpos($rgs, "-TL-") !== false) {
                    print "<td><a title='Teal'><font face='Verdana' size='1' color='Teal'><b>" . $t_tl . "</b></font></a></td>";
                }
                if (strpos($rgs, "-AQ-") !== false) {
                    print "<td><a title='Aqua'><font face='Verdana' size='1' color='Aqua'><b>" . $t_aq . "</b></font></a></td>";
                }
                if (strpos($rgs, "-SF-") !== false) {
                    print "<td><a title='Safron'><font face='Verdana' size='1' color='Safron'><b>" . $t_sf . "</b></font></a></td>";
                }
                if (strpos($rgs, "-AM-") !== false) {
                    print "<td><a title='Amber'><font face='Verdana' size='1' color='Amber'><b>" . $t_am . "</b></font></a></td>";
                }
                if (strpos($rgs, "-GL-") !== false) {
                    print "<td><a title='Gold'><font face='Verdana' size='1' color='Gold'><b>" . $t_gl . "</b></font></a></td>";
                }
                if (strpos($rgs, "-VM-") !== false) {
                    print "<td><a title='Vermilion'><font face='Verdana' size='1' color='Vermilion'><b>" . $t_vm . "</b></font></a></td>";
                }
                if (strpos($rgs, "-SL-") !== false) {
                    print "<td><a title='Silver'><font face='Verdana' size='1' color='Silver'><b>" . $t_sl . "</b></font></a></td>";
                }
                if (strpos($rgs, "-MR-") !== false) {
                    print "<td><a title='Maroon'><font face='Verdana' size='1' color='Maroon'><b>" . $t_mr . "</b></font></a></td>";
                }
                if (strpos($rgs, "-PK-") !== false) {
                    print "<td><a title='Pink'><font face='Verdana' size='1' color='Pink'><b>" . $t_pk . "</b></font></a></td>";
                }
                if (strpos($rgs, "-FLG-") !== false) {
                    print "<td bgcolor='#F0F0F0'><a title='Total Flagged Days'><font face='Verdana' size='1'><b>" . $t_flg . "</b></font></a></td>";
                }
                if (strpos($rgs, "-WKD-") !== false) {
                    print "<td><a title='Total Week Days'><font face='Verdana' size='1'><b>" . $t_wkd . "</b></font></a></td>";
                }
                if (strpos($rgs, "-SAT-") !== false) {
                    print "<td><a title='Total Saturdays'><font face='Verdana' size='1'><b>" . $t_sat . "</b></font></a></td>";
                }
                if (strpos($rgs, "-SUN-") !== false) {
                    print "<td><a title='Total Sundays'><font face='Verdana' size='1'><b>" . $t_sun . "</b></font></a></td>";
                }
                if (strpos($rgs, "-NS-") !== false) {
                    print "<td bgcolor='#F0F0F0'><a title='Night Shifts'><font face='Verdana' size='1'><b>" . $t_ns . "</b></font></a></td>";
                }
                if (strpos($rgs, "-GC-") !== false) {
                    addComma($t_gc);
                    print "<td><a title='Total Grace Hours'><font face='Verdana' size='1'><b>" . addComma($t_gc) . "</b></font></a></td>";
                }
                if (strpos($rgs, "-LI-") !== false) {
                    addComma($t_li);
                    print "<td><a title='Total Late In Hours'><font face='Verdana' size='1'><b>" . addComma($t_li) . "</b></font></a></td>";
                }
                if (strpos($rgs, "-MB-") !== false) {
                    addComma($t_mb);
                    print "<td><a title='Total More Break Hours'><font face='Verdana' size='1'><b>" . addComma($t_mb) . "</b></font></a></td>";
                }
                if (strpos($rgs, "-EO-") !== false) {
                    addComma($t_eo);
                    print "<td><a title='Total Early Out Hours'><font face='Verdana' size='1'><b>" . addComma($t_eo) . "</b></font></a></td>";
                }
                if (strpos($rgs, "-AO-") !== false) {
                    addComma($t_ao);
                    print "<td><a title='Total Approved Overtime Hours'><font face='Verdana' size='1'><b>" . addComma($t_ao) . "</b></font></a></td>";
                }
            }
            print "</tr>";
            print "</table>";
            $t_ae = 0;
            $t_rg = 0;
            $t_prm = 0;
            $t_rsn = 0;
            $t_rtd = 0;
            $t_trm = 0;
            $t_bk = 0;
            $t_px = 0;
            $t_v = 0;
            $t_i = 0;
            $t_b = 0;
            $t_g = 0;
            $t_y = 0;
            $t_o = 0;
            $t_r = 0;
            $t_gr = 0;
            $t_br = 0;
            $t_pr = 0;
            $t_mg = 0;
            $t_tl = 0;
            $t_aq = 0;
            $t_sf = 0;
            $t_am = 0;
            $t_gl = 0;
            $t_vm = 0;
            $t_sl = 0;
            $t_mr = 0;
            $t_pk = 0;
            $t_flg = 0;
            $t_wkd = 0;
            $t_sat = 0;
            $t_sun = 0;
            $t_p = 0;
            $t_a = 0;
            $t_af = 0;
            $t_ns = 0;
            $t_gc = 0;
            $t_li = 0;
            $t_mb = 0;
            $t_eo = 0;
            $t_ao = 0;
            $normal_hour;
            $ot1_hour;
            $ot2_hour;
            $ot3_hour;
            $ot4_hour;
            if ($excel != "yes") {
                print "&nbsp;<font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $counter . "</b></font>";
            }
        }
    }
    if ($prints != "yes") {
        print "<br><input type='button' value='Print Report' onClick='checkPrint(0)'>&nbsp;&nbsp;<input type='button' value='Excel' onClick='checkPrint(1)'>";
    }
    print "</div></div></div></div>";
    print "</p>";
}
print "</form>";
print "</div>";
include 'footer.php';
?>