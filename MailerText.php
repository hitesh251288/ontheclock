<?php


ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "19";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=MailerText.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$iconn = openIConnection();
if ($prints != "yes") {
    include 'header.php';
?>
<style>
    .form-control{
        width: auto !important;
    }
</style>
<div class="page-breadcrumb">
                    <div class="row">
                        <div class="col-12 d-flex no-block align-items-center">
                            <h4 class="page-title">Mailer Text</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Alter Logs
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
print "<center>";
//displayHeader($prints, $date, $time);
$act = $_POST["act"];
if ($act == "") {
    $act = $_GET["act"];
}
$message = $_GET["message"];
if ($message == "") {
    $message = "Mailer Text";
}
$txtAttendance = $_POST["txtAttendance"];
$txtAbsence = $_POST["txtAbsence"];
$txtOddLog = $_POST["txtOddLog"];
$txtLateArrival = $_POST["txtLateArrival"];
$txtEarlyExit = $_POST["txtEarlyExit"];
$txtFlagApplication = $_POST["txtFlagApplication"];
if ($act == "editRecord") {
    if ($txtAttendance != "") {
        $query = "UPDATE MailerText SET MailerText = '" . replaceString($txtAttendance, false) . "' WHERE MailerType = 'Attendance'";
        updateIData($iconn, $query, true);
        $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Updated Attendance Mailer Text to: " . replaceString($txtAttendance, false) . "')";
        updateIData($iconn, $query, true);
    }
    if ($txtAbsence != "") {
        $query = "UPDATE MailerText SET MailerText = '" . replaceString($txtAbsence, false) . "' WHERE MailerType = 'Absence'";
        updateIData($iconn, $query, true);
        $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Updated Absence Mailer Text to: " . replaceString($txtAbsence, false) . "')";
        updateIData($iconn, $query, true);
    }
    if ($txtOddLog != "") {
        $query = "UPDATE MailerText SET MailerText = '" . replaceString($txtOddLog, false) . "' WHERE MailerType = 'OddLog'";
        updateIData($iconn, $query, true);
        $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Updated OddLog Mailer Text to: " . replaceString($txtOddLog, false) . "')";
        updateIData($iconn, $query, true);
    }
    if ($txtLateArrival != "") {
        $query = "UPDATE MailerText SET MailerText = '" . replaceString($txtLateArrival, false) . "' WHERE MailerType = 'LateArrival'";
        updateIData($iconn, $query, true);
        $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Updated LateArrival Mailer Text to: " . replaceString($txtLateArrival, false) . "')";
        updateIData($iconn, $query, true);
    }
    if ($txtEarlyExit != "") {
        $query = "UPDATE MailerText SET MailerText = '" . replaceString($txtEarlyExit, false) . "' WHERE MailerType = 'EarlyExit'";
        updateIData($iconn, $query, true);
        $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Updated EarlyExit Mailer Text to: " . replaceString($txtEarlyExit, false) . "')";
        updateIData($iconn, $query, true);
    }
    if ($txtFlagApplication != "") {
        $query = "UPDATE MailerText SET MailerText = '" . replaceString($txtFlagApplication, false) . "' WHERE MailerType = 'FlagApplication'";
        updateIData($iconn, $query, true);
        $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Updated FlagApplication Mailer Text to: " . replaceString($txtFlagApplication, false) . "')";
        updateIData($iconn, $query, true);
    }
    header("Location: MailerText.php?message=Text Updated");
}
echo "\r\n</script>\r\n";
print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b></font></p>";
print "<table width='800'>";
print "<form name='frm1' method='post' action='MailerText.php'><input type='hidden' name='act' value='editRecord'>";
$query = "SELECT MailerType, MailerText FROM MailerText";
$result = mysqli_query($conn, $query);
while ($cur = mysqli_fetch_row($result)) {
    print "<tr>";
    displayTextarea("txt" . $cur[0], $cur[0], $cur[1], "no", "5", "50", "40%", "60%");
    print "</tr>";
}
print "<tr><td>&nbsp;</td><td>";
if (stripos($userlevel, $current_module . "D") !== false) {
    print "<input type='submit' class='btn btn-primary' value='Save Changes'>";
} else {
    print "&nbsp;";
}
print "</td></tr>";
print "</form>";
echo "</table>\r\n</center>";
echo "<div><div><div><div><div>";
include 'footer.php';
?>