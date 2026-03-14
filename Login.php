<?php include 'LoginScript.php'; ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>OnTheClock Login</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" loading="lazy" href="images/icons/favicon.ico"/>
        <link rel="stylesheet" type="text/css" href="resource/css/bootstrap.min.css">
        <link rel="preload" href="fonts/font-awesome-4.7.0/fonts/fontawesome-webfont.woff2?v=4.7.0" as="font" type="font/woff2" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--        <link rel="stylesheet" type="text/css" href="css/animate.css">
        <link rel="stylesheet" type="text/css" href="css/hamburgers.min.css">-->
        <!--<link rel="stylesheet" type="text/css" href="css/select2.min.css">-->
        <!--<link rel="stylesheet" type="text/css" href="css/util.css">-->
        <link rel="stylesheet" type="text/css" href="css/main.css">
        <link rel="stylesheet" type="text/css" href="styles.css">
    </head>
    <body>
        <div class="limiter">
            <div class="container-login100">
                <div class="wrap-login100 bordered-div">
                    <?php // displayLoginInfo($osm_result[1], false, false); ?>
                    <div class="login100-form">
                        <?php displayLoginInfo("", true, false); ?>
                    </div>
                    <!--<div class="login100-pic mb-3 text-center">-->
                    <form class="login100-form validate-form" name="frm1" method="post" action="Login.php?act=login">
                        <span class="js-tilt validationmessage">
                            <!--<img src="assets/images/logo-text.png">-->
                        </span>
                        <div class="wrap-input100 validate-input" data-validate = "Valid username is required: xyz">
                            <input type="hidden" name="url" value="<?php echo $url; ?>">
                            <input class="input100" type="text" name="txtUsername" placeholder="Username">
                            <span class="focus-input100"></span>
                            <span class="symbol-input100">
                                <i class="fa fa-envelope" aria-hidden="true"></i>
                            </span>
                        </div>
                        <div class="wrap-input100 validate-input" data-validate = "Password is required">
                            <input class="input100" type="password" name="txtPassword" placeholder="Password">
                            <span class="focus-input100"></span>
                            <span class="symbol-input100">
                                <i class="fa fa-lock" aria-hidden="true"></i>
                            </span>
                        </div>

                        <div class="wrap-input100 validate-input" data-validate = "Password is required">
                            <select name="lstUserType" class='input100'>
                                <option selected>User</option>
                                <option>Staff</option>
                            </select>
                            <span class="focus-input100"></span>
                            <span class="symbol-input100">
                                <i class="fa fa-users" aria-hidden="true"></i>
                            </span>

                        </div>

                        <div class="container-login100-form-btn">
                            <button type="submit" onClick="javascript:this.disabled = true; document.frm1.submit()" class="login100-form-btn">
                                Login
                            </button>
                            <!--<input type="submit" onClick="javascript:this.disabled=true;document.frm1.submit()" class="login100-form-btn" value="Login">-->
                        </div>

                        <div class="text-center p-t-12">
                            <span style="color:red;"><?php echo $message; ?></span>
                            <!--<a class="login100-form-btn" href="<?php //echo $_SERVER['REMOTE_ADDR'];  ?>/:8010/timemaster/Login.php" target="blank" class="btn btn-primary">Employee Attendance</a>-->
                        </div>

                        <div class="text-center p-t-136">
                            <a class="txt2" href="#">
                                <!-- Create your Account 
                                <i class="fa fa-long-arrow-right m-l-5" aria-hidden="true"></i>-->
                            </a>
                        </div>
                    </form>
                    <!--</div>-->
                </div>
            </div>
        </div>
        <script src="js/jquery-3.2.1.min.js"></script>
        <script src="js/popper.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/select2.min.js"></script>
        <script src="js/tilt.jquery.min.js"></script>
        <script src="js/main.js"></script>
        <script >
                    $('.js-tilt').tilt({
                    scale: 1.1
                    });
        
                    function openWindow() {
                    //a = document.frm1.serialkey.value;
                    //window.open('Register.php?serialkey='+a, 'Register', 'height=400;resize=no;menubar=no;addressbar=no');
                    //window.open('Register.php', 'Register', 'height=400;resize=no;menubar=no;addressbar=no');
                    if (confirm("Register Application")){
                            document.getElementById('btRegister').disabled = true; window.open('ExecuteScript.php?script=Register', 'ExecuteScript', 'height=300;width=400;resize=no;menubar=no;addressbar=no');
                    }

                    var timerlen = 5;
                    var slideAniLen = 250;
                    var timerID = new Array();
                    var startTime = new Array();
                    var obj = new Array();
                    var endHeight = new Array();
                    var moving = new Array();
                    var dir = new Array();
                    function slidedown(objname) {
                    if (moving[objname]) return;
                        if (document.getElementById(objname).style.display != "none")
                                return; // cannot slide down something that is already visiblemoving[objname] = true;
                        dir[objname] = "down";
                        startslide(objname);
                    }
                    function startslide(objname){
                            obj[objname] = document.getElementById(objname); 
                            endHeight[objname] = parseInt(obj[objname].style.height); 
                            startTime[objname] = (new Date()).getTime();
                    if (dir[objname] == "down"){
                            obj[objname].style.height = "1px";
                    }
                    obj[objname].style.display = "block";
                            timerID[objname] = setInterval("slidetick('' + objname + '');", timerlen);
                    }

                    function slidetick(objname) {
                    var elapsed = (new Date())
                            .getTime() - startTime[objname];
                    if (elapsed > slideAniLen) endSlide(objname)
                            else {
                                var d = Math.round(elapsed / slideAniLen * endHeight[objname]);
                                if (dir[objname] == "up")
                                    d = endHeight[objname] - d;
                                    obj[objname].style.height = d + "px";
                                }
                                return;
                    }

                    function endSlide(objname) {
                        clearInterval(timerID[objname]);
                        if (dir[objname] == "up")
                                obj[objname].style.display = "none";
                                obj[objname].style.height = endHeight[objname] + "px";
                                delete(moving[objname]);
                                delete(timerID[objname]); 
                                delete(startTime[objname]); 
                                delete(endHeight[objname]); 
                                delete(obj[objname]); 
                                delete(dir[objname]);
                        return;
                    }
                }
        </script>
    </body>
</html>