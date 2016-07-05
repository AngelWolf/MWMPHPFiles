<?php

include_once "mmoconnection.php"; 

$mydata = json_decode(file_get_contents('php://input'));

$teams = $mydata->teams;

foreach ($teams as &$entry) {
	
	$teammembers = $entry->teammembers;
	
	//Get the first character in the array.
	$charid = $teammembers[0]->CharacterID;
		
	$stmt = $conn->prepare("SELECT team_id FROM characters WHERE id = ? ");

	$stmt->bind_param("i", $charid);  // "i" means the database expects an integer

	$stmt->execute();

	$stmt->bind_result($team_id);
	
	if ($team_id != NULL) {
		
		
	
}