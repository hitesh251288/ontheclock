<?php
ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$username = $_SESSION[$session_variable . "username"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$userstatus = $_SESSION[$session_variable . "userstatus"];
$VirdiLevel = $_SESSION[$session_variable . "VirdiLevel"];
$lstUserType = $_SESSION[$session_variable . "lstUserType"];
if ($username == "") {
    header("Location: " . $config["REDIRECT"]);
}
$conn = openConnection();
$iconn = openIConnection();
$act = $_GET["act"];
if ($act == "changePassword") {
    if (changePassword($conn, $username, $_POST["txtPassword"], $_POST["txtNewPassword"], $lstUserType)) {
        $message = "Password changed successfully";
    } else {
        $message = "Password could not be changed <br>Either the Current Password supplied DOES NOT match OR the New Password COULD NOT be encrypted <br>If you continue to receive this message, please try to use a different New Password <br><br>";
    }
} else {
    if ($act == "rasSelection") {
        $text = "";
        for ($i = 0; $i < 40; $i++) {
            if ($_POST["chk" . $i] != "") {
                $text = $text . $_POST["chk" . $i];
            }
            if ($_POST["chk78"] != "") {
                $text = $text . $_POST["chk78"];
            }
            if ($_POST["chk79"] != "") {
                $text = $text . $_POST["chk79"];
            }
            if ($_POST["chk80"] != "") {
                $text = $text . $_POST["chk80"];
            }
            if ($_POST["chk81"] != "") {
                $text = $text . $_POST["chk81"];
            }
        }
        $query = "UPDATE UserMaster SET RASSelection = '" . $text . "' WHERE Username = '" . $username . "'";
        updateIData($iconn, $query, true);
        $text = "";
        for ($i = 38; $i < 78; $i++) {
            if ($_POST["chk" . $i] != "") {
                $text = $text . $_POST["chk" . $i];
            }
        }
        if ($VirdiLevel == "Classic") {
            $query = "UPDATE UserMaster SET RGSSelection = '" . $text . "' WHERE Username = '" . $username . "'";
            updateIData($iconn, $query, true);
            $text = "Personalized Attendance Snapshot and Group Summary Report Column Selection";
            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
            updateIData($iconn, $query, true);
        }
    } else {
        if ($act == "useCalendar") {
            $query = "UPDATE UserMaster SET UserType = '" . $_POST["lstUseCalendar"] . "' WHERE Username = '" . $username . "'";
            updateIData($iconn, $query, true);
        }
    }
}
if ($prints != "yes") {
    include 'header.php';
    ?>
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-12 d-flex no-block align-items-center">
                <h4 class="page-title">Change Password</h4>
                <div class="ms-auto text-end">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Change Password
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <?php
}
echo "<link rel=\"shortcut icon\" href=\"favicon.ico\">\r\n</head>\r\n<script>\r\n\tfunction checkPassword(){\r\n\t\tx = document.frm1;\r\n\t\tif (x.txtPassword.value == '' || x.txtNewPassword.value == '' || x.txtNewPassword.value != x.txtNewPasswordRepeat.value || x.txtNewPassword.value == x.txtPassword.value){\r\n\t\t\talert('Passwords should NOT be blank. New and Old Password Values should NOT be the same. New Password values should be the same.');\r\n\t\t\tx.txtPassword.focus();\r\n\t\t}else{\r\n\t\t\tx.submit();\r\n\t\t}\r\n\t}\r\n</script>\r\n\r\n";
if (getTotalDays(displayToday(), displayDate(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 2))) < 10 && 0 < getTotalDays(displayToday(), displayDate(getRegister(encryptDecrypt($_SESSION[$session_variable . "MACAddress"]), 2)))) {
    echo "\t<body onLoad=\"javascript:alert('Classic License and Support Services Expire in less than 10 Days')\">\r\n";
} else {
    print "<body>";
}

//echo "<center><div align='center'>\r\n\t";
//displayHeader($prints, false, false);
print "<center>";
print'<div class="container-fluid">
                <div class="row">
                        <div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12">
                            <div class="card">
                                    <div class="card-body table-responsive">';
//displayLinks(200, $userlevel);
//print "</center>";
?>
<div class="card">
    <div class="card-body">
        <?php
        print "<p align='center'><b>" . $message . "</b></p>";
        echo "\t\t\t\t<!-- <tr><td width='100%' colspan='2' align='left'><a title='Click to Close' href='javascript:;' onMouseOver=\"javascript:slidedown('mydiv')\" onClick=\"javascript:slideup('mydiv')\" style='text-decoration:none'><img src='img/help.gif' border='0'></a>\t\t\t\t\r\n\t\t\t\t<div id=\"mydiv\" style=\"display:none; overflow:hidden; height:170px; position:absolute; background-color:#FFFFFF\">\t\t\t\t\r\n\t\t\t\t<font face='Verdana' size='2'>\t\t\t\t\r\n\t\t\t\t<br>By default the current user name is displayed with an option to change his password. \r\n\t\t\t\t<br><i><b>To change the user's password:</b></i>\r\n\t\t\t\t<br>-\tType the current password in text box provided.\r\n\t\t\t\t<br>-\tType a new password in the column below.\r\n\t\t\t\t<br>-\tRe-type the new password for confirmation\r\n\t\t\t\t<br>-\tClick on the <b>Change Password</b> button to complete the process.\r\n\t\t\t\t<br><b>N/B:</b> The current user name and date is also displayed below the company's name and address at the right-hand corner of the Tab Menu\r\n\t\t\t\t<br><font size='1'>Last Update: 06/02/2009</font>\r\n\t\t\t\t</font>\t\t\t\t\r\n\t\t\t\t</div></td></tr> -->\r\n\t\t\t";
        print "<form method='post' action='Login.php?act=login'>";
        print "<input type='hidden' name='url' value='" . $url . "'>";
        print "<label class='form-label'>User Account: " . $username . "</label>";
        print "</form>";
        print "<form name='frm1' method='post' action='Password.php?act=changePassword'>";
        ?>
        <div class="row">
            <?php echo "<label class='form-label'><br>Change Password (Optional)</label>"; ?>
            <div class="col-3"></div>
            <div class="col-2">
                <?php
                echo "<label class='form-label'><br>Current Password</label>";
                print "<input type='password' name='txtPassword' class='form-control'>";
                ?>
            </div>
            <div class="col-2">
                <?php
                echo "<label class='form-label'><br>New Password</label>";
                echo "<input type='password' name='txtNewPassword' class='form-control'>";
                ?>
            </div>
            <div class="col-2">
                <?php
                echo "<label class='form-label'><br>New Password (repeat)</label>";
                echo "<input type='password' name='txtNewPasswordRepeat' class='form-control'>";
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <?php echo "<center><br><input type='button' class='btn btn-primary' value='Save Changes' onClick='javascript:checkPassword()'></center>"; ?>
            </div>
        </div>
        <?php
        print "</form>";
        print "<form method='post' action='Password.php?act=useCalendar'>";
        ?>
        <div class="row">
            <div class="col-5"></div>
            <div class="col-2">
                <?php
                print "<label class='form-label'>Use Pop Up Calendar for Date Fields</label>";
                print "<select name='lstUseCalendar' class='form-control-inner select2 form-select shadow-none'>";
                $query = "SELECT Usertype FROM UserMaster WHERE Username = '" . $username . "'";
                $result = selectData($conn, $query);
                if ($result[0] == "") {
                    $result[0] = "Yes";
                }
                print "<option value='" . $result[0] . "'>" . $result[0] . "</option>";
                echo "<option value='Yes'>Yes</option><option value='No'>No</option></select>";
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <?php
                print "<center><br><input type='submit' class='btn btn-primary' value='Save Changes'></center>";
                ?>
            </div>
        </div>
        </form>
    </div>
</div>
<?php
print "</div></div></div></div>";
print '<div class="row"><div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12"><div class="card"><div class="card-body">';
if (stripos($userlevel, "21V") !== false && $VirdiLevel != "Meal") {
    print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%'><tr>";
    print "<td colspan='2' align='center' bgcolor='#FFFFFF'><font face='Verdana' size='2' color='#6481BD'><b>Personalize Attendance Snapshot/ Group Summary Report</b> <br>Select Column(s) to be displayed in the Report and click <b>Save Changes</b></font></td>";
    print "</tr><tr>";
    print "<td bgcolor='#FFFFFF'><font face='Verdana' size='1'>&nbsp;<b>" . $_SESSION[$session_variable . "FlagReportText"] . "</b></font></td>";
    print "<td align='right' bgcolor='#FFFFFF'><font face='Verdana' size='1'><b>WK</b> = Week Day [EXCLUDING Proxy and Flag]; <b>PXY</b> = Proxy ; <b>FLG</b> = Flag Day ; <b>SAT</b> = Saturday ; <b>SUN</b> = Sunday <br><b>NS</b> = Night Shifts ; <b>LI</b> = Late IN ; <b>EO</b> = Early Out ; <b>MB</b> = More Break <br><b>TLD</b> = Total Days ; <b>TLH</b> =Total Hours ; <b>NSH</b> = Total Night Shift Hours ; <b>LIH</b> = Total Late In Hours ; <b>EOH</b> = Total Early Out Hours ; <b>MBH</b> = Total More Break Hours <br><b>O</b> = Overtime Hours ; <b>AO</b> = Approved Overtime Hours ; <b>N</b> = Normal Hours ; <b>A</b> = Absent Days ; <b>P</b> = Present Days";
    if ($VirdiLevel == "Classic") {
        print "<br><br><b>RG</b> = No of Employees Registered ; <b>DA</b> = No of Employees De-Activated <br><b>BK</b> = Black Flag ; <b>PX</b> = Proxy Flag <br><b>FLG</b> = Total Flags ; <b>WKD</b> = Week Day ; <b>SAT</b> = Saturday / OT1 ; <b>SUN</b> = Sunday / OT2 <br><b>P</b> = Total Employees Present ; <b>A</b> = Total Absent Employees ; <b>A/F</b> - Total Absent Employees (EXCLUDING Flagged Employees) <br><b>NS</b> = Total Night Shift Attendance <br><b>GC</b> = Total Grace Hours ; <b>LI</b> = Total Late In Hours ; <b>MB</b> =Total More Break Hours ; <b>EO</b> = Total Early Out Hours <br><b>AO</b> = Total Approved Overtime Hours";
    }
    print "</b></font></td>";
    print "</tr></table>";
    echo "\t<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%'><form method='post' action='Password.php?act=rasSelection'>\r\n\t\t<tr>\r\n\t\t\t<td colspan='29' bgcolor='#FAFAFA' align='center'><font face='Verdana' size='2'><b>Days</b></font></td>\r\n\t\t\t<td colspan='13' bgcolor='#FAFAFA' align='center'><font face='Verdana' size='2'><b>Hours</b></font></td>\r\n\t\t</tr>\r\n\t\t<tr>\r\n\t\t\t<td><font face='Verdana' size='2' color='Violet'>V</font></td> <td><font face='Verdana' size='2' color='Indigo'>I</font></td> <td><font face='Verdana' size='2' color='Blue'>B</font></td> <td><font face='Verdana' size='2' color='Green'>G</font></td> <td bgcolor='Brown'><font face='Verdana' size='2' color='Yellow'>Y</font></td> <td><font face='Verdana' size='2' color='Orange'>O</font></td> <td><font face='Verdana' size='2' color='Red'>R</font></td> <td><font face='Verdana' size='2' color='Gray'>GR</font></td> <td><font face='Verdana' size='2' color='Brown'>BR</font></td> <td><font face='Verdana' size='2' color='Purple'>PR</font></td>\r\n\t\t\t<td><font face='Verdana' size='2' color='Magenta'>MG</font></td> <td><font face='Verdana' size='2' color='Teal'>TL</font></td> <td><font face='Verdana' size='2' color='Aqua'>AQ</font></td> <td><font face='Verdana' size='2' color='Safron'>SF</font></td> <td><font face='Verdana' size='2' color='Amber'>AM</font></td> <td><font face='Verdana' size='2' color='Gold'>GL</font></td> <td><font face='Verdana' size='2' color='Vermilion'>VM</font></td> <td><font face='Verdana' size='2' color='Silver'>SL</font></td> <td><font face='Verdana' size='2' color='Maroon'>MR</font></td> <td><font face='Verdana' size='2' color='Pink'>PK</font></td>\r\n\t\t\t<td align='center'><font face='Verdana' size='2'>WK</font></td> <td align='center'><font face='Verdana' size='2'>PXY</font></td> <td align='center'><font face='Verdana' size='2'>FLG</font></td> <td align='center'><font face='Verdana' size='2'>SAT</font></td> <td align='center'><font face='Verdana' size='2'>SUN</font></td> <td align='center'><font face='Verdana' size='2'>TLD</font></td> <td align='center'><font face='Verdana' size='2'>NS</font></td> <td align='center'><font face='Verdana' size='2'>LI</font></td> <td align='center' ><font face='Verdana' size='2'>EO</font></td> <td align='center' ><font face='Verdana' size='2'>MB</font></td>\r\n\t\t\t<td align='center'><font face='Verdana' size='2'>WK</font></td> <td align='center'><font face='Verdana' size='2'>PXY</font></td> <td align='center'><font face='Verdana' size='2'>WK+PXY</font></td> <td align='center'><font face='Verdana' size='2'>FLG</font></td> <td align='center'><font face='Verdana' size='2'>SAT</font></td> <td align='center'><font face='Verdana' size='2'>WK+PXY+SAT</font></td> <td align='center'><font face='Verdana' size='2'>SUN</font></td> <td align='center' ><font face='Verdana' size='2'>TLH</font></td> <td align='center'><font face='Verdana' size='2'>NS</font></td> <td align='center'><font face='Verdana' size='2'>LIH</font></td> <td align='center' ><font face='Verdana' size='2'>EOH</font></td> <td align='center' ><font face='Verdana' size='2'>MBH</font></td>\r\n\t\t</tr>\r\n\t\t";
    $query = "SELECT RASSelection FROM UserMaster WHERE Username = '" . $username . "'";
    $result = selectData($conn, $query);
    print "<tr>";
    if (strpos($result[0], "-V-") !== false) {
        print "<td><input name='chk0' type='checkbox' value='-V-' checked></td>";
    } else {
        print "<td><input name='chk0' type='checkbox' value='-V-'></td>";
    }
    if (strpos($result[0], "-I-") !== false) {
        print "<td><input name='chk1' type='checkbox' value='-I-' checked></td>";
    } else {
        print "<td><input name='chk1' type='checkbox' value='-I-'></td>";
    }
    if (strpos($result[0], "-B-") !== false) {
        print "<td><input name='chk2' type='checkbox' value='-B-' checked></td>";
    } else {
        print "<td><input name='chk2' type='checkbox' value='-B-'></td>";
    }
    if (strpos($result[0], "-G-") !== false) {
        print "<td><input name='chk3' type='checkbox' value='-G-' checked></td>";
    } else {
        print "<td><input name='chk3' type='checkbox' value='-G-'></td>";
    }
    if (strpos($result[0], "-Y-") !== false) {
        print "<td><input name='chk4' type='checkbox' value='-Y-' checked></td>";
    } else {
        print "<td><input name='chk4' type='checkbox' value='-Y-'></td>";
    }
    if (strpos($result[0], "-O-") !== false) {
        print "<td><input name='chk5' type='checkbox' value='-O-' checked></td>";
    } else {
        print "<td><input name='chk5' type='checkbox' value='-O-'></td>";
    }
    if (strpos($result[0], "-R-") !== false) {
        print "<td><input name='chk6' type='checkbox' value='-R-' checked></td>";
    } else {
        print "<td><input name='chk6' type='checkbox' value='-R-'></td>";
    }
    if (strpos($result[0], "-GR-") !== false) {
        print "<td><input name='chk7' type='checkbox' value='-GR-' checked></td>";
    } else {
        print "<td><input name='chk7' type='checkbox' value='-GR-'></td>";
    }
    if (strpos($result[0], "-BR-") !== false) {
        print "<td><input name='chk8' type='checkbox' value='-BR-' checked></td>";
    } else {
        print "<td><input name='chk8' type='checkbox' value='-BR-'></td>";
    }
    if (strpos($result[0], "-PR-") !== false) {
        print "<td><input name='chk9' type='checkbox' value='-PR-' checked></td>";
    } else {
        print "<td><input name='chk9' type='checkbox' value='-PR-'></td>";
    }
    if (strpos($result[0], "-MG-") !== false) {
        print "<td><input name='chk10' type='checkbox' value='-MG-' checked></td>";
    } else {
        print "<td><input name='chk10' type='checkbox' value='-MG-'></td>";
    }
    if (strpos($result[0], "-TL-") !== false) {
        print "<td><input name='chk11' type='checkbox' value='-TL-' checked></td>";
    } else {
        print "<td><input name='chk11' type='checkbox' value='-TL-'></td>";
    }
    if (strpos($result[0], "-AQ-") !== false) {
        print "<td><input name='chk12' type='checkbox' value='-AQ-' checked></td>";
    } else {
        print "<td><input name='chk12' type='checkbox' value='-AQ-'></td>";
    }
    if (strpos($result[0], "-SF-") !== false) {
        print "<td><input name='chk13' type='checkbox' value='-SF-' checked></td>";
    } else {
        print "<td><input name='chk13' type='checkbox' value='-SF-'></td>";
    }
    if (strpos($result[0], "-AM-") !== false) {
        print "<td><input name='chk14' type='checkbox' value='-AM-' checked></td>";
    } else {
        print "<td><input name='chk14' type='checkbox' value='-AM-'></td>";
    }
    if (strpos($result[0], "-GL-") !== false) {
        print "<td><input name='chk15' type='checkbox' value='-GL-' checked></td>";
    } else {
        print "<td><input name='chk15' type='checkbox' value='-GL-'></td>";
    }
    if (strpos($result[0], "-VM-") !== false) {
        print "<td><input name='chk16' type='checkbox' value='-VM-' checked></td>";
    } else {
        print "<td><input name='chk16' type='checkbox' value='-VM-'></td>";
    }
    if (strpos($result[0], "-SL-") !== false) {
        print "<td><input name='chk17' type='checkbox' value='-SL-' checked></td>";
    } else {
        print "<td><input name='chk17' type='checkbox' value='-SL-'></td>";
    }
    if (strpos($result[0], "-MR-") !== false) {
        print "<td><input name='chk18' type='checkbox' value='-MR-' checked></td>";
    } else {
        print "<td><input name='chk18' type='checkbox' value='-MR-'></td>";
    }
    if (strpos($result[0], "-PK-") !== false) {
        print "<td><input name='chk19' type='checkbox' value='-PK-' checked></td>";
    } else {
        print "<td><input name='chk19' type='checkbox' value='-PK-'></td>";
    }
    if (strpos($result[0], "-WKD-") !== false) {
        print "<td><input name='chk20' type='checkbox' value='-WKD-' checked></td>";
    } else {
        print "<td><input name='chk20' type='checkbox' value='-WKD-'></td>";
    }
    if (strpos($result[0], "-PXY-") !== false) {
        print "<td><input name='chk21' type='checkbox' value='-PXY-' checked></td>";
    } else {
        print "<td><input name='chk21' type='checkbox' value='-PXY-'></td>";
    }
    if (strpos($result[0], "-FLG-") !== false) {
        print "<td><input name='chk22' type='checkbox' value='-FLG-' checked></td>";
    } else {
        print "<td><input name='chk22' type='checkbox' value='-FLG-'></td>";
    }
    if (strpos($result[0], "-SAT-") !== false) {
        print "<td><input name='chk23' type='checkbox' value='-SAT-' checked></td>";
    } else {
        print "<td><input name='chk23' type='checkbox' value='-SAT-'></td>";
    }
    if (strpos($result[0], "-SUN-") !== false) {
        print "<td><input name='chk24' type='checkbox' value='-SUN-' checked></td>";
    } else {
        print "<td><input name='chk24' type='checkbox' value='-SUN-'></td>";
    }
    if (strpos($result[0], "-TLD-") !== false) {
        print "<td><input name='chk25' type='checkbox' value='-TLD-' checked></td>";
    } else {
        print "<td><input name='chk25' type='checkbox' value='-TLD-'></td>";
    }
    if (strpos($result[0], "-NS-") !== false) {
        print "<td><input name='chk26' type='checkbox' value='-NS-' checked></td>";
    } else {
        print "<td><input name='chk26' type='checkbox' value='-NS-'></td>";
    }
    if (strpos($result[0], "-LI-") !== false) {
        print "<td><input name='chk27' type='checkbox' value='-LI-' checked></td>";
    } else {
        print "<td><input name='chk27' type='checkbox' value='-LI-'></td>";
    }
    if (strpos($result[0], "-EO-") !== false) {
        print "<td><input name='chk28' type='checkbox' value='-EO-' checked></td>";
    } else {
        print "<td><input name='chk28' type='checkbox' value='-EO-'></td>";
    }
    if (strpos($result[0], "-MB-") !== false) {
        print "<td><input name='chk29' type='checkbox' value='-MB-' checked></td>";
    } else {
        print "<td><input name='chk29' type='checkbox' value='-MB-'></td>";
    }
    if (strpos($result[0], "-WKH-") !== false) {
        print "<td><input name='chk30' type='checkbox' value='-WKH-' checked></td>";
    } else {
        print "<td><input name='chk30' type='checkbox' value='-WKH-'></td>";
    }
    if (strpos($result[0], "-PXH-") !== false) {
        print "<td><input name='chk31' type='checkbox' value='-PXH-' checked></td>";
    } else {
        print "<td><input name='chk31' type='checkbox' value='-PXH-'></td>";
    }
    if (strpos($result[0], "-WKH+PXH-") !== false) {
        print "<td><input name='chk80' type='checkbox' value='-WKH+PXH-' checked></td>";
    } else {
        print "<td><input name='chk80' type='checkbox' value='-WKH+PXH-'></td>";
    }
    if (strpos($result[0], "-FLH-") !== false) {
        print "<td><input name='chk32' type='checkbox' value='-FLH-' checked></td>";
    } else {
        print "<td><input name='chk32' type='checkbox' value='-FLH-'></td>";
    }
    if (strpos($result[0], "-SATH-") !== false) {
        print "<td><input name='chk33' type='checkbox' value='-SATH-' checked></td>";
    } else {
        print "<td><input name='chk33' type='checkbox' value='-SATH-'></td>";
    }
    if (strpos($result[0], "-WKH+PXH+SATH-") !== false) {
        print "<td><input name='chk81' type='checkbox' value='-WKH+PXH+SATH-' checked></td>";
    } else {
        print "<td><input name='chk81' type='checkbox' value='-WKH+PXH+SATH-'></td>";
    }
    if (strpos($result[0], "-SUNH-") !== false) {
        print "<td><input name='chk34' type='checkbox' value='-SUNH-' checked></td>";
    } else {
        print "<td><input name='chk34' type='checkbox' value='-SUNH-'></td>";
    }
    if (strpos($result[0], "-TLH-") !== false) {
        print "<td><input name='chk35' type='checkbox' value='-TLH-' checked></td>";
    } else {
        print "<td><input name='chk35' type='checkbox' value='-TLH-'></td>";
    }
    if (strpos($result[0], "-NSH-") !== false) {
        print "<td><input name='chk36' type='checkbox' value='-NSH-' checked></td>";
    } else {
        print "<td><input name='chk36' type='checkbox' value='-NSH-'></td>";
    }
    if (strpos($result[0], "-LIH-") !== false) {
        print "<td><input name='chk37' type='checkbox' value='-LIH-' checked></td>";
    } else {
        print "<td><input name='chk37' type='checkbox' value='-LIH-'></td>";
    }
    if (strpos($result[0], "-EOH-") !== false) {
        print "<td><input name='chk78' type='checkbox' value='-EOH-' checked></td>";
    } else {
        print "<td><input name='chk78' type='checkbox' value='-EOH-'></td>";
    }
    if (strpos($result[0], "-MBH-") !== false) {
        print "<td><input name='chk79' type='checkbox' value='-MBH-' checked></td>";
    } else {
        print "<td><input name='chk79' type='checkbox' value='-MBH-'></td>";
    }
    print "</tr>";
    echo "\t\t\r\n\t</table>\t\r\n\t<br>\r\n\t<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%'>\t\t\r\n\t\t";
    if ($VirdiLevel == "Classic") {
        echo "\t\t<tr>\r\n\t\t\t<td><font face='Verdana' size='2'>RG</font></td> <td><font face='Verdana' size='2'>PRM</font></td> <td><font face='Verdana' size='2'>RSN</font></td> <td><font face='Verdana' size='2'>RTD</font></td> <td><font face='Verdana' size='2'>TRM</font></td> <td bgcolor='#F0F0F0'><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='2'>BK</font></td> <td><font face='Verdana' size='2'>PX</font></td> <td><font face='Verdana' size='2' color='Violet'>V</font></td> <td><font face='Verdana' size='2' color='Indigo'>I</font></td> <td><font face='Verdana' size='2' color='Blue'>B</font></td> <td><font face='Verdana' size='2' color='Green'>G</font></td> <td bgcolor='Brown'><font face='Verdana' size='2' color='Yellow'>Y</font></td> <td><font face='Verdana' size='2' color='Orange'>O</font></td> <td><font face='Verdana' size='2' color='Red'>R</font></td> <td><font face='Verdana' size='2' color='Gray'>GR</font></td> <td><font face='Verdana' size='2' color='Brown'>BR</font></td> <td><font face='Verdana' size='2' color='Purple'>PR</font></td>\r\n\t\t\t<td><font face='Verdana' size='2' color='Magenta'>MG</font></td> <td><font face='Verdana' size='2' color='Teal'>TL</font></td> <td><font face='Verdana' size='2' color='Aqua'>AQ</font></td> <td><font face='Verdana' size='2' color='Safron'>SF</font></td> <td><font face='Verdana' size='2' color='Amber'>AM</font></td> <td><font face='Verdana' size='2' color='Gold'>GL</font></td> <td><font face='Verdana' size='2' color='Vermilion'>VM</font></td> <td><font face='Verdana' size='2' color='Silver'>SL</font></td> <td><font face='Verdana' size='2' color='Maroon'>MR</font></td> <td><font face='Verdana' size='2' color='Pink'>PK</font></td>\r\n\t\t\t<td bgcolor='#F0F0F0'><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='2'>FLG</font></td> <td><font face='Verdana' size='2'>WKD</font></td> <td><font face='Verdana' size='2'>SAT</font></td> <td><font face='Verdana' size='2'>SUN</font></td> <td bgcolor='#F0F0F0'><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='2'>P</font></td> <td><font face='Verdana' size='2'>A</font></td> <td><font face='Verdana' size='2'>A/F</font></td> <td bgcolor='#F0F0F0'><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='2'>NS</font></td> <td bgcolor='#F0F0F0'><font face='Verdana' size='1'>&nbsp;</font></td> <td><font face='Verdana' size='2'>GC</font></td> <td><font face='Verdana' size='2'>LI</font></td> <td><font face='Verdana' size='2'>MB</font></td> <td><font face='Verdana' size='2'>EO</font></td> <td><font face='Verdana' size='2'>AO</font></td>\r\n\t\t</tr>\r\n\t\t";
        $query = "SELECT RGSSelection FROM UserMaster WHERE Username = '" . $username . "'";
        $result = selectData($conn, $query);
        print "<tr>";
        if (strpos($result[0], "-RG-") !== false) {
            print "<td><input name='chk38' type='checkbox' value='-RG-' checked></td>";
        } else {
            print "<td><input name='chk38' type='checkbox' value='-RG-'></td>";
        }
        if (strpos($result[0], "-PRM-") !== false) {
            print "<td><input name='chk39' type='checkbox' value='-PRM-' checked></td>";
        } else {
            print "<td><input name='chk39' type='checkbox' value='-PRM-'></td>";
        }
        if (strpos($result[0], "-RSN-") !== false) {
            print "<td><input name='chk40' type='checkbox' value='-RSN-' checked></td>";
        } else {
            print "<td><input name='chk40' type='checkbox' value='-RSN-'></td>";
        }
        if (strpos($result[0], "-RTD-") !== false) {
            print "<td><input name='chk41' type='checkbox' value='-RTD-' checked></td>";
        } else {
            print "<td><input name='chk41' type='checkbox' value='-RTD-'></td>";
        }
        if (strpos($result[0], "-TRM-") !== false) {
            print "<td><input name='chk42' type='checkbox' value='-TRM-' checked></td>";
        } else {
            print "<td><input name='chk42' type='checkbox' value='-TRM-'></td>";
        }
        print "<td bgcolor='#F0F0F0'><font face='Verdana' size='1'>&nbsp;</font></td>";
        if (strpos($result[0], "-BK-") !== false) {
            print "<td><input name='chk43' type='checkbox' value='-BK-' checked></td>";
        } else {
            print "<td><input name='chk43' type='checkbox' value='-BK-'></td>";
        }
        if (strpos($result[0], "-PX-") !== false) {
            print "<td><input name='chk44' type='checkbox' value='-PX-' checked></td>";
        } else {
            print "<td><input name='chk44' type='checkbox' value='-PX-'></td>";
        }
        if (strpos($result[0], "-V-") !== false) {
            print "<td><input name='chk45' type='checkbox' value='-V-' checked></td>";
        } else {
            print "<td><input name='chk45' type='checkbox' value='-V-'></td>";
        }
        if (strpos($result[0], "-I-") !== false) {
            print "<td><input name='chk46' type='checkbox' value='-I-' checked></td>";
        } else {
            print "<td><input name='chk46' type='checkbox' value='-I-'></td>";
        }
        if (strpos($result[0], "-B-") !== false) {
            print "<td><input name='chk47' type='checkbox' value='-B-' checked></td>";
        } else {
            print "<td><input name='chk47' type='checkbox' value='-B-'></td>";
        }
        if (strpos($result[0], "-G-") !== false) {
            print "<td><input name='chk48' type='checkbox' value='-G-' checked></td>";
        } else {
            print "<td><input name='chk48' type='checkbox' value='-G-'></td>";
        }
        if (strpos($result[0], "-Y-") !== false) {
            print "<td><input name='chk49' type='checkbox' value='-Y-' checked></td>";
        } else {
            print "<td><input name='chk49' type='checkbox' value='-Y-'></td>";
        }
        if (strpos($result[0], "-O-") !== false) {
            print "<td><input name='chk50' type='checkbox' value='-O-' checked></td>";
        } else {
            print "<td><input name='chk50' type='checkbox' value='-O-'></td>";
        }
        if (strpos($result[0], "-R-") !== false) {
            print "<td><input name='chk51' type='checkbox' value='-R-' checked></td>";
        } else {
            print "<td><input name='chk51' type='checkbox' value='-R-'></td>";
        }
        if (strpos($result[0], "-GR-") !== false) {
            print "<td><input name='chk52' type='checkbox' value='-GR-' checked></td>";
        } else {
            print "<td><input name='chk52' type='checkbox' value='-GR-'></td>";
        }
        if (strpos($result[0], "-BR-") !== false) {
            print "<td><input name='chk53' type='checkbox' value='-BR-' checked></td>";
        } else {
            print "<td><input name='chk53' type='checkbox' value='-BR-'></td>";
        }
        if (strpos($result[0], "-PR-") !== false) {
            print "<td><input name='chk54' type='checkbox' value='-PR-' checked></td>";
        } else {
            print "<td><input name='chk54' type='checkbox' value='-PR-'></td>";
        }
        if (strpos($result[0], "-MG-") !== false) {
            print "<td><input name='chk55' type='checkbox' value='-MG-' checked></td>";
        } else {
            print "<td><input name='chk55' type='checkbox' value='-MG-'></td>";
        }
        if (strpos($result[0], "-TL-") !== false) {
            print "<td><input name='chk56' type='checkbox' value='-TL-' checked></td>";
        } else {
            print "<td><input name='chk56' type='checkbox' value='-TL-'></td>";
        }
        if (strpos($result[0], "-AQ-") !== false) {
            print "<td><input name='chk57' type='checkbox' value='-AQ-' checked></td>";
        } else {
            print "<td><input name='chk57' type='checkbox' value='-AQ-'></td>";
        }
        if (strpos($result[0], "-SF-") !== false) {
            print "<td><input name='chk58' type='checkbox' value='-SF-' checked></td>";
        } else {
            print "<td><input name='chk58' type='checkbox' value='-SF-'></td>";
        }
        if (strpos($result[0], "-AM-") !== false) {
            print "<td><input name='chk59' type='checkbox' value='-AM-' checked></td>";
        } else {
            print "<td><input name='chk59' type='checkbox' value='-AM-'></td>";
        }
        if (strpos($result[0], "-GL-") !== false) {
            print "<td><input name='chk60' type='checkbox' value='-GL-' checked></td>";
        } else {
            print "<td><input name='chk60' type='checkbox' value='-GL-'></td>";
        }
        if (strpos($result[0], "-VM-") !== false) {
            print "<td><input name='chk61' type='checkbox' value='-VM-' checked></td>";
        } else {
            print "<td><input name='chk61' type='checkbox' value='-VM-'></td>";
        }
        if (strpos($result[0], "-SL-") !== false) {
            print "<td><input name='chk62' type='checkbox' value='-SL-' checked></td>";
        } else {
            print "<td><input name='chk62' type='checkbox' value='-SL-'></td>";
        }
        if (strpos($result[0], "-MR-") !== false) {
            print "<td><input name='chk63' type='checkbox' value='-MR-' checked></td>";
        } else {
            print "<td><input name='chk63' type='checkbox' value='-MR-'></td>";
        }
        if (strpos($result[0], "-PK-") !== false) {
            print "<td><input name='chk64' type='checkbox' value='-PK-' checked></td>";
        } else {
            print "<td><input name='chk64' type='checkbox' value='-PK-'></td>";
        }
        print "<td bgcolor='#F0F0F0'><font face='Verdana' size='1'>&nbsp;</font></td>";
        if (strpos($result[0], "-FLG-") !== false) {
            print "<td><input name='chk65' type='checkbox' value='-FLG-' checked></td>";
        } else {
            print "<td><input name='chk65' type='checkbox' value='-FLG-'></td>";
        }
        if (strpos($result[0], "-WKD-") !== false) {
            print "<td><input name='chk66' type='checkbox' value='-WKD-' checked></td>";
        } else {
            print "<td><input name='chk66' type='checkbox' value='-WKD-'></td>";
        }
        if (strpos($result[0], "-SAT-") !== false) {
            print "<td><input name='chk67' type='checkbox' value='-SAT-' checked></td>";
        } else {
            print "<td><input name='chk67' type='checkbox' value='-SAT-'></td>";
        }
        if (strpos($result[0], "-SUN-") !== false) {
            print "<td><input name='chk68' type='checkbox' value='-SUN-' checked></td>";
        } else {
            print "<td><input name='chk68' type='checkbox' value='-SUN-'></td>";
        }
        print "<td bgcolor='#F0F0F0'><font face='Verdana' size='1'>&nbsp;</font></td>";
        if (strpos($result[0], "-P-") !== false) {
            print "<td><input name='chk69' type='checkbox' value='-P-' checked></td>";
        } else {
            print "<td><input name='chk69' type='checkbox' value='-P-'></td>";
        }
        if (strpos($result[0], "-A-") !== false) {
            print "<td><input name='chk70' type='checkbox' value='-A-' checked></td>";
        } else {
            print "<td><input name='chk70' type='checkbox' value='-A-'></td>";
        }
        if (strpos($result[0], "-A/F-") !== false) {
            print "<td><input name='chk71' type='checkbox' value='-A/F-' checked></td>";
        } else {
            print "<td><input name='chk71' type='checkbox' value='-A/F-'></td>";
        }
        print "<td bgcolor='#F0F0F0'><font face='Verdana' size='1'>&nbsp;</font></td>";
        if (strpos($result[0], "-NS-") !== false) {
            print "<td><input name='chk72' type='checkbox' value='-NS-' checked></td>";
        } else {
            print "<td><input name='chk72' type='checkbox' value='-NS-'></td>";
        }
        print "<td bgcolor='#F0F0F0'><font face='Verdana' size='1'>&nbsp;</font></td>";
        if (strpos($result[0], "-GC-") !== false) {
            print "<td><input name='chk73' type='checkbox' value='-GC-' checked></td>";
        } else {
            print "<td><input name='chk73' type='checkbox' value='-GC-'></td>";
        }
        if (strpos($result[0], "-LI-") !== false) {
            print "<td><input name='chk74' type='checkbox' value='-LI-' checked></td>";
        } else {
            print "<td><input name='chk74' type='checkbox' value='-LI-'></td>";
        }
        if (strpos($result[0], "-MB-") !== false) {
            print "<td><input name='chk75' type='checkbox' value='-MB-' checked></td>";
        } else {
            print "<td><input name='chk75' type='checkbox' value='-MB-'></td>";
        }
        if (strpos($result[0], "-EO-") !== false) {
            print "<td><input name='chk76' type='checkbox' value='-EO-' checked></td>";
        } else {
            print "<td><input name='chk76' type='checkbox' value='-EO-'></td>";
        }
        if (strpos($result[0], "-AO-") !== false) {
            print "<td><input name='chk77' type='checkbox' value='-AO-' checked></td>";
        } else {
            print "<td><input name='chk77' type='checkbox' value='-AO-'></td>";
        }
        print "</tr>";
    }
    echo "\t\t<tr>\r\n\t\t\t<td colspan='45' align='center' bgcolor='#FAFAFA'><br><input type='submit' class='btn btn-primary' value='Save Changes'></td>\t\t\t\r\n\t\t</tr>\r\n\t</form></table>\t\r\n\t</td></tr></table>\r\n\t";
}
echo "\t\r\n</div></center>";
print "</div></div></div></div></div>";
include 'footer.php';
?>