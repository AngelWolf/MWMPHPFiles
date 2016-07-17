<?php

include_once "mmoconnection.php"; 

$mydata = json_decode(file_get_contents('php://input'));

//error_log("hello", 0);

$charid = $mydata ->charid;

$health = $mydata ->health;
$energy = $mydata ->energy;

$level = $mydata ->level;
$experience = $mydata ->experience;

$dialogues = $mydata ->dialogues;
$quests = $mydata ->quests;
$quest_nodes = $mydata ->quest_nodes;

$current_port = $mydata ->current_port;
$posx = $mydata ->posx;
$posy = $mydata ->posy;
$posz = $mydata ->posz;
$in_instance = $mydata ->in_instance;
$has_entered = $mydata ->has_entered;

$yaw = $mydata ->yaw;

$stmt = $conn->prepare("UPDATE characters SET health = ?, energy = ?, experience = ?, level = ?, current_port = ?, posx = ?, posy = ?, posz = ?, yaw = ?, in_instance = ?, has_entered = ? WHERE id = ? ");

$stmt->bind_param("iiiiiiiiiiii", $health, $energy, $experience, $level, $current_port, $posx, $posy, $posz, $yaw, $in_instance, $has_entered, $charid);  // "s" means the database expects a string

$stmt->execute();
	
$stmt->close();

if ($in_instance == 1) {
	
	$previous_port = $mydata ->previous_port;
	$pp_posx = $mydata ->pp_posx;
	$pp_posy = $mydata ->pp_posy;
	$pp_posz = $mydata ->pp_posz;
	$pp_yaw = $mydata ->pp_yaw;
	
	$stmt = $conn->prepare("UPDATE characters SET health = ?, energy = ?, experience = ?, level = ?, previous_port = ?, pp_posx = ?, pp_posy = ?, pp_posz = ?, pp_yaw = ?, in_instance = ?, has_entered = ? WHERE id = ? ");

	$stmt->bind_param("iiiiiiiiiiii", $health, $energy, $experience, $level, $previous_port, $pp_posx, $pp_posy, $pp_posz, $pp_yaw, $in_instance, $has_entered, $charid);  // "s" means the database expects a string

	$stmt->execute();
	
	$stmt->close();
	
	
}



$stmt = $conn->prepare("DELETE FROM dialogues WHERE character_id = ? ");

$stmt->bind_param("i", $charid);
	
$stmt->execute();
	
$stmt->close();

$stmt = $conn->prepare("DELETE FROM quests WHERE character_id = ? ");

$stmt->bind_param("i", $charid);
	
$stmt->execute();
	
$stmt->close();

$stmt = $conn->prepare("DELETE FROM quest_nodes WHERE character_id = ? ");

$stmt->bind_param("i", $charid);
	
$stmt->execute();
	
$stmt->close();

foreach ($dialogues as &$entry) {

	$fragment_name = $entry->fragment_name;
	$fragment_state = $entry->fragment_state;
	
	$stmt = $conn->prepare("INSERT INTO dialogues VALUES (NULL, ?, ?, ? )");

	$stmt->bind_param("isi", $charid, $fragment_name, $fragment_state);

	$stmt->execute();
		
	$stmt->close();

}

foreach ($quests as &$entry) {

	$quest_name = $entry->quest_name;
	$quest_state = $entry->quest_state;
	
	$stmt = $conn->prepare("INSERT INTO quests VALUES (NULL, ?, ?, ? )");

	$stmt->bind_param("isi", $charid, $quest_name, $quest_state);

	$stmt->execute();
		
	$stmt->close();

}

foreach ($quest_nodes as &$entry) {

	$quest_node_name = $entry->quest_node_name;
	$quest_node_state = $entry->quest_node_state;
	
	$stmt = $conn->prepare("INSERT INTO quest_nodes VALUES (NULL, ?, ?, ? )");

	$stmt->bind_param("isi", $charid, $quest_node_name, $quest_node_state);

	$stmt->execute();
		
	$stmt->close();

}


echo json_encode(array('status'=>'OK' ));

?>
