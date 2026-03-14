<?php


ob_start("ob_gzhandler");
include "Functions.php";
error_reporting(E_ALL);
$conn = openConnection();
$iconn = openIConnection();
$query = "TRUNCATE TABLE UNIS.tcommanddown";
updateIData($iconn, $query, true);

?>