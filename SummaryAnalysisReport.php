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
if($_POST['search']){
$frmdate = date('Ymd', strtotime($_POST['frmdate']));    
$todate = date('Ymd', strtotime($_POST['todate']));   
$analysisQuery = "select empname, project1,project2,project3,project4,project5, SUM(prj1hrsamt) as prj1amt, SUM(prj2hrsamt) as prj2amt, "
        . "SUM(prj3hrsamt) as prj3amt, SUM(prj4hrsamt) as prj4amt, SUM(prj5hrsamt) as prj5amt, SUM(project1hrs) as prj1hrs, SUM(project2hrs) as prj2hrs, "
        . "SUM(project3hrs) as prj3hrs, SUM(project4hrs) as prj4hrs, SUM(project5hrs) as prj5hrs from "
        . "projecthrsallocation where empdate >='$frmdate' AND empdate<='$todate' group by empname";
}
$analysisResult = mysqli_query($conn, $analysisQuery);
$analysisRaw = mysqli_fetch_all($analysisResult, MYSQLI_ASSOC);
foreach ($analysisRaw as $projectAmount) {
    $projectwiseamt[] = $projectAmount;
}

$projectAllQuery = "select Name from projectmaster";
$projectAllResult = mysqli_query($conn, $projectAllQuery);
$projectAllRaw = mysqli_fetch_all($projectAllResult, MYSQLI_ASSOC);

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
<form method="post" action="SummaryAnalysisReport.php">
    <table border="1" cellpadding="1" bordercolor="#C0C0C0" cellspacing="-1" width="800">
    <tr>
        <th align="right" width="25%"><font face="Verdana" size="2">From Date: </font></th>
        <td width="25%"><input type="date" size="12" name="frmdate"  maxlength="12" value=""  class="form-controls" id="frmdate"/></td>
        <th align="right" width="25%"><font face="Verdana" size="2">To Date: </font></th>
        <td width="25%"><input type="date" size="12" name="todate"  maxlength="12" value=""  class="form-controls" id="todate"/></td>
        <td colspan="2"><input type="submit" id="searchfilter" name="search" class="form-controls" value="Search"></td>
    </tr>
    </table>
</form>
<table border="1" cellpadding="1" bordercolor="#C0C0C0" cellspacing="-1" width="800">
    <tbody>
        <tr>
            <th>Name</th>
            <th>Total Hrs Allocated</th>
            <th>Cost Allocated (NGN)</th>
        </tr>
        <?php
        $prj1amount = 0;
        $prj2amount = 0;
        $prj3amount = 0;
        $prj4amount = 0;
        $prj5amount = 0;
        $prj1hrstotal = 0;
        $prj2hrstotal = 0;
        $prj3hrstotal = 0;
        $prj4hrstotal = 0;
        $prj5hrstotal = 0;
        foreach ($projectAllRaw as $allProjects) {
            echo "<tr>";
            echo "<th>" . $allProjects['Name'] . "</th>";
            foreach ($projectwiseamt as $projectwiseAllAmt) {
                if ($projectwiseAllAmt['project1'] == $allProjects['Name']) {
                    $prj1hrstotal += $projectwiseAllAmt['prj1hrs'];
                    $prj1amount += $projectwiseAllAmt['prj1amt'];
                }
                if ($projectwiseAllAmt['project2'] == $allProjects['Name']) {
                    $prj2hrstotal += $projectwiseAllAmt['prj2hrs'];
                    $prj2amount += $projectwiseAllAmt['prj2amt'];
                }
                if ($projectwiseAllAmt['project3'] == $allProjects['Name']) {
                    $prj3hrstotal += $projectwiseAllAmt['prj3hrs'];
                    $prj3amount += $projectwiseAllAmt['prj3amt'];
                }
                if ($projectwiseAllAmt['project4'] == $allProjects['Name']) {
                    $prj4hrstotal += $projectwiseAllAmt['prj4hrs'];
                    $prj4amount += $projectwiseAllAmt['prj4amt'];
                }
                if ($projectwiseAllAmt['project5'] == $allProjects['Name']) {
                    $prj5hrstotal += $projectwiseAllAmt['prj5hrs'];
                    $prj5amount += $projectwiseAllAmt['prj5amt'];
                }
            }
//            echo "<pre>";print_R($projectwiseamt);
            
//            if ($allProjects['Name'] == 'MANAGEMENT') {
            if(in_array('MANAGEMENT', $allProjects)){
                echo "<td><center>" . $prj1hrstotal . "</center></td>";
                echo "<td><center>" . $prj1amount . "</center></td>";
            }
//            if ($allProjects['Name'] == 'FINANCE') {
            if(in_array('FINANCE', $allProjects)){
                echo "<td><center>" . $prj2hrstotal . "</center></td>";
                echo "<td><center>" . $prj2amount . "</center></td>";
            }
//            if ($allProjects['Name'] == 'MAINTANANCE') {
            if(in_array('MAINTANANCE', $allProjects)){
                echo "<td><center>" . $prj3hrstotal . "</center></td>";
                echo "<td><center>" . $prj3amount . "</center></td>";
            }
//            if ($allProjects['Name'] == 'REAL ESTATE') {
            if(in_array('REAL ESTATE', $allProjects)){
                echo "<td><center>" . $prj4hrstotal . "</center></td>";
                echo "<td><center>" . $prj4amount . "</center></td>";
            }
//            if ($allProjects['Name'] == 'IT PROJECT') {
            if(in_array('IT PROJECT', $allProjects)){
                echo "<td><center>" . $prj5hrstotal . "</center></td>";
                echo "<td><center>" . $prj5amount . "</center></td>";
            }
            echo "</tr>";
        }
        $grandTotalHrs = $prj1hrstotal+$prj2hrstotal+$prj3hrstotal+$prj4hrstotal+$prj5hrstotal;
        $grandTotalCost = $prj1amount+$prj2amount+$prj3amount+$prj4amount+$prj5amount;
        echo "<tr><th>Grand Total</th><th>$grandTotalHrs</th><th>$grandTotalCost</th></tr>";
        ?>
    </tbody>
</table>
<?php
print "</center>";
