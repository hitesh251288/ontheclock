<?php
ob_start("ob_gzhandler");
error_reporting(E_ERROR);
include "Functions.php";
$current_module = "31";
set_time_limit(0);
session_start();
$session_variable = $config["SESSION_VARIABLE"];
$userlevel = $_SESSION[$session_variable . "userlevel"];
$username = $_SESSION[$session_variable . "username"];

$conn = openConnection();

$query = "SELECT * FROM tuser where id=".$_GET['id'];
$result = mysqli_query($conn, $query);
$empData = mysqli_fetch_assoc($result);

$fullName = $empData['name'];
$dob = "";
$gender = $empData['idno'];
$nationality = "American";
$phoneNumber = $empData['phone'];
$employeeId = addZero($empData['id'], $_SESSION[$session_variable . "EmployeeCodeLength"]);
$department = $empData['dept'];
$employmentStatus = $empData['PassiveType'];
$dateOfHire = date('Y-m-d H:i:s', strtotime($empData['reg_date']));
$workLocation = $empData[''];
$reportsTo = $empData[''];
$rate = $empData[''];
$bankAccountInfo = $empData[''];
$bonusIncentives = $empData[''];
$workSchedule = $empData[''];
$overtimeRates = $empData[''];
$employeeSignature = $empData[''];
$sectionHeadSignature = $empData[''];
$encodedBy = $empData[''];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Information Sheet</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }

        .container {
            width: 50%;
            margin: 30px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
        }

        th, td {
            padding: 6px;
            border-bottom: 1px solid #e0e0e0;
            text-align: left;
            border-left: 1px solid #e0e0e0;
        }

        th {
            background-color: #f9f9f9;
        }

        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            border: none;
            color: white;
            cursor: pointer;
            margin-right: 10px;
        }

        button:hover {
            background-color: #45a049;
        }

        h1 {
            font-size: 24px;
            text-align: center;
            margin-bottom: 20px;
        }

        p {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        /* Hide the buttons for print */
        @media print {
            #printButtons {
                display: none;
            }
            
            body {
                margin: 0;
                padding: 0;
            }

            table {
                width: 100%;
            }
            
            .container{width:80%;}
            
            @page {
                size: auto;   /* Auto size allows the content to expand to the full page */
                margin: 0;    /* Set margins to 0 to remove any blank spaces */
            }
            
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Employee Information Sheet</h1>
    <!--</div>-->
    <table>
        <tr>
            <th colspan="2">PERSONAL INFORMATION</th>
        </tr>
        <tr>
            <td>Full Name:</td>
            <td><?php echo $fullName; ?></td>
        </tr>
        <tr>
            <td>Date of Birth:</td>
            <td><?php echo $dob; ?></td>
        </tr>
        <tr>
            <td>Gender:</td>
            <td><?php echo $gender; ?></td>
        </tr>
        <tr>
            <td>Nationality:</td>
            <td><?php echo $nationality; ?></td>
        </tr>
        <tr>
            <td>Phone number:</td>
            <td><?php echo $phoneNumber; ?></td>
        </tr>
        <tr>
            <th colspan="2">EMPLOYMENT DETAILS</th>
        </tr>
        <tr>
            <td>Employee ID:</td>
            <td><?php echo $employeeId; ?></td>
        </tr>
        <tr>
            <td>Department:</td>
            <td><?php echo $department; ?></td>
        </tr>
        <tr>
            <td>Employment Status:</td>
            <td><?php echo $employmentStatus; ?></td>
        </tr>
        <tr>
            <td>Date of Hire:</td>
            <td><?php echo $dateOfHire; ?></td>
        </tr>
        <tr>
            <td>Work Location:</td>
            <td><?php echo $workLocation; ?></td>
        </tr>
        <tr>
            <td>Reports To:</td>
            <td><?php echo $reportsTo; ?></td>
        </tr>
        <tr>
            <th colspan="2">FOR HR USE ONLY</th>
        </tr>
        <tr>
            <td>Rate:</td>
            <td><?php echo $rate; ?></td>
        </tr>
        <tr>
            <td>Bank Account Info:</td>
            <td><?php echo $bankAccountInfo; ?></td>
        </tr>
        <tr>
            <td>Bonus/Incentives:</td>
            <td><?php echo $bonusIncentives; ?></td>
        </tr>
        <tr>
            <td>Work Schedule:</td>
            <td><?php echo $workSchedule; ?></td>
        </tr>
        <tr>
            <td>Overtime Rates:</td>
            <td><?php echo $overtimeRates; ?></td>
        </tr>
        <tr>
            <td>Employee Signature:</td>
            <td><?php echo $employeeSignature; ?></td>
        </tr>
        <tr>
            <td>Section Head Signature:</td>
            <td><?php echo $sectionHeadSignature; ?></td>
        </tr>
        <tr>
            <td>Encoded By:</td>
            <td><?php echo $encodedBy; ?></td>
        </tr>
    </table>
    <br>
    <div id="printButtons"> <!-- Add a div to contain buttons -->
        <button onclick="window.print()">Print</button>
        <button onclick="exportToExcel()">Export to Excel</button>
        <!--<button onclick="exportToPDF()">Export to PDF</button>-->
    </div>
    </div>
    

    <script>
        function exportToExcel() {
            var table = document.querySelector('table');
            var html = table.outerHTML;
            window.open('data:application/vnd.ms-excel,' + encodeURIComponent(html));
        }

        function exportToPDF() {
            var doc = new jsPDF();
            doc.fromHTML(document.body, 15, 15, {
                'width': 170
            });
            doc.save('employee_information_sheet.pdf');
        }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.2/jspdf.min.js"></script>
</body>
</html>

