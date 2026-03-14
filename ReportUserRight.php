<?php


ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "11";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$username = $_SESSION[$session_variable . "username"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$userstatus = $_SESSION[$session_variable . "userstatus"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=ReportUserRight.php&message=Session Expired or Security Policy Violated");
}
$lstOrder = $_POST["lstOrder"];
if ($lstOrder == "") {
    $lstOrder = "Userstatus, Username";
}
$prints = $_GET["prints"];
$excel = $_GET["excel"];
$message = $_GET["message"];
if ($message == "") {
    $message = "User Rights Report";
}
$conn = openConnection();
if ($prints != "yes") {
    include 'header.php';
?>
<div class="page-breadcrumb">
                    <div class="row">
                        <div class="col-12 d-flex no-block align-items-center">
                            <h4 class="page-title">User Rights Report</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            User Rights Report
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

<?php
}
//echo "\r\n<html><head><title>User Rights Report</title></head>\r\n\t";
if ($prints != "yes") {
//    print "<body>";
    print'<div class="container-fluid">
                <div class="row">
                        <div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12">
                            <div class="card">
                                    <div class="card-body table-responsive">';
} else {
    if ($excel != "yes") {
        print "<body onLoad='javascript:window.print()'>";
    } else {
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=ReportEmployee.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        print "<body>";
    }
}
print "<center>";
if ($excel != "yes") {
//    displayHeader($prints, false, false);
}
print "<center>";
if ($prints != "yes") {
//    displayLinks(18, $userlevel);
}
print "</center>";
if ($excel != "yes") {
    print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b></font></p>";
}
if ($prints != "yes") {
    print "<h4 class='card-title'>Select ONE or MORE options and click Search Record</h4>";
    print "<table width='800' bgcolor='#F0F0F0'>";
} else {
    print "<table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
}
print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='ReportUserRight.php'>";
$query = "SELECT Username, Usermail, LastLogin, Userlevel FROM UserMaster WHERE Username NOT LIKE 'virdi' ORDER BY Userstatus, Username";
$counter = 0;
for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $counter++) {
    displayDate($cur[2]);
    print "<tr><td bgcolor='#FFFFFF'><font face='Verdana' size='1'>Username: <b>" . $cur[0] . "</b>, Email: <b>" . $cur[1] . "</b>, Last Login: <b>" . displayDate($cur[2]) . "</b></font></td> </tr>";
    displayAllRights($cur[3], getModules());
}
print "</table></form>";
if ($excel != "yes") {
    print "<br><p align='center'><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $counter . "</b></font>";
}
if ($prints != "yes") {
    print "<br><input type='button' class='btn btn-primary' value='Print Report' onClick='checkPrint(0)'>";
}
echo "</div></center>";
print "</div></div></div></div>";
include 'footer.php';

?>