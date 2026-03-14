<?php
ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "18";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=UserDelete.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$act = $_GET["act"];
if ($act == "") {
    $act = $_POST["act"];
}
$prints = $_GET["prints"];
$excel = $_GET["excel"];
$message = $_GET["message"];
if ($message == "") {
    $message = "Employee Report";
}
$eDate = $_POST['e_date'];
$eId = $_POST['empid'];
$query = "SELECT * from tuser where id not in( select e_id from tenter where e_date>$eDate)and id >$eId";
$result = mysqli_query($conn, $query);

if(isset($_GET['ids'])){
    $selectedIDs = explode(",", $_GET['ids']);
    if (empty($selectedIDs)) {
        echo "No records selected for deletion.";
    } else {
//        $uconn = mysqli_connect("127.0.0.1", "unisuser", "unisamho", "UNIS");
        $uconn = mysqli_connect("localhost", "root", "namaste", "unis");
        $delID = implode(", ", $selectedIDs);

        $sql = "DELETE from tuser where id IN($delID)";
//        $result = mysqli_query($conn, $sql);
        
        $deltenterQuery= "DELETE from tenter where e_id NOT IN(select id from tuser)";
//        $deltenterResult = mysqli_query($conn, $deltenterQuery);
        
        $deldaymasterQuery= "DELETE from daymaster where e_id NOT IN(select id from tuser)";
//        $deldaymasterResult = mysqli_query($conn, $deldaymasterQuery);
        
        $deleteattendanceQuery = "DELETE from attendancemaster where empid NOT IN(select id from tuser)";
//        $deldaymasterResult = mysqli_query($conn, $deleteattendanceQuery);
        
        $dropTriggerQuery = "Drop trigger unis.d_tuser";
//        $dropTriggerResult = mysqli_query($uconn, $dropTriggerQuery);
        
        $unisTriggerQuery = "CREATE DEFINER=root@localhost 
        TRIGGER d_tuser AFTER DELETE ON tuser
        FOR EACH ROW
        DELETE FROM Access.tuser WHERE id = old.L_ID";
//        $unisTriggerResult = mysqli_query($uconn, $unisTriggerQuery);
        
        if ($unisTriggerResult === TRUE) {
            $deleteMsg = "Data deleted successfully";
        } else {
            echo "Error creating trigger: " . $uconn->error;
        }
    }
}
print "<html><title>Delete Employee</title>";
print "<center>";
if ($excel != "yes") {
    displayHeader($prints, true, false);
}
print "<center>";
if ($prints != "yes") {
    displayLinks($current_module, $userlevel);
}
print "</center>";
?>
<style>
    .form-controls{
        width: 100%;
    }
</style>
<div class="alert alert-primary" role="alert" id="message">
    <?php echo $deleteMsg; ?>
</div>
<form action="UserDelete.php" method="post">
    <table width="800" border="1" cellpadding="1" bordercolor="#C0C0C0" cellspacing="-1">
        <tr>
            <td align="right" width="25%"><font size="2" face="Verdana"></font></td>
            <td align="right" width="25%"><font size="2" face="Verdana">Date: </font></td>
            <td align="right" width="25%"><font size="2" face="Verdana"><input type="date" name="e_date" value="" class="form-controls"/></font></td>
            <td></td>
        </tr>
        <tr>
            <td align="right" width="25%"><font size="2" face="Verdana"></font></td>
            <td align="right" width="25%"><font size="2" face="Verdana">Employee ID: </font></td>
            <td align="right" width="25%"><font size="2" face="Verdana"><input type="text" name="empid" value="" class="form-controls"/></font></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td><input type="submit" name="search" value="Search Record" /></td>
            <td></td>
        </tr>
    </table>
</form>
<form>
<table width='800' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>
    <tr>
        <th></th>
        <th>Employee ID</th>
        <th>Name</th>
    </tr>
    <?php while($row = mysqli_fetch_array($result)){?>
    <tr>
        <td><input type="checkbox" name="tuser[]" value="<?php echo $row['id']; ?>"></td>
        <td><font face='Verdana' size='1'><?php echo $row['id']; ?></font></td>
        <td><font face='Verdana' size='1'><?php echo $row['name']; ?></font></td>
    </tr>
    <?php } ?>
    <tr>
        <td></td>
        <td><button type="button" onclick="confirmDelete()" class="btn btn-primary form-control">Delete Record</button></td>
        <td></td>
    </tr>
</table>
</form>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
//        function confirmDelete() {
//            const confirmed = confirm("Are you sure you want to delete selected record?");
//            if (confirmed) {
//                alert("Record deleted successfully!");
//                return true;
//            }else{ 
//                return false;
//            }
//        }
        
        function confirmDelete() {
            var checkboxes = document.querySelectorAll('input[type="checkbox"]:checked');
            
            if (checkboxes.length === 0) {
                alert("Please select at least one record to delete.");
            } else {
                var confirmed = confirm("Are you sure you want to delete the selected record(s)?");

                if (confirmed) {
                    // Create an array to store the IDs of the selected records.
                    var recordIDs = [];

                    for (var i = 0; i < checkboxes.length; i++) {
                        recordIDs.push(checkboxes[i].value);
                    }

                    // Redirect to the PHP script to handle the deletion.
                    window.location.href = "UserDelete.php?ids=" + recordIDs.join(",");
                }
            }
        }
        setTimeout(function () {
        document.getElementById("message").style.display = "none";
    }, 5000);
    </script>
   