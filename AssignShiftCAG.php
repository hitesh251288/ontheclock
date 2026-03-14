<?php


ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "35";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!(checkSession($userlevel, $current_module) && insertToday() < 20241231)) {
    header("Location: " . $config["REDIRECT"] . "?url=AssignShiftCAG.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$iconn = openIConnection();
if ($prints != "yes") {
    include 'header.php';
?>
<div class="page-breadcrumb">
                    <div class="row">
                        <div class="col-12 d-flex no-block align-items-center">
                            <h4 class="page-title">Assign Shift to Contract Access Groups</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            Assign Shift to Contract Access Groups
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

<?php
}
//print "<html><title>Assign Shift to Contract Access Groups</title><body><center>";
//displayHeader($prints, false, false);
print "<center>";
//displayLinks($current_module, $userlevel);
print'<div class="container-fluid">
                <div class="row">
                        <div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12">
                            <div class="card">
                                    <div class="card-body table-responsive">';
//print "</center>";
$act = $_POST["act"];
if ($act == "") {
    $act = $_GET["act"];
}
$message = $_GET["message"];
if ($message == "") {
    $message = "Assign Shift Contract Access Groups";
}
if ($act == "editRecord") {
    $count = $_POST["txtCount"];
    if (0 < $count) {
        $i = 0;
        while ($i < $count) {
            $query = "UPDATE tuser SET group_id = '" . $_POST["lstGroup" . $i] . "' WHERE CAGID = '" . $_POST["txtCAG" . $i] . "'";
            if (updateIData($iconn, $query, true)) {
                $query = "UPDATE FlagDayRotation, tuser SET FlagDayRotation.group_id = '" . $_POST["lstGroup" . $i] . "' WHERE FlagDayRotation.e_id = tuser.id AND FlagDayRotation.e_date = '" . insertToday() . "' AND FlagDayRotation.RecStat = 0 AND tuser.CAGID = '" . $_POST["txtCAG" . $i] . "'";
                if (updateIData($iconn, $query, true)) {
                    $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Assigned Shift ID " . $_POST["lstGroup" . $i] . " to Access Group ID " . $_POST["txtCAG" . $i] . "')";
                    if (updateIData($iconn, $query, true)) {
                        $i++;
                    } else {
                        echo $query;
                        exit;
                    }
                } else {
                    echo $query;
                    exit;
                }
            } else {
                echo $query;
                exit;
            }
        }
        header("Location: AssignShiftCAG.php?message=Shifts assigned to Employees in respective Access Groups");
    }
    $query = "";
}

?>
<div class="card">
    <div class="card-body">
        <?php 
        echo "\r\n";
        print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b></font></p>";
        print "<form name='frm2' method='post' action='AssignShiftCAG.php'><input type='hidden' name='act' value='editRecord'>";
        print "<tr><td bgcolor='#FFFFFF' colspan='2'><img height='2' width='100%' src='img/orange-bar.gif'/></td></tr>";
        ?>
        <div class="row">
            <div class="col-5"></div>
            <div class="col-2">
                <?php
                $count = 0;
                $query = "SELECT CAGID, Name from CAG ORDER BY Name";
                for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $count++) {
                    print "<input type='hidden' name='txtCAG" . $count . "' value='" . $cur[0] . "'>";
                    $query = "SELECT group_id FROM tuser WHERE CAGID = '" . $cur[0] . "'";
                    $sub_result = selectData($iconn, $query);
                    $query = "SELECT id, name from tgroup ORDER BY id";
                    displayList("lstGroup" . $count, $cur[1] . ": ", $sub_result[0], $prints, $iconn, $query, "", "", "");
                }
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-5"></div>
            <div class="col-2">
                <?php 
                displayTextbox("txtDate", "Effective Date: ", displayToday(), "yes", "", "", "");
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <?php 
                if (stripos($userlevel, $current_module . "E") !== false) {
                    print "<input type='button' class='btn btn-primary' value='Save Changes' onClick='javascript:checkSubmit()'>";
                }
                print "<input type='hidden' name='txtCount' value='" . $count . "'>";
                ?>
            </div>
        </div>
        </form>
</div>
<?php

print "</div></div></div></div>";
echo "<script>\r\n\tfunction checkSubmit(){\r\n\t\tif (confirm('Assign Shifts to respective Access Groups')){\r\n\t\t\tdocument.frm2.submit();\r\n\t\t}\r\n\t}\r\n</script>\r\n</center>";
print "</div></div></div></div></div>";
include 'footer.php';
?>