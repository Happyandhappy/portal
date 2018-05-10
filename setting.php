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
$query = "select * from settings where views='" . $_POST['view']."'";
$result = $con->query($query);

if($result->num_rows>0){
	while ($row = $result->fetch_assoc()) {

		$data = array(
					"campaign" 		=> $row['campaign'],
					"subcampaign" 	=> $row['subcampaign'],
					"securityCode" 	=> $row['securityCode'],
					"groupId"		=> $row['groupId'],
					"refreshRate"	=> $row['refreshRate'],
				);
	}
}else{
	$data = array(
					"campaign" 		=> "",
					"subcampaign" 	=> "",
					"securityCode" 	=> "",
					"groupId"		=> "",
					"refreshRate"	=> 5,
				);
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
	<!-- <script src="./assets/js/setting.js"></script> -->
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
				<h3 class="modal-title" id="lineModalLabel"><i class="glyphicon glyphicon-cog"></i>  Settings</h3>
			</div>
			<form class="form" id = "form" method="POST" action="./mapping.php">
				<?php
					foreach ($_POST as $key => $value) {
						echo "<input type='hidden' name='$key' value='$value'>";
					}

				?>
				<div class="modal-body">
	            <!-- content goes here -->
	              <input type="hidden" name="view" id="view" value="<?php echo $_POST['view'] ?>">
					<div class="form-group">
						<label class="warning">Campaign Name</label>
						<input type="text" name="campaign" id="campaign" class="form-control input" required value="<?= $data['campaign'] ?>">
					</div>
					<div class="form-group">
						<label>Sub Campaign Name</label>
						<input type="text" name="subcampaign" id="subcompaign" class="form-control input" value="<?= $data['subcampaign'] ?>">
					</div>
					<div class="form-group">
						<label class="warning">SecurityCode</label>
						<input type="text" name="securityCode" id="securityCode" class="form-control input" required value="<?= $data['securityCode'] ?>">
					</div>
					<div class="form-group">
						<label class="warning">GroupId</label>
						<input type="text" name="groupId" id="gruopId" class="form-control input" required value="<?= $data['groupId'] ?>">
					</div>	
					<div class="form-group">
						<label class="warning">Refresh Rate (min)</label>
						<input type="number" name="refreshRate" id="refreshRate" class="form-control input" required value="<?= $data['refreshRate'] ?>">
					</div>					
				</div>

				<div class="modal-footer">
					<div class="form-group">
						<input type="submit" name="submit" class="form-control" value="Continue">
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