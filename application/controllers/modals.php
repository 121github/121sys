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
		$data = array();
		$urn = intval($this->input->post('urn'));
		$data = $this->Modal_model->view_record($urn);
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
		
	    $this->load->view('forms/edit_appointment_form.php',array("attendees"=>$attendees,"addresses"=>$addresses,"types"=>$types)); 	
		}
	}
}