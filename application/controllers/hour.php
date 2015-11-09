<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Hour extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        user_auth_check(false);
		$this->_campaigns = campaign_access_dropdown();
        $this->project_version = $this->config->item('project_version');

        $this->load->model('Form_model');
        $this->load->model('Filter_model');
        $this->load->model('Hour_model');
        $this->load->model('Time_model');
        $this->load->model('User_model');
    }

    /* hours page functions */
    public function hours()
    {
		$this->load->model('Cron_model');
		$teams = $this->Form_model->get_teams();
    	$campaigns = $this->Form_model->get_campaigns();
    	$agents = $this->Form_model->get_agents();
    	$this->Cron_model->update_hours($agents);
    	$data     = array(
    			'campaign_access' => $this->_campaigns,

    			'pageId' => 'Admin',
    			'title' => 'Admin | Hours',
    			'page' =>  'agent_hours',
    			'javascript' => array(
                    'admin/hours.js?v' . $this->project_version,
    					'lib/moment.js',
    					'lib/jquery.numeric.min.js',
    					'lib/daterangepicker.js',
    			),
    			'css' => array(
    					'dashboard.css',
                		'daterangepicker-bs3.css'
    			),
    			'campaigns' => $campaigns,
          		'agents' => $agents,
				'team_managers'=>$teams
    	);
    	$this->template->load('default', 'admin/hours.php', $data);
    }
    
    /**
     * Get the hours in a date range
     * 
     * POST
     * 
     */
    public function get_hours_data()
    {
    	if ($this->input->is_ajax_request()) {
            $post = $this->input->post();
            $date_from = $post['date_from'];
            $date_to = $post['date_to'];

            $result = array();
            if ($date_from && $date_to) {
            	$totalDays = floor(((strtotime($date_to) - strtotime($date_from)) / (60 * 60 * 24)));
                for ($i = 0; $i <= $totalDays; $i++) {
                    $date = date('Y-m-d',strtotime($date_to." - ".$i." DAYS"));
                    $post['date_from'] = $date;
                    $post['date_to'] = $date;
                    $user_logged = (isset($_SESSION['user_id']))?$_SESSION['user_id']:NULL;
                    if (!($post['team'])) {
                        $team_managers = $this->User_model->get_team_managers($user_logged);
                        if (!empty($team_managers)) {
                            $teams = '';
                            foreach($team_managers as $team_manager) {
                                $teams .= $team_manager['team_id'].",";
                            }
                            $teams = substr($teams,0,strlen($teams)-1);
                            $post['team'] = $teams;
                        }

                    }
                    $hours = $this->Hour_model->get_hours($post);
                    $date_ = date('d/m/Y',strtotime($date));
                    $result[$date_] = $hours;
                }
            }
    		
    		echo json_encode(array(
    				"success" => true,
    				"data" => $result,
    				"msg" => "Nothing found"
    		));
    	}
    }
    
    /**
     * Save an Hour for an agent and campaing in a particular date
     */
    public function save_hour()
    {
    	$form = $this->input->post();
    	
    	if (isset($form['date'])) {
    		$date = explode('-', $form['date']);
    		$day = $date[0];
    		$month = $date[1];
    		$year = $date[2];
    		
    		$form['date'] = $year.'-'.$month.'-'.$day;
    	}

        //Check if the agent exceeded the 7 hours today
        $isExceeded = $this->isDurationExceeded($form['date'], $form['user_id'], $form['duration'],$form['campaign_id']);

        if (!$isExceeded['success']) {
            $form['duration'] = $form['duration']*60;
            $form['updated_date'] = date('Y-m-d H:i:s');
            $form['updated_id'] = (isset($_SESSION['user_id']))?$_SESSION['user_id']:NULL;

            if (empty($form['hours_id'])) {
                $response = $this->Hour_model->add_new_hour($form);
            } else {
                $response = $this->Hour_model->update_hour($form);
            }

            if ($response) {
                echo json_encode(array(
                    "success" => true,
                    "data" => $response,
                    "message" => "Hour saved"
                ));
            }
            else {
                echo json_encode(array(
                    "success" => false,
                    "message" => "ERROR: Hour NOT saved"
                ));
            }
        }
        else {
            echo json_encode(array(
                "success" => false,
                "message" => "ERROR: ".$isExceeded['message']
            ));
        }

    }

    /**
     * Remove an Hour
     */
    public function remove_hour() {
        $form = $this->input->post();

        $response = $this->Hour_model->delete_hour($form['hours_id']);
        if ($response) {
            echo json_encode(array(
                "success" => true,
                "data" => $response,
                "message" => "Hour removed"
            ));
        } else {
            echo json_encode(array(
                "success" => false,
                "message" => "ERROR: Hour NOT removed"
            ));
        }
    }

    /* default hours page functions */
    public function default_hours()
    {
        $teams = $this->Form_model->get_teams();
        $campaigns = $this->Form_model->get_campaigns();
        $agents = $this->Form_model->get_agents();
        $data     = array(
            'campaign_access' => $this->_campaigns,

            'pageId' => 'Admin',
            'title' => 'Admin | Hours',
            'page' => 'default_hours',
            'javascript' => array(
                'admin/hours.js?v' . $this->project_version,
                'lib/moment.js',
                'lib/jquery.numeric.min.js',
            ),
            'css' => array(
                'dashboard.css',
            ),
            'campaigns' => $campaigns,
            'agents' => $agents,
            'team_managers'=>$teams
        );
        $this->template->load('default', 'admin/default_hours.php', $data);
    }

    /**
     * Get the hours in a date range
     *
     * POST
     *
     */
    public function get_default_hours_data()
    {
        if ($this->input->is_ajax_request()) {
            $post = $this->input->post();

            $default_hours = $this->Hour_model->get_default_hours($post);

            echo json_encode(array(
                "success" => true,
                "data" => $default_hours,
                "msg" => "Nothing found"
            ));
        }
    }

    /**
     * Save a Default Hour for an agent and campaing in a particular date
     */
    public function save_default_hour()
    {
        $form = $this->input->post();

        //Check if the agent exceeded the maximum hours defined in the default time of an agent
        $isExceeded = $this->isDefaultDurationExceeded($form['user_id'], $form['duration'],$form['campaign_id']);

        if (!$isExceeded['success']) {
            $form['duration'] = $form['duration']*60;

            if (empty($form['default_hours_id'])) {
                $response = $this->Hour_model->add_new_default_hour($form);
            } else {
                $response = $this->Hour_model->update_default_hour($form);
            }

            if ($response) {
                echo json_encode(array(
                    "success" => true,
                    "data" => $response,
                    "message" => "Default Hour saved"
                ));
            }
            else {
                echo json_encode(array(
                    "success" => false,
                    "message" => "ERROR: Default Hour NOT saved"
                ));
            }
        }
        else {
            echo json_encode(array(
                "success" => false,
                "message" => "ERROR: ".$isExceeded['message']
            ));
        }
    }

    /**
     * Remove an Default Hour
     */
    public function remove_default_hour() {
        $form = $this->input->post();

        $response = $this->Hour_model->delete_default_hour($form['default_hours_id']);
        if ($response) {
            echo json_encode(array(
                "success" => true,
                "data" => $response,
                "message" => "Default hour removed"
            ));
        } else {
            echo json_encode(array(
                "success" => false,
                "message" => "ERROR: Default Hour NOT removed"
            ));
        }
    }

    //Check if the duration is exceeded
    private function isDurationExceeded($date, $user_id, $new_duration, $campaign_id){
        $response = array();

        $hour_options = array();
        $hour_options['date_from'] = $date;
        $hour_options['date_to'] = $date;
        $hour_options['agent'] = $user_id;
        $hour_options['campaign'] = NULL;
        $hour_options['team'] = NULL;

        $current_duration = 0;
        $hours = $this->Hour_model->get_hours($hour_options);


        $time_options = array();
        $time_options['date_from'] = $date;
        $time_options['date_to'] = $date;
        $time_options['agent'] = $user_id;
        $time_options['campaign'] = NULL;
        $time_options['team'] = NULL;

        $max_agent_time = NULL;
        $agent_time = $this->Time_model->get_time($time_options);
        if (!empty($agent_time)) {
            if ($agent_time[0]['start_time'] && $agent_time[0]['end_time']) {
                $max_agent_time = (strtotime($agent_time[0]['end_time']) - strtotime($agent_time[0]['start_time']))/3600;
            }
            else {
                if ($agent_time[0]['default_start_time'] && $agent_time[0]['default_end_time']) {
                    $max_agent_time = (strtotime($agent_time[0]['default_end_time']) - strtotime($agent_time[0]['default_start_time']))/3600;
                }
            }
        }

        if ($max_agent_time) {
            foreach ($hours as $hour) {
                if ($hour['campaign_id'] != $campaign_id) {
                    $duration = ($hour['duration'])?$hour['duration']:0;
                    $current_duration += $duration;
                }
            }

            if ($current_duration/3600 + $new_duration/60 <= $max_agent_time) {
                $response['success'] = false;
                $response['message'] = '';
            }
            else {
                $response['success'] = true;
                $response['message'] = 'This agent exceeded the total hours in a day. Check the default time set for this agent';
            }
        }
        else {
            $response['success'] = true;
            $response['message'] = 'No default time founded for this agent. Please, check if the default time is set for this agent';
        }


        return $response;
    }

    //Check if the default duration is exceeded
    private function isDefaultDurationExceeded($user_id, $new_duration, $campaign_id){
        $response = array();

        $hour_options = array();
        $hour_options['agent'] = $user_id;
        $hour_options['campaign'] = NULL;
        $hour_options['team'] = NULL;

        $current_duration = 0;
        $hours = $this->Hour_model->get_default_hours($hour_options);


        $time_options = array();
        $time_options['agent'] = $user_id;
        $time_options['campaign'] = NULL;
        $time_options['team'] = NULL;

        $max_agent_time = NULL;
        $agent_time = $this->Time_model->get_default_time($time_options);
        if (!empty($agent_time)) {
            if ($agent_time[0]['start_time'] && $agent_time[0]['end_time']) {
                $max_agent_time = (strtotime($agent_time[0]['end_time']) - strtotime($agent_time[0]['start_time']))/3600;
            }
        }

        if ($max_agent_time) {
            foreach ($hours as $hour) {
                if ($hour['campaign_id'] != $campaign_id) {
                    $duration = ($hour['duration'])?$hour['duration']:0;
                    $current_duration += $duration;
                }
            }

            if ($current_duration/3600 + $new_duration/60 <= $max_agent_time) {
                $response['success'] = false;
                $response['message'] = '';
            }
            else {
                $response['success'] = true;
                $response['message'] = 'This agent exceeded the total hours in a day. Check the default time set for this agent';
            }
        }
        else {
            $response['success'] = true;
            $response['message'] = 'No default time founded for this agent. Please, check if the default time is set for this agent';
        }


        return $response;
    }
}
