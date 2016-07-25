<?php
include_once "mmoconnection.php";
$mydata = json_decode(file_get_contents('php://input'));
$instance_id = $mydata->$instance_id;
$stmt = $conn->prepare("UPDATE instances SET recycle = 1 WHERE instance_id = ?");
$stmt->bind_param("i", $instance_id);
$stmt->execute();
echo json_encode(array('status'=>'OK' ));
$stmt->close();
			
?>