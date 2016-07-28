<?php
include_once "mmoconnection.php";

// Incoming data:  Server_addres of game server that is requesting the launch of a new instance.
$mydata = json_decode(file_get_contents('php://input'));
$server_address = $mydata->server_address;

// Searches for the lowest port number on that server which has a recycle flag set.
$stmt = $conn->prepare("SELECT MIN(port) FROM instances WHERE server_address = ? AND recycle = 1");
$stmt->bind_param("s", $server_address);
$stmt->execute();
$stmt->bind_result( $port_to_use );

// If no port number comes back, none of the ports already taken have been released yet, so we make a new entry.  
if ($port_to_use == 0) {
		
	// Count the number of ports that already exist.
	$stmt = $conn->prepare("SELECT COUNT(*) FROM instances WHERE server_address = ? ");
	$stmt->bind_param("s", $server_address);
	$stmt->execute();
	$stmt->bind_result( $port );
	
	// Add 50000 to the count and that's our port number.
	$port = $port + 50000;
	
	// Register new instance on table with new port number.
	$stmt = $conn->prepare("INSERT INTO instances (instance_id, server_address, port, recycle) VALUES (NULL, ?, ?, 0) ");
	
} else {  // Else, we found a port number with the recycle flag set.  Delete the old entry with that port number and make a new entry.	
	
	// Get the old instance id that goes with the port.
	$stmt = $conn->prepare("SELECT instance_id FROM instances WHERE server_address = ? AND port = ? ");
	$stmt->bind_param("si", $server_address, $port_to_use);
	$stmt->execute();
	$stmt->bind_result( $instance_id );
	
	// Delete old row from table.
	$stmt = $conn->prepare("DELETE FROM instances WHERE instance_id = ? ");
	$stmt->bind_param("i", $instance_id);
	$stmt->execute();
	
	// Set port variable to the old port number 
	$port = $port_to_use;
	
	// Register new instance on table with port number and new instance_id.
	$stmt = $conn->prepare("INSERT INTO instances (instance_id, server_address, port, recycle) VALUES (NULL, ?, ?, 0) ");	
}

// Binds to either prepare case.
$stmt->bind_param("si", $server_address, $port);
$stmt->execute();

// Get newly created instance_id from the table.
$stmt = $conn->prepare("SELECT instance_id FROM instances WHERE server_address = ? AND port = ?");
$stmt->bind_param("si", $server_address, $port);
$stmt->execute();
$stmt->bind_result( $instance_id );
$stmt->close();

// Send port and instance_id back to server, which uses data to call mmoopeninstance.php on a game server.
echo json_encode(array('status'=>'OK', 'port'=>$port, 'instance_id'=>$instance_id ));

?>