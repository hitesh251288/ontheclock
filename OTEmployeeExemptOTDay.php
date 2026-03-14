<?php
ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "28";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!(checkSession($userlevel, $current_module) && $_SESSION[$session_variable . "VirdiLevel"] == "Classic")) {
    header("Location: " . $config["REDIRECT"] . "?url=OTEmployeeExemptOTDay.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$iconn = openIConnection();
$act = $_GET["act"];
if ($act == "") {
    $act = $_POST["act"];
}
$prints = $_GET["prints"];
$excel = $_GET["excel"];
$message = $_GET["message"];
if ($message == "") {
    $message = "Special OT Days for OT Exempted Employees";
}
$lstShift = $_POST["lstShift"];
$lstDepartment = $_POST["lstDepartment"];
$lstDivision = $_POST["lstDivision"];
$lstEmployeeIDFrom = $_POST["lstEmployeeIDFrom"];
$lstEmployeeIDTo = $_POST["lstEmployeeIDTo"];
$txtEmployeeCode = $_POST["txtEmployeeCode"];
$txtEmployee = $_POST["txtEmployee"];
$lstSort = $_POST["lstSort"];
$txtFrom = $_POST["txtFrom"];
$txtTo = $_POST["txtTo"];
if ($txtFrom == "") {
    $txtFrom = displayToday();
}
if ($txtTo == "") {
    $txtTo = displayToday();
}
$txtRemark = $_POST["txtRemark"];
$txtPhone = $_POST["txtPhone"];
$txtSNo = $_POST["txtSNo"];
$lstEmployeeStatus = "";
if (isset($_POST["lstEmployeeStatus"])) {
    $lstEmployeeStatus = $_POST["lstEmployeeStatus"];
} else {
    $lstEmployeeStatus = "ACT";
}
$lstExempted = "Yes";
$lstOT1 = $_POST["lstOT1"];
$lstOT2 = $_POST["lstOT2"];
$lstOTDay1 = $_POST["lstOTDay1"];
$lstOTDay2 = $_POST["lstOTDay2"];
$lstRotateShift = $_POST["lstRotateShift"];
$lstSelectedDepartment = $_POST["lstSelectedDepartment"];
$lstSelectedDivision = $_POST["lstSelectedDivision"];
$txtSelectedRemark = $_POST["txtSelectedRemark"];
$txtSelectedSNo = $_POST["txtSelectedSNo"];
$txtF1 = $_POST["txtF1"];
$txtF2 = $_POST["txtF2"];
$txtF3 = $_POST["txtF3"];
$txtF4 = $_POST["txtF4"];
$txtF5 = $_POST["txtF5"];
$txtF6 = $_POST["txtF6"];
$txtF7 = $_POST["txtF7"];
$txtF8 = $_POST["txtF8"];
$txtF9 = $_POST["txtF9"];
$txtF10 = $_POST["txtF10"];
$rotate_date = "";
if ($act == "saveChanges") {
    for ($i = 0; $i < $_POST["txtCount"]; $i++) {
        if ($_POST["chk" . $i] != "") {
            if ($lstRotateShift == "Yes") {
                $rotate_date = insertToday();
            } else {
                $rotate_date = "99999999";
            }
            $query = "UPDATE tuser SET OT1 = '" . $lstOTDay1 . "', OT2 = '" . $lstOTDay2 . "', OTRotate = '" . $lstRotateShift . "', OTRotateDate = '" . $rotate_date . "' WHERE id = '" . $_POST["txhID" . $i] . "'";
            updateIData($iconn, $query, true);
            $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Set OT Day for OT Exempted Employee ID: " . $_POST["txhID" . $i] . ", OT1 = " . $lstOTDay1 . ", OT2 = " . $lstOTDay2 . ", Rotate = " . $lstRotateShift . "')";
            updateIData($iconn, $query, true);
        }
    }
    $act = "searchRecord";
}
if ($prints != "yes") {
    include 'header.php';
    ?>
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-12 d-flex no-block align-items-center">
                <h4 class="page-title">OT Days/Dates Exempted Employees</h4>
                <div class="ms-auto text-end">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">
                                OT Days/Dates Exempted Employees
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <?php
}
print "<form name='frm1' id='frm1' method='post' onSubmit='return checkSearch()' action='OTEmployeeExemptOTDay.php'><input type='hidden' name='act' value='searchRecord'>";
//print "<html><title>OT Days/Dates Exempted Employees</title>";
if ($prints != "yes") {
//    print "<body>";
    print'<div class="container-fluid">
                <div class="row">
                        <div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12">
                            <div class="card">
                                    <div class="card-body table-responsive">';
} else {
    if ($excel != "yes") {
        print "<body onLoad='javascript:window.print()'>";
    } else {
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=OTEmployeeExemptOTDay.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        print "<body>";
    }
}

if ($excel != "yes") {
    ?>
    <div class="card">
        <div class="card-body">
            <?php
            print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b></font></p>";
            if ($prints != "yes") {
                print "<center><h4 class='card-title'>Select ONE or MORE options and click Search Record</h4></center>";
//            print "<table width='800' cellpadding='1' cellspacing='-1'>";
                //        print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Select ONE or MORE options and click 'Search Record'</b></font></td></tr>";
            } else {
//            print "<table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
//            print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Selected Options</b></font></td></tr>";
            }
            ?>
            <div class="row">
                <div class="col-3">
                    <?php
                    $query = "SELECT id, name from tgroup ORDER BY name";
                    displayList("lstShift", "Current Shift: ", $lstShift, $prints, $conn, $query, "", "", "");
                    ?>
                </div>
                <?php
                displaySearchFields($conn, $prints, $session_variable, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $lstEmployeeName, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
                ?>
            </div>
            <div class="row">
                <?php
                if ($prints != "yes") {
                    print "<div class='col-2'>";
                    displayEmployeeStatus($conn, "lstEmployeeStatus", $lstEmployeeStatus, "", "");
                    print "</div>";
                    print "<div class='col-2'>";
                    displayOTDay($conn, "OT Day 01", "lstOT1", $lstOT1);
                    print "</div>";
                    print "<div class='col-2'>";
                    displayOTDay($conn, "OT Day 02", "lstOT2", $lstOT2);
                    print "</div>";
                    print "<div class='col-2'>";
                    $array = array(array("tuser.id", "Employee Code"), array("tuser.name, tuser.id", "Employee Name - Code"), array("tuser.dept, tuser.id", "Dept - Code"), array("tuser.company, tuser.dept, tuser.id", "Div/Desg - Dept - Code"), array("tuser.company, tuser.dept, tuser.group_id, tuser.id", "Div - Dept - Current Shift - Code"));
                    displaySort($array, $lstSort, 5);
                    print "</div>";
                    print "</div>";
                    print "<div class='row'>";
                    print "<div class='col-12'>";
                    print "<center><br><input name='btSearch' class='btn btn-primary' type='submit' value='Search Record'></center>";
                    print "</div>";
                    print "</div>";
                }
                ?>
        </div>
    </div>
    <?php
}
print "</div></div></div></div>";

if ($act == "searchRecord") {
    $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tuser.idno, tuser.remark, tuser.OT1, tuser.OT2, tuser.OTRotate FROM tuser, tgroup WHERE tuser.group_id = tgroup.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
    if ($lstShift != "") {
        $query = $query . " AND tgroup.id = " . $lstShift;
    }
    $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $_POST["lstGroup"], $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
    if ($lstOT1 != "") {
        $query = $query . " AND tuser.OT1 = '" . $lstOT1 . "'";
    }
    if ($lstOT2 != "") {
        $query = $query . " AND tuser.OT2 = '" . $lstOT2 . "'";
    }
    $query = $query . employeeStatusQuery($lstEmployeeStatus);
    if ($lstExempted == "Yes") {
        $query = $query . " AND tuser.id IN (SELECT EmployeeID FROM OTEmployeeExempt) ";
    } else {
        if ($lstExempted == "No") {
            $query = $query . " AND tuser.id NOT IN (SELECT EmployeeID FROM OTEmployeeExempt) ";
        }
    }
    $query = $query . " ORDER BY " . $lstSort;
    print '<div class="row"><div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12"><div class="card"><div class="card-body table-responsive">';
    if ($prints != "yes") {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800' class='table table-striped table-bordered dataTable'><thead> <tr><td><font face='Verdana' size='2'><input type='checkbox' name='chkAll' id='chkAll' onClick='checkAll(this)'></font></td>";
    } else {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%' class='table table-striped table-bordered dataTable'><tr>";
    }
    print "<td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td> <td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td> <td><font face='Verdana' size='2'>Dept</font></td> <td><font face='Verdana' size='2'>Div/Desg</font></td> <td><font face='Verdana' size='2'>Rmk</font></td> <td><font face='Verdana' size='2'>Current Shift</font></td> <td><font face='Verdana' size='2'>OT 1</font></td> <td><font face='Verdana' size='2'>OT 2</font></td> <td><font face='Verdana' size='2'>Rotate</font></td></tr></thead>";
    $result = mysqli_query($conn, $query);
    for ($count = 0; $cur = mysqli_fetch_row($result); $count++) {
        if ($cur[3] == "") {
            $cur[3] = "&nbsp;";
        }
        if ($cur[5] == "") {
            $cur[5] = "&nbsp;";
        }
        if ($cur[6] == "") {
            $cur[6] = "&nbsp;";
        }
        print "<tr>";
        if ($prints != "yes") {
            print "<input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'>";
            print "<input type='hidden' name='txhID" . $count . "' value='" . $cur[0] . "'>";
            print "<td bgcolor='" . $bgcolor . "'><font face='Verdana' size='2'><input type='checkbox' name='chk" . $count . "' id='chk" . $count . "'></td>";
            addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
            print "<td><a title='ID'><font face='Verdana' color='#000000' size='1'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td>";
        } else {
            addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
            print "<td><a title='ID'><font face='Verdana' color='#000000' size='1'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td>";
        }
        print "<td><a title='Name'><font face='Verdana' size='1'>" . $cur[1] . "</font></a></td> <td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $cur[5] . "</font></a></td> <td><a title='Dept'><font face='Verdana' size='1'>" . $cur[2] . "</font></a></td> <td><a title='Div/Desg'><font face='Verdana' size='1'>" . $cur[3] . "</font></a></td> <td><a title='Rmk'><font face='Verdana' size='1'>" . $cur[6] . "</font></a></td> <td><a title='Current Shift'><font face='Verdana' size='1'>" . $cur[4] . "</font></a></td> <td><a title='OT 1'><font face='Verdana' size='1'>" . $cur[7] . "</font></a></td> <td><a title='OT 2'><font face='Verdana' size='1'>" . $cur[8] . "</font></a></td> <td><a title='Rotate Shift after OT Day'><font face='Verdana' size='1'>" . $cur[9] . "</font></a></td> </tr>";
    }
    print "</table><input type='hidden' name='txtCount' value='" . $count . "'>";
    if ($excel != "yes") {
        print "<p align='center'><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $count . "</b></font>";
    }

    if ($prints != "yes") {
        if (strpos($userlevel, $current_module . "E") !== false) {
            print "<div class='row'>";
            print "<div class='col-3'></div>";
            print "<div class='col-2'>";
            displayOTDay($conn, "OT Day 01", "lstOTDay1", $lstOTDay1);
            print "</div>";
            print "<div class='col-2'>";
            displayOTDay($conn, "OT Day 02", "lstOTDay2", $lstOTDay2);
            print "</div>";
            print "<div class='col-2'>";
            print "<label class='form-label'>Rotate Shift after OT Day:</label><select name='lstRotateShift' class='form-select select2 shadow-none'> <option value='" . $lstRotateShift . "' selected>" . $lstRotateShift . "</option> <option value='No'>No</option> <option value='Yes'>Yes</option> </select>";
            print "</div>";
            print "</div>";
            print "<div class='row'>";
            print "<div class='col-12'>";
            print "<center><br><input name='btSave' class='btn btn-primary' type='button' value='Save Changes' onClick='javascript:saveChanges()'>";
            print "</div>";
            print "</div>";
        }
        print "<div class='row'>";
        print "<div class='col-12'>";
        print "<center><br><input type='button' value='Print Report' class='btn btn-primary' onClick='checkPrint(0)'>&nbsp;&nbsp;<input type='button' class='btn btn-primary' value='Excel' onClick='checkPrint(1)'></center>";
        print "</div>";
        print "</div>";
        print "</form>";
    }
    print "</p>";
}
print "</form>";
//echo "\r\n<script>\r\nfunction saveChanges(){\t\r\n\tx = document.frm1;\t\t\r\n\tif (confirm('Assign Special OT Days for the Selected Employees')){\r\n\t\tx.act.value = 'saveChanges';\r\n\t\tx.btSave.disabled = true;\r\n\t\tx.submit();\r\n\t}\r\n}\r\n\r\nfunction openWindow(a){\r\n\twindow.open(\"EmployeeChild.php?act=viewRecord&txtID=\"+a, \"\",\"height=400;width=400\");\r\n}\r\n\r\nfunction checkAll(){\t\r\n\tx = document.frm1;\r\n\ty = x.chkAll;\r\n\tz = x.txtCount.value;\t\r\n\tfor (i=0;i<z;i++){\t\t\r\n\t\tif (y.checked == true){\t\t\t\r\n\t\t\tdocument.getElementById(\"chk\"+i).checked = true;\t\t\t\r\n\t\t}else{\r\n\t\t\tdocument.getElementById(\"chk\"+i).checked = false;\r\n\t\t}\r\n\t}\r\n}\r\n</script>\r\n</center>";
print "</div></div></div></div></div>";
include 'footer.php';
?>
<script>

    function saveChanges() {
         x = document.frm1;
        console.log(x.act.value, x.lstOTDay1.value);
        
        if (confirm('Assign Special OT Days for the Selected Employees')) {
            x.act.value = 'saveChanges';
            x.btSave.disabled = true;
            x.submit();
        }
    }

    function openWindow(a) {
        window.open("EmployeeChild.php?act=viewRecord&txtID=" + a, "", "height=400;width=400");
    }

    function checkAll(chkAll) {
        if (!chkAll || typeof chkAll.checked === "undefined") {
            console.error("'chkAll' checkbox is not valid.");
            return;
        }

        // Reference the form containing the checkboxes
        var form = document.forms["frm1"];
        if (!form) {
            console.error("Form 'frm1' not found.");
            return;
        }

        var txtCount = form.txtCount; // Reference to txtCount
        if (!txtCount || typeof txtCount.value === "undefined") {
            console.error("'txtCount' input element not found or has no value.");
            return;
        }

        var count = parseInt(txtCount.value, 10); // Parse the value of txtCount
        if (isNaN(count)) {
            console.error("'txtCount' does not contain a valid number.");
            return;
        }

        // Loop through the checkboxes based on the count
        for (var i = 0; i < count; i++) {
            var checkbox = document.getElementById("chk" + i); // Get each checkbox
            if (checkbox) {
                checkbox.checked = chkAll.checked; // Set its checked state
            } else {
                console.warn(`Checkbox with id 'chk${i}' not found.`);
            }
        }
    }

    $(document).ready(function () {
        var table = $('#zero_config').DataTable();

        // Use event delegation for the "Select All" checkbox
        $('#chkAll').on('click', function () {
            var checked = this.checked;
            // Select all checkboxes in the table (visible and non-visible rows)
            $('input[type="checkbox"]', table.rows().nodes()).prop('checked', checked);
        });
    });
</script>