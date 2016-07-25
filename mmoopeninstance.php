<?php
session_start();
include_once "InformationCollector.php";

// Found on http://php.net/manual/en/function.exec.php  Allows executables to be launched in the background without stalling the PHP script.
function execInBackground($cmd) { 
	if (substr(php_uname(), 0, 7) == "Windows"){ 
		pclose(popen("start /B ". $cmd, "r"));  
	} 
	else { 
		exec($cmd . " > /dev/null &");   
	} 
}

// Output from Blueprint will be just the name of the level to open.
$mydata = json_decode(file_get_contents('php://input'));

$level_name = $mydata->level_name;
$port = $mydata->port;
$server_address = $mydata->server_address;

//Create InformationCollector so we can check processor_load
$InfoCollector = new InformationCollector();

$Information = $InfoCollector->collect();

//If the CPU is running at less than 80%.
if ($Information['processor_load'] < 80)
{
	//Launch "CityOfTitansServer $level_name -$port"
	//AngelWolf: Server must call a packaged copy of the game.  Change this to point to your local install from the launcher.
	
	execInBackground("E:\MMOBuild\WindowsNoEditor\CityOfTitans\Binaries\Win64\CityOfTitansServer $level_name -log -port=$port");
	
	//Send port number back to the blueprint, where it will be passed to the player(s) entering the instance.
	echo json_encode(array('status' => 'OK', 'port' => $port, 'server_address' => $server_address));
	
}
//More to be added here later to handle switching to next available server when CPU is over 80%.  
else 
{	
	echo json_encode(array('status'=> '0'));
}

?>


