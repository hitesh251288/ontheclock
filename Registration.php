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
//$mac = getMAC();
$MAC = exec('getmac');
$mac = strtok($MAC, ' ');
$cui = shell_exec("echo | {$_SERVER["SystemRoot"]}\System32\wbem\wmic.exe path win32_processor get processorid");

//$macid = str_replace("-", "", substr($mac[3], 0, 17));
$macid = str_replace("-", "", $mac);
$processorids = str_replace("ProcessorId", "", $cui);
$processorid = str_replace("\r\n", "", substr($processorids, 0, 25));
$macprocessor = $macid . "--" . $processorid;
$macprocessorid = str_replace(" ", "", $macprocessor);


function GetMACS() {
    ob_start();
    system('getmac');
    $Content = ob_get_contents();
    ob_clean();
    return substr($Content, strpos($Content, '\\') - 20, 17);
}

$RegisterQuery = "SELECT * FROM othersettingmaster";
$registerResult = selectData($conn, $RegisterQuery);
$registerDatadecode = base64_decode($registerResult[76]);
$registerDataencdec = encryptDecrypt($registerDatadecode);
$registerData = json_decode($registerDataencdec);
?>
<html>
    <head>
        <title>Registration</title>
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
            <form name="registration_form" method="post" action="RegistrationScript.php">
                <div class="col-lg-1"></div>
                <div class="col-lg-10 full-backgroung">
                    <div class="row top-space">
                        <div class="col-lg-1"></div>
                        <div class="col-lg-5">
                            <!--<b>Company: BITPLUS DEMO</b>-->
                        </div>
                        <div class="col-lg-1"></div>
                    </div>
                    <div class="row">
                        <div class="col-lg-1"></div>
                        <div class="col-lg-10"><hr></div>
                        <div class="col-lg-1"></div>
                    </div>
                    <div class="row">
                        <div class="col-lg-1"></div>
                        <div class="col-lg-4"><input type="radio" name="regi" value="1" checked="checked"> Online Registration</div>
                        <!--<div class="col-lg-4"><input type="radio" name="regi" id="offlinereg" value="2"> Offline Registration</div>-->
                        <div class="col-lg-3"></div>
                    </div>
                    <div class="row">
                        <div class="col-lg-1"></div>
                        <div class="col-lg-10"><hr></div>
                        <div class="col-lg-1"></div>
                    </div>
                    <div class="row" id="online_regi">
                        <div class="col-lg-1"></div>
                        <div class="col-lg-11">
                            <?php if ($registerData->CompanyName) { ?>
                                Company Name:<input type="text" name="companyname" class="form-control" value="<?php echo $registerData->CompanyName; ?>" required="required" />
                            <?php } else { ?>
                                Company Name:<input type="text" name="companyname" class="form-control" value="" required="required"/>
                            <?php } ?>
                            Company Detail1:<input type="text" name="cmpdetail1" class="form-control" value="" />
                            Company Detail2:<input type="text" name="cmpdetail2" class="form-control" value="" />
                            Serial No:<input type="text" name="serialno" class="form-control" value="" required="required"/>
                            Password:<input type="password" name="password" class="form-control" value=""  required="required"/>
                            Expiry Date:<input type="date" name="enddate" class="form-control" value=""  required="required"/>
                            Mail Id:<input type="email" name="emailid" class="form-control" value=""  required="required"/>
                            Contact Person:<input type="text" name="contactperson" class="form-control" value="" /><br>
                            <input type="submit" name="onlineregister" class="btn btn-primary" value="Register" />
                        </div>
                        <div class="col-lg-3"></div>
                    </div>
                    <div class="row" id="offline_regi">
                        <div class="col-lg-1"></div>
                        <div class="col-lg-10">
                            Machine Key:<input type="text" name="machinekey" id="machineKey" class="form-control" value="<?php echo encryptDecrypt($macprocessorid); ?>" readonly="readonly"/><br>
                            Registration Key:<textarea name="registrationkey" class="form-control"></textarea><br>
                            <input type="submit" name="offlineregister" class="btn btn-primary" value="Register" />
                        </div>
                        <div class="col-lg-1" class="left">
                            <a href="#" class="hide-modal">Show</a>
                        </div>
                        <div class="col-lg-3"></div>
                    </div>
                    <div class="row top-space"></div>
                </div>
            </form>
        </div>
    </div>
    <div id="adapter" class="modal fade" role="dialog">
        <div class="modal-dialog" style="width:60%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Registration Key Detail</h4>
                </div>
                <div class="modal-body">
                    <!--<p>-->
                    <?php
                    $adapterDetail = shell_exec("echo | getmac  /v");
                    $allDetail = explode(PHP_EOL, $adapterDetail);
                    $i = 0;
                    foreach ($allDetail as $allDetails) {
                        ?>
                        <input type="checkbox" name="addmac" class="addmac" id="addmac" value="<?php echo encryptDecrypt(str_replace("-", "", GetMACS()) . "--" . str_replace(" ", "", $processorid)); ?>" /><?php echo "<pre>" . $allDetails . "</pre>"; ?><br>
                    <?php } ?>
                    <!--</p>-->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" id="btn_ok" data-dismiss="modal">OK</button>
                    <button type="button" class="btn btn-default" id="btn_close" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <?php if (isset($_GET['msg']) || isset($_GET['msg_err'])) { ?>
        <div id="myModal" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <?php if (isset($_GET['msg'])) { ?>
                            <a href="Login.php" type="button" class="close">&times;</a>
                        <?php } else { ?>
                            <a href="Registration.php" type="button" class="close">&times;</a>
                        <?php } ?>
                        <h4 class="modal-title">Message</h4>
                    </div>
                    <div class="modal-body">
                        <p><?php
                        if ($_GET['msg']) {
                            echo base64_decode($_GET['msg']);
                        } else {
                            echo base64_decode($_GET['msg_err']);
                        }
                        ?></p>
                    </div>
                    <div class="modal-footer">
                        <?php if (isset($_GET['msg'])) { ?>
                            <a href="Login.php" type="button" class="btn btn-default">OK</a>
                        <?php } else { ?>
                            <a href="Registration.php" class="btn btn-default">OK</a>    
    <?php } ?>
                    </div>
                </div>
            </div>
        </div>
<?php } ?>
    <script>
        $(document).ready(function () {
            $("#myModal").modal('show');
            $(".hide-modal").hide();
        });
        $('#offlinereg').click(function() {
        document.onkeyup = function (e) { 
            if (e.ctrlKey && e.altKey && e.shiftKey && e.which == 86) {
                $(".hide-modal").show();
                $(".hide-modal").click(function () {
                    var person = prompt("Please enter the Password");
                    if (person == "Bit-RegEndeavour") {
                        $("#adapter").modal('show');
                    }
//            $("#adapter").modal('show');
                });
            }
        };
    });
    </script>
    <script>
        $(document).ready(function () {
            $("#offline_regi").hide();
            $("input[type='radio']").change(function () {
                if ($(this).val() == 1) {
                    $("#online_regi").show();
                } else {
                    $("#online_regi").hide();
                }
                if ($(this).val() == 2) {
                    $("#offline_regi").show();
                } else {
                    $("#offline_regi").hide();
                }
            });
            $('input:checkbox').click(function () {
                var $inputs = $('input:checkbox');
                var mackey = "<?php echo encryptDecrypt($macprocessorid); ?>";
                $('#machineKey').val(mackey);
//                $('input[name="addmac"]:checked').each(function() {
                if ($(this).is(':checked')) {
                    var macvalue = $(this).val();
                    $('#btn_ok').click(function () {
                        $('#machineKey').val(macvalue);
                    });
                    $inputs.not(this).prop('disabled', true);
                } else {
                    var mackey = "<?php echo encryptDecrypt($macprocessorid); ?>";
                    $('#machineKey').val(mackey);
                    $('#btn_ok').click(function () {                        
                        $('#machineKey').val(mackey);
                    });
                    $inputs.prop('disabled', false);
                }
                
            });
//            });
        });
//            document.querySelector("[name='offlineregister']").addEventListener("click", function (event) {
//                // Disable all fields in the #online_regi section
//                document.querySelectorAll("#online_regi input").forEach(input => {
//                    input.disabled = true;
//                });
//
//                // Allow the form to submit
//            });
//
//            // Re-enable the fields after submission to avoid issues when navigating back
//            document.querySelectorAll("#online_regi input").forEach(input => {
//                input.disabled = false;
//            });
    </script>
</body>
</html>