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
    header("Location: " . $config["REDIRECT"] . "?url=MealMaster.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$iconn = openIConnection();
print "<html><title>Meal Slots</title><body>";
print "<link rel=\"stylesheet\" href=\"default.css\" type=\"text/css\" />";
print "<style>input{background-color:'#F0F0F0';font-family:'Verdana';font-size:'10';}</style>";
print "<style>select{background-color:'#FFFFFF';font-family:'Verdana';font-size:'10';}</style>";
$act = $_GET["act"];
if ($act == "") {
    $act = $_POST["act"];
}
$message = $_GET["message"];
if ($message == "") {
    $message = "Meal Slots";
}
$txtGroupAdd = $_POST["txtGroupAdd"];
$txtGroup = $_POST["txtGroup"];
$lstMeal = $_POST["lstMeal"];
if ($lstMeal == "") {
    $lstMeal = $_GET["lstMeal"];
}
$txtMealSlot = $_POST["txtMealSlot"];
if ($txtMealSlot == "") {
    $txtMealSlot = $_GET["txtMealSlot"];
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
    $query = "DELETE FROM MealMaster WHERE MealMasterID = " . $lstMeal;
    updateIData($iconn, $query, true);
    $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Deleted Meal ID: " . $lstMeal . "')";
    updateIData($iconn, $query, true);
    $message = "Record Deleted";
    header("Location: MealMaster.php?message=" . $message);
} else {
    if ($act == "addRecord") {
        $query = "SELECT MealMasterID FROM MealMaster WHERE MealTimeFrom = '" . $txtTimeFrom . "' AND MealTimeTo = '" . $txtTimeTo . "'";
        $result = selectData($conn, $query);
        if ($result[0] == "") {
            $query = "INSERT INTO MealMaster (MealSlot, MealTimeFrom, MealTimeTo) VALUES ('" . $txtMealSlot . "', '" . $txtTimeFrom . "', '" . $txtTimeTo . "')";
            updateIData($iconn, $query, true);
            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Created Meal Slot: " . $txtMealSlot . ", Time From: " . $txtTimeFrom . ", Time To: " . $txtTimeTo . "')";
            updateIData($iconn, $query, true);
            $message = "Record Added";
            header("Location: MealMaster.php?message=" . $message);
        } else {
            $message = "Meal for the entered Time ALREADY exists. Kindly use the Editing Mode";
        }
    } else {
        if ($act == "changeMealSlot") {
            $query = "UPDATE MealMaster SET MealSlot = '" . $txtMealSlot . "', MealTimeFrom = '" . $txtTimeFrom . "', MealTimeTo = '" . $txtTimeTo . "'  WHERE MealMaster.MealMasterID = '" . $lstMeal . "'";
            updateIData($iconn, $query, true);
            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Edited Meal - Slot: " . $txtMealSlot . ", Time From: " . $txtTimeFrom . ", Time To: " . $txtTimeTo . "')";
            updateIData($iconn, $query, true);
            $message = "Record Updated";
            header("Location: MealMaster.php?lstMeal=" . $lstMeal . "&message=" . $message);
        }
    }
}
echo "<script>\r\nfunction deleteRecord(){\r\n\tif (confirm('Delete this Record')){\r\n\t\twindow.location.href='MealMaster.php?act=deleteRecord&lstMeal='+document.frm1.lstMeal.value;\r\n\t}\r\n}\r\n</script>\r\n";
$txtDrillDate = "";
if ($prints != "yes") {
    print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b></font></p>";
    print "<table width='800' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
    if ($lstMeal == "" && stripos($userlevel, $current_module . "A") !== false) {
        print "<tr><td bgcolor='#F0F0F0' colspan='2'>&nbsp;</td></tr>";
        print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Add a new record</b></font></td></tr>";
        print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='MealMaster.php'><input type='hidden' name='txtTo' value='01/01/2090'> <input type='hidden' name='act' value='addRecord'>";
        print "<tr>";
        displayTextbox("txtMealSlot", "Meal Slot:", $txtMealSlot, $prints, "30", "20%", "80%");
        print "</tr>";
        print "<tr>";
        displayTextbox("txtTimeFrom", "Time From: <font size='1'>(HHMMSS)</font>", $txtTimeFrom, $prints, "8", "20%", "80%");
        print "</tr>";
        print "<tr>";
        displayTextbox("txtTimeTo", "Time To: <font size='1'>(HHMMSS)</font>", $txtTimeTo, $prints, "8", "20%", "80%");
        print "</tr>";
        print "<tr><td>&nbsp;</td><td><input type='submit' value='Submit' name='btSearch'></td></tr>";
        print "<tr><td bgcolor='#FFFFFF' colspan='2'><img height='2' width='100%' src='img/orange-bar.gif'/></td></tr>";
        print "<tr><td bgcolor='#F0F0F0' colspan='2'>&nbsp;</td></tr>";
        print "</form>";
    }
    print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Select a record from list to edit/delete</b></font></td></tr>";
    if ($lstMeal != "") {
        print "<form name='frm1' method='post' action='MealMaster.php' onSubmit='return checkSearch()'> <input type='hidden' name='txtTo' value='01/01/2090'> <input type='hidden' name='act' value='changeMealSlot'>";
    } else {
        print "<form name='frm2' method='post'>";
    }
    print "<tr>";
    $query = "SELECT MealMasterID, CONCAT( MealSlot, ': ', SUBSTR(MealTimeFrom, 1, 4), ' - ', SUBSTR(MealTimeTo, 1, 4) ) FROM MealMaster ORDER BY MealSlot";
    if ($lstMeal != "") {
        displayList("lstMeal", "Meal: ", $lstMeal, $prints, $conn, $query, "onChange=javascript:window.location.href='MealMaster.php?lstMeal='+document.frm1.lstMeal.value", "20%", "80%");
    } else {
        displayList("lstMeal", "Meal: ", $lstMeal, $prints, $conn, $query, "onChange=javascript:window.location.href='MealMaster.php?lstMeal='+document.frm2.lstMeal.value", "20%", "80%");
    }
    print "</tr>";
    if ($lstMeal != "") {
        $query = "SELECT MealMasterID, MealSlot, MealTimeFrom, MealTimeTo FROM MealMaster WHERE MealMasterID = " . $lstMeal;
        $result = selectData($conn, $query);
        $txtDrillDate = $result[1];
        print "<tr>";
        displayTextbox("txtMealSlot", "Slot:", $result[1], $prints, "12", "20%", "80%");
        print "</tr>";
        print "<tr>";
        displayTextbox("txtTimeFrom", "Time From: <font size='1'>(HHMMSS)</font>", $result[2], $prints, "8", "20%", "80%");
        print "</tr>";
        print "<tr>";
        displayTextbox("txtTimeTo", "Time To: <font size='1'>(HHMMSS)</font>", $result[3], $prints, "8", "20%", "80%");
        print "</tr><tr><td>&nbsp;</td><td>";
        if (stripos($userlevel, $current_module . "E") !== false) {
            print "<input type='submit' value='Save Changes'>";
        }
        if (stripos($userlevel, $current_module . "D") !== false) {
            print "&nbsp;&nbsp;<input type='button' value='Delete Record' onClick='javascript:deleteRecord()'>";
        }
        print "&nbsp;</td></tr>";
    }
    print "</form>";
}
echo "<script>\r\nfunction checkSubmit(){\r\n\tx = document.frm2;\r\n\tif (confirm('Save Changes')){\r\n\t\tx.btSave.disabled = true;\r\n\t\tx.submit();\r\n\t}\r\n}\r\n\r\nfunction checkAll(a, z){\r\n\tw = \"\";\r\n\tif (a == 1){\r\n\t\tw = \"Div\";\r\n\t}else if (a == 2){\r\n\t\tw = \"Dept\";\r\n\t}else if (a == 3){\r\n\t\tw = \"Remark\";\r\n\t}else if (a == 4){\r\n\t\tw = \"Phone\";\r\n\t}else if (a == 5){\r\n\t\tw = \"IdNo\";\r\n\t}else if (a == 6){\r\n\t\tw = \"Terminal\";\r\n\t}\r\n\tx = document.frm2;\r\n\ty = document.getElementById(\"chkAll\"+w);\r\n\tfor (i=0;i<(z*1);i++){\t\t\r\n\t\tif (y.checked == true){\t\t\t\r\n\t\t\tdocument.getElementById(\"chk\"+w+i).checked = true;\t\t\t\r\n\t\t}else{\r\n\t\t\tdocument.getElementById(\"chk\"+w+i).checked = false;\r\n\t\t}\r\n\t}\r\n}\r\n</script>\r\n</center></body></html>";

?>