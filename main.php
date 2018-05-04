<?php
session_start();	
define('__ROOT__', dirname(dirname(__FILE__))); 
require_once(__ROOT__.'/portal/config/database.php'); 
require_once(__ROOT__.'/portal/config/config.php');
if (!isset($_SESSION['refreshRate']))  $_SESSION['refreshRate'] = 5;

if(!isset($_SESSION['access_token']) || $_SESSION['access_token'] == "")
{
	header("Location: " . ORIGIN_URI);
	exit;
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
	                    <option value="" selected="" hidden="">- None -</option>
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