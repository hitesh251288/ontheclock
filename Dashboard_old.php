<?php

ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
include "DashboardScript.php";
session_start();
$current_module = "18";
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
$ex3 = $_SESSION[$session_variable . "Ex3"];
if (!is_numeric($ex3)) {
    $ex3 = 0;
}
$ex4 = "";
if ($ex4 == "") {
    $ex4 = "120";
}
if (!is_numeric($ex4)) {
    $ex4 = 120;
}
$lstEmployeeStatus = "ACT";
$lstClockingType = "All";
$count = 0;
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=Dashboard.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$act = $_GET["act"];

//Total Employee
$querytotal = "SELECT * from tuser WHERE PassiveType='ACT'";
$resulttotal = mysqli_query($conn, $querytotal);
$row_cnt = $resulttotal->num_rows;
$current_date = date("Ymd");

//Present
$query = "SELECT NightShiftMaxOutTime FROM OtherSettingMaster";
$result = selectData($conn, $query);
$cutoff = $result[0];
$querypresent = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, "
        . "tenter.e_date, MIN(tenter.e_time) as InTime,MAX(tenter.e_time) as OutTime, "
        . "tgate.name, tuser.idno, tuser.remark, tuser.phone FROM tuser, tgroup, "
        . "tenter, tgate WHERE tenter.e_group = tgroup.id AND tenter.e_id = tuser.id AND "
        . "tenter.g_id = tgate.id AND tgate.exit = 0 AND ((tenter.e_time > '" . $cutoff . "00' "
        . "AND tgroup.NightFlag = 1) OR "
        . "(tenter.e_time > '000000' AND tgroup.NightFlag = 0)) "
        . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . "  "
        . "AND tuser.id NOT IN (SELECT e_id FROM FlagDayRotation WHERE e_date = " . $current_date . ")  "
        . "AND tenter.e_date = '" . $current_date . "' AND tuser.PassiveType = '" . $lstEmployeeStatus . "' GROUP BY tuser.id,tenter.e_date";
$resultpresent = mysqli_query($conn, $querypresent);
$presentrow_cnt = $resultpresent->num_rows;

//Absent
$queryabsent = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, "
        . "tuser.idno, tuser.remark, tuser.PassiveType, tuser.F1, tuser.F2, "
        . "tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, "
        . "tuser.F10 FROM tuser, tgroup WHERE "
        . "SUBSTRING(tuser.datelimit, 2, 8) < '" . $current_date . "0000' AND "
        . "tuser.group_id = tgroup.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " "
        . "" . $_SESSION[$session_variable . "DivAccessQuery"] . "  AND tuser.OT1 NOT "
        . "LIKE '" . getDay(displayDate($current_date)) . "' AND tuser.OT2 NOT "
        . "LIKE '" . getDay(displayDate($current_date)) . "'  AND tuser.id NOT "
        . "IN (SELECT e_id FROM FlagDayRotation WHERE e_date = " . $current_date . ")  "
        . "AND tuser.id NOT IN (SELECT DISTINCT(tenter.e_id) FROM tenter, tgate, "
        . "tgroup WHERE tenter.g_id = tgate.id AND tenter.e_group = tgroup.id AND "
        . "tgate.exit = 0 AND ((tenter.e_time > '" . $cutoff . "00' AND tgroup.NightFlag = 1) "
        . "OR (tenter.e_time > '000000' AND tgroup.NightFlag = 0)) AND tenter.e_date = '" . $current_date . "') AND tuser.PassiveType = 'ACT'";
$resultabsent = mysqli_query($conn, $queryabsent);
$absentrow_cnt = $resultabsent->num_rows;

//Late In
$querylate = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, "
        . "tenter.e_date, tenter.e_time, tgate.name, tuser.idno, tuser.remark, "
        . "tgroup.GraceTo, tgroup.Start, tuser.F1, tuser.F2, tuser.F3, tuser.F4, "
        . "tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9, tuser.F10 FROM tuser, "
        . "tgroup, tenter, tgate WHERE tenter.e_group = tgroup.id AND tenter.e_id = tuser.id "
        . "AND tgroup.GraceTo >= '0000' AND tenter.g_id = tgate.id  AND "
        . "tgate.exit = 0 " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . "  "
        . "AND tenter.e_date = '" . $current_date . "' AND tuser.PassiveType = '" . $lstEmployeeStatus . "' ORDER BY tuser.id, tenter.e_date, tenter.e_time";

$last_id = "";
$last_date = "";
$resultlate = mysqli_query($conn, $querylate);

while ($cur = mysqli_fetch_row($resultlate)) {
    if (!($cur[0] == $last_id && $cur[5] == $last_date)) {
        if (getLateTime($current_date, $cur[10], $ex3) < $cur[6] && $cur[6] < getLateTime($current_date, $cur[10], $ex4) && 0 < getLateMin($current_date, $cur[10], $cur[6])) {
            if ($cur[3] == "") {
                $cur[3] = "&nbsp;";
            }
            if ($cur[8] == "") {
                $cur[8] = "&nbsp;";
            }
            if ($cur[9] == "") {
                $cur[9] = "&nbsp;";
            }
            $count++;
        }
        $last_id = $cur[0];
        $last_date = $cur[5];
    }
}

//Early Exit
$queryEarlyexit = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, "
        . "AttendanceMaster.ADate, AttendanceMaster.Day, AttendanceMaster.Week, "
        . "AttendanceMaster.EarlyIn, AttendanceMaster.LateIn, "
        . "AttendanceMaster.Break, AttendanceMaster.LessBreak, AttendanceMaster.MoreBreak, "
        . "AttendanceMaster.EarlyOut, AttendanceMaster.LateOut, AttendanceMaster.Normal, "
        . "AttendanceMaster.Grace, AttendanceMaster.Overtime, AttendanceMaster.AOvertime, "
        . "AttendanceMaster.Flag, tuser.idno, tuser.remark, AttendanceMaster.LateIn_flag, "
        . "AttendanceMaster.EarlyOut_flag, AttendanceMaster.MoreBreak_flag, "
        . "AttendanceMaster.Remark, tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, "
        . "tuser.F8, tuser.F9, tuser.F10 FROM tuser, tgroup, AttendanceMaster "
        . "WHERE AttendanceMaster.group_id = tgroup.id AND "
        . "AttendanceMaster.EmployeeID = tuser.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " 
        . $_SESSION[$session_variable . "DivAccessQuery"] . " "
        . "AND (AttendanceMaster.ADate >= " . $current_date . " OR "
        . "AttendanceMaster.ADate = 0) AND (AttendanceMaster.ADate <= " . $current_date . " OR "
        . "AttendanceMaster.ADate = 0) AND AttendanceMaster.EarlyOut > 0  AND tuser.PassiveType = '" . $lstEmployeeStatus . "'";

$resultEarlyexit = mysqli_query($conn, $queryEarlyexit);
$row_cnt_earlyexit = $resultEarlyexit->num_rows;

//On Leave
$table_name = "Access.FlagDayRotation";
$queryleave = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, "
        . "tgroup.name, " . $table_name . ".Flag, " . $table_name . ".e_date , "
        . "tgate.name, " . $table_name . ".Remark, " . $table_name . ".Rotate, " . $table_name . ".RecStat, "
        . "tuser.idno, tuser.remark, " . $table_name . ".OT, " . $table_name . ".OTH, "
        . "tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, "
        . "tuser.F9, tuser.F10 FROM tuser, tgroup, " . $table_name . ", tgate "
        . "WHERE tuser.group_id = tgroup.id AND " . $table_name . ".e_id = tuser.id "
        . "AND " . $table_name . ".g_id = tgate.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . "  "
        . "AND " . $table_name . ".e_date  >= '" . $current_date . "' AND " . $table_name . ".e_date  <= '" . $current_date . "' AND "
        . "tuser.PassiveType = '" . $lstEmployeeStatus . "'";
$resultleave = mysqli_query($conn, $queryleave);
$row_cnt_leave = $resultleave->num_rows;

//Department Wise Present
$Account = 0;
$Admin = 0;
$Cleaner = 0;
$Engineering = 0;
$Gm = 0;
$IT = 0;
$Maintenance = 0;
$Notassigned = 0;
$Production = 0;
$QA = 0;
$Security = 0;
$StoreLog = 0;
$offshift = 0;
$morningshift = 0;
$nightshift = 0;
$regularshift = 0;
$regular2shift = 0;
$dayshift = 0;
$firstshift = 0;
$secondshift = 0;
$n2shift = 0;

while ($presentrow = mysqli_fetch_row($resultpresent)) {
    //Department
    if ($presentrow[2] == 'ACCOUNT') {
        $Account++;
    }
    if ($presentrow[2] == 'ADMIN') {
        $Admin++;
    }
    if ($presentrow[2] == 'CLEANER') {
        $Cleaner++;
    }
    if ($presentrow[2] == 'ENGINEERING') {
        $Engineering++;
    }
    if ($presentrow[2] == 'G/M') {
        $Gm++;
    }
    if ($presentrow[2] == 'INFORMATION TECHNOLOGY') {
        $IT++;
    }
    if ($presentrow[2] == 'MAINTENANCE') {
        $Maintenance++;
    }
    if ($presentrow[2] == 'NOT ASSIGNED') {
        $Notassigned++;
    }
    if ($presentrow[2] == 'PRODUCTION') {
        $Production++;
    }
    if ($presentrow[2] == 'Q A') {
        $QA++;
    }
    if ($presentrow[2] == 'SECURITY') {
        $Security++;
    }
    if ($presentrow[2] == 'STORE/LOG') {
        $StoreLog++;
    }

    //Shift
    if ($presentrow[4] == 'OFF') {
        $offshift++;
    }
    if ($presentrow[4] == 'MORNING') {
        $morningshift++;
    }
    if ($presentrow[4] == 'NIGHT') {
        $nightshift++;
    }
    if ($presentrow[4] == 'REGULAR') {
        $regularshift++;
    }
    if ($presentrow[4] == 'REGULAR 2') {
        $regular2shift++;
    }
    if ($presentrow[4] == 'DAY') {
        $dayshift++;
    }
    if ($presentrow[4] == '7AM-2PM') {
        $firstshift++;
    }
    if ($presentrow[4] == '2PM-9PM') {
        $secondshift++;
    }
    if ($presentrow[4] == '9PM-7AM') {
        $n2shift++;
    }
}

$querydept = "SELECT distinct(dept) from tuser " . $_SESSION[$session_variable . "DeptAccessWhereQuery"] . " ORDER BY UPPER(dept)";
$resultdept = mysqli_query($conn, $querydept);
while ($rowdept = mysqli_fetch_assoc($resultdept)) {
    $dept[] = $rowdept['dept'];
}

$queryshift = "SELECT DISTINCT(name), id FROM tgroup";
$resultshift = mysqli_query($conn, $queryshift);
while ($rowshift = mysqli_fetch_assoc($resultshift)) {
    $shift[] = $rowshift['name'];
}
$offabsent = 0;
$morningabsent = 0;
$nightabsent = 0;
$regularabsent = 0;
$regular2absent = 0;
$dayabsent = 0;
$firstabsent = 0;
$secondabsent = 0;
$n2absent = 0;

while ($rowabsent = mysqli_fetch_row($resultabsent)) {
    if ($rowabsent[4] == 'OFF') {
        $offabsent++;
    }
    if ($rowabsent[4] == 'MORNING') {
        $morningabsent++;
    }
    if ($rowabsent[4] == 'NIGHT') {
        $nightabsent++;
    }
    if ($rowabsent[4] == 'REGULAR') {
        $regularabsent++;
    }
    if ($rowabsent[4] == 'REGULAR 2') {
        $regular2absent++;
    }
    if ($rowabsent[4] == 'DAY') {
        $dayabsent++;
    }
    if ($rowabsent[4] == '7AM-2PM') {
        $firstabsent++;
    }
    if ($rowabsent[4] == '2PM-9PM') {
        $secondabsent++;
    }
    if ($rowabsent[4] == '9PM-7AM') {
        $n2absent++;
    }
}
?>
<html>
    <head>
        <title>Dashboard</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!--<link rel="shortcut icon" href="img/favicon.png">-->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <!--<link rel="stylesheet" href="hitesh/font-awesome/font-awesome.min.css">-->
        <link rel="stylesheet" href="resource/css/bootstrap.min.css">

        <script src="resource/js/jquery.min.js"></script>
        <script src="resource/js/bootstrap.min.js"></script>
    <center><?php displayHeader($prints, true, false); ?></center>
    <center><?php displayLinks($current_module, $userlevel); ?></center>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-lg-3"></div>
            <div class="col-lg-2 col-xs-12 col-sm-4 col-md-2">
                <div class="small-box small-box-1">
                    <div class="inner">
                        <h3 class="widgetTotalCount" id="txtHeadCount"><?php echo $row_cnt; ?></h3>    
                        <p class="widgetTitle">Total Employee</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-users"></i>
                    </div>
                    <a href="ReportEmployee.php" class="small-box-footer" tabindex="0" target="_blank">View Details&nbsp;<i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-xs-12 col-sm-4 col-md-2">
                <div class="small-box small-box-2">
                    <div class="inner">
                        <h3 class="widgetTotalCount" id="txtPresent"><?php echo $presentrow_cnt; ?></h3>
                        <p class="widgetTitle">Present</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-check-square-o"></i>
                    </div>
                    <a href="ReportAttendance.php" class="small-box-footer" tabindex="0" target="_blank">View Details&nbsp;<i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-xs-12 col-sm-4 col-md-2">
                <div class="small-box small-box-3">
                    <div class="inner">
                        <h3 class="widgetTotalCount" id="txtAbsent"><?php echo $absentrow_cnt; ?></h3>
                        <p class="widgetTitle">Absent</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-ban"></i>
                    </div>
                    <a href="ReportAbsence.php" class="small-box-footer" tabindex="0" target="_blank">View Details&nbsp;<i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3"></div>
        </div>
        <div class="row">
            <div class="col-lg-3 col-xs-12 col-sm-4 col-md-2"></div>
            <div class="col-lg-2 col-xs-12 col-sm-4 col-md-2">
                <div class="small-box small-box-4">
                    <div class="inner">
                        <h3 class="widgetTotalCount" id="txtLateIn"><?php echo $count; ?></h3>
                        <p class="widgetTitle">Late In</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-clock-o"></i>
                    </div>
                    <a href="ReportLateArrival.php" class="small-box-footer" tabindex="0" target="_blank">View Details&nbsp;<i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-xs-12 col-sm-4 col-md-2">
                <div class="small-box small-box-5">
                    <div class="inner">
                        <h3 class="widgetTotalCount" id="txtEarlyOut"><?php echo $row_cnt_earlyexit; ?></h3>
                        <p class="widgetTitle">Early Out</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-sign-out"></i>
                    </div>
                    <a href="ReportWork.php" class="small-box-footer" tabindex="0" target="_blank">View Details&nbsp;<i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-xs-12 col-sm-4 col-md-2">
                <div class="small-box small-box-6">
                    <div class="inner">
                        <h3 class="widgetTotalCount" id="txtOnLeave"><?php echo $row_cnt_leave; ?></h3>
                        <p class="widgetTitle">On Leave</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-user"></i>
                    </div>
                    <a href="ReportPreFlag.php" class="small-box-footer" tabindex="0" target="_blank">View Details&nbsp;<i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-xs-12 col-sm-4 col-md-2"></div>
        </div>
        <div class="row">
            <!--<div class="col-lg-1"></div>-->
            <center><div class="graph">
                    <div class="box box-success box1">
                        <div class="box-header with-border">
                            <!--<i class="fa fa-refresh pull-right" id="presentdetail"></i>-->
                            <h4 class="box-title">Department Wise Presents </h4>
                            <div class="box-tools pull-right">
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="overlay">
                                <i class="fa fa-refresh fa-spin"></i>
                            </div>
                        </div>
                        <div class="grid">
                            <div id="piechart" style="width: 700px; height: 410px;"></div>
                        </div>
                    </div>
                </div></center>
            <!--<div class="col-lg-2"></div>-->
        </div>
        <div class="row">
            <!--<div class="col-lg-2"></div>-->
            <center><div class="graph">
                    <div class="box box-success box2">
                        <div class="box-header with-border">
                            <h4 class="box-title">Shift Wise Schedule V/S Presents <i class="fa fa-refresh pull-right" id="detailed"></i></h4>
                            <div class="box-tools pull-right">
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="overlay" style="display: none;">
                                <i class="fa fa-refresh fa-spin"></i>
                            </div>
                            <div class="grid">
                                <div id="shiftWiseScheduleVsPresentsGrid">
                                    <div class="table-responsive">
                                        <table id="ShiftWiseScheduleVsPresentsTable" class="datatable table table-responsive table-enquest table-striped table-bordered table-hover dataTable">
                                            <thead>
                                                <tr>
                                                    <th>Shift</th>
                                                    <th>Present</th>
                                                    <th>Absent</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="empty-row">
                                                    <td><?php echo $shift[2]; ?></td>
                                                    <td><?php echo $offshift; ?></td>
                                                    <td><?php echo $offabsent; ?></td>
                                                </tr>
                                                <tr class="empty-row">
                                                    <td><?php echo $shift[3]; ?></td>
                                                    <td><?php echo $morningshift; ?></td>
                                                    <td><?php echo $morningabsent; ?></td>
                                                </tr>
                                                <tr class="empty-row">
                                                    <td><?php echo $shift[4]; ?></td>
                                                    <td><?php echo $nightshift; ?></td>
                                                    <td><?php echo $nightabsent; ?></td>
                                                </tr>
                                                <tr class="empty-row">
                                                    <td><?php echo $shift[5]; ?></td>
                                                    <td><?php echo $regularshift; ?></td>
                                                    <td><?php echo $regularabsent; ?></td>
                                                </tr>
                                                <tr class="empty-row">
                                                    <td><?php echo $shift[7]; ?></td>
                                                    <td><?php echo $regular2shift; ?></td>
                                                    <td><?php echo $regular2absent; ?></td>
                                                </tr>
                                                <tr class="empty-row">
                                                    <td><?php echo $shift[8]; ?></td>
                                                    <td><?php echo $dayshift; ?></td>
                                                    <td><?php echo $dayabsent; ?></td>
                                                </tr>
                                                <tr class="empty-row">
                                                    <td><?php echo $shift[9]; ?></td>
                                                    <td><?php echo $firstshift; ?></td>
                                                    <td><?php echo $firstabsent; ?></td>
                                                </tr>
                                                <tr class="empty-row">
                                                    <td><?php echo $shift[10]; ?></td>
                                                    <td><?php echo $secondshift; ?></td>
                                                    <td><?php echo $secondabsent; ?></td>
                                                </tr>
                                                <tr class="empty-row">
                                                    <td><?php echo $shift[11]; ?></td>
                                                    <td><?php echo $n2shift; ?></td>
                                                    <td><?php echo $n2absent; ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div></center>
            <!--<div class="col-lg-2"></div>-->
        </div>
        <div class="row">
            <!--<div class="col-lg-2"></div>-->
            <center><div class="graph">
                    <div class="box box-success box3">
                        <div class="box-header with-border">
                            <!--<i class="fa fa-refresh pull-right" id="attendancestatus"></i>-->
                            <h4 class="box-title">Company Wide Attendance Status </h4>
                            <div class="box-tools pull-right">
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="overlay" style="display: none;">
                                <i class="fa fa-refresh fa-spin"></i>
                            </div>
                        </div>
                        <div class="grid">
                            <div>
                                <div id="piechart_3d" style="width: 700px; height: 400px;"></div>
                                <!--<div id="columnchart"></div>-->
                            </div>
                        </div>
                    </div>
                </div></center>
            <!--<div class="col-lg-2"></div>-->
        </div>
    </div>
    <script type="text/javascript" src="resource/js/loader.js"></script>
    <script>
        google.charts.load('current', {'packages': ['corechart']});
//        google.charts.load('current', {'packages': ['corechart'], callback: drawPieChart});
        google.charts.setOnLoadCallback(drawPieChart);

        function drawPieChart()
        { 
            var pie = google.visualization.arrayToDataTable([
                ['attendance', 'Number'],
                ['<?php echo $dept[0]; ?>', <?php echo $Account; ?>],
                ['<?php echo $dept[1]; ?>', <?php echo $Admin; ?>],
                ['<?php echo $dept[2]; ?>', <?php echo $Cleaner; ?>],
                ['<?php echo $dept[3]; ?>', <?php echo $Engineering; ?>],
                ['<?php echo $dept[4]; ?>', <?php echo $Gm; ?>],
                ['<?php echo $dept[5]; ?>', <?php echo $IT; ?>],
                ['<?php echo $dept[6]; ?>', <?php echo $Maintenance; ?>],
                ['<?php echo $dept[7]; ?>', <?php echo $Notassigned; ?>],
                ['<?php echo $dept[8]; ?>', <?php echo $Production; ?>],
                ['<?php echo $dept[9]; ?>', <?php echo $QA; ?>],
                ['<?php echo $dept[10]; ?>', <?php echo $Security; ?>],
                ['<?php echo $dept[11]; ?>', <?php echo $StoreLog; ?>],
            ]);

            var header = {
//          title: 'Department Wise Presents',
                slices: {0: {color: '#666666'}, 1: {color: '#006EFF'}}
            };
            var piechart = new google.visualization.PieChart(document.getElementById('piechart'));
            piechart.draw(pie, header);
        }
 
//        google.charts.load("current", {packages: ["corechart"]});
        google.charts.setOnLoadCallback(drawChart);
        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Status', 'Count'],
                ['Total Employee',<?php echo $row_cnt; ?>],
                ['Present', <?php echo $presentrow_cnt; ?>],
                ['Absent', <?php echo $absentrow_cnt ?>],
                ['Late In', <?php echo $count; ?>],
                ['Early Out', <?php echo $row_cnt_earlyexit; ?>],
                ['On Leave', <?php echo $row_cnt_leave; ?>],
//          ['Weekly Off',  1]
            ]);

            var options = {
//          title: 'My Daily Activities',
                is3D: true,
            };

            var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));
            chart.draw(data, options);
            
        }
//        $(document).ready(function () {
            $('#presentdetail').click(function () {
                $(".box1 .overlay").show();
                $('#piechart').load('Dashboard.php #piechart', function () {
                    google.charts.load('current', {'packages': ['corechart']});
                    google.charts.setOnLoadCallback(drawPieChart);
//                    var timeout = setTimeout(drawPieChart, 1000);
                    $(".box1 .overlay").hide();
                });
            });
            $('#detailed').click(function () {
                $(".box2 .overlay").show();
                $('#shiftWiseScheduleVsPresentsGrid').load('Dashboard.php #shiftWiseScheduleVsPresentsGrid', function () {
                    $(".box2 .overlay").hide();
                });
            });
            $('#attendancestatus').click(function () {
                $(".box3 .overlay").show();
                $('.box3 .grid #piechart_3d').load('Dashboard.php .box3 .grid #piechart_3d', function () {
                    google.charts.setOnLoadCallback(drawChart);
                    $(".box3 .overlay").hide();
                });
            });
//            setInterval("my_function();",1000); 
//            function my_function(){
//                $('#shiftWiseScheduleVsPresentsGrid').load('Dashboard.php #shiftWiseScheduleVsPresentsGrid');
//            }
//        });
    </script>
</body>
</html>