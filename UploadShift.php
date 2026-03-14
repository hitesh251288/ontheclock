<?php
ob_start("ob_gzhandler");
error_reporting(E_ALL);
ini_set('display_errors', 1);
include "Functions.php";
$current_module = "31";
set_time_limit(0);
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];
if (!checkSession($userlevel, $current_module)) {
    header("Location: " . $config["REDIRECT"] . "?url=UploadShift.php&message=Session Expired or Security Policy Violated");
}
$conn = openConnection();
$iconn = openIConnection();
$act = $_GET["act"];
?>
<html>
    <head>
        <title>Upload Shift</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <!--<link rel="stylesheet" href="hitesh/font-awesome/font-awesome.min.css">-->
        <link rel="stylesheet" href="hitesh/css/bootstrap.min.css">
        <script src="hitesh/js/jquery.min.js"></script>
        <script src="hitesh/js/bootstrap.min.js"></script>
    <center><?php //displayHeader($prints, true, false);   ?></center>
    <center><?php //displayLinks($current_module, $userlevel);   ?></center>
    <style>
        table.excel {
            border-style:ridge;
            border-width:1;
            border-collapse:collapse;
            font-family:sans-serif;
            font-size:12px;
        }
        table.excel thead th, table.excel tbody th {
            background:#CCCCCC;
            border-style:ridge;
            border-width:1;
            text-align: center;
            vertical-align:bottom;
        }
        table.excel tbody th {
            text-align:center;
            width:20px;
        }
        table.excel tbody td {
            vertical-align:bottom;
        }
        table.excel tbody td {
            padding: 0 3px;
            border: 1px solid #EEEEEE;
        }
        table{
            border-collapse: separate;
            box-sizing: border-box;
            text-indent: initial;
            unicode-bidi: isolate;
            line-height: normal;
            font-weight: normal;
            font-size: medium;
            font-style: normal;
            color: -internal-quirk-inherit;
            text-align: start;
            border-spacing: 2px;
            border-color: gray;
            white-space: normal;
            font-variant: normal;
        }
    </style>
</head>
<body>
    <div class="container timerow">
        <div class="row">
            <div class="col-lg-12">
                <form action="" method="post" enctype="multipart/form-data" style="margin-left: 16px;"><br>
                    <center><b><font face="Verdana" size="1" color="#FF0000">Note: <br>
                            1. Shift upload must be in excel format of .xls<br>
                            2. All Employee Data and Shift must match existing data in On The Clock<br><br><br></font></b>
                        <table width="600" bgcolor="#F0F0F0" border="1" cellpadding="1" bordercolor="#C0C0C0" cellspacing="-1">
                            <tbody>
                                <tr>
                                    <td><font size="2">Name</font></td>
                                    <td><font size="2">Current Shift</font></td>
                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                    <td><font size="2">(YYYYMMDD)</font></td>
                                    <td><font size="2">(YYYYMMDD)</font></td>
                                    <td><font size="2">(YYYYMMDD)</font></td>
                                    <td><font size="2">(YYYYMMDD)</font></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                    <td><font size="2">T</font></td>
                                    <td><font size="2">F</font></td>
                                    <td><font size="2">S</font></td>
                                    <td><font size="2">S</font></td>
                                </tr>
                            </tbody>
                        </table>
                        <br>
                        <table width="600" bgcolor="#F0F0F0" border="1" cellpadding="1" bordercolor="#C0C0C0" cellspacing="-1">
                            <tbody>
                                <tr>
                                    <td bgcolor="#FFFFFF"><font face="Verdana" size="2">Upload .xls file:</font></td>
                                    <td bgcolor="#FFFFFF"><input type="file" name="uploadFile" value="" /></td>
                                    <td bgcolor="#FFFFFF"><input type="submit" name="submit" value="Upload File"/></td>
                                    <td bgcolor="#FFFFFF"><font size="2"><a href="sample.xls">Sample Download</a></font></td>
                                    <td bgcolor="#FFFFFF"><input type="button" value="Close Window" onclick="javascript:window.close()"></td>
                                </tr>
                                <tr></tr>
                            </tbody>
                        </table>
                    </center>
                    <!--                    <div class="form-group">
                                            <label for="Upload excel">Upload excel file : </label><br><br>
                                            <input type="file" name="uploadFile" value="" />
                                        </div>
                                        <input type="submit" name="submit" value="Upload" class="btn btn-default"/><br><br>
                                        <a href="sample.xls">Sample Download</a>-->
                </form>
            </div>
        </div>
    </div>
    <?php
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

require 'vendor/autoload.php'; // make sure PhpSpreadsheet is installed via Composer

if (isset($_POST['submit'])) {
    
    $filename = $_FILES['uploadFile']['name'];
    $file = __DIR__ . "/upload/" . $filename;

    if (move_uploaded_file($_FILES['uploadFile']['tmp_name'], $file)) {
        
        try {
            // Load Excel file (supports .xls and .xlsx)
            $spreadsheet = IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true); // Keep array keys as column letters

        } catch (Exception $e) {
            die("Error reading file: " . $e->getMessage());
        }

        // Fetch shift group data
        $queryShift = "SELECT * FROM tgroup";
        $shiftData = mysqli_query($iconn, $queryShift);
        $shiftcount = mysqli_num_rows($shiftData);

        $groupid = [];
        $shiftName = [];
        $shiftwisedata = [];

        while ($fetchdata = mysqli_fetch_row($shiftData)) {
            $groupid[] = $fetchdata[0];
            $shiftName[] = $fetchdata[1];
            $shiftwisedata[] = array(
                'groupid' => $fetchdata[0],
                'shiftname' => $fetchdata[1]
            );
        }

        $totalrows = count($rows);
        $totalcolums = count($rows[1]); // number of columns in first row

        for ($row = 1; $row <= $totalrows; $row++) {
            if ($row >= 4) {
                $id = $rows[$row]['A']; // Column 1 in old code is now 'A'

                for ($k = 4; $k <= $totalcolums; $k++) {
                    // Convert numeric column index to letter (e.g., 4 -> D)
                    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($k);

                    if (!empty($rows[$row][$colLetter])) {
                        $e_gropup = null;

                        for ($d = 0; $d < count($shiftwisedata); $d++) {
                            if ($rows[$row][$colLetter] == $shiftName[$d]) {
                                $e_gropup = $groupid[$d];
                            }
                        }

                        $dateColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($k);
                        $dateValue = $rows[1][$dateColLetter];

                        $shiftQuery = "SELECT e_date,e_group,e_id FROM shiftroster 
                                       WHERE e_id='$id' 
                                       AND e_date='" . $dateValue . "'";
                        $shiftResult = mysqli_query($iconn, $shiftQuery);

                        if ($shiftResult->num_rows > 0) {
                            $updateQuery = "UPDATE shiftroster 
                                            SET e_group='$e_gropup' 
                                            WHERE e_id='$id' 
                                            AND e_date='" . $dateValue . "'";
                            updateIData($iconn, $updateQuery, true);
                        } else {
                            $insertQuery = "INSERT INTO shiftroster (e_id,e_date,e_group)
                                            VALUES ($id, '$dateValue', $e_gropup)";
                            updateIData($iconn, $insertQuery, true);
                        }
                    }
                }
            }
        }
        echo "Shift uploded successfully.";
    } else {
        die("Upload failed.");
    }

} else {
    echo '<span class="msg" style="margin-left:21%;">Select an excel file first!</span>';
}

    ?>

</body>
</html>