<?php
include_once "mmoconnection.php"; 

$mydata = json_decode(file_get_contents('php://input'));
$charid = $mydata ->charid;
$userid = $mydata ->userid;

$stmt = $conn->prepare("SELECT name, health, energy, level, experience, posx, posy, posz, yaw, in_instance, has_entered, previous_port,
current_port, pp_posx, pp_posy, pp_posz, pp_yaw FROM characters WHERE id = ? ");

$stmt->bind_param("i", $charid);  // "i" means the database expects an integer

$stmt->execute();

$stmt->bind_result($row_name, $row_health, $row_energy, $row_level, $row_experience, $row_posx, $row_posy, $row_posz, $row_yaw, $in_instance,
$has_entered, $previous_port, $current_port, $pp_posx, $pp_posy, $pp_posz, $pp_yaw);

  // output data of each row
if($stmt->fetch()) {

	$stmt->close();

	//store character id in session table:
	$stmt = $conn->prepare("UPDATE active_logins SET character_id = ? WHERE user_id = ? ");

	$stmt->bind_param("ii", $charid, $userid);

	$stmt->execute();

	$stmt->close();
	
	//dialogue:	
	$dialogues = array();
	
	$stmt = $conn->prepare("SELECT fragment_name, fragment_state FROM dialogues WHERE character_id = ? ");
	
	$stmt->bind_param("i", $charid);

	$stmt->execute();

	$stmt->bind_result($row_fragment_name, $row_fragment_state);
	
	while($stmt->fetch()) {

		$dialogues[] = array('fragment_name'=>$row_fragment_name, 'fragment_state'=> $row_fragment_state);

	}

	//quests:
	$quests = array();

	$stmt = $conn->prepare("SELECT quest_name, quest_state FROM quests WHERE character_id = ? ");

	$stmt->bind_param("i", $charid);

	$stmt->execute();

	$stmt->bind_result($row_quest_name, $row_quest_state);

	while($stmt->fetch()) {

		$quests[] = array('quest_name'=>$row_quest_name, 'quest_state'=> $row_quest_state);

	}
	
	//quest_nodes:
	$quest_nodes = array();

	$stmt = $conn->prepare("SELECT quest_node_name, quest_node_state FROM quest_nodes WHERE character_id = ? ");

	$stmt->bind_param("i", $charid);

	$stmt->execute();

	$stmt->bind_result($row_quest_node_name, $row_quest_node_state);

	while($stmt->fetch()) {

		$quest_nodes[] = array('quest_node_name'=>$row_quest_node_name, 'quest_node_state'=> $row_quest_node_state);

	}	
	
	if($in_instance == 0)  {
		
		echo  json_encode(array('status'=>'OK', 'name'=> $row_name, 'dialogues'=>$dialogues, 'quests'=>$quests, 'quest_nodes'=>$quest_nodes, 'health'=> $row_health, 
		'energy'=> $row_energy, 'level'=> $row_level, 'experience'=> $row_experience, 'posx'=> $row_posx, 'posy'=> $row_posy, 'posz'=>$row_posz, 'yaw'=> $row_yaw,
		'current_port'=> $current_port, 'in_instance'=> $in_instance, 'has_entered'=> $has_entered ));
		
	}
	else {
		
		echo  json_encode(array('status'=>'OK', 'name'=> $row_name, 'dialogues'=>$dialogues, 'quests'=>$quests, 'quest_nodes'=>$quest_nodes, 'health'=> $row_health, 
		'energy'=> $row_energy, 'level'=> $row_level, 'experience'=> $row_experience, 'posx'=> $row_posx, 'posy'=> $row_posy, 'posz'=>$row_posz, 'yaw'=> $row_yaw,
		'current_port'=> $current_port, 'in_instance'=> $in_instance, 'has_entered'=> $has_entered, 'previous_port'=> $previous_port,
		'pp_posx'=> $pp_posx, 'pp_posy'=> $pp_posy, 'pp_posz'=> $pp_posz, 'pp_yaw'=> $pp_yaw ));
		
	}
}

else echo  json_encode(array('status'=>'Character id '.$charid.' not found '));

?>