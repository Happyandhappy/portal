<?php
	/*
		$host     : host name
		$username : username
		$password : password
		$dbname   : database name
	*/
	$host = "localhost";
	$username = "root";
	$password = "";
	$dbname = "saleforce_portal";

	$con = new mysqli($host, $username, $password);

	// Check connection
	if ($con->connect_error) {
	    die("Connection failed: " . $con->connect_error);
	} 
?>
