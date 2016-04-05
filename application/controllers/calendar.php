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
        check_page_permissions('full calendar');
        $this->_campaigns = campaign_access_dropdown();
        $this->project_version = $this->config->item('project_version');

        $this->load->model('Calendar_model');
        $this->load->model('Form_model');
        $this->load->model('Appointments_model');
        $this->load->model('Booking_model');
    }

    public function switch_view()
    {
        $view = $this->input->post('view');
        $this->db->where("user_id", $_SESSION['user_id']);
        $this->db->update("users", array("calendar" => $view));
    }

    public function get_calendar_users()
    {
        if ($this->input->post('campaigns')) {
            $campaigns = $this->input->post('campaigns');
        } else {
            $campaigns = array();
        }
        $users = $this->Form_model->get_calendar_users($campaigns);
        echo json_encode(array("success" => true, "data" => $users));
    }

    public function get_slots_for_attendee()
    {
        $this->load->model('Admin_model');
        $slots = array();
        $slot_group = $this->Admin_model->get_user_slot_group($this->input->post('id'));
        if (count($slot_group)) {
            $slots = $this->Admin_model->get_slots_in_group($slot_group[0]['slot_group_id']);
        }
        echo json_encode($slots);
    }

    public function google_logout()
    {

    }

    public function check_token()
    {
        $client = new Google_Client();

        //get the user access_token
        $google_token = $this->Booking_model->getGoogleToken($_SESSION['user_id'],'google');
        if (isset($google_token[0]['access_token'])) {
            $access_token = $google_token[0]['access_token'];
            $token = $google_token[0];
            $client->setAccessToken(json_encode($token));

            $response = file_get_contents("https://www.googleapis.com/oauth2/v1/tokeninfo?access_token=" . $access_token);
            $check_token = json_decode($response);

            if (isset($check_token->issued_to)) {
                $this->firephp->log("token valid");
                return $client;
            } else if (isset($check_token->error)) {
                $this->firephp->log("token invalid");
                //unset($_SESSION['api']['google']);
            }

        }
    }

    public function import_google_calendar_events()
    {
        $client = $this->check_token();


    }

    public function google()
    {
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

            $Oauth2 = new Google_Service_Oauth2($google_client);
            $google_email = $Oauth2->userinfo->get()->email;

            //Get the Calendars
            $service = new Google_Service_Calendar($google_client);
            $calendarList  = $service->calendarList->listCalendarList();

            $gCalEvents = array();
            //Get the events for each calendar
            foreach ($calendarList->getItems() as $calendarListEntry) {
                //var_dump($calendarListEntry);
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
                    'timeMin' => date('c'),
                );
                $results = $service->events->listEvents($calendarListEntry->id, $optParams);
                if (count($results->getItems()) > 0) {
                    foreach ($results->getItems() as $event) {
                        $event['start'] = $event->getStart()->dateTime;
                        array_push($gCalEvents[$calendarListEntry->getSummary()]['data'],$event);
                    }
                }
            }

            $gCalEvents = json_decode(json_encode($gCalEvents), true);
            foreach ($gCalEvents as $calendar) {
                var_dump($calendar);
            }

        } else {
            $google_email = false;
        }


        $data = array(
            'campaign_access' => $this->_campaigns,
            'title' => 'Dashboard | Calendar',
            'page' => 'data',
            'google' => $google_email);

        $this->template->load('default', 'calendar/google_cal.php', $data);
    }


    //this loads the data management view
    public function index()
    {
        if (!isset($_SESSION['calendar-filter'])) {
            $_SESSION['calendar-filter']['distance'] = 1500;
            if (isset($_COOKIE['current_postcode'])) {
                $_SESSION['calendar-filter']['postcode'] = $_COOKIE['current_postcode'];
            }
        }

        $users = array();
        if (in_array("mix campaigns", $_SESSION['permissions'])) {
            $campaigns = $this->Form_model->get_calendar_campaigns();
            $users = isset($_SESSION['current_campaign']) ? $this->Form_model->get_calendar_users(array($_SESSION['current_campaign'])) : "";
            $disable_campaign_filter = false;
        }
        if (!in_array("mix campaigns", $_SESSION['permissions'])) {
            $users = isset($_SESSION['current_campaign']) ? $this->Form_model->get_calendar_users(array($_SESSION['current_campaign'])) : "";
            $campaigns = $this->Form_model->get_calendar_campaigns();
            $disable_campaign_filter = true;
        }
        if ($disable_campaign_filter && !isset($_SESSION['current_campaign'])) {
            redirect('error/calendar');
        }
        //get the calendar view preference
        $calendar_view = $this->db->get_where("users", array("user_id" => $_SESSION['user_id']))->row()->calendar;

        //Get the campaign_triggers if exists
        $campaign_triggers = array();
        if (isset($_SESSION['current_campaign'])) {
            $campaign_triggers = $this->Form_model->get_campaign_triggers_by_campaign_id($_SESSION['current_campaign']);
        }

        $data = array(
            'campaign_access' => $this->_campaigns,
            'campaign_triggers' => $campaign_triggers,
            'title' => 'Dashboard | Calendar',
            'page' => 'Calendar',
            'calendar_view' => $calendar_view == "1" ? "combined" : "seperate",
            'javascript' => array(
                'lib/underscore.js',
                'plugins/calendar/js/calendar.js?v' . $this->project_version,
                'calendar.js?v' . $this->project_version,
                'location.js?v' . $this->project_version,
                'import.js?v' . $this->project_version,
                'plugins/jqfileupload/jquery.fileupload.js',
                'plugins/jqfileupload/jquery.fileupload-process.js',
                'plugins/jqfileupload/jquery.fileupload-validate.js',
                'lib/jquery.numeric.min.js',
                'plugins/bootstrap-toggle/bootstrap-toggle.min.js'
            ),
            'disable_campaign' => $disable_campaign_filter,
            'date' => date('Y-m-d'),
            'campaigns' => $campaigns,
            'users' => $users,
            'css' => array(
                'calendar.css',
                'plugins/jqfileupload/jquery.fileupload.css',
                'plugins/bootstrap-toggle/bootstrap-toggle.min.css',
            )
        );
        $this->template->load('default', 'calendar/view.php', $data);
    }

    public function get_demo()
    {
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

    public function suggested_slots()
    {
        /* not using this its not complete
        $postcode = $this->input->post('postcode');
        if($this->input->post('urn')){
        $postcode = $this->Calendar_model->get_postcode_from_urn($this->input->post('urn'));
        }
        $postcode = postcodeCheckFormat($postcode);
        if($postcode==NULL){
        $postcode = (isset($_SESSION['postcode'])?$_SESSION['postcode']:"");
        }
        */
    }


    public function get_events()
    {
        $counts = array();
        $postcode = $this->input->post('postcode');
        if ($this->input->post('urn') && !$this->input->post('postcode')) {
            $postcode = $this->Calendar_model->get_postcode_from_urn($this->input->post('urn'));
        }
        if (isset($_POST['startDate'])) {
            $start = !empty($_POST['startDate']) ? date('Y-m-d H:i:s', ($_POST['startDate'] / 1000)) : date('Y-m-d h:i:s');
            $end = !empty($_POST['endDate']) ? date('Y-m-d H:i:s', ($_POST['endDate'] / 1000)) : date('2040-m-d h:i:s');
        } else {
            $start = "";
            $end = "";
        }
        if (isset($_POST['users'])) {
            $users = $_POST['users'];
        } else {
            $users = (isset($_SESSION['calendar-filter']['users']) ? $_SESSION['calendar-filter']['users'] : "");
        }

        if (!empty($postcode) && !empty($_POST['distance'])) {
            $postcode = postcodeCheckFormat($postcode);
            if ($postcode == NULL && !empty($_POST['distance'])) {
                echo json_encode(array("error" => "Postcode", "msg" => "Postcode is not valid"));
                exit;
            }
        }
        if (!empty($_POST['distance'])) {
            $distance = $_POST['distance'];
        } else {
            $distance = (isset($_SESSION['calendar-filter']['distance']) ? $_SESSION['calendar-filter']['distance'] : "1500");
        }
        if (isset($_POST['campaigns'])) {
            $campaigns = $_POST['campaigns'];
        } else {
            $campaigns = (isset($_SESSION['calendar-filter']['campaigns']) ? $_SESSION['calendar-filter']['campaigns'] : "");
        }

        $options = array("start" => $start, "end" => $end, "users" => $users, "campaigns" => $campaigns, "postcode" => $postcode, "distance" => $distance);
        $_SESSION['calendar-filter'] = $options;
        $result = array();
        if (isset($_POST['modal'])) {
            $options['modal'] = "list";
        }
        $events = $this->Calendar_model->get_events($options);

        $appointment_rules = $this->Calendar_model->get_appointment_overrides(false, $users);
        $aux = array();
        foreach ($appointment_rules as $rule) {
            if (!isset($aux[$rule['block_day']])) {
                $aux[$rule['block_day']] = array();
            }
            array_push($aux[$rule['block_day']], $rule);
        }
        $appointment_rules = $aux;

        if (isset($_POST['modal'])) {
            foreach ($events as $k => $row) {
                $date = date('Y-m-d', strtotime($row['start']));
                $result[$date]['dayEvents'][] = array("postcode" => $row['postcode'], "title" => $row['title'], 'endtime' => date('g:i a', strtotime($row['end'])), 'starttime' => date('g:i a', strtotime($row['start'])), 'distance' => isset($row['distance']) ? number_format($row['distance'], 1) : "", "attendees" => $row['attendeelist']);
                $result[$date]['number'] = (isset($result[$date]['number']) ? $result[$date]['number'] + 1 : 1);
                $result[$date]['app_count'] = $result[$date]['number'];
            }

        } else {
            foreach ($events as $row) {
                $tooltip = $row['appointment_type'] . ($row['appointment_type'] == "Telephone" ? " appointment" : "") . " with " . $row['contact'] . ($row['company'] ? " from " . $row['company'] : "");
                if (empty($row['icon'])) {
                    $row['icon'] = "fa fa-circle";
                }
                $text = explode("<br>", $row['text']);
                $result[] = array(
                    'id' => $row['appointment_id'],
                    //'title' => $row['title'],
                    'tooltip' => $tooltip,
                    //'text' => (!empty($text[0]) ? $text[0] : ""),
                    'url' => base_url() . 'records/detail/' . $row['urn'],
                    'class' => 'event-important',
                    'address_icon' => (isset($row['address']) && !empty($row['address']) ? "<span class='glyphicon glyphicon-map-marker'></span>" : ""),
                    'postcode' => $row['postcode'],
                    'address' => $row['address'],
                    'access_address_icon' => (isset($row['access_address']) && !empty($row['access_address']) ? "<span class='glyphicon glyphicon-record'></span>" : ""),
                    'access_postcode' => $row['access_postcode'],
                    'access_address' => "Access Address: " . (isset($row['access_address']) && !empty($row['access_address']) ? $row['access_address'] : "none"),
                    'distance_hover' => (isset($row['distance']) && !empty($row['distance']) ? "<br><span style='color:#7FFF00'>" . number_format($row['distance'], 2) . " Miles</span>" : ""),
                    'distance' => (isset($row['distance']) && $row['appointment_type_id'] !== "2" && !empty($row['distance']) ? number_format($row['distance'], 1) . " Miles" : ""),
                    'start' => strtotime($row['start']) . '000',
                    'end' => strtotime($row['end']) . '000',
                    'starttime' => date('H:i', strtotime($row['start'])),
                    'endtime' => date('H:i', strtotime($row['end'])),
                    'campaign' => $row['campaign_name'],
                    'company' => (!empty($row['company']) ? "<br>Company: " . $row['company'] : ""),
                    'contact' => (!empty($row['contact']) ? "<br>Contact: " . $row['contact'] : ""),
                    'urn' => $row['urn'],
                    'attendees' => (isset($row['attendeelist']) ? $row['attendeelist'] : "<span class='red'>No Attendee!</span>"),
                    'appointment_type' => $row['appointment_type'],
                    //'status' => (!empty($row['status']) ? "<br>Status: <span class='red'>" . $row['status'] . "</span>" : ""),
                    //'statusinline' => (!empty($row['status']) ? "<span class='red'>" . $row['status'] . "</span>" : ""),
                    'color' => $row['color'],
                    'startDate' => date('d/m/Y', strtotime($row['start'])),
                    'endDate' => date('d/m/Y', strtotime($row['end'])),
                    'date' => $row['date'],
                    'icon' => $row['icon']
                );
                if (isset($counts[$row['date']]['apps'])) {
                    $counts[$row['date']]['apps']++;
                } else {
                    $counts[$row['date']]['apps'] = 1;
                }
            }
        }
        if (!isset($_POST['modal'])) {
            foreach ($result as $k => $v) {
                $result[$k]['app_count'] = $counts[$v['date']]['apps'];
            }
        }
        echo json_encode(array('success' => 1, 'result' => $result, 'postcode' => $postcode, 'date' => date('Y-m'), 'rules' => $appointment_rules));
        exit;

    }

    /**
     * Add new appointment rule
     */
    public function add_appointment_rule()
    {
        if ($this->input->is_ajax_request()) {
            if (!$this->input->post('appointment_slot_id')) {
                echo json_encode(array(
                    "success" => false,
                    "msg" => "Select the slots to block"
                ));
                exit(0);
            }


            $form = $this->input->post();
            $form['block_day'] = to_mysql_datetime($form['block_day']);
            $form['block_day_end'] = to_mysql_datetime($form['block_day_end']);

            $days_diff = (strtotime($form['block_day_end']) - strtotime($form['block_day'])) / (60 * 60 * 24);
            unset($form['block_day_end']);
            $block_day_start = $form['block_day'];

            $rules_to_add = array();
            for ($i = 0; $i <= $days_diff; $i++) {
                $block_day = date("Y-m-d", strtotime($block_day_start . "+ " . $i . " days"));
                $form['block_day'] = $block_day;

                //Check if the attendee already has an appointment where the block day is between the start and the end date schedulled
                if (count($form['appointment_slot_id']) == 0) {
                    $form['appointment_slot_id'][] = 0;
                }
                foreach ($form['appointment_slot_id'] as $slot_id) {
                    if ($slot_id == 0) {
                        $slot_id = NULL;
                    }
                    if ($this->Appointments_model->checkNoAppointmentForTheDayBlocked($form['user_id'], $form['block_day'], $slot_id)) {
                        echo json_encode(array(
                            "success" => false,
                            "msg" => "ERROR: The attendee has at least one appointment scheduled on the day selected. Reschedule the appointment and block the day after that."
                        ));
                        exit(0);
                    } else {
                        //Check if already exist a rule for that day
                        $appointment_rules = $this->Calendar_model->get_appointment_rules_by_date_and_user($form['block_day'], $form['user_id']);
                        foreach ($appointment_rules as $appointment_rule) {
                            //If appointment_slot is set
                            if ($slot_id) {
                                //If all day is already set, delete it to insert the new one with a slot
                                if (!$appointment_rule['appointment_slot_id']) {
                                    $this->Calendar_model->delete_appointment_rule($appointment_rule['appointment_rules_id']);
                                } //If is the same appointment_slot, delete it to insert the new one on the same slot
                                else if ($slot_id == $appointment_rule['appointment_slot_id']) {
                                    $this->Calendar_model->delete_appointment_rule($appointment_rule['appointment_rules_id']);
                                }
                            } //If all day is set, delete all that are already set for this day to insert the new one for the whole day
                            else {
                                $this->Calendar_model->delete_appointment_rule($appointment_rule['appointment_rules_id']);
                            }
                        }
                        $array = array("block_day" => $form['block_day'], "user_id" => $form['user_id'], "appointment_slot_id" => $slot_id, "reason_id" => $form['reason_id'], "other_reason" => $form['other_reason']);
                        array_push($rules_to_add, $array);
                    }
                }
            }
            if (!empty($rules_to_add)) {
                //Add the appointment rules got
                foreach ($rules_to_add as $rule) {
                    $results = $this->Calendar_model->add_appointment_rule($rule);
                }
                echo json_encode(array(
                    "success" => (!empty($results)),
                    "msg" => (!empty($results)) ? "Appointment Rules added successfully" : "ERROR: Appointment Rules NOT added successfully!"
                ));
            }
        }
    }

    /**
     * Delete an appointment rule
     */
    public function delete_appointment_rule()
    {
        if ($this->input->is_ajax_request()) {
            $form = $this->input->post();

            $results = $this->Calendar_model->delete_appointment_rule($form['appointment_rules_id']);

            echo json_encode(array(
                "success" => (!empty($results)),
                "msg" => (!empty($results)) ? "Appointment Rules removed successfully" : "ERROR: Appointment Rules NOT removed successfully!"
            ));
        }
    }

    /**
     * Get appointment rules
     */
    public function get_appointment_rules()
    {
        if ($this->input->is_ajax_request()) {
						if($this->uri->segment(3)=="by_user"){
				$distinct_user = true;
			} else {
				$distinct_user = false;
			}
			$users = $this->input->post('users');
			
            //$appointment_rules = $this->Calendar_model->get_appointment_rules($distinct_user);
            $appointment_rules = $this->Calendar_model->get_appointment_overrides($distinct_user, $users);
            $aux = array();
            foreach ($appointment_rules as $rule) {
                if (!isset($aux[$rule['block_day']])) {
                    $aux[$rule['block_day']] = array();
                }
                array_push($aux[$rule['block_day']], $rule);
            }
            $appointment_rules = $aux;
            echo json_encode(array(
                "success" => (!empty($appointment_rules)),
                "data" => $appointment_rules
            ));
        }


    }

    /**
     * Get appointment rules by date
     */
    public function get_appointment_rules_by_date()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post('date') && $this->input->post('date') !== "false") {
                $date = to_mysql_datetime($this->input->post('date'));
            } else {
                $date = false;
            }
            $appointment_rules = $this->Calendar_model->get_appointment_rules_by_date($date);

            echo json_encode(array(
                "success" => (!empty($appointment_rules)),
                "data" => $appointment_rules
            ));
        }

    }

    /**
     * Get appointment rule reasons
     */
    public function get_appointment_rule_reasons()
    {
        if ($this->input->is_ajax_request()) {
            $appointment_rule_reasons = $this->Form_model->get_appointment_rule_reasons();

            echo json_encode(array(
                "data" => $appointment_rule_reasons
            ));
        }

    }

    /**
     * Get appointment slots
     */
    public function get_appointment_slots()
    {
        if ($this->input->is_ajax_request()) {
            $get_appointment_slots = $this->Form_model->get_appointment_slots();

            echo json_encode(array(
                "data" => $get_appointment_slots
            ));
        }

    }

}

?>
