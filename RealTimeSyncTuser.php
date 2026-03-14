<?php
ob_start();
error_reporting(0);
ini_set('display_errors', '1');

/* =======================
   CONNECTIONS
======================= */

// SQL Server (ODBC)
$server = "DESKTOP-M5OA37B\SQLEXPRESS";
$database = "iASWeb";
$user = "sa";
$password = "sss";
$connection_string = "Driver={ODBC Driver 17 for SQL Server};Server=$server;Database=$database;";
$odbc_conn = odbc_connect($connection_string, $user, $password);
if (!$odbc_conn) die("ODBC Connection failed: " . odbc_errormsg());

// MySQL
$mysqli = new mysqli("localhost", "root", "namaste", "access");
if ($mysqli->connect_error) die("MySQL Connection Failed: " . $mysqli->connect_error);


/* =======================
   STEP 1: GET ACTIVE EMPLOYEES FROM SQL SERVER
======================= */
$sqlIds = [];
$sqlServerQuery = "SELECT PAYCODE FROM [dbo].[tblemployee] WHERE ACTIVE='Y'";
$sqlServerResult = odbc_exec($odbc_conn, $sqlServerQuery);

while ($row = odbc_fetch_array($sqlServerResult)) {
    $sqlIds[] = $row['PAYCODE'];
}


/* =======================
   STEP 2: GET EXISTING EMPLOYEES FROM MYSQL
======================= */

$mysqlIds = [];
$mysqlQuery = "SELECT id FROM tuser";
$mysqlResult = mysqli_query($mysqli, $mysqlQuery);

while ($row = mysqli_fetch_assoc($mysqlResult)) {
    $mysqlIds[] = $row['id'];
}

$sqlIdsMap = array_flip($sqlIds); 

foreach ($sqlIds as $sid) {
    if (!in_array($sid, $mysqlIds)) {

        // Soft delete (recommended)
        $sql = "
            UPDATE [dbo].[tblemployee]
            SET ACTIVE='N'
            WHERE PAYCODE='$sid' AND ACTIVE='Y'
        ";

        odbc_exec($odbc_conn, $sql);
        echo "Marked inactive in SQL Server: $sid<br>";
    }
}

$sqlIds = [];   // RESET OLD IDS

$sqlServerQuery = "SELECT PAYCODE FROM [dbo].[tblemployee] WHERE ACTIVE='Y'";
$sqlServerResult = odbc_exec($odbc_conn, $sqlServerQuery);

while ($row = odbc_fetch_array($sqlServerResult)) {
    $sqlIds[] = $row['PAYCODE'];
}
/* =======================
   STEP 3: INSERT / UPDATE EMPLOYEES (USING YOUR QUERY)
======================= */

foreach ($sqlIds as $employee_id) {

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
        echo "No data for EmployeeID: $employee_id<br>";
        continue;
    }

    // Fetch values
    $id   = odbc_result($emp_result, 'id');
    $name = addslashes(odbc_result($emp_result, 'name'));

    $reg_date_raw = odbc_result($emp_result, 'reg_date');
    $reg_date = $reg_date_raw ? date('YmdHi', strtotime($reg_date_raw)) : '';

    $from_raw = odbc_result($emp_result, 'AccesstimeFrom');
    $to_raw   = odbc_result($emp_result, 'AccessTimeTo');

    $datelimit = 'N' . 
        ($from_raw ? date('Ymd', strtotime($from_raw)) : '00000000') .
        ($to_raw   ? date('Ymd', strtotime($to_raw))   : '00000000');

    $idno    = addslashes(odbc_result($emp_result, 'idno'));
    $dept    = addslashes(odbc_result($emp_result, 'dept'));
    $company = addslashes(odbc_result($emp_result, 'company'));

    // Insert / Update into MySQL
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

    if (mysqli_query($mysqli, $insert_sql)) {
        echo "Synced Employee: $id<br>";
    } else {
        echo "MySQL Sync Failed for ID: $id - " . mysqli_error($mysqli) . "<br>";
    }
}


/* =======================
   STEP 4: DELETE FROM MYSQL IF EMPLOYEE NOT IN SQL SERVER
======================= */

foreach ($mysqlIds as $id) {
    if (!in_array($id, $sqlIds)) {

        mysqli_query($mysqli, "DELETE FROM tuser WHERE id='$id'");
        echo "Deleted from MySQL: $id<br>";

        // OPTIONAL: Mark inactive in SQL Server
        odbc_exec($odbc_conn, "UPDATE [dbo].[tblemployee] SET ACTIVE='N' WHERE PAYCODE='$id'");
        echo "Marked inactive in SQL Server: $id<br>";
    }
}


/* =======================
   CLEANUP
======================= */

odbc_close($odbc_conn);
mysqli_close($mysqli);
ob_end_clean();

header("Location: ReportEmployee.php?msg=Real-time+Sync+Complete");
exit;
?>
