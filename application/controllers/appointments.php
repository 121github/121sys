<?php
require('upload.php');

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Appointments extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();
        user_auth_check();
        $this->_campaigns = campaign_access_dropdown();
        $this->load->model('User_model');
        $this->load->model('Records_model');
        $this->load->model('Survey_model');
        $this->load->model('Form_model');
		$this->load->model('Appointments_model');
        $this->_access = $this->User_model->campaign_access_check($this->input->post('urn'), true);
    }
public function index(){
	 $data = array(
            'campaign_access' => $this->_campaigns,
            'pageId' => 'System appointment',
            'title' => 'Appointments',
			'page'=> "appointments",
			            'css' => array(
                'dashboard.css',
                'plugins/morris/morris-0.4.3.min.css',
                'daterangepicker-bs3.css'
            )
        );
        $this->template->load('default', 'dashboard/appointments.php', $data);
}

    public function appointment_data()
    {
        if ($this->input->is_ajax_request()) {

            $records = $this->Appointments_model->appointment_data(false,$this->input->post());
            $count = $this->Appointments_model->appointment_data(true,$this->input->post());
            
            $data = array(
                "draw" => $this->input->post('draw'),
                "recordsTotal" => $count,
                "recordsFiltered" => $count,
                "data" => $records
            );
            echo json_encode($data);
        }
    }

	public function appointment_modal(){
	$id = $this->input->post('id');
    $appointment = $this->Appointments_model->appointment_modal($id);
	$attendees = $this->Appointments_model->appointment_attendees($id);
	$formatted_date = date('D jS M Y',strtotime($appointment['date_added']));
	$appointment['date_formatted'] = $formatted_date;
	$result['appointment'] = $appointment;
	$result['attendees'] = $attendees;
	echo json_encode(array("success"=>true,"data"=>	$result));
	}
	
}