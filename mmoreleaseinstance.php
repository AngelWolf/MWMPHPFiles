<?php
include_once "mmoconnection.php";

// Incoming data:  Instance_id of dedicated executable that is being shut down.
$mydata = json_decode(file_get_contents('php://input'));
$instance_id = $mydata->$instance_id;

// Flip recycle flag to 1 for that instance, signaling that this port number is now free to be recycled the next time the same server calls mmogetinstance.php again.
// The else part of the if/else block in that script will then run, assigning the old port to a new instance_id.
$stmt = $conn->prepare("UPDATE instances SET recycle = 1 WHERE instance_id = ?");
$stmt->bind_param("i", $instance_id);
$stmt->execute();
$stmt->close();

// Send OK status back to signal script finished.
echo json_encode(array('status'=>'OK' ));
			
?>