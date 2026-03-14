<?php


ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "13";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!(checkSession($userlevel, $current_module) && $_SESSION[$session_variable . "VirdiLevel"] == "Classic")) {
    header("Location: " . $config["REDIRECT"] . "?url=GroupMaster.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$iconn = openIConnection();
if ($prints != "yes") {
    include 'header.php';
?>
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-12 d-flex no-block align-items-center">
            <h4 class="page-title">Group Settings</h4>
            <div class="ms-auto text-end">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Group Settings
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
<?php
}

//print "<html><title>Group Settings</title><body><center>";
//displayHeader($prints, false, false);
print "<center>";
if ($prints != "yes") {
//    print "<body>";
    print'<div class="container-fluid">
                <div class="row">
                        <div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12">
                            <div class="card">
                                    <div class="card-body table-responsive">';
}
//displayLinks($current_module, $userlevel);

//print "</center>";
$act = $_GET["act"];
if ($act == "") {
    $act = $_POST["act"];
}
$message = $_GET["message"];
if ($message == "") {
    $message = "Employee Group Settings for Reports";
}
$txtGroupAdd = $_POST["txtGroupAdd"];
$txtGroup = $_POST["txtGroup"];
$lstGroup = $_POST["lstGroup"];
if ($lstGroup == "") {
    $lstGroup = $_GET["lstGroup"];
}
$txtCountDiv = $_POST["txtCountDiv"];
$txtCountDept = $_POST["txtCountDept"];
$txtCountRemark = $_POST["txtCountRemark"];
$txtCountPhone = $_POST["txtCountPhone"];
$txtCountIdNo = $_POST["txtCountIdNo"];
$txtCountShift = $_POST["txtCountShift"];
if ($act == "deleteRecord") {
    $query = "DELETE FROM GroupMaster WHERE GroupID = " . $lstGroup;
    if (updateIData($iconn, $query, true)) {
        $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Deleted Report Group ID: " . $lstGroup . "')";
        updateIData($iconn, $query, true);
        $message = "Record Deleted";
    } else {
        $message = "Record could NOT be Deleted";
    }
    header("Location: GroupMaster.php?message=" . $message);
} else {
    if ($act == "changeGroupName") {
        $query = "UPDATE GroupMaster SET GroupMaster.Name = '" . replaceString($txtGroup, true) . "' WHERE GroupID = " . $lstGroup;
        if (updateIData($iconn, $query, true)) {
            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Updated Project ID: " . $lstGroup . " - SET Name = " . replaceString($txtGroup, true) . "')";
            updateIData($iconn, $query, true);
            $message = "Group Updated&lstGroup=" . $lstGroup;
        } else {
            $message = "Group could NOT be Updated&lstGroup=" . $lstGroup;
        }
        header("Location: GroupMaster.php?message=" . $message);
    } else {
        if ($txtGroupAdd != "") {
            $query = "INSERT INTO GroupMaster (Name) VALUES ('" . replaceString($txtGroupAdd, true) . "')";
            if (updateIData($iconn, $query, true)) {
                $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Added Report Group: " . replaceString($txtGroupAdd, true) . "')";
                updateIData($iconn, $query, true);
                $message = "Group added";
            } else {
                $message = "Group could NOT be added";
            }
            header("Location: GroupMaster.php?message=" . $message);
        } else {
            if ($act == "editRecord") {
                $text = "Assigned Division to Report Group: " . $lstGroup . ": ";
                $query = "DELETE FROM GroupDiv WHERE GroupID = " . $lstGroup;
                updateIData($iconn, $query, true);
                for ($i = 0; $i < $txtCountDiv; $i++) {
                    if ($_POST["chkDiv" . $i] != "") {
                        $query = "INSERT INTO GroupDiv (GroupID, GroupDiv.Div) VALUES (" . $lstGroup . ", '" . $_POST["chkDiv" . $i] . "')";
                        updateIData($iconn, $query, true);
                        $text = $text . $_POST["chkDiv" . $i] . ", ";
                    }
                }
                if (1020 < strlen($text)) {
                    $text = substr($text, 0, 1020);
                }
                $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                updateIData($iconn, $query, true);
                $text = "Assigned Department to Report Group: " . $lstGroup . ": ";
                $query = "DELETE FROM GroupDept WHERE GroupID = " . $lstGroup;
                updateIData($iconn, $query, true);
                for ($i = 0; $i < $txtCountDept; $i++) {
                    if ($_POST["chkDept" . $i] != "") {
                        $query = "INSERT INTO GroupDept (GroupID, Dept) VALUES (" . $lstGroup . ", '" . $_POST["chkDept" . $i] . "')";
                        updateIData($iconn, $query, true);
                        $text = $text . $_POST["chkDept" . $i] . ", ";
                    }
                }
                if (1020 < strlen($text)) {
                    $text = substr($text, 0, 1020);
                }
                $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                updateIData($iconn, $query, true);
                $text = "Assigned Remark to Report Group: " . $lstGroup . ": ";
                $query = "DELETE FROM GroupRemark WHERE GroupID = " . $lstGroup;
                updateIData($iconn, $query, true);
                for ($i = 0; $i < $txtCountRemark; $i++) {
                    if ($_POST["chkRemark" . $i] != "") {
                        $query = "INSERT INTO GroupRemark (GroupID, Remark) VALUES (" . $lstGroup . ", '" . $_POST["chkRemark" . $i] . "')";
                        updateIData($iconn, $query, true);
                        $text = $text . $_POST["chkRemark" . $i] . ", ";
                    }
                }
                if (1020 < strlen($text)) {
                    $text = substr($text, 0, 1020);
                }
                $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                updateIData($iconn, $query, true);
                $text = "Assigned " . $_SESSION[$session_variable . "PhoneColumnName"] . " to Report Group: " . $lstGroup . ": ";
                $query = "DELETE FROM GroupPhone WHERE GroupID = " . $lstGroup;
                updateIData($iconn, $query, true);
                for ($i = 0; $i < $txtCountPhone; $i++) {
                    if ($_POST["chkPhone" . $i] != "") {
                        $query = "INSERT INTO GroupPhone (GroupID, Phone) VALUES (" . $lstGroup . ", '" . $_POST["chkPhone" . $i] . "')";
                        updateIData($iconn, $query, true);
                        $text = $text . $_POST["chkPhone" . $i] . ", ";
                    }
                }
                if (1020 < strlen($text)) {
                    $text = substr($text, 0, 1020);
                }
                $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                updateIData($iconn, $query, true);
                $text = "Assigned " . $_SESSION[$session_variable . "IDColumnName"] . " to Report Group: " . $lstGroup . ": ";
                $query = "DELETE FROM GroupIdNo WHERE GroupID = " . $lstGroup;
                updateIData($iconn, $query, true);
                for ($i = 0; $i < $txtCountIdNo; $i++) {
                    if ($_POST["chkIdNo" . $i] != "") {
                        $query = "INSERT INTO GroupIdNo (GroupID, IdNo) VALUES (" . $lstGroup . ", '" . $_POST["chkIdNo" . $i] . "')";
                        updateIData($iconn, $query, true);
                        $text = $text . $_POST["chkIdNo" . $i] . ", ";
                    }
                }
                if (1020 < strlen($text)) {
                    $text = substr($text, 0, 1020);
                }
                $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                updateIData($iconn, $query, true);
                $text = "Assigned Shift to Report Group: " . $lstGroup . ": ";
                $query = "DELETE FROM GroupShift WHERE GroupID = " . $lstGroup;
                updateIData($iconn, $query, true);
                for ($i = 0; $i < $txtCountShift; $i++) {
                    if ($_POST["chkShift" . $i] != "") {
                        $query = "INSERT INTO GroupShift (GroupID, Shift) VALUES (" . $lstGroup . ", '" . $_POST["chkShift" . $i] . "')";
                        updateIData($iconn, $query, true);
                        $text = $text . $_POST["txhShift" . $i] . ", ";
                    }
                }
                if (1020 < strlen($text)) {
                    $text = substr($text, 0, 1020);
                }
                $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                updateIData($iconn, $query, true);
                $message = "Record Updated";
                header("Location: GroupMaster.php?lstGroup=" . $lstGroup . "&message=" . $message);
            }
        }
    }
}
echo "<script>\r\nfunction deleteRecord(){\r\n\tif (confirm('Delete this Record')){\r\n\t\twindow.location.href='GroupMaster.php?act=deleteRecord&lstGroup='+document.frm1.lstGroup.value;\r\n\t}\r\n}\r\n</script>\r\n";
if ($prints != "yes") {
?>
<div class="card">
    <div class="card-body">
        <?php 
            print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b></font></p>";
            if (stripos($userlevel, $current_module . "A") !== false) {
                print "<form name='frm3' method='post' action='GroupMaster.php'>";
                print "<div class='row'>";
                print "<div class='col-5'></div>";
                print "<div class='col-2'>";
                print "<label class='form-label'><b>Add a new record</b></label>";
                displayTextbox("txtGroupAdd", "Group Name: ", $txtGroupAdd, $prints, "30", "20%", "80%");
                print "</div>";
                print "</div>";
                print "<div class='row'>";
                print "<div class='col-12'>";
                print "<center><br><input type='submit' class='btn btn-primary' value='Submit'></center>";
                print "<img height='2' width='100%' src='img/orange-bar.gif'/>";
                print "</div>";
                print "</div>";
                print "</form>";
            }
                ?>
        <div class="row">
            <div class="col-5"></div>
            <div class="col-2">
                <?php 
                print "<form name='frm1' method='post' action='GroupMaster.php?act=changeGroupName'><tr>";
                $query = "SELECT GroupID, Name FROM GroupMaster ORDER BY Name";
                $prints = "no";
                displayList("lstGroup", "Group: ", $lstGroup, $prints, $conn, $query, "onChange=javascript:window.location.href='GroupMaster.php?lstGroup='+document.frm1.lstGroup.value", "", "");
                ?>
            </div>
        </div>
                <?php 
                if ($lstGroup != "") {
                    $query = "SELECT GroupID, Name FROM GroupMaster WHERE GroupID = " . $lstGroup;
                    $result = selectData($conn, $query);
                    print "<div class='row'>";
                    print "<div class='col-5'></div>";
                    print "<div class='col-2'>";
                    displayTextbox("txtGroup", "Change Name To: ", $result[1], $prints, "30", "20%", "80%");
                    print "</div>";
                    print "</div>";
                    print "<div class='row'>";
                    print "<div class='col-12'>";
                    if (stripos($userlevel, $current_module . "E") !== false) {
                        print "<br><input type='submit' class='btn btn-primary' value='Change Name'>";
                    }
                    if (stripos($userlevel, $current_module . "D") !== false) {
                        print "&nbsp;&nbsp;<input type='button' class='btn btn-primary' value='Delete Record' onClick='javascript:deleteRecord()'>";
                    }
                    print "</div>";
                    print "</div>";
                }
                ?>
    </div>
</div>
<?php
 } 
print "</div></div></div></div>";
if ($lstGroup != "") {
    print "<form name='frm2' method='post' action='GroupMaster.php'><input type='hidden' name='act' value='editRecord'><input type='hidden' name='lstGroup' value='" . $lstGroup . "'>";
    if ($prints != "yes") {
        print "<table width='800' bgcolor='#F0F0F0'>";
    } else {
        print "<table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
    }
    print "<tr><td bgcolor='#FFFFFF' align='center' width='100%' colspan='5'><font face='Verdana' size='1'><b>Check Data to be Assigned to this Group</b></font></td></tr>";
    $counterDiv = 0;
    $counterDept = 0;
    $counterRemark = 0;
    $counterPhone = 0;
    $counterIdNo = 0;
    $counterShift = 0;
    print "<tr><td vAlign='top'><table border='1' bordercolor='#FFFFFF' cellspacing='-1'>";
    print "<tr><td><input type='checkbox' id='chkAllDiv' name='chkAllDiv' onClick='checkAll(1, document.frm2.txtCountDiv.value)'></td><td><font face='Verdana' size='2'><b>Division</b></font></td></tr>";
    $query = "SELECT GroupDiv.Div FROM GroupDiv WHERE GroupID = " . $lstGroup . " ORDER BY GroupDiv.Div";
    for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $counterDiv++) {
        print "<tr><td><input checked type='checkbox' id='chkDiv" . $counterDiv . "' name='chkDiv" . $counterDiv . "' value='" . $cur[0] . "'></td><td><font face='Verdana' size='1'>" . $cur[0] . "</font></td></tr>";
    }
    $query = "SELECT DISTINCT(company) FROM tuser WHERE company NOT IN (SELECT GroupDiv.Div FROM GroupDiv WHERE GroupID = " . $lstGroup . ") AND length(company) > 0 ORDER BY company";
    for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $counterDiv++) {
        print "<tr><td><input type='checkbox' id='chkDiv" . $counterDiv . "' name='chkDiv" . $counterDiv . "' value='" . $cur[0] . "'></td><td><font face='Verdana' size='1'>" . $cur[0] . "</font></td></tr>";
    }
    print "</table></td><input type='hidden' name='txtCountDiv' value='" . $counterDiv . "'>";
    print "<td vAlign='top'><table border='1' bordercolor='#FFFFFF' cellspacing='-1'>";
    print "<tr><td><input type='checkbox' id='chkAllDept' name='chkAllDept' onClick='checkAll(2, document.frm2.txtCountDept.value)'></td><td><font face='Verdana' size='2'><b>Dept</b></font></td></tr>";
    $query = "SELECT Dept FROM GroupDept WHERE GroupID = " . $lstGroup . " ORDER BY Dept";
    for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $counterDept++) {
        print "<tr><td><input checked type='checkbox' id='chkDept" . $counterDept . "' name='chkDept" . $counterDept . "' value='" . $cur[0] . "'></td><td><font face='Verdana' size='1'>" . $cur[0] . "</font></td></tr>";
    }
    $query = "SELECT DISTINCT(dept) FROM tuser WHERE dept NOT IN (SELECT dept FROM GroupDept WHERE GroupID = " . $lstGroup . ") ORDER BY dept";
    for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $counterDept++) {
        print "<tr><td><input type='checkbox' id='chkDept" . $counterDept . "' name='chkDept" . $counterDept . "' value='" . $cur[0] . "'></td><td><font face='Verdana' size='1'>" . $cur[0] . "</font></td></tr>";
    }
    print "</table></td><input type='hidden' name='txtCountDept' value='" . $counterDept . "'>";
    print "<td vAlign='top'><table border='1' bordercolor='#FFFFFF' cellspacing='-1'>";
    print "<tr><td><input type='checkbox' id='chkAllRemark' name='chkAllRemark' onClick='checkAll(3, document.frm2.txtCountRemark.value)'></td><td><font face='Verdana' size='2'><b>Remark</b></font></td></tr>";
    $query = "SELECT Remark FROM GroupRemark WHERE GroupID = " . $lstGroup . " ORDER BY Remark";
    for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $counterRemark++) {
        print "<tr><td><input checked type='checkbox' id='chkRemark" . $counterRemark . "' name='chkRemark" . $counterRemark . "' value='" . $cur[0] . "'></td><td><font face='Verdana' size='1'>" . $cur[0] . "</font></td></tr>";
    }
    $query = "SELECT DISTINCT(Remark) FROM tuser WHERE remark NOT IN (SELECT Remark FROM GroupRemark WHERE GroupID = " . $lstGroup . ") ORDER BY remark";
    for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $counterRemark++) {
        print "<tr><td><input type='checkbox' id='chkRemark" . $counterRemark . "' name='chkRemark" . $counterRemark . "' value='" . $cur[0] . "'></td><td><font face='Verdana' size='1'>" . $cur[0] . "</font></td></tr>";
    }
    print "</table></td><input type='hidden' name='txtCountRemark' value='" . $counterRemark . "'>";
    print "<td vAlign='top'><table border='1' bordercolor='#FFFFFF' cellspacing='-1'>";
    print "<tr><td><input type='checkbox' id='chkAllPhone' name='chkAllPhone' onClick='checkAll(4, document.frm2.txtCountPhone.value)'></td><td><font face='Verdana' size='2'><b>" . $_SESSION[$session_variable . "PhoneColumnName"] . "</b></font></td></tr>";
    $query = "SELECT Phone FROM GroupPhone WHERE GroupID = " . $lstGroup . " ORDER BY Phone";
    for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $counterPhone++) {
        print "<tr><td><input checked type='checkbox' id='chkPhone" . $counterPhone . "' name='chkPhone" . $counterPhone . "' value='" . $cur[0] . "'></td><td><font face='Verdana' size='1'>" . $cur[0] . "</font></td></tr>";
    }
    $query = "SELECT DISTINCT(phone) FROM tuser WHERE phone NOT IN (SELECT Phone FROM GroupPhone WHERE GroupID = " . $lstGroup . ") ORDER BY phone";
    for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $counterPhone++) {
        print "<tr><td><input type='checkbox' id='chkPhone" . $counterPhone . "' name='chkPhone" . $counterPhone . "' value='" . $cur[0] . "'></td><td><font face='Verdana' size='1'>" . $cur[0] . "</font></td></tr>";
    }
    print "</table></td><input type='hidden' name='txtCountPhone' value='" . $counterPhone . "'>";
    print "<td vAlign='top'><table border='1' bordercolor='#FFFFFF' cellspacing='-1'>";
    print "<tr><td><input type='checkbox' id='chkAllIdNo' name='chkAllIdNo' onClick='checkAll(5, document.frm2.txtCountIdNo.value)'></td><td><font face='Verdana' size='2'><b>" . $_SESSION[$session_variable . "IDColumnName"] . "</b></font></td></tr>";
    $query = "SELECT IdNo FROM GroupIdNo WHERE GroupID = " . $lstGroup . " ORDER BY IdNo";
    for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $counterIdNo++) {
        print "<tr><td><input checked type='checkbox' id='chkIdNo" . $counterIdNo . "' name='chkIdNo" . $counterIdNo . "' value='" . $cur[0] . "'></td><td><font face='Verdana' size='1'>" . $cur[0] . "</font></td></tr>";
    }
    $query = "SELECT DISTINCT(idno) FROM tuser WHERE idno NOT IN (SELECT IdNo FROM GroupIdNo WHERE GroupID = " . $lstGroup . ") ORDER BY idno";
    for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $counterIdNo++) {
        print "<tr><td><input type='checkbox' id='chkIdNo" . $counterIdNo . "' name='chkIdNo" . $counterIdNo . "' value='" . $cur[0] . "'></td><td><font face='Verdana' size='1'>" . $cur[0] . "</font></td></tr>";
    }
    print "</table></td><input type='hidden' name='txtCountIdNo' value='" . $counterIdNo . "'>";
    print "<td vAlign='top'><table border='1' bordercolor='#FFFFFF' cellspacing='-1'>";
    print "<tr><td><input type='checkbox' id='chkAllShift' name='chkAllShift' onClick='checkAll(6, document.frm2.txtCountShift.value)'></td><td><font face='Verdana' size='2'><b>Shift</b></font></td></tr>";
    $query = "SELECT Shift, name FROM GroupShift, tgroup WHERE GroupID = " . $lstGroup . " AND Shift=id ORDER BY name";
    for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $counterShift++) {
        print "<tr><td><input checked type='checkbox' id='chkShift" . $counterShift . "' name='chkShift" . $counterShift . "' value='" . $cur[0] . "'> <input type='hidden' name='txhShift" . $counterShift . "' value='" . $cur[1] . "'></td><td><font face='Verdana' size='1'>" . $cur[1] . "</font></td></tr>";
    }
    $query = "SELECT DISTINCT(id), name FROM tgroup WHERE id NOT IN (SELECT Shift FROM GroupShift WHERE GroupID = " . $lstGroup . ") ORDER BY name";
    for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $counterShift++) {
        print "<tr><td><input type='checkbox' id='chkShift" . $counterShift . "' name='chkShift" . $counterShift . "' value='" . $cur[0] . "'> <input type='hidden' name='txhShift" . $counterShift . "' value='" . $cur[1] . "'> </td><td><font face='Verdana' size='1'>" . $cur[1] . "</font></td></tr>";
    }
    print "</table></td><input type='hidden' name='txtCountShift' value='" . $counterShift . "'>";
    print "</tr></table>";
    if (stripos($userlevel, $current_module . "E") !== false) {
        print "<p align='center'><br><input name='btSave' type='button' class='btn btn-primary' value='Save Changes' onClick='javascript:checkSubmit()'></p>";
    }
    print "</div></div></div></div>";
    print "</form>";
}
print "</div></div></div></div></div></center>";
echo "<script>\r\nfunction checkSubmit(){\r\n\tx = document.frm2;\r\n\tif (confirm('Save Changes')){\r\n\t\tx.btSave.disabled = true;\r\n\t\tx.submit();\r\n\t}\r\n}\r\n\r\nfunction checkAll(a, z){\r\n\tw = \"\";\r\n\tif (a == 1){\r\n\t\tw = \"Div\";\r\n\t}else if (a == 2){\r\n\t\tw = \"Dept\";\r\n\t}else if (a == 3){\r\n\t\tw = \"Remark\";\r\n\t}else if (a == 4){\r\n\t\tw = \"Phone\";\r\n\t}else if (a == 5){\r\n\t\tw = \"IdNo\";\r\n\t}else if (a == 6){\r\n\t\tw = \"Shift\";\r\n\t}\t\r\n\tx = document.frm2;\r\n\ty = document.getElementById(\"chkAll\"+w);\t\r\n\tfor (i=0;i<(z*1);i++){\t\t\r\n\t\tif (y.checked == true){\t\t\t\r\n\t\t\tdocument.getElementById(\"chk\"+w+i).checked = true;\r\n\t\t}else{\r\n\t\t\tdocument.getElementById(\"chk\"+w+i).checked = false;\r\n\t\t}\r\n\t}\r\n}\r\n</script>";
include 'footer.php';
?>