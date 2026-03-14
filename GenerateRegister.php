<?php
ob_start("ob_gzhandler");
error_reporting(E_ALL);
include "Functions.php";
$conn = new mysqli("localhost", "root", "namaste", "access_lfz");
$unisConn = new mysqli("localhost", "root", "namaste", "unis_lftz");

// Check connection
if ($conn->connect_errno) {
    echo "Failed to connect to MySQL: " . $conn->connect_error;
    exit();
}

if (isset($_GET['reg'])) {
    $accessQuery = "Select fullname from onboardrequest where id=".$_GET['reg']." and status_reg=0";
    $accessResult = mysqli_query($conn, $accessQuery);
    $fullname = mysqli_fetch_assoc($accessResult);
   
    $unisQuery = "Select MAX(L_ID) as lastid from tuser";
    $unisResult = mysqli_query($unisConn, $unisQuery);
    $lastInsertedID = mysqli_fetch_assoc($unisResult);
    $addVal = 1;
    $employeeId = $lastInsertedID['lastid'] + (int) $addVal;
    
    $name = $fullname['fullname'];
    $insertQuery = "Insert into tuser(L_ID,C_Name)values($employeeId,'$name')";
    $insertResult = mysqli_query($unisConn, $insertQuery);
    
    if($insertResult){
        $unisInsert = "Record inserted in unis successfully";
    }else{
//        echo "MySQLi Error: " . mysqli_error($unisConn);
        if(mysqli_error($unisConn) == "Duplicate entry '$employeeId' for key 'PRIMARY'"){
//            echo $alterQuery = "ALTER TABLE tuser AUTO_INCREMENT = $employeeId";
            $newEmpID = $employeeId + 1;
            $alterQuery = "Insert into tuser(L_ID,C_Name)values($newEmpID,'$name')";
            $alterResult = mysqli_query($unisConn, $alterQuery);
            if($alterResult){
                $unisMsg = "Record Inserted in unis Successfully";
            }
        }
    }

    $date = date('Y-m-d h:i:s', time());
    $updateQuery = "Update onboardrequest SET employee_id=$employeeId,updated_at='$date', status_reg=1 where id=".$_GET['reg'];
    $result = mysqli_query($conn, $updateQuery);
    if($result){
        $accessUpdate = "Record Updated In Onboard Successfully";
    }else{
        $var = 4;
        $accessError = "Record Not Updated In Onboard";
    }
    
    header('Location:CasualOnboardInfoView.php?status=1&status=2&status=3&status='.$var);
}


