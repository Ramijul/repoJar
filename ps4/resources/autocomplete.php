
<?php

function openConnection()
{
	$pw = '915198305';
	$DBH = new PDO ( "mysql:host=atr.eng.utah.edu;dbname=cs4540_ramijuli", 'cs4540_ramijuli', $pw);
	$DBH->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
	return $DBH;
}

if (!isset($_GET['term']))
	exit();

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

$name = trim($_GET['term']);
$data = Array();
try
{
	$name = mysql_real_escape_string($name);
	
	$DBH = openConnection();
	$DBH->beginTransaction();
	
	$stmt = $DBH->prepare("select resumeName from Personal_Info where resumeName like ?");
	$stmt->bindValue(1, $name."%");
	
	$stmt->execute();
	
	while ($row = $stmt->fetch()) {
		$retVal = $row['resumeName'];
		$data[] = Array('label' => $retVal , 'value' => $retVal);
	}
}
catch (PDOException $e)
{
	
}

//send the data through a json call
echo json_encode($data);

