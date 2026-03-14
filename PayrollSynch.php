<?php


ob_start("ob_gzhandler");
error_reporting(E_ALL);
include "Functions.php";
$conn = openConnection();
$iconn = openIConnection();
$query = "SELECT LockDate, MACAddress, DBType, DBIP, DBName, DBUser, DBPass, EmployeeCodeLength FROM OtherSettingMaster";
$main_result = selectData($conn, $query);
$txtLockDate = $main_result[0];
$lstDBType = $main_result[2];
$txtDBIP = $main_result[3];
$txtDBName = $main_result[4];
$txtDBUser = $main_result[5];
$txtDBPass = $main_result[6];
$txtECodeLength = $main_result[7];
$txtMACAddress = $main_result[1];
if (checkMAC($conn) == false) {
    print "Un Registered Application. Process Terminated.";
    exit;
}
if (noTASoftware("", $txtMACAddress)) {
    print "Un Registered Application. Process Terminated.";
    exit;
}
$query = "SELECT TableName, Overwrite, EID, EName, IDNo, Dept, Division, Remark, Shift, Phone, Status, ActiveValue, PassiveValue, DataCOMPayroll, UpdateDate, UpdateSalary, Project, CostCentre FROM PayrollMap";
$main_result = selectData($conn, $query);
$oconn = "";
if ($lstDBType == "Oracle") {
    $oconn = oracle_connection($txtDBIP, $txtDBName, $txtDBUser, $txtDBPass);
} else {
    if ($lstDBType == "ODBC") {
        $oconn = odbc_connection($txtDBIP, $txtDBName, $txtDBUser, $txtDBPass);
    } else {
        if ($lstDBType == "MySQL") {
            $oconn = mysql_connection($txtDBIP, $txtDBName, $txtDBUser, $txtDBPass);
            $oiconn = mysqli_connect($txtDBIP, $txtDBUser, $txtDBPass, $txtDBName);
        }
    }
}
if ($oconn != "" && $main_result[1] != "" && checkMAC($conn) == true && $main_result[1] != "No Synchronization") {
    if ($main_result[1] == "Payroll DB") {
        if ($main_result[13] == "DataCOM") {
            $query = "ALTER TABLE employees CHANGE First First VARCHAR(255) NULL DEFAULT NULL, CHANGE SSNumber SSNumber VARCHAR(255) NULL DEFAULT NULL, CHANGE Category Category VARCHAR(255) NULL DEFAULT NULL, CHANGE Project Project VARCHAR(255) NULL DEFAULT NULL, CHANGE Sex Sex VARCHAR(255) NULL DEFAULT NULL, CHANGE JobTitle JobTitle VARCHAR(255) NULL DEFAULT NULL, CHANGE Zone Zone VARCHAR(255) NULL DEFAULT NULL, CHANGE Line Line VARCHAR(255) NULL DEFAULT NULL, CHANGE States States VARCHAR(255) NULL DEFAULT NULL, CHANGE Factory Factory VARCHAR(255) NULL DEFAULT NULL, CHANGE SECTION SECTION VARCHAR(255) NULL DEFAULT NULL, CHANGE Branch Branch VARCHAR(255) NULL DEFAULT NULL, CHANGE Department Department VARCHAR(255) NULL DEFAULT NULL, CHANGE Side Side VARCHAR(255) NULL DEFAULT NULL, CHANGE Affectation Affectation VARCHAR(255) NULL DEFAULT NULL";
            mysqli_query($oiconn, $query, true);
            $query = "ALTER TABLE employees ADD Shift VARCHAR( 255 ) NULL DEFAULT NULL";
            mysqli_query($oiconn, $query, true);
            $query = "ALTER TABLE employees CHANGE Shift Shift VARCHAR( 255 ) NULL";
            mysqli_query($oiconn, $query, true);
            $query = "ALTER TABLE department CHANGE Dept_code Dept_code VARCHAR( 255 ) NULL DEFAULT NULL , CHANGE Dept_desc Dept_desc VARCHAR( 255 ) NULL DEFAULT NULL ";
            mysqli_query($oiconn, $query, true);
            $query = "ALTER TABLE branhes CHANGE Branch_Name Branch_Name VARCHAR( 255 ) NULL DEFAULT NULL";
            mysqli_query($oiconn, $query, true);
            $query = "ALTER TABLE employeecostcenter CHANGE PROJECT PROJECT VARCHAR( 255 ) NULL DEFAULT NULL, CHANGE Branch_code Branch_code VARCHAR( 255 ) NULL DEFAULT NULL, CHANGE COSTCENTER COSTCENTER VARCHAR( 255 ) NULL DEFAULT NULL, CHANGE BRANCHES BRANCHES VARCHAR( 255 ) NULL DEFAULT NULL ";
            mysqli_query($oiconn, $query, true);
            if (strToLower($main_result[6]) == "jobtitle") {
                $query = "UPDATE jobs SET Job_code = Job_id WHERE Job_code IS NULL";
                mysqli_query($oiconn, $query, true);
            }
            $datacom_query = "UPDATE branhes SET Branch_code = Branch_id WHERE Branch_code IS NULL";
            mysqli_query($oiconn, $query, true);
            $datacom_query = "UPDATE department SET Dept_code = Dept_id WHERE Dept_code IS NULL";
            mysqli_query($oiconn, $query, true);
        }
        $query = "SELECT tuser.id, tuser.name, tuser.idno, tuser.dept, tuser.company, tgroup.name, tuser.remark, tuser.phone, tuser.datelimit, tuser.PassiveType, tuser.flagdatelimit FROM tuser, tgroup WHERE tuser.group_id = tgroup.id ORDER BY tuser.id";
        $result = mysqli_query($conn, $query);
        while ($cur = mysqli_fetch_row($result)) {
            $last_cur = "";
            $payroll_query = "SELECT " . $main_result[2] . " FROM " . $main_result[0] . " WHERE " . $main_result[2] . " = '" . addZero($cur[0], $txtECodeLength) . "'";
            if ($lstDBType == "Oracle") {
                $last_result = oci_parse($oconn, $payroll_query);
                oci_execute($last_result);
                $last_cur = oci_fetch_array($last_result, OCI_BOTH);
            } else {
                if ($lstDBType == "ODBC") {
                    $last_result = odbc_exec($oconn, $payroll_query);
                    odbc_fetch_into($last_result, $last_cur);
                } else {
                    if ($lstDBType == "MySQL") {
                        $last_result = mysqli_query($oconn, $payroll_query);
                        $last_cur = mysqli_fetch_row($last_result);
                    }
                }
            }
            if ($last_cur[0] == "" || count($last_cur[0]) < 1) {
                $payroll_query = "INSERT INTO " . $main_result[0] . " (" . $main_result[2] . ", " . $main_result[3] . ") VALUES ('" . addZero($cur[0], $txtECodeLength) . "', '" . $cur[1] . "')";
                if ($lstDBType == "Oracle") {
                    $res = ociParse($oconn, $payroll_query);
                    ociExecute($res);
                    ociCommit($oconn);
                } else {
                    if ($lstDBType == "ODBC") {
                        odbc_exec($oconn, $payroll_query);
                    } else {
                        if ($lstDBType == "MySQL") {
                            mysqli_query($oiconn, $payroll_query, true);
                        }
                    }
                }
            }
            $payroll_query = "UPDATE " . $main_result[0] . " SET " . $main_result[3] . " = '" . $cur[1] . "'";
            if ($main_result[5] != "" && $oconn != "") {
                $payroll_query = $payroll_query . ", " . $main_result[5] . " = '" . $cur[3] . "'";
            }
            if ($main_result[6] != "" && $oconn != "") {
                $payroll_query = $payroll_query . ", " . $main_result[6] . " = '" . $cur[4] . "'";
            }
            if ($main_result[4] != "" && $oconn != "") {
                $payroll_query = $payroll_query . ", " . $main_result[4] . " = '" . $cur[2] . "'";
            }
            if ($main_result[7] != "" && $oconn != "") {
                $payroll_query = $payroll_query . ", " . $main_result[7] . " = '" . $cur[6] . "'";
            }
            if ($main_result[8] != "" && $oconn != "") {
                $payroll_query = $payroll_query . ", " . $main_result[8] . " = '" . $cur[5] . "'";
            }
            if ($main_result[9] != "" && $oconn != "") {
                $payroll_query = $payroll_query . ", " . $main_result[9] . " = '" . $cur[7] . "'";
            }
            if ($main_result[10] != "" && $main_result[11] != "" && $main_result[12] != "" && $oconn != "") {
                if ($cur[9] == "ACT" || $cur[9] == "ADA" || $cur[9] == "FDA") {
                    $payroll_query = $payroll_query . ", " . $main_result[10] . " = '" . $main_result[11] . "'";
                    if ($main_result[13] == "DataCOM" && $main_result[14] == "Yes") {
                        $payroll_query .= ", FixedEmployee = '1', NotIncluded = '0', BeginningDate = '";
                        if (substr($cur[8], 1, 8) == "19770430") {
                            $payroll_query .= displayParadoxDate(substr($cur[10], 1, 8));
                        } else {
                            $payroll_query .= displayParadoxDate(substr($cur[8], 1, 8));
                        }
                        $payroll_query .= "', Exitdate = NULL ";
                    } else {
                        if ($main_result[13] == "DataCOM" && $main_result[14] == "No") {
                            $payroll_query = $payroll_query . ", FixedEmployee = '1', NotIncluded = '0' ";
                        }
                    }
                } else {
                    $payroll_query = $payroll_query . ", " . $main_result[10] . " = '" . $main_result[12] . "'";
                    if ($main_result[13] == "DataCOM" && $main_result[14] == "Yes") {
                        $payroll_query .= ", FixedEmployee = '0', NotIncluded = '1', BeginningDate = '";
                        if (substr($cur[8], 1, 8) == "19770430") {
                            $payroll_query .= displayParadoxDate(substr($cur[10], 1, 8)) . "', Exitdate = '" . displayParadoxDate(substr($cur[10], 9, 8));
                        } else {
                            $payroll_query .= displayParadoxDate(substr($cur[8], 1, 8)) . "', Exitdate = '" . displayParadoxDate(substr($cur[8], 9, 8));
                        }
                        $payroll_query .= "' ";
                    } else {
                        if ($main_result[13] == "DataCOM" && $main_result[14] == "No") {
                            $payroll_query = $payroll_query . ", FixedEmployee = '0', NotIncluded = '1' ";
                        }
                    }
                }
            }
            $payroll_query = $payroll_query . " WHERE " . $main_result[2] . " = '" . addZero($cur[0], $txtECodeLength) . "'";
            if ($lstDBType == "Oracle") {
                $res = ociParse($oconn, $payroll_query);
                ociExecute($res);
                ociCommit($oconn);
            } else {
                if ($lstDBType == "ODBC") {
                    odbc_exec($oconn, $payroll_query);
                } else {
                    if ($lstDBType == "MySQL") {
                        mysqli_query($oiconn, $payroll_query, true);
                    }
                }
            }
            if ($main_result[13] == "DataCOM") {
                if (strToLower($main_result[6]) == "jobtitle") {
                    $datacom_query = "SELECT Job_description FROM jobs WHERE Job_description = '" . $cur[4] . "'";
                    $datacom_this_result = mysqli_query($oconn, $datacom_query);
                    $datacom_this_cur = mysqli_fetch_row($datacom_this_result);
                    if ($datacom_this_cur[0] == "" || $datacom_this_cur == "") {
                        $datacom_query = "INSERT INTO jobs (Job_description) VALUES ('" . $cur[4] . "')";
                        mysqli_query($oiconn, $datacom_query, true);
                    }
                }
                $datacom_query = "SELECT Branch_code FROM branhes WHERE Branch_Name = '" . $cur[3] . "'";
                $datacom_this_result = mysqli_query($oconn, $datacom_query);
                $datacom_this_cur = mysqli_fetch_row($datacom_this_result);
                if ($datacom_this_cur == "" || $datacom_this_cur[0] == "") {
                    $datacom_query = "INSERT INTO branhes (Branch_Name) VALUES ('" . $cur[3] . "')";
                    mysqli_query($oiconn, $datacom_query, true);
                }
                $datacom_query = "SELECT Dept_id FROM department WHERE Dept_code = '" . $cur[3] . "'";
                $datacom_this_result = mysqli_query($oconn, $datacom_query);
                $datacom_this_cur = mysqli_fetch_row($datacom_this_result);
                if ($datacom_this_cur == "" || $datacom_this_cur[0] == "") {
                    $datacom_query = "INSERT INTO department (Dept_code, Dept_desc) VALUES ('" . $cur[3] . "', '" . $cur[3] . "')";
                    mysqli_query($oiconn, $datacom_query, true);
                }
                if ($main_result[16] != "" || $main_result[17] != "") {
                    $datacom_query = "SELECT EMPLOYEE_ID FROM employeecostcenter WHERE EMPLOYEE_ID = '" . addZero($cur[0], $txtECodeLength) . "'";
                    $datacom_this_result = mysqli_query($oconn, $datacom_query);
                    $datacom_this_cur = mysqli_fetch_row($datacom_this_result);
                    if ($datacom_this_cur == "" || $datacom_this_cur[0] == "") {
                        if ($main_result[16] == "Dept [TA]") {
                            $datacom_query = "INSERT INTO employeecostcenter(EMPLOYEE_ID, PROJECT, FROMDATE, Branch_code) VALUES ('" . addZero($cur[0], $txtECodeLength) . "', '" . $cur[3] . "', '2001-01-01', '" . $cur[3] . "')";
                        } else {
                            if ($main_result[16] == "Div/Desg [TA]") {
                                $datacom_query = "INSERT INTO employeecostcenter(EMPLOYEE_ID, PROJECT, FROMDATE, Branch_code) VALUES ('" . addZero($cur[0], $txtECodeLength) . "', '" . $cur[4] . "', '2001-01-01', '" . $cur[4] . "')";
                            } else {
                                if ($main_result[16] == "Social No [TA]") {
                                    $datacom_query = "INSERT INTO employeecostcenter(EMPLOYEE_ID, PROJECT, FROMDATE, Branch_code) VALUES ('" . addZero($cur[0], $txtECodeLength) . "', '" . $cur[2] . "', '2001-01-01', '" . $cur[2] . "')";
                                } else {
                                    if ($main_result[16] == "Remark [TA]") {
                                        $datacom_query = "INSERT INTO employeecostcenter(EMPLOYEE_ID, PROJECT, FROMDATE, Branch_code) VALUES ('" . addZero($cur[0], $txtECodeLength) . "', '" . $cur[6] . "', '2001-01-01', '" . $cur[6] . "')";
                                    } else {
                                        if ($main_result[16] == "Phone [TA]") {
                                            $datacom_query = "INSERT INTO employeecostcenter(EMPLOYEE_ID, PROJECT, FROMDATE, Branch_code) VALUES ('" . addZero($cur[0], $txtECodeLength) . "', '" . $cur[7] . "', '2001-01-01', '" . $cur[7] . "')";
                                        }
                                    }
                                }
                            }
                        }
                        if ($main_result[17] == "Dept [TA]") {
                            $datacom_query = "INSERT INTO employeecostcenter(EMPLOYEE_ID, COSTCENTER, FROMDATE, Branch_code) VALUES ('" . addZero($cur[0], $txtECodeLength) . "', '" . $cur[3] . "', '2001-01-01', '" . $cur[3] . "')";
                        } else {
                            if ($main_result[17] == "Div/Desg [TA]") {
                                $datacom_query = "INSERT INTO employeecostcenter(EMPLOYEE_ID, COSTCENTER, FROMDATE, Branch_code) VALUES ('" . addZero($cur[0], $txtECodeLength) . "', '" . $cur[4] . "', '2001-01-01', '" . $cur[4] . "')";
                            } else {
                                if ($main_result[17] == "Social No [TA]") {
                                    $datacom_query = "INSERT INTO employeecostcenter(EMPLOYEE_ID, COSTCENTER, FROMDATE, Branch_code) VALUES ('" . addZero($cur[0], $txtECodeLength) . "', '" . $cur[2] . "', '2001-01-01', '" . $cur[2] . "')";
                                } else {
                                    if ($main_result[17] == "Remark [TA]") {
                                        $datacom_query = "INSERT INTO employeecostcenter(EMPLOYEE_ID, COSTCENTER, FROMDATE, Branch_code) VALUES ('" . addZero($cur[0], $txtECodeLength) . "', '" . $cur[6] . "', '2001-01-01', '" . $cur[6] . "')";
                                    } else {
                                        if ($main_result[17] == "Phone [TA]") {
                                            $datacom_query = "INSERT INTO employeecostcenter(EMPLOYEE_ID, COSTCENTER, FROMDATE, Branch_code) VALUES ('" . addZero($cur[0], $txtECodeLength) . "', '" . $cur[7] . "', '2001-01-01', '" . $cur[7] . "')";
                                        }
                                    }
                                }
                            }
                        }
                        mysqli_query($oiconn, $datacom_query, true);
                    }
                }
                if ($main_result[15] == "Yes") {
                    $group = "";
                    if ($main_result[4] == "group_value") {
                        $group = $cur[2];
                    } else {
                        if ($main_result[7] == "group_value") {
                            $group = $cur[6];
                        } else {
                            if ($main_result[9] == "group_value") {
                                $group = $cur[7];
                            }
                        }
                    }
                    $datacom_query = "SELECT EmployeeID FROM employeegroup WHERE EmployeeID = '" . addZero($cur[0], $txtECodeLength) . "'";
                    $datacom_this_result = mysqli_query($oconn, $datacom_query);
                    $datacom_this_cur = mysqli_fetch_row($datacom_this_result);
                    if ($datacom_this_cur == "" || $datacom_this_cur[0] == "") {
                        $datacom_query = "INSERT INTO employeegroup(EmployeeID, group_value, FROMDATE, HOURRATE, Day, Hour) VALUES ('" . addZero($cur[0], $txtECodeLength) . "', '" . $group . "', '2001-01-01', '0', '22', '8')";
                        mysqli_query($oiconn, $datacom_query, true);
                    } else {
                        $datacom_query = "UPDATE employeegroup SET group_value =  '" . $group . "' WHERE EmployeeID = '" . addZero($cur[0], $txtECodeLength) . "' AND TODATE IS NULL";
                        mysqli_query($oiconn, $datacom_query, true);
                    }
                    $datacom_query = "SELECT EmployeeID FROM salary1 WHERE EmployeeID = '" . addZero($cur[0], $txtECodeLength) . "'";
                    $datacom_this_result = mysqli_query($oconn, $datacom_query);
                    $datacom_this_cur = mysqli_fetch_row($datacom_this_result);
                    if ($datacom_this_cur == "" || $datacom_this_cur[0] == "") {
                        $datacom_query = "SELECT Parameter_key, Parameter_name, Parameter_Amount, Parameter_Relation FROM parametersetup WHERE Group_code = '" . $group . "' ORDER BY Parameter_key";
                        $datacom_result = mysqli_query($oconn, $datacom_query);
                        while ($datacom_cur = mysqli_fetch_row($datacom_result)) {
                            if (strtoupper($datacom_cur[2]) == "NULL") {
                                $datacom_cur[2] = "0";
                            }
                            $datacom_query = "INSERT INTO salary1(EmployeeID, RelationKey, Description, date_value, Amount, Calculated, Displayed) VALUES ('" . addZero($cur[0], $txtECodeLength) . "', '" . $datacom_cur[0] . "', '" . $datacom_cur[1] . "', '2001-01-01', '" . $datacom_cur[2] . "', 'Y', 'Y')";
                            mysqli_query($oiconn, $datacom_query, true);
                            $datacom_query = "SELECT AUTO_INC FROM salary1 WHERE EmployeeID = '" . addZero($cur[0], $txtECodeLength) . "' AND RelationKey = '" . $datacom_cur[0] . "' AND Description = '" . $datacom_cur[1] . "' AND date_value = '2001-01-01' AND Amount = '" . $datacom_cur[2] . "' AND Calculated = 'Y' AND Displayed = 'Y'";
                            $datacom_this_result = mysqli_query($oconn, $datacom_query);
                            $datacom_this_cur = mysqli_fetch_row($datacom_this_result);
                            $auto_inc = $datacom_this_cur[0];
                            $datacom_query = "SELECT Parameter_key, Parameter_name FROM parametersetuprelation WHERE Parameter_relation = '" . $datacom_cur[3] . "'";
                            $datacom_this_result = mysqli_query($oconn, $datacom_query);
                            while ($datacom_this_cur = mysqli_fetch_row($datacom_this_result)) {
                                $datacom_query = "INSERT INTO relationtosalary (Relation_key, key_value, Description) VALUES ('" . $auto_inc . "', '" . $datacom_this_cur[0] . "', '" . $datacom_this_cur[1] . "')";
                                mysqli_query($oiconn, $datacom_query, true);
                            }
                        }
                    }
                }
            }
        }
        if ($main_result[10] != "" && $main_result[11] != "" && $main_result[12] != "") {
            $payroll_query = "SELECT " . $main_result[2] . " FROM " . $main_result[0] . " WHERE " . $main_result[10] . " = '" . $main_result[11] . "' ORDER BY " . $main_result[2];
            if ($lstDBType == "Oracle") {
                $last_result = oci_parse($oconn, $payroll_query);
                oci_execute($last_result);
                while ($last_cur = oci_fetch_array($last_result, OCI_BOTH)) {
                    $query = "SELECT id from tuser WHERE ID = " . $last_cur[0];
                    $result = selectData($conn, $query);
                    if ($result[0] == "") {
                        $payroll_query = "UPDATE " . $main_result[0] . " SET " . $main_result[10] . " = '" . $main_result[12] . "' WHERE " . $main_result[2] . " = " . $last_cur[0];
                        $res = ociParse($oconn, $payroll_query);
                        ociExecute($res);
                        ociCommit($oconn);
                    }
                }
            } else {
                if ($lstDBType == "ODBC") {
                    $last_result = odbc_exec($oconn, $payroll_query);
                    while (odbc_fetch_into($last_result, $last_cur)) {
                        $query = "SELECT id from tuser WHERE ID = " . $last_cur[0];
                        $result = selectData($conn, $query);
                        if ($result[0] == "") {
                            $payroll_query = "UPDATE " . $main_result[0] . " SET " . $main_result[10] . " = '" . $main_result[12] . "' WHERE " . $main_result[2] . " = " . $last_cur[0];
                            odbc_exec($oconn, $payroll_query);
                        }
                    }
                } else {
                    if ($lstDBType == "MySQL") {
                        $last_result = mysqli_query($oconn, $payroll_query);
                        while ($last_cur = mysqli_fetch_row($last_result)) {
                            $query = "SELECT id from tuser WHERE ID = " . $last_cur[0];
                            $result = selectData($conn, $query);
                            if ($result[0] == "" || $result == "") {
                                $payroll_query = "UPDATE " . $main_result[0] . " SET " . $main_result[10] . " = '" . $main_result[12] . "' WHERE " . $main_result[2] . " = " . $last_cur[0];
                                mysqli_query($oiconn, $payroll_query, true);
                            }
                        }
                    }
                }
            }
        }
    } else {
        if ($main_result[1] != "Payroll DB") {
            $payroll_query = "SELECT " . $main_result[2] . ", " . $main_result[3];
            if ($main_result[5] != "" && $oconn != "") {
                $payroll_query = $payroll_query . ", " . $main_result[5];
            }
            if ($main_result[6] != "" && $oconn != "") {
                $payroll_query = $payroll_query . ", " . $main_result[6];
            }
            if ($main_result[4] != "" && $oconn != "") {
                $payroll_query = $payroll_query . ", " . $main_result[4];
            }
            if ($main_result[7] != "" && $oconn != "") {
                $payroll_query = $payroll_query . ", " . $main_result[7];
            }
            if ($main_result[9] != "" && $oconn != "") {
                $payroll_query = $payroll_query . ", " . $main_result[9];
            }
            if ($main_result[10] != "" && $main_result[11] != "" && $main_result[12] != "" && $oconn != "") {
                $payroll_query = $payroll_query . ", " . $main_result[10];
            }
            $payroll_query = $payroll_query . " FROM " . $main_result[0] . " ORDER BY " . $main_result[2];
            if ($lstDBType == "Oracle") {
                $last_result = oci_parse($oconn, $payroll_query);
                oci_execute($last_result);
                while ($last_cur = oci_fetch_array($last_result, OCI_BOTH)) {
                    $query = "INSERT INTO tuser (id, name, group_id) VALUES ('" . $last_cur[0] . "', '" . $last_cur[1] . "', 0)";
                    updateData($conn, $query, true);
                    $query = "UPDATE tuser SET name = '" . $last_cur[1] . "'";
                    if ($main_result[5] != "" && $oconn != "") {
                        $query = $query . ", Dept = '" . $last_cur[2] . "'";
                    }
                    if ($main_result[6] != "" && $oconn != "") {
                        $query = $query . ", company = '" . $last_cur[3] . "'";
                    }
                    if ($main_result[4] != "" && $oconn != "") {
                        $query = $query . ", idno = '" . $last_cur[4] . "'";
                    }
                    if ($main_result[7] != "" && $oconn != "") {
                        $query = $query . ", remark = '" . $last_cur[5] . "'";
                    }
                    if ($main_result[9] != "" && $oconn != "") {
                        $query = $query . ", phone = '" . $last_cur[6] . "'";
                    }
                    if ($main_result[10] != "" && $main_result[11] != "" && $main_result[12] != "" && $oconn != "") {
                        if ($last_cur[7] == $main_result[11]) {
                            $query = $query . ", datelimit = CONCAT('N', SUBSTRING(datelimit, 2, 16))";
                        } else {
                            if ($last_cur[7] == $main_result[12]) {
                                $query = $query . ", datelimit = CONCAT('Y', SUBSTRING(datelimit, 2, 8), '" . getLastDay(insertToday(), 1) . "')";
                            }
                        }
                    }
                    $query = $query . " WHERE id = '" . $last_cur[0] . "'";
                    updateData($conn, $query, true);
                }
            } else {
                if ($lstDBType == "ODBC") {
                    $last_result = odbc_exec($oconn, $payroll_query);
                    while (odbc_fetch_into($last_result, $last_cur)) {
                        $query = "INSERT INTO tuser (id, name, group_id) VALUES ('" . $last_cur[0] . "', '" . $last_cur[1] . "', 0)";
                        updateData($conn, $query, true);
                        $query = "UPDATE tuser SET name = '" . $last_cur[1] . "'";
                        if ($main_result[5] != "" && $oconn != "") {
                            $query = $query . ", Dept = '" . $last_cur[2] . "'";
                        }
                        if ($main_result[6] != "" && $oconn != "") {
                            $query = $query . ", company = '" . $last_cur[3] . "'";
                        }
                        if ($main_result[4] != "" && $oconn != "") {
                            $query = $query . ", idno = '" . $last_cur[4] . "'";
                        }
                        if ($main_result[7] != "" && $oconn != "") {
                            $query = $query . ", remark = '" . $last_cur[5] . "'";
                        }
                        if ($main_result[9] != "" && $oconn != "") {
                            $query = $query . ", phone = '" . $last_cur[6] . "'";
                        }
                        if ($main_result[10] != "" && $main_result[11] != "" && $main_result[12] != "" && $oconn != "") {
                            if ($last_cur[7] == $main_result[11]) {
                                $query = $query . ", datelimit = CONCAT('N', SUBSTRING(datelimit, 2, 16))";
                            } else {
                                if ($last_cur[7] == $main_result[12]) {
                                    $query = $query . ", datelimit = CONCAT('Y', SUBSTRING(datelimit, 2, 8), '" . getLastDay(insertToday(), 1) . "')";
                                }
                            }
                        }
                        $query = $query . " WHERE id = '" . $last_cur[0] . "'";
                        updateData($conn, $query, true);
                    }
                } else {
                    if ($lstDBType == "MySQL") {
                        $last_result = mysqli_query($oconn, $payroll_query);
                        while ($last_cur = mysqli_fetch_row($last_result)) {
                            $query = "INSERT INTO tuser (id, name, group_id) VALUES ('" . $last_cur[0] . "', '" . $last_cur[1] . "', 0)";
                            updateData($conn, $query, true);
                            $query = "UPDATE tuser SET name = '" . $last_cur[1] . "'";
                            if ($main_result[5] != "" && $oconn != "") {
                                $query = $query . ", Dept = '" . $last_cur[2] . "'";
                            }
                            if ($main_result[6] != "" && $oconn != "") {
                                $query = $query . ", company = '" . $last_cur[3] . "'";
                            }
                            if ($main_result[4] != "" && $oconn != "") {
                                $query = $query . ", idno = '" . $last_cur[4] . "'";
                            }
                            if ($main_result[7] != "" && $oconn != "") {
                                $query = $query . ", remark = '" . $last_cur[5] . "'";
                            }
                            if ($main_result[9] != "" && $oconn != "") {
                                $query = $query . ", phone = '" . $last_cur[6] . "'";
                            }
                            if ($main_result[10] != "" && $main_result[11] != "" && $main_result[12] != "" && $oconn != "") {
                                if ($last_cur[7] == $main_result[11]) {
                                    $query = $query . ", datelimit = CONCAT('N', SUBSTRING(datelimit, 2, 16))";
                                } else {
                                    if ($last_cur[7] == $main_result[12]) {
                                        $query = $query . ", datelimit = CONCAT('Y', SUBSTRING(datelimit, 2, 8), '" . getLastDay(insertToday(), 1) . "')";
                                    }
                                }
                            }
                            $query = $query . " WHERE id = '" . $last_cur[0] . "'";
                            updateData($conn, $query, true);
                        }
                    }
                }
            }
            if ($main_result[10] != "" && $main_result[11] != "" && $main_result[12] != "") {
                $query = "SELECT id FROM tuser WHERE tuser.datelimit LIKE 'N%' OR (tuser.datelimit LIKE 'Y%' AND SUBSTRING(tuser.datelimit, 10, 8) >= '" . insertToday() . "')";
                $result = mysqli_query($conn, $query);
                while ($cur = mysqli_fetch_row($result)) {
                    $payroll_query = "SELECT " . $main_result[2] . " FROM " . $main_result[0] . " WHERE " . $main_result[2] . " = '" . addZero($cur[0], $txtECodeLength) . "'";
                    if ($lstDBType == "Oracle") {
                        $last_result = oci_parse($oconn, $payroll_query);
                        oci_execute($last_result);
                        $last_cur = oci_fetch_array($last_result, OCI_BOTH);
                    } else {
                        if ($lstDBType == "ODBC") {
                            $last_result = odbc_exec($oconn, $payroll_query);
                            odbc_fetch_into($last_result, $last_cur);
                        } else {
                            if ($lstDBType == "MySQL") {
                                $last_result = mysqli_query($oconn, $payroll_query);
                                $last_cur = mysqli_fetch_row($last_result);
                            }
                        }
                    }
                    if ($last_cur[0] == "") {
                        $query = "UPDATE tuser SET datelimit = CONCAT('Y', SUBSTRING(datelimit, 2, 8), '" . getLastDay(insertToday(), 1) . "') WHERE id = " . $cur[0];
                        updateData($conn, $query, true);
                    }
                }
            }
        }
    }
    $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('Payroll Synch', " . insertToday() . ", '" . getNow() . "')";
    updateData($conn, $query, true);
}
function nitgenCode($data)
{
    $data = $data . "000000000";
    $data = addZero($data, 15);
    return $data;
}

?>