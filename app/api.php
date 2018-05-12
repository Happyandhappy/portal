<?php

//https://login.salesforce.com/id/00D1a000000Z2uZEAS/0051a000000aNCUAA2

session_start();
require_once('../config/database.php'); 
require_once('../config/config.php');
require_once('./Saleforce.php');

if (isset($_GET['logout'])) logout();
if (isset($_GET['get']) && $_GET['get']!="") getListData();

function logout(){
	session_destroy();
	header("Location: ../index.php");
	exit;
}

function getListData(){
	$url = $_GET['get'];
	$sale = new Saleforce($_SESSION['access_token'], $_SESSION['instance_url']);
	echo json_encode($sale->getListViewDetail($url));
}

if (isset($_GET['option'])){
	$con = getConnection();
	$query = "select * from settings where views='" . $_GET['view'] . "'";
	
	$result = $con->query($query);
	if($result->num_rows>0){
		$query = "UPDATE settings SET opt=" . $_GET['option'] . " where views='" . $_GET['view'] . "'";
		$res = $con->query($query);
		echo $query;
	}else{
		$query = "INSERT INTO settings (opt) VALUES(" . $_GET['option'] . ")";
		$res = $con->query($query);
		echo "success insert";
	}
}

	// $url = $_SESSION['instance_url']."/services/data/v42.0/queryAll/?q=SELECT+name+from+Lead+where+Campaign+=+'Test'";
	// $url = $_SESSION['instance_url']."/services/data/v42.0/queryAll/?q=SELECT+name+from+CampaignMember+where+CampaignId+=+'7011a000000SlNOAA0'";
	// $url = $_SESSION['instance_url']."/services/data/v42.0/sobjects/CampaignMember/00v1a00000X631aAAB";
	

	// $ch = curl_init($url);
	// curl_setopt($ch,CURLOPT_HTTPHEADER, array("Authorization: Bearer ".$_SESSION['access_token']));
	// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	// $response = curl_exec($ch);
	// curl_close($ch);
	// $response = json_decode($response);
	// echo "<pre>";
	// print_r($response);
	// echo "</pre>";
	// exit;

//services/data/v42.0/sobjects/Campaign/listviews
//services/data/v42.0/sobjects/Account/listviews
//services/data/v42.0/sobjects/Case/listviews
//services/data/v42.0/sobjects/Contact/listviews
//services/data/v42.0/sobjects/Lead/listviews
//services/data/v42.0/sobjects/Opportunity/listviews
//services/data/v42.0/sobjects/Order
//services/data/v42.0/sobjects/Product2
//services/data/v42.0/sobjects/Report
///services/data/v42.0/sobjects/ContentAsset  //file
//services/data/v42.0/sobjects/ChatterActivity
//services/data/v42.0/sobjects/ListEmail