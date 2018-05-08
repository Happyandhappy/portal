<?php
session_start();	
require_once('./config/database.php'); 
require_once('./config/config.php');
if(!isset($_SESSION['access_token']) || $_SESSION['access_token'] == "")
{
	header("Location: ./index.php");exit;
}


if(!isset($_POST['view'])){
	header("Location: ./main.php");exit;	
}

$con = getConnection();
$query = "select * from mapping where view='" . $_POST['view']."'";
$result = $con->query($query);

if($result->num_rows>0){
	while ($row = $result->fetch_assoc()) {

		$data = array(
					"name" 			=> $row['name'],
					// "title" 		=> $row['title'],
					"company" 		=> $row['company'],
					"phone"			=> $row['phone'],
					"mobile"		=> $row['mobile'],
					"email"			=> $row['email'],
					"lead_status"	=> $row['lead_status'],
				);
	}
}else{
	$data = array(
					"name" 			=> "",
					// "title" 		=> "",
					"company" 		=> "",
					"phone"			=> "",
					"mobile"		=> "",
					"email"			=> "",
					"lead_status"	=> 1,
				);
}


?>

<!DOCTYPE html>
<html>
<head>
	<title>Mapping</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">
	<link rel="stylesheet" href="./assets/css/style.css">	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>	
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
	      	<!-- <a href = "./main.php" class="btn btn-primary logout">Main Page <i class="glyphicon glyphicon-home"></i> </a>	 -->
	        <a class="btn btn-primary logout" id="logout" href="./app/api.php?logout=">LogOut <i class="glyphicon glyphicon-log-in"></i></a>	
	      </div>
	    </div>
	  </div>
	</nav>
	<!-- line modal -->
	<div class="modal fade in" id="squarespaceModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" style="display:block;">
	  <div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title" id="lineModalLabel"><i class="glyphicon glyphicon-send"></i>  Mapping Saleforce Fields</h3>
				</div>
				<form class="form" id = "form" method="POST" action="./main.php">
					<input type="hidden" name="view" 	 		value="<?= $_POST['view']?>">
					<input type="hidden" name="campaign" 		value="<?= $_POST['campaign']?>">
					<input type="hidden" name="subcampaign" 	value="<?= $_POST['subcampaign']?>">
					<input type="hidden" name="securityCode" 	value="<?= $_POST['securityCode']?>">
					<input type="hidden" name="groupId" 		value="<?= $_POST['groupId']?>">
					<input type="hidden" name="refreshRate" 	value="<?= $_POST['refreshRate']?>">

					<div class="modal-body">
		            <!-- content goes here -->
		              <input type="hidden" name="view" id="view" value="<?php echo $_POST['view'] ?>">
						<div class="form-group">
							<label class="warning">Name</label>
							<input type="text" name="name" id="name" class="form-control input" required value="<?= $data['name'] ?>">
						</div>
<!-- 						<div class="form-group">
							<label class="warning">Title</label>
							<input type="text" name="title" id="title" class="form-control input" value="<?= $data['title'] ?>">
						</div> -->
						<div class="form-group">
							<label class="warning">Company</label>
							<input type="text" name="company" id="company" class="form-control input" required value="<?= $data['company'] ?>">
						</div>
						<div class="form-group">
							<label class="warning">Phone</label>
							<input type="text" name="phone" id="phone" class="form-control input"  value="<?= $data['phone'] ?>">
						</div>	
						<div class="form-group">
							<label class="warning">Mobile</label>
							<input type="text" name="mobile" id="mobile" class="form-control input"  value="<?= $data['mobile'] ?>">
						</div>	

						<div class="form-group">
							<label class="warning">Email</label>
							<input type="text" name="email" id="email" class="form-control input" required value="<?= $data['email'] ?>">
						</div>	

						<div class="form-group">
							<label class="warning">Lead Status</label>
							<select name="lead_status" id="lead_status" class="form-control input">
								<?php 
									if ($data['lead_status']=='Open - Not Contacted')
										echo "<option value='Open - Not Contacted' selected>Open - Not Contacted</option>";
									else 
										echo "<option value='Open - Not Contacted'>Open - Not Contacted</option>";
									
									if ($data['lead_status']=='Working - Contacted')
										echo "<option value='Working - Contacted' selected>Working - Contacted</option>";
									else
										echo "<option value='Working - Contacted'>Working - Contacted</option>";

									if ($data['lead_status']=='Closed - Converted')
										echo "<option value='Closed - Converted' selected>Closed - Converted</option>";
									else
										echo "<option value='Closed - Converted'>Closed - Converted</option>";

									if ($data['lead_status']=='Closed - Not Converted')
										echo "<option value='Closed - Not Converted' selected>Closed - Not Converted</option>";
									else
										echo "<option value='Closed - Not Converted'>Closed - Not Converted</option>";
								?>	
							</select>
						</div>	
					</div>

					<div class="modal-footer">
						<div class="form-group">
							<input type="submit" name="submit" class="form-control" value="Submit">
							<div class="loader hidden"></div>
						</div>
					</div>
		        </form>
			</div>
	  </div>
	</div>
	<div class="modal-backdrop fade in"></div>
</body>
</html>
