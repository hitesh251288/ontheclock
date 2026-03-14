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

if (isset($_POST['searchValue']) && !isset($_POST['flag'])) { 
//    echo "<pre>";print_R($_POST);
    $fromDate = date('Ymd', strtotime($_POST['fromDate']));
    $toDate = date('Ymd', strtotime($_POST['toDate']));
    
    $searchValue = $_POST['searchValue'];
    if($_POST['fromDate'] != '' && $_POST['toDate'] !=''){
        $dateQuery = "p.empdate >= '$fromDate' AND p.empdate <= '$toDate' AND ";
    }
    $prjChildEmpQuery = "select DISTINCT p.*, o.grade, o.hrrate, h.ot_holiday,h.regrate, h.ot_regular, h.regdayrate  from projecthrsallocation p LEFT JOIN "
            . "onboardrequest o ON o.fullname=p.empname LEFT JOIN hourlywagescasual h ON h.grades=o.grade where $dateQuery p.parentempcode =" . $searchValue ." "
            . "AND p.totalprjhrs!='' AND p.empcode !=".$searchValue;
    $prjChildEmpResult = mysqli_query($conn, $prjChildEmpQuery);
    $prjChildEmpRow = mysqli_fetch_all($prjChildEmpResult);
    $project1totalhrs = 0;
    $project2totalhrs = 0;
    $project3totalhrs = 0;
    $project4totalhrs = 0;
    $project5totalhrs = 0;
    $project1totalamt = 0;
    $project2totalamt = 0;
    $project3totalamt = 0;
    $project4totalamt = 0;
    $project5totalamt = 0;
    $totalprojecthrs = 0;
    $totalunpaidhrs = 0;
    $totaladditionalhrs = 0;
    $totaladditionalamt = 0;
    $totalregularamt = 0;
    $totalMinutes = 0; 
    $totalMinutes1 = 0; 
    $totalMinutes2 = 0; 
    $totalMinutes3 = 0; 
    $totalMinutes4 = 0; 
    $totalMinutes5 = 0; 
    $totalMinutestrn = 0;
    foreach ($prjChildEmpRow as $prjChildEmpAll) { 
//        echo "<pre>";print_R($prjChildEmpAll);
        $fromTime = $prjChildEmpAll[6];
        $toTime = $prjChildEmpAll[7];
        if($prjChildEmpAll[8] > 8){ 
            $sub = $prjChildEmpAll[8]-8;
            $otHours = $sub*$prjChildEmpAll[31];
        } 
        $publicDate = date('Ymd', strtotime($prjChildEmpAll[3]));
        $publicQuery = "SELECT OTDate from otdate where OTDate=$publicDate";
        $publicResult = mysqli_query($conn, $publicQuery);
//        $publicRaw = mysqli_fetch_all($publicResult);
        $publicRaw = mysqli_num_rows($publicResult);
        $isSunday = date("w", strtotime($prjChildEmpAll[3])) == 0;
        $isChecked = ($prjChildEmpAll[31] == 1) ? 'checked'.' '.'disabled' : '';
        if ($isSunday || $publicRaw > 0) {
            $color = "background-color:red";
        } else {
            $color = "";
        }
        $overtimeHours = $prjChildEmpAll[8] - 8;
        if($overtimeHours < 0){
            $overtimeHours = "";
        }
        echo $fetchappQuery = "SELECT * from projectapproval where empdate='$prjChildEmpAll[3]' AND parent_id=$prjChildEmpAll[1] AND child_id=$prjChildEmpAll[4]";
        $fetchappResult = mysqli_query($conn, $fetchappQuery);
        $fetchAppRow = mysqli_fetch_all($fetchappResult);
//        echo "<pre>";print_R($fetchAppRow);
        ?>
        <tr>
            <td style="<?php echo $color; ?>"><font face="Verdana" size="1"><?php echo $prjChildEmpAll[3]; ?></font></td>
            <td><font face="Verdana" size="1"><?php echo $prjChildEmpAll[1]; ?></font></td>
            <td><font face="Verdana" size="1"><?php echo $prjChildEmpAll[2]; ?></font></td>
            <td><font face="Verdana" size="1"><?php echo $prjChildEmpAll[4]; ?></font></td>
            <td><font face="Verdana" size="1"><?php echo $prjChildEmpAll[5]; ?></font></td>
            <td><font face="Verdana" size="1"><?php echo $prjChildEmpAll[32]; ?></font></td>
            <td><font face="Verdana" size="1"><?php echo date('H:i:s', strtotime($prjChildEmpAll[6])); ?></font></td>
            <td><font face="Verdana" size="1"><?php echo date('H:i:s', strtotime($prjChildEmpAll[7])); ?></font></td>
            <td><font face="Verdana" size="1"><?php echo $prjChildEmpAll[8]; ?></font></td>
            <td><font face="Verdana" size="1">
                <?php 
                    if($fetchAppRow[0][1] == $prjChildEmpAll[3] && $fetchAppRow[0][2]==$prjChildEmpAll[1] && $fetchAppRow[0][3] == $prjChildEmpAll[4] && $fetchAppRow[0][5] == $prjChildEmpAll[9]){
                        echo $fetchAppRow[0][4];
                    }else{ ?>
                <!--<input type="checkbox" class="project-checkbox" name="project1[]" value="<?php echo $prjChildEmpAll[3].','.$prjChildEmpAll[1].','.$prjChildEmpAll[9].','.$prjChildEmpAll[4]; ?>" data-parent-id="<?php echo $prjChildEmpAll[1]; ?>" data-child-id="<?php echo $prjChildEmpAll[4]; ?>" data-emp-date="<?php echo $prjChildEmpAll[3]; ?>" data-project-name="<?php echo $prjChildEmpAll[9]; ?>" />-->
                <?php } ?>
                </font></td>
            <td><font face="Verdana" size="1">
                <?php 
                    if($fetchAppRow[0][1] == $prjChildEmpAll[3] && $fetchAppRow[0][2]==$prjChildEmpAll[1] && $fetchAppRow[0][3] == $prjChildEmpAll[4] && $fetchAppRow[0][5] == $prjChildEmpAll[9] && $fetchAppRow[0][15]!=''){
                        echo $fetchAppRow[0][15];
                    }else{ 
                        if($prjChildEmpAll[9] !='' && $fetchAppRow[0][4]!=''){ ?>
                <input type="checkbox" class="project-checkbox" name="project1[]" value="<?php echo $prjChildEmpAll[3].','.$prjChildEmpAll[1].','.$prjChildEmpAll[9].','.$prjChildEmpAll[4]; ?>" data-parent-id="<?php echo $prjChildEmpAll[1]; ?>" data-child-id="<?php echo $prjChildEmpAll[4]; ?>" data-emp-date="<?php echo $prjChildEmpAll[3]; ?>" data-project-name="<?php echo $prjChildEmpAll[9]; ?>" /></font></td>
                        <?php  } } ?>                
            <td><font face="Verdana" size="1"><?php echo $prjChildEmpAll[9]; ?></font></td>
            <td><font face="Verdana" size="1"><?php 
            echo $prjChildEmpAll[14]; 
            $project1totalhrs =$prjChildEmpAll[14]; 
            list($hours1, $minutes1) = explode(':', $project1totalhrs);
            $totalMinutes1 += ($hours1 * 60) + $minutes1;
            ?></font></td>
            <td><font face="Verdana" size="1"><?php 
            $project1hoursAmt = $prjChildEmpAll[14];
            list($hoursprj1, $minutesprj1) = explode(':', $project1hoursAmt);
            $totalMinutesprj1 = ($hoursprj1 * 60) + $minutesprj1;
            $decimalHoursprj1 = $totalMinutesprj1 / 60;            
            if($isSunday || $publicRaw > 0){ 
                $project1amount = ($prjChildEmpAll[15]) ? $prjChildEmpAll[15] : $decimalHoursprj1 * $prjChildEmpAll[34];
                $whole_part1 = floor($project1amount);
                $decimal_part1 = $project1amount - $whole_part1;
                if ($decimal_part1 <= 0.5) {  
                    $final_amount1 = $whole_part1;  
                } else {  
                    $final_amount1 = $whole_part1 + 1;  
                } 
                echo $final_amount1;
                if($prjChildEmpAll[9] !=''){
                    echo "<input type='hidden' name='project1amt[]' value='{$prjChildEmpAll[3]},{$prjChildEmpAll[4]},{$final_amount1},{$prjChildEmpAll[1]}'>";
                }
                
                $prj1amounttotal = ($prjChildEmpAll[15]) ? $prjChildEmpAll[15] : $prjChildEmpAll[14]*$prjChildEmpAll[34]; 
                $project1totalamt +=$final_amount1;
            }else{
                $empdt = $prjChildEmpAll[3];
                $empcd = $prjChildEmpAll[4];
                $project1amount = ($prjChildEmpAll[15]) ? $prjChildEmpAll[15] : $decimalHoursprj1 * $prjChildEmpAll[35]; 
                $whole_part1 = floor($project1amount);
                $decimal_part1 = $project1amount - $whole_part1;
                if ($decimal_part1 <= 0.5) {  
                    $final_amount1 = $whole_part1;  
                } else {  
                    $final_amount1 = $whole_part1 + 1;  
                } 
                echo $final_amount1;
                if($prjChildEmpAll[9] !=''){
                    echo "<input type='hidden' name='project1amt[]' value='{$prjChildEmpAll[3]},{$prjChildEmpAll[4]},{$final_amount1},{$prjChildEmpAll[1]}'>";
                }
                $prj1amounttotal = ($prjChildEmpAll[15]) ? $prjChildEmpAll[15] : $prjChildEmpAll[14]*$prjChildEmpAll[35]; 
                $project1totalamt +=$final_amount1;
            }
            ?></font></td>
            <td><font face="Verdana" size="1">
                <?php 
                    if($fetchAppRow[0][1] == $prjChildEmpAll[3] && $fetchAppRow[0][2]==$prjChildEmpAll[1] && $fetchAppRow[0][3] == $prjChildEmpAll[4] && $fetchAppRow[0][7] == $prjChildEmpAll[10]){
                        echo $fetchAppRow[0][6];
                    }else{ ?>
                <!--<input type="checkbox" class="project-checkbox" name="project2[]" value="<?php //echo $prjChildEmpAll[3].','.$prjChildEmpAll[1].','.$prjChildEmpAll[10].','.$prjChildEmpAll[4]; ?>" data-parent-id="<?php echo $prjChildEmpAll[1]; ?>" data-child-id="<?php //echo $prjChildEmpAll[4]; ?>" data-emp-date="<?php //echo $prjChildEmpAll[3]; ?>" <?php //echo $isChecked; ?> />-->
                <?php } ?>
                </font></td>
            <td><font face="Verdana" size="1">
                <?php 
                    if($fetchAppRow[0][1] == $prjChildEmpAll[3] && $fetchAppRow[0][2]==$prjChildEmpAll[1] && $fetchAppRow[0][3] == $prjChildEmpAll[4] && $fetchAppRow[0][7] == $prjChildEmpAll[10] && $fetchAppRow[0][16]!=''){
                        echo $fetchAppRow[0][16];
                    }else{
                        if($prjChildEmpAll[10] !='' && $fetchAppRow[0][6]!=''){
                        ?>
                <input type="checkbox" class="project-checkbox" name="project2[]" value="<?php echo $prjChildEmpAll[3].','.$prjChildEmpAll[1].','.$prjChildEmpAll[10].','.$prjChildEmpAll[4]; ?>"  <?php //echo $isChecked; ?> /></font></td>
                    <?php } } ?>                
            <td><font face="Verdana" size="1"><?php echo $prjChildEmpAll[10]; ?></font></td>
            <td><font face="Verdana" size="1"><?php 
            echo $prjChildEmpAll[16]; 
            $project2totalhrs =$prjChildEmpAll[16]; 
            list($hours2, $minutes2) = explode(':', $project2totalhrs);
            $totalMinutes2 += ($hours2 * 60) + $minutes2;
            ?></font></td>
            <td><font face="Verdana" size="1"><?php 
            list($hoursprj2, $minutesprj2) = explode(':', $prjChildEmpAll[16]);
            $totalMinutesprj2 = ($hoursprj2 * 60) + $minutesprj2;
            $decimalHoursprj2 = $totalMinutesprj2 / 60;
            if($isSunday || $publicRaw > 0){ 
                $project2amount = ($prjChildEmpAll[17]) ? $prjChildEmpAll[17] : $decimalHoursprj2*$prjChildEmpAll[34]; 
                $whole_part2 = floor($project2amount);
                $decimal_part2 = $project2amount - $whole_part2;
                if ($decimal_part2 <= 0.5) {  
                    $final_amount2 = $whole_part2;  
                } else {  
                    $final_amount2 = $whole_part2 + 1;  
                } 
                echo $final_amount2;
                if($prjChildEmpAll[10] !=''){
                    echo "<input type='hidden' name='project2amt[]' value='{$prjChildEmpAll[3]},{$prjChildEmpAll[4]},{$final_amount2},{$prjChildEmpAll[1]}'>";
                }
                $project2amounttotal = ($prjChildEmpAll[17]) ? $prjChildEmpAll[17] : $prjChildEmpAll[16]*$prjChildEmpAll[34]; 
                $project2totalamt += $final_amount2;
            }else{
                $project2amount = ($prjChildEmpAll[17]) ? $prjChildEmpAll[17] : $decimalHoursprj2*$prjChildEmpAll[35]; 
                $whole_part2 = floor($project2amount);
                $decimal_part2 = $project2amount - $whole_part2;
                if ($decimal_part2 <= 0.5) {  
                    $final_amount2 = $whole_part2;  
                } else {  
                    $final_amount2 = $whole_part2 + 1;  
                } 
                echo $final_amount2;
                if($prjChildEmpAll[10] !=''){
                    echo "<input type='hidden' name='project2amt[]' value='{$prjChildEmpAll[3]},{$prjChildEmpAll[4]},{$final_amount2},{$prjChildEmpAll[1]}'>";
                }
                $project2amounttotal = ($prjChildEmpAll[17]) ? $prjChildEmpAll[17] : $prjChildEmpAll[16]*$prjChildEmpAll[35];
                $project2totalamt += $final_amount2;
            }
            ?></font></td>
            <td><font face="Verdana" size="1">
                <?php 
                    if($fetchAppRow[0][1] == $prjChildEmpAll[3] && $fetchAppRow[0][2]==$prjChildEmpAll[1] && $fetchAppRow[0][3] == $prjChildEmpAll[4] && $fetchAppRow[0][9] == $prjChildEmpAll[11]){
                        echo $fetchAppRow[0][8];
                    }else{ ?>
                <!--<input type="checkbox" class="project-checkbox" name="project3[]" value="<?php //echo $prjChildEmpAll[3].','.$prjChildEmpAll[1].','.$prjChildEmpAll[11].','.$prjChildEmpAll[4]; ?>" data-parent-id="<?php //echo $prjChildEmpAll[1]; ?>" data-child-id="<?php //echo $prjChildEmpAll[4]; ?>" data-emp-date="<?php echo $prjChildEmpAll[3]; ?>" <?php //echo $isChecked; ?> />-->
                <?php } ?>
                </font></td>
            <td><font face="Verdana" size="1">
                <?php 
                if($fetchAppRow[0][1] == $prjChildEmpAll[3] && $fetchAppRow[0][2]==$prjChildEmpAll[1] && $fetchAppRow[0][3] == $prjChildEmpAll[4] && $fetchAppRow[0][7] == $prjChildEmpAll[10] && $fetchAppRow[0][17]!=''){
                    echo $fetchAppRow[0][17];
                }else{
                    if($prjChildEmpAll[11] !='' && $fetchAppRow[0][8]!=''){
                    ?>
                <input type="checkbox" class="project-checkbox" name="project3[]" value="<?php echo $prjChildEmpAll[3].','.$prjChildEmpAll[1].','.$prjChildEmpAll[11].','.$prjChildEmpAll[4]; ?>"  <?php //echo $isChecked; ?> /></font></td>
                    <?php } } ?>
            <td><font face="Verdana" size="1"><?php echo $prjChildEmpAll[11]; ?></font></td>
            <td><font face="Verdana" size="1"><?php 
            echo $prjChildEmpAll[18]; 
            $project3totalhrs = $prjChildEmpAll[18]; 
            list($hours3, $minutes3) = explode(':', $project3totalhrs);
            $totalMinutes3 += ($hours3 * 60) + $minutes3;
            ?></font></td>
            <td><font face="Verdana" size="1"><?php 
            list($hoursprj3, $minutesprj3) = explode(':', $prjChildEmpAll[18]);
            $totalMinutesprj3 = ($hoursprj3 * 60) + $minutesprj3;
            $decimalHoursprj3 = $totalMinutesprj3 / 60;
            if($isSunday || $publicRaw > 0){ 
                $project3amount = ($prjChildEmpAll[19]) ? $prjChildEmpAll[19] : $decimalHoursprj3*$prjChildEmpAll[34]; 
                $whole_part = floor($project3amount);
                $decimal_part = $project3amount - $whole_part;
                if ($decimal_part <= 0.5) {  
                    $final_amount = $whole_part;  
                } else {  
                    $final_amount = $whole_part + 1;  
                } 
                echo $final_amount;
                if($prjChildEmpAll[11] !=''){
                    echo "<input type='hidden' name='project3amt[]' value='{$prjChildEmpAll[3]},{$prjChildEmpAll[4]},{$final_amount},{$prjChildEmpAll[1]}'>";
                }
                $project3amounttotal = ($prjChildEmpAll[19]) ? $prjChildEmpAll[19] : $prjChildEmpAll[18]*$prjChildEmpAll[34]; 
                $project3totalamt += $final_amount;
            }else{
                $project3amount = ($prjChildEmpAll[19]) ? $prjChildEmpAll[19] : $decimalHoursprj3 * $prjChildEmpAll[35]; 
                $whole_part = floor($project3amount);
                $decimal_part = $project3amount - $whole_part;
                if ($decimal_part <= 0.5) {  
                    $final_amount = $whole_part;  
                } else {  
                    $final_amount = $whole_part + 1;  
                } 
                echo $final_amount;
                if($prjChildEmpAll[11] !=''){
                    echo "<input type='hidden' name='project3amt[]' value='{$prjChildEmpAll[3]},{$prjChildEmpAll[4]},{$final_amount},{$prjChildEmpAll[1]}'>";
                }
                $project3amounttotal = ($prjChildEmpAll[19]) ? $prjChildEmpAll[19] : $prjChildEmpAll[18]*$prjChildEmpAll[35];
//                $project3totalamt += $project3amounttotal;
                $project3totalamt += $final_amount;
            }
            ?></font></td>
            <td><font face="Verdana" size="1">
                <?php 
                    if($fetchAppRow[0][1] == $prjChildEmpAll[3] && $fetchAppRow[0][2]==$prjChildEmpAll[1] && $fetchAppRow[0][3] == $prjChildEmpAll[4] && $fetchAppRow[0][11] == $prjChildEmpAll[12]){
                        echo $fetchAppRow[0][10];
                    }else{ ?>
                <!--<input type="checkbox" class="project-checkbox" name="project4[]" value="<?php //echo $prjChildEmpAll[3].','.$prjChildEmpAll[1].','.$prjChildEmpAll[12].','.$prjChildEmpAll[4]; ?>" data-parent-id="<?php //echo $prjChildEmpAll[1]; ?>" data-child-id="<?php //echo $prjChildEmpAll[4]; ?>" data-emp-date="<?php echo $prjChildEmpAll[3]; ?>" <?php echo $isChecked; ?> />-->
                <?php } ?>
                </font></td>
            <td><font face="Verdana" size="1">
                <?php 
                if($fetchAppRow[0][1] == $prjChildEmpAll[3] && $fetchAppRow[0][2]==$prjChildEmpAll[1] && $fetchAppRow[0][3] == $prjChildEmpAll[4] && $fetchAppRow[0][7] == $prjChildEmpAll[10] && $fetchAppRow[0][18]!=''){
                    echo $fetchAppRow[0][18];
                }else{
                    if($prjChildEmpAll[12] !='' && $fetchAppRow[0][10]!=''){
                    ?>
                <input type="checkbox" class="project-checkbox" name="project4[]" value="<?php echo $prjChildEmpAll[3].','.$prjChildEmpAll[1].','.$prjChildEmpAll[12].','.$prjChildEmpAll[4]; ?>" data-parent-id="<?php echo $prjChildEmpAll[1]; ?>" data-child-id="<?php echo $prjChildEmpAll[4]; ?>" data-emp-date="<?php echo $prjChildEmpAll[3]; ?>" <?php echo $isChecked; ?> /></font></td>
                    <?php } } ?>
            <td><font face="Verdana" size="1"><?php echo $prjChildEmpAll[12]; ?></font></td>
            <td><font face="Verdana" size="1"><?php 
                echo $prjChildEmpAll[20]; 
                $project4totalhrs =$prjChildEmpAll[20]; 
                list($hours4, $minutes4) = explode(':', $project4totalhrs);
                $totalMinutes4 += ($hours4 * 60) + $minutes4;
                ?></font></td>
            <td><font face="Verdana" size="1"><?php 
            list($hoursprj4, $minutesprj4) = explode(':', $prjChildEmpAll[20]);
            $totalMinutesprj4 = ($hoursprj4 * 60) + $minutesprj4;
            $decimalHoursprj4 = $totalMinutesprj4 / 60;
            if($isSunday || $publicRaw > 0){ 
                $project4amount = ($prjChildEmpAll[21]) ? $prjChildEmpAll[21] : $decimalHoursprj4*$prjChildEmpAll[34]; 
                $whole_part4 = floor($project4amount);
                $decimal_part4 = $project4amount - $whole_part4;
                if ($decimal_part4 <= 0.5) {  
                    $final_amount4 = $whole_part4;  
                } else {  
                    $final_amount4 = $whole_part4 + 1;  
                } 
                echo $final_amount4;
                if($prjChildEmpAll[12] !=''){
                    echo "<input type='hidden' name='project4amt[]' value='{$prjChildEmpAll[3]},{$prjChildEmpAll[4]},{$final_amount4},{$prjChildEmpAll[1]}'>";
                }
                $project4amounttotal = ($prjChildEmpAll[21]) ? $prjChildEmpAll[21] : $prjChildEmpAll[20]*$prjChildEmpAll[34]; 
                $project4totalamt += $final_amount4;
            }else{
                $project4amount = ($prjChildEmpAll[21]) ? $prjChildEmpAll[21] : $decimalHoursprj4*$prjChildEmpAll[35]; 
                $whole_part4 = floor($project4amount);
                $decimal_part4 = $project4amount - $whole_part4;
                if ($decimal_part4 <= 0.5) {  
                    $final_amount4 = $whole_part4;  
                } else {  
                    $final_amount4 = $whole_part4 + 1;  
                } 
                echo $final_amount4;
                if($prjChildEmpAll[12] !=''){
                    echo "<input type='hidden' name='project4amt[]' value='{$prjChildEmpAll[3]},{$prjChildEmpAll[4]},{$final_amount4},{$prjChildEmpAll[1]}'>";
                }
                $project4amounttotal = ($prjChildEmpAll[21]) ? $prjChildEmpAll[21] : $prjChildEmpAll[20]*$prjChildEmpAll[35]; 
                $project4totalamt += $final_amount4;
            }
            ?></font></td>
            <td><font face="Verdana" size="1">
                <?php 
                    if($fetchAppRow[0][1] == $prjChildEmpAll[3] && $fetchAppRow[0][2]==$prjChildEmpAll[1] && $fetchAppRow[0][3] == $prjChildEmpAll[4] && $fetchAppRow[0][13] == $prjChildEmpAll[13]){
                        echo $fetchAppRow[0][12];
                    }else{ ?>
                <!--<input type="checkbox" class="project-checkbox" name="project5[]" value="<?php //echo $prjChildEmpAll[3].','.$prjChildEmpAll[1].','.$prjChildEmpAll[13].','.$prjChildEmpAll[4]; ?>" data-parent-id="<?php //echo $prjChildEmpAll[1]; ?>" data-child-id="<?php //echo $prjChildEmpAll[4]; ?>" data-emp-date="<?php //echo $prjChildEmpAll[3]; ?>" <?php //echo $isChecked; ?> />-->
                <?php } ?>
                </font></td>
            <td><font face="Verdana" size="1">
                <?php 
                if($fetchAppRow[0][1] == $prjChildEmpAll[3] && $fetchAppRow[0][2]==$prjChildEmpAll[1] && $fetchAppRow[0][3] == $prjChildEmpAll[4] && $fetchAppRow[0][7] == $prjChildEmpAll[10] && $fetchAppRow[0][19]!=''){
                    echo $fetchAppRow[0][19];
                }else{ 
                    if($prjChildEmpAll[13] !='' && $fetchAppRow[0][12]!=''){
                    ?>
                <input type="checkbox" class="project-checkbox" name="project5[]" value="<?php echo $prjChildEmpAll[3].','.$prjChildEmpAll[1].','.$prjChildEmpAll[13].','.$prjChildEmpAll[4]; ?>" data-parent-id="<?php echo $prjChildEmpAll[1]; ?>" data-child-id="<?php echo $prjChildEmpAll[4]; ?>" data-emp-date="<?php echo $prjChildEmpAll[3]; ?>" <?php echo $isChecked; ?> /></font></td>
                    <?php } } ?>
            <td><font face="Verdana" size="1"><?php echo $prjChildEmpAll[13]; ?></font></td>
            <td><font face="Verdana" size="1"><?php 
                echo $prjChildEmpAll[22]; 
                $project5totalhrs =$prjChildEmpAll[22]; 
                list($hours5, $minutes5) = explode(':', $project5totalhrs);
                $totalMinutes5 += ($hours5 * 60) + $minutes5;
                ?></font></td>
            <td><font face="Verdana" size="1"><?php 
            list($hoursprj5, $minutesprj5) = explode(':', $prjChildEmpAll[22]);
            $totalMinutesprj5 = ($hoursprj5 * 60) + $minutesprj5;
            $decimalHoursprj5 = $totalMinutesprj5 / 60;
            if($isSunday || $publicRaw > 0){ 
                $project5amount = ($prjChildEmpAll[23]) ? $prjChildEmpAll[23] : $decimalHoursprj5*$prjChildEmpAll[34]; 
                $whole_part5 = floor($project5amount);
                $decimal_part5 = $project5amount - $whole_part5;
                if ($decimal_part5 <= 0.5) {  
                    $final_amount5 = $whole_part5;  
                } else {  
                    $final_amount5 = $whole_part5 + 1;  
                } 
                echo $final_amount5;
                if($prjChildEmpAll[13] !=''){
                    echo "<input type='hidden' name='project5amt[]' value='{$prjChildEmpAll[3]},{$prjChildEmpAll[4]},{$final_amount5},{$prjChildEmpAll[1]}'>";
                }
                $project5amounttotal = ($prjChildEmpAll[23]) ? $prjChildEmpAll[23] : $prjChildEmpAll[22]*$prjChildEmpAll[34]; 
                $project5totalamt += $final_amount5;
            }else{
                $project5amount = ($prjChildEmpAll[23]) ? $prjChildEmpAll[23] : $decimalHoursprj5*$prjChildEmpAll[35];  
                $whole_part5 = floor($project5amount);
                $decimal_part5 = $project5amount - $whole_part5;
                if ($decimal_part5 <= 0.5) {  
                    $final_amount5 = $whole_part5;  
                } else {  
                    $final_amount5 = $whole_part5 + 1;  
                } 
                echo $final_amount5;
                if($prjChildEmpAll[13] !=''){
                    echo "<input type='hidden' name='project5amt[]' value='{$prjChildEmpAll[3]},{$prjChildEmpAll[4]},{$final_amount5},{$prjChildEmpAll[1]}'>";
                }
                $project5amounttotal = ($prjChildEmpAll[23]) ? $prjChildEmpAll[23] : $prjChildEmpAll[22]*$prjChildEmpAll[35]; 
                $project5totalamt += $final_amount5;    
            }
            ?></font></td>
            <td><font face="Verdana" size="1"><?php 
                echo $prjChildEmpAll[24]; 
                $totalprojecthrs +=$prjChildEmpAll[24];
                list($hours, $minutes) = explode(':', $totalprojecthrs);
                $totalMinutes += ($hours * 60) + $minutes;
                ?></font></td>            
            <td><font face="Verdana" size="1"><?php 
                echo $prjChildEmpAll[25]; 
                $totalunpaidhrs +=$prjChildEmpAll[25]; 
                list($hourstrn, $minutestrn) = explode(':', $totalunpaidhrs);
                $totalMinutestrn += ($hourstrn * 60) + $minutestrn;
                ?></font></td>
            <td><font face="Verdana" size="1"><?php echo $overtimeHours; $totaladditionalhrs +=$overtimeHours; ?></font></td>
            <td><font face="Verdana" size="1">
                <?php
                if ($isSunday || $publicRaw > 0) {
                    echo ($prjChildEmpAll[29]) ? $prjChildEmpAll[29] : $overtimeHours * $prjChildEmpAll[34];
                    $totalovertimeamount = ($prjChildEmpAll[29]) ? $prjChildEmpAll[29] : $overtimeHours * $prjChildEmpAll[34];
                    $totaladditionalamt += $totalovertimeamount;
                } else {
                    echo ($prjChildEmpAll[29]) ? $prjChildEmpAll[29] : $overtimeHours * $prjChildEmpAll[35];
                    $totalovertimeamount = ($prjChildEmpAll[29]) ? $prjChildEmpAll[29] : $overtimeHours * $prjChildEmpAll[35];
                    $totaladditionalamt += $totalovertimeamount;
                }
                ?>
                </font></td>
            <td><font face="Verdana" size="1">0</font></td>
            <td><font face="Verdana" size="1"><?php echo $prjChildEmpAll[26]; ?></font></td>
            <?php if($designationRow['F8'] == 'FC' || $designationRow['F8'] == 'Admin') { ?>
            <td><font face="Verdana" size="1"><a href="ProjectHoursAllocationUpdate.php?iddata=<?php echo $prjChildEmpAll[0]; ?>" class="btn btn-primary" style="text-decoration: none;">Edit</a></font></td>
            <?php }else{ ?>
            <td></td>
            <?php } ?>
        </tr>
        
        <?php
    }
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
    
    echo "<tr><th colspan='12'>TOTAL OF PROJECT HOURS AND AMOUNT</th><th>$prj1Hours</th><th>$project1totalamt</th><th colspan='3'></th><th>$prj2Hours</th><th>$project2totalamt</th><th colspan='3'></th><th>$prj3Hours</th><th>$project3totalamt</th><th colspan='3'></th><th>$prj4Hours</th><th>$project4totalamt</th><th colspan='3'></th><th>$prj5Hours</th><th>$project5totalamt</th><th>$prjTotalHours</th><th>$prjTrnHours</th><th>$totaladditionalhrs</th><th>$totaladditionalamt</th><th>$totalregularamt</th><th colspan='2'></th></tr>";
    $conn->close();
}

if (isset($_POST['flag'])) {
//    echo "HEY"."<pre>";print_R($_POST);
    $fromDate = date('Ymd', strtotime($_POST['fromDate']));
    $toDate = date('Ymd', strtotime($_POST['toDate']));
    
    $searchValue = $_POST['searchValue'];
    if($_POST['fromDate'] != '' && $_POST['toDate'] !=''){
        $dateQuery = "p.empdate >= '$fromDate' AND p.empdate <= '$toDate' AND ";
    }
    $prjChildEmpQuery = "select DISTINCT p.*, o.grade, o.hrrate, h.ot_holiday,h.regrate, h.ot_regular, h.regdayrate from projecthrsallocation p "
            . "LEFT JOIN onboardrequest o ON o.fullname=p.empname "
            . "LEFT JOIN hourlywagescasual h ON h.grades=o.grade where $dateQuery p.parentempcode =" . $searchValue ." AND p.totalprjhrs!='' AND p.empcode =" . $searchValue;
    
    
    $prjChildEmpResult = mysqli_query($conn, $prjChildEmpQuery);
    $prjChildEmpRow = mysqli_fetch_all($prjChildEmpResult);
    $project1totalhrs = 0;
    $project2totalhrs = 0;
    $project3totalhrs = 0;
    $project4totalhrs = 0;
    $project5totalhrs = 0;
    $project1totalamt = 0;
    $project2totalamt = 0;
    $project3totalamt = 0;
    $project4totalamt = 0;
    $project5totalamt = 0;
    $totalprojecthrs = 0;
    $totalunpaidhrs = 0;
    $totaladditionalhrs = 0;
    $totaladditionalamt = 0;
    $totalregularamt = 0;
    $totalMinutes = 0; 
    $totalMinutes1 = 0; 
    $totalMinutes2 = 0; 
    $totalMinutes3 = 0; 
    $totalMinutes4 = 0; 
    $totalMinutes5 = 0; 
    $totalMinutestrn = 0; 
    foreach ($prjChildEmpRow as $prjChildEmpAll) { 
//        echo "<pre>";print_R($prjChildEmpAll);
        $fromTime = $prjChildEmpAll[6];
        $toTime = $prjChildEmpAll[7];
        if($prjChildEmpAll[8] > 8){ 
            $sub = $prjChildEmpAll[8]-8;
            $otHours = $sub*$prjChildEmpAll[31];
        } 
        $publicDate = date('Ymd', strtotime($prjChildEmpAll[3]));
        $publicQuery = "SELECT OTDate from otdate where OTDate=$publicDate";
        $publicResult = mysqli_query($conn, $publicQuery);
//        $publicRaw = mysqli_fetch_all($publicResult);
        $publicRaw = mysqli_num_rows($publicResult);
        
        $isPublicHoliday = false; // Implement your logic
        $isSunday = date("w", strtotime($prjChildEmpAll[3])) == 0;
        
        $regularAmtSuperVisor = $prjChildEmpAll[37]/8;
        $isChecked = ($prjChildEmpAll[31] == 1) ? 'checked'.' '.'disabled' : '';
        
        if ($isSunday || $publicRaw > 0) {
            $color = "background-color:red";
        } else {
            $color = "";
        }
        
        $overtimeHours = $prjChildEmpAll[8] - 8;
        if($overtimeHours < 0){
            $overtimeHours = "";
        }
        $fetchappQuery = "SELECT * from projectapproval where empdate='$prjChildEmpAll[3]' AND parent_id=$prjChildEmpAll[1] AND child_id=$prjChildEmpAll[4]";
        $fetchappResult = mysqli_query($conn, $fetchappQuery);
        $fetchAppRow = mysqli_fetch_all($fetchappResult);
        ?>
        <tr>
            <!--<td><input type="checkbox" name="approve[]" value="<?php echo $prjChildEmpAll[0]; ?>" <?php echo $isChecked; ?>/></td>-->
            <td style="<?php echo $color; ?>"><font face="Verdana" size="1"><?php echo $prjChildEmpAll[3]; ?></font></td>
            <td><font face="Verdana" size="1"><?php echo $prjChildEmpAll[1]; ?></font></td>
            <td><font face="Verdana" size="1"><?php echo $prjChildEmpAll[2]; ?></font></td>
            <td><font face="Verdana" size="1"><?php echo $prjChildEmpAll[4]; ?></font></td>
            <td><font face="Verdana" size="1"><?php echo $prjChildEmpAll[5]; ?></font></td>
            <td><font face="Verdana" size="1"><?php echo $prjChildEmpAll[32]; ?></font></td>
            <td><font face="Verdana" size="1"><?php echo date('H:i:s', strtotime($prjChildEmpAll[6])); ?></font></td>
            <td><font face="Verdana" size="1"><?php echo date('H:i:s', strtotime($prjChildEmpAll[7])); ?></font></td>
            <td><font face="Verdana" size="1"><?php echo $prjChildEmpAll[8]; ?></font></td>
            <td><font face="Verdana" size="1">
                <?php 
                    if($fetchAppRow[0][1] == $prjChildEmpAll[3] && $fetchAppRow[0][2]==$prjChildEmpAll[1] && $fetchAppRow[0][3] == $prjChildEmpAll[4] && $fetchAppRow[0][5] == $prjChildEmpAll[9]){
                        echo $fetchAppRow[0][4];
                    }else{ ?>
                <!--<input type="checkbox" class="project-checkbox" name="project1[]" value="<?php //echo $prjChildEmpAll[3].','.$prjChildEmpAll[1].','.$prjChildEmpAll[9].','.$prjChildEmpAll[4]; ?>" data-parent-id="<?php //echo $prjChildEmpAll[1]; ?>" data-child-id="<?php //echo $prjChildEmpAll[4]; ?>" data-emp-date="<?php //echo $prjChildEmpAll[3]; ?>" data-project-name="<?php echo $prjChildEmpAll[9]; ?>" />-->
                <?php } ?>
                </font></td>
            <td><font face="Verdana" size="1">
                <?php 
                    if($fetchAppRow[0][1] == $prjChildEmpAll[3] && $fetchAppRow[0][2]==$prjChildEmpAll[1] && $fetchAppRow[0][3] == $prjChildEmpAll[4] && $fetchAppRow[0][5] == $prjChildEmpAll[9] && $fetchAppRow[0][15]!=''){
                        echo $fetchAppRow[0][15];
                    }else{ 
                        if($prjChildEmpAll[9] !='' && $fetchAppRow[0][4]!=''){
                        ?>
                <input type="checkbox" class="project-checkbox" name="project1[]" value="<?php echo $prjChildEmpAll[3].','.$prjChildEmpAll[1].','.$prjChildEmpAll[9].','.$prjChildEmpAll[4]; ?>" data-parent-id="<?php echo $prjChildEmpAll[1]; ?>" data-child-id="<?php echo $prjChildEmpAll[4]; ?>" data-emp-date="<?php echo $prjChildEmpAll[3]; ?>" data-project-name="<?php echo $prjChildEmpAll[9]; ?>" /></font></td>
                    <?php } } ?>
            <td><font face="Verdana" size="1"><?php echo $prjChildEmpAll[9]; ?></font></td>
            <td><font face="Verdana" size="1"><?php 
                echo $prjChildEmpAll[14]; 
                $project1totalhrs =$prjChildEmpAll[14]; 
                list($hours1, $minutes1) = explode(':', $project1totalhrs);
                $totalMinutes1 += ($hours1 * 60) + $minutes1;
                ?></font></td>
            <td><font face="Verdana" size="1"><?php 
            $project1hoursAmt = $prjChildEmpAll[14];
            list($hoursprj1, $minutesprj1) = explode(':', $project1hoursAmt);
            $totalMinutesprj1 = ($hoursprj1 * 60) + $minutesprj1;
            $decimalHoursprj1 = $totalMinutesprj1 / 60;
            
            $project1amount = isset($prjChildEmpAll[15]) ? $prjChildEmpAll[15] : $decimalHoursprj1 * $regularAmtSuperVisor; 
            $whole_part1 = floor($project1amount);
            $decimal_part1 = $project1amount - $whole_part1;
            if ($decimal_part1 <= 0.5) {  
                $final_amount1 = $whole_part1;  
            } else {  
                $final_amount1 = $whole_part1 + 1;  
            } 
            echo $final_amount1;
            if($prjChildEmpAll[9] !=''){
                    echo "<input type='hidden' name='project1amt[]' value='{$prjChildEmpAll[3]},{$prjChildEmpAll[4]},{$final_amount1},{$prjChildEmpAll[1]}'>";
            }
            $prj1amounttotal = (isset($prjChildEmpAll[15]))? $prjChildEmpAll[15] : $prjChildEmpAll[14] * $regularAmtSuperVisor; 
            $project1totalamt +=$final_amount1;
            ?></font></td>
            <td><font face="Verdana" size="1">
                <?php 
                    if($fetchAppRow[0][1] == $prjChildEmpAll[3] && $fetchAppRow[0][2]==$prjChildEmpAll[1] && $fetchAppRow[0][3] == $prjChildEmpAll[4] && $fetchAppRow[0][7] == $prjChildEmpAll[10]){
                        echo $fetchAppRow[0][6];
                    }else{ ?>
                <!--<input type="checkbox" class="project-checkbox" name="project2[]" value="<?php //echo $prjChildEmpAll[3].','.$prjChildEmpAll[1].','.$prjChildEmpAll[10].','.$prjChildEmpAll[4]; ?>" data-parent-id="<?php //echo $prjChildEmpAll[1]; ?>" data-child-id="<?php //echo $prjChildEmpAll[4]; ?>" data-emp-date="<?php echo $prjChildEmpAll[3]; ?>" <?php //echo $isChecked; ?> />-->
                <?php } ?>
                </font></td>
            <td><font face="Verdana" size="1">
                <?php 
                    if($fetchAppRow[0][1] == $prjChildEmpAll[3] && $fetchAppRow[0][2]==$prjChildEmpAll[1] && $fetchAppRow[0][3] == $prjChildEmpAll[4] && $fetchAppRow[0][7] == $prjChildEmpAll[10] && $fetchAppRow[0][16]!=''){
                        echo $fetchAppRow[0][16];
                    }else{ 
                        if($prjChildEmpAll[10] !='' && $fetchAppRow[0][6]!=''){
                        ?>
                <input type="checkbox" class="project-checkbox" name="project2[]" value="<?php echo $prjChildEmpAll[3].','.$prjChildEmpAll[1].','.$prjChildEmpAll[10].','.$prjChildEmpAll[4]; ?>"  <?php //echo $isChecked; ?> /></font></td>
                    <?php } } ?>
            <td><font face="Verdana" size="1"><?php echo $prjChildEmpAll[10]; ?></font></td>
            <td><font face="Verdana" size="1"><?php 
                echo $prjChildEmpAll[16]; 
                $project2totalhrs = $prjChildEmpAll[16]; 
                list($hours2, $minutes2) = explode(':', $project2totalhrs);
                $totalMinutes2 += ($hours2 * 60) + $minutes2;
                ?></font></td>
            <td><font face="Verdana" size="1"><?php 
            list($hoursprj2, $minutesprj2) = explode(':', $prjChildEmpAll[16]);
            $totalMinutesprj2 = ($hoursprj2 * 60) + $minutesprj2;
            $decimalHoursprj2 = $totalMinutesprj2 / 60;
            $project2amount = isset($prjChildEmpAll[17])? $prjChildEmpAll[17] : $decimalHoursprj2 * $regularAmtSuperVisor; 
            $whole_part2 = floor($project2amount);
            $decimal_part2 = $project2amount - $whole_part2;
            if ($decimal_part2 <= 0.5) {  
                $final_amount2 = $whole_part2;  
            } else {  
                $final_amount2 = $whole_part2 + 1;  
            } 
            echo $final_amount2;
            if($prjChildEmpAll[10] !=''){
                    echo "<input type='hidden' name='project2amt[]' value='{$prjChildEmpAll[3]},{$prjChildEmpAll[4]},{$final_amount2},{$prjChildEmpAll[1]}'>";
            }
            $prj2amounttotal = (isset($prjChildEmpAll[17]))? $prjChildEmpAll[17] : $prjChildEmpAll[16] * $regularAmtSuperVisor; 
            $project2totalamt +=$final_amount2;
            ?></font></td>
            <td><font face="Verdana" size="1">
                <?php 
                    if($fetchAppRow[0][1] == $prjChildEmpAll[3] && $fetchAppRow[0][2]==$prjChildEmpAll[1] && $fetchAppRow[0][3] == $prjChildEmpAll[4] && $fetchAppRow[0][9] == $prjChildEmpAll[11]){
                        echo $fetchAppRow[0][8];
                    }else{ ?>
                <!--<input type="checkbox" class="project-checkbox" name="project3[]" value="<?php //echo $prjChildEmpAll[3].','.$prjChildEmpAll[1].','.$prjChildEmpAll[11].','.$prjChildEmpAll[4]; ?>" data-parent-id="<?php //echo $prjChildEmpAll[1]; ?>" data-child-id="<?php //echo $prjChildEmpAll[4]; ?>" data-emp-date="<?php //echo $prjChildEmpAll[3]; ?>" <?php //echo $isChecked; ?> />-->
                <?php } ?>
                </font></td>
            <td><font face="Verdana" size="1">
                <?php 
                if($fetchAppRow[0][1] == $prjChildEmpAll[3] && $fetchAppRow[0][2]==$prjChildEmpAll[1] && $fetchAppRow[0][3] == $prjChildEmpAll[4] && $fetchAppRow[0][7] == $prjChildEmpAll[10] && $fetchAppRow[0][17]!=''){
                    echo $fetchAppRow[0][17];
                }else{ 
                    if($prjChildEmpAll[11] !='' && $fetchAppRow[0][8]!=''){
                    ?>
                <input type="checkbox" class="project-checkbox" name="project3[]" value="<?php echo $prjChildEmpAll[3].','.$prjChildEmpAll[1].','.$prjChildEmpAll[11].','.$prjChildEmpAll[4]; ?>"  <?php //echo $isChecked; ?> /></font></td>
                    <?php } } ?>
            <td><font face="Verdana" size="1"><?php echo $prjChildEmpAll[11]; ?></font></td>
            <td><font face="Verdana" size="1"><?php 
                echo $prjChildEmpAll[18]; 
                $project3totalhrs = $prjChildEmpAll[18]; 
                list($hours3, $minutes3) = explode(':', $project3totalhrs);
                $totalMinutes3 += ($hours3 * 60) + $minutes3;
                ?></font></td>
            <td><font face="Verdana" size="1"><?php 
            list($hoursprj3, $minutesprj3) = explode(':', $prjChildEmpAll[18]);
            $totalMinutesprj3 = ($hoursprj3 * 60) + $minutesprj3;
            $decimalHoursprj3 = $totalMinutesprj3 / 60;
            $project3amount = (isset($prjChildEmpAll[19]))? $prjChildEmpAll[19] : $decimalHoursprj3 * $regularAmtSuperVisor; 
            $whole_part = floor($project3amount);
            $decimal_part = $project3amount - $whole_part;
            if ($decimal_part <= 0.5) {  
                $final_amount = $whole_part;  
            } else {  
                $final_amount = $whole_part + 1;  
            } 
            echo $final_amount;
            if($prjChildEmpAll[11] !=''){
                    echo "<input type='hidden' name='project3amt[]' value='{$prjChildEmpAll[3]},{$prjChildEmpAll[4]},{$final_amount},{$prjChildEmpAll[1]}'>";
            }
            $prj3amounttotal = (isset($prjChildEmpAll[19]))? $prjChildEmpAll[19] : $prjChildEmpAll[18] * $regularAmtSuperVisor; 
            $project3totalamt +=$final_amount;
            ?></font></td>
            <td><font face="Verdana" size="1">
                <?php 
                    if($fetchAppRow[0][1] == $prjChildEmpAll[3] && $fetchAppRow[0][2]==$prjChildEmpAll[1] && $fetchAppRow[0][3] == $prjChildEmpAll[4] && $fetchAppRow[0][11] == $prjChildEmpAll[12]){
                        echo $fetchAppRow[0][10];
                    }else{ ?>
                <!--<input type="checkbox" class="project-checkbox" name="project4[]" value="<?php //echo $prjChildEmpAll[3].','.$prjChildEmpAll[1].','.$prjChildEmpAll[12].','.$prjChildEmpAll[4]; ?>" data-parent-id="<?php //echo $prjChildEmpAll[1]; ?>" data-child-id="<?php //echo $prjChildEmpAll[4]; ?>" data-emp-date="<?php //echo $prjChildEmpAll[3]; ?>" <?php //echo $isChecked; ?> />-->
                <?php } ?>
                </font></td>
            <td><font face="Verdana" size="1">
                <?php 
                if($fetchAppRow[0][1] == $prjChildEmpAll[3] && $fetchAppRow[0][2]==$prjChildEmpAll[1] && $fetchAppRow[0][3] == $prjChildEmpAll[4] && $fetchAppRow[0][7] == $prjChildEmpAll[10] && $fetchAppRow[0][18]!=''){
                    echo $fetchAppRow[0][18];
                }else{ 
                    if($prjChildEmpAll[12] !='' && $fetchAppRow[0][10]!=''){
                    ?>
                <input type="checkbox" class="project-checkbox" name="project4[]" value="<?php echo $prjChildEmpAll[3].','.$prjChildEmpAll[1].','.$prjChildEmpAll[12].','.$prjChildEmpAll[4]; ?>" data-parent-id="<?php echo $prjChildEmpAll[1]; ?>" data-child-id="<?php echo $prjChildEmpAll[4]; ?>" data-emp-date="<?php echo $prjChildEmpAll[3]; ?>" <?php echo $isChecked; ?> /></font></td>
                    <?php } } ?>
            <td><font face="Verdana" size="1"><?php echo $prjChildEmpAll[12]; ?></font></td>
            <td><font face="Verdana" size="1"><?php 
                echo $prjChildEmpAll[20]; 
                $project4totalhrs = $prjChildEmpAll[20]; 
                list($hours4, $minutes4) = explode(':', $project4totalhrs);
                $totalMinutes4 += ($hours4 * 60) + $minutes4;
                ?></font></td>
            <td><font face="Verdana" size="1"><?php 
            list($hoursprj4, $minutesprj4) = explode(':', $prjChildEmpAll[20]);
            $totalMinutesprj4 = ($hoursprj4 * 60) + $minutesprj4;
            $decimalHoursprj4 = $totalMinutesprj4 / 60;
            $project4amount = isset($prjChildEmpAll[21]) ? $prjChildEmpAll[21] : $decimalHoursprj4 * $regularAmtSuperVisor; 
            $whole_part4 = floor($project4amount);
            $decimal_part4 = $project4amount - $whole_part4;
            if ($decimal_part4 <= 0.5) {  
                $final_amount4 = $whole_part4;  
            } else {  
                $final_amount4 = $whole_part4 + 1;  
            } 
            echo $final_amount4;
            if($prjChildEmpAll[12] !=''){
                    echo "<input type='hidden' name='project4amt[]' value='{$prjChildEmpAll[3]},{$prjChildEmpAll[4]},{$final_amount4},{$prjChildEmpAll[1]}'>";
            }
            $prj4amounttotal = (isset($prjChildEmpAll[21]))? $prjChildEmpAll[21] : $prjChildEmpAll[20] * $regularAmtSuperVisor; 
            $project4totalamt +=$final_amount4;
            ?></font></td>
            <td><font face="Verdana" size="1">
                <?php 
                    if($fetchAppRow[0][1] == $prjChildEmpAll[3] && $fetchAppRow[0][2]==$prjChildEmpAll[1] && $fetchAppRow[0][3] == $prjChildEmpAll[4] && $fetchAppRow[0][13] == $prjChildEmpAll[13]){
                        echo $fetchAppRow[0][12];
                    }else{ ?>
                <!--<input type="checkbox" class="project-checkbox" name="project5[]" value="<?php //echo $prjChildEmpAll[3].','.$prjChildEmpAll[1].','.$prjChildEmpAll[13].','.$prjChildEmpAll[4]; ?>" data-parent-id="<?php //echo $prjChildEmpAll[1]; ?>" data-child-id="<?php //echo $prjChildEmpAll[4]; ?>" data-emp-date="<?php //echo $prjChildEmpAll[3]; ?>" <?php //echo $isChecked; ?> />-->
                <?php } ?>
                </font></td>
            <td><font face="Verdana" size="1">
                <?php 
                if($fetchAppRow[0][1] == $prjChildEmpAll[3] && $fetchAppRow[0][2]==$prjChildEmpAll[1] && $fetchAppRow[0][3] == $prjChildEmpAll[4] && $fetchAppRow[0][7] == $prjChildEmpAll[10] && $fetchAppRow[0][19]!=''){
                    echo $fetchAppRow[0][19];
                }else{
                    if($prjChildEmpAll[13] !='' && $fetchAppRow[0][12]!=''){
                    ?>
                <input type="checkbox" class="project-checkbox" name="project5[]" value="<?php echo $prjChildEmpAll[3].','.$prjChildEmpAll[1].','.$prjChildEmpAll[13].','.$prjChildEmpAll[4]; ?>" data-parent-id="<?php echo $prjChildEmpAll[1]; ?>" data-child-id="<?php echo $prjChildEmpAll[4]; ?>" data-emp-date="<?php echo $prjChildEmpAll[3]; ?>" <?php echo $isChecked; ?> /></font></td>
                    <?php } } ?>
            <td><font face="Verdana" size="1"><?php echo $prjChildEmpAll[13]; ?></font></td>
            <td><font face="Verdana" size="1"><?php 
                echo $prjChildEmpAll[22]; 
                $project5totalhrs +=$prjChildEmpAll[22]; 
                list($hours5, $minutes5) = explode(':', $project5totalhrs);
                $totalMinutes5 += ($hours5 * 60) + $minutes5;                
                ?></font></td>
            <td><font face="Verdana" size="1"><?php 
            list($hoursprj5, $minutesprj5) = explode(':', $prjChildEmpAll[22]);
            $totalMinutesprj5 = ($hoursprj5 * 60) + $minutesprj5;
            $decimalHoursprj5 = $totalMinutesprj5 / 60;
            $project5amount = (isset($prjChildEmpAll[23]))? $prjChildEmpAll[23] : $decimalHoursprj5 * $regularAmtSuperVisor; 
            $whole_part5 = floor($project5amount);
            $decimal_part5 = $project5amount - $whole_part5;
            if ($decimal_part5 <= 0.5) {  
                $final_amount5 = $whole_part5;  
            } else {  
                $final_amount5 = $whole_part5 + 1;  
            } 
            echo $final_amount5;
            if($prjChildEmpAll[13] !=''){
                    echo "<input type='hidden' name='project5amt[]' value='{$prjChildEmpAll[3]},{$prjChildEmpAll[4]},{$final_amount5},{$prjChildEmpAll[1]}'>";
            }
            $prj5amounttotal = (isset($prjChildEmpAll[23]))? $prjChildEmpAll[23] : $prjChildEmpAll[22] * $regularAmtSuperVisor; 
            $project5totalamt +=$final_amount5;
            ?></font></td>
            <td><font face="Verdana" size="1"><?php 
                echo $prjChildEmpAll[24]; 
                $totalprojecthrs += $prjChildEmpAll[24]; 
                list($hours, $minutes) = explode(':', $totalprojecthrs);
                $totalMinutes += ($hours * 60) + $minutes;
                ?></font></td>
            <td><font face="Verdana" size="1"><?php 
                echo $prjChildEmpAll[25]; 
                $totalunpaidhrs += $prjChildEmpAll[25]; 
                list($hourstrn, $minutestrn) = explode(':', $totalunpaidhrs);
                $totalMinutestrn += ($hourstrn * 60) + $minutestrn;
                ?></font></td>
            <td><font face="Verdana" size="1"><?php echo $overtimeHours; $totaladditionalhrs += $overtimeHours; ?></font></td>
            <td><font face="Verdana" size="1">
                <?php
                if ($isSunday || $publicRaw > 0) {
                    echo ($prjChildEmpAll[29]) ? $prjChildEmpAll[29] : $overtimeHours * $regularAmtSuperVisor;
                    $totalovertimeamount = ($prjChildEmpAll[29]) ? $prjChildEmpAll[29] : $overtimeHours * $regularAmtSuperVisor;
                    $totaladditionalamt +=$totalovertimeamount;
                } else {
                    echo ($prjChildEmpAll[29]) ? $prjChildEmpAll[29] : $overtimeHours * $regularAmtSuperVisor;
                    $totalovertimeamount = ($prjChildEmpAll[29]) ? $prjChildEmpAll[29] : $overtimeHours * $regularAmtSuperVisor;
                    $totaladditionalamt +=$totalovertimeamount;
                }
                ?>
                </font></td>
            <td><font face="Verdana" size="1"><?php echo $prjChildEmpAll[37]; $totalregularamt += $prjChildEmpAll[37]; ?></font></td>
            <td><font face="Verdana" size="1"><?php echo $prjChildEmpAll[26]; ?></font></td>
            <?php if($designationRow['F8'] == 'FC' || $designationRow['F8'] == 'Admin') { ?>
            <td><font face="Verdana" size="1"><a href="ProjectHoursAllocationUpdate.php?iddata=<?php echo $prjChildEmpAll[0]; ?>&parent" class="btn btn-primary" style="text-decoration: none;">Edit</a></font></td>
            <?php }else{ ?>
            <td></td>
            <?php } ?>
        </tr>
        <?php
    }
    
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
    
    echo "<tr><th colspan='12'>TOTAL OF PROJECT HOURS AND AMOUNT</th><th>$prj1Hours</th><th>$project1totalamt</th><th colspan='3'></th><th>$prj2Hours</th><th>$project2totalamt</th><th colspan='3'></th><th>$prj3Hours</th><th>$project3totalamt</th><th colspan='3'></th><th>$prj4Hours</th><th>$project4totalamt</th><th colspan='3'></th><th>$prj5Hours</th><th>$project5totalamt</th><th>$prjTotalHours</th><th>$prjTrnHours</th><th>$totaladditionalhrs</th><th>$totaladditionalamt</th><th>$totalregularamt</th><th colspan='2'></th></tr>";
    $conn->close();
}
?>