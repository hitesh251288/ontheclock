<?php
ob_start("ob_gzhandler");
error_reporting(E_ALL);
include "Functions.php";
$conn = new mysqli("localhost", "root", "namaste", "access_lfz");

// Check connection
if ($conn->connect_errno) {
    echo "Failed to connect to MySQL: " . $conn->connect_error;
    exit();
}

if (isset($_POST['submit'])) {
//    echo "<pre>";print_R($_POST);
    $arrData = $_POST['addmore'];
    for ($i = 0; $i < count($arrData); $i++) {
        $activity = $_POST['activity'];
        $grade = $arrData[$i]['grades'];
        $regularRate = $arrData[$i]['regrate'];
        $otRateRegular = $arrData[$i]['ot_regular'];
        $otRateHoliday = $arrData[$i]['ot_holiday'];
        $regularDayRate = $arrData[$i]['regdayrate'];

        echo $insertQuery = "insert into hourlywagescasual(activity,grades,regrate,ot_regular,"
        . "ot_holiday,regdayrate)values('$activity','$grade','$regularRate','$otRateRegular',"
        . "'$otRateHoliday','$regularDayRate')";
//        echo "<br>";
        $insertResult = mysqli_query($conn, $insertQuery);
    }
    if ($insertResult) {
        echo "Data Inserted Successfully";
    } else {
        echo "There is an error to add data";
        $msgVar = 2;
    }
    header('Location:GradeRateMasterView.php?message=1&message='.$msgVar);
}