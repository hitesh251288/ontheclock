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
if ($_GET['id']) {
    $updatehrsQuery = "select * from projecthrsallocation where id=" . $_GET['id'];
} else {
//    $updatehrsQuery = "select * from projecthrsallocation where id=".$_GET['iddata'];
    $updatehrsQuery = "select p.*, o.grade, o.hrrate, h.ot_holiday,h.regrate, h.ot_regular, h.regdayrate from projecthrsallocation p "
            . "LEFT JOIN onboardrequest o ON o.fullname=p.empname "
            . "LEFT JOIN hourlywagescasual h ON h.grades=o.grade "
            . "where p.id=" . $_GET['iddata'];
}
$selecthrsResult = mysqli_query($conn, $updatehrsQuery);
$updateResult = mysqli_fetch_array($selecthrsResult);
$regularDayAmount = $updateResult[37] / 8;
//echo "<pre>";print_R($updateResult);
//echo $updateResult[5];
$projectQuery = "select Name from projectmaster";
$prjResult = mysqli_query($conn, $projectQuery);
$prjData = mysqli_fetch_all($prjResult);

//$userQuery = "SELECT employee_id,fullname FROM onboardrequest where grade LIKE 'S%' AND status_reg= '1'";
$userQuery = "SELECT id,name FROM tuser where F8='SV'";
$userResult = mysqli_query($conn, $userQuery);
$userData = mysqli_fetch_all($userResult);

$publicDate = date('Ymd', strtotime($updateResult[3]));
$publicQuery = "SELECT OTDate from otdate where OTDate=$publicDate";
$publicResult = mysqli_query($conn, $publicQuery);
//        $publicRaw = mysqli_fetch_all($publicResult);
$publicRaw = mysqli_num_rows($publicResult);
$isSunday = date("w", strtotime($updateResult[3])) == 0;
if ($isSunday || $publicRaw > 0) {
    $color = "background-color:red";
} else {
    $color = "";
}
if ($updateResult[8] > 8) {
    $overtimehours = $updateResult[8] - 8;
//    $otAmount = $sub * $updateResult[35];
//    $otAmount = $sub * $updateResult[35];
    $regularAmtSuperVisor = $overtimehours * $updateResult[37] / 8;
}

if ($overtimehours < 0) {
    $overtimehours = "";
}
print "<center>";
displayHeader($prints, false, false);
displayLinks($current_module, $userlevel);
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
<form method="post" action="ProjectHoursAllocationUpdateScript.php">
    <table width="800" border="1" cellpadding="1" bordercolor="#C0C0C0" cellspacing="-1">
        <tbody>
            <tr><input type="hidden" name="id" value="<?php echo $updateResult[0]; ?>" />
        <input type="hidden" name="iddata" value="<?php echo $_GET['iddata']; ?>" />
        <td align="right" width="25%"><font size="2" face="Verdana">Supervisor Code & Name: </font></td>
        <td width="75%" colspan="3">
            <select name="hireauth" class="form-control" disabled="disabled">
                <?php foreach ($userData as $userAll) { ?>
                    <option value="<?php echo $userAll[0]; ?>" name="<?php echo $userAll[1]; ?>" <?php echo ($userAll[0] == $updateResult[1]) ? "selected" : ""; ?>><?php echo $userAll[0] . ': ' . $userAll[1]; ?></option>
                <?php } ?>
            </select>
        </td>
        </tr>
        <tr>
            <td align="right" width="25%"><font size="2" face="Verdana">Date: </font></td>
            <td width="25%"><input type="text" size="12" name="empdate"  maxlength="12" value="<?php echo $updateResult[3]; ?>"  class="form-control" readonly="readonly" /></td>
            <td align="right" width="25%"><font size="2" face="Verdana"></font></td>
            <td align="right" width="25%"><font size="2" face="Verdana"></font></td>
        </tr>
        <tr>
            <td align="right" width="25%"><font size="2" face="Verdana">Employee Code: </font></td>
            <td width="25%"><input type="number" name="empcode" class="form-control" value="<?php echo $updateResult[4]; ?>" readonly="readonly"/></td>
            <td align="right" width="15%"><font size="2" face="Verdana">Employee Name: </font></td>
            <td width="35%"><input type="text" name="empname" class="form-controls" value="<?php echo $updateResult[5]; ?>" readonly="readonly"/></td>
        </tr>
        <tr>
            <td align="right" width="25%"><font size="2" face="Verdana">Entry Time: </font></td>
            <td width="25%"><input type="number" name="entrytime" class="form-control" value="<?php echo $updateResult[6]; ?>" readonly="readonly"/></td>
            <td align="right" width="25%"><font size="2" face="Verdana">Exit Time: </font></td>
            <td width="25%"><input type="number" name="exittime" class="form-control" value="<?php echo $updateResult[7]; ?>" readonly="readonly"/></td>
        </tr>
        <tr>
            <td align="right" width="25%"><font size="2" face="Verdana">Total Hours Work: </font></td>
            <td width="25%"><input type="text" name="totalhrwork" id="totalhrwork" class="form-control" value="<?php echo $updateResult[8]; ?>" readonly="readonly" onchange="calculateHours()"/></td>
            <td align="right" width="25%"><font size="2" face="Verdana"></font></td>
            <td align="right" width="25%"><font size="2" face="Verdana"></font></td>
        </tr>
        <tr>
            <td align="right" width="25%"><font size="2" face="Verdana">Project 1: </font></td>
            <td width="25%">
                <select class="dropdown form-controls" name="prj1" >
                    <option value="">--Select--</option>
                    <?php foreach ($prjData as $projects) { ?>
                        <option value="<?php echo $projects[0]; ?>" <?php echo ($updateResult[9] == $projects[0]) ? "selected" : ""; ?>><?php echo $projects[0]; ?></option>
                    <?php } ?>
                </select>
            </td>
            <td align="right" width="25%"><font size="2" face="Verdana">Project 1 Hours: </font></td>
            <td width="25%"><input type="text" name="project1hrs" class="form-control number-field" value="<?php echo $updateResult[14]; ?>" onchange="calculateHours()"/></td>
        </tr>
        <?php if ($_GET['iddata']) { 
            list($hoursprj1, $minutesprj1) = explode(':', $updateResult[14]);
            $totalMinutesprj1 = ($hoursprj1 * 60) + $minutesprj1;
            $decimalHoursprj1 = $totalMinutesprj1 / 60;
            ?>
            <tr>
                <td align="right" width="25%"><font size="2" face="Verdana">Project 1 Hours Amount: </font></td>
                <?php if (isset($_GET['parent'])) { 
                    $project1amount = isset($updateResult[15]) ? $updateResult[15] : $decimalHoursprj1 * $regularDayAmount;
                    $whole_part1 = floor($project1amount);
                    $decimal_part1 = $project1amount - $whole_part1;
                    if ($decimal_part1 <= 0.5) {  
                        $final_amount1 = $whole_part1;  
                    } else {  
                        $final_amount1 = $whole_part1 + 1;  
                    }
                    ?>
                    <td width="25%"><input type="text" name="project1hrsamt" class="form-control" value="<?php echo $final_amount1; ?>" /></td>
                <?php
                } else {
                    if ($isSunday || $publicRaw > 0) {
                        $project1amount = isset($updateResult[15]) ? $updateResult[15] : $decimalHoursprj1 * $updateResult[34];
                        $whole_part1 = floor($project1amount);
                        $decimal_part1 = $project1amount - $whole_part1;
                        if ($decimal_part1 <= 0.5) {  
                            $final_amount1 = $whole_part1;  
                        } else {  
                            $final_amount1 = $whole_part1 + 1;  
                        }
                        ?>
                        <td width="25%"><input type="text" name="project1hrsamt" class="form-control" value="<?php echo $final_amount1; ?>" style="<?php echo $color; ?>"/></td>    
                    <?php } else { 
                        $project1amount = isset($updateResult[15]) ? $updateResult[15] : $decimalHoursprj1 * $updateResult[35];
                        $whole_part1 = floor($project1amount);
                        $decimal_part1 = $project1amount - $whole_part1;
                        if ($decimal_part1 <= 0.5) {  
                            $final_amount1 = $whole_part1;  
                        } else {  
                            $final_amount1 = $whole_part1 + 1;  
                        }
                        ?>
                        <td width="25%"><input type="text" name="project1hrsamt" class="form-control" value="<?php echo $final_amount1; ?>" /></td>
                    <?php }
                }
                ?>
                <td colspan="2"></td>
            </tr>
<?php } ?>
        <tr>
            <td align="right" width="25%"><font size="2" face="Verdana">Project 2: </font></td>
            <td width="25%">
                <select class="dropdown form-controls" name="prj2" >
                    <option value="">--Select--</option>
                    <?php foreach ($prjData as $projects) { ?>
                        <option value="<?php echo $projects[0]; ?>" <?php echo ($updateResult[10] == $projects[0]) ? "selected" : ""; ?>><?php echo $projects[0]; ?></option>
<?php } ?>
                </select>
            </td>
            <td align="right" width="25%"><font size="2" face="Verdana">Project 2 Hours: </font></td>
            <td width="25%"><input type="text" name="project2hrs" class="form-control number-field" value="<?php echo $updateResult[16]; ?>" onchange="calculateHours()"/></td>
        </tr>
            <?php if ($_GET['iddata']) { 
            list($hoursprj2, $minutesprj2) = explode(':', $updateResult[16]);
            $totalMinutesprj2 = ($hoursprj2 * 60) + $minutesprj2;
            $decimalHoursprj2 = $totalMinutesprj2 / 60;    
            ?>
            <tr>
                <td align="right" width="25%"><font size="2" face="Verdana">Project 2 Hours Amount: </font></td>
                <?php if ($_GET['parent']) { 
                    $project2amount = isset($updateResult[17]) ? $updateResult[17] : $decimalHoursprj2 * $regularDayAmount;
                    $whole_part2 = floor($project2amount);
                    $decimal_part2 = $project2amount - $whole_part2;
                    if ($decimal_part2 <= 0.5) {  
                        $final_amount2 = $whole_part2;  
                    } else {  
                        $final_amount2 = $whole_part2 + 1;  
                    }
                    ?>
                    <td width="25%"><input type="text" name="project2hrsamt" class="form-control" value="<?php echo $final_amount2; ?>" /></td>
                <?php
                } else {
                    if ($isSunday || $publicRaw > 0) {
                        $project2amount = isset($updateResult[17]) ? $updateResult[17] : $decimalHoursprj2 * $updateResult[34];
                        $whole_part2 = floor($project2amount);
                        $decimal_part2 = $project2amount - $whole_part2;
                        if ($decimal_part2 <= 0.5) {  
                            $final_amount2 = $whole_part2;  
                        } else {  
                            $final_amount2 = $whole_part2 + 1;  
                        }
                        ?>
                        <td width="25%"><input type="text" name="project2hrsamt" class="form-control" value="<?php echo $final_amount2; ?>"  style="<?php echo $color; ?>"/></td>        
                    <?php } else { 
                        $project2amount = isset($updateResult[17]) ? $updateResult[17] : $decimalHoursprj2 * $updateResult[35];
                        $whole_part2 = floor($project2amount);
                        $decimal_part2 = $project2amount - $whole_part2;
                        if ($decimal_part2 <= 0.5) {  
                            $final_amount2 = $whole_part2;  
                        } else {  
                            $final_amount2 = $whole_part2 + 1;  
                        }
                        ?>
                        <td width="25%"><input type="text" name="project2hrsamt" class="form-control" value="<?php echo $final_amount2; ?>" /></td>    
                <?php }
            }
            ?>
                <td colspan="2"></td>
            </tr>
<?php } ?>
        <tr>
            <td align="right" width="25%"><font size="2" face="Verdana">Project 3: </font></td>
            <td width="25%">
                <select class="dropdown form-controls" name="prj3" >
                    <option value="">--Select--</option>
<?php foreach ($prjData as $projects) { ?>
                        <option value="<?php echo $projects[0]; ?>" <?php echo ($updateResult[11] == $projects[0]) ? "selected" : ""; ?>><?php echo $projects[0]; ?></option>
<?php } ?>
                </select>
            </td>
            <td align="right" width="25%"><font size="2" face="Verdana">Project 3 Hours: </font></td>
            <td width="25%"><input type="text" name="project3hrs" class="form-control number-field" value="<?php echo $updateResult[18]; ?>" onchange="calculateHours()"/></td>
        </tr>
            <?php if ($_GET['iddata']) { 
                list($hoursprj3, $minutesprj3) = explode(':', $updateResult[18]);
                $totalMinutesprj3 = ($hoursprj3 * 60) + $minutesprj3;
                $decimalHoursprj3 = $totalMinutesprj3 / 60;  
                ?>
            <tr>
                <td align="right" width="25%"><font size="2" face="Verdana">Project 3 Hours Amount: </font></td>
                <?php if (isset($_GET['parent'])) { 
                    $project3amount = (isset($updateResult[19])) ? $updateResult[19] : $decimalHoursprj3 * $regularDayAmount;
                    $whole_part3 = floor($project3amount);
                        $decimal_part3 = $project3amount - $whole_part3;
                        if ($decimal_part3 <= 0.5) {  
                            $final_amount3 = $whole_part3;  
                        } else {  
                            $final_amount3 = $whole_part3 + 1;  
                        }
                    ?>
                    <td width="25%"><input type="text" name="project3hrsamt" class="form-control" value="<?php echo $final_amount3; ?>" /></td>
                <?php
                } else {
                    if ($isSunday || $publicRaw > 0) {
                        $project3amount = isset($updateResult[19]) ? $updateResult[19] : $decimalHoursprj3 * $updateResult[34];
                        $whole_part3 = floor($project3amount);
                        $decimal_part3 = $project3amount - $whole_part3;
                        if ($decimal_part3 <= 0.5) {  
                            $final_amount3 = $whole_part3;  
                        } else {  
                            $final_amount3 = $whole_part3 + 1;  
                        }
                    ?>
                        <td width="25%"><input type="text" name="project3hrsamt" class="form-control" value="<?php echo $final_amount3; ?>"  style="<?php echo $color; ?>"/></td>
                <?php } else { 
                    $project3amount = isset($updateResult[19]) ? $updateResult[19] : $decimalHoursprj3 * $updateResult[35];
                        $whole_part3 = floor($project3amount);
                        $decimal_part3 = $project3amount - $whole_part3;
                        if ($decimal_part3 <= 0.5) {  
                            $final_amount3 = $whole_part3;  
                        } else {  
                            $final_amount3 = $whole_part3 + 1;  
                        }
                    ?>
                        <td width="25%"><input type="text" name="project3hrsamt" class="form-control" value="<?php echo $final_amount3; ?>" /></td>
        <?php }
    }
    ?>
                <td colspan="2"></td>
            </tr>
                    <?php } ?>
        <tr>
            <td align="right" width="25%"><font size="2" face="Verdana">Project 4: </font></td>
            <td width="25%">
                <select class="dropdown form-controls" name="prj4" >
                    <option value="">--Select--</option>
<?php foreach ($prjData as $projects) { ?>
                        <option value="<?php echo $projects[0]; ?>" <?php echo ($updateResult[12] == $projects[0]) ? "selected" : ""; ?>><?php echo $projects[0]; ?></option>
        <?php } ?>
                </select>
            </td>
            <td align="right" width="25%"><font size="2" face="Verdana">Project 4 Hours: </font></td>
            <td width="25%"><input type="text" name="project4hrs" class="form-control number-field" value="<?php echo $updateResult[20]; ?>" onchange="calculateHours()"/></td>
        </tr>
            <?php if ($_GET['iddata']) { 
                list($hoursprj4, $minutesprj4) = explode(':', $updateResult[20]);
                $totalMinutesprj4 = ($hoursprj4 * 60) + $minutesprj4;
                $decimalHoursprj4 = $totalMinutesprj4 / 60;
                ?>
            <tr>
                <td align="right" width="25%"><font size="2" face="Verdana">Project 4 Hours Amount: </font></td>
                <?php if (isset($_GET['parent'])) { 
                    $project4amount = isset($updateResult[21]) ? $updateResult[21] : $decimalHoursprj4 * $regularDayAmount;
                    $whole_part4 = floor($project4amount);
                    $decimal_part4 = $project4amount - $whole_part4;
                    if ($decimal_part4 <= 0.5) {  
                        $final_amount4 = $whole_part4;  
                    } else {  
                        $final_amount4 = $whole_part4 + 1;  
                    }
                    ?>
                    <td width="25%"><input type="text" name="project4hrsamt" class="form-control" value="<?php echo $final_amount4; ?>" /></td>
                <?php
                } else {
                    if ($isSunday || $publicRaw > 0) {
                        $project4amount = isset($updateResult[21]) ? $updateResult[21] : $decimalHoursprj4 * $updateResult[34];
                        $whole_part4 = floor($project4amount);
                        $decimal_part4 = $project4amount - $whole_part4;
                        if ($decimal_part4 <= 0.5) {  
                            $final_amount4 = $whole_part4;  
                        } else {  
                            $final_amount4 = $whole_part4 + 1;  
                        }
                        ?>
                        <td width="25%"><input type="text" name="project4hrsamt" class="form-control" value="<?php echo $final_amount4; ?>"  style="<?php echo $color; ?>"/></td>    
        <?php } else { 
                        $project4amount = isset($updateResult[21]) ? $updateResult[21] : $decimalHoursprj4 * $updateResult[35];
                        $whole_part4 = floor($project4amount);
                        $decimal_part4 = $project4amount - $whole_part4;
                        if ($decimal_part4 <= 0.5) {  
                            $final_amount4 = $whole_part4;  
                        } else {  
                            $final_amount4 = $whole_part4 + 1;  
                        }
            ?>
                        <td width="25%"><input type="text" name="project4hrsamt" class="form-control" value="<?php echo $final_amount4; ?>" /></td>
        <?php }
    }
    ?>
                <td colspan="2"></td>
            </tr>
                    <?php } ?>
        <tr>
            <td align="right" width="25%"><font size="2" face="Verdana">Project 5: </font></td>
            <td width="25%">
                <select class="dropdown form-controls" name="prj5" >
                    <option value="">--Select--</option>
        <?php foreach ($prjData as $projects) { ?>
                        <option value="<?php echo $projects[0]; ?>" <?php echo ($updateResult[13] == $projects[0]) ? "selected" : ""; ?>><?php echo $projects[0]; ?></option>
            <?php } ?>
                </select>
            </td>
            <td align="right" width="25%"><font size="2" face="Verdana">Project 5 Hours: </font></td>
            <td width="25%"><input type="text" name="project5hrs" class="form-control number-field" value="<?php echo $updateResult[22]; ?>" onchange="calculateHours()"/></td>
        </tr>
            <?php if ($_GET['iddata']) { 
                list($hoursprj5, $minutesprj5) = explode(':', $updateResult[22]);
                $totalMinutesprj5 = ($hoursprj5 * 60) + $minutesprj5;
                $decimalHoursprj5 = $totalMinutesprj5 / 60;
                ?>
            <tr>
                <td align="right" width="25%"><font size="2" face="Verdana">Project 5 Hours Amount: </font></td>
    <?php if (isset($_GET['parent'])) { 
                $project5amount = (isset($updateResult[23])) ? $updateResult[23] : $decimalHoursprj5 * $regularDayAmount;
                $whole_part5 = floor($project5amount);
                $decimal_part5 = $project5amount - $whole_part5;
                if ($decimal_part5 <= 0.5) {  
                    $final_amount5 = $whole_part5;  
                } else {  
                    $final_amount5 = $whole_part5 + 1;  
                }
        ?>
                    <td width="25%"><input type="text" name="project5hrsamt" class="form-control" value="<?php  ?>" /></td>
            <?php } else {
                if ($isSunday || $publicRaw > 0) {
                    $project5amount = (isset($updateResult[23])) ? $updateResult[23] : $decimalHoursprj5 * $updateResult[34];
                    $whole_part5 = floor($project5amount);
                    $decimal_part5 = $project5amount - $whole_part5;
                    if ($decimal_part5 <= 0.5) {  
                        $final_amount5 = $whole_part5;  
                    } else {  
                        $final_amount5 = $whole_part5 + 1;  
                    }
                    ?>
                        <td width="25%"><input type="text" name="project5hrsamt" class="form-control" value="<?php echo $final_amount5; ?>"  style="<?php echo $color; ?>"/></td>
        <?php } else { 
                    $project5amount = (isset($updateResult[23])) ? $updateResult[23] : $decimalHoursprj5 * $updateResult[35];
                    $whole_part5 = floor($project5amount);
                    $decimal_part5 = $project5amount - $whole_part5;
                    if ($decimal_part5 <= 0.5) {  
                        $final_amount5 = $whole_part5;  
                    } else {  
                        $final_amount5 = $whole_part5 + 1;  
                    }
            ?>
                        <td width="25%"><input type="text" name="project5hrsamt" class="form-control" value="<?php echo $final_amount5; ?>" /></td>
        <?php }
    } ?>
                <td colspan="2"></td>
            </tr>
            <?php } ?>
        <tr>
            <td align="right" width="25%"><font size="2" face="Verdana">Total Project Hours: </font></td>
            <td width="25%"><input type="text" name="totalprjhrs" id="totalprjhrs" class="form-control" value="<?php echo $updateResult[24]; ?>" readonly="readonly"/></td>
            <td align="right" width="25%"><font size="2" face="Verdana">Unpaid Hours: </font></td>
            <td width="25%"><input type="text" name="transithrs" id="transithrs" class="form-control" value="<?php echo $updateResult[25]; ?>" readonly="readonly"/></td>
        </tr>
        <tr>
            <?php if ($_GET['iddata']) { ?>
                <td align="right" width="25%"><font size="2" face="Verdana">Additional Amount: </font></td>
                <?php if (isset($_GET['parent'])) { ?>
                    <td width="25%"><input type="text" name="otamount" class="form-control" value="<?php echo (isset($updateResult[29])) ? $updateResult[29] : $regularAmtSuperVisor; ?>" /></td>                    
                <?php }else{ 
                     if ($isSunday || $publicRaw > 0) { ?>
                    <td width="25%"><input type="text" name="otamount" class="form-control" value="<?php echo (isset($updateResult[29])) ? $updateResult[29] : $overtimehours * $updateResult[34]; ?>" /></td>                         
                     <?php }else{ ?>
                    <td width="25%"><input type="text" name="otamount" class="form-control" value="<?php echo (isset($updateResult[29])) ? $updateResult[29] : $overtimehours * $updateResult[35]; ?>" /></td>                    
                <?php } } ?>
                
<?php } ?>
            <td align="right" width="25%"><font size="2" face="Verdana">Remark: </font></td>
            <td width="25%"><input type="text" name="remark" class="form-control" value="<?php echo $updateResult[26]; ?>" /></td>
        </tr>
        <tr>
<?php if (isset($_GET['parent'])) { ?>
                <td align="right" width="25%"><font size="2" face="Verdana">Regular Day Rate: </font></td>
                <td width="25%"><input type="text" name="regulardayrate" class="form-control" value="<?php echo (isset($updateResult[30])) ? $updateResult[30] : $updateResult[37]; ?>" /></td>
<?php } ?>
            <td align="right" width="25%"></font></td>
            <td width="75%" colspan="3"><font size="2" face="Verdana"><input type="submit" name="update" value="Update Record" class="btn btn-primary"/></td>
        </tr>
        </tbody>
    </table>
</form>
<script src="resource/js/jquery.min.js"></script>
<script>
                $(document).ready(function () {  
                    calculateHours();   
                    $(".number-field").on('input', calculateHours);  
                    $("#totalhrwork").on('input', calculateHours);   
                });  

                function parseTimeToMinutes(time) {  
                    const parts = time.split(':');  
                    const hours = parseInt(parts[0]) || 0;   
                    const minutes = parseInt(parts[1]) || 0;   
                    return hours * 60 + minutes; // Convert to total minutes  
                }  

                function convertMinutesToTimeFormat(totalMinutes) {  
                    const hours = Math.floor(totalMinutes / 60);  
                    const minutes = totalMinutes % 60;  
                    return hours + ':' + (minutes < 10 ? '0' : '') + minutes; // Format as H:MM  
                }  

                function calculateHours() {  
                    const totalHours = parseTimeToMinutes(document.getElementById("totalhrwork").value) || 0;   
                    const projectHoursFields = document.querySelectorAll(".number-field");  
                    const totalProjectHoursField = document.getElementById("totalprjhrs");  
                    const transitHoursField = document.getElementById("transithrs");  

                    let projectHours = 0;  
                    for (let i = 0; i < projectHoursFields.length; i++) {  
                        const timeValue = projectHoursFields[i].value;  
                        const minutes = parseTimeToMinutes(timeValue);  
                        projectHours += minutes;   
                    }  

                    const transitMinutes = totalHours - projectHours;  // Calculate transit minutes  

                    // Ensure the time does not go negative  
                    if (transitMinutes < 0) {  
                        $(totalProjectHoursField).css('background-color', 'red');  
                        transitHoursField.value = '00:00';   
                    } else {  
                        transitHoursField.value = convertMinutesToTimeFormat(transitMinutes);  
                        $(totalProjectHoursField).css('background-color', 'white');  
                    }  

                    totalProjectHoursField.value = convertMinutesToTimeFormat(projectHours);  
                }  
</script>
<?php
print "</center>";
?>

