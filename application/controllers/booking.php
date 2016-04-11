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
        $this->load->model('Appointments_model');
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
			'javascript'=>array("plugins/fullcalendar-2.6.1/fullcalendar.min.js",'plugins/bootstrap-toggle/bootstrap-toggle.min.js',"plugins/fullcalendar-2.6.1/gcal.js","plugins/jQuery-contextMenu-master/dist/jquery.contextMenu.min.js",
			'booking.js?v' . $this->project_version));
			
		 $this->template->load('default', 'bookings/booking.php', $data);	
	}
	
	public function events(){
		$start = $this->input->get('start');
		$end = $this->input->get('end');
		$attendee = $this->input->post('attendee');
        $status = $this->input->post('status');
		$events = $this->Booking_model->get_events($start,$end,$attendee,$status);
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
		echo json_encode(array(
            "success"=>true
        ));
	}
	
	public function google(){
        $data = array(
            'campaign_access' => $this->_campaigns,
            'title' => 'Bookings',
            'page' => 'Bookings',
            'css'=>array(
                "plugins/fullcalendar-2.6.1/fullcalendar.min.css",
                'plugins/bootstrap-toggle/bootstrap-toggle.min.css',
                'bootstrap-datetimepicker.css'
            ),
			'javascript'=>array(
                "plugins/fullcalendar-2.6.1/fullcalendar.min.js",
                'plugins/bootstrap-toggle/bootstrap-toggle.min.js',
                "plugins/fullcalendar-2.6.1/gcal.js",
                'lib/bootstrap-datetimepicker.js'
            ));
			
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
						'color' => genColorCodeFromText($id),
                        'url' => $event->htmlLink,
					));
				}
			}

		} else {
			$events = array();
		}


		echo json_encode($events);
		
	}
	
	public function get_appointment_rules_by_date(){
		  $appointment_rules = $this->Booking_model->get_appointment_rules_by_date_and_user($this->input->post('date'), $this->input->post('user_id'));
		  echo json_encode(array("data"=>$appointment_rules));
	}


    public function add_google_event() {
        if ($this->input->is_ajax_request()) {
            $appointment_id = $this->input->post("appointment_id");
            $data = $this->input->post("data");
            $event_status = $this->input->post("event_status");
            if (!($data)) {
                //Get the appointment data
                $data = $this->Appointments_model->get_appointment($appointment_id);
                $data['attendees'] = explode(";",$data['attendees']);
            }
            $msg = array();
            foreach ($data['attendees'] as $attendee) {
                //get the user access_token
                $google_token = $this->Booking_model->getGoogleToken($attendee,'google');
                if (isset($google_token[0]['access_token'])) {
                    $google_client = new Google_Client;

                    $google_client->setAccessToken(json_encode(array(
                        "access_token" => $google_token[0]['access_token'],
                        "token_type" => $google_token[0]['token_type'],
                        "expires_in" => $google_token[0]['expires_in'],
                        "id_token" => $google_token[0]['id_token'],
                        "created" => $google_token[0]['created'],
                    )));

                    if ($google_client->isAccessTokenExpired()) {
                        $google_client = $this->refreshToken($google_token[0]);
                    }

                    //Add event
                    $start_date = new Datetime($data['start']);
                    $end_date = new Datetime($data['end']);
                    $event = new Google_Service_Calendar_Event(array(
                        'summary' => $data['title'],
                        'location' => $data['postcode'],
                        'description' => $data['text'],
                        'status' => ($event_status?$event_status:"confirmed"),
                        'start' => array(
                            'dateTime' => $start_date->format('Y-m-d\TH:i:s\Z'),
                        ),
                        'end' => array(
                            'dateTime' => $end_date->format('Y-m-d\TH:i:s\Z'),
                        ),
//                        'recurrence' => array(
//                            'RRULE:FREQ=DAILY;COUNT=2'
//                        ),
//                        'attendees' => array(
//                            array('email' => 'lpage@example.com'),
//                            array('email' => 'sbrin@example.com'),
//                        ),
                        'reminders' => array(
                            'useDefault' => FALSE,
                            'overrides' => array(
                                array('method' => 'email', 'minutes' => 24 * 60),
                                array('method' => 'popup', 'minutes' => 10),
                            ),
                        )
                    ));

                    $event->setId("appointment".$appointment_id."attendee".$attendee);

                    $calendarId = ($google_token[0]['calendar_id']?$google_token[0]['calendar_id']:'primary');

                    $service = new Google_Service_Calendar($google_client);

                    //Check if the event already exists in all the calendars
                    //Get the calendars
                    $calService = new Google_Service_Calendar($google_client);
                    $calendarList  = $calService->calendarList->listCalendarList()->getItems();
                    foreach ($calendarList as $calendar) {
                        try {
                            //The event exists
                            $ev = $service->events->get($calendar->id, $event->getId());
                            //If the event exists update the event and move to a new calendar if it is needed
                            $result = $service->events->move($calendar->id, $event->getId(), $calendarId);
                            $result = $service->events->update($calendarId, $event->getId(), $event);
                            if ($event_status && $event_status == 'cancelled') {
                                array_push($msg,"Event ".$event->getId()." cancelled on google calendar on the calendar ".$calendarId);
                            }
                            else {
                                array_push($msg,"Event ".$event->getId()." updated on google calendar to the calendar ".$calendarId);
                            }

                            break;

                        } catch (Google_Exception $e) {
                            //If the event doesn't exist, and is the last calendar checked on the list insert a new one
                            if ($calendar == end($calendarList)) {
                                $result = $service->events->insert($calendarId, $event);
                                array_push($msg,"Event ".$event->getId()." added on google calendar on the calendar ".$calendarId);
                                break;
                            }
                        }
                    }
                }
            }
            //return success to page
            echo json_encode(array(
                "success" => !empty($msg),
                "msg" => (!empty($msg)?$msg:"No events added/updated on google calendar"),
            ));
        } else {
            echo "Denied";
            exit;
        }
    }

    public function get_google_data() {
        $google_token = $this->input->post("google_token");

        $google_client = new Google_Client;

        $google_client->setAccessToken(json_encode(array(
            "access_token" => $google_token['access_token'],
            "token_type" => $google_token['token_type'],
            "expires_in" => $google_token['expires_in'],
            "id_token" => $google_token['id_token'],
            "created" => $google_token['created'],
        )));

        if ($google_client->isAccessTokenExpired()) {
            $google_client = $this->refreshToken($google_token[0]);
        }

        //Get the user info
        $service = new Google_Service_Oauth2($google_client);
        $userInfo = $service->userinfo->get();

        //Get the calendars
        $service = new Google_Service_Calendar($google_client);
        $calendarList  = $service->calendarList->listCalendarList()->getItems();

        $aux = array();
        foreach ($calendarList as $calendar) {
            array_push($aux, array(
                'id' => $calendar->id,
                'name' => $calendar->summary,
                'accessRole' => $calendar->accessRole
            ));
        }
        $calendarList = $aux;

        echo json_encode(array(
            'success' => (!empty($userInfo)),
            'userInfo' => $userInfo,
            'calendarList' => $calendarList
        ));
    }

    public function set_google_calendar() {
        $form = $this->input->post();
        $calendar_id = ($form["calendar_id"]?$form["calendar_id"]:NULL);
        $user_id = (isset($form["user_id"])?$this->input->post("user_id"):$_SESSION['user_id']);

        $result = $this->Booking_model->set_google_calendar($user_id, $calendar_id);

        echo json_encode(array(
            'success' => $result,
            'msg' => ($result?"Calendar saved successfully!":"ERROR: The calendar was not saved for this user!")
        ));
    }

    public function refreshToken($token) {
        define('APPLICATION_NAME', 'Google Calendar API PHP Quickstart');
        define('CREDENTIALS_PATH', '.credentials/calendar-php-quickstart.json');
        define('CLIENT_SECRET_PATH','client_secret.json');
        // If modifying these scopes, delete your previously saved credentials
        // at ~/.credentials/calendar-php-quickstart.json
        define('SCOPES', implode(' ', array(
                Google_Service_Calendar::CALENDAR,
                'https://www.googleapis.com/auth/userinfo.email'
            )
        ));

        $client = new Google_Client();
        $client->setApplicationName(APPLICATION_NAME);
        $client->setScopes(SCOPES);
        $client->setAuthConfigFile(CLIENT_SECRET_PATH);
        $client->setPrompt("consent");
        $client->setAccessType('offline');

        $client->refreshToken($token['refresh_token']);

        $token = json_decode($client->getAccessToken(), true);

        $data = array(
            "api_name" => "google",
            "access_token" => $token['access_token'],
            "expires_in" => $token['expires_in'],
            "id_token" => $token['id_token'],
            "created" => $token['created'],
            "user_id" => $_SESSION['user_id'],
            "date_added" => date('Y-m-d H:i:s')
        );

        $this->db->where(array("user_id" => $_SESSION['user_id'], "api_name" => "google"));
        $this->db->insert_update("apis", $data);

        return $client;
    }
}