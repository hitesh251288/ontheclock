<?php
ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "31";
set_time_limit(0);
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=CasualPaysheet.php&message=Session Expired or Security Policy Violated");
}
//$conn = openConnection();
$conn = new mysqli("localhost", "root", "namaste", "access_lfz");

// Check connection
if ($conn->connect_errno) {
    echo "Failed to connect to MySQL: " . $conn->connect_error;
    exit();
}

$prints = $_GET["prints"];
$excel = $_GET["excel"];
print "<html><title>Project Hours Allocation View</title>";
if ($prints != "yes") {
    print "<body>";
} else { //echo "HEY";die;
    if ($excel != "yes") {
        print "<body onLoad='javascript:window.print()'>";
    } else { //echo "HEY";die;
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=CasualPaysheet.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        print "<body>";
    }
}
$designationQuery = "SELECT * FROM tuser where name='$username'";
$designationResult = mysqli_query($conn, $designationQuery);
$designationRow = mysqli_fetch_assoc($designationResult);
$dept = $designationRow['dept'];

$migrationQuery = "select * from paysheetmigration";
$migrationResult = mysqli_query($conn, $migrationQuery);
$migrationRaw = mysqli_fetch_all($migrationResult);
$fromdate = $migrationRaw[0][1];
$todate = $migrationRaw[0][2];
//$casualQuery = "SELECT id,name FROM tuser t where id LIKE '10%' AND remark != 'SENIOR STAFF'";
if($designationRow['F8'] == 'Admin'){
//    $casualQuery = "SELECT o.*,h.grades,h.regrate,h.ot_regular,h.ot_holiday,h.regdayrate,p.*,a.* FROM onboardrequest o "
//        . "LEFT JOIN hourlywagescasual h ON h.grades = o.grade "
//        . "LEFT JOIN projecthrsallocation p ON p.empcode=o.employee_id "
//        . "LEFT JOIN projectapproval a ON a.parent_id=p.parentempcode AND a.child_id=p.empcode AND a.empdate=p.empdate "
//        . "where o.status_reg=1 AND p.empdate >='$fromdate' and p.empdate <= '$todate' group by o.employee_id order by o.employee_id asc";
    $casualQuery = "SELECT o.*,h.grades,h.regrate,h.ot_regular,h.ot_holiday,h.regdayrate,p.*,a.* FROM onboardrequest o "
        . "LEFT JOIN hourlywagescasual h ON h.grades = o.grade "
        . "LEFT JOIN (
            SELECT 
                p.empcode,
                p.parentempcode,
                p.empdate,
                SUM(CASE WHEN a.pm_project1 != '' THEN p.prj1hrsamt ELSE 0 END) AS prj1hrsamt_total,
                SUM(CASE WHEN a.pm_project2 != '' THEN p.prj2hrsamt ELSE 0 END) AS prj2hrsamt_total,
                SUM(CASE WHEN a.pm_project3 != '' THEN p.prj3hrsamt ELSE 0 END) AS prj3hrsamt_total,
                SUM(CASE WHEN a.pm_project4 != '' THEN p.prj4hrsamt ELSE 0 END) AS prj4hrsamt_total,
                SUM(CASE WHEN a.pm_project5 != '' THEN p.prj5hrsamt ELSE 0 END) AS prj5hrsamt_total
            FROM 
                projecthrsallocation p
            LEFT JOIN 
                projectapproval a ON a.child_id = p.empcode 
                AND a.parent_id = p.parentempcode 
                AND a.empdate = p.empdate
            WHERE 
                p.empdate >= '$fromdate' AND p.empdate <= '$todate'
            GROUP BY 
                p.empcode, p.parentempcode
        ) AS p ON p.empcode=o.employee_id "
        . "LEFT JOIN projectapproval a ON a.child_id=o.employee_id AND a.parent_id=p.parentempcode AND a.empdate=p.empdate "
        . "where o.status_reg=1 AND p.empdate >='$fromdate' and p.empdate <= '$todate' group by o.employee_id order by o.employee_id asc";
}else{
//    $casualQuery = "SELECT o.*,h.grades,h.regrate,h.ot_regular,h.ot_holiday,h.regdayrate,p.*,a.* FROM onboardrequest o "
//            . "LEFT JOIN hourlywagescasual h ON h.grades = o.grade "
//            . "LEFT JOIN projecthrsallocation p ON p.empcode=o.employee_id "
//            . "LEFT JOIN projectapproval a ON a.parent_id=p.parentempcode AND a.child_id=p.empcode AND a.empdate=p.empdate "
//            . "where o.status_reg=1 AND p.empdate >='$fromdate' and p.empdate <= '$todate' AND o.dept='$dept' group by o.employee_id order by o.employee_id asc";
    $casualQuery = "SELECT o.*,h.grades,h.regrate,h.ot_regular,h.ot_holiday,h.regdayrate,p.*,a.* FROM onboardrequest o "
        . "LEFT JOIN hourlywagescasual h ON h.grades = o.grade "
        . "LEFT JOIN (
            SELECT 
                p.empcode,
                p.parentempcode,
                p.empdate,
                SUM(CASE WHEN a.pm_project1 != '' THEN p.prj1hrsamt ELSE 0 END) AS prj1hrsamt_total,
                SUM(CASE WHEN a.pm_project2 != '' THEN p.prj2hrsamt ELSE 0 END) AS prj2hrsamt_total,
                SUM(CASE WHEN a.pm_project3 != '' THEN p.prj3hrsamt ELSE 0 END) AS prj3hrsamt_total,
                SUM(CASE WHEN a.pm_project4 != '' THEN p.prj4hrsamt ELSE 0 END) AS prj4hrsamt_total,
                SUM(CASE WHEN a.pm_project5 != '' THEN p.prj5hrsamt ELSE 0 END) AS prj5hrsamt_total
            FROM 
                projecthrsallocation p
            LEFT JOIN 
                projectapproval a ON a.child_id = p.empcode 
                AND a.parent_id = p.parentempcode 
                AND a.empdate = p.empdate
            WHERE 
                p.empdate >= '$fromdate' AND p.empdate <= '$todate'
            GROUP BY 
                p.empcode, p.parentempcode
        ) AS p ON p.empcode=o.employee_id "
        . "LEFT JOIN projectapproval a ON a.child_id=o.employee_id AND a.parent_id=p.parentempcode AND a.empdate=p.empdate "
        . "where o.status_reg=1 AND p.empdate >='$fromdate' and p.empdate <= '$todate' AND o.dept='$dept' group by o.employee_id order by o.employee_id asc";
}
$casualResult = mysqli_query($conn, $casualQuery);
$casualData = mysqli_fetch_all($casualResult);

// Convert rates to hours and minutes  
function convertMinutesToHoursAndMinutes($minutes) {  
    return [  
        'hours' => floor($minutes / 60),  
        'minutes' => $minutes % 60  
    ];  
} 
// Function to convert time to total minutes  
function timeToMinutes($time) {  
    list($hours, $minutes) = explode(':', $time); // Split time into hours and minutes  
    return ($hours * 60) + (int)$minutes; // Convert to total minutes  
}
// Function to convert total minutes back to hours and minutes  
function minutesToTime($totalMinutes) {  
    $hours = floor($totalMinutes / 60); // Get the whole hours  
    $minutes = $totalMinutes % 60; // Get the remaining minutes  
    return sprintf('%d:%02d', $hours, $minutes); // Return as "HH:MM"  
}  

function convertTimeToDecimal($time) {  
    list($hours, $minutes) = explode(':', $time);  
    return (float)$hours + ((float)$minutes / 60);  
} 
print "<center>";
displayHeader($prints, false, false);
displayLinks($current_module, $userlevel);

echo "<br><span style='color:red;'><b>Shown Records From " . date('Y-m-d', strtotime($fromdate)) . " to " . date('Y-m-d', strtotime($todate)) . "</b></span>";
echo "<br><span style='color:red;'>You can change the date from Paysheet Migration</span>";
?>
<table border="1" cellpadding="1" bordercolor="#C0C0C0" cellspacing="-1" width="100%" id="exportexcel">
    <thead>
        <tr>
            <th><font face="Verdana" size="1">EMP ID</font></th>
            <th><font face="Verdana" size="1">NAME</font></th>
            <!--<th><font face="Verdana" size="1">Project 1</font></th>-->
            <th><font face="Verdana" size="1">Project 1 Amount</font></th>
            <!--<th><font face="Verdana" size="1">Project 2</font></th>-->
            <th><font face="Verdana" size="1">Project 2 Amount</font></th>
            <!--<th><font face="Verdana" size="1">Project 3</font></th>-->
            <th><font face="Verdana" size="1">Project 3 Amount</font></th>
            <!--<th><font face="Verdana" size="1">Project 4</font></th>-->
            <th><font face="Verdana" size="1">Project 4 Amount</font></th>
            <!--<th><font face="Verdana" size="1">Project 5</font></th>-->
            <th><font face="Verdana" size="1">Project 5 Amount</font></th>
            <th><font face="Verdana" size="1">Date of onboarding (Online form filled date)</font></th>
            <th><font face="Verdana" size="1">Date of biometrics registration</font></th>
            <th><font face="Verdana" size="1">Bank Name</font></th>
            <th><font face="Verdana" size="1">Name as appearing in bank acc</font></th>
            <th><font face="Verdana" size="1">DEPARTMENT</font></th>
            <th><font face="Verdana" size="1">GRADE</font></th>
            <th><font face="Verdana" size="1">RATE PER HOUR</font></th>
            <th><font face="Verdana" size="1">public Holiday Hr/Rate</font></th>
            <th><font face="Verdana" size="1">NORMAL HOURS PUNCHED & APPROVED</font></th>
            <!--<th><font face="Verdana" size="1">public Holiday/Sunday/Weekoff Hrs Approved</font></th>-->
            <th><font face="Verdana" size="1">OVERTIME HOURS PUNCHED & APPROVED</font></th>
            <th><font face="Verdana" size="1">Total Hrs Approved</font></th>
            <th><font face="Verdana" size="1">TOTAL WAGES AS PER NORMAL HRS</font></th>
            <!--<th><font face="Verdana" size="1">Total Wages As Per Holiday Hours</font></th>-->
            <th><font face="Verdana" size="1">TOTAL WAGES AS PER OVERTIME HOURS</font></th>
            <th><font face="Verdana" size="1">TOTAL WAGES PAYABLE</font></th>
        </tr>
        <?php
        $totalColumn43 = 0;
        foreach ($casualData as $casualDetail) {
            ?>
            <tr>
                <td><font face="Verdana" size="1"><?php echo $casualDetail[1]; ?></font></td>
                <td><font face="Verdana" size="1"><?php echo $casualDetail[3]; ?></font></td>
                <!--<td><font face="Verdana" size="1"><?php echo $casualDetail[37]; ?></font></td>-->
                <td><font face="Verdana" size="1"><?php echo (isset($casualDetail[51]) && $casualDetail[51] != '') ? $casualDetail[31] : ''; ?></font></td>
                <!--<td><font face="Verdana" size="1"><?php echo $casualDetail[38]; ?></font></td>-->
                <td><font face="Verdana" size="1"><?php echo (isset($casualDetail[52]) && $casualDetail[52] != '') ? $casualDetail[32] : ''; ?></font></td>
                <!--<td><font face="Verdana" size="1"><?php echo $casualDetail[39]; ?></font></td>-->
                <td><font face="Verdana" size="1"><?php echo (isset($casualDetail[53]) && $casualDetail[53] != '') ? $casualDetail[33] : ''; ?></font></td>
                <!--<td><font face="Verdana" size="1"><?php echo $casualDetail[40]; ?></font></td>-->
                <td><font face="Verdana" size="1"><?php echo (isset($casualDetail[54]) && $casualDetail[54] != '') ? $casualDetail[34] : ''; ?></font></td>
                <!--<td><font face="Verdana" size="1"><?php echo $casualDetail[41]; ?></font></td>-->
                <td><font face="Verdana" size="1"><?php echo (isset($casualDetail[55]) && $casualDetail[55] != '') ? $casualDetail[35] : ''; ?></font></td>
                <td><font face="Verdana" size="1"><?php echo $casualDetail[20]; ?></font></td>
                <td><font face="Verdana" size="1"><?php echo $casualDetail[21]; ?></font></td>
                <td><font face="Verdana" size="1"><?php echo $casualDetail[13]; ?></font></td>
                <td><font face="Verdana" size="1"><?php echo $casualDetail[3]; ?></font></td>
                <td><font face="Verdana" size="1"><?php echo $casualDetail[11]; ?></font></td>
                <td><font face="Verdana" size="1"><?php echo $casualDetail[16]; ?></font></td>
                <td><font face="Verdana" size="1"><?php echo $casualDetail[24]; ?></font></td>
                <td><font face="Verdana" size="1"><?php echo $casualDetail[25]; ?></font></td>
                <td><font face="Verdana" size="1"><?php
                    $nhquery = "select p.*,a.*,o.employee_id from projecthrsallocation p "
                            . "LEFT JOIN projectapproval a ON a.parent_id=p.parentempcode AND a.child_id=p.empcode AND a.empdate=p.empdate "
                            . "LEFT JOIN onboardrequest o ON o.employee_id=p.empcode "
                            . "where o.employee_id=$casualDetail[1] AND p.empdate >=$fromdate AND p.empdate <=$todate";
                    $nhResult = mysqli_query($conn, $nhquery);
                    $nhRaw = mysqli_fetch_all($nhResult);
                    $p1rate = 0;
                    $p2rate = 0;
                    $p3rate = 0;
                    $p4rate = 0;
                    $p5rate = 0;
                    $p1holidayrate = 0;
                    $p2holidayrate = 0;
                    $p3holidayrate = 0;
                    $p4holidayrate = 0;
                    $p5holidayrate = 0;
                    $ot1hrs = 0;
                    $ot2hrs = 0;
                    $ot3hrs = 0;
                    $ot4hrs = 0;
                    $ot5hrs = 0;
                    $totalotHours = 0;
                    foreach ($nhRaw as $nhRaws) {  
                        $isSunday = date("w", strtotime($nhRaws[3])) == 0;  // Check if the current date is Sunday  

                        // Processing Project 1  
                        if ($nhRaws[37] != '' || $nhRaws[47] != '') {  
                            if ($isSunday) {  
                                $p1holidayrate += $nhRaws[14]; // Add to holiday rate if Sunday  
                            } else {  
                                $project1hours = $nhRaws[14]; // Assuming it's in 'H:MM' format  
                                list($hours1, $minutes1) = explode(':', $project1hours);  
                                // Calculate total minutes for Project 1  
                                $prj1totalMinutes = ($hours1 * 60 + $minutes1) - $p1holidayrate; // Ensure holiday rate is subtracted correctly  
                                if ($prj1totalMinutes > 0) {  
                                    $p1rate += $prj1totalMinutes;  
                                }  
                            }  
                        }  

                        // Processing Project 2  
                        if ($nhRaws[39] != '' || $nhRaws[48] != '') {  
                            if ($isSunday) {  
                                $p2holidayrate += $nhRaws[16];  
                            } else {  
                                $project2hours = $nhRaws[16];  
                                list($hours2, $minutes2) = explode(':', $project2hours);  
                                $prj2totalMinutes = ($hours2 * 60 + $minutes2) - $p2holidayrate;  
                                if ($prj2totalMinutes > 0) {  
                                    $p2rate += $prj2totalMinutes;  
                                }  
                            }  
                        }  

                        // Processing Project 3  
                        if ($nhRaws[41] != '' || $nhRaws[49] != '') {  
                            if ($isSunday) {  
                                $p3holidayrate += $nhRaws[18];  
                            } else {  
                                $project3hours = $nhRaws[18];  
                                list($hours3, $minutes3) = explode(':', $project3hours);  
                                $prj3totalMinutes = ($hours3 * 60 + $minutes3) - $p3holidayrate;  
                                if ($prj3totalMinutes > 0) {  
                                    $p3rate += $prj3totalMinutes;  
                                }  
                            }  
                        }  

                        // Processing Project 4  
                        if ($nhRaws[43] != '' || $nhRaws[50] != '') {  
                            if ($isSunday) {  
                                $p4holidayrate += $nhRaws[20];  
                            } else {  
                                $project4hours = $nhRaws[20];  
                                list($hours4, $minutes4) = explode(':', $project4hours);  
                                $prj4totalMinutes = ($hours4 * 60 + $minutes4) - $p4holidayrate;  
                                if ($prj4totalMinutes > 0) {  
                                    $p4rate += $prj4totalMinutes;  
                                }  
                            }  
                        }  

                        // Processing Project 5  
                        if ($nhRaws[45] != '' || $nhRaws[51] != '') {  
                            if ($isSunday) {  
                                $p5holidayrate += $nhRaws[23];  
                            } else {  
                                $project5hours = $nhRaws[22];  
                                list($hours5, $minutes5) = explode(':', $project5hours);  
                                $prj5totalMinutes = ($hours5 * 60 + $minutes5) - $p5holidayrate;  
                                if ($prj5totalMinutes > 0) {  
                                    $p5rate += $prj5totalMinutes;  
                                }  
                            }  
                        }  

                        // Overtime Calculation  
                        // Assuming $nhRaws[8] is the total hours worked  
                        if ($nhRaws[47] != '' || $nhRaws[48] != '' || $nhRaws[49] != '' || $nhRaws[50] != '' || $nhRaws[51] != '') {  
//                            $ot1hrs = $nhRaws[8] - 8; // Assuming 8 hours is a regular working day  
                            list($workHours, $workMinutes) = explode(':', $nhRaws[8]);  
                            $totalWorkMinutes = ($workHours * 60) + $workMinutes;  
                            $ot1hrs = $totalWorkMinutes - 480;
                            if ($ot1hrs > 0) {  
                                $totalotHours += $ot1hrs;  
                            }  
                        }  
                    }  
                        
                    $p1time = convertMinutesToHoursAndMinutes($p1rate);  
                    $p2time = convertMinutesToHoursAndMinutes($p2rate);  
                    $p3time = convertMinutesToHoursAndMinutes($p3rate);  
                    $p4time = convertMinutesToHoursAndMinutes($p4rate);  
                    $p5time = convertMinutesToHoursAndMinutes($p5rate);  
                    $p5time = convertMinutesToHoursAndMinutes($p5rate);  

                    $totalCombinedMinutes = $p1rate + $p2rate + $p3rate + $p4rate + $p5rate;  
                    $finalTime = convertMinutesToHoursAndMinutes($totalCombinedMinutes);  

                    echo $totalapprovedHours = $finalTime['hours'] . ":" . str_pad($finalTime['minutes'], 2, '0', STR_PAD_LEFT);  
                    
                    $totalovertimehours = convertMinutesToHoursAndMinutes($totalotHours);
                    $totalotHours = $totalovertimehours['hours'] . ":" . str_pad($totalovertimehours['minutes'], 2, '0', STR_PAD_LEFT);
                    ?></font></td>
<!--                <td><font face="Verdana" size="1"><?php
                    //echo $totalholidayrate = $p1holidayrate + $p2holidayrate + $p3holidayrate + $p4holidayrate + $p5holidayrate;
                    ?></font></td>-->
                <td><font face="Verdana" size="1"><?php echo $totalotHours; ?></font></td>
                <td><font face="Verdana" size="1"><?php 
                        $totalMinutes1 = timeToMinutes($totalapprovedHours);  
                        $totalMinutes2 = timeToMinutes($totalotHours); 
                        $totalMinutes = $totalMinutes1 + $totalMinutes2;
                        echo $resultTime = minutesToTime($totalMinutes); 
                //echo $totalapprovedHours + $totalotHours; ?></font></td>
                <td><font face="Verdana" size="1"><?php 
                $decimalHours = convertTimeToDecimal($totalapprovedHours);
                $totalwagesNormalhours = $casualDetail[24] * $decimalHours;
                $whole_part1 = floor($totalwagesNormalhours);
                $decimal_part1 = $totalwagesNormalhours - $whole_part1;
                    if($decimal_part1 <= 0.5){
                        $totalwagesNormalhrs = $whole_part1;
                    }else{
                        $totalwagesNormalhrs = $whole_part1 + 1;
                    }
                    echo $totalwagesNormalhrs;
                ?></font></td>
                <!--<td><font face="Verdana" size="1"><?php echo $casualDetail[26] * $totalholidayrate; ?></font></td>-->
                <td><font face="Verdana" size="1"><?php 
                list($hourss, $minutess) = explode(':', $totalotHours);  
                $decimalHourss = $hourss + ($minutess / 60);   
                $totalwagesOvertimehours = round($casualDetail[24] * $decimalHourss * 1.5);
                $whole_partot = floor($totalwagesOvertimehours);
                $decimal_partot = $totalwagesOvertimehours - $whole_partot;
                if($decimal_partot <= 0.5){
                    $totalwagesOvertimehrs = $whole_partot;
                }else{
                    $totalwagesOvertimehrs = $whole_partot + 1;
                }
                echo $totalwagesOvertimehrs;
//                echo $totalwagesOvertimehrs = round($casualDetail[24] * $totalotHours * 1.5); 
                ?></font></td>
                <td><font face="Verdana" size="1"><?php echo $totalwagesNormalhrs + $totalwagesOvertimehrs; ?></font></td>
            </tr>
        <?php } ?>
    </thead>
</table>
<button id="exportToExcelBtn" class="btn btn-primary">Export to Excel</button>
<?php
print "</center>";
?>
<script src="resource/js/jquery.min.js"></script>
<script>
// Function to convert HTML table to CSV format
    function tableToCSV(table) {
        var csv = [];
        var rows = table.querySelectorAll('tr');

        // Iterate over table rows
        for (var i = 0; i < rows.length; i++) {  // Removed "- 1" to include all rows
            var row = [], cols = rows[i].querySelectorAll('td, th');

            // Iterate over table cells
            for (var j = 0; j < cols.length; j++) {  // Removed "- 1" to include all columns
                // Extract and escape text content from each cell
                var text = cols[j].innerText.replace(/"/g, '""');  // Escape double quotes
                row.push('"' + text + '"');
            }

            // Join the row elements with commas and push to CSV array
            csv.push(row.join(','));
        }

        // Join CSV array with newline characters
        return csv.join('\n');
    }

// Function to download CSV file
    function downloadCSV(csv, filename) {
        var csvFile;
        var downloadLink;

        // Create CSV file
        csvFile = new Blob([csv], {type: 'text/csv'});

        // Create download link
        downloadLink = document.createElement('a');

        // Set download link attributes
        downloadLink.download = filename;
        downloadLink.href = window.URL.createObjectURL(csvFile);

        // Append download link to body
        document.body.appendChild(downloadLink);

        // Click download link
        downloadLink.click();

        // Remove download link from body
        document.body.removeChild(downloadLink);
    }

// Ensure your HTML has an element with ID 'exportToExcelBtn'
    document.getElementById('exportToExcelBtn').addEventListener('click', function () {
        // Get the table element by its ID 'exportexcel'
        var table = document.getElementById('exportexcel');

        // Convert table to CSV format
        var csv = tableToCSV(table);

        // Download CSV file as 'exceldata.csv'
        downloadCSV(csv, 'exceldata.csv');
    });
</script>

