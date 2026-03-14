<?php

ob_start("ob_gzhandler");
error_reporting(E_ALL);
set_time_limit(0);
include "Functions.php";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$conn = openConnection();
$iconn = openIConnection();
$uconn = mysqli_connect("localhost", "root", "namaste", "access");
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}
$oconn = mssql_connection("DESKTOP-I1EBN6N\SQLEXPRESS", "HRM", "sa", "namaste");
echo "\n\r Connected to MSSQL: " . $oconn;

//$empQuery = "SELECT Is_Separate,Emp_BlackListDate,Emp_Payroll_No from dbo.tblEmployee";
$empQuery = "SELECT * from dbo.tblEmployee";
$empResult = mssql_query($empQuery, $oconn);

while ($empRow = mssql_fetch_array($empResult)) {
    if ($empRow['Is_Separate'] == 1) {
        $seperation[] = $empRow['Emp_Payroll_No'];
        $blacklistDate[] = $empRow['Emp_BlackListDate'];
    }
}

$tuserQuery = "SELECT id from tuser";
$tuserResult = mysqli_query($uconn, $tuserQuery);
while ($tuserRow = mysqli_fetch_assoc($tuserResult)) {
    $tuserID[] = $tuserRow['id'];
}

foreach ($tuserID as $sepEmployee) {
    if (in_array($sepEmployee, $seperation)) {
        $query = "UPDATE tuser SET PassiveType='RSN' where id='$sepEmployee'";
        $resultUpdate = mysqli_query($uconn, $query);
        if ($resultUpdate) {
            echo "Data Updated Successfully.";
        }
    }
    if (!in_array($sepEmployee, $seperation)) {
        $seperationData = array_diff($seperation, $tuserID);
        $j=0;
        for ($i = 0; $i <= count($seperationData); $i++) {
            $query = "INSERT INTO tuser (id,PassiveType)values('" . $seperationData[$i] . "','RSN')";
            $resultInsert = mysqli_query($uconn, $query);
            if ($resultInsert) {
                echo "Data Added Successfully.";
            }
        }
    }
}