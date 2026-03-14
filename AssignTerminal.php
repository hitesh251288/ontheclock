<?php


ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "13";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=AssignTerminal.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$iconn = openIConnection();
if ($prints != "yes") {
    include 'header.php';
?>
<div class="page-breadcrumb">
                    <div class="row">
                        <div class="col-12 d-flex no-block align-items-center">
                            <h4 class="page-title">Assign Terminals</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Assign Terminals
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

<?php
}
//print "<html><title>Assign Terminals</title><body><center>";
//displayHeader($prints,'','');
print "<center>";
//displayLinks($current_module, $userlevel);
//print "</center>";
print'<div class="container-fluid">
                <div class="row">
                        <div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12">
                            <div class="card">
                                    <div class="card-body table-responsive">';
$act = $_GET["act"];
if ($act == "") {
    $act = $_POST["act"];
}
$message = $_GET["message"];
if ($message == "") {
    $message = "Assign Terminal";
}
$lstDept = $_POST["lstDept"];
if ($lstDept == "") {
    $lstDept = $_GET["lstDept"];
}
$lstDept = str_replace("---", "&", $lstDept);
$txtCount = $_POST["txtCount"];
if ($act == "editRecord") {
    $text = "Assigned Department to Terminals: ";
    $query = "DELETE FROM DeptGate";
    updateIData($iconn, $query, true);
    for ($i = 0; $i < $txtCount; $i++) {
        if ($_POST["chk" . $i] != "" && $_POST["txhDept" . $i] != "") {
            $query = "INSERT INTO DeptGate (dept, g_id) VALUES ('" . $_POST["txhDept" . $i] . "', " . $_POST["txhGate" . $i] . ")";
            updateIData($iconn, $query, true);
            $text = $text . $_POST["txhDept" . $i] . " - " . $_POST["txhGateName" . $i] . ", ";
        }
    }
    if (1020 < strlen($text)) {
        $text = substr($text, 0, 1020);
    }
    $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
    updateIData($iconn, $query, true);
    $message = "Record Updated";
    header("Location: AssignTerminal.php?message=" . $message);
}
echo "<script>\r\nfunction deleteRecord(){\r\n\tif (confirm('Delete this Record')){\r\n\t\twindow.location.href='AssignTerminal.php?act=deleteRecord&lstDept='+document.frm1.lstDept.value;\r\n\t}\r\n}\r\n</script>\r\n";
print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b></font></p>";
print "<p><font face='Verdana' size='1'><b>Check Department(s) to be allowed to be clocked at the Terminal(s)</b></font></p>";
print "<form name='frm1' method='post' action='AssignTerminal.php'><input type='hidden' name='act' value='editRecord'>";
if ($prints != "yes") {
    print "<table width='800' bgcolor='#F0F0F0'><tr>";
} else {
    print "<table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'><tr>";
}
$counter = 0;
$sc = 0;
$query = "SELECT id, name FROM tgate ORDER BY name";
$result = mysqli_query($conn, $query);
while ($cur = mysqli_fetch_row($result)) {
    $sc = 0;
    print "<td bgcolor='#FFFFFF' align='center' vAlign='top'><font face='Verdana' size='1'><b>" . $cur[1] . "</b></font><br><table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
    print "<tr><td width='5%'><input type='checkbox' name='chkAll" . $counter . "' onClick='javascript:checkAll(" . $counter . ")'></td><td width='95%'><font face='Verdana' size='1'><b>All</b></font></td></tr>";
    $sub_query = "SELECT DeptGate.dept FROM DeptGate WHERE DeptGate.g_id = " . $cur[0] . " ORDER BY DeptGate.dept";
    for ($sub_result = mysqli_query($conn, $sub_query); $sub_cur = mysqli_fetch_row($sub_result); $sc++) {
        print "<tr><td width='5%'><input id='chk" . $counter . "' type='checkbox' name='chk" . $counter . "' checked><input type='hidden' name='txhGate" . $counter . "' value='" . $cur[0] . "'> <input type='hidden' name='txhDept" . $counter . "' value='" . $sub_cur[0] . "'> <input type='hidden' name='txhGateName" . $counter . "' value='" . $cur[1] . "'></td><td width='95%'><font face='Verdana' size='1'>" . $sub_cur[0] . "</font></td></tr>";
        $counter++;
    }
    $sub_query = "SELECT DISTINCT(dept) FROM tuser WHERE dept NOT IN (SELECT DeptGate.dept FROM DeptGate WHERE DeptGate.g_id = " . $cur[0] . ") ORDER BY dept";
    for ($sub_result = mysqli_query($conn, $sub_query); $sub_cur = mysqli_fetch_row($sub_result); $sc++) {
        print "<tr><td width='5%'><input id='chk" . $counter . "' type='checkbox' name='chk" . $counter . "'><input type='hidden' name='txhGate" . $counter . "' value='" . $cur[0] . "'> <input type='hidden' name='txhDept" . $counter . "' value='" . $sub_cur[0] . "'> <input type='hidden' name='txhGateName" . $counter . "' value='" . $cur[1] . "'></td><td width='95%'><font face='Verdana' size='1'>" . $sub_cur[0] . "</font></td></tr>";
        $counter++;
    }
    print "</table></td>";
}
print "</tr></table>";
print "<br><p align='center'><input name='btSave' type='button' class='btn btn-primary' value='Save Changes' onClick='javascript:checkSubmit()'></p>";
print "<input type='hidden' name='txtCount' value='" . $counter . "'> <input type='hidden' name='txtSCount' value='" . $sc . "'></form>";
print "</div></div></div></div></div>";
echo "<script>\r\nfunction checkSubmit(){\r\n\tx = document.frm1;\r\n\tif (confirm('Save Changes')){\r\n\t\tx.btSave.disabled = true;\r\n\t\tx.submit();\r\n\t}\r\n}\r\n\r\nfunction checkAll(a){\r\n\tx = document.frm1;\t\r\n\ty = document.getElementById(\"chkAll\"+a);\r\n\tz = x.txtSCount.value;\r\n\tfor (i=a*1;i<(z*1+a*1);i++){\t\t\r\n\t\tif (y.checked == true){\t\t\t\r\n\t\t\tdocument.getElementById(\"chk\"+i).checked = true;\t\t\t\r\n\t\t}else{\r\n\t\t\tdocument.getElementById(\"chk\"+i).checked = false;\r\n\t\t}\r\n\t}\r\n}\r\n</script>\r\n</center>";
include 'footer.php';
?>