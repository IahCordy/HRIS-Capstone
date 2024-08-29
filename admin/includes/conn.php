<?php
	$conn = new mysqli('localhost', 'root', '', 'origin_feur_hrsys');

	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	}
	
?>