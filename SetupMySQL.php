<?php
ob_start("ob_gzhandler");
$conn = mysqli_connect("localhost", "root", "root", "");
$query = "CREATE USER 'fdmsusr'@'%' IDENTIFIED BY 'fdmsamho'";
mysqli_query($conn, $query);
$query = "GRANT USAGE ON * . * TO 'fdmsusr'@'%' IDENTIFIED BY 'fdmsamho' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0";
mysqli_query($conn, $query);
$query = "CREATE USER 'fdmsusr'@'localhost' IDENTIFIED BY 'fdmsamho'";
mysqli_query($conn, $query);
$query = "GRANT USAGE ON * . * TO 'fdmsusr'@'localhost' IDENTIFIED BY 'fdmsamho' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0";
mysqli_query($conn, $query);
$query = "CREATE DATABASE Access";
mysqli_query($conn, $query);
mysqli_select_db($conn, "Access");

$query ="CREATE TABLE `Access`.`licensehistory` (
  `CoCode` varchar(100) NOT NULL,
  `LHistoryId` int(11) NOT NULL AUTO_INCREMENT,
  `LHistoryType` int(11) NOT NULL,
  `LHistoryMachineKey` varchar(255) NOT NULL,
  `LHistoryDetail` varchar(1000) NOT NULL,
  `CSysDate` varchar(30) NOT NULL,
  `Login_C_Id` varchar(50) NOT NULL,
  `SerialNo` varchar(100) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `MailId` varchar(100) NOT NULL,
  `ContactPerson` varchar(100) NOT NULL,
  PRIMARY KEY (`LHistoryId`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE IF NOT EXISTS `wagesmaster` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Category` varchar(45) NOT NULL,
  `Blgroup` varchar(10) NOT NULL,
  `MonFri` varchar(45) DEFAULT NULL,
  `Sat` varchar(45) DEFAULT NULL,
  `Sun` varchar(45) DEFAULT NULL,
  `PH` varchar(45) DEFAULT NULL,
  `WK` varchar(45) DEFAULT NULL,
  `Monthly` varchar(45) DEFAULT NULL,
  `ShiftId` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`ID`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`accessflag` (
  `TLSFlagID` int(10) NOT NULL AUTO_INCREMENT,
  `Violet` varchar(5) DEFAULT 'Yes',
  `Indigo` varchar(5) DEFAULT 'Yes',
  `Blue` varchar(5) DEFAULT 'Yes',
  `Green` varchar(5) DEFAULT 'Yes',
  `Yellow` varchar(5) DEFAULT 'Yes',
  `Orange` varchar(5) DEFAULT 'Yes',
  `Red` varchar(5) DEFAULT 'Yes',
  `Gray` varchar(5) DEFAULT 'Yes',
  `Brown` varchar(5) DEFAULT 'Yes',
  `Purple` varchar(5) DEFAULT 'Yes',
  `Black` varchar(5) DEFAULT 'Yes',
  `Proxy` varchar(5) DEFAULT 'Yes',
  `Magenta` varchar(5) DEFAULT 'Yes',
  `Teal` varchar(5) DEFAULT 'Yes',
  `Aqua` varchar(5) DEFAULT 'Yes',
  `Safron` varchar(5) DEFAULT 'Yes',
  `Amber` varchar(5) DEFAULT 'Yes',
  `Gold` varchar(5) DEFAULT 'Yes',
  `Vermilion` varchar(5) DEFAULT 'Yes',
  `Silver` varchar(5) DEFAULT 'Yes',
  `Maroon` varchar(5) DEFAULT 'Yes',
  `Pink` varchar(5) DEFAULT 'Yes',
  PRIMARY KEY (`TLSFlagID`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`adalog` (
  `LogID` int(11) NOT NULL AUTO_INCREMENT,
  `e_id` int(11) DEFAULT NULL,
  `DateFrom` int(8) DEFAULT NULL,
  `DateTo` int(8) DEFAULT NULL,
  PRIMARY KEY (`LogID`),
  UNIQUE KEY `EDF` (`e_id`,`DateFrom`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`alterlog` (
  `LogID` int(10) NOT NULL AUTO_INCREMENT,
  `Username` varchar(50) DEFAULT NULL,
  `ed` int(10) NOT NULL DEFAULT '0',
  `DateFrom` varchar(8) DEFAULT NULL,
  `TimeFrom` varchar(6) DEFAULT NULL,
  `GateFrom` int(10) NOT NULL DEFAULT '0',
  `DateTo` varchar(8) DEFAULT NULL,
  `TimeTo` varchar(6) DEFAULT NULL,
  `GateTo` int(10) NOT NULL DEFAULT '0',
  `TransactDate` int(10) NOT NULL DEFAULT '0',
  `ShiftFrom` int(10) NOT NULL DEFAULT '0',
  `ShiftTo` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`LogID`),
  KEY `ed` (`ed`),
  KEY `Username` (`Username`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`attendancemaster` (
  `AttendanceID` int(10) NOT NULL AUTO_INCREMENT,
  `EmployeeID` int(10) NOT NULL DEFAULT '0',
  `EmpID` varchar(10) DEFAULT NULL,
  `group_id` int(10) NOT NULL DEFAULT '0',
  `group_min` int(10) NOT NULL DEFAULT '0',
  `ADate` int(10) NOT NULL DEFAULT '0',
  `Week` int(10) NOT NULL DEFAULT '0',
  `EarlyIn` int(10) NOT NULL DEFAULT '0',
  `LateIn` int(10) NOT NULL DEFAULT '0',
  `Break` int(10) NOT NULL DEFAULT '0',
  `LessBreak` int(10) NOT NULL DEFAULT '0',
  `MoreBreak` int(10) NOT NULL DEFAULT '0',
  `EarlyOut` int(10) NOT NULL DEFAULT '0',
  `LateOut` int(10) NOT NULL DEFAULT '0',
  `Normal` int(10) NOT NULL DEFAULT '0',
  `Grace` int(10) NOT NULL DEFAULT '0',
  `Overtime` int(10) NOT NULL DEFAULT '0',
  `AOvertime` int(10) NOT NULL DEFAULT '0',
  `Day` varchar(50) DEFAULT NULL,
  `Flag` varchar(10) NOT NULL DEFAULT 'Black',
  `p_flag` int(11) NOT NULL DEFAULT '0',
  `LateIn_flag` int(1) NOT NULL DEFAULT '0',
  `EarlyOut_flag` int(1) NOT NULL DEFAULT '0',
  `MoreBreak_flag` int(1) NOT NULL DEFAULT '0',
  `OT1` varchar(255) NOT NULL DEFAULT 'Saturday',
  `OT2` varchar(255) NOT NULL DEFAULT 'Sunday',
  `NightFlag` int(11) NOT NULL DEFAULT '0',
  `RotateFlag` int(11) NOT NULL DEFAULT '0',
  `Remark` varchar(1024) DEFAULT NULL,
  `PHF` int(1) NOT NULL DEFAULT '0',
  `EarlyIn_flag` int(1) DEFAULT '0',
  `LateInColumn` int(11) DEFAULT '0',
  PRIMARY KEY (`AttendanceID`),
  UNIQUE KEY `AEA` (`EmployeeID`,`ADate`),
  KEY `g_id` (`group_id`),
  KEY `EmployeeID` (`EmployeeID`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`cag` (
  `CAGID` int(10) NOT NULL AUTO_INCREMENT,
  `CAGDate` int(8) DEFAULT '0',
  `Name` varchar(255) DEFAULT NULL,
  `CAGType` varchar(5) DEFAULT NULL,
  `Days` int(10) DEFAULT '0',
  `DateFrom` int(10) DEFAULT '0',
  `DateTo` int(10) DEFAULT '0',
  PRIMARY KEY (`CAGID`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`cagrotation` (
  `CAGRID` int(10) NOT NULL AUTO_INCREMENT,
  `CAGID` int(10) DEFAULT NULL,
  `e_date` int(10) DEFAULT NULL,
  `RecStat` int(1) DEFAULT '0',
  `group_id` int(10) DEFAULT '2',
  PRIMARY KEY (`CAGRID`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`cardholder` (
  `FTItemID` int(11) NOT NULL,
  `FirstName` varchar(255) NOT NULL,
  `LastName` varchar(255) NOT NULL,
  `Authorised` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`FTItemID`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`daymaster` (
  `DayMasterID` int(10) NOT NULL AUTO_INCREMENT,
  `e_id` int(10) NOT NULL DEFAULT '0',
  `TDate` int(10) NOT NULL DEFAULT '0',
  `Entry` varchar(6) DEFAULT NULL,
  `Start` varchar(6) DEFAULT NULL,
  `BreakOut` varchar(6) DEFAULT NULL,
  `BreakIn` varchar(6) DEFAULT NULL,
  `Close` varchar(6) DEFAULT NULL,
  `Exit` varchar(6) DEFAULT NULL,
  `p_flag` int(11) NOT NULL DEFAULT '0',
  `group_id` int(10) NOT NULL DEFAULT '0',
  `Flag` varchar(10) NOT NULL DEFAULT 'Black',
  `Work` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`DayMasterID`),
  UNIQUE KEY `DET` (`e_id`,`TDate`),
  KEY `e_id` (`e_id`),
  KEY `p_flag` (`p_flag`),
  KEY `group_id` (`group_id`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`deptgate` (
  `DeptGateID` int(10) NOT NULL AUTO_INCREMENT,
  `dept` varchar(255) DEFAULT NULL,
  `g_id` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`DeptGateID`),
  KEY `dept` (`dept`),
  KEY `id` (`g_id`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`drilldept` (
  `DrillID` int(11) NOT NULL AUTO_INCREMENT,
  `DrillMasterID` int(11) NOT NULL,
  `Dept` varchar(255) NOT NULL,
  PRIMARY KEY (`DrillID`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`drilldiv` (
  `DrillID` int(11) NOT NULL AUTO_INCREMENT,
  `DrillMasterID` int(11) NOT NULL,
  `Div` varchar(255) NOT NULL,
  PRIMARY KEY (`DrillID`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`drillidno` (
  `DrillID` int(11) NOT NULL AUTO_INCREMENT,
  `DrillMasterID` int(11) NOT NULL,
  `IDNo` varchar(255) NOT NULL,
  PRIMARY KEY (`DrillID`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`drillmaster` (
  `DrillMasterID` int(11) NOT NULL AUTO_INCREMENT,
  `DrillDate` int(8) NOT NULL,
  `DrillTimeFrom` varchar(6) NOT NULL,
  `DrillTimeTo` varchar(6) NOT NULL,
  PRIMARY KEY (`DrillMasterID`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`drillphone` (
  `DrillID` int(11) NOT NULL AUTO_INCREMENT,
  `DrillMasterID` int(11) NOT NULL,
  `Phone` varchar(255) NOT NULL,
  PRIMARY KEY (`DrillID`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`drillremark` (
  `DrillID` int(11) NOT NULL AUTO_INCREMENT,
  `DrillMasterID` int(11) NOT NULL,
  `Remark` varchar(255) NOT NULL,
  PRIMARY KEY (`DrillID`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`drillterminal` (
  `DrillID` int(11) NOT NULL AUTO_INCREMENT,
  `DrillMasterID` int(11) NOT NULL,
  `g_id` int(11) NOT NULL,
  PRIMARY KEY (`DrillID`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`employeeflag` (
  `EmployeeFlagID` int(11) NOT NULL AUTO_INCREMENT,
  `EmployeeID` int(11) NOT NULL,
  `Violet` int(5) NOT NULL DEFAULT '365',
  `Indigo` int(5) NOT NULL DEFAULT '365',
  `Blue` int(5) NOT NULL DEFAULT '365',
  `Green` int(5) NOT NULL DEFAULT '365',
  `Yellow` int(5) NOT NULL DEFAULT '365',
  `Orange` int(5) NOT NULL DEFAULT '365',
  `Red` int(5) NOT NULL DEFAULT '365',
  `Gray` int(5) NOT NULL DEFAULT '365',
  `Brown` int(5) NOT NULL DEFAULT '365',
  `Purple` int(5) NOT NULL DEFAULT '365',
  `Magenta` int(5) DEFAULT '365',
  `Teal` int(5) DEFAULT '365',
  `Aqua` int(5) DEFAULT '365',
  `Safron` int(5) DEFAULT '365',
  `Amber` int(5) DEFAULT '365',
  `Gold` int(5) DEFAULT '365',
  `Vermilion` int(5) DEFAULT '365',
  `Silver` int(5) DEFAULT '365',
  `Maroon` int(5) DEFAULT '365',
  `Pink` int(5) DEFAULT '365',
  PRIMARY KEY (`EmployeeFlagID`),
  UNIQUE KEY `EFEID` (`EmployeeID`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`event` (
  `ID` bigint(20) NOT NULL,
  `OccurrenceDate` int(8) NOT NULL,
  `OccurrenceTime` varchar(6) NOT NULL,
  `EventType` int(11) NOT NULL DEFAULT '0',
  `DivisionID` int(11) NOT NULL DEFAULT '0',
  `e_ID` int(11) NOT NULL DEFAULT '0',
  `g_ID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`flagapplication` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `DateFrom` int(8) DEFAULT '0',
  `DateTo` int(8) DEFAULT '0',
  `e_id` int(11) DEFAULT '0',
  `Flag` varchar(255) DEFAULT NULL,
  `A1` int(1) DEFAULT '0' COMMENT 'Add Rights',
  `A2` int(1) DEFAULT '0' COMMENT 'Edit Rights',
  `A3` int(1) DEFAULT '0' COMMENT 'Delete Rights',
  `Remark` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `EFT` (`e_id`,`DateFrom`,`DateTo`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`flagdayrotation` (
  `FlagDayRotationID` int(11) NOT NULL AUTO_INCREMENT,
  `e_id` int(11) NOT NULL DEFAULT '0',
  `e_date` int(8) NOT NULL DEFAULT '0',
  `g_id` int(11) NOT NULL DEFAULT '0',
  `Flag` varchar(1024) NOT NULL DEFAULT 'Black',
  `Rotate` int(1) NOT NULL DEFAULT '0',
  `RecStat` int(1) NOT NULL DEFAULT '0',
  `Remark` varchar(1024) NOT NULL DEFAULT '.',
  `OT1` int(11) DEFAULT NULL,
  `OT2` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT '2',
  `OT` varchar(5) DEFAULT NULL,
  `OTH` int(11) DEFAULT '0',
  PRIMARY KEY (`FlagDayRotationID`),
  UNIQUE KEY `FDRED` (`e_id`,`e_date`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`flagtitle` (
  `FlagTitleID` int(11) NOT NULL AUTO_INCREMENT,
  `Flag` varchar(255) NOT NULL,
  `Title` varchar(255) DEFAULT NULL,
  `FlagLink` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`FlagTitleID`),
  UNIQUE KEY `FTF` (`Flag`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`ftitem` (
  `ID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Description` varchar(255) NOT NULL DEFAULT '.',
  `DivisionID` int(11) NOT NULL,
  `TypeID` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`groupdept` (
  `GroupDeptID` int(11) NOT NULL AUTO_INCREMENT,
  `GroupID` int(11) NOT NULL DEFAULT '0',
  `Dept` varchar(255) NOT NULL DEFAULT '.',
  PRIMARY KEY (`GroupDeptID`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`groupdiv` (
  `GroupDivID` int(11) NOT NULL AUTO_INCREMENT,
  `GroupID` int(11) NOT NULL DEFAULT '0',
  `Div` varchar(255) NOT NULL DEFAULT '.',
  PRIMARY KEY (`GroupDivID`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`groupexempt` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Module` varchar(5) NOT NULL DEFAULT '.',
  `Grp` varchar(255) NOT NULL DEFAULT '.',
  `Val` varchar(255) NOT NULL DEFAULT '.',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `MGV` (`Module`,`Grp`,`Val`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`groupflaglimit` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Grp` varchar(255) NOT NULL DEFAULT '.',
  `Val` varchar(255) NOT NULL DEFAULT '.',
  `Violet` int(5) NOT NULL DEFAULT '365',
  `Indigo` int(5) NOT NULL DEFAULT '365',
  `Blue` int(5) NOT NULL DEFAULT '365',
  `Green` int(5) NOT NULL DEFAULT '365',
  `Yellow` int(5) NOT NULL DEFAULT '365',
  `Orange` int(5) NOT NULL DEFAULT '365',
  `Red` int(5) NOT NULL DEFAULT '365',
  `Gray` int(5) NOT NULL DEFAULT '365',
  `Brown` int(5) NOT NULL DEFAULT '365',
  `Purple` int(5) NOT NULL DEFAULT '365',
  `Magenta` int(5) DEFAULT '365',
  `Teal` int(5) DEFAULT '365',
  `Aqua` int(5) DEFAULT '365',
  `Safron` int(5) DEFAULT '365',
  `Amber` int(5) DEFAULT '365',
  `Gold` int(5) DEFAULT '365',
  `Vermilion` int(5) DEFAULT '365',
  `Silver` int(5) DEFAULT '365',
  `Maroon` int(5) DEFAULT '365',
  `Pink` int(5) DEFAULT '365',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `GV` (`Grp`,`Val`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`groupidno` (
  `GroupIdNoID` int(11) NOT NULL AUTO_INCREMENT,
  `GroupID` int(11) NOT NULL DEFAULT '0',
  `IdNo` varchar(255) NOT NULL DEFAULT '.',
  PRIMARY KEY (`GroupIdNoID`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`groupmaster` (
  `GroupID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL DEFAULT '.',
  PRIMARY KEY (`GroupID`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`groupphone` (
  `GroupPhoneID` int(11) NOT NULL AUTO_INCREMENT,
  `GroupID` int(11) NOT NULL DEFAULT '0',
  `Phone` varchar(255) NOT NULL DEFAULT '.',
  PRIMARY KEY (`GroupPhoneID`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`groupremark` (
  `GroupRemarkID` int(11) NOT NULL AUTO_INCREMENT,
  `GroupID` int(11) NOT NULL DEFAULT '0',
  `Remark` varchar(255) NOT NULL DEFAULT '.',
  PRIMARY KEY (`GroupRemarkID`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`groupshift` (
  `GroupShiftID` int(11) NOT NULL AUTO_INCREMENT,
  `GroupID` int(11) NOT NULL DEFAULT '0',
  `Shift` varchar(255) NOT NULL DEFAULT '.',
  PRIMARY KEY (`GroupShiftID`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`groupyearflaglimit` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Grp` varchar(255) DEFAULT NULL,
  `Val` varchar(255) DEFAULT NULL,
  `Years` int(3) DEFAULT '0',
  `Violet` int(5) DEFAULT '365',
  `Indigo` int(5) DEFAULT '365',
  `Blue` int(5) DEFAULT '365',
  `Green` int(5) DEFAULT '365',
  `Yellow` int(5) DEFAULT '365',
  `Orange` int(5) DEFAULT '365',
  `Red` int(5) DEFAULT '365',
  `Gray` int(5) DEFAULT '365',
  `Brown` int(5) DEFAULT '365',
  `Purple` int(5) DEFAULT '365',
  `Magenta` int(5) DEFAULT '365',
  `Teal` int(5) DEFAULT '365',
  `Aqua` int(5) DEFAULT '365',
  `Safron` int(5) DEFAULT '365',
  `Amber` int(5) DEFAULT '365',
  `Gold` int(5) DEFAULT '365',
  `Vermilion` int(5) DEFAULT '365',
  `Silver` int(5) DEFAULT '365',
  `Maroon` int(5) DEFAULT '365',
  `Pink` int(5) DEFAULT '365',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `GV` (`Grp`,`Val`,`Years`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`logmaster` (
  `LogID` int(10) NOT NULL AUTO_INCREMENT,
  `LogDate` int(10) NOT NULL DEFAULT '0',
  `LogTime` varchar(6) DEFAULT NULL,
  `ed` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`LogID`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`mailertext` (
  `MailerTextID` int(11) NOT NULL AUTO_INCREMENT,
  `MailerType` varchar(255) NOT NULL,
  `MailerText` varchar(1024) NOT NULL,
  PRIMARY KEY (`MailerTextID`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`mealmaster` (
  `MealMasterID` int(11) NOT NULL AUTO_INCREMENT,
  `MealSlot` varchar(255) NOT NULL,
  `MealTimeFrom` varchar(6) NOT NULL,
  `MealTimeTo` varchar(6) NOT NULL,
  PRIMARY KEY (`MealMasterID`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`migratemaster` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Col` varchar(255) DEFAULT NULL,
  `Val` varchar(255) DEFAULT NULL,
  `DateFrom` int(8) DEFAULT NULL,
  `DateTo` int(8) DEFAULT NULL,
  `MonthNo` int(5) DEFAULT '0',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `CV` (`Col`,`Val`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`modulemaster` (
  `ModuleID` int(10) NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ModuleID`),
  UNIQUE KEY `MN` (`Name`),
  UNIQUE KEY `Name` (`Name`)
)";
mysqli_query($conn, $query);

$query = "INSERT INTO ModuleMaster (ModuleID, Name) VALUES ('9', 'Approve Overtime'), ('4', 'Assign Shifts'), ('3', 'Assign Terminals'), ('2', 'Exit Terminals'), ('5', 'Other Settings'), ('15', 'Post Flag Days'), ('10', 'Pre Approve Overtime'), ('14', 'Pre Flag Days'), ('8', 'Project Assignment'), ('11', 'Proxy'), ('1', 'Shifts'), ('7', 'Time Alteration'), ('6', 'Users')";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`non_work_sat` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `OTDate` int(8) NOT NULL DEFAULT '0',
  `Day` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`otdate` (
  `OTDateID` int(11) NOT NULL AUTO_INCREMENT,
  `OTDate` int(8) NOT NULL DEFAULT '0',
  `Day` varchar(255) NOT NULL,
  PRIMARY KEY (`OTDateID`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`otday` (
  `OTDayID` int(11) NOT NULL AUTO_INCREMENT,
  `Day` varchar(255) NOT NULL,
  `OT` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`OTDayID`),
  UNIQUE KEY `OD` (`Day`),
  UNIQUE KEY `Day` (`Day`)
)";
mysqli_query($conn, $query);

$query = "INSERT IGNORE INTO Access.OTDay (Day, OT) VALUES ('Monday', '0'), ('Tuesday', '0'), ('Wednessday', '0'), ('Thursday', '0'), ('Friday', '0'), ('Saturday', '0'), ('Sunday', '0')";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`otdayrotation` (
  `OTDayRotationID` int(11) NOT NULL AUTO_INCREMENT,
  `e_id` int(11) NOT NULL DEFAULT '0',
  `e_date` int(8) NOT NULL DEFAULT '0',
  PRIMARY KEY (`OTDayRotationID`),
  UNIQUE KEY `ODREID` (`e_id`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`otemployeedateexempt` (
  `OTEmployeeDateExemptID` int(11) NOT NULL AUTO_INCREMENT,
  `EmployeeID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`OTEmployeeDateExemptID`),
  UNIQUE KEY `OEEDEI` (`EmployeeID`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`otemployeeexempt` (
  `OTEmployeeExemptID` int(11) NOT NULL AUTO_INCREMENT,
  `EmployeeID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`OTEmployeeExemptID`),
  UNIQUE KEY `OEEEI` (`EmployeeID`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`othersettingmaster` (
  `SettingID` int(10) NOT NULL AUTO_INCREMENT,
  `MinClockinPeriod` int(10) NOT NULL DEFAULT '0',
  `TotalDailyClockin` int(10) NOT NULL DEFAULT '0',
  `ExitTerminal` varchar(50) DEFAULT 'No',
  `Project` varchar(50) DEFAULT 'No',
  `FlagLimitType` varchar(255) DEFAULT NULL,
  `LessLunchOT` varchar(50) DEFAULT 'No',
  `NightShiftMaxOutTime` int(10) NOT NULL DEFAULT '0',
  `TotalExitClockin` int(10) NOT NULL DEFAULT '0',
  `NoExitException` varchar(50) DEFAULT 'No',
  `NoBreakException` varchar(5) DEFAULT 'Yes',
  `CompanyName` varchar(255) DEFAULT NULL,
  `CompanyDetail1` varchar(255) DEFAULT NULL,
  `CompanyDetail2` varchar(255) DEFAULT NULL,
  `RotateShift` varchar(5) DEFAULT 'No',
  `RotateShiftNextDay` int(10) NOT NULL DEFAULT '0',
  `IDColumnName` varchar(255) NOT NULL DEFAULT 'Sex',
  `RosterColumns` varchar(1024) NULL COMMENT 'Roster Report Column Selection',
  `Ex1` varchar(255) DEFAULT NULL,
  `Ex2` varchar(255) DEFAULT NULL,
  `Ex3` int(10) NOT NULL DEFAULT '0',
  `Ex4` int(10) NOT NULL DEFAULT '0',
  `PLFlag` varchar(255) NOT NULL DEFAULT 'Black',
  `LockDate` int(8) NOT NULL DEFAULT '20210101',
  `MACAddress` varchar(255) NOT NULL DEFAULT '.',
  `EarlyInOTDayDate` varchar(50) NOT NULL DEFAULT 'No',
  `SMTPServer` varchar(255) NOT NULL DEFAULT '.',
  `SMTPFrom` varchar(255) NOT NULL DEFAULT '.',
  `SMTPAuth` varchar(5) NOT NULL DEFAULT '.',
  `SMTPUsername` varchar(255) NOT NULL DEFAULT '.',
  `SMTPPassword` varchar(255) NOT NULL DEFAULT '.',
  `MinOTValue` int(11) NOT NULL DEFAULT '0',
  `DBType` varchar(255) NOT NULL DEFAULT 'Oracle',
  `DBIP` varchar(255) NOT NULL DEFAULT '127.0.0.1',
  `DBName` varchar(255) NOT NULL DEFAULT '.',
  `DBUser` varchar(255) NOT NULL DEFAULT '.',
  `DBPass` varchar(255) NOT NULL DEFAULT '.',
  `EmployeeCodeLength` int(11) NOT NULL DEFAULT '6',
  `PhoneColumnName` varchar(255) NOT NULL DEFAULT '--',
  `OTDateBalNHrs` int(11) NOT NULL DEFAULT '0',
  `LocationSynchShift` varchar(10) NOT NULL DEFAULT 'Server',
  `TCount` varchar(255) NOT NULL DEFAULT '.',
  `ApproveOTIgnoreActual` varchar(5) NOT NULL DEFAULT 'No',
  `AutoAssignTerminal` varchar(5) NOT NULL DEFAULT 'Yes',
  `AutoApproveOT` varchar(5) NOT NULL DEFAULT 'No',
  `MaxOTValue` int(11) NOT NULL DEFAULT '1440',
  `RoundOffAOT` varchar(5) NOT NULL DEFAULT 'None',
  `MoveNS` varchar(5) NOT NULL DEFAULT 'No',
  `UseShiftRoster` varchar(5) NOT NULL DEFAULT 'No',
  `SRDay` varchar(15) NOT NULL DEFAULT 'None',
  `SRScenario` varchar(255) NOT NULL DEFAULT 'None',
  `PreApproveOTValue` varchar(25) NOT NULL DEFAULT 'Lower Value',
  `MealCouponPrinterName` varchar(255) NOT NULL DEFAULT '.',
  `MealCouponFont` varchar(5) NOT NULL DEFAULT '80',
  `LateDays` int(2) NOT NULL DEFAULT '0',
  `AutoResetOT12` varchar(3) DEFAULT 'No',
  `ClientLogo` varchar(255) DEFAULT NULL,
  `SanSatOT` varchar(5) DEFAULT 'No',
  `F1` varchar(255) DEFAULT NULL,
  `F2` varchar(255) DEFAULT NULL,
  `F3` varchar(255) DEFAULT NULL,
  `F4` varchar(255) DEFAULT NULL,
  `F5` varchar(255) DEFAULT NULL,
  `F6` varchar(255) DEFAULT NULL,
  `F7` varchar(255) DEFAULT NULL,
  `F8` varchar(255) DEFAULT NULL,
  `F9` varchar(255) DEFAULT NULL,
  `F10` varchar(255) DEFAULT NULL,
  `EmployeeEmailField` varchar(5) DEFAULT NULL,
  `EmployeeSMSField` varchar(5) DEFAULT NULL,
  `SMTPPort` varchar(5) DEFAULT NULL,
  `SMTPSSL` varchar(5) DEFAULT NULL,
  `DivColumnName` varchar(255) NOT NULL DEFAULT 'Div',
  `RemarkColumnName` varchar(255) NOT NULL DEFAULT 'Rmk',
  `CAGR` varchar(5) DEFAULT 'No',
  `CompanyDetail3` enum('IN<3','JN<3') DEFAULT NULL,
  `CompanyDetail4` varchar(1000) NOT NULL DEFAULT '.',
  `nwsprx` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`SettingID`),
  KEY `IDColumnName` (`IDColumnName`)
)";
mysqli_query($conn, $query);

$query = "INSERT INTO Access.OtherSettingMaster (CompanyName, CompanyDetail1, CompanyDetail2) VALUES ('Un Registered', '', '')";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`pax` (
  `id` int(11) NOT NULL DEFAULT '0',
  `N1` varchar(10) DEFAULT NULL,
  `N2` varchar(10) DEFAULT NULL,
  `N3` varchar(10) DEFAULT NULL,
  `N4` varchar(10) DEFAULT NULL,
  `N5` varchar(10) DEFAULT NULL,
  `N6` varchar(10) DEFAULT NULL,
  `N7` varchar(10) DEFAULT NULL,
  `N8` varchar(10) DEFAULT NULL,
  `N9` varchar(10) DEFAULT NULL,
  `N0` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`payrollmap` (
  `PayrollMapID` int(11) NOT NULL AUTO_INCREMENT,
  `TableName` varchar(255) NOT NULL DEFAULT '.',
  `Overwrite` varchar(255) NOT NULL DEFAULT 'Payroll DB',
  `EID` varchar(255) NOT NULL DEFAULT '.',
  `EName` varchar(255) NOT NULL DEFAULT '.',
  `IDNo` varchar(255) NOT NULL DEFAULT '.',
  `Dept` varchar(255) NOT NULL DEFAULT '.',
  `Division` varchar(255) NOT NULL DEFAULT '.',
  `Remark` varchar(255) NOT NULL DEFAULT '.',
  `Shift` varchar(255) NOT NULL DEFAULT '.',
  `Phone` varchar(255) NOT NULL DEFAULT '.',
  `Status` varchar(255) NOT NULL DEFAULT '.',
  `ActiveValue` varchar(255) NOT NULL DEFAULT '.',
  `PassiveValue` varchar(255) NOT NULL DEFAULT '.',
  `DataCOMPayroll` varchar(50) NOT NULL DEFAULT 'No',
  `UpdateDate` varchar(5) NOT NULL DEFAULT 'No',
  `UpdateSalary` varchar(5) NOT NULL DEFAULT 'No',
  `Project` varchar(50) NOT NULL DEFAULT '',
  `CostCentre` varchar(50) NOT NULL DEFAULT '',
  `F1` varchar(255) DEFAULT NULL,
  `F2` varchar(255) DEFAULT NULL,
  `F3` varchar(255) DEFAULT NULL,
  `F4` varchar(255) DEFAULT NULL,
  `F5` varchar(255) DEFAULT NULL,
  `F6` varchar(255) DEFAULT NULL,
  `F7` varchar(255) DEFAULT NULL,
  `F8` varchar(255) DEFAULT NULL,
  `F9` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`PayrollMapID`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`preapproveot` (
  `PreApproveOTID` int(11) NOT NULL AUTO_INCREMENT,
  `OTDate` int(8) NOT NULL DEFAULT '0',
  `e_id` int(11) NOT NULL DEFAULT '0',
  `OT` int(11) NOT NULL DEFAULT '0',
  `A1` int(1) NOT NULL DEFAULT '0' COMMENT 'Add Rights',
  `A2` int(1) NOT NULL DEFAULT '0' COMMENT 'Edit Rights',
  `A3` int(1) NOT NULL DEFAULT '0' COMMENT 'Delete Rights',
  `Remark` varchar(255) NOT NULL DEFAULT '.',
  PRIMARY KEY (`PreApproveOTID`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`processlog` (
  `ProcessID` int(10) NOT NULL AUTO_INCREMENT,
  `PType` varchar(255) DEFAULT NULL,
  `PDate` int(10) NOT NULL DEFAULT '0',
  `PTime` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`ProcessID`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`projectlog` (
  `ProjectLogID` int(10) NOT NULL AUTO_INCREMENT,
  `DayMasterID` int(10) NOT NULL DEFAULT '0',
  `WeekMasterID` int(10) NOT NULL DEFAULT '0',
  `ProjectID` int(10) NOT NULL DEFAULT '0',
  `e_id` int(10) NOT NULL DEFAULT '0',
  `e_date` int(10) NOT NULL DEFAULT '0',
  `tfrom` varchar(50) DEFAULT NULL,
  `tto` varchar(50) DEFAULT NULL,
  `twork` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ProjectLogID`),
  KEY `DayMasterID` (`DayMasterID`),
  KEY `e_ID` (`e_id`),
  KEY `ProjectID` (`ProjectID`),
  KEY `WeekMasterID` (`WeekMasterID`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`projectmaster` (
  `ProjectID` int(10) NOT NULL AUTO_INCREMENT,
  `Code` varchar(255) DEFAULT NULL,
  `Name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ProjectID`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`proxydelete` (
  `ProxyDeleteID` int(10) NOT NULL AUTO_INCREMENT,
  `e_id` int(10) NOT NULL DEFAULT '0',
  `e_date` int(10) NOT NULL DEFAULT '0',
  `e_time` varchar(6) DEFAULT NULL,
  `group_id` int(10) NOT NULL DEFAULT '0',
  `g_id` int(10) NOT NULL DEFAULT '0',
  `ed` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ProxyDeleteID`),
  KEY `e_id` (`e_id`),
  KEY `ed` (`ed`),
  KEY `g_id` (`g_id`),
  KEY `group_id` (`group_id`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`proxyemployeeexempt` (
  `ProxyEmployeeExemptID` int(11) NOT NULL AUTO_INCREMENT,
  `EmployeeID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ProxyEmployeeExemptID`),
  UNIQUE KEY `PEEI` (`EmployeeID`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`relateditems` (
  `EventID` bigint(20) NOT NULL DEFAULT '0',
  `FTItemID` int(11) NOT NULL DEFAULT '0',
  `RelationCode` smallint(6) NOT NULL DEFAULT '0',
  UNIQUE KEY `EventID_2` (`EventID`,`FTItemID`),
  KEY `EventID` (`EventID`),
  KEY `FTItemID` (`FTItemID`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`sanitationdate` (
  `OTDateID` int(11) NOT NULL AUTO_INCREMENT,
  `OTDate` int(8) NOT NULL DEFAULT '0',
  PRIMARY KEY (`OTDateID`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`schedulemaster` (
  `ScheduleID` int(10) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ScheduleID`)
)";
mysqli_query($conn, $query);

$query = "INSERT INTO Access.ScheduleMaster (ScheduleID, Name) VALUES (1, 'Fixed Start-End, Flexi Multi Break (%2)'), (2, 'Fixed Start-End, Flexi Single Break (2/4)'), (3, 'Fixed Start-End, Fixed Break (2/4)'), (4, 'Flexi Start-End, No Break (%2)'), (5, 'Fixed Start-End, Multi In-Out (>1)')";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`shiftchangemaster` (
  `ShiftChangeID` int(10) NOT NULL AUTO_INCREMENT,
  `id` int(10) NOT NULL DEFAULT '0',
  `idf` int(10) DEFAULT '1',
  `AE` int(1) NOT NULL DEFAULT '0' COMMENT 'Auto Execute Shift Rotation',
  `SRDay` varchar(10) NOT NULL DEFAULT 'None',
  `SRScenario` varchar(255) NOT NULL DEFAULT 'None',
  `RotateShiftNextDay` int(8) NOT NULL DEFAULT '0',
  `RTime` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`ShiftChangeID`),
  KEY `id` (`id`),
  KEY `idf` (`idf`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`shiftroster` (
  `ShiftRosterID` int(11) NOT NULL AUTO_INCREMENT,
  `e_id` int(11) NOT NULL DEFAULT '0',
  `e_date` int(11) NOT NULL DEFAULT '0',
  `e_group` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ShiftRosterID`),
  UNIQUE KEY `ShiftRoster` (`e_id`,`e_date`,`e_group`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`shiftrotatelog` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `RDate` int(10) NOT NULL DEFAULT '0',
  `RTime` int(10) NOT NULL DEFAULT '0',
  `ShiftFrom` int(10) NOT NULL DEFAULT '0',
  `ShiftTo` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`shifttypemaster` (
  `ShiftTypeID` int(10) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ShiftTypeID`),
  UNIQUE KEY `SN` (`Name`),
  UNIQUE KEY `Name` (`Name`)
)";
mysqli_query($conn, $query);

$query = "INSERT INTO Access.ShiftTypeMaster (Name) VALUES ('Daily'), ('Weekly')";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`sra` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `e_id` int(11) NOT NULL DEFAULT '0',
  `e_group` int(11) NOT NULL DEFAULT '0',
  `gFrom` datetime DEFAULT NULL,
  `gTo` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `EEGG` (`e_id`,`e_group`,`gFrom`,`gTo`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`taskmaster` (
  `TaskID` int(11) NOT NULL AUTO_INCREMENT,
  `Username` varchar(255) DEFAULT NULL,
  `Task` varchar(1024) DEFAULT NULL,
  `TDate` int(8) DEFAULT NULL,
  `EmployeeID` int(11) DEFAULT NULL,
  `Schedule` varchar(3) DEFAULT NULL,
  `Status` int(11) DEFAULT NULL,
  `Importance` varchar(1) DEFAULT NULL,
  `Type` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`TaskID`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`tauditlog` (
  `seqno` int(10) NOT NULL AUTO_INCREMENT,
  `id` int(10) NOT NULL DEFAULT '0',
  `idtype` varchar(1) DEFAULT NULL,
  `rid` int(10) NOT NULL DEFAULT '0',
  `menu` varchar(1) DEFAULT NULL,
  `mode` varchar(1) DEFAULT NULL,
  `rdate` varchar(12) DEFAULT NULL,
  PRIMARY KEY (`seqno`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`tcommand` (
  `c_regtime` varchar(14) DEFAULT NULL,
  `c_key` int(10) NOT NULL DEFAULT '0',
  `c_type` varchar(1) DEFAULT NULL,
  `c_gid` int(10) NOT NULL DEFAULT '0',
  `c_time` varchar(14) DEFAULT NULL,
  `c_retry` int(10) NOT NULL DEFAULT '0',
  `c_data` longblob,
  `c_result` varchar(1) DEFAULT NULL,
  `c_cmd` varchar(1) DEFAULT NULL
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`tconfig` (
  `maxuser` int(10) NOT NULL DEFAULT '0',
  `minvid` int(10) NOT NULL DEFAULT '0',
  `maxvid` int(10) NOT NULL DEFAULT '0',
  `fpnum` varchar(1) DEFAULT '2',
  `autodn` varchar(1) DEFAULT '1',
  `dntime` varchar(4) DEFAULT '2500',
  `autoup` varchar(1) DEFAULT '1',
  `groupid` varchar(1) DEFAULT '2',
  `gateid` varchar(1) DEFAULT '2',
  `userid` varchar(1) DEFAULT '6',
  `passwd` varchar(1) DEFAULT '8',
  `attend` varchar(1) DEFAULT '0',
  `tsockport` int(10) NOT NULL DEFAULT '0',
  `csockport` int(10) NOT NULL DEFAULT '0',
  `polltime` int(10) NOT NULL DEFAULT '0',
  `serverip` varchar(20) DEFAULT '127.0.0.1',
  `savemode` varchar(1) DEFAULT '0',
  `update_flag` varchar(1) DEFAULT '1',
  `latetime` varchar(4) DEFAULT '',
  `lfdlevel` varchar(1) DEFAULT '',
  `L_PanicDuress` int(10) DEFAULT '0',
  `L_DefaultNotAccess` int(10) DEFAULT '0',
  `L_ServerLanguage` int(10) DEFAULT '0',
  `L_LFDLevel` int(10) DEFAULT '0',
  `L_AuthData` int(10) DEFAULT '0',
  `C_WebOpen` varchar(255) DEFAULT NULL,
  `C_MobileOpen` varchar(255) DEFAULT NULL,
  `L_AdminRestrict` int(10) DEFAULT '0',
  `L_FindUserByFP` int(10) DEFAULT '0'
)";
mysqli_query($conn, $query);

$query = "INSERT INTO Access.tconfig (serverip) VALUES ('127.0.0.1')";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`tent` (
  `e_date` varchar(8) NOT NULL DEFAULT '',
  `e_time` varchar(6) NOT NULL DEFAULT '',
  `g_id` int(10) NOT NULL DEFAULT '0',
  `e_id` int(10) NOT NULL DEFAULT '0',
  `e_name` varchar(30) DEFAULT NULL,
  `e_idno` varchar(30) DEFAULT NULL,
  `e_group` smallint(5) NOT NULL DEFAULT '0',
  `e_user` varchar(1) DEFAULT NULL,
  `e_mode` varchar(1) DEFAULT NULL,
  `e_type` varchar(1) DEFAULT NULL,
  `e_result` varchar(1) DEFAULT NULL,
  `e_etc` varchar(1) DEFAULT NULL,
  `ed` int(10) NOT NULL AUTO_INCREMENT,
  `p_flag` int(10) NOT NULL DEFAULT '0',
  `e_uptime` varchar(14) DEFAULT NULL,
  `e_upmode` varchar(1) DEFAULT NULL,
  `D_Latitude` int(10) DEFAULT '0',
  `D_Longitude` int(10) DEFAULT '0',
  `C_MobilePhone` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`e_date`,`e_time`,`g_id`,`e_id`),
  KEY `e_group` (`e_group`),
  KEY `ed` (`ed`),
  KEY `e_date` (`e_date`),
  KEY `e_time` (`e_time`),
  KEY `g_id` (`g_id`),
  KEY `e_id` (`e_id`),
  KEY `p_flag` (`p_flag`),
  KEY `e_etc` (`e_etc`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`tenter` (
  `e_date` varchar(8) NOT NULL DEFAULT '',
  `e_time` varchar(6) NOT NULL DEFAULT '',
  `g_id` int(10) NOT NULL DEFAULT '0',
  `e_id` int(10) NOT NULL DEFAULT '0',
  `e_name` varchar(30) DEFAULT NULL,
  `e_idno` varchar(30) DEFAULT NULL,
  `e_group` smallint(5) NOT NULL DEFAULT '0',
  `e_user` varchar(1) DEFAULT NULL,
  `e_mode` varchar(1) DEFAULT NULL,
  `e_type` varchar(1) DEFAULT NULL,
  `e_result` varchar(1) DEFAULT NULL,
  `e_etc` varchar(10) DEFAULT NULL,
  `ed` int(20) NOT NULL AUTO_INCREMENT,
  `p_flag` int(10) NOT NULL DEFAULT '0',
  `e_uptime` varchar(14) DEFAULT NULL,
  `e_upmode` varchar(1) DEFAULT NULL,
  `D_Latitude` int(10) DEFAULT '0',
  `D_Longitude` int(10) DEFAULT '0',
  `C_MobilePhone` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`e_date`,`e_time`,`g_id`,`e_id`),
  UNIQUE KEY `ed` (`ed`),
  KEY `e_group` (`e_group`),
  KEY `e_date` (`e_date`),
  KEY `e_time` (`e_time`),
  KEY `g_id` (`g_id`),
  KEY `e_id` (`e_id`),
  KEY `p_flag` (`p_flag`),
  KEY `e_etc` (`e_etc`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`tgate` (
  `id` int(10) NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `reg_date` varchar(12) DEFAULT NULL,
  `floor` int(10) NOT NULL DEFAULT '0',
  `place` varchar(30) DEFAULT NULL,
  `block` varchar(1) DEFAULT NULL,
  `userctrl` varchar(1) DEFAULT NULL,
  `passtime` varchar(8) DEFAULT NULL,
  `version` varchar(4) DEFAULT NULL,
  `admin` longblob,
  `lastup` varchar(14) DEFAULT NULL,
  `remark` varchar(50) DEFAULT NULL,
  `exit` tinyint(1) NOT NULL DEFAULT '0',
  `antipass` int(10) NOT NULL DEFAULT '0',
  `antipass_level` int(10) NOT NULL DEFAULT '0',
  `antipass_mode` int(10) NOT NULL DEFAULT '0',
  `Meal` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`tgatelog` (
  `e_date` varchar(8) DEFAULT NULL,
  `e_time` varchar(6) DEFAULT NULL,
  `id` int(10) NOT NULL DEFAULT '0',
  `bstatus` varchar(1) DEFAULT NULL,
  KEY `id` (`id`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`tgroup` (
  `id` smallint(5) NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `reg_date` varchar(12) DEFAULT NULL,
  `timelimit` varchar(8) DEFAULT NULL,
  `gate_id` longblob,
  `remark` varchar(255) DEFAULT NULL,
  `MinWorkForBreak` int(11) NOT NULL DEFAULT '0',
  `Start` varchar(6) DEFAULT NULL,
  `GraceTo` varchar(6) DEFAULT NULL,
  `FlexiBreak` int(10) NOT NULL DEFAULT '0',
  `BreakFrom` varchar(6) DEFAULT NULL,
  `BreakTo` varchar(6) DEFAULT NULL,
  `Close` varchar(6) DEFAULT NULL,
  `NightFlag` tinyint(1) NOT NULL DEFAULT '0',
  `ShiftTypeID` int(10) NOT NULL DEFAULT '0',
  `ScheduleID` int(10) NOT NULL DEFAULT '0',
  `WorkMin` int(10) NOT NULL DEFAULT '0',
  `MinOTWorkForBreak` int(11) NOT NULL DEFAULT '0',
  `RotateFlag` int(11) NOT NULL DEFAULT '0',
  `MinOT1Work` int(11) NOT NULL DEFAULT '0',
  `AccessRestrict` varchar(5) NOT NULL DEFAULT 'No',
  `RelaxRestrict` varchar(5) NOT NULL DEFAULT 'No',
  `StartHour` varchar(4) NOT NULL DEFAULT '0000',
  `CloseHour` varchar(4) NOT NULL DEFAULT '2359',
  `EarlyInOT` varchar(5) NOT NULL DEFAULT 'No',
  `LessLunchOT` varchar(5) NOT NULL DEFAULT 'No',
  `NoBreakException` varchar(5) NOT NULL DEFAULT 'Yes',
  `EarlyInOTDayDate` varchar(5) NOT NULL DEFAULT 'No',
  `MinOTValue` int(5) NOT NULL DEFAULT '0',
  `MaxOTValue` int(5) NOT NULL DEFAULT '1440',
  `ASLate` int(3) NOT NULL DEFAULT '0' COMMENT 'Option to Enter the Number of Normal Days of Lateness after which Employee should be Automatically Suspended',
  `ASAbsent` int(3) NOT NULL DEFAULT '0' COMMENT 'Option to Enter the Number of Normal Days of Absence after which Employee should be Automatically Suspended',
  `MaxOTValueOT1` int(5) NOT NULL DEFAULT '1440',
  `MaxOTValueOT2` int(5) NOT NULL DEFAULT '1440',
  `onetouch` varchar(25) DEFAULT NULL,
  `autosync` varchar(1) DEFAULT NULL,
  `MoveNS` varchar(5) NOT NULL DEFAULT 'No',
  `OT1RF` decimal(5,2) NOT NULL DEFAULT '0.00',
  `NSOTCO` int(4) NOT NULL DEFAULT '0',
  `ExemptOT1` varchar(3) NOT NULL DEFAULT 'No',
  `ExemptOT2` varchar(3) NOT NULL DEFAULT 'No',
  `ExemptOTDate` varchar(3) NOT NULL DEFAULT 'No',
  `ProxyOT` varchar(5) DEFAULT 'Yes',
  `NoBreakExceptionOT` varchar(5) DEFAULT 'Yes',
  `ExemptLI` varchar(10) DEFAULT 'NONE',
  `ExemptOT` varchar(10) DEFAULT 'NONE',
  `DNT` varchar(5) DEFAULT 'No',
  `EmpClose` varchar(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ScheduleID` (`ScheduleID`),
  KEY `ShiftTypeID` (`ShiftTypeID`),
  KEY `Skey` (`MinWorkForBreak`)
)";
mysqli_query($conn, $query);

$query = "INSERT INTO Access.tgroup (id, name, reg_date, timelimit, NightFlag) VALUES (0, 'Not Assigned', '197008130000', '00000001', 0)";
mysqli_query($conn, $query);

$query = "INSERT INTO Access.tgroup (id, name, reg_date, timelimit, NightFlag) VALUES (1, 'Assign All', '197008130000', '00000001', 0)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`tlsflag` (
  `TLSFlagID` int(10) NOT NULL AUTO_INCREMENT,
  `Violet` varchar(5) DEFAULT 'Yes',
  `Indigo` varchar(5) DEFAULT 'Yes',
  `Blue` varchar(5) DEFAULT 'Yes',
  `Green` varchar(5) DEFAULT 'Yes',
  `Yellow` varchar(5) DEFAULT 'Yes',
  `Orange` varchar(5) DEFAULT 'Yes',
  `Red` varchar(5) DEFAULT 'Yes',
  `Gray` varchar(5) DEFAULT 'Yes',
  `Brown` varchar(5) DEFAULT 'Yes',
  `Purple` varchar(5) DEFAULT 'Yes',
  `Black` varchar(5) DEFAULT 'Yes',
  `Proxy` varchar(5) DEFAULT 'Yes',
  `Magenta` varchar(5) DEFAULT 'Yes',
  `Teal` varchar(5) DEFAULT 'Yes',
  `Aqua` varchar(5) DEFAULT 'Yes',
  `Safron` varchar(5) DEFAULT 'Yes',
  `Amber` varchar(5) DEFAULT 'Yes',
  `Gold` varchar(5) DEFAULT 'Yes',
  `Vermilion` varchar(5) DEFAULT 'Yes',
  `Silver` varchar(5) DEFAULT 'Yes',
  `Maroon` varchar(5) DEFAULT 'Yes',
  `Pink` varchar(5) DEFAULT 'Yes',
  PRIMARY KEY (`TLSFlagID`)
)";
mysqli_query($conn, $query);

$query = "INSERT INTO Access.TLSFlag (Violet) VALUES ('Yes')";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`transact` (
  `TransactID` int(10) NOT NULL AUTO_INCREMENT,
  `Transactdate` int(10) NOT NULL DEFAULT '0',
  `Transacttime` int(10) NOT NULL DEFAULT '0',
  `Username` varchar(255) DEFAULT NULL,
  `Transactquery` varchar(1024) NOT NULL,
  PRIMARY KEY (`TransactID`),
  KEY `TransactID` (`TransactID`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`transactiontypemaster` (
  `TransactionTypeID` int(10) NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`TransactionTypeID`),
  UNIQUE KEY `TN` (`Name`),
  UNIQUE KEY `Name` (`Name`)
)";
mysqli_query($conn, $query);

$query = "INSERT INTO Access.TransactionTypeMaster (Name) VALUES ('ADD'), ('UPDATE'), ('DELETE')";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`tuser` (
  `id` int(10) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `reg_date` varchar(12) DEFAULT NULL,
  `datelimit` varchar(17) DEFAULT NULL,
  `idno` varchar(255) DEFAULT NULL,
  `badmin` varchar(1) DEFAULT NULL,
  `padmin` int(10) NOT NULL DEFAULT '0',
  `dept` varchar(255) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `group_id` int(20) DEFAULT '0',
  `cantgate` longblob,
  `timegate` longblob,
  `validtype` varchar(1) DEFAULT NULL,
  `pwd` varchar(255) DEFAULT NULL,
  `cancard` varchar(1) DEFAULT NULL,
  `cardnum` varchar(20) DEFAULT NULL,
  `identify` varchar(1) DEFAULT NULL,
  `seculevel` varchar(1) DEFAULT NULL,
  `fpdata` longblob,
  `fpimage` longblob,
  `fpname` longblob,
  `face` longblob,
  `voice` longblob,
  `remark` varchar(255) DEFAULT NULL,
  `OT1` varchar(255) NOT NULL DEFAULT 'Saturday',
  `OT2` varchar(255) NOT NULL DEFAULT 'Sunday',
  `antipass_state` int(10) NOT NULL DEFAULT '0',
  `antipass_lasttime` varchar(14) DEFAULT NULL,
  `OTRotate` varchar(255) NOT NULL DEFAULT 'No',
  `OldID1` int(11) NOT NULL DEFAULT '0',
  `OTRotateDate` int(11) NOT NULL DEFAULT '99999999',
  `flagdatelimit` varchar(17) NOT NULL DEFAULT 'N2001010120010101' COMMENT 'Store Actual AccessLimit while Flagging Employee for NOT clocking Temporarily',
  `PassiveType` varchar(5) NOT NULL DEFAULT 'ACT',
  `PassiveRemark` varchar(255) NOT NULL DEFAULT '.',
  `UserStatus` int(11) NOT NULL DEFAULT '10',
  `F1` varchar(255) DEFAULT NULL,
  `F2` varchar(255) DEFAULT NULL,
  `F3` varchar(255) DEFAULT NULL,
  `F4` varchar(255) DEFAULT NULL,
  `F5` varchar(255) DEFAULT NULL,
  `F6` varchar(255) DEFAULT NULL,
  `F7` varchar(255) DEFAULT NULL,
  `F8` varchar(255) DEFAULT NULL,
  `F9` varchar(255) DEFAULT NULL,
  `F10` varchar(255) DEFAULT NULL,
  `L_FaceIdentify` int(10) DEFAULT '0',
  `B_DuressFinger` longblob,
  `L_AuthValue` int(10) DEFAULT '0',
  `L_RegServer` int(10) DEFAULT '0',
  `C_RemotePW` varchar(255) DEFAULT NULL,
  `L_WrongCount` int(10) DEFAULT '0',
  `L_LogonLocked` int(10) DEFAULT '0',
  `C_LogonDateTime` varchar(255) DEFAULT NULL,
  `C_UdatePassword` varchar(255) DEFAULT NULL,
  `C_MustChgPwd` varchar(255) DEFAULT NULL,
  `CAGID` int(10) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `cardnum` (`cardnum`),
  KEY `company` (`company`),
  KEY `dept` (`dept`),
  KEY `group_id` (`group_id`),
  KEY `identify` (`identify`),
  KEY `idno` (`idno`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`tvisited` (
  `id` int(10) NOT NULL DEFAULT '0',
  `name` varchar(30) DEFAULT NULL,
  `reg_date` varchar(12) DEFAULT NULL,
  `datelimit` varchar(17) DEFAULT NULL,
  `timelimit` varchar(8) DEFAULT NULL,
  `out_date` varchar(12) DEFAULT NULL,
  `idno` varchar(30) DEFAULT NULL,
  `contact` varchar(50) DEFAULT NULL,
  `company` varchar(30) DEFAULT NULL,
  `dept` varchar(30) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` varchar(50) DEFAULT NULL,
  `group_id` smallint(5) NOT NULL DEFAULT '0',
  `cantgate` longblob,
  `timegate` longblob,
  `validtype` varchar(1) DEFAULT NULL,
  `pwd` varchar(8) DEFAULT NULL,
  `cancard` varchar(1) DEFAULT NULL,
  `cardnum` varchar(20) DEFAULT NULL,
  `identify` varchar(1) DEFAULT NULL,
  `seculevel` varchar(1) DEFAULT NULL,
  `fpdata` longblob,
  `fpimage` longblob,
  `fpname` longblob,
  `face` longblob,
  `id_image` longblob,
  `voice` longblob,
  `remark` varchar(50) DEFAULT NULL,
  UNIQUE KEY `idno` (`idno`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`tvisitor` (
  `id` int(10) NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `reg_date` varchar(12) DEFAULT NULL,
  `datelimit` varchar(17) DEFAULT NULL,
  `timelimit` varchar(8) DEFAULT NULL,
  `out_date` varchar(12) DEFAULT NULL,
  `idno` varchar(30) DEFAULT NULL,
  `contact` varchar(50) DEFAULT NULL,
  `company` varchar(30) DEFAULT NULL,
  `dept` varchar(30) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` varchar(50) DEFAULT NULL,
  `group_id` smallint(5) NOT NULL DEFAULT '0',
  `cantgate` longblob,
  `timegate` longblob,
  `validtype` varchar(1) DEFAULT NULL,
  `pwd` varchar(8) DEFAULT NULL,
  `cancard` varchar(1) DEFAULT NULL,
  `cardnum` varchar(20) DEFAULT NULL,
  `identify` varchar(1) DEFAULT NULL,
  `seculevel` varchar(1) DEFAULT NULL,
  `fpdata` longblob,
  `fpimage` longblob,
  `fpname` longblob,
  `face` longblob,
  `id_image` longblob,
  `voice` longblob,
  `remark` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`unismap` (
  `MapID` int(10) NOT NULL AUTO_INCREMENT,
  `ACol` varchar(255) DEFAULT NULL,
  `UCol` varchar(255) DEFAULT NULL,
  `UMaster` varchar(255) DEFAULT NULL,
  `UMasterName` varchar(255) DEFAULT NULL,
  `ECol` varchar(255) DEFAULT NULL,
  `MMaster` varchar(255) DEFAULT NULL,
  `EMasterName` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`MapID`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`userdept` (
  `UserDept` int(10) NOT NULL AUTO_INCREMENT,
  `Username` varchar(255) DEFAULT NULL,
  `Dept` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`UserDept`),
  KEY `Dept` (`Dept`),
  KEY `Username` (`Username`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`userdiv` (
  `UserDivID` int(11) NOT NULL AUTO_INCREMENT,
  `Username` varchar(255) NOT NULL,
  `Div` varchar(255) NOT NULL,
  PRIMARY KEY (`UserDivID`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`userdivlockdate` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Div` varchar(255) NOT NULL DEFAULT '.',
  `Date` int(8) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`userf3` (
  `UserF3` int(10) NOT NULL AUTO_INCREMENT,
  `Username` varchar(255) DEFAULT NULL,
  `F3` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`UserF3`),
  KEY `F3` (`F3`),
  KEY `Username` (`Username`)
)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`usermaster` (
  `Username` varchar(255) NOT NULL DEFAULT '',
  `Userpass` varchar(255) DEFAULT NULL,
  `Usermail` varchar(255) DEFAULT NULL,
  `Usertype` varchar(255) DEFAULT NULL,
  `Userlevel` longtext,
  `UserStatus` int(11) NOT NULL DEFAULT '5',
  `Lastlogin` int(10) NOT NULL DEFAULT '0',
  `RASSelection` varchar(1024) NOT NULL DEFAULT '-V--I--B--G--Y--O--R--GR--BR--PR--BK--WKD--PXY--FLG--SAT--SUN--TLD--NS--NF--TND--WKH--PXH--FLH--SATH--SUNH--TLH--NSH--NFH--TNH-',
  `RDSSelection` varchar(1024) NOT NULL DEFAULT '--P--Dept--Div--Shift--Total',
  `RDSFont` varchar(10) NOT NULL DEFAULT '1',
  `RDSCW` varchar(10) NOT NULL DEFAULT '15%',
  `RDSHeaderBreak` varchar(10) NOT NULL DEFAULT '25',
  `RGSSelection` varchar(1024) NOT NULL DEFAULT '-RG--DAR--DAPO--BK--PX--V--I--B--G--Y--O--R--GR--BR--PR--FLG--WKD--SAT--SUN--P--A--AF--NS--GC--LI--MB--EO--AO-',
  `RHSSelection` varchar(1024) DEFAULT '--IDNo--Dept--Div--Remark',
  `OT1F` decimal(5,2) DEFAULT '1.00',
  `OT2F` decimal(5,2) DEFAULT '1.00',
  `OTDF` decimal(5,2) DEFAULT '1.00',
  PRIMARY KEY (`Username`)
)";
mysqli_query($conn, $query);

$query = "INSERT INTO Access.UserMaster (Username, Userpass, Usermail, Userlevel, Userstatus, Lastlogin) VALUES ('virdi', '``C,P(#0,%41(', 'care.nig@endeavourafrica.com', '11V11A11E11D11R12V12A12E12D12R13V13A13E13D13R14V14A14E14D14R15V15A15E15D15R16V16A16E16D16R17V17A17E17D17R18V18A18E18D18R19V19A19E19D19R20V20A20E20D20R21V21A21E21D21R22V22A22E22D22R23V23A23E23D23R24V24A24E24D24R25V25A25E25D25R26V26A26E26D26R27V27A27E27D27R28V28A28E28D28R29V29A29E29D29R35V36V', '1', 20010101)";
mysqli_query($conn, $query);

$query = "INSERT INTO Access.UserMaster (Username, Userpass, Usermail, Userlevel, Userstatus, Lastlogin) VALUES ('admin', '`X6:M168%', 'care.nig@endeavourafrica.com', '11V11A11E11D11R12V12A12E12D12R13V13A13E13D13R14V14A14E14D14R15V15A15E15D15R16V16A16E16D16R17V17A17E17D17R18V18A18E18D18R19V19A19E19D19R20V20A20E20D20R21V21A21E21D21R22V22A22E22D22R23V23A23E23D23R24V24A24E24D24R25V25A25E25D25R26V26A26E26D26R27V27A27E27D27R28V28A28E28D28R29V29A29E29D29R35V36V', '5', 20010101)";
mysqli_query($conn, $query);

$query = "CREATE TABLE `Access`.`weekmaster` (
  `WeekMasterID` int(10) NOT NULL AUTO_INCREMENT,
  `WeekNo` int(10) NOT NULL DEFAULT '0',
  `e_id` int(10) NOT NULL DEFAULT '0',
  `LogDate` int(10) NOT NULL DEFAULT '0',
  `Start` varchar(10) DEFAULT NULL,
  `Close` varchar(10) DEFAULT NULL,
  `Seconds` int(10) NOT NULL DEFAULT '0',
  `p_flag` int(11) NOT NULL DEFAULT '0',
  `group_id` int(10) NOT NULL DEFAULT '0',
  `Flag` varchar(10) NOT NULL DEFAULT 'Black',
  PRIMARY KEY (`WeekMasterID`),
  KEY `e_id` (`e_id`),
  KEY `group_id` (`group_id`),
  KEY `LastWeekMasterID` (`p_flag`)
)";
mysqli_query($conn, $query);

$query = "DROP TRIGGER IF EXISTS Access.i_tuser ";
mysqli_query($conn, $query);

$query1 = "CREATE TRIGGER i_tuser AFTER INSERT ON Access.tuser
FOR EACH ROW
INSERT INTO Access.EmployeeFlag (EmployeeID) VALUES (NEW.id);";
mysqli_query($conn, $query);

$query = "DROP TRIGGER IF EXISTS Access.d_tuser ";
mysqli_query($conn, $query);

$query2 = "CREATE TRIGGER d_tuser AFTER DELETE ON Access.tuser
FOR EACH ROW
DELETE FROM Access.EmployeeFlag WHERE EmployeeID = OLD.id;";
mysqli_query($conn, $query);

$query = "GRANT ALL PRIVILEGES ON Access.* TO 'fdmsusr'@'%' WITH GRANT OPTION";
mysqli_query($conn, $query);

$query = "GRANT ALL PRIVILEGES ON Access.* TO 'fdmsusr'@'localhost' WITH GRANT OPTION";
mysqli_query($conn, $query);

$query = "CREATE DATABASE AccessTemp";
mysqli_query($conn, $query);
mysqli_select_db($conn, "AccessTemp");
$query = "CREATE TABLE AccessTemp.tenter (e_date varchar( 8 ) NOT NULL , e_time varchar( 6 ) NOT NULL , g_id int( 10 ) NOT NULL , e_id int( 10 ) NOT NULL , e_name varchar( 30 ) default NULL , e_idno varchar( 30 ) default NULL , e_group smallint( 5 ) default NULL , e_user varchar( 1 ) default NULL , e_mode varchar( 1 ) default NULL , e_type varchar( 1 ) default NULL , e_result varchar( 1 ) default NULL , e_etc varchar( 1 ) default NULL , e_retry int( 10 ) NOT NULL default '0' , PRIMARY KEY ( e_date , e_time , g_id , e_id ) )";
mysqli_query($conn, $query);
$query = " CREATE  TABLE  AccessTemp.tevent (  e_date varchar( 8  )  default NULL , e_time varchar( 6  )  default NULL , e_type varchar( 1  )  default NULL , e_source varchar( 1  )  default NULL , e_sourceid int( 10  )  default NULL , e_computer varchar( 32  )  default NULL , e_event varchar( 7  )  default NULL , e_content varchar( 255  )  default NULL  ) ";
mysqli_query($conn, $query);
$query = "GRANT ALL PRIVILEGES ON AccessTemp.* TO 'fdmsusr'@'%' WITH GRANT OPTION";
mysqli_query($conn, $query);
$query = "GRANT ALL PRIVILEGES ON AccessTemp.* TO 'fdmsusr'@'localhost' WITH GRANT OPTION";
mysqli_query($conn, $query);
$query = "CREATE USER 'unisuser'@'%' IDENTIFIED BY 'unisamho'";
mysqli_query($conn, $query);
$query = "GRANT USAGE ON * . * TO 'unisuser'@'%' IDENTIFIED BY 'unisamho' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0";
mysqli_query($conn, $query);
$query = "CREATE USER 'unisuser'@'localhost' IDENTIFIED BY 'unisamho'";
mysqli_query($conn, $query);
$query = "GRANT USAGE ON * . * TO 'unisuser'@'localhost' IDENTIFIED BY 'unisamho' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0";
mysqli_query($conn, $query);

$query = "CREATE DATABASE UNIS";
mysqli_query($conn, $query);
mysqli_select_db($conn, "UNIS");
$query = "USE UNIS; DROP TABLE IF EXISTS UNIS.cAccessArea; CREATE TABLE UNIS.cAccessArea (   C_Code VARCHAR(4) NOT NULL,   C_Name VARCHAR(30) NULL,   L_Flag INT(10) NULL,   C_Remark VARCHAR(255) NULL,   C_AccessTime VARCHAR(4) NULL,   C_AccessTime2 VARCHAR(4) NULL,   C_AccessTime3 VARCHAR(4) NULL,   C_AccessTime4 VARCHAR(4) NULL,   PRIMARY KEY (C_Code) );;  DROP TABLE IF EXISTS UNIS.cAccessGroup; CREATE TABLE UNIS.cAccessGroup (   C_Code VARCHAR(4) NOT NULL,   C_Name VARCHAR(30) NULL,   L_Flag INT(10) NULL,   C_Remark VARCHAR(255) NULL,   PRIMARY KEY (C_Code) );  DROP TABLE IF EXISTS UNIS.cAccessTime; CREATE TABLE UNIS.cAccessTime (   C_Code VARCHAR(4) NOT NULL,   C_Name VARCHAR(30) NULL,   L_Flag INT(10) NULL,   C_Holiday VARCHAR(4) NULL,   C_Sun VARCHAR(4) NULL,   C_Mon VARCHAR(4) NULL,   C_The VARCHAR(4) NULL,   C_Wed VARCHAR(4) NULL,   C_Thu VARCHAR(4) NULL,   C_Fri VARCHAR(4) NULL,   C_Sat VARCHAR(4) NULL,   C_Hol VARCHAR(4) NULL,   C_Remark VARCHAR(255) NULL,   PRIMARY KEY (C_Code) );  DROP TABLE IF EXISTS UNIS.cAuthority; CREATE TABLE UNIS.cAuthority (   C_Code VARCHAR(4) NOT NULL,   C_Name VARCHAR(30) NULL,   L_SetLocal INT(10) NULL,   L_RegInfo INT(10) NULL,   L_DataBackup INT(10) NULL,   L_MgrTerminal INT(10) NULL,   L_RegControl INT(10) NULL,   L_SetControl INT(10) NULL,   L_RegEmploye INT(10) NULL,   L_ModEmploye INT(10) NULL,   L_OutEmploye INT(10) NULL,   L_RegVisitor INT(10) NULL,   L_OutVisitor INT(10) NULL,   L_RegMoney INT(10) NULL,   L_RegWork INT(10) NULL,   L_SetWork INT(10) NULL,   L_ModWork INT(10) NULL,   L_RegMeal INT(10) NULL,   L_SetMeal INT(10) NULL,   L_ModMeal INT(10) NULL,   L_DelResult INT(10) NULL,   L_DelWork INT(10) NULL,   L_DelMeal INT(10) NULL,   L_MgrScope INT(10) NULL,   L_ChgBlacklist INT(10) NULL,   L_RelBlacklist INT(10) NULL,   L_ModBlacklist INT(10) NULL,   L_DelBlacklist INT(10) NULL,   L_Customized INT(10) NULL,   L_RegAdmin INT(10) NULL,   L_ModAdmin INT(10) NULL,   L_SetShutdown INT(10) NULL,   L_MntMgr INT(10) NULL,   L_MntClient INT(10) NULL,   L_MntTerminal INT(10) NULL,   L_MntAuthLog INT(10) NULL,   L_MntEvent INT(10) NULL,   L_TmnMgr INT(10) NULL,   L_TmnAdd INT(10) NULL,   L_TmnMod INT(10) NULL,   L_TmnDel INT(10) NULL,   L_TmnUpgrade INT(10) NULL,   L_TmnOption INT(10) NULL,   L_TmnAdmin INT(10) NULL,   L_TmnSendFile INT(10) NULL,   L_EmpMgr INT(10) NULL,   L_EmpAdd INT(10) NULL,   L_EmpMod INT(10) NULL,   L_EmpDel INT(10) NULL,   L_EmpSendTerminal INT(10) NULL,   L_EmpTerminalMng INT(10) NULL,   L_EmpRegAdmin INT(10) NULL,   L_VstMgr INT(10) NULL,   L_VstAdd INT(10) NULL,   L_VstMod INT(10) NULL,   L_VstDel INT(10) NULL,   L_BlckMgr INT(10) NULL,   L_BlckChange INT(10) NULL,   L_BlckRelease INT(10) NULL,   L_BlckDel INT(10) NULL,   L_BlckMod INT(10) NULL,   L_AccMgr INT(10) NULL,   L_AccSet INT(10) NULL,   L_MapMgr INT(10) NULL,   L_MapSet INT(10) NULL,   L_TnaMgr INT(10) NULL,   L_TnaSet INT(10) NULL,   L_TnaSpecial INT(10) NULL,   L_TnaWork INT(10) NULL,   L_TnaOutState INT(10) NULL,   L_TnaOutExcRecord INT(10) NULL,   L_TnaSummary INT(10) NULL,   L_TnaSendResult INT(10) NULL,   L_TnaDelData INT(10) NULL,   L_MealMgr INT(10) NULL,   L_MealOutRecord INT(10) NULL,   L_MealDelData INT(10) NULL,   L_MealOutDept INT(10) NULL,   L_MealOutPerson INT(10) NULL,   L_MealSet INT(10) NULL,   L_LogMgr INT(10) NULL,   L_LogOutRecord INT(10) NULL,   L_LogDelRecord INT(10) NULL,   L_SetRegInfo INT(10) NULL,   L_SetMgr INT(10) NULL,   L_SetServer INT(10) NULL,   L_SetPwd INT(10) NULL,   L_SetMail INT(10) NULL,   L_SetEtc INT(10) NULL,   PRIMARY KEY (C_Code) );  DROP TABLE IF EXISTS UNIS.cHoliday; CREATE TABLE UNIS.cHoliday (   C_Code VARCHAR(4) NOT NULL,   C_Name VARCHAR(30) NULL,   C_Remark VARCHAR(255) NULL,   PRIMARY KEY (C_Code) );  DROP TABLE IF EXISTS UNIS.cOffice; CREATE TABLE UNIS.cOffice (   C_Code VARCHAR(30) NOT NULL,   C_Name VARCHAR(50) NULL,   PRIMARY KEY (C_Code) );  DROP TABLE IF EXISTS UNIS.cPassback; CREATE TABLE UNIS.cPassback (   C_Code VARCHAR(4) NOT NULL,   C_Name VARCHAR(30) NULL,   C_Remark VARCHAR(255) NULL,   L_ZoneOut INT(10) NULL,   PRIMARY KEY (C_Code) );  DROP TABLE IF EXISTS UNIS.cPost; CREATE TABLE UNIS.cPost (   C_Code VARCHAR(30) NULL,   C_Name VARCHAR(30) NULL );  DROP TABLE IF EXISTS UNIS.cStaff; CREATE TABLE UNIS.cStaff (   C_Code VARCHAR(30) NULL,   C_Name VARCHAR(30) NULL );  DROP TABLE IF EXISTS UNIS.cTimezone; CREATE TABLE UNIS.cTimezone (   C_Code VARCHAR(4) NOT NULL,   C_Name VARCHAR(30) NULL,   L_Flag INT(10) NULL,   C_Remark VARCHAR(255) NULL,   L_AuthType INT(10) NULL,   L_AuthValue INT(10) NULL,   PRIMARY KEY (C_Code) );  DROP TABLE IF EXISTS UNIS.iAccessArea; CREATE TABLE UNIS.iAccessArea (   C_Code VARCHAR(4) NOT NULL,   L_TID INT(10) NOT NULL,   PRIMARY KEY (C_Code, L_TID) );  DROP TABLE IF EXISTS UNIS.iAccessGroup; CREATE TABLE UNIS.iAccessGroup (   C_Code VARCHAR(4) NULL,   L_Type INT(10) NULL,   C_AccessCode VARCHAR(4) NULL );  DROP TABLE IF EXISTS UNIS.iACUInfo; CREATE TABLE UNIS.iACUInfo (   L_TID INT(10) NULL,   C_PartitionStatus VARCHAR(12) NULL,   C_ZoneStatus VARCHAR(24) NULL,   C_LockStatus VARCHAR(12) NULL,   C_ReaderStatus VARCHAR(24) NULL,   C_ReaderVer1 VARCHAR(128) NULL,   C_ReaderVer2 VARCHAR(128) NULL,   C_ReaderVer3 VARCHAR(128) NULL,   C_ReaderVer4 VARCHAR(128) NULL,   C_ReaderVer5 VARCHAR(128) NULL,   C_ReaderVer6 VARCHAR(128) NULL,   C_ReaderVer7 VARCHAR(128) NULL,   C_ReaderVer8 VARCHAR(128) NULL,   C_ReaderName0 VARCHAR(128) NULL,   C_ReaderName1 VARCHAR(128) NULL,   C_ReaderName2 VARCHAR(128) NULL,   C_ReaderName3 VARCHAR(128) NULL,   C_ReaderName4 VARCHAR(128) NULL,   C_ReaderName5 VARCHAR(128) NULL,   C_ReaderName6 VARCHAR(128) NULL,   C_ReaderName7 VARCHAR(128) NULL,   C_WiegandName1 VARCHAR(128) NULL,   C_WiegandName2 VARCHAR(128) NULL,   C_WiegandName3 VARCHAR(128) NULL,   C_WiegandName4 VARCHAR(128) NULL,   UNIQUE INDEX Index_FB798021_906A_4567 (L_TID) );  DROP TABLE IF EXISTS UNIS.iCantTerminal; CREATE TABLE UNIS.iCantTerminal (   L_UID INT(10) NOT NULL,   L_TID INT(10) NOT NULL,   PRIMARY KEY (L_UID, L_TID) );  DROP TABLE IF EXISTS UNIS.iCardInfo; CREATE TABLE UNIS.iCardInfo (   L_CardSize INT(10) NULL,   L_CardType INT(10) NULL,   L_ReadType INT(10) NULL,   L_TemplateSize INT(10) NULL,   L_TemplateCount INT(10) NULL );  DROP TABLE IF EXISTS UNIS.iCardLayout; CREATE TABLE UNIS.iCardLayout (   L_Index INT(10) NULL,   L_Sector INT(10) NULL,   L_Block INT(10) NULL,   L_Start INT(10) NULL,   L_Length INT(10) NULL,   L_KeyType INT(10) NULL,   C_KeyValue VARCHAR(12) NULL,   C_AIDCode VARCHAR(16) NULL );  DROP TABLE IF EXISTS UNIS.iDVRInfo; CREATE TABLE UNIS.iDVRInfo (   L_DVRID INT(10) NULL,   C_DVRIP VARCHAR(100) NULL,   L_DVRPort INT(10) NULL,   C_DVRLoginID VARCHAR(100) NULL,   C_DVRLoginPW VARCHAR(100) NULL,   L_PrevTime INT(10) NULL );  DROP TABLE IF EXISTS UNIS.iHoliday; CREATE TABLE UNIS.iHoliday (   C_Code VARCHAR(4) NULL,   C_Holiday VARCHAR(4) NULL,   C_DayName VARCHAR(30) NULL );  DROP TABLE IF EXISTS UNIS.iMapDrawing; CREATE TABLE UNIS.iMapDrawing (   C_MapCode VARCHAR(4) NULL,   L_PosX INT(10) NULL,   L_PosY INT(10) NULL );  DROP TABLE IF EXISTS UNIS.iMapTerminal; CREATE TABLE UNIS.iMapTerminal (   C_MapCode VARCHAR(4) NULL,   L_TID INT(10) NULL,   L_Type INT(10) NULL,   L_PosX INT(10) NULL,   L_PosY INT(10) NULL );  DROP TABLE IF EXISTS UNIS.iMeal; CREATE TABLE UNIS.iMeal (   C_Code VARCHAR(4) NULL,   C_Name VARCHAR(30) NULL,   L_MealType INT(10) NULL,   L_MealLimit INT(10) NULL,   C_MealTime1 VARCHAR(5) NULL,   C_MealTime2 VARCHAR(5) NULL,   C_StatsDate VARCHAR(8) NULL,   UNIQUE INDEX Index_5321C1F8_548D_4972 (C_Code) );  DROP TABLE IF EXISTS UNIS.iMealPay; CREATE TABLE UNIS.iMealPay (   L_MealType INT(10) NULL,   L_Menu1 INT(10) NULL,   L_Menu2 INT(10) NULL,   L_Menu3 INT(10) NULL,   L_Menu4 INT(10) NULL,   UNIQUE INDEX Index_C5CF9FBE_AA3C_4A26 (L_MealType) );  DROP TABLE IF EXISTS UNIS.iMobileKeyAdmin; CREATE TABLE UNIS.iMobileKeyAdmin (   C_ServerDNS VARCHAR(100) NULL,   C_ClientID VARCHAR(45) NULL,   C_Secret VARCHAR(100) NULL,   C_EMail VARCHAR(100) NULL,   C_Password VARCHAR(20) NULL,   C_CountryCode VARCHAR(4) NULL,   C_PhoneNo VARCHAR(12) NULL,   C_SiteCode VARCHAR(5) NULL,   C_MacAddr VARCHAR(12) NULL,   L_tzIndex INT(10) NULL,   L_tzBias INT(10) NULL,   C_tzKeyName VARCHAR(100) NULL,   C_TimeZone VARCHAR(10) NULL,   C_Company VARCHAR(100) NULL,   C_Country VARCHAR(4) NULL,   L_SiteCode INT(10) NULL );  DROP TABLE IF EXISTS UNIS.iPassback; CREATE TABLE UNIS.iPassback (   L_TID INT(10) NULL,   L_RID INT(10) NULL,   C_AreaIn VARCHAR(4) NULL,   C_AreaOut VARCHAR(4) NULL,   L_Type INT(10) NULL,   C_LockoutTime VARCHAR(6) NULL,   C_Remark VARCHAR(255) NULL,   UNIQUE INDEX PK_iPassback (L_TID, L_RID) );  DROP TABLE IF EXISTS UNIS.iTerminalAdmin; CREATE TABLE UNIS.iTerminalAdmin (   L_TID INT(10) NOT NULL,   L_UID INT(10) NOT NULL,   PRIMARY KEY (L_TID, L_UID) );  DROP TABLE IF EXISTS UNIS.iTimezone; CREATE TABLE UNIS.iTimezone (   C_Code VARCHAR(4) NULL,   C_Timezone VARCHAR(8) NULL );  DROP TABLE IF EXISTS UNIS.iUserCard; CREATE TABLE UNIS.iUserCard (   C_CardNum VARCHAR(24) NOT NULL,   L_UID INT(10) NULL,   L_DataCheck INT(10) NULL,   PRIMARY KEY (C_CardNum) );  DROP TABLE IF EXISTS UNIS.iUserFace; CREATE TABLE UNIS.iUserFace (   L_UID INT(10) NULL,   B_Face LONGBLOB NULL,   UNIQUE INDEX PK_iUserFace (L_UID) );  DROP TABLE IF EXISTS UNIS.iUserFinger; CREATE TABLE UNIS.iUserFinger (   L_UID INT(10) NOT NULL,   L_IsWideChar INT(10) NULL,   B_TextFIR LONGBLOB NULL,   PRIMARY KEY (L_UID) );  DROP TABLE IF EXISTS UNIS.iUserMobileKey; CREATE TABLE UNIS.iUserMobileKey (   L_UID INT(10) NULL,   C_MobilePhone VARCHAR(16) NULL,   C_CountryCode VARCHAR(4) NULL,   L_KeyType INT(10) NULL,   C_ImkeyPeriod VARCHAR(24) NULL,   L_issuecount INT(10) NULL,   C_KeyNo VARCHAR(18) NULL,   L_NowIssue INT(10) NULL,   B_UUID LONGBLOB NULL,   UNIQUE INDEX PK_iUserMobileKey (L_UID) );  DROP TABLE IF EXISTS UNIS.iUserPicture; CREATE TABLE UNIS.iUserPicture (   L_UID INT(10) NOT NULL,   B_Picture LONGBLOB NULL,   PRIMARY KEY (L_UID) );  DROP TABLE IF EXISTS UNIS.iWorkTime; CREATE TABLE UNIS.iWorkTime (   C_Code VARCHAR(30) NULL,   C_Mon VARCHAR(8) NULL,   C_Tue VARCHAR(8) NULL,   C_Wed VARCHAR(8) NULL,   C_Thu VARCHAR(8) NULL,   C_Fri VARCHAR(8) NULL,   C_Sat VARCHAR(8) NULL,   C_Sun VARCHAR(8) NULL,   UNIQUE INDEX PK_C_Code (C_Code) );  DROP TABLE IF EXISTS UNIS.iZoneInfo; CREATE TABLE UNIS.iZoneInfo (   L_TID INT(10) NULL,   L_Index INT(10) NULL,   L_Zone INT(10) NULL,   C_Name VARCHAR(30) NULL,   UNIQUE INDEX PK_iZoneInfo (L_TID, L_Index, L_Zone) );  DROP TABLE IF EXISTS UNIS.tAccessGroupShift; CREATE TABLE UNIS.tAccessGroupShift (   L_AdminID INT(10) NULL,   C_Date VARCHAR(8) NULL,   C_Time VARCHAR(6) NULL,   C_Before VARCHAR(4) NULL,   C_After VARCHAR(4) NULL,   L_Result INT(10) NULL,   UNIQUE INDEX PK_tAccessGroupShift (L_AdminID, C_Date, C_Time, C_Before, C_After) );  DROP TABLE IF EXISTS UNIS.tAccessShiftSchedule; CREATE TABLE UNIS.tAccessShiftSchedule (   C_AccessGroup VARCHAR(4) NULL,   C_BasicDate VARCHAR(8) NULL,   L_SpinDays INT(10) NULL,   C_ShiftCode VARCHAR(120) NULL,   UNIQUE INDEX PK_tAccessShiftSchedule (C_AccessGroup) );  DROP TABLE IF EXISTS UNIS.tAuditServer; CREATE TABLE UNIS.tAuditServer (   C_EventTime VARCHAR(14) NULL,   L_LogonID INT(10) NULL,   L_Section INT(10) NULL,   C_Target VARCHAR(30) NULL,   L_Process INT(10) NULL,   L_Detail INT(10) NULL,   C_Remark VARCHAR(128) NULL );  DROP TABLE IF EXISTS UNIS.tAuditTerminal; CREATE TABLE UNIS.tAuditTerminal (   C_EventTime VARCHAR(14) NOT NULL,   L_TID INT(10) NOT NULL,   L_AdminID INT(10) NOT NULL,   C_AdminName VARCHAR(30) NULL,   L_Type INT(10) NOT NULL,   L_UserID INT(10) NOT NULL,   C_UserName VARCHAR(30) NULL,   PRIMARY KEY (C_EventTime, L_TID, L_AdminID, L_Type, L_UserID) );  DROP TABLE IF EXISTS UNIS.tAutoDown; CREATE TABLE UNIS.tAutoDown (   C_RegTime VARCHAR(14) NULL,   L_CID INT(10) NULL,   L_Index INT(10) NULL,   L_Target INT(10) NULL,   L_Process INT(10) NULL,   L_TID INT(10) NULL,   L_UID INT(10) NULL,   C_AccessGroup VARCHAR(4) NULL,   C_OfficeCode VARCHAR(30) NULL,   L_RetryCount INT(10) NULL,   L_DataCheck INT(10) NULL,   UNIQUE INDEX PK_tAutoDown (C_RegTime, L_CID, L_Index, L_RetryCount) );  DROP TABLE IF EXISTS UNIS.tChangedInfo; CREATE TABLE UNIS.tChangedInfo (   C_CreateTime VARCHAR(14) NULL,   L_Target INT(10) NULL,   L_Procedure INT(10) NULL,   L_TargetID INT(10) NULL,   C_TargetCode VARCHAR(30) NULL,   L_ClientID INT(10) NULL,   UNIQUE INDEX PK_tChangedInfo (C_CreateTime, L_Target, L_Procedure, L_TargetID) );  DROP TABLE IF EXISTS UNIS.tClientLog; CREATE TABLE UNIS.tClientLog (   C_EventTime VARCHAR(14) NULL,   L_LogonID INT(10) NULL,   L_Type INT(10) NULL,   L_Result INT(10) NULL );  DROP TABLE IF EXISTS UNIS.tCmdDown; CREATE TABLE UNIS.tCmdDown (   C_RegTime VARCHAR(14) NOT NULL,   L_TID INT(10) NOT NULL,   L_UID INT(10) NOT NULL,   C_Time VARCHAR(14) NULL,   B_Data LONGBLOB NULL,   L_Retry INT(10) NULL,   PRIMARY KEY (C_RegTime, L_TID, L_UID) );  DROP TABLE IF EXISTS UNIS.tCommandDown; CREATE TABLE UNIS.tCommandDown (   C_RegTime VARCHAR(14) NULL,   L_CID INT(10) NULL,   L_TID INT(10) NULL,   L_Index INT(10) NULL,   L_UID INT(10) NULL,   L_CMD INT(10) NULL,   L_DataType INT(10) NULL,   L_DataLen INT(10) NULL,   B_Data LONGBLOB NULL,   L_RetryCount INT(10) NULL,   UNIQUE INDEX PK_tCommandDown (C_RegTime, L_CID, L_TID, L_Index, L_RetryCount) );  DROP TABLE IF EXISTS UNIS.tConfig; CREATE TABLE UNIS.tConfig (   C_MasterPwd VARCHAR(30) NULL,   L_UniqueType INT(10) NULL,   L_AutoDown INT(10) NULL,   C_DownTime VARCHAR(4) NULL,   L_AutoUp INT(10) NULL,   L_RegSameFp INT(10) NULL,   L_FpNum INT(10) NULL,   L_UidCipher INT(10) NULL,   L_TidCipher INT(10) NULL,   L_UniqueCipher INT(10) NULL,   L_MinVID INT(10) NULL,   L_MaxVID INT(10) NULL,   L_tSockPort INT(10) NULL,   L_PollTime INT(10) NULL,   L_SaveMode INT(10) NULL,   L_FirstTime INT(10) NULL,   L_AccessLogDays INT(10) NULL,   L_EventLogDays INT(10) NULL,   L_CmdDownDays INT(10) NULL,   L_Blacklist INT(10) NULL,   L_FireRange INT(10) NULL,   L_FireOpen INT(10) NULL,   L_FireAlarm INT(10) NULL,   L_FireFinish INT(10) NULL,   L_PanicRange INT(10) NULL,   L_PanicOpen INT(10) NULL,   L_PanicAlarm INT(10) NULL,   L_PanicFinish INT(10) NULL,   L_CrisisRange INT(10) NULL,   L_CrisisOpen INT(10) NULL,   L_CrisisAlarm INT(10) NULL,   L_CrisisFinish INT(10) NULL,   L_TransPicture INT(10) NULL,   L_DoorEvent INT(10) NULL,   C_DBVersion VARCHAR(128) NULL,   L_IsUserOverwrite INT(10) NULL,   L_TemplateFormat INT(10) NULL,   L_FingerFormat INT(10) NULL,   L_IsAuthorizedFingerSave INT(10) NULL,   L_IsUnAuthorizedFingerSave INT(10) NULL,   C_FingerPath VARCHAR(255) NULL,   B_MainLogoPicture LONGBLOB NULL,   C_PicturePath VARCHAR(255) NULL,   C_JpegPath VARCHAR(255) NULL,   L_FillFingerEmploye INT(10) NULL,   L_DownFpNum INT(10) NULL,   L_CryptoPWD INT(10) NULL,   C_FingerOrder VARCHAR(10) NULL,   L_BlockingTime INT(10) NULL,   L_DDNS INT(10) NULL,   C_HostName VARCHAR(32) NULL,   C_ContractNo VARCHAR(12) NULL,   L_UpdateTerm INT(10) NULL,   L_EncryptedPacket INT(10) NULL,   L_EncryptedUserinfo INT(10) NULL,   L_UseVID INT(10) NULL,   L_PanicDuress INT(10) NULL,   L_DefaultNotAccess INT(10) NULL,   L_ServerLanguage INT(10) NULL,   L_LFDLevel INT(10) NULL );  DROP TABLE IF EXISTS UNIS.tConnectServer; CREATE TABLE UNIS.tConnectServer (   C_SystemAddr VARCHAR(30) NULL,   C_SystemName VARCHAR(128) NULL,   UNIQUE INDEX PK_tConnectServer (C_SystemAddr) );  DROP TABLE IF EXISTS UNIS.tDailyCount; CREATE TABLE UNIS.tDailyCount (   C_Date VARCHAR(8) NULL,   C_Office VARCHAR(30) NULL,   L_TID INT(10) NULL,   L_Zone INT(10) NULL,   L_SensorNo INT(10) NULL,   L_InCount INT(10) NULL,   L_OutCount INT(10) NULL,   UNIQUE INDEX PK_tDailyCount (C_Date, L_TID, L_Zone, L_SensorNo) );  DROP TABLE IF EXISTS UNIS.tEmploye; CREATE TABLE UNIS.tEmploye (   L_UID INT(10) NOT NULL,   C_IncludeDate VARCHAR(8) NULL,   C_RetiredDate VARCHAR(8) NULL,   C_Office VARCHAR(30) NULL,   C_Post VARCHAR(30) NULL,   C_Staff VARCHAR(30) NULL,   C_Authority VARCHAR(4) NULL,   C_Work VARCHAR(4) NULL,   C_Money VARCHAR(4) NULL,   C_Meal VARCHAR(4) NULL,   C_Phone VARCHAR(255) NULL,   C_Email VARCHAR(255) NULL,   C_Address VARCHAR(255) NULL,   C_Remark VARCHAR(255) NULL,   PRIMARY KEY (L_UID) );  DROP TABLE IF EXISTS UNIS.tEnter; CREATE TABLE UNIS.tEnter (   C_Date VARCHAR(8) NOT NULL,   C_Time VARCHAR(6) NOT NULL,   L_TID INT(10) NOT NULL,   L_UID INT(10) NOT NULL,   C_Name VARCHAR(30) NULL,   C_Unique VARCHAR(20) NULL,   C_Office VARCHAR(30) NULL,   C_Post VARCHAR(30) NULL,   C_Card VARCHAR(24) NULL,   L_UserType INT(10) NULL,   L_Mode INT(10) NULL,   L_MatchingType INT(10) NULL,   L_Result INT(10) NULL,   L_IsPicture INT(10) NULL,   L_Device INT(10) NULL,   L_OverCount INT(10) NULL,   C_Property VARCHAR(8) NULL,   L_JobCode INT(10) NULL,   L_Etc INT(10) NULL,   L_Trans INT(10) NULL,   D_Latitude DOUBLE(15, 5) NULL,   D_Longitude DOUBLE(15, 5) NULL,   C_MobilePhone VARCHAR(15) NULL,   PRIMARY KEY (C_Date, C_Time, L_TID, L_UID),   INDEX IDX_TENTER (L_Trans) );  DROP TABLE IF EXISTS UNIS.tHourlyCount; CREATE TABLE UNIS.tHourlyCount (   C_Date VARCHAR(8) NULL,   L_Time INT(10) NULL,   C_Office VARCHAR(30) NULL,   L_TID INT(10) NULL,   L_SensorNo INT(10) NULL,   L_Zone INT(10) NULL,   L_InCount INT(10) NULL,   L_OutCount INT(10) NULL,   C_CreateDate VARCHAR(14) NULL,   UNIQUE INDEX PK_tHourlyCount (C_Date, L_Time, C_Office, L_TID, L_Zone) );  DROP TABLE IF EXISTS UNIS.tMailConfig; CREATE TABLE UNIS.tMailConfig (   L_MailFlag INT(10) NULL,   C_MailServer VARCHAR(125) NULL,   L_MailPort INT(10) NULL,   C_MailID VARCHAR(127) NULL,   C_MailPWD VARCHAR(127) NULL,   C_MailSender VARCHAR(127) NULL,   C_MailFROM VARCHAR(127) NULL,   C_MailTO VARCHAR(255) NULL,   C_MailCC VARCHAR(255) NULL,   C_MailBCC VARCHAR(255) NULL,   C_MailAttach VARCHAR(255) NULL,   L_Disconnect INT(10) NULL,   L_CoverOpen INT(10) NULL,   L_DoorPick INT(10) NULL,   L_NotClose INT(10) NULL,   L_LockError INT(10) NULL,   L_Emergency INT(10) NULL,   L_External INT(10) NULL,   L_Blacklist INT(10) NULL,   L_CounterError INT(10) NULL,   L_TailGateError INT(10) NULL,   L_Security INT(10) NULL,   L_MatchingFail INT(10) NULL,   L_AttachPicture INT(10) NULL,   L_DuressFinger INT(10) NULL,   L_ACU INT(10) NULL,   L_NoPermission INT(10) NULL );  DROP TABLE IF EXISTS UNIS.tMapImage; CREATE TABLE UNIS.tMapImage (   C_Code VARCHAR(4) NULL,   C_Name VARCHAR(30) NULL,   C_FileName VARCHAR(30) NULL,   L_FileSize INT(10) NULL,   B_FileData LONGBLOB NULL,   UNIQUE INDEX PK_C_Code (C_Code) );  DROP TABLE IF EXISTS UNIS.tMealEnter; CREATE TABLE UNIS.tMealEnter (   C_Date VARCHAR(8) NULL,   C_Time VARCHAR(6) NULL,   L_TID INT(10) NULL,   L_UID INT(10) NULL,   L_MealType INT(10) NULL,   C_Menu VARCHAR(2) NULL,   L_MealPay INT(10) NULL,   C_Reason VARCHAR(2) NULL,   C_Upmode VARCHAR(1) NULL,   L_MealCount INT(10) NULL,   C_StatsDate VARCHAR(8) NULL,   UNIQUE INDEX Index_3444C95F_D5AC_461E (C_Date, C_Time, L_TID, L_UID) );  DROP TABLE IF EXISTS UNIS.tMealInfo; CREATE TABLE UNIS.tMealInfo (   C_Code1 VARCHAR(4) NULL,   C_Code2 VARCHAR(4) NULL,   UNIQUE INDEX Index_9671E141_A14A_434E (C_Code1, C_Code2) );  DROP TABLE IF EXISTS UNIS.tMealType; CREATE TABLE UNIS.tMealType (   C_Code VARCHAR(4) NOT NULL,   C_Name VARCHAR(30) NULL,   L_DayLimit INT(10) NULL,   L_MonthLimit INT(10) NULL,   C_Period VARCHAR(19) NULL,   PRIMARY KEY (C_Code) );  DROP TABLE IF EXISTS UNIS.tMoney; CREATE TABLE UNIS.tMoney (   C_Code VARCHAR(4) NOT NULL,   C_Name VARCHAR(30) NULL,   L_PayUnit INT(10) NULL,   D_WT1Money DOUBLE(15, 5) NULL,   D_WT2Money DOUBLE(15, 5) NULL,   D_WT3Money DOUBLE(15, 5) NULL,   D_WT4Money DOUBLE(15, 5) NULL,   D_WT5Money DOUBLE(15, 5) NULL,   D_WT6Money DOUBLE(15, 5) NULL,   PRIMARY KEY (C_Code) );  DROP TABLE IF EXISTS UNIS.tOrganization; CREATE TABLE UNIS.tOrganization (   L_PatternID INT(10) NULL,   C_PatternName VARCHAR(30) NULL,   C_ParentName VARCHAR(30) NULL,   C_Name VARCHAR(30) NULL,   L_Depth INT(10) NULL );  DROP TABLE IF EXISTS UNIS.tPeopleCountConfig; CREATE TABLE UNIS.tPeopleCountConfig (   C_CompanyName VARCHAR(255) NULL,   B_CompanyLogo LONGBLOB NULL,   L_DataProcess INT(10) NULL,   L_InColor INT(10) NULL,   L_OutColor INT(10) NULL,   L_Zone1Color INT(10) NULL,   L_Zone2Color INT(10) NULL,   L_AutoBatch INT(10) NULL,   L_BatchTime INT(10) NULL,   L_ProcessDay INT(10) NULL,   L_ProcessTotalSum INT(10) NULL );  DROP TABLE IF EXISTS UNIS.tStatusCheck; CREATE TABLE UNIS.tStatusCheck (   L_Status INT(10) NULL );  DROP TABLE IF EXISTS UNIS.tSynchUser; CREATE TABLE UNIS.tSynchUser (   C_EventTime VARCHAR(14) NULL,   L_JobClass INT(10) NULL,   L_JobProc INT(10) NULL,   L_UID INT(10) NULL,   C_Name VARCHAR(30) NULL,   C_Unique VARCHAR(20) NULL,   C_CardNum VARCHAR(24) NULL,   C_UpdateTime VARCHAR(14) NULL,   L_Result INT(10) NULL,   C_Remark VARCHAR(255) NULL );  DROP TABLE IF EXISTS UNIS.tTerminal; CREATE TABLE UNIS.tTerminal (   L_ID INT(10) NOT NULL,   C_Name VARCHAR(30) NULL,   L_FnWork INT(10) NULL,   L_FnMeal INT(10) NULL,   L_FnSchool INT(10) NULL,   C_Office VARCHAR(30) NULL,   C_Place VARCHAR(255) NULL,   C_RegDate VARCHAR(14) NULL,   L_CommType INT(10) NULL,   C_IPAddr VARCHAR(255) NULL,   L_IPPort INT(10) NULL,   L_ComPort INT(10) NULL,   L_Baudrate INT(10) NULL,   L_Passback INT(10) NULL,   C_AreaIn VARCHAR(4) NULL,   C_AreaOut VARCHAR(4) NULL,   C_lastup VARCHAR(14) NULL,   C_Version VARCHAR(255) NULL,   C_Remark VARCHAR(255) NULL,   L_RemoteCtrl INT(10) NULL,   C_MacAddr VARCHAR(12) NULL,   L_tzUseFlag INT(10) NULL,   L_tzIndex INT(10) NULL,   L_tzBias INT(10) NULL,   C_tzKeyName VARCHAR(128) NULL,   L_Status INT(10) NULL,   L_InstallType INT(10) NULL,   L_VTerminalID INT(10) NULL,   L_DataCheck INT(10) NULL,   L_SoftPassback INT(10) NULL,   L_Type INT(10) NULL,   L_AuthType INT(10) NULL,   L_DVRID INT(10) NULL,   L_Chnl1 INT(10) NULL,   L_Chnl2 INT(10) NULL,   PRIMARY KEY (L_ID) );  DROP TABLE IF EXISTS UNIS.tTerminalLog; CREATE TABLE UNIS.tTerminalLog (   C_EventTime VARCHAR(14) NULL,   L_TID INT(10) NULL,   L_Type INT(10) NULL );  DROP TABLE IF EXISTS UNIS.tTerminalStateLog; CREATE TABLE UNIS.tTerminalStateLog (   C_EventTime VARCHAR(14) NULL,   L_TID INT(10) NULL,   L_UID INT(10) NULL,   L_Class INT(10) NULL,   L_Detail INT(10) NULL,   L_SensorNo INT(10) NULL,   L_TargetID INT(10) NULL,   L_Partition INT(10) NULL,   L_Account INT(10) NULL,   L_Qualifier INT(10) NULL,   C_EventInfo VARCHAR(24) NULL,   UNIQUE INDEX PK_tTerminalStateLog (C_EventTime, L_TID, L_UID, L_Class, L_Detail) );  DROP TABLE IF EXISTS UNIS.tUser; CREATE TABLE UNIS.tUser (   L_ID INT(10) NOT NULL,   C_Name VARCHAR(64) NULL,   C_Unique VARCHAR(64) NULL,   L_Type INT(10) NULL,   C_RegDate VARCHAR(14) NULL,   L_OptDateLimit INT(10) NULL,   C_DateLimit VARCHAR(16) NULL,   L_AccessType INT(10) NULL,   C_Password VARCHAR(64) NULL,   L_Identify INT(10) NULL,   L_VerifyLevel INT(10) NULL,   C_AccessGroup VARCHAR(4) NULL,   C_PassbackStatus VARCHAR(4) NULL,   L_VOIPUsed INT(10) NULL,   L_DoorOpen INT(10) NULL,   L_AutoAnswer INT(10) NULL,   L_EnableMeta1 INT(10) NULL,   L_RingCount1 INT(10) NULL,   C_LoginID1 VARCHAR(20) NULL,   C_SipAddr1 VARCHAR(36) NULL,   L_EnableMeta2 INT(10) NULL,   L_RingCount2 INT(10) NULL,   C_LoginID2 VARCHAR(20) NULL,   C_SipAddr2 VARCHAR(36) NULL,   C_UserMessage VARCHAR(128) NULL,   L_Blacklist INT(10) NULL,   L_IsNotice INT(10) NULL,   C_Notice VARCHAR(128) NULL,   C_PassbackTime VARCHAR(14) NULL,   L_ExceptPassback INT(10) NULL,   L_DataCheck INT(10) NULL,   L_Partition INT(10) NULL,   L_FaceIdentify INT(10) NULL,   B_DuressFinger LONGBLOB NULL,   L_AuthValue INT(10) NULL,   L_RegServer INT(10) NULL,   PRIMARY KEY (L_ID) );  DROP TABLE IF EXISTS UNIS.tVisited; CREATE TABLE UNIS.tVisited (   C_Name VARCHAR(30) NULL,   C_Unique VARCHAR(20) NULL,   C_RegDate VARCHAR(14) NULL,   C_LastDate VARCHAR(8) NULL,   C_Company VARCHAR(30) NULL,   C_Info VARCHAR(255) NULL,   C_Phone VARCHAR(255) NULL,   C_Email VARCHAR(255) NULL,   C_Address VARCHAR(255) NULL,   C_Remark VARCHAR(255) NULL );  DROP TABLE IF EXISTS UNIS.tVisitor; CREATE TABLE UNIS.tVisitor (   L_UID INT(10) NOT NULL,   C_Office VARCHAR(30) NULL,   C_Post VARCHAR(30) NULL,   C_Target VARCHAR(30) NULL,   C_Goal VARCHAR(255) NULL,   C_Company VARCHAR(30) NULL,   C_Info VARCHAR(255) NULL,   C_Phone VARCHAR(255) NULL,   C_Email VARCHAR(255) NULL,   C_Address VARCHAR(255) NULL,   C_Remark VARCHAR(255) NULL,   PRIMARY KEY (L_UID) );  DROP TABLE IF EXISTS UNIS.tWiegand; CREATE TABLE UNIS.tWiegand (   C_Code VARCHAR(4) NULL,   C_Name VARCHAR(30) NULL,   L_Type INT(10) NULL,   B_FormatData LONGBLOB NULL );  DROP TABLE IF EXISTS UNIS.tWorkType; CREATE TABLE UNIS.tWorkType (   C_Code VARCHAR(4) NOT NULL,   C_Name VARCHAR(30) NULL,   C_BasicDay VARCHAR(8) NULL,   C_HoliCode VARCHAR(4) NULL,   L_SpinCount INT(10) NULL,   C_ShiftCode VARCHAR(60) NULL,   C_HoliShift VARCHAR(2) NULL,   L_WT1Unit INT(10) NULL,   L_WT1AddTime INT(10) NULL,   L_WT1AddCondi INT(10) NULL,   L_WT1DelTime INT(10) NULL,   L_WT1DelCondi INT(10) NULL,   L_WT1Min INT(10) NULL,   L_WT1Max INT(10) NULL,   L_WT1Rate INT(10) NULL,   L_WT2Unit INT(10) NULL,   L_WT2AddTime INT(10) NULL,   L_WT2AddCondi INT(10) NULL,   L_WT2DelTime INT(10) NULL,   L_WT2DelCondi INT(10) NULL,   L_WT2Min INT(10) NULL,   L_WT2Max INT(10) NULL,   L_WT2Rate INT(10) NULL,   L_WT3Unit INT(10) NULL,   L_WT3AddTime INT(10) NULL,   L_WT3AddCondi INT(10) NULL,   L_WT3DelTime INT(10) NULL,   L_WT3DelCondi INT(10) NULL,   L_WT3Min INT(10) NULL,   L_WT3Max INT(10) NULL,   L_WT3Rate INT(10) NULL,   L_WT4Unit INT(10) NULL,   L_WT4AddTime INT(10) NULL,   L_WT4AddCondi INT(10) NULL,   L_WT4DelTime INT(10) NULL,   L_WT4DelCondi INT(10) NULL,   L_WT4Min INT(10) NULL,   L_WT4Max INT(10) NULL,   L_WT4Rate INT(10) NULL,   L_WT5Unit INT(10) NULL,   L_WT5AddTime INT(10) NULL,   L_WT5AddCondi INT(10) NULL,   L_WT5DelTime INT(10) NULL,   L_WT5DelCondi INT(10) NULL,   L_WT5Min INT(10) NULL,   L_WT5Max INT(10) NULL,   L_WT5Rate INT(10) NULL,   L_WT6Unit INT(10) NULL,   L_WT6AddTime INT(10) NULL,   L_WT6AddCondi INT(10) NULL,   L_WT6DelTime INT(10) NULL,   L_WT6DelCondi INT(10) NULL,   L_WT6Min INT(10) NULL,   L_WT6Max INT(10) NULL,   L_WT6Rate INT(10) NULL,   L_ST1AddTime INT(10) NULL,   L_ST1AddCondi INT(10) NULL,   L_ST1DelTime INT(10) NULL,   L_ST1DelCondi INT(10) NULL,   L_ST1Min INT(10) NULL,   L_ST1Max INT(10) NULL,   L_ST1Trans INT(10) NULL,   L_ST2AddTime INT(10) NULL,   L_ST2AddCondi INT(10) NULL,   L_ST2DelTime INT(10) NULL,   L_ST2DelCondi INT(10) NULL,   L_ST2Min INT(10) NULL,   L_ST2Max INT(10) NULL,   L_ST2Trans INT(10) NULL,   L_ST3AddTime INT(10) NULL,   L_ST3AddCondi INT(10) NULL,   L_ST3DelTime INT(10) NULL,   L_ST3DelCondi INT(10) NULL,   L_ST3Min INT(10) NULL,   L_ST3Max INT(10) NULL,   L_ST3Trans INT(10) NULL,   L_ST4AddTime INT(10) NULL,   L_ST4AddCondi INT(10) NULL,   L_ST4DelTime INT(10) NULL,   L_ST4DelCondi INT(10) NULL,   L_ST4Min INT(10) NULL,   L_ST4Max INT(10) NULL,   L_ST4Trans INT(10) NULL,   L_ST5AddTime INT(10) NULL,   L_ST5AddCondi INT(10) NULL,   L_ST5DelTime INT(10) NULL,   L_ST5DelCondi INT(10) NULL,   L_ST5Min INT(10) NULL,   L_ST5Max INT(10) NULL,   L_ST5Trans INT(10) NULL,   L_ST6AddTime INT(10) NULL,   L_ST6AddCondi INT(10) NULL,   L_ST6DelTime INT(10) NULL,   L_ST6DelCondi INT(10) NULL,   L_ST6Min INT(10) NULL,   L_ST6Max INT(10) NULL,   L_ST6Trans INT(10) NULL,   PRIMARY KEY (C_Code) );  DROP TABLE IF EXISTS UNIS.wExceptRecord; CREATE TABLE UNIS.wExceptRecord (   L_UID INT(10) NULL,   C_Name VARCHAR(30) NULL,   C_Unique VARCHAR(30) NULL,   C_OfficeCode VARCHAR(30) NULL,   C_OfficeName VARCHAR(30) NULL,   C_PostCode VARCHAR(30) NULL,   C_PostName VARCHAR(30) NULL,   C_StaffCode VARCHAR(30) NULL,   C_StaffName VARCHAR(30) NULL,   C_WorkDate VARCHAR(8) NULL,   C_ShiftCode VARCHAR(2) NULL,   C_ShiftName VARCHAR(30) NULL,   L_ExceptType INT(10) NULL,   L_ExceptTM1 INT(10) NULL,   L_ExceptTM2 INT(10) NULL,   L_ExceptTime INT(10) NULL );  DROP TABLE IF EXISTS UNIS.wSpecialShift; CREATE TABLE UNIS.wSpecialShift (   C_WorkDate VARCHAR(8) NULL,   L_UID INT(10) NULL,   C_ShiftCode VARCHAR(2) NULL,   UNIQUE INDEX PK_wSpecialShift (C_WorkDate, L_UID) );  DROP TABLE IF EXISTS UNIS.wTempWorkResult; CREATE TABLE UNIS.wTempWorkResult (   C_WorkDate VARCHAR(8) NULL,   L_UID INT(10) NULL,   L_AccessTime INT(10) NULL,   L_Mode INT(10) NULL,   UNIQUE INDEX PK_wTempWorkResult (C_WorkDate, L_UID, L_Mode) );  DROP TABLE IF EXISTS UNIS.wWorkConfig; CREATE TABLE UNIS.wWorkConfig (   L_MoneyDigit INT(10) NULL,   L_TimeShape INT(10) NULL,   L_MinuteDigit INT(10) NULL,   L_SumPeriodType INT(10) NULL,   L_SumStartDay INT(10) NULL,   L_AutoExecTime INT(10) NULL,   C_LastResultDate VARCHAR(8) NULL,   C_LastSumDate VARCHAR(8) NULL,   L_LastSumWeek INT(10) NULL,   C_NeisSavePath VARCHAR(100) NULL,   L_NeisUsed INT(10) NULL );  DROP TABLE IF EXISTS UNIS.wWorkResult; CREATE TABLE UNIS.wWorkResult (   C_WorkDate VARCHAR(8) NULL,   L_UID INT(10) NULL,   C_Name VARCHAR(30) NULL,   C_Unique VARCHAR(30) NULL,   C_OfficeCode VARCHAR(30) NULL,   C_OfficeName VARCHAR(30) NULL,   C_PostCode VARCHAR(30) NULL,   C_PostName VARCHAR(30) NULL,   C_StaffCode VARCHAR(30) NULL,   C_StaffName VARCHAR(30) NULL,   C_ShiftCode VARCHAR(2) NULL,   C_ShiftName VARCHAR(30) NULL,   L_Complete INT(10) NULL,   L_WorkState INT(10) NULL,   L_InTime INT(10) NULL,   L_OutTime INT(10) NULL,   L_LateTime INT(10) NULL,   L_LackTime INT(10) NULL,   L_MultiRange INT(10) NULL,   L_WT1In INT(10) NULL,   L_WT1Out INT(10) NULL,   L_WT1Late INT(10) NULL,   L_WT1Lack INT(10) NULL,   L_WT1Time INT(10) NULL,   L_WT2In INT(10) NULL,   L_WT2Out INT(10) NULL,   L_WT2Late INT(10) NULL,   L_WT2Lack INT(10) NULL,   L_WT2Time INT(10) NULL,   L_WT3In INT(10) NULL,   L_WT3Out INT(10) NULL,   L_WT3Late INT(10) NULL,   L_WT3Lack INT(10) NULL,   L_WT3Time INT(10) NULL,   L_WT4In INT(10) NULL,   L_WT4Out INT(10) NULL,   L_WT4Late INT(10) NULL,   L_WT4Lack INT(10) NULL,   L_WT4Time INT(10) NULL,   L_WT5In INT(10) NULL,   L_WT5Out INT(10) NULL,   L_WT5Late INT(10) NULL,   L_WT5Lack INT(10) NULL,   L_WT5Time INT(10) NULL,   L_WT6In INT(10) NULL,   L_WT6Out INT(10) NULL,   L_WT6Late INT(10) NULL,   L_WT6Lack INT(10) NULL,   L_WT6Time INT(10) NULL,   D_PayMoney DOUBLE(15, 5) NULL,   L_Modify INT(10) NULL,   C_Remark VARCHAR(255) NULL,   UNIQUE INDEX PK_wWorkResult (C_WorkDate, L_UID) );  DROP TABLE IF EXISTS UNIS.wWorkShift; CREATE TABLE UNIS.wWorkShift (   C_Code VARCHAR(2) NULL,   C_Name VARCHAR(30) NULL,   L_InoutMode INT(10) NULL,   L_RangeTime1 INT(10) NULL,   L_RangeTime2 INT(10) NULL,   L_IgnoreAbsent INT(10) NULL,   L_MultiRange INT(10) NULL,   L_LateTime INT(10) NULL,   L_LackTime INT(10) NULL,   L_AutoInTime INT(10) NULL,   L_AutoOutTime INT(10) NULL,   L_Range1TM1 INT(10) NULL,   L_Range1TM2 INT(10) NULL,   L_Range2TM1 INT(10) NULL,   L_Range2TM2 INT(10) NULL,   L_Range3TM1 INT(10) NULL,   L_Range3TM2 INT(10) NULL,   L_Range4TM1 INT(10) NULL,   L_Range4TM2 INT(10) NULL,   L_ExceptExit INT(10) NULL,   L_ExceptRtnMode INT(10) NULL,   L_ExceptOut INT(10) NULL,   L_ExceptInMode INT(10) NULL,   L_Except1TM1 INT(10) NULL,   L_Except1TM2 INT(10) NULL,   L_Except2TM1 INT(10) NULL,   L_Except2TM2 INT(10) NULL,   L_Except3TM1 INT(10) NULL,   L_Except3TM2 INT(10) NULL,   L_Except4TM1 INT(10) NULL,   L_Except4TM2 INT(10) NULL,   L_Except5TM1 INT(10) NULL,   L_Except5TM2 INT(10) NULL,   L_SF1Work INT(10) NULL,   L_SF1Type INT(10) NULL,   L_SF1Time1 INT(10) NULL,   L_SF1Time2 INT(10) NULL,   L_SF1Range INT(10) NULL,   L_SF1AutoOut INT(10) NULL,   L_SF1Unit INT(10) NULL,   L_SF1Min INT(10) NULL,   L_SF1Max INT(10) NULL,   L_SF1Rate INT(10) NULL,   L_SF2Work INT(10) NULL,   L_SF2Type INT(10) NULL,   L_SF2Time1 INT(10) NULL,   L_SF2Time2 INT(10) NULL,   L_SF2Range INT(10) NULL,   L_SF2AutoOut INT(10) NULL,   L_SF2Unit INT(10) NULL,   L_SF2Min INT(10) NULL,   L_SF2Max INT(10) NULL,   L_SF2Rate INT(10) NULL,   L_SF3Work INT(10) NULL,   L_SF3Type INT(10) NULL,   L_SF3Time1 INT(10) NULL,   L_SF3Time2 INT(10) NULL,   L_SF3Range INT(10) NULL,   L_SF3AutoOut INT(10) NULL,   L_SF3Unit INT(10) NULL,   L_SF3Min INT(10) NULL,   L_SF3Max INT(10) NULL,   L_SF3Rate INT(10) NULL,   L_SF4Work INT(10) NULL,   L_SF4Type INT(10) NULL,   L_SF4Time1 INT(10) NULL,   L_SF4Time2 INT(10) NULL,   L_SF4Range INT(10) NULL,   L_SF4AutoOut INT(10) NULL,   L_SF4Unit INT(10) NULL,   L_SF4Min INT(10) NULL,   L_SF4Max INT(10) NULL,   L_SF4Rate INT(10) NULL,   L_SF5Work INT(10) NULL,   L_SF5Type INT(10) NULL,   L_SF5Time1 INT(10) NULL,   L_SF5Time2 INT(10) NULL,   L_SF5Range INT(10) NULL,   L_SF5AutoOut INT(10) NULL,   L_SF5Unit INT(10) NULL,   L_SF5Min INT(10) NULL,   L_SF5Max INT(10) NULL,   L_SF5Rate INT(10) NULL,   UNIQUE INDEX PK_wWorkShift (C_Code) );  DROP TABLE IF EXISTS UNIS.wWorkSummary; CREATE TABLE UNIS.wWorkSummary (   C_SumDate VARCHAR(8) NULL,   L_UID INT(10) NULL,   C_Name VARCHAR(30) NULL,   C_Unique VARCHAR(30) NULL,   C_OfficeCode VARCHAR(30) NULL,   C_OfficeName VARCHAR(30) NULL,   C_PostCode VARCHAR(30) NULL,   C_PostName VARCHAR(30) NULL,   C_StaffCode VARCHAR(30) NULL,   C_StaffName VARCHAR(30) NULL,   C_WorkCode VARCHAR(4) NULL,   C_WorkName VARCHAR(30) NULL,   C_Period1 VARCHAR(8) NULL,   C_Period2 VARCHAR(8) NULL,   L_Latetime INT(10) NULL,   L_LackTime INT(10) NULL,   L_ST1Late INT(10) NULL,   L_ST1Lack INT(10) NULL,   L_ST1Time INT(10) NULL,   L_ST2Late INT(10) NULL,   L_ST2Lack INT(10) NULL,   L_ST2Time INT(10) NULL,   L_ST3Late INT(10) NULL,   L_ST3Lack INT(10) NULL,   L_ST3Time INT(10) NULL,   L_ST4Late INT(10) NULL,   L_ST4Lack INT(10) NULL,   L_ST4Time INT(10) NULL,   L_ST5Late INT(10) NULL,   L_ST5Lack INT(10) NULL,   L_ST5Time INT(10) NULL,   L_ST6Late INT(10) NULL,   L_ST6Lack INT(10) NULL,   L_ST6Time INT(10) NULL,   D_PayMoney DOUBLE(15, 5) NULL,   L_Modify INT(10) NULL,   C_Remark VARCHAR(255) NULL,   UNIQUE INDEX PK_wWorkSummary (C_SumDate, L_UID) );    SET FOREIGN_KEY_CHECKS = 1; ";
$query_array = explode(";", $query);
for ($i = 0; $i < count($query_array); $i++) {
    // Trim whitespace and check if the query is not empty
    $query_part = trim($query_array[$i]);
    if (!empty($query_part)) {
        if (mysqli_query($conn, $query_part)) {
            echo "Query executed successfully: $query_part<br>";
        } else {
            echo "Error executing query: $query_part<br>" . mysqli_error($conn) . "<br>";
        }
    }
}
$query = "INSERT INTO UNIS.cAccessArea(C_Code, C_Name, L_Flag, C_Remark, C_AccessTime, C_AccessTime2, C_AccessTime3, C_AccessTime4) VALUES ('****', 'None', NULL, NULL, NULL, NULL, NULL, NULL);  INSERT INTO UNIS.cAccessGroup(C_Code, C_Name, L_Flag, C_Remark) VALUES ('****', 'None', NULL, NULL);  INSERT INTO UNIS.cAuthority(C_Code, C_Name, L_SetLocal, L_RegInfo, L_DataBackup, L_MgrTerminal, L_RegControl, L_SetControl, L_RegEmploye, L_ModEmploye, L_OutEmploye, L_RegVisitor, L_OutVisitor, L_RegMoney, L_RegWork, L_SetWork, L_ModWork, L_RegMeal, L_SetMeal, L_ModMeal, L_DelResult, L_DelWork, L_DelMeal, L_MgrScope, L_ChgBlacklist, L_RelBlacklist, L_ModBlacklist, L_DelBlacklist, L_Customized, L_RegAdmin, L_ModAdmin, L_SetShutdown, L_MntMgr, L_MntClient, L_MntTerminal, L_MntAuthLog, L_MntEvent, L_TmnMgr, L_TmnAdd, L_TmnMod, L_TmnDel, L_TmnUpgrade, L_TmnOption, L_TmnAdmin, L_TmnSendFile, L_EmpMgr, L_EmpAdd, L_EmpMod, L_EmpDel, L_EmpSendTerminal, L_EmpTerminalMng, L_EmpRegAdmin, L_VstMgr, L_VstAdd, L_VstMod, L_VstDel, L_BlckMgr, L_BlckChange, L_BlckRelease, L_BlckDel, L_BlckMod, L_AccMgr, L_AccSet, L_MapMgr, L_MapSet, L_TnaMgr, L_TnaSet, L_TnaSpecial, L_TnaWork, L_TnaOutState, L_TnaOutExcRecord, L_TnaSummary, L_TnaSendResult, L_TnaDelData, L_MealMgr, L_MealOutRecord, L_MealDelData, L_MealOutDept, L_MealOutPerson, L_MealSet, L_LogMgr, L_LogOutRecord, L_LogDelRecord, L_SetRegInfo, L_SetMgr, L_SetServer, L_SetPwd, L_SetMail, L_SetEtc) VALUES ('****', 'User', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),   ('1000', 'Post Admin', 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, 1, 1, NULL, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 1, NULL, NULL, NULL, 1, 1, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),   ('2000', 'Terminal Admin', 0, 0, 0, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, 1, NULL, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),   ('3000', 'Office Admin', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, NULL, 1, NULL, NULL, NULL, NULL, NULL),   ('4000', 'Main Admin', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 1, 1, 1, 1, 0, NULL, NULL, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);  INSERT INTO UNIS.cHoliday(C_Code, C_Name, C_Remark) VALUES ('****', 'Unassigned', NULL);  INSERT INTO UNIS.cOffice(C_Code, C_Name) VALUES ('****', 'None');  INSERT INTO UNIS.cPassback(C_Code, C_Name, C_Remark, L_ZoneOut) VALUES ('****', 'None', NULL, NULL);  INSERT INTO UNIS.cPost(C_Code, C_Name) VALUES ('****', 'None');  INSERT INTO UNIS.cStaff(C_Code, C_Name) VALUES ('****', 'None');  INSERT INTO UNIS.cTimezone(C_Code, C_Name, L_Flag, C_Remark, L_AuthType, L_AuthValue) VALUES ('****', 'Unassigned', NULL, NULL, NULL, -1);  INSERT INTO UNIS.tClientLog(C_EventTime, L_LogonID, L_Type, L_Result) VALUES ('20090107193524', 0, 1, 1),   ('20090107193530', 0, 2, 1),   ('20090216163245', 0, 1, 1),   ('20090216163413', 0, 1, 1),   ('20090216163549', 0, 2, 1),   ('20090216163748', 0, 1, 1),   ('20090216164300', 0, 2, 1),   ('20090216164454', 0, 1, 1),   ('20090216164808', 0, 2, 1);  INSERT INTO UNIS.tConfig(C_MasterPwd, L_UniqueType, L_AutoDown, C_DownTime, L_AutoUp, L_RegSameFp, L_FpNum, L_UidCipher, L_TidCipher, L_UniqueCipher, L_MinVID, L_MaxVID, L_tSockPort, L_PollTime, L_SaveMode, L_FirstTime, L_AccessLogDays, L_EventLogDays, L_CmdDownDays, L_Blacklist, L_FireRange, L_FireOpen, L_FireAlarm, L_FireFinish, L_PanicRange, L_PanicOpen, L_PanicAlarm, L_PanicFinish, L_CrisisRange, L_CrisisOpen, L_CrisisAlarm, L_CrisisFinish, L_TransPicture, L_DoorEvent, C_DBVersion, L_IsUserOverwrite, L_TemplateFormat, L_FingerFormat, L_IsAuthorizedFingerSave, L_IsUnAuthorizedFingerSave, C_FingerPath, B_MainLogoPicture, C_PicturePath, C_JpegPath, L_FillFingerEmploye, L_DownFpNum, L_CryptoPWD, C_FingerOrder, L_BlockingTime, L_DDNS, C_HostName, C_ContractNo, L_UpdateTerm, L_EncryptedPacket, L_EncryptedUserinfo, L_UseVID, L_PanicDuress, L_DefaultNotAccess, L_ServerLanguage, L_LFDLevel) VALUES ('1', 1, 0, '2500', 1, 0, 3, 8, 4, 20, 7000, 9000, 9870, 10, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 'C:\\UNIS\\Picture', NULL, 0, 10, 0, '0516273849', 0, 0, NULL, NULL, 0, 0, 0, 0, 1, 0, 0, NULL);  INSERT INTO UNIS.tConnectServer(C_SystemAddr, C_SystemName) VALUES ('192.168.10.246', 'Anuj_Ultrabook');  INSERT INTO UNIS.tMailConfig(L_MailFlag, C_MailServer, L_MailPort, C_MailID, C_MailPWD, C_MailSender, C_MailFROM, C_MailTO, C_MailCC, C_MailBCC, C_MailAttach, L_Disconnect, L_CoverOpen, L_DoorPick, L_NotClose, L_LockError, L_Emergency, L_External, L_Blacklist, L_CounterError, L_TailGateError, L_Security, L_MatchingFail, L_AttachPicture, L_DuressFinger, L_ACU, L_NoPermission) VALUES (0, '', 25, '', '', '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL);  INSERT INTO UNIS.tMealType(C_Code, C_Name, L_DayLimit, L_MonthLimit, C_Period) VALUES ('****', '???', NULL, NULL, NULL);  INSERT INTO UNIS.tMoney(C_Code, C_Name, L_PayUnit, D_WT1Money, D_WT2Money, D_WT3Money, D_WT4Money, D_WT5Money, D_WT6Money) VALUES ('****', '???', NULL, NULL, NULL, NULL, NULL, NULL, NULL);  INSERT INTO UNIS.tPeopleCountConfig(C_CompanyName, B_CompanyLogo, L_DataProcess, L_InColor, L_OutColor, L_Zone1Color, L_Zone2Color, L_AutoBatch, L_BatchTime, L_ProcessDay, L_ProcessTotalSum) VALUES (NULL, '', 0, 16776960, 16711935, 16776960, 16711935, NULL, NULL, NULL, NULL);  INSERT INTO UNIS.tStatusCheck(L_Status) VALUES (0);  INSERT INTO UNIS.tWorkType(C_Code, C_Name, C_BasicDay, C_HoliCode, L_SpinCount, C_ShiftCode, C_HoliShift, L_WT1Unit, L_WT1AddTime, L_WT1AddCondi, L_WT1DelTime, L_WT1DelCondi, L_WT1Min, L_WT1Max, L_WT1Rate, L_WT2Unit, L_WT2AddTime, L_WT2AddCondi, L_WT2DelTime, L_WT2DelCondi, L_WT2Min, L_WT2Max, L_WT2Rate, L_WT3Unit, L_WT3AddTime, L_WT3AddCondi, L_WT3DelTime, L_WT3DelCondi, L_WT3Min, L_WT3Max, L_WT3Rate, L_WT4Unit, L_WT4AddTime, L_WT4AddCondi, L_WT4DelTime, L_WT4DelCondi, L_WT4Min, L_WT4Max, L_WT4Rate, L_WT5Unit, L_WT5AddTime, L_WT5AddCondi, L_WT5DelTime, L_WT5DelCondi, L_WT5Min, L_WT5Max, L_WT5Rate, L_WT6Unit, L_WT6AddTime, L_WT6AddCondi, L_WT6DelTime, L_WT6DelCondi, L_WT6Min, L_WT6Max, L_WT6Rate, L_ST1AddTime, L_ST1AddCondi, L_ST1DelTime, L_ST1DelCondi, L_ST1Min, L_ST1Max, L_ST1Trans, L_ST2AddTime, L_ST2AddCondi, L_ST2DelTime, L_ST2DelCondi, L_ST2Min, L_ST2Max, L_ST2Trans, L_ST3AddTime, L_ST3AddCondi, L_ST3DelTime, L_ST3DelCondi, L_ST3Min, L_ST3Max, L_ST3Trans, L_ST4AddTime, L_ST4AddCondi, L_ST4DelTime, L_ST4DelCondi, L_ST4Min, L_ST4Max, L_ST4Trans, L_ST5AddTime, L_ST5AddCondi, L_ST5DelTime, L_ST5DelCondi, L_ST5Min, L_ST5Max, L_ST5Trans, L_ST6AddTime, L_ST6AddCondi, L_ST6DelTime, L_ST6DelCondi, L_ST6Min, L_ST6Max, L_ST6Trans) VALUES ('****', '???', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);  INSERT INTO UNIS.wWorkConfig(L_MoneyDigit, L_TimeShape, L_MinuteDigit, L_SumPeriodType, L_SumStartDay, L_AutoExecTime, C_LastResultDate, C_LastSumDate, L_LastSumWeek, C_NeisSavePath, L_NeisUsed) VALUES (0, 0, 0, 0, 1, -1, '20100101', '20100101', 0, NULL, NULL);  INSERT INTO UNIS.wWorkShift(C_Code, C_Name, L_InoutMode, L_RangeTime1, L_RangeTime2, L_IgnoreAbsent, L_MultiRange, L_LateTime, L_LackTime, L_AutoInTime, L_AutoOutTime, L_Range1TM1, L_Range1TM2, L_Range2TM1, L_Range2TM2, L_Range3TM1, L_Range3TM2, L_Range4TM1, L_Range4TM2, L_ExceptExit, L_ExceptRtnMode, L_ExceptOut, L_ExceptInMode, L_Except1TM1, L_Except1TM2, L_Except2TM1, L_Except2TM2, L_Except3TM1, L_Except3TM2, L_Except4TM1, L_Except4TM2, L_Except5TM1, L_Except5TM2, L_SF1Work, L_SF1Type, L_SF1Time1, L_SF1Time2, L_SF1Range, L_SF1AutoOut, L_SF1Unit, L_SF1Min, L_SF1Max, L_SF1Rate, L_SF2Work, L_SF2Type, L_SF2Time1, L_SF2Time2, L_SF2Range, L_SF2AutoOut, L_SF2Unit, L_SF2Min, L_SF2Max, L_SF2Rate, L_SF3Work, L_SF3Type, L_SF3Time1, L_SF3Time2, L_SF3Range, L_SF3AutoOut, L_SF3Unit, L_SF3Min, L_SF3Max, L_SF3Rate, L_SF4Work, L_SF4Type, L_SF4Time1, L_SF4Time2, L_SF4Range, L_SF4AutoOut, L_SF4Unit, L_SF4Min, L_SF4Max, L_SF4Rate, L_SF5Work, L_SF5Type, L_SF5Time1, L_SF5Time2, L_SF5Range, L_SF5AutoOut, L_SF5Unit, L_SF5Min, L_SF5Max, L_SF5Rate) VALUES ('**', 'Unassigned', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);";
$query_array = explode(";", $query);
for ($i = 0; $i < count($query_array); $i++) {
    // Trim whitespace and check if the query is not empty
    $query_part = trim($query_array[$i]);
    if (!empty($query_part)) {
        if (mysqli_query($conn, $query_part)) {
            echo "Query executed successfully: $query_part<br>";
        } else {
            echo "Error executing query: $query_part<br>" . mysqli_error($conn) . "<br>";
        }
    }
}
$query = "CREATE TABLE  `UNIS`.`caccessliftoption` (`C_Code` char(4) NOT NULL,`B_LiftFloor` blob,PRIMARY KEY (`C_Code`))";
mysqli_query($conn, $query);

$query = "CREATE TABLE  `UNIS`.`iuseriris` (`L_UID` int(11) DEFAULT NULL,`B_Iris` blob)";
mysqli_query($conn, $query);

$query = "CREATE TABLE  `UNIS`.`thistoryuser` (
  `l_id` int(11) DEFAULT NULL,
  `c_name` varchar(64) DEFAULT NULL,
  `c_unique` varchar(64) DEFAULT NULL,
  `c_regdate` char(14) DEFAULT NULL,
  `c_accessgroup` varchar(4) DEFAULT NULL,
  `L_Authvalue` int(11) DEFAULT NULL,
  `c_office` varchar(30) DEFAULT NULL,
  `c_post` varchar(30) DEFAULT NULL,
  `c_staff` varchar(30) DEFAULT NULL,
  `c_authority` varchar(4) DEFAULT NULL,
  `c_work` char(4) DEFAULT NULL,
  `c_meal` char(4) DEFAULT NULL,
  `hisdate` char(14) DEFAULT NULL,
  `Loginusrnm` varchar(64) DEFAULT NULL,
  `c_usrstat` varchar(200) DEFAULT NULL
)";
mysqli_query($conn, $query);

$query = "GRANT ALL PRIVILEGES ON UNIS.* TO 'unisuser'@'%' WITH GRANT OPTION";
mysqli_query($conn, $query);
$query = "GRANT ALL PRIVILEGES ON UNIS.* TO 'unisuser'@'localhost' WITH GRANT OPTION";
mysqli_query($conn, $query);
$query = "GRANT ALL PRIVILEGES ON *.* TO 'unisuser'@'%'";
mysqli_query($conn, $query);
$query = "GRANT ALL PRIVILEGES ON *.* TO 'unisuser'@'localhost'";
mysqli_query($conn, $query);
$query = "DROP TRIGGER IF EXISTS UNIS.i_tenter ";
mysqli_query($conn, $query);
$query = "CREATE TRIGGER i_tenter AFTER INSERT ON UNIS.tenter FOR EACH ROW INSERT INTO Access.tenter (e_date, e_time, e_id, g_id, e_group) VALUES (New.C_Date, New.C_Time, New.L_UID, New.L_TID, (SELECT tuser.group_id FROM Access.tuser WHERE id = NEW.L_UID)) ";
mysqli_query($conn, $query);
$query = "DROP TRIGGER IF EXISTS UNIS.i_tgate ";
mysqli_query($conn, $query);
$query = "CREATE TRIGGER i_tgate AFTER INSERT ON UNIS.tterminal FOR EACH ROW INSERT INTO Access.tgate (id, name, reg_date) VALUES (New.L_ID, New.C_Name, SUBSTRING(New.C_RegDate, 0,8)) ";
mysqli_query($conn, $query);
$query = "DROP TRIGGER IF EXISTS UNIS.u_tgate ";
mysqli_query($conn, $query);
$query = "CREATE TRIGGER u_tgate AFTER UPDATE ON UNIS.tterminal FOR EACH ROW UPDATE Access.tgate SET name = New.C_Name WHERE id = New.L_ID ";
mysqli_query($conn, $query);
$query = "DROP TRIGGER IF EXISTS UNIS.d_tgate ";
mysqli_query($conn, $query);
$query = "CREATE TRIGGER d_tgate AFTER DELETE ON UNIS.tterminal FOR EACH ROW DELETE FROM Access.tgate WHERE id = old.L_ID ";
mysqli_query($conn, $query);
$query = "DROP TRIGGER IF EXISTS UNIS.i_tuser ";
mysqli_query($conn, $query);
$query = "CREATE TRIGGER i_tuser AFTER INSERT ON UNIS.tuser FOR EACH ROW INSERT INTO Access.tuser (id, name, reg_date, datelimit, dept) VALUES (New.L_ID, New.C_Name, IFNULL(SUBSTRING(New.C_RegDate, 0, 8), 20010101), IFNULL(CONCAT('N', New.C_DateLimit), 'N2001010120200101'), '.') ";
mysqli_query($conn, $query);
$query = "DROP TRIGGER IF EXISTS UNIS.u_tuser ";
mysqli_query($conn, $query);
$query = "CREATE TRIGGER u_tuser AFTER UPDATE ON UNIS.tuser FOR EACH ROW UPDATE Access.tuser SET name = New.C_Name, reg_date = SUBSTRING(New.C_RegDate, 0,8) WHERE id = New.L_ID ";
mysqli_query($conn, $query);
$query = "DROP TRIGGER IF EXISTS UNIS.d_tuser ";
mysqli_query($conn, $query);
$query = "CREATE TRIGGER d_tuser AFTER DELETE ON UNIS.tuser FOR EACH ROW DELETE FROM Access.tuser WHERE id = old.L_ID ";
mysqli_query($conn, $query);

$query = "CREATE DATABASE AccessArchive";
mysqli_query($conn, $query);
$query = "CREATE  TABLE  AccessArchive.archive_tenter (  e_date varchar( 8  )  NOT  NULL DEFAULT  '', e_time varchar( 6  )  NOT  NULL DEFAULT  '', g_id int( 10  )  NOT  NULL DEFAULT  '0', e_id int( 10  )  NOT  NULL DEFAULT  '0', e_name varchar( 30  )  DEFAULT NULL , e_idno varchar( 30  )  DEFAULT NULL , e_group smallint( 5  )  NOT  NULL DEFAULT  '0', e_user varchar( 1  )  DEFAULT NULL , e_mode varchar( 1  )  DEFAULT NULL , e_type varchar( 1  )  DEFAULT NULL , e_result varchar( 1  )  DEFAULT NULL , e_etc varchar( 1  )  DEFAULT NULL , ed int( 10  )  NOT  NULL  DEFAULT  '0', p_flag int( 10  )  NOT  NULL DEFAULT  '0', e_uptime varchar( 14  )  DEFAULT NULL , e_upmode varchar( 1  )  DEFAULT NULL , PRIMARY  KEY (  e_date ,  e_time ,  g_id ,  e_id  ) , KEY  e_group (  e_group  ) , KEY  ed (  ed  ) , KEY  e_date (  e_date  ) , KEY  e_time (  e_time  ) , KEY  g_id (  g_id  ) , KEY  e_id (  e_id  ) , KEY  p_flag (  p_flag  ) , KEY  e_etc (  e_etc  )  ) ";
mysqli_query($conn, $query);
$query = "CREATE  TABLE  AccessArchive.archive_am (  AttendanceID int( 10  )  NOT  NULL  DEFAULT  '0' , EmployeeID int( 10  )  NOT  NULL DEFAULT  '0', EmpID varchar( 10  )  DEFAULT NULL , group_id int( 10  )  NOT  NULL DEFAULT  '0', group_min int( 10  )  NOT  NULL DEFAULT  '0', ADate int( 10  )  NOT  NULL DEFAULT  '0', Week int( 10  )  NOT  NULL DEFAULT  '0', EarlyIn int( 10  )  NOT  NULL DEFAULT  '0', LateIn int( 10  )  NOT  NULL DEFAULT  '0', Break int( 10  )  NOT  NULL DEFAULT  '0', LessBreak int( 10  )  NOT  NULL DEFAULT  '0', MoreBreak int( 10  )  NOT  NULL DEFAULT  '0', EarlyOut int( 10  )  NOT  NULL DEFAULT  '0', LateOut int( 10  )  NOT  NULL DEFAULT  '0', Normal int( 10  )  NOT  NULL DEFAULT  '0', Grace int( 10  )  NOT  NULL DEFAULT  '0', Overtime int( 10  )  NOT  NULL DEFAULT  '0', AOvertime int( 10  )  NOT  NULL DEFAULT  '0', Day varchar( 50  )  DEFAULT NULL , Flag varchar( 10  )  NOT  NULL DEFAULT  'Black', p_flag int( 11  )  NOT  NULL DEFAULT  '0', LateIn_flag int( 1  )  NOT  NULL DEFAULT  '0', EarlyOut_flag int( 1  )  NOT  NULL DEFAULT  '0', MoreBreak_flag int( 1  )  NOT  NULL DEFAULT  '0', OT1 varchar( 255  )  NOT  NULL DEFAULT  'Saturday', OT2 varchar( 255  )  NOT  NULL DEFAULT  'Sunday', NightFlag int( 11  )  NOT  NULL DEFAULT  '0', RotateFlag int( 11  )  NOT  NULL DEFAULT  '0', Remark varchar( 1024  )  NOT  NULL DEFAULT  '.', PRIMARY  KEY (  AttendanceID  ) , UNIQUE  KEY  AEA (  EmployeeID ,  ADate  ) , KEY  g_id (  group_id  ) , KEY  EmployeeID (  EmployeeID  )  ) ";
mysqli_query($conn, $query);
$query = "CREATE  TABLE  AccessArchive.archive_dm (  DayMasterID int( 10  )  NOT  NULL  DEFAULT  '0' , e_id int( 10  )  NOT  NULL DEFAULT  '0', TDate int( 10  )  NOT  NULL DEFAULT  '0', archive_dm.Entry varchar( 6  )  DEFAULT NULL , Start varchar( 6  )  DEFAULT NULL , BreakOut varchar( 6  )  DEFAULT NULL , BreakIn varchar( 6  )  DEFAULT NULL , Close varchar( 6  )  DEFAULT NULL , archive_dm.Exit varchar( 6  )  DEFAULT NULL , p_flag int( 11  )  NOT  NULL DEFAULT  '0', group_id int( 10  )  NOT  NULL DEFAULT  '0', Flag varchar( 10  )  NOT  NULL DEFAULT  'Black', Work int( 11  )  NOT  NULL DEFAULT  '0', PRIMARY  KEY (  DayMasterID  ) , UNIQUE  KEY  DET (  e_id ,  TDate  ) , KEY  p_flag (  p_flag  ) , KEY  e_id (  e_id  )  )";
mysqli_query($conn, $query);
$query = "CREATE TABLE AccessArchive.archive_trans (TransactID int( 10 ) NOT NULL AUTO_INCREMENT , Transactdate int( 10 ) NOT NULL DEFAULT '0', Transacttime int( 10 ) NOT NULL DEFAULT '0', Username varchar( 255 ) DEFAULT NULL , Transactquery varchar( 1024 ) NOT NULL , PRIMARY KEY ( TransactID ) , KEY TransactID ( TransactID ) )";
mysqli_query($conn, $query);
$query = "GRANT ALL PRIVILEGES ON accessarchive.* TO 'fdmsusr'@'localhost' WITH GRANT OPTION";
mysqli_query($conn, $query);
$query = "GRANT ALL PRIVILEGES ON accessarchive.* TO 'fdmsusr'@'%' WITH GRANT OPTION";
mysqli_query($conn, $query);
    
$query = "SET PASSWORD FOR 'root'@'localhost' = PASSWORD('namaste')";
mysqli_query($conn, $query);