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
        $this->load->model('Form_model');
        $this->load->model('Filter_model');
        $this->load->model('Hour_model');
        $this->load->model('User_model');
    }

    /* hours page functions */
    public function hours()
    {
		$this->load->model('Cron_model');
		$teams = $this->Form_model->get_teams();
    	$campaigns = $this->Form_model->get_campaigns();
    	$agents = $this->Form_model->get_agents();
    	$exception_types = $this->Form_model->get_hours_exception_type();
    	$this->Cron_model->update_hours($agents);
    	$data     = array(
    			'campaign_access' => $this->_campaigns,
    			'pageId' => 'Admin',
    			'title' => 'Admin | Hours',
    			'page' => array(
    					'admin' => 'hours',
    					'inner' => 'hours',
    			),
    			'javascript' => array(
    					'admin/hours.js',
    					'lib/moment.js',
    					'lib/jquery.numeric.min.js',
    					'lib/daterangepicker.js',
    					'lib/bootstrap-datetimepicker.js'
    			),
    			'css' => array(
    					'dashboard.css',
                		'daterangepicker-bs3.css'
    			),
    			'campaigns' => $campaigns,
          		'agents' => $agents,
				'team_managers'=>$teams,
    			'exception_types'=>$exception_types
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
     * Add a new Hour Exception
     */
    public function add_hour_exception() {
    	$form = $this->input->post();
    	
   		$exception_id = $this->Hour_model->add_hour_exception($form);
        if ($exception_id) {
        	$hours_form = array();
        	$hours_form['hours_id'] = $form['hours_id'];
        	$hours_form['updated_date'] = date('Y-m-d H:i:s');
        	$hours_form['updated_id'] = (isset($_SESSION['user_id']))?$_SESSION['user_id']:NULL;
        	 
        	$response = $this->Hour_model->update_hour($hours_form);
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
     * Remove an Hour Exception
     */
    public function remove_hour_exception() {
    	$form = $this->input->post();
    	 
    	$response = $this->Hour_model->delete_hour_exception($form['exception_id']);
    	if ($response) {
    		$hours_form = array();
    		$hours_form['hours_id'] = $form['hours_id'];
    		$hours_form['updated_date'] = date('Y-m-d H:i:s');
    		$hours_form['updated_id'] = (isset($_SESSION['user_id']))?$_SESSION['user_id']:NULL;
    		
    		$response_hours = $this->Hour_model->update_hour($hours_form);
    		if ($response_hours) {
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
     * Get the Hour Exceptions for a particular Hour
     */
    public function get_hour_exception() {
    	$form = $this->input->post();
    
    	$response = $this->Hour_model->get_hour_exception($form['hours_id']);
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

        if (!$isExceeded) {
            if (isset($form['exception_id'])) {
                unset($form['exception_id']);
            }
            if (isset($form['exception-duration'])) {
                unset($form['exception-duration']);
            }

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
                "message" => "ERROR: This agent exceeded the total hours in a day"
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

    private function isDurationExceeded($date, $user_id, $new_duration, $campaign_id){
        $options = array();
        $options['date_from'] = $date;
        $options['date_to'] = $date;
        $options['agent'] = $user_id;
        $options['campaign'] = NULL;
        $options['team'] = NULL;

        $current_duration = 0;

        $hours = $this->Hour_model->get_hours($options);

        foreach ($hours as $hour) {
            if ($hour['campaign_id'] != $campaign_id) {
                $duration = ($hour['duration'])?$hour['duration']:0;
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
            'page' => array(
                'admin' => 'hours',
                'inner' => 'hours',
            ),
            'javascript' => array(
                'admin/hours.js',
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
        //$isExceeded = $this->isDurationExceeded($form['user_id'], $form['duration'],$form['campaign_id']);
        $isExceeded = false;

        if (!$isExceeded) {
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
                "message" => "ERROR: This agent exceeded the total hours in a day. Check the default time set for this agent"
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
}
