<?php 
ob_start("ob_gzhandler");
error_reporting(E_ALL);
ini_set('display_errors', '1');
include "Functions.php";

// Start session only if necessary
//session_start();

// Establish the database connection
$connection = openConnection();

// Get parameters from GET or POST
$act = $_GET["act"] ?? null;
$message = $_GET["message"] ?? null;
$url = $_GET["url"] ?? $_POST["url"] ?? null;
$txtUsername = $_POST["txtUsername"] ?? null;
$txtPassword = $_POST["txtPassword"] ?? null;
$lstUserType = $_POST["lstUserType"] ?? null;

if ($act === "login") { 
    // Validate input
    if (empty($txtUsername) || empty($txtPassword)) {
        $message = "Blank Username and/or Password";
    } else {
        // Login function
        $error = login($connection, $txtUsername, $txtPassword, $lstUserType);

        // Error handling using switch case for better readability
        switch ($error) {
            case 0:
                header("Location: " . ($url ?: "Dashboard.php"));
                exit;
            case 1:
                $message = "Script Modification Error";
                break;
            case 2:
                $message = "Invalid Terminal Error";
                break;
            case 3:
                $message = "Invalid Username and/or Password";
                break;
            case 4:
                $message = "Service Expired";
                break;
            case 5:
                $message = "Password Period Limit Expired";
                break;
            default:
                $message = "Unknown error occurred";
        }
    }
} elseif ($act === "forgotpassword") {
    // Use prepared statement to avoid SQL injection
    $query = "SELECT Username, Password FROM UserMaster WHERE Username = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("s", $txtUsername);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if (!$result) {
        $message = "Invalid Username. Please try again.";
    } else {
        $password = decryptString($result['Password']);
        $subject = "Password Retrieval";
        $body = "Dear $txtUsername,<br><br>Recently you requested your password to be sent through the 'Forgot Password' section on the site.<br>Your Password is $password<br><br>If you did not request for the password, kindly report immediately by replying to this Email.";
        
        if (send_mail("", $result['Username'], "", "", $subject, $body, "", "")) {
            $message = "The Password has been mailed to your Email Address.";
        } else {
            $message = "The Request could not be processed. Please try again later.";
        }
    }
} elseif ($act === "signout") {
    session_start();
    session_destroy();
}

        
// Fetch other settings
$query = "SELECT MACAddress, ClientLogo FROM OtherSettingMaster";
$osm_result = selectData($connection, $query);

$this_mac = $osm_result[0];
//echo $this_mac = base64_decode($osm_result[1]);die;

if(!empty(getRegister($this_mac, 7))){
    $targetDate = date('Y-m-d', strtotime(getRegister($this_mac, 2)));
    $today = date('Y-m-d');
    $dateDiff = date_diff(date_create($today), date_create($targetDate));
    if($dateDiff->days <= 15){
        $message = "<b>Dear Customer Your Licence Will Expire In ".$dateDiff->days." Days. Please Contact Endeavour.</b>";
    }
}       
        
// Close the database connection
$connection->close();
?>