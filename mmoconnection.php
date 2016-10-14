<?php
	
$servername = 'localhost'; 
$username = 'root';  
$password = 'Z4cmcU9zi8uQCsgkqdMm';
$dbname = 'cot';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo(json_encode(array('status'=>"Connection failed: " . $conn->connect_error)));
	die;
} 



?>
