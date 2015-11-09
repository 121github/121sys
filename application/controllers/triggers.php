<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Triggers extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->model('Form_model');
		  $this->load->model('Trigger_model');
		$this->_access = $this->User_model->campaign_access_check($this->input->post('urn'), true);
    }
  
  //sends an email and sms to the contacts if this is the first no contact outcome for the record , or if it's the 4th attempt they get an sms and an email. Only if they haven't recieved an email or text already in the last 24 hours
public function ghs_no_contact_notification(){
	$urn = $this->input->post('urn');
	$qry="select history_id,campaign_id from history join outcomes using(outcome_id) where delay_hours is not null and urn = '$urn'";
	$query = $this->db->query($qry);
	if($query->num_rows()=="1"||$query->num_rows()=="4"){
		if($query->row()->campaign_id ==22){
					$email_template_id = "22";
					$sms_template_id = "2";
		} 
		//FIRST TIME - send email and sms
	if($query->num_rows()=="1"){
		$email_trigger = $this->Trigger_model->send_email_to_contact($email_template_id,$urn);
		$sms_trigger = $this->Trigger_model->send_sms_to_contact($sms_template_id,$urn);
	}
		//FORTH TIME - sms only
		if($query->num_rows()=="4"){
		$sms_trigger = $this->Trigger_model->send_sms_to_contact($sms_template_id,$urn);
		}
		
		$functions = array();
		if($email_trigger){
		$functions[] = 	'campaign_functions.email_trigger';
		}
		if($sms_trigger){
		$functions[] = 	'campaign_functions.sms_trigger';
		}
		//if the response contains "js_functions", any functions in this array will be ran by the javascript
		echo json_encode(array("js_functions"=>$functions));
		
	}

}


}