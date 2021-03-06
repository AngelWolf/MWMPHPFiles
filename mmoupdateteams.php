<?php
session_start();
include_once "mmoconnection.php"; 

$server_id = str_replace('PHPSESSID=', '', SID);

$mydata = json_decode(file_get_contents('php://input'));

$teams = $mydata->teams;

$team_ids = array();

$stmt = $conn->prepare("DELETE FROM teams WHERE server_id = ? ");

$stmt->bind_param("s", $server_id);
	
$stmt->execute();
	
$stmt->close();

foreach ($teams as &$entry) {
	
	$teammembers = $entry->teammembers;
	
	//Get the first character in the array.
	$charid = $teammembers[0]->CharacterID;
		
	$stmt = $conn->prepare("SELECT team_id FROM characters WHERE id = ? ");

	$stmt->bind_param("i", $charid);  // "i" means the database expects an integer

	$stmt->execute();

	$stmt->bind_result($team_id);
	
	if ($team_id != NULL) {
		
		//Already have team.  Update players entry.  Return team_id.
		
		$CIDArray = array();
		
		foreach ($teammembers as &$entry2) {
			
				$CIDArray[] = $entry2->CharacterID;	

				error_log($entry2->CharacterID, 0);
				
				$stmt = $conn->prepare("UPDATE characters SET team_id = ? WHERE id = ? ");
				
				$stmt->bind_param("si", $team_id, $entry2->CharacterID);  // "s" means the database expects a string
				
				$stmt->execute();
	
				$stmt->close();				
		}	
		
		$CharacterIDs = implode(",", $CIDArray);
		
		$stmt = $conn->prepare("INSERT INTO teams VALUES ( ?, ?, ? )");

		$stmt->bind_param("ssi", $server_id, $team_id, $CharacterIDs);

		$stmt->execute();
		
		$stmt->close();
				
	}
	else {
		
		//Generate new team_id.
		
		$team_id = substr(md5(rand()), 7, 10);		
		
		$CIDArray = array();
		
		foreach ($teammembers as &$entry2) {
			
			$CIDArray[] = $entry2->CharacterID;		
			
			$stmt = $conn->prepare("UPDATE characters SET team_id = ? WHERE id = ? ");
			
			$stmt->bind_param("si", $team_id, $entry2->CharacterID);  // "s" means the database expects a string
			
			$stmt->execute();
	
			$stmt->close();				
		}	
		
		$CharacterIDs = implode(",", $CIDArray);
				
		$stmt = $conn->prepare("INSERT INTO teams VALUES (?, ?, ?)"); 
		
		$stmt->bind_param("ssi", $server_id, $team_id, $CharacterIDs);  // "s" means the database expects a string
		
		$stmt->execute();
	
		$stmt->close();
		
	}
		
	//Add team_id to response object.
	$team_ids[] = $team_id;
	
}

echo  json_encode(array('status'=>'OK', 'team_ids'=> $team_ids));


?>