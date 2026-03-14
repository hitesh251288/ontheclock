<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

// SQL Server Connection (ODBC)
$server = "WIN-7MBH2MJSGT8\SQLEXPRESS05";
$database = "iASWebSetup";
$user = "sa";
$password = "admin@123";
$connection_string = "Driver={ODBC Driver 17 for SQL Server};Server=$server;Database=$database;";
$odbc_conn = odbc_connect($connection_string, $user, $password);
if (!$odbc_conn) die("ODBC Connection failed: " . odbc_errormsg());

// Connect to MySQL
$mysql_conn = mysqli_connect("localhost", "root", "namaste", "access");
if (!$mysql_conn) {
    die("MySQL connection failed: " . mysqli_connect_error());
}

$queue_sql = "SELECT ID_NO, DeviceName FROM tblmachine";

$queue_result = odbc_exec($odbc_conn, $queue_sql);
if (!$queue_result) die("Failed to fetch queue: " . odbc_errormsg());
while ($queue_row = odbc_fetch_array($queue_result)) {
        $id_no = $queue_row['ID_NO'];
        $branch = $queue_row['DeviceName'];

        // Insert into MySQL (avoid duplicates)
        $query = "
            INSERT INTO tgate (id, name, floor, antipass, antipass_level, antipass_mode, Meal)
            VALUES ('" . $id_no . "',
                    '" . $branch . "',
                    0, 0, 0, 0, 0)
            ON DUPLICATE KEY UPDATE name=VALUES(name)
        ";

        if (!mysqli_query($mysql_conn, $query)) {
            error_log("❌ Insert failed for ID $id_no: " . mysqli_error($mysql_conn));
        }
    }
    echo "✅ Data sync completed.";

// Cleanup
odbc_close($odbc_conn);
mysqli_close($mysql_conn);