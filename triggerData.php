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

$oconn = mssql_connection('DESKTOP-I1EBN6N\SQLEXPRESS', 'UCDB', 'sa', 'bit@123');
//$oconn = mssql_connection('TAI-BMS-030', 'UCDB', 'sa', 'Tabio$123');

/*****************Terminals******************/
$tsql = "SELECT terminal_id,name from dbo.terminals";
$tResult = mssql_query($tsql, $oconn);
while ($tRow = mssql_fetch_row($tResult)) {
    $tGateName[] = array('terminal_id'=>$tRow[0],'gatename'=>$tRow[1]);
}
for ($i = 0; $i < count($tGateName); $i++) {
    $fetchgateQuery = "SELECT * from tgate where name='".$tGateName[$i]['gatename']."'";
    $terminalResult = mysqli_query($conn, $fetchgateQuery);

    while ($terminalRow = mysqli_fetch_array($terminalResult)) {
        $tgateDetail[] = $terminalRow[1];
    }
}
for ($i = 0; $i < count($tGateName); $i++) {
    if (isset($tgateDetail[0]) != $tGateName[$i]['gatename']) {
        
        $tgateQuery = "INSERT INTO tgate (name) VALUES ('" . $tGateName[$i]['gatename'] . "')";
        //updateIData($iconn, $tgateQuery, true);
    }else{
        $gateUpdate = "UPDATE tgate SET name='" . $tGateName[$i]['gatename'] . "' WHERE id='" . $tGateName[$i]['terminal_id'] . "'";
        //updateIData($iconn, $gateUpdate, true);
    } 
}

/**********************Tenter**************************/
$sql = "SELECT CONVERT(VARCHAR(19), event_time, 120),user_id,terminal_id from dbo.auth_logs_202206";
$sResult = mssql_query($sql, $oconn);

while ($sqlexdb = mssql_fetch_row($sResult)) {

    $date = date("Ymd", strtotime($sqlexdb[0]));
    $time = date("his", strtotime($sqlexdb[0]));
    $userid = $sqlexdb[1];
    $tid = $sqlexdb[2];
    $sqlData[] = array('sdate' => $date,'stime' => $time,'userid' => $userid,'t_id'=>$tid);
}

foreach($sqlData as $allData){
    $mysql = "SELECT e_date,e_time,e_id,g_id from tenter where e_date ='".$allData['sdate']."' AND e_time = '".$allData['stime']."'";

    $mResult = mysqli_query($conn, $mysql);
    while($row = mysqli_fetch_array($mResult)){
    $mysqlData[] = array('mdate' => $row[0],'mtime' => $row[1],'e_id'=>$row[2],'g_id'=>$row[3]);

    }
}
//echo "<pre>";print_r($mysqlData);die;
for($i=0;$i<count($sqlData);$i++){
    if($sqlData[$i]['sdate']!=$mysqlData[$i]['mdate'] && $sqlData[$i]['stime']!=$mysqlData[$i]['mtime'] && $sqlData[$i]['userid']!=$mysqlData[$i]['e_id'] && $sqlData[$i]['t_id']!=$mysqlData[$i]['g_id']){

       $addQuery = "INSERT INTO tenter (e_date,e_time,g_id,e_id) VALUES ('".$sqlData[$i]['sdate']."','".$sqlData[$i]['stime']."','".$sqlData[$i]['t_id']."','".$sqlData[$i]['userid']."')";
       
        //$addResult = updateIData($iconn, $addQuery, true);
    }
}


/************************User data insert*********************************/

//$usql = "SELECT user_id,name,regist_at,message_code,unique_id,expire_at from dbo.users";
$usql = "SELECT u.user_id,u.name,u.regist_at,u.message_code,u.unique_id,u.expire_at,d.message from dbo.users u,dbo.user_messages d where u.message_code=d.message_id;";
$uResult = mssql_query($usql, $oconn);

while ($usqlexdb = mssql_fetch_row($uResult)) {

    $userid = $usqlexdb[0];
    $uname = $usqlexdb[1];
    $rdate = date("YmdHi", strtotime($usqlexdb[2]));
    $dept = $usqlexdb[6];
    $uniqueid = $usqlexdb[4];
    $datelimit = date("Ymd", strtotime($usqlexdb[2])).''.date("Ymd", strtotime($usqlexdb[5]));    

    $usqlData[] = array('userid' => $userid,'uname' => $uname,'dept'=>$dept,'register_date'=>$rdate,'unique_id'=>$uniqueid,'datelimit'=>$datelimit);
}
//echo "<pre>";print_R($usqlData);die;
foreach($usqlData as $uallData){
    $umysql = "SELECT id,name,dept from tuser where name ='".$uallData['uname']."' AND id = '".$uallData['userid']."'";

    $umResult = mysqli_query($conn, $umysql);
    while($urow = mysqli_fetch_array($umResult)){

    $userreg_date = date("Ymd", strtotime($urow[3]));
    $mysqluData[] = array('muser_id' => $urow[0],'muname' => $urow[1],'mdept'=>$urow[2]);

    }
}
for($i=0;$i<count($usqlData);$i++){

    if(isset($usqlData[$i]['userid'])!=$mysqluData[$i]['muser_id'] && isset($usqlData[$i]['uname'])!=$mysqluData[$i]['muname']){

        $adduserQuery = "INSERT INTO tuser (id,name,reg_date,datelimit,dept,F1) VALUES ('".$usqlData[$i]['userid']."','".$usqlData[$i]['uname']."','".$usqlData[$i]['register_date']."','".$usqlData[$i]['datelimit']."','".$usqlData[$i]['dept']."','".$usqlData[$i]['unique_id']."')";
        
        ///$adduResult = updateIData($iconn, $adduserQuery, true);         
    }else{       

        $updateuserquery = "UPDATE tuser SET name='".$usqlData[$i]['uname']."',datelimit='".'N'.$usqlData[$i]['datelimit']."',dept='".$usqlData[$i]['dept']."',reg_date='".$usqlData[$i]['register_date']."',F1='".$usqlData[$i]['unique_id']."' where id='".$usqlData[$i]['userid']."'";
        
        //$updateResult = updateIData($iconn, $updateuserquery, true);
    }
}