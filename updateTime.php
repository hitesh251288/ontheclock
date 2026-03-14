<?php
$conn = mysqli_connect("localhost","root","namaste","access");

$empId   = $_POST['empId'];
$attDate = $_POST['attDate'];
$oldTime = $_POST['oldTime']; // value before editing (HH:MM:SS)
$newTime = $_POST['newTime']; // new edited value (HH:MM:SS)

// Convert to DB format if needed (HHMMSS)
$oldTime = $oldTime ? str_replace(":", "", $oldTime) : "";
$newTime = $newTime ? str_replace(":", "", $newTime) : "";



// Decide if it's InTime (MIN) or OutTime (MAX)
$minQuery = "SELECT MIN(e_time) AS minTime, MAX(e_time) AS maxTime 
             FROM tenter 
             WHERE e_id='$empId' AND e_date='$attDate'";
$res = mysqli_query($conn, $minQuery);
$row = mysqli_fetch_assoc($res);
$minTime = str_replace(":", "", $row['minTime']);
$maxTime = str_replace(":", "", $row['maxTime']);
if ($oldTime == $minTime) {
    $query = "UPDATE tenter 
              SET e_time = '$newTime'
              WHERE e_id='$empId' 
                AND e_date='$attDate' 
                AND e_time='$minTime'
              LIMIT 1";
} elseif ($oldTime == $maxTime) {
    $query = "UPDATE tenter 
              SET e_time = '$newTime'
              WHERE e_id='$empId' 
                AND e_date='$attDate' 
                AND e_time='$maxTime'
              LIMIT 1";
} else {
    echo "Invalid update (debug: old=$oldTime, min=$minTime, max=$maxTime)";
    exit;
}

if (mysqli_query($conn, $query)) {
    echo "Updated successfully";
} else {
    echo "Error: " . mysqli_error($conn);
}


?>