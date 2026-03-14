<?php


ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "19";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=ReportOKHR.php&message=Session Expired or Security Policy Violated");
}
$conn = mysql_connection("127.0.0.1", "", "root", "namaste");
$act = $_GET["act"];
if ($act == "") {
    $act = $_POST["act"];
}
$prints = $_GET["prints"];
$excel = $_GET["excel"];
$message = $_GET["message"];
if ($message == "") {
    $message = "HR Summary Report";
}
$txtFrom = $_POST["txtFrom"];
$txtTo = $_POST["txtTo"];
if ($txtFrom == "") {
    $txtFrom = displayToday();
}
if ($txtTo == "") {
    $txtTo = displayToday();
}
$txhCount = $_POST["txhCount"];
if ($txhCount == "") {
    $txhCount = $_GET["txhCount"];
}
$v_count = "";
$v_j = 0;
$get_statement = "";
for ($i = 0; $i < $txhCount; $i++) {
    if (0 < strlen($_POST["chkType" . $i]) || 0 < strlen($_GET["chkType" . $i])) {
        if (0 < strlen($_POST["chkType" . $i])) {
            $v_count[$v_j] = $_POST["chkType" . $i];
            $get_statement .= "&chkType" . $i . "=" . $_POST["chkType" . $i];
        } else {
            $v_count[$v_j] = $_GET["chkType" . $i];
            $get_statement .= "&chkType" . $i . "=" . $_GET["chkType" . $i];
        }
        $v_j++;
    }
}
$txtMACAddress = $_SESSION[$session_variable . "MACAddress"];
print "<html><title>Payroll Report Summary</title><style>.f1{font-family:'Verdana';font-size:'10';color:'#000000';}</style> <style>.f2{font-family:'Verdana';font-size:'12';color:'#000000';}</style>";
if ($prints != "yes") {
    print "<body>";
} else {
    if ($excel != "yes") {
        print "<body onLoad='javascript:window.print()'>";
    } else {
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=ReportOKHR.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        print "<body>";
    }
}
if ($excel != "yes") {
    displayHeader($prints, true, false);
}
if ($excel != "yes") {
    print "<p align='center'><font face='Verdana' size='1' color='#339952'><b>" . $message . "</b></font></p>";
}
if ($excel != "yes") {
    print "<table width='100%' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>";
    print "<form name='frm1' method='post' onSubmit='return checkSearch()' action='ReportOKHR.php'><input type='hidden' name='act' value='searchRecord'>";
    print "<tr>";
    displayTextbox("txtFrom", "Date From <font size='1'>(DD/MM/YYYY)</font>: ", $txtFrom, $prints, 12, "25%", "75%");
    print "</tr>";
    print "<tr>";
    displayTextbox("txtTo", "Date To <font size='1'>(DD/MM/YYYY)</font>: ", $txtTo, $prints, 12, "25%", "75%");
    print "</tr>";
    $i = 0;
    if ($txtMACAddress == "78-2B-CB-6A-3C-81") {
        print "<tr><td align='right'><font face='Verdana' size='2'>Type (Plast):</font></td><td><font face='Verdana' size='1'>";
        mysql_select_db("okplast_payroll", $conn);
        $query = "SELECT distinct(group_value) FROM generateddetail ORDER BY UPPER(group_value)";
        for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $i++) {
            if (stripos($get_statement, $cur[0]) !== false) {
                print "<input type='checkbox' name='chkType" . $i . "' value='" . $cur[0] . "' checked>&nbsp;" . $cur[0] . "&nbsp;&nbsp;";
            } else {
                print "<input type='checkbox' name='chkType" . $i . "' value='" . $cur[0] . "'>&nbsp;" . $cur[0] . "&nbsp;&nbsp;";
            }
        }
        print "</font></td></tr>";
    } else {
        print "<tr><td align='right'><font face='Verdana' size='2'>Type (F1):</font></td><td><font face='Verdana' size='1'>";
        mysql_select_db("okfoods_payroll", $conn);
        $query = "SELECT distinct(group_value) FROM generateddetail ORDER BY UPPER(group_value)";
        for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $i++) {
            if (stripos($get_statement, $cur[0]) !== false) {
                print "<input type='checkbox' name='chkType" . $i . "' value='" . $cur[0] . "' checked>&nbsp;" . $cur[0] . "&nbsp;&nbsp;";
            } else {
                print "<input type='checkbox' name='chkType" . $i . "' value='" . $cur[0] . "'>&nbsp;" . $cur[0] . "&nbsp;&nbsp;";
            }
        }
        print "</font></td></tr>";
        print "<tr><td align='right'><font face='Verdana' size='2'>Type (HQ):</font></td><td><font face='Verdana' size='1'>";
        mysql_select_db("okfood2_payroll", $conn);
        $query = "SELECT distinct(group_value) FROM generateddetail ORDER BY UPPER(group_value)";
        for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $i++) {
            if (stripos($get_statement, $cur[0]) !== false) {
                print "<input type='checkbox' name='chkType" . $i . "' value='" . $cur[0] . "' checked>&nbsp;" . $cur[0] . "&nbsp;&nbsp;";
            } else {
                print "<input type='checkbox' name='chkType" . $i . "' value='" . $cur[0] . "'>&nbsp;" . $cur[0] . "&nbsp;&nbsp;";
            }
        }
        print "</font></td></tr>";
        print "<tr><td align='right'><font face='Verdana' size='2'>Type (Sweets):</font></td><td><font face='Verdana' size='1'>";
        mysql_select_db("oksweet_payroll", $conn);
        $query = "SELECT distinct(group_value) FROM generateddetail ORDER BY UPPER(group_value)";
        for ($result = mysqli_query($conn, $query); $cur = mysqli_fetch_row($result); $i++) {
            if (stripos($get_statement, $cur[0]) !== false) {
                print "<input type='checkbox' name='chkType" . $i . "' value='" . $cur[0] . "' checked>&nbsp;" . $cur[0] . "&nbsp;&nbsp;";
            } else {
                print "<input type='checkbox' name='chkType" . $i . "' value='" . $cur[0] . "'>&nbsp;" . $cur[0] . "&nbsp;&nbsp;";
            }
        }
        print "</font></td></tr>";
    }
    print "<input type='hidden' name='txhCount' value='" . $i . "'>";
    $txhCount = $i;
    if ($prints != "yes") {
        print "<tr><td>&nbsp;</td><td><input name='btSearch' type='submit' value='Search Record'>&nbsp;&nbsp;<input type='button' value='Excel' onClick='checkPrint(1)'>&nbsp;&nbsp;<input type='button' value='CSV' onClick='checkPrint(3)'></td></tr></td></tr>";
    }
    print "</table><br>";
    $i = 0;
}
$result_array_name = "";
$result_array_id = "";
$result_array_sign = "";
$result_array_name[0] = "Co-operative";
$result_array_id[0] = "Field29";
$result_array_sign[0] = "-";
$result_array_name[1] = "Net Cash";
$result_array_id[1] = "Netincome";
$result_array_sign[1] = "+";
$result_array_name[2] = "Net Bank";
$result_array_id[2] = "Netincome";
$result_array_sign[2] = "+";
$result_array_name[3] = "Overtime";
$result_array_id[3] = "Education";
$result_array_sign[3] = "+";
$result_array_name[4] = "PAYE";
$result_array_id[4] = "EOSProvision";
$result_array_sign[4] = "-";
$result_array_name[5] = "Pension";
$result_array_id[5] = "CompanyMedicaltaxes";
$result_array_sign[5] = "-";
$result_array_name[6] = "Union Senior";
$result_array_id[6] = "BonusProvision";
$result_array_sign[6] = "-";
$result_array_name[7] = "Union Junior";
$result_array_id[7] = "BonusProvision";
$result_array_sign[7] = "-";
$result_array_name[8] = "Local Levy";
$result_array_id[8] = "CompanyFamily";
$result_array_sign[8] = "-";
$result_array_name[9] = "Association";
$result_array_id[9] = "Field32";
$result_array_sign[9] = "-";
$result_array_name[10] = "Basic";
$result_array_id[10] = "Salary";
$result_array_sign[10] = "+";
$result_array_name[11] = "Housing";
$result_array_id[11] = "Transportation";
$result_array_sign[11] = "+";
$result_array_name[12] = "Transport";
$result_array_id[12] = "Allowance";
$result_array_sign[12] = "+";
$result_array_name[13] = "Meal";
$result_array_id[13] = "TotalHourWork";
$result_array_sign[13] = "+";
$result_array_name[14] = "Bonus/SUN/HOL";
$result_array_id[14] = "Bonus";
$result_array_sign[14] = "+";
$result_array_name[15] = "Bonus";
$result_array_id[15] = "TotalOvertimeMoney";
$result_array_sign[15] = "+";
$result_array_name[16] = "Shift Allowance";
$result_array_id[16] = "Paid_Vacation";
$result_array_sign[16] = "+";
$result_array_name[17] = "Incentive";
$result_array_id[17] = "TotalOvertimeWork";
$result_array_sign[17] = "+";
$result_array_name[18] = "Beverage";
$result_array_id[18] = "Untaxable";
$result_array_sign[18] = "+";
$result_array_name[19] = "Wash";
$result_array_id[19] = "FamillyAllowance";
$result_array_sign[19] = "+";
$result_array_name[20] = "Utility";
$result_array_id[20] = "Laon";
$result_array_sign[20] = "+";
$result_array_name[21] = "Attendance Allowance";
$result_array_id[21] = "Taxation";
$result_array_sign[21] = "+";
$result_array_name[22] = "Attendance Bonus";
$result_array_id[22] = "CompanyEOS";
$result_array_sign[22] = "+";
$result_array_name[23] = "Production Allowance";
$result_array_id[23] = "MedicalTaxes";
$result_array_sign[23] = "+";
$result_array_name[24] = "Production Bonus";
$result_array_id[24] = "OtherTaxes";
$result_array_sign[24] = "+";
$result_array_name[25] = "C_Incentive";
$result_array_id[25] = "Field30";
$result_array_sign[25] = "+";
$result_array_name[26] = "Leave Allowance";
$result_array_id[26] = "Field31";
$result_array_sign[26] = "+";
$result_array_name[27] = "Hot N Cold";
$result_array_id[27] = "Field33";
$result_array_sign[27] = "+";
$result_array_name[28] = "Night Allowance";
$result_array_id[28] = "Field34";
$result_array_sign[28] = "+";
$result_array_name[29] = "Short Pay";
$result_array_id[29] = "Field44";
$result_array_sign[29] = "+";
$result_array_name[30] = "Medical/ Short Pay";
$result_array_id[30] = "Field41";
$result_array_sign[30] = "+";
$result_array_name[31] = "Tax Payback";
$result_array_id[31] = "Field42";
$result_array_sign[31] = "+";
$result_array_name[32] = "Mid Pay";
$result_array_id[32] = "TotalIncome";
$result_array_sign[32] = "-";
$result_array_name[33] = "Fix_Salary/Wk-Day";
$result_array_id[33] = "Fix_Salary";
$result_array_sign[33] = "+";
$result_array_name[34] = "IOU";
$result_array_id[34] = "ExcludedTaxes";
$result_array_sign[34] = "-";
$result_array_name[35] = "Car Loan";
$result_array_id[35] = "TotalSocial";
$result_array_sign[35] = "-";
$result_array_name[36] = "Personal Loan";
$result_array_id[36] = "Field27";
$result_array_sign[36] = "-";
$result_array_name[37] = "House Loan";
$result_array_id[37] = "Field28";
$result_array_sign[37] = "-";
$result_array_name[38] = "Sick";
$result_array_id[38] = "Field36";
$result_array_sign[38] = "-";
$result_array_name[39] = "Absent";
$result_array_id[39] = "Field38";
$result_array_sign[39] = "-";
$result_array_name[40] = "Suspension";
$result_array_id[40] = "Field37";
$result_array_sign[40] = "-";
$result_array_name[41] = "Annual Leave";
$result_array_id[41] = "Field39";
$result_array_sign[41] = "-";
$result_array_name[42] = "Other Deduction";
$result_array_id[42] = "Field40";
$result_array_sign[42] = "-";
$result_array_name[43] = "Exam Leave";
$result_array_id[43] = "Field47";
$result_array_sign[43] = "-";
$result_array_name[44] = "Causal Leave";
$result_array_id[44] = "Field46";
$result_array_sign[44] = "-";
$result_array_name[45] = "Maternity Leave";
$result_array_id[45] = "Field35";
$result_array_sign[45] = "-";
$result_array_name[46] = "Tax (PAYE)";
$result_array_id[46] = "Field48";
$result_array_sign[46] = "-";
$result_array_name[47] = "Pension";
$result_array_id[47] = "Field49";
$result_array_sign[47] = "-";
$result_array_name[48] = "SAT";
$result_array_id[48] = "Retroactive";
$result_array_sign[48] = "+";
$total_array = "";
$total_array[0] = "0";
$total_array[1] = "0";
$total_array[2] = "0";
$total_array[3] = "0";
$total_array[4] = "0";
$total_array[5] = "0";
$total_array[6] = "0";
$total_array[7] = "0";
$total_array[8] = "0";
$total_array[9] = "0";
if ($act == "viewRecordDetails") {
    $txtColumn = $_GET["txtColumn"];
    $txtDB = $_GET["txtDB"];
    $txtFrom = $_GET["txtFrom"];
    $txtTo = $_GET["txtTo"];
    $txtProject = $_GET["txtProject"];
    $txtGroup = $_GET["txtGroup"];
    $txtColumnName = $_GET["txtColumnName"];
    print "<table border='1' cellspacing='-1'>";
    if ($txtDB == "ALLALL") {
        print "<tr><td class='f2'><b>DB</b></td> <td class='f2'><b>Employee ID</b></td> <td class='f2'><b>Employee Name</b></td> <td class='f2'><b>Project</b></td> <td class='f2'><b>Group</b></td> <td class='f2'><b>PFA Admin</b></td> <td class='f2'><b>SSNumber</b></td> <td class='f2'><b>Payment Mode</b></td> <td class='f2'><b>Bank Acc</b></td><td class='f2'><b>Dept</b></td><td class='f2'><b>Sex</b></td>";
        for ($i = 0; $i < count($result_array_name); $i++) {
            if ($result_array_sign[$i] == "+" && $result_array_name[$i] != "Net Bank") {
                if ($result_array_name[$i] == "Net Cash") {
                    print "<td class='f2'><b>Net</b></td>";
                } else {
                    print "<td class='f2'><b>" . $result_array_name[$i] . "</b></td>";
                }
            }
        }
        print "</tr>";
        $company_count = 4;
        $i_count = 1;
        if ($txtMACAddress == "78-2B-CB-6A-3C-81") {
            $company_count = 1;
            $i_count = 0;
        }
        for ($ii = $i_count; $ii < $company_count; $ii++) {
            $txtDB = "";
            $cc = 10;
            switch ($ii) {
                case 0:
                    $txtDB = "okplast_payroll";
                    break;
                case 1:
                    $txtDB = "okfoods_payroll";
                    break;
                case 2:
                    $txtDB = "okfood2_payroll";
                    break;
                case 3:
                    $txtDB = "oksweet_payroll";
                    break;
            }
            mysql_select_db($txtDB, $conn);
            $query = "SELECT generateddetail.EmployeeID, employees.First, generateddetail.Project, generateddetail.group_value, employees.pfa_admin, employees.SSNumber, employees.PaymentMode, employees.Bankaccount, employees.department, generateddetail.gender ";
            for ($j = 0; $j < count($result_array_name); $j++) {
                if ($result_array_sign[$j] == "+" && $result_array_name[$j] != "Net Bank") {
                    $query .= ", SUM(generateddetail." . $result_array_id[$j] . ") ";
                    $cc++;
                }
            }
            if ($v_j == 0) {
                $query .= " FROM generateddetail, generatedmaster, employees WHERE generatedmaster.Id = generateddetail.ID AND generatedmaster.From_Value >=  '" . displayParadoxDate(insertDate($txtFrom)) . " 00:00:00' AND generatedmaster.To_Value <= '" . displayParadoxDate(insertDate($txtTo)) . " 23:59:59' AND generateddetail.EmployeeID = employees.id AND generateddetail.group_value <> 'CAF' AND generateddetail.group_value <> 'CAS' AND generateddetail.group_value <> 'STO' AND generateddetail.group_value <> 'CAP'  AND generateddetail.group_value <> 'TRI'  ";
            } else {
                $query .= " FROM generateddetail, generatedmaster, employees WHERE generatedmaster.Id = generateddetail.ID AND generatedmaster.From_Value >=  '" . displayParadoxDate(insertDate($txtFrom)) . " 00:00:00' AND generatedmaster.To_Value <= '" . displayParadoxDate(insertDate($txtTo)) . " 23:59:59' AND generateddetail.EmployeeID = employees.id AND ( ";
                for ($i = 0; $i < $v_j; $i++) {
                    $query .= " generateddetail.group_value = '" . $v_count[$i] . "' ";
                    if ($i < $v_j - 1) {
                        $query .= " OR ";
                    }
                }
                $query .= " ) ";
            }
            if ($txtProject != "") {
                $query .= " AND generateddetail.Project = '" . $txtProject . "' ";
            }
            if ($txtGroup != "") {
                $query .= " AND generateddetail.GROUP_VALUE LIKE '" . $txtGroup . "%' ";
            }
            $query .= " GROUP BY generateddetail.EmployeeID, employees.First, generateddetail.Project, generateddetail.group_value, employees.pfa_admin, employees.SSNumber, employees.PaymentMode, employees.Bankaccount, employees.department, generateddetail.gender ORDER BY generateddetail.EmployeeID ";
            $result = mysqli_query($conn, $query);
            while ($cur = mysqli_fetch_row($result)) {
                print "<tr>";
                substr($txtDB, 0, strpos($txtDB, "_"));
                print "<td class='f2'>" . substr($txtDB, 0, strpos($txtDB, "_")) . "</td>";
                for ($j = 0; $j < $cc; $j++) {
                    if (9 < $j) {
                        addComma($cur[$j]);
                        print "<td class='f2'>" . addComma($cur[$j]) . "</td>";
                    } else {
                        if ($cur[$j] == "") {
                            $cur[$j] = "&nbsp;";
                        }
                        print "<td class='f2'>" . $cur[$j] . "</td>";
                    }
                }
                print "</tr>";
            }
        }
        print "</table>";
        print "<table border='0' cellspacing='-1'><tr><td>&nbsp;</td></tr></table>";
        print "<table border='1' cellspacing='-1'>";
        print "<tr><td class='f2'><b>DB</b></td> <td class='f2'><b>Employee ID</b></td> <td class='f2'><b>Employee Name</b></td> <td class='f2'><b>Project</b></td> <td class='f2'><b>Group</b></td> <td class='f2'><b>PFA Admin</b></td> <td class='f2'><b>SSNumber</b></td> <td class='f2'><b>Payment Mode</b></td> <td class='f2'><b>Bank Acc</b></td><td class='f2'><b>Dept</b></td><td class='f2'><b>Sex</b></td>";
        for ($i = 0; $i < count($result_array_name); $i++) {
            if ($result_array_sign[$i] == "-") {
                print "<td class='f2'><b>" . $result_array_name[$i] . "</b></td>";
            }
        }
        print "</tr>";
        $company_count = 4;
        $i_count = 1;
        if ($txtMACAddress == "78-2B-CB-6A-3C-81") {
            $company_count = 1;
            $i_count = 0;
        }
        for ($ii = $i_count; $ii < $company_count; $ii++) {
            $txtDB = "";
            $cc = 10;
            switch ($ii) {
                case 0:
                    $txtDB = "okplast_payroll";
                    break;
                case 1:
                    $txtDB = "okfoods_payroll";
                    break;
                case 2:
                    $txtDB = "okfood2_payroll";
                    break;
                case 3:
                    $txtDB = "oksweet_payroll";
                    break;
            }
            mysql_select_db($txtDB, $conn);
            $query = "SELECT generateddetail.EmployeeID, employees.First, generateddetail.Project, generateddetail.group_value, employees.pfa_admin, employees.SSNumber, employees.PaymentMode, employees.Bankaccount, employees.department, generateddetail.Gender ";
            for ($j = 0; $j < count($result_array_name); $j++) {
                if ($result_array_sign[$j] == "-") {
                    $query .= ", SUM(generateddetail." . $result_array_id[$j] . ") ";
                    $cc++;
                }
            }
            if ($v_j == 0) {
                $query .= " FROM generateddetail, generatedmaster, employees WHERE generatedmaster.Id = generateddetail.ID AND generatedmaster.From_Value >=  '" . displayParadoxDate(insertDate($txtFrom)) . " 00:00:00' AND generatedmaster.To_Value <= '" . displayParadoxDate(insertDate($txtTo)) . " 23:59:59' AND generateddetail.EmployeeID = employees.id AND generateddetail.group_value <> 'CAF' AND generateddetail.group_value <> 'CAS' AND generateddetail.group_value <> 'STO' AND generateddetail.group_value <> 'CAP' AND generateddetail.group_value <> 'TRI'  ";
            } else {
                $query .= " FROM generateddetail, generatedmaster, employees WHERE generatedmaster.Id = generateddetail.ID AND generatedmaster.From_Value >=  '" . displayParadoxDate(insertDate($txtFrom)) . " 00:00:00' AND generatedmaster.To_Value <= '" . displayParadoxDate(insertDate($txtTo)) . " 23:59:59' AND generateddetail.EmployeeID = employees.id AND ( ";
                for ($i = 0; $i < $v_j; $i++) {
                    $query .= " generateddetail.group_value = '" . $v_count[$i] . "' ";
                    if ($i < $v_j - 1) {
                        $query .= " OR ";
                    }
                }
                $query .= " ) ";
            }
            if ($txtProject != "") {
                $query .= " AND generateddetail.Project = '" . $txtProject . "' ";
            }
            if ($txtGroup != "") {
                $query .= " AND generateddetail.GROUP_VALUE LIKE '" . $txtGroup . "%' ";
            }
            $query .= " GROUP BY generateddetail.EmployeeID, employees.First, generateddetail.Project, generateddetail.group_value, employees.pfa_admin, employees.SSNumber, employees.PaymentMode, employees.Bankaccount, employees.department, generateddetail.Gender ORDER BY generateddetail.EmployeeID ";
            $result = mysqli_query($conn, $query);
            while ($cur = mysqli_fetch_row($result)) {
                print "<tr>";
                substr($txtDB, 0, strpos($txtDB, "_"));
                print "<td class='f2'>" . substr($txtDB, 0, strpos($txtDB, "_")) . "</td>";
                for ($j = 0; $j < $cc; $j++) {
                    if (9 < $j) {
                        if ($j == 13 || $j == 14) {
                            if ($j == 13 && substr(strtoupper($cur[3]), 0, 1) == "S" || $j == 14 && substr(strtoupper($cur[3]), 0, 1) == "J") {
                                addComma($cur[$j]);
                                print "<td class='f2'>" . addComma($cur[$j]) . "</td>";
                            } else {
                                print "<td class='f2'>0</td>";
                            }
                        } else {
                            addComma($cur[$j]);
                            print "<td class='f2'>" . addComma($cur[$j]) . "</td>";
                        }
                    } else {
                        if ($cur[$j] == "") {
                            $cur[$j] = "&nbsp;";
                        }
                        print "<td class='f2'>" . $cur[$j] . "</td>";
                    }
                }
                print "</tr>";
            }
        }
    } else {
        print "<tr><td class='f2'><b>Employee ID</b></td> <td class='f2'><b>Employee Name</b></td> <td class='f2'><b>Project</b></td> <td class='f2'><b>Group</b></td>";
        if ($txtColumnName == "Pension") {
            print "<td class='f2'><b>PFA</b></td>";
            print "<td class='f2'><b>PFA No</b></td>";
        } else {
            if ($txtColumnName == "Net Bank") {
                print "<td class='f2'><b>Bank Acc</b></td>";
            }
        }
        print "<td class='f2'><b>" . $txtColumnName . "</b></td><td class='f2'><b>Dept</b></td><td class='f2'><b>Sex</b></td></tr>";
        $txt_db = $txtDB;
        $emp_grand_total = 0;
        $emp_grand_amt = 0;
        $company_count = 4;
        $i_count = 1;
        if ($txtMACAddress == "78-2B-CB-6A-3C-81" || $txtDB != "ALL") {
            $company_count = 1;
            $i_count = 0;
        }
        for ($ii = $i_count; $ii < $company_count; $ii++) {
            if ($txt_db == "ALL") {
                switch ($ii) {
                    case 0:
                        $txtDB = "okplast_payroll";
                        break;
                    case 1:
                        $txtDB = "okfoods_payroll";
                        break;
                    case 2:
                        $txtDB = "okfood2_payroll";
                        break;
                    case 3:
                        $txtDB = "oksweet_payroll";
                        break;
                }
            }
            mysql_select_db($txtDB, $conn);
            if ($v_j == 0) {
                $query = "SELECT generateddetail.EmployeeID, employees.First, generateddetail.group_value, generateddetail." . $txtColumn . ", generateddetail.Project, employees.pfa_admin, employees.SSNumber, employees.PaymentMode, employees.Bankaccount, employees.department, generateddetail.Gender FROM generateddetail, generatedmaster, employees WHERE generatedmaster.Id = generateddetail.ID AND generatedmaster.From_Value >=  '" . displayParadoxDate(insertDate($txtFrom)) . " 00:00:00' AND generatedmaster.To_Value <= '" . displayParadoxDate(insertDate($txtTo)) . " 23:59:59' AND generateddetail.EmployeeID = employees.id AND generateddetail.group_value <> 'CAF' AND generateddetail.group_value <> 'CAS' AND generateddetail.group_value <> 'STO' AND generateddetail.group_value <> 'CAP' AND generateddetail.group_value <> 'TRI'   ";
            } else {
                $query = "SELECT generateddetail.EmployeeID, employees.First, generateddetail.group_value, generateddetail." . $txtColumn . ", generateddetail.Project, employees.pfa_admin, employees.SSNumber, employees.PaymentMode, employees.Bankaccount, employees.department, generateddetail.Gender FROM generateddetail, generatedmaster, employees WHERE generatedmaster.Id = generateddetail.ID AND generatedmaster.From_Value >=  '" . displayParadoxDate(insertDate($txtFrom)) . " 00:00:00' AND generatedmaster.To_Value <= '" . displayParadoxDate(insertDate($txtTo)) . " 23:59:59' AND generateddetail.EmployeeID = employees.id AND ( ";
                for ($i = 0; $i < $v_j; $i++) {
                    $query .= " generateddetail.group_value = '" . $v_count[$i] . "' ";
                    if ($i < $v_j - 1) {
                        $query .= " OR ";
                    }
                }
                $query .= " ) ";
            }
            if ($txtProject != "") {
                $query .= " AND generateddetail.Project = '" . $txtProject . "' ";
            }
            if ($txtGroup != "") {
                $query .= " AND generateddetail.GROUP_VALUE LIKE '" . $txtGroup . "%' ";
            }
            if ($txtColumn == "Netincome" && $txtColumnName == "Net Cash") {
                $query .= " AND generateddetail.PaymentMode = 'Cash' ";
            }
            if ($txtColumn == "Netincome" && $txtColumnName == "Net Bank") {
                $query .= " AND generateddetail.PaymentMode = 'Bank' ";
            }
            $emp_tot = 0;
            $amt_tot = 0;
            $result = mysqli_query($conn, $query);
            while ($cur = mysqli_fetch_row($result)) {
                if (0 < $cur[3]) {
                    print "<tr> <td class='f2'>" . $cur[0] . "</td> <td class='f2'>" . $cur[1] . "</td> <td class='f2'>" . $cur[4] . "</td> <td class='f2'>" . $cur[2] . "</td>";
                    if ($txtColumnName == "Pension") {
                        print "<td class='f2'>" . $cur[5] . "</td>";
                        print "<td class='f2'>" . $cur[6] . "</td>";
                    } else {
                        if ($txtColumnName == "Net Bank") {
                            print "<td class='f2'>" . $cur[8] . "</td>";
                        }
                    }
                    addComma($cur[3]);
                    print "<td class='f2'>" . addComma($cur[3]) . "</td><td class='f2'>" . $cur[9] . "</td><td class='f2'>" . $cur[10] . "</td></tr>";
                    $emp_tot++;
                    $amt_tot = $amt_tot + $cur[3];
                }
            }
            if (0 < $emp_tot) {
                print "<tr><td class='f2'><b>&nbsp;</b></td> <td class='f2'><b>&nbsp;</b></td> <td class='f2'><b>Total</b></td> <td class='f2'><b>" . $emp_tot . "</b></td>";
                if ($txtColumnName == "Pension") {
                    print "<td class='f2'><b>&nbsp;</b></td>";
                    print "<td class='f2'><b>&nbsp;</b></td>";
                } else {
                    if ($txtColumnName == "Net Bank") {
                        print "<td class='f2'><b>&nbsp;</b></td>";
                    }
                }
                addComma($amt_tot);
                print "<td class='f2'><b>" . addComma($amt_tot) . "</b></td><td class='f2'><b>&nbsp;</b></td><td class='f2'><b>&nbsp;</b></td></tr>";
                $emp_grand_total = $emp_grand_total + $emp_tot;
                $emp_grand_amt = $emp_grand_amt + $amt_tot;
            }
        }
        print "<tr><td class='f2'><b>&nbsp;</b></td> <td class='f2'><b>&nbsp;</b></td> <td class='f2'><b>Gr Total</b></td> <td class='f2'><b>" . $emp_grand_total . "</b></td>";
        if ($txtColumnName == "Pension") {
            print "<td class='f2'><b>&nbsp;</b></td>";
            print "<td class='f2'><b>&nbsp;</b></td>";
        } else {
            if ($txtColumnName == "Net Bank") {
                print "<td class='f2'><b>&nbsp;</b></td>";
            }
        }
        addComma($emp_grand_amt);
        print "<td class='f2'><b>" . addComma($emp_grand_amt) . "</b></td><td class='f2'><b>&nbsp;</b></td><td class='f2'><b>&nbsp;</b></td></tr>";
    }
    print "</table>";
} else {
    if ($act == "searchRecord") {
        print "<table border='1' cellspacing='-1'><tr><td>&nbsp;</td> ";
        if ($txtMACAddress == "78-2B-CB-6A-3C-81") {
            print "<td class='f2'><b>Plast - OKPLAST</b></td> <td class='f2'><b>Plast - OKPEN</b></td>";
        } else {
            print "<td class='f2'><b>FD-1</b></td> <td class='f2'><b>FD-2</b></td> <td class='f2'><b>Sweet</b></td>";
        }
        print "<td class='f2'><b>Grand Total</b></td>";
        print "</tr>";
        $this_0 = 0;
        $this_1 = 0;
        $this_2 = 0;
        $this_3 = 0;
        $this_4 = 0;
        $this_5 = 0;
        $this_6 = 0;
        $this_7 = 0;
        $this_0_ = 0;
        $this_1_ = 0;
        $this_2_ = 0;
        $this_3_ = 0;
        $this_4_ = 0;
        $this_5_ = 0;
        $this_6_ = 0;
        $this_7_ = 0;
        $txtGroup = "";
        for ($i = 0; $i < count($result_array_name); $i++) {
            if ($i == 6) {
                $txtGroup = "S";
                $this_0 = getPayrollTotal($conn, "SUM", "okplast_payroll", "OKPLAST", "S", $txtFrom, $txtTo, $result_array_id[$i], $i, $v_count);
                $this_1 = getPayrollTotal($conn, "SUM", "okplast_payroll", "OKPEN", "S", $txtFrom, $txtTo, $result_array_id[$i], $i, $v_count);
                if ($txtMACAddress != "78-2B-CB-6A-3C-81") {
                    $this_2 = 0;
                    $this_3 = 0;
                    $this_4 = 0;
                    $this_5 = getPayrollTotal($conn, "SUM", "okfoods_payroll", "", "S", $txtFrom, $txtTo, $result_array_id[$i], $i, $v_count);
                    $this_6 = getPayrollTotal($conn, "SUM", "okfood2_payroll", "", "S", $txtFrom, $txtTo, $result_array_id[$i], $i, $v_count);
                    $this_7 = getPayrollTotal($conn, "SUM", "oksweet_payroll", "", "S", $txtFrom, $txtTo, $result_array_id[$i], $i, $v_count);
                }
                $this_0_ = getPayrollTotal($conn, "COUNT", "okplast_payroll", "OKPLAST", "S", $txtFrom, $txtTo, $result_array_id[$i], $i, $v_count);
                $this_1_ = getPayrollTotal($conn, "COUNT", "okplast_payroll", "OKPEN", "S", $txtFrom, $txtTo, $result_array_id[$i], $i, $v_count);
                if ($txtMACAddress != "78-2B-CB-6A-3C-81") {
                    $this_2_ = 0;
                    $this_3_ = 0;
                    $this_4_ = 0;
                    $this_5_ = getPayrollTotal($conn, "COUNT", "okfoods_payroll", "", "S", $txtFrom, $txtTo, $result_array_id[$i], $i, $v_count);
                    $this_6_ = getPayrollTotal($conn, "COUNT", "okfood2_payroll", "", "S", $txtFrom, $txtTo, $result_array_id[$i], $i, $v_count);
                    $this_7_ = getPayrollTotal($conn, "COUNT", "oksweet_payroll", "", "S", $txtFrom, $txtTo, $result_array_id[$i], $i, $v_count);
                }
            } else {
                if ($i == 7) {
                    $txtGroup = "J";
                    $this_0 = getPayrollTotal($conn, "SUM", "okplast_payroll", "OKPLAST", "J", $txtFrom, $txtTo, $result_array_id[$i], $i, $v_count);
                    $this_1 = getPayrollTotal($conn, "SUM", "okplast_payroll", "OKPEN", "J", $txtFrom, $txtTo, $result_array_id[$i], $i, $v_count);
                    if ($txtMACAddress != "78-2B-CB-6A-3C-81") {
                        $this_2 = 0;
                        $this_3 = 0;
                        $this_4 = 0;
                        $this_5 = getPayrollTotal($conn, "SUM", "okfoods_payroll", "", "J", $txtFrom, $txtTo, $result_array_id[$i], $i, $v_count);
                        $this_6 = getPayrollTotal($conn, "SUM", "okfood2_payroll", "", "J", $txtFrom, $txtTo, $result_array_id[$i], $i, $v_count);
                        $this_7 = getPayrollTotal($conn, "SUM", "oksweet_payroll", "", "J", $txtFrom, $txtTo, $result_array_id[$i], $i, $v_count);
                    }
                    $this_0_ = getPayrollTotal($conn, "COUNT", "okplast_payroll", "OKPLAST", "J", $txtFrom, $txtTo, $result_array_id[$i], $i, $v_count);
                    $this_1_ = getPayrollTotal($conn, "COUNT", "okplast_payroll", "OKPEN", "J", $txtFrom, $txtTo, $result_array_id[$i], $i, $v_count);
                    if ($txtMACAddress != "78-2B-CB-6A-3C-81") {
                        $this_2_ = 0;
                        $this_3_ = 0;
                        $this_4_ = 0;
                        $this_5_ = getPayrollTotal($conn, "COUNT", "okfoods_payroll", "", "J", $txtFrom, $txtTo, $result_array_id[$i], $i, $v_count);
                        $this_6_ = getPayrollTotal($conn, "COUNT", "okfood2_payroll", "", "J", $txtFrom, $txtTo, $result_array_id[$i], $i, $v_count);
                        $this_7_ = getPayrollTotal($conn, "COUNT", "oksweet_payroll", "", "J", $txtFrom, $txtTo, $result_array_id[$i], $i, $v_count);
                    }
                } else {
                    $txtGroup = "";
                    $this_0 = getPayrollTotal($conn, "SUM", "okplast_payroll", "OKPLAST", "", $txtFrom, $txtTo, $result_array_id[$i], $i, $v_count);
                    $this_1 = getPayrollTotal($conn, "SUM", "okplast_payroll", "OKPEN", "", $txtFrom, $txtTo, $result_array_id[$i], $i, $v_count);
                    if ($txtMACAddress != "78-2B-CB-6A-3C-81") {
                        $this_2 = 0;
                        $this_3 = 0;
                        $this_4 = 0;
                        $this_5 = getPayrollTotal($conn, "SUM", "okfoods_payroll", "", "", $txtFrom, $txtTo, $result_array_id[$i], $i, $v_count);
                        $this_6 = getPayrollTotal($conn, "SUM", "okfood2_payroll", "", "", $txtFrom, $txtTo, $result_array_id[$i], $i, $v_count);
                        $this_7 = getPayrollTotal($conn, "SUM", "oksweet_payroll", "", "", $txtFrom, $txtTo, $result_array_id[$i], $i, $v_count);
                    }
                    $this_0_ = getPayrollTotal($conn, "COUNT", "okplast_payroll", "OKPLAST", "", $txtFrom, $txtTo, $result_array_id[$i], $i, $v_count);
                    $this_1_ = getPayrollTotal($conn, "COUNT", "okplast_payroll", "OKPEN", "", $txtFrom, $txtTo, $result_array_id[$i], $i, $v_count);
                    if ($txtMACAddress != "78-2B-CB-6A-3C-81") {
                        $this_2_ = 0;
                        $this_3_ = 0;
                        $this_4_ = 0;
                        $this_5_ = getPayrollTotal($conn, "COUNT", "okfoods_payroll", "", "", $txtFrom, $txtTo, $result_array_id[$i], $i, $v_count);
                        $this_6_ = getPayrollTotal($conn, "COUNT", "okfood2_payroll", "", "", $txtFrom, $txtTo, $result_array_id[$i], $i, $v_count);
                        $this_7_ = getPayrollTotal($conn, "COUNT", "oksweet_payroll", "", "", $txtFrom, $txtTo, $result_array_id[$i], $i, $v_count);
                    }
                }
            }
            if ($result_array_id[$i] == "Netincome") {
                $total_array[0] = $total_array[0] * 1 + $this_0;
                $total_array[1] = $total_array[1] * 1 + $this_1;
                $total_array[8] = $total_array[8] * 1 + $this_0 + $this_1;
                $total_array[2] = $total_array[2] * 1 + $this_2;
                $total_array[3] = $total_array[3] * 1 + $this_3;
                $total_array[4] = $total_array[4] * 1 + $this_4;
                $total_array[5] = $total_array[5] * 1 + $this_5;
                $total_array[6] = $total_array[6] * 1 + $this_6;
                $total_array[7] = $total_array[7] * 1 + $this_7;
                $total_array[9] = $total_array[9] * 1 + $this_2 + $this_3 + $this_4 + $this_5 + $this_6 + $this_7;
            }
            if ($result_array_id[$i] == "Salary" || $result_array_id[$i] == "TotalIncome") {
                print "<tr style='page-break-before:always;'><td colspan='12'>&nbsp;</td></tr>";
            }
            if (0 < $this_0 + $this_1 + $this_2 + $this_3 + $this_4 + $this_5 + $this_6 + $this_7) {
                print "<tr><td class='f1'><b>" . $result_array_name[$i] . "</b></td>";
                if ($txtMACAddress == "78-2B-CB-6A-3C-81") {
                    addComma($this_0);
                    addComma($this_1);
                    addComma($this_0 + $this_1);
                    print "<td class='f1'><a style='text-decoration:none' target='_blank' href='ReportOKHR.php?act=viewRecordDetails&excel=yes&txtDB=okplast_payroll&txtGroup=" . $txtGroup . "&txtProject=OKPLAST&txtColumnName=" . $result_array_name[$i] . "&txtColumn=" . $result_array_id[$i] . "&txtFrom=" . $txtFrom . "&txtTo=" . $txtTo . "&txhCount=" . $txhCount . "" . $get_statement . "'>" . addComma($this_0) . "</a></td> <td class='f1'><a style='text-decoration:none' target='_blank' href='ReportOKHR.php?act=viewRecordDetails&excel=yes&txtDB=okplast_payroll&txtGroup=" . $txtGroup . "&txtProject=OKPEN&txtColumnName=" . $result_array_name[$i] . "&txtColumn=" . $result_array_id[$i] . "&txtFrom=" . $txtFrom . "&txtTo=" . $txtTo . "&txhCount=" . $txhCount . "" . $get_statement . "'>" . addComma($this_1) . "</a></td> <td class='f1'><a style='text-decoration:none' target='_blank' href='ReportOKHR.php?act=viewRecordDetails&excel=yes&txtDB=ALL&txtGroup=" . $txtGroup . "&txtProject=&txtColumnName=" . $result_array_name[$i] . "&txtColumn=" . $result_array_id[$i] . "&txtFrom=" . $txtFrom . "&txtTo=" . $txtTo . "&txhCount=" . $txhCount . "" . $get_statement . "'><b>" . addComma($this_0 + $this_1) . "</b></a></td>";
                } else {
                    addComma($this_5);
                    addComma($this_6);
                    addComma($this_7);
                    print "<td class='f1'><a style='text-decoration:none' target='_blank' href='ReportOKHR.php?act=viewRecordDetails&excel=yes&txtDB=okfoods_payroll&txtGroup=" . $txtGroup . "&txtProject=&txtColumnName=" . $result_array_name[$i] . "&txtColumn=" . $result_array_id[$i] . "&txtFrom=" . $txtFrom . "&txtTo=" . $txtTo . "&txhCount=" . $txhCount . "" . $get_statement . "'>" . addComma($this_5) . "</a></td> <td class='f1'><a style='text-decoration:none' target='_blank' href='ReportOKHR.php?act=viewRecordDetails&excel=yes&txtDB=okfood2_payroll&txtGroup=" . $txtGroup . "&txtProject=&txtColumnName=" . $result_array_name[$i] . "&txtColumn=" . $result_array_id[$i] . "&txtFrom=" . $txtFrom . "&txtTo=" . $txtTo . "&txhCount=" . $txhCount . "" . $get_statement . "'>" . addComma($this_6) . "</a></td> <td class='f1'><a style='text-decoration:none' target='_blank' href='ReportOKHR.php?act=viewRecordDetails&excel=yes&txtDB=oksweet_payroll&txtGroup=" . $txtGroup . "&txtProject=&txtColumnName=" . $result_array_name[$i] . "&txtColumn=" . $result_array_id[$i] . "&txtFrom=" . $txtFrom . "&txtTo=" . $txtTo . "&txhCount=" . $txhCount . "" . $get_statement . "'>" . addComma($this_7) . "</a></td>";
                    addComma($this_0 + $this_1 + $this_2 + $this_3 + $this_4 + $this_5 + $this_6 + $this_7);
                    print "<td class='f1'><a style='text-decoration:none' target='_blank' href='ReportOKHR.php?act=viewRecordDetails&excel=yes&txtDB=ALL&txtGroup=" . $txtGroup . "&txtProject=&txtColumnName=" . $result_array_name[$i] . "&txtColumn=" . $result_array_id[$i] . "&txtFrom=" . $txtFrom . "&txtTo=" . $txtTo . "&txhCount=" . $txhCount . "" . $get_statement . "'><b>" . addComma($this_0 + $this_1 + $this_2 + $this_3 + $this_4 + $this_5 + $this_6 + $this_7) . "</b></a></td>";
                }
                print "</tr>";
                print "<tr><td class='f1'><b>Employees</b></td>";
                if ($txtMACAddress == "78-2B-CB-6A-3C-81") {
                    addComma($this_0_);
                    addComma($this_1_);
                    addComma($this_0_ + $this_1_);
                    print "<td class='f1'>" . addComma($this_0_) . "</td> <td class='f1'>" . addComma($this_1_) . "</td> <td class='f1'><b>" . addComma($this_0_ + $this_1_) . "</b></td>";
                } else {
                    addComma($this_5_);
                    addComma($this_6_);
                    addComma($this_7_);
                    print "<td class='f1'>" . addComma($this_5_) . "</td> <td class='f1'>" . addComma($this_6_) . "</td> <td class='f1'>" . addComma($this_7_) . "</td>";
                    addComma($this_0_ + $this_1_ + $this_2_ + $this_3_ + $this_4_ + $this_5_ + $this_6_ + $this_7_);
                    print "<td class='f1'><a style='text-decoration:none' target='_blank' href='ReportOKHR.php?act=viewRecordDetails&excel=yes&txtDB=ALL&txtGroup=" . $txtGroup . "&txtProject=&txtColumnName=" . $result_array_name[$i] . "&txtColumn=" . $result_array_id[$i] . "&txtFrom=" . $txtFrom . "&txtTo=" . $txtTo . "&txhCount=" . $txhCount . "" . $get_statement . "'><b>" . addComma($this_0_ + $this_1_ + $this_2_ + $this_3_ + $this_4_ + $this_5_ + $this_6_ + $this_7_) . "</b></a></td>";
                }
                print "</tr>";
                print "<tr><td class='f1'>&nbsp;</td>";
                if ($txtMACAddress == "78-2B-CB-6A-3C-81") {
                    print "<td class='f1'>&nbsp;</td> <td class='f1'>&nbsp;</td>";
                } else {
                    print "<td class='f1'>&nbsp;</td> <td class='f1'>&nbsp;</td> <td class='f1'><b>&nbsp;</b></td>";
                    print "<td class='f1'><b>&nbsp;</b></td>";
                }
                print "</tr>";
            }
        }
        print "<tr><td class='f1'><b>Total</b></td>";
        if ($txtMACAddress == "78-2B-CB-6A-3C-81") {
            addComma($total_array[0]);
            addComma($total_array[1]);
            print "<td class='f1'><b>" . addComma($total_array[0]) . "</b></td> <td class='f1'><b>" . addComma($total_array[1]) . "</b></td>";
            addComma($total_array[8]);
            print "<td class='f1'><a style='text-decoration:none' target='_blank' href='ReportOKHR.php?act=viewRecordDetails&prints=yes&excel=yes&txtDB=ALLALL&txtGroup=" . $txtGroup . "&txtProject=&txtFrom=" . $txtFrom . "&txtTo=" . $txtTo . "'><b>" . addComma($total_array[8]) . "</b></a></td>";
        } else {
            addComma($total_array[5]);
            addComma($total_array[6]);
            addComma($total_array[7]);
            print "<td class='f1'><b>" . addComma($total_array[5]) . "</b></td> <td class='f1'><b>" . addComma($total_array[6]) . "</b></td> <td class='f1'><b>" . addComma($total_array[7]) . "</b></td>";
            addComma($total_array[0] + $total_array[1] + $total_array[2] + $total_array[3] + $total_array[4] + $total_array[5] + $total_array[6] + $total_array[7]);
            print "<td class='f1'><a style='text-decoration:none' target='_blank' href='ReportOKHR.php?act=viewRecordDetails&prints=yes&excel=yes&txtDB=ALLALL&txtGroup=" . $txtGroup . "&txtProject=&txtFrom=" . $txtFrom . "&txtTo=" . $txtTo . "&txhCount=" . $txhCount . "" . $get_statement . "'><b>" . addComma($total_array[0] + $total_array[1] + $total_array[2] + $total_array[3] + $total_array[4] + $total_array[5] + $total_array[6] + $total_array[7]) . "</b></a></td>";
        }
        print "</tr>";
        print "</table>";
        if ($prints != "yes") {
            print "<br><input type='button' value='Print Report' onClick='checkPrint(0)'>&nbsp;&nbsp;<input type='button' value='Excel' onClick='checkPrint(1)'>";
        }
        print "</p>";
    }
}
print "</form>";
echo "</center></body></html>";
function getPayrollTotal($conn, $param, $db, $project, $group, $txtFrom, $txtTo, $col, $i_, $v_count)
{
    mysql_select_db($db, $conn);
    if ($param == "COUNT") {
        if (count($v_count) == 0) {
            $query = "SELECT IFNULL(" . $param . "(" . $col . "), 0) FROM generateddetail WHERE ID IN (SELECT Id FROM generatedmaster WHERE From_Value >=  '" . displayParadoxDate(insertDate($txtFrom)) . " 00:00:00' AND To_Value <= '" . displayParadoxDate(insertDate($txtTo)) . " 23:59:59') AND " . $col . " > 0 AND generateddetail.group_value <> 'CAF' AND generateddetail.group_value <> 'CAS' AND generateddetail.group_value <> 'STO'  AND generateddetail.group_value <> 'CAP' AND generateddetail.group_value <> 'TRI'   ";
        } else {
            $query = "SELECT IFNULL(" . $param . "(" . $col . "), 0) FROM generateddetail WHERE ID IN (SELECT Id FROM generatedmaster WHERE From_Value >=  '" . displayParadoxDate(insertDate($txtFrom)) . " 00:00:00' AND To_Value <= '" . displayParadoxDate(insertDate($txtTo)) . " 23:59:59') AND " . $col . " > 0 AND ( ";
            for ($i = 0; $i < count($v_count); $i++) {
                $query .= " generateddetail.group_value = '" . $v_count[$i] . "' ";
                if ($i < count($v_count) - 1) {
                    $query .= " OR ";
                }
            }
            $query .= " ) ";
        }
    } else {
        if (count($v_count) == 0) {
            $query = "SELECT IFNULL(" . $param . "(" . $col . "), 0) FROM generateddetail WHERE ID IN (SELECT Id FROM generatedmaster WHERE From_Value >=  '" . displayParadoxDate(insertDate($txtFrom)) . " 00:00:00' AND To_Value <= '" . displayParadoxDate(insertDate($txtTo)) . " 23:59:59') AND generateddetail.group_value <> 'CAF' AND generateddetail.group_value <> 'CAS' AND generateddetail.group_value <> 'STO'  AND generateddetail.group_value <> 'CAP' AND generateddetail.group_value <> 'TRI'   ";
        } else {
            $query = "SELECT IFNULL(" . $param . "(" . $col . "), 0) FROM generateddetail WHERE ID IN (SELECT Id FROM generatedmaster WHERE From_Value >=  '" . displayParadoxDate(insertDate($txtFrom)) . " 00:00:00' AND To_Value <= '" . displayParadoxDate(insertDate($txtTo)) . " 23:59:59') AND ( ";
            for ($i = 0; $i < count($v_count); $i++) {
                $query .= " generateddetail.group_value = '" . $v_count[$i] . "' ";
                if ($i < count($v_count) - 1) {
                    $query .= " OR ";
                }
            }
            $query .= " ) ";
        }
    }
    if ($i_ == 1) {
        $query .= " AND PaymentMode = 'Cash' ";
    } else {
        if ($i_ == 2) {
            $query .= " AND PaymentMode = 'Bank' ";
        }
    }
    if ($project != "") {
        $query .= " AND Project = '" . $project . "' ";
    }
    if ($group != "") {
        $query .= " AND GROUP_VALUE LIKE '" . $group . "%' ";
    }
    $result = selectData($conn, $query);
    return $result[0];
}

?>