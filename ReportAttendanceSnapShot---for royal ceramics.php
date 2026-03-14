<?php


ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "21";
set_time_limit(0);
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
$ot1f = $_SESSION[$session_variable . "ot1f"];
$ot2f = $_SESSION[$session_variable . "ot2f"];
$otdf = $_SESSION[$session_variable . "otdf"];
$macAddress = $_SESSION[$session_variable . "MACAddress"];
$frt = $_SESSION[$session_variable . "FlagReportText"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=ReportAttendanceSnapShot.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$act = $_GET["act"];
if ($act == "") {
    $act = $_POST["act"];
}
$prints = $_GET["prints"];
$excel = $_GET["excel"];
$subReport = $_GET["subReport"];
$message = $_GET["message"];
if ($message == "") {
    $message = "Attendance Snap Shot Report<br>Report Valid ONLY for Shifts with Routine Type = Daily";
}
$lstShift = $_POST["lstShift"];
$lstDepartment = $_GET["lstDepartment"];
if ($lstDepartment == "") {
    $lstDepartment = $_POST["lstDepartment"];
}
$lstDivision = $_GET["lstDivision"];
if ($lstDivision == "") {
    $lstDivision = $_POST["lstDivision"];
}
$lstEmployeeIDFrom = $_POST["lstEmployeeIDFrom"];
$lstEmployeeIDTo = $_POST["lstEmployeeIDTo"];
$txtEmployeeCode = $_POST["txtEmployeeCode"];
$txtEmployee = $_POST["txtEmployee"];
$txtFrom = $_GET["txtFrom"];
if ($txtFrom == "") {
    $txtFrom = $_POST["txtFrom"];
}
$txtTo = $_GET["txtTo"];
if ($txtTo == "") {
    $txtTo = $_POST["txtTo"];
}
if ($txtFrom == "") {
    if (substr(insertToday(), 6, 2) == "01") {
        if (substr(insertToday(), 4, 2) == "01") {
            $txtFrom = "01/12/" . (substr(insertToday(), 0, 4) - 1);
        } else {
            if (substr(insertToday(), 4, 2) - 1 < 10) {
                $txtFrom = "01/0" . (substr(insertToday(), 4, 2) - 1) . "/" . substr(insertToday(), 0, 4);
            } else {
                $txtFrom = "01/" . (substr(insertToday(), 4, 2) - 1) . "/" . substr(insertToday(), 0, 4);
            }
        }
    } else {
        $txtFrom = "01/" . substr(displayToday(), 3, 7);
    }
}
if ($txtTo == "") {
    $txtTo = displayDate(getLastDay(insertToday(), 1));
}
$txtRemark = $_GET["txtRemark"];
if ($txtRemark == "") {
    $txtRemark = $_POST["txtRemark"];
}
$txtPhone = $_GET["txtPhone"];
if ($txtPhone == "") {
    $txtPhone = $_POST["txtPhone"];
}
$txtSNo = $_GET["txtSNo"];
if ($txtSNo == "") {
    $txtSNo = $_POST["txtSNo"];
}
$lstGroup = $_GET["lstGroup"];
if ($lstGroup == "") {
    $lstGroup = $_POST["lstGroup"];
}
$lstEmployeeStatus = "";
if (isset($_POST["lstEmployeeStatus"])) {
    $lstEmployeeStatus = $_POST["lstEmployeeStatus"];
} else {
    if (isset($_GET["lstEmployeeStatus"])) {
        $lstEmployeeStatus = $_GET["lstEmployeeStatus"];
    } else {
        $lstEmployeeStatus = "ACT";
    }
}
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
$lstReportType = $_POST["lstReportType"];
$txtSatFactor = $_POST["txtSatFactor"];
if ($txtSatFactor == "" || is_numeric($txtSatFactor) == false) {
    $txtSatFactor = $ot1f;
}
$txtSunFactor = $_POST["txtSunFactor"];
if ($txtSunFactor == "" || is_numeric($txtSunFactor) == false) {
    $txtSunFactor = $ot2f;
}
$txtFlagFactor = $_POST["txtFlagFactor"];
if ($txtFlagFactor == "" || is_numeric($txtFlagFactor) == false) {
    $txtFlagFactor = $otdf;
}
$lstDB = $_POST["lstDB"];
if ($lstDB == "") {
    $lstDB = "Live";
}
$tflag = false;
if ($macAddress == "00-18-8B-8C-C9-D2") {
    $tflag = true;
} else {
    if ($macAddress == "00-15-5D-82-6E-0A") {
        $tflag = true;
    }
}
displaySuperHeader($prints, $excel, $csv, $current_module, $userlevel, "Attendance Snapshot Report", true, false);
if ($excel != "yes") {
    print "<p align='center'><font face='Verdana' size='1' color='#FF0000'><b>" . $message . "</b></font></p>";
    if ($prints != "yes") {
        print "<table width='800' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
        print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Select ONE or MORE options and click 'Search Record'</b></font></td></tr>";
    } else {
        print "<table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
        print "<tr><td>&nbsp;</td><td><font face='Verdana' size='1'><b>Selected Options</b></font></td></tr>";
    }
    print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='ReportAttendanceSnapShot.php'><input type='hidden' name='act' value='searchRecord'><tr>";
    $query = "SELECT id, name from tgroup WHERE ShiftTypeID = 1 ORDER BY name";
    displayList("lstShift", "Current Shift: ", $lstShift, $prints, $conn, $query, "", "25%", "75%");
    print "</tr>";
    displaySearchFields($conn, $prints, $session_variable, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $lstEmployeeName, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $lstGroup);
    print "<tr>";
    print "<td colspan='2' ><table width='100%' cellspacing='0' cellpadding='0' border='0'><tr>";
    displayTextbox("txtFrom", "Date From <font size='1'>(DD/MM/YYYY)</font>: ", $txtFrom, $prints, 12, "25%", "25%");
    displayTextbox("txtSatFactor", "Saturday OT Factor: ", $txtSatFactor, $prints, 4, "25%", "25%");
    print "</tr></table></td>";
    print "</tr>";
    print "<tr>";
    print "<td colspan='2' ><table width='100%' cellspacing='0' cellpadding='0' border='0'><tr>";
    displayTextbox("txtTo", "Date To <font size='1'>(DD/MM/YYYY)</font>: ", $txtTo, $prints, 12, "25%", "25%");
    displayTextbox("txtSunFactor", "Sunday OT Factor: ", $txtSunFactor, $prints, 4, "25%", "25%");
    print "</tr></table></td>";
    print "</tr>";
    if ($prints != "yes") {
        print "<tr>";
        print "<td colspan='2'><table width='100%' cellspacing='0' cellpadding='0' border='0'><tr>";
        print "<td align='right' width='25%'><font face='Verdana' size='2'>Report Type:</font></td> <td width='25%'><select name='lstReportType' class='form-control'><option selected value='" . $lstReportType . "'>" . $lstReportType . "</option><option value='Basic/Net'>Basic/Net</option><option value='Custom1'>Custom1</option><option value='Custom2'>Custom2</option><option value='Custom3'>Custom3</option><option value='Custom4'>Custom4</option><option value='Custom5'>Custom5</option><option value='Custom6'>Custom6</option><option value='Custom7'>Custom7</option><option value='Custom8'>Custom8</option><option value='Custom9'>Custom9</option><option value='Custom10'>Custom10</option><option value=''>---</option></select></td>";
        displayTextbox("txtFlagFactor", "Flag OT Factor:", $txtFlagFactor, $prints, 4, "25%", "25%");
        print "</tr></table></td>";
        print "</tr>";
        print "<tr>";
        print "<td colspan='2'><table width='100%' cellspacing='0' cellpadding='0' border='0'><tr>";
        displayEmployeeStatus($conn, "lstEmployeeStatus", $lstEmployeeStatus, "25%", "25%");
        print "<td align='right' width='25%'><font face='Verdana' size='2'>DB:</font></td><td width='25%'><select name='lstDB' class='form-control'><option selected value='" . $lstDB . "'>" . $lstDB . "</option> <option value='Live'>Live</option> <option value='Archive'>Archive</option> </select></td>";
        print "</tr></table></td>";
        print "</tr>";
        print "<tr><td>&nbsp;</td><td><input name='btSearch' type='submit' value='Search Record'>&nbsp;&nbsp;<input type='button' value='Excel' onClick='checkPrint(1)'>&nbsp;&nbsp;<input type='button' value='CSV' onClick='checkPrint(3)'></td></tr></td></tr>";
    }
    print "</table><br>";
}
if ($act == "searchRecord") {
    $v_basic = "";
    $v_net = "";
    if ($lstReportType == "Basic/Net") {
        $query = "SELECT DBIP, DBName, DBUser, DBPass FROM OtherSettingMaster";
        $main_result = selectData($conn, $query);
        $txtDBIP = $main_result[0];
        $txtDBName = $main_result[1];
        $txtDBUser = $main_result[2];
        $txtDBPass = $main_result[3];
        $mconn = mysql_connection($txtDBIP, $txtDBName, $txtDBUser, $txtDBPass);
        if ($mconn != "") {
            $query = "SELECT generateddetail.EmployeeID, generateddetail.Salary, generateddetail.Netincome FROM generateddetail, generatedmaster WHERE generatedmaster.Id = generateddetail.ID AND generatedmaster.From_Value >=  '" . displayParadoxDate(insertDate($txtFrom)) . " 00:00:00' AND generatedmaster.To_Value <= '" . displayParadoxDate(insertDate($txtTo)) . " 23:59:59' ORDER BY generateddetail.EmployeeID ";
            $result = mysqli_query($query, $mconn);
            while ($cur = mysqli_fetch_row($result)) {
                $v_basic[$cur[0]] = $cur[1];
                $v_net[$cur[0]] = $cur[2];
            }
        }
    }
   
    $query = "SELECT LateDays FROM OtherSettingMaster";
    $lateness_result = selectData($conn, $query);
    $lateness_absence = $lateness_result[0];
    $global_saturday = 0;
    $global_sunday = 0;
    $satsun_query = "SELECT Day FROM OTDay WHERE OT = 1";
    $satsun_result = mysqli_query($satsun_query, $conn);
    while ($satsun_cur = mysqli_fetch_row($satsun_result)) {
        if ($satsun_cur[0] == "Saturday") {
            $global_saturday = 1;
        } else {
            if ($satsun_cur[0] == "Sunday") {
                $global_sunday = 1;
            }
        }
    }
    if ($excel != "yes" && $lstReportType != "Custom2" && $lstReportType != "Custom3") {
        print "<p align='center'><font face='Verdana' size='1'><b>" . $frt . "</b></font></p>";
        print "<p><font face='Verdana' size='1'><b>WK</b> = Week Day [EXCLUDING Proxy and Flag]; <b>PXY</b> = Proxy ; <b>FLG</b> = Flag Day ; <b>SAT</b> = Saturday ; <b>SUN</b> = Sunday <br><b>NS</b> = Night Shifts ; <b>LI</b> = Late In ; <b>EO</b> = Early Out ; <b>MB</b> = More Break <br><b>TLD</b> = Total Days ; <b>TND</b> = Total Night Shifts ; <b>TLH</b> =Total Hours ; <b>TNH</b> = Total Night Shift Hours ; <b>LIH</b> = Total Late In Hours ; <b>EOH</b> = Total Early Out Hours ; <b>MBH</b> = Total More Break Hours <br><b>O</b> = Overtime Hours ; <b>AO</b> = Approved Overtime Hours ; <b>N</b> = Normal Hours ; <b>A</b> = Absent Days ; <b>P</b> = Present Days (Excludes Sundays for Custom6 option) <br><b>A</b> = Total Absent Days ; <b>A/S</b> = Absent Days EXCLUDING Sunday / OT2 ; <b>A/SS</b> = Absent Days EXCLUDING Saturdays and Sundays / OT1 and OT2 ; <b>WD</b> = Total Weekdays in selected period <br>No of Late Days to be Treated as ONE Absent: <b>" . $lateness_absence . "</b>";
        print "</b></font></p>";
    }
    $query = "SELECT * FROM TLSFlag";
    $colour_result = selectData($conn, $query);
    $query = "SELECT RASSelection FROM UserMaster WHERE Username = '" . $username . "'";
    $ras_result = selectData($conn, $query);
    $ras = $ras_result[0];
    $table_name = "Access.AttendanceMaster";
    if ($lstDB == "Archive") {
        $table_name = "AccessArchive.archive_am";
    }
    if ($lstReportType == "Custom1") {
        $query = "SELECT DISTINCT(EmployeeID), tuser.name, tuser.PassiveType, tuser.phone, tuser.dept, tuser.datelimit FROM " . $table_name . ", tuser WHERE " . $table_name . ".EmployeeID = tuser.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
    } else {
        $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, " . $table_name . ".group_min, " . $table_name . ".Normal, " . $table_name . ".Grace, " . $table_name . ".Overtime, " . $table_name . ".ADate, tuser.idno, tuser.remark, " . $table_name . ".Flag, " . $table_name . ".Day, " . $table_name . ".NightFlag, " . $table_name . ".AOvertime, " . $table_name . ".OT1, " . $table_name . ".OT2, tuser.PassiveType, " . $table_name . ".LateIn, " . $table_name . ".EarlyOut, " . $table_name . ".MoreBreak, " . $table_name . ".LateIn_flag, " . $table_name . ".EarlyOut_flag, " . $table_name . ".MoreBreak_flag, " . $table_name . ".EarlyIn_flag, tuser.datelimit FROM tuser, tgroup, " . $table_name . " WHERE " . $table_name . ".group_id = tgroup.id AND " . $table_name . ".EmployeeID = tuser.id " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
        if ($lstShift != "") {
            $query = $query . " AND tgroup.id = " . $lstShift;
        }
    }
    $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $lstGroup, $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
    if ($txtFrom != "" && $lstWeek == "") {
        $query = $query . " AND (" . $table_name . ".ADate >= " . insertDate($txtFrom) . " OR " . $table_name . ".ADate = 0) ";
    }
    if ($txtTo != "" && $lstWeek == "") {
        $query = $query . " AND (" . $table_name . ".ADate <= " . insertDate($txtTo) . " OR " . $table_name . ".ADate = 0) ";
    }
    $query = $query . employeeStatusQuery($lstEmployeeStatus);
    if ($lstReportType == "Custom1") {
        $query .= " ORDER BY " . $table_name . ".EmployeeID";
    } else {
        $query .= " ORDER BY tuser.id, " . $table_name . ".ADate";
    }
    $dayCount = getTotalDays($txtFrom, $txtTo);
    if ($csv != "yes") {
        print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%'><tr>";
    }
    
    if ($lstReportType == "Custom3") {
        if ($csv != "yes") {
            print "<td><font face='Verdana' size='2'>Company</font></td>";
            print "<td><font face='Verdana' size='2'>Employee Code</font></td> <td><font face='Verdana' size='2'>Display Name</font></td>";
            print "<td><font face='Verdana' size='2'>Company Rule</font></td> <td><font face='Verdana' size='2'>Payslip Type</font></td> <td><font face='Verdana' size='2'>Process Period</font></td> <td><font face='Verdana' size='2'>Pay Run Definition</font></td> <td><font face='Verdana' size='2'>EmpEarningDef : OVERTIME - Fixed</font></td> <td><font face='Verdana' size='2'>EmpEarningDef : WEEKENDOVERTIME - Fixed</font></td> <td><font face='Verdana' size='2'>EmpEarningDef : SUN/PH - Fixed</font></td> <td><font face='Verdana' size='2'>EmployeeRule - Hours Per Day</font></td> <td><font face='Verdana' size='2'>EmployeeLeaveDef : ANN_LVE - Entitlement</font></td> <td><font face='Verdana' size='2'>EmployeeLeaveDef : MAT_LVE - Entitlement</font></td> <td><font face='Verdana' size='2'>EmployeeLeaveDef : SICK_LVE - Entitlement</font></td> <td><font face='Verdana' size='2'>EmployeePayPeriod - Actual Worked Days</font></td> <td><font face='Verdana' size='2'>EmployeePayPeriod - Period Work Days</font></td>";
        } else {
            print "Company;";
            print "Employee Code;Display Name;";
            print "Company Rule;Payslip Type;Process Period;Pay Run Definition;EmpEarningDef : OVERTIME - Fixed;EmpEarningDef : WEEKENDOVERTIME - Fixed;EmpEarningDef : SUN/PH - Fixed;EmployeeRule - Hours Per Day;EmployeeLeaveDef : ANN_LVE - Entitlement;EmployeeLeaveDef : MAT_LVE - Entitlement;EmployeeLeaveDef : SICK_LVE - Entitlement;EmployeePayPeriod - Actual Worked Days;EmployeePayPeriod - Period Work Days;";
        }
    } else {
        if ($csv != "yes") {
            print "<td><font face='Verdana' size='2'>ID</font></td> <td><font face='Verdana' size='2'>Name</font></td>";
        } else {
            print "ID;Name;";
        }
        if ($lstReportType != "Custom1" && $lstReportType != "Custom2" && $lstReportType != "Custom5" && $lstReportType != "Custom6") {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "IDColumnName"] . "</font></td>";
            } else {
                print $_SESSION[$session_variable . "IDColumnName"] . ";";
            }
        }
        if ($lstReportType != "Custom5" && $lstReportType != "Custom6") {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='2'>Dept</font></td>";
            } else {
                print "Dept;";
            }
        }
        if ($lstReportType != "Custom1" && $lstReportType != "Custom2" && $lstReportType != "Custom5" && $lstReportType != "Custom6") {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "DivColumnName"] . "</font></td>";
            } else {
                print $_SESSION[$session_variable . "DivColumnName"] . ";";
            }
        }
        if ($lstReportType != "Custom1" && $lstReportType != "Custom6" && $lstReportType != "Custom7" && $lstReportType != "Custom8" && $lstReportType != "Custom9" && $lstReportType != "Custom10") {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='2'>" . $_SESSION[$session_variable . "RemarkColumnName"] . "</font></td> ";
            } else {
                print $_SESSION[$session_variable . "RemarkColumnName"] . ";";
            }
        }
        if ($lstReportType == "Custom6") {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='2'>Category</font></td> ";
            } else {
                print "Category;";
            }
        }
//        if ($lstReportType == "Custom8") {
//            if ($csv != "yes") {
//                print "<td><font face='Verdana' size='2'>Category</font></td>";
//            } else {
//                print "Category;";
//            }
//        }
//        if ($lstReportType == "Custom7") {
//            if ($csv != "yes") {
//                print "<td><font face='Verdana' size='2'>Category</font></td> <td><font face='Verdana' size='2'>OT Hr</font></td> <td><font face='Verdana' size='2'>APT OT Hr</font></td>";
//            } else {
//                print "Category;OT;AO;";
//            }
//        }    
        if($lstReportType == "Custom9") {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='2'>Out Station</font></td><td><font face='Verdana' size='2'>Sick Leave</font></td><td><font face='Verdana' size='2'>Annual Leave</font></td><td><font face='Verdana' size='2'>Casual Leave</font></td><td><font face='Verdana' size='2'>Week Day Present</font></td><td><font face='Verdana' size='2'>Off Day Present</font></td><td><font face='Verdana' size='2'>Absent Days</font></td>";
            } else {
                print "V;B;G;O;WKD;SAT+SUN;A;";
            }
        }
        if ($lstReportType == "Custom7") {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='2'>OT Hr</font></td> <td><font face='Verdana' size='2'>APT OT Hr</font></td>";
            } else {
                print "OT;AO;";
            }
        }        
    }
    

    if ($lstReportType == "Custom1") {
        if ($tflag) {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='2'><b>Present Days</b></font></td>";
                print "<td><font face='Verdana' size='2'><b>Absent EXCLUDING Saturdays and Sundays</b></font></td>";
                print "<td><font face='Verdana' size='2'><b>Maternity Leave</b></font></td>";
                print "<td><font face='Verdana' size='2'><b>Sick Leave</b></font></td>";
                print "<td><font face='Verdana' size='2'><b>Annual Leave</b></font></td>";
                print "<td><font face='Verdana' size='2'><b>Casual Leave</b></font></td>";
                print "<td><font face='Verdana' size='2'><b>Night Shifts</b></font></td>";
                print "<td><font face='Verdana' size='2'><b>Week Days Approved OT Hours</b></font></td>";
                print "<td><font face='Verdana' size='2'><b>Total Saturdays</b></font></td>";
                print "<td><font face='Verdana' size='2'><b>Saturday Approved OT Hours</b></font></td>";
                print "<td><font face='Verdana' size='2'><b>Total Sundays</b></font></td>";
                print "<td><font face='Verdana' size='2'><b>Sunday Approved OT Hours</b></font></td>";
                print "<td><font face='Verdana' size='2'><b>Total Public Holidays</b></font></td>";
                print "<td><font face='Verdana' size='2'><b>Public Holiday Approved OT Hours</b></font></td>";
            } else {
                print "Present Days;";
                print "Absent EXCLUDING Saturdays and Sundays;";
                print "Maternity Leave;";
                print "Sick Leave;";
                print "Annual Leave;";
                print "Casual Leave;";
                print "Night Shifts;";
                print "Week Days Approved OT Hours;";
                print "Total Saturdays;";
                print "Saturday Approved OT Hours;";
                print "Total Sundays;";
                print "Sunday Approved OT Hours;";
                print "Total Public Holidays;";
                print "Public Holiday Approved OT Hours;";
            }
        } else {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='2'><b>P</b></font></td>";
                print "<td><font face='Verdana' size='2'><b>A/SS</b></font></td>";
                print "<td><font face='Verdana' size='2'><b>ML</b></font></td>";
                print "<td><font face='Verdana' size='2'><b>SL</b></font></td>";
                print "<td><font face='Verdana' size='2'><b>AL</b></font></td>";
                print "<td><font face='Verdana' size='2'><b>CL</b></font></td>";
                print "<td><font face='Verdana' size='2'><b>NS</b></font></td>";
                print "<td><font face='Verdana' size='2'><b>WKH AO</b></font></td>";
                print "<td><font face='Verdana' size='2'><b>SAT</b></font></td>";
                print "<td><font face='Verdana' size='2'><b>SAT AO</b></font></td>";
                print "<td><font face='Verdana' size='2'><b>SUN</b></font></td>";
                print "<td><font face='Verdana' size='2'><b>SUN AO</b></font></td>";
                print "<td><font face='Verdana' size='2'><b>PH</b></font></td>";
                print "<td><font face='Verdana' size='2'><b>PH AO</b></font></td>";
            } else {
                print "P;";
                print "A/SS;";
                print "ML;";
                print "SL;";
                print "AL;";
                print "CL;";
                print "NS;";
                print "WKH AO;";
                print "SAT;";
                print "SAT AO;";
                print "SUN;";
                print "SUN AO;";
                print "PH;";
                print "PH AO;";
            }
        }
    } else {
        if ($lstReportType != "Custom3" && $lstReportType != "Custom7" && $lstReportType != "Custom9" && $lstReportType != "Custom10") {
            if (strpos($ras, "-V-") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2' color='Violet'><b>";
                }
                if ($tflag) {
                    getFlagTitle($frt, "Violet");
                    print getFlagTitle($frt, "Violet");
                } else {
                    print "V";
                }
                if ($csv != "yes") {
                    print "</b></font></td>";
                } else {
                    print ";";
                }
            }
            if (strpos($ras, "-I-") !== false && $lstReportType != "Custom8") {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2' color='Indigo'><b>";
                }
                if ($tflag) {
                    getFlagTitle($frt, "Indigo");
                    print getFlagTitle($frt, "Indigo");
                } else {
                    print "I";
                }
                if ($csv != "yes") {
                    print "</b></font></td>";
                } else {
                    print ";";
                }
            }
            if (strpos($ras, "-B-") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2' color='Blue'><b>B</b>";
                }
                if ($tflag) {
                    getFlagTitle($frt, "Blue");
                    print getFlagTitle($frt, "Blue");
                } else {
                    print "B";
                }
                if ($csv != "yes") {
                    print "</b></font></td>";
                } else {
                    print ";";
                }
            }
            if (strpos($ras, "-G-") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2' color='Green'><b>";
                }
                if ($tflag) {
                    getFlagTitle($frt, "Green");
                    print getFlagTitle($frt, "Green");
                } else {
                    print "G";
                }
                if ($csv != "yes") {
                    print "</b></font></td>";
                } else {
                    print ";";
                }
            }
            if (strpos($ras, "-Y-") !== false) {
                if ($csv != "yes") {
                    print "<td bgcolor='Brown'><font face='Verdana' size='2' color='Yellow'><b>";
                }
                if ($tflag) {
                    getFlagTitle($frt, "Yellow");
                    print getFlagTitle($frt, "Yellow");
                } else {
                    print "Y";
                }
                if ($csv != "yes") {
                    print "</b></font></td>";
                } else {
                    print ";";
                }
            }
            if (strpos($ras, "-O-") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2' color='Orange'><b>";
                }
                if ($tflag) {
                    getFlagTitle($frt, "Orange");
                    print getFlagTitle($frt, "Orange");
                } else {
                    print "O";
                }
                if ($csv != "yes") {
                    print "</b></font></td>";
                } else {
                    print ";";
                }
            }
            if (strpos($ras, "-R-") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2' color='Red'><b>";
                }
                if ($tflag) {
                    getFlagTitle($frt, "Red");
                    print getFlagTitle($frt, "Red");
                } else {
                    print "R";
                }
                if ($csv != "yes") {
                    print "</b></font></td>";
                } else {
                    print ";";
                }
            }
            if (strpos($ras, "-GR-") !== false && $lstReportType != "Custom8") {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2' color='Gray'><b>";
                }
                if ($tflag) {
                    getFlagTitle($frt, "Gray");
                    print getFlagTitle($frt, "Gray");
                } else {
                    print "GR";
                }
                if ($csv != "yes") {
                    print "</b></font></td>";
                } else {
                    print ";";
                }
            }
            if (strpos($ras, "-BR-") !== false && $lstReportType != "Custom8") {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2' color='Brown'><b>";
                }
                if ($tflag) {
                    getFlagTitle($frt, "Brown");
                    print getFlagTitle($frt, "Brown");
                } else {
                    print "BR";
                }
                if ($csv != "yes") {
                    print "</b></font></td>";
                } else {
                    print ";";
                }
            }
            if (strpos($ras, "-PR-") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2' color='Purple'><b>";
                }
                if ($tflag) {
                    getFlagTitle($frt, "Purple");
                    print getFlagTitle($frt, "Purple");
                } else {
                    print "PR";
                }
                if ($csv != "yes") {
                    print "</b></font></td>";
                } else {
                    print ";";
                }
            }
            if (strpos($ras, "-MG-") !== false && $lstReportType == "Custom8") {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2' color='Magenta'><b>";
                }
                if ($tflag) {
                    getFlagTitle($frt, "Magenta");
                    print getFlagTitle($frt, "Magenta");
                } else {
                    print "MG";
                }
                if ($csv != "yes") {
                    print "</b></font></td>";
                } else {
                    print ";";
                }
            }
            if (strpos($ras, "-TL-") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2' color='Teal'><b>";
                }
                if ($tflag) {
                    getFlagTitle($frt, "Teal");
                    print getFlagTitle($frt, "Teal");
                } else {
                    print "TL";
                }
                if ($csv != "yes") {
                    print "</b></font></td>";
                } else {
                    print ";";
                }
            }
            if (strpos($ras, "-AQ-") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2' color='Aqua'><b>";
                }
                if ($tflag) {
                    getFlagTitle($frt, "Aqua");
                    print getFlagTitle($frt, "Aqua");
                } else {
                    print "AQ";
                }
                if ($csv != "yes") {
                    print "</b></font></td>";
                } else {
                    print ";";
                }
            }
            if (strpos($ras, "-SF-") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2' color='Safron'><b>";
                }
                if ($tflag) {
                    getFlagTitle($frt, "Safron");
                    print getFlagTitle($frt, "Safron");
                } else {
                    print "SF";
                }
                if ($csv != "yes") {
                    print "</b></font></td>";
                } else {
                    print ";";
                }
            }
            if (strpos($ras, "-AM-") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2' color='Amber'><b>";
                }
                if ($tflag) {
                    getFlagTitle($frt, "Amber");
                    print getFlagTitle($frt, "Amber");
                } else {
                    print "AM";
                }
                if ($csv != "yes") {
                    print "</b></font></td>";
                } else {
                    print ";";
                }
            }
            if (strpos($ras, "-GL-") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2' color='Golden'><b>";
                }
                if ($tflag) {
                    getFlagTitle($frt, "Golden");
                    print getFlagTitle($frt, "Golden");
                } else {
                    print "GL";
                }
                if ($csv != "yes") {
                    print "</b></font></td>";
                } else {
                    print ";";
                }
            }
            if (strpos($ras, "-VM-") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2' color='Vermilion'><b>";
                }
                if ($tflag) {
                    getFlagTitle($frt, "Vermilion");
                    print getFlagTitle($frt, "Vermilion");
                } else {
                    print "VM";
                }
                if ($csv != "yes") {
                    print "</b></font></td>";
                } else {
                    print ";";
                }
            }
            if (strpos($ras, "-SL-") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2' color='Silver'><b>";
                }
                if ($tflag) {
                    getFlagTitle($frt, "Silver");
                    print getFlagTitle($frt, "Silver");
                } else {
                    print "SL";
                }
                if ($csv != "yes") {
                    print "</b></font></td>";
                } else {
                    print ";";
                }
            }
            if (strpos($ras, "-MR-") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2' color='Maroon'><b>";
                }
                if ($tflag) {
                    getFlagTitle($frt, "Maroon");
                    print getFlagTitle($frt, "Maroon");
                } else {
                    print "MR";
                }
                if ($csv != "yes") {
                    print "</b></font></td>";
                } else {
                    print ";";
                }
            }
            if (strpos($ras, "-PK-") !== false) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='2' color='Pink'><b>";
                }
                if ($tflag) {
                    getFlagTitle($frt, "Pink");
                    print getFlagTitle($frt, "Pink");
                } else {
                    print "PK";
                }
                if ($csv != "yes") {
                    print "</b></font></td>";
                } else {
                    print ";";
                }
            }
            if (strpos($ras, "-WKD-") !== false && $lstReportType != "Custom8") {
                if ($csv != "yes") {
                    print "<td align='center'><font face='Verdana' size='2'><b>";
                }
                if ($tflag) {
                    print "Week Days";
                } else {
                    print "WKD";
                }
                if ($csv != "yes") {
                    print "</b></font></td>";
                } else {
                    print ";";
                }
            }
            if (strpos($ras, "-PXY-") !== false && $lstReportType != "Custom8") {
                if ($csv != "yes") {
                    print "<td align='center'><font face='Verdana' size='2'><b>";
                }
                if ($tflag) {
                    print "Proxy";
                } else {
                    print "PXY";
                }
                if ($csv != "yes") {
                    print "</b></font></td>";
                } else {
                    print ";";
                }
            }
            if (strpos($ras, "-FLG-") !== false && $lstReportType != "Custom8") {
                if ($csv != "yes") {
                    print "<td align='center'><font face='Verdana' size='2'><b>";
                }
                if ($tflag) {
                    print "Leave/PH Days";
                } else {
                    print "FLG";
                }
                if ($csv != "yes") {
                    print "</b></font></td>";
                } else {
                    print ";";
                }
            }
            if (strpos($ras, "-SAT-") !== false && $lstReportType != "Custom8") {
                if ($csv != "yes") {
                    print "<td align='center'><font face='Verdana' size='2'><b>";
                }
                if ($tflag) {
                    print "Saturdays";
                } else {
//                    print "SAT";
                    print "Weekly OFF 1";
                }
                if ($csv != "yes") {
                    print "</b></font></td>";
                } else {
                    print ";";
                }
            }
            if (strpos($ras, "-SUN-") !== false && $lstReportType != "Custom8") {
                if ($csv != "yes") {
                    print "<td align='center'><font face='Verdana' size='2'><b>";
                }
                if ($tflag) {
                    print "Sundays";
                } else {
//                    print "SUN";
                    print "Weekly OFF 2";
                }
                if ($csv != "yes") {
                    print "</b></font></td>";
                } else {
                    print ";";
                }
            }
            if($lstReportType == "Custom8"){
                if ($csv != "yes") {
//                    print "<td><font face='Verdana' size='1'>Present Days</font></td> <td><font face='Verdana' size='1'>Absent Days</font></td>";
                    print "<td><font face='Verdana' size='2'>Present Days</font></td> <td><font face='Verdana' size='2'>Absent Days</font></td> <td><font face='Verdana' size='2'>A/S</font></td> <td><font face='Verdana' size='2'>A/SS</font></td><td><font face='Verdana' size='2'>WKH-AO</font></td><td><font face='Verdana' size='2'>Weekly OFF 1 Hrs - AO</font></td><td><font face='Verdana' size='2'>Weekly OFF 2 Hrs - AO</font></td>";
                } else {
//                    print "P;A;";
                    print "P;A;A/S;A/SS;WKH-AO;WO1H-AO;WO2H-AO;";
                }
            }
            if (strpos($ras, "-TLD-") !== false && $lstReportType != "Custom8") {
                if ($lstReportType == "Custom2") {
                    if ($csv != "yes") {
                        print "<td colspan='3' align='center' bgcolor='#F0F0F0'><font face='Verdana' size='2'><b>";
                    } else {
                        print ";;";
                    }
                } else {
                    if ($csv != "yes") {
                        if ($lstReportType == "Custom6") {
                            print "<td colspan='6' align='center' bgcolor='#F0F0F0'><font face='Verdana' size='2'><b>";
                        } else {
                            print "<td colspan='5' align='center' bgcolor='#F0F0F0'><font face='Verdana' size='2'><b>";
                        }
                    } else {
                        if ($lstReportType == "Custom6") {
                            print ";;;;;";
                        } else {
                            print ";;;;";
                        }
                    }
                }
                if ($tflag == true || $lstReportType == "Custom2") {
                    print "Total Days";
                } else {
                    
                        print "TLD";
                   
                }
                if ($csv != "yes") {
                    print "</b></font></td>";
                } else {
                    print ";";
                }
            }
            if (strpos($ras, "-NS-") !== false && $lstReportType != "Custom8") {
                if ($csv != "yes") {
                    print "<td align='center'><font face='Verdana' size='2'><b>";
                }
                if ($tflag) {
                    print "Night Shifts";
                } else {
                    print "NS";
                }
                if ($csv != "yes") {
                    print "</b></font></td>";
                } else {
                    print ";";
                }
            }
            if (strpos($ras, "-LI-") !== false) {
                if ($csv != "yes") {
                    print "<td align='center'><font face='Verdana' size='2'><b>";
                }
                if ($tflag) {
                    print "Late In Days";
                } else {
                    print "LI";
                }
                if ($csv != "yes") {
                    print "</b></font></td>";
                } else {
                    print ";";
                }
            }
            if (strpos($ras, "-EO-") !== false) {
                if ($csv != "yes") {
                    print "<td align='center'><font face='Verdana' size='2'><b>";
                }
                if ($tflag) {
                    print "Early Out Days";
                } else {
                    print "EO";
                }
                if ($csv != "yes") {
                    print "</b></font></td>";
                } else {
                    print ";";
                }
            }
            if (strpos($ras, "-MB-") !== false) {
                if ($csv != "yes") {
                    print "<td align='center'><font face='Verdana' size='2'><b>";
                }
                if ($tflag) {
                    print "More Break Days";
                } else {
                    print "MB";
                }
                if ($csv != "yes") {
                    print "</b></font></td>";
                } else {
                    print ";";
                }
            }
            if (strpos($ras, "-WKH-") !== false && $lstReportType != "Custom8") {
                if ($csv != "yes") {
                    print "<td colspan='3' align='center'><font face='Verdana' size='2'><b>";
                } else {
                    print ";;";
                }
                if ($tflag) {
                    print "Week Hrs";
                } else {
                    print "WKH";
                }
                if ($csv != "yes") {
                    print "</b></font></td>";
                } else {
                    print ";";
                }
            }
            if (strpos($ras, "-PXH-") !== false && $lstReportType != "Custom8") {
                if ($csv != "yes") {
                    print "<td colspan='3' align='center'><font face='Verdana' size='2'><b>";
                } else {
                    print ";;";
                }
                if ($tflag) {
                    print "Proxy Hrs";
                } else {
                    print "PXH";
                }
                if ($csv != "yes") {
                    print "</b></font></td>";
                } else {
                    print ";";
                }
            }
            if (strpos($ras, "-WKH+PXH-") !== false) {
                if ($csv != "yes") {
                    print "<td colspan='3' align='center'><font face='Verdana' size='2'><b>";
                } else {
                    print ";;";
                }
                if ($tflag) {
                    print "Week+Proxy";
                } else {
                    print "WKH+PXH";
                }
                if ($csv != "yes") {
                    print "</b></font></td>";
                } else {
                    print ";";
                }
            }
            if (strpos($ras, "-FLH-") !== false && $lstReportType != "Custom8") {
                if ($csv != "yes") {
                    print "<td colspan='3' align='center'><font face='Verdana' size='2'><b>";
                } else {
                    print ";;";
                }
                if ($tflag) {
                    print "Flag Hrs";
                } else {
                    print "FLH";
                }
                if ($csv != "yes") {
                    print "</b></font></td>";
                } else {
                    print ";";
                }
            }
            if (strpos($ras, "-SATH-") !== false && $lstReportType != "Custom8") {
                if ($csv != "yes") {
                    print "<td colspan='3' align='center'><font face='Verdana' size='2'><b>";
                } else {
                    print ";;";
                }
                if ($tflag) {
                    print "Saturday Hrs";
                } else {
//                    print "SATH";
                    print "Weekly OFF 1 Hrs";
                }
                if ($csv != "yes") {
                    print "</b></font></td>";
                } else {
                    print ";";
                }
            }
            if (strpos($ras, "-WKH+PXH+SATH-") !== false) {
                if ($csv != "yes") {
                    print "<td colspan='3' align='center'><font face='Verdana' size='2'><b>";
                } else {
                    print ";;";
                }
                if ($tflag) {
                    print "Week+Proxy+Saturday";
                } else {
                    print "WKH+PXH+SATH";
                }
                if ($csv != "yes") {
                    print "</b></font></td>";
                } else {
                    print ";";
                }
            }
            if (strpos($ras, "-SUNH-") !== false && $lstReportType != "Custom8") {
                if ($csv != "yes") {
                    print "<td colspan='3' align='center'><font face='Verdana' size='2'><b>";
                } else {
                    print ";;";
                }
                if ($tflag) {
                    print "Sunday Hrs";
                } else {
//                    print "SUNH";
                    print "Weekly OFF 2 Hrs";
                }
                if ($csv != "yes") {
                    print "</b></font></td>";
                } else {
                    print ";";
                }
            }
            if (strpos($ras, "-TLH-") !== false && $lstReportType != "Custom8") {
                if ($csv != "yes") {
                    print "<td colspan='3' align='center'><font face='Verdana' size='2'><b>";
                } else {
                    print ";;";
                }
                if ($tflag) {
                    print "Total Hrs";
                } else {
                    print "TLH";
                }
                if ($csv != "yes") {
                    print "</b></font></td>";
                } else {
                    print ";";
                }
            }
            if (strpos($ras, "-NSH-") !== false && $lstReportType != "Custom8") {
                if ($csv != "yes") {
                    print "<td colspan='3' align='center'><font face='Verdana' size='2'><b>";
                } else {
                    print ";;";
                }
                if ($tflag) {
                    print "Night Shift Hrs";
                } else {
                    print "NSH";
                }
                print "</b></font></td>";
            }
            if (strpos($ras, "-LIH-") !== false) {
                if ($csv != "yes") {
                    print "<td align='center'><font face='Verdana' size='2'><b>";
                }
                if ($tflag) {
                    print "Late In Hrs";
                } else {
                    print "LIH";
                }
                if ($csv != "yes") {
                    print "</b></font></td>";
                } else {
                    print ";";
                }
            }
            if (strpos($ras, "-EOH-") !== false) {
                if ($csv != "yes") {
                    print "<td align='center'><font face='Verdana' size='2'><b>";
                }
                if ($tflag) {
                    print "Early Out Hrs";
                } else {
                    print "EOH";
                }
                if ($csv != "yes") {
                    print "</b></font></td>";
                } else {
                    print ";";
                }
            }
            if (strpos($ras, "-MBH-") !== false) {
                if ($csv != "yes") {
                    print "<td align='center'><font face='Verdana' size='2'><b>";
                }
                if ($tflag) {
                    print "More Break Hrs";
                } else {
                    print "MBH";
                }
                if ($csv != "yes") {
                    print "</b></font></td>";
                } else {
                    print ";";
                }
            }
            if ($lstReportType == "Basic/Net") {
                if ($csv != "yes") {
                    print "<td align='center'><font face='Verdana' size='2'><b>Basic</b></font></td>";
                    print "<td align='center'><font face='Verdana' size='2'><b>Net</b></font></td>";
                } else {
                    print "Basic;Net;";
                }
            }
            if ($lstReportType == "Custom5") {
                if ($csv != "yes") {
                    print "<td align='center'><font face='Verdana' size='2'><b>P/A</b></font></td>";
                } else {
                    print "P/A;";
                }
            }
        }
    }
    if ($csv != "yes") {
        print "</tr>";
    } else {
        print "\n";
    }
    if ($lstReportType != "Custom1" && $lstReportType != "Custom3" && $lstReportType != "Custom7" && $lstReportType != "Custom8" && $lstReportType != "Custom9" && $lstReportType != "Custom10") {
        if ($csv != "yes") {
            print "<tr>";
        }
        if ($lstReportType == "Custom2") {
            for ($i = 0; $i < 4; $i++) {
                if ($csv != "yes") {
                    print "<td><font face='Verdana' size='1'>&nbsp;</font></td>";
                } else {
                    print ";";
                }
            }
        } else {
            if ($lstReportType == "Custom5") {
                for ($i = 0; $i < 3; $i++) {
                    if ($csv != "yes") {
                        print "<td><font face='Verdana' size='1'>&nbsp;</font></td>";
                    } else {
                        print ";";
                    }
                }
            } else {
                if ($lstReportType == "Custom6") {
                    for ($i = 0; $i < 3; $i++) {
                        if ($csv != "yes") {
                            print "<td><font face='Verdana' size='1'>&nbsp;</font></td>";
                        } else {
                            print ";";
                        }
                    }
                } else {
                    for ($i = 0; $i < 6; $i++) {
                        if ($csv != "yes") {
                            print "<td><font face='Verdana' size='1'>&nbsp;</font></td>";
                        } else {
                            print ";";
                        }
                    }
                }
            }
        }
//        if($lstReportType == "Custom7"){
//            if (strpos($ras, "-TLH-") !== false) {
//                if ($csv != "yes") {
//                    print " <td><font face='Verdana' size='1'><b>O</b></font></td> <td><font face='Verdana' size='1'><b>AO</b></font></td>";
//                } else {
//                    print "O;AO;";
//                }
//            }
//        }
        if (strpos($ras, "-V-") !== false) {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='1' color='Violet'><b>&nbsp;</b></font></td>";
            } else {
                print ";";
            }
        }
        if (strpos($ras, "-I-") !== false && $lstReportType != "Custom8") {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='1' color='Indigo'><b>&nbsp;</b></font></td>";
            } else {
                print ";";
            }
        }
        if (strpos($ras, "-B-") !== false) {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='1' color='Blue'><b>&nbsp;</b></font></td>";
            } else {
                print ";";
            }
        }
        if (strpos($ras, "-G-") !== false) {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='1' color='Green'><b>&nbsp;</b></font></td>";
            } else {
                print ";";
            }
        }
        if (strpos($ras, "-Y-") !== false) {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='1' color='Yellow'><b>&nbsp;</b></font></td>";
            } else {
                print ";";
            }
        }
        if (strpos($ras, "-O-") !== false) {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='1' color='Orange'><b>&nbsp;</b></font></td>";
            } else {
                print ";";
            }
        }
        if (strpos($ras, "-R-") !== false) {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='1' color='Red'><b>&nbsp;</b></font></td>";
            } else {
                print ";";
            }
        }
        if (strpos($ras, "-GR-") !== false && $lstReportType != "Custom8") {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='1' color='Gray'><b>&nbsp;</b></font></td>";
            } else {
                print ";";
            }
        }
        if (strpos($ras, "-BR-") !== false && $lstReportType != "Custom8") {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='1' color='Brown'><b>&nbsp;</b></font></td>";
            } else {
                print ";";
            }
        }
        if (strpos($ras, "-PR-") !== false) {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='1' color='Purple'><b>&nbsp;</b></font></td>";
            } else {
                print ";";
            }
        }
        if (strpos($ras, "-MG-") !== false && $lstReportType == "Custom8") {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='1' color='Magenta'><b>&nbsp;</b></font></td>";
            } else {
                print ";";
            }
        }
        if (strpos($ras, "-TL-") !== false) {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='1' color='Teal'><b>&nbsp;</b></font></td>";
            } else {
                print ";";
            }
        }
        if (strpos($ras, "-AQ-") !== false) {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='1' color='Aqua'><b>&nbsp;</b></font></td>";
            } else {
                print ";";
            }
        }
        if (strpos($ras, "-SF-") !== false) {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='1' color='Safron'><b>&nbsp;</b></font></td>";
            } else {
                print ";";
            }
        }
        if (strpos($ras, "-AM-") !== false) {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='1' color='Amber'><b>&nbsp;</b></font></td>";
            } else {
                print ";";
            }
        }
        if (strpos($ras, "-GL-") !== false) {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='1' color='Gold'><b>&nbsp;</b></font></td>";
            } else {
                print ";";
            }
        }
        if (strpos($ras, "-VM-") !== false) {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='1' color='Vermillon'><b>&nbsp;</b></font></td>";
            } else {
                print ";";
            }
        }
        if (strpos($ras, "-SL-") !== false) {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='1' color='Silver'><b>&nbsp;</b></font></td>";
            } else {
                print ";";
            }
        }
        if (strpos($ras, "-MR-") !== false) {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='1' color='Maroon'><b>&nbsp;</b></font></td>";
            } else {
                print ";";
            }
        }
        if (strpos($ras, "-PK-") !== false) {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='1' color='Pink'><b>&nbsp;</b></font></td>";
            } else {
                print ";";
            }
        }
        if (strpos($ras, "-WKD-") !== false && $lstReportType != "Custom8") {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='1'><b>&nbsp;</b></font></td>";
            } else {
                print ";";
            }
        }
        if (strpos($ras, "-PXY-") !== false && $lstReportType != "Custom8") {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='1'><b>&nbsp;</b></font></td>";
            } else {
                print ";";
            }
        }
        if (strpos($ras, "-FLG-") !== false && $lstReportType != "Custom8") {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='1'><b>&nbsp;</b></font></td>";
            } else {
                print ";";
            }
        }
        if (strpos($ras, "-SAT-") !== false && $lstReportType != "Custom8") {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='1'><b>&nbsp;</b></font></td>";
            } else {
                print ";";
            }
        }
        if (strpos($ras, "-SUN-") !== false && $lstReportType != "Custom8") {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='1'><b>&nbsp;</b></font></td>";
            } else {
                print ";";
            }
        }
        if (strpos($ras, "-TLD-") !== false) {
            if ($lstReportType == "Custom2") {
                if ($csv != "yes") {
                    print "<td bgcolor='#F0F0F0'><font face='Verdana' size='1'><b>Present</b></font></td> <td bgcolor='#F0F0F0'><font face='Verdana' size='1'><b>Absent</b></font></td> <td bgcolor='#F0F0F0'><font face='Verdana' size='1'><b>Week Days</b></font></td>";
                } else {
                    print "Present;Absent;Week Days;";
                }
            } else {
                if ($csv != "yes") {
                    print "<td bgcolor='#F0F0F0'><font face='Verdana' size='1'><b>P</b></font></td> <td bgcolor='#F0F0F0'><font face='Verdana' size='1'><b>A</b></font></td> <td bgcolor='#F0F0F0'><font face='Verdana' size='1'><b>A/S</b></font></td> <td bgcolor='#F0F0F0'><font face='Verdana' size='1'><b>A/SS</b></font></td>";
                    if ($lstReportType == "Custom6") {
                        print "<td bgcolor='#F0F0F0'><font face='Verdana' size='1'><b>Off Days</b></font></td><td bgcolor='#F0F0F0'><font face='Verdana' size='1'><b>Tot Days</b></font></td>";
                    } else {
                        print "<td bgcolor='#F0F0F0'><font face='Verdana' size='1'><b>WD</b></font></td>";
                    }
                } else {
                    print "P;A;A/S;A/SS;";
                    if ($lstReportType == "Custom6") {
                        print "OFF;TOT;";
                    } else {
                        print "WD;";
                    }
                }
            }
        }
        if (strpos($ras, "-NS-") !== false && $lstReportType != "Custom8") {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='1'><b>&nbsp;</b></font></td>";
            } else {
                print ";";
            }
        }
        if (strpos($ras, "-LI-") !== false) {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='1'><b>&nbsp;</b></font></td>";
            } else {
                print ";";
            }
        }
        if (strpos($ras, "-EO-") !== false) {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='1'><b>&nbsp;</b></font></td>";
            } else {
                print ";";
            }
        }
        if (strpos($ras, "-WKH-") !== false && $lstReportType != "Custom8") {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='1'><b>N</b></font></td> <td><font face='Verdana' size='1'><b>O</b></font></td> <td><font face='Verdana' size='1'><b>AO</b></font></td>";
            } else {
                print "N;O;AO;";
            }
        }
        if (strpos($ras, "-PXH-") !== false && $lstReportType != "Custom8") {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='1'><b>N</b></font></td> <td><font face='Verdana' size='1'><b>O</b></font></td> <td><font face='Verdana' size='1'><b>AO</b></font></td>";
            } else {
                print "N;O;AO;";
            }
        }
        if (strpos($ras, "-WKH+PXH-") !== false) {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='1'><b>N</b></font></td> <td><font face='Verdana' size='1'><b>O</b></font></td> <td><font face='Verdana' size='1'><b>AO</b></font></td>";
            } else {
                print "N;O;AO;";
            }
        }
        if (strpos($ras, "-FLH-") !== false && $lstReportType != "Custom8") {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='1'><b>N</b></font></td> <td><font face='Verdana' size='1'><b>O</b></font></td> <td><font face='Verdana' size='1'><b>AO</b></font></td>";
            } else {
                print "N;O;AO;";
            }
        }
        if (strpos($ras, "-SATH-") !== false) {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='1'><b>N</b></font></td> <td><font face='Verdana' size='1'><b>O</b></font></td> <td><font face='Verdana' size='1'><b>AO</b></font></td>";
            } else {
                print "N;O;AO;";
            }
        }
        if (strpos($ras, "-WKH+PXH+SATH-") !== false) {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='1'><b>N</b></font></td> <td><font face='Verdana' size='1'><b>O</b></font></td> <td><font face='Verdana' size='1'><b>AO</b></font></td>";
            } else {
                print "N;O;AO;";
            }
        }
        if (strpos($ras, "-SUNH-") !== false && $lstReportType != "Custom8") {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='1'><b>N</b></font></td> <td><font face='Verdana' size='1'><b>O</b></font></td> <td><font face='Verdana' size='1'><b>AO</b></font></td>";
            } else {
                print "N;O;AO;";
            }
        }
        if (strpos($ras, "-TLH-") !== false && $lstReportType != "Custom8") {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='1'><b>N</b></font></td> <td><font face='Verdana' size='1'><b>O</b></font></td> <td><font face='Verdana' size='1'><b>AO</b></font></td>";
            } else {
                print "N;O;AO;";
            }
        }
        if (strpos($ras, "-NSH-") !== false && $lstReportType != "Custom8") {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='1'><b>N</b></font></td> <td><font face='Verdana' size='1'><b>O</b></font></td> <td><font face='Verdana' size='1'><b>AO</b></font></td>";
            } else {
                print "N;O;AO;";
            }
        }
        if (strpos($ras, "-LIH-") !== false) {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='1'><b>&nbsp;</b></font></td>";
            } else {
                print ";";
            }
        }
        if (strpos($ras, "-EOH-") !== false) {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='1'><b>&nbsp;</b></font></td>";
            } else {
                print ";";
            }
        }
        if (strpos($ras, "-MBH-") !== false) {
            if ($csv != "yes") {
                print "<td><font face='Verdana' size='1'><b>&nbsp;</b></font></td>";
            } else {
                print ";";
            }
        }
        if ($lstReportType == "Basic/Net") {
            if ($csv != "yes") {
                print "<td align='center'><font face='Verdana' size='1'><b>&nbsp;</b></font></td>";
                print "<td align='center'><font face='Verdana' size='1'><b>&nbsp;</b></font></td>";
            } else {
                print ";;";
            }
        }
        if ($lstReportType == "Custom5") {
            if ($csv != "yes") {
                print "<td align='center'><font face='Verdana' size='1'><b>&nbsp;</b></font></td>";
            } else {
                print ";";
            }
        }
        if ($csv != "yes") {
            print "</tr>";
        } else {
            print "\n";
        }
    }
    $row_count = 0;
    $t_no_days = 0;
    $t_no_abdays = 0;
    $t_green = 0;
    $t_red = 0;
    $t_blue = 0;
    $t_gray = 0;
    $t_ns = 0;
    $t_wkh = 0;
    $t_sat = 0;
    $t_sath = 0;
    $t_sun = 0;
    $t_sunh = 0;
    $t_ph = 0;
    $t_phh = 0;
//    if ($lstReportType == "Custom7") {
//        if ($csv != "yes") {
//                print "<tr><td>";
//            }
//            if ($prints != "yes") {
//                print "<a title='ID: Click to view Clocking Details for the selected Period (ACTIVE EMPLOYEES ONLY)' href='ReportDailyRoster.php?act=searchRecord&prints=yes&excel=no&subReport=yes&lstEmployeeStatus=" . $cur[2] . "&lstEmployeeID=" . $cur[0] . "&txtFrom=" . $txtFrom . "&txtTo=" . $txtTo . "&lstDB=" . $lstDB . "' target='_blank'>";
//            }
//            if ($csv != "yes") {
//                print "</tr>";
//            } else {
//                print "\n";
//            }
//    }
    if ($lstReportType == "Custom1") {
        for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $row_count++) {
            if ($csv != "yes") {
                print "<tr><td>";
            }
            if ($prints != "yes") {
                print "<a title='ID: Click to view Clocking Details for the selected Period (ACTIVE EMPLOYEES ONLY)' href='ReportDailyRoster.php?act=searchRecord&prints=yes&excel=no&subReport=yes&lstEmployeeStatus=" . $cur[2] . "&lstEmployeeID=" . $cur[0] . "&txtFrom=" . $txtFrom . "&txtTo=" . $txtTo . "&lstDB=" . $lstDB . "' target='_blank'>";
            }
            if ($csv != "yes") {
                addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
                print "<font face='Verdana' size='1' color='#000000'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>";
            } else {
                addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
                print addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . ";";
            }
            if ($cur[0] == $cur[1] && strpos($userlevel, "11D") !== false) {
                $pax_query = "SELECT N1, N2, N3, N4, N5, N6, N7, N8, N9, N0 FROM PAX WHERE id = " . $cur[0] * 7;
                $pax_result = selectData($conn, $pax_query);
                if ($pax_result[0] != "") {
                    for ($pax_i = 0; $pax_i < 10; $pax_i++) {
                        strrev($pax_result[$pax_i]);
                        print strrev($pax_result[$pax_i]);
                    }
                } else {
                    print $cur[1];
                }
            } else {
                print $cur[1];
            }
            if ($csv != "yes") {
                print "</font></a></td> <td><font face='Verdana' size='1'>" . $cur[4] . "</font></a></td>";
            } else {
                print $cur[4] . ";";
            }
            $no_days = 0;
            $sub_query = "SELECT COUNT(AttendanceID) FROM " . $table_name . ", tuser WHERE " . $table_name . ".EmployeeID = tuser.id AND EmployeeID = " . $cur[0] . " AND ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND ((tuser.Remark = 'SNR' AND (Flag = 'Black' OR Flag = 'Proxy' OR Flag = 'Purple' OR Flag = 'Aqua' OR Flag = 'Indigo')) OR (tuser.Remark = 'JNR' AND (Flag = 'Black' OR Flag = 'Proxy' OR Flag = 'Purple' OR Flag = 'Indigo'))) ";
            $sub_result = selectData($conn, $sub_query);
            $no_days = $sub_result[0];
            $proxy_ph = 0;
            $sub_query = "SELECT COUNT(AttendanceID) FROM " . $table_name . " WHERE EmployeeID = " . $cur[0] . " AND ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND Flag = 'Proxy' AND ADate IN (SELECT OTDate FROM OTDate) ";
            $sub_result = selectData($conn, $sub_query);
            $proxy_ph = $sub_result[0];
            $no_days = $no_days - $proxy_ph;
            if ($no_days < 0) {
                $no_days = 0;
            }
            print "<td><a title='Present Days'><font face='Verdana' size='1'>" . $no_days . "</font></a></td>";
            $t_no_days = $t_no_days + $no_days;
            $sub_query = "SELECT COUNT(AttendanceID) FROM " . $table_name . ", tuser WHERE EmployeeID = id AND EmployeeID = " . $cur[0] . " AND ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND " . $table_name . ".Day = " . $table_name . ".OT1 AND AOvertime > 0 ";
            $sub_result = selectData($conn, $sub_query);
            $poff_prsnt = $sub_result[0];
            $sub_query = "SELECT COUNT(AttendanceID) FROM " . $table_name . ", tuser WHERE EmployeeID = id AND EmployeeID = " . $cur[0] . " AND ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND " . $table_name . ".Day = " . $table_name . ".OT2 AND AOvertime > 0 ";
            $sub_result = selectData($conn, $sub_query);
            $off_prsnt = $sub_result[0];
            $sub_query = "SELECT COUNT(FlagDayRotationID) FROM FlagDayRotation WHERE e_id = " . $cur[0] . " AND e_date >= " . insertDate($txtFrom) . " AND e_date <= " . insertDate($txtTo) . " AND OT = 'OT1' ";
            $sub_result = selectData($conn, $sub_query);
            $actual_preoff = $sub_result[0];
            $sub_query = "SELECT COUNT(FlagDayRotationID) FROM FlagDayRotation WHERE e_id = " . $cur[0] . " AND e_date >= " . insertDate($txtFrom) . " AND e_date <= " . insertDate($txtTo) . " AND OT = 'OT2' ";
            $sub_result = selectData($conn, $sub_query);
            $actual_off = $sub_result[0];
            $no_abdays = 0;
            $cur[5] = substr($cur[5], 1, 8);
            if ($cur[5] < insertDate($txtFrom)) {
                $cur[5] = insertDate($txtFrom);
            }
            if (stripos($cur[3], "Y") !== false) {
                $sub_query = "SELECT COUNT(AttendanceID) FROM " . $table_name . " WHERE EmployeeID = " . $cur[0] . " AND ADate >= " . $cur[5] . " AND ADate <= " . insertDate($txtTo) . " ";
                $sub_result = selectData($conn, $sub_query);
                $totalDays = $sub_result[0];
                $no_abdays = $dayCount - $totalDays - $proxy_ph;
                $sub_query = "SELECT COUNT(AttendanceID) FROM " . $table_name . ", FlagDayRotation WHERE " . $table_name . ".EmployeeID = FlagDayRotation.e_id AND " . $table_name . ".EmployeeID = " . $cur[0] . " AND " . $table_name . ".ADate = FlagDayRotation.e_date AND " . $table_name . ".ADate >= " . $cur[5] . " AND " . $table_name . ".ADate <= " . insertDate($txtTo) . " AND " . $table_name . ".group_id = 2 AND " . $table_name . ".Flag = 'Proxy' ";
                $sub_result = selectData($conn, $sub_query);
                $no_abdays = $no_abdays - ($actual_preoff + $actual_off - $sub_result[0] - ($poff_prsnt + $off_prsnt));
                if ($dayCount - $totalDays < $no_abdays) {
                    $no_abdays = $dayCount - $totalDays;
                }
                if ($no_abdays < 0) {
                    $no_abdays = 0;
                }
            } else {
                $no_abdays = getASS($conn, $cur[0], displayDate($cur[5]), $txtTo);
            }
            if ($csv != "yes") {
                print "<td><a title='Absent Days'><font face='Verdana' size='1'>" . $no_abdays . "</font></a></td>";
            } else {
                print $no_abdays . ";";
            }
            $t_no_abdays = $t_no_abdays + $no_abdays;
            $sub_query = "SELECT COUNT(AttendanceID) FROM " . $table_name . " WHERE EmployeeID = " . $cur[0] . " AND ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND Flag = 'Green' ";
            $sub_result = selectData($conn, $sub_query);
            if ($csv != "yes") {
                print "<td><a title='Maternity Leave'><font face='Verdana' size='1'>" . $sub_result[0] . "</font></a></td>";
            } else {
                print $sub_result[0] . ";";
            }
            $t_green = $t_green + $sub_result[0];
            $sub_query = "SELECT COUNT(AttendanceID) FROM " . $table_name . " WHERE EmployeeID = " . $cur[0] . " AND ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND Flag = 'Red' ";
            $sub_result = selectData($conn, $sub_query);
            if ($csv != "yes") {
                print "<td><a title='Sick Leave'><font face='Verdana' size='1'>" . $sub_result[0] . "</font></a></td>";
            } else {
                print $sub_result[0] . ";";
            }
            $t_red = $t_red + $sub_result[0];
            $sub_query = "SELECT COUNT(AttendanceID) FROM " . $table_name . " WHERE EmployeeID = " . $cur[0] . " AND ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND Flag = 'Blue' ";
            $sub_result = selectData($conn, $sub_query);
            if ($csv != "yes") {
                print "<td><a title='Annual Leave'><font face='Verdana' size='1'>" . $sub_result[0] . "</font></a></td>";
            } else {
                print $sub_result[0] . ";";
            }
            $t_blue = $t_blue + $sub_result[0];
            $sub_query = "SELECT COUNT(AttendanceID) FROM " . $table_name . " WHERE EmployeeID = " . $cur[0] . " AND ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND Flag = 'Gray' ";
            $sub_result = selectData($conn, $sub_query);
            if ($csv != "yes") {
                print "<td><a title='Casual Leave'><font face='Verdana' size='1'>" . $sub_result[0] . "</font></a></td>";
            } else {
                print $sub_result[0] . ";";
            }
            $t_gray = $t_gray + $sub_result[0];
            $sub_query = "SELECT COUNT(AttendanceID) FROM " . $table_name . " WHERE EmployeeID = " . $cur[0] . " AND ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND NightFlag = 1 AND Flag <> 'Aqua' ";
            $sub_result = selectData($conn, $sub_query);
            if ($csv != "yes") {
                print "<td><a title='Night Shift'><font face='Verdana' size='1'>" . $sub_result[0] . "</font></a></td>";
            } else {
                print $sub_result[0] . ";";
            }
            $t_ns = $t_ns + $sub_result[0];
            $sub_query = "SELECT SUM(AOvertime) FROM " . $table_name . ", tuser WHERE " . $table_name . ".EmployeeID = tuser.id AND EmployeeID = " . $cur[0] . " AND ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND " . $table_name . ".Day <> " . $table_name . ".OT1 AND " . $table_name . ".Day <> " . $table_name . ".OT2 AND " . $table_name . ".Flag <> 'Purple' AND (tuser.Remark = 'SNR' OR (tuser.Remark = 'JNR' AND " . $table_name . ".Flag <> 'Aqua')) ";
            $sub_result = selectData($conn, $sub_query);
            if ($csv != "yes") {
                addComma($sub_result[0] / 3600, 2);
                print "<td><a title='WKH AO'><font face='Verdana' size='1'>" . addComma($sub_result[0] / 3600, 2) . "</font></a></td>";
            } else {
                addComma($sub_result[0] / 3600, 2);
                print addComma($sub_result[0] / 3600, 2) . ";";
            }
            $t_wkh = $t_wkh + $sub_result[0];
            $sub_query = "SELECT COUNT(AOvertime) FROM " . $table_name . " WHERE EmployeeID = " . $cur[0] . " AND ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND Day = OT1 AND AOvertime > 0 ";
            $sub_result = selectData($conn, $sub_query);
            if ($csv != "yes") {
                print "<td><a title='SAT'><font face='Verdana' size='1'>" . $sub_result[0] . "</font></a></td>";
            } else {
                print $sub_result[0] . ";";
            }
            $t_sat = $t_sat + $sub_result[0];
            $sub_query = "SELECT SUM(AOvertime) FROM " . $table_name . " WHERE EmployeeID = " . $cur[0] . " AND ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND Day = OT1 ";
            $sub_result = selectData($conn, $sub_query);
            if ($csv != "yes") {
                addComma($sub_result[0] / 3600, 2);
                print "<td><a title='SAT AO'><font face='Verdana' size='1'>" . addComma($sub_result[0] / 3600, 2) . "</font></a></td>";
            } else {
                addComma($sub_result[0] / 3600, 2);
                print addComma($sub_result[0] / 3600, 2) . ";";
            }
            $t_sath = $t_sath + $sub_result[0];
            $sub_query = "SELECT COUNT(AOvertime) FROM " . $table_name . " WHERE EmployeeID = " . $cur[0] . " AND ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND Day = OT2 AND AOvertime > 0 ";
            $sub_result = selectData($conn, $sub_query);
            if ($csv != "yes") {
                print "<td><a title='SUN'><font face='Verdana' size='1'>" . $sub_result[0] . "</font></a></td>";
            } else {
                print $sub_result[0] . ";";
            }
            $t_sun = $t_sun + $sub_result[0];
            $sub_query = "SELECT SUM(AOvertime) FROM " . $table_name . " WHERE EmployeeID = " . $cur[0] . " AND ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND Day = OT2 ";
            $sub_result = selectData($conn, $sub_query);
            if ($csv != "yes") {
                addComma($sub_result[0] / 3600, 2);
                print "<td><a title='SUN AO'><font face='Verdana' size='1'>" . addComma($sub_result[0] / 3600, 2) . "</font></a></td>";
            } else {
                addComma($sub_result[0] / 3600, 2);
                print addComma($sub_result[0] / 3600, 2) . ";";
            }
            $t_sunh = $t_sunh + $sub_result[0];
            $sub_query = "SELECT COUNT(AOvertime) FROM " . $table_name . " WHERE EmployeeID = " . $cur[0] . " AND ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND Flag = 'Purple' AND AOvertime > 0 ";
            $sub_result = selectData($conn, $sub_query);
            if ($csv != "yes") {
                print "<td><a title='PH'><font face='Verdana' size='1'>" . $sub_result[0] . "</font></a></td>";
            } else {
                print $sub_result[0] . ";";
            }
            $t_ph = $t_ph + $sub_result[0];
            $sub_query = "SELECT SUM(AOvertime) FROM " . $table_name . " WHERE EmployeeID = " . $cur[0] . " AND ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND Flag = 'Purple' ";
            $sub_result = selectData($conn, $sub_query);
            if ($csv != "yes") {
                addComma($sub_result[0] / 3600, 2);
                print "<td><a title='PH AO'><font face='Verdana' size='1'>" . addComma($sub_result[0] / 3600, 2) . "</font></a></td>";
            } else {
                addComma($sub_result[0] / 3600, 2);
                print addComma($sub_result[0] / 3600, 2) . ";";
            }
            $t_phh = $t_phh + $sub_result[0];
            if ($csv != "yes") {
                print "</tr>";
            } else {
                print "\n";
            }
        }
    } else {
        if ($lstReportType == "Custom3") {
            $count = 0;
            $subc = 0;
            $eid = "";
            $result = mysqli_query($conn, $query);
            while ($cur = mysqli_fetch_row($result)) {
                if ($eid != $cur[0]) {
                    if ($csv != "yes") {
                        print "<tr>";
                    }
                    if (getRegister($txtMACAddress, 7) == "123") {
                        if ($csv != "yes") {
                            print "<td><font face='Verdana' size='1' color='#000000'>UNIKEM</font></td>";
                        } else {
                            print "UNIKEM;";
                        }
                    } else {
                        if ($csv != "yes") {
                            print "<td><font face='Verdana' size='1' color='#000000'>" . $cur[3] . "</font></td>";
                        } else {
                            print $cur[3] . ";";
                        }
                    }
                    if ($prints != "yes") {
                        print "<a title='ID: Click to view Clocking Details for the selected Period (ACTIVE EMPLOYEES ONLY)' href='ReportDailyRoster.php?act=searchRecord&prints=yes&excel=no&subReport=yes&lstEmployeeStatus=" . $cur[2] . "&lstEmployeeID=" . $cur[0] . "&txtFrom=" . $txtFrom . "&txtTo=" . $txtTo . "&lstDB=" . $lstDB . "' target='_blank'>";
                    }
                    if ($csv != "yes") {
                        addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
                        print "<font face='Verdana' size='1' color='#000000'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td>";
                        print "<td><a title='Name'><font face='Verdana' size='1'>" . $cur[1] . "</font></a></td>";
                    } else {
                        addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
                        print addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . ";" . $cur[1] . ";";
                    }
                    if (getRegister($txtMACAddress, 7) == "123") {
                        if ($csv != "yes") {
                            print "<td><font face='Verdana' size='1' color='#000000'>UNIKEM</font></td>";
                        } else {
                            print "UNIKEM;";
                        }
                    } else {
                        if ($csv != "yes") {
                            print "<td><font face='Verdana' size='1' color='#000000'>" . $cur[3] . "</font></td>";
                        } else {
                            print $cur[3] . ";";
                        }
                    }
                    if ($csv != "yes") {
                        print "<td><font face='Verdana' size='1'>Normal</font></td>";
                        substr($txtTo, 6, 4);
                        substr($txtTo, 3, 2);
                        substr($txtTo, 0, 2);
                        print "<td><font face='Verdana' size='1'>" . substr($txtTo, 6, 4) . "/" . substr($txtTo, 3, 2) . "/" . substr($txtTo, 0, 2) . "</font></td>";
                        print "<td><font face='Verdana' size='1'>MAIN</font></td>";
                    } else {
                        print "Normal;";
                        substr($txtTo, 6, 4);
                        substr($txtTo, 3, 2);
                        substr($txtTo, 0, 2);
                        print substr($txtTo, 6, 4) . "/" . substr($txtTo, 3, 2) . "/" . substr($txtTo, 0, 2) . ";";
                        print "MAIN;";
                    }
                    $sub_query = "SELECT SUM(AOvertime) FROM " . $table_name . " WHERE EmployeeID = " . $cur[0] . " AND ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND Day <> OT1 AND Day <> OT2 AND Flag <> 'Purple' ";
                    $sub_result = selectData($conn, $sub_query);
                    if ($csv != "yes") {
                        round($sub_result[0] / 3600, 2);
                        print "<td><font face='Verdana' size='1'>" . round($sub_result[0] / 3600, 2) . "</font></td>";
                    } else {
                        round($sub_result[0] / 3600, 2);
                        print round($sub_result[0] / 3600, 2) . ";";
                    }
                    $sub_query = "SELECT SUM(AOvertime) FROM " . $table_name . " WHERE EmployeeID = " . $cur[0] . " AND ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND Day = OT1 AND Flag <> 'Purple' ";
                    $sub_result = selectData($conn, $sub_query);
                    if ($csv != "yes") {
                        round($sub_result[0] / 3600, 2);
                        print "<td><font face='Verdana' size='1'>" . round($sub_result[0] / 3600, 2) . "</font></td>";
                    } else {
                        round($sub_result[0] / 3600, 2);
                        print round($sub_result[0] / 3600, 2) . ";";
                    }
                    $sub_query = "SELECT SUM(AOvertime) FROM " . $table_name . " WHERE EmployeeID = " . $cur[0] . " AND ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND (Flag = 'Purple' OR Day = OT2)";
                    $sub_result = selectData($conn, $sub_query);
                    if ($csv != "yes") {
                        round($sub_result[0] / 3600, 2);
                        print "<td><font face='Verdana' size='1'>" . round($sub_result[0] / 3600, 2) . "</font></td>";
                    } else {
                        round($sub_result[0] / 3600, 2);
                        print round($sub_result[0] / 3600, 2) . ";";
                    }
                    $sub_query = "SELECT SUM(Normal) FROM " . $table_name . " WHERE EmployeeID = " . $cur[0] . " AND ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo);
                    $sub_result = selectData($conn, $sub_query);
                    if ($csv != "yes") {
                        round($sub_result[0] / 3600, 2);
                        print "<td><font face='Verdana' size='1'>" . round($sub_result[0] / 3600, 2) . "</font></td>";
                    } else {
                        round($sub_result[0] / 3600, 2);
                        print round($sub_result[0] / 3600, 2) . ";";
                    }
                    $sub_query = "SELECT COUNT(AttendanceID) FROM " . $table_name . " WHERE EmployeeID = " . $cur[0] . " AND ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND Flag = (SELECT Flag FROM FlagTitle WHERE Title = 'AL')";
                    $sub_result = selectData($conn, $sub_query);
                    if ($csv != "yes") {
                        print "<td><font face='Verdana' size='1'>" . $sub_result[0] . "</font></td>";
                    } else {
                        print $sub_result[0] . ";";
                    }
                    $sub_query = "SELECT COUNT(AttendanceID) FROM " . $table_name . " WHERE EmployeeID = " . $cur[0] . " AND ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND Flag = (SELECT Flag FROM FlagTitle WHERE Title = 'ML')";
                    $sub_result = selectData($conn, $sub_query);
                    if ($csv != "yes") {
                        print "<td><font face='Verdana' size='1'>" . $sub_result[0] . "</font></td>";
                    } else {
                        print $sub_result[0] . ";";
                    }
                    $sub_query = "SELECT COUNT(AttendanceID) FROM " . $table_name . " WHERE EmployeeID = " . $cur[0] . " AND ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " AND Flag = (SELECT Flag FROM FlagTitle WHERE Title = 'SL')";
                    $sub_result = selectData($conn, $sub_query);
                    if ($csv != "yes") {
                        print "<td><font face='Verdana' size='1'>" . $sub_result[0] . "</font></td>";
                    } else {
                        print $sub_result[0] . ";";
                    }
                    $sub_query = "SELECT COUNT(AttendanceID) FROM " . $table_name . " WHERE EmployeeID = " . $cur[0] . " AND ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo);
                    $sub_result = selectData($conn, $sub_query);
                    if ($csv != "yes") {
                        print "<td><font face='Verdana' size='1'>" . $sub_result[0] . "</font></td>";
                    } else {
                        print $sub_result[0] . ";";
                    }
                    $dayCount = getTotalDays($txtFrom, $txtTo);
                    if ($csv != "yes") {
                        getDayCount(insertDate($txtFrom), insertDate($txtTo), $dayCount, "Saturday");
                        getDayCount(insertDate($txtFrom), insertDate($txtTo), $dayCount, "Sunday");
                        print "<td><font face='Verdana' size='1'>" . ($dayCount - getDayCount(insertDate($txtFrom), insertDate($txtTo), $dayCount, "Saturday") - getDayCount(insertDate($txtFrom), insertDate($txtTo), $dayCount, "Sunday")) . "</font></td>";
                    } else {
                        getDayCount(insertDate($txtFrom), insertDate($txtTo), $dayCount, "Saturday");
                        getDayCount(insertDate($txtFrom), insertDate($txtTo), $dayCount, "Sunday");
                        print $dayCount - getDayCount(insertDate($txtFrom), insertDate($txtTo), $dayCount, "Saturday") - getDayCount(insertDate($txtFrom), insertDate($txtTo), $dayCount, "Sunday") . ";";
                    }
                    if ($csv != "yes") {
                        print "</tr>";
                    } else {
                        print "\n";
                    }
                    $row_count++;
                    $eid = $cur[0];
                }
            }
        } else {
            $count = 0;
            $subc = 0;
            $eid = "";
            $reg_day = 0;
            $wkdn = 0;
            $wkdo = 0;
            $pxyn = 0;
            $pxyo = 0;
            $flgn = 0;
            $flgo = 0;
            $satn = 0;
            $sato = 0;
            $sunn = 0;
            $suno = 0;
            $nsn = 0;
            $nso = 0;
            $li = 0;
            $eo = 0;
            $mb = 0;
            $tld = 0;
            $violet = 0;
            $indigo = 0;
            $blue = 0;
            $green = 0;
            $yellow = 0;
            $orange = 0;
            $red = 0;
            $gray = 0;
            $brown = 0;
            $purple = 0;
            $magenta = 0;
            $teal = 0;
            $aqua = 0;
            $safron = 0;
            $amber = 0;
            $golden = 0;
            $vermilion = 0;
            $silver = 0;
            $maroon = 0;
            $pink = 0;
            $black = 0;
            $h_wkdn = 0;
            $h_wkdo = 0;
            $h_wkdao = 0;
            $h_pxyn = 0;
            $h_pxyo = 0;
            $h_pxyao = 0;
            $h_flgn = 0;
            $h_flgo = 0;
            $h_flgao = 0;
            $h_satn = 0;
            $h_sato = 0;
            $h_satao = 0;
            $h_sunn = 0;
            $h_suno = 0;
            $h_sunao = 0;
            $h_nsn = 0;
            $h_nso = 0;
            $h_nsao = 0;
            $h_li = 0;
            $h_eo = 0;
            $h_mb = 0;
            $h_satabn = 0;
            $h_satabo = 0;
            $h_sunabn = 0;
            $h_sunabo = 0;
            $txtDate = insertDate($txtFrom);
            $txtLastDate = $txtDate;
            $data9 = "";
            $late_days = 0;
            $sunCount = 0;
            $sunFlag = false;
            $sunArray = "";
            $satCount = 0;
            $satFlag = false;
            $satArray = "";
            $result = mysqli_query($conn, $query);
            while ($cur = mysqli_fetch_row($result)) {
                if ($cur[3] == "") {
                    $cur[3] = "&nbsp;";
                }
                if ($cur[10] == "") {
                    $cur[10] = "&nbsp;";
                }
                if ($cur[11] == "") {
                    $cur[11] = "&nbsp;";
                }
                if ($eid != $cur[0]) {
                    $sunFlag = false;
                    $satFlag = false;
//                    if ($lstReportType == "Custom9") {
//                            if ($csv != "yes") {
//                                print "<td><a title='Total Violet Days'><font face='Verdana' size='1' color='Violet'>" . $violet . "</font></a></td><td><a title='Total Blue Days'><font face='Verdana' size='1' color='Blue'>" . $blue . "</font></a></td><td><a title='Total Orange Days'><font face='Verdana' size='1' color='Orange'>" . $orange . "</font></a></td><td><a title='Total Green Days'><font face='Verdana' size='1' color='Green'>" . $green . "</font></a></td>";
//                            } else {
//                                print $violet . ";".$blue . ";".$orange.";".$green.";";
//                            }
//                        }
                    
                    if ($count != 0) {
                        
                        if (strpos($ras, "-V-") !== false && $lstReportType != "Custom7") {
                            if ($csv != "yes") {
                                print "<td><a title='Total Violet Days'><font face='Verdana' size='1' color='Violet'>" . $violet . "</font></a></td>";
                            } else {
                                print $violet . ";";
                            }
                        }
                        if (strpos($ras, "-I-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom8" && $lstReportType != "Custom9") {
                            if ($csv != "yes") {
                                print "<td><a title='Total Indigo Days'><font face='Verdana' size='1' color='Indigo'>" . $indigo . "</font></a></td>";
                            } else {
                                print $indigo . ";";
                            }
                        }
                        if (strpos($ras, "-B-") !== false && $lstReportType != "Custom7") {
                            if ($csv != "yes") {
                                print "<td><a title='Total Blue Days'><font face='Verdana' size='1' color='Blue'>" . $blue . "</font></a></td>";
                            } else {
                                print $blue . ";";
                            }
                        }
                        if (strpos($ras, "-G-") !== false && $lstReportType != "Custom7") {
                            if ($csv != "yes") {
                                print "<td><a title='Total Green Days'><font face='Verdana' size='1' color='Green'>" . $green . "</font></a></td>";
                            } else {
                                print $green . ";";
                            }
                        }
                        if (strpos($ras, "-Y-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom9") {
                            if ($csv != "yes") {
                                print "<td bgcolor='Brown'><a title='Total Yellow Days'><font face='Verdana' size='1' color='Yellow'>" . $yellow . "</font></a></td>";
                            } else {
                                print $yellow . ";";
                            }
                        }
                        if (strpos($ras, "-O-") !== false && $lstReportType != "Custom7") {
                            if ($csv != "yes") {
                                print "<td><a title='Total Orange Days'><font face='Verdana' size='1' color='Orange'>" . $orange . "</font></a></td>";
                            } else {
                                print $orange . ";";
                            }
                        }
                        if (strpos($ras, "-R-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom9") {
                            if ($csv != "yes") {
                                print "<td><a title='Total Red Days'><font face='Verdana' size='1' color='Red'>" . $red . "</font></a></td>";
                            } else {
                                print $red . ";";
                            }
                        }
                        if (strpos($ras, "-GR-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom8" && $lstReportType != "Custom9") {
                            if ($csv != "yes") {
                                print "<td><a title='Total Green Days'><font face='Verdana' size='1' color='Gray'>" . $gray . "</font></a></td>";
                            } else {
                                print $gray . ";";
                            }
                        }
                        if (strpos($ras, "-BR-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom8" && $lstReportType != "Custom9") {
                            if ($csv != "yes") {
                                print "<td><a title='Total Brown Days'><font face='Verdana' size='1' color='Brown'>" . $brown . "</font></a></td>";
                            } else {
                                print $brown . ";";
                            }
                        }
                        if (strpos($ras, "-PR-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom9") {
                            if ($csv != "yes") {
                                print "<td><a title='Total Purple Days'><font face='Verdana' size='1' color='Purple'>" . $purple . "</font></a></td>";
                            } else {
                                print $purple . ";";
                            }
                        }
                        if (strpos($ras, "-MG-") !== false && $lstReportType != "Custom7") {
                            if ($csv != "yes") {
                                print "<td><a title='Total Magenta Days'><font face='Verdana' size='1' color='Magenta'>" . $magenta . "</font></a></td>";
                            } else {
                                print $magenta . ";";
                            }
                        }
                        if (strpos($ras, "-TL-") !== false && $lstReportType != "Custom7") {
                            if ($csv != "yes") {
                                print "<td><a title='Total Teal Days'><font face='Verdana' size='1' color='Teal'>" . $teal . "</font></a></td>";
                            } else {
                                print $teal . ";";
                            }
                        }
                        if (strpos($ras, "-AQ-") !== false && $lstReportType != "Custom7") {
                            if ($csv != "yes") {
                                print "<td><a title='Total Aqua Days'><font face='Verdana' size='1' color='Aqua'>" . $aqua . "</font></a></td>";
                            } else {
                                print $aqua . ";";
                            }
                        }
                        if (strpos($ras, "-SF-") !== false && $lstReportType != "Custom7") {
                            if ($csv != "yes") {
                                print "<td><a title='Total Safron Days'><font face='Verdana' size='1' color='Safron'>" . $safron . "</font></a></td>";
                            } else {
                                print $safron . ";";
                            }
                        }
                        if (strpos($ras, "-AM-") !== false && $lstReportType != "Custom7") {
                            if ($csv != "yes") {
                                print "<td><a title='Total Amber Days'><font face='Verdana' size='1' color='Amber'>" . $amber . "</font></a></td>";
                            } else {
                                print $amber . ";";
                            }
                        }
                        if (strpos($ras, "-GL-") !== false && $lstReportType != "Custom7") {
                            if ($csv != "yes") {
                                print "<td><a title='Total Golden Days'><font face='Verdana' size='1' color='Golden'>" . $golden . "</font></a></td>";
                            } else {
                                print $golden . ";";
                            }
                        }
                        if (strpos($ras, "-VM-") !== false && $lstReportType != "Custom7") {
                            if ($csv != "yes") {
                                print "<td><a title='Total Vermilion Days'><font face='Verdana' size='1' color='Vermilion'>" . $vermilion . "</font></a></td>";
                            } else {
                                print $vermilion . ";";
                            }
                        }
                        if (strpos($ras, "-SL-") !== false && $lstReportType != "Custom7") {
                            if ($csv != "yes") {
                                print "<td><a title='Total Silver Days'><font face='Verdana' size='1' color='Silver'>" . $silver . "</font></a></td>";
                            } else {
                                print $silver . ";";
                            }
                        }
                        if (strpos($ras, "-MR-") !== false && $lstReportType != "Custom7") {
                            if ($csv != "yes") {
                                print "<td><a title='Total Maroon Days'><font face='Verdana' size='1' color='Maroon'>" . $maroon . "</font></a></td>";
                            } else {
                                print $maroon . ";";
                            }
                        }
                        if (strpos($ras, "-PK-") !== false && $lstReportType != "Custom7") {
                            if ($csv != "yes") {
                                print "<td><a title='Total Pink Days'><font face='Verdana' size='1' color='Pink'>" . $pink . "</font></a></td>";
                            } else {
                                print $pink . ";";
                            }
                        }
                        
                        if (strpos($ras, "-WKD-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom8") {
                            if ($csv != "yes") {
                                print "<td><a title='Total Week Days'><font face='Verdana' size='1'>" . ($wkdn + $wkdo) . "</font></a></td>";
                            } else {
                                print $wkdn + $wkdo . ";";
                            }
                        }
                        if (strpos($ras, "-PXY-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom8" && $lstReportType != "Custom9") {
                            if ($csv != "yes") {
                                print "<td><a title='Total Proxy Days'><font face='Verdana' size='1'>" . ($pxyn + $pxyo) . "</font></a></td>";
                            } else {
                                print $pxyn + $pxyo . ";";
                            }
                        }
                        if (strpos($ras, "-FLG-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom8" && $lstReportType != "Custom9") {
                            if ($csv != "yes") {
                                print "<td><a title='Total Flag Days'><font face='Verdana' size='1'>" . ($flgn + $flgo) . "</font></a></td>";
                            } else {
                                print $flgn + $flgo . ";";
                            }
                        }
                        if (strpos($ras, "-SAT-") !== false  && $lstReportType == "Custom9") {
                            if ($csv != "yes") {
                                print "<td><a title='Total Saturdays and Sundays'><font face='Verdana' size='1'>" . ($satn + $sato + $sunn + $suno) . "</font></a></td>";
                            } else {
                                print $satn + $sato + $sunn + $suno. ";";
                            }
                        }
                        if (strpos($ras, "-SAT-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom8" && $lstReportType != "Custom9") {
                            if ($csv != "yes") {
                                print "<td><a title='Total Saturdays'><font face='Verdana' size='1'>" . ($satn + $sato) . "</font></a></td>";
                            } else {
                                print $satn + $sato . ";";
                            }
                        }
                        if (strpos($ras, "-SUN-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom8" && $lstReportType != "Custom9") {
                            if ($csv != "yes") {
                                print "<td><a title='Total Sundays'><font face='Verdana' size='1'>" . ($sunn + $suno) . "</font></a></td>";
                            } else {
                                print $sunn + $suno . ";";
                            }
                        }
                        if (strpos($ras, "-TLD-") !== false && $lstReportType != "Custom7") {
                            if ($csv != "yes") {
                                if ($lstReportType == "Custom6") {
                                    round($late_days / $lateness_absence);
                                    print "<td bgcolor='#F0F0F0'><a title='Total Days Present (Excluding Sundays AND the Flags SET be Ignored in Global Settings)'><font face='Verdana' size='1'>" . ($wkdn + $pxyn + $flgn + $satn + $wkdo + $pxyo + $flgo + $sato - $tld - round($late_days / $lateness_absence)) . "</font></a></td>";
                                } else {
                                    round($late_days / $lateness_absence);
                                    if($lstReportType != "Custom9"){
                                        print "<td bgcolor='#F0F0F0'><a title='Total Days Present (Excluding the Flags SET be Ignored in Global Settings)'><font face='Verdana' size='1'>" . ($wkdn + $pxyn + $flgn + $satn + $sunn + $wkdo + $pxyo + $flgo + $sato + $suno - $tld - round($late_days / $lateness_absence)) . "</font></a></td>";
                                    }
                                }
                            } else {
                                if ($lstReportType == "Custom6") {
                                    round($late_days / $lateness_absence);
                                    print $wkdn + $pxyn + $flgn + $satn + $wkdo + $pxyo + $flgo + $sato - $tld - round($late_days / $lateness_absence) . ";";
                                } else {
                                    round($late_days / $lateness_absence);
                                    print $wkdn + $pxyn + $flgn + $satn + $sunn + $wkdo + $pxyo + $flgo + $sato + $suno - $tld - round($late_days / $lateness_absence) . ";";
                                }
                            }
                            if ($lstReportType != "Custom2") {
                                $this_record = $dayCount - $reg_day - ($wkdn + $pxyn + $flgn + $satn + $sunn + $wkdo + $pxyo + $flgo + $sato + $suno) + round($late_days / $lateness_absence);
                                if ($this_record < 0) {
                                    $this_record = 0;
                                }
                                if ($csv != "yes") {
                                    print "<td bgcolor='#F0F0F0'><a title='Total Days Absent'><font face='Verdana' size='1'>" . $this_record . "</font></a></td>";
                                } else {
                                    print $this_record . ";";
                                }
                            }
                            $sub_query = "SELECT COUNT(FlagDayRotationID) FROM FlagDayRotation WHERE e_id = " . $eid . " AND e_date >= " . insertDate($txtFrom) . " AND e_date <= " . insertDate($txtTo) . " AND (OT = 'OT2' OR OT = 'OT1') ";
                            $sub_result = selectData($conn, $sub_query);
                            if (0 < $sub_result[0]) {
                                $sub_query = "SELECT COUNT(AttendanceID) FROM " . $table_name . " WHERE EmployeeID = " . $eid . " AND ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " ";
                                $sub_result = selectData($conn, $sub_query);
                                $totalDays = $sub_result[0];
                                $no_abdays_ = $dayCount - $totalDays;
                                $sub_query = "SELECT COUNT(FlagDayRotationID) FROM FlagDayRotation WHERE e_id = " . $eid . " AND e_date >= " . insertDate($txtFrom) . " AND e_date <= " . insertDate($txtTo) . " AND e_date NOT IN (SELECT ADate FROM " . $table_name . " WHERE EmployeeID = " . $eid . " AND ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " ) AND OT = 'OT2' ";
                                $sub_result = selectData($conn, $sub_query);
                                $no_abdays = $no_abdays_ - $sub_result[0] + round($late_days / $lateness_absence);
                                if ($dayCount - $totalDays < $no_abdays) {
                                    $no_abdays = $dayCount - $totalDays;
                                }
                                if ($no_abdays < 0) {
                                    $no_abdays = 0;
                                }
                                if ($lstReportType == "Custom8") {
                                    if ($csv != "yes") {
                                        print "<td bgcolor='#F0F0F0'><a title='Total Days Absent EXCLUDING Sundays'><font face='Verdana' size='1'>" . $no_abdays . "</font></a></td>";
                                    } else {
                                        print $no_abdays . ";";
                                    }
                                }
                                if ($lstReportType != "Custom2" && $lstReportType != "Custom9") {
                                    if ($csv != "yes") {
                                        print "<td bgcolor='#F0F0F0'><a title='Total Days Absent EXCLUDING Sundays'><font face='Verdana' size='1'>" . $no_abdays . "</font></a></td>";
                                    } else {
                                        print $no_abdays . ";";
                                    }
                                }
                                $sub_query = "SELECT COUNT(FlagDayRotationID) FROM FlagDayRotation WHERE e_id = " . $eid . " AND e_date >= " . insertDate($txtFrom) . " AND e_date <= " . insertDate($txtTo) . " AND e_date NOT IN (SELECT ADate FROM " . $table_name . " WHERE EmployeeID = " . $eid . " AND ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " ) AND (OT = 'OT2' OR OT = 'OT1') ";
                                $sub_result = selectData($conn, $sub_query);
                                $no_abdays = $no_abdays_ - $sub_result[0] + round($late_days / $lateness_absence);
                                if ($dayCount - $totalDays < $no_abdays) {
                                    $no_abdays = $dayCount - $totalDays;
                                }
                                if ($no_abdays < 0) {
                                    $no_abdays = 0;
                                }
                                if ($csv != "yes") {
                                    if($lstReportType != "Custom9"){
                                        print "<td bgcolor='#F0F0F0'><a title='Total Days Absent EXCLUDING Sundays and Saturdays'><font face='Verdana' size='1'>" . $no_abdays . "</font></a></td>";
                                    }
                                } else {
                                    print $no_abdays . ";";
                                }
                            } else {
                                if ($global_saturday == 0) {
                                    $this_record = $dayCount - $reg_day - $sunCount - ($wkdn + $pxyn + $flgn + $satn + $wkdo + $pxyo + $flgo + $sato - $tld) + round($late_days / $lateness_absence);
                                } else {
                                    $this_record = $dayCount - $reg_day + $h_satabo + $h_satabn - $sunCount - ($wkdn + $pxyn + $flgn + $satn + $wkdo + $pxyo + $flgo + $sato - $tld) + round($late_days / $lateness_absence);
                                }
                                if ($this_record < 0) {
                                    $this_record = 0;
                                }
                                if ($lstReportType != "Custom2"&& $lstReportType != "Custom9") {
                                    if ($csv != "yes") {
//                                        if($lstReportType != "Custom8"){
                                            print "<td bgcolor='#F0F0F0'><a title='Total Days Absent EXCLUDING Sundays'><font face='Verdana' size='1'>" . $this_record . "</font></a></td>";
//                                        }
                                    } else {
                                        print $this_record . ";";
                                    }
                                }
                                $this_record = $dayCount - $reg_day + $h_sunabo + $h_sunabn + $h_satabo + $h_satabn - $sunCount - $satCount - ($wkdn + $pxyn + $flgn + $wkdo + $pxyo + $flgo - $tld) + round($late_days / $lateness_absence);
                                if ($this_record < 0) {
                                    $this_record = 0;
                                }
                                if ($csv != "yes") {
                                    if($lstReportType != "Custom9"){
                                        print "<td bgcolor='#F0F0F0'><a title='Total Days Absent EXCLUDING Sundays and Saturdays'><font face='Verdana' size='1'>" . $this_record . "</font></a></td>";
                                    }
                                } else {
                                    print $this_record . ";";
                                }
                                $no_abdays = $this_record;
                            }
                            if ($csv != "yes") {
                                if ($lstReportType == "Custom6") {
                                    getDayCount(insertDate($txtFrom), insertDate($txtTo), $dayCount, "Sunday");
                                    print "<td bgcolor='#F0F0F0'><a title='Off Days'><font face='Verdana' size='1'>" . getDayCount(insertDate($txtFrom), insertDate($txtTo), $dayCount, "Sunday") . "</font></a></td><td bgcolor='#F0F0F0'><a title='Total Days'><font face='Verdana' size='1'>" . $dayCount . "</font></a></td>";
                                } else {
                                    if($lstReportType != "Custom8" && $lstReportType != "Custom9"){
                                        print "<td bgcolor='#F0F0F0'><a title='Total Weekdays'><font face='Verdana' size='1'>" . ($dayCount - $sunCount - $satCount) . "</font></a></td>";
                                    }
                                }
                            } else {
                                if ($lstReportType == "Custom6") {
                                    getDayCount(insertDate($txtFrom), insertDate($txtTo), $dayCount, "Sunday");
                                    print getDayCount(insertDate($txtFrom), insertDate($txtTo), $dayCount, "Sunday") . "\n" . $dayCount . "\n";
                                } else {
                                    print $dayCount - $sunCount - $satCount . "\n";
                                }
                            }
                        }
                        if (strpos($ras, "-NS-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom8" && $lstReportType != "Custom9") {
                            if ($csv != "yes") {
                                print "<td><a title='Total Night Shifts'><font face='Verdana' size='1'>" . ($nsn + $nso) . "</font></a></td>";
                            } else {
                                print $nsn + $nso . ";";
                            }
                        }
                        if (strpos($ras, "-LI-") !== false) {
                            if ($csv != "yes") {
                                print "<td><a title='Late In'><font face='Verdana' size='1'>" . $li . "</font></a></td>";
                            } else {
                                print $li . ";";
                            }
                        }
                        if (strpos($ras, "-EO-") !== false) {
                            if ($csv != "yes") {
                                print "<td><a title='Early Out'><font face='Verdana' size='1'>" . $eo . "</font></a></td>";
                            } else {
                                print $eo . ";";
                            }
                        }
                        if (strpos($ras, "-MB-") !== false) {
                            if ($csv != "yes") {
                                print "<td><a title='More Break'><font face='Verdana' size='1'>" . $mb . "</font></a></td>";
                            } else {
                                print $mb . ";";
                            }
                        }
                        if (strpos($ras, "-WKH-") !== false && $lstReportType == "Custom8") {
                            if ($csv != "yes") {
                                round($h_wkdao / 3600, 2);
                                print "<td><a title='Week Days Approved OT Hours'><font face='Verdana' size='1'>" . round($h_wkdao / 3600, 2) . "</font></a></td>";
                            } else {
                                round($h_wkdao / 3600, 2);
                                print round($h_wkdao / 3600, 2) . ";";
                            }
                        }
                        if (strpos($ras, "-WKH-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom8" && $lstReportType != "Custom9") {
                            if ($csv != "yes") {
                                round($h_wkdn / 3600, 2);
                                round($h_wkdo / 3600, 2);
                                round($h_wkdao / 3600, 2);
                                print "<td><a title='Week Days Normal Hours'><font face='Verdana' size='1'>" . round($h_wkdn / 3600, 2) . "</font></a></td> <td><a title='Week Days OT Hours'><font face='Verdana' size='1'>" . round($h_wkdo / 3600, 2) . "</font></a></td> <td><a title='Week Days Approved OT Hours'><font face='Verdana' size='1'>" . round($h_wkdao / 3600, 2) . "</font></a></td>";
                            } else {
                                round($h_wkdn / 3600, 2);
                                round($h_wkdo / 3600, 2);
                                round($h_wkdao / 3600, 2);
                                print round($h_wkdn / 3600, 2) . ";" . round($h_wkdo / 3600, 2) . ";" . round($h_wkdao / 3600, 2) . ";";
                            }
                        }
                        if (strpos($ras, "-PXH-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom8" && $lstReportType != "Custom9") {
                            if ($csv != "yes") {
                                round($h_pxyn / 3600, 2);
                                round($h_pxyo / 3600, 2);
                                round($h_pxyao / 3600, 2);
                                print "<td><a title='Proxy Days Normal Hours'><font face='Verdana' size='1'>" . round($h_pxyn / 3600, 2) . "</font></a></td> <td><a title='Proxy Days OT Hours'><font face='Verdana' size='1'>" . round($h_pxyo / 3600, 2) . "</font></a></td> <td><a title='Proxy Days Approved OT Hours'><font face='Verdana' size='1'>" . round($h_pxyao / 3600, 2) . "</font></a></td>";
                            } else {
                                round($h_pxyn / 3600, 2);
                                round($h_pxyo / 3600, 2);
                                round($h_pxyao / 3600, 2);
                                print round($h_pxyn / 3600, 2) . ";" . round($h_pxyo / 3600, 2) . ";" . round($h_pxyao / 3600, 2) . ";";
                            }
                        }
                        if (strpos($ras, "-WKH+PXH-") !== false) {
                            if ($csv != "yes") {
                                round(($h_wkdn + $h_pxyn) / 3600, 2);
                                round(($h_wkdo + $h_pxyo) / 3600, 2);
                                round(($h_wkdao + $h_pxyao) / 3600, 2);
                                print "<td><a title='Week+Proxy Days Normal Hours'><font face='Verdana' size='1'>" . round(($h_wkdn + $h_pxyn) / 3600, 2) . "</font></a></td> <td><a title='Week+Proxy Days OT Hours'><font face='Verdana' size='1'>" . round(($h_wkdo + $h_pxyo) / 3600, 2) . "</font></a></td> <td><a title='Week+Proxy Days Approved OT Hours'><font face='Verdana' size='1'>" . round(($h_wkdao + $h_pxyao) / 3600, 2) . "</font></a></td>";
                            } else {
                                round(($h_wkdn + $h_pxyn) / 3600, 2);
                                round(($h_wkdo + $h_pxyo) / 3600, 2);
                                round(($h_wkdao + $h_pxyao) / 3600, 2);
                                print round(($h_wkdn + $h_pxyn) / 3600, 2) . ";" . round(($h_wkdo + $h_pxyo) / 3600, 2) . ";" . round(($h_wkdao + $h_pxyao) / 3600, 2) . ";";
                            }
                        }
                        if (strpos($ras, "-FLH-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom8" && $lstReportType != "Custom9") {
                            if ($csv != "yes") {
                                round($h_flgn / 3600, 2);
                                round($h_flgo / 3600, 2);
                                round($h_flgao / 3600, 2);
                                print "<td><a title='Flag Days Normal Hours'><font face='Verdana' size='1'>" . round($h_flgn / 3600, 2) . "</font></a></td> <td><a title='Flag Days OT Hours'><font face='Verdana' size='1'>" . round($h_flgo / 3600, 2) . "</font></a></td> <td><a title='Flag Days Approved OT Hours'><font face='Verdana' size='1'>" . round($h_flgao / 3600, 2) . "</font></a></td>";
                            } else {
                                round($h_flgn / 3600, 2);
                                round($h_flgo / 3600, 2);
                                round($h_flgao / 3600, 2);
                                print round($h_flgn / 3600, 2) . ";" . round($h_flgo / 3600, 2) . ";" . round($h_flgao / 3600, 2) . ";";
                            }
                        }
                        if (strpos($ras, "-SATH-") !== false && $lstReportType == "Custom8") {
                            if ($csv != "yes") {
                                round($h_satao / 3600, 2);
                                print "<td><a title='Saturdays Approved OT Hours'><font face='Verdana' size='1'>" . round($h_satao / 3600, 2) . "</font></a></td>";
                            } else {
                                round($h_satao / 3600, 2);
                                print round($h_satao / 3600, 2) . ";";
                            }
                        }
                        if (strpos($ras, "-SATH-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom8" && $lstReportType != "Custom9") {
                            if ($csv != "yes") {
                                round($h_satn / 3600, 2);
                                round($h_sato / 3600, 2);
                                round($h_satao / 3600, 2);
                                print "<td><a title='Saturdays Normal Hours'><font face='Verdana' size='1'>" . round($h_satn / 3600, 2) . "</font></a></td> <td><a title='Saturdays OT Hours'><font face='Verdana' size='1'>" . round($h_sato / 3600, 2) . "</font></a></td> <td><a title='Saturdays Approved OT Hours'><font face='Verdana' size='1'>" . round($h_satao / 3600, 2) . "</font></a></td>";
                            } else {
                                round($h_satn / 3600, 2);
                                round($h_sato / 3600, 2);
                                round($h_satao / 3600, 2);
                                print round($h_satn / 3600, 2) . ";" . round($h_sato / 3600, 2) . ";" . round($h_satao / 3600, 2) . ";";
                            }
                        }
                        if (strpos($ras, "-WKH+PXH+SATH-") !== false && $lstReportType != "Custom9") {
                            if ($csv != "yes") {
                                round(($h_wkdn + $h_pxyn + $h_satn) / 3600, 2);
                                round(($h_wkdo + $h_pxyo + $h_sato) / 3600, 2);
                                round(($h_wkdao + $h_pxyao + $h_satao) / 3600, 2);
                                print "<td><a title='Week+Proxy+SAT Days Normal Hours'><font face='Verdana' size='1'>" . round(($h_wkdn + $h_pxyn + $h_satn) / 3600, 2) . "</font></a></td> <td><a title='Week+Proxy+SAT Days OT Hours'><font face='Verdana' size='1'>" . round(($h_wkdo + $h_pxyo + $h_sato) / 3600, 2) . "</font></a></td> <td><a title='Week+Proxy+SAT Days Approved OT Hours'><font face='Verdana' size='1'>" . round(($h_wkdao + $h_pxyao + $h_satao) / 3600, 2) . "</font></a></td>";
                            } else {
                                round(($h_wkdn + $h_pxyn + $h_satn) / 3600, 2);
                                round(($h_wkdo + $h_pxyo + $h_sato) / 3600, 2);
                                round(($h_wkdao + $h_pxyao + $h_satao) / 3600, 2);
                                print round(($h_wkdn + $h_pxyn + $h_satn) / 3600, 2) . ";" . round(($h_wkdo + $h_pxyo + $h_sato) / 3600, 2) . ";" . round(($h_wkdao + $h_pxyao + $h_satao) / 3600, 2) . ";";
                            }
                        }
                        if (strpos($ras, "-SUNH-") !== false && $lstReportType == "Custom8" && $lstReportType != "Custom9") {
                            if ($csv != "yes") {
                                round($h_sunao / 3600, 2);
                                print "<td><a title='Sundays Approved OT Hours'><font face='Verdana' size='1'>" . round($h_sunao / 3600, 2) . "</font></a></td>";
                            } else {
                                round($h_sunao / 3600, 2);
                                print round($h_sunao / 3600, 2) . ";";
                            }
                        }
                        if (strpos($ras, "-SUNH-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom8" && $lstReportType != "Custom9") {
                            if ($csv != "yes") {
                                round($h_sunn / 3600, 2);
                                round($h_suno / 3600, 2);
                                round($h_sunao / 3600, 2);
                                print "<td><a title='Sundays Normal Hours'><font face='Verdana' size='1'>" . round($h_sunn / 3600, 2) . "</font></a></td> <td><a title='Sundays OT Hours'><font face='Verdana' size='1'>" . round($h_suno / 3600, 2) . "</font></a></td> <td><a title='Sundays Approved OT Hours'><font face='Verdana' size='1'>" . round($h_sunao / 3600, 2) . "</font></a></td>";
                            } else {
                                round($h_sunn / 3600, 2);
                                round($h_suno / 3600, 2);
                                round($h_sunao / 3600, 2);
                                print round($h_sunn / 3600, 2) . ";" . round($h_suno / 3600, 2) . ";" . round($h_sunao / 3600, 2) . ";";
                            }
                        }
                        if (strpos($ras, "-TLH-") !== false && $lstReportType != "Custom8") {
                            if ($csv != "yes") {    
                                if($lstReportType == "Custom7" && $lstReportType != "Custom9"){
                                    round(($h_wkdo + $h_pxyo + $h_flgo + $h_sato + $h_suno) / 3600, 2);
                                    print "<td bgcolor='#F0F0F0'><a title='Total OT Hours'><font face='Verdana' size='1'>" . round(($h_wkdo + $h_pxyo + $h_flgo + $h_sato + $h_suno) / 3600, 2) . "</font></a></td> <td bgcolor='#F0F0F0'><a title='Total Approved OT Hours'><font face='Verdana' size='1'>";
                                    if ($lstReportType == "Custom4" && round(($h_wkdn + $h_pxyn + $h_flgn + $h_satn + $h_sunn) / 3600, 2) < ($dayCount - $sunCount - $satCount) * 8) {
                                        print "0";
                                    } else {
                                        round(($h_wkdao + $h_pxyao + $h_flgao + $h_satao + $h_sunao) / 3600, 2);
                                        print round(($h_wkdao + $h_pxyao + $h_flgao + $h_satao + $h_sunao) / 3600, 2);
                                    }
                                }else{
                                    if($lstReportType != "Custom9"){
                                    round(($h_wkdn + $h_pxyn + $h_flgn + $h_satn + $h_sunn) / 3600, 2);
                                    print "<td bgcolor='#F0F0F0'><a title='Total Normal Hours'><font face='Verdana' size='1'>" . round(($h_wkdn + $h_pxyn + $h_flgn + $h_satn + $h_sunn) / 3600, 2) . "</font></a></td>";
                                    round(($h_wkdo + $h_pxyo + $h_flgo + $h_sato + $h_suno) / 3600, 2);
                                    print "<td bgcolor='#F0F0F0'><a title='Total OT Hours'><font face='Verdana' size='1'>" . round(($h_wkdo + $h_pxyo + $h_flgo + $h_sato + $h_suno) / 3600, 2) . "</font></a></td> <td bgcolor='#F0F0F0'><a title='Total Approved OT Hours'><font face='Verdana' size='1'>";
                                    if ($lstReportType == "Custom4" && round(($h_wkdn + $h_pxyn + $h_flgn + $h_satn + $h_sunn) / 3600, 2) < ($dayCount - $sunCount - $satCount) * 8) {
                                        print "0";
                                    } else {
                                        round(($h_wkdao + $h_pxyao + $h_flgao + $h_satao + $h_sunao) / 3600, 2);
                                        print round(($h_wkdao + $h_pxyao + $h_flgao + $h_satao + $h_sunao) / 3600, 2);
                                    }
                                    }
                                }
                                print "</font></a></td>";
                            } else {
                                if($lstReportType == "Custom7"){
                                    round(($h_wkdo + $h_pxyo + $h_flgo + $h_sato + $h_suno) / 3600, 2);
                                    print round(($h_wkdo + $h_pxyo + $h_flgo + $h_sato + $h_suno) / 3600, 2) . ";";
                                    if ($lstReportType == "Custom4" && round(($h_wkdn + $h_pxyn + $h_flgn + $h_satn + $h_sunn) / 3600, 2) < ($dayCount - $sunCount - $satCount) * 8) {
                                        print "0;";
                                    } else {
                                        round(($h_wkdao + $h_pxyao + $h_flgao + $h_satao + $h_sunao) / 3600, 2);
                                        print round(($h_wkdao + $h_pxyao + $h_flgao + $h_satao + $h_sunao) / 3600, 2) . ";";
                                    }
                                }else{
                                    round(($h_wkdn + $h_pxyn + $h_flgn + $h_satn + $h_sunn) / 3600, 2);
                                    print round(($h_wkdn + $h_pxyn + $h_flgn + $h_satn + $h_sunn) / 3600, 2) . ";";
                                    round(($h_wkdo + $h_pxyo + $h_flgo + $h_sato + $h_suno) / 3600, 2);
                                    print round(($h_wkdo + $h_pxyo + $h_flgo + $h_sato + $h_suno) / 3600, 2) . ";";
                                    if ($lstReportType == "Custom4" && round(($h_wkdn + $h_pxyn + $h_flgn + $h_satn + $h_sunn) / 3600, 2) < ($dayCount - $sunCount - $satCount) * 8) {
                                        print "0;";
                                    } else {
                                        round(($h_wkdao + $h_pxyao + $h_flgao + $h_satao + $h_sunao) / 3600, 2);
                                        print round(($h_wkdao + $h_pxyao + $h_flgao + $h_satao + $h_sunao) / 3600, 2) . ";";
                                    }
                                }
                            }
                        }
                        if (strpos($ras, "-NSH-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom8" && $lstReportType != "Custom9") {
                            if ($csv != "yes") {
                                round($h_nsn / 3600, 2);
                                round($h_nso / 3600, 2);
                                round($h_nsao / 3600, 2);
                                print "<td><a title='Un Flagged Night Shift Normal Hours'><font face='Verdana' size='1'>" . round($h_nsn / 3600, 2) . "</font></a></td> <td><a title='Un Flagged Night Shift OT Hours'><font face='Verdana' size='1'>" . round($h_nso / 3600, 2) . "</font></a></td> <td><a title='Un Flagged Night Shift Approved OT Hours'><font face='Verdana' size='1'>" . round($h_nsao / 3600, 2) . "</font></a></td>";
                            } else {
                                round($h_nsn / 3600, 2);
                                round($h_nso / 3600, 2);
                                round($h_nsao / 3600, 2);
                                print round($h_nsn / 3600, 2) . ";" . round($h_nso / 3600, 2) . ";" . round($h_nsao / 3600, 2) . ";";
                            }
                        }
                        if (strpos($ras, "-LIH-") !== false) {
                            if ($csv != "yes") {
                                if ($lstReportType == "Custom6") {
                                    round($h_li / 60, 2);
                                    print "<td><a title='Total Late In Minutes'><font face='Verdana' size='1'>" . round($h_li / 60, 2) . "</font></a></td>";
                                } else {
                                    round($h_li / 3600, 2);
                                    print "<td><a title='Total Late In Hours'><font face='Verdana' size='1'>" . round($h_li / 3600, 2) . "</font></a></td>";
                                }
                            } else {
                                if ($lstReportType == "Custom6") {
                                    round($h_li / 60, 2);
                                    print round($h_li / 60, 2) . ";";
                                } else {
                                    round($h_li / 3600, 2);
                                    print round($h_li / 3600, 2) . ";";
                                }
                            }
                        }
                        if (strpos($ras, "-EOH-") !== false) {
                            if ($csv != "yes") {
                                round($h_eo / 3600, 2);
                                print "<td><a title='Total Early Out Hours'><font face='Verdana' size='1'>" . round($h_eo / 3600, 2) . "</font></a></td>";
                            } else {
                                round($h_eo / 3600, 2);
                                print round($h_eo / 3600, 2) . ";";
                            }
                        }
                        if (strpos($ras, "-MBH-") !== false) {
                            if ($csv != "yes") {
                                round($h_mb / 3600, 2);
                                print "<td><a title='Total More Break Hours'><font face='Verdana' size='1'>" . round($h_mb / 3600, 2) . "</font></a></td>";
                            } else {
                                round($h_mb / 3600, 2);
                                print round($h_mb / 3600, 2) . ";";
                            }
                        }
                        if ($lstReportType == "Basic/Net") {
                            if ($csv != "yes") {
                                addComma($v_basic[$eid]);
                                print "<td><font face='Verdana' size='1'>" . addComma($v_basic[$eid]) . "</font></td>";
                                addComma($v_net[$eid]);
                                print "<td><font face='Verdana' size='1'>" . addComma($v_net[$eid]) . "</font></td>";
                            } else {
                                addComma($v_basic[$eid]);
                                print addComma($v_basic[$eid]) . ";";
                                addComma($v_net[$eid]);
                                print addComma($v_net[$eid]) . ";";
                            }
                        }
                        if ($lstReportType == "Custom5") {
                            if ($csv != "yes") {
                                print "<td align='center'><font face='Verdana' size='1'><b>P</b></font></td>";
                            } else {
                                print "P;";
                            }
                        }
                        if ($csv != "yes") {
                            print "</tr>";
                        } else {
                            print "\n";
                        }
                        $row_count++;
                    }
                    $ot_query = "SELECT OT1, OT2 FROM " . $table_name . " WHERE EmployeeID = " . $cur[0] . " AND ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo);
                    $ot_result = selectData($conn, $ot_query);
                    $satCount = getDayCount(insertDate($txtFrom), insertDate($txtTo), $dayCount, $ot_result[0]);
                    $sunCount = getDayCount(insertDate($txtFrom), insertDate($txtTo), $dayCount, $ot_result[1]);
                    $fdr_query = "SELECT COUNT(OT) FROM FlagDayRotation WHERE OT = 'OT1' AND e_id = " . $cur[0] . " AND e_date >= " . insertDate($txtFrom) . " AND e_date <= " . insertDate($txtTo);
                    $fdr_result = selectData($conn, $fdr_query);
                    if (0 < $fdr_result[0]) {
                        $satCount = $fdr_result[0];
                        $satFlag = true;
                        $satArray = "";
                        $sat_arrray_count = 0;
                        $sat_arrray_query = "SELECT e_date FROM FlagDayRotation WHERE OT = 'OT1' AND e_id = " . $cur[0] . " AND e_date >= " . insertDate($txtFrom) . " AND e_date <= " . insertDate($txtTo);
                        for ($sat_arrray_result = mysqli_query($sat_arrray_query, $conn); $sat_arrray_cur = mysqli_fetch_row($sat_arrray_result); $sat_arrray_count++) {
                            $satArray[$sat_arrray_count] = $sat_arrray_cur[0];
                        }
                    }
                    $fdr_query = "SELECT COUNT(OT) FROM FlagDayRotation WHERE OT = 'OT2' AND e_id = " . $cur[0] . " AND e_date >= " . insertDate($txtFrom) . " AND e_date <= " . insertDate($txtTo);
                    $fdr_result = selectData($conn, $fdr_query);
                    if (0 < $fdr_result[0]) {
                        $sunCount = $fdr_result[0];
                        $sunFlag = true;
                        $sunArray = "";
                        $sun_arrray_count = 0;
                        $sun_arrray_query = "SELECT e_date FROM FlagDayRotation WHERE OT = 'OT2' AND e_id = " . $cur[0] . " AND e_date >= " . insertDate($txtFrom) . " AND e_date <= " . insertDate($txtTo);
                        for ($sun_arrray_result = mysqli_query($sun_arrray_query, $conn); $sun_arrray_cur = mysqli_fetch_row($sun_arrray_result); $sun_arrray_count++) {
                            $sunArray[$sun_arrray_count] = $sun_arrray_cur[0];
                        }
                    }
                    if ($csv != "yes") {
                        print "<tr><td><input type='hidden' name='txh" . $count . "' value='" . $cur[0] . "'>";
                    }
                    if ($prints != "yes") {
                        print "<a title='ID: Click to view Clocking Details for the selected Period (ACTIVE EMPLOYEES ONLY)' href='ReportDailyRoster.php?act=searchRecord&prints=yes&excel=no&subReport=yes&lstEmployeeStatus=" . $cur[18] . "&lstEmployeeID=" . $cur[0] . "&txtFrom=" . $txtFrom . "&txtTo=" . $txtTo . "&lstDB=" . $lstDB . "' target='_blank'>";
                    }
                    if ($csv != "yes") {
                        addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
                        print "<font face='Verdana' size='1' color='#000000'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>";
                    } else {
                        addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
                        print addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . ";";
                    }
                    if ($cur[0] == $cur[1] && strpos($userlevel, "11D") !== false) {
                        $pax_query = "SELECT N1, N2, N3, N4, N5, N6, N7, N8, N9, N0 FROM PAX WHERE id = " . $cur[0] * 7;
                        $pax_result = selectData($conn, $pax_query);
                        if ($pax_result[0] != "") {
                            for ($pax_i = 0; $pax_i < 10; $pax_i++) {
                                strrev($pax_result[$pax_i]);
                                print strrev($pax_result[$pax_i]);
                            }
                        } else {
                            print $cur[1];
                        }
                    } else {
                        print $cur[1];
                    }
                    if ($csv != "yes") {
                        print "</font></a></td>";
                    }
                    if ($lstReportType != "Custom2" && $lstReportType != "Custom5" && $lstReportType != "Custom6") {
                        if ($csv != "yes") {
                            print "<td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $cur[10] . "</font></a></td>";
                        } else {
                            print $cur[10] . ";";
                        }
                    }
                    if ($lstReportType != "Custom5" && $lstReportType != "Custom6") {
                        if ($csv != "yes") {
                            print "<td><a title='Dept'><font face='Verdana' size='1'>" . $cur[2] . "</font></a></td>";
                        } else {
                            print $cur[2] . ";";
                        }
                    }
                    if ($lstReportType != "Custom2" && $lstReportType != "Custom5" && $lstReportType != "Custom6") {
                        if ($csv != "yes") {
                            print "<td><a title='Div/Desg'><font face='Verdana' size='1'>" . $cur[3] . "</font></a></td>";
                        } else {
                            print $cur[3] . ";";
                        }
                    }
//                    if (0 < $count && $lstReportType == "Custom9") {
//                        if ($csv != "yes") {
//                            print "<td><a title='Total Violet Days'><font face='Verdana' size='1' color='Violet'>" . $violet . "</font></a></td><td><a title='Total Blue Days'><font face='Verdana' size='1' color='Blue'>" . $blue . "</font></a></td><td><a title='Total Orange Days'><font face='Verdana' size='1' color='Orange'>" . $orange . "</font></a></td><td><a title='Total Green Days'><font face='Verdana' size='1' color='Green'>" . $green . "</font></a></td><td><a title='Total Week Days'><font face='Verdana' size='1'>" . ($wkdn + $wkdo) . "</font></a></td>";
//                        } else {
//                            print $violet . ";".$blue . ";".$orange.";".$green.";".$wkdn + $wkdo . ";";
//                        }

//                    }
                    if ($lstReportType != "Custom6" && $lstReportType != "Custom7" && $lstReportType != "Custom8" && $lstReportType != "Custom9" && $lstReportType != "Custom10") {
                        if ($csv != "yes") {
                            print "<td><a title='Rmk'><font face='Verdana' size='1'>" . $cur[11] . "</font></a></td> ";
                        } else {
                            print $cur[11] . ";";
                        }
                    }
                    
                    if ($lstReportType == "Custom6") {
                        $prefix = "L";
                        if (substr($cur[11], 0, 1) == "1") {
                            $prefix = "C";
                        }
                        if ($csv != "yes") {
                            print "<td><a title='Category'><font face='Verdana' size='1'>" . $prefix . "</font></a></td> ";
                        } else {
                            print $prefix . ";";
                        }
                    }
//                    if ($lstReportType == "Custom7") {
//                        $prefix = "L";
//                        if (substr($cur[11], 0, 1) == "1") {
//                            $prefix = "C";
//                        }
//                        if ($csv != "yes") {
//                            print "<td><a title='Category'><font face='Verdana' size='1'>" . $prefix . "</font></a></td> ";
//                        } else {
//                            print $prefix . ";";
//                        }
//                    }
//                    if ($lstReportType == "Custom8") {
//                        $prefix = "L";
//                        if (substr($cur[11], 0, 1) == "1") {
//                            $prefix = "C";
//                        }
//                        if ($csv != "yes") {
//                            print "<td><a title='Category'><font face='Verdana' size='1'>" . $prefix . "</font></a></td> ";
//                        } else {
//                            print $prefix . ";";
//                        }
//                    }
//                    if($lstReportType == "Custom9"){
//                        if ($csv != "yes") {
//                            print "<td><a title='Shift'><font face='Verdana' size='1'>" . $cur[11] . "</font></a></td> ";
//                        } else {
//                            print $cur[11] . ";";
//                        }
//                    }
                    $eid = $cur[0];
                    $subc = 0;
                    $reg_day = 0;
                    $cur[26] = substr($cur[26], 1, 8);
                    if (insertDate($txtFrom) < $cur[26] && getRegister($txtMACAddress, 7) != "66") {
                        $reg_day = getTotalDays($txtFrom, displayDate($cur[26]));
                    }
                    $wkdn = 0;
                    $wkdo = 0;
                    $pxyn = 0;
                    $pxyo = 0;
                    $flgn = 0;
                    $flgo = 0;
                    $satn = 0;
                    $sato = 0;
                    $sunn = 0;
                    $suno = 0;
                    $nsn = 0;
                    $nso = 0;
                    $li = 0;
                    $eo = 0;
                    $mb = 0;
                    $tld = 0;
                    $violet = 0;
                    $indigo = 0;
                    $blue = 0;
                    $green = 0;
                    $yellow = 0;
                    $orange = 0;
                    $red = 0;
                    $gray = 0;
                    $brown = 0;
                    $purple = 0;
                    $magenta = 0;
                    $teal = 0;
                    $aqua = 0;
                    $safron = 0;
                    $amber = 0;
                    $golden = 0;
                    $vermilion = 0;
                    $silver = 0;
                    $maroon = 0;
                    $pink = 0;
                    $black = 0;
                    $h_wkdn = 0;
                    $h_wkdo = 0;
                    $h_wkdao = 0;
                    $h_pxyn = 0;
                    $h_pxyo = 0;
                    $h_pxyao = 0;
                    $h_flgn = 0;
                    $h_flgo = 0;
                    $h_flgao = 0;
                    $h_satn = 0;
                    $h_sato = 0;
                    $h_satao = 0;
                    $h_sunn = 0;
                    $h_suno = 0;
                    $h_sunao = 0;
                    $h_nsn = 0;
                    $h_nso = 0;
                    $h_nsao = 0;
                    $h_li = 0;
                    $h_eo = 0;
                    $h_mb = 0;
                    $h_nfao = 0;
                    $h_satabn = 0;
                    $h_satabo = 0;
                    $h_sunabn = 0;
                    $h_sunabo = 0;
                    $wkda = 0;
                    $wkdsata = 0;
                    $late_days = 0;
                    if ($cur[14] == 1) {
                        $txtDate = getLastDay(insertDate($txtFrom), 1);
                        $txtLastDate = $txtDate;
                    } else {
                        $txtDate = insertDate($txtFrom);
                        $txtLastDate = $txtDate;
                    }
                }
                if (0 < $cur[19]) {
                    $late_days++;
                }
                while (true) {
                    $subc++;
                    if ($cur[9] == $txtDate || $cur[9] == $txtLastDate) {
                        if ($cur[12] != "Black" && $cur[12] != "Proxy") {
                            $cur[15] = $cur[15] * $txtFlagFactor;
                            if (0 < $cur[8]) {
                                $flgo++;
                                if ($cur[13] == $cur[16]) {
                                    $h_satabo++;
                                }
                                if ($cur[13] == $cur[17]) {
                                    $h_sunabo++;
                                }
                            } else {
                                $flgn++;
                                if ($cur[13] == $cur[16]) {
                                    $h_satabn++;
                                }
                                if ($cur[13] == $cur[17]) {
                                    $h_sunabn++;
                                }
                            }
                            $h_flgn = $h_flgn + $cur[6];
                            $h_flgo = $h_flgo + $cur[8];
                            $h_flgao = $h_flgao + $cur[15];
                            if ($cur[12] == "Violet") {
                                $violet++;
                                if ($colour_result[1] == "No") {
                                    $tld++;
                                }
                            } else {
                                if ($cur[12] == "Indigo") {
                                    $indigo++;
                                    if ($colour_result[2] == "No") {
                                        $tld++;
                                    }
                                } else {
                                    if ($cur[12] == "Blue") {
                                        $blue++;
                                        if ($colour_result[3] == "No") {
                                            $tld++;
                                        }
                                    } else {
                                        if ($cur[12] == "Green") {
                                            $green++;
                                            if ($colour_result[4] == "No") {
                                                $tld++;
                                            }
                                        } else {
                                            if ($cur[12] == "Yellow") {
                                                $yellow++;
                                                if ($colour_result[5] == "No") {
                                                    $tld++;
                                                }
                                            } else {
                                                if ($cur[12] == "Orange") {
                                                    $orange++;
                                                    if ($colour_result[6] == "No") {
                                                        $tld++;
                                                    }
                                                } else {
                                                    if ($cur[12] == "Red") {
                                                        $red++;
                                                        if ($colour_result[7] == "No") {
                                                            $tld++;
                                                        }
                                                    } else {
                                                        if ($cur[12] == "Gray") {
                                                            $gray++;
                                                            if ($colour_result[8] == "No") {
                                                                $tld++;
                                                            }
                                                        } else {
                                                            if ($cur[12] == "Brown") {
                                                                $brown++;
                                                                if ($colour_result[9] == "No") {
                                                                    $tld++;
                                                                }
                                                            } else {
                                                                if ($cur[12] == "Purple") {
                                                                    $purple++;
                                                                    if ($colour_result[10] == "No") {
                                                                        $tld++;
                                                                    }
                                                                } else {
                                                                    if ($cur[12] == "Black") {
                                                                        $black++;
                                                                        if ($colour_result[11] == "No") {
                                                                            $tld++;
                                                                        }
                                                                    } else {
                                                                        if ($cur[12] == "Proxy") {
                                                                            $proxy++;
                                                                            if ($colour_result[12] == "No") {
                                                                                $tld++;
                                                                            }
                                                                        } else {
                                                                            if ($cur[12] == "Magenta") {
                                                                                $magenta++;
                                                                                if ($colour_result[13] == "No") {
                                                                                    $tld++;
                                                                                }
                                                                            } else {
                                                                                if ($cur[12] == "Teal") {
                                                                                    $teal++;
                                                                                    if ($colour_result[14] == "No") {
                                                                                        $tld++;
                                                                                    }
                                                                                } else {
                                                                                    if ($cur[12] == "Aqua") {
                                                                                        $aqua++;
                                                                                        if ($colour_result[15] == "No") {
                                                                                            $tld++;
                                                                                        }
                                                                                    } else {
                                                                                        if ($cur[12] == "Safron") {
                                                                                            $safron++;
                                                                                            if ($colour_result[16] == "No") {
                                                                                                $tld++;
                                                                                            }
                                                                                        } else {
                                                                                            if ($cur[12] == "Amber") {
                                                                                                $amber++;
                                                                                                if ($colour_result[17] == "No") {
                                                                                                    $tld++;
                                                                                                }
                                                                                            } else {
                                                                                                if ($cur[12] == "Gold") {
                                                                                                    $gold++;
                                                                                                    if ($colour_result[18] == "No") {
                                                                                                        $tld++;
                                                                                                    }
                                                                                                } else {
                                                                                                    if ($cur[12] == "Vermilion") {
                                                                                                        $vermilion++;
                                                                                                        if ($colour_result[19] == "No") {
                                                                                                            $tld++;
                                                                                                        }
                                                                                                    } else {
                                                                                                        if ($cur[12] == "Silver") {
                                                                                                            $silver++;
                                                                                                            if ($colour_result[20] == "No") {
                                                                                                                $tld++;
                                                                                                            }
                                                                                                        } else {
                                                                                                            if ($cur[12] == "Maroon") {
                                                                                                                $maroon++;
                                                                                                                if ($colour_result[21] == "No") {
                                                                                                                    $tld++;
                                                                                                                }
                                                                                                            } else {
                                                                                                                if ($cur[12] == "Pink") {
                                                                                                                    $pink++;
                                                                                                                    if ($colour_result[22] == "No") {
                                                                                                                        $tld++;
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
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            if ($satFlag == false && $cur[13] == $cur[16] || $satFlag == true && in_array($cur[9], $satArray) == true) {
                                $cur[15] = $cur[15] * $txtSatFactor;
                                if (0 < $cur[8]) {
                                    $sato++;
                                } else {
                                    $satn++;
                                }
                                $h_satn = $h_satn + $cur[6];
                                $h_sato = $h_sato + $cur[8];
                                $h_satao = $h_satao + $cur[15];
                            } else {
                                if ($sunFlag == false && $cur[13] == $cur[17] || $sunFlag == true && in_array($cur[9], $sunArray) == true) {
                                    $cur[15] = $cur[15] * $txtSunFactor;
                                    if (0 < $cur[8]) {
                                        $suno++;
                                    } else {
                                        $sunn++;
                                    }
                                    $h_sunn = $h_sunn + $cur[6];
                                    $h_suno = $h_suno + $cur[8];
                                    $h_sunao = $h_sunao + $cur[15];
                                } else {
                                    if ($cur[12] == "Proxy") {
                                        if (0 < $cur[8]) {
                                            $pxyo++;
                                        } else {
                                            $pxyn++;
                                        }
                                        $h_pxyn = $h_pxyn + $cur[6];
                                        $h_pxyo = $h_pxyo + $cur[8];
                                        $h_pxyao = $h_pxyao + $cur[15];
                                        if ($colour_result[12] == "No") {
                                            $tld++;
                                        }
                                    } else {
                                        if ($cur[12] == "Black") {
                                            if (0 < $cur[8]) {
                                                $wkdo++;
                                            } else {
                                                $wkdn++;
                                            }
                                            $h_wkdn = $h_wkdn + $cur[6];
                                            $h_wkdo = $h_wkdo + $cur[8];
                                            $h_wkdao = $h_wkdao + $cur[15];
                                            if ($colour_result[11] == "No") {
                                                $tld++;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        if ($cur[14] == 1) {
                            if (0 < $cur[8]) {
                                $nso++;
                            } else {
                                $nsn++;
                            }
                            $h_nsn = $h_nsn + $cur[6];
                            $h_nso = $h_nso + $cur[8];
                            $h_nsao = $h_nsao + $cur[15];
                        }
                        if (0 < $cur[19] && $cur[22] == 0) {
                            $li++;
                            $h_li = $h_li + $cur[19];
                        }
                        if (0 < $cur[20] && $cur[23] == 0) {
                            $eo++;
                            $h_eo = $h_eo + $cur[20];
                        }
                        if (0 < $cur[21] && $cur[24] == 0) {
                            $mb++;
                            $h_mb = $h_mb + $cur[21];
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
                        break;
                    }
                    if ($dayCount < $subc) {
                        break;
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
                $count++;
                $data9 = $cur[9];
            }
            if (0 < $count) {
                if (strpos($ras, "-V-") !== false && $lstReportType != "Custom7") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Violet Days'><font face='Verdana' size='1' color='Violet'>" . $violet . "</font></a></td>";
                    } else {
                        print $violet . ";";
                    }
                }
                if (strpos($ras, "-I-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom8" && $lstReportType != "Custom9") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Indigo Days'><font face='Verdana' size='1' color='Indigo'>" . $indigo . "</font></a></td>";
                    } else {
                        print $indigo . ";";
                    }
                }
                if (strpos($ras, "-B-") !== false && $lstReportType != "Custom7") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Blue Days'><font face='Verdana' size='1' color='Blue'>" . $blue . "</font></a></td>";
                    } else {
                        print $blue . ";";
                    }
                }
                if (strpos($ras, "-G-") !== false && $lstReportType != "Custom7") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Green Days'><font face='Verdana' size='1' color='Green'>" . $green . "</font></a></td>";
                    } else {
                        print $green . ";";
                    }
                }
                if (strpos($ras, "-Y-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom9") {
                    if ($csv != "yes") {
                        print "<td bgcolor='Brown'><a title='Total Yellow Days'><font face='Verdana' size='1' color='Yellow'>" . $yellow . "</font></a></td>";
                    } else {
                        print $yellow . ";";
                    }
                }
                if (strpos($ras, "-O-") !== false && $lstReportType != "Custom7") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Orange Days'><font face='Verdana' size='1' color='Orange'>" . $orange . "</font></a></td>";
                    } else {
                        print $orange . ";";
                    }
                }
                if (strpos($ras, "-R-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom9") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Red Days'><font face='Verdana' size='1' color='Red'>" . $red . "</font></a></td>";
                    } else {
                        print $red . ";";
                    }
                }
                if (strpos($ras, "-GR-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom8" && $lstReportType != "Custom9") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Green Days'><font face='Verdana' size='1' color='Gray'>" . $gray . "</font></a></td>";
                    } else {
                        print $gray . ";";
                    }
                }
                if (strpos($ras, "-BR-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom8" && $lstReportType != "Custom9") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Brown Days'><font face='Verdana' size='1' color='Brown'>" . $brown . "</font></a></td>";
                    } else {
                        print $brown . ";";
                    }
                }
                if (strpos($ras, "-PR-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom9") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Purple Days'><font face='Verdana' size='1' color='Purple'>" . $purple . "</font></a></td>";
                    } else {
                        print $purple . ";";
                    }
                }
                if (strpos($ras, "-MG-") !== false && $lstReportType != "Custom7") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Magenta Days'><font face='Verdana' size='1' color='Magenta'>" . $magenta . "</font></a></td>";
                    } else {
                        print $magenta . ";";
                    }
                }
                if (strpos($ras, "-TL-") !== false && $lstReportType != "Custom7") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Teal Days'><font face='Verdana' size='1' color='Teal'>" . $teal . "</font></a></td>";
                    } else {
                        print $teal . ";";
                    }
                }
                if (strpos($ras, "-AQ-") !== false && $lstReportType != "Custom7") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Aqua Days'><font face='Verdana' size='1' color='Aqua'>" . $aqua . "</font></a></td>";
                    } else {
                        print $aqua . ";";
                    }
                }
                if (strpos($ras, "-SF-") !== false && $lstReportType != "Custom7") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Safron Days'><font face='Verdana' size='1' color='Safron'>" . $safron . "</font></a></td>";
                    } else {
                        print $safron . ";";
                    }
                }
                if (strpos($ras, "-AM-") !== false && $lstReportType != "Custom7") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Amber Days'><font face='Verdana' size='1' color='Amber'>" . $amber . "</font></a></td>";
                    } else {
                        print $amber . ";";
                    }
                }
                if (strpos($ras, "-GL-") !== false && $lstReportType != "Custom7") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Golden Days'><font face='Verdana' size='1' color='Golden'>" . $golden . "</font></a></td>";
                    } else {
                        print $golden . ";";
                    }
                }
                if (strpos($ras, "-VM-") !== false && $lstReportType != "Custom7") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Vermilion Days'><font face='Verdana' size='1' color='Vermilion'>" . $vermilion . "</font></a></td>";
                    } else {
                        print $vermilion . ";";
                    }
                }
                if (strpos($ras, "-SL-") !== false && $lstReportType != "Custom7") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Silver Days'><font face='Verdana' size='1' color='Silver'>" . $silver . "</font></a></td>";
                    } else {
                        print $silver . ";";
                    }
                }
                if (strpos($ras, "-MR-") !== false && $lstReportType != "Custom7") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Maroon Days'><font face='Verdana' size='1' color='Maroon'>" . $maroon . "</font></a></td>";
                    } else {
                        print $maroon . ";";
                    }
                }
                if (strpos($ras, "-PK-") !== false && $lstReportType != "Custom7") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Pink Days'><font face='Verdana' size='1' color='Pink'>" . $pink . "</font></a></td>";
                    } else {
                        print $pink . ";";
                    }
                }
                
                if (strpos($ras, "-WKD-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom8") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Week Days'><font face='Verdana' size='1'>" . ($wkdn + $wkdo) . "</font></a></td>";
                    } else {
                        print $wkdn + $wkdo . ";";
                    }
                }
                if (strpos($ras, "-PXY-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom8" && $lstReportType != "Custom9") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Proxy Days'><font face='Verdana' size='1'>" . ($pxyn + $pxyo) . "</font></a></td>";
                    } else {
                        print $pxyn + $pxyo . ";";
                    }
                }
                if (strpos($ras, "-FLG-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom8" && $lstReportType != "Custom9") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Flag Days'><font face='Verdana' size='1'>" . ($flgn + $flgo) . "</font></a></td>";
                    } else {
                        print $flgn + $flgo . ";";
                    }
                }
                if (strpos($ras, "-SAT-") !== false && $lstReportType == "Custom9") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Saturdays and Sundays'><font face='Verdana' size='1'>" . ($satn + $sato + $sunn + $suno) . "</font></a></td>";
                    } else {
                        print $satn + $sato + $sunn + $suno. ";";
                    }
                }
                if (strpos($ras, "-SAT-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom8" && $lstReportType != "Custom9") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Saturdays'><font face='Verdana' size='1'>" . ($satn + $sato) . "</font></a></td>";
                    } else {
                        print $satn + $sato . ";";
                    }
                }
                if (strpos($ras, "-SUN-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom8" && $lstReportType != "Custom9") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Sundays'><font face='Verdana' size='1'>" . ($sunn + $suno) . "</font></a></td>";
                    } else {
                        print $sunn + $suno . ";";
                    }
                }
                if (strpos($ras, "-TLD-") !== false && $lstReportType != "Custom7") {
                    if ($csv != "yes") {
                        if ($lstReportType == "Custom6") {
                            round($late_days / $lateness_absence);
                            print "<td bgcolor='#F0F0F0'><a title='Total Days Present (Excluding Sundays and the Flags SET be Ignored in Global Settings)'><font face='Verdana' size='1'>" . ($wkdn + $pxyn + $flgn + $satn + $wkdo + $pxyo + $flgo + $sato - $tld - round($late_days / $lateness_absence)) . "</font></a></td>";
                        } else {
                            round($late_days / $lateness_absence);
                            if($lstReportType != "Custom9"){                            
                                print "<td bgcolor='#F0F0F0'><a title='Total Days Present (Excluding the Flags SET be Ignored in Global Settings)'><font face='Verdana' size='1'>" . ($wkdn + $pxyn + $flgn + $satn + $sunn + $wkdo + $pxyo + $flgo + $sato + $suno - $tld - round($late_days / $lateness_absence)) . "</font></a></td>";
                            }
                        }
                    } else {
                        if ($lstReportType == "Custom6") {
                            round($late_days / $lateness_absence);
                            print $wkdn + $pxyn + $flgn + $satn + $wkdo + $pxyo + $flgo + $sato - $tld - round($late_days / $lateness_absence) . ";";
                        } else {
                            round($late_days / $lateness_absence);
                            print $wkdn + $pxyn + $flgn + $satn + $sunn + $wkdo + $pxyo + $flgo + $sato + $suno - $tld - round($late_days / $lateness_absence) . ";";
                        }
                    }
                    if ($lstReportType != "Custom2") {
                        $this_record = $dayCount - $reg_day - ($wkdn + $pxyn + $flgn + $satn + $sunn + $wkdo + $pxyo + $flgo + $sato + $suno) + round($late_days / $lateness_absence);
                        if ($this_record < 0) {
                            $this_record = 0;
                        }
                        if ($csv != "yes") {
                            print "<td bgcolor='#F0F0F0'><a title='Total Days Absent'><font face='Verdana' size='1'>" . $this_record . "</font></a></td>";
                        } else {
                            print $this_record . ";";
                        }
                    }
                    $sub_query = "SELECT COUNT(FlagDayRotationID) FROM FlagDayRotation WHERE e_id = " . $eid . " AND e_date >= " . insertDate($txtFrom) . " AND e_date <= " . insertDate($txtTo) . " AND (OT = 'OT2' OR OT = 'OT1') ";
                    $sub_result = selectData($conn, $sub_query);
                    if (0 < $sub_result[0]) {
                        $sub_query = "SELECT COUNT(AttendanceID) FROM " . $table_name . " WHERE EmployeeID = " . $eid . " AND ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " ";
                        $sub_result = selectData($conn, $sub_query);
                        $totalDays = $sub_result[0];
                        $no_abdays_ = $dayCount - $totalDays;
                        $sub_query = "SELECT COUNT(FlagDayRotationID) FROM FlagDayRotation WHERE e_id = " . $eid . " AND e_date >= " . insertDate($txtFrom) . " AND e_date <= " . insertDate($txtTo) . " AND e_date NOT IN (SELECT ADate FROM " . $table_name . " WHERE EmployeeID = " . $eid . " AND ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " ) AND OT = 'OT2' ";
                        $sub_result = selectData($conn, $sub_query);
                        $no_abdays = $no_abdays_ - $sub_result[0] + round($late_days / $lateness_absence);
                        if ($dayCount - $totalDays < $no_abdays) {
                            $no_abdays = $dayCount - $totalDays;
                        }
                        if ($no_abdays < 0) {
                            $no_abdays = 0;
                        }
                        if ($lstReportType == "Custom8" && $lstReportType != "Custom9") {
                            if ($csv != "yes") {
                                    print "<td bgcolor='#F0F0F0'><a title='Total Days Absent EXCLUDING Sundays'><font face='Verdana' size='1'>" . $no_abdays . "</font></a></td>";
                                
                            } else {
                                print $no_abdays . ";";
                            }
                        }
                        if ($lstReportType != "Custom2" && $lstReportType != "Custom9") {
                            if ($csv != "yes") {
                                    print "<td bgcolor='#F0F0F0'><a title='Total Days Absent EXCLUDING Sundays'><font face='Verdana' size='1'>" . $no_abdays . "</font></a></td>";
                                
                            } else {
                                print $no_abdays . ";";
                            }
                        }
                        $sub_query = "SELECT COUNT(FlagDayRotationID) FROM FlagDayRotation WHERE e_id = " . $eid . " AND e_date >= " . insertDate($txtFrom) . " AND e_date <= " . insertDate($txtTo) . " AND e_date NOT IN (SELECT ADate FROM " . $table_name . " WHERE EmployeeID = " . $eid . " AND ADate >= " . insertDate($txtFrom) . " AND ADate <= " . insertDate($txtTo) . " ) AND (OT = 'OT2' OR OT = 'OT1') ";
                        $sub_result = selectData($conn, $sub_query);
                        $no_abdays = $no_abdays_ - $sub_result[0] + round($late_days / $lateness_absence);
                        if ($dayCount - $totalDays < $no_abdays) {
                            $no_abdays = $dayCount - $totalDays;
                        }
                        if ($no_abdays < 0) {
                            $no_abdays = 0;
                        }
                        if ($csv != "yes") {
                            if($lstReportType != "Custom9"){
                                print "<td bgcolor='#F0F0F0'><a title='Total Days Absent EXCLUDING Sundays and Saturdays'><font face='Verdana' size='1'>" . $no_abdays . "</font></a></td>";
                            }
                        } else {
                            print $no_abdays . ";";
                        }
                    } else {
                        if ($global_saturday == 0) {
                            $this_record = $dayCount - $reg_day - $sunCount - ($wkdn + $pxyn + $flgn + $satn + $wkdo + $pxyo + $flgo + $sato - $tld) + round($late_days / $lateness_absence);
                        } else {
                            $this_record = $dayCount - $reg_day + $h_satabo + $h_satabn - $sunCount - ($wkdn + $pxyn + $flgn + $satn + $wkdo + $pxyo + $flgo + $sato - $tld) + round($late_days / $lateness_absence);
                        }
                        if ($this_record < 0) {
                            $this_record = 0;
                        }
//                        if ($lstReportType == "Custom9") {
//                            if ($csv != "yes") {
//                                print "<td bgcolor='#F0F0F0'><a title='Total Days Absent EXCLUDING Sundays'><font face='Verdana' size='1'>" . $this_record . "</font></a></td>";
//                            } else {
//                                print $this_record . ";";
//                            }
//                        }
                        if ($lstReportType != "Custom2" && $lstReportType != "Custom9") {
                            if ($csv != "yes") {
                                print "<td bgcolor='#F0F0F0'><a title='Total Days Absent EXCLUDING Sundays'><font face='Verdana' size='1'>" . $this_record . "</font></a></td>";
                            } else {
                                print $this_record . ";";
                            }
                        }
						echo $wkdn;
						//36 - 0 + 0 + 0 + 0 + 0 - 5 - 5 -(0)
                        //$this_record = $dayCount - $reg_day + $h_sunabo + $h_sunabn + $h_satabo + $h_satabn - $sunCount - $satCount - ($wkdn + $pxyn + $flgn + $wkdo + $pxyo + $flgo - $tld) + round($late_days / $lateness_absence);
						$this_record = $dayCount - $reg_day + $h_sunabn + $h_satabn - ($wkdn + $pxyn + $flgn + $wkdo + $pxyo + $flgo - $tld) + round($late_days / $lateness_absence);
                        if ($this_record < 0) {
                            $this_record = 0;
                        }
                        if ($csv != "yes") {
                            if($lstReportType != "Custom9"){
                                print "<td bgcolor='#F0F0F0'><a title='Total Days Absent EXCLUDING Sundays and Saturdays'><font face='Verdana' size='1'>" . $this_record . "</font></a></td>";
                            }
                        } else {
                            print $this_record . ";";
                        }
                        $no_abdays = $this_record;
                    }
                    if ($csv != "yes") {
                        if ($lstReportType == "Custom6") {
                            getDayCount(insertDate($txtFrom), insertDate($txtTo), $dayCount, "Sunday");
                            print "<td bgcolor='#F0F0F0'><a title='Off Days'><font face='Verdana' size='1'>" . getDayCount(insertDate($txtFrom), insertDate($txtTo), $dayCount, "Sunday") . "</font></a></td><td bgcolor='#F0F0F0'><a title='Total Days'><font face='Verdana' size='1'>" . $dayCount . "</font></a></td>";
                        } else {
                            round($late_days / $lateness_absence);
                            if($lstReportType != "Custom8" && $lstReportType != "Custom9"){
                                print "<td bgcolor='#F0F0F0'><a title='Total Weekdays in the selected Period'><font face='Verdana' size='1'>" . ($wkdn + $pxyn + $flgn + $satn + $sunn + $wkdo + $pxyo + $flgo + $sato + $suno - $tld - round($late_days / $lateness_absence) + $no_abdays) . "</font></a></td>";
                            }
                        }
                    } else {
                        if ($lstReportType == "Custom6") {
                            getDayCount(insertDate($txtFrom), insertDate($txtTo), $dayCount, "Sunday");
                            print getDayCount(insertDate($txtFrom), insertDate($txtTo), $dayCount, "Sunday") . "\n" . $dayCount . "\n";
                        } else {
                            round($late_days / $lateness_absence);
                            print $wkdn + $pxyn + $flgn + $satn + $sunn + $wkdo + $pxyo + $flgo + $sato + $suno - $tld - round($late_days / $lateness_absence) + $no_abdays . ";";
                        }
                    }
                }
                if (strpos($ras, "-NS-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom8" && $lstReportType != "Custom9") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Night Shifts'><font face='Verdana' size='1'>" . ($nsn + $nso) . "</font></a></td>";
                    } else {
                        print $nsn + $nso . ";";
                    }
                }
                if (strpos($ras, "-LI-") !== false) {
                    if ($csv != "yes") {
                        print "<td><a title='Late In'><font face='Verdana' size='1'>" . $li . "</font></a></td>";
                    } else {
                        print $li . ";";
                    }
                }
                if (strpos($ras, "-EO-") !== false) {
                    if ($csv != "yes") {
                        print "<td><a title='Early Out'><font face='Verdana' size='1'>" . $eo . "</font></a></td>";
                    } else {
                        print $eo . ";";
                    }
                }
                if (strpos($ras, "-MB-") !== false) {
                    if ($csv != "yes") {
                        print "<td><a title='More Break'><font face='Verdana' size='1'>" . $mb . "</font></a></td>";
                    } else {
                        print $mb . ";";
                    }
                }
                if (strpos($ras, "-WKH-") !== false && $lstReportType == "Custom8") {
                    if ($csv != "yes") {
                        round($h_wkdao / 3600, 2);
                        print "<td><a title='Week Days Approved OT Hours'><font face='Verdana' size='1'>" . round($h_wkdao / 3600, 2) . "</font></a></td>";
                    } else {
                        round($h_wkdao / 3600, 2);
                        print round($h_wkdao / 3600, 2) . ";";
                    }
                }
                if (strpos($ras, "-WKH-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom8" && $lstReportType != "Custom9") {
                    if ($csv != "yes") {
                        round($h_wkdn / 3600, 2);
                        round($h_wkdo / 3600, 2);
                        round($h_wkdao / 3600, 2);
                        print "<td><a title='Week Days Normal Hours'><font face='Verdana' size='1'>" . round($h_wkdn / 3600, 2) . "</font></a></td> <td><a title='Week Days OT Hours'><font face='Verdana' size='1'>" . round($h_wkdo / 3600, 2) . "</font></a></td> <td><a title='Week Days Approved OT Hours'><font face='Verdana' size='1'>" . round($h_wkdao / 3600, 2) . "</font></a></td>";
                    } else {
                        round($h_wkdn / 3600, 2);
                        round($h_wkdo / 3600, 2);
                        round($h_wkdao / 3600, 2);
                        print round($h_wkdn / 3600, 2) . ";" . round($h_wkdo / 3600, 2) . ";" . round($h_wkdao / 3600, 2) . ";";
                    }
                }
                if (strpos($ras, "-PXH-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom8" && $lstReportType != "Custom9") {
                    if ($csv != "yes") {
                        round($h_pxyn / 3600, 2);
                        round($h_pxyo / 3600, 2);
                        round($h_pxyao / 3600, 2);
                        print "<td><a title='Proxy Days Normal Hours'><font face='Verdana' size='1'>" . round($h_pxyn / 3600, 2) . "</font></a></td> <td><a title='Proxy Days OT Hours'><font face='Verdana' size='1'>" . round($h_pxyo / 3600, 2) . "</font></a></td> <td><a title='Proxy Days Approved OT Hours'><font face='Verdana' size='1'>" . round($h_pxyao / 3600, 2) . "</font></a></td>";
                    } else {
                        round($h_pxyn / 3600, 2);
                        round($h_pxyo / 3600, 2);
                        round($h_pxyao / 3600, 2);
                        print round($h_pxyn / 3600, 2) . ";" . round($h_pxyo / 3600, 2) . ";" . round($h_pxyao / 3600, 2) . ";";
                    }
                }
                if (strpos($ras, "-WKH+PXH-") !== false && $lstReportType != "Custom9") {
                    if ($csv != "yes") {
                        round(($h_wkdn + $h_pxyn) / 3600, 2);
                        round(($h_wkdo + $h_pxyo) / 3600, 2);
                        round(($h_wkdao + $h_pxyao) / 3600, 2);
                        print "<td><a title='Week+Proxy Days Normal Hours'><font face='Verdana' size='1'>" . round(($h_wkdn + $h_pxyn) / 3600, 2) . "</font></a></td> <td><a title='Week+Proxy Days OT Hours'><font face='Verdana' size='1'>" . round(($h_wkdo + $h_pxyo) / 3600, 2) . "</font></a></td> <td><a title='Week+Proxy Days Approved OT Hours'><font face='Verdana' size='1'>" . round(($h_wkdao + $h_pxyao) / 3600, 2) . "</font></a></td>";
                    } else {
                        round(($h_wkdn + $h_pxyn) / 3600, 2);
                        round(($h_wkdo + $h_pxyo) / 3600, 2);
                        round(($h_wkdao + $h_pxyao) / 3600, 2);
                        print round(($h_wkdn + $h_pxyn) / 3600, 2) . ";" . round(($h_wkdo + $h_pxyo) / 3600, 2) . ";" . round(($h_wkdao + $h_pxyao) / 3600, 2) . ";";
                    }
                }
                if (strpos($ras, "-FLH-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom8" && $lstReportType != "Custom9") {
                    if ($csv != "yes") {
                        round($h_flgn / 3600, 2);
                        round($h_flgo / 3600, 2);
                        round($h_flgao / 3600, 2);
                        print "<td><a title='Flag Days Normal Hours'><font face='Verdana' size='1'>" . round($h_flgn / 3600, 2) . "</font></a></td> <td><a title='Flag Days OT Hours'><font face='Verdana' size='1'>" . round($h_flgo / 3600, 2) . "</font></a></td> <td><a title='Flag Days Approved OT Hours'><font face='Verdana' size='1'>" . round($h_flgao / 3600, 2) . "</font></a></td>";
                    } else {
                        round($h_flgn / 3600, 2);
                        round($h_flgo / 3600, 2);
                        round($h_flgao / 3600, 2);
                        print round($h_flgn / 3600, 2) . ";" . round($h_flgo / 3600, 2) . ";" . round($h_flgao / 3600, 2) . ";";
                    }
                }
                if (strpos($ras, "-SATH-") !== false && $lstReportType == "Custom8" && $lstReportType != "Custom9") {
                    if ($csv != "yes") {
                        round($h_satao / 3600, 2);
                        print "<td><a title='Saturdays Approved OT Hours'><font face='Verdana' size='1'>" . round($h_satao / 3600, 2) . "</font></a></td>";
                    } else {
                        round($h_satao / 3600, 2);
                        print round($h_satao / 3600, 2) . ";";
                    }
                }
                if (strpos($ras, "-SATH-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom8" && $lstReportType != "Custom9") {
                    if ($csv != "yes") {
                        round($h_satn / 3600, 2);
                        round($h_sato / 3600, 2);
                        round($h_satao / 3600, 2);
                        print "<td><a title='Saturdays Normal Hours'><font face='Verdana' size='1'>" . round($h_satn / 3600, 2) . "</font></a></td> <td><a title='Saturdays OT Hours'><font face='Verdana' size='1'>" . round($h_sato / 3600, 2) . "</font></a></td> <td><a title='Saturdays Approved OT Hours'><font face='Verdana' size='1'>" . round($h_satao / 3600, 2) . "</font></a></td>";
                    } else {
                        round($h_satn / 3600, 2);
                        round($h_sato / 3600, 2);
                        round($h_satao / 3600, 2);
                        print round($h_satn / 3600, 2) . ";" . round($h_sato / 3600, 2) . ";" . round($h_satao / 3600, 2) . ";";
                    }
                }
                if (strpos($ras, "-WKH+PXH+SATH-") !== false && $lstReportType != "Custom9") {
                    if ($csv != "yes") {
                        round(($h_wkdn + $h_pxyn + $h_satn) / 3600, 2);
                        round(($h_wkdo + $h_pxyo + $h_sato) / 3600, 2);
                        round(($h_wkdao + $h_pxyao + $h_satao) / 3600, 2);
                        print "<td><a title='Week+Proxy+SAT Days Normal Hours'><font face='Verdana' size='1'>" . round(($h_wkdn + $h_pxyn + $h_satn) / 3600, 2) . "</font></a></td> <td><a title='Week+Proxy+SAT Days OT Hours'><font face='Verdana' size='1'>" . round(($h_wkdo + $h_pxyo + $h_sato) / 3600, 2) . "</font></a></td> <td><a title='Week+Proxy+SAT Days Approved OT Hours'><font face='Verdana' size='1'>" . round(($h_wkdao + $h_pxyao + $h_satao) / 3600, 2) . "</font></a></td>";
                    } else {
                        round(($h_wkdn + $h_pxyn + $h_satn) / 3600, 2);
                        round(($h_wkdo + $h_pxyo + $h_sato) / 3600, 2);
                        round(($h_wkdao + $h_pxyao + $h_satao) / 3600, 2);
                        print round(($h_wkdn + $h_pxyn + $h_satn) / 3600, 2) . ";" . round(($h_wkdo + $h_pxyo + $h_sato) / 3600, 2) . ";" . round(($h_wkdao + $h_pxyao + $h_satao) / 3600, 2) . ";";
                    }
                }
                if (strpos($ras, "-SUNH-") !== false && $lstReportType == "Custom8" && $lstReportType != "Custom9") {
                    if ($csv != "yes") {
                        round($h_sunao / 3600, 2);
                        print "<td><a title='Sundays Approved OT Hours'><font face='Verdana' size='1'>" . round($h_sunao / 3600, 2) . "</font></a></td>";
                    } else {
                        round($h_sunao / 3600, 2);
                        print round($h_sunao / 3600, 2) . ";";
                    }
                }
                if (strpos($ras, "-SUNH-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom8" && $lstReportType != "Custom9") {
                    if ($csv != "yes") {
                        round($h_sunn / 3600, 2);
                        round($h_suno / 3600, 2);
                        round($h_sunao / 3600, 2);
                        print "<td><a title='Sundays Normal Hours'><font face='Verdana' size='1'>" . round($h_sunn / 3600, 2) . "</font></a></td> <td><a title='Sundays OT Hours'><font face='Verdana' size='1'>" . round($h_suno / 3600, 2) . "</font></a></td> <td><a title='Sundays Approved OT Hours'><font face='Verdana' size='1'>" . round($h_sunao / 3600, 2) . "</font></a></td>";
                    } else {
                        round($h_sunn / 3600, 2);
                        round($h_suno / 3600, 2);
                        round($h_sunao / 3600, 2);
                        print round($h_sunn / 3600, 2) . ";" . round($h_suno / 3600, 2) . ";" . round($h_sunao / 3600, 2) . ";";
                    }
                }
                if(strpos($ras, "-TLH-") !== false && $lstReportType != "Custom8") {
                    if ($csv != "yes") {
                        if($lstReportType == "Custom7" && $lstReportType != "Custom9"){
                            round(($h_wkdo + $h_pxyo + $h_flgo + $h_sato + $h_suno) / 3600, 2);
                            print "<td bgcolor='#F0F0F0'><a title='Total OT Hours'><font face='Verdana' size='1'>" . round(($h_wkdo + $h_pxyo + $h_flgo + $h_sato + $h_suno) / 3600, 2) . "</font></a></td> <td bgcolor='#F0F0F0'><a title='Total Approved OT Hours'><font face='Verdana' size='1'>";
                            if ($lstReportType == "Custom4" && round(($h_wkdn + $h_pxyn + $h_flgn + $h_satn + $h_sunn) / 3600, 2) < ($dayCount - $sunCount - $satCount) * 8) {
                                print "0";
                            } else {
                                round(($h_wkdao + $h_pxyao + $h_flgao + $h_satao + $h_sunao) / 3600, 2);
                                print round(($h_wkdao + $h_pxyao + $h_flgao + $h_satao + $h_sunao) / 3600, 2);
                            }
                        }else{
                            if($lstReportType != "Custom9"){
                            round(($h_wkdn + $h_pxyn + $h_flgn + $h_satn + $h_sunn) / 3600, 2);
                            print "<td bgcolor='#F0F0F0'><a title='Total Normal Hours'><font face='Verdana' size='1'>" . round(($h_wkdn + $h_pxyn + $h_flgn + $h_satn + $h_sunn) / 3600, 2) . "</font></a></td>";
                            round(($h_wkdo + $h_pxyo + $h_flgo + $h_sato + $h_suno) / 3600, 2);
                            print "<td bgcolor='#F0F0F0'><a title='Total OT Hours'><font face='Verdana' size='1'>" . round(($h_wkdo + $h_pxyo + $h_flgo + $h_sato + $h_suno) / 3600, 2) . "</font></a></td> <td bgcolor='#F0F0F0'><a title='Total Approved OT Hours'><font face='Verdana' size='1'>";
                            if ($lstReportType == "Custom4" && round(($h_wkdn + $h_pxyn + $h_flgn + $h_satn + $h_sunn) / 3600, 2) < ($dayCount - $sunCount - $satCount) * 8) {
                                print "0";
                            } else {
                                round(($h_wkdao + $h_pxyao + $h_flgao + $h_satao + $h_sunao) / 3600, 2);
                                print round(($h_wkdao + $h_pxyao + $h_flgao + $h_satao + $h_sunao) / 3600, 2);
                            }
                            }
                        }
                        print "</font></a></td>";
                    } else {
                        if($lstReportType == "Custom7"){
                            round(($h_wkdo + $h_pxyo + $h_flgo + $h_sato + $h_suno) / 3600, 2);
                            print round(($h_wkdo + $h_pxyo + $h_flgo + $h_sato + $h_suno) / 3600, 2) . ";";
                            if ($lstReportType == "Custom4" && round(($h_wkdn + $h_pxyn + $h_flgn + $h_satn + $h_sunn) / 3600, 2) < ($dayCount - $sunCount - $satCount) * 8) {
                                print "0;";
                            } else {
                                round(($h_wkdao + $h_pxyao + $h_flgao + $h_satao + $h_sunao) / 3600, 2);
                                print round(($h_wkdao + $h_pxyao + $h_flgao + $h_satao + $h_sunao) / 3600, 2) . ";";
                            }
                        }else{
                        round(($h_wkdn + $h_pxyn + $h_flgn + $h_satn + $h_sunn) / 3600, 2);
                        print round(($h_wkdn + $h_pxyn + $h_flgn + $h_satn + $h_sunn) / 3600, 2) . ";";
                        round(($h_wkdo + $h_pxyo + $h_flgo + $h_sato + $h_suno) / 3600, 2);
                        print round(($h_wkdo + $h_pxyo + $h_flgo + $h_sato + $h_suno) / 3600, 2) . ";";
                        if ($lstReportType == "Custom4" && round(($h_wkdn + $h_pxyn + $h_flgn + $h_satn + $h_sunn) / 3600, 2) < ($dayCount - $sunCount - $satCount) * 8) {
                            print "0;";
                        } else {
                            round(($h_wkdao + $h_pxyao + $h_flgao + $h_satao + $h_sunao) / 3600, 2);
                            print round(($h_wkdao + $h_pxyao + $h_flgao + $h_satao + $h_sunao) / 3600, 2) . ";";
                        }
                        }
                    }
                }
                if (strpos($ras, "-NSH-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom8" && $lstReportType != "Custom9") {
                    if ($csv != "yes") {
                        round($h_nsn / 3600, 2);
                        round($h_nso / 3600, 2);
                        round($h_nsao / 3600, 2);
                        print "<td><a title='Un Flagged Night Shift Normal Hours'><font face='Verdana' size='1'>" . round($h_nsn / 3600, 2) . "</font></a></td> <td><a title='Un Flagged Night Shift OT Hours'><font face='Verdana' size='1'>" . round($h_nso / 3600, 2) . "</font></a></td> <td><a title='Un Flagged Night Shift Approved OT Hours'><font face='Verdana' size='1'>" . round($h_nsao / 3600, 2) . "</font></a></td>";
                    } else {
                        round($h_nsn / 3600, 2);
                        round($h_nso / 3600, 2);
                        round($h_nsao / 3600, 2);
                        print round($h_nsn / 3600, 2) . ";" . round($h_nso / 3600, 2) . ";" . round($h_nsao / 3600, 2) . ";";
                    }
                }
                if (strpos($ras, "-LIH-") !== false) {
                    if ($csv != "yes") {
                        if ($lstReportType == "Custom6") {
                            round($h_li / 60, 2);
                            print "<td><a title='Total Late In Minutes'><font face='Verdana' size='1'>" . round($h_li / 60, 2) . "</font></a></td>";
                        } else {
                            round($h_li / 3600, 2);
                            print "<td><a title='Total Late In Hours'><font face='Verdana' size='1'>" . round($h_li / 3600, 2) . "</font></a></td>";
                        }
                    } else {
                        if ($lstReportType == "Custom6") {
                            round($h_li / 60, 2);
                            print round($h_li / 60, 2) . ";";
                        } else {
                            round($h_li / 3600, 2);
                            print round($h_li / 3600, 2) . ";";
                        }
                    }
                }
                if (strpos($ras, "-EOH-") !== false) {
                    if ($csv != "yes") {
                        round($h_eo / 3600, 2);
                        print "<td><a title='Total Early Out Hours'><font face='Verdana' size='1'>" . round($h_eo / 3600, 2) . "</font></a></td>";
                    } else {
                        round($h_eo / 3600, 2);
                        print round($h_eo / 3600, 2) . ";";
                    }
                }
                if (strpos($ras, "-MBH-") !== false) {
                    if ($csv != "yes") {
                        round($h_mb / 3600, 2);
                        print "<td><a title='Total More Break Hours'><font face='Verdana' size='1'>" . round($h_mb / 3600, 2) . "</font></a></td>";
                    } else {
                        round($h_mb / 3600, 2);
                        print round($h_mb / 3600, 2) . ";";
                    }
                }
                if ($lstReportType == "Basic/Net") {
                    if ($csv != "yes") {
                        addComma($v_basic[$eid]);
                        print "<td><font face='Verdana' size='1'>" . addComma($v_basic[$eid]) . "</font></td>";
                        addComma($v_net[$eid]);
                        print "<td><font face='Verdana' size='1'>" . addComma($v_net[$eid]) . "</font></td>";
                    } else {
                        addComma($v_basic[$eid]);
                        print addComma($v_basic[$eid]) . ";";
                        addComma($v_net[$eid]);
                        print addComma($v_net[$eid]) . ";";
                    }
                }
                if ($lstReportType == "Custom5") {
                    if ($csv != "yes") {
                        print "<td align='center'><font face='Verdana' size='1'><b>P</b></font></td>";
                    } else {
                        print "P;";
                    }
                }
                if ($csv != "yes") {
                    print "</tr>";
                } else {
                    print "\n";
                }
                $row_count++;
            }
        }
    }
    if (strpos($ras, "-TLD-") !== false && $lstReportType != "Custom5" && $lstReportType != "Custom3" && $lstReportType != "Custom7" && $lstReportType != "Custom6" && $lstReportType != "Custom8" && $lstReportType != "Custom9" && $lstReportType != "Custom10") {
        $query = "SELECT tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tuser.OT1, tuser.OT2, tuser.idno, tuser.remark FROM tuser, tgroup WHERE tuser.group_id = tgroup.id  " . $_SESSION[$session_variable . "DeptAccessQuery"] . " " . $_SESSION[$session_variable . "DivAccessQuery"] . " ";
        if ($lstShift != "") {
            $query = $query . " AND tgroup.id = " . $lstShift;
        }
        $query = displayQueryFields($query, $lstDepartment, $lstDivision, $lstEmployeeIDFrom, $lstEmployeeIDTo, $txtEmployeeCode, $txtEmployee, $txtSNo, $txtRemark, $txtPhone, $lstGroup, $conn, $txtF1, $txtF2, $txtF3, $txtF4, $txtF5, $txtF6, $txtF7, $txtF8, $txtF9, $txtF10);
        $query .= " AND tuser.id NOT IN (SELECT " . $table_name . ".EmployeeID FROM " . $table_name . " WHERE " . $table_name . ".ADate >= " . insertDate($txtFrom) . " AND " . $table_name . ".ADate <= " . insertDate($txtTo) . ") ";
        $query .= employeeStatusQuery($lstEmployeeStatus);
        $result = mysqli_query($conn, $query);
        while ($cur = mysqli_fetch_row($result)) {
            if ($cur[5] == "") {
                $cur[5] = "Saturday";
            }
            if ($cur[6] == "") {
                $cur[6] = "Sunday";
            }
            $satCount = getDayCount(insertDate($txtFrom), insertDate($txtTo), $dayCount, $cur[5]);
            $sunCount = getDayCount(insertDate($txtFrom), insertDate($txtTo), $dayCount, $cur[6]);
            if ($csv != "yes") {
                print "<tr>";
                addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
                print "<td><font face='Verdana' size='1' color='#000000'>" . addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . "</font></a></td> <td><a title='Name'><font face='Verdana' size='1'>";
            } else {
                addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]);
                print addZero($cur[0], $_SESSION[$session_variable . "EmployeeCodeLength"]) . ";";
            }
            if ($cur[0] == $cur[1] && strpos($userlevel, "11D") !== false) {
                $pax_query = "SELECT N1, N2, N3, N4, N5, N6, N7, N8, N9, N0 FROM PAX WHERE id = " . $cur[0] * 7;
                $pax_result = selectData($conn, $pax_query);
                if ($pax_result[0] != "") {
                    for ($pax_i = 0; $pax_i < 10; $pax_i++) {
                        strrev($pax_result[$pax_i]);
                        print strrev($pax_result[$pax_i]);
                    }
                } else {
                    print $cur[1];
                }
            } else {
                print $cur[1];
            }
            if ($lstReportType == "Custom1") {
                if ($csv != "yes") {
                    print "</font></a></td><td><a title='Dept'><font face='Verdana' size='1'>" . $cur[2] . "</font></a></td>";
                    print "<td><font face='Verdana' size='2'>0</font></td>";
                    print "<td><font face='Verdana' size='1'>" . ($dayCount - $satCount - $sunCount) . "</font></td>";
                } else {
                    print $cur[2] . ";0;" . ($dayCount - $satCount - $sunCount) . ";";
                }
                $t_no_abdays = $t_no_abdays + $dayCount - $satCount - $sunCount;
                for ($i = 0; $i < 12; $i++) {
                    if ($csv != "yes") {
                        print "<td><font face='Verdana' size='1'>0</font></td>";
                    } else {
                        print "0;";
                    }
                }
                if ($csv != "yes") {
                    print "</tr>";
                } else {
                    print "\n";
                }
            } else {
                if ($csv != "yes") {
                    print "</font></a></td>";
                }
                if ($lstReportType != "Custom2" && $lstReportType != "Custom5") {
                    if ($csv != "yes") {
                        print "<td><a title='" . $_SESSION[$session_variable . "IDColumnName"] . "'><font face='Verdana' size='1'>" . $cur[7] . "</font></a></td>";
                    } else {
                        print $cur[7] . ";";
                    }
                }
                if ($lstReportType != "Custom5") {
                    if ($csv != "yes") {
                        print "<td><a title='Dept'><font face='Verdana' size='1'>" . $cur[2] . "</font></a></td>";
                    } else {
                        print $cur[2] . ";";
                    }
                }
                if ($lstReportType != "Custom2" && $lstReportType != "Custom5") {
                    if ($csv != "yes") {
                        print "<td><a title='Div/Desg'><font face='Verdana' size='1'>" . $cur[3] . "</font></a></td>";
                    } else {
                        print $cur[3] . ";";
                    }
                }
                if ($lstReportType != "Custom6" && $lstReportType != "Custom7" && $lstReportType != "Custom8" && $lstReportType != "Custom9" && $lstReportType != "Custom10") {
                    if ($csv != "yes") {
                        print "<td><a title='Rmk'><font face='Verdana' size='1'>" . $cur[8] . "</font></a></td> ";
                    } else {
                        print $cur[8] . ";";
                    }
                }
                if (strpos($ras, "-V-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom9") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Violet Days'><font face='Verdana' size='1' color='Violet'>0</font></a></td>";
                    } else {
                        print "0;";
                    }
                }
                if (strpos($ras, "-I-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom8" && $lstReportType != "Custom9") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Indigo Days'><font face='Verdana' size='1' color='Indigo'>0</font></a></td>";
                    } else {
                        print "0;";
                    }
                }
                if (strpos($ras, "-B-") !== false && $lstReportType != "Custom7") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Blue Days'><font face='Verdana' size='1' color='Blue'>0</font></a></td>";
                    } else {
                        print "0;";
                    }
                }
                if (strpos($ras, "-G-") !== false && $lstReportType != "Custom7") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Green Days'><font face='Verdana' size='1' color='Green'>0</font></a></td>";
                    } else {
                        print "0;";
                    }
                }
                if (strpos($ras, "-Y-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom9") {
                    if ($csv != "yes") {
                        print "<td bgcolor='Brown'><a title='Total Yellow Days'><font face='Verdana' size='1' color='Yellow'>0</font></a></td>";
                    } else {
                        print "0;";
                    }
                }
                if (strpos($ras, "-O-") !== false && $lstReportType != "Custom7") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Orange Days'><font face='Verdana' size='1' color='Orange'>0</font></a></td>";
                    } else {
                        print "0;";
                    }
                }
                if (strpos($ras, "-R-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom9") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Red Days'><font face='Verdana' size='1' color='Red'>0</font></a></td>";
                    } else {
                        print "0;";
                    }
                }
                if (strpos($ras, "-GR-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom8" && $lstReportType != "Custom9") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Green Days'><font face='Verdana' size='1' color='Gray'>0</font></a></td>";
                    } else {
                        print "0;";
                    }
                }
                if (strpos($ras, "-BR-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom8" && $lstReportType != "Custom9") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Brown Days'><font face='Verdana' size='1' color='Brown'>0</font></a></td>";
                    } else {
                        print "0;";
                    }
                }
                if (strpos($ras, "-PR-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom9") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Purple Days'><font face='Verdana' size='1' color='Purple'>0</font></a></td>";
                    } else {
                        print "0;";
                    }
                }
                if (strpos($ras, "-MG-") !== false && $lstReportType != "Custom7") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Magenta Days'><font face='Verdana' size='1' color='Magenta'>0</font></a></td>";
                    } else {
                        print "0;";
                    }
                }
                if (strpos($ras, "-TL-") !== false && $lstReportType != "Custom7") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Teal Days'><font face='Verdana' size='1' color='Teal'>0</font></a></td>";
                    } else {
                        print "0;";
                    }
                }
                if (strpos($ras, "-AQ-") !== false && $lstReportType != "Custom7") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Aqua Days'><font face='Verdana' size='1' color='Aqua'>0</font></a></td>";
                    } else {
                        print "0;";
                    }
                }
                if (strpos($ras, "-SF-") !== false && $lstReportType != "Custom7") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Safron Days'><font face='Verdana' size='1' color='Safron'>0</font></a></td>";
                    } else {
                        print "0;";
                    }
                }
                if (strpos($ras, "-AM-") !== false && $lstReportType != "Custom7") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Amber Days'><font face='Verdana' size='1' color='Amber'>0</font></a></td>";
                    } else {
                        print "0;";
                    }
                }
                if (strpos($ras, "-GL-") !== false && $lstReportType != "Custom7") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Golden Days'><font face='Verdana' size='1' color='Golden'>0</font></a></td>";
                    } else {
                        print "0;";
                    }
                }
                if (strpos($ras, "-VM-") !== false && $lstReportType != "Custom7") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Vermilion Days'><font face='Verdana' size='1' color='Vermilion'>0</font></a></td>";
                    } else {
                        print "0;";
                    }
                }
                if (strpos($ras, "-SL-") !== false && $lstReportType != "Custom7") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Silver Days'><font face='Verdana' size='1' color='Silver'>0</font></a></td>";
                    } else {
                        print "0;";
                    }
                }
                if (strpos($ras, "-MR-") !== false && $lstReportType != "Custom7") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Maroon Days'><font face='Verdana' size='1' color='Maroon'>0</font></a></td>";
                    } else {
                        print "0;";
                    }
                }
                if (strpos($ras, "-PK-") !== false && $lstReportType != "Custom7") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Pink Days'><font face='Verdana' size='1' color='Pink'>0</font></a></td>";
                    } else {
                        print "0;";
                    }
                }
                
                if (strpos($ras, "-WKD-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom8") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Week Days'><font face='Verdana' size='1'>0</font></a></td>";
                    } else {
                        print "0;";
                    }
                }
                if (strpos($ras, "-PXY-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom8" && $lstReportType != "Custom9") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Proxy Days'><font face='Verdana' size='1'>0</font></a></td>";
                    } else {
                        print "0;";
                    }
                }
                if (strpos($ras, "-FLG-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom9") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Flag Days'><font face='Verdana' size='1'>0</font></a></td>";
                    } else {
                        print "0;";
                    }
                }
                if (strpos($ras, "-SAT-") !== false && $lstReportType == "Custom9") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Saturdays and Sundays'><font face='Verdana' size='1'>0</font></a></td>";
                    } else {
                        print "0;";
                    }
                }
                if (strpos($ras, "-SAT-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom8" && $lstReportType != "Custom9") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Saturdays'><font face='Verdana' size='1'>0</font></a></td>";
                    } else {
                        print "0;";
                    }
                }
                if (strpos($ras, "-SUN-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom8" && $lstReportType != "Custom9") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Sundays'><font face='Verdana' size='1'>0</font></a></td>";
                    } else {
                        print "0;";
                    }
                }
                if (strpos($ras, "-TLD-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom9") {
                    if ($csv != "yes") {
                        print "<td bgcolor='#F0F0F0'><a title='Total Days Present (Excluding the Flags SET be Ignored in Global Settings)'><font face='Verdana' size='1'>0</font></a></td>";
                    } else {
                        print "0;";
                    }
//                    if ($lstReportType == "Custom9") {
//                        if ($csv != "yes") {
//                            print "<td bgcolor='#F0F0F0'><a title='Total Days Absent'><font face='Verdana' size='1'>" . $dayCount . "</font></a></td>";
//                            print "<td bgcolor='#F0F0F0'><a title='Total Days Absent EXCLUDING Sundays'><font face='Verdana' size='1'>" . ($dayCount - $sunCount) . "</font></a></td>";
//                        } else {
//                            print $dayCount . ";" . ($dayCount - $sunCount) . ";";
//                        }
//                    }
                    if ($lstReportType != "Custom2") {
                        if ($csv != "yes") {
                            print "<td bgcolor='#F0F0F0'><a title='Total Days Absent'><font face='Verdana' size='1'>" . $dayCount . "</font></a></td>";
                            if($lstReportType != "Custom9"){
                                print "<td bgcolor='#F0F0F0'><a title='Total Days Absent EXCLUDING Sundays'><font face='Verdana' size='1'>" . ($dayCount - $sunCount) . "</font></a></td>";
                            }
                        } else {
                            print $dayCount . ";" . ($dayCount - $sunCount) . ";";
                        }
                    }
                    if ($csv != "yes") {
                        if($lstReportType != "Custom9"){
                            print "<td bgcolor='#F0F0F0'><a title='Total Days Absent EXCLUDING Sundays and Saturdays'><font face='Verdana' size='1'>" . ($dayCount - $sunCount - $satCount) . "</font></a></td>";
                            print "<td bgcolor='#F0F0F0'><a title='Total Weekdays'><font face='Verdana' size='1'>" . ($dayCount - $sunCount - $satCount) . "</font></a></td>";
                        }
                    } else {
                        print $dayCount - $sunCount - $satCount . ";" . ($dayCount - $sunCount - $satCount) . ";";
                    }
                }
                if (strpos($ras, "-NS-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom8" && $lstReportType != "Custom9") {
                    if ($csv != "yes") {
                        print "<td><a title='Total Night Shifts'><font face='Verdana' size='1'>0</font></a></td>";
                    } else {
                        print "0;";
                    }
                }
                if (strpos($ras, "-LI-") !== false) {
                    if ($csv != "yes") {
                        print "<td><a title='Late In'><font face='Verdana' size='1'>0</font></a></td>";
                    } else {
                        print "0;";
                    }
                }
                if (strpos($ras, "-EO-") !== false) {
                    if ($csv != "yes") {
                        print "<td><a title='Early Out'><font face='Verdana' size='1'>0</font></a></td>";
                    } else {
                        print "0;";
                    }
                }
                if (strpos($ras, "-MB-") !== false) {
                    if ($csv != "yes") {
                        print "<td><a title='More Break'><font face='Verdana' size='1'>0</font></a></td>";
                    } else {
                        print "0;";
                    }
                }
                if (strpos($ras, "-WKH-") !== false && $lstReportType == "Custom8" && $lstReportType != "Custom9") {
                    if ($csv != "yes") {
                        print "<td><a title='Week Days Approved OT Hours'><font face='Verdana' size='1'>0</font></a></td>";
                    } else {
                        print "0;";
                    }
                }
                if (strpos($ras, "-WKH-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom8" && $lstReportType != "Custom9") {
                    if ($csv != "yes") {
                        print "<td><a title='Week Days Normal Hours'><font face='Verdana' size='1'>0</font></a></td> <td><a title='Week Days OT Hours'><font face='Verdana' size='1'>0</font></a></td> <td><a title='Week Days Approved OT Hours'><font face='Verdana' size='1'>0</font></a></td>";
                    } else {
                        print "0;0;0;";
                    }
                }
                if (strpos($ras, "-PXH-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom8" && $lstReportType != "Custom9") {
                    if ($csv != "yes") {
                        print "<td><a title='Proxy Days Normal Hours'><font face='Verdana' size='1'>0</font></a></td> <td><a title='Proxy Days OT \tHours'><font face='Verdana' size='1'>0</font></a></td> <td><a title='Proxy Days Approved OT Hours'><font face='Verdana' size='1'>0</font></a></td>";
                    } else {
                        print "0;0;0;";
                    }
                }
                if (strpos($ras, "-FLH-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom8" && $lstReportType != "Custom9") {
                    if ($csv != "yes") {
                        print "<td><a title='Flag Days Normal Hours'><font face='Verdana' size='1'>0</font></a></td> <td><a title='Flag Days OT Hours'><font face='Verdana' size='1'>0</font></a></td> <td><a title='Flag Days Approved OT Hours'><font face='Verdana' size='1'>0</font></a></td>";
                    } else {
                        print "0;0;0;";
                    }
                }
                if (strpos($ras, "-SATH-") !== false && $lstReportType == "Custom8" && $lstReportType != "Custom9") {
                    if ($csv != "yes") {
                        print "<td><a title='Saturdays Approved OT Hours'><font face='Verdana' size='1'>0</font></a></td>";
                    } else {
                        print "0;";
                    }
                }
                if (strpos($ras, "-SATH-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom8" && $lstReportType != "Custom9") {
                    if ($csv != "yes") {
                        print "<td><a title='Saturdays Normal Hours'><font face='Verdana' size='1'>0</font></a></td> <td><a title='Saturdays OT Hours'><font face='Verdana' size='1'>0</font></a></td> <td><a title='Saturdays Approved OT Hours'><font face='Verdana' size='1'>0</font></a></td>";
                    } else {
                        print "0;0;0;";
                    }
                }
                if (strpos($ras, "-SUNH-") !== false && $lstReportType == "Custom8" && $lstReportType != "Custom9") {
                    if ($csv != "yes") {
                        print "<td><a title='Sundays Approved OT Hours'><font face='Verdana' size='1'>0</font></a></td>";
                    } else {
                        print "0;";
                    }
                }
                if (strpos($ras, "-SUNH-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom8") {
                    if ($csv != "yes") {
                        print "<td><a title='Sundays Normal Hours'><font face='Verdana' size='1'>0</font></a></td> <td><a title='Sundays OT Hours'><font face='Verdana' size='1'>0</font></a></td> <td><a title='Sundays Approved OT Hours'><font face='Verdana' size='1'>0</font></a></td>";
                    } else {
                        print "0;0;0;";
                    }
                }
                
                if (strpos($ras, "-TLH-") !== false && $lstReportType != "Custom8") {
                    if ($lstReportType == "Custom7") {
                        if ($csv != "yes") {
                            print "<td bgcolor='#F0F0F0'><a title='Total OT Hours'><font face='Verdana' size='1'>0</font></a></td> <td bgcolor='#F0F0F0'><a title='Total Approved OT Hours'><font face='Verdana' size='1'>0</font></a></td>";
                        } else {
                            print "0;0;";
                        }
                    }else{
                        if($lstReportType != "Custom9"){
                        if ($csv != "yes") {
                            print "<td bgcolor='#F0F0F0'><a title='Total Normal Hours'><font face='Verdana' size='1'>0</font></a></td>";
                            print "<td bgcolor='#F0F0F0'><a title='Total OT Hours'><font face='Verdana' size='1'>0</font></a></td> <td bgcolor='#F0F0F0'><a title='Total Approved OT Hours'><font face='Verdana' size='1'>0</font></a></td>";
                        } else {
                            print "0;0;0;";
                        }
                        }
                    }
                }
                if (strpos($ras, "-NSH-") !== false && $lstReportType != "Custom7" && $lstReportType != "Custom8" && $lstReportType != "Custom9") {
                    if ($csv != "yes") {
                        print "<td><a title='Un Flagged Night Shift Normal Hours'><font face='Verdana' size='1'>0</font></a></td> <td><a title='Un Flagged Night Shift OT Hours'><font face='Verdana' size='1'>0</font></a></td> <td><a title='Un Flagged Night Shift Approved OT Hours'><font face='Verdana' size='1'>0</font></a></td>";
                    } else {
                        print "0;0;0;";
                    }
                }
                if (strpos($ras, "-LIH-") !== false) {
                    if ($csv != "yes") {
                        print "<td><a title='Total Late In Hours'><font face='Verdana' size='1'>0</font></a></td>";
                    } else {
                        print "0;";
                    }
                }
                if (strpos($ras, "-EOH-") !== false) {
                    if ($csv != "yes") {
                        print "<td><a title='Total Early Out Hours'><font face='Verdana' size='1'>0</font></a></td>";
                    } else {
                        print "0;";
                    }
                }
                if (strpos($ras, "-MBH-") !== false) {
                    if ($csv != "yes") {
                        print "<td><a title='Total More Break Hours'><font face='Verdana' size='1'>0</font></a></td>";
                    } else {
                        print "0;";
                    }
                }
                if ($lstReportType == "Basic/Net") {
                    if ($csv != "yes") {
                        print "<td><font face='Verdana' size='1'>0</font></td>";
                        print "<td><font face='Verdana' size='1'>0</font></td>";
                    } else {
                        print "0;0;";
                    }
                }
                if ($lstReportType == "Custom5") {
                    if ($csv != "yes") {
                        print "<td align='center'><font face='Verdana' size='1'><b>A</b></font></td>";
                    } else {
                        print "A;";
                    }
                }
                if ($csv != "yes") {
                    print "</tr>";
                } else {
                    print "\n";
                }
            }
        }
    }
    if ($lstReportType == "Custom1") {
        if ($csv != "yes") {
            print "<tr>";
            print "<td><font face='Verdana' size='1'>&nbsp;</font></td>";
            print "<td><font face='Verdana' size='1'>&nbsp;</font></td>";
            print "<td><font face='Verdana' size='1'>&nbsp;</font></td>";
            print "<td><font face='Verdana' size='1'><b>" . $t_no_days . "</b></font></td>";
            print "<td><font face='Verdana' size='1'><b>" . $t_no_abdays . "</b></font></td>";
            print "<td><font face='Verdana' size='1'><b>" . $t_green . "</b></font></td>";
            print "<td><font face='Verdana' size='1'><b>" . $t_red . "</b></font></td>";
            print "<td><font face='Verdana' size='1'><b>" . $t_blue . "</b></font></td>";
            print "<td><font face='Verdana' size='1'><b>" . $t_gray . "</b></font></td>";
            print "<td><font face='Verdana' size='1'><b>" . $t_ns . "</b></font></td>";
            addComma($t_wkh / 3600, 2);
            print "<td><font face='Verdana' size='1'><b>" . addComma($t_wkh / 3600, 2) . "</b></font></td>";
            print "<td><font face='Verdana' size='1'><b>" . $t_sat . "</b></font></td>";
            addComma($t_sath / 3600, 2);
            print "<td><font face='Verdana' size='1'><b>" . addComma($t_sath / 3600, 2) . "</b></font></td>";
            print "<td><font face='Verdana' size='1'><b>" . $t_sun . "</b></font></td>";
            addComma($t_sunh / 3600, 2);
            print "<td><font face='Verdana' size='1'><b>" . addComma($t_sunh / 3600, 2) . "</b></font></td>";
            print "<td><font face='Verdana' size='1'><b>" . $t_ph . "</b></font></td>";
            addComma($t_phh / 3600, 2);
            print "<td><font face='Verdana' size='1'><b>" . addComma($t_phh / 3600, 2) . "</b></font></td>";
            print "</tr>";
        } else {
            print ";";
            print ";";
            print ";";
            print $t_no_days . ";";
            print $t_no_abdays . ";";
            print $t_green . ";";
            print $t_red . ";";
            print $t_blue . ";";
            print $t_gray . ";";
            print $t_ns . ";";
            addComma($t_wkh / 3600, 2);
            print addComma($t_wkh / 3600, 2) . ";";
            print $t_sat . ";";
            addComma($t_sath / 3600, 2);
            print addComma($t_sath / 3600, 2) . ";";
            print $t_sun . ";";
            addComma($t_sunh / 3600, 2);
            print addComma($t_sunh / 3600, 2) . ";";
            print $t_ph . ";";
            addComma($t_phh / 3600, 2);
            print addComma($t_phh / 3600, 2) . ";";
            print "\n";
        }
    }
    
    if ($csv != "yes") {
        print "</table>";
    }
    if ($excel != "yes") {
        print "<br><p align='center'><font face='Verdana' size='1'>Total Record(s) Displayed: <b>" . $row_count . "</b></font>";
    }
    if ($prints != "yes") {
        print "<br><input type='button' value='Print Report' onClick='checkPrint(0)'>&nbsp;&nbsp;<input type='button' value='Excel' onClick='checkPrint(1)'>";
    }
    if ($csv != "yes") {
        print "</p>";
    }
}
if ($csv != "yes") {
    print "</form></center></body></html>";
}

?>