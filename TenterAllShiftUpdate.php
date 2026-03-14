<?php
error_reporting(E_ERROR);
ini_set('display_errors', '1');

// MySQL connection
$mysql_conn = mysqli_connect("localhost", "root", "namaste", "access");
if (!$mysql_conn) die("MySQL Connection Failed: " . mysqli_error($mysql_conn));

// Log file
$log_file = "update_group_log.txt";
$update_count = 0;

// Fetch all tenter records
$sql = "SELECT e_id, e_date, e_time, g_id FROM tenter";
$result = mysqli_query($mysql_conn, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    $e_id   = $row['e_id'];
    $e_date = $row['e_date'];
    $e_time = $row['e_time'];
    $g_id   = $row['g_id'];

    $e_group = 0; // default if not found

    // Look up group in shiftroster
    $shift_query = "SELECT e_group 
                    FROM shiftroster 
                    WHERE e_id = '$e_id' 
                      AND e_date = '$e_date' 
                    LIMIT 1";
    $shift_result = mysqli_query($mysql_conn, $shift_query);

    if ($shift_result && mysqli_num_rows($shift_result) > 0) {
        $shift_row = mysqli_fetch_assoc($shift_result);
        $e_group = $shift_row['e_group'];
    }

    // Update tenter
    $update_sql = "UPDATE tenter 
                   SET e_group = '$e_group' 
                   WHERE e_id = '$e_id' 
                     AND e_date = '$e_date' 
                     AND e_time = '$e_time' 
                     AND g_id = '$g_id' 
                   LIMIT 1";
    if (mysqli_query($mysql_conn, $update_sql)) {
        $update_count++;
    } else {
        file_put_contents($log_file, "Update failed for e_id $e_id, date $e_date: " . mysqli_error($mysql_conn) . "\n", FILE_APPEND);
    }
}

echo "Updated e_group for $update_count records.\n";

mysqli_close($mysql_conn);
?>
