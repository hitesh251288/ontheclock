<?php
// Path to Access DB
$accessPath = 'C:\\iAS\\TimeWatch.mdb';
$accessPassword = 'SSS';

// Create COM connection to Access DB
try {
    $conn = new COM("ADODB.Connection");
    $connStr = "Provider=Microsoft.ACE.OLEDB.12.0;Data Source=$accessPath;Jet OLEDB:Database Password=$accessPassword;";
    $conn->Open($connStr);
} catch (com_exception $e) {
    die("❌ Could not connect to Access DB: " . $e->getMessage());
}

// Connect to MySQL
$mysql_conn = mysqli_connect("localhost", "root", "namaste", "access");
if (!$mysql_conn) {
    die("MySQL connection failed: " . mysqli_connect_error());
}

// Query from Access
$rs = new COM("ADODB.Recordset");
$sql = "SELECT ID_NO, branch FROM tblmachine";
$rs->Open($sql, $conn, 1, 3); // 1=adOpenKeyset, 3=adLockOptimistic

if (!$rs->EOF) {
    while (!$rs->EOF) {
        $id_no = trim($rs->Fields("ID_NO")->Value);
        $branch = trim($rs->Fields("branch")->Value);

        // Insert into MySQL (avoid duplicates)
        $query = "
            INSERT INTO tgate (id, name, floor, antipass, antipass_level, antipass_mode, Meal)
            VALUES ('" . mysqli_real_escape_string($mysql_conn, $id_no) . "',
                    '" . mysqli_real_escape_string($mysql_conn, $branch) . "',
                    0, 0, 0, 0, 0)
            ON DUPLICATE KEY UPDATE name=VALUES(name)
        ";

        if (!mysqli_query($mysql_conn, $query)) {
            error_log("❌ Insert failed for ID $id_no: " . mysqli_error($mysql_conn));
        }

        $rs->MoveNext();
    }
    echo "✅ Data sync completed.";
} else {
    echo "⚠ No records found in tblmachine.";
}

// Cleanup
$rs->Close();
$conn->Close();
mysqli_close($mysql_conn);