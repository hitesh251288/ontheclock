<?php
// Connect to the source database
$sourceDB = mysqli_connect("source_host", "source_username", "source_password", "source_database");

// Check connection
if (!$sourceDB) {
    die("Source Database Connection failed: " . mysqli_connect_error());
}

// Connect to the destination database
$destinationDB = mysqli_connect("destination_host", "destination_username", "destination_password", "destination_database");

// Check connection
if (!$destinationDB) {
    die("Destination Database Connection failed: " . mysqli_connect_error());
}

// Query to fetch data from the source table
$fetchQuery = "SELECT * FROM source_table";
$result = mysqli_query($sourceDB, $fetchQuery);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Escape the unique column value
        $unique_value = mysqli_real_escape_string($destinationDB, $row['unique_column']);

        // Check if the record already exists in the destination table
        $checkQuery = "SELECT id FROM destination_table WHERE unique_column = '$unique_value'";
        $checkResult = mysqli_query($destinationDB, $checkQuery);

        if (mysqli_num_rows($checkResult) == 0) {
            // Escape other values to prevent SQL injection
            $column1 = mysqli_real_escape_string($destinationDB, $row['column1']);
            $column2 = mysqli_real_escape_string($destinationDB, $row['column2']);
            // Add other columns as needed

            // If the record does not exist, insert it into the destination table
            $insertQuery = "INSERT INTO destination_table (column1, column2, ...)
                            VALUES ('$column1', '$column2', ...)";
            mysqli_query($destinationDB, $insertQuery);
        }
    }
} else {
    echo "No data found in the source table.";
}

// Close the database connections
mysqli_close($sourceDB);
mysqli_close($destinationDB);
?>
