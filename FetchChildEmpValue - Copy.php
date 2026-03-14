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
        ?>
        <tr>
            <td><font face="Verdana" size="1"><input type="text" name="dateemp[]" value="<?php echo date('Y/m/d', strtotime($childEmpAll[4])); ?>" class="form-controls" readonly="readonly"/></font></td>
            <td><font face="Verdana" size="1"><input type="number" name="empcode[]" value="<?php echo $childEmpAll[0]; ?>" class="form-controls" readonly="readonly"/></font></td>
            <td><font face="Verdana" size="1"><input type="text" name="empname[]" value="<?php echo $childEmpAll[1]; ?>" class="form-controls" readonly="readonly"/></font></td>
            <td><font face="Verdana" size="1"><input type="number" name="entrytime[]" value="<?php echo $childEmpAll[2]; ?>" class="form-controls" readonly="readonly"/></font></td>
            <td><font face="Verdana" size="1"><input type="number" name="endtime[]" value="<?php echo $childEmpAll[3]; ?>" class="form-controls" readonly="readonly"/></font></td>
            <td><font face="Verdana" size="1"><input type="number" name="totalhrwork[]" value="<?php echo number_format($totalHours / 3600, 2); ?>" readonly="readonly"/></font></td>
            <td><font face="Verdana" size="1"><input type="number" name="prj1hrs[]"  value="" class="number-field form-controls"/></font></td>
            <td><font face="Verdana" size="1"><input type="number" name="prj2hrs[]"  value="" class="number-field form-controls"/></font></td>
            <td><font face="Verdana" size="1"><input type="number" name="prj3hrs[]"  value="" class="number-field form-controls"/></font></td>
            <td><font face="Verdana" size="1"><input type="number" name="prj4hrs[]"  value="" class="number-field form-controls"/></font></td>
            <td><font face="Verdana" size="1"><input type="number" name="prj5hrs[]"  value="" class="number-field form-controls"/></font></td>
            <td><font face="Verdana" size="1"><input type="number" name="totalhrs[]" value="" class="form-controls"/></font></td>
            <td><font face="Verdana" size="1"><input type="number" name="transithrs[]" value="" class="form-controls"/></font></td>
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
        
        ?>
        <tr>
            <td><font face="Verdana" size="1"><input type="text" class="dateemp form-controls" name="dateemp[]" value="<?php echo date('Y/m/d', strtotime($childEmpAll[4])); ?>" readonly="readonly"/></font></td>
            <td><font face="Verdana" size="1"><input type="number" name="empcode[]" value="<?php echo $childEmpAll[0]; ?>" class="form-controls" readonly="readonly"/></font></td>
            <td><font face="Verdana" size="1"><input type="text" name="empname[]" value="<?php echo $childEmpAll[1]; ?>" class="form-controls" readonly="readonly"/></font></td>
            <td><font face="Verdana" size="1"><input type="text" name="entrytime[]" value="<?php echo $childEmpAll[2]; ?>" class="form-controls" readonly="readonly"/></font></td>
            <td><font face="Verdana" size="1"><input type="text" name="endtime[]" value="<?php echo $childEmpAll[3]; ?>" class="form-controls" readonly="readonly"/></font></td>
            <td><font face="Verdana" size="1"><input type="number" name="totalhrwork[]" value="<?php echo number_format($totalHours / 3600, 2); ?>" class="form-controls" readonly="readonly"/></font></td>
            <td><font face="Verdana" size="1">
                <select class="dropdown form-controls" name="project1[]" data-employee-id="<?php echo $childEmpAll[0]; ?>" data-date="<?php echo $childEmpAll[4]; ?>">
                    <option value="">--Select--</option>
                    <?php foreach ($prjfetchRow as $prjfetchName) { ?>
                        <option value="<?php echo $prjfetchName[0]; ?>" <?php echo ($childEmpAll[5] == $prjfetchName[0]) ? 'selected' : ''; ?>><?php echo $prjfetchName[0]; ?></option>
                    <?php } ?>
                </select>
                </font>
            </td>
            <td><font face="Verdana" size="1"><input type="number" name="prj1hrs[]"  value="<?php echo $childEmpAll[10]; ?>"  class="number-field form-controls"/></font></td>
            <td><font face="Verdana" size="1">
                <select class="dropdown form-controls" name="project2[]" data-employee-id="<?php echo $childEmpAll[0]; ?>" data-date="<?php echo $childEmpAll[4]; ?>">
                    <option value="">--Select--</option>
                    <?php foreach ($prjfetchRow as $prjfetchName) { ?>
                        <option value="<?php echo $prjfetchName[0]; ?>" <?php echo ($childEmpAll[6] == $prjfetchName[0]) ? 'selected' : ''; ?>><?php echo $prjfetchName[0]; ?></option>
                    <?php } ?>
                </select>
                </font>
            </td>
            <td><font face="Verdana" size="1"><input type="number" name="prj2hrs[]"  value="<?php echo $childEmpAll[11]; ?>" class="number-field form-controls"/></font></td>
            <td><font face="Verdana" size="1">
                <select class="dropdown form-controls" name="project3[]" data-employee-id="<?php echo $childEmpAll[0]; ?>" data-date="<?php echo $childEmpAll[4]; ?>">
                    <option value="">--Select--</option>
                    <?php foreach ($prjfetchRow as $prjfetchName) { ?>
                        <option value="<?php echo $prjfetchName[0]; ?>" <?php echo ($childEmpAll[7] == $prjfetchName[0]) ? 'selected' : ''; ?>><?php echo $prjfetchName[0]; ?></option>
                    <?php } ?>
                </select>
                </font>
            </td>
            <td><font face="Verdana" size="1"><input type="number" name="prj3hrs[]"  value="<?php echo $childEmpAll[12]; ?>" class="number-field form-controls"/></font></td>
            <td><font face="Verdana" size="1">
                <select class="dropdown form-controls" name="project4[]" data-employee-id="<?php echo $childEmpAll[0]; ?>" data-date="<?php echo $childEmpAll[4]; ?>">
                    <option value="">--Select--</option>
                    <?php foreach ($prjfetchRow as $prjfetchName) { ?>
                        <option value="<?php echo $prjfetchName[0]; ?>" <?php echo ($childEmpAll[8] == $prjfetchName[0]) ? 'selected' : ''; ?>><?php echo $prjfetchName[0]; ?></option>
                    <?php } ?>
                </select>
                </font>
            </td>
            <td><font face="Verdana" size="1"><input type="number" name="prj4hrs[]"  value="<?php echo $childEmpAll[13]; ?>" class="number-field form-controls"/></font></td>
            <td><font face="Verdana" size="1">
                <select class="dropdown form-controls" name="project5[]" data-employee-id="<?php echo $childEmpAll[0]; ?>" data-date="<?php echo $childEmpAll[4]; ?>">
                    <option value="">--Select--</option>
                    <?php foreach ($prjfetchRow as $prjfetchName) { ?>
                        <option value="<?php echo $prjfetchName[0]; ?>" <?php echo ($childEmpAll[9] == $prjfetchName[0]) ? 'selected' : ''; ?>><?php echo $prjfetchName[0]; ?></option>
                    <?php } ?>
                </select>
                </font>
            </td>
            <td><font face="Verdana" size="1"><input type="number" name="prj5hrs[]"  value="<?php echo $childEmpAll[14]; ?>" class="number-field form-controls"/></font></td>
            <td><font face="Verdana" size="1"><input type="text" name="totalhrs[]" value="<?php echo $childEmpAll[15]; ?>" readonly="readonly" class="form-controls"/></font></td>
            <td><font face="Verdana" size="1"><input type="number" name="transithrs[]" value="<?php echo $childEmpAll[16]; ?>" readonly="readonly" class="form-controls"/></font></td>
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
        $prjfetchQuery = "select Name from projectmaster where ProjectID IN(" . $childEmpAll[19] . ")";
        $prjfetchResult = mysqli_query($conn, $prjfetchQuery);
        $prjfetchRow = mysqli_fetch_all($prjfetchResult);
//        echo "<pre>";print_R($prjfetchRow);
//        $totalHours = date('H:i',strtotime($childEmpAll[3])) - date('H:i',strtotime($childEmpAll[2]));
        $totalHours = strtotime($childEmpAll[3]) - strtotime($childEmpAll[2]);
        ?>
        <tr>
            <td><font face="Verdana" size="1"><input type="text" class="dateemp form-controls" name="dateemp[]" value="<?php echo date('Y/m/d', strtotime($childEmpAll[4])); ?>" readonly="readonly"/></font></td>
            <td><font face="Verdana" size="1"><input type="number" name="empcode[]" value="<?php echo $childEmpAll[0]; ?>" class="form-controls" readonly="readonly"/></font></td>
            <td><font face="Verdana" size="1"><input type="text" name="empname[]" value="<?php echo $childEmpAll[1]; ?>" class="form-controls" readonly="readonly"/></font></td>
            <td><font face="Verdana" size="1"><input type="number" name="entrytime[]" value="<?php echo $childEmpAll[2]; ?>" class="form-controls" readonly="readonly"/></font></td>
            <td><font face="Verdana" size="1"><input type="number" name="endtime[]" value="<?php echo $childEmpAll[3]; ?>" class="form-controls" readonly="readonly"/></font></td>
            <td><font face="Verdana" size="1"><input type="number" name="totalhrwork[]" value="<?php echo number_format($totalHours / 3600, 2); ?>" class="form-controls" readonly="readonly"/></font></td>
            <td><font face="Verdana" size="1">
                <select class="dropdown form-controls" name="project1[]" data-employee-id="<?php echo $childEmpAll[0]; ?>" data-date="<?php echo $childEmpAll[4]; ?>">
                    <option value="">--Select--</option>
                    <?php foreach ($prjfetchRow as $prjfetchName) { ?>
                        <option value="<?php echo $prjfetchName[0]; ?>" <?php echo ($childEmpAll[5] == $prjfetchName[0]) ? 'selected' : ''; ?>><?php echo $prjfetchName[0]; ?></option>
                    <?php } ?>
                </select>
                </font>
            </td>
            <td><font face="Verdana" size="1"><input type="number" name="prj1hrs[]"  value="<?php echo $childEmpAll[10]; ?>"  class="number-field form-controls"/></font></td>
            <td><font face="Verdana" size="1">
                <select class="dropdown form-controls" name="project2[]" data-employee-id="<?php echo $childEmpAll[0]; ?>" data-date="<?php echo $childEmpAll[4]; ?>">
                    <option value="">--Select--</option>
                    <?php foreach ($prjfetchRow as $prjfetchName) { ?>
                        <option value="<?php echo $prjfetchName[0]; ?>" <?php echo ($childEmpAll[6] == $prjfetchName[0]) ? 'selected' : ''; ?>><?php echo $prjfetchName[0]; ?></option>
                    <?php } ?>
                </select>
                </font>
            </td>
            <td><font face="Verdana" size="1"><input type="number" name="prj2hrs[]"  value="<?php echo $childEmpAll[11]; ?>" class="number-field form-controls"/></font></td>
            <td><font face="Verdana" size="1">
                <select class="dropdown form-controls" name="project3[]" data-employee-id="<?php echo $childEmpAll[0]; ?>" data-date="<?php echo $childEmpAll[4]; ?>">
                    <option value="">--Select--</option>
                    <?php foreach ($prjfetchRow as $prjfetchName) { ?>
                        <option value="<?php echo $prjfetchName[0]; ?>" <?php echo ($childEmpAll[7] == $prjfetchName[0]) ? 'selected' : ''; ?>><?php echo $prjfetchName[0]; ?></option>
                    <?php } ?>
                </select>
                </font>
            </td>
            <td><font face="Verdana" size="1"><input type="number" name="prj3hrs[]"  value="<?php echo $childEmpAll[12]; ?>" class="number-field form-controls"/></font></td>
            <td><font face="Verdana" size="1">
                <select class="dropdown form-controls" name="project4[]" data-employee-id="<?php echo $childEmpAll[0]; ?>" data-date="<?php echo $childEmpAll[4]; ?>">
                    <option value="">--Select--</option>
                    <?php foreach ($prjfetchRow as $prjfetchName) { ?>
                        <option value="<?php echo $prjfetchName[0]; ?>" <?php echo ($childEmpAll[8] == $prjfetchName[0]) ? 'selected' : ''; ?>><?php echo $prjfetchName[0]; ?></option>
                    <?php } ?>
                </select>
                </font>
            </td>
            <td><font face="Verdana" size="1"><input type="number" name="prj4hrs[]"  value="<?php echo $childEmpAll[13]; ?>" class="number-field form-controls"/></font></td>
            <td><font face="Verdana" size="1">
                <select class="dropdown form-controls" name="project5[]" data-employee-id="<?php echo $childEmpAll[0]; ?>" data-date="<?php echo $childEmpAll[4]; ?>">
                    <option value="">--Select--</option>
                    <?php foreach ($prjfetchRow as $prjfetchName) { ?>
                        <option value="<?php echo $prjfetchName[0]; ?>" <?php echo ($childEmpAll[9] == $prjfetchName[0]) ? 'selected' : ''; ?>><?php echo $prjfetchName[0]; ?></option>
                    <?php } ?>
                </select>
                </font>
            </td>
            <td><font face="Verdana" size="1"><input type="number" name="prj5hrs[]"  value="<?php echo $childEmpAll[14]; ?>" class="number-field form-controls"/></font></td>
            <td><font face="Verdana" size="1"><input type="text" name="totalhrs[]" value="<?php echo $childEmpAll[15]; ?>" readonly="readonly" class="form-controls"/></font></td>
            <td><font face="Verdana" size="1"><input type="number" name="transithrs[]" value="<?php echo $childEmpAll[16]; ?>" readonly="readonly" class="form-controls"/></font></td>
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
        $('.number-field, [name="totalhrwork[]"]').on('input', function () {
            const row = $(this).closest('tr');
            const totalhrworkField = row.find('[name="totalhrwork[]"]');
            const inputFields = row.find('.number-field');
            const totalField = row.find('[name="totalhrs[]"]');
            const transithrsField = row.find('[name="transithrs[]"]');

            let sum = 0;
            inputFields.each(function () {
                sum += parseFloat($(this).val()) || 0;
            });

            totalField.val(sum.toFixed(2));

            if (sum > parseFloat(totalhrworkField.val())) {
                totalField.css('background-color', 'red');
                transithrsField.val('');
            } else {
                totalField.css('background-color', 'white');
                transithrsField.val((parseFloat(totalhrworkField.val()) - sum).toFixed(2));
            }
        });
    });
</script>