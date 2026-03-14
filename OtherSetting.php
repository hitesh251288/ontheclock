<?php
ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "19";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$username = $_SESSION[$session_variable . "username"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$userstatus = $_SESSION[$session_variable . "userstatus"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=OtherSetting.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$iconn = openIConnection();
$act = $_POST["act"];
$message = "Global Settings";
if ($act == "editRecord") {
    $txtMinClockinPeriod = $_POST["txtMinClockinPeriod"];
    $lstExitTerminal = $_POST["lstExitTerminal"];
    $lstProject = $_POST["lstProject"];
    $lstNoExitException = $_POST["lstNoExitException"];
    $lstAutoAssignTerminal = $_POST["lstAutoAssignTerminal"];
    $txtNightShiftMaxOutTime = $_POST["txtNightShiftMaxOutTime"];
    $lstRotateShift = $_POST["lstRotateShift"];
    $txtDivColumnName = $_POST["txtDivColumnName"];
    $txtRemarkColumnName = $_POST["txtRemarkColumnName"];
    $txtIDColumnName = $_POST["txtIDColumnName"];
    $txtRosterColumns = $_POST["chkIDColumn"] . $_POST["chkDept"] . $_POST["chkDiv"] . $_POST["chkRmk"] . $_POST["chkShift"] . $_POST["chkEntry"] . $_POST["chkStart"] . $_POST["chkBreakOut"] . $_POST["chkBreakIn"] . $_POST["chkClose"] . $_POST["chkExit"] . $_POST["chkEarlyIn"] . $_POST["chkLateIn"] . $_POST["chkLessBreak"] . $_POST["chkMoreBreak"] . $_POST["chkEarlyOut"] . $_POST["chkLateOut"] . $_POST["chkGrace"] . $_POST["chkNormal"] . $_POST["chkOT"] . $_POST["chkAppOT"] . $_POST["chkFlag"] . $_POST["chkOT1"] . $_POST["chkOT2"] . $_POST["chkTH"] . $_POST["chkF1"] . $_POST["chkF2"] . $_POST["chkF3"] . $_POST["chkF4"] . $_POST["chkF5"];
    $txtBackupPath = $_POST["txtBackupPath"];
    $txtLockDate = $_POST["txtLockDate"];
    $txtSMTPServer = $_POST["txtSMTPServer"];
    $txtSMTPFrom = $_POST["txtSMTPFrom"];
    $txtSMTPUser = $_POST["txtSMTPUser"];
    $txtSMTPPass = $_POST["txtSMTPPass"];
    $txtSMTPPort = $_POST["txtSMTPPort"];
    if ($txtSMTPPort == "") {
        $txtSMTPPort = "25";
    }
    $lstSMTPSSL = $_POST["lstSMTPSSL"];
    if ($lstSMTPSSL == "") {
        $lstSMTPSSL = "None";
    }
    $lstDBType = $_POST["lstDBType"];
    $txtDBIP = $_POST["txtDBIP"];
    $txtDBName = $_POST["txtDBName"];
    $txtDBUser = $_POST["txtDBUser"];
    $txtDBPass = $_POST["txtDBPass"];
    $txtEmployeeCodeLength = $_POST["txtEmployeeCodeLength"];
    $txtLateDays = $_POST["txtLateDays"];
    if ($txtLateDays == "") {
        $txtLateDays = "0";
    }
    $txtPhoneColumnName = $_POST["txtPhoneColumnName"];
    $lstLocationSynchShift = $_POST["lstLocationSynchShift"];
    if ($lstLocationSynchShift == "") {
        $lstLocationSynchShift = "Server";
    }
    $lstEmployeeEmailField = $_POST["lstEmployeeEmailField"];
    $lstEmployeeSMSField = $_POST["lstEmployeeSMSField"];
    $lstApproveOTIgnoreActual = $_POST["lstApproveOTIgnoreActual"];
    $lstAutoApproveOT = $_POST["lstAutoApproveOT"];
    $lstRoundOffAOT = $_POST["lstRoundOffAOT"];
    $lstMoveNS = $_POST["lstMoveNS"];
    $lstUseShiftRoster = $_POST["lstUseShiftRoster"];
    $lstFlagLimitType = $_POST["lstFlagLimitType"];
    $lstAutoResetOT12 = $_POST["lstAutoResetOT12"];
    $txhClientLogo = $_POST["txhClientLogo"];
    $txtMapTableName = $_POST["txtMapTableName"];
    $lstMapOverwrite = $_POST["lstMapOverwrite"];
    $txtMapEID = $_POST["txtMapEID"];
    $txtMapEName = $_POST["txtMapEName"];
    $txtMapIDNo = $_POST["txtMapIDNo"];
    $txtMapDept = $_POST["txtMapDept"];
    $txtMapDiv = $_POST["txtMapDiv"];
    $txtMapRemark = $_POST["txtMapRemark"];
    $txtMapShift = $_POST["txtMapShift"];
    $txtMapPhone = $_POST["txtMapPhone"];
    $txtMapStatus = $_POST["txtMapStatus"];
    $txtMapActive = $_POST["txtMapActive"];
    $txtMapPassive = $_POST["txtMapPassive"];
    $lstMapDataCOMPayroll = $_POST["lstMapDataCOMPayroll"];
    $lstMapUpdateDate = $_POST["lstMapUpdateDate"];
    $lstMapUpdateSalary = $_POST["lstMapUpdateSalary"];
    $lstMapProject = $_POST["lstMapProject"];
    $lstMapCostCentre = $_POST["lstMapCostCentre"];
    $txtMapF1 = $_POST["txtMapF1"];
    $txtMapF2 = $_POST["txtMapF2"];
    $txtMapF3 = $_POST["txtMapF3"];
    $txtMapF4 = $_POST["txtMapF4"];
    $txtMapF5 = $_POST["txtMapF5"];
    $txtMapF6 = $_POST["txtMapF6"];
    $txtMapF7 = $_POST["txtMapF7"];
    $txtMapF8 = $_POST["txtMapF8"];
    $txtMapF9 = $_POST["txtMapF9"];
    $lstPreApproveOTValue = $_POST["lstPreApproveOTValue"];
    $txtMealCouponPrinterName = $_POST["txtMealCouponPrinterName"];
    $txtMealCouponFont = $_POST["txtMealCouponFont"];
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
    $lstCAGR = $_POST["lstCAGR"];
    if ($_SESSION[$session_variable . "VirdiLevel"] == "Classic") {
        $query = "UPDATE OtherSettingMaster SET MinClockinPeriod = " . $txtMinClockinPeriod . ", ExitTerminal = '" . $lstExitTerminal . "', Project = '" . $lstProject . "', NightShiftMaxOutTime = " . $txtNightShiftMaxOutTime . ", NoExitException = '" . $lstNoExitException . "', RotateShift = '" . $lstRotateShift . "', DivColumnName = '" . replaceString($txtDivColumnName, false) . "', RemarkColumnName = '" . replaceString($txtRemarkColumnName, false) . "', IDColumnName = '" . replaceString($txtIDColumnName, false) . "', RosterColumns = '" . $txtRosterColumns . "', Ex1 = '" . $txtBackupPath . "', LockDate = " . insertDate($txtLockDate) . ", SMTPServer = '" . $txtSMTPServer . "', SMTPFrom = '" . $txtSMTPFrom . "', SMTPUsername = '" . $txtSMTPUser . "', SMTPPassword = '" . $txtSMTPPass . "', SMTPPort = '" . $txtSMTPPort . "', SMTPSSL = '" . $lstSMTPSSL . "', DBType = '" . $lstDBType . "', DBIP = '" . $txtDBIP . "', DBName = '" . $txtDBName . "', DBUser = '" . $txtDBUser . "', DBPass = '" . $txtDBPass . "', EmployeeCodeLength = '" . $txtEmployeeCodeLength . "', LateDays = '" . $txtLateDays . "', PhoneColumnName = '" . replaceString($txtPhoneColumnName, false) . "', LocationSynchShift = '" . replaceString($lstLocationSynchShift, false) . "', EmployeeEmailField = '" . replaceString($lstEmployeeEmailField, false) . "', EmployeeSMSField = '" . replaceString($lstEmployeeSMSField, false) . "', ApproveOTIgnoreActual = '" . replaceString($lstApproveOTIgnoreActual, false) . "', AutoAssignTerminal = '" . $lstAutoAssignTerminal . "', AutoApproveOT = '" . $lstAutoApproveOT . "', RoundOffAOT = '" . $lstRoundOffAOT . "', UseShiftRoster = '" . $lstUseShiftRoster . "', PreApproveOTValue = '" . $lstPreApproveOTValue . "', MealCouponPrinterName = '" . $txtMealCouponPrinterName . "', MealCouponFont = '" . $txtMealCouponFont . "', FlagLimitType = '" . $lstFlagLimitType . "', AutoResetOT12 = '" . $lstAutoResetOT12 . "', F1 = '" . $txtF1 . "', F2 = '" . $txtF2 . "', F3 = '" . $txtF3 . "', F4 = '" . $txtF4 . "', F5 = '" . $txtF5 . "', F6 = '" . $txtF6 . "', F7 = '" . $txtF7 . "', F8 = '" . $txtF8 . "', F9 = '" . $txtF9 . "', F10 = '" . $txtF10 . "', CAGR = '" . $lstCAGR . "' ";
    } else {
        $query = "UPDATE OtherSettingMaster SET MinClockinPeriod = " . $txtMinClockinPeriod . ", ExitTerminal = '" . $lstExitTerminal . "', Project = '" . $lstProject . "', NightShiftMaxOutTime = " . $txtNightShiftMaxOutTime . ", NoExitException = '" . $lstNoExitException . "', RotateShift = '" . $lstRotateShift . "', DivColumnName = '" . replaceString($txtDivColumnName, false) . "', RemarkColumnName = '" . replaceString($txtRemarkColumnName, false) . "', IDColumnName = '" . replaceString($txtIDColumnName, false) . "', RosterColumns = '" . $txtRosterColumns . "', Ex1 = '" . $txtBackupPath . "', LockDate = " . insertDate($txtLockDate) . ", SMTPServer = '" . $txtSMTPServer . "', SMTPFrom = '" . $txtSMTPFrom . "', SMTPUsername = '" . $txtSMTPUser . "', SMTPPassword = '" . $txtSMTPPass . "', DBType = '" . $lstDBType . "', DBIP = '" . $txtDBIP . "', DBName = '" . $txtDBName . "', DBUser = '" . $txtDBUser . "', DBPass = '" . $txtDBPass . "', EmployeeCodeLength = '" . $txtEmployeeCodeLength . "', LateDays = '" . $txtLateDays . "', PhoneColumnName = '" . replaceString($txtPhoneColumnName, false) . "', ApproveOTIgnoreActual = '" . replaceString($lstApproveOTIgnoreActual, false) . "', AutoAssignTerminal = '" . $lstAutoAssignTerminal . "', AutoApproveOT = '" . $lstAutoApproveOT . "', RoundOffAOT = '" . $lstRoundOffAOT . "', FlagLimitType = '" . $lstFlagLimitType . "'";
    }
    if (updateIData($iconn, $query, true)) {
        $text = "Global Setting: MinClockin=" . $txtMinClockinPeriod . ", ExitTmnl=" . substr($lstExitTerminal, 0, 1) . ", Prjct=" . substr($lstProject, 0, 1) . ", NSMaxOutTime=" . $txtNightShiftMaxOutTime . ", NoExitExcptn=" . substr($lstNoExitException, 0, 1) . ", RotShift=" . substr($lstRotateShift, 0, 1) . ", Div = " . replaceString($txtDivColumnName, false) . ", Remark = " . replaceString($txtRemarkColumnName, false) . ", SocialNo=" . replaceString($txtIDColumnName, false) . ", Backup=" . $txtBackupPath . ", LockDt=" . $txtLockDate . ", SMTP=" . $txtSMTPServer . ", Email=" . $txtSMTPFrom . ", User=" . $txtSMTPUser . ", Port=" . $txtSMTPPort . ", SSL=" . $lstSMTPSSL . ", DB=" . $lstDBType . ", DBIP=" . $txtDBIP . ", DBName=" . $txtDBName . ", DBUser=" . $txtDBUser . ", EmpCodeLen=" . $txtEmployeeCodeLength . ", LateDays=" . $txtLateDays . ", PhoneCol=" . replaceString($txtPhoneColumnName, false) . ", LocSynch=" . replaceString($lstLocationSynchShift, false) . ", EmpEmail = " . replaceString($lstEmployeeEmailField, false) . ", EmpSMS = " . replaceString($lstEmployeeSMSField, false) . ", IgnoreActualOTForAppOT=" . substr($lstApproveOTIgnoreActual, 0, 1) . ", AutoAssnTmnl=" . substr($lstAutoAssignTerminal, 0, 1) . ", AutoAppOT=" . substr($lstAutoApproveOT, 0, 1) . ", AOT30=" . $lstRoundOffAOT . ", SR=" . substr($lstUseShiftRoster, 0, 1) . ", PreAppOTVal=" . $lstPreApproveOTValue . ", Printer=" . $txtMealCouponPrinterName . ", Font=" . $txtMealCouponFont . ", FlagLimitType = " . $lstFlagLimitType . ", AutoResetOT12 = " . $lstAutoResetOT12 . ", F1 = " . $txtF1 . ", F2 = " . $txtF2 . ", F3 = " . $txtF3 . ", F4 = " . $txtF4 . ", F5 = " . $txtF5 . ", F6 = " . $txtF6 . ", F7 = " . $txtF7 . ", F8 = " . $txtF8 . ", F9 = " . $txtF9 . ", F10 = " . $txtF10 . ", GroupRotation = " . $lstCAGR;
        $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
        updateIData($iconn, $query, true);
    }
    if ($lstMapOverwrite != "" && $_SESSION[$session_variable . "VirdiLevel"] == "Classic") {
        $query = "DELETE FROM PayrollMap";
        if (updateIData($iconn, $query, true)) {
            $query = "ALTER TABLE  payrollmap CHANGE  DataCOMPayroll DataCOMPayroll VARCHAR( 50 ) NOT NULL DEFAULT  'No'";
            if (updateIData($iconn, $query, true)) {
                $query = "INSERT INTO PayrollMap (TableName, Overwrite, EID, EName, IDNo, Dept, Division, Remark, Shift, Phone, Status, ActiveValue, PassiveValue, DataCOMPayroll, UpdateDate, UpdateSalary, Project, CostCentre, F1, F2, F3, F4, F5, F6, F7, F8, F9) VALUES ('" . $txtMapTableName . "', '" . $lstMapOverwrite . "', '" . $txtMapEID . "', '" . $txtMapEName . "', '" . $txtMapIDNo . "', '" . $txtMapDept . "', '" . $txtMapDiv . "', '" . $txtMapRemark . "', '" . $txtMapShift . "', '" . $txtMapPhone . "', '" . $txtMapStatus . "', '" . $txtMapActive . "', '" . $txtMapPassive . "', '" . $lstMapDataCOMPayroll . "', '" . $lstMapUpdateDate . "', '" . $lstMapUpdateSalary . "', '" . $lstMapProject . "', '" . $lstMapCostCentre . "', '" . $txtMapF1 . "', '" . $txtMapF2 . "', '" . $txtMapF3 . "', '" . $txtMapF4 . "', '" . $txtMapF5 . "', '" . $txtMapF6 . "', '" . $txtMapF7 . "', '" . $txtMapF8 . "', '" . $txtMapF9 . "')";
                if (updateIData($iconn, $query, true)) {
                    $text = "Updated Payroll Mapping SET Table Name = " . $txtMapTableName . ", Overwrite TO = " . $lstMapOverwrite . ", Employee Name = " . $txtMapEName . ", ID No = " . $txtMapIDNo . ", Dept = " . $txtMapDept . ", Div/Desg = " . $txtMapDiv . ", Remark = " . $txtMapRemark . ", Shift = " . $txtMapShift . ", Phone = " . $txtMapPhone . ", Status = " . $txtMapStatus . ", Active Value = " . $txtMapActive . ", Passive Value = " . $txtMapPassive . ", Use Payroll = " . $lstMapDataCOMPayroll . ", Update Dates = " . $lstMapUpdateDate . ", Update Salary = " . $lstMapUpdateSalary . ", Project = " . $lstMapProject . ", Cost Centre = " . $lstMapCostCentre . ", F1 = " . $txtMapF1 . ", F2 = " . $txtMapF2 . ", F3 = " . $txtMapF3 . ", F4 = " . $txtMapF4 . ", F5 = " . $txtMapF5 . ", F6 = " . $txtMapF6 . ", F7 = " . $txtMapF7 . ", F8 = " . $txtMapF8 . ", F9 = " . $txtMapF9;
                    $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                    updateIData($iconn, $query, true);
                }
            }
        }
    }
    if ($_SESSION[$session_variable . "VirdiLevel"] == "Classic") {
        $query = "UPDATE TLSFlag SET ";
        $text = "Updated Flags to be included for Total Count in Attendance Snap Shot Report: ";
        if ($_POST["chkViolet"] == "on") {
            $query = $query . " Violet = 'Yes', ";
            $text = $text . " Violet = Yes, ";
        } else {
            $query = $query . " Violet = 'No', ";
            $text = $text . " Violet = No, ";
        }
        if ($_POST["chkIndigo"] == "on") {
            $query = $query . " Indigo = 'Yes', ";
            $text = $text . " Indigo = Yes, ";
        } else {
            $query = $query . " Indigo = 'No', ";
            $text = $text . " Indigo = No, ";
        }
        if ($_POST["chkBlue"] == "on") {
            $query = $query . " Blue = 'Yes', ";
            $text = $text . " Blue = Yes, ";
        } else {
            $query = $query . " Blue = 'No', ";
            $text = $text . " Blue = No, ";
        }
        if ($_POST["chkGreen"] == "on") {
            $query = $query . " Green = 'Yes', ";
            $text = $text . " Green = Yes, ";
        } else {
            $query = $query . " Green = 'No', ";
            $text = $text . " Green = No, ";
        }
        if ($_POST["chkYellow"] == "on") {
            $query = $query . " Yellow = 'Yes', ";
            $text = $text . " Yellow = Yes, ";
        } else {
            $query = $query . " Yellow = 'No', ";
            $text = $text . " Yellow = No, ";
        }
        if ($_POST["chkOrange"] == "on") {
            $query = $query . " Orange = 'Yes', ";
            $text = $text . " Orange = Yes, ";
        } else {
            $query = $query . " Orange = 'No', ";
            $text = $text . " Orange = No, ";
        }
        if ($_POST["chkRed"] == "on") {
            $query = $query . " Red = 'Yes', ";
            $text = $text . " Red = Yes, ";
        } else {
            $query = $query . " Red = 'No', ";
            $text = $text . " Red = No, ";
        }
        if ($_POST["chkGray"] == "on") {
            $query = $query . " Gray = 'Yes', ";
            $text = $text . " Gray = Yes, ";
        } else {
            $query = $query . " Gray = 'No', ";
            $text = $text . " Gray = No, ";
        }
        if ($_POST["chkBrown"] == "on") {
            $query = $query . " Brown = 'Yes', ";
            $text = $text . " Brown = Yes, ";
        } else {
            $query = $query . " Brown = 'No', ";
            $text = $text . " Brown = No, ";
        }
        if ($_POST["chkPurple"] == "on") {
            $query = $query . " Purple = 'Yes', ";
            $text = $text . " Purple = Yes, ";
        } else {
            $query = $query . " Purple = 'No', ";
            $text = $text . " Purple = No, ";
        }
        if ($_POST["chkMagenta"] == "on") {
            $query = $query . " Magenta = 'Yes', ";
            $text = $text . " Magenta = Yes, ";
        } else {
            $query = $query . " Magenta = 'No', ";
            $text = $text . " Magenta = No, ";
        }
        if ($_POST["chkTeal"] == "on") {
            $query = $query . " Teal = 'Yes', ";
            $text = $text . " Teal = Yes, ";
        } else {
            $query = $query . " Teal = 'No', ";
            $text = $text . " Teal = No, ";
        }
        if ($_POST["chkAqua"] == "on") {
            $query = $query . " Aqua = 'Yes', ";
            $text = $text . " Aqua = Yes, ";
        } else {
            $query = $query . " Aqua = 'No', ";
            $text = $text . " Aqua = No, ";
        }
        if ($_POST["chkSafron"] == "on") {
            $query = $query . " Safron = 'Yes', ";
            $text = $text . " Safron = Yes, ";
        } else {
            $query = $query . " Safron = 'No', ";
            $text = $text . " Safron = No, ";
        }
        if ($_POST["chkAmber"] == "on") {
            $query = $query . " Amber = 'Yes', ";
            $text = $text . " Amber = Yes, ";
        } else {
            $query = $query . " Amber = 'No', ";
            $text = $text . " Amber = No, ";
        }
        if ($_POST["chkGold"] == "on") {
            $query = $query . " Gold = 'Yes', ";
            $text = $text . " Gold = Yes, ";
        } else {
            $query = $query . " Gold = 'No', ";
            $text = $text . " Gold = No, ";
        }
        if ($_POST["chkVermilion"] == "on") {
            $query = $query . " Vermilion = 'Yes', ";
            $text = $text . " Vermilion = Yes, ";
        } else {
            $query = $query . " Vermilion = 'No', ";
            $text = $text . " Vermilion = No, ";
        }
        if ($_POST["chkSilver"] == "on") {
            $query = $query . " Silver = 'Yes', ";
            $text = $text . " Silver = Yes, ";
        } else {
            $query = $query . " Silver = 'No', ";
            $text = $text . " Silver = No, ";
        }
        if ($_POST["chkMaroon"] == "on") {
            $query = $query . " Maroon = 'Yes', ";
            $text = $text . " Maroon = Yes, ";
        } else {
            $query = $query . " Maroon = 'No', ";
            $text = $text . " Maroon = No, ";
        }
        if ($_POST["chkPink"] == "on") {
            $query = $query . " Pink = 'Yes' ";
            $text = $text . " Pink = Yes ";
        } else {
            $query = $query . " Pink = 'No' ";
            $text = $text . " Pink = No ";
        }
        if (updateIData($iconn, $query, true)) {
            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
            updateIData($iconn, $query, true);
        }
        $query = "SELECT Flag FROM FlagTitle";
        $text = "UPDATE Flag Titles: ";
        $result = mysqli_query($conn, $query);
        while ($cur = mysqli_fetch_row($result)) {
            $query = "UPDATE FlagTitle SET Title = '" . $_POST["txtFlagTitle" . $cur[0]] . "', FlagLink = '" . $_POST["chkFlagLink" . $cur[0]] . "' WHERE Flag = '" . $cur[0] . "'";
            $text .= " " . $cur[0] . " = " . $_POST["txtFlagTitle" . $cur[0]] . ", Link To: " . $_POST["chkFlagLink" . $cur[0]] . " :: ";
            updateIData($iconn, $query, true);
        }
        $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
        updateIData($iconn, $query, true);
        $query = "UPDATE AccessFlag SET ";
        $text = "Allow Access for Employees on Days with Flags: ";
        if ($_POST["chkAViolet"] == "on") {
            $query = $query . " Violet = 'Yes', ";
            $text = $text . " Violet = Yes, ";
        } else {
            $query = $query . " Violet = 'No', ";
            $text = $text . " Violet = No, ";
        }
        if ($_POST["chkAIndigo"] == "on") {
            $query = $query . " Indigo = 'Yes', ";
            $text = $text . " Indigo = Yes, ";
        } else {
            $query = $query . " Indigo = 'No', ";
            $text = $text . " Indigo = No, ";
        }
        if ($_POST["chkABlue"] == "on") {
            $query = $query . " Blue = 'Yes', ";
            $text = $text . " Blue = Yes, ";
        } else {
            $query = $query . " Blue = 'No', ";
            $text = $text . " Blue = No, ";
        }
        if ($_POST["chkAGreen"] == "on") {
            $query = $query . " Green = 'Yes', ";
            $text = $text . " Green = Yes, ";
        } else {
            $query = $query . " Green = 'No', ";
            $text = $text . " Green = No, ";
        }
        if ($_POST["chkAYellow"] == "on") {
            $query = $query . " Yellow = 'Yes', ";
            $text = $text . " Yellow = Yes, ";
        } else {
            $query = $query . " Yellow = 'No', ";
            $text = $text . " Yellow = No, ";
        }
        if ($_POST["chkAOrange"] == "on") {
            $query = $query . " Orange = 'Yes', ";
            $text = $text . " Orange = Yes, ";
        } else {
            $query = $query . " Orange = 'No', ";
            $text = $text . " Orange = No, ";
        }
        if ($_POST["chkARed"] == "on") {
            $query = $query . " Red = 'Yes', ";
            $text = $text . " Red = Yes, ";
        } else {
            $query = $query . " Red = 'No', ";
            $text = $text . " Red = No, ";
        }
        if ($_POST["chkAGray"] == "on") {
            $query = $query . " Gray = 'Yes', ";
            $text = $text . " Gray = Yes, ";
        } else {
            $query = $query . " Gray = 'No', ";
            $text = $text . " Gray = No, ";
        }
        if ($_POST["chkABrown"] == "on") {
            $query = $query . " Brown = 'Yes', ";
            $text = $text . " Brown = Yes, ";
        } else {
            $query = $query . " Brown = 'No', ";
            $text = $text . " Brown = No, ";
        }
        if ($_POST["chkAPurple"] == "on") {
            $query = $query . " Purple = 'Yes', ";
            $text = $text . " Purple = Yes, ";
        } else {
            $query = $query . " Purple = 'No', ";
            $text = $text . " Purple = No, ";
        }
        if ($_POST["chkAMagenta"] == "on") {
            $query = $query . " Magenta = 'Yes', ";
            $text = $text . " Magenta = Yes, ";
        } else {
            $query = $query . " Magenta = 'No', ";
            $text = $text . " Magenta = No, ";
        }
        if ($_POST["chkATeal"] == "on") {
            $query = $query . " Teal = 'Yes', ";
            $text = $text . " Teal = Yes, ";
        } else {
            $query = $query . " Teal = 'No', ";
            $text = $text . " Teal = No, ";
        }
        if ($_POST["chkAAqua"] == "on") {
            $query = $query . " Aqua = 'Yes', ";
            $text = $text . " Aqua = Yes, ";
        } else {
            $query = $query . " Aqua = 'No', ";
            $text = $text . " Aqua = No, ";
        }
        if ($_POST["chkASafron"] == "on") {
            $query = $query . " Safron = 'Yes', ";
            $text = $text . " Safron = Yes, ";
        } else {
            $query = $query . " Safron = 'No', ";
            $text = $text . " Safron = No, ";
        }
        if ($_POST["chkAAmber"] == "on") {
            $query = $query . " Amber = 'Yes', ";
            $text = $text . " Amber = Yes, ";
        } else {
            $query = $query . " Amber = 'No', ";
            $text = $text . " Amber = No, ";
        }
        if ($_POST["chkAGold"] == "on") {
            $query = $query . " Gold = 'Yes', ";
            $text = $text . " Gold = Yes, ";
        } else {
            $query = $query . " Gold = 'No', ";
            $text = $text . " Gold = No, ";
        }
        if ($_POST["chkAVermilion"] == "on") {
            $query = $query . " Vermilion = 'Yes', ";
            $text = $text . " Vermilion = Yes, ";
        } else {
            $query = $query . " Vermilion = 'No', ";
            $text = $text . " Vermilion = No, ";
        }
        if ($_POST["chkASilver"] == "on") {
            $query = $query . " Silver = 'Yes', ";
            $text = $text . " Silver = Yes, ";
        } else {
            $query = $query . " Silver = 'No', ";
            $text = $text . " Silver = No, ";
        }
        if ($_POST["chkAMaroon"] == "on") {
            $query = $query . " Maroon = 'Yes', ";
            $text = $text . " Maroon = Yes, ";
        } else {
            $query = $query . " Maroon = 'No', ";
            $text = $text . " Maroon = No, ";
        }
        if ($_POST["chkAPink"] == "on") {
            $query = $query . " Pink = 'Yes' ";
            $text = $text . " Pink = Yes ";
        } else {
            $query = $query . " Pink = 'No' ";
            $text = $text . " Pink = No ";
        }
        updateIData($iconn, $query, true);
        $message = "Record edited Successfully";
        $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
        updateIData($iconn, $query, true);
    }
} else {
    if ($act == "changeLockDate") {
        $txtLockDate = $_POST["txtLockDate"];
        $query = "UPDATE OtherSettingMaster SET LockDate = " . insertDate($txtLockDate);
        updateIData($iconn, $query, true);
        $text = "Updated Global Settings SET LockDate = " . $txtLockDate;
        $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
        updateIData($iconn, $query, true);
    } else {
        if ($act == "uploadLogo") {
            $query = "SELECT ClientLogo FROM OtherSettingMaster";
            $result = selectData($conn, $query);
            if ($result[0] != "") {
                unlink("img/" . $result[0]);
            }
            $file = basename($_FILES["uploadedfile"]["name"]);
            $query = "UPDATE OtherSettingMaster SET ClientLogo = '" . $file . "'";
            if (updateIData($iconn, $query, true)) {
                $text = "Global Setting: Updated Client Logo = " . $file;
                $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                if (updateIData($iconn, $query, true)) {
                    $target_path = "img/" . basename($_FILES["uploadedfile"]["name"]);
                    if (move_uploaded_file($_FILES["uploadedfile"]["tmp_name"], $target_path)) {
                        $message = "Company Logo Uploaded Succesfully";
                    } else {
                        $message = "Company Logo could NOT be Uploaded";
                    }
                }
            }
        } else {
            if ($act == "deleteLogo") {
                $query = "SELECT ClientLogo FROM OtherSettingMaster";
                $result = selectData($conn, $query);
                if ($result[0] != "") {
                    unlink("img/" . $result[0]);
                }
                $query = "UPDATE OtherSettingMaster SET ClientLogo = '' ";
                if (updateIData($iconn, $query, true)) {
                    $text = "Global Setting: Removed Client Logo = " . $file;
                    $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                    updateIData($iconn, $query, true);
                }
            }
        }
    }
}
if ($prints != "yes") {
    include 'header.php';
    ?>
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-12 d-flex no-block align-items-center">
                <h4 class="page-title">Global Settings</h4>
                <div class="ms-auto text-end">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Global Settings
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <?php
}
//echo "\r\n<html><head><title>Global Settings</title></head>\r\n<script>\r\n\t\r\n</script>\r\n<body><center><div align='center'>\r\n\t";
//$query = "SELECT MinClockinPeriod, TotalDailyClockin, ExitTerminal, Project, FlagLimitType, LessLunchOT, NightShiftMaxOutTime, TotalExitClockin, NoExitException, NoBreakException, RotateShift, RotateShiftNextDay, IDColumnName, RosterColumns, Ex3, Ex1, Ex2, LockDate, MACAddress, EarlyInOTDayDate, SMTPServer, SMTPFrom, SMTPUsername, SMTPPassword, MinOTValue, DBType, DBIP, DBName, DBUser, DBPass, EmployeeCodeLength, PhoneColumnName, LocationSynchShift, ApproveOTIgnoreActual, AutoAssignTerminal, AutoApproveOT, MaxOTValue, RoundOffAOT, MoveNS, UseShiftRoster, SRDay, SRScenario, PreApproveOTValue, MealCouponPrinterName, MealCouponFont , LateDays, AutoResetOT12, ClientLogo, F1, F2, F3, F4, F5, F6, F7, F8, F9, F10, EmployeeEmailField, EmployeeSMSField, SMTPPort, SMTPSSL, DivColumnName, RemarkColumnName, CAGR FROM OtherSettingMaster";
$query = "SELECT MinClockinPeriod, TotalDailyClockin, ExitTerminal, Project, FlagLimitType, LessLunchOT, NightShiftMaxOutTime, TotalExitClockin, NoExitException, NoBreakException, RotateShift, RotateShiftNextDay, IDColumnName, RosterColumns, Ex3, Ex1, Ex2, LockDate, MACAddress, EarlyInOTDayDate, SMTPServer, SMTPFrom, SMTPUsername, SMTPPassword, MinOTValue, DBType, DBIP, DBName, DBUser, DBPass, EmployeeCodeLength, PhoneColumnName, LocationSynchShift, ApproveOTIgnoreActual, AutoAssignTerminal, AutoApproveOT, MaxOTValue, RoundOffAOT, MoveNS, UseShiftRoster, SRDay, SRScenario, PreApproveOTValue, MealCouponPrinterName, MealCouponFont , LateDays, AutoResetOT12, ClientLogo, F1, F2, F3, F4, F5, F6, F7, F8, F9, F10, EmployeeEmailField, EmployeeSMSField, SMTPPort, SMTPSSL, DivColumnName, RemarkColumnName FROM OtherSettingMaster";
$result = selectData($conn, $query);
$txtMinClockinPeriod = $result[0];
$txtTotalDailyClockin = $result[1];
$lstExitTerminal = $result[2];
$lstProject = $result[3];
$lstFlagLimitType = $result[4];
$lstLessLunchOT = $result[5];
$txtNightShiftMaxOutTime = $result[6];
$txtTotalExitClockin = $result[7];
$lstNoExitException = $result[8];
$lstNoBreakException = $result[9];
$lstRotateShift = $result[10];
$txtIDColumnName = $result[12];
$txtRosterColumns = $result[13];
$txtLateReport = $result[14];
$txtBackupPath = $result[15];
$txtVersion = $result[16];
$txtLockDate = displayDate($result[17]);
$txtMACAddress = $result[18];
$lstEarlyInOTDayDate = $result[19];
$txtSMTPServer = $result[20];
$txtSMTPFrom = $result[21];
$txtSMTPUser = $result[22];
$txtSMTPPass = $result[23];
$txtMinOTValue = $result[24];
$lstDBType = $result[25];
$txtDBIP = $result[26];
$txtDBName = $result[27];
$txtDBUser = $result[28];
$txtDBPass = $result[29];
$txtEmployeeCodeLength = $result[30];
$txtPhoneColumnName = $result[31];
$lstLocationSynchShift = $result[32];
$lstApproveOTIgnoreActual = $result[33];
$lstAutoAssignTerminal = $result[34];
$lstAutoApproveOT = $result[35];
$txtMaxOTValue = $result[36];
$lstRoundOffAOT = $result[37];
$lstMoveNS = $result[38];
$lstUseShiftRoster = $result[39];
$lstPreApproveOTValue = $result[42];
$txtMealCouponPrinterName = $result[43];
$txtMealCouponFont = $result[44];
$txtLateDays = $result[45];
$lstAutoResetOT12 = $result[46];
$txhClientLogo = $result[47];
$txtF1 = $result[48];
$txtF2 = $result[49];
$txtF3 = $result[50];
$txtF4 = $result[51];
$txtF5 = $result[52];
$txtF6 = $result[53];
$txtF7 = $result[54];
$txtF8 = $result[55];
$txtF9 = $result[56];
$txtF10 = $result[57];
$lstEmployeeEmailField = $result[58];
$lstEmployeeSMSField = $result[59];
$txtSMTPPort = $result[60];
$lstSMTPSSL = $result[61];
$txtDivColumnName = $result[62];
$txtRemarkColumnName = $result[63];
$lstCAGR = $result[64];
$query = "SELECT TableName, Overwrite, EID, EName, IDNo, Dept, Division, Remark, Shift, Phone, Status, ActiveValue, PassiveValue, DataCOMPayroll, UpdateDate, UpdateSalary, Project, CostCentre, F1, F2, F3, F4, F5, F6, F7, F8, F9  FROM PayrollMap";
$result = selectData($conn, $query);
$txtMapTableName = $result[0];
$lstMapOverwrite = $result[1];
$txtMapEID = $result[2];
$txtMapEName = $result[3];
$txtMapIDNo = $result[4];
$txtMapDept = $result[5];
$txtMapDiv = $result[6];
$txtMapRemark = $result[7];
$txtMapShift = $result[8];
$txtMapPhone = $result[9];
$txtMapStatus = $result[10];
$txtMapActive = $result[11];
$txtMapPassive = $result[12];
$lstMapDataCOMPayroll = $result[13];
$lstMapUpdateDate = $result[14];
$lstMapUpdateSalary = $result[15];
$lstMapProject = $result[16];
$lstMapCostCentre = $result[17];
$txtMapF1 = $result[18];
$txtMapF2 = $result[19];
$txtMapF3 = $result[20];
$txtMapF4 = $result[21];
$txtMapF5 = $result[22];
$txtMapF6 = $result[23];
$txtMapF7 = $result[24];
$txtMapF8 = $result[25];
$txtMapF9 = $result[26];

print "<div class='container-fluid'>";
print "<div class='card'><div class='card-body'>";
print "<form name='frm' method='post' action='OtherSetting.php' enctype='multipart/form-data'>";
print "<div class='row'>";
print "<input type='hidden' name='act' value='editRecord'>";
print "<input type='hidden' name='VirdiLevel' value='" . $_SESSION[$session_variable . "VirdiLevel"] . "'>";
?>
<h4><font face='Verdana' size='1' color='#6481BD'><b><?php echo $message; ?></b></font></h4>
<div class="col-6">
    <?php globaldisplaytextbox("txtMinClockinPeriod", "Minimum Clockin Seconds between 2 Clockins for same Employee <font size='1'>[Ideal = 30 Seconds]</font>", $txtMinClockinPeriod, $prints, 5, "", ""); ?>
</div>
<div class="col-6">
    <?php globaldisplaytextbox("txtNightShiftMaxOutTime", "Maximum OUT Time (HHMM) for Night Shift Staff <br><font size='1'>[Ideal = 1500]</font>", $txtNightShiftMaxOutTime, $prints, 5, "", ""); ?>
</div>
</div>
<div class="row">
    <div class="col-4">
        <?php
        if ($_SESSION[$session_variable . "VirdiLevel"] == "Classic") {
            echo "<div class='mb-3'>
                <label class='form-label'>Use Projects</label>
                <select name='lstProject' class='form-control form-select shadow-none'>
                    <option selected value='" . $lstProject . "'>" . $lstProject . "</option>
                    <option value='Yes'>Yes</option>
                    <option value='No'>No</option>
                </select>
              </div>";
        } else {
            globaldisplaytextbox("lstProject", "Use Projects", "No", "yes", 5, "", "");
        }
        ?>
    </div>
    <div class="col-4">
        <?php
        if ($_SESSION[$session_variable . "VirdiLevel"] == "Classic") {
            echo "<div class='mb-3'>
            <label class='form-label'>Allow NON-CLOCKING on Exit Terminal</label>
            <select name='lstNoExitException' class='form-control form-select shadow-none'>
                    <option selected value = '" . $lstNoExitException . "'>" . $lstNoExitException . "</option> 
                    <option value = 'Yes'>Yes</option> 
                    <option value = 'Yes (Overide Single Clockin)'>Yes (Overide Single Clockin)</option> 
                    <option value = 'No'>No</option>
                </select>
              </div>";
        } else {
            globaldisplaytextbox("lstNoExitException", "Allow NON-CLOCKING on Exit Terminal", "Yes", "yes", 5, "", "");
        }
        ?>
    </div>
    <div class="col-4">
        <?php
        if ($_SESSION[$session_variable . "VirdiLevel"] == "Classic") {
            echo "<div class='mb-3'>
                <label class='form-label'>Auto Assign Terminals<font size='1'>[For Dept wise Clocking Authorization]</font></label>
                <select name='lstAutoAssignTerminal' class='form-control form-select shadow-none'>
                    <option selected value = '" . $lstAutoAssignTerminal . "'>" . $lstAutoAssignTerminal . "</option> 
                    <option value = 'Yes'>Yes</option> 
                    <option value = 'No'>No</option>
                </select>
            </div>";
        } else {
            globaldisplaytextbox("lstAutoAssignTerminal", "Auto Assign Terminals", "Yes", "yes", 5, "", "");
        }
        ?>
    </div>
</div>
<div class="row">
    <div class="col-4">
        <?php
        if ($_SESSION[$session_variable . "VirdiLevel"] == "Classic") {
            echo "<div class='mb-3'>
                    <label class='form-label'>Use Shift Rotation</label>
                    <select name='lstRotateShift' class='form-control form-select shadow-none'>
                        <option selected value = '" . $lstRotateShift . "'>" . $lstRotateShift . "</option> 
                        <option value = 'Yes'>Yes</option> 
                        <option value = 'No'>No</option>
                    </select>
            </div>";
            echo "<div class='mb-3'>
                    <label class='form-label'>Use Access Group Shift Roster</label>
                    <select name='lstCAGR' class='form-control form-select shadow-none'>
                        <option selected value = '" . $lstCAGR . "'>" . $lstCAGR . "</option> 
                        <option value = 'Yes'>Yes</option> 
                        <option value = 'No'>No</option>
                    </select>
            </div>";
        } else {
            globaldisplaytextbox("lstRotateShift", "Use Shift Rotation", "No", "yes", 5, "", "");
            globaldisplaytextbox("lstCAGR", "Use Access Group Shift Rotation", "No", "yes", 5, "", "");
        }
        ?>
    </div>
    <div class="col-4">
        <?php
        if ($_SESSION[$session_variable . "VirdiLevel"] == "Classic") {
            echo "<div class='mb-3'>
                    <label class='form-label'>Use Shift Roster</label>
                    <select name='lstUseShiftRoster' class='form-control form-select shadow-none'>
                        <option selected value = '" . $lstUseShiftRoster . "'>" . $lstUseShiftRoster . "</option> 
                        <option value = 'Yes'>Yes</option> 
                        <option value = 'No'>No</option>
                    </select>
            </div>";
            echo "<div class='mb-3'>
                    <label class='form-label'>Auto Reset OT1/OT2 to Sat/Sun <font size='1'>[10 Day Period]</font></label>
                    <select name='lstAutoResetOT12' class='form-control form-select shadow-none'>
                        <option selected value = '" . $lstAutoResetOT12 . "'>" . $lstAutoResetOT12 . "</option> 
                        <option value = 'Yes'>Yes</option> 
                        <option value = 'No'>No</option>
                    </select>
            </div>";
        } else {
            globaldisplaytextbox("lstUseShiftRoster", "Use Shift Roster", "No", "yes", 5, "", "");
            globaldisplaytextbox("lstAutoResetOT12", "Automatically Reset OT1/OT2 to Sat/Sun [10 Day Period]", "No", "yes", 5, "", "");
        }
        ?>
    </div>
    <div class="col-4">
        <?php
        echo "<div class='mb-3'>
                <label class='form-label'><b>Auto Approve</b> Overtime</label>
                    <select name='lstAutoApproveOT' class='form-control form-select shadow-none'>
                        <option selected value = '" . $lstAutoApproveOT . "'>" . $lstAutoApproveOT . "</option> 
                        <option value = 'Yes'>Yes</option> 
                        <option value = 'No'>No</option>
                    </select>
            </div>";
        ?>
    </div>
</div>
<div class="row">
    <div class="col-4">
        <?php
        echo "<div class='mb-3'>
                <label class='form-label'>Round Off Approved OT to Lower Minute of</label>
                    <select name='lstRoundOffAOT' class='form-control form-select shadow-none'>
                        <option selected value = '" . $lstRoundOffAOT . "'>" . $lstRoundOffAOT . "</option> 
                        <option value = 'None'>None</option> 
                        <option value = '15'>15</option> 
                        <option value = '30'>30</option> 
                        <option value = '60'>60</option>
                    </select>
            </div>";
        ?>
    </div>
    <div class="col-4">
        <?php
        if ($_SESSION[$session_variable . "VirdiLevel"] == "Classic") {
            echo "<div class='mb-3'>
                <label class='form-label'><b>Ignore</b> Actual OT Value while Editing Overtime Approvals</font></label>
                    <select name='lstApproveOTIgnoreActual' class='form-control form-select shadow-none'>
                        <option selected value = '" . $lstApproveOTIgnoreActual . "'>" . $lstApproveOTIgnoreActual . "</option> 
                        <option value = 'Yes'>Yes</option> 
                        <option value = 'No'>No</option>
                    </select> 
                    <font face='Verdana' size='1'>[Allows Authorized Users to <b>INCREASE</b> Approved Value More Than Actual OT]</font>
            </div>";
//            print "<td align='right'><font face='Verdana' size='2'><b>Ignore</b> Actual OT Value while Editing Overtime Approvals</font></td><td><select name='lstApproveOTIgnoreActual' class='form-control form-select shadow-none'><option selected value = '" . $lstApproveOTIgnoreActual . "'>" . $lstApproveOTIgnoreActual . "</option> <option value = 'Yes'>Yes</option> <option value = 'No'>No</option></select> <font face='Verdana' size='1'>[Allows Authorized Users to <b>INCREASE</b> Approved Value More Than Actual OT]</font></td></tr>";
        } else {
            globaldisplaytextbox("lstApproveOTIgnoreActual", "<b>Ignore</b> Actual OT Value while Editing Overtime Approvals", "No", "yes", 5, "", "");
        }
        ?>
    </div>
    <div class="col-4">
        <?php
        if ($_SESSION[$session_variable . "VirdiLevel"] == "Classic") {
            echo "<div class='mb-3'>
                <label class='form-label'>Overtime Value to be Approved from Pre-Approval Module</label>
                    <select name='lstPreApproveOTValue' class='form-control form-select shadow-none'>
                        <option selected value = '" . $lstPreApproveOTValue . "'>" . $lstPreApproveOTValue . "</option> 
                        <option value = 'Lower Value'>Lower Value</option> 
                        <option value = 'Pre-Approved'>Pre-Approved</option> 
                    </select>  
                    <font face='Verdana' size='1'>[Between Actual and Pre-Approved OT]</font>
            </div>";
        } else {
            globaldisplaytextbox("lstPreApproveOTValue", "Overtime Value to be Approved from Pre-Approval Module", "Lower Value", "yes", 5, "", "");
        }
        ?>
    </div>
</div>
<div class="row">
    <div class="col-3">
        <?php
        //[Max Length: <b>9</b>]
        globaldisplaytextbox("txtDivColumnName", "Rename Div/Desg Column to <font color='#FF0000' size='1'></font>", $txtDivColumnName, $prints, 100, "", "");
        ?>
    </div>
    <div class="col-3">
        <?php
        //[Max Length: <b>9</b>]
        globaldisplaytextbox("txtRemarkColumnName", "Rename Remark Column to <font color='#FF0000' size='1'></font>", $txtRemarkColumnName, $prints, 100, "", "");
        ?>
    </div>
    <div class="col-3">
        <?php
        //[Max Length: <b>9</b>]
        globaldisplaytextbox("txtIDColumnName", "Rename Social No Column to <font color='#FF0000' size='1'></font></font>", $txtIDColumnName, $prints, 100, "", "");
        ?>
    </div>
    <div class="col-3">
<?php
//[Max Length: <b>9</b>]
globaldisplaytextbox("txtPhoneColumnName", "Rename Phone Column to <font color='#FF0000' size='1'></font>", $txtPhoneColumnName, $prints, 100, "", "");
?>
    </div>
</div>
<div class="row">
    <div class="col-6">
        <?php
        globaldisplaytextbox("txtEmployeeCodeLength", "Employee Code Length", $txtEmployeeCodeLength, $prints, 6, "", "");
        ?>
    </div>
    <div class="col-6">
<?php
globaldisplaytextbox("txtLateDays", "Snapshot Report: No of Late Days to be Treated as <b>ONE</b> Absent", $txtLateDays, $prints, 6, "", "");
?>
    </div>
</div>
<div class="row">
    <div class="col-6">
        <?php
        globaldisplaytextbox("txtBackupPath", "Database Backup Path <font size='1'>[Remove Trailing Backslash '<b>\\</b>']</font>", $txtBackupPath, $prints, 75, "50%", "50%");
        ?>
    </div>
    <div class="col-6">
<?php
globaldisplaytextbox("txtMealCouponPrinterName", "MySQL BIN Folder Path <font size='1'>[Remove Trailing Backslash '<b>\\</b>']</font>", $txtMealCouponPrinterName, $prints, 75, "", "");
?>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <?php
        if ($_SESSION[$session_variable . "VirdiLevel"] == "Classic") {
            echo "<div class='mb-3'>
                <label class='form-label'>During Offline Synch, assign Employee Shift from</label>
                    <select name='lstLocationSynchShift' class='form-control form-select shadow-none'>
                        <option selected value = '" . $lstLocationSynchShift . "'>" . $lstLocationSynchShift . "</option> 
                        <option value = 'Server'>Server</option> 
                        <option value = 'Location'>Location</option>
                    </select>
            </div>";
            echo "<div class='mb-3'>
                <label class='form-label'>Employee Email Field</label>
                    <select name='lstEmployeeEmailField' class='form-control form-select shadow-none'>
                        <option selected value = '" . $lstEmployeeEmailField . "'>" . $lstEmployeeEmailField . "</option>";
            for ($i = 1; $i < 11; $i++) {
                echo "<option value = 'F" . $i . "'>F" . $i . "</option>";
            }
            echo "</select>
            </div>";
            echo "<div class='mb-3'>
                <label class='form-label'>Employee SMS Field</label>
                    <select name='lstEmployeeSMSField' class='form-control form-select shadow-none'>
                        <option selected value = '" . $lstEmployeeSMSField . "'>" . $lstEmployeeSMSField . "</option>";
            for ($i = 1; $i < 11; $i++) {
                echo "<option value = 'F" . $i . "'>F" . $i . "</option>";
            }
            echo "</select>
            </div>";
        } else {
            globaldisplaytextbox("txtMealCouponFont", "During Offline Synch, assign Employee Shift from", "Server", "yes", 5, "", "");
        }
        ?>
    </div>
</div>
</div>
</div>
<?php if (strpos($userlevel, $current_module . "E") !== false && $_SESSION[$session_variable . "VirdiLevel"] == "Classic") { ?>
    <div class="card">
        <div class="card-body">
            <div class="row">    
                <h4>Payroll Database <b>[DB]</b> / Mapping [Column Names to be Mapped With] Details </h4>
                <div class="col-4">
                    <?php
                    echo "<div class='mb-3'>
                <label class='form-label'>DB Type</label>
                    <select name='lstDBType' class='form-control form-select shadow-none'>
                        <option selected value = '" . $lstDBType . "'>" . $lstDBType . "</option> 
                        <option value = 'ODBC'>ODBC</option> 
                        <option value = 'MySQL'>MySQL</option> 
                        <option value = 'MSSQL'>MSSQL</option> 
                        <option value = 'Oracle'>Oracle</option> 
                        <option value = 'None'>None</option>
                    </select>
            </div>";
                    ?>
                </div>
                <div class="col-4">
                    <?php
                    globaldisplaytextbox("txtDBIP", "DB IP", $txtDBIP, $prints, 40, "", "");
                    ?>
                </div>
                <div class="col-4">
    <?php
    globaldisplaytextbox("txtDBName", "DB Name/ SID", $txtDBName, $prints, 40, "", "");
    ?>
                </div>
            </div>
            <div class="row">    
                <div class="col-4">
                    <?php
                    globaldisplaytextbox("txtDBUser", "DB Username", $txtDBUser, $prints, 15, "", "");
                    ?>
                </div>
                <div class="col-4">
                    <?php
                    displayTextboxs("txtDBPass", "DB Password", $txtDBPass, $prints, 15, "", "", "type='password'");
                    ?>
                </div>
                <div class="col-4">
    <?php
    displayTextboxs("txtDBPassRepeat", "Password Repeat", $txtDBPass, $prints, 15, "", "", "type='password'");
    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <?php
                    echo "<div class='mb-3'>
                        <label class='form-label'>While doing Payroll Synchronization, for common Employees Overwrite Data/ DeActivate Extra Employees, of:</label>
                        <select name='lstMapOverwrite' class='form-control-inner form-select shadow-none'>
                            <option selected value = '" . $lstMapOverwrite . "'>" . $lstMapOverwrite . "</option> 
                            <option value = 'Payroll DB'>Payroll DB</option> 
                            <option value = 'TA Application'>TA Application</option> 
                            <option value = 'No Synchronization'>No Synchronization</option>
                        </select>
                    </div>";
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <?php
                    echo "<div class='mb-3'>
                        <label class='form-label'>Use Payroll</label>
                        <select name='lstMapDataCOMPayroll' onChange='javascript:checkDataCOMPayroll()' class='form-control form-select shadow-none'>
                            <option selected value = '" . $lstMapDataCOMPayroll . "'>" . $lstMapDataCOMPayroll . "</option> 
                            <option value = 'PayMaster'>PayMaster</option> <option value = 'WebPayMaster'>WebPayMaster</option> 
                            <option value = 'DataCOM'>DataCOM</option> 
                            <option value = 'No'>No</option>
                        </select>
                    </div>";
                    ?>
                </div>
                <div class="col-4">
                    <?php
                    echo "<div class='mb-3'>
                        <label class='form-label'>Update Dates</label>
                        <select name='lstMapUpdateDate' class='form-control form-select shadow-none'>
                            <option selected value = '" . $lstMapUpdateDate . "'>" . $lstMapUpdateDate . "</option> 
                            <option value = 'Yes'>Yes</option> 
                            <option value = 'No'>No</option>
                        </select>
                    </div>";
                    ?>
                </div>
                <div class="col-4">
                    <?php
                    echo "<div class='mb-3'>
                        <label class='form-label'>Update Salaries</label>
                        <select name='lstMapUpdateSalary' class='form-control form-select shadow-none'>
                            <option selected value = '" . $lstMapUpdateSalary . "'>" . $lstMapUpdateSalary . "</option> 
                            <option value = 'Yes'>Yes</option>
                            <option value = 'No'>No</option>
                        </select>
                    </div>";
                    ?>
                </div>
            </div>
            <div class="row">
                <h5>[TA = Time and Attendance Application]</h5>
                <div class="col-6">
                    <?php
                    echo "<div class='mb-3'>
                        <label class='form-label'>Project/ Cadre</label>
                        <select name='lstMapProject' class='form-control form-select shadow-none'>";
                    if ($lstMapProject == "dept") {
                        print "<option selected value = 'dept'>Dept [TA]</option>";
                    } else {
                        if ($lstMapProject == "company") {
                            print "<option selected value = 'company'>Div/Desg [TA]</option>";
                        } else {
                            if ($lstMapProject == "idno") {
                                print "<option selected value = 'idno'>Social No [TA]</option>";
                            } else {
                                if ($lstMapProject == "remark") {
                                    print "<option selected value = 'remark'>Remark [TA]</option>";
                                } else {
                                    if ($lstMapProject == "phone") {
                                        print "<option selected value = 'phone'>Phone [TA]</option>";
                                    } else {
                                        print "<option selected value = '" . $lstMapProject . "'>" . $lstMapProject . "</option>";
                                    }
                                }
                            }
                        }
                    }
                    echo "<option value = 'dept'>Dept [TA]</option> 
                                <option value = 'company'>Div/Desg [TA]</option> 
                                <option value = 'idno'>Social No [TA]</option> 
                                <option value = 'remark'>Remark [TA]</option> 
                                <option value = 'phone'>Phone [TA]</option> 
                                <option value = ''>---</option>
                        </select>
                    </div>";
                    ?>
                </div>
                <div class="col-6">
                    <?php
                    echo "<div class='mb-3'>
                        <label class='form-label'>Cost Centre</label>
                        <select name='lstMapCostCentre' class='form-control form-select shadow-none'>
                            <option selected value = '" . $lstMapCostCentre . "'>" . $lstMapCostCentre . "</option> 
                            <option value = 'dept'>Dept [TA]</option> 
                            <option value = 'Company'>Div/Desg [TA]</option> 
                            <option value = 'idno'>Social No [TA]</option> 
                            <option value = 'Remark'>Remark [TA]</option> 
                            <option value = 'Phone'>Phone [TA]</option> 
                            <option value = ''>---</option>
                        </select>
                    </div>";
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <?php
                    globaldisplaytextbox("txtMapTableName", "Table Name", $txtMapTableName, $prints, 20, "", "");
                    ?>    
                </div>
                <div class="col-4">
                    <?php
                    globaldisplaytextbox("txtMapEID", "Employee ID", $txtMapEID, $prints, 20, "", "");
                    ?>    
                </div>
                <div class="col-4">
    <?php
    globaldisplaytextbox("txtMapEName", "Name", $txtMapEName, $prints, 20, "", "");
    ?>    
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <?php
                    globaldisplaytextbox("txtMapIDNo", "Social No", $txtMapIDNo, $prints, 20, "", "");
                    ?>    
                </div>
                <div class="col-4">
                    <?php
                    globaldisplaytextbox("txtMapDept", "Dept", $txtMapDept, $prints, 20, "", "");
                    ?>    
                </div>
                <div class="col-4">
    <?php
    globaldisplaytextbox("txtMapDiv", "Div/Desg", $txtMapDiv, $prints, 20, "", "");
    ?>    
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <?php
                    globaldisplaytextbox("txtMapRemark", "Remark", $txtMapRemark, $prints, 20, "", "");
                    ?>    
                </div>
                <div class="col-4">
                    <?php
                    globaldisplaytextbox("txtMapShift", "Shift", $txtMapShift, $prints, 20, "", "");
                    ?>    
                </div>
                <div class="col-4">
    <?php
    globaldisplaytextbox("txtMapPhone", "Phone", $txtMapPhone, $prints, 20, "", "");
    ?>    
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <?php
                    globaldisplaytextbox("txtMapStatus", "Active/Passive", $txtMapStatus, $prints, 20, "", "");
                    ?>    
                </div>
                <div class="col-4">
                    <?php
                    globaldisplaytextbox("txtMapActive", "Active Value", $txtMapActive, $prints, 20, "", "");
                    ?>    
                </div>
                <div class="col-4">
    <?php
    globaldisplaytextbox("txtMapPassive", "Passive Value", $txtMapPassive, $prints, 20, "", "");
    ?>    
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <?php
                    globaldisplaytextbox("txtMapF1", "F1", $txtMapF1, $prints, 20, "", "");
                    ?>    
                </div>
                <div class="col-4">
                    <?php
                    globaldisplaytextbox("txtMapF2", "F2", $txtMapF2, $prints, 20, "", "");
                    ?>    
                </div>
                <div class="col-4">
    <?php
    globaldisplaytextbox("txtMapF3", "F3", $txtMapF3, $prints, 20, "", "");
    ?>    
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <?php
                    globaldisplaytextbox("txtMapF4", "F4", $txtMapF4, $prints, 20, "", "");
                    ?>    
                </div>
                <div class="col-4">
                    <?php
                    globaldisplaytextbox("txtMapF5", "F5", $txtMapF5, $prints, 20, "", "");
                    ?>    
                </div>
                <div class="col-4">
    <?php
    globaldisplaytextbox("txtMapF6", "F6", $txtMapF6, $prints, 20, "", "");
    ?>    
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <?php
                    globaldisplaytextbox("txtMapF7", "F7", $txtMapF7, $prints, 20, "", "");
                    ?>    
                </div>
                <div class="col-4">
                    <?php
                    globaldisplaytextbox("txtMapF8", "F8", $txtMapF8, $prints, 20, "", "");
                    ?>    
                </div>
                <div class="col-4">
    <?php
    globaldisplaytextbox("txtMapF9", "F9", $txtMapF9, $prints, 20, "", "");
    ?>    
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<div class="card">
    <div class="card-body">
        <div class="row">
            <h4>SMTP Server Details</h4>
            <div class="col-6">
                <?php
                globaldisplaytextbox("txtSMTPServer", "SMTP Server", $txtSMTPServer, $prints, 255, "", "");
                ?>
            </div>
            <div class="col-6">
<?php
globaldisplaytextbox("txtSMTPFrom", "FROM Email", $txtSMTPFrom, $prints, 255, "", "");
?>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <?php
                globaldisplaytextbox("txtSMTPServerAUTH", "SMTP AUTH", "YES", "yes", 15, "", "");
                ?> 
            </div>
            <div class="col-6">
<?php
globaldisplaytextbox("txtSMTPUser", "SMTP Username", $txtSMTPUser, $prints, 255, "", "");
?> 
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <?php
                displayTextboxs("txtSMTPPass", "SMTP Password", $txtSMTPPass, $prints, 25, "", "", "type='password'");
                ?> 
            </div>
            <div class="col-6">
<?php
displayTextboxs("txtSMTPPassRepeat", "Password Repeat", $txtSMTPPass, $prints, 25, "", "", "type='password'");
?> 
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <?php
                globaldisplaytextbox("txtSMTPPort", "SMTP Port", $txtSMTPPort, $prints, 5, "", "");
                ?> 
            </div>
            <div class="col-6">
                <?php
                echo "<div class='mb-3'>
                        <label class='form-label'>SSL</label>
                        <select name='lstSMTPSSL' class='form-control form-select shadow-none'>
                            <option selected value = '" . $lstSMTPSSL . "'>" . $lstSMTPSSL . "</option> 
                            <option value = 'None'>None</option> 
                            <option value = 'SSL'>SSL</option> 
                            <option value = 'TLS'>TLS</option>
                        </select>
                    </div>";
                ?> 
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <?php
                if ($_SESSION[$session_variable . "VirdiLevel"] == "Classic") {
                    echo "<div class='mb-3'>
                        <label class='form-label'>Employee Flag Limit Start Range</label>
                        <select name='lstFlagLimitType' class='form-control form-select shadow-none'>
                            <option selected value = '" . $lstFlagLimitType . "'>" . $lstFlagLimitType . "</option> 
                            <option value = 'Jan 01'>Jan 01</option> 
                            <option value = 'Employee Start Date'>Employee Start Date</option>
                        </select>
                    </div>";
                } else {
                    globaldisplaytextbox("lstFlagLimitType", "Employee Flag Limit Start Range", "Jan 01", "yes", 25, "", "");
                }
                ?>
            </div>
        </div>
    </div>
</div>
<?php if ($_SESSION[$session_variable . "VirdiLevel"] == "Classic") { ?>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <h4>Include Selected Flags for the <b>TOTAL</b> Days Column Calculation for Snapshot Report</h4>
                <div class="col-12">
                    <?php
                    echo "<table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'><tr>";
                    $query = "SELECT * FROM TLSFLag";
                    $result = selectData($conn, $query);
                    for ($i = 1; $i < count($result); $i++) {
                        if ($i != 11 && $i != 12) {
                            print "<td vAlign='top'><font face='Verdana' size='1'>";
                        }
                        if ($i == 1) {
                            if ($result[$i] == "Yes") {
                                print "<input type='checkbox' name='chkViolet' checked>";
                            } else {
                                print "<input type='checkbox' name='chkViolet'>";
                            }
                            print "&nbsp;&nbsp;Violet<br>";
                        } else {
                            if ($i == 2) {
                                if ($result[$i] == "Yes") {
                                    print "<input type='checkbox' name='chkIndigo' checked>";
                                } else {
                                    print "<input type='checkbox' name='chkIndigo'>";
                                }
                                print "&nbsp;&nbsp;Indigo<br>";
                            } else {
                                if ($i == 3) {
                                    if ($result[$i] == "Yes") {
                                        print "<input type='checkbox' name='chkBlue' checked>";
                                    } else {
                                        print "<input type='checkbox' name='chkBlue'>";
                                    }
                                    print "&nbsp;&nbsp;Blue<br>";
                                } else {
                                    if ($i == 4) {
                                        if ($result[$i] == "Yes") {
                                            print "<input type='checkbox' name='chkGreen' checked>";
                                        } else {
                                            print "<input type='checkbox' name='chkGreen'>";
                                        }
                                        print "&nbsp;&nbsp;Green<br>";
                                    } else {
                                        if ($i == 5) {
                                            if ($result[$i] == "Yes") {
                                                print "<input type='checkbox' name='chkYellow' checked>";
                                            } else {
                                                print "<input type='checkbox' name='chkYellow'>";
                                            }
                                            print "&nbsp;&nbsp;Yellow<br>";
                                        } else {
                                            if ($i == 6) {
                                                if ($result[$i] == "Yes") {
                                                    print "<input type='checkbox' name='chkOrange' checked>";
                                                } else {
                                                    print "<input type='checkbox' name='chkOrange'>";
                                                }
                                                print "&nbsp;&nbsp;Orange<br>";
                                            } else {
                                                if ($i == 7) {
                                                    if ($result[$i] == "Yes") {
                                                        print "<input type='checkbox' name='chkRed' checked>";
                                                    } else {
                                                        print "<input type='checkbox' name='chkRed'>";
                                                    }
                                                    print "&nbsp;&nbsp;Red<br>";
                                                } else {
                                                    if ($i == 8) {
                                                        if ($result[$i] == "Yes") {
                                                            print "<input type='checkbox' name='chkGray' checked>";
                                                        } else {
                                                            print "<input type='checkbox' name='chkGray'>";
                                                        }
                                                        print "&nbsp;&nbsp;Gray<br>";
                                                    } else {
                                                        if ($i == 9) {
                                                            if ($result[$i] == "Yes") {
                                                                print "<input type='checkbox' name='chkBrown' checked>";
                                                            } else {
                                                                print "<input type='checkbox' name='chkBrown'>";
                                                            }
                                                            print "&nbsp;&nbsp;Brown<br>";
                                                        } else {
                                                            if ($i == 10) {
                                                                if ($result[$i] == "Yes") {
                                                                    print "<input type='checkbox' name='chkPurple' checked>";
                                                                } else {
                                                                    print "<input type='checkbox' name='chkPurple'>";
                                                                }
                                                                print "&nbsp;&nbsp;Purple<br>";
                                                            } else {
                                                                if ($i == 13) {
                                                                    if ($result[$i] == "Yes") {
                                                                        print "<input type='checkbox' name='chkMagenta' checked>";
                                                                    } else {
                                                                        print "<input type='checkbox' name='chkMagenta'>";
                                                                    }
                                                                    print "&nbsp;&nbsp;Magenta<br>";
                                                                } else {
                                                                    if ($i == 14) {
                                                                        if ($result[$i] == "Yes") {
                                                                            print "<input type='checkbox' name='chkTeal' checked>";
                                                                        } else {
                                                                            print "<input type='checkbox' name='chkTeal'>";
                                                                        }
                                                                        print "&nbsp;&nbsp;Teal<br>";
                                                                    } else {
                                                                        if ($i == 15) {
                                                                            if ($result[$i] == "Yes") {
                                                                                print "<input type='checkbox' name='chkAqua' checked>";
                                                                            } else {
                                                                                print "<input type='checkbox' name='chkAqua'>";
                                                                            }
                                                                            print "&nbsp;&nbsp;Aqua<br>";
                                                                        } else {
                                                                            if ($i == 16) {
                                                                                if ($result[$i] == "Yes") {
                                                                                    print "<input type='checkbox' name='chkSafron' checked>";
                                                                                } else {
                                                                                    print "<input type='checkbox' name='chkSafron'>";
                                                                                }
                                                                                print "&nbsp;&nbsp;Safron<br>";
                                                                            } else {
                                                                                if ($i == 17) {
                                                                                    if ($result[$i] == "Yes") {
                                                                                        print "<input type='checkbox' name='chkAmber' checked>";
                                                                                    } else {
                                                                                        print "<input type='checkbox' name='chkAmber'>";
                                                                                    }
                                                                                    print "&nbsp;&nbsp;Amber<br>";
                                                                                } else {
                                                                                    if ($i == 18) {
                                                                                        if ($result[$i] == "Yes") {
                                                                                            print "<input type='checkbox' name='chkGold' checked>";
                                                                                        } else {
                                                                                            print "<input type='checkbox' name='chkGold'>";
                                                                                        }
                                                                                        print "&nbsp;&nbsp;Gold<br>";
                                                                                    } else {
                                                                                        if ($i == 19) {
                                                                                            if ($result[$i] == "Yes") {
                                                                                                print "<input type='checkbox' name='chkVermilion' checked>";
                                                                                            } else {
                                                                                                print "<input type='checkbox' name='chkVermilion'>";
                                                                                            }
                                                                                            print "&nbsp;&nbsp;Vermilion<br>";
                                                                                        } else {
                                                                                            if ($i == 20) {
                                                                                                if ($result[$i] == "Yes") {
                                                                                                    print "<input type='checkbox' name='chkSilver' checked>";
                                                                                                } else {
                                                                                                    print "<input type='checkbox' name='chkSilver'>";
                                                                                                }
                                                                                                print "&nbsp;&nbsp;Silver<br>";
                                                                                            } else {
                                                                                                if ($i == 21) {
                                                                                                    if ($result[$i] == "Yes") {
                                                                                                        print "<input type='checkbox' name='chkMaroon' checked>";
                                                                                                    } else {
                                                                                                        print "<input type='checkbox' name='chkMaroon'>";
                                                                                                    }
                                                                                                    print "&nbsp;&nbsp;Maroon<br>";
                                                                                                } else {
                                                                                                    if ($i == 22) {
                                                                                                        if ($result[$i] == "Yes") {
                                                                                                            print "<input type='checkbox' name='chkPink' checked>";
                                                                                                        } else {
                                                                                                            print "<input type='checkbox' name='chkPink'>";
                                                                                                        }
                                                                                                        print "&nbsp;&nbsp;Pink<br>";
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
                        if ($i != 11 && $i != 12) {
                            print "</font></td>";
                        }
                        if ($i == 10) {
                            print "</tr><tr>";
                        }
                    }
                    echo "</tr></table>"
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <h4>Allow Access for Employees on Days with Selected Flags</h4>
            <div class="row">
                <div class="col-12">
                    <?php
                    print "<table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'><tr>";
                    $query = "SELECT * FROM AccessFlag";
                    $result = selectData($conn, $query);
                    if (is_array($result) && count($result) > 0) {
                        for ($i = 1; $i < count($result); $i++) {
                            if ($i != 11 && $i != 12) {
                                print "<td vAlign='top'><font face='Verdana' size='1'>";
                            }
                            if ($i == 1) {
                                if ($result[$i] == "Yes") {
                                    print "<input type='checkbox' name='chkAViolet' checked>";
                                } else {
                                    print "<input type='checkbox' name='chkAViolet'>";
                                }
                                print "&nbsp;&nbsp;Violet<br>";
                            } else {
                                if ($i == 2) {
                                    if ($result[$i] == "Yes") {
                                        print "<input type='checkbox' name='chkAIndigo' checked>";
                                    } else {
                                        print "<input type='checkbox' name='chkAIndigo'>";
                                    }
                                    print "&nbsp;&nbsp;Indigo<br>";
                                } else {
                                    if ($i == 3) {
                                        if ($result[$i] == "Yes") {
                                            print "<input type='checkbox' name='chkABlue' checked>";
                                        } else {
                                            print "<input type='checkbox' name='chkABlue'>";
                                        }
                                        print "&nbsp;&nbsp;Blue<br>";
                                    } else {
                                        if ($i == 4) {
                                            if ($result[$i] == "Yes") {
                                                print "<input type='checkbox' name='chkAGreen' checked>";
                                            } else {
                                                print "<input type='checkbox' name='chkAGreen'>";
                                            }
                                            print "&nbsp;&nbsp;Green<br>";
                                        } else {
                                            if ($i == 5) {
                                                if ($result[$i] == "Yes") {
                                                    print "<input type='checkbox' name='chkAYellow' checked>";
                                                } else {
                                                    print "<input type='checkbox' name='chkAYellow'>";
                                                }
                                                print "&nbsp;&nbsp;Yellow<br>";
                                            } else {
                                                if ($i == 6) {
                                                    if ($result[$i] == "Yes") {
                                                        print "<input type='checkbox' name='chkAOrange' checked>";
                                                    } else {
                                                        print "<input type='checkbox' name='chkAOrange'>";
                                                    }
                                                    print "&nbsp;&nbsp;Orange<br>";
                                                } else {
                                                    if ($i == 7) {
                                                        if ($result[$i] == "Yes") {
                                                            print "<input type='checkbox' name='chkARed' checked>";
                                                        } else {
                                                            print "<input type='checkbox' name='chkARed'>";
                                                        }
                                                        print "&nbsp;&nbsp;Red<br>";
                                                    } else {
                                                        if ($i == 8) {
                                                            if ($result[$i] == "Yes") {
                                                                print "<input type='checkbox' name='chkAGray' checked>";
                                                            } else {
                                                                print "<input type='checkbox' name='chkAGray'>";
                                                            }
                                                            print "&nbsp;&nbsp;Gray<br>";
                                                        } else {
                                                            if ($i == 9) {
                                                                if ($result[$i] == "Yes") {
                                                                    print "<input type='checkbox' name='chkABrown' checked>";
                                                                } else {
                                                                    print "<input type='checkbox' name='chkABrown'>";
                                                                }
                                                                print "&nbsp;&nbsp;Brown<br>";
                                                            } else {
                                                                if ($i == 10) {
                                                                    if ($result[$i] == "Yes") {
                                                                        print "<input type='checkbox' name='chkAPurple' checked>";
                                                                    } else {
                                                                        print "<input type='checkbox' name='chkAPurple'>";
                                                                    }
                                                                    print "&nbsp;&nbsp;Purple<br>";
                                                                } else {
                                                                    if ($i == 13) {
                                                                        if ($result[$i] == "Yes") {
                                                                            print "<input type='checkbox' name='chkAMagenta' checked>";
                                                                        } else {
                                                                            print "<input type='checkbox' name='chkAMagenta'>";
                                                                        }
                                                                        print "&nbsp;&nbsp;Magenta<br>";
                                                                    } else {
                                                                        if ($i == 14) {
                                                                            if ($result[$i] == "Yes") {
                                                                                print "<input type='checkbox' name='chkATeal' checked>";
                                                                            } else {
                                                                                print "<input type='checkbox' name='chkATeal'>";
                                                                            }
                                                                            print "&nbsp;&nbsp;Teal<br>";
                                                                        } else {
                                                                            if ($i == 15) {
                                                                                if ($result[$i] == "Yes") {
                                                                                    print "<input type='checkbox' name='chkAAqua' checked>";
                                                                                } else {
                                                                                    print "<input type='checkbox' name='chkAAqua'>";
                                                                                }
                                                                                print "&nbsp;&nbsp;Aqua<br>";
                                                                            } else {
                                                                                if ($i == 16) {
                                                                                    if ($result[$i] == "Yes") {
                                                                                        print "<input type='checkbox' name='chkASafron' checked>";
                                                                                    } else {
                                                                                        print "<input type='checkbox' name='chkASafron'>";
                                                                                    }
                                                                                    print "&nbsp;&nbsp;Safron<br>";
                                                                                } else {
                                                                                    if ($i == 17) {
                                                                                        if ($result[$i] == "Yes") {
                                                                                            print "<input type='checkbox' name='chkAAmber' checked>";
                                                                                        } else {
                                                                                            print "<input type='checkbox' name='chkAAmber'>";
                                                                                        }
                                                                                        print "&nbsp;&nbsp;Amber<br>";
                                                                                    } else {
                                                                                        if ($i == 18) {
                                                                                            if ($result[$i] == "Yes") {
                                                                                                print "<input type='checkbox' name='chkAGold' checked>";
                                                                                            } else {
                                                                                                print "<input type='checkbox' name='chkAGold'>";
                                                                                            }
                                                                                            print "&nbsp;&nbsp;Gold<br>";
                                                                                        } else {
                                                                                            if ($i == 19) {
                                                                                                if ($result[$i] == "Yes") {
                                                                                                    print "<input type='checkbox' name='chkAVermilion' checked>";
                                                                                                } else {
                                                                                                    print "<input type='checkbox' name='chkAVermilion'>";
                                                                                                }
                                                                                                print "&nbsp;&nbsp;Vermilion<br>";
                                                                                            } else {
                                                                                                if ($i == 20) {
                                                                                                    if ($result[$i] == "Yes") {
                                                                                                        print "<input type='checkbox' name='chkASilver' checked>";
                                                                                                    } else {
                                                                                                        print "<input type='checkbox' name='chkASilver'>";
                                                                                                    }
                                                                                                    print "&nbsp;&nbsp;Silver<br>";
                                                                                                } else {
                                                                                                    if ($i == 21) {
                                                                                                        if ($result[$i] == "Yes") {
                                                                                                            print "<input type='checkbox' name='chkAMaroon' checked>";
                                                                                                        } else {
                                                                                                            print "<input type='checkbox' name='chkAMaroon'>";
                                                                                                        }
                                                                                                        print "&nbsp;&nbsp;Maroon<br>";
                                                                                                    } else {
                                                                                                        if ($i == 22) {
                                                                                                            if ($result[$i] == "Yes") {
                                                                                                                print "<input type='checkbox' name='chkAPink' checked>";
                                                                                                            } else {
                                                                                                                print "<input type='checkbox' name='chkAPink'>";
                                                                                                            }
                                                                                                            print "&nbsp;&nbsp;Pink<br>";
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
                            if ($i != 11 && $i != 12) {
                                print "</font></td>";
                            }
                            if ($i == 10) {
                                print "</tr><tr>";
                            }
                        }
                    }
                    print "</tr></table>";
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <h4>Flag Titles</h4>
                <div class="col-12">
                    <?php
                    print "<table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
                    $query = "SELECT Title FROM FlagTitle WHERE Flag = 'Pink'";
                    $result = selectData($conn, $query);
                    $pink_title = $result[0];
                    $purple_title = "";
                    $query = "SELECT Flag, Title, FlagLink FROM FlagTitle";
                    $result = mysqli_query($conn, $query);
                    for ($flag_c = 0; $cur = mysqli_fetch_row($result); $flag_c++) {
                        if ($flag_c == 9) {
                            print "<td><font face='Verdana' size='1' color='Pink'><b>Pink</b><br><input size='10' name='txtFlagTitlePink' value='" . $pink_title . "' class='form-control'>";
                            $purple_title = $cur[1];
                        } else {
                            if ($flag_c == 19) {
                                print "<td><font face='Verdana' size='1' color='Purple'><b>Purple</b><br><input size='10' name='txtFlagTitlePurple' value='" . $purple_title . "' class='form-control'>";
                            } else {
                                print "<td><font face='Verdana' size='1' color='" . $cur[0] . "'><b>" . $cur[0] . "</b><br><input size='10' name='txtFlagTitle" . $cur[0] . "' value='" . $cur[1] . "' class='form-control'>";
                            }
                        }
                        if (9 < $flag_c) {
                            $this_val = "Violet";
                            switch ($flag_c) {
                                case 11:
                                    $this_val = "Indigo";
                                    break;
                                case 12:
                                    $this_val = "Blue";
                                    break;
                                case 13:
                                    $this_val = "Green";
                                    break;
                                case 14:
                                    $this_val = "Yellow";
                                    break;
                                case 15:
                                    $this_val = "Orange";
                                    break;
                                case 16:
                                    $this_val = "Red";
                                    break;
                                case 17:
                                    $this_val = "Gray";
                                    break;
                                case 18:
                                    $this_val = "Brown";
                                    break;
                                case 19:
                                    $this_val = "Purple";
                                    break;
                            }
                            if (1 < strlen($cur[2])) {
                                print "<br><input type='checkbox' name='chkFlagLink" . $cur[0] . "' value='" . $this_val . "' checked>Linked";
                            } else {
                                print "<br><input type='checkbox' name='chkFlagLink" . $cur[0] . "' value='" . $this_val . "'>Linked";
                            }
                        }
                        print "</font></b></td>";
                        if ($cur[0] == "Purple") {
                            print "</tr><tr>";
                        }
                    }
                    print "</tr></table>";
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <h4>Additional Field Titles</h4>
                <div class="col-4">
                    <?php
                    echo "<div class='mb-3'>
                        <label class='form-label'>F1</label>
                        <input size='10' name='txtF1' value='" . $txtF1 . "' class='form-control'>
                    </div>";
                    ?>
                </div>
                <div class="col-4">
                    <?php
                    echo "<div class='mb-3'>
                        <label class='form-label'>F2</label>
                        <input size='10' name='txtF2' value='" . $txtF2 . "' class='form-control'>
                    </div>";
                    ?>
                </div>
                <div class="col-4">
                    <?php
                    echo "<div class='mb-3'>
                        <label class='form-label'>F3</label>
                        <input size='10' name='txtF3' value='" . $txtF3 . "' class='form-control'>
                    </div>";
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <?php
                    echo "<div class='mb-3'>
                        <label class='form-label'>F4</label>
                        <input size='10' name='txtF4' value='" . $txtF4 . "' class='form-control'>
                    </div>";
                    ?>    
                </div>
                <div class="col-4">
                    <?php
                    echo "<div class='mb-3'>
                        <label class='form-label'>F5</label>
                        <input size='10' name='txtF5' value='" . $txtF5 . "' class='form-control'>
                    </div>";
                    ?>    
                </div>
                <div class="col-4">
                    <?php
                    echo "<div class='mb-3'>
                        <label class='form-label'>F6</label>
                        <input size='10' name='txtF6' value='" . $txtF6 . "' class='form-control'>
                    </div>";
                    ?>    
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <?php
                    echo "<div class='mb-3'>
                        <label class='form-label'>F7</label>
                        <input size='10' name='txtF7' value='" . $txtF7 . "' class='form-control'>
                    </div>";
                    ?>    
                </div>
                <div class="col-4">
                    <?php
                    echo "<div class='mb-3'>
                        <label class='form-label'>F8</label>
                        <input size='10' name='txtF8' value='" . $txtF8 . "' class='form-control'>
                    </div>";
                    ?>    
                </div>
                <div class="col-4">
                    <?php
                    echo "<div class='mb-3'>
                        <label class='form-label'>F9</label>
                        <input size='10' name='txtF9' value='" . $txtF9 . "' class='form-control'>
                    </div>";
                    ?>    
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <?php
                    echo "<div class='mb-3'>
                        <label class='form-label'>F10</label>
                        <input size='10' name='txtF10' value='" . $txtF10 . "' class='form-control'>
                    </div>";
                    ?>    
                </div>
            </div>
        </div>
    </div>
    <!--</div>-->
<?php } ?>
<div class="card">
    <div class="card-body">
        <div class="row">
            <h4>Daily Roster Report Personalization<font face='Verdana' size='1'>[Check the Columns to be viewed]</font></h4>
            <div class="col-12">
                <?php
                print "<table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
                print "<tr><td><font face='Verdana' size='1'>";
                if (strpos($txtRosterColumns, "chkIDColumn") !== false) {
                    print "<input type='checkbox' name='chkIDColumn' value='chkIDColumn' checked>";
                } else {
                    print "<input type='checkbox' name='chkIDColumn' value='chkIDColumn'>";
                }
                print "&nbsp;" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='1'>";
                if (strpos($txtRosterColumns, "chkDept") !== false) {
                    print "<input type='checkbox' name='chkDept' value='chkDept' checked>";
                } else {
                    print "<input type='checkbox' name='chkDept' value='chkDept'>";
                }
                print "&nbsp;Dept </font></td> <td><font face='Verdana' size='1'>";
                if (strpos($txtRosterColumns, "chkDiv") !== false) {
                    print "<input type='checkbox' name='chkDiv' value='chkDiv' checked>";
                } else {
                    print "<input type='checkbox' name='chkDiv' value='chkDiv'>";
                }
                print "&nbsp;Div/Desg </font></td> <td><font face='Verdana' size='1'>";
                if (strpos($txtRosterColumns, "chkRmk") !== false) {
                    print "<input type='checkbox' name='chkRmk' value='chkRmk' checked>";
                } else {
                    print "<input type='checkbox' name='chkRmk' value='chkRmk'>";
                }
                print "&nbsp;Rmk </font></td> <td><font face='Verdana' size='1'>";
                if (strpos($txtRosterColumns, "chkShift") !== false) {
                    print "<input type='checkbox' name='chkShift' value='chkShift' checked>";
                } else {
                    print "<input type='checkbox' name='chkShift' value='chkShift'>";
                }
                print "&nbsp;Shift </font></td> <td><font face='Verdana' size='1'>";
                if (strpos($txtRosterColumns, "chkFlag") !== false) {
                    print "<input type='checkbox' name='chkFlag' value='chkFlag' checked>";
                } else {
                    print "<input type='checkbox' name='chkFlag' value='chkFlag'>";
                }
                print "&nbsp;Flag</font></td> </tr>";
                print "<tr><td><font face='Verdana' size='1'>";
                if (strpos($txtRosterColumns, "chkEntry") !== false) {
                    print "<input type='checkbox'  name='chkEntry' value='chkEntry' checked>";
                } else {
                    print "<input type='checkbox'  name='chkEntry' value='chkEntry'>";
                }
                print "&nbsp;Entry </font></td> <td><font face='Verdana' size='1'>";
                if (strpos($txtRosterColumns, "chkStart") !== false) {
                    print "<input type='checkbox' name='chkStart' value='chkStart' checked>";
                } else {
                    print "<input type='checkbox' name='chkStart' value='chkStart'>";
                }
                print "&nbsp;Start </font></td> <td><font face='Verdana' size='1'>";
                if (strpos($txtRosterColumns, "chkBreakOut") !== false) {
                    print "<input type='checkbox' name='chkBreakOut' value='chkBreakOut' checked>";
                } else {
                    print "<input type='checkbox' name='chkBreakOut' value='chkBreakOut'>";
                }
                print "&nbsp;Break Out </font></td> <td><font face='Verdana' size='1'>";
                if (strpos($txtRosterColumns, "chkBreakIn") !== false) {
                    print "<input type='checkbox' name='chkBreakIn' value='chkBreakIn' checked>";
                } else {
                    print "<input type='checkbox' name='chkBreakIn' value='chkBreakIn'>";
                }
                print "&nbsp;Break In </font></td> <td><font face='Verdana' size='1'>";
                if (strpos($txtRosterColumns, "chkClose") !== false) {
                    print "<input type='checkbox' name='chkClose' value='chkClose' checked>";
                } else {
                    print "<input type='checkbox' name='chkClose' value='chkClose'>";
                }
                print "&nbsp;Close </font></td> <td><font face='Verdana' size='1'>";
                if (strpos($txtRosterColumns, "chkExit") !== false) {
                    print "<input type='checkbox' name='chkExit' value='chkExit' checked>";
                } else {
                    print "<input type='checkbox' name='chkExit' value='chkExit'>";
                }
                print "&nbsp;Exit </font></td> </tr>";
                print "<tr><td><font face='Verdana' size='1'>";
                if (strpos($txtRosterColumns, "chkEarlyIn") !== false) {
                    print "<input type='checkbox' name='chkEarlyIn' value='chkEarlyIn' checked>";
                } else {
                    print "<input type='checkbox' name='chkEarlyIn' value='chkEarlyIn'>";
                }
                print "&nbsp;Early In </font></td> <td><font face='Verdana' size='1'>";
                if (strpos($txtRosterColumns, "chkLateIn") !== false) {
                    print "<input type='checkbox' name='chkLateIn' value='chkLateIn' checked>";
                } else {
                    print "<input type='checkbox' name='chkLateIn' value='chkLateIn'>";
                }
                print "&nbsp;Late In </font></td> <td><font face='Verdana' size='1'>";
                if (strpos($txtRosterColumns, "chkLessBreak") !== false) {
                    print "<input type='checkbox' name='chkLessBreak' value='chkLessBreak' checked>";
                } else {
                    print "<input type='checkbox' name='chkLessBreak' value='chkLessBreak'>";
                }
                print "&nbsp;Less Break </font></td> <td><font face='Verdana' size='1'>";
                if (strpos($txtRosterColumns, "chkMoreBreak") !== false) {
                    print "<input type='checkbox' name='chkMoreBreak' value='chkMoreBreak' checked>";
                } else {
                    print "<input type='checkbox' name='chkMoreBreak' value='chkMoreBreak'>";
                }
                print "&nbsp;More Break </font></td> <td><font face='Verdana' size='1'>";
                if (strpos($txtRosterColumns, "chkEarlyOut") !== false) {
                    print "<input type='checkbox' name='chkEarlyOut' value='chkEarlyOut' checked>";
                } else {
                    print "<input type='checkbox' name='chkEarlyOut' value='chkEarlyOut'>";
                }
                print "&nbsp;EarlyOut </font></td> <td><font face='Verdana' size='1'>";
                if (strpos($txtRosterColumns, "chkLateOut") !== false) {
                    print "<input type='checkbox' name='chkLateOut' value='chkLateOut' checked>";
                } else {
                    print "<input type='checkbox' name='chkLateOut' value='chkLateOut'>";
                }
                print "&nbsp;LateOut </font></td> </tr>";
                print "<tr><td><font face='Verdana' size='1'>";
                if (strpos($txtRosterColumns, "chkGrace") !== false) {
                    print "<input type='checkbox' name='chkGrace' value='chkGrace' checked>";
                } else {
                    print "<input type='checkbox' name='chkGrace' value='chkGrace'>";
                }
                print "&nbsp;Grace </font></td> <td><font face='Verdana' size='1'>";
                if (strpos($txtRosterColumns, "chkNormal") !== false) {
                    print "<input type='checkbox' name='chkNormal' value='chkNormal' checked>";
                } else {
                    print "<input type='checkbox' name='chkNormal' value='chkNormal'>";
                }
                print "&nbsp;Normal </font></td> <td><font face='Verdana' size='1'>";
                if (strpos($txtRosterColumns, "chkOT") !== false) {
                    print "<input type='checkbox' name='chkOT' value='chkOT' checked>";
                } else {
                    print "<input type='checkbox' name='chkOT' value='chkOT'>";
                }
                print "&nbsp;OT </font></td> <td><font face='Verdana' size='1'>";
                if (strpos($txtRosterColumns, "chkAppOT") !== false) {
                    print "<input type='checkbox' name='chkAppOT' value='chkAppOT' checked>";
                } else {
                    print "<input type='checkbox' name='chkAppOT' value='chkAppOT'>";
                }
                print "&nbsp;AppOT </font></td> <td><font face='Verdana' size='1'>";
                if (strpos($txtRosterColumns, "chkOT1") !== false) {
                    print "<input type='checkbox' name='chkOT1' value='chkOT1' checked>";
                } else {
                    print "<input type='checkbox' name='chkOT1' value='chkOT1'>";
                }
                print "&nbsp;OT 1</font></td> <td><font face='Verdana' size='1'>";
                if (strpos($txtRosterColumns, "chkOT2") !== false) {
                    print "<input type='checkbox' name='chkOT2' value='chkOT2' checked>";
                } else {
                    print "<input type='checkbox' name='chkOT2' value='chkOT2'>";
                }
                print "&nbsp;OT 2</font></td> </tr>";
                print "<tr><td><font face='Verdana' size='1'>";
                if (strpos($txtRosterColumns, "chkTH") !== false) {
                    print "<input type='checkbox' name='chkTH' value='chkTH' checked>";
                } else {
                    print "<input type='checkbox' name='chkTH' value='chkTH'>";
                }
                print "&nbsp;Total Hrs </font></td>";
                print "<td><font face='Verdana' size='1'>";
                if (strpos($txtRosterColumns, "chkF1") !== false) {
                    print "<input type='checkbox' name='chkF1' value='chkF1' checked>";
                } else {
                    print "<input type='checkbox' name='chkF1' value='chkF1'>";
                }
                print "&nbsp;F1 </font></td>";
                print "<td><font face='Verdana' size='1'>";
                if (strpos($txtRosterColumns, "chkF2") !== false) {
                    print "<input type='checkbox' name='chkF2' value='chkF2' checked>";
                } else {
                    print "<input type='checkbox' name='chkF2' value='chkF2'>";
                }
                print "&nbsp;F2 </font></td>";
                print "<td><font face='Verdana' size='1'>";
                if (strpos($txtRosterColumns, "chkF3") !== false) {
                    print "<input type='checkbox' name='chkF3' value='chkF3' checked>";
                } else {
                    print "<input type='checkbox' name='chkF3' value='chkF3'>";
                }
                print "&nbsp;F3 </font></td>";
                print "<td><font face='Verdana' size='1'>";
                if (strpos($txtRosterColumns, "chkF4") !== false) {
                    print "<input type='checkbox' name='chkF4' value='chkF4' checked>";
                } else {
                    print "<input type='checkbox' name='chkF4' value='chkF4'>";
                }
                print "&nbsp;F4 </font></td>";
                print "<td><font face='Verdana' size='1'>";
                if (strpos($txtRosterColumns, "chkF5") !== false) {
                    print "<input type='checkbox' name='chkF5' value='chkF5' checked>";
                } else {
                    print "<input type='checkbox' name='chkF5' value='chkF5'>";
                }
                print "&nbsp;F5 </font></td>";
                print "</tr>";
                print "</table>";
                ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-body">
                <h4>Transaction Record Lock/ Migration Date</h4><br>
                <font face='Verdana' size='1'>[Requires <u>EDIT/DELETE</u> Rights on this Module to change this value]</font>
                <div class="mb-3">
                    <?php
                    echo "<input name='txtLockDate' size='12' value='" . $txtLockDate . "' class='form-control mb-3'> <input type='hidden' name='txhLockDate' value='" . $txtLockDate . "'>";
                    if (strpos($userlevel, $current_module . "D") !== false) {
                        print "<input name='btChangeLockDate' class='btn btn-primary' type='button' value='Change' onClick='javascript:changeLockDate()'>";
                        if ($_SESSION[$session_variable . "VirdiLevel"] == "Classic") {
                            print "&nbsp;&nbsp;<input name='btUserDivLockDate' class='btn btn-primary' type='button' value='Division wise Lock Dates' onClick='javascript:userDivLockDate()'>";
                        }
                    } else {
                        print "<input disabled type='button' class='btn btn-primary' value='Change'>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6">
                <?php if (strpos($userlevel, $current_module . "E") !== false) { ?>
            <div class="card">
                <div class="card-body">
                    <h4>Company Logo</h4>
                    <?php
                    echo "<div class='mb-3'>";
                    echo "<label class='form-label' for='uploadedfile'>Company Logo</label>";

                    // File upload and hidden inputs
                    echo "<input type='hidden' name='MAX_FILE_SIZE' value='512000'/>";
                    echo "<input type='hidden' name='targetDir' value='img/'/>";
                    echo "<input type='file' class='form-control' name='uploadedfile' id='uploadedfile'/>";

                    // Submit Logo button
                    echo "<br><input type='button' class='btn btn-primary' value='Submit Logo' onClick=\"document.frm.act.value='uploadLogo';document.frm.submit()\">";

                    // Display Delete Logo button if logo exists
                    if ($txhClientLogo != "") {
                        echo "&nbsp;&nbsp;<input type='button' class='btn btn-danger' value='Delete Logo' onClick=\"document.frm.act.value='deleteLogo';document.frm.submit()\">";
                    }
                    // File size and dimensions information
                    echo "<br><small class='text-muted'>(File Size should not exceed 512 KB) (Recommended Logo width: Less than 800 pixels)</small>";
                    echo "</div>";
                    ?>
                </div>
            </div>
<?php } ?>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4>Execute Scripts Manually <br>[Requires <u>ADD</u> Rights on this Module]</h4>
                <?php
                if (strpos($userlevel, $current_module . "A") !== false) {
                    echo "<div class='row'>";
                    echo "<div class='col-2 mb-3'>";
                    if (getRegister($txtMACAddress, 7) == "165") {
                        print "<input disabled id='bt1' type='button' class='btn btn-primary' value='Day Master' onClick='openScriptWindow(1)'>";
                    } else {
                        print "<input id='bt1' type='button' class='btn btn-primary' value='Day Master' onClick='openScriptWindow(1)'>";
                    }
                    echo "</div>";
                    if ($_SESSION[$session_variable . "VirdiLevel"] == "Classic") {
                        echo "<div class='col-2 mb-3'>";
                        print "<a name='bt18'><input id='bt18' type='button' class='btn btn-primary' value='Pay-Off' onClick='openScriptWindow(18)'></a>";
                        echo "</div>";
                        echo "<div class='col-2 mb-3'>";
                        print "<input id='bt19' type='button' class='btn btn-primary' value='Access Limit' onClick='openScriptWindow(19)'>";
                        echo "</div>";
//                        print "<br>";
                    }

                    if (getRegister($txtMACAddress, 7) == "-1") {
                        echo "<div class='col-2'>";
                        print "&nbsp;&nbsp;<input id='bt2' type='button' class='btn btn-primary' value='Week Master' onClick='openScriptWindow(2)'>";
                        echo "</div>";
                    }

//                    echo "</div>";

                    if (strpos($userlevel, "27D") !== false && $_SESSION[$session_variable . "VirdiLevel"] == "Classic") {
                        if (getRegister($txtMACAddress, 7) == "99" || getRegister($txtMACAddress, 7) == "123") {
//                            echo "<div class='row'>";
                            echo "<div class='col-2 mb-3'>";
                            print "<input id='bt25' type='button' class='btn btn-primary' value='Payroll Synch' onClick='openScriptWindow(25)'>";
                            echo "</div>";
//                            echo "</div>";
                        } else {
//                            echo "<div class='row'>";
                            if (getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) != 3) {
                                echo "<div class='col-2 mb-3'>";
                                print "<input id='bt12' type='button' class='btn btn-primary' value='PayMaster Users' onClick='openScriptWindow(12)'>";
                                echo "</div>";
                                echo "<div class='col-2 mb-3'>";
                                print "<a href='' value='Add PayMaster Users'></a>";
                                print "<input id='bt28' type='button' class='btn btn-primary' value='PayMaster Attendance' onClick='openScriptWindow(28)'>";
                                echo "</div>";
////                            echo "</div>";
//                            echo "<div class='row'>";
                                echo "<div class='col-2 mb-3'>";
                                print "<input id='bt34' type='button' class='btn btn-primary' value='PayMaster Attendance CSV' onClick='openScriptWindow(34)'>";
                                echo "</div>";
                                echo "</div>";
                            }
                        }
                    }
                    echo "<div class='row'>";
                    echo "<div class='col-2 mb-3'>";
                    if (strpos($userlevel, "17D") !== false) {
                        print "<input id='bt14' type='button' class='btn btn-primary' value='Shift Synch' onClick='openScriptWindow(14)'>";
                    }
                    echo "</div>";
//                    print "<br><br>";

                    if (strpos($userlevel, $current_module . "A") !== false) {
                        echo "<div class='col-2 mb-3'>";
                        print "<input id='bt4' type='button' class='btn btn-primary' value='Backup' onClick='openScriptWindow(4)'>";
//                      print "<input id='bt5' type='button' class='btn btn-primary' value='Version Update' onClick='openScriptWindow(5)'>";
                        echo "</div>";
                    }

                    if (strpos($userlevel, $current_module . "D") !== false && $_SESSION[$session_variable . "VirdiLevel"] == "Classic") {
                        echo "<div class='col-2 mb-3'>";
                        print "<input id='bt16' type='button' class='btn btn-primary' value='Maintain DB' onClick='openScriptWindow(16)'>";
                        if (getRegister($txtMACAddress, 7) == "21" || getRegister($txtMACAddress, 7) == "109" || getRegister($txtMACAddress, 7) == "4" || getRegister($txtMACAddress, 7) == "14" || getRegister($txtMACAddress, 7) == "63" || getRegister($txtMACAddress, 7) == "89" || getRegister($txtMACAddress, 7) == "18" || getRegister($txtMACAddress, 7) == "24" || getRegister($txtMACAddress, 7) == "49" || getRegister($txtMACAddress, 7) == "19" || getRegister($txtMACAddress, 7) == "151" || getRegister($txtMACAddress, 7) == "115") {
                            print "&nbsp;&nbsp;<input id='bt11' type='button' class='btn btn-primary' value='DB Migrate' onClick='openScriptWindow(11)'>";
                        } else {
                            if (getRegister($txtMACAddress, 7) == "-1") {
                                print "&nbsp;&nbsp;<input id='bt15' type='button' class='btn btn-primary' value='DB Migrate' onClick='openScriptWindow(15)'>";
                            } else {
                                if (getRegister($txtMACAddress, 7) == "27") {
                                    print "&nbsp;&nbsp;<input id='bt26' type='button' class='btn btn-primary' value='DB Migrate' onClick='openScriptWindow(26)'>";
                                } else {
                                    if (getRegister($txtMACAddress, 7) == "130" || getRegister($txtMACAddress, 7) == "177") {
                                        print "&nbsp;&nbsp;<input id='bt30' type='button' class='btn btn-primary' value='DB Migrate' onClick='openScriptWindow(30)'>";
                                    } else {
                                        if (getRegister($txtMACAddress, 7) == "112") {
                                            print "&nbsp;&nbsp;<input id='bt31' type='button' class='btn btn-primary' value='DB Migrate' onClick='openScriptWindow(31)'>";
                                        } else {
                                            if (getRegister($txtMACAddress, 7) == "139") {
                                                print "&nbsp;&nbsp;<input id='bt32' type='button' class='btn btn-primary' value='DB Migrate' onClick='openScriptWindow(32)'>";
                                            } else {
                                                if (getRegister($txtMACAddress, 7) == "161") {
                                                    print "&nbsp;&nbsp;<input id='bt33' type='button' class='btn btn-primary' value='DB Migrate' onClick='openScriptWindow(33)'>";
                                                } else {
                                                    if (getRegister($txtMACAddress, 7) == "165") {
                                                        print "&nbsp;&nbsp;<input id='bt35' type='button' class='btn btn-primary' value='DB Migrate' onClick='openScriptWindow(35)'>";
                                                    } else {
                                                        if (getRegister($txtMACAddress, 7) == "181") {
                                                            print "&nbsp;&nbsp;<input id='bt36' type='button' class='btn btn-primary' value='DB Migrate' onClick='openScriptWindow(36)'>";
                                                        } else {
                                                            if (getRegister($txtMACAddress, 7) == "156") {
                                                                print "&nbsp;&nbsp;<input id='bt37' type='button' class='btn btn-primary' value='DB Migrate' onClick='openScriptWindow(37)'>";
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
                        echo "</div>";
//                        echo "</div>";
//                        echo "<div class='row'>";
                        echo "<div class='col-2 mb-3'>";
                        print "<input id='bt15' type='button' class='btn btn-primary' value='Fix Wrong Shift Clockins' onClick='openScriptWindow(20)'>";
                        echo "</div>";
                        echo "</div>";
//                        echo "<div class='col-6 mb-3'></div>";
//                        echo "</div>";
                    }
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                    echo "<div class='row'>";
                    echo "<div class='col-12'>
                            <div class='card'>
                                <div class='card-body'>";
                    if (getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) != 3) {
                        if ($_SESSION[$session_variable . "VirdiLevel"] == "Classic") {
                            echo "<div class='row'>";
                            print "<h4>Mailers <br>[Requires <u>ADD</u> Rights on this Module]</h4>";
                            echo "<div class='col-2 mb-3'>";
                            print"<input id='bt6' type='button' class='btn btn-primary' value='Attendance' onClick='openScriptWindow(6)'>";
                            echo "</div>";
                            echo "<div class='col-2 mb-3'>";
                            print "<input id='bt7' type='button' class='btn btn-primary' value='Absence' onClick='openScriptWindow(7)' style='width:75px;'>";
                            echo "</div>";
                            echo "<div class='col-2 mb-3'>";
                            print "<input id='bt24' type='button' value='ATT/ ABS' class='btn btn-primary' onClick='openScriptWindow(24)'>";
                            echo "</div>";
//                        echo "</div>";
//                        echo "<div class='row'>";
                            echo "<div class='col-2 mb-3'>";
                            print "<input id='bt8' type='button' class='btn btn-primary' value='Odd Log' onClick='openScriptWindow(8)' style='width:75px;'>";
                            echo "</div>";
                            echo "<div class='col-2 mb-3'>";
                            print "<input id='bt9' type='button' class='btn btn-primary' value='Late Arrival' onClick='openScriptWindow(9)'>";
                            echo "</div>";
                            echo "<div class='col-2 mb-3'>";
                            print "<input id='bt10' type='button' class='btn btn-primary' value='Early Exit' onClick='openScriptWindow(10)'>";
                            echo "</div>";
                            echo "</div>";
                            echo "<div class='row'>";
                            echo "<div class='col-12 mb-3'>";
                            print "<a href='MailerText.php' target='_blank'><font size='1' color='#000000' face='Verdana'>Click to Edit Mailer Text</font></a>";
                            echo "</div>";
                            echo "</div>";
                        }
                    }
                } else {
                    echo "<div class='row'>";
                    echo "<div class='col-4  mb-3'>";
                    print "<input disabled id='bt1' type='button' class='btn btn-primary' value='Day Master' onClick='openScriptWindow(1)'>";
                    echo "</div>";
                    echo "<div class='col-4  mb-3'>";
                    print "<input disabled id='bt2' type='button' class='btn btn-primary' value='Week Master' onClick='openScriptWindow(2)'>";
                    echo "</div>";
                    echo "<div class='col-4  mb-3'>";
                    print "<input disabled id='bt4' type='button' class='btn btn-primary' value='Backup' onClick='openScriptWindow(4)'>";
                    echo "</div>";
                    echo "</div>";
                    echo "<div class='row'>";
                    echo "<div class='col-4  mb-3'>";
//                    print "<input disabled id='bt5' type='button' class='btn btn-primary' value='Version Update' onClick='openScriptWindow(5)'>";
                    print "<input disabled id='bt12' type='button' value='PayMaster Synch' onClick='openScriptWindow(12)'>";
                    echo "</div>";
                    echo "</div>";
                    if (getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 8) != 3) {
                        if ($_SESSION[$session_variable . "VirdiLevel"] == "Classic") {
                            echo "<div class='row'>";
                            print "<h4><b>Mailers <br>[Requires <u>ADD</u> Rights on this Module]</h4>";
                            echo "<div class='col-4  mb-3'>";
                            print "<input disabled id='bt6' type='button' class='btn btn-primary' value='Attendance' onClick='openScriptWindow(6)'>";
                            echo "</div>";
                            echo "<div class='col-4  mb-3'>";
                            print "<input disabled id='bt7' type='button' value='Absence' class='btn btn-primary' onClick='openScriptWindow(7)'>";
                            echo "</div>";
                            echo "<div class='col-4  mb-3'>";
                            print "<input disabled id='bt8' type='button' value='Odd Log' onClick='openScriptWindow(8)'>";
                            echo "</div>";
                            echo "</div>";
                            echo "<div class='row'>";
                            echo "<div class='col-4  mb-3'>";
                            print "<input disabled id='bt9' type='button' value='Late Arrival' class='btn btn-primary' onClick='openScriptWindow(9)'>";
                            echo "</div>";
                            echo "<div class='col-4  mb-3'>";
                            print "<input disabled id='bt10' type='button' class='btn btn-primary' value='Early Exit' onClick='openScriptWindow(10)'>";
                            echo "</div>";
                            echo "</div>";
                        }
                    }
                }
                echo "</div>";
                echo "</div>";
                echo "</div>";
                echo "</div>";
                ?>
                <!--            </div>
                        </div>
                    </div>-->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-4">
                                        <?php
                                        if (strpos($userlevel, $current_module . "E") !== false) {
                                            print "<input name='btSaveChanges' type='button' class='btn btn-primary' onClick='javascript:checkSubmit()' value='Save Changes'>";
                                        }
                                        ?>
                                    </div>
                                    <?php
                                    if (strpos($userlevel, "27D") !== false && strpos($userlevel, $current_module . "E") !== false) {
                                        print "<div class='col-4 mb-3'>";
                                        print "<input type='button' class='btn btn-primary' onClick='javascript:openWindow()' value='Upload Emp Info'>";
                                        print "</div>";
                                        print "<div class='col-4 mb-3'>";
                                        print "<input type='button' class='btn btn-primary' onClick='javascript:openWindowEmpDate()' value='Upload Emp Date'>";
                                        print "</div>";
                                    }
                                    ?>
                                </div>
                                <div class="row">
                                    <div class="col-12 mb-3">
<?php
print "<font face='Verdana' size='1'>To PROCESS Daily Clocking Data for Daily Basis Shift - Go to -->Start - Programs - Accessories - System Tools - Task Manager--< and Create a New Task to be RUN on the specified DAYS and TIME. Select the Program to be RUN as '[APACHE_ROOT]/virdi/DayMaster.bat'.";
?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 mb-3">
<?php
print "Scheduled Tasks are System Password DEPENDENT. Please make sure that you also change the Password for the Task whenever you change the System Password.";
?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 mb-3">
<?php
print "<b>Shift Synch</b>: Assigns Current Shift to all Employees with Not Assigned Shift in Alter Time";
?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                echo "</form>\t\t\r\n\t</table>\t\r\n\t<script>\r\n\t\tfunction checkSubmit(){\r\n\t\t\tx = document.frm;\r\n\t\t\tif (x.txtMinClockinPeriod.value == '' || x.txtMinClockinPeriod.value*1 != x.txtMinClockinPeriod.value/1){\r\n\t\t\t\talert(\"Invalid Seconds Value\");\r\n\t\t\t\tx.txtMinClockinPeriod.focus();\t\t\t\t\r\n\t\t\t}else if (checkTime(x.txtNightShiftMaxOutTime.value) == false){\r\n\t\t\t\talert(\"Invalid Time. Time Format should be HHMM ONLY.\");\r\n\t\t\t\tx.txtNightShiftMaxOutTime.focus();\t\t\t\t\r\n\t\t\t}else if (x.txtEmployeeCodeLength.value == '' || x.txtEmployeeCodeLength.value*1 != x.txtEmployeeCodeLength.value/1 || x.txtEmployeeCodeLength.value*1 < 0){\r\n\t\t\t\talert(\"Invalid Employee Code Length\");\r\n\t\t\t\tx.txtEmployeeCodeLength.focus();\r\n\t\t\t}else if (x.lstCAGR.value == 'Yes' && x.lstUseShiftRoster.value == 'Yes'){\r\n\t\t\t\talert(\"Either Use Shift Roster OR Access Group Roster\");\r\n\t\t\t\tx.lstCAGR.focus();\r\n\t\t\t}else if (x.txtLateDays.value == '' || x.txtLateDays.value*1 != x.txtLateDays.value/1 || x.txtLateDays.value*1 < 0){\r\n\t\t\t\talert(\"Invalid Lateness Days\");\r\n\t\t\t\tx.txtLateDays.focus();\r\n\t\t\t}else if (check_valid_date(x.txtLockDate.value) == false){\r\n\t\t\t\talert(\"Invalid Date Format. Date Format should be DD/MM/YYYY ONLY. Default Value: 01/01/2001\");\r\n\t\t\t\tx.txtLockDate.focus();\t\t\t\t\r\n\t\t\t}else if (x.VirdiLevel.value == \"Classic\" && (x.txtDBName.value != \"\" && (x.txtDBPass.value != x.txtDBPassRepeat.value))){\r\n\t\t\t\talert(\"Payroll Password Values DO NOT match\");\r\n\t\t\t\tx.txtDBPass.focus();\r\n\t\t\t}else if (x.VirdiLevel.value == \"Classic\" && (x.txtMapTableName.value != \"\" && x.lstMapOverwrite.value == \"\")){\r\n\t\t\t\talert(\"Please select the Database to be Overwriten/ Deleted during Payroll Synchronization\");\r\n\t\t\t\tx.txtMapOverwrite.focus();\r\n\t\t\t}else if (x.VirdiLevel.value == \"Classic\" && (x.txtMapTableName.value != \"\" && (x.txtMapEID.value == \"\" || x.txtMapEName.value == \"\"))){\r\n\t\t\t\talert(\"Please enter the Employee ID and Name Columns\");\r\n\t\t\t\tx.txtMapEID.focus();\r\n\t\t\t}else if (x.VirdiLevel.value == \"Classic\" && (x.lstMapDataCOMPayroll.value == \"No\" && x.lstMapUpdateDate.value == \"Yes\")){\r\n\t\t\t\talert(\"Cannot Update Dates IF Payroll is NOT selected\");\r\n\t\t\t\tx.lstMapUpdateDate.focus();\r\n\t\t\t}else if (x.VirdiLevel.value == \"Classic\" && (x.lstMapDataCOMPayroll.value == \"No\" && x.lstMapUpdateSalary.value == \"Yes\")){\r\n\t\t\t\talert(\"Cannot Update Dates IF Payroll is NOT selected\");\r\n\t\t\t\tx.lstMapUpdateSalary.focus();\r\n\t\t\t}else if (x.VirdiLevel.value == \"Classic\" && x.lstEmployeeEmailField.value == x.lstEmployeeSMSField.value && x.lstEmployeeEmailField.value != \"\"){\r\n\t\t\t\talert(\"EMail and SMS Field Values CANNOT be same\");\r\n\t\t\t\tx.lstEmployeeEmailField.focus();\r\n\t\t\t}else if (x.VirdiLevel.value == \"Classic\" && (x.lstMapDataCOMPayroll.value == \"No\" && x.lstMapCostCentre.value != \"\")){\r\n\t\t\t\talert(\"Cannot Update Dates IF Payroll is NOT selected\");\r\n\t\t\t\tx.lstMapCostCentre.focus();\r\n\t\t\t}else if (x.VirdiLevel.value == \"Classic\" && (x.lstMapDataCOMPayroll.value == \"No\" && x.lstMapCostCentre.value != \"\")){\r\n\t\t\t\talert(\"Cannot Update Dates IF Payroll is NOT selected\");\r\n\t\t\t\tx.lstMapCostCentre.focus();\r\n\t\t\t}else if (x.VirdiLevel.value == \"Classic\" && (x.lstMapUpdateSalary.value == \"Yes\" && (x.txtMapIDNo.value != \"group_value\" && x.txtMapRemark.value != \"group_value\" && x.txtMapPhone.value != \"group_value\"))){\r\n\t\t\t\talert(\"Update Salary Option while Mapping Payroll requires the Payroll [group_value] Column Mapping against Social No OR Remark OR Phone\");\r\n\t\t\t\tx.lstMapUpdateSalary.focus();\r\n\t\t\t//}else if (x.VirdiLevel.value == \"Classic\" && (x.txtSMTPServer.value != \"\" && (x.txtSMTPFrom.value == \"\" || x.txtSMTPUser.value == \"\" || x.txtSMTPPass.value == \"\"))){\r\n\t\t\t}else if (x.txtSMTPServer.value != \"\" && (x.txtSMTPFrom.value == \"\" || x.txtSMTPUser.value == \"\" || x.txtSMTPPass.value == \"\")){\r\n\t\t\t//}else if (x.txtSMTPServer.value != \"\" && x.txtSMTPFrom.value == \"\"){\r\n\t\t\t\talert(\"Please enter the Sender's Email Address/ SMTP Username and Password\");\r\n\t\t\t\tx.txtSMTPFrom.focus();\r\n\t\t\t//}else if (x.VirdiLevel.value == \"Classic\" && (x.txtSMTPServer.value != \"\" && (x.txtSMTPPass.value != x.txtSMTPPassRepeat.value))){\r\n\t\t\t}else if (x.txtSMTPServer.value != \"\" && (x.txtSMTPPass.value != x.txtSMTPPassRepeat.value)){\r\n\t\t\t\talert(\"SMTP Password Values DO NOT match\");\r\n\t\t\t\tx.txtSMTPPass.focus();\t\t\t\t\r\n\t\t\t\t/*\r\n\t\t\t}else if (x.lstRoundOffAOT.value != \"None\" && x.lstAutoApproveOT.value == \"No\"){\r\n\t\t\t\talert(\"Auto Approve OT should be YES for Rounding Off of Approved OT\");\r\n\t\t\t\tx.lstAutoApproveOT.focus();\t\t\t\t\r\n\t\t\t}else if (x.lstRotateShift.value == \"Yes\" && x.lstUseShiftRoster.value == \"Yes\"){\r\n\t\t\t\talert(\"Automatic Shift Rotation and Shift Roster CANNOT be used together\");\r\n\t\t\t\tx.lstRotateShift.focus();\t\t\t\t\r\n\t\t\t}else if (x.lstRotateShift.value == \"No\" && x.lstSRDay.value != \"None\" && x.lstSRScenario.value != \"None\"){\r\n\t\t\t\talert(\"Please Set the Shift Rotation Day and Scenario Values to None for Non Automatic Shift Rotations\");\r\n\t\t\t\tx.lstSRDay.focus();\r\n\t\t\t\t*/\r\n\t\t\t}else{\r\n\t\t\t\t//Check Database Path\r\n\t\t\t\tvar db_path = x.txtBackupPath.value;\r\n\t\t\t\tvar new_db_path = \"\";\r\n\t\t\t\tif (db_path.charAt(db_path.length-1) == '\\\\'){\r\n\t\t\t\t\tdb_path = db_path.substring(0, db_path.length-1);\t\r\n\t\t\t\t}\r\n\t\t\t\tfor (i=0;i<db_path.length;i++){\r\n\t\t\t\t\tif (db_path.charAt(i) == '\\\\'){\r\n\t\t\t\t\t\tnew_db_path = new_db_path + \"\\\\\\\\\";\r\n\t\t\t\t\t}else{\r\n\t\t\t\t\t\tnew_db_path = new_db_path + db_path.charAt(i);\r\n\t\t\t\t\t}\r\n\t\t\t\t}\r\n\t\t\t\tx.txtBackupPath.value = new_db_path;\r\n\r\n\t\t\t\t//Check MySQL Bin Path\r\n\t\t\t\tvar my_path = x.txtMealCouponPrinterName.value;\r\n\t\t\t\tvar new_my_path = \"\";\r\n\t\t\t\tif (my_path.charAt(my_path.length-1) == '\\\\'){\r\n\t\t\t\t\tmy_path = my_path.substring(0, my_path.length-1);\t\r\n\t\t\t\t}\r\n\t\t\t\tfor (i=0;i<my_path.length;i++){\r\n\t\t\t\t\tif (my_path.charAt(i) == '\\\\'){\r\n\t\t\t\t\t\tnew_my_path = new_my_path + \"\\\\\\\\\";\r\n\t\t\t\t\t}else{\r\n\t\t\t\t\t\tnew_my_path = new_my_path + my_path.charAt(i);\r\n\t\t\t\t\t}\r\n\t\t\t\t}\r\n\t\t\t\tx.txtMealCouponPrinterName.value = new_my_path;\r\n\t\t\t\tif (confirm('Save Changes')){\r\n\t\t\t\t\t//alert(new_my_path);\r\n\t\t\t\t\tx.btSaveChanges.disabled = true;\r\n\t\t\t\t\tx.submit();\r\n\t\t\t\t}\r\n\t\t\t}\r\n\t\t}\r\n\r\n\t\tfunction changeLockDate(){\r\n\t\t\tx = document.frm;\r\n\t\t\ta = x.txtLockDate.value;\r\n\t\t\tb = x.txhLockDate.value;\r\n\t\t\tif (check_valid_date(a) == false){\r\n\t\t\t\talert(\"Invalid Date Format. Date Format should be DD/MM/YYYY ONLY\");\r\n\t\t\t\tx.txtLockDate.focus();\r\n\t\t\t}else{\r\n\t\t\t\ta = a.substring(6,10) + a.substring(3,5) + a.substring(0,2);\r\n\t\t\t\tb = b.substring(6,10) + b.substring(3,5) + b.substring(0,2);\r\n\t\t\t\tif (a < b){\r\n\t\t\t\t\talert('New Lock Date should be greater than '+x.txhLockDate.value);\r\n\t\t\t\t}else{\r\n\t\t\t\t\tif (confirm('Change Lock Date')){\r\n\t\t\t\t\t\tx.act.value = 'changeLockDate';\r\n\t\t\t\t\t\tx.btChangeLockDate.disabled = true;\r\n\t\t\t\t\t\tx.submit();\r\n\t\t\t\t\t}\r\n\t\t\t\t}\r\n\t\t\t}\r\n\t\t}\r\n\r\n\t\tfunction checkTime(a){\r\n\t\t\t//alert(a);\r\n\t\t\tif (a.length != 4){\r\n\t\t\t\treturn false;\r\n\t\t\t}else if (a*1 != a/1){\r\n\t\t\t\treturn false;\r\n\t\t\t}else if (a.substring(0, 2)*1 > 24){\r\n\t\t\t\treturn false;\r\n\t\t\t}else if (a.substring(2, 4)*1 > 59){\r\n\t\t\t\treturn false;\r\n\t\t\t}\r\n\t\t}\r\n\r\n\t\tfunction check_valid_date(z){\r\n\t\t\t//alert(DD);\r\n\t\t\t//alert(MM);\r\n\t\t\t//alert(YYYY);\r\n\t\t\t//z = DD+\"/\"+MM+\"/\"YYYY;\r\n\t\t\tif(z.length != 10 || z.substring(6,10)*1 < 1900 || z.substring(6,10)*1 > 2200){\r\n\t\t\t\treturn false;\r\n\t\t\t}else{\r\n\t\t\t\tif (z.substring(0,2)*1 < 28 && z.substring(3,5)*1 < 13 && z.substring(2,3) == '/'  && z.substring(5,6) == '/'){\r\n\t\t\t\t\treturn true;\r\n\t\t\t\t}else{\r\n\t\t\t\t\tif ((z.substring(3,5)*1 == 4 || z.substring(3,5)*1 == 6 || z.substring(3,5)*1 == 9 || z.substring(3,5)*1 == 11) && z.substring(0,2)*1 < 31){\r\n\t\t\t\t\t\treturn true;\r\n\t\t\t\t\t}else if (z.substring(3,5)*1 == 2 && z.substring(6,10)*1 % 4 == 0 && z.substring(0,2)*1 < 30){\r\n\t\t\t\t\t\treturn true;\r\n\t\t\t\t\t}else if (z.substring(3,5)*1 == 2 && z.substring(6,10)*1 % 4 != 0 && z.substring(0,2)*1 < 29){\r\n\t\t\t\t\t\treturn true;\r\n\t\t\t\t\t}else if ((z.substring(3,5)*1 == 1 || z.substring(3,5)*1 == 3 || z.substring(3,5)*1 == 5 || z.substring(3,5)*1 == 7 || z.substring(3,5)*1 == 8 || z.substring(3,5)*1 == 10 || z.substring(3,5)*1 == 12) && z.substring(0,2)*1 < 32){\r\n\t\t\t\t\t\treturn true;\r\n\t\t\t\t\t}else{\r\n\t\t\t\t\t\treturn false;\r\n\t\t\t\t\t}\t\t\r\n\t\t\t\t}\r\n\t\t\t}\r\n\t\t}\r\n\r\n\t\tfunction userDivLockDate(){\r\n\t\t\twindow.open('UserDivLockDate.php', 'UserDivLockDate', 'width=400;height=400;resize=no;menubar=no;addressbar=no');\r\n\t\t}\r\n\t\t\r\n\t\tfunction openWindow(){\r\n\t\t\twindow.open('UploadEmployeeInfo.php', 'UploadEmployeeInfo', 'width=700;height=600;resize=no;menubar=no;addressbar=no');\r\n\t\t}\r\n\r\n\t\tfunction openWindowEmpDate(){\r\n\t\t\twindow.open('UploadEmployeeDate.php', 'UploadEmployeeDate', 'width=700;height=600;resize=no;menubar=no;addressbar=no');\r\n\t\t}\r\n\r\n\t\tfunction openScriptWindow(a){\r\n\t\t\tvar b = \"DayMaster\";\r\n\t\t\tif (a != 18){\r\n\t\t\t\tif (a == 2){\r\n\t\t\t\t\tb = \"WeekMaster\";\r\n\t\t\t\t}else if (a == 3){\r\n\t\t\t\t\tb = \"RotateShift\";\r\n\t\t\t\t}else if (a == 4){\r\n\t\t\t\t\tb = \"Backup\";\r\n\t\t\t\t}else if (a == 5){\r\n\t\t\t\t\tb = \"VersionUpdate\";\r\n\t\t\t\t}else if (a == 6){\r\n\t\t\t\t\tb = \"MailerAttendance\";\r\n\t\t\t\t}else if (a == 7){\r\n\t\t\t\t\tb = \"MailerAbsence\";\r\n\t\t\t\t}else if (a == 8){\r\n\t\t\t\t\tb = \"MailerOddLog\";\r\n\t\t\t\t}else if (a == 9){\r\n\t\t\t\t\tb = \"MailerLateArrival\";\r\n\t\t\t\t}else if (a == 10){\r\n\t\t\t\t\tb = \"MailerEarlyExit\";\r\n\t\t\t\t}else if (a == 11){\r\n\t\t\t\t\tb = \"OIMigrate\";\r\n\t\t\t\t}else if (a == 12){\r\n\t\t\t\t\t//b = \"PayrollSynch\";\r\n\t\t\t\t\tb = \"PayMasterUser\";\r\n\t\t\t\t}else if (a == 13){\r\n\t\t\t\t\tb = \"AAMigrate\";\r\n\t\t\t\t}else if (a == 14){\r\n\t\t\t\t\tb = \"ShiftSynch\";\r\n\t\t\t\t}else if (a == 15){\r\n\t\t\t\t\tb = \"BSMigrate\";\r\n\t\t\t\t}else if (a == 16){\r\n\t\t\t\t\tb = \"MaintainDB\";\r\n\t\t\t\t}else if (a == 17){\r\n\t\t\t\t\tb = \"UNISMigrate\";\r\n\t\t\t\t}else if (a == 19){\r\n\t\t\t\t\tb = \"AccessLimit\";\r\n\t\t\t\t}else if (a == 20){\r\n\t\t\t\t\tb = \"FixWrongShiftClockin\";\r\n\t\t\t\t}else if (a == 21){\r\n\t\t\t\t\tb = \"MFMigrate\";\r\n\t\t\t\t}else if (a == 22){\r\n\t\t\t\t\tb = \"ASMigrate\";\r\n\t\t\t\t}else if (a == 23){\r\n\t\t\t\t\tb = \"OCMigrate\";\r\n\t\t\t\t}else if (a == 24){\r\n\t\t\t\t\tb = \"MailerAttendanceAbsence\";\r\n\t\t\t\t}else if (a == 25){\r\n\t\t\t\t\tb = \"PayrollSynch\";\r\n\t\t\t\t}else if (a == 26){\r\n\t\t\t\t\tb = \"MPMigrate\";\r\n\t\t\t\t}else if (a == 27){\r\n\t\t\t\t\tb = \"ACServer\";\r\n\t\t\t\t}else if (a == 28){\r\n\t\t\t\t\tb = \"PayMasterAttendance\";\r\n\t\t\t\t}else if (a == 29){\r\n\t\t\t\t\tb = \"UNiSynch\";\r\n\t\t\t\t}else if (a == 30){\r\n\t\t\t\t\tb = \"MIMigrate\";\r\n\t\t\t\t}else if (a == 31){\r\n\t\t\t\t\tb = \"MMMigrate\";\r\n\t\t\t\t}else if (a == 32){\r\n\t\t\t\t\tb = \"BVMigrate\";\r\n\t\t\t\t}else if (a == 33){\r\n\t\t\t\t\tb = \"MEMigrate\";\r\n\t\t\t\t}else if (a == 34){\r\n\t\t\t\t\tb = \"PayMasterAttendanceCSV\";\r\n\t\t\t\t}else if (a == 35){\r\n\t\t\t\t\tb = \"MAIMigrate\";\r\n\t\t\t\t}else if (a == 36){\r\n\t\t\t\t\tb = \"OMMigrate\";\r\n\t\t\t\t}else if (a == 37){\r\n\t\t\t\t\tb = \"MDMigrate\";\r\n\t\t\t\t}\r\n\t\t\t\tif (confirm(\"Execute Script/ Send Mailer: \"+b)){\r\n\t\t\t\t\tdocument.getElementById('bt'+a).disabled = true;\r\n\t\t\t\t\twindow.open('ExecuteScript.php?script='+b, 'ExecuteScript', 'height=300;width=400;resize=no;menubar=no;addressbar=no');\r\n\t\t\t\t}\r\n\t\t\t}else{\r\n\t\t\t\t//Pay Off\r\n\t\t\t\tdocument.getElementById('bt'+a).disabled = true;\r\n\t\t\t\twindow.open('SameDayPayOff.php', 'SamDayPayOff', 'height=300;width=300;resize=no;menubar=no;addressbar=no');\r\n\t\t\t}\r\n\t\t}\r\n\r\n\t\tfunction checkDataCOMPayroll(){\r\n\t\t\tx = document.frm;\r\n\t\t\tif (x.lstMapDataCOMPayroll.value == 'DataCOM'){\r\n\t\t\t\tif (x.txtMapTableName.value == ''){x.txtMapTableName.value = 'employees';}\r\n\t\t\t\tif (x.txtMapEID.value == ''){x.txtMapEID.value = 'id';}\r\n\t\t\t\tif (x.txtMapEName.value == ''){x.txtMapEName.value = 'first';}\r\n\t\t\t\tif (x.txtMapIDNo.value == ''){x.txtMapIDNo.value = 'sex';}\r\n\t\t\t\tif (x.txtMapDept.value == ''){x.txtMapDept.value = 'department';}\r\n\t\t\t\tif (x.txtMapDiv.value == ''){x.txtMapDiv.value = 'project';}\r\n\t\t\t\tif (x.txtMapRemark.value == ''){x.txtMapRemark.value = 'Remark';}\r\n\t\t\t\tif (x.txtMapShift.value == ''){x.txtMapShift.value = 'Shift';}\r\n\r\n\t\t\t\tif (x.txtMapPhone.value == ''){x.txtMapPhone.value = 'group_value';}\r\n\t\t\t\t//if (x.lstMapUpdateDate.value == ''){x.lstMapUpdateDate.value = 'Yes';}\r\n\t\t\t\t//if (x.lstMapUpdateSalary.value == ''){x.lstMapUpdateSalary.value = 'Exitdate';}\r\n\t\t\t\t\r\n\t\t\t\tif (x.txtMapStatus.value == ''){x.txtMapStatus.value = 'NotIncluded';}\r\n\t\t\t\tif (x.txtMapActive.value == ''){x.txtMapActive.value = '0';}\r\n\t\t\t\tif (x.txtMapPassive.value == ''){x.txtMapPassive.value = '1';}\r\n\t\t\t}else if (x.lstMapDataCOMPayroll.value == 'PayMaster'){\r\n\t\t\t\tif (x.txtMapTableName.value == ''){x.txtMapTableName.value = 'tblEmployee';}\r\n\t\t\t\tif (x.txtMapEID.value == ''){x.txtMapEID.value = 'EmpNo';}\r\n\t\t\t\tif (x.txtMapEName.value == ''){x.txtMapEName.value = 'EmpName';}\r\n\t\t\t\tif (x.txtMapIDNo.value == ''){x.txtMapIDNo.value = 'Sex';}\r\n\t\t\t\tif (x.txtMapDept.value == ''){x.txtMapDept.value = 'DepartmentCode';}\r\n\t\t\t\tif (x.txtMapDiv.value == ''){x.txtMapDiv.value = 'BranchCode';}\r\n\t\t\t\tif (x.txtMapRemark.value == ''){x.txtMapRemark.value = 'DesignationCode';}\r\n\t\t\t\tif (x.txtMapShift.value == ''){x.txtMapShift.value = 'ShiftCode';}\r\n\r\n\t\t\t\tif (x.txtMapPhone.value == ''){x.txtMapPhone.value = 'CategoryCode';}\r\n\t\t\t\t//if (x.lstMapUpdateDate.value == ''){x.lstMapUpdateDate.value = 'Yes';}\r\n\t\t\t\t//if (x.lstMapUpdateSalary.value == ''){x.lstMapUpdateSalary.value = 'Exitdate';}\r\n\t\t\t\t\r\n\t\t\t\tif (x.txtMapStatus.value == ''){x.txtMapStatus.value = 'InActive';}\r\n\t\t\t\tif (x.txtMapActive.value == ''){x.txtMapActive.value = '0';}\r\n\t\t\t\tif (x.txtMapPassive.value == ''){x.txtMapPassive.value = '1';}\r\n\t\t\t}else if (x.lstMapDataCOMPayroll.value == 'WebPayMaster'){\r\n\t\t\t\tif (x.txtMapTableName.value == ''){x.txtMapTableName.value = 'tblEmployee';}\r\n\t\t\t\tif (x.txtMapEID.value == ''){x.txtMapEID.value = 'Emp_Payroll_No';}\r\n\t\t\t\tif (x.txtMapEName.value == ''){x.txtMapEName.value = 'Emp_Name';}\r\n\t\t\t\tif (x.txtMapIDNo.value == ''){x.txtMapIDNo.value = 'Emp_Class_Id';}\r\n\t\t\t\tif (x.txtMapDept.value == ''){x.txtMapDept.value = 'Emp_Dept_Id';}\r\n\t\t\t\tif (x.txtMapDiv.value == ''){x.txtMapDiv.value = 'Emp_Location_Id';}\r\n\t\t\t\tif (x.txtMapRemark.value == ''){x.txtMapRemark.value = 'Emp_Desig_Id';}\r\n\t\t\t\tif (x.txtMapShift.value == ''){x.txtMapShift.value = 'MasterOne_Id';}\r\n\r\n\t\t\t\tif (x.txtMapPhone.value == ''){x.txtMapPhone.value = 'Category_Id';}\r\n\t\t\t\t//if (x.lstMapUpdateDate.value == ''){x.lstMapUpdateDate.value = 'Yes';}\r\n\t\t\t\t//if (x.lstMapUpdateSalary.value == ''){x.lstMapUpdateSalary.value = 'Exitdate';}\r\n\t\t\t\t\r\n\t\t\t\tif (x.txtMapStatus.value == ''){x.txtMapStatus.value = 'InActive';}\r\n\t\t\t\tif (x.txtMapActive.value == ''){x.txtMapActive.value = '0';}\r\n\t\t\t\tif (x.txtMapPassive.value == ''){x.txtMapPassive.value = '1';}\r\n\t\t\t}else if (x.lstMapDataCOMPayroll.value == 'No'){\r\n\t\t\t\tx.txtMapTableName.value = '';\r\n\t\t\t\tx.txtMapEID.value = '';\r\n\t\t\t\tx.txtMapEName.value = '';\r\n\t\t\t\tx.txtMapIDNo.value = '';\r\n\t\t\t\tx.txtMapDept.value = '';\r\n\t\t\t\tx.txtMapDiv.value = '';\r\n\t\t\t\tx.txtMapShift.value = '';\r\n\t\t\t\tx.txtMapRemark.value = '';\r\n\r\n\t\t\t\tx.txtMapPhone.value = '';\r\n\t\t\t\t\r\n\t\t\t\tx.txtMapStatus.value = '';\r\n\t\t\t\tx.txtMapActive.value = '';\r\n\t\t\t\tx.txtMapPassive.value = '';\r\n\t\t\t}\r\n\t\t}\r\n\t</script>";
                print "</div>";
                include 'footer.php';
                ?>