<?php

ob_start("ob_gzhandler");
error_reporting(E_ALL);
set_time_limit(0);
include "Functions.php";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
$conn = openConnection();
$iconn = openIConnection();
$jconn = openIConnection();

print "<center>";
displayHeader($prints, true, false);
displayLinks("", $userlevel);
print "</center>";
print "<html><title>AddPayMasterUser</title>";
print "<body>";
print "<center>";
print "<form method='post' action='AddpayMasterUser.php'>";
print "<table width='800' cellpadding='1'  cellspacing='1'>";
print "<tr><td>&nbsp;</td></tr>";
print "<tr><td>&nbsp;&nbsp;&nbsp;<b>Paymaster WebAPI URL</b></td></tr>";
print "<tr><td>&nbsp;</td></tr>";
print "<tr>";
//print "<td>&nbsp;</td>";
//displayTextbox("url", "URL</font>: ", $txtTo, $prints, 12, "25%", "75%");
print "<td>&nbsp;&nbsp;&nbsp;<input type='text' name='url' value='' />/api/PayMaster/EmployeeImport";
print "</td>";
print "</tr>";
//print "<br>";
print "<tr><td>&nbsp;</td></tr>";
print "<tr>";
//print "<td>&nbsp;</td>";
print "<td>&nbsp;&nbsp;&nbsp;<input type='submit' name='submit' value='Push Data'/>";
print "</td>";
print "</tr>";
print "<tr><td>&nbsp;</td></tr>";
print "</table>";
print "</form>";
print "</center>";
print "</body>";


if ($_POST['submit']) {
    
//$oconn = mssql_connection($txtDBIP, $txtDBName, $txtDBUser, $txtDBPass);
$oconn = mssql_connection('192.168.100.208', 'WEBPAY', 'sa', 'bitplus@123');
//echo "\n\rConnected to MSSQL: " . $oconn;




$query = "SELECT id FROM tuser where company='VEEPEE INDUSTRIES'";
    $result = mysqli_query($conn, $query);

    while ($f_data = mysqli_fetch_row($result)) {
//echo "There"."<pre>";print_R($f_data);echo "</pre>";
        $upayID[] =$f_data[0];
       
    }

$userquery = "SELECT Emp_payroll_no FROM tblEmployee where cmp_id = 7 AND Emp_Payroll_No NOT LIKE 'E0%'";

$m_result = mssql_query($userquery, $oconn);
while ($u_cur = mssql_fetch_row($m_result)) {
//echo "Mssql: ";echo "<pre>";print_R($u_cur);echo "</pre>";
		
    $uID[] =$u_cur[0];
}
$array1 = $upayID;
$array2 = $uID;
$resultarr=array_diff($array1,$array2);
//echo "<pre>";print_R($upayID);echo "</pre>";

$myXMLData = "<EmployeeDataList>";
    //for ($i = 0; $i < count($resultarr); $i++) {
foreach($resultarr as $resultarrs){
//echo $upayID[$i][0]['id']. "!=" .$uID[$i][0]['Emp_Payroll_No'];echo "<br>";

$array1 = $upayID[$i];
$array2 = $uID[$i][0];
$result=array_diff($array1,$array2);
//echo "IN: "."<pre>";print_r($result);echo "</pre>";

//echo $userquery = "SELECT * FROM tblEmployee where cmp_id = 7 AND Emp_Payroll_No NOT LIKE 'E0%' AND Emp_Payroll_No =".$upayID[$i][0]['id'];
$userquery = "SELECT * FROM tuser where company='VEEPEE INDUSTRIES' AND id IN($resultarrs)";
$resultUser = mysqli_query($conn, $userquery);

    	while ($f_data_User = mysqli_fetch_row($resultUser)) {
		//echo "<pre>";print_R($f_data_User);echo "</pre>";
   	
//echo "<br>";


            $name = explode(' ', $f_data_User[1]);
		//echo "<pre>";print_R($name);echo "</pre>";
		if($name[0] && $name[1]){
			$last_name = $name[1];	
		}else{
		        $last_name = $name[0];			
		}
		if($name[0] && $name[1] && $name[2]){
			$middle_name = $name[1];
		}else{
			$middle_name = '';
		}
            $first_name = $name[0];

            $myXMLData = $myXMLData . "<EmployeeImportModel>";
            $myXMLData = $myXMLData . "<Emp_Payroll_No>".$f_data_User[0]."</Emp_Payroll_No>";
            $myXMLData = $myXMLData . "<Emp_Title>MR.</Emp_Title>";
            $myXMLData = $myXMLData . "<Emp_First_Name>".$first_name."</Emp_First_Name>";
	    $myXMLData = $myXMLData . "<Emp_Middle_Name>".$middle_name."</Emp_Middle_Name>";	
            $myXMLData = $myXMLData . "<Emp_Last_Name>".$last_name."</Emp_Last_Name>";
            $myXMLData = $myXMLData . "<Employment_Type>Regular</Employment_Type>";
            $myXMLData = $myXMLData . "<Emp_Gender>Male</Emp_Gender>";
            $myXMLData = $myXMLData . "<Emp_Join_Date>2017-01-12</Emp_Join_Date>";
            $myXMLData = $myXMLData . "<Emp_WeekOff_Type>Day Wise</Emp_WeekOff_Type>";
            $myXMLData = $myXMLData . "<WeekDay>Sunday</WeekDay>";
            $myXMLData = $myXMLData . "<Emp_Department_Name>Printing</Emp_Department_Name>";
            $myXMLData = $myXMLData . "<Emp_Location_Name>VEEPEE INDUSTRIES LIMITED</Emp_Location_Name>";
            $myXMLData = $myXMLData . "<Emp_Designation_Name>HRM</Emp_Designation_Name>";
            $myXMLData = $myXMLData . "<Category_Name>JUNIOR</Category_Name>";
            $myXMLData = $myXMLData . "<PaymentMode>Bank</PaymentMode>";
            $myXMLData = $myXMLData . "</EmployeeImportModel>";
      } 
    }
$myXMLData = $myXMLData . "</EmployeeDataList>";
	 $myXMLData1 = "<EmployeeImportData>
                            <CompanyCode>VPIL</CompanyCode>
                            $myXMLData
                    </EmployeeImportData>";
    
    $xml = simplexml_load_string($myXMLData1) or die("Error: Cannot create object");

    $post_data = array(
        "xml" => $myXMLData1,
    );

    $stream_options = array(
        'http' => array(
            'method' => 'POST',
            'header' => "Content-type: application/xml",
            'content' => $myXMLData1,
        ),
    );
    $posturl = $_POST['url'];
    $url = $posturl . "/api/PayMaster/EmployeeImport";
        //$url = "http://192.168.100.208:8082/api/PayMaster/EmployeeImport";
    $context = stream_context_create($stream_options);

    $response = file_get_contents($url, null, $context);
        //echo "<pre>";print_R($response);
    $rss = new SimpleXmlElement($response);
        //echo "<pre>";print_R($rss);
    if ($rss->Status == 200) {
        print "<center>";
        echo "<h3>Data Pushed Successfully.</h3>";
        print "</center>";
    } else {
        $hdk = 0;
        $countArray = sizeof($rss->ErrorList->ErrorContractModel, $mode);
        for ($hd = 1; $hd <= $countArray; $hd++) {
            print "<center>";
            echo "<h3>".$rss->ErrorList->ErrorContractModel[$hdk++]->Message . "</h3>"."<br><br>";
            print "</center>";
        }
    }
}