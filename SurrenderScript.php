<?php
ob_start("ob_gzhandler");
//header('Content-Type: application/json; charset=utf-8');
error_reporting(0);
date_default_timezone_set("Africa/Algiers");
set_time_limit(0);
include "Functions.php";
session_start();
ini_set("allow_url_fopen", 1);
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];

$conn = openConnection();
$iconn = openIConnection();

$surrenderQuery = "SELECT CompanyDetail4 FROM othersettingmaster";
$surrenderResult = selectData($conn, $surrenderQuery);
$surrenderResultDatadecode = base64_decode($surrenderResult[0]);
$surrenderResultDataencdec = encryptDecrypt($surrenderResultDatadecode);
$surrenderResultData = json_decode($surrenderResultDataencdec);

$CompanyName = $surrenderResultData->CompanyName;
$MacProcesserId = $surrenderResultData->MacProcessorId;
$SerialNo = $surrenderResultData->SerialNo;
$HostName = getenv('COMPUTERNAME');
$RequestIp = getHostByName(php_uname('n'));
$remarks = $_POST['remarks'];
$licenseSurrender = array(
  "CustomerName" => $CompanyName,
  "ProductName" => "VTIME",
  "ServiceProductKey" => "VTIME",
  "RequestIP" => $RequestIp,
  "HostName" => $HostName,
  "PublicKey" => "ESNLProducts",
  "SerialNo" => $SerialNo,
  "MacProcesserId" => $MacProcesserId,
  "ProjectKey" => "VV",
  "Remarks" => $remarks,
);


if($licenseSurrender){
    $data = json_encode($licenseSurrender);
    $licenseSurrenderData = array("LicenseDetails" => base64_encode($data));
}

$json_url = API_URL."/licenseapi/SurrLic";

// jSON String for request
$json_string = json_encode($licenseSurrenderData);

// Initializing curl
$ch = curl_init($json_url);

// Configuring curl options
$options = array(
CURLOPT_RETURNTRANSFER => true,
CURLOPT_HTTPHEADER => array('Content-type: application/json') ,
CURLOPT_POSTFIELDS => $json_string
);

// Setting curl options
curl_setopt_array( $ch, $options );

// Getting results
$result = curl_exec($ch); // Getting jSON result string

$jsondata = json_decode($result);

$responseData = json_decode(base64_decode($jsondata->response));
//echo "<pre>";print_R($responseData);
//echo "HEY";die;
$responseStatus = $responseData->ResposeStatus;
$responseMessage = $responseData->Message;
if($jsondata->IsValid == 1 && empty($jsondata->ErrorList) && $responseStatus == 1 && $responseMessage=='Successfull'){
    $surrenderQuery = "UPDATE OtherSettingMaster SET MACAddress = '',CompanyDetail3='".encryptDecrypt(base64_encode(4))."'";
    $updatedata = updateIData($iconn, $surrenderQuery, true);
    header('Location: SurrenderLicense.php?msg='.base64_encode("Your license is surrendered Successfully."));
}else{
    $hdk = 0;
    $countArray = sizeof($jsondata->ErrorList, $mode);
    for ($hd = 1; $hd <= $countArray; $hd++) {
        header('Location: SurrenderLicense.php?msg_err='.base64_encode($jsondata->ErrorList[$hdk++]->ErrorMessages[0]));
    }
}