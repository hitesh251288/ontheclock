<?php
ob_start("ob_gzhandler");
error_reporting(E_ALL);
set_time_limit(0);
include "Functions.php";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$conn = openConnection();
$iconn = openIConnection();
$uconn =  mysqli_connect("localhost", "root", "namaste", "unis");
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  exit();
}
$oconn = mssql_connection("DESKTOP-I1EBN6N\SQLEXPRESS", "PayMaster", "sa", "bit123");
echo "\n\r Connected to MSSQL: " . $oconn;

$uempQuery = "SELECT C_Name FROM tuser";
$uempResult = mysqli_query($uconn, $uempQuery);

while($uempRow = mysqli_fetch_array($uempResult)){
    $uempName[] = $uempRow['C_Name'];   
}
$empQuery = "SELECT Emp_Payroll_No, Emp_Name from tblEmployee";
$empResult = mssql_query($empQuery,$oconn);


while($empRow = mssql_fetch_array($empResult)){
    $empName[] = $empRow['Emp_Name'];
}
$array1 = $empName;
$array2 = $uempName;
$arrayDiff = array_diff($array1, $array2);
foreach($arrayDiff as $key=>$val){
    $query = "select E.*, C.Cmp_name, d.dept_name From tblemployee e  Inner join tblcompany c On E.Cmp_Id = c.Cmp_Id Inner join tblDepartment D On E.Cmp_Id = D.Cmp_Id and E.Emp_Dept_Id = D.Dept_Id where e.Emp_Name='$val'";
    $result = mssql_query($query,$oconn);
    while($row = mssql_fetch_array($result)){
        $data[] = array($row['Emp_Name'],(int)$row['Emp_Payroll_No'],$row['dept_name']);
        $deptName[] = $row['dept_name'];
    }
}

$udeptQuery = "SELECT * from cpost";
$udeptResult = mysqli_query($uconn, $udeptQuery);
while($udeptRow = mysqli_fetch_array($udeptResult)){    
//    $deptData[] = array($deptRow['C_Code'],$deptRow['C_Name']);
    $udeptData[] = $udeptRow['C_Name'];
    $udeptLastID = $udeptRow['C_Code'];
}

$deptQuery = "SELECT Dept_Name FROM tblDepartment";
$deptResult = mssql_query($deptQuery,$oconn);
while($deptRow = mssql_fetch_array($deptResult)){
    $deptData[] = $deptRow['Dept_Name'];
}

$deptArray1 = $udeptData;
$deptArray2 = $deptData;
$deptDiff = array_diff($deptArray2, $deptArray1);
//echo "<pre>";print_R($deptArray1);
//echo "<pre>";print_R($deptArray2);
//echo "<pre>";print_R($deptDiff);

$deptID = $udeptLastID+1;
foreach($deptDiff as $deptKey => $deptVal){
    $ID = $deptID++;
    $IDS = str_pad($ID,3,"0",STR_PAD_LEFT);
    $deptDataQuery = "INSERT INTO cpost (C_Code,C_Name)VALUES('$IDS','$deptVal')";
//    echo "<br>";
    mysqli_query($uconn, $deptDataQuery);
}
for($i=0;$i<count($data);$i++){
//    echo "<pre>";print_R($data[$i][2]);
//    echo "<pre>";print_R($udeptData[$i]);
    
    $addEmp = "INSERT INTO tuser (L_ID,C_Name,C_Unique,L_Type) VALUES (".$data[$i][1].",'".$data[$i][0]."',".addZero($data[$i][1], $_SESSION[$session_variable . "EmployeeCodeLength"]).",'0')";
//    echo "<br>";
        $query = "SELECT * FROM Cpost where C_Name='".$data[$i][2]."'";
        $result = mysqli_query($uconn, $query);
        $row = mysqli_fetch_array($result);
//        echo "<pre>";print_R($row);
//        echo "<br>";
        $addEmpId = "INSERT INTO temploye (L_UID,C_Post) VALUES (".$data[$i][1].",'".$row[0]."')";
//        echo "<br>";
    
    
    mysqli_query($uconn, $addEmp);
    mysqli_query($uconn, $addEmpId);
}