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

	  //this loads the data management view
    public function index()
    {
        $campaigns = $this->Form_model->get_campaigns();
		$users = array();
		if(isset($_SESSION['calendar_filter']['campaign_id'])){
		$users = $this->Form_model->get_campaign_access(false,$_SESSION['calendar_filter']['campaign_id']);
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
                'plugins/calendar/js/calendar.js'
                
            ),
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
		$users = (!empty($_POST['users'])?$_POST['users']:"");
		$campaigns = (!empty($_POST['campaigns'])?$_POST['campaigns']:"");
		$options = array("start"=>$start,"end"=>$end,"users"=>$users,"campaigns"=>$campaigns);
		$result = array();
		$events = $this->Calendar_model->get_events($options);
		
		foreach($events as $row) {
    $result[] = array(
        'id' => $row['appointment_id'],
        'title' => $row['title'],
		'text' => (!empty($row['text'])?$row['text']:""),
        'url' => base_url().'records/detail/'.$row['urn'],
		'class' => 'event-important',
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