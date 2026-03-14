<?php

//echo "<pre>";print_R(get_loaded_extensions());

$con = mysqli_connect("41.222.233.193", "e@push$13S", "M!ohin@ni@007", "epush", 3363);
$cons = mysqli_connect("localhost", "root", "namaste", "access", 3306);
if (!$con) {
    die("Could not connect" . mysqli_connect_error());
} else {
    echo "Connected";
}


$query = "SELECT * FROM DeviceLogs_Processed where UserId=25339";
$result = mysqli_query($con, $query);
//$k = 0;
while ($row = mysqli_fetch_array($result)) {
    $data[] = $row;
    $userID = $row['UserId'];
    $date = strtotime($row['LogDate']);
    $dateonly = date('Ymd', $date);
    $time = date('His', $date);
    $processData[] = array(
        'e_date' => $dateonly,
        'e_time' => $time,
        'e_id' => $userID,
    );

    $matchTenter = "SELECT e_date,e_time,e_id from tenter where e_id=$userID AND e_date=$dateonly AND e_time=$time";
//    echo "<br>";
    $matchTenterResult = mysqli_query($cons, $matchTenter);
    $rowcount = mysqli_num_rows($matchTenterResult);

    if($rowcount == 0){
        echo $iQuery = "INSERT INTO tenter(e_date,e_time,e_id)values('$dateonly','$time','$userID')";
        echo "<br>";
        echo "HEY";
        $iResult = mysqli_query($cons, $iQuery);
        if($iResult){
            echo "Data inserted successfully";
        }else{
            echo "Datas not inserted";
//            exit();
        }
    }
}

// where EmployeeCode=25339
$empQuery = "SELECT * from Employees";
$empResult = mysqli_query($con, $empQuery);
while ($empRow = mysqli_fetch_array($empResult)) {
//    $empData[] = $empRow;
    $dateofjoin = strtotime($empRow['DOJ']);
    $doj = date('Ymd', $dateofjoin);
    $name = $empRow['EmployeeName'];
    $id = $empRow['EmployeeCode'];
    $gender = $empRow['Gender'];
    $status = $empRow['Status'];
    if ($status == 'Working') {
        $act = 'ACT';
    } else {
        $act = '';
    }
    $fetchempQuery = "SELECT id from tuser where id=$id";
    $matchTuserResult = mysqli_query($cons, $fetchempQuery);
    $rowcountTuser = mysqli_num_rows($matchTuserResult);
    if($rowcountTuser == 0){
        echo $empInsert = "INSERT INTO tuser(id,name,reg_date,idno,PassiveType)values($id,'$name','$doj','$gender','$act')";
        echo "<br>";
        $empFetchResult = mysqli_query($cons, $empInsert);
        if($empFetchResult) {
            echo "Data inserted successfully";
        } else {
            echo "Data not inserted";
//            exit();
        }
    }
    
}