<?php


ob_start("ob_gzhandler");
error_reporting(E_ALL);
date_default_timezone_set("Africa/Algiers");
set_time_limit(0);
include "Functions.php";
include "ftp.php";
$text = "";
$local_dir = ".";
$remote_dir = ".";
print "<html><body><center><font face='Verdana' size='3'><b>";
print "<p align='center'><font face='Verdana' size='2'><b><u><i>Executing Script</i></u> <br><br><div id='img1' align='center'>This Process may take a Long Time <br><br>Please DO NOT Close this Window</b></font><br><img src='img/processing.gif' name='horse' onClick=alert('ExecutingScript')></div></p>";
$ftp_conn = ftp_connect(decryptString("PQ69HY\";N-79)"));
if (ftp_login($ftp_conn, decryptString("PQ69HY\";N-79`E&9REF=/"), decryptString("``0.Q0S7R%V:?178M]U:C%&:0"))) {
    $text = "Online Version Update Process Successfully Completed";
} else {
    $text = "Online Version Update Process Failed. Server Connection Unavailable";
}
$ftp = new FTP();
$ftp->download($local_dir, $remote_dir, $ftp_conn);
ftp_close($ftp_conn);
exec("php VersionUpdate.php");
exec("php Register.php");
$iconn = openIConnection();
$query = "INSERT INTO ProcessLog (PType, PDate, PTime) VALUES ('" . $text . "', " . insertToday() . ", '" . getNow() . "')";
updateIData($iconn, $query, true);
print $text;
print "<br><br><input type='button' onClick='javascript:closeWindow()'>";
print "</b></font></center></body></html>";

?>