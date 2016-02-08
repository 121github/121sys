<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Google extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
$this->client = new Google_Client();
$this->client->setAccessType('offline');
$this->client->setApplicationName('121 System');
$this->client->setClientId('941364401612-76ishcpj4cld2kjk8f84uk6c1tgvn6vv.apps.googleusercontent.com');
$this->client->setClientSecret('gBtxtvrN_KmFmEqZqpCrtrkb');
$this->client->setScopes(array("https://www.googleapis.com/auth/calendar","https://www.googleapis.com/auth/userinfo.email"));
$scriptUri = "http://".$_SERVER["HTTP_HOST"].'/121sys/google/authcode';
$this->client->setRedirectUri($scriptUri);
$this->client->setDeveloperKey('AIzaSyBimUzlpAP3mDdBVMlQLiG9K76rUU1ifKM'); 
	}
	
	public function authenticate(){
$auth_url = $this->client->createAuthUrl();
header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
	}
	
	public function authcode(){		

if (!isset($_GET['code'])) {
 $this->authenticate();
} else {
  $this->client->authenticate($_GET['code']);
  $token=  json_decode($this->client->getAccessToken(),true);
  $_SESSION['api']['google']['token'] = $token;
  $data = array("api_name"=>"google","api_token"=>json_encode($token),"user_id"=>$_SESSION['user_id'],"date_added"=>date('Y-m-d H:i:s'));
$this->db->where(array("user_id"=>$_SESSION['user_id'],"api_name"=>"google"));
$this->db->insert_update("apis",$data);
  
  $redirect_uri = base_url().'calendar/google';
  header('Location: ' . $redirect_uri);
}

	echo json_encode(array("success"=>true));	
	}
	
	
	public function logout(){
	if(isset($_SESSION['api']['google'])){
	unset($_SESSION['api']['google']);	
	}
	$this->db->where(array('user_id'=>$_SESSION['user_id'],'api_name'=>'google'));	
	$this->db->delete('apis');
	$redirect_uri = $_SERVER['HTTP_REFERER'];
  	header('Location: ' . $redirect_uri);
	}
}

