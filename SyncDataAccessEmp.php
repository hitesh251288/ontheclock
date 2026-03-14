<?php
// Path to Access DB
$accessPath = 'D:\\iAS\\TimeWatch.mdb';
$accessPassword = 'SSS';

// Create COM connection to Access DB
try {
    $conn = new COM("ADODB.Connection");
    $connStr = "Provider=Microsoft.ACE.OLEDB.12.0;Data Source=$accessPath;Jet OLEDB:Database Password=$accessPassword;";
    $conn->Open($connStr);
} catch (com_exception $e) {
    die("❌ Could not connect to Access DB: " . $e->getMessage());
}

// Connect to MySQL
$mysql_conn = mysqli_connect("localhost", "root", "namaste", "access");
if (!$mysql_conn) {
    die("MySQL connection failed: " . mysqli_connect_error());
}

// Query Access
$sql = "SELECT 
  tblEmployee.PAYCODE, 
  tblEmployee.EMPNAME, 
  tblEmployee.DATEOFJOIN, 
  tblEmployee.LeavingDate, 
  tblEmployee.SEX,
  tblDepartment.DEPARTMENTNAME AS DeptName,
  tblCompany.COMPANYNAME AS CompanyName
FROM 
  (tblEmployee 
  LEFT JOIN tblDepartment ON tblEmployee.DEPARTMENTCODE = tblDepartment.DEPARTMENTCODE)
  LEFT JOIN tblCompany ON tblEmployee.COMPANYCODE = tblCompany.COMPANYCODE";

$rs = $conn->Execute($sql);

while (!$rs->EOF) {
    $paycode  = $rs->Fields("PAYCODE")->Value;
    $empname  = addslashes($rs->Fields("EMPNAME")->Value);
    $doj      = date('Ymd', strtotime($rs->Fields("DATEOFJOIN")->Value));
    $leaving  = date('Ymd', strtotime($rs->Fields("LeavingDate")->Value));
    $sex      = $rs->Fields("SEX")->Value;
    $dept     = addslashes($rs->Fields("DeptName")->Value);
    $company  = addslashes($rs->Fields("CompanyName")->Value);
    $reg_date = $doj . "0000";
    $datelimit = "N" . $doj . $doj;

    $insert = "
    INSERT INTO tuser (id, name, reg_date, datelimit, idno, dept, company)
    VALUES ('$paycode', '$empname', '$reg_date', '$datelimit', '$sex', '$dept', '$company')
    ON DUPLICATE KEY UPDATE 
      name='$empname', reg_date='$reg_date', datelimit='$datelimit',
      idno='$sex', dept='$dept', company='$company'
    ";

    mysqli_query($mysql_conn, $insert);
    $rs->MoveNext();
}

echo "<center>✅ Data pushed successfully.</center>";

// Cleanup
$rs->Close();
$conn->Close();
?>
