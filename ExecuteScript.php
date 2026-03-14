<?php


ob_start("ob_gzhandler");
error_reporting(E_ERROR);
set_time_limit(0);
if ($_GET["display"] == "") {
    print "<script language='JavaScript'> var message='Executing Script. Please DO NOT Click OR Close this Window'; function click(e) {if (document.all) {if (event.button == 2) {alert(message);return false;}}if (document.layers) {if (e.which == 3) {alert(message);return false;}}}if (document.layers) {document.captureEvents(Event.MOUSEDOWN);}document.onmousedown=click;</script>";
    print "<body>";
    print "<p align='center'><font face='Verdana' size='2'><b><u><i>Executing Script</i></u> <br><br><div id='img1' align='center'>This Process may take a Long Time <br><br>Please DO NOT Close this Window</b></font><br><img src='img/processing.gif' name='horse' onClick=alert('ExecutingScript')></div></p>";
    print "<iframe align='center' height='200' width='400' src='ExecuteScript.php?script=" . $_GET["script"] . "&display=no' SCROLLING='no' FRAMEBORDER='0' border=0></iframe>";
    print "</body>";
} else {
    print "<body onLoad=javascript:parent.document.getElementById('img1').style.display='none'>";
    print "<style>input{background-color:'#F0F0F0';font-family:'Verdana';font-size:'10';}</style>";
    if ($_GET["script"] == "PayMasterAttendanceCSV") {
        exec("php PayMasterAttendance.php csv");
    } else {
        exec("php " . $_GET["script"] . ".php");
    }
    print "<p align='center'><font face='Verdana' size='2'><b>Script Executed</b></font><br><br><input type='button' value='Close Window' onClick='javascript:parent.window.close()'></p>";
    print "<p align='center'><font face='Verdana' size='2'><b>Script Execution Result</b></font><br><br>";
    include "Functions.php";
    $conn = openConnection();
    $query = "SELECT PDate, PTime, PType FROM ProcessLog WHERE PDate = " . insertToday() . " ORDER BY PTime DESC ";
    $result = mysqli_query($conn, $query);
    print "<table border='1' cellpadding='1' bordercolor='#C0C0C0' cellspacing='-1' width='100%'>";
    print "<tr><td><font face='Verdana' size='2'><b>Process Date</b></font></td> <td><font face='Verdana' size='2'><b>Process Time</b></font></td> <td><font face='Verdana' size='2'><b>Process Type</b></font></td> </tr>";
    while ($cur = mysqli_fetch_row($result)) { 
        displayDate($cur[0]);
        displayTime($cur[1]);
        print "<tr><td><font face='Verdana' size='1'>" . displayDate($cur[0]) . "</font></td> <td><font face='Verdana' size='1'>" . displayTime($cur[1]) . "</font></td> <td><font face='Verdana' size='1'>" . $cur[2] . "</font></td> </tr>";
    }
    print "</table>";
    print "</p>";
    print "</body>";
}

?>