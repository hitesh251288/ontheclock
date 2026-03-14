<?php


ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$conn = openConnection();
$con = openConnection();
$act = $_GET["act"];
echo $act;
if ($act == "print") {
    print "<body onLoad='javascript:window.print()'>";
    print "<table width='240' height='150' border='1' background='card/bg/Blocks.jpg'>";
    print "<tr><td width='100%'><img src='card/emp/1.jpg' align='left' height='125'>Billy Joe <br>Seasoning <br>Admin <br>01/01/2009 <br><img align='right' vAlign='bottom' src='card/logo/logo.gif' height='30'></td></tr>";
    print "</table>";
    print "</body>";
    $my_img = imagecreate(240, 150);
} else {
    if ($act == "bin") {
        $query = "SELECT c_gid, c_data from tcommand";
        $result = mysqli_query($query);
        while ($row = mysqli_fetch_array($result)) {
            echo "<br><br>" . $row["c_gid"] . " - ";
            $fileContent = $row["c_data"];
            header("Content-type: text/plain");
            echo $fileContent;
        }
    } else {
        print "<body>";
        print "<script>function openWindow(){window.open('RepairDisplay.php?act=print', '','height=150;width=240');}</script>";
        print "<input type='button' onClick='javascript:openWindow()' value='Click to Print'>";
        print "</body>";
    }
}

?>