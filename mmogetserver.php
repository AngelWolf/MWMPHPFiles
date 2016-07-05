<?php

$mydata = json_decode(file_get_contents('php://input'));

	echo json_encode(array('status'=>'OK', 'address'=>'73.42.246.49'));


?>
