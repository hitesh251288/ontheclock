<?php
ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
set_time_limit(0);
session_start();

//$conn = openConnection();
$conn = new mysqli("localhost", "root", "namaste", "access_lfz");

// Check connection
if ($conn->connect_errno) {
    echo "Failed to connect to MySQL: " . $conn->connect_error;
    exit();
}

if(isset($_POST['update'])){
//    echo "<pre>";print_R($_POST);exit;
    $id = $_POST['id'];
    $prj1 = $_POST['prj1'];
    $prj2 = $_POST['prj2'];
    $prj3 = $_POST['prj3'];
    $prj4 = $_POST['prj4'];
    $prj5 = $_POST['prj5'];
    $prj1hrs = $_POST['project1hrs'];
    $prj2hrs = $_POST['project2hrs'];
    $prj3hrs = $_POST['project3hrs'];
    $prj4hrs = $_POST['project4hrs'];
    $prj5hrs = $_POST['project5hrs'];
    $totalprjhrs = $_POST['totalprjhrs'];
    $transithrs = $_POST['transithrs'];
    $remark = $_POST['remark'];
    $currDate = date('y-m-d H:i:s');
    
    if($_POST['project1hrsamt']){
        $prj1hrsamount = $_POST['project1hrsamt'];
        $prj1hramt = " prj1hrsamt='$prj1hrsamount',";
    }
    if($_POST['project2hrsamt']){
        $prj2hrsamount = $_POST['project2hrsamt'];
        $prj2hramt = " prj2hrsamt='$prj2hrsamount',";
    }
    if($_POST['project3hrsamt']){
        $prj3hrsamount = $_POST['project3hrsamt'];
        $prj3hramt = " prj3hrsamt='$prj3hrsamount',";
    }
    if($_POST['project4hrsamt']){
        $prj4hrsamount = $_POST['project4hrsamt'];
        $prj4hramt = " prj4hrsamt='$prj4hrsamount',";
    }
    if($_POST['project5hrsamt']){
        $prj5hrsamount = $_POST['project5hrsamt'];
        $prj5hramt = " prj5hrsamt='$prj5hrsamount',";
    }
    if($_POST['otamount']){
        $otamount = $_POST['otamount'];
        $otamount = ",otamount='$otamount' ";
    }
    if($_POST['regulardayrate']){
        $regulardayrate = $_POST['regulardayrate'];
        $regulardayrate = ",regulardayrate='$regulardayrate' ";
    }
    
    $updatehrsallocQuery = "update projecthrsallocation SET project1='$prj1', project2='$prj2', "
        . "project3='$prj3', project4='$prj4', project5='$prj5', project1hrs='$prj1hrs', $prj1hramt"
        . "project2hrs='$prj2hrs',$prj2hramt project3hrs='$prj3hrs',$prj3hramt project4hrs='$prj4hrs',$prj4hramt project5hrs='$prj5hrs',$prj5hramt "
        . "totalprjhrs='$totalprjhrs', transithrs='$transithrs', remark='$remark', updated_at='$currDate' $otamount $regulardayrate where id=".$id;
    $updatehrsallocResult = mysqli_query($conn, $updatehrsallocQuery);
    
    if($updatehrsallocResult){
        echo "Data Updated Successfully";
        $updatest = 'status=1';
    }else{
        echo "Problem occur to update data";
        $updatest = 'status=2';
    }
    if($_POST['iddata'] != ''){
        header('Location: PmFmProjectDetails.php?'.$updatest);
    }else{
        header('Location: ProjectHoursAllocationView.php?'.$updatest);
    }
}

