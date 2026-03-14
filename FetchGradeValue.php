<?php
ob_start("ob_gzhandler");
error_reporting(E_ERROR);

$conn = new mysqli("localhost", "root", "namaste", "access_lfz");

// Check connection
if ($conn->connect_errno) {
    echo "Failed to connect to MySQL: " . $conn->connect_error;
    exit();
}

if (isset($_POST['selectedValue'])) {
    $selectedValue = $_POST['selectedValue'];
    if($selectedValue == 'S1' || $selectedValue == 'S2' || $selectedValue == 'S3' || $selectedValue == 'S4' || $selectedValue == 'S5' || $selectedValue == 'IT1' || $selectedValue == 'TP1' ||  $selectedValue == 'TP2' || $selectedValue == 'DG1' || $selectedValue == 'DG2'){
        $hrrateQuery = "SELECT regdayrate FROM hourlywagescasual WHERE grades = '$selectedValue'";
    }else{
        $hrrateQuery = "SELECT regrate FROM hourlywagescasual WHERE grades = '$selectedValue'";
    }
    $hrrateResult = mysqli_query($conn, $hrrateQuery);
    if ($hrrateResult) {
        $regrateRow = mysqli_fetch_assoc($hrrateResult);
        echo $regrateRow['regrate'];
        echo $regrateRow['regdayrate'];
    } else {
        echo 'No matching value found';
    }
}
$conn->close();
?>






