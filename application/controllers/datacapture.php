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
		if(!$this->input->get_post('campaign')){
		 $error = "You must include the campaign id in the url";
		 echo json_encode(array("success"=>false,"error"=>$error));
		 exit;
		} 
		if($this->input->get_post('campaign')==""){
		 $error = "The campaign field cannot be empty";
		 echo json_encode(array("success"=>false,"error"=>$error));
		 exit;
		}   
		if($this->input->get_post('source')==""){
		 $error = "The source field cannot be empty";
		 echo json_encode(array("success"=>false,"error"=>$error));
		 exit;
		}   
			$user_id = $this->User_model->validate_login($this->input->get_post('id'), $this->input->get_post('key'),true,true);

		  if (!$user_id) {
			 $error = "The id and key combination were invalid";  
			  echo json_encode(array("success"=>false,"error"=>$error));
		 	  exit;
		  	}
			
		//check the user has access to the selected campaign
		  $qry = "select campaign_id from campaigns left join `users_to_campaigns` using(campaign_id) where campaign_status = 1 and user_id = '" . $user_id . "' and campaign_id = '".intval($this->input->get_post('campaign'))."'";
  if($this->db->query($qry)->num_rows()==0){
	   $error = "Invalid campaign id";  
			  echo json_encode(array("success"=>false,"error"=>$error));
		 	  exit;
  		}

		$source = $this->input->get_post('source');
		$this->db->where("source_name",$source);
		$get_source = $this->db->get("data_sources");
		if($get_source->num_rows()>0){
		$source_id = $get_source->row()->source_id;	
		} else {
			$this->db->insert("data_sources",array("source_name"=>$source));
			$source_id = $this->db->insert_id();
		}
		
			//fields //these fields are being sent by the shade greener webform. for other webforms the fields might be different. Eventually we should make this database driven because different clients may have different field names. Alternatively we should specify the field names to the client!!!
			$name = $this->input->get_post('name')==""?NULL:$this->input->get_post('name');
			$email = $this->input->get_post('email')==""?NULL:$this->input->get_post('email');
			$telephone = $this->input->get_post('tel')==""?NULL:$this->input->get_post('tel');
			$postcode = $this->input->get_post('postcode')==""?NULL:$this->input->get_post('postcode');
			$source = $this->input->get_post('source')==""?NULL:$this->input->get_post('source');
			$callback_time = $this->input->get_post('callback_time')==""?NULL:$this->input->get_post('callback_time');
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
			$insert_record = array("campaign_id"=>$campaign,"added_by"=>$user_id,"date_added"=>date('Y-m-d H:i:s'),"source_id"=>$source_id,"pot_id"=>65);
			if(strtolower($callback_time)=="am"){
			$insert_record['nextcall'] = date('Y-m-d 09:00:00');
			} else if(strtolower($callback_time)=="pm"){
			$insert_record['nextcall'] = date('Y-m-d 13:00:00');
			}
			$this->db->insert("records",$insert_record);
			$urn = $this->db->insert_id();
			//insert the record details
			$insert_record_details = array("urn"=>$urn,"c1"=>strtolower($callback_time),"c2"=>$source);
			$this->db->insert("record_details",$insert_record_details);
			//insert the contact
			$insert_contact = array("urn"=>$urn,"fullname"=>$name,"email"=>$email);
			$this->db->insert("contacts",$insert_contact);
			$id = $this->db->insert_id();
			//insert the contact telephone
			$insert_contact_telephone = array("contact_id"=>$id,"description"=>"Telephone","telephone_number"=>$telephone);
			$this->db->insert("contact_telephone",$insert_contact_telephone);
			
			//insert the contact address
			if(!empty($postcode)){
			$insert_contact_address = array("contact_id"=>$id,"postcode"=>$postcode, "primary" => 1);
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