<?php
	
$servername = 'localhost'; 
$username = 'root';  
$password = 'w0NlPspk1ddKvsSIvoJa';
$dbname = 'cot';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo(json_encode(array('status'=>"Connection failed: " . $conn->connect_error)));
	die;
} 



?>
