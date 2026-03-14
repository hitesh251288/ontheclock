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
$dept = $designationRow['dept'];
if($designationRow['F8'] == 'Admin'){
    $userQuery = "Select id,name from tuser where F8 = 'SV'";
}else{
    $userQuery = "Select id,name from tuser where F8 = 'SV' AND dept='$dept'";
}
//$userQuery = "SELECT employee_id,fullname FROM onboardrequest where grade LIKE 'S%' AND status_reg= '1'";
$userResult = mysqli_query($conn, $userQuery);
$userData = mysqli_fetch_all($userResult, MYSQLI_ASSOC);

//$casualQuery = "SELECT id,name FROM tuser t where id LIKE '10%' AND remark != 'SENIOR STAFF'";
//$casualQuery = "SELECT employee_id,fullname FROM onboardrequest where grade NOT LIKE 'S%' AND status_reg= '1'";
if($designationRow['F8'] == 'Admin'){
    $casualQuery = "SELECT t.id,t.name,r.parent_id,r.id as reportingid,s.name as supervisor FROM tuser t LEFT JOIN reportinghirekey r ON r.child_id=t.id LEFT JOIN tuser s ON r.parent_id=s.id where t.F8 = 'CASUAL'";
}else{
    $casualQuery = "SELECT t.id,t.name,r.parent_id,r.id as reportingid,s.name as supervisor FROM tuser t LEFT JOIN reportinghirekey r ON r.child_id=t.id LEFT JOIN tuser s ON r.parent_id=s.id where t.F8 = 'CASUAL' AND t.dept='$dept' OR s.dept='$dept'";
}
$casualResult = mysqli_query($conn, $casualQuery);
$casualData = mysqli_fetch_all($casualResult, MYSQLI_ASSOC);

if(isset($_GET['status'])==1){
    $status1 = "Data Inserted Successfully";
}

if(isset($_GET['status'])==2 && $_GET['status'] != ''){
    $status2 = "There Is Problem To Insert Data";
}

$hireQuery = "select parent_id, child_id from reportinghirekey";
$hireResult = mysqli_query($conn, $hireQuery);
$hireData = mysqli_fetch_all($hireResult, MYSQLI_ASSOC);
//echo "<pre>";print_R($hireData);

// Prepare hireData for easier processing in PHP and JS
$hireMap = [];
foreach ($hireData as $row) { 
    $hireMap[$row['parent_id']][] = $row['child_id'];
}
print "<center>";
displayHeader($prints, false, false);
displayLinks($current_module, $userlevel);
if($_GET['status']){
?>
<div class="alert alert-primary" role="alert" id="message">
    <?php echo $status1 . '<br>' . $status2; ?>
    <?php echo $message1 . '<br>' . $message2; ?>
</div>
<?php } ?>
<!--<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>-->
<form method="post" action="ReportingHireKey.php">
    <table width="800" border="1" cellpadding="1" bordercolor="#C0C0C0" cellspacing="-1">
        <tbody>
            <tr>
                <td>&nbsp;</td>
                <td><font face="Verdana" size="1"><b>Supervisor List From Below</b></font></td>
            </tr>
            <tr>
                <td align="right" width="20%"><font size="2" face="Verdana">Supervisor Link Form : </font></td>
                <td width="80%">
                    <select name="hireauth" class="form-control" required="required" onchange="checkRelatedCheckbox(this)">
                        <option value="">---</option>
                        <?php foreach ($userData as $userAll) { ?>
                            <option value="<?php echo $userAll['id']; ?>"><?php echo $userAll['id'] . ': ' . $userAll['name']; ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
        </tbody>
    </table>
    <table width="800" border="1" cellpadding="1" bordercolor="#C0C0C0" cellspacing="-1">
        <thead>
            <tr>
                <th align="left"></th>
                <th align="left">ID</th>
                <th align="left">Employee Name</th>
                <th align="left">Supervisor Name</th>
                <th align="left">Action</th>
            </tr>
        </thead>
        <tbody> 
            <?php 
            $i=0;
            foreach ($casualData as $casualAll) { 
                $checkData =  in_array($casualAll['id'], array_column($hireData, 'child_id')) ? 'checked disabled' : '';  
                if(isset($casualAll['parent_id'])){ 
                    $unassign = "<a href='ReportingHireKey.php?id=" . $casualAll['reportingid'] . "' class='btn btn-primary' style='text-decoration:none;'>Unassign</a>";
                }else{
                    $unassign = "";
                }
                ?>
                <tr>
                    <td><font face="Verdana" size="1"><input type="checkbox" name="childemp[]" value="<?php echo $casualAll['id']; ?>" <?php echo $checkData; ?>/></font></td>
                    <td><font face="Verdana" size="1"><?php echo $casualAll['id']; ?></font></td>
                    <td><font face="Verdana" size="1"><?php echo $casualAll['name']; ?></font></td>
                    <td><font face="Verdana" size="1"><?php echo $casualAll['supervisor']; ?></font></td>
                    <td><font face="Verdana" size="1"><?php echo $unassign; ?></font></td>
                </tr>
            <?php }  ?>
        </tbody>
    </table>
    <br>
    <input type="submit" name="submit" value="Assign Employee"/>
</form>
<script src="hitesh/js/jquery.min.js"></script>
<script>
    setTimeout(function () {
           document.getElementById("message").style.display = "none";
       }, 5000);
    var message = "<?php echo $_GET['message']; ?>";
    
    if(message==1){
        alert("User has been Unassign");
    }
    if(message==2){
        alert("There is problem to unassign user");
    }

    $(document).ready(function(){
        $('input[type="checkbox"][name="childemp[]"]').prop('checked', false); 
    });
   
    var hireMap = <?php echo json_encode($hireMap); ?>;

    function checkRelatedCheckbox(select) {
        var selectedValue = select.value;
        $('input[type="checkbox"][name="childemp[]"]').prop('checked', false); // Optionally reset all checkboxes
        if (selectedValue in hireMap) {
            $('input[type="checkbox"][name="childemp[]"]').each(function () {
                if (hireMap[selectedValue].includes($(this).val())) {
                    $(this).prop('checked', true);
                }
            });
        }

        // $('input[type="checkbox"][name="childemp[]"]').prop('disabled', false);
    }
</script>
<?php
print "</center>";

if (isset($_POST['submit'])) {
    $parentID = $_POST['hireauth'];
    $childID = $_POST['childemp'];
    $assign = $_SESSION['virdiusername'];

    for ($i = 0; $i < count($childID); $i++) {
        $reportQuery = "Insert into reportinghirekey(parent_id,child_id,assign_by)values($parentID,$childID[$i],'$assign')";
        $reportResult = mysqli_query($conn, $reportQuery);
        if ($reportResult) {
            echo "Data Inserted Successfully";
        } else {
            echo "There Is Problem To Insert Data";
            $repVar = 2;
        }
    }
    header('Location:ReportingHireKey.php?status=1&status=' . $repVar);
}

if(isset($_GET['id'])){
    $id = $_GET['id'];
    $query = "DELETE FROM reportinghirekey where id=".$id;
    $keyResult = mysqli_query($conn, $query);

    if($keyResult){
        echo "User has been unassign";
        $keyVar = 1;
    }else{
        echo "There is problem to unassign user";
        $keyVar = 2;
    }
    header('Location:ReportingHireKey.php?message=' . $keyVar);
}
