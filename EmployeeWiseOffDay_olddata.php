<?php
// Database connection setup
$conn = mysqli_connect('localhost', 'root', 'namaste', 'access_geeta');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if necessary columns exist and add them if not
$requiredColumns = ['OTDate', 'Day', 'EmployeeID', 'OT1', 'OT2'];
$columnsQuery = "SHOW COLUMNS FROM OTDay";
$columnsResult = mysqli_query($conn, $columnsQuery);
$existingColumns = [];

while ($column = mysqli_fetch_assoc($columnsResult)) {
    $existingColumns[] = $column['Field'];
}

// Add missing columns
foreach ($requiredColumns as $column) {
    if (!in_array($column, $existingColumns)) {
        if ($column == 'OTDate') {
            $alterQuery = "ALTER TABLE OTDay ADD $column DATE";
        } elseif ($column == 'EmployeeID') {
            $alterQuery = "ALTER TABLE OTDay ADD $column INT";
        } else {
            $alterQuery = "ALTER TABLE OTDay ADD $column TINYINT(1) DEFAULT NULL";
        }
        if (!mysqli_query($conn, $alterQuery)) {
            die("Error adding column $column: " . mysqli_error($conn));
        }
    }
}

// Fetch all employees from the database
$employeesQuery = "SELECT id, name FROM tuser";
$employeesResult = mysqli_query($conn, $employeesQuery);
$employees = [];
while ($row = mysqli_fetch_assoc($employeesResult)) {
    $employees[] = $row;
}

// Process form submission to update off-days
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $weekStartDate = $_POST['week_start']; // Start date of the week
    $weekEndDate = date('Y-m-d', strtotime($weekStartDate . ' + 6 days')); // End of the week

    foreach ($_POST['employees'] as $employeeID => $offDays) {
        // Loop through each day in the week
        $date = new DateTime($weekStartDate);
        while ($date->format('Y-m-d') <= $weekEndDate) {
            $currentDay = strtoupper($date->format('D')); // Get current day (e.g., MON, TUE)

            // Determine if it's an off-day (OT1 or OT2)
            $OT1 = (in_array($currentDay, $offDays)) ? 1 : NULL;
            $OT2 = (count($offDays) > 1 && isset($offDays[1]) && $offDays[1] == $currentDay) ? 1 : NULL;

            // Prepare OT1 and OT2 values (ensure they're not empty strings)
            $OT1 = !empty($OT1) ? $OT1 : 'NULL';
            $OT2 = !empty($OT2) ? $OT2 : 'NULL';

            // Insert or update the off-day
            $query = "INSERT INTO OTDay (OTDate, Day, EmployeeID, OT1, OT2) 
                      VALUES ('" . $date->format('Y-m-d') . "', '$currentDay', '$employeeID', $OT1, $OT2)
                      ON DUPLICATE KEY UPDATE OT1 = $OT1, OT2 = $OT2";

            mysqli_query($conn, $query);

            // Move to the next day
            $date->modify('+1 day');
        }
    }
    echo "Off days updated!";
}

// Get current week dates for form display
$startDate = isset($_GET['week_start']) ? $_GET['week_start'] : date('Y-m-d', strtotime('monday this week'));
$endDate = date('Y-m-d', strtotime($startDate . ' + 6 days'));

// Fetch existing off-day data for the current week
$offDaysQuery = "SELECT EmployeeID, Day, OT1, OT2 FROM OTDay WHERE OTDate BETWEEN '$startDate' AND '$endDate'";
$offDaysResult = mysqli_query($conn, $offDaysQuery);
$existingOffDays = [];

while ($row = mysqli_fetch_assoc($offDaysResult)) {
    $existingOffDays[$row['EmployeeID']][$row['Day']] = [
        'OT1' => $row['OT1'],
        'OT2' => $row['OT2']
    ];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Employee Off Day Schedule</title>
</head>
<body>
    <h1>Manage Employee Off Days (Week: <?php echo $startDate . ' - ' . $endDate; ?>)</h1>
    <form method="POST" action="">
        <label for="week_start">Select Week Start Date:</label>
        <input type="date" id="week_start" name="week_start" value="<?php echo $startDate; ?>" required>
        <br><br>

        <table border="1">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Monday</th>
                    <th>Tuesday</th>
                    <th>Wednesday</th>
                    <th>Thursday</th>
                    <th>Friday</th>
                    <th>Saturday</th>
                    <th>Sunday</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($employees as $employee): ?>
                    <tr>
                        <td><?php echo $employee['name']; ?></td>
                        <?php for ($i = 0; $i < 7; $i++): ?>
                            <?php
                            $day = date('D', strtotime($startDate . " +$i days"));
                            $upperDay = strtoupper($day);
                            $isOT1 = isset($existingOffDays[$employee['id']][$upperDay]['OT1']) ? $existingOffDays[$employee['id']][$upperDay]['OT1'] : NULL;
                            $isOT2 = isset($existingOffDays[$employee['id']][$upperDay]['OT2']) ? $existingOffDays[$employee['id']][$upperDay]['OT2'] : NULL;
                            ?>
                            <td>
                                <input type="checkbox" name="employees[<?php echo $employee['id']; ?>][]" value="<?php echo $upperDay; ?>" 
                                <?php echo ($isOT1 || $isOT2) ? 'checked' : ''; ?>> Off
                            </td>
                        <?php endfor; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <button type="submit">Update Off Days</button>
    </form>
</body>
</html>

<?php
mysqli_close($conn);
?>