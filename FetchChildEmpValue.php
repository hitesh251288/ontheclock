<?php
ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
set_time_limit(0);
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
$conn = new mysqli("localhost", "root", "namaste", "access_lfz");

// Check connection
if ($conn->connect_errno) {
    echo "Failed to connect to MySQL: " . $conn->connect_error;
    exit();
}

function separateTime($inputTime) {  
    // Split the input string by the colon  
    $parts = explode(':', $inputTime);  
    
    // Validate the input format  
    if (count($parts) !== 2) {  
        return ['hours' => 0, 'minutes' => 0]; // Invalid format  
    }  

    // Parse hours and minutes  
    $hours = (int)$parts[0];  
    $minutes = (int)$parts[1];  

    // Ensure minutes are within the 0-59 range  
    if ($minutes < 0 || $minutes >= 60) {  
        return ['hours' => 0, 'minutes' => 0]; // Invalid minutes  
    }  

    return ['hours' => $hours, 'minutes' => $minutes];  
}

$designationQuery = "SELECT * FROM tuser where name='$username'";
$designationResult = mysqli_query($conn, $designationQuery);
$designationRow = mysqli_fetch_assoc($designationResult);

if (isset($_POST['selectedValue']) && !isset($_POST['fromDate']) && !isset($_POST['toDate'])) {
    $curdate = date('Ymd');
    $selectedValue = $_POST['selectedValue'];
    $chilEmpQuery = "select r.child_id, t.name, d.Entry, d.Exit,d.TDate from reportinghirekey r "
            . "LEFT JOIN tuser t ON t.id=r.child_id LEFT JOIN daymaster d ON d.e_id=t.id where TDate='$curdate' AND r.parent_id =" . $selectedValue;

    $childEmpResult = mysqli_query($conn, $chilEmpQuery);
    $childEmpRow = mysqli_fetch_all($childEmpResult);
    $i = 1;
    $h = 1;
    foreach ($childEmpRow as $childEmpAll) {
        $fromTime = $childEmpAll[2];
        $toTime = $childEmpAll[3];
//        $totalHours = date('H:i',strtotime($childEmpAll[3])) - date('H:i',strtotime($childEmpAll[2]));
        $totalHours = strtotime($childEmpAll[3]) - strtotime($childEmpAll[2]);
        $hours = floor($totalHours / 3600);
        $minutes = ($totalHours % 3600) / 60;
        $initialTimeDiff = sprintf('%02d:%02d', $hours, $minutes);
        $prj1hrs = separateTime($childEmpAll[10]);
        $prj2hrs = separateTime($childEmpAll[11]);
        $prj3hrs = separateTime($childEmpAll[12]);
        $prj4hrs = separateTime($childEmpAll[13]);
        $prj5hrs = separateTime($childEmpAll[14]);
        ?>
        <tr>
            <td><font face="Verdana" size="1"><input type="text" name="dateemp[]" value="<?php echo date('Y/m/d', strtotime($childEmpAll[4])); ?>" class="form-controls" readonly="readonly"/></font></td>
            <td><font face="Verdana" size="1"><input type="number" name="empcode[]" value="<?php echo $childEmpAll[0]; ?>" class="form-controls" readonly="readonly"/></font></td>
            <td><font face="Verdana" size="1"><input type="text" name="empname[]" value="<?php echo $childEmpAll[1]; ?>" class="form-controls" readonly="readonly"/></font></td>
            <td><font face="Verdana" size="1"><input type="text" name="entrytime[]" value="<?php echo displayVirdiTime($childEmpAll[2]); ?>" class="form-controls entrytime"/></font></td>
            <td><font face="Verdana" size="1"><input type="text" name="endtime[]" value="<?php echo displayVirdiTime($childEmpAll[3]); ?>" class="form-controls endtime"/></font></td>
            <td><font face="Verdana" size="1"><input type="text" name="totalhrwork[]" value="<?php echo $initialTimeDiff; ?>" readonly="readonly" class="form-controls totalhrwork"/></font></td>
            <td><font face="Verdana" size="1"><input type="text" name="prj1hrs[]"  value="" class="number-field form-controls  prj-hrs"/></font></td>
            <td><font face="Verdana" size="1"><input type="text" name="prj2hrs[]"  value="" class="number-field form-controls  prj-hrs"/></font></td>
            <td><font face="Verdana" size="1"><input type="text" name="prj3hrs[]"  value="" class="number-field form-controls  prj-hrs"/></font></td>
            <td><font face="Verdana" size="1"><input type="text" name="prj4hrs[]"  value="" class="number-field form-controls  prj-hrs"/></font></td>
            <td><font face="Verdana" size="1"><input type="text" name="prj5hrs[]"  value="" class="number-field form-controls  prj-hrs"/></font></td>
            <td><font face="Verdana" size="1"><input type="text" name="totalhrs[]" value="" class="form-controls total-hours"/></font></td>
            <td><font face="Verdana" size="1"><input type="text" name="transithrs[]" value="" class="form-controls"/></font></td>
            <td><font face="Verdana" size="1"><input type="text" name="remark[]" value="" class="form-controls"/></font></td>
        </tr>
        <?php
    }
//    $conn->close();
}  

if (isset($_POST['fromDate']) && isset($_POST['toDate']) && !isset($_POST['flag']) || isset($_POST['supervisorValue'])) {
    $fromDate = date('Ymd', strtotime($_POST["fromDate"]));
    $toDate = date('Ymd', strtotime($_POST["toDate"]));

    if($_POST['supervisorValue']){
        $supervisorValue = explode(': ', $_POST['supervisorValue']);
        $selectedValue = $supervisorValue[0];
    }else{
        $selectedValue = $_POST['selectedValue'];
    }
    // AND p.empdate=d.TDate
//    $chilEmpQuery = "select r.child_id, t.name, d.Entry, d.Exit,d.TDate from reportinghirekey r "
//            . "LEFT JOIN tuser t ON t.id=r.child_id LEFT JOIN daymaster d ON d.e_id=t.id where d.TDate >= '$fromDate' AND d.TDate <= '$toDate' AND r.parent_id =" . $selectedValue;
    $chilEmpQuery = "select DISTINCT r.child_id, o.fullname, d.Entry, d.Exit,d.TDate, p.project1,p.project2,p.project3,p.project4,p.project5,p.project1hrs,p.project2hrs,p.project3hrs,"
            . "p.project4hrs,p.project5hrs,p.totalprjhrs,p.transithrs,p.remark, a.supervisor_id, a.project_id,a.manager_id from reportinghirekey r "
            . "LEFT JOIN daymaster d ON d.e_id=r.child_id LEFT JOIN onboardrequest o ON o.employee_id=r.child_id LEFT JOIN "
            . "projecthrsallocation p ON p.empcode=o.employee_id AND p.empdate=d.TDate "
            . "LEFT JOIN assignproject a ON a.supervisor_id=r.parent_id "
            . "where d.TDate >= '$fromDate' AND d.TDate <= '$toDate' AND r.parent_id =" . $selectedValue;
    $childEmpResult = mysqli_query($conn, $chilEmpQuery);
    $childEmpRow = mysqli_fetch_all($childEmpResult);

    $j = 1;
    $k = 1;
    foreach ($childEmpRow as $childEmpAll) {
        $fromTime = $childEmpAll[2];
        $toTime = $childEmpAll[3];
//        $prjfetchQuery = "select Name from projectmaster where ProjectID IN(" . $childEmpAll[19] . ")";
        $prjfetchQuery = "select Name from projectmaster";
        $prjfetchResult = mysqli_query($conn, $prjfetchQuery);
        $prjfetchRow = mysqli_fetch_all($prjfetchResult);

//        echo "<pre>";print_R($childEmpAll);
//        $totalHours = date('H:i',strtotime($childEmpAll[3])) - date('H:i',strtotime($childEmpAll[2]));
        $totalHours = strtotime($childEmpAll[3]) - strtotime($childEmpAll[2]);
        // Calculate hours and minutes for initial display
        $hours = floor($totalHours / 3600);
        $minutes = ($totalHours % 3600) / 60;
        $initialTimeDiff = sprintf('%02d hours %02d minutes', $hours, $minutes);
        $prj1hrs = separateTime($childEmpAll[10]);
        $prj2hrs = separateTime($childEmpAll[11]);
        $prj3hrs = separateTime($childEmpAll[12]);
        $prj4hrs = separateTime($childEmpAll[13]);
        $prj5hrs = separateTime($childEmpAll[14]);
//        echo "{$time['hours']} hours {$time['minutes']} minutes";
        ?>
        <tr><input type="hidden" name="designation" value="<?php echo $designationRow['F8']; ?>">
            <td><font face="Verdana" size="1"><input type="text" class="dateemp form-controls" name="dateemp[]" value="<?php echo date('Y/m/d', strtotime($childEmpAll[4])); ?>" readonly="readonly"/></font></td>
            <td><font face="Verdana" size="1"><input type="number" name="empcode[]" value="<?php echo $childEmpAll[0]; ?>" class="form-controls" readonly="readonly"/></font></td>
            <td><font face="Verdana" size="1"><input type="text" name="empname[]" value="<?php echo $childEmpAll[1]; ?>" class="form-controls" readonly="readonly"/></font></td>
            <td><font face="Verdana" size="1"><input type="text" name="entrytime[]" value="<?php echo displayVirdiTime($childEmpAll[2]); ?>" class="form-controls entrytime"/></font></td>
            <td><font face="Verdana" size="1"><input type="text" name="endtime[]" value="<?php echo displayVirdiTime($childEmpAll[3]); ?>" class="form-controls endtime"/></font></td>
            <td><font face="Verdana" size="1"><input type="text" name="totalhrwork[]" value="<?php echo $initialTimeDiff ; ?>" class="form-controls totalhrwork" readonly="readonly"/></font></td>
            <td><font face="Verdana" size="1">
                <select class="dropdown form-controls" name="project1[]" data-employee-id="<?php echo $childEmpAll[0]; ?>" data-date="<?php echo $childEmpAll[4]; ?>">
                    <option value="">--Select--</option>
                    <?php foreach ($prjfetchRow as $prjfetchName) { ?>
                        <option value="<?php echo $prjfetchName[0]; ?>" <?php echo ($childEmpAll[5] == $prjfetchName[0]) ? 'selected' : ''; ?>><?php echo $prjfetchName[0]; ?></option>
                    <?php } ?>
                </select>
                </font>
            </td>
            <td><font face="Verdana" size="1"><input type="text" name="prj1hrs[]"  value="<?php echo !empty($childEmpAll[10]) ? "{$prj1hrs['hours']} hours {$prj1hrs['minutes']} minutes" : ''; ?>"  class="number-field form-controls prj-hrs"/></font></td>
            <td><font face="Verdana" size="1">
                <select class="dropdown form-controls" name="project2[]" data-employee-id="<?php echo $childEmpAll[0]; ?>" data-date="<?php echo $childEmpAll[4]; ?>">
                    <option value="">--Select--</option>
                    <?php foreach ($prjfetchRow as $prjfetchName) { ?>
                        <option value="<?php echo $prjfetchName[0]; ?>" <?php echo ($childEmpAll[6] == $prjfetchName[0]) ? 'selected' : ''; ?>><?php echo $prjfetchName[0]; ?></option>
                    <?php } ?>
                </select>
                </font>
            </td>
            <td><font face="Verdana" size="1"><input type="text" name="prj2hrs[]"  value="<?php echo !empty($childEmpAll[11]) ? "{$prj2hrs['hours']} hours {$prj2hrs['minutes']} minutes" : ''; ?>" class="number-field form-controls prj-hrs"/></font></td>
            <td><font face="Verdana" size="1">
                <select class="dropdown form-controls" name="project3[]" data-employee-id="<?php echo $childEmpAll[0]; ?>" data-date="<?php echo $childEmpAll[4]; ?>">
                    <option value="">--Select--</option>
                    <?php foreach ($prjfetchRow as $prjfetchName) { ?>
                        <option value="<?php echo $prjfetchName[0]; ?>" <?php echo ($childEmpAll[7] == $prjfetchName[0]) ? 'selected' : ''; ?>><?php echo $prjfetchName[0]; ?></option>
                    <?php } ?>
                </select>
                </font>
            </td>
            <td><font face="Verdana" size="1"><input type="text" name="prj3hrs[]"  value="<?php echo !empty($childEmpAll[12]) ? "{$prj3hrs['hours']} hours {$prj3hrs['minutes']} minutes" : ''; ?>" class="number-field form-controls prj-hrs"/></font></td>
            <td><font face="Verdana" size="1">
                <select class="dropdown form-controls" name="project4[]" data-employee-id="<?php echo $childEmpAll[0]; ?>" data-date="<?php echo $childEmpAll[4]; ?>">
                    <option value="">--Select--</option>
                    <?php foreach ($prjfetchRow as $prjfetchName) { ?>
                        <option value="<?php echo $prjfetchName[0]; ?>" <?php echo ($childEmpAll[8] == $prjfetchName[0]) ? 'selected' : ''; ?>><?php echo $prjfetchName[0]; ?></option>
                    <?php } ?>
                </select>
                </font>
            </td>
            <td><font face="Verdana" size="1"><input type="text" name="prj4hrs[]"  value="<?php echo !empty($childEmpAll[13]) ? "{$prj4hrs['hours']} hours {$prj4hrs['minutes']} minutes" :''; ?>" class="number-field form-controls prj-hrs"/></font></td>
            <td><font face="Verdana" size="1">
                <select class="dropdown form-controls" name="project5[]" data-employee-id="<?php echo $childEmpAll[0]; ?>" data-date="<?php echo $childEmpAll[4]; ?>">
                    <option value="">--Select--</option>
                    <?php foreach ($prjfetchRow as $prjfetchName) { ?>
                        <option value="<?php echo $prjfetchName[0]; ?>" <?php echo ($childEmpAll[9] == $prjfetchName[0]) ? 'selected' : ''; ?>><?php echo $prjfetchName[0]; ?></option>
                    <?php } ?>
                </select>
                </font>
            </td>
            <td><font face="Verdana" size="1"><input type="text" name="prj5hrs[]"  value="<?php echo !empty($childEmpAll[14]) ? "{$prj5hrs['hours']} hours {$prj5hrs['minutes']} minutes" :''; ?>" class="number-field form-controls prj-hrs"/></font></td>
            <td><font face="Verdana" size="1"><input type="text" name="totalhrs[]" value="<?php echo $childEmpAll[15]; ?>" readonly="readonly" class="form-controls total-hours"/></font></td>
            <td><font face="Verdana" size="1"><input type="text" name="transithrs[]" value="<?php echo $childEmpAll[16]; ?>" readonly="readonly" class="form-controls"/></font></td>
            <?php if($designationRow['F8'] == 'HOD' || $designationRow['F8'] == 'PM' || $designationRow['F8'] == 'SV' || $designationRow['F8'] == 'Admin') {  ?>
            <td><font face="Verdana" size="1"><input type="text" name="remark[]" value="<?php echo $childEmpAll[17]; ?>" class="form-controls"/></font></td>
            <?php }else{ ?>
            <td></td>
            <?php } ?>
        </tr>
        <?php
    }
    $conn->close();
}

if (isset($_POST['flag'])) {
//    echo "<pre>";print_R($_POST);
    $fromDate = date('Ymd', strtotime($_POST["fromDate"]));
    $toDate = date('Ymd', strtotime($_POST["toDate"]));

    $selectedValue = $_POST['selectedValue'];
//    $chilEmpQuery = "select t.id, t.name, d.Entry, d.Exit,d.TDate from tuser t "
//            . "LEFT JOIN daymaster d ON t.id=d.e_id where d.TDate >= '$fromDate' AND d.TDate <= '$toDate' AND d.e_id =" . $selectedValue;
//    $chilEmpQuery = "select r.child_id, o.fullname, d.Entry, d.Exit,d.TDate, p.project1,p.project2,p.project3,p.project4,p.project5,p.project1hrs,p.project2hrs,p.project3hrs,"
//            . "p.project4hrs,p.project5hrs,p.totalprjhrs,p.transithrs,p.remark, a.supervisor_id, a.project_id,a.manager_id from reportinghirekey r "
//            . "LEFT JOIN daymaster d ON d.e_id=r.child_id LEFT JOIN onboardrequest o ON o.employee_id=r.child_id LEFT JOIN "
//            . "projecthrsallocation p ON p.empcode=o.employee_id AND p.empdate=d.TDate "
//            . "LEFT JOIN assignproject a ON a.supervisor_id=r.parent_id "
//            . "where d.TDate >= '$fromDate' AND d.TDate <= '$toDate' AND r.parent_id =" . $selectedValue;
    
//    $chilEmpQuery = "select r.parent_id, o.fullname, d.Entry, d.Exit,d.TDate, p.project1,p.project2,p.project3,p.project4,p.project5,p.project1hrs,p.project2hrs,p.project3hrs,"
//            . "p.project4hrs,p.project5hrs,p.totalprjhrs,p.transithrs,p.remark, a.supervisor_id, a.project_id,a.manager_id from onboardrequest o "
//            . "LEFT JOIN reportinghirekey r ON r.parent_id=o.employee_id "
//            . "LEFT JOIN daymaster d ON o.employee_id=d.e_id "
//            . "LEFT JOIN projecthrsallocation p ON p.empcode=o.employee_id AND p.empdate=d.TDate "
//            . "LEFT JOIN assignproject a ON a.supervisor_id=r.parent_id "
//            . "where d.TDate >= '$fromDate' AND d.TDate <= '$toDate' AND r.parent_id =" . $selectedValue . " group by TDate";
    $chilEmpQuery = "select d.e_id, t.name, d.Entry, d.Exit,d.TDate, p.project1,p.project2,p.project3,p.project4,p.project5,p.project1hrs,p.project2hrs,p.project3hrs,p.project4hrs,
                    p.project5hrs,p.totalprjhrs,p.transithrs,p.remark, a.supervisor_id, a.project_id,a.manager_id from onboardrequest o
                    LEFT JOIN daymaster d ON d.e_id=" . $selectedValue . "
                    LEFT JOIN tuser t ON t.id=d.e_id
                    LEFT JOIN assignproject a ON a.supervisor_id=d.e_id
                    LEFT JOIN projecthrsallocation p ON p.parentempcode=a.supervisor_id AND p.empdate=d.TDate
                    where d.TDate >= '$fromDate' AND d.TDate <= '$toDate' AND d.e_id=" . $selectedValue . " group by TDate";
    
    $childEmpResult = mysqli_query($conn, $chilEmpQuery);
    $childEmpRow = mysqli_fetch_all($childEmpResult);
    $j = 1;
    $k = 1;
    foreach ($childEmpRow as $childEmpAll) {
        $fromTime = $childEmpAll[2];
        $toTime = $childEmpAll[3];
//        $prjfetchQuery = "select Name from projectmaster where ProjectID IN(" . $childEmpAll[19] . ")";
        $prjfetchQuery = "select Name from projectmaster";
        $prjfetchResult = mysqli_query($conn, $prjfetchQuery);
        $prjfetchRow = mysqli_fetch_all($prjfetchResult);
//        echo "<pre>";print_R($prjfetchRow);
//        $totalHours = date('H:i',strtotime($childEmpAll[3])) - date('H:i',strtotime($childEmpAll[2]));
        $totalHours = strtotime($childEmpAll[3]) - strtotime($childEmpAll[2]);
        $hours = floor($totalHours / 3600);
        $minutes = ($totalHours % 3600) / 60;
        $initialTimeDiff = sprintf('%02d:%02d', $hours, $minutes);
        $prj1hrs = separateTime($childEmpAll[10]);
        $prj2hrs = separateTime($childEmpAll[11]);
        $prj3hrs = separateTime($childEmpAll[12]);
        $prj4hrs = separateTime($childEmpAll[13]);
        $prj5hrs = separateTime($childEmpAll[14]);
        ?>
        <tr>
            <td><font face="Verdana" size="1"><input type="text" class="dateemp form-controls" name="dateemp[]" value="<?php echo date('Y/m/d', strtotime($childEmpAll[4])); ?>" readonly="readonly"/></font></td>
            <td><font face="Verdana" size="1"><input type="number" name="empcode[]" value="<?php echo $childEmpAll[0]; ?>" class="form-controls" readonly="readonly"/></font></td>
            <td><font face="Verdana" size="1"><input type="text" name="empname[]" value="<?php echo $childEmpAll[1]; ?>" class="form-controls" readonly="readonly"/></font></td>
            <td><font face="Verdana" size="1"><input type="text" name="entrytime[]" value="<?php echo displayVirdiTime($childEmpAll[2]); ?>" class="form-controls entrytime" /></font></td>
            <td><font face="Verdana" size="1"><input type="text" name="endtime[]" value="<?php echo displayVirdiTime($childEmpAll[3]); ?>" class="form-controls endtime"/></font></td>
            <td><font face="Verdana" size="1"><input type="text" name="totalhrwork[]" value="<?php echo $initialTimeDiff; ?>" class="form-controls totalhrwork" readonly="readonly"/></font></td>
            <td><font face="Verdana" size="1">
                <select class="dropdown form-controls" name="project1[]" data-employee-id="<?php echo $childEmpAll[0]; ?>" data-date="<?php echo $childEmpAll[4]; ?>">
                    <option value="">--Select--</option>
                    <?php foreach ($prjfetchRow as $prjfetchName) { ?>
                        <option value="<?php echo $prjfetchName[0]; ?>" <?php echo ($childEmpAll[5] == $prjfetchName[0]) ? 'selected' : ''; ?>><?php echo $prjfetchName[0]; ?></option>
                    <?php } ?>
                </select>
                </font>
            </td>
            <td><font face="Verdana" size="1"><input type="text" name="prj1hrs[]"  value="<?php echo !empty($childEmpAll[10]) ? "{$prj1hrs['hours']} hours {$prj1hrs['minutes']} minutes" : ''; ?>"  class="number-field form-controls prj-hrs"/></font></td>
            <td><font face="Verdana" size="1">
                <select class="dropdown form-controls" name="project2[]" data-employee-id="<?php echo $childEmpAll[0]; ?>" data-date="<?php echo $childEmpAll[4]; ?>">
                    <option value="">--Select--</option>
                    <?php foreach ($prjfetchRow as $prjfetchName) { ?>
                        <option value="<?php echo $prjfetchName[0]; ?>" <?php echo ($childEmpAll[6] == $prjfetchName[0]) ? 'selected' : ''; ?>><?php echo $prjfetchName[0]; ?></option>
                    <?php } ?>
                </select>
                </font>
            </td>
            <td><font face="Verdana" size="1"><input type="text" name="prj2hrs[]"  value="<?php echo !empty($childEmpAll[11]) ? "{$prj2hrs['hours']} hours {$prj2hrs['minutes']} minutes" : ''; ?>" class="number-field form-controls prj-hrs"/></font></td>
            <td><font face="Verdana" size="1">
                <select class="dropdown form-controls" name="project3[]" data-employee-id="<?php echo $childEmpAll[0]; ?>" data-date="<?php echo $childEmpAll[4]; ?>">
                    <option value="">--Select--</option>
                    <?php foreach ($prjfetchRow as $prjfetchName) { ?>
                        <option value="<?php echo $prjfetchName[0]; ?>" <?php echo ($childEmpAll[7] == $prjfetchName[0]) ? 'selected' : ''; ?>><?php echo $prjfetchName[0]; ?></option>
                    <?php } ?>
                </select>
                </font>
            </td>
            <td><font face="Verdana" size="1"><input type="text" name="prj3hrs[]"  value="<?php echo !empty($childEmpAll[12]) ? "{$prj3hrs['hours']} hours {$prj3hrs['minutes']} minutes" : ''; ?>" class="number-field form-controls prj-hrs"/></font></td>
            <td><font face="Verdana" size="1">
                <select class="dropdown form-controls" name="project4[]" data-employee-id="<?php echo $childEmpAll[0]; ?>" data-date="<?php echo $childEmpAll[4]; ?>">
                    <option value="">--Select--</option>
                    <?php foreach ($prjfetchRow as $prjfetchName) { ?>
                        <option value="<?php echo $prjfetchName[0]; ?>" <?php echo ($childEmpAll[8] == $prjfetchName[0]) ? 'selected' : ''; ?>><?php echo $prjfetchName[0]; ?></option>
                    <?php } ?>
                </select>
                </font>
            </td>
            <td><font face="Verdana" size="1"><input type="text" name="prj4hrs[]"  value="<?php echo !empty($childEmpAll[13]) ? "{$prj4hrs['hours']} hours {$prj4hrs['minutes']} minutes" : ''; ?>" class="number-field form-controls prj-hrs"/></font></td>
            <td><font face="Verdana" size="1">
                <select class="dropdown form-controls" name="project5[]" data-employee-id="<?php echo $childEmpAll[0]; ?>" data-date="<?php echo $childEmpAll[4]; ?>">
                    <option value="">--Select--</option>
                    <?php foreach ($prjfetchRow as $prjfetchName) { ?>
                        <option value="<?php echo $prjfetchName[0]; ?>" <?php echo ($childEmpAll[9] == $prjfetchName[0]) ? 'selected' : ''; ?>><?php echo $prjfetchName[0]; ?></option>
                    <?php } ?>
                </select>
                </font>
            </td>
            <td><font face="Verdana" size="1"><input type="text" name="prj5hrs[]"  value="<?php echo !empty($childEmpAll[14]) ? "{$prj5hrs['hours']} hours {$prj5hrs['minutes']} minutes" : ''; ?>" class="number-field form-controls prj-hrs"/></font></td>
            <td><font face="Verdana" size="1"><input type="text" name="totalhrs[]" value="<?php echo $childEmpAll[15]; ?>" readonly="readonly" class="form-controls total-hours"/></font></td>
            <td><font face="Verdana" size="1"><input type="text" name="transithrs[]" value="<?php echo $childEmpAll[16]; ?>" readonly="readonly" class="form-controls"/></font></td>
            <?php if($designationRow['F8'] == 'FC' || $designationRow['F8'] == 'Admin') {  ?>
            <td><font face="Verdana" size="1"><input type="text" name="remark[]" value="<?php echo $childEmpAll[17]; ?>" class="form-controls"/></font></td>
            <?php }else{ ?>
            <td></td>
            <?php } ?>
        </tr>
        <?php
    }
    $conn->close();
}

?>
<script>
    $('.dropdown').on('change', function () {
        var selectedValue = $(this).val();
        var selectedDate = $(this).data('date');
        var selectedEmployeeId = $(this).data('employee-id');
//        $('.dropdown').not(this).find('option[value="' + selectedValue + '"]').prop('disabled', true);
        $('.dropdown').not(this).each(function () {
                if ($(this).data('date') === selectedDate && $(this).data('employee-id') === selectedEmployeeId) {
                    $(this).find('option[value="' + selectedValue + '"]').prop('disabled', true);
                }
            });
    });
    $(document).ready(function () {
        function formatTime(input) {
            // Split the input by decimal point
            var parts = input.split('.');
            var hours = parts[0] || '0';
            var minutes = parts[1] ? Math.round((parseFloat('0.' + parts[1]) * 60)) : '0';

            // Ensure two digits for minutes
            minutes = minutes < 10 ? '0' + minutes : minutes;

            return `${hours} hours ${minutes} minutes`;
        }

        // Event handler for input fields
        $('input.prj-hrs').on('input', function() {
            var input = $(this).val();

            // Check if input is in decimal format
            if (/^\d+(\.\d+)?$/.test(input)) {
                var formattedTime = formatTime(input);
                $(this).val(formattedTime);
            }
        });
        
        $('.number-field, [name="totalhrwork[]"]').on('input', function () {
            const row = $(this).closest('tr');
            const totalhrworkField = row.find('[name="totalhrwork[]"]');
            const inputFields = row.find('.number-field');
            const totalField = row.find('[name="totalhrs[]"]');
            const transithrsField = row.find('[name="transithrs[]"]');

            let totalUsedMinutes = 0;

            inputFields.each(function () {
                const value = $(this).val();
                const timeParts = parseTimeToDecimal(value);

                // Convert hours and minutes to total minutes
                totalUsedMinutes += (timeParts.hours * 60) + timeParts.minutes;
            });

            // Calculate total hours and minutes from used minutes
            const totalHours = Math.floor(totalUsedMinutes / 60);
            const totalMinutes = totalUsedMinutes % 60;

            // Display the total in hours and minutes format
            totalField.val(`${totalHours} hours ${totalMinutes} minutes`);

            // Calculate transit hours
            const totalhrwork = parseTimeToDecimal(totalhrworkField.val());
            const totalWorkMinutes = (totalhrwork.hours * 60) + totalhrwork.minutes;

            if (totalUsedMinutes > totalWorkMinutes) {
                totalField.css('background-color', 'red');
                transithrsField.val('Exceeds available time');
            } else {
                totalField.css('background-color', 'white');
                const remainingMinutes = totalWorkMinutes - totalUsedMinutes;
                const transitHours = Math.floor(remainingMinutes / 60);
                const transitMinutes = remainingMinutes % 60;
                transithrsField.val(`${transitHours} hours ${transitMinutes} minutes`);
            }
        });

        // Function to parse "HH hours MM minutes" to an object with hours and minutes
        function parseTimeToDecimal(timeString) {
            const timePattern = /(\d+)\s*hours?\s*(\d*)\s*minutes?/i;
            const match = timeString.match(timePattern);

            if (match) {
                const hours = parseInt(match[1]) || 0;
                const minutes = parseInt(match[2]) || 0;
                return { hours, minutes };
            }

            // If the string doesn't match the pattern, try parsing it as a number
            const decimalPattern = /^(\d+)(?:\.(\d+))?$/;
            const decimalMatch = timeString.match(decimalPattern);
            if (decimalMatch) {
                const wholeHours = parseInt(decimalMatch[1]);
                const fractionalHours = parseFloat("0." + decimalMatch[2]) || 0;
                const totalMinutes = Math.round(fractionalHours * 60);
                return { hours: wholeHours, minutes: totalMinutes };
            }

            // Default return if no valid input
            return { hours: 0, minutes: 0 };
        }

        $('.entrytime, .endtime').on('change', function() {
            // Find the closest table row
            var $row = $(this).closest('tr');

            // Get entry time and end time values
            var entryTime = $row.find('.entrytime').val();
            var endTime = $row.find('.endtime').val();

            if (entryTime && endTime) {
                // Calculate the difference in hours and minutes
                var diff = calculateTimeDifference(entryTime, endTime);

                // Update the total hours worked field
                $row.find('.totalhrwork').val(diff);
            }
        });

        function calculateTimeDifference(start, end) {
            // Parse the time strings
            var startTime = parseTime(start);
            var endTime = parseTime(end);

            // Calculate the difference in milliseconds
            var diffMs = endTime - startTime;

            // Handle negative difference due to overnight shifts
            if (diffMs < 0) {
                diffMs += 24 * 60 * 60 * 1000; // add 24 hours in milliseconds
            }

            // Convert to hours and minutes
            var hours = Math.floor(diffMs / (1000 * 60 * 60));
            var minutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));

            return formatHoursAndMinutes(hours, minutes);
        }

        function parseTime(timeStr) {
            // Split the time string into hours and minutes
            var parts = timeStr.split(':');
            var hours = parseInt(parts[0], 10);
            var minutes = parseInt(parts[1], 10);

            // Create a Date object at today's date with the given time
            var date = new Date();
            date.setHours(hours, minutes, 0, 0);

            return date;
        }

        function formatHoursAndMinutes(hours, minutes) {
            var hourText = hours === 1 ? 'hour' : 'hours';
            var minuteText = minutes === 1 ? 'minute' : 'minutes';

            // Format the result as "X hours Y minutes"
            return hours + ' ' + hourText + ' ' + minutes + ' ' + minuteText;
        }
        
//        function formatHoursAndMinutes(hours, minutes) {
//            // Ensure hours and minutes are two digits
//            var hourStr = hours.toString().padStart(2, '0');
//            var minuteStr = minutes.toString().padStart(2, '0');
//
//            // Format the result as "HH:MM"
//            return hourStr + ':' + minuteStr;
//        }
    });
</script>