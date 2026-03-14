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
    header("Location: " . $config["REDIRECT"] . "?url=ReportUserTransact.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$act = $_GET["act"];
if ($act == "") {
    $act = $_POST["act"];
}
$prints = $_GET["prints"];
$excel = $_GET["excel"];
$message = $_GET["message"];
if ($message == "") {
    $message = "User Transaction Report";
}
$lstUser = $_POST["lstUser"];
$lstModule = $_POST["lstModule"];
$lstType = $_POST["lstType"];
$txtFrom = $_POST["txtFrom"];
$txtTo = $_POST["txtTo"];
if ($txtFrom == "") {
    $txtFrom = displayToday();
}
if ($txtTo == "") {
    $txtTo = displayToday();
}
$txtT1 = $_POST["txtT1"];
$txtT2 = $_POST["txtT2"];
$txtT3 = $_POST["txtT3"];
$txtT4 = $_POST["txtT4"];
$txtT5 = $_POST["txtT5"];
$lstSort = $_POST["lstSort"];
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
if ($prints != "yes") {
    include 'header.php';
?>
<div class="page-breadcrumb">
                    <div class="row">
                        <div class="col-12 d-flex no-block align-items-center">
                            <h4 class="page-title">User Transaction Report</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            User Transaction Report
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

<?php
}
print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='ReportUserTransact.php'><input type='hidden' name='act' value='searchRecord'>";
//print "<html><title>User Transaction Report</title>";
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
        header("Content-Disposition: attachment; filename=ReportUserTransact.xls");
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
                if ($username == "virdi") {
                    $query = "SELECT Username, Username from UserMaster ORDER BY Username";
                } else {
                    $query = "SELECT Username, Username from UserMaster WHERE Username NOT LIKE 'virdi' ORDER BY Username";
                }
                displayList("lstUser", "User: ", $lstUser, $prints, $conn, $query, "", "", "");
                ?>
            </div>
            <?php 
            
            ?>
        </div>
        <div class="row">
            <div class="col-2">
                <?php 
                $query = "SELECT Name, Name from ModuleMaster WHERE Name NOT Like '%Time Alter%' AND Name NOT LIKE '%Exit Terminal%' ORDER BY Name";
                displayList("lstModule", "Module: ", $lstModule, $prints, $conn, $query, "", "", "");
                ?>
            </div>
            <div class="col-2">
                <?php 
                displayTextbox("txtFrom", "Date From (DD/MM/YYYY): ", $txtFrom, $prints, 12, "", "");
                ?>
            </div>
            <div class="col-2">
                <?php 
                displayTextbox("txtTo", "Date To (DD/MM/YYYY): ", $txtTo, $prints, 12, "", "");
                ?>
            </div>
            <div class="col-2">
                <?php 
                displayTextbox("txtT1", "Text 1 (Employee ID): ", $txtT1, $prints, 30, "", "");
                ?>
            </div>
            <div class="col-2">
                <?php 
                displayTextbox("txtT2", "Text 2 (Date): ", $txtT2, $prints, 30, "", "");
                ?>
            </div>
            <div class="col-2">
                <?php 
                displayTextbox("txtT3", "Text 3: ", $txtT3, $prints, 30, "", "");
                ?>
            </div>
            </div>
            <div class="row">
            <div class="col-2">
                <?php 
                displayTextbox("txtT4", "Text 4: ", $txtT4, $prints, 30, "", "");
                ?>
            </div>
            <div class="col-2">
                <?php 
                displayTextbox("txtT5", "Text 5: ", $txtT5, $prints, 30, "", "");
                ?>
            </div>
        <?php 
            if ($prints != "yes") {
                print "<div class='col-2'>";
                $array = array(array("Transact.Transactdate, Transact.Transacttime", "Transaction Date"), array("Transact.Username, Transact.Transactdate, Transact.Transacttime", "System User"));
                displaySort($array, $lstSort, 2);
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

if ($act == "searchRecord" && stripos($userlevel, $current_module . "R") !== false) {
    if ($lstModule == "Time Alteration") {
        $query = "SELECT Username, DateFrom, TimeFrom, GateFrom, DateTo, TimeTo, GateTo, TransactDate FROM AlterLog WHERE LogID > 0 ";
        if ($txtFrom != "") {
            $query = $query . " AND TransactDate >= " . insertDate($txtFrom);
        }
        if ($txtTo != "") {
            $query = $query . " AND TransactDate <= " . insertDate($txtTo);
        }
        if ($lstUser != "") {
            $query = $query . " AND Username = '" . $lstUser . "'";
        } else {
            if ($username != "virdi") {
                $query = $query . " AND Username NOT LIKE 'virdi'";
            }
        }
    } else {
        $query = "SELECT Transactdate, Transacttime, Username, Transactquery FROM Transact WHERE TransactID > 0";
        if ($txtFrom != "") {
            $query = $query . " AND TransactDate >= " . insertDate($txtFrom);
        }
        if ($txtTo != "") {
            $query = $query . " AND TransactDate <= " . insertDate($txtTo);
        }
        if ($lstUser != "") {
            $query = $query . " AND Username = '" . $lstUser . "'";
        } else {
            if ($username != "virdi") {
                $query = $query . " AND Username NOT LIKE 'virdi'";
            }
        }
        if ($lstModule == "Shifts") {
            $query = $query . " AND Transactquery LIKE '%Updated Shift%'";
        } else {
            if ($lstModule == "Assign Terminals") {
                $query = $query . " AND Transactquery LIKE '%Assigned Terminals%'";
            } else {
                if ($lstModule == "Assign Shifts") {
                    $query = $query . " AND (Transactquery LIKE '%Assigned Shift%' OR Transactquery LIKE '%Shift Rotation%')";
                } else {
                    if ($lstModule == "Global Settings") {
                        $query = $query . " AND Transactquery LIKE '%Updated Global Settings%'";
                    } else {
                        if ($lstModule == "Users") {
                            $query = $query . " AND (Transactquery LIKE '%Logged In%' OR Transactquery LIKE '%Changed Password%' OR Transactquery LIKE '%Added User%' OR Transactquery LIKE '%Deleted User%' OR Transactquery LIKE '%Copied User Right%' OR Transactquery LIKE '%Updated User%' OR Transactquery LIKE '%User Department Access%')";
                        } else {
                            if ($lstModule == "Projects") {
                                $query = $query . " AND Transactquery LIKE '%Project ID%'";
                            } else {
                                if ($lstModule == "Project Assignment") {
                                    $query = $query . " AND Transactquery LIKE '%Project Log%'";
                                } else {
                                    if ($lstModule == "Approve/Pre Approve Overtime") {
                                        $query = $query . " AND Transactquery LIKE '%Approve OT%'";
                                    } else {
                                        if ($lstModule == "Pre Approve Overtime") {
                                            $query = $query . " AND Transactquery LIKE '%Pre Approve OT%'";
                                        } else {
                                            if ($lstModule == "Proxy") {
                                                $query = $query . " AND Transactquery LIKE '%Proxy%'";
                                            } else {
                                                if ($lstModule == "Pre Flag Days") {
                                                    $query = $query . " AND Transactquery LIKE '%Pre Flagged%'";
                                                } else {
                                                    if ($lstModule == "Post Flag Days") {
                                                        $query = $query . " AND Transactquery LIKE '%Post Flagged%'";
                                                    } else {
                                                        if ($lstModule == "Delete Processed Log") {
                                                            $query = $query . " AND Transactquery LIKE '%Deleted Processed Record%'";
                                                        } else {
                                                            if ($lstModule == "Employees") {
                                                                $query = $query . " AND Transactquery LIKE '%Updated ID%'";
                                                            } else {
                                                                if ($lstModule == "OT Days/Date") {
                                                                    $query = $query . " AND (Transactquery LIKE '%OT Day%' OR Transactquery LIKE '%OT Date%' OR Transactquery LIKE '%Exempted%')";
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        if ($txtT1 != "") {
            $query = $query . " AND Transactquery LIKE '%" . $txtT1 . "%'";
        }
        if ($txtT2 != "") {
            $query = $query . " AND Transactquery LIKE '%" . $txtT2 . "%'";
        }
        if ($txtT3 != "") {
            $query = $query . " AND Transactquery LIKE '%" . $txtT3 . "%'";
        }
        if ($txtT4 != "") {
            $query = $query . " AND Transactquery LIKE '%" . $txtT4 . "%'";
        }
        if ($txtT5 != "") {
            $query = $query . " AND Transactquery LIKE '%" . $txtT5 . "%'";
        }
    }
    $query = $query . " ORDER BY " . $lstSort;
    print '<div class="row"><div class="col-md-12 col-xlg-12 col-xs-12 col-sm-12 col-lg-12"><div class="card"><div class="card-body table-responsive">';
    if ($prints != "yes") {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='800' class='table table-striped table-bordered dataTable' id='zero_config'>";
    } else {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%' class='table table-striped table-bordered dataTable'>";
    }
    if ($lstModule == "Time Alteration") {
        print "<thead><tr><td><font face='Verdana' size='2'><b>Transaction Date</b></font></td> <td><font face='Verdana' size='2'><b>Username</b></font></td> <td><font face='Verdana' size='2'><b>Date From</b></font></td> <td><font face='Verdana' size='2' color='#FF0000'><b>Date To</b></font></td> <td><font face='Verdana' size='2'><b>Time From</b></font></td> <td><font face='Verdana' size='2' color='#FF0000'><b>Time To</b></font></td> <td><font face='Verdana' size='2'><b>Gate From</b></font></td> <td><font face='Verdana' size='2' color='#FF0000'><b>Gate To</b></font></td></tr></thead>";
    } else {
        print "<thead><tr><td><font face='Verdana' size='2'><b>Transaction Date</b></font></td> <td><font face='Verdana' size='2'><b>Transaction Time</b></font></td> <td><font face='Verdana' size='2'><b>Username</b></font></td> <td><font face='Verdana' size='2'><b>Statement</b></font></td> </tr></thead>";
    }
    $count = 0;
    for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $count++) {
        if ($lstModule == "Time Alteration") {
            displayDate($cur[7]);
            displayDate($cur[1]);
            displayDate($cur[4]);
            displayTime($cur[2]);
            displayTime($cur[5]);
            print "<tr><td><font face='Verdana' size='1'>" . displayDate($cur[7]) . "</font></td> <td><font face='Verdana' size='1'>" . $cur[0] . "</font></td> <td><font face='Verdana' size='1'>" . displayDate($cur[1]) . "</font></td> <td><font face='Verdana' size='1' color='#FF0000'>" . displayDate($cur[4]) . "</font></td> <td><font face='Verdana' size='1'>" . displayTime($cur[2]) . "</font></td> <td><font face='Verdana' size='1' color='#FF0000'>" . displayTime($cur[5]) . "</font></td> <td><font face='Verdana' size='1'>" . $cur[3] . "</font></td> <td><font face='Verdana' size='1' color='#FF0000'>" . $cur[6] . "</font></td></tr>";
        } else {
            displayDate($cur[0]);
            displayTime($cur[1]);
            print "<tr><td><font face='Verdana' size='1'>" . displayDate($cur[0]) . "</font></td> <td><font face='Verdana' size='1'>" . displayTime($cur[1]) . "</font></td> <td><font face='Verdana' size='1'>" . $cur[2] . "</font></td> <td><font face='Verdana' size='1'>" . $cur[3] . "</font></td> </tr>";
        }
    }
    print "</table>";
    if ($excel != "yes") {
        print "<br><p align='center'><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $count . "</b></font>";
    }
    if ($prints != "yes") {
        print "<br><input type='button' value='Print Report' class='btn btn-primary' onClick='checkPrint(0)'>&nbsp;&nbsp;<input type='button' class='btn btn-primary' value='Excel' onClick='checkPrint(1)'>";
    }
    print "</p>";
}
print "</form>";
echo "</center>";
print "</div></div></div></div></div>";
include 'footer.php';

?>