<?php

$resumename = '';

if (isset($_REQUEST['resName']))
	$resumename = $_REQUEST['resName'];

$name = '';
$address = '';
$number = '';
$startdate = Array();
$enddate = Array();
$text = Array();
$errMsg = '';
$numOfJobs = 0;
$experience = false;

require ("resources/getViewDetails.php");

require ("resources/viewHeader.php");
require ("viewBody.php");
?>
