<?php
//require('upload.php');
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Calendar extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        user_auth_check(false);
		$this->_campaigns = campaign_access_dropdown();
        $this->load->model('Calendar_model');
		$this->load->model('Form_model');
    }

	public function get_calendar_users(){
		if($this->input->post('campaigns')){
			$campaigns = $this->input->post('campaigns');
		} else {
		$campaigns = array();	
		}
			$users = $this->Form_model->get_calendar_users($campaigns);
			echo json_encode(array("success"=>true,"data"=>$users));
	}

	  //this loads the data management view
    public function index()
    {
		$users = array();
		if(in_array("search campaigns",$_SESSION['permissions'])){
		$campaigns = $this->Form_model->get_calendar_campaigns();
		$users = isset($_SESSION['current_campaign'])?$this->Form_model->get_calendar_users(array($_SESSION['current_campaign'])):"";
		$disable_campaign_filter = false;
		}
		if(!in_array("search campaigns",$_SESSION['permissions'])){
		$users = isset($_SESSION['current_campaign'])?$this->Form_model->get_calendar_users(array($_SESSION['current_campaign'])):"";	
		$campaigns = $this->Form_model->get_calendar_campaigns();
		$disable_campaign_filter = true;
		}
		if($disable_campaign_filter&&!isset($_SESSION['current_campaign'])){
		 redirect('error/calendar');	
		}
        $data      = array(
            'campaign_access' => $this->_campaigns,
			'pageId' => 'Dashboard',
            'title' => 'Dashboard',
            'page' => array(
                'admin' => 'data'
            ),
            'javascript' => array(
			'lib/underscore.js',
                'plugins/calendar/js/calendar.js',
				'calendar.js',
				'location.js'
            ),
			'disable_campaign'=>$disable_campaign_filter,
			'date' => date('Y-m-d'),
            'campaigns' => $campaigns,
			'users'=>$users,
            'css' => array(
                'calendar.css',
            )
        );
        $this->template->load('default', 'calendar/view.php', $data);
    }
	
	public function get_demo(){
		echo '{
    "success": 1,
    "result": [
        {
            "id": 293,
            "title": "Event 1",
            "url": "http://example.com",
            "class": "event-important",
            "start": 1415724392424,
            "end": 1415724399424
        }
    ]
}';
		
	}
	
	public function get_events(){
		$start = date('Y-m-d h:i:s', ($_POST['startDate'] / 1000));
		$end = date('Y-m-d h:i:s', ($_POST['endDate'] / 1000));
		
		if(!empty($_POST['users'])){
		$users = $_POST['users'];	
		} else {
		$users = (isset($_SESSION['users'])?$_SESSION['users']:"");	
		}
		
		$postcode = postcodeCheckFormat($this->input->post('postcode'));
		if(!empty($_POST['postcode'])){
		$postcode = $_POST['postcode'];	
		} else {
		$postcode = (isset($_SESSION['postcode'])?$_SESSION['postcode']:"");	
		}
		$this->firephp->log($postcode);
		if(!empty($_POST['distance'])){
		$distance = $_POST['distance'];	
		} else {
		$distance = (isset($_SESSION['distance'])?$_SESSION['distance']:"");	
		}
		
			if(!empty($_POST['campaigns'])){
		$campaigns = $_POST['campaigns'];	
		} else {
		$campaigns = (isset($_SESSION['campaigns'])?$_SESSION['campaigns']:"");	
		}
		
		$options = array("start"=>$start,"end"=>$end,"users"=>$users,"campaigns"=>$campaigns,"postcode"=>$postcode,"distance"=>$distance);
		$_SESSION['calendar-filter'] = $options;
		$result = array();
		$events = $this->Calendar_model->get_events($options);
		
		foreach($events as $row) {
    $result[] = array(
        'id' => $row['appointment_id'],
        'title' => $row['title'],
		'text' => (!empty($row['text'])?$row['text']:""),
        'url' => base_url().'records/detail/'.$row['urn'],
		'class' => 'event-important',
		'distance_hover' => (isset($row['distance'])&&!empty($row['distance'])?"<br><span style='color:#7FFF00'>".number_format($row['distance'],2)." Miles</span>":""),
        'start' => strtotime($row['start']) . '000',
        'end' => strtotime($row['end']) .'000',
		'endtime' => date('g:i a',strtotime($row['end'])),
		'campaign'=>$row['campaign_name'],
		'company'=>(!empty($row['company'])?"<br>".$row['company']:""),
		'urn'=>$row['urn'],
		'starttime' => date('g:i a',strtotime($row['start'])),
		'attendees' => (isset($row['attendeelist'])?$row['attendeelist']:"<span class='red'>No Attendee!</span>"),
		'status'=>(!empty($row['status'])?"<br><span class='red'>".$row['status']."</span>":""),
		'statusinline'=>(!empty($row['status'])?"<span class='red'>".$row['status']."</span>":""),
		'color'=>$row['color']
    );
}

echo json_encode(array('success' => 1, 'result' => $result));
exit;
		
	}
	
	
	}
	?>