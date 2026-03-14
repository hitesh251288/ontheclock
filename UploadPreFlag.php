<?php


ob_start("ob_gzhandler");
error_reporting(E_ERROR);
set_time_limit(4096);
include "Functions.php";
$current_module = "25";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=UploadPreFlag.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$iconn = openIConnection();
$act = $_GET["act"];
if ($act == "") {
    $act = $_POST["act"];
}
$prints = $_GET["prints"];
$excel = $_GET["excel"];
$message = "Upload Pre Flag Details <br><br>Please ensure that the XML File to be uploaded is in the Below Format ONLY <br><br>First Row SHOULD contain the Header (Column Title)<br>Fill Empty Cells with 0 (Zero) <br>For Night Shift, the Relevant Shifts should EXIST in the Shift Rotation Cycle <br>All Uploaded Records will be IMMEDIATELY Processed<br><br><table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'><tr><td><font size='1'>[Employee Code (2-8 Numeric Digits ONLY)]</font></td> <td><font size='1'>[Date From (YYYYMMDD)]</font></td> <td><font size='1'>[Date To (YYYYMMDD)]</font></td> <td><font size='1'>[Day Shift Proxy Days]</font></td> <td><font size='1'>[Night Shift Proxy Days]</font></td> <td><font size='1' color='Violet'>[V]</font></td> <td><font size='1'  color='Indigo'>[I]</font></td> <td><font size='1'  color='Blue'>[B]</font></td> <td><font size='1'  color='Green'>[G]</font></td> <td bgcolor='Brown'><font size='1'  color='Yellow'>[Y]</td> <td><font size='1'  color='Orange'>[O]</font></td> <td><font size='1'  color='Red'>[R]</font></td> <td><font size='1'  color='Green'>[GR]</font></td> <td><font size='1'  color='Brown'>[BR]</font></td> <td><font size='1'  color='Purple'>[PR]</font></td> <td><font size='1'>[Approved OT Hours]</font></td> </tr> </table>";
if ($_GET["message"] != "") {
    $message = $_GET["message"] . "<br><br>" . $message;
}
if ($act == "uploadFile") {
    $txtFileName = $_POST["txtFileName"];
    $data = array();
    if ($_FILES["file"]["tmp_name"]) {
        $dom = DOMDocument::load($_FILES["file"]["tmp_name"]);
        $rows = $dom->getElementsByTagName("Row");
        $LocationID_row = true;
        foreach ($rows as $row) {
            if (!$LocationID_row) {
                $Code = "";
                $DFrom = "";
                $DTo = "";
                $PXD = "";
                $PXN = "";
                $Violet = "";
                $Indigo = "";
                $Blue = "";
                $Green = "";
                $Yellow = "";
                $Orange = "";
                $Red = "";
                $Gray = "";
                $Brown = "";
                $Purple = "";
                $AOT = "";
                $index = 1;
                $cells = $row->getElementsByTagName("Cell");
                foreach ($cells as $cell) {
                    $ind = $cell->getAttribute("Index");
                    if ($ind != null) {
                        $index = $ind;
                    }
                    if ($index == 1) {
                        $Code = $cell->nodeValue;
                    }
                    if ($index == 2) {
                        $DFrom = $cell->nodeValue;
                    }
                    if ($index == 3) {
                        $DTo = $cell->nodeValue;
                    }
                    if ($index == 4) {
                        $PXD = $cell->nodeValue;
                    }
                    if ($index == 5) {
                        $PXN = $cell->nodeValue;
                    }
                    if ($index == 6) {
                        $Violet = $cell->nodeValue;
                    }
                    if ($index == 7) {
                        $Indigo = $cell->nodeValue;
                    }
                    if ($index == 8) {
                        $Blue = $cell->nodeValue;
                    }
                    if ($index == 9) {
                        $Green = $cell->nodeValue;
                    }
                    if ($index == 10) {
                        $Yellow = $cell->nodeValue;
                    }
                    if ($index == 11) {
                        $Orange = $cell->nodeValue;
                    }
                    if ($index == 12) {
                        $Red = $cell->nodeValue;
                    }
                    if ($index == 13) {
                        $Gray = $cell->nodeValue;
                    }
                    if ($index == 14) {
                        $Brown = $cell->nodeValue;
                    }
                    if ($index == 15) {
                        $Purple = $cell->nodeValue;
                    }
                    if ($index == 16) {
                        $AOT = $cell->nodeValue;
                    }
                    $index += 1;
                }
                add_attendance($Code, $DFrom, $DTo, $PXD, $PXN, $Violet, $Indigo, $Blue, $Green, $Yellow, $Orange, $Red, $Gray, $Brown, $Purple, $AOT);
            }
            $LocationID_row = false;
        }
    }
    $flag_array = array("Violet", "Indigo", "Blue", "Green", "Yellow", "Orange", "Red", "Gray", "Brown", "Purple");
    $counter = 0;
    foreach ($data as $row) {
        $query = "INSERT INTO EmployeeFlag (EmployeeID) VALUES ('" . $row["Code"] . "')";
        updateData($conn, $query, true);
        $insert_flag = false;
        for ($i = 0; $i < count($flag_array); $i++) {
            $query = "SELECT " . $flag_array[$i] . " FROM EmployeeFlag WHERE EmployeeID = '" . $row["Code"] . "'";
            $result = selectData($conn, $query);
            $max_flag_limit = $result[0];
            $query = "SELECT COUNT(FlagDayRotationID) FROM FlagDayRotation WHERE e_id = '" . $row["Code"] . "' AND Flag = '" . $flag_array[$i] . "' AND e_date >= " . substr(insertToday(), 0, 4) . "0101 AND e_date <= " . substr(insertToday(), 0, 4) . "1231 AND RecStat = 0";
            $result = selectData($conn, $query);
            $pre_flag_count = $result[0];
            if ($pre_flag_count == "") {
                $pre_flag_count = 0;
            }
            $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster WHERE EmployeeID = '" . $row["Code"] . "' AND Flag = '" . $flag_array[$i] . "' AND ADate >= " . substr(insertToday(), 0, 4) . "0101 AND ADate <= " . substr(insertToday(), 0, 4) . "1231 AND Flag NOT LIKE 'Delete'";
            $result = selectData($conn, $query);
            $post_flag_count = $result[0];
            if ($post_flag_count == "") {
                $post_flag_count = 0;
            }
            if ($pre_flag_count + $post_flag_count + $row[$flag_array[$i]] < $max_flag_limit) {
                $insert_flag = true;
            } else {
                $insert_flag = false;
            }
        }
        if ($insert_flag) {
            for ($i = 0; $i < count($flag_array); $i++) {
                if (0 < $row[$flag_array[$i]]) {
                    $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster WHERE ADate >= '" . $row["DFrom"] . "' AND ADate <= '" . $row["DTo"] . "' AND EmployeeID = '" . $row["Code"] . "' AND Flag = '" . $flag_array[$i] . "'";
                    $result = selectData($conn, $query);
                    if (0 < $result[0]) {
                        $row[$flag_array[$i]] = $row[$flag_array[$i]] - $result[0];
                        if ($row[$flag_array[$i]] < 0) {
                            $row[$flag_array[$i]] = 0;
                        }
                    }
                }
            }
            if (0 < $row["PXN"]) {
                $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster WHERE ADate >= '" . $row["DFrom"] . "' AND ADate <= '" . $row["DTo"] . "' AND EmployeeID = '" . $row["Code"] . "' AND (Flag = 'Black' OR Flag = 'Proxy') AND NightFlag = 1";
                $result = selectData($conn, $query);
                if (0 < $result[0]) {
                    $row["PXN"] = $row["PXN"] - $result[0];
                    if ($row["PXN"] < 0) {
                        $row["PXN"] = 0;
                    }
                }
            }
            if (0 < $row["PXD"]) {
                if (0 < $row["PXN"]) {
                    $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster WHERE ADate >= '" . $row["DFrom"] . "' AND ADate <= '" . $row["DTo"] . "' AND EmployeeID = '" . $row["Code"] . "' AND (Flag = 'Black' OR Flag = 'Proxy') AND NightFlag = 0";
                } else {
                    $query = "SELECT COUNT(AttendanceID) FROM AttendanceMaster WHERE ADate >= '" . $row["DFrom"] . "' AND ADate <= '" . $row["DTo"] . "' AND EmployeeID = '" . $row["Code"] . "' AND (Flag = 'Black' OR Flag = 'Proxy')";
                }
                $result = selectData($conn, $query);
                if (0 < $result[0]) {
                    $row["PXD"] = $row["PXD"] - $result[0];
                    if ($row["PXD"] < 0) {
                        $row["PXD"] = 0;
                    }
                }
            }
            if (0 < $row["AOT"]) {
                $query = "SELECT SUM(AOvertime) FROM AttendanceMaster WHERE ADate >= '" . $row["DFrom"] . "' AND ADate <= '" . $row["DTo"] . "' AND EmployeeID = '" . $row["Code"] . "'";
                $result = selectData($conn, $query);
                if (0 < $result[0]) {
                    $row["AOT"] = $row["AOT"] - $result[0] / 3600;
                    if ($row["AOT"] < 0) {
                        $row["AOT"] = 0;
                    }
                }
            }
            $night_shift = 0;
            $day_shift = 0;
            $lstDeptTerminal = 0;
            $query = "SELECT tuser.OT1, tuser.OT2, tuser.group_id, tgroup.NightFlag FROM tuser, tgroup WHERE tuser.group_id = tgroup.id AND tuser.id = '" . $row["Code"] . "'";
            $tuser_result = selectData($conn, $query);
            if ($tuser_result[3] == 0) {
                $day_shift = $tuser_result[2];
            } else {
                $night_shift = $tuser_result[2];
            }
            $query = "SELECT DISTINCT(g_id) FROM tenter WHERE e_id = '" . $row["Code"] . "'";
            $result = selectData($conn, $query);
            $lstDeptTerminal = $result[0];
            if (0 < $row["PXN"] && $night_shift == 0 || 0 < $row["PXD"] && $day_shift == 0) {
                if (0 < $day_shift) {
                    $query = "SELECT ShiftChangeMaster.idf FROM ShiftChangeMaster WHERE id = " . $day_shift;
                    $result = selectData($conn, $query);
                    $query = "SELECT ShiftChangeMaster.id FROM ShiftChangeMaster, tgroup WHERE ShiftChangeMaster.idf = '" . $result[0] . "' AND ShiftChangeMaster.id = tgroup.id AND tgroup.NightFlag = 1";
                    $result = selectData($conn, $query);
                    if ($result != "" && $result[0] != "") {
                        $night_shift = $result[0];
                    }
                } else {
                    $query = "SELECT idf FROM ShiftChangeMaster WHERE id = " . $night_shift;
                    $result = selectData($conn, $query);
                    $query = "SELECT ShiftChangeMaster.id FROM ShiftChangeMaster, tgroup WHERE ShiftChangeMaster.idf = '" . $result[0] . "' AND ShiftChangeMaster.id = tgroup.id AND tgroup.NightFlag = 0";
                    $result = selectData($conn, $query);
                    if ($result != "" && $result[0] != "") {
                        $day_shift = $result[0];
                    }
                }
            }
            $weekday_array = "";
            $weekend_array = "";
            $weekday_counter = 0;
            $weekend_counter = 0;
            $overtime_only = false;
            if (checkdate(substr($row["DFrom"], 4, 2), substr($row["DFrom"], 6, 2), substr($row["DFrom"], 0, 4)) == true && checkdate(substr($row["DTo"], 4, 2), substr($row["DTo"], 6, 2), substr($row["DTo"], 0, 4)) == true) {
                for ($i = $row["DFrom"]; $i <= $row["DTo"]; $i++) {
                    if (checkdate(substr($i, 4, 2), substr($i, 6, 2), substr($i, 0, 4)) == true) {
                        $query = "SELECT FlagDayRotationID FROM FlagDayRotation WHERE e_id = '" . $row["Code"] . "' AND e_date = '" . $i . "'";
                        $result = selectData($conn, $query);
                        if ($result == "" && $result[0] == "") {
                            $query = "SELECT AttendanceID FROM AttendanceMaster WHERE EmployeeID = '" . $row["Code"] . "' AND ADate = '" . $i . "'";
                            $result = selectData($conn, $query);
                            if ($result == "" && $result[0] == "") {
                                if (getDay(displayDate($i)) == $tuser_result[0] || getDay(displayDate($i)) == $tuser_result[1]) {
                                    $weekend_array[$weekend_counter] = $i;
                                    $weekend_counter++;
                                } else {
                                    $weekday_array[$weekday_counter] = $i;
                                    $weekday_counter++;
                                }
                            } else {
                                if ($overtime_only == false && $row["PXD"] == 0 && $row["PXN"] == 0 && $row["Violet"] == 0 && $row["Indigo"] == 0 && $row["Blue"] == 0 && $row["Green"] == 0 && $row["Yellow"] == 0 && $row["Orange"] == 0 && $row["Red"] == 0 && $row["Gray"] == 0 && $row["Brown"] == 0 && $row["Purple"] == 0) {
                                    $query = "UPDATE AttendanceMaster SET AOvertime = '" . $row["AOT"] * 3600 . "' WHERE EmployeeID = '" . $row["Code"] . "' AND ADate = '" . $row["DFrom"] . "'";
                                    updateIData($iconn, $query, true);
                                    $text = "Attendance Upload from XML: " . $row["Code"] . ", Date: " . displayDate($i) . ", AOT: " . $row["AOT"];
                                    $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                                    updateIData($iconn, $query, true);
                                    $overtime_only == true;
                                }
                            }
                        }
                    }
                }
            }
            for ($i = 0; $i < $weekend_counter; $i++) {
                $weekday_array[$weekday_counter] = $weekend_array[$i];
                $weekday_counter++;
            }
            $insert_counter = 0;
            for ($i = 0; $i < count($flag_array); $i++) {
                for ($j = 0; $j < $row[$flag_array[$i]]; $j++) {
                    if (0 < $day_shift) {
                        insertAttendance($conn, $iconn, $row["Code"], displayDate($weekday_array[$insert_counter]), displayDate($weekday_array[$insert_counter]), $day_shift, $flag_array[$i], $lstDeptTerminal);
                        $text = "Attendance Upload from XML: " . $row["Code"] . ", Date: " . displayDate($weekday_array[$insert_counter]) . ", Shift: " . $day_shift . ", Flag: " . $flag_array[$i];
                    } else {
                        insertAttendance($conn, $iconn, $row["Code"], displayDate($weekday_array[$insert_counter]), displayDate($weekday_array[$insert_counter]), $night_shift, $flag_array[$i], $lstDeptTerminal);
                        $text = "Attendance Upload from XML: " . $row["Code"] . ", Date: " . displayDate($weekday_array[$insert_counter]) . ", Shift: " . $night_shift . ", Flag: " . $flag_array[$i];
                    }
                    $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                    updateIData($iconn, $query, true);
                    $insert_counter++;
                }
            }
            if ($insert_counter < $weekday_counter && 0 < $night_shift) {
                for ($j = 0; $j < $row["PXN"]; $j++) {
                    insertAttendance($conn, $iconn, $row["Code"], displayDate($weekday_array[$insert_counter]), displayDate($weekday_array[$insert_counter]), $night_shift, "Proxy", $lstDeptTerminal);
                    if ($j == 0 && $day_shift == 0) {
                        $query = "UPDATE AttendanceMaster SET Overtime = '0', AOvertime = '" . $row["AOT"] * 3600 . "' WHERE EmployeeID = '" . $row["Code"] . "' AND ADate = '" . $weekday_array[$insert_counter] . "'";
                        updateIData($iconn, $query, true);
                    }
                    $text = "Attendance Upload from XML: " . $row["Code"] . ", Date: " . displayDate($weekday_array[$insert_counter]) . ", Shift: " . $night_shift . ", Flag: " . $flag_array[$i];
                    $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                    updateIData($iconn, $query, true);
                    $insert_counter++;
                }
            }
            if ($insert_counter < $weekday_counter && 0 < $day_shift) {
                for ($j = 0; $j < $row["PXD"]; $j++) {
                    insertAttendance($conn, $iconn, $row["Code"], displayDate($weekday_array[$insert_counter]), displayDate($weekday_array[$insert_counter]), $day_shift, "Proxy", $lstDeptTerminal);
                    if ($j == 0) {
                        $query = "UPDATE AttendanceMaster SET Overtime = '0', AOvertime = '" . $row["AOT"] * 3600 . "' WHERE EmployeeID = '" . $row["Code"] . "' AND ADate = '" . $weekday_array[$insert_counter] . "'";
                        updateIData($iconn, $query, true);
                    }
                    $text = "Attendance Upload from XML: " . $row["Code"] . ", Date: " . displayDate($weekday_array[$insert_counter]) . ", Shift: " . $day_shift . ", Flag: " . $flag_array[$i] . ", OT: " . $row["AOT"];
                    $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', '" . $text . "')";
                    updateIData($iconn, $query, true);
                    $insert_counter++;
                }
            }
            $counter++;
        }
    }
    $message = $message . "<br><br><font color='#FF0000'>File Uploaded. Employees Updated: " . $counter . "</font>";
}
echo "\r\n<html><head><title>Upload Pre Flag Details from XML File</title></head>\r\n<style>input{background-color:'#F0F0F0';font-family:'Verdana';font-size:'10';}</style>\r\n<style>select{background-color:'#FFFFFF';font-family:'Verdana';font-size:'10';}</style>\r\n<script>\r\n\tfunction uploadFile(){\r\n\t\tx = document.frm1;\r\n\t\ty = x.file;\r\n\r\n\t\ta = y.value;\r\n\t\tb = a.substring((a.length-4), a.length);\r\n\r\n\t\tif (b != '.xml'){\r\n\t\t\talert('ONLY XML Files allowed to be uploaded');\r\n\t\t}else{\r\n\t\t\t//if (confirm('Are you sure you have Replaced Blank Spaces by Zeros in the XML File?')){\r\n\t\t\t\tx.bt.disabled = true;\r\n\t\t\t\tx.bt2.disabled = true;\r\n\t\t\t\tx.txtFileName.value = a.substring(a.lastIndexOf('\\\\')+1, a.length);\r\n\t\t\t\tx.submit();\r\n\t\t\t//}\r\n\t\t}\r\n\t}\r\n\t\r\n</script>\r\n<body><center><div align='center'>\t\r\n\t";
print "<center>";
print "</center>";
print "<table width='50%'>";
print "<tr><td width='100%' colspan='2' align='center'><br><font face='Verdana' size='2' color='#6481BD'><b>" . $message . "</b></font></td></tr>";
print "</table>";
echo "\t\r\n\t<form enctype=\"multipart/form-data\" action=\"UploadPreFlag.php\" method=\"post\" name=\"frm1\">\r\n\t\t<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"2000000\" />\r\n\t\t<input type=\"hidden\" name=\"act\" value=\"uploadFile\" />\r\n\t\t<input type=\"hidden\" name=\"txtFileName\" value=\"\" />\r\n\t\t<input type=\"hidden\" name=\"txtCheckFileName\" value=\"\" />\r\n\t\t<table width=\"600\" bgcolor='#F0F0F0' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>\r\n\t\t\t<tr><td bgcolor='#FFFFFF'><font face='Verdana' size='2'>XML File to be Uploaded:</font></td><td bgcolor='#FFFFFF'><input type=\"file\" name=\"file\" /></td><td bgcolor='#FFFFFF'><input type=\"button\" value=\"Upload File\" onClick=\"javascript:uploadFile()\" name=\"bt\"/></td><td bgcolor='#FFFFFF'><input type=\"button\" value=\"Close Window\" onClick=\"javascript:window.close()\" name=\"bt2\"/></td></tr>\r\n\t\t\t<!-- <tr><td vAlign='top' colspan='3' bgcolor='#FFFFFF'><a href='javascript:;' onClick=\"javascript:slidedown('mydiv')\"><font face='Verdana' size='2' color='#000000'>Click Here to view the Steps to be followed before File Upload</font></a></td></tr>\r\n\t\t\t<tr><td colspan='3'><font face='Verdana' size='1'>\r\n\t\t\t\t<div id=\"mydiv\" style=\"display:none; overflow:hidden; height:450px;\">\r\n\t\t\t\t<b>01.</b> Open the Excel File to be uploaded<br>\r\n\t\t\t\t<b>02.</b> Click CTRL-H<br> \r\n\t\t\t\t<b>03.</b> Click Options<br>\r\n\t\t\t\t<b>04.</b> Check (Tick Mark) the Option (Match entire cell c<u>o</u>ntents)<br><br>\r\n\t\t\t\t<img src='http://www.indianhcabuja.com/img/excel/1.gif' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'><br><br>\r\n\t\t\t\t<b>05.</b> Enter \"0\" in \"R<u>e</u>place with:\" and Click Replace <u>A</u>ll<br>\r\n\t\t\t\t<b>06.</b> Click \"<u>F</u>ile\" - \"Save <u>A</u>s\" from the Menu Bar<br>\r\n\t\t\t\t<b>07.</b> Select \"XML Spreadsheet\" from the Drop Down under \"Save as <u>t</u>ype:\"<br>\r\n\t\t\t\t<b>08.</b> Choose the appropriate Folder<br>\r\n\t\t\t\t<b>09.</b> Click <u>S</u>ave<br>\r\n\t\t\t\t<b>10.</b> Upload this Saved File<br>\r\n\t\t\t\t</div>\r\n\t\t\t</font></td></tr> -->\r\n\t\t</table>\r\n\t</form>\r\n\t\r\n\t<script>\r\n\t\tfunction deleteFile(x){\r\n\t\t\tif (confirm('Delete this File?')){\r\n\t\t\t\tx.submit();\r\n\t\t\t}\r\n\t\t}\r\n\t</script>\r\n\t</p>\r\n</div></center></body></html>";
function add_attendance($Code, $DFrom, $DTo, $PXD, $PXN, $Violet, $Indigo, $Blue, $Green, $Yellow, $Orange, $Red, $Gray, $Brown, $Purple, $AOT)
{
    global $data;
    $data[] = array("Code" => $Code, "DFrom" => $DFrom, "DTo" => $DTo, "PXD" => $PXD, "PXN" => $PXN, "Violet" => $Violet, "Indigo" => $Indigo, "Blue" => $Blue, "Green" => $Green, "Yellow" => $Yellow, "Orange" => $Orange, "Red" => $Red, "Gray" => $Gray, "Brown" => $Brown, "Purple" => $Purple, "AOT" => $AOT);
}

?>