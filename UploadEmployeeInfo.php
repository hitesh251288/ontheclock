<?php

ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "27";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
$txtMACAddress = $_SESSION[$session_variable . "MACAddress"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=UploadEmployeeInfo.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$iconn = openIConnection();
$act = $_GET["act"];
if ($act == "") {
    $act = $_POST["act"];
}
$prints = $_GET["prints"];
$excel = $_GET["excel"];
$message = "Upload Employee Data <br><br>This Process OVER WRITES ALL the Existing Employee Information <br>Please ensure that the XML File to be uploaded is in the Below Format ONLY <br><br>First Row SHOULD contain the Header (Column Title)<br><br><table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'><tr><td><font size='1'>[Employee Code (2-8 Numeric Digits ONLY)]</font></td> <td><font size='1'>[Employee Full Name]</font></td> <td><font size='1'>[IdNo (Client Defined Column to be Used) (" . $_SESSION[$session_variable . "IDColumnName"] . ")]</font></td> <td><font size='1'>[Department]</font></td> <td><font size='1'>[Division/Designation]</font></td> <td><font size='1'>[Remarks]</font></td> <td><font size='1'>[Shift Name (Should be EXACTLY same as the Group Name in Virdi Remote Access Manager)]</td> <td><font size='1'>[Phone (Client Defined Column to be Used) (" . $_SESSION[$session_variable . "PhoneColumnName"] . ")]</font></td> <td><font size='1'>[F1 (" . $_SESSION[$session_variable . "F1"] . ")]</font></td> <td><font size='1'>[F2 (" . $_SESSION[$session_variable . "F2"] . ")]</font></td> <td><font size='1'>[F3 (" . $_SESSION[$session_variable . "F3"] . ")]</font></td> <td><font size='1'>[F4 (" . $_SESSION[$session_variable . "F4"] . ")]</font></td> <td><font size='1'>[F5 (" . $_SESSION[$session_variable . "F5"] . ")]</font></td> <td><font size='1'>[F6 (" . $_SESSION[$session_variable . "F6"] . ")]</font></td> <td><font size='1'>[F7 (" . $_SESSION[$session_variable . "F7"] . ")]</font></td> <td><font size='1'>[F8 (" . $_SESSION[$session_variable . "F8"] . ")]</font></td> <td><font size='1'>[F9 (" . $_SESSION[$session_variable . "F9"] . ")]</font></td> <td><font size='1'>[F10 (" . $_SESSION[$session_variable . "F10"] . ")]</font></td>  </tr> <tr><td><font size='1'>Ex: 100001</font></td> <td><font size='1'>Jack Peter</font></td> <td><font size='1'>Male</font></td> <td><font size='1'>Production</font></td> <td><font size='1'>VIRDI</font></td> <td><font size='1'>Senior M1</font></td> <td><font size='1'>Day</td> <td><font size='1'>CASUAL</td> <td><font size='1'>.</td> <td><font size='1'>.</td> <td><font size='1'>.</td> <td><font size='1'>.</td> <td><font size='1'>.</td> <td><font size='1'>.</td> <td><font size='1'>.</td> <td><font size='1'>.</td> <td><font size='1'>.</td> <td><font size='1'>.</td> </tr></table>";
if ($_GET["message"] != "") {
    $message = $_GET["message"] . "<br><br>" . $message;
}
if ($act == "uploadFile") {
    $txtFileName = $_POST["txtFileName"];
    $data = array();
    if ($_FILES["file"]["tmp_name"]) {
        $xmlContent = file_get_contents($_FILES["file"]["tmp_name"]);
        $dom = new DOMDocument();

        // Load the XML content
        if ($dom->loadXML($xmlContent)) {
            $rows = $dom->getElementsByTagName("Row");
            $LocationID_row = true;
            foreach ($rows as $row) {
                if (!$LocationID_row) {
                    $Code = "";
                    $Name = "";
                    $IDNo = "";
                    $Dept = "";
                    $Div = "";
                    $Remark = "";
                    $Shift = "";
                    $Phone = "";
                    $F1 = "";
                    $F2 = "";
                    $F3 = "";
                    $F4 = "";
                    $F5 = "";
                    $F6 = "";
                    $F7 = "";
                    $F8 = "";
                    $F9 = "";
                    $F10 = "";
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
                            $Name = $cell->nodeValue;
                        }
                        if ($index == 3) {
                            $IDNo = $cell->nodeValue;
                        }
                        if ($index == 4) {
                            $Dept = $cell->nodeValue;
                        }
                        if ($index == 5) {
                            $Div = $cell->nodeValue;
                        }
                        if ($index == 6) {
                            $Remark = $cell->nodeValue;
                        }
                        if ($index == 7) {
                            $Shift = $cell->nodeValue;
                        }
                        if ($index == 8) {
                            $Phone = $cell->nodeValue;
                        }
                        if ($index == 9) {
                            $F1 = $cell->nodeValue;
                        }
                        if ($index == 10) {
                            $F2 = $cell->nodeValue;
                        }
                        if ($index == 11) {
                            $F3 = $cell->nodeValue;
                        }
                        if ($index == 12) {
                            $F4 = $cell->nodeValue;
                        }
                        if ($index == 13) {
                            $F5 = $cell->nodeValue;
                        }
                        if ($index == 14) {
                            $F6 = $cell->nodeValue;
                        }
                        if ($index == 15) {
                            $F7 = $cell->nodeValue;
                        }
                        if ($index == 16) {
                            $F8 = $cell->nodeValue;
                        }
                        if ($index == 17) {
                            $F9 = $cell->nodeValue;
                        }
                        if ($index == 18) {
                            $F10 = $cell->nodeValue;
                        }
                        $index += 1;
                    }
                    add_person($Code, $Name, $Dept, $Div, $IDNo, $Remark, $Shift, $Phone, $F1, $F2, $F3, $F4, $F5, $F6, $F7, $F8, $F9, $F10);
                }
                $LocationID_row = false;
            }
        } else {
            echo "Failed to load XML file. Please check the file content.";
        }
    }
    $counter = 0;
    foreach ($data as $row) {
        $commit = true;
        if (0 < strlen($row["Code"]) && 0 < strlen($row["Name"]) && 0 < strlen($row["Dept"])) {
            if ($txtMACAddress == "E4-11-5B-DE-92-E8" || $txtMACAddress == "F8-BC-12-57-46-3E" || $txtMACAddress == "2C-27-D7-3A-E7-AA" || $txtMACAddress == "8C-EC-4B-42-D7-A7" || $txtMACAddress == "00-26-2D-D0-72-EE" || $txtMACAddress == "70-5A-0F-4A-2A-C7" || $txtMACAddress == "70-5A-0F-4A-28-0B" || $txtMACAddress == "18-66-DA-3D-E5-AC" || $txtMACAddress == "A0-8C-FD-C0-B6-E5") {
                if ($row["Name"] == "." || $row["Dept"] == "." || $row["Div"] == "." || $row["IDNo"] == "." || $row["Remark"] == "." || $row["Phone"] == "." || $row["Name"] == "" || $row["Dept"] == "" || $row["Div"] == "" || $row["IDNo"] == "" || $row["Remark"] == "" || $row["Phone"] == "") {
                    echo "<br><br>Did Nothing";
                } else {
                    $query = "UPDATE tuser SET Name = '" . $row["Name"] . "', idno = '" . $row["IDNo"] . "', Remark = '" . $row["Remark"] . "', Phone = '" . $row["Phone"] . "', F1 = '" . $row["F1"] . "', F2 = '" . $row["F2"] . "', F3 = '" . $row["F3"] . "', F4 = '" . $row["F4"] . "', F5 = '" . $row["F5"] . "', F6 = '" . $row["F6"] . "', F7 = '" . $row["F7"] . "', F8 = '" . $row["F8"] . "', F9 = '" . $row["F9"] . "', F10 = '" . $row["F10"] . "'  WHERE id = '" . $row["Code"] . "'";
                }
            } else {
                $query = "UPDATE tuser SET Name = '" . $row["Name"] . "', Dept = '" . $row["Dept"] . "', Company = '" . $row["Div"] . "', idno = '" . $row["IDNo"] . "', Remark = '" . $row["Remark"] . "', Phone = '" . $row["Phone"] . "', F1 = '" . $row["F1"] . "', F2 = '" . $row["F2"] . "', F3 = '" . $row["F3"] . "', F4 = '" . $row["F4"] . "', F5 = '" . $row["F5"] . "', F6 = '" . $row["F6"] . "', F7 = '" . $row["F7"] . "', F8 = '" . $row["F8"] . "', F9 = '" . $row["F9"] . "', F10 = '" . $row["F10"] . "'  WHERE id = '" . $row["Code"] . "'";
            }
            updateIData($iconn, $query, true);
            $commit = true;
            if ($commit && $row["Shift"] != "") {
                $query = "UPDATE tuser, tgroup SET tuser.group_id = tgroup.id WHERE tgroup.name = '" . $row["Shift"] . "' AND tuser.id = '" . $row["Code"] . "'";
                if (updateIData($iconn, $query, true)) {
                    
                }
            }
            if ($commit) {
                $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'XML File Upload: Updated Employee ID: " . $row["Code"] . " -  Name = " . $row["Name"] . ", IDNo = " . $row["IDNo"] . ", Dept = " . $row["Dept"] . ", Div = " . $row["Div"] . ", Remark = " . $row["Remark"] . ", Shift = " . $row["Shift"] . ", Phone = " . $row["Phone"] . ", F1 = " . $row["F1"] . ", F2 = " . $row["F2"] . ", F3 = " . $row["F3"] . ", F4 = " . $row["F4"] . ", F5 = " . $row["F5"] . ", F6 = " . $row["F6"] . ", F7 = " . $row["F7"] . ", F8 = " . $row["F8"] . ", F9 = " . $row["F9"] . ", F10 = " . $row["F10"] . "')";
                if (updateIData($iconn, $query, true)) {
                    $counter++;
                }
            }
        }
    }
    $message = $message . "<br><br><font color='#FF0000'>File Uploaded. Rows Updated: " . $counter . "</font>";
}
echo "\r\n<html><head><title>Upload User Info XML File</title></head>\r\n<style>input{background-color:'#F0F0F0';font-family:'Verdana';font-size:'10';}</style>\r\n<style>select{background-color:'#FFFFFF';font-family:'Verdana';font-size:'10';}</style>\r\n<script>\r\n\tfunction uploadFile(){\r\n\t\tx = document.frm1;\r\n\t\ty = x.file;\r\n\r\n\t\ta = y.value;\r\n\t\tb = a.substring((a.length-4), a.length);\r\n\r\n\t\tif (b != '.xml'){\r\n\t\t\talert('ONLY XML Files allowed to be uploaded');\r\n\t\t}else{\r\n\t\t\t//if (confirm('Are you sure you have Replaced Blank Spaces by Zeros in the XML File?')){\r\n\t\t\t\tx.bt.disabled = true;\r\n\t\t\t\tx.bt2.disabled = true;\r\n\t\t\t\tx.txtFileName.value = a.substring(a.lastIndexOf('\\\\')+1, a.length);\r\n\t\t\t\tx.submit();\r\n\t\t\t//}\r\n\t\t}\r\n\t}\r\n\t\r\n</script>\r\n<body><center><div align='center'>\t\r\n\t";
print "<center>";
print "</center>";
print "<table width='50%'>";
print "<tr><td width='100%' colspan='2' align='center'><br><font face='Verdana' size='2' color='#6481BD'><b>" . $message . "</b></font></td></tr>";
print "</table>";
echo "\t\r\n\t<form enctype=\"multipart/form-data\" action=\"UploadEmployeeInfo.php\" method=\"post\" name=\"frm1\">\r\n\t\t<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"2000000\" />\r\n\t\t<input type=\"hidden\" name=\"act\" value=\"uploadFile\" />\r\n\t\t<input type=\"hidden\" name=\"txtFileName\" value=\"\" />\r\n\t\t<input type=\"hidden\" name=\"txtCheckFileName\" value=\"\" />\r\n\t\t<table width=\"600\" bgcolor='#F0F0F0' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>\r\n\t\t\t<tr><td bgcolor='#FFFFFF'><font face='Verdana' size='2'>XML File to be Uploaded:</font></td><td bgcolor='#FFFFFF'><input type=\"file\" name=\"file\" /></td><td bgcolor='#FFFFFF'><input type=\"button\" value=\"Upload File\" onClick=\"javascript:uploadFile()\" name=\"bt\"/></td><td bgcolor='#FFFFFF'><input type=\"button\" value=\"Close Window\" onClick=\"javascript:window.close()\" name=\"bt2\"/></td></tr>\r\n\t\t\t<!-- <tr><td vAlign='top' colspan='3' bgcolor='#FFFFFF'><a href='javascript:;' onClick=\"javascript:slidedown('mydiv')\"><font face='Verdana' size='2' color='#000000'>Click Here to view the Steps to be followed before File Upload</font></a></td></tr>\r\n\t\t\t<tr><td colspan='3'><font face='Verdana' size='1'>\r\n\t\t\t\t<div id=\"mydiv\" style=\"display:none; overflow:hidden; height:450px;\">\r\n\t\t\t\t<b>01.</b> Open the Excel File to be uploaded<br>\r\n\t\t\t\t<b>02.</b> Click CTRL-H<br> \r\n\t\t\t\t<b>03.</b> Click Options<br>\r\n\t\t\t\t<b>04.</b> Check (Tick Mark) the Option (Match entire cell c<u>o</u>ntents)<br><br>\r\n\t\t\t\t<img src='http://www.indianhcabuja.com/img/excel/1.gif' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'><br><br>\r\n\t\t\t\t<b>05.</b> Enter \"0\" in \"R<u>e</u>place with:\" and Click Replace <u>A</u>ll<br>\r\n\t\t\t\t<b>06.</b> Click \"<u>F</u>ile\" - \"Save <u>A</u>s\" from the Menu Bar<br>\r\n\t\t\t\t<b>07.</b> Select \"XML Spreadsheet\" from the Drop Down under \"Save as <u>t</u>ype:\"<br>\r\n\t\t\t\t<b>08.</b> Choose the appropriate Folder<br>\r\n\t\t\t\t<b>09.</b> Click <u>S</u>ave<br>\r\n\t\t\t\t<b>10.</b> Upload this Saved File<br>\r\n\t\t\t\t</div>\r\n\t\t\t</font></td></tr> -->\r\n\t\t</table>\r\n\t</form>\r\n\t\r\n\t<script>\r\n\t\tfunction deleteFile(x){\r\n\t\t\tif (confirm('Delete this File?')){\r\n\t\t\t\tx.submit();\r\n\t\t\t}\r\n\t\t}\r\n\t</script>\r\n\t</p>\r\n</div></center></body></html>";

function add_person($Code, $Name, $Dept, $Div, $IDNo, $Remark, $Shift, $Phone, $F1, $F2, $F3, $F4, $F5, $F6, $F7, $F8, $F9, $F10) {
    global $data;
    $data[] = array("Code" => $Code, "Name" => $Name, "Dept" => $Dept, "Div" => $Div, "IDNo" => $IDNo, "Remark" => $Remark, "Shift" => $Shift, "Phone" => $Phone, "F1" => $F1, "F2" => $F2, "F3" => $F3, "F4" => $F4, "F5" => $F5, "F6" => $F6, "F7" => $F7, "F8" => $F8, "F9" => $F9, "F10" => $F10);
}

?>