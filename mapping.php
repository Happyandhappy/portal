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

if (!isset($_POST['lastview'])) $_POST['lastview'] ="";

$con = getConnection();
$query = "select _keys, _values from mapping where username='" . $_SESSION['username']."'";
$result = $con->query($query);

$data = array();
$data['Id']="";
if($result->num_rows>0){
	while ($row  = $result->fetch_assoc()) {
		$_keys 	 = $row['_keys'];
		$_values = $row['_values'];
		$symbols = explode(';', $_keys);
		$values  = explode(';', $_values);
		for ($i = 0 ; $i<count($symbols) ; $i++){
			$data[$symbols[$i]] = $values[$i];
		}
	}
}else{
	foreach ($_POST as $key => $value) {
		if ($key=='view') continue;
		if ($key=='campaign') break;
		$data[$key] = "";
	}
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
					<form action="./main.php" method="post"> 
						<input type="hidden" name="view" value="<?php echo($_POST['lastview']) ?>">
					<button class="close-thik"></button>
				</form>
				</div>
				<form class="" id = "form" method="POST" action="./main.php">
					<input type="hidden" name="view" 	 		value="<?= $_POST['view']?>">
					<input type="hidden" name="campaign" 		value="<?= $_POST['campaign']?>">
					<input type="hidden" name="subcampaign" 	value="<?= $_POST['subcampaign']?>">
					<input type="hidden" name="securityCode" 	value="<?= $_POST['securityCode']?>">
					<input type="hidden" name="groupId" 		value="<?= $_POST['groupId']?>">
					<input type="hidden" name="lastview" 		value="<?= $_POST['lastview']?>">
					<div class="modal-body">
		            <!-- content goes here -->
		              	<input type="hidden" name="view" id="view" value="<?php echo $_POST['view'] ?>">
						<?php foreach ($_POST as $key => $value) {
							if ($key == 'view') continue;
							if ($key == 'campaign') break;
							if ($key == 'lastview') continue;
							if (isset($data[$key]))
								echo '<div class="form-group">
										<label>' . $value . '</label>
										<input type="text" name="' . $key . '" id="'. $key. '" class="form-control input" value="'. $data[$key] .'">
										</div>';
							else 
								echo '<div class="form-group">
										<label>' . $value . '</label>
										<input type="text" name="' . $key . '" id="'. $key. '" class="form-control input">
										</div>';
						} ?>
						<div class="form-group">
							<label>ID</label>
							<input type="text" name="Id" class="form-control input" value="<?= $data['Id']?>">							
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
