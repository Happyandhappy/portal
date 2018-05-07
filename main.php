<?php
session_start();	 
require_once('./config/database.php'); 
require_once('./config/config.php');
require_once('./app/Saleforce.php');


$_SESSION['refreshRate'] = getRefreshRate();


if(!isset($_SESSION['access_token']) || $_SESSION['access_token'] == "")
{
	header("Location: ./index.php");
	exit;
}

$sale = new Saleforce($_SESSION['access_token'], $_SESSION['instance_url']);

$view = "";
$data_result = array();
if ( count($_POST) > 0 ) {
	$campaign 		= $_POST['campaign'];
	$subcampaign 	= $_POST['subcampaign'];
	$securityCode 	= $_POST['securityCode'];
	$groupId		= $_POST['groupId'];
	$refreshRate	= $_POST['refreshRate'];
	
	// get view data
	$view = $_POST['view'];
	$table_data = $sale->getListViewDetail($view);

	// get owner data
	$owner_url = "/services/data/v42.0/queryAll/?q=select+LastName+,+FirstName+,+City+,+State+,+Country+,+PostalCode+,+StayInTouchNote+,+MobilePhone+,+Phone+from+User+where+Username+=+'".$_SESSION['username']."'";
	$owner_data = $sale->getListViewDetail($owner_url);
	$owner_data = $owner_data->records[0];

	if (isset($owner_data->LastName)) $lastName = $owner_data->LastName;
	else $lastName = "";

	if (isset($owner_data->FirstName)) $firstName = $owner_data->FirstName;
	else $firstName = "";

	if (isset($owner_data->Address)) $address = $owner_data->Address->street . ", " . $owner_data->Address->state . ", " . $owner_data->Address->country;
	else $address = "";

	if (isset($owner_data->City)) $city = $owner_data->City;
	else $city = "";

	if (isset($owner_data->State)) $state = $owner_data->State;
	else $state = "";

	if (isset($owner_data->PostalCode)) $zipcode = $owner_data->PostalCode;
	else $zipcode = "";

	if (isset($owner_data->StayInTouchNote)) $notes = $owner_data->StayInTouchNote;
	else $notes = "";

	if(isset($owner_data->MobilePhone)) $mobile = $owner_data->MobilePhone;
	else $mobile = "";

	if (isset($owner_data->Phone)) $phone = $owner_data->Phone;
	else $phone = "";

	$ClientId = 1;
	// save or update data to db 
	$con = getConnection();

	$query = "select * from settings where view='" . $_POST['view'] . "'";
	$res = $con->query($query);
	if ($res->num_rows>0){
		$query = "UPDATE settings SET campaign = '".$campaign."', subcampaign='". $subcampaign . "' , securityCode='" . $securityCode. "', groupId='" . $groupId . "', refreshRate =" . $refreshRate . ", firstName = '" . $firstName . "' , lastName = '" . $lastName . "' , address = '" .  $address . "' , city = '" . $city . "' , state = '" . $state . "' , zipcode = '" . $zipcode . "' , notes = '" . $notes . "', mobile = '" . $mobile . "', phone ='" . $phone . "' where view = '" . $_POST['view']."'";		
	}
	else{
		$query = "INSERT settings (username, campaign, subcampaign, securityCode, groupId, refreshRate, firstName, lastName, address, city, state, zipcode, notes, mobile, phone, view) VALUES ('".$_SESSION['username']. "','" . $campaign . "','" . $subcampaign . "','" . $securityCode . "','" . $groupId . "','" . $refreshRate . "','" . $firstName . "','" . $lastName . "','" . $address. "','" . $city . "','" . $state . "','" . $zipcode . "','" . $notes . "','" . $mobile . "','" . $phone . "','" . $view  . "')";
	}
	$res = $con->query($query);


// update or inject curl requests here///////////////////////////////////////////////////////////////
	$url = "https://www.chasedatacorp.com/HttpImport/InjectLead.php?Campaign=" . $campaign . 
			"&Subcampaign=" . $subcampaign . 
			"&GroupId=" . $groupId . 
			"&SecurityCode=".$securityCode . 
			"&FirstName=".$firstName . 
			"&LastName=" . $lastName . 
			"&ClientId=" . $ClientId . 
			"&Address=" . $address . 
			"&City=" . $city . 
			"&State=" . $state . 
			"&ZipCode" . $zipcode . 
			"&Notes=" . $notes . 
			"&PrimaryPhone=" . $phone . 
			"&adv_MobilePhone=" . $mobile."&DuplicatesCheck=2";

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$res = curl_exec($ch);
	curl_close($ch);
	$res = str_replace("<br>", "", $res);
	$res = str_replace("\n", "", $res);

	if ($res != "Result: OK"){
		$url = "https://www.chasedatacorp.com/HttpImport/UpdateLead.php?GroupId=" . $groupId . 
				"&SecurityCode=" . $securityCode . 
				"&SearchField=Phone&Identifier=" . $phone . 
				"&FirstName=" . $firstName . 
				"&LastName=" . $lastName . 
				"&adv_MobilePhone=" . $mobile . 
				"&Address=" . $address . 
				"&State=" . $state . 
				"&ZipCode" . $zipcode . 
				"&Notes=" . $notes;

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$res = curl_exec($ch);
			curl_close($ch);
	}
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
	<input type="hidden" id = "refreshRate" name="refreshRate" value="<?php echo $refreshRate;?>">
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
			<div class="col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-6 col-offset-3">
				
				<div class="row icon">
						<div class="_logo">
							<img src="./assets/logo.jpg">
						</div>
					</div>
				<form method="POST" class="form" action="./setting.php">
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
				</form>
			</div>	
			<div  class="col-md-12 col-sm-6 col-6 col-offset-3">	
				<table class="table table-striped" id="table">
					<?php if (isset($table_data->records) && count($table_data->records)>0):?>
					<thead>
						<tr>
							<th>_No</th>
							<th>NAME</th>
							<th>COMPANY</th>
							<th>STATE/PROVINCE</th>
							<th>EMAIL</th>
							<th>LEAD STATUS</th>
							<th>CREATED DATE</th>
							<th>OWNER ALIAS</th>
							<th>UNREAD BY OWNER</th>
						</tr>
					</thead>
						<?php foreach ($table_data->records as $row) { $index++;?>
							<tr>
								<td><?= $index ?></td>
								<td><?= $row->columns[0]->value ?></td>
								<td><?= $row->columns[1]->value ?></td>
								<td><?= $row->columns[2]->value ?></td>
								<td><?= $row->columns[3]->value ?></td>
								<td><?= $row->columns[4]->value ?></td>
								<td><?= $row->columns[5]->value ?></td>
								<td><?= $row->columns[6]->value ?></td>
								<td>
									<?php 
										if ($row->columns[7]->value == 'true') 
											echo "<input type='checkbox' checked disabled>";
										else 
											echo "<input type='checkbox' disabled>";
									?>
								</td>
							</tr>	
						<?php } ?>
					<tbody>
						
					</tbody>
					<?php endif?>				
				</table>
			</div>
		</div>
			<div class="loader hidden"></div>
		</div>

</body>
</html>