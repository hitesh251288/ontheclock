<?php
ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "35";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=ContractAccess.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$iconn = openIConnection();
if ($prints != "yes") {
    include 'header.php';
    ?>
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-12 d-flex no-block align-items-center">
                <h4 class="page-title">Contract Access Groups</h4>
                <div class="ms-auto text-end">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Contract Access Groups
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <?php
}
//print "<html><title>Contract Access Groups</title>";
print "<body onLoad=javascript:checkType()>";
print'<div class="container-fluid">
                <div class="row">
                        <div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12">
                            <div class="card">
                                    <div class="card-body table-responsive">';
//displayHeader($prints, false, false);
//displayLinks($current_module, $userlevel);
//print "</center>";
$act = $_POST["act"];
if ($act == "") {
    $act = $_GET["act"];
}
$message = $_GET["message"];
if ($message == "") {
    $message = "Contract Access Groups";
}
$lstContractAccess = $_POST["lstContractAccess"];
if ($lstContractAccess == "") {
    $lstContractAccess = $_GET["lstContractAccess"];
}
$txtID = $_POST["txtID"];
if ($txtID == "") {
    $txtID = $_GET["txtID"];
}
$txtName = $_POST["txtName"];
$lstType = $_POST["lstType"];
$txtDay = $_POST["txtDay"];
$txtDayFrom = $_POST["txtDayFrom"];
$txtDayTo = $_POST["txtDayTo"];
if ($act == "deleteRecord") {
    $txtID = $txtID / 1024;
    $query = "SELECT COUNT(CAGID) FROM tuser WHERE CAG = " . $txtID;
    $result = selectData($conn, $query);
    if (0 < $cur[0]) {
        $message = "Record cannot be Deleted as it is associated with one or more Employees";
    } else {
        $query = "DELETE FROM CAG WHERE CAGID = " . $txtID;
        if (updateIData($iconn, $query, true)) {
            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Deleted Contract Access Group ID: " . $txtID . "')";
            if (updateIData($iconn, $query, true)) {
                $message = "Record Deleted";
            }
        }
    }
    header("Location: " . $PHP_SELF . "?message=" . $message);
} else {
    if ($act == "editRecord") {
        $query = "";
        if ($lstType == "Day") {
            $query = "UPDATE CAG SET Name = '" . replaceString($txtName, false) . "', CAGType = 'Day', Days = '" . $txtDay . "', DateFrom = 0, DateTo = 0 ";
            $text = "Updated Contract Access Group ID " . $txtID . " - Name = " . replaceString($txtName, false) . ", CAGType = Day, Days = " . $txtDay . ", DateFrom = 0, DateTo = 0";
        } else {
            if ($lstType == "Date") {
                $query = "UPDATE CAG SET Name = '" . replaceString($txtName, false) . "', CAGType = 'Date', Days = 0, DateFrom = " . $txtDayFrom . ", DateTo = " . $txtDayTo;
                $text = "Updated Contract Access Group ID " . $txtID . " - Name = " . replaceString($txtName, false) . ", CAGType = Date, Days = 0, DateFrom = " . $txtDayFrom . ", DateTo = " . $txtDayTo;
            }
        }
        $query = $query . " WHERE CAGID = " . $txtID;
        if (updateIData($iconn, $query, true)) {
            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
            if (updateIData($iconn, $query, true)) {
                header("Location: ContractAccess.php?lstContractAccess=" . $txtID . "&message=Access Group updated");
            } else {
                header("Location: ContractAccess.php?lstContractAccess=" . $txtID . "&message=Access Group COULD NOT be updated");
            }
        } else {
            header("Location: ContractAccess.php?lstContractAccess=" . $txtID . "&message=Access Group COULD NOT be updated");
        }
    } else {
        if ($act == "addRecord") {
            $query = "";
            if ($lstType == "Day") {
                $query = "INSERT INTO CAG (CAGDate, Name, CAGType, Days) VALUES ('" . insertToday() . "', '" . replaceString($txtName, false) . "', '" . $lstType . "', '" . $txtDay . "')";
                $text = "Created Contract Access Group (Name, CAGType, Days) VALUES (" . replaceString($txtName, false) . ", " . $lstType . ", " . $txtDay . ")";
            } else {
                if ($lstType == "Date") {
                    $query = "INSERT INTO CAG (CAGDate, Name, CAGType, DateFrom, DateTo) VALUES ('" . insertToday() . "', '" . replaceString($txtName, false) . "', '" . $lstType . "', '" . $txtDayFrom . "', '" . $txtDayTo . "')";
                    $text = "Created Contract Access Group (Name, CAGType, DateFrom, DateTo) VALUES (" . replaceString($txtName, false) . ", " . $lstType . ", " . $txtDayFrom . ", " . $txtDayTo . ")";
                }
            }
            if (updateIData($iconn, $query, true)) {
                $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                if (updateIData($iconn, $query, true)) {
                    header("Location: ContractAccess.php?message=Contract Access Group added");
                } else {
                    header("Location: ContractAccess.php?message=Contract Access Group COULD NOT be added");
                }
            } else {
                header("Location: ContractAccess.php?message=Contract Access Group COULD NOT be added");
            }
        }
    }
}
echo "<script>\r\nfunction deleteRecord(){\r\n\tif (confirm('Delete this Record')){\r\n\t\twindow.location.href='ContractAccess.php?act=deleteRecord&txtID='+(document.frm2.txtID.value*1024);\r\n\t}\r\n}\r\n\r\nfunction checkType(){\r\n\tx = document.frm2;\r\n\ta = x.lstType.value;\r\n\r\n\tif (a == 'Day'){\r\n\t\tx.txtDay.disabled = false;\r\n\t\tx.txtDayFrom.disabled = true;\r\n\t\tx.txtDayTo.disabled = true;\r\n\t}else if (a == 'Date'){\r\n\t\tx.txtDay.disabled = true;\r\n\t\tx.txtDayFrom.disabled = false;\r\n\t\tx.txtDayTo.disabled = false;\r\n\t}\r\n}\r\n\r\nfunction checkSubmit(c){\r\n\tx = document.frm2;\r\n\ta = x.lstType.value;\r\n\tb = x.txtDay.value;\r\n\tc = x.txtDayFrom.value;\r\n\td = x.txtDayTo.value;\r\n\te = x.txtID.value;\r\n\t\r\n\tif (x.txtName.value == ''){\r\n\t\talert('Please enter the Group Name');\r\n\t\tx.txtName.focus();\r\n\t}else if (a == ''){\r\n\t\talert('Please select the Access Type');\r\n\t\tx.lstSchedule.focus();\r\n\t}else if (a == 'Day' && (b > 31 || b <= 0)){\r\n\t\talert('Please enter the correct Number of Access Days');\r\n\t\tx.txtDay.focus();\r\n\t}else if (a == 'Date' && (c > 31 || c <= 0 || d > 31 || d <= 0)){\r\n\t\talert('Please enter the correct Access From and Date Days');\r\n\t\tx.txtDayFrom.focus();\t\r\n\t}else{\r\n\t\tif (e == 0){\r\n\t\t\tx.act.value = 'addRecord';\r\n\t\t}else{\r\n\t\t\tx.act.value = 'editRecord';\r\n\t\t}\r\n\t\tx.submit();\r\n\t}\r\n}\r\n\r\nfunction changeScheduleType(){\r\n\tx = document.frm2;\r\n\tx.act.value = 'changeScheduleType';\r\n\tx.submit();\r\n}\r\n\r\nfunction checkTime(a){\r\n\t//alert(a);\r\n\tif (a.length != 4){\r\n\t\treturn false;\r\n\t}else if (a*1 != a/1){\r\n\t\treturn false;\r\n\t}else if (a.substring(0, 2)*1 > 24){\r\n\t\treturn false;\r\n\t}else if (a.substring(2, 4)*1 > 59){\r\n\t\treturn false;\r\n\t}\r\n}\r\n\r\n</script>\r\n";
?>
<div class="card">
    <div class="card-body">
        <?php
        print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b></font></p>";
        print "<center><h4>Select a record from list to edit/delete</h4></center>";
        print "<form name='frm1' method='post' action='ContractAccess.php'>";
        ?>
        <div class="row">
            <div class="col-5"></div>
            <div class="col-2">
                <?php
                $query = "SELECT CAGID, Name FROM CAG WHERE CAGID > 0 ORDER BY Name";
                $prints = "no";
                displayList("lstContractAccess", "Access Group Name: ", $lstContractAccess, $prints, $conn, $query, "onChange=javascript:window.location.href='ContractAccess.php?lstContractAccess='+document.frm1.lstContractAccess.value", "", "");
                ?>
            </div>
        </div>
        <?php
        print "</form>";
        if ($lstContractAccess != "") {
            $query = "SELECT CAGID, CAGDate, Name, CAGType, Days, DateFrom, DateTo FROM CAG WHERE CAGID = " . $lstContractAccess;
            $result = selectData($conn, $query);
            $txtID = $result[0];
            $txtName = $result[2];
            $lstType = $result[3];
            $txtDay = $result[4];
            $txtDayFrom = $result[5];
            $txtDayTo = $result[6];
        }
        ?>
        <div class="row">
            <div class="col-12">
                <?php
                print "<center><img height='2' width='100%' src='img/orange-bar.gif'/></center>";
                print "<center>";
                if ($lstContractAccess != "") {
                    print "<label class='form-label'><b>Edit a record</b></label>";
                } else {
                    print "<label class='form-label'><b>Add a new record</b></label>";
                }
                print "</center>";
                ?>
            </div>
        </div>
        <?php
            print "<form name='frm2' method='post' action='ContractAccess.php'><input type='hidden' name='act'> <input type='hidden' name='txtID' value='" . $txtID . "'> ";
            ?>
        <div class="row">
            <div class="col-2">
                <?php
                displayTextbox("txtName", "Access Group Name: ", $txtName, $prints, "", "", "");
                ?>
            </div>
            <div class="col-2">
                <?php
                print "<label class='form-label'>Access Type:</label><select size='1' class='select2 form-select shadow-none' name='lstType' onChange=javascript:checkType()>";
                print "<option value='" . $lstType . "' selected>" . $lstType . "</option>";
                print "<option value='Day'>Day</option>";
                print "<option value='Date'>Date</option>";
                print "</select>";
                ?>
            </div>
            <div class="col-2">
                <?php
                displayTextbox("txtDay", "No of Days allowed in a Month: ", $txtDay, $prints, "", "", "");
                ?>
            </div>
            <div class="col-2">
                <?php
                displayTextbox("txtDayFrom", "Day From: ", $txtDayFrom, $prints, "", "", "");
                ?>
            </div>
            <div class="col-2">
                <?php
                displayTextbox("txtDayTo", "Day To: ", $txtDayTo, $prints, "", "", "");
                ?>
            </div>
            <div class="row">
                <div class="col-12">
                    <?php
                    if (stripos($userlevel, $current_module . "A") !== false && $lstContractAccess == "") {
                        print "<center>";
                        print "<br><input type='button' class='btn btn-primary' value='Submit Record' onClick='checkSubmit(0)'>";
                        print "</center>";
                    } else {
                        if (stripos($userlevel, $current_module . "E") !== false && $lstContractAccess != "") {
                            print "<center>";
                            $query = "SELECT group_id FROM tuser WHERE group_id = " . $txtID;
                            print "<br><input type='button' class='btn btn-primary' value='Save Changes' onClick='checkSubmit(1)'>";
                            if (stripos($userlevel, $current_module . "D") !== false) {
                                print "&nbsp;&nbsp;<input type='button' class='btn btn-primary' value='Delete Record' onClick='deleteRecord()'>";
                            }
                            print "</center>";
                        }
                    }
                    ?>
                </div>
            </div>
            </form>
        </div>
    </div>
    <?php
    print "</div></div></div></div></div>";
    include 'footer.php';
    ?>