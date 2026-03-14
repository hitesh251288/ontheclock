<?php


ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "33";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=DrillMaster.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$iconn = openIConnection();
if ($prints != "yes") {
    include 'header.php';
?>
<div class="page-breadcrumb">
                    <div class="row">
                        <div class="col-12 d-flex no-block align-items-center">
                            <h4 class="page-title">Drill (Task/ Exercise)</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Drill (Task/ Exercise)
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

<?php
}

//print "<html><title>Drill (Task/ Exercise)</title><body><center>";
//displayHeader($prints, true, true);
print "<center>";
//displayLinks($current_module, $userlevel);
print "</center>";

$act = $_GET["act"];
if ($act == "") {
    $act = $_POST["act"];
}
$message = $_GET["message"];
if ($message == "") {
    $message = "Drill (Task/ Exercise)";
}
$txtGroupAdd = $_POST["txtGroupAdd"];
$txtGroup = $_POST["txtGroup"];
$lstDrill = $_POST["lstDrill"];
if ($lstDrill == "") {
    $lstDrill = $_GET["lstDrill"];
}
$txtCountTerminal = $_POST["txtCountTerminal"];
$txtCountDiv = $_POST["txtCountDiv"];
$txtCountDept = $_POST["txtCountDept"];
$txtCountRemark = $_POST["txtCountRemark"];
$txtCountPhone = $_POST["txtCountPhone"];
$txtCountIdNo = $_POST["txtCountIdNo"];
$txtFrom = $_POST["txtFrom"];
if ($txtFrom == "") {
    $txtFrom = $_GET["txtFrom"];
}
if ($txtFrom == "") {
    $txtFrom = displayToday();
}
$txtTimeFrom = $_POST["txtTimeFrom"];
$txtTimeTo = $_POST["txtTimeTo"];
if ($txtTimeFrom == "") {
    $txtTimeFrom = "000000";
}
if ($txtTimeTo == "") {
    $txtTimeTo = "235900";
}
if ($act == "deleteRecord") {
    $query = "DELETE FROM DrillMaster WHERE DrillMasterID = " . $lstDrill;
    updateIData($iconn, $query, true);
    $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Deleted Drill ID: " . $lstDrill . "')";
    updateIData($iconn, $query, true);
    $message = "Record Deleted";
    header("Location: DrillMaster.php?message=" . $message);
} else {
    if ($act == "addRecord") {
        $query = "SELECT DrillMasterID FROM DrillMaster WHERE DrillDate = '" . insertDate($txtFrom) . "' AND DrillTimeFrom = '" . $txtTimeFrom . "' AND DrillTimeTo = '" . $txtTimeTo . "'";
        $result = selectData($conn, $query);
        if ($result[0] == "") {
            $query = "INSERT INTO DrillMaster (DrillDate, DrillTimeFrom, DrillTimeTo) VALUES ('" . insertDate($txtFrom) . "', '" . $txtTimeFrom . "', '" . $txtTimeTo . "')";
            updateIData($iconn, $query, true);
            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Created Drill for Date: " . insertDate($txtFrom) . ", Time From: " . $txtTimeFrom . ", Time To: " . $txtTimeTo . "')";
            updateIData($iconn, $query, true);
            $message = "Record Added";
            header("Location: DrillMaster.php?message=" . $message);
        } else {
            $message = "Drill for the entered Date and Time ALREADY exists. Kindly use the Editing Mode";
        }
    } else {
        if ($act == "changeDates") {
            $query = "UPDATE DrillMaster SET DrillDate = '" . insertDate($txtFrom) . "', DrillTimeFrom = '" . $txtTimeFrom . "', DrillTimeTo = '" . $txtTimeTo . "'  WHERE DrillMaster.DrillMasterID = '" . $lstDrill . "'";
            updateIData($iconn, $query, true);
            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Edited Drill - Date: " . insertDate($txtFrom) . ", Time From: " . $txtTimeFrom . ", Time To: " . $txtTimeTo . "')";
            updateIData($iconn, $query, true);
            $message = "Record Updated";
            header("Location: DrillMaster.php?lstDrill=" . $lstDrill . "&message=" . $message);
        } else {
            if ($act == "editRecord") {
                $text = "Assigned Terminal to Drill: " . $lstDrill . ": ";
                $query = "DELETE FROM DrillTerminal WHERE DrillMasterID = " . $lstDrill;
                updateIData($iconn, $query, true);
                for ($i = 0; $i < $txtCountTerminal; $i++) {
                    if ($_POST["chkTerminal" . $i] != "") {
                        $query = "INSERT INTO DrillTerminal (DrillMasterID, g_id) VALUES (" . $lstDrill . ", '" . $_POST["chkTerminal" . $i] . "')";
                        updateIData($iconn, $query, true);
                        $text = $text . $_POST["chkTerminal" . $i] . ", ";
                    }
                }
                if (1020 < strlen($text)) {
                    $text = substr($text, 0, 1020);
                }
                $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                updateIData($iconn, $query, true);
                $text = "Assigned Division to Drill: " . $lstDrill . ": ";
                $query = "DELETE FROM DrillDiv WHERE DrillMasterID = " . $lstDrill;
                updateIData($iconn, $query, true);
                for ($i = 0; $i < $txtCountDiv; $i++) {
                    if ($_POST["chkDiv" . $i] != "") {
                        $query = "INSERT INTO DrillDiv (DrillMasterID, DrillDiv.Div) VALUES (" . $lstDrill . ", '" . $_POST["chkDiv" . $i] . "')";
                        updateIData($iconn, $query, true);
                        $text = $text . $_POST["chkDiv" . $i] . ", ";
                    }
                }
                if (1020 < strlen($text)) {
                    $text = substr($text, 0, 1020);
                }
                $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                updateIData($iconn, $query, true);
                $text = "Assigned Department to Drill: " . $lstDrill . ": ";
                $query = "DELETE FROM DrillDept WHERE DrillMasterID = " . $lstDrill;
                updateIData($iconn, $query, true);
                for ($i = 0; $i < $txtCountDept; $i++) {
                    if ($_POST["chkDept" . $i] != "") {
                        $query = "INSERT INTO DrillDept (DrillMasterID, Dept) VALUES (" . $lstDrill . ", '" . $_POST["chkDept" . $i] . "')";
                        updateIData($iconn, $query, true);
                        $text = $text . $_POST["chkDept" . $i] . ", ";
                    }
                }
                if (1020 < strlen($text)) {
                    $text = substr($text, 0, 1020);
                }
                $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                updateIData($iconn, $query, true);
                $text = "Assigned Remark to Drill: " . $lstDrill . ": ";
                $query = "DELETE FROM DrillRemark WHERE DrillMasterID = " . $lstDrill;
                updateIData($iconn, $query, true);
                for ($i = 0; $i < $txtCountRemark; $i++) {
                    if ($_POST["chkRemark" . $i] != "") {
                        $query = "INSERT INTO DrillRemark (DrillMasterID, Remark) VALUES (" . $lstDrill . ", '" . $_POST["chkRemark" . $i] . "')";
                        updateIData($iconn, $query, true);
                        $text = $text . $_POST["chkRemark" . $i] . ", ";
                    }
                }
                if (1020 < strlen($text)) {
                    $text = substr($text, 0, 1020);
                }
                $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                updateIData($iconn, $query, true);
                $text = "Assigned " . $_SESSION[$session_variable . "PhoneColumnName"] . " to Drill: " . $lstDrill . ": ";
                $query = "DELETE FROM DrillPhone WHERE DrillMasterID = " . $lstDrill;
                updateIData($iconn, $query, true);
                for ($i = 0; $i < $txtCountPhone; $i++) {
                    if ($_POST["chkPhone" . $i] != "") {
                        $query = "INSERT INTO DrillPhone (DrillMasterID, Phone) VALUES (" . $lstDrill . ", '" . $_POST["chkPhone" . $i] . "')";
                        updateIData($iconn, $query, true);
                        $text = $text . $_POST["chkPhone" . $i] . ", ";
                    }
                }
                if (1020 < strlen($text)) {
                    $text = substr($text, 0, 1020);
                }
                $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                updateIData($iconn, $query, true);
                $text = "Assigned " . $_SESSION[$session_variable . "IDColumnName"] . " to Drill: " . $lstDrill . ": ";
                $query = "DELETE FROM DrillIdNo WHERE DrillMasterID = " . $lstDrill;
                updateIData($iconn, $query, true);
                for ($i = 0; $i < $txtCountIdNo; $i++) {
                    if ($_POST["chkIdNo" . $i] != "") {
                        $query = "INSERT INTO DrillIdNo (DrillMasterID, IdNo) VALUES (" . $lstDrill . ", '" . $_POST["chkIdNo" . $i] . "')";
                        updateIData($iconn, $query, true);
                        $text = $text . $_POST["chkIdNo" . $i] . ", ";
                    }
                }
                if (1020 < strlen($text)) {
                    $text = substr($text, 0, 1020);
                }
                $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                updateIData($iconn, $query, true);
                $message = "Record Updated";
                header("Location: DrillMaster.php?lstDrill=" . $lstDrill . "&message=" . $message);
            }
        }
    }
}
echo "<script>\r\nfunction deleteRecord(){\r\n\tif (confirm('Delete this Record')){\r\n\t\twindow.location.href='DrillMaster.php?act=deleteRecord&lstDrill='+document.frm1.lstDrill.value;\r\n\t}\r\n}\r\n</script>\r\n";
$txtDrillDate = "";
if ($prints != "yes") {
    print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b></font></p>";
    print'<div class="container-fluid">
                <div class="row">
                        <div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12">
                            <div class="card">
                                    <div class="card-body table-responsive">';
    print "<table width='800' cellpadding='1' cellspacing='-1'>";
    if ($lstDrill == "" && stripos($userlevel, $current_module . "A") !== false) {
        print "<tr><td bgcolor='#F0F0F0' colspan='2'>&nbsp;</td></tr>";
        print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Add a new record</b></font></td></tr>";
        print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='DrillMaster.php'><input type='hidden' name='txtTo' value='01/01/2090'> <input type='hidden' name='act' value='addRecord'>";
        print "<tr>";
        displayTextbox("txtFrom", "Drill Date: <font size='1'>(DD/MM/YYYY)</font>", $txtFrom, $prints, "12", "20%", "80%");
        print "</tr>";
        print "<tr>";
        displayTextbox("txtTimeFrom", "Time From: <font size='1'>(HHMMSS)</font>", $txtTimeFrom, $prints, "8", "20%", "80%");
        print "</tr>";
        print "<tr>";
        displayTextbox("txtTimeTo", "Time To: <font size='1'>(HHMMSS)</font>", $txtTimeTo, $prints, "8", "20%", "80%");
        print "</tr>";
        print "<tr><td>&nbsp;</td><td><input type='submit' value='Submit' name='btSearch' class='btn btn-primary'></td></tr>";
        print "<tr><td bgcolor='#FFFFFF' colspan='2'><img height='2' width='100%' src='img/orange-bar.gif'/></td></tr>";
        print "<tr><td bgcolor='#F0F0F0' colspan='2'>&nbsp;</td></tr>";
        print "</form>";
    }
    
    print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Select a record from list to edit/delete</b></font></td></tr>";
    if ($lstDrill != "") {
        print "<form name='frm1' method='post' action='DrillMaster.php' onSubmit='return checkSearch()'> <input type='hidden' name='txtTo' value='01/01/2090'> <input type='hidden' name='act' value='changeDates'>";
    } else {
        print "<form name='frm2' method='post'>";
    }
    print "<tr>";
    $query = "SELECT DrillMasterID, CONCAT( DATE_FORMAT( DrillDate, '%d/%m/%Y' ) , ': ', SUBSTR(DrillTimeFrom, 1, 4), ' - ', SUBSTR(DrillTimeTo, 1, 4) ) FROM DrillMaster ORDER BY DrillDate";
    if ($lstDrill != "") {
        displayList("lstDrill", "Drill: ", $lstDrill, $prints, $conn, $query, "onChange=javascript:window.location.href='DrillMaster.php?lstDrill='+document.frm1.lstDrill.value", "20%", "80%");
    } else {
        displayList("lstDrill", "Drill: ", $lstDrill, $prints, $conn, $query, "onChange=javascript:window.location.href='DrillMaster.php?lstDrill='+document.frm2.lstDrill.value", "20%", "80%");
    }
    print "</tr>";
    if ($lstDrill != "") {
        $query = "SELECT DrillMasterID, DrillDate, DrillTimeFrom, DrillTimeTo FROM DrillMaster WHERE DrillMasterID = " . $lstDrill;
        $result = selectData($conn, $query);
        $txtDrillDate = $result[1];
        print "<tr>";
        displayTextbox("txtFrom", "Date: <font size='1'>(DD/MM/YYYY)</font>", displayDate($result[1]), $prints, "12", "20%", "80%");
        print "</tr>";
        print "<tr>";
        displayTextbox("txtTimeFrom", "Time From: <font size='1'>(HHMMSS)</font>", $result[2], $prints, "8", "20%", "80%");
        print "</tr>";
        print "<tr>";
        displayTextbox("txtTimeTo", "Time To: <font size='1'>(HHMMSS)</font>", $result[3], $prints, "8", "20%", "80%");
        print "</tr><tr><td>&nbsp;</td><td>";
        if (stripos($userlevel, $current_module . "E") !== false && insertToday() < $result[1]) {
            print "<input type='submit' value='Change Date/Time'>";
        }
        if (stripos($userlevel, $current_module . "D") !== false && insertToday() < $result[1]) {
            print "&nbsp;&nbsp;<input type='button' value='Delete Record' onClick='javascript:deleteRecord()'>";
        }
        print "&nbsp;</td></tr>";
    }
    print "</form>";
    
}
if ($lstDrill != "") {
    print "<form name='frm2' method='post' action='DrillMaster.php'><input type='hidden' name='act' value='editRecord'><input type='hidden' name='lstDrill' value='" . $lstDrill . "'>";
//    print '<div class="row"><div class="col-md-12"><div class="card"><div class="card-body table-responsive">';
    if ($prints != "yes") {
        print "<table width='800' bgcolor='#F0F0F0' border='0' class='table table-striped table-bordered dataTable'>";
    } else {
        print "<table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' class='table table-striped table-bordered dataTable'>";
    }
    print "<tr><td bgcolor='#FFFFFF' align='center' width='100%' colspan='6'><font face='Verdana' size='1'><b>Check Terminal/ Employees to be Assigned to this Drill</b></font></td></tr>";
    $counterTerminal = 0;
    $counterDiv = 0;
    $counterDept = 0;
    $counterRemark = 0;
    $counterPhone = 0;
    $counterIdNo = 0;
    print "<tr><td vAlign='top'><table border='1' bordercolor='#FFFFFF' cellspacing='-1'>";
    print "<tr><td><input type='checkbox' id='chkAllTerminal' name='chkAllTerminal' onClick='checkAll(6, document.frm2.txtCountTerminal.value)'></td><td><font face='Verdana' size='2'><b>Terminal</b></font></td></tr>";
    $query = "SELECT DrillTerminal.g_id, tgate.name FROM DrillTerminal, tgate WHERE DrillTerminal.g_id = tgate.id AND DrillTerminal.DrillMasterID = " . $lstDrill . " ORDER BY DrillTerminal.g_id";
    for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $counterTerminal++) {
        print "<tr><td><input checked type='checkbox' id='chkTerminal" . $counterTerminal . "' name='chkTerminal" . $counterTerminal . "' value='" . $cur[0] . "'></td><td><font face='Verdana' size='1'>" . $cur[1] . "</font></td></tr>";
    }
    $query = "SELECT id, name FROM tgate WHERE id NOT IN (SELECT DrillTerminal.g_id FROM DrillTerminal WHERE DrillMasterID = " . $lstDrill . ") AND id > 0 ORDER BY id";
    for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $counterTerminal++) {
        print "<tr><td><input type='checkbox' id='chkTerminal" . $counterTerminal . "' name='chkTerminal" . $counterTerminal . "' value='" . $cur[0] . "'></td><td><font face='Verdana' size='1'>" . $cur[1] . "</font></td></tr>";
    }
    print "</table></td><input type='hidden' name='txtCountTerminal' value='" . $counterTerminal . "'>";
    print "<td vAlign='top'><table border='1' bordercolor='#FFFFFF' cellspacing='-1'>";
    print "<tr><td><input type='checkbox' id='chkAllDiv' name='chkAllDiv' onClick='checkAll(1, document.frm2.txtCountDiv.value)'></td><td><font face='Verdana' size='2'><b>Division</b></font></td></tr>";
    $query = "SELECT DrillDiv.Div FROM DrillDiv WHERE DrillMasterID = " . $lstDrill . " ORDER BY DrillDiv.Div";
    for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $counterDiv++) {
        print "<tr><td><input checked type='checkbox' id='chkDiv" . $counterDiv . "' name='chkDiv" . $counterDiv . "' value='" . $cur[0] . "'></td><td><font face='Verdana' size='1'>" . $cur[0] . "</font></td></tr>";
    }
    $query = "SELECT DISTINCT(company) FROM tuser WHERE company NOT IN (SELECT DrillDiv.Div FROM DrillDiv WHERE DrillMasterID = " . $lstDrill . ") AND length(company) > 0 ORDER BY company";
    for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $counterDiv++) {
        print "<tr><td><input type='checkbox' id='chkDiv" . $counterDiv . "' name='chkDiv" . $counterDiv . "' value='" . $cur[0] . "'></td><td><font face='Verdana' size='1'>" . $cur[0] . "</font></td></tr>";
    }
    print "</table></td><input type='hidden' name='txtCountDiv' value='" . $counterDiv . "'>";
    print "<td vAlign='top'><table border='1' bordercolor='#FFFFFF' cellspacing='-1'>";
    print "<tr><td><input type='checkbox' id='chkAllDept' name='chkAllDept' onClick='checkAll(2, document.frm2.txtCountDept.value)'></td><td><font face='Verdana' size='2'><b>Dept</b></font></td></tr>";
    $query = "SELECT Dept FROM DrillDept WHERE DrillMasterID = " . $lstDrill . " ORDER BY Dept";
    for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $counterDept++) {
        print "<tr><td><input checked type='checkbox' id='chkDept" . $counterDept . "' name='chkDept" . $counterDept . "' value='" . $cur[0] . "'></td><td><font face='Verdana' size='1'>" . $cur[0] . "</font></td></tr>";
    }
    $query = "SELECT DISTINCT(dept) FROM tuser WHERE dept NOT IN (SELECT dept FROM DrillDept WHERE DrillMasterID = " . $lstDrill . ") AND length(dept) > 0 ORDER BY dept";
    for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $counterDept++) {
        print "<tr><td><input type='checkbox' id='chkDept" . $counterDept . "' name='chkDept" . $counterDept . "' value='" . $cur[0] . "'></td><td><font face='Verdana' size='1'>" . $cur[0] . "</font></td></tr>";
    }
    print "</table></td><input type='hidden' name='txtCountDept' value='" . $counterDept . "'>";
    print "<td vAlign='top'><table border='1' bordercolor='#FFFFFF' cellspacing='-1'>";
    print "<tr><td><input type='checkbox' id='chkAllRemark' name='chkAllRemark' onClick='checkAll(3, document.frm2.txtCountRemark.value)'></td><td><font face='Verdana' size='2'><b>Remark</b></font></td></tr>";
    $query = "SELECT Remark FROM DrillRemark WHERE DrillMasterID = " . $lstDrill . " ORDER BY Remark";
    for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $counterRemark++) {
        print "<tr><td><input checked type='checkbox' id='chkRemark" . $counterRemark . "' name='chkRemark" . $counterRemark . "' value='" . $cur[0] . "'></td><td><font face='Verdana' size='1'>" . $cur[0] . "</font></td></tr>";
    }
    $query = "SELECT DISTINCT(Remark) FROM tuser WHERE remark NOT IN (SELECT Remark FROM DrillRemark WHERE DrillMasterID = " . $lstDrill . ") AND length(Remark) > 0  ORDER BY remark";
    for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $counterRemark++) {
        print "<tr><td><input type='checkbox' id='chkRemark" . $counterRemark . "' name='chkRemark" . $counterRemark . "' value='" . $cur[0] . "'></td><td><font face='Verdana' size='1'>" . $cur[0] . "</font></td></tr>";
    }
    print "</table></td><input type='hidden' name='txtCountRemark' value='" . $counterRemark . "'>";
    print "<td vAlign='top'><table border='1' bordercolor='#FFFFFF' cellspacing='-1'>";
    print "<tr><td><input type='checkbox' id='chkAllPhone' name='chkAllPhone' onClick='checkAll(4, document.frm2.txtCountPhone.value)'></td><td><font face='Verdana' size='2'><b>" . $_SESSION[$session_variable . "PhoneColumnName"] . "</b></font></td></tr>";
    $query = "SELECT Phone FROM DrillPhone WHERE DrillMasterID = " . $lstDrill . " ORDER BY Phone";
    for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $counterPhone++) {
        print "<tr><td><input checked type='checkbox' id='chkPhone" . $counterPhone . "' name='chkPhone" . $counterPhone . "' value='" . $cur[0] . "'></td><td><font face='Verdana' size='1'>" . $cur[0] . "</font></td></tr>";
    }
    $query = "SELECT DISTINCT(phone) FROM tuser WHERE phone NOT IN (SELECT Phone FROM DrillPhone WHERE DrillMasterID = " . $lstDrill . ") AND length(phone) > 0 ORDER BY phone";
    for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $counterPhone++) {
        print "<tr><td><input type='checkbox' id='chkPhone" . $counterPhone . "' name='chkPhone" . $counterPhone . "' value='" . $cur[0] . "'></td><td><font face='Verdana' size='1'>" . $cur[0] . "</font></td></tr>";
    }
    print "</table></td><input type='hidden' name='txtCountPhone' value='" . $counterPhone . "'>";
    print "<td vAlign='top'><table border='1' bordercolor='#FFFFFF' cellspacing='-1'>";
    print "<tr><td><input type='checkbox' id='chkAllIdNo' name='chkAllIdNo' onClick='checkAll(5, document.frm2.txtCountIdNo.value)'></td><td><font face='Verdana' size='2'><b>" . $_SESSION[$session_variable . "IDColumnName"] . "</b></font></td></tr>";
    $query = "SELECT IdNo FROM DrillIdNo WHERE DrillMasterID = " . $lstDrill . " ORDER BY IdNo";
    for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $counterIdNo++) {
        print "<tr><td><input checked type='checkbox' id='chkIdNo" . $counterIdNo . "' name='chkIdNo" . $counterIdNo . "' value='" . $cur[0] . "'></td><td><font face='Verdana' size='1'>" . $cur[0] . "</font></td></tr>";
    }
    $query = "SELECT DISTINCT(idno) FROM tuser WHERE idno NOT IN (SELECT IdNo FROM DrillIdNo WHERE DrillMasterID = " . $lstDrill . ") AND length(idno) > 0 ORDER BY idno";
    for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $counterIdNo++) {
        print "<tr><td><input type='checkbox' id='chkIdNo" . $counterIdNo . "' name='chkIdNo" . $counterIdNo . "' value='" . $cur[0] . "'></td><td><font face='Verdana' size='1'>" . $cur[0] . "</font></td></tr>";
    }
    print "</table></td><input type='hidden' name='txtCountIdNo' value='" . $counterIdNo . "'>";
    print "</tr></table>";
    if (stripos($userlevel, $current_module . "E") !== false && insertToday() < $txtDrillDate) {
        print "<p align='center'><input name='btSave' type='button' value='Save Changes' onClick='javascript:checkSubmit()'></p>";
    }
    print "</form>";
    
}
print "</div></div></div></div></div>";
include 'footer.php';
echo "<script>\r\nfunction checkSubmit(){\r\n\tx = document.frm2;\r\n\tif (confirm('Save Changes')){\r\n\t\tx.btSave.disabled = true;\r\n\t\tx.submit();\r\n\t}\r\n}\r\n\r\nfunction checkAll(a, z){\r\n\tw = \"\";\r\n\tif (a == 1){\r\n\t\tw = \"Div\";\r\n\t}else if (a == 2){\r\n\t\tw = \"Dept\";\r\n\t}else if (a == 3){\r\n\t\tw = \"Remark\";\r\n\t}else if (a == 4){\r\n\t\tw = \"Phone\";\r\n\t}else if (a == 5){\r\n\t\tw = \"IdNo\";\r\n\t}else if (a == 6){\r\n\t\tw = \"Terminal\";\r\n\t}\r\n\tx = document.frm2;\r\n\ty = document.getElementById(\"chkAll\"+w);\r\n\tfor (i=0;i<(z*1);i++){\t\t\r\n\t\tif (y.checked == true){\t\t\t\r\n\t\t\tdocument.getElementById(\"chk\"+w+i).checked = true;\t\t\t\r\n\t\t}else{\r\n\t\t\tdocument.getElementById(\"chk\"+w+i).checked = false;\r\n\t\t}\r\n\t}\r\n}\r\n</script>\r\n</center>";

?>