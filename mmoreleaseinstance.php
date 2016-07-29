<?php
include_once "mmoconnection.php";
$mydata = json_decode(file_get_contents('php://input'));
$instance_id = $mydata->$instance_id;
$port = $mydata->$port;
$server_address = $mydata->$server_address
$stmt = $conn->prepare("DELETE FROM instances WHERE instance_id = ? ");
$stmt->bind_param("i", $instance_id);
$stmt->execute();
$stmt = $conn->prepare("INSERT INTO instances (instance_id, server_address, port, recycle) VALUES (NULL, ?, ?, 0) ");
$stmt->bind_param("si", $server_address, $port);
$stmt->execute();
echo json_encode(array('status'=>'OK' ));
$stmt->close();		
?>