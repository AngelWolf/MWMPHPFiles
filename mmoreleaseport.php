<?php
session_start();
include_once "mmoconnection.php";

$mydata = json_decode(file_get_contents('php://input'));

$port = $mydata->port;
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

$stmt = $conn->prepare("INSERT INTO released_instances (server_address, port) VALUES (?, ?)");
		
$stmt->bind_param("si", $server_address, $port);
		
$stmt->execute();
		
$total_released++;
		
$stmt = $conn->prepare("UPDATE server_globals SET value = ? WHERE variable = ? ");
		
$stmt->bind_param("is", $total_released, 'total_released');
		
$stmt->execute();
		
$stmt = $conn->prepare("DELETE FROM instances WHERE server_address = ? AND port = ?");
		
$stmt->bind_param("si", $server_address, $port);
		
$stmt->execute();
		
$total_instances--;
		
$stmt = $conn->prepare("UPDATE server_globals SET value = ? WHERE variable = ? ");
		
$stmt->bind_param("is", $total_instances, 'total_instances');
		
$stmt->execute();
			
?>