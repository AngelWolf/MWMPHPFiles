<?php
session_start();
include_once "mmoconnection.php";

$mydata = json_decode(file_get_contents('php://input'));

$server_address = $mydata->server_address;

$stmt = $conn->prepare("SELECT value FROM server_globals WHERE variable = ? ");
		
$stmt->bind_param("s", 'total_released');
		
$stmt->execute();
		
$stmt->bind_result( $total_released );

$stmt->fetch();

$stmt = $conn->prepare("SELECT value FROM server_globals WHERE variable = ? ");
		
$stmt->bind_param("s", 'total_instances');
		
$stmt->execute();
		
$stmt->bind_result( $total_instances );

$stmt->fetch();

if($total_released > 0) {
	
	$stmt = $conn->prepare("SELECT port FROM released_instances WHERE server_address = ? ");
		
	$stmt->bind_param("s", $server_address);
		
	$stmt->execute();
		
	$stmt->bind_result( $reused_port );

	if($stmt->fetch()) {

		$stmt = $conn->prepare("DELETE FROM released_instances WHERE server_address = ? AND port = ?");
		
		$stmt->bind_param("si", $server_address, $reused_port);
		
		$stmt->execute();
		
		$total_released--;
		
		$stmt = $conn->prepare("UPDATE server_globals SET value = ? WHERE variable = ? ");
		
		$stmt->bind_param("is", $total_released, 'total_released');
		
		$stmt->execute();
		
		$stmt = $conn->prepare("INSERT INTO instances (server_address, port) VALUES (?, ?");
		
		$stmt->bind_param("si", $server_address, $reused_port);
		
		$stmt->execute();
		
		$total_instances++;
		
		$stmt = $conn->prepare("UPDATE server_globals SET value = ? WHERE variable = ? ");
		
		$stmt->bind_param("is", $total_instances, 'total_instances');
		
		$stmt->execute();
			
		echo json_encode(array('status'=>'OK', 'port'=>$reused_port ));
	}
}

else {
	
	$total_instances++;
	
	$port = $total_instances + 30000;
	
	$stmt = $conn->prepare("INSERT INTO instances (server_address, port) VALUES (?, ?");
		
	$stmt->bind_param("si", $server_address, $port);
		
	$stmt->execute();
		
	$stmt = $conn->prepare("UPDATE server_globals SET value = ? WHERE variable = ? ");
		
	$stmt->bind_param("is", $total_instances, 'total_instances');
		
	$stmt->execute();
	
	echo json_encode(array('status'=>'OK', 'port'=>$port ));	
}

?>