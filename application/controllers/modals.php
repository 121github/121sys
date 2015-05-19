<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Modals extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        user_auth_check();
        $this->_campaigns = campaign_access_dropdown();
        $this->load->model('User_model');
        $this->load->model('Modal_model');
        $this->_access = $this->User_model->campaign_access_check($this->input->post('urn'), true);
    }

	public function view_record(){
		if ($this->input->is_ajax_request()) {
			$this->load->model('Records_model');
		$data = array();
		$ownership = array();
		$urn = intval($this->input->post('urn'));
		$record = $this->Modal_model->view_record($urn);
		foreach($record as $row){
		$data=$row;
		if($row['owner']){
		$ownership[$row['owner']] = $row['owner'];	
		}
		}
		if(count($ownership)){
		$data['ownership'] = implode(", ",$ownership);
		} else {
		$data['ownership'] = "-";	
		}
		$history = $this->Modal_model->view_history($urn);
		$data['history'] = $history;
		$appointments = $this->Modal_model->view_appointments($urn);
		foreach($appointments as $k => $appointment){
		if($appointment['status'] == "1" && strtotime($appointment['sqlstart']) < strtotime(date('Y-m-d H:i'))){	
		$appointments[$k]['status'] = "<span class='glyphicon glyphicon-time orange'></span>";
		} else if($appointment['status'] == "1" && strtotime($appointment['sqlstart']) > strtotime(date('Y-m-d H:i'))){
		$appointments[$k]['status'] = "<span class='glyphicon glyphicon-ok green'></span>";	
		} else if($appointment['status'] == "0"){
		$appointments[$k]['status'] = "<span class='glyphicon glyphicon-remove red'></span>";	
		}
		}
		$data['appointments'] = $appointments;
		//add in the custom fields
		$additional_info = $this->Records_model->get_additional_info($urn, $data['campaign_id']);
		$data['custom_info'] = $additional_info;
		$all_addresses = $this->Records_model->get_addresses($urn);
		$addresses = array();
		 foreach($all_addresses as $k=>$address): 
		 if(!empty($address['postcode'])){
 			$add = ($address['type']=="company"?$address['name'].", ":"");
 			$add .= (!empty($address['add1'])?$address['add1'].", ":"");
			$add .= (!empty($address['postcode'])?$address['postcode']:"");
			$addresses[] = array("postcode"=>$address['postcode'],"address"=>$add);
		}
 			endforeach;
		$data['addresses'] = $addresses;
		
		echo json_encode(array("success"=>true,"data"=>$data));
		}
	}

    public function view_appointment()
    {
		if ($this->input->is_ajax_request()) {
		$data = array();
		$id = intval($this->input->post('id'));
		$postcode= false;
		if(isset($_SESSION['current_postcode'])){
		$postcode = postcodeFormat($_SESSION['current_postcode']);
		}
		$result = $this->Modal_model->view_appointment($id,$postcode);
		if($result){
		foreach($result as $row){
		$attendee_names[] = $row['attendee'];
		$attendees[] = $row['user_id'];
		$data = $row;
		$data['attendee_names'] = $attendee_names;	
		$data['attendees'] = $attendees;
		}
		$data['current_postcode'] = $postcode;	
		echo json_encode(array("success"=>true,"data"=>$data));
		} else {
		echo json_encode(array("success"=>false,"msg"=>"Appointment could not be loaded"));	
		}
		}
	}
	public function edit_appointment(){
		if ($this->input->is_ajax_request()) {
		$this->load->model('Records_model');
		$urn = intval($this->input->post('urn'));
		$campaign_id = $this->Records_model->get_campaign_from_urn($urn);
		$addresses         = $this->Records_model->get_addresses($urn);
        $attendees         = $this->Records_model->get_attendees(false, $campaign_id);
		$types         = $this->Records_model->get_appointment_types(false, $campaign_id);
		
	    $this->load->view('forms/edit_appointment_form.php',array("urn"=>$urn,"attendees"=>$attendees,"addresses"=>$addresses,"types"=>$types)); 	
		}
	}
}