<?php 
ob_start("ob_gzhandler");
error_reporting(E_ALL);
date_default_timezone_set("Africa/Algiers");
//include "Functions.php";
$txtDBIP = "localhost";
$txtDBName = "access";
$txtDBUser = "root";
$txtDBPass = "namaste";

$tamaster_conn = mysqli_connect($txtDBIP, $txtDBUser, $txtDBPass, $txtDBName);
// Check connection
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  exit();
}

$assignEmpquery = "CREATE TABLE IF NOT EXISTS `userf3` (
  `UserF3` int(10) NOT NULL AUTO_INCREMENT,
  `Username` varchar(255) DEFAULT NULL,
  `F3` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`UserF3`),
  KEY `F3` (`F3`),
  KEY `Username` (`Username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;";

$assignEmpResult = mysqli_query($tamaster_conn, $assignEmpquery);

$licensequery = "CREATE TABLE IF NOT EXISTS `licensehistory` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;";

$licenseResult = mysqli_query($tamaster_conn, $licensequery);

$wageQuery = "CREATE TABLE IF NOT EXISTS `wagesmaster` (
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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1";
$wageResult = mysqli_query($tamaster_conn, $wageQuery);

$altertable = "ALTER TABLE `othersettingmaster` ADD `CompanyDetail3` ENUM('IN<3','JN<3') NULL default NULL,ADD COLUMN `CompanyDetail4` VARCHAR(1000) NOT NULL";
$alterResult = mysqli_query($tamaster_conn, $altertable);

$updateData = "UPDATE OtherSettingMaster SET CompanyName = ''";
$updateResult = mysqli_query($tamaster_conn, $updateData);

if($licenseResult){
    header("Location:Login.php");
}


?>