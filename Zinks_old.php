<?php

error_reporting(E_ERROR);
global $session_variable;
$username = $_SESSION[$session_variable . "username"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$virdiLevel = $_SESSION[$session_variable . "VirdiLevel"];
$txtMACAddress = $_SESSION[$session_variable . "MACAddress"];
$link = "";
$url = $_SERVER["REQUEST_URI"];
echo "\r\n<link rel=\"stylesheet\" href=\"styles.css\" type=\"text/css\" />\r\n<!-- dd menu -->\r\n<script type=\"text/javascript\" language=\"javascript\" src='default.js'></script>\r\n\t<table width=\"100%\" border=\"0\" align=\"center\">\t\t\t\r\n\t\t\t<tr>\r\n\t\t\t\t<td>\r\n\t\t\t\t\t<div id='cssmenu'>\r\n\t\t\t\t\t\t<ul>\r\n\t\t\t\t\t\t\t<li class='active has-sub'><a href='#'><span>Attendance</span></a>\r\n\t\t\t\t\t\t\t\t";
if ($_SESSION[$session_variable . "ExitTerminal"] != "Canteen") {
    echo "\t\t\t\t\t\t\t\t<ul>\t\t\t\t\t\t\t\t\t\r\n\t\t\t\t\t\t\t\t\t<li class='has-sub'><a href='#'><span>Alter Logs</span></a>\r\n\t\t\t\t\t\t\t\t\t\t<ul>\r\n\t\t\t\t\t\t\t\t\t\t";
    if (strpos($userlevel, "17V") !== false) {
        print "<li class='last'><a href='AlterTime.php'><span>Alter Logs</span></a></li>";
    }
    if (strpos($userlevel, "24V") !== false && $virdiLevel == "Classic") {
        print "<li class='last'><a href='Proxy.php'><span>Mark Proxy</span></a></li>";
    }
    echo "\t\t\t\t\t\t\t\t\t\t</ul>\r\n\t\t\t\t\t\t\t\t\t</li>\t\t\t\t\t\t\t\t\r\n\t\t\t\t\t\t\t\t\t<li class='has-sub'><a href='#'><span>Overtime</span></a>\r\n\t\t\t\t\t\t\t\t\t\t<ul>\r\n\t\t\t\t\t\t\t\t\t\t\t";
    if (strpos($userlevel, "26V") !== false && $virdiLevel == "Classic") {
        print "<li class='last'><a href='PreApproveOvertime.php'><span>Pre Approve</span></a></li>";
    }
    if (strpos($userlevel, "15V") !== false) {
        print "<li class='last'><a href='ApproveOvertime.php'><span>Post Approve</span></a></li>";
    }
    if (strpos($userlevel, "15V") !== false) {
        print "<li class='last'><a href='ApproveOvertimeSummary.php'><span>Bulk Approve</span></a></li>";
    }
    echo "\t\t\t\t\t\t\t\t\t\t</ul>\r\n\t\t\t\t\t\t\t\t\t</li>\r\n\t\t\t\t\t\t\t\t\t<li class='has-sub'><a href='#'><span>Flags</span></a>\r\n\t\t\t\t\t\t\t\t\t\t<ul>\r\n\t\t\t\t\t\t\t\t\t\t\t";
    if (strpos($userlevel, "34V") !== false && $virdiLevel == "Classic") {
        print "<li class='last'><a href='FlagApplication.php'><span>Flag Application</span></a></li>";
    }
    if (strpos($userlevel, "25V") !== false && $virdiLevel == "Classic" || getRegister(encryptDecrypt($txtMACAddress), 7) == "173") {
        print "<li class='last'><a href='OffDay.php'><span>Pre Flag</span></a></li>";
    }
    if (strpos($userlevel, "23V") !== false && $virdiLevel == "Classic" || getRegister(encryptDecrypt($txtMACAddress), 7) == "173") {
        print "<li class='last'><a href='FlagDay.php'><span>Post Flag</span></a></li>";
    }
    if (strpos($userlevel, "32V") !== false && $virdiLevel == "Classic") {
        print "<li class='last'><a href='FlagRoster.php'><span>Flag Roaster</span></a></li>";
    }
    if (strpos($userlevel, "35V") !== false && $virdiLevel == "Classic") {
        print "<li class='last'><a href='CAGRoster.php'><span>Access Group Roaster</span></a></li>";
    }
    echo "\t\t\t\t\t\t\t\t\t\t</ul>\r\n\t\t\t\t\t\t\t\t\t</li>\r\n\t\t\t\t\t\t\t\t\t<li class='has-sub'><a href='#'><span>Delete Logs</span></a>\r\n\t\t\t\t\t\t\t\t\t\t<ul>\r\n\t\t\t\t\t\t\t\t\t\t\t";
    if (strpos($userlevel, "22V") !== false) {
        print "<li class='last'><a href='DeleteProcessedRecord.php'><span>Processed Logs</span></a></li>";
        print "<li class='last'><a href='DeleteProcessedRawRecord.php'><span>Raw Logs</span></a></li>";
    }
    if (strpos($userlevel, "25D") !== false && $virdiLevel == "Classic" || getRegister(encryptDecrypt($txtMACAddress), 7) == "173") {
        print "<li class='last'><a href='DeletePreFlaggedRecord.php'><span>PreFlag Logs</span></a></li>";
    }
    echo "\t\t\t\t\t\t\t\t\t\t</ul>\r\n\t\t\t\t\t\t\t\t\t</li>\r\n\t\t\t\t\t\t\t\t\t<li class='has-sub'><a href='#'><span>Others</span></a>\r\n\t\t\t\t\t\t\t\t\t\t<ul>\r\n\t\t\t\t\t\t\t\t\t\t\t";
    if (strpos($userlevel, "29V") !== false && $virdiLevel == "Classic") {
        print "<li class='last'><a href='ExemptLateInEarlyOutMoreBreak.php'><span>Exemptions</span></a></li>";
    }
    if (strpos($userlevel, "31V") !== false && $_SESSION[$session_variable . "UseShiftRoster"] == "Yes" && $virdiLevel == "Classic" || getRegister(encryptDecrypt($txtMACAddress), 7) == "173") {
        print "<li class='last'><a href='ShiftRoster.php'><span>Shift Roaster</span></a></li>";
    }
    if (strpos($userlevel, "16V") !== false && $_SESSION[$session_variable . "Project"] == "Yes" && $virdiLevel == "Classic") {
        print "<li class='last'><a href='AssignProject.php'><span>Assign Project</span></a></li>";
    }
    if (strpos($userlevel, "33V") !== false && $virdiLevel == "Classic") {
        print "<li class='last'><a href='DrillMaster.php'><span>Drill</span></a></li>";
    }
    echo "\t\t\t\t\t\t\t\t\t\t</ul>\r\n\t\t\t\t\t\t\t\t\t</li>\r\n\t\t\t\t\t\t\t\t</ul>\r\n\t\t\t\t\t\t\t\t";
}
echo "\t\t\t\t\t\t\t</li>\r\n\t\t\t\t\t\t\t<li class='active has-sub'><a href='#'><span>Reports</span></a>\r\n\t\t\t\t\t\t\t\t<ul>\r\n\t\t\t\t\t\t\t\t\t<li class='has-sub'><a href='#'><span>General</span></a>\r\n\t\t\t\t\t\t\t\t\t\t<ul>\r\n\t\t\t\t\t\t\t\t\t\t\t";
if (strpos($userlevel, "18R") !== false) {
    print "<li class='last'><a href='Empreports.php'><span>Employee Report</span></a></li>";
    print "<li class='last'><a href='ReportClockingLog.php'><span>Raw Log</span></a></li>";
    if ($virdiLevel != "Meal") {
        print "<li class='last'><a href='ReportAttendance.php'><span>Attendance</span></a></li>";
        print "<li class='last'><a href='ReportOddLog.php'><span>Odd Log</span></a></li>";
        print "<li class='last'><a href='ReportExitLog.php'><span>Exit Log</span></a></li>";
        print "<li class='last'><a href='ReportLateArrival.php'><span>Late Arrival</span></a></li>";
        print "<li class='last'><a href='ReportMoreBreak.php'><span>More Break</span></a></li>";
        print "<li class='last'><a href='ReportEarlyExit.php'><span>Early Exit</span></a></li>";
        print "<li class='last'><a href='ReportLIEO.php'><span>Late In/ Early Exit</span></a></li>";
        print "<li class='last'><a href='ReportAbsence.php'><span>Absent</span></a></li>";
        print "<li class='last'><a href='ReportAbsenceCount.php'><span>Absent Count</span></a></li>";
        print "<li class='last'><a href='MonthlyAttendanceSummary.php'><span>Monthly Summary Report</span></a></li>";
    }
    print "<li class='last'><a href='ReportMealCount.php'><span>Meal Count</span></a></li>";
    if ($_SESSION[$session_variable . "ExitTerminal"] == "Yes" && $virdiLevel == "Classic") {
        print "<li class='last'><a href='ReportExitTerminalError.php'><span>Exit Terminal Error</span></a></li>";
    }
}
if (strpos($userlevel, "27R") !== false) {
    print "<li class='last'><a href='ReportEmployee.php'><span>Employees</span></a></li>";
    print "<li class='last'><a href='ReportEmployeeBarCode.php'><span>Employees Bar Code</span></a></li>";
}
echo "\t\t\t\t\t\t\t\t\t\t</ul>\r\n\t\t\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\t\t\t\t\t<li class='has-sub'><a href='#'><span>Processed Logs</span></a>\r\n\t\t\t\t\t\t\t\t\t\t<ul>\r\n\t\t\t\t\t\t\t\t\t\t\t";
if (strpos($userlevel, "20R") !== false && $virdiLevel != "Meal") {
    print "<li class='last'><a href='ReportDailyClocking.php'><span>Processed Logs [Daily Routine]</span></a></li>";
    print "<li class='last'><a href='ReportWeeklyClocking.php'><span>Processed Logs [Weekly Routine]</span></a></li>";
}
echo "\t\t\t\t\t\t\t\t\t\t</ul>\r\n\t\t\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\t\t\t\t\t<li class='has-sub'><a href='#'><span>HR</span></a>\r\n\t\t\t\t\t\t\t\t\t\t<ul>\r\n\t\t\t\t\t\t\t\t\t\t\t";
if (strpos($userlevel, "21R") !== false && $virdiLevel != "Meal") {
    print "<li class='last'><a href='ReportWork.php'><span>Work</span></a></li>";
    print "<li class='last'><a href='ReportDailyRoster.php'><span>Roaster</span></a></li>";
    if ($virdiLevel == "Classic") {
        print "<li class='last'><a href='ReportPeriodicSummary.php'><span>Day Summary</span></a></li>";
        print "<li class='last'><a href='ReportMonthlyHours.php'><span>Hour Summary</span></a></li>";
        print "<li class='last'><a href='ReportMonthSummary.php'><span>Monthly Comparison</span></a></li>";
        print "<li class='last'><a href='ReportShiftSnapShot.php'><span>Shift Summary</span></a></li>";
        print "<li class='last'><a href='ReportGroupSummary.php'><span>Group Summary</span></a></li>";
    }
    print "<li class='last'><a href='ReportAttendanceSnapShot.php'><span>Snapshot</span></a></li>";
    print "<li class='last'><a href='ReportIOULog.php'><span>PayMaster IOU</span></a></li>";
    print "<li class='last'><a href='AttendanceSummary.php'><span>Attendance Summary</span></a></li>";
}
echo "\t\t\t\t\t\t\t\t\t\t</ul>\r\n\t\t\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\t\t\t\t\t<li class='has-sub'><a href='#'><span>Attendance</span></a>\r\n\t\t\t\t\t\t\t\t\t\t<ul>\r\n\t\t\t\t\t\t\t\t\t\t\t";
if (strpos($userlevel, "25R") !== false && $virdiLevel == "Classic") {
    print "<li class='last'><a href='ReportPreFlag.php'><span>Pre Flag</span></a></li>";
}
if (strpos($userlevel, "16R") !== false && $_SESSION[$session_variable . "Project"] == "Yes" && $virdiLevel == "Classic") {
    print "<li class='last'><a href='ReportProject.php'><span>Projects</span></a></li>";
}
if (strpos($userlevel, "26R") !== false && $virdiLevel == "Classic") {
    print "<li class='last'><a href='ReportPreApproval.php'><span>Pre Approval</span></a></li>";
}
if ((strpos($userlevel, "17R") !== false || strpos($userlevel, "24R") !== false) && $virdiLevel != "Meal") {
    print "<li class='last'><a href='ReportAlterTime.php'><span>Alter Logs</span></a></li>";
}
echo "\t\t\t\t\t\t\t\t\t\t</ul>\r\n\t\t\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\t\t\t\t\t<li class='has-sub'><a href='#'><span>Settings</span></a>\r\n\t\t\t\t\t\t\t\t\t\t<ul>\r\n\t\t\t\t\t\t\t\t\t\t\t";
if (strpos($userlevel, "30R") !== false && $virdiLevel == "Classic") {
    print "<li class='last'><a href='ReportFlagLimit.php'><span>Flag Limit</span></a></li>";
}
if (strpos($userlevel, "27R") !== false && $virdiLevel == "Classic") {
    print "<li class='last'><a href='ReportADA.php'><span>ADA</span></a></li>";
}
if (strpos($userlevel, "11R") !== false) {
    print "<li class='last'><a href='ReportUserTransact.php'><span>User Transactions</span></a></li>";
    print "<li class='last'><a href='ReportUserInfo.php'><span>User Info</span></a></li>";
    print "<li class='last'><a href='ReportUserRight.php'><span>User Rights</span></a></li>";
    print "<li class='last'><a href='ReportUserDept.php'><span>User-Dept Rights</span></a></li>";
    print "<li class='last'><a href='ReportUserDiv.php'><span>User-Division Rights</span></a></li>";
}
if (strpos($userlevel, "12R") !== false && $_SESSION[$session_variable . "RotateShift"] == "Yes" && $virdiLevel == "Classic") {
    print "<li class='last'><a href='ReportShiftRotation.php'><span>Shift Rotation</span></a></li>";
}
if (strpos($userlevel, "19R") !== false && $virdiLevel != "Meal") {
    print "<li class='last'><a href='ReportProcessLog.php'><span>System Logs</span></a></li>";
}
echo "\t\t\t\t\t\t\t\t\t\t</ul>\r\n\t\t\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\t\t\t\t</ul>\t\t\t\t\t\t\t\t\t\r\n\t\t\t\t\t\t\t</li>\r\n\t\t\t\t\t\t\t<li class='active has-sub'><a href='#'><span>Settings</span></a>\r\n\t\t\t\t\t\t\t\t<ul>\r\n\t\t\t\t\t\t\t\t\t<li class='has-sub'><a href='#'><span>Users</span></a>\r\n\t\t\t\t\t\t\t\t\t\t<ul>\r\n\t\t\t\t\t\t\t\t\t\t\t";
if (strpos($userlevel, "11V") !== false) {
    print "<li class='last'><a href='UserManagement.php'><span>Users</span></a></li>";
    print "<li class='last'><a href='AssignUserDept.php'><span>Assign User Dept Access</span></a></li>";
    print "<li class='last'><a href='AssignUserDiv.php'><span>Assign User Div Access</span></a></li>";
}
echo "\t\t\t\t\t\t\t\t\t\t</ul>\r\n\t\t\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\t\t\t\t\t<li class='has-sub'><a href='#'><span>Overtime/ Proxy</span></a>\r\n\t\t\t\t\t\t\t\t\t\t<ul>\r\n\t\t\t\t\t\t\t\t\t\t\t";
if (strpos($userlevel, "28V") !== false) {
    if ($virdiLevel != "Meal") {
        print "<li class='last'><a href='OTDayDate.php'><span>OT Days/ Dates</span></a></li>";
    }
    if ($virdiLevel == "Classic") {
        print "<li class='last'><a href='ProxyEmployeeExempt.php'><span>Proxy Exemption</span></a></li>";
        print "<li class='last'><a href='OTEmployeeExempt.php'><span>OT Exemption</span></a></li>";
        print "<li class='last'><a href='OTEmployeeExemptOTDay.php'><span>Special OT Days for Exempted Employees</span></a></li>";
    }
}
echo "\t\t\t\t\t\t\t\t\t\t</ul>\r\n\t\t\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\t\t\t\t\t<li class='has-sub'><a href='#'><span>Shifts</span></a>\r\n\t\t\t\t\t\t\t\t\t\t<ul>\r\n\t\t\t\t\t\t\t\t\t\t\t";
if (strpos($userlevel, "12V") !== false && $virdiLevel != "Meal") {
    print "<li class='last'><a href='ShiftMaster.php'><span>Shifts</span></a></li>";
    if ($virdiLevel == "Classic") {
        print "<li class='last'><a href='ShiftSummaryMaster.php'><span>Shift Summary</span></a></li>";
    }
    if ($_SESSION[$session_variable . "RotateShift"] == "Yes" && $virdiLevel == "Classic") {
        print "<li class='last'><a href='ShiftRotation.php'><span>Shift Rotation</span></a></li>";
    }
    if (strpos($userlevel, "14V") !== false && $virdiLevel != "Meal") {
        print "<li class='last'><a href='AssignShift.php'><span>Assign Shift to Employees</span></a></li>";
    }
    if (strpos($userlevel, "35V") !== false && $virdiLevel == "Classic") {
        print "<li class='last'><a href='AssignShiftCAG.php'><span>Assign Shift to Access Group</span></a></li>";
    }
}
echo "\t\t\t\t\t\t\t\t\t\t</ul>\r\n\t\t\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\t\t\t\t\t<li class='has-sub'><a href='#'><span>Employees</span></a>\r\n\t\t\t\t\t\t\t\t\t\t<ul>\r\n\t\t\t\t\t\t\t\t\t\t\t";
if (strpos($userlevel, "27V") !== false) {
    print "<li class='last'><a href='EmployeeMaster.php'><span>Employee Info</span></a></li>";
}
if (strpos($userlevel, "30V") !== false && $virdiLevel == "Classic") {
    print "<li class='last'><a href='EmployeeFlagLimit.php'><span>Flag Limits</span></a></li>";
}
echo "\t\t\t\t\t\t\t\t\t\t</ul>\r\n\t\t\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\t\t\t\t\t<li class='has-sub'><a href='#'><span>General</span></a>\r\n\t\t\t\t\t\t\t\t\t\t<ul>\r\n\t\t\t\t\t\t\t\t\t\t\t";
if (strpos($userlevel, "16A") !== false && $_SESSION[$session_variable . "Project"] == "Yes" && $virdiLevel == "Classic") {
    print "<li class='last'><a href='ProjectMaster.php'><span>Projects</span></a></li>";
}
if (strpos($userlevel, "13V") !== false && $virdiLevel == "Classic") {
    print "<li class='last'><a href='GroupMaster.php'><span>Report Groups</span></a></li>";
}
if (strpos($userlevel, "35V") !== false && $virdiLevel == "Classic") {
    print "<li class='last'><a href='ContractAccess.php'><span>Contract Access Groups</span></a></li>";
}
if (strpos($userlevel, "13V") !== false && $virdiLevel == "Classic") {
    print "<li class='last'><a href='AssignTerminal.php'><span>Assign Terminal</span></a></li>";
    if ($_SESSION[$session_variable . "ExitTerminal"] == "Yes") {
        print "<li class='last'><a href='ExitTerminal.php'><span>Exit Terminals</span></a></li>";
    }
    print "<li class='last'><a href='MealTerminal.php'><span>Meal Terminals</span></a></li>";
}
if (strpos($userlevel, "19V") !== false) {
    print "<li class='last'><a href='Archive.php'><span>DB Archive</span></a></li>";
    if ($virdiLevel == "Classic") {
        print "<li class='last'><a href='UNISMap.php'><span>UNIS Map</span></a></li>";
        print "<li class='last'><a href='MigrateMaster.php'><span>Payroll Migration</span></a></li>";
    }
    print "<li class='last'><a href='OtherSetting.php'><span>Global</span></a></li>";
//    print "<li class='last'><a href='PayMasterweb.php'><span>PayMaster Web</span></a></li>";
}
//print "\t\t\t\t\t\t\t\t\t\t</ul>\r\n\t\t\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\t\t\t\t</ul>\r\n\t\t\t\t\t\t\t</li>\r\n\t\t\t\t\t\t\t<li class='active has-sub'><a href='#'><span>Personalize</span></a>\r\n\t\t\t\t\t\t\t\t<ul>\r\n\t\t\t\t\t\t\t\t\t<li class='active'><a href='Password.php'><span>Password</span></a></li>\r\n\t\t\t\t\t\t\t\t\t<li class='active'><a href='TaskMaster.php'><span>Tasks</span></a></li>\r\n\t\t\t\t\t\t\t\t\t<li class='active'><a href='Dashboard.php'><span>Dashboard</span></a></li>\r\n\t\t\t\t\t\t\t\t</ul>\r\n\t\t\t\t\t\t\t</li><li class='active'><a href='Login.php?act=signout'><span>Logout</span></a></li>\r\n\t\t\t\t\t\t</ul>\t\t\t\t\t\t\r\n\t\t\t\t\t</div>\r\n\t\t\t\t</td>\r\n\t\t\t</tr>\t\t\t\r\n\t</table>";
//if($_SESSION['virdiusername'] == 'BANKA MADHAV' || $_SESSION['virdiusername'] == 'KUMAR PADALA' || $_SESSION['virdiusername'] == 'MIHIR THAKKAR' || $_SESSION['virdiusername'] == 'SABIR ALI'){
 if($_SESSION['virdidesign'] == 'HOD' || $_SESSION['virdidesign'] == 'Admin' || $_SESSION['virdidesign'] == 'PM'){
    echo "\t\t\t\t\t\t\t\t\t\t</ul>\r\n\t\t\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\t\t\t\t</ul>\r\n\t\t\t\t\t\t\t</li>\r\n\t\t\t\t\t\t\t<li class='active has-sub'><a href='#'><span>Personalize</span></a>\r\n\t\t\t\t\t\t\t\t<ul>\r\n\t\t\t\t\t\t\t\t\t<li class='active'><a href='Password.php'><span>Password</span></a></li>\r\n\t\t\t\t\t\t\t\t\t<li class='active'><a href='TaskMaster.php'><span>Tasks</span></a></li>\r\n\t\t\t\t\t\t\t\t\t<li class='active'><a href='Dashboard.php'><span>Dashboard</span></a></li>\r\n\t\t\t\t\t\t\t\t</ul>\r\n\t\t\t\t\t\t\t</li>"
         . "<li class='active has-sub'><a href='#'><span>Casual</span></a>\r\n\t\t\t\t\t\t\t\t<ul>\r\n\t\t\t\t\t\t\t\t\t<li class='active'><a href='CasualOnboardInfoView.php'><span>Casual Onboard Info</span></a></li>\r\n\t\t\t\t\t\t\t\t\t<li class='active'><a href='GradeRateMasterView.php'><span>Grade and Rate Master</span></a></li>\r\n\t\t\t\t\t\t\t\t\t<li class='active'><a href='ProjectMaster.php'><span>Project Master</span></a></li>\r\n\t\t\t\t\t\t\t\t\t<li class='active'><a href='ProjectHoursAllocationView.php'><span>Project Wise Hours Allocation</span></a></li>\r\n\t\t\t\t\t\t\t\t\t<li class='active'><a href='ReportingHireKey.php'><span>Reporting hierarchy</span></a></li>\r\n\t\t\t\t\t\t\t\t\t<li class='active'><a href='AssignMangersProject.php'><span>Supervisor Assign Manager/Project</span></a></li>\r\n\t\t\t\t\t\t\t\t\t<li class='active'><a href='HodAssignManager.php'><span>HOD Assign Manager/Project</span></a></li>\r\n\t\t\t\t\t\t\t\t\t<li class='active'><a href='CasualPaysheet.php'><span>Casual Paysheet</span></a></li>\r\n\t\t\t\t\t\t\t\t\t<li class='active'><a href='PmFmProjectDetails.php'><span>(PM/FM) Project Details</span></a></li>\r\n\t\t\t\t\t\t\t\t<li class='active'><a href='PaysheetMigration.php'><span>Paysheet Migration</span></a></li>\r\n\t\t\t\t\t\t\t\t</ul>\r\n\t\t\t\t\t\t\t</li>\r\n\t\t\t\t\t\t\t"
         . "<li class='active'><a href='Login.php?act=signout'><span>Logout</span></a></li>\r\n\t\t\t\t\t\t</ul>\t\t\t\t\t\t\r\n\t\t\t\t\t</div>\r\n\t\t\t\t</td>\r\n\t\t\t</tr>\t\t\t\r\n\t</table>"; 
 }else{
    echo "\t\t\t\t\t\t\t\t\t\t</ul>\r\n\t\t\t\t\t\t\t\t\t<li>\r\n\t\t\t\t\t\t\t\t</ul>\r\n\t\t\t\t\t\t\t</li>\r\n\t\t\t\t\t\t\t<li class='active has-sub'><a href='#'><span>Personalize</span></a>\r\n\t\t\t\t\t\t\t\t<ul>\r\n\t\t\t\t\t\t\t\t\t<li class='active'><a href='Password.php'><span>Password</span></a></li>\r\n\t\t\t\t\t\t\t\t\t<li class='active'><a href='TaskMaster.php'><span>Tasks</span></a></li>\r\n\t\t\t\t\t\t\t\t\t<li class='active'><a href='Dashboard.php'><span>Dashboard</span></a></li>\r\n\t\t\t\t\t\t\t\t</ul>\r\n\t\t\t\t\t\t\t</li><li class='active has-sub'><a href='#'><span>Casual</span></a>\r\n\t\t\t\t\t\t\t\t<ul>\r\n\t\t\t\t\t\t\t\t\t<li class='active'><a href='ProjectHoursAllocationView.php'><span>Project Wise Hours Allocation</span></a></li>\r\n\t\t\t\t\t\t\t\t\t</ul>\r\n\t\t\t\t\t\t\t</li>\r\n\t\t\t\t\t\t\t<li class='active'><a href='Login.php?act=signout'><span>Logout</span></a></li>\r\n\t\t\t\t\t\t</ul>\t\t\t\t\t\t\r\n\t\t\t\t\t</div>\r\n\t\t\t\t</td>\r\n\t\t\t</tr>\t\t\t\r\n\t</table>";  
 }

// <li class='active'><a href='AnalysisReport.php'><span>Detail Analysis project report</span></a></li>\r\n\t\t\t\t\t\t\t\t<li class='active'><a href='SummaryAnalysisReport.php'><span>Summary Analysis Project Report</span></a></li>\r\n\t\t\t\t\t\t\t\t
?>