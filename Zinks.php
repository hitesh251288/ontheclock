<?php 
error_reporting(E_ERROR);
$currentPage = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$menuMap = [
    'Attendance' => ['AlterTime.php', 'Proxy.php', 'PreApproveOvertime.php', 'ApproveOvertime.php', 'ApproveOvertimeSummary.php', 'FlagApplication.php', 'OffDay.php', 'FlagDay.php', 'FlagRoster.php', 'CAGRoster.php', 'ShiftRoster.php', 'DeleteProcessedRecord.php', 'DeleteProcessedRawRecord.php', 'DeletePreFlaggedRecord.php', 'ExemptLateInEarlyOutMoreBreak.php'],
    'Reports' => ['Empreports.php', 'ReportClockingLog.php', 'ReportAttendance.php', 'ReportOddLog.php', 'ReportExitLog.php', 'ReportMoreBreak.php', 'ReportEarlyExit.php', 'ReportLIEO.php', 'ReportAbsenceCount.php', 'MonthlyAttendanceSummary.php', 'EmployeeDailyAttendanceReport.php', 'ReportEmployee.php', 'ReportDailyClocking.php', 'ReportWeeklyClocking.php', 'ReportWork.php', 'ReportDailyRoster.php', 'ReportPeriodicSummary.php', 'ReportMonthlyHours.php', 'ReportMonthSummary.php', 'ReportShiftSnapShot.php', 'ReportGroupSummary.php', 'ReportAttendanceSnapShot.php', 'AttendanceSummary.php', 'ReportPreFlag.php', 'ReportProject.php', 'ReportPreApproval.php', 'ReportAlterTime.php', 'ReportFlagLimit.php', 'ReportADA.php', 'ReportUserTransact.php', 'ReportUserInfo.php', 'ReportUserRight.php', 'ReportUserDept.php', 'ReportUserDiv.php', 'ReportShiftRotation.php', 'ReportProcessLog.php', 'ReportAbsence.php', 'YearlyAttendanceSummary.php', 'ReportLateArrival.php'],
    'Settings' => ['UserManagement.php', 'AssignUserDept.php', 'AssignUserDiv.php', 'UserAssignEmployee.php', 'OTDayDate.php', 'ProxyEmployeeExempt.php', 'OTEmployeeExempt.php', 'OTEmployeeExemptOTDay.php', 'ShiftMaster.php', 'ShiftSummaryMaster.php', 'ShiftRotation.php', 'AssignShift.php', 'EmployeeMaster.php', 'EmployeeWiseOffDay.php', 'EmployeeFlagLimit.php', 'GroupMaster.php', 'ContractAccess.php', 'AssignTerminal.php', 'Archive.php', 'MigrateMaster.php', 'OtherSetting.php'],
    'Wages' => ['WagesCalculation.php', 'WagesCalculationMaster.php']
];
$activeSection = '';
foreach ($menuMap as $section => $pages) {
    if (in_array($currentPage, $pages)) {
        $activeSection = $section;
        break;
    }
}
global $session_variable;
$username = $_SESSION[$session_variable . "username"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$virdiLevel = $_SESSION[$session_variable . "VirdiLevel"];
$txtMACAddress = $_SESSION[$session_variable . "MACAddress"];
$link = "";
$url = $_SERVER["REQUEST_URI"];
?>
<link rel="stylesheet" href="styles.css" type="text/css" />
<script type="text/javascript" language="javascript" src='default.js'></script>
<style>
    #menuSearch {
        width: 100%;
        padding: 10px;
        /*margin-bottom: 15px;*/
        border: 1px solid #ddd;
        border-radius: 4px;
        margin-top: 20px;
    }

    #sidebarnav li {
        list-style-type: none;
        padding: 8px;
        cursor: pointer;
    }

    #sidebarnav li a {
        color: #333;
        text-decoration: none;
        display: block;
        padding: 8px 16px;
    }

    #sidebarnav li a:hover,
    /*#sidebarnav li.active > a,*/
    #sidebarnav li.actives > a{
        background-color: #007bff !important;
        color: #fff !important;
    }
    .left-sidebar {  
        width: 250px; /* Set your desired width */  
        height: 100%; /* Full height of the viewport */  
        position: fixed; /* Fixed position */  
        top: 0;  
        left: 0;  
        background: #f8f9fa; /* Background color, adjust as needed */  
    }  

    .scroll-sidebar {  
        height: calc(100vh - 20px); /* Adjust if you have any padding/margin */  
        overflow-y: auto; /* Enable vertical scrolling */  
        /*padding: 10px;  Optional padding */  
        width:auto;
    }
</style>
<aside class="left-sidebar" data-sidebarbg="skin5">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <!--<input type="text" id="menuSearch" placeholder="Search Menu..." onkeyup="filterMenu()">-->
            <ul id="sidebarnav" class="pt-4">
                <li class="sidebar-item <?php echo ($currentPage == 'Dashboard.php') ? 'actives' : ''; ?>"><a href="Dashboard.php" class="sidebar-link"><i class="mdi mdi-view-dashboard"></i><span class="hide-menu"> Dashboard </span></a></li></li>
                <li class="sidebar-item has-sub <?php echo ($activeSection == 'Attendance') ? 'actives' : ''; ?>">
                    <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-clock"></i><span class="hide-menu">Attendance </span></a>
                    <?php if ($_SESSION[$session_variable . "ExitTerminal"] != "Canteen") { ?>
                        <ul aria-expanded="false" class="collapse first-level">
                            <li class="sidebar-item has-sub"><a class="sidebar-link has-arrow waves-effect waves-dark active" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> Logs Detail </span></a>
                                <ul aria-expanded="true" class="collapse third-level">
                                    <?php if (strpos($userlevel, "17V") !== false) { ?>
                                        <li class="sidebar-item <?php echo ($currentPage == 'AlterTime.php') ? 'actives' : ''; ?>"><a href="AlterTime.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> Alter Logs </span></a></li>
                                    <?php } ?>
                                    <?php if (strpos($userlevel, "24V") !== false && $virdiLevel == "Classic") { ?>
                                        <li class="sidebar-item <?php echo ($currentPage == 'Proxy.php') ? 'actives' : ''; ?>"><a href="Proxy.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> Mark Proxy </span></a></li>
                                    <?php } ?>
                                </ul>
                                <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> Overtime </span></a>
                                <ul aria-expanded="true" class="collapse third-level">
                                    <?php if (strpos($userlevel, "26V") !== false && $virdiLevel == "Classic") { ?>
                                        <li class="sidebar-item <?php echo ($currentPage == 'PreApproveOvertime.php') ? 'actives' : ''; ?>"><a href="PreApproveOvertime.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> Pre Approve </span></a></li>
                                    <?php } ?>
                                    <?php if (strpos($userlevel, "15V") !== false) { ?>
                                        <li class="sidebar-item <?php echo ($currentPage == 'ApproveOvertime.php') ? 'actives' : ''; ?>"><a href="ApproveOvertime.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> Post Approve </span></a></li>
                                    <?php } ?>    
                                    <?php
                                    //if(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) != 3){
                                    if (strpos($userlevel, "15V") !== false) {
                                        ?>
                                        <li class="sidebar-item <?php echo ($currentPage == 'ApproveOvertimeSummary.php') ? 'actives' : ''; ?>"><a href="ApproveOvertimeSummary.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> Bulk Approve </span></a></li>
                                    <?php } //}  ?>
                                </ul>
                                <?php //if(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) != 3){  ?>
                                <a href="javascript:void(0)" class="sidebar-link has-arrow waves-effect waves-dark"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> Flags </span></a>
                                <ul aria-expanded="true" class="collapse third-level">
                                    <?php if (strpos($userlevel, "34V") !== false && $virdiLevel == "Classic") { ?>
                                        <li class="sidebar-item <?php echo ($currentPage == 'FlagApplication.php') ? 'actives' : ''; ?>"><a href="FlagApplication.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> Flag Application </span></a></li>
                                    <?php } ?>
                                    <?php if (strpos($userlevel, "25V") !== false && $virdiLevel == "Classic" || getRegister(encryptDecrypt($txtMACAddress), 7) == "173") { ?>
                                        <li class="sidebar-item <?php echo ($currentPage == 'OffDay.php') ? 'actives' : ''; ?>"><a href="OffDay.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> Pre Flag </span></a></li>
                                    <?php } ?>
                                    <?php if (strpos($userlevel, "23V") !== false && $virdiLevel == "Classic" || getRegister(encryptDecrypt($txtMACAddress), 7) == "173") { ?>
                                        <li class="sidebar-item <?php echo ($currentPage == 'FlagDay.php') ? 'actives' : ''; ?>"><a href="FlagDay.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> Post Flag </span></a></li>
                                    <?php } ?>
                                </ul>
                                <?php //} ?>
                                <?php //if(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) != 3){  ?>
                                <a href="javascript:void(0)" class="sidebar-link has-arrow waves-effect waves-dark"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> All Rosters </span></a>
                                <ul aria-expanded="true" class="collapse third-level">
                                    <?php if (strpos($userlevel, "32V") !== false && $virdiLevel == "Classic") { ?>
                                        <li class="sidebar-item <?php echo ($currentPage == 'FlagRoster.php') ? 'actives' : ''; ?>"><a href="FlagRoster.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> Flag Roaster </span></a></li>
                                    <?php } ?>
                                    <?php if (strpos($userlevel, "35V") !== false && $virdiLevel == "Classic") { ?>
                                        <li class="sidebar-item <?php echo ($currentPage == 'CAGRoster.php') ? 'actives' : ''; ?>"><a href="CAGRoster.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> Access Group Roaster </span></a></li>
                                    <?php } ?>
                                    <?php if (strpos($userlevel, "31V") !== false && $_SESSION[$session_variable . "UseShiftRoster"] == "Yes" && $virdiLevel == "Classic" || getRegister(encryptDecrypt($txtMACAddress), 7) == "173") { ?>
                                        <li class="sidebar-item <?php echo ($currentPage == 'ShiftRoster.php') ? 'actives' : ''; ?>"><a href="ShiftRoster.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> Shift Roaster </span></a></li>
                                    <?php } ?>
                                </ul>
                                <?php //} ?>
                                <a href="javascript:void(0)" class="sidebar-link has-arrow waves-effect waves-dark"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> Delete Logs </span></a>
                                <ul aria-expanded="true" class="collapse third-level">
                                    <?php if (strpos($userlevel, "22V") !== false) { ?>
                                        <li class="sidebar-item <?php echo ($currentPage == 'DeleteProcessedRecord.php') ? 'actives' : ''; ?>"><a href="DeleteProcessedRecord.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> Processed Logs </span></a></li>
                                        <li class="sidebar-item <?php echo ($currentPage == 'DeleteProcessedRawRecord.php') ? 'actives' : ''; ?>"><a href="DeleteProcessedRawRecord.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> Raw Logs </span></a></li>
                                    <?php } ?>
                                    <?php
                                    //if(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) != 3){
                                    if (strpos($userlevel, "25D") !== false && $virdiLevel == "Classic" || getRegister(encryptDecrypt($txtMACAddress), 7) == "173") {
                                        ?>
                                        <li class="sidebar-item <?php echo ($currentPage == 'DeletePreFlaggedRecord.php') ? 'actives' : ''; ?>"><a href="DeletePreFlaggedRecord.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> PreFlag Logs </span></a></li>
                                    <?php } //}  ?>
                                </ul>
                                <?php //if(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) != 3){  ?>
                                <a href="javascript:void(0)" class="sidebar-link has-arrow waves-effect waves-dark"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> Exemption Info </span></a>
                                <ul aria-expanded="true" class="collapse third-level">
                                    <?php if (strpos($userlevel, "29V") !== false && $virdiLevel == "Classic") { ?>
                                        <li class="sidebar-item <?php echo ($currentPage == 'ExemptLateInEarlyOutMoreBreak.php') ? 'actives' : ''; ?>"><a href="ExemptLateInEarlyOutMoreBreak.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> Exemptions </span></a></li>
                                    <?php } ?>
                                    <?php if (strpos($userlevel, "16V") !== false && $_SESSION[$session_variable . "Project"] == "Yes" && $virdiLevel == "Classic") { ?>
                                        <li class="sidebar-item <?php echo ($currentPage == 'AssignProject.php') ? 'actives' : ''; ?>"><a href="AssignProject.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> Assign Project </span></a></li>
                                    <?php } ?>
                                    <?php /* if (strpos($userlevel, "33V") !== false && $virdiLevel == "Classic") { ?>
                                      <li class="sidebar-item"><a href="DrillMaster.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> Drill </span></a></li>
                                      <?php } */ ?>
                                </ul>
                                <?php //}  ?>
                            </li>
                        </ul>
                    <?php } ?>
                </li>
                <li class="sidebar-item <?php echo (in_array($currentPage, $menuMap['Reports'])) ? 'actives' : ''; ?>">
                    <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-receipt"></i><span class="hide-menu">Reports </span></a>
                    <ul aria-expanded="false" class="collapse first-level">
                        <li class="sidebar-item"><a href="javascript:void(0)" class="sidebar-link has-arrow waves-effect waves-dark"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> General </span></a>
                            <ul aria-expanded="true" class="collapse third-level">
                                <?php if (strpos($userlevel, "18R") !== false) { ?>
                                    <li class="sidebar-item <?php echo ($currentPage == 'Empreports.php') ? 'actives' : ''; ?>"><a href="Empreports.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> Employee Report </span></a></li>
                                    <li class="sidebar-item <?php echo ($currentPage == 'ReportClockingLog.php') ? 'actives' : ''; ?>"><a href="ReportClockingLog.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> Raw Log </span></a></li>
                                    <?php if ($virdiLevel != "Meal") { ?>
                                        <li class="sidebar-item <?php echo ($currentPage == 'ReportAttendance.php') ? 'actives' : ''; ?>"><a href="ReportAttendance.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> Attendance </span></a></li>
                                        <?php //if(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) != 3){  ?>
                                        <li class="sidebar-item <?php echo ($currentPage == 'ReportOddLog.php') ? 'actives' : ''; ?>"><a href="ReportOddLog.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> Odd Log </span></a></li>
                                        <?php //}  ?>
                                        <?php //if(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) != 3 && getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) != 2){ ?>
                                        <li class="sidebar-item <?php echo ($currentPage == 'ReportExitLog.php') ? 'actives' : ''; ?>"><a href="ReportExitLog.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> Exit Log </span></a></li>
                                        <?php //}  ?>
                                        <?php //if(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) != 3){ ?>
                                        <li class="sidebar-item <?php echo ($currentPage == 'ReportLateArrival.php') ? 'actives' : ''; ?>"><a href="ReportLateArrival.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> Late Arrival </span></a></li>
                                        <?php //}  ?>
                                        <?php // if(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) != 3 && getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) != 2){ ?>
                                        <li class="sidebar-item <?php echo ($currentPage == 'ReportMoreBreak.php') ? 'actives' : ''; ?>"><a href="ReportMoreBreak.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> More Break </span></a></li>
                                        <li class="sidebar-item <?php echo ($currentPage == 'ReportEarlyExit.php') ? 'actives' : ''; ?>"><a href="ReportEarlyExit.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> Early Exit </span></a></li>
                                        <?php // }  ?>
                                        <li class="sidebar-item <?php echo ($currentPage == 'ReportLIEO.php') ? 'actives' : ''; ?>"><a href="ReportLIEO.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> Late In/ Early Exit </span></a></li>
                                        <?php // if(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) != 3 && getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) != 2){  ?>
                                        <li class="sidebar-item <?php echo ($currentPage == 'ReportAbsence.php') ? 'actives' : ''; ?>"><a href="ReportAbsence.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> Absent </span></a></li>
                                        <?php // }  ?>
                                        <li class="sidebar-item <?php echo ($currentPage == 'ReportAbsenceCount.php') ? 'actives' : ''; ?>"><a href="ReportAbsenceCount.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> Absent Count </span></a></li>
                                        <li class="sidebar-item <?php echo ($currentPage == 'MonthlyAttendanceSummary.php') ? 'actives' : ''; ?>"><a href="MonthlyAttendanceSummary.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> Monthly Summary Report </span></a></li>
                                        <li class="sidebar-item <?php echo ($currentPage == 'EmployeeDailyAttendanceReport.php') ? 'actives' : ''; ?>"><a href="EmployeeDailyAttendanceReport.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> Employee Daily <br> Attendance Report </span></a></li>
                                    <?php } ?>
                        <!--<li class="sidebar-item"><a href="ReportMealCount.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> Meal Count </span></a></li>-->
                                    <?php
                                    //if(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) != 3 && getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) != 2){ 
                                    if ($_SESSION[$session_variable . "ExitTerminal"] == "Yes" && $virdiLevel == "Classic") {
                                        ?>
                                        <li class="sidebar-item <?php echo ($currentPage == 'ReportExitTerminalError.php') ? 'actives' : ''; ?>"><a href="ReportExitTerminalError.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> Exit Terminal Error </span></a></li>
                                        <?php
                                    } //}
                                } if (strpos($userlevel, "27R") !== false) {
                                    ?>
                                    <li class="sidebar-item <?php echo ($currentPage == 'ReportEmployee.php') ? 'actives' : ''; ?>"><a href="ReportEmployee.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> Employees </span></a></li>
                                    <!--<li class="sidebar-item"><a href="ReportEmployeeBarCode.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> Employees Bar Code </span></a></li>-->
                                <?php } ?>
                            </ul>
                            <a href="javascript:void(0)" class="sidebar-link has-arrow waves-effect waves-dark"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> Processed Logs </span></a>
                            <ul aria-expanded="true" class="collapse third-level">
                                <?php if (strpos($userlevel, "20R") !== false && $virdiLevel != "Meal") { ?>
                                    <li class="sidebar-item <?php echo ($currentPage == 'ReportDailyClocking.php') ? 'actives' : ''; ?>"><a href="ReportDailyClocking.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">Processed Logs [Daily Routine]</span></a></li>
                                    <?php //if(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) != 3 && getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) != 2){  ?>
                                    <li class="sidebar-item <?php echo ($currentPage == 'ReportWeeklyClocking.php') ? 'actives' : ''; ?>"><a href="ReportWeeklyClocking.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">Processed Logs [Weekly Routine]</span></a></li>
                                <?php } //}  ?>
                            </ul>
                            <a href="javascript:void(0)" class="sidebar-link has-arrow waves-effect waves-dark"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> HR </span></a>
                            <ul aria-expanded="true" class="collapse third-level">
                                <?php
                                if (strpos($userlevel, "21R") !== false && $virdiLevel != "Meal") {
                                    //if(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) != 3 && getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) != 2){ 
                                    ?>
                                    <li class="sidebar-item <?php echo ($currentPage == 'ReportWork.php') ? 'actives' : ''; ?>"><a href="ReportWork.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">Work</span></a></li>
                                    <?php //} ?>
                                    <?php //if(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) != 3){   ?>
                                    <li class="sidebar-item <?php echo ($currentPage == 'ReportDailyRoster.php') ? 'actives' : ''; ?>"><a href="ReportDailyRoster.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">Roaster</span></a></li>
                                    <?php //} ?>
                                    <?php if ($virdiLevel == "Classic") { ?>
                                        <li class="sidebar-item <?php echo ($currentPage == 'ReportPeriodicSummary.php') ? 'actives' : ''; ?>"><a href="ReportPeriodicSummary.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">Day Summary</span></a></li>
                                        <li class="sidebar-item <?php echo ($currentPage == 'ReportMonthlyHours.php') ? 'actives' : ''; ?>"><a href="ReportMonthlyHours.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">Hour Summary</span></a></li>
                                        <?php // if(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) != 3){   ?>
                                        <li class="sidebar-item <?php echo ($currentPage == 'ReportMonthSummary.php') ? 'actives' : ''; ?>"><a href="ReportMonthSummary.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">Monthly Comparison</span></a></li>
                                        <?php // }  ?>
                                        <li class="sidebar-item <?php echo ($currentPage == 'ReportShiftSnapShot.php') ? 'actives' : ''; ?>"><a href="ReportShiftSnapShot.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">Shift Summary</span></a></li>
                                        <?php // if(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) != 3){   ?>
                                        <!--<li class="sidebar-item <?php //echo ($currentPage == 'ReportGroupSummary.php') ? 'actives' : '';   ?>"><a href="ReportGroupSummary.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">Group Summary</span></a></li>-->
                                        <?php // } ?>
                                    <?php } ?>
                                    <li class="sidebar-item <?php echo ($currentPage == 'ReportAttendanceSnapShot.php') ? 'actives' : ''; ?>"><a href="ReportAttendanceSnapShot.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">Snapshot</span></a></li>
                                    <!--<li class="sidebar-item"><a href="ReportIOULog.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">PayMaster IOU</span></a></li>-->
                                    <li class="sidebar-item <?php echo ($currentPage == 'AttendanceSummary.php') ? 'actives' : ''; ?>"><a href="AttendanceSummary.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">Attendance Summary</span></a></li>
                                    <li class="sidebar-item <?php echo ($currentPage == 'YearlyAttendanceSummary.php') ? 'actives' : ''; ?>"><a href="YearlyAttendanceSummary.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">Employee Absence <br> Summary</span></a></li>                                    
                                <?php } ?>
                            </ul>
                            <a href="javascript:void(0)" class="sidebar-link has-arrow waves-effect waves-dark"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> Attendance </span></a>
                            <ul aria-expanded="true" class="collapse third-level">
                                <?php if (strpos($userlevel, "20R") !== false && $virdiLevel != "Meal") { ?>
                                    <li class="sidebar-item <?php echo ($currentPage == 'ReportPreFlag.php') ? 'actives' : ''; ?>"><a href="ReportPreFlag.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">Pre Flag</span></a></li>
                                    <li class="sidebar-item <?php echo ($currentPage == 'ReportProject.php') ? 'actives' : ''; ?>"><a href="ReportProject.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">Projects</span></a></li>
                                    <li class="sidebar-item <?php echo ($currentPage == 'ReportPreApproval.php') ? 'actives' : ''; ?>"><a href="ReportPreApproval.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">Pre Approval</span></a></li>
                                    <li class="sidebar-item <?php echo ($currentPage == 'ReportAlterTime.php') ? 'actives' : ''; ?>"><a href="ReportAlterTime.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">Alter Logs</span></a></li>
                                <?php } ?>
                            </ul>
                            <a href="javascript:void(0)" class="sidebar-link has-arrow waves-effect waves-dark"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> Settings </span></a>
                            <ul aria-expanded="true" class="collapse third-level">
                                <?php
//                            if(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) != 3){ 
                                if (strpos($userlevel, "30R") !== false && $virdiLevel == "Classic") {
                                    ?>
                                    <li class="sidebar-item <?php echo ($currentPage == 'ReportFlagLimit.php') ? 'actives' : ''; ?>"><a href="ReportFlagLimit.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">Flag Limit</span></a></li>
                                    <?php
                                } //}
                                //if(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) != 3 && getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) != 2){ 
                                if (strpos($userlevel, "27R") !== false && $virdiLevel == "Classic") {
                                    ?>
                                    <li class="sidebar-item <?php echo ($currentPage == 'ReportADA.php') ? 'actives' : ''; ?>"><a href="ReportADA.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">ADA</span></a></li>
                                    <?php
                                } //} 
                                if (strpos($userlevel, "11R") !== false) {
                                    ?>
                                    <li class="sidebar-item <?php echo ($currentPage == 'ReportUserTransact.php') ? 'actives' : ''; ?>"><a href="ReportUserTransact.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">User Transactions</span></a></li>
                                    <li class="sidebar-item <?php echo ($currentPage == 'ReportUserInfo.php') ? 'actives' : ''; ?>"><a href="ReportUserInfo.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">User Info</span></a></li>
                                    <li class="sidebar-item <?php echo ($currentPage == 'ReportUserRight.php') ? 'actives' : ''; ?>"><a href="ReportUserRight.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">User Rights</span></a></li>
                                    <li class="sidebar-item <?php echo ($currentPage == 'ReportUserDept.php') ? 'actives' : ''; ?>"><a href="ReportUserDept.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">User-Dept Rights</span></a></li>
                                    <li class="sidebar-item <?php echo ($currentPage == 'ReportUserDiv.php') ? 'actives' : ''; ?>"><a href="ReportUserDiv.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">User-Division Rights</span></a></li>
                                    <?php
                                }
//                                    if(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) != 3){
                                if (strpos($userlevel, "12R") !== false && $_SESSION[$session_variable . "RotateShift"] == "Yes" && $virdiLevel == "Classic") {
                                    ?>
                                    <li class="sidebar-item <?php echo ($currentPage == 'ReportShiftRotation.php') ? 'actives' : ''; ?>"><a href="ReportShiftRotation.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">Shift Rotation</span></a></li>
                                    <?php
                                } //} 
                                if (strpos($userlevel, "19R") !== false && $virdiLevel != "Meal") {
                                    ?>
                                    <li class="sidebar-item <?php echo ($currentPage == 'ReportProcessLog.php') ? 'actives' : ''; ?>"><a href="ReportProcessLog.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">System Logs</span></a></li>
                                <?php } ?>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li class="sidebar-item <?php echo (in_array($currentPage, $menuMap['Settings'])) ? 'actives' : ''; ?>">
                    <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-settings"></i><span class="hide-menu">Settings </span></a>
                    <ul aria-expanded="false" class="collapse first-level">
                        <li class="sidebar-item">
                            <a href="javascript:void(0)" class="sidebar-link has-arrow waves-effect waves-dark"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> Users </span></a>
                            <ul aria-expanded="true" class="collapse third-level">
                                <?php if (strpos($userlevel, "11V") !== false) { ?>
                                    <li class="sidebar-item <?php echo ($currentPage == 'UserManagement.php') ? 'actives' : ''; ?>"><a href="UserManagement.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">Users</span></a></li>
                                    <li class="sidebar-item <?php echo ($currentPage == 'AssignUserDept.php') ? 'actives' : ''; ?>"><a href="AssignUserDept.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">Assign User Dept Access</span></a></li>
                                    <li class="sidebar-item <?php echo ($currentPage == 'AssignUserDiv.php') ? 'actives' : ''; ?>"><a href="AssignUserDiv.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">Assign User Div Access</span></a></li>
                                    <li class="sidebar-item <?php echo ($currentPage == 'UserAssignEmployee.php') ? 'actives' : ''; ?>"><a href="UserAssignEmployee.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">Assign User Employee Access</span></a></li>
                                <?php } ?>
                            </ul>
                            <a href="javascript:void(0)" class="sidebar-link has-arrow waves-effect waves-dark"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> Overtime/Proxy </span></a>
                            <ul aria-expanded="true" class="collapse third-level">
                                <?php
                                if (strpos($userlevel, "28V") !== false) {
                                    if ($virdiLevel != "Meal") {
                                        ?>
                                        <li class="sidebar-item <?php echo ($currentPage == 'OTDayDate.php') ? 'actives' : ''; ?>"><a href="OTDayDate.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">OT Days/ Dates</span></a></li>
                                    <?php } if ($virdiLevel == "Classic") { ?>
                                        <li class="sidebar-item <?php echo ($currentPage == 'ProxyEmployeeExempt.php') ? 'actives' : ''; ?>"><a href="ProxyEmployeeExempt.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">Proxy Exemption</span></a></li>
                                        <li class="sidebar-item <?php echo ($currentPage == 'OTEmployeeExempt.php') ? 'actives' : ''; ?>"><a href="OTEmployeeExempt.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">OT Exemption</span></a></li>
                                        <?php //if(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) != 3 && getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) != 2){  ?>
                                        <li class="sidebar-item <?php echo ($currentPage == 'OTEmployeeExemptOTDay.php') ? 'actives' : ''; ?>"><a href="OTEmployeeExemptOTDay.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">Special OT Days <br>for Exempted Employees</span></a></li>
                                    <?php
                                    }
                                } //} 
                                ?>
                            </ul>
                            <a href="javascript:void(0)" class="sidebar-link has-arrow waves-effect waves-dark"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> Shifts </span></a>
                            <ul aria-expanded="true" class="collapse third-level">
                                <?php if (strpos($userlevel, "12V") !== false && $virdiLevel != "Meal") { ?>
                                    <li class="sidebar-item <?php echo ($currentPage == 'ShiftMaster.php') ? 'actives' : ''; ?>"><a href="ShiftMaster.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">Shifts</span></a></li>
                                    <?php if ($virdiLevel == "Classic") { ?>
                                        <li class="sidebar-item <?php echo ($currentPage == 'ShiftSummaryMaster.php') ? 'actives' : ''; ?>"><a href="ShiftSummaryMaster.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">Shift Summary</span></a></li>
                                        <?php
                                    }
                                    //if(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) != 3){ 
                                    if ($_SESSION[$session_variable . "RotateShift"] == "Yes" && $virdiLevel == "Classic") {
                                        ?>
                                        <li class="sidebar-item <?php echo ($currentPage == 'ShiftRotation.php') ? 'actives' : ''; ?>"><a href="ShiftRotation.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">Shift Rotation</span></a></li>
                                        <?php
                                    } //} 
                                    if (strpos($userlevel, "14V") !== false && $virdiLevel != "Meal") {
                                        ?>
                                        <li class="sidebar-item <?php echo ($currentPage == 'AssignShift.php') ? 'actives' : ''; ?>"><a href="AssignShift.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">Assign Shift to Employees</span></a></li>
                                    <?php } if (strpos($userlevel, "35V") !== false && $virdiLevel == "Classic") { ?>
                                        <!--<li class="sidebar-item"><a href="AssignShiftCAG.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">Assign Shift to Access<br> Group</span></a></li>-->
                                    <?php
                                    }
                                }
                                ?>
                            </ul>
                            <a href="javascript:void(0)" class="sidebar-link has-arrow waves-effect waves-dark"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> Employees </span></a>
                            <ul aria-expanded="true" class="collapse third-level">
                                <?php if (strpos($userlevel, "11V") !== false) { ?>
                                    <li class="sidebar-item <?php echo ($currentPage == 'EmployeeMaster.php') ? 'actives' : ''; ?>"><a href="EmployeeMaster.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">Employee Info</span></a></li>
                                    <li class="sidebar-item <?php echo ($currentPage == 'EmployeeWiseOffDay.php') ? 'actives' : ''; ?>"><a href="EmployeeWiseOffDay.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">Employee Wise <br> Off Day</span></a></li>
                                    <?php // if(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) != 3){ ?>
                                    <li class="sidebar-item <?php echo ($currentPage == 'EmployeeFlagLimit.php') ? 'actives' : ''; ?>"><a href="EmployeeFlagLimit.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">Flag Limits</span></a></li>
                                <?php } //}  ?>
                            </ul>
                            <a href="javascript:void(0)" class="sidebar-link has-arrow waves-effect waves-dark"><i class="mdi mdi-note-outline"></i><span class="hide-menu"> General </span></a>
                            <ul aria-expanded="true" class="collapse third-level">
                                <?php
                                //if(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) != 3 && getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) != 2){ 
                                if (strpos($userlevel, "16A") !== false && $_SESSION[$session_variable . "Project"] == "Yes" && $virdiLevel == "Classic") {
                                    ?>
                                    <li class="sidebar-item <?php echo ($currentPage == 'ProjectMaster.php') ? 'actives' : ''; ?>"><a href="ProjectMaster.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">Projects</span></a></li>
                                    <?php
                                } //} 
                                //if(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) != 3){
                                if (strpos($userlevel, "13V") !== false && $virdiLevel == "Classic") {
                                    ?>
                                    <li class="sidebar-item <?php echo ($currentPage == 'GroupMaster.php') ? 'actives' : ''; ?>"><a href="GroupMaster.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">Report Groups Settings</span></a></li>
                                <?php } if (strpos($userlevel, "35V") !== false && $virdiLevel == "Classic") { ?>
                                    <li class="sidebar-item <?php echo ($currentPage == 'ContractAccess.php') ? 'actives' : ''; ?>"><a href="ContractAccess.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">Contract Access Groups</span></a></li>
                                    <?php
                                } //} 
                                if (strpos($userlevel, "13V") !== false && $virdiLevel == "Classic") {
                                    ?>
                                    <li class="sidebar-item <?php echo ($currentPage == 'AssignTerminal.php') ? 'actives' : ''; ?>"><a href="AssignTerminal.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">Assign Terminal</span></a></li>
                                    <?php if ($_SESSION[$session_variable . "ExitTerminal"] == "Yes") { ?>
                                        <li class="sidebar-item <?php echo ($currentPage == 'ExitTerminal.php') ? 'actives' : ''; ?>"><a href="ExitTerminal.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">Exit Terminals</span></a></li>
                                    <?php } ?>
                            <!--<li class="sidebar-item"><a href="MealTerminal.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">Meal Terminals</span></a></li>-->
                                    <?php
                                }
                                if (strpos($userlevel, "19V") !== false) {
                                    //if(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) != 3 && getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) != 2){     
                                    ?>
                                    <li class="sidebar-item <?php echo ($currentPage == 'Archive.php') ? 'actives' : ''; ?>"><a href="Archive.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">DB Archive</span></a></li>
                                    <?php if ($virdiLevel == "Classic") { ?>
                                                <!--<li class="sidebar-item"><a href="UNISMap.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">UNIS Map</span></a></li>-->
                                        <li class="sidebar-item <?php echo ($currentPage == 'MigrateMaster.php') ? 'actives' : ''; ?>"><a href="MigrateMaster.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">Payroll Migration</span></a></li>
                                    <?php } //}  ?>
                                    <li class="sidebar-item <?php echo ($currentPage == 'OtherSetting.php') ? 'actives' : ''; ?>"><a href="OtherSetting.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">Global</span></a></li>
<?php } ?>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li class="sidebar-item <?php echo (in_array($currentPage, $menuMap['Wages'])) ? 'actives' : ''; ?>">
                    <a href="javascript:void(0)" class="sidebar-link has-arrow waves-effect waves-dark"><i class="mdi mdi-cash"></i><span class="hide-menu"> Wages </span></a>
                    <ul aria-expanded="true" class="collapse third-level">
                        <?php if (strpos($userlevel, "37V") !== false) { ?>
                            <li class="sidebar-item <?php echo ($currentPage == 'WagesCalculationMaster.php') ? 'actives' : ''; ?>"><a href="WagesCalculationMaster.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">Wages Master</span></a></li>
                            <li class="sidebar-item <?php echo ($currentPage == 'AssignEmployeeGroup.php') ? 'actives' : ''; ?>"><a href="AssignEmployeeGroup.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">Wages Assign Group</span></a></li>
                        <?php } if (strpos($userlevel, "38V") !== false) { ?>    
                            <li class="sidebar-item <?php echo ($currentPage == 'WagesCalculation.php') ? 'actives' : ''; ?>"><a href="WagesCalculation.php" class="sidebar-link"><i class="mdi mdi-note-outline"></i><span class="hide-menu">Wages Calculation</span></a></li>
                        <?php } ?>
                    </ul>
                </li>
                <li class="sidebar-item">
                    <!--<img src="img/end-logo.png" style="width: 75%;margin-left: 20px;"/>-->
                </li>

<?php echo aboutInfo($prints, $false, $false); ?>

            </ul>
        </nav>

        <!-- End Sidebar navigation -->
    </div>

    <!-- End Sidebar scroll-->
</aside>
<?php
$_SESSION['cached_sidebar'][$pageKey] = ob_get_clean();
echo $_SESSION['cached_sidebar'][$pageKey];
?>
<script>
    function filterMenu() {
        var input, filter, ul, li, a, i, j, k, txtValue, subItems, subLi, subA, subTxtValue, subSubItems, subSubLi, subSubA, subSubTxtValue;
        input = document.getElementById('menuSearch');
        filter = input.value.toLowerCase();
        ul = document.getElementById("sidebarnav");
        li = ul.getElementsByTagName('li');

// If search input is empty, collapse all menus and return
        if (filter === "") {
            collapseAllMenus(ul);
            for (i = 0; i < li.length; i++) {
                li[i].style.display = ""; // Show all main menu items
            }
            return;
        }
        // Collapse all menus by default
        collapseAllMenus(ul);

        for (i = 0; i < li.length; i++) {
            a = li[i].getElementsByTagName("a")[0];
            txtValue = a.textContent || a.innerText;

            if (txtValue.toLowerCase().indexOf(filter) > -1) {
                li[i].style.display = ""; // Show the main menu item
                li[i].classList.add("show"); // Ensure the parent is expanded
                expandParents(li[i]); // Expand all parent menus
                showAllChildren(li[i]); // Show all child elements
            } else {
                subItems = li[i].getElementsByTagName("ul")[0];
                if (subItems) {
                    let foundInSubMenu = false;
                    subLi = subItems.getElementsByTagName("li");
                    for (j = 0; j < subLi.length; j++) {
                        subA = subLi[j].getElementsByTagName("a")[0];
                        subTxtValue = subA.textContent || subA.innerText;
                        if (subTxtValue.toLowerCase().indexOf(filter) > -1) {
                            subLi[j].style.display = ""; // Show the sub-menu item
                            li[i].style.display = ""; // Show the main menu item
                            li[i].classList.add("show"); // Ensure the parent is expanded
                            subItems.classList.add("show"); // Expand the submenu
                            expandParents(subLi[j]); // Expand all parent menus
                            showAllChildren(subLi[j]); // Show all child elements
                            foundInSubMenu = true;
                        } else {
                            subSubItems = subLi[j].getElementsByTagName("ul")[0];
                            if (subSubItems) {
                                let foundInSubSubMenu = false;
                                subSubLi = subSubItems.getElementsByTagName("li");
                                for (k = 0; k < subSubLi.length; k++) {
                                    subSubA = subSubLi[k].getElementsByTagName("a")[0];
                                    subSubTxtValue = subSubA.textContent || subSubA.innerText;
                                    if (subSubTxtValue.toLowerCase().indexOf(filter) > -1) {
                                        subSubLi[k].style.display = ""; // Show the sub-sub-menu item
                                        subLi[j].style.display = ""; // Show the sub-menu item
                                        li[i].style.display = ""; // Show the main menu item
                                        li[i].classList.add("show"); // Ensure the parent is expanded
                                        subItems.classList.add("show"); // Expand the submenu
                                        subSubItems.classList.add("show"); // Expand the sub-submenu
                                        expandParents(subSubLi[k]); // Expand all parent menus
                                        showAllChildren(subSubLi[k]); // Show all child elements
                                        foundInSubSubMenu = true;
                                    } else {
                                        subSubLi[k].style.display = "none"; // Hide non-matching sub-sub-menu item
                                    }
                                }
                                if (!foundInSubSubMenu) {
                                    subLi[j].style.display = "none"; // Hide non-matching sub-menu item
                                }
                            } else {
                                subLi[j].style.display = "none"; // Hide non-matching sub-menu item
                            }
                        }
                    }
                    if (!foundInSubMenu) {
                        li[i].style.display = "none"; // Hide the main menu item if no sub-menus match
                        li[i].classList.remove("show");
                    }
                } else {
                    li[i].style.display = "none"; // Hide the main menu item if no sub-menus exist and it doesn't match
                    li[i].classList.remove("show");
                }
            }
        }
    }

    function collapseAllMenus(ul) {
        var allMenus = ul.getElementsByTagName('ul');
        for (var i = 0; i < allMenus.length; i++) {
            allMenus[i].classList.remove('show'); // Collapse all submenus
        }
    }

    function expandParents(element) {
        var parent = element.parentElement;
        while (parent && parent.tagName !== 'UL' && parent.tagName !== 'BODY') {
            if (parent.style) {
                parent.style.display = "";
                if (parent.classList.contains('collapse')) {
                    parent.classList.add("show"); // Ensure it's expanded
                }
            }
            parent = parent.parentElement;
        }
    }

    function showAllChildren(element) {
        var subMenus = element.getElementsByTagName('ul');
        for (var i = 0; i < subMenus.length; i++) {
            subMenus[i].classList.add('show'); // Expand all child menus
            var subItems = subMenus[i].getElementsByTagName('li');
            for (var j = 0; j < subItems.length; j++) {
                subItems[j].style.display = ""; // Show all child items
            }
        }
    }
</script>