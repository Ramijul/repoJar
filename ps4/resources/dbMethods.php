<?php

require 'resources/connectDB.php';

//saves the data by the resume name
function save($name) 
{
	if (nameExists($name))
	{
		debug_to_console("in save nameExists true");
		return updateRows($name);
	}
	else 
	{
		debug_to_console("in save nameExists fasle");
		return insertRow($name);
	}
}

/**
 * Send debug code to the Javascript console
 */
function debug_to_console($data) {
	if(is_array($data) || is_object($data))
	{
		echo("<script>console.log('PHP: ".json_encode($data)."');</script>");
	} else {
		echo("<script>console.log('PHP: ".$data."');</script>");
	}
}




/****************** New code begins **************************/

//update the required tables
function updateRows($name)
{
	$resumeDetails = $GLOBALS["resumeDetails"];
	//indicates information from which pages need to be inserted
	$infoPage = false;//indicates if contact info page has been filled
	$hasPosition = false;//indicates if position sought page has been filled
	$experience = false;//indicates if experience page has been filled
	
	//all the fields required for the updates
	$person = '';
	$phone = '';
	$address = '';
	$position = '';
	$startdate = Array();
	$enddate = Array();
	$text = Array();
	$numOfJobs = '';
	
	if (array_key_exists('experience', $resumeDetails) )
		$experience = $resumeDetails['experience'];
	
	debug_to_console("in update rows ");
	
	//now insert them as appropriate
	try
	{
		$DBH = openConnection();
		$DBH->beginTransaction();
	
		$query = "";
		$stmt = "";
		
		//set up the data required for the updating the info page
		if (array_key_exists('contactInfo', $resumeDetails) && (strcmp($resumeDetails['contactInfo'], 'modified') == 0))
		{
			//these three fields would never be blank if the contact information page was modified
			$person = $resumeDetails['name'];
			$address = $resumeDetails['address'];
			$phone = $resumeDetails['number'];
			
			$query = "update Personal_Info set name = ?, phoneNumber = ?, address = ? where resumeName = ?";
			$stmt = $DBH->prepare($query);
			
			$stmt->bindValue(1, mysql_real_escape_string($person));
			$stmt->bindValue(2, mysql_real_escape_string($phone));
			$stmt->bindValue(3, mysql_real_escape_string($address));
			$stmt->bindValue(4, mysql_real_escape_string($name));
			
			$stmt->execute();
		}
		if (array_key_exists('position', $resumeDetails))
		{
			$position = $resumeDetails['position'];
			
			$query = "update Personal_Info set positionSought = ? where resumeName = ?";
			$stmt = $DBH->prepare($query);
				
			$stmt->bindValue(1, mysql_real_escape_string($position));
			$stmt->bindValue(2, mysql_real_escape_string($name));
			
			$stmt->execute();
		}
		if ($experience)
		{
			$stmt = $DBH->prepare("select count(jobNumber) as count from Job_History where resumeName like ?");
			$stmt->bindValue(1, mysql_real_escape_string($name));
			$stmt->execute();
			
			debug_to_console("in experience");
			
			$count = '';
			while ($row = $stmt->fetch())
				$count = $row['count'];
			
			//delete any rows that exist by the resume name provided
			if ($count > 0)
			{
				$stmt = $DBH->prepare("delete from Job_History where resumeName like ?");
				$stmt->bindValue(1, $name);
				$stmt->execute();
			}
			debug_to_console($count);
			
			//insert the new rows
			//get all the stored data from session for the experience page
			$numOfJobs = $resumeDetails['numberofjobs'];
			$enddate = $resumeDetails['enddate'];
			$startdate = $resumeDetails['startdate'];
			$text = $resumeDetails['text'];
			
			debug_to_console($numOfJobs);
			debug_to_console($enddate);
			debug_to_console($startdate);
			debug_to_console($text);
			
			$query = "insert into Job_History (resumeName, startDate, endDate, descr) values";
				
			//execute the above query as many times as jobs were entered
			for ($i = 0; $i < $numOfJobs; $i++)
			{
				$comma = '';
				
			if ($i >= 1)
				$comma = ',';
					
				$query .= "$comma (?,?,?,?)";
			}
				
			$stmt = $DBH->prepare($query);
			
			$k = 1;
			for ($j = 0; $j < $numOfJobs; $j++)
			{
				$stmt->bindValue($k++, $name);
				$stmt->bindValue($k++, $startdate[$j]);
				$stmt->bindValue($k++, $enddate[$j]);
				$stmt->bindValue($k++, $text[$j]);
			}
			
			debug_to_console($stmt);
			
			$stmt->execute();
			debug_to_console("executed experience");
		}
		$DBH->commit();
		return true;
	}
	catch (PDOException $e) 
	{
		return false;
	}
}
/******************End of new code***************************/

//inserts rows into both tables according to user inputs
//the tables are Personal_Info (holds everything but the employment history inputs);
//and, Job_History (one row for each job).
function insertRow($name)
{
	$resumeDetails = $GLOBALS["resumeDetails"];
	//indicates information from which pages need to be inserted
	$infoPage = false;//indicates if contact info page has been filled
	$hasPosition = false;//indicates if position sought page has been filled
	$experience = false;//indicates if experience page has been filled
	
	//all the fields required for the insert statements
	$person = '';
	$phone = '';
	$address = '';
	$position = '';
	$startdate = Array();
	$enddate = Array();
	$text = Array();
	$numOfJobs = '';
	
	debug_to_console("in insertRow ");
	debug_to_console($resumeDetails);
	
	//set up the data required for the inserts
	if (array_key_exists('contactInfo', $resumeDetails) && (strcmp($resumeDetails['contactInfo'], 'modified') == 0))
	{
		//these three fields would never be blank if the contact information page was modified
		$person = $resumeDetails['name'];
		$address = $resumeDetails['address'];
		$phone = $resumeDetails['number'];
		
		$infoPage = true;
	}

	if (array_key_exists('position', $resumeDetails))
	{
		$position = $resumeDetails['position'];
		$hasPosition = true;
	}
	
	if (array_key_exists('experience', $resumeDetails))
		$experience = $resumeDetails['experience'];
	
	if ($experience)
	{
		//get all the stored data from session for the experience page
		$numOfJobs = $resumeDetails['numberofjobs'];
		$enddate = $resumeDetails['enddate'];
		$startdate = $resumeDetails['startdate'];
		$text = $resumeDetails['text'];
	}
	
	//now insert them as appropriate
	try{
		$DBH = openConnection();
		$DBH->beginTransaction();
		
		$query = "";
		$stmt = "";
		
		//info page or the position page has been completed
		if ($infoPage || $hasPosition)
		{
			//only insert the values that have been filled out
			//assuming that the none of the two pages can be partially filled.
			if ($infoPage && $hasPosition)
			{
				$query = "insert into Personal_Info (resumeName, name, address, phoneNumber, positionSought) values (?,?,?,?,?)";
				$stmt = $DBH->prepare($query);
				$stmt->bindValue(1, mysql_real_escape_string($name));
				$stmt->bindValue(2, mysql_real_escape_string($person));
				$stmt->bindValue(3, mysql_real_escape_string($address));
				$stmt->bindValue(4, mysql_real_escape_string($phone));
				$stmt->bindValue(5, mysql_real_escape_string($position));
				
				debug_to_console("infoPage && hasPosition");
			}
			else if ($infoPage && !$hasPosition)
			{
				$query = "insert into Personal_Info (resumeName, name, address, phoneNumber) values (?,?,?,?)";
				$stmt = $DBH->prepare($query);
				$stmt->bindValue(1, mysql_real_escape_string($name));
				$stmt->bindValue(2, mysql_real_escape_string($person));
				$stmt->bindValue(3, mysql_real_escape_string($address));
				$stmt->bindValue(4, mysql_real_escape_string($phone));	
				debug_to_console("infoPage && !hasPosition");
			}
			else if (!$infoPage && $hasPosition)
			{
				$query = "insert into Personal_Info (resumeName, position) values (?,?)";
				$stmt = $DBH->prepare($query);
				$stmt->bindValue(1, mysql_real_escape_string($name));
				$stmt->bindValue(2, mysql_real_escape_string($position));
				debug_to_console("!infoPage && hasPosition");
			}
			debug_to_console($stmt);
			$stmt->execute();
			debug_to_console("executed personal info");
		}		
		if ($experience)//if experience page has been filled out
		{
			$query = "insert into Job_History (resumeName, startDate, endDate, descr) values";
			
			//execute the above query as many times as jobs were entered 
			for ($i = 0; $i < $numOfJobs; $i++)
			{	
				$comma = '';
				if ($i >= 1)
					$comma = ',';
				$query .= "$comma (?,?,?,?)";
			}

			$stmt = $DBH->prepare($query);
			
			debug_to_console("in experience");
			debug_to_console($query);
			$k = 1;
			for ($j = 0; $j < $numOfJobs; $j++)
			{
				$stmt->bindValue($k++, $name);
				$stmt->bindValue($k++, $startdate[$j]);
				$stmt->bindValue($k++, $enddate[$j]);
				$stmt->bindValue($k++, $text[$j]);
			}
			debug_to_console($stmt);
			$stmt->execute();
			debug_to_console("executed experience");
		}
		$DBH->commit();
		return true;
	}
	catch (PDOException $e) {
		debug_to_console($e->__toString());
		return false;
	}
}

//deletes the resume by its name
function delete($name)
{
	try {
	
		debug_to_console("delete $name");
		//sanitize the input
		$name = mysql_real_escape_string($name);
	
		$DBH = openConnection();
		$DBH->beginTransaction();
	
		$stmt = $DBH->prepare("delete from Personal_Info where resumeName like ?");
		$stmt->bindValue(1, $name);
		$stmt->execute();
		
		$DBH->commit();
		return true;
	}
	catch (PDOException $e) {
		debug_to_console($e->__toString());
		return false;
	}	
}

function load($name)
{
	try {
	
		//sanitize the input
		$name = mysql_real_escape_string($name);
		$info = Array();
	
		$DBH = openConnection();
		$DBH->beginTransaction();
	
		$stmt = $DBH->prepare("select * from Personal_Info where resumeName = ?");
		$stmt->bindValue(1, $name);
		$stmt->execute();
		
		while ($row = $stmt->fetch()) {
			$info['resumename'] = $row['resumeName'];
			$info['name'] = ($row['name'] !== NULL)? $row['name']:'';
			$info['address'] = ($row['address'] !== NULL)? $row['address']:'';
			$info['number'] = ($row['phoneNumber'] !== NULL)? $row['phoneNumber']:'';
			$info['position'] = ($row['positionSought'] !== NULL)? $row['positionSought']:'';
		}
		
		$stmt = $DBH->prepare("select * from Job_History where resumeName = ?");
		$stmt->bindValue(1, $name);
		$stmt->execute();
		
		$sDate = Array();//start date
		$eDate = Array();//end date
		$jobDescr = Array();//text
		$rowNum = 0;
		while ($row = $stmt->fetch()) {
			$rowNum++;
			$info['experience'] = true;
			array_push($sDate, $row['startDate']);
			array_push($eDate, $row['endDate']);
			array_push($jobDescr, $row['descr']);
		}
		$info['resumename'] = $name;
		$info['numberofjobs'] = $rowNum;
		$info['startdate'] = $sDate;
		$info['enddate'] = $eDate;
		$info['text'] = $jobDescr;
		
		$GLOBALS["resumeDetails"] = $info;
		return true;
	}
	catch (PDOException $e) {
		return false;
	}
}

//checks if a Resume Name exists in the db
//returns true if found
function nameExists($name) {
	try {
		//sanitize the input
		$name = mysql_real_escape_string($name);
		
		$DBH = openConnection();
		$DBH->beginTransaction();

		$stmt = $DBH->prepare("select resumeName from Personal_Info where resumeName like ?");
		$stmt->bindValue(1, $name);
		$stmt->execute();
		
		if((strcmp($stmt->fetch()['resumeName'], $name) == 0))
			return true;
		else
			return false;
	}
	catch (PDOException $e) {
		return false;
	}
}