<?php

include_once "mmoconnection.php"; 

$mydata = json_decode(file_get_contents('php://input'));

$userid = $mydata ->userid;
$key = $mydata ->sessionkey;

$stmt = $conn->prepare("SELECT session_key FROM active_logins WHERE user_id = ? ");

$stmt->bind_param("i", $userid);  // "s" means the database expects a string

$stmt->execute();

$stmt->bind_result($row_sessionkey);

if (!$stmt->fetch()) echo  json_encode(array('status'=>'You are not logged in.'));

else {
 
	if ($key == $row_sessionkey)  { //  if the user owns the current active session
		
		$chararray = array();		

		$stmt->close();
		
		$stmt = $conn->prepare("SELECT id, name, level, current_port FROM characters WHERE user_id = ? ");
		
		$stmt->bind_param("i", $userid);
		
		$stmt->execute();
				
		$stmt->bind_result($row_id, $row_name, $row_level, $row_current_port );
					
		while ($stmt->fetch()) 
		{
			//add to array of characters:	
			$chararray[] = array('id' => $row_id, 'name'=>$row_name, 'level'=>$row_level, 'current_port'=>$row_current_port);
		}

		echo json_encode(array('status'=>'OK', 'characters'=>$chararray));

	} 

}


?>
