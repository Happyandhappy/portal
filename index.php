<?php 
session_start();
require_once('./config/database.php'); 
require_once('./config/config.php');
$_SESSION['error'] = "";
if (!isset($_SESSION['access_token']) || $_SESSION['access_token'] == ""){

	if (isset($_POST['username'])){
		$_SESSION['username'] = $_POST['username'];
		$_SESSION['password'] = $_POST['password'];
		$_SESSION['secret_token'] = $_POST['secret_token'];

	    define("USERNAME", $_SESSION['username']);
	    define("PASSWORD", $_SESSION['password']);
	    define("SECURITY_TOKEN",$_SESSION['secret_token']);

	    getAccessID();
		// getCode();
	}
	else if (isset($_GET['code'])){
		$_SESSION['code'] = $_GET['code'];
		getAccess_Token();	
	}
}else{
	header('Location: ' . "./main.php");
	exit;
}

function getAccessID(){
	$url = "https://login.salesforce.com/services/oauth2/token";
	$params = "grant_type=password&client_id=" . CLIENT_ID . "&client_secret=" . CLIENT_SECRET . "&username=" . USERNAME . "&password=" . PASSWORD.SECURITY_TOKEN;
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	$response = curl_exec($ch);
	curl_close($ch);
	$response = json_decode($response);

	if (isset($response->error)){
		$_SESSION['error'] = "Invalid User Information!";
	}else{
		$_SESSION['error'] = "";
		$_SESSION['access_token'] = $response->access_token;
		$_SESSION['access_token'] = $response->access_token;
		$_SESSION['instance_url'] = $response->instance_url;
		$_SESSION['signature'] = $response->signature;	

		saveDatetoDB();
	}
}

function saveDatetoDB(){
	$con = getConnection();

	$query = "select * from users where username='". $_SESSION['username']."'";

	$result = $con->query($query);
	if ($result->num_rows==0){
		$query = "INSERT INTO users (username, password, secret_token) VALUES ('". $_SESSION['username']."','". $_SESSION['password'] . "','" . $_SESSION['secret_token']. "')";
		$result = $con->query($query);

		$query = "INSERT INTO settings (username,refreshRate) VALUES ('".$_SESSION['username']."', 5)";
		$res = $con->query($query);
	}

	header('Location: ./main.php');	
	exit;
}

function getCode(){
	$auth_url =LOGIN_URI."/services/oauth2/authorize?response_type=code&client_id=".CLIENT_ID.
	"&client_secret=".CLIENT_SECRET."&redirect_uri=" . urlencode(REDIRECT_URI);
	header('Location: ' . $auth_url);
	exit;
}	

function getAccess_Token(){
	$token_url = LOGIN_URI . "/services/oauth2/token";
	$params =  "grant_type=authorization_code"
			  ."&client_id=" . CLIENT_ID
			  ."&client_secret=" . CLIENT_SECRET
			  ."&username="   //. USERNAME
			  ."&password="   //. PASSWORD
			  ."&code=".$_SESSION['code']
			  ."&redirect_uri=".REDIRECT_URI;
	$ch = curl_init($token_url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	$response = curl_exec($ch);
	curl_close($ch);

	$result = json_decode($response);

	$_SESSION['access_token'] = $result->access_token;
	$_SESSION['instance_url'] = $result->instance_url;
	$_SESSION['id_token'] = $result->id_token;
	$_SESSION['signature'] = $result->signature;

	header('Location: ./main.php');
	exit;
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Saleforce Portal</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">
	<link rel="stylesheet" href="./assets/css/style.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>
	<div class="container">
		<div id = "loginbox" class="mainbox col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3">

			<div class="row icon">
				<div class="iconmelon">
					<svg viewBox = "0 0 32 32">
						<g filter="">
							<use xlink:href="#git"></use>
						</g>
					</svg>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					<div class="panel-title text-center">Web Portal</div>
				</div>

				<div class="panel-body">
					<form name="form" id="form" class="form-horizontal" method="POST">
						<div id = "alert">
							<?php if ($_SESSION['error'] != ""):?>
							<div class="alert alert-dismissible fade in">
								<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
								<strong>Warning!</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Invalid User Information!	
							</div>
							<?php endif; ?>
						</div>
						<div class="input-group">
							<span class="input-group-addon"> <i class="glyphicon glyphicon-user"></i></span>
							<input type="text" name="username" id="user" class="form-control" placeholder="Username" required>
						</div>
						<div class="input-group">
							<span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
							<input class="form-control" type="password" name="password" id="password" placeholder="Password" required>
						</div>
						<div class="input-group">
							<span class="input-group-addon"><i class="glyphicon glyphicon-link"></i></span> 
							<input type="text" name="secret_token" id="secret_token" class="form-control" placeholder="Secret Token" required>
						</div>
	                    <div class="form-group">
	                        <div class="col-sm-12 controls">
	                            <button type="submit" href="#" class="btn btn-primary pull-right"><i class="glyphicon glyphicon-log-in"></i> Log in</button>                          
	                        </div>
	                    </div>
				</div>	
			</form>
		</div>
	</div>
</body>
</html>

