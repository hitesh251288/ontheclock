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
$act = $_GET["act"];
if ($act == "") {
    $act = $_POST["act"];
}
print "<center>";
displayHeader($prints, false, false);
displayLinks($current_module, $userlevel);

$designationQuery = "SELECT * FROM tuser where name='$username'";
$designationResult = mysqli_query($conn, $designationQuery);
$designationRow = mysqli_fetch_assoc($designationResult);
$dept = $designationRow['dept'];

if($designationRow['F8'] == 'Admin'){
    $fetchQuery = "SELECT * from onboardrequest";
}else{
    $fetchQuery = "SELECT * from onboardrequest where dept='$dept'";
}

$result = mysqli_query($conn, $fetchQuery);
if (isset($_GET['status']) == 1 || isset($_GET['status']) == 2 && isset($_GET['status']) != '') {
    $message1 = "Record Inserted In Unis Successfully";
}
if (isset($_GET['status']) == 3 && isset($_GET['status']) != '') {
    $message2 = "Record Updated In Onboard Successfully";
}
if ($_GET['status'] == 4 && isset($_GET['status']) != '') {
    $message3 = "Record Not Updated In Onboard";
}
if(isset($_GET['msg'])==0  && isset($_GET['msg']) != ''){
    $msg0 = "Data Not Inserted";
}
if(isset($_GET['msg'])==1){
    $msg1 = "Data Inserted Successfully";
}
if(isset($_GET['msg'])==2){
    $msg2 = "Data Updated Successfully";
}
if($_GET['msg']==3 && isset($_GET['msg']) != ''){
    $msg3 = "Data Not Updated";
}
if($_GET['status'] || $_GET['msg']){
?>
<div class="alert alert-primary" role="alert" id="message">
    <?php echo $message1 . '<br>' . $message2 . '<br>' . $message3; ?>
    <?php echo $msg0 . '<br>' . $msg1 . '<br>' . $msg2.'<br>'.$msg3; ?>
</div>
<?php } ?>
<a href="CasualOnboardInfo.php" class="btn btn-primary" style="text-decoration: none;">Add Casual Onboard Info</a><br>
<table border="1" cellpadding="1" bordercolor="#C0C0C0" cellspacing="-1" width="800">
    <thead>
        <tr>
            <!--<th><font face="Verdana" size="2">ID</font></th>-->
            <!--<th><font face="Verdana" size="2">Employee ID</font></th>-->
            <th><font face="Verdana" size="2">Form No.</font></th>
            <th><font face="Verdana" size="2">Full Name</font></th>
            <th><font face="Verdana" size="2">Date Of Birth</font></th>
            <th><font face="Verdana" size="2">Mobile Number</font></th>
            <th><font face="Verdana" size="2">Contact Address</font></th>
            <th><font face="Verdana" size="2">Name Next Of Kin</font></th>
            <th><font face="Verdana" size="2">Contact Number Of Next Of Kin</font></th>
            <th><font face="Verdana" size="2">Gender</font></th>
            <th><font face="Verdana" size="2">Commencement Date</font></th>
            <th><font face="Verdana" size="2">Department</font></th>
            <th><font face="Verdana" size="2">Designation</font></th>
            <th><font face="Verdana" size="2">Bank Name</font></th>
            <th><font face="Verdana" size="2">Account Number</font></th>
            <th><font face="Verdana" size="2">Name As Per Bank</font></th>
            <th><font face="Verdana" size="2">Hourly Rate</font></th>
            <th><font face="Verdana" size="2">Grade</font></th>
            <th><font face="Verdana" size="2">BVN</font></th>
            <th><font face="Verdana" size="2">NIN</font></th>
            <th colspan="2"><font face="Verdana" size="2">Action</font></th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <!--<td><?php echo $row['id']; ?></td>-->
                <!--<td><?php ?></td>-->
                <td><font face="Verdana" size="1"><?php echo $row['form_no']; ?></font></td>
                <td><font face="Verdana" size="1"><?php echo $row['fullname']; ?></font></td>
                <td><font face="Verdana" size="1"><?php echo $row['dob']; ?></font></td>
                <td><font face="Verdana" size="1"><?php echo $row['mob_no']; ?></font></td>
                <td><font face="Verdana" size="1"><?php echo $row['cnt_add']; ?></font></td>
                <td><font face="Verdana" size="1"><?php echo $row['name_kin']; ?></font></td>
                <td><font face="Verdana" size="1"><?php echo $row['cnt_kin']; ?></font></td>
                <td><font face="Verdana" size="1"><?php echo $row['gender']; ?></font></td>
                <td><font face="Verdana" size="1"><?php echo $row['commencement_date']; ?></font></td>
                <td><font face="Verdana" size="1"><?php echo $row['dept']; ?></font></td>
                <td><font face="Verdana" size="1"><?php echo $row['designation']; ?></font></td>
                <td><font face="Verdana" size="1"><?php echo $row['bankname']; ?></font></td>
                <td><font face="Verdana" size="1"><?php echo $row['acno']; ?></font></td>
                <td><font face="Verdana" size="1"><?php echo $row['name_as_per_bank']; ?></font></td>
                <td><font face="Verdana" size="1"><?php echo $row['hrrate']; ?></font></td>
                <td><font face="Verdana" size="1"><?php echo $row['grade']; ?></font></td>
                <td><font face="Verdana" size="1"><?php echo $row['bvn']; ?></font></td>
                <td><font face="Verdana" size="1"><?php echo $row['nin']; ?></font></td>
                <td><a href="CasualOnboardInfo.php?id=<?php echo $row['id']; ?>" class="btn btn-primary" style="text-decoration: none;">Edit</a></td>
                <td><?php if ($row['status_reg'] == '0') { ?><a href="GenerateRegister.php?reg=<?php echo $row['id']; ?>" class="btn btn-primary" style="text-decoration: none;">Registration</a><?php } ?></td>
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