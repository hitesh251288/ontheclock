<?php
ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "12";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!(checkSession($userlevel, $current_module) && $_SESSION[$session_variable . "VirdiLevel"] == "Classic")) {
    header("Location: " . $config["REDIRECT"] . "?url=ShiftRotation.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$iconn = openIConnection();
if ($prints != "yes") {
    include 'header.php';
    ?>
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-12 d-flex no-block align-items-center">
                <h4 class="page-title">Shift Rotation</h4>
                <div class="ms-auto text-end">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Shift Rotation
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <?php
}
print "<center>";
print'<div class="container-fluid">
                <div class="row">
                        <div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12">
                            <div class="card">
                                    <div class="card-body table-responsive">';
//print "<html><title>Shift Rotation</title><body><center>";
//displayHeader($prints, false, false);
//displayLinks($current_module, $userlevel);
//print "</center>";
$act = $_POST["act"];
if ($act == "") {
    $act = $_GET["act"];
}
$message = $_GET["message"];
$lstShift = $_POST["lstShift"];
if ($lstShift == "") {
    $lstShift = $_GET["lstShift"];
}
$txtID = $_POST["txtID"];
if ($txtID == "") {
    $txtID = $_GET["txtID"];
}
$counter = $_POST["txtCounter"];
$ae = $_POST["chkAE"];
if ($ae == "") {
    $ae = 0;
}
$lstSRDay = $_POST["lstSRDay"];
$lstSRScenario = $_POST["lstSRScenario"];
$txtRotateShiftNextDay = $_POST["txtRotateShiftNextDay"];
$txtSRTime = $_POST["txtSRTime"];
$txtIDF = $_GET["txtIDF"];
if ($act == "editRecord") {
    $message = editShiftChangeMaster($conn, $iconn, $counter, $ae, $txtIDF, $username, $lstSRDay, $lstSRScenario, $txtRotateShiftNextDay, $txtSRTime);
    header("Location: ShiftRotation.php?message=" . $message);
} else {
    if ($act == "addRecord") {
        $message = addShiftChangeMaster($conn, $iconn, $ae, $txtIDF, $username, $lstSRDay, $lstSRScenario, $txtRotateShiftNextDay, $txtSRTime);
        header("Location: ShiftRotation.php?message=" . $message);
    } else {
        if ($act == "deleteRecord") {
            $message = deleteShiftChangeMaster($conn, $iconn, $txtIDF, $username);
            header("Location: ShiftRotation.php?message=" . $message);
        }
    }
}
print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b></font></p>";
for ($i = 1; $i <= 20; $i++) {
    print "<br><p align='center'><font face='Verdana' size='1' color='#A0A0A0'><b>Shift Rotation Set " . $i . "</b></font></p>";
    $query = "SELECT id, AE, SRDay, SRScenario, RotateShiftNextDay, RTime FROM ShiftChangeMaster WHERE idf = " . $i . " ORDER BY ShiftChangeID";
    $result = selectData($conn, $query);
    $ae = $result[1];
    $srd = $result[2];
    $srs = $result[3];
    $rsnd = $result[4];
    $rtime = $result[5];
    print "<div class='row'><div class='col-3'></div>";
    print "<table width='800' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>"
    . "<form name='frm2" . $i . "' method='post' action='ShiftRotation.php?txtIDF=" . $i . "'><input type='hidden' name='act' value='addRecord'>";
    $query = "SELECT id, name FROM tgroup WHERE id NOT IN (SELECT id from ShiftChangeMaster) AND id > 2";
    print "<div class='col-4'>";
    displayList("lstShift", "Select a Shift to add to Rotation Cycle", "", $prints, $conn, $query, "", "50%", "50%");
    print "</div>";
    print "<div class='col-3'>";
    print "<input type='hidden' name='chkAE' value='" . $ae . "'> <input type='hidden' name='lstSRDay' value='" . $srd . "'> <input type='hidden' name='lstSRScenario' value='" . $srs . "'> <input type='hidden' name='txtRotateShiftNextDay' value='" . $rsnd . "'> <input type='hidden' name='txtSRTime' value='" . $rtime . "'>";
    if (stripos($userlevel, $current_module . "A") !== false) {
        print "<br><lable></label><input type='submit' value='Add to Rotation' class='btn btn-primary'>";
    }
//    print "</td></tr>";
    print "</div><div class='col-2'></div>";
    print "</form></table>";
    print "</div>";
    print "<div class='row'><div class='col-3'></div>";
    print "<table width='800' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'><form name='frm3" . $i . "' method='post' action='ShiftRotation.php?txtIDF=" . $i . "'><input type='hidden' name='act' value='deleteRecord'>";
    $query = "SELECT id, name FROM tgroup WHERE id IN (SELECT id from ShiftChangeMaster WHERE idf = " . $i . ")";
    print "<div class='col-4'>";
    displayList("lstShift", "Select a Shift to remove from Rotation Cycle", "", $prints, $conn, $query, "", "50%", "50%");
    print "</div>";
    print "<div class='col-3'>";
    print "<input type='hidden' name='chkAE' value='" . $ae . "'> <input type='hidden' name='lstSRDay' value='" . $srd . "'> <input type='hidden' name='lstSRScenario' value='" . $srs . "'> <input type='hidden' name='txtRotateShiftNextDay' value='" . $rsnd . "'> <input type='hidden' name='txtSRTime' value='" . $rtime . "'>";
    if (stripos($userlevel, $current_module . "A") !== false) {
        print "<br><lable></label><input type='submit' value='Remove from Rotation' class='btn btn-primary'>";
    }
    print "</td></tr>";
    print "</div><div class='col-2'></div>";
    print "</form></table>";
    print "</div>";
    print "<div class='row'><div class='col-2'></div>";
    print "<table width='800' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'><form name='frm1" . $i . "' method='post' action='ShiftRotation.php?txtIDF=" . $i . "'><input type='hidden' name='act' value='editRecord'>";
    $query = "SELECT id FROM ShiftChangeMaster WHERE idf = " . $i . " ORDER BY ShiftChangeID";
    $count = 0;
    $result2 = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_row($result2)) {
        $count++;
        $query = "SELECT id, Name FROM tgroup ORDER BY Name";
        print "<div class='col-3'>";
        displayList("lstShift" . ($count - 1), "Shift " . $count . ": ", $cur[0], $prints, $conn, $query, "", "50%", "50%");
        print "</div>";
    }
    print "<div class='col-1'></div></div>";
    mysqli_num_rows($result2);
    print "<input type='hidden' name='txtCounter' value='" . mysqli_num_rows($result2) . "'>";
    $query = "SELECT id, AE, SRDay, SRScenario, RotateShiftNextDay, RTime FROM ShiftChangeMaster WHERE idf = " . $i . " ORDER BY ShiftChangeID";
    $result = selectData($conn, $query);
    print "<tr><td align='right'><font face='Verdana' size='2'>Execute Automatic Shift Rotation</font></td><td>";
    if ($result[1] == 1) {
        print "<input type='checkbox' value='1' name='chkAE' checked>";
    } else {
        print "<input type='checkbox' value='1' name='chkAE'>";
    }
    print "</td></tr>";
    print "<tr>";
    print "<td align='right'><font face='Verdana' size='2'>Shift Rotation Day</font></td><td><select name='lstSRDay' class='form-control'><option selected value = '" . $result[2] . "'>" . $result[2] . "</option> <option value = 'Saturday'>Saturday</option> <option value = 'Sunday'>Sunday</option> <option value = 'Monday'>Monday</option> <option value = 'None'>None</option></select></td></tr>";
    print "</tr>";
    print "<tr>";
    print "<td align='right'><font face='Verdana' size='2'>Shift Rotation Scenario</font></td><td><select name='lstSRScenario' class='form-control'><option selected value = '" . $result[3] . "'>" . $result[3] . "</option> <option value = 'Morning - 2 Shifts'>Morning - 2 Shifts</option> <option value = 'Morning - 2 Shifts (No Day Shift on Rotation Day)'>Morning - 2 Shifts (No Day Shift on Rotation Day)</option> <option value = 'Morning - 2 Shifts (Day to Day Shift)'>Morning - 2 Shifts (Day to Day Shift)</option> <option value = 'Evening - 2 Shifts'>Evening - 2 Shifts</option> <option value = 'Morning - 3 Shifts'>Morning - 3 Shifts</option> <option value = 'Evening - 3 Shifts'>Evening - 3 Shifts</option> <option value = 'None'>None</option></select></td></tr>";
    print "</tr>";
    print "<tr>";
    displayTextboxS("txtRotateShiftNextDay", "Next Shift Rotation Date <br><font size='1' color='#FF0000'>[Edit this Field <b>ONLY</b> if you have RUN the Shift Rotation Process Manually]</font>", displayDate($result[4]), $prints, 12, "50%", "50%", " onBlur='checkValidDate(this)' ");
    print "</tr>";
    print "<tr>";
    displayTextboxS("txtSRTime", "Shift Rotation Time", addZero($result[5], 4), $prints, 5, "50%", "50%", " onBlur='checkValidTime(this)' ");
    print "</tr>";
    print "<tr><td>&nbsp;</td><td>";
    if (stripos($userlevel, $current_module . "E") !== false) {
        print "<input type='submit' value='Save Changes' class='btn btn-primary'>";
    }
    if (stripos($userlevel, $current_module . "D") !== false) {
        print "&nbsp;&nbsp;<input type='button' value='Rotate Manually' id='bt" . $i . "' onClick='javascript:openScriptWindow(" . $i . ")' class='btn btn-primary'>";
    }
    print "<br><br><font face='Verdana' size='1'>To SET Shift Rotation Schedule for <b>THIS SET</b> - Go to -->Start - Programs - Accessories - System Tools - Task Manager--< and Create a New Task to be RUN on the specified DAY and TIME. Select the Program to be RUN as '[PHP_ROOT]/virdi/<b>RotateShift" . $i . ".bat</b>'. <br><br>To SET Shift Rotation Schedule for <b>ALL SETS</b> - Go to -->Start - Programs - Accessories - System Tools - Task Manager--< and Create a New Task to be RUN on the specified DAY and TIME. Select the Program to be RUN as '[PHP_ROOT]/virdi/<b>RotateShift.bat</b>'. <br><br>Scheduled Tasks are System Password DEPENDENT. Please make sure that you also change the Password for the Task whenever you change the System Password.</font></td></tr>";
    print "</form>";
    print "</table><br>";
}
print '</div></div></div></div></div>';
echo "<script>\r\n\tfunction checkValidDate(x){\r\n\t\tif (check_valid_date(x.value) == false){\r\n\t\t\talert('Invalid Date. Date Format should be DD/MM/YYYY');\r\n\t\t\tx.focus();\r\n\t\t}\r\n\t}\r\n\r\n\tfunction checkValidTime(x){\r\n\t\ta = x.value + \"00\";\r\n\t\tif (check_valid_time(a) == false){\r\n\t\t\talert('Invalid Time. Time Format should be HHMM');\r\n\t\t\tx.focus();\r\n\t\t}\r\n\t}\r\n\r\n\tfunction openScriptWindow(a){\r\n\t\tif (confirm('Rotate this Shift Set ['+a+'] Manually')){\r\n\t\t\tdocument.getElementById('bt'+a).disabled = true;\r\n\t\t\twindow.open('ExecuteScript.php?script=RotateShift'+a, 'ExecuteScript', 'height=300;width=400;resize=no;menubar=no;addressbar=no');\r\n\t\t}\t\t\t\r\n\t}\r\n</script>\r\n\t<!-- <iframe align='center' width=\"800\" height=\"350\" src=\"ShiftRotation2.php\" SCROLLING=\"no\" FRAMEBORDER=\"0\" border=0></iframe>\r\n\t<iframe align='center' width=\"800\" height=\"350\" src=\"ShiftRotation3.php\" SCROLLING=\"no\" FRAMEBORDER=\"0\" border=0></iframe>\r\n\t<iframe align='center' width=\"800\" height=\"350\" src=\"ShiftRotation4.php\" SCROLLING=\"no\" FRAMEBORDER=\"0\" border=0></iframe>\r\n\t<iframe align='center' width=\"800\" height=\"350\" src=\"ShiftRotation5.php\" SCROLLING=\"no\" FRAMEBORDER=\"0\" border=0></iframe>\r\n\t<iframe align='center' width=\"800\" height=\"350\" src=\"ShiftRotation6.php\" SCROLLING=\"no\" FRAMEBORDER=\"0\" border=0></iframe>\r\n\t<iframe align='center' width=\"800\" height=\"350\" src=\"ShiftRotation7.php\" SCROLLING=\"no\" FRAMEBORDER=\"0\" border=0></iframe>\r\n\t<iframe align='center' width=\"800\" height=\"350\" src=\"ShiftRotation8.php\" SCROLLING=\"no\" FRAMEBORDER=\"0\" border=0></iframe>\r\n\t<iframe align='center' width=\"800\" height=\"350\" src=\"ShiftRotation9.php\" SCROLLING=\"no\" FRAMEBORDER=\"0\" border=0></iframe>\r\n\t<iframe align='center' width=\"800\" height=\"350\" src=\"ShiftRotation10.php\" SCROLLING=\"no\" FRAMEBORDER=\"0\" border=0></iframe> -->\r\n";
print '</center>';
include 'footer.php';
?>