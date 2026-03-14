<script src="resource/js/jquery.min.js"></script>
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

<?php
//ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "31";
set_time_limit(0);
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=PmFmProjectDetails.php&message=Session Expired or Security Policy Violated");
}
//$conn = openConnection();
$conn = new mysqli("localhost", "root", "namaste", "access_lfz");
// Check connection
if ($conn->connect_errno) {
    echo "Failed to connect to MySQL: " . $conn->connect_error;
    exit();
}
$prints = $_GET["prints"];
$excel = $_GET["excel"];
print "<html><title>PMFM Project Details</title>";
if ($prints != "yes") {
    print "<body>";
} else { //echo "HEY";die;
    if ($excel != "yes") {
        print "<body onLoad='javascript:window.print()'>";
    } else { //echo "HEY";die;
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=PmFmProjectDetails.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        print "<body>";
    }
}

$designationQuery = "SELECT * FROM tuser where name='$username'";
$designationResult = mysqli_query($conn, $designationQuery);
$designationRow = mysqli_fetch_assoc($designationResult);
$dept = $designationRow['dept'];

$selecthrsQuery = "select * from projecthrsallocation";
$selecthrsResult = mysqli_query($conn, $selecthrsQuery);

//$userQuery = "SELECT employee_id,fullname FROM onboardrequest where grade LIKE 'S%' AND status_reg= '1'";
if($designationRow['F8'] == 'Admin'){
    $userQuery = "SELECT id,name FROM tuser where F8='SV'";
}else{
    $userQuery = "SELECT id,name FROM tuser where F8='SV' AND dept='$dept'";
}
$userResult = mysqli_query($conn, $userQuery);
$userData = mysqli_fetch_all($userResult);

if ($_GET['status'] == 1) {
    $message1 = "Data Updated Successfully";
}
if ($_GET['status'] == 2) {
    $message2 = "There is an error to update data";
}
if ($_GET['status'] == 3) {
    $message3 = "Approved Successfully";
}


if (isset($_POST['submit'])) {
    
    $project1Data = $_POST['project1'];
    $project2Data = $_POST['project2'];
    $project3Data = $_POST['project3'];
    $project4Data = $_POST['project4'];
    $project5Data = $_POST['project5'];
    
    $project1amt = $_POST['project1amt'];
    $project2amt = $_POST['project2amt'];
    $project3amt = $_POST['project3amt'];
    $project4amt = $_POST['project4amt'];
    $project5amt = $_POST['project5amt'];
    
    foreach($project1amt as $project1amtdata){
        $project1amtDetails = explode(',', $project1amtdata);
        echo "<pre>";print_R($project1amtDetails);
        echo $updateprjAmt1Query = "update projecthrsallocation SET prj1hrsamt=$project1amtDetails[2] where empdate=$project1amtDetails[0] AND empcode=$project1amtDetails[1] AND parentempcode=$project1amtDetails[3]";
        mysqli_query($conn, $updateprjAmt1Query);
        echo "<br>";
    }
    foreach($project2amt as $project2amtdata){
        $project2amtDetails = explode(',', $project2amtdata);
        echo "<pre>";print_R($project2amtDetails);
        echo $updateprjAmt2Query = "update projecthrsallocation SET prj2hrsamt=$project2amtDetails[2] where empdate=$project2amtDetails[0] AND empcode=$project2amtDetails[1] AND parentempcode=$project2amtDetails[3]";
        mysqli_query($conn, $updateprjAmt2Query);
        echo "<br>";
    }
    foreach($project3amt as $project3amtdata){
        $project3amtDetails = explode(',', $project3amtdata);
        echo "<pre>";print_R($project3amtDetails);
        echo $updateprjAmt3Query = "update projecthrsallocation SET prj3hrsamt=$project3amtDetails[2] where empdate=$project3amtDetails[0] AND empcode=$project3amtDetails[1] AND parentempcode=$project3amtDetails[3]";
        mysqli_query($conn, $updateprjAmt3Query);
        echo "<br>";
    }
    foreach($project4amt as $project4amtdata){
        $project4amtDetails = explode(',', $project4amtdata);
        echo "<pre>";print_R($project4amtDetails);
        echo $updateprjAmt4Query = "update projecthrsallocation SET prj4hrsamt=$project4amtDetails[2] where empdate=$project4amtDetails[0] AND empcode=$project4amtDetails[1] AND parentempcode=$project4amtDetails[3]";
        mysqli_query($conn, $updateprjAmt4Query);
        echo "<br>";
    }
    foreach($project5amt as $project5amtdata){
        $project5amtDetails = explode(',', $project5amtdata);
        echo "<pre>";print_R($project5amtDetails);
        echo $updateprjAmt5Query = "update projecthrsallocation SET prj5hrsamt=$project5amtDetails[2] where empdate=$project5amtDetails[0] AND empcode=$project5amtDetails[1] AND parentempcode=$project5amtDetails[3]";
        mysqli_query($conn, $updateprjAmt5Query);
        echo "<br>";
    }
//    echo "<pre>";print_R($_POST);die;
    foreach ($project1Data as $project1DataAll) {
        $project1AllDetails = explode(',', $project1DataAll);
        // AND project1='$project1AllDetails[2]'
        $prj1Query = "select empdate,parent_id,child_id,project1 from projectapproval where empdate='$project1AllDetails[0]' AND parent_id='$project1AllDetails[1]' AND child_id='$project1AllDetails[3]'";
        $prj1Result = mysqli_query($conn, $prj1Query);
        $countrows = mysqli_num_rows($prj1Result);
        if ($countrows == 0) {
            $approveQuery = "insert into projectapproval(empdate,parent_id,child_id,project1,pm_project1,pm_status)"
                    . "values('$project1AllDetails[0]',$project1AllDetails[1],$project1AllDetails[3],'$project1AllDetails[2]','$username',1)";
//            echo "<br>";
            mysqli_query($conn, $approveQuery);
        } else {
            $approveUpdateQuery = "Update projectapproval SET "
                    . "project1='$project1AllDetails[2]',pm_project1='$username',pm_status=1 WHERE empdate='$project1AllDetails[0]' AND "
                    . "parent_id=$project1AllDetails[1] AND child_id=$project1AllDetails[3]";
//            echo "<br>";
            $prj1UpdateResult = mysqli_query($conn, $approveUpdateQuery);
        }
    }
    foreach ($project2Data as $project2DataAll) {
        $project2AllDetails = explode(',', $project2DataAll);
        $prj2Query = "select empdate,parent_id,child_id,project2 from projectapproval where empdate='$project2AllDetails[0]' AND parent_id='$project2AllDetails[1]' AND child_id='$project2AllDetails[3]'";
        $prj2Result = mysqli_query($conn, $prj2Query);
        $countrows2 = mysqli_num_rows($prj2Result);
        if ($countrows2 === 0) {
            $approve2Query = "insert into projectapproval(empdate,parent_id,child_id,project2,pm_project2,pm_status)"
                    . "values('$project2AllDetails[0]',$project2AllDetails[1],$project2AllDetails[3],'$project2AllDetails[2]','$username',1)";
            mysqli_query($conn, $approve2Query);
        } else {
            $approve2UpdateQuery = "Update projectapproval SET "
                    . "project2='$project2AllDetails[2]',pm_project2='$username',pm_status=1 WHERE empdate='$project2AllDetails[0]' AND "
                    . "parent_id=$project2AllDetails[1] AND child_id=$project2AllDetails[3]";
            $prj2UpdateResult = mysqli_query($conn, $approve2UpdateQuery);
        }
    }
    foreach ($project3Data as $project3DataAll) {
        $project3AllDetails = explode(',', $project3DataAll);
        $prj3Query = "select empdate,parent_id,child_id,project3 from projectapproval where empdate='$project3AllDetails[0]' AND parent_id='$project3AllDetails[1]' AND child_id='$project3AllDetails[3]'";
        $prj3Result = mysqli_query($conn, $prj3Query);
        $countrows3 = mysqli_num_rows($prj3Result);
        if ($countrows3 === 0) {
            $approve3Query = "insert into projectapproval(empdate,parent_id,child_id,project3,pm_project3,pm_status)"
                    . "values('$project3AllDetails[0]',$project3AllDetails[1],$project3AllDetails[3],'$project3AllDetails[2]','$username',1)";
            mysqli_query($conn, $approve3Query);
        } else {
            $approve3UpdateQuery = "Update projectapproval SET "
                    . "project3='$project3AllDetails[2]',pm_project3='$username',pm_status=1 WHERE empdate='$project3AllDetails[0]' AND "
                    . "parent_id=$project3AllDetails[1] AND child_id=$project3AllDetails[3]";
            $prj3UpdateResult = mysqli_query($conn, $approve3UpdateQuery);
        }
    }
    foreach ($project4Data as $project4DataAll) {
        $project4AllDetails = explode(',', $project4DataAll);
        $prj4Query = "select empdate,parent_id,child_id,project4 from projectapproval where empdate='$project4AllDetails[0]' AND parent_id='$project4AllDetails[1]' AND child_id='$project4AllDetails[3]'";
        $prj4Result = mysqli_query($conn, $prj4Query);
        $countrows4 = mysqli_num_rows($prj4Result);
        if ($countrows4 === 0) {
            $approve4Query = "insert into projectapproval(empdate,parent_id,child_id,project4,pm_project4,pm_status)"
                    . "values('$project4AllDetails[0]',$project4AllDetails[1],$project4AllDetails[3],'$project4AllDetails[2]','$username',1)";
            mysqli_query($conn, $approve4Query);
        } else {
            $approve4UpdateQuery = "Update projectapproval SET "
                    . "project4='$project4AllDetails[2]',pm_project4='$username',pm_status=1 WHERE empdate='$project4AllDetails[0]' AND "
                    . "parent_id=$project4AllDetails[1] AND child_id=$project4AllDetails[3]";
            $prj4UpdateResult = mysqli_query($conn, $approve4UpdateQuery);
        }
    }
    foreach ($project5Data as $project5DataAll) {
        $project5AllDetails = explode(',', $project5DataAll);
        $prj5Query = "select empdate,parent_id,child_id,project5 from projectapproval where empdate='$project5AllDetails[0]' AND parent_id='$project5AllDetails[1]' AND child_id='$project5AllDetails[3]'";
        $prj5Result = mysqli_query($conn, $prj5Query);
        $countrows5 = mysqli_num_rows($prj5Result);
        if ($countrows5 === 0) {
            $approve5Query = "insert into projectapproval(empdate,parent_id,child_id,project5,pm_project4,pm_status)"
                    . "values('$project5AllDetails[0]',$project5AllDetails[1],$project5AllDetails[3],'$project5AllDetails[2]','$username',1)";
            mysqli_query($conn, $approve5Query);
        } else {
            $approve5UpdateQuery = "Update projectapproval SET "
                    . "project5='$project5AllDetails[2]',pm_project5='$username',pm_status=1 WHERE empdate='$project5AllDetails[0]' AND "
                    . "parent_id=$project5AllDetails[1] AND child_id=$project5AllDetails[3]";
            $prj5UpdateResult = mysqli_query($conn, $approve5UpdateQuery);
        }
    }
    header("Location: PmFmProjectDetails.php?status=3");
}

$designationQuery = "SELECT * FROM tuser where name='$username'";
$designationResult = mysqli_query($conn, $designationQuery);
$designationRow = mysqli_fetch_assoc($designationResult);

print "<center>";
displayHeader($prints, false, false);
displayLinks($current_module, $userlevel);
?>
<table border="1" cellpadding="1" bordercolor="#C0C0C0" cellspacing="-1" width="800">
    <thead>
        <tr>
            <th align="right" width="25%"><font face="Verdana" size="2">Supervisor Code & Name: </font></th>
            <td width="55%">

                <select name="hireauth" class="form-controls" id="hireauth">
                    <?php foreach ($userData as $userAll) { ?>
                        <option value="<?php echo $userAll[0]; ?>" name="<?php echo $userAll[1]; ?>" <?php echo ($userAll[0] == $updateResult[1]) ? "selected" : ""; ?>><?php echo $userAll[0] . ': ' . $userAll[1]; ?></option>
                    <?php } ?>
                </select>

            </td>
            <?php if ($designationRow['F8'] == 'FC' || $designationRow['F8'] == 'Admin' || $designationRow['F8'] == 'HOD' || $designationRow['F8'] == 'PM') { ?>
                <td width="20%" colspan="2"><font face="Verdana" size="2">Show Supervisor Records: <input type="checkbox" name="parent"  id="parent"/></font></td>
            <?php } ?>
        </tr>
        <tr>
            <th align="right" width="25%"><font face="Verdana" size="2">From Date: </font></th>
            <td width="25%"><input type="date" size="12" name="frmdate"  maxlength="12" value="<?php echo date('Y-m-d'); ?>"  class="form-control" id="frmdate" value="<?php echo $fromDate; ?>"/></td>
            <th align="right" width="25%"><font face="Verdana" size="2">To Date: </font></th>
            <td width="25%"><input type="date" size="12" name="todate"  maxlength="12" value="<?php echo date('Y-m-d'); ?>"  class="form-controls" id="todate"/></td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td width="25%"><input type="button" id="searchfilter" class="form-control" value="Search"></td>
            <!--<td width="25%"><input type='button' value='Excel' id="exportButton" class='btn btn-primary'></td>-->
            <td width="25%">
                <!--<button id="exportButton">Export to Excel</button>-->
            </td>

        </tr>
    </thead>
</table>
<?php if ($_GET['status']) { ?>
    <div class="alert alert-primary" role="alert" id="message">
        <?php echo $message1 . '<br>' . $message2 . '<br>' . $message3; ?>
    </div>
<?php } ?>
<form method="post" action="PmFmProjectDetails.php" name="frm1" id="prjDetail">
    <table border="1" cellpadding="1" bordercolor="#C0C0C0" cellspacing="-1" id="exportexcel">
        <thead>
            <tr>
                <!--<th><font face="Verdana" size="2">ID</font></th>-->
                <!--<th><font face="Verdana" size="2">Employee ID</font></th>-->
                <!--<th><font face="Verdana" size="1"><input type="checkbox" id="select-all" /></font></th>-->
                <th><font face="Verdana" size="1">Date</font></th>
                <th><font face="Verdana" size="1">Supervisor Code</font></th>
                <th><font face="Verdana" size="1">Supervisor Name</font></th>
                <th><font face="Verdana" size="1">Emp Code</font></th>
                <th><font face="Verdana" size="1">Emp Name</font></th>
                <th><font face="Verdana" size="1">Grade</font></th>
                <!--<th><font face="Verdana" size="1">Terminal Out</font></th>-->
                <th><font face="Verdana" size="1">Entry Time</font></th>
                <th><font face="Verdana" size="1">Out Time</font></th>
                <th><font face="Verdana" size="1">Total Hours Work</font></th>
                <th><font face="Verdana" size="1">Manager Approval</font></th>
                <th><font face="Verdana" size="1">PM Approval</font></th>
                <th><font face="Verdana" size="1">
                    <label for="prj1">Project 1/Location 1</label></font>
                </th>
                <th><font face="Verdana" size="1">
                    <label for="prj1hrs">Project 1 Hours</label></font>
                </th>            
                <th><font face="Verdana" size="1">
                    <label for="prj1hrsamt">Project 1 Hours Amount</label></font>
                </th>
                <th><font face="Verdana" size="1">Manager Approval</font></th>
                <th><font face="Verdana" size="1">PM Approval</font></th>
                <th><font face="Verdana" size="1">
                    <label for="prj1">Project 2/Location 2</label></font>
                </th>
                <th><font face="Verdana" size="1">
                    <label for="prj1">Project 2 Hours</label></font>
                </th>
                <th><font face="Verdana" size="1">
                    <label for="prj2hrsamt">Project 2 Hours Amount</label></font>
                </th>
                <th><font face="Verdana" size="1">Manager Approval</font></th>
                <th><font face="Verdana" size="1">PM Approval</font></th>
                <th><font face="Verdana" size="1">
                    <label for="prj1">Project 3/Location 3</label></font>
                </th>
                <th><font face="Verdana" size="1">
                    <label for="prj1">Project 3 Hours</label></font>
                </th>
                <th><font face="Verdana" size="1">
                    <label for="prj3hrsamt">Project 3 Hours Amount</label></font>
                </th>
                <th><font face="Verdana" size="1">Manager Approval</font></th>
                <th><font face="Verdana" size="1">PM Approval</font></th>
                <th><font face="Verdana" size="1">
                    <label for="prj1">Project 4/Location 4</label></font>
                </th>
                <th><font face="Verdana" size="1">
                    <label for="prj1">Project 4 Hours</label></font>
                </th>
                <th><font face="Verdana" size="1">
                    <label for="prj4hrsamt">Project 4 Hours Amount</label></font>
                </th>
                <th><font face="Verdana" size="1">Manager Approval</font></th>
                <th><font face="Verdana" size="1">PM Approval</font></th>
                <th><font face="Verdana" size="1">
                    <label for="prj1">Project 5/Location 5</label></font>
                </th>
                <th><font face="Verdana" size="1">
                    <label for="prj1">Project 5 Hours</label></font>
                </th>
                <th><font face="Verdana" size="1">
                    <label for="prj5hrsamt">Project 5 Hours Amount</label></font>
                </th>
                <th><font face="Verdana" size="1">Total Project Hours</font></th>
                <th><font face="Verdana" size="1">Unpaid Hours</font></th>
                <th><font face="Verdana" size="1">Additional Hours</font></th>
                <th><font face="Verdana" size="1">Addtitional Amount</font></th>
                <th><font face="Verdana" size="1">Regular Day Rate</font></th>
                <th><font face="Verdana" size="1">Remarks</font></th>
                <th><font face="Verdana" size="1">Action</font></th>
            </tr>
        </thead>
        <tbody id="childemp"></tbody>
    </table>
    <input type="submit" name="submit" value="Approve" />
</form>
<button id="exportToExcelBtn" class="btn btn-primary">Export to Excel</button>
<div id="loader">
    <img src="img/loader.gif" >
</div>
</center>
</body>
</html>
<script type="text/javascript">
    $(document).ready(function () {
        $('#loader').hide();
        $('#exportToExcelBtn').hide();
        
//    $("#searchfilter").click(function () {
        document.getElementById("searchfilter").addEventListener("click", function () {
            $('#loader').show();
            var searchValue = $("#hireauth").val();
            var fromDate = $("#frmdate").val();
            var toDate = $("#todate").val();
            var parent = $("#parent").checked;
            var checkboxes = document.getElementsByName("parent");
            const selectedCheckboxes = Array.from(checkboxes)
                    .filter(checkbox => checkbox.checked)
                    .map(checkbox => checkbox.value);

//            var empcode = $("#empcode").val();

            $.ajax({
                type: "POST",
                url: "FetchPmFmFilter.php",
                data: {
                    fromDate: fromDate,
                    toDate: toDate,
//                    prjcode: toDate,
                    flag: selectedCheckboxes,
                    searchValue: searchValue
                },
                success: function (response) {
                    $("#childemp").html(response);
                    $('#loader').hide();
                    updateURL(fromDate, toDate);
                    $('#exportToExcelBtn').show();
                },
                error: function () {
                    // Handle errors and hide loader
                    $('#loader').hide();
                }
            });
        });

        setTimeout(function () {
            document.getElementById("message").style.display = "none";
        }, 5000);
    });
</script>
<script src="resource/js/footerscript.js"></script>


