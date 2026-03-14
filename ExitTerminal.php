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
    header("Location: " . $config["REDIRECT"] . "?url=ExitTerminal.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$iconn = openIConnection();
print "<html><title>Exit Terminals</title><body><center>";
displayHeader($prints, true, true);
print "<center>";
displayLinks($current_module, $userlevel);
print "</center>";
$act = $_GET["act"];
$message = $_GET["message"];
if ($message == "") {
    $message = "Exit Terminal Details";
}
$lstTerminal = $_POST["lstTerminal"];
if ($lstTerminal == "") {
    $lstTerminal = $_GET["lstTerminal"];
}
$txtTerminal = $_POST["txtTerminal"];
$lstTerminalAdd = $_POST["lstTerminalAdd"];
if ($act == "deleteRecord") {
    $query = "UPDATE tgate SET tgate.exit = 0 where id = " . $lstTerminal;
    updateIData($iconn, $query, true);
    $message = "Record Deleted";
    header("Location: ExitTerminal.php?message=" . $message);
} else {
    if ($txtTerminal != "") {
        $query = "UPDATE tgate SET name = '" . replaceString($txtTerminal, true) . "' WHERE id = " . $lstTerminal;
        updateIData($iconn, $query, true);
        header("Location: ExitTerminal.php?message=Terminal updated");
    } else {
        if ($lstTerminalAdd != "") {
            $query = "UPDATE tgate SET tgate.exit = 1 where id = " . $lstTerminalAdd;
            updateIData($iconn, $query, true);
            header("Location: ExitTerminal.php?message=Terminal added");
        }
    }
}
echo "<script>\r\nfunction deleteRecord(){\r\n\tif (confirm('Delete this Record')){\r\n\t\twindow.location.href='ExitTerminal.php?act=deleteRecord&lstTerminal='+document.frm1.lstTerminal.value;\r\n\t}\r\n}\r\n</script>\r\n";
print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b></font></p>";
print "<table width='800' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Select a Terminal from the below List to edit/delete <br>(Deleting the Terminal removes it from the 'Exit Terminals' list</b></font></td></tr>";
print "<form name='frm1' method='post' action='ExitTerminal.php'><tr>";
$query = "SELECT id, name FROM tgate WHERE tgate.exit = 1 ORDER BY name";
$prints = "no";
displayList("lstTerminal", "Exit Terminals: ", $lstTerminal, $prints, $conn, $query, "onChange=javascript:window.location.href='ExitTerminal.php?lstTerminal='+document.frm1.lstTerminal.value", "20%", "80%");
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
    print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Select a Terminal from the below List and click 'Submit Record' to add it to the 'Exit Terminals' List</b></font></td></tr>";
    print "<form name='frm2' method='post' action='ExitTerminal.php'><tr>";
    $query = "SELECT id, name FROM tgate WHERE tgate.exit = 0 AND name NOT LIKE '' ORDER BY name";
    $prints = "no";
    displayList("lstTerminalAdd", "Department Terminals: ", $lstTerminalAdd, $prints, $conn, $query, "onClick='javascript:addRecord()'", "20%", "80%");
    print "</tr>";
    print "<tr><td>&nbsp;</td><td><input name='bt2' type='submit' value='Submit Record' disabled></td></tr>";
    print "</form>";
}
echo "</table>\r\n<script>\r\nfunction addRecord(){\r\n\tx = document.frm2;\r\n\tif (x.lstTerminalAdd.value == ''){\r\n\t\tx.bt2.disabled = true;\r\n\t}else{\r\n\t\tx.bt2.disabled = false;\r\n\t}\r\n}\r\n</script>\r\n</center></body></html>";

?>