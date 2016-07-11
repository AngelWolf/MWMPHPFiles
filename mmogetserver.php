<?php
start_session();
$mydata = json_decode(file_get_contents('php://input'));

	echo json_encode(array('status'=>'OK', 'address'=>'73.42.246.49', 'server_id'=>htmlspecialchars(SID)));


?>
