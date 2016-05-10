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
        $this->load->model('Contacts_model');
        $this->load->model('Appointments_model');
        $this->load->model('Form_model');
        $this->load->model('Records_model');
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
	
	
	public function week(){
	$this->index("agendaWeek");	
	}
	public function day(){
	$this->index("agendaDay");	
	}
	public function month(){
	$this->index("month");	
	}
	
	public function index($view=false){
		if(!$view){
			$view = "agendaWeek";
		}
        //Get the campaign_triggers if exists
        $campaign_triggers = array();
        if(isset($_SESSION['current_campaign'])) {
            if ($_SESSION['current_campaign']) {
                $campaign_triggers = $this->Form_model->get_campaign_triggers_by_campaign_id($_SESSION['current_campaign']);
            }
        }

		$data = array(
            'campaign_access' => $this->_campaigns,
            'title' => 'Bookings',
			'view' =>$view,
            'page' => 'Bookings',
            "campaign_triggers" => $campaign_triggers,
			'css'=>array("plugins/fullcalendar-2.6.1/fullcalendar.min.css",      'plugins/bootstrap-toggle/bootstrap-toggle.min.css','plugins/jQuery-contextMenu-master/dist/jquery.contextMenu.min.css'),
			'javascript'=>array("plugins/fullcalendar-2.6.1/fullcalendar.min.js",'plugins/bootstrap-toggle/bootstrap-toggle.min.js',"plugins/fullcalendar-2.6.1/gcal.js","plugins/jQuery-contextMenu-master/dist/jquery.contextMenu.min.js",
			'booking.js?v' . $this->project_version));
			
		 $this->template->load('default', 'bookings/booking.php', $data);	
	}
	
	public function events(){
		session_write_close();
		$start = $this->input->post('start');
		$end = $this->input->post('end');
		if(!isset($_POST['attendee'])){
		$users = $this->Form_model->get_calendar_users();
		foreach($users as $row){
		if($_SESSION['user_id']==$row['id']){
			$attendee = $_SESSION['user_id'];
		}
		}
		} else {
		$attendee = $this->input->post('attendee');
		}
		
		if(in_array("own appointments",$_SESSION['permissions'])){
		$attendee = $_SESSION['user_id'];
		}
		$pc = validate_postcode($this->input->post('postcode'));
		$postcode = $pc?$postcode:false;
		$appointment_type = $this->input->post('appointment_type');
        $status = $this->input->post('status');
		$events = $this->Booking_model->get_events($start,$end,$attendee,$status,$appointment_type,$postcode);
		foreach($events as $k => $event){
			$events[$k]['color'] = genColorCodeFromText($event['attendee_names']);
			if($postcode){
			$events[$k]['distance'] = number_format($event['distance']). " miles away";
			}
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

            if ($google_client->isAccessTokenExpired()) {
                $google_client = $this->refreshToken($google_token[0]);
            }

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
                        'timeMin' => $this->input->post('start').'T07:00:00Z',
                        'timeMax' => $this->input->post('end').'T23:59:59Z'
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
                        'description' => $event->description,
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
            $description = $this->input->post("description");
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
                        'description' => ($description?$description:$data['text']),
                        'status' => ($event_status?$event_status:"confirmed"),
                        'start' => array(
                            'dateTime' => $start_date->format('Y-m-d\TH:i:sO'),
                        ),
                        'end' => array(
                            'dateTime' => $end_date->format('Y-m-d\TH:i:sO'),
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

                    $google_event_id = $this->Booking_model->getGoogleEventId($appointment_id);
                    $event->setId(($google_event_id?$google_event_id:"appointment".$appointment_id."attendee".$attendee));


                    $calendar = $this->Booking_model->get_google_calendars_by_user_and_campaign($attendee, $data['campaign_id']);
                    $calendarId = (!empty($calendar)?$calendar['calendar_id']:'primary');

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

                            $google_event_id = $this->Booking_model->saveGoogleEventId($appointment_id, $event->getId());

                            break;

                        } catch (Google_Exception $e) {
                            //If the event doesn't exist, and is the last calendar checked on the list insert a new one
                            if ($calendar == end($calendarList)) {
                                $result = $service->events->insert($calendarId, $event);
                                //Save the google_event_id on the appointment
                                $google_event_id = $this->Booking_model->saveGoogleEventId($appointment_id, $event->getId());
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
                "msg_title" => "Google Calendar",
                "msg" => (!empty($msg)?$msg:"No events added/updated on google calendar"),
            ));
        } else {
            echo "Denied";
            exit;
        }
    }

    public function get_google_data() {
        $user_id = $this->input->post("user_id");
        $userInfo = array();
        $calendarList = array();

        $google_token = $this->Booking_model->getGoogleToken($user_id,'google');
        if (isset($google_token[0]['access_token'])) {
            $google_client = new Google_Client;

            $google_client->setAccessToken(json_encode(array(
                "access_token" => $google_token[0]['access_token'],
                "token_type" => $google_token[0]['token_type'],
                "expires_in" => $google_token[0]['expires_in'],
                "id_token" => $google_token[0]['id_token'],
                "created" => $google_token[0]['created']
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
        }

        echo json_encode(array(
            'success' => (!empty($userInfo)),
            'userInfo' => $userInfo,
            'calendarList' => $calendarList
        ));
    }

    public function set_google_calendar() {
        $form = $this->input->post();
        $data = $form;

        unset($form['campaign_name']);

        $result = $this->Booking_model->set_google_calendar($form);
        $data['id'] = $result;

        echo json_encode(array(
            'success' => ($result!=0),
            'data' => $data,
            'msg' => ($result?"Calendar saved successfully!":"ERROR: The calendar was not saved for this user!")
        ));
    }

    public function remove_google_calendar() {
        $id = $this->input->post('id');

        $result = $this->Booking_model->remove_google_calendar($id);

        echo json_encode(array(
            'success' => (!empty($result)),
            'msg' => (!empty($result)?"Calendar removed successfully!":"ERROR: The calendar was not removed from this user!")
        ));
    }

    public function get_google_calendars_by_user() {
        $form = $this->input->post();

        $result = $this->Booking_model->get_google_calendars_by_user($form['user_id']);

        echo json_encode(array(
            'success' => (!empty($result)),
            'data' => $result
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


    public function sync_google_cal() {
        $user_id = $this->input->post("user_id");
        $google_calendar_id = $this->input->post("google_calendar_id");
        $google_calendar = $this->Booking_model->getGoogleCalendar($google_calendar_id);

        //get the user access_token
        $google_token = $this->Booking_model->getGoogleToken($user_id,'google');
        //get google events and put them into array
        if (isset($google_token[0]['access_token'])) {
            $google_client = new Google_Client;

            $google_client->setAccessToken(json_encode(array(
                "access_token" => $google_token[0]['access_token'],
                "token_type" => $google_token[0]['token_type'],
                "expires_in" => $google_token[0]['expires_in'],
                "id_token" => $google_token[0]['id_token'],
                "created" => $google_token[0]['create
                d'],
            )));

            if ($google_client->isAccessTokenExpired()) {
                $google_client = $this->refreshToken($google_token[0]);
            }

            $calendars_selected = ((!empty($google_calendar))?$google_calendar['calendar_id']:'primary');

            //Get the Calendars
            $service = new Google_Service_Calendar($google_client);
            $calendar = $service->calendars->get($calendars_selected);

            $optParams = array(
                'orderBy' => 'startTime',
                'singleEvents' => TRUE,
                //Get the events since the previous month
                'timeMin' => date("Y-m-d", strtotime("first day of previous month")).'T07:00:00Z'
            );
            $events = $service->events->listEvents($calendar->id, $optParams);


            $appointments = array(
                "added" => array(),
                "updated" => array()
            );
            foreach ($events as $event) {
				$postcode = postcode_from_string($event->summary);
				if(empty($postcode)){
				$postcode = postcode_from_string($event->description);
				}
                $data = array(
                    'google_id' => $event->id,
                    'title' => $event->summary,
                    'text' => ($event->description?$event->description:''),
                    'start' => ($event->getStart()->dateTime?$event->getStart()->dateTime:$event->getStart()->date),
                    'end' => ($event->getEnd()->dateTime?$event->getEnd()->dateTime:$event->getEnd()->date),
                    'address' => $event->location,
                    'attendees' => array($user_id),
                    'contact_id' => 'other'
                );
					if(empty($postcode)){
						$data['postcode'] = $postcode;
					}
                //Check if the appointment already exist on the system
                $appointment = $this->Booking_model->get_appointments_by_google_id($event->id);

                if (!empty($appointment)) {
                    //Update the appointments to 121system
                    $data['appointment_id'] = $appointment['appointment_id'];
                    $data['urn'] = $appointment['urn'];
					unset($data['text']);
                    $appointment_id = $this->Records_model->save_appointment($data);
                    array_push($appointments['updated'], $appointment_id);
                }
                else {
					//get the campaign for the new record
				$this->db->where("user_id",$user_id);
				$campaign_id = $this->db->get("google_calendar")->row()->campaign_id;
                    //Create record
                    $urn = $this->Records_model->save_record(array(
                        "campaign_id" => $campaign_id
                    ));
                    //Add a contact with no name
                    $contact_id = $this->Contacts_model->save_contact(array(
                        "urn" => $urn,
                        "fullname" => ""
                    ));

                    //Add the appointments to 121system
                    $data['urn'] = $urn;
                    $appointment_id = $this->Records_model->save_appointment($data);
                    array_push($appointments['added'], $appointment_id);
                }
            }

        } else {
            $appointments = array();
        }

        echo json_encode($appointments);
    }

    public function load_add_google_calendar_form()
    {

        $user_id = $this->input->post("user_id");
        $campaign_id = $this->input->post("campaign_id");
        $data = array();

        $google_token = $this->Booking_model->getGoogleToken($user_id,'google');
        if (isset($google_token[0]['access_token'])) {
            $google_client = new Google_Client;

            $google_client->setAccessToken(json_encode(array(
                "access_token" => $google_token[0]['access_token'],
                "token_type" => $google_token[0]['token_type'],
                "expires_in" => $google_token[0]['expires_in'],
                "id_token" => $google_token[0]['id_token'],
                "created" => $google_token[0]['created']
            )));

            if ($google_client->isAccessTokenExpired()) {
                $google_client = $this->refreshToken($google_token[0]);
            }

            $calendarList = array();

            if ($campaign_id) {
                //Get the calendars
                $service = new Google_Service_Calendar($google_client);
                $calendarList  = $service->calendarList->listCalendarList()->getItems();

                $aux = array();
                $calendars_selected = $this->Booking_model->get_google_calendars_selected_by_campaign($campaign_id);
                foreach ($calendars_selected as $calendar) {
                    array_push($aux, $calendar['calendar_id']);
                }
                $calendars_selected = $aux;

                $aux = array();
                foreach ($calendarList as $calendar) {
                    array_push($aux, array(
                        'id' => $calendar->id,
                        'name' => $calendar->summary,
                        'accessRole' => $calendar->accessRole,
                        'selected' => (in_array($calendar->id,$calendars_selected))
                    ));
                }
                $calendarList = $aux;
            }

            $userCalendars = $this->Booking_model->get_google_calendars_by_user($user_id);
            $campaigns = $this->Form_model->get_campaigns();

            $data = array(
                "campaigns"=>$campaigns,
                "calendars"=>$calendarList,
                "userCalendars"=>$userCalendars,
                "user_id" => $user_id,
                "api_id" => $google_token[0]['api_id']
            );
        }

        if ($this->input->is_ajax_request()) {
            switch($this->input->post("format")) {
                case "html":
                    $this->load->view('forms/add_google_calendar_form.php', $data);
                    break;
                case "json":
                    echo json_encode($data);
                    break;
                default:
                    $this->load->view('forms/add_google_calendar_form.php', $data);
                    break;
            }

        }
    }
}