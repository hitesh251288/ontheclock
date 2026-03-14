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
    header("Location: " . $config["REDIRECT"] . "?url=AssignUserDept.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$iconn = openIConnection();
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
                                            Assign User - Depts for Access Restriction
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

<?php
}
//print "<html><title>Assign User - Depts for Access Restriction</title><body><center>";
//displayHeader($prints, false, false);

print "<center>";
//displayLinks($current_module, $userlevel);
print'<div class="container-fluid">
                <div class="row">
                        <div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12">
                            <div class="card">
                                    <div class="card-body table-responsive">';
//print "</center>";
$act = $_GET["act"];
if ($act == "") {
    $act = $_POST["act"];
}
$message = $_GET["message"];
if ($message == "") {
    $message = "Assign User - Depts for Access Restriction <br>Assign ALL or Assign NONE grants Permissions to ACCESS all Department Employees <br>Viewing Rights on UserManagement AND Editing Rights on Employee Settings enables Rights on User Department/ Division Access";
}
$lstUser = $_POST["lstUser"];
if ($lstUser == "") {
    $lstUser = $_GET["lstUser"];
}
$lstUser = str_replace("---", "&", $lstUser);
$txtCount = $_POST["txtCount"];
if ($act == "editRecord") {
    $query = "DELETE FROM UserDept WHERE Username = '" . $lstUser . "'";
    updateIData($iconn, $query, true);
    for ($i = 0; $i < $txtCount; $i++) {
        if ($_POST["txh" . $i] != "" && $_POST["chk" . $i] != "") {
            $query = "INSERT INTO UserDept (Username, Dept) VALUES ('" . $lstUser . "', '" . $_POST["txh" . $i] . "')";
            updateIData($iconn, $query, true);
        }
    }
    $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Updated User Department Access for User: " . $lstUser . "')";
    updateIData($iconn, $query, true);
    $message = "Record Updated";
    header("Location: AssignUserDept.php?message=" . $message . "&lstUser=" . str_replace("&", "---", $lstUser));
}
echo "<script>\r\nfunction deleteRecord(){\r\n\tif (confirm('Delete this Record')){\r\n\t\twindow.location.href='AssignUserDept.php?act=deleteRecord&lstUser='+document.frm1.lstUser.value;\r\n\t}\r\n}\r\n</script>\r\n";
print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b></font></p>";
print "<table width='800' cellpadding='1' cellspacing='-1'>";
print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Select a User from the below List to edit/delete Departments for Access Restrictions</b></font></td></tr>";
print "<form name='frm1' method='post' action='AssignUserDept.php'><input type='hidden' name='act' value='editRecord'><tr>";
if ($username == "virdi") {
    $query = "SELECT DISTINCT(Username), Username FROM Usermaster ORDER BY Username";
} else {
    $query = "SELECT DISTINCT(Username), Username FROM Usermaster WHERE Username NOT LIKE 'virdi' ORDER BY Username";
}
$prints = "no";
print "<div class='row'>";
print "<div class='col-5'></div>";
print "<div class='col-2'>";
displayList("lstUser", "User: ", $lstUser, $prints, $conn, $query, "onChange=javascript:window.location.href='AssignUserDept.php?lstUser='+document.frm1.lstUser.value.replace('&','---')", "20%", "80%");
print "</div>";
print "</div>";
if ($lstUser != "") {
    print "<tr>";
    print "<td align='right'><input type='checkbox' name='chkAll' onClick='javascript:checkAll()'></td><td><font face='Verdana' size='2'><b>Departments</b></font><td></td>";
    print "</tr>";
    $count = 0;
    $query = "SELECT UserDept.Dept FROM UserDept WHERE UserDept.Username = '" . $lstUser . "' ORDER BY UserDept.Dept";
    for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $count++) {
        print "<tr>";
        print "<td align='right'><input type='checkbox' checked name='chk" . $count . "' id='chk" . $count . "'><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'></td><td><font face='Verdana' size='1'>" . $cur[0] . "</font><td></td>";
        print "</tr>";
    }
    $query = "SELECT DISTINCT(dept) FROM tuser WHERE dept NOT IN (SELECT Dept FROM UserDept WHERE Username = '" . $lstUser . "') AND dept NOT LIKE '' ORDER BY dept";
    for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $count++) {
        print "<tr>";
        print "<td align='right'><input type='checkbox' name='chk" . $count . "' id='chk" . $count . "'><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'></td><td><font face='Verdana' size='1'>" . $cur[0] . "</font><td></td>";
        print "</tr>";
    }
    if (stripos($userlevel, $current_module . "V") !== false && stripos($userlevel, "27E") !== false) {
        print "<tr><td>&nbsp;</td><td><input type='submit' class='btn btn-primary' value='Save Changes'>";
    }
    print "</td></tr>";
}
print "<input type='hidden' name='txtCount' value='" . $count . "'></form>";
print "</div></div></div></div>";
echo "</table>\r\n<script>\r\nfunction checkAll(){\r\n\tx = document.frm1;\t\r\n\ty = x.chkAll;\r\n\tz = x.txtCount.value;\t\r\n\tfor (i=0;i<z;i++){\t\t\r\n\t\tif (y.checked == true){\t\t\t\r\n\t\t\tdocument.getElementById(\"chk\"+i).checked = true;\t\t\t\r\n\t\t}else{\r\n\t\t\tdocument.getElementById(\"chk\"+i).checked = false;\r\n\t\t}\r\n\t}\r\n}\r\n</script>\r\n</center>";
include 'footer.php';
?>