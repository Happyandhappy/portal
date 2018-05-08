<?php
session_start();	 
require_once('./config/database.php'); 
require_once('./config/config.php');
require_once('./app/Saleforce.php');


if(!isset($_SESSION['access_token']) || $_SESSION['access_token'] == "")
{
	header("Location: ./index.php");
	exit;
}


$sale = new Saleforce($_SESSION['access_token'], $_SESSION['instance_url']);

$option = 0;
$view = "";
$option = getOption($view);

$data_result = array();
if ( count($_POST) > 0 ) {
	$campaign 		= $_POST['campaign'];
	$subcampaign 	= $_POST['subcampaign'];
	$securityCode 	= $_POST['securityCode'];
	$groupId		= $_POST['groupId'];
	$refreshRate	= $_POST['refreshRate'];
	$name 			= $_POST['name'];
	// $title			= $_POST['title'];
	$company 		= $_POST['company'];
	// $phone 			= $_POST['phone'];
	// $mobile 		= $_POST['mobile'];
	$email 			= $_POST['email'];
	$lead_status    = $_POST['lead_status'];

	// get view data
	$view = $_POST['view'];
	$option = getOption($view);

	// save or update data to db 
	$con = getConnection();

	$query = "select * from settings where view='" . $_POST['view'] . "'";
	$res = $con->query($query);
	if ($res->num_rows>0){
		$query = "UPDATE settings SET campaign = '".$campaign."', subcampaign='". $subcampaign . "' , securityCode='" . $securityCode. "', groupId='" . $groupId . "', refreshRate =" . $refreshRate . ", option =" . $option . " where view = '" . $_POST['view']."'";			
	}
	else{
		$query = "INSERT settings (username, campaign, subcampaign, securityCode, groupId, refreshRate, option,  view) VALUES ('".$_SESSION['username']. "','" . $campaign . "','" . $subcampaign . "','" . $securityCode . "','" . $groupId . "','" . $refreshRate. "'," . $option . ",'" . $view  . "')";
	}
	$res = $con->query($query);

	$query = "select * from mapping where view='" . $_POST['view'] . "'";
	$res = $con->query($query);
	if($res->num_rows>0){
		$query = "UPDATE mapping SET name='" . $name . "', company ='" . $company . "', email = '" . $email . "', lead_status = '" . $lead_status . "' where view='" . $_POST['view'] . "'";
	}else{
		$query = "INSERT mapping (view, name,  company,  email , lead_status) VALUES ('" .$view . "','" . $name . "','" . $company . "','" . $email . "','" . $lead_status ."')";
	}
	$res = $con->query($query);
}

// select views data
$url ="/services/data/v42.0/sobjects/Lead/listviews";
$select_data = $sale->getListViewDetail($url);
$index = 0;
?>

<!DOCTYPE html>
<html>
<head>
	<title>Saleforce Portal</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>	
	<link rel="stylesheet" href="./assets/css/style.css">	
	<script src="./assets/js/main.js"></script>
</head>
<body>
	<?php	if ( count($_POST) > 0 ) {?>
		<input type="hidden" id = "refreshRate" name="refreshRate" value="<?php echo $refreshRate;?>">
		<input type="hidden" id = "campaign" name="campaign" value="<?php echo $campaign;?>">
		<input type="hidden" id = "subcampaign" name="subcampaign" value="<?php echo $subcampaign ;?>">
		<input type="hidden" id = "securityCode" name="securityCode" value="<?php echo $securityCode ;?>">
		<input type="hidden" id = "groupId" name="groupId" value="<?php echo $groupId ;?>">
		<input type="hidden" id = "name" name="name" value="<?php echo $name; ?>">
		<input type="hidden" id = "company" name="company" value="<?php echo $company; ?>">
		<input type="hidden" id = "email" name="email" value="<?php echo $email; ?>">
		<input type="hidden" id = "lead_status" name="lead_status" value="<?php echo $lead_status; ?>">
	<?php } ?>
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
	      	<!-- <a href = "./setting.php" class="btn btn-primary logout" id="setting">Setting <i class="glyphicon glyphicon-cog"></i></a>	 -->
	        <a class="btn btn-primary logout" id="logout" href="./app/api.php?logout=">LogOut <i class="glyphicon glyphicon-log-in"></i></a>	
	      </div>
	    </div>
	  </div>
	</nav>

	<div class="container">
		<div class="panel_body">
			<div class="row">
				<div class="col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-6 col-offset-3">
					<div class="row icon">
						<div class="_logo">
							<img src="./assets/logo1.png" style="height: 85px;"> <span style="font-size: 30px; top:50%;">&nbsp;&nbsp;+&nbsp;&nbsp;</span>
							<img src="./assets/logo.png" style="height: 85px;">
						</div>
					</div>
					
					<form method="POST" class="form" action="./setting.php">
						<div class="col-md-8 col-sm-8 col-8">
							<div class="form-group">
								<label>Select Views</label>
				                <select class="selectpicker form-control select" id="views" name="view" required="">
				                	<option hidden required>- None -</option>
				                	<?php
				                		foreach ($select_data->listviews as $row) {
				                			if ($view == $row->resultsUrl)
				                				echo "<option value='".$row->resultsUrl."' selected>".$row->label."</option>";
				                			else 
				                				echo "<option value='".$row->resultsUrl."'>".$row->label."</option>";
				                		}
				                	?>
				                </select>
							</div>
						</div>
						<div class="col-md-4 col-sm-4 col-4">
							<div class="form-group">
								<label>Stop / Start to sending Leads</label>
								 <select class="form-control select" id="option">
								 	<?php 
								 		if ($option == 1)
						                	echo "<option value='1' selected>Yes</option>
						                	<option value='0'>No</option>";
						                else
						                	echo "<option value='1'>Yes</option>
						                	<option value='0'  selected>No</option>";
				                	?>
				                </select>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div  class="col-md-12 col-sm-6 col-6 col-offset-3 second">	
			<table class="table table-striped" id="table">			
			</table>
		</div>
		<div class="loader hidden"></div>
	</div>

</body>
</html>