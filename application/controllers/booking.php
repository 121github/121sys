<?php
//require('upload.php');
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Booking extends CI_Controller
{
    public function __construct()
    {
		parent::__construct();
		        user_auth_check();
        $this->_campaigns = campaign_access_dropdown();
        $this->project_version = $this->config->item('project_version');
		$this->load->model('Booking_model');
	}
	
		 public function get_times()
    {
		$times = array();
		$increment = 30;
		$count = 16;
		$start = "9:00";
		for($i=0;$i<=$count;$i++){
			$mins = $increment * $i;
			$times[] = date('H:i',strtotime("$start +".$mins." minute"));
		};
		return $times;
	}
	
	
		public function get_days()
    {
		$days = array();
		$increment = 1;
		$count = 6;
		for($i=0;$i<=$count;$i++){
			 $days[date('Y-m-d',strtotime("previous monday +".$i." day"))] = array();
		};
		return $days;
	}
	
	
	public function index(){
		$data = array(
            'campaign_access' => $this->_campaigns,
            'title' => 'Bookings',
            'page' => 'Bookings',
			'css'=>array("plugins/fullcalendar-2.6.1/fullcalendar.min.css",      'plugins/bootstrap-toggle/bootstrap-toggle.min.css','plugins/jQuery-contextMenu-master/dist/jquery.contextMenu.min.css'),
			'javascript'=>array("plugins/fullcalendar-2.6.1/fullcalendar.min.js",'plugins/bootstrap-toggle/bootstrap-toggle.min.js',"plugins/fullcalendar-2.6.1/gcal.js","plugins/jQuery-contextMenu-master/dist/jquery.contextMenu.min.js"));
			
		 $this->template->load('default', 'bookings/booking.php', $data);	
	}
	
	public function events(){
		$start = $this->input->get('start');
		$end = $this->input->get('end');
		$attendee = $this->input->get('attendee');
		$events = $this->Booking_model->get_events($start,$end,$attendee);
		foreach($events as $k => $event){
			$events[$k]['color'] = genColorCodeFromText($event['attendees']);
		}
		echo json_encode($events);
	}
	
	public function set_event_time(){
		$start = $this->input->post('start');
		$end = $this->input->post('end');
		$id = $this->input->post('id');
		$this->Booking_model->set_event_time($id,$start,$end);
		echo json_encode(array("success"=>true));
	}
	
	public function google(){
		$data = array(
            'campaign_access' => $this->_campaigns,
            'title' => 'Bookings',
            'page' => 'Bookings',
			'css'=>array("plugins/fullcalendar-2.6.1/fullcalendar.min.css",      'plugins/bootstrap-toggle/bootstrap-toggle.min.css'),
			'javascript'=>array("plugins/fullcalendar-2.6.1/fullcalendar.min.js",'plugins/bootstrap-toggle/bootstrap-toggle.min.js',"plugins/fullcalendar-2.6.1/gcal.js"));
			
		 $this->template->load('default', 'bookings/google.php', $data);	
	}
	
		public function google_events(){
			//get google events and put them into array
			$events = array();
			echo json_encode($events);
		
	}
	
	public function get_appointment_rules_by_date(){
		  $appointment_rules = $this->Booking_model->get_appointment_rules_by_date_and_user($this->input->post('date'), $this->input->post('user_id'));
		  echo json_encode(array("data"=>$appointment_rules));
	}
	
}