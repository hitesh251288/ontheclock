<?php
set_time_limit(0);
// MySQL database configuration
$hostname = "localhost";
$username = "root";
$password = "namaste";
$database = "access";

// Connect to MySQL database
$mysqli = new mysqli($hostname, $username, $password, $database);
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Function to escape values
function escapeValue($value, $mysqli) {
    return $value === null ? 'NULL' : "'" . $mysqli->real_escape_string($value) . "'";
}

// Get all table names in the database
$tables = array();
$result = $mysqli->query("SHOW TABLES");
while ($row = $result->fetch_row()) {
    $tables[] = $row[0];
}

// Loop through each table and fetch data row by row
$content = '';
foreach ($tables as $table) {
    $result = $mysqli->query("SELECT * FROM $table");

    $content .= "DROP TABLE IF EXISTS $table;\n";
    $row2 = $mysqli->query("SHOW CREATE TABLE $table")->fetch_row();
    $content .= $row2[1].";\n";

    while ($row = $result->fetch_assoc()) {
        $keys = array_keys($row);
        $values = array();
        foreach ($row as $value) {
            $values[] = escapeValue($value, $mysqli);
        }

        $content .= "INSERT INTO $table (".implode(", ", $keys).") VALUES (".implode(", ", $values).");\n";
    }

    $content .= "\n";
}

// Save the SQL content to a file
$backup_file = "Access_Virdi_backup_" . date('Y-m-d_H-i-s') . ".sql";
file_put_contents($backup_file, $content);

echo "Backup completed. The SQL file has been saved as: $backup_file";

// Close MySQL connection
$mysqli->close();
?>