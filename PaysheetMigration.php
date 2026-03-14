<?php
ob_start("ob_gzhandler");
error_reporting(E_ALL);
set_time_limit(0);
ini_set("memory_limit", 0 - 1);
include "Functions.php";
$current_module = "19";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$username = $_SESSION[$session_variable . "username"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$userstatus = $_SESSION[$session_variable . "userstatus"];
$txtMACAddress = $_SESSION[$session_variable . "MACAddress"];
if (!(checkSession($userlevel, $current_module) && $_SESSION[$session_variable . "VirdiLevel"] == "Classic")) {
    header("Location: " . $config["REDIRECT"] . "?url=PaysheetMigration.php&message=Session Expired or Security Policy Violated");
}
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

$migrationQuery = "select * from paysheetmigration";
$migrationResult = mysqli_query($conn, $migrationQuery);
$migrationRaw = mysqli_fetch_all($migrationResult);

if($_GET['status']==1){
    $message1 = "<span style='color:red;'>Date Migrate Successfully</span>";
}
print "<html><title>Paysheet Migration Settings</title><body><center>";
displayHeader($prints, false, false);
print "<center>";
displayLinks($current_module, $userlevel);
?>
<div class="alert alert-primary" role="alert" id="message">
    <?php echo '<br>' . $message1 . '<br>'; ?>
</div>
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
<form method="post" action="PaysheetMigration.php">
    <table border="1" cellpadding="1" bordercolor="#C0C0C0" cellspacing="-1" width="800">
        <tr>
            <td align="right" width="25%"><font size="2" face="Verdana">From Date: </font></td>
            <td width="25%"><input type="text" placeholder="yyyymmdd" class="form-controls" name="from_date" value="<?php echo (isset($migrationRaw[0][1]))?$migrationRaw[0][1] : ''; ?>" required="required"></td>
            <td align="right" width="50%"></td>
        </tr>
        <tr>
            <td align="right" width="25%"><font size="2" face="Verdana">To Date: </font></td>
            <td width="25%"><input type="text" placeholder="yyyymmdd" class="form-controls" name="to_date" value="<?php echo (isset($migrationRaw[0][2]))?$migrationRaw[0][2] : ''; ?>" required="required"></td>
            <td align="right" width="50%"></td>
        </tr>
        <tr>
            <td align="right" width="25%"></td>
            <td align="right" width="25%"><input type="submit" class="form-controls" name="submit" value="Save"></td>
            <td align="right" width="50%"></td>
        </tr>
        
    </table>
</form>
<script>
    setTimeout(function () {
        document.getElementById("message").style.display = "none";
    }, 5000);
</script>
<?php
print "</center>";

if(isset($_POST['submit'])){
//    echo "<pre>";print_R($_POST);
    $fromdate = $_POST['from_date'];
    $todate = $_POST['to_date'];
    
    $fetchQuery = "select * from paysheetmigration where fromdate!='' and todate!=''";
    $fetchResult = mysqli_query($conn, $fetchQuery);
    $numrowscount = mysqli_num_rows($fetchResult);
    
    if($numrowscount == 0){
        $paysheetQuery = "insert into paysheetmigration(fromdate,todate)values('$fromdate','$todate')";
        $paysheetResult = mysqli_query($conn, $paysheetQuery);
    }else{
        $updateQuery = "update paysheetmigration SET fromdate='$fromdate', todate='$todate'";
        $UpdateResult = mysqli_query($conn, $updateQuery);
    }
    if($paysheetResult || $UpdateResult){
        header('Location:PaysheetMigration.php?status=1');
    }
}
?>