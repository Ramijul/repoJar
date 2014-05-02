<?php

require 'resources/connectDB.php';

//saves the data by the resume name

try {

	//sanitize the input
	$resname = mysql_real_escape_string($resumename);

	$DBH = openConnection();
	$DBH->beginTransaction();

	$stmt = $DBH->prepare("select * from Personal_Info where resumeName = ?");
	$stmt->bindValue(1, $resname);
	$stmt->execute();
	
	while ($row = $stmt->fetch()) {
		$name = ($row['name'] !== NULL)? $row['name']:'';
		$address = ($row['address'] !== NULL)? $row['address']:'';
		$number = ($row['phoneNumber'] !== NULL)? $row['phoneNumber']:'';
		$position = ($row['positionSought'] !== NULL)? $row['positionSought']:'';
	}
	
	$stmt = $DBH->prepare("select * from Job_History where resumeName = ?");
	$stmt->bindValue(1, $resname);
	$stmt->execute();
	
	$rowNum = 0;
	while ($row = $stmt->fetch()) {
		$rowNum++;
		$experience = true;
		array_push($startdate, $row['startDate']);
		array_push($enddate, $row['endDate']);
		array_push($text, $row['descr']);
	}
	$numOfJobs = $rowNum;
}
catch (PDOException $e) {
	$errMsg = 'There was a problem while loading the resume. Please try again!';
}


?>