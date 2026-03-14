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
    header("Location: " . $config["REDIRECT"] . "?url=ReportUserInfo.php&message=Session Expired or Security Policy Violated");
}
$lstOrder = $_POST["lstOrder"];
if ($lstOrder == "") {
    $lstOrder = "Username";
}
$prints = $_GET["prints"];
$excel = $_GET["excel"];
$message = $_GET["message"];
if ($message == "") {
    $message = "User Info Report";
}
$conn = openConnection();
if ($prints != "yes") {
    include 'header.php';
?>
<div class="page-breadcrumb">
                    <div class="row">
                        <div class="col-12 d-flex no-block align-items-center">
                            <h4 class="page-title">User Info Report</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            User Info Report
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

<?php
}
//echo "\r\n<html><head><title>User Info Report</title></head>\r\n\r\n\t";
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
print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='ReportUserInfo.php'>";
if ($excel != "yes") {
?>
<div class="card">
    <div class="card-body">
        <?php 
        print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b></font></p>";
        
        ?>
        <div class="row">
            <div class="col-5"></div>
        <?php 
        if ($prints != "yes") {
            print "<div class='col-2'>";
            print "<label class='form-label'>Sort By:</label>";
            print "<select name='lstOrder' onChange='document.frm1.submit()' class='form-control select2 form-select shadow-none'><option selected value=''>---</option> <option value='Username'>Username</option> <option value='Usermail'>Email</option> <option value='LastLogin'>Last Login Date</option></select>";
            print "</div>";
        }
        ?>
        </div>
    </div>
</div>
<?php
 } 
print "</div></div></div></div>";
$query = "SELECT Username, Usermail, LastLogin FROM UserMaster WHERE Username NOT LIKE 'virdi' ORDER BY " . $lstOrder;
print '<div class="row"><div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12"><div class="card"><div class="card-body table-responsive">';
if ($prints != "yes") {
    print "<table width='800' bgcolor='#F0F0F0' class='table table-striped table-bordered dataTable'>";
} else {
    print "<table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' class='table table-striped table-bordered dataTable'>";
}
print "<tr><td bgcolor='#FFFFFF'><font face='Verdana' size='1'><b>Username</b></font></td> <td bgcolor='#FFFFFF'><font face='Verdana' size='1'><b>Email</b></font></td> <td bgcolor='#FFFFFF'><font face='Verdana' size='1'><b>Last Login</b></font></td> </tr>";
$counter = 0;
for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $counter++) {
    if ($cur[1] == "") {
        $cur[1] = "&nbsp;";
    }
    print "<tr>";
    if ($prints != "yes" && strpos($userlevel, $current_module . "V") !== false) {
        print "<td bgcolor='#FFFFFF' align='center'><a target='_blank' href='UserManagement.php?act=view&lstUsername=" . $cur[0] . "'><font face='Verdana' size='1'>" . $cur[0] . "</a></font></td>";
    } else {
        print "<td bgcolor='#FFFFFF' align='center'><font face='Verdana' size='1'>" . $cur[0] . "</font></td>";
    }
    displayDate($cur[2]);
    print "<td bgcolor='#FFFFFF'><font face='Verdana' size='1'>" . $cur[1] . "</font></td> <td bgcolor='#FFFFFF'><font face='Verdana' size='1'>" . displayDate($cur[2]) . "</font></td></tr>";
}
print "</table>";

if ($excel != "yes") {
    print "<br><p align='center'><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $counter . "</b></font>";
}
if ($prints != "yes") {
    print "<br><input type='button' value='Print Report' class='btn btn-primary' onClick='checkPrint(0)'>&nbsp;&nbsp;<input type='button' class='btn btn-primary' value='Excel' onClick='checkPrint(1)'>";
}
echo "\t</form>\r\n</div></center></body></html>";
print "</div></div></div></div></div>";;
include 'footer.php';
?>