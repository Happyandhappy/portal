<?php
	/*
		$host     : host name
		$username : username
		$password : password
		$dbname   : database name
	*/
	function getConnection(){
		$host = "localhost";
		$username = "root";
		$password = "";
		$dbname = "amacklai_salesforce";

		$con = new mysqli($host, $username, $password,$dbname);
		// Check connection
		if ($con->connect_error) {
		    die("Connection failed: " . $con->connect_error);
		} 

		return $con;
	}

	function getOption($view){
		$con = getConnection();
		$query = "SELECT opt from settings where views='" . $view."'";

		$result = $con->query($query);
		if ($result->num_rows > 0){
			while ($row = $result->fetch_assoc()) {
				return $row['opt'];
			}
		}
		return 0;
	}
	
?>
