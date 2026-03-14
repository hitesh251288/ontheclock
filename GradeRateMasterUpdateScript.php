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

if(isset($_POST['update'])){
    $id = $_POST['id'];
    $activity = $_POST['activity'];
    $grades = $_POST['grades'];
    $regrate = $_POST['regrate'];
    $ot_regular = $_POST['ot_regular'];
    $ot_holiday = $_POST['ot_holiday'];
    $regdayrate = $_POST['regdayrate'];
    $date = date('Y-m-d h:i:s', time());
    
    $updateQuery = "update hourlywagescasual set activity='$activity',grades='$grades',"
            . "regrate='$regrate',ot_regular='$ot_regular',ot_holiday='$ot_holiday',"
            . "regdayrate='$regdayrate',updated_at='$date' where id=".$id;
    $hrwagesResult = mysqli_query($conn, $updateQuery);
    if($hrwagesResult){
        echo "Data Updated Successfully";
    }else{
        echo "Problem to update data";
        $statusVar = 2;
    }
    header('Location:GradeRateMasterView.php?status=1&status='.$statusVar);
}