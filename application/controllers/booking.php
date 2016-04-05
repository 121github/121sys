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
			'css'=>array("plugins/fullcalendar-2.6.1/fullcalendar.min.css",      'plugins/bootstrap-toggle/bootstrap-toggle.min.css'),
			'javascript'=>array("plugins/fullcalendar-2.6.1/fullcalendar.min.js",'plugins/bootstrap-toggle/bootstrap-toggle.min.js',"plugins/fullcalendar-2.6.1/gcal.js"));
			
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

	public function google_auth(){
        //get the user access_token
        $google_token = $this->Booking_model->getGoogleToken($_SESSION['user_id'],'google');

        if (isset($google_token[0]['access_token'])) {
            $google_client = new Google_Client;

            $google_client->setAccessToken(json_encode(array(
                "access_token" => $google_token[0]['access_token'],
                "token_type" => $google_token[0]['token_type'],
                "expires_in" => $google_token[0]['expires_in'],
                "id_token" => $google_token[0]['id_token'],
                "created" => $google_token[0]['created'],
            )));

            if($google_client->isAccessTokenExpired()) {
                echo json_encode(array(
                    'success' => false,
                    'accessTokenExpired' => true,
                    'calendars' => array(),
                ));
            }
            else {
                //Get the Calendars
                $service = new Google_Service_Calendar($google_client);
                $calendarList  = $service->calendarList->listCalendarList();

                $calendars = array();
                //Get the events for each calendar
                foreach ($calendarList->getItems() as $calendarListEntry) {
                    $calendars[$calendarListEntry->getSummary()] = array(
                        "id" => $calendarListEntry->id,
                        "name" => $calendarListEntry->getSummary()
                    );
                }
                echo json_encode(array(
                    'success' => true,
                    'accessTokenExpired' => false,
                    'calendars' => $calendars,
                ));
            }
        } else {
            echo json_encode(array(
                'success' => false,
                'accessTokenExpired' => false,
                'calendars' => array()
            ));
        }
	}
	
	public function google_events(){
        //get the user access_token
        $google_token = $this->Booking_model->getGoogleToken($_SESSION['user_id'],'google');
		//get google events and put them into array
        if (isset($google_token[0]['access_token'])) {
			$google_client = new Google_Client;

            $google_client->setAccessToken(json_encode(array(
                "access_token" => $google_token[0]['access_token'],
                "token_type" => $google_token[0]['token_type'],
                "expires_in" => $google_token[0]['expires_in'],
                "id_token" => $google_token[0]['id_token'],
                "created" => $google_token[0]['created'],
            )));

            $calendars_selected = $this->input->post('calendars_selected');
            $calendars_selected = (strlen($calendars_selected) > 0?explode(',',$calendars_selected):array());

			//Get the Calendars
			$service = new Google_Service_Calendar($google_client);
			$calendarList  = $service->calendarList->listCalendarList();

			$gCalEvents = array();
			//Get the events for each calendar
			foreach ($calendarList->getItems() as $calendarListEntry) {
                if (empty($calendars_selected) || in_array($calendarListEntry->id, $calendars_selected)) {
                    $gCalEvents[$calendarListEntry->getSummary()] = array(
                        "id" => $calendarListEntry->id,
                        "name" => $calendarListEntry->getSummary(),
                        "data" => array()
                    );
                    $service = new Google_Service_Calendar($google_client);
                    $optParams = array(
                        //'maxResults' => 10,
                        'orderBy' => 'startTime',
                        'singleEvents' => TRUE,
                        'timeMin' => $this->input->post('start').'T10:07:00Z',
                        'timeMax' => $this->input->post('end').'T10:23:59Z'
                    );
                    $results = $service->events->listEvents($calendarListEntry->id, $optParams);
                    if (count($results->getItems()) > 0) {
                        foreach ($results->getItems() as $event) {
                            array_push($gCalEvents[$calendarListEntry->getSummary()]['data'],$event);
                        }
                    }
                }
			}

			$events = array();
			foreach($gCalEvents as $id => $calendar) {
				foreach ($calendar['data'] as $event) {
					array_push($events, array(
						'id' => $event->id,
						'title' => $event->summary,
						'start' => ($event->getStart()->dateTime?$event->getStart()->dateTime:$event->getStart()->date),
						'end' => ($event->getEnd()->dateTime?$event->getEnd()->dateTime:$event->getEnd()->date),
						'color' => genColorCodeFromText($id)
					));
				}
			}

		} else {
			$events = array();
		}


		echo json_encode($events);
		
	}
	
}