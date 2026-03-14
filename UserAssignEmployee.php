<?php
ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "11";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=UserAssignEmployee.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$iconn = openIConnection();
//print "<html><title>Assign User - Depts for Access Restriction</title><body><center>";
//displayHeader($prints, false, false);
//print "<center>";
//displayLinks($current_module, $userlevel);
if ($prints != "yes") {
    include 'header.php';
    ?>
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-12 d-flex no-block align-items-center">
                <h4 class="page-title">Assign User - Depts for Access Restriction</h4>
                <div class="ms-auto text-end">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Assign User
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <?php
}
//print "</center>";
$act = $_GET["act"];
if ($act == "") {
    $act = $_POST["act"];
}
$message = $_GET["message"];
if ($message == "") {
    $message = "Assign User - Employee for Access Restriction <br>Assign ALL or Assign NONE grants Permissions to ACCESS all Employees <br>Viewing Rights on UserManagement AND Editing Rights on Employee Settings enables Rights on User Employee Access";
}
$lstUser = $_POST["lstUser"];
if ($lstUser == "") {
    $lstUser = $_GET["lstUser"];
}
$lstUser = str_replace("---", "&", $lstUser);
$txtCount = $_POST["txtCount"];
if ($act == "editRecord") {
    $query = "DELETE FROM userf3 WHERE Username = '" . $lstUser . "'";
    updateIData($iconn, $query, true);
    for ($i = 0; $i < $txtCount; $i++) {
        if ($_POST["txh" . $i] != "" && $_POST["chk" . $i] != "") {
            $query = "INSERT INTO userf3 (Username, F3) VALUES ('" . $lstUser . "', '" . $_POST["txh" . $i] . "')";
            updateIData($iconn, $query, true);
        }
    }
    $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Updated User Employee Access for User: " . $lstUser . "')";
    updateIData($iconn, $query, true);
    $message = "Record Updated";
//    header("Location: UserAssignEmployee.php?message=" . $message . "&lstUser=" . str_replace("&", "---", $lstUser));
    header("Location: UserAssignEmployee.php?message=" . urlencode($message) . "&lstUser=" . str_replace("&", "---", $lstUser) . "&department=" . $selectedDept);
}
print'<div class="container-fluid">
                <div class="row">
                        <div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12">
                            <div class="card">
                                    <div class="card-body table-responsive">';
echo "<script>\r\nfunction deleteRecord(){\r\n\tif (confirm('Delete this Record')){\r\n\t\twindow.location.href='UserAssignEmployee.php?act=deleteRecord&lstUser='+document.frm1.lstUser.value;\r\n\t}\r\n}\r\n</script>\r\n";
print "<p align='center'><font face='Verdana' size='1' color='#FF0000'><b>" . $message . "</b></font></p>";
//print "<table width='800' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
//print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Select a User from the below List to edit/delete Departments for Access Restrictions</b></font></td></tr>";
print "<p align='center'><font face='Verdana' size='1'><b>Select a User from the below List to edit/delete Departments for Access Restrictions</b></font></p>";
print "<form name='frm1' method='post' action='UserAssignEmployee.php'><input type='hidden' name='act' value='editRecord'>";
//print "<input type='hidden' name='lstUser' value='" . htmlspecialchars($lstUser) . "'>";
//print "<input type='hidden' name='department' value='" . htmlspecialchars($selectedDept) . "'>";
if ($username == "virdi") {
    $query = "SELECT DISTINCT(Username), Username FROM Usermaster ORDER BY Username";
} else {
    $query = "SELECT DISTINCT(Username), Username FROM Usermaster WHERE Username NOT LIKE 'virdi' ORDER BY Username";
}
$prints = "no";
print "<div class='row'><div class='col-2'></div><div class='col-4'>";
displayList("lstUser", "User: ", $lstUser, $prints, $conn, $query, "onChange=javascript:window.location.href='UserAssignEmployee.php?lstUser='+document.frm1.lstUser.value.replace('&','---')", "25%", "75%");
//print "</tr>";
print "</div>";
print "<div class='col-4'>";
$selectedDept = isset($_POST['department']) ? $_POST['department'] : (isset($_GET['department']) ? $_GET['department'] : '');
$deptQuery = "SELECT DISTINCT(Dept) From tuser";
$deptResult = mysqli_query($conn, $deptQuery);
print "<td align='right' width='25%'><font face='Verdana' size='2'><label>Department:</label></font></td>";
print "<td width='75%'><select class='form-control form-select select2 shadow-none select2-hidden-accessible' name='department' onchange=\"location.href='UserAssignEmployee.php?lstUser=" . urlencode($lstUser) . "&department='+encodeURIComponent(this.value)\">";
print "<option value=''>---</option>";
while($deptCur = mysqli_fetch_row($deptResult)){
     $selected = ($selectedDept == $deptCur[0]) ? "selected" : "";
    print "<option value='$deptCur[0]' $selected>$deptCur[0]</option>";
}
print "</select></td>";
//print "</tr>";
print "<div class='col-2'></div></div></div></div></div></div></div>";
print "<div class='row'><div class='col-md-12'><div class='card'><div class='card-body table-responsive'>";
print "<div class='row'><div class='col-4'></div><div class='col-4'>";
if ($lstUser != "") {
    print "<tr>";
    print "<td colspan='3'>";
    print "<table width='100%' cellspacing='-1' cellpadding='-1' border='0'>";
    print "<tbody>";
    print "<tr>"
            . "<td align='right' width='25%'><input type='checkbox' name='chkAll' onClick='javascript:checkAll()'></td>"
            . "<td width='5%'><font face='Verdana' size='2'><b>ID</b></font></td>"
            . "<td width='70%'><font face='Verdana' size='2'><b>Employee</b></font></td>";
    print "</tr>";
    print "</tbody></table></td>";
    print "</tr>";
    $count = 0;
    $query = "SELECT userf3.F3, tuser.id FROM userf3,tuser  WHERE tuser.name=userf3.F3 AND userf3.Username = '" . $lstUser . "' ORDER BY userf3.F3";
    for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $count++) {
        print "<tr>";
        print "<td colspan='3'>";
        print "<table width='100%' cellspacing='-1' cellpadding='-1' border='0'>";
        print "<tbody>";
        print "<tr>";
        print "<td align='right' width='25%'><input type='checkbox' checked name='chk" . $count . "' id='chk" . $count . "'><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'></td><td width='5%'><font face='Verdana' size='1'>".$cur[1]."</font></td><td width='70%'><font face='Verdana' size='1'>" . $cur[0] . "</font></td>";
        print "</tr>";
        print "</tbody></table></td>";
        print "</tr>";
    }
    $deptFilter = ($selectedDept != "") ? "AND Dept = '" . $selectedDept . "'" : "";
    $safeUser = $lstUser;
    $safeDept = $selectedDept;

    $query = "SELECT name, id FROM tuser 
              WHERE name NOT IN (
                  SELECT F3 FROM userf3 WHERE Username = '$safeUser'
              )";

    if ($selectedDept != "") {
        $query .= " AND Dept = '$safeDept'";
    }

    $query .= " AND PassiveType='ACT' ORDER BY id";
    $empCount = 0;
    for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $count++) {
        print "<tr>";
        print "<td colspan='3'>";
        print "<table width='100%' cellspacing='-1' cellpadding='-1' border='0'>";
        print "<tbody>";
        print "<tr>";
        print "<td align='right' width='25%'><input type='checkbox' name='chk" . $count . "' id='chk" . $count . "'><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'></td><td width='5%'><font face='Verdana' size='1'>" . $cur[1] . "</font></td><td width='70%'><font face='Verdana' size='1'>" . $cur[0] . "</font></td>";
        print "</tr>";
        print "</tr>";
        print "</tbody></table></td>";
        print "</tr>";
        $empCount++;
    }
    
//    print "<center>Total Records: $empCount</center>";
    if (stripos($userlevel, $current_module . "V") !== false && stripos($userlevel, "27E") !== false) {
        print "<br><p align='center'><input type='submit' value='Save Changes' class='btn btn-primary'></p>";
    }
    print "</td></tr>";
    
}

print "<input type='hidden' name='txtCount' value='" . $count . "'></form>";
print "</table>";
if($empCount > 0){
    print "<p align='center'><font face='Verdana' size='1'>Total Unassigned Employee Record Displayed: <b>" . $empCount . "</b></font></p>";
}
print "</div><div class='col-4'></div>";
print '</div></div></div></div></div></div>';
print "<script>\r\nfunction checkAll(){\r\n\tx = document.frm1;\t\r\n\ty = x.chkAll;\r\n\tz = x.txtCount.value;\t\r\n\tfor (i=0;i<z;i++){\t\t\r\n\t\tif (y.checked == true){\t\t\t\r\n\t\t\tdocument.getElementById(\"chk\"+i).checked = true;\t\t\t\r\n\t\t}else{\r\n\t\t\tdocument.getElementById(\"chk\"+i).checked = false;\r\n\t\t}\r\n\t}\r\n}\r\n</script>";
include 'footer.php';
?>