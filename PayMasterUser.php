<?php


ob_start("ob_gzhandler");
error_reporting(E_ALL);
set_time_limit(0);
include "Functions.php";
$conn = openConnection();
$iconn = openIConnection();
$jconn = openIConnection();
$uconn = mysqli_connection("127.0.0.1", "UNIS", "root", "namaste");
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
$txtDBIP = $txtDBIP . ",1433\\SQLEXPRESS";
$co_code = 1;
$query = "SELECT TableName, Overwrite, EID, EName, IDNo, Dept, Division, Remark, Shift, Phone, Status, ActiveValue, PassiveValue, DataCOMPayroll, UpdateDate, UpdateSalary, Project, CostCentre, F1, F2, F3, F4, F5, F6, F7, F8, F9 FROM PayrollMap";
$main_result = selectData($conn, $query);
$pay_table = "";
$pay_id = "";
$pay_name = "";
$pay_map = "";
if ($main_result[13] == "PayMaster") {
    $pay_table[0] = "tblEmployee";
    $pay_table[1] = "tblDepartment";
    $pay_table[2] = "tblBranch";
    $pay_table[3] = "tblDesignation";
    $pay_table[4] = "tblCategory";
    $pay_table[5] = "tblMasterOne";
    $pay_table[6] = "tblPayDedGroup";
    $pay_table[7] = "tblSex";
    $pay_table[8] = "tblMasterTwo";
    $pay_id[0] = "EmpNo";
    $pay_id[1] = "DepartmentCode";
    $pay_id[2] = "BranchCode";
    $pay_id[3] = "DesignationCode";
    $pay_id[4] = "CategoryCode";
    $pay_id[5] = "MasterCode";
    $pay_id[6] = "PayDedGroupCode";
    $pay_id[7] = "Sex";
    $pay_id[8] = "MasterTwoCode";
    $pay_name[0] = "EmpName";
    $pay_name[1] = "DepartmentName";
    $pay_name[2] = "BranchName";
    $pay_name[3] = "DesignationName";
    $pay_name[4] = "CategoryName";
    $pay_name[5] = "MasterName";
    $pay_name[6] = "PayDedGroupName";
    $pay_name[7] = "Sex";
    $pay_name[8] = "MasterName";
} else {
    if ($main_result[13] == "WebPayMaster") {
        $pay_table[0] = "tblEmployee";
        $pay_table[1] = "tblDepartment";
        $pay_table[2] = "tblLocation";
        $pay_table[3] = "tblDesignation";
        $pay_table[4] = "tblCategory";
        $pay_table[5] = "tblclass";
        $pay_table[6] = "tblMasterOne";
        $pay_table[7] = "tblMasterTwo";
        $pay_id[0] = "Emp_Payroll_No";
        $pay_id[1] = "Dept_Id";
        $pay_id[2] = "Location_Id";
        $pay_id[3] = "Desig_Id";
        $pay_id[4] = "Category_Id";
        $pay_id[5] = "Class_Id";
        $pay_id[6] = "MasterOne_Id";
        $pay_id[7] = "MasterTwo_Id";
        $pay_name[0] = "Emp_Name";
        $pay_name[1] = "Dept_Name";
        $pay_name[2] = "Location_Name";
        $pay_name[3] = "Desig_Name";
        $pay_name[4] = "Category_Name";
        $pay_name[5] = "Class_Name";
        $pay_name[6] = "MasterOne_Name";
        $pay_name[7] = "MasterTwo_Name";
        $pay_map[0] = "Emp_Name";
        $pay_map[1] = "Emp_Dept_Id";
        $pay_map[2] = "Emp_Location_Id";
        $pay_map[3] = "Emp_Desig_Id";
        $pay_map[4] = "Category_Id";
        $pay_map[5] = "Emp_Class_Id";
        $pay_map[6] = "MasterOne_Id";
        $pay_map[7] = "MasterTwo_Id";
    }
}
$virdi[0] = "";
$virdi[1] = "";
$virdi[2] = "";
$virdi[3] = "Name";
$virdi[4] = "idno";
$virdi[5] = "dept";
$virdi[6] = "company";
$virdi[7] = "remark";
$virdi[8] = "";
$virdi[9] = "phone";
$virdi[18] = "F1";
$virdi[19] = "F2";
$virdi[20] = "F3";
$virdi[21] = "F4";
$virdi[22] = "F5";
$virdi[23] = "F6";
$virdi[24] = "F7";
$virdi[25] = "F8";
$virdi[26] = "F9";
if (checkMAC($conn) == false) {
    print "Un Registered Application. Process Terminated.";
    exit;
}
if (noTASoftware("", $txtMACAddress)) {
    print "Un Registered Application. Process Terminated.";
    exit;
}
$query = "UNLOCK Tables";
if (!updateIData($iconn, $query, true)) {
    echo "\n\r 90:Error in Query: " . $query;
}
$main_count = 1;
if (getRegister($txtMACAddress, 7) == "86") {
    $main_count = 3;
}
for ($iii = 0; $iii < $main_count; $iii++) {
    if (getRegister($txtMACAddress, 7) == "86") {
        if ($iii == 0) {
            $txtDBName = "GSM";
            $co_code = 1;
        } else {
            if ($iii == 1) {
                $txtDBName = "GPI";
                $co_code = 2;
            } else {
                if ($iii == 2) {
                    $txtDBName = "UMPI";
                    $co_code = 3;
                }
            }
        }
    }
    $oconn = mssql_connection($txtDBIP, $txtDBName, $txtDBUser, $txtDBPass);
    if (getRegister($txtMACAddress, 7) == "36" || getRegister($txtMACAddress, 7) == "52") {
        $co_code = 2;
    } else {
        if (getRegister($txtMACAddress, 7) == "51") {
            $co_code = 3;
        } else {
            if (getRegister($txtMACAddress, 7) == "53" || getRegister($txtMACAddress, 7) == "150") {
                $co_code = 4;
            } else {
                if (getRegister($txtMACAddress, 7) == "60") {
                    $co_code = 6;
                } else {
                    if (getRegister($txtMACAddress, 7) == "107" || getRegister($txtMACAddress, 7) == "106" || getRegister($txtMACAddress, 7) == "182") {
                        $co_code = 5;
                    } else {
                        if (getRegister($txtMACAddress, 7) == "58") {
                            $co_code = 7;
                        } else {
                            if (getRegister($txtMACAddress, 7) == "59") {
                                $co_code = 8;
                            } else {
                                if (getRegister($txtMACAddress, 7) == "103" || getRegister($txtMACAddress, 7) == "126") {
                                    $co_code = 9;
                                } else {
                                    if (getRegister($txtMACAddress, 7) == "82") {
                                        $co_code = 10;
                                    } else {
                                        if (getRegister($txtMACAddress, 7) == "117" || getRegister($txtMACAddress, 7) == "150") {
                                            $co_code = 11;
                                        } else {
                                            if (getRegister($txtMACAddress, 7) == "183") {
                                                $co_code = 17;
                                            } else {
                                                if (getRegister($txtMACAddress, 7) == "154") {
                                                    $co_code = 18;
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
    if ($oconn != "" && $main_result[1] != "" && checkMAC($conn) == true && $main_result[1] != "No Synchronization") {
        if (getRegister($txtMACAddress, 7) == "168" || getRegister($txtMACAddress, 7) == "44") {
            $query = "SELECT Dept_Id, Dept_Name FROM tblDepartment";
            $m_result = mssql_query($query, $oconn);
            while ($m_cur = mssql_fetch_row($m_result)) {
                $query = "SELECT C_Code FROM cpost WHERE C_Code = '" . $m_cur[0] . "'";
                $result = selectData($uconn, $query);
                if (0 >= $result[0]) {
                    $query = "INSERT INTO cpost (C_Code, C_name) VALUES ('" . $m_cur[0] . "', '" . replaceString($m_cur[1], false) . "')";
                    if (!updateIData($uconn, $query, true)) {
                        echo "\n\r 258:Error in Query: " . $query;
                    }
                }
            }
            $query = "SELECT SubDepartment_id, SubDepartment_Name FROM tblSubDepartment";
            $m_result = mssql_query($query, $oconn);
            while ($m_cur = mssql_fetch_row($m_result)) {
                $query = "SELECT C_Code FROM Cstaff WHERE C_Code = '" . $m_cur[0] . "'";
                $result = selectData($uconn, $query);
                if (0 >= $result[0]) {
                    $query = "INSERT INTO Cstaff (C_Code, C_name) VALUES ('" . $m_cur[0] . "', '" . replaceString($m_cur[1], false) . "')";
                    if (!updateIData($uconn, $query, true)) {
                        echo "\n\r 280:Error in Query: " . $query;
                    }
                }
            }
            $query = "SELECT Location_Id, Location_Name FROM tblLocation";
            $m_result = mssql_query($query, $oconn);
            while ($m_cur = mssql_fetch_row($m_result)) {
                $query = "SELECT C_Code FROM Coffice WHERE C_Code = '" . $m_cur[0] . "'";
                $result = selectData($uconn, $query);
                if (0 >= $result[0]) {
                    $query = "INSERT INTO Coffice (C_Code, C_name) VALUES ('" . $m_cur[0] . "', '" . replaceString($m_cur[1], false) . "')";
                    if (!updateIData($uconn, $query, true)) {
                        echo "\n\r 301:Error in Query: " . $query;
                    }
                }
            }
            $query = "SELECT Desig_Id, Desig_Name FROM tblDesignation";
            $m_result = mssql_query($query, $oconn);
            while ($m_cur = mssql_fetch_row($m_result)) {
                $query = "SELECT C_Code FROM tmoney WHERE C_Code = '" . $m_cur[0] . "'";
                $result = selectData($uconn, $query);
                if (0 >= $result[0]) {
                    $query = "INSERT INTO tmoney (C_Code, C_name) VALUES ('" . $m_cur[0] . "', '" . replaceString($m_cur[1], false) . "')";
                    if (!updateIData($uconn, $query, true)) {
                        echo "\n\r 301:Error in Query: " . $query;
                    }
                }
            }
            $query = "SELECT Class_id, Class_Name FROM tblClass";
            $m_result = mssql_query($query, $oconn);
            while ($m_cur = mssql_fetch_row($m_result)) {
                $query = "SELECT C_Code FROM Cpassback WHERE C_Code = '" . $m_cur[0] . "'";
                $result = selectData($uconn, $query);
                if (0 >= $result[0]) {
                    $query = "INSERT INTO Cpassback (C_Code, C_name) VALUES ('" . $m_cur[0] . "', '" . replaceString($m_cur[1], false) . "')";
                    if (!updateIData($uconn, $query, true)) {
                        echo "\n\r 301:Error in Query: " . $query;
                    }
                }
            }
            $query = "SELECT Category_id, Category_Name FROM TblCategory";
            $m_result = mssql_query($query, $oconn);
            while ($m_cur = mssql_fetch_row($m_result)) {
                $query = "SELECT C_Code FROM tmealtype WHERE C_Code = '" . $m_cur[0] . "'";
                $result = selectData($uconn, $query);
                if (0 >= $result[0]) {
                    $query = "INSERT INTO tmealtype (C_Code, C_name) VALUES ('" . $m_cur[0] . "', '" . replaceString($m_cur[1], false) . "')";
                    if (!updateIData($uconn, $query, true)) {
                        echo "\n\r 301:Error in Query: " . $query;
                    }
                }
            }
            $query = "SELECT Category_id, Category_Name FROM TblCategory";
            $m_result = mssql_query($query, $oconn);
            while ($m_cur = mssql_fetch_row($m_result)) {
                $query = "SELECT C_Code FROM tmealtype WHERE C_Code = '" . $m_cur[0] . "'";
                $result = selectData($uconn, $query);
                if (0 >= $result[0]) {
                    $query = "INSERT INTO tmealtype (C_Code, C_name) VALUES ('" . $m_cur[0] . "', '" . replaceString($m_cur[1], false) . "')";
                    if (!updateIData($uconn, $query, true)) {
                        echo "\n\r 301:Error in Query: " . $query;
                    }
                }
            }
            $query = "UPDATE tblEmployee SET MasterOne_Id = 1 WHERE MasterOne_Id IS NULL";
            if (!mssql_query($query, $oconn)) {
                echo "\n\rError in Query: " . $query;
            }
            $query = "UPDATE tblEmployee SET MasterTwo_Id = 3 WHERE MasterTwo_Id IS NULL";
            if (!mssql_query($query, $oconn)) {
                echo "\n\rError in Query: " . $query;
            }
            $query = "SELECT Emp_Payroll_No, Emp_Name, CONVERT(varchar, Emp_Join_Date, 23), Emp_Dept_Id, Emp_SubDepartment_Id, Emp_Location_Id, Emp_Desig_Id, Employment_Type, Emp_Class_Id, Is_Separate, Category_Id, MasterOne_Name, MasterTwo_Name FROM tblEmployee, tblMasterOne, tblMasterTwo WHERE tblEmployee.MasterOne_Id = tblMasterOne.MasterOne_Id AND tblEmployee.MasterTwo_Id = tblMasterTwo.MasterTwo_Id";
            $m_result = mssql_query($query, $oconn);
            while ($m_cur = mssql_fetch_row($m_result)) {
                $query = "SELECT l_id FROM unis.tuser WHERE l_id = '" . $m_cur[0] . "'";
                $result = selectData($uconn, $query);
                if (0 < $result[0]) {
                    $start_date = insertParadoxDate($m_cur[2]);
                    $end_date = $start_date;
                    $date_limit = "N";
                    $passive_type = "ACT";
                    if ($m_cur[9] == 1) {
                        $query = "SELECT CONVERT(varchar, LeavingDate, 23), ReasonForLeaving_Name FROM tblEmployee_leavingDetails, tblReasonForLeaving WHERE tblEmployee_leavingDetails.ReasonForLeaving_Id = tblReasonForLeaving.ReasonForLeaving_Id AND Emp_id = '" . $m_cur[0] . "'";
                        $e_result = mssql_query($query, $oconn);
                        $e_cur = mssql_fetch_row($e_result);
                        $end_date = insertParadoxDate($e_cur[0]);
                        $date_limit = "Y";
                        $passive_type = $e_cur[1];
                    }
                    $query = "UPDATE UNIS.tuser SET C_Name = '" . replaceString($m_cur[1], false) . "', C_UserMessage = '" . replaceString($m_cur[11], false) . "', C_Notice = '" . replaceString($m_cur[12], false) . "', C_PassbackStatus = '" . replaceString($m_cur[8], false) . "', C_RegDate = '" . $start_date . "010101', L_OptDateLimit = '" . $m_cur[9] . "', C_DateLimit = '" . $start_date . "" . $end_date . "'  WHERE l_id = '" . $m_cur[0] . "'";
                    if (updateIData($uconn, $query, true)) {
                        $query = "UPDATE UNIS.temploye SET c_post = '" . replaceString($m_cur[3], false) . "', c_staff = '" . replaceString($m_cur[4], false) . "', c_office = '" . replaceString($m_cur[5], false) . "', c_money = '" . replaceString($m_cur[6], false) . "', c_remark = '" . replaceString($m_cur[7], false) . "', c_meal = '" . replaceString($m_cur[10], false) . "' WHERE L_uid = '" . $m_cur[0] . "'";
                        if (updateIData($uconn, $query, true)) {
                            $query = "UPDATE Access.tuser SET Name = '" . replaceString($m_cur[1], false) . "', reg_date = '" . $start_date . "0001', datelimit = '" . $date_limit . "" . $start_date . "" . $end_date . "', PassiveType = '" . $passive_type . "', dept = (SELECT C_name FROM UNIS.cpost WHERE C_Code = '" . $m_cur[3] . "'), remark = (SELECT C_name FROM UNIS.cstaff WHERE C_Code = '" . $m_cur[4] . "'), company = (SELECT C_name FROM UNIS.Coffice WHERE C_Code = '" . $m_cur[5] . "'), f3 = (SELECT C_name FROM UNIS.tmoney WHERE C_Code = '" . $m_cur[6] . "'), phone = '" . $m_cur[7] . "', f4 = '" . $m_cur[8] . "', idno = (SELECT C_name FROM UNIS.tmealtype WHERE C_Code = '" . $m_cur[10] . "'), f2 = '" . replaceString($m_cur[11], false) . "', f1 = '" . replaceString($m_cur[12], false) . "' WHERE id = '" . $m_cur[0] . "'";
                            if (!updateIData($uconn, $query, true)) {
                                echo "\n\r 410:Error in Query: " . $query;
                            }
                        } else {
                            echo "\n\r 414:Error in Query: " . $query;
                        }
                    } else {
                        echo "\n\r 418:Error in Query: " . $query;
                    }
                } else {
                    $start_date = insertParadoxDate($m_cur[2]);
                    $end_date = $start_date;
                    $date_limit = "N";
                    $passive_type = "ACT";
                    if ($m_cur[9] == 1) {
                        $query = "SELECT CONVERT(varchar, LeavingDate, 23), ReasonForLeaving_Name FROM tblEmployee_leavingDetails, tblReasonForLeaving WHERE tblEmployee_leavingDetails.ReasonForLeaving_Id = tblReasonForLeaving.ReasonForLeaving_Id AND Emp_id = '" . $m_cur[0] . "'";
                        $e_result = mssql_query($query, $oconn);
                        $e_cur = mssql_fetch_row($e_result);
                        $end_date = insertParadoxDate($e_cur[0]);
                        $date_limit = "Y";
                        $passive_type = $e_cur[1];
                    }
                    $query = "INSERT INTO UNIS.tuser (l_id, C_Name, C_UserMessage, C_Notice, C_PassbackStatus, L_Type, L_OptDateLimit, C_RegDate, C_DateLimit) VALUES('" . $m_cur[0] . "', '" . replaceString($m_cur[1], false) . "', '" . replaceString($m_cur[11], false) . "', '" . replaceString($m_cur[12], false) . "', '" . replaceString($m_cur[8], false) . "', 0, '" . $m_cur[9] . "', '" . $start_date . "010101', '" . $start_date . "" . $end_date . "')";
                    if (updateIData($uconn, $query, true)) {
                        $query = "INSERT INTO UNIS.temploye (L_uid, c_post, c_staff, c_office, c_money, c_remark, c_meal) VALUES ('" . $m_cur[0] . "', '" . replaceString($m_cur[3], false) . "', '" . replaceString($m_cur[4], false) . "', '" . replaceString($m_cur[5], false) . "', '" . replaceString($m_cur[6], false) . "', '" . replaceString($m_cur[7], false) . "', '" . replaceString($m_cur[10], false) . "')";
                        if (updateIData($uconn, $query, true)) {
                            $query = "INSERT INTO Access.tuser (id, Name, dept, remark, company, f3, phone, f4, idno, f2, f1, reg_date, datelimit, PassiveType) VALUES ('" . replaceString($m_cur[0], false) . "', '" . replaceString($m_cur[1], false) . "', (SELECT C_name FROM UNIS.cpost WHERE C_Code = '" . $m_cur[3] . "'), (SELECT C_name FROM UNIS.cstaff WHERE C_Code = '" . $m_cur[4] . "'), (SELECT C_name FROM UNIS.Coffice WHERE C_Code = '" . $m_cur[5] . "'), (SELECT C_name FROM UNIS.tmoney WHERE C_Code = '" . $m_cur[6] . "'), '" . replaceString($m_cur[7], false) . "', '" . replaceString($m_cur[8], false) . "', (SELECT C_name FROM UNIS.tmealtype WHERE C_Code = '" . $m_cur[10] . "'), '" . replaceString($m_cur[11], false) . "', '" . replaceString($m_cur[12], false) . "', '" . $start_date . "0001', '" . $date_limit . "" . $start_date . "" . $end_date . "', '" . $passive_type . "')";
                            if (!updateIData($uconn, $query, true)) {
                                $query = "UPDATE Access.tuser SET Name = '" . replaceString($m_cur[1], false) . "', reg_date = '" . $start_date . "0001', datelimit = '" . $date_limit . "" . $start_date . "" . $end_date . "', PassiveType = '" . $passive_type . "', dept = (SELECT C_name FROM UNIS.cpost WHERE C_Code = '" . $m_cur[3] . "'), remark = (SELECT C_name FROM UNIS.cstaff WHERE C_Code = '" . $m_cur[4] . "'), company = (SELECT C_name FROM UNIS.Coffice WHERE C_Code = '" . $m_cur[5] . "'), f3 = (SELECT C_name FROM UNIS.tmoney WHERE C_Code = '" . $m_cur[6] . "'), phone = '" . replaceString($m_cur[7], false) . "', f4 = '" . replaceString($m_cur[8], false) . "', idno = (SELECT C_name FROM UNIS.tmealtype WHERE C_Code = '" . $m_cur[10] . "'), f2 = '" . replaceString($m_cur[11], false) . "', f1 = '" . replaceString($m_cur[12], false) . "' WHERE id = '" . $m_cur[0] . "'";
                                if (!updateIData($uconn, $query, true)) {
                                    echo "\n\r 463:Error in Query: " . $query;
                                }
                            }
                        } else {
                            echo "\n\r 438:Error in Query: " . $query;
                        }
                    } else {
                        echo "\n\r 442:Error in Query: " . $query;
                    }
                }
            }
        } else {
            if ($main_result[1] == "Payroll DB") {
                for ($vi = 3; $vi < 27; $vi++) {
                    $query = "UPDATE tuser SET " . $virdi[$vi] . " = '.' WHERE " . $virdi[$vi] . " IS NULL OR " . $virdi[$vi] . " = ''";
                    if (!updateIData($iconn, $query, true)) {
                        echo "\n\r 160:Error in Query: " . $query;
                    }
                }
                if ($main_result[13] == "PayMaster") {
                    for ($synch = 4; $synch < 27; $synch++) {
                        for ($arr = 0; $arr < count($pay_id); $arr++) {
                            if (strtolower($pay_id[$arr]) == strtolower($main_result[$synch])) {
                                createMasters($jconn, $iconn, $oconn, $pay_table[$arr], $pay_id[$arr], $pay_name[$arr], $virdi[$synch], $co_code, $txtMACAddress);
                            }
                        }
                    }
                } else {
                    if ($main_result[13] == "WebPayMaster") {
                        for ($synch = 4; $synch <= count($virdi); $synch++) {
                            for ($arr = 0; $arr < count($pay_id); $arr++) {
                                if ($pay_map[$arr] == $main_result[$synch]) {
                                    createMasters($jconn, $iconn, $oconn, $pay_table[$arr], $pay_id[$arr], $pay_name[$arr], $virdi[$synch], $co_code, $txtMACAddress);
                                }
                            }
                        }
                    }
                }
                if ($main_result[13] == "PayMaster" || $main_result[13] == "WebPayMaster") {
                    $query = "UPDATE tuser SET PassiveType = 'ACT' WHERE PassiveType IS NULL OR PassiveType = '' OR PassiveType = '.'";
                    updateIData($iconn, $query, true);
                    $query = "UPDATE tuser SET F1 = '.' WHERE F1 IS NULL OR F1 = ''";
                    updateIData($iconn, $query, true);
                    $query = "UPDATE tuser SET F2 = '.' WHERE F2 IS NULL OR F2 = ''";
                    updateIData($iconn, $query, true);
                    $query = "UPDATE tuser SET F3 = '.' WHERE F3 IS NULL OR F3 = ''";
                    updateIData($iconn, $query, true);
                    if (getRegister($txtMACAddress, 7) == "39") {
                        createMasters($jconn, $iconn, $oconn, "tblMasterOne", "MasterCode", "MasterName", "F2", $co_code, $txtMACAddress);
                        createMasters($jconn, $iconn, $oconn, "tblPayDedGroup", "PayDedGroupCode", "PayDedGroupName", "idno", $co_code, $txtMACAddress);
                    } else {
                        if (getRegister($txtMACAddress, 7) == "57" || getRegister($txtMACAddress, 7) == "58" || getRegister($txtMACAddress, 7) == "59" || getRegister($txtMACAddress, 7) == "60" || getRegister($txtMACAddress, 7) == "82" || getRegister($txtMACAddress, 7) == "103" || getRegister($txtMACAddress, 7) == "117" || getRegister($txtMACAddress, 7) == "150") {
                            createMasters($jconn, $iconn, $oconn, "tblMasterOne", "MasterCode", "MasterName", "F1", $co_code, $txtMACAddress);
                        } else {
                            if (getRegister($txtMACAddress, 7) == "66") {
                                createMasters($jconn, $iconn, $oconn, "tblMasterOne", "MasterCode", "MasterName", "F1", $co_code, $txtMACAddress);
                                createMasters($jconn, $iconn, $oconn, "tblMasterTwo", "MasterTwoCode", "MasterName", "F2", $co_code, $txtMACAddress);
                            }
                        }
                    }
                }
                $emp_code = 0;
                $query = "SELECT MAX(EmpCode) FROM " . $main_result[0];
                $sub_result = mssql_query($query, $oconn);
                $sub_cur = mssql_fetch_row($sub_result);
                if (0 < $sub_cur[0] * 1) {
                    $emp_code = $sub_cur[0] * 1;
                    $emp_code++;
                }
                if (getRegister($txtMACAddress, 7) == "86") {
                    if ($iii == 0) {
                        $query = "SELECT tuser.id, tuser.name, tuser.idno, tuser.dept, tuser.company, tgroup.name, tuser.remark, tuser.phone, tuser.datelimit, tuser.PassiveType, tuser.flagdatelimit, '', '', '', '', '', '', '', tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9 FROM tuser, tgroup WHERE tuser.group_id = tgroup.id AND tuser.company = 'GSM' ORDER BY tuser.id";
                    } else {
                        if ($iii == 1) {
                            $query = "SELECT tuser.id, tuser.name, tuser.idno, tuser.dept, tuser.company, tgroup.name, tuser.remark, tuser.phone, tuser.datelimit, tuser.PassiveType, tuser.flagdatelimit, '', '', '', '', '', '', '', tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9 FROM tuser, tgroup WHERE tuser.group_id = tgroup.id AND tuser.company = 'GPI' ORDER BY tuser.id";
                        } else {
                            if ($iii == 2) {
                                $query = "SELECT tuser.id, tuser.name, tuser.idno, tuser.dept, tuser.company, tgroup.name, tuser.remark, tuser.phone, tuser.datelimit, tuser.PassiveType, tuser.flagdatelimit, '', '', '', '', '', '', '', tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9 FROM tuser, tgroup WHERE tuser.group_id = tgroup.id AND tuser.company = 'UMPI' ORDER BY tuser.id";
                            }
                        }
                    }
                } else {
                    if (getRegister($txtMACAddress, 7) == "82" || getRegister($txtMACAddress, 7) == "103" || getRegister($txtMACAddress, 7) == "106" || getRegister($txtMACAddress, 7) == "107" || getRegister($txtMACAddress, 7) == "126" || getRegister($txtMACAddress, 7) == "57" || getRegister($txtMACAddress, 7) == "58" || getRegister($txtMACAddress, 7) == "59" || getRegister($txtMACAddress, 7) == "60" || getRegister($txtMACAddress, 7) == "117" || getRegister($txtMACAddress, 7) == "150" || getRegister($txtMACAddress, 7) == "107") {
                        $query = "SELECT tuser.id, tuser.name, tuser.idno, tuser.dept, tuser.company, tgroup.name, tuser.remark, tuser.phone, tuser.datelimit, tuser.PassiveType, tuser.flagdatelimit, '', '', '', '', '', '', tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9 FROM tuser, tgroup WHERE tuser.group_id = tgroup.id AND tuser.idno <> '' AND tuser.idno <> '.' ORDER BY tuser.id";
                    } else {
                        $query = "SELECT tuser.id, tuser.name, tuser.idno, tuser.dept, tuser.company, tgroup.name, tuser.remark, tuser.phone, tuser.datelimit, tuser.PassiveType, tuser.flagdatelimit, '', '', '', '', '', '', tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9 FROM tuser, tgroup WHERE tuser.group_id = tgroup.id ORDER BY tuser.id";
                    }
                }
                $result = mysqli_query($jconn, $query);
                while ($cur = mysqli_fetch_row($result)) {
                    $payroll_query = "SELECT " . $main_result[2] . " FROM " . $main_result[0] . " WHERE " . $main_result[2] . " = '" . addZero($cur[0], $txtECodeLength) . "'";
                    $payroll_result = mssql_query($payroll_query, $oconn);
                    $payroll_cur = mssql_fetch_row($payroll_result);
                    if (is_numeric($payroll_cur[0]) == "") {
                        if ($main_result[13] == "WebPayMaster") {
                            $payroll_insert_query = "INSERT INTO " . $main_result[0] . " (Emp_Code, Emp_Id, " . $main_result[2] . ", " . $main_result[3] . ", Cmp_Id, Emp_Desig_ID, Is_Separate, HoldReason, Emp_Join_Date, Emp_BlacklistDate) VALUES ('" . $emp_code . "', '" . $cur[2] . "', ";
                        } else {
                            if (getRegister($txtMACAddress, 7) == "126" || getRegister($txtMACAddress, 7) == "103") {
                                $payroll_insert_query = "INSERT INTO " . $main_result[0] . " (EmpCode, EmpNo, " . $main_result[2] . ", " . $main_result[3] . ", CoCode, DesignationCode, IsOnHold, ReasonForLeaving, DateOfJoining, DateOfLeaving) VALUES ('" . $emp_code . "', '" . $cur[2] . "', ";
                            } else {
                                $payroll_insert_query = "INSERT INTO " . $main_result[0] . " (EmpCode, " . $main_result[2] . ", " . $main_result[3] . ", CoCode, DesignationCode, IsOnHold, ReasonForLeaving, DateOfJoining, DateOfLeaving) VALUES ('" . $emp_code . "', ";
                            }
                        }
                        $payroll_insert_query = $payroll_insert_query . "'" . addZero($cur[0], $txtECodeLength) . "', '" . $cur[1] . "', '" . $co_code . "', 0, 0, '" . $cur[9] . "' ";
                        if ($cur[9] == "ACT") {
                            $payroll_insert_query = $payroll_insert_query . ", '" . displayParadoxDate(substr($cur[8], 1, 8)) . " 00:00:00', NULL) ";
                        } else {
                            if (!($cur[9] == "ADA" || $cur[9] == "FDA")) {
                                $payroll_insert_query = $payroll_insert_query . ", '" . displayParadoxDate(substr($cur[8], 1, 8)) . " 00:00:00', '" . displayParadoxDate(substr($cur[8], 9, 8)) . " 00:00:00') ";
                            }
                        }
                        if (mssql_query($payroll_insert_query, $oconn)) {
                            $emp_code++;
                        } else {
                            echo "\n\r" . $payroll_insert_query;
                            exit;
                        }
                    }
                }
                if (getRegister($txtMACAddress, 7) == "86") {
                    if ($iii == 0) {
                        $query = "SELECT tuser.id, tuser.name, tuser.F1, tuser.F2, tuser.idno, tuser.dept, tuser.company, tuser.remark, tgroup.name, tuser.phone, tuser.datelimit, tuser.PassiveType, tuser.flagdatelimit, '', '', '', '', '', tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9 FROM tuser, tgroup WHERE tuser.group_id = tgroup.id AND tuser.company = 'GSM' ORDER BY tuser.id";
                    } else {
                        if ($iii == 1) {
                            $query = "SELECT tuser.id, tuser.name, tuser.F1, tuser.F2, tuser.idno, tuser.dept, tuser.company, tuser.remark, tgroup.name, tuser.phone, tuser.datelimit, tuser.PassiveType, tuser.flagdatelimit, '', '', '', '', '', tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9  FROM tuser, tgroup WHERE tuser.group_id = tgroup.id AND tuser.company = 'GPI' ORDER BY tuser.id";
                        } else {
                            if ($iii == 2) {
                                $query = "SELECT tuser.id, tuser.name, tuser.F1, tuser.F2, tuser.idno, tuser.dept, tuser.company, tuser.remark, tgroup.name, tuser.phone, tuser.datelimit, tuser.PassiveType, tuser.flagdatelimit, '', '', '', '', '', tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9  FROM tuser, tgroup WHERE tuser.group_id = tgroup.id AND tuser.company = 'UMPI' ORDER BY tuser.id";
                            }
                        }
                    }
                } else {
                    if (getRegister($txtMACAddress, 7) == "82" || getRegister($txtMACAddress, 7) == "103" || getRegister($txtMACAddress, 7) == "106" || getRegister($txtMACAddress, 7) == "107" || getRegister($txtMACAddress, 7) == "126" || getRegister($txtMACAddress, 7) == "57" || getRegister($txtMACAddress, 7) == "58" || getRegister($txtMACAddress, 7) == "59" || getRegister($txtMACAddress, 7) == "60" || getRegister($txtMACAddress, 7) == "117" || getRegister($txtMACAddress, 7) == "150" || getRegister($txtMACAddress, 7) == "107") {
                        $query = "SELECT tuser.id, tuser.name, tuser.F1, tuser.F2, tuser.idno, tuser.dept, tuser.company, tuser.remark, tgroup.name, tuser.phone, tuser.datelimit, tuser.PassiveType, tuser.flagdatelimit, '', '', '', '', '', tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9 FROM tuser, tgroup WHERE tuser.group_id = tgroup.id AND tuser.idno <> '' AND tuser.idno <> '.' ORDER BY tuser.id";
                    } else {
                        $query = "SELECT tuser.id, tuser.name, tuser.F1, tuser.F2, tuser.idno, tuser.dept, tuser.company, tuser.remark, tgroup.name, tuser.phone, tuser.datelimit, tuser.PassiveType, tuser.flagdatelimit, '', '', '', '', '', tuser.F1, tuser.F2, tuser.F3, tuser.F4, tuser.F5, tuser.F6, tuser.F7, tuser.F8, tuser.F9 FROM tuser, tgroup WHERE tuser.group_id = tgroup.id ORDER BY tuser.id";
                    }
                }
                $result = mysqli_query($jconn, $query);
                while ($cur = mysqli_fetch_row($result)) {
                    $payroll_query = "SET NOCOUNT ON UPDATE " . $main_result[0] . " SET " . $main_result[3] . " = '" . replaceString($cur[1], false) . "', IsOnHold = 0, ReasonForLeaving = '" . $cur[11] . "' ";
                    $sql_query = "SELECT name FROM sys.columns WHERE object_id = OBJECT_ID('tblEmployee')";
                    $sql_result = mssql_query($sql_query, $oconn);
                    while ($sql_cur = mssql_fetch_row($sql_result)) {
                        for ($synch = 4; $synch < 27; $synch++) {
                            if (strtolower($sql_cur[0]) == strtolower($main_result[$synch]) && strtolower($sql_cur[0]) != "employmenttype") {
                                if (stripos($sql_cur[0], "Code") !== false) {
                                    $payroll_query .= ", " . $main_result[$synch] . " = (SELECT " . $pay_id[array_search($sql_cur[0], $pay_id)] . " FROM " . $pay_table[array_search($sql_cur[0], $pay_id)] . " WHERE " . $pay_name[array_search($sql_cur[0], $pay_id)] . " = '" . $cur[$synch] . "')";
                                } else {
                                    $payroll_query .= ", " . $main_result[$synch] . " = '" . $cur[$synch] . "'";
                                }
                            }
                        }
                    }
                    if ($main_result[4] != "" && $oconn != "" && getRegister($txtMACAddress, 7) == "39") {
                        $payroll_query = $payroll_query . ", MasterCode = (SELECT MasterCode FROM tblMasterone WHERE MasterName = '" . $cur[3] . "' AND MasterCode IS NOT NULL) ";
                    }
                    if ($main_result[4] != "" && $oconn != "" && getRegister($txtMACAddress, 7) == "66") {
                        $payroll_query = $payroll_query . ", MasterCode = (SELECT MasterCode FROM tblMasterone WHERE MasterName = '" . $cur[2] . "' AND MasterCode IS NOT NULL), MasterTwoCode = (SELECT MasterTwoCode FROM tblMasterTwo WHERE MasterName = '" . $cur[2] . "' AND MasterTwoCode IS NOT NULL), PermanentTelephone = '" . $cur[13] . "' ";
                    }
                    $pieces = explode(" ", $cur[1]);
                    if (count($pieces) == 2) {
                        $pieces[2] = "";
                    } else {
                        if (count($pieces) == 1) {
                            $pieces[1] = "";
                            $pieces[2] = "";
                        }
                    }
                    $payroll_query = $payroll_query . ", FamilyName = '" . $pieces[0] . "', OtherNames = '" . $pieces[1] . " " . $pieces[2] . "' ";
                    if ($cur[11] == "ACT") {
                        $payroll_query = $payroll_query . ", DateOfJoining = '" . displayParadoxDate(substr($cur[10], 1, 8)) . " 00:00:00', DateOfLeaving = NULL ";
                    } else {
                        if (!($cur[11] == "ADA" || $cur[11] == "FDA")) {
                            $payroll_query = $payroll_query . ", DateOfJoining = '" . displayParadoxDate(substr($cur[10], 1, 8)) . " 00:00:00', DateOfLeaving = '" . displayParadoxDate(substr($cur[10], 9, 8)) . " 00:00:00'";
                        }
                    }
                    for ($vi = 4; $vi <= 9; $vi++) {
                        if (strtolower($main_result[16]) == strtolower($virdi[$vi])) {
                            if (stripos(strtolower($cur[$vi]), "cas") !== false || stripos(strtolower($cur[$vi]), "sto") !== false || stripos(strtolower($cur[$vi]), "caf") !== false || stripos(strtolower($cur[$vi]), "contractor") !== false) {
                                $payroll_query = $payroll_query . ", EmploymentType = 1 ";
                            } else {
                                $payroll_query = $payroll_query . ", EmploymentType = 0 ";
                            }
                        }
                    }
                    if (getRegister($txtMACAddress, 7) == "126" || getRegister($txtMACAddress, 7) == "103") {
                        $payroll_query = $payroll_query . " WHERE EmpNo = '" . $cur[4] . "'";
                    } else {
                        $payroll_query = $payroll_query . " WHERE " . $main_result[2] . " = '" . addZero($cur[0], $txtECodeLength) . "'";
                    }
                    if (!mssql_query($payroll_query, $oconn)) {
                        echo "\n\r 425:Error in Query: " . $payroll_query;
                        $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('Payroll Synch Users - Error in Query: " . $payroll_query . "', " . insertToday() . ", '" . getNow() . "')";
                        updateIData($iconn, $query, true);
                    }
                }
                if (getRegister($txtMACAddress, 7) == "57" || getRegister($txtMACAddress, 7) == "58" || getRegister($txtMACAddress, 7) == "59" || getRegister($txtMACAddress, 7) == "60") {
                    $query = "UPDATE tblEmployee SET PayDedGroupCode = CategoryCode, ApplySpecialTax = 1 WHERE EmploymentType = 1";
                    if (!mssql_query($query, $oconn)) {
                        echo "\n\r 371:Error in Query: " . $query;
                        $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('Payroll Synch Users - Error in Query: " . $query . "', " . insertToday() . ", '" . getNow() . "')";
                        updateIData($iconn, $query, true);
                        exit;
                    }
                }
                if (getRegister($txtMACAddress, 7) == "68") {
                    $query = "UPDATE tblEmployee SET PayDedGroupCode = CategoryCode";
                    if (!mssql_query($query, $oconn)) {
                        echo "\n\r 385:Error in Query: " . $query;
                        $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('Payroll Synch Users - Error in Query: " . $query . "', " . insertToday() . ", '" . getNow() . "')";
                        updateIData($iconn, $query, true);
                        exit;
                    }
                }
                if (getRegister($txtMACAddress, 7) == "39") {
                    $query = "UPDATE tblEmployee SET PayDedGroupCode = CategoryCode, ApplySpecialTax = 1 WHERE EmploymentType = 1";
                    if (mssql_query($query, $oconn)) {
                        $query = "UPDATE tblEmployee SET PaymentMode = 1 WHERE DateOfLeaving IS NOT NULL AND EmploymentType = 0";
                        if (mssql_query($query, $oconn)) {
                            $query = "UPDATE tblEmployee SET PaymentMode = 2 WHERE DateOfLeaving IS NULL AND EmploymentType = 0";
                            if (!mssql_query($query, $oconn)) {
                                echo "\n\r 413:Error in Query: " . $query;
                                $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('Payroll Synch Users - Error in Query: " . $query . "', " . insertToday() . ", '" . getNow() . "')";
                                updateIData($iconn, $query, true);
                                exit;
                            }
                        } else {
                            echo "\n\r 405:Error in Query: " . $query;
                            $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('Payroll Synch Users - Error in Query: " . $query . "', " . insertToday() . ", '" . getNow() . "')";
                            updateIData($iconn, $query, true);
                            exit;
                        }
                    } else {
                        echo "\n\r 397:Error in Query: " . $query;
                        $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('Payroll Synch Users - Error in Query: " . $query . "', " . insertToday() . ", '" . getNow() . "')";
                        updateIData($iconn, $query, true);
                        exit;
                    }
                }
            }
        }
        $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('Payroll Synch Users', " . insertToday() . ", '" . getNow() . "')";
        updateIData($iconn, $query, true);
    } else {
        $query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('Payroll Synch Users Failed: Unable to connect to the Server', " . insertToday() . ", '" . getNow() . "')";
        updateIData($iconn, $query, true);
    }
}
function createMasters($conn, $iconn, $aconn, $table, $f1, $f2, $f3, $co_code, $txtMACAddress)
{
    if ($table != "tblSex") {
        if ($table != "tblEmployee" && $table != "tblPayDedGroup" && $table != "tblCategory" && getRegister($txtMACAddress, 7) != "103" && getRegister($txtMACAddress, 7) != "126" && getRegister($txtMACAddress, 7) != "117" && getRegister($txtMACAddress, 7) != "150" && getRegister($txtMACAddress, 7) != "107" && getRegister($txtMACAddress, 7) != "106" && getRegister($txtMACAddress, 7) != "182" && getRegister($txtMACAddress, 7) != "183") {
            $query = "TRUNCATE TABLE " . $table;
            mssql_query($query, $aconn);
        }
        $query = "UPDATE tuser SET " . $f3 . " = '.' WHERE " . $f3 . " IS NULL OR " . $f3 . " = ''";
        updateIData($iconn, $query, true);
        $query = "UPDATE tuser SET " . $f3 . " = TRIM(" . $f3 . ") WHERE " . $f3 . " IS NOT NULL";
        updateIData($iconn, $query, true);
        if (getRegister($txtMACAddress, 7) == "82" || getRegister($txtMACAddress, 7) == "103" || getRegister($txtMACAddress, 7) == "106" || getRegister($txtMACAddress, 7) == "107" || getRegister($txtMACAddress, 7) == "126" || getRegister($txtMACAddress, 7) == "57" || getRegister($txtMACAddress, 7) == "58" || getRegister($txtMACAddress, 7) == "59" || getRegister($txtMACAddress, 7) == "60" || getRegister($txtMACAddress, 7) == "117" || getRegister($txtMACAddress, 7) == "150" || getRegister($txtMACAddress, 7) == "154" || getRegister($txtMACAddress, 7) == "107" || getRegister($txtMACAddress, 7) == "106" || getRegister($txtMACAddress, 7) == "182" || getRegister($txtMACAddress, 7) == "183") {
            $query = "SELECT DISTINCT(" . $f3 . ") FROM tuser WHERE " . $f3 . " IS NOT NULL AND " . $f3 . " <> '.' AND " . $f3 . " <> '' ORDER BY " . $f3;
        } else {
            $query = "SELECT DISTINCT(" . $f3 . ") FROM tuser WHERE " . $f3 . " IS NOT NULL ORDER BY " . $f3;
        }
        $tuser_result = mysqli_query($conn, $query);
        while ($cur = mysqli_fetch_row($tuser_result)) {
            if ($cur[0] != "") {
                $query = "SELECT MAX(" . $f1 . ") FROM " . $table;
                $sub_result = mssql_query($query, $aconn);
                $sub_cur = mssql_fetch_row($sub_result);
                $sub_cur[0] = $sub_cur[0] * 1 + 1;
                if ($sub_cur[0] == "") {
                    $sub_cur[0] = 1;
                }
                if ($table == "tblBranch" && getRegister($txtMACAddress, 7) == "39") {
                    if ($cur[0] == "LORNA NIGERIA LIMITED") {
                        $query = "INSERT INTO " . $table . " (" . $f1 . ", BranchId, " . $f2 . ", CoCode, BranchNotInUse) VALUES (" . $sub_cur[0] . ", 'LNL', 'LORNA NIGERIA LIMITED', '" . $co_code . "', 0)";
                    } else {
                        if ($cur[0] == "GODREJ NIGERIA LIMITED") {
                            $query = "INSERT INTO " . $table . " (" . $f1 . ", BranchId, " . $f2 . ", CoCode, BranchNotInUse) VALUES (" . $sub_cur[0] . ", 'GNL', 'GODREJ NIGERIA LIMITED', '" . $co_code . "', 0)";
                        }
                    }
                } else {
                    if ($table == "tblDepartment") {
                        $query = "SELECT " . $f2 . " FROM " . $table . " WHERE " . $f2 . " = '" . replaceString($cur[0], false) . "'";
                        $insert_result = mssql_query($query, $aconn);
                        $insert_cur = mssql_fetch_row($insert_result);
                        if ($insert_cur[0] != replaceString($cur[0], false)) {
                            $query = "INSERT INTO " . $table . " (" . $f1 . ", DepartmentId, " . $f2 . ", CoCode, UserId, ProcessDay, DepartmentNotInUse) VALUES (" . $sub_cur[0] . ", " . $sub_cur[0] . ", '" . replaceString($cur[0], false) . "', '" . $co_code . "', 1, 0, 0)";
                        }
                    } else {
                        $query = "SELECT " . $f2 . " FROM " . $table . " WHERE " . $f2 . " = '" . replaceString($cur[0], false) . "'";
                        $insert_result = mssql_query($query, $aconn);
                        $insert_cur = mssql_fetch_row($insert_result);
                        if ($insert_cur[0] != replaceString($cur[0], false)) {
                            $query = "INSERT INTO " . $table . " (" . $f1 . ", " . $f2 . ", CoCode) VALUES (" . $sub_cur[0] . ", '" . replaceString($cur[0], false) . "', '" . $co_code . "')";
                        }
                    }
                }
                if (!mssql_query($query, $aconn)) {
                    echo "\n\r" . $query;
                }
            }
        }
    }
}
function getCountSum($conn, $where, $cs, $f1, $id)
{
    $query = "SELECT " . $cs . "(" . $f1 . ") " . $where . " AND AttendanceMaster.EmployeeID = " . $id;
    $result = selectData($conn, $query);
    if ($result[0] == "") {
        $result[0] = 0;
    }
    return $result[0];
}

?>