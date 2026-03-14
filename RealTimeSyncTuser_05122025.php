<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

// SQL Server Connection (ODBC)
$server = "DESKTOP-M5OA37B\SQLEXPRESS";
$database = "iASWeb";
$user = "sa";
$password = "sss";
$connection_string = "Driver={ODBC Driver 17 for SQL Server};Server=$server;Database=$database;";
$odbc_conn = odbc_connect($connection_string, $user, $password);
if (!$odbc_conn) die("ODBC Connection failed: " . odbc_errormsg());

// MySQL Connection
$mysqli = new mysqli("localhost", "root", "namaste", "access");
if ($mysqli->connect_error) die("MySQL Connection Failed: " . $mysqli->connect_error);

// Log file
$log_file = "sync_log.txt";

// Step 1: Get unsynced employee IDs from SQL Server
$queue_sql = "SELECT q.queue_id, q.employee_id, q.sync_type
              FROM [dbo].[syncqueue] q
              WHERE q.sync_status = 0";
$queue_result = odbc_exec($odbc_conn, $queue_sql);
if (!$queue_result) die("Failed to fetch queue: " . odbc_errormsg());

while ($queue_row = odbc_fetch_array($queue_result)) {
    
    $queue_id = $queue_row['queue_id'];
    $employee_id = $queue_row['employee_id'];

    // Step 2: Fetch employee data from tblemployee and related tables
    $emp_sql = "
        SELECT t1.PAYCODE AS id, 
               t1.EMPNAME AS name, 
               t1.DateOFJOIN AS reg_date, 
               t2.AccesstimeFrom,
               t2.AccessTimeTo,
               t1.sex AS idno, 
               t3.departmentname AS dept,
               t4.companyname AS company
        FROM [dbo].[tblemployee] AS t1
        LEFT JOIN [dbo].[Userdetail] AS t2 ON t1.PAYCODE = t2.UserID
        LEFT JOIN [dbo].[tblDepartment] AS t3 ON t1.DepartmentCode = t3.DepartmentCode
        LEFT JOIN [dbo].[tblcompany] AS t4 ON t1.COMPANYCODE = t4.COMPANYCODE
        WHERE t1.PAYCODE = $employee_id
    ";

    $emp_result = odbc_exec($odbc_conn, $emp_sql);
    if (!$emp_result || !odbc_fetch_row($emp_result)) {
        file_put_contents($log_file, "No data for EmployeeID: $employee_id\n", FILE_APPEND);
        continue;
    }

    $id = odbc_result($emp_result, 'id');
    $name = odbc_result($emp_result, 'name');
	$reg_date_raw = odbc_result($emp_result, 'reg_date');
	$reg_date = $reg_date_raw ? date('YmdHi', strtotime($reg_date_raw)) : '';
    //$reg_date = date('YmdHi', strtotime(odbc_result($emp_result, 'reg_date')));
	$from_raw = odbc_result($emp_result, 'AccesstimeFrom');
	$to_raw   = odbc_result($emp_result, 'AccessTimeTo');
	$datelimit = 'N' . ($from_raw ? date('Ymd', strtotime($from_raw)) : '00000000')
					 . ($to_raw   ? date('Ymd', strtotime($to_raw))   : '00000000');
    /*$datelimit = 'N' . date('Ymd', strtotime(odbc_result($emp_result, 'AccesstimeFrom')))
                    . date('Ymd', strtotime(odbc_result($emp_result, 'AccessTimeTo')));*/
    $idno = odbc_result($emp_result, 'idno');
    $dept = odbc_result($emp_result, 'dept');
    $company = odbc_result($emp_result, 'company');

        $sync_type = $queue_row['sync_type'];

        if ($sync_type == 2) {
            // Delete from MySQL
            $delete_sql = "DELETE FROM tuser WHERE id = $employee_id";

            if (mysqli_query($mysqli, $delete_sql)) {
                
//                odbc_exec($odbc_conn,
//                    "INSERT INTO syncqueue(employee_id, sync_status, sync_type)
//                     VALUES ($employee_id, 0, 2)"
//                );
                // Mark as synced in SQL Server
                odbc_exec($odbc_conn, 
                    "UPDATE [dbo].[syncqueue] SET sync_status = 1 
                     WHERE queue_id = $queue_id"
                );
                file_put_contents($log_file, 
                    "DELETED EmployeeID: $employee_id from MySQL\n", 
                    FILE_APPEND
                );
            } else {
                file_put_contents($log_file, 
                    "Delete FAILED for EmployeeID: $employee_id → ".mysqli_error($mysqli)."\n", 
                    FILE_APPEND
                );
            }
            continue; // go next queue row
        }
	$limit_sql = "SELECT COUNT(*) AS total FROM tuser";
	$limit_result = mysqli_query($mysqli, $limit_sql);
	$limit_row = mysqli_fetch_assoc($limit_result);
	$total_users = (int)$limit_row['total'];

	if ($total_users >= 900) {
		echo "❌ Cannot insert EmployeeID $id — tuser table limit (900) reached.\n";

		file_put_contents($log_file,
			"LIMIT BLOCK: tuser full (900). EmployeeID $id not inserted.\n",
			FILE_APPEND
		);

		// DO NOT update syncqueue status → will retry later
		continue;
	}
    // Step 3: Insert/Update in MySQL
    $insert_sql = "
        INSERT INTO tuser (id, name, reg_date, datelimit, idno, dept, company)
        VALUES ($id, '$name', '$reg_date', '$datelimit', '$idno', '$dept', '$company')
        ON DUPLICATE KEY UPDATE 
            name = VALUES(name), 
            reg_date = VALUES(reg_date), 
            datelimit = VALUES(datelimit), 
            idno = VALUES(idno), 
            dept = VALUES(dept), 
            company = VALUES(company)
    ";
//echo "Insert SQL: $insert_sql\n";
    if (mysqli_query($mysqli, $insert_sql)) {
//        echo "Synced EmployeeID: $id (Queue ID: $queue_id)\n";
		//echo "MySQL Sync OK for EmployeeID [$id], QueueID [$queue_id]\n";
        // Step 4: Update sync_status in SQL Server
        $update_sql = "UPDATE [dbo].[syncqueue] SET sync_status = 1 WHERE queue_id = $queue_id";
        if (!odbc_exec($odbc_conn, $update_sql)) {
			echo "ODBC Update Failed for QueueID [$queue_id]: " . odbc_errormsg($odbc_conn) . "\n";
		} else {
			//echo "SQL Server syncqueue updated for QueueID [$queue_id]\n";
		}
    } else {
        echo "Failed to sync ID: $id\n";
        file_put_contents($log_file, "MySQL insert failed for EmployeeID: $id - " . mysqli_error($mysqli) . "\n", FILE_APPEND);
    }
}

// Cleanup
odbc_close($odbc_conn);
mysqli_close($mysqli);
//echo "Real-time Sync Complete!\n";
header("Location: ReportEmployee.php?msg=Real-time+Sync+Complete");
exit;
?>
