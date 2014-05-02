<?php
$list = array($resumename => "Active Resume",
			  "Contact Information" => "index.php",
			  "Position Sought" => "position.php",
			  "Employment History" => "employment.php",
			  "Resume" => "resume.php",
			  "Archive" => "archive.php",
			  "Help" => "Help.html");

//creates the navigation list
//sets the class to be selected if the curret page matches the key in the array
foreach ($list as $key => $val)
{
	//display the resume name if exists
	if ( (strcmp($val, "Active Resume") == 0)  && (strlen($key) >= 5) )
		echo '<li class="resname">'.$val.': '.$key.'</li>';
	else if (strcmp($key, $pageName) == 0)
		echo '<li class="selected"><a href="'.$val.'">'.$key.'</a></li>';
	else 
		echo '<li><a href="'.$val.'">'.$key.'</a></li>';
}
?>
