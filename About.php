<?php
ob_start("ob_gzhandler");
error_reporting(0);
date_default_timezone_set("Africa/Algiers");
include "Functions.php";
session_start();
$current_module = "25";
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
$ex3 = $_SESSION[$session_variable . "Ex3"];
if (!is_numeric($ex3)) {
    $ex3 = 0;
}
$ex4 = "";
if ($ex4 == "") {
    $ex4 = "120";
}
if (!is_numeric($ex4)) {
    $ex4 = 120;
}
$lstEmployeeStatus = "ACT";
$lstClockingType = "All";
$count = 0;
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=About.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$iconn = openIConnection();

$query = "Select * from othersettingmaster";
$result = mysqli_query($conn, $query);
$licenseData = mysqli_fetch_assoc($result);
$AboutDatadecode = base64_decode($licenseData['CompanyDetail4']);
$AboutDataencdec = encryptDecrypt($AboutDatadecode);
$AboutData = json_decode($AboutDataencdec);

// Fetch data from the API
function fetchDataFromAPI($apiUrl) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($httpStatus != 200) {
        die("Error fetching data: HTTP Status $httpStatus");
    }

    return json_decode($response, true);
}

// Insert data into the database
function insertDataIntoDatabase($data, $conn) {
    foreach ($data as $row) {
        $CompanyName = mysqli_real_escape_string($conn, $row['CompanyName']);
        $Cmpdetail1 = mysqli_real_escape_string($conn, $row['Cmpdetail1']);
        $Cmpdetail2 = mysqli_real_escape_string($conn, $row['Cmpdetail2']);
        $SerialNo = mysqli_real_escape_string($conn, $row['SerialNo']);
        $MacAddress = mysqli_real_escape_string($conn, $row['MacAddress']);
        $Password = mysqli_real_escape_string($conn, $row['Password']);
        $MacProcessorId = mysqli_real_escape_string($conn, $row['MacProcessorId']);
        $StartDate = mysqli_real_escape_string($conn, $row['StartDate']);
        $EndDate = mysqli_real_escape_string($conn, $row['EndDate']);
        $NoOfUser = mysqli_real_escape_string($conn, $row['NoOfUser']);

        $query = "INSERT INTO license_data (CompanyName, Cmpdetail1, Cmpdetail2, SerialNo, 
                    Password, MacProcessorId, StartDate, EndDate, NoOfUser)
                  VALUES ('$CompanyName', '$Cmpdetail1', '$Cmpdetail2', '$SerialNo', 
                    '$Password', '$MacProcessorId', '$StartDate', '$EndDate', '$NoOfUser')";

        if (!mysqli_query($conn, $query)) {
            echo "Error inserting data: " . mysqli_error($conn) . "\n";
        }
    }
}

if (isset($_POST['update'])) { 

    /* API Start */

    // API URL
//    $apiUrl = "http://127.0.0.1/licenseapi/fetchdata.php";
//    $apiUrl = "http://89.107.58.217:8010/licenseapi/fetchdata.php";
    $apiUrl = API_URL."/licenseapi/fetchdata.php";


//    $MAC = exec('getmac');
//    $systemMac = encryptDecrypt(strtok($MAC, ' '));
    $LicenseDetails = fetchDataFromAPI($apiUrl);

    foreach ($LicenseDetails as $LicenseDetail) { 
//        echo "<pre>";print_r($LicenseDetail);
        $CompanyName = $LicenseDetail['CompanyName'];
        $Cmpdetail1 = $LicenseDetail['Cmpdetail1'];
        $Cmpdetail2 = $LicenseDetail['Cmpdetail2'];
        $MacAddress = $LicenseDetail['MacAddress'];
        $SerialNo = $LicenseDetail['SerialNo'];
        $Password = $LicenseDetail['Password'];
        $MacProcessorId = $LicenseDetail['MacProcessorId'];
        $StartDate = $LicenseDetail['StartDate'];
        $EndDate = $LicenseDetail['EndDate'];
        $NoOfUser = $LicenseDetail['NoOfUser'];
        $Surrender = $LicenseDetail['Surrender'];
//        $Version = $LicenseDetail['Version'];
        $mac = encryptDecrypt(base64_decode($LicenseDetail['MacProcessorId']));
        $trimmedMac = str_replace("-", "", substr($mac, 0, 14));
        $formattedMac = implode("-", str_split($trimmedMac, 2));
        $APIMacId = $formattedMac;

        $LicenseDetailJsonData = json_encode($LicenseDetail);
        $encLicenseData = encryptDecrypt($LicenseDetailJsonData);
        $encodeLicenseData = base64_encode($encLicenseData);
        
        $licenseQuery = "SELECT MACAddress, CompanyDetail4 from othersettingmaster where MACAddress='" . $MacAddress . "'";
        $enddateUpdate = selectData($conn, $licenseQuery);
        
        if($enddateUpdate !== null && is_array($enddateUpdate) && count($enddateUpdate) > 0){
            $allData = json_decode(encryptDecrypt(base64_decode($enddateUpdate[1])));
            if (encryptDecrypt($MacAddress) == encryptDecrypt($enddateUpdate[0])) {
                //echo "<pre>";print_R(json_decode(encryptDecrypt(base64_decode($encodeLicenseData))));
                if ($enddateUpdate[1] === $encodeLicenseData) { 
                    header('Location: About.php?status=updated'); 
                    exit; 
                }
                if($Surrender == 0){
                    $updatenamedate = "UPDATE OtherSettingMaster SET CompanyName = '$CompanyName', CompanyDetail1 = '$Cmpdetail1',CompanyDetail2='$Cmpdetail2', CompanyDetail3='" . encryptDecrypt(base64_encode(0)) . "',CompanyDetail4='" . $encodeLicenseData . "' Where MACAddress='$enddateUpdate[0]'";
                    $updatedata = updateIData($iconn, $updatenamedate, true);
                    if ($updatedata == true) {
                        header('Location: About.php?status=success');
                    } else {
                        header('Location: About.php?status=updated');
                    }
                }else{
                    $surrenderQuery = "UPDATE OtherSettingMaster SET MACAddress = '',CompanyDetail3='".encryptDecrypt(base64_encode(4))."'";
                    $Surrenderdata = updateIData($iconn, $surrenderQuery, true);
                    if ($Surrenderdata == true) {
                        header('Location: Login.php?act=signout');
                    } 
                }
            } else {
                header('Location: About.php?status=macerror');
//                exit;
            }
        }       
    }
}

if ($prints != "yes") {
    include 'header.php';
    ?>
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-12 d-flex no-block align-items-center">
                <h4 class="page-title">About Us</h4>
                <div class="ms-auto text-end">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">
                                About Us
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <?php
}
print'<div class="container-fluid">
                <div class="row">
                        <div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12">
                            <div class="card">
                                    <div class="card-body table-responsive">';
?>
<div class="container timerow">
    <div class="row">
        <center><div id="statusMessage"></div></center>
        <div class="col-lg-1"></div>
        <div class="col-lg-10 full-backgroung">
            <div class="row top-space">  
                <div class="col-lg-1"></div>
                <div class="col-lg-5">
                    <b>Version : </b><a href="VersionInfo.php" target="_blank" style="color:inherit;"><?php echo $licenseData['Ex2']; ?></a>
                </div>
                <div class="col-lg-5">
                    <b>Serial No: </b><?php echo $AboutData->SerialNo . " (Professional)"; ?>
                </div>
                <div class="col-lg-1"></div>
            </div>
            <div class="row">
                <div class="col-lg-1"></div>
                <div class="col-lg-5"><b>Licensed to: </b><?php echo $AboutData->CompanyName; ?></div>
                <div class="col-lg-5"><b>Expiry Date: </b><?php echo $AboutData->EndDate; ?></div>
                <div class="col-lg-1"></div>
            </div>
            <div class="row">
                <div class="col-lg-1"></div>
                <div class="col-lg-10"><hr></hr></div>
                <div class="col-lg-1"></div>
            </div>
            <div class="row">
                <div class="col-lg-1"></div>
                <div class="col-lg-10"><u><b>Contact Information</b></u></div>
                <div class="col-lg-1"></div>
            </div>
            <div class="row top-space">
                <div class="col-lg-1"></div>
                <div class="col-lg-10">Endeavour Solution Nigeria Limited<br>14E, Industrial Street, Off Industrial Avenue, Ilupeju, Lagos, Nigeria</div>
                <div class="col-lg-1"></div>
            </div>
            <div class="row">
                <div class="col-lg-1"></div>
                <div class="col-lg-10"><b>Tel. :</b> +234 812 927 8533</div>
                <div class="col-lg-1"></div>
            </div>
            <div class="row">
                <div class="col-lg-1"></div>
                <div class="col-lg-10"><b>Email :</b> info@endeavourafrica.com</div>
                <div class="col-lg-1"></div>
            </div>
            <div class="row">
                <div class="col-lg-1"></div>
                <div class="col-lg-10"><b>Website :</b> www.endeavourafrica.com</div>
                <div class="col-lg-1"></div>
            </div>
            <div class="row">
                <div class="col-lg-1"></div>
                <div class="col-lg-10"><hr></hr></div>
                <div class="col-lg-1"></div>
            </div>
            <div class="row">
                <div class="col-lg-1"></div>
                <div class="col-lg-10"><b>Warning:</b> This computer program is protected  by copyright law and international treaties. Unauthorized reporduction or distribution of this program, or any portion of it, may result in severe civil and criminal penalties, and will be prosecuted to the maximum extent possible under law.</div>
                <div class="col-lg-1"></div>
            </div>

            <div class="row top-space">
                <div class="col-lg-1"></div>
                <div class="col-lg-5"><b>Distributed & Supported By</b><br><br><img src="img/end-logo.png" width="200"/></div>
                <!--<div class="col-lg-5"><b>Developed By</b><br><br><img src="img/bit-logo.png" width="200"/></div>-->
                <div class="col-lg-1"></div>
            </div>
            <div class="row">
                <div class="col-lg-1"></div>
                <div class="col-lg-10"><hr></hr></div>
                <div class="col-lg-1"></div>
            </div>
            <div class="row">
                <div class="col-lg-1"></div>

                <!--<div class="col-lg-2"><a href="LicenseDetail.php" class="btn btn-primary">License Detail</a></div>-->
                <div class="col-lg-2">
                    <form method="post" action="About.php">
                        <input type="hidden" name="enddate" value="<?php echo $AboutData->Enddate; ?>">
                        <input type="submit" name="update" value="Update" class="btn btn-primary">
                    </form>
                </div>
                <!--<div class="col-lg-2"><a href="#" class="btn btn-primary">System Info.</a></div>-->
                <div class="col-lg-7"></div>
            </div>
            <div class="row top-space"></div>
        </div>
    </div>
</div>
</div>
</div>
</div>
</div>
</div>
<?php include 'footer.php'; ?>
<script>
// Parse URL parameters
    const params = new URLSearchParams(window.location.search);
    const status = params.get('status');

// Get the statusMessage container
    const statusMessage = document.getElementById('statusMessage');

// Show the appropriate message
    if (status === 'success') {
        statusMessage.innerHTML = '<div class="alert alert-success message success" role="alert">Your license is successfully updated.</div>';
    } else if (status === 'error') {
        statusMessage.innerHTML = '<div class="alert alert-danger message error" role="alert">There is problem to update license.</div>';
    } else if (status === 'macerror') {
        statusMessage.innerHTML = '<div class="alert alert-danger message error" role="alert">MACAddress not found.</div>';
    }
    if (status === 'updated') {
        statusMessage.innerHTML = '<div class="alert alert-success message success" role="alert">Your license is up to date.</div>';
    }
</script>
