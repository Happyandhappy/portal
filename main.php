<?php
session_start();	 
require_once('./config/database.php'); 
require_once('./config/config.php');

// if (!isset($_SESSION['refreshRate']))  $_SESSION['refreshRate'] = 5;
$_SESSION['refreshRate'] = getRefreshRate();

if(!isset($_SESSION['access_token']) || $_SESSION['access_token'] == "")
{
	header("Location: ./index.php");
	exit;
}

if ( count($_POST) > 0 ) {
	$campaign 		= $_POST['campaign'];
	$subcampaign 	= $_POST['subcampaign'];
	$securityCode 	= $_POST['securityCode'];
	$groupId		= $_POST['groupId'];
	$refreshRate	= $_POST['refreshRate'];
	
	if (isset($_POST['firstName']))  $firstName	= $_POST['firstName'];
	else $firstName	= null;

	if (isset($_POST['lastName']))   $lastName = $_POST['lastName'];
	else $lastName	= null;

	if (isset($_POST['address']))    $address = $_POST['address'];
	else $address	= null;

	if (isset($_POST['city']))       $city = $_POST['city'];
	else $city		= null;

	if (isset($_POST['state']))      $state	= $_POST['state'];
	else $state		= null;

	if (isset($_POST['zipcode']))    $zipcode = $_POST['zipcode'];
	else $zipcode	= null;

	if (isset($_POST['notes']))      $notes	= $_POST['notes'];
	else $notes		= null;
	
	if (isset($_POST['mobile']))     $mobile = $_POST['mobile'];
	else $mobile	= null;

	if (isset($_POST['phone']))      $phone	= $_POST['phone'];
	else $phone		= null;

	$con = getConnection();
	$query = "UPDATE settings SET campaign = '".$campaign."', subcampaign='". $subcampaign . "' , securityCode='" . $securityCode. "', groupId='" . $groupId . "', refreshRate =" . $refreshRate . ", firstName = '" . $firstName . "' , lastName = '" . $lastName . "' , address = '" .  $address . "' , city = '" . $city . "' , state = '" . $state . "' , zipcode = '" . $zipcode . "' , notes = '" . $notes . "', mobile = '" . $mobile . "', phone ='" . $phone . "' where username = '" . $_SESSION['username']."'";
	$res = $con->query($query);

// update or inject curl requests here///////////////////////////////////////////////////////////////
		
}

?>

<!DOCTYPE html>
<html>

<title>Saleforce Portal</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>	
	<link rel="stylesheet" href="./assets/css/style.css">	
	<script src="./assets/js/main.js"></script>
<head>
	<title>Saleforce Portal</title>
</head>
<body>
	<input type="hidden" id = "refreshRate" name="refreshRate" value="<?php echo($_SESSION['refreshRate']);?>">
	<nav class="navbar navbar-default">
	  <div class="container-fluid">
	    <div class="navbar-header">
	      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
	        <span class="sr-only">Toggle navigation</span>
	        <span class="icon-bar"></span>
	        <span class="icon-bar"></span>
	        <span class="icon-bar"></span>
	      </button>
	      <a class="navbar-brand" href="#">SaleForce Portal</a>
	    </div>

	    <!-- Collect the nav links, forms, and other content for toggling -->
	    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
	      <div class="navbar-form navbar-right">
	      	<a href = "./setting.php" class="btn btn-primary logout" id="setting">Setting <i class="glyphicon glyphicon-cog"></i></a>	
	        <a class="btn btn-primary logout" id="logout" href="./app/api.php?logout=">LogOut <i class="glyphicon glyphicon-log-in"></i></a>	
	      </div>
	    </div>
	  </div>
	</nav>

	<div class="container">
		<div class="col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-6 col-offset-3">
			
			<div class="row icon">
					<div class="iconmelon">
						<img src="./assets/logo.png">
					</div>
				</div>
			<form method="POST" class="form">
				<div class="form-group">
					<label>Select Views</label>
	                <select class="selectpicker form-control select" id="views" name="view" required="">
	                </select>
				</div>
			</form>
		</div>	
		<div  class="col-md-12 col-sm-6 col-6 col-offset-3">	
			<table class="table table-striped" id="table">				
			</table>
		</div>
		<div class="loader hidden"></div>
	</div>
</body>
</html>