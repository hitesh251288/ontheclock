<?php
ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "35";
set_time_limit(0);
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
$flagLimitType = $_SESSION[$session_variable . "FlagLimitType"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=CAGRoster.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$iconn = openIConnection();
$jconn = openIConnection();
$act = $_GET["act"];
if ($act == "") {
    $act = $_POST["act"];
}
$prints = $_GET["prints"];
$excel = $_GET["excel"];
$message = $_GET["message"];
if ($message == "") {
    $message = "Access Group Roster (Pre Attendance Pattern) <br>Valid ONLY for Shifts with Routine Type = Daily";
}
$txtFrom = $_POST["txtFrom"];
$txtTo = $_POST["txtTo"];
if ($txtFrom == "") {
    $txtFrom = displayDate(getNextDay(insertToday(), 1));
}
if ($txtTo == "") {
    $txtTo = "31/12/" . substr(displayToday(), 6, 4);
}
$lstSetFlag = $_POST["lstSetFlag"];
$txtPatternChangeDay = $_POST["txtPatternChangeDay"];
if ($txtPatternChangeDay == "") {
    $txtPatternChangeDay = "8";
}
if (!is_numeric($txtPatternChangeDay)) {
    $txtPatternChangeDay = "8";
}
$dayCount = $txtPatternChangeDay - 1;
$lstSetGroup = $_POST["lstSetGroup"];
$txhRowCount = $_POST["txhRowCount"];
if ($act == "saveRecord") {
    $this_id = "";
    $tot = $_POST["txtTot"];
    for ($i = 0; $i <= $tot; $i++) {
        if ($_POST["chk" . $i] != "") {
            $j = $_POST["chk" . $i];
            while ($j <= insertDate($txtTo)) {
                $query = "INSERT INTO CAGRotation (CAGID, e_date, group_id) VALUES (" . $_POST["txhID" . $i] . ", " . $j . ", '" . $lstSetGroup . "')";
                if (updateIData($iconn, $query, true)) {
                    
                }
                $j = getNextDay($j, $txtPatternChangeDay);
            }
            if ($this_id != $_POST["txhID" . $i]) {
                $text = "Created Access Group Schedule: " . $_POST["txhID" . $i] . " FROM Date: " . $txtFrom . " TO Date: " . $txtTo . " in Pattern of " . $txtPatternChangeDay . " Days with Shift: " . $lstSetGroup;
                $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                updateIData($jconn, $query, true);
            }
            $this_id = $_POST["txhID" . $i];
        }
    }
    $message = "Successfully Scheduled the Pattern from the Selected Date(s) for the Selected Access Group(s)";
    $act = "searchRecord";
} else {
    if ($act == "deletePattern") {
        $this_id = "";
        $tot = $_POST["txtTot"];
        for ($i = 0; $i <= $tot; $i++) {
            if ($_POST["chk" . $i] != "") {
                $j = $_POST["chk" . $i];
                while ($j <= insertDate($txtTo)) {
                    $query = "DELETE FROM CAGRotation WHERE CAGID = " . $_POST["txhID" . $i] . " AND e_date <= " . insertDate($txtTo);
                    if (updateIData($iconn, $query, true)) {
                        
                    }
                    $j = getNextDay($j, $txtPatternChangeDay);
                }
                if ($this_id != $_POST["txhID" . $i]) {
                    $text = "Deleted Access Group Schedule: " . $_POST["txhID" . $i] . " FROM Date: " . $txtFrom . " TO Date: " . $txtTo;
                    $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                    updateIData($jconn, $query, true);
                }
                $this_id = $_POST["txhID" . $i];
            }
        }
        $message = "Successfully Deleted the Pattern from the Selected Date(s) for the Selected Access Group(s)";
        $act = "searchRecord";
    }
}
if ($prints != "yes") {
    include 'header.php';
    ?>
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-12 d-flex no-block align-items-center">
                <h4 class="page-title">Access Group Roster (Pre Attendance Pattern)</h4>
                <div class="ms-auto text-end">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Access Group Roster (Pre Attendance Pattern)
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <?php
}
//print "<html><title>Access Group Roster (Pre Attendance Pattern)</title>";
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
        header("Content-Disposition: attachment; filename=CAGRoster.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        print "<body>";
    }
}
print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='CAGRoster.php'><input type='hidden' name='act' value='searchRecord'>";
if ($excel != "yes") {
    ?>
    <div class="card">
        <div class="card-body">
            <?php
            print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b></font></p>";
            if ($prints != "yes") {
//            print "<table width='800' cellpadding='1' cellspacing='-1'>";
            } else {
//            print "<table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
            }
            
            ?>
            <div class="row">
                <div class="col-3"></div>
                <div class="col-2">
                    <?php
                    displayTextbox("txtFrom", "Pattern Start Date: ", $txtFrom, $prints, 12, "", "");
                    ?>
                </div>
                <div class="col-2">
                    <?php
                    displayTextbox("txtTo", "Pattern End Date: ", $txtTo, $prints, 12, "", "");
                    ?>
                </div>
                <div class="col-2">
                    <?php
                    displayTextbox("txtPatternChangeDay", "Pattern Change Days: ", $txtPatternChangeDay, $prints, 5, "25%", "75%");
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <?php
                    print "<center><input name='btSearch' class='btn btn-primary' type='submit' value='Search Record'></center>";
                    ?>
                </div>
            </div>
            <!--</form>-->
        </div>
    </div>
    <?php
}
print "</div></div></div></div>";
if ($act == "searchRecord") {
    print '<div class="row"><div class="col-md-12"><div class="card"><div class="card-body table-responsive">';
    print "<table width='800' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' class='table table-striped table-bordered dataTable'>";
    print "<tr><td bgcolor='#F0F0F0'>&nbsp;</td><td bgcolor='#F0F0F0'>&nbsp;</td>";
    for ($i = 0; $i < $dayCount; $i++) {
        print "<td bgcolor='#F0F0F0'>&nbsp;</td>";
    }
    print "</tr>";
    print "<tr><td><font face='Verdana' size='2'>Name</font></td><td><font face='Verdana' size='1'>&nbsp;</font></td>";
    for ($i = 0; $i < $dayCount; $i++) {
        $next = strtotime(substr($txtFrom, 0, 2) . "-" . substr($txtFrom, 3, 2) . "-" . substr($txtFrom, 6, 4) . " + " . $i . " day");
        $a = getDate($next);
        print "<td><font face='Verdana' size='2'>" . $a["mday"] . "</font></td>";
    }
    print "</tr>";
    print "<tr>";
    print "<td><font face='Verdana' size='1'>&nbsp;</font></td><td>&nbsp;</td>";
    for ($i = 0; $i < $dayCount; $i++) {
        $next = strtotime(substr($txtFrom, 0, 2) . "-" . substr($txtFrom, 3, 2) . "-" . substr($txtFrom, 6, 4) . " + " . $i . " day");
        $a = getDate($next);
        substr($a["weekday"], 0, 1);
        print "<td><a title='" . $a["weekday"] . "'><font face='Verdana' size='2'>" . substr($a["weekday"], 0, 1) . "</font></a></td>";
    }
    print "</tr>";
    print "<tr><td><font face='Verdana' size='1'>&nbsp;</font></td>";
    if ($prints != "yes") {
        print "<td bgcolor='#F0F0F0' align='center'><font face='Verdana' size='2'><b>All</b></font>";
    } else {
        print "<td bgcolor='#F0F0F0' align='center'><font face='Verdana' size='2'><b>&nbsp;</b></font>";
    }
    for ($i = 0; $i < $dayCount; $i++) {
        print "<td bgcolor='#F0F0F0'><font face='Verdana' size='1'>";
        if ($prints != "yes") {
            print "<input type='checkbox' onClick='javascript:checkAllDate(" . $i . ", " . $dayCount . ", this)'>";
        } else {
            print "&nbsp;";
        }
        print "</font></td>";
    }
    print "</tr>";
    $count = 0;
    $tot = 0;
    $subc = 0;
    $eid = "";
    $txtDate = insertDate($txtFrom);
    $txtLastDate = insertDate($txtTo);
    $row_count = 0;
    $query = "SELECT CAGID, Name from CAG ORDER BY Name";
    $result = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
        $row_count++;
        $tot++;
        print "<tr><td><input type='hidden' name='txtCAG" . $count . "' value='" . $cur[0] . "'><font face='Verdana' size='1'>" . $cur[1] . "</font></td>";
        print "<td bgcolor='#F0F0F0'>";
        if ($prints != "yes") {
            print "<a title='Check All'><input type='checkbox' onClick='javascript:checkAllEmp(" . ($tot + 1) . ", " . $dayCount . ", this)'></a>";
        } else {
            print "<font face='Verdana' size='1'>&nbsp;</font>";
        }
        print "</td>";
        $txtDate = insertDate($txtFrom);
        for ($i = 0; $i < $dayCount; $i++) {
            $tot++;
            $subc++;
            $count++;
            $query = "SELECT CAGRID, tgroup.name FROM CAGRotation, tgroup WHERE tgroup.id = CAGRotation.group_id AND CAGRotation.e_date = '" . $txtDate . "' AND CAGRotation.CAGID = '" . $cur[0] . "'";
            $flag_result = selectData($conn, $query);
            if ($flag_result[0] != "") {
                print "<td><font face='Verdana' size='1'>" . $flag_result[1] . "</font></td>";
            } else {
                displayDate($txtDate);
                print "<td><a title='" . displayDate($txtDate) . "'><input type='checkbox' name='chk" . $tot . "' value='" . $txtDate . "' id='chk-" . $tot . "'> <input type='hidden' name='txhID" . $tot . "' value='" . $cur[0] . "'></a></td>";
            }
            $next = strtotime(substr($txtDate, 6, 2) . "-" . substr($txtDate, 4, 2) . "-" . substr($txtDate, 0, 4) . " + 1 day");
            $a = getDate($next);
            $m = $a["mon"];
            if ($m < 10) {
                $m = "0" . $m;
            }
            $d = $a["mday"];
            if ($d < 10) {
                $d = "0" . $d;
            }
            $txtDate = $a["year"] . $m . $d;
        }
    }
    print "</table><p align='center'>";
    if ($prints != "yes" && 0 < $count && stripos($userlevel, $current_module . "A") !== false && $_SESSION[$session_variable . "LockDate"] < insertDate($txtFrom) && $_SESSION[$session_variable . "LockDate"] < insertDate($txtTo)) {
        print "<div class='row'>";
        print "<div class='col-3'></div>";
        print "<div class='col-2'></div>";
        print "<div class='col-2'>";
        $query = "SELECT id, name from tgroup WHERE ShiftTypeID = 1 AND id > 1 ORDER BY name";
        displayList("lstSetGroup", "Shift: ", $lstGroup, $prints, $conn, $query, "", "", "");
        print "</div>";
        print "</div>";
        print "<div class='row'>";
        print "<div class='col-12'>";
        print "<center><input name='btSubmit' type='button' class='btn btn-primary' value='Save Changes' onClick='javascript:checkSubmit()'>";
        if (stripos($userlevel, $current_module . "D") !== false) {
            print "&nbsp;&nbsp;<input name='btDelete' type='button' value='Delete Pattern' class='btn btn-primary' onClick='javascript:deletePattern()'></td>";
        }
        print "</center>";
        print "</div>";
        print "</div>";
    }
    if ($excel != "yes") {
        print "<center><br><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $row_count . "</b></font></center>";
    }
    print "</p>";
}
print "<input type='hidden' name='txtTot' value='" . $tot . "'> <input type='hidden' name='txhRowCount' value='" . $row_count . "'></form>";
print "</div></div></div></div></div>";
include 'footer.php';
echo "\r\n<script>\r\nfunction checkSubmit(){\r\n\tx = document.frm1;\r\n\tif (x.lstSetGroup.value == ''){\r\n\t\talert('Please select a Shift');\r\n\t\tx.lstSetGroup.focus();\r\n\t}else{\r\n\t\tx.act.value='saveRecord';\r\n\t\tx.btSubmit.disabled = true;\r\n\t\tx.submit();\t\t\r\n\t}\r\n}\r\n\r\nfunction deletePattern(){\r\n\tx = document.frm1;\r\n\tif (confirm('Delete Selected Pattern')){\r\n\t\tx.act.value='deletePattern';\r\n\t\tx.btDelete.disabled = true;\r\n\t\tx.submit();\t\t\r\n\t}\r\n}\r\n\r\nfunction checkAllEmp(b, c, x){\r\n\tif (x.checked){\r\n\t\tfor (i=b;i<(b+c);i++){\r\n\t\t\tif (document.getElementById('chk-'+i)){\r\n\t\t\t\tdocument.getElementById('chk-'+i).checked = true;\r\n\t\t\t}\r\n\t\t}\r\n\t}else{\r\n\t\tfor (i=b;i<(b+c);i++){\r\n\t\t\tif (document.getElementById('chk-'+i)){\r\n\t\t\t\tdocument.getElementById('chk-'+i).checked = false;\r\n\t\t\t}\r\n\t\t}\r\n\t}\r\n}\r\n\r\nfunction checkAllDate(b, c, x){\r\n\ta = document.frm1.txtTot.value;\t\r\n\tif (x.checked){\r\n\t\tfor (i=b;i<a;i=(i+c+1)){\r\n\t\t\tif (document.getElementById('chk-'+(i+2))){\r\n\t\t\t\tdocument.getElementById('chk-'+(i+2)).checked = true;\r\n\t\t\t}\r\n\t\t}\r\n\t}else{\r\n\t\tfor (i=b;i<a;i=(i+c+1)){\r\n\t\t\tif (document.getElementById('chk-'+(i+2))){\r\n\t\t\t\tdocument.getElementById('chk-'+(i+2)).checked = false;\r\n\t\t\t}\r\n\t\t}\r\n\t}\r\n}\r\n</script>\r\n</center></body></html>";
?>