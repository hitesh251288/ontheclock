<?php

/*
| -----------------------------------------------------
| PRODUCT NAME: 	TimeMaster
| -----------------------------------------------------
| AUTHOR:			Endeavour Developers Team
| -----------------------------------------------------
| EMAIL:			info@esnl.com
| -----------------------------------------------------
| COPYRIGHT:		RESERVED BY Timemaster
| -----------------------------------------------------
| WEBSITE:			https://www.endeavourafrica.com/
| -----------------------------------------------------
*/

ini_set('memory_limit', '5120M'); 
ob_start("ob_gzhandler");
error_reporting(0);
set_time_limit(0);
include "Functions.php";

//var_dump($argv);
$csv = $argv[1];
$file_name = "PayMaster-Attendance-" . insertToday() . "" . getNow() . ".csv";
$conn = openConnection();
$iconn = openIConnection();
$jconn = openIConnection();
$kconn = openIConnection();
$lconn = openIConnection();

$query = "SELECT LockDate, MACAddress, DBType, DBIP, DBName, DBUser, DBPass FROM OtherSettingMaster";
$main_result = selectData($conn, $query);
$txtLockDate = $main_result[0];
$lstDBType = $main_result[2];
$txtDBIP = $main_result[3];
$txtDBName = $main_result[4];
$txtDBUser = $main_result[5];
$txtDBPass = $main_result[6];
$txtMACAddress = $main_result[1];

//if (checkMAC($conn) == false) {
//    print "Un Registered Application. Process Terminated.";
//    exit;
//}

// select from daymaster table the needed fields
$dayMasterQuery = "SELECT tenter.ed, tenter.e_id, tenter.e_date, tenter.e_time, tenter.g_id, tuser.id, tuser.name, tuser.idno, tuser.f1, tuser.dept, tuser.company, tuser.remark, tuser.group_id, tgroup.name, tgate.name from tenter JOIN tuser ON tenter.e_id = tuser.id JOIN tgroup ON tuser.group_id = tgroup.id JOIN tgate ON tenter.g_id = tgate.id order by tenter.ed DESC LIMIT 5000";

$dayMasterResult = mysqli_query($conn, $dayMasterQuery);
while ($dayMasterRes = mysqli_fetch_array($dayMasterResult)) {
    // echo "<pre>";print_R($dayMasterRes); die;
    $dayMasterID[] = $dayMasterRes[0];
    $dayMasterData[] = $dayMasterRes;
}
//var_dump(count($dayMasterData)); die;
    //  echo "<pre>";print_R($dayMasterData); die;
/*$myServer = "medlog.database.windows.net";
$myUser = "sazure";
$myPass = "gSf_W4St1";
$myDB = "medlog_att";

//connection to the database
$dbhandle = mssql_connect($myServer, $myUser, $myPass)
  or die("Couldn't connect to SQL Server on $myServer");*/

/*try {

    $conn = new PDO("sqlsrv:server = tcp:medlog.database.windows.net,1433; Database = medlog_att", "sazure", "gSf_W4St1");

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

}

catch (PDOException $e) {

    print("Error connecting to SQL Server.");

    die(print_r($e));

}*/

//echo "Con: ".$oconn = mssql_connection("tcp:medlog.database.windows.net,1433", "medlog_att", "sazure", "gSf_W4St1"); 
//echo "Con: ".$oconn = mssql_connection("MEDLOGNGPC529", "medlog_att", "sa", "admin@123"); 
//die;
$serverName = "medlog.database.windows.net,1433"; //serverName\instanceName
$connectionInfo = array( "Database"=>"medlog_att", "UID"=>"sazure@medlog.database.windows.net", "PWD"=>"gSf_W4St1");
$connectSQL = sqlsrv_connect( $serverName, $connectionInfo);

/*
if($connectSQL) {
     echo "Connection established.<br />";
}else{
     echo "Connection could not be established.<br />";
     echo "<pre>";die( print_r( sqlsrv_errors(), true));
}die;*/
/*$oconn = mssql_connection($txtDBIP, $txtDBName, $txtDBUser, $txtDBPass);
echo "Con: ".$oconn = mssql_connection("medlog.database.windows.net,1433", "medlog_att", "sazure@medlog.database.windows.net", "gSf_W4St1"); */
$query = "SELECT * FROM dbo.medlog_daymaster";
//$result = mssql_query($query, $oconn);
$result = sqlsrv_query($query, $connectSQL);
if (sqlsrv_num_rows($result) > 0) { 
    while ($res = sqlsrv_fetch_array($result)) {
        $dayMasterIDS[] = $res[1];
    }
}else {

	//select all data in medlog table
	$getData = "SELECT * FROM dbo.medlog_rawlog order by TDate desc LIMIT 5000";
	$result = sqlsrv_query($connectSQL, $getData);

	while ($row = sqlsrv_fetch_array($result)) {
		//echo "<pre>";print_R($row); die;
    	   $currentMedlogData[] = $row;		   
	}
	//echo "<pre>";print_R($currentMedlogData);die;
	//  pass into an array so i can compare the difference
	//echo "<pre>";print_R($currentMedlogData); die;
	foreach($dayMasterData as $row){
	//echo "<pre>";print_R($row);
	//$arrayData[] = $row;
		$arrayData[] = array(
		'0' => $row[0],
		'id' => $row[0],
		'1' => $row[1],
		'e_id' => $row[1],
		'2' => $row[8],
		'Genesis_id' => $row[8],
		'3' => $row[6],
		'Name' => $row[6],
		'4' => $row[7],
		'Emp_code' => $row[7],
		'5' => $row[9],
		'Dept' => $row[9],
		'6' => $row[10],
		'Div_Desg' => $row[10], 
		'7' => $row[11],
		'Rmk' => $row[11],
		'8' => $row[13],
		'Tshift' => $row[13],
		'9' => $row[2],
		'Tdate' => $row[2],
		'10' => $row[3],
		'Time' => $row[3],
		'11' => $row[14],
		'Terminal' => $row[14],
		);
	}
//die;
	// var_dump(count($currentMedlogData)); die;
	//loop through thier record 
	foreach($currentMedlogData as $data1){
    		$aTmp1[] = $data1['Id'];
				// echo "<pre>";print_R($aTmp1); die;

	}
	// if there is no data in medlog table make it an empty array
	if($aTmp1 == NULL){
	$aTmp1 = array();
	}
	
	//var_dump(count($arrayData)); die;
	// loop through our db
	foreach($arrayData as $data){
    		$aTmp2[] = $data['id'];
			// echo "<pre>";print_R($aTmp2); die;

	}
	//echo "<pre>";print_R($aTmp2); die;
	// check the difference in data
	$new_arrayT = array_diff($aTmp2,$aTmp1);
	//var_dump(count($new_arrayT)); die;
	//echo "<pre>";print_R($new_arrayT);die;
	//$dayMasterNewQuery = 'SELECT tenter.ed, tenter.e_id, tenter.e_date, tenter.e_time, tenter.g_id, tuser.id, tuser.name, tuser.idno, tuser.f1, tuser.dept, tuser.company, tuser.remark, tuser.group_id, tgroup.name, tgate.name from tenter JOIN tuser ON tenter.e_id = tuser.id JOIN tgroup ON tuser.group_id = tgroup.id JOIN tgate ON tenter.g_id = tgate.id WHERE tenter.ed IN (' . implode(',', array_map('intval', $new_arrayT)) . ') order by tenter.ed ASC';
	$dayMasterNewQuery = 'SELECT tenter.ed, tenter.e_id, tenter.e_date, tenter.e_time, tenter.g_id, tuser.id, tuser.name, tuser.idno, tuser.f1, tuser.dept, tuser.company, tuser.remark, tuser.group_id, tgroup.name, tgate.name from tenter JOIN tuser ON tenter.e_id = tuser.id JOIN tgroup ON tuser.group_id = tgroup.id JOIN tgate ON tenter.g_id = tgate.id WHERE tenter.ed IN (' . implode(',', array_map('intval', $new_arrayT)) . ') order by tenter.ed ASC';
	//$dayMasterNewQuery = 'SELECT tenter.ed, tenter.e_id, tenter.e_date, tenter.e_time, tenter.g_id, tuser.id, tuser.name, tuser.idno, tuser.f1, tuser.dept, tuser.company, tuser.remark, tuser.group_id, tgroup.name, tgate.name from tenter JOIN tuser ON tenter.e_id = tuser.id JOIN tgroup ON tuser.group_id = tgroup.id JOIN tgate ON tenter.g_id = tgate.id WHERE tenter.ed IN (' . implode(',', array_map('intval', $new_arrayT)) . ') AND tenter.e_id=882 AND tenter.e_date="20230526" order by tenter.ed ASC';
	$dayMasterNewResult = mysqli_query($conn, $dayMasterNewQuery);
	
	while ($dayMasterNewRes = mysqli_fetch_array($dayMasterNewResult)) {
   
    	$dayMasterNewID[] = $dayMasterNewRes[0];
    	$dayMasterNewData[] = $dayMasterNewRes;
 	//echo "<pre>";print_R($dayMasterNewRes); die;
	}

	//var_dump(count($dayMasterNewData)); die;

	//echo "<pre>";print_R($aTmp2); die;


	 //echo "<pre>"; print_R($dayMasterNewData);die;
	//stop
    foreach ($dayMasterNewData as $dayMasterAllData) {
        //echo "<pre>";
         //print_R($dayMasterAllData);	die;
	$sqlQuery = "select * from dbo.medlog_rawlog where e_id='$dayMasterAllData[1]' AND Tdate='$dayMasterAllData[2]' AND Time='$dayMasterAllData[3]'";
	$params = array();
	$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
	$sqlQueryData = sqlsrv_query($connectSQL, $sqlQuery, $params, $options );
	
	$sqlQueryResult = sqlsrv_num_rows($sqlQueryData);
	//echo "<pre>";print_R($sqlQueryResult);
	//echo "<br>";
	if($sqlQueryResult == 0){
	$sql = "INSERT INTO dbo.medlog_rawlog ( e_id, Genesis_id, Name, Emp_code, Dept, Div_Desg, Rmk, Tshift, Tdate, Time, Terminal) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
	//echo $sql = "INSERT INTO dbo.medlog_rawlog ( e_id, Genesis_id, Name, Emp_code, Dept, Div_Desg, Rmk, Tshift, Tdate, Time, Terminal) VALUES ('".$dayMasterAllData[1]."', '".$dayMasterAllData[8]."', '".$dayMasterAllData[6]."', '".$dayMasterAllData[7]."', '".$dayMasterAllData[9]."', '".$dayMasterAllData[10]."', '".$dayMasterAllData[11]."', '".$dayMasterAllData[13]."', '".$dayMasterAllData[2]."', '".$dayMasterAllData[3]."', '".$dayMasterAllData[14]."')";
	//echo "<br>";
	$params = array($dayMasterAllData[1], $dayMasterAllData[8], $dayMasterAllData[6], $dayMasterAllData[7], $dayMasterAllData[9], $dayMasterAllData[10], $dayMasterAllData[11], $dayMasterAllData[13], $dayMasterAllData[2], $dayMasterAllData[3], $dayMasterAllData[14]);
	//echo "<pre>";print_r($params);
	$stmt = sqlsrv_query($connectSQL, $sql, $params);
	//$stmt = sqlsrv_query($connectSQL, $sql);
	}
	if( $stmt === false ) {
	echo "<br>";
            echo "<h3>Opps! An Error occured from our end.</h3>";
     	die( print_r( sqlsrv_errors(), true));
	}    
        else{
	echo "<br>";
            echo "<h3> Data Migrated Successfully</h3>";
        }
	
    }
}

// ends
$DayMasterInsertData = array_diff($dayMasterID, $dayMasterIDS);

foreach ($DayMasterInsertData as $DayMasterInsertAllData) {
    $fetchQuery = "SELECT * FROM daymaster where DayMasterID IN($DayMasterInsertAllData)";
    $resultData = mysqli_query($conn, $fetchQuery);
    while ($resInsert = mysqli_fetch_array($resultData)) {
        $insertQuery = "INSERT INTO DayMaster (DayMasterId,e_id, TDate, Start_time, Close_time) VALUES (" . $resInsert[0] . "," . $resInsert[1] . "," . $resInsert[2] . "," . $resInsert[4] . "," . $resInsert[7] . ")";
        $success = mssql_query($insertQuery,$oconn);
        if($success){
            echo "<h3>Data Inserted Successfully.</h3>";
        }
	else{
	echo "<h3>Data Error.</h3>";
	}
    }
}