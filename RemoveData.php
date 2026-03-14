<?php

ob_start("ob_gzhandler");
error_reporting(E_ALL);
set_time_limit(0);
include "Functions.php";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
$conn = openConnection();
$iconn = openIConnection();
$jconn = openIConnection();

$unis_conn = mysqli_connect('localhost', 'root', 'namaste', 'unis');
print "<center>";
displayHeader($prints, true, false);
displayLinks("", $userlevel);
print "</center>";
print "<html><title>AddPayMasterUser</title>";
print "<body>";
print "<center>";
print "<form method='post' action='RemoveData.php' onsubmit='return confirmation()'>";
print "<table width='800' cellpadding='1'  cellspacing='1'>";
print "<tr><td>&nbsp;</td></tr>";
print "<tr><td>&nbsp;&nbsp;&nbsp;<b>Delete Data From Tables</b></td></tr>";
print "<tr><td>&nbsp;</td></tr>";
print "<tr>";
//print "<td>&nbsp;</td>";
//displayTextbox("url", "URL</font>: ", $txtTo, $prints, 12, "25%", "75%");
//print "<td>&nbsp;&nbsp;&nbsp;<input type='date' name='removedatadate' value='' />";
print "</td>";
print "</tr>";
//print "<br>";
print "<tr><td>&nbsp;</td></tr>";
print "<tr>";
//print "<td>&nbsp;</td>";
print "<td>&nbsp;&nbsp;&nbsp;<input type='submit' name='deleteaccess' value='Delete Data'/>";
print "</td>";
print "</tr>";
print "<tr><td>&nbsp;</td></tr>";
print "</table>";
print "</form>";
print "</center>";
print "</body>";


if ($_POST['deleteaccess']) {

//    echo "<pre>";
//    print_R($_POST);
    $tenterQuery = "DELETE from tenter where e_id not in (select id from tuser)";
    $tenterResult = updateidata($iconn, $tenterQuery, true);//mysqli_query($conn, $tenterQuery);
    if ($tenterResult == true) {
        echo "<center>Data removed from tenter table</center>";
        echo "<br>";
    }else{
        echo "<center>No data found to removed from tenter table</center>";
        echo "<br>";
    }
    $daymasterQuery = "DELETE from daymaster where e_id not in (select id from tuser)";
    $daymasterResult = updateidata($iconn, $daymasterQuery, true);//mysqli_query($conn, $daymasterQuery);
    
    if ($daymasterResult) {
        echo "<center>Data removed from daymaster table</center>";
        echo "<br>";
    }else{
        echo "<center>No data found to removed from daymaster table</center>";
        echo "<br>";
    }
        
    $attendancemasterQuery = "DELETE from attendancemaster where empid not in (select id from tuser)";
    $attendacemasterResult = updateidata($iconn, $attendancemasterQuery, true);//mysqli_query($conn, $attendancemasterQuery);
    
    if ($attendacemasterResult) {
        echo "<center>Data removed from attendancemaster table</center>";
        echo "<br>";
    }else{
        echo "<center>No data found to removed from attendancemaster table</center>";
        echo "<br>";
    }
    
    $unistuserQuery = "DELETE FROM `unis`.`tuser` where L_ID not in (select id from `access`.`tuser`)";
    $unistuserResult = updateidata($unis_conn, $unistuserQuery, true);
    if ($unistuserResult) {
        echo "<center>Data removed from unis tuser table</center>";
        echo "<br>";
    }else{
        echo "<center>No data found to removed from unis tuser table</center>";
        echo "<br>";
    }
    
    $unistemployeeQuery = "DELETE FROM `unis`.`temploye` where L_UID not in (select L_ID from tuser)";
    $unistemployeeResult = updateidata($unis_conn, $unistemployeeQuery, true);
    if ($unistemployeeResult) {
        echo "<center>Data removed from unis temployee table</center>";
        echo "<br>";
    }else{
        echo "<center>No data found to removed from unis temployee table</center>";
        echo "<br>";
    }
    
    $unistenterQuery = "DELETE FROM `unis`.`tenter` where L_UID not in (select L_ID from tuser)";
    $unistenterResult = updateidata($unis_conn, $unistenterQuery, true);
    if ($unistenterResult) {
        echo "<center>Data removed from unis tenter table</center>";
        echo "<br>";
    }else{
        echo "<center>No data found to removed from unis tenter table</center>";
        echo "<br>";
    }
}
?>
<script>
function confirmation(){
    var result = confirm("Are you sure want to delete?");
    if (result == true) {
        return true;
      } else {
        return false;
      } 
}
</script>