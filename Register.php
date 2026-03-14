<?php

ob_start("ob_gzhandler");
set_time_limit(0);
error_reporting(E_ALL);
ini_set('display_errors', 1);
//include "Functions.php";
//echo "HEY";die;
//$conn = openConnection();
//$iconn = openIConnection();
$conn = mysqli_connect("localhost", "root", "namaste", "access");
//$MAC = exec('getmac');
//$mac = getMAC();
function encryptDecrypt($str) {
    $str = str_replace("-+-+-+", "'", $str);
    $str = str_replace("@@@@@", "�", $str);
    $ky = "DOANKHENBARAHAATH";
    $ky = str_replace(chr(32), "", $ky);
    if (strlen($ky) < 8) {
        exit("key error");
    }
    $kl = strlen($ky) < 32 ? strlen($ky) : 32;
    $k = array();
    for ($i = 0; $i < $kl; $i++) {
        $k[$i] = ord($ky[$i]) & 31;
    }
    $j = 0;
    for ($i = 0; $i < strlen($str); $i++) {
        $e = ord($str[$i]);
        $str[$i] = $e & 224 ? chr($e ^ $k[$j]) : chr($e);
        $j++;
        $j = $j == $kl ? 0 : $j;
    }
    $str = str_replace("'", "-+-+-+", $str);
    $str = str_replace("�", "@@@@@", $str);
    return $str;
}
function encryptString($data) {
    $data = convert_uuencode($data);
    $data = strrev($data);
    return $data;
}
$register = false;

//for ($i = 0; $i < count($mac); $i++) { 
//    if ($mac[$i] != "" && substr($mac[$i], 0, 17) == getRegister(encryptDecrypt(substr($mac[$i], 0, 17)), 0)) {
//        $query = "UPDATE OtherSettingMaster SET MACAddress = '" . encryptDecrypt(substr($mac[$i], 0, 17)) . "', LockDate = '20170101' WHERE SettingID = 1";
//        updateIData($iconn, $query, true);
//        $register = true;
//        break;
//    }
//}

/*Start*/

//$MAC = exec('getmac');
//$mac = strtok($MAC, ' ');
//$cui = shell_exec("echo | {$_SERVER["SystemRoot"]}\System32\wbem\wmic.exe path win32_processor get processorid");
//
////$macid = str_replace("-", "", substr($mac[3], 0, 17));
//$macid = str_replace("-", "", $mac);
//$processorids = str_replace("ProcessorId", "", $cui);
//$processorid = str_replace("\r\n", "", substr($processorids, 0, 25));
//$macprocessor = $macid . "--" . $processorid;
//$macprocessorid = str_replace(" ", "", $macprocessor);
//$macaddfind = substr(chunk_split(substr($macprocessorid, 0, 12), 2, '-'), 0, 17);

function getActiveMacAddress() {
    $output = [];
    exec('getmac', $output);

    foreach ($output as $line) {
        // Extract MAC address (format: XX-XX-XX-XX-XX-XX)
        if (stripos($line, 'Media disconnected') !== false) {
            continue;
        }

        if (preg_match('/([0-9A-Fa-f]{2}-){5}[0-9A-Fa-f]{2}/', $line, $matches)) {
            return $matches[0]; // Return the first found MAC address
        }
    }
    return "MAC Address not found!";
}

// Get and display MAC Address
$mac = getActiveMacAddress();
//
$LicenseDetail = array(
            "CompanyName" => 'Endeavour Africa',
            "SerialNo" => '12345',
//            "MacProcessorId" => base64_encode($macprocessorid),
            "StartDate" => date("Y-m-d"),
            "EndDate" => date("Y-m-d", strtotime("+2 day")),
//            "EndDate" => '2025-01-01',
            "NoOfUser" => 1,
        );

$LicenseDetailJsonData = json_encode($LicenseDetail);
$encLicenseData = encryptDecrypt($LicenseDetailJsonData);
$encodeLicenseData = base64_encode($encLicenseData);

////MACAddress = '" . encryptDecrypt($macaddfind) . "',
$updatenamedate = "UPDATE OtherSettingMaster SET MACAddress = '" . encryptDecrypt($mac) . "'";
//$updatedataResult = updateIData($iconn, $updatenamedate, true);
$updatedataResult = mysqli_query($conn, $updatenamedate);

$updatedetail= "UPDATE OtherSettingMaster SET CompanyName = 'Endeavour Africa',"
        . "CompanyDetail1 = 'Lagos',CompanyDetail2='Nigeria', "
        . "CompanyDetail3='" . encryptDecrypt(base64_encode(0)) . "',"
        . "CompanyDetail4='" . $encodeLicenseData . "' WHERE SettingID = 1";
//$updatedeResult = updateIData($iconn, $updatedetail, true);
$updatedeResult = mysqli_query($conn, $updatedetail);

//echo "HEY";die;
//if ($register == false) {
////    $query = "UPDATE OtherSettingMaster SET MACAddress = '" . encryptDecrypt("DE-MO-VE-RS-IO-NN") . "', CompanyName = 'DEMO VERSION', CompanyDetail1 = 'Expires: " . displayDate($config["EXPIRY_DATE"]) . "', CompanyDetail2 = '', LockDate = '20150101', TCount = '" . encryptDecrypt(strrev("DE-MO-VE-RS-IO-NN") . "-100") . "'";
//    $query = "UPDATE OtherSettingMaster SET MACAddress = '" . encryptDecrypt("D0-AB-D5-38-45-46") . "', CompanyName = 'ENDEAVOUR AFRICA', CompanyDetail1 = 'Expires: " . displayDate($config["EXPIRY_DATE"]) . "', CompanyDetail2 = '', LockDate = '20150101', TCount = '" . encryptDecrypt(strrev("D0-AB-D5-38-45-46") . "-100") . "'";
//    updateIData($iconn, $query, true);
//}
if($conn)
{
    $new_password ="EAL@2020";
    $username = "virdi";
    $query = "UPDATE UserMaster SET Userpass = '" . encryptString($new_password) . "' WHERE Username = '" . $username . "'";
//    updateIData($iconn, $query, true);
    mysqli_query($conn, $query);
    
    $query = "UPDATE UserMaster SET Usermail = 'care.nig@endeavourafrica.com' WHERE Usermail like '%@lagosmart.com%'";
//    updateIData($iconn, $query, true);
    mysqli_query($conn, $query);
}
header('Location: Login.php');
?>