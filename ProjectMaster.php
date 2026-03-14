<?php


ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "16";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=ProjectMaster.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$iconn = openIConnection();
print "<html><title>Project Details</title><body><center>";
displayHeader($prints);
print "<center>";
if ($prints != "yes") {
    displayLinks($current_module, $userlevel);
}
print "</center>";
$act = $_GET["act"];
$message = $_GET["message"];
if ($message == "") {
    $message = "Project Details";
}
$lstProject = $_POST["lstProject"];
if ($lstProject == "") {
    $lstProject = $_GET["lstProject"];
}
$txtProject = $_POST["txtProject"];
$txtCode = $_POST["txtCode"];
$txtProjectAdd = $_POST["txtProjectAdd"];
$lstManager = $_POST["lstManager"];
$txtCodeAdd = $_POST["txtCodeAdd"];
$designationQuery = "SELECT * FROM tuser where name='$username'";
$designationResult = mysqli_query($conn, $designationQuery);
$designationRow = mysqli_fetch_assoc($designationResult);
$dept = $designationRow['dept'];

if ($act == "deleteRecord") {
    $query = "SELECT ProjectID FROM ProjectLog WHERE ProjectID = " . $lstProject;
    $result = selectData($conn, $query);
    if ($result[0] != "") {
        $message = "Record cannot be Deleted as it is associated with one or more Transactions";
    } else {
        $query = "DELETE FROM ProjectMaster WHERE ProjectID = " . $lstProject;
        updateIData($iconn, $query, true);
        $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Deleted Project ID: " . $lstProject . "')";
        updateIData($iconn, $query, true);
        $message = "Record Deleted";
    }
    header("Location: " . $PHP_SELF . "?message=" . $message);
} else {
    if ($txtProject != "") {
        $query = "SELECT ProjectID FROM ProjectLog WHERE ProjectID = " . $lstProject;
        $result = selectData($conn, $query);
        if ($result[0] != "") {
            $message = "Record cannot be Edited as it is associated with one or more Transactions";
        } else {
            $query = "UPDATE ProjectMaster SET ProjectMaster.Code = '" . replaceString($txtCode, true) . "', ProjectMaster.Name = '" . replaceString($txtProject, true) . "', ProjectMaster.Manager = '" . replaceString($lstManager, true) . "' WHERE ProjectID = " . $lstProject;
            updateIData($iconn, $query, true);
            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Updated Project ID: " . $lstProject . " - Code = " . replaceString($txtCode, true) . ", Name = " . replaceString($txtProject, true) . "')";
            updateIData($iconn, $query, true);
        }
        header("Location: " . $PHP_SELF . "?message=Project updated");
    } else {
        if ($txtProjectAdd != "") {
            $query = "INSERT INTO ProjectMaster (ProjectID, ProjectMaster.Code, ProjectMaster.Name, ProjectMaster.Manager) VALUES (" . (getMax($conn, "ProjectMaster", "ProjectID") + 1) . ", '" . replaceString($txtCodeAdd, true) . "', '" . replaceString($txtProjectAdd, true) . "', '" . replaceString($lstManager, true) . "')";
            updateIData($iconn, $query, true);
            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Added Project ID: [NEW] - Code = " . replaceString($txtCodeAdd, true) . ", Name = " . replaceString($txtProjectAdd, true) . "')";
            updateIData($iconn, $query, true);
            header("Location: " . $PHP_SELF . "?message=Project added");
        }
    }
}
echo "<script>\r\nfunction deleteRecord(){\r\n\tif (confirm('Delete this Record')){\r\n\t\twindow.location.href='ProjectMaster.php?act=deleteRecord&lstProject='+document.frm1.lstProject.value;\r\n\t}\r\n}\r\n</script>\r\n";
print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b></font></p>";
print "<table width='800' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Select a record from list to edit/delete</b></font></td></tr>";
print "<form name='frm1' method='post' action='" . $PHP_SELF . "'><tr>";
if($designationRow['F8'] == 'Admin'){
    $query = "SELECT ProjectID, CONCAT(ProjectMaster.Code, ': ', ProjectMaster.Name) FROM ProjectMaster ORDER BY ProjectMaster.Code, ProjectMaster.Name";
}else{
    $query = "SELECT ProjectMaster.ProjectID, CONCAT(ProjectMaster.Code, ': ', ProjectMaster.Name) FROM ProjectMaster, tuser where tuser.id=ProjectMaster.Manager AND tuser.dept='$dept' ORDER BY ProjectMaster.Code, ProjectMaster.Name";
}
$prints = "no";
displayList("lstProject", "Project/Location Master: ", $lstProject, $prints, $conn, $query, "onChange=javascript:window.location.href='" . $PHP_SELF . "?lstProject='+document.frm1.lstProject.value", "20%", "80%");
print "</tr>";

if ($lstProject != "") {
    $query = "SELECT ProjectMaster.Code, ProjectMaster.Name, ProjectMaster.Manager FROM ProjectMaster WHERE ProjectID = " . $lstProject;
    $result = selectData($conn, $query);
//    $resultData = updateIData($iconn, $queryUser, true);
    if($designationRow['F8'] == 'Admin'){
        $queryUser = "SELECT id, name FROM tuser WHERE F8='PM'";
    }else{
        $queryUser = "SELECT id, name FROM tuser WHERE F8='PM' AND dept='$dept'";
    }
    $userData = mysqli_query($conn, $queryUser);
    print "<tr>";
    displayTextbox("txtCode", "Change Code To: ", $result[0], $prints, "30", "20%", "80%");
    print "</tr>";
    print "<tr>";
    displayTextbox("txtProject", "Change Name To: ", $result[1], $prints, "30", "20%", "80%");
    print "</tr>";
    print "<tr>";
    print "<td width='20%' align='right'>Manager Name To:</td><td width='80%'>";
    print "<select name='lstManager' class='form-control'>";
    print "<option></option>";
    while($row = mysqli_fetch_array($userData)){
        $selected = (isset($result[2]) && $result[2] == $row[0]) ? 'selected' : '';
        print "<option value=".$row[0]." $selected>".$row[1]."</option>";
    }
    print "</select>";
    print "</td>";
//    displayList("lstManager", "Manager Name: ", $result[0], $prints, $conn, $queryUser, "", "20%", "80%");
    print "</tr>";
    if (stripos($userlevel, $current_module . "E") !== false) {
        print "<tr><td>&nbsp;</td><td><input type='submit' value='Save Changes'>";
    }
    if (stripos($userlevel, $current_module . "D") !== false) {
        print "&nbsp;&nbsp;<input type='button' value='Delete Record' onClick='javascript:deleteRecord()'>";
    }
    print "</td></tr>";
}?>
<div id="loader">
    <img src="img/loader.gif" >
</div>
<?php 
print "</form>";
if (stripos($userlevel, $current_module . "A") !== false) {
    if($designationRow['F8'] == 'Admin'){
        $queryUser = "SELECT id, name FROM tuser WHERE F8='PM'";
    }else{
        $queryUser = "SELECT id, name FROM tuser WHERE F8='PM' AND dept='$dept'";
    }
//    $result = selectData($conn, $query);
//    $prints = "no";
    print "<tr><td bgcolor='#FFFFFF' colspan='2'><img height='2' width='100%' src='img/orange-bar.gif'/></td></tr>";
    print "<tr><td bgcolor='#F0F0F0' colspan='2'>&nbsp;</td></tr>";
    print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Add a new record</b></font></td></tr>";
    print "<form name='frm2' method='post' action='" . $PHP_SELF . "'>";
    print "<tr>";
    displayTextbox("txtCodeAdd", "Project Code: ", $txtProjectCode, $prints, "30", "20%", "80%");
    print "</tr>";
    print "<tr>";
    displayTextbox("txtProjectAdd", "Project Name: ", $txtProjectAdd, $prints, "30", "20%", "80%");
    print "</tr>";
    print "<tr>";
    displayList("lstManager", "Manager Name: ", $lstManager, $prints, $conn, $queryUser, "", "20%", "80%");
    print "</tr>";
    print "<tr><td>&nbsp;</td><td><input type='submit' value='Submit'></td></tr>";
    print "</form>";
}
echo "</table>\r\n</center></body></html>";
?>
<script src="resource/js/jquery.min.js"></script> 
<script>
    $(document).ready(function() {
        $('#loader').hide();
        $('select[name="lstProject"]').change(function() {
            $('#loader').show();
        });
    });
</script>