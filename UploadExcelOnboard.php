<?php
ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "31";
set_time_limit(0);
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];

$conn = new mysqli("localhost", "root", "namaste", "access_lftz");

// Check connection
if ($conn->connect_errno) {
    echo "Failed to connect to MySQL: " . $conn->connect_error;
    exit();
}

$csvFile = "onboardupload.csv";

if (($handle = fopen($csvFile, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
//        echo "<pre>";print_R($data);
        // Assuming your CSV has columns: col1, col2, col3, etc.
        $col1 = $data[0];
        $col2 = $data[1];
        $col3 = $data[2];
        $date = date('Y-m-d h:i:s');
        // Insert data into MySQL table
        echo $sql = "INSERT INTO onboardrequest (employee_id, fullname, grade, status_reg, created_at) VALUES ('$col2', '$col1', '$col3','1','$date')";
        echo "<br>";
        if (mysqli_query($conn, $sql) === TRUE) {
            echo "Record inserted successfully<br>";
        } else {
            echo "Error inserting record: " . $conn->error . "<br>";
        }
    }
    fclose($handle);
} else {
    echo "Error opening CSV file<br>";
}

$conn->close();
