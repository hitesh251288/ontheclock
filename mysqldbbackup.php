<?php
set_time_limit(0);

// MySQL database configurations for multiple databases
$database_configs = array(
    array(
        "hostname" => "localhost",
        "username" => "root",
        "password" => "namaste",
        "database" => "access"
    ),
    array(
        "hostname" => "localhost",
        "username" => "root",
        "password" => "namaste",
        "database" => "unis"
    ),
    array(
        "hostname" => "localhost",
        "username" => "root",
        "password" => "namaste",
        "database" => "archive"
    )
);

// Backup each database
foreach ($database_configs as $db_config) {
    // Connect to MySQL database
    $mysqli = new mysqli($db_config['hostname'], $db_config['username'], $db_config['password'], $db_config['database']);
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
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
    $backup_file = "Backup_" . $db_config['database'] . "_" . date('Y-m-d_H-i-s') . ".sql";
    file_put_contents($backup_file, $content);

    echo "Backup completed for database: {$db_config['database']}. The SQL file has been saved as: $backup_file";

    // Close MySQL connection
    $mysqli->close();
}

// Function to escape values
function escapeValue($value, $mysqli) {
    return $value === null ? 'NULL' : "'" . $mysqli->real_escape_string($value) . "'";
}
?>