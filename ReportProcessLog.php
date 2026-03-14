<?php


ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "12";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=ReportProcessLog.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$act = $_GET["act"];
if ($act == "") {
    $act = $_POST["act"];
}
$prints = $_GET["prints"];
$excel = $_GET["excel"];
$message = $_GET["message"];
if ($message == "") {
    $message = "Process Log Report";
}
$txtFrom = $_POST["txtFrom"];
$txtTo = $_POST["txtTo"];
if ($txtFrom == "") {
    $txtFrom = "01/01/" . substr(insertToday(), 0, 4);
}
if ($txtTo == "") {
    $txtTo = displayToday();
}
$lstDB = $_POST["lstDB"];
if ($lstDB == "") {
    $lstDB = "Live";
}
if ($prints != "yes") {
    include 'header.php';
?>
<div class="page-breadcrumb">
                    <div class="row">
                        <div class="col-12 d-flex no-block align-items-center">
                            <h4 class="page-title">Process Log Report</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Process Log Report
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

<?php
}
print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='ReportProcessLog.php'><input type='hidden' name='act' value='searchRecord'>";
//print "<html><title>Process Log Report</title>";
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
        header("Content-Disposition: attachment; filename=ReportProcessLog.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        print "<body>";
    }
}

if ($excel != "yes") {
?>
<div class="card">
    <div class="card-body">
        <?php 
        print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b></font></p>";
        if ($prints != "yes") {
            print "<center><h4 class='card-title'>Select ONE or MORE options and click Search Record</h4></center>";
//            print "<table width='800' cellpadding='1' cellspacing='-1'>";
    //        print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Select ONE or MORE options and click 'Search Record'</b></font></td></tr>";
        } else {
//            print "<table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
//            print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Selected Options</b></font></td></tr>";
        }
        
        ?>
        <div class="row">
            <div class="col-3"></div>
            <div class="col-2">
                <?php 
                displayTextbox("txtFrom", "Date From (DD/MM/YYYY): ", $txtFrom, $prints, 12, "25%", "75%");
                ?>
            </div>
            <div class="col-2">
                <?php 
                displayTextbox("txtTo", "Date To (DD/MM/YYYY): ", $txtTo, $prints, 12, "25%", "75%");
                ?>
            </div>
            <div class="col-2">
                <?php 
                print "<label class='form-label'>DB:</label><select class='select2 form-select shadow-none' name='lstDB'><option selected value='" . $lstDB . "'>" . $lstDB . "</option> <option value='Live'>Live</option> <option value='Archive'>Archive</option> </select>";
                ?>
            </div>
            </div>
            <div class="row">
            <div class="col-12">
                <?php 
                if ($prints != "yes") {
                    print "<center><br><input name='btSearch' class='btn btn-primary' type='submit' value='Search Record'></center>";
                }
                ?>
            </div>
            </div>
    </div>
</div>
<?php
 } 
print "</div></div></div></div>";

if ($act == "searchRecord" && stripos($userlevel, $current_module . "R") !== false) {
    $table_name = "Access.ProcessLog";
    if ($lstDB == "Archive") {
        $table_name = "AccessArchive.archive_trans";
    }
    $query = "SELECT PDate, PTime, PType FROM " . $table_name . " WHERE ProcessID > 0";
    if ($txtFrom != "") {
        $query = $query . " AND PDate >= " . insertDate($txtFrom);
    }
    if ($txtTo != "") {
        $query = $query . " AND PDate <= " . insertDate($txtTo);
    }
    $query = $query . " ORDER BY PDate DESC, PTime DESC";
    print '<div class="row"><div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12"><div class="card"><div class="card-body table-responsive">';
    if ($prints != "yes") {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800' class='table table-striped table-bordered dataTable' id='zero_config'>";
    } else {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%' class='table table-striped table-bordered dataTable'>";
    }
    print "<thead><tr><td><font face='Verdana' size='2'><b>Process Date</b></font></td> <td><font face='Verdana' size='2'><b>Process Time</b></font></td> <td><font face='Verdana' size='2'><b>Process Type</b></font></td> </tr></thead>";
    $count = 0;
    $bgcolor = "#F0F0F0";
    $date = "";
    for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $count++) {
        if ($date != $cur[0]) {
            if ($bgcolor == "#F0F0F0") {
                $bgcolor = "#FFFFFF";
            } else {
                $bgcolor = "#F0F0F0";
            }
            $date = $cur[0];
        }
        displayDate($cur[0]);
        displayTime($cur[1]);
        print "<tr><td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1'>" . displayDate($cur[0]) . "</font></td> <td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1'>" . displayTime($cur[1]) . "</font></td> <td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1'>" . $cur[2] . "</font></td> </tr>";
    }
    print "</table>";
    if ($excel != "yes") {
        print "<br><p align='center'><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $count . "</b></font>";
    }
    if ($prints != "yes") {
        print "<br><input type='button' class='btn btn-primary' value='Print Report' onClick='checkPrint(0)'>&nbsp;&nbsp;<input type='button' class='btn btn-primary' value='Excel' onClick='checkPrint(1)'>";
    }
    print "</p>";
}
print "</form>";
echo "</center>";
print "</div></div></div></div></div>";
include 'footer.php';

?>