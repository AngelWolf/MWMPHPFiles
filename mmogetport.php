<?php
session_start();
include_once "mmoconnection.php";

$mydata = json_decode(file_get_contents('php://input'));

$server_address = $mydata->server_address;

$stmt = $conn->prepare("SELECT server_id, port FROM instances WHERE server_address = ? ");
		
$stmt->bind_param("s", $server_address);
		
$stmt->execute();
		
$stmt->bind_result($server_id, $port );

while ($stmt->fetch()) {
		//add to array of ports:	
		$portarray[] = array('server_address' => $server_address, 'server_id' => $server_id, 'port' => $port);
}

for ($portcount = 8001; ; $portcount++) {

	$port_found = binsearch($portcount, $portarray);

	if($port_found == false) {
		break;
	}
	else {
	
		if(portarray[$port_found].server_id == NULL){
			break;
		}
	}
}

echo json_encode(array('status'=>'OK', 'port'=>$portcount ));




function binsearch($needle, $haystack)
{
    $high = count($haystack);
    $low = 0;
    
    while ($high - $low > 1){
        $probe = ($high + $low) / 2;
        if ($haystack[$probe] < $needle){
            $low = $probe;
        }else{
            $high = $probe;
        }
    }

    if ($high == count($haystack) || $haystack[$high] != $needle) {
        return false;
    }else {
        return $high;
    }
}
?>