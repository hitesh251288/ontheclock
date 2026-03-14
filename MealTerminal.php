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
    header("Location: " . $config["REDIRECT"] . "?url=MealTerminal.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
if ($prints != "yes") {
    include 'header.php';
?>
<div class="page-breadcrumb">
                    <div class="row">
                        <div class="col-12 d-flex no-block align-items-center">
                            <h4 class="page-title">Meal Terminals</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Meal Terminals
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

<?php
}
//print "<html><title>Meal Terminals</title><body><center>";
//displayHeader($prints, true, true);
print "<center>";
//displayLinks($current_module, $userlevel);
print'<div class="container-fluid">
                <div class="row">
                        <div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12">
                            <div class="card">
                                    <div class="card-body table-responsive">';
//print "</center>";
$act = $_GET["act"];
$message = $_GET["message"];
if ($message == "") {
    $message = "Meal Terminal Details";
}
$lstTerminal = $_POST["lstTerminal"];
if ($lstTerminal == "") {
    $lstTerminal = $_GET["lstTerminal"];
}
$txtTerminal = $_POST["txtTerminal"];
$lstTerminalAdd = $_POST["lstTerminalAdd"];
if ($act == "deleteRecord") {
    $query = "UPDATE tgate SET tgate.Meal = 0, tgate.Exit = 0 where id = " . $lstTerminal;
    updateDataTransact($conn, $query, true, $username);
    $message = "Record Deleted";
    header("Location: MealTerminal.php?message=" . $message);
} else {
    if ($txtTerminal != "") {
        $query = "UPDATE tgate SET name = '" . replaceString($txtTerminal, true) . "' WHERE id = " . $lstTerminal;
        updateDataTransact($conn, $query, true, $username);
        header("Location: MealTerminal.php?message=Terminal updated");
    } else {
        if ($lstTerminalAdd != "") {
            $query = "UPDATE tgate SET tgate.Meal = 1, tgate.Exit = 1 where id = " . $lstTerminalAdd;
            updateDataTransact($conn, $query, true, $username);
            header("Location: MealTerminal.php?message=Terminal added");
        }
    }
}
echo "<script>\r\nfunction deleteRecord(){\r\n\tif (confirm('Delete this Record')){\r\n\t\twindow.location.href='MealTerminal.php?act=deleteRecord&lstTerminal='+document.frm1.lstTerminal.value;\r\n\t}\r\n}\r\n</script>\r\n";
print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b></font></p>";
print "<table width='800' cellpadding='1' cellspacing='-1'>";
print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Select a Terminal from the below List to edit/delete <br>(Deleting the Terminal removes it from the 'Meal Terminals' list</b></font></td></tr>";
print "<form name='frm1' method='post' action='MealTerminal.php'><tr>";
$query = "SELECT id, name FROM tgate WHERE tgate.Meal = 1 ORDER BY name";
$prints = "no";
displayList("lstTerminal", "Meal Terminals: ", $lstTerminal, $prints, $conn, $query, "onChange=javascript:window.location.href='MealTerminal.php?lstTerminal='+document.frm1.lstTerminal.value", "20%", "80%");
print "</tr>";
if ($lstTerminal != "") {
    $query = "SELECT name FROM tgate WHERE id = " . $lstTerminal;
    $result = selectData($conn, $query);
    print "<tr>";
    displayTextbox("txtTerminal", "Change Name To: ", $result[0], $prints, "30", "20%", "80%");
    print "</tr>";
    if (stripos($userlevel, $current_module . "E") !== false) {
        print "<tr><td>&nbsp;</td><td><input type='submit' value='Save Changes'>";
    }
    if (stripos($userlevel, $current_module . "D") !== false) {
        print "&nbsp;&nbsp;<input type='button' value='Delete Record' onClick='javascript:deleteRecord()'>";
    }
    print "</td></tr>";
}
print "</form>";
if (stripos($userlevel, $current_module . "A") !== false) {
    print "<tr><td bgcolor='#FFFFFF' colspan='2'><img height='2' width='100%' src='img/orange-bar.gif'/></td></tr>";
    print "<tr><td bgcolor='#F0F0F0' colspan='2'>&nbsp;</td></tr>";
    print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Select a Terminal from the below List and click 'Submit Record' to add it to the 'Meal Terminals' List</b></font></td></tr>";
    print "<form name='frm2' method='post' action='MealTerminal.php'><tr>";
    $query = "SELECT id, name FROM tgate WHERE tgate.Meal = 0 AND tgate.Exit = 0 AND name NOT LIKE '' ORDER BY name";
    $prints = "no";
    displayList("lstTerminalAdd", "Department Terminals: ", $lstTerminalAdd, $prints, $conn, $query, "onClick='javascript:addRecord()'", "20%", "80%");
    print "</tr>";
    print "<tr><td>&nbsp;</td><td><input name='bt2' type='submit' class='btn btn-primary' value='Submit Record' disabled></td></tr>";
    print "</form>";
}
print "</div></div></div></div></div>";
echo "</table>\r\n\t<iframe align='center' width=\"815\" height=\"400\" src=\"MealMaster.php\" SCROLLING=\"no\" FRAMEBORDER=\"0\" border=0></iframe>\r\n<script>\r\nfunction addRecord(){\r\n\tx = document.frm2;\r\n\tif (x.lstTerminalAdd.value == ''){\r\n\t\tx.bt2.disabled = true;\r\n\t}else{\r\n\t\tx.bt2.disabled = false;\r\n\t}\r\n}\r\n</script>\r\n</center>";
include 'footer.php';

?>