<?php


ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "27";
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=UploadEmployeeDate.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$iconn = openIConnection();
$act = $_GET["act"];
if ($act == "") {
    $act = $_POST["act"];
}
$prints = $_GET["prints"];
$excel = $_GET["excel"];
$message = "Upload Employee Start and End Dates <br><br>This Process OVER WRITES ALL the Existing Employee Information <br>Please ensure that the XML File to be uploaded is in the Below Format ONLY <br><br>First Row SHOULD contain the Header (Column Title)<br><br><table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'><tr><td><font size='1'>[Employee Code (2-8 Numeric Digits ONLY)]</font></td> <td><font size='1'>[Start Date (<b><font size='2' color='#FF0000'>YYYYMMDD</font></b>)<br>(Enter <b>.</b> {dot} if Start Date NOT to be changed)]</font></td> <td><font size='1'>[End Date (<b><font size='2' color='#FF0000'>YYYYMMDD</font></b>)<br>(Enter <b>.</b> {dot} if End Date NOT to be changed) <br>(If End Date < Today, Employee will be marked as RESIGNED (RSN))]</font></td> </tr></table>";
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
                $StartDate = "";
                $EndDate = "";
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
                        $StartDate = $cell->nodeValue;
                    }
                    if ($index == 3) {
                        $EndDate = $cell->nodeValue;
                    }
                    $index += 1;
                }
                add_date($Code, $StartDate, $EndDate);
            }
            $LocationID_row = false;
        }
    }
    $counter = 0;
    $dl1 = "";
    $dl2 = "";
    $dl3 = "";
    foreach ($data as $row) {
        $query = "SELECT datelimit, flagdatelimit, PassiveType FROM tuser WHERE id = '" . $row["Code"] . "'";
        $result = selectData($conn, $query);
        if ($result[2] == "FDA" || $result[2] == "ADA") {
            $dl1 = substr($result[1], 0, 1);
            $dl2 = substr($result[1], 1, 8);
            $dl3 = substr($result[1], 9, 8);
        } else {
            $dl1 = substr($result[0], 0, 1);
            $dl2 = substr($result[0], 1, 8);
            $dl3 = substr($result[0], 9, 8);
        }
        $passiveType = "";
        if ($row["StartDate"] != "." && strlen($row["StartDate"]) == 8 && is_numeric($row["StartDate"]) == true) {
            $dl2 = $row["StartDate"];
        }
        if ($row["EndDate"] != "." && strlen($row["EndDate"]) == 8 && is_numeric($row["EndDate"]) == true) {
            $dl3 = $row["EndDate"];
            if ($row["EndDate"] < insertToday()) {
                $dl1 = "Y";
                $passiveType = " , PassiveType = 'RSN' ";
            } else {
                $dl1 = "N";
                $passiveType = "";
            }
        }
        if ($result[2] == "FDA" || $result[2] == "ADA") {
            $query = "UPDATE tuser SET flagdatelimit = '" . $dl1 . "" . $dl2 . "" . $dl3 . "' WHERE id = '" . $row["Code"] . "'";
        } else {
            $query = "UPDATE tuser SET datelimit = '" . $dl1 . "" . $dl2 . "" . $dl3 . "' " . $passiveType . " WHERE id = '" . $row["Code"] . "'";
        }
        updateIData($iconn, $query, true);
        $query = "INSERT INTO Transact (Transactdate, Transacttime, Username, Transactquery) VALUES (" . insertToday() . ", " . getNow() . ", '" . $username . "', 'Employee Date XML File Upload: Updated Employee Dates: " . $row["Code"] . ": Start Date = " . displayDate($row["StartDate"]) . ", End Date = " . displayDate($row["EndDate"]) . "')";
        updateIData($iconn, $query, true);
        $counter++;
    }
    $message = $message . "<br><br><font color='#FF0000'>File Uploaded. Rows Updated: " . $counter . "</font>";
}
echo "\r\n<html><head><title>Upload Employee Dates XML File</title></head>\r\n<style>input{background-color:'#F0F0F0';font-family:'Verdana';font-size:'10';}</style>\r\n<style>select{background-color:'#FFFFFF';font-family:'Verdana';font-size:'10';}</style>\r\n<script>\r\n\tfunction uploadFile(){\r\n\t\tx = document.frm1;\r\n\t\ty = x.file;\r\n\r\n\t\ta = y.value;\r\n\t\tb = a.substring((a.length-4), a.length);\r\n\r\n\t\tif (b != '.xml'){\r\n\t\t\talert('ONLY XML Files allowed to be uploaded');\r\n\t\t}else{\r\n\t\t\t//if (confirm('Are you sure you have Replaced Blank Spaces by Zeros in the XML File?')){\r\n\t\t\t\tx.bt.disabled = true;\r\n\t\t\t\tx.bt2.disabled = true;\r\n\t\t\t\tx.txtFileName.value = a.substring(a.lastIndexOf('\\\\')+1, a.length);\r\n\t\t\t\tx.submit();\r\n\t\t\t//}\r\n\t\t}\r\n\t}\r\n\t\r\n</script>\r\n<body><center><div align='center'>\t\r\n\t";
print "<center>";
print "</center>";
print "<table width='80%'>";
print "<tr><td width='100%' colspan='2' align='center'><br><font face='Verdana' size='2' color='#6481BD'><b>" . $message . "</b></font></td></tr>";
print "</table>";
echo "\t\r\n\t<form enctype=\"multipart/form-data\" action=\"UploadEmployeeDate.php\" method=\"post\" name=\"frm1\">\r\n\t\t<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"2000000\" />\r\n\t\t<input type=\"hidden\" name=\"act\" value=\"uploadFile\" />\r\n\t\t<input type=\"hidden\" name=\"txtFileName\" value=\"\" />\r\n\t\t<input type=\"hidden\" name=\"txtCheckFileName\" value=\"\" />\r\n\t\t<table width=\"600\" bgcolor='#F0F0F0' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'>\r\n\t\t\t<tr><td bgcolor='#FFFFFF'><font face='Verdana' size='2'>XML File to be Uploaded:</font></td><td bgcolor='#FFFFFF'><input type=\"file\" name=\"file\" /></td><td bgcolor='#FFFFFF'><input type=\"button\" value=\"Upload File\" onClick=\"javascript:uploadFile()\" name=\"bt\"/></td><td bgcolor='#FFFFFF'><input type=\"button\" value=\"Close Window\" onClick=\"javascript:window.close()\" name=\"bt2\"/></td></tr>\r\n\t\t\t<!-- <tr><td vAlign='top' colspan='3' bgcolor='#FFFFFF'><a href='javascript:;' onClick=\"javascript:slidedown('mydiv')\"><font face='Verdana' size='2' color='#000000'>Click Here to view the Steps to be followed before File Upload</font></a></td></tr>\r\n\t\t\t<tr><td colspan='3'><font face='Verdana' size='1'>\r\n\t\t\t\t<div id=\"mydiv\" style=\"display:none; overflow:hidden; height:450px;\">\r\n\t\t\t\t<b>01.</b> Open the Excel File to be uploaded<br>\r\n\t\t\t\t<b>02.</b> Click CTRL-H<br> \r\n\t\t\t\t<b>03.</b> Click Options<br>\r\n\t\t\t\t<b>04.</b> Check (Tick Mark) the Option (Match entire cell c<u>o</u>ntents)<br><br>\r\n\t\t\t\t<img src='http://www.indianhcabuja.com/img/excel/1.gif' border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1'><br><br>\r\n\t\t\t\t<b>05.</b> Enter \"0\" in \"R<u>e</u>place with:\" and Click Replace <u>A</u>ll<br>\r\n\t\t\t\t<b>06.</b> Click \"<u>F</u>ile\" - \"Save <u>A</u>s\" from the Menu Bar<br>\r\n\t\t\t\t<b>07.</b> Select \"XML Spreadsheet\" from the Drop Down under \"Save as <u>t</u>ype:\"<br>\r\n\t\t\t\t<b>08.</b> Choose the appropriate Folder<br>\r\n\t\t\t\t<b>09.</b> Click <u>S</u>ave<br>\r\n\t\t\t\t<b>10.</b> Upload this Saved File<br>\r\n\t\t\t\t</div>\r\n\t\t\t</font></td></tr> -->\r\n\t\t</table>\r\n\t</form>\r\n\t\r\n\t<script>\r\n\t\tfunction deleteFile(x){\r\n\t\t\tif (confirm('Delete this File?')){\r\n\t\t\t\tx.submit();\r\n\t\t\t}\r\n\t\t}\r\n\t</script>\r\n\t</p>\r\n</div></center></body></html>";
function add_date($Code, $StartDate, $EndDate)
{
    global $data;
    $data[] = array("Code" => $Code, "StartDate" => $StartDate, "EndDate" => $EndDate);
}

?>