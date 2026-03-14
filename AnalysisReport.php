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
    header("Location: " . $config["REDIRECT"] . "?url=ShiftRoster.php&message=Session Expired or Security Policy Violated");
}
//$conn = openConnection();
$conn = new mysqli("localhost", "root", "namaste", "access_lfz");

// Check connection
if ($conn->connect_errno) {
    echo "Failed to connect to MySQL: " . $conn->connect_error;
    exit();
}

function fetch_all($result) {
    $rows = array();
    while ($row = $result->fetch_assoc())
        $rows[] = $row;

    return $rows;
}

if ($_POST['search']) {
    $frmdate = date('Ymd', strtotime($_POST['frmdate']));
    $todate = date('Ymd', strtotime($_POST['todate']));
    $analysisQuery = "select empname, project1,project2,project3,project4,project5, SUM(prj1hrsamt) as prj1amt, SUM(prj2hrsamt) as prj2amt, "
            . "SUM(prj3hrsamt) as prj3amt, SUM(prj4hrsamt) as prj4amt, SUM(prj5hrsamt) as prj5amt from "
            . "projecthrsallocation where empdate >='$frmdate' AND empdate<='$todate' group by empname,project1, project2, project3, project4, project5;";
}
$analysisResult = mysqli_query($conn, $analysisQuery);
$analysisRaw = mysqli_fetch_all($analysisResult, MYSQLI_ASSOC);
$project1hrsamt = 0;
$project2hrsamt = 0;
$project3hrsamt = 0;
$project4hrsamt = 0;
$project5hrsamt = 0;
foreach ($analysisRaw as $projectAmount) {
    $analysisReport[] = $projectAmount;
//    echo "<pre>";print_R($projectAmount);
//    if($projectAmount[0] == '')
    $project1hrsamt += $projectAmount[1];
    $project2hrsamt += $projectAmount[2];
    $project3hrsamt += $projectAmount[3];
    $project4hrsamt += $projectAmount[4];
    $project5hrsamt += $projectAmount[5];
}

//echo "<pre>";print_R($allocatedRaw);
$projectAllQuery = "select Name from projectmaster";
$projectAllResult = mysqli_query($conn, $projectAllQuery);
$projectAllRaw = mysqli_fetch_all($projectAllResult, MYSQLI_ASSOC);
//echo "<pre>";print_R($projectAllRaw);
print "<center>";
displayHeader($prints, false, false);
displayLinks($current_module, $userlevel);
?>
<style>
    .form-controls{
        display: block;
        width: 100%;
        height: 25px;
        padding: 1px 2px;
        font-size: 14px;
        line-height: 1.42857143;
        /* color: #555; */
        background-color: #fff;
        background-image: none;
        border: 1px solid #a680a8;
        border-radius: 4px;
    }
</style>
<font face="Verdana" size="3" color="#FF0000"><b>Detail Analysis Project Report(Cost To NGN)</b></font>
<form method="post" action="">
    <table border="1" cellpadding="1" bordercolor="#C0C0C0" cellspacing="-1" width="800">
        <tr>
            <th align="right" width="25%"><font face="Verdana" size="2">From Date: </font></th>
            <td width="25%"><input type="date" size="12" name="frmdate"  maxlength="12" value="<?php echo date('Y-m-d'); ?>"  class="form-controls" id="frmdate"/></td>
            <th align="right" width="25%"><font face="Verdana" size="2">To Date: </font></th>
            <td width="25%"><input type="date" size="12" name="todate"  maxlength="12" value="<?php echo date('Y-m-d'); ?>"  class="form-controls" id="todate"/></td>
            <td colspan="2"><input type="submit" id="searchfilter" name="search" class="form-controls" value="Search"></td>
        </tr>
    </table>
</form>
<table border="1" cellpadding="1" bordercolor="#C0C0C0" cellspacing="-1" width="800" id="exportexcel">
    <tbody>
        <tr>
            <th>Name</th>
            <?php
            $count = 0;
            foreach ($projectAllRaw as $allProjects) {
                $count++;
                echo "<th>" . $allProjects['Name'] . "</th>";
            }
            ?>
        </tr>
        <?php
        $total1 = 0;
        $total2 = 0;
        $total3 = 0;
        $total4 = 0;
        $total5 = 0;
        $total = array();
        foreach ($analysisReport as $analysisData) {
            echo "<tr>";
            echo "<td>" . $analysisData['empname'] . "</td>";
            
            foreach ($projectAllRaw as $projectsAllData) {
                $projectName = $projectsAllData['Name'];
//                echo $analysisData[$projectName];
//                echo "<pre>";print_R($analysisData);
                if (isset($analysisData['project1']) == $projectsAllData['Name']) {
                    echo "<td>" . $analysisData['prj1amt'] . "</td>";
                    $total1 += $analysisData['prj1amt'];
                }
                if (isset($analysisData['project2']) == $projectsAllData['Name']) {
                    echo "<td>" . $analysisData['prj2amt'] . "</td>";
                    $total2 += $analysisData['prj2amt'];
                }
                if (isset($analysisData['project3']) == $projectsAllData['Name']) {
                    echo "<td>" . $analysisData['prj3amt'] . "</td>";
                    $total3 += $analysisData['prj3amt'];
                }
                if (isset($analysisData['project4']) == $projectsAllData['Name']) {
                    echo "<td>" . $analysisData['prj4amt'] . "</td>";
                    $total4 += $analysisData['prj4amt'];
                }
                if (isset($analysisData['project5']) == $projectsAllData['Name']) {
                    echo "<td>" . $analysisData['prj5amt'] . "</td>";
                    $total5 += $analysisData['prj5amt'];
                }

                break;
//                echo "</tr>";
            }
            echo "</tr>";
        }
        
//        foreach ($analysisReport as $analysisData) {
//            echo "<tr>";
//            echo "<td>" . $analysisData['empname'] . "</td>";
//            // Loop through each project in $projectAllRaw
//            foreach ($projectAllRaw as $projectsAllData) {
//                $projectName = $projectsAllData['Name'];
////                echo "<br>";
//                // Check if the project name exists in analysisData
//                if (isset($analysisData[$projectName])) {
//                    echo "<td>" . $analysisData[$projectName] . "</td>"; // Display the amount if available
//                    // Update the total for the corresponding project
//                    if (!isset($total[$projectName])) {
//                        $total[$projectName] = 0; // Initialize total for the project if not already set
//                    }
//                    $total[$projectName] += $analysisData[$projectName]; // Add the amount to the total
//                } else {
//                    echo "<td></td>"; // If the project name doesn't exist, display an empty cell
//                }
//            }
//            echo "</tr>";
//        }
//        
//        // Display the total amounts for each project
//        echo "<tr><th>Grand Total</th>";
//        foreach ($projectAllRaw as $projectsAllData) {
//            $projectName = $projectsAllData['Name'];
//            echo "<td>" . (isset($total[$projectName]) ? $total[$projectName] : 0) . "</td>"; // Display the total amount
//        }
//        echo "</tr>";
        echo "<tr><th>Grand Total</th><td>$total1</td><td>$total2</td><td>$total3</td><td>$total4</td><td>$total5</td></tr>";
        ?>
    </tbody>
</table>
<button id="exportToExcelBtn" class="btn btn-primary">Export to Excel</button>
<div id="loader">
    <img src="img/loader.gif" >
</div>
<?php
print "</center>";
?>
<script src="resource/js/jquery.min.js"></script>
<script>
    $('#exportToExcelBtn').show();
    $(document).ready(function () {
        $('#loader').hide();
    });
    $("#searchfilter").click(function () {
        var fromDate = $("#frmdate").val();
        var toDate = $("#todate").val();
        $('#loader').show();
        updateURL(fromDate, toDate);
    });

    function getUrlParameter(name) {
        name = name.replace(/[[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }

// Check if date parameters exist in the URL
    var fromDateURL = getUrlParameter('frmdate');
    var toDateURL = getUrlParameter('todate');

// If date parameters exist, set them as the default values for the form fields
    if (fromDateURL && toDateURL) {
        $('#frmdate').val(fromDateURL);
        $('#todate').val(toDateURL);
    }

    function updateURL(fromDate, toDate) {
        if (history.pushState) {
            var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?frmdate=' + fromDate + '&todate=' + toDate;
            window.history.pushState({path: newurl}, '', newurl);
        }
    }

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
