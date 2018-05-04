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

	function getRefreshRate(){
		$con = getConnection();
		$query = "SELECT refreshRate from settings where username='" . $_SESSION['username']."'";

		$result = $con->query($query);
		if ($result->num_rows > 0){
			while ($row = $result->fetch_assoc()) {
				return (int)$row['refreshRate'];
			}
		}
		return 5;
	}

?>
