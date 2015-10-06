<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Time extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        user_auth_check(false);
		$this->_campaigns = campaign_access_dropdown();
$this->_pots = campaign_pots();
        $this->load->model('Form_model');
        $this->load->model('Filter_model');
        $this->load->model('Time_model');
        $this->load->model('User_model');
    }

    /* times page functions */
    public function agent_time()
    {
		$teams = $this->Form_model->get_teams();
    	$agents = $this->Form_model->get_agents();
        $exception_types = $this->Form_model->get_time_exception_type();
    	$data     = array(
    			'campaign_access' => $this->_campaigns,
'campaign_pots' => $this->_pots,
    			'pageId' => 'Admin',
    			'title' => 'Admin | Time',
    			'page' =>  'agent_time',
    			'javascript' => array(
    					'admin/time.js',
    					'lib/moment.js',
    					'lib/jquery.numeric.min.js',
    					'lib/daterangepicker.js',
    					'lib/bootstrap-datetimepicker.js'
    			),
    			'css' => array(
    					'dashboard.css',
                		'daterangepicker-bs3.css'
    			),
          		'agents' => $agents,
				'team_managers'=>$teams,
                'exception_types'=>$exception_types
    	);
    	$this->template->load('default', 'admin/time.php', $data);
    }
    
    /**
     * Get the time in a date range
     * 
     * POST
     * 
     */
    public function get_time_data()
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
                    $time = $this->Time_model->get_time($post);
                    $date_ = date('d/m/Y',strtotime($date));
                    $result[$date_] = $time;
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
     * Add a new Time Exception
     */
    public function add_time_exception() {
    	$form = $this->input->post();

   		$exception_id = $this->Time_model->add_time_exception($form);
        if ($exception_id) {
        	$time_form = array();
            $time_form['time_id'] = $form['time_id'];
            $time_form['updated_date'] = date('Y-m-d H:i:s');
            $time_form['updated_id'] = (isset($_SESSION['user_id']))?$_SESSION['user_id']:NULL;

        	$response = $this->Time_model->update_time($time_form);
        	if ($response) {
        		echo json_encode(array(
        				"success" => true,
        				"exception_id" => $exception_id
        		));
        	} else {
        		echo json_encode(array(
        				"success" => false
        		));
        	}
        } else {
            echo json_encode(array(
                "success" => false
            ));
        }
    }

    /**
     * Remove an Time Exception
     */
    public function remove_time_exception() {
    	$form = $this->input->post();

    	$response = $this->Time_model->delete_time_exception($form['exception_id']);
    	if ($response) {
            $time_form = array();
            $time_form['time_id'] = $form['time_id'];
            $time_form['updated_date'] = date('Y-m-d H:i:s');
            $time_form['updated_id'] = (isset($_SESSION['user_id']))?$_SESSION['user_id']:NULL;

    		$response_time = $this->Time_model->update_time($time_form);
    		if ($response_time) {
	    		echo json_encode(array(
	    				"success" => true,
	    				"data" => $response
	    		));
    		} else {
	    		echo json_encode(array(
	    				"success" => false
	    		));
	    	}
    	} else {
    		echo json_encode(array(
    				"success" => false
    		));
    	}
    }

    /**
     * Get the Time Exceptions for a particular Time
     */
    public function get_time_exception() {
    	$form = $this->input->post();

    	$response = $this->Time_model->get_time_exception($form['time_id']);
    	if ($response) {
    		echo json_encode(array(
    				"success" => true,
    				"data" => $response
    		));
    	} else {
    		echo json_encode(array(
    				"success" => false,
    				"message" => "No results"
    		));
    	}
    }

    /**
     * Save an Time for an agent and campaing in a particular date
     */
    public function save_time()
    {
    	$form = $this->input->post();

    	if (isset($form['date'])) {
    		$date = explode('-', $form['date']);
    		$day = $date[0];
    		$month = $date[1];
    		$year = $date[2];

    		$form['date'] = $year.'-'.$month.'-'.$day;
    	}

        //Check if the agent exceeded the 7 time today
        //$isExceeded = $this->isDurationExceeded($form['date'], $form['user_id'], $form['duration'],$form['campaign_id']);
        $isExceeded = false;

        if (!$isExceeded) {
            if (isset($form['exception_id'])) {
                unset($form['exception_id']);
            }
            if (isset($form['exception-duration'])) {
                unset($form['exception-duration']);
            }

            $form['updated_date'] = date('Y-m-d H:i:s');
            $form['updated_id'] = (isset($_SESSION['user_id']))?$_SESSION['user_id']:NULL;

            if (empty($form['time_id'])) {
                $response = $this->Time_model->add_new_time($form);
            } else {
                $response = $this->Time_model->update_time($form);
            }

            if ($response) {
                echo json_encode(array(
                    "success" => true,
                    "data" => $response,
                    "message" => "Time saved"
                ));
            }
            else {
                echo json_encode(array(
                    "success" => false,
                    "message" => "ERROR: Time NOT saved"
                ));
            }
        }
        else {
            echo json_encode(array(
                "success" => false,
                "message" => "ERROR: This agent exceeded the total time in a day"
            ));
        }

    }

    /**
     * Remove an Time
     */
    public function remove_time() {
        $form = $this->input->post();

        $response = $this->Time_model->delete_time($form['time_id']);
        if ($response) {
            echo json_encode(array(
                "success" => true,
                "data" => $response,
                "message" => "Time removed"
            ));
        } else {
            echo json_encode(array(
                "success" => false,
                "message" => "ERROR: Time NOT removed"
            ));
        }
    }

    private function isDurationExceeded($date, $user_id, $new_duration, $campaign_id){
        $options = array();
        $options['date_from'] = $date;
        $options['date_to'] = $date;
        $options['agent'] = $user_id;
        $options['campaign'] = NULL;
        $options['team'] = NULL;

        $current_duration = 0;

        $time = $this->Time_model->get_time($options);

        foreach ($time as $time) {
            if ($time['campaign_id'] != $campaign_id) {
                $duration = ($time['duration'])?$time['duration']:0;
                $current_duration += $duration;
            }
        }

        if ($current_duration/3600 + $new_duration/60 > 7) {
            return true;
        }
        else {
            return false;
        }
    }

    /* default time page functions */
    public function default_time()
    {
        $teams = $this->Form_model->get_teams();
        $campaigns = $this->Form_model->get_campaigns();
        $agents = $this->Form_model->get_agents();
        $data     = array(
            'campaign_access' => $this->_campaigns,
'campaign_pots' => $this->_pots,
            'pageId' => 'Admin',
            'title' => 'Admin | Time',
            'page' => 'default_time',
            'javascript' => array(
                'admin/time.js',
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
        $this->template->load('default', 'admin/default_time.php', $data);
    }

    /**
     * Get the time in a date range
     *
     * POST
     *
     */
    public function get_default_time_data()
    {
        if ($this->input->is_ajax_request()) {
            $post = $this->input->post();

            $default_time = $this->Time_model->get_default_time($post);

            echo json_encode(array(
                "success" => true,
                "data" => $default_time,
                "msg" => "Nothing found"
            ));
        }
    }

    /**
     * Save a Default Time for an agent and campaing in a particular date
     */
    public function save_default_time()
    {
        $form = $this->input->post();

        //Check if the agent exceeded the maximum hours defined in the default time of an agent
        //$isExceeded = $this->isDurationExceeded($form['user_id'], $form['duration'],$form['campaign_id']);
        $isExceeded = false;

        if (!$isExceeded) {
            if (empty($form['default_time_id'])) {
                $response = $this->Time_model->add_new_default_time($form);
            } else {
                $response = $this->Time_model->update_default_time($form);
            }

            if ($response) {
                echo json_encode(array(
                    "success" => true,
                    "data" => $response,
                    "message" => "Default Time saved"
                ));
            }
            else {
                echo json_encode(array(
                    "success" => false,
                    "message" => "ERROR: Default Time NOT saved"
                ));
            }
        }
        else {
            echo json_encode(array(
                "success" => false,
                "message" => "ERROR: This agent exceeded the total hours in a day. Check the default time set for this agent"
            ));
        }
    }

    /**
     * Remove an Default Time
     */
    public function remove_default_time() {
        $form = $this->input->post();

        $response = $this->Time_model->delete_default_time($form['default_time_id']);
        if ($response) {
            echo json_encode(array(
                "success" => true,
                "data" => $response,
                "message" => "Default time removed"
            ));
        } else {
            echo json_encode(array(
                "success" => false,
                "message" => "ERROR: Default Time NOT removed"
            ));
        }
    }
}
