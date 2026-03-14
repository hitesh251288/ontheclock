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
$prints = $_GET["prints"];
$excel = $_GET["excel"];
print "<html><title>Project Hours Allocation View</title>";
if ($prints != "yes") {
    print "<body>";
} else { //echo "HEY";die;
    if ($excel != "yes") {
        print "<body onLoad='javascript:window.print()'>";
    } else { //echo "HEY";die;
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=ProjectHoursAllocationView.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        print "<body>";
    }
}

$designationQuery = "SELECT * FROM tuser where name='$username'";
$designationResult = mysqli_query($conn, $designationQuery);
$designationRow = mysqli_fetch_assoc($designationResult);
//echo "<pre>";print_r($_SESSION);

if ($designationRow['F8'] == 'SV') {
    if (isset($_POST['search'])) {
        $frmdate = date('Ymd', strtotime($_POST['frmdate']));
        $todate = date('Ymd', strtotime($_POST['todate']));
        $dept = $designationRow['dept'];
        $selecthrsQuery = "select p.*,t.dept from projecthrsallocation p LEFT JOIN tuser t ON t.id=p.parentempcode where p.parentempname='$username' AND p.empdate >='$frmdate' AND p.empdate <='$todate' AND p.totalprjhrs!='' AND t.id=p.parentempcode AND t.dept='$dept'";
    }
} else { // where empdate='20230814'
    if (isset($_POST['search'])) {
        $frmdate = date('Ymd', strtotime($_POST['frmdate']));
        $todate = date('Ymd', strtotime($_POST['todate']));
        $dept = $designationRow['dept'];
        if($designationRow['F8'] == 'Admin'){
//            $selecthrsQuery = "select p.*,t.dept from projecthrsallocation p LEFT JOIN tuser t ON t.id=p.empcode where p.empdate >='$frmdate' AND p.empdate <='$todate' AND p.totalprjhrs!=''";
            $selecthrsQuery = "select p.*,t.dept from projecthrsallocation p LEFT JOIN tuser t ON t.id=p.parentempcode where p.empdate >='$frmdate' AND p.empdate <='$todate' AND p.totalprjhrs!=''";
        }else{
            $selecthrsQuery = "select p.*,t.dept from projecthrsallocation p LEFT JOIN tuser t ON t.id=p.parentempcode where p.empdate >='$frmdate' AND p.empdate <='$todate' AND p.totalprjhrs!='' AND t.id=p.parentempcode AND t.dept='$dept'";
        }
    }
}
$selecthrsResult = mysqli_query($conn, $selecthrsQuery);

if (isset($_POST['enablelink'])) {
    //empdate >=20230801 and empdate <=20230831
    $enableQuery = "Update projecthrsallocation set approve = 1 where approve=0";
    mysqli_query($conn, $enableQuery);
    header('Location:ProjectHoursAllocationView.php');
}
if (isset($_POST['disablelink'])) {
    $disableQuery = "Update projecthrsallocation set approve = 0 where approve=1";
    mysqli_query($conn, $disableQuery);
    header('Location:ProjectHoursAllocationView.php');
}

if (isset($_POST['submit'])) {
//    echo "<pre>";print_R($_POST);die;
    $project1Data = $_POST['project1'];
    $project2Data = $_POST['project2'];
    $project3Data = $_POST['project3'];
    $project4Data = $_POST['project4'];
    $project5Data = $_POST['project5'];
    foreach ($project1Data as $project1DataAll) {
        $project1AllDetails = explode(',', $project1DataAll);
//        echo "<pre>";print_R($project1AllDetails);
        // AND project1='$project1AllDetails[2]'
        $prj1Query = "select empdate,parent_id,child_id,project1 from projectapproval where empdate='$project1AllDetails[0]' AND parent_id='$project1AllDetails[1]' AND child_id='$project1AllDetails[3]'";
        $prj1Result = mysqli_query($conn, $prj1Query);
        $countrows = mysqli_num_rows($prj1Result);
        if ($countrows == 0) {
            $approveQuery = "insert into projectapproval(empdate,parent_id,child_id,p1_approveby,project1)"
                    . "values('$project1AllDetails[0]',$project1AllDetails[1],$project1AllDetails[3],'$username','$project1AllDetails[2]')";
            mysqli_query($conn, $approveQuery);
        } else {
            $approveUpdateQuery = "Update projectapproval SET p1_approveby='$username', "
                    . "project1='$project1AllDetails[2]' WHERE empdate='$project1AllDetails[0]' AND "
                    . "parent_id=$project1AllDetails[1] AND child_id=$project1AllDetails[3]";
            $prj1UpdateResult = mysqli_query($conn, $approveUpdateQuery);
        }
    }
    foreach ($project2Data as $project2DataAll) {
        $project2AllDetails = explode(',', $project2DataAll);
        $prj2Query = "select empdate,parent_id,child_id,project2 from projectapproval where empdate='$project2AllDetails[0]' AND parent_id='$project2AllDetails[1]' AND child_id='$project2AllDetails[3]'";
        $prj2Result = mysqli_query($conn, $prj2Query);
        $countrows2 = mysqli_num_rows($prj2Result);
        if ($countrows2 === 0) {
            $approve2Query = "insert into projectapproval(empdate,parent_id,child_id,p2_approveby,project2)"
                    . "values('$project2AllDetails[0]',$project2AllDetails[1],$project2AllDetails[3],'$username','$project2AllDetails[2]')";
            mysqli_query($conn, $approve2Query);
        } else {
            echo $approve2UpdateQuery = "Update projectapproval SET p2_approveby='$username', "
            . "project2='$project2AllDetails[2]' WHERE empdate='$project2AllDetails[0]' AND "
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
            $approve3Query = "insert into projectapproval(empdate,parent_id,child_id,p3_approveby,project3)"
                    . "values('$project3AllDetails[0]',$project3AllDetails[1],$project3AllDetails[3],'$username','$project3AllDetails[2]')";
            mysqli_query($conn, $approve3Query);
        } else {
            $approve3UpdateQuery = "Update projectapproval SET p3_approveby='$username', "
                    . "project3='$project3AllDetails[2]' WHERE empdate='$project3AllDetails[0]' AND "
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
            $approve4Query = "insert into projectapproval(empdate,parent_id,child_id,p4_approveby,project4)"
                    . "values('$project4AllDetails[0]',$project4AllDetails[1],$project4AllDetails[3],'$username','$project4AllDetails[2]')";
            mysqli_query($conn, $approve4Query);
        } else {
            $approve4UpdateQuery = "Update projectapproval SET p4_approveby='$username', "
                    . "project4='$project4AllDetails[2]' WHERE empdate='$project4AllDetails[0]' AND "
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
            $approve5Query = "insert into projectapproval(empdate,parent_id,child_id,p5_approveby,project5)"
                    . "values('$project5AllDetails[0]',$project5AllDetails[1],$project5AllDetails[3],'$username','$project5AllDetails[2]')";
            mysqli_query($conn, $approve5Query);
        } else {
            $approve5UpdateQuery = "Update projectapproval SET p5_approveby='$username', "
                    . "project5='$project5AllDetails[2]' WHERE empdate='$project5AllDetails[0]' AND "
                    . "parent_id=$project5AllDetails[1] AND child_id=$project5AllDetails[3]";
            $prj5UpdateResult = mysqli_query($conn, $approve5UpdateQuery);
        }
    }
    header('Location: ProjectHoursAllocationView.php?status=3');
}

$designationQuery = "SELECT * FROM tuser where name='$username'";
$designationResult = mysqli_query($conn, $designationQuery);
$designationRow = mysqli_fetch_assoc($designationResult);

if ($_GET['status'] == 1) {
    $message1 = "Data Updated Successfully";
}
if ($_GET['status'] == 2) {
    $message2 = "There is an error to update data";
}
if ($_GET['status'] == 3) {
    $message3 = "Approved Successfully";
}

print "<center>";
displayHeader($prints, false, false);
displayLinks($current_module, $userlevel);
if ($_GET['status']) {
    ?>
    <div class="alert alert-primary" role="alert" id="message">
        <?php echo $message1 . '<br>' . $message2 . '<br>' . $message3; ?>
    </div>
<?php } ?>
<style>
    .disabledButton {
        pointer-events: none;
        opacity: 0.5; 
    }
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
<?php if ($designationRow['F8'] == 'Admin' || $designationRow['F8'] == 'FC') { ?>
    <form method="POST" action="">
        <button id="toggleEditLinks" class="btn btn-primary" name="enablelink">Enable Link</button>
        <button id="toggleEditLinks" class="btn btn-primary" name="disablelink" >Disable Link</button>
    </form>
<?php } ?>
<a href="ProjectHoursAllocation.php" class="btn btn-primary" style="text-decoration: none;">Add Project Hours Allocation</a>
<table border="1" cellpadding="1" bordercolor="#C0C0C0" cellspacing="-1" width="800" id="dateForm">
    <form method="post" action="">
        <thead>
            <tr>
                <th align="right" width="25%"><font face="Verdana" size="2">From Date: </font></th>
                <td width="25%"><input type="date" size="12" name="frmdate"  maxlength="12" value="<?php echo date('Y-m-d'); ?>"  class="form-controls" id="frmdate"/></td>
                <th align="right" width="25%"><font face="Verdana" size="2">To Date: </font></th>
                <td width="25%"><input type="date" size="12" name="todate"  maxlength="12" value="<?php echo date('Y-m-d'); ?>"  class="form-controls" id="todate"/></td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td width="25%"><input type="submit" id="searchfilter" name="search" class="form-control" value="Search"></td>
                <td width="25%"></td>
            </tr>
        </thead>
    </form>
</table>
<form method="post" action="ProjectHoursAllocationView.php" id="prjDetail">
    <table border="1" cellpadding="1" bordercolor="#C0C0C0" cellspacing="-1" width="100%" id="exportexcel">
        <thead>
            <tr>
                <!--<th><font face="Verdana" size="2">ID</font></th>-->
                <!--<th><font face="Verdana" size="2">Employee ID</font></th>-->
                <th><font face="Verdana" size="1">Date</font></th>
                <th><font face="Verdana" size="1">Parent Code</font></th>
                <th><font face="Verdana" size="1">Parent Name</font></th>
                <th><font face="Verdana" size="1">Emp Code</font></th>
                <th><font face="Verdana" size="1">Emp Name</font></th>
    <!--            <th><font face="Verdana" size="1">Terminal IN</font></th>
                <th><font face="Verdana" size="1">Terminal Out</font></th>-->
                <th><font face="Verdana" size="1">Entry Time</font></th>
                <th><font face="Verdana" size="1">Out Time</font></th>
                <th><font face="Verdana" size="1">Total Hours Work</font></th>
                <th></th>
                <th><font face="Verdana" size="1">
                    <label for="prj1">Project 1/Location 1</label></font>
                </th>
                <th><font face="Verdana" size="1">
                    <label for="prj1">Project 1 Hours</label></font>
                </th>
                <th></th>
                <th><font face="Verdana" size="1">
                    <label for="prj1">Project 2/Location 2</label></font>
                </th>
                <th><font face="Verdana" size="1">
                    <label for="prj1">Project 2 Hours</label></font>
                </th>
                <th></th>
                <th><font face="Verdana" size="1">
                    <label for="prj1">Project 3/Location 3</label></font>
                </th>
                <th><font face="Verdana" size="1">
                    <label for="prj1">Project 3 Hours</label></font>
                </th>
                <th></th>
                <th><font face="Verdana" size="1">
                    <label for="prj1">Project 4/Location 4</label></font>
                </th>
                <th><font face="Verdana" size="1">
                    <label for="prj1">Project 4 Hours</label></font>
                </th>
                <th></th>
                <th><font face="Verdana" size="1">
                    <label for="prj1">Project 5/Location 5</label></font>
                </th>
                <th><font face="Verdana" size="1">
                    <label for="prj1">Project 5 Hours</label></font>
                </th>
                <th><font face="Verdana" size="1">Total Project Hours</font></th>
                <th><font face="Verdana" size="1">Unpaid Hours</font></th>
                <th><font face="Verdana" size="1">Remarks</font></th>
                <th><font face="Verdana" size="1">Action</font></th>
            </tr>
        </thead>
        <tbody id="childemp">
            <?php
            $project1totalhrs = 0;
            $project2totalhrs = 0;
            $project3totalhrs = 0;
            $project4totalhrs = 0;
            $project5totalhrs = 0;
            $totalprojecthrs = 0;
            $totalunpaidhrs = 0;
            $totaladditionalhrs = 0;
            $totalMinutes = 0; 
            $totalMinutes1 = 0; 
            $totalMinutes2 = 0; 
            $totalMinutes3 = 0; 
            $totalMinutes4 = 0; 
            $totalMinutes5 = 0; 
            $totalMinutestrn = 0; 
            while ($selectHrsRows = mysqli_fetch_array($selecthrsResult)) {
                $publicDate = date('Ymd', strtotime($selectHrsRows['empdate']));
                $publicQuery = "SELECT OTDate from otdate where OTDate=$publicDate";
                $publicResult = mysqli_query($conn, $publicQuery);
                $isSunday = date("w", strtotime($selectHrsRows['empdate'])) == 0;
                if ($isSunday || $publicRaw > 0) {
                    $color = "background-color:red";
                } else {
                    $color = "";
                }
                $fetchappQuery = "SELECT * from projectapproval where empdate='$selectHrsRows[3]' AND parent_id=$selectHrsRows[1] AND child_id=$selectHrsRows[4]";
//            echo "<br>";
                $fetchappResult = mysqli_query($conn, $fetchappQuery);
                $fetchAppRow = mysqli_fetch_all($fetchappResult);
//            echo "<pre>";print_R($fetchAppRow);
                ?>
                <tr>
                    <td style="<?php echo $color; ?>"><font face="Verdana" size="1"><?php echo $selectHrsRows['empdate']; ?></font></td>
                    <td><font face="Verdana" size="1"><?php echo $selectHrsRows['parentempcode']; ?></font></td>
                    <td><font face="Verdana" size="1"><?php echo $selectHrsRows['parentempname']; ?></font></td>
                    <td><font face="Verdana" size="1"><?php echo $selectHrsRows['empcode']; ?></font></td>
                    <td><font face="Verdana" size="1"><?php echo $selectHrsRows['empname']; ?></font></td>
                    <td><font face="Verdana" size="1"><?php echo date('H:i:s', strtotime($selectHrsRows['entrytime'])); ?></font></td>
                    <td><font face="Verdana" size="1"><?php echo date('H:i:s', strtotime($selectHrsRows['exittime'])); ?></font></td>
                    <td><font face="Verdana" size="1"><?php echo $selectHrsRows['totalhrwork']; ?></font></td>
                    <td><font face="Verdana" size="1">
                        <?php
                        if ($fetchAppRow[0][1] == $selectHrsRows[3] && $fetchAppRow[0][2] == $selectHrsRows[1] && $fetchAppRow[0][3] == $selectHrsRows[4] && $fetchAppRow[0][5] == $selectHrsRows[9]) {
                            if ($fetchAppRow[0][4] == '' && $fetchAppRow[0][15] != '') {
                                echo $fetchAppRow[0][15];
                            } else {
                                echo $fetchAppRow[0][4];
                            }
                        } else {
                            if ($selectHrsRows[1] == $selectHrsRows[4] && $designationRow['F8'] == 'SV') {
                                $disabled = 'disabled';
                            } else {
                                $disabled = '';
                            }
                            ?>
                            <input type="checkbox" class="project-checkbox" name="project1[]" value="<?php echo $selectHrsRows[3] . ',' . $selectHrsRows[1] . ',' . $selectHrsRows[9] . ',' . $selectHrsRows[4]; ?>" <?php echo $disabled; ?>/>
                        <?php } ?>
                        </font></td>
                    <td><font face="Verdana" size="1"><?php echo $selectHrsRows['project1']; ?></font></td>
                    <td><font face="Verdana" size="1"><?php
                        echo $selectHrsRows['project1hrs'];
                        $project1totalhrs = $selectHrsRows['project1hrs'];
                        list($hours1, $minutes1) = explode(':', $project1totalhrs);
                        $totalMinutes1 += ($hours1 * 60) + $minutes1; 
                        ?></font></td>
                    <td><font face="Verdana" size="1">
                        <?php
                        //echo $fetchAppRow[0][3] .'=='. $selectHrsRows[4];
                        if ($fetchAppRow[0][1] == $selectHrsRows[3] && $fetchAppRow[0][2] == $selectHrsRows[1] && $fetchAppRow[0][3] == $selectHrsRows[4] && $fetchAppRow[0][7] == $selectHrsRows[10]) {
                            if ($fetchAppRow[0][6] == '' && $fetchAppRow[0][16] != '') {
                                echo $fetchAppRow[0][16];
                            } else {
                                echo $fetchAppRow[0][6];
                            }
                        } else {
                            if ($selectHrsRows[1] == $selectHrsRows[4] && $designationRow['F8'] == 'SV') {
                                $disabled = 'disabled';
                            } else {
                                $disabled = '';
                            }
                            ?>
                            <input type="checkbox" class="project-checkbox" name="project2[]" value="<?php echo $selectHrsRows[3] . ',' . $selectHrsRows[1] . ',' . $selectHrsRows[10] . ',' . $selectHrsRows[4]; ?>" <?php echo $disabled; ?>/>
    <?php } ?>          
                        </font></td>
                    <td><font face="Verdana" size="1"><?php echo $selectHrsRows['project2']; ?></font></td>
                    <td><font face="Verdana" size="1"><?php
                        echo $selectHrsRows['project2hrs'];
                        $project2totalhrs = $selectHrsRows['project2hrs'];
                        list($hours2, $minutes2) = explode(':', $project2totalhrs);
                        $totalMinutes2 += ($hours2 * 60) + $minutes2;
                        ?></font></td>
                    <td>
                        <font face="Verdana" size="1">
                        <?php
                        //echo $fetchAppRow[0][9] .'=='. $selectHrsRows[11];
                        if ($fetchAppRow[0][1] == $selectHrsRows[3] && $fetchAppRow[0][2] == $selectHrsRows[1] && $fetchAppRow[0][3] == $selectHrsRows[4] && $fetchAppRow[0][9] == $selectHrsRows[11]) {
                            if ($fetchAppRow[0][8] == '' && $fetchAppRow[0][17] != '') {
                                echo $fetchAppRow[0][17];
                            } else {
                                echo $fetchAppRow[0][8];
                            }
                        } else {
                            if ($selectHrsRows[1] == $selectHrsRows[4] && $designationRow['F8'] == 'SV') {
                                $disabled = 'disabled';
                            } else {
                                $disabled = '';
                            }
                            ?>
                            <input type="checkbox" class="project-checkbox" name="project3[]" value="<?php echo $selectHrsRows[3] . ',' . $selectHrsRows[1] . ',' . $selectHrsRows[11] . ',' . $selectHrsRows[4]; ?>" <?php echo $disabled; ?>/>
    <?php } ?>
                        </font></td>
                    <td><font face="Verdana" size="1"><?php echo $selectHrsRows['project3']; ?></font></td>
                    <td><font face="Verdana" size="1"><?php
                    echo $selectHrsRows['project3hrs'];
                    $project3totalhrs = $selectHrsRows['project3hrs'];
                    list($hours3, $minutes3) = explode(':', $project3totalhrs);
                    $totalMinutes3 += ($hours3 * 60) + $minutes3;
    ?></font></td>
                    <td>
                        <font face="Verdana" size="1">
                        <?php
                        if ($fetchAppRow[0][1] == $selectHrsRows[3] && $fetchAppRow[0][2] == $selectHrsRows[1] && $fetchAppRow[0][3] == $selectHrsRows[4] && $fetchAppRow[0][11] == $selectHrsRows[12]) {
                            if ($fetchAppRow[0][10] == '' && $fetchAppRow[0][18] != '') {
                                echo $fetchAppRow[0][18];
                            } else {
                                echo $fetchAppRow[0][10];
                            }
                        } else {
                            if ($selectHrsRows[1] == $selectHrsRows[4] && $designationRow['F8'] == 'SV') {
                                $disabled = 'disabled';
                            } else {
                                $disabled = '';
                            }
                            ?>
                            <input type="checkbox" class="project-checkbox" name="project4[]" value="<?php echo $selectHrsRows[3] . ',' . $selectHrsRows[1] . ',' . $selectHrsRows[12] . ',' . $selectHrsRows[4]; ?>" <?php echo $disabled; ?>/>
                        <?php } ?>
                        </font></td>
                    <td><font face="Verdana" size="1"><?php echo $selectHrsRows['project4']; ?></font></td>
                    <td><font face="Verdana" size="1"><?php
                    echo $selectHrsRows['project4hrs'];
                    $project4totalhrs = $selectHrsRows['project4hrs'];
                    list($hours4, $minutes4) = explode(':', $project4totalhrs);
                    $totalMinutes4 += ($hours4 * 60) + $minutes4;
                        ?></font></td>
                    <td>
                        <font face="Verdana" size="1">
                        <?php
                        if ($fetchAppRow[0][1] == $selectHrsRows[3] && $fetchAppRow[0][2] == $selectHrsRows[1] && $fetchAppRow[0][3] == $selectHrsRows[4] && $fetchAppRow[0][13] == $selectHrsRows[13]) {
                            if ($fetchAppRow[0][12] == '' && $fetchAppRow[0][19] != '') {
                                echo $fetchAppRow[0][19];
                            } else {
                                echo $fetchAppRow[0][12];
                            }
                        } else {
                            if ($selectHrsRows[1] == $selectHrsRows[4] && $designationRow['F8'] == 'SV') {
                                $disabled = 'disabled';
                            } else {
                                $disabled = '';
                            }
                            ?>
                            <input type="checkbox" class="project-checkbox" name="project5[]" value="<?php echo $selectHrsRows[3] . ',' . $selectHrsRows[1] . ',' . $selectHrsRows[13] . ',' . $selectHrsRows[4]; ?>" <?php echo $disabled; ?>/>
                        <?php } ?>
                        </font></td>
                    <td><font face="Verdana" size="1"><?php echo $selectHrsRows['project5']; ?></font></td>
                    <td><font face="Verdana" size="1"><?php
                        echo $selectHrsRows['project5hrs'];
                        $project5totalhrs = $selectHrsRows['project5hrs'];
                        list($hours5, $minutes5) = explode(':', $project5totalhrs);
                        $totalMinutes5 += ($hours5 * 60) + $minutes5;
                        ?></font></td>
                    <td><font face="Verdana" size="1"><?php
                    echo $selectHrsRows['totalprjhrs'];
                    $totalprojecthrs = $selectHrsRows['totalprjhrs'];
                    list($hours, $minutes) = explode(':', $totalprojecthrs);
                    $totalMinutes += ($hours * 60) + $minutes;
                        ?></font></td>
                    <td><font face="Verdana" size="1"><?php
                    echo $selectHrsRows['transithrs'];
                    $totalunpaidhrs = $selectHrsRows['transithrs'];
                    list($hourstrn, $minutestrn) = explode(':', $totalunpaidhrs);
                    $totalMinutestrn += ($hourstrn * 60) + $minutestrn;
                    ?></font></td>
                    <td><font face="Verdana" size="1"><?php echo $selectHrsRows['remark']; ?></font></td>
                    <?php
                    if ($designationRow['Designation'] == 'FC' || $designationRow['F8'] == 'Admin') {
//                        if ($designationRow['F8'] == 'Admin') {
                        $currentDay = date('j');
                        if ($selectHrsRows[31] == 0) {
                            $isDisabledClass = 'disabledButton';
//                                if($currentDay == '11'){
//                                    $isDisabledClass = 'disabledButton';
//                                }
                        } else {
                            $isDisabledClass = '';
                        }
//                        }
                        ?>
                        <td><font face="Verdana" size="1"><a href="ProjectHoursAllocationUpdate.php?id=<?php echo $selectHrsRows[0]; ?>" class="btn btn-primary editLink <?php echo $isDisabledClass; ?>" style="text-decoration: none;" >Edit</a></font></td>
    <?php } else { ?>
                        <td></td>
    <?php } ?>
                </tr>
<?php }
            // Project 1
            $totalHours1 = floor($totalMinutes1 / 60);  
            $remainingMinutes1 = $totalMinutes1 % 60;  
            $prj1Hours = sprintf('%d:%02d', $totalHours1, $remainingMinutes1); 
            
            // project 2
            $totalHours2 = floor($totalMinutes2 / 60);  
            $remainingMinutes2 = $totalMinutes2 % 60;  
            $prj2Hours = sprintf('%d:%02d', $totalHours2, $remainingMinutes2); 
            
            // project 3
            $totalHours3 = floor($totalMinutes3 / 60);  
            $remainingMinutes3 = $totalMinutes3 % 60;  
            $prj3Hours = sprintf('%d:%02d', $totalHours3, $remainingMinutes3); 
            
            // project 4
            $totalHours4 = floor($totalMinutes4 / 60);  
            $remainingMinutes4 = $totalMinutes4 % 60;  
            $prj4Hours = sprintf('%d:%02d', $totalHours4, $remainingMinutes4); 
            
            // project 5
            $totalHours5 = floor($totalMinutes5 / 60);  
            $remainingMinutes5 = $totalMinutes5 % 60;  
            $prj5Hours = sprintf('%d:%02d', $totalHours5, $remainingMinutes5); 
            
            // Total Project Hours
            $totalHours = floor($totalMinutes / 60);  
            $remainingMinutes = $totalMinutes % 60;  
            $prjTotalHours = sprintf('%d:%02d', $totalHours, $remainingMinutes); 
            
            // Transit Hours
            $totalHourstrn = floor($totalMinutestrn / 60);  
            $remainingMinutestrn = $totalMinutestrn % 60;  
            $prjTrnHours = sprintf('%d:%02d', $totalHourstrn, $remainingMinutestrn); 
?>
            <tr><th colspan="10">TOTAL OF PROJECT HOURS AND AMOUNT</th><th><?php echo $prj1Hours; ?></th><th colspan="2"></th><th><?php echo $prj2Hours; ?></th><th colspan="2"></th><th><?php echo $prj3Hours; ?></th><th colspan="2"></th><th><?php echo $prj4Hours; ?></th><th colspan="2"></th><th><?php echo $prj5Hours; ?></th><th><?php echo $prjTotalHours; ?></th><th><?php echo $prjTrnHours; ?></th><th colspan="2"></th></tr>
        </tbody>
    </table>
    <?php if ($designationRow['F8'] != 'SV') { ?>
    <input type="submit" name="submit" value="Approve" />
    <?php } ?>
</form>
<?php if($selecthrsResult->num_rows > 0){ ?>
<button id="exportToExcelBtn" class="btn btn-primary">Export to Excel</button>
<?php } ?>
<div id="loader">
    <img src="img/loader.gif" >
</div>
<script src="resource/js/jquery.min.js"></script>
<script>
    $('#exportToExcelBtn').show();
    $(document).ready(function () {
        $('#loader').hide();
//        $('#exportToExcelBtn').hide();
    });
    $("#searchfilter").click(function () {
        var fromDate = $("#frmdate").val();
        var toDate = $("#todate").val();
        $('#loader').show();    
        updateURL(fromDate, toDate);
    });
    setTimeout(function () {
        document.getElementById("message").style.display = "none";
    }, 5000);
</script>
<script src="resource/js/footerscript.js"></script>
<?php
print "</center>";
