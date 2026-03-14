<?php

ob_start("ob_gzhandler");
error_reporting(E_ERROR);

$conn = new mysqli("localhost", "root", "namaste", "access_lfz");

// Check connection
if ($conn->connect_errno) {
    echo "Failed to connect to MySQL: " . $conn->connect_error;
    exit();
}
function timeToMinutes($time) {  
    list($hours, $minutes) = explode(':', $time);  
    return intval($hours) * 60 + intval($minutes);  
} 
if (isset($_POST['submit'])) {
    if ($_POST['totalhrs'] != '') {
        $pattern = '/(\d{1,2})\s*hours?\s*(\d{1,2})\s*minutes?/';  
        if ($_POST['designation'] == 'PM') {
            //    echo "<pre>";print_R($_POST);
            $hireauthCode = $_POST['hireauth'];
            $parentNameQuery = "select name from tuser where id=" . $hireauthCode;
            $parentNameResult = mysqli_query($conn, $parentNameQuery);
            $parentNameRaw = mysqli_fetch_assoc($parentNameResult);

            $parentEmpName = $parentNameRaw['name'];
            $empdate = $_POST['dateemp'];
            $empcode = $_POST['empcode'];
            $empname = $_POST['empname'];
            $entrytime = str_replace(':', '', $_POST['entrytime']);
            $endtime = str_replace(':', '', $_POST['endtime']);
            $totalhrwork = $_POST['totalhrwork'];

            $prj1 = $_POST['project1'];
            $prj2 = $_POST['project2'];
            $prj3 = $_POST['project3'];
            $prj4 = $_POST['project4'];
            $prj5 = $_POST['project5'];
            $prj1hrs = preg_replace($pattern, '$1:$2', $_POST['prj1hrs']);
            $prj2hrs = preg_replace($pattern, '$1:$2', $_POST['prj2hrs']);
            $prj3hrs = preg_replace($pattern, '$1:$2', $_POST['prj3hrs']);
            $prj4hrs = preg_replace($pattern, '$1:$2', $_POST['prj4hrs']);
            $prj5hrs = preg_replace($pattern, '$1:$2', $_POST['prj5hrs']);
            $totalhrs = preg_replace($pattern, '$1:$2', $_POST['totalhrs']);
            $transithrs = preg_replace($pattern, '$1:$2', $_POST['transithrs']);
            $remark = $_POST['remark'];

            for ($i = 0; $i < count($empdate); $i++) {
                $employeeDate = date('Ymd', strtotime($empdate[$i]));
                $selectQuery = "select * from projecthrsallocation where empcode = $empcode[$i] AND empdate = '$employeeDate' AND parentempcode=$hireauthCode";
                $selectResult = mysqli_query($conn, $selectQuery);

                if ($selectResult->num_rows == 0) { 
                    $employeeDate = date('Ymd', strtotime($empdate[$i]));
                    $hrsallocationQuery = "insert into projecthrsallocation(parentempcode,parentempname,empdate,"
                            . "empcode,empname,entrytime,exittime,totalhrwork,project1,project2,project3,"
                            . "project4,project5,project1hrs,project2hrs,project3hrs,project4hrs,project5hrs,"
                            . "totalprjhrs,transithrs,remark)values($hireauthCode,'$parentEmpName','$employeeDate',"
                            . "$empcode[$i],'$empname[$i]','$entrytime[$i]','$endtime[$i]','$totalhrwork[$i]','$prj1[$i]','$prj2[$i]','$prj3[$i]',"
                            . "'$prj4[$i]','$prj5[$i]','$prj1hrs[$i]','$prj2hrs[$i]','$prj3hrs[$i]','$prj4hrs[$i]','$prj5hrs[$i]','$totalhrs[$i]','$transithrs[$i]','$remark[$i]')";
                    $hrsallocationResult = mysqli_query($conn, $hrsallocationQuery);
                } else { 
                    $updatehrsallocationQuery = "update projecthrsallocation SET totalhrwork='$totalhrwork[$i]', project1='$prj1[$i]', project2='$prj2[$i]', project3='$prj3[$i]', "
                            . "project4='$prj4[$i]', project5='$prj5[$i]',project1hrs='$prj1hrs[$i]', project2hrs='$prj2hrs[$i]', project3hrs='$prj3hrs[$i]', "
                            . "project4hrs='$prj4hrs[$i]', project5hrs='$prj5hrs[$i]',totalprjhrs='$totalhrs[$i]', transithrs='$transithrs[$i]', remark='$remark[$i]' "
                            . "where parentempcode=$hireauthCode AND empdate='$employeeDate' AND empcode='$empcode[$i]'";
                    $updatehrsallocationResult = mysqli_query($conn, $updatehrsallocationQuery);
                    $varprj = 3;
                }
            }
            if ($hrsallocationResult) {
                echo "Data Inserted Successfully";
            } else {
                echo "Problem Occur To Insert Data";
                $prjstatus = 2;
            }
            header('Location: ProjectHoursAllocation.php?prjstatus=1&prjstatus=' . $prjstatus . '&prjstatus=' . $varprj);
        } else {
//                echo "<pre>";print_R($_POST);
            $hireauthCode = $_POST['hireauth'];
            $parentNameQuery = "select name from tuser where id=" . $hireauthCode;
            $parentNameResult = mysqli_query($conn, $parentNameQuery);
            $parentNameRaw = mysqli_fetch_assoc($parentNameResult);

            $parentEmpName = $parentNameRaw['name'];
            $empdate = $_POST['dateemp'];
            $empcode = $_POST['empcode'];
            $empname = $_POST['empname'];
            $entrytime = str_replace(':', '', $_POST['entrytime']);
            $endtime = str_replace(':', '', $_POST['endtime']);
//            $totalhrwork = preg_replace('/(\d{2})\s+hours\s+(\d{2})\s+minutes/', '$1:$2', $_POST['totalhrwork']);
            $totalhrwork = preg_replace($pattern, '$1:$2', $_POST['totalhrwork']);
            $prj1 = $_POST['project1'];
            $prj2 = $_POST['project2'];
            $prj3 = $_POST['project3'];
            $prj4 = $_POST['project4'];
            $prj5 = $_POST['project5'];
            $prj1hrs = preg_replace($pattern, '$1:$2', $_POST['prj1hrs']);
            $prj2hrs = preg_replace($pattern, '$1:$2', $_POST['prj2hrs']);
            $prj3hrs = preg_replace($pattern, '$1:$2', $_POST['prj3hrs']);
            $prj4hrs = preg_replace($pattern, '$1:$2', $_POST['prj4hrs']);
            $prj5hrs = preg_replace($pattern, '$1:$2', $_POST['prj5hrs']);
            $totalhrs = preg_replace($pattern, '$1:$2', $_POST['totalhrs']);
            $transithrs = preg_replace($pattern, '$1:$2', $_POST['transithrs']);
            $remark = $_POST['remark'];
//            echo "<pre>";print_R($_POST);die;
            for ($i = 0; $i < count($empdate); $i++) {
                $employeeDate = date('Ymd', strtotime($empdate[$i]));
                $selectQuery = "select * from projecthrsallocation where empcode = $empcode[$i] AND empdate = '$employeeDate' AND parentempcode=$hireauthCode";
                $selectResult = mysqli_query($conn, $selectQuery);

                if ($selectResult->num_rows == 0) {
                    if (timeToMinutes($totalhrs[$i]) > timeToMinutes($totalhrwork[$i])) {
                        $greaterhrs = 4;
                        header('Location: ProjectHoursAllocation.php?prjmessage=' . $greaterhrs);
                        exit;
                    }else{
                    $employeeDate = date('Ymd', strtotime($empdate[$i]));
                    $hrsallocationQuery = "insert into projecthrsallocation(parentempcode,parentempname,empdate,"
                            . "empcode,empname,entrytime,exittime,totalhrwork,project1,project2,project3,"
                            . "project4,project5,project1hrs,project2hrs,project3hrs,project4hrs,project5hrs,"
                            . "totalprjhrs,transithrs,remark)values($hireauthCode,'$parentEmpName','$employeeDate',"
                            . "$empcode[$i],'$empname[$i]','$entrytime[$i]','$endtime[$i]','$totalhrwork[$i]','$prj1[$i]','$prj2[$i]','$prj3[$i]',"
                            . "'$prj4[$i]','$prj5[$i]','$prj1hrs[$i]','$prj2hrs[$i]','$prj3hrs[$i]','$prj4hrs[$i]','$prj5hrs[$i]','$totalhrs[$i]','$transithrs[$i]','$remark[$i]')";
                        $hrsallocationResult = mysqli_query($conn, $hrsallocationQuery);
//                    echo "<br>";
                    }
                } else {
//                    echo $totalhrs[$i] .">". $totalhrwork[$i];
//                    echo "<br>";
                    if (timeToMinutes($totalhrs[$i]) > timeToMinutes($totalhrwork[$i])) { 
                        $greaterhrs = 4;
                        header('Location: ProjectHoursAllocation.php?prjmessage=' . $greaterhrs);
                        exit;
                    }else{
                    $updatehrsallocationQuery = "update projecthrsallocation SET totalhrwork='$totalhrwork[$i]', project1='$prj1[$i]', project2='$prj2[$i]', project3='$prj3[$i]', "
                            . "project4='$prj4[$i]', project5='$prj5[$i]',project1hrs='$prj1hrs[$i]', project2hrs='$prj2hrs[$i]', project3hrs='$prj3hrs[$i]', "
                            . "project4hrs='$prj4hrs[$i]', project5hrs='$prj5hrs[$i]',totalprjhrs='$totalhrs[$i]', transithrs='$transithrs[$i]', remark='$remark[$i]' "
                            . "where parentempcode=$hireauthCode AND empdate='$employeeDate' AND empcode='$empcode[$i]'";
//                    echo "<br>";
                    $updatehrsallocationResult = mysqli_query($conn, $updatehrsallocationQuery);
                    $varprj = 3;
                    }
                }
            }
            if ($hrsallocationResult) {
                echo "Data Inserted Successfully";
            } else {
                echo "Problem Occur To Insert Data";
                $prjstatus = 2;
            }
            header('Location: ProjectHoursAllocation.php?prjstatus=1&prjstatus=' . $prjstatus . '&prjstatus=' . $varprj);
        }
    }
}
