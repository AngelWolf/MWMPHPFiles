<?php
include_once "mmoconnection.php";
$mydata = json_decode(file_get_contents('php://input'));
$server_address = $mydata->server_address;

$stmt = $conn->prepare("SELECT MIN(port) FROM instances WHERE server_address = ? AND recycle = 1");
$stmt->bind_param("s", $server_address);
$stmt->execute();
$stmt->bind_result( $port_to_use );
if ($port_to_use == 0) {
	$stmt = $conn->prepare("SELECT COUNT(*) FROM instances WHERE server_address = ? ");
	$stmt->bind_param("s", $server_address);
	$stmt->execute();
	$stmt->bind_result( $port );
	$port = $port + 50000;
	$stmt = $conn->prepare("INSERT INTO instances (instance_id, server_address, port, recycle) VALUES (NULL, ?, ?, 0) ");
} else {
	
	$stmt = $conn->prepare("SELECT instance_id FROM instances WHERE server_address = ? AND port = ? ");
	$stmt->bind_param("si", $server_address, $port_to_use);
	$stmt->execute();
	$stmt->bind_result( $instance_id );
	
	$stmt = $conn->prepare("DELETE FROM instances WHERE instance_id = ? ");
	$stmt->bind_param("si", $server_address, $port_to_use);
	$stmt->execute();
	
	$stmt = $conn->prepare("INSERT INTO instances (instance_id, server_address, port, recycle) VALUES (NULL, ?, ?, 0) ");
		
	$port = $port_to_use;
	$stmt = $conn->prepare("UPDATE instances recycle = 0 WHERE port = ? ");
}
$stmt->bind_param("si", $server_address, $port);
$stmt->execute();
$stmt = $conn->prepare("SELECT instance_id FROM instances WHERE server_address = ? AND port = ?");
$stmt->bind_param("s", $server_address);
$stmt->execute();
$stmt->bind_result( $instance_id );
echo json_encode(array('status'=>'OK', 'port'=>$port, 'instance_id'=>$instance_id ));
$stmt->close();

?>