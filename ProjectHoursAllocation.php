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
    header("Location: " . $config["REDIRECT"] . "?url=ProjectHoursAllocation.php&message=Session Expired or Security Policy Violated");
}
//$conn = openConnection();
$conn = new mysqli("localhost", "root", "namaste", "access_lfz");

// Check connection
if ($conn->connect_errno) {
    echo "Failed to connect to MySQL: " . $conn->connect_error;
    exit();
}

$projectQuery = "select Name from projectmaster";
$prjResult = mysqli_query($conn, $projectQuery);
$prjData = mysqli_fetch_all($prjResult);

//$hireQuery = "select r.parent_id,r.child_id,t.name from reportinghirekey r LEFT JOIN tuser t ON t.id=r.child_id";
//$hireResult = mysqli_query($conn, $hireQuery);
//$hireData = mysqli_fetch_all($hireResult);
//echo "<pre>";print_R($hireData);

$designationQuery = "SELECT * FROM tuser where name='$username'";
$designationResult = mysqli_query($conn, $designationQuery);
$designationRow = mysqli_fetch_assoc($designationResult);
$dept = $designationRow['dept'];
$parentQuery = "select DISTINCT r.parent_id, t.name from reportinghirekey r LEFT JOIN tuser t ON t.id=r.parent_id";
$parentResult = mysqli_query($conn, $parentQuery);
$parentData = mysqli_fetch_all($parentResult);

//$userQuery = "Select id,name from tuser where remark = 'SENIOR STAFF'";
if($designationRow['F8'] == 'Admin'){
    $userQuery = "SELECT id,name FROM tuser where F8 = 'SV'";
}else{
    $userQuery = "SELECT id,name FROM tuser where F8 = 'SV' AND dept='$dept'";
}
$userResult = mysqli_query($conn, $userQuery);
$userData = mysqli_fetch_all($userResult);

print "<center>";
displayHeader($prints, false, false);
displayLinks($current_module, $userlevel);

if ($_GET['prjstatus'] == 1) {
    $message1 = "Data Inserted Successfully";
}
if ($_GET['prjstatus'] == 2 && $_GET['prjstatus'] != '') {
    $message2 = "There is an error to insert data";
}
if ($_GET['prjstatus'] == 3) {
    $message3 = "Data Updated Successfully";
}
if($_GET['prjmessage'] == 4){
    $message4 = "Total Project Hours Greater Than Total Work Hours. So You Cannot Submit. Kindly Check.";
}
?>
<style>
    .form-controls{
        display: block;
        /*width: 100%;*/
        width: auto;
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
    #loader {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        width: 50%;
        transform: translate(-50%, -50%);
        z-index: 9999;
    }
    .dropdown {
        width: auto; 
    }
</style>
<?php if ($_GET['prjstatus']) { ?>
    <div class="alert alert-primary" role="alert" id="message">
        <?php echo $message1 . '<br>' . $message2 . '<br>' . $message3 . '<br>' .$message4; ?>
    </div>
<?php } ?>
<?php if ($_GET['prjmessage']) { ?>
    <div class="alert alert-primary" role="alert" id="message">
        <?php echo $message4; ?>
    </div>
<?php } ?>
<form method="post" action="ProjectHoursAllocationScript.php">
    <table border="1" cellpadding="1" bordercolor="#C0C0C0" cellspacing="-1" >
        <thead>
            <tr>
                <th align="right" width="10%"><font face="Verdana" size="1">Start Date</font></th>
                <td width="10%"><font face="Verdana" size="1"><input type="date" id="fromDate" class="form-controls" name="fromDate" value="<?php echo date('Y-m-d'); ?>"></font></td>
                <th align="right" width="15%"><font size="2" face="Verdana">Login User Name: </font></td>
                <td width="65%"><font size="2" face="Verdana"><?php echo $username; ?></font></td>
            </tr>
            <tr>
                <th align="right" width="10%"><font face="Verdana" size="1">End Date</font></th>
                <td width="10%"><font face="Verdana" size="1"><input type="date" id="toDate" class="form-controls" name="toDate" value="<?php echo date('Y-m-d'); ?>"></font></td>
                <?php if ($designationRow['F8'] == 'FC' || $designationRow['F8'] == 'Admin' || $designationRow['F8'] == 'PM') { ?>
                    <td width="15%">Show Supervisor Records: <input type="checkbox" name="parent"  id="parent"/></td>
                <?php } else { ?>
                    <td width="15%"></td>
                <?php } ?>
                <td width="65%"></td>
            </tr>
            <tr>
                <th align="right" width="10%"><font face="Verdana" size="1">Supervisor Name</font></th>
                <?php
                if ($designationRow['F8'] == 'SV') {
                    foreach ($userData as $userAll) {
                        if ($userAll[1] == $_SESSION['virdiusername']) {
                            $id = $userAll[0];
                            $name = $userAll[1];
                        }
                    }
                    ?>
                    <td width="15%"><font face="Verdana" size="1">
                        <input type="text" name="<?php echo $name; ?>" id="supervisordata" value="<?php echo $id . ': ' . $name; ?>" class="form-controls"/>
                        </font>
                    </td>
                <?php } else { ?>
                    <td width="15%"><font face="Verdana" size="1">
                        <select name="hireauth" required="required" id="hireauth" class="form-controls">
                            <!--<option value="">---</option>-->
                            <?php foreach ($userData as $userAll) { ?>
                                <option value="<?php echo $userAll[0]; ?>" name="<?php echo $userAll[1]; ?>"><?php echo $userAll[0] . ': ' . $userAll[1]; ?></option>
                            <?php } ?>
                        </select>
                        </font>
                    </td>
                <?php } ?>
                <td width="15%"><input type="button" id="fetchDataBtn" class="form-control" value="Search"></td>
                <td width="65%"><font size="2" face="Verdana"></font></td>
            </tr>
        </thead>
    </table>

    <table border="1" cellpadding="1" bordercolor="#C0C0C0" cellspacing="-1" >
        <thead>
            <tr>
                <!--<th><font face="Verdana" size="2">ID</font></th>-->
                <!--<th><font face="Verdana" size="2">Employee ID</font></th>-->
                <th width="7%"><font face="Verdana" size="1">Date</font></th>
                <th width="7%"><font face="Verdana" size="1">Employee Code</font></th>
                <th width="10%"><font face="Verdana" size="1">Employee Name</font></th>
    <!--            <th><font face="Verdana" size="1">Terminal IN</font></th>
                <th><font face="Verdana" size="1">Terminal Out</font></th>-->
                <th><font face="Verdana" size="1">Entry Time</font></th>
                <th><font face="Verdana" size="1">Out Time</font></th>
                <th><font face="Verdana" size="1">Total Hours Work</font></th>
                <th><font face="Verdana" size="1">
                    <label for="prj1">Project/Location 1</label>
<!--                    <select class="dropdown form-controls" name="prj1" required="required">
                        <option>--Select--</option>
                    <?php foreach ($prjData as $projects) { ?>
                                    <option value="<?php echo $projects[0]; ?>"><?php echo $projects[0]; ?></option>
                    <?php } ?>
                    </select>-->
                    </font>
                </th>
                <th><font face="Verdana" size="1">Project/Location 1 Hours</th>
                <th><font face="Verdana" size="1">
                    <label for="prj1">Project/Location 2</label>
<!--                    <select class="dropdown form-controls" name="prj2" required="required">
                        <option>--Select--</option>
                    <?php foreach ($prjData as $projects) { ?>
                                    <option value="<?php echo $projects[0]; ?>"><?php echo $projects[0]; ?></option>
                    <?php } ?>
                    </select>-->
                    </font>
                </th>
                <th><font face="Verdana" size="1">Project/Location 2 Hours</th></th>
                <th><font face="Verdana" size="1">
                    <label for="prj1">Project/Location 3</label>
<!--                    <select class="dropdown form-controls" name="prj3" required="required">
                        <option>--Select--</option>
                    <?php foreach ($prjData as $projects) { ?>
                                    <option value="<?php echo $projects[0]; ?>"><?php echo $projects[0]; ?></option>
                    <?php } ?>
                    </select>-->
                    </font>
                </th>
                <th><font face="Verdana" size="1">Project/Location 3 Hours</th></th>
                <th><font face="Verdana" size="1">
                    <label for="prj1">Project/Location 4</label>
<!--                    <select class="dropdown form-controls" name="prj4">
                        <option>--Select--</option>
                    <?php foreach ($prjData as $projects) { ?>
                                    <option value="<?php echo $projects[0]; ?>"><?php echo $projects[0]; ?></option>
                    <?php } ?>
                    </select>-->
                    </font>
                </th>
                <th><font face="Verdana" size="1">Project/Location 4 Hours</th></th>
                <th><font face="Verdana" size="1">
                    <label for="prj1">Project/Location 5</label>
<!--                    <select class="dropdown form-controls" name="prj5">
                        <option>--Select--</option>
                    <?php foreach ($prjData as $projects) { ?>
                                    <option value="<?php echo $projects[0]; ?>"><?php echo $projects[0]; ?></option>
                    <?php } ?>
                    </select>-->
                    </font>
                </th>
                <th><font face="Verdana" size="1">Project/Location 5 Hours</th></th>
                <th><font face="Verdana" size="1">Total Project/Location Hours</font></th>
                <th><font face="Verdana" size="1">Unpaid Hours</font></th>
                <th><font face="Verdana" size="1">Remarks</font></th>
            </tr>
        </thead>
        <tbody id="childemp"></tbody>
    </table><br>
    <div id="loader">
        <img src="img/loader.gif" >
    </div>
    <input type="submit" name="submit" value="Submit Record" class="btn btn-primary"/>
</form>
<?php
print "<center>";
?>
<script src="resource/js/jquery.min.js"></script>
<script>
    $(document).ready(function () {
        $('#loader').hide();
        var defaultSelectedValue = $('#hireauth').val();
        $.ajax({
            url: 'FetchChildEmpValue.php',
            method: 'POST',
            data: {selectedValue: defaultSelectedValue},
            success: function (response) {
                $('#childemp').html(response);
            }
        });
        $('#hireauth').on('change', function () {
            var selectedValue = $(this).val();
            $.ajax({
                url: 'FetchChildEmpValue.php',
                method: 'POST',
                data: {
                    selectedValue: selectedValue},
                success: function (response) {
                    $('#childemp').html(response);
                }
            });
        });
        $("#fetchDataBtn").click(function () {
            $('#loader').show();
            var selectedValue = $("#hireauth").val();
            var supervisorValue = $("#supervisordata").val();
            var fromDate = $("#fromDate").val();
            var toDate = $("#toDate").val();
            var parent = $("#parent").checked;
            var checkboxes = document.getElementsByName("parent");
            const selectedCheckboxes = Array.from(checkboxes)
                    .filter(checkbox => checkbox.checked)
                    .map(checkbox => checkbox.value);

            $.ajax({
                type: "POST",
                url: "FetchChildEmpValue.php",
                data: {
                    fromDate: fromDate,
                    toDate: toDate,
                    flag: selectedCheckboxes,
                    selectedValue: selectedValue,
                    supervisorValue: supervisorValue
                },
                success: function (response) {
                    $('#loader').hide();
                    $("#childemp").html(response);
                }
            });
        });
    });
<?php
$currentDate = date('Y-m-d');

if ($designationRow['F8'] == 'SV') {
    $twoDaysAgo = date('Y-m-d', strtotime('-2 days'));
}
if ($designationRow['F8'] == 'PM') {
//    $twoDaysAgo = date('Y-m-d', strtotime('-7 days'));
    $twoDaysAgo = date('Y-m-d', strtotime('-365 days'));
}
if ($designationRow['F8'] == 'HOD') {
    $twoDaysAgo = date('Y-m-d', strtotime('-30 days'));
}
?>

    // Set the minimum and maximum date values using JavaScript
    var currentDate = "<?php echo $currentDate; ?>";
    var twoDaysAgo = "<?php echo $twoDaysAgo; ?>";

    // Get the date input element by ID
    var datefromInput = document.getElementById('fromDate');
    var datetoInput = document.getElementById('toDate');

    // Set the min and max attributes for the date input field
    datefromInput.min = twoDaysAgo;
    datefromInput.max = '';
    datetoInput.min = twoDaysAgo;
    datetoInput.max = '';
    setTimeout(function () {
        document.getElementById("message").style.display = "none";
    }, 5000);
</script>
