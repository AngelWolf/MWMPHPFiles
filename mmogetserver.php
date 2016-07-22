<?php
session_start();
$mydata = json_decode(file_get_contents('php://input'));

$server_id = str_replace('PHPSESSID=', '', SID);

	echo json_encode(array('status'=>'OK', 'address'=>'67.183.91.154', 'server_id'=>$server_id));


?>
