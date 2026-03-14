<?php
ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "37";
set_time_limit(0);
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
$macAddress = $_SESSION[$session_variable . "MACAddress"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=AssignEmployeeGroup.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$iconn = openIConnection();
$message = $_GET["message"];
if ($message == "") {
    $message = "Assign Employee Group";
}
if (isset($_POST['assignGroup'])) {
    $selectedGroup = $_POST['grpEmpAssign'] ?? '';
    $selectedEmployees = $_POST['chk'] ?? [];

    if ($selectedGroup && count($selectedEmployees)) {
        foreach ($selectedEmployees as $empId) {
            $empId = mysqli_real_escape_string($conn, $empId);
            $query = "UPDATE tuser SET F1 = '$selectedGroup' WHERE id = '$empId'";
            updateIData($iconn, $query, true);
        }
        $recordUpdated = "Group assigned successfully.";
//        header("Location: " . $_SERVER['PHP_SELF']);
//        exit;
    } else {
        echo "<script>alert('Please select a group and at least one employee.');</script>";
    }
}

$groupQuery = "SELECT DISTINCT Blgroup From wagesmaster";
$grpResult = mysqli_query($conn, $groupQuery);
while ($grpRow = mysqli_fetch_row($grpResult)) {
    $blGroup[] = $grpRow[0];
}

$empQuery = "SELECT id, name, F1, company from tuser where remark='Casual'";
$empResult = mysqli_query($conn, $empQuery);
include 'header.php';
?>
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-12 d-flex no-block align-items-center">
            <h4 class="page-title">Assign Employee Group</h4>
            <div class="ms-auto text-end">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Assign Employee Group
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <form method="POST">
        <p align='center' style="color:green;font-family:Verdana;font-size:12px;"><b><?php echo $recordUpdated;
echo isset($recordUpdated) ? '<meta http-equiv="refresh" content="0">' : '';
?></b></p>
        <div class="row">
            <div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5"></div>
                            <div class="col-2">
                                <label>Group :</label>
                                <select name="grpEmpAssign" class="form-control form-select" required="required">
                                    <option value="">--</option>
                                    <?php foreach ($blGroup as $grpData) { ?>
                                        <option value="<?php echo $grpData; ?>"><?php echo $grpData; ?></option>
<?php } ?>
                                </select>
                            </div>
                            <div class="col-5"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <!--<div class="col-4" style="text-align: right;"></div>-->
                        <!--<div class="col-6">ID&nbsp;&nbsp;&nbsp;&nbsp;Name&nbsp;&nbsp;&nbsp;&nbsp;Assign Group</div>-->
                        <div class="col-2"></div>
                        <div class="col-8">
                            <table class="table table-striped table-bordered dataTable">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" name="chkAll" ></th>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Division</th>
                                        <th>Assign Group</th>
                                    </tr>
                                </thead>
<?php while ($empRow = mysqli_fetch_row($empResult)) { ?>
                                    <tr>
                                        <td><input type="checkbox" name="chk[]" value="<?= $empRow[0]; ?>"></td>
                                        <td><?php echo $empRow[0]; ?></td>
                                        <td><?php echo $empRow[1]; ?></td>
                                        <td><?php echo $empRow[3]; ?></td>
                                        <td><?php echo $empRow[2]; ?></td>
                                    </tr>
<?php } ?>
                            </table>
                        </div>
                        <div class="col-2"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12 text-center">
                <button type="submit" name="assignGroup" class="btn btn-primary">Assign Group</button>
            </div>
        </div>
    </form>    
</div>
<?php include 'footer.php'; ?>
<script>
    document.querySelector("input[name='chkAll']").addEventListener('change', function () {
        const checkboxes = document.querySelectorAll("input[name='chk[]']");
        checkboxes.forEach(cb => cb.checked = this.checked);
    });
</script>
