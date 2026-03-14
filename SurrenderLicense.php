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
//if (!checkSession($userlevel, $current_module)) {
//    header("Location: " . $config["REDIRECT"] . "?url=LicenseDetail.php&message=Session Expired or Security Policy Violated");
//}
$conn = openConnection();

$surrenderQuery = "SELECT * FROM othersettingmaster";
$surrenderResult = selectData($conn, $surrenderQuery);
//echo "<pre>";print_R($surrenderResult);
$surrenderDatadecode = base64_decode($surrenderResult[75]);
$surrenderDataencdec = encryptDecrypt($surrenderDatadecode);
$surrenderData = json_decode($surrenderDataencdec);
$CoCode = $surrenderData->CompanyName;
$SerialNo = $surrenderData->SerialNo;
?>
<html>
    <head>
        <title>Surrender License</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="resource/css/font-awesome.min.css">
        <!--<link rel="stylesheet" href="hitesh/font-awesome/font-awesome.min.css">-->
        <link rel="stylesheet" href="resource/css/bootstrap.min.css">
        <script src="resource/js/jquery.min.js"></script>
        <script src="resource/js/bootstrap.min.js"></script>
    <center><?php displayHeader($prints, true, false); ?></center>
    <center><?php displayLinks($current_module, $userlevel); ?></center>
    <style>
        pre{margin: -24px 0 0 26px;}
    </style>
</head>
<body>
    <div class="container timerow">
        <div class="row">
            <form name="surrender_form" method="post" action="SurrenderScript.php">
                <div class="col-lg-1"></div>
                <div class="col-lg-10 full-backgroung">
                    <div class="row top-space">
                        <div class="col-lg-1"></div>
                        <div class="col-lg-5">
                            <b>Surrender License</b>
                        </div>
                        <div class="col-lg-1"></div>
                    </div>
                    <div class="row">
                        <div class="col-lg-1"></div>
                        <div class="col-lg-10"><hr></div>
                        <div class="col-lg-1"></div>
                    </div>
                    <div class="row" id="online_regi">
                        <div class="col-lg-1"></div>
                        <div class="col-lg-11">
                            <div class="form-group">
                                <label for="companyname">Company Name:</label>
                                <?php echo $CoCode; ?>
                            </div>
                            <div class="form-group">
                                <label for="serialno">Serial No:</label>
                                <?php echo $SerialNo; ?>
                            </div>
                            <div class="form-group">
                                <label for="remarks">Remarks*: </label>
                                <textarea class="form-control" name="remarks" required="required"></textarea>
                            </div>
                            <input type="submit" name="ok" onclick="return  confirm('Are you Sure you want to Surrender license for below Company.\n\n Company Name : <?php echo $CoCode; ?>')" class="btn btn-default" value="Ok" />
                            <input type="reset" name="cancel" class="btn btn-primary" value="Cancel" />
                        </div>
                        <div class="col-lg-3"></div>
                    </div>
                    <div class="row top-space"></div>
                </div>
            </form>
        </div>
    </div>
<?php if (isset($_GET['msg']) || isset($_GET['msg_err'])) { ?>
        <div id="myModal" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <?php if(isset($_GET['msg'])){ ?>
                        <a href="Login.php" type="button" class="close">&times;</a>
                        <?php }else{ ?>
                        <a href="SurrenderLicense.php" type="button" class="close">&times;</a>
                        <?php } ?>
                        <h4 class="modal-title">Message</h4>
                    </div>
                    <div class="modal-body">
                        <p><?php if($_GET['msg']) { echo base64_decode($_GET['msg']); }else{ echo base64_decode($_GET['msg_err']); } ?></p>
                    </div>
                    <div class="modal-footer">
                        <?php if(isset($_GET['msg'])){ ?>
                        <a href="Login.php" class="btn btn-default">OK</a>
                        <?php }else{ ?>
                        <a href="SurrenderLicense.php" class="btn btn-default">OK</a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
<?php } ?>
    <script>
        $(document).ready(function () {
            $("#myModal").modal('show');
        });
        $(".hide-modal").click(function () {
            $("#adapter").modal('show');
        });
    </script>
</body>
</html>