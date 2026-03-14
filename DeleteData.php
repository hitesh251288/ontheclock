<?php
error_reporting(E_ALL);
date_default_timezone_set("Africa/Algiers");
include "Functions.php";
session_start();
$second_execution = "";
$conn = openConnection();
$iconn = openIConnection();

// Database connection details
$servername = "localhost";
$username = "root";
$password = "namaste";
$dbname = "unis";

// Create connection
$unisConn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$unisConn) {
    die("Connection failed: " . mysqli_connect_error());
}

$act = $_GET["act"];
if ($act == "") {
    $act = $_POST["act"];
}
$txtFrom = $_GET["txtFrom"];
if ($txtFrom == "") {
    $txtFrom = $_POST["txtFrom"];
}
$txtTo = $_GET["txtTo"];
if ($txtTo == "") {
    $txtTo = $_POST["txtTo"];
}

include 'header.php';
?>
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-12 d-flex no-block align-items-center">
            <h4 class="page-title">Delete Data</h4>
            <div class="ms-auto text-end">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Delete Data
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<?php
print'<div class="container-fluid">
                <div class="row">
                        <div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12">
                            <div class="card">
                                    <div class="card-body table-responsive">';
print "<form name='frm1' method='post' onSubmit='return confirmDelete()' action='DeleteData.php'><input type='hidden' name='act' value='deleteRecord'>";

    ?>
    <div class="card">
        <div class="card-body">
            <?php
            print "<p align='center'><font face='Verdana' size='1' color='#FF0000'><b>" . $message . "</b></font></p>";
            if($prints != "yes") {
                print "<h4><center><font face='Verdana' size='1'><b>Select From And To Date And Delete The Record</b></font></center></h4>";
            }
            ?>
            <div class="row">
                <div class="col-4"></div>
                <div class="col-2">
                    <?php
                    displayTextbox("txtFrom", "Date From <font size='1'>(DD/MM/YYYY)</font>: ", $txtFrom, $prints, 12, "25%", "25%");
                    ?>
                </div>
                <div class="col-2">
                    <?php
                    displayTextbox("txtTo", "Date To <font size='1'>(DD/MM/YYYY)</font>: ", $txtTo, $prints, 12, "25%", "25%");
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <?php
                    if ($prints != "yes") {
                        print "<center><input name='btSearch' type='submit' class='btn btn-primary' value='Delete Record'></center>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</form>
<?php
print "</div></div></div></div>";
if ($act == "deleteRecord") {
    print '<center>';
    print '<div class="row"><div class="col-md-12"><div class="card"><div class="card-body">';
    // Start date and end date (can be passed via POST or GET parameters)
    $startDate = insertDate($txtFrom); // Example start date
    $endDate = insertDate($txtTo);   // Example end date
// Validate date range
if (!empty($startDate) && !empty($endDate)) {
    $batchSize = 1000;
    do {
    // Delete from 'tenter' table based on 'e_date'
    $queryTenter = "DELETE FROM tenter WHERE e_date BETWEEN '$startDate' AND '$endDate' LIMIT $batchSize";
    if (updateIData($iconn, $queryTenter, true)) {
        echo "Records deleted from 'tenter' successfully!<br>";
    } else {
        echo "Error deleting records from 'tenter': " . mysqli_error($conn) . "<br>";
    }

    // Delete from 'attendancemaster' table based on 'ADate'
    $queryAttendanceMaster = "DELETE FROM attendancemaster WHERE ADate BETWEEN '$startDate' AND '$endDate' LIMIT $batchSize";
    if (updateIData($iconn, $queryAttendanceMaster, true)){
//    if (mysqli_query($conn, $queryAttendanceMaster)) {
        echo "Records deleted from 'attendancemaster' successfully!<br>";
    } else {
        echo "Error deleting records from 'attendancemaster': " . mysqli_error($conn) . "<br>";
    }

    // Delete from 'daymaster' table based on 'TDate'
    $queryDayMaster = "DELETE FROM daymaster WHERE TDate BETWEEN '$startDate' AND '$endDate' LIMIT $batchSize";
    if (updateIData($iconn, $queryDayMaster, true)) {
//    if (mysqli_query($conn, $queryDayMaster)) {
        echo "Records deleted from 'daymaster' successfully!<br>";
    } else {
        echo "Error deleting records from 'daymaster': " . mysqli_error($conn) . "<br>";
    }
    
    $queryUnisTenter = "DELETE FROM tenter WHERE C_Date BETWEEN '$startDate' AND '$endDate' LIMIT $batchSize";
    if (updateIData($unisConn, $queryUnisTenter, true)) {
        echo "Records deleted from 'tenter Unis' successfully!<br>";
    } else {
        echo "Error deleting records from 'tenter': " . mysqli_error($conn) . "<br>";
    }
} while ($deletedRows > 0);
} else {
    echo "Please provide a valid start and end date.";
}
// Close the connection
    mysqli_close($conn);
    print "</div></div></div></div>";
    print '</center>';
}
//print "</form>";
print "</div>";
include 'footer.php';
?>
<script type="text/javascript">
    function confirmDelete() {
        return confirm("Are you sure you want to delete the records?");
    }
</script>