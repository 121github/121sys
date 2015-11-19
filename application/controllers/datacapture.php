<?php
//class just for testing things
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Datacapture extends CI_Controller
{
	 public function __construct()
    {
        parent::__construct();
	 $this->load->model('User_model');
	}
	
	
public function index(){
	if(!$this->input->get_post('id')||!$this->input->get_post('key')){
		 $error = "You must include the id and key fields in the url";
		 echo json_encode(array("success"=>false,"error"=>$error));
		 exit;
	}
		if(!isset($_GET['campaign'])){
		 $error = "You must include the campaign id in the url";
		 echo json_encode(array("success"=>false,"error"=>$error));
		 exit;
		} 
		if(empty($_GET['campaign'])){
		 $error = "The campaign field cannot be empty";
		 echo json_encode(array("success"=>false,"error"=>$error));
		 exit;
		}   
			$user_id = $this->User_model->validate_login($this->input->get('id'), $this->input->get('key'),true,true);

		  if (!$user_id) {
			 $error = "The id and key combination were invalid";  
			  echo json_encode(array("success"=>false,"error"=>$error));
		 	  exit;
		  	}
			
		//check the user has access to the selected campaign
		  $qry = "select campaign_id from campaigns left join `users_to_campaigns` using(campaign_id) where campaign_status = 1 and user_id = '" . $user_id . "' and campaign_id = '".intval($_GET['campaign'])."'";
  if($this->db->query($qry)->num_rows()==0){
	   $error = "Invalid campaign id";  
			  echo json_encode(array("success"=>false,"error"=>$error));
		 	  exit;
  		}
			//fields
			$name = $this->input->get('name')==""?NULL:$this->input->get_post('name');
			$email = $this->input->get('email')==""?NULL:$this->input->get_post('email');
			$telephone = $this->input->get('tel')==""?NULL:$this->input->get_post('tel');
			$postcode = $this->input->get('postcode')==""?NULL:$this->input->get_post('postcode');
			$callback_time = $this->input->get('callback_time')==""?NULL:$this->input->get_post('callback_time');
			$campaign = intval($this->input->get_post('campaign'));
			
			if(empty($telephone)){
			  $error = "No telephone number was provided";  
			  echo json_encode(array("success"=>false,"error"=>$error));
		 	  exit;
			}
			//check duplicates
			$this->db->where(array("telephone_number"=>$telephone,"campaign_id"=>$campaign));
			$this->db->join("contacts","contacts.contact_id=contact_telephone.contact_id");
			$this->db->join("records","contacts.urn=records.urn");
			$check = $this->db->get("contact_telephone")->num_rows();

			if($check>0){
				 $error = "Telephone number already exists in this campaign";  
			  echo json_encode(array("success"=>false,"error"=>$error));
		 	  exit;
			}
			//insert the record
			$insert_record = array("campaign_id"=>$campaign,"added_by"=>$user_id,"date_added"=>date('Y-m-d H:i:s'));
			$this->db->insert("records",$insert_record);
			$urn = $this->db->insert_id();
			//insert the record details
			$insert_record_details = array("urn"=>$urn,"c1"=>$callback_time);
			$this->db->insert("record_details",$insert_record_details);
			//insert the contact
			$insert_contact = array("urn"=>$urn,"fullname"=>$name,"email"=>$email);
			$this->db->insert("contacts",$insert_contact);
			$this->firephp->log($this->db->last_query());
			$id = $this->db->insert_id();
			//insert the contact telephone
			$insert_contact_telephone = array("contact_id"=>$id,"description"=>"Telephone","telephone_number"=>$telephone);
			$this->db->insert("contact_telephone",$insert_contact_telephone);
			
			//insert the contact address
			if(!empty($postcode)){
			$insert_contact_address = array("contact_id"=>$id,"postcode"=>$postcode);
			$this->db->insert("contact_addresses",$insert_contact_address);
			$this->firephp->log($this->db->last_query());
			}
			if(!$this->db->_error_message()){
			echo  json_encode(array("success"=>true,"record_id"=>$urn));
		 	exit;	
			}
			
	
	if(!isset($_GET['json'])){
	echo "<p>The list below shows the data that we will capture in this url. If you would like the response in json format please include <b>&amp;json=true</b> in your URL parameters</p>";
foreach($_GET as $k=>$v){

	$key = htmlentities($k);
	$val = htmlentities($v);
	echo $key.":<b>".$val."</b><br>";
	}
	} else {
	echo json_encode($_GET);	
	}

}
}