<?php
session_start();	
require_once('./config/database.php'); 
require_once('./config/config.php');
if(!isset($_SESSION['access_token']) || $_SESSION['access_token'] == "")
{
	header("Location: ./index.php");exit;
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Settings</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">
	<link rel="stylesheet" href="./assets/css/style.css">	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>	
	<script src="./assets/js/setting.js"></script>
</head>

<body>
	<nav class="navbar navbar-default">
	  <div class="container-fluid">
	    <div class="navbar-header">
	      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
	        <span class="sr-only">Toggle navigation</span>
	        <span class="icon-bar"></span>
	        <span class="icon-bar"></span>
	        <span class="icon-bar"></span>
	      </button>
	      <a class="navbar-brand" href="./main.php">SaleForce Portal</a>
	    </div>

	    <!-- Collect the nav links, forms, and other content for toggling -->
	    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
	      <div class="navbar-form navbar-right">
	      	<a href = "./main.php" class="btn btn-primary logout">Main Page <i class="glyphicon glyphicon-home"></i> </a>	
	        <a class="btn btn-primary logout" id="logout" href="./app/api.php?logout=">LogOut <i class="glyphicon glyphicon-log-in"></i></a>	
	      </div>
	    </div>
	  </div>
	</nav>
	<div class="container">
		<div class="col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-6 col-offset-3">
			<div id = "alert">
			</div>
			<form class="form" id = "form" method="POST" action="./main.php">
				<input type="hidden" name="ownerId" id="ownerId" value="<?php if(isset($_SESSION['ownerId'])) echo $_SESSION['ownerId'] ;?>">
				<div class="form-group">
					<label class="warning">Campaign Name</label>
					<input type="text" name="campaign" id="campaign" class="form-control input" required>
				</div>
				<div class="form-group">
					<label>Sub Campaign Name</label>
					<input type="text" name="subcampaign" id="subcompaign" class="form-control input">
				</div>
				<div class="form-group">
					<label class="warning">SecurityCode</label>
					<input type="text" name="securityCode" id="securityCode" class="form-control input" required>
				</div>
				<div class="form-group">
					<label class="warning">GroupId</label>
					<input type="text" name="groupId" id="gruopId" class="form-control input" required>
				</div>	
				<div class="form-group">
					<label class="warning">Refresh Rate (min)</label>
					<input type="number" name="refreshRate" id="refreshRate" class="form-control input" value="5" required>
				</div>
				<div class="form-group">
					<input type="submit" name="submit" class="form-control" value="Submit">
					<div class="loader hidden"></div>
				</div>
			</form>
		</div>
	</div>


</body>
</html>