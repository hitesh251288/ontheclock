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

print "<center>";
displayHeader($prints, false, false);
displayLinks($current_module, $userlevel);


if (isset($_GET['id'])) {
    $fetchQuery = "SELECT * from hourlywagescasual where id=" . $_GET['id'];
    $result = mysqli_query($conn, $fetchQuery);
    $fetchData = mysqli_fetch_array($result);
//    echo "<pre>";print_R($fetchData);
}

$deptQuery = "select DISTINCT dept from deptgate";
$deptResult = mysqli_query($conn, $deptQuery);
$deptData = mysqli_fetch_all($deptResult);
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
<!--<link href="resource/css/bootstrap.min.css" rel="stylesheet" />
<script src="resource/js/bootstrap.min.js" ></script>-->
<form method="post" action="GradeRateMasterUpdateScript.php">
    <input type="hidden" name="id" value="<?php echo $fetchData['id']; ?>" />
    <table width="800" border="1" cellpadding="1" bordercolor="#C0C0C0" cellspacing="-1">
        <tbody>
            <tr>
                <td align="right" width="35%"><font size="2" face="Verdana">Activity :</font></td>
                <td width="65%">
                    <select name="activity" required="required" class="form-control">
                        <option>--- Select Activity ---</option>
                        <?php foreach ($deptData as $deptAll) { ?>
                            <option value="<?php echo $deptAll[0]; ?>" <?php echo (isset($_GET['id']) && $fetchData['activity'] == $deptAll[0]) ? 'selected' : ''; ?>><?php echo $deptAll[0]; ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td align="right" width="35%"><font size="2" face="Verdana">Grades :</font></td>
                <td width="65%"><input type="text" name="grades" class="form-control" required="required" value="<?php echo (isset($_GET['id'])) ? $fetchData['grades'] : ''; ?>"/></td>
            </tr>
            <tr>
                <td align="right" width="35%"><font size="2" face="Verdana">Regular Rate/hr for all working days :</font></td>
                <td width="65%"><input type="number" name="regrate" class="form-control" id="input_field_div" value="<?php echo (isset($_GET['id'])) ? $fetchData['regrate'] : ''; ?>"/></td>
            </tr>
            <tr>
                <td align="right" width="35%"><font size="2" face="Verdana">O/T Rate/hr on regular days :</font></td>
                <td width="65%"><input type="number" name="ot_regular" class="form-control"  value="<?php echo (isset($_GET['id'])) ? $fetchData['ot_regular'] : ''; ?>"/></td>
            </tr>
            <tr>
                <td align="right" width="35%"><font size="2" face="Verdana">O/T Rate/hr on Sunday/ Public Holidays :</font></td>
                <td width="65%"><input type="number" name="ot_holiday" class="form-control" value="<?php echo (isset($_GET['id'])) ? $fetchData['ot_holiday'] : ''; ?>"/></td>
            </tr>
            <tr>
                <td align="right" width="35%"><font size="2" face="Verdana">Regular day Rate :</font></td>
                <td width="65%"><input type="number" name="regdayrate" class="form-control" value="<?php echo (isset($_GET['id'])) ? $fetchData['regdayrate'] : ''; ?>" id="input_field_div1"/></td>
            </tr>
            <tr>
                <td align="right" width="35%"><font size="2" face="Verdana"></font></td>
                <td width="65%"><input type="submit" name="update" value="Update" /></td>
            </tr>
        </tbody> 
    </table>
</form>
<?php
print "</center>";
?>
<script>
    const inputFieldDiv = document.getElementById("input_field_div");
    const inputFieldDiv1 = document.getElementById("input_field_div1");
    inputFieldDiv.addEventListener("input", function () {
        inputFieldDiv1.readonly = this.value.trim() !== "";
    });
    inputFieldDiv1.addEventListener("input", function () {
        inputFieldDiv.readonly = this.value.trim() !== "";
    });
</script>