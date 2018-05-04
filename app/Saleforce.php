<?php
/**
* className : Saleforce
*/

class Saleforce
{
	public $user   = "info@chasedatacorp.com";
	public $pass   = "Dialer123";
	public $secret_token  = "uqlprJZbFTxPWRqjJ7dY7Qco";
	public $con = null;
  public $instance = "na24";
  public $parentId = "0051a000000aNCU";
  public $accessToken = "";

	function __construct( $accessToken, $instance)
	{
     $this->accessToken = $accessToken;
     $this->instance = $instance;
	}

   	function getHeaders(){
        return array(
            "Authorization: Bearer ".$this->accessToken,
        );
   	}

   	function Httprequest($url,$method,$data=""){
   		$curl= curl_init($url);
        curl_setopt($curl,CURLOPT_HTTPHEADER,$this->getHeaders());
        curl_setopt($curl,CURLOPT_CUSTOMREQUEST,$method);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    	  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT ,0);
        curl_setopt($curl, CURLOPT_TIMEOUT, 400); //timeout in seconds

        if($method=="POST" || $method=="PATCH"){
            curl_setopt($curl, CURLOPT_POSTFIELDS, (gettype($data)==="string")?$data:json_encode($data));
        }
        $response= curl_exec($curl);
        curl_close($curl);
        return ($response);
   	}

   	function getRestApiVersions(){
     		$url  = $this->instance . "/services/data";
     		$data = $this->Httprequest($url,"GET");
     		return json_decode($data);
   	}

    function getOrganizationLimits(){
        $url  = $this->instance."/services/data/v42.0/limits/";
        echo $url;
        $data = $this->Httprequest($url,"GET");
        return json_decode($data);
    }

    function getAvailableResources(){
      $url  = $this->instance . "/services/data/v42.0/";
      $data = $this->Httprequest($url,"GET");
      return json_decode($data);
    }

    function getObjectsList()
    {
      $url =$this->instance . "/services/data/v42.0/sobjects/";
      $data = $this->Httprequest($url,"GET");
      return json_decode($data);
    }

    function retrieveMetadataOfobject(){
      $url = $this->instance . "/services/data/v42.0/sobjects/Account/";
      $data = $this->Httprequest($url,"GET");
      return json_decode($data);
    }

    function getListView($type){
        $url = $this->instance . "/services/data/v42.0/sobjects/" . $type . "/listviews";
        $data = $this->Httprequest($url, "GET");
        return json_decode($data);
    }

    function getListViewDetail($url){
        return json_decode($this->Httprequest($this->instance.$url,"GET"));
    }

    
}
