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
    header("Location: " . $config["REDIRECT"] . "?url=ReportShiftRotation.php&message=Session Expired or Security Policy Violated");
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
    $message = "Shift Rotation Report";
}
$txtFrom = $_POST["txtFrom"];
$txtTo = $_POST["txtTo"];
if ($txtFrom == "") {
    $txtFrom = "01/01/" . substr(insertToday(), 0, 4);
}
if ($txtTo == "") {
    $txtTo = displayToday();
}
if ($prints != "yes") {
    include 'header.php';
    ?>
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-12 d-flex no-block align-items-center">
                <h4 class="page-title">Shift Rotation Report</h4>
                <div class="ms-auto text-end">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Shift Rotation Report
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <?php
}
if ($prints != "yes") {
    print'<div class="container-fluid">
                <div class="row">
                        <div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12">
                            <div class="card">
                                    <div class="card-body table-responsive">';
} else {
    if ($excel != "yes") {
        print "<body onLoad='javascript:window.print()'>";
    } else {
        ob_end_clean();
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=ReportEmployee.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        print "<body>";
    }
}
print "<center>";
//if ($excel != "yes") {
//    displayHeader($prints, true, false);
//}
//print "<center>";
//if ($prints != "yes") {
//    displayLinks(18, $userlevel);
//}
//print "</center>";
if ($excel != "yes") {
    ?>
<div class="card">
    <div class="card-body">
        <?php
    print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b></font></p>";
    if ($prints != "yes") {
//        print "<table width='800' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
        print "<center><h4 class='card-title'>Select ONE or MORE options and click 'Search Record'</h4></center>";
    } else {
//        print "<table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
        print "<center><h4 class='card-title'>Selected Options</h4></center>";
    }
    print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='ReportShiftRotation.php'><input type='hidden' name='act' value='searchRecord'><tr>";
    print "<div class='row'>";
    print "<div class='col-3'></div>";
    print "<div class='col-3'>";
    displayTextbox("txtFrom", "Date From (DD/MM/YYYY): ", $txtFrom, $prints, 12, "25%", "75%");
    print "</div>";
    print "<div class='col-3'>";
    displayTextbox("txtTo", "Date To (DD/MM/YYYY): ", $txtTo, $prints, 12, "25%", "75%");
    print "</div>";
    print "<div class='col-3'></div>";
    print "</div>";
    print "<div class='row'>";
    print "<div class='col-3'></div>";
    print "<div class='col-6'>";
    if ($prints != "yes") {
        print "<input name='btSearch' type='submit' value='Search Record' class='btn btn-primary'>";
//        . "&nbsp;&nbsp;<input type='button' value='Excel' onClick='checkPrint(1)'>&nbsp;&nbsp;<input type='button' value='CSV' onClick='checkPrint(3)'></td></tr></td></tr>";
    }
    print "</div>";
    print "<div class='col-3'></div>";
    print "</div>";
//    print "</table><br>";
    ?>
     </div>
        </div>   
        <?php
}
print '</div></div></div></div>';
if ($act == "searchRecord" && stripos($userlevel, $current_module . "R") !== false) {
    $query = "SELECT RDate, RTime, ShiftFrom, ShiftTo FROM ShiftRotateLog WHERE ID > 0";
    if ($txtFrom != "") {
        $query = $query . " AND RDate >= " . insertDate($txtFrom);
    }
    if ($txtTo != "") {
        $query = $query . " AND RDate <= " . insertDate($txtTo);
    }
    $query = $query . " ORDER BY RDate DESC, RTime";
    print '<div class="row"><div class="col-md-12"><div class="card"><div class="card-body table-responsive">';
    print "<div class='row'><div class='col-2'></div><div class='col-8'>";
    if ($prints != "yes") {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800'>";
    } else {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%'>";
    }
    
    print "<tr><td><font face='Verdana' size='2'><b>Rotation Date</b></font></td> <td><font face='Verdana' size='2'><b>Rotation Time</b></font></td> <td><font face='Verdana' size='2'><b>Shift From</b></font></td> <td><font face='Verdana' size='2'><b>Shift To</b></font></td> </tr>";
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
        $query = "SELECT name FROM tgroup WHERE id = " . $cur[2];
        $result1 = selectData($conn, $query);
        $cur[2] = $result1[0];
        $query = "SELECT name FROM tgroup WHERE id = " . $cur[3];
        $result1 = selectData($conn, $query);
        $cur[3] = $result1[0];
        displayDate($cur[0]);
        displayTime($cur[1]);
        print "<tr><td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1'>" . displayDate($cur[0]) . "</font></td> <td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1'>" . displayTime($cur[1]) . "</font></td> <td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1'>" . $cur[2] . "</font></td> <td bgcolor='" . $bgcolor . "'><font face='Verdana' size='1'>" . $cur[3] . "</font></td> </tr>";
    }
    print "</table>";
    print "</div><div class='col-2'></div></div>";
    if ($excel != "yes") {
        print "<br><p align='center'><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $count . "</b></font>";
    }
    if ($prints != "yes") {
        print "<br><input type='button' value='Print Report' onClick='checkPrint(0)' class='btn btn-primary'>&nbsp;&nbsp;<input type='button' value='Excel' onClick='checkPrint(1)' class='btn btn-primary'>";
    }
    print "</p>";
    print "</div></div></div></div>";
}
print "</form>";
print "</div>";
echo "</center>";
include 'footer.php';

?>