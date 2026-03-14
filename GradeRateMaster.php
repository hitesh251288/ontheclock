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
$act = $_GET["act"];
if ($act == "") {
    $act = $_POST["act"];
}
print "<center>";
displayHeader($prints, false, false);
displayLinks($current_module, $userlevel);

$deptQuery = "select DISTINCT dept from deptgate";
$deptResult = mysqli_query($conn, $deptQuery);
$deptData = mysqli_fetch_all($deptResult);
?>
<form class="form-horizontal" action="GradeRateMasterInsertScript.php" method="post">
    <table border="1" cellpadding="1" bordercolor="#C0C0C0" cellspacing="-1" width="800" id="dynamicTable">
        <thead>
            <tr>
                <th><font face="Verdana" size="2">Activity</font></th>
                <th><font face="Verdana" size="2">Grades</font></th>
                <th><font face="Verdana" size="2">Regular Rate/hr for all working days</font></th>
                <th><font face="Verdana" size="2">O/T Rate/hr on regular days</font></th>
                <th><font face="Verdana" size="2">O/T Rate/hr on Sunday/ Public Holidays</font></th>
                <th><font face="Verdana" size="2">Regular day Rate</font></th>
                <th><font face="Verdana" size="2">Action</font></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><select name="activity" required="required">
                    <option>--- Select Activity ---</option>
                        <?php foreach($deptData as $deptAll){ ?>
                        <option value="<?php echo $deptAll[0]; ?>"><?php echo $deptAll[0]; ?></option>
                        <?php } ?>
                    </select>
                </td>  
                <td><input type="text" name="addmore[0][grades]" class="form-control" required="required"/></td>  
                <td><input type="number" name="addmore[0][regrate]" class="form-control" id="input_field_div"/></td>  
                <td><input type="number" name="addmore[0][ot_regular]" class="form-control" /></td>  
                <td><input type="number" name="addmore[0][ot_holiday]" class="form-control" /></td>  
                <td><input type="number" name="addmore[0][regdayrate]" class="form-control" id="input_field_div1"/></td>  
                <td><button type="button" name="add" id="add" class="btn btn-success">Add More</button></td>  
            </tr>
        </tbody>
    </table><br>
    <input type="submit" name="submit" value="Submit" />
</form>
<?php
print "</center>";
?>
<link rel="stylesheet" href="resource/css/bootstrap.min.css" />  
<script src="resource/js/jquery.min.js"></script>
<script>
    var i = 0;

    $("#add").click(function () {
        ++i;
        $("#dynamicTable").append('<tr><td></td><td><input type="text" name="addmore[' + i + '][grades]" class="form-control" /></td><td><input type="number"  name="addmore[' + i + '][regrate]" class="form-control" id="input_field_divs' + i + '"/></td><td><input type="number" name="addmore[' + i + '][ot_regular]"  class="form-control" /></td><td><input type="number" name="addmore[' + i + '][ot_holiday]"  class="form-control" /></td><td><input type="number" name="addmore[' + i + '][regdayrate]"  class="form-control" id="input_field_divs1' + i + '"/></td><td><button type="button" class="btn btn-danger remove-tr">Remove</button></td></tr>');

        const inputFieldDivs = document.getElementById("input_field_divs" + i);
        const inputFieldDivs1 = document.getElementById("input_field_divs1" + i);
        inputFieldDivs.addEventListener("input", function () {
            inputFieldDivs1.disabled = this.value.trim() !== "";
        });
        inputFieldDivs1.addEventListener("input", function () {
            inputFieldDivs.disabled = this.value.trim() !== "";
        });
    });

    $(document).on('click', '.remove-tr', function () {
        $(this).parents('tr').remove();
    });

    const inputFieldDiv = document.getElementById("input_field_div");
    const inputFieldDiv1 = document.getElementById("input_field_div1");
    inputFieldDiv.addEventListener("input", function () {
        inputFieldDiv1.disabled = this.value.trim() !== "";
    });
    inputFieldDiv1.addEventListener("input", function () {
        inputFieldDiv.disabled = this.value.trim() !== "";
    });
</script>

