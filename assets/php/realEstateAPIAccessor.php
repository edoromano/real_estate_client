<?php
ini_set('max_execution_time', 30000);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

class Request 
{ 
    public $username = 'eromano'; 
    public $password = '12345678'; 
    private $auth_token = 'uC2MfNQoqjUXufyY-ntZ';
    private $apiURL = 'http://45.55.167.103:3000/api/';
    private $acceptHeader = 'application/vnd.real_estate.v1';
    private $contentTypeHeader = 'application/json';


    function formatParameters($params, $method = "GET"){
        $array = [];
        $parameters = [];
        $theKey = "";
        foreach ($params as $kkey=> $object) {
            $theKey = $kkey;
            $array[$kkey] = array();            
            foreach ($object as $key => $value) {
                $parameters[$key] = [];
                $parameters[$key]=$value;
            }
            $array[$theKey] = $parameters;      
        }
        if($method == "GET")
            return  $theKey!=""? http_build_query($array[$theKey]): "";
        else
            return json_encode($array);
    }

    function getHouses($params){
        $headers = array("Accept: application/json", "Content-Type: application/json", "Accept: ".$this->acceptHeader);
        return $this->doRequest($this->apiURL."/houses", $headers,"GET", $params, "house");
    }

    function setHouse($params){
        $headers = array("Accept: application/json", "Content-Type: application/json", "Accept: ".$this->acceptHeader, "Authorization: ".$this->auth_token);
        return $this->doRequest($this->apiURL."users/1/houses", $headers,"POST", $params, "house");
    }


    function doRequest($url, $headers, $method, $params = "", $entity =""){
    	$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        $formatedParameters = $this->formatParameters($params, $method);       
        if($method == "POST"){          
            curl_setopt($curl,CURLOPT_POST, 1);
            curl_setopt($curl,CURLOPT_POSTFIELDS, $formatedParameters);
            curl_setopt($curl, CURLOPT_URL, $url);
        }
        else{
            curl_setopt($curl, CURLOPT_URL, $url."?".$formatedParameters);
        }

        $resp = curl_exec($curl);
		$ch_error = curl_error($curl);
		if($ch_error){
			echo "hay un error";	
			echo $ch_error;
		}
		else {
			return $resp;
		}
		curl_close($curl);
    }
} 

$response = new Request();

switch ($_REQUEST["request"]) {
	case 'houses':
        if($_REQUEST["action"]=='get')
		  echo $response->getHouses(isset($_REQUEST['params'])? $_REQUEST['params'] : []);
		break;
	
	case 'users':
        if($_REQUEST["action"]=='createUser')
          echo $response->setHouse(isset($_REQUEST['params'])? $_REQUEST['params'] : []);
        if($_REQUEST["action"]=='createHouse')
          echo $response->setHouse(isset($_REQUEST['params'])? $_REQUEST['params'] : []);
		break;

	default:
		echo $response->getHouses();
		break;
}
