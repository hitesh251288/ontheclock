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
//if (!checkSession($userlevel, $current_module)) {
//    header("Location: " . $config["REDIRECT"] . "?url=AssignMangersProject.php&message=Session Expired or Security Policy Violated");
//}
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

//$userQuery = "SELECT employee_id,fullname FROM onboardrequest where grade LIKE 'S%' AND status_reg= '1'";
if($designationRow['F8'] == 'Admin'){
    $userQuery = "SELECT id,name FROM tuser where F8='SV'";
    $userResult = mysqli_query($conn, $userQuery);
    $userData = mysqli_fetch_all($userResult);

    $managerQuery = "SELECT id,name FROM tuser where F8='PM'";
    $managerResult = mysqli_query($conn, $managerQuery);
    $managerData = mysqli_fetch_all($managerResult);
}else{
    $userQuery = "SELECT id,name FROM tuser where F8='SV' AND dept='$dept'";
    $userResult = mysqli_query($conn, $userQuery);
    $userData = mysqli_fetch_all($userResult);

    $managerQuery = "SELECT id,name FROM tuser where F8='PM' AND dept='$dept'";
    $managerResult = mysqli_query($conn, $managerQuery);
    $managerData = mysqli_fetch_all($managerResult);
}


$projectQuery = "SELECT * from projectmaster";
$projectResult = mysqli_query($conn, $projectQuery);
$projectData = mysqli_fetch_all($projectResult);

$assignPrjQuery = "select supervisor_id, manager_id from assignproject";
$assignPrjResult = mysqli_query($conn, $assignPrjQuery);
$assignPrjData = mysqli_fetch_all($assignPrjResult, MYSQLI_ASSOC);

// Prepare hireData for easier processing in PHP and JS
$hireMap = [];
$allSupervisorIds = [];
foreach ($assignPrjData as $row) { 
    $supervisorIds = explode(',', $row['supervisor_id']);
    $allSupervisorIds = array_merge($allSupervisorIds, $supervisorIds);
    $hireMap[$row['manager_id']] = explode(',', $row['supervisor_id']);
}
$allSupervisorIds = array_unique($allSupervisorIds);

if(isset($_GET['status'])==1){
    $status1 = "Data Inserted Successfully";
}

//if(isset($_GET['status'])==2 && $_GET['status'] != ''){
//    $status2 = "There Is Problem To Insert Data";
//}

if(isset($_GET['sup_id'])) {
    $idToDelete = $_GET['sup_id'];
    
    // Fetch the row from the table
    $query = "SELECT * FROM assignproject";
    $result = $conn->query($query);
    
      if($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            // Remove the specified ID from the comma-separated list
            $idList = explode(',', $row['supervisor_id']);
            $updatedIdList = array_diff($idList, [$idToDelete]);
            
            // Check if the ID was found and removed
            if (count($idList) != count($updatedIdList)) {
                // If IDs are left after removal, update the column
                if (!empty($updatedIdList)) {
                    $updatedIdString = implode(',', $updatedIdList);
                    $updateQuery = "UPDATE assignproject SET supervisor_id = '$updatedIdString' WHERE id = {$row['id']}";
                } else {
                    // If no IDs are left, set the column to an empty string
                    $updateQuery = "UPDATE assignproject SET supervisor_id = '' WHERE id = {$row['id']}";
                }
                
                $updateResult = $conn->query($updateQuery);
                
                if($updateResult){
                    echo "User has been unassign";
                    $keyVar = 1;
                    header('Location:AssignMangersProject.php?message=' . $keyVar);
                }else{
                    echo "There is problem to unassign user";
                    $keyVar = 2;
                    header('Location:AssignMangersProject.php?message=' . $keyVar);
                }
                
                // Since the ID was found and removed, exit the loop
                break;
            }
        }
    } else {
        echo "No records found in the table.";
    }
} 

print "<center>";
displayHeader($prints, false, false);
displayLinks($current_module, $userlevel);
if($_GET['status']){
?>
<div class="alert alert-primary" role="alert" id="message">
    <?php echo $status1 . '<br>' . $status2; ?>
</div>
<?php } ?>
<form method="post" action="AssignMangersProject.php">
    <table width="800" border="1" cellpadding="1" bordercolor="#C0C0C0" cellspacing="-1">
        <tbody>
            <tr>
                <td>&nbsp;</td>
                <td><font face="Verdana" size="1"><b>Select Manager From Below</b></font></td>
            </tr>
            <tr>
                <td align="right" width="20%"><font size="2" face="Verdana">Manager : </font></td>
                <td width="80%">
                    <select name="managers" class="form-control" required="required" onchange="checkRelatedCheckbox(this.value)">
                        <option value="">---</option>
                        <?php foreach ($managerData as $managerAll) { ?>
                            <option value="<?php echo $managerAll[0]; ?>"><?php echo $managerAll[0] . ': ' . $managerAll[1]; ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
        </tbody>
    </table>
    <table width="800" border="1" cellpadding="1" bordercolor="#C0C0C0" cellspacing="-1">
        <thead>
<!--            <tr><th colspan="3">Select Project Managers</th></tr>-->
            <tr>
                <th align="left"></th>
                <th align="left">ID</th>
                <th align="left">Supervisor</th>
                <th align="left">Manager</th>
                <th align="left">Action</th>
            </tr>
        </thead>
        <tbody> 
            <?php
            foreach ($userData as $userAll) { 
//                $matchingId = $managerAll[0];
                $isChecked = in_array($userAll[0], $allSupervisorIds) ? 'checked disabled' : '';  
                if($isChecked){ 
                    $unassign = "<a href='AssignMangersProject.php?sup_id=" . $userAll[0] . "' class='btn btn-primary' style='text-decoration:none;'>Unassign</a>";
                }else{
                    $unassign = "";
                }
                ?>
                <tr>
                    <td><font face="Verdana" size="1"><input type="checkbox" name="supervisor[]" value="<?php echo $userAll[0]; ?>" <?php echo $isChecked; ?> /></font></td>
                    <td><font face="Verdana" size="1"><?php echo $userAll[0]; ?></font></td>
                    <td><font face="Verdana" size="1"><?php echo $userAll[1]; ?></font></td>
                    <td><font face="Verdana" size="1">
                        <?php
                        foreach ($assignPrjData as $assign) {
                            if (in_array($userAll[0], explode(',', $assign['supervisor_id']))) {
                                $managerIds = explode(',', $assign['manager_id']);
                                foreach ($managerIds as $managerId) {
                                    foreach ($managerData as $manager) {
                                        if ($manager[0] == $managerId) {
                                            $managerNames[$userAll[0]][] = $manager[1];
                                        }
                                    }
                                }
                            }
                        }
                        
                        if(isset($managerNames[$userAll[0]])) {
                            echo implode(', ', $managerNames[$userAll[0]]);
                        }
                        ?>
                        </font>
                    </td>
                    <td><font face="Verdana" size="1"><?php echo $unassign; ?></font></td>
                </tr>
            <?php } ?>

        </tbody>
    </table>
    <!-- <table width="800" border="1" cellpadding="1" bordercolor="#C0C0C0" cellspacing="-1">
        <thead>
            <tr><th colspan="3">Select Projects</th></tr>
            <tr>
                <th></th>
                <th>Code</th>
                <th>Project Name</th>
            </tr>
        </thead>
        <?php /* foreach ($projectData as $projectAll) { ?>
            <tr>
                <td><font face="Verdana" size="1"><input type="checkbox" name="projectname[]" value="<?php echo $projectAll[0]; ?>" /></font></td>
                <td><?php echo $projectAll[1]; ?></td>
                <td><?php echo $projectAll[2]; ?></td>
            </tr>
        <?php } */ ?>
    </table> -->
    <br>
    <input type="submit" name="submit" value="Assign Supervisor"/>
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
        $('input[type="checkbox"][name="supervisor[]"]').prop('checked', false);
    });
    
    var hireMap = <?php echo json_encode($hireMap); ?>;
    
    function checkRelatedCheckbox(managerId) {
        $('input[type="checkbox"][name="supervisor[]"]').prop('checked', false);
        $('input[name="supervisor[]"]').each(function () {
            if (hireMap[managerId] && hireMap[managerId].includes($(this).val())) {
                $(this).prop('checked', true);
//                $(this).prop('disabled', true);
            } else {
                $(this).prop('checked', false);
            }
        });
    }
    
</script>
<?php
print "</center>";

if (isset($_POST['submit'])) {
    $supervisor = count($_POST['supervisor']);
    $projectsname = count($_POST['projectname']);
    $assignID = $managers + $projectsname;
    $supervisorArray = $_POST['supervisor'];
    $projectArray = $_POST['projectname'];
    $managerID = $_POST['managers'];

    $projectID = implode(',', $projectArray);
    $supervisorID = implode(',', $supervisorArray);

    $checkQuery = "SELECT * FROM assignproject WHERE manager_id = $managerID";
    $result = mysqli_query($conn, $checkQuery);
    
    if (mysqli_num_rows($result) > 0) {
        $assignDataQuery = "UPDATE assignproject SET supervisor_id = CONCAT(supervisor_id, ',$supervisorID') WHERE manager_id = $managerID";
    }else{
        $assignDataQuery = "Insert into assignproject(supervisor_id,manager_id,project_id)values('$supervisorID',$managerID,'$projectID')";
    }
    $assignDataResult = mysqli_query($conn, $assignDataQuery);
    if ($assignDataResult) {
        $repVar = 1;
    } else {
        $repVar = 2;
    }
    header('Location:AssignMangersProject.php?status=' . $repVar);
}
