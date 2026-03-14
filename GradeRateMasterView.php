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
$designationQuery = "SELECT * FROM tuser where name='$username'";
$designationResult = mysqli_query($conn, $designationQuery);
$designationRow = mysqli_fetch_assoc($designationResult);

print "<center>";
displayHeader($prints, false, false);
displayLinks($current_module, $userlevel);
print "<center>";

if (isset($_GET['del_id'])) {
    $id = intval($_GET['del_id']); // Get the ID to delete and sanitize it

    // SQL to delete a record
    $deletewagequery = "DELETE FROM hourlywagescasual WHERE id = $id";

    if (mysqli_query($conn, $deletewagequery) === TRUE) {
        $message = 1;
    } else {
        $message = "Error deleting record: " . $conn->error;
    }
    header('Location:GradeRateMasterView.php?msg='.$message);
}

$gradeQuery = "select * from hourlywagescasual";
$gradeResult = mysqli_query($conn, $gradeQuery);

if(isset($_GET['status'])==1){
    $status1 = "Data Updated Successfully";
}
if(isset($_GET['status'])==2 && $_GET['status'] != ''){
    $status2 = "Data Not Updated";
}
if(isset($_GET['message'])==1){
    $message1 = "Data Inserted Successfully";
}
if(isset($_GET['message'])==2 && $_GET['message'] != ''){
    $message2 = "There is an error to add data";
}

if(isset($_GET['msg'])==1){
    $msg = "Record deleted successfully.";
}else{
    $msg = "Error deleting record";
}

if($_GET['status'] || $_GET['message'] || $_GET['msg']){
?>
<div class="alert alert-primary" role="alert" id="message">
    <?php echo $status1 . '<br>' . $status2; ?>
    <?php echo $message1 . '<br>' . $message2; ?>
    <?php echo $msg; ?>
</div>
<?php } ?>
<a href="GradeRateMaster.php" class="btn btn-primary" style="text-decoration: none;">Add Grade And Rate</a><br>
<table border="1" cellpadding="1" bordercolor="#C0C0C0" cellspacing="-1" width="800">
    <thead>
        <tr>
            <!--<th><font face="Verdana" size="2">ID</font></th>-->
            <!--<th><font face="Verdana" size="2">Employee ID</font></th>-->
            <th><font face="Verdana" size="1">Sr.No</font></th>
            <th><font face="Verdana" size="1">Activity</font></th>
            <th><font face="Verdana" size="1">Grades</font></th>
            <th><font face="Verdana" size="1">Regular Rate/hr for all working days</font></th>
            <th><font face="Verdana" size="1">O/T Rate/hr on regular days</font></th>
            <th><font face="Verdana" size="1">O/T Rate/hr on Sunday/ Public Holidays</font></th>
            <th><font face="Verdana" size="1">Regular day Rate</font></th>
            <th colspan="2"><font face="Verdana" size="1">Action</font></th>
        </tr>
    </thead>
    <tbody>
        <?php $k = 1; while ($gradeRow = mysqli_fetch_assoc($gradeResult)) { ?>
            <tr>
                <td><font face="Verdana" size="1"><?php echo $k++; ?></font></td>
                <td><font face="Verdana" size="1"><?php echo $gradeRow['activity']; ?></font></td>
                <td><font face="Verdana" size="1"><?php echo $gradeRow['grades']; ?></font></td>
                <td><font face="Verdana" size="1"><?php echo $gradeRow['regrate']; ?></font></td>
                <td><font face="Verdana" size="1"><?php echo $gradeRow['ot_regular']; ?></font></td>
                <td><font face="Verdana" size="1"><?php echo $gradeRow['ot_holiday']; ?></font></td>
                <td><font face="Verdana" size="1"><?php echo $gradeRow['regdayrate']; ?></font></td>
                <td><a href="GradeRateMasterUpdate.php?id=<?php echo $gradeRow['id']; ?>" class="btn btn-primary" style="text-decoration: none;">Edit</a></td>
                <?php if($designationRow['F8'] == 'Admin'){ ?>
                <td><a href="GradeRateMasterView.php?del_id=<?php echo $gradeRow['id']; ?>" class="btn btn-primary" style="text-decoration: none;">Delete</a></td>
                <?php } ?>
            </tr>
        <?php } ?>
    </tbody>
</table>
<?php
print "</center>";
?>
<script>
    setTimeout(function () {
        document.getElementById("message").style.display = "none";
    }, 5000);
</script>