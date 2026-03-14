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
$ot1f = $_SESSION[$session_variable . "ot1f"];
$ot2f = $_SESSION[$session_variable . "ot2f"];
$otdf = $_SESSION[$session_variable . "otdf"];
$macAddress = $_SESSION[$session_variable . "MACAddress"];
$frt = $_SESSION[$session_variable . "FlagReportText"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=WagesCalculationMaster.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$iconn = openIConnection();
$message = $_GET["message"];
if ($message == "") {
    $message = "Wages Calculation Master";
}
if (isset($_POST['deleteWage'])) {
//    echo "<pre>";print_r($_POST['deleteRecord']);die;
    $idToDelete = intval($_POST['deleteWage']);
    $deleteQuery = "DELETE FROM wagesmaster WHERE id = $idToDelete";
    updateIData($iconn, $deleteQuery, true); // or mysqli_query($conn, $deleteQuery);
    $recordUpdated = "Record Deleted Successfully";
}
/* * ********Update Wages*********** */
if (isset($_POST['updateWages'])) {
    $selectedCategory = $_POST['category'] ?? '';

    // Ensure cat[] exists for all rows
    $_POST['cat'] = array_map(function ($v) use ($selectedCategory) {
        return trim($v) === '' ? $selectedCategory : $v;
    }, $_POST['cat']);

    foreach ($_POST['group'] as $index => $group) {
        $id = isset($_POST['id'][$index]) ? intval($_POST['id'][$index]) : 0;

        // Escape and assign values
        $category = $_POST['cat'][$index];
        $group = $_POST['group'][$index];
        $monfri = $_POST['monfri'][$index];
        $sat = $_POST['sat'][$index];
        $sun = $_POST['sun'][$index];
        $ph = $_POST['ph'][$index];
        $wk = $_POST['wk'][$index];
        $monthly = $_POST['monthly'][$index];
        $shiftCat = $_POST['shiftCat'][$index];
        $effective  = date("Ymd", strtotime($_POST['validfrm'][$index]));
//        $effective  = '20250105';
        if ($id > 0) {
            // Update query
            $update = "UPDATE wagesmaster SET 
                    Category = '$category', 
                    Blgroup = '$group', 
                    MonFri = '$monfri', 
                    Sat = '$sat', 
                    Sun = '$sun', 
                    PH = '$ph', 
                    Wk = '$wk', 
                    Monthly = '$monthly',
                    ShiftId = $shiftCat,
                    valid_from = '$effective'    
                   WHERE id = $id";
            updateIData($iconn, $update, true);
            $recordUpdated = "Record Updated Successfully";
        } else {
            // New row - Insert
            $insert = "INSERT INTO wagesmaster (
                          Category, Blgroup, MonFri, Sat, Sun, PH, Wk, Monthly, ShiftId, valid_from, valid_to
                      ) VALUES (
                         '$category', '$group', '$monfri', '$sat', '$sun', '$ph', '$wk', '$monthly', $shiftCat, '$effective', NULL
                      )";
            updateIData($iconn, $insert, true);
            $recordUpdated = "Record Inserted Successfully";
        }
    }
}
/* * ************************* */

$selectedCategory = isset($_GET['category']) ? $_GET['category'] : '';
$query = "SELECT * from wagesmaster WHERE Category = '" . $selectedCategory . "'";
$wagesResult = mysqli_query($conn, $query);

// Static categories you always want shown
$staticCategories = ['cat_8', 'cat_12'];

$catQuery = "SELECT DISTINCT Category From wagesmaster";
$catResult = mysqli_query($conn, $catQuery);

// If DB has no category, use static ones
$dbCategories = [];
if (mysqli_num_rows($catResult) > 0) {
    while ($catRow = mysqli_fetch_row($catResult)) {
        $dbCategories[] = $catRow[0];
    }
}

$categories = array_unique(array_merge($staticCategories, $dbCategories));

$shiftQuery = "SELECT * FROM tgroup";
$shiftResult = mysqli_query($conn, $shiftQuery);

if ($prints != "yes") {
    include 'header.php';
    ?>
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-12 d-flex no-block align-items-center">
                <h4 class="page-title">Wages Calculation Master</h4>
                <div class="ms-auto text-end">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Wages Calculation Master
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <?php
}
print'<div class="container-fluid">
                <div class="row">
                        <div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12">
                            <div class="card">
                                    <div class="card-body table-responsive">';
?>
<form method="GET" action="">
    <p align='center' style="color:green;font-family:Verdana;font-size:12px;"><b><?php echo $recordUpdated;
echo isset($recordUpdated) ? '<meta http-equiv="refresh" content="0">' : '';
?></b></p>
    <p align='center'><font face='Verdana' size='1' color='#FF0000'><b>Select category to update rate</b></font></p>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-5"></div>
                <div class="col-2">
                    <label><font size="2" face="Verdana">Category :</font></label>
                    <select name="category" class="form-control form-select select2 shadow-none select2-hidden-accessible" onchange="this.form.submit()" required="required">
                        <option value="">Select Category</option>
                        <?php
                        foreach ($categories as $catValue) {
                            $isSelected = ($selectedCategory == $catValue) ? 'selected' : '';
                            ?>
                            <option value="<?php echo $catValue; ?>" <?php echo $isSelected; ?>>
                                <?php
                                if ($catValue === 'cat_12') {
                                    echo '12 Hours';
                                } elseif ($catValue === 'cat_8') {
                                    echo '8 Hours';
                                } else {
                                    echo $catValue;
                                }
                                ?>
                            </option>
<?php } ?>
                    </select>
                </div>
                <div class="col-5"></div>
            </div>
        </div>
    </div>
</form>
</div>
</div>
</div>
</div>
<?php
$hasRows = mysqli_num_rows($wagesResult) > 0;
if (true && $selectedCategory) {
    ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-responsive">
                    <form method="POST" action="">
                        <input type="hidden" name="category" value="<?php echo $selectedCategory; ?>">
                        <table width="800" border="1" cellpadding="1" cellspacing="-1" bordercolor="#C0C0C0" class="table table-striped table-bordered dataTable" id="wagesTable">
                            <thead>
                                <tr>
                                    <td><font size="2" face="Verdana">Group</font></td>
                                    <td><font size="2" face="Verdana">Mon-Fri Rate</font></td>
                                    <td><font size="2" face="Verdana">Saturday Rate</font></td>
                                    <td><font size="2" face="Verdana">Sunday Rate</font></td>
                                    <td><font size="2" face="Verdana">Public Holiday Rate</font></td>
                                    <td><font size="2" face="Verdana">Weekly Incentive</font></td>
                                    <td><font size="2" face="Verdana">Monthly Incentive</font></td>
                                    <td><font size="2" face="Verdana">Assign Shift</font></td>
                                    <td><font size="2" face="Verdana">Valid From</font></td>
                                    <td><font size="2" face="Verdana">Action</font></td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 0;
                                if ($hasRows) {
                                    while ($row = mysqli_fetch_row($wagesResult)) {
                                        print "<tr>";
                                        print "<input type='hidden' name='id[$i]' value='$row[0]'>";
                                        print "<input type='hidden' name='cat[$i]' value='$selectedCategory'>";
                                        print "<td><input type='text' name='group[$i]' value='$row[2]' size='6' class='form-control'></td>";
                                        print "<td><input type='text' name='monfri[$i]' value='$row[3]' size='6' class='form-control'></td>";
                                        print "<td><input type='text' name='sat[$i]' value='$row[4]' size='6' class='form-control'></td>";
                                        print "<td><input type='text' name='sun[$i]' value='$row[5]' size='6' class='form-control'></td>";
                                        print "<td><input type='text' name='ph[$i]' value='$row[6]' size='6' class='form-control'></td>";
                                        print "<td><input type='text' name='wk[$i]' value='$row[7]' size='6' class='form-control'></td>";
                                        print "<td><input type='text' name='monthly[$i]' value='$row[8]' size='6' class='form-control'></td>";
                                        // Shift Dropdown
                                        print "<td><select name='shiftCat[$i]' class='form-control form-select' required>";
                                        print "<option value=''>Select Shift</option>";
                                        mysqli_data_seek($shiftResult, 0); // reset pointer before re-looping
                                        while ($shiftRow = mysqli_fetch_row($shiftResult)) {
                                            $shiftId = $shiftRow[0];
                                            $shiftName = $shiftRow[1];
                                            $selected = ($row[9] == $shiftId) ? "selected" : "";
                                            print "<option value='$shiftId' $selected>$shiftName</option>";
                                        }
                                        print "</select></td>";
                                        print "<td><input type='date' name='validfrm[$i]' value='".date('Y-m-d', strtotime($row[10]))."' size='10' class='form-control'></td>";
                                        print "<td>";
                                        if ($row[0]) {
                                            // Existing record — delete via form submit
                                            print "<button type='submit' name='deleteWage' value='$row[0]' class='btn btn-danger' onclick=\"return confirm('Are you sure to delete this record?')\">";
                                            print "<i class='mdi mdi-delete'></i>";
                                            print "</button>";
                                        } else {
                                            // Unsaved row — allow JS delete
                                            print "<button type='button' class='btn btn-danger deleteRowBtn'>";
                                            print "<i class='mdi mdi-delete'></i>";
                                            print "</button>";
                                        }
                                        print "</td>";
                                        print "</tr>";
                                        $i++;
                                    }
                                } else {
                                    // No records — show a blank row
                                    print "<tr>";
                                    print "<input type='hidden' name='id[$i]' value=''>";
                                    print "<input type='hidden' name='cat[$i]' value='$selectedCategory'>";
                                    print "<td><input type='text' name='group[$i]' class='form-control'></td>";
                                    print "<td><input type='text' name='monfri[$i]' class='form-control'></td>";
                                    print "<td><input type='text' name='sat[$i]' class='form-control'></td>";
                                    print "<td><input type='text' name='sun[$i]' class='form-control'></td>";
                                    print "<td><input type='text' name='ph[$i]' class='form-control'></td>";
                                    print "<td><input type='text' name='wk[$i]' class='form-control'></td>";
                                    print "<td><input type='text' name='monthly[$i]' class='form-control'></td>";
                                    print "<td><input type='date' name='validfrm[$i]' class='form-control'></td>";

                                    print "<td><select name='shiftCat[$i]' class='form-control form-select' required>";
                                    print "<option value=''>Select Shift</option>";
                                    mysqli_data_seek($shiftResult, 0);
                                    while ($shiftRow = mysqli_fetch_row($shiftResult)) {
                                        print "<option value='{$shiftRow[0]}'>{$shiftRow[1]}</option>";
                                    }
                                    print "</select></td>";
                                    print "<td><button type='button' id='addRowBtn' class='btn btn-primary'><i class='mdi mdi-plus'></i></button></td>";
                                    print "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    <?php if ((strpos($userlevel, $current_module . "A") !== false || strpos($userlevel, $current_module . "E") !== false || strpos($userlevel, $current_module . "D") !== false)) { ?>
                            <center><br><?php if($hasRows){ ?><button type="button" id="addRowBtn" class="btn btn-primary"><i class='mdi mdi-plus'></i> Add New Row</button><?php } ?>&nbsp;&nbsp;&nbsp;<input type="submit" name="updateWages" value="Add/Update Wages" class='btn btn-primary'></center>
                    <?php } ?>
                    </form>
                    <!-- Hidden Template -->
                    <table style="display:none;">
                        <tbody>
                            <tr id="rowTemplate">
                        <input type="hidden" name="id[]" value="">
                        <input type="hidden" name="cat[]" value="">
                        <td><input type="text" name="group[]" class="form-control"></td>
                        <td><input type="text" name="monfri[]" class="form-control"></td>
                        <td><input type="text" name="sat[]" class="form-control"></td>
                        <td><input type="text" name="sun[]" class="form-control"></td>
                        <td><input type="text" name="ph[]" class="form-control"></td>
                        <td><input type="text" name="wk[]" class="form-control"></td>
                        <td><input type="text" name="monthly[]" class="form-control"></td>
                        <td>
                            <select name="shiftCat[]" class="form-control" required>
                                <option value="">Select Shift</option>
                                <?php
                                mysqli_data_seek($shiftResult, 0);
                                while ($shiftRow = mysqli_fetch_row($shiftResult)) {
                                    ?>
                                    <option value="<?= $shiftRow[0] ?>"><?= $shiftRow[1] ?></option>
    <?php } ?>
                            </select>
                        </td>
                        <td><button type="button" class="btn btn-danger deleteRowBtn"><i class="mdi mdi-delete"></i></button></td>
                        </tr>
                        </tbody>
                    </table>
<?php } ?>
            </div></div></div></div></div>
<?php include 'footer.php'; ?>
<script>
    $(document).ready(function () {
        // Add row
        $("#addRowBtn").click(function () {
            var newRow = $("#rowTemplate").clone().removeAttr('id').show();

            newRow.find("input, select").each(function () {
                $(this).val('');
            });

            // Set selected category to cat[]
            var selectedCat = $("select[name='category']").val();
            newRow.find("input[name='cat[]']").val(selectedCat);

            // Reset ID so it's treated as a new row
            newRow.find("input[name='id[]']").val('');

            $("#wagesTable tbody").append(newRow);
        });

        // Delete row
        $("#wagesTable").on("click", ".deleteRowBtn", function () {
            $(this).closest("tr").remove();
        });
    });
</script>