<?php

//ob_start("ob_gzhandler");

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

//$mac = getMAC();
$MAC = exec('getmac');
$mac = strtok($MAC, ' ');
$cui = shell_exec("echo | {$_SERVER["SystemRoot"]}\System32\wbem\wmic.exe path win32_processor get processorid");
$macid = str_replace("-", "", $mac);
//$macid = str_replace("-","",substr($mac[3], 0, 17));
$macAddress = substr($mac[3], 0, 17);
$processorids = str_replace("ProcessorId", "", $cui);
$processorid = str_replace("\r\n", "", substr($processorids, 0, 25));
$macprocessor = $macid . "--" . $processorid;
$macprocessorid = str_replace(" ", "", $macprocessor);

$computerName = getenv('COMPUTERNAME');
$ipaddress = getHostByName(php_uname('n'));
// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}
$act = $_GET["act"];

$companyname = $_POST['companyname'];
$cmpdetail1 = $_POST['cmpdetail1'];
$cmpdetail2 = $_POST['cmpdetail2'];
$serialno = $_POST['serialno'];
$password = $_POST['password'];
$emailid = $_POST['emailid'];
$contactperson = $_POST['contactperson'];
$machineKey = $_POST['machinekey'];

$license = array(
    'CustomerName' => $companyname,
    'ProductName' => 'VTIME',
    'ServiceProductKey' => 'VTIME',
    'RequestIP' => $ipaddress,
    'HostName' => $computerName,
    'PublicKey' => 'ESNLProducts',
    'SerialNo' => $serialno,
    'Password' => $password,
    'MailId' => $emailid,
    'ContactPerson' => $contactperson,
    'MacProcesserId' => encryptDecrypt($machineKey),
//    'MacProcesserId' => 'E06995779949--BFEBFBFF000206A7',
    'ProjectKey' => 'VV'
);

if ($license) {
    $data = json_encode($license);
    $licenseDetailData = array("LicenseDetails" => base64_encode($data));
}

// define("API_URL", "http://license.bitplus.in/LicenseService_Other");
// $json_url = API_URL."/License/GetLicense";
$json_url = API_URL . "/licenseapi/GetLicense";
//jSON String for request
$json_string = json_encode($licenseDetailData);
// Initializing curl
$ch = curl_init($json_url);

// Configuring curl options
$options = array(
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => array('Content-type: application/json'),
    CURLOPT_POSTFIELDS => $json_string
);

// Setting curl options
curl_setopt_array($ch, $options);

// Getting results
$result = curl_exec($ch); // Getting jSON result string

$jsondata = json_decode($result);
//echo "<pre>";print_R($jsondata);die;
$machineKey = encryptDecrypt($_POST['machinekey']);
$userQuery = "SELECT * FROM usermaster where Username='admin'";
$userData = selectData($conn, $userQuery);

if ($jsondata->IsValid == 1 && empty($jsondata->ErrorList) && $machineKey == $macprocessorid) {
//if($machineKey == $macprocessorid){
    $responseData = json_decode(base64_decode($jsondata->response));
    if ($responseData->ResposeStatus == 1 && $responseData->Message == 'Successfull' && !empty($responseData->Keys)) {
        $responseKey = json_decode(base64_decode($responseData->Keys));
        $CustomerName = $responseKey->CustomerName;
        $responseKey->SerialNo;
        $SerialNo = encryptDecrypt($responseKey->SerialNo);
        $MachineKey = $responseKey->MachineKey;
//        $LicenseDetail = json_decode($responseData->Keys);
        $LHistoryDetail = encryptDecrypt($result);
        $StartDate = $responseKey->Dates->StartDate;
        $EndDate = date("d/m/Y", strtotime($responseKey->Dates->EndDate));
        $MacProcesserId = encryptDecrypt($responseData->MacProcesserId);
        $LicenseHistoryType = $_POST['regi'];
        $CSysDate = date('Ymd');
        $Password = encryptDecrypt($_POST['password']);
        $MailId = $_POST['emailid'];
        $ContactPerson = $_POST['contactperson'];
        $macaddfind = substr(chunk_split(substr($responseData->MacProcesserId, 0, 12), 2, '-'), 0, 17);
        $LicenseDetail = array(
            "CompanyName" => $responseKey->CustomerName,
            "SerialNo" => $responseKey->SerialNo,
            "MacProcessorId" => $responseData->MacProcesserId,
            "StartDate" => $responseKey->Dates->StartDate,
            "EndDate" => $responseKey->Dates->EndDate,
//            "EndDate" => '31-Jan-2021',
            "NoOfUser" => $responseKey->Numeric->NoOfUser,
        );

        $conn1 = mysqli_connect("localhost", "root", "namaste", "unis");
//        echo "<pre>";print_R($LicenseDetail);die;
        $CompanyName = $LicenseDetail['CompanyName'];
        $SerialNo = $LicenseDetail['SerialNo'];
        $MacProcessorId = $LicenseDetail['MacProcessorId'];
        $StartDate = $LicenseDetail['StartDate'];
        $EndDate = $LicenseDetail['EndDate'];
        $NoOfUser = $LicenseDetail['NoOfUser'];

        // Check if SerialNo already exists
        $checkQuery = "SELECT * FROM LicenceDetails WHERE SerialNo = '$SerialNo'";
        $checkResult = mysqli_query($conn1, $checkQuery);
        
        if (mysqli_num_rows($checkResult) > 0) {
            // SerialNo exists, update the record
            $updateQuery = "UPDATE LicenceDetails 
                            SET CompanyName = '$CompanyName', 
                                MacProcessorId = '$MacProcessorId', 
                                StartDate = '$StartDate', 
                                EndDate = '$EndDate', 
                                NoOfUser = $NoOfUser 
                            WHERE SerialNo = '$SerialNo'";
            $result = mysqli_query($conn1, $updateQuery);
        } else {
            $licenseQuery = "INSERT INTO LicenceDetails(CompanyName, SerialNo, MacProcessorId, StartDate, EndDate, NoOfUser) VALUES "
                    . "('$CompanyName', '$SerialNo', '$MacProcessorId', '$StartDate', '$EndDate', $NoOfUser)";
            $LicenseUpdate = updateIData($conn1, $licenseQuery, true);
        }
        $LicenseDetailJsonData = json_encode($LicenseDetail);
        $encLicenseData = encryptDecrypt($LicenseDetailJsonData);
        $encodeLicenseData = base64_encode($encLicenseData);

        $insertHistory = "INSERT INTO licensehistory (CoCode,LHistoryType,LHistoryMachineKey,"
                . "LHistoryDetail,CSysDate,Login_C_Id,SerialNo,"
                . "Password,MailId,ContactPerson) VALUES ('" . encryptDecrypt($CustomerName) . "','$LicenseHistoryType','$MacProcesserId',"
                . "'$LHistoryDetail','$CSysDate','$userData[0]',"
                . "'$SerialNo','$Password','$MailId','$ContactPerson')";
        $InsertHistory = updateIData($iconn, $insertHistory, true);

        $updatenamedate = "UPDATE OtherSettingMaster SET CompanyName = '$CustomerName',CompanyDetail1 = '$cmpdetail1',CompanyDetail2='$cmpdetail2', MACAddress = '" . encryptDecrypt($macaddfind) . "',CompanyDetail3='" . encryptDecrypt(base64_encode(0)) . "',CompanyDetail4='" . $encodeLicenseData . "'";
        $updatedata = updateIData($iconn, $updatenamedate, true);
        header('Location: Registration.php?msg=' . base64_encode("License Registered Successfully. You will require to re-login."));
    }
} else {
    $hdk = 0;
    $countArray = sizeof($jsondata->ErrorList, $mode);
    for ($hd = 1; $hd <= $countArray; $hd++) {
        header('Location: Registration.php?msg_err=' . base64_encode($jsondata->ErrorList[$hdk++]->ErrorMessages[0]));
    }
}
?>