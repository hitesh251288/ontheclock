<?php
ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
//session_start();
//$current_module = "18";
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];

$conn = openConnection();
?>
<html>
    <head>
        <title>Register</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!--<link rel="shortcut icon" href="img/favicon.png">-->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <!--<link rel="stylesheet" href="resource/font-awesome/font-awesome.min.css">-->
        <link rel="stylesheet" href="resource/css/bootstrap.min.css">

        <script src="resource/js/jquery.min.js"></script>
        <script src="resource/js/bootstrap.min.js"></script>
    <center><?php displayHeader('', true, false); ?></center>
    <center><?php displayLinks($current_module, $userlevel); ?></center>
    <style>
        .headback{background-image:linear-gradient(to top, #ffffff 0%, #56c0fe 80%) !important;}
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-lg-2"></div>
            <div class="col-lg-8 headback">
                <h4><center>License Registration</center></h4>
            </div>
            <div class="col-lg-2"></div>
        </div>
        <form action="licenseregi_script.php" method="post">
            <div class="row">
                <div class="col-lg-2"></div>
                <div class="col-lg-2">
                    <input type="radio" name="registersur" checked="checked" value="register" id="register"/> Register/Update Online
                </div>
                <div class="col-lg-2">
                    <input type="radio" name="registersur" value="surrender" id="surrender"/> Surrender License
                </div>
                <div class="col-lg-2"></div>
            </div>
            <div class="row">
                <div class="col-lg-2"></div>
                <div class="col-lg-2">
                    <input type="radio" name="localhostrem" checked="checked" value="localhost"/> Local Host
                </div>
                <div class="col-lg-2">
                    <input type="radio" name="localhostrem" value="remote"/> Remote Server
                </div>
                <div class="col-lg-4">
                    Server IP <input type="text" name="serverip" value=""/>
                </div>
                <div class="col-lg-2"></div>
            </div>
            <div class="row">
                <div class="col-lg-4"></div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label>Serial No:</label>
                        <input type="text" name="serialno" value="" class="form-control"/>
                    </div>
                    <div class="form-group">
                        <label>Password:</label>
                        <input type="password" name="password" value="" class="form-control"/>
                    </div>
                    <div class="form-group">
                        <label>Contact Person:</label>
                        <input type="text" name="contactperson" value="" class="form-control"/>
                    </div>
                    <div class="form-group">
                        <label>Email Address:</label>
                        <input type="text" name="emailadd" value="" class="form-control"/>
                    </div>
                    <div class="form-group" id="remarks">
                        <label>Remarks:</label>
                        <input type="text" name="remarks" value=""  class="form-control"/>
                    </div>
                    <div class="form-group">
                        <input type="submit" name="register" value="Register" class="form-control"/>
                    </div>
                </div>
                <div class="col-lg-2"></div>
            </div>
        </form>
    </div>
<script>
$(document).ready(function () {
    $("#remarks").hide();
    $("#register").click(function () {
        $("#remarks").hide();
    });
    $("#surrender").click(function () {
        $("#remarks").show();
    });
});
</script>
</body>
</html>