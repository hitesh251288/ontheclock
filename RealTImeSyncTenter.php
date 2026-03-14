<?php
error_reporting(E_ERROR);
ini_set('display_errors', '1');

// SQL Server (ODBC)
$server = "WIN-7MBH2MJSGT8\SQLEXPRESS05";
$database = "iASWebSetup";
$user = "sa";
$password = "admin@123";
$connection_string = "Driver={ODBC Driver 17 for SQL Server};Server=$server;Database=$database;";
$odbc_conn = odbc_connect($connection_string, $user, $password);
if (!$odbc_conn) die("ODBC Connection Failed: " . odbc_errormsg());

// MySQL (MySQLi)
$mysql_conn = mysqli_connect("localhost", "root", "namaste", "access");
if (!$mysql_conn) die("MySQL Connection Failed: " . mysqli_error($mysql_conn));

// Fetch records from sync queue
$sql = "SELECT q.queue_id, q.UserID, q.AttDateTime, d.ID_NO
        FROM [dbo].[syncqueueAttendance] q
        LEFT JOIN [dbo].[tblmachine] d ON d.SerialNumber = q.DeviceID
        WHERE q.sync_status = 0
        ORDER BY q.queue_id ASC";

$odbc_result = odbc_exec($odbc_conn, $sql);

// Log file
$log_file = "sync_log.txt";
$success_count = 0;

/*if (!odbc_fetch_row($odbc_result)) {
    echo "No records found in syncqueueAttendance.\n";
    odbc_close($odbc_conn);
    mysqli_close($mysql_conn);
    exit;
}*/
if (!$odbc_result) {
    echo "Query Failed\n";
    exit;
}

if (odbc_num_rows($odbc_result) == 0) {
    echo "No records found in syncqueueAttendance.\n";
    exit;
}
// Rewind cursor
//odbc_execute($odbc_result);

while ($row = odbc_fetch_array($odbc_result)) {
    $queue_id = $row['queue_id'];
    $e_id = $row['UserID'];
    $e_date = date('Ymd', strtotime($row['AttDateTime']));
    $e_time = date('His', strtotime($row['AttDateTime']));
    $g_id = $row['ID_NO'];
    $e_etc = 16;
	if ($g_id === '' || $g_id === null) {
		$g_id_sql = 0; // insert numeric 0 instead of NULL
	} else {
		$g_id_sql = (int)$g_id;
	}
    // Check for duplicate
    $check_query = "SELECT COUNT(*) AS count FROM tenter WHERE e_id = '$e_id' AND e_date = '$e_date' AND e_time = '$e_time'";
    $check_result = mysqli_query($mysql_conn, $check_query);
    $check_row = mysqli_fetch_assoc($check_result);

    if ($check_row['count'] == 0) {
    $insert_query = "INSERT INTO tenter (e_id, e_date, e_time, g_id, e_etc)
                     VALUES ('$e_id', '$e_date', '$e_time', $g_id_sql, '$e_etc')";
    if (mysqli_query($mysql_conn, $insert_query)) {

        // 🔹 Default group = 0
        $e_group = 0;

		$setting_query = "SELECT UseShiftRoster FROM othersettingmaster LIMIT 1";
		$setting_result = mysqli_query($mysql_conn, $setting_query);

		$use_shiftroster = 'No'; // default value
		if ($setting_result && mysqli_num_rows($setting_result) > 0) {
			$setting_row = mysqli_fetch_assoc($setting_result);
			$use_shiftroster = $setting_row['UseShiftRoster'];
		}

		// 🔹 If UseShiftRoster = 'Yes' → use shiftroster, else use tuser
		if ($use_shiftroster == 'Yes') {
			
			// 🔹 Try to fetch group from shiftroster
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
		
		} else {
			// ✅ Use e_group from tuser (default when UseShiftRoster = 'No')
			$user_query = "SELECT group_id 
						   FROM tuser 
						   WHERE id = '$e_id' 
						   LIMIT 1";
			$user_result = mysqli_query($mysql_conn, $user_query);

			if ($user_result && mysqli_num_rows($user_result) > 0) {
				$user_row = mysqli_fetch_assoc($user_result);
				$e_group = $user_row['group_id'];
			}
		}
		// 🔹 Make sure e_group always has a valid value
		/*if ($e_group == '' || !is_numeric($e_group)) {
			$e_group = 0;
		}*/
        // 🔹 Always update tenter (with either found group or 0)
        $update_tenter = "UPDATE tenter 
                          SET e_group = '$e_group' 
                          WHERE e_id = '$e_id' 
                            AND e_date = '$e_date' 
                            AND e_time = '$e_time' 
                            AND g_id = $g_id_sql";
							//echo "<br>";
        mysqli_query($mysql_conn, $update_tenter);

        // 🔹 Mark record synced in SQL Server
        $update_sql = "UPDATE [dbo].[syncqueueAttendance] 
                       SET sync_status = 1 
                       WHERE queue_id = $queue_id";
        odbc_exec($odbc_conn, $update_sql);

        $success_count++;
    } else {
        file_put_contents($log_file, "MySQL insert failed for queue_id $queue_id: " . mysqli_error($mysql_conn) . "\n", FILE_APPEND);
    }
}

}

echo "Successfully synced $success_count records.\n";

odbc_close($odbc_conn);
mysqli_close($mysql_conn);
?>
