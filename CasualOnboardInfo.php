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
function fetch_all($result)
{
   $rows = array(); 
   while ($row = mysqli_fetch_array($result))
          $rows[] = $row;

   return $rows;
}


if (isset($_GET['id'])) {
    $fetchQuery = "SELECT * from onboardrequest where id=" . $_GET['id'];
    $result = mysqli_query($conn, $fetchQuery);
    $fetchData = mysqli_fetch_array($result);
}
$deptQuery = "select DISTINCT dept from deptgate";
$deptResult = mysqli_query($conn, $deptQuery);
$deptData = mysqli_fetch_all($deptResult);

$gradeQuery = "select grades,regrate from hourlywagescasual";
$gradeResult = mysqli_query($conn, $gradeQuery);
$gradeData = mysqli_fetch_all($gradeResult);

$casualQuery = "SELECT name,id FROM tuser t where id LIKE '10%' AND remark != 'SENIOR STAFF'";
$casualResult = mysqli_query($conn, $casualQuery);
$casualData = mysqli_fetch_all($casualResult);

$designationQuery = "SELECT * FROM tuser where name='$username'";
$designationResult = mysqli_query($conn, $designationQuery);
$designationRow = mysqli_fetch_assoc($designationResult);
if($designationRow['F8'] == 'FC' || $designationRow['F8'] == 'Admin'){
    $readonly = '';
}else{
    $readonly = 'readonly';
}

print "<center>";
displayHeader($prints, false, false);
displayLinks($current_module, $userlevel);
?>
<style>
    /*    .form-container {
            border: 5px solid #000;
            padding: 20px;
        }
        .form-group{
            margin-bottom: 1px !important;
        }*/
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
<!--<link href="resource/css/bootstrap.min.css" rel="stylesheet" />-->
<script src="resource/js/jquery.min.js"></script>
<!--<script src="resource/js/bootstrap.min.js" ></script>-->
<form class="form-horizontal" action="" method="post">
<table width="800" border="1" cellpadding="1" bordercolor="#C0C0C0" cellspacing="-1">
    <tbody>
        <tr>
            <td align="right" width="25%"><font size="2" face="Verdana">Form No: </font></td>
            <td width="25%"><input type="text" class="form-controls" id="form_no" name="form_no" value="<?php echo (isset($_GET['id'])) ? $fetchData['form_no'] : ''; ?>" required="required"></td>
            <td align="right" width="25%"><font size="2" face="Verdana">Full Name:</font></td>
            <td align="right" width="25%">
                <font size="2" face="Verdana">
                    <input type="text" class="form-controls"  name="fullname" value="<?php echo (isset($_GET['id'])) ? $fetchData['fullname'] : ''; ?>" required="required">
                </font>
            </td>
        </tr>
        <tr>
            <td align="right" width="25%"><font size="2" face="Verdana">Date Of Birth: </font></td>
            <td width="25%"><input type="date" class="form-controls" id="dob" name="dob" value="<?php echo (isset($_GET['id'])) ? $fetchData['dob'] : ''; ?>"></td>
            <td align="right" width="25%"><font size="2" face="Verdana">Mobile Number:</font></td>
            <td align="right" width="25%">
                <font size="2" face="Verdana"><input type="text" class="form-controls" id="mob_no" name="mob_no" value="<?php echo (isset($_GET['id'])) ? $fetchData['mob_no'] : ''; ?>"></font>
            </td>
        </tr>
        <tr>
            <td align="right" width="25%"><font size="2" face="Verdana">Contact Address: </font></td>
            <td width="25%"><textarea type="text" class="form-controls" id="cnt_add" name="cnt_add"><?php echo (isset($_GET['id'])) ? $fetchData['cnt_add'] : ''; ?></textarea></td>
            <td align="right" width="25%"><font size="2" face="Verdana">Name Next Of Kin:</font></td>
            <td align="right" width="25%">
                <font size="2" face="Verdana"><input type="text" class="form-controls" id="name_kin" name="name_kin" value="<?php echo (isset($_GET['id'])) ? $fetchData['name_kin'] : ''; ?>"></font>
            </td>
        </tr>
        <tr>
            <td align="right" width="25%"><font size="2" face="Verdana">Contact Number Of Next Of Kin: </font></td>
            <td width="25%"><input type="text" class="form-controls" id="cnt_kin" name="cnt_kin" value="<?php echo (isset($_GET['id'])) ? $fetchData['cnt_kin'] : ''; ?>"></td>
            <td align="right" width="25%"><font size="2" face="Verdana">Gender:</font></td>
            <td width="25%">
                <font size="2" face="Verdana"><input type="radio"  id="male" name="gender" value="male" <?php echo (isset($_GET['id']) && $fetchData['gender'] == 'male') ? 'checked' : ''; ?> checked="checked"> <label for="male">Male</label>
                <input type="radio"  id="female" name="gender" value="female" <?php echo (isset($_GET['id']) && $fetchData['gender'] == 'female') ? 'checked' : ''; ?>> <label for="female">Female</label></font>
            </td>
        </tr>
        <tr>
            <td align="right" width="25%"><font size="2" face="Verdana">Commencement Date: </font></td>
            <td width="25%"><input type="date" class="form-controls" id="commencement_date" name="commencement_date" value="<?php echo (isset($_GET['id'])) ? $fetchData['commencement_date'] : ''; ?>"></td>
            <td align="right" width="25%"><font size="2" face="Verdana">Department:</font></td>
            <td align="right" width="25%">
                <font size="2" face="Verdana">
                <select class="form-controls" id="dept" name="dept">
                    <option>--- Select Department ---</option>
                    <?php foreach ($deptData as $deptAll) { ?>
                        <option value="<?php echo $deptAll[0]; ?>" <?php echo (isset($_GET['id']) && $fetchData['dept'] == $deptAll[0]) ? 'selected' : ''; ?>><?php echo $deptAll[0]; ?></option>
                    <?php } ?>
                </select>
                </font>
            </td>
        </tr>
        <tr>
            <td align="right" width="25%"><font size="2" face="Verdana">Designation: </font></td>
            <td width="25%"><input type="text" class="form-controls" id="designation" name="designation" value="<?php echo (isset($_GET['id'])) ? $fetchData['designation'] : ''; ?>"></td>
            <td align="right" width="25%"><font size="2" face="Verdana">Bank Name:</font></td>
            <td align="right" width="25%">
                <font size="2" face="Verdana"><input type="text" class="form-controls" id="bankname" name="bankname" value="<?php echo (isset($_GET['id'])) ? $fetchData['bankname'] : ''; ?>" <?php echo $readonly; ?> ></font>
            </td>
        </tr>
        <tr>
            <td align="right" width="25%"><font size="2" face="Verdana">Account Number: </font></td>
            <td width="25%"><input type="number" class="form-controls" id="acno" name="acno" value="<?php echo (isset($_GET['id'])) ? $fetchData['acno'] : ''; ?>" <?php echo $readonly; ?>></td>
            <td align="right" width="25%"><font size="2" face="Verdana">Grade:</font></td>
            <td align="right" width="25%">
                <font size="2" face="Verdana">
                <select class="form-controls" id="grade" name="grade">
                    <option>--- Select Grade ---</option>
                    <?php foreach ($gradeData as $gradeAll) { ?>
                        <option value="<?php echo $gradeAll[0]; ?>" <?php echo ($_GET['id'] && $fetchData['grade'] == $gradeAll[0]) ? 'selected' : ''; ?>><?php echo $gradeAll[0]; ?></option>
                    <?php } ?>
                </select>
                </font>
            </td>
        </tr>
        <tr>
            <td align="right" width="25%"><font size="2" face="Verdana">Hourly Rate: </font></td>
            <td width="25%"><input type="text" class="form-controls" id="hrrate" name="hrrate" value="<?php echo (isset($_GET['id'])) ? $fetchData['hrrate'] : ''; ?>" readonly="readonly"></td>
            <td align="right" width="25%"><font size="2" face="Verdana">BVN:</font></td>
            <td align="right" width="25%">
                <font size="2" face="Verdana"><input type="number" class="form-controls" id="bvn" name="bvn" value="<?php echo (isset($_GET['id'])) ? $fetchData['bvn'] : ''; ?>"></font>
            </td>
        </tr>
        <tr>
            <td align="right" width="25%"><font size="2" face="Verdana">NIN: </font></td>
            <td width="25%"><input type="text" class="form-controls" id="nin" name="nin" value="<?php echo (isset($_GET['id'])) ? $fetchData['nin'] : ''; ?>"></td>
            <td align="right" width="25%"><font size="2" face="Verdana">Employee Name As Per Bank: </font></td>
            <td align="right" width="25%"><input type="text" class="form-controls" id="name_as_per_bank" name="name_as_per_bank" value="<?php echo (isset($_GET['id'])) ? $fetchData['name_as_per_bank'] : ''; ?>" <?php echo $readonly; ?>></td>
        </tr>
        <tr>
            <td align="right" width="25%"><font size="2" face="Verdana"></font></td>
            <td width="25%"></td>
            <td width="25%">
                <?php if (isset($_GET['id'])) { ?>
                    <input type="submit" class="form-control" name="update" value="Update">
                <?php } else { ?>
                    <input type="submit" class="form-control" name="submit" value="Submit">
                <?php } ?>
            </td>
            <td align="right" width="25%"></td>
        </tr>
    </tbody>
</table>
</form>
<?php
if (isset($_POST['submit'])) {
//    echo "<pre>";print_R($_POST);exit;
    $form_no = $_POST['form_no'];
    $fullname = $_POST['fullname'];
    $dob = $_POST['dob'];
    $mob_no = $_POST['mob_no'];
    $cnt_add = $_POST['cnt_add'];
    $name_kin = $_POST['name_kin'];
    $cnt_kin = $_POST['cnt_kin'];
    $gender = $_POST['gender'];
    $commencement_date = $_POST['commencement_date'];
    $dept = $_POST['dept'];
    $designation = $_POST['designation'];
    $bankname = $_POST['bankname'];
    $acno = $_POST['acno'];
    $hrrate = $_POST['hrrate'];
    $grade = $_POST['grade'];
    $bvn = $_POST['bvn'];
    $nin = $_POST['nin'];
    $name_as_per_bank = $_POST['name_as_per_bank'];
    $onboardQuery = "Insert into onboardrequest(form_no,fullname,dob,mob_no,cnt_add,name_kin,"
            . "cnt_kin,gender,commencement_date,dept,designation,bankname,acno,hrrate,grade,bvn,"
            . "nin,status_reg,name_as_per_bank)values('$form_no','$fullname','$dob','$mob_no','$cnt_add','$name_kin',"
            . "'$cnt_kin','$gender','$commencement_date','$dept','$designation','$bankname','$acno',"
            . "'$hrrate','$grade','$bvn','$nin','0','$name_as_per_bank')";
    $onboardResult = mysqli_query($conn, $onboardQuery);
    if ($onboardResult) {
        echo "Data Inserted Successfully";
        header('Location:CasualOnboardInfoView.php?msg=1');
    } else {
        $vars = 0;
        echo "Data Not Inserted";
        header('Location:CasualOnboardInfoView.php?msg=' . $vars);
    }
}
if (isset($_POST['update'])) {
    $form_no = $_POST['form_no'];
    $fullname = $_POST['fullname'];
    $dob = $_POST['dob'];
    $mob_no = $_POST['mob_no'];
    $cnt_add = $_POST['cnt_add'];
    $name_kin = $_POST['name_kin'];
    $cnt_kin = $_POST['cnt_kin'];
    $gender = $_POST['gender'];
    $commencement_date = $_POST['commencement_date'];
    $dept = $_POST['dept'];
    $designation = $_POST['designation'];
    $bankname = $_POST['bankname'];
    $acno = $_POST['acno'];
    $hrrate = $_POST['hrrate'];
    $grade = $_POST['grade'];
    $bvn = $_POST['bvn'];
    $nin = $_POST['nin'];
    $name_as_per_bank = $_POST['name_as_per_bank'];
    $updateQuery = "update onboardrequest SET form_no='$form_no',fullname='$fullname',"
            . "dob='$dob',mob_no='$mob_no',cnt_add='$cnt_add',name_kin='$name_kin',"
            . "cnt_kin='$cnt_kin',gender='$gender',commencement_date='$commencement_date',"
            . "dept='$dept',designation='$designation',bankname='$bankname',acno='$acno',"
            . "hrrate='$hrrate',grade='$grade',bvn='$bvn',nin='$nin',name_as_per_bank='$name_as_per_bank' where id=" . $_GET['id'];
    $updateResult = mysqli_query($conn, $updateQuery);
    if ($updateResult) {
        echo "Data Updated Successfully";
        header('Location:CasualOnboardInfoView.php?msg=2');
    } else {
        $var = 3;
        echo "Data Not Updated";
        header('Location:CasualOnboardInfoView.php?msg=' . $var);
    }
}
print "</center>";
?>
<script>
    $('#grade').on('change', function () {
        var selectedValue = $(this).val();
        $.ajax({
            url: 'FetchGradeValue.php',
            method: 'POST',
            data: {selectedValue: selectedValue},
            success: function (response) {
                $('#hrrate').val(response);
            }
        });
    });
</script>